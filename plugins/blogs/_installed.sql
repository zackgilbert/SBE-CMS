
--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` bigint(20) NOT NULL auto_increment,
  `user_id` bigint(20) default NULL,
  `url` varchar(100) NOT NULL default '',
  `title` varchar(255) default NULL,
  `body` text NOT NULL,
  `excerpt` varchar(255) default NULL,
  `status` enum('public','private','registered_only') NOT NULL default 'public',
  `comment_status` enum('open','closed','registered_only') NOT NULL default 'open',
  `comment_count` int(11) NOT NULL default '0',
  `blog_categories` text NOT NULL,
  `thumb` varchar(100) NOT NULL,
  `published_at` datetime default NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` bigint(20) NOT NULL auto_increment,
  `sitemap_id` bigint(20) default NULL,
  `url` varchar(100) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `count` bigint(20) NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

--
-- Table structure for table `blog_settings`
--

CREATE TABLE `blog_settings` (
  `id` bigint(20) NOT NULL auto_increment,
  `sitemap_id` bigint(20) default NULL,
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

--
-- Table structure for table `blog_sitemap`
--

CREATE TABLE `blog_sitemap` (
  `id` bigint(20) NOT NULL auto_increment,
  `sitemap_id` bigint(20) NOT NULL default '0',
  `blog_id` bigint(20) NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;
