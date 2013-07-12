ALTER TABLE `task` ADD COLUMN `assigned_id` INT(11) NULL  AFTER `project_id` , 
  ADD CONSTRAINT `fk_task_assigned1`
  FOREIGN KEY (`assigned_id` )
  REFERENCES `user` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_task_assigned1` (`assigned_id` ASC) ;
