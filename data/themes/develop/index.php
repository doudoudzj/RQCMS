<?php
$top10cache=getLatestArticle(10);
$stickcache=getStickArticle(10);
$picscache=getPicArticle(5);
$commentdata=getLatestComment(10);
$linkarr=getLink();
$hotcache=getHotArticle(10);
$listcache=array();
$latestarray=@include RQ_DATA.'/cache/latest_'.$host['host'].'.php';
//得到最新的所有栏目的文章id
if($latestarray)
{
	unset($latestarray['cateids'][0]);
	$listcache=$latestarray['cateids'];
}

include RQ_DATA."/themes/$theme/header.php";
include_once RQ_CORE.'/include/category.php';
$appcate=getChildArr(1,$cateArr);

?>
<h3>最新文章$picscache</h3>
<div id=focus><dl><dt>
<?php
foreach($picscache as $k=>$v){
?>
<a href="<?php echo $v['url']; ?>" title="<?php echo $v['title']; ?>">$k</a>
<?php
}?>
<dd>
<?php
foreach($picscache as $k=>$v){?>
<img src="<?php echo $v['aurl']; ?>" id="pic<?php echo $v['aid']; ?>" />
<?php
}?>
</dd></dl></div>
<hr>

        <div id=focist>
          <ul>
<?php
foreach($top10cache AS $data){
?>
            <li><a href="<?php echo $data['aurl']; ?>" title="<?php echo $data['title']; ?>"><?php echo $data['title'];?></a></li>
<?php
}?>
          </ul>
        </div>
<?php
foreach($cateArr as $cateid=>$cname){
if(isset($listcache[$cateid])){//隐藏的栏目不显示
?>
      <div class=box>
        <h3><a href="<?php echo $cname['curl']; ?>"><?php echo $cname['name'];;?></a></h3>
        <ul>
<?php
$value=$listcache[$cateid];
if(!empty($value))
{
foreach($value AS $k=>$v){
$data=$latestarray['article'][$v];
?>
           <li><a href="<?php echo $data['aurl']; ?>" title="<?php echo $data['title']; ?>"><?php echo $data['title'];;?></a></li>
<?php
}}?>
        </ul>
      </div>
<?php
}}
?>
      <div id=oneline></div>
    </div>

	
<hr>	
<div class=rightbox>
	<h3>热门文章 $hotcache</h3>
	<ul>
<?php
foreach($hotcache as $data){ ?>
          <li><a href="<?php echo $data['aurl']; ?>" title="<?php echo $data['title']; ?>"><?php echo $data['title'];;?></a></li>
<?php
}?>
        </ul>
      </div>
<hr>
      <div class=rightbox>
        <h3>推荐文章 $stickcache</h3>
        <ul>
<?php
foreach($stickcache AS $data){
?>
          <li><a href="<?php echo $data['aurl']; ?>" title="<?php echo $data['title']; ?>"><?php echo $data['title'];;?></a></li>
<?php
}?>
        </ul>
      </div>
<hr>
      <div class=rightbox>
        <h3>最新评论 $commentdata</h3>
        <ul>
<?php
foreach($commentdata AS $data){
?>
         <li><a href="<?php echo $data['url']; ?>"><?php echo $data['content'];?></a></li>
<?php
}?>
        </ul>
      </div>
<hr>
  <div class=links>
	<h3>友情链接: $linkarr</h3>
    <ul>
<?php
if($linkarr){
foreach($linkarr AS $link){
?>
      <li><a href="<?php echo $link['url'];?>" target="_blank" title="<?php echo $link['note'];?>"><?php echo $link['name'];?></a></li>
<?php
}}?>
    </ul>
  </div>
  
  
<?php
include RQ_DATA."/themes/$theme/footer.php";
?>