<?php
/**
 *  该升级程序可以将以前的所有旧版本升级到 RQCMS 1.2 
 *  注意要 RQ_CORE 和 RQ_DATA 要和网站一致
 *  升级完成后删除这个文件.升级完后后台更新一下缓存
 */

//核心参数
define('RQ_ROOT',dirname(__file__));
define('RQ_CORE',RQ_ROOT.'/core');
define('RQ_DATA',RQ_ROOT.'/data');
define('RQ_HOST',$_SERVER['HTTP_HOST']);
define('RQ_POST',$_SERVER['REQUEST_METHOD'] == 'GET' ? false : true);
define('RQ_HTTP',(isset($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'],'off')!=0) ? 'https://' : 'http://');


//加载公共类和配置文件
include RQ_CORE.'/library/class.mysql.php';
include RQ_CORE.'/library/func.base.php';
include RQ_CORE.'/library/func.agile.php';
include RQ_CORE.'/library/func.cache.php';
include RQ_CORE.'/library/func.data.php';
include RQ_DATA.'/config.php';

//数据库实例化
$DB=new DB_MySQL();
$DB->connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE,0);

header('Content-Type: text/html; charset=UTF-8');
print <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="UTF-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta name="author" content="RQ204" />
<title>RQCMS自动升级程序</title></head><body>
EOT;
$varhost=$DB->fetch_first('Select * from '.DB_PREFIX.'host');
if(!isset($varhost['host2']))//1.2的升级
{
	$DB->query("ALTER TABLE `".DB_PREFIX."host` ADD COLUMN `host2` VARCHAR(100) NULL DEFAULT ''");
	echo '升级host字段host2成功<br />';
}

$hostquery=$DB->query('Select * from '.DB_PREFIX.'host');
while($arr=$DB->fetch_array($hostquery))
{
	$varhid=$arr['hid'];
	$varfilemap=$DB->fetch_first('Select * from '.DB_PREFIX."filemap where original='archive.php' and hostid=$varhid");
	if(!isset($varfilemap['original'])) $DB->query('insert into '.DB_PREFIX."filemap (`original`,`filename`,`hostid`) values ('archive.php','archive','$varhid')");
	$varfilemap=$DB->fetch_first('Select * from '.DB_PREFIX."filemap where original='link.php' and hostid=$varhid");
	if(!isset($varfilemap['original'])) $DB->query('insert into '.DB_PREFIX."filemap (`original`,`filename`,`hostid`) values ('link.php','link','$varhid')");
}
 
 exit('升级完成<body></html>');
