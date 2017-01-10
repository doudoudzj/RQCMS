<?php
/**
 * sablog 1.6 to rqcms 转换程序
 *
 * @copyright  Copyright (c) 2010-2011 RQ204
 * @license    GNU General Public License 2.0
 */
error_reporting(E_ALL);
$dbhost='localhost';
$dbuser='rq204';
$dbname='rqcms';
$dbpassword='rq204';
$dbprefix='sablog_';
$db=new DB_MySQL();
$db->connect($dbhost,$dbuser,$dbpassword,$dbname);

$dbprefix2='rqcms_';
$uid='1';
$hostid='1';
$username='rq204';

//处理分类
$catesarr=array();
$cquery=$db->query("select * from {$dbprefix}categories order by cid asc");
while($ct=$db->fetch_array($cquery))
{
	$name=$ct['name'];
	$cid=$ct['cid'];
	$displayorder=$ct['displayorder'];
	$db->query("Insert into {$dbprefix2}category (`oid`,`hostid`,`name`,`displayorder`) values ('$cid','$hostid','$name','$displayorder')");
	$catesarr[$ct['cid']]=$db->insert_id();
}

//处理文章
$artarr=array();
$aquery=$db->query("Select * from {$dbprefix}articles order by articleid asc");
while($at=$db->fetch_array($aquery))
{
	$oid=$at['articleid'];
	$title=addslashes ($at['title']);
	$content=addslashes ($at['content']);
	$ocid=$at['cid'];
	$cid=$catesarr[$ocid];
	$des=addslashes ($at['description']);
	$tag=$at['keywords'];
	$tags=explode(',',$tag);
	$views=$at['views'];
	$dateline=$at['dateline'];
	$comments=$at['comments'];
	$closecomment=$at['closecomment'];
	$visible=$at['visible'];
	$stick=$at['stick'];
	$readpassword=$at['readpassword'];
	$sql="insert into {$dbprefix2}article (`oid`,`hostid`,`cateid`,`userid`,`username`,`title`,`tag`,`excerpt`,`dateline`,`modified`,`views`,`comments`,`comment`,`visible`,`stick`,`password`,`content`) values ('$oid','$hostid','$cid','$uid','$username','$title','$tag','$des','$dateline','$dateline','$views','$comments','$closecomment','$visible','$stick','$readpassword','$content')";
	//exit($sql);
	$db->query($sql);
	$aid=$db->insert_id();
	$artarr[$oid]=$aid;
	echo $aid,"\r\n";
	
	if(is_array($tags))
	{
		foreach($tags as $tg)
		{
			$sql2="Insert into {$dbprefix2}tag (`tag`,`hostid`,`articleid`) values ('$tg','$hostid','$aid') ";
			$db->query($sql2);
		}
	}
}


//处理附件
$attarr=array();//用于更新文章的附件用的
$fquery=$db->query("Select * from {$dbprefix}attachments order by attachmentid asc");
while($ft=$db->fetch_array($fquery))
{
	$oaid=$ft['attachmentid'];
	$oarticleid=$ft['articleid'];
	if(isset($artarr[$oarticleid]))
	{
		$dateline=$ft['dateline'];
		$articleid=$artarr[$oarticleid];
		$filename=$ft['filename'];
		$filetype=$ft['filetype'];
		$filesize=$ft['filesize'];
		$filepath=$ft['filepath'];
		if($filepath&&$filepath[0]=='/') $filepath=substr($filepath,1,strlen($filepath)-1);
		$downloads=$ft['downloads'];
		$thumb_filepath=$ft['thumb_filepath'];
		if($thumb_filepath&&$thumb_filepath[0]=='/') $thumb_filepath=substr($thumb_filepath,1,strlen($thumb_filepath)-1);
		$thumb_width=$ft['thumb_width'];
		$thumb_height=$ft['thumb_height'];
		$isimage=$ft['isimage'];

		$sql3="Insert into {$dbprefix2}attachment(`hostid`,`articleid`,`dateline`,`filename`,`filetype`,`filesize`,`downloads`,`filepath`,`thumb_filepath`,`thumb_width`,`thumb_height`,`isimage`) values ('$hostid','$articleid','$dateline','$filename','$filetype','$filesize','$downloads','$filepath','$thumb_filepath','$thumb_width','$thumb_height','$isimage')";
		
		$db->query($sql3);
		$aid=$db->insert_id();
		$attarr[$articleid]['att'][]=array('filename'=>$filename,'filetype'=>$filetype,'filesize'=>$filesize,'thumb_width'=>$thumb_width,'thumb_height'=>$thumb_height,'isimage'=>$isimage,'aid'=>$aid);
		$attarr[$articleid]['oid'][$aid]=$oaid;
	}
}

