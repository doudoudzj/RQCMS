<?php
/**
 * RQCMS 1.0   A simple,personal,multi-site cms 
 *
 * @copyright  Copyright (c) 2010-2012 RQ204
 * @license    GNU General Public License 2.0
 * @t          http://t.qq.com/winslow
 */
 //版权相关设置
define('RQ_AppName','RQCMS');
define('RQ_VERSION','1.11');
define('RQ_RELEASE','20121008');
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
foreach(array('REDIRECT_REDIRECT_SCRIPT_URL','REDIRECT_SCRIPT_URL','SCRIPT_URL','REDIRECT_URL','HTTP_X_REWRITE_URL','REQUEST_URI','SCRIPT_NAME') as $rqfile)
{
	if(isset($_SERVER[$rqfile]))
	{	
		define('REQUEST_URI',substr($_SERVER[$rqfile],1));break;
	}
}

//加载公共类和配置文件
include RQ_CORE.'/library/class.mysql.php';
include RQ_CORE.'/library/func.base.php';
include RQ_CORE.'/library/func.agile.php';
include RQ_CORE.'/library/func.cache.php';
include RQ_CORE.'/library/func.data.php';
include RQ_DATA.'/config.php';

//禁止自动转反斜杠
if(get_magic_quotes_runtime()) set_magic_quotes_runtime(false);
doStripslashes();

//错误提示设置和参数过滤
if(RQ_DEBUG) 
{
	error_reporting(E_ALL);
	set_error_handler("debug");
}
else error_reporting(0);
ob_start();

//IP地址和User-Agent
$onlineip=getIp();
$useragent=isset($_SERVER['HTTP_USER_AGENT'])?addslashesDeep($_SERVER['HTTP_USER_AGENT']):'';
$hosturl=RQ_HTTP.RQ_HOST;

//时区的设置
date_default_timezone_set('Asia/Shanghai');
$timestamp=time();


//处理部分服务器对重写的网址没有GET参数的解决办法,如kangle服务器
if(empty($_GET)&&strpos($_SERVER['REQUEST_URI'],'?')>1)
{
	foreach(explode('&',substr($_SERVER['REQUEST_URI'],strpos($_SERVER['REQUEST_URI'],'?')+1)) as $tget)
	{
		$gets=explode('=',$tget);
		$_GET[$gets[0]]=isset($gets[1])?$gets[1]:'';
	}
}
if(empty($_POST)&&isset($HTTP_RAW_POST_DATA)) $_POST=$HTTP_RAW_POST_DATA;

//读取缓存数据,加载插件，注意，大写开头的变量是所有站点可用，小写开头的变量是当前站点可用
$HostArr = array();//站点数组,如果为多个,则需要判断是否存在的站点
$mapArr = array();//文件数组,需要寻找对应的文件并包含
$pluginArr = array();//插件文件数组
$pluginConfigArr=array();//插件的配置数据
$hookArr = array();//当前站点的插件数据
$cateArr=array();//当前站点的分类数据
$varArr=array();//当前站点的变量数据,建议插件的缓存也加入

$HostArr = @include RQ_DATA.'/cache/hosts.php';//加载所有站点信息
if($HostArr&&is_array($HostArr)&&isset($HostArr[RQ_HOST]))
{
	$host=$HostArr[RQ_HOST];//站点数组,最常用
}

//数据库实例化
$DB=new DB_MySQL();
$DB->connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE,0);

if(!isset($host))//没找到任何站点时，403或是安装
{
	$view='install.php';
	if(is_array($HostArr)&&count($HostArr)>0) $view='404.php';
	include_once RQ_CORE.'/'.$view;
	exit();
}

$hostid=$host['hid'];//站点id
$theme=$host['theme'];//站点模板
$mapArr= @include RQ_DATA.'/cache/map_'.$host['host'].'.php';
$cateArr=@include RQ_DATA.'/cache/cate_'.$host['host'].'.php';
$varArr=@include RQ_DATA.'/cache/var_'.$host['host'].'.php';
$pluginArr = @include RQ_DATA.'/cache/plugins.php';
if(!$cateArr) $cateArr=array();
if(isset($pluginArr)&&!empty($pluginArr))
{
	foreach($pluginArr as $pluginHost=>$pluginNameValue)
	{
		if($host['host']==$pluginHost)
		{
			$pluginArr=$pluginArr[$pluginHost];break;
		}
	}
}

//是否动态网址
$isDynamic=!$host['url_html'];

