<?php

namespace app\v3\controller;

use app\v3\common\BaseController;
use app\v3\common\NormalException;
use app\v3\logic\LogicHistory;
use app\v3\service\ServiceUser;

/**
 * History控制器(历史模块)
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class History extends BaseController
{
    public function getHistory()
    {
        ServiceUser::verifyUser(ServiceUser::MODE_ADMIN);
        return $this->toSuccessJson(LogicHistory::getHistory(request()->param()));
    }

    public function queryHistory()
    {
        ServiceUser::verifyUser(ServiceUser::MODE_SUPER_ADMIN);
        return $this->toSuccessJson(LogicHistory::queryHistory(request()->param()));
    }
}