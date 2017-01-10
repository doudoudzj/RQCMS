<?php
include RQ_DATA."/themes/$theme/header.php";
?>
分类名称 $cate['name'];<br>
<?php echo $cate['name'];?>
<hr>分类链接 $cate['curl'];<br>
<?php echo $cate['curl'];?>
<hr>分页函数在header中，可自己修改 $multipage=pagination($allcount,$host['list_shownum'],$page,'category',$cate['url']);<br>
文章列表 $articledb  ,包含分页<br>
<?php
include RQ_DATA."/themes/$theme/list.php";
?>

<hr>推荐文章 $stickcache=getStickArticle(10); <br>
<ul>
<?php
 $stickcache=getStickArticle(10);
foreach($stickcache AS $data){
?>
          <li><a href="<?php echo $data['aurl'];?>" title="<?php echo $data['title'];?>"><?php echo $data['title'];?></a></li>
<?php
}?>
</ul>
<hr>热门文章 $hotcache=getHotArticle(10,$cate['cid']);<br>

<ul>
<?php
$hotcache=getHotArticle(10,$cate['cid']);
foreach($hotcache AS $data){
?>
        <li><a href="<?php echo $data['aurl'];?>" title="<?php echo $data['title'];?>,浏览<?php echo $data['views'];?>"><?php echo $data['title'];?></a></li>
<?php
}?>
</ul>

<?php
include RQ_DATA."/themes/$theme/footer.php";
?>
