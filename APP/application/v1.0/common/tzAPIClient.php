<?php

/**
 * Tiaozhan API Service Client
 *
 * PHP Library for Tiaozhan API Service
 *
 * @package    api
 *
 * @author     shiwenbo <shiwenbo@tiaozhan.com>
 * @author     xczh <zhuxingchi@tiaozhan.com>
 *
 * @copyright  Copyright (C) 2017 Tiaozhan Net. All Rights Reserved.
 * @link       https://git.tiaozhan.com/swenb/tzapi-client-php
 */

namespace tiaozhan\lib\api;

/** Global **/

/** Functions **/

/**
 * Obtain a API Client with specific configuration
 *
 * @param  version   const  'the Client version'
 * @param  protocol  const  'the protocol to interactive with API Server'
 * @param  host      const  'the API Server's hostname'
 * @param  debug     bool   'enable debug or not'
 *
 * @return Client
 * @throws ClientException
 */
function Client($version = Client::API_VERSION_V2, $protocol = Client::API_PROTOCOL_HTTPS, $host = Client::API_HOST, $debug = false)
{
    $classname = __NAMESPACE__ . '\\Client' . strtoupper($version);
    if (class_exists($classname)) {
        return new $classname($protocol, $host, $debug);
    } else {
        throw new ClientException('Unsupported Client Version: '.$version, ClientException::E_INVALID_PARAM);
    }
}

/** Client Class **/

abstract class Client
{
    const API_VERSION_V2 = 'v2';
    const API_PROTOCOL_HTTPS = 'https';
    const API_PROTOCOL_HTTP = 'http';
    const API_HOST = 'api.tiaozhan.com';

    protected $useHTTPS = null;
    protected $debug = null;
    protected $host = '';
    protected $curl_handle = null;
    protected $curl_version = '';

