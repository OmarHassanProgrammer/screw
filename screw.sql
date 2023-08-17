-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 17, 2023 at 02:45 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `screw`
--

-- --------------------------------------------------------

--
-- Table structure for table `player_room`
--

CREATE TABLE `player_room` (
  `id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `room_pass` varchar(11) NOT NULL,
  `cards` varchar(25) DEFAULT NULL,
  `scores` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `player_room`
--

INSERT INTO `player_room` (`id`, `player_id`, `room_pass`, `cards`, `scores`) VALUES
(3, 1, 'ASDFG', '5,2,5,13', ',23,1,2,0'),
(4, 2, 'ASDFG', '17,2,12,16', ',26,0,5,37'),
(5, 3, 'ASDFG', '9,10,11,4', ',31,8,0,33'),
(13, 4, 'ASDFG', '6,9,9,14', ',0,3,15,44');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `password` varchar(11) NOT NULL,
  `players` varchar(25) NOT NULL,
  `deck` varchar(150) DEFAULT NULL,
  `lastThrown` int(11) NOT NULL DEFAULT -1,
  `thrown` varchar(150) NOT NULL,
  `turn` int(11) NOT NULL DEFAULT -1,
  `started` tinyint(1) NOT NULL DEFAULT 0,
  `mode` varchar(10) NOT NULL DEFAULT 'entering',
  `screw` int(11) NOT NULL DEFAULT -1,
  `active` int(11) NOT NULL DEFAULT -1,
  `activeStep` int(11) NOT NULL DEFAULT -1,
  `reveal` varchar(10) NOT NULL DEFAULT '-1,-1',
  `drawn` int(11) NOT NULL DEFAULT -1,
  `drawThrow` tinyint(1) NOT NULL DEFAULT 0,
  `subCard` varchar(11) NOT NULL DEFAULT '-1',
  `card` varchar(10) NOT NULL DEFAULT '',
  `action` varchar(10) NOT NULL DEFAULT 'before',
  `startDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `password`, `players`, `deck`, `lastThrown`, `thrown`, `turn`, `started`, `mode`, `screw`, `active`, `activeStep`, `reveal`, `drawn`, `drawThrow`, `subCard`, `card`, `action`, `startDate`) VALUES
(3, 'ASDFG', ',1,2,3,4,', '', -1, '', 0, 0, 'ended', -1, -1, -1, '-1,-1', -1, 0, '', '', 'before', '2023-08-16 02:22:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `token` int(11) NOT NULL,
  `last_seen` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `token`, `last_seen`) VALUES
(1, 'Omar', 84285, '2023-08-15 22:10:11'),
(2, 'Ahmed', 99378, '2023-08-16 01:33:33'),
(3, 'Fouda', 42749, '2023-08-16 01:36:10'),
(4, 'Amar', 60338, '2023-08-16 01:44:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `player_room`
--
ALTER TABLE `player_room`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `player_room`
--
ALTER TABLE `player_room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
