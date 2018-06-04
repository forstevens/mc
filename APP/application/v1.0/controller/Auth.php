<?php

namespace app\v3\controller;

use app\v3\common\BaseController;
use app\v3\common\NormalException;
use app\v3\logic\LogicUser;
use app\v3\service\ServiceUser;

/**
 * Auth控制器(权限模块)
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class Auth extends BaseController
{
    protected $beforeActionList = [
        'mustSuperAdmin',
    ];

    public function mustSuperAdmin()
    {
        // 统一检查权限
        ServiceUser::verifyUser(ServiceUser::MODE_SUPER_ADMIN);
    }

    public function addUser()
    {
        return $this->toSuccessJson(LogicUser::addUser(request()->param()));
    }

    public function removeUser($userid)
    {
        return $this->toSuccessJson(LogicUser::removeUser($userid));
    }

    public function changeUserAuth()
    {
        return $this->toSuccessJson(LogicUser::updateUser(request()->param()));
    }

    public function queryUser()
    {
        return $this->toSuccessJson(LogicUser::queryUser(request()->param()));
    }
}