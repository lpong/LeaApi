# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 192.168.66.10 (MySQL 5.7.21)
# Database: api
# Generation Time: 2018-04-03 10:24:56 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table api
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api`;

CREATE TABLE `api` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `project_id` int(11) unsigned NOT NULL DEFAULT '0',
  `category_id` int(11) unsigned NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `method` varchar(20) NOT NULL,
  `url` varchar(128) NOT NULL DEFAULT '',
  `params` json DEFAULT NULL,
  `headers` json DEFAULT NULL,
  `model` varchar(20) DEFAULT NULL,
  `model_header` varchar(128) DEFAULT NULL,
  `formdata` json DEFAULT NULL,
  `urlencoded` json DEFAULT NULL,
  `raw` text,
  `sort` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `remark` text,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table api_read
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_read`;

CREATE TABLE `api_read` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) unsigned NOT NULL,
  `api_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `at_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table api_response
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_response`;

CREATE TABLE `api_response` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `api_id` int(11) unsigned NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `remark` text,
  `body` longtext,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `api_id_index` (`api_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(522) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table msg
# ------------------------------------------------------------

DROP TABLE IF EXISTS `msg`;

CREATE TABLE `msg` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `msg` varchar(255) NOT NULL DEFAULT '',
  `send_id` int(10) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `create_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table project
# ------------------------------------------------------------

DROP TABLE IF EXISTS `project`;

CREATE TABLE `project` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT '0',
  `name` varchar(128) NOT NULL,
  `cover` varchar(128) NOT NULL DEFAULT '',
  `remark` text NOT NULL COMMENT 'api描述',
  `description` varchar(255) DEFAULT NULL COMMENT '项目描述',
  `script` text,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `view` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table project_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `project_user`;

CREATE TABLE `project_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `auth` varchar(20) NOT NULL DEFAULT '' COMMENT 'self 本人发布 write 编写权限  rea读权限',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 不看 1-正常 2-退出 9-邀请中',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(64) NOT NULL DEFAULT '',
  `nickname` varchar(32) NOT NULL DEFAULT '',
  `face` varchar(128) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `login_times` int(11) NOT NULL DEFAULT '0',
  `last_login_ip` varchar(16) DEFAULT NULL,
  `last_login_time` timestamp NULL DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
