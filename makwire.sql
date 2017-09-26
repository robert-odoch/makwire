-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 26, 2017 at 11:27 PM
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
  `college_id` int(11) UNSIGNED NOT NULL,
  `programme_name` varchar(200) NOT NULL,
  `level` enum('undergraduate','graduate','postgraduate') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `programmes`
--

INSERT INTO `programmes` (`programme_id`, `college_id`, `programme_name`, `level`) VALUES
(1, 1, 'BSc. in Forestry', 'undergraduate'),
(2, 1, 'BSc. in Meteorology', 'undergraduate'),
(3, 1, 'BSc. in Agriculture', 'undergraduate'),
(4, 1, 'BSc. in Horticulture', 'undergraduate'),
(5, 1, 'BSc. in Human Nutrition', 'undergraduate'),
(6, 1, 'BSc. in Agricultural Engineering', 'undergraduate'),
(7, 1, 'BSc. in Food Science and Technology', 'undergraduate'),
(8, 1, 'BSc. in Tourism and Hospitality Management', 'undergraduate'),
(9, 1, 'BSc. in Agricultural Land Use and Management', 'undergraduate'),
(10, 1, 'BSc. in Conservation Forestry and Products Technology', 'undergraduate'),
(11, 1, 'Bachelor of Community Forestry', 'undergraduate'),
(12, 1, 'Bachelor of Geographical Sciences', 'undergraduate'),
(13, 1, 'Bachelor of Environmental Science', 'undergraduate'),
(14, 1, 'Bachelor of Agribusiness Management', 'undergraduate'),
(15, 1, 'Bachelor of Environmental Health Science', 'undergraduate'),
(16, 1, 'Bachelor of Agricultural Extension Education', 'undergraduate'),
(17, 1, 'Bachelor of Agricultural and Rural Innovation', 'undergraduate'),
(18, 1, 'Bachelor of Social and Entrepreneurial Forestry', 'undergraduate'),
(19, 1, 'Diploma in Livestock Products Development and Entreprenuership', 'undergraduate'),
(20, 1, 'MSc. in Forestry', 'graduate'),
(21, 1, 'MSc. in Agroforestry', 'graduate'),
(22, 1, 'MSc. in Soil Science', 'graduate'),
(23, 1, 'MSc. in Crop Science', 'graduate'),
(24, 1, 'MSc. in Animal Science', 'graduate'),
(25, 1, 'MSc. in Applied Human Nutrition', 'graduate'),
(26, 1, 'MSc. in Agricultural Engineering', 'graduate'),
(27, 1, 'MSc. in Food Science and Technology', 'graduate'),
(28, 1, 'MSc. in Integrated Watershed Management', 'graduate'),
(29, 1, 'MSc. in Plant Breeding and Seed Systems', 'graduate'),
(30, 1, 'MSc. in Agricultural Extension Education', 'graduate'),
(31, 1, 'MSc. in Environment and Natural Resources', 'graduate'),
(32, 1, 'MSc. in Agricultural and Applied Economics', 'graduate'),
(33, 1, 'M.A. in Geography', 'graduate'),
(34, 1, 'Master of Agribusiness Management', 'graduate'),
(35, 1, 'PhD in Plant Breeding and Biotechnology', 'graduate'),
(36, 1, 'PGD in Meteorology', 'postgraduate'),
(37, 1, 'PGD in Environmental Impact Assessment', 'postgraduate'),
(38, 1, 'PGD in Environmental Information Management', 'postgraduate'),
(39, 2, 'BSc. in Actuarial Science', 'undergraduate'),
(40, 2, 'BSc. in Population Studies', 'undergraduate'),
(41, 2, 'BSc. in Business Statistics', 'undergraduate'),
(42, 2, 'BSc. in Quantitative Economics', 'undergraduate'),
(43, 2, 'B.A. in Economics', 'undergraduate'),
(44, 2, 'B.A. in Development Economics', 'undergraduate'),
(45, 2, 'Bachelor of Commerce', 'undergraduate'),
(46, 2, 'Bachelor of Statistics', 'undergraduate'),
(47, 2, 'Bachelor of International Business', 'undergraduate'),
(48, 2, 'Bachelor of Business Administration', 'undergraduate'),
(49, 2, 'Bachelor of Human Resource Management', 'undergraduate'),
(50, 2, 'Bachelor of Procurement and Supplies Management', 'undergraduate'),
(51, 2, 'MSc. in Population Studies', 'graduate'),
(52, 2, 'MSc. in Quantitative Economics', 'graduate'),
(53, 2, 'M.A. in Economics', 'graduate'),
(54, 2, 'M.A. in Demography', 'graduate'),
(55, 2, 'M.A. in Economic Policy Management', 'graduate'),
(56, 2, 'M.A. in Population and Development', 'graduate'),
(57, 2, 'M.A. in Economic Policy and Planning', 'graduate'),
(58, 2, 'M.A. in Gender Analysis in Economics', 'graduate'),
(59, 2, 'Master of Statistics', 'graduate'),
(60, 2, 'Master of Business Administration', 'graduate'),
(61, 2, 'PhD in Economics', 'graduate'),
(62, 2, 'PhD in Statistics', 'graduate'),
(63, 2, 'PGD in Statistics', 'postgraduate'),
(64, 2, 'PGD in Demography', 'postgraduate'),
(65, 3, 'BSc. in Computer Science', 'undergraduate'),
(66, 3, 'BSc. in Software Engineering', 'undergraduate'),
(67, 3, 'Bachelor of Information Systems', 'undergraduate'),
(68, 3, 'Bachelor of Information Technology', 'undergraduate'),
(69, 3, 'Bachelor of Information Systems and Technology', 'undergraduate'),
(70, 3, 'Bachelor of Library and Information Science', 'undergraduate'),
(71, 3, 'Bachelor of Records and Archives Management', 'undergraduate'),
(72, 3, 'MSc. in Computer Science', 'graduate'),
(73, 3, 'MSc. in Information Science', 'graduate'),
(74, 3, 'Msc. in Information Systems', 'graduate'),
(75, 3, 'MSc. in Data Communications and Software Engineering', 'graduate'),
(76, 3, 'Master of Information Technology', 'graduate'),
(77, 3, 'PhD in Computer Science', 'graduate'),
(78, 3, 'PhD in Information Systems', 'graduate'),
(79, 3, 'PhD in Information Science', 'graduate'),
(80, 3, 'PhD in Software Engineering', 'graduate'),
(81, 3, 'PhD in Information Technology', 'graduate'),
(82, 3, 'PGD in Computer Science', 'postgraduate'),
(83, 3, 'PGD in Information Systems', 'postgraduate'),
(84, 3, 'PGD in Information Technology', 'postgraduate'),
(85, 3, 'PGD in Data Communications and Software Engineering', 'postgraduate'),
(86, 4, 'BSc. with Education (Physical)', 'undergraduate'),
(87, 4, 'BSc. with Education (Economics)', 'undergraduate'),
(88, 4, 'BSc. with Education (Biological)', 'undergraduate'),
(89, 4, 'B.A. with Education', 'undergraduate'),
(90, 4, 'Bachelor of Medical Education', 'undergraduate'),
(91, 4, 'Bachelor of Science (External)', 'undergraduate'),
(92, 4, 'Bachelor of Commerce (External)', 'undergraduate'),
(93, 4, 'Bachelor of Education (External)', 'undergraduate'),
(94, 4, 'Bachelor of Adult and Community Education', 'undergraduate'),
(95, 4, 'Bachelor of Agricultural and Rural Innovation (External)', 'undergraduate'),
(96, 4, 'Higher Diploma for Clinical Instruction', 'undergraduate'),
(97, 4, 'M.Ed. in Science Education', 'graduate'),
(98, 4, 'M.Ed. in Curriculum Studies', 'graduate'),
(99, 4, 'M.Ed. in Educational Foundations', 'graduate'),
(100, 4, 'M.Ed. in Language and Literature Education', 'graduate'),
(101, 4, 'M.Ed. in Social Sciences and Arts Education', 'graduate'),
(102, 4, 'M.A. in Educational Policy and Planning', 'graduate'),
(103, 4, 'Master of Adult and Community Education', 'graduate'),
(104, 4, 'PGD in Education', 'postgraduate'),
(105, 5, 'BSc. in Land Economics', 'undergraduate'),
(106, 5, 'BSc. in Civil Engineering', 'undergraduate'),
(107, 5, 'BSc. in Quantity Surveying', 'undergraduate'),
(108, 5, 'BSc. in Computer Engineering', 'undergraduate'),
(109, 5, 'BSc. in Electrical Engineering', 'undergraduate'),
(110, 5, 'BSc. in Mechanical Engineering', 'undergraduate'),
(111, 5, 'BSc. in Construction Management', 'undergraduate'),
(112, 5, 'BSc. in Land Surveying and Geomatics', 'undergraduate'),
(113, 5, 'BSc. in Telecommunications Engineering', 'undergraduate'),
(114, 5, 'Bachelor of Architecture', 'undergraduate'),
(115, 5, 'Bachelor of Industrial and Fine Arts', 'undergraduate'),
(116, 5, 'Bachelor of Urban and Regional Planning', 'undergraduate'),
(117, 5, 'MSc. in Civil Engineering', 'graduate'),
(118, 5, 'MSc. in Renewable Energy', 'graduate'),
(119, 5, 'MSc. in Mechanical Engineering', 'graduate'),
(120, 5, 'MSc. in Electrical Engineering', 'graduate'),
(121, 5, 'M.A. in Fine Art', 'graduate'),
(122, 5, 'Master of Architecture', 'graduate'),
(123, 5, 'Master of Engineering (Civil)', 'graduate'),
(124, 5, 'Master of Engineering (Electrical)', 'graduate'),
(125, 5, 'Master of Engineering (Mechanical)', 'graduate'),
(126, 5, 'PhD in Engineering', 'graduate'),
(127, 5, 'PhD in Industrial and Fine Art', 'graduate'),
(128, 5, 'PhD in Technology (Surveying)', 'graduate'),
(129, 5, 'PhD in Technology (Architecture)', 'graduate'),
(130, 5, 'PhD in Technology (Civil Engineering)', 'graduate'),
(131, 5, 'PhD in Technology (Construction Management)', 'graduate'),
(132, 5, 'PhD in Technology (Electrical Engineering)', 'graduate'),
(133, 5, 'PhD in Technology (Mechanical Engineering)', 'graduate'),
(134, 5, 'PGD in Urban Planning and Design', 'postgraduate'),
(135, 5, 'PGD in Construction Project Management', 'postgraduate'),
(136, 6, 'BSc. in Nursing', 'undergraduate'),
(153, 6, 'BSc. in Medical Radiography', 'undergraduate'),
(154, 6, 'BSc. in Speech and Language Therapy', 'undergraduate'),
(155, 6, 'BSc. in Biomedical Sciences', 'undergraduate'),
(156, 6, 'BSc. in Biomedical Engineering', 'undergraduate'),
(157, 6, 'Bachelor of Pharmacy', 'undergraduate'),
(158, 6, 'Bachelor of Optometry', 'undergraduate'),
(159, 6, 'Bachelor of Cytotechnology', 'undergraduate'),
(160, 6, 'Bachelor of Dental Surgery', 'undergraduate'),
(161, 6, 'Bachelor of Medicine and Bachelor of Surgery', 'undergraduate'),
(162, 6, 'Diploma in Palliative Care', 'undergraduate'),
(163, 6, 'Diploma in Clinical Psychiatry', 'undergraduate'),
(164, 6, 'Diploma in Public Health Nursing', 'undergraduate'),
(165, 6, 'Diploma in Environmental Health Sciences', 'undergraduate'),
(166, 6, 'Diploma in Medical Laboratory Technology', 'undergraduate'),
(167, 6, 'MSc. in Physiology', 'graduate'),
(168, 6, 'MSc. in Pharmacology', 'graduate'),
(169, 6, 'MSc. in Human Anatomy', 'graduate'),
(170, 6, 'MSc. in Medical Illustration', 'graduate'),
(171, 6, 'MSc. in Clinical Epidemiology and Biostatistics', 'graduate'),
(172, 6, 'M.Med Pathology', 'graduate'),
(173, 6, 'M.Med Radiology', 'graduate'),
(174, 6, 'M.Med Psychiatry', 'graduate'),
(175, 6, 'M.Med Anesthesia', 'graduate'),
(176, 6, 'M.Med Microbiology', 'graduate'),
(177, 6, 'M.Med Ophthalmology', 'graduate'),
(178, 6, 'M.Med Family Medicine', 'graduate'),
(179, 6, 'M.Med General Surgery', 'graduate'),
(180, 6, 'M.Med Internal Medicine', 'graduate'),
(181, 6, 'M.Med Orthopedic Surgery', 'graduate'),
(182, 6, 'M.Med Ear, Nose and Throat', 'graduate'),
(183, 6, 'M.Med Paediatrics and Child Health', 'graduate'),
(184, 6, 'M.Med Obstetrics and Gynecology', 'graduate'),
(185, 6, 'Master of Public Health', 'graduate'),
(186, 6, 'Master of Public Health Nutrition', 'graduate'),
(187, 6, 'Master of Health Services Research', 'graduate'),
(188, 6, 'Master of Dentistry (Oral and Maxillofacial Surgery)', 'graduate'),
(189, 6, 'PhD in Medicine', 'graduate'),
(190, 6, 'PhD in Public Health', 'graduate'),
(191, 6, 'PhD in Health Sciences', 'graduate'),
(192, 6, 'PhD in Biomedical Science', 'graduate'),
(193, 6, 'PhD in Biomedical Laboratory Sciences', 'graduate'),
(194, 6, 'PhD in Ecosystem Health and Production', 'graduate'),
(195, 6, 'PhD in Laboratory Sciences and Management', 'graduate'),
(196, 6, 'PGD in Anesthesia', 'postgraduate'),
(197, 6, 'PGD in Gynecology', 'postgraduate'),
(198, 6, 'PGD in Public Health', 'postgraduate'),
(199, 6, 'PGD in Quality of Health Care', 'postgraduate'),
(200, 7, 'B.A. in Music', 'undergraduate'),
(201, 7, 'B.A. in Drama and Film', 'undergraduate'),
(202, 7, 'Bachelor of Arts (Arts)', 'undergraduate'),
(203, 7, 'Bachelor of Arts (Social Sciences)', 'undergraduate'),
(204, 7, 'Bachelor of Philosophy', 'undergraduate'),
(205, 7, 'Bachelor of Mass Communication', 'undergraduate'),
(206, 7, 'Bachelor of Development Studies', 'undergraduate'),
(207, 7, 'Bachelor of Secretarial Studies', 'undergraduate'),
(208, 7, 'Bachelor of Community Psychology', 'undergraduate'),
(209, 7, 'Bachelor of Ethics and Human Rights', 'undergraduate'),
(210, 7, 'Bachelor of Journalism and Communication', 'undergraduate'),
(211, 7, 'Bachelor of Archeology and Heritage Studies', 'undergraduate'),
(212, 7, 'Bachelor of Social and Philosophical Studies', 'undergraduate'),
(213, 7, 'Bachelor of Social Work and Social Administration', 'undergraduate'),
(214, 7, 'Bachelor of Industrial and Organisational Psychology', 'undergraduate'),
(215, 7, 'Diploma in Performing Arts', 'undergraduate'),
(216, 7, 'Diploma in Music, Dance and Drama', 'undergraduate'),
(217, 7, 'Diploma in Youth Development Work', 'undergraduate'),
(218, 7, 'Diploma in Refugee Law and Forced Migration', 'undergraduate'),
(219, 7, 'MSc. in Clinical Psychology', 'graduate'),
(220, 7, 'M.A. in Music', 'graduate'),
(221, 7, 'M.A. in History', 'graduate'),
(222, 7, 'M.A. in Sociology', 'graduate'),
(223, 7, 'M.A. in Philosophy', 'graduate'),
(224, 7, 'M.A. in Literature', 'graduate'),
(225, 7, 'M.A. in Counseling', 'graduate'),
(226, 7, 'M.A. in Human Rights', 'graduate'),
(227, 7, 'M.A. in Languages (foreign)', 'graduate'),
(228, 7, 'M.A. in Linguistics', 'graduate'),
(229, 7, 'M.A. in Gender Studies', 'graduate'),
(230, 7, 'M.A. in Religious Studies', 'graduate'),
(231, 7, 'M.A. in African Languages', 'graduate'),
(232, 7, 'M.A. in Rural Development', 'graduate'),
(233, 7, 'M.A. in Peace and Conflict Studies', 'graduate'),
(234, 7, 'M.A. in Journalism and Communication', 'graduate'),
(235, 7, 'M.A. in Ethics and Public Management', 'graduate'),
(236, 7, 'M.A. in Social and Management Studies', 'graduate'),
(237, 7, 'M.A. in Community Based Rehabilitation', 'graduate'),
(238, 7, 'M.A. in Public Administration and Management', 'graduate'),
(239, 7, 'M.A. in Social Sector Planning and Management', 'graduate'),
(240, 7, 'M.A. in Leadership and Human Relation Studies', 'graduate'),
(241, 7, 'M.A. in Religious and Theological Studies (Ggaba and Kinyamasika)', 'graduate'),
(242, 7, 'M.A. in International Relations and Diplomatic Studies', 'graduate'),
(243, 7, 'M.Ed. in Educational Psychology', 'graduate'),
(244, 7, 'Master of Organisational Psychology', 'graduate'),
(245, 7, 'PGD in Mass Communication', 'postgraduate'),
(246, 7, 'PGD in Guidance and Counseling', 'postgraduate'),
(247, 7, 'PGD in Investigative Journalism', 'postgraduate'),
(248, 7, 'PGD in Theology and Pastoral Studies', 'postgraduate'),
(249, 7, 'PGD in Translation and Interpretation Studies', 'postgraduate'),
(250, 7, 'PGD in Gender and Local Economic Development', 'postgraduate'),
(251, 7, 'PGD in Environmental Journalism and Communication', 'postgraduate'),
(252, 8, 'BSc. in Biotechnology', 'undergraduate'),
(253, 8, 'BSc. in Conservation Biology', 'undergraduate'),
(254, 8, 'BSc. in Industrial Chemistry', 'undergraduate'),
(255, 8, 'BSc. in Petroleum Geoscience and Production', 'undergraduate'),
(256, 8, 'BSc. in Fisheries and Aquaculture', 'undergraduate'),
(257, 8, 'Bachelor of Science (Physical)', 'undergraduate'),
(258, 8, 'Bachelor of Science (Economics)', 'undergraduate'),
(259, 8, 'Bachelor of Science (Biological)', 'undergraduate'),
(260, 8, 'Bachelor of Sports Science', 'undergraduate'),
(261, 8, 'MSc. in Botany', 'graduate'),
(262, 8, 'MSc. in Zoology', 'graduate'),
(263, 8, 'MSc. in Geology', 'graduate'),
(264, 8, 'MSc. in Physics', 'graduate'),
(265, 8, 'MSc. in Chemistry', 'graduate'),
(266, 8, 'MSc. in Mathematics', 'graduate'),
(267, 8, 'MSc. in Biochemistry', 'graduate'),
(268, 8, 'MSc. in Mathematical Modeling', 'graduate'),
(269, 9, 'BSc. in Wildlife Health and Management', 'undergraduate'),
(270, 9, 'Bachelor of Veterinary Medicine', 'undergraduate'),
(271, 9, 'Bachelor of Biomedical Laboratory Technology', 'undergraduate'),
(272, 9, 'Bachelor of Industrial Livestock and Business', 'undergraduate'),
(273, 9, 'Bachelor of Animal Production Technology and Management', 'undergraduate'),
(274, 9, 'Diploma in Pig Industry and Business', 'undergraduate'),
(275, 9, 'Diploma in Bee Industry and Business', 'undergraduate'),
(276, 9, 'Diploma in Dairy Industry and Busines', 'undergraduate'),
(277, 9, 'Diploma in Feed Industry and Business', 'undergraduate'),
(278, 9, 'Diploma in Fish Industry and Business', 'undergraduate'),
(279, 9, 'Diploma in Meat Industry and Business', 'undergraduate'),
(280, 9, 'Diploma in Leather Industry and Business', 'undergraduate'),
(281, 9, 'Diploma in Poultry Industry and Business', 'undergraduate'),
(282, 9, 'Diploma in Laboratory Science Education and Industry', 'undergraduate'),
(283, 9, 'Diploma in Pet and Recreational Industry and Business', 'undergraduate'),
(284, 9, 'MSc. in Molecular Biology', 'graduate'),
(285, 9, 'MSc. in Veterinary Pathology', 'graduate'),
(286, 9, 'MSc. in Livestock Development Planning and Management', 'graduate'),
(287, 9, 'MSc. in Natural Products Technology and Value Chain Management', 'graduate'),
(288, 9, 'MSc. in Animal Product Processing, Entrepreneurship and Safety', 'graduate'),
(289, 9, 'Master of Veterinary Preventive Medicine', 'graduate'),
(290, 9, 'Master of Wildlife Health and Management', 'graduate'),
(291, 9, 'Master of Wildlife Tourism and Recreation Management', 'graduate'),
(292, 9, 'Master of Biomedical Laboratory Sciences and Management', 'graduate'),
(293, 9, 'Master of Veterinary Medicine (Food Animal Health and Production)', 'graduate'),
(294, 9, 'PGD in Livestock Development Planning and Management', 'postgraduate'),
(295, 10, 'Bachelor of Laws', 'undergraduate'),
(296, 10, 'Master of Laws', 'graduate'),
(297, 10, 'PhD in Law', 'graduate');

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
  `level` enum('undergraduate','graduate','postgraduate') NOT NULL,
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
  ADD UNIQUE KEY `programme_name` (`programme_name`),
  ADD KEY `school_id` (`college_id`);

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
  ADD CONSTRAINT `programmes_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
