<?php

namespace app\debug\controller;

// 引用API
require_once(APP_PATH . 'v3/common/tzAPIClient.php');

use tiaozhan\lib\api;

use think\Env;
use think\Request;
use think\Response;
use think\exception\HttpResponseException;
use think\exception\ErrorException;
use think\Controller;
use think\Config;
use app\v3\common\Util;
use app\v3\common\NormalException;
use app\v3\common\BaseController;
use app\v3\common\ErrorReports;
use app\v3\logic\LogicApply;
use app\v3\logic\LogicHistory;
use app\v3\logic\LogicUser;
use app\v3\logic\Verifier;
use app\v3\model\ModelApply;
use app\v3\model\ModelHistory;
use app\v3\model\ModelPlace;
use app\v3\model\ModelResource;
use app\v3\model\ModelSystemInfo;
use app\v3\model\ModelUser;
use app\v3\service\ServiceUser;

class Index extends BaseController
{
    private $miss = null;

    protected $beforeActionList = [
        'checkDebugModule',
    ];

    public function checkDebugModule()
    {
        // 这个函数在所有其动作之前执行
        // 若debug_module为关闭，则直接退出控制器
        if (!Config::get('debug_module')) {
            throw new NormalException(config('msg.miss'));
        }
    }

    public function index()
    {
        var_dump(Config::get('app_debug'));
        var_dump(Config::get('online_debug'));
        var_dump(Config::get('debug_module'));
        var_dump(getenv('PHP_APP_DEBUG'));
        var_dump(getenv('PHP_ONLINE_DEBUG'));
        var_dump(getenv('PHP_DEBUG_MODULE'));
        var_dump(phpversion());
        try {
            var_dump(PHP_SESSION_ACTIVE);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        try {
            session_start();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        try {
            var_dump(session_id());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return 'debug-index';
    }

    public function test()
    {
        return json(LogicUser::queryUser(['page'=>1,'limit'=>3]));
        // return json(ModelHistory::all());
        // return json(request()->param());
        // return self::testQueryPublicBoard();
    }

    public function getReportList()
    {
        echo ErrorReports::getReportList();
    }

    public function getReport($id)
    {
        echo ErrorReports::getReportById($id);
    }

    public static function testQueryPublicBoard()
    {
        $req = Request::instance();
        $params = $req->param();
        $data = LogicApply::queryPublicBoard($params);
        return json($data);
    }

    // public static function testGetSystemInfo()
    // {
    //     $d = ['version' => ModelSystemInfo::getInfo('version')];
    //     return json($d);
    // }

    // public static function testPlaceToResource()
    // {
    //     $p = ModelPlace::get(1);
    //     $res = $p->resources;
    //     var_dump($res); // 该place的所有resource模型
    //     foreach ($res as $key => $value) {
    //         $res[$key] = $value->toArray();
    //     }
    //     var_dump($res); // 将所有的模型转换成数组(json对象)
    //     return 'success';
    // }

    // public static function testResourceToPlace()
    // {
    //     $r = ModelResource::get(1);
    //     $res = $r->place;
    //     var_dump($res); // 该resource的父place模型
    //     var_dump($res->toArray()); // 转为数组
    //     return 'success';
    // }

    // public static function testUserToResource()
    // {
    //     $u = ModelUser::get('dhr1698');
    //     $res = $u->resources;
    //     // var_dump($res); // 该user的所有resource模型
    //     foreach ($res as $key => $value) {
    //         $res[$key] = $value->toArray();
    //     }
    //     var_dump($res); // 将所有的模型转换成数组(json对象)
    //     return 'success';
    // }

    // public static function testPlaceToUser()
    // {
    //     $r = ModelResource::get(1);
    //     $res = $r->admin;
    //     var_dump($res); // 该resource的父place模型
    //     var_dump($res->toArray()); // 转为数组
    //     return 'success';
    // }

    public function logout()
    {
        ServiceUser::deleteUserSession();
        return 'success';
    }
}
