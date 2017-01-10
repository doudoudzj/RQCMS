<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
if(!$action) $action='cache';

//系统管理包含一个 缓存生成 , 重新统计,日志查看
$cachedb=array();
$url = 'admin.php?file=maintenance';

if(RQ_POST)
{
	if ($action == 'cache') {
		filemaps_recache();
		plugins_recache();
		links_recache();
		stick_recache();
		comments_recache();
		rss_recache();
		cates_recache();
		vars_recache();
		pics_recache();
		latest_recache();
		redirect('所有缓存已经更新', $url);
	}
}
else
{
	if($action == 'log') 
	{
		if(!in_array($do,array('login','search','visit'))) $do='login';
		
		if($page) 
		{
			$start_limit = ($page - 1) * 30;
		}
		else 
		{
			$start_limit = 0;
			$page = 1;
		}
		$searchs  = $DB->query("SELECT * FROM ".DB_PREFIX."log where `type`='$do'");
		$tatol     = $DB->num_rows($searchs);
		$multipage = multi($tatol, 30, $page, "admin.php?file=maintenance&action=log&do=$do");
		$searchdb = array();
		$query = $DB->query("SELECT * FROM ".DB_PREFIX."log where `type`='$do' ORDER BY lid DESC LIMIT $start_limit, 30");
		while ($search = $DB->fetch_array($query)) {
			$search['dateline'] = date('Y-m-d H:i',$search['dateline']);
			$searchdb[] = $search;
		}//end while
		unset($search);
		$DB->free_result($query);
	}
	else if($action=='cache')
	{
		$cachefile=array('rss_'.$host['host']=>'Rss文件',
		'var_'.$host['host']=>'自定义模板变量',
		'map_'.$host['host']=>'映射文件',
		'comments_'.$host['host']=>'最新评论',
		'stick_'.$host['host']=>'置顶文章',
		'tag_'.$host['host']=>'热门Tag文件',
		'pic_'.$host['host']=>'包含图片的文章',
		'cate_'.$host['host']=>'分类信息',
		'latest_'.$host['host']=>'栏目最新文件');
		foreach($cachefile as $cfile=>$desc)
		{
			$filepath = RQ_DATA.'/cache/'.$cfile.'.php';
			if(is_file($filepath))
			{
				$cachefile['name'] = $cfile.'.php';
				$cachefile['desc'] = $desc;
				$cachefile['size'] = sizecount(filesize($filepath));
				$cachefile['mtime'] = date('Y-m-d H:i',@filemtime($filepath));
				$cachedb[] = $cachefile;
			}
		}
	}
}

