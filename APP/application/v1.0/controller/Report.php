<?php

namespace app\v3\controller;

use app\v3\common\BaseController;
use app\v3\common\NormalException;
use app\v3\common\ErrorReports;
use app\v3\service\ServiceUser;

/**
 * Reports控制器，用于显示错误报告
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class Report extends BaseController
{
    protected $beforeActionList = [
        'mustSuperAdmin',
    ];

    public function mustSuperAdmin()
    {
        // 统一检查权限
        ServiceUser::verifyUser(ServiceUser::MODE_SUPER_ADMIN);
    }

    public function list()
    {
        echo ErrorReports::getReportList();
    }

    public function getReport($id)
    {
        echo ErrorReports::getReportById($id);
    }
}