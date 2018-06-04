<?php

namespace app\v3\common;

/**
 * 验证层，该层封装了大部分基础的参数检查，是一个纯工具类，全部为静态函数
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 * Last update: 18/3/22 移动文件至common
 */
class Verifier
{
    /**
     * 验证名为$param的参数是否是正整数，是正整数则转换为int类型
     * @param array $data 输入数据
     * @param string $param 参数名(如id或applyid等)
     * @param string $name 参数含义
     * @param bool $strict 是否强制要求该参数
     * @param function $extra 额外验证函数，传入一个参数$num
     * @return bool 是否符合要求
     */
    public static function verifyAndConvertNum(&$data, $param, $name, $strict = false, $extra = null)
    {
        if ($strict && !isset($data[$param])) {
            throw new NormalException('缺少必要参数:' . $name);
        }
        if (isset($data[$param])) {
            if (is_string($data[$param]) && !ctype_digit($data[$param])) {
                throw new NormalException($name . '不正确');
            }
            $num = intval($data[$param]);
            // 执行额外验证
            if ($extra !== null) {
                $r = $extra($num);
                if ($r !== null && !boolval($r)) {
                    throw new NormalException($name . '不正确');
                }
            }
            $data[$param] = $num;
            return true;
        }
        return false;
    }

    /**
     * 验证时间段是否正确，正确则转换为时间字符串
     * @param array $data 输入数据
     * @param bool $strict 是否强制要求该参数
     * @param function $extra 额外验证函数，传入两个参数$startTime, $endTime
     * @return bool 是否符合要求
     */
    public static function verifyAndConvertTime(&$data, $strict = false, $extra = null)
    {
        if ($strict) {
            if (!isset($data['start_time'])) {
                throw new NormalException('缺少必要参数:开始时间');
            }
            if (!isset($data['end_time'])) {
                throw new NormalException('缺少必要参数:结束时间');
            }
        }
        if (isset($data['start_time'])) {
            if (!isset($data['end_time'])) {
                throw new NormalException('时间段不成对');
            }
            if (!ctype_digit($data['start_time']) || !ctype_digit($data['end_time'])) {
                throw new NormalException('时间段不存在');
            }
            $startTime = intval($data['start_time']);
            $endTime = intval($data['end_time']);
            // intval会将过大的时间转换成2038年左右的时间，故不必检查最大值
            if ($startTime < 1000000000 || $endTime < 1000000000) {
                throw new NormalException('时间段不存在');
            }
            if ($endTime <= $startTime) {
                throw new NormalException('时间段太短');
            }
            // 执行额外验证
            if ($extra !== null) {
                $r = $extra($startTime, $endTime);
                if ($r !== null && !boolval($r)) {
                    throw new NormalException('时间段不正确');
                }
            }
            $data['start_time'] = date("Y-m-d H:i:s", $startTime);
            $data['end_time'] = date("Y-m-d H:i:s", $endTime);
            return true;
        }
        if (isset($data['end_time'])) {
            if (!isset($data['start_time'])) {
                throw new NormalException('时间段不成对');
            }
        }
        return false;
    }

    /**
     * 验证时间字符串是否正确，正确则转换为时间
     * @param array $data 输入数据
     * @param string $param 参数名
     * @param string $name 参数含义
     * @param bool $strict 是否强制要求该参数
     * @param function $extra 额外验证函数，传入两个参数$startTime, $endTime
     * @return bool 是否符合要求
     */
    public static function verifyAndConvertSingleTime(&$data, $param, $name, $strict = false, $extra = null)
    {
        if ($strict && !isset($data[$param])) {
            throw new NormalException('缺少必要参数:' . $name);
        }
        if (isset($data[$param])) {
            if (!ctype_digit($data[$param])) {
                throw new NormalException($name . '不正确');
            }
            $time = intval($data[$param]);
            // intval会将过大的时间转换成2038年左右的时间，故不必检查最大值
            if ($time < 1000000000) {
                throw new NormalException('时间不正确');
            }
            // 执行额外验证
            if ($extra !== null) {
                $r = $extra($time);
                if ($r !== null && !boolval($r)) {
                    throw new NormalException($name . '不正确');
                }
            }
            $data[$param] = date("Y-m-d H:i:s", $time);
            return true;
        }
        return false;
    }

