# 通知模块

权限：该模块所有API仅数据库管理员（超级管理员）可以访问

## 接口列表

[增加通知](#post_notification) `POST {base_url}/api/v3/superadmin/notification`

[删除通知](#delete_notification) `DELETE {base_url}/api/v3/superadmin/notification/:notificationid`

[修改通知信息](#put_notification) `PUT {base_url}/api/v3/superadmin/notification`

[获取通知信息](#get_notification) `GET {base_url}/api/v3/superadmin/notification`

## 接口详情

<a name="post_notification"></a>

### 增加通知 `POST {base_url}/api/v3/superadmin/notification`

+ Request

        {
            "title": "通知标题",
            "content": "通知内容",
            "tend": "通知类型",
            "overtime": "通知过期时间"
        }

+ Response 200

        {
            "success": 1
        }

<a name="delete_notification"></a>

### 删除通知 `DELETE {base_url}/api/v3/superadmin/notification/:notificationid`

+ Request

        // 无参数，将notificationid放于url中

+ Response 200

        {
            "success": 1
        }

<a name="put_notification"></a>

### 修改通知信息 `PUT {base_url}/api/v3/superadmin/notification`

+ Request

        {
            "id": "通知编号",
            "title": "通知标题",
            "content": "通知内容",
            "tend": "通知类型",
            "overtime": "通知过期时间"
        }

_以上字段中除id外均可缺省（即修改部分字段的信息），但最好不要全部缺省_

+ Response 200

        {
            "success": 1
        }

<a name="get_notification"></a>

### 获取通知信息 `GET {base_url}/api/v3/superadmin/notification`

+ Request

        {
            "id": "通知编号",
            "mark": "标记，1未过期的通知，0全部通知"
        }

+ Response 200

        {
            "success": 1,
            "data":[
                {
                    "id": "通知编号",
                    "title": "通知标题",
                    "content": "通知内容",
                    "tend": "通知类型",
                    "overtime": "通知过期时间"
                }
            ]
        }
