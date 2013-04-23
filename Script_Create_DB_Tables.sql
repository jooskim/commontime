-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 22, 2013 at 07:40 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `CT_FlagHistory`
--

DROP TABLE IF EXISTS `CT_FlagHistory`;
CREATE TABLE `CT_FlagHistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refScore` int(11) NOT NULL,
  `flagBy` int(11) NOT NULL,
  `description` varchar(4000) DEFAULT NULL,
  `isResolved` int(11) NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `CT_FlagHistory`
--

INSERT INTO `CT_FlagHistory` (`id`, `refScore`, `flagBy`, `description`, `isResolved`, `timestamp`) VALUES
(2, 2, 1, 'asdfjkhawleiufhalwieufhawliue', 0, 1366050223);

-- --------------------------------------------------------

--
-- Table structure for table `CT_Friends`
--

DROP TABLE IF EXISTS `CT_Friends`;
CREATE TABLE `CT_Friends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refUser` int(11) DEFAULT NULL,
  `targetUser` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

--
-- Dumping data for table `CT_Friends`
--

INSERT INTO `CT_Friends` (`id`, `refUser`, `targetUser`) VALUES
(25, 13, 1),
(27, 13, 15),
(35, 1, 13),
(37, 1, 15);

-- --------------------------------------------------------

--
-- Table structure for table `CT_Genre`
--

DROP TABLE IF EXISTS `CT_Genre`;
CREATE TABLE `CT_Genre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refScore` int(11) DEFAULT NULL,
  `genre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `CT_Genre`
--

INSERT INTO `CT_Genre` (`id`, `refScore`, `genre`) VALUES
(1, 1, 'Suites'),
(2, 1, 'For strings'),
(3, 1, 'Scores featuring string ensemble'),
(4, 3, 'Variations for guitar');

-- --------------------------------------------------------

--
-- Table structure for table `CT_Instrumentation`
--

DROP TABLE IF EXISTS `CT_Instrumentation`;
CREATE TABLE `CT_Instrumentation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refScore` int(11) DEFAULT NULL,
  `instrumentation` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `CT_Instrumentation`
--

INSERT INTO `CT_Instrumentation` (`id`, `refScore`, `instrumentation`) VALUES
(1, 1, 'Violins'),
(2, 1, 'Violas'),
(3, 1, 'Cellos'),
(4, 1, 'Bass'),
(5, 2, 'Guitar'),
(6, 3, 'Guitar');

-- --------------------------------------------------------

--
-- Table structure for table `CT_Mylist`
--

DROP TABLE IF EXISTS `CT_Mylist`;
CREATE TABLE `CT_Mylist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL COMMENT 'foreign key',
  `title` varchar(50) NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `CT_Mylist`
--

INSERT INTO `CT_Mylist` (`id`, `creator`, `title`, `timestamp`) VALUES
(4, 1, 'default mylist', 1366264770),
(5, 18, 'default mylist', 1366304546),
(6, 19, 'default mylist', 1366328303),
(7, 13, 'default mylist', 1366391891);

-- --------------------------------------------------------

--
-- Table structure for table `CT_MylistEntity`
--

DROP TABLE IF EXISTS `CT_MylistEntity`;
CREATE TABLE `CT_MylistEntity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refScrapbook` int(11) NOT NULL COMMENT 'foreign key',
  `refScore` int(11) NOT NULL COMMENT 'foreign key',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `CT_MylistEntity`
--

INSERT INTO `CT_MylistEntity` (`id`, `refScrapbook`, `refScore`, `timestamp`) VALUES
(6, 4, 1, 1366266060),
(9, 5, 3, 1366304597),
(8, 4, 3, 1366266102);

-- --------------------------------------------------------

--
-- Table structure for table `CT_Score`
--

