<?php
/**
 * RQCMS       A simple,personal,multi-site cms 
 *
 * @copyright  Copyright (c) 2010-2014 RQ204
 * @license    GNU General Public License 2.0
 * @t          http://t.qq.com/winslow
 */
 //版权相关设置
define('RQ_AppName','RQCMS');
define('RQ_VERSION','2.4');
define('RQ_RELEASE','20160327');
define('RQ_AUTHOR','RQ204');
define('RQ_WEBSITE','http://www.rqcms.com');
define('RQ_EMAIL','rq204@qq.com');

//核心参数
define('RQ_ROOT',dirname(__file__));
define('RQ_CORE',RQ_ROOT.'/core');
define('RQ_DATA',RQ_ROOT.'/data');
define('RQ_HOST',$_SERVER['HTTP_HOST']);
define('RQ_POST',$_SERVER['REQUEST_METHOD'] == 'GET' ? false : true);
define('RQ_HTTP',(isset($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'],'off')!=0) ? 'https://' : 'http://');
define('RQ_ISIE',isset($_SERVER['HTTP_USER_AGENT'])&&strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')); 

//加载公共类和配置文件
include RQ_CORE.'/library/class.mysql.php';
include RQ_CORE.'/library/func.base.php';
include RQ_CORE.'/library/func.agile.php';
include RQ_CORE.'/library/func.cache.php';
include RQ_CORE.'/library/func.data.php';
include RQ_DATA.'/config.php';

//错误提示设置和参数过滤
if(RQ_DEBUG) 
{
	error_reporting(E_ALL);
	set_error_handler("debug");
}
else error_reporting(0);

//禁止自动转反斜杠
if(get_magic_quotes_runtime()) set_magic_quotes_runtime(false);
doStripslashes();

//数据库实例化
$DB=new DB_MySQL();
$DB->connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE,0);

//开启缓冲区
ob_start();

//获取请求的网址，处理部分服务器对重写的网址没有GET参数的解决办法,使用的是iirf中的U参数将请求网址保存在HTTP_X_REWRITE_URL
if(isset($_SERVER['SERVER_SOFTWARE'])&&strpos($_SERVER['SERVER_SOFTWARE'],'IIS')!==false)//IIS,如 Microsoft-IIS/6.0是HTTP_X_REWRITE_URL，7.5是REQUEST_URI
{
	if(!isset($_SERVER['HTTP_X_REWRITE_URL'])) exit('this iis server is not support rqcms!');
    $HTTP_X_REWRITE_URL=$_SERVER['HTTP_X_REWRITE_URL'];

	$REQUEST_URI=substr($HTTP_X_REWRITE_URL,1);
	if(empty($_GET)&&strpos($HTTP_X_REWRITE_URL,'?'))
	{
		$_GET=getGetArr($HTTP_X_REWRITE_URL);
	}
	$QUERY_URL=$HTTP_X_REWRITE_URL;
}
else if(isset($_SERVER['SERVER_SOFTWARE'])&&strpos($_SERVER['SERVER_SOFTWARE'],'nginx')!==false)//nginx
{
	if(!isset($_SERVER['REQUEST_URI'])) exit('this nginx server is not support rqcms!');
	$REQUEST_URI=substr($_SERVER['REQUEST_URI'],1);
	if(isset($_SERVER['HTTP_X_REWRITE_URL'])) $QUERY_URL=$_SERVER['HTTP_X_REWRITE_URL'];
	else $QUERY_URL=$_SERVER['QUERY_STRING'];
}
else
{
	foreach(array('REDIRECT_REDIRECT_SCRIPT_URL','REDIRECT_SCRIPT_URL','SCRIPT_URL','REDIRECT_URL','HTTP_X_REWRITE_URL','REQUEST_URI','SCRIPT_NAME') as $rqfile)
	{
		if(isset($_SERVER[$rqfile]))
		{	
			$REQUEST_URI=substr($_SERVER[$rqfile],1);
			$QUERY_URL=$_SERVER['REQUEST_URI'];
			break;
		}
	}
	if(!isset($REQUEST_URI)) exit('this http server is not support rqcms!');
	if(empty($_GET)&&strpos($_SERVER['REQUEST_URI'],'?')>1) $_GET=getGetArr($_SERVER['REQUEST_URI']);
}

if(empty($_POST)&&isset($HTTP_RAW_POST_DATA)) $_POST=$HTTP_RAW_POST_DATA;

//IP地址和User-Agent
$onlineip=getIp();
$useragent=isset($_SERVER['HTTP_USER_AGENT'])?addslashesDeep($_SERVER['HTTP_USER_AGENT']):'';
$useragent=htmlspecialchars($useragent);
$timestamp=time();
$hookArr = array();//当前站点的插件方法列表

//读取选择站点
$HostArr = @include RQ_DATA.'/cache/hosts.php';//加载所有站点信息,所有站点信息都在里边
if($HostArr&&is_array($HostArr)&&isset($HostArr[RQ_HOST]))
{
	$host=$HostArr[RQ_HOST];//站点数组,最常用
}

if(!isset($host))//没找到任何站点时，403或是安装
{
	if(is_array($HostArr)&&count($HostArr)>0) include_once RQ_CORE.'/404.php';
	else if($REQUEST_URI!='install.php') exit("<p ><a href='/install.php'>install RQCMS</a>");
	else include_once RQ_CORE.'/install.php';
	exit();
}

//读取缓存数据
$hostid=$host['hid'];//站点id
$setting=array();//配置信息，包含filemap,plugin,var,link,redirect
$setting=@include RQ_DATA.'/cache/setting_'.$hostid.'.php';
if(!$setting) $setting=array();
$category=array();//当前站点的分类数据
$category=@include RQ_DATA.'/cache/category_'.$hostid.'.php';
if(!$category) $category=array();
$varArr=isset($setting['var'])?$setting['var']:array();

//定义RQ_FILE
$urlstring=$REQUEST_URI;
$urlext=!$host['url_ext']?'':('.'.$host['url_ext']);
if($urlext&&substr($REQUEST_URI,0-strlen($urlext))==$urlext)
{
	$urlstring=substr($REQUEST_URI,0,strlen($REQUEST_URI)-strlen($urlext));
}
if($urlstring)
{
	$urlstring=trim($urlstring,'/');
	$urlargs=explode('/',$urlstring);
	define('RQ_FILE',$urlargs[0]);
	$_GET['url1']=count($urlargs)>1?$urlargs[1]:'';
	if(count($urlargs)>2) $_GET['url2']=$urlargs[2];
	if(count($urlargs)>3) $_GET['url3']=$urlargs[3];
}
else define('RQ_FILE','index');
//定义数据表前缀，用来区分不同的站点
define('DB_PREFIX','rqcms_'.$hostid.'_');

$constant = get_defined_constants();

//加载模板
$theme=$host['theme'];//站点模板
if($host['thememobile']&&from_mobile())
{
	$theme=$host['thememobile'];
}
if($host['themeweixin']&&from_weixin())
{
	$theme=$host['themeweixin'];
}
if(!isset($theme)) $theme='default';

//加载执行文件和模板
$views=isset($setting['filemap'][RQ_FILE])?$setting['filemap'][RQ_FILE]:"404";
if(RQ_FILE=='index') $views='index';
$coreView=RQ_CORE.'/'.$views.'.php';//核心处理文件
$tempView=RQ_DATA.'/themes/'.$theme.'/'.$views.'.php';//风格模板文件
$contentType='Content-Type: text/html; charset=UTF-8';

//加载插件，插件目录和插件文件名应保持一致
if (isset($setting['plugin']) && is_array($setting['plugin']))
{
	foreach($setting['plugin'] as $pluginName=>$pluginData)
	{
		if(file_exists(RQ_DATA.'/plugins/'.$pluginName.'/'.$pluginName.'.php'))
		{
			include RQ_DATA.'/plugins/'.$pluginName.'/'.$pluginName.'.php';
		}
	}
}

//特别几个网址的处理
$host_url=RQ_HTTP.RQ_HOST;
$page_url=RQ_HTTP.RQ_HOST.$QUERY_URL;
$refer_url=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
$login_url=mkUrl('profile','login');
$logout_url=mkUrl('profile','logout');
$register_url=mkUrl('profile','register');
$profile_url=mkUrl('profile','');
$search_url=mkUrl('search','');
$tag_url=mkUrl('tag','');
$comment_url=mkUrl('comment','');
$admin_url=mkUrl('admin','');
$rss_url=mkUrl('rss','');

doAction('before_router');
include_once $coreView;
include_once $tempView;

//输出前处理,输出ContentType,网址重写，插件处理，网页压缩
header($contentType);
header('Cache-Control:max-age=0');//缓存的处理http://blog.csdn.net/nashuiliang/article/details/7854633
$output=ob_get_contents();
ob_end_clean();
doAction('before_output');
if($host['gzipcompress']&& function_exists('ob_gzhandler'))
{
	ob_start('ob_gzhandler');
}
else
{
	ob_start();
}
echo $output;
ob_flush();//输出内容