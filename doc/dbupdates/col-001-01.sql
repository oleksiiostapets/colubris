CREATE TABLE IF NOT EXISTS `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `smbo_id` int(11) DEFAULT NULL,
  `is_archive` tinyint(4) DEFAULT '0',
  `total_sales` decimal(8,2) DEFAULT NULL,
  `day_credit` int(11) DEFAULT NULL,
  `mailed_dts` datetime DEFAULT NULL,
  `printed_dts` datetime DEFAULT NULL,
  `ebalance` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `is_admin` tinyint(4) DEFAULT '0',
  `is_manager` tinyint(4) DEFAULT '0',
  `is_developer` tinyint(4) DEFAULT '0',
  `hash` varchar(255) DEFAULT NULL,
  `weekly_target` int(11) DEFAULT NULL,
  `is_timereport` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_user_client` (`client_id`),
  CONSTRAINT `fk_user_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;