DROP TABLE IF EXISTS `CT_Score`;
CREATE TABLE `CT_Score` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `isFlagged` int(11) DEFAULT '0',
  `isPublic` int(11) NOT NULL DEFAULT '0',
  `composer` varchar(200) DEFAULT NULL COMMENT 'foreign key',
  `composeYear` int(11) DEFAULT NULL,
  `publishYear` int(11) DEFAULT NULL,
  `description` varchar(4000) DEFAULT NULL,
  `downloadLink` varchar(4000) DEFAULT 'assets/scores/sample.jpg',
  `downloads` int(11) NOT NULL DEFAULT '0',
  `likes` int(11) DEFAULT '0',
  `likeList` varchar(4000) DEFAULT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `uploadedBy` int(11) NOT NULL DEFAULT '0' COMMENT 'foreign key',
  `language` varchar(45) DEFAULT NULL,
  `opusNum` varchar(45) DEFAULT NULL,
  `key` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `CT_Score`
--

INSERT INTO `CT_Score` (`id`, `title`, `isFlagged`, `isPublic`, `composer`, `composeYear`, `publishYear`, `description`, `downloadLink`, `downloads`, `likes`, `likeList`, `timestamp`, `uploadedBy`, `language`, `opusNum`, `key`) VALUES
(1, 'Suite for String Orchestra, Op.63', 0, 1, 'Arthur P. Schmidt', 1909, 1909, '3 movments Praeludium. Pizzicato and Adagietto. Fuge', 'assets/scores/sample.jpg', 5, 3, '', 20130211, 1, 'German', 'Op.63', '12'),
(2, 'Caazapa (Barrios Mangore, Agustin)', 1, 1, 'Barrios Mangore, Agustin', 1952, 1979, '4 pieces: Primavera Junto a tu corazon in D minor in G major', 'assets/scores/sample.jpg', 12, 3, '', 20130301, 15, 'English', 'Op.8', '2'),
(3, 'Au Clair de la Lune, chante dans Les Voitures Versees, varie, Op.7', 0, 1, 'Carcassi, Matteo', 1823, 1827, 'Anvers: A. Schott, n.d., plate 2555', 'assets/scores/sample.jpg', 32, 2, '', 20130315, 13, 'German', 'Op.7', '2');

-- --------------------------------------------------------

--
-- Table structure for table `CT_ScoreComment`
--

DROP TABLE IF EXISTS `CT_ScoreComment`;
CREATE TABLE `CT_ScoreComment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refScore` int(11) NOT NULL,
  `commentBy` int(11) NOT NULL,
  `comment` varchar(4000) NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `CT_ScoreComment`
--

INSERT INTO `CT_ScoreComment` (`id`, `refScore`, `commentBy`, `comment`, `timestamp`) VALUES
(1, 1, 1, 'hi!', 1365989455),
(2, 2, 1, 'hi', 1366050176);

-- --------------------------------------------------------

--
-- Table structure for table `CT_ScoreTag`
--

DROP TABLE IF EXISTS `CT_ScoreTag`;
CREATE TABLE `CT_ScoreTag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refScore` int(11) NOT NULL,
  `tagBy` int(11) NOT NULL,
  `tag` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `CT_ScoreTag`
--

INSERT INTO `CT_ScoreTag` (`id`, `refScore`, `tagBy`, `tag`) VALUES
(1, 1, 1, 'Alexander Zemlinszky'),
(2, 1, 2, 'Romantic'),
(3, 3, 1, 'Cello'),
(6, 1, 1, 'Friedrich Bushbaum'),
(5, 2, 3, 'Piano'),
(7, 1, 3, 'Zemlinsky, Alexander von'),
(8, 3, 1, 'Zemlinsky, Alexander von'),
(9, 4, 1, 'Walzer-Gesange'),
(10, 1, 2, 'Lieder'),
(11, 1, 2, 'Songs'),
(12, 1, 2, 'Voice'),
(13, 1, 2, 'Liebe Schwalbe');

-- --------------------------------------------------------

--
-- Table structure for table `CT_SearchLog`
--

