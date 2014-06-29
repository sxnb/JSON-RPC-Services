CREATE DATABASE `webservices`;

USE `webservices`;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `note` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `ownerId` int(11) NOT NULL,
 `title` varchar(128) NOT NULL,
 `content` text NOT NULL,
 `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `deleted` tinyint(4) NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`),
 UNIQUE KEY `id` (`id`),
 KEY `note_ibfk_1` (`ownerId`),
 CONSTRAINT `note_ibfk_1` FOREIGN KEY (`ownerId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

CREATE TABLE `token` (
 `userId` int(11) NOT NULL,
 `token` varchar(64) NOT NULL,
 `expirationDate` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE USER 'webServicesUser'@'localhost' IDENTIFIED BY '6CYwh6KFF2TZaVBn';
GRANT SELECT, INSERT, UPDATE, DELETE ON `webservices`.* TO 'webServicesUser'@'localhost';

INSERT INTO `user` (`username`, `password`) VALUES ('test', 'c70c88483aec891b530ca1fedcee9784');
