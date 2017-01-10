<?php
//获取一个栏目中有多少文章
function getArticleNum($cateids)
{
	global $DB;
	$fetch=$DB->fetch_first("Select count(*) as a from `".DB_PREFIX."article` where `cateid` in ($cateids)");
	return $fetch['a'];
}

// 检查分类名是否符合逻辑
function checkname($name) {
	if(!$name || strlen($name) > 30) {
		$result = '分类名不能为空并且不能超过30个字符<br />';
		return $result;
	}
}

function getCateOption($category,$select,$self='')
{
	$re='';
	foreach($category as $a=>$b)
	{
		if($b['pid']=='0')
		{
			if($self==$b['cid']) continue;
			$add=$select==$a?' selected':'';
			$re.='<option value="'.$a.'" '.$add.'>'.$b['name'].'</option>';
			$re.=getoption($a,$category,$select,1,$self);
		}
	}
	return $re;
}

function getoption($pid,$category,$select,$level,$self)
{
	$re='';
	foreach($category as $a=>$b)
	{
		if($b['pid']==$pid)
		{
			if($self==$b['cid']) continue;
			$add=$select==$a?' selected':'';
			$pad=str_pad('', $level, '+', STR_PAD_LEFT);
			$re.='<option value="'.$a.'"'.$add.'>'.$pad.$b['name'].'</option>';
			$re.=getoption($a,$category,$select,$level+1,$self);
		}
	}
	return $re;
}

function getChildLevel($cid,$category)
{
	$level=0;
	foreach($category as $id=>$cateinfo)
	{
		if($cateinfo['pid']==$cid)
		{
			$level=$level+getChildLevel($id,$category);
		}
	}
	return $level;
}

function getMaxCid($category)
{
	sort($category);
	$a=end($category);
	return $a['cid'];
}
	