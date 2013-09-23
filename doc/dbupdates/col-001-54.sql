CREATE  TABLE `reqcomment_user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `reqcomment_id` INT(11) NULL ,
  `user_id` INT(11) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_reqcomment_user_reqcomment1` (`reqcomment_id` ASC) ,
  INDEX `fk_reqcomment_user_user1` (`user_id` ASC) ,
  CONSTRAINT `fk_reqcomment_user_reqcomment1`
    FOREIGN KEY (`reqcomment_id` )
    REFERENCES `reqcomment` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reqcomment_user_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
