# 场地模块

权限：该模块所有API仅数据库管理员（超级管理员）可以访问

## 接口列表

[增加场地](#post_place) `POST {base_url}/api/v3/superadmin/place`

[删除场地](#delete_place) `DELETE {base_url}/api/v3/superadmin/place/:placeid`

[修改场地信息](#put_place) `PUT {base_url}/api/v3/superadmin/place`

[获取场地信息](#get_place) `GET {base_url}/api/v3/superadmin/place`

## 接口详情

<a name="post_place"></a>

### 增加场地 `POST {base_url}/api/v3/superadmin/place`

+ Request

        {
            "place_name": "场地名称",
            "location": "场地位置坐标（即一个json）",
            "committee": "场地类型（0非团委，1团委）"
        }

+ Response 200

        {
            "success": 1
        }

<a name="delete_place"></a>

### 删除场地 `DELETE {base_url}/api/v3/superadmin/place/:placeid`

+ Request

        // 无参数，将placeid放在url中即可

+ Response 200

        {
            "success": 1
        }

<a name="put_place"></a>

### 修改场地信息 `PUT {base_url}/api/v3/superadmin/place`

+ Request

        {
            "id": "场地编号",
            "place_name": "场地名称",
            "location": "场地位置坐标（json）",
            "committee": "场地类型（0非团委，1团委）"
        }

_以上字段中除id外均可缺省（即修改部分字段的信息），但最好不要全部缺省_

+ Response 200

        {
            "success": 1
        }

<a name="get_place"></a>

### 获取场地信息 `GET {base_url}/api/v3/superadmin/place`

+ Request

        {
            "id": "场地编号"
        }

+ Response 200

        {
            "success": 1,
            "data":[
                {
                    "id": "场地编号",
                    "place_name": "场地名称",
                    "location": "场地位置坐标（json）",
                    "committee": "场地类型（0非团委，1团委）"
                },
                ...
            ]
        }
