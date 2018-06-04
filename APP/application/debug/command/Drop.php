<?php

namespace app\debug\command;

use think\Db;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Log;

/**
 * Drop 删除数据库中所有的数据表
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 */
class Drop extends Command
{
    protected function configure()
    {
        $this->setName('apply3:drop')
            ->setDescription('删除数据库中所有数据表(慎用)')
            ->setHelp(sprintf('%s删除数据库中所有数据表%s', PHP_EOL, PHP_EOL));
    }

    protected function execute(Input $input, Output $output)
    {
        $con = Db::connect(); // 获取默认数据库连接
        $tables = $con->getTables(); // 获取所有数据表
        if (!$output->confirm($input, '确认删除所有数据表? [y]/n')) {
            $output->writeln('操作已经取消!');
            return;
        }
        foreach ($tables as $t) { // 删除所有数据表
            $output->writeln("正在删除数据表: $t");
            $con->execute("DROP TABLE `$t`"); // 由于在控制台执行，就没有使用绑定参数的模式
        }

        // 记录日志
        Log::write('Command apply:drop 删除数据库中所有数据表','notice');
        $output->writeln("删除完成.");
    }
}