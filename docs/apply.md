# 申请模块

权限：该模块所有API任何人可以访问

## 接口列表

[个人申请查询](#get_apply) `GET {base_url}/api/v3/apply`

[资源申请](#post_apply) `POST {base_url}/api/v3/apply`

[撤销申请](#delete_apply) `DELETE {base_url}/api/v3/apply/:applyid`

[修改申请](#put_apply) `PUT {base_url}/api/v3/apply`

## 说明

1. 为了保护隐私，公共申请查询(board)不会返回拒绝原因、NetID、手机号

2. 为了节约流量，个人申请查询(apply)不会返回NetID和姓名、公共申请查询(board)不会返回id和resource_id

3. 为了保护隐私和节约流量，两种查询都不会返回申请的时间，仅管理员可见

4. 为了可扩展性，申请时的手机号可以和CAS中的手机号不同，因此个人申请查询时也会返回手机号

5. 为了保证安全，申请时的NetID和姓名将强制和登录人一致，不得更改，后端将忽略这种参数

6. 所有时间均为时间戳，单位为秒，可以使用 var timestamp = new Date().getTime()/1000; 来获取当前时间戳

7. 目前返回响应的时间为时间日期字符串，这个可以日后再调

8. 以上有多余说明

## 接口详情

<a name="get_apply"></a>

### 个人申请查询 `GET {base_url}/api/v3/apply`

+ Request

        {
        // 无须参数，后端返回该用户的申请记录即可
        }

+ Response 200

        {
            "success": 1,
            "data":[
                {
                    "id":"申请编号1",
                    "resource_id":"资源id1",
                    "start_time":"开始时间1",
                    "end_time":"结束时间1",
                    "activity":"活动名称1",
                    "organization":"申请组织1", // 如挑战网
                    "apply_scale":"申请人数",
                    "phone":"手机号1",
                    "status":"审核状态1",
                    "reason":"拒绝原因1"
                },
                {
                    "id":"申请编号2",
                    "resource_id":"资源id2",
                    "start_time":"开始时间2",
                    "end_time":"结束时间2",
                    "activity":"活动名称2",
                    "organization":"申请组织2", // 如挑战网
                    "apply_scale":"申请人数",
                    "phone":"手机号2",
                    "status":"审核状态2",
                    "reason":"拒绝原因2"
                },
                ...
            ]
        }

<a name="post_apply"></a>

### 资源申请 `POST {base_url}/api/v3/apply`

+ Request

        {
            "resource_id": "要申请的资源id",
            "activity":"活动名称",
            "organization": "申请组织",
            "apply_scale":"申请人数",
            "phone": "手机号",
            "start_time":"开始时间",
            "end_time":"结束时间",
        }

+ Response 200

        {
            "success": 1,
            "data":
            {
                "id": xxx
            }
        }

<a name="delete_apply"></a>

### 撤销申请 `DELETE {base_url}/api/v3/apply/:applyid`

+ Request

        // 无参数，请将applyid放在url中，以保证兼容性
        // 例如 DELETE {base_url}/api/v3/apply/123

+ Response 200

        {
            "success": 1
        }

<a name="put_apply"></a>

### 修改申请 `PUT {base_url}/api/v3/apply`

_修改后该申请的状态将变为“审核中”即可_

+ Request

        // 考虑到实际情况，场地和申请者不予修改
        {
            "id":"申请编号",
            "activity":"活动名称",
            "organization": "申请组织", // 如挑战网
            "apply_scale":"申请人数",
            "phone": "手机号",
            "start_time":"开始时间",
            "end_time":"结束时间",
        }

+ Response 200

        {
            "success": 1
        }
