<?php
if(!defined('RQ_ROOT')) exit('Access Denied');

if(!in_array($do,array('login','search','spider','dberror'))) $do='login';
if($page) 
{
	$start_limit = ($page - 1) * 30;
}
else 
{
	$start_limit = 0;
	$page = 1;
}
if($do=='dberror')
{
	//todo查询文本文件
	/*
	$logfilename = RQ_DATA.'/logs/'.$logsfile.'.php';
if(file_exists($logfilename)){
$logfile = @file($logfilename);
} else{
$logfile=array();
}
$logs = array();
if(is_array($logfile)) {
foreach($logfile as $log) {
	$logs[] = $log;
}
}
$logs = @array_reverse($logs);
$tatol = count($logs);
if ($tatol>100) {
$output=@array_slice($logs,0,100);
$output=@array_reverse($output);
$output=@implode("",$output);

@touch($logfilename);
@$fp=fopen($logfilename,'rb+');
@flock($fp,LOCK_EX);
@fwrite($fp,$output);
@ftruncate($fp,strlen($output));
@fclose($fp);
@chmod($filename,0777);

redirect('多余的'.$opname.'已成功删除', 'admin.php?file=log&action='.$logsfile);*/
}
else
{
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
