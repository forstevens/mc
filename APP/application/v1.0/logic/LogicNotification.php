<?php

namespace app\v3\logic;

use app\v3\common\NormalException;
use app\v3\common\Verifier;
use app\v3\model\ModelNotification;
use app\v3\service\ServiceUser;

/**
 * LogicNotification 检查层，用于对输入参数进行基本检查
 * 本层只放不需要访问数据库的检查
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 */
class LogicNotification
{
    /**
     * 增加通知
     * @param $params array 数据
     */
    public static function addNotification($params)
    {
        Verifier::verifyString($params, 'title', '通知标题', 50, true);

        Verifier::verifyString($params, 'content', '通知内容', 300, true);

        Verifier::verifyAndConvertNum($params, 'tend', '通知类型', true, function($t) {
            if ($t < 0) {
                throw new NormalException('通知类型不正确');
            }
        });

        Verifier::verifyAndConvertSingleTime($params, 'overtime', '通知过期时间', true, function($o) {
            if ($o < strtotime(date("Y-m-d H:i:s"))) {
                throw new NormalException('通知过期时间不正确');
            }
        });

        return ModelNotification::addNotification($params);
    }

    /**
     * 删除通知
     * @param $params array 数据
     */
    public static function removeNotification($id)
    {
        $params = ['id' => $id];
        Verifier::verifyAndConvertNum($params, 'id', '通知编号', true, function($i) {
            if ($i < 0) {
                throw new NormalException('通知编号不正确');
            }
        });

        return ModelNotification::removeNotification($params['id']);
    }

    /**
     * 修改通知信息
     * @param $params array 数据
     */
    public static function updateNotification($params)
    {
        Verifier::verifyAndConvertNum($params, 'id', '通知编号', true, function($i) {
            if ($i < 0) {
                throw new NormalException('通知编号不正确');
            }
        });

        Verifier::verifyString($params, 'title', '通知标题', 50);

        Verifier::verifyString($params, 'content', '通知内容', 300);

        Verifier::verifyAndConvertNum($params, 'tend', '通知类型', false, function($t) {
            if ($t < 0) {
                throw new NormalException('通知类型不正确');
            }
        });

        Verifier::verifyAndConvertSingleTime($params, 'overtime', '通知过期时间', false, function($o) {
            if ($o < strtotime(date("Y-m-d H:i:s"))) {
                throw new NormalException('通知过期时间不正确');
            }
        });

        return ModelNotification::updateNotification($params);
    }

    /**
     * 获取通知信息
     * @param $params array 数据
     */
    public static function getNotificationInfo($params)
    {
        Verifier::verifyAndConvertNum($params, 'id', '通知编号', true, function($i) {
            if ($i < 0) {
                throw new NormalException('通知编号不正确');
            }
        });

        Verifier::verifyAndConvertNum($params, 'mark', '标记', true);

        return ModelNotification::getNotificationInfo($params);
    }
}