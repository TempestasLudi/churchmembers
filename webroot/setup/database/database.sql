-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 02 nov 2012 om 14:00
-- Serverversie: 5.5.24-log
-- PHP-versie: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databank: `cexport`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `addresses`
--

CREATE TABLE IF NOT EXISTS `addresses` (
  `ADR_id` int(11) NOT NULL AUTO_INCREMENT,
  `ADR_familyname` text COLLATE utf8_bin NOT NULL,
  `ADR_familyname_preposition` text COLLATE utf8_bin NOT NULL,
  `ADR_street` text COLLATE utf8_bin NOT NULL,
  `ADR_number` text COLLATE utf8_bin NOT NULL,
  `ADR_street_extra` text COLLATE utf8_bin NOT NULL,
  `ADR_zip` text COLLATE utf8_bin NOT NULL,
  `ADR_city` text COLLATE utf8_bin NOT NULL,
  `ADR_country` text COLLATE utf8_bin NOT NULL,
  `ADR_telephone` text COLLATE utf8_bin NOT NULL,
  `ADR_email` text COLLATE utf8_bin NOT NULL,
  `ADR_archive` int(1) NOT NULL DEFAULT '0',
  `ADR_lat` decimal(10,7) NOT NULL DEFAULT '0.0000000',
  `ADR_lng` decimal(10,7) NOT NULL DEFAULT '0.0000000',
  PRIMARY KEY (`ADR_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2 ;

--
-- Gegevens worden uitgevoerd voor tabel `addresses`
--

INSERT INTO `addresses` (`ADR_id`, `ADR_familyname`, `ADR_familyname_preposition`, `ADR_street`, `ADR_number`, `ADR_street_extra`, `ADR_zip`, `ADR_city`, `ADR_country`, `ADR_telephone`, `ADR_email`, `ADR_archive`, `ADR_lat`, `ADR_lng`) VALUES
(1, '.Archief adres', '', '', '', '', '', '', '', '', '', 1, '0.0000000', '0.0000000');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `EVENT_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `EVENT_parent_id` smallint(6) NOT NULL,
  `EVENT_MEMBER_id` smallint(6) NOT NULL,
  `EVENT_MEMBER_adr_id` int(11) NOT NULL,
  `EVENT_MEMBER_fullname` text COLLATE utf8_bin NOT NULL,
  `EVENT_MEMBER_address` text COLLATE utf8_bin NOT NULL,
  `EVENTTYPE_id` smallint(6) NOT NULL,
  `EVENT_date` date NOT NULL,
  `EVENT_note` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`EVENT_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `eventtypes`
--

CREATE TABLE IF NOT EXISTS `eventtypes` (
  `EVENTTYPE_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `EVENTTYPE_name` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`EVENTTYPE_id`),
  UNIQUE KEY `RELATIONTYPE_name` (`EVENTTYPE_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=37 ;

--
-- Gegevens worden uitgevoerd voor tabel `eventtypes`
--

INSERT INTO `eventtypes` (`EVENTTYPE_id`, `EVENTTYPE_name`) VALUES
(3, 'EVENT_SICK'),
(2, 'EVENT_BIRTH'),
(4, 'EVENT_DIED'),
(5, 'EVENT_ADD_TRAVEL_TESTIMONY'),
(6, 'EVENT_ADD_STAY_TESTIMONY'),
(7, 'EVENT_ADD_CONFESSION_TESTIMONY'),
(8, 'EVENT_ADD_GUESTMEMBERSHIP'),
(9, 'EVENT_GONE'),
(10, 'EVENT_MOVED'),
(11, 'EVENT_MARRIAGE'),
(12, 'EVENT_DIVORCE'),
(13, 'EVENT_ADD_MEMBERSHIP'),
(15, 'EVENT_CONTINUE_GUESTMEMBERSHIP'),
(16, 'EVENT_CONTINUE_STAY_TESTIMONY'),
(17, 'EVENT_MOVED_TRAVEL_TESTIMONY'),
(18, 'EVENT_MOVED_STAY_TESTIMONY'),
(19, 'EVENT_MOVED_CONFESSION_TESTIMONY'),
(20, 'EVENT_MOVED_GUESTMEMBERSHIP'),
(21, 'EVENT_CONTINUE_TRAVEL_TESTIMONY'),
(26, 'EVENT_CHANGED_EMAIL'),
(23, 'EVENT_END_GUESTMEMBERSHIP'),
(24, 'EVENT_BAPTISED'),
(25, 'EVENT_CONFESSION'),
(27, 'EVENT_CHANGED_PHONE'),
(29, 'EVENT_MOVED_BIRTH_TESTIMONY'),
(30, 'EVENT_ADD_BIRTH_TESTIMONY'),
(31, 'EVENT_CHANGED_BUSINESS_EMAIL'),
(32, 'EVENT_CHANGED_BUSINESS_PHONE'),
(33, 'EVENT_MOVED_ABROAD'),
(34, 'EVENT_CHANGED_HOME_EMAIL'),
(35, 'EVENT_CHANGED_HOME_PHONE'),
(36, 'EVENT_ADD_NEWMEMBER');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `failedaccess`
--

CREATE TABLE IF NOT EXISTS `failedaccess` (
  `FAILEDACCESS_id` int(11) NOT NULL AUTO_INCREMENT,
  `FAILEDACCESS_ip` varchar(40) COLLATE utf8_bin NOT NULL,
  `FAILEDACCESS_loginname` varchar(40) COLLATE utf8_bin NOT NULL,
  `FAILEDACCESS_pass` blob NOT NULL,
  `FAILEDACCESS_timestamp` bigint(25) NOT NULL,
  PRIMARY KEY (`FAILEDACCESS_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `groupaddresses`
--

CREATE TABLE IF NOT EXISTS `groupaddresses` (
  `GROUPADDRESSES_id` int(10) NOT NULL AUTO_INCREMENT,
  `GROUPADDRESSES_groupid` int(5) NOT NULL,
  `GROUPADDRESSES_addressid` int(5) NOT NULL,
  PRIMARY KEY (`GROUPADDRESSES_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `groupmembers`
--

CREATE TABLE IF NOT EXISTS `groupmembers` (
  `GROUPMEMBERS_id` int(10) NOT NULL AUTO_INCREMENT,
  `GROUPMEMBERS_groupid` int(5) NOT NULL,
  `GROUPMEMBERS_memberid` int(5) NOT NULL,
  PRIMARY KEY (`GROUPMEMBERS_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `GROUP_id` int(11) NOT NULL AUTO_INCREMENT,
  `GROUP_parent_id` int(11) NOT NULL DEFAULT '0',
  `GROUP_name` text COLLATE utf8_bin NOT NULL,
  `GROUP_description` text COLLATE utf8_bin NOT NULL,
  `GROUP_abbreviation` tinytext COLLATE utf8_bin NOT NULL,
  `GROUP_type` text COLLATE utf8_bin NOT NULL,
  `GROUP_inyearbook` tinyint(1) NOT NULL DEFAULT '0',
  `GROUP_onmap` tinyint(1) NOT NULL DEFAULT '0',
  `GROUP_marker` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`GROUP_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Gegevens worden uitgevoerd voor tabel `groups`
--

INSERT INTO `groups` (`GROUP_id`, `GROUP_parent_id`, `GROUP_name`, `GROUP_description`, `GROUP_abbreviation`, `GROUP_type`, `GROUP_inyearbook`, `GROUP_onmap`, `GROUP_marker`) VALUES
(1, 1, 'Root', '', '', '', 0, 0, '');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `MEMBER_id` int(11) NOT NULL AUTO_INCREMENT,
  `MEMBER_familyname` text COLLATE utf8_bin NOT NULL,
  `MEMBER_familyname_preposition` text COLLATE utf8_bin NOT NULL,
  `MEMBER_initials` text COLLATE utf8_bin NOT NULL,
  `MEMBER_firstname` text COLLATE utf8_bin NOT NULL,
  `MEMBER_christianname` text COLLATE utf8_bin NOT NULL,
  `MEMBER_gender` text COLLATE utf8_bin NOT NULL,
  `MEMBER_membertype_id` tinyint(3) NOT NULL,
  `MEMBER_mobilephone` text COLLATE utf8_bin NOT NULL,
  `MEMBER_email` text COLLATE utf8_bin NOT NULL,
  `MEMBER_business_email` text COLLATE utf8_bin NOT NULL,
  `MEMBER_photo` text COLLATE utf8_bin NOT NULL,
  `MEMBER_business_phone` text COLLATE utf8_bin NOT NULL,
  `MEMBER_notes` text COLLATE utf8_bin NOT NULL,
  `MEMBER_introduction` text COLLATE utf8_bin NOT NULL,
  `MEMBER_birthdate` date DEFAULT NULL,
  `MEMBER_birthplace` text COLLATE utf8_bin NOT NULL,
  `MEMBER_baptismdate` date DEFAULT NULL,
  `MEMBER_baptismcity` text COLLATE utf8_bin NOT NULL,
  `MEMBER_baptismchurch` text COLLATE utf8_bin NOT NULL,
  `MEMBER_confessiondate` date DEFAULT NULL,
  `MEMBER_confessioncity` text COLLATE utf8_bin NOT NULL,
  `MEMBER_confessionchurch` text COLLATE utf8_bin NOT NULL,
  `MEMBER_mariagedate` date DEFAULT NULL,
  `MEMBER_mariagecity` text COLLATE utf8_bin NOT NULL,
  `MEMBER_mariagechurch` text COLLATE utf8_bin NOT NULL,
  `ADR_id` int(11) NOT NULL,
  `MEMBER_parent` tinyint(1) NOT NULL COMMENT 'Is parent/married',
  `MEMBER_rank` tinyint(2) NOT NULL,
  `MEMBER_inyearbook` tinyint(1) NOT NULL DEFAULT '1',
  `MEMBER_birthdateview` tinyint(1) NOT NULL DEFAULT '1',
  `MEMBER_familynameview` tinyint(1) NOT NULL DEFAULT '0',
  `MEMBER_archive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`MEMBER_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `membertypes`
--

CREATE TABLE IF NOT EXISTS `membertypes` (
  `MEMBERTYPE_id` int(11) NOT NULL AUTO_INCREMENT,
  `MEMBERTYPE_name` text COLLATE utf8_bin NOT NULL,
  `MEMBERTYPE_abbreviation` tinytext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`MEMBERTYPE_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=10 ;

--
-- Gegevens worden uitgevoerd voor tabel `membertypes`
--

INSERT INTO `membertypes` (`MEMBERTYPE_id`, `MEMBERTYPE_name`, `MEMBERTYPE_abbreviation`) VALUES
(1, 'Dooplid', 'D'),
(2, 'Belijdend lid', 'B'),
(3, 'Gastdooplid', 'GD'),
(4, 'Gast belijdend lid', 'GB'),
(5, 'Geen lid -20 jr', '--'),
(6, 'Geen lid +20 jr', '--'),
(7, 'Catechumeen', 'C'),
(9, 'Niet ingedeeld', '--');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `SETTINGS_id` int(11) NOT NULL AUTO_INCREMENT,
  `SETTINGS_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `SETTINGS_value` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`SETTINGS_id`),
  UNIQUE KEY `SETTINGS_name` (`SETTINGS_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=34 ;

--
-- Gegevens worden uitgevoerd voor tabel `settings`
--

INSERT INTO `settings` (`SETTINGS_id`, `SETTINGS_name`, `SETTINGS_value`) VALUES
(2, 'systemname', 'ChurchMembers'),
(3, 'cookie_name', 'ChurchMembers'),
(4, 'login_maxattempts', '5'),
(7, 'locale_officecode', ''),
(8, 'locale_officecode_visible', 'false'),
(9, 'export_docraptor_key', ''),
(11, 'google_analytics_accountid', ''),
(12, 'system_secure', 'false'),
(13, 'cookie_domain', ''),
(14, 'auth_enabled', 'false'),
(15, 'auth_validationurl', ''),
(16, 'auth_loginurl', ''),
(17, 'auth_logouturl', ''),
(18, 'system_secure_port', '443'),
(19, 'export_docraptor_enabled', 'false'),
(20, 'system_version', '1.4.296'),
(21, 'google_analytics_domainname', 'none'),
(24, 'login_mail', 'false'),
(25, 'administrator_email', ''),
(23, 'maintenance', 'false'),
(26, 'map_externaljson', ''),
(27, 'mail_subject', ''),
(28, 'smtp_username', ''),
(29, 'smtp_password', ''),
(30, 'smtp_ssl', ''),
(31, 'smtp_port', ''),
(32, 'smtp_host', ''),
(33, 'mail_use', 'false');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `USER_id` int(5) NOT NULL AUTO_INCREMENT,
  `USER_username` varchar(50) COLLATE utf8_bin NOT NULL,
  `USER_password` blob NOT NULL,
  `USERTYPE_id` int(3) NOT NULL DEFAULT '999',
  PRIMARY KEY (`USER_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `usertypes`
--

CREATE TABLE IF NOT EXISTS `usertypes` (
  `USERTYPE_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `USERTYPE_name` varchar(100) COLLATE utf8_bin NOT NULL,
  `USERTYPE_description` varchar(255) COLLATE utf8_bin NOT NULL,
  `USERTYPE_rights` text COLLATE utf8_bin NOT NULL,
  `USERTYPE_template` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT 'default',
  PRIMARY KEY (`USERTYPE_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1000 ;

--
-- Gegevens worden uitgevoerd voor tabel `usertypes`
--

INSERT INTO `usertypes` (`USERTYPE_id`, `USERTYPE_name`, `USERTYPE_description`, `USERTYPE_rights`, `USERTYPE_template`) VALUES
(1, 'Administrator', 'Admin, full rights, also adjust churchmembers application.', '{"view_address":1,"view_groups":1,"view_mutations":1,"view_report":1,"view_map":1,"view_archive":1,"edit_mode":1,"add_address":1,"add_group":1,"add_member":1,"add_relation":1,"add_data":1, "send_mail":1, "view_admin":1,"sort_members":1,"delete_data":1}', 'editor'),
(10, 'Editor', 'Editor, advanced user, can see all info, can edit the administration and generate custom reports', '{"view_address":1,"view_groups":1,"view_mutations":1,"view_report":1,"view_map":1,"view_archive":1,"edit_mode":1,"add_address":1,"add_group":1,"add_member":1,"add_relation":1,"add_data":1, "send_mail":1, "view_admin":0,"sort_members":1,"delete_data":1}', 'editor'),
(999, 'Member', 'Member can only read data, no hidden data', '{"blank":0,"view_address":1,"edit_mode":0,"view_groups":1,"add_address":0,"view_mutations":0,"add_group":0,"view_report":1,"add_member":0,"view_map":1,"add_relation":0,"view_archive":0,"add_data":0,"send_mail":1,"view_admin":0,"sort_members":0,"delete_data":0}', 'default');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
