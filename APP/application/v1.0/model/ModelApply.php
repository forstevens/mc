<?php

namespace app\v3\model;

use think\Model;
use app\v3\common\NormalException;
use app\v3\service\ServiceUser;

/**
 * ModelApply Apply模型
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class ModelApply extends Model
{
    // 数据表名
    protected $table = 'apply';
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
        'id'               => 'integer',
        'resource_id'      => 'integer',
        'applicant_userid' => 'string',
        'applicant_name'   => 'string',
        'activity'         => 'string',
        'organization'     => 'string',
        'apply_scale'       => 'string',
        'start_time'       => 'datetime',
        'end_time'         => 'datetime',
        'apply_time'       => 'datetime',
        'phone'            => 'string',
        'status'           => 'integer',
        'reason'           => 'string',
    ];
    // 申请状态
    const STATUS_CANCELLED = 0; // 已撤销
    const STATUS_CHECKING = 1; // 审核中
    const STATUS_SUCCESS = 2; // 通过
    const STATUS_FAILED = 3; // 失败
    // 拒绝原因
    const DELETE_REASON = '已撤销';
    const DEFAULT_REASON = '审核中';

    // 公共申请查询返回的字段
    const PUBLIC_BOARD_FIELDS = [
        'id',
        'applicant_name',
        'activity',
        'organization',
        'apply_scale',
        'start_time',
        'end_time',
        'status',
    ];

    // 个人申请查询返回的字段
    const QUERY_USER_APPLIES_FIELDS = [
        'id',
        'resource_id',
        'activity',
        'organization',
        'apply_scale',
        'start_time',
        'end_time',
        'phone',
        'status',
        'reason',
    ];

    // 管理员获取自己管理的申请时返回的字段
    const QUERY_PERMITTED_APPLIES_FIELDS = [
        'id',
        'resource_id',
        'applicant_name',
        'activity',
        'organization',
        'apply_scale',
        'start_time',
        'end_time',
        'apply_time',
        'phone',
        'status',
        'reason',
    ];

    // 资源申请返回的字段
    const CREATE_APPLY_FIELDS = [
        'id',
    ];

    // 修改申请返回的字段
    const UPDATE_APPLY_FIELDS = [
    ];

    /**
     * 公共申请查询
     * @param array $data 数据
     */
    public static function queryPublicBoard($data)
    {
        // 构建查询
        $query = new self();
        $query->where('resource_id', $data['resource_id']);
        $query->where('start_time', '>=', $data['start_time']);
        $query->where('end_time', '<=', $data['end_time']);
        $query->where('status', '>=', self::STATUS_CANCELLED); // 筛选未撤销的申请

        // 查询并过滤
        $query->field(self::PUBLIC_BOARD_FIELDS);
        $res = $query->select();
        foreach ($res as $key => $apply) {
            $res[$key] = $apply->toArray();
        }

        return $res;
    }

    /**
     * 个人申请查询
     * @param string $userid 用户的NetID
     */
    public static function queryAppliesByUserID($userid)
    {
        // 构建查询
        $query = new self();
        $query->where('applicant_userid', $userid);

        // 查询并过滤
        $query->field(self::QUERY_USER_APPLIES_FIELDS);
        $query->order('apply_time desc'); // 强制将最晚申请的放在最前面
        $res = $query->select();
        foreach ($res as $key => $apply) {
            $res[$key] = $apply->toArray();
        }

        return $res;
    }

    /**
     * 管理员查询其管理的申请
     * @param array $data 数据
     */
    public static function queryPermittedApplies($data)
    {
        // 构建查询
        $query = (new self())->buildQuery();
        $query->where('resource_id', $data['resource_id']);
        if (isset($data['status'])) {
            $query->where('status', $data['status']);
        }
        $start = ($data['page'] - 1) * $data['limit']; // $start参数实测0为第一个
        $query->order('apply_time desc'); // 强制将最晚申请的放在最前面
        $options = $query->getOptions();
        $count = $query->count();
        $query->setOptions($options);
        $query->limit($start,$data['limit']);

        // 查询并过滤
        $query->field(self::QUERY_PERMITTED_APPLIES_FIELDS);
        $res = $query->select();
        foreach ($res as $key => $apply) {
            $res[$key] = $apply->toArray();
        }

        return [
            'total' => $count,
            'data'  => $res,
        ];
    }

    /**
     * 创建新申请
     * @param array $data 数据(请解析start_datetime和end_datetime)
     */
    public static function createApply($data)
    {
        $u = ServiceUser::get();

        $res = ModelResource::get($data['resource_id']);
        $s = self::getHour($data['start_time']);
        $e = self::getHour($data['end_time']);
        if ($s < intval($res['start'])) {
            throw new NormalException('申请起始时间过早');
        }
        if ($e > intval($res['end'])) {
            throw new NormalException('申请结束时间过晚');
        }

        $a = new self();
        $a->resource_id = $data['resource_id'];
        $a->applicant_userid = $u->getUserID();
        $a->applicant_name = $u->getUserName();
        $a->activity = $data['activity'];
        $a->organization = $data['organization'];
        $a->apply_scale = $data['apply_scale'];
        $a->start_time = $data['start_time'];
        $a->end_time = $data['end_time'];
        $a->apply_time = time();
        $a->phone = $data['phone'];
        $a->status = self::STATUS_CHECKING;
        $a->reason = self::DEFAULT_REASON;
        $a->save();

        ModelHistory::record($a->id, 'Create Apply', $data);

        return $a->visible(self::CREATE_APPLY_FIELDS)->toArray();
    }

    /**
     * 撤销申请
     * @param int $id 申请编号
     */
    public static function deleteApply($id)
    {
        $u = ServiceUser::get();

        $a = self::get($id);
        if (!$a) {
            throw new NormalException('申请不存在');
        }
        if ($u->getUserID() != $a->applicant_userid) {
            throw new NormalException('您不是该申请的申请者');
        }
        if ($a->status == self::STATUS_FAILED) {
            throw new NormalException('该申请已经被拒绝');
        }
        if ($a->status == self::STATUS_CANCELLED) {
            throw new NormalException('该申请已经被撤销');
        }
        $a->status = self::STATUS_CANCELLED;
        $a->reason = self::DELETE_REASON;
        $a->save();

        ModelHistory::record($id, 'Delete Apply');
    }

    /**
     * 修改申请信息
     * @param array $data 数据(请解析start_datetime和end_datetime)
     */
    public static function updateApply($data)
    {
        $u = ServiceUser::get();

        $id = $data['id'];
        $a = self::get($id);
        if (!$a) {
            throw new NormalException('申请不存在');
        }
        if ($u->getUserID() != $a->applicant_userid) {
            throw new NormalException('您不是该申请的申请者');
        }
        if ($a->status == self::STATUS_CANCELLED) {
            throw new NormalException('该申请已经被撤销');
        }

        $res = ModelResource::get($a->resource_id);
        $s = self::getHour($data['start_time']);
        $e = self::getHour($data['end_time']);
        if ($s < intval($res['start'])) {
            throw new NormalException('申请起始时间过早');
        }
        if ($e > intval($res['end'])) {
            throw new NormalException('申请结束时间过晚');
        }
        
        $a->activity = $data['activity'];
        $a->organization = $data['organization'];
        $a->apply_scale = $data['apply_scale'];
        $a->start_time = $data['start_time'];
        $a->end_time = $data['end_time'];
        $a->apply_time = time(); // 因为修改申请信息，状态回到未审核，因此修改申请时间看起来也合理
        $a->phone = $data['phone'];
        $a->status = self::STATUS_CHECKING;
        $a->reason = self::DEFAULT_REASON;
        $a->save();

        ModelHistory::record($a->id, 'Update Apply', $data);

        // 目前接口暂时不返回任何数据
        // return $a->visible(self::UPDATE_APPLY_FIELDS)->toArray();
    }

    /**
     * 审查某一申请
     * @param array $data 数据
     * @return array 申请的详细信息
     */
    public static function checkApply($data)
    {
        $id = $data['id'];
        $a = self::get($id);
        if (!$a) {
            throw new NormalException('申请不存在');
        }
        if ($a->status == $data['status']) {
            if ($a->status == self::STATUS_SUCCESS) {
                throw new NormalException('该申请已经处于通过状态');
            } else if ($a->status == self::STATUS_FAILED) {
                throw new NormalException('该申请已经处于拒绝状态');
            } else {
                throw new NormalException('该审核请求的状态由于重复而无效');
            }
        }
        if ($a->status == self::STATUS_CANCELLED) {
            throw new NormalException('该申请已经被撤销');
        }
        $a->status = $data['status'];
        $a->reason = $data['reason'];
        $a->save();

        ModelHistory::record($id, 'Check Apply', $data);

        return $a->toArray();
    }

    /**
     * 获取时间字符串中的小时
     */
    private static function getHour($timeStr)
    {
        $datetime = new \DateTime($timeStr);
        $hourStr = $datetime->format('H');
        return intval($hourStr);
    }
}