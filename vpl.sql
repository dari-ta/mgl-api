-- phpMyAdmin SQL Dump
-- version 4.1.14.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 05. Mai 2015 um 20:43
-- Server Version: 5.1.73-log
-- PHP-Version: 5.5.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: ``
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vpl`
--

CREATE TABLE IF NOT EXISTS `vpl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rev` int(11) NOT NULL,
  `class` varchar(5) NOT NULL,
  `std` varchar(5) NOT NULL,
  `subj` varchar(5) NOT NULL,
  `room` varchar(5) NOT NULL,
  `froom` varchar(5) NOT NULL,
  `fsubj` varchar(5) NOT NULL,
  `comm` text NOT NULL,
  `hash` varchar(35) NOT NULL,
  `date` varchar(10) NOT NULL,
  `act` varchar(1) NOT NULL,
  `bdate` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
