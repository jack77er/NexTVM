DROP TABLE IF EXISTS `#__tvm_entry`;
DROP TABLE IF EXISTS `#__tvm_events`; 
 
CREATE TABLE `#__tvm_entry` (
	`id`       INT(11) NOT NULL AUTO_INCREMENT,
	`event_id` INT(11) NOT NULL,
	`user_id` INT(11) NOT NULL,
	`state` TINYINT(5) NOT NULL,
	`acknowledged` TINYINT(5), 
	`comment` VARCHAR(256),
	`created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`updated` TIMESTAMP DEFAULT 0,
	`updated_by` INT(11),
	`published` tinyint(4) NOT NULL,
	PRIMARY KEY (`id`)
)
	ENGINE =MyISAM
	AUTO_INCREMENT =0
	DEFAULT CHARSET =utf8;
 

--
-- Tabellenstruktur für Tabelle `y84ah_tvm_events`
--

CREATE TABLE IF NOT EXISTS `#__tvm_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `starttime` time NOT NULL,
  `duration` int(11) NOT NULL,
  `title` varchar(256) DEFAULT NULL,
  `max_users` int(11) DEFAULT NULL,
  `deadline` int(11) DEFAULT NULL,
  `location` varchar(256) DEFAULT NULL,
  `event_comment` varchar(256) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `published` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
 