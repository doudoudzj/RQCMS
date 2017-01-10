<?php
//数据库配置
define('DB_HOST','localhost');//数据库地址
define('DB_DATABASE','rqcms');//数据库
define('DB_PREFIX','rqcms_');//数据表前缀
define('DB_USER','rqcms');//用户名
define('DB_PASSWORD','rq204');//密码

//参数开关
define('RQ_DEBUG',True);//是否开启调试模式
define('RQ_ALIAS',True);//是否开启泛域名解析,可以实现内容页单独域名功能
define('RQ_CACHE',True);//对内容页是否启用自动缓存。