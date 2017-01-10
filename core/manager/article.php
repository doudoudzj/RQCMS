<?php
include RQ_CORE.'/include/article.php';
include RQ_CORE.'/include/tag.php';
include RQ_CORE.'/library/func.convert.php';
if(empty($action)) $action='list';

if(RQ_POST)
{	
	$article=isset($_POST['article'])?$_POST['article']:array();
	$content=isset($_POST['content'])?$_POST['content']:array();
	$article['modified']=$timestamp;
	
	if(in_array($action,array('add','mod')))
	{
		$article['title'] = trim($article['title']);
		$article['cateid'] = intval($article['cateid']);
		$article['keywords'] = trim($article['keywords']);
		$article['stick'] = isset($article['stick'])?intval($article['stick']):0;
		$article['dateline'] = isset($article['edittime'])?getDateLine():$timestamp;
		$tags        = getTagArr(trim($article['tag']));
		$attachments=getAttach();//一个序列化的结果,附件名,Id,大小
		$attcount=count($attachments);
		$article['attachments']=$attcount;
		$attachInfo=array();//
		$article['tag']=!empty($tags)?implode(',',$tags):'';
		saveCookie();
	}

	switch($action)
	{
		case 'add':
			if(empty($article['title'])) redirect('标题不得为空',$admin_url.'?file=article&action=add');
			if(empty($content['content'])) redirect('内容不得为空',$admin_url.'?file=article&action=add');

			// 插入数据部分
			$addsql="INSERT INTO ".DB_PREFIX."article set ".getJoinSql($article);
			$DB->query($addsql);
			$articleid = $DB->insert_id();
			if(!$article['url']) $DB->query('update '.DB_PREFIX."article set url='$articleid' where aid=$articleid");

			if($attachments&&is_array($attachments))
			{
				$fileidarr=array();
				foreach($attachments as $key=>$attachment)
				{
					$DB->unbuffered_query("Insert into ".DB_PREFIX."attachment (`articleid`,`dateline`,`filename`,`filetype`,`filesize`,`filepath`,`isimage`) values ('$articleid','$dateline','$attachment[filename]','$attachment[filetype]','$attachment[filesize]','$attachment[filepath]','$attachment[isimage]')");
					$attachments[$key]['aid']=$DB->insert_id();
					$fileidarr[$attachments[$key]['localid']]=$attachments[$key]['aid'];
				}
				foreach($fileidarr as $localid=>$fileid)
				{
					if($content!='') $content=str_replace('[localfile='.$localid.']','[attach='.$fileid.']',$content);
				}
			}
			$content['articleid']=$articleid;
			$cindex=ceil($articleid/500000);
			$tablename=DB_PREFIX."content{$cindex}";
			if($cindex>1&&$articleid%500000<5)
			{			
				$tables=GetTables();
				if(!in_array($tablename,$tables))
				{
					$DB->query("CREATE table {$tablename} select * from `rqcms_1_content1` where 1>2;ALTER TABLE `{$tablename}` ADD UNIQUE (`articleid`)");
				}
			}
			
			$DB->query("Insert into ".DB_PREFIX."content{$cindex} set ".getJoinSql($content));
			//添加tags
			modtag('',$article['tag'],$articleid);
			clearCookie();
			redirect('添加文章成功', $admin_url.'?file=article&action=add');
			break;
		case 'mod'://修改文章
			$aid=intval($_POST['aid']);
			$old=$DB->fetch_first('Select * from '.DB_PREFIX."article where aid=$aid");
			if(!$old) redirect('不存在的记录',$admin_url.'?file=article&action=list');
			if(empty($article['title'])) redirect('标题不得为空',$admin_url.'?file=article&action=mod&aid='.$aid);
			if(empty($content['content'])) redirect('内容不得为空',$admin_url.'?file=article&action=mod&aid='.$aid);
			$oldtag=$old['tag'];
			
			//附件先处理
			$attachments=getAttach();
			$oldattach=array();
			$aquery=$DB->query('select * from '.DB_PREFIX."attachment where articleid=$aid");
			while($adb=$DB->fetch_array($aquery))
			{
				$oldattach[]=$adb;
			}
			$oldattachids=array();
			foreach($oldattach as $k=>$v)
			{
				$oldattachids[]=$v['aid'];
			}
			$keepattach=isset($_POST['keep'])?$_POST['keep']:array();

			if(!empty($keepattach)&&is_array($keepattach)&&count($keepattach)<count($oldattachids))
			{
				$diff=array_diff($oldattachids,$keepattach);
				foreach($diff as $key=>$attid)
				{
					foreach($attachments as $k=>$v)
					{
						foreach($oldattach as $o=>$d)
						{
							if($d['aid']==$attid)//删除的是这条记录
							{
								if($d['filename']==$v['filename'])//这里是就是更新了.
								{
									$DB->query("update ".DB_PREFIX."attachment set `filesize`='$attachment[filesize]',`filepath`='$attachment[filepath]' where articleid=$aid and aid=$attid");
									$oldattach[o]=$attachments[$v];
									$oldattach[o]['aid']=$attid;
									unset($attachments[$v]);
									unset($diff[$key]);
									break;
								}
							}
						}
					}
				}
				$diffids=implode(',',$diff);
				if($diffids) 
				{
					$dquery=$DB->query('select * from '.DB_PREFIX."attachment where aid in ($diffids) and articleid=$aid");
					while($dfetch=$DB->fetch_array($dquery))
					{
						$filepath=RQ_DATA.'/files/'.$dfetch['filepath'];
						if(file_exists($filepath)) @unlink($filepath);
						$thumbpath=RQ_DATA.'/files/'.$dfetch['thumb_filepath'];
						if(file_exists($filepath)) @unlink($thumbpath);
					}
					$DB->query('Delete from '.DB_PREFIX."attachment where aid in ($diffids) and articleid=$aid");
				}
			}
			if($attachments)
			{
				$fileidarr=array();
				foreach($attachments as $attachment)
				{
					$DB->unbuffered_query("Insert into ".DB_PREFIX."attachment (`articleid`,`dateline`,`filename`,`filetype`,`filesize`,`filepath`,`isimage`,`modified`) values ('$aid','$dateline','$attachment[filename]','$attachment[filetype]','$attachment[filesize]','$attachment[filepath]','$attachment[isimage]','$timestamp')");
					$attachment['aid']=$DB->insert_id();
					$fileidarr[$attachment['localid']]=$attachment['aid'];
					unset($attachment['filepath']);
					unset($attachment['thumb_filepath']);
				}
				foreach($fileidarr as $localid=>$fileid)
				{
					if($content['content']!='') $content['content']=str_replace('[localfile='.$localid.']','[attach='.$fileid.']',$content['content']);
				}
			}
			$attach=$DB->fetch_first('select count(*) from '.DB_PREFIX."attachment where articleid=$aid");
			$attcount=$attach['count(*)'];
			// 插入数据部分
			$DB->query("Update ".DB_PREFIX."article set ".getJoinSql($article)." where aid=$aid");
			$cindx=ceil($aid/500000);
			$DB->query("Update ".DB_PREFIX."content{$cindx} set ".getJoinSql($content)." where `articleid`='$aid'");
			//添加tags
			modtag($oldtag,$article['tag'],$aid);
			clearCookie();
			stick_recache();
			latest_recache();
			redirect('修改文章成功', $admin_url.'?file=article&action=list');
		break;
		case 'domore':
			if(isset($_POST['aids'])&&is_array($_POST['aids']))
			{
				$aids=implode_ids($_POST['aids']);
				$aquery=$DB->query('Select aid from '.DB_PREFIX."article where aid in ($aids)");
				$aidarr=array();
				while($ainfo=$DB->fetch_array($aquery))
				{
					$aidarr[]=$ainfo['aid'];
				}
				$aids=implode_ids($aidarr);
				if(in_array($do,array('delete','move')))
				{
					$query=$DB->query('Select * from '.DB_PREFIX."article where aid in ($aids)");
					while($article=$DB->fetch_array($query))
					{
						$articledb[]=$article;
					}
				}
				else
				{
					switch($do)
					{
						case 'dodelete':
							$articlequery=$DB->query('select aid,tag from '.DB_PREFIX."article where aid in ($aids) ");
							while($articledb=$DB->fetch_array($articlequery))
							{
								modtag($articledb['tag'],'',$articledb['aid']);
							}
							
							$DB->query('delete from '.DB_PREFIX."article where aid in ($aids) ");
							$tableaids=array();
							foreach($aidarr as $delaid)
							{
								$cindex=ceil($delaid/500000);
								$tableaids[$cindex][]=$delaid;
							}

							foreach($tableaids as $table=>$delarr)
							{
								$delids=implode_ids($delarr);
								$DB->query('delete from '.DB_PREFIX."content{$table} where articleid in ($delids)");
							}

							$DB->query('delete from '.DB_PREFIX."attachment where articleid in ($aids) ");
							stick_recache();
							
							redirect('您选择的文章已成功删除', $admin_url.'?file=article&action=list');
							break;
						case 'domove':
							$cid=$_POST['cid'];
							$cateinfo=$DB->fetch_first('select * from '.DB_PREFIX."category where cid='$cid' ");
							if($cateinfo)
							{
								$DB->query('update '.DB_PREFIX."article set cateid='$cid' where aid in ($aids) ");
								stick_recache();
								
								latest_recache();
								redirect('您选择的文章成功移动', $admin_url.'?file=article&action=list&cid='.$cid.($view?'&view='.$view:''));
							}
							else redirect('您选择的栏目不存在', $admin_url.'?file=article&action=list');
						break;
					}
				}
			}
			else redirect('请选择要操作的文章', $admin_url.'?file=article&action=list');
			break;
		case 'list':
			if ($do == 'search') 
			{
				$searchsql='Select a.*,c.cid,c.name as cname from '.DB_PREFIX.'article a,'.DB_PREFIX."category c where c.cid=a.cateid";
				$searchfield=$_POST['searchfield'];
				$keywords = !empty($_POST['keywords'])?trim($_POST['keywords']):'';
				if ($keywords) 
				{
					$keywords = str_replace("_","\_",$keywords);
					$keywords = str_replace("%","\%",$keywords);
					if(preg_match("(AND|\+|&|\s)", $keywords) && !preg_match("(OR|\|)", $keywords)) {
						$andor = ' AND ';
						$sqltxtsrch = '1';
						$keywords = preg_replace("/( AND |&| )/is", "|", $keywords);
					} else {
						$andor = ' OR ';
						$sqltxtsrch = '0';
						$keywords = preg_replace("/( OR |\|)/is", "|", $keywords);
					}
					$keywords = str_replace('*', '%', addcslashes($keywords, '%_'));
					foreach(explode("|", $keywords) AS $text) {
						$text = trim($text);
						if($text) {
							$sqltxtsrch .= $andor;
							if($searchfield)
							{
								$sqltxtsrch .= "(a.".$searchfield." LIKE '%".$text."%')";
							}
							else
							{
								$sqltxtsrch .= "(a.excerpt LIKE '%".$text."%' OR a.title LIKE '%".$text."%' OR a.tag LIKE '%".$text."%' OR a.keywords LIKE '%".$text."%' OR a.url LIKE '%".$text."%' OR a.keywords LIKE '%".$text."%')";
							}
							doAction('admin_article_search_changesql');
						}
					}
					$searchsql .= " AND ($sqltxtsrch)";
				}
				if(!empty($_POST['cateid']))
				{
					$searchsql .= " AND a.cateid='".intval($_POST['cateid'])."'";
				}
				$searchsql .= !empty($_POST['startdate']) ? " AND dateline < '".strtotime($_POST['startdate'])."'" : '';
				$searchsql .= !empty($_POST['enddate'] )? " AND dateline > '".strtotime($_POST['enddate'])."'" : '';
				$searchsql.=!empty($_POST['views'])? " AND views ".$_POST['views'] : '';
				$squery=$DB->query($searchsql);
				$multipage='';
				$articledb = array();
				while ($article = $DB->fetch_array($squery)) {
					if ($article['attachments']) {
						$article['attachments'] = count(unserialize($article['attachments']));
						$article['attachment'] = '<a href="{$admin_url}?file=attachment&action=list&amp;aid='.$article['aid'].'">操作</a>('.$article['attachments'].')';
					} else {
						$article['attachment'] = '<a href="{$admin_url}?file=attachment&action=list&amp;aid='.$article['aid'].'"><span class="yes">上传</span></a>';
					}
					$article['dateline'] = date('Y-m-d H:i',$article['dateline']);
					$articledb[] = $article;
				}
				$total=count($articledb);
			}
			else redirect('请指定搜索条件', $admin_url.'?file=article&action=list');
			break;
		default:
		redirect('未定义操作', $admin_url.'?file=article&action=list');
	}
}
else
{
	if($action=='add'||$action=='mod')
	{
		$attachdb=array();//上传的附件数据
		$aid=isset($_GET['aid'])?intval($_GET['aid']):0;
		$article=$DB->fetch_first('Select * from '.DB_PREFIX."article where aid=$aid");
	
		$time=empty($article['dateline'])?time():$article['dateline'];
		$time=date("Y-m-d-H-i-s",$time);
		list($newyear,$newmonth,$newday,$newhour,$newmin,$newsec)=explode('-',$time);
		//类别
		if(!$article)
		{
			$stick_check='';
			$visible_check='checked';
			$tdtitle='添加内容';
		}
		else
		{
			$cindex=ceil($article['aid']/500000);
			$content=$DB->fetch_first('Select * from `'.DB_PREFIX."content{$cindex}` where articleid={$aid}");
			$article=array_merge($article, $content);

			$tdtitle='编辑内容';
			$stick_check=$article['stick']?'checked':'';
			$aquery=$DB->query("Select * from ".DB_PREFIX."attachment where articleid=$aid");
			while($ath=$DB->fetch_array($aquery))
			{
				$ath['dateline']=date('Y-m-d',$ath['dateline']);
				$ath['filesize']=sizecount($ath['filesize']);
				$attachdb[]=$ath;
			}
		}

	}
	else if($action=='list')
	{
		$searchsql='';
		$addquery='';
		$pagelink='';
		$view=isset($_GET['view'])?$_GET['view']:'';
		$tag=isset($_GET['tag'])?$_GET['tag']:'';
		$cid=isset($_GET['cid'])?intval($_GET['cid']):'';
		if ($view == 'stick') {
			$addquery = " AND a.stick='1'";
			$pagelink = '&view=stick';
		} elseif ($cid) {
			$cate = $DB->fetch_first("SELECT name FROM ".DB_PREFIX."category WHERE cid='$cid'");
			$addquery = " AND a.cateid='$cid'";
			$pagelink = '&cid='.$cid;
		} 
		else $addquery = "";	

		if($page) {
		$start_limit = ($page - 1) * 30;
		} else {
			$start_limit = 0;
			$page = 1;
		}
		$articledb = array();
		if(empty($tag))
		{
			$rs = $DB->fetch_first("SELECT count(*) AS articles FROM ".DB_PREFIX."article a WHERE 1 $searchsql $addquery");
			$total = $rs['articles'];
			$multipage = multi($total, 30, $page, $admin_url.'?file=article&action=list'.$pagelink);
			$query = $DB->query("SELECT a.*,c.name as cname FROM ".DB_PREFIX."article a 
			LEFT JOIN ".DB_PREFIX."category c ON c.cid=a.cateid
			WHERE 1 $searchsql $addquery ORDER BY a.aid DESC LIMIT $start_limit, 30");
		}
		else
		{
			$item = addslashes($tag);
			$tagaids=gettagids($item);
			if($tagaids)
			{
				$query=$DB->query('Select a.*,c.name as cname from '.DB_PREFIX."article a LEFT JOIN ".DB_PREFIX."category c ON c.cid=a.cateid where a.aid in ($tagaids)");
				$tagarr=explode(',',$tagaids);
				$total=count($tagarr);
			}
			else
			{
				redirect('标签不存在', $admin_url.'?file=article&action=list');
			}
			
			$pagelink = '&tag='.urlencode($item);
			$multipage = multi($total, 30, $page, $admin_url.'?file=article&action=list'.$pagelink);
			$subnav = 'Tags:'.$item;
		}
		while ($article = $DB->fetch_array($query)) {
				if ($article['attachments']) {
					$article['attachments'] = $article['attachments'];
					$article['attachment'] = '<a href="{$admin_url}?file=attachment&action=list&amp;aid='.$article['aid'].'">操作</a>('.$article['attachments'].')';
				} else {
					$article['attachment'] = '<a href="{$admin_url}?file=attachment&action=list&amp;aid='.$article['aid'].'"><span class="yes">上传</span></a>';
				}
				$article['dateline'] = date('Y-m-d H:i',$article['dateline']);
				$articledb[] = $article;
		}
		unset($article);
		$DB->free_result($query);
	}
}
?>