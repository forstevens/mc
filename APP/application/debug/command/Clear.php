<?php

namespace app\debug\command;

use think\Db;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\Log;

/**
 * Clear 清空数据库但不删除数据表
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 */
class Clear extends Command
{
    protected function configure()
    {
        $this->setName('apply3:clear')
            ->setDescription('清空数据库但不删除数据表')
            ->addArgument('migrations', Argument::OPTIONAL, '是否清空migrations')
            ->setHelp(sprintf('%s清空数据库但不删除数据表%s', PHP_EOL, PHP_EOL));
    }

    protected function execute(Input $input, Output $output)
    {
        // 连接数据库
        $con = Db::connect();
        $tables = $con->getTables();

        // 再次确认是否清空数据库
        $flag = $input->getArgument('migrations');
        if (!$output->confirm($input, '确认清空数据库? [y]/n')) {
            $output->writeln('操作已经取消!');
            return;
        }

        // 确认是否清空migrations
        if ($flag !== null) {
            if ($output->confirm($input, '确认连同migrations清空? [y]/n')) {
                $output->writeln('migrations将被清空!');
                $flag = false;
            } else {
                $output->writeln('migrations将被跳过!');
                $flag = true;
            }
        } else {
            $flag = true;
        }

        $clearTables = [];
        // 清空数据表
        foreach ($tables as $t) {
            if ($t == 'migrations' && $flag) {
                $output->writeln("跳过数据表: $t");
            } else {
                $output->writeln("正在清空数据表: $t");
                $con->execute("TRUNCATE `$t`"); // 由于在控制台执行，就没有使用绑定参数的模式
                $clearTables[] = $t;
            }
        }

        // 记录日志
        Log::write('Command apply:clear 清空数据库中所有数据表','notice');
        $output->writeln("清空完成.");
    }
}