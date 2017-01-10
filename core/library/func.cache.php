<?php
// 站点缓存更新
function hosts_recache()
{
	global $DB;
	$contents=array();
	$hosts = $DB->query('SELECT * FROM `'.DB_PREFIX.'host`');
	while ($arrhosts = $DB->fetch_array($hosts)) 
	{
		$contents[$arrhosts['host']]=$arrhosts;
	}
	writeCache('hosts',$contents);
}

// 更新映射文件
function filemaps_recache()
{
	global $DB,$hostid;
	$add=$hostid?" where h.hid=$hostid and f.hostid=$hostid":'';
	$files= $DB->query('SELECT f.*,h.host,h.hid FROM `'.DB_PREFIX.'filemap` f,`'.DB_PREFIX.'host` h'.$add);
	$arrfiles=array();
	while ($fs = $DB->fetch_array($files)) 
	{
		$args=array();
		if($fs['maps'])
		{
			$arr=explode(',',$fs['maps']);
			foreach($arr as $arg)
			{
				$ag=explode('=',$arg);
				if(count($ag)==2&&$ag[0]&&$ag[1]) $args[$ag[0]]=$ag[1];
			}
		}
		$arrfiles[$fs['host']][$fs['filename']]=array($fs['original'],$args);
	}

	foreach($arrfiles as $k=>$filemap)
	{
		writeCache('map_'.$k,$filemap);
	}
	if(empty($arrfiles)) empty_recache('map');
}

// 插件缓存
function plugins_recache() {
	global $DB;
	$query= $DB->query('SELECT p.*,h.host FROM `'.DB_PREFIX.'plugin` p,`'.DB_PREFIX.'host` h where p.active=1');
	$plugins=array();
	while ($ps = $DB->fetch_array($query)) 
	{
		$plugins[$ps['host']][]=$ps['file'];
		$plugins['data'][$ps['host']][$ps['file']]=$ps['config'];
	}
	writeCache('plugins',$plugins);
}

// 评论缓存
function comments_recache()
{
	global $DB,$hostid;
	$add=$hostid?" where hid=$hostid":'';
	$query=$DB->query('select * from `'.DB_PREFIX.'host`'.$add);
	while($hostinfo=$DB->fetch_array($query))
	{
		$hostid=$hostinfo['hid'];
		$comments = $DB->query('SELECT * from `'.DB_PREFIX."comment`  WHERE visible = 1 and hostid='$hostid' ORDER BY cid desc");
		$commentdb = array();
		while ($comment= $DB->fetch_array($comments))
		{
			$commentdb[] = $comment;
		}
		writeCache('comment_'.$hostinfo['host'],$commentdb);
	}
	if(empty($comments)) empty_recache('comment');
}

// 链接缓存
function links_recache()
{
	global $DB,$hostid;
	$links = $DB->query('SELECT l.*,h.host FROM `'.DB_PREFIX.'link` l,`'.DB_PREFIX.'host` h WHERE l.visible = 1 and l.hostid=h.hid ORDER BY l.displayorder ASC, l.name ASC');
	$linkdb = array();
	while ($link = $DB->fetch_array($links))
	{
		$linkdb[$link['host']][] = $link;
	}
	unset($link);
	writeCache('links',$linkdb);
}

// rss缓存
function rss_recache()
{
	global $DB,$hostid;
	$add=$hostid?" where hid=$hostid":'';
	$arrfiles=array();
	$query=$DB->query('select * from `'.DB_PREFIX.'host`'.$add);
	while($host=$DB->fetch_array($query))
	{
		$rquery= $DB->query('SELECT a.*,h.host,h.hid FROM `'.DB_PREFIX.'article` a,`'.DB_PREFIX.'host` h where a.hostid='.$host['hid'].' and h.hid='.$host['hid'].' ORDER BY a.aid DESC limit '.$host['rss_num']);
		while($rss=$DB->fetch_array($rquery))
		{
			unset($rss['content']);
			$arrfiles[$host['host']][]=showArticle($rss);
		}
	}
	foreach($arrfiles as $k=>$v)
	{
		writeCache('rss_'.$k,$v);
	}
	if(empty($arrfiles)) empty_recache('rss');
}

// 置顶
function stick_recache()
{
	global $DB,$hostid;
	$add=$hostid?" and h.hid=$hostid and a.hostid=$hostid":'';
	$files= $DB->query('SELECT a.*,h.host FROM `'.DB_PREFIX.'article` a,`'.DB_PREFIX.'host` h where a.stick=1 '.$add.' ORDER BY a.aid DESC limit 100');
	$arrfiles=array();
	while ($fs = $DB->fetch_array($files)) 
	{
		unset($fs['content']);
		$arrfiles[$fs['host']][]=showArticle($fs);
	}
	foreach($arrfiles as $k=>$v)
	{
		writeCache('stick_'.$k,$v);
	}
	if(empty($arrfiles)) empty_recache('stick');
}

