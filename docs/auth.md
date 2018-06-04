# 权限模块

权限：该模块所有API仅数据库管理员（超级管理员）可以访问

## 接口列表

[添加用户](#post_user) `POST {base_url}/api/v3/superadmin/user`

[删除用户](#delete_user) `DELETE {base_url}/api/v3/superadmin/user/:userid`

[修改用户权限](#put_user) `PUT {base_url}/api/v3/superadmin/user`

[获取用户权限](#get_user) `GET {base_url}/api/v3/superadmin/user`

## 接口详情

<a name="post_user"></a>

### 添加用户 `POST {base_url}/api/v3/superadmin/user`

+ Request

        {
            "userid":"用户NetID",
            "name": "姓名",
            "phone": "手机号",
            "auth":"权限"
        }

+ Response 200

        {
            "success": 1
        }

<a name="delete_user"></a>

### 删除用户 `DELETE {base_url}/api/v3/superadmin/user/:userid`

+ Request

        // 无参数，请将userid放在url中，以保证兼容性

+ Response 200

        {
            "success": 1
        }

<a name="put_user"></a>

### 修改用户权限 `PUT {base_url}/api/v3/superadmin/user`

+ Request

        {
            "userid":"用户NetID",
            "name": "姓名",
            "phone": "手机号",
            "auth":"权限"
        }

+ Response 200

        {
            "success": 1
        }

<a name="get_user"></a>

### 获取用户权限 `GET {base_url}/api/v3/superadmin/user`

+ Request

        {
            "userid":"用户NetID",
            "user_name":"用户姓名",
            "phone":"用户手机号",
            "auth":"用户权限",
            "limit":"每页显示的最多数目",
            "page":"第几页"
        }

_以上字段除limit和page必须存在外，任意字段可以缺省_

+ Response 200

        {
            "success": 1,
            "data": {
                "total":123, // 记录总数
                "data":[
                        {
                                "userid":"用户NetID",
                                "user_name":"用户姓名",
                                "phone":"用户手机号",
                                "auth":"用户权限"
                        },
                        {
                                "userid":"用户NetID",
                                "user_name":"用户姓名",
                                "phone":"用户手机号",
                                "auth":"用户权限"
                        }
                ]
            }
        }