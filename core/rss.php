<?php
$tempView=$coreView;
$rssfile = RQ_DATA.'/cache/rss_'.$host['host'].'.php';
$rssdb=@include($rssfile);
if(!$rssdb) $rssdb=array();//rss数据
doAction('rss_before_output',$rssdb);
$ContentType="Content-Type: application/xml";
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<rss version=\"2.0\">\n";
echo "\t<channel>\n";
echo "\t\t<title>".htmlspecialchars($host['name'])."</title>\n";
echo "\t\t<link>http://".$host['host']."</link>\n";
echo "\t\t<description>".htmlspecialchars($host['description'])."</description>\n";
echo "\t\t<lastBuildDate>".date('r', $timestamp)."</lastBuildDate>\n";
echo "\t\t<ttl>".$host['rss_ttl']."</ttl>\n";
if ($rssdb&&is_array($rssdb)) {//well 如果这里使用泛解析域名的话怎么处理呢？
	foreach ($rssdb AS $article) {
		$articleurl = 'http://'.RQ_HOST.'/article.php?'.$article['arg'];
		echo "\t\t<item>\n";
		echo "\t\t\t<guid>".$articleurl."</guid>\n";
		echo "\t\t\t<title>".$article['title']."</title>\n";
		echo "\t\t\t<author>".$article['username']."</author>\n";
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