<?php

namespace app\v3\common;

use think\Response;
use think\exception\HttpResponseException;

/**
 * NormalException 普通异常，指可以将错误信息显示给用户的异常
 * 该异常继承自HttpResponseException，抛出后将直接由tp5返回Http响应
 * 无须try catch处理返回
 * 信息以json格式返回，值得注意的是，为了方便HTTP响应头仍为200
 * 所以前端一定要根据success来确定是否成功
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 移动文件至common
 */
class NormalException extends HttpResponseException
{
    public function __construct($errMsg)
    {
        $json = [
            'success' => 0,
            'err_msg' => $errMsg,
        ];
        $response = Response::create($json, 'json', 200);
        // 直接抛出TP5框架专有的Http响应异常，退出控制器，直接返回json
        parent::__construct($response);
    }
}