    public function __construct($protocol, $host, $debug)
    {
        if ($this->useHTTPS === null) {
            $this->useHTTPS = ($protocol === self::API_PROTOCOL_HTTPS ? true : false);
        }
        if (!function_exists('curl_init') || !function_exists('curl_exec')) {
            throw new ClientException('cURL extension is not found', ClientException::E_BAD_ENVIRONMENT);
        } else {
            // cURL init
            $this->curl_version = curl_version();
            $this->curl_handle = curl_init();
            if ($this->useHTTPS && !(CURL_VERSION_SSL & $this->curl_version['features'])) {
                throw new ClientException('your cURL extension not support HTTPS', ClientException::E_BAD_ENVIRONMENT);
            }
            curl_setopt($this->curl_handle, CURLOPT_HEADER, false);
            curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($this->curl_handle, CURLOPT_ENCODING, '');
            if (defined('CURLOPT_PROTOCOLS')) {
                curl_setopt($this->curl_handle, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            }
            if (defined('CURLOPT_REDIR_PROTOCOLS')) {
                curl_setopt($this->curl_handle, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            }
        }
        if ($this->host === '') {
            $this->host = $host;
        }
        if ($this->debug === null) {
            $this->debug = boolval($debug);
        }
    }

    public function __destruct()
    {
        if (is_resource($this->curl_handle)) {
            curl_close($this->curl_handle);
        }
    }

    protected static function getBaseUrl($version, $protocol=self::API_PROTOCOL_HTTPS, $host=self::API_HOST)
    {
        return $protocol . '://' . $host . '/' . $version;
    }

    protected function httpGet($url, $headers = array(), $options = array())
    {
        return self::httpRequest($url, $headers, null, 'GET', $options);
    }

    protected function httpPost($url, $headers = array(), $data=array(), $options = array())
    {
        return self::httpRequest($url, $headers, $data, 'POST', $options);
    }

    protected function httpRequest($url, $headers = array(), $data = array(), $type = 'GET', $options = array())
    {
        $default_options = array(
            'timeout' => 10,
            'connect_timeout' => 3,
            'useragent' => 'tzAPIClient-php',
            'referer' => '',
            'need_header' => false,
            'need_body' => true,
        );
        $options = array_merge($default_options, $options);
        // process array or object type body
        if (!empty($data) && !is_string($data)) {
            $data = http_build_query($data, null, '&');
        }
        switch ($type) {
            case 'POST':
                curl_setopt($this->curl_handle, CURLOPT_POST, true);
                curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $data);
                break;
            case 'HEAD':
                curl_setopt($this->curl_handle, CURLOPT_CUSTOMREQUEST, 'HEAD');
                curl_setopt($this->curl_handle, CURLOPT_NOBODY, true);
                break;
            case 'PATCH':
            case 'PUT':
            case 'DELETE':
            case 'OPTIONS':
            default:
                curl_setopt($this->curl_handle, CURLOPT_CUSTOMREQUEST, $type);
                if (!empty($data)) {
                    curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $data);
                }
        }
        curl_setopt($this->curl_handle, CURLOPT_TIMEOUT, ceil($options['timeout']));
        curl_setopt($this->curl_handle, CURLOPT_CONNECTTIMEOUT, ceil($options['connect_timeout']));
        curl_setopt($this->curl_handle, CURLOPT_URL, $url);
        curl_setopt($this->curl_handle, CURLOPT_REFERER, $options['referer'] or $url);
        curl_setopt($this->curl_handle, CURLOPT_USERAGENT, $options['useragent']);
        curl_setopt($this->curl_handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        if (!empty($headers)) {
            curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($this->curl_handle, CURLOPT_HEADER, $options['need_header']);
        curl_setopt($this->curl_handle, CURLOPT_NOBODY, !$options['need_body']);

        if (isset($options['verify'])) {
            if ($options['verify'] === false) {
                curl_setopt($this->curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($this->curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
            } elseif (is_string($options['verify'])) {
                curl_setopt($this->curl_handle, CURLOPT_CAINFO, $options['verify']);
            }
        }
        if (isset($options['verifyname']) && $options['verifyname'] === false) {
            curl_setopt($this->curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
        }
        $response = curl_exec($this->curl_handle);
        if (false === $response) {
            throw new ClientException('('.curl_errno($this->curl_handle).') '.curl_error($this->curl_handle), ClientException::E_CURL_ERROR);
        }
        $info = curl_getinfo($this->curl_handle);
        $header = '';
        $body = '';
        if ($options['need_header']) {
            $header = substr($response, 0, $info['header_size']);
            $body = substr($response, $info['header_size']);
        } elseif ($options['need_body']) {
            $body = $response;
        }
        return array($header, $body, $info);
    }

    protected static function getCurrentUrl()
    {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }
}

class ClientV2 extends Client
{
    const API_VERSION = 'v2';
    const API_CLIENT_VERSION = '2.0rc3';

    private $baseUrl = '';
    private $post_login_hook = null;

    public function __construct($protocol, $host, $debug)
    {
        parent::__construct($protocol, $host, $debug);
        $this->baseUrl = self::getBaseUrl(self::API_VERSION, $protocol, $host);
    }

    protected function httpGet($url, $headers = array(), $options = array())
    {
        $result = parent::httpGet($this->baseUrl . $url);
        if ($result[2]['http_code']!=200) {
            throw new ClientV2Exception("API Server Bad HTTP Code: ".$result[2]['http_code'], ClientV2Exception::E_API_ERROR);
        }
        $result[1] = json_decode($result[1], true);
        if ($result[1]['code'] != 0) {
            throw new ClientV2Exception("API Server Error: ".$result[1]['msg'], ClientV2Exception::E_API_ERROR);
        }
        return $result;
    }

    /**
     * 获得当前Client的版本
     *
     * @return  const  版本号
     */
    public static function getClientVer()
    {
        return self::API_CLIENT_VERSION;
    }

    /**
     * 获得API Server运行状态信息
     *
     * @return array 服务器端运行状态信息
     * @throws ClientException
     */
    public function getServerInfo()
    {
        $result = $this->httpGet('/server_info');
        return $result[1]['data'];
    }

    /**
     * 获得当前Server的版本
     *
     * @return  const  版本号
     */
    public function getServerVer()
    {
        $server_info = $this->getServerInfo();
        return $server_info['version'];
    }

    /**
     * 当前客户端是否过期
     *
     * @return  bool  是否过期
     */
    public function needUpdate()
    {
        return self::getServerVer() > self::getClientVer();
    }

    /**
     * 根据Userid(NetID)获取用户信息
     *
     * @param $userid string "the user's netid"
     *
     * @see self::getUserInfo
     */
    public function getUserInfoByUserid($userid)
    {
        return $this->getUserInfo('userid', $userid);
    }

    /**
     * 根据Userno获取用户信息
     *
     * @param $userno string "the user number"
     *
     * @see self::getUserInfo
     */
    public function getUserInfoByUserno($userno)
    {
        return $this->getUserInfo('userno', $userno);
    }

    /**
     * 根据Cardno获取用户信息
     *
     * @param $cardno string "the ID card number"
     *
     * @see self::getUserInfo
     */
    public function getUserInfoByCardno($cardno)
    {
        return $this->getUserInfo('cardno', $cardno);
    }

    /**
     * 根据Username获取用户信息
     *
     * @param $username string "the user's chinese name'"
     *
     * @see self::getUserInfo
     */
    public function getUserInfoByUsername($username)
    {
        return $this->getUserInfo('username', $username);
    }

    /**
     * 根据Mobile获取用户信息
     *
     * @param $mobile string "the user's mobile number"
     *
     * @see self::getUserInfo
     */
    public function getUserInfoByMobile($mobile)
    {
        return $this->getUserInfo('mobile', $mobile);
    }

    /**
     * 获取XJTU用户信息
     *
     * @param   $key        string  查询条件
     * @param   $value      string  查询值
     * @param   $use_cache  bool    是否允许使用缓存数据（启用能极大的提高查询效率）
     *
     * @return  array   用户信息数组
     * @throws  ClientV2Exception ClientException
     */
    public function getUserInfo($key, $value, $use_cache=true)
    {
        if (empty($key) || empty($value)) {
            throw new ClientV2Exception('param key and value must not be empty', ClientV2Exception::E_INVALID_PARAM);
        }
        $url = "/xjtuuser/$key/$value";
        if (!$use_cache) {
            $url = $url.'?cache=0';
        }
        $result = $this->httpGet($url);
        return $result[1]['data'];
    }

    /**
     * 根据userno获取用户照片
     *
     * @param  $userno  string "学生学号/教师工号"
     *
     * @see self::getUserPhoto
     */
    public function getUserPhotoByUserno($userno)
    {
        return $this->getUserPhoto('userno', $userno);
    }

    /**
     * 获取XJTU用户照片
     *
     * @param   $key        string  查询条件
     * @param   $value      string  查询值
     *
     * @return  string  用户照片的base64编码字符串（二进制编码格式JPG）
     * @throws  ClientV2Exception ClientException
     */
    public function getUserPhoto($key, $value)
    {
        if (empty($key) || empty($value)) {
            throw new ClientV2Exception('param key and value must not be empty', ClientV2Exception::E_INVALID_PARAM);
        }
        $url = "/xjtuuserPhoto/$value";
        $result = $this->httpGet($url);
        return $result[1]['data'];
    }

    /**
     * 注册自定义登陆回调函数(Post Login Hook)
     *
     * @param $post_login_hook  callable  登陆后触发的Hook，无返回值，调用后必须使得boolval(call_user_func($pre_login_hook))===true
     *                                    函数声明    function post_login_hook(array $user) $user为用户信息数组
     */
    public function registerLoginCallback(callable $post_login_hook)
    {
        if (true === is_callable($post_login_hook)) {
            $this->post_login_hook = $post_login_hook;
        } else {
            throw new ClientV2Exception(var_export($post_login_hook, true). ' is not a valid callable', ClientV2Exception::E_INVALID_PARAM);
        }
    }

    /**
     * 要求使用CAS登陆
     *
     * @param  $redirect_url  string  登陆完毕后浏览器将重定向回到此URL
     * @param  $return        bool    设置为为true时仅返回重定向目标URL，不发送Location头
     *
     */
    public function casLogin($redirect_url='', $return = false)
    {
        if (!$redirect_url) {
            $redirect_url = parent::getCurrentUrl();
        }
        $url = $this->baseUrl . '/casLogin?redirect_url=' . urlencode($redirect_url);
        if ($return) {
            return $url;
        }
        header('Location: '.$url);
        exit(0);
    }

    /**
     * 检查登陆完毕的用户身份GUID
     *
     * @param  $guid  string  登陆完毕后带回的GET参数guid
     *
     * @throws ClientV2Exception ClientException
     */
    public function casLoginCheck($guid = '')
    {
        if (!$guid && isset($_SERVER['QUERY_STRING'])) {
            // if guid param is empty, try to obtain it from current QUERY_STRING
            parse_str($_SERVER['QUERY_STRING'], $parsed_array);
            if (true === array_key_exists('guid', $parsed_array)) {
                $guid = $parsed_array['guid'];
                unset($parsed_array);
            }
        }
        if (!$guid) {
            throw new ClientV2Exception('guid is empty', ClientV2Exception::E_GUID_EMPTY);
        }
        $result = $this->httpGet("/casLoginCheck?guid=$guid");
        if ($this->post_login_hook) {
            return call_user_func($this->post_login_hook, $result[1]['data']);
        } else {
            return $result[1]['data'];
        }
    }
}

/** Context **/

/**
 * 使用Session会话管理登陆状态的上下文管理器类
 *
 * 为了更便于使用ClientV2类而设计的简化管理器
 */
class ClientV2SessionContext
{
    const DEFAULT_LOGIN_HOOK_SESSIONID = 'tzAPIClient-php';

    protected $client = null;

    public function __construct(ClientV2 $client=null)
    {
        if (null === $client) {
            $client = Client(Client::API_VERSION_V2);
        }
        $client->registerLoginCallback(function (array $user) {
            if (!empty($user) && !empty($user['userid'])) {
                self::ensureSessionStarted();
                $_SESSION[self::DEFAULT_LOGIN_HOOK_SESSIONID] = $user;
            }
        });
        $this->client = $client;
    }

    public function __call($func, $args)
    {
        if (true === is_callable(array($this->client, $func), false, $callable_name)) {
            return call_user_func_array(array(&$this->client, $callable_name), $args);
        }
        throw new ClientV2Exception('Called a not exist method ('. var_export($func, true). ')', ClientV2Exception::E_METHOD_NOT_EXISTS);
    }

    protected static function isSessionStarted()
    {
        if (php_sapi_name() !== 'cli') {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? true : false;
            } else {
                return session_id() === '' ? false : true;
            }
        }
        return false;
    }

    protected static function ensureSessionStarted()
    {
        if (false === self::isSessionStarted()) {
            session_start();
        }
    }

    /**
     * 获取当前登陆用户信息
     *
     * @return  array  用户信息数组，若未登陆则返回空数组
     */
    public static function getLoginUser()
    {
        self::ensureSessionStarted();
        if (array_key_exists(self::DEFAULT_LOGIN_HOOK_SESSIONID, $_SESSION)) {
            return $_SESSION[self::DEFAULT_LOGIN_HOOK_SESSIONID];
        }
        return array();
    }

    /**
     * 判断用户是否已登陆
     *
     * @return bool 是否已登陆
     */
    public static function isLogined()
    {
        return boolval(self::getLoginUser());
    }

    /**
     * 强制用户登陆
     *
     * 用户已登陆则直接返回，未登陆则重定向至CAS进行认证
     *
     * @throws ClientV2Exception ClientException
     */
    public function forceLogin()
    {
        if ($this->isLogined()) {
            return;
        }
        try {
            $this->client->casLoginCheck();
        } catch (ClientV2Exception $e) {
            if (ClientV2Exception::E_GUID_EMPTY == $e->getCode()) {
                $this->client->casLogin();
            } else {
                throw $e;
            }
        }
    }
    
    /**
     * 强制用户登出
     *
     * 如果用户已登陆，则删除本Context的登录状态
     * 如果需要清除CAS系统的登录状态，会将浏览器重定向到CAS系统的登出页面
     *
     * @param $CASLogout boolean 是否清除CAS系统的登录状态
     */
    public function forceLogout($CASLogout = true)
    {
        if ($this->isLogined()) {
            self::ensureSessionStarted();
            $_SESSION[self::DEFAULT_LOGIN_HOOK_SESSIONID] = array();
        }
        if($CASLogout == true) {
            header('Location: https://cas.xjtu.edu.cn/logout');
            exit(0);
        }
    }
}

/** Exceptions **/

class ClientException extends \Exception
{
    const E_INVALID_PARAM = 10001;
    const E_BAD_ENVIRONMENT = 10002;
    const E_CURL_ERROR = 10003;
    

    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

class ClientV2Exception extends ClientException
{
    const E_API_ERROR = 20001;
    const E_INVALID_PARAM = 20002;
    const E_GUID_EMPTY = 20003;
    const E_METHOD_NOT_EXISTS = 20004;
}
