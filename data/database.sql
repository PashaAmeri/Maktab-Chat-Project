-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 07, 2022 at 05:55 PM
-- Server version: 5.7.36
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chat_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

DROP TABLE IF EXISTS `chats`;
CREATE TABLE IF NOT EXISTS `chats` (
  `ID` int(255) NOT NULL AUTO_INCREMENT,
  `type` varchar(64) NOT NULL,
  `name` varchar(32) DEFAULT NULL,
  `about` varchar(150) DEFAULT NULL,
  `members` int(255) DEFAULT NULL,
  `creation_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`ID`, `type`, `name`, `about`, `members`, `creation_date`) VALUES
(1, 'group', 'Public', 'A Public chat for everyone!', 0, '2022-03-19 17:53:51');

-- --------------------------------------------------------

--
-- Table structure for table `chats_members`
--

DROP TABLE IF EXISTS `chats_members`;
CREATE TABLE IF NOT EXISTS `chats_members` (
  `ID` int(255) NOT NULL AUTO_INCREMENT,
  `chat_id` int(255) DEFAULT NULL,
  `user_id` int(255) DEFAULT NULL,
  `role` varchar(32) DEFAULT NULL,
  `join_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `user_id` (`user_id`),
  KEY `chat_id` (`chat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `ID` int(255) NOT NULL AUTO_INCREMENT,
  `from_id` int(255) DEFAULT NULL,
  `to_id` int(255) DEFAULT NULL,
  `message` varchar(100) DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `edit` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `to_id` (`to_id`),
  KEY `from_id` (`from_id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`ID`, `from_id`, `to_id`, `message`, `date`, `edit`) VALUES
(1, 0, 1, 'System Created Public!', '2022-05-07 00:34:27', 0);

-- --------------------------------------------------------

--
-- Table structure for table `profile_pics`
--

DROP TABLE IF EXISTS `profile_pics`;
CREATE TABLE IF NOT EXISTS `profile_pics` (
  `ID` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

DROP TABLE IF EXISTS `token`;
CREATE TABLE IF NOT EXISTS `token` (
  `ID` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) DEFAULT NULL,
  `token` char(64) DEFAULT NULL,
  `validator` char(64) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `nickname` varchar(32) DEFAULT NULL,
  `email` varchar(320) DEFAULT NULL,
  `phone_number` int(11) DEFAULT NULL,
  `about` varchar(150) DEFAULT NULL,
  `password` char(64) DEFAULT NULL,
  `register_date` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `username`, `name`, `nickname`, `email`, `phone_number`, `about`, `password`, `register_date`, `last_login`) VALUES
(0, 'system', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '2022-05-07 22:09:02');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
