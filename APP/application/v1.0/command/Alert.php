<?php

namespace app\v3\command;

use app\v3\model\ModelAdminResource;
use think\Log;
use think\Exception;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\v3\service\ServiceMessage;
use app\v3\common\NormalException;
use app\v3\model\ModelHistory;
use app\v3\model\ModelApply;
use app\v3\model\ModelUser;

/**
 * Alert 用于向审查者（管理员）发送消息，提醒当前审查状况
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 */
class Alert extends Command
{
    protected function configure()
    {
        $this->setName('apply3:alert')
            ->setDescription('向审查者发送提醒短信')
            ->setHelp(sprintf('%s向审查者发送提醒短信(立即生效,每次执行向所有未完成工作的审查者发送)%s', PHP_EOL, PHP_EOL));
    }

    protected function execute(Input $input, Output $output)
    {
        $users = ModelUser::all();
        foreach ($users as $u) {
            if ($u->auth == ModelUser::AUTH_ADMIN) {
                $uncheckedCount = 0;
                $ts = ModelAdminResource::where(['admin_id' => $u->userid])->select();
                foreach ($ts as $t) {
                    $uncheckedCount += ModelApply::where(['resource_id' => $t->resource_id, 'status' => ModelApply::STATUS_CHECKING])->count();
                }
                $content = "【挑战网】尊敬的{$u->name}老师，您好！您还有{$uncheckedCount}份申请没有处理，请及时查看！";
                $output->writeln("正在发往{$u->phone}:{$content}");
                try {
                    ServiceMessage::sendMessage($content, $u->phone);
                } catch (NormalException $e) {
                    $output->writeln("{$u->name}老师的短信发送失败: " . $e->getMessage());
                }
                try {
                    ModelHistory::record(ModelHistory::NONE_APPLYID, 'Alert Message', ['content' => $content, 'phone' => $u->phone]);
                } catch (\PDOException $e) {
                    $output->writeln('警告,本操作未能记录到数据库中:' . $e->getMessage());
                }
                // 记录日志
                Log::write('Command apply:alert 发送提醒短信至' . $u->phone, 'notice');
            }
        }
        $output->writeln("发送完毕.");
    }
}