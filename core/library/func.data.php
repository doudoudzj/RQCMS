<?php
//得到最新$num条$cateid分类的文章
function getLatestArticle($num,$cateid=0)
{
	global $host;
	$cache=@include RQ_DATA.'/cache/latest_'.$host['hid'].'.php';
	$cateadd=$cateid?" where cateid in ({$cateid})":'';
	$sql='SELECT * FROM `'.DB_PREFIX."article` $cateadd ORDER BY aid DESC limit $num";
	$arr=getCacheDB($cache,$num,$cateid,$sql);
	rsort($arr);
	return $arr;
}

//得到置顶的$num条$cateid分类文章
function getStickArticle($num,$cateid=0)
{
	global $host;
	$cache=@include RQ_DATA.'/cache/stick_'.$host['hid'].'.php';
	$cateadd=$cateid?" and cateid in ({$cateid})":'';
	$sql='SELECT * FROM `'.DB_PREFIX."article` where stick=1 $cateadd ORDER BY aid DESC limit $num";
	return getCacheDB($cache,$num,$cateid,$sql);
}

//得到热门文章
function getHotArticle($num,$cateid=0)
{
	global $host,$DB;
	$cache=@include RQ_DATA.'/cache/hot_'.$host['hid'].'.php';
	$cateadd=$cateid?" where cateid in ({$cateid})":'';
	$sql='SELECT * FROM `'.DB_PREFIX."article` $cateadd ORDER BY views DESC limit $num";
	return getCacheDB($cache,$num,$cateid,$sql);
}

//得到相关文章,是从本站查询的
function getRelatedArticle($aid,$tagarr,$num)
{
	global $DB,$host;
	$articledb=array();
	$tag="'".implode("','",$tagarr)."'";
	$query=$DB->query('Select * from '.DB_PREFIX."tag where tag in ($tag)");
	
	$aidarr=array();
	while($aq=$DB->fetch_array($query))
	{
		$dbaids=explode(',',$aq['aids']);
		$aidarr=array_merge($aidarr,$dbaids);
	}

	if(!empty($aidarr))
	{
		$aidarr=array_unique($aidarr);
		unset($aidarr[$aid]);
		shuffle($aidarr);
		if(count($aidarr)>$num) $aidarr=array_slice($aidarr,0,$num);
		$aids=implode_ids($aidarr);
		$query=$DB->query('Select * from '.DB_PREFIX."article where aid in ($aids) order by aid");
		while($article=$DB->fetch_array($query))
		{
			$articledb[]=showArticle($article);
		}
		shuffle($articledb);
	}	
	return $articledb;
}

//得到某个分类的文章列表
function getCateArticle($cateids,$page)
{
	global $DB,$hostid,$host,$category;
	$pagenum = intval($host['list_shownum']);
	$start_limit = ($page - 1) * $pagenum;
	$catesql=$cateids==0?'':"WHERE `cateid` in ($cateids)";
	$sql = "SELECT * FROM ".DB_PREFIX."article $catesql ORDER BY aid DESC LIMIT $start_limit, ".$pagenum;//exit($sql);
	$articledb=array();
	$query=$DB->query($sql);
	while($article=$DB->fetch_array($query))
	{
		$articledb[]=showArticle($article);
	}
	return $articledb;
}

