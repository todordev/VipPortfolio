CREATE TABLE IF NOT EXISTS `#__vp_images` (
  `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(24) NOT NULL,
  `projects_id` smallint(6) UNSIGNED NOT NULL,
  PRIMARY KEY(`id`)
)
ENGINE=MYISAM
ROW_FORMAT=default
CHARACTER SET utf8 
COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__vp_categories` (
  `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `alias` varchar(128) DEFAULT ' ',
  `desc` text,
  `image` varchar(24),
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  `meta_title` varchar(80) DEFAULT ' ',
  `meta_keywords` varchar(255) DEFAULT ' ',
  `meta_desc` varchar(255) DEFAULT ' ',
  `meta_canonical` varchar(255) DEFAULT ' ',
  PRIMARY KEY(`id`),
  INDEX `idx_cat_alias`(`alias`)
)
ENGINE=MYISAM
ROW_FORMAT=default
CHARACTER SET utf8 
COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__vp_projects` (
  `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(255),
  `thumb` varchar(24),
  `image` varchar(24),
  `catid` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY(`id`)
)
ENGINE=MYISAM
ROW_FORMAT=default
CHARACTER SET utf8 
COLLATE utf8_general_ci;