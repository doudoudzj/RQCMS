<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
/*
@文件下载
*/

if ($host['attachments_remote_open']) 
{	
	if(strpos($_SERVER['HTTP_REFERER'],RQ_HTTP.RQ_HOST)!=0) message('附件禁止从地址栏直接输入或从其他站点链接访问', './');
}

// 查询文章
$aid = intval($_GET['aid']);
if (!$aid)
{
	message('缺少参数', './');
} 
else 
{
	$attachinfo = $DB->fetch_first("select * from ".DB_PREFIX."attachment where aid='$aid'");
	if (!$attachinfo)
	{		message('附件不存在', './');
	}
	else
	{
		$DB->unbuffered_query("UPDATE ".DB_PREFIX."attachment SET downloads=downloads+1 WHERE aid='$aid'");
	}
}

if(RQ_CACHE) cacheControl($attachinfo['dateline']);

$filepath = RQ_DATA.'/files/'.$attachinfo['filepath'];
$filepath=str_replace('//','/',$filepath);

$attachment = $isimage ? 'inline' : 'attachment';
$attachinfo['filetype'] = $attachinfo['filetype'] ? $attachinfo['filetype'] : 'unknown/unknown';

if(is_readable($filepath)) 
{
	ob_end_clean();
	$ua = $_SERVER["HTTP_USER_AGENT"];
	$filename=urlencode($attachinfo['filename']);
	$filename=str_replace("+", "%20", $filename);
	
	if (preg_match("/MSIE/", $ua)) {
		header('Content-Disposition: '.$attachment.'; filename="' . $filename . '"');
	} else {
		header('Content-Disposition: '.$attachment.'; filename="' . $filename . '"');
	}
	header('Cache-control: max-age=31536000');
	header('Expires: ' . gmdate('D, d M Y H:i:s',$timestamp+31536000) . ' GMT');
	//header('Last-Modified: ' . gmdate('D, d M Y H:i:s',$attachinfo['dateline']) . ' GMT');
	header('Content-Encoding: none');
	header('Content-type: '.$attachinfo['filetype']);
	header('Content-Length: '.filesize($filepath));
	$fp = fopen($filepath, 'rb'); 
	fpassthru($fp);
	fclose($fp);
	exit;
}
else 
{
	message('读取附件失败', './');
}
?>