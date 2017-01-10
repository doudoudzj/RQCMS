<?php
$tempView=$coreView;
$rssdb=array();//rss数据

if(isset($_GET['url1'])&&$_GET['url1'])
{
	$rssdb=array();
	foreach($category as $ct)
	{
		if($ct['url']==$_GET['url1']) $cate=$ct;
	}
}
if($host['rss_num']>20)
{
	$cateadd=isset($cate)?" where cateid={$cate['cid']}":'';
	$rquery= $DB->query('SELECT * FROM `'.DB_PREFIX.'article` '.$cateadd.' ORDER BY aid DESC limit '.$host['rss_num']);
	while($article=$DB->fetch_array($rquery))
	{
		$rssdb[]=showArticle($article);
	}
}
else
{
	$rssdb=getLatestArticle($host['rss_num']);
	if(isset($cate)) $rssdb=getLatestArticle($host['rss_num'],$cate['cid']);
}

doAction('rss_before_output',$rssdb);
$contentType="Content-Type: application/xml";
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<rss version=\"2.0\">\n";
echo "\t<channel>\n";
echo "\t\t<title>".htmlspecialchars($host['name'])."</title>\n";
echo "\t\t<link>http://".$host['host']."</link>\n";
echo "\t\t<description>".htmlspecialchars($host['description'])."</description>\n";
echo "\t\t<lastBuildDate>".date('r', $timestamp)."</lastBuildDate>\n";
echo "\t\t<ttl>".$host['rss_ttl']."</ttl>\n";
if ($rssdb&&is_array($rssdb)) {
	foreach ($rssdb AS $article) {
		$articleurl = RQ_HTTP.RQ_HOST.'/'.$article['aurl'];
		echo "\t\t<item>\n";
		echo "\t\t\t<guid>".$articleurl."</guid>\n";
		echo "\t\t\t<title>".$article['title']."</title>\n";
		if ($article['password']) {
			echo "\t\t\t<description>文章需要输入密码才能浏览.</description>\n";
		} else {
			echo "\t\t\t<description><![CDATA[".$article['excerpt']."]]></description>\n";
		}
		echo "\t\t\t<link>".$articleurl."</link>\n";
		echo "\t\t\t<pubDate>".$article['dateline']."</pubDate>\n";
		echo "\t\t</item>\n";
	}
}
echo "\t</channel>\n";
echo "</rss>";