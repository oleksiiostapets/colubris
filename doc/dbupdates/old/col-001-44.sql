CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `new_data` text,
  `changed_fields` text,
  `class` varchar(254) DEFAULT NULL,
  `rec_id` varchar(200) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_log_user1_idx` (`user_id`),
  CONSTRAINT `fk_log_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

