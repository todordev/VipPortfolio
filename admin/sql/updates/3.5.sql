SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE `#__vp_projects` CHANGE `url` `url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `#__vp_projects` CHANGE `thumb` `thumb` VARCHAR( 24 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `#__vp_projects` CHANGE `image` `image` VARCHAR( 24 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE `#__vp_categories` CHANGE `desc` `desc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `#__vp_categories` CHANGE `image` `image` VARCHAR( 24 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `#__vp_categories` CHANGE `meta_title` `meta_title` VARCHAR( 80 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `#__vp_categories` CHANGE `meta_keywords` `meta_keywords` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `#__vp_categories` CHANGE `meta_desc` `meta_desc` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `#__vp_categories` CHANGE `meta_canonical` `meta_canonical` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

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