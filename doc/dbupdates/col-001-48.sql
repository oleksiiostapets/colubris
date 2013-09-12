/*[12:50:54 PM][514 ms]*/
ALTER TABLE `colubris`.`requirement` ADD COLUMN `is_deleted` TINYINT(1) DEFAULT 0 NOT NULL AFTER `is_included`;