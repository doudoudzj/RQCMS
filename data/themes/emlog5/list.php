<?php
if(!defined('RQ_ROOT')) exit('Access Denied');

?>
<div id="contentleft">
	<h2><img src="http://live.emlog.net/content/templates/default/images/import.gif" title="置顶日志" /> <a href="http://live.emlog.net/post-2000.html">{标题}</a></h2>
	<p class="date">作者：{作者} 发布于：{时间}
		 
	<a href="http://live.emlog.net/admin/write_log.php?action=edit&gid=2000">编辑</a>	</p>
	<p><b>功能介绍</b></p>
{内容}
	<p class="tag">标签:	{标签}</p>
	<p class="count">
	<a href="http://live.emlog.net/post-2000.html#comments">评论({评论次数})</a>
	<a href="http://live.emlog.net/post-2000.html">浏览({浏览次数})</a>
	</p>
	<div style="clear:both;"></div>
	
<div id="pagenavi">
	</div>

</div><!-- end #contentleft-->