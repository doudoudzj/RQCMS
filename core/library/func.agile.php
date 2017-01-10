<?php
/**
 * Seo相关的，如Url处理，缓存等
 */

/**
 * 将数组写入缓存文件
 *
 * @param string $cacheFile 要保存的文件路径
 * @param array $cacheArray 需要保存的数组
 * @return false or strlen
 */
function writeCache($cacheFile,$cacheArray)
{
	if(!is_array($cacheArray)) return false;
	$array = "<?php\nreturn ".var_export($cacheArray, true).";\n?>";
	$wirteFile = RQ_DATA.'/cache/'.$cacheFile.'.php';
	$strlen = file_put_contents($wirteFile, $array);
	@chmod($wirteFile, 0777);
	return $strlen;
}


//生成新的网址
function mkUrl($file,$url1='',$url2='',$url3='')
{
	global $setting,$host;
	$url1=rawurlencode($url1);
	if(isset($setting['filemap'])&&is_array($setting['filemap']))
	{	
		$map=array_flip($setting['filemap']);
		if(isset($map[$file])) 
		{
			$newurl='/'.$map[$file];
			if($url1) $newurl.='/'.$url1;
			if($url2) $newurl.='/'.$url2;
			if($url3) $newurl.='/'.$url3;
			if($url1) $newurl.='.'.$host['url_ext'];
			else $newurl.='/index.'.$host['url_ext'];
			return $newurl;
		}
		return '';
	}
	else
	{
		$newurl='/'.$file;
		if($url1) $newurl.='/'.$url1;
		if($url2) $newurl.='/'.$url2;
		if($url3) $newurl.='/'.$url3;
		if($url1) $newurl.='.'.$host['url_ext'];
		else $newurl.='/index.'.$host['url_ext'];
		return $newurl;
	}
}

function message($msg,$returnurl='')
{
	global $theme,$host;
	if(!$returnurl) $returnurl='http://'.$host['host'];
	include RQ_DATA."/themes/$theme/message.php";
	exit();
}

function showArticle($article)
{
	global $host,$category;
	$article['month'] = date('M', $article['dateline']);
	$article['day'] = date('d', $article['dateline']);
	$article['lastmodified']=$article['modified'];
	$article['dateline']=date($host['time_format'],$article['dateline']);
	$article['modified']=date($host['time_format'],$article['modified']);
	$article['aurl'] = mkUrl('article',$article['url'],0);
	$article['curl'] = mkUrl('category',$category[$article['cateid']]['url'],0);
	$article['attachments']=$article['attachments'];
	return $article;
}

function cacheControl($lastmodified)
{
	$lastmodified=gmdate('D, d M Y H:i:s',$lastmodified).' GMT';
	if(array_key_exists('HTTP_IF_MODIFIED_SINCE',$_SERVER))
	{
		if($_SERVER['HTTP_IF_MODIFIED_SINCE']==$lastmodified)
		{
			header('HTTP/1.0 304 Not Modified');
			exit;
		}
	}
	else
	{
		header("Cache-Control: max-age=259200");
		header("Last-Modified: ".$lastmodified); //Fri, 31 Oct 2008 02:14:04 GMT
	}
}

function setMap($original,$filename)
{
	global $setting;
	$map=array_flip($setting['filemap']);
	$map[$original]=$filename;
	$setting['filemap']=array_flip($map);
}