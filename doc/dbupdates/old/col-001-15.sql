ALTER TABLE `task` ADD COLUMN `created_dts` TIMESTAMP NULL  AFTER `assigned_id` , ADD COLUMN `updated_dts` TIMESTAMP NULL  AFTER `created_dts` ;
