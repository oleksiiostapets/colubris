CREATE TABLE `taskcomment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `text` text,
  `user_id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT '0',
  `created_dts` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_taskcomment_task1` (`task_id`),
  KEY `fk_taskcomment_user1` (`user_id`),
  CONSTRAINT `fk_taskcomment_task1` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_taskcomment_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

