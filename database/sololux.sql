-- Adminer 4.7.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `resource_categories`;
CREATE TABLE `resource_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(299) NOT NULL,
  `is_active` enum('Y','N') NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` varchar(199) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `resource_images`;
CREATE TABLE `resource_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(10) unsigned NOT NULL,
  `url` longtext,
  `description` longtext,
  `image1` longtext,
  `image2` longtext,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2019-07-11 12:51:29
