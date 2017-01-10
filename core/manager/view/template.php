<?php
print <<<EOT
<script type="text/javascript">
function really(d,m,n) {
	if (confirm(m)) {
		window.location.href=$admin_url.'?file=template&action=delonetag&tag='+d+'&tagid='+n;
	}
}
</script>
<div class="mainbody">
  <table border="0"  cellspacing="0" cellpadding="0" style="width:100%;">
    <tr>
      <td valign="top" style="width:150px;"><div class="tableborder">
          <div class="tableheader">模板管理</div>
          <div class="leftmenubody">
            <div class="leftmenuitem">&#8226; <a href="{$admin_url}?file=template&action=template">模板管理</a></div>
          </div>
        </div>
        <div class="tableborder">
          <div class="tableheader">模板变量</div>
          <div class="leftmenubody">
            <div class="leftmenuitem">&#8226; <a href="{$admin_url}?file=template&action=stylevar">自定义变量</a></div>
          </div>
        </div>
        </div></td>
      <td valign="top" style="width:20px;"></td>
      <td valign="top">
EOT;
if ($action == 'stylevar') {print <<<EOT
        <div class="box">
          <div class="alert">关于自定义模板变量</div>
          <div class="alertmsg">设置一个变量about,内容为 &lt;b&gt;关于我&lt;/b&gt;在前后台模板的任意地方,均可以放一个 <b>\$varArr[about]</b> 变量,模板则直接显示 <b>关于我</b></div>
        </div>
EOT;
} 
print <<<EOT
        <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">   