//分类及系统设置参数
function cates_recache()
{
	global $DB,$hostid;
	$add=$hostid?" where hid=$hostid":'';
	$query=$DB->query('select * from `'.DB_PREFIX.'host`'.$add);
	while($cates=$DB->fetch_array($query))
	{
		$arrcates=array();
		$hid=$cates['hid'];
		$cquery= $DB->query('SELECT c.*,h.host FROM `'.DB_PREFIX.'category` c,`'.DB_PREFIX."host` h where h.hid='$hid' and c.hostid='$hid'");
		while($cate=$DB->fetch_array($cquery))
		{
			$arrcates[$cate['cid']]=showCategory($cate);
		}
		writeCache('cate_'.$cates['host'],$arrcates);
	}
}

//全局变量参数
function vars_recache()
{
	global $DB,$hostid;
	$add=$hostid?" and h.hid=$hostid and c.hostid=$hostid":'';
	$var= $DB->query('SELECT c.*,h.host FROM `'.DB_PREFIX.'var` c,`'.DB_PREFIX."host` h where c.visible=1 and c.type='style' $add");
	$arrvars=array();
	while ($fs = $DB->fetch_array($var)) 
	{
		$arrvars[$fs['host']][$fs['title']]=$fs['value'];
	}
	foreach($arrvars as $k=>$v)
	{
		writeCache('var_'.$k,$v);
	}
	if(empty($arrvars)) empty_recache('var');
}

//图片文章
function pics_recache()
{
	global $DB,$hostid;
	$add=$hostid?" and h.hid=$hostid and a.hostid=$hostid":'';
	$var= $DB->query('SELECT a.*,h.host,d.* FROM `'.DB_PREFIX.'article` a,`'.DB_PREFIX."host` h,".DB_PREFIX."attachment d where a.thumb>0 and a.thumb=d.aid and a.visible=1 $add order by a.aid desc limit 20");
	$arrvars=array();
	while ($fs = $DB->fetch_array($var)) 
	{
		unset($fs['content']);
		$arrvars[$fs['host']][]=showArticle($fs);
	}
	foreach($arrvars as $k=>$v)
	{
		writeCache('pic_'.$k,$v);
	}
	if(empty($arrvars)) empty_recache('pic');
}

function latest_recache()
{
	global $DB,$hostid,$host;
	$cache=array();
	$add=$hostid?" and h.hid=$hostid and c.hostid=$hostid":'';
	$query= $DB->query('SELECT c.cid,c.oid,c.name,c.url,h.host,h.hid from '.DB_PREFIX."category c ,".DB_PREFIX."host h where c.visible=1 $add");
	while($catearr=$DB->fetch_array($query))
	{
		$cid=$catearr['cid'];
		$hname=$catearr['host'];
		$artquery=$DB->query('Select * from '.DB_PREFIX."article where visible=1 and cateid=$cid order by aid desc limit {$host['listcachenum']}");
		while($artarr=$DB->fetch_array($artquery))
		{
			unset($artarr['content']);
			$cache[$hname]['article'][$artarr['aid']]=showArticle($artarr);
			$cache[$hname]['cateids'][$cid][]=$artarr['aid'];
		}
		if($DB->num_rows($artquery)==0)
		{
			$cache[$hname]['cateids'][$cid]=array();
		}
	}
	$aquery=$DB->query('Select c.*,h.host from '.DB_PREFIX."article c,".DB_PREFIX."host h where visible=1 $add order by c.aid desc limit {$host['listcachenum']}");
	while($top=$DB->fetch_array($aquery))
	{
		unset($top['content']);
		$cache[$top['host']]['article'][$top['aid']]=showArticle($top);
		$cache[$top['host']]['cateids'][0][]=$top['aid'];
	}

	foreach($cache as $h=>$v)
	{
		$cateids=$v['cateids'];
		$arr=array('article'=>$v['article'],'cateids'=>$v['cateids']);
		writeCache('latest_'.$h,$arr);
	}
	if(empty($cache)) empty_recache('latest');
}

// 更新自动转向
function redirect_recache()
{
	global $DB,$hostid;
	$add=$hostid?" and h.hid=$hostid and c.hostid=$hostid":'';
	$var= $DB->query('SELECT c.*,h.host FROM `'.DB_PREFIX.'var` c,`'.DB_PREFIX."host` h where `type`='redirect' and c.type='style' $add");
	$arrvars=array();
	while ($fs = $DB->fetch_array($var)) 
	{
		$arrvars[$fs['host']][$fs['title']]=array($fs['value'],$fs['visible']);
	}
	foreach($arrvars as $k=>$v)
	{
		writeCache('redirect_'.$k,$v);
	}
	if(empty($arrvars)) empty_recache('redirect');
}

function empty_recache($cache)
{
	global $DB,$hostid;
	$add=$hostid?" where hid=$hostid":'';
	$hosts = $DB->query('SELECT * FROM `'.DB_PREFIX.'host`'.$add);
	while ($arrhosts = $DB->fetch_array($hosts)) 
	{
		writeCache($cache.'_'.$arrhosts['host'],array());
	}
}