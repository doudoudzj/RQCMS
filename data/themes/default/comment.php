<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
$top10cache=getLatestArticle(10);
$host10comments=getHotComment(10);

include RQ_DATA."/themes/$theme/header.php";
print <<<EOT
<div id=main>
<div id=left>
<div class=leftbox>
<h3>当前位置&gt;&gt;查看评论</h3>
EOT;
if ($total) {
foreach($commentdb as $key => $comment){print <<<EOT
<p class="art-title"><a href="{$comment['aurl']}">$comment[title]</a></p><p class="lesscontent">$comment[content]</p>
<p class="lessdate">Post by {$comment['username']} on {$comment['commentdate']}</p>
EOT;
}print <<<EOT
$multipage
EOT;
} else {print <<<EOT
<p><strong>没有任何评论</strong></p>
EOT;
}print <<<EOT
</div></div>
<div id=right>
<div class=rightbox>
<h3>热评文章</h3>
<ul>
EOT;
foreach($host10comments AS $data){
print <<<EOT
          <li><a href="$data[aurl]" title="$data[title]">$data[title]</a></li>
EOT;
}print <<<EOT
</ul></div>
<div class=rightbox>
<h3>热门文章</h3>
<ul>
EOT;
foreach($top10cache AS $data){
print <<<EOT
        <li><a href="$data[aurl]" title="$data[title],浏览$data[views]">$data[title]</a></li>
EOT;
}print <<<EOT
</ul></div>
</div></div>
EOT;
include RQ_DATA."/themes/$theme/footer.php";
?>
