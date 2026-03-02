-- MySQL dump 10.13  Distrib 5.7.11, for Win32 (AMD64)
--
-- Host: localhost    Database: course_orientation
-- ------------------------------------------------------
-- Server version	5.7.11

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
-- Table structure for table `app_logs`
--

DROP TABLE IF EXISTS `app_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `source` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'app',
  `level` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `event_type` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `context` json DEFAULT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `http_method` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_code` smallint(6) DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_app_logs_created_at` (`created_at`),
  KEY `idx_app_logs_source_created_at` (`source`,`created_at`),
  KEY `idx_app_logs_user_created_at` (`id_utilisateur`,`created_at`),
  KEY `idx_app_logs_event_created_at` (`event_type`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_logs`
--

LOCK TABLES `app_logs` WRITE;
/*!40000 ALTER TABLE `app_logs` DISABLE KEYS */;
INSERT INTO `app_logs` VALUES (1,'boitier','info','scan','test log api','{\"tag\": \"B01\"}',NULL,'POST','/api/logs/ingest',200,'127.0.0.1','2026-02-12 07:51:45'),(2,'app','info','http_request','GET connexion','{\"query\": []}',NULL,'GET','/connexion',200,'127.0.0.1','2026-02-12 07:53:13'),(3,'app','info','http_request','GET connexion','{\"query\": []}',NULL,'GET','/connexion',200,'127.0.0.1','2026-02-12 07:53:43'),(4,'app','info','http_request','GET admin','{\"query\": []}',NULL,'GET','/admin',302,'127.0.0.1','2026-02-12 08:01:44'),(5,'app','info','http_request','GET connexion','{\"query\": []}',NULL,'GET','/connexion',200,'127.0.0.1','2026-02-12 08:01:45'),(6,'app','info','http_request','GET prof/gestion-seances','{\"query\": []}',4,'GET','/prof/gestion-seances',200,'127.0.0.1','2026-02-12 08:11:13'),(7,'app','info','http_request','GET prof','{\"query\": []}',4,'GET','/prof',200,'127.0.0.1','2026-02-12 08:11:13'),(8,'app','info','http_request','GET prof/gestion-boitiers','{\"query\": []}',4,'GET','/prof/gestion-boitiers',200,'127.0.0.1','2026-02-12 08:11:17'),(9,'app','info','http_request','GET prof','{\"query\": []}',4,'GET','/prof',200,'127.0.0.1','2026-02-12 08:11:19'),(10,'app','info','http_request','GET prof/gestion-seances','{\"query\": []}',4,'GET','/prof/gestion-seances',200,'127.0.0.1','2026-02-12 08:11:19'),(11,'app','info','http_request','GET prof','{\"query\": []}',4,'GET','/prof',200,'127.0.0.1','2026-02-12 08:11:21'),(12,'app','info','http_request','GET prof/gestion-eleves','{\"query\": []}',4,'GET','/prof/gestion-eleves',200,'127.0.0.1','2026-02-12 08:11:22'),(13,'app','info','http_request','GET prof','{\"query\": []}',4,'GET','/prof',200,'127.0.0.1','2026-02-12 08:11:24'),(14,'app','info','http_request','GET prof/gestion-classes','{\"query\": []}',4,'GET','/prof/gestion-classes',200,'127.0.0.1','2026-02-12 08:11:25'),(15,'app','info','http_request','GET prof','{\"query\": []}',4,'GET','/prof',200,'127.0.0.1','2026-02-12 08:11:26'),(16,'app','info','http_request','GET prof/gestion-eleves','{\"query\": []}',4,'GET','/prof/gestion-eleves',200,'127.0.0.1','2026-02-12 08:11:27'),(17,'app','info','http_request','GET prof/gestion-eleves','{\"query\": {\"nom\": null, \"prenom\": null, \"id_classe\": \"1\"}}',4,'GET','/prof/gestion-eleves',200,'127.0.0.1','2026-02-12 08:11:47'),(18,'app','info','http_request','GET prof','{\"query\": []}',4,'GET','/prof',200,'127.0.0.1','2026-02-12 08:11:47'),(19,'app','info','http_request','GET prof/gestion-classes','{\"query\": []}',4,'GET','/prof/gestion-classes',200,'127.0.0.1','2026-02-12 08:11:49'),(20,'app','info','http_request','POST prof/gestion-classes','{\"query\": []}',4,'POST','/prof/gestion-classes',302,'127.0.0.1','2026-02-12 08:12:14'),(21,'app','info','http_request','GET prof/gestion-classes','{\"query\": []}',4,'GET','/prof/gestion-classes',200,'127.0.0.1','2026-02-12 08:12:15'),(22,'app','info','http_request','GET prof/modifier-classe','{\"query\": {\"id_classe\": \"2\"}}',4,'GET','/prof/modifier-classe',200,'127.0.0.1','2026-02-12 08:12:16'),(23,'app','error','http_request','POST prof/modifier-classe/import-csv','{\"query\": []}',4,'POST','/prof/modifier-classe/import-csv',500,'127.0.0.1','2026-02-12 08:13:57'),(24,'app','info','http_request','GET prof/modifier-classe','{\"query\": {\"id_classe\": \"2\"}}',4,'GET','/prof/modifier-classe',200,'127.0.0.1','2026-02-12 08:17:09'),(25,'app','error','http_request','POST prof/modifier-classe/import-csv','{\"query\": []}',4,'POST','/prof/modifier-classe/import-csv',500,'127.0.0.1','2026-02-12 08:19:23'),(26,'app','info','http_request','GET prof/modifier-classe','{\"query\": {\"id_classe\": \"2\"}}',4,'GET','/prof/modifier-classe',200,'127.0.0.1','2026-02-12 08:21:54'),(27,'app','info','http_request','POST prof/modifier-classe/import-csv','{\"query\": []}',4,'POST','/prof/modifier-classe/import-csv',302,'127.0.0.1','2026-02-12 08:22:02'),(28,'app','info','http_request','GET prof/modifier-classe','{\"query\": {\"id_classe\": \"2\"}}',4,'GET','/prof/modifier-classe',200,'127.0.0.1','2026-02-12 08:22:03'),(29,'app','info','http_request','GET prof/gestion-classes','{\"query\": []}',4,'GET','/prof/gestion-classes',200,'127.0.0.1','2026-02-12 08:22:11'),(30,'app','info','http_request','GET prof','{\"query\": []}',4,'GET','/prof',200,'127.0.0.1','2026-02-12 08:22:13'),(31,'app','info','http_request','GET prof/gestion-eleves','{\"query\": []}',4,'GET','/prof/gestion-eleves',200,'127.0.0.1','2026-02-12 08:22:16'),(32,'app','info','http_request','GET logout','{\"query\": []}',NULL,'GET','/logout',302,'127.0.0.1','2026-02-12 08:22:26'),(33,'app','info','http_request','GET connexion','{\"query\": []}',NULL,'GET','/connexion',200,'127.0.0.1','2026-02-12 08:22:26'),(34,'app','info','http_request','POST login','{\"query\": []}',5,'POST','/login',302,'127.0.0.1','2026-02-12 08:22:32'),(35,'app','info','http_request','GET eleve','{\"query\": []}',5,'GET','/eleve',200,'127.0.0.1','2026-02-12 08:22:33'),(36,'app','info','http_request','GET eleve/profil','{\"query\": []}',5,'GET','/eleve/profil',200,'127.0.0.1','2026-02-12 08:22:35'),(37,'app','info','http_request','GET eleve','{\"query\": []}',5,'GET','/eleve',200,'127.0.0.1','2026-02-12 08:22:39'),(38,'app','info','http_request','GET eleve/historique','{\"query\": []}',5,'GET','/eleve/historique',200,'127.0.0.1','2026-02-12 08:23:01'),(39,'app','info','http_request','GET eleve','{\"query\": []}',5,'GET','/eleve',200,'127.0.0.1','2026-02-12 08:23:03'),(40,'app','info','http_request','GET eleve','{\"query\": []}',5,'GET','/eleve',200,'127.0.0.1','2026-02-12 08:25:14'),(41,'app','info','http_request','GET eleve/profil','{\"query\": []}',5,'GET','/eleve/profil',200,'127.0.0.1','2026-02-12 08:25:15'),(42,'app','info','http_request','POST eleve/profil','{\"query\": []}',5,'POST','/eleve/profil',302,'127.0.0.1','2026-02-12 08:25:24'),(43,'app','info','http_request','GET eleve/profil','{\"query\": []}',5,'GET','/eleve/profil',200,'127.0.0.1','2026-02-12 08:25:24'),(44,'app','info','http_request','GET eleve','{\"query\": []}',5,'GET','/eleve',200,'127.0.0.1','2026-02-12 08:25:26'),(45,'app','info','http_request','GET logout','{\"query\": []}',NULL,'GET','/logout',302,'127.0.0.1','2026-02-12 08:25:58'),(46,'app','info','http_request','GET connexion','{\"query\": []}',NULL,'GET','/connexion',200,'127.0.0.1','2026-02-12 08:25:59'),(47,'app','info','http_request','POST login','{\"query\": []}',4,'POST','/login',302,'127.0.0.1','2026-02-12 08:26:04'),(48,'app','info','http_request','GET prof','{\"query\": []}',4,'GET','/prof',200,'127.0.0.1','2026-02-12 08:26:04'),(49,'app','info','http_request','GET prof/historique-seances','{\"query\": []}',4,'GET','/prof/historique-seances',200,'127.0.0.1','2026-02-12 08:26:06'),(50,'app','info','http_request','GET prof/seance-trajet','{\"query\": {\"id\": \"1\"}}',4,'GET','/prof/seance-trajet',200,'127.0.0.1','2026-02-12 08:26:08'),(51,'app','info','http_request','GET logout','{\"query\": []}',NULL,'GET','/logout',302,'127.0.0.1','2026-02-12 08:27:39'),(52,'app','info','http_request','GET connexion','{\"query\": []}',NULL,'GET','/connexion',200,'127.0.0.1','2026-02-12 08:27:40'),(53,'app','info','http_request','POST login','{\"query\": []}',5,'POST','/login',302,'127.0.0.1','2026-02-12 08:27:49'),(54,'app','info','http_request','GET eleve','{\"query\": []}',5,'GET','/eleve',200,'127.0.0.1','2026-02-12 08:27:49'),(55,'app','info','http_request','GET eleve/historique','{\"query\": []}',5,'GET','/eleve/historique',200,'127.0.0.1','2026-02-12 08:28:12'),(56,'app','info','http_request','GET logout','{\"query\": []}',NULL,'GET','/logout',302,'127.0.0.1','2026-02-12 08:29:28'),(57,'app','info','http_request','GET connexion','{\"query\": []}',NULL,'GET','/connexion',200,'127.0.0.1','2026-02-12 08:29:29'),(58,'app','info','http_request','POST login','{\"query\": []}',4,'POST','/login',302,'127.0.0.1','2026-02-12 08:29:33'),(59,'app','info','http_request','GET prof','{\"query\": []}',4,'GET','/prof',200,'127.0.0.1','2026-02-12 08:29:33'),(60,'app','info','http_request','GET prof/historique-seances','{\"query\": []}',4,'GET','/prof/historique-seances',200,'127.0.0.1','2026-02-12 08:29:36'),(61,'app','info','http_request','GET prof/seance-trajet','{\"query\": {\"id\": \"1\"}}',4,'GET','/prof/seance-trajet',200,'127.0.0.1','2026-02-12 08:29:37'),(62,'app','info','http_request','GET prof/historique-seances','{\"query\": []}',4,'GET','/prof/historique-seances',200,'127.0.0.1','2026-02-12 08:30:21'),(63,'app','info','http_request','GET prof/seance-trajet','{\"query\": {\"id\": \"1\"}}',4,'GET','/prof/seance-trajet',200,'127.0.0.1','2026-02-12 08:30:23'),(64,'app','info','http_request','GET prof/historique-seances','{\"query\": []}',4,'GET','/prof/historique-seances',200,'127.0.0.1','2026-02-12 08:30:46'),(65,'app','info','http_request','GET prof/historique-seances','{\"query\": []}',4,'GET','/prof/historique-seances',200,'127.0.0.1','2026-02-12 08:30:46'),(66,'app','info','http_request','GET logout','{\"query\": []}',NULL,'GET','/logout',302,'127.0.0.1','2026-02-12 08:32:45'),(67,'app','info','http_request','GET connexion','{\"query\": []}',NULL,'GET','/connexion',200,'127.0.0.1','2026-02-12 08:32:46'),(68,'app','info','http_request','POST login','{\"query\": []}',5,'POST','/login',302,'127.0.0.1','2026-02-12 08:32:53'),(69,'app','info','http_request','GET eleve','{\"query\": []}',5,'GET','/eleve',200,'127.0.0.1','2026-02-12 08:32:53'),(70,'app','info','http_request','GET eleve/profil','{\"query\": []}',5,'GET','/eleve/profil',200,'127.0.0.1','2026-02-12 08:33:38'),(71,'app','info','http_request','POST eleve/profil','{\"query\": []}',5,'POST','/eleve/profil',302,'127.0.0.1','2026-02-12 08:33:48'),(72,'app','info','http_request','GET eleve/profil','{\"query\": []}',5,'GET','/eleve/profil',200,'127.0.0.1','2026-02-12 08:33:49'),(73,'app','info','http_request','POST eleve/profil','{\"query\": []}',5,'POST','/eleve/profil',302,'127.0.0.1','2026-02-12 08:34:39'),(74,'app','info','http_request','GET eleve/profil','{\"query\": []}',5,'GET','/eleve/profil',200,'127.0.0.1','2026-02-12 08:34:40'),(75,'app','info','http_request','GET eleve','{\"query\": []}',5,'GET','/eleve',200,'127.0.0.1','2026-02-12 08:34:45'),(76,'app','info','http_request','GET logout','{\"query\": []}',NULL,'GET','/logout',302,'127.0.0.1','2026-02-12 08:34:55'),(77,'app','info','http_request','GET connexion','{\"query\": []}',NULL,'GET','/connexion',200,'127.0.0.1','2026-02-12 08:34:56'),(78,'app','info','http_request','POST login','{\"query\": []}',4,'POST','/login',302,'127.0.0.1','2026-02-12 08:35:26'),(79,'app','info','http_request','GET prof','{\"query\": []}',4,'GET','/prof',200,'127.0.0.1','2026-02-12 08:35:26'),(80,'app','info','http_request','GET prof/historique-seances','{\"query\": []}',4,'GET','/prof/historique-seances',200,'127.0.0.1','2026-02-12 08:36:16'),(81,'app','info','http_request','GET prof/seance-trajet','{\"query\": {\"id\": \"1\"}}',4,'GET','/prof/seance-trajet',200,'127.0.0.1','2026-02-12 08:36:21'),(82,'app','info','http_request','GET prof/historique-seances','{\"query\": []}',4,'GET','/prof/historique-seances',200,'127.0.0.1','2026-02-12 08:37:25'),(83,'app','info','http_request','GET prof/seance-trajet','{\"query\": {\"id\": \"2\"}}',4,'GET','/prof/seance-trajet',200,'127.0.0.1','2026-02-12 08:37:27'),(84,'app','info','http_request','GET prof/historique-seances','{\"query\": []}',4,'GET','/prof/historique-seances',200,'127.0.0.1','2026-02-12 08:38:08'),(85,'app','info','http_request','GET prof/historique-seances','{\"query\": []}',4,'GET','/prof/historique-seances',200,'127.0.0.1','2026-02-12 08:38:09'),(86,'app','info','http_request','GET prof','{\"query\": []}',4,'GET','/prof',200,'127.0.0.1','2026-02-12 08:38:11'),(87,'app','info','http_request','GET logout','{\"query\": []}',NULL,'GET','/logout',302,'127.0.0.1','2026-02-12 08:40:50'),(88,'app','info','http_request','GET connexion','{\"query\": []}',NULL,'GET','/connexion',200,'127.0.0.1','2026-02-12 08:40:51'),(89,'app','info','http_request','POST login','{\"query\": []}',4,'POST','/login',302,'127.0.0.1','2026-02-12 08:44:32'),(90,'app','info','http_request','GET prof','{\"query\": []}',4,'GET','/prof',200,'127.0.0.1','2026-02-12 08:44:33'),(91,'app','info','http_request','GET prof/historique-seances','{\"query\": []}',4,'GET','/prof/historique-seances',200,'127.0.0.1','2026-02-12 08:44:38'),(92,'app','info','http_request','GET prof/seance-trajet','{\"query\": {\"id\": \"1\"}}',4,'GET','/prof/seance-trajet',200,'127.0.0.1','2026-02-12 08:44:41'),(93,'app','info','http_request','GET prof/historique-seances','{\"query\": []}',4,'GET','/prof/historique-seances',200,'127.0.0.1','2026-02-12 08:45:05'),(94,'app','info','http_request','GET prof','{\"query\": []}',4,'GET','/prof',200,'127.0.0.1','2026-02-12 08:45:23'),(95,'app','info','http_request','GET prof/seance-trajet','{\"query\": {\"id\": \"2\"}}',4,'GET','/prof/seance-trajet',200,'127.0.0.1','2026-02-12 08:45:23'),(96,'app','info','http_request','GET prof/historique-seances','{\"query\": []}',4,'GET','/prof/historique-seances',200,'127.0.0.1','2026-02-12 08:46:02'),(97,'app','info','http_request','GET logout','{\"query\": []}',NULL,'GET','/logout',302,'127.0.0.1','2026-02-12 08:46:02'),(98,'app','info','http_request','GET connexion','{\"query\": []}',NULL,'GET','/connexion',200,'127.0.0.1','2026-02-12 08:46:03'),(99,'app','info','http_request','POST login','{\"query\": []}',5,'POST','/login',302,'127.0.0.1','2026-02-12 08:46:09'),(100,'app','info','http_request','GET eleve','{\"query\": []}',5,'GET','/eleve',200,'127.0.0.1','2026-02-12 08:46:09'),(101,'app','info','http_request','GET logout','{\"query\": []}',NULL,'GET','/logout',302,'127.0.0.1','2026-02-12 08:47:12'),(102,'app','info','http_request','GET connexion','{\"query\": []}',NULL,'GET','/connexion',200,'127.0.0.1','2026-02-12 08:47:13'),(103,'app','info','http_request','POST login','{\"query\": []}',3,'POST','/login',302,'127.0.0.1','2026-02-12 08:48:50'),(104,'app','info','http_request','GET admin','{\"query\": []}',3,'GET','/admin',200,'127.0.0.1','2026-02-12 08:48:51'),(105,'app','info','http_request','GET admin/utilisateurs','{\"query\": []}',3,'GET','/admin/utilisateurs',200,'127.0.0.1','2026-02-12 08:49:34'),(106,'app','info','http_request','GET admin/utilisateurs/ajouter','{\"query\": []}',3,'GET','/admin/utilisateurs/ajouter',200,'127.0.0.1','2026-02-12 08:49:35'),(107,'app','info','http_request','POST admin/utilisateurs/ajouter','{\"query\": []}',3,'POST','/admin/utilisateurs/ajouter',302,'127.0.0.1','2026-02-12 08:50:48'),(108,'app','info','http_request','GET admin/utilisateurs','{\"query\": []}',3,'GET','/admin/utilisateurs',200,'127.0.0.1','2026-02-12 08:50:49'),(109,'app','info','http_request','GET logout','{\"query\": []}',NULL,'GET','/logout',302,'127.0.0.1','2026-02-12 08:50:52'),(110,'app','info','http_request','GET connexion','{\"query\": []}',NULL,'GET','/connexion',200,'127.0.0.1','2026-02-12 08:50:52'),(111,'app','info','http_request','POST login','{\"query\": []}',23,'POST','/login',302,'127.0.0.1','2026-02-12 08:50:56'),(112,'app','info','http_request','GET admin','{\"query\": []}',23,'GET','/admin',200,'127.0.0.1','2026-02-12 08:50:57'),(113,'app','error','http_request','GET admin/logs','{\"query\": []}',23,'GET','/admin/logs',500,'127.0.0.1','2026-02-12 08:51:04'),(114,'app','info','http_request','GET admin/utilisateurs','{\"query\": []}',23,'GET','/admin/utilisateurs',200,'127.0.0.1','2026-02-12 08:51:29'),(115,'app','info','http_request','GET admin/utilisateurs/23/modifier','{\"query\": []}',23,'GET','/admin/utilisateurs/23/modifier',200,'127.0.0.1','2026-02-12 08:51:32'),(116,'app','info','http_request','GET admin/utilisateurs','{\"query\": []}',23,'GET','/admin/utilisateurs',200,'127.0.0.1','2026-02-12 08:52:08'),(117,'app','info','http_request','GET admin','{\"query\": []}',23,'GET','/admin',200,'127.0.0.1','2026-02-12 08:52:18'),(118,'app','info','http_request','GET admin/logs','{\"query\": []}',23,'GET','/admin/logs',200,'127.0.0.1','2026-02-12 08:52:20'),(119,'app','info','http_request','GET admin/logs','{\"query\": {\"date\": null, \"user\": \"4\", \"level\": null, \"source\": null}}',23,'GET','/admin/logs',200,'127.0.0.1','2026-02-12 08:52:25'),(120,'app','info','http_request','GET admin/logs','{\"query\": {\"date\": null, \"user\": \"4\", \"level\": \"error\", \"source\": null}}',23,'GET','/admin/logs',200,'127.0.0.1','2026-02-12 08:52:36'),(121,'app','info','http_request','GET admin','{\"query\": []}',23,'GET','/admin',200,'127.0.0.1','2026-02-12 08:52:42'),(122,'app','info','http_request','GET logout','{\"query\": []}',NULL,'GET','/logout',302,'127.0.0.1','2026-02-12 08:52:45'),(123,'app','info','http_request','GET connexion','{\"query\": []}',NULL,'GET','/connexion',200,'127.0.0.1','2026-02-12 08:52:45');
/*!40000 ALTER TABLE `app_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `balise`
--

DROP TABLE IF EXISTS `balise`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `balise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `lat` float DEFAULT NULL,
  `lng` float DEFAULT NULL,
  `alt` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `balise`
--

LOCK TABLES `balise` WRITE;
/*!40000 ALTER TABLE `balise` DISABLE KEYS */;
INSERT INTO `balise` VALUES (1,'B01','Arbre',45.1,5.7,200),(2,'B02','Rocher',45.2,5.8,210),(3,'B03','Riviere',45.3,5.9,220);
/*!40000 ALTER TABLE `balise` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boitier`
--

DROP TABLE IF EXISTS `boitier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boitier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mac` varchar(50) DEFAULT NULL,
  `etat` varchar(20) DEFAULT NULL,
  `reseau` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boitier`
--

LOCK TABLES `boitier` WRITE;
/*!40000 ALTER TABLE `boitier` DISABLE KEYS */;
INSERT INTO `boitier` VALUES (1,'AA:BB:CC:DD','disponible','192.168.1.50');
/*!40000 ALTER TABLE `boitier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boitier_etat_logs`
--

DROP TABLE IF EXISTS `boitier_etat_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boitier_etat_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_boitier` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `ancien_etat` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nouvel_etat` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_boitier_etat_logs_boitier_time` (`id_boitier`,`created_at`),
  KEY `idx_boitier_etat_logs_user_time` (`id_utilisateur`,`created_at`),
  CONSTRAINT `boitier_etat_logs_id_boitier_foreign` FOREIGN KEY (`id_boitier`) REFERENCES `boitier` (`id`) ON DELETE CASCADE,
  CONSTRAINT `boitier_etat_logs_id_utilisateur_foreign` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boitier_etat_logs`
--

LOCK TABLES `boitier_etat_logs` WRITE;
/*!40000 ALTER TABLE `boitier_etat_logs` DISABLE KEYS */;
INSERT INTO `boitier_etat_logs` VALUES (1,1,4,'actif','hors_service','2026-02-12 07:48:24'),(2,1,4,'hors_service','disponible','2026-02-12 07:48:29');
/*!40000 ALTER TABLE `boitier_etat_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classe`
--

DROP TABLE IF EXISTS `classe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classe`
--

LOCK TABLES `classe` WRITE;
/*!40000 ALTER TABLE `classe` DISABLE KEYS */;
INSERT INTO `classe` VALUES (1,'Terminale STI2D'),(2,'CIEL 2');
/*!40000 ALTER TABLE `classe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compose_parcours`
--

DROP TABLE IF EXISTS `compose_parcours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compose_parcours` (
  `id_parcours` int(11) NOT NULL,
  `id_balise` int(11) NOT NULL,
  PRIMARY KEY (`id_parcours`,`id_balise`),
  KEY `id_balise` (`id_balise`),
  CONSTRAINT `compose_parcours_ibfk_1` FOREIGN KEY (`id_parcours`) REFERENCES `parcours` (`id`),
  CONSTRAINT `compose_parcours_ibfk_2` FOREIGN KEY (`id_balise`) REFERENCES `balise` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compose_parcours`
--

LOCK TABLES `compose_parcours` WRITE;
/*!40000 ALTER TABLE `compose_parcours` DISABLE KEYS */;
INSERT INTO `compose_parcours` VALUES (1,1),(1,2),(1,3);
/*!40000 ALTER TABLE `compose_parcours` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groupe_seance`
--

DROP TABLE IF EXISTS `groupe_seance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupe_seance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_seance` int(11) DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_seance` (`id_seance`),
  CONSTRAINT `groupe_seance_ibfk_1` FOREIGN KEY (`id_seance`) REFERENCES `seance` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupe_seance`
--

LOCK TABLES `groupe_seance` WRITE;
/*!40000 ALTER TABLE `groupe_seance` DISABLE KEYS */;
INSERT INTO `groupe_seance` VALUES (1,1,'Binome 1'),(2,2,'Thomas solo');
/*!40000 ALTER TABLE `groupe_seance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groupe_seance_utilisateur`
--

DROP TABLE IF EXISTS `groupe_seance_utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupe_seance_utilisateur` (
  `id_groupe` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  PRIMARY KEY (`id_groupe`,`id_utilisateur`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `groupe_seance_utilisateur_ibfk_1` FOREIGN KEY (`id_groupe`) REFERENCES `groupe_seance` (`id`),
  CONSTRAINT `groupe_seance_utilisateur_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupe_seance_utilisateur`
--

LOCK TABLES `groupe_seance_utilisateur` WRITE;
/*!40000 ALTER TABLE `groupe_seance_utilisateur` DISABLE KEYS */;
INSERT INTO `groupe_seance_utilisateur` VALUES (1,1),(1,2),(2,5);
/*!40000 ALTER TABLE `groupe_seance_utilisateur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liaison_classe`
--

DROP TABLE IF EXISTS `liaison_classe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liaison_classe` (
  `id_classe` int(11) NOT NULL,
  `id_eleve` int(11) NOT NULL,
  PRIMARY KEY (`id_classe`,`id_eleve`),
  KEY `id_eleve` (`id_eleve`),
  CONSTRAINT `liaison_classe_ibfk_1` FOREIGN KEY (`id_classe`) REFERENCES `classe` (`id`),
  CONSTRAINT `liaison_classe_ibfk_2` FOREIGN KEY (`id_eleve`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liaison_classe`
--

LOCK TABLES `liaison_classe` WRITE;
/*!40000 ALTER TABLE `liaison_classe` DISABLE KEYS */;
INSERT INTO `liaison_classe` VALUES (1,1),(1,2),(2,5),(2,6),(2,7),(2,8),(2,9),(2,10),(2,11),(2,12),(2,13),(2,14),(2,15),(2,16),(2,17),(2,18),(2,19),(2,20),(2,21),(2,22);
/*!40000 ALTER TABLE `liaison_classe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `localisation`
--

DROP TABLE IF EXISTS `localisation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `localisation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_seance` int(11) NOT NULL,
  `id_boitier` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `altitude` decimal(8,2) DEFAULT NULL,
  `recorded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_localisation_seance_time` (`id_seance`,`recorded_at`),
  KEY `idx_localisation_boitier_time` (`id_boitier`,`recorded_at`),
  CONSTRAINT `localisation_id_boitier_foreign` FOREIGN KEY (`id_boitier`) REFERENCES `boitier` (`id`),
  CONSTRAINT `localisation_id_seance_foreign` FOREIGN KEY (`id_seance`) REFERENCES `seance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `localisation`
--

LOCK TABLES `localisation` WRITE;
/*!40000 ALTER TABLE `localisation` DISABLE KEYS */;
INSERT INTO `localisation` VALUES (8,2,1,50.51930000,2.65140000,78.20,'2026-02-12 09:37:18',NULL,NULL),(9,2,1,50.51936000,2.65152000,78.50,'2026-02-12 09:38:18',NULL,NULL),(10,2,1,50.51942000,2.65164000,78.70,'2026-02-12 09:39:18',NULL,NULL),(11,2,1,50.51950000,2.65170000,79.10,'2026-02-12 09:40:18',NULL,NULL),(12,2,1,50.51956000,2.65162000,79.00,'2026-02-12 09:41:18',NULL,NULL),(13,2,1,50.51952000,2.65150000,78.80,'2026-02-12 09:42:18',NULL,NULL),(14,2,1,50.51946000,2.65142000,78.40,'2026-02-12 09:43:18',NULL,NULL),(15,1,1,50.51759000,2.65240000,200.50,'2026-02-12 09:44:20',NULL,NULL),(16,1,1,50.51855000,2.65207000,201.10,'2026-02-12 09:44:20',NULL,NULL),(17,1,1,50.51829000,2.65460000,201.90,'2026-02-12 09:44:20',NULL,NULL),(18,1,1,50.51826000,2.65491000,201.90,'2026-02-12 09:44:20',NULL,NULL);
/*!40000 ALTER TABLE `localisation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_02_12_000100_create_localisation_table',1),(2,'2026_02_12_000200_create_boitier_etat_logs_table',2),(3,'2026_02_12_000300_create_app_logs_table',3),(4,'2026_02_12_000400_add_id_eleve_to_seance_table',4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parcours`
--

DROP TABLE IF EXISTS `parcours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parcours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) DEFAULT NULL,
  `niveau` varchar(20) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parcours`
--

LOCK TABLES `parcours` WRITE;
/*!40000 ALTER TABLE `parcours` DISABLE KEYS */;
INSERT INTO `parcours` VALUES (1,'Parcours facile','facile','Parcours découverte en forêt');
/*!40000 ALTER TABLE `parcours` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resultat`
--

DROP TABLE IF EXISTS `resultat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resultat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_seance` int(11) DEFAULT NULL,
  `nb_balise_valide` int(11) DEFAULT NULL,
  `note` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_seance` (`id_seance`),
  CONSTRAINT `resultat_ibfk_1` FOREIGN KEY (`id_seance`) REFERENCES `seance` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resultat`
--

LOCK TABLES `resultat` WRITE;
/*!40000 ALTER TABLE `resultat` DISABLE KEYS */;
INSERT INTO `resultat` VALUES (1,1,2,14),(2,2,2,14.5);
/*!40000 ALTER TABLE `resultat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scan_seance`
--

DROP TABLE IF EXISTS `scan_seance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scan_seance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_seance` int(11) DEFAULT NULL,
  `id_balise` int(11) DEFAULT NULL,
  `id_eleve` int(11) DEFAULT NULL,
  `valide` tinyint(1) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_seance` (`id_seance`),
  KEY `id_balise` (`id_balise`),
  KEY `id_eleve` (`id_eleve`),
  CONSTRAINT `scan_seance_ibfk_1` FOREIGN KEY (`id_seance`) REFERENCES `seance` (`id`),
  CONSTRAINT `scan_seance_ibfk_2` FOREIGN KEY (`id_balise`) REFERENCES `balise` (`id`),
  CONSTRAINT `scan_seance_ibfk_3` FOREIGN KEY (`id_eleve`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scan_seance`
--

LOCK TABLES `scan_seance` WRITE;
/*!40000 ALTER TABLE `scan_seance` DISABLE KEYS */;
INSERT INTO `scan_seance` VALUES (1,1,1,1,1,'2026-02-10 11:30:28'),(2,1,2,2,1,'2026-02-10 11:30:28'),(3,1,3,1,0,'2026-02-10 11:30:28'),(4,2,1,5,1,'2026-02-12 09:37:18'),(5,2,2,5,1,'2026-02-12 09:39:18'),(6,2,3,5,0,'2026-02-12 09:41:18');
/*!40000 ALTER TABLE `scan_seance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seance`
--

DROP TABLE IF EXISTS `seance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) DEFAULT NULL,
  `id_parcours` int(11) DEFAULT NULL,
  `id_boitier` int(11) DEFAULT NULL,
  `date_debut` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_parcours` (`id_parcours`),
  KEY `id_boitier` (`id_boitier`),
  KEY `idx_seance_id_eleve` (`id_eleve`),
  CONSTRAINT `fk_seance_id_eleve` FOREIGN KEY (`id_eleve`) REFERENCES `utilisateur` (`id`) ON DELETE SET NULL,
  CONSTRAINT `seance_ibfk_1` FOREIGN KEY (`id_parcours`) REFERENCES `parcours` (`id`),
  CONSTRAINT `seance_ibfk_2` FOREIGN KEY (`id_boitier`) REFERENCES `boitier` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seance`
--

LOCK TABLES `seance` WRITE;
/*!40000 ALTER TABLE `seance` DISABLE KEYS */;
INSERT INTO `seance` VALUES (1,1,1,1,'2026-02-10 11:30:27'),(2,5,1,1,'2026-02-12 09:36:18');
/*!40000 ALTER TABLE `seance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `login` varchar(100) DEFAULT NULL,
  `mdp` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateur`
--

LOCK TABLES `utilisateur` WRITE;
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` VALUES (1,'Dupont','Lucas','dupont.lucas','1234','eleve',NULL),(2,'Martin','Emma','martin.emma','1234','eleve',NULL),(3,'Declercq','Tifaine','admin','admin','admin',NULL),(4,'prof1','prof1','prof1','$2y$10$f/RPi0oKFHfR2dXpx9WHAerX9ZqCLk8FcVJwEbSR0CwvA.V2dgySC','prof',NULL),(5,'ALLENDER','Thomas','allender.thomas','$2y$10$xfSSppUu2uEqZq2/KslGKuX8Qbu4.kdJsJAFZnpvNodZ0YJnqPkWO','eleve','/uploads/profiles/eleve_5_1770885279.jpg'),(6,'BELLEVAL','Mattheo','belleval.mattheo','$2y$10$.3NG9g5lfXvuDranPyRcRe1NtAjv15FJ2/1RCBoOEin5ocKnbn4Ce','eleve',NULL),(7,'BOURDON','Simon','bourdon.simon','$2y$10$iU2opmGo7dgQFQlagrMzhO/ypNctDvuCNrbeVj8hYd4CJjxRx9ldK','eleve',NULL),(8,'CAILLIERET','Thomas','caillieret.thomas','$2y$10$ZN.At7W8qpX61mPiPH6lHutFL563snO1kfXBMK0C72L6n5pz6Nzo6','eleve',NULL),(9,'CAMBIER','Maxence','cambier.maxence','$2y$10$pcZt8v8v5XK/7YIy37htfOVYhlvQE06muvJv9z/iieAWNnf1Z94HC','eleve',NULL),(10,'CHRETIEN','Florentin','chretien.florentin','$2y$10$CQTFkQ0VNm1am8maOoYxZOU5nfv.EYuV0Bg3DdhWgNSkBMMITwATG','eleve',NULL),(11,'DESPREZ','Guylian','desprez.guylian','$2y$10$l3nERovv51.CXrCNAWoYweZSI.Rfg20H6f2JCg0IowPWoa2bbqvYS','eleve',NULL),(12,'DOUILLEZ','Matheo','douillez.matheo','$2y$10$agh9oTTHFc90ZPH85I/LxOR93Xy2JsYSpDng8DmYBXTlLQrX.z8um','eleve',NULL),(13,'DUPROT','Teo','duprot.teo','$2y$10$0zMCYuYebjuo83NXOQ/G/.LQPyuNXkZ6xcRp1R4aOYU63n4Nw4TFS','eleve',NULL),(14,'FORGET','Anthony','forget.anthony','$2y$10$.QsoRUoklwCa.IXWeDyBv.N7UozwMikOBsGPHshCyMh44nb6hJViC','eleve',NULL),(15,'GUNTH','Pierre-Alexandre','gunth.pierre-alexandre','$2y$10$q6IiKFie1HWXnJ7M29MNuekx72XE0EAv9jfV/ioUJsCLZY2umWrnC','eleve',NULL),(16,'KOWALCZYK','Alexandre','kowalczyk.alexandre','$2y$10$2ra9dc.QB8UP1PAPfBeQLevxigfAoHzqnObBFw65qTSlvzbDyMoZi','eleve',NULL),(17,'LEPINGLE','Noa','lepingle.noa','$2y$10$L4ZqJy6xjTnZKIycKsIV1u/.FYUaSD3l.rzeghMHwQdD6NhaCc2/W','eleve',NULL),(18,'MARLIER','Louca','marlier.louca','$2y$10$FO0cG38xukPwkATzSF08o.xjmvr0bH3IJN2BCUIpb0rFMwfuMwMPm','eleve',NULL),(19,'MARSEILLE','Ethan','marseille.ethan','$2y$10$9SbcMZBE5rEBHUrcox6PF.lXytRcFPAPnJNnn/3MIM.qho.1AvOpO','eleve',NULL),(20,'SEROUX','Louis','seroux.louis','$2y$10$s5uJwok8FifDWoz1blybYOXRx2UrcPxc1KZO8XflnMsbFsPLexKiq','eleve',NULL),(21,'SWIDURSKI','Mathis','swidurski.mathis','$2y$10$qf4rqkBLbU6L7j1S58OP4eTQwIrgM/bzEDyhOYKlkYYhULdIcoyga','eleve',NULL),(22,'THELLIER','Axel','thellier.axel','$2y$10$dO0nOvroLMEB99FtCHygbe5BCZd6J1iahtirUMgXhovNv/P3W2pp6','eleve',NULL),(23,'Dile','Dohe','dile.dohe','$2y$10$M7dAFas4ysyILjGTK/yl9ucb0P8FTmU.7UxkhAkx8/A6R859xLOmy','admin',NULL);
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-12 10:27:35
