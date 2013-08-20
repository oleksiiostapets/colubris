/*[7:58:07 PM][400 ms]*/
ALTER TABLE `user` ADD COLUMN `is_system` TINYINT(1) DEFAULT 0 NOT NULL AFTER `client_id`;

/*[1:22:47 PM][244 ms]*/
CREATE TABLE `organisation`
(
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
  `desc` TEXT CHARSET utf8 COLLATE utf8_general_ci,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  KEY(`id`)
) ENGINE=INNODB CHARSET=utf8 COLLATE=utf8_general_ci;

/*[1:25:43 PM][391 ms]*/
ALTER TABLE `colubris`.`user` ADD COLUMN `organisation_id` INT(11) NOT NULL AFTER `is_deleted`;


/* create AgileTech agiletech  */
INSERT INTO `organisation` (`name`, `desc`) VALUES ('AgileTech','');

/* all users to agiletech  */
UPDATE USER SET `organisation_id` = (SELECT `id` FROM `organisation` WHERE `name` = 'AgileTech' LIMIT 0,1) WHERE is_system <> 1;