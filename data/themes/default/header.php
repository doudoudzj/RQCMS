<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
//这里不同有几点,一个是title,keywords,description

if(!isset($keywords)) $keywords=$host['keywords'];
if(!isset($description)) $description=$host['description'];

//使用内容页泛域名功能后，首页需要使用绝对地址，反之使用相对地址
$homeurl=RQ_ALIAS&&isset($aliasname)?RQ_HTTP.$host['host'].'/':'/';

if(!isset($rssinfo)) $rssinfo='';
print <<<EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
<title>{$title}</title>
<meta name="author" content="rq204">
<meta name="keywords" content="{$keywords}">
<meta name="description" content="{$description}">
<meta content="text/html; charset=utf-8" http-equiv=Content-Type>
<link title="{$host['name']}" rel=alternate type=application/rss+xml href="rss.php$rssinfo">
<link rel=stylesheet type=text/css href="images/common.css">
</head>
<body>
<div class=wrap>
  <div class=top-nav>
    <div class=top-menu>
      <ul>
		<li><a href="http://www.infzm.com/" target=_blank>南方周末</a></li>
		<li><a href="http://focus.news.163.com/" target=_blank>网易深度</a></li>
      </ul>
    </div>
    <div class=top-nav-logo></div>
  </div>
  <div class=top>
    <div class=top640-480><a href="http://english.locoy.com" target=_blank><IMG src="images/welcome.png" width=468 height=60></a></div>
    <div class=ad-text>
      <ul>
        <li><a href="#" onClick="javascript:window.external.AddFavorite('{$host['host']}','{$host['name']} {$host['keywords']}');">收藏本站</a></li>
        <li><a href="rss.php$rssinfo" target=_blank>RSS订阅</a></li>
      </ul>
    </div>
  </div>
  <div class=menu>
    <ul id=menu-left>
      <li><a href="{$homeurl}">首页</a></li>
      <li><a href="search.php">搜索</a></li>
      <li><a href="tag.php">标签</a></li>
      <li><a href="comment.php">评论</a></li>
	</ul>
EOT;
if ($uid) {print <<<EOT
    <ul id=menu-right>
      <li><a href="profile.php">资料</a></li>
      <li><a href="profile.php?action=logout">注销</a></li>
EOT;
if ($groupid == 3 || $groupid == 4) {print <<<EOT
      <li><a href="admin.php" target="_blank">管理</a></li>
EOT;
}}else{print <<<EOT
      <li><a href="profile.php?action=reg">注册</a></li>
      <li><a href="profile.php?action=login">登陆</a></li>
EOT;
}print <<<EOT
    </ul>
  </div>
EOT;
?>