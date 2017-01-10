<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
exit('正在完善中...');
$action = in_array($action, array('adminlog', 'loginlog', 'deladminlog', 'delloginlog', 'dberrorlog', 'deldberrorlog')) ? $action : 'adminlog';
if (in_array($action, array('adminlog', 'deladminlog'))) {
	$logsfile = 'adminlog';
	$opname = '操作记录';
} elseif (in_array($action, array('loginlog', 'delloginlog'))) {
	$logsfile = 'loginlog';
	$opname = '登陆记录';
} elseif (in_array($action, array('dberrorlog', 'deldberrorlog'))) {
	$logsfile = 'dberrorlog';
	$opname = '数据库出错记录';
}
if (in_array($action, array('deladminlog', 'delloginlog', 'deldberrorlog'))) {
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

		redirect('多余的'.$opname.'已成功删除', 'admin.php?file=log&action='.$logsfile);
	} else {
		redirect('记录少于100条不允许删除', 'admin.php?file=log&action='.$logsfile);
	}
}//removelog

//管理日志页面
if (in_array($action, array('adminlog', 'loginlog', 'dberrorlog'))) {
	@$logfile = file(RQ_DATA.'/logs/'.$logsfile.'.php');
	$logs = $logdb = array();
	if(is_array($logfile)) {
		foreach($logfile as $log) {
			$logs[] = $log;
		}
	}
	$logs = @array_reverse($logs);

	if($page) {
		$start_limit = ($page - 1) * 30;
	} else {
		$start_limit = 0;
		$page = 1;
	}
	$tatol = count($logs);
	if ($tatol) {
		$multipage = multi($tatol, 30, $page, 'admin.php?file=log&action='.$logsfile);
		for($i = 0; $i < $start_limit; $i++) {
			unset($logs[$i]);
		}
		for($i = $start_limit + 30; $i < $tatol; $i++) {
			unset($logs[$i]);
		}
		if ($action == 'adminlog') {
			foreach($logs as $logrow) {
				$logrow = explode("\t", $logrow);
				$logrow[1] = sadate('Y-m-d H:i:s', $logrow[1]);
				$logdb[] = $logrow;
			}
		} elseif ($action == 'loginlog') {
			foreach($logs as $logrow) {
				$logrow = explode("\t", $logrow);
				$logrow[1] = $logrow[1] ? htmlspecialchars($logrow[1]) : '<span class="no">Null</span>';
				$logrow[2] = sadate('Y-m-d H:i:s', $logrow[2]);
				$logrow[4] = trim($logrow[4]) == 'Succeed' ? '<span class="yes">Succeed</span>' : '<span class="no">Failed</span>';
				$logdb[] = $logrow;
			}
		} else {
			foreach($logs as $logrow) {
				$logrow = explode("\t", $logrow);
				$logrow[1] = sadate('Y-m-d H:i:s', $logrow[1]);
				$logdb[] = $logrow;
			}
		}
	}
	$subnav = $opname;
	unset($logrow);
}//end

