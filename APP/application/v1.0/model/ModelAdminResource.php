<?php

namespace app\v3\model;

use think\Model;
use app\v3\common\NormalException;

/**
 * ModelApply User->Resource中间表模型
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 */
class ModelAdminResource extends Model
{
    // 数据表名
    protected $table = 'admin_resource';
    // 类型声明
    protected $type = [
        'admin_id'    => 'string',
        'resource_id' => 'integer',
    ];

    /**
     * 增加关联
     * @param $data array 数据
     */
    public static function addAdminResource($data)
    {
        if (!ModelResource::hasResource($data['resource_id'])) {
            throw new NormalException('资源不存在');
        }
        if (!ModelUser::hasAdmin($data['admin_id'])) {
            throw new NormalException('管理员不存在或其权限不正确');
        }

        $relation = new self();
        $relation->admin_id = $data['admin_id'];
        $relation->resource_id = $data['resource_id'];
        $relation->save();

        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Add Relation', $data);
    }

    /**
     * 删除关联
     * @param $data array 数据
     */
    public static function removeAdminResource($data)
    {
        $query = new self();
        $query->where('admin_id', $data['admin_id']);
        $query->where('resource_id', $data['resource_id']);
        $res = $query->delete();

        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Remove Relation', [
            'admin_id' => $data['admin_id'],
            'resource_id' => $data['resource_id']
        ]);
    }

    /**
     * 获取关联
     * @param $data array 数据
     */
    public static function getAdminResource($data)
    {
        $query = new self();
        if (isset($data['admin_id'])) {
            $query->where('admin_id', $data['admin_id']);
        }
        if (isset($data['resource_id'])) {
            $query->where('resource_id', $data['resource_id']);
        }

        $res = $query->select();
        foreach ($res as $index => $relation) {
            $res[$index] = $relation->toArray();
        }
        return $res;
    }
}