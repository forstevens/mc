<?php

namespace app\v3\model;

use think\Model;
use app\v3\common\NormalException;

/**
 * ModelResource Resource模型
 * 由于此表不会有经常性的改动，特地导出为json放于public目录下，以减少数据库的IO量
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class ModelResource extends Model
{
    // 数据表名
    protected $table = 'resource';
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
        'id'            => 'integer',
        'resource_name' => 'string',
        'place_id'      => 'integer',
        'start'         => 'integer',
        'end'           => 'integer',
        'device'        => 'integer',
        'capacity'      => 'integer',
        'admin_id'      => 'string',
        'other'         => 'string',
    ];

    /**
     * 获取所有资源
     * @return array 所有的资源
     */
    public static function getPublicResources()
    {
        // 查询
        $res = (new self())->buildQuery()->select();
        foreach ($res as $key => $apply) {
            $res[$key] = $apply->toArray();
        }
        return $res;
    }

    /**
     * 增加资源
     * @param $data array 数据
     */
    public static function addResource($data)
    {
        if (!ModelPlace::hasPlace($data['place_id'])) {
            throw new NormalException('场所不存在');
        }
        if ($data['start'] >= $data['end']) {
            throw new NormalException('起止时间不正确');
        }

        $resource = new self();
        $resource->allowField([
            'resource_name',
            'place_id',
            'start',
            'end',
            'device',
            'capacity',
            'admin_id',
            'other',
        ])->save($data);

        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Add Resource', $data);
    }

    /**
     * 删除资源
     * @param $resourceid int 资源编号
     */
    public static function removeResource($resourceid)
    {
        $resource = self::get($resourceid);
        if (!$resource) {
            throw new NormalException('资源不存在');
        }
        $resource->delete();

        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Remove Resource', ['id' => $resource->id]);
    }

    /**
     * 修改资源信息
     * @param $data array 数据
     */
    public static function updateResource($data)
    {
        $id = $data['id'];
        $resource = self::get($id);
        if (!$resource) {
            throw new NormalException('资源不存在');
        }
        if (isset($data['resource_name'])) {
            $resource->resource_name = $data['resource_name'];
        }
        if (isset($data['place_id'])) {
            if (!ModelPlace::hasPlace($data['place_id'])) {
                throw new NormalException('场所不存在');
            }
            $resource->place_id = $data['place_id'];
        }
        if (isset($data['start']) && isset($data['end'])) {
            if ($data['start'] >= $data['end']) {
                throw new NormalException('起止时间不正确');
            }
        }
        if (isset($data['start'])) {
            $resource->start = $data['start'];
        }
        if (isset($data['end'])) {
            $resource->end = $data['end'];
        }
        if (isset($data['device'])) {
            $resource->device = $data['device'];
        }
        if (isset($data['capacity'])) {
            $resource->capacity = $data['capacity'];
        }
        if (isset($data['other'])) {
            $resource->other = $data['other'];
        }
        $resource->save();

        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Update Resource', $data);
    }

    /**
     * 获取资源信息
     * @param $resourceid int 资源编号
     */
    public static function getResourceInfo($resourceid)
    {
        if (!$resourceid) {
            $resource = self::all();
        } else {
            $query = new self();
            $resource = $query->where('id', $resourceid)->select();
            if (!$resource) {
                throw new NormalException('资源不存在');
            }
        }
        foreach ($resource as $index => $r) {
            $resource[$index] = $r->toArray();
        }
        return $resource;
    }

    /**
     * 判断是否有该id的资源
     */
    public static function hasResource($resourceId)
    {
        $r = self::get($resourceId);
        if ($r) {
            return true;
        } else {
            return false;
        }
    }
}