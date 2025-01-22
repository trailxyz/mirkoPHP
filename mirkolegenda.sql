/*
SQLyog Community v13.2.1 (64 bit)
MySQL - 10.4.28-MariaDB : Database - mirkolegenda
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`mirkolegenda` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `mirkolegenda`;

/*Table structure for table `articles` */

DROP TABLE IF EXISTS `articles`;

CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `articles` */

insert  into `articles`(`id`,`title`,`content`,`author_id`,`created_at`) values 
(2,'test','testis',1,'2025-01-22 20:14:11'),
(3,'bdihdbi','jdkjndjndkjn',4,'2025-01-22 20:40:43'),
(4,'jhhjhj','jfjvgchgchchchc',1,'2025-01-22 23:24:28');

/*Table structure for table `mailing_lists` */

DROP TABLE IF EXISTS `mailing_lists`;

CREATE TABLE `mailing_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mailing_lists` */

insert  into `mailing_lists`(`id`,`email`,`created_at`) values 
(1,'abel@mislovic.com','2025-01-22 20:48:20'),
(2,'test@test.hr','2025-01-22 20:48:28');

/*Table structure for table `page_reviews` */

DROP TABLE IF EXISTS `page_reviews`;

CREATE TABLE `page_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `review` text NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `page_link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `page_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `page_reviews` */

insert  into `page_reviews`(`id`,`user_id`,`review`,`rating`,`created_at`,`page_link`) values 
(1,1,'dsadsa',5,'2025-01-22 20:50:00',NULL),
(2,1,'dsadsadsadsdsadsa',5,'2025-01-22 23:24:56','https://www.mislovic.com');

/*Table structure for table `product_reviews` */

DROP TABLE IF EXISTS `product_reviews`;

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `review` text NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `product_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `product_reviews` */

insert  into `product_reviews`(`id`,`user_id`,`review`,`rating`,`product_link`,`created_at`,`photo`) values 
(9,1,'dsfdfdfds',5,'https://www.samsung.com/hr/smartphones/galaxy-s25-ultra/accessories/','2025-01-22 21:18:07','../ASSets/PRIMJER.png');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`username`,`password`,`email`,`role`,`created_at`) values 
(1,'abel','$2y$10$9GVdCIJ27c8dcO2YDdCz5.db0K5fty4/hi5vn.YvcKu.81ORkZ68m','abel@mislovic.com','admin','2025-01-22 18:28:53'),
(4,'kokakola','$2y$10$j1iItmX3/Wbf5L2H3yL0quksonBiqVgig/QfBm47FmoEqPwAGGZqC','koka@kola.hr','user','2025-01-22 20:00:49'),
(5,'IZBN1195','$2y$10$wbt/F4yNTi9re6dQ7JDuB.c2JCvz6vSEh.QArPSYTBoj1FdMLh.QC','test@test.hr','user','2025-01-22 23:10:35'),
(6,'admin','$2y$10$eO3Ivnllhr7LA2PgF6A/b.dvszDLF00e0zcxyKgZ6lWb63ORxOXru','sbrekalo@mev.hr','admin','2025-01-22 23:47:15');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
