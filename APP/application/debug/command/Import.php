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
 * Import 从json文件导入到数据库
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 */
class Import extends Command
{
    // 数据库连接
    private $connection;
    // 数据表(数组)
    private $tables;
    // 当前需要导入的数据表
    private $currentTable;
    // 当前需要导入的数据;
    private $currentData;

    protected function configure()
    {
        $this->setName('apply3:import')
            ->setDescription('将json文件导入到数据库(第一个参数为数据表名,第二个参数为文件名)')
            ->addArgument('table', Argument::REQUIRED, '数据表名')
            ->addArgument('file', Argument::OPTIONAL, '导入文件名')
            ->setHelp(sprintf('%s将json数组导入到数据库%s', PHP_EOL, PHP_EOL));
    }

    protected function execute(Input $input, Output $output)
    {
        // 获取参数
        $table = $input->getArgument('table');
        $file = $input->getArgument('file');

        // 检查数据表是否存在
        $this->connection = Db::connect();
        $this->tables = $this->connection->getTables();

        $inTables = [];
        if ($table == '*' || $table == 'all') {
            // 搜索import下所有json文件
            $pathBase = ROOT_PATH . 'database' . DS . 'import' . DS;
            $files = glob($pathBase . '*.json');
            // 依次导入
            foreach ($files as $path) {
                // 正则出文件名(表名)
                $matches = [];
                $flag = preg_match("/(\w+)\.json$/", $path, $matches);
                if ($flag != 1) {
                    $output->writeln('该文件的文件名格式异常: ' . $path);
                }
                // 导入
                if ($this->importTable($matches[1], $path)) {
                    $inTables[] = $matches[1];
                }
            }
        } else {
            if (!in_array($table, $this->tables)) {
                $output->writeln('数据表不存在,操作已终止.');
                return;
            }

            // 若未指定文件，则指向默认文件
            if ($file === null) {
                $file = ROOT_PATH . 'database' . DS . 'import' . DS . $table . '.json';
            }

            // 导出数据表
            if ($this->importTable($table, $file)) {
                $inTables[] = $table;
            }
        }

        // 记录历史
        if (count($inTables) > 0) {
            try {
                ModelHistory::record(ModelHistory::NONE_APPLYID, 'Import Database', ['tables' => $inTables]);
            } catch (\PDOException $e) {
                $output->writeln('警告,本操作未能记录到数据库中:' . $e->getMessage());
            }
            // 记录日志
            Log::write('Command apply:import 导入数据库','notice');
        }
        $output->writeln("导入完毕.");
    }

    /**
     * 导出数据表
     * @param string $table 数据表名称
     * @param string $path 导出文件路径
     * @return bool 是否成功导出
     */
    public function importTable($table, $path)
    {
        // 检查数据表存在
        if (!in_array($table, $this->tables)) {
            $this->output->writeln('数据表' . $table . '不存在,操作终止!');
            return false;
        }

        // 检查文件存在
        if (!file_exists($path)) {
            $this->output->writeln('文件' . $path . '不存在,操作终止!');
            return false;
        }

        // 防止打开出错
        try {
            $file = fopen($path, 'r');
        } catch (Exception $e) {
            $this->output->writeln('打开文件出错: ' . $e->getMessage());
            $this->output->writeln('操作已终止.');
            return false;
        }

        $jsontxt = fread($file, filesize($path));

        $this->currentTable = $table;
        $this->currentData = json_decode($jsontxt, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            $this->output->writeln("导入数据$table.json时出现json格式错误,操作终止!");
            fclose($file);
            return false;
        }

        try {
            $this->connection->transaction(function() {
                Db::name($this->currentTable)->insertAll($this->currentData);
            });
        } catch (Exception $e) {
            $this->output->writeln("导入数据$table.json时出现数据库错误,操作终止: " . $e->getMessage());
            fclose($file);
            return false;
        }

        fclose($file);
        $this->output->writeln("数据表: $table 导入完毕.");
        return true;
    }
}