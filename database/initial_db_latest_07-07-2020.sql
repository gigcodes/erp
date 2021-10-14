-- MySQL dump 10.13  Distrib 8.0.26, for Linux (x86_64)
--
-- Host: localhost    Database: sololuxury
-- ------------------------------------------------------
-- Server version 8.0.26-0ubuntu0.20.04.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proxy` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_message` int DEFAULT NULL,
  `store_website_id` int NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `instance_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `send_end` int NOT NULL,
  `send_start` int NOT NULL,
  `is_connected` int NOT NULL DEFAULT '0',
  `last_online` datetime DEFAULT NULL,
  `frequency` int DEFAULT NULL,
  `is_customer_support` int NOT NULL DEFAULT '0',
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `followers_count` int DEFAULT NULL,
  `posts_count` int DEFAULT NULL,
  `dp_count` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_processed` tinyint(1) NOT NULL DEFAULT '0',
  `broadcast` int NOT NULL DEFAULT '0',
  `broadcasted_messages` int NOT NULL DEFAULT '0',
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `manual_comment` int NOT NULL DEFAULT '0',
  `bulk_comment` int NOT NULL DEFAULT '0',
  `blocked` int NOT NULL DEFAULT '0',
  `is_seeding` int NOT NULL DEFAULT '0',
  `seeding_stage` int NOT NULL DEFAULT '0',
  `comment_pending` int NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `last_cron_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=960 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` int NOT NULL,
  `subject_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `causer_id` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1831 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `activities_routines`
--

DROP TABLE IF EXISTS `activities_routines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities_routines` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `times_a_day` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `times_a_week` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `times_a_month` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_id` int DEFAULT NULL,
  `subject_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` int DEFAULT NULL,
  `causer_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `properties` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ad_accounts`
--

DROP TABLE IF EXISTS `ad_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ad_accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `account_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `config_file` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_error` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_error_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ad_campaigns`
--

DROP TABLE IF EXISTS `ad_campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ad_campaigns` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ad_account_id` int NOT NULL,
  `goal` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campaign_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `campaign_budget_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campaign_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campaign_response` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ad_groups`
--

DROP TABLE IF EXISTS `ad_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ad_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` int NOT NULL,
  `google_campaign_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keywords` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `budget` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_ad_group_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_ad_group_response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ads`
--

DROP TABLE IF EXISTS `ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` int NOT NULL,
  `adgroup_id` int NOT NULL,
  `finalurl` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `displayurl` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `headlines` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `descriptions` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tracking_tamplate` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `final_url_suffix` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `customparam` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `different_url_mobile` int NOT NULL,
  `mobile_final_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ad_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ad_response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ads_schedules`
--

DROP TABLE IF EXISTS `ads_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ads_schedules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `scheduled_for` datetime NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ads_schedules_attachments`
--

DROP TABLE IF EXISTS `ads_schedules_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ads_schedules_attachments` (
  `ads_schedule_id` int unsigned NOT NULL,
  `attachment_id` int unsigned NOT NULL,
  `attachment_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `affiliates`
--

DROP TABLE IF EXISTS `affiliates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `affiliates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `hashtag_id` int DEFAULT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caption` longtext COLLATE utf8mb4_unicode_ci,
  `posted_at` datetime DEFAULT NULL,
  `source` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `facebook` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_followers` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_followers` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_followers` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube_followers` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_followers` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pinterest` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pinterest_followers` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emailaddress` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_flagged` tinyint(1) NOT NULL DEFAULT '0',
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unique_visitors_per_month` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_views_per_month` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `worked_on` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` int DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('affiliate','influencer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'affiliate',
  `store_website_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `agent_call_statuses`
--

DROP TABLE IF EXISTS `agent_call_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agent_call_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` int DEFAULT NULL,
  `agent_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent_name_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_id` int DEFAULT NULL,
  `twilio_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `agents`
--

DROP TABLE IF EXISTS `agents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `analytics`
--

DROP TABLE IF EXISTS `analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `analytics` (
  `operatingSystem` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `social_network` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `device_info` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sessions` int NOT NULL,
  `pageviews` int NOT NULL,
  `bounceRate` int NOT NULL,
  `avgSessionDuration` bigint NOT NULL,
  `timeOnPage` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `analytics_summaries`
--

DROP TABLE IF EXISTS `analytics_summaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `analytics_summaries` (
  `brand_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_keys`
--

DROP TABLE IF EXISTS `api_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_keys` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_response_message_value_histories`
--

DROP TABLE IF EXISTS `api_response_message_value_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_response_message_value_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `api_response_message_id` int NOT NULL,
  `user_id` int NOT NULL,
  `old_value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_response_messages`
--

DROP TABLE IF EXISTS `api_response_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_response_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int DEFAULT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=503 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `article_categories`
--

DROP TABLE IF EXISTS `article_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assets_category`
--

DROP TABLE IF EXISTS `assets_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assets_category` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assets_manager`
--

DROP TABLE IF EXISTS `assets_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assets_manager` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asset_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int unsigned NOT NULL,
  `purchase_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_cycle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double(8,2) NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usage` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archived` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_assets_manager_categories` (`category_id`),
  CONSTRAINT `fk_assets_manager_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assigned_user_pages`
--

DROP TABLE IF EXISTS `assigned_user_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assigned_user_pages` (
  `user_id` int unsigned NOT NULL,
  `menu_page_id` int unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`menu_page_id`),
  KEY `assigned_user_pages_user_id_index` (`user_id`),
  KEY `assigned_user_pages_menu_page_id_index` (`menu_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assinged_department_menu`
--

DROP TABLE IF EXISTS `assinged_department_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assinged_department_menu` (
  `department_id` int unsigned NOT NULL,
  `menu_page_id` int unsigned NOT NULL,
  `Admin` tinyint(1) NOT NULL,
  `HOD` tinyint(1) NOT NULL,
  `Supervisor` tinyint(1) NOT NULL,
  `Users` tinyint(1) NOT NULL,
  PRIMARY KEY (`department_id`,`menu_page_id`),
  KEY `assinged_department_menu_department_id_index` (`department_id`),
  KEY `assinged_department_menu_menu_page_id_index` (`menu_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attachments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_to` int NOT NULL,
  `external` tinyint(1) NOT NULL,
  `order` int NOT NULL,
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attachments_uploaded_to_index` (`uploaded_to`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attribute_replacements`
--

DROP TABLE IF EXISTS `attribute_replacements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attribute_replacements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `field_identifier` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_term` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_to_peform` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `replacement_term` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `authorized_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auto_comment_histories`
--

DROP TABLE IF EXISTS `auto_comment_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auto_comment_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `target` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` int NOT NULL,
  `auto_reply_hashtag_id` int NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `caption` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auto_complete_messages`
--

DROP TABLE IF EXISTS `auto_complete_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auto_complete_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `full_text_index` (`message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auto_refresh_pages`
--

DROP TABLE IF EXISTS `auto_refresh_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auto_refresh_pages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `page` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auto_refresh_pages_page_index` (`page`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auto_replies`
--

DROP TABLE IF EXISTS `auto_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auto_replies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reply` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sending_time` datetime DEFAULT NULL,
  `repeat` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auto_reply_hashtags`
--

DROP TABLE IF EXISTS `auto_reply_hashtags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auto_reply_hashtags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `automated_messages`
--

DROP TABLE IF EXISTS `automated_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `automated_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `back_link_anchors`
--

DROP TABLE IF EXISTS `back_link_anchors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `back_link_anchors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tool_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `database` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `anchor` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `domains_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `backlinks_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `back_link_checker`
--

DROP TABLE IF EXISTS `back_link_checker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `back_link_checker` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `domains` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `links` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `review_numbers` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rank` int NOT NULL,
  `rating` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serp_id` int NOT NULL,
  `snippet` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `visible_link` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `back_link_checkers`
--

DROP TABLE IF EXISTS `back_link_checkers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `back_link_checkers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `domains` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `links` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `review_numbers` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rank` int NOT NULL,
  `rating` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serp_id` int NOT NULL,
  `snippet` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `visible_link` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `back_link_domains`
--

DROP TABLE IF EXISTS `back_link_domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `back_link_domains` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tool_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `database` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain_ascore` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `backlinks_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `subtype` enum('organic','paid') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `back_linkings`
--

DROP TABLE IF EXISTS `back_linkings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `back_linkings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `backlink_indexed_pages`
--

DROP TABLE IF EXISTS `backlink_indexed_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `backlink_indexed_pages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `tool_id` int NOT NULL,
  `database` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `response_code` int NOT NULL,
  `backlinks_num` int NOT NULL,
  `domains_num` int NOT NULL,
  `last_seen` int NOT NULL,
  `external_num` int NOT NULL,
  `internal_num` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `backlink_overview`
--

DROP TABLE IF EXISTS `backlink_overview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `backlink_overview` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tool_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `database` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ascore` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `domains_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `urls_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ips_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ipclassc_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `follows_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nofollows_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sponsored_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ugc_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `texts_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `images_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `forms_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `frames_num` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `backlinks`
--

DROP TABLE IF EXISTS `backlinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `backlinks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `tool_id` int NOT NULL,
  `database` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `backlinks_overview` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `backlinks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tld_distribution` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `anchors` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `indexed_pages` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `competitors` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `comparison_by_referring_domains` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_comparison` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `authority_score_profile` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `categories_profile` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `categories` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `historical_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `barcode_media`
--

DROP TABLE IF EXISTS `barcode_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barcode_media` (
  `id` int NOT NULL AUTO_INCREMENT,
  `media_id` int DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'product',
  `type_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,0) NOT NULL DEFAULT '0',
  `extra` text,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `media_id` (`media_id`),
  KEY `type` (`type`),
  KEY `type_id` (`type_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=510 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `benchmarks`
--

DROP TABLE IF EXISTS `benchmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `benchmarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `selections` int NOT NULL DEFAULT '0',
  `searches` int NOT NULL DEFAULT '0',
  `attributes` int NOT NULL DEFAULT '0',
  `supervisor` int NOT NULL DEFAULT '0',
  `imagecropper` int NOT NULL DEFAULT '0',
  `lister` int NOT NULL DEFAULT '0',
  `approver` int NOT NULL DEFAULT '0',
  `inventory` int NOT NULL DEFAULT '0',
  `for_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `block_web_message_lists`
--

DROP TABLE IF EXISTS `block_web_message_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `block_web_message_lists` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int DEFAULT NULL,
  `object_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blogger_email_templates`
--

DROP TABLE IF EXISTS `blogger_email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogger_email_templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bcc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blogger_payments`
--

DROP TABLE IF EXISTS `blogger_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogger_payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `blogger_id` int unsigned NOT NULL,
  `currency` int NOT NULL DEFAULT '0',
  `payment_date` date DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `payable_amount` decimal(13,4) DEFAULT NULL,
  `paid_amount` decimal(13,4) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `other` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blogger_payments_blogger_id_foreign` (`blogger_id`),
  KEY `blogger_payments_status_index` (`status`),
  CONSTRAINT `blogger_payments_blogger_id_foreign` FOREIGN KEY (`blogger_id`) REFERENCES `bloggers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blogger_product_images`
--

DROP TABLE IF EXISTS `blogger_product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogger_product_images` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blogger_product_id` int unsigned NOT NULL,
  `other` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blogger_product_images_blogger_product_id_foreign` (`blogger_product_id`),
  CONSTRAINT `blogger_product_images_blogger_product_id_foreign` FOREIGN KEY (`blogger_product_id`) REFERENCES `blogger_products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blogger_products`
--

DROP TABLE IF EXISTS `blogger_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogger_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `blogger_id` int unsigned NOT NULL,
  `brand_id` int unsigned NOT NULL,
  `shoot_date` date DEFAULT NULL,
  `first_post` date DEFAULT NULL,
  `second_post` date DEFAULT NULL,
  `first_post_likes` int DEFAULT NULL,
  `first_post_engagement` int DEFAULT NULL,
  `first_post_response` int DEFAULT NULL,
  `first_post_sales` int DEFAULT NULL,
  `second_post_likes` int DEFAULT NULL,
  `second_post_engagement` int DEFAULT NULL,
  `second_post_response` int DEFAULT NULL,
  `second_post_sales` int DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `initial_quote` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_quote` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `images` text COLLATE utf8mb4_unicode_ci,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `other` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blogger_products_blogger_id_foreign` (`blogger_id`),
  KEY `blogger_products_brand_id_foreign` (`brand_id`),
  CONSTRAINT `blogger_products_blogger_id_foreign` FOREIGN KEY (`blogger_id`) REFERENCES `bloggers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `blogger_products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bloggers`
--

DROP TABLE IF EXISTS `bloggers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bloggers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_handle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `followers` int DEFAULT NULL,
  `followings` int DEFAULT NULL,
  `avg_engagement` int DEFAULT NULL,
  `fake_followers` int DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `industry` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brands` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `book_activities`
--

DROP TABLE IF EXISTS `book_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `book_activities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extra` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `book_id` int NOT NULL,
  `user_id` int NOT NULL,
  `entity_id` int NOT NULL,
  `entity_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `book_activities_book_id_index` (`book_id`),
  KEY `book_activities_user_id_index` (`user_id`),
  KEY `book_activities_entity_id_index` (`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `book_comments`
--

DROP TABLE IF EXISTS `book_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `book_comments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int unsigned NOT NULL,
  `entity_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci,
  `html` text COLLATE utf8mb4_unicode_ci,
  `parent_id` int unsigned DEFAULT NULL,
  `local_id` int unsigned DEFAULT NULL,
  `created_by` int unsigned NOT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `book_comments_entity_id_entity_type_index` (`entity_id`,`entity_type`),
  KEY `book_comments_local_id_index` (`local_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `book_images`
--

DROP TABLE IF EXISTS `book_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `book_images` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  `path` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_to` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `book_images_type_index` (`type`),
  KEY `book_images_uploaded_to_index` (`uploaded_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `book_tags`
--

DROP TABLE IF EXISTS `book_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `book_tags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int NOT NULL,
  `entity_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `book_tags_entity_id_entity_type_index` (`entity_id`,`entity_type`),
  KEY `book_tags_name_index` (`name`),
  KEY `book_tags_value_index` (`value`),
  KEY `book_tags_order_index` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `books` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  `restricted` tinyint(1) NOT NULL DEFAULT '0',
  `image_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `books_slug_index` (`slug`),
  KEY `books_created_by_index` (`created_by`),
  KEY `books_updated_by_index` (`updated_by`),
  KEY `books_restricted_index` (`restricted`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bookshelves`
--

DROP TABLE IF EXISTS `bookshelves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookshelves` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `restricted` tinyint(1) NOT NULL DEFAULT '0',
  `image_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookshelves_slug_index` (`slug`(191)),
  KEY `bookshelves_created_by_index` (`created_by`),
  KEY `bookshelves_updated_by_index` (`updated_by`),
  KEY `bookshelves_restricted_index` (`restricted`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bookshelves_books`
--

DROP TABLE IF EXISTS `bookshelves_books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookshelves_books` (
  `bookshelf_id` int unsigned NOT NULL,
  `book_id` int unsigned NOT NULL,
  `order` int unsigned NOT NULL,
  PRIMARY KEY (`bookshelf_id`,`book_id`),
  KEY `bookshelves_books_book_id_foreign` (`book_id`),
  CONSTRAINT `bookshelves_books_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bookshelves_books_bookshelf_id_foreign` FOREIGN KEY (`bookshelf_id`) REFERENCES `bookshelves` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brand_category_price_range`
--

DROP TABLE IF EXISTS `brand_category_price_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brand_category_price_range` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `brand_segment` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_price` int NOT NULL,
  `max_price` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=343 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brand_category_size_charts`
--

DROP TABLE IF EXISTS `brand_category_size_charts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brand_category_size_charts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int NOT NULL,
  `category_id` int NOT NULL,
  `store_website_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brand_fans`
--

DROP TABLE IF EXISTS `brand_fans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brand_fans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(191) NOT NULL,
  `brand_url` varchar(196) NOT NULL,
  `username` varchar(191) NOT NULL,
  `profile_url` varchar(400) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brand_logos`
--

DROP TABLE IF EXISTS `brand_logos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brand_logos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `logo_image_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brand_reviews`
--

DROP TABLE IF EXISTS `brand_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brand_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `website` varchar(191) NOT NULL,
  `brand` varchar(191) NOT NULL,
  `review_url` varchar(400) NOT NULL,
  `username` varchar(191) NOT NULL,
  `title` varchar(200) NOT NULL,
  `body` mediumtext NOT NULL,
  `stars` int NOT NULL,
  `used` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7527 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brand_scraper_results`
--

DROP TABLE IF EXISTS `brand_scraper_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brand_scraper_results` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `brand_id` int DEFAULT NULL,
  `scraper_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_urls` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `brand_scraper_results_brand_id_index` (`brand_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brand_tagged_posts`
--

DROP TABLE IF EXISTS `brand_tagged_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brand_tagged_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `brand_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_url` mediumtext NOT NULL,
  `username` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `image_url` mediumtext NOT NULL,
  `posted_on` text NOT NULL,
  `no_likes` int NOT NULL,
  `no_comments` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brand_with_logos`
--

DROP TABLE IF EXISTS `brand_with_logos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brand_with_logos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int DEFAULT NULL,
  `brand_logo_image_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `euro_to_inr` double NOT NULL,
  `min_sale_price` int DEFAULT NULL,
  `max_sale_price` int DEFAULT NULL,
  `deduction_percentage` int NOT NULL,
  `flash_sales_percentage` int NOT NULL DEFAULT '0',
  `apply_b2b_discount_above` int NOT NULL DEFAULT '0',
  `b2b_sales_discount` int NOT NULL DEFAULT '0',
  `sales_discount` int NOT NULL DEFAULT '0',
  `magento_id` int unsigned DEFAULT '0',
  `brand_segment` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku_strip_last` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku_add` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `references` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku_search_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_server_id` int DEFAULT NULL,
  `priority` int DEFAULT '0',
  `next_step` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `brand_image` text COLLATE utf8mb4_unicode_ci,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `brands_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=132719 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `broadcast_images`
--

DROP TABLE IF EXISTS `broadcast_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `broadcast_images` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `products` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sending_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `budget_categories`
--

DROP TABLE IF EXISTS `budget_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `budget_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int unsigned NOT NULL DEFAULT '0',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `budgets`
--

DROP TABLE IF EXISTS `budgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `budgets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `budget_category_id` int unsigned NOT NULL,
  `budget_subcategory_id` int unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `amount` int NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `build_process_histories`
--

DROP TABLE IF EXISTS `build_process_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `build_process_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `created_by` int NOT NULL,
  `build_number` int NOT NULL,
  `status` enum('running','success','failure') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'running',
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `build_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `build_process_histories_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bulk_customer_replies_keyword_customer`
--

DROP TABLE IF EXISTS `bulk_customer_replies_keyword_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bulk_customer_replies_keyword_customer` (
  `keyword_id` int unsigned NOT NULL,
  `customer_id` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bulk_customer_replies_keywords`
--

DROP TABLE IF EXISTS `bulk_customer_replies_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bulk_customer_replies_keywords` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `value` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_manual` tinyint(1) NOT NULL DEFAULT '0',
  `count` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_processed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `value` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  UNIQUE KEY `cache_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_busy_message_statuses`
--

DROP TABLE IF EXISTS `call_busy_message_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_busy_message_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_busy_messages`
--

DROP TABLE IF EXISTS `call_busy_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_busy_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lead_id` int DEFAULT '0',
  `twilio_call_sid` varchar(255) DEFAULT NULL,
  `caller_sid` varchar(255) DEFAULT NULL,
  `message` text,
  `recording_url` varchar(200) DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `call_busy_message_statuses_id` int DEFAULT NULL,
  `audio_text` text NOT NULL,
  `customer_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `call_busy_messages_call_busy_message_statuses_id_index` (`call_busy_message_statuses_id`),
  KEY `call_busy_messages_customer_id_index` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_histories`
--

DROP TABLE IF EXISTS `call_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_histories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `status` varchar(255) NOT NULL,
  `store_website_id` int DEFAULT NULL,
  `call_id` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_recordings`
--

DROP TABLE IF EXISTS `call_recordings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_recordings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `callsid` varchar(255) DEFAULT NULL,
  `twilio_call_sid` varchar(255) DEFAULT NULL,
  `recording_url` varchar(255) DEFAULT NULL,
  `lead_id` int unsigned DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `customer_number` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `call_recordings_order_id` (`order_id`),
  CONSTRAINT `call_recordings_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campaign_events`
--

DROP TABLE IF EXISTS `campaign_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaign_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_event` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `captions`
--

DROP TABLE IF EXISTS `captions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `captions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `caption` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `platform` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `case_costs`
--

DROP TABLE IF EXISTS `case_costs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `case_costs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `case_id` int unsigned DEFAULT NULL,
  `billed_date` date DEFAULT NULL,
  `amount` decimal(13,4) DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `amount_paid` decimal(13,4) DEFAULT NULL,
  `other` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `case_costs_case_id_foreign` (`case_id`),
  CONSTRAINT `case_costs_case_id_foreign` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `case_receivables`
--

DROP TABLE IF EXISTS `case_receivables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `case_receivables` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `case_id` int unsigned NOT NULL,
  `currency` int NOT NULL DEFAULT '0',
  `receivable_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `receivable_amount` decimal(13,4) DEFAULT NULL,
  `received_amount` decimal(13,4) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `other` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `case_receivables_case_id_foreign` (`case_id`),
  KEY `case_receivables_status_index` (`status`),
  CONSTRAINT `case_receivables_case_id_foreign` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cases`
--

DROP TABLE IF EXISTS `cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cases` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lawyer_id` int unsigned DEFAULT NULL,
  `case_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `for_against` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `court_detail` text COLLATE utf8mb4_unicode_ci,
  `whatsapp_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `last_date` date DEFAULT NULL,
  `next_date` date DEFAULT NULL,
  `cost_per_hearing` double(8,2) DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `other` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cases_lawyer_id_foreign` (`lawyer_id`),
  CONSTRAINT `cases_lawyer_id_foreign` FOREIGN KEY (`lawyer_id`) REFERENCES `lawyers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cash_flows`
--

DROP TABLE IF EXISTS `cash_flows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_flows` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `cash_flow_category_id` int unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date` date NOT NULL,
  `amount` decimal(8,0) NOT NULL DEFAULT '0',
  `erp_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `erp_eur_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `amount_eur` decimal(8,2) NOT NULL DEFAULT '0.00',
  `due_amount_eur` double NOT NULL DEFAULT '0',
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expected` decimal(13,4) DEFAULT NULL,
  `actual` decimal(13,4) DEFAULT NULL,
  `cash_flow_able_id` int DEFAULT NULL,
  `cash_flow_able_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monetary_account_id` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `order_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cash_flows_user_id_foreign` (`user_id`),
  KEY `cash_flows_updated_by_foreign` (`updated_by`),
  KEY `cash_flows_status_index` (`status`),
  KEY `cash_flows_order_status_index` (`order_status`),
  KEY `cash_flows_currency_index` (`currency`),
  CONSTRAINT `cash_flows_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cash_flows_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL,
  `category_segment_id` int DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `magento_id` int unsigned NOT NULL,
  `show_all_id` int unsigned DEFAULT NULL,
  `dimension_range` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size_range` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `simplyduty_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_after_autocrop` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `references` longtext COLLATE utf8mb4_unicode_ci,
  `ignore_category` longtext COLLATE utf8mb4_unicode_ci,
  `need_to_check_measurement` tinyint(1) NOT NULL,
  `need_to_check_size` tinyint(1) NOT NULL,
  `size_chart_needed` int NOT NULL DEFAULT '0',
  `push_type` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `category_segment_id` (`category_segment_id`),
  KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=227 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `category_segment_discounts`
--

DROP TABLE IF EXISTS `category_segment_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_segment_discounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int NOT NULL,
  `category_segment_id` int NOT NULL,
  `amount` int NOT NULL,
  `amount_type` enum('percentage') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_segment_discounts_brand_id_category_segment_id_index` (`brand_id`,`category_segment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12974 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `category_segments`
--

DROP TABLE IF EXISTS `category_segments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_segments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `category_update_users`
--

DROP TABLE IF EXISTS `category_update_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_update_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chapters`
--

DROP TABLE IF EXISTS `chapters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chapters` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `book_id` int NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  `restricted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `chapters_book_id_index` (`book_id`),
  KEY `chapters_slug_index` (`slug`),
  KEY `chapters_priority_index` (`priority`),
  KEY `chapters_created_by_index` (`created_by`),
  KEY `chapters_updated_by_index` (`updated_by`),
  KEY `chapters_restricted_index` (`restricted`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charities`
--

DROP TABLE IF EXISTS `charities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `charities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_no` int NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `whatsapp_number` int DEFAULT NULL,
  `assign_to` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charity_countries`
--

DROP TABLE IF EXISTS `charity_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `charity_countries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `charity_id` int NOT NULL,
  `country_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charity_order_history`
--

DROP TABLE IF EXISTS `charity_order_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `charity_order_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_order_charity_id` int NOT NULL,
  `comment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charity_product_store_websites`
--

DROP TABLE IF EXISTS `charity_product_store_websites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `charity_product_store_websites` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website_id` int NOT NULL,
  `charity_id` int NOT NULL,
  `price` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charity_status_masters`
--

DROP TABLE IF EXISTS `charity_status_masters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `charity_status_masters` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `charity_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_bot_error_logs`
--

DROP TABLE IF EXISTS `chat_bot_error_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_bot_error_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `chatbot_question_id` int NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_bot_keyword_groups`
--

DROP TABLE IF EXISTS `chat_bot_keyword_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_bot_keyword_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `keyword_id` int NOT NULL,
  `group_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_bot_phrase_groups`
--

DROP TABLE IF EXISTS `chat_bot_phrase_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_bot_phrase_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `phrase_id` int NOT NULL,
  `keyword_id` int NOT NULL,
  `group_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_message_phrases`
--

DROP TABLE IF EXISTS `chat_message_phrases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_message_phrases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `phrase` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` int NOT NULL DEFAULT '0',
  `word_id` int DEFAULT NULL,
  `chat_id` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15011 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_message_words`
--

DROP TABLE IF EXISTS `chat_message_words`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_message_words` (
  `id` int NOT NULL AUTO_INCREMENT,
  `word` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1618 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `is_queue` int NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lead_id` int unsigned DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `customer_id` int unsigned DEFAULT NULL,
  `purchase_id` int DEFAULT NULL,
  `supplier_id` int unsigned DEFAULT NULL,
  `vendor_id` int unsigned DEFAULT NULL,
  `charity_id` int DEFAULT NULL,
  `user_id` int unsigned DEFAULT '0',
  `sop_user_id` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sent_to_user_id` int DEFAULT NULL,
  `ticket_id` int unsigned DEFAULT NULL,
  `task_id` int unsigned DEFAULT NULL,
  `account_id` int DEFAULT NULL,
  `instagram_user_id` int DEFAULT NULL,
  `lawyer_id` int unsigned DEFAULT NULL,
  `case_id` int unsigned DEFAULT NULL,
  `blogger_id` int unsigned DEFAULT NULL,
  `voucher_id` int unsigned DEFAULT NULL,
  `developer_task_id` int DEFAULT NULL,
  `issue_id` int DEFAULT NULL,
  `erp_user` int unsigned DEFAULT NULL,
  `contact_id` int unsigned DEFAULT NULL,
  `dubbizle_id` int unsigned DEFAULT NULL,
  `site_development_id` int DEFAULT NULL,
  `payment_receipt_id` int DEFAULT NULL,
  `assigned_to` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `approved` tinyint(1) DEFAULT '0',
  `status` int NOT NULL DEFAULT '0',
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  `is_delivered` tinyint unsigned NOT NULL DEFAULT '0',
  `is_read` tinyint unsigned NOT NULL DEFAULT '0',
  `error_status` int NOT NULL DEFAULT '1',
  `error_info` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `resent` int unsigned NOT NULL DEFAULT '0',
  `is_reminder` tinyint(1) NOT NULL DEFAULT '0',
  `is_email` int DEFAULT '0',
  `from_email` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `to_email` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cc_email` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `media_url` varchar(2048) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_processed_for_keyword` tinyint(1) NOT NULL DEFAULT '0',
  `document_id` int DEFAULT NULL,
  `group_id` int DEFAULT NULL,
  `old_id` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_chatbot` int DEFAULT '0',
  `message_application_id` int DEFAULT '0',
  `social_strategy_id` int DEFAULT NULL,
  `store_social_content_id` int DEFAULT NULL,
  `quoted_message_id` int DEFAULT NULL,
  `hubstaff_activity_summary_id` int DEFAULT NULL,
  `question_id` int DEFAULT NULL,
  `learning_id` tinyint(1) DEFAULT NULL,
  `additional_data` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hubstuff_activity_user_id` int DEFAULT NULL,
  `user_feedback_id` int DEFAULT NULL,
  `user_feedback_category_id` int DEFAULT NULL,
  `user_feedback_status` int DEFAULT NULL,
  `send_by` int DEFAULT NULL,
  `email_id` int DEFAULT NULL,
  `message_en` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_messages_order_id` (`order_id`),
  KEY `chat_messages_task_id_foreign` (`task_id`),
  KEY `chat_messages_erp_user_foreign` (`erp_user`),
  KEY `chat_messages_vendor_id_foreign` (`vendor_id`),
  KEY `chat_messages_lawyer_id_foreign` (`lawyer_id`),
  KEY `chat_messages_case_id_foreign` (`case_id`),
  KEY `chat_messages_blogger_id_foreign` (`blogger_id`),
  KEY `chat_messages_customer_id_index` (`customer_id`),
  KEY `chat_messages_voucher_id_foreign` (`voucher_id`),
  KEY `chat_messages_group_id_index` (`group_id`),
  KEY `chat_messages_ticket_id_foreign` (`ticket_id`),
  KEY `chat_messages_issue_id_developer_task_id_index` (`issue_id`,`developer_task_id`),
  KEY `chat_messages_sop_user_id_index` (`sop_user_id`),
  KEY `user_id` (`user_id`),
  KEY `charity_id` (`charity_id`),
  KEY `charity_id_2` (`charity_id`),
  CONSTRAINT `chat_messages_blogger_id_foreign` FOREIGN KEY (`blogger_id`) REFERENCES `bloggers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `chat_messages_case_id_foreign` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `chat_messages_erp_user_foreign` FOREIGN KEY (`erp_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `chat_messages_lawyer_id_foreign` FOREIGN KEY (`lawyer_id`) REFERENCES `lawyers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `chat_messages_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chat_messages_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `chat_messages_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `chat_messages_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=1786826 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_messages_quick_datas`
--

DROP TABLE IF EXISTS `chat_messages_quick_datas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_messages_quick_datas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` int NOT NULL,
  `last_unread_message_id` int unsigned DEFAULT NULL,
  `last_unread_message` text COLLATE utf8mb4_unicode_ci,
  `last_unread_message_at` timestamp NULL DEFAULT NULL,
  `last_communicated_message` text COLLATE utf8mb4_unicode_ci,
  `last_communicated_message_id` int unsigned DEFAULT NULL,
  `last_communicated_message_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_messages_quick_datas_model_id_index` (`model_id`),
  KEY `chat_messages_quick_datas_last_unread_message_id_index` (`last_unread_message_id`),
  KEY `chat_messages_quick_datas_last_communicated_message_id_index` (`last_communicated_message_id`)
) ENGINE=MyISAM AUTO_INCREMENT=182 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_categories`
--

DROP TABLE IF EXISTS `chatbot_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_dialog_error_logs`
--

DROP TABLE IF EXISTS `chatbot_dialog_error_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_dialog_error_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `chatbot_dialog_id` int NOT NULL,
  `store_website_id` int NOT NULL,
  `response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_dialog_responses`
--

DROP TABLE IF EXISTS `chatbot_dialog_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_dialog_responses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `response_type` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `message_to_human_agent` int NOT NULL DEFAULT '0',
  `chatbot_dialog_id` int NOT NULL,
  `store_website_id` int NOT NULL,
  `condition_sign` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=235 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_dialogs`
--

DROP TABLE IF EXISTS `chatbot_dialogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_dialogs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `response_type` varchar(255) NOT NULL DEFAULT 'standard',
  `name` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `parent_id` int NOT NULL DEFAULT '0',
  `match_condition` varchar(255) NOT NULL,
  `metadata` varchar(255) DEFAULT NULL,
  `workspace_id` varchar(255) DEFAULT NULL,
  `previous_sibling` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dialog_type` enum('node','folder') NOT NULL,
  `store_website_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=299 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_error_logs`
--

DROP TABLE IF EXISTS `chatbot_error_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_error_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `chatbot_question_id` int NOT NULL,
  `store_website_id` int NOT NULL,
  `response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=236 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_intents_annotations`
--

DROP TABLE IF EXISTS `chatbot_intents_annotations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_intents_annotations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question_example_id` int NOT NULL,
  `chatbot_keyword_id` int NOT NULL,
  `chatbot_value_id` int DEFAULT NULL,
  `start_char_range` int NOT NULL,
  `end_char_range` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_keyword_value_types`
--

DROP TABLE IF EXISTS `chatbot_keyword_value_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_keyword_value_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chatbot_keyword_value_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_keyword_values`
--

DROP TABLE IF EXISTS `chatbot_keyword_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_keyword_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `value` varchar(255) NOT NULL,
  `chatbot_keyword_id` int NOT NULL,
  `types` enum('synonyms','patterns') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8382 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_keywords`
--

DROP TABLE IF EXISTS `chatbot_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_keywords` (
  `id` int NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `workspace_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_message_log_responses`
--

DROP TABLE IF EXISTS `chatbot_message_log_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_message_log_responses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `chatbot_message_log_id` int NOT NULL,
  `request` text COLLATE utf8mb4_unicode_ci,
  `response` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chatbot_message_log_id` (`chatbot_message_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=188 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_message_logs`
--

DROP TABLE IF EXISTS `chatbot_message_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_message_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` int NOT NULL,
  `chat_message_id` int DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `response` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `chat_message_id` (`chat_message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_question_examples`
--

DROP TABLE IF EXISTS `chatbot_question_examples`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_question_examples` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question` varchar(255) NOT NULL,
  `chatbot_question_id` int DEFAULT NULL,
  `types` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=575679 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_questions`
--

DROP TABLE IF EXISTS `chatbot_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `value` varchar(255) NOT NULL,
  `suggested_reply` text,
  `category_id` int DEFAULT NULL,
  `workspace_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `keyword_or_question` varchar(191) DEFAULT NULL,
  `sending_time` datetime DEFAULT NULL,
  `repeat` varchar(191) DEFAULT NULL,
  `is_active` int NOT NULL DEFAULT '0',
  `erp_or_watson` varchar(191) DEFAULT NULL,
  `auto_approve` tinyint(1) NOT NULL DEFAULT '0',
  `chat_message_id` bigint unsigned DEFAULT NULL,
  `task_category_id` int DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  `task_description` text,
  `task_type` varchar(191) DEFAULT NULL,
  `repository_id` int DEFAULT NULL,
  `module_id` int DEFAULT NULL,
  `dynamic_reply` tinyint(1) NOT NULL DEFAULT '0',
  `watson_account_id` int DEFAULT '0',
  `watson_status` varchar(191) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=131 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_questions_reply`
--

DROP TABLE IF EXISTS `chatbot_questions_reply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_questions_reply` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `suggested_reply` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_website_id` int unsigned NOT NULL,
  `chatbot_question_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_replies`
--

DROP TABLE IF EXISTS `chatbot_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_replies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci,
  `reply` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `chat_id` int DEFAULT '0',
  `replied_chat_id` int DEFAULT NULL,
  `reply_from` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` int NOT NULL DEFAULT '0' COMMENT '0 => Unread , 1 => Read',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_id` (`chat_id`),
  KEY `chat_id_2` (`chat_id`),
  KEY `chat_id_3` (`chat_id`),
  KEY `chatbot_replies_replied_chat_id_index` (`replied_chat_id`),
  KEY `chatbot_replies_reply_from_index` (`reply_from`)
) ENGINE=MyISAM AUTO_INCREMENT=384 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatbot_settings`
--

DROP TABLE IF EXISTS `chatbot_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `chat_name` varchar(255) DEFAULT NULL,
  `vendor` varchar(255) NOT NULL,
  `instance_id` varchar(255) NOT NULL,
  `workspace_id` varchar(255) NOT NULL,
  `is_active` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chats`
--

DROP TABLE IF EXISTS `chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chats` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sourceid` int NOT NULL,
  `userid` int NOT NULL,
  `messages` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cold_lead_broadcasts`
--

DROP TABLE IF EXISTS `cold_lead_broadcasts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cold_lead_broadcasts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_of_users` int NOT NULL,
  `frequency` int NOT NULL,
  `started_at` datetime NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` text COLLATE utf8mb4_unicode_ci,
  `messages_sent` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `frequency_completed` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cold_leads`
--

DROP TABLE IF EXISTS `cold_leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cold_leads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `image` text COLLATE utf8mb4_unicode_ci,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `because_of` text COLLATE utf8mb4_unicode_ci,
  `status` int NOT NULL DEFAULT '0',
  `messages_sent` int NOT NULL DEFAULT '0',
  `account_id` int DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_gender_processed` tinyint(1) NOT NULL DEFAULT '0',
  `is_country_processed` tinyint(1) NOT NULL DEFAULT '0',
  `followed_by` int DEFAULT NULL,
  `is_imported` tinyint(1) NOT NULL DEFAULT '0',
  `address` text COLLATE utf8mb4_unicode_ci,
  `customer_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35355 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `color_names_references`
--

DROP TABLE IF EXISTS `color_names_references`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `color_names_references` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `color_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `erp_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2213 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `color_references`
--

DROP TABLE IF EXISTS `color_references`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `color_references` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int NOT NULL,
  `original_color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `erp_color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `command_execution_historys`
--

DROP TABLE IF EXISTS `command_execution_historys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `command_execution_historys` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `command_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `command_answer` longtext COLLATE utf8mb4_unicode_ci,
  `user_id` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comments_stats`
--

DROP TABLE IF EXISTS `comments_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments_stats` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `target` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_author` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_send` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `narrative` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'common',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `communication_histories`
--

DROP TABLE IF EXISTS `communication_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `communication_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int unsigned DEFAULT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refer_id` int DEFAULT NULL,
  `is_stopped` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `communication_histories_refer_id_index` (`refer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=382 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `competitor_followers`
--

DROP TABLE IF EXISTS `competitor_followers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `competitor_followers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `competitor_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `competitor_pages`
--

DROP TABLE IF EXISTS `competitor_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `competitor_pages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'instagram',
  `platform_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cursor` text COLLATE utf8mb4_unicode_ci,
  `is_processed` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `competitors`
--

DROP TABLE IF EXISTS `competitors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `competitors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tool_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `database` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtype` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `common_keywords` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keywords` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `traffic` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `domain` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `complaint_threads`
--

DROP TABLE IF EXISTS `complaint_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `complaint_threads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `complaint_id` int unsigned NOT NULL,
  `account_id` int unsigned DEFAULT NULL,
  `thread` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `complaint_threads_complaint_id_foreign` (`complaint_id`),
  KEY `complaint_threads_account_id_foreign` (`account_id`),
  CONSTRAINT `complaint_threads_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `complaint_threads_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `complaints` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int DEFAULT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complaint` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan_of_action` text COLLATE utf8mb4_unicode_ci,
  `where` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thread_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `media_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `receipt_username` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_customer_flagged` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `complaints_customer_id_foreign` (`customer_id`),
  CONSTRAINT `complaints_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compositions`
--

DROP TABLE IF EXISTS `compositions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compositions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `replace_with` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compositions_replace_with_index` (`replace_with`)
) ENGINE=InnoDB AUTO_INCREMENT=10879 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact_bloggers`
--

DROP TABLE IF EXISTS `contact_bloggers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_bloggers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_handle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quote` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contacts_user_id_foreign` (`user_id`),
  CONSTRAINT `contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_manageent_emails`
--

DROP TABLE IF EXISTS `content_manageent_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `content_manageent_emails` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `country_duties`
--

DROP TABLE IF EXISTS `country_duties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country_duties` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `hs_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `origin` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `duty` decimal(8,2) NOT NULL,
  `vat` decimal(8,2) NOT NULL,
  `duty_percentage` decimal(8,2) NOT NULL,
  `vat_percentage` decimal(8,2) NOT NULL,
  `duty_group_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=254 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `country_group_items`
--

DROP TABLE IF EXISTS `country_group_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country_group_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `country_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_group_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `country_groups`
--

DROP TABLE IF EXISTS `country_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coupon_code_rules`
--

DROP TABLE IF EXISTS `coupon_code_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_code_rules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` int NOT NULL DEFAULT '0',
  `times_used` int NOT NULL DEFAULT '0',
  `website_ids` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_group_ids` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `coupon_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `coupon_code` text COLLATE utf8mb4_unicode_ci,
  `use_auto_generation` int NOT NULL DEFAULT '0',
  `uses_per_coupon` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uses_per_coustomer` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_website_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_rss` int NOT NULL DEFAULT '0',
  `priority` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `magento_rule_id` int DEFAULT NULL,
  `stop_rules_processing` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `simple_action` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_step` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_qty` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apply_to_shipping` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `simple_free_shipping` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `magento_id` bigint unsigned DEFAULT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start` datetime NOT NULL,
  `expiration` datetime DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_fixed` double(8,2) NOT NULL DEFAULT '0.00',
  `discount_percentage` double(8,2) NOT NULL DEFAULT '0.00',
  `minimum_order_amount` smallint unsigned NOT NULL DEFAULT '0',
  `maximum_usage` smallint unsigned DEFAULT NULL,
  `usage_count` smallint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `initial_amount` double(15,8) DEFAULT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coupon_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `rule_id` int NOT NULL,
  `uses` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupons_initial_amount_index` (`initial_amount`),
  KEY `coupons_email_index` (`email`),
  KEY `coupons_coupon_type_index` (`coupon_type`),
  KEY `coupons_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `courier`
--

DROP TABLE IF EXISTS `courier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courier` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `credit_history`
--

DROP TABLE IF EXISTS `credit_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int DEFAULT NULL,
  `model_id` int DEFAULT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `used_credit` decimal(15,2) DEFAULT NULL,
  `used_in` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. for order so value will be like ORDER',
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'value added or minus so value will be ADD, MINUS',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_history_customer_id_foreign` (`customer_id`),
  CONSTRAINT `credit_history_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `credit_logs`
--

DROP TABLE IF EXISTS `credit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `request` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cron_job_reports`
--

DROP TABLE IF EXISTS `cron_job_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cron_job_reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `signature` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1055698 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cron_jobs`
--

DROP TABLE IF EXISTS `cron_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cron_jobs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `signature` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `schedule` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_count` int NOT NULL DEFAULT '0',
  `last_error` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cronfrequency_parameters`
--

DROP TABLE IF EXISTS `cronfrequency_parameters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cronfrequency_parameters` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `frequency_id` int unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crontask_frequencies`
--

DROP TABLE IF EXISTS `crontask_frequencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crontask_frequencies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int unsigned NOT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `interval` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_frequencies_task_id_idx` (`task_id`),
  CONSTRAINT `task_frequencies_task_id_fk` FOREIGN KEY (`task_id`) REFERENCES `crontasks` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crontask_results`
--

DROP TABLE IF EXISTS `crontask_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crontask_results` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int unsigned NOT NULL,
  `ran_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `duration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `result` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_results_task_id_idx` (`task_id`),
  KEY `task_results_ran_at_idx` (`ran_at`),
  CONSTRAINT `task_id_fk` FOREIGN KEY (`task_id`) REFERENCES `crontasks` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crontasks`
--

DROP TABLE IF EXISTS `crontasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crontasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `command` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parameters` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expression` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UTC',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `dont_overlap` tinyint(1) NOT NULL DEFAULT '0',
  `run_in_maintenance` tinyint(1) NOT NULL DEFAULT '0',
  `notification_email_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_phone_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_slack_webhook` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `auto_cleanup_num` int NOT NULL DEFAULT '0',
  `auto_cleanup_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `run_on_one_server` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tasks_is_active_idx` (`is_active`),
  KEY `tasks_dont_overlap_idx` (`dont_overlap`),
  KEY `tasks_run_in_maintenance_idx` (`run_in_maintenance`),
  KEY `tasks_run_on_one_server_idx` (`run_on_one_server`),
  KEY `tasks_auto_cleanup_num_idx` (`auto_cleanup_num`),
  KEY `tasks_auto_cleanup_type_idx` (`auto_cleanup_type`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crop_amends`
--

DROP TABLE IF EXISTS `crop_amends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crop_amends` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `file_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `settings` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crop_image_get_requests`
--

DROP TABLE IF EXISTS `crop_image_get_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crop_image_get_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned DEFAULT NULL,
  `request` text COLLATE utf8mb4_unicode_ci,
  `response` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crop_image_get_requests_product_id_index` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crop_image_http_request_responses`
--

DROP TABLE IF EXISTS `crop_image_http_request_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crop_image_http_request_responses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cropped_image_reference_id` int unsigned NOT NULL,
  `request` text COLLATE utf8mb4_unicode_ci,
  `response` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `crop_image_get_request_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cropped_image_ref_id` (`cropped_image_reference_id`),
  KEY `cropped_image_get_req_id` (`crop_image_get_request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cropped_image_references`
--

DROP TABLE IF EXISTS `cropped_image_references`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cropped_image_references` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `original_media_id` int NOT NULL,
  `original_media_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_media_id` int NOT NULL,
  `new_media_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `speed` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instance_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cropped_image_references_product_id_index` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cropping_instances`
--

DROP TABLE IF EXISTS `cropping_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cropping_instances` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `instance_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rate` double(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_address_datas`
--

DROP TABLE IF EXISTS `customer_address_datas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_address_datas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firstname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middlename` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prefix` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_basket_products`
--

DROP TABLE IF EXISTS `customer_basket_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_basket_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_basket_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `product_sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_price` decimal(10,0) NOT NULL DEFAULT '0',
  `product_currency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_baskets`
--

DROP TABLE IF EXISTS `customer_baskets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_baskets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int DEFAULT NULL,
  `customer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  `language_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_bulk_messages_dnd`
--

DROP TABLE IF EXISTS `customer_bulk_messages_dnd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_bulk_messages_dnd` (
  `customer_id` bigint unsigned NOT NULL,
  `filter` varchar(251) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`customer_id`,`filter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_categories`
--

DROP TABLE IF EXISTS `customer_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_charities`
--

DROP TABLE IF EXISTS `customer_charities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_charities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_handle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_iban` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_swift` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `frequency` int NOT NULL DEFAULT '0',
  `reminder_last_reply` int NOT NULL DEFAULT '1',
  `reminder_from` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reminder_message` text COLLATE utf8mb4_unicode_ci,
  `has_error` tinyint NOT NULL DEFAULT '0',
  `is_blocked` tinyint NOT NULL DEFAULT '0',
  `updated_by` int NOT NULL,
  `status` int DEFAULT '1',
  `frequency_of_payment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ifsc_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` text COLLATE utf8mb4_unicode_ci,
  `chat_session_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_charity_website_stores`
--

DROP TABLE IF EXISTS `customer_charity_website_stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_charity_website_stores` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_charity_id` int DEFAULT NULL,
  `website_store_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_kyc_documents`
--

DROP TABLE IF EXISTS `customer_kyc_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_kyc_documents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `type` int NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_live_chats`
--

DROP TABLE IF EXISTS `customer_live_chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_live_chats` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `thread` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int DEFAULT NULL,
  `seen` int NOT NULL DEFAULT '0',
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_live_chats_customer_id_index` (`customer_id`),
  KEY `customer_live_chats_seen_index` (`seen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_marketing_platforms`
--

DROP TABLE IF EXISTS `customer_marketing_platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_marketing_platforms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `marketing_platform_id` int NOT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint NOT NULL DEFAULT '0',
  `remark` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_next_actions`
--

DROP TABLE IF EXISTS `customer_next_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_next_actions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_order_charities`
--

DROP TABLE IF EXISTS `customer_order_charities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_order_charities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `order_id` int NOT NULL,
  `charity_id` int NOT NULL,
  `amount` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_contribution` int NOT NULL,
  `our_contribution` int NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_with_categories`
--

DROP TABLE IF EXISTS `customer_with_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_with_categories` (
  `customer_id` int unsigned NOT NULL,
  `category_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `whatsapp_number` varchar(255) DEFAULT NULL,
  `broadcast_number` varchar(191) DEFAULT NULL,
  `instahandler` varchar(255) DEFAULT NULL,
  `ig_username` varchar(255) DEFAULT NULL,
  `shoe_size` varchar(191) DEFAULT NULL,
  `clothing_size` varchar(191) DEFAULT NULL,
  `gender` varchar(191) DEFAULT NULL,
  `rating` int NOT NULL DEFAULT '1',
  `do_not_disturb` tinyint(1) NOT NULL DEFAULT '0',
  `is_blocked_lead` int DEFAULT '0',
  `lead_product_freq` int DEFAULT '0',
  `is_blocked` tinyint(1) NOT NULL DEFAULT '0',
  `is_flagged` tinyint(1) NOT NULL DEFAULT '0',
  `is_error_flagged` tinyint(1) NOT NULL DEFAULT '0',
  `is_priority` tinyint(1) NOT NULL DEFAULT '0',
  `credit` varchar(191) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `pincode` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `notes` longtext,
  `instruction_completed_at` datetime DEFAULT NULL,
  `facebook_id` varchar(191) DEFAULT NULL,
  `frequency` int DEFAULT NULL,
  `reminder_last_reply` int NOT NULL DEFAULT '1',
  `reminder_from` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `language` varchar(191) NOT NULL,
  `reminder_message` text,
  `is_categorized_for_bulk_messages` tinyint(1) NOT NULL DEFAULT '0',
  `customer_next_action_id` int NOT NULL,
  `chat_session_id` varchar(255) DEFAULT NULL,
  `in_w_list` int DEFAULT '0',
  `store_website_id` int DEFAULT NULL,
  `store_name` varchar(191) DEFAULT NULL,
  `newsletter` int DEFAULT '0',
  `user_id` int DEFAULT NULL,
  `updated_by` int NOT NULL,
  `currency` varchar(191) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `wedding_anniversery` date DEFAULT NULL,
  `source` varchar(191) DEFAULT NULL,
  `platform_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customers_language_index` (`language`),
  KEY `customers_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3055 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daily_activities`
--

DROP TABLE IF EXISTS `daily_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_activities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `time_slot` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity` longtext COLLATE utf8mb4_unicode_ci,
  `user_id` int NOT NULL,
  `is_admin` int DEFAULT NULL,
  `assist_msg` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `for_date` date NOT NULL,
  `pending_for` int NOT NULL DEFAULT '0',
  `actual_start_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0000-00-00 00:00:00',
  `timezone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_completed` datetime DEFAULT NULL,
  `general_category_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `repeat_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `repeat_on` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `repeat_end` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `repeat_end_date` date DEFAULT NULL,
  `parent_row` int DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `for_datetime` datetime DEFAULT NULL,
  `type` enum('event','learning') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'event',
  `type_table_id` int DEFAULT NULL,
  `next_run_at` date DEFAULT NULL COMMENT 'if type learning for daily',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16090 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daily_activities_histories`
--

DROP TABLE IF EXISTS `daily_activities_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_activities_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `daily_activities_id` int DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daily_cash_flows`
--

DROP TABLE IF EXISTS `daily_cash_flows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_cash_flows` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `received_from` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected` int DEFAULT NULL,
  `received` int DEFAULT NULL,
  `date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `database_historical_records`
--

DROP TABLE IF EXISTS `database_historical_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `database_historical_records` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `database_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` double(25,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `database_table_historical_records`
--

DROP TABLE IF EXISTS `database_table_historical_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `database_table_historical_records` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `database_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` double(25,2) NOT NULL,
  `database_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1302 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `delivery_approvals`
--

DROP TABLE IF EXISTS `delivery_approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_approvals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `private_view_id` int DEFAULT NULL,
  `assigned_user_id` int unsigned DEFAULT NULL,
  `approved` tinyint NOT NULL DEFAULT '0',
  `status` varchar(191) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_approvals_private_view_id_foreign` (`private_view_id`),
  KEY `delivery_approvals_assigned_user_id_foreign` (`assigned_user_id`),
  CONSTRAINT `delivery_approvals_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `delivery_approvals_private_view_id_foreign` FOREIGN KEY (`private_view_id`) REFERENCES `private_views` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `description_changes`
--

DROP TABLE IF EXISTS `description_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `description_changes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `replace_with` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `designers`
--

DROP TABLE IF EXISTS `designers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `designers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `website` text COLLATE utf8mb4_general_ci NOT NULL,
  `title` tinytext COLLATE utf8mb4_general_ci NOT NULL,
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `designers` mediumtext COLLATE utf8mb4_general_ci NOT NULL,
  `image` mediumtext COLLATE utf8mb4_general_ci,
  `email` text COLLATE utf8mb4_general_ci,
  `social_handle` text COLLATE utf8mb4_general_ci,
  `instagram_handle` text COLLATE utf8mb4_general_ci,
  `site_link` text COLLATE utf8mb4_general_ci,
  `phone` text COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `developer_comments`
--

DROP TABLE IF EXISTS `developer_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `developer_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `send_to` int NOT NULL,
  `message` longtext NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `developer_costs`
--

DROP TABLE IF EXISTS `developer_costs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `developer_costs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `amount` int NOT NULL,
  `paid_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `developer_languages`
--

DROP TABLE IF EXISTS `developer_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `developer_languages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `developer_messages_alert_schedules`
--

DROP TABLE IF EXISTS `developer_messages_alert_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `developer_messages_alert_schedules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `time` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `developer_modules`
--

DROP TABLE IF EXISTS `developer_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `developer_modules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `developer_modules_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `developer_task_comments`
--

DROP TABLE IF EXISTS `developer_task_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `developer_task_comments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `developer_task_documents`
--

DROP TABLE IF EXISTS `developer_task_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `developer_task_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `description` text,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `developer_task_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `developer_tasks`
--

DROP TABLE IF EXISTS `developer_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `developer_tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `module_id` int DEFAULT NULL,
  `log_keyword_id` int DEFAULT NULL,
  `priority` int NOT NULL,
  `subject` varchar(191) DEFAULT NULL,
  `task` longtext NOT NULL,
  `cost` int DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `is_flagged` tinyint NOT NULL DEFAULT '0',
  `module` int NOT NULL DEFAULT '0',
  `completed` tinyint NOT NULL DEFAULT '0',
  `estimate_time` timestamp NULL DEFAULT NULL,
  `estimate_minutes` int DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `assigned_by` int DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  `parent_id` int NOT NULL,
  `created_by` int NOT NULL,
  `task_type_id` int NOT NULL,
  `is_resolved` tinyint NOT NULL DEFAULT '0',
  `reference` text,
  `object` varchar(191) DEFAULT NULL,
  `object_id` int DEFAULT NULL,
  `responsible_user_id` int NOT NULL,
  `master_user_id` int NOT NULL DEFAULT '0',
  `master_user_priority` int NOT NULL DEFAULT '0',
  `language` varchar(191) DEFAULT NULL,
  `hubstaff_task_id` int unsigned NOT NULL,
  `github_branch_name` varchar(191) DEFAULT NULL,
  `is_milestone` tinyint(1) NOT NULL DEFAULT '0',
  `no_of_milestone` int DEFAULT NULL,
  `milestone_completed` int DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `lead_hubstaff_task_id` int DEFAULT NULL,
  `team_lead_id` int DEFAULT NULL,
  `tester_id` int DEFAULT NULL,
  `team_lead_hubstaff_task_id` int DEFAULT NULL,
  `tester_hubstaff_task_id` int DEFAULT NULL,
  `scraper_id` int DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  `estimate_date` date NOT NULL,
  `lead_estimate_time` varchar(191) DEFAULT NULL,
  `site_developement_id` int DEFAULT NULL,
  `priority_no` int DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `frequency` int NOT NULL DEFAULT '0',
  `last_send_reminder` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reminder_last_reply` int NOT NULL DEFAULT '1',
  `reminder_from` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reminder_message` text,
  `repository_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `developer_tasks_brand_id_index` (`brand_id`),
  KEY `hubstaff_task_id` (`hubstaff_task_id`),
  KEY `developer_tasks_status_assigned_to_tester_id_team_lead_id_index` (`status`,`assigned_to`,`tester_id`,`team_lead_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3931 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `developer_tasks_history`
--

DROP TABLE IF EXISTS `developer_tasks_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `developer_tasks_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `developer_task_id` int NOT NULL,
  `attribute` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `model` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `developer_tasks_history_developer_task_id_index` (`developer_task_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `digital_marketing_platform_components`
--

DROP TABLE IF EXISTS `digital_marketing_platform_components`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `digital_marketing_platform_components` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `digital_marketing_platform_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `digital_marketing_platform_files`
--

DROP TABLE IF EXISTS `digital_marketing_platform_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `digital_marketing_platform_files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `digital_marketing_platform_id` int NOT NULL,
  `user_id` int NOT NULL,
  `file_name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `digital_marketing_platform_remarks`
--

DROP TABLE IF EXISTS `digital_marketing_platform_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `digital_marketing_platform_remarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `digital_marketing_platform_id` int NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `digital_marketing_platforms`
--

DROP TABLE IF EXISTS `digital_marketing_platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `digital_marketing_platforms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_platform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `digital_marketing_solution_attributes`
--

DROP TABLE IF EXISTS `digital_marketing_solution_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `digital_marketing_solution_attributes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `digital_marketing_solution_id` int NOT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `digital_marketing_solution_files`
--

DROP TABLE IF EXISTS `digital_marketing_solution_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `digital_marketing_solution_files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `digital_marketing_solution_id` int NOT NULL,
  `user_id` int NOT NULL,
  `file_name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `digital_marketing_solution_researches`
--

DROP TABLE IF EXISTS `digital_marketing_solution_researches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `digital_marketing_solution_researches` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `priority` int NOT NULL,
  `digital_marketing_solution_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `digital_marketing_solutions`
--

DROP TABLE IF EXISTS `digital_marketing_solutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `digital_marketing_solutions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` text COLLATE utf8mb4_unicode_ci,
  `digital_marketing_platform_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `digital_marketing_usps`
--

DROP TABLE IF EXISTS `digital_marketing_usps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `digital_marketing_usps` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `digital_marketing_platform_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `display_advertising_reports`
--

DROP TABLE IF EXISTS `display_advertising_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `display_advertising_reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `tool_id` int NOT NULL,
  `database` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publisher_display_ads` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `advertisers` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `publishers` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `advertiser_display_ads` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `landing_pages` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `advertiser_display_ads_on_a_publishers_website` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `advertisers_rank` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `publishers_rank` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document_categories`
--

DROP TABLE IF EXISTS `document_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document_histories`
--

DROP TABLE IF EXISTS `document_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `document_id` int NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int NOT NULL,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document_remarks`
--

DROP TABLE IF EXISTS `document_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_remarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `document_id` int NOT NULL,
  `module_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remark` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document_send_histories`
--

DROP TABLE IF EXISTS `document_send_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_send_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `send_by` int NOT NULL,
  `send_to` int NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `via` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `version` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `from_email` tinyint NOT NULL DEFAULT '0',
  `file_contents` blob,
  PRIMARY KEY (`id`),
  KEY `documents_user_id_foreign` (`user_id`),
  CONSTRAINT `documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=179 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domain_landing_pages`
--

DROP TABLE IF EXISTS `domain_landing_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `domain_landing_pages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `tool_id` int NOT NULL,
  `database` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_seen` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_seen` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `times_seen` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ads_count` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domain_organic_page`
--

DROP TABLE IF EXISTS `domain_organic_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `domain_organic_page` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tool_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `database` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `Url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_of_keywords` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `traffic` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `traffic_percentage` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domain_overviews`
--

DROP TABLE IF EXISTS `domain_overviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `domain_overviews` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `tool_id` int NOT NULL,
  `database` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rank` int NOT NULL,
  `organic_keywords` int NOT NULL,
  `organic_traffic` int NOT NULL,
  `organic_cost` int NOT NULL,
  `adwords_keywords` int NOT NULL,
  `adwords_traffic` int NOT NULL,
  `adwords_cost` int NOT NULL,
  `pla_keywords` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pla_uniques` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domain_reports`
--

DROP TABLE IF EXISTS `domain_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `domain_reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `tool_id` int NOT NULL,
  `database` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain_organic_search_keywords` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain_paid_search_keywords` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ads_copies` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `competitors_in_organic_search` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `competitors_in_paid_search` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain_ad_history` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain_vs_domain` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain_pla_search_keywords` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pla_copies` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pla_competitors` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain_organic_pages` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain_organic_subdomains` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domain_search_keywords`
--

DROP TABLE IF EXISTS `domain_search_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `domain_search_keywords` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `tool_id` int NOT NULL,
  `database` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtype` enum('organic','paid') COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  `previous_position` int NOT NULL,
  `position_difference` int NOT NULL,
  `search_volume` int NOT NULL,
  `cpc` int NOT NULL,
  `url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `traffic` int NOT NULL,
  `traffic_percentage` int NOT NULL,
  `traffic_cost` int NOT NULL,
  `competition` int NOT NULL,
  `number_of_results` int NOT NULL,
  `trends` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dubbizles`
--

DROP TABLE IF EXISTS `dubbizles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dubbizles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `url` varchar(400) NOT NULL,
  `keywords` varchar(400) NOT NULL,
  `post_date` varchar(50) NOT NULL,
  `requirements` varchar(400) NOT NULL,
  `body` varchar(10000) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `frequency` int DEFAULT NULL,
  `reminder_message` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `duty_group_countries`
--

DROP TABLE IF EXISTS `duty_group_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `duty_group_countries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `duty_group_id` bigint unsigned NOT NULL,
  `country_duty_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `duty_group_countries_duty_group_id_foreign` (`duty_group_id`),
  KEY `duty_group_countries_country_duty_id_foreign` (`country_duty_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `duty_groups`
--

DROP TABLE IF EXISTS `duty_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `duty_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hs_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duty` decimal(8,2) NOT NULL,
  `vat` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_addresses`
--

DROP TABLE IF EXISTS `email_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `from_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recovery_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recovery_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `host` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `encryption` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `plesk_email_address_id` int DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  `signature_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_address` text COLLATE utf8mb4_unicode_ci,
  `signature_logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_social` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_assignes`
--

DROP TABLE IF EXISTS `email_assignes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_assignes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email_address_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_category`
--

DROP TABLE IF EXISTS `email_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_category` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_content_history`
--

DROP TABLE IF EXISTS `email_content_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_content_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `mailinglist_templates_id` int NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `date` date DEFAULT NULL,
  `updated_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_events`
--

DROP TABLE IF EXISTS `email_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `list_contact_id` int NOT NULL,
  `template_id` int NOT NULL,
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  `delivered` tinyint(1) NOT NULL DEFAULT '0',
  `opened` tinyint(1) NOT NULL DEFAULT '0',
  `spam` tinyint(1) NOT NULL DEFAULT '0',
  `spam_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_leads`
--

DROP TABLE IF EXISTS `email_leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_leads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3029 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_notification_email_details`
--

DROP TABLE IF EXISTS `email_notification_email_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_notification_email_details` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `emails` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_notification_email_details_emails_index` (`emails`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_remarks`
--

DROP TABLE IF EXISTS `email_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_remarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email_id` int NOT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_run_histories`
--

DROP TABLE IF EXISTS `email_run_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_run_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email_address_id` int NOT NULL,
  `is_success` int NOT NULL DEFAULT '1' COMMENT '1-success,0-fail',
  `message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_status`
--

DROP TABLE IF EXISTS `email_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_templates_key_index` (`key`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emails`
--

DROP TABLE IF EXISTS `emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emails` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int unsigned DEFAULT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'outgoing',
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  `from` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `template` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `additional_data` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_excel_importer` int NOT NULL DEFAULT '0' COMMENT '1 - transfer exist, 2 - executed but we transfer file not exist',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cc` longtext COLLATE utf8mb4_unicode_ci,
  `bcc` longtext COLLATE utf8mb4_unicode_ci,
  `origin_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_draft` tinyint NOT NULL DEFAULT '0',
  `store_website_id` int DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `email_category_id` int NOT NULL,
  `approve_mail` tinyint(1) NOT NULL DEFAULT '0',
  `digital_platfirm` int DEFAULT NULL,
  `message_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `schedule_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emails_store_website_id_index` (`store_website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=493 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entity_permissions`
--

DROP TABLE IF EXISTS `entity_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entity_permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `restrictable_id` int NOT NULL,
  `restrictable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` int NOT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `restrictions_restrictable_id_restrictable_type_index` (`restrictable_id`,`restrictable_type`),
  KEY `restrictions_role_id_index` (`role_id`),
  KEY `restrictions_action_index` (`action`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `erp_accounts`
--

DROP TABLE IF EXISTS `erp_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `erp_accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `table` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `row_id` int DEFAULT NULL,
  `transacted_by` int NOT NULL,
  `debit` decimal(8,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(8,2) NOT NULL DEFAULT '0.00',
  `user_id` int DEFAULT NULL,
  `vendor_id` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `metadata` longtext COLLATE utf8mb4_unicode_ci,
  `remark` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `erp_events`
--

DROP TABLE IF EXISTS `erp_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `erp_events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_description` text COLLATE utf8mb4_unicode_ci,
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` int NOT NULL DEFAULT '0',
  `brand_id` text COLLATE utf8mb4_unicode_ci,
  `category_id` text COLLATE utf8mb4_unicode_ci,
  `number_of_person` int DEFAULT '100',
  `product_start_date` datetime DEFAULT '0000-00-00 00:00:00',
  `product_end_date` datetime DEFAULT '0000-00-00 00:00:00',
  `minute` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `hour` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `day_of_month` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `month` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `day_of_week` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `created_by` int NOT NULL,
  `next_run_date` datetime DEFAULT '0000-00-00 00:00:00',
  `is_closed` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `erp_lead_sending_histories`
--

DROP TABLE IF EXISTS `erp_lead_sending_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `erp_lead_sending_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `customer_id` int NOT NULL,
  `lead_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=710 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `erp_lead_status`
--

DROP TABLE IF EXISTS `erp_lead_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `erp_lead_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `erp_lead_status_histories`
--

DROP TABLE IF EXISTS `erp_lead_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `erp_lead_status_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int NOT NULL,
  `old_status` int NOT NULL,
  `new_status` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `erp_leads`
--

DROP TABLE IF EXISTS `erp_leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `erp_leads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lead_status_id` int unsigned DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `product_id` int unsigned DEFAULT NULL,
  `brand_id` int unsigned DEFAULT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_price` decimal(8,2) DEFAULT '0.00',
  `max_price` decimal(8,2) DEFAULT '0.00',
  `brand_segment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `erp_leads_lead_status_id_index` (`lead_status_id`),
  KEY `erp_leads_customer_id_index` (`customer_id`),
  KEY `erp_leads_product_id_index` (`product_id`),
  KEY `erp_leads_brand_id_index` (`brand_id`),
  KEY `erp_leads_category_id_index` (`category_id`),
  CONSTRAINT `erp_leads_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `erp_leads_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `erp_leads_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `erp_leads_lead_status_id_foreign` FOREIGN KEY (`lead_status_id`) REFERENCES `erp_lead_status` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `erp_leads_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `erp_leads_brands`
--

DROP TABLE IF EXISTS `erp_leads_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `erp_leads_brands` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int NOT NULL,
  `erp_lead_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `erp_leads_categories`
--

DROP TABLE IF EXISTS `erp_leads_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `erp_leads_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `erp_lead_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `erp_logs`
--

DROP TABLE IF EXISTS `erp_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `erp_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int NOT NULL,
  `url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request` text COLLATE utf8mb4_unicode_ci,
  `response` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `erp_priorities`
--

DROP TABLE IF EXISTS `erp_priorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `erp_priorities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `erp_priorities_model_type_index` (`model_type`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `estimated_delivery_histories`
--

DROP TABLE IF EXISTS `estimated_delivery_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estimated_delivery_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `field` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_by` int NOT NULL,
  `old_value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estimated_delivery_histories_order_id_index` (`order_id`),
  KEY `estimated_delivery_histories_field_index` (`field`),
  KEY `estimated_delivery_histories_updated_by_index` (`updated_by`),
  KEY `estimated_delivery_histories_old_value_index` (`old_value`),
  KEY `estimated_delivery_histories_new_value_index` (`new_value`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `excel_importer_details`
--

DROP TABLE IF EXISTS `excel_importer_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `excel_importer_details` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `excel_importer_id` int NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_tool` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_sku_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `made_in` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `made_in_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discounted_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discounted_price_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_color_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_lmeasurement` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_lmeasurement_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_hmeasurement` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_hmeasurement_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_composition` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_composition_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_dmeasurement` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_dmeasurement_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_measurement` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `property_measurement_tools` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` text COLLATE utf8mb4_unicode_ci,
  `image_tools` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `excel_importers`
--

DROP TABLE IF EXISTS `excel_importers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `excel_importers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `md5` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excel_importer_detail_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `brand` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `excel_impoter_detail_suppliers`
--

DROP TABLE IF EXISTS `excel_impoter_detail_suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `excel_impoter_detail_suppliers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excel_importer_details_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facebook_messages`
--

DROP TABLE IF EXISTS `facebook_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facebook_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `sender` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receiver` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_sent_by_me` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facebook_posts`
--

DROP TABLE IF EXISTS `facebook_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facebook_posts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int NOT NULL,
  `caption` text COLLATE utf8mb4_unicode_ci,
  `post_body` text COLLATE utf8mb4_unicode_ci,
  `post_by` int NOT NULL,
  `posted_on` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fcm_tokens`
--

DROP TABLE IF EXISTS `fcm_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fcm_tokens` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_website_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fcm_tokens_store_website_id_foreign` (`store_website_id`),
  KEY `fcm_tokens_token_index` (`token`),
  CONSTRAINT `fcm_tokens_store_website_id_foreign` FOREIGN KEY (`store_website_id`) REFERENCES `store_websites` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` int unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flagged_instagram_posts`
--

DROP TABLE IF EXISTS `flagged_instagram_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flagged_instagram_posts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `media_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flow_action_messages`
--

DROP TABLE IF EXISTS `flow_action_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flow_action_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `action_id` int NOT NULL,
  `sender_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_email_address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `html_content` text COLLATE utf8mb4_unicode_ci,
  `reply_to_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_email_as_reply_to` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mail_tpl` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flow_actions`
--

DROP TABLE IF EXISTS `flow_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flow_actions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `path_id` int NOT NULL,
  `type_id` int NOT NULL,
  `rank` int NOT NULL,
  `time_delay` int NOT NULL,
  `time_delay_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `message_title` text COLLATE utf8mb4_unicode_ci,
  `condition` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flow_paths`
--

DROP TABLE IF EXISTS `flow_paths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flow_paths` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `flow_id` int NOT NULL,
  `deleted` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `parent_action_id` int DEFAULT NULL,
  `path_for` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flow_types`
--

DROP TABLE IF EXISTS `flow_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flow_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flows`
--

DROP TABLE IF EXISTS `flows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flows` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `flow_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flow_description` text COLLATE utf8mb4_unicode_ci,
  `flow_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `general_categories`
--

DROP TABLE IF EXISTS `general_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gift_cards`
--

DROP TABLE IF EXISTS `gift_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gift_cards` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sender_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receiver_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receiver_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gift_card_coupon_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gift_card_description` text COLLATE utf8mb4_unicode_ci,
  `gift_card_amount` double(8,2) DEFAULT NULL,
  `gift_card_message` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `store_website_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gift_cards_store_website_id_foreign` (`store_website_id`),
  KEY `gift_cards_gift_card_coupon_code_index` (`gift_card_coupon_code`),
  CONSTRAINT `gift_cards_store_website_id_foreign` FOREIGN KEY (`store_website_id`) REFERENCES `store_websites` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `github_branch_states`
--

DROP TABLE IF EXISTS `github_branch_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `github_branch_states` (
  `repository_id` int NOT NULL,
  `branch_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ahead_by` int NOT NULL,
  `behind_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_commit_author_username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_commit_time` datetime DEFAULT NULL,
  PRIMARY KEY (`repository_id`,`branch_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `github_group_members`
--

DROP TABLE IF EXISTS `github_group_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `github_group_members` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `github_groups_id` int NOT NULL,
  `github_users_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `github_groups`
--

DROP TABLE IF EXISTS `github_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `github_groups` (
  `id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `github_repositories`
--

DROP TABLE IF EXISTS `github_repositories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `github_repositories` (
  `id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `html` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `webhook` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `github_repository_groups`
--

DROP TABLE IF EXISTS `github_repository_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `github_repository_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `github_repositories_id` int NOT NULL,
  `github_groups_id` int NOT NULL,
  `rights` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `github_repository_users`
--

DROP TABLE IF EXISTS `github_repository_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `github_repository_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `github_repositories_id` int NOT NULL,
  `github_users_id` int NOT NULL,
  `rights` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=371 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `github_users`
--

DROP TABLE IF EXISTS `github_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `github_users` (
  `id` int NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gmail_data`
--

DROP TABLE IF EXISTS `gmail_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gmail_data` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender` text NOT NULL,
  `received_at` text NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `tags` longtext NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gmail_data_lists`
--

DROP TABLE IF EXISTS `gmail_data_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gmail_data_lists` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `received_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domain` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gmail_data_media`
--

DROP TABLE IF EXISTS `gmail_data_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gmail_data_media` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `gmail_data_list_id` int DEFAULT NULL,
  `images` longtext COLLATE utf8mb4_unicode_ci,
  `page_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_analytic_datas`
--

DROP TABLE IF EXISTS `google_analytic_datas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_analytic_datas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website_analytics_id` int DEFAULT NULL,
  `browser` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iso_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avg_time_page` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_view` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unique_page_views` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exit_rate` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entrances` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entrance_rate` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_analytics`
--

DROP TABLE IF EXISTS `google_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_analytics` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_analytics_audience`
--

DROP TABLE IF EXISTS `google_analytics_audience`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_analytics_audience` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website_analytics_id` int DEFAULT NULL,
  `age` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `google_analytics_audience_website_analytics_id_index` (`website_analytics_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_analytics_geo_network`
--

DROP TABLE IF EXISTS `google_analytics_geo_network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_analytics_geo_network` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website_analytics_id` int DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iso_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `google_analytics_geo_network_website_analytics_id_index` (`website_analytics_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_analytics_histories`
--

DROP TABLE IF EXISTS `google_analytics_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_analytics_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_analytics_page_tracking`
--

DROP TABLE IF EXISTS `google_analytics_page_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_analytics_page_tracking` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website_analytics_id` int DEFAULT NULL,
  `page` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avg_time_page` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_views` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unique_page_views` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exit_rate` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entrances` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entrance_rate` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `google_analytics_page_tracking_website_analytics_id_index` (`website_analytics_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_analytics_platform_device`
--

DROP TABLE IF EXISTS `google_analytics_platform_device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_analytics_platform_device` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website_analytics_id` int DEFAULT NULL,
  `browser` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `google_analytics_platform_device_website_analytics_id_index` (`website_analytics_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_analytics_user`
--

DROP TABLE IF EXISTS `google_analytics_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_analytics_user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website_analytics_id` int DEFAULT NULL,
  `user_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `google_analytics_user_website_analytics_id_index` (`website_analytics_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_client_account_mails`
--

DROP TABLE IF EXISTS `google_client_account_mails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_client_account_mails` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `google_account` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_client_account_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GOOGLE_CLIENT_REFRESH_TOKEN` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GOOGLE_CLIENT_ACCESS_TOKEN` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_in` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_client_accounts`
--

DROP TABLE IF EXISTS `google_client_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_client_accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `GOOGLE_CLIENT_ID` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GOOGLE_CLIENT_SECRET` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GOOGLE_CLIENT_KEY` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GOOGLE_CLIENT_APPLICATION_NAME` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GOOGLE_CLIENT_MULTIPLE_KEYS` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_in` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_client_notifications`
--

DROP TABLE IF EXISTS `google_client_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_client_notifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `google_client_id` int NOT NULL,
  `receiver_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notification_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_scrapper_content`
--

DROP TABLE IF EXISTS `google_scrapper_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_scrapper_content` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `image` text CHARACTER SET latin1,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `about_us` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `facebook` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instagram` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_scrapper_keywords`
--

DROP TABLE IF EXISTS `google_scrapper_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_scrapper_keywords` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `end` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_search_analytics`
--

DROP TABLE IF EXISTS `google_search_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_search_analytics` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int unsigned DEFAULT NULL,
  `clicks` double(16,2) DEFAULT NULL,
  `impressions` double(8,2) DEFAULT NULL,
  `ctr` double(8,2) DEFAULT NULL,
  `position` double(8,2) DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `query` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `search_apperiance` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `google_search_analytics_site_id_foreign` (`site_id`),
  CONSTRAINT `google_search_analytics_site_id_foreign` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_search_images`
--

DROP TABLE IF EXISTS `google_search_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_search_images` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `crop_image` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_search_related_images`
--

DROP TABLE IF EXISTS `google_search_related_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_search_related_images` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `google_search_image_id` int DEFAULT NULL,
  `google_image` longtext COLLATE utf8mb4_unicode_ci,
  `image_url` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_server`
--

DROP TABLE IF EXISTS `google_server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_server` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_traslation_settings`
--

DROP TABLE IF EXISTS `google_traslation_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_traslation_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_error_at` timestamp NULL DEFAULT NULL,
  `account_json` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `google_traslation_settings_email_index` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_web_masters`
--

DROP TABLE IF EXISTS `google_web_masters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `google_web_masters` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sites` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crawls` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `googleads`
--

DROP TABLE IF EXISTS `googleads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `googleads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `adgroup_google_campaign_id` bigint unsigned DEFAULT NULL,
  `google_adgroup_id` bigint unsigned DEFAULT NULL,
  `google_ad_id` bigint unsigned DEFAULT NULL,
  `headline1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `headline2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `headline3` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_url` text COLLATE utf8mb4_unicode_ci,
  `path1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ads_response` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'E.g UNKNOWN, ENABLED,PAUSED,REMOVED',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `googleadsaccounts`
--

DROP TABLE IF EXISTS `googleadsaccounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `googleadsaccounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `account_name` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_websites` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `config_file_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'it is basically will be adsapi_php.ini',
  `notes` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_error` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_error_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `googleadsgroups`
--

DROP TABLE IF EXISTS `googleadsgroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `googleadsgroups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `adgroup_google_campaign_id` bigint unsigned DEFAULT NULL,
  `google_adgroup_id` bigint unsigned DEFAULT NULL,
  `ad_group_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bid` decimal(15,2) DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'E.g UNKNOWN, ENABLED,PAUSED,REMOVED',
  `adgroup_response` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `googlecampaigns`
--

DROP TABLE IF EXISTS `googlecampaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `googlecampaigns` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int unsigned DEFAULT NULL,
  `google_campaign_id` bigint unsigned DEFAULT NULL,
  `campaign_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `budget_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `start_date` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'format like 20201023',
  `end_date` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'format like 20201122',
  `budget_uniq_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `budget_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `merchant_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sales_country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `channel_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `channel_sub_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bidding_strategy_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_cpa_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_roas_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maximize_clicks` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ad_rotation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campaign_response` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT 'UNKNOWN, ENABLED,PAUSED,REMOVED',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `googlecampaigns_account_id_foreign` (`account_id`),
  CONSTRAINT `googlecampaigns_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `googleadsaccounts` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `googlefiletranslatorfiles`
--

DROP TABLE IF EXISTS `googlefiletranslatorfiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `googlefiletranslatorfiles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tolanguage` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `googlefiletranslatorfiles_name_index` (`name`),
  KEY `googlefiletranslatorfiles_tolanguage_index` (`tolanguage`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `googlescrapping`
--

DROP TABLE IF EXISTS `googlescrapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `googlescrapping` (
  `id` int NOT NULL AUTO_INCREMENT,
  `keyword` text COLLATE utf8mb4_general_ci,
  `name` text COLLATE utf8mb4_general_ci NOT NULL,
  `link` mediumtext COLLATE utf8mb4_general_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `source` varchar(191) COLLATE utf8mb4_general_ci NOT NULL,
  `is_updated` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `group_members`
--

DROP TABLE IF EXISTS `group_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `group_members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_name` varchar(191) DEFAULT NULL,
  `group_url` varchar(400) NOT NULL,
  `username` varchar(191) NOT NULL,
  `profile_url` varchar(400) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `group_routes`
--

DROP TABLE IF EXISTS `group_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `group_routes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL,
  `route_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domain` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hash_tags`
--

DROP TABLE IF EXISTS `hash_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hash_tags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `platforms_id` int NOT NULL,
  `hashtag` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `rating` int NOT NULL DEFAULT '5',
  `post_count` int NOT NULL DEFAULT '0',
  `is_processed` tinyint NOT NULL DEFAULT '0',
  `instagram_account_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hash_tags_platforms_id_index` (`platforms_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14938 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hashtag_post_comments`
--

DROP TABLE IF EXISTS `hashtag_post_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hashtag_post_comments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_url` varchar(400) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hashtag_post_id` int unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_commented` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `review_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hashtag_post_histories`
--

DROP TABLE IF EXISTS `hashtag_post_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hashtag_post_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashtag` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` int DEFAULT NULL,
  `instagram_automated_message_id` int DEFAULT NULL,
  `post_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cursor` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hashtag_post_likes`
--

DROP TABLE IF EXISTS `hashtag_post_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hashtag_post_likes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(191) NOT NULL,
  `profile_url` varchar(400) NOT NULL,
  `hashtag_post_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hashtag_posts`
--

DROP TABLE IF EXISTS `hashtag_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hashtag_posts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashtag_id` int unsigned NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` text COLLATE utf8mb4_unicode_ci,
  `post_url` text COLLATE utf8mb4_unicode_ci,
  `created_date` datetime DEFAULT NULL,
  `likes` int NOT NULL DEFAULT '0',
  `number_comments` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `historial_datas`
--

DROP TABLE IF EXISTS `historial_datas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_datas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `object` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `measuring_point` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `history_whatsapp_number`
--

DROP TABLE IF EXISTS `history_whatsapp_number`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `history_whatsapp_number` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `date_time` datetime NOT NULL,
  `object` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `object_id` int NOT NULL,
  `old_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hs_code_groups`
--

DROP TABLE IF EXISTS `hs_code_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hs_code_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `hs_code_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `composition` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hs_code_groups_categories_compositions`
--

DROP TABLE IF EXISTS `hs_code_groups_categories_compositions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hs_code_groups_categories_compositions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `hs_code_group_id` int NOT NULL,
  `category_id` int NOT NULL,
  `composition` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hs_code_settings`
--

DROP TABLE IF EXISTS `hs_code_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hs_code_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination_country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hs_codes`
--

DROP TABLE IF EXISTS `hs_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hs_codes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hubstaff_activities`
--

DROP TABLE IF EXISTS `hubstaff_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hubstaff_activities` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `task_id` int NOT NULL,
  `starts_at` datetime NOT NULL,
  `tracked` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `keyboard` int NOT NULL,
  `mouse` int NOT NULL,
  `overall` int NOT NULL,
  `hubstaff_payment_account_id` int DEFAULT NULL,
  `status` tinyint NOT NULL,
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `is_manual` tinyint(1) NOT NULL DEFAULT '0',
  `user_notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`task_id`),
  KEY `user_id_2` (`user_id`,`task_id`),
  KEY `starts_at` (`starts_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hubstaff_activity_by_payment_frequencies`
--

DROP TABLE IF EXISTS `hubstaff_activity_by_payment_frequencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hubstaff_activity_by_payment_frequencies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `activity_excel_file` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hubstaff_activity_notifications`
--

DROP TABLE IF EXISTS `hubstaff_activity_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hubstaff_activity_notifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `hubstaff_user_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_track` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `min_percentage` double(8,2) NOT NULL DEFAULT '0.00',
  `actual_percentage` double(8,2) NOT NULL DEFAULT '0.00',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int NOT NULL,
  `client_remarks` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hubstaff_activity_summaries`
--

DROP TABLE IF EXISTS `hubstaff_activity_summaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hubstaff_activity_summaries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `date` date NOT NULL,
  `tracked` int NOT NULL DEFAULT '0',
  `user_requested` int NOT NULL,
  `accepted` int NOT NULL DEFAULT '0',
  `rejected` int NOT NULL DEFAULT '0',
  `rejection_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sender` int NOT NULL,
  `receiver` int NOT NULL,
  `forworded_person` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved_ids` json DEFAULT NULL,
  `rejected_ids` json DEFAULT NULL,
  `final_approval` tinyint(1) NOT NULL DEFAULT '0',
  `pending` int DEFAULT NULL,
  `pending_ids` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hubstaff_historys`
--

DROP TABLE IF EXISTS `hubstaff_historys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hubstaff_historys` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `developer_task_id` bigint unsigned NOT NULL,
  `old_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hubstaff_members`
--

DROP TABLE IF EXISTS `hubstaff_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hubstaff_members` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `hubstaff_user_id` int NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `pay_rate` double(8,2) NOT NULL,
  `bill_rate` double(8,2) NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_activity_percentage` double(8,2) NOT NULL DEFAULT '0.00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hubstaff_user_id` (`hubstaff_user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=221 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hubstaff_payment_accounts`
--

DROP TABLE IF EXISTS `hubstaff_payment_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hubstaff_payment_accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `accounted_at` datetime NOT NULL,
  `billing_start` datetime NOT NULL,
  `billing_end` datetime NOT NULL,
  `hrs` double(8,2) NOT NULL DEFAULT '0.00',
  `rate` double(8,2) NOT NULL DEFAULT '0.00',
  `currency` char(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `payment_currency` char(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INR',
  `ex_rate` double(8,2) NOT NULL DEFAULT '0.00',
  `status` int NOT NULL DEFAULT '1',
  `payment_info` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_remark` text COLLATE utf8mb4_unicode_ci,
  `scheduled_on` datetime NOT NULL,
  `total_payout` double(8,2) NOT NULL DEFAULT '0.00',
  `total_paid` double(8,2) NOT NULL DEFAULT '0.00',
  `amount` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hubstaff_projects`
--

DROP TABLE IF EXISTS `hubstaff_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hubstaff_projects` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `hubstaff_project_id` int NOT NULL,
  `organisation_id` int NOT NULL,
  `hubstaff_project_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hubstaff_project_description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hubstaff_project_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hubstaff_task_efficiency`
--

DROP TABLE IF EXISTS `hubstaff_task_efficiency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hubstaff_task_efficiency` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT '0',
  `admin_input` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_input` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hubstaff_task_notes`
--

DROP TABLE IF EXISTS `hubstaff_task_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hubstaff_task_notes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hubstaff_tasks`
--

DROP TABLE IF EXISTS `hubstaff_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hubstaff_tasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `hubstaff_task_id` int NOT NULL,
  `project_id` int NOT NULL,
  `hubstaff_project_id` int NOT NULL,
  `summary` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `im_queues`
--

DROP TABLE IF EXISTS `im_queues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `im_queues` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `im_client` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_to` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_from` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci,
  `image` text COLLATE utf8mb4_unicode_ci,
  `priority` int DEFAULT '10',
  `marketing_message_type_id` int DEFAULT NULL,
  `broadcast_id` int DEFAULT NULL,
  `send_after` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=228 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `image_schedules`
--

DROP TABLE IF EXISTS `image_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `image_schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_id` int unsigned NOT NULL,
  `description` text,
  `scheduled_for` datetime DEFAULT NULL,
  `facebook` tinyint NOT NULL,
  `instagram` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `facebook_post_id` varchar(255) DEFAULT NULL,
  `instagram_post_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `posted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `image_tags`
--

DROP TABLE IF EXISTS `image_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `image_tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_id` int unsigned NOT NULL,
  `tag_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) DEFAULT NULL,
  `brand` int unsigned DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `publish_date` timestamp NULL DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `lifestyle` int NOT NULL DEFAULT '0',
  `approved_user` int unsigned DEFAULT NULL,
  `approved_date` timestamp NULL DEFAULT NULL,
  `is_scheduled` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `posted` int NOT NULL DEFAULT '0',
  `product_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `influencer_keywords`
--

DROP TABLE IF EXISTS `influencer_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `influencer_keywords` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instagram_account_id` int DEFAULT NULL,
  `wait_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_of_requets` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `influencers`
--

DROP TABLE IF EXISTS `influencers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `influencers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `brand_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blogger` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_post` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `second_post` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deals` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `list_first_post` text COLLATE utf8mb4_unicode_ci,
  `list_second_post` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `influencers_d_ms`
--

DROP TABLE IF EXISTS `influencers_d_ms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `influencers_d_ms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `influencer_id` int NOT NULL,
  `message_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `influencers_history`
--

DROP TABLE IF EXISTS `influencers_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `influencers_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `influencers_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `insta_messages`
--

DROP TABLE IF EXISTS `insta_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `insta_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `number` int DEFAULT NULL,
  `message` longtext NOT NULL,
  `lead_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `approved` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '0',
  `media_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instagram_auto_comments`
--

DROP TABLE IF EXISTS `instagram_auto_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instagram_auto_comments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `use_count` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `options` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instagram_automated_messages`
--

DROP TABLE IF EXISTS `instagram_automated_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instagram_automated_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `sender_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `receiver_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hashtag_posts',
  `message` text COLLATE utf8mb4_unicode_ci,
  `attachments` text COLLATE utf8mb4_unicode_ci,
  `status` int NOT NULL DEFAULT '0',
  `reusable` int NOT NULL DEFAULT '0',
  `use_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `account_id` int DEFAULT NULL,
  `target_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instagram_bulk_messages`
--

DROP TABLE IF EXISTS `instagram_bulk_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instagram_bulk_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int NOT NULL,
  `receipts` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` tinyint NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instagram_comment_queues`
--

DROP TABLE IF EXISTS `instagram_comment_queues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instagram_comment_queues` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `message` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_id` int NOT NULL,
  `account_id` int NOT NULL,
  `is_send` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instagram_configs`
--

DROP TABLE IF EXISTS `instagram_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instagram_configs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_customer_support` int NOT NULL DEFAULT '0',
  `frequency` int DEFAULT NULL,
  `last_online` datetime DEFAULT NULL,
  `is_connected` int NOT NULL DEFAULT '0',
  `send_start` int NOT NULL,
  `send_end` int NOT NULL,
  `instance_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_default` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `device_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `simcard_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `simcard_owner` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sim_card_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recharge_date` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instagram_direct_messages`
--

DROP TABLE IF EXISTS `instagram_direct_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instagram_direct_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `instagram_thread_id` int NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_type` int NOT NULL,
  `is_send` int NOT NULL DEFAULT '0',
  `sender_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receiver_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instagram_direct_messages_history`
--

DROP TABLE IF EXISTS `instagram_direct_messages_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instagram_direct_messages_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instagram_keywords`
--

DROP TABLE IF EXISTS `instagram_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instagram_keywords` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instagram_logs`
--

DROP TABLE IF EXISTS `instagram_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instagram_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int NOT NULL,
  `log_title` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instagram_post_logs`
--

DROP TABLE IF EXISTS `instagram_post_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instagram_post_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int DEFAULT NULL,
  `log_title` longtext COLLATE utf8mb4_unicode_ci,
  `log_description` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instagram_posts`
--

DROP TABLE IF EXISTS `instagram_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instagram_posts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `hashtag_id` int DEFAULT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` int NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `posted_at` datetime NOT NULL,
  `source` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hashtag',
  `comments_count` int DEFAULT NULL,
  `likes` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=197 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instagram_posts_comments`
--

DROP TABLE IF EXISTS `instagram_posts_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instagram_posts_comments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `instagram_post_id` int NOT NULL,
  `comment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_pic_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `posted_at` datetime NOT NULL,
  `metadata` text COLLATE utf8mb4_unicode_ci,
  `people_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `priority` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19813 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instagram_threads`
--

DROP TABLE IF EXISTS `instagram_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instagram_threads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int DEFAULT NULL,
  `thread_id` varchar(191) DEFAULT NULL,
  `thread_v2_id` varchar(255) DEFAULT NULL,
  `instagram_user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cold_lead_id` int DEFAULT NULL,
  `account_id` int DEFAULT NULL,
  `last_message_at` datetime DEFAULT NULL,
  `last_message` text,
  `scrap_influencer_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instagram_users_lists`
--

DROP TABLE IF EXISTS `instagram_users_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instagram_users_lists` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `fullname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int NOT NULL,
  `location_id` int NOT NULL,
  `because_of` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `following` int DEFAULT NULL,
  `followers` int DEFAULT NULL,
  `posts` int DEFAULT NULL,
  `is_processed` int NOT NULL DEFAULT '0',
  `is_manual` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instruction_categories`
--

DROP TABLE IF EXISTS `instruction_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instruction_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instruction_times`
--

DROP TABLE IF EXISTS `instruction_times`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instruction_times` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `instructions_id` int NOT NULL,
  `total_minutes` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instructions`
--

DROP TABLE IF EXISTS `instructions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instructions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL DEFAULT '1',
  `instruction` longtext NOT NULL,
  `customer_id` int DEFAULT NULL,
  `product_id` int NOT NULL DEFAULT '0',
  `order_id` int NOT NULL DEFAULT '0',
  `assigned_from` int unsigned NOT NULL,
  `assigned_to` int unsigned NOT NULL,
  `pending` int unsigned NOT NULL DEFAULT '0',
  `is_priority` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `verified` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `skipped_count` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `instructions_customer_id_index` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventory_histories`
--

DROP TABLE IF EXISTS `inventory_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `total_product` int DEFAULT NULL,
  `updated_product` int DEFAULT NULL,
  `in_stock` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventory_status_histories`
--

DROP TABLE IF EXISTS `inventory_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_status_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `in_stock` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `product_id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `prev_in_stock` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `product_id_2` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7289 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventory_statuses`
--

DROP TABLE IF EXISTS `inventory_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `issues`
--

DROP TABLE IF EXISTS `issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `issues` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT '0',
  `issue` longtext NOT NULL,
  `priority` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `module` varchar(191) NOT NULL,
  `responsible_user_id` int DEFAULT NULL,
  `resolved_at` date DEFAULT NULL,
  `is_resolved` tinyint(1) NOT NULL DEFAULT '0',
  `submitted_by` int DEFAULT NULL,
  `cost` decimal(8,2) NOT NULL DEFAULT '0.00',
  `subject` text,
  `estimate_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2021 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `joint_permissions`
--

DROP TABLE IF EXISTS `joint_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `joint_permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `entity_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int NOT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `has_permission` tinyint(1) NOT NULL DEFAULT '0',
  `has_permission_own` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `joint_permissions_entity_id_entity_type_index` (`entity_id`,`entity_type`),
  KEY `joint_permissions_role_id_index` (`role_id`),
  KEY `joint_permissions_action_index` (`action`),
  KEY `joint_permissions_has_permission_index` (`has_permission`),
  KEY `joint_permissions_has_permission_own_index` (`has_permission_own`),
  KEY `joint_permissions_created_by_index` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=1401 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keyword_auto_genrated_message_logs`
--

DROP TABLE IF EXISTS `keyword_auto_genrated_message_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keyword_auto_genrated_message_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keyword_match` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_sent_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `keyword_auto_genrated_message_logs_model_index` (`model`),
  KEY `keyword_auto_genrated_message_logs_model_id_index` (`model_id`),
  KEY `keyword_auto_genrated_message_logs_message_sent_id_index` (`message_sent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keyword_instructions`
--

DROP TABLE IF EXISTS `keyword_instructions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keyword_instructions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `keywords` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `instruction_category_id` int NOT NULL,
  `remark` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keyword_reports`
--

DROP TABLE IF EXISTS `keyword_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keyword_reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `tool_id` int NOT NULL,
  `database` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword_overview_all_database` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword_overview_one_database` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_keyword_overview_one_database` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `organic_results` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_results` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `related_keyword` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword_ads_history` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `broad_match_keywords` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `phrase_questions` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword_difficulty` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keyword_tags`
--

DROP TABLE IF EXISTS `keyword_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keyword_tags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `keyword_id` int NOT NULL,
  `tag_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keyword_to_categories`
--

DROP TABLE IF EXISTS `keyword_to_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keyword_to_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `keyword_value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keywordassigns`
--

DROP TABLE IF EXISTS `keywordassigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keywordassigns` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_category` int NOT NULL,
  `task_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `assign_to` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keywords`
--

DROP TABLE IF EXISTS `keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keywords` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `landing_page_products`
--

DROP TABLE IF EXISTS `landing_page_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_page_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` text COLLATE utf8mb4_unicode_ci,
  `shopify_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_website_id` int unsigned DEFAULT NULL,
  `stock_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `status` int NOT NULL,
  `landing_page_status_id` int unsigned NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `landing_page_statuses`
--

DROP TABLE IF EXISTS `landing_page_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_page_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `languages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_view` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `active` int NOT NULL,
  `status` int DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `laravel_github_logs`
--

DROP TABLE IF EXISTS `laravel_github_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laravel_github_logs` (
  `log_time` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_file_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commit_time` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stacktrace` text COLLATE utf8mb4_unicode_ci,
  `commit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `laravel_logs`
--

DROP TABLE IF EXISTS `laravel_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laravel_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `log` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `controller_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lawyer_specialities`
--

DROP TABLE IF EXISTS `lawyer_specialities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lawyer_specialities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lawyers`
--

DROP TABLE IF EXISTS `lawyers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lawyers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referenced_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `speciality_id` int unsigned DEFAULT NULL,
  `rating` tinyint DEFAULT NULL,
  `whatsapp_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `other` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lawyers_speciality_id_foreign` (`speciality_id`),
  CONSTRAINT `lawyers_speciality_id_foreign` FOREIGN KEY (`speciality_id`) REFERENCES `lawyer_specialities` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_broadcasts_lead`
--

DROP TABLE IF EXISTS `lead_broadcasts_lead`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_broadcasts_lead` (
  `lead_broadcast_id` int NOT NULL,
  `lead_id` int NOT NULL,
  `status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_hubstaff_detail`
--

DROP TABLE IF EXISTS `lead_hubstaff_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_hubstaff_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hubstaff_task_id` bigint unsigned NOT NULL,
  `task_id` bigint unsigned NOT NULL,
  `team_lead_id` bigint unsigned NOT NULL,
  `current` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_lists`
--

DROP TABLE IF EXISTS `lead_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_lists` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `erp_lead_id` int NOT NULL,
  `list_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `client_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contactno` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solophone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instahandler` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` int NOT NULL,
  `status` int NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `assigned_user` int NOT NULL,
  `source` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leadsourcetxt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selected_product` text COLLATE utf8mb4_unicode_ci,
  `size` text COLLATE utf8mb4_unicode_ci,
  `brand` int DEFAULT NULL,
  `multi_brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `multi_category` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `userid` int NOT NULL,
  `assign_status` int DEFAULT NULL,
  `remark` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `whatsapp_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7712 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `learning_duedate_history`
--

DROP TABLE IF EXISTS `learning_duedate_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `learning_duedate_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `learning_id` int NOT NULL,
  `old_duedate` date DEFAULT NULL,
  `new_duedate` date DEFAULT NULL,
  `update_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `learning_modules`
--

DROP TABLE IF EXISTS `learning_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `learning_modules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `title` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_approved` int DEFAULT NULL,
  `is_active` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `learning_status_history`
--

DROP TABLE IF EXISTS `learning_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `learning_status_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `learning_id` int NOT NULL,
  `old_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `update_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `learnings`
--

DROP TABLE IF EXISTS `learnings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `learnings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` int DEFAULT NULL,
  `assign_from` int NOT NULL,
  `assign_to` int NOT NULL,
  `status` int DEFAULT NULL,
  `assign_status` int DEFAULT NULL,
  `is_statutory` int NOT NULL,
  `is_private` tinyint NOT NULL,
  `is_watched` tinyint NOT NULL,
  `is_flagged` tinyint NOT NULL,
  `task_details` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_subject` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `completion_date` datetime NOT NULL,
  `remark` longtext COLLATE utf8mb4_unicode_ci,
  `actual_start_date` datetime NOT NULL,
  `is_completed` datetime NOT NULL,
  `general_category_id` int DEFAULT NULL,
  `is_verified` datetime DEFAULT NULL,
  `sending_time` datetime DEFAULT NULL,
  `time_slot` longtext COLLATE utf8mb4_unicode_ci,
  `planned_at` int DEFAULT NULL,
  `pending_for` int NOT NULL,
  `recurring_type` longtext COLLATE utf8mb4_unicode_ci,
  `statutory_id` int DEFAULT NULL,
  `model_type` longtext COLLATE utf8mb4_unicode_ci,
  `model_id` int DEFAULT NULL,
  `deleted_at` datetime NOT NULL,
  `approximate` int NOT NULL,
  `hubstaff_task_id` int NOT NULL,
  `cost` decimal(8,2) DEFAULT NULL,
  `is_milestone` tinyint NOT NULL,
  `no_of_milestone` int DEFAULT NULL,
  `milestone_completed` int DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `master_user_id` int DEFAULT NULL,
  `lead_hubstaff_task_id` int DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `site_developement_id` int DEFAULT NULL,
  `priority_no` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `learning_user` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_vendor` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_module` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_submodule` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_assignment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_duedate` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `links_to_posts`
--

DROP TABLE IF EXISTS `links_to_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `links_to_posts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `date_scrapped` date DEFAULT NULL,
  `date_posted` datetime DEFAULT NULL,
  `date_next_post` datetime DEFAULT NULL,
  `article` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `list_contacts`
--

DROP TABLE IF EXISTS `list_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `list_contacts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `list_id` int unsigned NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `listing_histories`
--

DROP TABLE IF EXISTS `listing_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `listing_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'update',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `listing_payments`
--

DROP TABLE IF EXISTS `listing_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `listing_payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `paid_at` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_chat_users`
--

DROP TABLE IF EXISTS `live_chat_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `live_chat_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `website_ids` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `live_chat_users_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `livechatinc_settings`
--

DROP TABLE IF EXISTS `livechatinc_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livechatinc_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_excel_import_versions`
--

DROP TABLE IF EXISTS `log_excel_import_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_excel_import_versions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_version` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_excel_imports_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_excel_imports`
--

DROP TABLE IF EXISTS `log_excel_imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_excel_imports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `md5` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number_of_products` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_products_created` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `supplier_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number_products_updated` int DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_google_cses`
--

DROP TABLE IF EXISTS `log_google_cses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_google_cses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `image_url` text COLLATE utf8mb4_unicode_ci,
  `keyword` text COLLATE utf8mb4_unicode_ci,
  `response` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_google_vision`
--

DROP TABLE IF EXISTS `log_google_vision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_google_vision` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `image_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_google_vision_reference`
--

DROP TABLE IF EXISTS `log_google_vision_reference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_google_vision_reference` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_reference` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `composite_reference` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender_reference` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnt` int unsigned NOT NULL DEFAULT '1',
  `ignore` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `log_google_vision_reference_type_value_unique` (`type`,`value`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_keywords`
--

DROP TABLE IF EXISTS `log_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_keywords` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_list_magentos`
--

DROP TABLE IF EXISTS `log_list_magentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_list_magentos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `queue_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size_chart_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_attributes` text COLLATE utf8mb4_unicode_ci,
  `message` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `magento_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  `sync_status` enum('success','error','waiting','started_push','size_chart_needed','image_not_found','translation_not_found') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `languages` text COLLATE utf8mb4_unicode_ci COMMENT 'Language Id (JSON)',
  `user_id` int DEFAULT NULL,
  `total_request_assigned` int DEFAULT '0',
  `tried` int DEFAULT '0',
  `job_start_time` timestamp NULL DEFAULT NULL,
  `job_end_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_magento`
--

DROP TABLE IF EXISTS `log_magento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_magento` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date_time` datetime NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `response` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_remark`
--

DROP TABLE IF EXISTS `log_remark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_remark` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `remark` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logid` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_requests`
--

DROP TABLE IF EXISTS `log_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `request` json NOT NULL,
  `response` json NOT NULL,
  `url` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_code` int NOT NULL,
  `time_taken` int DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10952 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_scraper`
--

DROP TABLE IF EXISTS `log_scraper`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_scraper` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_sku` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `properties` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `images` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `size_system` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discounted_price` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_sale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `validated` tinyint NOT NULL,
  `validation_result` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `raw_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=391 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_scraper_old`
--

DROP TABLE IF EXISTS `log_scraper_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_scraper_old` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_sku` text COLLATE utf8mb4_unicode_ci,
  `brand` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `properties` text COLLATE utf8mb4_unicode_ci,
  `images` text COLLATE utf8mb4_unicode_ci,
  `size_system` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discounted_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_sale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `validated` tinyint NOT NULL,
  `validation_result` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `raw_data` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_scraper_website_index` (`website`)
) ENGINE=MyISAM AUTO_INCREMENT=883764 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_scraper_vs_ai`
--

DROP TABLE IF EXISTS `log_scraper_vs_ai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_scraper_vs_ai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `ai_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_input` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `result_scraper` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `result_ai` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_scraper_vs_ai_product_id_foreign` (`product_id`),
  CONSTRAINT `log_scraper_vs_ai_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_status`
--

DROP TABLE IF EXISTS `log_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_task`
--

DROP TABLE IF EXISTS `log_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_task` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logId` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_tineye`
--

DROP TABLE IF EXISTS `log_tineye`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_tineye` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `image_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `md5` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `magento_cron_datas`
--

DROP TABLE IF EXISTS `magento_cron_datas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `magento_cron_datas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `cron_id` int NOT NULL,
  `job_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cron_message` text COLLATE utf8mb4_unicode_ci,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cronstatus` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cron_created_at` timestamp NULL DEFAULT NULL,
  `cron_scheduled_at` timestamp NULL DEFAULT NULL,
  `cron_executed_at` timestamp NULL DEFAULT NULL,
  `cron_finished_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `magento_log_history`
--

DROP TABLE IF EXISTS `magento_log_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `magento_log_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `log_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `old_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `magento_setting_logs`
--

DROP TABLE IF EXISTS `magento_setting_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `magento_setting_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `magento_setting_name_logs`
--

DROP TABLE IF EXISTS `magento_setting_name_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `magento_setting_name_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `magento_settings_id` int DEFAULT NULL,
  `old_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `magento_setting_push_logs`
--

DROP TABLE IF EXISTS `magento_setting_push_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `magento_setting_push_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `command` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `setting_id` int DEFAULT NULL,
  `command_output` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `magento_settings`
--

DROP TABLE IF EXISTS `magento_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `magento_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `scope` enum('default','websites','stores') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  `scope_id` bigint unsigned NOT NULL,
  `store_website_id` int DEFAULT NULL,
  `website_store_id` int DEFAULT NULL,
  `website_store_view_id` int DEFAULT NULL,
  `name` varchar(192) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(192) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `magento_settings_scope_index` (`scope`),
  KEY `magento_settings_scope_id_index` (`scope_id`),
  KEY `magento_settings_name_index` (`name`),
  KEY `magento_settings_path_index` (`path`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `magento_user_password_history`
--

DROP TABLE IF EXISTS `magento_user_password_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `magento_user_password_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_userid` int NOT NULL,
  `old_password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailing_remarks`
--

DROP TABLE IF EXISTS `mailing_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailing_remarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailing_template_files`
--

DROP TABLE IF EXISTS `mailing_template_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailing_template_files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `mailing_id` int NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailing_template_files_histories`
--

DROP TABLE IF EXISTS `mailing_template_files_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailing_template_files_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `mailing_id` int NOT NULL,
  `old_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailinglist_emails`
--

DROP TABLE IF EXISTS `mailinglist_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailinglist_emails` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `mailinglist_id` int NOT NULL,
  `template_id` int NOT NULL,
  `html` text COLLATE utf8mb4_unicode_ci,
  `scheduled_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `progress` int NOT NULL DEFAULT '0',
  `total_emails_scheduled` int NOT NULL DEFAULT '0',
  `total_emails_sent` int NOT NULL DEFAULT '0',
  `total_emails_undelivered` int NOT NULL DEFAULT '0',
  `api_template_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailinglist_template_categories`
--

DROP TABLE IF EXISTS `mailinglist_template_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailinglist_template_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mailinglist_template_categories_title_unique` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailinglist_templates`
--

DROP TABLE IF EXISTS `mailinglist_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailinglist_templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_class` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_tpl` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salutation` text COLLATE utf8mb4_unicode_ci,
  `introduction` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_count` int unsigned NOT NULL,
  `text_count` int unsigned NOT NULL,
  `example_image` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `store_website_id` int unsigned DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `static_template` text COLLATE utf8mb4_unicode_ci,
  `auto_send` tinyint(1) NOT NULL DEFAULT '0',
  `duration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration_in` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailinglists`
--

DROP TABLE IF EXISTS `mailinglists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailinglists` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_id` int NOT NULL,
  `remote_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_master` tinyint(1) NOT NULL DEFAULT '0',
  `is_spam` tinyint(1) NOT NULL DEFAULT '0',
  `emails_sent` int NOT NULL DEFAULT '0',
  `spam_date` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marketing_message_customers`
--

DROP TABLE IF EXISTS `marketing_message_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marketing_message_customers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `marketing_message_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marketing_message_types`
--

DROP TABLE IF EXISTS `marketing_message_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marketing_message_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marketing_messages`
--

DROP TABLE IF EXISTS `marketing_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marketing_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_group_id` int NOT NULL,
  `scheduled_at` datetime NOT NULL,
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marketing_platforms`
--

DROP TABLE IF EXISTS `marketing_platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marketing_platforms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `disk` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `directory` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aggregate_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` int unsigned NOT NULL,
  `bits` varchar(68) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_processed` tinyint NOT NULL DEFAULT '0' COMMENT '[0=>pending,1=>processed,2=>file-not-exists]',
  PRIMARY KEY (`id`),
  KEY `media_disk_directory_index` (`disk`,`directory`),
  KEY `media_aggregate_type_index` (`aggregate_type`),
  KEY `media_is_processed_index` (`is_processed`)
) ENGINE=InnoDB AUTO_INCREMENT=1538544 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mediables`
--

DROP TABLE IF EXISTS `mediables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mediables` (
  `media_id` int unsigned NOT NULL,
  `mediable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mediable_id` int unsigned NOT NULL,
  `tag` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int unsigned NOT NULL,
  PRIMARY KEY (`media_id`,`mediable_type`,`mediable_id`,`tag`),
  KEY `mediables_mediable_id_mediable_type_index` (`mediable_id`,`mediable_type`),
  KEY `mediables_tag_index` (`tag`),
  KEY `mediables_order_index` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meeting_and_other_times`
--

DROP TABLE IF EXISTS `meeting_and_other_times`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_and_other_times` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` int NOT NULL,
  `user_id` int NOT NULL,
  `time` int NOT NULL,
  `old_time` int NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approve` tinyint(1) NOT NULL DEFAULT '0',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meeting_and_other_times_model_id_model_user_id_approve_index` (`model_id`,`model`,`user_id`,`approve`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `memory_usage`
--

DROP TABLE IF EXISTS `memory_usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `memory_usage` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `total` int DEFAULT NULL,
  `used` int DEFAULT NULL,
  `free` int DEFAULT NULL,
  `buff_cache` int DEFAULT NULL,
  `available` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=114019 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu_pages`
--

DROP TABLE IF EXISTS `menu_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_pages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` tinyint(1) NOT NULL,
  `have_child` tinyint(1) NOT NULL,
  `department` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message_queue_history`
--

DROP TABLE IF EXISTS `message_queue_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_queue_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `number` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `counter` int NOT NULL DEFAULT '0',
  `type` enum('individual','group') COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message_queues`
--

DROP TABLE IF EXISTS `message_queues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_queues` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `customer_id` int unsigned DEFAULT NULL,
  `chat_message_id` int unsigned DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '0',
  `group_id` int unsigned NOT NULL,
  `sending_time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `message_queues_chat_message_id_foreign` (`chat_message_id`),
  CONSTRAINT `message_queues_chat_message_id_foreign` FOREIGN KEY (`chat_message_id`) REFERENCES `chat_messages` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=51229 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `userid` int unsigned NOT NULL,
  `customer_id` int unsigned DEFAULT NULL,
  `assigned_to` int unsigned NOT NULL DEFAULT '0',
  `moduleid` int DEFAULT NULL,
  `moduletype` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_user_id_foreign` (`userid`),
  CONSTRAINT `messages_user_id_foreign` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messaging_group_customers`
--

DROP TABLE IF EXISTS `messaging_group_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messaging_group_customers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `message_group_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messaging_groups`
--

DROP TABLE IF EXISTS `messaging_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messaging_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_website_id` int NOT NULL,
  `service_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messsage_applications`
--

DROP TABLE IF EXISTS `messsage_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messsage_applications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1883 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `missing_brands`
--

DROP TABLE IF EXISTS `missing_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `missing_brands` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` int unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_type_model_id_index` (`model_type`,`model_id`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` int unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_type_model_id_index` (`model_type`,`model_id`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `monetary_account_histories`
--

DROP TABLE IF EXISTS `monetary_account_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `monetary_account_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `note` text COLLATE utf8mb4_unicode_ci,
  `monetary_account_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `monetary_account_histories_model_id_index` (`model_id`),
  KEY `monetary_account_histories_model_type_index` (`model_type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `monetary_accounts`
--

DROP TABLE IF EXISTS `monetary_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `monetary_accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `currency` int NOT NULL DEFAULT '1',
  `amount` decimal(13,4) DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `short_note` mediumtext COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `other` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `negative_reviews`
--

DROP TABLE IF EXISTS `negative_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `negative_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `website` varchar(191) NOT NULL,
  `brand` varchar(191) NOT NULL,
  `review_url` varchar(400) NOT NULL,
  `username` varchar(191) NOT NULL,
  `title` varchar(200) NOT NULL,
  `body` mediumtext NOT NULL,
  `stars` int NOT NULL,
  `reply` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newsletter_products`
--

DROP TABLE IF EXISTS `newsletter_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `newsletter_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newsletters`
--

DROP TABLE IF EXISTS `newsletters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletters` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_website_id` int DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `sent_on` timestamp NULL DEFAULT NULL,
  `mail_list_id` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `newsletters_store_website_id_foreign` (`store_website_id`),
  CONSTRAINT `newsletters_store_website_id_foreign` FOREIGN KEY (`store_website_id`) REFERENCES `store_websites` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notification_queues`
--

DROP TABLE IF EXISTS `notification_queues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_queues` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `user_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `task_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` int DEFAULT NULL,
  `time_to_add` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `message_id` int DEFAULT NULL,
  `reminder` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `task_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_id` int DEFAULT NULL,
  `sent_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isread` tinyint(1) NOT NULL DEFAULT '0',
  `reminder` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_access_tokens`
--

DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `client_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_auth_codes`
--

DROP TABLE IF EXISTS `oauth_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `client_id` int NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_clients`
--

DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_clients` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_personal_access_clients`
--

DROP TABLE IF EXISTS `oauth_personal_access_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_personal_access_clients` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_personal_access_clients_client_id_index` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_refresh_tokens`
--

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `old`
--

DROP TABLE IF EXISTS `old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `old` (
  `serial_no` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int NOT NULL,
  `commitment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `communication` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','disputed','settled','paid','closed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_blocked` int NOT NULL DEFAULT '0',
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gst` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_iban` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_swift` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int NOT NULL,
  `pending_payment` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_payable` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`serial_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `old_categories`
--

DROP TABLE IF EXISTS `old_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `old_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `old_incomings`
--

DROP TABLE IF EXISTS `old_incomings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `old_incomings` (
  `serial_no` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int NOT NULL,
  `commitment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `communication` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','disputed','settled','paid','closed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`serial_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `old_payments`
--

DROP TABLE IF EXISTS `old_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `old_payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `old_id` int NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_date` date NOT NULL,
  `paid_date` date NOT NULL,
  `pending_amount` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_amount` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_provided` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `work_hour` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `other` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `old_remarks`
--

DROP TABLE IF EXISTS `old_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `old_remarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `old_id` int NOT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remark` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_customer_address`
--

DROP TABLE IF EXISTS `order_customer_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_customer_address` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `address_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` int DEFAULT NULL,
  `firstname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `postcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` text COLLATE utf8mb4_unicode_ci,
  `telephone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_misscall_send_message_histories`
--

DROP TABLE IF EXISTS `order_misscall_send_message_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_misscall_send_message_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_products`
--

DROP TABLE IF EXISTS `order_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` int DEFAULT NULL,
  `product_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EUR',
  `eur_price` double(8,2) NOT NULL DEFAULT '0.00',
  `order_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` int unsigned DEFAULT '1',
  `purchase_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipment_date` datetime DEFAULT NULL,
  `reschedule_count` int unsigned NOT NULL DEFAULT '0',
  `purchase_id` int unsigned DEFAULT NULL,
  `batch_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `supplier_discount_info_id` int DEFAULT NULL,
  `inventory_status_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=244 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_reports`
--

DROP TABLE IF EXISTS `order_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `customer_id` int unsigned DEFAULT NULL,
  `completion_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_status_histories`
--

DROP TABLE IF EXISTS `order_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_status_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `old_status` int DEFAULT NULL,
  `new_status` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_statuses`
--

DROP TABLE IF EXISTS `order_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_statuses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` varchar(255) NOT NULL,
  `magento_status` varchar(191) DEFAULT NULL,
  `message_text_tpl` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `order_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `awb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_detail` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clothing_size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shoe_size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `advance_detail` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `advance_date` date DEFAULT NULL,
  `balance_amount` int DEFAULT NULL,
  `sales_person` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_phone_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_status_id` int DEFAULT NULL,
  `date_of_delivery` date DEFAULT NULL,
  `estimated_delivery_date` date DEFAULT NULL,
  `note_if_any` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `received_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assign_status` int DEFAULT NULL,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `refund_answer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refund_answer_date` datetime DEFAULT NULL,
  `auto_messaged` int NOT NULL DEFAULT '0',
  `auto_messaged_date` timestamp NULL DEFAULT NULL,
  `auto_emailed` tinyint NOT NULL DEFAULT '0',
  `auto_emailed_date` timestamp NULL DEFAULT NULL,
  `remark` text COLLATE utf8mb4_unicode_ci,
  `is_priority` tinyint(1) NOT NULL DEFAULT '0',
  `coupon_id` bigint unsigned DEFAULT NULL,
  `monetary_account_id` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `whatsapp_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_id` int DEFAULT NULL,
  `store_currency_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_id` int DEFAULT NULL,
  `store_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_order_id_index` (`order_id`),
  KEY `orders_customer_id_index` (`customer_id`),
  KEY `orders_order_status_id_index` (`order_status_id`),
  KEY `orders_user_id_index` (`user_id`),
  KEY `orders_coupon_id_index` (`coupon_id`),
  KEY `orders_invoice_id_index` (`invoice_id`),
  KEY `orders_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2177 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `out_of_stock_subscribes`
--

DROP TABLE IF EXISTS `out_of_stock_subscribes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `out_of_stock_subscribes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `website_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `page_instructions`
--

DROP TABLE IF EXISTS `page_instructions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_instructions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `page` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instruction` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `page_instructions_page_index` (`page`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `page_notes`
--

DROP TABLE IF EXISTS `page_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_notes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `page_notes_category_id_foreign` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `page_notes_categories`
--

DROP TABLE IF EXISTS `page_notes_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_notes_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_notes_categories_name_index` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `page_revisions`
--

DROP TABLE IF EXISTS `page_revisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_revisions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `html` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `book_slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'version',
  `markdown` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revision_number` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_revisions_page_id_index` (`page_id`),
  KEY `page_revisions_slug_index` (`slug`),
  KEY `page_revisions_book_slug_index` (`book_slug`),
  KEY `page_revisions_type_index` (`type`),
  KEY `page_revisions_revision_number_index` (`revision_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `page_screenshots`
--

DROP TABLE IF EXISTS `page_screenshots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_screenshots` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_link` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `book_id` int NOT NULL,
  `chapter_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `html` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int NOT NULL,
  `updated_by` int NOT NULL,
  `restricted` tinyint(1) NOT NULL DEFAULT '0',
  `draft` tinyint(1) NOT NULL DEFAULT '0',
  `markdown` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `revision_count` int NOT NULL,
  `template` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pages_book_id_index` (`book_id`),
  KEY `pages_chapter_id_index` (`chapter_id`),
  KEY `pages_slug_index` (`slug`),
  KEY `pages_priority_index` (`priority`),
  KEY `pages_created_by_index` (`created_by`),
  KEY `pages_updated_by_index` (`updated_by`),
  KEY `pages_restricted_index` (`restricted`),
  KEY `pages_draft_index` (`draft`),
  KEY `pages_template_index` (`template`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_histories`
--

DROP TABLE IF EXISTS `password_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `password_id` int NOT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `registered_with` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `passwords`
--

DROP TABLE IF EXISTS `passwords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `passwords` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `registered_with` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_mail_records`
--

DROP TABLE IF EXISTS `payment_mail_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_mail_records` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_date` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` text COLLATE utf8mb4_unicode_ci,
  `total_amount` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount_paid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_balance` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` text COLLATE utf8mb4_unicode_ci,
  `command_execution` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_receipts`
--

DROP TABLE IF EXISTS `payment_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_receipts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `worked_minutes` int DEFAULT NULL,
  `payment` decimal(8,2) DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_id` int DEFAULT NULL,
  `developer_task_id` int DEFAULT NULL,
  `rate_estimated` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remarks` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `billing_start_date` date DEFAULT NULL,
  `billing_end_date` date DEFAULT NULL,
  `billing_due_date` datetime DEFAULT NULL,
  `monetary_account_id` int DEFAULT NULL,
  `by_command` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_receipts_status_index` (`status`),
  KEY `payment_receipts_date_index` (`date`),
  KEY `payment_receipts_user_id_index` (`user_id`),
  KEY `payment_receipts_billing_due_date_index` (`billing_due_date`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `payment_method_id` int unsigned NOT NULL,
  `note` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` double(8,2) NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `paid_upto` date DEFAULT NULL,
  `payment_receipt_id` int DEFAULT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_payment_receipt_id_index` (`payment_receipt_id`),
  KEY `payments_amount_index` (`amount`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `people_names`
--

DROP TABLE IF EXISTS `people_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `people_names` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `race` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permission_request`
--

DROP TABLE IF EXISTS `permission_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_request` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `permission_id` int DEFAULT NULL,
  `request_date` datetime DEFAULT NULL,
  `permission_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_role` (
  `permission_id` int unsigned NOT NULL,
  `role_id` int unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_permission_id_index` (`permission_id`),
  KEY `permission_role_role_id_index` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permission_user`
--

DROP TABLE IF EXISTS `permission_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_user` (
  `user_id` int unsigned NOT NULL,
  `permission_id` int unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`permission_id`),
  KEY `permission_user_user_id_index` (`user_id`),
  KEY `permission_user_permission_id_index` (`permission_id`),
  CONSTRAINT `permission_user_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `permission_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `route` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `permissions_route_index` (`route`)
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `picture_colors`
--

DROP TABLE IF EXISTS `picture_colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `picture_colors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `image_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `picked_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `picked_color` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pinterest_boards`
--

DROP TABLE IF EXISTS `pinterest_boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pinterest_boards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pinterest_users_id` bigint unsigned NOT NULL,
  `board_id` bigint NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pinterest_boards_pinterest_users_id_foreign` (`pinterest_users_id`),
  CONSTRAINT `pinterest_boards_pinterest_users_id_foreign` FOREIGN KEY (`pinterest_users_id`) REFERENCES `pinterest_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pinterest_users`
--

DROP TABLE IF EXISTS `pinterest_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pinterest_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pinterest_id` bigint NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plan_basis_status`
--

DROP TABLE IF EXISTS `plan_basis_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plan_basis_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plan_categories`
--

DROP TABLE IF EXISTS `plan_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plan_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plan_solutions`
--

DROP TABLE IF EXISTS `plan_solutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plan_solutions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `solution` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plan_types`
--

DROP TABLE IF EXISTS `plan_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plan_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plans` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `budget` int DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `basis` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `implications` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` text COLLATE utf8mb4_unicode_ci,
  `strength` text COLLATE utf8mb4_unicode_ci,
  `weakness` text COLLATE utf8mb4_unicode_ci,
  `opportunity` text COLLATE utf8mb4_unicode_ci,
  `threat` text COLLATE utf8mb4_unicode_ci,
  `category` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `platforms`
--

DROP TABLE IF EXISTS `platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `platforms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int NOT NULL,
  `type` enum('post','album','story') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
  `ig` longtext COLLATE utf8mb4_unicode_ci,
  `caption` text COLLATE utf8mb4_unicode_ci,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status` enum('1','2','3') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `scheduled_at` datetime DEFAULT NULL,
  `posted_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `hashtags` longtext COLLATE utf8mb4_unicode_ci,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pre_accounts`
--

DROP TABLE IF EXISTS `pre_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instagram` int NOT NULL DEFAULT '0',
  `facebook` int NOT NULL DEFAULT '0',
  `pinterest` int NOT NULL DEFAULT '0',
  `twitter` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `price_comparison`
--

DROP TABLE IF EXISTS `price_comparison`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `price_comparison` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `price_comparison_site_id` bigint NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double(10,2) NOT NULL,
  `shipping` double(10,2) NOT NULL,
  `checkout_price` double(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tax` double(10,2) NOT NULL DEFAULT '0.00',
  `converted_price` double(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5046 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `price_comparison_site`
--

DROP TABLE IF EXISTS `price_comparison_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `price_comparison_site` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_cat_shoes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_cat_bags` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_cat_clothing` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_cat_accessories` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_brands` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `price_overrides`
--

DROP TABLE IF EXISTS `price_overrides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `price_overrides` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  `brand_segment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `country_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_group_id` int DEFAULT NULL,
  `type` enum('PERCENTAGE','FIXED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PERCENTAGE',
  `calculated` enum('+','-') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '+',
  `value` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `priorities`
--

DROP TABLE IF EXISTS `priorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `priorities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `private_view_products`
--

DROP TABLE IF EXISTS `private_view_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `private_view_products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `private_view_id` int NOT NULL,
  `product_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `private_views`
--

DROP TABLE IF EXISTS `private_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `private_views` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `assigned_user_id` int unsigned DEFAULT NULL,
  `order_product_id` int unsigned DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `status` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `private_views_assigned_user_id_foreign` (`assigned_user_id`),
  CONSTRAINT `private_views_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_attributes`
--

DROP TABLE IF EXISTS `product_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_attributes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `has_mediables` int DEFAULT NULL,
  `is_barcode_check` int DEFAULT NULL,
  `was_auto_rejected` int DEFAULT '0',
  `is_crop_skipped` int DEFAULT '0',
  `is_auto_processing_failed` int DEFAULT '0',
  `is_being_ordered` tinyint(1) NOT NULL DEFAULT '0',
  `is_listing_rejected_automatically` tinyint DEFAULT '0',
  `is_titlecased` tinyint DEFAULT '0',
  `manual_cropped_at` datetime DEFAULT NULL,
  `manual_cropped_by` int DEFAULT NULL,
  `is_manual_cropped` tinyint(1) NOT NULL DEFAULT '0',
  `manual_crop` tinyint(1) NOT NULL DEFAULT '0',
  `is_order_rejected` tinyint(1) NOT NULL DEFAULT '0',
  `cropped_at` datetime DEFAULT NULL,
  `instruction_completed_at` datetime DEFAULT NULL,
  `is_crop_being_verified` tinyint(1) NOT NULL DEFAULT '0',
  `authorized_by` int DEFAULT NULL,
  `is_authorized` tinyint(1) NOT NULL DEFAULT '0',
  `is_being_cropped` tinyint(1) NOT NULL DEFAULT '0',
  `was_crop_rejected` tinyint(1) NOT NULL DEFAULT '0',
  `import_date` datetime DEFAULT NULL,
  `is_price_different` tinyint(1) NOT NULL DEFAULT '0',
  `last_searcher` int DEFAULT NULL,
  `last_selector` int DEFAULT NULL,
  `last_attributer` int DEFAULT NULL,
  `is_enhanced` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_cancellation_policies`
--

DROP TABLE IF EXISTS `product_cancellation_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_cancellation_policies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci,
  `days_cancelation` int DEFAULT NULL,
  `days_refund` int DEFAULT NULL,
  `percentage` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_category_histories`
--

DROP TABLE IF EXISTS `product_category_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_category_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `old_category_id` int NOT NULL,
  `category_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1950 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_color_histories`
--

DROP TABLE IF EXISTS `product_color_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_color_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `old_color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_discount_excel_files`
--

DROP TABLE IF EXISTS `product_discount_excel_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_discount_excel_files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier_brand_discounts_id` bigint unsigned NOT NULL,
  `excel_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_dispatch`
--

DROP TABLE IF EXISTS `product_dispatch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_dispatch` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `modeof_shipment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_person` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `awb` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eta` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_time` datetime NOT NULL,
  `product_id` int unsigned DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_dispatch_product_id_index` (`product_id`),
  KEY `product_dispatch_created_by_index` (`created_by`),
  CONSTRAINT `product_dispatch_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `product_dispatch_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_location`
--

DROP TABLE IF EXISTS `product_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_location` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_location_history`
--

DROP TABLE IF EXISTS `product_location_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_location_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `location_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `courier_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `courier_details` text COLLATE utf8mb4_unicode_ci,
  `date_time` datetime NOT NULL,
  `product_id` int unsigned DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `instruction_message` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_location_history_product_id_index` (`product_id`),
  KEY `product_location_history_created_by_index` (`created_by`),
  CONSTRAINT `product_location_history_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `product_location_history_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_push_error_logs`
--

DROP TABLE IF EXISTS `product_push_error_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_push_error_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `store_website_id` int DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `request_data` text COLLATE utf8mb4_unicode_ci,
  `response_data` text COLLATE utf8mb4_unicode_ci,
  `response_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_list_magento_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `product_id_2` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_push_information_histories`
--

DROP TABLE IF EXISTS `product_push_information_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_push_information_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `old_sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_quantity` smallint unsigned DEFAULT NULL,
  `quantity` smallint unsigned DEFAULT NULL,
  `old_stock_status` tinyint(1) DEFAULT '0',
  `stock_status` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `store_website_id` int unsigned DEFAULT NULL,
  `is_added_from_csv` tinyint(1) NOT NULL DEFAULT '1',
  `old_is_added_from_csv` tinyint(1) NOT NULL DEFAULT '1',
  `old_is_available` tinyint(1) NOT NULL DEFAULT '1',
  `is_available` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_push_information_histories_product_id_index` (`product_id`),
  KEY `product_push_information_histories_old_sku_index` (`old_sku`),
  KEY `product_push_information_histories_sku_index` (`sku`),
  KEY `product_push_information_histories_old_status_index` (`old_status`),
  KEY `product_push_information_histories_status_index` (`status`),
  KEY `product_push_information_histories_old_quantity_index` (`old_quantity`),
  KEY `product_push_information_histories_quantity_index` (`quantity`),
  KEY `product_push_information_histories_old_stock_status_index` (`old_stock_status`),
  KEY `product_push_information_histories_stock_status_index` (`stock_status`),
  KEY `product_push_information_histories_user_id_index` (`user_id`),
  KEY `product_push_information_histories_store_website_id_index` (`store_website_id`),
  KEY `product_push_information_histories_is_added_from_csv_index` (`is_added_from_csv`),
  KEY `product_push_information_histories_old_is_added_from_csv_index` (`old_is_added_from_csv`),
  KEY `product_push_information_histories_old_is_available_index` (`old_is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_push_information_summeries`
--

DROP TABLE IF EXISTS `product_push_information_summeries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_push_information_summeries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  `product_push_count` smallint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_push_information_summeries_brand_id_index` (`brand_id`),
  KEY `product_push_information_summeries_category_id_index` (`category_id`),
  KEY `product_push_information_summeries_store_website_id_index` (`store_website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_push_informations`
--

DROP TABLE IF EXISTS `product_push_informations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_push_informations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` smallint unsigned NOT NULL,
  `stock_status` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `store_website_id` int unsigned DEFAULT NULL,
  `is_added_from_csv` tinyint(1) NOT NULL DEFAULT '1',
  `real_product_id` int DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `product_push_informations_product_id_index` (`product_id`),
  KEY `product_push_informations_sku_index` (`sku`),
  KEY `product_push_informations_status_index` (`status`),
  KEY `product_push_informations_quantity_index` (`quantity`),
  KEY `product_push_informations_stock_status_index` (`stock_status`),
  KEY `product_push_informations_store_website_id_index` (`store_website_id`),
  KEY `product_push_informations_is_added_from_csv_index` (`is_added_from_csv`),
  KEY `product_push_informations_real_product_id_index` (`real_product_id`),
  KEY `product_push_informations_is_available_index` (`is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_quicksell_groups`
--

DROP TABLE IF EXISTS `product_quicksell_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_quicksell_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `quicksell_group_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_quickshell_groups`
--

DROP TABLE IF EXISTS `product_quickshell_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_quickshell_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `quicksell_group_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_references`
--

DROP TABLE IF EXISTS `product_references`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_references` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_references_product_id_foreign` (`product_id`),
  CONSTRAINT `product_references_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=1331 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_sizes`
--

DROP TABLE IF EXISTS `product_sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_sizes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `size` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_status`
--

DROP TABLE IF EXISTS `product_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_status` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=117895 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_status_histories`
--

DROP TABLE IF EXISTS `product_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_status_histories` (
  `product_id` int unsigned NOT NULL,
  `old_status` int NOT NULL,
  `new_status` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `product_status_histories_product_id_foreign` (`product_id`),
  CONSTRAINT `product_status_histories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_stocks`
--

DROP TABLE IF EXISTS `product_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_stocks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `size` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_stocks_product_id_index` (`product_id`),
  KEY `product_stocks_supplier_id_index` (`supplier_id`),
  KEY `product_stocks_size_index` (`size`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_suppliers`
--

DROP TABLE IF EXISTS `product_suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_suppliers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `supplier_id` int unsigned NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `supplier_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int unsigned NOT NULL DEFAULT '0',
  `price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_special` double NOT NULL DEFAULT '0',
  `price_discounted` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size_system` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `composition` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=82027 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_templates`
--

DROP TABLE IF EXISTS `product_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `template_no` int NOT NULL DEFAULT '0',
  `product_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  `currency` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `discounted_price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `product_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `font_style` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `font_size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `background_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_processed` int DEFAULT '0',
  `category_id` int DEFAULT NULL,
  `type` int NOT NULL DEFAULT '0',
  `store_website_id` int DEFAULT NULL,
  `template_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `uid` text COLLATE utf8mb4_unicode_ci,
  `image_url` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_translation_histories`
--

DROP TABLE IF EXISTS `product_translation_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_translation_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_translation_id` int unsigned NOT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_rejected` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_translation_histories_product_translation_id_index` (`product_translation_id`),
  KEY `product_translation_histories_locale_index` (`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_translations`
--

DROP TABLE IF EXISTS `product_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_translations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint NOT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_id` int NOT NULL,
  `is_rejected` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `composition` longtext COLLATE utf8mb4_unicode_ci,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` text COLLATE utf8mb4_unicode_ci,
  `country_of_manufacture` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dimension` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=260 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_update_logs`
--

DROP TABLE IF EXISTS `product_update_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_update_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `created_by` int NOT NULL,
  `product_id` int NOT NULL,
  `log` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_verifying_users`
--

DROP TABLE IF EXISTS `product_verifying_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_verifying_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `productactivities`
--

DROP TABLE IF EXISTS `productactivities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productactivities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `status_id` bigint unsigned NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `status_id` int unsigned NOT NULL DEFAULT '1',
  `sub_status_id` int DEFAULT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `short_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` text COLLATE utf8mb4_unicode_ci,
  `size_eu` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(8,2) DEFAULT NULL,
  `price_eur_special` decimal(8,2) NOT NULL DEFAULT '0.00',
  `price_eur_discounted` double NOT NULL DEFAULT '0',
  `discounted_percentage` decimal(8,2) NOT NULL DEFAULT '0.00',
  `stage` tinyint(1) NOT NULL DEFAULT '1',
  `measurement_size_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lmeasurement` text COLLATE utf8mb4_unicode_ci,
  `hmeasurement` text COLLATE utf8mb4_unicode_ci,
  `dmeasurement` text COLLATE utf8mb4_unicode_ci,
  `size_value` int DEFAULT NULL,
  `composition` longtext COLLATE utf8mb4_unicode_ci,
  `made_in` varchar(191) CHARACTER SET utf8 DEFAULT NULL,
  `brand` int DEFAULT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suggested_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_inr` double DEFAULT NULL,
  `price_inr_special` double DEFAULT '0',
  `price_inr_discounted` double NOT NULL DEFAULT '0',
  `price_special_offer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `euro_to_inr` int DEFAULT NULL,
  `percentage` int DEFAULT NULL,
  `factor` double DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `dnf` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isApproved` tinyint(1) DEFAULT '0',
  `rejected_note` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isUploaded` tinyint(1) NOT NULL DEFAULT '0',
  `is_uploaded_date` datetime DEFAULT NULL,
  `isFinal` tinyint(1) NOT NULL DEFAULT '0',
  `isListed` tinyint(1) NOT NULL DEFAULT '0',
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `stock` int NOT NULL DEFAULT '0',
  `is_on_sale` tinyint(1) NOT NULL DEFAULT '0',
  `purchase_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_status` int DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `supplier_link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_attributer` int unsigned DEFAULT NULL,
  `last_imagecropper` int unsigned DEFAULT NULL,
  `last_selector` int unsigned DEFAULT NULL,
  `last_searcher` int unsigned DEFAULT NULL,
  `quick_product` tinyint NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `import_date` datetime DEFAULT NULL,
  `status` int unsigned NOT NULL DEFAULT '0',
  `shopify_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_scraped` tinyint(1) NOT NULL,
  `is_image_processed` tinyint(1) NOT NULL DEFAULT '0',
  `is_without_image` tinyint(1) NOT NULL DEFAULT '0',
  `is_cron_check` int NOT NULL DEFAULT '0',
  `is_price_different` tinyint(1) NOT NULL DEFAULT '0',
  `crop_count` int NOT NULL DEFAULT '0',
  `is_crop_rejected` tinyint NOT NULL DEFAULT '0',
  `crop_remark` text COLLATE utf8mb4_unicode_ci,
  `is_crop_approved` tinyint NOT NULL DEFAULT '0',
  `is_farfetched` int NOT NULL DEFAULT '0',
  `approved_by` int DEFAULT NULL,
  `reject_approved_by` int DEFAULT NULL,
  `was_crop_rejected` tinyint(1) NOT NULL DEFAULT '0',
  `crop_rejected_by` int DEFAULT NULL,
  `crop_approved_by` int DEFAULT NULL,
  `is_being_cropped` tinyint(1) NOT NULL DEFAULT '0',
  `is_crop_ordered` tinyint(1) NOT NULL DEFAULT '0',
  `listing_remark` text COLLATE utf8mb4_unicode_ci,
  `is_listing_rejected` tinyint(1) NOT NULL DEFAULT '0',
  `listing_rejected_by` int DEFAULT NULL,
  `listing_rejected_on` date DEFAULT NULL,
  `is_corrected` tinyint(1) NOT NULL DEFAULT '0',
  `is_script_corrected` tinyint(1) NOT NULL DEFAULT '0',
  `is_authorized` tinyint(1) NOT NULL DEFAULT '0',
  `authorized_by` int DEFAULT NULL,
  `crop_ordered_by` int DEFAULT NULL,
  `is_crop_being_verified` tinyint(1) NOT NULL DEFAULT '0',
  `crop_approved_at` datetime DEFAULT NULL,
  `crop_rejected_at` datetime DEFAULT NULL,
  `crop_ordered_at` datetime DEFAULT NULL,
  `listing_approved_at` datetime DEFAULT NULL,
  `is_order_rejected` tinyint(1) NOT NULL DEFAULT '0',
  `manual_crop` tinyint(1) NOT NULL DEFAULT '0',
  `is_manual_cropped` tinyint(1) NOT NULL DEFAULT '0',
  `manual_cropped_by` int DEFAULT NULL,
  `manual_cropped_at` datetime DEFAULT NULL,
  `is_titlecased` tinyint(1) NOT NULL DEFAULT '0',
  `is_listing_rejected_automatically` tinyint(1) NOT NULL DEFAULT '0',
  `was_auto_rejected` tinyint(1) NOT NULL DEFAULT '0',
  `is_being_ordered` tinyint(1) NOT NULL DEFAULT '0',
  `instruction_completed_at` datetime DEFAULT NULL,
  `is_auto_processing_failed` tinyint(1) NOT NULL DEFAULT '0',
  `is_crop_skipped` tinyint(1) NOT NULL DEFAULT '0',
  `cropped_at` datetime DEFAULT NULL,
  `is_enhanced` tinyint(1) NOT NULL DEFAULT '0',
  `is_pending` int NOT NULL DEFAULT '0',
  `is_barcode_check` int DEFAULT NULL,
  `has_mediables` int DEFAULT '0',
  `scrap_priority` int DEFAULT '0',
  `assigned_to` int DEFAULT NULL,
  `last_brand` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_brand_index` (`brand`),
  KEY `products_supplier_index` (`supplier`(191)),
  KEY `products_is_on_sale_index` (`is_on_sale`),
  KEY `products_listing_approved_at_index` (`listing_approved_at`),
  KEY `products_status_id_foreign` (`status_id`),
  KEY `stock` (`stock`),
  KEY `fk_index_created_at` (`created_at`),
  KEY `deleted_at` (`deleted_at`),
  KEY `supplier` (`supplier`(191)),
  KEY `sku` (`sku`),
  KEY `supplier_id` (`supplier_id`),
  KEY `products_purchase_status_location_index` (`purchase_status`,`location`),
  KEY `products_category_index` (`category`),
  KEY `products_has_mediables_index` (`has_mediables`),
  CONSTRAINT `products_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=297571 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products_new`
--

DROP TABLE IF EXISTS `products_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_new` (
  `id` int unsigned NOT NULL,
  `status_id` int unsigned NOT NULL DEFAULT '1',
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `short_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(8,2) DEFAULT NULL,
  `price_eur_special` decimal(8,2) NOT NULL DEFAULT '0.00',
  `price_eur_discounted` double NOT NULL DEFAULT '0',
  `stage` tinyint(1) NOT NULL DEFAULT '1',
  `measurement_size_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lmeasurement` text COLLATE utf8mb4_unicode_ci,
  `hmeasurement` text COLLATE utf8mb4_unicode_ci,
  `dmeasurement` text COLLATE utf8mb4_unicode_ci,
  `size_value` int DEFAULT NULL,
  `composition` longtext COLLATE utf8mb4_unicode_ci,
  `made_in` varchar(191) CHARACTER SET utf8 DEFAULT NULL,
  `brand` int DEFAULT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_inr` double DEFAULT NULL,
  `price_inr_special` double DEFAULT '0',
  `price_inr_discounted` double NOT NULL DEFAULT '0',
  `price_special_offer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `euro_to_inr` int DEFAULT NULL,
  `percentage` int DEFAULT NULL,
  `factor` double DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `dnf` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isApproved` tinyint(1) DEFAULT '0',
  `rejected_note` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isUploaded` tinyint(1) NOT NULL DEFAULT '0',
  `is_uploaded_date` datetime DEFAULT NULL,
  `isFinal` tinyint(1) NOT NULL DEFAULT '0',
  `isListed` tinyint(1) NOT NULL DEFAULT '0',
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `stock` int NOT NULL DEFAULT '0',
  `is_on_sale` tinyint(1) NOT NULL DEFAULT '0',
  `purchase_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_attributer` int unsigned DEFAULT NULL,
  `last_imagecropper` int unsigned DEFAULT NULL,
  `last_selector` int unsigned DEFAULT NULL,
  `last_searcher` int unsigned DEFAULT NULL,
  `quick_product` tinyint NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `import_date` datetime DEFAULT NULL,
  `status` int unsigned NOT NULL DEFAULT '0',
  `is_scraped` tinyint(1) NOT NULL,
  `is_image_processed` tinyint(1) NOT NULL DEFAULT '0',
  `is_without_image` tinyint(1) NOT NULL DEFAULT '0',
  `is_price_different` tinyint(1) NOT NULL DEFAULT '0',
  `crop_count` int NOT NULL DEFAULT '0',
  `is_crop_rejected` tinyint NOT NULL DEFAULT '0',
  `crop_remark` text COLLATE utf8mb4_unicode_ci,
  `is_crop_approved` tinyint NOT NULL DEFAULT '0',
  `is_farfetched` int NOT NULL DEFAULT '0',
  `approved_by` int DEFAULT NULL,
  `reject_approved_by` int DEFAULT NULL,
  `was_crop_rejected` tinyint(1) NOT NULL DEFAULT '0',
  `crop_rejected_by` int DEFAULT NULL,
  `crop_approved_by` int DEFAULT NULL,
  `is_being_cropped` tinyint(1) NOT NULL DEFAULT '0',
  `is_crop_ordered` tinyint(1) NOT NULL DEFAULT '0',
  `listing_remark` text COLLATE utf8mb4_unicode_ci,
  `is_listing_rejected` tinyint(1) NOT NULL DEFAULT '0',
  `listing_rejected_by` int DEFAULT NULL,
  `listing_rejected_on` date DEFAULT NULL,
  `is_corrected` tinyint(1) NOT NULL DEFAULT '0',
  `is_script_corrected` tinyint(1) NOT NULL DEFAULT '0',
  `is_authorized` tinyint(1) NOT NULL DEFAULT '0',
  `authorized_by` int DEFAULT NULL,
  `crop_ordered_by` int DEFAULT NULL,
  `is_crop_being_verified` tinyint(1) NOT NULL DEFAULT '0',
  `crop_approved_at` datetime DEFAULT NULL,
  `crop_rejected_at` datetime DEFAULT NULL,
  `crop_ordered_at` datetime DEFAULT NULL,
  `listing_approved_at` datetime DEFAULT NULL,
  `is_order_rejected` tinyint(1) NOT NULL DEFAULT '0',
  `manual_crop` tinyint(1) NOT NULL DEFAULT '0',
  `is_manual_cropped` tinyint(1) NOT NULL DEFAULT '0',
  `manual_cropped_by` int DEFAULT NULL,
  `manual_cropped_at` datetime DEFAULT NULL,
  `is_titlecased` tinyint(1) NOT NULL DEFAULT '0',
  `is_listing_rejected_automatically` tinyint(1) NOT NULL DEFAULT '0',
  `was_auto_rejected` tinyint(1) NOT NULL DEFAULT '0',
  `is_being_ordered` tinyint(1) NOT NULL DEFAULT '0',
  `instruction_completed_at` datetime DEFAULT NULL,
  `is_auto_processing_failed` tinyint(1) NOT NULL DEFAULT '0',
  `is_crop_skipped` tinyint(1) NOT NULL DEFAULT '0',
  `cropped_at` datetime DEFAULT NULL,
  `is_enhanced` tinyint(1) NOT NULL DEFAULT '0',
  `is_pending` int NOT NULL DEFAULT '0',
  `is_barcode_check` int DEFAULT NULL,
  `has_mediables` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `products_brand_index` (`brand`),
  KEY `products_supplier_index` (`supplier`(250)),
  KEY `products_is_on_sale_index` (`is_on_sale`),
  KEY `products_listing_approved_at_index` (`listing_approved_at`),
  KEY `products_status_id_foreign` (`status_id`),
  KEY `stock` (`stock`),
  KEY `fk_index_created_at` (`created_at`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_file_managers`
--

DROP TABLE IF EXISTS `project_file_managers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_file_managers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_dev_master` int NOT NULL DEFAULT '0',
  `parent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_file_managers_history`
--

DROP TABLE IF EXISTS `project_file_managers_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_file_managers_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_keywords`
--

DROP TABLE IF EXISTS `project_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_keywords` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `keyword_id` int NOT NULL,
  `project_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proxies`
--

DROP TABLE IF EXISTS `proxies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proxies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reliability` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `public_keys`
--

DROP TABLE IF EXISTS `public_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `public_keys` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_discounts`
--

DROP TABLE IF EXISTS `purchase_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_discounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int NOT NULL,
  `product_id` int unsigned NOT NULL,
  `percentage` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_discounts_purchase_id_foreign` (`purchase_id`),
  CONSTRAINT `purchase_discounts_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_order_customer`
--

DROP TABLE IF EXISTS `purchase_order_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order_customer` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int NOT NULL,
  `customer_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_customer_purchase_id_index` (`purchase_id`),
  KEY `purchase_order_customer_customer_id_index` (`customer_id`),
  CONSTRAINT `purchase_order_customer_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `purchase_order_customer_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_product_order_excel_file_versions`
--

DROP TABLE IF EXISTS `purchase_product_order_excel_file_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_product_order_excel_file_versions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `excel_id` int DEFAULT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_version` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_product_order_excel_files`
--

DROP TABLE IF EXISTS `purchase_product_order_excel_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_product_order_excel_files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `excel_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_product_order_images`
--

DROP TABLE IF EXISTS `purchase_product_order_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_product_order_images` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_product_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_product_order_logs`
--

DROP TABLE IF EXISTS `purchase_product_order_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_product_order_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `purchase_product_order_id` int DEFAULT NULL,
  `order_products_id` int DEFAULT NULL,
  `header_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `replace_from` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `replace_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_product_orders`
--

DROP TABLE IF EXISTS `purchase_product_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_product_orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `order_products_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `invoice` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_currency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_amount` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duty_cost` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mrp_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `special_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_products_order_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_product_orders_product_id_index` (`product_id`),
  KEY `purchase_product_orders_order_id_index` (`order_id`),
  KEY `purchase_product_orders_supplier_id_index` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_product_supplier`
--

DROP TABLE IF EXISTS `purchase_product_supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_product_supplier` (
  `product_id` int unsigned NOT NULL,
  `supplier_id` int unsigned NOT NULL,
  `chat_message_id` int unsigned NOT NULL,
  KEY `purchase_product_supplier_product_id_index` (`product_id`),
  KEY `purchase_product_supplier_supplier_id_index` (`supplier_id`),
  KEY `purchase_product_supplier_chat_message_id_index` (`chat_message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_products`
--

DROP TABLE IF EXISTS `purchase_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_id` int NOT NULL,
  `product_id` int NOT NULL,
  `order_product_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_status`
--

DROP TABLE IF EXISTS `purchase_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_handler` int NOT NULL,
  `supplier_id` int unsigned DEFAULT NULL,
  `agent_id` int unsigned DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `purchase_status_id` int unsigned DEFAULT NULL,
  `supplier_phone` varchar(255) DEFAULT NULL,
  `whatsapp_number` varchar(255) NOT NULL DEFAULT '919152731486',
  `transaction_id` varchar(191) DEFAULT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `transaction_amount` varchar(191) DEFAULT NULL,
  `bill_number` varchar(255) DEFAULT NULL,
  `shipper` varchar(191) DEFAULT NULL,
  `shipment_status` varchar(191) DEFAULT NULL,
  `shipment_cost` varchar(191) DEFAULT NULL,
  `shipment_date` datetime DEFAULT NULL,
  `proforma_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `proforma_id` varchar(191) DEFAULT NULL,
  `proforma_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchases_purchase_status_id_foreign` (`purchase_status_id`),
  CONSTRAINT `purchases_purchase_status_id_foreign` FOREIGN KEY (`purchase_status_id`) REFERENCES `purchase_status` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `push_fcm_notification_histories`
--

DROP TABLE IF EXISTS `push_fcm_notification_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `push_fcm_notification_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_id` int DEFAULT NULL,
  `success` int DEFAULT '0',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `push_fcm_notifications`
--

DROP TABLE IF EXISTS `push_fcm_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `push_fcm_notifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_website_id` int NOT NULL,
  `sent_at` datetime DEFAULT NULL,
  `sent_on` datetime DEFAULT NULL,
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `push_fcm_notifications_store_website_id_foreign` (`store_website_id`),
  CONSTRAINT `push_fcm_notifications_store_website_id_foreign` FOREIGN KEY (`store_website_id`) REFERENCES `store_websites` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `push_notifications`
--

DROP TABLE IF EXISTS `push_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `push_notifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_id` int DEFAULT NULL,
  `isread` tinyint(1) NOT NULL DEFAULT '0',
  `reminder` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `queue_monitor`
--

DROP TABLE IF EXISTS `queue_monitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `queue_monitor` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `job_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `started_at_exact` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `finished_at_exact` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_elapsed` double(12,6) DEFAULT NULL,
  `failed` tinyint(1) NOT NULL DEFAULT '0',
  `attempt` int NOT NULL DEFAULT '0',
  `progress` int DEFAULT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci,
  `exception_message` text COLLATE utf8mb4_unicode_ci,
  `exception_class` text COLLATE utf8mb4_unicode_ci,
  `data` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `queue_monitor_job_id_index` (`job_id`),
  KEY `queue_monitor_started_at_index` (`started_at`),
  KEY `queue_monitor_time_elapsed_index` (`time_elapsed`),
  KEY `queue_monitor_failed_index` (`failed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quick_replies`
--

DROP TABLE IF EXISTS `quick_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quick_replies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quick_sell_groups`
--

DROP TABLE IF EXISTS `quick_sell_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quick_sell_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `group` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suppliers` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brands` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `special_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categories` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `refer_friend`
--

DROP TABLE IF EXISTS `refer_friend`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `refer_friend` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `referrer_first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referrer_last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referrer_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referrer_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referee_first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referee_last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referee_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referee_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_website_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referral_programs`
--

DROP TABLE IF EXISTS `referral_programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `referral_programs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credit` double(8,2) NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lifetime_minutes` int NOT NULL DEFAULT '10080',
  `store_website_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `referral_programs_name_index` (`name`),
  KEY `referral_programs_credit_index` (`credit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reffring_domains`
--

DROP TABLE IF EXISTS `reffring_domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reffring_domains` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `tool_id` int NOT NULL,
  `database` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referring_domains` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `referring_ips` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `referring_domains_by_country` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `refunds`
--

DROP TABLE IF EXISTS `refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `refunds` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `order_id` int NOT NULL,
  `type` varchar(255) NOT NULL,
  `chq_number` varchar(255) DEFAULT NULL,
  `awb` varchar(255) DEFAULT NULL,
  `payment` varchar(255) DEFAULT NULL,
  `date_of_refund` timestamp NULL DEFAULT NULL,
  `date_of_issue` timestamp NULL DEFAULT NULL,
  `details` longtext,
  `dispatch_date` timestamp NULL DEFAULT NULL,
  `date_of_request` timestamp NULL DEFAULT NULL,
  `credited` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rejected_images`
--

DROP TABLE IF EXISTS `rejected_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rejected_images` (
  `website_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `user_id` int NOT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '1->approve, 0->rejected',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`website_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rejected_leads`
--

DROP TABLE IF EXISTS `rejected_leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rejected_leads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'instagram',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `remarks`
--

DROP TABLE IF EXISTS `remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `remarks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `taskid` int DEFAULT NULL,
  `module_type` varchar(255) DEFAULT NULL,
  `remark` text,
  `user_name` text,
  `is_flagged` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `delete_at` timestamp NULL DEFAULT NULL,
  `is_hide` tinyint NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `replies`
--

DROP TABLE IF EXISTS `replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `replies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL DEFAULT '1',
  `reply` longtext NOT NULL,
  `model` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `replies_model_category_id_index` (`model`,`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reply_categories`
--

DROP TABLE IF EXISTS `reply_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reply_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `parent_id` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_categories`
--

DROP TABLE IF EXISTS `resource_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL DEFAULT '0',
  `title` varchar(299) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` enum('Y','N') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` varchar(199) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_images`
--

DROP TABLE IF EXISTS `resource_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_images` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` int unsigned NOT NULL,
  `url` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image1` text COLLATE utf8mb4_unicode_ci,
  `image2` text COLLATE utf8mb4_unicode_ci,
  `created_by` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `images` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_cat_id` int NOT NULL,
  `is_pending` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `return_exchange_histories`
--

DROP TABLE IF EXISTS `return_exchange_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `return_exchange_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `return_exchange_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `history_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `return_exchange_histories_return_exchange_id_index` (`return_exchange_id`),
  KEY `return_exchange_histories_user_id_index` (`user_id`),
  KEY `return_exchange_histories_status_id_index` (`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `return_exchange_products`
--

DROP TABLE IF EXISTS `return_exchange_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `return_exchange_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `return_exchange_id` int DEFAULT NULL,
  `status_id` int DEFAULT NULL,
  `product_id` int NOT NULL,
  `order_product_id` text COLLATE utf8mb4_unicode_ci,
  `name` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `return_exchange_products_return_exchange_id_index` (`return_exchange_id`),
  KEY `return_exchange_products_status_id_index` (`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `return_exchange_statuses`
--

DROP TABLE IF EXISTS `return_exchange_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `return_exchange_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `status_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `return_exchanges`
--

DROP TABLE IF EXISTS `return_exchanges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `return_exchanges` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `type` enum('refund','exchange','buyback','return','cancellation') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason_for_refund` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refund_amount` decimal(8,2) DEFAULT '0.00',
  `status` int NOT NULL,
  `pickup_address` text COLLATE utf8mb4_unicode_ci,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `refund_amount_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chq_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `awb` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_refund` timestamp NULL DEFAULT NULL,
  `date_of_issue` timestamp NULL DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `dispatch_date` timestamp NULL DEFAULT NULL,
  `date_of_request` timestamp NULL DEFAULT NULL,
  `credited` tinyint(1) NOT NULL DEFAULT '0',
  `est_completion_date` date DEFAULT NULL,
  `website_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `return_exchanges_customer_id_index` (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `review_brands_list`
--

DROP TABLE IF EXISTS `review_brands_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `review_brands_list` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `url` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `review_schedules`
--

DROP TABLE IF EXISTS `review_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `review_schedules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int unsigned DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `date` date NOT NULL,
  `posted_date` datetime DEFAULT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `review_count` int DEFAULT NULL,
  `review_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `review_schedules_account_id_foreign` (`account_id`),
  KEY `review_schedules_customer_id_foreign` (`customer_id`),
  CONSTRAINT `review_schedules_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `review_schedules_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `review_schedule_id` int DEFAULT NULL,
  `account_id` int unsigned DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `review` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `is_approved` int unsigned NOT NULL DEFAULT '0',
  `posted_date` datetime DEFAULT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `review_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_account_id_foreign` (`account_id`),
  KEY `reviews_customer_id_foreign` (`customer_id`),
  CONSTRAINT `reviews_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `reviews_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` int unsigned NOT NULL,
  `role_id` int unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_user`
--

DROP TABLE IF EXISTS `role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_user` (
  `user_id` int unsigned NOT NULL,
  `role_id` int unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_user_user_id_index` (`user_id`),
  KEY `role_user_role_id_index` (`role_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `roles_name_guard_name_index` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `routes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1310 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rude_words`
--

DROP TABLE IF EXISTS `rude_words`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rude_words` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `universal` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `s_e_ranking`
--

DROP TABLE IF EXISTS `s_e_ranking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `s_e_ranking` (
  `id` int DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_check_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int unsigned DEFAULT NULL,
  `date_of_request` date DEFAULT NULL,
  `sales_person_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_handle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selected_product` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allocated_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `finished_at` time DEFAULT NULL,
  `check_1` tinyint(1) DEFAULT '0',
  `check_2` tinyint(1) NOT NULL DEFAULT '0',
  `check_3` tinyint(1) NOT NULL DEFAULT '0',
  `sent_to_client` time DEFAULT NULL,
  `remark` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sales_item`
--

DROP TABLE IF EXISTS `sales_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier` tinytext COLLATE utf8mb4_general_ci NOT NULL,
  `brand` tinytext COLLATE utf8mb4_general_ci NOT NULL,
  `product_link` mediumtext COLLATE utf8mb4_general_ci NOT NULL,
  `title` tinytext COLLATE utf8mb4_general_ci NOT NULL,
  `old_price` tinytext COLLATE utf8mb4_general_ci,
  `new_price` tinytext COLLATE utf8mb4_general_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_general_ci,
  `dimension` mediumtext COLLATE utf8mb4_general_ci,
  `SKU` text COLLATE utf8mb4_general_ci NOT NULL,
  `country` text COLLATE utf8mb4_general_ci,
  `material_used` mediumtext COLLATE utf8mb4_general_ci,
  `color` text COLLATE utf8mb4_general_ci,
  `images` longtext COLLATE utf8mb4_general_ci,
  `sizes` text COLLATE utf8mb4_general_ci,
  `category` text COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `satutory_tasks`
--

DROP TABLE IF EXISTS `satutory_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `satutory_tasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` int DEFAULT NULL,
  `assign_from` int NOT NULL,
  `assign_to` int NOT NULL,
  `assign_status` int DEFAULT NULL,
  `task_details` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `task_subject` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `remark` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recurring_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recurring_day` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `completion_date` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schedule_groups`
--

DROP TABLE IF EXISTS `schedule_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `images` text NOT NULL,
  `description` text,
  `scheduled_for` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheduled_messages`
--

DROP TABLE IF EXISTS `scheduled_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheduled_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `customer_id` int DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` text COLLATE utf8mb4_unicode_ci,
  `data` text COLLATE utf8mb4_unicode_ci,
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  `sending_time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scheduled_messages_user_id_foreign` (`user_id`),
  KEY `scheduled_messages_customer_id_foreign` (`customer_id`),
  CONSTRAINT `scheduled_messages_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `scheduled_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrap_activities`
--

DROP TABLE IF EXISTS `scrap_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrap_activities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scraped_product_id` int unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrap_api_logs`
--

DROP TABLE IF EXISTS `scrap_api_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrap_api_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `scraper_id` int DEFAULT NULL,
  `server_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_messages` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrap_counts`
--

DROP TABLE IF EXISTS `scrap_counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrap_counts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `link_count` int NOT NULL,
  `scraped_date` date NOT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrap_entries`
--

DROP TABLE IF EXISTS `scrap_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrap_entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` longtext NOT NULL,
  `site_name` varchar(16) NOT NULL DEFAULT 'GNB',
  `is_scraped` tinyint(1) NOT NULL DEFAULT '0',
  `is_product_page` tinyint(1) NOT NULL DEFAULT '0',
  `pagination` longtext,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_updated_on_server` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrap_histories`
--

DROP TABLE IF EXISTS `scrap_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrap_histories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `operation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` int NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrap_influencers`
--

DROP TABLE IF EXISTS `scrap_influencers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrap_influencers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `followers` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `following` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `posts` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `post_id` int DEFAULT NULL,
  `post_caption` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_user_id` int DEFAULT NULL,
  `post_media_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_hashtag_id` int DEFAULT NULL,
  `post_likes` int DEFAULT NULL,
  `post_comments_count` int DEFAULT NULL,
  `post_media_url` text COLLATE utf8mb4_unicode_ci,
  `posted_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment_user_id` int DEFAULT NULL,
  `comment_user_full_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment_username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_post_id` int DEFAULT NULL,
  `comment_id` int DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `comment_profile_pic_url` text COLLATE utf8mb4_unicode_ci,
  `comment_posted_at` timestamp NULL DEFAULT NULL,
  `profile_pic` text COLLATE utf8mb4_unicode_ci,
  `friends` text COLLATE utf8mb4_unicode_ci,
  `cover_photo` text COLLATE utf8mb4_unicode_ci,
  `interests` text COLLATE utf8mb4_unicode_ci,
  `work_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read_status` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrap_logs`
--

DROP TABLE IF EXISTS `scrap_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrap_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `scraper_id` int DEFAULT NULL,
  `folder_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_messages` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrap_remarks`
--

DROP TABLE IF EXISTS `scrap_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrap_remarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `scrap_id` int NOT NULL,
  `module_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scraper_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remark` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scrap_field` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scrap_remarks_scraper_name_index` (`scraper_name`)
) ENGINE=MyISAM AUTO_INCREMENT=249 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrap_request_histories`
--

DROP TABLE IF EXISTS `scrap_request_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrap_request_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `scraper_id` int NOT NULL,
  `date` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `end_time` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_sent` int NOT NULL DEFAULT '0',
  `request_failed` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scrap_request_histories_scraper_id_index` (`scraper_id`)
) ENGINE=InnoDB AUTO_INCREMENT=305 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrap_statistics`
--

DROP TABLE IF EXISTS `scrap_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrap_statistics` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `brand` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrape_queues`
--

DROP TABLE IF EXISTS `scrape_queues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrape_queues` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `done` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scraped_product_missing_log`
--

DROP TABLE IF EXISTS `scraped_product_missing_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scraped_product_missing_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_product` int NOT NULL DEFAULT '0',
  `missing_category` int NOT NULL DEFAULT '0',
  `missing_color` int NOT NULL DEFAULT '0',
  `missing_composition` int NOT NULL DEFAULT '0',
  `missing_name` int NOT NULL DEFAULT '0',
  `missing_short_description` int NOT NULL DEFAULT '0',
  `missing_price` int NOT NULL DEFAULT '0',
  `missing_size` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scraped_products`
--

DROP TABLE IF EXISTS `scraped_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scraped_products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `website` varchar(255) NOT NULL,
  `is_excel` tinyint NOT NULL DEFAULT '0',
  `sku` varchar(255) NOT NULL,
  `product_id` int DEFAULT NULL,
  `has_sku` tinyint(1) NOT NULL DEFAULT '0',
  `title` text NOT NULL,
  `composition` varchar(191) DEFAULT NULL,
  `color` varchar(191) DEFAULT NULL,
  `categories` varchar(191) DEFAULT NULL,
  `brand_id` int unsigned NOT NULL,
  `description` longtext,
  `images` mediumtext NOT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `price` varchar(255) NOT NULL,
  `price_eur` decimal(8,2) DEFAULT NULL,
  `discounted_price_eur` decimal(8,2) DEFAULT NULL,
  `size_system` varchar(2) DEFAULT NULL,
  `properties` longtext,
  `url` varchar(1025) DEFAULT NULL,
  `is_property_updated` tinyint NOT NULL DEFAULT '0',
  `is_price_updated` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `last_cron_check` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_enriched` tinyint(1) NOT NULL DEFAULT '0',
  `can_be_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `is_color_fixed` tinyint(1) NOT NULL DEFAULT '0',
  `is_sale` tinyint(1) NOT NULL DEFAULT '0',
  `original_sku` varchar(255) DEFAULT NULL,
  `discounted_price` varchar(191) DEFAULT NULL,
  `discounted_percentage` decimal(8,2) NOT NULL DEFAULT '0.00',
  `ip_address` varchar(255) DEFAULT NULL,
  `category` int DEFAULT NULL,
  `validated` int DEFAULT NULL,
  `validation_result` text,
  `raw_data` text,
  `cron_executed` int NOT NULL DEFAULT '0',
  `last_inventory_at` datetime DEFAULT NULL,
  `is_external_scraper` int NOT NULL DEFAULT '0',
  `size` longtext,
  `material_used` longtext,
  `country` longtext,
  `supplier` longtext,
  PRIMARY KEY (`id`),
  KEY `scraped_products_sku_index` (`sku`),
  KEY `scraped_products_last_inventory_at_index` (`last_inventory_at`),
  KEY `scraped_products_is_excel_index` (`is_excel`),
  KEY `scraped_products_website_at_index` (`website`),
  KEY `scraped_products_product_id_index` (`product_id`),
  KEY `url` (`url`(1024)),
  KEY `scraped_products_composition_index` (`composition`),
  FULLTEXT KEY `properties` (`properties`)
) ENGINE=InnoDB AUTO_INCREMENT=345667 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scraper_durations`
--

DROP TABLE IF EXISTS `scraper_durations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scraper_durations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `scraper_id` bigint DEFAULT NULL,
  `process_id` bigint DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scraper_imags`
--

DROP TABLE IF EXISTS `scraper_imags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scraper_imags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `store_website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coordinates` longtext COLLATE utf8mb4_unicode_ci,
  `height` int DEFAULT NULL,
  `width` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`),
  KEY `website_id` (`website_id`,`store_website`)
) ENGINE=InnoDB AUTO_INCREMENT=428815 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scraper_mappings`
--

DROP TABLE IF EXISTS `scraper_mappings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scraper_mappings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `scrapers_id` int NOT NULL,
  `selector` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `function` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parameter` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scraper_position_histories`
--

DROP TABLE IF EXISTS `scraper_position_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scraper_position_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `scraper_id` int NOT NULL,
  `scraper_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `server_scraper_position_histories_scraper_id_index` (`scraper_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scraper_processes`
--

DROP TABLE IF EXISTS `scraper_processes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scraper_processes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `scraper_id` int NOT NULL,
  `server_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `scraper_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scraper_results`
--

DROP TABLE IF EXISTS `scraper_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scraper_results` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `scraper_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_urls` int NOT NULL,
  `existing_urls` int NOT NULL,
  `new_urls` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scraper_screenshot_histories`
--

DROP TABLE IF EXISTS `scraper_screenshot_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scraper_screenshot_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `scraper_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scraper_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scraper_server_histories`
--

DROP TABLE IF EXISTS `scraper_server_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scraper_server_histories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `scraper_id` int NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scraper_server_status_histories`
--

DROP TABLE IF EXISTS `scraper_server_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scraper_server_status_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `scraper_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scraper_string` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `server_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_memory` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `used_memory` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `in_percentage` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `pid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrapers`
--

DROP TABLE IF EXISTS `scrapers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrapers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int NOT NULL,
  `parent_supplier_id` int DEFAULT '0',
  `scraper_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_scrape` tinyint NOT NULL DEFAULT '0',
  `scraper_type` int DEFAULT NULL,
  `scraper_total_urls` int NOT NULL DEFAULT '0',
  `scraper_new_urls` int NOT NULL DEFAULT '0',
  `scraper_existing_urls` int NOT NULL DEFAULT '0',
  `scraper_start_time` int NOT NULL,
  `scraper_logic` text COLLATE utf8mb4_unicode_ci,
  `scraper_made_by` int DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  `scraper_priority` int DEFAULT NULL,
  `inventory_lifetime` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `next_step_in_product_flow` int DEFAULT NULL,
  `end_time` datetime NOT NULL,
  `start_time` datetime NOT NULL,
  `product_url_selector` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `designer_url_selector` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `starting_urls` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int DEFAULT NULL,
  `time_out` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `run_gap` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '24',
  `auto_restart` int DEFAULT '0',
  `flag` int DEFAULT '0',
  `developer_flag` int DEFAULT '0',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `server_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_completed_at` timestamp NULL DEFAULT NULL,
  `last_started_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scrapers_supplier_id_index` (`supplier_id`),
  KEY `scrapers_scraper_name_index` (`scraper_name`),
  KEY `scrapers_scraper_priority_index` (`scraper_priority`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1079 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrapped_category_mappings`
--

DROP TABLE IF EXISTS `scrapped_category_mappings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrapped_category_mappings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_mapped` tinyint(1) NOT NULL DEFAULT '0',
  `is_skip` tinyint(1) NOT NULL DEFAULT '0',
  `category_id` int unsigned DEFAULT NULL,
  `is_auto_fix` tinyint(1) NOT NULL,
  `is_auto_skip` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `scrapped_category_mappings_name_unique` (`name`),
  KEY `scrapped_category_mappings_is_mapped_index` (`is_mapped`),
  KEY `scrapped_category_mappings_name_index` (`name`),
  KEY `scrapped_category_mappings_category_id_index` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrapped_facebook_users`
--

DROP TABLE IF EXISTS `scrapped_facebook_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrapped_facebook_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `url` text COLLATE utf8mb4_unicode_ci,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `keyword` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrapped_product_category_mappings`
--

DROP TABLE IF EXISTS `scrapped_product_category_mappings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrapped_product_category_mappings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `category_mapping_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scrapped_product_category_mappings_product_id_index` (`product_id`),
  KEY `scrapped_product_category_mappings_category_mapping_id_index` (`category_mapping_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrapper_log_status`
--

DROP TABLE IF EXISTS `scrapper_log_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scrapper_log_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `search_queues`
--

DROP TABLE IF EXISTS `search_queues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `search_queues` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `search_type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `search_term` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` int DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `search_terms`
--

DROP TABLE IF EXISTS `search_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `search_terms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `term` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int NOT NULL,
  `score` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `search_terms_entity_type_entity_id_index` (`entity_type`,`entity_id`),
  KEY `search_terms_term_index` (`term`(191)),
  KEY `search_terms_entity_type_index` (`entity_type`),
  KEY `search_terms_score_index` (`score`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `semrush_keywords`
--

DROP TABLE IF EXISTS `semrush_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `semrush_keywords` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `semrush_tags`
--

DROP TABLE IF EXISTS `semrush_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `semrush_tags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sendgrid_events`
--

DROP TABLE IF EXISTS `sendgrid_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sendgrid_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NULL DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sg_event_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sg_message_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categories` json DEFAULT NULL,
  `payload` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sendgrid_events_sg_event_id_unique` (`sg_event_id`),
  KEY `sendgrid_events_email_index` (`email`),
  KEY `sendgrid_events_event_index` (`event`),
  KEY `sendgrid_events_sg_message_id_index` (`sg_message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seo_analytics`
--

DROP TABLE IF EXISTS `seo_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seo_analytics` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `domain_authority` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linking_authority` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inbound_links` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ranking_keywords` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seo_keyword_ideas`
--

DROP TABLE IF EXISTS `seo_keyword_ideas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seo_keyword_ideas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idea` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_website_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seo_tools`
--

DROP TABLE IF EXISTS `seo_tools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seo_tools` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tool` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `val` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` char(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `welcome_message` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `simply_duty_calculations`
--

DROP TABLE IF EXISTS `simply_duty_calculations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `simply_duty_calculations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `vat` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vat_rate` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vat_minimis` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duty_minimis` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_type_destination` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_type_origin` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_rate` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `insurance` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duty_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duty_hscode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duty` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duty_rate` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `simply_duty_categories`
--

DROP TABLE IF EXISTS `simply_duty_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `simply_duty_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `correct_composition` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `simply_duty_countries`
--

DROP TABLE IF EXISTS `simply_duty_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `simply_duty_countries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `country_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `default_duty` double(10,2) NOT NULL DEFAULT '0.00',
  `segment_id` int DEFAULT '0',
  `status` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `simply_duty_country_histories`
--

DROP TABLE IF EXISTS `simply_duty_country_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `simply_duty_country_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `simply_duty_countries_id` int DEFAULT NULL,
  `old_segment` int DEFAULT NULL,
  `new_segment` int DEFAULT NULL,
  `old_duty` int DEFAULT NULL,
  `new_duty` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `simply_duty_currencies`
--

DROP TABLE IF EXISTS `simply_duty_currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `simply_duty_currencies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `simply_duty_segments`
--

DROP TABLE IF EXISTS `simply_duty_segments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `simply_duty_segments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `segment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_audit`
--

DROP TABLE IF EXISTS `site_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_audit` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `store_website_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `errors` int NOT NULL,
  `warnings` int NOT NULL,
  `notices` int NOT NULL,
  `broken` int NOT NULL,
  `blocked` int NOT NULL,
  `redirected` int NOT NULL,
  `healthy` int NOT NULL,
  `haveIssues` int NOT NULL,
  `haveIssuesDelta` int NOT NULL,
  `defects` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `markups` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `depths` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `crawlSubdomains` tinyint NOT NULL,
  `respectCrawlDelay` tinyint NOT NULL,
  `canonical` int NOT NULL,
  `user_agent_type` int NOT NULL,
  `last_audit` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_failed_audit` int NOT NULL,
  `next_audit` int NOT NULL,
  `running_pages_crawled` int NOT NULL,
  `running_pages_limit` int NOT NULL,
  `pages_crawled` int NOT NULL,
  `pages_limit` int NOT NULL,
  `total_checks` int NOT NULL,
  `errors_delta` int NOT NULL,
  `warnings_delta` int NOT NULL,
  `notices_delta` int NOT NULL,
  `mask_allow` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mask_disallow` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `removedParameters` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `excluded_checks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_cropped_images`
--

DROP TABLE IF EXISTS `site_cropped_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_cropped_images` (
  `website_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`website_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_development_artowrk_histories`
--

DROP TABLE IF EXISTS `site_development_artowrk_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_development_artowrk_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `site_development_id` int NOT NULL,
  `from_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_development_categories`
--

DROP TABLE IF EXISTS `site_development_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_development_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `master_category_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_development_hidden_categories`
--

DROP TABLE IF EXISTS `site_development_hidden_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_development_hidden_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_development_master_categories`
--

DROP TABLE IF EXISTS `site_development_master_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_development_master_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_development_status_histories`
--

DROP TABLE IF EXISTS `site_development_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_development_status_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `site_development_id` int NOT NULL,
  `status_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_development_status_histories_site_development_id_index` (`site_development_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_development_statuses`
--

DROP TABLE IF EXISTS `site_development_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_development_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_developments`
--

DROP TABLE IF EXISTS `site_developments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_developments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `site_development_category_id` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `developer_id` int DEFAULT NULL,
  `designer_id` int DEFAULT NULL,
  `website_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `html_designer` int DEFAULT NULL,
  `artwork_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Yes',
  `tester_id` int DEFAULT NULL,
  `site_development_master_category_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_issues`
--

DROP TABLE IF EXISTS `site_issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_issues` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `project_id` int NOT NULL,
  `issue_id` int NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_page` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sitejabber_q_a_s`
--

DROP TABLE IF EXISTS `sitejabber_q_a_s`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sitejabber_q_a_s` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('question','answer','reply') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sites`
--

DROP TABLE IF EXISTS `sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sites` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `site_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_level` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sizes`
--

DROP TABLE IF EXISTS `sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sizes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `magento_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `references` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `sizes_name_index` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sku_color_references`
--

DROP TABLE IF EXISTS `sku_color_references`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sku_color_references` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int NOT NULL,
  `color_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sku_format_histories`
--

DROP TABLE IF EXISTS `sku_format_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sku_format_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sku_format_id` int NOT NULL,
  `old_sku_format` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku_format` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sku_formats`
--

DROP TABLE IF EXISTS `sku_formats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sku_formats` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int NOT NULL,
  `category_id` int NOT NULL,
  `sku_examples` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku_format` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku_format_without_color` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_group`
--

DROP TABLE IF EXISTS `sms_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_group` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `group_id` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_ids` longtext COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '0',
  `deleted_at` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sms_group_group_id_unique` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_service`
--

DROP TABLE IF EXISTS `sms_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_service` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `social_strategies`
--

DROP TABLE IF EXISTS `social_strategies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_strategies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `social_strategy_subject_id` int NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `execution_id` int DEFAULT NULL,
  `content_id` int DEFAULT NULL,
  `website_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `social_strategy_remarks`
--

DROP TABLE IF EXISTS `social_strategy_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_strategy_remarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `social_strategy_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `social_strategy_subjects`
--

DROP TABLE IF EXISTS `social_strategy_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_strategy_subjects` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `social_tags`
--

DROP TABLE IF EXISTS `social_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_tags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sop_permissions`
--

DROP TABLE IF EXISTS `sop_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sop_permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sop_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sops`
--

DROP TABLE IF EXISTS `sops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sops` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `status_changes`
--

DROP TABLE IF EXISTS `status_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `status_changes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `from_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_changes_user_id_foreign` (`user_id`),
  CONSTRAINT `status_changes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_products`
--

DROP TABLE IF EXISTS `stock_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `stock_id` int NOT NULL,
  `product_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stocks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `courier` varchar(255) NOT NULL,
  `package_from` varchar(255) DEFAULT NULL,
  `awb` varchar(255) NOT NULL,
  `l_dimension` varchar(255) DEFAULT NULL,
  `w_dimension` varchar(255) DEFAULT NULL,
  `h_dimension` varchar(255) DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL,
  `pcs` int DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_development_remarks`
--

DROP TABLE IF EXISTS `store_development_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_development_remarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `store_development_id` int NOT NULL,
  `user_id` int NOT NULL,
  `user_flagged` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `admin_flagged` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_gt_metrix_account`
--

DROP TABLE IF EXISTS `store_gt_metrix_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_gt_metrix_account` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_magento_api_search_products`
--

DROP TABLE IF EXISTS `store_magento_api_search_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_magento_api_search_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website_id` int DEFAULT NULL,
  `website` longtext COLLATE utf8mb4_unicode_ci,
  `sku` longtext COLLATE utf8mb4_unicode_ci,
  `size` longtext COLLATE utf8mb4_unicode_ci,
  `brands` longtext COLLATE utf8mb4_unicode_ci,
  `dimensions` longtext COLLATE utf8mb4_unicode_ci,
  `composition` longtext COLLATE utf8mb4_unicode_ci,
  `images` longtext COLLATE utf8mb4_unicode_ci,
  `size_chart_url` longtext COLLATE utf8mb4_unicode_ci,
  `category_names` longtext COLLATE utf8mb4_unicode_ci,
  `english` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arabic` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `german` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spanish` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `french` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `italian` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `japanese` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `korean` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `russian` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chinese` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_master_statuses`
--

DROP TABLE IF EXISTS `store_master_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_master_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_order_statuses`
--

DROP TABLE IF EXISTS `store_order_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_order_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_status_id` int NOT NULL,
  `store_website_id` int NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `store_master_status_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_reindex_history`
--

DROP TABLE IF EXISTS `store_reindex_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_reindex_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `server_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_social_accounts`
--

DROP TABLE IF EXISTS `store_social_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_social_accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_social_content_categories`
--

DROP TABLE IF EXISTS `store_social_content_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_social_content_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_social_content_histories`
--

DROP TABLE IF EXISTS `store_social_content_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_social_content_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_social_content_id` int NOT NULL,
  `message` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_social_content_milestones`
--

DROP TABLE IF EXISTS `store_social_content_milestones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_social_content_milestones` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int NOT NULL,
  `ono_of_content` int NOT NULL,
  `store_social_content_id` int NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_social_content_remarks`
--

DROP TABLE IF EXISTS `store_social_content_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_social_content_remarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `store_social_content_id` int NOT NULL,
  `remarks` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_social_content_reviews`
--

DROP TABLE IF EXISTS `store_social_content_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_social_content_reviews` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int NOT NULL,
  `review` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `review_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_social_content_statuses`
--

DROP TABLE IF EXISTS `store_social_content_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_social_content_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_social_contents`
--

DROP TABLE IF EXISTS `store_social_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_social_contents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_social_content_category_id` int NOT NULL,
  `store_website_id` int NOT NULL,
  `request_date` timestamp NULL DEFAULT NULL,
  `due_date` timestamp NULL DEFAULT NULL,
  `publish_date` timestamp NULL DEFAULT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_social_content_status_id` int NOT NULL,
  `creator_id` int DEFAULT NULL,
  `publisher_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_views_gt_metrix`
--

DROP TABLE IF EXISTS `store_views_gt_metrix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_views_gt_metrix` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_view_id` int DEFAULT NULL,
  `test_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flag` int DEFAULT NULL,
  `error` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` text COLLATE utf8mb4_unicode_ci,
  `html_load_time` int DEFAULT NULL,
  `html_bytes` int DEFAULT NULL,
  `page_load_time` int DEFAULT NULL,
  `page_bytes` int DEFAULT NULL,
  `page_elements` int DEFAULT NULL,
  `pagespeed_score` int DEFAULT NULL,
  `yslow_score` int DEFAULT NULL,
  `resources` text COLLATE utf8mb4_unicode_ci,
  `pdf_file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pagespeed_json` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pagespeed_insight_json` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `yslow_json` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92703 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_analytics`
--

DROP TABLE IF EXISTS `store_website_analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_analytics` (
  `id` int NOT NULL AUTO_INCREMENT,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_error` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_error_at` timestamp NULL DEFAULT NULL,
  `account_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `view_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_website_id` int DEFAULT NULL,
  `google_service_account_json` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_website_id` (`store_website_id`),
  KEY `store_website_analytics_website_index` (`website`),
  CONSTRAINT `store_website_analytics_ibfk_1` FOREIGN KEY (`store_website_id`) REFERENCES `store_websites` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_attributes`
--

DROP TABLE IF EXISTS `store_website_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_attributes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `attribute_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attribute_val` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_website_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_brand_histories`
--

DROP TABLE IF EXISTS `store_website_brand_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_brand_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  `type` enum('assign','remove','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'error',
  `message` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_brands`
--

DROP TABLE IF EXISTS `store_website_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_brands` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int NOT NULL,
  `markup` double(8,2) DEFAULT '0.00',
  `magento_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_website_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_website_brands_brand_id_store_website_id_index` (`brand_id`,`store_website_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18660 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_categories`
--

DROP TABLE IF EXISTS `store_website_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `remote_id` int DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `category_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3665 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_category_seos`
--

DROP TABLE IF EXISTS `store_website_category_seos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_category_seos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  `meta_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_keyword` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `language_id` int NOT NULL DEFAULT '1',
  `meta_keyword_avg_monthly` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=210 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_category_seos_histories`
--

DROP TABLE IF EXISTS `store_website_category_seos_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_category_seos_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_cate_seos_id` int DEFAULT NULL,
  `old_keywords` text COLLATE utf8mb4_unicode_ci,
  `new_keywords` text COLLATE utf8mb4_unicode_ci,
  `old_description` text COLLATE utf8mb4_unicode_ci,
  `new_description` text COLLATE utf8mb4_unicode_ci,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_colors`
--

DROP TABLE IF EXISTS `store_website_colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_colors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `erp_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform_id` int DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_goal_remarks`
--

DROP TABLE IF EXISTS `store_website_goal_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_goal_remarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `remark` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_website_goal_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_goals`
--

DROP TABLE IF EXISTS `store_website_goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_goals` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `goal` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `solution` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_website_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_orders`
--

DROP TABLE IF EXISTS `store_website_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website_id` int NOT NULL,
  `status_id` int NOT NULL,
  `order_id` int NOT NULL,
  `platform_order_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_website_orders_website_id_index` (`website_id`),
  KEY `store_website_orders_status_id_index` (`status_id`),
  KEY `store_website_orders_order_id_index` (`order_id`),
  KEY `store_website_orders_platform_order_id_index` (`platform_order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_page_histories`
--

DROP TABLE IF EXISTS `store_website_page_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_page_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `content` text COLLATE utf8mb4_unicode_ci,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `result` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `result_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_website_page_id` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_website_page_histories_url_index` (`url`),
  KEY `store_website_page_histories_result_type_index` (`result_type`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_pages`
--

DROP TABLE IF EXISTS `store_website_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_pages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_keywords` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `content_heading` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `layout` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` int DEFAULT '0',
  `stores` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `copy_page_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `meta_keyword_avg_monthly` longtext COLLATE utf8mb4_unicode_ci,
  `is_pushed` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_product_attributes`
--

DROP TABLE IF EXISTS `store_website_product_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_product_attributes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `discount_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `store_website_id` int unsigned NOT NULL,
  `uploaded_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stock` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_website_product_attributes_product_id_foreign` (`product_id`),
  KEY `store_website_product_attributes_store_website_id_foreign` (`store_website_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_product_checks`
--

DROP TABLE IF EXISTS `store_website_product_checks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_product_checks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website_id` int DEFAULT NULL,
  `website` longtext COLLATE utf8mb4_unicode_ci,
  `sku` longtext COLLATE utf8mb4_unicode_ci,
  `size` longtext COLLATE utf8mb4_unicode_ci,
  `brands` longtext COLLATE utf8mb4_unicode_ci,
  `dimensions` longtext COLLATE utf8mb4_unicode_ci,
  `composition` longtext COLLATE utf8mb4_unicode_ci,
  `images` longtext COLLATE utf8mb4_unicode_ci,
  `english` longtext COLLATE utf8mb4_unicode_ci,
  `arabic` longtext COLLATE utf8mb4_unicode_ci,
  `german` longtext COLLATE utf8mb4_unicode_ci,
  `spanish` longtext COLLATE utf8mb4_unicode_ci,
  `french` longtext COLLATE utf8mb4_unicode_ci,
  `italian` longtext COLLATE utf8mb4_unicode_ci,
  `japanese` longtext COLLATE utf8mb4_unicode_ci,
  `korean` longtext COLLATE utf8mb4_unicode_ci,
  `russian` longtext COLLATE utf8mb4_unicode_ci,
  `chinese` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_product_price_histories`
--

DROP TABLE IF EXISTS `store_website_product_price_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_product_price_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sw_product_prices_id` int DEFAULT NULL,
  `notes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_product_prices`
--

DROP TABLE IF EXISTS `store_website_product_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_product_prices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `default_price` int DEFAULT NULL,
  `segment_discount` int DEFAULT NULL,
  `duty_price` int DEFAULT NULL,
  `override_price` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `web_store_id` int DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_product_screenshots`
--

DROP TABLE IF EXISTS `store_website_product_screenshots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_product_screenshots` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  `store_website_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_products`
--

DROP TABLE IF EXISTS `store_website_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `store_website_id` int NOT NULL,
  `platform_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_website_products_product_id_index` (`product_id`),
  KEY `store_website_products_store_website_id_index` (`store_website_id`),
  KEY `store_website_products_platform_id_index` (`platform_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_remarks`
--

DROP TABLE IF EXISTS `store_website_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_remarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `store_website_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_sales_prices`
--

DROP TABLE IF EXISTS `store_website_sales_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_sales_prices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `amount` double(8,2) NOT NULL,
  `amount_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_seo_formats`
--

DROP TABLE IF EXISTS `store_website_seo_formats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_seo_formats` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `meta_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keyword` text COLLATE utf8mb4_unicode_ci,
  `store_website_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_sizes`
--

DROP TABLE IF EXISTS `store_website_sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_sizes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `size_id` int NOT NULL,
  `platform_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_website_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_website_sizes_size_id_index` (`size_id`),
  KEY `store_website_sizes_platform_id_index` (`platform_id`),
  KEY `store_website_sizes_store_website_id_index` (`store_website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=287 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_twilio_numbers`
--

DROP TABLE IF EXISTS `store_website_twilio_numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_twilio_numbers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int unsigned NOT NULL,
  `twilio_active_number_id` int unsigned NOT NULL,
  `twilio_credentials_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `message_available` text COLLATE utf8mb4_unicode_ci,
  `message_not_available` text COLLATE utf8mb4_unicode_ci,
  `message_busy` text COLLATE utf8mb4_unicode_ci,
  `end_work_message` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_user_history`
--

DROP TABLE IF EXISTS `store_website_user_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_user_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int DEFAULT NULL,
  `store_website_user_id` int DEFAULT NULL,
  `model` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attribute` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_website_users`
--

DROP TABLE IF EXISTS `store_website_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_website_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `website_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'production' COMMENT 'production,staging',
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deleted` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_websites`
--

DROP TABLE IF EXISTS `store_websites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_websites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `website` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `repository_id` int DEFAULT NULL,
  `cropper_color_name` varchar(191) DEFAULT NULL,
  `cropper_color` varchar(191) DEFAULT NULL,
  `is_published` int NOT NULL DEFAULT '0',
  `disable_push` int NOT NULL DEFAULT '0',
  `remote_software` varchar(191) DEFAULT NULL,
  `magento_url` varchar(191) DEFAULT NULL,
  `stage_magento_url` varchar(191) DEFAULT NULL,
  `dev_magento_url` varchar(191) DEFAULT NULL,
  `magento_username` varchar(191) DEFAULT NULL,
  `magento_password` varchar(191) DEFAULT NULL,
  `api_token` varchar(191) DEFAULT NULL,
  `stage_api_token` varchar(191) DEFAULT NULL,
  `dev_api_token` varchar(191) DEFAULT NULL,
  `instagram` varchar(191) DEFAULT NULL,
  `instagram_remarks` text,
  `facebook` varchar(191) DEFAULT NULL,
  `facebook_remarks` text,
  `country_duty` varchar(191) DEFAULT NULL,
  `is_price_override` int DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `server_ip` varchar(191) DEFAULT NULL,
  `username` varchar(191) DEFAULT NULL,
  `password` varchar(191) DEFAULT NULL,
  `staging_username` varchar(191) DEFAULT NULL,
  `staging_password` varchar(191) DEFAULT NULL,
  `mysql_username` varchar(191) DEFAULT NULL,
  `mysql_password` varchar(191) DEFAULT NULL,
  `mysql_staging_username` varchar(191) DEFAULT NULL,
  `mysql_staging_password` varchar(191) DEFAULT NULL,
  `website_source` varchar(191) NOT NULL,
  `push_web_id` varchar(191) DEFAULT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `push_web_key` text,
  `cropping_size` varchar(191) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `build_name` varchar(255) DEFAULT NULL,
  `repository` varchar(255) DEFAULT NULL,
  `semrush_project_id` varchar(191) DEFAULT NULL,
  `mailing_service_id` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `is_published` (`is_published`),
  KEY `store_websites_deleted_at_index` (`deleted_at`),
  KEY `store_websites_cropper_color_index` (`cropper_color`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_websites_country_shipping`
--

DROP TABLE IF EXISTS `store_websites_country_shipping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_websites_country_shipping` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int DEFAULT NULL,
  `country_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ship_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1178 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_wise_landing_page_products`
--

DROP TABLE IF EXISTS `store_wise_landing_page_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_wise_landing_page_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `landing_page_products_id` int unsigned NOT NULL,
  `store_website_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suggested_product_lists`
--

DROP TABLE IF EXISTS `suggested_product_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suggested_product_lists` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `suggested_products_id` int unsigned DEFAULT NULL,
  `customer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `chat_message_id` int DEFAULT NULL,
  `media_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remove_attachment` tinyint(1) NOT NULL DEFAULT '0',
  `date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suggested_product_lists_suggested_products_id_foreign` (`suggested_products_id`),
  KEY `suggested_product_lists_customer_id_index` (`customer_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `suggested_product_lists_suggested_products_id_foreign` FOREIGN KEY (`suggested_products_id`) REFERENCES `suggested_products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suggested_products`
--

DROP TABLE IF EXISTS `suggested_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suggested_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `brands` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categories` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` int NOT NULL,
  `customer_id` int NOT NULL,
  `chat_message_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'attachment',
  `platform_id` int DEFAULT NULL,
  `number` int DEFAULT '5',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suggestion_products`
--

DROP TABLE IF EXISTS `suggestion_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suggestion_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `suggestion_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suggestion_products_suggestion_id_foreign` (`suggestion_id`),
  KEY `suggestion_products_product_id_foreign` (`product_id`),
  CONSTRAINT `suggestion_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `suggestion_products_suggestion_id_foreign` FOREIGN KEY (`suggestion_id`) REFERENCES `suggestions` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=545 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suggestions`
--

DROP TABLE IF EXISTS `suggestions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suggestions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `brand` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` int NOT NULL DEFAULT '5',
  `chat_message_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suggestions_customer_id_foreign` (`customer_id`),
  CONSTRAINT `suggestions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_brand_count_histories`
--

DROP TABLE IF EXISTS `supplier_brand_count_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_brand_count_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier_brand_count_id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `cnt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` longtext COLLATE utf8mb4_unicode_ci,
  `brand_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_brand_counts`
--

DROP TABLE IF EXISTS `supplier_brand_counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_brand_counts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int NOT NULL,
  `cnt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `brand_id` int NOT NULL,
  `url` longtext COLLATE utf8mb4_unicode_ci,
  `category_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_brand_discounts`
--

DROP TABLE IF EXISTS `supplier_brand_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_brand_discounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generic_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exceptions` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition_from_retail` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition_from_retail_exceptions` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_category`
--

DROP TABLE IF EXISTS `supplier_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_category` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_category_counts`
--

DROP TABLE IF EXISTS `supplier_category_counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_category_counts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int NOT NULL,
  `category_id` int NOT NULL,
  `cnt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_category_permissions`
--

DROP TABLE IF EXISTS `supplier_category_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_category_permissions` (
  `user_id` int NOT NULL,
  `supplier_category_id` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_discount_infos`
--

DROP TABLE IF EXISTS `supplier_discount_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_discount_infos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `discount` int DEFAULT NULL,
  `fixed_price` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_discount_log_history`
--

DROP TABLE IF EXISTS `supplier_discount_log_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_discount_log_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier_brand_discounts_id` bigint unsigned NOT NULL,
  `header_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_inventory`
--

DROP TABLE IF EXISTS `supplier_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_inventory` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inventory` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=943 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_order_inquiry_datas`
--

DROP TABLE IF EXISTS `supplier_order_inquiry_datas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_order_inquiry_datas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count_number` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_order_templates`
--

DROP TABLE IF EXISTS `supplier_order_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_order_templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int DEFAULT NULL,
  `template` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_price_range`
--

DROP TABLE IF EXISTS `supplier_price_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_price_range` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `price_from` int NOT NULL,
  `price_to` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_size`
--

DROP TABLE IF EXISTS `supplier_size`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_size` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `size` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_status`
--

DROP TABLE IF EXISTS `supplier_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_subcategory`
--

DROP TABLE IF EXISTS `supplier_subcategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_subcategory` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_translate_history`
--

DROP TABLE IF EXISTS `supplier_translate_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_translate_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int DEFAULT NULL,
  `msg_id` int DEFAULT NULL,
  `original_msg` text COLLATE utf8mb4_unicode_ci,
  `translate_msg` text COLLATE utf8mb4_unicode_ci,
  `error_log` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier_category_id` int unsigned DEFAULT NULL,
  `supplier_sub_category_id` int NOT NULL,
  `supplier_status_id` int unsigned DEFAULT NULL,
  `supplier_size_id` int NOT NULL,
  `scrapper` int DEFAULT NULL,
  `size_system_id` int DEFAULT NULL,
  `supplier` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_handle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_handle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_flagged` tinyint(1) NOT NULL DEFAULT '0',
  `has_error` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `source` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  `brands` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `scraped_brands_raw` longtext COLLATE utf8mb4_unicode_ci,
  `scraped_brands` longtext COLLATE utf8mb4_unicode_ci,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_updated` tinyint(1) NOT NULL DEFAULT '1',
  `frequency` int NOT NULL DEFAULT '0',
  `reminder_message` text COLLATE utf8mb4_unicode_ci,
  `is_blocked` tinyint NOT NULL DEFAULT '0',
  `updated_by` int NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_price_range_id` int DEFAULT NULL,
  `est_delivery_time` int DEFAULT '0',
  `priority` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_supplier_unique` (`supplier`),
  KEY `suppliers_supplier_category_id_foreign` (`supplier_category_id`),
  KEY `suppliers_supplier_status_id_foreign` (`supplier_status_id`),
  KEY `suppliers_language_index` (`language`),
  KEY `deleted_at` (`deleted_at`),
  CONSTRAINT `suppliers_supplier_category_id_foreign` FOREIGN KEY (`supplier_category_id`) REFERENCES `supplier_category` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `suppliers_supplier_status_id_foreign` FOREIGN KEY (`supplier_status_id`) REFERENCES `supplier_status` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4663 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_size_managers`
--

DROP TABLE IF EXISTS `system_size_managers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_size_managers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `erp_size` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0-Deleted, 1-Not deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_size_relations`
--

DROP TABLE IF EXISTS `system_size_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_size_relations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `system_size_manager_id` int NOT NULL,
  `system_size` int NOT NULL,
  `size` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_sizes`
--

DROP TABLE IF EXISTS `system_sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_sizes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0-Deleted, 1-Not deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `table 159`
--

DROP TABLE IF EXISTS `table 159`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `table 159` (
  `COL 1` varchar(50) DEFAULT NULL,
  `COL 2` varchar(6) DEFAULT NULL,
  `COL 3` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `target_locations`
--

DROP TABLE IF EXISTS `target_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `target_locations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `targeted_accounts`
--

DROP TABLE IF EXISTS `targeted_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `targeted_accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_attachments`
--

DROP TABLE IF EXISTS `task_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_attachments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_categories`
--

DROP TABLE IF EXISTS `task_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int unsigned NOT NULL DEFAULT '0',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_approved` int NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_statuses`
--

DROP TABLE IF EXISTS `task_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_types`
--

DROP TABLE IF EXISTS `task_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_user_histories`
--

DROP TABLE IF EXISTS `task_user_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_user_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` int NOT NULL,
  `old_id` int NOT NULL,
  `new_id` int NOT NULL,
  `user_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `master_user_hubstaff_task_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_users`
--

DROP TABLE IF EXISTS `task_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `task_users_task_id_foreign` (`task_id`),
  KEY `task_users_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9518 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` int DEFAULT NULL,
  `assign_from` int NOT NULL,
  `assign_to` int NOT NULL,
  `status` int DEFAULT '3',
  `assign_status` int DEFAULT NULL,
  `is_statutory` int NOT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `is_watched` tinyint(1) NOT NULL DEFAULT '0',
  `is_flagged` tinyint(1) NOT NULL DEFAULT '0',
  `task_details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_subject` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `completion_date` timestamp NULL DEFAULT NULL,
  `remark` text CHARACTER SET utf8,
  `actual_start_date` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `is_completed` timestamp NULL DEFAULT NULL,
  `general_category_id` int DEFAULT NULL,
  `is_verified` datetime DEFAULT NULL,
  `sending_time` datetime DEFAULT NULL,
  `time_slot` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `planned_at` date DEFAULT NULL,
  `pending_for` int NOT NULL DEFAULT '0',
  `recurring_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statutory_id` int DEFAULT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approximate` int NOT NULL DEFAULT '0',
  `hubstaff_task_id` int unsigned NOT NULL,
  `cost` decimal(8,2) DEFAULT NULL,
  `is_milestone` tinyint(1) NOT NULL DEFAULT '0',
  `no_of_milestone` int DEFAULT NULL,
  `milestone_completed` int DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `master_user_id` int DEFAULT NULL,
  `lead_hubstaff_task_id` int DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `site_developement_id` int DEFAULT NULL,
  `priority_no` int DEFAULT NULL,
  `frequency` int NOT NULL DEFAULT '0',
  `last_send_reminder` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reminder_last_reply` int NOT NULL DEFAULT '1',
  `reminder_from` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reminder_message` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `tasks_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=10641 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tasks_history`
--

DROP TABLE IF EXISTS `tasks_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `date_time` datetime NOT NULL,
  `task_id` int NOT NULL,
  `user_id` int NOT NULL,
  `old_assignee` int NOT NULL,
  `new_assignee` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `team_user`
--

DROP TABLE IF EXISTS `team_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_user` (
  `team_id` int NOT NULL,
  `user_id` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teams` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teams_user_id_index` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `template_modifications`
--

DROP TABLE IF EXISTS `template_modifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `template_modifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `template_id` int unsigned NOT NULL,
  `row_index` int DEFAULT NULL,
  `tag` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `template_modifications_template_id_foreign` (`template_id`),
  CONSTRAINT `template_modifications_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `no_of_images` int NOT NULL DEFAULT '0',
  `auto_generate_product` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `temporary_category_updations`
--

DROP TABLE IF EXISTS `temporary_category_updations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `temporary_category_updations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attribute_id` int DEFAULT NULL,
  `need_to_skip` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_statuses`
--

DROP TABLE IF EXISTS `ticket_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ticket_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `assigned_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_of_ticket` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'live_chat',
  `status_id` int NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type_of_inquiry` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notify_on` enum('email','phone') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` double(8,2) DEFAULT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keyword` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `lang_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trafficanalitics_reports`
--

DROP TABLE IF EXISTS `trafficanalitics_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trafficanalitics_reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `tool_id` int NOT NULL,
  `database` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `traffic_summary` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `traffic_sources` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `traffic_destinations` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `geo_distribution` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subdomains` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `top_pages` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain_rankings` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `audience_insights` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_accuracy` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `translated_products`
--

DROP TABLE IF EXISTS `translated_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `translated_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `language_id` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `name` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `translation_languages`
--

DROP TABLE IF EXISTS `translation_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `translation_languages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `translation_languages_locale_unique` (`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `translations`
--

DROP TABLE IF EXISTS `translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `translations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `text_original` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `from` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=596 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twilio_active_numbers`
--

DROP TABLE IF EXISTS `twilio_active_numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `twilio_active_numbers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_sid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `friendly_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `workspace_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voice_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_updated` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sms_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `voice_receive_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_version` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `voice_application_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms_application_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trunk_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_address_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identity_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bundle_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uri` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `twilio_credential_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twilio_activities`
--

DROP TABLE IF EXISTS `twilio_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `twilio_activities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `twilio_credential_id` int DEFAULT NULL,
  `twilio_workspace_id` int DEFAULT NULL,
  `activity_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `availability` tinyint(1) DEFAULT '0',
  `activity_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twilio_agents`
--

DROP TABLE IF EXISTS `twilio_agents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `twilio_agents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twilio_call_data`
--

DROP TABLE IF EXISTS `twilio_call_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `twilio_call_data` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `call_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aget_user_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `call_data` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twilio_call_forwarding`
--

DROP TABLE IF EXISTS `twilio_call_forwarding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `twilio_call_forwarding` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `twilio_number_sid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `twilio_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `forwarding_on` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `twilio_active_number_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twilio_call_waitings`
--

DROP TABLE IF EXISTS `twilio_call_waitings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `twilio_call_waitings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `call_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twilio_credentials`
--

DROP TABLE IF EXISTS `twilio_credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `twilio_credentials` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `twilio_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `twiml_app_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twilio_key_options`
--

DROP TABLE IF EXISTS `twilio_key_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `twilio_key_options` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key` int DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `message` text COLLATE utf8mb4_unicode_ci,
  `website_store_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twilio_sitewise_times`
--

DROP TABLE IF EXISTS `twilio_sitewise_times`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `twilio_sitewise_times` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int DEFAULT NULL,
  `start_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twilio_task_queue`
--

DROP TABLE IF EXISTS `twilio_task_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `twilio_task_queue` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `twilio_credential_id` int DEFAULT NULL,
  `twilio_workspace_id` int DEFAULT NULL,
  `task_queue_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `task_order` enum('FIFO','LIFO') COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_queue_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reservation_activity_id` int DEFAULT NULL,
  `assignment_activity_id` int DEFAULT NULL,
  `max_reserved_workers` int DEFAULT NULL,
  `target_workers` text COLLATE utf8mb4_unicode_ci,
  `deleted` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twilio_workers`
--

DROP TABLE IF EXISTS `twilio_workers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `twilio_workers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `twilio_credential_id` int DEFAULT NULL,
  `twilio_workspace_id` int DEFAULT NULL,
  `worker_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `worker_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `worker_phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twilio_workflows`
--

DROP TABLE IF EXISTS `twilio_workflows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `twilio_workflows` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `twilio_credential_id` int DEFAULT NULL,
  `twilio_workspace_id` int DEFAULT NULL,
  `workflow_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `workflow_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fallback_assignment_callback_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assignment_callback_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twilio_workspaces`
--

DROP TABLE IF EXISTS `twilio_workspaces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `twilio_workspaces` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `twilio_credential_id` int DEFAULT NULL,
  `workspace_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `workspace_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `workspace_response` text COLLATE utf8mb4_unicode_ci,
  `deleted` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `callback_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unknown_sizes`
--

DROP TABLE IF EXISTS `unknown_sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unknown_sizes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `size` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `url_reports`
--

DROP TABLE IF EXISTS `url_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `url_reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `tool_id` int NOT NULL,
  `database` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_organic_search_keywords` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_paid_search_keywords` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_actions`
--

DROP TABLE IF EXISTS `user_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_actions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=148 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_avaibilities`
--

DROP TABLE IF EXISTS `user_avaibilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_avaibilities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `date` date NOT NULL,
  `from` decimal(4,2) DEFAULT NULL,
  `to` decimal(4,2) DEFAULT NULL,
  `day` decimal(4,2) NOT NULL DEFAULT '0.00',
  `minute` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL,
  `note` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_bank_informations`
--

DROP TABLE IF EXISTS `user_bank_informations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_bank_informations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ifsc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_customers`
--

DROP TABLE IF EXISTS `user_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_customers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_database_tables`
--

DROP TABLE IF EXISTS `user_database_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_database_tables` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_database_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_database_tables_user_database_id_index` (`user_database_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_databases`
--

DROP TABLE IF EXISTS `user_databases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_databases` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `database` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_databases_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_event_attendees`
--

DROP TABLE IF EXISTS `user_event_attendees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_event_attendees` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_event_id` int NOT NULL,
  `contact` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `suggested_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_event_participants`
--

DROP TABLE IF EXISTS `user_event_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_event_participants` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `object` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `object_id` int NOT NULL,
  `user_event_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_events`
--

DROP TABLE IF EXISTS `user_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `daily_activity_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_feedback_categories`
--

DROP TABLE IF EXISTS `user_feedback_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_feedback_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_feedback_status_updates`
--

DROP TABLE IF EXISTS `user_feedback_status_updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_feedback_status_updates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `user_feedback_status_id` int DEFAULT NULL,
  `user_feedback_category_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_feedback_statuses`
--

DROP TABLE IF EXISTS `user_feedback_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_feedback_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_login_ips`
--

DROP TABLE IF EXISTS `user_login_ips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_login_ips` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_logins`
--

DROP TABLE IF EXISTS `user_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_logins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `login_at` timestamp NULL DEFAULT NULL,
  `logout_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_logs`
--

DROP TABLE IF EXISTS `user_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35948 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_manual_crop`
--

DROP TABLE IF EXISTS `user_manual_crop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_manual_crop` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_manual_crop_user_id_foreign` (`user_id`),
  KEY `user_manual_crop_product_id_foreign` (`product_id`),
  CONSTRAINT `user_manual_crop_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `user_manual_crop_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_pemfile_history`
--

DROP TABLE IF EXISTS `user_pemfile_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_pemfile_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `server_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'add,delete',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extra` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_product_feedbacks`
--

DROP TABLE IF EXISTS `user_product_feedbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_product_feedbacks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `senior_user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_products`
--

DROP TABLE IF EXISTS `user_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_products_user_id_foreign` (`user_id`),
  KEY `user_products_product_id_foreign` (`product_id`),
  CONSTRAINT `user_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `user_products_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_rates`
--

DROP TABLE IF EXISTS `user_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_rates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `start_date` datetime NOT NULL,
  `hourly_rate` double DEFAULT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=329 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_system_ip`
--

DROP TABLE IF EXISTS `user_system_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_system_ip` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `index_txt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `other_user_name` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_updated_attribute_histories`
--

DROP TABLE IF EXISTS `user_updated_attribute_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_updated_attribute_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `old_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attribute_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'compositions',
  `attribute_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_updated_attribute_histories_attribute_name_index` (`attribute_name`),
  KEY `user_updated_attribute_histories_attribute_id_index` (`attribute_id`),
  KEY `user_updated_attribute_histories_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=410 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `responsible_user` int unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_token_hubstaff` text COLLATE utf8mb4_unicode_ci,
  `refresh_token_hubstaff` text COLLATE utf8mb4_unicode_ci,
  `is_auto_approval` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `last_checked` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `agent_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount_assigned` int unsigned DEFAULT NULL,
  `is_planner_completed` tinyint(1) NOT NULL DEFAULT '1',
  `crop_approval_rate` decimal(8,2) NOT NULL,
  `crop_rejection_rate` decimal(8,2) NOT NULL,
  `listing_approval_rate` decimal(8,2) DEFAULT NULL,
  `listing_rejection_rate` decimal(8,2) DEFAULT NULL,
  `department_id` int NOT NULL,
  `fixed_price_user_or_job` int NOT NULL COMMENT '1. Fixed price user, 2. Fixed price job',
  `payment_frequency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'fornightly,weekly,biweekly,monthly',
  `billing_frequency_day` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hourly_rate` decimal(8,2) DEFAULT NULL,
  `approve_login` date DEFAULT NULL,
  `user_timeout` int DEFAULT NULL,
  `mail_notification` tinyint(1) NOT NULL DEFAULT '0',
  `last_mail_sent_payment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_whitelisted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=385 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_auto_comment_histories`
--

DROP TABLE IF EXISTS `users_auto_comment_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_auto_comment_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `auto_comment_history_id` int unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `is_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendor_categories`
--

DROP TABLE IF EXISTS `vendor_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendor_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendor_category_permission`
--

DROP TABLE IF EXISTS `vendor_category_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendor_category_permission` (
  `user_id` int NOT NULL,
  `vendor_category_id` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendor_payments`
--

DROP TABLE IF EXISTS `vendor_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendor_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int unsigned NOT NULL,
  `currency` int NOT NULL DEFAULT '0',
  `payment_date` date DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `payable_amount` decimal(13,4) DEFAULT NULL,
  `paid_amount` decimal(13,4) DEFAULT NULL,
  `service_provided` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_hour` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `other` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vendor_payments_vendor_id_foreign` (`vendor_id`),
  KEY `vendor_payments_status_index` (`status`),
  CONSTRAINT `vendor_payments_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendor_products`
--

DROP TABLE IF EXISTS `vendor_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendor_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int unsigned NOT NULL,
  `date_of_order` datetime NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int unsigned NOT NULL DEFAULT '0',
  `price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `payment_terms` text COLLATE utf8mb4_unicode_ci,
  `delivery_date` datetime DEFAULT NULL,
  `received_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_details` text COLLATE utf8mb4_unicode_ci,
  `recurring_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vendor_products_vendor_id_foreign` (`vendor_id`),
  CONSTRAINT `vendor_products_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendors`
--

DROP TABLE IF EXISTS `vendors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_handle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_iban` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_swift` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `frequency` int NOT NULL DEFAULT '0',
  `reminder_last_reply` int NOT NULL DEFAULT '1',
  `reminder_from` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reminder_message` text COLLATE utf8mb4_unicode_ci,
  `has_error` tinyint NOT NULL DEFAULT '0',
  `is_blocked` tinyint NOT NULL DEFAULT '0',
  `updated_by` int NOT NULL,
  `status` int DEFAULT '1',
  `frequency_of_payment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ifsc_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` text COLLATE utf8mb4_unicode_ci,
  `chat_session_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vendors_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `views`
--

DROP TABLE IF EXISTS `views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `views` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `viewable_id` int NOT NULL,
  `viewable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `views` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `views_user_id_index` (`user_id`),
  KEY `views_viewable_id_index` (`viewable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visitor_logs`
--

DROP TABLE IF EXISTS `visitor_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visitor_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `browser` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visits` int NOT NULL DEFAULT '1',
  `last_visit` datetime NOT NULL,
  `page_current` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chats` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `voucher_categories`
--

DROP TABLE IF EXISTS `voucher_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voucher_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int unsigned NOT NULL DEFAULT '0',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vouchers`
--

DROP TABLE IF EXISTS `vouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vouchers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `delivery_approval_id` int DEFAULT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `travel_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` datetime NOT NULL,
  `approved` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reject_reason` text COLLATE utf8mb4_unicode_ci,
  `reject_count` tinyint NOT NULL DEFAULT '0',
  `resubmit_count` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `vouchers_delivery_approval_id_foreign` (`delivery_approval_id`),
  CONSTRAINT `vouchers_delivery_approval_id_foreign` FOREIGN KEY (`delivery_approval_id`) REFERENCES `delivery_approvals` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `watson_accounts`
--

DROP TABLE IF EXISTS `watson_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `watson_accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `api_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `work_space_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assistant_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `watson_push` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `watson_workspace`
--

DROP TABLE IF EXISTS `watson_workspace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `watson_workspace` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `element_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `watson_account_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `waybill_invoices`
--

DROP TABLE IF EXISTS `waybill_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `waybill_invoices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `line_type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_source` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_identifier` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_date` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_amount` decimal(10,0) DEFAULT '0',
  `invoice_currency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_terms` text COLLATE utf8mb4_unicode_ci,
  `due_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_account` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_account` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_account_name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_account_name_additional` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_address_1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_postcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_state_province` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_country_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_contact` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipment_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipment_date` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pieces` int DEFAULT NULL,
  `origin` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orig_name` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orig_country_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orig_country_name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senders_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senders_city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `waybill_track_histories`
--

DROP TABLE IF EXISTS `waybill_track_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `waybill_track_histories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `waybill_id` int NOT NULL,
  `comment` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dat` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `waybills`
--

DROP TABLE IF EXISTS `waybills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `waybills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `awb` varchar(255) NOT NULL,
  `from_customer_id` varchar(191) DEFAULT NULL,
  `from_customer_name` varchar(191) DEFAULT NULL,
  `from_city` varchar(191) DEFAULT NULL,
  `from_country_code` varchar(191) DEFAULT NULL,
  `from_customer_phone` varchar(191) DEFAULT NULL,
  `from_customer_address_1` varchar(191) DEFAULT NULL,
  `from_customer_address_2` varchar(191) DEFAULT NULL,
  `from_customer_pincode` varchar(191) DEFAULT NULL,
  `from_company_name` varchar(191) DEFAULT NULL,
  `to_customer_id` varchar(191) DEFAULT NULL,
  `to_customer_name` varchar(191) DEFAULT NULL,
  `to_city` varchar(191) DEFAULT NULL,
  `to_country_code` varchar(191) DEFAULT NULL,
  `to_customer_phone` varchar(191) DEFAULT NULL,
  `to_customer_address_1` varchar(191) DEFAULT NULL,
  `to_customer_address_2` varchar(191) DEFAULT NULL,
  `to_customer_pincode` varchar(191) DEFAULT NULL,
  `to_company_name` varchar(191) DEFAULT NULL,
  `box_length` double(8,2) NOT NULL,
  `box_width` double(8,2) NOT NULL,
  `box_height` double(8,2) NOT NULL,
  `actual_weight` double(8,2) NOT NULL,
  `volume_weight` double(8,2) DEFAULT NULL,
  `cost_of_shipment` varchar(191) DEFAULT NULL,
  `duty_cost` varchar(191) DEFAULT NULL,
  `package_slip` varchar(255) NOT NULL,
  `pickup_date` timestamp NULL DEFAULT NULL,
  `pickuprequest` int NOT NULL DEFAULT '0',
  `paid_date` timestamp NULL DEFAULT NULL,
  `payment_mode` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webhook_notifications`
--

DROP TABLE IF EXISTS `webhook_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `webhook_notifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` enum('get','post','put') COLLATE utf8mb4_unicode_ci DEFAULT 'post',
  `payload` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'application/json',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_product_csvs`
--

DROP TABLE IF EXISTS `website_product_csvs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `website_product_csvs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `store_website_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_products`
--

DROP TABLE IF EXISTS `website_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `website_products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `store_website_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_store_view_value`
--

DROP TABLE IF EXISTS `website_store_view_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `website_store_view_value` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` int NOT NULL,
  `store_view_id` int NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9330 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_store_views`
--

DROP TABLE IF EXISTS `website_store_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `website_store_views` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int DEFAULT '0',
  `sort_order` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `platform_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_store_id` int DEFAULT NULL,
  `store_group_id` int DEFAULT NULL,
  `ref_theme_group_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `site_submit_webmaster` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `website_store_views_name_index` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=978 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_store_views_webmaster_history`
--

DROP TABLE IF EXISTS `website_store_views_webmaster_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `website_store_views_webmaster_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `website_store_views_id` int DEFAULT NULL,
  `log` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_stores`
--

DROP TABLE IF EXISTS `website_stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `website_stores` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `root_category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_id` int DEFAULT NULL,
  `is_default` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `website_stores_name_index` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=754 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `websites`
--

DROP TABLE IF EXISTS `websites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `websites` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `countries` text COLLATE utf8mb4_unicode_ci,
  `store_website_id` int DEFAULT NULL,
  `is_finished` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_price_ovveride` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `websites_name_index` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=757 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wetransfers`
--

DROP TABLE IF EXISTS `wetransfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wetransfers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_processed` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whats_app_group_numbers`
--

DROP TABLE IF EXISTS `whats_app_group_numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `whats_app_group_numbers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `group_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whats_app_groups`
--

DROP TABLE IF EXISTS `whats_app_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `whats_app_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int NOT NULL,
  `group_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `whats_app_groups_group_id_index` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_configs`
--

DROP TABLE IF EXISTS `whatsapp_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `whatsapp_configs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_customer_support` int NOT NULL,
  `frequency` int NOT NULL,
  `last_online` datetime DEFAULT NULL,
  `is_connected` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `send_start` int NOT NULL,
  `send_end` int NOT NULL,
  `device_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `simcard_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `simcard_owner` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sim_card_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recharge_date` date DEFAULT NULL,
  `instance_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` int DEFAULT '0',
  `status` int NOT NULL DEFAULT '0',
  `store_website_id` int DEFAULT NULL,
  `is_use_own` int NOT NULL DEFAULT '0',
  `default_for` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zoom_meetings`
--

DROP TABLE IF EXISTS `zoom_meetings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `zoom_meetings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `meeting_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_topic` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meeting_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meeting_agenda` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `join_meeting_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_meeting_url` text COLLATE utf8mb4_unicode_ci,
  `start_date_time` datetime NOT NULL,
  `meeting_duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `host_zoom_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `user_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoom_recording` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_deleted_from_zoom` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1882, '2021_10_13_185438_add_parent_id_to_reply_categories_table', 955),
(1881, '2021_10_12_105006_alter_flow_paths_add_path_for', 954),
(1880, '2021_10_11_161713_alter_flow_paths_add_parent_action_id', 954),
(1879, '2021_10_11_123942_alter_flow_actions_add_parent_action_id_condition', 954),
(1878, '2021_10_11_182525_create_email_assignes_table', 953),
(1877, '2021_10_10_105211_add_type_to_erp_leads_table', 952),
(1876, '2021_10_08_155515_add_paid_to_store_views_gt_metrix_table', 951),
(1875, '2021_10_07_163150_alter_flow_actions_add_message_title', 951),
(1874, '2021_10_07_160224_alter_chat_messages_add_scheduled_at', 951),
(1873, '2021_10_07_142854_alter_flow_action_messages_html_content_nullable', 950),
(1872, '2021_10_07_114453_alter_flow_action_messages_add_mail_tpl', 950),
(1871, '2021_10_05_153149_scrapper_table_add_assigned_to', 949),
(1870, '2021_10_05_112206_add_schedule_at_in_emails_table', 948),
(1869, '2021_09_30_113416_create_erp_leads_brands_table', 947),
(1868, '2021_09_30_113314_create_erp_leads_categories_table', 947),
(1867, '2021_09_29_155127_create_sms_service_table', 946),
(1866, '2021_09_29_075802_create_marketing_message_customers', 946),
(1865, '2021_09_29_075030_create_marketing_messages', 946),
(1864, '2021_09_29_074628_create_messaging_customers', 946),
(1863, '2021_09_29_074154_create_messaging_groups', 946),
(1862, '2021_09_30_110323_alter_table_suggested_product_table_index', 945),
(1861, '2021_09_08_124420_add_mailing_service_id_to_store_websites_table', 944),
(1860, '2021_09_23_093712_flow_action_messages', 943),
(1859, '2021_09_23_093659_flow_actions', 943),
(1858, '2021_09_23_093650_flow_paths', 943),
(1857, '2021_09_23_093605_flows', 943),
(1856, '2021_09_23_062305_create_flow_types_table', 943),
(1855, '2021_09_22_145652_create_sms_group_table', 942),
(1854, '2021_09_24_695337_create_table_scraped_product_missing_log', 941),
(1853, '2021_09_22_245108_alter_table_mailinglist_templates_add_from_email', 940),
(1852, '2021_09_17_162512_alter_twilio_worker_add_worker_phone', 939),
(1851, '2021_09_17_161246_alter_twilio_workspace_add_callback_url', 939),
(1850, '2021_09_22_121848_create_credit_logs_table', 938),
(1849, '2021_09_21_134151_add_log_keyword_id_in_to_developer_tasks_table', 937),
(1848, '2021_09_21_145108_alter_table_magento_settings_add_status', 936),
(1847, '2021_09_21_145108_alter_table_magento_setting_push_logs_add_status', 936),
(1846, '2021_09_13_142810_add_user_id_to_rejected_images_table', 935),
(1845, '2021_09_20_145108_alter_table_twillio_number_workspace_id', 934),
(1844, '2021_09_17_083532_create_product_Update_logs_table', 933),
(1843, '2021_09_16_134644_create-twilio-task-queue', 932),
(1842, '2021_09_16_121527_create_twilio_workflow_table', 932),
(1841, '2021_09_16_090420_create_twilio_activities_table', 932),
(1840, '2021_09_16_173318_alter_chatbot_message_log_index', 931),
(1839, '2021_09_13_172418_create_chatbot_message_log_responses_table', 930),
(1838, '2021_09_13_172400_create_chatbot_message_logs_table', 930),
(1837, '2021_09_14_142754_alter_customer_add_platform_id', 929),
(1836, '2021_09_13_141059_alter_magento_settings_push_logs_table', 928),
(1835, '2021_09_13_105553_create_magento_cron_datas_table', 928),
(1834, '2021_09_14_105208_alter_site_development_category', 927),
(1833, '2021_09_13_530946_alter_chat_messages_table_add_field', 926),
(1832, '2021_09_13_111015_create_table_build_process_history', 925),
(1831, '2021_09_10_142226_alter_table_chat_message_add_index_charity_id', 924),
(1830, '2021_09_09_144856_create_magento_setting_push_log_table', 923),
(1829, '2021_09_09_121220_alter_store_website_add_repository_id', 923),
(1828, '2021_09_09_370318_alter_table_chatmessages_add_message_en', 922),
(1827, '2021_09_09_270318_alter_table_emails_add_message_en', 922),
(1826, '2021_09_09_103920_create_table_customer_charity_website_stores', 921),
(1825, '2021_09_07_100024_add_columns_to_store_websites', 920),
(1824, '2021_09_08_180525_alter_table_user_whitelisted', 919),
(1823, '2021_09_03_140147_add_language_to_mailinglists_table', 918),
(1822, '2021_09_06_133749_create_twilio_workers_table', 917),
(1821, '2021_09_06_105355_create_twilio_workspaces_table', 917),
(1820, '2021_09_07_161056_erp', 916),
(1819, '2021_09_06_180217_alter_site_development_add_master_category_id', 915),
(1818, '2021_09_06_164439_create_site_development_master_category', 915),
(1817, '2021_09_06_164301_create_task_master_category', 915),
(1816, '2021_09_06_073503_create_mailing_template_files_histories_table', 914),
(1815, '2021_09_03_170318_alter_table_tickets_add_lang_code', 913),
(1814, '2021_09_06_063503_create_store_website_sales_price_table', 912),
(1813, '2021_09_06_091801_alter_competitors_add_domain', 911),
(1812, '2021_09_06_091639_alter_backlink_domain_add_subtype', 911),
(1811, '2021_08_16_095433_create_seo_tools_table', 910),
(1810, '2021_08_31_104557_create_seo_keyword_ideas_table', 909),
(1809, '2021_09_03_270318_alter_table_store_website_pages_add_is_pushed', 908),
(1808, '2021_09_02_126858_add_user_id_to_media_table', 907),
(1807, '2021_09_02_170318_alter_table_learnings_add_currency', 906),
(1806, '2021_08_30_105657_create_project_keyword_table', 905),
(1805, '2021_08_30_105634_create_semrush_keyword_table', 905),
(1804, '2021_08_30_105615_create_semrush_tag_table', 905),
(1803, '2021_08_30_105555_create_keyword_tag_table', 905),
(1802, '2021_08_30_064634_create_site_audits_table', 905),
(1801, '2021_08_30_063503_create_site_issues_table', 905),
(1800, '2021_08_27_102040_create_domain_landing_pages_table', 905),
(1799, '2021_08_27_101117_create_backlink_indexed_pages_table', 905),
(1798, '2021_08_27_095337_create_domain_organic_page_table', 905),
(1797, '2021_08_26_081330_create_backlink_overview_table', 905),
(1796, '2021_08_25_163842_create_back_link_anchors_table', 905),
(1795, '2021_08_25_163812_create_back_link_domains_table', 905),
(1794, '2021_08_25_053202_create_competitors_table', 905),
(1793, '2021_08_23_101601_alter_store_websites_add_semrush_project_id', 905),
(1792, '2021_08_20_105702_create_domain_overviews_table', 905),
(1791, '2021_08_20_075716_create_domain_search_keywords_table', 905),
(1790, '2021_08_19_064539_create_reffring_domains_table', 905),
(1789, '2021_08_19_054207_create_url_reports_table', 905),
(1788, '2021_08_19_050650_create_trafficanalitics_reports_table', 905),
(1787, '2021_08_19_050645_create_display_advertising_reports_table', 905),
(1786, '2021_08_19_050641_create_backlinks_table', 905),
(1785, '2021_08_19_050618_create_keyword_reports_table', 905),
(1784, '2021_08_19_050137_create_domain_reports_table', 905),
(1783, '2021_09_02_110512_alter_table_customer_charity_store_website', 904),
(1782, '2021_09_02_100307_alter_table_magento_settings_created_by', 903),
(1781, '2021_09_01_333359_create_magento_setting_name_log_table', 903),
(1780, '2021_08_27_140347_add_insight_column_store_views_gt_metrix_table', 902),
(1779, '2021_08_27_170318_alter_table_marketing_template_category', 901),
(1778, '2021_08_27_133044_alter_table_magento_settings_table', 900),
(1777, '2021_08_27_034250_magento_setting_logs', 899),
(1776, '2021_08_22_224412_create_charity_product_store_websites_table', 898),
(1775, '2021_08_26_091618_alter_out_of_stock_subscribe_table', 897),
(1774, '2021_08_25_134001_add_account_id_to_store_views_gt_metrix', 897),
(1773, '2021_08_23_160607_create_store_gt_metrix_account', 897),
(1772, '2021_08_24_100842_create_out_of_stock_subscribes_table', 896),
(1771, '2021_08_24_145137_alter_key_wise_message_table', 895),
(1770, '2021_08_20_125843_create_twilio_key_options_table', 895),
(1769, '2021_08_20_094404_create_twilio_call_waiting_table', 895),
(1768, '2021_08_23_180131_alter_remove_unique_phone_customer_table_table', 894),
(1767, '2021_08_23_154055_add_dev_stage_fields_to_store_websites_table', 893),
(1766, '2021_08_21_080619_add_store_website_to_customer_charity_table', 893),
(1765, '2021_08_21_333359_create_simply_duty_country_history_table', 892),
(1764, '2021_08_21_223259_create_store_website_product_prices_histroy_table', 892),
(1763, '2021_08_21_222259_create_store_website_product_prices_table', 891),
(1762, '2021_08_21_182457_alter_simply_duty_countries_add_field_status_table', 890),
(1761, '2021_08_21_182457_alter_brand_add_field_status_table', 889),
(1760, '2019_04_08_000000_create_sendgrid_events', 888),
(1759, '2021_08_19_103834_alter_add_column_end_work_message_table', 887),
(1758, '2021_08_19_105210_alter_table_log_list_magento_add_translation_enum', 886),
(1757, '2021_08_19_082457_alter_store_website_twilio_numbers_add_field_table', 885),
(1756, '2021_08_18_182055_add_magento_value_to_charity_brand', 885),
(1755, '2021_08_18_170821_alter_twilio_agent_table', 885),
(1754, '2021_08_18_161223_create_twilio_sitewise_times_table', 885),
(1753, '2021_08_18_154029_add_dev_api_token_to_store_websites_table', 885),
(1752, '2021_08_17_133113_create_email_events_table', 884),
(1751, '2021_08_16_151420_alter_mailing_list__table', 883),
(1750, '2021_08_14_200927_create_campaign_events', 883),
(1749, '2021_08_13_145711_add_duration_mailinglist_template', 883),
(1748, '2021_08_16_280209_create_magento_settings_table', 882),
(1747, '2021_08_17_171102_create_twilio_call_data_table', 881),
(1746, '2021_08_17_153514_create_twilio_agent_table', 881),
(1745, '2021_08_17_311711_alter_table_chat_message_add_email_id', 880),
(1744, '2021_08_17_211711_alter_table_email_address_add_signature_field', 879),
(1743, '2021_08_17_100359_create_user_feedback_status_updates_table', 878),
(1742, '2021_08_16_170907_create_store_reindex_history_table', 877),
(1741, '2021_08_16_110637_create_charity_country_table', 876),
(1740, '2021_08_16_273711_alter_table_simple_duty_coutry_add_segment', 875),
(1739, '2021_08_16_130724_alter_order_status_histories_null_table', 874),
(1738, '2021_08_16_221412_create_simple_duty_segment_table', 873),
(1737, '2021_08_14_221251_clear_google_client_notifications_table', 872),
(1736, '2021_08_14_220412_create_api_response_message_value_histories_table', 872),
(1735, '2021_08_13_111410_alter_table_truncate_three_table_20148', 872),
(1734, '2021_08_12_173801_alter_truncate_table_customer_live_chats_table', 872),
(1733, '2021_08_12_165406_add_column_to_call_busy_messages_table', 872),
(1732, '2021_08_12_164942_alter_table_google_scrapper_content_column_change', 872),
(1731, '2021_08_12_164733_create_google_client_account_mails_table', 872),
(1730, '2021_08_12_160506_audio_text_to_call_busy_messages_table', 872),
(1729, '2021_08_11_164855_create_order_misscall_send_message_histories_table', 872),
(1728, '2021_08_11_080643_add_status_id_to_call_busy_messages_table', 872),
(1727, '2021_08_11_074923_create_call_busy_message_statuses_table', 872),
(1726, '2021_08_13_173711_alter_table_log_list_magento_request', 871),
(1725, '2021_08_11_155423_alter_manually_command_table', 870),
(1724, '2021_08_11_152319_add_product_id_to_customer_charities_table', 870),
(1723, '2021_08_11_095157_add_read_status_to_scrap_influencers_table', 869),
(1722, '2021_08_11_112534_create_inventory_histories_table', 868),
(1721, '2021_08_11_122243_create_table_google_scrapper_content', 867),
(1720, '2021_08_11_121225_create_table_google_scrapper_keywords', 867),
(1719, '2021_08_10_145701_add_charity_to_tables', 867),
(1718, '2021_08_11_110616_alter_table_product_cron_setup_fix', 866),
(1717, '2021_08_10_143150_create_payment_mail_data_table', 865),
(1716, '2021_08_10_091942_add__is_available_to_product_push_information_histories_table', 865),
(1715, '2021_08_09_161931_add_index_website_product_csvs_table', 865),
(1714, '2021_08_06_142957_add_primary_keys_to_tables', 865),
(1713, '2021_08_05_154125_create_google_client_notifications_table', 865),
(1712, '2021_08_04_141703_create_google_clint_accounts_table', 865),
(1711, '2021_08_10_094055_index_column_store_websites_table_', 864),
(1710, '2021_08_10_085157_add_charity_id_to_chat_messages_table', 863),
(1709, '2021_08_09_135121_create_customer_charities_table', 863),
(1708, '2021_08_06_111031_create_product_push_information_summeries_table', 862),
(1707, '2021_08_05_131031_add_is_added_from_csv_to_product_push_information_history_table', 862),
(1706, '2021_08_05_125257_add_is_added_from_csv_to_product_push_information_table', 862),
(1705, '2021_08_05_112623_create_google_analytic_datas_table', 862),
(1704, '2021_08_04_172233_index_column_google_analytics_page_tracking_table', 862),
(1703, '2021_08_04_160909_index_column_google_google_analytics_user_table', 862),
(1702, '2021_08_04_160904_index_column_google_google_analytics_geo_network_table', 862),
(1701, '2021_08_04_160857_index_column_google_google_analytics_platform_device_table', 862),
(1700, '2021_08_04_155123_index_column_google_analytics_audience_table', 862),
(1699, '2021_08_07_120837_alter_table_store_view_gt_matrix_created_updated_id_changes', 861),
(1698, '2021_08_06_151037_crop_image_http_request_responses_4534', 860),
(1697, '2021_08_06_142758_create_crop_image_get_data_request', 860),
(1696, '2021_08_06_143718_alter_table_store_view_gt_matrix_pagespeedyslowjson', 859),
(1695, '2021_08_06_114535_alter_table_store_view_gt_matrix_resources', 859),
(1694, '2021_08_06_121421_alter_table_suggested_product_extra_field_attach', 858),
(1693, '2021_08_06_100727_create_crop_image_http_request_response_table', 857),
(1692, '2021_08_05_092146_alter_table_product_discount_excel_files_truncate', 856),
(1691, '2021_08_04_160257_add_website_storage_id_to_product_push_information_history_table', 856),
(1690, '2021_07_21_123531_add_field_to_website_store_views_table_ref_theme', 855),
(1689, '2021_07_09_160606_add_column_chat_messages_table_user_feedback', 854),
(1688, '2021_08_03_190325_add_field_to_group_routes_table', 853),
(1687, '2021_08_03_185934_delete_old_routes', 853),
(1686, '2021_08_03_152007_create_excel_impoter_detail_suppliers_table', 853),
(1685, '2021_08_02_172442_alter_table_store_view_gt_matrix_pdf', 852),
(1684, '2021_08_02_133528_alter_table_payment_receipts_monetary_account', 851),
(1683, '2021_08_02_104101_alter_table_received_payment_fields', 850),
(1682, '2021_07_31_140330_alter_product_push_informations_3498578349', 850),
(1681, '2021_07_30_142402_create_group_routes_table', 850),
(1680, '2021_07_30_123140_create_instagram_keywords_table', 850),
(1679, '2021_07_29_154519_alter_table_project_file_managers_add_field', 850),
(1678, '2021_07_28_142714_alter_table_project_file_managers_history', 850),
(1677, '2021_07_30_150045_alter_table_cashflow_monetary_account_id', 849),
(1676, '2021_07_30_145503_alter_table_order_monetary_account_id', 849),
(1675, '2021_07_30_141845_create_table_monetry_account_history', 848),
(1674, '2021_07_30_114345_alter_tabel_monetary_accounts_extra_fields', 847),
(1673, '2021_07_30_095535_alter_table_cashflow_field_amount', 846),
(1672, '2021_07_29_173633_alter_table_store_website_product_attributes_fields', 845),
(1671, '2021_07_29_161741_alter_table_cashflow_due_amount_', 844),
(1670, '2021_07_29_150524_alter_table_cashflow_eur_price', 843),
(1668, '2021_07_29_111703_alter_table_influencer_keyword', 842),
(1667, '2021_07_28_140611_create_site_development_status_histories', 841),
(1666, '2021_07_28_094929_add_index_to_tasks_table', 840),
(1665, '2021_07_28_092217_add_index_to_teams_table', 840),
(1664, '2021_07_28_092036_add_index_to_payments_table', 840),
(1663, '2021_07_28_091943_add_index_to_payment_receipts_table', 840),
(1662, '2021_07_27_105352_create_website_product_csvs_table', 839),
(1661, '2021_07_27_084927_add_column_scraper_images_table', 839),
(1660, '2021_07_28_100340_alter_table_cashflow_currency_fiedl', 838),
(1659, '2021_07_27_161040_create_table_store_website_product_screenshots', 837),
(1657, '2021_07_26_111625_alter_table__supplier__discount_log_history', 836),
(1656, '2021_07_24_095758_create_product_discount_excel_files_table', 836),
(1655, '2021_07_27_142510_alter_table_log_list_magento_image_not', 835),
(1654, '2021_07_26_170145_alter_table_order_product_currency_field', 834),
(1653, '2021_07_26_102822_add_column_sops_table', 833),
(1652, '2021_07_26_114143_alter_table_categories_add_enum', 832),
(1651, '2021_07_26_111532_alter_table_category_size_chart_needed', 831),
(1650, '2021_07_24_174609_atler_table_log_list_magentos_5467', 830),
(1649, '2021_07_24_130040_create_sop_permissions_table', 829),
(1648, '2021_07_24_082624_alter_add_flag_remark_table', 829),
(1647, '2021_07_23_180848_add_column_store_magento_api_search_products_column', 829),
(1646, '2021_07_23_172514_alter_tickets_add_column_softdelete_table', 829),
(1645, '2021_07_23_134930_add_user_id_to_product_push_information_histories_table', 829),
(1644, '2021_07_22_150814_hubstaff_history', 829),
(1643, '2021_07_22_132836_create_product_push_information_histories_table', 829),
(1642, '2021_07_22_082050_create_product_push_informations_table', 829),
(1641, '2021_07_23_182528_alter_table_log_list_magento_retry', 828),
(1640, '2021_07_23_172309_alter_table_log_list_magento_enum', 827),
(1639, '2021_07_23_150940_alter_table_log_list_magento_size_chart_url', 826),
(1638, '2021_07_23_134546_alter_table_log_list_magento_fields', 825),
(1637, '2021_07_21_174041_alter_table_scrap_influencers', 824),
(1636, '2021_07_20_113120_alter_table_chat_messages_changes', 823),
(1635, '2021_07_19_155401_alter_table_chat_messages', 823),
(1634, '2021_07_21_123531_add_field_to_website_store_views_table', 822),
(1633, '2021_07_20_104442_create_store_magento_api_search_products_table', 822),
(1632, '2021_07_20_182034_alter_table_brands_add_skip_external_scraper', 821),
(1631, '2021_07_20_145427_alter_scraper_imags_add_device_table', 820),
(1630, '2021_07_19_163613_alter_add_app_sid_twilio_table', 819),
(1629, '2021_07_17_141526_add_field_to_website_store_views_table', 819),
(1628, '2021_07_14_145317_drop_foreign_chat_messages_table', 818),
(1627, '2021_07_16_143336_alter_table_cropped_image_references', 817),
(1626, '2021_07_16_141459_create_table_cropping_instances', 816),
(1625, '2021_07_16_100819_alter_table_categories_product_type_mode', 815),
(1624, '2021_07_15_133205_create_tbl_magento_log_history', 814),
(1623, '2021_07_15_112617_alter_table_add_column_chat_messages', 814),
(1622, '2021_07_14_163433_add_field_to_payment_receipts', 814),
(1621, '2021_07_15_091500_create_content_manageent_emails_table', 813),
(1620, '2021_07_08_215857_create_store_website_user_history_table', 813),
(1619, '2021_07_14_084820_create_command_execution_historys_table', 812),
(1618, '2021_07_12_164737_add_is_process_to_media_table', 811),
(1617, '2021_07_13_150318_fix_field_name_project_file_manager', 810),
(1616, '2021_07_13_112549_alter_scrapper_img_34543', 809),
(1615, '2021_07_09_170215_add_field_to_suggested_product_lists_table', 808),
(1613, '2021_07_09_141105_create_user_feedback_categories_table', 808),
(1612, '2021_07_09_140953_create_user_feedback_statuses_table', 808),
(1611, '2021_07_09_105716_add_index_to_customers_table', 807),
(1610, '2021_07_09_083831_add_index_to_suggested_product_lists_table', 807),
(1609, '2021_07_08_175614_create_customer_address_datas_new_table', 806),
(1608, '2021_07_08_175310_drop_customer_address_datas_table', 806),
(1607, '2021_07_08_162637_alter_customer_address_table', 806),
(1606, '2021_07_07_165321_create_hubstaff_activity_by_payment_frequencies_table', 806),
(1605, '2021_07_06_224931_atler_add_column_message_queue_history_table', 806),
(1604, '2021_07_07_193752_alter_add_new_column_categories_table', 805),
(1603, '2021_07_08_103535_alter_table_field_scraper_process', 804),
(1602, '2021_07_07_170918_create_customer_address_data_table', 803),
(1601, '2021_07_07_153756_create_table_scraper_process_time', 802),
(1600, '2021_07_07_123206_add_column_chat_messages_table', 801),
(1599, '2021_07_07_105703_alter_table_store_websites_disable_push', 800),
(1598, '2021_07_04_183318_alter_add_new_columns_to_scrap_influencers_table', 799),
(1597, '2021_07_05_193135_alter_add_column_email_address_table', 798),
(1596, '2021_07_05_170453_alter_table_site_developments_table', 797),
(1595, '2021_07_05_104244_create_purchase_product_order_excel_file_versions_table', 796),
(1594, '2021_07_05_091955_create_purchase_product_order_excel_files_table', 796),
(1593, '2021_07_02_124530_create_google_search_related_images_table', 796),
(1592, '2021_07_02_123602_create_google_search_images_table', 796),
(1591, '2021_07_01_130800_add_need_to_check_measurement_to_categories_table', 796),
(1590, '2021_07_02_131848_aletr_add_column_table', 795),
(1589, '2021_07_02_092113_create_supplier_order_templates_table', 795),
(1588, '2021_07_01_200048_create_sytem_ip_user_table', 794),
(1587, '2021_07_01_165329_drop_foreign_instruction_table', 793),
(1586, '2021_07_01_170242_alter_table_store_website_brand', 792),
(1585, '2021_06_30_212810_create-user-pemfile-history-table', 791),
(1584, '2021_06_30_150053_alter_table_users_table', 790),
(1583, '2021_06_30_093403_alter_table_product_location_history_table', 789),
(1582, '2021_06_30_101815_alter_table_whatsapp_config', 788),
(1581, '2021_06_29_210946_add_mode_to_store_website_users', 787),
(1580, '2021_06_29_161655_add_data_to_media_table', 787),
(1579, '2021_06_28_222745_add_col_repoid_development_task_table', 786),
(1578, '2021_06_28_214458_create_learning_duedate_history_table', 786),
(1577, '2021_06_28_142508_alter_table_developer_task_send_remider_time', 785),
(1576, '2021_06_28_135208_alter_table_task_last_send_reminder', 784),
(1575, '2021_06_26_084522_add_index_to_vendors_table', 783),
(1574, '2021_06_26_084240_add_index_to_developer_modules_table', 783),
(1573, '2021_06_26_083035_add_index_to_products_table', 783),
(1572, '2021_06_26_082717_add_index_to_orders_table', 783),
(1571, '2021_06_26_082235_add_index_to_users_table', 783),
(1570, '2021_06_28_094655_alter_rename_column_name_table', 782),
(1569, '2021_06_26_152352_add_index_to_roles_table', 781),
(1568, '2021_06_26_152047_add_index_to_compositions_table', 781),
(1567, '2021_06_26_151723_add_index_to_scraped_products_table', 781),
(1566, '2021_06_25_102026_add_index_to_products_tablee', 781),
(1565, '2021_06_25_221807_add_new_cols_daily_activities_table', 780),
(1564, '2021_06_25_214358_add_new_cols_developer_tasks_tabke', 780),
(1563, '2021_06_25_202438_add_new_cols_tasks_tabke', 780),
(1562, '2021_06_25_142424_add_index_to_store_websites_table', 780),
(1561, '2021_06_25_142133_add_index_to_category_segment_discounts_table', 780),
(1560, '2021_06_25_133640_add_index_to_brands_table', 780),
(1559, '2021_06_25_133023_add_index_to_store_website_brands_table', 780),
(1558, '2021_06_25_131536_add_is_flagged_to_developer_tasks', 779),
(1557, '2021_06_25_133445_alter_table_learning_module_column', 778),
(1556, '2021_06_25_133112_alter_table_daily_activities_timezone', 777),
(1555, '2021_06_25_102026_add_index_to_products_table', 776),
(1554, '2021_06_25_102535_alter_table_task_default_status', 775),
(1553, '2021_06_24_145508_add_is_auto_skip_to_scrapped_category_mappings_table', 774),
(1552, '2021_06_24_110303_alter_table_chatbot_replies_field', 773),
(1551, '2021_06_24_095505_add_index_to_products_table', 772),
(1550, '2021_06_23_160400_add_is_auto_fix_to_scrapped_category_mappings', 771),
(1549, '2021_06_23_141410_alter_add_field_order_ia_table', 771),
(1548, '2021_06_23_165504_alter_table_user_auto_approval', 770),
(1547, '2021_06_23_092649_create_webhook_notification_table_34543', 769),
(1546, '2021_06_23_091128_alter_chatmessage_additional_data_table', 769),
(1545, '2021_06_22_211543_create_learning_status_history_table', 768),
(1544, '2021_06_23_1624392529_create_message_queue_history_table', 767),
(1543, '2021_06_23_074006_alter_table_gmail_data_medias_table', 766),
(1542, '2021_06_22_105623_create_gmail_data_lists_table', 765),
(1541, '2021_06_22_074445_create_gmail_data_media_table', 764),
(1540, '2021_06_22_072825_alter_table_gmail_datas_table', 764),
(1539, '2021_06_21_220444_add_hub_staff_user_id_and_total_track_hub_staff_activity_notifications_table', 764),
(1538, '2021_06_21_184650_create_brand_with_logo_table', 764),
(1537, '2021_06_21_171937_alter_table_scrap_api_logs_table', 764),
(1536, '2021_06_21_163320_create_brand_logo_table', 764),
(1535, '2021_06_21_095509_create_purchase_product_order_images_table', 763),
(1534, '2021_06_18_141803_create_purchase_product_order_log_table', 762),
(1533, '2021_06_18_141322_create_scrap_api_logs_table', 762),
(1532, '2021_06_18_100319_create_purchase_product_order_table', 762),
(1531, '2021_06_18_111141_alter_table_auto_generated_message_log_text', 761),
(1530, '2021_06_17_182321_create_email_notification_email_details_table', 760),
(1529, '2021_06_17_180905_alter_users_email_notification_table', 760),
(1528, '2021_06_17_154309_alter_table_scraper_server_history_run_duration', 759),
(1527, '2021_06_16_134125_add_index_to_live_chat_users_table', 759),
(1526, '2021_06_16_133835_add_index_to_page_instructions_table', 759),
(1525, '2021_06_16_132850_add_index_to_replies_table', 759),
(1524, '2021_06_16_125829_add_index_to_bulk_customer_replies_keyword_table', 759),
(1523, '2021_06_17_092607_alter_drop_foreign_key_page_notes_table', 758),
(1522, '2021_06_16_151725_add_indexs_to_tables', 758),
(1521, '2021_06_16_134231_create_keyword_auto_generated_message_logs_table', 758),
(1520, '2021_06_14_100944_add_index_to_auto_refresh_pages', 757),
(1519, '2021_06_14_100702_add_index_to_store_website_analytics', 757),
(1518, '2021_06_14_100534_add_index_to_customer_live_chats', 757),
(1517, '2021_06_14_095534_add_index_to_meeting_and_other_times', 757),
(1516, '2021_06_14_091955_add_index_to_erp_priorities', 757),
(1515, '2021_06_14_091429_add_index_to_devloper_tasks_history', 757),
(1514, '2021_06_14_082826_add_index_to_devloper_tasks', 757),
(1513, '2021_06_14_074848_add_index_to_chat_messages', 757),
(1512, '2021_06_12_092044_create_table_memory_usage', 757),
(1511, '2021_06_10_124129_create_erp_lead_status_histories_table', 757),
(1510, '2021_06_09_084044_alter_add_column_learning_id_table', 757),
(1509, '2021_06_02_141901_update_brands_table', 756),
(1508, '2021_06_11_101134_alter_table_database_historical_record_field', 755),
(1507, '2021_06_11_100428_alter_table_database_historical_records', 754),
(1506, '2021_06_10_172000_create_tbl_bulk_message_cutomer_dnd', 753),
(1505, '2021_06_10_131611_add_index_to_tables', 753),
(1504, '2021_06_10_141127_alter_tbl_products_add_index_to_supplierid', 752),
(1503, '2021_06_09_142154_alter_user_updated_attribute_table_546657', 751),
(1502, '2021_06_09_131451_alter_category_table_fro_erp_category', 751),
(1501, '2021_06_09_100044_alter_scrap_add_column_table', 751),
(1500, '2021_06_08_152813_create_new_auto_messages', 751),
(1499, '2021_06_08_102831_alter_scrapped_category_mappings_indexing_table', 750),
(1498, '2021_06_07_200237_alter_scrapped_products_table_to_apply_fulltext_index', 750),
(1497, '2021_06_07_194432_alter_scrapped_category_mapping_349857', 750),
(1496, '2021_06_04_161756_create_supplier_order_inquery_datas_table', 749),
(1495, '2021_06_04_150118_create_tbl_auto_complete_messages', 748),
(1494, '2021_06_04_090031_update_review_brands_list_table', 748),
(1493, '2021_06_03_155353_create_scrapped_product_category_mappings_table', 748),
(1492, '2021_06_03_151006_create_scrapped_category_mappings_table', 748),
(1491, '2021_06_03_105719_alter_table_products_discount_percentage', 748),
(1490, '2021_06_03_105320_alter_table_scraped_product_discount_percentage', 748),
(1489, '2021_06_03_090114_update_emails_table', 748),
(1488, '2021_06_02_160323_create_scraper_durations_table', 748),
(1487, '2021_06_02_135657_alter_learnings_changes_table', 748),
(1486, '2021_06_02_102741_create_scrapper_log_status_table', 748),
(1485, '2021_06_01_185835_alter_daily_activities_table', 748),
(1484, '2021_05_31_161604_create_plan_solutions_table', 748),
(1483, '2021_05_29_111936_alter_product_templates_table', 748),
(1482, '2021_05_29_111637_create_review_brands_list_table', 748),
(1481, '2021_05_29_103315_create_log_excel_imports_versions_table', 748),
(1480, '2021_05_29_064524_create_plan_categories_table', 748),
(1479, '2021_05_29_064218_update_plans_table_4894567', 748),
(1478, '2021_05_29_064218_alter_update_plans_table', 748),
(1477, '2021_05_28_163213_create_supplier_brand_discounts_table', 748),
(1476, '2021_05_28_093338_update_plans_table', 748),
(1475, '2021_05_28_093333_create_plan_types_table', 748),
(1474, '2021_05_27_135256_create_category_updation__table', 748),
(1473, '2021_05_27_103354_update_excel_importer_details_table', 748),
(1472, '2021_05_27_081027_create_new_column_need_to_skip_categories_table', 748),
(1471, '2021_05_25_183024_create_supplier_translate_history_table', 748),
(1470, '2021_05_21_130052_alter_scraper_imags', 748),
(1469, '2021_05_21_105523_alt_field_importance_to_plans_table', 748),
(1468, '2021_05_20_180642_alt_field_status_to_routes_table', 748),
(1467, '2021_05_20_034626_update_return_exchanges_table', 748),
(1466, '2021_05_19_135651_update_documents_table', 748),
(1465, '2021_05_19_100220_create_user_login_ips_table', 747),
(1464, '2021_05_15_173105_update_users_table', 747),
(1463, '2021_05_18_133726_create_log_keywords_table', 746),
(1462, '2021_05_17_162204_create_learning_modules_table', 745),
(1461, '2021_05_17_170931_alter_table_hash_tag_instagram_id', 744),
(1460, '2021_05_15_110243_create_learnings_table', 743),
(1459, '2021_05_17_134724_create_scrap_inde132156x_table', 742),
(1458, '2021_05_14_153851_create_add_brand_id_developer_tasks_table', 741),
(1457, '2021_05_14_145329_create_plan_basis_status_table', 740),
(1456, '2021_05_14_123901_alter_plans_table', 740),
(1455, '2021_05_13_125515_create_productactivities_table', 739),
(1454, '2021_05_11_115409_create_hubstaff_notes_table', 738),
(1453, '2021_05_04_071450_create_agent_call_status_table', 737),
(1452, '2021_05_03_164135_create_database_table_historical_records', 737),
(1451, '2021_05_07_144815_alter_table_watson_account_field', 736),
(1450, '2021_05_06_180325_create_plans_table', 736),
(1449, '2021_05_07_102714_alter_table_log_request_field_time_taken', 735),
(1448, '2021_05_06_112405_add_product_id_to_images_table', 735),
(1447, '2021_04_30_191025_new_permission_request', 734),
(1446, '2021_04_30_144128_create_table_auto_refresh', 733),
(1445, '2021_04_30_153310_alter_order_table', 732),
(1444, '2021_04_29_143510_change_hubstaff_activities_id_type', 731),
(1443, '2021_04_24_115642_create_store_websites_country_shipping', 730),
(1442, '2021_04_23_172356_alter_table_log_request_add_field', 729),
(1441, '2021_04_23_142555_alter_table_brand_priority_default_zero', 729),
(1440, '2021_04_22_121147_alter_product_templates', 728),
(1439, '2021_04_20_170107_alter_table_user_database_tables', 727),
(1438, '2021_04_20_151754_create_table_user_database', 726),
(1437, '2021_04_17_113044_alter_store_website_product_attributes', 726),
(1436, '2021_04_19_134128_alter_table_scraper_server_status_histories', 725),
(1435, '2021_04_17_153256_google_analytics_histories', 724),
(1434, '2021_04_16_162422_alter_table_products_sub_status', 723),
(1433, '2021_04_14_174253_create_table_brand_scraper_result', 722),
(1432, '2021_04_14_130758_alter_table_add_payment_due_date', 721),
(1431, '2021_04_14_115642_alter_table_user_add_billing_frequency', 720),
(1430, '2021_04_13_153340_alter_table_scraper_server_history_add_field', 719),
(1429, '2021_04_13_100851_alter_table_chat_message_payment_receipt_id', 718),
(1428, '2021_04_10_120117_google_analytics_audience', 717),
(1427, '2021_04_09_104542_alter_store_website_pages_tbl', 717),
(1426, '2021_04_09_101004_alter_store_website_category_seos_tbl', 717),
(1425, '2021_04_09_125340_alter_table_create_start_time', 716),
(1424, '2021_04_08_124058_google_analytics_user', 715),
(1423, '2021_04_08_123954_google_analytics_geo_network', 715),
(1422, '2021_04_08_123832_google_analytics_platform_device', 715),
(1421, '2021_04_08_123632_google_analytics_page_tracking', 715),
(1420, '2021_04_07_150030_google_analytics', 715),
(1419, '2021_04_06_174502_influencers_history', 714),
(1418, '2021_04_06_192509_create_table_customer_basket_products_table', 713),
(1417, '2021_04_06_191742_create_table_customer_basket', 713),
(1416, '2021_04_06_144510_create_table_scrap_logs', 712),
(1415, '2021_04_05_163028_alter_hubstaff_activity_summaries', 711),
(1414, '2021_04_05_140315_alter_table_scraper_developer_flag', 710),
(1413, '2021_04_03_101634_create_table_scraper_server_status', 709),
(1412, '2021_04_02_181004_alter_table_scraper_add_flag', 708),
(1410, '2021_04_02_134337_alter_table_influencer_keyword_add_field_account', 707),
(1409, '2021_04_01_164107_create_table_store_website_seo_format', 706),
(1408, '2021_04_01_125919_create_table_user_updated_attribute_history', 705),
(1406, '2021_03_30_171918_alter_table_hubstaff_activity_summery_requested_field', 704),
(1405, '2021_03_25_173731_store_views_gt_metrix', 703),
(1404, '2021_03_26_133817_alter_table_scraper_auto_restart', 702),
(1403, '2021_03_26_094945_alter_website_store_views', 701),
(1402, '2021_03_24_161645_website_store_views_webmaster_history', 700),
(1401, '2021_03_24_173919_alter_table_scraper_remarks_extra_fields', 699),
(1400, '2021_03_23_160025_alter_table_products_suggested_color_field', 698),
(1399, '2021_03_23_140242_alter_table_chat_messages_error_status', 697),
(1398, '2021_03_22_182644_create_table_scraper_position_histories', 696),
(1397, '2021_03_22_122757_instagram_direct_messages_history', 695),
(1396, '2021_03_20_100405_daily_activities_histories', 694),
(1395, '2021_03_19_194411_alter_table_chat_messages_table', 693),
(1394, '2021_03_19_145107_alter_daily_activities', 693),
(1393, '2021_03_19_183426_create_table_push_fcm_notification_histories_table', 692),
(1392, '2021_03_19_175118_alter_table_google_ads_account_table', 691),
(1391, '2021_03_19_171441_alter_table_ad_accounts_add_field', 690),
(1390, '2021_03_19_160026_alter_table_google_translation_settings', 689),
(1389, '2021_03_19_155518_alter_table_store_website_analytics_email', 689),
(1388, '2021_03_18_183704_alter_table_developer_tasks_field_scraper', 688),
(1387, '2021_03_04_134456_add_column_chat_session_id_to_vendor_table', 687),
(1386, '2021_03_17_124152_order_customer_address', 686),
(1385, '2021_03_15_191058_alter_table_communication_histories', 685),
(1384, '2021_03_15_173711_alter_table_emails_status_and_other', 685),
(1383, '2021_03_15_143048_posts', 684),
(1382, '2021_03_15_103911_alter_accounts', 684),
(1381, '2021_02_26_155339_add_column_hashtag_to_post', 683),
(1380, '2021_02_26_141147_create_table_instagram_post_logs', 683),
(1379, '2021_03_12_131134_alter_table_scraper_screenshot_history_table', 682),
(1378, '2021_03_10_092208_alter_table_coupon_code_rules_create', 681),
(1377, '2021_03_09_182812_alter_add_column_to_suggested_products_tbl', 680),
(1376, '2021_03_05_123235_create_email_content_history_table', 679),
(1375, '2021_03_04_130336_create_log_requests_table', 678),
(1374, '2021_03_03_161358_alter_chatbot_questions_extra', 677),
(1373, '2021_03_02_160601_alter_chatbot_questions', 677),
(1372, '2021_03_04_102944_alter_table_customer_newsletter', 676),
(1371, '2021_03_04_102224_alter_table_customer_store_website_store', 675),
(1370, '2021_03_03_193717_add_priority_column_to_brands_table', 674),
(1369, '2021_03_02_191055_alter_table_ticket_add_fields', 673),
(1368, '2021_03_01_191718_alter_table_enum_return_exchanges', 672),
(1367, '2021_03_01_162552_add_column_template_status_to_product_templates_table', 672),
(1366, '2021_02_27_162130_create_template_modifications_table', 672),
(1365, '2021_02_25_190919_add_uid_and_preview_url_column_to_templates_table', 672),
(1364, '2021_03_01_140346_alter_table_last_run_cron_time', 671),
(1363, '2021_02_26_180210_alter_table_coupon_code_rules', 670),
(1362, '2021_02_23_121730_create_table_log_instagram', 669),
(1361, '2021_02_23_152125_alter_coupons_table', 668),
(1360, '2021_02_22_162957_alter_coupon_code_rules_table', 668),
(1359, '2021_02_22_154040_create_website_store_view_value_table', 668),
(1358, '2021_02_24_125730_create_table_product_cancellation_policies', 667),
(1357, '2021_02_25_152258_store_website_category_seos_histories', 666),
(1356, '2021_02_24_122000_create_api_response_messages_table', 665),
(1355, '2021_02_24_114938_add_date_column_to_google_search_anlytics_table', 664),
(1354, '2021_02_23_201137_create_google_search_analytics_table', 664),
(1353, '2021_02_23_135154_create_sites_table', 664),
(1352, '2021_02_22_194342_add_is_default_column_to_website_stores_table', 664),
(1351, '2021_02_24_142404_alter_table_chat_messages_is_email_field', 663),
(1350, '2021_02_23_104658_create_google_traslation_settings_table', 662),
(1349, '2021_02_18_124215_create_digital_marketing_files_storage', 662),
(1348, '2021_02_19_172508_scraper_imags', 661),
(1347, '2021_02_19_153620_create_magento_user_password_history_table', 661),
(1346, '2021_02_19_135645_add_column_to_scrap_product', 660),
(1345, '2021_02_19_180040_alter_table_emails_table_add_column_error_msg', 659),
(1344, '2021_02_18_100419_cretae_erp_logs_table', 658),
(1343, '2021_02_16_184456_add_new_column_emails_table', 657),
(1342, '2021_02_15_150607_add_action_field_in_laravel_logs_table', 656),
(1341, '2021_02_15_113928_add_fileds_in_laravel_logs_table', 656),
(1340, '2021_02_11_133900_add_firstname_lastname_to_store_website_users', 656),
(1339, '2021_02_11_094406_add_is_deleted_to_store_website_users', 656),
(1338, '2021_02_10_175753_create_table_store_website_users', 656),
(1337, '2021_02_12_102219_create_coupon_code_rules_table', 655),
(1336, '2021_02_15_155810_add_status_column_to_tasks_table', 654),
(1335, '2021_02_15_114411_create_store_website_product_check', 653),
(1334, '2021_02_12_124201_add_user_log_list_magento', 653),
(1333, '2021_02_09_180955_update_log_excel_imports_table_add_columns_message', 653),
(1332, '2021_02_01_128100_update_log_excel_imports_table_add_column_md5', 653),
(1331, '2021_02_13_211145_alt_description_to_activities_table', 652),
(1330, '2021_02_13_130414_create_store_website_attributes_table', 652),
(1329, '2021_02_13_111202_alt_source_to_customers_table', 652),
(1328, '2021_02_12_101820_alt_store_website_id_to_store_website_category_seos_table', 651),
(1327, '2021_02_11_134755_add_column_autopush_product', 650),
(1326, '2021_02_10_132850_create_chat_bot_question_error_logs_table', 649),
(1325, '2021_02_09_185204_create_store_website_brand_histories_table', 648),
(1324, '2021_02_09_182117_add_new_columns_in_user_avaibilities_table', 647),
(1323, '2021_02_08_144125_update_suppliers', 647),
(1322, '2021_02_04_170906_google_web_master', 646),
(1321, '2021_02_05_185859_create_table_email_run_histories', 645),
(1320, '2021_01_23_135326_ad_accounts', 644),
(1319, '2021_01_22_211724_ad', 644),
(1318, '2021_01_22_172644_ad_group', 644),
(1317, '2021_01_22_135338_ads_campaign', 644),
(1316, '2021_02_04_105232_add_google_service_account_json_to_store_website_analytics_table', 643),
(1315, '2021_02_03_111208_add_is_flagged_to_remarks', 642),
(1314, '2021_02_03_141139_add_website_to_laravel_logs_table', 641),
(1313, '2021_02_02_172253_alt_payment_field_way_bills_table', 640),
(1312, '2021_01_29_125222_add_category_color_composition_in_scraped_products_table', 639),
(1311, '2021_01_29_144931_alter_table_product_size_add_field_supplier', 638),
(1309, '2021_01_28_135249_alter_table_product_suppliers_size_system', 636),
(1308, '2021_01_28_134515_alter_table_supplier_id_and_size_system_id', 636),
(1307, '2021_01_26_151414_add_language_store_website_category_seos_table', 635),
(1305, '2021_01_20_104911_create_store_website_category_seo_table', 633),
(1304, '2021_01_21_101505_alter_table_websites_is_price_override', 632),
(1303, '2021_01_20_112844_alter_size_system_in_supplier_table', 631),
(1302, '2021_01_18_175044_system_size_manger', 630),
(1301, '2021_01_18_165837_system_size', 630),
(1300, '2021_01_19_143803_system_size_relations', 629),
(1299, '2021_01_19_152242_alter_table_store_website_is_price_ovverride', 628),
(1296, '2021_01_18_103840_add_default_duty_to_simply_duty_countries_table', 626),
(1295, '2021_01_15_130714_create_category_segment_discounts_table', 625),
(1294, '2021_01_15_101409_add_category_segment_id_to_categories_table', 625),
(1293, '2021_01_15_100727_create_category_segments_table', 625),
(1292, '2021_01_11_151722_alter_table_store_website_page_history_response_url', 624),
(1291, '2021_01_07_140524_alter_table_product_push_error_logs_magento_log', 623),
(1290, '2021_01_06_110940_alter_table_product_templates_store_website', 622),
(1289, '2021_01_04_155248_add_shipment_price_invoice', 621),
(1288, '2020_12_31_181424_alter_table_store_website_email_address_null', 620),
(1287, '2020_12_29_095127_alter_table_copy_page_id', 619),
(1286, '2020_12_28_173237_alter_table_store_website_page_language', 618),
(1285, '2020_12_25_175728_create_table_store_website_page_history', 617),
(1281, '2020_12_25_101411_alter_table_name_on_language', 616),
(1280, '2020_12_24_113750_alter_table_store_website_product_attributes_price', 615),
(1279, '2020_12_21_114715_alter_table_add_is_completed_flag', 614),
(1277, '2020_12_21_102132_alter_table_website_store_views_null', 613),
(1276, '2020_12_18_161229_create_table_store_website_page', 612),
(1275, '2020_12_14_153626_create_table_website_stores', 611),
(1274, '2020_12_17_161424_alter_table_websites_countires', 610),
(1272, '2020_12_15_172850_alter_table_chatbot_reply_replied_from', 609),
(1271, '2020_12_15_160037_alter_table_chatbot_reply_replied_chat_id', 608),
(1269, '2020_12_14_153635_create_table_website_store_views', 607),
(1267, '2020_12_14_152856_create_table_websites', 607),
(1266, '2020_12_14_104915_alter_table_chat_message_question_id', 606),
(1265, '2020_12_12_174723_alter_table_developer_task_field_priority_field', 605),
(1264, '2020_12_12_174656_alter_table_task_field_priority_field', 605),
(1262, '2020_12_07_152824_alter_table_return_refund_status_message', 604),
(1260, '2020_12_04_132322_alter_table_store_website_colors', 603),
(1259, '2020_12_03_00000_alter_tbl_chatbot_dialogs_new', 602),
(1258, '2020_12_02_173640_alter_table_newsletter_sending_list', 601),
(1257, '2020_12_02_094140_create_newsletter_products_table', 600),
(1256, '2020_12_02_094139_create_newsletter', 600),
(1254, '2020_11_30_154816_add_fields_in_template_table', 599),
(1253, '2020_11_29_090713_create_description_changes_table', 598),
(1252, '2020_11_29_112429_create_unknown_sizes_table', 597),
(1251, '2020_11_29_110728_add_references_in_sizes_table', 597),
(1250, '2020_11_27_095514_create_captions_table', 597),
(1249, '2020_11_21_170454_add_min_and_max_sale_price_in_brands_tables', 596),
(1248, '2020_11_23_092858_remove_index_from_unique_waybill', 595),
(1247, '2020_10_17_132336_add_fieldsintowaybills_table_one', 594),
(1246, '2020_10_17_125641_add_fieldsintowaybills_table', 594),
(1245, '2020_09_23_090256_alter_waybill_able', 594),
(1244, '2018_02_05_000000_create_queue_monitor_table', 593),
(1243, '2020_11_11_180935_create_trigger_for_jobs', 592),
(1241, '2020_11_19_122309_add_supplier_to_missing_brands_tables', 591),
(1240, '2020_11_17_163953_create_missing_brands_table', 590),
(1239, '2020_11_17_154835_add_cropping_size_in_store_websites', 589),
(1238, '2020_11_17_145334_add_last_brand_from_product', 588),
(1237, '2020_11_13_121011_add_index_in_product_inventory_status_histories', 587),
(1236, '2020_11_10_162742_add_duedate_to_assets_manager', 586),
(1235, '2020_11_09_205023_add_column_to_tickets_table', 585),
(1234, '2020_11_06_163705_add_fieldsforcampaign_googlecampaigns_table_field', 584),
(1233, '2020_11_06_141817_alter_table_product_push_error_log', 584),
(1232, '2020_11_06_104449_add_fieldsforcampaign_googlecampaigns_table', 584),
(1231, '2020_11_03_113122_create_credit_history_table', 583),
(1230, '2020_11_03_134712_alter_field_notification_key', 582),
(1229, '2020_11_02_135923_add_fieldtosuggestproductlist_table', 581),
(1228, '2020_11_02_153420_add_color_field_in_crop_image_reference', 580),
(1227, '2020_10_31_092633_update_affiliate_table_influencer_page', 579),
(1226, '2020_10_22_082618_add_storewebsiteid_field_to_affiliate_table', 578),
(1225, '2020_10_20_152334_add_fields_to_affilate_table', 578),
(1224, '2020_10_28_120129_update_sent_at_sent_on_fields_fcm_notifications', 577),
(1223, '2020_10_28_105551_remove_field_from_fcm_notifications', 577),
(1222, '2020_10_28_100354_create_fcm_tokens_table', 577),
(1221, '2020_10_26_114958_removecolumnsfrom_products_table', 576),
(1220, '2020_10_22_172047_add_fields_to_products_table', 576),
(1219, '2020_10_22_171257_add_fields_to_product_translations_table', 576),
(1218, '2020_10_27_162838_add_supplier_id_to_inventory_status_histories_table', 575),
(1217, '2020_10_23_155232_add_date_to_suggested_product_lists_table', 574),
(1216, '2020_10_23_095918_add_inventory_status_id_in_order_products_table', 573),
(1215, '2020_10_22_200331_create_inventory_statuses_table', 573),
(1214, '2020_10_22_124230_add_discount_info_id_in_order_products_table', 573),
(1213, '2020_10_22_090921_create_supplier_discount_infos_table', 573),
(1212, '2020_10_26_150823_fix_issue_with_supplier_table', 572),
(1211, '2020_10_23_125555_create_googleads_table', 571),
(1210, '2020_10_23_111913_add_fieldsintocampaign_table_a', 571),
(1209, '2020_10_23_095929_create_googleadsgroups_table', 571),
(1208, '2020_10_23_093334_add_fieldsintocampaign_table_b', 571),
(1207, '2020_10_23_091222_add_fieldsintocampaign_table_c', 571),
(1206, '2020_10_22_135552_create_googleadsaccounts_table', 571),
(1205, '2020_10_22_124626_modifyfield_table_googlecamp', 571),
(1204, '2020_10_22_120503_create_googlecampaign_table', 571),
(1203, '2020_10_23_113527_add_dob_and_anniversary_in_customer_table', 570),
(1202, '2020_10_22_124338_create_push_fcm_notifications_table', 569),
(1201, '2020_10_22_120918_add_fields_to_store_websites_table', 569),
(1200, '2020_10_23_111743_add_field_to_tasks_table', 568),
(1199, '2020_10_21_162659_add_statusfield_table', 567),
(1198, '2020_10_21_145803_create_waybill_invoices_table', 567),
(1197, '2020_10_21_104809_alter_table_return_exchanges_enum_field', 566),
(1196, '2020_10_19_191745_update_column_in_return_exchanges_table', 566),
(1195, '2020_10_18_152206_create_facebook_posts_table', 566),
(1194, '2020_10_19_115128_add_remove_attachment_to_suggested_product_lists_table', 565),
(1193, '2020_10_16_162538_update_inventory_status_histories_table', 564),
(1192, '2020_10_16_152621_add_approve_mail_to_emails_table', 564),
(1191, '2020_10_16_111534_create_inventory_status_histories_table', 564),
(1190, '2020_10_16_093110_add_est_delivery_time_to_suppliers_table', 564),
(1189, '2020_10_10_105501_create_order_status_histories_table', 563),
(1188, '2020_10_16_075336_add_watson_push_to_watson_accounts_table', 562),
(1187, '2020_10_15_172047_create_googlefiletranslatorfiles_table', 561),
(1186, '2020_10_16_095633_create_gift_cards_table', 560),
(1185, '2020_10_13_130443_create_referral_programs_table', 559),
(1184, '2020_10_07_165207_add_fields_to_coupons_table', 559),
(1183, '2020_10_07_112808_create_refer_friend_table', 559),
(1182, '2020_10_14_145145_add_due_date_to_tasks_table', 558),
(1181, '2020_10_14_145014_add_due_date_to_developer_tasks_table', 558),
(1180, '2020_10_14_091408_add_fieldtochatmsg_table', 558),
(1179, '2020_10_13_105356_create_chatbot_dialog_error_logs_table', 557),
(1178, '2020_10_13_105223_add_store_website_id_to_chatbot_dialog_responses_table', 557),
(1177, '2020_10_13_072444_create_chatbot_error_logs_table', 557),
(1176, '2020_10_13_115138_create_scrapped_facebook_users_table', 556),
(1175, '2020_10_13_145459_alter_language_table_status', 555),
(1174, '2020_10_13_104107_add_url_field_mangento_log', 554),
(1173, '2020_10_09_105204_create_product_push_error_logs_table', 553),
(1172, '2020_10_09_155316_alter_table_ticket_field_source_of_ticket', 552),
(1171, '2020_10_07_091558_add_fields_to_tickets_table', 551),
(1170, '2020_10_08_143146_add_tax_field_to_price_comparison_table', 550),
(1169, '2020_10_07_160155_add_new_columns_to_scrap_influencers_table', 549),
(1168, '2020_10_07_135350_alter_tbl_site_cropped_images', 549),
(1167, '2020_10_07_080846_create_tbl_site_cropped_images', 549),
(1166, '2020_10_06_170956_add_new_column_to_instagram_threads_table', 549),
(1165, '2020_10_06_162712_alter_tbl_products', 549),
(1164, '2020_10_05_180040_create_estimated_delivery_histories_table', 548),
(1163, '2020_10_05_142644_add_history_type_in_return_exchange_histories_table', 548),
(1162, '2020_10_05_141529_add_est_completion_date_in_return_exchanges_table', 548),
(1161, '2020_10_04_202308_create_log_scraper_table', 548),
(1160, '2020_10_01_152105_create_project_file_managers_table', 548),
(1159, '2020_09_23_143039_add_approve_login_in_users_table', 548),
(1158, '2020_10_05_105128_alter_tbl_chatbot_dialog_responses', 547),
(1157, '2020_10_05_061045_alter_tbl_chatbot_dialogs', 547),
(1156, '2020_10_01_131252_create_suggested_product_lists_table', 546),
(1155, '2020_10_01_103243_create_suggested_products_table', 546),
(1154, '2020_10_03_114345_store_website_products', 545),
(1153, '2020_09_30_155314_alter_tabl_task_user_histories', 544),
(1152, '2020_09_30_155011_update_suggested_reply_column_in_chatbot_questions_reply_table', 544),
(1151, '2020_09_30_120706_add_store_id_to_email_addresses_table', 544),
(1150, '2020_09_30_112121_add_site_dev_field_to_developer_tasks', 544),
(1149, '2020_09_28_224518_edit_hubstaff_activity_summaries_table', 544),
(1148, '2020_10_02_133116_create_table_size_storewise', 543),
(1146, '2020_10_02_104224_create_table_for_size', 542),
(1145, '2020_09_29_152125_store_view_with_website_groups', 542),
(1144, '2020_09_28_151151_add_hubstaff_activity_summary_id_to_chat_messages_table', 541),
(1143, '2020_09_29_091221_update_erp_leads_table', 540),
(1142, '2020_09_29_090001_add_new_fields_to_chatbot_questions_table', 539),
(1141, '2020_09_29_025642_add_assign_to_charity_table', 539),
(1140, '2020_09_28_141211_create_charity_status_masters_table', 539),
(1139, '2020_09_28_125806_add_whatsapp_number_charities_table', 539),
(1138, '2020_09_28_124615_create_charity_order_history_table', 539),
(1137, '2020_09_28_124341_add_contirbutions_to_customer_order_charities_table', 539),
(1136, '2020_09_28_125726_create_tbl_lead_hubstaf_detail', 538),
(1135, '2020_09_26_124114_add_subject_static_template_to_mailinglist_templates_table', 538),
(1134, '2020_09_25_155240_alter_tbl_developer_tasks_add_lead_dev_estimation_time', 538),
(1133, '2020_09_26_133007_create_log_list_magentos_table', 537),
(1132, '2020_09_26_132625_create_languages_table', 536),
(1131, '2020_09_28_092503_alter_tbl_chatbot_questions', 535),
(1130, '2020_09_15_073000_add_foreign_keys_to_store_website_analytics_table', 534),
(1129, '2020_09_15_072959_create_store_website_analytics_table', 534),
(1128, '2020_09_25_140407_add_index_to_store_website_orders_table', 533),
(1127, '2020_09_25_140355_add_index_to_orders_table', 533),
(1126, '2020_09_25_081235_update_product_id_field_in_erp_lead_sending_histories', 532),
(1125, '2020_09_23_200900_update_product_id_field_in_erp_lead_sending_histories', 532),
(1124, '2020_09_23_143252_create_erp_lead_sending_histories_table', 532),
(1123, '2020_09_18_150650_alter_email_table_field', 531),
(1122, '2020_09_24_052156_add_keywords_in_scrap_influencers_table', 530);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1121, '2020_09_23_155801_alter_table_watson_workspace_add_account_id', 530),
(1120, '2020_09_23_143948_create_tbl_watson_workspace', 530),
(1119, '2020_09_23_111223_alter_watson_accounts_table_for_workspace_id_and_assistant_id_34', 529),
(1118, '2020_09_23_100044_add_new_columns_to_return_exchanges_table', 528),
(1117, '2020_09_22_105314_alterscraped_products_table', 528),
(1116, '2020_09_21_022513_create_customer_order_charities_table', 527),
(1115, '2020_09_21_022456_create_charities_table', 527),
(1114, '2020_09_22_102710_update_default_for_to_varchar_whatsapp_configs', 526),
(1113, '2020_09_21_140533_create_chatbot_questions_reply_table', 526),
(1112, '2020_09_21_110105_create_waybill_track_histories_table', 525),
(1111, '2020_09_20_152812_create_lead_lists_table', 525),
(1110, '2020_09_16_161146_create_email_leads_table', 525),
(1109, '2020_09_14_172754_add_store_id_and_default_for_to_whatsapp_configs', 525),
(1108, '2020_09_22_111037_fix_message_application_id', 524),
(1107, '2020_09_25_135409_create_store_website_id_in_landing_product-part', 523),
(1106, '2020_09_25_135409_create_store_website_id_in_landing_product', 522),
(1105, '2020_09_21_144513_update_developer_tasks_history', 522),
(1104, '2020_09_21_144500_update_developer_tasks', 522),
(1103, '2020_09_09_173458_add_status_id_to_landing_page_products_table', 522),
(1102, '2020_09_21_171402_generate_customer_migration_file_erp_lead', 521),
(1101, '2020_09_21_062149_add_is_rejeted_to_product_translation_history_table', 520),
(1100, '2020_09_18_162203_create_translation_languages_table', 520),
(1099, '2020_09_18_152411_create_product_translation_history_table', 520),
(1098, '2020_09_18_152116_add_site_id_to_product_translations_table', 520),
(1097, '2020_09_18_144812_create_watson_accounts_table', 519),
(1096, '2020_09_15_172542_add_deleted_at_column_in_erp_leads_table', 519),
(1095, '2020_09_15_172222_add_deleted_at_column_in_return_exchanges_table', 519),
(1094, '2020_09_17_110741_tickets', 518),
(1093, '2020_09_19_120406_create_influencer_keywords_table', 517),
(1092, '2020_09_15_160126_create_supplier_price_range_table', 516),
(1091, '2020_09_15_160126_add_supplier_price_range_id_to_suppliers_table', 516),
(1090, '2020_09_15_112710_add_order_price_to_order_products_table', 516),
(1089, '2020_09_15_104434_add_price_to_orders_table', 516),
(1088, '2020_09_14_105027_create_email_status_table', 516),
(1087, '2020_09_14_104815_add_email_category_id_to_emails', 516),
(1086, '2020_09_14_103358_create_email_category_table', 516),
(1085, '2020_09_12_184835_alter_table_mailinglist_templates_for_store_id_and_category_id_234', 516),
(1084, '2020_09_12_183426_create_table_mailinglist_templates_category', 516),
(1083, '2020_09_10_123744_create_product_status_histories_table', 516),
(1082, '2020_09_11_192230_add_user_notes_to_hubstaff_activity_table', 515),
(1081, '2020_09_10_132138_add_columns_to_chatbot_question_examples_table', 515),
(1080, '2020_09_09_134935_add_columns_to_chatbot_questions_table', 515),
(1079, '2020_09_14_192506_devtask-2893', 514),
(1078, '2020_09_11_200143_add_instagram_user_id_in_chat_messages_table', 513),
(1077, '2020_09_11_190540_add_fullname_in_instagram_users_lists_table', 513),
(1076, '2020_09_10_162123_create_return_exchange_statuses_table', 512),
(1074, '2020_09_10_103820_update_waybill_table', 511),
(1072, '2020_09_07_062040_create_hubstaff_task_efficiency_table', 510),
(1071, '2020_09_10_115131_insert_landing_page_status_records', 509),
(1069, '2020_09_07_173458_add_status_id_to_landing_page_products_table', 508),
(1068, '2020_09_07_170143_create_landing_page_statuses_table', 508),
(1067, '2020_09_05_154853_create_task_user_histories_table', 507),
(1066, '2020_09_05_112622_create_meeting_and_other_times_table', 507),
(1065, '2020_09_04_132307_add_team_lead_and_tester_to_developer_tasks_table', 507),
(1064, '2020_09_03_111849_update_waybills_table', 507),
(1063, '2020_08_20_080227_add_indexing_to_scrap_request_histories', 507),
(1062, '2020_08_17_161146_create_scrap_request_histories_table', 507),
(1061, '2020_09_07_220721_create_routes_table', 506),
(1060, '2020_09_02_124239_create_ticket_statuses_table', 505),
(1059, '2020_09_02_123817_create_tickets_table', 505),
(1058, '2020_09_03_175532_update_call_histories_table', 504),
(1057, '2020_09_01_150426_create_status_field_return_exchange', 503),
(1054, '2020_08_28_144053_add_is_active_in_task_categories_table', 502),
(1053, '2020_08_29_101216_add_is_manual_in_hubstaff_activities_table', 501),
(1052, '2020_08_25_135409_create_store_website_id_in_landing_product-part', 500),
(1051, '2020_08_18_163923_add_lead_hubstaff_task_id_to_developer_tasks_table', 499),
(1050, '2020_08_18_153621_add_is_approved_to_developer_tasks_history_table', 499),
(1049, '2020_08_18_130243_create_task_statuses_table', 499),
(1048, '2020_08_20_120026_add_plesk_email_address_id_to_email_addresses', 498),
(1047, '2020_08_27_090131_add_tester_id_in_site_developments_table', 497),
(1046, '2020_08_25_164917_add_is_reviewed_in_chat_messages_table', 496),
(1045, '2020_08_25_033310_add_is_send_in_instagram_direct_messages_table', 496),
(1044, '2020_08_22_155215_add_instagram_user_id_in_instagram_threads_table', 496),
(1043, '2020_08_22_142258_add_new_message_in_accounts_table', 496),
(1042, '2020_08_22_130450_add_webiste_id_in_accounts_table', 496),
(1041, '2020_08_25_135409_create_store_website_id_in_landing_product', 495),
(1040, '2020_08_20_132622_add_quoted_message_id_to_chat_messages_table', 494),
(1039, '2020_08_22_142445_create_store_social_content_reviews_table', 493),
(1038, '2020_08_21_154611_add_field_model_in_developer_tasks_history_table', 492),
(1037, '2020_08_21_141703_add_due_date_to_tasks_table', 492),
(1036, '2020_08_21_121257_add_master_user_id_to_tasks_table', 492),
(1035, '2020_08_19_181338_create_store_wise_landing_page_products_table', 491),
(1034, '2020_08_19_091505_add_customer_id_to_waybills', 490),
(1033, '2020_08_18_164046_add_email_in_mailinglists_table', 489),
(1032, '2020_08_17_124351_add_twilio_active_number_id_to_twilio_call_forwarding', 488),
(1031, '2020_08_14_164919_add_twilio_credential_id_to_twilio_active_numbers', 488),
(1030, '2020_08_17_104848_convert_int_to_string_api_template_id_mailinglist_emails_table', 487),
(1029, '2020_07_08_053647_add_website_in_live_chat_users_table', 487),
(1028, '2018_07_06_165603_add_indexes_for_tasks', 486),
(1027, '2018_07_03_120000_alter_tasks_table_add_run_on_one_server_support', 486),
(1026, '2018_01_02_121533_alter_tasks_table_add_auto_cleanup_num_and_type_fields', 486),
(1025, '2017_08_26_083622_alter_tasks_table_add_notifications_fields', 486),
(1024, '2017_08_24_085132_create_frequency_parameters_table', 486),
(1023, '2017_08_05_201914_create_task_results_table', 486),
(1022, '2017_08_05_195539_create_task_frequencies_table', 486),
(1021, '2017_08_05_194349_create_tasks_table', 486),
(1020, '2020_08_11_125849_create_log_remark_table', 485),
(1019, '2020_08_13_072003_add_greeting_messages_to_store_website_twilio_numbers', 484),
(1018, '2020_08_11_115919_create_twilio_credentials_table', 483),
(1017, '2020_08_10_134334_create_twilio_call_forwarding_table', 483),
(1016, '2020_08_10_083545_create_store_website_twilio_numbers_table', 483),
(1015, '2020_08_10_065831_create_twilio_active_numbers_table', 483),
(1014, '2020_08_09_130030_add_status_in_emails_table', 482),
(1013, '2020_08_09_130010_remove_remarks_from_emails_table', 482),
(1012, '2020_08_09_122822_create_email_remarks_table', 482),
(1011, '2020_08_12_085623_add_indexing_to_chat_messages_quick_datas_table', 481),
(1010, '2020_08_11_104049_create_site_development_artowrk_histories_table', 481),
(1009, '2020_08_11_103847_add_new_columns_to_site_developments_table', 481),
(1008, '2020_08_10_130224_create_store_social_content_milestones_table', 481),
(1007, '2020_08_10_122702_add_scraper_start_at_in_scrapers_table', 481),
(1006, '2020_08_10_135646_create_keywordassigns_table', 480),
(1005, '2020_08_10_082245_add_proxy_in_accounts_table', 479),
(1004, '2020_08_08_085558_create_user_avaibilities_table', 478),
(1003, '2020_08_07_151643_add_store_website_id_to_replies_table', 477),
(1002, '2020_08_07_132502_add_sender_receiver_to_hubstaff_activity_summaries_table', 476),
(1001, '2020_08_07_145032_add_supplier_size_to_suppliers_table', 475),
(1000, '2020_08_07_144438_create_supplier_size_table', 475),
(999, '2020_08_07_020117_add_remarks_in_emails_table', 474),
(998, '2020_08_06_000837_add_origin_id_ref_id_in_emails_table', 474),
(997, '2020_08_06_000205_modify_model_type_in_emails_table', 474),
(996, '2020_08_05_235941_modify_model_id_in_emails_table', 474),
(995, '2020_08_05_155320_create_log_task_table', 473),
(994, '2020_08_05_151323_create_log_status_table', 473),
(993, '2020_08_05_104733_add_store_master_status_id_to__store_order_status_table', 472),
(992, '2020_08_05_092812_create_store_master_statuses_table', 472),
(991, '2020_08_06_111522_create_store_social_content_remarks_table', 471),
(990, '2020_08_06_100613_add_store_social_content_id_to_chat_messages_table', 471),
(989, '2020_08_06_091255_create_store_social_content_histories_table', 471),
(988, '2020_08_05_180025_create_store_social_contents_table', 471),
(987, '2020_08_05_171534_create_store_social_content_statuses_table', 471),
(986, '2020_08_05_165826_create_store_social_content_categories_table', 471),
(985, '2020_08_05_155827_create_store_social_accounts_table', 471),
(984, '2020_08_04_141926_alter_supplier_table_column_scraper', 470),
(983, '2020_08_02_170456_rename_magento_order_id_in_store_website_orders_table', 469),
(982, '2020_07_31_171302_add_customer_id_to_developer_tasks_table', 468),
(981, '2020_07_31_171031_add_customer_id_to_tasks_table', 468),
(980, '2020_08_03_112410_add_scrap_field_in_scrap_remarks_table', 467),
(979, '2020_08_01_150627_create_index_key_for_developer_task', 467),
(978, '2020_07_31_034502_alter_table_site_development_status', 466),
(977, '2020_07_30_085547_create_vendor_category_permission_table', 465),
(976, '2020_07_28_144524_alter_chat_message_quick_datas', 464),
(975, '2020_07_28_140329_add_child_scraper_in_scrapers_table', 463),
(974, '2020_07_28_141248_add_scrapper_to_suppliers', 462),
(973, '2020_07_28_112322_add_supplier_sub_category_id_to_suppliers', 462),
(972, '2020_07_28_104651_create_supplier_subcategory_table', 462),
(971, '2020_07_28_070807_add_scraped_products_index_table', 461),
(968, '2020_07_23_103860_create_store_website_colors_table', 460),
(967, '2020_07_28_121443_add_social_strategy_id_to_chat_messages_table', 459),
(966, '2020_07_28_111811_create_social_strategy_remarks_table', 459),
(965, '2020_07_28_091523_create_social_strategies_table', 459),
(964, '2020_07_27_151154_create_social_strategy_subjects_table', 459),
(963, '2020_07_27_094920_make_payment_request_id_nullable_in_payments_table', 458),
(962, '2020_07_20_135732_create_team_user_table', 458),
(961, '2020_07_24_134309_add_store_website_id_to_log_list_magentos_table', 457),
(960, '2020_07_24_152022_add_paid_in_hubstaff_activities_table', 456),
(959, '2020_07_24_145931_add_billing_dates_to_payment_receipts_table', 456),
(958, '2020_07_24_140601_create_hubstaff_activity_summaries_table', 456),
(957, '2020_07_24_115934_add_milestone_to_tasks_table', 455),
(956, '2020_07_24_100008_add_milestone_to_developer_tasks_table', 455),
(955, '2020_07_23_110253_add_status_to_hubstaff_activities_table', 454),
(954, '2020_07_22_133803_add_user_id_to_payment_receipts_table', 453),
(953, '2020_07_22_045907_add_html_field_in_store_developments_table', 452),
(952, '2020_07_22_135405_add_new_store_development_remark_table', 451),
(949, '2020_07_22_095143_add_payment_receipt_id_to_payments_table', 450),
(948, '2020_07_22_083603_add_currency_to_payment_receipts_table', 450),
(947, '2020_07_22_082930_drop_billing_dates_from_payment_receipts_table', 450),
(946, '2020_07_21_162608_add_status_and_client_remarks_to_hubstaff_activity_notifications_table', 449),
(945, '2020_07_21_143407_add_remarks_to_payment_receipt_table', 449),
(944, '2020_07_20_161434_add_cost_to_tasks_table', 449),
(943, '2020_07_20_144930_create_payment_receipts_table', 449),
(942, '2020_07_17_042051_create_store_website_orders_table', 448),
(941, '2020_07_21_081347_add_team_user_table', 447),
(940, '2020_07_18_180514_add_assigned_to_to_products_table', 446),
(939, '2020_07_13_181748_create_chat_messages_quick_datas_table', 445),
(938, '2020_07_18_162430_add_hourly_rate_to_users_table', 444),
(937, '2020_07_17_160115_create_teams_table', 444),
(936, '2020_07_17_095102_create_developer_tasks_history', 444),
(935, '2020_07_11_150258_add_website_source_to_store_website_table', 444),
(934, '2020_07_09_170636_edit_scraper_table_for_last_completed_at', 444),
(933, '2020_07_09_121135_edit_remark_table_for_is_hide', 444),
(932, '2020_07_09_110354_add_last_paid_on_to_payments', 444),
(931, '2020_07_14_203606_create_brand_category_size_chart', 443),
(930, '2020_07_14_134909_create_store_order_statuses_table', 442),
(929, '2020_07_08_132114_create_supplier_category_permission_table', 441),
(928, '2020_07_13_122502_edit_store_website_categories_table_for_cat_name', 440),
(927, '2020_07_13_011358_create_table_sku_format_history_table', 439),
(925, '2020_07_11_014607_add_suggested_reply_in_chatbot_question_table', 438),
(924, '2020_07_10_033400_add_deleted_at_and_delete_by_most_used_pharse', 437),
(923, '2020_07_10_021500_add_designer_in_site_development_table', 436),
(922, '2020_07_08_072451_add_extra_field_stock_landing_product_table', 435),
(921, '2020_07_08_072450_add_extra_field_landing_product_table', 434),
(920, '2020_07_08_063950_add_field_priority_queue_in_product_table', 433),
(919, '2020_07_07_065050_add_website_name_to_customer_live_chats_table', 433),
(918, '2020_07_06_150325_edit_vendor_table_for_some_fields', 433),
(917, '2020_07_08_093820_add_frequency_and_fixed_salary_field_to_users_table', 432),
(916, '2020_07_04_084133_add_active_to_languages_table', 431),
(915, '2020_07_04_083453_create_product_translations_table', 431),
(914, '2020_07_03_110446_edit_store_website_table_for_some_fields', 431),
(913, '2020_07_03_011358_create_product_color_history_table', 430),
(911, '2020_07_02_044220_alter_table_price_override_country_group', 429),
(910, '2020_07_02_033420_create_country_group_items_table', 428),
(909, '2020_07_02_033410_create_country_groups_table', 428),
(908, '2020_07_01_103427_add_invoice_id_to_orders_table', 428),
(907, '2020_07_01_103058_create_invoices_table', 428),
(906, '2020_07_01_061920_add_api_key_in_store_website_table', 427),
(904, '2020_06_30_075314_create_table_user_bank_information', 426),
(903, '2020_06_30_090439_add_currency_to_orders_table', 425),
(901, '2020_06_29_154553_site_development_hidden_categories_table', 424),
(900, '2020_06_25_053220_add_magneto_value_to_store_website_brands_table', 423),
(899, '2020_06_26_044220_alter_table_price_override_table', 422),
(897, '2020_06_25_115920_alter_table_store_website', 421),
(896, '2020_06_25_115914_price_override_table', 420),
(894, '2020_06_23_071220_create_duty_group_table', 419),
(893, '2020_06_23_071120_create_country_duty_table', 419),
(892, '2020_06_19_033020_store_website_product_attributes_table', 418),
(890, '2020_06_19_103320_add_category_update_user_table', 417),
(889, '2020_06_18_032220_add_hubstaff_activity_notification_table', 416),
(888, '2020_06_18_012200_alter_field_min_activity_percentage_in_table_hubstaff_members', 415),
(887, '2020_06_18_112800_alter_field_user_id_in_hubstaff_member', 414),
(886, '2020_06_16_050809_add_fields_in_hubstaff_payment_accounts', 413),
(885, '2020_06_06_073819_add_is_manual_and_is_processed_in_instagram_users_lists_table', 412),
(884, '2020_06_11_031820_add_product_verifying_users_table', 411),
(883, '2020_06_09_041420_add_status_field_in_vendor_table', 410),
(882, '2020_06_08_063420_add_product_category_histories_table', 409),
(881, '2020_06_09_011415_add_site_development_status_table', 408),
(880, '2020_06_06_030601_add_customer_kyc_documents_table', 407),
(879, '2020_06_01_115103_add_actual_start_date_in_daily_activities_table', 406),
(878, '2020_06_01_115120_add_actual_start_date_in_task_table', 405),
(877, '2020_06_03_122020_add_landing_page_product_table', 404),
(874, '2020_06_01_051420_add_shopify_id_in_product_table', 403),
(873, '2020_06_01_022110_create_store_website_goal_remarks_table', 403),
(872, '2020_06_01_022102_create_store_website_goals_table', 402),
(871, '2020_05_30_120808_create_posts_table', 401),
(870, '2020_05_29_124502_create_digital_marketing_platform_components_table', 401),
(869, '2020_05_29_032000_add_stock_status_in_product_table', 400),
(868, '2020_05_27_100103_add_general_category_id_in_task_table', 400),
(867, '2020_05_27_100103_add_general_category_id_in_daily_activities_table', 400),
(866, '2020_05_27_100102_create_general_categories_table', 400),
(865, '2020_05_27_092600_add_daily_activities_id_in_user_event_table', 399),
(864, '2020_05_23_013720_add_participants_in_user_event_table', 399),
(863, '2020_05_20_013804_create_digital_marketing_solution_researches_table', 398),
(862, '2020_05_19_024804_create_digital_marketing_usp_table', 397),
(861, '2020_05_19_024804_create_digital_marketing_solutions_table', 396),
(860, '2020_05_19_024804_create_digital_marketing_solution_attributes_table', 395),
(859, '2020_05_18_070804_create_digital_marketing_platform_table', 394),
(858, '2020_05_18_070804_create_digital_marketing_platform_remarks_table', 393),
(857, '2020_05_18_012105_add_website_user_in_customer_table', 392),
(856, '2020_05_17_020105_add_social_profile_store_website_table', 392),
(855, '2020_05_05_121243_create_page_instructions_table', 392),
(854, '2020_04_29_105826_add_site_development_id_in_chat_messages_table', 391),
(853, '2020_04_28_120143_create_site_development_categories_table', 391),
(852, '2020_04_28_115812_create_site_developments_table', 390),
(851, '2020_04_27_170328_add_posts_followes_following_location_in_instagram_users_lists_table', 389),
(850, '2020_04_27_170048_add_likes_comments_count_in_instagram_posts_table', 389),
(849, '2020_04_27_033505_create_return_exchange_histories_table', 389),
(848, '2020_04_15_061016_add_new_column_in_accounts_table', 388),
(847, '2020_04_14_0339560_add_message_template_order_status_table', 388),
(846, '2020_04_07_045829_create_new_instagram_configs_table', 388),
(845, '2020_03_21_014827_create_return_exchange_products_table', 387),
(844, '2020_03_21_012327_create_return_exchange_table', 386),
(843, '2020_03_20_1295900_add_order_status_id_field_on_order', 385),
(842, '2020_03_18_075200_add_mail_tpl_file_mailinglist_templates', 384),
(840, '2020_03_15_174400_add_full_scrape_column_in_scrapers_table', 383),
(839, '2020_03_16_160327_create_email_templates', 382),
(836, '2020_02_13_122007_make_hourly_rate_nullable_in_user_rates_table', 381),
(835, '2020_03_11_082627_create_block_web_message_list', 380),
(832, '2020_03_05_071610_add_product_id_to_scraper_products_table', 379),
(831, '2020_03_02_161427_add_currency_to_customer', 378),
(830, '2020_03_02_141935_create_currency_table', 378),
(829, '2020_02_21_153941_create_translated_products_table', 378),
(828, '2020_02_21_051837_create_languages_table', 378),
(827, '2020_03_02_112900_add_product_id_in_order_product_table', 377),
(826, '2020_02_29_080822_add_supplier_in_wetransfers_tables', 377),
(825, '2020_02_27_130255_alter_add_crop_color_in_store_websites_table', 377),
(824, '2020_01_24_115850_create_wetransfers_table', 377),
(823, '2020_02_27_122715_alter_vendor_table_reminder_fields', 376),
(822, '2020_02_27_122715_alter_customer_table_reminder_fields', 375),
(819, '2020_02_26_044415_create_database_historical_records', 374),
(817, '2020_02_26_034100_create_table_store_website_attach_brands_table', 373),
(816, '2020_02_25_053016_alter_suggestion_table_chat_id', 372),
(815, '2020_02_21_085015_create_user_event_attendees_table', 372),
(814, '2020_02_21_084943_create_user_events_table', 372),
(813, '2020_02_20_070602_alter_chatbot_replies_table', 371),
(811, '2020_02_20_000001_alter_column_in_customer_table', 370),
(809, '2019_12_07_123100_update_log_excel_imports_table_add_column_website', 369),
(808, '2019_12_06_112838_update_log_excel_imports_add_column_status', 369),
(807, '2020_02_17_044720_alter_scraped_products_table', 368),
(805, '2020_02_11_131620_add_is_active_in_permissions_table', 367),
(804, '2020_02_11_121852_add_magento_status_column', 367),
(803, '2020_02_07_113631_add_stacktrace_column', 367),
(802, '2020_01_24_151135_add_new_column_dialog_type_in_chatbot_dialogs_table', 366),
(801, '2020_02_07_122916_alter_whatsapp_config_table', 365),
(798, '2020_02_05_153128_create_payment_account_table', 364),
(797, '2020_02_05_103116_add_accounted_column_to_hubstaff_activity', 364),
(796, '2020_02_01_174257_create_laravel_log_github_table', 364),
(795, '2020_01_15_120421_add_coupon_id_to_orders_table', 364),
(794, '2020_01_15_120108_create_coupons_table', 364),
(793, '2020_01_31_034749_alter_store_website_user_password', 363),
(790, '2020_01_30_140949_create_store_website_category_table', 362),
(788, '2020_01_28_160209_create_payments_table', 361),
(787, '2020_01_28_160030_create_payment_methods_table', 361),
(786, '2020_01_28_122200_alter_store_website_remote', 359),
(785, '2020_01_25_105858_create_store_websites_table', 359),
(784, '2020_01_27_152614_add_language_column_to_customers_table_27-01-2020', 360),
(783, '2020_01_25_122530_add_new_column_type_in_product_templates_table', 360),
(782, '2020_01_24_184616_create_mailinglist_emails_table', 360),
(781, '2020_01_24_132520_create_mailing_template_files_table', 360),
(780, '2020_01_24_105424_create_user_rates_table', 360),
(779, '2020_01_24_012657_create_public_keys_table', 360),
(778, '2020_01_23_163747_create_mailinglist_templates_table', 360),
(777, '2020_01_23_155355_add_new_column_broadcast_id_in_im_queues_tables', 360),
(776, '2020_01_23_031556_add_column_last_error_to_cron_job', 359),
(775, '2020_01_23_111213_create_barcode_media_table', 359),
(774, '2020_01_28_122200_alter_store_website_remote', 358),
(771, '2020_01_25_140949_create_website_products_table', 357),
(770, '2020_01_25_105858_create_store_websites_table', 357),
(768, '2020_01_23_102342_add__hubstaff_activities_columns', 356),
(767, '2020_01_23_031556_add_column_last_error_to_cron_job', 355),
(766, '2020_01_23_071256_add_sizeeu_to_product_table', 354),
(764, '2020_01_22_121056_create_mailing_remarks_table', 353),
(763, '2020_01_21_154552_add_mailing_list_contact_table', 353),
(762, '2020_01_17_165917_create_services_table', 353),
(761, '2020_01_17_154416_create_mailinglists_table', 353),
(760, '2020_01_15_133957_create_search_queues_table', 353),
(759, '2020_01_21_154054_add_new_types_column_in_chatbot_keyword_values_table', 352),
(758, '2020_01_20_203108_create_chatbot_keyword_value_types', 352),
(757, '2020_01_17_115558_add_isflagged_and_title_to_affiliates', 352),
(756, '2020_01_14_163035_create_affiliates_table', 352),
(755, '2020_01_22_000001_add_column_to_scrappers_table', 351),
(754, '2020_01_21_150649_add_column_to_branch_state_table', 351),
(753, '2020_01_21_112613_add_branch_column_to_developer_tasks_table', 350),
(752, '2020_01_18_165259_create_github_branch_state_table', 350),
(751, '2020_01_18_151019_add_new_columns_to_scrap_influencers_tables', 350),
(750, '2020_01_17_152437_create_developer_languages_table', 349),
(749, '2020_01_17_145734_create_developer_task_documents_table', 349),
(748, '2020_01_17_105642_create_hubstaff_activities_table', 349),
(747, '2020_01_17_000001_alter_erp_priorities_user_id', 348),
(746, '2020_01_16_114409_add_hubstaff_column_to_developer_tasks', 347),
(745, '2020_01_16_113843_add_hubstaff_column_to_tasks', 347),
(744, '2020_01_08_173847_create_github_repository_groups', 347),
(743, '2020_01_08_165819_create_github_group_members', 347),
(742, '2020_01_08_165048_create_github_groups_table', 347),
(741, '2020_01_08_164331_create_github_repository_users_table', 347),
(740, '2020_01_08_163407_create_github_users_table', 347),
(739, '2020_01_08_163034_create_github_repositories_table', 347),
(738, '2020_01_07_111418_create_hubstaff_tasks_table', 347),
(737, '2020_01_07_105417_create_hubstaff_projects_table', 347),
(736, '2020_01_06_172550_change_hubstaff_token_db_type', 347),
(735, '2020_01_06_171458_add_hubstaff_user_email_column', 347),
(734, '2020_01_06_163601_add_user_hubstaff_refresh_token', 347),
(733, '2020_01_16_000001_alter_developer_task_table', 346),
(732, '2020_01_16_000000_alter_chat_message_table', 346),
(731, '2020_01_14_140859_alter_simply_duty_countries_table', 346),
(730, '2020_01_13_192327_change_format_from_integer_to_string_simply_duty_countries_table', 346),
(729, '2020_01_13_151109_add_language_column_to_suppliers_table_13-01-2020', 346),
(728, '2020_01_13_140652_add_from_destionation_columns_in_hs_code_settings_table', 346),
(727, '2020_01_12_113410_add_supplier_email_to_log_excel_imports_table', 346),
(726, '2020_01_03_152642_add_new_magento_status_column_in_order_statuses_table', 346),
(725, '2019_12_29_113054_create_hs_code_settings_table', 346),
(724, '2019_12_29_110412_create_hs_codes_table', 346),
(723, '2019_12_22_144533_add_composition_column_hs_code_groups_table', 346),
(722, '2019_12_22_024336_update_simply_duty_categories_add_correct_composition_column', 346),
(721, '2019_12_21_111329_create_hs_code_groups_categories_compositions_table', 346),
(720, '2019_12_19_171245_create_hs_code_groups_table', 346),
(719, '2020_01_14_000000_add_column_language_in_development_task', 345),
(718, '2020_01_09_163727_add_column_auto_generate_product_in_templates_table', 345),
(717, '2020_01_06_183542_add_platform_id_in_hash_tags_table', 345),
(716, '2020_01_06_181507_create_platforms_table', 345),
(715, '2020_01_09_000000_add_capacity_into_assets_manager_table', 344),
(714, '2020_01_08_071718_create_scraper_mappings_table', 344),
(713, '2020_01_08_071207_add_multiple_columns_in_scrapers_table', 344),
(712, '2020_01_08_000000_add_new_columns_in_assets_manager_table', 343),
(711, '2020_01_07_112251_create_scrap_influencers_table', 343),
(710, '2020_01_06_230946_alter_scrapers_table_add_status', 343),
(709, '2020_01_06_205933_create_chatbot_categories_table', 342),
(708, '2020_01_07_172322_add_new_columns_in_chatbot_questions_table', 341),
(707, '2020_01_05_172322_add_new_columns_in_assets_manager_table', 341),
(706, '2019_12_25_200404_create_instagram_comment_queues_table', 341),
(705, '2019_12_25_134001_add_comment_pending_accounts_table', 341),
(704, '2019_12_25_131857_add_column_is_send_comments_stats_table', 341),
(703, '2020_01_04_123721_alter_developer_tasks_object_columns', 340),
(702, '2020_01_04_090546_create_chatbot_intents_annotations_table', 339),
(701, '2020_01_02_150033_alter_supplier_add_scraped_brands', 339),
(700, '2019_12_29_173545_update_chatbot_dialogs_add_fields', 338),
(699, '2019_12_31_000000_alter_product_table_has_mediables', 337),
(698, '2019_12_29_170451_add_product_id_in_cropped_image_references_table', 337),
(697, '2019_12_29_123721_add_speed_column_in_cropped_image_references_table', 337),
(696, '2019_12_27_154621_create_log_tineye', 337),
(695, '2019_12_26_215024_add_sku_search_url_in_brands_table', 337),
(694, '2019_12_26_144419_create_visitor_logs_table', 337),
(693, '2019_12_21_201709_add_reference_column_in_developer_tasks_table', 337),
(692, '2019_12_21_191151_create_historial_datas_table', 337),
(691, '2019_12_24_135750_alter_product_suppliers_add_column_price_special', 336),
(690, '2019_12_24_131719_alter_products_add_column_price_eur_discounted', 336),
(689, '2019_12_24_131315_update_brands_add_sales_columns', 336),
(688, '2019_12_23_184626_create_chat_bot_phrase_groups_table', 336),
(687, '2019_12_23_184610_create_chat_bot_keyword_groups_table', 336),
(686, '2019_12_24_012123_create_chatbot_reply_table', 335),
(684, '2019_12_24_012122_update_chat_messages_table_add_chatbot', 334),
(683, '2019_12_23_173545_update_customers_table_add_session_id', 333),
(682, '2019_12_22_025706_create_chatbot_keyword_values_table', 333),
(681, '2019_12_22_025706_create_chatbot_keywords_table', 333),
(680, '2019_12_22_043134_create_chatbot_dialog_table', 333),
(679, '2019_12_22_043134_create_chatbot_dialog_response_table', 333),
(678, '2019_12_22_025706_create_chatbot_settings_table', 333),
(677, '2019_12_22_025706_create_chatbot_questions_table', 333),
(676, '2019_12_22_025706_create_chatbot_question_examples_table', 333),
(675, '2019_12_08_151120_update_simply_duty_calculations_add_new_columns', 332),
(674, '2019_12_07_175537_create_simply_duty_calculations_table', 332),
(673, '2019_12_07_175310_create_simply_duty_countries_table', 332),
(672, '2019_12_07_175144_create_simply_duty_currencies_table', 332),
(671, '2019_12_07_174616_create_simply_duty_categories_table', 332),
(670, '2019_12_07_173545_update_categories_table_add_simplyduty_code_column', 332),
(669, '2019_12_19_104947_alter_chat_message_phrases_table_chat_id', 331),
(668, '2019_12_19_094947_create_chat_message_phrases_table', 331),
(667, '2019_12_19_081355_create_chat_message_words_table', 331),
(662, '2019_12_19_000332_alter_categories_table_column', 330),
(661, '2019_12_18_152443_alter_auto_replies_add_column_is_active', 330),
(660, '2019_12_17_105426_create_laravel_logs_table', 330),
(659, '2019_12_14_175836_create_log_google_cses_table', 330),
(658, '2019_12_11_015840_update_brands_table_add_column_google_server_id', 330),
(657, '2019_12_18_124824_create_scrapers_table', 329),
(654, '2019_12_17_103056_alter_products_table_add_barcode_column', 328),
(653, '2019_12_17_103055_drop_category_maps', 328),
(652, '2019_12_16_132859_update_whats_app_config_is_connected_column', 328),
(651, '2019_12_15_190018_create_list_magento', 328),
(650, '2019_12_12_000332_alter_suppliers_table_add_scraper_column', 327),
(649, '2019_12_11_000332_alter_order_table_column', 326),
(648, '2019_12_09_062008_create_scrap_history_table', 326),
(647, '2019_12_08_112842_create_scrape_queues', 325),
(646, '2019_12_06_185053_update_log_excel_imports_add_column_number_products_updated', 325),
(645, '2019_12_06_102427_update_products_add_price_eur_colums', 325),
(644, '2019_12_07_000332_alter_suppliers_table_add_scraper_column', 324),
(643, '2019_12_05_195319_update_livechat_column_add_username_add_key_columns', 323),
(642, '2019_12_05_165848_create_live_chat_users_table', 323),
(641, '2019_12_05_163418_create_livechatinc_settings_table', 323),
(640, '2019_12_05_093945_update_live_chat_table_add_seen_add_status_column', 323),
(639, '2019_11_30_205052_create_customer_live_chats_table', 323),
(638, '2019_12_05_115017_create_erp_events_table', 322),
(637, '2019_12_04_000333_alter_customer_table_add_updated_by', 321),
(636, '2019_12_04_000332_alter_vendor_table_add_updated_by', 320),
(635, '2019_12_04_000331_alter_supplier_table_add_updated_by', 319),
(634, '2019_12_01_102159_update_whatsapp_config_add_status_column', 318),
(633, '2019_12_01_025124_create_instruction_times_table', 318),
(632, '2019_11_30_170950_alter_tasks_table_add_approximate', 318),
(631, '2019_11_29_173930_update_whatapp_config_table_add_sim_card_type_column', 318),
(630, '2019_11_29_130650_update_im_queue_table_add_marketing_message_type_id_column', 318),
(629, '2019_11_29_130327_create_marketing_message_types_table', 318),
(628, '2019_11_29_103258_update_whatsapp_config_table_date_add_status_device_name_sim_owner_column', 318),
(627, '2019_11_28_132530_create_scraper_results_table', 318),
(626, '2019_11_28_132015_update_scraper_table_add_scraper_total_urls_scraper_existing_urls_scraper_new_urls_columns', 318),
(625, '2019_11_17_175758_add_issue_table_column_to_developer_tasks_table', 318),
(624, '2019_10_13_023937_create_hubstaff_members', 318),
(623, '2019_11_29_000331_alter_instructions_table_add_skipped_count', 317),
(622, '2019_11_28_210113_create_erp_priorities_table', 317),
(621, '2019_11_28_113655_update_whatsapp_config_start_at_end_at_column', 316),
(620, '2019_11_27_183713_create_email_addresses_table', 316),
(619, '2019_11_27_120329_update_whatsapp_config_add_frequency_column', 316),
(618, '2019_11_22_222500_create_google_server_table', 315),
(617, '2019_11_21_124341_alter_customers_table_add_customer_next_action_id', 315),
(616, '2019_11_21_114331_create_customer_next_action_table', 315),
(615, '2019_11_21_221135_update_whatsapp_config_add_last_online_and_status_table', 314),
(614, '2019_11_21_162825_alter_chat_messages_table_add_queue', 314),
(613, '2019_11_21_115403_alter_table_customer_add_broadcast_number', 314),
(612, '2019_11_18_102326_rename_table_whats_app_configs_to_whatsapp_configs', 314),
(611, '2019_11_11_151433_update_settings_add_column_welcome_message_table', 314),
(610, '2019_11_16_160036_update_product_dispatch_table_add_column_delivery_person', 313),
(608, '2019_11_10_112446_update_sku_formats_add_sku_example_column_table', 312),
(607, '2019_11_10_100037_alter_sku_format_add_sku_format_without_color', 312),
(606, '2019_11_08_225814_create_customer_marketing_platforms_table', 312),
(605, '2019_11_08_225729_create_marketing_platforms_table', 312),
(604, '2019_11_08_225617_create_whats_app_configs_table', 312),
(603, '2019_11_07_113051_create_im_queues_table', 312),
(602, '2019_11_06_063251_alter_product_templates_table_change_product_id', 312),
(601, '2019_11_06_063057_alter_product_templates_table_drop_product_id_foreign', 312),
(600, '2019_11_04_220928_alter_templates_table_add_no_of_images', 312),
(599, '2019_11_04_110938_update_instagram_posts_table', 312),
(598, '2019_11_03_195612_create_sku_color_references', 312),
(597, '2019_11_03_164221_update_old_table_add_column_account_name', 312),
(596, '2019_11_03_102356_create_priorities_table', 312),
(595, '2019_11_03_095341_update_instagram_posts_comments_table', 312),
(594, '2019_11_03_093226_update_hash_tags_tables', 312),
(593, '2019_10_30_151141_update_chat_message_tables_add_column_message_application_id', 312),
(592, '2019_10_30_085918_create_messsage_applications_table', 312),
(591, '2019_10_17_160036_update_chat_messages_table_add_column_old_id', 312),
(590, '2019_11_01_140553_create_templates_table', 311),
(589, '2019_10_23_221950_alter_instructions_table_add_product_id', 310),
(588, '2019_10_23_162531_update_resource_images_table', 310),
(587, '2019_10_20_151020_update_quick_sell_groups_tables', 309),
(586, '2019_10_20_125122_update_quick_sell_groups_table', 309),
(585, '2019_10_19_041754_create_old_remarks_table', 309),
(584, '2019_10_19_023403_create_old_payments_table', 309),
(583, '2019_10_18_061215_alter_erp_leads_add_column_brand_segment_and_gender', 309),
(582, '2019_10_17_160036_update_chat_messages_table', 309),
(581, '2019_10_17_135959_update_old_table', 309),
(580, '2019_10_17_121121_create_old_categories_table', 309),
(579, '2019_10_19_140553_create_product_templates_table', 308),
(578, '2019_10_16_130741_create_document_send_histories_table', 307),
(577, '2019_10_14_122534_update_product_table', 307),
(576, '2019_10_14_122114_create_quick_sell_groups_table', 307),
(575, '2019_10_14_094002_create_supplier_brand_count_histories_table', 307),
(574, '2019_10_14_020213_update_supplier_brand_counts_table', 307),
(573, '2019_10_13_130441_update_products_table', 307),
(572, '2019_10_13_113036_create_product_quicksell_groups', 307),
(571, '2019_10_10_120342_update_supplier_table', 307),
(570, '2019_10_10_110957_update_vendor_table', 307),
(569, '2019_10_13_000000_create_product_disptach_table', 306),
(568, '2019_10_06_030629_create_task_attachments_table', 306),
(567, '2019_10_12_000000_create_product_location_table', 305),
(566, '2019_10_12_000000_create_courier_table', 305),
(565, '2019_10_10_220806_alter_chat_messages_add_columns_is_delivered_is_read', 305),
(564, '2019_10_11_000001_alter_table_instructions', 304),
(563, '2019_10_11_000000_create_product_location_history_table', 303),
(560, '2019_10_10_143914_create_product_quickshell_groups_table', 302),
(559, '2019_10_08_013641_update_excel_importer_tables', 302),
(558, '2019_10_06_151303_update_document_tables', 302),
(557, '2019_10_05_115329_update_vendors_table', 302),
(556, '2019_10_05_024053_update_excel_importer_table', 302),
(555, '2019_10_04_105949_alter_log_scraper_add_original_sku', 302),
(554, '2019_10_03_154609_create_excel_importer_details_table', 302),
(553, '2019_10_03_154557_create_excel_importers_table', 302),
(552, '2019_09_29_201428_add_task_type_id_to_developers_table', 302),
(551, '2019_09_29_172858_add_createdby_to_developer_tasks_table', 301),
(550, '2019_09_29_172620_create_developer_task_comments_table', 301),
(549, '2019_09_04_195101_create_document_histories_table', 301),
(548, '2019_09_04_183633_create_document_remarks_table', 301),
(547, '2019_10_03_111827_alter_page_notes_add_category_id_table', 300),
(546, '2019_10_03_111826_create_page_notes_categories_table', 299),
(545, '2019_10_01_152631_update_supplier_brand_count_table', 299),
(544, '2019_10_01_142624_create_supplier_brand_counts_table', 299),
(543, '2019_09_30_183008_create_supplier_category_counts_table', 299),
(542, '2019_09_30_155315_create_log_excel_imports_table', 299),
(541, '2019_09_29_123848_create_erp_leads_table', 298),
(540, '2019_09_29_123847_create_erp_lead_status_table', 297),
(539, '2019_09_26_172822_alter_log_scraper_add_column_raw', 297),
(538, '2019_09_25_123846_create_sku_formats_table', 297),
(537, '2019_09_25_103859_alter_purchase_product_add_column_order_product_id', 296),
(536, '2019_09_25_103858_alter_purchase_table_add_column_purchase_status_id', 296),
(535, '2019_09_25_103856_alter_purchase_table_add_column_purchase_status_id', 295),
(534, '2019_09_25_103856_create_purchase_status_table', 294),
(533, '2019_09_25_103856_create_purchase_order_customer', 293),
(532, '2019_09_24_125132_alter_log_scraper_add_column_category', 292),
(531, '2019_09_24_104429_update_password_histories_change_tables', 292),
(530, '2019_09_23_170410_update_passwords_password_histories_table', 292),
(529, '2019_09_23_170350_update_passwords_table', 292),
(528, '2019_09_23_155645_create_password_histories_table', 292),
(527, '2019_09_16_111826_create_purchase_product_supplier_table', 292),
(526, '2019_09_21_153937_create_history_whatsapp_number_table', 291),
(524, '2019_09_18_154826_create_user_logs_table', 290),
(523, '2019_09_15_153937_create_task_history_table', 290),
(522, '2019_09_15_190004_update_permission_table', 289),
(521, '2019_09_17_110709_create_page_notes_table', 288),
(520, '2019_09_14_120126_add_delete_recording_flag_in_zoom_meetings_table', 288),
(519, '2019_09_12_181343_alter_developer_tasks_add_task_type_id_column', 288),
(518, '2019_09_12_180913_create_task_types_table', 288),
(517, '2019_09_12_115712_update_chat_messages_table', 288),
(516, '2019_09_12_100418_create_whats_app_group_numbers_table', 288),
(515, '2019_09_12_100041_add_user_details_in_zoom_meetings_table', 288),
(514, '2019_09_12_095240_create_whats_app_groups_table', 288),
(513, '2019_09_11_154910_add_meeting_details_in_zoom_meetings_table', 288),
(512, '2019_09_10_110709_create_zoom_meetings_table', 288),
(511, '2019_09_12_181326_add_foreign_keys_to_bookshelves_books_table', 287),
(510, '2019_09_12_181325_create_views_table', 287),
(509, '2019_09_12_181325_create_tags_table', 287),
(508, '2019_09_12_181325_create_search_terms_table', 287),
(507, '2019_09_12_181325_create_pages_table', 287),
(506, '2019_09_12_181325_create_page_revisions_table', 287),
(505, '2019_09_12_181325_create_joint_permissions_table', 287),
(504, '2019_09_12_181325_create_images_table', 287),
(503, '2019_09_12_181325_create_entity_permissions_table', 287),
(502, '2019_09_12_181325_create_comments_table', 287),
(501, '2019_09_12_181325_create_chapters_table', 287),
(500, '2019_09_12_181325_create_bookshelves_table', 287),
(499, '2019_09_12_181325_create_bookshelves_books_table', 287),
(498, '2019_09_12_181325_create_books_table', 287),
(497, '2019_09_12_181325_create_attachments_table', 287),
(496, '2019_09_12_181325_create_activities_table', 287),
(495, '2019_09_06_090627_add_supplier_status_id_to_suppliers', 287),
(494, '2019_09_06_090418_add_supplier_category_id_to_suppliers', 287),
(493, '2019_09_06_090105_add_scraper_name_to_suppliers', 287),
(492, '2019_09_06_085813_create_supplier_status_table', 287),
(491, '2019_09_06_085208_create_supplier_category_table', 287),
(490, '2019_09_04_203809_create_s_e_ranking_table', 286),
(489, '2019_08_30_135641_update_permissions_table', 285),
(488, '2019_09_04_101334_alter_log_scraper_add_column_ip_address', 284),
(487, '2019_08_30_225702_create_se_ranking_table', 283),
(486, '2019_08_31_094229_create_permission_user_table', 282),
(485, '2019_08_30_142436_create_role_user_table', 282),
(484, '2019_08_30_140553_create_permission_role_table', 282),
(483, '2019_08_19_203458_create_article_categories_table', 282),
(482, '2019_08_19_185103_create_links_to_posts_table', 282),
(481, '2019_08_30_144421_create_assets_category_table', 281),
(480, '2019_08_30_142932_create_assets_manager_table', 281),
(479, '2019_08_30_153432_alter_brands_add_columns_for_sku', 280),
(478, '2019_08_24_152907_add_voucher_id_chat_messages_table', 279),
(477, '2019_08_20_225246_add_fields_vouchers_table', 279),
(476, '2019_08_29_153539_update_documents_table', 278),
(475, '2019_08_29_134346_create_document_categories_table', 278),
(474, '2019_08_28_160801_alter_documents_add_category', 277),
(473, '2019_08_28_125653_add_reminder_columns_in_dubbizles_table', 276),
(472, '2019_08_28_150258_add_department_id_to_users', 275),
(471, '2019_08_28_145829_create_assinged_department_menu_table', 275),
(470, '2019_08_28_145734_create_departments_table', 275),
(469, '2019_08_28_145355_create_assigned_user_pages_table', 275),
(468, '2019_08_28_144808_create_menu_pages_table', 275),
(467, '2019_08_27_103232_create_status', 274),
(466, '2019_08_26_171638_alter_scraped_products_add_column_is_excel', 273),
(465, '2019_08_26_151912_add_is_processed_for_keyword_column_in_chat_messages_table', 272),
(464, '2019_08_24_135024_alter_table_products_add_indexes_for_brand_supplier_is_on_sale_listing_approved_at', 271),
(463, '2019_08_24_112507_alter_brands_add_column_references', 270),
(462, '2019_08_23_174757_alter_log_scraper_add_column_brand', 269),
(461, '2019_08_23_142926_alter_table_products_change_column_price_to_integer', 268),
(460, '2019_08_20_200910_alter_table_scraped_products_make_columns_nullable', 267),
(459, '2019_08_20_194743_alter_table_scraped_products_add_column_currency', 267),
(458, '2019_08_20_144309_create_log_scraper', 267),
(457, '2019_08_19_010848_alter_table_categories_add_columns_for_range', 267),
(453, '2019_08_17_165823_alter_scraped_products_add_column', 266),
(452, '2019_08_17_162105_add_is_processed_column_in_bulk_customer_replies_keywords_table', 266),
(451, '2019_08_17_154656_create_bulk_customer_replies_keyword_customer_table', 266),
(450, '2019_08_16_195905_alter_table_categories', 265),
(449, '2019_08_16_132802_create_bulk_customer_replies_keywords_table', 265),
(448, '2019_08_16_013129_add_is_categorized_for_bulk_messages_column_in_customers_table', 265),
(447, '2019_08_16_142043_create_price_range', 264),
(445, '2019_08_15_164058_change_model_id_column_in_keyword_to_categories_table', 263),
(444, '2019_08_15_140006_create_keyword_to_categories_table', 262),
(443, '2019_08_15_135223_create_customer_with_category_table', 262),
(442, '2019_08_15_134745_create_customer_categories_table', 262),
(441, '2019_08_15_110727_create_product_status', 261),
(440, '2019_08_15_130427_create_failed_jobs_table', 260),
(439, '2019_08_14_202357_add_is_enhanced_column_in_products_table', 259),
(438, '2019_08_13_204531_create_user_manual_crop_table', 258),
(437, '2019_08_13_191158_log_magento', 257),
(436, '2019_08_10_231338_add_column_hubstaff_auth_token_to_users_table', 256),
(435, '2019_08_10_111011_create_monetary_accounts_table', 256),
(434, '2019_08_09_224439_create_blogger_payments_table', 256),
(433, '2019_08_08_233032_create_case_receivables_table', 256),
(432, '2019_08_08_223912_add_deleted_at_field_vendor_payments_table', 256),
(431, '2019_08_07_225844_create_vendor_payments_table', 256),
(430, '2019_08_03_122127_modify_cash_flows_table', 256),
(429, '2019_08_11_043745_alter_price_comparison', 255),
(428, '2019_08_09_100041_update_log_google_vision_reference', 254),
(427, '2019_08_07_201830_create_price_comparison', 254),
(426, '2019_08_07_084454_create_price_comparison_site', 254),
(425, '2019_08_07_114239_create_supplier_inventory', 253),
(424, '2019_08_03_200007_create_google_analytics_table', 252),
(423, '2019_08_03_173509_create_cache_table', 252),
(422, '2019_07_27_173326_create_pinterest_boards_table', 252),
(421, '2019_07_27_093800_create_pinterest_users', 252),
(420, '2019_08_03_225655_create_log_google_vision_reference', 251),
(419, '2019_08_02_045350_create_log_google_vision', 251),
(418, '2019_07_04_190529_add_facebook_id_column_in_products_table', 251),
(417, '2019_07_04_161826_fetch_composition_to_products_if_they_are_scraped', 251),
(416, '2019_08_02_193917_add_foreign_key_to_log_scraper_vs_ai', 250),
(415, '2019_08_02_110714_create_log_scraper_vs_ai', 249),
(414, '2019_08_01_122021_add_cropped_at_column_in_products_table', 248),
(413, '2019_07_31_200513_add_is_customer_flagged_column_in_complaints_table', 247),
(412, '2019_07_29_223609_create_blogger_email_templates_table', 246),
(411, '2019_07_29_185352_add_blogger_id_field_chat_messages_table', 246),
(410, '2019_07_24_191936_create_blogger_product_images_table', 246),
(409, '2019_07_24_184309_create_blogger_products_table', 246),
(408, '2019_07_24_184217_create_bloggers_table', 246),
(407, '2019_07_24_184151_create_contact_bloggers_table', 246),
(406, '2019_07_29_232443_create_old_table', 245),
(405, '2019_07_28_161448_create_old_incomings_table', 245),
(404, '2019_07_27_200735_create_back_link_checker_table', 244),
(403, '2019_07_28_154508_create_keyword_instructions_table', 243),
(402, '2019_07_24_202925_create_s_e_o_analytics_table', 242),
(401, '2019_07_22_242425_create_case_costs_table', 241),
(400, '2019_07_22_223831_add_case_id_chat_messages_table', 241),
(399, '2019_07_21_223711_create_cases_table', 241),
(398, '2019_07_21_223409_add_lawyer_id_to_chat_messages_table', 241),
(397, '2019_07_20_112533_create_lawyers_table', 241),
(396, '2019_07_20_112532_create_lawyer_specialities_table', 241),
(395, '2019_07_22_015538_add_reminder_column_in_vendors_table', 240),
(394, '2019_07_22_004219_add_reminder_column_in_suppliers_table', 239),
(393, '2019_07_21_191613_add_soft_deletes_in_accounts_table', 238),
(392, '2019_07_21_190525_add_is_verified_column_in_auto_comment_histories_table', 237),
(391, '2019_07_21_182032_create_erp_accounts_table', 236),
(390, '2019_07_19_142524_add_is_crop_skipped_column_in_products_table', 235),
(389, '2019_07_18_155534_create_users_auto_comment_histories_table', 234),
(388, '2019_07_18_133034_add_columns_for_reminders_in_customers_table', 233),
(387, '2019_07_17_164607_create_picture_colors_table', 232),
(386, '2019_07_17_162509_add_is_auto_processing_failed_column_in_products_table', 231),
(385, '2019_07_17_120717_create_color_names_references_table', 230),
(384, '2019_07_16_023245_add_facebook_id_column_in_customers_table', 229),
(383, '2019_07_16_021917_create_facebook_messages_table', 228),
(382, '2019_07_14_200501_create_cropped_image_references_table', 227),
(381, '2019_07_13_144244_add_is_updated_column_in_suppliers_table', 226),
(380, '2019_07_13_125940_create_color_references_table', 225),
(379, '2019_07_12_202347_add_deleted_at_column_in_suppliers_table', 224),
(378, '2019_07_12_182648_add_deleted_at_column_in_vendors_table', 223),
(377, '2019_07_09_175019_add_cc_bcc_columns_to_emails_table', 222),
(376, '2019_07_12_091020_resource_image', 221),
(375, '2019_07_12_091007_resource_categories', 221),
(374, '2019_07_11_192542_add_status_column_in_suppliers_table', 220),
(373, '2019_07_11_151502_add_estimate_time_column_in_issues_table', 219),
(372, '2019_07_11_133909_create_developer_messages_alert_schedules_table', 218),
(371, '2019_07_10_210834_add_instruction_completed_at_column_in_customers_table', 217),
(370, '2019_07_10_200557_add_instruction_completed_at_column_in_products_table', 216),
(369, '2019_07_10_195033_add_is_being_ordered_column_in_products_table', 215),
(368, '2019_07_08_130849_add_replace_with_column_in_compositions_table', 214),
(367, '2019_07_07_160027_add_more_columns_in_users__table', 213),
(366, '2019_07_07_124415_create_product_sizes_table', 212),
(365, '2019_07_06_194552_create_compositions_table', 211),
(364, '2019_07_05_205204_add_columns_in_users_table', 210),
(363, '2019_07_05_173507_add_user_id_column_in_vendor_categories_table', 209),
(362, '2019_07_05_134652_add_timestamps_column_in_user_products_table', 208),
(361, '2019_07_03_133429_create_listing_payments_table', 207),
(360, '2019_07_01_180910_add_was_auto_rejected_column_in_products_table', 206),
(359, '2019_07_01_151358_add_last_inventory_at_column_in_scraped_products_table', 205),
(358, '2019_06_30_191857_add_is_listing_rejected_automatically_column_in_products_table', 204),
(357, '2019_06_30_132353_add_is_titlecased_column_in_products_table', 203),
(356, '2019_06_29_174639_add_columns_in_attribute_replacements_tabke', 202),
(355, '2019_06_29_155314_add_subject_column_in_issues_table', 201),
(354, '2019_06_29_151400_create_attribute_replacements_table', 200),
(353, '2019_06_29_145926_add_more_columns_in_products_table', 199),
(352, '2019_06_28_193405_add_is_order_rejected_column_in_products_table', 198),
(351, '2019_06_28_020414_create_user_product_feedbacks_table', 197),
(350, '2019_06_28_002536_add_action_column_in_listing_histories_table', 196),
(349, '2019_06_27_211326_add_columns_in_products_table', 195),
(348, '2019_06_27_210833_add_columns_in_products_table', 194),
(347, '2019_06_27_164535_create_listing_histories_table', 193),
(346, '2019_06_27_022827_add_notes_column_in_suppliers_table', 192),
(345, '2019_06_27_022757_add_notes_column_in_vendors_table', 192),
(344, '2019_06_27_022657_add_notes_column_in_customers_table', 191),
(343, '2019_06_26_104202_add_is_crop_being_verified_column_in_products_table', 190),
(342, '2019_06_26_015608_add_customer_id_column_in_cold_leads_table', 189),
(341, '2019_06_26_011805_add_address_column_in_cold_leads_table', 188),
(340, '2019_06_26_011240_add_is_imported_in_cold_leads_table', 187),
(339, '2019_06_25_212157_add_developer_task_id_in_chat_messages_table', 186),
(338, '2019_06_25_195515_add_columns_in_developer_tasks_table', 185),
(337, '2019_06_25_125458_add_issue_id_in_chat_messages_table', 184),
(336, '2019_06_25_120958_add_columns_in_issues_table', 184),
(335, '2019_06_25_103344_add_crop_ordered_by_column_in_products_table', 183),
(334, '2019_06_25_005314_create_sops_table', 182),
(333, '2019_06_24_183439_add_columns_in_products_table', 181),
(332, '2019_06_24_161840_add_listing_remark_column_in_products_table', 180),
(331, '2019_06_21_145525_add_shipment_changed_count_to_order_products', 179),
(330, '2019_06_21_135034_add_order_product_id_to_private_viewing', 179),
(329, '2019_06_22_105859_add_is_crop_ordered_column_in_products_table', 178),
(328, '2019_06_21_202719_add_is_being_cropped_in_products_table', 177),
(327, '2019_06_21_190023_add_columns_in_instagram_posts_table', 177),
(326, '2019_06_21_181910_add_columns_in_products_table', 176);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(325, '2019_06_21_180958_add_columns_to_products_table', 175),
(324, '2019_06_20_203115_add_is_active_to_users', 174),
(323, '2019_06_19_200418_add_shipment_date_to_order_products', 173),
(322, '2019_06_17_181655_add__shipment_date_to_purchases', 173),
(321, '2019_06_20_141903_add_is_planner_completed_to_users', 172),
(320, '2019_06_19_222216_add_is_farfetched_column_in_products_table', 171),
(319, '2019_06_19_183406_add_is_crop_approved_column_in_products_table', 170),
(318, '2019_06_19_165736_add_more_columns_to_vendors', 169),
(317, '2019_06_18_203815_create_pre_accounts_table', 168),
(316, '2019_06_18_213113_add_columns_to_daily_activities', 167),
(315, '2019_06_18_163202_create_crop_amends_table', 166),
(314, '2019_06_18_134613_add_more_columns_to_tasks', 166),
(313, '2019_06_17_210828_create_user_customers_table', 165),
(312, '2019_06_17_201002_create_vendor_categories_table', 164),
(311, '2019_06_17_200907_add_category_id_to_vendors', 164),
(310, '2019_06_17_192822_add_price_discounted_to_product_suppliers', 163),
(309, '2019_06_17_183616_add_discounted_price_column_in_scraped_products_table', 162),
(308, '2019_06_17_180154_add_crop_remark_column_in_products_table', 161),
(307, '2019_06_16_125418_create_voucher_categories_table', 160),
(306, '2019_06_16_125258_add_category_id_to_vouchers', 160),
(305, '2019_06_16_182955_add_is_crop_rejected_column_in_products_table', 159),
(304, '2019_06_16_165457_add_is_approved_to_task_categories', 158),
(303, '2019_06_16_141123_add_crop_count_in_products_table', 157),
(302, '2019_06_16_112232_add_columns_in_accounts_table', 156),
(301, '2019_06_16_111658_add_is_seeding_column_in_accounts_table', 156),
(300, '2019_06_15_053844_add_original_sku_column_in_scraped_products_table', 155),
(299, '2019_06_15_112750_add_sku_to_products_suppliers', 154),
(298, '2019_06_14_234512_add_reference_column_for_categories_table', 153),
(297, '2019_06_14_200438_add_more_fields_to_product_suppliers', 152),
(296, '2019_06_14_183538_add_sending_time_to_tasks', 151),
(295, '2019_06_14_133148_add_recurring_to_vendor_products', 150),
(294, '2019_06_12_201227_add_credentials_to_vendors_table', 150),
(293, '2019_06_13_222840_add_is_price_different_to_products', 149),
(292, '2019_06_13_172949_add_columns_to_product_suppliers', 148),
(291, '2019_06_13_014103_add_columns_in_scrap_statistics_table', 147),
(290, '2019_06_12_180502_add_narrative_column_in_comments_stats_table', 146),
(289, '2019_06_12_133250_add_blocked_column_in_accounts_table', 145),
(288, '2019_06_12_121947_add_two_columns_to_vendors_table', 144),
(287, '2019_06_12_114841_add_vendor_id_to_chat_messages', 144),
(286, '2019_06_12_010716_create_scrap_statistics_table', 143),
(285, '2019_06_10_190926_add_parent_id_to_task_categories', 142),
(284, '2019_06_10_191328_add_options_to_instagram_auto_comments_table', 141),
(283, '2019_06_10_162154_add_manual_comment_column_to_accounts_table', 140),
(282, '2019_06_10_104858_add_followed_by_to_cold_leads_table', 140),
(281, '2019_06_10_131634_add_post_count_in_hash_tags_table', 139),
(280, '2019_06_09_164934_add_is_flagged_to_tasks', 138),
(279, '2019_06_08_234500_create_comments_stats_table', 137),
(278, '2019_06_08_151452_add_gender_column_in_different_table', 136),
(277, '2019_06_07_171347_add_country_column_in_accounts_table', 135),
(276, '2019_06_07_144804_add_country_column_in_auto_comment_histories_table', 135),
(275, '2019_06_07_133227_add_country_column_in_instagram_auto_comments_table', 135),
(274, '2019_06_06_162238_create_category_maps_table', 134),
(273, '2019_06_06_210602_add_is_reminder_to_messages', 133),
(272, '2019_06_06_113620_add_is_on_sale_to_products', 132),
(271, '2019_06_06_011414_add_amount_assigned_to_users', 131),
(270, '2019_06_06_010753_create_users_products_table', 131),
(269, '2019_06_05_145502_add_is_sale_column_on_scraped_products_table', 130),
(268, '2019_06_02_133615_add_is_verified_to_tasks', 129),
(267, '2019_06_04_180113_add_columns_to_suppliers_table', 128),
(266, '2019_06_02_111304_add_columns_in_auto_comment_histories_table', 127),
(265, '2019_05_28_164043_add_details_to_delivery_approvals', 126),
(264, '2019_05_28_141933_add_assigned_user_to_private_viewing', 126),
(263, '2019_06_01_130751_change_composition_type_for_products', 125),
(262, '2019_06_01_121109_add_columns_in_cold_leads_table', 124),
(261, '2019_05_31_214647_create_auto_comment_histories_table', 124),
(260, '2019_05_31_214618_create_auto_reply_hashtags_table', 124),
(259, '2019_06_01_125130_change_description_type_for_products', 123),
(258, '2019_06_01_120136_add_is_without_image_to_products', 122),
(257, '2019_05_31_200312_create_instagram_auto_comments_table', 121),
(256, '2019_05_31_202722_add_is_watched_to_tasks', 120),
(255, '2019_05_31_193216_add_customer_id_again_to_scheduled_messages', 119),
(254, '2019_05_31_191611_change_customer_id_for_scheduled_messages', 119),
(253, '2019_05_31_183407_add_data_to_scheduled_messages', 119),
(252, '2019_05_31_134458_add_reaccuring_type_to_tasks', 118),
(251, '2019_05_30_173133_add_has_error_to_suppliers', 117),
(250, '2019_05_30_142554_create_quick_replies_table', 116),
(249, '2019_05_29_180741_change_contact_phone_type', 115),
(247, '2019_05_29_172924_add_resent_to_chat_messages', 114),
(246, '2019_05_27_180855_add_priority_to_suppliers', 113),
(245, '2019_05_27_173401_add_category_to_contacts', 112),
(244, '2019_05_27_165920_add_contact_id_to_messages', 111),
(243, '2019_05_27_155748_add_type_column_to_user_tasks', 111),
(242, '2019_05_27_153427_create_contacts_table', 111),
(241, '2019_05_27_130151_add_is_processed_column_in_competitor_pages_table', 110),
(240, '2019_05_26_132553_add_broadcasted_messages_in_accounts_table', 110),
(239, '2019_05_25_203655_add_dubbizle_id_to_messages', 110),
(238, '2019_05_25_124636_create_task_users_table', 109),
(236, '2019_05_25_192648_add_account_id_in_cold_leads_table', 108),
(235, '2019_05_25_151531_add_broadcast_column_in_accounts_table', 107),
(234, '2019_05_25_150219_add_messages_sent_column_in_cold_leads_table', 107),
(233, '2019_05_25_134008_add_frequency_completed_column_in_cold_lead_broadcasts_table', 107),
(232, '2019_05_25_120709_create_lead_broadcasts_lead_table', 107),
(231, '2019_05_25_104921_create_cold_lead_broadcasts_table', 107),
(230, '2019_05_24_215641_add_private_to_tasks', 106),
(229, '2019_05_24_135636_add_columns_in_cold_leads_table', 105),
(228, '2019_05_24_133010_create_flagged_instagram_posts_table', 105),
(227, '2019_05_24_130054_create_instagram_direct_messages_table', 105),
(226, '2019_05_24_125428_add_columns_to_instagram_threads_table', 105),
(225, '2019_05_23_164729_add_task_id_to_chat_messages', 104),
(224, '2019_05_23_172100_add_cusror_column_in_competitor_pages_table', 103),
(223, '2019_05_23_164446_create_competitor_followers_table', 103),
(222, '2019_05_23_140849_add_columns_in_instagram_automated_messages_table', 103),
(221, '2019_05_22_183219_create_cron_jobs_table', 102),
(219, '2019_05_22_122019_add_columns_to_influencers_table', 101),
(218, '2019_05_21_223133_create_influencers_d_ms_table', 100),
(217, '2019_05_21_221823_create_influencers_table', 100),
(216, '2019_05_20_192644_create_dubbizles_table', 99),
(215, '2019_05_20_185649_add_title_column_in_reviews_table', 99),
(214, '2019_05_20_182923_create_activities_routines_table', 99),
(213, '2019_05_20_150209_add_is_active_column_to_accounts_table', 99),
(212, '2019_05_20_131759_create_instagram_automated_messages_table', 99),
(211, '2019_05_19_192341_create_hashtag_post_histories_table', 99),
(210, '2019_05_19_141112_add_is_image_processed_in_products_table', 98),
(209, '2019_05_19_022632_add_profile_url_in_hashtag_post_comments_table', 98),
(208, '2019_05_18_181300_create_people_names_table', 98),
(207, '2019_05_17_200009_add_is_processed_column_to_hash_tags_table', 98),
(206, '2019_05_17_191817_create_keywords_table', 98),
(205, '2019_05_17_180539_create_instagram_posts_comments_table', 98),
(204, '2019_05_17_173201_create_instagram_posts_table', 98),
(203, '2019_05_15_130026_create_instagram_users_lists_table', 98),
(202, '2019_05_15_115407_create_target_locations_table', 98),
(201, '2019_05_17_214308_add_icon_to_instructions_categories', 97),
(200, '2019_05_17_113327_add_seen_to_emails', 96),
(199, '2019_05_16_130842_add_is_color_fixed_for_scraped_products_table', 95),
(198, '2019_05_14_092828_create_sitejabber_q_a_s_table', 94),
(197, '2019_05_13_091648_create_proxies_table', 94),
(196, '2019_05_15_151114_add_type_column_to_emails', 93),
(195, '2019_05_12_221931_create_user_actions_table', 92),
(194, '2019_05_12_220619_create_competitor_pages_table', 92),
(193, '2017_12_13_150000_fix_query_arguments', 92),
(192, '2017_06_20_311102_add_agent_name_hash', 92),
(191, '2017_01_31_311101_fix_agent_name', 92),
(190, '2015_11_23_311100_add_nullable_to_tracker_error', 92),
(189, '2015_11_23_311099_add_tracker_language_foreign_key_to_sessions', 92),
(188, '2015_11_23_311098_add_language_id_column_to_sessions', 92),
(187, '2015_11_23_311097_create_tracker_languages_table', 92),
(186, '2015_11_23_311096_add_tracker_referer_column_to_log', 92),
(185, '2015_03_13_311095_add_tracker_referer_columns', 92),
(184, '2015_03_13_311094_create_tracker_referer_search_term_table', 92),
(183, '2015_03_07_311093_create_tracker_tables_relations', 92),
(182, '2015_03_07_311092_create_tracker_connections_table', 92),
(181, '2015_03_07_311091_create_tracker_sql_queries_log_table', 92),
(180, '2015_03_07_311090_create_tracker_sql_query_bindings_parameters_table', 92),
(179, '2015_03_07_311089_create_tracker_sql_query_bindings_table', 92),
(178, '2015_03_07_311088_create_tracker_sql_queries_table', 92),
(177, '2015_03_07_311087_create_tracker_events_log_table', 92),
(176, '2015_03_07_311086_create_tracker_events_table', 92),
(175, '2015_03_07_311085_create_tracker_log_table', 92),
(174, '2015_03_07_311084_create_tracker_system_classes_table', 92),
(173, '2015_03_07_311083_create_tracker_errors_table', 92),
(172, '2015_03_07_311082_create_tracker_sessions_table', 92),
(171, '2015_03_07_311081_create_tracker_geoip_table', 92),
(170, '2015_03_07_311080_create_tracker_referers_table', 92),
(169, '2015_03_07_311079_create_tracker_domains_table', 92),
(168, '2015_03_07_311078_create_tracker_devices_table', 92),
(167, '2015_03_07_311077_create_tracker_cookies_table', 92),
(166, '2015_03_07_311076_create_tracker_agents_table', 92),
(165, '2015_03_07_311075_create_tracker_route_path_parameters_table', 92),
(164, '2015_03_07_311074_create_tracker_routes_paths_table', 92),
(163, '2015_03_07_311073_create_tracker_routes_table', 92),
(162, '2015_03_07_311072_create_tracker_queries_arguments_table', 92),
(161, '2015_03_07_311071_create_tracker_queries_table', 92),
(160, '2015_03_07_311070_create_tracker_paths_table', 92),
(159, '2019_05_13_140911_add_gender_to_customers', 91),
(158, '2019_05_11_224137_create_page_screenshots_table', 90),
(157, '2019_05_11_215236_create_rejected_leads_table', 90),
(156, '2019_05_11_192940_create_automated_messages_table', 90),
(155, '2019_05_11_164810_add_because_of_column_in_cold_leads_table', 90),
(154, '2019_05_11_132253_create_targeted_accounts_table', 90),
(153, '2019_05_10_214719_create_cold_leads_table', 90),
(152, '2019_05_11_194534_add_purchase_status_to_products', 89),
(151, '2019_05_11_184921_add_proforma_details_to_purchase', 89),
(150, '2019_05_11_153926_create_purchase_discounts_table', 89),
(149, '2019_05_10_140824_add_rating_in_hashtags_table', 89),
(148, '2019_05_09_161258_add_media_id_in_complaints_table', 89),
(147, '2019_05_09_124410_make_customer_id_nullable_in_complaints_table', 89),
(146, '2019_05_09_115334_add_statuses_to_hashtag_post_comments_table', 89),
(145, '2019_05_07_075135_create_instagram_bulk_messages_table', 89),
(144, '2019_05_10_154328_default_email_to_suppliers', 88),
(143, '2019_05_10_134514_add_proforma_to_purchase', 87),
(142, '2019_05_10_113859_add_columns_to_supplier', 87),
(141, '2019_05_09_224605_add_supplier_to_chat_messages', 87),
(140, '2019_05_10_125911_add_special_special_price_to_products', 86),
(139, '2019_05_09_151706_create_scheduled_messages_table', 85),
(138, '2019_05_09_143851_add_columns_to_auto_replies', 85),
(137, '2019_05_09_133217_add_priority_to_customers', 84),
(136, '2019_05_09_131157_add_priority_to_orders', 83),
(135, '2019_05_09_111006_add_priority_to_instructions', 83),
(134, '2019_05_08_184049_create_cron_job_reports_table', 82),
(133, '2019_05_08_170114_create_hashtag_post_comments_table', 81),
(132, '2019_05_08_165531_create_hashtag_posts_table', 81),
(131, '2019_05_08_163610_adjust_columns_in_hash_tags_table', 81),
(130, '2019_05_08_123211_add_is_approved_to_products', 80),
(129, '2019_05_07_174647_add_sending_time_to_broadcast_images', 79),
(128, '2019_05_07_111340_add_error_flag_to_customers', 78),
(127, '2019_05_06_164513_add_new_columns_to_customers', 77),
(126, '2019_05_06_154415_change_qty_order_product_type', 77),
(125, '2019_05_04_195556_add_is_listed_to_products', 76),
(124, '2019_05_04_150532_create_hash_tags_table', 75),
(123, '2019_05_03_194408_add_error_status_to_chat_messages', 74),
(122, '2019_05_03_111302_add_flagged_to_customers', 73),
(121, '2019_05_02_131419_add_assigned_to_chat_messages', 72),
(120, '2019_05_02_203249_create_auto_replies_table', 71),
(119, '2019_05_01_212547_create_broadcast_images_table', 70),
(118, '2019_05_01_142718_add_subject_to_dev_tasks', 69),
(117, '2019_04_30_180050_add_type_to_complaints', 68),
(116, '2019_04_30_142142_add_status_to_complaint', 67),
(115, '2019_04_30_132939_add_platform_to_reviews', 66),
(114, '2019_04_29_182645_add_account_id_to_threads', 65),
(113, '2019_04_29_131624_add_additional_column_to_message_queues', 64),
(112, '2019_04_28_174909_change_vouchers_columns', 63),
(111, '2019_04_28_155200_create_status_changes_table', 62),
(110, '2019_04_27_144910_add_new_fields_to_suppliers', 61),
(109, '2019_04_26_205812_create_complaint_threads_table', 60),
(108, '2019_04_26_150646_create_api_keys_table', 59),
(107, '2019_04_25_195505_add_credit_column_to_customers', 58),
(106, '2019_04_25_190924_create_complaints_table', 57),
(105, '2019_04_24_150400_create_product_references_table', 56),
(104, '2019_04_24_131053_add_more_columns_to_emails', 55),
(103, '2019_04_23_204334_create_emails_table', 54),
(102, '2019_04_23_141440_add_whatsapp_number_to_agents', 53),
(101, '2019_04_23_131040_add_supplier_id_to_purchases', 53),
(100, '2019_04_22_194511_modify_reviews_table', 52),
(99, '2019_04_22_121343_create_agents_table', 51),
(98, '2019_04_22_111242_add_more_columns_to_suppliers', 51),
(97, '2019_04_21_205326_add_customer_id_to_review_schedules', 50),
(96, '2019_04_21_195825_create_suggestion_products_table', 49),
(95, '2019_04_21_194033_create_customer_suggestion_table', 49),
(94, '2019_04_21_175719_add_status_to_communication_histories', 48),
(93, '2019_04_20_200539_add_product_suppliers', 47),
(92, '2019_04_20_200204_create_suppliers_table', 47),
(91, '2019_04_20_183808_add_status_column_to_private_viewing', 46),
(90, '2019_04_20_140153_add_more_columns_to_reviews', 45),
(89, '2019_04_18_184633_create_vendor_products_table', 44),
(88, '2019_04_18_134657_add_is_uploaded_date', 43),
(87, '2019_04_17_185823_add_new_column_to_orders', 42),
(86, '2019_04_17_213948_create_scrap_counts_table', 41),
(85, '2019_04_17_143259_create_communication_histories_table', 40),
(84, '2019_04_17_113253_create_vendors_table', 39),
(82, '2019_04_14_134247_create_social_tags_table', 38),
(81, '2019_04_13_155026_create_scrap_activities_table', 37),
(80, '2019_04_13_143855_change_email_column_to_accounts', 36),
(79, '2019_04_11_205249_add_columns_count_to_table', 35),
(78, '2019_04_11_005019_add_is_blocked_to_customers', 34),
(77, '2019_04_08_232207_create_reviews_table', 33),
(76, '2019_04_08_231946_create_review_schedules_table', 33),
(75, '2019_04_08_181913_create_accounts_table', 33),
(74, '2019_04_05_231356_create_daily_cash_flows_table', 32),
(73, '2019_04_05_215716_create_budget_categories_table', 31),
(72, '2019_04_05_215318_create_budgets_table', 31),
(71, '2019_04_05_143319_create_cash_flows_table', 30),
(70, '2019_04_04_233244_create_documents_table', 29),
(69, '2019_04_04_155451_add_location_column_to_products', 28),
(68, '2019_04_03_143829_create_passwords_table', 27),
(67, '2019_03_22_163957_add_is_scraped_to_products', 26),
(66, '2019_03_22_001600_add_import_date_to_products', 25),
(65, '2019_03_21_224953_add_status_column_to_products', 24),
(64, '2019_03_19_022506_add_timing_to_instructions', 23),
(63, '2019_03_18_154219_add_approved_to_vouchers', 22),
(62, '2019_03_15_002134_add_columns_to_vouchers', 21),
(61, '2019_03_14_190251_create_files_table', 20),
(60, '2019_03_14_125237_create_ads_schedules_table', 19),
(59, '2019_03_14_125818_create_ads_schedules_attachments_table', 18),
(58, '2019_03_13_154916_add_message_id_to_message_queues', 17),
(57, '2019_03_13_152125_add_status_column_to_ms_queues', 17),
(56, '2019_03_13_145430_add_do_not_disturb_to_customers', 17),
(55, '2019_03_13_141129_add_sent_column_to_msg_queues', 17),
(54, '2019_03_12_170846_add_sent_column_to_chat_messages', 16),
(53, '2019_03_10_153733_create_message_queues_table', 15),
(49, '2019_03_09_180543_add_box_dimensions_to_waybills', 14),
(48, '2019_03_08_161044_add_is_updated_on_server_column_to_scraped_products_table', 13),
(47, '2019_03_08_155254_add_can_be_deleted_to_scraped_products_table', 12),
(46, '2019_03_07_212800_create_vouchers_table', 11),
(45, '2019_03_04_162703_add_purchase_statuses_to_products', 10),
(44, '2019_03_01_202417_add_is_enriched_to_scraped_products_table', 9),
(43, '2018_08_14_105818_create_category_table', 8),
(42, '2018_08_13_153255_create_mediable_tables', 7),
(41, '2018_08_12_191554_create_settings_table', 6),
(40, '2018_08_11_080258_create_activity_log_table', 5),
(39, '2018_08_11_064131_create_products_table', 4),
(38, '2018_08_10_092522_create_notifications_table', 3),
(37, '2018_08_08_091826_create_products_table', 2),
(36, '2018_08_07_051656_create_products_table', 1),
(35, '2018_08_06_171020_create_permission_tables', 1),
(34, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(33, '2016_06_01_000004_create_oauth_clients_table', 1),
(32, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(31, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(30, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(29, '2014_10_12_100000_create_password_resets_table', 1),
(28, '2014_10_12_000000_create_users_table', 1);


/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-10-14 12:03:13