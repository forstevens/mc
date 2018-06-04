# 历史模块

权限：该模块审查者（管理员）和数据库管理员（超级管理员）分别可以访问各自的接口

## 接口列表

[获取历史记录](#get_admin_history) `GET {base_url}/api/v3/admin/history`

[查询历史记录](#get_superadmin_history) `GET {base_url}/api/v3/superadmin/history`

## 说明

1. 获取历史记录只能获取当前审查者（管理员）的历史记录，使用limit和page参数分页查询，用于老师查一下自己都干了些啥

2. 获取历史记录不会显示操作者和权限

3. 查询历史记录是bug一样存在的随意查询，仅能由数据库管理员（超级管理员）查询

## 接口详情

<a name="get_admin_history"></a>

### 获取历史记录 `GET {base_url}/api/v3/admin/history`

+ Request

        {
            "limit":"每页显示的最多数目",
            "page":"第几页",
            "order":"desc"
        }

_order参数可以为asc或者desc，控制返回值的排序方式，asc为时间早的放前面，desc为时间晚的放前面，其他值以及默认按照desc执行_

+ Response 200

        {
            "success": 1,
            "data": {
                "total":123, // 记录总数
                "data": [
                    {
                        "id":"记录编号1",
                        "apply_id":"申请编号1",
                        "detail":"操作详情1",
                        "operation_time":"操作时间1"
                    },
                    {
                        "id":"记录编号2",
                        "apply_id":"申请编号2",
                        "detail":"操作详情2",
                        "operation_time":"操作时间2"
                    },
                    ...
                ]
            }
        }

<a name="get_superadmin_history"></a>

### 查询历史记录 `GET {base_url}/api/v3/superadmin/history`

+ Request

        {
            "apply_id":"申请编号",
            "operator_userid":"操作者NetID",
            "operator_auth":"操作者权限",
            "start_time":"开始时间",
            "end_time":"结束时间",
            "limit":"每页显示的最多数目",
            "page":"第几页",
            "order":"asc"
        }

_order参数可以为asc或者desc，控制返回值的排序方式，asc为时间早的放前面，desc为时间晚的放前面，其他值以及默认按照asc执行_

_以上字段除limit和page必须同时存在外，任意字段可以缺省_
_关于时间：服务器将返回符合条件“开始时间>=start且结束时间<=end”的数据_

+ Response 200

        {
            "success": 1,
            "data": {
                "total":123, // 记录总数
                "data":[
                    {
                        "id":"记录编号1",
                        "apply_id":"申请编号1",
                        "detail":"操作详情1",
                        "operator_userid":"操作者的NetID1",
                        "operator_auth":"操作者权限1",
                        "operation_time":"操作时间1"
                    },
                    {
                        "id":"记录编号2",
                        "apply_id":"申请编号2",
                        "detail":"操作详情2",
                        "operator_userid":"操作者的NetID2",
                        "operator_auth":"操作者权限2",
                        "operation_time":"操作时间2"
                    },
                    ...
                ]
            }
        }
