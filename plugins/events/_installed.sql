
CREATE TABLE IF NOT EXISTS `events` (
  `id` bigint(20) NOT NULL auto_increment,
  `sitemap_id` bigint(20) default '0',
  `name` varchar(255) NOT NULL default '',
  `repeats_every` varchar(100) default NULL,
  `event_category_id` int(3) default NULL,
  `subcategory` varchar(50) default NULL,
  `start_date` date NOT NULL default '0000-00-00',
  `end_date` date default NULL,
  `hours` varchar(255) default NULL,
  `location` text,
  `price` varchar(50) default NULL,
  `age` varchar(10) default NULL,
  `contact` varchar(255) default NULL,
  `description` text,
  `ends_on` date default NULL,
  `created_by` enum('user','staff') NOT NULL default 'user',
  `status` enum('public','private','registered_only') NOT NULL default 'public',
  `comment_status` enum('open','closed','registered_only') NOT NULL default 'open',
  `comment_count` int(11) NOT NULL default '0',
  `thumb` varchar(100) NOT NULL,
  `_dates` text NOT NULL,
  `published_at` datetime default NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `event_categories` (
  `id` int(3) NOT NULL auto_increment,
  `sitemap_id` bigint(20) NOT NULL default '0',
  `url` varchar(100) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Choir-Classical', 'Choir/Classical', '2009-08-12 14:04:31');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Comedy', 'Comedy', '2009-08-12 14:04:42');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Exhibits-Art-Galleries-Spaces', 'Exhibits - Art Galleries/Spaces', '2009-08-12 14:04:53');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Exhibits-Museums', 'Exhibits - Museums', '2009-08-12 14:05:04');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Exhibits-Receptions-Shows', 'Exhibits - Receptions/Shows', '2009-08-12 14:05:18');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Dance-Event', 'Dance Event', '2009-08-12 14:05:23');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Dance-Participation', 'Dance Participation', '2009-08-12 14:05:30');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Events-Festivals', 'Events/Festivals', '2009-08-12 14:05:38');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Farmers-Markets', 'Farmers Markets', '2009-08-12 14:05:46');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Kids', 'Kids', '2009-08-12 14:05:52');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Music-Concerts', 'Music/Concerts', '2009-08-12 14:06:28');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Nature-Parks', 'Nature/Parks', '2009-08-12 14:06:00');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Sports-Recreation-Hobbies', 'Sports/Recreation/Hobbies', '2009-08-12 14:06:07');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Theater-Audition', 'Theater Audition', '2009-08-12 14:06:12');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Theater-Musicals', 'Theater/Musicals', '2009-08-12 14:06:19');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Word-Lectures-Classes', 'Word - Lectures/Classes', '2009-08-12 14:06:28');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Word-Readings-Signings', 'Word - Readings/Signings', '2009-08-12 14:06:36');
INSERT INTO `event_categories` (`sitemap_id`, `url`, `name`, `created_at`) VALUES(0, 'Word-Workshops-Clubs', 'Word - Workshops/Clubs', '2009-08-12 14:06:47');

CREATE TABLE IF NOT EXISTS `event_venues` (
  `id` bigint(20) NOT NULL auto_increment,
  `sitemap_id` bigint(20) NOT NULL default '0',
  `url` varchar(100) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `address` varchar(255) default NULL,
  `phone` varchar(15) default NULL,
  `website_url` varchar(255) default NULL,
  `glatlng` varchar(50) default NULL,
  `thumb` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime default NULL,
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
