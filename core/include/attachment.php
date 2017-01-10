<?php
if(!defined('RQ_ROOT')) exit('Access Denied');

// 获取附件大小





// 获得文件扩展名

function getextension($filename) {
	$pathinfo = pathinfo($filename);
	return $pathinfo['extension'];
}







// 删除附件函数
function removeattachment($attacharr) {
	global $DB, $db_prefix, $options;
	$attachids = 0;
	$attachnum = count($attacharr);
	if ($attacharr && $attachnum) {
		$filepath = '../'.$options['attachments_dir'];
		foreach ($attacharr as $attachid => $attach) {
			$attachids .= ','.intval($attachid);
			@chmod ($filepath.$attach['filepath'], 0777);
			@unlink($filepath.$attach['filepath']);
			if ($attach['thumb_filepath']) {
				@chmod ($filepath.$attach['thumb_filepath'], 0777);
				@unlink($filepath.$attach['thumb_filepath']);
			}
		}
		$DB->unbuffered_query("DELETE FROM ".DB_PREFIX."attachment WHERE aid IN ($attachids)");
	}
}

?>