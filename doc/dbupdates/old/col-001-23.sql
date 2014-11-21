ALTER TABLE `quote` ADD COLUMN `rate` FLOAT(6,2) NULL  AFTER `deadline` , ADD COLUMN `currency` VARCHAR(32) NULL  AFTER `rate` ;
