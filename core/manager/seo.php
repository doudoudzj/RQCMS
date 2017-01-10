<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
include RQ_CORE.'/include/tag.php';
if(RQ_POST)
{
	switch($action)
	{
		case 'domodtag':
			//修改Tag
			$newitem = $_POST['tag'];
			$olditem = $_POST['oldtag'];
			$result  = checktag($newitem);
			if($result)	{
				redirect($result);
			}
			modtag($olditem,$newitem);
			redirect('修改Tags成功', 'admin.php?file=seo&action=taglist');
			break;
		case 'dodeltag':
			//批量删除Tag
			if (!isset($_POST['tag'])) {
				redirect('未选择任何Tags','admin.php?file=seo&action=taglist');
			}
			$tags=is_array($_POST['tag'])?$_POST['tag']:array($_POST['tag']);
			foreach ($tags as $tag)
			{
				removetag($tag);
			}
			redirect('成功删除所选Tags', 'admin.php?file=seo&action=taglist');
			break;
		case 'dodelredirect':
			//批量删除Tag
			if (!isset($_POST['vid'])) {
				redirect('未选择任何跳转网址','admin.php?file=seo&action=taglist');
			}
			$vids=implode_ids($_POST['vid']);
			$DB->query('delete from '.DB_PREFIX."var where `type`='redirect' and hostid=$hostid and vid in ($vids)");
			redirect('成功删除所选跳转网址', 'admin.php?file=seo&action=redirect');
			break;
		case 'domodredirect':
			//批量删除Tag
			if (!isset($_POST['vid'])) {
				redirect('未指定修改的网址','admin.php?file=seo&action=redirect');
			}
			$vid=intval($_POST['vid']);
			$title=isset($_POST['title'])?$_POST['title']:'';
			$value=isset($_POST['value'])?$_POST['value']:'';
			if(!$title)  redirect('原网址不得为空', 'admin.php?file=seo&action=redirect');
			if(!$value)  redirect('转向地址不得为空', 'admin.php?file=seo&action=redirect');
			$search=$DB->fetch_first('select * from '.DB_PREFIX."var where title='$title' and vid!=$vid and `type`='redirect' and hostid=$hostid");
			if($search) redirect('原地址不能同时转向多个地址', 'admin.php?file=seo&action=redirect');
			$DB->query("update ".DB_PREFIX."var set title='$title',value='$value' where vid=$vid and hostid=$hostid");
			break;
		default:
			redirect('未定义操作', 'admin.php?file=seo');
	}
}

//下边为GET方法
if(empty($action)) $action='taglist';
//标签列表
if ($action=='taglist') {
	if($page) {
		$start_limit = ($page - 1) * 30;
	} else {
		$start_limit = 0;
		$page = 1;
	}
	$numsql = "LIMIT $start_limit, 30";
	$rs = $DB->fetch_first("SELECT count(*) AS tags FROM ".DB_PREFIX."tag");
	$tatol = $rs['tags'];
	$multipage = multi($tatol, 30, $page, 'admin.php?file=seo&action=taglist');

    $query = $DB->query("SELECT tag,count(articleid) as usenum FROM ".DB_PREFIX."tag where hostid='$hostid' group by tag $numsql");
	$tagdb = array();
    while ($tag = $DB->fetch_array($query)) {
		$tag['url'] = urlencode($tag['tag']);
		$tag['item'] = htmlspecialchars($tag['tag']);
		$tagdb[] = $tag;
	}
	unset($tag);
	$DB->free_result($query);
}//list
else if($action=='redirect')
{
	if($page) {
		$start_limit = ($page - 1) * 30;
	} else {
		$start_limit = 0;
		$page = 1;
	}
	$numsql = "LIMIT $start_limit, 30";
	$rs = $DB->query("SELECT * FROM ".DB_PREFIX."var where hostid=$hostid and `type`='redirect'");
	$tatol =count($rs);
	$multipage = multi($tatol, 30, $page, 'admin.php?file=tag&action=redirect');
	$redirectdb = array();
    while ($tag = $DB->fetch_array($rs)) {
		$redirectdb[] = $tag;
	}
	unset($tag);
	$DB->free_result($rs);
}
//修改标签
else if($action == 'modtag') {
	$tag = $_GET['tag'];
	$tagsql="SELECT articleid FROM ".DB_PREFIX."tag WHERE tag='$tag'";
	$tagquery=$DB->query($tagsql);
	$aidarr=array();
	while($taginfo=$DB->fetch_array($tagquery))
	{
		$aidarr[]=$taginfo['articleid'];
	}
	if (!empty($aidarr)) {
		$aids=implode(',',$aidarr);
		$query  = $DB->query("SELECT aid, title FROM ".DB_PREFIX."article WHERE aid IN ($aids)");
		$articledb = array();
		while ($article = $DB->fetch_array($query)) {
			$articledb[] = $article;
		}
		$usenum=count($aidarr);
		unset($article);
		$DB->free_result($query);
	}
}else if($action == 'modredirect') {
	$vid = $_GET['vid'];
	$redirectdb=$DB->fetch_first("SELECT * FROM ".DB_PREFIX."var WHERE hostid=$hostid and vid=$vid");
	if(!$redirectdb) redirect('不存在的转向网址记录', 'admin.php?file=seo');
}//mod