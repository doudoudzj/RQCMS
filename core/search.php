<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
$keywords = isset($_POST['keywords'])?$_POST['keywords']:'';
$articledb=array();
$searchurl='search.php';
if(RQ_POST)
{
	if(empty($keywords)) message('搜索内容为空', $searchurl);
	
	//过滤及检测
	if(strlen($keywords) < $host['search_keywords_min_len']) 
	{
		message('关键字不能少于'.$host['search_keywords_min_len'].'个字节.', 'search.php');
	}
	if($groupid<2&&$host['search_post_space']>0)//时间间隔处理
	{
		$history=$DB->fetch_first('Select max(dateline) as time from '.DB_PREFIX."log where `type`='s' and `ip`='$onlineip'");
		if($history&&$timestamp-$history['time']<$host['search_post_space'])
		{
			message('对不起,您在 '.$options['search_post_space'].' 秒内只能进行一次搜索.', $searchurl);
		}
	}

	//写入搜索日志
	$DB->query('Insert into '.DB_PREFIX."log (`hostid`,`user`,`dateline`,`type`,`useragent`,`ip`,`content`) values ('$hostid','$username','$timestamp','s','$useragent','$onlineip','$keywords')"); 
	$keywords = str_replace("_","\_",$keywords);
	$keywords = str_replace("%","\%",$keywords);
	
	doAction('search_before_featch');
	
	if(preg_match("(AND|\+|&|\s)", $keywords) && !preg_match("(OR|\|)", $keywords)) {
		$andor = ' AND ';
		$sqltxtsrch = '1';
		$keywords = preg_replace("/( AND |&| )/is", "+", $keywords);
	} else {
		$andor = ' OR ';
		$sqltxtsrch = '0';
		$keywords = preg_replace("/( OR |\|)/is", "+", $keywords);
	}
	$keywords = str_replace('*', '%', addcslashes($keywords, '%_'));
	foreach(explode("+", $keywords) AS $text) {
		$text = trim($text);
		if($text) {
			$sqltxtsrch .= $andor;
			$sqltxtsrch .= "(keywords LIKE '%".str_replace('_', '\_', $text)."%' OR title LIKE '%".$text."%' OR excerpt LIKE '%".$text."%')" ;
		}
	}
	//搜索文章

	$sortby = 'dateline';
	$orderby = 'desc';
	$query_sql = "SELECT * FROM ".DB_PREFIX."article WHERE visible='1' and hostid=$hostid AND ($sqltxtsrch) ORDER BY dateline desc limit 100";
	$totals = $ids = 0;
	$query = $DB->query($query_sql);
	while($article = $DB->fetch_array($query)) 
	{
		$articledb[]=showArticle($article);
	}
	$total=count($articledb);
	//插入日志
	$DB->query('insert into `'.DB_PREFIX."log` (`user`,`dateline`,`type`,`useragent`,`ip`,`content`) values ('$username','$timestamp','search','$useragent','$onlineip','$keywords')");
	
	$multipage='';
	$title=$keywords;
}
else
{
	$searchfrom = 'article';
	$searchurl = 'search.php';
	$articledb=array();
	$title='搜索文章';
}

doAction('search_before_view');
