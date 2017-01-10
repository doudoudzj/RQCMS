<?php
$latestarray=getLatestArticle(10);

include RQ_DATA."/themes/$theme/header.php";
?>
最新文章调用，在所有页面都可以使用<br>
$top10cache=getLatestArticle(10);<br>
系统默认是20条缓存，多的会查询数据库<br>
<ul>
<?php
$top10cache=getLatestArticle(10);
foreach($top10cache AS $data){
?>
    <li><a href="<?php echo $data['aurl']; ?>" title="<?php echo $data['title']; ?>"><?php echo $data['title'];?></a></li>
<?php
}?>
</ul>
<hr>
栏目循环和每个栏目最新文章,$cateid是栏目id<br>
$value=getLatestArticle(5,$cateid);<br>
<?php
foreach($category as $cateid=>$cname){
?>
        <h3>栏目信息：<a href="<?php echo $cname['curl']; ?>"><?php echo $cname['name'];;?></a></h3>
        <ul>
<?php
$value=getLatestArticle(5,$cateid);

if(!empty($value))
{
foreach($value AS $data){
?>
           <li><a href="<?php echo $data['aurl']; ?>" title="<?php echo $data['title']; ?>"><?php echo $data['title'];;?></a></li>
<?php
}}
?>
 </ul>
<?php
}
?>

<hr>  
        <h3>热门文章</h3>
		$hotcache=getHotArticle(10);
        <ul>
<?php
$hotcache=getHotArticle(10);
foreach($hotcache as $data){ ?>
          <li><a href="<?php echo $data['aurl']; ?>" title="<?php echo $data['title']; ?>"><?php echo $data['title'];;?></a></li>
<?php
}?>
        </ul>
 <hr>
        <h3>推荐文章</h3>
		$stickcache=getStickArticle(10);
        <ul>
<?php
$stickcache=getStickArticle(10);
foreach($stickcache AS $data){
?>
          <li><a href="<?php echo $data['aurl']; ?>" title="<?php echo $data['title']; ?>"><?php echo $data['title'];;?></a></li>
<?php
}?>
        </ul>
<hr>
	<h3>友情链接:</h3>
	$linkarr=isset($setting['link'])?$setting['link']:array();
    <ul>
<?php
$linkarr=isset($setting['link'])?$setting['link']:array();
if($linkarr){
foreach($linkarr AS $link){
?>
      <li><a href="<?php echo $link['url'];?>" target="_blank" title="<?php echo $link['note'];?>"><?php echo $link['name'];?></a></li>
<?php
}}?>
    </ul>
<hr>
备案信息
<a href="http://www.miibeian.gov.cn/" target="_blank"><?php echo $host['icp'];?></a>
<?php
include RQ_DATA."/themes/$theme/footer.php";
?>