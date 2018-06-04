<?php

namespace app\v3\logic;

use app\v3\common\NormalException;
use app\v3\common\Verifier;
use app\v3\model\ModelPlace;
use app\v3\service\ServiceUser;

/**
 * LogicPlace 检查层，用于对输入参数进行基本检查
 * 本层只放不需要访问数据库的检查
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class LogicPlace
{
    /**
     * 获取所有资源
     * @return array 所有的资源
     */
    public static function getPublicPlaces()
    {
        // 返回查询结果
        return ModelPlace::getPublicPlaces();
    }

    /**
     * 增加场地
     * @param $params array 数据
     */
    public static function addPlace($params)
    {
        Verifier::verifyString($params, 'place_name', '场地名称', 300, true);
        Verifier::verifyJson($params, 'location', '场地位置坐标（json）', 300, true);
        Verifier::verifyString($params, 'committee', '场地类型', 300, true);

        return ModelPlace::addPlace($params);
    }

    /**
     * 删除场地
     * @param $id int 场地编号
     * @param $removeChildren int 是否移除子资源
     */
    public static function removePlace($id)
    {
        $params = ['id' => $id];
        Verifier::verifyAndConvertNum($params, 'id', '场地编号', true, function($i) {
            if ($i < 0) {
                throw new NormalException('场地编号不正确');
            }
        });

        return ModelPlace::removePlace($params['id'], false);
    }

    /**
     * 修改场地信息
     * @param $params array 数据
     */
    public static function updatePlace($params)
    {
        Verifier::verifyAndConvertNum($params, 'id', '场地编号', true, function($i) {
            if ($i < 0) {
                throw new NormalException('场地编号不正确');
            }
        });

        Verifier::verifyString($params, 'place_name', '场地名称', 300);
        Verifier::verifyJson($params, 'location', '场地位置坐标（json）', 300);
        Verifier::verifyString($params, 'committee', '场地类型', 300);

        return ModelPlace::updatePlace($params);
    }

    /**
     * 获取场地信息
     * @param $params array 数据
     */
    public static function getPlaceInfo($params)
    {
        Verifier::verifyAndConvertNum($params, 'id', '场地编号', true, function($i) {
            if ($i < 0) {
                throw new NormalException('场地编号不正确');
            }
        });

        return ModelPlace::getPlaceInfo($params['id']);
    }
}