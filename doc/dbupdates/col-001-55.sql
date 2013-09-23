CREATE  TABLE `taskcomment_user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `taskcomment_id` INT(11) NULL ,
  `user_id` INT(11) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_taskcomment_user_taskcomment1` (`taskcomment_id` ASC) ,
  INDEX `fk_taskcomment_user_user1` (`user_id` ASC) ,
  CONSTRAINT `fk_taskcomment_user_taskcomment1`
    FOREIGN KEY (`taskcomment_id` )
    REFERENCES `taskcomment` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_taskcomment_user_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
