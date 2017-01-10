<?php
//获取一个栏目中有多少文章
function getArticleNum($hostid,$cateid)
{
	global $DB;
	$fetch=$DB->fetch_first("Select count(*) as a from `".DB_PREFIX."article` where `hostid`='$hostid' and `cateid`='$cateid'");
	return $fetch['a'];
}

// 检查分类名是否符合逻辑
function checkname($name) {
	if(!$name || strlen($name) > 30) {
		$result = '分类名不能为空并且不能超过30个字符<br />';
		return $result;
	}
}

// 删除Tag函数
function removetag($item,$tagid) {
	global $DB, $db_prefix;
	$item = addslashes($item);
	$tag = $DB->fetch_first("SELECT aids FROM ".DB_PREFIX."tags WHERE tag='$item'");
	if ($tag) {
		$query  = $DB->query("SELECT articleid, keywords FROM ".DB_PREFIX."articles WHERE articleid IN (".$tag['aids'].")");
		while ($article = $DB->fetch_array($query)) {
			$article['keywords'] = str_replace(','.$item.',', ',', $article['keywords']);
			$article['keywords'] = str_replace(','.$item, '', $article['keywords']);
			$article['keywords'] = str_replace($item.',', '', $article['keywords']);
			$article['keywords'] = str_replace($item, '', $article['keywords']);
			$DB->unbuffered_query("UPDATE ".DB_PREFIX."articles SET keywords='".addslashes($article['keywords'])."' WHERE articleid='".$article['articleid']."'");
		}
		$DB->unbuffered_query("DELETE FROM ".DB_PREFIX."tags WHERE tagid='".intval($tagid)."'");
	}
}


