DROP TABLE IF EXISTS `customers`;

CREATE TABLE `customers` (
  `cust_id` int(5) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `town` varchar(20) NOT NULL,
  `eircode` varchar(8) NOT NULL,
  `password` varchar(20) NOT NULL,
  `phone` char(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `cardnumber` char(16) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'R',
  `county` varchar(9) NOT NULL,
  PRIMARY KEY (`cust_id`),
  UNIQUE KEY `email` (`email`),
  KEY `county` (`county`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `customers` WRITE;

INSERT INTO `customers` VALUES 
(1,'DanielUser','Tralee','V93 GG84','1119J7!\"£','0861234123','eyyylamao@students.ittralee.ie','1111111111111111','D','Carlow'),
(2,'P.Rick','Limerick','C65 VB54','hunter2','0835968214','murphy@gmail.com','1234123412341234','R','Limerick'),
(3,'Jenosh','Cork','P98 TH54','password1','0898234511','jennifer@gmail.com','1111111111111111','R','Cork'),
(4,'Patsy','Dundalk','L34 TH65','randomString','0871212703','OCONNELL@gmail.com','4321432143214321','R','Louth'),
(5,'Bridie','Roscommon','R34 TG84','nopassforyou','0857120341','yancy@gmail.com','4871903265918452','R','Carlow'),
(6,'Ryomas','Wexford','V93 AC60','realpassword','0833812639','thomas@gmail.com','9365837501810381','R','Leitrim'),
(7,'Placeholder','Wexford','D12 RG43','Placeholder','0833812639','Placeholder','9365837501810381','R','Leitrim'),
(8,'WeNeedToCookJesse','Here','There','wadwd345','0882344012','email','7890789078907890','R','Leitrim'),
(9,'Jessa','Tralee','B54 GT33','plPL56%^','0868901314','gmail@msn.com','1234123412341234','R','Louth'),
(10,'Pat Lash','carlow','Q89 RR31','opihn&*56','0878901314','real@gmail.com','6789678967896789','R','Kildare'),
(11,'Jekson','Tralee','P87 GM78','oiregorag','0868901314','gmail@gmail.com','4567456745674567','R','Monaghan'),
(14,'kealy','Bantry','P19 MM54','password','0868901314','email@email.com','4321432143214321','R','Carlow');
UNLOCK TABLES;

DROP TABLE IF EXISTS `games`;
CREATE TABLE `games` (
  `game_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  `developer` varchar(20) NOT NULL,
  `genre` varchar(20) DEFAULT NULL,
  `saleprice` decimal(4,2) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'R',
  PRIMARY KEY (`game_id`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`quantity` > 0),
  CONSTRAINT `CONSTRAINT_2` CHECK (`saleprice` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `games` WRITE;
INSERT INTO `games` VALUES 
(1,'title','developer','this',89.12,5,'R'),
(2,'doom','idsoftware','mayhem',99.50,8,'R'),
(3,'tit','dev','this shouldnt be',35.30,7,'R'),
(4,'Kids Game','Sega','Platformer',60.00,50,'R'),
(5,'Zelda','Nintendo','Platformer',60.00,50,'R'),
(6,'Find Ruler','Rockstar','Puzzle',60.00,50,'R');
UNLOCK TABLES;

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_date` date NOT NULL,
  `cost` decimal(6,2) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'P',
  `cust_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `cust_id` (`cust_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`cust_id`) REFERENCES `customers` (`cust_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `orders` WRITE;
INSERT INTO `orders` VALUES 
(1,'2023-01-12',30.00,'P',1),
(2,'2023-02-12',30.00,'P',2),
(3,'2023-03-12',90.00,'F',3),
(4,'2023-04-12',120.00,'R',4),
(5,'2023-05-12',120.00,'C',7),
(6,'2023-06-12',120.00,'P',6),
(7,'2023-07-12',250.00,'P',2),
(8,'2023-08-12',5500.00,'C',7),
(9,'2023-09-12',1.00,'R',6),
(10,'2023-10-12',0.50,'F',3),
(11,'2023-11-12',3.00,'C',5),
(12,'2023-12-12',80.00,'F',1),
(13,'2023-03-12',30.00,'T',7),
(14,'2023-06-12',80.00,'F',7);
UNLOCK TABLES;

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `order_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  KEY `order_id` (`order_id`),
  KEY `game_id` (`game_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `order_items` WRITE;
INSERT INTO `order_items` VALUES 
(1,1),
(1,2),
(1,3),
(1,4),
(2,1),
(2,2),
(2,3),
(2,4),
(3,5),
(3,1),
(3,2),
(3,3),
(4,4),
(4,5),
(4,6),
(4,1),
(5,4),
(5,5),
(5,6),
(5,1),
(6,4),
(6,5),
(6,6),
(6,1),
(7,4),
(7,5),
(7,6),
(7,1),
(8,4),
(8,5),
(8,6),
(8,1),
(9,4),
(9,5),
(9,6),
(9,1),
(10,4),
(10,5),
(10,6),
(10,1),
(11,4),
(11,5),
(11,6),
(11,1),
(12,4),
(12,5),
(12,6),
(12,1),
(13,4),
(13,5),
(13,6),
(13,1),
(14,4),
(14,5),
(14,6),
(14,1);
UNLOCK TABLES;