<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
$stickcache=getStickArticle(10);
$hotcache=getHotArticle(10);
include RQ_DATA."/themes/{$theme}/header.php";
print <<<EOT
  <div id=main>
EOT;
if (isset($articledb)&&$keywords) {print <<<EOT
    <div id=left>
      <div class=leftbox>
        <h3>关键字&gt;&gt;{$keywords}</h3>
EOT;
require RQ_DATA."/themes/{$theme}/list.php";
print <<<EOT
      </div>
    </div>
    <div id=right>
      <div class=rightbox>
        <h3>推荐文章</h3>
        <ul>
EOT;
foreach($stickcache AS $data){
print <<<EOT
          <li><a href="show.php?$data[arg]" title="$data[title]" target="_parent">$data[title]</a></li>
EOT;
}print <<<EOT
        </ul>
      </div>
      <div class=rightbox>
        <h3>热门文章</h3>
        <ul>
EOT;
foreach($hotcache AS $data){
print <<<EOT
        <li><a href="article.php?$data[arg]" title="$data[title],浏览$data[views]" target="_parent">$data[title]</a></li>
EOT;
}print <<<EOT
        </ul>
      </div>
EOT;
}else{print <<<EOT
    <div id=fullbox>
      <div style=" margin:40px auto; width:880px; text-align:center;">
      <p style=" margin:10px auto; width:600px; text-align:center; font-weight:bold;color:#1c5E96;">{$host['name']}搜索</p>
      <form action="search.php" method="post" >
      <input type="hidden" name="formhash" value="53368c9c"/>
      <span style="font-family:宋体; font-size:16px; font-weight:600; color:#1c5E96;">关键字:</span>
      <input type="text" name="keywords" id="keywords" type="text" value="" onmouseover="this.focus()"  autocomplete="off" style=" width:220px; height:22px; line-height:22px;"/> <input style="margin-left:8px; height:30px;" type="submit" id="go" value="搜 &nbsp; 索" /></form>
    </div>
EOT;
}print <<<EOT
  </div>
EOT;
include RQ_DATA."/themes/{$theme}/footer.php";