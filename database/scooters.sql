SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `scooters` DEFAULT CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci;
USE `scooters`;

DROP TABLE IF EXISTS `audits`;
CREATE TABLE IF NOT EXISTS `audits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `component` varchar(50) COLLATE utf8mb3_spanish_ci NOT NULL,
  `action` varchar(50) COLLATE utf8mb3_spanish_ci NOT NULL,
  `oldObject` text COLLATE utf8mb3_spanish_ci NOT NULL,
  `newObject` text COLLATE utf8mb3_spanish_ci NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(45) COLLATE utf8mb3_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=483 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;


DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `price` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

DROP TABLE IF EXISTS `partner_payment_iva_rules`;
CREATE TABLE IF NOT EXISTS `partner_payment_iva_rules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `percentage` int NOT NULL,
  `partnerId` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_partner_iva_rules` (`partnerId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

DROP TABLE IF EXISTS `partner_payment_monthly_rules`;
CREATE TABLE IF NOT EXISTS `partner_payment_monthly_rules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `amount` double NOT NULL,
  `months` int NOT NULL,
  `partnerId` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_partner_monthly_rules` (`partnerId`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;


DROP TABLE IF EXISTS `partner_payment_swap_rules`;
CREATE TABLE IF NOT EXISTS `partner_payment_swap_rules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `amount` double NOT NULL,
  `partnerId` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_partner_swap_rules` (`partnerId`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tenantId` int DEFAULT NULL,
  `amount` float NOT NULL,
  `swap_amount` float DEFAULT NULL,
  `monthly_amount` float NOT NULL,
  `iva_amount` float NOT NULL,
  `final_amount` float NOT NULL,
  `invoice` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `dateCreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `partnerId` int DEFAULT NULL,
  `partner_amount` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tenant` (`tenantId`),
  KEY `fk_partner_payment` (`partnerId`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

DROP TABLE IF EXISTS `scooters`;
CREATE TABLE IF NOT EXISTS `scooters` (
  `id` int NOT NULL AUTO_INCREMENT,
  `scooterNumber` varchar(50) NOT NULL,
  `categoryId` int NOT NULL,
  `description` text NOT NULL,
  `price` double NOT NULL,
  `stateId` int NOT NULL,
  `partnerId` int DEFAULT NULL,
  `field1` varchar(255) NULL,
  `field2` varchar(255) NULL,
  `field3` varchar(255) NULL,
  `field4` varchar(255) NULL,
  `field5` varchar(255) NULL,
  `field6` varchar(255) NULL,
  `field7` varchar(255) NULL,
  `field8` varchar(255) NULL,
  `field9` varchar(255) NULL,
  `field10` varchar(255) NULL,
  `field11` varchar(255) NULL,
  `field12` varchar(255) NULL,
  `field13` varchar(255) NULL,
  `field14` varchar(255) NULL,
  `field15` varchar(255) NULL,
  `field16` varchar(255) NULL,
  `field17` varchar(255) NULL,
  `field18` varchar(255) NULL,
  `field19` varchar(255) NULL,
  `field20` varchar(255) NULL,
  `field21` varchar(255) NULL,
  `field22` varchar(255) NULL,
  `field23` varchar(255) NULL,
  `field24` varchar(255) NULL,
  `field25` varchar(255) NULL,
  `field26` varchar(255) NULL,
  `field27` varchar(255) NULL,
  `field28` varchar(255) NULL,
  `field29` varchar(255) NULL,
  `field30` varchar(255) NULL,
  PRIMARY KEY (`id`),
  KEY `fk_category` (`categoryId`),
  KEY `fk_state` (`stateId`),
  KEY `fk_scooter_partner` (`partnerId`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

DROP TABLE IF EXISTS `scooter_audits`;
CREATE TABLE IF NOT EXISTS `scooter_audits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `scooterId` int NOT NULL,
  `action` varchar(50) COLLATE utf8mb3_spanish_ci NOT NULL,
  `oldObject` text CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci,
  `newObject` text COLLATE utf8mb3_spanish_ci NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

DROP TABLE IF EXISTS `scooter_incidents`;
CREATE TABLE IF NOT EXISTS `scooter_incidents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `description` varchar(100) COLLATE utf8mb3_spanish_ci NOT NULL,
  `scooterNumber` varchar(50) COLLATE utf8mb3_spanish_ci NOT NULL,
  `scooterId` int NOT NULL,
  `createdAd` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` varchar(45) COLLATE utf8mb3_spanish_ci NOT NULL,
  `updatedBy` varchar(45) COLLATE utf8mb3_spanish_ci NOT NULL,
  `resolved` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = resolved , 0 = no unresolved',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

DROP TABLE IF EXISTS `states`;
CREATE TABLE IF NOT EXISTS `states` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb3_spanish_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb3_spanish_ci NOT NULL,
  `createdAd` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` varchar(45) COLLATE utf8mb3_spanish_ci NOT NULL,
  `updatedBy` varchar(45) COLLATE utf8mb3_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `coverImg` text NOT NULL,
  `aboutContent` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

INSERT INTO `system_settings` (`id`, `name`, `email`, `contact`, `coverImg`, `aboutContent`) VALUES
(1, 'Scooter Rental Management System', 'email@email.com', '+55555555555', 'scooter.jpg', '&lt;p style=&quot;text-align: center; background: transparent; position: relative;&quot;&gt;&lt;span style=&quot;color: rgb(0, 0, 0); font-family: &amp;quot;Open Sans&amp;quot;, Arial, sans-serif; font-weight: 400; text-align: justify;&quot;&gt;&amp;nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&rsquo;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.&lt;/span&gt;&lt;br&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center; background: transparent; position: relative;&quot;&gt;&lt;br&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center; background: transparent; position: relative;&quot;&gt;&lt;br&gt;&lt;/p&gt;&lt;p&gt;&lt;/p&gt;');

DROP TABLE IF EXISTS `tenants`;
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstName` varchar(100) NOT NULL,
  `middleName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `scooterId` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1 = active, 0= inactive',
  `dateIn` date NOT NULL,
  `dateFinish` date NULL,
  `partnerId` int DEFAULT NULL,
  `price` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_scooter` (`scooterId`),
  KEY `fk_users` (`partnerId`)
) ENGINE=InnoDB AUTO_INCREMENT=233 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1=Admin,2=Partner',
  `dateCreated` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

INSERT INTO `users` (`id`, `name`, `username`, `password`, `type`, `dateCreated`) VALUES
(1, 'Administrator', 'admin', '6b43b1a712f050920e8f02c89323d707', 1, '2023-01-28 20:05:34');

ALTER TABLE `payments`
  ADD CONSTRAINT `fk_partner_payment` FOREIGN KEY (`partnerId`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_tenant` FOREIGN KEY (`tenantId`) REFERENCES `tenants` (`id`);

ALTER TABLE `scooters`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_scooter_partner` FOREIGN KEY (`partnerId`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_state` FOREIGN KEY (`stateId`) REFERENCES `states` (`id`);

ALTER TABLE `tenants`
  ADD CONSTRAINT `fk_scooter` FOREIGN KEY (`scooterId`) REFERENCES `scooters` (`id`),
  ADD CONSTRAINT `fk_users` FOREIGN KEY (`partnerId`) REFERENCES `users` (`id`);
COMMIT;