<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;
use think\migration\db\Column;

class Init extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        // NetID最大长度
        $useridLength = 50;
        // 电话号码最大长度
        $phoneLength = 20;
        // 人名的最大长度
        $nameLength = 20;

        // SystemInfo表
        $systemInfo = $this->table('system_info', [
            'engine' => 'InnoDB',
            'id' => false,
            'primary_key' => ['key'],
        ]);
        $systemInfo->addColumn('key', 'string', ['limit' => 30, 'comment' => 'info key'])
            ->addColumn('value', 'string', ['limit' => 150, 'comment' => 'info value'])
            ->create();

        // User表
        $user = $this->table('user', [
            'engine' => 'InnoDB',
            'id' => false,
            'primary_key' => ['userid'],
        ]);
        $user->addColumn('userid', 'string', ['limit' => $useridLength, 'comment' => '用户NetID'])
            ->addColumn('user_name', 'string', ['limit' => $phoneLength, 'comment' => '用户姓名'])
            ->addColumn('phone', 'string', ['limit' => $nameLength, 'comment' => '用户手机号'])
            ->addColumn('auth', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'signed' => false, 'comment' => '用户权限，1申请者，2审查者，3数据库管理员'])
            ->addColumn('sms', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'signed' => false, 'comment' => '是否使用短息服务'])
            ->create();

        // Apply表
        $apply = $this->table('apply', [
            'engine' => 'InnoDB',
            'signed' => false,
        ]);
        $apply->addColumn('resource_id', 'integer', ['limit' => 32767, 'signed' => false, 'comment' => '资源，如会议室、投影仪'])
            ->addColumn('applicant_userid', 'string', ['limit' => $useridLength, 'comment' => '申请者NetID'])
            ->addColumn('applicant_name', 'text', ['comment' => '申请者姓名'])
            ->addColumn('activity', 'text', ['comment' => '活动名称'])
            ->addColumn('organization', 'text', ['comment' => '申请组织'])
            ->addColumn('apply_scale', 'text', ['comment' => '申请人数'])
            ->addColumn('start_time', 'datetime', ['comment' => '申请开始时间'])
            ->addColumn('end_time', 'datetime', ['comment' => '申请结束时间'])
            ->addColumn('apply_time', 'datetime', ['comment' => '申请时间'])
            ->addColumn('phone', 'string', ['limit' => $phoneLength,'comment' => '申请者手机号'])
            ->addColumn('status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'signed' => false, 'comment' => '申请状态，0已撤销，1审核中，2通过，3失败'])
            ->addColumn('reason', 'text', ['comment' => '拒绝原因'])
            ->addIndex(['resource_id'], ['name' => 'resource'])
            ->addIndex(['applicant_userid'], ['name' => 'userid'])
            ->addIndex(['start_time', 'end_time'], ['name' => 'target_time'])
            ->addIndex(['apply_time'], ['name' => 'apply_time'])
            ->addIndex(['phone'], ['name' => 'phone'])
            ->addIndex(['status'], ['name' => 'status'])
            ->create();

        // History表
        $history = $this->table('history', [
            'engine' => 'MyISAM',
            'signed' => false,
        ]);
        $history->addColumn('apply_id', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'signed' => false, 'comment' => '被操作申请的编号'])
            ->addColumn('detail', 'text', ['comment' => '操作详情'])
            ->addColumn('operator_userid', 'string', ['limit' => $useridLength, 'comment' => '操作者NetID'])
            ->addColumn('operator_auth', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'signed' => false, 'comment' => '操作者权限，1申请者，2审查者，3数据库管理员'])
            ->addColumn('operation_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '申请开始时间'])
            ->addIndex(['apply_id'], ['name' => 'apply_id'])
            ->addIndex(['operator_userid'], ['name' => 'userid'])
            ->addIndex(['operator_auth'], ['name' => 'auth'])
            ->addIndex(['operation_time'], ['name' => 'optime'])
            ->create();

        // 注意：
        // 以下两个表数据表不会经常改变，故将其改为数据库与json文件相结合的形式
        // 减轻数据库压力的同时保持可修改性
        // 查询筛选功能移至前端执行，也可以减缓服务器压力

        // Place表
        $place = $this->table('place', [
            'engine' => 'InnoDB',
            'signed' => false,
        ]);
        $place->addColumn('place_name', 'text', ['comment' => '场所名'])
            ->addColumn('location', 'text', ['comment' => '坐标(json)'])
            ->addColumn('committee', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'signed' => false, 'comment' => '场地类型，0非团委，1团委'])
            ->create();

        // Resource表
        $resource = $this->table('resource', [
            'engine' => 'InnoDB',
            'signed' => false,
        ]);
        $resource->addColumn('resource_name', 'text', ['comment' => '资源名'])
            ->addColumn('place_id', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'signed' => false, 'comment' => '场所编号'])
            ->addColumn('start', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'signed' => false, 'comment' => '开始时间'])
            ->addColumn('end', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'signed' => false, 'comment' => '结束时间'])
            ->addColumn('device', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'signed' => false, 'comment' => '资源类型，0教室，1设备'])
            ->addColumn('capacity', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'signed' => false, 'comment' => '容纳人数'])
            ->addColumn('other', 'text', ['comment' => '其他(暂留)'])
            ->create();

        // AdminResource表
        $admin_resource = $this->table('admin_resource', [
            'engine' => 'InnoDB',
            'id' => false,
        ]);
        $admin_resource->addColumn('admin_id', 'string', ['limit' => $useridLength, 'comment' => '负责人的NetID'])
            ->addColumn('resource_id', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'signed' => false, 'comment' => '资源编号'])
            ->addIndex(['admin_id', 'resource_id'], ['name' => 'link'])
            ->create();
        
        // Notification表
        $notification = $this->table('notification', [
            'engine' => 'InnoDB',
            'signed' => false
        ]);
        $notification->addColumn('title', 'text', ['comment' => '通知标题'])
            ->addColumn('content', 'text', ['comment' => '通知内容'])
            ->addColumn('tend', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'signed' => false, 'comment' => '通知类型, 1成功，2警告，3消息，4错误'])
            ->addColumn('overtime', 'datetime', ['comment' => '通知过期时间'])
            ->create();
    }
}
