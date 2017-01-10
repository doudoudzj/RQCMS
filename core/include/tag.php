<?php
// 检查提交Tag是否符合逻辑
function checktag($tag) {
	$tag = str_replace('，', ',', $tag);
	if (strrpos($tag, ',')) {
		$result .= '关键字中不能含有“,”或“，”字符<br />';
		return $result;
	}
	if(strlen($tag) > 15) {
		$result .= '关键字不能超过15个字符<br />';
		return $result;
	}
}

//更改tag
function modtag($oldtag,$newtag,$aid)
{
	global $DB;
	$oldarr=array();
	$newarr=array();
	if($oldtag) $oldarr=explode(',',$oldtag);
	if($newtag) $newarr=explode(',',$newtag);
	$delold=array_diff($oldarr,$newarr);
	$addnew=array_diff($newarr,$oldarr);
	if($delold)
	{
		foreach($delold as $tag)
		{
			$aids=gettagids($tag);
			if($aids)
			{
				$aidsarr=explode(',',$aids);
				foreach($aidsarr as $k=>$temp)
				{
					if($temp==$aid) unset($aidsarr[$k]);
				}
				$aidsnew='';
				if($aidarr) $aidsnew=implode(',',$aidsarr);
				$DB->query("update ".DB_PREFIX."tag set aids='$aidsnew' where tag='$tag'");
			}
		}
	}
	
	if($addnew)
	{
		foreach($addnew as $tag)
		{
			$aids=gettagids($tag);
			if($aids==null)
			{
				$DB->query("insert into ".DB_PREFIX."tag (`tag`,`aids`) values ('$tag','$aid')");
			}
			else
			{
				if($aids=='') $aids=$aid;
				else
				{
					$aidsarr=explode(',',$aids);
					$aidsarr[]=$aid;
				}
				$aidsnew=implode(',',$aidsarr);
				$DB->query("update ".DB_PREFIX."tag set aids='$aidsnew' where tag='$tag'  ");
			}
		}
	}

	$DB->query("delete from ".DB_PREFIX."tag where aids=''");
	
}

//删除Tag
function removealltag($tagname)
{
	global $DB;
	$aids=gettagids($tagname);
	if($aids)
	{
		$query=$DB->query('Select tag,aid from '.DB_PREFIX."article where aid in ($aids)  ");
		while($result=$DB->fetch_array($query))
		{
			$tagstr=$result['tag'];
			$aid=$result['aid'];	

			$newtagstr='';
			$oldtagarr=explode(',',$tagstr);
			foreach($oldtagarr as $oldtag) 
			{
				if($oldtag!=$tagname) $newtagstr.=','.$oldtag;
			}
			if($newtagstr) 
			{
				$newtagstr=substr($newtagstr,1);
			}
			$DB->query('update '.DB_PREFIX."article set `tag`='$newtagstr' where `aid`='$aid'");
		}
	}
	$DB->query("Delete from ".DB_PREFIX."tag where tag='$tagname'");
}

function cleartag($tagname)
{
	$tagname=trim($tagname);
	$trim=array('/','*','$','-',';','#','"');
	foreach($trim as $tr) $tagname=str_replace($tr,'',$tagname);
	if(preg_match('/[a-zA-Z ]*/ui', $tagname))
	{
		$tagname=ucwords(strtolower($tagname));
	}
	return $tagname;
}

function rebuildtag($tagindex,$indexurl)
{
	global $DB;
	$list=array();
	
	if($tagindex==0)
	{
		$DB->query('TRUNCATE `'.DB_PREFIX.'tag`');
	}
	
	$tagquery=$DB->query("Select tag,aid from ".DB_PREFIX."article where aid>$tagindex and tag!='' limit 5000");
	while($data=$DB->fetch_array($tagquery))
	{
		$tagarr=explode(',',$data['tag']);
		$newtag=array();
		foreach($tagarr as $tagname)
		{
			$tagname=cleartag($tagname);
			if($tagname&&strlen($tagname)>1)
			{			
				$list[$tagname][]=$data['aid'];
				$newtag[]=$tagname;
			}
		}
		$tagindex=$data['aid'];
		$newtag=implode(',',$newtag);
		if($newtag!=$data['tag'])
		{
			$newtag=addslashes($newtag);
			$DB->query("update ".DB_PREFIX."article set tag='$newtag' where aid=$tagindex");
		}
	}
	
	$DB->query("DROP TABLE IF EXISTS `mtemp`");
  
	if(count($list)==0)
	{	
		$DB->query("create table `mtemp` select tag,GROUP_CONCAT(`aids`) as aids from `".DB_PREFIX."tag` GROUP BY tag");
		$DB->query('TRUNCATE `'.DB_PREFIX.'tag`');
		$DB->query("insert into ".DB_PREFIX."tag (`tag`,`aids`) select tag,aids from mtemp");
		Jump('tag升级完成',$indexurl);
	}
	
	$sql="CREATE TABLE `mtemp` (`tag` varchar(20) NOT NULL,`aids` varchar(500) NOT NULL) ENGINE=MEMORY DEFAULT CHARSET=utf8;";
	$DB->query($sql);
	foreach($list as $tagname=>$data)
	{
		$aids=implode(',',$data);
		$tagname=addslashes($tagname);
		if(strlen($aids)>500)
		{
			$DB->query("insert into `".DB_PREFIX."tag` (`tag`,`aids`) values ('$tagname','$aids')");	
		}
		else
		{
			$DB->query("insert into `mtemp` (`tag`,`aids`) values ('$tagname','$aids')");
		}
	}

	$DB->query("insert into ".DB_PREFIX."tag (`tag`,`aids`) select tag,aids from mtemp");
	$DB->query("DROP TABLE IF EXISTS `mtemp`");
	Jump('继续升级tag中,下次更新文章id'.$tagindex,$indexurl."&tagindex=$tagindex");
}
	
//得到ids
function gettagids($tagname)
{
	global $DB;
	$tagsql="SELECT aids FROM ".DB_PREFIX."tag WHERE tag='$tagname' ";
	$aids=null;
	$tagarr=$DB->fetch_first($tagsql);
	if($tagarr)
	{
		$aids= $tagarr['aids'];
	}
	return $aids;
}