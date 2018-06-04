<?php

/**
 * 该数组指定了如何导入环境变量配置
 * 箭头左边是该配置的模板文件
 * 箭头右边是导出文件
 * 执行apply3:envs命令后
 * 模板中所有双大括号指定的变量将被替换(字符串替换)为环境变量中的值
 * 
 * 例如：
 * (文件：.env)
 * [test]
 * param_1 = 123
 * 
 * (文件：abc.php.example)
 * <?php
 * $myparam = {{param_1}};
 * 
 * (文件：envs.php)
 * return [
 *     'abc.php.example' => 'abc.php',
 * ]
 * 
 * 则执行apply3:envs命令后，将会生成一个abc.php文件，内容如下：
 * <?php
 * $myparam = 123;
 */

return [
];
