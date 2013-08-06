-- MySQL dump 10.13  Distrib 5.5.32, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: col
-- ------------------------------------------------------
-- Server version	5.5.32-0ubuntu0.12.04.1

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attach`
--

LOCK TABLES `attach` WRITE;
/*!40000 ALTER TABLE `attach` DISABLE KEYS */;
INSERT INTO `attach` VALUES (1,4,'','2013-07-12 08:37:48','2013-07-12 08:37:48',3);
/*!40000 ALTER TABLE `attach` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `budget`
--

DROP TABLE IF EXISTS `budget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budget` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `budget`
--

LOCK TABLES `budget` WRITE;
/*!40000 ALTER TABLE `budget` DISABLE KEYS */;
/*!40000 ALTER TABLE `budget` ENABLE KEYS */;
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client`
--

LOCK TABLES `client` WRITE;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
INSERT INTO `client` VALUES (1,'Gradpool','test@test.com',0,0,0.00,0,NULL,NULL,0.00),(2,'Aisling Software',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(3,'Owen Cooney',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(4,'Agile',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(5,'Foxframe',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(6,'Tunepresto',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(7,'University College Dublin',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(8,'Relate Software',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(9,'MORsolutions',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(10,'Travelshake',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(11,'Barry Alistair',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(12,'Kelly Price',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(13,'Whitepier',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(14,'Transpoco',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(15,'Linked Finance',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(16,'Orna Ross',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(17,'Eamonn Brennan',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(18,'Tony Realise4',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(19,'Realex Payments',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(20,'Mammoth Services',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(21,'Harry Gwynne',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(22,'Brian Stephenson','brian@textrepublic.com',NULL,0,NULL,NULL,NULL,NULL,NULL),(23,'client1','alftest@list.ru',NULL,1,NULL,NULL,NULL,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `filestore_file`
--

LOCK TABLES `filestore_file` WRITE;
/*!40000 ALTER TABLE `filestore_file` DISABLE KEYS */;
INSERT INTO `filestore_file` VALUES (1,2,1,'0/20130621111333__dsc-0013.jpg','DSC_0013.JPG',851689,0,''),(2,2,1,'0/20130621111424__dsc-0015.jpg','DSC_0015.JPG',852845,0,''),(3,2,1,'0/20130621131835__dsc-0078.jpg','DSC_0078.JPG',845292,0,''),(4,2,1,'0/20130712113743__asc-0013.jpg','ASC_0013.JPG',851689,0,''),(5,4,1,'0/20130725153136__git.book.rus.pdf','GIT.BOOK.RUS.pdf',316643,0,''),(6,4,1,'0/20130725154324__git.book.rus.pdf','GIT.BOOK.RUS.pdf',316643,0,''),(7,2,1,'0/20130725154650__gr1.jpg','gr1.jpg',1926,0,''),(8,1,1,'0/20130731110000__accept.png','accept.png',781,0,'');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `filestore_image`
--

LOCK TABLES `filestore_image` WRITE;
/*!40000 ALTER TABLE `filestore_image` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `filestore_type`
--

LOCK TABLES `filestore_type` WRITE;
/*!40000 ALTER TABLE `filestore_type` DISABLE KEYS */;
INSERT INTO `filestore_type` VALUES (1,'png','image/png','png'),(2,'jpeg','image/jpeg','jpeg'),(3,'gif','image/gif','gif'),(4,'application/pdf','application/pdf',NULL);
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
INSERT INTO `filestore_volume` VALUES (1,'upload','upload',1000000000,0,8,'Y',NULL);
/*!40000 ALTER TABLE `filestore_volume` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `participant`
--

LOCK TABLES `participant` WRITE;
/*!40000 ALTER TABLE `participant` DISABLE KEYS */;
INSERT INTO `participant` VALUES (1,1,22,'developer'),(2,2,22,'developer'),(6,2,25,'developer'),(7,3,25,'developer'),(8,3,22,'developer');
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
  PRIMARY KEY (`id`),
  KEY `fk_project_client1` (`client_id`),
  CONSTRAINT `fk_project_client1` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project`
--

LOCK TABLES `project` WRITE;
/*!40000 ALTER TABLE `project` DISABLE KEYS */;
INSERT INTO `project` VALUES (1,'project1','',1,'',''),(2,'project2','',1,'',''),(3,'project3','',2,'',''),(4,'Client Project 1','Client Project 1',1,'',''),(5,'Cli P 2','',1,'','');
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
  PRIMARY KEY (`id`),
  KEY `fk_quote_project1` (`project_id`),
  CONSTRAINT `fk_quote_project1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quote`
--

LOCK TABLES `quote` WRITE;
/*!40000 ALTER TABLE `quote` DISABLE KEYS */;
INSERT INTO `quote` VALUES (2,1,'q2',NULL,NULL,NULL,NULL,'estimation_approved',21,'',0,NULL,30.00,'GBP'),(3,4,'Client quotation 1',NULL,NULL,NULL,NULL,'estimated',23,NULL,NULL,NULL,NULL,NULL),(4,2,'q22',NULL,NULL,NULL,NULL,'estimate_needed',21,'',10,NULL,NULL,NULL),(5,3,'q3',NULL,NULL,NULL,NULL,'estimate_needed',21,'',0,'2013-07-30 21:00:00',NULL,NULL),(7,1,'q5',NULL,NULL,NULL,NULL,'not_estimated',21,NULL,NULL,NULL,NULL,NULL),(8,1,'q6',NULL,NULL,NULL,NULL,'estimation_approved',21,'asdasd asd asd a sd',0,'2013-09-29 21:00:00',35.00,'GBP'),(10,1,'qqq1',NULL,NULL,NULL,NULL,'quotation_requested',23,'test',NULL,NULL,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reqcomment`
--

LOCK TABLES `reqcomment` WRITE;
/*!40000 ALTER TABLE `reqcomment` DISABLE KEYS */;
INSERT INTO `reqcomment` VALUES (1,1,'not understand',22,0,NULL),(2,1,'This is easy, Just do it!',21,0,NULL),(3,9,'test',23,0,NULL),(6,15,'test 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 3\r\ntest 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 \r\ntest 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 \r\ntest 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 test 1234567890 ',21,0,NULL),(7,2,'my test 123\r\nmy test 123\r\nmy test 123 фыв афвы афвы а фыва фыв афыв афыв афыв афы вафы ва',22,NULL,NULL),(8,2,'ttt1',21,0,NULL),(10,10,'ab',22,0,NULL),(14,15,'dd',21,0,NULL),(20,6,'asd1',22,0,NULL),(21,14,'tee',21,8,NULL),(22,1,'test',22,NULL,'2013-08-01 06:42:00');
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
  `descr` text,
  `estimate` decimal(8,2) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `quote_id` int(11) DEFAULT '0',
  `file_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requirement`
--

LOCK TABLES `requirement` WRITE;
/*!40000 ALTER TABLE `requirement` DISABLE KEYS */;
INSERT INTO `requirement` VALUES (1,'req1','tttt',2.00,21,2,0),(2,'req2','To do!',12.00,21,2,1),(3,'new req1','',NULL,23,3,3),(5,'r4','',2.00,21,8,NULL),(6,'r1','',3.50,21,8,NULL),(7,'r2','',1.50,21,8,NULL),(9,'rr1','rawed',NULL,23,7,NULL),(10,'re1','aaaaaa',3.00,23,4,NULL),(11,'r1','ss',NULL,21,9,NULL),(13,'r1','asdas',NULL,21,5,NULL),(14,'req1234567890 t1234567890 y1234567890 u1234567890 i1234567890 o1234567890 p1234567890 a1234567890 s1234567890','saldfsdk jsdlfg sldjfnglskdjgh http://google.com/ slkdjfgh slkdjf skldjfg hlsdjkfhg ksdjfghs ld fnvskljdfh gdfh gvsdf \r\ngdsf gsl jfdgs;dklf ngsldf gs\r\ndfg sd jfngksjdf nglkdfn gskdjfg wdk hglejfngvs ldf gnv 49hrvwoierg ]w4rgp4w5ih 9wurenvlrth w4ur v 4gw e\r\n woruhg iurgier nlekjrntg',3.00,20,10,NULL),(15,'req2','asdf',NULL,21,10,NULL),(16,'req3','test',0.00,21,10,7),(18,'req4','',NULL,21,10,6);
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
  `assigned_id` int(11) DEFAULT NULL,
  `created_dts` timestamp NULL DEFAULT NULL,
  `updated_dts` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_task_budget1` (`budget_id`),
  KEY `fk_task_project1` (`project_id`),
  KEY `fk_task_screen1` (`requirement_id`),
  KEY `fk_task_assigned1` (`assigned_id`),
  CONSTRAINT `fk_task_assigned1` FOREIGN KEY (`assigned_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_budget1` FOREIGN KEY (`budget_id`) REFERENCES `budget` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_project1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_screen1` FOREIGN KEY (`requirement_id`) REFERENCES `requirement` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task`
--

LOCK TABLES `task` WRITE;
/*!40000 ALTER TABLE `task` DISABLE KEYS */;
INSERT INTO `task` VALUES (2,'Task 1','normal','finished',NULL,'task for req1 - step1',NULL,0.50,1,NULL,1,22,NULL,'2013-07-11 12:43:41'),(3,'Task2','normal','finished',NULL,'step 2фыв фыв фыв фыв фыв фыв фыв ыфва вфап вап вап  ыва пыва рывап ыва пыа пыв апыа пы ап\r\nыва пы вапы ва пв апы',NULL,1.00,1,NULL,1,22,'2013-07-11 12:43:30','2013-08-06 08:41:07'),(4,'just task','normal','unstarted',NULL,'asdf',NULL,10.00,NULL,NULL,1,22,'2013-07-12 06:33:51','2013-07-12 06:33:51'),(5,'task3','normal','unstarted',NULL,'sfadf',NULL,1.00,2,NULL,1,21,'2013-07-12 07:43:58','2013-07-12 07:43:58');
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
  `created_dts` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_task_time_task1` (`task_id`),
  KEY `fk_task_time_user1` (`user_id`),
  CONSTRAINT `fk_task_time_task1` FOREIGN KEY (`task_id`) REFERENCES `task` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_task_time_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_time`
--

LOCK TABLES `task_time` WRITE;
/*!40000 ALTER TABLE `task_time` DISABLE KEYS */;
INSERT INTO `task_time` VALUES (1,3,22,1.00,'done','2013-08-06 08:52:38'),(2,3,22,0.50,'','2013-08-06 08:54:44');
/*!40000 ALTER TABLE `task_time` ENABLE KEYS */;
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
  `is_admin` tinyint(4) DEFAULT '0',
  `is_manager` tinyint(4) DEFAULT '0',
  `is_developer` tinyint(4) DEFAULT '0',
  `hash` varchar(255) DEFAULT NULL,
  `weekly_target` int(11) DEFAULT NULL,
  `is_timereport` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_user_client` (`client_id`),
  CONSTRAINT `fk_user_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Romans Malinovskis','r@agiletech.ie','ff58c902bd980c805af25516caa93c76',NULL,1,1,1,'8b310b249133f88ba98935c6d4c0657e',20,1),(3,'Gita Malinovska','g@agiletech.ie','fe01ce2a7fbac8fafaed7c982a04e229',NULL,1,1,1,'b6be7013a2aa47ce1157a0684a9220cc',30,1),(4,'Andrew Fluery','andrew@gradpool.ie',NULL,1,0,0,0,'16046270f4bdcf4f04989c1e42e53a7a',40,1),(7,'Janis Volbergs','j@agiletech.ie','f5bb0c8de146c67b44babbf4e6584cc0',NULL,1,1,1,'8aa9e4b16ed60a428771edf375f23f23',30,1),(8,'Dmitry','m@agiletech.ie','e61c3734215b62c1e13a88792ebb18d4',NULL,0,1,1,'fd58b564da1b4d9b5893fc5329903aa4',40,1),(9,'max','max@agiletech.ie','d1696816bc1a7afe92f1c8787ac222c3',NULL,0,0,1,'02da9f0ec5dbf471dbdf289221a5e4fb',35,1),(10,'Serhij Stasyuk','stas@agiletech.ie','202cb962ac59075b964b07152d234b70',4,1,0,1,'5e2073e08525213f257e388f0826e051',20,1),(11,'Prashant','prashanth@cgvakindia.com','098f6bcd4621d373cade4e832627b4f6',NULL,0,1,1,'891a65302e16a3340967b147ea4eb67c',NULL,1),(13,'Ray Rogers','ray.rogers@relate-software.com','098f6bcd4621d373cade4e832627b4f6',8,0,0,0,'9556cdcc3598d4b64413552f15797d77',NULL,1),(14,'Peter','lf','fe01ce2a7fbac8fafaed7c982a04e229',15,0,0,0,'bb5f2f29d69b71be4bbfaa2899b03e79',NULL,1),(16,'Orna Ross','info@ornaross.com','41d56ec6a51ea83a1be672dd6b5dc298',16,0,0,0,'2f70976948dd7316924507d7c7b3ac55',NULL,1),(17,'Barry Alistair Paterson','barry@irishdev.com','098f6bcd4621d373cade4e832627b4f6',11,0,0,0,'6c704e572f09f8801b5ee67f840bddc0',NULL,0),(18,'Tony Goold','tony@realise4.ie','8f494d6b79722c201450e7ecec8f7694',18,0,0,0,'62e7e78717eaa554f6f36a97350fe1ff',NULL,0),(19,'Alex','a@agiletech.ie','5dadc79e341f236249b2661836dcbdca',NULL,1,1,1,'377f73316169f735fd091a8980768651',NULL,1),(20,'Admin','admin','202cb962ac59075b964b07152d234b70',NULL,1,0,0,'d0357110161ceb0d2a776a97633d6b33',NULL,1),(21,'Manager','man','202cb962ac59075b964b07152d234b70',NULL,0,1,0,'50128184852cae55957bf1d87720d82f',NULL,0),(22,'Developer','dev','202cb962ac59075b964b07152d234b70',NULL,0,0,1,'d002f2926c9af3960d7bb3056adca78e',40,1),(23,'Client','client','202cb962ac59075b964b07152d234b70',1,0,0,0,'d002f2926c9af3960d7bb3056adca78d',NULL,1),(24,'Client2','client2','202cb962ac59075b964b07152d234b70',2,0,0,0,NULL,NULL,1),(25,'Oleksii Developer','oleksii','202cb962ac59075b964b07152d234b70',NULL,0,0,1,NULL,NULL,0);
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

-- Dump completed on 2013-08-06 14:58:22
