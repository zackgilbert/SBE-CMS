--
-- Table structure for table `galleries`
--

CREATE TABLE IF NOT EXISTS `galleries` (
  `id` bigint(20) NOT NULL auto_increment,
  `sitemap_id` bigint(20) NOT NULL,
  `user_id` bigint(20) default NULL,
  `url` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `num_photos` int(11) NOT NULL default '0',
  `comment_status` enum('open','closed','registered_only') NOT NULL default 'open',
  `thumb` varchar(100) NOT NULL,
  `published_at` datetime default NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_photos`
--

CREATE TABLE IF NOT EXISTS `gallery_photos` (
  `id` bigint(20) NOT NULL auto_increment,
  `gallery_id` bigint(20) NOT NULL,
  `order` int(11) default NULL,
  `times_viewed` bigint(20) NOT NULL default '0',
  `comment_count` bigint(20) NOT NULL default '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;
