<?php
if(empty($action)) $action='list';
$sitedb=array();
$setting['name']=$setting['host']=$setting['hid']='';
$files=array('index.php','category.php','article.php','attachment.php','search.php','comment.php','tag.php','profile.php','admin.php','captcha.php','install.php','rss.php');
foreach($files as $f) $info[$f]=$f;
foreach($files as $f) $args[$f]='';
$hid=isset($_GET['hid'])?intval($_GET['hid']):'';

if(RQ_POST)
{
	switch($action)
	{
		case 'add':
			if(!isset($_POST['setting'])) redirect('Setting参数不足','admin.php?file=special');
			if(!isset($_POST['maps'])) redirect('maps参数不足','admin.php?file=special');
			$hostname=$_POST['setting']['name'];
			$hosturl=$_POST['setting']['host'];
			//先检查网址是否存在
			$exsits=$DB->fetch_first('Select * from '.DB_PREFIX."host where host='$hosturl'");
			if($exsits) redirect('该站点已经存在','admin.php?file=special');

			$sql="INSERT INTO `".DB_PREFIX."host` (`name`, `host`, `gzipcompress`, `theme`, `password`, `keywords`, `description`, `icp`, `close`, `close_note`, `list_shownum`, `article_order`, `title_limit`, `tags_shownum`, `related_shownum`, `related_title_limit`, `related_order`, `audit_comment`, `comment_order`, `article_comment_num`, `comment_min_len`, `comment_max_len`, `commentlist_num`, `comment_post_space`, `allow_search_content`, `search_post_space`, `search_keywords_min_len`, `attach_save_dir`, `attach_thumbs`, `attach_display`, `attach_thumbs_size`, `attachments_remote_open`, `watermark`, `watermark_size`, `watermark_pos`, `watermark_trans`, `watermark_padding`, `server_timezone`, `time_article_format`, `time_comment_format`, `closereg`, `censoruser`, `wap_enable`, `wap_article_pagenum`, `wap_article_title_limit`, `wap_tags_pagenum`, `wap_comment_pagenum`, `banip_enable`, `ban_ip`, `spam_enable`, `spam_words`, `spam_url_num`, `js_enable`, `js_cache_life`, `js_lock_url`, `rss_enable`, `rss_num`, `rss_ttl`,`status`,`friend_url`,`listcachenum`) VALUES ('$hostname', '$hosturl', 0, 'default', '', 'CMS,RQCMS', '又一个RQCMS', '1234567890', 0, '服务器检修中,稍后开放', 10, 'articleid', 0, 10, 10, 0, 'dateline', 1, 0, 10, 10, 3000, 20, 10, 1, 10, 2, 2, 0, 2, '200x200', 1, 0, 150, 4, 10, 5, '8', 'Y-m-d', 'Y-m-d', 0, 'admin', 0, 10, 0, 100, 20, 0, '', 0, '', 0, 0, 3600, '', 1, 20, 3600,1,'aid',20)";
			$DB->query($sql);
			$insertid=$DB->insert_id();
			foreach($files as $k)
			{
				$filename=$_POST['maps'][$k];
				$filemaps=$_POST['args'][$k];
				$DB->query('Insert into '.DB_PREFIX."filemap (`hostid`,`original`,`filename`,`maps`) values ('$insertid','$k','$filename','$filemaps')");
			}
			$hostid=$insertid;
			hosts_recache();
			filemaps_recache();
			plugins_recache();
			links_recache();
			rss_recache();
			latest_recache();
			cates_recache();
			vars_recache();
			stick_recache();
			comments_recache();
			pics_recache();
			redirect('新站点添加成功','admin.php?file=special');
		break;
		case 'edit':
			$hid=isset($_POST['setting']['hid'])?intval($_POST['setting']['hid']):'';
			if(!$hid) redirect('缺少站点Id参数','admin.php?file=special');
			if(!isset($_POST['setting'])) redirect('Setting参数不足','admin.php?file=special');
			if(!isset($_POST['maps'])) redirect('maps参数不足','admin.php?file=special');
			$result=$DB->fetch_first('Select * from '.DB_PREFIX."host where hid=$hid");
			if(!$result) redirect('不存在的站点','admin.php?file=special');
			$hostname=$_POST['setting']['name'];
			$hosturl=$_POST['setting']['host'];
			$DB->query('update '.DB_PREFIX."host set `host`='$hosturl',`name`='$hostname' where hid=$hid");
			foreach($files as $k)
			{
				$filename=$_POST['maps'][$k];
				$filemaps=$_POST['args'][$k];
				$DB->query('Update '.DB_PREFIX."filemap set `filename`='$filename',`maps`='$filemaps' where `original`='$k' and `hostid`='$hid'");
			}
			$hostid=$hid;
			hosts_recache();
			filemaps_recache();
			$Files= @include RQ_DATA.'/cache/map_'.$host['host'].'.php';
			redirect('站点更新成功','admin.php?file=special');
		break;
	}
}
else
{
	//先加载所有站点信息
	switch($action)
	{
		case "cacheall";
			$hostid=null;
			hosts_recache();
			cates_recache();
			filemaps_recache();
			plugins_recache();
			comments_recache();
			links_recache();
			rss_recache();
			stick_recache();
			vars_recache();
			pics_recache();
			latest_recache();
			redirect_recache();
			redirect('更新所有站点缓存成功','admin.php?file=special');
			break;
		case 'list':
		$query=$DB->query('Select * from '.DB_PREFIX.'host');
		while($res=$DB->fetch_array($query))
		{
			if($res['status']) $res['status']='正常';
			else $res['status']='关闭';
			$sitedb[]=$res;
		}
		break;		
		case 'edit':
		if(!$hid) redirct('缺少站点Id参数');
		$setting=$DB->fetch_first('Select * from '.DB_PREFIX.'host where hid='.$hid);
		$query=$DB->query('Select * from '.DB_PREFIX.'filemap where hostid='.$hid);
		while($fname=$DB->fetch_array($query))
		{
			$info[$fname['original']]=$fname['filename'];
			$args[$fname['original']]=$fname['maps'];
		}
		break;
		case 'bakup':
		break;
		case 'go':
		if(!$hid) redirct('缺少站点Id参数');
		$nsessionid=urlencode($sessionid);
		$info=$DB->fetch_first('Select * from '.DB_PREFIX.'host where hid='.$hid);
		$rhost=$info['host'];
		if($info)
		{	
			$admin='admin.php';
			foreach($Files as $file=>$args)
			{
				if(is_array($args)&&$args[0]=='admin.php')
				{
					$admin=$file;
					break;
				}
			}
			redirect('正在转向转站点'.$info['name'],RQ_HTTP."{$rhost}/{$admin}?sessionid={$nsessionid}");
			break;
		}
	}
}
?>