-- database dump for: bunzilla 0.2b
-- tracked by Bunzilla v0.2b
-- 2015-10-15 09:36:44pm

CREATE DATABASE IF NOT EXISTS `bunzilla`;
USE `bunzilla`;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` tinyint(4) NOT NULL,
  `reproduce` tinyint(4) NOT NULL,
  `expected` tinyint(4) NOT NULL,
  `actual` tinyint(4) NOT NULL,
  `color` char(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `change_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `version` (`version`),
  FULLTEXT KEY `message` (`message`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report` int(10) unsigned NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `epenis` tinyint(1) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `edit_time` int(10) unsigned DEFAULT NULL,
  `reply_to` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report` (`report`),
  FULLTEXT KEY `message` (`message`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `priorities` (
  `id` tinyint(3) unsigned NOT NULL,
  `title` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` char(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `category` int(10) unsigned NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `reproduce` text COLLATE utf8mb4_unicode_ci,
  `expected` text COLLATE utf8mb4_unicode_ci,
  `actual` text COLLATE utf8mb4_unicode_ci,
  `closed` tinyint(1) NOT NULL,
  `epenis` tinyint(1) NOT NULL,
  `edit_time` int(10) unsigned DEFAULT NULL,
  `status` int(10) unsigned DEFAULT NULL,
  `priority` tinyint(3) unsigned NOT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `status` (`status`),
  FULLTEXT KEY `subject` (`subject`),
  FULLTEXT KEY `description` (`description`),
  FULLTEXT KEY `reproduce` (`reproduce`),
  FULLTEXT KEY `expected` (`expected`),
  FULLTEXT KEY `actual` (`actual`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `status_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report` int(10) unsigned DEFAULT NULL,
  `category` int(10) unsigned DEFAULT NULL,
  `priority` tinyint(3) unsigned DEFAULT NULL,
  `status` int(10) unsigned DEFAULT NULL,
  `tag` int(10) unsigned DEFAULT NULL,
  `who` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `report` (`report`),
  KEY `category` (`category`),
  KEY `priority` (`priority`),
  KEY `status` (`status`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` char(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tag_joins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report` int(10) unsigned NOT NULL DEFAULT '0',
  `tag` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `report` (`report`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` char(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- executed in 0.045275926589966s