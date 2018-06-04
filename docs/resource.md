# 资源模块

权限：该模块所有API仅数据库管理员（超级管理员）可以访问

## 接口列表

[增加资源](#post_resource) `POST {base_url}/api/v3/superadmin/resource`

[删除资源](#delete_resource) `DELETE {base_url}/api/v3/superadmin/resource/:resourceid`

[修改资源信息](#put_resource) `PUT {base_url}/api/v3/superadmin/resource`

[获取资源信息](#get_resource) `GET {base_url}/api/v3/superadmin/resource`

## 接口详情

<a name="post_resource"></a>

### 增加资源 `POST {base_url}/api/v3/superadmin/resource`

+ Request

        {
            "resource_name": "资源名称",
            "place_id": "所属场地的编号",
            "start": "开始时间",
            "end": "结束时间",
            "device": "资源类型（0教室，1设备）",
            "capacity": "容量",
            "other": "其他信息"
        }

+ Response 200

        {
            "success": 1
        }

<a name="delete_resource"></a>

### 删除资源 `DELETE {base_url}/api/v3/superadmin/resource/:resourceid`

+ Request

        // 无参数，将resourceid放于url中

+ Response 200

        {
            "success": 1
        }

<a name="put_resource"></a>

### 修改资源信息 `PUT {base_url}/api/v3/superadmin/resource`

+ Request

        {
            "id": "资源编号",
            "resource_name": "资源名称",
            "place_id": "所属场地的编号",
            "start": "开始时间",
            "end": "结束时间",
            "device": "资源类型（0教室，1设备）",
            "capacity": "容量",
            "other": "其他信息"
        }

_以上字段中除id外均可缺省（即修改部分字段的信息），但最好不要全部缺省_

+ Response 200

        {
            "success": 1
        }

<a name="get_resource"></a>

### 获取资源信息 `GET {base_url}/api/v3/superadmin/resource`

+ Request

        {
            "id": "资源编号"
        }

+ Response 200

        {
            "success": 1,
            "data":[
                {
                    "id": "资源编号",
                    "resource_name": "资源名称",
                    "place_id": "所属场地编号",
                    "start": "开始时间",
                    "end": "结束时间",
                    "device": "资源类型（0教室，1设备）",
                    "capacity": "容量",
                    "other": "其他信息"
                }
            ]
        }