//处理用户
$userarr=array();
$usernamearr=array();
$uquery=$db->query("select * from {$dbprefix}users");
while($us=$db->fetch_array($uquery))
{
	$userid=$us['userid'];
	$username=$us['username'];
	$password=$us['password'];
	$logincount=$us['logincount'];
	$loginip=$us['loginip'];
	$logintime=$us['logintime'];
	$url=$us['url'];
	$articles=$us['articles'];
	$regdateline=$us['regdateline'];
	$regip=$us['regip'];
	$lastpost=$us['lastpost'];
	$email='';
	
	if(!empty($url))
	{
		if(strpos($url,'@')==true)
		{
			$email=$url;
			$url='';
		}
	}
	
	$usql="Insert into {$dbprefix2}user (`hostid`,`username`,`password`,`groupid`,`email`,`url`,`articles`,`regdateline`,`regip`,`logincount`,`loginip`,`logintime`,`lastpost`) values ('$hostid','$username','$password','1','$email','$url','$articles','$regdateline','$regip','$logincount','$loginip','$logintime','$lastpost')";
	$db->query($usql);
	$uid=$db->insert_id();
	$userarr[$userid]=$uid;
	$usernamearr[$username]=$uid;
}


//处理留言
$commentarr=array();
$coquery=$db->query("select * from {$dbprefix}comments");
while($co=$db->fetch_array($coquery))
{
	$oarticleid=$co['articleid'];
	$articleid=$artarr[$oarticleid];
	$url=$co['url'];
	$dateline=$co['dateline'];
	$content=addslashes($co['content']);
	$ipaddress=$co['ipaddress'];
	$username=$co['author'];
	$email="";
	$userid='0';
	
	if(!empty($url))
	{
		if(strpos($url,'@')==true)
		{
			$email=$url;
			$url='';
		}
	}
	
	if(isset($usernamearr[$username]))
	{
		$userid=$usernamearr[$username];
	}
	
	$cmsql="insert into {$dbprefix2}comment (`hostid`,`articleid`,`userid`,`username`,`dateline` ,`content` ,`ipaddress`,`url`,`email`) values ('$hostid','$articleid','$userid','$username','$dateline','$content','$ipaddress','$url','$email')";
	$db->query($cmsql);
	if(isset($commentarr[$articleid])) $commentarr[$articleid]=$commentarr[$articleid]+1;
	else $commentarr[$articleid]=1;
}
echo "\r\ncomments:\r\n";
print_r($commentarr);
//更新文章中的附件和留言数
if(!empty($attarr))
{
	foreach($attarr as $k=>$v)
	{
		$article=$db->fetch_first("select * from {$dbprefix2}article where aid='$k'");
		$content=$article['content'];
		$articleid=$article['aid'];
		foreach($v['oid'] as $nid=>$oid)
		{
			$content=str_replace("[attach=$oid]","[attachs=$nid]",$content);
		}
		
		$content=str_replace("[attachs=","[attach=",$content);
		$content=addslashes($content);
		$attachments=addslashes(serialize($v['att']));
		$comments=0;
		if(isset($commentarr[$articleid]))
		{
			$comments=$commentarr[$articleid];
			unset($commentarr[$articleid]);
		}
		$db->query("update {$dbprefix2}article set content='$content',attachments='$attachments',comments='$comments' where aid='$k'");
	}
}
$nullatt=addslashes(serialize(array()));
$db->query("update {$dbprefix2}article set attachments='$nullatt' where attachments=''");
if(!empty($commentarr))
{
	foreach($commentarr as $k=>$v)
	{
		$db->query("update {$dbprefix2}article set comments='$v' where aid=$k");
	}
}


//自定义变量
$squery=$db->query("Select * from {$dbprefix}stylevars order by stylevarid asc");
while($st=$db->fetch_array($squery))
{
	$title=addslashes($st['title']);
	$value=addslashes($st['value']);
	$visible=$st['visible'];
	$sqls="Insert into {$dbprefix2}var (`hostid`,`type`,`title`,`value`,`visible`) values ('$hostid','style','$title','$value','$visible')";
	$db->query($sqls);
}

//友情链接
$lquery=$db->query("Select * from {$dbprefix}links order by linkid asc");
while($st=$db->fetch_array($lquery))
{
	$displayorder=$st['displayorder'];
	$name=addslashes($st['name']);
	$url=addslashes($st['url']);
	$note=addslashes($st['note']);
	$visible=$st['visible'];
	
	$lsql="Insert into {$dbprefix2}link (`hostid`,`displayorder`,`name`,`url`,`note`,`visible`) values ('$hostid','$displayorder','$name','$url','$note','$visible')";
	$db->query($lsql);
}


