CREATE TABLE IF NOT EXISTS `#__etdorganizations_organizations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `catid` int(10) unsigned NOT NULL DEFAULT '0',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `address` mediumtext,
  `suburb` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postcode` varchar(100) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `email_to` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `images` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_catid` (`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__etdorganizations_organization_contacts` (
  `organization_id` int(10) unsigned NOT NULL,
  `contact_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`organization_id`, `contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;