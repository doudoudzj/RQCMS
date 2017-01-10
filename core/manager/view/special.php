<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
print <<<EOT
<div class="mainbody">
  <table border="0" cellspacing="0" cellpadding="0" style="width:100%;">
    <tr>
      <td valign="top" style="width:150px;"><div class="tableborder">
          <div class="tableheader">站点管理</div>
          <div class="leftmenubody">
            <div class="leftmenuitem">&#8226; <a href="admin.php?file=special&action=list">站点列表</a></div>
			 <div class="leftmenuitem">&#8226; <a href="admin.php?file=special&action=add">添加站点</a></div>
            <div class="leftmenuitem">&#8226; <a href="admin.php?file=special&action=user">用户管理</a></div>
			<div class="leftmenuitem">&#8226; <a href="admin.php?file=database&action=backup">备份数据</a></div>
			<div class="leftmenuitem">&#8226; <a href="admin.php?file=special&action=cacheall">更新缓存</a></div>
          </div>
        </div></td>
      <td valign="top" style="width:20px;"></td>
      <td valign="top">
        <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0"><form action='admin.php?file=special' method='post'><input type="hidden" name="action" value="{$action}">
EOT;
if($action=='add'||$action=='edit') {print <<<EOT
          <tr>
            <td valign="top" class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                 <tr class="tdbheader">
				 <input type="hidden" name="setting[hid]" value="{$setting['hid']}">
                    <td colspan="2">基本设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="200"><b>网站名称:</b></td>
                    <td><input class="formfield" type="text" name="setting[name]" size="35" maxlength="50" value="{$setting['name']}"></td>
                  </tr>
				  <tr class="tablecell">
					 <td width="200"><b>网站地址:</b></td>
                    <td><input class="formfield" type="text" name="setting[host]" size="35" maxlength="50" value="{$setting['host']}"></td>
                  </tr>
EOT;
print <<<EOT
                  <td class="tablebottom" colspan="4"></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td height="20"></td>
          </tr>
          <tr>
            <td valign="top" class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                <tr class="tdbheader">
                  <td width="200">原文件名</td>
				  <td>新文件名</td>
				  <td>参数映射(新值=旧值,如id=aid)</td>
                </tr>
                <tr class="tablecell">
                  <td width="200">首页(index.php):</td>
                  <td><input class="formfield" type="text" name="maps[index.php]" size="35" maxlength="50" value="{$info['index.php']}"></td>
				  <td><input class="formfield" type="text" name="args[index.php]" size="35" maxlength="200" value="{$args['index.php']}"></td>
                </tr>
                <tr class="tablecell">
                  <td width="200">列表页(category.php):</td>
                  <td><input class="formfield" type="text" name="maps[category.php]" size="35" maxlength="50" value="{$info['category.php']}"></td>
				    <td><input class="formfield" type="text" name="args[category.php]" size="35" maxlength="200" value="{$args['category.php']}"></td>
                </tr>
				                <tr class="tablecell">
                  <td width="200">内容页(article.php):</td>
                  <td><input class="formfield" type="text" name="maps[article.php]" size="35" maxlength="50" value="{$info['article.php']}"></td>
				    <td><input class="formfield" type="text" name="args[article.php]" size="35" maxlength="200" value="{$args['article.php']}"></td>
                </tr>
				                <tr class="tablecell">
                  <td width="200">附件页(attachment.php):</td>
                  <td><input class="formfield" type="text" name="maps[attachment.php]" size="35" maxlength="50" value="{$info['attachment.php']}"></td>
				    <td><input class="formfield" type="text" name="args[attachment.php]" size="35" maxlength="200" value="{$args['attachment.php']}"></td>
                </tr>
				          </tr>
				                <tr class="tablecell">
                  <td width="200">搜索页(search.php):</td>
                  <td><input class="formfield" type="text" name="maps[search.php]" size="35" maxlength="50" value="{$info['search.php']}"></td>
				    <td><input class="formfield" type="text" name="args[search.php]" size="35" maxlength="200" value="{$args['search.php']}"></td>
                </tr>
				          </tr>
				                <tr class="tablecell">
                  <td width="200">评论页(comment.php):</td>
                  <td><input class="formfield" type="text" name="maps[comment.php]" size="35" maxlength="50" value="{$info['comment.php']}"></td>
				    <td><input class="formfield" type="text" name="args[comment.php]" size="35" maxlength="200" value="{$args['comment.php']}"></td>
                </tr>
				  <tr class="tablecell">
                  <td width="200">Tag页(tag.php):</td>
                  <td><input class="formfield" type="text" name="maps[tag.php]" size="35" maxlength="50" value="{$info['tag.php']}"></td>
				    <td><input class="formfield" type="text" name="args[tag.php]" size="35" maxlength="200" value="{$args['tag.php']}"></td>
                </tr>
						  <tr class="tablecell">
                  <td width="200">用户页(profile.php):</td>
                  <td><input class="formfield" type="text" name="maps[profile.php]" size="35" maxlength="50" value="{$info['profile.php']}"></td>
				    <td><input class="formfield" type="text" name="args[profile.php]" size="35" maxlength="200" value="{$args['profile.php']}"></td>
                </tr>
						  <tr class="tablecell">
                  <td width="200">管理页(admin.php):</td>
                  <td><input class="formfield" type="text" name="maps[admin.php]" size="35" maxlength="50" value="{$info['admin.php']}"></td>
				    <td><input class="formfield" type="text" name="args[admin.php]" size="35" maxlength="200" value="{$args['admin.php']}"></td>
                </tr>		
				<tr class="tablecell">
                  <td width="200">安装页(install.php):</td>
                  <td><input class="formfield" type="text" name="maps[install.php]" size="35" maxlength="50" value="{$info['install.php']}"></td>
				    <td><input class="formfield" type="text" name="args[install.php]" size="35" maxlength="200" value="{$args['install.php']}"></td>
                </tr>
				<tr class="tablecell">
                  <td width="200">RSS页(rss.php):</td>
                  <td><input class="formfield" type="text" name="maps[rss.php]" size="35" maxlength="50" value="{$info['rss.php']}"></td>
				    <td><input class="formfield" type="text" name="args[rss.php]" size="35" maxlength="200" value="{$args['rss.php']}"></td>
                </tr>
					<tr class="tablecell">
                  <td width="200">验证码页(captcha.php):</td>
                  <td><input class="formfield" type="text" name="maps[captcha.php]" size="35" maxlength="50" value="{$info['captcha.php']}"></td>
				    <td><input class="formfield" type="text" name="args[captcha.php]" size="35" maxlength="200" value="{$args['captcha.php']}"></td>
                </tr>
				<tr class="tablecell">
                  <td width="200">Js页(captcha.php):</td>
                  <td><input class="formfield" type="text" name="maps[js.php]" size="35" maxlength="50" value="{$info['js.php']}"></td>
				    <td><input class="formfield" type="text" name="args[js.php]" size="35" maxlength="200" value="{$args['js.php']}"></td>
                </tr>
				 <tr class="tablecell">
                    <td colspan="3" align="center"><input type="submit" value="提交" class="formbutton">
                      <input type="reset" value="重置" class="formbutton">
                    </td>
                  </tr>
              </table></td>
          </tr>
          <tr>
            <td height="20"></td>
        </table></td>
    </tr>
EOT;
}else if($action=='list') {
print <<<EOT
    <tr class="tdbheader">
      <td width="10%">ID</td>
      <td width="30%">网站名称</td>
      <td>网站域名</td>
	  <td width="10%">状态</td>
	  <td width="10%">编辑</td>
	  <td width="10%">切换</td>
      <td width="2%" nowrap><input name="chkall" type="checkbox" onclick="checkall(this.form)" value="on"></td>
    </tr>
EOT;
foreach($sitedb as $site) {print <<<EOT
		<tr class="tablecell">
		  <td>{$site['hid']}</td>
		  <td>{$site['name']}</td>
		  <td><a href="http://{$site['host']}" target='_blank'>{$site['host']}</a></td>
		  <td>{$site['status']}</td>
		  <td><a href="admin.php?file=special&action=edit&hid={$site['hid']}">编辑</a></td>
		  <td><a href="admin.php?file=special&action=go&hid={$site['hid']}">转到</a></td>
		  <td nowrap><input type="checkbox" name="hids" value="{$site['hid']}"></td>
		</tr>
EOT;
}
print <<<EOT
  </table>
</div>
EOT;
}
print '</form>';

?>