exit('success');

class DB_MySQL  {

	var $querycount = 0;

	function geterrdesc() {
		return mysql_error();
	}

	function geterrno() {
		return intval(mysql_errno());
	}

	function insert_id() {
		$id = mysql_insert_id();
		return $id;
	}

	function connect($servername, $dbusername, $dbpassword, $dbname, $usepconnect=0) {
		if($usepconnect) {
			if(!@mysql_pconnect($servername, $dbusername, $dbpassword)) {
				$this->halt('数据库链接失败');
			}
		} else {
			if(!@mysql_connect($servername, $dbusername, $dbpassword)) {
				$this->halt('数据库链接失败');
			}
		}

		if($this->version() > '4.1') {
			$charset=$dbcharset='utf8';
			if(!$dbcharset && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8'))) {
				$dbcharset = str_replace('-', '', $charset);
			}
			if($dbcharset) {
				//mysql_query("SET NAMES '$dbcharset'");
				mysql_query("SET character_set_connection=$dbcharset, character_set_results=$dbcharset, character_set_client=binary;");
			}
		}

		if($this->version() > '5.0.1') {
			mysql_query("SET sql_mode=''");
		}
		if($dbname) {
			$this->select_db($dbname);
		}
	}

	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}

	function query($sql, $type = '') {
		//echo "<div style=\"text-align: left;\">".htmlspecialchars($sql)."</div>";
		/*
		遇到问题时用这个来检查SQL执行语句
		$fp = fopen('sqlquerylog.txt', 'a');
		flock($fp, 2);
		fwrite($fp, $sql."\n");
		fclose($fp);
		*/
		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ?
			'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($sql)) && $type != 'SILENT') {
			$this->halt('MySQL Query Error', $sql);
		}
		$this->querycount++;
		return $query;
	}
	
	function unbuffered_query($sql) {
		$query = $this->query($sql, 'UNBUFFERED');
		return $query;
	}

	function select_db($dbname) {
		return mysql_select_db($dbname);
	}

	function fetch_row($query) {
		$query = mysql_fetch_row($query);
		return $query;
	}

	function fetch_first($sql) {
		$result = $this->query($sql);
		$record = $this->fetch_array($result);
		return $record;
	}

	function num_rows($query) {
		$query = mysql_num_rows($query);
		return $query;
	}

	function num_fields($query) {
		return mysql_num_fields($query);
	}
	
	function result($query, $row) {
		$query = @mysql_result($query, $row);
		return $query;
	}
	
	function free_result($query) {
		$query = mysql_free_result($query);
		return $query;
	}

	function version() {
		return mysql_get_server_info();
	}

	function close() {
		return mysql_close();
	}

	function halt($msg, $sql=''){
		global $username,$timestamp,$onlineip;

		if ($sql) {
			@$fp = fopen(RQ_DATA.'/logs/dberrorlog.php', 'a');
			@fwrite($fp, "<?PHP exit('Access Denied'); ?>\t$username\t$timestamp\t$onlineip\t".basename(RQ_FILE)."\t".htmlspecialchars($this->geterrdesc())."\t".str_replace(array("\r", "\n", "\t"), array(' ', ' ', ' '), trim(htmlspecialchars($sql)))."\n");
			@fclose($fp);
		}

		$message = "<html>\n<head>\n";
		$message .= "<meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\">\n";
		$message .= "<style type=\"text/css\">\n";
		$message .=  "body,p,pre {\n";
		$message .=  "font:12px Verdana;\n";
		$message .=  "}\n";
		$message .=  "</style>\n";
		$message .= "</head>\n";
		$message .= "<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#006699\" vlink=\"#5493B4\">\n";

		$message .= "<p>数据库出错:</p><pre><b>".htmlspecialchars($msg)."</b></pre>\n";
		$message .= "<b>Mysql error description</b>: ".htmlspecialchars($this->geterrdesc())."\n<br />";
		$message .= "<b>Mysql error number</b>: ".$this->geterrno()."\n<br />";
		$message .= "<b>Date</b>: ".date("Y-m-d @ H:i")."\n<br />";
		$message .= "<b>Script</b>: http://".$_SERVER['HTTP_HOST'].getenv("REQUEST_URI")."\n<br />";

		$message .= "</body>\n</html>";
		echo $message;
		exit;
	}
}
?>