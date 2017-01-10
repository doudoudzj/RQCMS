<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
include RQ_CORE.'/include/category.php';
include RQ_CORE.'/include/attachment.php';
if(RQ_POST)
{
	switch($action)
	{
		case 'doadd':
			//添加行业
			$name   = trim($_POST['name']);
			$displayorder = intval($_POST['displayorder']);
			$url=trim($_POST['url']);
			$pid=trim($_POST['pid']);
			$keywords=trim($_POST['keywords']);
			$description=trim($_POST['description']);
			$result = checkname($name);
			if($result)
			{
				redirect($result);
			}
			$name = char_cv($name);
			$rs = $DB->fetch_first("SELECT count(*) AS categories FROM ".DB_PREFIX."category WHERE name='$name' and hostid='0'");
			if($rs['categories']) 
			{
				redirect('该行业名在数据库中已存在');
			}
			if(!$url) redirect('友好网址不得为空');
			$us = $DB->fetch_first("SELECT count(*) AS url FROM ".DB_PREFIX."category WHERE url='$url' and hostid='0'");
			if($us['url']) 
			{
				redirect('该友好网址在数据库中已存在');
			}
			$DB->query("INSERT INTO ".DB_PREFIX."category (name,displayorder,url,hostid,keywords,description,pid) VALUES ('$name','$displayorder','$url','0','$keywords','$description','$pid')");
			cate0_recache();
			latest_recache();
			redirect('添加新行业成功', 'admin.php?file=category2');
			break;
		case 'domod':
			//修改行业
			$name   = trim($_POST['name']);
			$url   = trim($_POST['url']);
			$cid    = intval($_POST['cid']);
			$pid    = intval($_POST['pid']);
			$displayorder=intval($_POST['displayorder']);
			$keywords=trim($_POST['keywords']);
			$description=trim($_POST['description']);
			$result = checkname($name);
			if($result) redirect($result);
			$name = char_cv($name);
			$rs = $DB->fetch_first("SELECT count(*) AS categories FROM ".DB_PREFIX."category WHERE cid!='$cid' AND name='$name' and hostid='0'");
			if($rs['categories']) {
				redirect('已经有其他行业使用【'.$name.'】这个名称');
			}
			if(!$url) redirect('友好网址不得为空');
			$us = $DB->fetch_first("SELECT count(*) AS url FROM ".DB_PREFIX."category WHERE cid!='$cid' AND url='$url' and hostid='0'");
			if($us['url']) {
				redirect('已经有其他友好网址使用【'.$url.'】这个名称');
			}
			// 更新行业
			$DB->unbuffered_query("UPDATE ".DB_PREFIX."category SET name='$name',displayorder='$displayorder',url='$url',keywords='$keywords',description='$description',pid='$pid' WHERE cid='$cid'");
			cate0_recache();
			stick_recache();
			rss_recache();
			stick_recache();
			pics_recache();
			latest_recache();
			redirect('修改行业成功', 'admin.php?file=category2');
			break;
		case 'dodel':
			//删除行业
			$cid = intval($_POST['cid']);
			$aids = $a_tatol = 0;
			// 删除行业
			$DB->query("DELETE FROM ".DB_PREFIX."category WHERE cid='$cid' and hostid='0'");
			// 加载附件相关函数
			//require_once(SACMS_ROOT.'include/func_attachment.php');
			$query = $DB->query("SELECT aid, keywords, userid, visible FROM ".DB_PREFIX."article WHERE cateid='$cid' and hostid='0' ORDER BY aid");
			while ($article = $DB->fetch_array($query)) {
				$aids .= ','.$article['aid'];
				if ($article['keywords']) {
					updatetags($article['aid'], '', $article['keywords']);
				}
				if ($article['visible']) {
					$a_tatol++;
					$DB->query("UPDATE ".DB_PREFIX."user SET articles=articles-1 WHERE uid='".$article['userid']."'");
				}
			}//end while

			// 删除该行业下文章中的附件
			$query  = $DB->query("SELECT aid,filepath,thumb_filepath FROM ".DB_PREFIX."attachment WHERE articleid IN ($aids)");
			$nokeep = array();
			while($attach = $DB->fetch_array($query)) {
				$nokeep[$attach['aid']] = $attach;
			}
			removeattachment($nokeep);
			$DB->unbuffered_query("DELETE FROM ".DB_PREFIX."comment WHERE articleid IN ($aids)");

			// 删除行业下的文章
			$DB->unbuffered_query("DELETE FROM ".DB_PREFIX."article WHERE cateid='$cid'");
			cate0_recache();
			stick_recache();
			rss_recache();
			stick_recache();
			pics_recache();
			latest_recache();
			redirect('成功删除行业和该行业下所有文章以及相关评论', 'admin.php?file=category2');
			break;
		case 'updatedisplayorder':
			// 更新行业排序
			if (!$_POST['displayorder'] || !is_array($_POST['displayorder'])) 
			{
				redirect('未选择任何行业');
			}
			$displayorder=$_POST['displayorder'];
			foreach($displayorder as $cid => $order) 
			{
				$DB->unbuffered_query("UPDATE ".DB_PREFIX."category SET displayorder='".intval($order)."' WHERE cid='$cid' and hostid='0'");
			}
			cates_recache();
			redirect('所有行业的排序已更新', 'admin.php?file=category2');
			break;
		default:
			redirect('未定义操作', 'admin.php?file=category2');
	}
}
else
{
	if(empty($action)) $action='list';
	$catenav = '行业管理';
	
	$cateArr=array();
	$catequery=$DB->query('Select * from '.DB_PREFIX."category where hostid=0 order by displayorder desc");
	while($cateinfo=$DB->fetch_array($catequery))
	{
		$cid=$cateinfo['cid'];
		$cateArr[$cid]=$cateinfo;
	}
	
	foreach($cateArr as $cid=>$cateinfo)
	{
		$cateArr[$cid]['child']=getChildCate($cid,$cateArr);
	}

	//行业操作
	if (in_array($action, array('add', 'mod', 'del'))) {
	 //先得到所有
		if ($action == 'add') {
			$subnav = '添加行业';
			$cate['cid']=$cate['name']=$cate['url']=$cate['keywords']=$cate['description']='';
			$cate['displayorder']=0;
			$cate['pid']=isset($_GET['pid'])?$_GET['pid']:'';
		} else {
			$cate = $DB->fetch_first("SELECT * FROM ".DB_PREFIX."category WHERE cid='".intval($_GET['cid'])."' and hostid='0'");
			if($action == 'mod') {
				$subnav = '修改行业';
			} else {
				$subnav = '删除行业';
			}
		}
	}
	if($action=='list')
	{
		foreach($cateArr as $cid=>$cateinf)
		{
			$cateArr[$cid]['articles']=getArticleNum('0',$cateinf['child']);
		}
	}
}