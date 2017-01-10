<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
print <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="UTF-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta name="keywords" content="{$host['keywords']}" />
<meta name="description" content="{$host['description']}" />
<meta name="copyright" content="rq204" />
<meta name="author" content="rq204" />
<link rel="stylesheet" href="images/common.css" type="text/css" media="all"  />
EOT;
if ($returnurl) {
print <<<EOT
<meta http-equiv="REFRESH" content="3;URL=$returnurl">
EOT;
}print <<<EOT
<title>系统消息 $host[name]</title>
</head>
<body>
<div id="message">
  <h2>$host[name]</h2>
  <p style="margin-bottom:20px;"><strong>$msg</strong></p>
EOT;
if ($returnurl) {print <<<EOT
  <p>2秒后将自动跳转<br /><a href="$returnurl">如果不想等待或浏览器没有自动跳转请点击这里跳转</a></p>
EOT;
}print <<<EOT
</div>
</body>
</html>
EOT;
?>