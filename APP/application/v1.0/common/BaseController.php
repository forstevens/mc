<?php

namespace app\v3\common;

use think\Env;
use think\Controller;

/**
 * 基础控制器，控制器的父类，主要用于调整全局header头和一些公用函数
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 移动文件至common
 */
class BaseController extends Controller
{
    /**
     * 添加跨域访问支持头
     * 注意：生产环境请务必修改环境变量
     */
    public function _initialize()
    {
        if (config('header.acao_on')) {
            header('Access-Control-Allow-Origin:' . config('url.frontend'));
        }
        if (config('header.acac_on')) {
            header('Access-Control-Allow-Credentials: true');
        }
        if (config('header.acam_on')) {
            header('Access-Control-Allow-Methods:GET, POST, DELETE, PUT');
        }
    }

    /**
     * 将data转换为对应的成功json，不传入参数则无data键值对，其余均有data键值对
     * @param array $data 数据
     */
    protected static function toSuccessJson($data = null)
    {
        if ($data === null) {
            $json = [
                'success' => 1,
            ];
        } else {
            $json = [
                'success' => 1,
                'data'    => $data,
            ];
        }
        return json($json);
    }
}