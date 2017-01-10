<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
print <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="UTF-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta name="author" content="RQ204" />
<title>{$host['name']} - RQCMS {$constant['RQ_VERSION']}</title>
<link rel="stylesheet" href="{$cssfile}" type="text/css">
<script type="text/javascript" src="{$cssdir}global.js"></script>
</head>
<body>
<a name="TOP" id="TOP"></a>
<table width="100%" border="0" cellpadding="0" cellspacing="0" background="{$cssdir}page_bg.jpg">
  <tr>
    <td><div class="topBar">
      <table border="0" cellspacing="0" cellpadding="0" style="width:100%;">
        <tr>
          <td class="topLinksLeft"></td>
EOT;
if ($groupid) {
print <<<EOT
          <td class="topLinks">欢迎您 $username [<a href="admin.php?file=login&action=logout">注销身份</a>] 
EOT;
if($groupid==4) echo ' [<a href="admin.php?file=special">站点管理</a>]';
if ($groupid) print <<<EOT
  [<a href="../index.php" target="_blank">站点首页</a>]
</td>
        </tr>
      </table>
    </div>
EOT;
}if (isset($adminitem) && $adminitem) {print <<<EOT
    <table width="100%" height="25" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td>&nbsp;</td>
EOT;
foreach ($adminitem AS $link => $title)	{
print <<<EOT
         <td width="9%" class="navcell" onMouseover="document.getElementById('$link').className='cpnavmenuHover'" onMouseout="document.getElementById('{$link}').className='cpnavmenu'"><div class="cpnavmenu" id="{$link}"><a href="admin.php?file={$link}">{$title}</a></div></td>
EOT;
}print <<<EOT
          <td>&nbsp;</td>
        </tr>
      </table>
EOT;
}print <<<EOT
</td>
  </tr>
</table>
EOT;
?>