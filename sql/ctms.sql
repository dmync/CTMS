-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 28, 2026 at 07:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ctms`
--

-- --------------------------------------------------------

--
-- Table structure for table `batting_score`
--

CREATE TABLE `batting_score` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `innings_no` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `runs` int(11) DEFAULT 0,
  `balls` int(11) DEFAULT 0,
  `fours` int(11) DEFAULT 0,
  `sixes` int(11) DEFAULT 0,
  `is_out` tinyint(1) DEFAULT 0,
  `out_desc` varchar(150) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batting_score`
--

INSERT INTO `batting_score` (`id`, `match_id`, `innings_no`, `player_id`, `runs`, `balls`, `fours`, `sixes`, `is_out`, `out_desc`) VALUES
(1, 1, 1, 1, 1, 1, 0, 0, 0, ''),
(2, 1, 1, 2, 4, 4, 0, 0, 1, 'bowled'),
(3, 1, 2, 3, 7, 7, 1, 0, 0, ''),
(4, 1, 2, 4, 0, 0, 0, 0, 0, ''),
(5, 2, 1, 1, 10, 6, 2, 0, 0, ''),
(6, 2, 1, 2, 6, 3, 0, 1, 1, 'bowled'),
(7, 2, 2, 3, 3, 2, 0, 0, 0, ''),
(8, 2, 2, 4, 0, 0, 0, 0, 0, ''),
(9, 4, 1, 1, 4, 2, 1, 0, 1, 'bowled'),
(10, 4, 1, 2, 0, 0, 0, 0, 0, ''),
(11, 4, 2, 3, 6, 8, 1, 0, 0, ''),
(12, 4, 2, 4, 1, 1, 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `bowling_figures`
--

CREATE TABLE `bowling_figures` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `innings_no` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `balls` int(11) DEFAULT 0,
  `runs` int(11) DEFAULT 0,
  `wickets` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bowling_figures`
--

INSERT INTO `bowling_figures` (`id`, `match_id`, `innings_no`, `player_id`, `balls`, `runs`, `wickets`) VALUES
(1, 1, 1, 3, 5, 5, 1),
(2, 1, 1, 4, 0, 0, 0),
(3, 1, 2, 1, 6, 4, 0),
(4, 1, 2, 2, 1, 4, 0),
(5, 2, 1, 3, 6, 10, 0),
(6, 2, 1, 4, 3, 7, 1),
(7, 2, 2, 1, 2, 3, 0),
(8, 2, 2, 2, 0, 0, 0),
(9, 4, 1, 3, 2, 4, 1),
(10, 4, 1, 4, 0, 0, 0),
(11, 4, 2, 1, 6, 2, 0),
(12, 4, 2, 2, 3, 6, 0);

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `over_no` int(11) DEFAULT 0,
  `ball_no` int(11) DEFAULT 0,
  `runs` int(11) DEFAULT 0,
  `is_wicket` tinyint(1) DEFAULT 0,
  `extra` varchar(20) DEFAULT NULL,
  `innings_no` int(11) DEFAULT 1,
  `striker_id` int(11) DEFAULT NULL,
  `bowler_id` int(11) DEFAULT NULL,
  `runs_bat` int(11) DEFAULT 0,
  `runs_extra` int(11) DEFAULT 0,
  `extra_type` varchar(10) DEFAULT NULL,
  `wicket_type` varchar(20) DEFAULT NULL,
  `dismissed_player_id` int(11) DEFAULT NULL,
  `commentary` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deliveries`
--

INSERT INTO `deliveries` (`id`, `match_id`, `over_no`, `ball_no`, `runs`, `is_wicket`, `extra`, `innings_no`, `striker_id`, `bowler_id`, `runs_bat`, `runs_extra`, `extra_type`, `wicket_type`, `dismissed_player_id`, `commentary`) VALUES
(1, 1, 0, 0, 1, 0, NULL, 1, 1, 3, 1, 0, NULL, NULL, NULL, 'Runs: 1'),
(2, 1, 0, 0, 0, 0, NULL, 1, 2, 3, 0, 0, NULL, NULL, NULL, 'Runs: 0'),
(3, 1, 0, 0, 2, 0, NULL, 1, 2, 3, 2, 0, NULL, NULL, NULL, 'Runs: 2'),
(4, 1, 0, 0, 2, 0, NULL, 1, 2, 3, 2, 0, NULL, NULL, NULL, 'Runs: 2'),
(5, 1, 0, 0, 0, 1, NULL, 1, 2, 3, 0, 0, NULL, 'bowled', 2, 'WICKET! bowled'),
(6, 1, 0, 0, 0, 0, NULL, 2, 3, 1, 0, 0, NULL, NULL, NULL, 'Runs: 0'),
(7, 1, 0, 0, 0, 0, NULL, 2, 3, 1, 0, 0, NULL, NULL, NULL, 'Runs: 0'),
(8, 1, 0, 0, 2, 0, NULL, 2, 3, 1, 2, 0, NULL, NULL, NULL, 'Runs: 2'),
(9, 1, 0, 0, 0, 0, NULL, 2, 3, 1, 0, 0, NULL, NULL, NULL, 'Runs: 0'),
(10, 1, 0, 0, 1, 0, NULL, 2, 3, 1, 0, 1, 'NB', NULL, NULL, 'No Ball +1, off bat 0'),
(11, 1, 0, 0, 0, 0, NULL, 2, 3, 1, 0, 0, NULL, NULL, NULL, 'Runs: 0'),
(12, 1, 0, 0, 1, 0, NULL, 2, 3, 1, 1, 0, NULL, NULL, NULL, 'Runs: 1'),
(13, 1, 0, 0, 4, 0, NULL, 2, 3, 2, 4, 0, NULL, NULL, NULL, 'Runs: 4'),
(14, 2, 0, 0, 0, 0, NULL, 1, 1, 3, 0, 0, NULL, NULL, NULL, 'Runs: 0'),
(15, 2, 0, 0, 0, 0, NULL, 1, 1, 3, 0, 0, NULL, NULL, NULL, 'Runs: 0'),
(16, 2, 0, 0, 4, 0, NULL, 1, 1, 3, 4, 0, NULL, NULL, NULL, 'Runs: 4'),
(17, 2, 0, 0, 0, 0, NULL, 1, 1, 3, 0, 0, NULL, NULL, NULL, 'Runs: 0'),
(18, 2, 0, 0, 4, 0, NULL, 1, 1, 3, 4, 0, NULL, NULL, NULL, 'Runs: 4'),
(19, 2, 0, 0, 2, 0, NULL, 1, 1, 3, 2, 0, NULL, NULL, NULL, 'Runs: 2'),
(20, 2, 0, 0, 1, 0, NULL, 1, 2, 4, 0, 1, 'WD', NULL, NULL, 'Wide ball +1'),
(21, 2, 0, 0, 6, 0, NULL, 1, 2, 4, 6, 0, NULL, NULL, NULL, 'Runs: 6'),
(22, 2, 0, 0, 0, 0, NULL, 1, 2, 4, 0, 0, NULL, NULL, NULL, 'Runs: 0'),
(23, 2, 0, 0, 0, 1, NULL, 1, 2, 4, 0, 0, NULL, 'bowled', 2, 'WICKET! bowled'),
(24, 2, 0, 0, 2, 0, NULL, 2, 3, 1, 2, 0, NULL, NULL, NULL, 'Runs: 2'),
(25, 2, 0, 0, 1, 0, NULL, 2, 3, 1, 1, 0, NULL, NULL, NULL, 'Runs: 1'),
(26, 4, 0, 0, 4, 0, NULL, 1, 1, 3, 4, 0, NULL, NULL, NULL, 'Runs: 4'),
(27, 4, 0, 0, 0, 1, NULL, 1, 1, 3, 0, 0, NULL, 'bowled', 1, 'WICKET! bowled'),
(28, 4, 0, 0, 0, 0, NULL, 2, 3, 1, 0, 0, NULL, NULL, NULL, 'Runs: 0'),
(29, 4, 0, 0, 0, 0, NULL, 2, 3, 1, 0, 0, NULL, NULL, NULL, 'Runs: 0'),
(30, 4, 0, 0, 0, 0, NULL, 2, 3, 1, 0, 0, NULL, NULL, NULL, 'Runs: 0'),
(31, 4, 0, 0, 0, 0, NULL, 2, 3, 1, 0, 0, NULL, NULL, NULL, 'Runs: 0'),
(32, 4, 0, 0, 0, 0, NULL, 2, 3, 1, 0, 0, NULL, NULL, NULL, 'Runs: 0'),
(33, 4, 0, 0, 1, 0, NULL, 2, 3, 1, 0, 1, 'WD', NULL, NULL, 'Wide ball +1'),
(34, 4, 0, 0, 1, 0, NULL, 2, 3, 1, 1, 0, NULL, NULL, NULL, 'Runs: 1'),
(35, 4, 0, 0, 1, 0, NULL, 2, 3, 2, 1, 0, NULL, NULL, NULL, 'Runs: 1'),
(36, 4, 0, 0, 1, 0, NULL, 2, 4, 2, 1, 0, NULL, NULL, NULL, 'Runs: 1'),
(37, 4, 0, 0, 4, 0, NULL, 2, 3, 2, 4, 0, NULL, NULL, NULL, 'Runs: 4'),
(166, 5, 1, 1, 4, 0, NULL, 1, 0, 3, 4, 0, NULL, NULL, NULL, 'Runs: 4'),
(167, 5, 1, 2, 2, 0, NULL, 1, 0, 3, 2, 0, NULL, NULL, NULL, 'Runs: 2'),
(168, 5, 1, 3, 1, 0, NULL, 1, 0, 3, 1, 0, NULL, NULL, NULL, 'Runs: 1'),
(169, 5, 1, 4, 4, 0, NULL, 1, 0, 3, 4, 0, NULL, NULL, NULL, 'Runs: 4'),
(170, 5, 1, 5, 0, 1, NULL, 1, 0, 3, 0, 0, NULL, 'runout', NULL, 'WICKET! runout');

-- --------------------------------------------------------

--
-- Table structure for table `innings`
--

CREATE TABLE `innings` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `innings_no` int(11) NOT NULL,
  `batting_team_id` int(11) NOT NULL,
  `bowling_team_id` int(11) NOT NULL,
  `total_runs` int(11) DEFAULT 0,
  `wickets` int(11) DEFAULT 0,
  `balls` int(11) DEFAULT 0,
  `status` varchar(20) DEFAULT 'In Progress',
  `striker_id` int(11) DEFAULT NULL,
  `non_striker_id` int(11) DEFAULT NULL,
  `next_batsman_idx` int(11) DEFAULT 3,
  `current_bowler_id` int(11) DEFAULT NULL,
  `target` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `innings`
--

INSERT INTO `innings` (`id`, `match_id`, `innings_no`, `batting_team_id`, `bowling_team_id`, `total_runs`, `wickets`, `balls`, `status`, `striker_id`, `non_striker_id`, `next_batsman_idx`, `current_bowler_id`, `target`) VALUES
(1, 1, 1, 1, 2, 5, 1, 5, 'Completed', 2, 1, 3, 3, NULL),
(2, 1, 2, 2, 1, 8, 0, 7, 'Completed', 3, 4, 3, 2, 6),
(3, 2, 1, 1, 2, 17, 1, 9, 'Completed', 2, 1, 3, 4, NULL),
(4, 2, 2, 2, 1, 3, 0, 2, 'In Progress', 4, 3, 3, 1, 18),
(5, 4, 1, 1, 2, 4, 1, 2, 'Completed', 1, 2, 3, 3, NULL),
(6, 4, 2, 2, 1, 8, 0, 9, 'Completed', 3, 4, 3, 2, 5),
(7, 5, 1, 1, 2, 11, 1, 5, 'Completed', 0, 0, 3, 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `team_batting` int(11) DEFAULT NULL,
  `team_bowling` int(11) DEFAULT NULL,
  `overs_limit` int(11) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `winner_team_id` int(11) DEFAULT NULL,
  `win_margin` varchar(50) DEFAULT NULL,
  `batting_team_id` int(11) DEFAULT NULL,
  `bowling_team_id` int(11) DEFAULT NULL,
  `toss_winner_team_id` int(11) DEFAULT NULL,
  `wickets_limit` int(11) DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`id`, `tournament_id`, `team_batting`, `team_bowling`, `overs_limit`, `status`, `created_at`, `winner_team_id`, `win_margin`, `batting_team_id`, `bowling_team_id`, `toss_winner_team_id`, `wickets_limit`) VALUES
(1, 1, 1, 2, 10, 'Completed', '2026-01-25 15:06:11', NULL, NULL, NULL, NULL, NULL, 10),
(2, 1, 1, 2, 10, 'In Progress', '2026-01-25 15:12:58', NULL, NULL, NULL, NULL, NULL, 10),
(3, 1, 1, 2, 10, 'In Progress', '2026-01-25 15:19:46', NULL, NULL, NULL, NULL, NULL, 10),
(4, 1, 1, 2, 10, 'Completed', '2026-01-25 16:02:02', NULL, NULL, NULL, NULL, NULL, 10),
(5, 1, NULL, NULL, 5, 'In Progress', '2026-01-26 14:13:31', NULL, NULL, 1, 2, NULL, 10);

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `team_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `is_captain` tinyint(1) DEFAULT NULL,
  `is_wicketkeeper` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`id`, `team_id`, `name`, `is_captain`, `is_wicketkeeper`) VALUES
(1, 1, 'Player A1', 1, 0),
(2, 1, 'Player A2', 0, 0),
(3, 2, 'Player B1', 1, 0),
(4, 2, 'Player B2', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Admin'),
(2, 'Umpire'),
(3, 'Viewer');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `tournament_id`, `name`) VALUES
(1, 1, 'Team A'),
(2, 1, 'Team B');

-- --------------------------------------------------------

--
-- Table structure for table `tournaments`
--

CREATE TABLE `tournaments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `year` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournaments`
--

INSERT INTO `tournaments` (`id`, `name`, `year`) VALUES
(1, 'ABC Trophy', 2024);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role_id`) VALUES
(1, 'admin', '1234', 1),
(2, 'umpire', '1234', 2),
(3, 'viewer', '1234', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batting_score`
--
ALTER TABLE `batting_score`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_id` (`match_id`);

--
-- Indexes for table `bowling_figures`
--
ALTER TABLE `bowling_figures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_id` (`match_id`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_del_match_inn` (`match_id`,`innings_no`);

--
-- Indexes for table `innings`
--
ALTER TABLE `innings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_id` (`match_id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournament_id` (`tournament_id`);

--
-- Indexes for table `tournaments`
--
ALTER TABLE `tournaments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batting_score`
--
ALTER TABLE `batting_score`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `bowling_figures`
--
ALTER TABLE `bowling_figures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `innings`
--
ALTER TABLE `innings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tournaments`
--
ALTER TABLE `tournaments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `batting_score`
--
ALTER TABLE `batting_score`
  ADD CONSTRAINT `batting_score_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`);

--
-- Constraints for table `bowling_figures`
--
ALTER TABLE `bowling_figures`
  ADD CONSTRAINT `bowling_figures_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`);

--
-- Constraints for table `innings`
--
ALTER TABLE `innings`
  ADD CONSTRAINT `innings_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`);

--
-- Constraints for table `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`);

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
