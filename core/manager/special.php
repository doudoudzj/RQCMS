<?php
if(empty($action)) $action='list';
$sitedb=array();
$hostArr['name']=$hostArr['host']=$hostArr['host2']=$hostArr['hid']='';
$hostArr['url_ext']='php';
$files=array('index','category','article','attachment','search','tag','admin','rss');
foreach($files as $f) $info[$f]='';
foreach($files as $f) $args[$f]='';
$hid=isset($_GET['hid'])?intval($_GET['hid']):'';

if(RQ_POST)
{
	if($action=='add'||$action=='edit')
	{	
		if(!$hid) $hid=isset($_POST['setting']['hid'])?intval($_POST['setting']['hid']):'';
		$hidadd=!$hid?'':'&action=edit&hid='.$hid;
		$test=array();
		foreach($files as $k)
		{
			$postvalue=$_POST['filemap'][$k];
			if(!$postvalue) redirect($k.'参数不能为空',$admin_url.'?file=special'.$hidadd);
			if(!preg_match("/^\w*?$/i",$postvalue)) redirect($k.'参数不符合条件，只能是字母或数字',$admin_url.'?file=special'.$hidadd);
			if(isset($test[$postvalue])) redirect($k.'参数和参数'.$test[$postvalue].'新文件名重复，请修改',$admin_url.'?file=special'.$hidadd);
			$test[$postvalue]=$k;
		}
		$url_ext=$_POST['setting']['url_ext'];
		if($url_ext&&!preg_match("/^\w*?$/i",$url_ext)) redirect('文件后缀只能是字母或数字',$admin_url.'?file=special'.$hidadd);
	}
	switch($action)
	{
		case 'add':
			if(!isset($_POST['setting'])) redirect('Setting参数不足',$admin_url.'?file=special');
			$hostname=$_POST['setting']['name'];
			$hosturl=$_POST['setting']['host'];
			$host2=$_POST['setting']['host2'];
			$url_ext=$_POST['setting']['url_ext'];
			//先检查网址是否存在
			$exsits=$DB->fetch_first("Select * from rqcms_host where host='$hosturl'");
			if($exsits) redirect('该站点已经存在',$admin_url.'?file=special');

			$sql="INSERT INTO `rqcms_host` (`name`, `host`,`host2`, `gzipcompress`, `theme`, `keywords`, `description`, `icp`, `list_shownum`,`tags_shownum`, `related_shownum`,`allow_search_content`, `search_post_space`, `search_keywords_min_len`,`attach_save_dir`, `attach_thumbs`, `attach_display`, `attach_thumbs_size`, `attachments_remote_open`, `rss_enable`, `rss_num`,`status`,`url_ext`,`search_field_allow`) VALUES 
('$hostname', '$hosturl', '$host2',0, 'default','CMS,RQCMS', '又一个RQCMS', '1234567890', 10, 10, 10, 1, 10, 2, 2, 0, 2, '200x200', 1, 1, 20,1,'$url_ext','tag,keywords,title,excerpt');";

			$DB->query($sql);
			$insertid=$DB->insert_id();
		
			$sqlfile=RQ_CORE.'/resource/install.sql';
			$sql=file_get_contents($sqlfile);
			$tablenum=0;
			runquery($sql,"rqcms_{$insertid}_");
			echo "成功创建{$tablenum}个表<br />";
			
			foreach($files as $k)
			{
				$filename=$_POST['filemap'][$k];
				$DB->query("Insert into rqcms_{$insertid}_filemap (`original`,`filename`) values ('$k','$filename')");
			}
			
			host_recache();
			setting_recache($insertid);
			stick_recache($insertid);
			category_recache($insertid);
			latest_recache($insertid);
			hot_recache($insertid);
			search_recache($insertid);
			redirect('新站点添加成功',$admin_url.'?file=special');
		break;
		case 'edit':
			if(!$hid) redirect('缺少站点Id参数',$admin_url.'?file=special');
			if(!isset($_POST['setting'])) redirect('Setting参数不足',$admin_url.'?file=special');
			$result=$DB->fetch_first("Select * from rqcms_host where hid=$hid");
			if(!$result) redirect('不存在的站点',$admin_url.'?file=special');
			$hostname=$_POST['setting']['name'];
			$hosturl=$_POST['setting']['host'];
			$host2=$_POST['setting']['host2'];
			$url_ext=$_POST['setting']['url_ext'];
			$DB->query("update rqcms_host set `host`='$hosturl',`host2`='$host2',`name`='$hostname',`url_ext`='$url_ext' where hid=$hid");
			foreach($files as $k)
			{
				$filename=$_POST['filemap'][$k];
				$DB->query('Update rqcms_'.$hid."_filemap set `filename`='$filename' where `original`='$k'");
			}
			host_recache();
			setting_recache($hid);
			latest_recache($hid);
			search_recache($hid);
			stick_recache($hid);
			hot_recache($hid);
			if($hostid==$hid)
			{
				$host['url_ext']=$url_ext;
				$setting=@include RQ_DATA.'/cache/setting_'.$hostid.'.php';
			}
			redirect('站点更新成功',mkUrl('admin','').'?file=special');
		break;
	}
}
else
{
	//先加载所有站点信息
	switch($action)
	{
		case "cacheall";
			$hquery=$DB->query('select * from rqcms_host');
			while($hostinfo=$DB->fetch_array($hquery))
			{
				$hid=$hostinfo['hid'];
				setting_recache($hid);
				latest_recache($hid);
				category_recache($hid);
				search_recache($hid);
				stick_recache($hid);
				hot_recache($hid);
			}
			redirect('更新所有站点缓存成功',mkUrl('admin','').'?file=special');
			break;
		case 'list':
			$query=$DB->query('Select * from rqcms_host');
			while($res=$DB->fetch_array($query))
			{
				if($res['status']) $res['status']='正常';
				else $res['status']='关闭';
				$sitedb[]=$res;
			}
			break;		
		case 'edit':
			$hostArr==array();
			if(!$hid) redirct('缺少站点Id参数');
			$hostArr=$DB->fetch_first('Select * from rqcms_host where hid='.$hid);
			if(empty($hostArr)) redirct('不存在的站点id');
			$query=$DB->query('Select * from '.DB_PREFIX.'filemap');
			while($fname=$DB->fetch_array($query))
			{
				$info[$fname['original']]=$fname['filename'];
			}
			break;
		case 'bakup':
			break;
		case 'add':
			foreach($files as $f) $info[$f]=$f;
			break;
		case 'go':
			if(!$hid) redirct('缺少站点Id参数');
			$nsessionid=urlencode($sessionid);
			$host=$DB->fetch_first('Select * from rqcms_host where hid='.$hid);
			$rhost=$host['host'];
			$filemapArr=getFiles($hid);
			$admin_url=mkUrl('admin','');
			if($host)
			{
				redirect('正在转向转站点'.$host['name'],RQ_HTTP."{$rhost}{$admin_url}?sessionid={$nsessionid}");
				break;
			}
	}
}

function getFiles($hostid)
{
	global $DB,$host;
	$files= $DB->query('SELECT * FROM `rqcms_'.$hostid.'_filemap`');
	$arrfiles=array();
	while ($fs = $DB->fetch_array($files)) 
	{
		$args=array();
		$arrfiles[$fs['filename']]=$fs['original'];
	}
	return $arrfiles;
}

?>