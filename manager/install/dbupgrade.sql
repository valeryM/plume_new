DROP TABLE IF EXISTS `plume_rsslinks`;
CREATE TABLE IF NOT EXISTS `plume_rsslinks` (
  `resource_id` int(10) unsigned NOT NULL default '0',
  `rsslink_serial` varchar(32) NOT NULL default '',
  `rsslink_titlewebsite` varchar(250) NOT NULL default '',
  `rsslink_linkwebsite` varchar(250) NOT NULL default '',
  `feed_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`resource_id`),
  KEY `rsslink_serial` (`rsslink_serial`),
  KEY `feed_id` (`feed_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;