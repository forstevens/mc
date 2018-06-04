# 数据库结构

+ Database

        database
        ├─ system_info 系统信息
        │  ├─ key 键
        │  └─ value 值
        ├─ user 用户表(主要存管理员信息)
        │  ├─ userid 用户的NetID
        │  └─ auth 用户的权限
        ├─ apply 申请表
        │  ├─ id 申请编号
        │  ├─ resource_id 资源编号
        │  ├─ start_time 开始时间
        │  ├─ end_time 结束时间
        │  ├─ apply_time 申请时间
        │  ├─ activity 活动名称
        │  ├─ organization 组织名称
        │  ├─ applicant_userid 申请者NetID
        │  ├─ applicant_name 申请者姓名
        │  ├─ apply_scale 申请人数
        │  ├─ phone 申请者手机号
        │  ├─ status 当前状态
        │  └─ reason 拒绝原因
        ├─ history 历史记录表
        │  ├─ id 记录编号
        │  ├─ applyid 申请编号
        │  ├─ detail 行为详情
        │  ├─ operator_userid 操作者NetID
        │  ├─ operator_auth 操作者权限
        │  └─ operation_time 操作时间
        ├─ place 场所表
        │  ├─ id 场地编号
        │  ├─ place_name 场地名称
        │  ├─ location 场地坐标(json)
        │  └─ committee 场地类型（0非团委，1团委）
        ├─ resource 资源表
        │  ├─ id 资源编号
        │  ├─ resource_name 资源名称
        │  ├─ place_id 场地编号
        │  ├─ device 资源类型（0教室，1设备）
        │  ├─ capacity 容纳人数
        │  ├─ admin_id 负责人的NetID
        │  ├─ phone 负责人手机
        │  └─ other 其他信息
        ├─ admin_resource 关联表
        │  ├─ admin_id 负责人的NetID
        │  └─ resource_id 资源编号
        └─ notification 通知表
           ├─ id 通知编号
           ├─ title 通知标题
           ├─ content 通知内容
           ├─ tend 通知类型（1成功，2警告，3消息，4错误）
           └─ overtime 通知过期时间