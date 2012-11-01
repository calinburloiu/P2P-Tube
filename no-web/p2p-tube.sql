-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 01, 2012 at 12:34 PM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-7+squeeze8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `koala_livinglab`
--

-- --------------------------------------------------------

--
-- Table structure for table `captcha`
--

CREATE TABLE IF NOT EXISTS `captcha` (
  `captcha_id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
  `captcha_time` int(10) unsigned NOT NULL,
  `ip_address` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `word` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`captcha_id`),
  KEY `word` (`word`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1504 ;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `user_agent` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE IF NOT EXISTS `test` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`,`body`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `sex` tinyint(1) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `country` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'RO',
  `locality` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ui_lang` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `time_zone` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'UP2',
  `roles` int(16) NOT NULL DEFAULT '0',
  `auth_src` enum('internal','ldap','openid') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'internal',
  `video_prefs` text COLLATE utf8_unicode_ci,
  `registration_date` date NOT NULL,
  `last_login` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=259 ;

-- --------------------------------------------------------

--
-- Table structure for table `users_actions`
--

CREATE TABLE IF NOT EXISTS `users_actions` (
  `user_id` int(11) NOT NULL,
  `action` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `target_type` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `target_id` int(11) NOT NULL,
  `date` date NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `target_id` (`target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_openid`
--

CREATE TABLE IF NOT EXISTS `users_openid` (
  `openid_url` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`openid_url`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_unactivated`
--

CREATE TABLE IF NOT EXISTS `users_unactivated` (
  `user_id` int(11) NOT NULL,
  `activation_code` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE IF NOT EXISTS `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `duration` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `formats` text COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(3) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tags` text COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `likes` int(11) NOT NULL DEFAULT '0',
  `dislikes` int(11) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `thumbs_count` int(2) NOT NULL DEFAULT '4',
  `default_thumb` int(2) NOT NULL DEFAULT '0',
  `license` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Creative Commons Share-Alike License',
  `adult` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`,`description`,`tags`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=221 ;

-- --------------------------------------------------------

--
-- Table structure for table `videos_comments`
--

CREATE TABLE IF NOT EXISTS `videos_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `time` datetime NOT NULL,
  `likes` int(11) NOT NULL DEFAULT '0',
  `dislikes` int(11) NOT NULL DEFAULT '0',
  `abuses` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `video_id` (`video_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `videos_unactivated`
--

CREATE TABLE IF NOT EXISTS `videos_unactivated` (
  `video_id` int(11) NOT NULL,
  `activation_code` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `cis_response` int(2) NOT NULL DEFAULT '0',
  `uploaded_file` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `video_id` (`video_id`),
  KEY `activation_code` (`activation_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
