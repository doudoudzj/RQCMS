<?php
include RQ_CORE.'/include/category.php';
include RQ_CORE.'/include/attachment.php';
if(RQ_POST)
{
	switch($action)
	{
		case 'doadd':
			//添加分类
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
			$rs = $DB->fetch_first("SELECT count(*) AS categories FROM ".DB_PREFIX."category WHERE name='$name' ");
			if($rs['categories']) 
			{
				redirect('该分类名在数据库中已存在');
			}
			if(!$url) redirect('友好网址不得为空');
			$us = $DB->fetch_first("SELECT count(*) AS url FROM ".DB_PREFIX."category WHERE url='$url' ");
			if($us['url']) 
			{
				redirect('该友好网址在数据库中已存在');
			}
			$DB->query("INSERT INTO ".DB_PREFIX."category (name,displayorder,url,keywords,description,pid) VALUES ('$name','$displayorder','$url','$keywords','$description','$pid')");
			category_recache();
			latest_recache();
			redirect('添加新分类成功', $admin_url.'?file=category');
			break;
		case 'domod':
			//修改分类
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
			$rs = $DB->fetch_first("SELECT count(*) AS categories FROM ".DB_PREFIX."category WHERE cid!='$cid' AND name='$name' ");
			if($rs['categories']) {
				redirect('已经有其他分类使用【'.$name.'】这个名称');
			}
			if(!$url) redirect('友好网址不得为空');
			$us = $DB->fetch_first("SELECT count(*) AS url FROM ".DB_PREFIX."category WHERE cid!='$cid' AND url='$url' ");
			if($us['url']) {
				redirect('已经有其他友好网址使用【'.$url.'】这个名称');
			}
			// 更新分类
			$DB->unbuffered_query("UPDATE ".DB_PREFIX."category SET name='$name',displayorder='$displayorder',url='$url',keywords='$keywords',description='$description',pid='$pid' WHERE cid='$cid'");
			category_recache();
			stick_recache();
			
			latest_recache();
			redirect('修改分类成功', $admin_url.'?file=category');
			break;
		case 'dodel':
			//删除分类
			$cid = intval($_POST['cid']);
			$aids = $a_tatol = 0;
			// 删除分类
			$DB->query("DELETE FROM ".DB_PREFIX."category WHERE cid='$cid' ");
			// 加载附件相关函数
			//require_once(SACMS_ROOT.'include/func_attachment.php');
			$query = $DB->query("SELECT aid, keywords, visible FROM ".DB_PREFIX."article WHERE cateid='$cid'  ORDER BY aid");
			while ($article = $DB->fetch_array($query)) {
				$aids .= ','.$article['aid'];
				if ($article['keywords']) {
					updatetags($article['aid'], '', $article['keywords']);
				}
			}//end while

			// 删除该分类下文章中的附件
			$query  = $DB->query("SELECT aid,filepath,thumb_filepath FROM ".DB_PREFIX."attachment WHERE articleid IN ($aids)");
			$nokeep = array();
			while($attach = $DB->fetch_array($query)) {
				$nokeep[$attach['aid']] = $attach;
			}
			removeattachment($nokeep);
			$DB->unbuffered_query("DELETE FROM ".DB_PREFIX."comment WHERE articleid IN ($aids)");

			// 删除分类下的文章
			$DB->unbuffered_query("DELETE FROM ".DB_PREFIX."article WHERE cateid='$cid'");
			category_recache();
			stick_recache();
			stick_recache();
			
			latest_recache();
			redirect('成功删除分类和该分类下所有文章以及相关评论', $admin_url.'?file=category');
			break;
		case 'updatedisplayorder':
			// 更新分类排序
			if (!$_POST['displayorder'] || !is_array($_POST['displayorder'])) 
			{
				redirect('未选择任何分类');
			}
			$displayorder=$_POST['displayorder'];
			foreach($displayorder as $cid => $order) 
			{
				$DB->unbuffered_query("UPDATE ".DB_PREFIX."category SET displayorder='".intval($order)."' WHERE cid='$cid' ");
			}
			category_recache();
			redirect('所有分类的排序已更新', $admin_url.'?file=category');
			break;
		default:
			redirect('未定义操作', $admin_url.'?file=category');
	}
}
else
{
	if(empty($action)) $action='list';
	$catenav = '分类管理';
	
	$category=array();
	$catequery=$DB->query('Select * from '.DB_PREFIX."category  order by displayorder desc");
	while($cateinfo=$DB->fetch_array($catequery))
	{
		$cid=$cateinfo['cid'];
		$category[$cid]=$cateinfo;
	}
	
	foreach($category as $cid=>$cateinfo)
	{
		$category[$cid]['child']=getChildCate($cid,$category);
	}

	//分类操作
	if (in_array($action, array('add', 'mod', 'del'))) {
	 //先得到所有
		if ($action == 'add') {
			$subnav = '添加分类';
			$cate['cid']=$cate['name']=$cate['url']=$cate['keywords']=$cate['description']='';
			$cate['pid']=isset($_GET['pid'])?$_GET['pid']:'';
			$cate['displayorder']=0;
		} else {
			$cate = $DB->fetch_first("SELECT * FROM ".DB_PREFIX."category WHERE cid='".intval($_GET['cid'])."' ");
			if($action == 'mod') {
				$subnav = '修改分类';
			} else {
				$subnav = '删除分类';
			}
		}
	}
	if($action=='list')
	{
		foreach($category as $cid=>$cateinf)
		{
			$category[$cid]['articles']=getArticleNum($cateinf['child']);
		}
	}
}