    /**
     * 验证名为$param的参数是否是长度限制内的字符串
     * @param array $data 输入数据
     * @param string $param 参数名
     * @param string $name 参数含义
     * @param int $maxLength 最大长度
     * @param bool $strict 是否强制要求该参数
     * @param function $extra 额外验证函数，传入一个参数$string
     * @return bool 是否符合规范
     */
    public static function verifyString($data, $param, $name, $maxLength = null, $strict = false, $extra = null)
    {
        if ($strict && !isset($data[$param])) {
            throw new NormalException('缺少必要参数:' . $name);
        }
        if (isset($data[$param])) {
            $string = $data[$param];
            if (!is_string($string)) {
                throw new NormalException($name . '不正确');
            }
            // 检查长度
            $len = strlen($string);
            if ($len <= 0) {
                throw new NormalException($name . '太短');
            } else if ($maxLength !== null && $len > $maxLength) {
                throw new NormalException($name . '太长');
            }
            // 执行额外验证
            if ($extra !== null) {
                $r = $extra($string);
                if ($r !== null && !boolval($r)) {
                    throw new NormalException($name . '不正确');
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 验证名为$param的参数是否是电话号码
     * @param array $data 输入数据
     * @param string $param 参数名
     * @param bool $strict 是否强制要求该参数
     * @param function $extra 额外验证函数，传入一个参数$phone
     * @return bool 是否符合规范
     */
    public static function verifyPhone($data, $param, $strict = false, $extra = null)
    {
        if ($strict && !isset($data[$param])) {
            throw new NormalException('缺少必要参数:手机号码');
        }
        if (isset($data[$param])) {
            $phone = $data[$param];
            if (!is_string($data[$param])) {
                $phone = strval($phone);
            }
            if (!preg_match('/^1\d{10}$/', $phone)) {
                throw new NormalException('手机号格式不正确');
            }
            // 执行额外验证
            if ($extra !== null) {
                $r = $extra($phone);
                if ($r !== null && !boolval($r)) {
                    throw new NormalException('手机号不正确');
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 验证名为$param的参数是否是长度限制内的Json字符串，默认不限制长度
     * @param array $data 输入数据
     * @param string $param 参数名
     * @param string $name 参数含义
     * @param int $maxLength 最大长度(使用null代表不限制最长)
     * @param bool $strict 是否强制要求该参数
     * @param function $extra 额外验证函数，传入一个参数$json(已经解析了的json)
     * @param bool $assoc true:传入的json为数组格式 false:传入的json为对象格式
     * @return bool 是否符合规范
     */
    public static function verifyJson($data, $param, $name, $maxLength = null, $strict = false, $extra = null, $assoc = false)
    {
        if ($strict && !isset($data[$param])) {
            throw new NormalException('缺少必要参数:' . $name);
        }
        if (isset($data[$param])) {
            if (!is_string($data[$param])) {
                throw new NormalException($name . '不正确');
            }
            $len = strlen($data[$param]);
            if ($len <= 0) {
                throw new NormalException($name . '太短');
            }
            if ($maxLength !== null && $len > $maxLength) {
                throw new NormalException($name . '太长');
            }
            $json = json_decode($data[$param], $assoc);
            if (json_last_error() != JSON_ERROR_NONE) {
                throw new NormalException('参数 ' . $name . ' 不是一个合法Json');
            }
            // 执行额外验证
            if ($extra !== null) {
                $r = $extra($json);
                if ($r !== null && !boolval($r)) {
                    throw new NormalException($name . '不正确');
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 验证名为$param的参数是否是长度限制内的Json字符串，是则将其转换为Json，默认不限制长度
     * @param array $data 输入数据
     * @param string $param 参数名
     * @param string $name 参数含义
     * @param int $maxLength 最大长度(使用null代表不限制最长)
     * @param bool $strict 是否强制要求该参数
     * @param function $extra 额外验证函数，传入一个参数$json(已经解析了的json)
     * @param bool $assoc true:传入的json为数组格式 false:传入的json为对象格式
     * @return bool 是否符合规范
     */
    public static function verifyAndConvertJson(&$data, $param, $name, $maxLength = null, $strict = false, $extra = null, $assoc = false)
    {
        if (self::verifyJson($data, $param, $name, $maxLength, $strict, $extra, $assoc)) {
            $data[$param] = json_decode($data[$param], $assoc);
            return true;
        }
        return false;
    }
}