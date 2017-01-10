<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
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
print <<<EOT
  <div id=main>
    <div id=left>
      <h3>最新文章</h3>
      <div class=leftbox_index>
        <div id=focus><dl><dt>
EOT;
foreach($picscache as $k=>$v){
print <<<EOT
<a href="{$v['url']}" title="{$v['title']}">$k</a>
EOT;
}print <<<EOT
<dd>
EOT;
foreach($picscache as $k=>$v){print <<<EOT
<img src="{$v['aurl']}" id="pic{$v['aid']}" />
EOT;
}print <<<EOT
</dd></dl></div>
        </div>
        <div id=focist>
          <ul>
EOT;
foreach($top10cache AS $data){
print <<<EOT
            <li><a href="{$data['aurl']}" title="{$data['title']}">{$data['title']}</a></li>
EOT;
}print <<<EOT
          </ul>
        </div>
EOT;
foreach($cateArr as $cateid=>$cname){
if(isset($listcache[$cateid])){//隐藏的栏目不显示
print <<<EOT
      <div class=box>
        <h3><a href="{$cname['curl']}">{$cname['name']}</a></h3>
        <ul>
EOT;
$value=$listcache[$cateid];
if(!empty($value))
{
foreach($value AS $k=>$v){
$data=$latestarray['article'][$v];
print <<<EOT
           <li><a href="{$data['aurl']}" title="{$data['title']}">{$data['title']}</a></li>
EOT;
}}print <<<EOT
        </ul>
      </div>
EOT;
}}
print <<<EOT
      <div id=oneline></div>
    </div>
    <div id=right>
      <div class=rightbox>
        <h3>热门文章</h3>
        <ul>
EOT;
foreach($hotcache as $data){ print <<<EOT
          <li><a href="{$data['aurl']}" title="{$data['title']}">{$data['title']}</a></li>
EOT;
}print <<<EOT
        </ul>
      </div>
      <div class=rightbox>
        <h3>推荐文章</h3>
        <ul>
EOT;
foreach($stickcache AS $data){
print <<<EOT
          <li><a href="{$data['aurl']}" title="{$data['title']}">{$data['title']}</a></li>
EOT;
}print <<<EOT
        </ul>
      </div>
      <div class=rightbox>
        <h3>最新评论</h3>
        <ul>
EOT;
foreach($commentdata AS $data){
print <<<EOT
         <li><a href="{$data['url']}">{$data['content']}</a></li>
EOT;
}print <<<EOT
        </ul>
      </div>
    </div>
  </div>
  <div class=links>
	<h3>友情链接:</h3>
    <ul>
EOT;
if($linkarr){
foreach($linkarr AS $link){
print <<<EOT
      <li><a href="$link[url]" target="_blank" title="$link[note]">$link[name]</a></li>
EOT;
}}print <<<EOT
    </ul>
  </div>
EOT;
include RQ_DATA."/themes/$theme/footer.php";
?>