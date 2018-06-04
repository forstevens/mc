<?php

namespace app\v3\model;

use think\Model;
use app\v3\common\NormalException;

/**
 * ModelUser User模型
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class ModelUser extends Model
{
    const AUTH_DEFAULT = 1; // 默认权限 申请者
    const AUTH_ADMIN = 2; // 审查者(管理员)
    const AUTH_SUPER_ADMIN = 3; // 数据库管理员(超级管理员)
    // 数据表名
    protected $table = 'user';
    // 主键
    protected $pk = 'userid';
    // 保存时自动补完
    protected $auto = [];
    // 新增时自动补完
    protected $insert = [];
    // 更新时自动补完
    protected $update = [];
    // 类型声明
    protected $type = [
        'userid'    => 'string',
        'user_name' => 'string',
        'phone'     => 'string',
        'auth'      => 'integer',
    ];

    const QUERY_USER_FIELDS = [
        'userid',
        'user_name',
        'phone',
        'auth',
    ];

    /**
     * 获取该用户管理的所有资源组成的id数组
     * 非管理员返回空数组，管理员啥都没有管也返回空数组
     * @return array 资源id数组
     */
    public function getResources()
    {
        $result = ModelAdminResource::where(['admin_id' => $this->userid])->select();
        foreach ($result as $key => $value) {
            // 只输出该资源的id
            $result[$key] = $value->resource_id;
        }
        return $result;
    }

    /**
     * 判断该用户是否管理着某个资源（默认该用户为管理员）
     * @param int $resource_id 资源的id
     * @return bool 是否管理这个资源
     */
    public function isManagingResources($resource_id)
    {
        $result = ModelAdminResource::where(['admin_id' => $this->userid])->select();
        foreach ($result as $link) {
            if ($resource_id == $link->resource_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * 根据参数查询用户
     * @param array $data 参数
     */
    public static function queryUser($data)
    {
        // 构建查询
        $query = (new self())->buildQuery();
        if (isset($data['userid'])) {
            $query->where('userid', $data['userid']);
        }
        if (isset($data['user_name'])) {
            $query->where('user_name', $data['user_name']);
        }
        if (isset($data['phone'])) {
            $query->where('phone', $data['phone']);
        }
        if (isset($data['auth'])) {
            $query->where('auth', $data['auth']);
        }
        $start = ($data['page'] - 1) * $data['limit']; // 0为第一个
        $options = $query->getOptions();
        $count = $query->count();
        $query->setOptions($options);
        $query->limit($start,$data['limit']);

        // 查询并过滤
        $query->field(self::QUERY_USER_FIELDS);
        $res = $query->select();
        foreach ($res as $key => $user) {
            $res[$key] = $user->toArray();
        }

        return [
            'total' => $count,
            'data'  => $res,
        ];
    }

    /**
     * 添加新用户
     * @param array $data 数据
     */
    public static function addUser($data)
    {
        $mu = self::get($data['userid']);
        if ($mu) {
            throw new NormalException('用户已存在');
        }

        $mu = new self();
        $mu->userid = $data['userid'];
        $mu->user_name = $data['user_name'];
        $mu->phone = $data['phone'];
        $mu->auth = $data['auth'];
        $mu->save();
        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Add User', $data);

        return $mu->toArray();
    }

    /**
     * 移除新用户
     * @param string $userid 用户NetID
     */
    public static function removeUser($userid)
    {
        $mu = self::get($userid);
        if (!$mu) {
            throw new NormalException('用户不存在');
        }
        $mu->delete();

        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Remove User', ['userid' => $userid]);
    }

    /**
     * 修改用户权限
     * @param array $data 数据
     */
    public static function updateUser($data)
    {
        $userid = $data['userid'];

        $mu = self::get($userid);
        if (!$mu) {
            throw new NormalException('用户不存在');
        }
        if (isset($data['user_name']))
            $mu->user_name = $data['user_name'];
        if (isset($data['phone']))
            $mu->phone = $data['phone'];
        if (isset($data['auth']))
            $mu->auth = $data['auth'];
        $mu->save();

        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Update User', $data);

        return $mu->toArray();
    }

    /**
     * 获取某个用户的权限，默认均为申请者
     * @param string $userid 该用户的NetID
     */
    public static function getUserAuth($userid)
    {
        $u = self::get($userid);
        if ($u) {
            return $u->auth;
        } else {
            return self::AUTH_DEFAULT;
        }
    }

    /**
     * 检查是否有该NetID的审查者(管理员)
     * @param string $userid 该用户的NetID
     */
    public static function hasAdmin($userid)
    {
        $u = self::get($userid);
        if ($u) {
            return $u->auth == self::AUTH_ADMIN;
        }
        return false;
    }

    /**
     * 检查是否有该NetID的数据库管理员(超管)
     * @param string $userid 该用户的NetID
     */
    public static function hasSAdmin($userid)
    {
        $u = self::get($userid);
        if ($u) {
            return $u->auth == self::AUTH_SUPER_ADMIN;
        }
        return false;
    }
}
