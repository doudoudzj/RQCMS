<?php
//数据库配置
define('DB_HOST','127.0.0.1');//数据库地址
define('DB_DATABASE','rqcms');//数据库名称
define('DB_USER','rq204');//数据库用户名
define('DB_PASSWORD','rq204');//数据库密码

//参数开关
define('RQ_DEBUG',False);//是否开启调试模式
define('RQ_CACHE',True);//对内容页是否启用自动缓存。

//时区的设置
date_default_timezone_set('Asia/Shanghai');