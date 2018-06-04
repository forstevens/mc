# 挑战网Apply3镜像

> 最近修订记录：
> 2018.3.22 - dongjiangbin 初稿  

**[Type]** `App Image`

本镜像基于tz-php7制作，用法基本和tz-php7相同，但是对容器做了一定配置，请仔细阅读以下的change logs

_另外请注意，script下的文件可能因为git的原因换行符变为\r\n，请务必检查换行符并保证换行符为\n_

------

## Change Logs

1. docker-compose.yml

添加项目：
``` yml
  ports:
    - 127.0.0.1:80:80
  env_file: .env
```

主要修改为环境变量从文件.env读取，方便环境变量的修改。（script/run也相应做了修改）
修改样例可以参考.env.example和docker-compose.yml.example

2. website.conf

rewrite修改
``` conf
if (!-e $request_filename) {
    rewrite  ^/api/v3/(.*)\.json$ /static/$1.josn last;
    rewrite  ^/(.*)$  /index.php?s=$1  last;
    break;
}
```
添加了api/v3/*.json的rewrite规则，从而使这两个接口绕开php，节约资源。
但这样要求php对static目录有写入权限。

3. run脚本

``` shell
sed -i "7s/curl_exec,//" /etc/php7/php.ini
```
解禁curl_exec命令，tzAPIClient需要使用。

4. build脚本

``` shell
mv /runtime/config/website.conf /etc/nginx/sites-enabled/website.conf
chown -R nginx:nginx /runtime/APP/runtime
chown -R nginx:nginx /runtime/APP/public/static

sleep 1s # sleep 1s to hack bug

cat << EOF >> /etc/crontabs/root
30 3 * * * php /runtime/think apply3:disable
EOF
```
传入新的nginx配置，以及调整runtime和static的权限。
理论上static不需要全部权限，只需要写入和读取即可，无需执行，请运维酌情修改。

定时执行apply:disable，每天凌晨3点半自动拒绝过期申请，不发短信。

## 项目介绍 for op
### APP下的几个目录用途如下：
+ application 应用
+ database 本地调试用数据库操作（上线后此目录无用）
+ extend、vendor 第三方依赖（基本没有）
+ thinkphp tp5框架
+ public 项目根目录
+ runtime 运行时（主要是logs、reports等）

### application下的3个目录及部分文件用途如下：
+ debug debug用模块（上线时不应该启动此模块，但也不要直接删除，通过环境变量禁用即可）
+ extra 部分参数配置（重要的均为从环境变量读取）
+ v3 apply3主模块
+ config.php 配置文件，主要用来配置debug模式（从环境变量读取）
+ database.php 数据库配置文件（从环境变量读取）
+ route.php 路由文件（为了减轻运维的工作，使用route来完成大部分url重写工作）

### php命令行指令
使用docker exec ... /bin/sh 进入容器后，可以在/runtime下使用“php think 指令名 [参数列表]”的形式调用部分指令。
虽然大部分都因为数据库权限问题而无法使用，但仍对这些命令做了保留，直接使用“php think”也可以查看所有指令。

其中最重要的指令为“php think apply3:alert”，每次运行该指令时，将会短信通知所有没有完成审核的老师尽快完成审核。
请保证该指令每天定时执行。

### debug模式
apply3针对debug进行了扩充，可以在环境变量中设置（.env）同时开启APP_DEBUG和ONLINE_DEBUG，开启在线调试模式。
该模式的优点为：生成完整错误报告，保存到/runtime/APP/runtime/reports目录中，但返回页面仅给出文件名，没有详细错误信息，从而保障项目细节不被公开。
并在sadmin模块中保留了查看错误报告的api，拥有超级管理员权限(auth=3，负责老师为auth=2)的用户可以直接查看错误报告，从而方便调试。

### public/api/v3/*.json
考虑到场地、资源等模块的实际情况，该部分在上线稳定后几乎不会修改。
为了减轻服务器的负担，这两个api实际上是直接获取静态文件，绕开了fastcgi，从而减轻了php进程的压力。
但实际上为了保证这两个文件和数据库内容同步，数据库更改时（通过api修改），会自动将数据库内容写入这两个文件，因此这个目录也需要写入权限。

### 环境变量解释
``` sh
APP_DEBUG=true # 是否启动调试模式（请尽量保证本项和下一项同时开启）
ONLINE_DEBUG=true # 是否开启在线调试模式
DEBUG_MODULE=false # 是否开启Debug用模块（请务必关闭，该模块有越权操作）

DB_DBNAME=tiaozhan_apply # 数据库名称
DB_USERNAME=username # 数据库用户名
DB_PASSWORD=password # 数据库密码

SMS_API=http://api.smsbao.com/sms # 短信服务api地址
SMS_USERNAME=username # 短信服务用户名
SMS_PASSWORD=password # 短信服务密码

URL_FE=http://apply.tiaozhan.com # 前端页面URL（上线后和下一项一致）
URL_BE=http://apply.tiaozhan.com # 后端页面URL（上线后和下一项一致）
```