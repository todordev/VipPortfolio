CREATE TABLE IF NOT EXISTS `#__vp_categories` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `alias` varchar(128) NOT NULL,
  `desc` text,
  `image` varchar(24) DEFAULT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ordering` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `meta_title` varchar(80) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(255) DEFAULT NULL,
  `meta_canonical` varchar(255) DEFAULT NULL,
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

CREATE TABLE IF NOT EXISTS `#__vp_projects` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `thumb` varchar(24) DEFAULT NULL,
  `image` varchar(24) DEFAULT NULL,
  `catid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ordering` tinyint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_itpvp_pcatid` (`catid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
