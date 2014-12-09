/* Foreign Keys must be dropped in the target to ensure that requires changes can be done*/

ALTER TABLE `user` 
	DROP FOREIGN KEY `fk_user_client`  ;


/* Alter table in target */
ALTER TABLE `attach` 
	ADD COLUMN `deleted_by` int(11)   NOT NULL DEFAULT 0 after `deleted_id` ;

/* Alter table in target */
ALTER TABLE `client` 
	ADD COLUMN `deleted_by` int(11)   NOT NULL DEFAULT 0 after `deleted_id` , 
	ADD COLUMN `avatar_id` int(11)   NOT NULL DEFAULT 0 after `deleted_by` ;

/* Alter table in target */
ALTER TABLE `organisation` 
	ADD COLUMN `deleted_by` int(11)   NOT NULL DEFAULT 0 after `deleted_id` ;

/* Alter table in target */
ALTER TABLE `project` 
	ADD COLUMN `deleted_by` int(11)   NOT NULL DEFAULT 0 after `deleted_id` ;

/* Alter table in target */
ALTER TABLE `quote` 
	ADD COLUMN `deleted_by` int(11)   NOT NULL DEFAULT 0 after `show_time_to_client` ;

/* Alter table in target */
ALTER TABLE `reqcomment` 
	ADD COLUMN `deleted_by` int(11)   NOT NULL DEFAULT 0 after `deleted_id` ;

/* Alter table in target */
ALTER TABLE `requirement` 
	ADD COLUMN `deleted_by` int(11)   NOT NULL DEFAULT 0 after `deleted_id` ;

/* Create table in target */
CREATE TABLE `right`(
	`id` int(11) NOT NULL  auto_increment , 
	`user_id` int(11) NULL  , 
	`right` text COLLATE utf8_general_ci NULL  , 
	PRIMARY KEY (`id`) , 
	UNIQUE KEY `user_id`(`user_id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Alter table in target */
ALTER TABLE `task` 
	ADD COLUMN `deleted_by` int(11)   NOT NULL DEFAULT 0 after `deleted_id` ;

/* Alter table in target */
ALTER TABLE `task_time` 
	ADD COLUMN `is_deleted` tinyint(1)   NULL DEFAULT 0 after `remove_billing` ;

/* Alter table in target */
ALTER TABLE `taskcomment` 
	ADD COLUMN `deleted_by` int(11)   NOT NULL DEFAULT 0 after `deleted_id` ;

/* Alter table in target */
ALTER TABLE `user` 
	ADD COLUMN `avatar_id` int(11)   NULL after `chash` , 
	ADD COLUMN `deleted_by` int(11)   NOT NULL DEFAULT 0 after `avatar_id` , 
	CHANGE `lhash` `lhash` varchar(255)  COLLATE utf8_general_ci NULL after `deleted_by` , 
	CHANGE `lhash_exp` `lhash_exp` timestamp   NULL after `lhash` ; 

/* The foreign keys that were dropped are now re-created*/

ALTER TABLE `user` 
	ADD CONSTRAINT `fk_user_client` 
	FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION ;
