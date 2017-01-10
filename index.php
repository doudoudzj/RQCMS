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
define('RQ_VERSION','0.99');
define('RQ_RELEASE','20120708');
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
		define('RQ_FILE',($_SERVER[$rqfile]=='/'||$_SERVER[$rqfile]=='/?')?'index.php':ltrim(strpos($_SERVER[$rqfile],'?')>1?substr($_SERVER[$rqfile],0,strpos($_SERVER[$rqfile],'?')):$_SERVER[$rqfile],'/'));define('REQUEST_URI',$_SERVER[$rqfile]);break;
	}
}

//加载公共类和配置文件
include RQ_CORE.'/library/class.mysql.php';
include RQ_CORE.'/library/func.base.php';
include RQ_CORE.'/library/func.cache.php';
include RQ_CORE.'/library/func.data.php';
include RQ_DATA.'/config.php';

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

//错误提示设置和参数过滤
if(RQ_DEBUG) 
{
	error_reporting(E_ALL);
	set_error_handler("debug");
}
else error_reporting(0);
ob_start();
doStripslashes();
if(get_magic_quotes_runtime()) set_magic_quotes_runtime(false);

//数据库实例化
$DB=new DB_MySQL();
$DB->connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE,0);

//读取缓存数据,加载插件
$Hosts = array();//站点数据,如果为多个,则需要判断是否存在的站点
$Files = array();//文件数组,需要寻找对应的文件并包含
$Plugins = array();//插件文件数组
$PluginsConfig=array();//插件的配置数据
$wdHooks = array();//插件函数数据
$Hosts = @include RQ_DATA.'/cache/hosts.php';//加载所有站点信息
$cates=array();//当前站点的分类数据
$var=array();//当前站点的变量数据,建议插件的缓存也加入

if($Hosts&&is_array($Hosts)&&isset($Hosts[RQ_HOST]))
{
	$host=$Hosts[RQ_HOST];//站点数组,包含
}
else if($Hosts&&is_array($Hosts)&&!isset($Hosts[RQ_HOST]))
{
	//如果使用了泛解析的，多级域名的处理
	foreach($Hosts as $hs)
	{	
		//$aliasname为除过根域名外的域名部分，如 xx.rq.cn 相对 rq.cn 多出来的 xx 即为该值
		$aliasname=substr(RQ_HOST,0,strlen(RQ_HOST)-strlen($hs['host']));
		if(substr(RQ_HOST,0-(strlen($hs['host'])))==$hs['host']&&substr($aliasname,-1)=='.')
		{
			$Hosts = @include RQ_DATA.'/cache/hosts.php';
			$host=$Hosts[$hs['host']];
			$aliasname=substr($aliasname,0,strlen($aliasname)-1);
			break;
		}
		else unset($aliasname);
	}
}
if(isset($host))
{
	$hostid=$host['hid'];//站点id
	$theme=$host['theme'];//站点模板
	$Files= @include RQ_DATA.'/cache/map_'.$host['host'].'.php';
	$cates=@include RQ_DATA.'/cache/cate_'.$host['host'].'.php';
	$var=@include RQ_DATA.'/cache/var_'.$host['host'].'.php';
	$Plugins = @include RQ_DATA.'/cache/plugins.php';
	if(!$cates) $cates=array();
	if(isset($Plugins)&&!empty($Plugins))
	{
		foreach($Plugins as $pluginHost=>$pluginNameValue)
		{
			if($host['host']==$pluginHost)
			{
				$Plugins=$Plugins[$pluginHost];
			}
		}
	}
}

//时区的设置
date_default_timezone_set('Asia/Shanghai');
$timestamp=time();

//IP地址和User-Agent
$onlineip=getIp();
$useragent=isset($_SERVER['HTTP_USER_AGENT'])?addslashesDeep($_SERVER['HTTP_USER_AGENT']):'';

//设置运行的文件
if(!$Files||!is_array($Files))
{
	$Files=array('file'=>array('install.php'=>'install.php'),'arg'=>array());
}
else //权限判定
{
	$username=$groupid=$uid=0;
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
}

//参数重写,根据$Files数组将网址转为固定的网址格式，如article.php在网页上显示为read.php,该功能是将read.php转换成article.php
argRewrite();

$constant = get_defined_constants();
if(!isset($theme)) $theme='default';
//加载执行文件和模板
$views=isset($Files['file'][RQ_FILE])?$Files['file'][RQ_FILE]:"404.php";
if(RQ_FILE=='index.php'&&isset($host)) $views='index.php';
if(isset($host['close'])&&$host['close']&&isset($Files['file'][RQ_FILE])&&$Files['file'][RQ_FILE]!='admin.php') exit($host['close_note']);
$coreView=RQ_CORE.'/'.$views;//核心处理文件
$tempView=RQ_DATA.'/themes/'.$theme.'/'.$views;//风格模板文件
$ContentType='Content-Type: text/html; charset=UTF-8';

//加载插件，插件目录和插件文件名应保持一致
if ($Plugins && is_array($Plugins))
{
	foreach($Plugins as $pluginName=>$pluginData)
	{
		if(file_exists(RQ_DATA.'/plugins/'.$pluginName.'/'.$pluginName.'.php'))
		{
			include RQ_DATA.'/plugins/'.$pluginName.'/'.$pluginName.'.php';
		}
	}
}

doAction('before_router');
include_once $coreView;
include_once $tempView;
//输出前处理,输出ContentType,网址重写，插件处理，网页压缩
header($ContentType);
$output=ob_get_contents();
ob_end_clean();
$output=urlRewrite($output);
doAction('before_output',$output);
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