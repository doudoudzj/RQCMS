
DROP TABLE IF EXISTS `rqcms_host`;
CREATE TABLE `rqcms_host` (
  `hid` smallint(5) NOT NULL AUTO_INCREMENT COMMENT '站点id',
  `name` varchar(50) NOT NULL,
  `host` varchar(50) NOT NULL,
  `host2` varchar(100) NOT NULL,
  `gzipcompress` tinyint(1) NOT NULL,
  `theme` varchar(15) NOT NULL,
  `thememobile` varchar(15) NOT NULL,
  `themeweixin` varchar(15) NOT NULL,
  `keywords` varchar(80) NOT NULL,
  `description` varchar(255) NOT NULL,
  `icp` varchar(30) NOT NULL,
  `list_shownum` tinyint(3) NOT NULL,
  `friend_url` varchar(10) NOT NULL,
  `tags_shownum` smallint(4) NOT NULL,
  `listcachenum` tinyint(3) NOT NULL default 20,
  `related_shownum` tinyint(3) NOT NULL,
  `allow_search_content` tinyint(1) NOT NULL,
  `search_post_space` smallint(5) NOT NULL,
  `search_keywords_min_len` tinyint(2) NOT NULL,
  `search_field_allow` varchar(100) NOT NULL,
  `search_max_num` mediumint(8) NOT NULL default 0,
  `attach_save_dir` tinyint(1) NOT NULL,
  `attach_thumbs` tinyint(3) NOT NULL,
  `attach_display` tinyint(1) NOT NULL,
  `attach_thumbs_size` varchar(10) NOT NULL,
  `attachments_remote_open` tinyint(1) NOT NULL,
  `rss_enable` tinyint(1) NOT NULL,
  `rss_num` tinyint(3) NOT NULL,
  `rss_ttl` smallint(5) NOT NULL,
  `status` tinyint(1) NOT NULL default 0,
  `url_ext` varchar(6) NOT NULL,
  `time_format` varchar(20) NOT NULL default 'Y-m-d H:i',
  PRIMARY KEY (`hid`),
  KEY `host` (`host`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
INSERT INTO `rqcms_host` (`hid`, `name`, `host`, `gzipcompress`, `theme`, `keywords`, `description`, `icp`, `list_shownum`, `tags_shownum`, `related_shownum`,`allow_search_content`, `search_post_space`, `search_keywords_min_len`,   `attach_save_dir`, `attach_thumbs`, `attach_display`, `attach_thumbs_size`, `attachments_remote_open`, `rss_enable`, `rss_num`,`status`,`url_ext`,`search_field_allow`) VALUES 
(1, '默认站点', 'rq.cn', 0, 'default','CMS,RQCMS', '又一个RQCMS', '1234567890', 10, 10, 10, 0, 1, 2,    2, 0, 2, '200x200', 1, 'admin', 20,1,'php','tag,keywords,title,excerpt');

Insert Into `prefix_filemap` (`original`,`filename`) values ('index','index');
Insert Into `prefix_filemap` (`original`,`filename`) values ('admin','admin');
Insert Into `prefix_filemap` (`original`,`filename`) values ('attachment','attachment');
Insert Into `prefix_filemap` (`original`,`filename`) values ('category','category');
Insert Into `prefix_filemap` (`original`,`filename`) values ('rss','rss');
Insert Into `prefix_filemap` (`original`,`filename`) values ('search','search');
Insert Into `prefix_filemap` (`original`,`filename`) values ('tag','tag');
Insert Into `prefix_filemap` (`original`,`filename`) values ('article','article');