<?php
print <<<EOT
<div class="mainbody">
  <table border="0"  cellspacing="0" cellpadding="0" style="width:100%;">
    <tr>
      <td valign="top" style="width:150px;"><div class="tableborder">
          <div class="tableheader">系统设置</div>
          <div class="leftmenubody">          
EOT;
foreach ($settingsmenu as $key => $value) {print <<<EOT
            <div class="leftmenuitem">&#8226; <a href="{$admin_url}?file=configurate&amp;type=$key">$value</a></div>       
EOT;
}print <<<EOT
          </div>
        </div></td>
      <td valign="top" style="width:20px;"></td>
      <td valign="top"><form action="{$admin_url}?file=configurate" method="post">
          <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="rightmainbody"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">               
EOT;
if(!$type || $type=='basic'){print <<<EOT
                  <tr class="tdbheader">
                    <td colspan="2">基本设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>网站名称:</b></td>
                    <td><input class="formfield" type="text" name="setting[name]" size="35" maxlength="50" value="{$settings['name']}"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>页面Gzip压缩:</b><br />
                      将页面内容以 gzip 压缩后传输,可以加快传输速度，需 PHP 4.0.4 以上且支持 Zlib 模块才能使用</td>
                    <td><select name="setting[gzipcompress]">
                        <option value="1" $gzipcompress_Y>是</option>
                        <option value="0" $gzipcompress_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>网站关键字:</b></td>
                    <td><input class="formfield" type="text" name="setting[keywords]" size="35" maxlength="255" value="{$settings['keywords']}"></td>
                  </tr>
				  <tr class="tablecell">
                    <td width="60%"><b>网站描述:</b></td>
                    <td><input class="formfield" type="text" name="setting[description]" size="35" maxlength="255" value="{$settings['description']}"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>信息产业部网站备案号:</b></td>
                    <td><input class="formfield" type="text" name="setting[icp]" size="35" maxlength="50" value="{$settings['icp']}"></td>
                  </tr>
EOT;
}
if(!$type || $type=='display'){print <<<EOT
                  <tr class="tdbheader">
                    <td colspan="2">显示设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>列表每页显示文章的数量:</b><br />
                      指文章列表页每页有多少文章</td>
                    <td><input class="formfield" type="text" name="setting[list_shownum]" size="15" maxlength="50" value="{$settings['list_shownum']}"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>标签列表每页的数量:</b><br />默认标签页显示多少个标签</td>
                    <td><input class="formfield" type="text" name="setting[tags_shownum]" size="15" maxlength="50" value="$settings[tags_shownum]"></td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>列表或内容时间显示格式:</b><br />统一的对dateline和modified进行时间转化</td>
                    <td><input class="formfield" type="text" name="setting[time_format]" size="15" maxlength="50" value="$settings[time_format]"></td>
                  </tr>
				  <!--
				  -->
EOT;
}
if(!$type || $type=='search'){print <<<EOT
                  <tr class="tdbheader">
                    <td colspan="2">搜索设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>是否允许搜索内容:</b><br />
                      少量数据时可以搜索内容,一般只搜索标题即可，看情况设置</td>
                    <td><select name="setting[allow_search_content]">
                        <option value="1" $allow_search_content_Y>是</option>
                        <option value="0" $allow_search_content_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>搜索间隔:</b><br />
                      使用搜索功能的时间间隔，设为“0”则不限制</td>
                    <td><input class="formfield" type="text" name="setting[search_post_space]" size="15" maxlength="50" value="$settings[search_post_space]"></td>
                  </tr>
				  <tr class="tablecell">
                    <td width="60%"><b>搜索关键字的最少字节数:</b><br />
                      至少输入多少个字节才可以进行搜索，设为“0”则不限制</td>
                    <td><input class="formfield" type="text" name="setting[search_keywords_min_len]" size="15" maxlength="50" value="$settings[search_keywords_min_len]"></td>
                  </tr> 
                  <tr class="tablecell">
                    <td width="60%"><b>指定搜索字段:</b><br />
                      默认是tag,keywords,title,excerpt</td>
                    <td><input class="formfield" type="text" name="setting[search_field_allow]" size="35" maxlength="100" value="$settings[search_field_allow]"></td>
                  </tr> 
				<tr class="tablecell">
                    <td width="60%"><b>搜索结果最多显示数量:</b><br />
                      默认是0，即全部显示</td>
                    <td><input class="formfield" type="text" name="setting[search_max_num]" size="15" maxlength="50" value="$settings[search_max_num]"></td>
                  </tr> 				  
EOT;
}
if(!$type || $type=='attach'){print <<<EOT
                  <tr class="tdbheader">
                    <td colspan="2">附件设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>附件存放方式:</b><br />
                      为了方便管理附件请选择一个适合您服务器情况的方式</td>
                    <td><select name="setting[attach_save_dir]">
                        <option value="0" $attach_save_dir[0]>全部存放同一目录</option>
                        <option value="1" $attach_save_dir[1]>按分类存放</option>
                        <option value="2" $attach_save_dir[2]>按月份存放</option>
                        <option value="3" $attach_save_dir[3]>按文件类型存放</option>
                      </select>
                    </td>
                  </tr>
				   <tr class="tablecell">
                    <td width="60%"><b>附件的下载处理方式:</b><br />
                      直接下载文件的要自己设置附件页attachment.php模板</td>
                    <td><select name="setting[attach_display]">
                        <option value="0" $attach_display[0]>直接下载该文件</option>
                        <option value="1" $attach_display[1]>显示下载页面后再下载</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>附件是否禁止从其他站查看:</b><br />
                      如果选了直接下载文件的话，该选项起效。选是的话，用户将不能从别的网站上直接下载文件</td>
                    <td><select name="setting[attachments_remote_open]">
                        <option value="1" $attachments_remote_open_Y>是</option>
                        <option value="0" $attachments_remote_open_N>否</option>
                      </select>
                    </td>
                  </tr>                
EOT;
}
if(!$type || $type=='rss'){print <<<EOT
                  <tr class="tdbheader">
                    <td colspan="2">RSS订阅设置</td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>开启RSS订阅功能:</b><br />
                      开启后将允许用户使用 RSS 客户端软件接收最新的文章.</td>
                    <td><select name="setting[rss_enable]">
                        <option value="1" $rss_enable_Y>是</option>
                        <option value="0" $rss_enable_N>否</option>
                      </select>
                    </td>
                  </tr>
                  <tr class="tablecell">
                    <td width="60%"><b>RSS 订阅文章数量:</b></td>
                    <td><input class="formfield" type="text" name="setting[rss_num]" size="15" maxlength="50" value="$settings[rss_num]"></td>
                  </tr>
				  
EOT;
}print <<<EOT
                  <input type="hidden" name="action" value="updatesetting" />
                  <input type="hidden" name="type" value="$type" />
                  <tr class="tablecell">
                    <td colspan="2" align="center"><input type="submit" value="提交" class="formbutton">
                      <input type="reset" value="重置" class="formbutton">
                    </td>
                  </tr>
                  <tr>
                    <td class="tablebottom" colspan="2"></td>
                  </tr>
                </table></td>
            </tr>
          </table>
        </form></td>
    </tr>
  </table>
</div>
EOT;
?>

