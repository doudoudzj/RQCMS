<?php
// 站点缓存更新
function host_recache()
{
	global $DB;
	$contents=array();
	$hosts = $DB->query('SELECT * FROM `rqcms_host`');
	while ($arrhosts = $DB->fetch_array($hosts)) 
	{
		$contents[$arrhosts['host']]=$arrhosts;
		if(isset($arrhosts['host2'])&&$arrhosts['host2']!='')
		{
			$hostarr=explode(',',$arrhosts['host2']);
			foreach($hostarr as $ha)
			{
				if($ha&&!isset($contents[$ha])) $contents[$ha]=$arrhosts;
			}
		}
	}
	writeCache('hosts',$contents);
}

//更新插件，链接，映射文件，自动转向
function setting_recache($hostid=1)
{
	global $DB;
	$setting=array();
	$query= $DB->query('SELECT * FROM `rqcms_'.$hostid.'_plugin` where active=1');
	while ($ps = $DB->fetch_array($query)) 
	{
		$setting['plugin'][$ps['file']]=$ps['config'];
	}
	
	$links = $DB->query('SELECT * FROM `rqcms_'.$hostid.'_link`  WHERE visible = 1 ORDER BY displayorder ASC, name ASC');
	while ($link = $DB->fetch_array($links))
	{
		$setting['link'][] = $link;
	}
	
	$files= $DB->query('SELECT * FROM `rqcms_'.$hostid.'_filemap`');
	while ($fs = $DB->fetch_array($files)) 
	{
		$setting['filemap'][$fs['filename']]=$fs['original'];
	}
	
	$varArr= $DB->query('SELECT * FROM `rqcms_'.$hostid."_redirect`");
	while ($fs = $DB->fetch_array($varArr)) 
	{
		$setting['redirect'][$fs['old']]=array($fs['new'],$fs['status']);
	}
	
	writeCache('setting_'.$hostid,$setting);
}

//分类及系统设置参数
function category_recache($hostid=1)
{
	global $DB;
	$cquery= $DB->query('SELECT * FROM `rqcms_'.$hostid."_category` order by displayorder asc");
	$arrcates=array();
	while($cate=$DB->fetch_array($cquery))
	{
		$cate['curl']=mkUrl('category',$cate['url'],0);
		$arrcates[$cate['cid']]=$cate;
	}
	
	foreach($arrcates as $k=>$v)
	{
		$count='0';
		$countarr=$DB->fetch_first('SELECT count(*) as ct FROM `rqcms_'.$hostid."_article` where visible=1 and cateid='{$cate['cid']}'");
		if(is_array($countarr)) $count=$countarr['ct'];
		$arrcates[$k]['count']=$count;
		$arrcates[$k]['child']=getChildCate($k,$arrcates);
	}
	
	writeCache('category_'.$hostid,$arrcates);
}

// 最新的缓存
function latest_recache($hostid=1)
{
	global $DB,$category;
	$dataArr=array();
	$query= $DB->query('SELECT * FROM `rqcms_'.$hostid.'_article` where visible=1 ORDER BY aid DESC limit 20');
	while ($fs = $DB->fetch_array($query)) 
	{
		$dataArr['data'][$fs['aid']]=showArticle($fs);
		$dataArr['aids'][0][]=$fs['aid'];
	}
	foreach($category as $cateid=>$cname)
	{
		$query= $DB->query('SELECT * FROM `rqcms_'.$hostid."_article` where visible=1 and cateid={$cateid} ORDER BY aid DESC limit 20");
		while ($fs = $DB->fetch_array($query)) 
		{
			$dataArr['data'][$fs['aid']]=showArticle($fs);
			$dataArr['aids'][$cateid][]=$fs['aid'];
		}
	}
	writeCache('latest_'.$hostid,$dataArr);
}

// 置顶
function stick_recache($hostid=1)
{
	global $DB,$category;
	$dataArr=array();
	$files= $DB->query('SELECT * FROM `rqcms_'.$hostid.'_article` where stick=1 and visible=1 ORDER BY aid DESC limit 20');
	while ($fs = $DB->fetch_array($files)) 
	{
		$dataArr['data'][$fs['aid']]=showArticle($fs);
		$dataArr['aids'][0][]=$fs['aid'];
	}
	foreach($category as $cateid=>$cname)
	{
		$query= $DB->query('SELECT * FROM `rqcms_'.$hostid."_article` where stick=1 and visible=1 and cateid={$cateid} ORDER BY aid DESC limit 20");
		while ($fs = $DB->fetch_array($query)) 
		{
			$dataArr['data'][$fs['aid']]=showArticle($fs);
			$dataArr['aids'][$cateid][]=$fs['aid'];
		}
	}
	writeCache('stick_'.$hostid,$dataArr);
}

//阅读排行的文章
function hot_recache($hostid=1)
{
	global $DB,$category;
	$query=$DB->query('Select * from rqcms_'.$hostid."_article where visible=1 order by views desc limit 20");
	$cache=array();
	while($article=$DB->fetch_array($query))
	{
		$cache['data'][$article['aid']]=showArticle($article);
		$cache['aids'][0][]=$article['aid'];
	}
	foreach($category as $cateid=>$cname)
	{
		$query= $DB->query("SELECT * FROM `rqcms_".$hostid."_article` where visible=1 and cateid={$cateid} order by views desc limit 20");
		while ($fs = $DB->fetch_array($query)) 
		{
			$cache['data'][$fs['aid']]=showArticle($fs);
			$cache['aids'][$cateid][]=$fs['aid'];
		}
	}
	
	writeCache('hot_'.$hostid,$cache);
}

//最新20条搜索内容
function search_recache($hostid=1)
{
	global $DB;
	$query=$DB->query('Select distinct keywords from rqcms_'.$hostid."_search  order by dateline desc limit 20");
	$cache=array();
	while($data=$DB->fetch_array($query))
	{
		$cache[]=$data[keywords];
	}
	writeCache('search_'.$hostid,$cache);
}