DROP TABLE IF EXISTS `rqcms_article`;
CREATE TABLE `rqcms_article` (
  `aid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `oid` mediumint(8) NOT NULL,
  `hostid` tinyint(3) NOT NULL,
  `cateid` smallint(4) unsigned NOT NULL DEFAULT '0',
  `userid` smallint(5) unsigned NOT NULL,
  `username` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '',
  `keywords` varchar(120) NOT NULL DEFAULT '',
  `tag` varchar(100) NOT NULL,
  `url` char(60) NOT NULL,
  `thumb` mediumint(8) NOT NULL DEFAULT '0',
  `source` varchar(1000) NOT NULL,
  `excerpt` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `modified` int(10) NOT NULL,
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `comments` smallint(5) unsigned NOT NULL DEFAULT '0',
  `attachments` varchar(20000) NOT NULL,
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `stick` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `score` smallint(5) NOT NULL DEFAULT '0',
  `password` varchar(20) NOT NULL,
  `ban` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`aid`),
  KEY `article` (`oid`,`hostid`,`cateid`,`userid`,`url`,`dateline`,`visible`,`modified`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `rqcms_attachment`;
CREATE TABLE `rqcms_attachment` (
  `aid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `hostid` tinyint(3) NOT NULL,
  `articleid` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(100) NOT NULL DEFAULT '',
  `filetype` varchar(50) NOT NULL DEFAULT '',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `downloads` mediumint(8) NOT NULL DEFAULT '0',
  `filepath` varchar(255) NOT NULL DEFAULT '',
  `thumb_filepath` varchar(255) NOT NULL DEFAULT '',
  `thumb_width` smallint(5) NOT NULL DEFAULT '0',
  `thumb_height` smallint(5) NOT NULL DEFAULT '0',
  `isimage` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `score` smallint(5) NOT NULL DEFAULT '0',
  `modified` int(10) NOT NULL,
  PRIMARY KEY (`aid`),
  KEY `attachment` (`articleid`,`isimage`,`dateline`,`modified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `rqcms_category`;
CREATE TABLE `rqcms_category` (
  `cid` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `oid` mediumint(8) NOT NULL,
  `hostid` tinyint(3) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `url` char(60) NOT NULL DEFAULT '',
  `pid` smallint(4) NOT NULL DEFAULT '0',
  `style` varchar(20) NOT NULL,
  `keywords` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(300) NOT NULL DEFAULT '',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `displayorder` SMALLINT(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cid`,`url`,`oid`,`visible`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `rqcms_category` (`cid`, `hostid`, `name`, `pid`, `style`, `keywords`, `description`, `displayorder`,`url`) VALUES (NULL, '1', '默认栏目', '0', '', '', '', '0','hello');

DROP TABLE IF EXISTS `rqcms_comment`;
CREATE TABLE `rqcms_comment` (
  `cid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `hostid` tinyint(3) NOT NULL,
  `articleid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `userid` smallint(5) NOT NULL DEFAULT '0',
  `username` varchar(50) NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `content` mediumtext NOT NULL,
  `url` char(60) NOT NULL,
  `email`  char(60) NOT NULL,
  `attachment` text NOT NULL,
  `ipaddress` varchar(16) NOT NULL DEFAULT '',
  `score` smallint(5) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `ban` tinyint(1) NOT NULL,
  PRIMARY KEY (`cid`),
  KEY `comment` (`articleid`,`dateline`,`ipaddress`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `rqcms_filemap`;
CREATE TABLE `rqcms_filemap` (
  `hostid` tinyint(3) NOT NULL,
  `original` varchar(15) NOT NULL,
  `filename` varchar(15) NOT NULL,
  `maps` varchar(1000) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
Insert Into `rqcms_filemap` (`hostid`,`original`,`filename`) values ('1','index.php','index.php');
Insert Into `rqcms_filemap` (`hostid`,`original`,`filename`) values ('1','admin.php','admin.php');
Insert Into `rqcms_filemap` (`hostid`,`original`,`filename`) values ('1','attachment.php','attachment.php');
Insert Into `rqcms_filemap` (`hostid`,`original`,`filename`) values ('1','category.php','category.php');
Insert Into `rqcms_filemap` (`hostid`,`original`,`filename`) values ('1','captcha.php','captcha.php');
Insert Into `rqcms_filemap` (`hostid`,`original`,`filename`) values ('1','comment.php','comment.php');
Insert Into `rqcms_filemap` (`hostid`,`original`,`filename`) values ('1','profile.php','profile.php');
Insert Into `rqcms_filemap` (`hostid`,`original`,`filename`) values ('1','rss.php','rss.php');
Insert Into `rqcms_filemap` (`hostid`,`original`,`filename`) values ('1','search.php','search.php');
Insert Into `rqcms_filemap` (`hostid`,`original`,`filename`) values ('1','tag.php','tag.php');
Insert Into `rqcms_filemap` (`hostid`,`original`,`filename`) values ('1','article.php','article.php');

DROP TABLE IF EXISTS `rqcms_link`;
CREATE TABLE `rqcms_link` (
  `lid` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `hostid` tinyint(3) NOT NULL,
  `displayorder` smallint(5) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(200) NOT NULL DEFAULT '',
  `note` varchar(200) NOT NULL DEFAULT '',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`lid`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `rqcms_link` (`lid`, `hostid`, `displayorder`, `name`, `url`, `note`, `visible`) VALUES (NULL, '1', '0', 'RQCMS', 'http://wwww.rqcms.com', 'RQCMS官方站点', '1');

DROP TABLE IF EXISTS `rqcms_host`;
CREATE TABLE `rqcms_host` (
  `hid` tinyint(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `host` varchar(50) NOT NULL,
  `gzipcompress` tinyint(1) NOT NULL,
  `theme` varchar(15) NOT NULL,
  `password` varchar(10) NOT NULL,
  `keywords` varchar(80) NOT NULL,
  `description` varchar(255) NOT NULL,
  `icp` varchar(30) NOT NULL,
  `close` tinyint(1) NOT NULL,
  `close_note` text NOT NULL,
  `list_shownum` tinyint(3) NOT NULL,
  `article_order` varchar(10) NOT NULL,
  `friend_url` varchar(10) NOT NULL,
  `title_limit` tinyint(3) NOT NULL,
  `tags_shownum` smallint(4) NOT NULL,
  `listcachenum` tinyint(3) NOT NULL,
  `related_shownum` tinyint(3) NOT NULL,
  `related_title_limit` tinyint(3) NOT NULL,
  `related_order` varchar(10) NOT NULL,
  `guest_comment` tinyint(1) NOT NULL,
  `audit_comment` tinyint(3) NOT NULL,
  `comment_order` tinyint(3) NOT NULL,
  `article_comment_num` tinyint(3) NOT NULL,
  `comment_min_len` tinyint(3) NOT NULL,
  `comment_max_len` smallint(5) NOT NULL,
  `commentlist_num` tinyint(3) NOT NULL,
  `comment_post_space` smallint(5) NOT NULL,
  `allow_search_content` tinyint(1) NOT NULL,
  `search_post_space` smallint(5) NOT NULL,
  `search_keywords_min_len` tinyint(2) NOT NULL,
  `attach_save_dir` tinyint(1) NOT NULL,
  `attach_thumbs` tinyint(3) NOT NULL,
  `attach_display` tinyint(1) NOT NULL,
  `attach_thumbs_size` varchar(10) NOT NULL,
  `attachments_remote_open` tinyint(1) NOT NULL,
  `watermark` tinyint(1) NOT NULL,
  `watermark_size` varchar(15) NOT NULL,
  `watermark_pos` tinyint(1) NOT NULL,
  `watermark_trans` tinyint(3) NOT NULL,
  `watermark_padding` tinyint(3) NOT NULL,
  `server_timezone` varchar(3) NOT NULL,
  `time_article_format` varchar(50) NOT NULL,
  `time_comment_format` varchar(50) NOT NULL,
  `closereg` tinyint(1) NOT NULL,
  `censoruser` varchar(2000) NOT NULL,
  `wap_enable` tinyint(1) NOT NULL,
  `wap_article_pagenum` tinyint(3) NOT NULL,
  `wap_article_title_limit` tinyint(3) NOT NULL,
  `wap_tags_pagenum` tinyint(3) NOT NULL,
  `wap_comment_pagenum` tinyint(3) NOT NULL,
  `banip_enable` tinyint(1) NOT NULL,
  `ban_ip` text NOT NULL,
  `spam_enable` tinyint(1) NOT NULL,
  `spam_words` text NOT NULL,
  `spam_url_num` tinyint(3) NOT NULL,
  `js_enable` tinyint(3) NOT NULL,
  `js_cache_life` smallint(5) NOT NULL,
  `js_lock_url` varchar(1000) NOT NULL,
  `rss_enable` tinyint(1) NOT NULL,
  `rss_num` tinyint(3) NOT NULL,
  `rss_ttl` smallint(5) NOT NULL,
  `status` tinyint(1) NOT NULL default 0,
  PRIMARY KEY (`hid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
INSERT INTO `rqcms_host` (`hid`, `name`, `host`, `gzipcompress`, `theme`, `password`, `keywords`, `description`, `icp`, `close`, `close_note`, `list_shownum`, `article_order`, `title_limit`, `tags_shownum`, `related_shownum`, `related_title_limit`, `related_order`, `audit_comment`, `comment_order`, `article_comment_num`, `comment_min_len`, `comment_max_len`, `commentlist_num`, `comment_post_space`, `allow_search_content`, `search_post_space`, `search_keywords_min_len`, `attach_save_dir`, `attach_thumbs`, `attach_display`, `attach_thumbs_size`, `attachments_remote_open`, `watermark`, `watermark_size`, `watermark_pos`, `watermark_trans`, `watermark_padding`, `server_timezone`, `time_article_format`, `time_comment_format`, `closereg`, `censoruser`, `wap_enable`, `wap_article_pagenum`, `wap_article_title_limit`, `wap_tags_pagenum`, `wap_comment_pagenum`, `banip_enable`, `ban_ip`, `spam_enable`, `spam_words`, `spam_url_num`, `js_enable`, `js_cache_life`, `js_lock_url`, `rss_enable`, `rss_num`, `rss_ttl`,`status`,`listcachenum`,`friend_url`,`guest_comment`) VALUES (1, '默认站点', 'rq.cn', 0, 'default', '', 'CMS,RQCMS', '又一个RQCMS', '1234567890', 0, '服务器检修中,稍后开放', 10, 'articleid', 0, 10, 10, 0, 'dateline', 1, 0, 10, 10, 3000, 20, 10, 1, 10, 2, 2, 0, 2, '200x200', 1, 0, 150, 4, 10, 5, '8', 'Y-m-d', 'Y-m-d', 0, 'admin', 0, 10, 0, 100, 20, 0, '', 0, '', 0, 0, 3600, '', 1, 20, 3600,1,10,'aid','0');


DROP TABLE IF EXISTS `rqcms_tag`;
CREATE TABLE `rqcms_tag` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(20) NOT NULL DEFAULT '',
  `hostid` tinyint(3) NOT NULL,
  `articleid` mediumint(8) NOT NULL,
  PRIMARY KEY (`tid`),
  KEY `articleid` (`tag`,`articleid`,`hostid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `rqcms_tag` (`tag`,`hostid`,`articleid`) values ('rqcms',1,1);

DROP TABLE IF EXISTS `rqcms_user`;
CREATE TABLE `rqcms_user` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `hostid` tinyint(3) NOT NULL,
  `username` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `groupid` smallint(5) NOT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `qq` bigint(13) NOT NULL DEFAULT '0',
  `msn` varchar(50) NOT NULL DEFAULT '',
  `face` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL,
  `articles` mediumint(8) NOT NULL DEFAULT '0',
  `regdateline` int(10) NOT NULL,
  `regip` varchar(16) NOT NULL,
  `logincount` mediumint(9) NOT NULL,
  `loginip` varchar(15) NOT NULL,
  `logintime` int(11) NOT NULL,
  `useragent` varchar(200) NOT NULL,
  `lastpost` int(10) NOT NULL,
  `sessionid` varchar(30) DEFAULT NULL,
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`,`username`,`sessionid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `rqcms_plugin`;
CREATE TABLE `rqcms_plugin` (
	`pid` SMALLINT(5) NOT NULL AUTO_INCREMENT,
	`hostid` TINYINT(3) NOT NULL,
	`file` VARCHAR(50) NULL DEFAULT NULL,
	`name` VARCHAR(50) NOT NULL,
	`author` VARCHAR(50) NOT NULL,
	`version` VARCHAR(50) NOT NULL,
	`description` VARCHAR(255) NOT NULL,
	`url` VARCHAR(50) NULL,
	`active` TINYINT(1) NOT NULL,
	`config` TEXT NOT NULL,
	PRIMARY KEY (`pid`),
	INDEX `hostid` (`hostid`),
	INDEX `file` (`file`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `rqcms_var`;
CREATE TABLE `rqcms_var` (
  `vid` smallint(5) NOT NULL AUTO_INCREMENT,
  `hostid` tinyint(3) NOT NULL,
  `type` varchar(10) NOT NULL,
  `title` varchar(200) NOT NULL,
  `value` text NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`vid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
Insert Into `rqcms_var` (`hostid`,`type`,`title`,`value`,`visible`) values ('1','style','demo','这是一个测试变量','1');

DROP TABLE IF EXISTS `rqcms_log`;
CREATE TABLE `rqcms_log` (
  `lid` int(10) NOT NULL AUTO_INCREMENT,
  `user` varchar(12) NOT NULL,
  `dateline` int(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  `useragent` varchar(200) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`lid`),
  KEY `user` (`user`,`type`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;