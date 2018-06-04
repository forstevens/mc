# 公共查询模块

权限：该模块所有API任何人可以访问

## 接口列表

[公共申请查询](#get_board) `GET {base_url}/api/v3/board`

[获取场地信息](#get_places) `GET {base_url}/api/v3/places`

[获取资源信息](#get_resources) `GET {base_url}/api/v3/resources`

## 说明

1. 为了保护隐私，公共申请查询(board)不会返回拒绝原因、NetID、手机号

2. 为了节约流量，个人申请查询(apply)不会返回NetID和姓名、公共申请查询(board)不会返回id和resource_id

3. 为了保护隐私和节约流量，两种查询都不会返回申请的时间，仅管理员可见

4. 为了可扩展性，申请时的手机号可以和CAS中的手机号不同，因此个人申请查询时也会返回手机号

5. 为了保证安全，申请时的NetID和姓名将强制和登录人一致，不得更改，后端将忽略这种参数

6. 所有时间均为时间戳，单位为秒，可以使用 var timestamp = new Date().getTime()/1000; 来获取当前时间戳

7. 目前返回响应的时间为时间日期字符串，这个可以日后再调

8. 以上说明是复制来的，有多余说明

## 接口详情

<a name="get_board"></a>

### 公共申请查询 `GET {base_url}/api/v3/board`

+ Request

        {
            "resource_id":"要查询资源的id",
            "start_time":"开始时间"，
            "end_time":"结束"
        }

+ Response 200

        {
            "success": 1,
            "data":[
                {
                    "start_time":"开始时间1",
                    "end_time":"结束时间1",
                    "activity":"活动名称1",
                    "organization":"申请组织1", // 如挑战网
                    "applicant_name":"申请人姓名1", // 如是聚聚
                    "apply_scale":"申请人数",
                    "status":"审核状态1"
                },
                {
                    "start_time":"开始时间2",
                    "end_time":"结束时间2",
                    "activity":"活动名称2",
                    "organization":"申请组织2", // 如挑战网
                    "applicant_name":"申请人姓名2", // 如是聚聚
                    "apply_scale":"申请人数",
                    "status":"审核状态2"
                },
                ...
            ]
        }

<a name="get_places"></a>

### 获取场地信息 `GET {base_url}/api/v3/places`

+ Request

        // 无需参数

+ Response 200

        {
            "success": 1,
            "data": [
                {
                    "id": 1, 
                    "place_name": "校团委", 
                    "location": {
                        "x": "x_location", 
                        "y": "y_location"
                    }, 
                    "committee": 1
                }, 
                {
                    "id": 2, 
                    "place_name": "启德书院", 
                    "location": {
                        "x": "x_location", 
                        "y": "y_location"
                    }, 
                    "committee": 0
                }, 
                ...
            ]
        }

+ Response 500 或者其他

<a name="get_resources"></a>

#### 获取资源信息 `GET {base_url}/api/v3/resources`

+ Request

        // 无需参数

+ Response 200

        {
            "success": 1,
            "data": [
                {
                    "id": 1, 
                    "resource_name": "会议室", 
                    "place_id": 1, 
                    "start": 8, 
                    "end": 23, 
                    "device": 0, 
                    "capacity": 1000, 
                    "other": "xxx(显示借用规定)"
                }, 
                {
                    "id": 2, 
                    "resource_name": "青年之家", 
                    "place_id": 1, 
                    "start": 8, 
                    "end": 23, 
                    "device": 0, 
                    "capacity": 1000, 
                    "other": "xxx(显示借用规定)"
                }, 
                ...
            ]
        }

+ Response 500 或者其他
