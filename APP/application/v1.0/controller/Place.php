<?php

namespace app\v3\controller;

use app\v3\common\BaseController;
use app\v3\common\NormalException;
use app\v3\logic\LogicPlace;
use app\v3\service\ServiceUser;

/**
 * Place控制器(场所模块)
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/2/7 整理结构
 */
class Place extends BaseController
{
    protected $beforeActionList = [
        'mustSuperAdmin',
    ];

    public function mustSuperAdmin()
    {
        // 统一检查权限
        ServiceUser::verifyUser(ServiceUser::MODE_SUPER_ADMIN);
    }

    public function addPlace()
    {
        return $this->toSuccessJson(LogicPlace::addPlace(request()->param()));
    }

    public function removePlace($placeid)
    {
        return $this->toSuccessJson(LogicPlace::removePlace($placeid));
    }

    public function updatePlace()
    {
        return $this->toSuccessJson(LogicPlace::updatePlace(request()->param()));
    }

    public function getPlaceInfo()
    {
        return $this->toSuccessJson(LogicPlace::getPlaceInfo(request()->param()));
    }
}