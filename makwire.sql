-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 04, 2017 at 07:36 PM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 7.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `makwire`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `actor_id` int(11) UNSIGNED NOT NULL,
  `subject_id` int(11) UNSIGNED NOT NULL,
  `source_id` bigint(20) UNSIGNED NOT NULL,
  `source_type` enum('post','comment','reply','photo','user','birthday_message') NOT NULL,
  `activity` enum('like','comment','share','reply','post','photo','profile_pic_change','friend_request','confirmed_friend_request','birthday','message') NOT NULL,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activity_id`, `actor_id`, `subject_id`, `source_id`, `source_type`, `activity`, `date_entered`) VALUES
(1, 3, 1, 8, 'post', 'share', '2017-02-22 18:13:53'),
(2, 2, 1, 8, 'post', 'comment', '2017-02-22 18:14:20'),
(3, 2, 1, 8, 'post', 'share', '2017-02-22 19:43:50'),
(4, 2, 1, 8, 'post', 'like', '2017-02-22 19:44:23'),
(13, 3, 1, 8, 'post', 'like', '2017-02-22 20:27:07'),
(15, 3, 1, 8, 'post', 'comment', '2017-02-22 20:34:51'),
(16, 2, 1, 8, 'post', 'comment', '2017-02-22 20:35:39'),
(17, 3, 1, 8, 'post', 'comment', '2017-02-22 20:36:22'),
(18, 1, 1, 8, 'post', 'comment', '2017-02-22 20:37:00'),
(19, 2, 1, 8, 'post', 'comment', '2017-02-22 20:38:09'),
(20, 2, 1, 8, 'post', 'comment', '2017-02-22 20:38:51'),
(23, 3, 1, 8, 'post', 'comment', '2017-02-22 20:41:06'),
(24, 2, 1, 8, 'post', 'comment', '2017-02-22 20:44:39'),
(26, 3, 1, 8, 'post', 'comment', '2017-02-22 20:51:08'),
(27, 3, 1, 35, 'comment', 'like', '2017-02-22 21:00:41'),
(28, 3, 1, 8, 'post', 'comment', '2017-02-22 21:01:21'),
(29, 2, 3, 34, 'comment', 'like', '2017-02-22 21:02:18'),
(30, 2, 1, 37, 'comment', 'like', '2017-02-22 21:02:22'),
(31, 2, 1, 41, 'comment', 'like', '2017-02-22 21:08:46'),
(32, 2, 1, 8, 'post', 'comment', '2017-02-22 21:09:13'),
(33, 3, 1, 8, 'post', 'comment', '2017-02-22 21:09:23'),
(34, 1, 1, 8, 'post', 'comment', '2017-02-22 21:41:35'),
(35, 3, 1, 8, 'post', 'comment', '2017-02-22 21:42:38'),
(36, 1, 1, 8, 'post', 'comment', '2017-02-22 21:48:44'),
(37, 3, 1, 8, 'post', 'comment', '2017-02-22 21:49:25'),
(38, 3, 2, 33, 'comment', 'reply', '2017-02-22 21:55:12'),
(39, 2, 2, 33, 'comment', 'reply', '2017-02-22 21:58:11'),
(40, 4, 2, 33, 'comment', 'like', '2017-02-22 22:02:54'),
(41, 1, 1, 1, 'user', 'birthday', '2016-02-23 00:38:52'),
(43, 2, 3, 34, 'comment', 'reply', '2017-02-23 09:23:03'),
(46, 1, 2, 60, 'reply', 'like', '2017-02-23 09:30:06'),
(47, 3, 1, 8, 'post', 'comment', '2017-02-23 13:30:57'),
(50, 1, 3, 62, 'comment', 'like', '2017-02-24 13:40:30'),
(51, 3, 2, 33, 'comment', 'like', '2017-02-24 14:29:24'),
(52, 3, 2, 33, 'comment', 'reply', '2017-02-24 14:30:05'),
(53, 2, 3, 59, 'reply', 'like', '2017-02-24 14:30:30'),
(54, 3, 3, 10, 'post', 'post', '2017-02-25 00:13:25'),
(56, 1, 3, 63, 'comment', 'like', '2017-02-25 18:53:21'),
(57, 1, 3, 63, 'comment', 'like', '2017-02-25 19:03:56'),
(59, 3, 2, 44, 'comment', 'like', '2017-03-11 11:48:09'),
(60, 3, 2, 36, 'comment', 'like', '2017-03-11 16:41:59'),
(61, 3, 1, 37, 'comment', 'like', '2017-03-11 16:49:25'),
(62, 1, 0, 1, 'reply', 'like', '2017-03-12 13:35:55'),
(63, 1, 3, 1, 'birthday_message', 'like', '2017-03-12 13:37:57'),
(64, 2, 1, 1, 'user', 'message', '2017-03-12 14:03:34'),
(65, 3, 1, 1, 'user', 'message', '2017-03-12 14:10:50'),
(74, 3, 0, 21, 'photo', 'like', '2017-03-12 22:40:05'),
(75, 3, 2, 21, 'photo', 'comment', '2017-03-12 22:40:59'),
(76, 3, 0, 21, 'photo', 'like', '2017-03-12 22:43:16'),
(77, 1, 2, 21, 'photo', 'like', '2017-03-12 22:52:41'),
(79, 2, 3, 34, 'comment', 'reply', '2017-03-12 23:17:52'),
(80, 3, 1, 8, 'comment', 'reply', '2017-03-17 14:35:01'),
(81, 1, 3, 10, 'post', 'share', '2017-05-04 13:39:26'),
(82, 3, 3, 1, 'photo', 'photo', '2017-05-05 17:07:49'),
(83, 3, 3, 2, 'photo', 'photo', '2017-05-05 17:13:56'),
(84, 3, 3, 3, 'photo', 'photo', '2017-05-05 18:04:25'),
(85, 3, 3, 4, 'photo', 'photo', '2017-05-05 18:09:27'),
(86, 3, 3, 11, 'post', 'post', '2017-05-07 23:16:23'),
(87, 3, 3, 11, 'post', 'comment', '2017-05-07 23:17:10'),
(88, 3, 3, 34, 'comment', 'reply', '2017-05-07 23:17:40'),
(89, 3, 0, 0, '', 'comment', '2017-05-10 23:03:04'),
(90, 3, 3, 4, 'photo', 'comment', '2017-05-10 23:03:48'),
(91, 3, 3, 4, 'photo', 'comment', '2017-05-10 23:05:31'),
(92, 1, 3, 10, 'post', 'like', '2017-05-10 23:22:38'),
(93, 1, 3, 4, 'photo', 'share', '2017-05-10 23:28:14'),
(94, 2, 3, 2, 'photo', 'like', '2017-05-11 19:03:50'),
(95, 2, 3, 2, 'photo', 'comment', '2017-05-11 19:04:27'),
(96, 2, 3, 2, 'photo', 'share', '2017-05-11 19:05:00'),
(97, 3, 2, 74, 'comment', 'like', '2017-05-11 19:42:26'),
(98, 3, 2, 74, 'comment', 'reply', '2017-05-11 19:55:01'),
(99, 2, 3, 75, 'reply', 'like', '2017-05-11 19:55:20'),
(100, 2, 3, 73, 'comment', 'reply', '2017-05-12 20:22:34'),
(101, 3, 2, 76, 'reply', 'like', '2017-05-12 20:22:52'),
(102, 2, 3, 73, 'comment', 'like', '2017-05-12 20:23:31'),
(103, 3, 2, 3, 'birthday_message', 'like', '2017-05-18 19:04:45'),
(104, 1, 1, 1, 'user', 'birthday', '2017-05-19 14:43:44'),
(105, 1, 1, 12, 'post', 'post', '2017-05-19 20:35:08'),
(107, 1, 3, 2, 'birthday_message', 'comment', '2017-05-20 18:22:05'),
(108, 3, 1, 78, 'reply', 'like', '2017-05-20 18:34:05'),
(110, 3, 2, 33, 'comment', 'reply', '2017-05-20 19:17:23'),
(111, 3, 3, 2, 'birthday_message', 'reply', '2017-05-20 19:23:31'),
(112, 1, 3, 81, 'reply', 'like', '2017-05-20 19:33:48'),
(113, 1, 2, 3, 'birthday_message', 'reply', '2017-05-20 19:46:03'),
(114, 2, 1, 82, 'reply', 'like', '2017-05-20 19:48:13'),
(115, 1, 1, 5, 'photo', 'photo', '2017-05-21 15:53:07'),
(116, 1, 1, 6, 'photo', 'photo', '2017-05-21 15:59:30'),
(117, 1, 1, 7, 'photo', 'photo', '2017-05-21 16:42:37'),
(118, 1, 1, 8, 'photo', 'profile_pic_change', '2017-05-21 16:44:41'),
(119, 1, 1, 9, 'photo', 'profile_pic_change', '2017-05-21 16:54:36'),
(120, 1, 1, 10, 'photo', 'profile_pic_change', '2017-06-04 19:40:12'),
(121, 1, 1, 11, 'photo', 'photo', '2017-06-04 19:41:00'),
(122, 1, 1, 12, 'photo', 'profile_pic_change', '2017-06-04 19:42:47');

-- --------------------------------------------------------

--
-- Table structure for table `birthday_messages`
--

CREATE TABLE `birthday_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `sender_id` int(11) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `age` mediumint(9) UNSIGNED NOT NULL,
  `date_sent` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `birthday_messages`
