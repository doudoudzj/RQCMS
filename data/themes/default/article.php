<?php
include RQ_DATA."/themes/$theme/header.php";
?>

文章标题：$article['title'];<br>
<?php echo $article['title'];?>
<hr>发布时间:$article['dateline'];<br>
<?php echo $article['dateline'];?>
<hr>浏览量：$article['views'];<br>
<?php echo $article['views'];?>
<hr>分类链接 $article['curl'];<br>
<?php echo $article['curl'];?>
<hr>分类名称：$article['cname'];<br>
<?php echo $article['cname'];?>
<hr>文章内容：echo $article['content'];<br>
<?php 	echo $article['content']; ?>
<hr>附件：$article['attachments'];<br>
<?php
if($article['attachments'])
{
	foreach($article['attachments'] as $image)
	{
		if($image['isimage'])
		{
			?>
			<p class="attach"><?php echo $image['filename'];?><br /><a href="<?php echo $image['aurl'];?>" target="_blank"><img src="<?php echo $image['aurl'];?>" border="0" alt="大小: <?php echo $image['filesize'];?>KB&#13;浏览: <?php echo $image['downloads'];?> 次" /></a></p>
<?php
		}
	}
	foreach($article['attachments'] as $attach)
	{
		if(!$attach['isimage']) 
		{
			?>
			<p class="attach"><strong>附件: </strong><a href="<?php echo $image['aurl'];?>" target="_blank"><?php echo $attach['filename'];?></a> (<?php echo $attach['filesize'];?>KB, 下载次数:<?php echo $attach['downloads'];?>)</p>
<?php
		}
	}
}
?>
<hr>Tags:$article['tag']<br><ul>
<?php
if($article['tag'])
{
	foreach($article['tag'] as $tag)
	{
	$tagurl=mkUrl('tag',$tag);
	?>
	<li><a href='<?php echo $tagurl;?>'><?php echo $tag;?></a></li>
	<?php
	}
}
?>
</ul>
<hr>原文链接:$article['aurl'];<br>
<?php echo $article['aurl'];?>
<hr>相关文章 if(is_array($article['tag'])) $likedata=getRelatedArticle($article['aid'],$article['tag'],10);<br>
<ul>
<?php
if(is_array($article['tag'])) $likedata=getRelatedArticle($article['aid'],$article['tag'],10);
if(isset($likedata)){
foreach($likedata as $key => $title){
?>
          <li><a href="<?php echo $title['aurl'];?>" title="<?php echo $title['title'];?>,浏览<?php echo $title['views'];?>"><?php echo $title['title'];?></a></li>
<?php
}}?>
</ul>
<hr>阅读排行 $hotdata=getHotArticle(10,$article['cateid']); <br>
<ul>
<?php
$hotdata=getHotArticle(10,$article['cateid']);
foreach($hotdata AS $data){
?>
        <li><a href="<?php echo $data['aurl'];?>" title="<?php echo $data['title'];?>,浏览<?php echo $data['views'];?>"><?php echo $data['title'];?></a></li>
<?php
}?>
</ul>
</ul>
<?php
include RQ_DATA."/themes/$theme/footer.php";
?>
