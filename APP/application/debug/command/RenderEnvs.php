<?php

namespace app\debug\command;

use think\Exception;
use think\Config;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\Log;
use app\v3\model\ModelHistory;

/**
 * RenderEnvs 将环境变量配置导入项目配置
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 * 
 * 执行apply3:envs命令后
 * 模板中所有双大括号指定的变量将被替换(字符串替换)为环境变量中的值
 * “模板=>目标文件”的Map默认在/application/extra/envs.php中设置
 * 也手动指定一个文件，格式请参见envs.php
 * 
 * 例如：
 * (文件：.env)
 * [test]
 * param_1 = 123
 * 
 * (文件：abc.php.example)
 * <?php
 * $myparam = {{param_1}};
 * 
 * (文件：envs.php)
 * return [
 *     'abc.php.example' => 'abc.php',
 * ]
 * 
 * 则执行apply3:envs命令后，将会生成一个abc.php文件，内容如下：
 * <?php
 * $myparam = 123;
 */
class RenderEnvs extends Command
{
    const MAX_TRY_COUNT = 10;
    private $maxTryCount = self::MAX_TRY_COUNT;
    protected function configure()
    {
        $this->setName('apply3:renderenvs')
            ->setDescription('将环境变量导入项目配置')
            ->addArgument('envs', Argument::OPTIONAL, '模板路径数组(使用相对路径)')
            ->addArgument('counts', Argument::OPTIONAL, '模板路径数组(使用相对路径)')
            ->setHelp(sprintf('%s将环境变量导入项目配置(模板路径数组默认为extra下的envs.php)%s', PHP_EOL, PHP_EOL));
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('警告：不建议使用此指令');
        // 获取配置参数
        $envsPath = $input->getArgument('envs');
        $counts = $input->getArgument('counts');
        if ($envsPath !== null) {
            $map = require (ROOT_PATH . $envsPath);
        } else {
            $map = config('envs');
        }
        if ($counts !== null) {
            $this->maxTryCount = $counts;
        }
        foreach ($map as $src => $trg) {
            // 检查文件存在
            $this->renderFile($src, $trg);
        }
        // 记录历史
        try {
            ModelHistory::record(ModelHistory::NONE_APPLYID, 'Render Envs', $map);
        } catch (\PDOException $e) {
            $output->writeln('警告,本操作未能记录到数据库中:' . $e->getMessage());
        }
        // 记录日志
        Log::write('Command apply:renderenvs 环境变量导入项目配置','notice');
        $output->writeln("导出完毕.");
    }

    /**
     * 将源文件中的环境变量进行文本替换，渲染到目标文件
     * @param string $source 源文件路径
     * @param string $target 目标文件路径
     * @return bool 渲染是否成功
     */
    private function renderFile($source, $target)
    {
        // 检查文件存在
        if (!file_exists($source)) {
            $this->output->writeln('文件' . $source . '不存在,操作终止!');
            return false;
        }
        // 防止打开出错
        try {
            $fileSource = fopen($source, 'r');
            $fileTarget = fopen($target, 'w');
            $inText = fread($fileSource, filesize($source));
            fclose($fileSource);
        } catch (Exception $e) {
            $this->output->writeln('打开文件出错: ' . $e->getMessage());
            $this->output->writeln('操作已终止.');
            return false;
        }
        // 读取源文件中所有需要替换的变量名
        $matches = [];
        preg_match_all("/{{([\w\.]+)}}/", $inText, $matches);
        $names = $matches[1];
        // 读取对应环境变量值
        $values = [];
        foreach ($names as $name) {
            $value = $this->getEnv($name);
            if (false !== $value) {
                $values[] = $value;
            } else {
                $this->output->writeln("文件 {$source} 的渲染已经终止！");
                return false;
            }
        }
        // 文本替换
        $outText = str_replace($matches[0], $values, $inText);
        fwrite($fileTarget, $outText);
        fclose($fileTarget);
        $this->output->writeln("文件 {$target} 导出成功！");
        return true;
    }

    /**
     * 获取名为$name的环境变量的值
     * 最多尝试$maxTryCount次获取
     * @param string $name 变量名
     * @return string 环境变量值
     */
    private function getEnv($name)
    {
        // 这一行摘自TP5
        $fullname = ENV_PREFIX . strtoupper(str_replace('.', '_', $name));
        for ($i = 0; $i < $this->maxTryCount;) {
            $result = getenv($fullname);
            if (false !== $result) {
                break;
            }
            $i++;
            $this->output->writeln("尝试第{$i}次获取环境变量{$name}失败，正在重试！");
        }
        if (false !== $result) {
            $result = self::parseBool($result);
            if (strpos($name, 'password')) {
                $this->output->writeln("环境变量{$name}获取成功，内容已隐藏！");
            } else {
                $this->output->writeln("环境变量{$name}获取成功：{$result}");
            }
            return $result;
        }
        $this->output->writeln("环境变量{$name}获取失败！");
        return false;
    }

    /**
     * 检测$value值是否是布尔(主要应对坑爹的PHP环境变量自动替换)
     * 该函数可能会因平台/PHP版本不同而异
     * 并以此调整返回值
     * @param mixed $value 传入的值
     * @return string 原值或者经过转换的值
     */
    private static function parseBool($value)
    {
        if ($value == '1') {
            return 'true';
        }
        if ($value == '') {
            return 'false';
        }
        return $value;
    }
}