<?php
if(!$action) $action = 'template';
include RQ_CORE.'/include/template.php';
$refile=$admin_url.'?file=template&action=template';
//读取模板套系(目录)
$template_dir = RQ_DATA.'/themes/';

if(RQ_POST)
{
	switch($action)
	{
		case 'addstylevar':
			//添加自定义模板变量
			$title = strtolower(addslashes($_POST['title']));
			$value = addslashes($_POST['value']);
			if (!$title || !$value) {
				redirect('请填写完整');
			}
			$query = $DB->query("SELECT COUNT(*) FROM ".DB_PREFIX."var WHERE title='$title'  ");
			if($DB->result($query, 0)) {
				redirect('变量名已经存在,请返回修改');
			} elseif(!preg_match("/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/", $title)) {
				redirect('变量名称不合法,请返回修改');
			}
			$DB->query("INSERT INTO ".DB_PREFIX."var (title, value) VALUES ('$title','$value')");
			setting_recache();
			redirect('自定义变量添加成功',$admin_url.'?file=template&action=stylevar');
			break;
		case 'domorestylevar':
			//批量处理自定义模板变量
			if($ids = implode_ids($_POST['delete'])) {
				$DB->query("DELETE FROM	".DB_PREFIX."var WHERE vid IN ($ids) ");
			}
			if(is_array($_POST['stylevar'])) {
				foreach($_POST['stylevar'] as $stylevarid => $value) {
					$DB->unbuffered_query("UPDATE ".DB_PREFIX."var SET value='".addslashes(trim($_POST['stylevar'][$stylevarid]))."', visible='".intval($_POST['visible'][$stylevarid])."' WHERE vid='".intval($stylevarid)."' ");
				}
			}
			setting_recache();
			redirect('自定义模板变量已成功更新', $admin_url.'?file=template&action=stylevar');
			break;
		default:
			redirect('未定义操作', $refile);
	}
}
else
{
	switch($action)
	{
		//设置模板
		case 'settemplate':
			$name = $_GET['name'];
			if (file_exists($template_dir.$name) && strpos($name,'..')===false) 
			{
				$themetype='theme';
				$themearr=array('theme'=>'电脑','thememobile'=>'手机','themeweixin'=>'微信');
				if($_GET['type']=='mobile') $themetype='thememobile';
				if($_GET['type']=='weixin') $themetype='themeweixin';
				$themename=$themearr[$themetype];
				$DB->query("update rqcms_host set $themetype='$name' where hid=$hostid");
				host_recache();
				redirect("{$themename}模板已经更新", $refile);
			} 
			else 
			{
				redirect('模板不存在',$refile);
			}
			break;
		//自定义模板变量
		case 'stylevar':
			if($page) {
				$start_limit = ($page - 1) * 30;
			} else {
				$start_limit = 0;
				$page = 1;
			}
			$total = $DB->num_rows($DB->query("SELECT vid FROM ".DB_PREFIX."var"));
			$multipage = multi($total, 30, $page, $admin_url.'?file=template&action=stylevar');
			$query = $DB->query("SELECT * FROM ".DB_PREFIX."var ORDER BY vid DESC LIMIT $start_limit, 30");

			$stylevardb = array();
			while ($stylevar = $DB->fetch_array($query)) {
				if ($stylevar['visible']) {
					$stylevar['visible'] = '<option value="1" selected>启用</option><option value="0">禁用</option>';
				} else {
					$stylevar['visible'] = '<option value="1">启用</option><option value="0" selected>禁用</option>';
				}
				$stylevardb[] = $stylevar;
			}
			unset($stylevar);
			$DB->free_result($query);
			$subnav = '自定义模板变量管理';
			break;
		default:
			$current_infofile = $theme.'/info.txt';
			if (file_exists($template_dir.$current_infofile)) {
				$current_template_info = get_template_info($current_infofile);
			} else {
			$current_template_info['name']= $current_template_info['author']=$current_template_info['version']=$current_template_info['description']=$current_template_info['templatedir']= '';
			}
			$mobile_infofile = $host['thememobile'].'/info.txt';
			if (file_exists($template_dir.$mobile_infofile)) {
				$mobile_template_info = get_template_info($mobile_infofile);
			} else {
				$mobile_template_info['name']= $mobile_template_info['author']=$mobile_template_info['version']=$mobile_template_info['description']=$mobile_template_info['templatedir']= '';
			}
			
			$weixin_infofile = $host['themeweixin'].'/info.txt';
			if (file_exists($template_dir.$weixin_infofile)) {
				$weixin_template_info = get_template_info($weixin_infofile);
			} else {
				$weixin_template_info['name']= $weixin_template_info['author']=$weixin_template_info['version']=$weixin_template_info['description']=$weixin_template_info['templatedir']= '';
			}
			
			$dir1 = opendir($template_dir);
			$available_template_db = array();
			while($file1 = readdir($dir1)){
				if ($file1 != '' && $file1 != '.' && $file1 != '..'){
					if (is_dir($template_dir.'/'.$file1)){
						$dir2 = opendir($template_dir.'/'.$file1);
						while($file2 = readdir($dir2)){
							if (is_file($template_dir.'/'.$file1.'/'.$file2) && $file2 == 'info.txt'){
								$available_template_db[] = get_template_info($file1.'/'.$file2);
							}
						}
						closedir($dir2);
					}
				}
			}
			closedir($dir1);
			unset($file1);
			$subnav = '选择模板';
	}
}