<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
$catearg=$host['friend_url'];
if($catearg=='aid') $catearg='cid';
if(!isset($_GET[$catearg])||!$_GET[$catearg]) message('未定义参数', './');

$cate=array();
foreach($cates as $ct)
{
	if($ct[$catearg]==$_GET[$catearg]) $cate=$ct;
}
if(empty($cate)) message('不存在的栏目', './');

$expr="c.$catearg='{$_GET[$catearg]}'";
$page=isset($_GET['page'])?intval($_GET['page']):1;
$articledb=getCateArticle($expr,$page);
$tatol=count($articledb);
$multipage='';
$catekey=$catearg;
if(is_array($Files['arg'][RQ_FILE])&&!empty($Files['arg'][RQ_FILE]))
{
	$cidkeys=array_keys($Files['arg'][RQ_FILE]);
	$catekey=$cidkeys[0];
}

$allcount=1;
if($tatol>0)
{
	$arr=$DB->fetch_first("SELECT count(*) FROM ".DB_PREFIX."article WHERE hostid=$hostid and visible=1 and cateid={$cate['cid']}");
	if(!empty($arr)) $allcount=$arr['count(*)'];
	$allpage=@ceil($allcount/$host['list_shownum']);
	$multipage=pagination($allcount,$host['list_shownum'],$page,'category.php?'.$catekey.'='.$_GET[$catearg].'&page');
}

$title=$cate['name'];
$keywords=$cate['keywords'];
$description=$cate['description'];

doAction('category_before_view');