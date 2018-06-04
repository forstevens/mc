<?php

namespace app\v3\controller;

use app\v3\common\BaseController;
use app\v3\common\NormalException;
use app\v3\logic\LogicApply;
use app\v3\model\ModelApply;
use app\v3\service\ServiceUser;
use app\v3\service\ServiceMessage;

/**
 * Check控制器(审核模块)
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class Check extends BaseController
{
    protected $beforeActionList = [
        'mustAdmin' => ['except' => ['getPublicBoard']],
    ];

    public function mustAdmin()
    {
        // 统一检查登录
        ServiceUser::verifyUser(ServiceUser::MODE_ADMIN);
    }

    public function getPermittedResources()
    {
        return $this->toSuccessJson(ServiceUser::get()->toModel()->getResources());
    }

    public function getPermittedApplies()
    {
        return $this->toSuccessJson(LogicApply::queryPermittedApplies(request()->param()));
    }

    public function check()
    {
        $params = request()->param();
        $apply = LogicApply::checkApply($params);
        $status = $apply['status'];
        if ($status == ModelApply::STATUS_SUCCESS) {
            $content = '【挑战网】恭喜您，' . $apply['applicant_name'] .
            '！您的申请"' . $apply['activity'] . '"已经通过。';
            $phone = $apply['phone'];
        } else if ($status == ModelApply::STATUS_FAILED) {
            $content = '【挑战网】十分遗憾，' . $apply['applicant_name'] .
            '！您的申请"' . $apply['activity'] . '"未能通过。';
            $phone = $apply['phone'];
        } else {
            // never reached
            throw new NormalException('审核结果异常');
        }
        ServiceMessage::sendMessage($content, $phone);
        return $this->toSuccessJson();
    }
}