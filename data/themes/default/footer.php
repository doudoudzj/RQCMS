<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
print <<<EOT
  <div class=foot>
    Copyright © 2010-2010 <a href="http://{$constant['RQ_HOST']}">{$host['name']}</a> All Rights Reserved. Powered by <a href="{$constant['RQ_WEBSITE']}" 
target=_blank><B>{$constant['RQ_AppName']}</B> </a><br />
EOT;
print <<<EOT
    <a href="http://validator.w3.org/check?uri=referer" target="_blank">XHTML 1.0</a>. <a href="profile.php?action=clearcookies">清除Cookies</a> 
EOT;
if($host['icp']){print <<<EOT
<a href="http://www.miibeian.gov.cn/" target="_blank">$host[icp]</a>
EOT;
}print <<<EOT
  </div>
</div>
</body>
</html>
EOT;
?>