SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS `#__vp_projects` (
  `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(255),
  `thumb` varchar(24),
  `image` varchar(24),
  `catid` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY(`id`),
  INDEX `idx_itpvp_pcatid`(`catid`)
)
ENGINE=INNODB
CHARACTER SET utf8 
COLLATE utf8_general_ci ;

CREATE TABLE IF NOT EXISTS `#__vp_images` (
  `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `image` varchar(24) NOT NULL,
  `thumb` varchar(24) NOT NULL,
  `project_id` smallint(6) UNSIGNED NOT NULL,
  PRIMARY KEY(`id`),
  INDEX `idx_itpvp_pi_id`(`project_id`)
)
ENGINE=INNODB
CHARACTER SET utf8 
COLLATE utf8_general_ci ;

CREATE TABLE IF NOT EXISTS `#__vp_categories` (
  `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `alias` varchar(128) NOT NULL,
  `desc` text,
  `image` varchar(24),
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  `meta_title` varchar(80),
  `meta_keywords` varchar(255),
  `meta_desc` varchar(255),
  `meta_canonical` varchar(255),
  PRIMARY KEY(`id`),
  INDEX `idx_cat_alias`(`alias`)
)
ENGINE=INNODB
CHARACTER SET utf8 
COLLATE utf8_general_ci ;

SET FOREIGN_KEY_CHECKS=1;