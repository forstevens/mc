<?php

namespace app\v3\controller;

use app\v3\common\BaseController;
use app\v3\common\NormalException;
use app\v3\logic\LogicAdminResource;
use app\v3\service\ServiceUser;

/**
 * Relation控制器(关联表模块)
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 */
class Relation extends BaseController
{
    protected $beforeActionList = [
        'mustSuperAdmin',
    ];

    public function mustSuperAdmin()
    {
        // 统一检查权限
        ServiceUser::verifyUser(ServiceUser::MODE_SUPER_ADMIN);
    }

    public function addRelation()
    {
        return $this->toSuccessJson(LogicAdminResource::addAdminResource(request()->param()));
    }

    public function removeRelation($adminid, $resourceid)
    {
        return $this->toSuccessJson(LogicAdminResource::removeAdminResource(['admin_id'=>$adminid, 'resource_id'=>$resourceid]));
    }

    public function getRelation()
    {
        return $this->toSuccessJson(LogicAdminResource::getAdminResource(request()->param()));
    }
}