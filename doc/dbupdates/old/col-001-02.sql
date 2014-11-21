CREATE TABLE IF NOT EXISTS `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `descr` text,
  `client_id` int(11) DEFAULT NULL,
  `demo_url` varchar(255) DEFAULT NULL,
  `prod_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_project_client1` (`client_id`),
  CONSTRAINT `fk_project_client1` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `quote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `issued` date DEFAULT NULL,
  `html` text,
  `attachment_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_quote_project1` (`project_id`),
  CONSTRAINT `fk_quote_project1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `budget` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `amount_paid` decimal(8,2) DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  `priority` varchar(45) DEFAULT NULL,
  `mandays` decimal(8,2) DEFAULT NULL,
  `manhours` decimal(8,2) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `success_criteria` int(11) DEFAULT NULL,
  `closed` tinyint(4) DEFAULT '0',
  `accepted` tinyint(4) DEFAULT '0',
  `is_monthly` tinyint(4) DEFAULT '0',
  `quote_id` int(11) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `is_moreinfo_needed` tinyint(4) DEFAULT '0',
  `is_delaying` tinyint(4) DEFAULT '0',
  `is_overtime` tinyint(4) DEFAULT '0',
  `expenses` decimal(8,2) DEFAULT NULL,
  `expenses_descr` text,
  `currency` varchar(45) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `total_pct` int(11) DEFAULT NULL,
  `timeline_html` text,
  PRIMARY KEY (`id`),
  KEY `fk_budget_project1` (`project_id`),
  KEY `fk_budget_quote1` (`quote_id`),
  CONSTRAINT `fk_budget_project1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_budget_quote1` FOREIGN KEY (`quote_id`) REFERENCES `quote` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;

