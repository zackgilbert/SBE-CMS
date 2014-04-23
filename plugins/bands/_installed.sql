CREATE TABLE IF NOT EXISTS `bands` (
  `id` bigint(20) NOT NULL auto_increment,
  `sitemap_id` bigint(20) NOT NULL,
  `url` varchar(100) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `tagline` varchar(100) defaullt NULL,
  `biography` varchar(255) default NULL,
  `website_url` varchar(255) default NULL,
  `thumb` varchar(100) NOT NULL,
  `published_at` datetime default NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;