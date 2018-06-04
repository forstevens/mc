<?php

namespace app\v3\logic;

use app\v3\common\NormalException;
use app\v3\common\Verifier;
use app\v3\model\ModelAdminResource;

/**
 * LogicAdminResource 检查层，用于对输入参数进行基本检查
 * 本层只放不需要访问数据库的检查
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 */
class LogicAdminResource
{
    /**
     * 增加关联
     * @param $params array 数据
     */
    public static function addAdminResource($params)
    {
        Verifier::verifyString($params, 'admin_id', '负责人的NetID', 50, true);
        Verifier::verifyAndConvertNum($params, 'resource_id', '资源编号', true, function($p) {
            if ($p < 0) {
                throw new NormalException('资源编号不正确');
            }
        });

        return ModelAdminResource::addAdminResource($params);
    }

    /**
     * 删除关联
     * @param $params array 数据
     */
    public static function removeAdminResource($params)
    {
        Verifier::verifyString($params, 'admin_id', '负责人的NetID', 50, true);
        Verifier::verifyAndConvertNum($params, 'resource_id', '资源编号', true, function($p) {
            if ($p < 0) {
                throw new NormalException('资源编号不正确');
            }
        });

        return ModelAdminResource::removeAdminResource($params);
    }

    /**
     * 获取关联
     * @param $params array 数据
     */
    public static function getAdminResource($params)
    {
        Verifier::verifyString($params, 'admin_id', '负责人的NetID', 50, false);
        Verifier::verifyAndConvertNum($params, 'resource_id', '资源编号', false, function($p) {
            if ($p < 0) {
                throw new NormalException('资源编号不正确');
            }
        });

        return ModelAdminResource::getAdminResource($params);
    }
}