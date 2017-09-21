-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 21, 2017 at 01:59 PM
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
  `source_type` enum('post','comment','reply','photo','user','birthday_message','friend_request','video','link') NOT NULL,
  `activity` enum('like','comment','share','reply','post','photo','profile_pic_change','friend_request','confirmed_friend_request','birthday','message','video','link') NOT NULL,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

-- --------------------------------------------------------

--
-- Table structure for table `colleges`
--

CREATE TABLE `colleges` (
  `college_id` int(11) UNSIGNED NOT NULL,
  `college_name` varchar(100) NOT NULL,
  `short_name` varchar(10) NOT NULL,
  `domain` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `colleges`
--

INSERT INTO `colleges` (`college_id`, `college_name`, `short_name`, `domain`) VALUES
(1, 'College of Agricultural and Environmental Sciences', 'CAES', 'caes.mak.ac.ug'),
(2, 'College of Business and Management Sciences', 'CoBAMS', 'bams.mak.ac.ug'),
(3, 'College of Computing and Information Sciences', 'CoCIS', 'cis.mak.ac.ug'),
(4, 'College of Education and External Studies', 'CEES', 'cees.mak.ac.ug'),
(5, 'College of Engineering, Design, Art and Technology', 'CEDAT', 'cedat.mak.ac.ug'),
(6, 'College of Health Sciences', 'CHS', 'chs.mak.ac.ug'),
(7, 'College of Humanities and Social Sciences', 'CHUSS', 'chuss.mak.ac.ug'),
(8, 'College of Natural Sciences', 'CoNAS', 'cns.mak.ac.ug'),
(9, 'College of Veterinary Medicine, Animal Resources & Bio-security', 'CoVAB', 'covab.mak.ac.ug'),
(10, 'School of Law', 'LAW', 'law.mak.ac.ug');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` bigint(20) UNSIGNED NOT NULL,
  `commenter_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `source_id` bigint(20) UNSIGNED NOT NULL,
  `source_type` enum('post','photo','video','link','comment','birthday_message') NOT NULL,
  `comment` tinytext NOT NULL,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `region_id` tinyint(1) UNSIGNED NOT NULL,
  `country_id` int(11) UNSIGNED NOT NULL,
  `district_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`district_id`, `region_id`, `country_id`, `district_name`) VALUES
(1, 1, 1, 'Soroti'),
(2, 1, 1, 'Bududa'),
(3, 1, 1, 'Bugiri'),
(4, 1, 1, 'Busia'),
(5, 1, 1, 'Butaleja'),
(6, 1, 1, 'Iganga'),
(7, 1, 1, 'Jinja'),
(8, 1, 1, 'Kamuli'),
(9, 1, 1, 'Kapchorwa'),
(10, 1, 1, 'Katakwi'),
(11, 1, 1, 'Kayunga'),
(12, 1, 1, 'Kibuku'),
(13, 1, 1, 'Kumi'),
(14, 1, 1, 'Luuka'),
(15, 1, 1, 'Manafwa'),
(16, 1, 1, 'Mayuge'),
(17, 1, 1, 'Mbale'),
(18, 1, 1, 'Namatumba'),
(19, 1, 1, 'Ngora'),
(20, 1, 1, 'Pallisa'),
(21, 1, 1, 'Tororo'),
(22, 2, 1, 'Butambala'),
(23, 2, 1, 'Buvuma'),
(24, 2, 1, 'Buikwe'),
(25, 2, 1, 'Mukono'),
(26, 2, 1, 'Nakaseke'),
(27, 2, 1, 'Mubende'),
(28, 2, 1, 'Kalangala'),
(29, 2, 1, 'Rakai'),
(30, 2, 1, 'Kampala'),
(31, 2, 1, 'Mityana'),
(32, 2, 1, 'Luwero'),
(33, 2, 1, 'Mpigi'),
(34, 2, 1, 'Wakiso'),
(35, 2, 1, 'Kalungu'),
(36, 2, 1, 'Lyantonde'),
(37, 2, 1, 'Masaka'),
(38, 3, 1, 'Bushenyi'),
(39, 3, 1, 'Kabarole'),
(40, 3, 1, 'Kasese'),
(41, 3, 1, 'Bundibugyo'),
(42, 3, 1, 'Ibanda'),
(43, 3, 1, 'Mbarara'),
(44, 3, 1, 'Kisoro'),
(45, 3, 1, 'Kiruhura'),
(46, 3, 1, 'Kanungu'),
(47, 3, 1, 'Isingiro'),
(48, 3, 1, 'Kyenjojo'),
(49, 3, 1, 'Rukungiri'),
(50, 3, 1, 'Kabale'),
(51, 3, 1, 'Ntungamo'),
(52, 3, 1, 'Kamwenge'),
(53, 3, 1, 'Ntoroko'),
(54, 3, 1, 'Sheema'),
(55, 3, 1, 'Kyegegwa'),
(56, 4, 1, 'Agago'),
(57, 4, 1, 'Pader'),
(58, 4, 1, 'Lira'),
(59, 4, 1, 'Alebtong'),
(60, 4, 1, 'Otuke'),
(61, 4, 1, 'Amuru'),
(62, 4, 1, 'Nwoya'),
(63, 4, 1, 'Kitgum'),
(64, 4, 1, 'Apac'),
(65, 4, 1, 'Kole'),
(66, 4, 1, 'Gulu'),
(67, 4, 1, 'Amolatar'),
(68, 4, 1, 'Dokolo'),
(69, 4, 1, 'Oyam'),
(70, 4, 1, 'Kaberamaido'),
(71, 4, 1, 'Kaabong'),
(72, 4, 1, 'Kotido'),
(73, 5, 1, 'Moyo'),
(74, 5, 1, 'Buliisa'),
(75, 5, 1, 'Kibaale'),
(76, 5, 1, 'Yumbe'),
(77, 5, 1, 'Nakasongola'),
(78, 5, 1, 'Hoima'),
(79, 5, 1, 'Masindi'),
(80, 5, 1, 'Kiryandongo'),
(81, 5, 1, 'Arua'),
(82, 5, 1, 'Kiboga'),
(83, 5, 1, 'Nebbi'),
(84, 5, 1, 'Koboko'),
(85, 5, 1, 'Zombo');

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `friend_id` int(11) UNSIGNED NOT NULL,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

-- --------------------------------------------------------

--
-- Table structure for table `halls`
--

CREATE TABLE `halls` (
  `hall_id` int(11) UNSIGNED NOT NULL,
  `hall_name` varchar(30) NOT NULL,
  `gender` enum('M','F') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `halls`
--

INSERT INTO `halls` (`hall_id`, `hall_name`, `gender`) VALUES
(1, 'Africa Hall', 'F'),
(2, 'Complex Hall', 'F'),
(3, 'Livingstone Hall', 'M'),
(4, 'Lumumba Hall', 'M'),
(5, 'Mary Stuart Hall', 'F'),
(6, 'Mitchell Hall', 'M'),
(7, 'Nkrumah Hall', 'M'),
(8, 'Nsibirwa Hall', 'M'),
(9, 'University Hall', 'M');

-- --------------------------------------------------------

--
-- Table structure for table `hostels`
--

CREATE TABLE `hostels` (
  `hostel_id` int(11) UNSIGNED NOT NULL,
  `hostel_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `like_id` bigint(20) UNSIGNED NOT NULL,
  `source_id` bigint(20) UNSIGNED NOT NULL,
  `source_type` enum('post','photo','video','link','comment','reply','birthday_message') NOT NULL,
  `liker_id` int(11) UNSIGNED NOT NULL,
  `date_liked` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE `links` (
  `link_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `url` varchar(500) NOT NULL,
  `title` tinytext NOT NULL,
  `description` tinytext NOT NULL,
  `image` varchar(1000) NOT NULL,
  `site` varchar(100) NOT NULL,
  `comment` text,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_token`
--

CREATE TABLE `password_reset_token` (
  `email` varchar(80) NOT NULL,
  `token` varchar(40) NOT NULL,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE `photos` (
  `photo_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `file_type` varchar(15) NOT NULL,
  `full_path` varchar(200) NOT NULL,
  `description` text,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

-- --------------------------------------------------------

--
-- Table structure for table `programmes`
--

CREATE TABLE `programmes` (
  `programme_id` int(11) UNSIGNED NOT NULL,
  `school_id` int(11) UNSIGNED NOT NULL,
  `programme_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
(1, 1, 'School of Agricultural Sciences'),
(2, 1, 'School of Forestry, Environmental and Geographical Sciences'),
(3, 1, 'School of Food Technology, Nutrition and Bio-engineering'),
(4, 2, 'School of Economics'),
(5, 2, 'School of Business'),
(6, 2, 'School of Statistics and Applied Economics'),
(7, 3, 'School of Computing and Informatics Technology'),
(8, 3, 'East African School of Library and Information Sciences'),
(9, 4, 'School of Education'),
(10, 4, 'School of Distance and Lifelong Learning'),
(11, 5, 'School of Engineering'),
(12, 5, 'School of the Built Environment'),
(13, 5, 'Margaret Trowell School of Industrial and Fine Art'),
(14, 6, 'School of Medicine'),
(15, 6, 'School of Public Health'),
(16, 6, 'School of Biomedical Sciences'),
(17, 6, 'School of Health Sciences'),
(18, 7, 'School of Liberal and Performing Arts'),
(19, 7, 'School of Women and Gender Studies'),
(20, 7, 'School of Languages, Literature and Communication'),
(21, 7, 'School of Psychology'),
(22, 7, 'School of Social Sciences'),
(23, 7, 'Makerere Institute of Social Research'),
(24, 8, 'School of Physical Sciences'),
(25, 8, 'School of Biological Sciences'),
(26, 9, 'School of Bio-security, Biotechnical and Laboratory Sciences'),
(27, 9, 'School of Veterinary and Animal Resources'),
(28, 10, 'School of Law');

-- --------------------------------------------------------

--
-- Table structure for table `shares`
--

CREATE TABLE `shares` (
  `share_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `sharer_id` int(11) UNSIGNED NOT NULL,
  `subject_type` enum('post','photo','video','link') NOT NULL,
  `date_shared` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uganda_regions`
--

CREATE TABLE `uganda_regions` (
  `id` tinyint(1) UNSIGNED NOT NULL,
  `region` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `uganda_regions`
--

INSERT INTO `uganda_regions` (`id`, `region`) VALUES
(1, 'Eastern'),
(2, 'Central'),
(3, 'South Western'),
(4, 'Northern'),
(5, 'Western');

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
  `district_id` int(10) UNSIGNED DEFAULT NULL,
  `last_activity` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_emails`
--

CREATE TABLE `user_emails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `email` varchar(80) NOT NULL,
  `activation_code` char(40) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL,
  `is_backup` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

-- --------------------------------------------------------

--
-- Table structure for table `user_schools`
--

CREATE TABLE `user_schools` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `school_id` int(11) UNSIGNED NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `level` enum('undergraduate','graduate') NOT NULL,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_unfollow`
--

CREATE TABLE `user_unfollow` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `follower_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `video_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `url` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `actor_id` (`actor_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `birthday_messages`
--
ALTER TABLE `birthday_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`college_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `commenter_id` (`commenter_id`);

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
  ADD KEY `district_name` (`district_name`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `region_id` (`region_id`);
ALTER TABLE `districts` ADD FULLTEXT KEY `district_name_2` (`district_name`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `friend_id` (`friend_id`);

--
-- Indexes for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `target_id` (`target_id`),
  ADD KEY `user_id` (`user_id`);

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
  ADD PRIMARY KEY (`like_id`),
  ADD KEY `liker_id` (`liker_id`);

--
-- Indexes for table `links`
--
ALTER TABLE `links`
  ADD PRIMARY KEY (`link_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `sender_id_2` (`sender_id`,`receiver_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `notification_read`
--
ALTER TABLE `notification_read`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `user_id` (`user_id`,`activity_id`);

--
-- Indexes for table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`photo_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `programmes`
--
ALTER TABLE `programmes`
  ADD PRIMARY KEY (`programme_id`),
  ADD KEY `school_id` (`school_id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`school_id`),
  ADD KEY `college_id` (`college_id`);

--
-- Indexes for table `shares`
--
ALTER TABLE `shares`
  ADD PRIMARY KEY (`share_id`),
  ADD KEY `sharer_id` (`sharer_id`);

--
-- Indexes for table `uganda_regions`
--
ALTER TABLE `uganda_regions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `district_id` (`district_id`);
ALTER TABLE `users` ADD FULLTEXT KEY `profile_name` (`profile_name`);

--
-- Indexes for table `user_emails`
--
ALTER TABLE `user_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_halls`
--
ALTER TABLE `user_halls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`hall_id`),
  ADD KEY `hall_id` (`hall_id`);

--
-- Indexes for table `user_hostels`
--
ALTER TABLE `user_hostels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`hostel_id`),
  ADD KEY `hostel_id` (`hostel_id`);

--
-- Indexes for table `user_programmes`
--
ALTER TABLE `user_programmes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`programme_id`,`user_school_id`),
  ADD KEY `programme_id` (`programme_id`),
  ADD KEY `user_school_id` (`user_school_id`);

--
-- Indexes for table `user_schools`
--
ALTER TABLE `user_schools`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`school_id`),
  ADD KEY `school_id` (`school_id`);

--
-- Indexes for table `user_unfollow`
--
ALTER TABLE `user_unfollow`
  ADD PRIMARY KEY (`id`),
  ADD KEY `follower_id` (`follower_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`video_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `birthday_messages`
--
ALTER TABLE `birthday_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `colleges`
--
ALTER TABLE `colleges`
  MODIFY `college_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `country_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `district_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `request_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `halls`
--
ALTER TABLE `halls`
  MODIFY `hall_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `hostels`
--
ALTER TABLE `hostels`
  MODIFY `hostel_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `like_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `links`
--
ALTER TABLE `links`
  MODIFY `link_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notification_read`
--
ALTER TABLE `notification_read`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `photo_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `programmes`
--
ALTER TABLE `programmes`
  MODIFY `programme_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `school_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `shares`
--
ALTER TABLE `shares`
  MODIFY `share_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `uganda_regions`
--
ALTER TABLE `uganda_regions`
  MODIFY `id` tinyint(1) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_emails`
--
ALTER TABLE `user_emails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_halls`
--
ALTER TABLE `user_halls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_hostels`
--
ALTER TABLE `user_hostels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_programmes`
--
ALTER TABLE `user_programmes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_schools`
--
ALTER TABLE `user_schools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_unfollow`
--
ALTER TABLE `user_unfollow`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `video_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`actor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `activities_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `birthday_messages`
--
ALTER TABLE `birthday_messages`
  ADD CONSTRAINT `birthday_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `birthday_messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`commenter_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `districts`
--
ALTER TABLE `districts`
  ADD CONSTRAINT `districts_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`country_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `districts_ibfk_2` FOREIGN KEY (`region_id`) REFERENCES `uganda_regions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD CONSTRAINT `friend_requests_ibfk_1` FOREIGN KEY (`target_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `friend_requests_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`liker_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `links`
--
ALTER TABLE `links`
  ADD CONSTRAINT `links_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification_read`
--
ALTER TABLE `notification_read`
  ADD CONSTRAINT `notification_read_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `programmes`
--
ALTER TABLE `programmes`
  ADD CONSTRAINT `programmes_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `schools` (`school_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `schools`
--
ALTER TABLE `schools`
  ADD CONSTRAINT `schools_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `shares`
--
ALTER TABLE `shares`
  ADD CONSTRAINT `shares_ibfk_1` FOREIGN KEY (`sharer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_emails`
--
ALTER TABLE `user_emails`
  ADD CONSTRAINT `user_emails_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_halls`
--
ALTER TABLE `user_halls`
  ADD CONSTRAINT `user_halls_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_halls_ibfk_2` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`hall_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_hostels`
--
ALTER TABLE `user_hostels`
  ADD CONSTRAINT `user_hostels_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_hostels_ibfk_2` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`hostel_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_programmes`
--
ALTER TABLE `user_programmes`
  ADD CONSTRAINT `user_programmes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_programmes_ibfk_2` FOREIGN KEY (`programme_id`) REFERENCES `programmes` (`programme_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_programmes_ibfk_3` FOREIGN KEY (`user_school_id`) REFERENCES `user_schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_schools`
--
ALTER TABLE `user_schools`
  ADD CONSTRAINT `user_schools_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_schools_ibfk_2` FOREIGN KEY (`school_id`) REFERENCES `schools` (`school_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_unfollow`
--
ALTER TABLE `user_unfollow`
  ADD CONSTRAINT `user_unfollow_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_unfollow_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
