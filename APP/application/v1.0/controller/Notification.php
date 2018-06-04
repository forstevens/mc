<?php

namespace app\v3\controller;

use app\v3\common\BaseController;
use app\v3\common\NormalException;
use app\v3\logic\LogicNotification;
use app\v3\service\ServiceUser;

/**
 * Notification控制器(通知模块)
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 */
class Notification extends BaseController
{
    protected $beforeActionList = [
        'mustSuperAdmin' => ['except' => ['getNotificationInfo']],
    ];

    public function mustSuperAdmin()
    {
        // 统一检查权限
        ServiceUser::verifyUser(ServiceUser::MODE_SUPER_ADMIN);
    }

    public function addNotification()
    {
        return $this->toSuccessJson(LogicNotification::addNotification(request()->param()));
    }

    public function removeNotification($notificationid)
    {
        return $this->toSuccessJson(LogicNotification::removeNotification($notificationid));
    }

    public function updateNotification()
    {
        return $this->toSuccessJson(LogicNotification::updateNotification(request()->param()));
    }

    public function getNotificationInfo()
    {
        return $this->toSuccessJson(LogicNotification::getNotificationInfo(request()->param()));
    }
}