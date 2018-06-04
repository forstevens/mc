<?php

namespace app\v3\controller;

require_once(APP_PATH . 'v3/common/tzAPIClient.php');

use tiaozhan\lib\api;

use app\v3\common\NormalException;
use app\v3\common\BaseController;
use app\v3\service\ServiceUser;

/**
 * User控制器(登录模块)
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class User extends BaseController
{
    public function getUserInfo()
    {
        $u = ServiceUser::get();
        // 根据登录状态返回结果
        if ($u) {
            return $this->toSuccessJson($u->toArray());
        } else {
            return $this->toSuccessJson([
                'auth' => 0
            ]);
        }
    }

    public function login()
    {
        $u = ServiceUser::get();
        if ($u) {
            throw new NormalException('您已登录');
        } else {
            ServiceUser::login();
        }
    }

    public function loginCallback()
    {
        ServiceUser::loginCallback(request()->param());
    }

    /**
     * 带跳转的CAS登出方法
     * 
     * https://github.com/eeyes-net/cas_proxy-2017-04/blob/master/public/index.php
     * 
     * 58~71行 请允许我膜拜一下
     */
    public function logout()
    {
        $u = ServiceUser::get();
        if ($u) {
            ServiceUser::deleteUserSession();
            $redirectUrl = config('url.froutend_logout');
            $this->assign('redirectUrl', $redirectUrl);
            return $this->fetch();
        } else {
            throw new NormalException('您未登录');
        }
    }
}