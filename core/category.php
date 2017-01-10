<?php
if(!isset($_GET['url1'])) run404('未定义参数');

$cate=array();
foreach($category as $ct)
{
	if($ct['url']==$_GET['url1']) $cate=$ct;
}
if(empty($cate)) run404('不存在的栏目');

$page=isset($_GET['url2'])?intval($_GET['url2']):1;
$pagenums=1;
$articledb=getCateArticle($cate['child'],$page);
$total=count($articledb);
$multipage='';

$allcount=0;
if($total>0)
{
	$arr=$DB->fetch_first("SELECT count(*) FROM ".DB_PREFIX."article WHERE cateid in ({$cate['child']})");
	if(!empty($arr)) $allcount=$arr['count(*)'];
	$pagenums=@ceil($allcount/$host['list_shownum']);
}

$title=$cate['name'];
$keywords=$cate['keywords'];
$description=$cate['description'];

doAction('category_before_view');