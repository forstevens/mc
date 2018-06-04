<?php

namespace app\v3\model;

use think\Model;
use app\v3\common\NormalException;

/**
 * ModelPlace Place模型
 * 由于此表不会有经常性的改动，特地导出为json放于public目录下，以减少数据库的IO量
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class ModelPlace extends Model
{
    // 数据表名
    protected $table = 'place';
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
        'id'         => 'integer',
        'place_name' => 'string',
        'location'   => 'string',
        'committee'  => 'integer',
    ];

    /**
     * 获取所有场所
     * @return array 所有的场所
     */
    public static function getPublicPlaces()
    {
        // 查询
        $res = (new self())->buildQuery()->select();
        foreach ($res as $key => $apply) {
            $res[$key] = $apply->toArray();
        }
        return $res;
    }

    /**
     * 增加场地
     * @param $data array 数据
     */
    public static function addPlace($data)
    {
        $place = new self();
        $place->place_name = $data['place_name'];
        $place->location = $data['location'];
        $place->committee = $data['committee'];
        $place->save();

        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Add Place', $data);
    }

    /**
     * 删除场地
     * @param $placeid int 场地编号
     * @param $removeChildren int 是否移除子资源
     */
    public static function removePlace($placeid, $removeChildren)
    {
        $place = self::get($placeid);
        if (!$place) {
            throw new NormalException('场地不存在');
        }
        $children = $place->getResources();
        if (count($children) > 0) {
            if ($removeChildren) {
                foreach ($children as $child) {
                    $child->delete();
                }
            } else {
                throw new NormalException("该场所下仍有资源");
            }
        }
        $place->delete();

        $data = [
            'placeid'         => $placeid,
            'remove_children' => $removeChildren,
        ];

        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Remove Place', $data);
    }

    /**
     * 修改场地信息
     * @param $data array 数据
     */
    public static function updatePlace($data)
    {
        $id = $data['id'];
        $place = self::get($id);
        if (!$place) {
            throw new NormalException('场地不存在');
        }
        if (isset($data['place_name'])) {
            $place->place_name = $data['place_name'];
        }
        if (isset($data['location'])) {
            $place->location = $data['location'];
        }
        if (isset($data['committee'])) {
            $place->committee = $data['committee'];
        }
        $place->save();

        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Update Place', $data);
    }

    /**
     * 获取场地信息
     * @param $placeid int 场地编号
     */
    public static function getPlaceInfo($placeid)
    {
        if (!$placeid) {
            $place = self::all();
        } else {
            $query = new self();
            $place = $query->where('id', $placeid)->select();
            if (!$place) {
                throw new NormalException('场地不存在');
            }
        }
        foreach ($place as $index => $r) {
            $place[$index] = $r->toArray();
        }
        return $place;
    }

    /**
     * 判断是否有该id的场所
     */
    public static function hasPlace($placeId)
    {
        $p = self::get($placeId);
        if ($p) {
            return true;
        } else {
            return false;
        }
    }

    public function getResources()
    {
        $r = new ModelResource();
        return $r->where('place_id', $this->id)->select();
    }
}