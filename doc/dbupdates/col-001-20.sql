ALTER TABLE `quote` ADD COLUMN `duration` INT(11) NULL  AFTER `general` , ADD COLUMN `deadline` TIMESTAMP NULL  AFTER `duration` ;
