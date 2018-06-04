<?php

namespace app\v3\logic;

use app\v3\common\NormalException;
use app\v3\common\Verifier;
use app\v3\model\ModelResource;
use app\v3\service\ServiceUser;

/**
 * LogicResource 检查层，用于对输入参数进行基本检查
 * 本层只放不需要访问数据库的检查
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class LogicResource
{
    /**
     * 获取所有资源
     * @return array 所有的资源
     */
    public static function getPublicResources()
    {
        // 返回查询结果
        return ModelResource::getPublicResources();
    }

    /**
     * 增加资源
     * @param $params array 数据
     */
    public static function addResource($params)
    {
        Verifier::verifyString($params, 'resource_name', '资源名称', 300, true);

        Verifier::verifyAndConvertNum($params, 'place_id', '所属场地的编号', true, function($p) {
            if ($p < 0) {
                throw new NormalException('所属场地的编号不正确');
            }
        });

        Verifier::verifyAndConvertNum($params, 'start', '开始时间', true, function($p) {
            if ($p < 0) {
                throw new NormalException('开始时间不正确');
            }
        });

        Verifier::verifyAndConvertNum($params, 'end', '结束时间', true, function($p) {
            if ($p > 24) {
                throw new NormalException('结束时间');
            }
        });

        Verifier::verifyAndConvertNum($params, 'device', '资源类型', true, function($t) {
            if ($t < 0) {
                throw new NormalException('资源类型不正确');
            }
        });

        Verifier::verifyAndConvertNum($params, 'capacity', '容纳人数', true, function($c) {
            if ($c < 0) {
                throw new NormalException('容纳人数不正确');
            }
        });

        Verifier::verifyString($params, 'other', '其他信息', null, true);

        return ModelResource::addResource($params);
    }

    /**
     * 删除资源
     * @param $params array 数据
     */
    public static function removeResource($id)
    {
        $params = ['id' => $id];
        Verifier::verifyAndConvertNum($params, 'id', '资源编号', true, function($i) {
            if ($i < 0) {
                throw new NormalException('资源编号不正确');
            }
        });

        return ModelResource::removeResource($params['id']);
    }

    /**
     * 修改资源信息
     * @param $params array 数据
     */
    public static function updateResource($params)
    {
        Verifier::verifyAndConvertNum($params, 'id', '资源编号', true, function($i) {
            if ($i < 0) {
                throw new NormalException('资源编号不正确');
            }
        });

        Verifier::verifyString($params, 'resource_name', '资源名称', 300);

        Verifier::verifyAndConvertNum($params, 'place_id', '所属场地的编号', false, function($p) {
            if ($p < 0) {
                throw new NormalException('所属场地的编号不正确');
            }
        });

        Verifier::verifyAndConvertNum($params, 'start', '开始时间', false, function($p) {
            if ($p < 0) {
                throw new NormalException('开始时间不正确');
            }
        });

        Verifier::verifyAndConvertNum($params, 'end', '结束时间', false, function($p) {
            if ($p > 24) {
                throw new NormalException('结束时间');
            }
        });

        Verifier::verifyAndConvertNum($params, 'device', '资源类型', false, function($t) {
            if ($t < 0) {
                throw new NormalException('资源类型不正确');
            }
        });

        Verifier::verifyAndConvertNum($params, 'capacity', '容纳人数', false, function($c) {
            if ($c < 0) {
                throw new NormalException('容纳人数不正确');
            }
        });

        Verifier::verifyString($params, 'other', '其他信息');

        return ModelResource::updateResource($params);
    }

    /**
     * 获取资源信息
     * @param $params array 数据
     */
    public static function getResourceInfo($params)
    {
        Verifier::verifyAndConvertNum($params, 'id', '资源编号', true, function($i) {
            if ($i < 0) {
                throw new NormalException('资源编号不正确');
            }
        });

        return ModelResource::getResourceInfo($params['id']);
    }
}