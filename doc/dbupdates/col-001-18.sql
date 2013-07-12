ALTER TABLE `attach` ADD COLUMN `task_id` INT(11) NULL  AFTER `updated_dts` , 
  ADD CONSTRAINT `fk_attach_task1`
  FOREIGN KEY (`task_id` )
  REFERENCES `col`.`task` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_attach_task1` (`task_id` ASC) ;
