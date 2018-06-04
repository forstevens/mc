<?php

namespace app\v3\command;

use think\Exception;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\v3\common\NormalException;
use app\v3\model\ModelApply;
use app\v3\model\ModelHistory;

/**
 * Disable 用于自动将过期的申请设置为不通过
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 */
class Disable extends Command
{
    protected function configure()
    {
        $this->setName('apply3:disable')
            ->setDescription('自动将过期的申请设置为不通过')
            ->setHelp(sprintf('%s自动将过期的申请设置为不通过(立即生效)%s', PHP_EOL, PHP_EOL));
    }

    protected function execute(Input $input, Output $output)
    {
        $query = new ModelApply();
        $query->where('end_time', '< time', date("Y-m-d H:i:s"));
        $query->where('status', ModelApply::STATUS_CHECKING);
        $applies = $query->select();
        $i = 0;
        foreach ($applies as $a) {
            $a->status = ModelApply::STATUS_FAILED;
            $a->reason = '时间已失效';
            $a->save();
            $i++;
        }
        $output->writeln("共计操作{$i}条数据.");
        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Disable Applies', ['number' => $i]);
        $output->writeln("操作完毕.");
    }
}