DROP TABLE IF EXISTS `CT_SearchLog`;
CREATE TABLE `CT_SearchLog` (
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

DROP TABLE IF EXISTS `CT_Style`;
CREATE TABLE `CT_Style` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refScore` int(11) NOT NULL,
  `style` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `CT_Style`
--

INSERT INTO `CT_Style` (`id`, `refScore`, `style`) VALUES
(1, 1, 'Romantic'),
(2, 3, 'Classical'),
(3, 2, 'Romantic');

-- --------------------------------------------------------

--
-- Table structure for table `CT_User`
--

DROP TABLE IF EXISTS `CT_User`;
CREATE TABLE `CT_User` (
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
  `avatarPic` varchar(200) DEFAULT 'assets/images/profile.jpg',
  `securityQ` varchar(200) DEFAULT NULL,
  `securityA` varchar(200) CHARACTER SET utf16 DEFAULT NULL,
  `emailSHA` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `CT_User`
--

INSERT INTO `CT_User` (`id`, `userEmail`, `userPw`, `firstName`, `middleName`, `lastName`, `level`, `redFlag`, `joinDate`, `lastAccess`, `avatarPic`, `securityQ`, `securityA`, `emailSHA`) VALUES
(1, 'jooskim@umich.edu', 'https://www.google.com/accounts/o8/id?id=AItOawm1o6xSVvLGw4UU_BOk4mmXzorLm0BWKqM', 'Joosung', NULL, 'Kim', 1, 0, '2013-04-02 01:04:36', '2013-04-19 00:50:24', 'assets/images/joosung.jpg', NULL, NULL, '86165a9e249b9e1be1b7e069ad993dd780593027'),
(2, 'jsk9260@gmail.com', 'https://www.google.com/accounts/o8/id?id=AItOawmBxU_l8snhjpinBYZD9PG7RiMgPzvVxgY', 'Joosung', NULL, 'Kim', 1, 0, '2013-04-02 01:08:29', '2013-04-15 16:48:56', 'assets/images/profile.jpg', NULL, NULL, 'b751208512ad8f596d12aab1581cead7b0beda52'),
(13, 'joooo', '202cb962ac59075b964b07152d234b70', 'First', NULL, 'Last', 1, 0, '2013-04-02 22:51:26', '2013-04-19 17:18:08', 'assets/images/profile.jpg', NULL, NULL, 'e686493b86ccd00d63b4248f075c46a226459d9e'),
(12, 'asdf', 'bcc95c2d9c99b6eb053cc99aaef00092', 'aweg', NULL, 'geg', 1, 0, '2013-04-02 07:04:21', NULL, 'assets/images/profile.jpg', NULL, NULL, '3da541559918a808c2402bba5012f6c60b27661c'),
(9, 'asdfe', '202cb962ac59075b964b07152d234b70', 'test1', NULL, 'test2', 1, 0, '2013-04-02 06:58:05', NULL, 'assets/images/profile.jpg', NULL, NULL, '11a9e81eaa229b8379404b9c7d4a1eb08564c692'),
(14, 'tester', 'f5d1278e8109edd94e1e4197e04873b9', 'tester', NULL, 'Kim', 1, 0, '2013-04-11 15:01:42', '2013-04-11 15:01:42', 'assets/images/profile.jpg', NULL, NULL, 'ab4d8d2a5f480a137067da17100271cd176607a1'),
(15, 'zyoung.k@gmail.com', 'https://www.google.com/accounts/o8/id?id=AItOawnXUIgQgGuDfYNmwJJPyZ3r9pbLqvCEEdc', 'Jiyoung', NULL, 'Kim', 1, 0, '2013-04-12 20:32:53', '2013-04-12 21:19:59', 'assets/images/jiyoung.jpg', NULL, NULL, 'f16d2267a015122fdfbf83f7dfb3b1258370efd2'),
(17, 'test@test.net', '098f6bcd4621d373cade4e832627b4f6', 'SI664', NULL, 'Kim', 1, 0, '2013-04-15 18:20:22', '2013-04-15 18:42:58', 'assets/images/profile.jpg', NULL, NULL, '79fc33f9048c6436f353360155168c76f0d90083'),
(18, 'common', '9efab2399c7c560b34de477b9aa0a465', 'Common', NULL, 'Time', 1, 0, '2013-04-18 16:45:13', '2013-04-18 16:45:13', 'assets/images/profile.jpg', NULL, NULL, '94c8c21d08740f5da9eaa38d1f175c592692f0d1'),
(19, 'awef', 'edfd67a46a3f048fbd89763dce7eeffe', 'awef', NULL, 'awef', 1, 0, '2013-04-18 23:38:22', '2013-04-18 23:38:22', 'assets/images/profile.jpg', NULL, NULL, 'fd2575b94e9eae453aa826fbe1c8435caf66dca3');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