--

INSERT INTO `birthday_messages` (`id`, `user_id`, `sender_id`, `message`, `age`, `date_sent`) VALUES
(1, 1, 3, 'happy birthday', 21, '2017-03-11 16:29:44'),
(2, 1, 3, 'happy birthday', 21, '2017-03-11 16:33:10'),
(3, 1, 2, 'hbd', 21, '2017-03-12 14:03:33'),
(4, 1, 3, 'hbd dear', 21, '2017-03-12 14:10:50');

-- --------------------------------------------------------

--
-- Table structure for table `colleges`
--

CREATE TABLE `colleges` (
  `college_id` int(11) UNSIGNED NOT NULL,
  `college_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `colleges`
--

INSERT INTO `colleges` (`college_id`, `college_name`) VALUES
(1, 'College of Computing and Information Sciences');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` bigint(20) UNSIGNED NOT NULL,
  `commenter_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `source_id` bigint(20) UNSIGNED NOT NULL,
  `source_type` enum('post','comment','photo','birthday_message') NOT NULL,
  `comment` tinytext NOT NULL,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `commenter_id`, `parent_id`, `source_id`, `source_type`, `comment`, `date_entered`) VALUES
(1, 3, 0, 1, 'post', 'wow', '2017-01-21 09:30:53'),
(2, 1, 1, 1, 'comment', 'cool', '2017-01-21 09:36:07'),
(3, 1, 1, 1, 'comment', 'cool', '2017-01-21 09:38:30'),
(4, 1, 1, 1, 'comment', '   ', '2017-01-22 15:54:16'),
(5, 1, 1, 1, 'comment', 'yadda', '2017-01-25 15:43:33'),
(6, 3, 0, 7, 'post', 'What did you mean by this', '2017-01-25 22:23:31'),
(7, 1, 6, 6, 'comment', 'nothing', '2017-01-25 22:23:56'),
(8, 1, 0, 1, 'post', 'another', '2017-01-25 22:39:42'),
(9, 3, 0, 7, 'post', 'Kitufu', '2017-02-19 17:36:46'),
(10, 2, 0, 1, 'post', 'Hello You!', '2017-02-21 13:16:58'),
(11, 2, 0, 3, 'post', 'wow', '2017-02-21 13:30:10'),
(12, 2, 0, 3, 'post', 'wow', '2017-02-21 13:31:40'),
(13, 2, 0, 3, 'post', 'i like it', '2017-02-21 13:39:31'),
(14, 1, 0, 3, 'post', 'wow', '2017-02-21 14:10:39'),
(15, 1, 0, 3, 'post', 'wow', '2017-02-21 14:12:54'),
(16, 2, 0, 3, 'post', 'true dat', '2017-02-21 14:13:36'),
(17, 1, 0, 3, 'post', 'wow', '2017-02-21 14:13:44'),
(18, 4, 0, 7, 'post', 'hi', '2017-02-21 14:40:32'),
(19, 3, 0, 7, 'post', 'hello', '2017-02-21 14:43:16'),
(20, 3, 0, 7, 'post', 'hello again', '2017-02-21 14:47:27'),
(21, 3, 0, 7, 'post', 'wow', '2017-02-21 16:22:42'),
(22, 3, 0, 1, 'post', 'you said it.', '2017-02-21 19:17:07'),
(23, 3, 0, 1, 'post', 'more later', '2017-02-21 19:27:38'),
(24, 2, 0, 1, 'post', 'I SEE', '2017-02-21 19:33:31'),
(33, 2, 0, 8, 'post', 'wow', '2017-02-22 18:14:20'),
(34, 3, 0, 8, 'post', 'i tell you', '2017-02-22 19:45:04'),
(35, 1, 0, 8, 'post', 'you people', '2017-02-22 19:51:20'),
(36, 2, 0, 8, 'post', 'oi', '2017-02-22 19:52:05'),
(37, 1, 0, 8, 'post', 'gfr', '2017-02-22 19:52:57'),
(38, 3, 0, 8, 'post', 'hello', '2017-02-22 19:53:48'),
(39, 1, 0, 8, 'post', 'yu', '2017-02-22 19:54:20'),
(40, 1, 0, 8, 'post', 'we', '2017-02-22 19:55:11'),
(41, 1, 0, 8, 'post', 'fu', '2017-02-22 19:55:42'),
(42, 2, 0, 8, 'post', 'fu', '2017-02-22 20:28:20'),
(43, 3, 0, 8, 'post', 'xyz', '2017-02-22 20:34:51'),
(44, 2, 0, 8, 'post', 'wow', '2017-02-22 20:35:39'),
(45, 3, 0, 8, 'post', 'wow', '2017-02-22 20:36:21'),
(46, 1, 0, 8, 'post', 'this is getting crazy', '2017-02-22 20:37:00'),
(47, 2, 0, 8, 'post', 'lskd', '2017-02-22 20:38:09'),
(48, 2, 0, 8, 'post', 'ldkd', '2017-02-22 20:38:51'),
(49, 3, 0, 8, 'post', 'hwo', '2017-02-22 20:41:06'),
(50, 2, 0, 8, 'post', 'coment', '2017-02-22 20:44:39'),
(51, 3, 0, 8, 'post', 'comment', '2017-02-22 20:51:08'),
(52, 3, 0, 8, 'post', 'jk''kj', '2017-02-22 21:01:21'),
(53, 2, 0, 8, 'post', 'vg', '2017-02-22 21:09:13'),
(54, 3, 0, 8, 'post', 'jj''', '2017-02-22 21:09:23'),
(55, 1, 0, 8, 'post', 'kdkd', '2017-02-22 21:41:35'),
(56, 3, 0, 8, 'post', 'jkj''', '2017-02-22 21:42:38'),
(57, 1, 0, 8, 'post', 'fgg', '2017-02-22 21:48:44'),
(58, 3, 0, 8, 'post', 'fggh', '2017-02-22 21:49:25'),
(59, 3, 33, 33, 'comment', 'what?', '2017-02-22 21:55:12'),
(60, 2, 33, 33, 'comment', 'hehe', '2017-02-22 21:58:11'),
(61, 2, 34, 34, 'comment', 'wow', '2017-02-23 09:23:03'),
(62, 3, 0, 8, 'post', 'who wah', '2017-02-23 13:30:57'),
(63, 3, 0, 11, 'photo', 'weww', '2017-02-23 15:09:47'),
(64, 3, 33, 33, 'comment', 'what?', '2017-02-24 14:30:05'),
(65, 3, 0, 11, 'photo', 'did you mean..', '2017-02-25 20:34:07'),
(66, 3, 0, 21, 'photo', 'wow', '2017-03-12 22:40:59'),
(67, 2, 34, 34, 'comment', 'you', '2017-03-12 23:17:52'),
(68, 3, 8, 8, 'comment', 'i see', '2017-03-17 14:35:01'),
(69, 3, 0, 11, 'post', 'first program', '2017-05-07 23:17:10'),
(70, 3, 34, 34, 'comment', 'what?', '2017-05-07 23:17:40'),
(73, 3, 0, 4, 'photo', 'I like that...', '2017-05-10 23:05:31'),
(74, 2, 0, 2, 'photo', 'Which days?', '2017-05-11 19:04:27'),
(75, 3, 74, 74, 'comment', 'those days...', '2017-05-11 19:55:01'),
(76, 2, 73, 73, 'comment', 'Zeee!!!', '2017-05-12 20:22:34'),
(79, 3, 33, 33, 'comment', 'hello test', '2017-05-20 19:16:38'),
(80, 3, 33, 33, 'comment', 'hello test', '2017-05-20 19:17:23'),
(81, 3, 2, 2, 'birthday_message', 'I don''t see my reply', '2017-05-20 19:23:31'),
(82, 1, 3, 3, 'birthday_message', 'thanks dear', '2017-05-20 19:46:03');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `country_id` int(11) UNSIGNED NOT NULL,
  `country_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`country_id`, `country_name`) VALUES
