<?php

namespace app\v3\controller;

use app\v3\common\BaseController;
use app\v3\common\NormalException;
use app\v3\logic\LogicResource;
use app\v3\service\ServiceUser;

/**
 * Resource控制器(资源模块)
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/2/7 整理结构
 */
class Resource extends BaseController
{
    protected $beforeActionList = [
        'mustSuperAdmin',
    ];

    public function mustSuperAdmin()
    {
        // 统一检查权限
        ServiceUser::verifyUser(ServiceUser::MODE_SUPER_ADMIN);
    }

    public function addResource()
    {
        return $this->toSuccessJson(LogicResource::addResource(request()->param()));
    }

    public function removeResource($resourceid)
    {
        return $this->toSuccessJson(LogicResource::removeResource($resourceid));
    }

    public function updateResource()
    {
        return $this->toSuccessJson(LogicResource::updateResource(request()->param()));
    }

    public function getResourceInfo()
    {
        return $this->toSuccessJson(LogicResource::getResourceInfo(request()->param()));
    }
}