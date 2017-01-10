<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
$stickcache=getStickArticle(10);//置顶文章
$hotcache=getHotArticle(10,$cate['cid']);

$rss_url=mkUrl('rss.php',$cate['url']);

include RQ_DATA."/themes/{$theme}/header.php";

$multipage=pagination($allcount,$host['list_shownum'],$page,'category.php',$cate['url']);//todo

print <<<EOT
<div id=main>
<div id=left>
<div class=leftbox>
<h3>当前位置&gt;&gt;<a href="{$cate['curl']}">{$cate['name']}</a></h3>
EOT;
include RQ_DATA."/themes/{$theme}/list.php";
print <<<EOT
</div></div>
<div id=right>
<div class=rightbox>
<h3>推荐文章</h3>
<ul>
EOT;
foreach($stickcache AS $data){
print <<<EOT
          <li><a href="{$data['aurl']}" title="$data[title]">$data[title]</a></li>
EOT;
}print <<<EOT
</ul></div>
<div class=rightbox>
<h3>热门文章</h3>
<ul>
EOT;
foreach($hotcache AS $data){
print <<<EOT
        <li><a href="$data[aurl]" title="$data[title],浏览$data[views]">$data[title]</a></li>
EOT;
}print <<<EOT
</ul></div>
</div></div>
EOT;
include RQ_DATA."/themes/$theme/footer.php";
?>
