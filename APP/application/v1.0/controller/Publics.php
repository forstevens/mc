<?php

namespace app\v3\controller;

use app\v3\common\NormalException;
use app\v3\common\BaseController;
use app\v3\logic\LogicApply;
use app\v3\logic\LogicResource;
use app\v3\logic\LogicPlace;
use app\v3\service\ServiceUser;

/**
 * Publics控制器(公共查询模块)
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class Publics extends BaseController
{
    public function getPublicBoard()
    {
        return $this->toSuccessJson(LogicApply::queryPublicBoard(request()->param()));
    }

    public function getPublicPlaces()
    {
        return $this->toSuccessJson(LogicPlace::getPublicPlaces());
    }

    public function getPublicResources()
    {
        return $this->toSuccessJson(LogicResource::getPublicResources());
    }
}