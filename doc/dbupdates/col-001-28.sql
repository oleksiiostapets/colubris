ALTER TABLE `task` ADD COLUMN `requester_id` INT(11) NULL DEFAULT NULL  AFTER `project_id` , 
  ADD CONSTRAINT `fk_task_requester1`
  FOREIGN KEY (`requester_id` )
  REFERENCES `user` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_task_requester1` (`requester_id` ASC) ;
