-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 04, 2013 at 03:08 AM
-- Server version: 5.5.25
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Commontime`
--
CREATE DATABASE `Commontime` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `Commontime`;

-- --------------------------------------------------------

--
-- Table structure for table `CT_FlagHistory`
--

CREATE TABLE IF NOT EXISTS `CT_FlagHistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refScore` int(11) NOT NULL,
  `flagBy` int(11) NOT NULL,
  `description` varchar(4000) DEFAULT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `CT_Genre`
--

CREATE TABLE IF NOT EXISTS `CT_Genre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `genre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `CT_Instrumentation`
--

CREATE TABLE IF NOT EXISTS `CT_Instrumentation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instrumentation` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `CT_Mylist`
--

CREATE TABLE IF NOT EXISTS `CT_Mylist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL COMMENT 'foreign key',
  `title` varchar(50) NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `CT_MylistEntity`
--

CREATE TABLE IF NOT EXISTS `CT_MylistEntity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refScrapbook` int(11) NOT NULL COMMENT 'foreign key',
  `refScore` int(11) NOT NULL COMMENT 'foreign key',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `CT_Score`
--

CREATE TABLE IF NOT EXISTS `CT_Score` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `isFlagged` int(11) DEFAULT '0',
  `isPublic` int(11) NOT NULL DEFAULT '0',
  `genre` varchar(1000) DEFAULT NULL,
  `composer` varchar(200) DEFAULT NULL COMMENT 'foreign key',
  `composeYear` int(11) DEFAULT NULL,
  `publishYear` int(11) DEFAULT NULL,
  `instrumentation` varchar(50) DEFAULT NULL,
  `description` varchar(4000) DEFAULT NULL,
  `downloads` int(11) NOT NULL DEFAULT '0',
  `likes` int(11) DEFAULT '0',
  `likeList` varchar(4000) DEFAULT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `uploadedBy` int(11) NOT NULL DEFAULT '0' COMMENT 'foreign key',
  `language` varchar(45) DEFAULT NULL,
  `opusNum` varchar(45) DEFAULT NULL,
  `key` varchar(45) DEFAULT NULL,
  `style` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `CT_ScoreComment`
--

CREATE TABLE IF NOT EXISTS `CT_ScoreComment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refScore` int(11) NOT NULL,
  `commentBy` int(11) NOT NULL,
  `comment` varchar(4000) NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `CT_ScoreTag`
--

CREATE TABLE IF NOT EXISTS `CT_ScoreTag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refScore` int(11) NOT NULL,
  `tagBy` int(11) NOT NULL,
  `tag` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `CT_SearchLog`
--

CREATE TABLE IF NOT EXISTS `CT_SearchLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'used for tag cloud',
  `searchMode` int(11) NOT NULL,
  `searchTerm` varchar(200) NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `CT_Style`
--

CREATE TABLE IF NOT EXISTS `CT_Style` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `style` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `CT_User`
--

CREATE TABLE IF NOT EXISTS `CT_User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userEmail` varchar(50) NOT NULL,
  `userPw` varchar(1000) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `middleName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '1' COMMENT 'for penalizing & incentivizing purposes',
  `redFlag` int(11) DEFAULT '0',
  `joinDate` timestamp NULL DEFAULT NULL,
  `lastAccess` timestamp NULL DEFAULT NULL,
  `avatarPic` varchar(200) DEFAULT './img/default.png',
  `securityQ` varchar(200) DEFAULT NULL,
  `securityA` varchar(200) CHARACTER SET utf16 DEFAULT NULL,
  `emailSHA` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
