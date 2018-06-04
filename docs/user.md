# 用户模块

权限：该模块所有API任何人可以访问

## 接口列表

[获取当前用户信息](#get_user) `GET {base_url}/api/v3/user`

[用户登录](#post_user) `POST {base_url}/api/v3/user`

[用户登出](#delete_user) `DELETE {base_url}/api/v3/user`

## 登录状态详解

前端发送GET {base_url}/api/v3/user后，根据success即可确认是否登录

## 登录过程详解

1. 前端发送POST {base_url}/api/v3/user，参数为空

2. 登录完以后将跳转至环境变量中设置的[url]froutend_login地址中，请前端根据需要修改

_模拟表单是指，直接用JS创建DOM，弄出一个指向目标地址的表单，并提交，这样才能使浏览器跳转_

_若使用ajax模式发送，就会造成虚拟client跳转，而浏览器没动，用户无法输入用户名和密码_

## 登出过程详解

1. 前端发送DELETE {base_url}/api/v3/user，参数为空

2. 登出完以后将跳转至环境变量中设置的[url]froutend_logout地址中，请前端根据需要修改

## 接口详情

<a name="get_user"></a>

### 获取当前用户信息 `GET {base_url}/api/v3/user`

+ Request

        // 后端直接调用tzapi-client-php，无需参数，无论发什么后端都会无视掉

+ Response 200

        // 未登录时
        {
            "success": 1,
            "data": {
                "auth": 0
            }
        }

+ Response 200

        {
            "success":1,
            "data":
            {
                "userid":"NetID",
                "auth":1,//权限，1代表申请者，2代表审查者，3表示数据库管理员
                "userinfo":
                {
                    "dep":"xx学院",
                    "depid":"学院代号",
                    "userid":"NetID",
                    "username":"姓名",
                    "userno":"学号",
                    "usertype":"1",
                    "uno":"",
                    "sex":"性别",
                    "usertype_name":"统招本科生",
                    "speciality":"专业",
                    "class":"班级",
                    "dormname":"",
                    "dormid":"宿舍楼号",
                    "roomid":"宿舍号",
                    "cardno":"",
                    "card_account":"",
                    "balance":"",
                    "bankacc":"",
                    "nation":"",
                    "native_area":"出生地",
                    "people":"种族",
                    "idtype":"证件类型",
                    "idnumber":"证件号",
                    "zzmm":"中国国产主义青年团",
                    "birthday":"1998-01-01",
                    "email":"xxx@stu.xjtu.edu.cn",
                    "mobile":"手机号",
                    "tutorname":"",
                    "openflag":"",
                    "last_update":1515859468//上次更新时间戳
                }
            }
        }

<a name="post_user"></a>

### 用户登录 `POST {base_url}/api/v3/user`

+ Request

        // 不需要任何参数，后端将无视参数

+ Response

        // 没有响应，只会跳转到前端想要跳转的地方

<a name="delete_user"></a>

### 用户登出 `DELETE {base_url}/api/v3/user`

+ Request

        // 不需要任何参数，后端将无视参数

+ Response

        // 没有响应，只会跳转到前端想要跳转的地方
