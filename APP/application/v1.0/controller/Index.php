<?php

namespace app\v3\controller;

use app\v3\common\NormalException;
use app\v3\common\BaseController;

/**
 * Index控制器
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class Index extends BaseController
{
    public function index()
    {
        $data = [
            'message' => config('msg.welcome'),
        ];
        return $this->toSuccessJson($data);
    }

    public function miss()
    {
        throw new NormalException(config('msg.miss'));
    }
}
