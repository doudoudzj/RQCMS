﻿<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
$hotdata=getHotArticle(10,$article['cateid']);
if(is_array($article['tag'])) $likedata=getRelatedArticle($article['aid'],$article['tag'],10);

include RQ_DATA."/themes/{$theme}/header.php";
print <<<EOT
  <div id=main>
    <div id=left>
      <div class=leftbox>
        <h3><a href="{$article['curl']}">{$article['cname']}</a>&gt;&gt;$article[title]</h3>
        <H2>$article[title]</H2>
        <div id=info>发布:{$article['dateline']}     浏览:<span id=spn1>$article[views]</span></div>
        <div id=contents>
EOT;
if (!$article['allowread']) {print <<<EOT
<div class="needpwd"><form action="{$article['aurl']}" method="post">这篇日志被加密了。请输入密码后查看。<br /><input class="formfield" type="password" name="readpassword" style="margin-right:5px;" /> <button class="formbutton" type="submit">提交</button></form></div>
EOT;
} 
else
{print <<<EOT
$article[content]<BR /> 
EOT;
if($article['attachments'])
{
	foreach($article['attachments'] as $image)
	{
		if($image['isimage'])
		{
			print <<<EOT
			<p class="attach">{$image['filename']}<br /><a href="{$image['aurl']}" target="_blank"><img src="{$image['aurl']}" border="0" alt="大小: {$image['filesize']}KB&#13;浏览: {$image['downloads']} 次" /></a></p>
EOT;
		}
	}
	foreach($article['attachments'] as $attach)
	{
		if(!$attach['isimage']) 
		{
			print <<<EOT
			<p class="attach"><strong>附件: </strong><a href="{$image['aurl']}" target="_blank">{$attach['filename']}</a> ({$attach['filesize']}KB, 下载次数:{$attach['downloads']})</p>
EOT;
		}
	}
}
}
print <<<EOT
        </div>
        <div class=pagebreak></div>
      </div><!--end leftbox-->
      <div id=comments>
        <h3>相关信息</h3>
        <ul id=like>
         <li>上下一篇</a> &raquo;</p></li>
          <li>Tag：
EOT;
if($article['tag'])
{foreach($article['tag'] as $tag){
$tagurl=urlencode($tag);
print <<<EOT
<a href='tag.php?item=$tagurl'>$tag</a>&nbsp;
EOT;
}
print <<<EOT
</li>
          <li>原文链接：<a href="{$article['aurl']}">{$article['aurl']}</a></li>
          <li><B>将本文收藏到网摘：</B></li>
        </ul>
      <div class=pagebreak></div>
	  </div><!--end leftbox-->
      <div id=comments>
EOT;
}
if ($article['comments']) {print <<<EOT
<span style="FLOAT:right;padding-bottom: 2px;font-size: 12px;">{$article['comments']}条记录</span>访客评论
EOT;
foreach($commentdb as $key => $comment){print <<<EOT
<div class=cbox><a name="cm{$comment['cid']}"></a><p class="lesscontent" id="comm_$comment[cid]">$comment[content]</p>
<p class="lessdate">Post by $comment[username] on $comment[dateline] <img style="cursor: hand" onclick="addquote('comm_$comment[cid]','$comment[userid]')" src="images/quote.gif" border="0" alt="引用此文发表评论" /> <font color="#000000">#<strong>$comment[cid]</strong></font></p></div>
EOT;
}print <<<EOT
$multipage
<br />
EOT;
}
if (!$article['closed']) {
print <<<EOT
  <a name="addcomment"></a>
  <form method="post" name="form" id="form" action="comment.php" onsubmit="return checkform();">
    <input type="hidden" name="url" value="{$article['url']}" />
    <div class="formbox">
EOT;
if ($uid) {
print <<<EOT
  <p>已经登陆为 <b>$username</b> [<a href="profile.php?action=logout">注销</a>]</p>
EOT;
} else {print <<<EOT
  <p>
    <label for="username">
    名字 (必填):<br /><input name="username" id="username" type="text" value="$comment_username" tabindex="1" class="formfield" style="width: 210px;" /></label>
  </p>
  <p>
    <label for="password">
    密码 (游客不需要密码):<br /><input name="password" id="password" type="password" value="" tabindex="2" class="formfield" style="width: 210px;" /></label>
  </p>
  <p>
    <label for="url">
    网址或电子邮件 (选填):<br /><input type="text" name="url" id="url" value="$comment_url" tabindex="3" class="formfield" style="width: 210px;" /></label>
  </p>
EOT;
}print <<<EOT
  <p>评论内容 (必填):<br />
	<textarea name="content" cols="84" rows="6" tabindex="4" onkeydown="ctlent(event);" class="formfield" id="content">$cmcontent</textarea>
  </p>
EOT;
if ($host['audit_comment'] && $groupid < 2) {print <<<EOT
  <p>
    <label for="clientcode">
    验证码(*):<br /><input name="clientcode" id="clientcode" value="" tabindex="5" class="formfield" size="6" maxlength="6" /> <img id="seccode" class="codeimg" src="captcha.php" alt="单击图片换张图片" border="0" onclick="this.src='captcha.php?update=' + Math.random()" /></label>(*请输入图片后三位数字)
  </p>
EOT;
}print <<<EOT
      <p><input type="hidden" name="action" value="addcomment" />
          <button type="submit" name="submit" class="formbutton">提交</button></p>
	</div>
  </form>
EOT;
} else {print <<<EOT
<p align="center"><strong>本文因为某种原因此时不允许访客进行评论</strong></p>
EOT;
}print <<<EOT
    </div><!--end comments-->
	</div><!--end left-->
    <div id=right>
      <div class=rightbox>
        <h3>相关文章</h3>
        <ul>
EOT;
if(isset($likedata)){
foreach($likedata as $key => $title){
print <<<EOT
          <li><a href="{$title['aurl']}" title="$title[title],浏览$title[views]">$title[title]</a></li>
EOT;
}}print <<<EOT
        </ul>
      </div>
      <div class=rightbox>
        <h3>阅读排行</h3>
        <ul>
EOT;
foreach($hotdata AS $data){
print <<<EOT
        <li><a href="{$data['aurl']}" title="$data[title],浏览$data[views]">$data[title]</a></li>
EOT;
}print <<<EOT
        </ul>
      </div>
    </div><!--end right-->
  </div><!--end main-->
EOT;
include RQ_DATA."/themes/$theme/footer.php";
?>
