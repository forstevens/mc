# apply3 数据库初始化说明

> Author:
丁浩然 dinghaoran@tiaozhan.com

### 综述

我们对于数据库的一系列操作，实现了高度的集成与封装。

基本用命令行操作就能实现apply3数据库的初始化。

#### 步骤详细说明

+ 第一步，删除数据库中的所有数据表(如果有的话)。

        php think apply3:drop

+ 第二步，使用Phinx数据库迁移文件构造出各个数据表的结构。

        php think migrate:run

+ 第三步，将APP/database/import目录下的所有json文件的内容导入相对应的数据表。

        php think apply3:import *

+ 第四步，需要手动将apply2的数据库导入到本地mysql中。

+ 第五步，切换到apply-trans项目的根目录(懒得写在一块儿了，就分了一个小项目)。双击local.bat即可执行如下三条语句。

        php think trans:apply

        与

        php think trans:resource

        与

        php think trans:user

+ 第六步，切回本项目的根目录，将过期的申请一键改为不通过。

        php think apply3:disable

+ 第七步，此时数据库已初始化完毕，将新数据库导出为.sql文件即可。
