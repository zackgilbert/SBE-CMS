--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` bigint(20) NOT NULL auto_increment,
  `table` varchar(100) NOT NULL default '',
  `table_id` bigint(20) NOT NULL default '0',
  `sitemap_id` bigint(20) default NULL,
  `user_id` bigint(20) default NULL,
  `name` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `comment` text NOT NULL,
  `rating` tinyint(2) default NULL,
  `ip` varchar(20) default NULL,
  `agent` varchar(255) default NULL,
  `approved_at` datetime default NULL,
  `moderated_at` datetime default NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;
