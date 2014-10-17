delimiter $$

CREATE TABLE `localization_languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(3) COLLATE utf8_bin NOT NULL,
  `name` varchar(100) COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `name_unique` (`name`),
  UNIQUE KEY `full_unique` (`name`,`code`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin$$

CREATE TABLE `localization_message_references` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20) unsigned NOT NULL,
  `file` varchar(200) DEFAULT NULL,
  `line` int(10) unsigned DEFAULT NULL,
  `comment` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `message_id` (`message_id`),
  KEY `file` (`file`),
  KEY `line` (`line`),
  KEY `comment` (`comment`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8$$

CREATE TABLE `localization_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `js` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin$$

CREATE TABLE `localization_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `text` text COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `message_id` (`message_id`),
  KEY `language_id` (`language_id`),
  KEY `not_translated_index` (`message_id`,`language_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin$$
