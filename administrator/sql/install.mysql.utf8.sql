﻿DROP TABLE IF EXISTS `#__tvm_entry`;
DROP TABLE IF EXISTS `#__tvm_events`; 
DROP TABLE IF EXISTS `#__tvm_categories`; 
 
CREATE TABLE `#__tvm_entry` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `state` tinyint(5) NOT NULL,
  `acknowledged` tinyint(5) DEFAULT NULL,
  `comment` varchar(256) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_by` int(11) DEFAULT NULL,
  `published` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
--
-- Tabellenstruktur für Tabelle `y84ah_tvm_events`
--
CREATE TABLE IF NOT EXISTS `#__tvm_events` (
  `id` int(11) NOT NULL,
  `category` tinyint(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `starttime` time NOT NULL,
  `duration` int(11) NOT NULL,
  `title` varchar(256) DEFAULT NULL,
  `max_users` int(11) DEFAULT NULL,
  `deadline` int(11) DEFAULT NULL,
  `location` varchar(256) DEFAULT NULL,
  `event_comment` varchar(256) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `published` tinyint(4) NOT NULL DEFAULT 1,
  `template` varchar(1) DEFAULT '0',
  `periodic` enum('none','daily','weekly','monthly','yearly') DEFAULT 'none',
  `periodic_value` int(11) DEFAULT NULL,
  `closed` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#__tvm_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `published` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `#__tvm_entry`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `#__tvm_events`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `#__tvm_categories`
  ADD PRIMARY KEY (`id`);