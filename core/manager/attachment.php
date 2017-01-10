<?php
if(!defined('RQ_ROOT')) exit('Access Denied');if (!$action) $action = 'list';

if(RQ_POST)
{
	switch($action)
	{
		case 'delattachments':	//批量删除附件
			if ($attachmentids = implode_ids($_POST['attachment'])) {
				$nokeep = array();
				$query  = $DB->query("SELECT attachmentid,filepath,thumb_filepath FROM ".DB_PREFIX."attachments WHERE attachmentid IN ($attachmentids)");
				while($attach = $DB->fetch_array($query)) {
					$nokeep[$attach['attachmentid']] = $attach;
				}
				removeattachment($nokeep);
				statistics_recache();
				$articleid = intval($_POST['articleid']);
				redirect('成功删除所选附件,如果删除数量很多.建议执行一次附件修复操作,更新文章中的附件信息以提高访问速度.', 'admin.php?file=attachment&action=list&articleid='.$articleid,'10');
			} else {
				redirect('未选择任何附件');
			}
		break;
		case 'dorepair':
			$query = $DB->query("SELECT aid,filepath FROM ".DB_PREFIX."attachment");
			while ($attach = $DB->fetch_array($query)) {
				if(!file_exists(SACMS_ROOT.$options['attachments_dir'].'/'.$attach['filepath'])){
					$DB->unbuffered_query("DELETE FROM ".DB_PREFIX."attachments WHERE attachmentid='".$attach['attachmentid']."'");
				}
			}
			unset($attach,$query);
			$count = 0;
			$query = $DB->query("SELECT articleid,attachments FROM ".DB_PREFIX."articles WHERE attachments <> ''");
			while ($aids = $DB->fetch_array($query)) {
				$attachs = unserialize(stripslashes_array($aids['attachments']));
				if (is_array($attachs)) {
					$update=0;
					foreach ($attachs as $key=>$attach) {
						if($attach['filepath'] && file_exists(SACMS_ROOT.$options['attachments_dir'].'/'.$attach['filepath'])){
							$check = $DB->fetch_first("SELECT attachmentid FROM ".DB_PREFIX."attachments WHERE attachmentid='".$attach['attachmentid']."'");	
							if (!$check) {
								$count++;
								$attach['filename'] = addslashes($attach['filename']);
								$DB->query("INSERT INTO ".DB_PREFIX."attachments (filename, filesize, filetype, filepath, dateline, downloads, isimage, thumb_filepath, thumb_width, thumb_height) VALUES ('".addslashes($attach['filename'])."', '".addslashes($attach['filesize'])."', '".addslashes($attach['filetype'])."', '".addslashes($attach['filepath'])."', '".addslashes($attach['dateline'])."', '".addslashes($attach['downloads'])."', '".addslashes($attach['isimage'])."', '".addslashes($attach['thumb_filepath'])."', '".addslashes($attach['thumb_width'])."', '".addslashes($attach['thumb_height'])."')");
							}
						} else{
							$count++;
							$check = $DB->fetch_first("SELECT attachmentid FROM ".DB_PREFIX."attachments WHERE attachmentid='".$attach['attachmentid']."'");
							if($check){
								$DB->unbuffered_query("DELETE FROM ".DB_PREFIX."attachments WHERE attachmentid='".$attach['attachmentid']."'");
							}
							$update=1;
							unset($attachs[$key]);
						}
					}
					if($update){
						$attachs = $attachs ? addslashes(serialize($attachs)) : '';
						$DB->unbuffered_query("UPDATE ".DB_PREFIX."articles SET attachments='$attachs' WHERE articleid='".$aids['articleid']."'");
					}
				} else{
					$count++;
					$DB->unbuffered_query("UPDATE ".DB_PREFIX."articles SET attachments='' WHERE articleid='".$aids['articleid']."'");
				}
			}
			// 更新首页显示的附件数
			$attachment_count = $DB->num_rows($DB->query("SELECT att.attachmentid, art.visible FROM ".DB_PREFIX."attachments att LEFT JOIN ".DB_PREFIX."articles art ON (art.articleid = att.articleid) WHERE art.visible='1'"));
			$DB->unbuffered_query("UPDATE ".DB_PREFIX."statistics SET attachment_count='$attachment_count'");	
			statistics_recache();
			redirect('成功修复'.$count.'个附件记录', 'admin.php?file=attachment&action=list');
		break;
		case 'addattachtoarticle':	//添加附件到指定文章
			$aid = intval($_POST['aid']);
			include_once RQ_CORE.'/include/article.php';
			$article = $DB->fetch_first("SELECT title,attachments,visible FROM ".DB_PREFIX."article WHERE aid='$aid' and hostid=$hostid");
			if(!$article) {
				redirect('文章不存在', 'admin.php?file=attachment');
			}

			// 修改附件
			$attachment_count=0;
			$attachments=getAttach();
			if($attachments&&is_array($attachments))
			{
				foreach($attachments as $key=>$attachment)
				{
					$DB->unbuffered_query("Insert into ".DB_PREFIX."attachment (`articleid`,`dateline`,`filename`,`filetype`,`filesize`,`filepath`,`thumb_filepath`,`thumb_width`,`thumb_height`,`isimage`,`hostid`) values ('$aid','$timestamp','$attachment[filename]','$attachment[filetype]','$attachment[filesize]','$attachment[filepath]','$attachment[thumb_filepath]','$attachment[thumb_width]','$attachment[thumb_height]','$attachment[isimage]','$hostid')");
					$attachments[$key]['aid']=$DB->insert_id();
					unset($attachments[$key]['filepath']);
					unset($attachments[$key]['thumb_filepath']);
					$attachment_count++;
				}
			}
			$attachstr=addslashes(serialize($oldattach));
			$DB->query('update '.DB_PREFIX."article set `attachments`=`attachments`+$attachment_count where aid='$aid'");
			redirect('成功上传了'.$attachment_count.'个附件到《'.$article['title'].'》', 'admin.php?file=attachment&action=list&aid='.$aid);
			break;
	}
}
else
{
	$view = in_array(isset($_GET['view'])?$_GET['view']:'', array('image', 'file','hot','big')) ? $_GET['view'] : '';
	if ($action == 'list') {
		if($page) {
			$start_limit = ($page - 1) * 30;
		} else {
			$start_limit = 0;
			$page = 1;
		}
		$sql = "WHERE 1";
		$subnav = '全部附件';
		if ($view == 'image') {
			$sql .= " AND (a.filetype LIKE '%image/%')";
			$subnav = '图片附件';
		} elseif ($view == 'file') {
			$sql .= " AND !(a.filetype LIKE '%image/%')";
			$subnav = '非图片附件';
		}elseif ($view == 'hot') {
			$order= "downloads";

		} elseif ($view == 'big') {
			$order = "filesize";
		}
		$aid =isset($_GET['aid'])?intval($_GET['aid']):'';
		if ($aid) {
			$article = $DB->fetch_first("SELECT title FROM ".DB_PREFIX."article WHERE aid='$aid' and hostid=$hostid");
			$subnav = '《'.$article['title'].'》的附件';
			$sql .= " AND a.articleid='$aid'";
		} else {
			$warning = '';
			$a_dir = RQ_DATA.'/files/';
			$dircount = dircount($a_dir);
			$stats = $DB->fetch_first("SELECT count(*) as count, sum(filesize) as sum FROM ".DB_PREFIX."attachment where hostid=$hostid");
			$stats['count'] = ($stats['count'] != 0) ? $stats['count'] : 0;
			$stats['sum'] = ($stats['count'] == 0) ? '0 KB' : sizecount($stats['sum']);
			if (!@is_dir($a_dir)) {
				$warning = ' <font color="#FF0000"><strong>(文件夹无效,请重新设定上传文件夹!)</strong></font>';
			}
		}

		$tatol = $DB->num_rows($DB->query("SELECT aid FROM ".DB_PREFIX."attachment a ".$sql.(isset($order)?" order by $order desc":'')));
		$aidadd=$aid?'&aid='.$aid:'';
		$multipage = multi($tatol, 30, $page, 'admin.php?file=attachment&action=list&view='.$view.$aidadd);
		$query = $DB->query("SELECT a.*,ar.title as article FROM ".DB_PREFIX."attachment a LEFT JOIN ".DB_PREFIX."article ar ON (ar.aid=a.articleid) $sql and ar.hostid=$hostid  ORDER BY ".(isset($order)?"$order DESC, ":'')."a.aid DESC LIMIT $start_limit, 30");

		$attachdb = array();
		while ($attach = $DB->fetch_array($query)) {
			$attach['thumb'] = $attach['thumb_filepath'] ? '<a href="attachment.php?id='.$attach['aid'].'type=thumb" target="_blank"><font color=#FF0000>有</font></a>' : '无';
			$attach['filename'] = htmlspecialchars($attach['filename']);
			$attach['filepath'] = htmlspecialchars($attach['filepath']);
			$attach['filesize'] = sizecount($attach['filesize']);
			$attach['filetype'] = htmlspecialchars($attach['filetype']);
			$attach['name'] = htmlspecialchars($attach['filename']);
			$attach['dateline'] = date("Y-m-d H:i",$attach['dateline']);
			$pathdata = explode('/',$attach['filepath']);
			if (count($pathdata) == 2) {
				$attach['subdir'] = $pathdata[0];
			} 
			else $attach['subdir']='';
			$attachdb[] = $attach;
		}
		$title=$attach['article'];
		unset($attach);
		$DB->free_result($query);
	}
	if($action == 'repair') $subnav = '附件修复';
	if($action == 'clear') $subnav = '附件清理';
	if($action == 'list') $subnav = '附件管理';
}