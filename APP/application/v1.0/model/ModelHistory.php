<?php

namespace app\v3\model;

use think\Model;
use app\v3\common\NormalException;
use app\v3\service\ServiceUser;

/**
 * ModelHistory History模型
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class ModelHistory extends Model
{
    // 数据表名
    protected $table = 'history';
    // 主键
    protected $pk = 'id';
    // 保存时自动补完
    protected $auto = [];
    // 新增时自动补完
    protected $insert = [];
    // 更新时自动补完
    protected $update = [];
    // 类型声明
    protected $type = [
        'id'                 => 'integer',
        'apply_id'           => 'integer',
        'detail'             => 'string',
        'operator_userid'    => 'string',
        'operator_auth'      => 'integer',
        'operation_time'     => 'datetime',
    ];

    // 非apply操作时记录的applyId
    const NONE_APPLYID = 0;

    // 获取个人历史时的字段
    const GET_HISTORY = [
        'id',
        'apply_id',
        'detail',
        'operation_time',
    ];

    /**
     * 获取某用户所有的历史记录
     * @param array $data 数据
     */
    public static function getHistory($data)
    {
        // 构建查询
        $userid = ServiceUser::get()->getUserID();
        $query = (new self())->buildQuery();
        $query->where('operator_userid', $userid);
        $start = ($data['page'] - 1) * $data['limit']; // $start参数实测0为第一个
        if (isset($data['order']) && $data['order'] == 'asc') { // 默认将该用户最后的操作记录放最前面
            $query->order('operation_time asc');
        } else {
            $query->order('operation_time desc');
        }
        $options = $query->getOptions();
        $count = $query->count();
        $query->setOptions($options);
        $query->limit($start,$data['limit']);

        // 查询并过滤
        $query->field(self::GET_HISTORY);
        $res = $query->select();
        foreach ($res as $key => $history) {
            $res[$key] = $history->toArray();
        }

        return [
            'total' => $count,
            'data'  => $res,
        ];
    }

    /**
     * 查询历史记录
     * @param array $data 数据(请保证start_datetime和end_datetime同时存在)
     */
    public static function queryHistory($data)
    {
        // 构建查询
        $query = (new self())->buildQuery();
        if (isset($data['apply_id'])) {
            $query->where('apply_id', $data['apply_id']);
        }
        if (isset($data['operator_userid'])) {
            $query->where('operator_userid', $data['operator_userid']);
        }
        if (isset($data['operator_auth'])) {
            if (is_int($data['operator_auth'])) {
                $query->where('operator_auth', $data['operator_auth']);
            } else {
                $args = $data['operator_auth'];
                $query->where('operator_auth', $args['opt'], $args['arg']);
            }
        }
        if (isset($data['start_time']) && isset($data['end_time'])) {
            $query->where('operation_time', '>=', $data['start_time']);
            $query->where('operation_time', '<=', $data['end_time']);
        }
        $start = ($data['page'] - 1) * $data['limit']; // $start参数实测0为第一个
        if (isset($data['order']) && $data['order'] == 'desc') { // 查询历史默认把最早的放前面
            $query->order('operation_time desc');
        } else {
            $query->order('operation_time asc');
        }
        $options = $query->getOptions();
        $count = $query->count();
        $query->setOptions($options);
        $query->limit($start, $data['limit']);

        // 查询，全部输出
        $res = $query->select();
        foreach ($res as $key => $history) {
            $res[$key] = $history->toArray();
        }

        return [
            'total' => $count,
            'data'  => $res,
        ];
    }

    /**
     * 记录一次操作
     * @param integer $applyId 申请编号
     * @param string $operationName 操作名
     * @param array $data 接受的数据
     */
    public static function record($applyId, $operationName, $data = null)
    {
        $u = ServiceUser::get();

        // 处理数据
        if ($u) {
            $userid = $u->getUserID();
            $auth = $u->getAuth();
            if ($data) {
                $data = $operationName . ':' . json_encode($data, JSON_UNESCAPED_UNICODE);
            } else {
                $data = $operationName;
            }
        } else {
            // 这种情况主要是记录debug人员的操作
            $userid = '(undefined)';
            $auth = 127;
            if ($data) {
                $data = $operationName . '(System):' . json_encode($data, JSON_UNESCAPED_UNICODE);
            } else {
                $data = $operationName . '(System)';
            }
        }

        // 记录历史
        $h = new ModelHistory();
        $h->apply_id = $applyId;
        $h->detail = $data;
        $h->operator_userid = $userid;
        $h->operator_auth = $auth;
        $h->operation_time = time();
        $h->save();

        return $h->toArray();
    }
}