<?php

namespace app\v3\logic;

use app\v3\common\NormalException;
use app\v3\common\Verifier;
use app\v3\model\ModelHistory;
use app\v3\service\ServiceUser;

/**
 * LogicHistory 验证层，用于对输入参数进行基本验证
 * 本层只放不需要访问数据库的验证
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class LogicHistory
{
    // 非apply操作时记录的applyId
    const NONE_APPLYID = ModelHistory::NONE_APPLYID;

    /**
     * 获取某用户所有的历史记录
     * @param array $data 数据
     */
    public static function getHistory($data)
    {
        // 验证limit
        Verifier::verifyAndConvertNum($params, 'limit', '单页记录个数', true, function($l) {
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

        return ModelHistory::getHistory($params);
    }

    /**
     * 查询历史记录
     * @param array $params 数据
     */
    public static function queryHistory($params)
    {
        // 验证申请编号
        Verifier::verifyAndConvertNum($params, 'apply_id', '申请编号', false, function($a) {
            if ($a < 0) {
                throw new NormalException('申请编号不正确');
            }
        });

        // 验证userid
        Verifier::verifyString($params, 'operator_userid', '操作者NetID', 50);

        try {
            Verifier::verifyAndConvertNum($params, 'operator_auth', '操作者权限');
        } catch (NormalException $e) {
            try {
                Verifier::verifyAndConvertJson($params, 'operator_auth', '操作者权限', null, false, null, true);
            } catch (NormalException $e) {
                throw new NormalException('无法识别的操作者权限查询');
            }
        }

        // 验证时间段
        Verifier::verifyAndConvertTime($params);

        // 验证limit
        Verifier::verifyAndConvertNum($params, 'limit', '单页记录个数', true, function($l) {
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

        return ModelHistory::queryHistory($params);
    }

    /**
     * 记录一次操作
     * @param integer $applyId 申请编号
     * @param string $operationName 操作名
     * @param array $data 接受的数据
     */
    public static function record($applyId, $operationName, $data)
    {
        return ModelHistory::record($applyId, $operationName, $data);
    }
}