(1, 'Uganda');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `district_id` int(11) UNSIGNED NOT NULL,
  `country_id` int(11) UNSIGNED NOT NULL,
  `district_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`district_id`, `country_id`, `district_name`) VALUES
(1, 1, 'Oyam'),
(2, 1, 'Kampala');

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `friend_id` int(11) UNSIGNED NOT NULL,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `blocked` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`id`, `user_id`, `friend_id`, `date_entered`, `blocked`) VALUES
(2, 3, 1, '2017-01-21 09:27:13', 0),
(3, 2, 3, '2017-02-21 13:29:44', 0),
(4, 1, 4, '2017-02-21 14:40:05', 0),
(5, 1, 2, '2017-02-22 00:38:24', 0);

-- --------------------------------------------------------

--
-- Table structure for table `friend_requests`
--

CREATE TABLE `friend_requests` (
  `request_id` bigint(20) UNSIGNED NOT NULL,
  `target_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `seen` tinyint(1) UNSIGNED NOT NULL,
  `confirmed` tinyint(1) UNSIGNED NOT NULL,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `friend_requests`
--

INSERT INTO `friend_requests` (`request_id`, `target_id`, `user_id`, `seen`, `confirmed`, `date_entered`) VALUES
(2, 3, 1, 1, 1, '2017-01-21 09:25:19'),
(6, 4, 1, 1, 1, '2017-02-20 22:04:32'),
(9, 2, 3, 1, 1, '2017-02-21 13:26:44'),
(10, 1, 2, 1, 1, '2017-02-22 00:38:05');

