-- MariaDB dump 10.19  Distrib 10.4.27-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: shippingapp
-- ------------------------------------------------------
-- Server version	10.4.27-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `cust_id` int(11) NOT NULL AUTO_INCREMENT,
  `forename` varchar(20) NOT NULL,
  `surname` varchar(20) NOT NULL,
  `town` varchar(20) NOT NULL,
  `eircode` varchar(8) NOT NULL,
  `password` varchar(20) NOT NULL,
  `phone` char(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `cardnumber` char(16) NOT NULL,
  `status` varchar(15) NOT NULL DEFAULT 'Registered',
  `county` varchar(9) NOT NULL,
  PRIMARY KEY (`cust_id`),
  UNIQUE KEY `email` (`email`),
  KEY `county` (`county`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Jameson','Daniel','Tralee','V93 GG84','password','0863445123','daniel.jameson@students.ittralee.ie','4444444444444444','Registered','Kerry'),(2,'Murphy','Patrick','Limerick','C65 VB54','hunter2','0835968214','murphy@gmail.com','1234123412341234','Registered','Limerick'),(3,'O Shea','Jennifer','Cork','P98 TH54','password1','0898234511','jennifer@gmail.com','1111111111111111','Registered','Cork'),(4,'O Connell','Patrick','Dundalk','L34 TH65','randomString','0871212703','OCONNELL@gmail.com','4321432143214321','Registered','Louth'),(5,'McCarthy','Orla','Roscommon','R34 TG84','nopassforyou','0857120341','yancy@gmail.com','4871903265918452','Registered','Carlow'),(6,'Ryan','Thomas','Wexford','V93 AC60','realpassword','0833812639','thomas@gmail.com','9365837501810381','Registered','Leitrim'),(7,'Placeholder','Placeholder','Wexford','D12 RG43','Placeholder','0833812639','Placeholder','9365837501810381','Registered','Leitrim'),(8,'Jack','Jones','Here','There','wadwd345','0882344012','email','7890789078907890','Registered','Leitrim'),(9,'Jesse','James','Tralee','B54 GT33','plPL56%^','0868901314','gmail@msn.com','1234123412341234','Registered','louth'),(10,'pat','plassey','carlow','Q89 RR31','opihn&*56','0878901314','real@gmail.com','6789678967896789','Registered','Kildare'),(11,'jesse','Jackson','Tralee','P87 GM78','oiregorag','0868901314','gmail@gmail.com','4567456745674567','Registered','Monaghan');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `games` (
  `game_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  `developer` varchar(20) NOT NULL,
  `publisher` varchar(20) NOT NULL,
  `genre` varchar(20) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `buyprice` decimal(4,2) NOT NULL,
  `saleprice` decimal(4,2) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `status` varchar(15) NOT NULL DEFAULT 'Registered',
  PRIMARY KEY (`game_id`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`quantity` > 0),
  CONSTRAINT `CONSTRAINT_2` CHECK (`buyprice` > 0),
  CONSTRAINT `CONSTRAINT_3` CHECK (`saleprice` > -0.01)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `games`
--

LOCK TABLES `games` WRITE;
/*!40000 ALTER TABLE `games` DISABLE KEYS */;
INSERT INTO `games` VALUES (1,'title','developer','publisher','this','this is a descriptio',39.50,89.12,5,'Registered'),(2,'doom','idsoftware','bethesda','mayhem','a very good game',35.50,99.50,8,'Registered'),(3,'tit','dev','pub','gen','this shouldn\'t be re',23.40,35.30,7,'Registered'),(4,'Kids Game','Sega','Sega','Platformer','Cinderella rescues Italian plumber!',30.00,60.00,50,'Registered'),(5,'Zelda','Nintendo','Nintendo','Platformer','Little Green man rescues princess!',30.00,60.00,50,'Registered'),(6,'Find Ruler','Rockstar','Rockstar','Puzzle','While under a porch, seek the red graphing tool!',30.00,60.00,50,'Registered');
/*!40000 ALTER TABLE `games` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `order_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  KEY `order_id` (`order_id`),
  KEY `game_id` (`game_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1),(1,2),(1,3),(1,4),(2,1),(2,2),(2,3),(2,4),(3,5),(3,1),(3,2),(3,3),(4,4),(4,5),(4,6),(4,1),(5,4),(5,5),(5,6),(5,1),(6,4),(6,5),(6,6),(6,1),(7,4),(7,5),(7,6),(7,1),(8,4),(8,5),(8,6),(8,1),(9,4),(9,5),(9,6),(9,1),(10,4),(10,5),(10,6),(10,1),(11,4),(11,5),(11,6),(11,1),(12,4),(12,5),(12,6),(12,1),(13,4),(13,5),(13,6),(13,1),(14,4),(14,5),(14,6),(14,1);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_date` date NOT NULL,
  `cost` decimal(6,2) NOT NULL,
  `status` varchar(15) NOT NULL DEFAULT 'Placed',
  `cust_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `cust_id` (`cust_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`cust_id`) REFERENCES `customers` (`cust_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'2023-01-12',30.00,'Placed',1),(2,'2023-02-12',30.00,'Placed',2),(3,'2023-03-12',90.00,'Fulfilled',3),(4,'2023-04-12',120.00,'Returned',4),(5,'2023-05-12',120.00,'Cancelled',7),(6,'2023-06-12',120.00,'Placed',6),(7,'2023-07-12',250.00,'Placed',2),(8,'2023-08-12',5500.00,'Cancelled',7),(9,'2023-09-12',1.00,'Returned',6),(10,'2023-10-12',0.50,'Fulfilled',3),(11,'2023-11-12',3.00,'Cancelled',5),(12,'2023-12-12',80.00,'Fulfilled',1),(13,'2023-03-12',30.00,'In Transit',7),(14,'2023-06-12',80.00,'Fulfilled',7);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-04-18 16:53:40
