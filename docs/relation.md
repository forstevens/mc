# 关联表模块

权限：该模块所有API仅数据库管理员（超级管理员）可以访问

## 接口列表

[增加关联](#post_relation) `POST {base_url}/api/v3/superadmin/relation`

[删除关联](#delete_relation) `DELETE {base_url}/api/v3/superadmin/relation/:adminid/:resourceid`

[获取关联](#get_relation) `GET {base_url}/api/v3/superadmin/relation`

## 接口详情

<a name="post_relation"></a>

### 增加关联 `POST {base_url}/api/v3/superadmin/relation`

+ Request

        {
            "admin_id": "负责人的NetID"
            "resource_id": "负责的资源编号"
        }

+ Response 200

        {
            "success": 1
        }

<a name="delete_relation"></a>

### 删除关联 `DELETE {base_url}/api/v3/superadmin/relation/:adminid/:resourceid`

+ Request

        // 无参数，将adminid和resourceid放于url中

+ Response 200

        {
            "success": 1
        }

<a name="get_relation"></a>

### 获取关联 `GET {base_url}/api/v3/superadmin/relation`

+ Request

        {
            "admin_id": "负责人的NetID"
        }
        
        或者
        
        {
            "resource_id": "资源编号"
        }

+ Response 200

        {
            "success": 1,
            "data":[
                {
                    "admin_id": "负责人的NetID",
                    "resource_id": "资源编号"
                },
                {
                    "admin_id": "负责人的NetID",
                    "resource_id": "资源编号"
                },
                ...
            ]
        }