-- --------------------------------------------------------

--
-- Table structure for table `halls`
--

CREATE TABLE `halls` (
  `hall_id` int(11) UNSIGNED NOT NULL,
  `hall_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `halls`
--

INSERT INTO `halls` (`hall_id`, `hall_name`) VALUES
(1, 'Africa Hall'),
(2, 'Complex Hall'),
(3, 'Livingstone Hall'),
(4, 'Lumumba Hall'),
(5, 'Mary Stuart Hall'),
(6, 'Mitchell Hall'),
(7, 'Nkrumah Hall'),
(8, 'Nsibirwa Hall'),
(9, 'University Hall');

-- --------------------------------------------------------

--
-- Table structure for table `hostels`
--

CREATE TABLE `hostels` (
  `hostel_id` int(11) UNSIGNED NOT NULL,
  `hostel_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `hostels`
--

INSERT INTO `hostels` (`hostel_id`, `hostel_name`) VALUES
(1, 'Paramount Hostel');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `like_id` bigint(20) UNSIGNED NOT NULL,
  `source_id` bigint(20) UNSIGNED NOT NULL,
  `source_type` enum('post','photo','comment','reply','birthday_message') NOT NULL,
  `liker_id` int(11) UNSIGNED NOT NULL,
  `date_liked` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`like_id`, `source_id`, `source_type`, `liker_id`, `date_liked`) VALUES
(1, 4, 'reply', 2, '2017-01-26 08:59:50'),
(2, 3, 'reply', 2, '2017-01-26 09:01:05'),
(3, 1, 'post', 2, '2017-01-26 10:38:33'),
(4, 7, 'post', 3, '2017-02-19 17:31:58'),
(5, 7, 'post', 2, '2017-02-19 19:39:03'),
(9, 8, 'post', 2, '2017-02-22 19:44:23'),
(10, 8, 'post', 3, '2017-02-22 20:27:07'),
(11, 35, 'comment', 3, '2017-02-22 21:00:41'),
(12, 34, 'comment', 2, '2017-02-22 21:02:17'),
(13, 37, 'comment', 2, '2017-02-22 21:02:22'),
(14, 41, 'comment', 2, '2017-02-22 21:08:46'),
(15, 33, 'comment', 4, '2017-02-22 22:02:54'),
(16, 60, 'reply', 3, '2017-02-23 09:11:44'),
(17, 61, 'reply', 3, '2017-02-23 09:23:25'),
(18, 61, 'reply', 1, '2017-02-23 09:27:19'),
(19, 60, 'reply', 1, '2017-02-23 09:30:06'),
(20, 62, 'comment', 1, '2017-02-24 13:40:30'),
(21, 33, 'comment', 3, '2017-02-24 14:29:24'),
(22, 59, 'reply', 2, '2017-02-24 14:30:30'),
(24, 63, 'comment', 1, '2017-02-25 19:03:56'),
(25, 44, 'comment', 3, '2017-03-11 11:48:09'),
(26, 36, 'comment', 3, '2017-03-11 16:41:59'),
(27, 37, 'comment', 3, '2017-03-11 16:49:25'),
(29, 1, 'birthday_message', 1, '2017-03-12 13:37:57'),
(31, 21, 'photo', 3, '2017-03-12 22:43:16'),
(32, 21, 'photo', 1, '2017-03-12 22:52:40'),
(33, 10, 'post', 1, '2017-05-10 23:22:37'),
(34, 2, 'photo', 2, '2017-05-11 19:03:50'),
(35, 74, 'comment', 3, '2017-05-11 19:42:26'),
(36, 75, 'reply', 2, '2017-05-11 19:55:20'),
(37, 76, 'reply', 3, '2017-05-12 20:22:52'),
(38, 73, 'comment', 2, '2017-05-12 20:23:31'),
(39, 3, 'birthday_message', 3, '2017-05-18 19:04:44'),
(40, 78, 'reply', 3, '2017-05-20 18:34:05'),
(41, 81, 'reply', 1, '2017-05-20 19:33:48'),
(42, 82, 'reply', 2, '2017-05-20 19:48:13');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` int(11) UNSIGNED NOT NULL,
  `receiver_id` int(11) UNSIGNED NOT NULL,
  `message` tinytext NOT NULL,
  `seen` tinyint(1) UNSIGNED NOT NULL,
  `date_sent` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `message`, `seen`, `date_sent`) VALUES
(1, 1, 2, 'hi', 1, '2017-01-21 09:11:55'),
(2, 1, 2, 'hello', 1, '2017-01-26 13:52:19'),
(3, 1, 2, 'hello', 1, '2017-01-26 13:59:21'),
(4, 1, 2, 'hello', 1, '2017-01-26 14:00:58'),
(5, 1, 3, 'hi', 1, '2017-02-24 14:32:27'),
(6, 3, 1, 'how are you', 1, '2017-02-24 14:32:52'),
(7, 1, 3, 'fine and you', 1, '2017-02-24 14:33:11'),
(9, 3, 1, 'Good afternon', 1, '2017-05-07 14:06:19'),
(10, 3, 2, 'good evening', 1, '2017-05-07 23:23:07'),
(11, 2, 3, 'gd evening, how are you?', 1, '2017-05-07 23:25:02');

-- --------------------------------------------------------

--
-- Table structure for table `notification_read`
--

CREATE TABLE `notification_read` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `date_read` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notification_read`
--

