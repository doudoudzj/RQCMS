<?phpif(!defined('RQ_ROOT')) exit('Access Denied');
if(empty($action)) $action = 'list';
$groupdb=array(4=>'创始人',3=>'管理员',2=>'编辑',1=>'注册会员',0=>'游客');
if(RQ_POST){	// //添加用户	if($action == 'adduser')	{		$username       = trim($_POST['username']);		$newpassword    = trim($_POST['newpassword']);		$comfirpassword = trim($_POST['comfirpassword']);		$url            = trim($_POST['url']);		$groupid        = intval($_POST['groupid']);		$email =$_POST['email'];		$qq=$_POST['qq'];		$msn=$_POST['msn'];		if (!$username || strlen($username) > 20) {			redirect('登陆名不能为空并且不能超过20个字符');		}		$name_key = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#','$','(',')','%','@','+','?',';','^');		foreach($name_key as $value){			if (strpos($username,$value) !== false){				redirect('用户名包含敏感字符');			}		}		if ($newpassword == '') {			redirect('密码不能为空并且密码长度不能小于8位');		}		if ($newpassword != $comfirpassword) {			redirect('请确认输入的密码一致');		}		if (strpos($newpassword,"\n") !== false || strpos($password,"\r") !== false || strpos($password,"\t") !== false) {			redirect('密码包含不可接受字符.');		}		$url = char_cv($url);		if ($url)		{			if (!preg_match("#^(http|news|https|ftp|ed2k|rtsp|mms)://#", $url)) {					redirect('网站URL错误');				}				$key = array("\\",' ',"'",'"','*',',','<','>',"\r","\t","\n",'(',')','+',';');				foreach($key as $value){					if (strpos($url,$value) !== false){ 						redirect('网站URL错误');					}				}		}		if ($email)		{			$r = $DB->fetch_first("SELECT uid FROM ".DB_PREFIX."user WHERE email='$email'");			if($r['uid']) {				redirect('该E-mail已被注册');			}		}		if($msn)		{			$r = $DB->fetch_first("SELECT uid FROM ".DB_PREFIX."user WHERE msn='$msn'");				if($r['uid']) {					redirect('该Msn已被注册');				}		}		if($qq)		{			$r = $DB->fetch_first("SELECT uid FROM ".DB_PREFIX."user WHERE qq='$qq'");				if($r['uid']) {					redirect('该QQ已被注册');				}		}		$username    = char_cv($username);		$newpassword = md5($newpassword);		$query = $DB->query("SELECT uid FROM ".DB_PREFIX."user WHERE username='$username'");		if($DB->num_rows($query)) {			redirect('该用户名已被注册');		}		$DB->query("INSERT INTO ".DB_PREFIX."user (username, password, url, regdateline, regip, groupid,hostid) VALUES ('$username', '$newpassword', '$url', '$timestamp', '$onlineip', '$groupid','$hostid')");		redirect('添加新用户成功', 'admin.php?file=user&action=list');	}// //修改用户// if($action == 'moduser') {		// $username       = trim($_POST['username']);	// $newpassword    = trim($_POST['newpassword']);	// $comfirpassword = trim($_POST['comfirpassword']);	// $url            = trim($_POST['url']);	// $groupid        = intval($_POST['groupid']);	// $userid         = intval($_POST['userid']);	// if (!$username || strlen($username) > 20) {		// redirect('登陆名不能为空并且不能超过20个字符');    // }	// $password_sql = '';	// if ($newpassword) {		// if(strlen($newpassword) < 8) {			// redirect('新密码长度不能小于8位');		// }		// if ($newpassword != $comfirpassword) {			// redirect('请确认输入的新密码一致');		// }		// if (strpos($newpassword,"\n") !== false || strpos($password,"\r") !== false || strpos($password,"\t") !== false) {			// redirect('密码包含不可接受字符');		// }		// $password_sql = ", password='".md5($newpassword)."'";	// }	// $name_key = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#','$','(',')','%','@','+','?',';','^');	// foreach($name_key as $value){		// if (strpos($username,$value) !== false){			// redirect('用户名包含敏感字符');		// }	// }	// $url = char_cv($url);	// if ($url) {		// if (isemail($url)) {			// $r = $DB->fetch_first("SELECT userid FROM ".DB_PREFIX."users WHERE url='$url' AND userid!='$userid'");			// if($r['userid']) {				// redirect('该E-mail已被注册');			// }			// unset($r);		// } else {						// if (!preg_match("#^(http|news|https|ftp|ed2k|rtsp|mms)://#", $url)) {				// redirect('网站URL错误');			// }			// $key = array("\\",' ',"'",'"','*',',','<','>',"\r","\t","\n",'(',')','+',';');			// foreach($key as $value){				// if (strpos($url,$value) !== false){ 					// redirect('网站URL错误');				// }			// }		// }	// }	// $username = char_cv($username);    // $r = $DB->fetch_first("SELECT userid FROM ".DB_PREFIX."users WHERE username='$username' AND userid!='$userid'");    // if($r) {		// redirect('该用户名已被注册');    // }	// $usernamesql = $username ? "username='$username'," : '';    // $DB->unbuffered_query("UPDATE ".DB_PREFIX."users SET $usernamesql url='$url', groupid='$groupid' $password_sql WHERE userid='$userid'");    // redirect('用户修改成功','admin.php?file=user&action=mod&userid='.$userid);// }// //删除用户// if($action == 'delusers') {		// if ($uids = implode_ids($_POST['user'])) {		// $user_count = count($_POST['user']);			// if ($_POST['deluserarticle']) {			// $aids = $a_tatol = 0;			// // 删除该用户发表的文章以及相关数据			// require_once(SACMS_ROOT.'include/func_attachment.php');			// $query = $DB->query("SELECT articleid,keywords,visible,cid FROM ".DB_PREFIX."articles WHERE uid IN ($uids)");			// while ($article = $DB->fetch_array($query)) {				// if ($article['keywords']) {					// updatetags($article['articleid'], '', $article['keywords']);				// }				// if ($article['visible']) {					// $a_tatol++;					// $DB->unbuffered_query("UPDATE ".DB_PREFIX."categories SET articles=articles-1 WHERE cid='".$article['cid']."'");				// }				// $aids .= ','.$article['articleid'];			// }//end while						// // 删除该用户的文章中的附件			// $query  = $DB->query("SELECT attachmentid,filepath,thumb_filepath FROM ".DB_PREFIX."attachments WHERE articleid IN ($aids)");			// $nokeep = array();			// while($attach = $DB->fetch_array($query)) {				// $nokeep[$attach['attachmentid']] = $attach;			// }			// removeattachment($nokeep);			// $DB->unbuffered_query("DELETE FROM ".DB_PREFIX."comments WHERE articleid IN ($aids)");			// $DB->unbuffered_query("DELETE FROM ".DB_PREFIX."trackbacks WHERE articleid IN ($aids)");			// $DB->unbuffered_query("DELETE FROM ".DB_PREFIX."trackbacklog WHERE articleid IN ($aids)");			// $DB->unbuffered_query("DELETE FROM ".DB_PREFIX."articles WHERE uid IN ($uids)");			// $DB->unbuffered_query("UPDATE ".DB_PREFIX."statistics SET article_count=article_count-".$a_tatol);		// }		// // 删除用户		// $DB->unbuffered_query("DELETE FROM ".DB_PREFIX."users WHERE userid IN ($uids)");		// $DB->unbuffered_query("UPDATE ".DB_PREFIX."statistics SET user_count=user_count-".$user_count);		// archives_recache();		// categories_recache();		// statistics_recache();		// redirect('删除用户成功', 'admin.php?file=user&action=list');	// } else {				// redirect('未选择任何用户');	// }// }}else{	$groupid        = isset($_GET['groupid'])?$_GET['groupid']:'';	$groupselect[2]=$groupselect[3]=$groupselect[4]='';	if ($action == 'add')	{		$info['username']=$info['uid']=$info['url']=$info['qq']=$info['email']=$info['msn']='';		$nav='添加用户';		$do = 'adduser';		$groupselect[4] = 'selected';	} 	elseif($action=='mod')	{		$nav='编辑用户';		$userid = intval($_GET['userid']);		$do = 'moduser';		$info = $DB->fetch_first("SELECT * FROM ".DB_PREFIX."user WHERE uid='$userid'");		$groupselect[$info['groupid']] = 'selected';	}
	elseif($action == 'list') 	{
		if($page) {
			$start_limit = ($page - 1) * 30;
		} else {
			$start_limit = 0;
			$page = 1;
		}
		$sqladd = " WHERE hostid='$hostid' ";
		$pagelink = '';
		//察看是否发表过评论
		$lastpost = (!isset($_GET['lastpost']))?'':$_GET['lastpost'] ;
		if ($lastpost == 'already') {
			$sqladd .= " AND lastpost <> '0'";
			$pagelink .= '&lastpost=already';
			$subnav = '发表过评论的用户';
		}
		elseif ($lastpost == 'never') {
			$sqladd .= " AND lastpost='0'";
			$pagelink .= '&lastpost=never';
			$subnav = '从未发表过评论的用户';
		}
		//察看用户组
		if ($groupid && in_array($groupid,array_flip($groupdb))) {
			$sqladd .= " AND groupid='$groupid'";
			$pagelink .= '&groupid='.$groupid;
			$subnav = $groupdb[$groupid].'的用户';
		}
		//察看IP段
		$ip =isset($_GET['ip'])? char_cv($_GET['ip']):'';
		if ($ip)		{
			$frontlen = strrpos($ip, '.');
			$ipc = substr($ip, 0, $frontlen);
			$sqladd .= " AND (loginip LIKE '%".$ipc."%')";
			$pagelink .= '&ip='.$ip;
			$subnav  = '上次登陆IP为['.$ip.']同一C段的相关用户';
		}
		//搜索用户
		$srhname =isset($_GET['srhname'])?( char_cv($_GET['srhname'] ? $_GET['srhname'] : $_POST['srhname'])):'';
		if ($srhname) {
			$sqladd .= " AND (BINARY username LIKE '%".str_replace('_', '\_', $srhname)."%' OR username='$srhname')";
			$pagelink .= '&srhname='.$srhname;
		}

		//排序
		$order =isset($_GET['order'])? $_GET['order']:'';
		if ($order && in_array($order,array('username','logincount','regdateline'))) {
			$orderby = $order;
			$orderdb = array('username'=>'用户名','logincount'=>'登陆次数','regdateline'=>'注册时间');
			$subnav = '以'.$orderdb[$order].'降序察看全部用户';
			$pagelink .= '&order='.$order;
		} else {
			$orderby = 'uid';
		}
		$tatol     = $DB->num_rows($DB->query("SELECT uid FROM ".DB_PREFIX."user ".$sqladd));
		$multipage = multi($tatol, 30, $page, 'admin.php?file=user&action=list'.$pagelink);
		$query = $DB->query("SELECT * FROM ".DB_PREFIX."user $sqladd ORDER BY $orderby DESC LIMIT $start_limit, 30");
		$userdb = array();
		while ($user = $DB->fetch_array($query))		{
			$user['lastpost']    = $user['lastpost'] ? date('Y-m-d H:i',$user['lastpost']) : '从未发表';
			$user['regdateline'] = date('Y-m-d',$user['regdateline']);
			$user['url']         = $user['url'] ? '<a href="'.$user['url'].'" target="_blank">'.$user['url'].'</a>': '<font color="#FF0000">Null</font>';			$user['email']=$user['email']? '<a href="mailto:'.$user['email'].'" target="_blank">'.$user['email'].'</a>' : '<font color="#FF0000">Null</font>';
			$user['logintime'] = $user['logintime'] ? date('Y-m-d H:i',$user['logintime']) : '从未登陆';
			$user['loginip']   = $user['loginip'] ? $user['loginip'] : '从未登陆';
			$user['group'] = $groupdb[$user['groupid']];
			$user['disabled'] = $user['groupid'] == 4 ? 'disabled' : '';
			$userdb[] = $user;
		}
		unset($user);
		$DB->free_result($query);
	} //end list}