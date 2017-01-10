<?php
if(!$action) $action='cache';

//系统管理包含一个 缓存生成 , 重新统计,日志查看
$cachedb=array();
$url = $admin_url.'?file=maintenance';

if(RQ_POST)
{
	if ($action == 'cache') {
		setting_recache($hostid);
		stick_recache($hostid);
		category_recache($hostid);
		latest_recache($hostid);
		hot_recache($hostid);
		search_recache($hostid);
		redirect('所有缓存已经更新', $url);
	}
	else if($action == 'log') 
	{
		include RQ_CORE.'/manager/log.php';
	}
}
else
{
	if($action=='log'&&!in_array($do,array('login','search','dberror'))) $do='login';
	if($action == 'log') 
	{
		include RQ_CORE.'/manager/log.php';
	}
	else if($action=='cache')
	{
		$cachefile=array(
		'filemap_'.$hostid=>'映射文件',
		'stick_'.$hostid=>'置顶文章',
		'latest_'.$hostid=>'栏目最新文件',
		'search_'.$hostid=>'最新搜索记录',
		'hot_'.$hostid=>'阅读排行文件');
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