//得到符合条件的文章，包含附件
function getArticle($url)
{
	global $DB,$hostid,$host;
	$sql = "SELECT * FROM ".DB_PREFIX."article WHERE url='$url' limit 1";
	$article=$DB->fetch_first($sql);
	if(!empty($article))
	{
		$cindex=ceil($article['aid']/500000);
		$sql="SELECT * FROM ".DB_PREFIX."content{$cindex} WHERE articleid={$article['aid']} limit 1";
		$article2=$DB->fetch_first($sql);
		$article=array_merge($article,$article2);
		$article=showArticle($article);
		$articleid=$article['aid'];
		//处理附件
		if ($article['attachments']) 
		{
			$attachs=getAttachById($articleid);
			if (isset($attachs[$articleid])&&is_array($attachs[$articleid])) 
			{
				$article['attachments']=array();
				foreach($attachs[$articleid] as $aid=>$attach)
				{
					$article['attachments'][$aid]=$attach;
					$article['attachments'][$aid]['downloads']=$attach['downloads'];
					$article['attachments'][$aid]['filesize']=(int)($attach['filesize']/1024);
					$argurl=mkUrl('attachment',$aid);
					if($attach['isimage'])
					{
						$file="<a href='{$argurl}' target='_blank'><img src='{$argurl}' alt='{$attach['filename']}'></a>";
					}
					else
					{
						$file="<a href='{$argurl}' target='_blank'>{$attach['filename']}</a>";
					}

					if(strpos($article['content'],"[attach=$aid]")!==false)
					{
						$article['content']=str_replace("[attach=$aid]",$file,$article['content']);
						unset($article['attachments'][$aid]);//加在文章中后就不用在后边显示了.
					}
					else
					{
						$article['attachments'][$aid]['aurl']=$argurl;
					}
				}
				//print_r($article['attachments']);exit;
			}
		}
		if(!empty($article['tag'])) $article['tag']=explode(',',$article['tag']);
	}
	return $article;
}


//按id得到附件
function getAttachById($aids)
{
	global $DB,$host;
	$attacharr=array();
	$downloads=$DB->query('select * from '.DB_PREFIX."attachment where articleid in (".$aids.')');
	while($dds=$DB->fetch_array($downloads))
	{
		$attacharr[$dds['articleid']][$dds['aid']]=$dds;
	}
	return $attacharr;
}

//得到上一篇文章和下一篇文章
function getPreNextArticle($aid)
{
	global $DB,$host;
	$data=array();
	$preArr=$DB->fetch_first('Select max(aid) from'.DB_PREFIX."article where aid<$aid limit 1");
	$nextArr=$DB->fetch_first('Select min(aid) from'.DB_PREFIX."article where aid>$aid limit 1");
	if(empty($perArr)&&empty($nextArr)) return $data;
	if(!empty($preArr))
	{	
		$preid=$preArr['max(aid)'];
		$data['Pre']=$DB->fetch_first('Select * from '.DB_PREFIX.'article where aid=$perid');
		$data['Pre']=showArticle($data['Pre']);
	}
	if(!empty($nextArr))
	{	
		$nextid=$nextArr['max(aid)'];
		$data['Next']=$DB->fetch_first('Select * from '.DB_PREFIX.'article where aid=$nextid');
		$data['Next']=showArticle($data['Next']);
	}
	return data;
}


function getArticleByAid($query)
{
	global $DB;
	$articledb=array();
	$aidarr=array();
	while($aid=$DB->fetch_array($query))
	{
		$aidarr[]=$aid['aid'];
	}
	if(count($aidarr)>0)
	{
		$aids=implode_ids($aidarr);
		$query=$DB->query('Select * from '.DB_PREFIX."article where aid in ($aids)");
		while($article=$DB->fetch_array($query))
		{
			$articledb[]=showArticle($article);
		}
	}
	return $articledb;
}

//得到最新$num条搜索的记录
function getLatestSearch($num)
{
	global $host;
	$latestarray=@include RQ_DATA.'/cache/search_'.$host['hid'].'.php';
	if(!empty($latestarray))
	{
		if(count($latestarray)>$num) $latestarray=array_slice($latestarray, 0, $num); 
	}
	return $latestarray;
}

//先缓存中得数据，不够再查数据库
function getCacheDB($cache,$num,$cateids,$sql)
{
	global $DB;
	$articledb=array();
	$catearr=explode(',',$cateids);
	foreach($catearr as $cateid)
	{
		if(isset($cache['aids'][$cateid]))
		{
			foreach($cache['aids'][$cateid] as $aid)
			{
				$articledb[$aid]=$cache['data'][$aid];
			}
		}
	}
	if($num>20&&count($articledb)<$num) //少于的话查询一下数据库
	{	
		$articledb=array();
		$files= $DB->query($sql);
		while ($fs = $DB->fetch_array($files)) 
		{
			$articledb[]=showArticle($fs);
		}
	}
	else if(count($articledb)>$num)
	{
		$articledb=array_slice($articledb, 0, $num);
	}
	return $articledb;
}