if(REQUEST_URI==''||REQUEST_URI=='?'||substr(REQUEST_URI,0,1)=='?') define('RQ_FILE','index'.($isDynamic?'.'.$host['url_ext']:''));
else
{
	if($isDynamic)//动态网址
	{
		define('RQ_FILE',ltrim(strpos(REQUEST_URI,'?')>1?substr(REQUEST_URI,0,strpos(REQUEST_URI,'?')):REQUEST_URI,'/'));
	}
	else//静态网址
	{
		$urlstring=trim(REQUEST_URI,'/');
		$urlargs=explode('/',trim(REQUEST_URI,'/'));
		$urlext=!$host['url_ext']?'':('.'.$host['url_ext']);

		if($urlext&&count($urlargs)==1&&substr($urlargs[0],0-strlen($urlext))==$urlext) $urlargs[0]=substr($urlargs[0],0,0-strlen($urlext));
		else if($urlext&&count($urlargs)==1&&substr($urlargs[0],0-strlen($urlext))!=$urlext) $urlargs[0]='';
		define('RQ_FILE',$urlargs[0]);
		if(count($urlargs)%2==1) $urlargs[]='';
		if($urlext&&strlen($urlargs[1])>strlen($urlext)&&substr($urlargs[1],0-strlen($urlext))==$urlext) $urlargs[1]=substr($urlargs[1],0,0-strlen($urlext));
		$_GET['url']=$urlargs[1];
		
		for($i=2;$i<count($urlargs)-1;$i++)
		{
			if($i%2==0) $_GET[$urlargs[$i]]=$urlargs[$i+1];
		}
	}
}

//设置运行的文件
$username='';
$groupid=$uid=0;
$sessionid=isset($_COOKIE['sessionid'])?$_COOKIE['sessionid']:'';
if(isset($_GET['sessionid'])) $sessionid=$_GET['sessionid'];//在多站点切换时,使用这个来保持登陆状态
if(!empty($sessionid)&&strlen($sessionid)==30)
{
	$userinfo=$DB->fetch_first('Select * from '.DB_PREFIX."user where `sessionid`='$sessionid' and (`groupid`=4 or `hostid`='$hostid')");//创始人可以登陆每个站点,其他人受限
	if($userinfo)
	{
		$nowips=explode('.',$onlineip);
		$oldips=explode('.',$userinfo['loginip']);
		$diffip=array_diff_assoc($nowips,$oldips);
		if(count($diffip)<2&&!isset($diffip[2])&&$useragent==$userinfo['useragent'])//当最后一位不同时认为是同一地点
		{
			$uid=$userinfo['uid'];
			$username=$userinfo['username'];
			$groupid=$userinfo['groupid'];//0是游客,1注册会员,2编辑,3管理员,4创始人
		}
		if(!isset($_COOKIE['sessionid'])||$_COOKIE['sessionid']!=$sessionid)
		{	
			if(isset($_COOKIE['sessionid'])&&$_COOKIE['sessionid']!=$sessionid) 
			{
				setcookie('sessionid','');
				if(!RQ_ISIE) setcookie('sessionid','',-1,'/','.'.$host['host']);//使用泛域名解析后，需要删除.rq.cn这样的cookie的域
				message('站点切换成功','admin.php?sessionid='.$sessionid);
			}
			else 
				setcookie('sessionid',$sessionid);
		}
	}
}
//参数重写,根据$mapArr数组将网址转为固定的网址格式，如article.php在网页上显示为read.php,该功能是将read.php转换成article.php
argRewrite();

$constant = get_defined_constants();
if(!isset($theme)) $theme='default';
//加载执行文件和模板
$views=isset($mapArr['file'][RQ_FILE])?$mapArr['file'][RQ_FILE]:"404.php";
if((RQ_FILE=='index.php'||RQ_FILE=='index')) $views='index.php';
if($host['close']&&isset($mapArr['file'][RQ_FILE])&&$mapArr['file'][RQ_FILE]!='admin.php') exit($host['close_note']);
$coreView=RQ_CORE.'/'.$views;//核心处理文件
$tempView=RQ_DATA.'/themes/'.$theme.'/'.$views;//风格模板文件
$contentType='Content-Type: text/html; charset=UTF-8';

//加载插件，插件目录和插件文件名应保持一致
if ($pluginArr && is_array($pluginArr))
{
	foreach($pluginArr as $pluginName=>$pluginData)
	{
		if(file_exists(RQ_DATA.'/plugins/'.$pluginName.'/'.$pluginName.'.php'))
		{
			include RQ_DATA.'/plugins/'.$pluginName.'/'.$pluginName.'.php';
		}
	}
}

//特别几个网址的处理
$login_url=mkUrl('profile.php','login');
$logout_url=mkUrl('profile.php','logout');
$register_url=mkUrl('profile.php','register');
$profile_url=mkUrl('profile.php','');
$search_url=mkUrl('search.php','');
$tag_url=mkUrl('tag.php','');
$comment_url=mkUrl('comment.php','');
$admin_url=mkUrl('admin.php','');
$rss_url=mkUrl('rss.php','');

doAction('before_router');
include_once $coreView;
include_once $tempView;
//输出前处理,输出ContentType,网址重写，插件处理，网页压缩
header($contentType);
$output=ob_get_contents();
ob_end_clean();
$output=adminRewrite($output);
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