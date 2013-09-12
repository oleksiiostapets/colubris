/*[12:50:54 PM][514 ms]*/
ALTER TABLE `requirement` ADD COLUMN `is_deleted` TINYINT(1) DEFAULT 0 NOT NULL AFTER `is_included`;
ALTER TABLE `attach` ADD COLUMN `is_deleted` TINYINT(1) DEFAULT 0 NOT NULL AFTER `task_id`;
ALTER TABLE `taskcomment` ADD COLUMN `is_deleted` TINYINT(1) DEFAULT 0 NOT NULL AFTER `created_dts`;