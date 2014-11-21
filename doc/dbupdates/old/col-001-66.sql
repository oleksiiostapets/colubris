CREATE TABLE `pivotal_story` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) DEFAULT NULL,
  `pivo_project_id` int(11) DEFAULT NULL,
  `pivo_story_id` int(11) DEFAULT NULL,
  `updated_at` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

