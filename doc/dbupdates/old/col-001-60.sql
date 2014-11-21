ALTER TABLE `quote` ADD COLUMN `is_archived` TINYINT(4) NOT NULL DEFAULT '0'  AFTER `expires_dts` ;