INSERT INTO `notification_read` (`id`, `user_id`, `activity_id`, `date_read`) VALUES
(1, 1, 1, '2017-02-22 18:14:02'),
(2, 1, 2, '2017-02-22 18:14:42'),
(3, 2, 1, '2017-02-22 19:20:54'),
(4, 1, 3, '2017-02-22 19:43:59'),
(5, 1, 4, '2017-02-22 19:44:35'),
(6, 1, 5, '2017-02-22 19:45:13'),
(7, 3, 2, '2017-02-22 19:45:25'),
(8, 2, 5, '2017-02-22 19:45:48'),
(9, 3, 6, '2017-02-22 19:51:30'),
(10, 1, 7, '2017-02-22 19:52:26'),
(11, 2, 8, '2017-02-22 19:53:13'),
(12, 1, 9, '2017-02-22 19:54:23'),
(13, 3, 10, '2017-02-22 19:54:50'),
(14, 1, 13, '2017-02-22 20:27:24'),
(15, 2, 12, '2017-02-22 20:29:15'),
(16, 2, 15, '2017-02-22 20:35:03'),
(17, 1, 15, '2017-02-22 20:35:21'),
(18, 3, 16, '2017-02-22 20:35:49'),
(19, 1, 16, '2017-02-22 20:35:59'),
(20, 2, 17, '2017-02-22 20:36:31'),
(21, 1, 17, '2017-02-22 20:36:45'),
(22, 3, 18, '2017-02-22 20:37:09'),
(23, 1, 19, '2017-02-22 20:38:39'),
(24, 2, 18, '2017-02-22 20:38:56'),
(25, 1, 20, '2017-02-22 20:39:20'),
(26, 1, 23, '2017-02-22 20:42:03'),
(27, 1, 22, '2017-02-22 20:42:03'),
(28, 2, 23, '2017-02-22 20:45:06'),
(29, 3, 24, '2017-02-22 20:49:48'),
(30, 1, 26, '2017-02-22 20:51:23'),
(31, 2, 26, '2017-02-22 20:52:41'),
(32, 1, 30, '2017-02-22 21:02:47'),
(33, 1, 33, '2017-02-22 21:09:35'),
(34, 3, 32, '2017-02-22 21:21:48'),
(35, 3, 29, '2017-02-22 21:21:56'),
(36, 3, 21, '2017-02-22 21:22:03'),
(37, 3, 29, '2017-02-22 21:22:08'),
(38, 3, 32, '2017-02-22 21:22:10'),
(39, 3, 29, '2017-02-22 21:23:31'),
(40, 3, 21, '2017-02-22 21:23:33'),
(41, 3, 32, '2017-02-22 21:23:38'),
(42, 3, 29, '2017-02-22 21:23:43'),
(43, 3, 21, '2017-02-22 21:23:47'),
(44, 2, 33, '0000-00-00 00:00:00'),
(45, 2, 33, '0000-00-00 00:00:00'),
(46, 2, 33, '0000-00-00 00:00:00'),
(47, 2, 33, '0000-00-00 00:00:00'),
(48, 2, 33, '0000-00-00 00:00:00'),
(49, 2, 33, '2017-02-22 21:09:23'),
(50, 1, 35, '2017-02-22 21:42:38'),
(51, 2, 35, '2017-02-22 21:42:38'),
(52, 3, 34, '2017-02-22 21:41:35'),
(53, 3, 36, '2017-02-22 21:48:44'),
(54, 2, 36, '2017-02-22 21:48:44'),
(55, 2, 37, '2017-02-22 21:49:25'),
(56, 1, 37, '2017-02-22 21:49:25'),
(57, 2, 38, '2017-02-22 21:55:12'),
(58, 3, 39, '2017-02-22 21:58:11'),
(59, 2, 40, '2017-02-22 22:02:54'),
(60, 3, 41, '2017-02-22 00:00:00'),
(61, 2, 41, '2017-02-22 00:00:00'),
(62, 2, 42, '2017-02-23 09:11:44'),
(63, 3, 43, '2017-02-23 09:23:03'),
(64, 2, 44, '2017-02-23 09:23:25'),
(65, 2, 45, '2017-02-23 09:27:19'),
(66, 2, 46, '2017-02-23 09:30:06'),
(67, 2, 48, '2017-02-23 15:09:47'),
(68, 2, 47, '2017-02-23 13:30:57'),
(69, 1, 47, '2017-02-23 13:30:57'),
(70, 2, 49, '2017-02-23 17:24:21'),
(71, 3, 50, '2017-02-24 13:40:30'),
(72, 2, 51, '2017-02-24 14:29:24'),
(73, 2, 52, '2017-02-24 14:30:05'),
(74, 3, 53, '2017-02-24 14:30:30'),
(75, 3, 56, '2017-02-25 18:53:21'),
(76, 3, 57, '2017-02-25 19:03:56'),
(77, 2, 58, '2017-02-25 20:34:07'),
(78, 2, 55, '2017-02-25 17:05:25'),
(79, 2, 60, '2017-03-11 16:41:59'),
(80, 2, 59, '2017-03-11 11:48:09'),
(81, 1, 61, '2017-03-11 16:49:25'),
(82, 3, 63, '2017-03-12 13:37:57'),
(83, 1, 64, '2017-03-12 14:03:34'),
(84, 1, 65, '2017-03-12 14:10:50'),
(85, 3, 66, '2017-03-12 21:35:00'),
(86, 2, 72, '2017-03-12 22:32:19'),
(87, 3, 73, '2017-03-12 22:39:33'),
(88, 2, 75, '2017-03-12 22:40:59'),
(89, 1, 73, '2017-03-12 22:39:33'),
(90, 1, 72, '2017-03-12 22:32:19'),
(91, 2, 77, '2017-03-12 22:52:41'),
(92, 2, 78, '2017-03-12 23:09:35'),
(93, 3, 79, '2017-03-12 23:17:52'),
(94, 1, 80, '2017-03-17 14:35:01'),
(95, 3, 83, '2017-03-18 08:40:39'),
(96, 3, 85, '2017-03-18 08:49:04'),
(97, 3, 84, '2017-03-18 08:46:47'),
(98, 3, 86, '2017-03-18 08:58:44'),
(99, 3, 81, '2017-05-04 13:39:26'),
(100, 2, 80, '2017-03-17 14:35:01'),
(101, 2, 88, '2017-05-07 23:17:40'),
(102, 3, 92, '2017-05-10 23:22:38'),
(103, 3, 93, '2017-05-10 23:28:14'),
(104, 3, 94, '2017-05-11 19:03:50'),
(105, 3, 95, '2017-05-11 19:04:27'),
(106, 3, 96, '2017-05-11 19:05:00'),
(107, 2, 97, '2017-05-11 19:42:26'),
(108, 2, 98, '2017-05-11 19:55:01'),
(109, 3, 99, '2017-05-11 19:55:20'),
(110, 3, 100, '2017-05-12 20:22:34'),
(111, 2, 101, '2017-05-12 20:22:52'),
(112, 3, 102, '2017-05-12 20:23:31'),
(113, 3, 104, '0000-00-00 00:00:00'),
(114, 2, 104, '0000-00-00 00:00:00'),
(115, 2, 103, '2017-05-18 19:04:45'),
(116, 4, 104, '2017-05-19 14:43:44'),
(117, 1, 95, '2017-05-11 19:04:27'),
(118, 1, 89, '2017-05-10 23:03:04'),
(119, 3, 107, '2017-05-20 18:22:05'),
(120, 1, 108, '2017-05-20 18:34:05'),
(121, 1, 111, '2017-05-20 19:23:31'),
(122, 3, 112, '2017-05-20 19:33:48'),
(123, 2, 113, '2017-05-20 19:46:03'),
(124, 2, 111, '2017-05-20 19:23:31'),
(125, 2, 110, '2017-05-20 19:17:23'),
(126, 2, 109, '2017-05-20 19:16:38'),
(127, 2, 107, '2017-05-20 18:22:05'),
(128, 1, 114, '2017-05-20 19:48:13'),
(129, 3, 119, '2017-05-21 16:54:36'),
(130, 3, 118, '2017-05-21 16:44:41'),
(131, 2, 119, '2017-05-21 16:54:36'),
(132, 2, 118, '2017-05-21 16:44:41'),
(133, 4, 119, '2017-05-21 16:54:36'),
(134, 4, 118, '2017-05-21 16:44:41');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `audience_id` int(11) UNSIGNED NOT NULL,
  `audience` enum('timeline','group') NOT NULL DEFAULT 'timeline',
  `post` mediumtext NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `audience_id`, `audience`, `post`, `user_id`, `date_entered`) VALUES
