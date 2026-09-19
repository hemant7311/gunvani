-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2026 at 04:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gunvani`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `fullname`, `created_at`) VALUES
(1, 'gunvani', '$2y$10$64wqLrXpwxuffDwdIMAAguNx3pHQsD9Oct7.Zr7qHJE1/3dEz1sgu', 'gunvani news', '2025-11-13 16:29:28');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `summary` varchar(500) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('published','draft') NOT NULL DEFAULT 'published',
  `published_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `category_id`, `title`, `slug`, `summary`, `content`, `image`, `status`, `published_at`, `created_at`) VALUES
(3, 3, 'Noida startup hub attracts international investments in renewable energy', 'noida-startup-hub-renewable-investments', 'A new wave of clean energy startups in Noida has drawn global investors and created more than 1,200 jobs.', 'The sector is seeing strong interest from overseas funds as startups focus on solar power, battery storage, and sustainable mobility. Entrepreneurs say this momentum will transform Noida into a leading green business destination.', '', 'published', '2026-07-24 08:30:00', '2026-07-26 03:10:40'),
(4, 4, 'Mathura prepares for the annual festival with enhanced safety and transport plans', 'mathura-festival-safety-transport-plan', 'City authorities have finalized the festival schedule with extra police deployment and shuttle services for pilgrims.', 'The plan includes traffic restrictions, emergency response teams, and shuttle routes connecting the railway station to the temple area. Officials aim to ensure a peaceful and seamless experience for visitors during the festival.', '', 'published', '2026-07-23 18:00:00', '2026-07-26 03:10:40'),
(6, 5, 'today news', 'shikohabad-today-news', 'shikohabad news', 'all content', '1785041867_a2b6cdf055468ddd332df3d3134ddb07.jpg', 'published', '2026-07-26 06:56:00', '2026-07-26 04:57:47'),
(7, 5, 'shikohabad viral news', 'shikohabad-viral-news', 'news', 'shikohabad startup hub attracts international investments in renewable energy', '1785041959_slider.png', 'published', '2026-07-26 06:58:00', '2026-07-26 04:59:19');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(3, 'Noida', 'noida', 'Technology, business and lifestyle news from Noida.', '2026-07-26 03:10:40'),
(4, 'Mathura', 'mathura', 'Cultural events and city life news from Mathura.', '2026-07-26 03:10:40'),
(5, 'Shikohbad', 'shikohabad-news', 'explain', '2026-07-26 04:56:06');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_sections`
--

CREATE TABLE `homepage_sections` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(500) DEFAULT NULL,
  `section_type` enum('latest','category','featured') NOT NULL DEFAULT 'latest',
  `category_id` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homepage_sections`
--

INSERT INTO `homepage_sections` (`id`, `title`, `subtitle`, `section_type`, `category_id`, `article_id`, `display_order`, `created_at`) VALUES
(1, 'Featured Story', 'Top news from city corridors and live updates.', 'featured', NULL, NULL, 1, '2026-07-26 03:10:40'),
(3, 'Noida Business Pulse', 'Economic and lifestyle highlights from Noida.', 'category', 3, NULL, 0, '2026-07-26 03:10:40'),
(4, 'Today viral news', 'line write', 'latest', NULL, NULL, 2, '2026-07-26 05:05:02'),
(5, 'agra news', 'kldafj lkadsfjlkadsj flka jdlkf', 'latest', NULL, NULL, 0, '2026-07-26 05:17:37');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `member_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `dob` varchar(255) NOT NULL,
  `doi` varchar(255) NOT NULL,
  `doe` varchar(255) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `qr` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `member_id`, `name`, `dob`, `doi`, `doe`, `mobile`, `address`, `location`, `blood_group`, `designation`, `photo`, `qr`, `created_at`) VALUES
(4, 'gunvani87', 'dangershi', '2025-12-18', '2025-12-28', '2025-12-28', '8754875487', 'shikohabad', 'skb', 'B-', 'abc', '1763054846_gunvani.jpg', '', '2025-11-13 17:27:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `homepage_sections`
--
ALTER TABLE `homepage_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `article_id` (`article_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `homepage_sections`
--
ALTER TABLE `homepage_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `fk_articles_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `homepage_sections`
--
ALTER TABLE `homepage_sections`
  ADD CONSTRAINT `fk_sections_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sections_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
