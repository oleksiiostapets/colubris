/*[6:59:35 PM][631 ms]*/
ALTER TABLE `colubris`.`requirement` ADD COLUMN `is_included` TINYINT(1) DEFAULT 1 NOT NULL AFTER `file_id`;