(1, 1, 'timeline', 'Hello world!', 1, '2017-01-21 09:29:43'),
(7, 1, 'timeline', '\r\n', 1, '2017-01-22 13:59:22'),
(8, 1, 'timeline', 'A good evening to all my friends.', 1, '2017-02-22 16:08:03'),
(9, 3, 'timeline', 'It''s a beautiful friday!', 3, '2017-02-24 16:43:13'),
(10, 3, 'timeline', 'Nothing!', 3, '2017-02-25 00:13:25'),
(11, 3, 'timeline', 'Hello World!', 3, '2017-05-07 23:16:23'),
(12, 1, 'timeline', 'Testing 1, 2, ...', 1, '2017-05-19 20:35:08');

-- --------------------------------------------------------

--
-- Table structure for table `programmes`
--

CREATE TABLE `programmes` (
  `programme_id` int(11) UNSIGNED NOT NULL,
  `school_id` int(11) UNSIGNED NOT NULL,
  `programme_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `programmes`
--

INSERT INTO `programmes` (`programme_id`, `school_id`, `programme_name`) VALUES
(1, 1, 'BSc. in Software Engineering');

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `school_id` int(11) UNSIGNED NOT NULL,
  `college_id` int(11) UNSIGNED NOT NULL,
  `school_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`school_id`, `college_id`, `school_name`) VALUES
