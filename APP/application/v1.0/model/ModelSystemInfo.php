<?php

namespace app\v3\model;

use think\Model;
use app\v3\common\NormalException;
use app\v3\service\ServiceUser;

/**
 * ModelSystemInfo SystemInfo模型
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class ModelSystemInfo extends Model
{
    // 数据表名
    protected $table = 'system_info';
    // 主键
    protected $pk = 'key';
    // 保存时自动补完
    protected $auto = [];
    // 新增时自动补完
    protected $insert = [];
    // 更新时自动补完
    protected $update = [];
    // 类型声明
    protected $type = [
        'key'   => 'string',
        'value' => 'string',
    ];

    /**
     * 设置某一系统信息
     * @param string $key 键
     * @param string $value 值
     */
    public static function setInfo($key, $value)
    {
        $u = ServiceUser::get();
        if (!$u) {
            throw new NormalException('您未登录');
        }
        if (!$u->isSuperAdmin()) {
            throw new NormalException('您无权执行此操作');
        }

        $i = self::get($key);
        if (!$i) {
            $i = new self();
            $i->key = $key;
        }
        $i->value = $value;
        $i->save();

        $data = $i->toArray();

        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Set SystemInfo', $data);

        return $data;
    }

    /**
     * 获取某一系统信息
     * @param string $key 键
     * @return string 值
     */
    public static function getInfo($key)
    {
        $i = self::get($key);
        if ($i) {
            return $i->value;
        } else {
            return null;
        }
    }
}
