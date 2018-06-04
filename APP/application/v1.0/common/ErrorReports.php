<?php

namespace app\v3\common;

use think\exception\ErrorException;

/**
 * 错误报告，方便debug和v3同时使用而提取至common
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * Last update: 18/4/1 创建ErrorReports类
 */
class ErrorReports
{
    /**
     * 获取错误全部错误报告
     * @return string html字符串
     */
    public static function getReportList()
    {
        // 如果文件夹不存在则创建
        !is_dir(REPORT_PATH) && mkdir(REPORT_PATH, 0755, true);
        // 打开文件夹
        $handler = opendir(REPORT_PATH);
        $resultHtml = "";
        // 读取所有文件
        while (($fname = readdir($handler)) !== false) {
            // 过滤Linux下的“.”和“..”
            if ($fname == '.' || $fname == '..') {
                continue;
            }
            // 准备html
            $id = substr($fname,0,strpos($fname, '.'));
            $time = substr($fname,0,strpos($fname, '.') - 5);
            $time = date("Y-m-d H:i:s", $time);
            // 结果html
            $resultHtml = $resultHtml . "<p><a href=\"report\\${id}\">${time} ${id}</a></p>";
        }
        // 关闭目录
        closedir($handler);
        return $resultHtml; // 返回结果html字符串
    }

    /**
     * 通过id获取某个错误报告
     * @param int $id 错误报告id（时间戳+随机数标识码）
     * @return string html字符串
     */
    public static function getReportById($id)
    {
        // 构建路径
        $path = REPORT_PATH . $id . ".html";
        // 打开文件，不存在则返回无结果
        try {
            $report = fopen($path, "r");
        } catch (ErrorException $e) {
            return "Report no found";
        }
        // 否则读取html内容
        $resultHtml = fread($report,filesize($path));
        // 关闭文件
        fclose($report);
        return $resultHtml;
    }
}