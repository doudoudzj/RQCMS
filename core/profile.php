<?php
if(!defined('RQ_ROOT')) exit('Access Denied');

$action=isset($_GET['action'])?$_GET['action']:(isset($_POST['action'])?$_POST['action']:'');
$loginurl='profile.php?action=login';
$regurl='profile.php?action=register';

if(RQ_POST)
{
	if($action == 'register' || $action == 'modpro')
	{
		$doreg = $action == 'register' ? true : false;
		$username        = trim($_POST['username']);
		$password        = $_POST['password'];
		$confirmpassword = $_POST['confirmpassword'];
		$url             = trim($_POST['url']);
		$result = checkurl($url);
		if($result)
		{
			message($result);
		}
		if ($doreg) 
		{
			//注册
			if ($options['seccode_enable']) 
			{
				$clientcode = $_POST['clientcode'];
				session_start();
				$tc=strtolower($_SESSION['code']);
				$truecode=$tc{3}.$tc{2}.$tc{1};
				if (!$clientcode || strtolower($clientcode) != $truecode) 
				{
					unset($_SESSION['code']);
					message('验证码错误,请返回重新输入.', $regurl);
				}
			}

			if(!$username || strlen($username) > 30) 
			{
				message('用户名为空或者超过30字节.', $regurl);
			}

			if ($options['censoruser'])
			{
				$options['censoruser'] = str_replace('，', ',', $options['censoruser']);
				$banname = explode(',',$options['censoruser']);
				foreach($banname as $value)
				{
					if (strpos($username,$value) !== false)
					{
						message('此用户名包含不可接受字符或被管理员屏蔽,请选择其它用户名.', $regurl);
					}
				}
			}

			$name_key = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#','$','(',')','%','@','+','?',';','^');
			foreach($name_key as $value)
			{
				if (strpos($username,$value) !== false)
				{
					message('此用户名包含不可接受字符或被管理员屏蔽,请选择其它用户名.', $regurl);
				}
			}

			if (!$password || strlen($password) < 3) 
			{
				message('密码不能为空并且密码长度不能小于3位.',$regurl);
			}
			if ($password != $confirmpassword)
			{
				message('请确认输入的密码一致.', $regurl);
			}
			if (strpos($newpassword,"\n") !== false || strpos($password,"\r") !== false || strpos($password,"\t") !== false)
			{
				message('密码包含不可接受字符.', $regurl);
			}
			$username = char_cv($username);
			$r = $DB->fetch_first("SELECT userid FROM ".DB_PREFIX."users WHERE username='$username'");
			if($r['userid']) 
			{
				message('该用户名已被注册,请返回重新选择其他用户名.', $regurl);
				unset($r);
			}
			$url = char_cv($url);
			if ($url && isemail($url))
			{
				$r = $DB->fetch_first("SELECT userid FROM ".DB_PREFIX."users WHERE url='$url'");
				if($r['userid']) 
				{
					message('该E-mail已被注册.', $regurl);
				}
				unset($r);
			}

			$password = md5($password);

			$DB->query("INSERT INTO ".DB_PREFIX."users (username, password, logincount, loginip, logintime, url, regdateline, regip, groupid) VALUES ('$username', '$password', '1', '$onlineip', '$timestamp', '$url', '$timestamp', '$onlineip', '3')");
			$userid = $DB->insert_id();
			$DB->unbuffered_query("UPDATE ".DB_PREFIX."statistics SET user_count=user_count+1");
			setcookie('sax_auth', authcode("$userid\t$password\t1"), $timestamp+2592000);
			require_once(SACMS_ROOT.'include/cache.php');
			statistics_recache();
			message('注册成功.', './');
		}
		else
		{
			//修改资料
			$password_sql = '';
			$oldpassword = md5($_POST['oldpassword']);
			$newpassword = $_POST['newpassword'];
			if ($newpassword)
			{
				$user = $DB->fetch_first("SELECT password FROM ".DB_PREFIX."users WHERE userid='$sax_uid'");
				if (!$user) {
					message('出错,请尝试重新登陆再进行此操作',$loginurl);
				}
				if ($oldpassword != $user['password']) {
					message('密码无效');
				}
				if(strlen($newpassword) < 8) {
					message('新密码长度不能小于8位');
				}
				if ($newpassword != $confirmpassword) {
					message('请确认输入的新密码一致');
				}
				if (strpos($newpassword,"\n") !== false || strpos($newpassword,"\r") !== false || strpos($newpassword,"\t") !== false) {
					message('密码包含不可接受字符');
				}
				$password_sql = ", password='".md5($newpassword)."'";
			}
			$url = char_cv($url);
			if ($url && isemail($url)) {
				$r = $DB->fetch_first("SELECT userid FROM ".DB_PREFIX."users WHERE url='$url' AND userid!='$sax_uid'");
				if($r['userid']) {
					message('该E-mail已被注册');
				}
				unset($r);
			}
			$DB->unbuffered_query("UPDATE ".DB_PREFIX."users SET url='$url' $password_sql WHERE userid='$sax_uid'");
			if ($newpassword) {
				setcookie('sax_auth', '');
				setcookie('comment_post_time', '');
				setcookie('search_post_time', '');
				setcookie('comment_username', '');
				setcookie('comment_url', '');
				message('资料已修改成功,您修改了密码,需要重新登陆.', $loginurl);
			} else {
				message('资料已修改成功.', 'profile.php');
			}
		}
	}
	elseif($action=='dologin')
	{
		if ($options['seccode_enable']) 
		{
			$clientcode = $_POST['clientcode'];
			session_start();
			$tc= strtolower($_SESSION['code']);
			$truecode=$tc{0}.$tc{2}.$tc{3};
			if (!$clientcode || strtolower($clientcode) !=$truecode) {
				unset($_SESSION['code']);
				message('验证码错误,请返回重新输入.', $loginurl);
			}
		}
	// 取值并过滤部分
	$username = char_cv(trim($_POST['username']));
	$password = md5($_POST['password']);
	$userinfo = $DB->fetch_first("SELECT userid,password,logincount,url,groupid FROM ".DB_PREFIX."users WHERE username='$username'");
	if($userinfo['userid'] && $userinfo['password'] == $password) 
		{
		$DB->unbuffered_query("UPDATE ".DB_PREFIX."users SET logincount=logincount+1, logintime='$timestamp', loginip='$onlineip' WHERE userid='".$userinfo['userid']."'");
		$logincount = $userinfo['logincount']+1;
		setcookie('sax_auth', authcode("$userinfo[userid]\t$password\t$logincount"), $timestamp+2592000);
		message('登陆成功', './');
		} else {
		message('登陆失败', $loginurl);
		}
	}
}
else
{
	switch($action)
	{
		case 'clearcookies':
			if(is_array($_COOKIE)) 
			{
				foreach ($_COOKIE as $key => $val) 
				{
					setcookie($key, '');
				}
			}
		message('清除COOKIE成功', './');
		break;
		case 'logout':
			$adminitem=array();
			$groupid=0;
			setcookie('sessionid',null);
			$sessionid=getRandStr(10);
			$DB->query('update '.DB_PREFIX."user set `sessionid`='$sessionid' where uid='$uid'");
			ob_end_clean();
			ob_start();
			message('注销成功', './');
		break;
		case 'login':
			$pagefile = 'login';
			$title='登陆';
			break;
		case 'register':
			$pagefile='reg';
			$title='注册用户';
			break;
			case 'edit':
				$pagefile = 'edit';
				$title='编辑个人信息';
		break;
		default:
		$title='用户中心';
	}
}