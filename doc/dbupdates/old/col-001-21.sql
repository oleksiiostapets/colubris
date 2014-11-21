ALTER TABLE `participant` DROP FOREIGN KEY `fk_participant_project1` ;
ALTER TABLE `participant` 
  ADD CONSTRAINT `fk_participant_project1`
  FOREIGN KEY (`project_id` )
  REFERENCES `project` (`id` )
  ON DELETE CASCADE
  ON UPDATE CASCADE;
