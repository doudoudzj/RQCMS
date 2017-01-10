<?php
$settingsmenu = array(
	'basic' => '基本设置',
	'display' => '显示设置',
	'search' => '搜索设置',
	'attach' => '附件设置',
	'rss' => 'RSS订阅',
);
$type=isset($_GET['type'])?$_GET['type']:(isset($_POST['type'])?$_POST['type']:'');
if(RQ_POST&&isset($_POST['action'])&&$_POST['action'] == 'updatesetting')
{
	if(isset($_POST['setting']['search_field_allow'])&&!$_POST['setting']['search_field_allow']) redirect('搜索字段不得为空', $admin_url.'?file=configurate&type='.$type);
	if(isset($_POST['host'])) unset($_POST['host']);
	$sql='Update rqcms_host set ';
	foreach($_POST['setting'] AS $key => $val)
	{
		$sql.="`$key`='$val',";
	}
	$sql=substr($sql,0,strlen($sql)-1);
	$sql.=' where `hid`='.$hostid;
	$DB->query($sql);
	host_recache();
	redirect('更新系统配置成功', $admin_url.'?file=configurate&type='.$type);
}
else
{
	$query=$DB->query("Select * from `rqcms_host` where `hid`='$hostid'");
	$settings=$DB->fetch_array($query);
	
	//基本设置
	ifselected('close');
	ifselected('gzipcompress');

	
	//评论设置
	ifselected('guest_comment');
	ifselected('audit_comment');
	ifselected('comment_order');
	
	//搜索设置
	ifselected('allow_search_content');
	
	//附件设置
	$attach_save_dir[0]=$attach_save_dir[1]=$attach_save_dir[2]=$attach_save_dir[3]='';
	$attach_save_dir[$settings['attach_save_dir']]='selected';
	$attach_display[0]=$attach_display[1]=$attach_display[2]='';
	$attach_display[$settings['attach_display']]='selected';
	ifselected('attach_thumbs');
	ifselected('attach_remote_open');
	
	//WAP设置
	ifselected('wap_enable');

	//RSS订阅设置
	ifselected('rss_enable');
	
	//是否远程查看附件
	ifselected('attachments_remote_open');
}

function ifselected($varArr) {
	global $settings,${$varArr.'_Y'},${$varArr.'_N'};
	if(isset($settings[$varArr])&&$settings[$varArr]) {
		${$varArr.'_Y'} = 'selected';
	} else {
		${$varArr.'_N'} = 'selected';
	}
}
?>