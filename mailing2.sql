-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 22, 2016 at 10:41 AM
-- Server version: 5.6.26-log
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mailing2`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE IF NOT EXISTS `addresses` (
  `idaddresses` int(11) NOT NULL,
  `surname` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `company` varchar(45) DEFAULT NULL,
  `job_title` varchar(45) DEFAULT NULL,
  `list_idlist` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1985 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `inbox`
--

CREATE TABLE IF NOT EXISTS `inbox` (
  `idinbox` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8_bin NOT NULL,
  `email` varchar(45) COLLATE utf8_bin NOT NULL,
  `signature` varchar(500) COLLATE utf8_bin NOT NULL,
  `username` varchar(45) COLLATE utf8_bin NOT NULL,
  `password` varchar(45) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `list`
--

CREATE TABLE IF NOT EXISTS `list` (
  `idlist` int(11) NOT NULL,
  `name` varchar(45) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `idlog` int(11) NOT NULL,
  `send_result` tinyint(1) NOT NULL,
  `read_result` tinyint(1) NOT NULL,
  `newsletter_idnewsletter` int(11) NOT NULL,
  `addresses_idaddresses` int(11) NOT NULL,
  `time_sent` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3007 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

CREATE TABLE IF NOT EXISTS `newsletter` (
  `idnewsletter` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=184 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_log_details`
--
CREATE TABLE IF NOT EXISTS `v_log_details` (
`title` varchar(100)
,`datetime` datetime
,`firstname` varchar(100)
,`surname` varchar(100)
,`email` varchar(100)
,`group` varchar(45)
,`send_result` tinyint(1)
,`time_sent` datetime
,`idnewsletter` int(11)
);

-- --------------------------------------------------------

--
-- Structure for view `v_log_details`
--
DROP TABLE IF EXISTS `v_log_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_log_details` AS select `newsletter`.`title` AS `title`,`newsletter`.`datetime` AS `datetime`,`addresses`.`firstname` AS `firstname`,`addresses`.`surname` AS `surname`,`addresses`.`email` AS `email`,`list`.`name` AS `group`,`log`.`send_result` AS `send_result`,`log`.`time_sent` AS `time_sent`,`newsletter`.`idnewsletter` AS `idnewsletter` from (((`log` join `addresses` on((`addresses`.`idaddresses` = `log`.`addresses_idaddresses`))) join `newsletter` on((`newsletter`.`idnewsletter` = `log`.`newsletter_idnewsletter`))) join `list` on((`list`.`idlist` = `addresses`.`list_idlist`)));

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`idaddresses`),
  ADD KEY `fk_addresses_list1_idx` (`list_idlist`);

--
-- Indexes for table `inbox`
--
ALTER TABLE `inbox`
  ADD PRIMARY KEY (`idinbox`);

--
-- Indexes for table `list`
--
ALTER TABLE `list`
  ADD PRIMARY KEY (`idlist`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`idlog`),
  ADD KEY `fk_log_newsletter1_idx` (`newsletter_idnewsletter`),
  ADD KEY `fk_log_addresses1_idx` (`addresses_idaddresses`);

--
-- Indexes for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`idnewsletter`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `idaddresses` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1985;
--
-- AUTO_INCREMENT for table `inbox`
--
ALTER TABLE `inbox`
  MODIFY `idinbox` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `list`
--
ALTER TABLE `list`
  MODIFY `idlist` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `idlog` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3007;
--
-- AUTO_INCREMENT for table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `idnewsletter` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=184;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `fk_addresses_list1` FOREIGN KEY (`list_idlist`) REFERENCES `list` (`idlist`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `log_ibfk_1` FOREIGN KEY (`newsletter_idnewsletter`) REFERENCES `newsletter` (`idnewsletter`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `log_ibfk_2` FOREIGN KEY (`addresses_idaddresses`) REFERENCES `addresses` (`idaddresses`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
