ALTER TABLE `#__vp_projects` ENGINE = InnoDB;
ALTER TABLE `#__vp_images` ENGINE = InnoDB;
ALTER TABLE `#__vp_categories` ENGINE = InnoDB;

ALTER TABLE `#__vp_projects` CHANGE `catid` `catid` SMALLINT( 6 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__vp_projects` ADD INDEX `idx_itpvp_pcatid` ( `catid` );

ALTER TABLE `#__vp_categories` CHANGE `alias` `alias` VARCHAR( 128 ) NOT NULL DEFAULT '';

ALTER TABLE `#__vp_images` CHANGE `projects_id` `project_id` SMALLINT( 6 ) UNSIGNED NOT NULL;
ALTER TABLE `#__vp_images` ADD `thumb` VARCHAR( 32 ) NOT NULL AFTER `name`;
ALTER TABLE `#__vp_images` CHANGE `name` `image` VARCHAR( 24 ) NOT NULL; 
ALTER TABLE `#__vp_images` ADD INDEX `idx_itpvp_pi_id` ( `project_id` ); 