<?php

namespace app\v3\service;

use think\Env;
use app\v3\common\Verifier;
use app\v3\common\NormalException;
use app\v3\model\ModelHistory;

/**
 * ServiceMessage类，封装管理短信服务API
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * Last update: 18/3/22 移动文件至service
 */
class ServiceMessage
{
    const STATUS_STR = [
        "0" => "短信发送成功",
        "-1" => "参数不全",
        "-2" => "服务器空间不支持", // 请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！
        "30" => "密码错误",
        "40" => "账号不存在",
        "41" => "余额不足",
        "42" => "帐户已过期",
        "43" => "IP地址限制",
        "50" => "内容含有敏感词"
    ];

    public static function getStatusStr($code)
    {
        if (array_key_exists($code, self::STATUS_STR)) {
            return self::STATUS_STR[$code];
        } else {
            return '未知错误';
        }
    }

    /**
     * 进行发送短信的方法
     * 发送成功直接返回，未成功发送时会抛出NormalException
     *
     * @param $content string 需要发送的短信内容
     * @param $phone   string 短信发送至的手机号
     */
    public static function sendMessage($content, $phone)
    {
        // 获取环境变量并检查
        $smsapi = config('sms.api');
        $username = config('sms.username');
        $password = config('sms.password');
        if ($username == '' || $password == '') {
            throw new NormalException('服务器错误，无法发送短信');
        }

        // 配置参数
        $params = [
            'content' => $content,
            'phone'   => $phone,
        ];
        // 检查短信内容
        Verifier::verifyString($params, 'content', '短信内容', 300, true);
        // 检查手机号
        Verifier::verifyPhone($params, 'phone', true);

        $sendurl = $smsapi
            . "?u=". $username
            . "&p=" . md5($password)
            . "&m=" . $phone
            . "&c=" . urlencode($content);

        // 初始化
        $curl = curl_init();
        // 设置请求的url
        curl_setopt($curl, CURLOPT_URL, $sendurl);
        // 设置获取的信息以文件流的形式返回，而不是直接输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        // 跳过SLL证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // 不从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        // 执行命令
        $result = curl_exec($curl);
        // 关闭URL请求
        curl_close($curl);

        if ($result == 0) {
            ModelHistory::record(0, 'Message success', $params);
            return; // 发送成功
        } else {
            $errMsg = '短信发送失败:' . self::getStatusStr($result) . '，请联系挑战网';
            $params['err_msg'] = $errMsg;
            ModelHistory::record(0, 'Message error', $params);
            throw new NormalException($errMsg); // 抛出异常
        }
    }
}