(1, 1, 'School of Computing and Informatics Technology');

-- --------------------------------------------------------

--
-- Table structure for table `shares`
--

CREATE TABLE `shares` (
  `share_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `sharer_id` int(11) UNSIGNED NOT NULL,
  `subject_type` enum('post','photo') NOT NULL,
  `date_shared` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `shares`
--

INSERT INTO `shares` (`share_id`, `subject_id`, `sharer_id`, `subject_type`, `date_shared`) VALUES
(1, 8, 3, 'post', '2017-02-22 18:13:53'),
(2, 8, 2, 'post', '2017-02-22 19:43:49'),
(3, 11, 3, 'photo', '2017-02-23 17:24:21'),
(4, 11, 1, 'photo', '2017-02-25 17:05:25'),
(5, 21, 1, 'photo', '2017-03-12 23:09:35'),
(6, 10, 1, 'post', '2017-05-04 13:39:26'),
(7, 4, 1, 'photo', '2017-05-10 23:28:14'),
(8, 2, 2, 'photo', '2017-05-11 19:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `dob` date NOT NULL,
  `other_names` varchar(60) NOT NULL,
  `lname` varchar(40) NOT NULL,
  `gender` enum('M','F') NOT NULL,
  `uname` varchar(50) NOT NULL,
  `passwd` char(255) NOT NULL,
  `profile_name` varchar(80) NOT NULL,
  `profile_pic_path` varchar(200) DEFAULT NULL,
  `logged_in` tinyint(1) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `dob`, `other_names`, `lname`, `gender`, `uname`, `passwd`, `profile_name`, `profile_pic_path`, `logged_in`, `date_created`) VALUES
(1, '1996-05-19', 'Robert Elvis', 'Odoch', 'M', 'xizo', '$2y$10$QWdE8EBiImCCht253lJ.v.baEutefoHUxCH6jnYzvSU00xCOGIVyC', 'Robert Elvis Odoch', '/opt/lampp/htdocs/makwire/uploads/small/linux_tux_penguin_logo_barcode_102200_1920x10801.jpg', 0, '2017-01-20 20:49:31'),
(2, '1996-01-01', 'Alex', 'Moruleng', 'M', 'moru', '$2y$10$zW.MTo0PHTSl4H.PUrDUi..ZE5E0RpNVTBTmjGdb7gbxCXk.AJYIK', 'Moruleng Alex', NULL, 0, '2017-01-20 20:49:31'),
(3, '1996-02-26', 'Ronald', 'Gubi', 'M', 'gubi', '$2y$10$gKYOCZboYUU2iHj4ZhXi4eXAocJGXp9m./9tRh9w9CiXKIm.PxbuW', 'Gubi Ronald', NULL, 0, '2017-01-20 20:49:31'),
(4, '1994-10-09', 'Pius', 'Owaro', 'M', 'obbo', '$2y$10$vq/saTgPJ3zvROcx04MLIOEqkWqCJtL7qYFVPFa4cIPrtGWfKu7aC', 'Owaro Pius', NULL, 0, '2017-01-20 20:49:32'),
(5, '0000-00-00', 'uncle', 'bob', 'M', 'uncle', '$2y$10$sE18AzAsrx55EdcQP7jo9OCf8x2BfOh7hdvkx0/epnhTRjpeqCsyq', 'Gubi Ronald', NULL, 1, '2017-03-21 19:44:30');

-- --------------------------------------------------------

--
-- Table structure for table `user_colleges`
--

CREATE TABLE `user_colleges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `college_id` int(11) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `level` enum('undergraduate','postgraduate') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_colleges`
--

INSERT INTO `user_colleges` (`id`, `user_id`, `college_id`, `date_from`, `date_to`, `level`) VALUES
(1, 1, 1, '2014-08-14', '2020-01-25', 'undergraduate');

-- --------------------------------------------------------

--
-- Table structure for table `user_emails`
--

CREATE TABLE `user_emails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `email` varchar(80) NOT NULL,
  `activation_code` char(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_emails`
--

INSERT INTO `user_emails` (`id`, `user_id`, `email`, `activation_code`) VALUES
(6, NULL, 'rodoch@cis.mak.ac.ug', NULL),
(7, NULL, 'xizofrey@cis.mak.ac.ug', 'd55b5db9d8425590fde6a19577d0464ffdac6302'),
(8, NULL, 'moru@cees.mak.ac.ug', '4c9efb83cc8909442471b61d055d7360ba8fc735');

-- --------------------------------------------------------

--
-- Table structure for table `user_halls`
--

CREATE TABLE `user_halls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `hall_id` int(11) UNSIGNED NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `resident` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_halls`
--

INSERT INTO `user_halls` (`id`, `user_id`, `hall_id`, `date_from`, `date_to`, `resident`) VALUES
(1, 1, 1, '2015-08-14', '2020-01-01', 0),
(2, 1, 4, '2013-01-01', '2014-01-01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_hostels`
--

CREATE TABLE `user_hostels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `hostel_id` int(11) UNSIGNED NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_hostels`
--

INSERT INTO `user_hostels` (`id`, `user_id`, `hostel_id`, `date_from`, `date_to`) VALUES
(1, 1, 1, '2017-01-01', '2023-01-01');

-- --------------------------------------------------------

--
-- Table structure for table `user_photos`
--

CREATE TABLE `user_photos` (
  `photo_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `image_type` varchar(15) NOT NULL,
  `full_path` varchar(200) NOT NULL,
  `description` text,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_photos`
--

INSERT INTO `user_photos` (`photo_id`, `user_id`, `image_type`, `full_path`, `description`, `date_entered`) VALUES
(1, 3, 'image/png', '/opt/lampp/htdocs/makwire/uploads/fille6.png', 'Really cool', '2017-05-05 17:07:49'),
(2, 3, 'image/jpeg', '/opt/lampp/htdocs/makwire/uploads/odoch-robert.jpg', 'Those days....', '2017-05-05 17:13:56'),
(3, 3, 'image/jpeg', '/opt/lampp/htdocs/makwire/uploads/linux_tux_penguin_logo_barcode_102200_1920x1080.jpg', '', '2017-05-05 18:04:25'),
(4, 3, 'image/png', '/opt/lampp/htdocs/makwire/uploads/fille4.png', 'Fille music forever!!!', '2017-05-05 18:09:27'),
(5, 1, 'image/jpeg', '/opt/lampp/htdocs/makwire/uploads/linus.jpeg', '', '2017-05-21 15:53:07'),
(6, 1, 'image/jpeg', '/opt/lampp/htdocs/makwire/uploads/unix.jpg', '', '2017-05-21 15:59:30'),
(7, 1, 'image/jpeg', '/opt/lampp/htdocs/makwire/uploads/dmr.jpg', 'Be a pointer my friend!', '2017-05-21 16:42:37'),
(8, 1, 'image/jpeg', '/opt/lampp/htdocs/makwire/uploads/odoch-robert2.jpg', NULL, '2017-05-21 16:44:41'),
(9, 1, 'image/jpeg', '/opt/lampp/htdocs/makwire/uploads/dmr1.jpg', NULL, '2017-05-21 16:54:36'),
(10, 1, 'image/jpeg', '/opt/lampp/htdocs/makwire/uploads/unix1.jpg', NULL, '2017-06-04 19:40:12'),
(11, 1, 'image/jpeg', '/opt/lampp/htdocs/makwire/uploads/unix2.jpg', NULL, '2017-06-04 19:41:00'),
(12, 1, 'image/jpeg', '/opt/lampp/htdocs/makwire/uploads/linux_tux_penguin_logo_barcode_102200_1920x10801.jpg', NULL, '2017-06-04 19:42:47');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `profile_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `district_id` int(11) UNSIGNED DEFAULT NULL,
  `country_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`profile_id`, `user_id`, `district_id`, `country_id`) VALUES
(1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_programmes`
--

CREATE TABLE `user_programmes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `programme_id` int(11) UNSIGNED NOT NULL,
  `user_school_id` bigint(20) UNSIGNED NOT NULL,
  `year_of_study` enum('0','1','2','3','4','5') NOT NULL,
  `graduated` tinyint(1) UNSIGNED NOT NULL,
  `last_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_programmes`
--

INSERT INTO `user_programmes` (`id`, `user_id`, `programme_id`, `user_school_id`, `year_of_study`, `graduated`, `last_updated`) VALUES
(2, 1, 1, 1, '0', 1, '2017-06-03 19:29:31');

-- --------------------------------------------------------

--
-- Table structure for table `user_schools`
--

CREATE TABLE `user_schools` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `school_id` int(11) UNSIGNED NOT NULL,
  `user_college_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_schools`
--

INSERT INTO `user_schools` (`id`, `user_id`, `school_id`, `user_college_id`) VALUES
(1, 1, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `birthday_messages`
--
ALTER TABLE `birthday_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`college_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`district_id`),
  ADD KEY `district_name` (`district_name`);
ALTER TABLE `districts` ADD FULLTEXT KEY `district_name_2` (`district_name`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `halls`
--
ALTER TABLE `halls`
  ADD PRIMARY KEY (`hall_id`);

--
-- Indexes for table `hostels`
--
ALTER TABLE `hostels`
  ADD PRIMARY KEY (`hostel_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`like_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `notification_read`
--
ALTER TABLE `notification_read`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexes for table `programmes`
--
ALTER TABLE `programmes`
  ADD PRIMARY KEY (`programme_id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`school_id`);

--
-- Indexes for table `shares`
--
ALTER TABLE `shares`
  ADD PRIMARY KEY (`share_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);
ALTER TABLE `users` ADD FULLTEXT KEY `profile_name` (`profile_name`);

--
-- Indexes for table `user_colleges`
--
ALTER TABLE `user_colleges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `college_id` (`college_id`);

--
-- Indexes for table `user_emails`
--
ALTER TABLE `user_emails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_halls`
--
ALTER TABLE `user_halls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_hostels`
--
ALTER TABLE `user_hostels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_photos`
--
ALTER TABLE `user_photos`
  ADD PRIMARY KEY (`photo_id`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`profile_id`);

--
-- Indexes for table `user_programmes`
--
ALTER TABLE `user_programmes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_schools`
--
ALTER TABLE `user_schools`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_college_id` (`user_college_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;
--
-- AUTO_INCREMENT for table `birthday_messages`
--
ALTER TABLE `birthday_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `colleges`
--
ALTER TABLE `colleges`
  MODIFY `college_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;
--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `country_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `district_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `request_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `halls`
--
ALTER TABLE `halls`
  MODIFY `hall_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `hostels`
--
ALTER TABLE `hostels`
  MODIFY `hostel_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `like_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `notification_read`
--
ALTER TABLE `notification_read`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `programmes`
--
ALTER TABLE `programmes`
  MODIFY `programme_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `school_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `shares`
--
ALTER TABLE `shares`
  MODIFY `share_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `user_colleges`
--
ALTER TABLE `user_colleges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `user_emails`
--
ALTER TABLE `user_emails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `user_halls`
--
ALTER TABLE `user_halls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user_hostels`
--
ALTER TABLE `user_hostels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `user_photos`
--
ALTER TABLE `user_photos`
  MODIFY `photo_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `profile_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `user_programmes`
--
ALTER TABLE `user_programmes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user_schools`
--
ALTER TABLE `user_schools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
