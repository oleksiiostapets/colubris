CREATE  TABLE `rate` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `from` INT(11) NULL ,
  `to` INT(11) NULL ,
  `organisation_id` INT(11) NOT NULL,
  `value` FLOAT(8,2) NULL ,
  PRIMARY KEY (`id`) );
