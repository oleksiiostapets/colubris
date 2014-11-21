ALTER TABLE `reqcomment` ADD COLUMN `user_id` INT(11) NOT NULL  , 
  ADD CONSTRAINT `fk_reqcomment_user1`
  FOREIGN KEY (`user_id` )
  REFERENCES `user` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_reqcomment_user1` (`user_id` ASC) ;


