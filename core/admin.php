<?php

//权限验证
$username='';
$groupid=$uid=0;
$sessionid=isset($_COOKIE['sessionid'])?$_COOKIE['sessionid']:'';
if(isset($_GET['sessionid'])) $sessionid=$_GET['sessionid'];//在多站点切换时,使用这个来保持登陆状态
if(!empty($sessionid)&&strlen($sessionid)==30)
{
	$userinfo=$DB->fetch_first("Select * from rqcms_admin where `sessionid`='$sessionid'");//创始人可以登陆站点,其他人受限
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
			}
			else 
				setcookie('sessionid',$sessionid);
		}
	}
}

$tempView=$coreView;//不用再去加载模板了
$coredir=basename(RQ_CORE);//core目录
$datadir=basename(RQ_DATA);//data目录

$css_url =$admin_url.'?file=css';//管理后台的css文件
$viewdir=$coredir.'/manager/view/';
$incfile=!empty($_GET['file'])?$_GET['file']:'main';
$do=isset($_POST['do'])?$_POST['do']:'';
if(!$do) $do=isset($_GET['do'])?$_GET['do']:'';
$action=!empty($_GET['action'])?$_GET['action']:(!empty($_POST['action'])?$_POST['action']:'');
$cssdir='/'.$coredir.'/manager/view/images/';
$editordir='/'.$coredir.'/manager/editor/';

$page=isset($_GET['page'])?intval($_GET['page']):'';
if($incfile!='css'&&$groupid<2)  $incfile='login';

//加载一些类
include RQ_CORE.'/library/func.image.php';
include RQ_CORE.'/library/func.admin.php';
// 操作提示页面

if(!function_exists('redirect'))
{
function redirect($msg, $url = 'javascript:history.go(-1);', $min='2')
{
	global $cssdir,$css_url;
	ob_end_clean();
	ob_start();
	include RQ_CORE.'/manager/view/redirect.php';
	$output=ob_get_contents();
	@ob_end_clean();
	exit($output);
}
}

$adminitem=array();
switch($groupid)
{
	case 2:
		$adminitem = array(
		'article' => '文章管理',
	);
	break;
	case 3:
		$adminitem = array(
		'configurate' => '系统设置',
		'article' => '文章管理',
		'attachment' => '附件管理',
		'category' => '分类管理',
		'user' => '用户管理',
		'template' => '模板管理',
		'link' => '友情链接',
		'seo'=>'网站优化',
		'maintenance' => '系统维护',
	);
	break;
	case 4:
		$adminitem = array(
		'configurate' => '系统设置',
		'article' => '文章管理',
		'attachment' => '附件管理',
		'category' => '分类管理',
		'user' => '用户管理',
		'template' => '模板管理',
		'link' => '友情链接',
		'seo'=>'网站优化',
		'plugin'=>'插件管理',
		'maintenance' => '系统维护' //这里要添加缓存更新和日志管理功能
		);
	break;
}

$other=array('css','login','special','main','xmlrpc','database','upload');

doAction('change_admin_item');

if(!in_array($incfile,$other)&&!array_key_exists($incfile,$adminitem)) redirect('未定义操作',$admin_url.'?file=main');

if(isset($_GET['indexcache']))
{
	stick_recache();
	latest_recache();
	exit('cache sucess');
}

if($groupid!=4&&$incfile=='special') redirect('您无权限访问多站点设置',$admin_url.'?file=main');
if($groupid<3&&$incfile=='tag') redirect('您无权限编辑tag设置',$admin_url.'?file=main');
if($groupid!=4&&$incfile=='database') redirect('您无权限操作数据库设置',$admin_url.'?file=main');

$onlines=array();//在线后台用户
if($incfile!='css') include RQ_CORE.'/manager/view/header.php';
include RQ_CORE.'/manager/'.$incfile.'.php';
include RQ_CORE.'/manager/view/'.$incfile.'.php';
if($incfile!='css') include RQ_CORE.'/manager/view/footer.php';
