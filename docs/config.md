# 初始化及服务器配置

## 下载文件

此次apply3将tp5框架完整(至少是较为完整地)上传到了git上，因此只需要完整克隆仓库即可完成下载过程

使用以下指令将apply3后端仓库克隆到当前命令行目录下的apply3文件夹中

``` git clone git@git.tiaozhan.com:tiaozhan-dev/apply3.git ```

## 创建数据库及用户

请在创建本项目所需的专用数据库，不同环境操作不同，一下仅以Windows下XAMPP为例

1. 打开XAMPP，启动Apache和MySQL，点击MySQL的Admin进入管理界面。

2. 在上方找到账户一栏，进入。

3. 找到新建一栏，点击新增用户账户。

4. 填写User name（用户名），Host name选择本地，点击生成密码并暂时记录下该密码，勾选下方的创建与用户同名的数据库并授予所有权限，然后拉动页面到最下方，点击执行。

5. 此时，数据库创建完毕，数据库名和用户名都是刚才填写的User name，密码是生成的密码，请在下一步结束前妥善保管

## 配置环境变量

环境变量需要在项目根目录创建.env文件并填写必要条目，可以参考.env.example

由于某些环境下文件名异常问题，您可能需要使用命令行/终端完成次任务，如果您之前git clone时使用的git bash还没有关闭，请使用它完成以下任务

1. 进入apply3项目根目录

如果之前git clone以后目录没有变，那么使用cd apply3即可进入

``` cd apply3 ```

2. 将.env.example复制到.env

Windows下：

``` copy .env.example .env ```

Linux下(git bash属于此类)：

``` cp .env.example .env ```

之后打开.env文件，内容大致如下：

``` py
[debug]
app = false # 是否开启调试模式
online = true # 是否开启在线调试模式
debug_module = false # 是否开启debug模块

[database]
database = tiaozhan_apply # 数据库名
username = tiaozhan_apply # 用户名
password = tiaozhan_apply # 密码

[sms]
api = http://api.smsbao.com/sms # 短信服务api地址
username = username # 短信服务用户名
password = password # 短信服务密码

[header]
acao_on = true # 是否开启 Access-Control-Allow-Origin
acac_on = true # 是否开启 Access-Control-Allow-Credentials
acam_on = true # 是否开启 Access-Control-Allow-Methods(开启则使用其值为GET,POST,DELETE,PUT)

[url]
frontend = http://apply.tiaozhan.com # 前端域名
froutend_login = http://apply.tiaozhan.com # 登录动作结束后跳转向的地址
froutend_logout = http://apply.tiaozhan.com # 登出动作结束后跳转向的地址
backend = http://apply.tiaozhan.com # 后端域名
backend_login_callback = http://apply.tiaozhan.com/v1/user/callback # 后端登录动作所需的callback地址
```

1. 调试部分

生产模式下请将关闭调试模式，若出现严重问题需要错误报告，请务必_同时开启“调试模式”和“在线调试模式”_

在线调试模式将对外隐藏错误报告，将错误报告存到项目根目录的logs文件夹下，并在出错时向用户报告该错误报告的文件名。
这样普通用户只能知道报告名，但是无法获取报告，仅有拥有管理员权限的人可以下载错误报告。

调试模块中含有部分用于后端调试用的方法，生产模式_禁止_开启

2. 数据库部分

数据库名、用户名、密码写成和之前创建数据库部分的对应即可

3. 短信服务部分

api基本上使用默认即可，除非短信服务换了（不过这个时候估计代码也得换）
用户名和密码根据实际情况输入即可

4. header部分

用于设置后端是否启动相关跨域请求许可
如果Apache或者Nginx已经配置了全局的跨域许可，则请根据实际情况决定是否关闭

5. url部分

frontend是前端域名，用于防止XSS时设置Access-Control-Allow-Origin，请务必和前端服务器设置一致

其后的login和logout正如其名，是指登录完毕以后和登出完毕以后，跳转向的地址，这两个地址应该指向前端服务器。
默认请和frontend保持一致，如果有需要请自行修改

backend是后端服务器的域名，其后的login.callback是登录动作中的callback指向的地址。
如果后端没有说明，请保证其地址为后端域名+/v1/user/callback

## 配置Access-Control-Allow-Origin

这里配置的主要目的是为了解决public目录下/v1/place.json和/v1/resource.json无法获取的问题

以下为Apache的配置方式，Nginx类似，请自行百度、谷歌

``` conf
<VirtualHost *:80>
    # This first-listed virtual host is also the default for *:80
    ServerName 127.0.0.2
    ServerAlias apply.tiaozhan.be.com

    SetEnv url_frontend http://apply.tiaozhan.fe.com # 重点在这里，将url_frontend环境变量设置为apply前端的域名

    DocumentRoot "E:/Projects/Tiaozhan/apply/apply3/public"
    <Directory "E:/Projects/Tiaozhan/apply/apply3/public">

        Options Indexes FollowSymLinks Includes ExecCGI

        AllowOverride All

        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    # This first-listed virtual host is also the default for *:80
    ServerName 127.0.0.3
    ServerAlias apply.tiaozhan.fe.com
    DocumentRoot "E:/Projects/Tiaozhan/apply/apply3-fe"
    <Directory "E:/Projects/Tiaozhan/apply/apply3-fe">

        Options Indexes FollowSymLinks Includes ExecCGI

        AllowOverride All

        Require all granted
    </Directory>
</VirtualHost>
```

由于public下的.htaccess是读取的环境变量url_frontend，因此设置shell环境变量或者系统环境变量理论上也是可以做到的。

对于Nginx，只要保证返回文件时(php无需设置)前面添加Header，指定Access-Control-Allow-Origin为前端服务器域名即可

## 初始化数据库

请在项目根目录下执行初始化批处理：

Windows平台：双击init.bat或者在命令行执行init.bat即可

Linux平台：执行``` bash init.sh ```

其内容大致如下：

```
php think apply3:renderenvs
php think apply3:drop
php think migrate:run
php think apply3:import *
php think apply3:export place public/v1/place.json
php think apply3:export resource public/v1/resource.json
```

作用依次为：

1. 从环境变量中读取参数并填入项目实际配置中

_该功能可能会有平台/PHP版本相关性，不保证其功能正常_

_若配置出现问题，请手动查找项目中的.example文件(目前有6个)，并根据模板填写实际配置文件_

2. 删除数据库中原有的所有数据表(防止bug)

3. 执行迁移命令，创建各个数据表

4. 从/database/import导入所有的json文件到数据库，添加初始数据

5. 将数据库中的place表导出到/public/v1/place.json，保证前端请求不会报错

6. 将数据库中的resource表导出到 /public/v1/resource.json，保证前端请求不会报错

完成后数据库即初始化完毕

## 启动申请自动提醒功能

提醒功能已经完成，每一次在根目录下执行``` php think apply3:alert ```时，将会向所有未完成审批的老师发送短信

但是自动定时功能暂时没有完成