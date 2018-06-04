<?php

namespace app\v3\logic;

use app\v3\common\NormalException;
use app\v3\common\Verifier;
use app\v3\model\ModelUser;
use app\v3\service\ServiceUser;

/**
 * LogicUser 验证层，用于对输入参数进行基本验证
 * 本层只放不需要访问数据库的验证
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class LogicUser
{
    /**
     * 查询用户
     * @param array $params 数据
     */
    public static function queryUser($params)
    {
        // 验证userid
        Verifier::verifyString($params, 'userid', 'NetID', 50);

        // 验证姓名
        Verifier::verifyString($params, 'user_name', '姓名', 50);

        // 验证手机号
        Verifier::verifyPhone($params, 'phone');

        // 验证权限
        Verifier::verifyAndConvertNum($params, 'auth', '权限', false, function($a) {
            if ($a < 1 || $a > 3) {
                throw new NormalException('权限有误');
            }
        });

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

        return ModelUser::queryUser($params);
    }

    /**
     * 添加新用户
     * @param array $params 数据
     */
    public static function addUser($params)
    {
        // 验证userid
        Verifier::verifyString($params, 'userid', 'NetID', 50, true);

        // 验证姓名
        Verifier::verifyString($params, 'user_name', '姓名', 50, true);

        // 验证手机号
        Verifier::verifyPhone($params, 'phone', true);

        // 验证权限
        Verifier::verifyAndConvertNum($params, 'auth', '权限', true, function($a) {
            if ($a < 1 || $a > 3) {
                throw new NormalException('权限有误');
            }
        });

        return ModelUser::addUser($params);
    }

    /**
     * 移除新用户
     * @param string $userid 用户NetID
     */
    public static function removeUser($userid)
    {
        // 验证userid
        Verifier::verifyString(['userid' => $userid], 'userid', 'NetID', 50, true);

        return ModelUser::removeUser($userid);
    }

    /**
     * 修改用户权限
     * @param array $params 数据
     */
    public static function updateUser($params)
    {
        // 验证userid
        Verifier::verifyString($params, 'userid', 'NetID', 50, true);

        // 验证姓名
        Verifier::verifyString($params, 'user_name', '姓名', 50);

        // 验证手机号
        Verifier::verifyPhone($params, 'phone');

        // 验证权限
        Verifier::verifyAndConvertNum($params, 'auth', '权限', false, function($a) {
            if ($a < 1 || $a > 3) {
                throw new NormalException('权限有误');
            }
        });

        return ModelUser::updateUser($params);
    }

    /**
     * 获取某个用户的权限，默认均为申请者
     * @param string $userid 该用户的NetID
     */
    public static function getUserAuth($params)
    {
        // 验证userid
        Verifier::verifyString($params, 'userid', 'NetID', 50, true);

        return ModelUser::getUserAuth($params['userid']);
    }
}