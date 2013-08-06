CREATE  TABLE `task_time` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `task_id` INT(11) NULL ,
  `user_id` INT(11) NULL ,
  `spent_time` DECIMAL(8,2) NULL ,
  `comment` TEXT NULL ,
  `created_dts` TIMESTAMP NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_task_time_task1` (`task_id` ASC) ,
  INDEX `fk_task_time_user1` (`user_id` ASC) ,
  CONSTRAINT `fk_task_time_task1`
    FOREIGN KEY (`task_id` )
    REFERENCES `task` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_task_time_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE);
    
ALTER TABLE `task` DROP COLUMN `spent_time` ;

