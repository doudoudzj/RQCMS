DROP TABLE IF EXISTS `prefix_article`;
CREATE TABLE `prefix_article` (
  `aid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cateid` smallint(5) unsigned NOT NULL COMMENT '分类id',
  `title` varchar(100) NOT NULL COMMENT '标题',
  `keywords` varchar(120) NOT NULL DEFAULT '' COMMENT '关键词',
  `tag` varchar(100) NOT NULL COMMENT 'tag',
  `url` varchar(255) NOT NULL UNIQUE COMMENT '友好网址',
  `thumb` varchar(100) NOT NULL DEFAULT '' COMMENT '缩略图地址',
  `source` varchar(20) NOT NULL DEFAULT '' COMMENT '出处',
  `excerpt` varchar(255) NOT NULL COMMENT '摘要',
  `search` varchar(1500) NOT NULL COMMENT '相关搜索词',
  `writer`  varchar(100) NOT NULL COMMENT '作者',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `modified` int(10) NOT NULL COMMENT '修改时间',
  `views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '访问量',
  `comments` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '评论的个数',
  `attachments` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '附件个数',
  `stick` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否置顶',
  PRIMARY KEY (`aid`),
  Index `cateid` (`cateid`),
  Index `views` (`views`),
  Index `stick` (`stick`),
  Index `dateline` (`dateline`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `prefix_attachment`;
CREATE TABLE `prefix_attachment` (
  `aid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '附件id',
  `articleid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章id',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `filename` varchar(100) NOT NULL DEFAULT '' COMMENT '文件名',
  `filetype` varchar(50) NOT NULL DEFAULT '' COMMENT '文件类型',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `downloads` mediumint(8) NOT NULL DEFAULT '0' COMMENT '下载量',
  `filepath` varchar(255) NOT NULL DEFAULT '' COMMENT '文件地址',
  `isimage` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否图片',
  `score` smallint(5) NOT NULL DEFAULT '0' COMMENT '查看积分',
  `modified` int(10) NOT NULL COMMENT '最后修改时间',
  `tag` smallint(5) NOT NULL DEFAULT '0' COMMENT '缩略图高',
  PRIMARY KEY (`aid`),
  INDEX `articleid` (`articleid`),
  INDEX `isimage` (`isimage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `prefix_category`;
CREATE TABLE `prefix_category` (
  `cid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '栏目id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '栏目名称',
  `url` char(60) NOT NULL UNIQUE DEFAULT '' COMMENT '栏目友好网址',
  `pid` smallint(5) NOT NULL DEFAULT '0' COMMENT '父级栏目id',
  `style` varchar(20) NOT NULL COMMENT '栏目模板风格',
  `keywords` varchar(100) NOT NULL DEFAULT '' COMMENT '栏目关键字',
  `description` varchar(300) NOT NULL DEFAULT '' COMMENT '栏目描述',
  `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可见',
  `displayorder` smallint(5) NOT NULL DEFAULT '0' COMMENT '显示次序',
  PRIMARY KEY (`cid`),
  INDEX `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `prefix_category` (`cid`,`name`, `pid`, `style`, `keywords`, `description`, `displayorder`,`url`) VALUES (NULL,'默认栏目', '0', '', '', '', '0','hello');

DROP TABLE IF EXISTS `prefix_comment`;
CREATE TABLE `prefix_comment` (
  `cid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `articleid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `userid` smallint(5) NOT NULL DEFAULT '0',
  `username` varchar(50) NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `content` mediumtext NOT NULL,
  `url` char(60) NOT NULL,
  `email`  char(60) NOT NULL,
  `ipaddress` varchar(16) NOT NULL DEFAULT '',
  `score` smallint(5) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `ban` tinyint(1) NOT NULL,
  PRIMARY KEY (`cid`),
  INDEX `articleid` (`articleid`),
  INDEX `dateline` (dateline),
  INDEX `ipaddress` (ipaddress)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `prefix_filemap`;
CREATE TABLE `prefix_filemap` (
  `original` varchar(15) UNIQUE NOT NULL,
  `filename` varchar(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `prefix_link`;
CREATE TABLE `prefix_link` (
  `lid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `displayorder` smallint(5) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(200) NOT NULL DEFAULT '',
  `note` varchar(200) NOT NULL DEFAULT '',
  `bak` varchar(200) NOT NULL DEFAULT '',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`lid`),
  INDEX `displayorder` (`displayorder`),
  INDEX `visible` (`visible`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `prefix_link` (`lid`, `displayorder`, `name`, `url`, `note`, `visible`) VALUES (NULL, '0', 'RQCMS', 'http://wwww.rqcms.com', 'RQCMS官方站点', '1');

DROP TABLE IF EXISTS `prefix_tag`;
CREATE TABLE `prefix_tag` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(20) UNIQUE NOT NULL,
  `aids` text NOT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `prefix_tag` (`tag`,`aids`) values ('rqcms','1');

DROP TABLE IF EXISTS `prefix_user`;
CREATE TABLE `prefix_user` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
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
   PRIMARY KEY (`uid`),
   Index `sessionid` (`sessionid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `prefix_plugin`;
CREATE TABLE `prefix_plugin` (
	`pid` smallint(5) NOT NULL AUTO_INCREMENT,
	`file` VARCHAR(50) NULL DEFAULT NULL,
	`name` VARCHAR(50) NOT NULL,
	`author` VARCHAR(50) NOT NULL,
	`version` VARCHAR(50) NOT NULL,
	`description` VARCHAR(255) NOT NULL,
	`url` VARCHAR(50) NULL,
	`active` TINYINT(1) NOT NULL,
	`config` TEXT NOT NULL,
	PRIMARY KEY (`pid`),
	Index `file` (`file`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `prefix_var`;
CREATE TABLE `prefix_var` (
  `vid` smallint(5) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `value` text NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`vid`),
  Index `visible` (`visible`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
Insert Into `prefix_var` (`title`,`value`,`visible`) values ('demo','这是一个测试变量','1');

DROP TABLE IF EXISTS `prefix_login`;
CREATE TABLE `prefix_login` (
  `lid` int(10) NOT NULL AUTO_INCREMENT,
  `user` varchar(12) NOT NULL,
  `dateline` int(10) NOT NULL,
  `useragent` varchar(200) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`lid`),
  Index `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `prefix_redirect`;
CREATE TABLE `prefix_redirect` (
	`rid` MEDIUMINT(5) NOT NULL AUTO_INCREMENT,
	`old` VARCHAR(200) NULL DEFAULT NULL,
	`new` VARCHAR(200) NULL DEFAULT NULL,
	`status` TINYINT(4) NULL DEFAULT '1',
	PRIMARY KEY (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
Insert Into `prefix_redirect` (`old`,`new`,`status`) values ('rqcms','http://www.rqcms.com','302');

DROP TABLE IF EXISTS `prefix_search`;
CREATE TABLE `prefix_search` (
	`sid` INT(10) NOT NULL AUTO_INCREMENT,
	`keywords` VARCHAR(50) NULL DEFAULT '',
	`ip` VARCHAR(15) NULL DEFAULT '',
	`dateline` INT(10) NULL DEFAULT '0',
	PRIMARY KEY (`sid`),
	Index `keywords` (`keywords`),
	Index `ip` (`ip`),
	Index `dateline` (`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `prefix_content1`;
CREATE TABLE `prefix_content1` (
	`articleid` MEDIUMINT(8) NULL DEFAULT NULL,
	`content` MEDIUMTEXT NULL,
	UNIQUE INDEX `articleid` (`articleid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;