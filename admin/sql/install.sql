SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS `#__vp_categories` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `alias` varchar(128) NOT NULL,
  `desc` text NOT NULL,
  `image` varchar(24) NOT NULL DEFAULT '',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ordering` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `meta_title` varchar(80) NOT NULL DEFAULT '',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '',
  `meta_desc` varchar(255) NOT NULL DEFAULT '',
  `meta_canonical` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_cat_alias` (`alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vp_images` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(24) NOT NULL,
  `thumb` varchar(24) NOT NULL,
  `project_id` smallint(6) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itpvp_pi_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vp_pages` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` bigint(20) NOT NULL DEFAULT '0',
  `title` varchar(512) NOT NULL,
  `page_url` varchar(255) NOT NULL DEFAULT '',
  `pic` varchar(255) NOT NULL DEFAULT '',
  `pic_square` varchar(255) NOT NULL DEFAULT '',
  `fans` int(11) NOT NULL DEFAULT '0',
  `type` varchar(32) NOT NULL DEFAULT '',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_id` (`page_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Facebook pages';

CREATE TABLE IF NOT EXISTS `#__vp_projects` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `thumb` varchar(24) NOT NULL DEFAULT '',
  `image` varchar(24) NOT NULL DEFAULT '',
  `catid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ordering` tinyint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_itpvp_pcatid` (`catid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vp_tabs` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `app_id` bigint(20) NOT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `page_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itpvp_page_id` (`page_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
  
SET FOREIGN_KEY_CHECKS=1;