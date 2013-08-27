-- MySQL dump 10.13  Distrib 5.5.24, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: alfdemo
-- ------------------------------------------------------
-- Server version	5.5.24-0ubuntu0.12.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `attach`
--

DROP TABLE IF EXISTS `attach`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attach` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) DEFAULT NULL,
  `description` text,
  `created_dts` timestamp NULL DEFAULT NULL,
  `updated_dts` timestamp NULL DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_attach_task1` (`task_id`),
  CONSTRAINT `fk_attach_task1` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attach`
--

LOCK TABLES `attach` WRITE;
/*!40000 ALTER TABLE `attach` DISABLE KEYS */;
INSERT INTO `attach` VALUES (2,17,'','2013-07-31 12:44:06','2013-07-31 12:44:06',4),(3,18,'','2013-08-02 15:13:16','2013-08-02 15:13:16',14),(4,19,'','2013-08-02 15:15:27','2013-08-02 15:15:27',14),(5,20,'','2013-08-07 07:06:23','2013-08-07 07:06:23',28),(6,21,'','2013-08-07 07:20:46','2013-08-07 07:20:46',30),(7,22,'','2013-08-07 11:07:58','2013-08-07 11:07:58',34),(8,23,'','2013-08-07 14:39:26','2013-08-07 14:39:25',36),(9,24,'','2013-08-08 08:47:20','2013-08-08 08:47:19',46),(10,25,'','2013-08-08 12:04:24','2013-08-08 12:04:24',48),(11,26,'ДО','2013-08-09 09:29:33','2013-08-09 09:30:16',53),(12,27,'ПОСЛЕ','2013-08-09 09:29:45','2013-08-09 09:30:22',53);
/*!40000 ALTER TABLE `attach` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client` (
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
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  `organisation_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client`
--

LOCK TABLES `client` WRITE;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
INSERT INTO `client` VALUES (1,'First Client','alfost@list.ru',0,0,0.00,0,NULL,NULL,0.00,0,1),(2,'jpoint Susan Kelly','susan@jpoint.ie',0,0,0.00,0,NULL,NULL,0.00,0,1),(3,'Harry Gwynne','gwynne.h@hotmail.co.uk',0,0,0.00,0,NULL,NULL,0.00,0,1),(4,'Валерий Вавилов','valery.vavilov@gmail.com',0,0,0.00,0,NULL,NULL,0.00,0,1),(5,'Ray Rogers','ray.rogers@relate-software.com',0,0,0.00,0,NULL,NULL,0.00,0,1),(6,'Linked Finance','lf_temp@a@agiletech.ie',0,0,0.00,0,NULL,NULL,0.00,0,1),(7,'Aisling Software','',0,0,0.00,0,NULL,NULL,0.00,0,1);
/*!40000 ALTER TABLE `client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `filestore_file`
--

DROP TABLE IF EXISTS `filestore_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filestore_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filestore_type_id` int(11) NOT NULL DEFAULT '0',
  `filestore_volume_id` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `original_filename` varchar(255) DEFAULT NULL,
  `filesize` int(11) NOT NULL DEFAULT '0',
  `filenum` int(11) NOT NULL DEFAULT '0',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `filestore_file`
--

LOCK TABLES `filestore_file` WRITE;
/*!40000 ALTER TABLE `filestore_file` DISABLE KEYS */;
INSERT INTO `filestore_file` VALUES (1,2,1,'0/20130625141946__gr1.jpg','gr1.jpg',1926,0,''),(2,2,1,'0/20130625142005__gr2.jpg','gr2.jpg',2189,0,''),(3,2,1,'0/20130712094627__asc-0015.jpg','ASC_0015.JPG',852845,0,''),(4,2,1,'0/20130712095439__asc-0028.jpg','ASC_0028.JPG',867899,0,''),(5,1,1,'0/20130724085502__1111111.png','1111111.png',46321,0,''),(6,1,1,'0/20130724085811__1111111.png','1111111.png',36428,0,''),(7,1,1,'0/20130724085919__1111111.png','1111111.png',38399,0,''),(8,1,1,'0/20130724085944__1111111.png','1111111.png',38127,0,''),(9,1,1,'0/20130724090042__1111111.png','1111111.png',14874,0,''),(10,1,1,'0/20130724090141__1111111.png','1111111.png',201936,0,''),(11,1,1,'0/20130724090257__1111111.png','1111111.png',150826,0,''),(12,1,1,'0/20130724090339__1111111.png','1111111.png',230466,0,''),(13,1,1,'0/20130731085704__aaa-.png','aaa_.png',17943,0,''),(14,1,1,'0/20130731090101__aaa-.png','aaa_.png',14852,0,''),(15,2,1,'0/20130731090841__-.jpg','давай тех заданье.jpg',32419,0,''),(16,1,1,'0/20130731115444__relate-admin.png','Relate Admin.png',86271,0,''),(17,1,1,'0/20130731134405__screen-shot-2013-07-31-at-13.27.55.png','Screen Shot 2013-07-31 at 13.27.55.png',14844,0,''),(18,1,1,'0/20130802161315__infodos.png','infodos.png',77813,0,''),(19,1,1,'0/20130802161526__infod.png','infod.png',55029,0,''),(20,2,1,'0/20130807080621__ie-menu-and-logo.jpg','ie-menu-and-logo.jpg',463595,0,''),(21,2,1,'0/20130807082044__comments-textarea-and-button.jpg','comments-textarea-and-button.jpg',512459,0,''),(22,2,1,'0/20130807120756__infodos-menu.jpg','infodos-menu.jpg',617018,0,''),(23,2,1,'0/20130807153922__infodos-markers.jpg','infodos-markers.jpg',740841,0,''),(24,1,1,'0/20130808094718__nk-safari-symbol.png','nk-safari-symbol.png',138111,0,''),(25,1,1,'0/20130808130421__nk-safari-image.png','nk-safari-image.png',361938,0,''),(26,1,1,'0/20130809102932__infodos-admin-object1.png','infodos-admin-object1.png',32035,0,''),(27,1,1,'0/20130809102942__infodos-admin-object2.png','infodos-admin-object2.png',35451,0,''),(28,2,1,'0/20130814102914__-.jpg','давай тех заданье.jpg',32419,0,''),(29,2,1,'0/20130816151139_1_thumb-.jpg','thumb_давай тех заданье.jpg',5011,0,''),(30,2,1,'0/20130816151139__-.jpg','давай тех заданье.jpg',32419,0,'');
/*!40000 ALTER TABLE `filestore_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `filestore_image`
--

DROP TABLE IF EXISTS `filestore_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filestore_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `original_file_id` int(11) NOT NULL DEFAULT '0',
  `thumb_file_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `filestore_image`
--

LOCK TABLES `filestore_image` WRITE;
/*!40000 ALTER TABLE `filestore_image` DISABLE KEYS */;
INSERT INTO `filestore_image` VALUES (1,NULL,30,29);
/*!40000 ALTER TABLE `filestore_image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `filestore_type`
--

DROP TABLE IF EXISTS `filestore_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filestore_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(64) NOT NULL DEFAULT '',
  `extension` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `filestore_type`
--

LOCK TABLES `filestore_type` WRITE;
/*!40000 ALTER TABLE `filestore_type` DISABLE KEYS */;
INSERT INTO `filestore_type` VALUES (1,'png','image/png','png'),(2,'jpeg','image/jpeg','jpeg'),(3,'gif','image/gif','gif');
/*!40000 ALTER TABLE `filestore_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `filestore_volume`
--

DROP TABLE IF EXISTS `filestore_volume`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filestore_volume` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `dirname` varchar(255) NOT NULL DEFAULT '',
  `total_space` bigint(20) NOT NULL DEFAULT '0',
  `used_space` bigint(20) NOT NULL DEFAULT '0',
  `stored_files_cnt` int(11) NOT NULL DEFAULT '0',
  `enabled` enum('Y','N') DEFAULT 'Y',
  `last_filenum` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `filestore_volume`
--

LOCK TABLES `filestore_volume` WRITE;
/*!40000 ALTER TABLE `filestore_volume` DISABLE KEYS */;
INSERT INTO `filestore_volume` VALUES (1,'upload','upload',1000000000,0,30,'Y',NULL);
/*!40000 ALTER TABLE `filestore_volume` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organisation`
--

DROP TABLE IF EXISTS `organisation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organisation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `desc` text,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organisation`
--

LOCK TABLES `organisation` WRITE;
/*!40000 ALTER TABLE `organisation` DISABLE KEYS */;
INSERT INTO `organisation` VALUES (1,'AgileTech','',0);
/*!40000 ALTER TABLE `organisation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `participant`
--

DROP TABLE IF EXISTS `participant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_participant_user1` (`user_id`),
  KEY `fk_participant_project1` (`project_id`),
  CONSTRAINT `fk_participant_project1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_participant_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `participant`
--

LOCK TABLES `participant` WRITE;
/*!40000 ALTER TABLE `participant` DISABLE KEYS */;
INSERT INTO `participant` VALUES (1,1,3,'developer'),(4,2,7,'developer'),(5,2,6,'developer'),(6,2,5,'manager'),(7,3,10,'developer'),(9,3,6,'developer'),(10,1,6,'developer'),(11,1,1,'developer'),(12,4,5,'manager'),(13,4,6,'developer'),(14,4,10,'developer'),(15,5,5,'manager'),(16,5,6,'developer'),(17,5,9,'manager'),(18,5,8,'manager'),(19,5,10,'qa'),(20,6,5,'manager'),(21,6,6,'developer'),(22,6,8,'manager'),(23,6,7,'developer'),(24,6,9,'manager'),(25,6,10,'qa'),(26,7,5,'manager'),(27,7,6,'developer'),(28,7,7,'developer'),(29,7,8,'manager'),(30,7,9,'manager'),(31,7,10,'qa'),(32,7,10,'developer'),(33,8,7,'developer'),(34,8,9,'manager'),(35,8,12,'design'),(36,9,6,'developer'),(37,9,7,'developer'),(38,9,10,'developer'),(39,9,8,'manager'),(40,9,9,'manager'),(41,9,12,'design'),(42,3,8,'manager'),(43,3,9,'manager'),(46,11,7,'developer');
/*!40000 ALTER TABLE `participant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `descr` text,
  `client_id` int(11) DEFAULT NULL,
  `demo_url` varchar(255) DEFAULT NULL,
  `prod_url` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  `organisation_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_project_client1` (`client_id`),
  CONSTRAINT `fk_project_client1` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project`
--

LOCK TABLES `project` WRITE;
/*!40000 ALTER TABLE `project` DISABLE KEYS */;
INSERT INTO `project` VALUES (1,'Project1','',1,'','',0,1),(2,'My-Tools','',2,'','',0,1),(3,'3d Chamber','',3,'','',0,1),(4,'Relate Software','',5,'','',0,1),(5,'FEX','prototype http://dev.fex.net/\r\najax version http://fex.demo.agiletech.ie/public/\r\nsocket version http://fex2.agiletech.ie/public/\r\n',4,'','http://fex2.agiletech.ie/public/',0,1),(6,'NK','',4,'','http://nk.org.ua',0,1),(7,'INFO DOSIE','',4,'','',0,1),(8,'newseek.org','',4,'','http://newseek.org/',0,1),(9,'Colubris','',NULL,'','',0,1),(11,'Linked Finance','',6,'','',0,1),(12,'SortMyBooks Online','',7,'','http://sortmybooksonline.com/',0,1);
/*!40000 ALTER TABLE `project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quote`
--

DROP TABLE IF EXISTS `quote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `issued` date DEFAULT NULL,
  `html` text,
  `attachment_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'quotation_requested',
  `user_id` int(11) DEFAULT '0',
  `general` text,
  `duration` int(11) DEFAULT NULL,
  `deadline` timestamp NULL DEFAULT NULL,
  `rate` float(6,2) DEFAULT NULL,
  `currency` varchar(32) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  `organisation_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_quote_project1` (`project_id`),
  CONSTRAINT `fk_quote_project1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quote`
--

LOCK TABLES `quote` WRITE;
/*!40000 ALTER TABLE `quote` DISABLE KEYS */;
INSERT INTO `quote` VALUES (1,1,'Quotation1',NULL,NULL,NULL,NULL,'estimated',4,'',0,NULL,0.00,'EUR',0,1),(2,1,'Pack of tasks 12-07-2013',NULL,NULL,NULL,NULL,'finished',2,'',0,NULL,40.00,'GBP',0,1),(3,1,'das uber quotation',NULL,NULL,NULL,NULL,'estimate_needed',4,NULL,NULL,NULL,NULL,NULL,0,1),(4,2,'improvements',NULL,NULL,NULL,NULL,'estimated',5,'',NULL,NULL,NULL,NULL,0,1),(5,3,'next phase of 3rd Chamber',NULL,NULL,NULL,NULL,'estimated',5,'',25,'2013-09-07 23:00:00',45.00,'GBP',0,1),(6,4,'Website Issues',NULL,NULL,NULL,NULL,'estimated',5,'',NULL,NULL,NULL,NULL,0,1),(8,1,'q111',NULL,NULL,NULL,NULL,'quotation_requested',4,'aaa',0,NULL,0.00,'EUR',0,1),(9,1,'123123',NULL,NULL,NULL,NULL,'estimate_needed',4,'123dddd',NULL,NULL,NULL,NULL,0,1);
/*!40000 ALTER TABLE `quote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reqcomment`
--

DROP TABLE IF EXISTS `reqcomment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reqcomment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `requirement_id` int(11) NOT NULL,
  `text` text,
  `user_id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT '0',
  `created_dts` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reqcomment_requirement1` (`requirement_id`),
  KEY `fk_reqcomment_user1` (`user_id`),
  CONSTRAINT `fk_reqcomment_requirement1` FOREIGN KEY (`requirement_id`) REFERENCES `requirement` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reqcomment_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reqcomment`
--

LOCK TABLES `reqcomment` WRITE;
/*!40000 ALTER TABLE `reqcomment` DISABLE KEYS */;
INSERT INTO `reqcomment` VALUES (1,1,'Please make it quickly',2,0,NULL),(2,1,'I\'ll do',3,0,NULL),(3,20,'If I rememer correctly this is the jquery.placeholder.min.js bug',10,0,NULL),(4,37,'Facebook \'Like\' button?',10,0,NULL),(5,14,'Need to discuss about responsive design',10,0,NULL),(7,39,'Need to discuss',10,0,NULL),(9,15,'Need to setup Cron',10,0,NULL),(10,13,'seems like we need designer for this',6,0,NULL),(11,21,'Can you provide screenshot of this prompt?',6,0,NULL),(12,25,'If there is something (even if only one) we show what we need to show. If there is no any matching items - we show 5 items ordered by last vote date',6,0,NULL),(13,37,'Estimate time for FB like button',6,0,NULL),(14,38,'placeholder bug :(',6,0,NULL),(15,39,'\"\\n\" == < br > ,,,\r\n\' \' ==  & bnsp ;',6,0,NULL),(16,43,'you don\'t need to save image in this case. Image is saved already. Just save whole form.\r\n\"Save\" in this case meens \"save image on your computer\" :)',6,0,NULL),(18,46,'Twitter widget',10,0,NULL),(19,14,'pease remove iPhone / iPad optimization',1,0,NULL),(20,43,'We\'ll change templates to show thumbnail right away.',1,0,NULL),(24,54,'Change title to \'Not Enabled\'',10,NULL,NULL),(25,53,'Just pull last changes to live server. It works on the demo',10,NULL,NULL),(26,37,'Additional - Need to implement Page Title for the site',10,NULL,NULL),(27,20,'perhaps we can remove placeholder.min.js completely?',8,NULL,'2013-08-05 15:51:53'),(28,59,'Давай техзадаье!',1,30,'2013-08-16 14:11:47'),(29,13,'NB also including the very small \'share my motion\' page after you create a motion. E.g. that page could really do with icons for Facebook and Twitter, not just text saying share by Facebook & Twitter. ',13,NULL,'2013-08-16 18:16:46');
/*!40000 ALTER TABLE `reqcomment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requirement`
--

DROP TABLE IF EXISTS `requirement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requirement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `descr` text,
  `estimate` decimal(8,2) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `quote_id` int(11) DEFAULT '0',
  `file_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requirement`
--

LOCK TABLES `requirement` WRITE;
/*!40000 ALTER TABLE `requirement` DISABLE KEYS */;
INSERT INTO `requirement` VALUES (1,'Req1',NULL,'Make head',0.50,4,1,1),(2,'Req2',NULL,'Make body!',5.00,4,1,2),(3,'New design',NULL,'Implement this',20.00,2,2,3),(4,'New functionality',NULL,'and this',30.00,2,2,NULL),(5,'first',NULL,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec egestas id elit at rutrum. Aenean nunc enim, lobortis non semper eget, mollis vel massa. Proin malesuada sapien sit amet ultricies imperdiet. Sed mattis congue ipsum eu venenatis. Nunc congue dolor massa, sed facilisis tellus porta sit amet. Nam tempor libero accumsan sollicitudin vulputate. Curabitur eu urna eu turpis dapibus euismod tristique sit amet tellus. Duis metus libero, feugiat nec erat nec, ullamcorper sodales est. Quisque quam orci, ullamcorper mattis felis a, ultrices blandit enim. Aliquam porta, odio at auctor luctus, velit neque posuere massa, nec interdum ligula enim quis ante. Ut eleifend egestas sollicitudin. Vestibulum sed malesuada ligula, vitae vestibulum urna. Nulla in quam lacinia, fermentum elit sed, gravida diam. Etiam ullamcorper massa et neque lacinia, at luctus leo pretium. In porta felis sit amet ullamcorper placerat. Fusce eu faucibus erat.',NULL,4,3,NULL),(6,'no category name in url',NULL,'Routing of urls, so that the page name appears directly after the base_url. For example: existing: http://my-tools.ie/categories/woodworking-machinery/bandsaws/ required: my-tools.ie/bandsaws',4.00,5,4,NULL),(7,'removal of product id number from url',NULL,'removal of product id number from url, for example:\r\nexisting: http://my-tools.ie/product/295244-JET_VBS18MW_Bandsaw\r\nrequired: my-tools.ie/JETVBS18MWBandsaw',2.00,5,4,NULL),(8,'Add a further field to category upload for a short description',NULL,'Add a further field to category upload for a short description to be included. There is a field already on the database in the table cart_category called description that does not appear to be in use.',1.00,5,4,NULL),(9,'Create thumbnails for images',NULL,'Create thumbnails for images. As it stands each page loads the full images which are resized on the page. This causes a delay in the loading of the page, as the size of the page can be very large depending on the number of images on the page.',3.00,5,4,NULL),(10,'change the Username and Password',NULL,'Please can we change the Username and Password as lot\'s of people have access now and I\'d like to reign it in again. Please change to Username: Solon Password: Solon \r\n',0.50,5,5,NULL),(11,'When people search for a comment or click on a notification it should take them to the precise comment',NULL,'When people search for a comment or click on a notification it should take them to the precise comment. At the moment it just takes you to the general motion. Which is fine when there are only a few comments, but not so good when there are hundreds!!',3.00,5,5,NULL),(12,' I noticed if you try and make a comment when logged out it makes you log in. But then when you log in, it doesn\'t make the comment you wanted to make',NULL,' I noticed if you try and make a comment when logged out it makes you log in - fine. But then when you log in, it doesn\'t make the comment you wanted to make, it just takes you to the homepage. It should log you in and leave you where you are and either automatically post the comment, or leave you on the page that you were on so you can click \'post\' again. At the moment someone could type a huge comment, click post, log in screen comes up, they log in, then it takes them to the homepage and they lose their comment. Obviously that would be pretty annoying for them.',5.00,5,5,NULL),(13,' Tidy up About and other ancillary pages so they look more professional',NULL,' Tidy up About and other ancillary pages so they look more professional. Headings in particular still don\'t look as professional as they could.  ',8.00,5,5,NULL),(14,'optimised for IE, Mozilla, and Chrome. Also iPhone/iPad if possible',NULL,'Make sure optimised for IE, Mozilla, and Chrome. Also iPhone/iPad if possible. Let\'s discuss how big a a job this is.  ',8.00,5,5,NULL),(15,'There also needs to be a function where I can manage this in the admin area',NULL,'We have created a weekly summary but I haven\'t seen it go out yet. It needs to go out every Friday at 9am. There also needs to be a function where I can manage this in the admin area. E.g. change the time date it goes out and add people to the receiver list who may not be members of the site e.g. newspaper editors, MPs, etc. There also needs to be an unsubscribe link on the email itself at will automatically unsubscribe people.  ',8.00,5,5,NULL),(16,'Logo',NULL,' Logo (to be supplied by me) to be added into top right corner',0.50,5,5,NULL),(17,'your profile picture should take you to your profile page',NULL,'You should be able to click your profile picture to take you to your profile page, not just click in the drop down menu',0.50,5,5,NULL),(18,'Creating an account - I think we should force people to choose an Avatar',NULL,'Creating an account - I think we should force people to choose an Avatar if they don\'t want to upload a profile pick. Site looks bad with lots of question marks everywhere!! If people log in with facebook/Twitter can we automatically take their profile picture across?',3.00,5,5,NULL),(19,' Remember my details/keep me logged in',NULL,'Log in - Remember my details/keep me logged in - This needs to be added\r\n',2.00,5,5,NULL),(20,' When you start typing the \'email\' \'password\' text that is already in the box should automatically disappear. ',NULL,'Log in - When you start typing the \'email\' \'password\' text that is already in the box should automatically disappear. Users have complained that when they start typing the existing text saying \'email\' or \'password\' gets caught up with what they are typing as their email address or password. ',3.00,5,5,NULL),(21,' incorrect password prompt ',NULL,'Log in - incorrect password prompt - if people put in the wrong password which then directs you to recover your password if necessary ',1.00,5,5,NULL),(22,'Can we add a sign in with Twitter button?',NULL,'Log in - Can we add a sign in with Twitter button? And if people do that take their photo/profile info across? ',4.00,5,5,NULL),(23,'I sometimes get this error when trying to log in',NULL,'Log in - I sometimes get this error when trying to log in. I type my email in then press Tab so I can put my password in but sometimes before I get a chance to put my password in this comes up even though my account is obviously confirmed.  ',1.00,5,5,5),(24,' Reorder top 5 motions',NULL,'Index page - Reorder top 5 motions - Top left is the most voted, then below that for second most voted, then top right, then below that, then bottom right',2.00,5,5,NULL),(25,'Top 5 motions. 2',NULL,'Index page - Top 5 motions. If no motion has been voted today/7days/30days it stays on whatever the last motions voted were until someone does vote on something. I didn\'t like the idea of putting random motions there, but we do need something just in case people haven\'t voted on anything so it\'s never an empty space. ',3.00,5,5,NULL),(26,'shouldn\'t be able to vote on motions by clicking thumbs up/thumbs down on the homepage',NULL,'Index page - shouldn\'t be able to vote on motions by clicking thumbs up/thumbs down on the homepage - clicking there should just take you through to the motion page itself. I don\'t want people voting on motions without reading them!!',0.50,5,5,NULL),(27,' Please can we make the highlighting for Today/7 Days/30 Days/All Time under Top 5 motions clearer.',NULL,'Index page - Please can we make the highlighting for Today/7 Days/30 Days/All Time under Top 5 motions clearer. At the moment it\'s kind of hard to see which one it\'s on. \r\n',3.00,5,5,NULL),(28,'Most important issue facing the country. ',NULL,'Index page - Most important issue facing the country. Your vote/dot should disappear if you haven\'t voted today. At the moment it stays on whatever you voted for last but doesn\'t actually count as a vote. I think that might have been my idea to start with but it doesn\'t really work, so let\'s make it disappear totally after a day, and hopefully that should encourage people to vote more often too.   ',1.00,5,5,NULL),(29,' welcome to the site pop up - It also needs a little box saying \'Don\'t show this message again\' ',NULL,'Index page - welcome to the site pop up - It also needs a little box saying \'Don\'t show this message again\' ',0.50,5,5,NULL),(30,'Could we just break up the welcome thing a bit.',NULL,'Index page - Could we just break up the welcome thing a bit. Can we put in some paragraphs to make it easier to read. \'This in turn....\' on a new line. And then \'We think democracy means....\' also on a new line.  \r\n',0.20,5,5,6),(31,' Add in an button to inbed a youtube video in the description box',NULL,'Add Motion - Add in an button to inbed a youtube video in the description box which would then appear on the motion page',4.00,5,5,NULL),(32,'Change \'this house believes\' back to \'Motion title\' ',NULL,'Add Motion - Change \'this house believes\' back to \'Motion title\' ',0.10,5,5,NULL),(33,'Cross to cancel this should be clearer. ',NULL,'Add motion - Cross to cancel this should be clearer. As you can see in the top right of the screenshot below here it\'s very vague and one beta user said to me that there wasn\'t a way to cancel this screen at all until I pointed out to him the faint cross in the top right. Same with the cross on various other boxes throughout the site, eg. graph on the index page, recover password box, the \'please share your motion\' box when you create a new motion, etc.  ',1.00,5,5,7),(34,'If you try and add a motion when not logged in you get this error',NULL,'Add Motion - If you try and add a motion when not logged in you get this error',1.00,5,5,8),(35,' When you click share on a motion page, can we get the motion title to automatically come up in the box?',NULL,'Motion - When you click share on a motion page, can we get the motion title to automatically come up in the box? In the same way it posts the title automatically in your twitter feed when your twitter account is linked.\r\n',2.00,5,5,NULL),(36,'Change \'Abstain\' to \'Unsure\' throughout the site ',NULL,'Motion - Change \'Abstain\' to \'Unsure\' throughout the site ',1.00,5,5,NULL),(37,'Add in a \'like\' button to arguments and comments and comments on arguments. ',NULL,'Motion page - Add in a \'like\' button to arguments and comments and comments on arguments. ',1.00,5,5,NULL),(38,' You can add an argument with nothing in the box.',NULL,'Motion Page - You can add an argument with nothing in the box. If you try and add an argument or a comment with nothing in the box it should not post anything at all. E.g. when you comment with nothing in the box it comes up with this screenshot below. But I don\'t think it should let you comment at all if you haven\'t written anything. Maybe it could just flash the box red like if you don\'t fill in a required field on a form? ',1.00,5,5,9),(39,' spaces/paragraphs should appear in the text as you put them in on the Add Motion box',NULL,'Motion page - spaces/paragraphs should appear in the text as you put them in on the Add Motion box. At the moment you can have paragraphs in the \'Add Motion\' page but then when you create it it just turns it into a mass of text. Developer says that it will require to implement text editor.',0.50,5,5,NULL),(40,'This error below with the thumbs down on Support when it should be thumbs up ',NULL,'Motion Page - This error below with the thumbs down on Support when it should be thumbs up ',0.20,5,5,10),(41,'Checkboxes in Twitter\'s and Facebook\'s preferences do not work. ',NULL,'Settings - Checkboxes in Twitter\'s and Facebook\'s preferences do not work. Posts are still showing up. Obviously we need to sort this out. ',4.00,5,5,NULL),(42,'Even when I check the box for it I\'m not getting emails about new comments. ',NULL,'Settings - Even when I check the box for it I\'m not getting emails about new comments. We need to make sure we\'ve got the right template email in place for this too. This ties into the enhanced notification function further down this page. ',2.00,5,5,NULL),(43,'When you upload a profile picture and click save it tried to open the picture rather than saving it. ',NULL,'Settings - When you upload a profile picture and click save it tried to open the picture rather than saving it. I\'m not sure we need a \'save\' button there. Can\'t we just make it so when you update it with a photo it automatically saves it? ',1.00,5,5,NULL),(44,'Facebook/Twitter account linking/sharing functionality needs to be seamless.',NULL,'Settings/Sharing motions - Facebook/Twitter account linking/sharing functionality needs to be seamless. Let\'s discuss this but it\'s really buggy at the moment. And it needs to be ultra clean, easy, and smooth. It\'s one of the most important parts of the site and we really need it to be perfect and to give users real control over what is being posted because their is nothing more annoying than getting stuff posted all over facebook when you don\'t want it to. ',2.00,5,5,NULL),(45,'Need to sort out the Website/Twitter/Facebook headers. ',NULL,'Profile - Need to sort out the Website/Twitter/Facebook headers. It seems weird having some of those headers on some of the profiles and not on others. Perhaps we could change it so if people input a website it shows the website as www.example.com rather than just saying website. And then we could just have a small twitter and or Facebook icon if people have their Facebook or Twitter profiles linked. Also need something in settings to toggle displaying this or not. I really like the way Twitter have their edit profile box as you can see in the screenshot below. Just like very simple summary, perhaps we can do something like that. + Links to Facebook Twitter profiles.   ',4.00,5,5,11),(46,'Talking of the Twitter feed. It would be amazing if we could give people the option to embed their twitter feed by the side of their profile.',NULL,'Profile - Talking of the Twitter feed. It would be amazing if we could give people the option to embed their twitter feed by the side of their profile. Is that possible? Would Twitter even let us do that? I think Link\'d in do this so it must be possible. ',1.00,5,5,NULL),(47,'If I have 0 followers and I click on followers and get this error',NULL,'Profile - If I have 0 followers and I click on followers and get this error',0.50,5,5,12),(48,' What happens to messages when people send them from the contact us page?',NULL,'Contact us - What happens to messages when people send them from the contact us page? How do I receive them? I\'m just getting gmail for business set up at the moment, can we link that in? Not sure where any comments go at the moment? ',0.50,5,5,NULL),(49,'Need a more complex notifications tool plus space in the settings to manage it properly. ',NULL,'Notifications - Need a more complex notifications tool plus space in the settings to manage it properly. At the moment it seems that it currently give you a notification when someone comments on a motion that you\'ve commented on. I\'d like there to be various options on a notifications tab in settings (which obviously need to effect the notifications people receive). So can we let people choose between on site/email notifications for each of these. It also needs to give them a choice of what they want notifications on. \r\n1) Replies to arguments \r\n2) Comments on motions you have created\r\n3) Comments on motions you have commented on \r\n4) When a person follows you\r\n5) When your comments are \'liked\'  ',6.00,5,5,NULL),(50,' Various bits of the admin area aren\'t working properly - please can we check all functionality and repair where necessary. ',NULL,'Admin Area - Various bits of the admin area aren\'t working properly - please can we check all functionality and repair where necessary. E.g I have noticed that you cannot delete users, it comes up with an error. Same when you try and add Admin users. ',3.00,5,5,NULL),(51,'hello me',NULL,'hi',NULL,1,3,NULL),(52,'What’s New Dates',NULL,'As you can see on the website, whenever we add a new item to the “What’s New” section, all items update with the latest update. They should match the created date in the Admin display. ',3.00,5,6,13),(53,'Search Function',NULL,'The search option is not working anywhere in the Support login. ',0.50,5,6,NULL),(54,'Admin Console – User Requests v Non-Approved',NULL,'The Non Approved users is still not updating to show all users who have not been enabled. See example below. The Non Approved shows just one entry, ‘Tanyaige’\r\nBelow is a sample from the users screen and you can see the user has not been enabled',2.00,5,6,14),(55,'Q&A, Testing, bug fixing',NULL,'',10.00,9,5,NULL),(56,'r1',NULL,'sdg b',3.00,13,7,NULL),(57,'web',NULL,'awdfb',4.00,13,7,NULL),(58,'dbfv',NULL,'qbf',1.00,13,7,NULL),(59,'r1',NULL,'',NULL,4,8,NULL),(60,'r2',NULL,'',NULL,4,8,NULL);
/*!40000 ALTER TABLE `requirement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `priority` varchar(32) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `descr_original` text,
  `deviation` text,
  `estimate` decimal(8,2) DEFAULT NULL,
  `requirement_id` int(11) DEFAULT NULL,
  `budget_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `requester_id` int(11) DEFAULT NULL,
  `assigned_id` int(11) DEFAULT NULL,
  `created_dts` timestamp NULL DEFAULT NULL,
  `updated_dts` timestamp NULL DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  `organisation_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_task_budget1` (`budget_id`),
  KEY `fk_task_project1` (`project_id`),
  KEY `fk_task_screen1` (`requirement_id`),
  KEY `fk_task_assigned1` (`assigned_id`),
  KEY `fk_task_requester1` (`requester_id`),
  CONSTRAINT `fk_task_assigned1` FOREIGN KEY (`assigned_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_project1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_requester1` FOREIGN KEY (`requester_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_screen1` FOREIGN KEY (`requirement_id`) REFERENCES `requirement` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task`
--

LOCK TABLES `task` WRITE;
/*!40000 ALTER TABLE `task` DISABLE KEYS */;
INSERT INTO `task` VALUES (4,'перевод для формы логина','normal','accepted',NULL,'',NULL,0.00,NULL,NULL,6,10,7,'2013-07-31 12:43:34','2013-08-09 17:44:36',0,1),(6,'Поиск не ищет по запросу менее 3 символов','low','finished',NULL,'Поиск не ищет по запросу менее 3 символов. При этом желательно сообщить об этом пользователю вместо сообщения “Не найдено”.',NULL,0.00,NULL,NULL,7,10,10,'2013-08-02 14:39:48','2013-08-07 17:15:09',0,1),(7,'хтмл тэги в комментариях','normal','unstarted',NULL,'В комментариях пользователь может ввести хтмл тэги. Теги не обрабатываются, а отображаются в комментариях.',NULL,0.00,NULL,NULL,7,10,10,'2013-08-02 14:41:15','2013-08-07 11:02:00',0,1),(8,'Ссылка на источник','normal','unstarted',NULL,'При незаполнении ссылки на источник, ссылка ведет ни исходную (эту же) страницу. Следует либо заставлять автора статьи вписывать ссылку (обязательное поле) либо не отображать ее\r\nhttp://infostok.org/person/126-timoshenko-yulya-volodimirvna',NULL,0.00,NULL,NULL,7,10,10,'2013-08-02 14:42:53','2013-08-07 11:01:52',0,1),(9,'Неправильный формат ссылки','low','unstarted',NULL,'Неправильный формат ссылки http://infostok.org/person/46-\r\nhttp://awesomescreenshot.com/04e1kfq051\r\nhttp://infostok.org/article/81-seks-turizm-v-obedinennoy-oppozicii-ili-kak-nemyrya-sdal-sharite-i-katerinchuka',NULL,0.00,NULL,NULL,7,10,10,'2013-08-02 14:45:06','2013-08-07 11:01:46',0,1),(10,'Искаженные пропорции картинки','low','unstarted',NULL,'Искаженные пропорции картинки\r\nhttp://awesomescreenshot.com/0cd1kfr3fd\r\nhttp://infostok.org/company/147-mediaholding-v-gayduka',NULL,0.00,NULL,NULL,7,10,10,'2013-08-02 14:45:40','2013-08-07 11:01:39',0,1),(11,'Перепутали статью с персоной','low','unstarted',NULL,'Перепутали статью с персоной. Находится в разделе Казнокрады.\r\nhttp://infostok.org/person/236-nardep-labazyuk-originalno-otchitalsya-za-matpomosch',NULL,0.00,NULL,NULL,7,10,10,'2013-08-02 14:47:28','2013-08-07 11:01:32',0,1),(12,'Несоответствующее отображении ссылки','low','unstarted',NULL,'Несоответствующее отображении ссылки на https://mail.google.com/mail/h/m35p00lbmlmg/?&v=b&cs=wh&to=press.labazuk@ukr.net\r\nhttp://infostok.org/person/236-nardep-labazyuk-originalno-otchitalsya-za-matpomosch',NULL,0.00,NULL,NULL,7,10,10,'2013-08-02 14:48:11','2013-08-07 11:01:24',0,1),(13,'Опечатка в слове капча/каптча','normal','accepted',NULL,'Опечатка в слове.\r\nhttp://awesomescreenshot.com/0491kg5r5b',NULL,0.00,NULL,NULL,7,10,7,'2013-08-02 15:01:58','2013-08-09 17:45:23',0,1),(14,'Ошибка в консоли Хрома','normal','unstarted',NULL,'Не найдены файлы',NULL,0.00,NULL,NULL,7,10,10,'2013-08-02 15:12:57','2013-08-07 11:01:09',0,1),(15,'В комментариях не сохраняется форматирование текста (перенос строк)','normal','unstarted',NULL,'В комментариях не сохраняется форматирование текста (перенос строк)',NULL,0.00,NULL,NULL,7,10,10,'2013-08-02 15:15:42','2013-08-07 11:01:01',0,1),(16,'Assigned user','normal','accepted',NULL,'При добавлении таска, он фатоматически ассайнится на добавившего его юзера. Выбрать при добавлении нельзя. Можно изменить потом.',NULL,0.00,NULL,NULL,9,10,7,'2013-08-02 17:08:39','2013-08-07 19:21:55',0,1),(17,'Результат поиска ничего не сообщает пользователю','low','unstarted',NULL,'Если \"Ничего не найдено\", пользователю ничего не сообщается. ',NULL,0.00,NULL,NULL,6,10,6,'2013-08-05 11:14:53','2013-08-07 10:41:32',0,1),(18,'Стандартный favicon АТК','low','unstarted',NULL,'Стандартный favicon АТК',NULL,0.00,NULL,NULL,7,10,6,'2013-08-05 11:34:04','2013-08-07 11:00:42',0,1),(19,'Страница 404','normal','accepted',NULL,'Отсутствует страница 404 при переходе по неверной ссылке. Например http://nk.org.ua/ekonomika/fg5fyul1787fyu0-nbu-zaschitit-kopii-pasyportov-v-obmguilennikakh.\r\nОтвет сервера \"We are currently having some technical difficulties. Please retry later.\"\r\n\r\nПоправить meta title $Agile Toolkit$',NULL,0.00,NULL,NULL,6,10,6,'2013-08-05 13:00:42','2013-08-08 10:29:13',0,1),(20,'Карта не работает','high','accepted',NULL,'Не переходит по клику на регион',NULL,0.00,NULL,NULL,7,10,6,'2013-08-05 13:24:29','2013-08-07 14:33:30',0,1),(21,'Confirmation email','normal','accepted',NULL,'Поле \"От\" содержит \"test@emample.com\"',NULL,0.00,NULL,NULL,5,10,6,'2013-08-05 19:14:26','2013-08-08 18:12:54',0,1),(22,'Confirmation email link','normal','unstarted',NULL,'При переходе по линку в письме сайт выдает ошибку 403 You don\'t have permission to access / on this server.',NULL,0.00,NULL,NULL,5,10,6,'2013-08-05 19:17:31','2013-08-07 13:14:34',0,1),(23,'Забыли пароль','normal','unstarted',NULL,'При попытке восстановления пароля, после ввода имени пользователя, ничего не происходит. Просто редиректит на главную без какого-либо сообщения. При этом пришло письмо с новым паролем.\r\nВ письме с новым паролем линк такой http://fex2.agiletech.ie/ без /public/',NULL,0.00,NULL,NULL,5,10,6,'2013-08-05 19:20:54','2013-08-07 13:14:19',0,1),(24,'Login','normal','unstarted',NULL,'Что действует как логин? email или имя пользователя?\r\nПри попытке залогиниться, форма ругается на имя пользователя (stakantin), а при попытке ввести email (stakantin@mail.ru) - на пароль.\r\nПри этом пароль присланный в письме не подходит в обоих вариантах',NULL,0.00,NULL,NULL,5,10,6,'2013-08-05 19:29:35','2013-08-07 13:14:11',0,1),(25,'Wrong password','low','unstarted',NULL,'При введении неверного пароля, поле пишет \"Incorect login\"',NULL,0.00,NULL,NULL,5,10,6,'2013-08-05 19:34:06','2013-08-07 13:14:04',0,1),(26,'Forgot password','normal','unstarted',NULL,'Если введенное имя.мыло не найдено в базе, ошибка Application Error: ☺No records matching criteria (loadBy() заменить на tryLoadBy())',NULL,0.00,NULL,NULL,5,10,6,'2013-08-05 19:48:59','2013-08-07 13:13:59',0,1),(27,'Убрать колонку Assigned','low','accepted',NULL,'Убрать колонку Assigned для клиентов\r\n\r\nпока комменты не прикрутил, пишу тут... Мы договорились, что не надо это делать',NULL,0.00,NULL,NULL,9,10,7,'2013-08-05 20:17:15','2013-08-07 19:22:21',0,1),(28,'IE8. Меню и лого','normal','unstarted',NULL,'Стили не соответствуют (цвета)\r\nФон логотипа черный\r\nСм скриншот',NULL,0.00,NULL,NULL,6,10,6,'2013-08-07 07:06:07','2013-08-07 10:41:44',0,1),(29,'ИЕ8. Форма поиска. Отсутствует placeholder','normal','unstarted',NULL,'',NULL,0.00,NULL,NULL,6,10,6,'2013-08-07 07:10:39','2013-08-07 10:41:24',0,1),(30,'IE8. Комментарии. Кнопка и поле','normal','unstarted',NULL,'В форме добавления комментариев кнопка \"Добавить\" серого цвета. Поле текста комментария после добавления изменяет размер (уменьшается по ширине)\r\nСм скриншот',NULL,0.00,NULL,NULL,6,10,6,'2013-08-07 07:19:05','2013-08-07 10:41:09',0,1),(31,'ИЕ8. Опросы и результаты.','normal','accepted',NULL,'Не работает аккордеон. Показываются все опросы одновременно.\r\n\"Голосование открыто\" не показывается\r\nСсылка \"Проголосовать\" показывается как \"На сколько лет Москва моложе Киева?\"',NULL,0.00,NULL,NULL,6,10,6,'2013-08-07 07:31:17','2013-08-15 08:11:07',0,1),(32,'ИЕ 8. Поле ввода капчи.','normal','accepted',NULL,'Поле широкое (такое же как и поле ввода текста коммента). Находится под картинкой капчи',NULL,0.00,NULL,NULL,6,10,6,'2013-08-07 07:44:52','2013-08-08 10:29:47',0,1),(33,'ИЕ8. Пагинатор. Стили','normal','unstarted',NULL,'Дизайн не соответствует (цвета)',NULL,0.00,NULL,NULL,6,10,6,'2013-08-07 10:39:03','2013-08-07 10:39:03',0,1),(34,'IE8. Menu. Style','normal','unstarted',NULL,'Дизайн не соответствует в ИЕ',NULL,0.00,NULL,NULL,7,10,6,'2013-08-07 11:07:42','2013-08-07 11:07:42',0,1),(35,'ИЕ8. Форма \"Поделитесь с нами информацией\". Кнопка \"Отправить\".','normal','unstarted',NULL,'Дизайн не соответствует. Светло-серая кнопка. Текста на ней не видно',NULL,0.00,NULL,NULL,7,10,6,'2013-08-07 11:23:48','2013-08-07 11:23:48',0,1),(36,'ИЕ8. Сайдбар. Популярные статьи. Маркеры элементов списка','normal','accepted',NULL,'Перед элементами списка есть маркеры',NULL,0.00,NULL,NULL,7,10,6,'2013-08-07 11:35:20','2013-08-07 14:55:55',0,1),(38,'Бизнес. Текст не помещается','normal','unstarted',NULL,'Не помещается часть текста в блоке\r\nhttp://awesomescreenshot.com/07e1kz5a81\r\nТо же самое в \"Тушки\"',NULL,0.00,NULL,NULL,7,10,6,'2013-08-07 11:42:57','2013-08-07 11:42:57',0,1),(40,'ИЕ8. Форма отправки комментария не работает','normal','accepted',NULL,'',NULL,0.00,NULL,NULL,7,10,6,'2013-08-07 12:46:11','2013-08-07 14:33:02',0,1),(41,'IE8. Главная страница.','normal','accepted',NULL,'Выдает сообщение \"The File APIs are not fully supported in this browser\"',NULL,0.00,NULL,NULL,5,10,6,'2013-08-07 13:16:03','2013-08-08 18:12:03',0,1),(42,'Ошибка','normal','accepted',NULL,'При попытке изменить статус таска с finished на accepted в Дэшборде\r\nС другими тасками такого не происходит\r\nНа странице тасков нормально срабатывает\r\n\r\nТаск NK\r\nИЕ 8. Поле ввода капчи.',NULL,0.00,NULL,NULL,9,10,7,'2013-08-07 19:21:08','2013-08-08 10:29:59',0,1),(43,'Опера. Не обновляется картинка капчи при клике по ней и после добавления комментария','normal','accepted',NULL,'',NULL,0.00,NULL,NULL,6,10,6,'2013-08-08 07:14:52','2013-08-08 08:46:05',0,1),(44,'Mozilla. Опросы','normal','accepted',NULL,'Не раскрывается по клику',NULL,0.00,NULL,NULL,6,10,6,'2013-08-08 07:51:48','2013-08-08 12:15:30',0,1),(45,'Mozilla. Не обновляется картинка капчи при клике по ней и после добавления комментария','normal','accepted',NULL,'',NULL,0.00,NULL,NULL,6,10,6,'2013-08-08 08:03:38','2013-08-08 08:45:57',0,1),(46,'Safari. Symbol','normal','accepted',NULL,'',NULL,0.00,NULL,NULL,6,10,6,'2013-08-08 08:46:59','2013-08-08 10:29:31',0,1),(47,'Safari. Font','normal','unstarted',NULL,'Шрифт на главной страницу в заголовках не соответствует',NULL,0.00,NULL,NULL,6,10,6,'2013-08-08 08:50:06','2013-08-08 08:50:06',0,1),(48,'Safari. Image','normal','accepted',NULL,'Не обрезается/кадрируется картинка\r\nhttp://nk.org.ua/ukraina/51768-ukraine-otdali-dvenadtsatoe-mesto-v-reytinge-populyarnosti-sredi-puteshestvennikov',NULL,0.00,NULL,NULL,6,10,5,'2013-08-08 12:04:08','2013-08-08 12:04:08',0,1),(49,'Результаты поиска. Перевод','low','accepted',NULL,'Названия месяцев по-английски\r\nhttp://infostok.org/search/articles?q=%D0%B2%D0%B8%D0%B4',NULL,0.00,NULL,NULL,7,10,6,'2013-08-08 18:09:55','2013-08-09 06:23:53',0,1),(50,'Dashboard. Add time of last change of the task','normal','accepted',NULL,'',NULL,0.00,NULL,NULL,9,10,7,'2013-08-08 20:12:14','2013-08-09 17:35:33',0,1),(51,'Admin. Add image to person','normal','unstarted','bug','Картинки (к краткой аннотации)->добавить.\r\nApplication Error: Не возможно создать файл\r\nТо же самое к Аннотации',NULL,0.00,NULL,NULL,7,10,6,'2013-08-09 08:56:21','2013-08-09 08:57:55',0,1),(52,'Админка. Кнопка \"Убрать все категории\"','normal','accepted','bug','Кнопка \"Убрать все категории\" так же закрывает все регионы',NULL,0.00,NULL,NULL,7,10,5,'2013-08-09 09:18:55','2013-08-09 17:52:46',0,1),(53,'Админ-Сущности-Личности-Таги. Высота','normal','unstarted','bug','После удаления одно из тагов, высота тагов уменьшается',NULL,0.00,NULL,NULL,7,10,6,'2013-08-09 09:29:17','2013-08-09 09:29:17',0,1),(54,'Проверка на дублирование логина','normal','accepted','bug','Два пользователя Susan Kelly',NULL,0.00,NULL,NULL,9,10,7,'2013-08-12 10:24:31','2013-08-20 06:39:49',0,1),(55,'Ограничение видимости проекта','low','accepted','project','В Разделе Таски не должен появляться проект, который не утвержден клиентом (нестартован)',NULL,0.00,NULL,NULL,9,10,7,'2013-08-12 10:33:03','2013-08-12 14:46:29',0,1),(56,'социальные кнопки на сайте','normal','accepted','change request','Нужно поставить социальные кнопки (fb, vk, odnoklassniki) на страницы типа /person, /company, /article - либо под статью, либо под заголовок статьи.',NULL,2.00,NULL,NULL,7,10,7,'2013-08-12 12:43:58','2013-08-13 10:40:39',0,1),(57,'check links in emails - they are wrong','normal','accepted','bug','',NULL,0.00,NULL,NULL,9,10,7,'2013-08-12 20:05:31','2013-08-20 06:39:34',0,1),(58,'Make settings for emails','high','accepted','change request','',NULL,0.00,NULL,NULL,9,10,7,'2013-08-13 07:04:17','2013-08-20 06:39:42',0,1),(59,'на странице не должны создаваться контекстные ссылки на ту же страницу','normal','accepted','change request','',NULL,1.00,NULL,NULL,7,7,7,'2013-08-13 08:44:03','2013-08-13 08:44:03',0,1),(61,'Вместо fb-share лучше поставить лайки - они работают лучше. И надо также добавить кнопки под заголовок','normal','accepted','change request','',NULL,1.00,NULL,NULL,7,7,7,'2013-08-13 10:40:07','2013-08-13 10:40:29',0,1),(62,'В данный момент редирект со страниц с www на страницы без www - 302, нужно поставить 301','normal','accepted','change request','',NULL,0.00,NULL,NULL,6,7,7,'2013-08-13 12:02:56','2013-08-13 12:02:56',0,1),(63,'Автоматические контекстные ссылки','normal','accepted','change request','Автоматические контекстные ссылки. Нужно реализовать функционал контекстных ссылок на всех текстовых страницах. \r\nДля начала можно реализовать контекстные ссылки на персон и компании:\r\n1) чекать только 1е вхождение в текст\r\n2) менять местами имя и фамилию (  перестановка (Тимошенко Юлія и Юлія Тимошенко)\r\n3) чекать только инфинитив',NULL,0.00,NULL,NULL,7,7,7,'2013-08-13 12:04:13','2013-08-13 12:04:13',0,1),(65,'Research and update data for application','normal','accepted','support','[16:40:19] Peter O\'Mahony: One if the guys in the office made patmuscleclinic@hotmail.com go live without checking the \'is Paid\' box\r\n[16:41:36] Peter O\'Mahony: This has meant that no outbids applied, you can\'t make a bid and the loan is currently set up for 0 months.\r\n[16:42:29] Peter O\'Mahony: Can you bring it back in to the applications section or make the change?\r\n',NULL,0.00,NULL,NULL,11,7,7,'2013-08-13 14:32:55','2013-08-13 14:32:51',0,1),(66,'Put a „tested” function on status of a task after tester has checked and approved it.','normal','accepted','change request','добавить статус \"оттестировано\"',NULL,0.00,NULL,NULL,9,6,7,'2013-08-14 08:21:48','2013-08-14 09:57:45',0,1),(67,'Separate bugs on finished and not yet finished (so that a client do not see that bugs travel from tester to developer and back)','normal','accepted','change request','',NULL,0.00,NULL,NULL,9,5,7,'2013-08-14 12:57:08','2013-08-14 12:57:08',0,1),(68,'Report filter - make possibility to filter tasks without quote','normal','accepted','change request','',NULL,0.00,NULL,NULL,9,7,7,'2013-08-14 13:42:30','2013-08-14 15:04:36',0,1),(69,'Fix PDF printing','high','finished','support','There are new records in /srv/www/sortmybooksonline.com/logs/am3_error_log\r\nsince 2013-08-15 12:23:01.000000000 +0100.\r\n\r\nIt was renamed into /srv/www/sortmybooksonline.com/logs/am3_error_log.1376565841.\r\n\r\nShowing at most 50 lines of it:\r\n\r\n===========================================================\r\n\r\n[15-Aug-2013 12:23:10]  /srv/www/sortmybooksonline.com/page/<b>image.php</b>:53\r\n[Warning] mkdir() [function.mkdir]: Too many links\r\n\r\n===========================================================',NULL,0.20,NULL,NULL,12,NULL,8,'2013-08-15 13:29:55','2013-08-15 13:29:55',0,1),(70,'tasks -> time - > make checkbox \"should we bill client\"','normal','accepted','change request','',NULL,0.00,NULL,NULL,9,7,7,'2013-08-15 13:39:21','2013-08-16 07:28:22',0,1),(71,'soft delete','normal','accepted','change request','',NULL,0.00,NULL,NULL,9,7,7,'2013-08-15 13:55:02','2013-08-15 13:56:31',0,1),(72,'Make tabs for deleted objects','normal','accepted','change request','',NULL,0.00,NULL,NULL,9,7,7,'2013-08-15 14:17:01','2013-08-16 08:48:50',0,1),(73,'ссылка на автора статьи','normal','accepted','change request','в данный момент, если ссылка на автора статьи не прописана в админке, эта ссылка ведет на ту страницу, где она находится. Надо сделать так, чтобы в случае, если ссылка не указана, имя автора ссылкой не являлось.',NULL,0.00,NULL,NULL,7,7,7,'2013-08-16 07:47:05','2013-08-16 08:02:14',0,1),(74,'Add in # functionality ','normal','unstarted','change request','I\'ve had some problems with the \'The most important issue facing the country?\' for a while now. We\'ve discussed some of the problems before, but they include the need to change the list and resetting the votes, how changing your vote today related to historical data, the fact that people can\'t directly add things themselves, the fact that the list probably wouldn\'t change all that much anyway as people\'s most important issue doesn\'t tend to change from day to day, and so on. It als just feels generally a bit clumsy and I\'m not sure it can go to market in its current condition. \r\n\r\nAnyway, I\'ve been thinking about it a lot, and what it is trying to convey and I think I\'ve come up with a better way of presenting the information using #hashtags \r\n\r\nSo what this would require \r\n1) Replace The most important issue facing the country? on the index page with a new box - \'Trending\' \r\n2) When people create a motion add in an additional box (probably below \'description\' and above \'category\') asking them to add #hashtags with some example text e.g #Syria #NHS #HouseofCommons etc. \r\n3) Then the most frequently used #hashtags at any point in time would appear on the index page in the new \'Trending\' box  \r\n4) When people click on any of these #hashtags it takes them to a list of all the motions that have that #hashtag\r\n5) Replace The most important issue facing the country? on our weekly summary with the new \'Trending\' list with the strongest trends from that week   ',NULL,0.00,NULL,NULL,3,NULL,NULL,'2013-08-16 19:05:24','2013-08-16 19:05:24',0,1),(75,'projects -> tasks -> edit must be the same as tasks -> edit','normal','accepted','bug','',NULL,0.00,NULL,NULL,9,7,7,'2013-08-19 06:48:32','2013-08-19 06:48:32',0,1),(76,'разобраться с таском Fix PDF printing','normal','accepted','support','Fix PDF printing почему то пошел не в тот проект.  Попытаться разобраться почему и переместить в нужный проект (Sort My Book)',NULL,0.00,NULL,NULL,9,5,7,'2013-08-19 10:36:38','2013-08-20 06:39:25',0,1),(77,'test','normal','unstarted','change request','',NULL,0.00,NULL,NULL,12,NULL,NULL,'2013-08-19 11:31:14','2013-08-19 11:31:19',1,1),(78,'until a client has not approved an estimate, he cannot see reports. And developers do not see the project in Tasks.','normal','accepted','change request','until a client has not approved an estimate, he cannot see reports. And developers do not see the project in Tasks.',NULL,0.00,NULL,NULL,9,5,7,'2013-08-19 11:36:34','2013-08-19 12:49:42',0,1),(79,'Separate bugs on finished and not yet finished (so that a client do not see that bugs travel from tester to developer and back)','normal','accepted','change request','Separate bugs on finished and not yet finished (so that a client do not see that bugs travel from tester to developer and back)',NULL,0.00,NULL,NULL,9,5,7,'2013-08-19 11:37:41','2013-08-19 12:50:06',0,1),(80,'Make system ID – distinction for different development companies ','normal','accepted','project','Make system ID – distinction for different development companies ',NULL,0.00,NULL,NULL,9,5,6,'2013-08-20 11:59:10','2013-08-20 12:05:38',0,1);
/*!40000 ALTER TABLE `task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_time`
--

DROP TABLE IF EXISTS `task_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `spent_time` decimal(8,2) DEFAULT NULL,
  `comment` text,
  `date` date DEFAULT NULL,
  `remove_billing` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_task_time_task1` (`task_id`),
  KEY `fk_task_time_user1` (`user_id`),
  CONSTRAINT `fk_task_time_task1` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_task_time_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_time`
--

LOCK TABLES `task_time` WRITE;
/*!40000 ALTER TABLE `task_time` DISABLE KEYS */;
INSERT INTO `task_time` VALUES (1,36,6,0.50,'html typo','2013-08-07',0),(2,40,6,1.00,'Captcha didn\'t work in IE8','2013-08-07',0),(3,20,6,1.00,'после смены имени АПИ перестали работать захардкодженые джаваскриптовые функции.','2013-08-07',0),(4,19,6,0.50,'','2013-08-07',0),(5,32,6,0.50,'','2013-08-07',0),(6,6,6,0.10,'Для большей производительности и более релевантного поиска поисковый сервер Sphinx настроен так, что он игнорирует слова состоящие меньше чем из 4х букв. И поиск производит по целым словам а не по совпадению части слова. В этом и заключается смысл использования данного сервера.','2013-08-07',0),(7,42,7,0.50,'','2013-08-08',0),(8,4,7,0.50,'','2013-08-08',0),(9,50,7,0.20,'','2013-08-09',0),(10,13,7,0.20,'','2013-08-09',0),(11,56,7,2.00,'','2013-08-12',0),(12,54,7,0.50,'','2013-08-12',0),(13,58,7,1.50,'','2013-08-13',0),(14,59,7,1.00,'','2013-08-13',0),(16,61,7,1.00,'','2013-08-13',0),(17,62,7,0.50,'','2013-08-01',0),(18,63,7,4.00,'','2013-08-05',0),(19,57,7,1.00,'','2013-08-13',0),(20,65,7,0.50,'','2013-08-13',0),(21,66,7,1.00,'','2013-08-14',0),(22,67,7,4.00,'','2013-08-14',0),(23,68,7,2.00,'','2013-08-14',0),(24,31,6,0.50,'','2013-08-08',0),(25,69,8,0.20,'renamed reports folder in /srv/www/sortmybooksonline.com/tests','2013-08-15',0),(26,71,7,3.00,'finished for tables user, client, project, quote, task','2013-08-15',0),(27,70,7,1.00,'','2013-08-16',0),(28,73,7,0.50,'','2013-08-16',0),(29,72,7,3.00,'','2013-08-16',0),(30,75,7,1.00,'','2013-08-19',1),(31,80,6,4.00,'разработка функционала и его базовая имплементация','2013-08-16',0),(32,80,7,5.00,'','2013-08-20',0);
/*!40000 ALTER TABLE `task_time` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taskcomment`
--

DROP TABLE IF EXISTS `taskcomment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taskcomment`
--

LOCK TABLES `taskcomment` WRITE;
/*!40000 ALTER TABLE `taskcomment` DISABLE KEYS */;
INSERT INTO `taskcomment` VALUES (1,55,'Ограничение может быть только для таска, прикреплённого к квоте. У квоты есть статусы. У проекта статусов нет',7,NULL,'2013-08-12 14:46:19'),(2,56,'Я не создавал этот тикет. Мне его потестить или как?\r\nКостя',10,NULL,'2013-08-12 20:00:07'),(3,56,'мы его уже тестили. Вадима в скайпе просил. Закрывай просто',7,NULL,'2013-08-12 20:04:16'),(5,53,'она на самом деле должна быть такой маленькой :) Баг в том что изначально почему то большие. Я не вникал так как времени не было ',6,NULL,'2013-08-15 08:12:53'),(6,78,'Должен немного прояснить структуру и увидишь, что этот таск не нужен.\r\nЕсть проекты, к ним мы создаём квоты (в них реквайры с эстимейтами).\r\nПока квота незаапрувлена, её не видно в фильтре в тасках.\r\nПоэтому к этой квоте никто не сможет создать таски и соответственно,\r\nникто не увидит никаких данных по этой квоте в репортах.\r\nНо проект нельзя закрывать. Пример с бывшей работы. Приходит клиент,\r\nначинаем говорить о новом проекте. Создание квоты в процессе, но есть\r\nкакая-то хитрая фишка, которая нуждается в девелоперском ресёрче. Есть\r\nдаже договорённость с клиентом, что он оплатит ресёрч, даже если\r\nрезультат будет не очень хорош (ну просто хочет он чего-то\r\nневероятного). Соответственно создаётся таск, девелопер трекается туда,\r\nно таск вне квоты. Итог - созданный проект обязан быть видим.',7,NULL,'2013-08-19 12:49:28'),(7,79,'Любое изменение в таске, его аттачах и комментах\r\nрассылает письма реквестеру и девелоперу этого таска. Но есть исключение\r\n- если изменения касаются тасков с статусом \"started\" или \"finished\" -\r\nто письма клиенту не отправляются. А вот когда тестер или кто другой\r\nпоставит статус \"finished\" или \"accepted\", то уведомление настигнет клиента.\r\nНо в самом разделе тасков убирать возможность клиенту увидеть эти\r\nнезавершённые таски нельзя. Представьте, клиент создал баг, причём баг\r\nсерьёзный, дев начинает работу, меняет статус и клиент не видит больше\r\nэтого таска. Реакция клиента? Или создаст новый или будет нас ругать.',7,NULL,'2013-08-19 12:49:59');
/*!40000 ALTER TABLE `taskcomment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `is_admin` tinyint(4) DEFAULT '0',
  `is_manager` tinyint(4) DEFAULT '0',
  `is_developer` tinyint(4) DEFAULT '0',
  `hash` varchar(255) DEFAULT NULL,
  `weekly_target` int(11) DEFAULT NULL,
  `is_timereport` tinyint(4) DEFAULT '1',
  `mail_task_changes` tinyint(4) DEFAULT '1',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  `organisation_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_client` (`client_id`),
  CONSTRAINT `fk_user_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Admin','admin','202cb962ac59075b964b07152d234b70',NULL,0,1,1,0,'d0357110161ceb0d2a776a97633d6b33',NULL,1,1,0,1),(2,'Manager','man','202cb962ac59075b964b07152d234b70',NULL,0,0,1,0,NULL,NULL,0,1,0,1),(3,'Developer','dev','202cb962ac59075b964b07152d234b70',NULL,0,0,0,1,NULL,NULL,0,1,0,1),(4,'Client','client','202cb962ac59075b964b07152d234b70',1,0,0,0,0,'7e2ba77e8c3f7cf2c0eaf7a3e57aa110',NULL,0,1,0,1),(5,'Vadym Manager','vadym_m','4297f44b13955235245b2497399d7a93',NULL,0,0,1,0,'ea1ae611fab1d35ca509bf0b34836cec',NULL,0,1,0,1),(6,'Vadym Developer','vadym','4297f44b13955235245b2497399d7a93',NULL,0,0,0,1,'f96f40a01030cf146a8c29c280befe45',NULL,0,1,0,1),(7,'Oleksii Developer','oleksii.ostapets@gmail.com','202cb962ac59075b964b07152d234b70',NULL,0,0,0,1,'26ec2e2d323130fc7d218ecd5e31915d',NULL,0,1,0,1),(8,'Romans','r@agiletech.ie','ff58c902bd980c805af25516caa93c76',NULL,0,0,1,0,'8616b5d75d23dcf2910dcb53ec1f30a4',NULL,1,1,0,1),(9,'Aleksejs Cizevskis','a@agiletech.ie','77417ff9519a1ce1e7aff751b1edd915',NULL,0,0,1,0,'99d592ae32e85da96e31d0dee85912f1',NULL,0,1,0,1),(10,'Kostya','kolodnitsky@gmail.com','202cb962ac59075b964b07152d234b70',NULL,0,0,0,1,'af87386a3734eee5b3bb5d73e82e7a6a',NULL,0,1,0,1),(11,'Валерий Вавилов','valery.vavilov@gmail.com','202cb962ac59075b964b07152d234b70',4,0,0,0,0,'c4e0b807effbf39107c0e40c41b19715',NULL,0,1,0,1),(12,'Dmitry','me@mayack.com','4297f44b13955235245b2497399d7a93',NULL,0,0,1,1,NULL,NULL,0,1,0,1),(13,'Harry Gwynne','gwynne.h@hotmail.co.uk','be638346e4d2b0170fcd73742ef60b54',3,0,0,0,0,'f5161142c5f771332bcb3ca71943b4c3',NULL,0,1,0,1),(14,'Susan Kelly','susan@jpoint.ie','4297f44b13955235245b2497399d7a93',2,0,0,0,0,NULL,NULL,0,1,0,1),(15,'Ray Rogers','ray.rogers@relate-software.com','4297f44b13955235245b2497399d7a93',5,0,0,0,0,'ca916c15627405097ca0d2cb0deb95a9',NULL,0,1,0,1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-08-20 13:18:52
