ALTER TABLE `quote` DROP FOREIGN KEY `fk_quote_project1` ;
ALTER TABLE `quote` 
  ADD CONSTRAINT `fk_quote_project1`
  FOREIGN KEY (`project_id` )
  REFERENCES `project` (`id` )
  ON DELETE CASCADE
  ON UPDATE CASCADE;
