# apply3.0  

> Author:

shiwenbo@tiaozhan.com

dongjiangbin@tiaozhan.com

dinghaoran@tiaozhan.com

配置为Docker

## 综述
这是对apply2.0版本的重构。较前一版本主要的优化有：  
1.采用前后端分离  
2.前端手机端适配  
3.其他优化  
开发时，请注意以下内容：  
1.前端请注意手机端适配问题  
2.后端请提供restful风格的接口  
3.请仔细研究apply.tiaozhan.com中的种种功能  
3.后端请学习使用tzapi-client-php（挑战网通用基础API服务）来进行用户登录操作，注意接口中可能抛出的异常  
4.后端请学习使用短信服务接口  
5.当前文档并未完成，请前后端配合完成  

## 关于运维

请查看op.md

## 响应

#### 当请求成功时，返回以下内容

+ Response 200

        {
            "success":1,
            "data":{...}
        }

#### 当请求失败时，返回以下内容

+ Response 200

        {
            "success":0,
            "err_msg":"报错信息"
        }

## 文档目录

[初始化及服务器配置](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/config.md) _重要，必看_

[用户模块](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/user.md)

[公共查询模块](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/publics.md)

[申请模块](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/apply.md)

[审查模块](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/check.md)

[历史模块](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/history.md)

[权限模块](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/auth.md)

[场所模块](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/place.md)

[资源模块](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/resource.md)

[关联表模块](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/relation.md)

[通知模块](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/notification.md)

[数据库结构](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/database.md)

[场地与资源编号对应表](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/json.md)

## 接口一览表

+ 登录模块

[获取当前用户信息](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/user.md#get_user) `GET {base_url}/api/v3/user`

[用户登录](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/user.md#post_user) `POST {base_url}/api/v3/user`

[用户登出](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/user.md#delete_user) `DELETE {base_url}/api/v3/user`

+ 公共查询模块

[公共申请查询](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/publics.md#get_board) `GET {base_url}/api/v3/board`

[获取场地信息](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/publics.md#get_places) `GET {base_url}/api/v3/places`

[获取资源信息](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/publics.md#get_resources) `GET {base_url}/api/v3/resources`

+ 申请模块

[个人申请查询](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/apply.md#get_apply) `GET {base_url}/api/v3/apply`

[资源申请](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/apply.md#post_apply) `POST {base_url}/api/v3/apply`

[撤销申请](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/apply.md#delete_apply) `DELETE {base_url}/api/v3/apply/:applyid`

[修改申请](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/apply.md#put_apply) `PUT {base_url}/api/v3/apply`

+ 审查模块

[获取管理的资源](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/check.md#get_resources) `GET {base_url}/api/v3/admin/resources`

[获取管辖的申请](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/check.md#get_applyies) `GET {base_url}/api/v3/admin/applies`

[审核某一条申请](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/check.md#put_apply) `PUT {base_url}/api/v3/admin/apply`

+ 历史模块

[获取历史记录](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/history.md#get_admin_history) `GET {base_url}/api/v3/admin/history`

[查询历史记录](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/history.md#get_superadmin_history) `GET {base_url}/api/v3/superadmin/history`

+ 权限模块

[添加用户](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/auth.md#post_user) `POST {base_url}/api/v3/superadmin/user`

[删除用户](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/auth.md#delete_user) `DELETE {base_url}/api/v3/superadmin/user/:userid`

[修改用户权限](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/auth.md#put_user) `PUT {base_url}/api/v3/superadmin/user`

[获取用户权限](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/auth.md#get_user) `GET {base_url}/api/v3/superadmin/user`

+ 场所模块

[增加场地](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/place.md#post_place) `POST {base_url}/api/v3/superadmin/place`

[删除场地](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/place.md#delete_place) `DELETE {base_url}/api/v3/superadmin/place/:placeid`

[修改场地信息](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/place.md#put_place) `PUT {base_url}/api/v3/superadmin/place`

[获取场地信息](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/place.md#get_place) `GET {base_url}/api/v3/superadmin/place`

+ 资源模块

[增加资源](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/resource.md#post_resource) `POST {base_url}/api/v3/superadmin/resource`

[删除资源](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/resource.md#delete_resource) `DELETE {base_url}/api/v3/superadmin/resource/:resourceid`

[修改资源信息](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/resource.md#put_resource) `PUT {base_url}/api/v3/superadmin/resource`

[获取资源信息](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/resource.md#get_resource) `GET {base_url}/api/v3/superadmin/resource`

+ 关联表模块

[增加关联](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/relation.md#post_relation) `POST {base_url}/api/v3/superadmin/relation`

[删除关联](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/relation.md#delete_relation) `DELETE {base_url}/api/v3/superadmin/relation/:adminid/:resourceid`

[获取关联](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/relation.md#get_relation) `GET {base_url}/api/v3/superadmin/relation`

+ 通知模块

[增加通知](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/notification.md#post_notification) `POST {base_url}/api/v3/superadmin/notification`

[删除通知](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/notification.md#delete_notification) `DELETE {base_url}/api/v3/superadmin/notification/:notificationid`

[修改通知信息](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/notification.md#put_notification) `PUT {base_url}/api/v3/superadmin/notification`

[获取通知信息](https://git.tiaozhan.com/tiaozhan-dev/apply3/blob/dev/docs/notification.md#get_notification) `GET {base_url}/api/v3/superadmin/notification`