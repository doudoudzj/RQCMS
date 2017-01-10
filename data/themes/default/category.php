<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
$randdata=getStickArticle(10,$cate['cid']);
$stickcache=getStickArticle(10);
$hotcache=getHotArticle(10,$cate['cid']);

include RQ_DATA."/themes/{$theme}/header.php";
print <<<EOT
<div id=main>
<div id=left>
<div class=leftbox>
<h3>当前位置&gt;&gt;<a href="category.php?{$cate['crg']}">{$cate['name']}</a></h3>
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
          <li><a href="article.php?{$data['arg']}" title="$data[title]" target="_parent">$data[title]</a></li>
EOT;
}print <<<EOT
</ul></div>
<div class=rightbox>
<h3>热门文章</h3>
<ul>
EOT;
foreach($hotcache AS $data){
print <<<EOT
        <li><a href="article.php?$data[arg]" title="$data[title],浏览$data[views]" target="_parent">$data[title]</a></li>
EOT;
}print <<<EOT
</ul></div>
<div class=rightbox>
<h3>随机推荐</h3>
<ul>
EOT;
foreach($randdata as $data){
print <<<EOT
        <li><a href="article.php?{$data['arg']}" title="$data[title]" target="_parent">$data[title]</a></li>
EOT;
}print <<<EOT
</ul></div>
</div></div>
EOT;
include RQ_DATA."/themes/$theme/footer.php";
?>
