<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
if ($total) {print <<<EOT
        <ul id=list>
EOT;
foreach($articledb as $key => $article){
print <<<EOT
          <li><span class=postdate>$article[dateline]</span> <a href="$article[aurl]" title="$article[excerpt]">$article[title]</a> </li>
EOT;
}print <<<EOT
        </ul>
EOT;
if($multipage){
print <<<EOT
        $multipage
EOT;
}
} else {print <<<EOT
<p><strong>没有任何文章</strong></p>
EOT;
}
?>