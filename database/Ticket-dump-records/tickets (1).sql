-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost:80
-- Generation Time: Oct 19, 2020 at 11:56 AM
-- Server version: 5.7.31-0ubuntu0.16.04.1
-- PHP Version: 7.2.34-2+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sololuxury`
--

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(11) NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ticket_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `assigned_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_of_ticket` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'live_chat',
  `status_id` int(11) NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type_of_inquiry` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `customer_id`, `name`, `email`, `ticket_id`, `subject`, `message`, `assigned_to`, `source_of_ticket`, `status_id`, `date`, `created_at`, `updated_at`, `type_of_inquiry`, `last_name`, `country`, `phone_no`, `order_no`) VALUES
(1, 3014, 'Kajal', 'jhunjhunwallakajal@gmail.com', 'T3IUQ', 'Card Holders', 'Message: Im looking for womens card holders', NULL, 'live_chat', 1, '2020-09-07 03:24:20', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(2, 3015, 'Leo', 'boxo88@bk.ru', '3I99H', 'Product', 'Message: Hi, how are you, this site is going well I think', NULL, 'live_chat', 1, '2020-08-29 10:40:08', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(3, 3008, 'Bardambek Yusupov', 'bardam.yus@gmail.com', 'PWTCR', 'Task test', 'Message: Hi', NULL, 'live_chat', 1, '2020-08-25 01:26:31', '2020-09-11 11:48:23', '2020-09-11 12:08:33', NULL, NULL, NULL, NULL, NULL),
(4, 3008, 'Bardambek Yusupov', 'bardam.yus@gmail.com', 'J7XPB', 'About new Products', 'Message: Hi', NULL, 'live_chat', 1, '2020-08-25 01:25:30', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(5, 3016, 'Ben Yus', 'bardam.yus@solo.com', 'GPN82', 'qwxdqa', 'Message: qwcqwc', NULL, 'live_chat', 1, '2020-08-23 05:12:15', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(6, 3017, 'dgj dj', 'dsfrg@zdf.com', 'WYHZ4', 'sht', 'Message: sgfh', NULL, 'live_chat', 1, '2020-08-12 05:37:50', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(7, 3018, 'Umidbek', 'Adambaev99@mail.ru', 'M56P5', 'website', 'Message: excellent', NULL, 'live_chat', 1, '2020-08-12 04:16:21', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(8, 3019, 'Sahil Mordani', 'sahil1000m@gmail.com', '1P064', 'hcuidtcku', 'Message: hvgoh', NULL, 'live_chat', 1, '2020-08-10 05:35:38', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(9, 2001, 'Pravin Solanki', 'solanki7492@gmail.com', 'L327F', 'test', 'Message: test', NULL, 'live_chat', 1, '2020-08-07 08:03:10', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(10, 3020, 'john', 'adambaev@mail.ru', 'TRT2W', 'website', 'Message: good', NULL, 'live_chat', 1, '2020-08-05 01:14:45', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(11, 3021, 'James Adolf', 'buying@amourint.com', '6KZ33', 'test', 'Message: test', NULL, 'live_chat', 1, '2020-08-04 06:02:43', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(12, 3022, 'nancy parihar', 'nancyparihar01@yahoo.com', 'XBNAJ', 'Is website is closed?', 'Message: Or can i buy product?', NULL, 'live_chat', 1, '2020-07-30 12:42:54', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(13, 3023, 'himanshi saini', 'himanshisaini9891@gmail.com', 'ORWM7', 'lv bags', 'Message: i want to ask about do u have lv speedy range as ur site is not working and i dont have any whtsapp number of ur team.', NULL, 'live_chat', 1, '2020-07-29 03:08:52', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(14, 3024, 'Satyam', 'satyam.luvit@gmail.com', 'Y4AZS', 'sd', 'Message: dsd', NULL, 'live_chat', 1, '2020-07-03 18:42:32', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(15, 3025, 'James', 'info@theluxuryunlimited.com', 'OWWJ4', 'contact', 'Message: can you contact me', NULL, 'live_chat', 1, '2020-07-03 05:50:36', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(16, 3026, 'Rubica', 'rubicalourds123@gmail.com', 'P6RD5', 'Message', 'Message: When will the site be back to normal?', NULL, 'live_chat', 1, '2020-06-28 00:00:47', '2020-09-11 11:48:23', '2020-09-11 11:48:23', NULL, NULL, NULL, NULL, NULL),
(17, 3027, 'Richa Rajput', 'thericharajput@gmail.com', 'EN50X', 'Availability of tory burch footwear', 'Message: Pls let me know the the ready to ship products available in Tory burch size 5.5 US. My number is 9012750132', NULL, 'live_chat', 1, '2020-06-12 01:32:08', '2020-09-11 11:48:24', '2020-09-11 11:48:24', NULL, NULL, NULL, NULL, NULL),
(18, 3028, 'Vishal shroff', 'vishalshroff8@hotmail.com', '4VQPG', 'Bags shoes', 'Message: Please call me 9831022295', NULL, 'live_chat', 1, '2020-04-23 08:54:25', '2020-09-11 11:48:24', '2020-09-11 11:48:24', NULL, NULL, NULL, NULL, NULL),
(19, 3029, 'Sameer Vijan', 'sameervijan.gst@gmail.com', 'I04BG', 'Gucci slides', 'Message: Do you have any gucci slides for men in size 45', NULL, 'live_chat', 1, '2020-03-24 02:07:28', '2020-09-11 11:48:24', '2020-09-11 11:48:24', NULL, NULL, NULL, NULL, NULL),
(20, 3030, 'Natanya', 'natanya.mordani@gmail.com', 'XVWP4', 'Bonjour', 'Message: مرحبا, كيف حالك', NULL, 'live_chat', 1, '2020-01-30 11:06:20', '2020-09-11 11:48:24', '2020-09-11 11:48:24', NULL, NULL, NULL, NULL, NULL),
(21, 3031, 'Anshika', 'anshika.j.shaw@gmail.com', 'TAYIQ', 'Request to view all products', 'Message: Hi, your site is apparently under construction. I would like to view your products. Help', NULL, 'live_chat', 1, '2020-01-28 12:01:49', '2020-09-11 11:48:24', '2020-09-11 11:48:24', NULL, NULL, NULL, NULL, NULL),
(22, 3032, 'Pooja gupta', 'pooh.mittal02@gmail.com', '2CW65', 'How to buy', 'Message: Where to see stuff', NULL, 'live_chat', 1, '2020-01-25 04:17:16', '2020-09-11 11:48:24', '2020-09-11 11:48:24', NULL, NULL, NULL, NULL, NULL),
(23, 3033, 'chetna', 'silentprayer.chetna@gmail.com', '7AVU6', 'shop', 'Message: when are you back >', NULL, 'live_chat', 1, '2020-01-23 15:44:34', '2020-09-11 11:48:24', '2020-09-11 11:48:24', NULL, NULL, NULL, NULL, NULL),
(24, 3034, 'Nakshatra', 'nakshatrabajaj@gmail.com', '784VO', 'Would like to order shoes', 'Message: Gucci shoes', NULL, 'live_chat', 1, '2020-01-16 11:01:50', '2020-09-11 11:48:24', '2020-09-11 11:48:24', NULL, NULL, NULL, NULL, NULL),
(25, 3035, 'Shreya shivhare', 'shreyashivhare555@gmail.com', '60T7E', 'Not able to access the website or instagram page', 'Message: this website is been under instructions since long time. Have you guys shut down your Instagram page to ?', NULL, 'live_chat', 1, '2020-01-14 07:28:18', '2020-09-11 11:48:24', '2020-09-11 11:48:24', NULL, NULL, NULL, NULL, NULL),
(26, 0, 'Pravin', 'abc@example.com', NULL, 'Some subject name', 'Some message need to ask', NULL, '', 0, '2020-10-09 11:49:29', '2020-10-09 10:19:29', '2020-10-09 10:19:29', 'Orders', 'Solanki', 'India', '919876543210', 'ORDER-NO'),
(27, 0, 'Pravin', 'abc@example.com', NULL, 'Some subject name', 'Some message need to ask', NULL, 'site url', 0, '2020-10-09 11:53:39', '2020-10-09 10:23:39', '2020-10-09 10:23:39', 'Orders', 'Solanki', 'India', '919876543210', 'ORDER-NO'),
(28, 0, 'Pravin', 'abc@example.com', NULL, 'Some subject name', 'Some message need to ask', NULL, 'site url', 0, '2020-10-09 11:56:01', '2020-10-09 10:26:01', '2020-10-09 10:26:01', 'Orders', 'Solanki', 'India', '919876543210', 'ORDER-NO'),
(29, 0, 'Pravin', 'abc@example.com', NULL, 'Some subject name', 'Some message need to ask', NULL, 'site url', 0, '2020-10-09 11:56:15', '2020-10-09 10:26:15', '2020-10-09 10:26:15', 'Orders', 'Solanki', 'India', '919876543210', 'ORDER-NO'),
(30, 0, 'Pravin', 'abc@example.com', 'T20201009155643', 'Some subject name', 'Some message need to ask', NULL, 'site url', 0, '2020-10-09 11:56:43', '2020-10-09 10:26:43', '2020-10-09 10:26:43', 'Orders', 'Solanki', 'India', '919876543210', 'ORDER-NO'),
(31, 0, 'Pravin', 'abc@example.com', 'T20201009155741', 'Some subject name', 'Some message need to ask', NULL, 'site url', 0, '2020-10-09 11:57:41', '2020-10-09 10:27:41', '2020-10-09 10:27:41', 'Orders', 'Solanki', 'India', '919876543210', 'ORDER-NO');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
