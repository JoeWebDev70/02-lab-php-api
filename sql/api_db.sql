CREATE DATABASE  IF NOT EXISTS `api_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `api_db`;
-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: api_db
-- ------------------------------------------------------
-- Server version	8.1.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Langages de programmation',0),(2,'Frameworks et bibliothèques',0),(3,'Base de données',0),(4,'Développement front end',0),(5,'Développement back end',0),(6,'Sécurité web',0),(7,'Déploiement et gestion de serveurs',0),(8,'Développement mobile et responsive',0),(9,'Outils de développement',0),(10,'Tendances et nouveautés',0),(11,'Communauté et ressources d\'apprentissage',0);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resource`
--

DROP TABLE IF EXISTS `resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource` (
  `id` int NOT NULL AUTO_INCREMENT,
  `url` varchar(512) NOT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  `technology_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_resource_technology_idx` (`technology_id`),
  CONSTRAINT `fk_resource_technology` FOREIGN KEY (`technology_id`) REFERENCES `technology` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resource`
--

LOCK TABLES `resource` WRITE;
/*!40000 ALTER TABLE `resource` DISABLE KEYS */;
INSERT INTO `resource` VALUES (1,'https://developer.mozilla.org/fr/docs/Web/HTML',0,1),(2,'https://developer.mozilla.org/fr/docs/Learn/HTML/Introduction_to_HTML/Getting_started',0,1),(3,'https://www.php.net/manual/fr/intro-whatis.php',0,4),(4,'https://getbootstrap.com/docs/5.3/getting-started/introduction/',0,7),(5,'https://www.mysql.com/fr/',0,5),(6,'https://developer.mozilla.org/fr/docs/Web/JavaScript',0,14),(7,'https://getbootstrap.com/',0,17),(8,'https://developer.mozilla.org/en-US/docs/Learn/CSS/CSS_layout/Responsive_Design',0,18),(9,'https://www.docker.com/products/docker-desktop/',0,22),(10,'https://hub.docker.com/search?q=',0,22),(11,'https://developer.mozilla.org/fr/docs/Web/CSS',0,12);
/*!40000 ALTER TABLE `resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `technology`
--

DROP TABLE IF EXISTS `technology`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `technology` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  `category_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_technology_category_idx` (`category_id`),
  CONSTRAINT `fk_technology_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `technology`
--

LOCK TABLES `technology` WRITE;
/*!40000 ALTER TABLE `technology` DISABLE KEYS */;
INSERT INTO `technology` VALUES (1,'HTML5','http://php-dev-2.online/resources_logo/HTML5_1.png',0,1),(2,'CSS','http://php-dev-2.online/resources_logo/CSS_1.png',0,1),(3,'SQL','',0,1),(4,'PHP','http://php-dev-2.online/resources_logo/PHP_1.png',0,1),(5,'React JS','',0,2),(6,'Angular','',0,2),(7,'Boostrap','http://php-dev-2.online/resources_logo/Boostrap_2.png',0,2),(8,'MySQL','http://php-dev-2.online/resources_logo/MySQL_3.png',0,3),(9,'PostgreSQL','',0,3),(10,'Javascript','http://php-dev-2.online/resources_logo/Javascript_4.png',0,4),(11,'HTML5','http://php-dev-2.online/resources_logo/HTML5_4.png',0,4),(12,'CSS','http://php-dev-2.online/resources_logo/CSS_4.png',0,4),(13,'PHP','http://php-dev-2.online/resources_logo/PHP_5.png',0,5),(14,'Javascript','http://php-dev-2.online/resources_logo/Javascript_5.png',0,5),(15,'Cloud Access Security Brokers','',0,6),(16,'JavaScript','http://php-dev-2.online/resources_logo/JavaScript_8.png',0,8),(17,'Bootstrap','',0,8),(18,'CSS','http://php-dev-2.online/resources_logo/CSS_8.png',0,8),(19,'Scilab','',0,9),(20,'Cake PHP','',0,9),(21,'JQuery','',0,9),(22,'Docker','http://php-dev-2.online/resources_logo/Docker_10.png',0,10),(23,'Metaverse','',0,10),(24,'Stackoverflow','',0,11),(25,'Github','',0,11),(26,'Oracle',NULL,0,7),(27,'Javascript','http://php-dev-2.online/resources_logo/Javascript_1.png',0,1),(31,'MySQL','http://php-dev-2.online/resources_logo/MySQL_5.png',0,5);
/*!40000 ALTER TABLE `technology` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trigger_update_value_deleted` AFTER UPDATE ON `technology` FOR EACH ROW BEGIN
	IF !(NEW.deleted <=> OLD.deleted) THEN
		UPDATE resource SET deleted= NEW.deleted WHERE technology_id= OLD.id;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-10-04 14:12:34
