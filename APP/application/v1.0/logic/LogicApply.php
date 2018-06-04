<?php

namespace app\v3\logic;

use app\v3\common\NormalException;
use app\v3\common\Verifier;
use app\v3\model\ModelApply;
use app\v3\model\ModelResource;
use app\v3\service\ServiceUser;

/**
 * LogicApply 检查层，用于对输入参数进行基本检查
 * 本层只放不需要访问数据库的检查
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class LogicApply
{
    // 申请状态
    const STATUS_CANCELLED = ModelApply::STATUS_CANCELLED;
    const STATUS_CHECKING = ModelApply::STATUS_CHECKING;
    const STATUS_SUCCESS = ModelApply::STATUS_SUCCESS;
    const STATUS_FAILED = ModelApply::STATUS_FAILED;
    // 拒绝原因
    const DELETE_REASON = ModelApply::DELETE_REASON;
    const DEFAULT_REASON = ModelApply::DEFAULT_REASON;
    
    /**
     * 公共申请查询
     * @param array $params 数据
     */
    public static function queryPublicBoard($params)
    {
        // 检查资源
        Verifier::verifyAndConvertNum($params, 'resource_id', '资源编号', true, function($r) {
            if (!ModelResource::hasResource($r)) {
                throw new NormalException('资源不存在');
            }
        });
        // 检查时间段
        Verifier::verifyAndConvertTime($params, true, function($s, $e){
            if ($e - $s > 864000) {
                throw new NormalException('查询时间段太长');
            }
        });

        return ModelApply::queryPublicBoard($params);
    }

    /**
     * 个人申请查询
     * @param ServiceUser $user 用户
     */
    public static function queryAppliesByUser($user)
    {
        return ModelApply::queryAppliesByUserID($user->getUserID());
    }

    /**
     * 管理员查询其管理的申请
     * @param array $params 数据
     */
    public static function queryPermittedApplies($params)
    {
        // 验证资源
        Verifier::verifyAndConvertNum($params, 'resource_id', '资源编号', true, function($rid) {
            if (!ModelResource::hasResource($rid)) {
                throw new NormalException('资源不存在');
            }
            $u = ServiceUser::get();
            if (!$u->toModel()->isManagingResources($rid)) {
                throw new NormalException('您无权管理该资源');
            }
        });

        // 验证审核状态
        Verifier::verifyAndConvertNum($params, 'status', '审核状态', false, function($s) {
            if ($s < 0 || $s > 3) {
                throw new NormalException('审核状态不正确');
            }
        });

        // 验证limit
        Verifier::verifyAndConvertNum($params, 'limit', '单页记录个数', false, function($l) {
            if ($l < 1) {
                throw new NormalException('单页记录个数太少');
            }
            if ($l > 50) {
                throw new NormalException('单页记录个数太多');
            }
        });

        // 验证页码
        Verifier::verifyAndConvertNum($params, 'page', '页码', true, function($p) {
            if ($p < 1) {
                throw new NormalException('页码不正确');
            }
        });

        return ModelApply::queryPermittedApplies($params);
    }

    /**
     * 创建新申请
     * @param array $params 数据
     */
    public static function createApply($params)
    {
        // 检查资源
        Verifier::verifyAndConvertNum($params, 'resource_id', '资源编号', true, function($r) {
            if (!ModelResource::hasResource($r)) {
                throw new NormalException('资源不存在');
            }
        });

        // 检查时间段
        Verifier::verifyAndConvertTime($params, true, function($s, $e) {
            $now = time();
            if ($s < $now) {
                throw new NormalException('不能申请过去的资源');
            }
            if ($e - $s > 54000) { // 15个小时
                throw new NormalException('时间段太长');
            }
            if (intval(date('H', $s)) < 8) {
                throw new NormalException('申请起始时间过早');
            }
            if (intval(date('H', $e)) > 23) {
                throw new NormalException('申请结束时间过晚');
            }
        });

        // 检查活动名称
        Verifier::verifyString($params, 'activity', '活动名称', 300, true);

        // 检查组织名称
        Verifier::verifyString($params, 'organization', '组织名称', 300, true);

        // 检查申请人数
        Verifier::verifyString($params, 'apply_scale', '申请人数', 300, true);

        // 检查手机号
        Verifier::verifyPhone($params, 'phone', true);

        return ModelApply::createApply($params);
    }

    /**
     * 撤销申请
     * @param int $id 申请编号
     */
    public static function deleteApply($id)
    {
        $params = ['id' => $id];

        // 检查申请编号
        Verifier::verifyAndConvertNum($params, 'id', '申请编号', true, function($i) {
            if ($i < 0) {
                throw new NormalException('申请编号不正确');
            }
        });

        return ModelApply::deleteApply($params['id']);
    }

    /**
     * 修改申请信息
     * @param array $params 数据
     */
    public static function updateApply($params)
    {
        // 检查资源
        Verifier::verifyAndConvertNum($params, 'resource_id', '资源编号', true, function($r) {
            if (!ModelResource::hasResource($r)) {
                throw new NormalException('资源不存在');
            }
        });

        // 检查时间段
        Verifier::verifyAndConvertTime($params, true, function($s, $e) {
            $now = time();
            if ($s < $now) {
                throw new NormalException('不能申请过去的资源');
            }
            if ($e - $s > 50400) {
                throw new NormalException('时间段太长');
            }
            if (intval(date('H', $s)) < 8) {
                throw new NormalException('申请起始时间过早');
            }
            if (intval(date('H', $e)) > 22) {
                throw new NormalException('申请结束时间过晚');
            }
        });

        // 检查活动名称
        Verifier::verifyString($params, 'activity', '活动名称', 300, true);

        // 检查申请人数
        Verifier::verifyString($params, 'apply_scale', '申请人数', 300, true);

        // 检查组织名称
        Verifier::verifyString($params, 'organization', '组织名称', 300, true);

        // 检查手机号
        Verifier::verifyPhone($params, 'phone', true);

        return ModelApply::updateApply($params);
    }

    /**
     * 审查某一申请
     * @param array $params 数据
     * @return array 申请的详细信息
     */
    public static function checkApply($params)
    {
        // 检查申请编号
        Verifier::verifyAndConvertNum($params, 'id', '申请编号', true, function($i) {
            if ($i < 0) {
                throw new NormalException('申请编号不正确');
            }
        });

        // 检查状态
        Verifier::verifyAndConvertNum($params, 'status', '状态', true, function($s) {
            if ($s < 2 || $s > 3) {
                throw new NormalException('状态不正确');
            }
        });

        // 检查原因
        Verifier::verifyString($params, 'reason', '原因', 300, true);

        return ModelApply::checkApply($params);
    }
}