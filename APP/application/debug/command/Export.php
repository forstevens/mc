<?php

namespace app\debug\command;

use think\Db;
use think\Exception;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\Log;
use app\v3\model\ModelHistory;

/**
 * Export 将数据库中某数据表导出到文件
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 */
class Export extends Command
{
    // 数据库连接
    private $connection;

    protected function configure()
    {
        $this->setName('apply3:export')
            ->setDescription('将指定数据表导出到文件(第一个参数为数据表名,第二个参数为文件名)')
            ->addArgument('table', Argument::REQUIRED, '数据表名')
            ->addArgument('file', Argument::OPTIONAL, '导出文件名')
            ->addArgument('auto', Argument::OPTIONAL, '是否自动覆盖')
            ->setHelp(sprintf('%s将某数据表中的数据导出成json数组到文件%s', PHP_EOL, PHP_EOL));
    }

    protected function execute(Input $input, Output $output)
    {
        // 获取参数
        $table = $input->getArgument('table');
        $file = $input->getArgument('file');

        // 检查数据表是否存在
        $this->connection = Db::connect();
        $tables = $this->connection->getTables();

        $outTables = [];
        if ($table == '*' || $table == 'all') {
            foreach ($tables as $t) {
                // 忽略file参数，强制向默认位置输出
                $path = ROOT_PATH . 'database' . DS . 'export' . DS . $t . '.json';
                if ($this->exportTable($t, $path)) {
                    $outTables[] = $t;
                }
            }
        } else {
            if (!in_array($table, $tables)) {
                $output->writeln('数据表不存在,操作已终止.');
                return;
            }
    
            // 若未指定文件，则指向默认文件
            if ($file === null) {
                $file = ROOT_PATH . 'database' . DS . 'export' . DS . $table . '.json';
            }
    
            // 导出数据表
            if ($this->exportTable($table, $file)) {
                $outTables[] = $table;
            }
        }

        // 记录历史
        if (count($outTables) > 0) {
            try {
                ModelHistory::record(ModelHistory::NONE_APPLYID, 'Export Database', ['tables' => $outTables]);
            } catch (\PDOException $e) {
                $output->writeln('警告,本操作未能记录到数据库中:' . $e->getMessage());
            }
            // 记录日志
            Log::write('Command apply:export 导出数据库','notice');
        }
        $output->writeln("导出完毕.");
    }

    /**
     * 导出数据表
     * @param string $table 数据表名称
     * @param string $path 导出文件路径
     * @return bool 是否成功导出
     */
    public function exportTable($table, $path)
    {
        // 检查覆盖
        if (file_exists($path)) {
            $this->output->writeln("正准备导出数据表: $table");
            $auto = $this->input->getArgument('auto');
            if ($auto) {
                $this->output->writeln('文件自动覆盖.');
            } else {
                if (!$this->output->confirm($this->input, '文件' . $path . '已存在,是否覆盖? [y]/n')) {
                    $this->output->writeln('操作已取消.');
                    return false;
                }
            }
        }

        // 防止打开出错
        try {
            $file = fopen($path, 'w');
        } catch (ErrorException $e) {
            $this->output->writeln('打开文件出错: ' . $e->getMessage());
            $this->output->writeln('操作已终止.');
            return false;
        }

        // 读取数据
        $res = $this->connection->query("SELECT * FROM `$table`"); // 由于在控制台执行，就没有使用绑定参数的模式

        // 转换json
        if (count($res) > 0) {
            // 找出json格式的字段
            $jsonFields = self::findJsonFields($res[0]);
            // 将json格式的字段转换为PHP数组
            self::convertJson($res, $jsonFields);
        }

        // 导出保存
        $jsontxt = json_encode([
            'success' => 1,
            'data'    => $res
        ], JSON_UNESCAPED_UNICODE);
        fwrite($file, $jsontxt);
        fclose($file);

        $this->output->writeln("数据表: $table 导出完毕.");
        return true;
    }

    /**
     * 获取一行数据中所有是json数据的字段名
     * @param array $line 样例数据数组
     * @return array 字段名数组
     */
    private static function findJsonFields($line)
    {
        $fields = [];
        foreach ($line as $field => $value) {
            if (is_int($value)) {
                continue;
            }
            json_decode($value);
            if (json_last_error() == JSON_ERROR_NONE) {
                $fields[] = $field;
            }
        }
        return $fields;
    }

    /**
     * 将结果集中所有的json字段转换成数组
     * @param array $results 结果集
     * @param array $jsonFields json字段名数组
     */
    private static function convertJson(&$results, $jsonFields)
    {
        // 若含有json字段，则将其全部转换，没有则跳过
        if (count($jsonFields) <= 0) {
            return;
        }
        foreach ($results as $index => $line) {
            foreach ($jsonFields as $field) {
                $results[$index][$field] = json_decode($results[$index][$field], true);
            }
        }
    }
}