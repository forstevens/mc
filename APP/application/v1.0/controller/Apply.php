<?php

namespace app\v3\controller;

use app\v3\common\NormalException;
use app\v3\common\BaseController;
use app\v3\logic\LogicApply;
use app\v3\service\ServiceUser;

/**
 * Apply控制器(申请模块)
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class Apply extends BaseController
{
    protected $beforeActionList = [
        'mustLogin'
    ];

    public function mustLogin()
    {
        // 统一检查登录
        ServiceUser::verifyUser(ServiceUser::MODE_LOGIN);
    }

    public function getUserApplies()
    {
        return $this->toSuccessJson(LogicApply::queryAppliesByUser(ServiceUser::get()));
    }

    public function createApply()
    {
        return $this->toSuccessJson(LogicApply::createApply(request()->param()));
    }

    public function deleteApply($applyid)
    {
        return $this->toSuccessJson(LogicApply::deleteApply($applyid));
    }

    public function updateApply()
    {
        return $this->toSuccessJson(LogicApply::updateApply(request()->param()));
    }
}