EOT;
if($action == 'template'){print <<<EOT
                <tr class="tdbheader">
                  <td>当前模板</td>
                </tr>
                <tr>
                  <td class="alertbox">
EOT;
print <<<EOT
        <table border="0" cellpadding="10">
					 <tr>
					 <td>模板类型</td><td>模板名称</td><td>制作者</td><td>适用版本</td><td>模板描述</td><td>模板目录</td><td>操作</td>
					 </tr>					
					 <tr>
					 <td>电脑</td><td>$current_template_info[name]</td><td>$current_template_info[author]</td><td>$current_template_info[version]</td><td>$current_template_info[description]</td><td>$current_template_info[templatedir]</td><td></td>
					 </tr>
					  <tr>
						<td>手机</td><td>$mobile_template_info[name]</td><td>$mobile_template_info[author]</td><td>$mobile_template_info[version]</td><td>$mobile_template_info[description]</td><td>$mobile_template_info[templatedir]</td><td></td>
					 </tr>
					 
					  <tr>
					 <td>微信</td><td>$weixin_template_info[name]</td><td>$weixin_template_info[author]</td><td>$weixin_template_info[version]</td><td>$weixin_template_info[description]</td><td>$weixin_template_info[templatedir]</td><td></td>
					 </tr>
                    </table> 
                  </td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td height="20"></td>
          </tr>
          <tr>
            <td class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                <tr class="tdbheader">
                  <td>可用模板</td>
                </tr>
                <tr>
                  <td class="alertbox">
EOT;
if ($available_template_db) {
foreach($available_template_db as $id => $template){print <<<EOT

                    <div class="availabletheme">
                      <h3>$template[name]</h3>
                      <a href="{$admin_url}?file=template&action=settemplate&name=$template[dirurl]" class="screenshot"><img src="$template[screenshot]" border="0" /></a> 
					  <a href="{$admin_url}?file=template&action=settemplate&name=$template[dirurl]&type=pc">设置为PC模板</a> <a href="{$admin_url}?file=template&action=settemplate&name=$template[dirurl]&type=mobile">设置为手机模板</a> <a href="{$admin_url}?file=template&action=settemplate&name=$template[dirurl]&type=weixin">设置为微信模板</a> 
					  </div>
                    
EOT;
}} else {print <<<EOT
                    <b>没有可用模板</b>
EOT;
}print <<<EOT
                  </td>
                </tr>
                <tr>
                  <td class="tablebottom"></td>
                </tr>               
EOT;
} elseif($action == 'stylevar'){print <<<EOT

                <form action="{$admin_url}?file=template" method="post" name="form">
				<input type="hidden" name="action" value="domorestylevar">
                  <tr class="tdbheader">
                    <td width="4%" nowrap="nowrap">状态</td>
                    <td width="31%" nowrap="nowrap">变量名</td>
                    <td width="61%" nowrap="nowrap">变量内容</td>
                    <td width="4%" nowrap="nowrap"><input name="chkall" value="on" type="checkbox" onclick="checkall(this.form)"></td>
                  </tr>                 
EOT;
foreach($stylevardb as $stylevar){print <<<EOT
                  <tr class="tablecell">
				    <td nowrap="nowrap"><select name="visible[$stylevar[vid]]">$stylevar[visible]</select></td>
                    <td nowrap="nowrap"><b>\$varArr[$stylevar[title]]</b></td>
                    <td nowrap="nowrap"><textarea id="varid_$stylevar[vid]" class="formarea" name="stylevar[$stylevar[vid]]" style="width:400px;height:30px;">$stylevar[value]</textarea> <b><a href=“javascript:void(0);” onclick="resizeup('varid_$stylevar[vid]');">[+]</a> <a href=“javascript:void(0);” onclick="resizedown('varid_$stylevar[vid]');">[-]</a></b></td>
                    <td nowrap><input type="checkbox" name="delete[]" value="$stylevar[vid]"></td>
                  </tr>                
EOT;
}print <<<EOT
                  <tr class="tablecell">
                    <td colspan="4" nowrap="nowrap"><div class="records">记录:$total</div>
                      <div class="multipage">$multipage</div></td>
                  </tr>
                  <tr class="tablecell">
                    <td colspan="4" align="center"><input type="submit" value="更新 / 删除(所选)" class="formbutton"></td>
                  </tr>
                  <tr>
                    <td class="tablebottom" colspan="4"></td>
                  </tr>
                </form>
              </table></td>
          </tr>
          <tr>
            <td height="20"></td>
          </tr>
          <tr>
            <td class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                <form action="{$admin_url}?file=template" method="post" name="form">
				<input type="hidden" name="action" value="addstylevar">
                  <tr class="tdbheader">
                    <td nowrap="nowrap" colspan="2">添加自定义变量</td>
                  </tr>
                  <tr class="tablecell">
                    <td><b>变量名:</b></td>
                    <td><input class="formfield" type="text" name="title" size="35" maxlength="50"> 只允许英文</td>
                  </tr>
                  <tr class="tablecell">
                    <td><b>变量内容:</b></td>
                    <td valign="top"><textarea id="addvar" class="formarea" type="text" name="value" style="width:400px;height:50px;"></textarea> <b><a href=“javascript:void(0);” onclick="resizeup('addvar');">[+]</a> <a href=“javascript:void(0);” onclick="resizedown('addvar');">[-]</a></b></td>
                  </tr>
                  <tr class="tablecell">
                    <td colspan="2" align="center"><input type="submit" value="添加" class="formbutton"></td>
                  </tr>
                  <tr>
                    <td class="tablebottom" colspan="2"></td>
                  </tr>
                </form>              
EOT;
} elseif($action == 'filelist'){print <<<EOT
                <tr class="tdbheader">
                  <td nowrap="nowrap">模板名</td>
                  <td nowrap="nowrap">操作</td>
                </tr>             
EOT;
foreach($filedb as $key => $file){print <<<EOT
                <tr class="tablecell">
                  <td nowrap="nowrap"><b><a href="{$admin_url}?file=template&action=mod&path=$path&file=$file[filename]&ext=$file[extension]">$file[filename]</a></b></td>
                  <td nowrap="nowrap"><a href="{$admin_url}?file=template&action=del&path=$path&file=$file[filename]&ext=$file[extension]">删除</a></td>
                </tr>           
EOT;
}print <<<EOT
                <tr class="tablecell">
                  <td colspan="2"><b>共有 $i 个模板文件</b></td>
                </tr>             
EOT;
} elseif ($action == 'mod') {print <<<EOT
                <form action="{$admin_url}?file=template" method="post" name="form">
                  <input type="hidden" name="action" value="savefile">
                  <tr class="tdbheader">
                    <td colspan="2">编辑模板文件</td>
                  </tr>              
EOT;
if (!$writeable) {print <<<EOT
                  <tr class="tablecell">
                    <td><b>写入状态:</b></td>
                    <td><span class="no"><b>当前模板文件不可写入, 请设置为 0777 权限后再编辑此文件.</b></span></td>
                  </tr>      
EOT;
}print <<<EOT
                  <tr class="tablecell">
                    <td width="20%"><b>模板套系:</b></td>
                    <td width="80%">$path
                      <input type="hidden" name="path" value="$path"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="20%"><b>模板名称:</b></td>
                    <td width="80%">$file
                      <input type="hidden" name="file" value="$file"><input type="hidden" name="ext" value="$ext"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="20%" valign="top"><b>模板内容:</b><br /><b><a href=“javascript:void(0);” onclick="resizeup('filecontent');">[+]</a> <a href=“javascript:void(0);” onclick="resizedown('filecontent');">[-]</a></b></td>
                    <td width="80%"><textarea id="filecontent" class="formarea" cols="85" rows="25" name="content" style="width:95%;height:400px;font:12px'Courier New';">$contents</textarea></td>
                  </tr>
                  <tr nowrap class="tablecell">
                    <td colspan="2" align="center"><input type="submit" value="保存" class="formbutton">
                      <input type="reset" value="重置" class="formbutton">
                    </td>
                  </tr>
                </form>     
EOT;
} elseif ($action == 'newtemplate') {print <<<EOT
                <form action="{$admin_url}?file=template" method="post" name="form">
                  <input type="hidden" name="action" value="donewtemplate">
                  <tr class="tdbheader">
                    <td colspan="2">新建模板</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="20%"><b>模板名称:</b></td>
                    <td width="80%"><input class="formfield" type="text" name="newtemplatename" value=""> 只允许英文、数字和下划线</td>
                  </tr>
                  <tr nowrap class="tablecell">
                    <td colspan="2" align="center"><input type="submit" value="保存" class="formbutton">
                      <input type="reset" value="重置" class="formbutton">
                    </td>
                  </tr>
                </form>           
EOT;
} elseif ($action == 'del') {print <<<EOT
                <form action="{$admin_url}?file=template" method="post" name="form">
                  <tr class="alertheader">
                    <td colspan="1"><a name="删除模板"></a>删除模板</td>
                  </tr>
                  <tr>
                    <td class="alertbox"><p>模板套系: <a href="{$admin_url}?file=template&action=filelist&path=$path">$path</a></p>
                      <p>模板文件: <a href="{$admin_url}?file=template&action=mod&path=$path&file=$file">$file</a></p>
                      <p><b>注意: 删除模板文件将不会显示和该模板有关的一切页面，确定吗？</b></p>
                      <p>
                        <input type="submit" value="确认" class="formbutton">
                      </p>
                      <input type="hidden" name="path" value="$path">
                      <input type="hidden" name="file" value="$file">
					  <input type="hidden" name="ext" value="$ext">
                      <input type="hidden" name="action" value="delfile">
                    </td>
                  </tr>
                </form>
EOT;
}print <<<EOT
                <tr>
                  <td class="tablebottom" colspan="6"></td>
                </tr>
              </table></td>
          </tr>
        </table></td>
    </tr>
  </table>
</div>
EOT;
?>

