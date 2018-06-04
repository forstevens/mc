<?php

namespace app\v3\model;

use think\Model;
use app\v3\common\NormalException;

/**
 * ModelNotification Notification模型
 * @author 丁浩然 <dinghaoran@tiaozhan.com>
 */
class ModelNotification extends Model
{
    // 数据表名
    protected $table = 'notification';
    // 主键
    protected $pk = 'id';
    // 保存时自动补完
    protected $auto = [];
    // 新增时自动补完
    protected $insert = [];
    // 更新时自动补完
    protected $update = [];
    // 类型声明
    protected $type = [
        'id'       => 'integer',
        'title'    => 'string',
        'content'  => 'string',
        'tend'     => 'integer',
        'overtime' => 'datetime'
    ];

    /**
     * 增加通知
     * @param $data array 数据
     */
    public static function addNotification($data)
    {
        $notification = new self();
        $notification->title = $data['title'];
        $notification->content = $data['content'];
        $notification->tend = $data['tend'];
        $notification->overtime = $data['overtime'];
        $notification->save();

        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Add Notification', $data);
    }

    /**
     * 删除通知
     * @param $notificationid int 通知编号
     */
    public static function removeNotification($notificationid)
    {
        $notification = self::get($notificationid);
        if (!$notification) {
            throw new NormalException('通知不存在');
        }
        $notification->delete();

        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Remove Notification', ['id' => $notification->id]);
    }

    /**
     * 修改通知信息
     * @param $data array 数据
     */
    public static function updateNotification($data)
    {
        $id = $data['id'];
        $notification = self::get($id);
        if (!$notification) {
            throw new NormalException('通知不存在');
        }
        if (isset($data['title'])) {
            $notification->title = $data['title'];
        }
        if (isset($data['content'])) {
            $notification->content = $data['content'];
        }
        if (isset($data['tend'])) {
            $notification->tend = $data['tend'];
        }
        if (isset($data['overtime'])) {
            $notification->overtime = $data['overtime'];
        }
        $notification->save();

        ModelHistory::record(ModelHistory::NONE_APPLYID, 'Update Notification', $data);
    }

    /**
     * 获取通知信息
     * @param $data array 数据
     */
    public static function getNotificationInfo($data)
    {
        if (!$data['id']) {
            // 获取所有的通知
            if (!$data['mark']) {
                $notification = self::all();
            }
            else {
                // 获取未过期的通知
                $query = new self();
                $notification = $query->where('overtime', '>', date("Y-m-d H:i:s"))->select();
            }
        } else {
            $query = new self();
            $notification = $query->where('id', $data['id'])->select();
            if (!$notification) {
                throw new NormalException('通知不存在');
            }
        }
        foreach ($notification as $index => $n) {
            $notification[$index] = $n->toArray();
        }
        return $notification;
    }
}