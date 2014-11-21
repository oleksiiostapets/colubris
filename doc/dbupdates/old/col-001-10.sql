ALTER TABLE `participant` CHANGE COLUMN `budget_id` `project_id` INT(11) NOT NULL  , 
  ADD CONSTRAINT `fk_participant_project1`
  FOREIGN KEY (`project_id` )
  REFERENCES `project` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_participant_project1` (`project_id` ASC) ;


