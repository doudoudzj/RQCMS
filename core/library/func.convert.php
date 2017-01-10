<?php
//提供其它CMS转换成RQCMS的类

//清除一下hostid对应的数据库
function ClearDatabase($DB)
{
	$arrTable=array('category','article','content','tag','content');
	foreach($arrTable as $at) $DB->query('TRUNCATE `'.DB_PREFIX."$at`");
}
	
//使用数据TAG得到入库的SQL语句
//$idandtag是一个数组，前边id后边tag
function GetTagSql($idandtag)
{
	$sql='Insert into `'.DB_PREFIX."tag` (`tag`,`articleid`) values ";
	foreach($idandtag as $id=>$tags)
	{
		foreach($tags as $tag)
		{
			$sql.="('$tag','$id'),";
		}
	}
	$sql=trim($sql,',');
	return $sql;
}

function Jump($msg,$url=''){
ob_end_clean();
print <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>系统消息</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta HTTP-EQUIV="REFRESH" content="1;url=$url">
</head>
<body style="text-align:center">
<div class="box">
  <h2 class="alert">$msg</h2>
  <div class="alertmsg"><a href="$url">如果你不想等待或浏览器没有自动跳转请点击这里跳转</a></div>
</div>
</body>
</html>
EOT;
exit;
}

//获取dedecms的softlink
function GetDedeSoftLink($dedelink)
{
	//{dede:link islocal=\'1\' text=\'Click here to download this file\'} http://xxx.com.imperialgamestudio.turbogrannies.1307620446181.apk {/dede:link}
	$b=strpos($dedelink,'}');
	if($b>0)
	{
		$c=strpos($dedelink,'{',$b);
		if($c>0)
		{
			$d=substr($dedelink,$b+1,$c-$b-1);
			return $d;
		}
	}
	return '';
}

//增加varchar类型的字段
function AddVarcharColumn($table,$column,$len)
{
	global $DB;
	$DB->query('ALTER TABLE `'.DB_PREFIX."$table` ADD COLUMN `$cloumn` VARCHAR($len) NOT NULL DEFAULT ''");
}

//使用数组生成单个插入语句
//arr是键名键值
function GetInsertSql($arr,$table)
{
	$sql='Insert into `'.DB_PREFIX.$table."` set ";
	foreach($arr as $t=>$v)
	{
		$sql.="`$t`='".addslashes($v)."',";
	}
	$sql=trim($sql,',');
	return $sql;
}		

//获取表中的所有字段
function GetTableField($table)
{
	global $DB;
	$arrlist=array();
	$prefix=$table=='rqcms_host'?'':DB_PREFIX;
	$sqlColumns = $DB->query("SHOW COLUMNS FROM ".$prefix."$table");
	while($re=$DB->fetch_array($sqlColumns))
	{
		$arrlist[]=$re['Field'];
	}
	return $arrlist;
}