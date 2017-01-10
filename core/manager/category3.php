<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
include RQ_CORE.'/include/category.php';
include RQ_CORE.'/include/attachment.php';
if(RQ_POST)
{
	switch($action)
	{
		case 'doadd':
			//添加地区
			$name   = trim($_POST['name']);
			$displayorder = intval($_POST['displayorder']);
			$url=trim($_POST['url']);
			$pid=trim($_POST['pid']);
			$result = checkname($name);
			if($result)
			{
				redirect($result);
			}
			$name = char_cv($name);
			$rs = $DB->fetch_first("SELECT count(*) AS categories FROM ".DB_PREFIX."area WHERE name='$name'");
			if($rs['categories']) 
			{
				redirect('该地区名在数据库中已存在');
			}
			if(!$url) redirect('友好网址不得为空');
			$us = $DB->fetch_first("SELECT count(*) AS url FROM ".DB_PREFIX."area WHERE url='$url'");
			if($us['url']) 
			{
				redirect('该友好网址在数据库中已存在');
			}
			$DB->query("INSERT INTO ".DB_PREFIX."area (name,displayorder,url,pid) VALUES ('$name','$displayorder','$url','$pid')");
			redirect('添加新地区成功', 'admin.php?file=category3');
			break;
		case 'domod':
			//修改地区
			$name   = trim($_POST['name']);
			$url   = trim($_POST['url']);
			$cid    = intval($_POST['cid']);
			$pid    = intval($_POST['pid']);
			$displayorder=intval($_POST['displayorder']);
			$result = checkname($name);
			if($result) redirect($result);
			$name = char_cv($name);
			$rs = $DB->fetch_first("SELECT count(*) AS categories FROM ".DB_PREFIX."area WHERE cid!='$cid' AND name='$name'");
			if($rs['categories']) {
				redirect('已经有其他地区使用【'.$name.'】这个名称');
			}
			if(!$url) redirect('友好网址不得为空');
			$us = $DB->fetch_first("SELECT count(*) AS url FROM ".DB_PREFIX."area WHERE cid!='$cid' AND url='$url'");
			if($us['url']) {
				redirect('已经有其他友好网址使用【'.$url.'】这个名称');
			}
			// 更新地区
			$DB->unbuffered_query("UPDATE ".DB_PREFIX."area SET name='$name',displayorder='$displayorder',url='$url',pid='$pid' WHERE cid='$cid'");
			redirect('修改地区成功', 'admin.php?file=category3');
			break;
		case 'dodel':
			//删除地区
			$cid = intval($_POST['cid']);
			$aids = $a_tatol = 0;
			// 删除地区
			$DB->query("DELETE FROM ".DB_PREFIX."area WHERE cid='$cid'");
			// 加载附件相关函数
			//require_once(SACMS_ROOT.'include/func_attachment.php');
			// 删除地区下的文章
			//$DB->unbuffered_query("DELETE FROM ".DB_PREFIX."article WHERE cateid='$cid'");
			redirect('成功删除地区和该地区下所有文章以及相关评论', 'admin.php?file=category3');
			break;
		case 'updatedisplayorder':
			// 更新地区排序
			if (!$_POST['displayorder'] || !is_array($_POST['displayorder'])) 
			{
				redirect('未选择任何地区');
			}
			$displayorder=$_POST['displayorder'];
			foreach($displayorder as $cid => $order) 
			{
				$DB->unbuffered_query("UPDATE ".DB_PREFIX."area SET displayorder='".intval($order)."' WHERE cid='$cid'");
			}
			redirect('所有地区的排序已更新', 'admin.php?file=category3');
			break;
		default:
			redirect('未定义操作', 'admin.php?file=category3');
	}
}
else
{
	if(empty($action)) $action='list';
	$catenav = '地区管理';
	
	$cateArr=array();
	$catequery=$DB->query('Select * from '.DB_PREFIX."area order by displayorder desc");
	while($cateinfo=$DB->fetch_array($catequery))
	{
		$cid=$cateinfo['cid'];
		$cateArr[$cid]=$cateinfo;
	}
	
	foreach($cateArr as $cid=>$cateinfo)
	{
		$cateArr[$cid]['child']=getChildCate($cid,$cateArr);
	}

	//地区操作
	if (in_array($action, array('add', 'mod', 'del'))) {
	 //先得到所有
		if ($action == 'add') {
			$subnav = '添加地区';
			$cate['cid']=$cate['name']=$cate['url']='';
			$cate['displayorder']=0;
			$cate['pid']=isset($_GET['pid'])?$_GET['pid']:'';
		} else {
			$cate = $DB->fetch_first("SELECT * FROM ".DB_PREFIX."area WHERE cid='".intval($_GET['cid'])."'");
			if($action == 'mod') {
				$subnav = '修改地区';
			} else {
				$subnav = '删除地区';
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