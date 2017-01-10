<?php
//在不同的页面，有不同的title,keywords,description

function pagination($count,$perlogs,$page,$file,$url){
	$pnums = @ceil($count / $perlogs);
	$re = '';
	for ($i = $page-5;$i <= $page+5 && $i <= $pnums; $i++){
		if ($i > 0){
			if ($i == $page){
				$re .= " <span>$i</span> ";
			} else {
				$curl=mkUrl($file,$url,$i);
				$re .= " <a href=\"$curl\">$i</a> ";
			}
		}
	}
	$u1=mkUrl($file,$url,1);
	$uend=mkUrl($file,$url,$pnums);
	if ($page > 6) $re = "<a href=\"<?php echo $u1}\" title=\"首页\">&laquo;</a><em>...</em>$re";
	if ($page + 5 < $pnums) $re .= "<em>...</em> <a href=\"<?php echo $uend}\" title=\"尾页\">&raquo;</a>";
	if ($pnums <= 1) $re = '';
	return $re;
}

if(!isset($keywords)) $keywords=$host['keywords'];
if(!isset($description)) $description=$host['description'];

$homeurl='/';
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<meta name="keywords" content="<?php echo $keywords; ?>">
<meta name="description" content="<?php echo $description; ?>">
<meta content="text/html; charset=utf-8" http-equiv=Content-Type>
<base href="<?php echo $host_url; ?>">
<link title="<?php echo $host['name']; ?>" rel=alternate type=application/rss+xml href="<?php echo $rss_url; ?>">
</head><body>