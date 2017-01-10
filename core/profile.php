<?php
if(!defined('RQ_ROOT')) exit('Access Denied');

$url=isset($_GET['url'])?$_GET['url']:(isset($_POST['url'])?$_POST['url']:'');

if(RQ_POST)
{
	if($url == 'doregister' || $url == 'domod')
	{
		$doreg = $url == 'register' ? true : false;
		$confirmpassword = $_POST['confirmpassword'];
		if ($doreg) 
		{
			$username        =trim($_POST['username']);
	     	$password        = $_POST['password'];
			doAction('profile_reg_check');
			//注册
			if ($options['seccode_enable']) 
			{
				$clientcode = $_POST['clientcode'];
				session_start();
				$truecode=strtolower($_SESSION['code']);
				if (strtolower($clientcode) != $truecode) 
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
			$r = $DB->fetch_first("SELECT uid FROM ".DB_PREFIX."user WHERE username='$username'");
			if($r['uid']) 
			{
				message('该用户名已被注册,请返回重新选择其他用户名.', $regurl);
				unset($r);
			}
			$url = char_cv($url);
			if ($url && isemail($url))
			{
				$r = $DB->fetch_first("SELECT uid FROM ".DB_PREFIX."user WHERE url='$url'");
				if($r['uid']) 
				{
					message('该E-mail已被注册.', $regurl);
				}
				unset($r);
			}

			$password = md5($password);

			$DB->query("INSERT INTO ".DB_PREFIX."user (username, password, logincount, loginip, logintime, url, regdateline, regip, groupid) VALUES ('$username', '$password', '1', '$onlineip', '$timestamp', '$url', '$timestamp', '$onlineip', '3')");
			$uid = $DB->insert_id();
			//自动登录
			message('注册成功.', './');
		}
		else
		{
			//修改资料
			$password_sql = '';
			$oldpassword = md5($_POST['oldpassword']);
			$newpassword = $_POST['newpassword'];
			
			$email=$_POST['email'];
			if($email)
			{
				$password_sql = "email='$email'";
			}
			
			if ($newpassword)
			{
				$user = $DB->fetch_first("SELECT password FROM ".DB_PREFIX."user WHERE uid='$uid'");
				if (!$user) {
					message('出错,请尝试重新登陆再进行此操作',$loginurl);
				}
				if ($oldpassword != $user['password']) {
					message('密码无效',$profile_url);
				}
				if(strlen($newpassword) < 8) {
					message('新密码长度不能小于8位',$profile_url);
				}
				if ($newpassword != $confirmpassword) {
					message('请确认输入的新密码一致',$profile_url);
				}
				if (strpos($newpassword,"\n") !== false || strpos($newpassword,"\r") !== false || strpos($newpassword,"\t") !== false) {
					message('密码包含不可接受字符',$profile_url);
				}
				if($password_sql) $password_sql = ", password='".md5($newpassword)."'";
				else $password_sql = "password='".md5($newpassword)."'";
			}
			if($password_sql) $DB->unbuffered_query("UPDATE ".DB_PREFIX."user SET $password_sql WHERE uid='$uid'");
			if ($newpassword)
			{
				setcookie('sessionid',null);
				setcookie('comment_post_time', '');
				setcookie('search_post_time', '');
				setcookie('comment_username', '');
				setcookie('comment_url', '');
				message('资料已修改成功,您修改了密码,需要重新登陆.', $login_url);
			} else {
				message('资料已修改成功.', 'profile.php');
			}
		}
	}
	else if($url=='dologin')
	{
		// 取值并过滤部分
		$username = trim($_POST['username']);
		$password = md5($_POST['password']);
		$userinfo = $DB->fetch_first("SELECT * FROM ".DB_PREFIX."user WHERE username='$username'");
		
		if($userinfo)
		{
			if($userinfo['password']==md5($password))
			{
				$uid=$userinfo['uid'];
				if($userinfo['groupid']<3&&$userinfo['hostid']!=$hostid) $loginerr='不存在的用户名';
				elseif($userinfo['groupid']>2) $loginerr='您是管理员，需要从网站后台登录';//不是创始人,只能登陆一个站点
				else
				{
					$sessionid=getRandStr(30,true);//生成那个登陆信息
					$expire=$timestamp+31536000;//过期时间设置，记住我为最长时间，否则为浏览器关闭则无效
					setcookie('sessionid',$sessionid,$expire,'', RQ_ISIE?'':'.'.$host['host']);//使用泛域名解析后，需要设置.rq.cn这样的cookie的域
					$DB->query('update '.DB_PREFIX."user set `logincount`=`logincount`+1,`loginip`='$onlineip',`logintime`='$timestamp',`sessionid`='$sessionid',`useragent`='$useragent' where uid='$uid'");
					$DB->query('insert into '.DB_PREFIX."log (`user`,`dateline`,`type`,`useragent`,`ip`,`content`) values ('$lusername','$timestamp','l','$useragent','$onlineip','成功')");
					redirect('登陆成功', $profile_url);
				}
			}
			else $loginerr='密码错误';
		}
		else $loginerr='不存在的用户名';
		message($loginerr,$login_url);
	}
}
else
{
	switch($url)
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
			$pagefile='register';
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