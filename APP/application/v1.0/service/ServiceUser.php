<?php
namespace app\v3\service;

// 引用API
require_once(APP_PATH . 'v3/common/tzAPIClient.php');

use tiaozhan\lib\api;

use app\v3\common\NormalException;
use app\v3\model\ModelUser;

/**
 * ServiceUser User服务
 * 区别于数据库User模型，该User对象存的是当前登录用户的相关信息
 * 而User模型中的信息并不代表登录状态
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/3/22 修改依赖
 */
class ServiceUser
{
    // 登录验证模式
    // 仅登录即可
    const MODE_LOGIN = 0;
    // 需要管理员权限
    const MODE_ADMIN = 1;
    // 需要超级管理员权限
    const MODE_SUPER_ADMIN = 2;
    // 需要任意一种管理员权限
    const MODE_ANY_ADMIN = 3;

    // 用户NetID
    private $userid;
    // 用户权限 1表示申请者 2表示管理员 3表示超级管理员
    private $auth;
    // 用户其他信息
    private $userinfo;

    /**
     * 构造方法
     * @param string $userid 用户NetID
     * @param int $auth 用户权限
     * @param array $userid 用户详细信息
     */
    private function __construct($userid, $auth, $userinfo)
    {
        $this->userid = $userid;
        $this->auth = $auth;
        $this->userinfo = $userinfo;
    }

    /**
     * 获取当前用户
     * @return ServiceUser 用户信息
     */
    public static function get()
    {
        return session('user');
    }

    /**
     * 令客户端浏览器跳转至登录界面
     */
    public static function login()
    {
        // 设置一个secret来防止XSS攻击
        $secret = md5(strval(rand())) . strval(rand()) . md5(strval(rand()));
        session('secret', $secret);
        // 强制跳转至后端callback
        $urlbase = config('url.backend_login_callback');
        if ($urlbase == '') {
            // 防止忘记设置callback地址
            throw new NormalException('服务器错误');
        }
        $url = $urlbase . '?secret=' . $secret;
        $client = api\Client();
        // 执行登录，登录后将跳转至callback，带有guid和secret参数
        $client->casLogin($url);
    }

    /**
     * 登录的回调，检查用户登录状况
     * @param array $params GET参数
     * @param bool $autoRetry 是否自动尝试再次登录
     */
    public static function loginCallback($params, $autoRetry = true)
    {
        // 检查参数，检查XSS验证
        if (!isset($params['guid']) || !isset($params['secret']) 
        || $params['secret'] != session('secret')) {
            // 删除secret
            session('secret', null);
            // 自动重试登录
            if ($autoRetry) {
                self::login();
            }
            throw new NormalException('登录失败，疑似访问拦截');
        }
        // 删除secret
        session('secret', null);
        // 向挑战api请求用户数据
        $client = api\Client();
        try {
            $data = $client->casLoginCheck($params['guid']);
        } catch (api\ClientException $e) {
            // 自动重试登录
            if ($autoRetry) {
                self::login();
            }
            throw new NormalException('登录失败，疑似超时');
        }
        // 检查登录状况
        if (!isset($data['userid']) || !$data['userid']) {
            throw new NormalException('登录失败，您是否取消了该操作？');
        }
        // 将用户数据存到Session
        $userid = $data['userid'];
        $auth = ModelUser::getUserAuth($userid);
        $userinfo = $data['userinfo'];
        session('user', new self($userid, $auth, $userinfo));

        // 跳转回前端登录后界面
        $url = config('url.froutend_login');
        header('Location: ' . $url);
        exit(0);
    }

    /**
     * 登出
     */
    public static function deleteUserSession()
    {
        session('user', null);
    }

    /**
     * 检查用户登录以及权限
     * @param int $verifyMode 验证模式
     * @return ServiceUser 已经经过验证的用户对象
     */
    public static function verifyUser($verifyMode = self::MODE_LOGIN)
    {
        $u = self::get();
        if (!$u) {
            throw new NormalException('您未登录');
        }
        switch ($verifyMode) {
            case self::MODE_ADMIN:
                if (!$u->isAdmin()) {
                    throw new NormalException('您无权执行此操作');
                }
                break;
            case self::MODE_SUPER_ADMIN:
                if (!$u->isSuperAdmin()) {
                    throw new NormalException('您无权执行此操作');
                }
                break;
            case self::MODE_ANY_ADMIN:
                if (!($u->isAdmin() || $u->isSuperAdmin())) {
                    throw new NormalException('您无权执行此操作');
                }
                break;
        }
        return $u;
    }

    /**
     * 获取当前用户对应的Model模型
     * @return ModelUser 用户
     */
    public function toModel()
    {
        $u = ModelUser::get($this->userid);
        if (!$u) {
            throw new NormalException('服务器错误');
        }
        return $u;
    }

    /**
     * 获取该用户的NetID
     * @return string NetID
     */
    public function getUserID()
    {
        return $this->userid;
    }

    /**
     * 获取该用户的权限等级
     * @return int 权限等级 1表示申请者 2表示管理员 3表示超级管理员
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * 获取该用户的详细信息
     * @return array 详细信息
     */
    public function getUserInfo()
    {
        return $this->userinfo;
    }

    /**
     * 获取该用户的姓名
     * @return string 用户姓名
     */
    public function getUserName()
    {
        return $this->userinfo['username'];
    }

    /**
     * 检查该用户是否是管理员
     * @return bool 是否是管理员
     */
    public function isAdmin()
    {
        return $this->auth == 2;
    }

    /**
     * 检查该用户是否是超级管理员
     * @return bool 是否是超级管理员
     */
    public function isSuperAdmin()
    {
        return $this->auth == 3;
    }

    /**
     * 将User对象转换为数组以方便输出Json
     * @return array User数据
     */
    public function toArray()
    {
        return [
            'userid'   => $this->userid,
            'auth'     => $this->auth,
            'userinfo' => $this->userinfo,
        ];
    }
}