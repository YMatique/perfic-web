-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: localhost    Database: perfic-web-app
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ai_insights`
--

DROP TABLE IF EXISTS `ai_insights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_insights` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` enum('spending_pattern','category_concentration','anomaly','trend','savings_opportunity','spending_alert','savings_tip','pattern_detected','goal_progress','budget_warning','anomaly_detected') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `impact_level` enum('low','medium','high') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `data` json DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `related_category_id` bigint unsigned DEFAULT NULL,
  `related_goal_id` bigint unsigned DEFAULT NULL,
  `impact_value` decimal(10,2) DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `is_actionable` tinyint(1) NOT NULL DEFAULT '0',
  `action_data` json DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_insights_related_category_id_foreign` (`related_category_id`),
  KEY `ai_insights_related_goal_id_foreign` (`related_goal_id`),
  KEY `ai_insights_user_id_is_read_priority_index` (`user_id`,`is_read`,`priority`),
  KEY `ai_insights_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `ai_insights_expires_at_index` (`expires_at`),
  KEY `ai_insights_category_id_foreign` (`category_id`),
  CONSTRAINT `ai_insights_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ai_insights_related_category_id_foreign` FOREIGN KEY (`related_category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ai_insights_related_goal_id_foreign` FOREIGN KEY (`related_goal_id`) REFERENCES `goals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ai_insights_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_insights`
--

LOCK TABLES `ai_insights` WRITE;
/*!40000 ALTER TABLE `ai_insights` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_insights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `behavior_patterns`
--

DROP TABLE IF EXISTS `behavior_patterns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `behavior_patterns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `pattern_type` enum('spending','income','category','temporal','location') COLLATE utf8mb4_unicode_ci NOT NULL,
  `pattern_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `average_value` decimal(10,2) NOT NULL,
  `frequency` int NOT NULL,
  `confidence` decimal(3,2) NOT NULL,
  `pattern_data` json DEFAULT NULL,
  `calculated_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `behavior_patterns_user_id_pattern_type_index` (`user_id`,`pattern_type`),
  KEY `behavior_patterns_user_id_confidence_index` (`user_id`,`confidence`),
  CONSTRAINT `behavior_patterns_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `behavior_patterns`
--

LOCK TABLES `behavior_patterns` WRITE;
/*!40000 ALTER TABLE `behavior_patterns` DISABLE KEYS */;
/*!40000 ALTER TABLE `behavior_patterns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('income','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `order` int NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_user_id_type_is_active_index` (`user_id`,`type`,`is_active`),
  KEY `categories_user_id_order_index` (`user_id`,`order`),
  CONSTRAINT `categories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,1,'Salário','income','#10b981','payments',1,1,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(2,1,'Freelances','income','#059669','work',1,2,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(3,1,'Investimentos','income','#047857','trending_up',1,3,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(4,1,'Outros Rendimentos','income','#065f46','monetization_on',1,4,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(5,1,'Alimentação','expense','#ef4444','restaurant',1,1,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(6,1,'Transporte','expense','#f97316','directions_car',1,2,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(7,1,'Moradia','expense','#eab308','home',1,3,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(8,1,'Saúde','expense','#22c55e','local_hospital',1,4,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(9,1,'Educação','expense','#3b82f6','school',1,5,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(10,1,'Compras','expense','#8b5cf6','shopping_bag',1,6,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(11,1,'Entretenimento','expense','#ec4899','movie',1,7,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(12,1,'Vestuário','expense','#f59e0b','checkroom',1,8,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(13,1,'Conta de Luz','expense','#fbbf24','flash_on',1,9,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(14,1,'Conta de Água','expense','#06b6d4','water_drop',1,10,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(15,1,'Internet/Telefone','expense','#6366f1','wifi',1,11,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(16,1,'Streaming/Assinaturas','expense','#8b5cf6','subscriptions',1,12,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(17,1,'Poupança','expense','#059669','savings',1,13,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(18,1,'Emergências','expense','#dc2626','priority_high',1,14,1,'2026-01-26 12:32:50','2026-01-26 12:32:50'),(19,1,'Outros','expense','#6b7280','more_horiz',1,15,1,'2026-01-26 12:32:50','2026-01-26 12:32:50');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorization_rules`
--

DROP TABLE IF EXISTS `categorization_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorization_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `keyword` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `confidence` decimal(3,2) NOT NULL,
  `rule_type` enum('keyword','regex','amount_range','location','merchant') COLLATE utf8mb4_unicode_ci NOT NULL,
  `rule_data` json DEFAULT NULL,
  `usage_count` int NOT NULL DEFAULT '0',
  `success_count` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_auto_generated` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categorization_rules_category_id_foreign` (`category_id`),
  KEY `categorization_rules_user_id_is_active_index` (`user_id`,`is_active`),
  KEY `categorization_rules_keyword_user_id_index` (`keyword`,`user_id`),
  KEY `categorization_rules_user_id_confidence_index` (`user_id`,`confidence`),
  CONSTRAINT `categorization_rules_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `categorization_rules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorization_rules`
--

LOCK TABLES `categorization_rules` WRITE;
/*!40000 ALTER TABLE `categorization_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `categorization_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financial_scores`
--

DROP TABLE IF EXISTS `financial_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `financial_scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `score_breakdown` json DEFAULT NULL,
  `calculated_for_month` date NOT NULL,
  `calculated_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `financial_scores_user_id_calculated_for_month_unique` (`user_id`,`calculated_for_month`),
  KEY `financial_scores_user_id_calculated_for_month_index` (`user_id`,`calculated_for_month`),
  CONSTRAINT `financial_scores_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_scores`
--

LOCK TABLES `financial_scores` WRITE;
/*!40000 ALTER TABLE `financial_scores` DISABLE KEYS */;
/*!40000 ALTER TABLE `financial_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goals`
--

DROP TABLE IF EXISTS `goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `goals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` enum('spending_limit','savings_target','category_limit','income_target') COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_amount` decimal(10,2) NOT NULL,
  `period` enum('daily','weekly','monthly','quarterly','yearly') COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `current_progress` decimal(10,2) NOT NULL DEFAULT '0.00',
  `last_calculated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goals_category_id_foreign` (`category_id`),
  KEY `goals_user_id_is_active_index` (`user_id`,`is_active`),
  KEY `goals_user_id_type_is_active_index` (`user_id`,`type`,`is_active`),
  KEY `goals_start_date_end_date_index` (`start_date`,`end_date`),
  CONSTRAINT `goals_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goals`
--

LOCK TABLES `goals` WRITE;
/*!40000 ALTER TABLE `goals` DISABLE KEYS */;
INSERT INTO `goals` VALUES (1,1,'category_limit',5,'Limite Alimentação - January 2026',15000.00,'monthly','2026-01-01','2026-01-31',1,6844.00,'2026-01-26 12:46:05','2026-01-26 12:32:51','2026-01-26 12:46:05'),(2,1,'savings_target',NULL,'Meta de Economia - January 2026',8000.00,'monthly','2026-01-01','2026-01-31',1,36540.00,'2026-01-26 12:46:05','2026-01-26 12:32:52','2026-01-26 12:46:05'),(3,1,'savings_target',1,'Reserva de Emergência 2024',100000.00,'yearly','2026-01-01','2026-12-31',1,36540.00,'2026-01-26 12:46:05','2026-01-26 12:32:52','2026-01-26 12:46:05'),(4,1,'spending_limit',NULL,'Limite Total - January 2026',35000.00,'monthly','2026-01-01','2026-01-31',1,74230.00,'2026-01-26 12:46:05','2026-01-26 12:32:52','2026-01-26 12:46:05');
/*!40000 ALTER TABLE `goals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_09_05_064351_create_tenants_table',1),(5,'2025_09_05_064516_create_categories_table',1),(6,'2025_09_05_064652_create_recurring_transactions_table',1),(7,'2025_09_05_064705_create_transactions_table',1),(8,'2025_09_05_065050_create_goals_table',1),(9,'2025_09_05_065330_create_financial_scores_table',1),(10,'2025_09_05_065627_create_behavior_patterns_table',1),(11,'2025_09_05_065828_create_ai_insights_table',1),(12,'2025_09_05_070029_create_categorization_rules_table',1),(13,'2025_09_19_111907_alter_ai_insights_add_new_fields',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recurring_transactions`
--

DROP TABLE IF EXISTS `recurring_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recurring_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `type` enum('income','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `frequency` enum('daily','weekly','monthly','bimonthly','quarterly','yearly') COLLATE utf8mb4_unicode_ci NOT NULL,
  `due_day` int DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `next_execution` timestamp NULL DEFAULT NULL,
  `last_execution` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recurring_transactions_category_id_foreign` (`category_id`),
  KEY `recurring_transactions_user_id_is_active_index` (`user_id`,`is_active`),
  KEY `recurring_transactions_next_execution_is_active_index` (`next_execution`,`is_active`),
  CONSTRAINT `recurring_transactions_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recurring_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recurring_transactions`
--

LOCK TABLES `recurring_transactions` WRITE;
/*!40000 ALTER TABLE `recurring_transactions` DISABLE KEYS */;
INSERT INTO `recurring_transactions` VALUES (1,1,1,'income',50000.00,'Salário Mensal','monthly',5,'2026-01-01',NULL,1,'2026-02-04 22:00:00',NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51'),(2,1,13,'expense',1500.00,'Conta de Luz - Automático','monthly',10,'2026-01-01',NULL,1,'2026-02-09 22:00:00',NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51'),(3,1,14,'expense',1000.00,'Conta de Água - Automático','monthly',15,'2026-01-01',NULL,1,'2026-02-14 22:00:00',NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51'),(4,1,15,'expense',2000.00,'Internet/Telefone - Automático','monthly',20,'2026-01-01',NULL,1,'2026-02-19 22:00:00',NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51'),(5,1,16,'expense',500.00,'Streaming/Assinaturas - Automático','monthly',25,'2026-01-01',NULL,1,'2026-02-24 22:00:00',NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51'),(6,1,17,'expense',5000.00,'Poupança Automática','monthly',6,'2026-01-01',NULL,1,'2026-02-05 22:00:00',NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51');
/*!40000 ALTER TABLE `recurring_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('LVwNy1cVsBZIsFy80MzKDRSBbKVtxSJrHgbidxm7',1,'127.0.0.1','Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWGdTcThGZUtzUk1nNHlCOEFKWjB2amZ3TWdXQXBVT3Y3MUxmRGdJTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZXBvcnRzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1769439255);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `settings` json DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_uuid_unique` (`uuid`),
  UNIQUE KEY `tenants_email_unique` (`email`),
  KEY `tenants_email_uuid_index` (`email`,`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `recurring_transaction_id` bigint unsigned DEFAULT NULL,
  `type` enum('income','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `transaction_date` timestamp NOT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `categorized_by_ai` tinyint(1) NOT NULL DEFAULT '0',
  `ai_confidence` decimal(3,2) DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachments` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_category_id_foreign` (`category_id`),
  KEY `transactions_recurring_transaction_id_foreign` (`recurring_transaction_id`),
  KEY `transactions_user_id_transaction_date_index` (`user_id`,`transaction_date`),
  KEY `transactions_user_id_category_id_transaction_date_index` (`user_id`,`category_id`,`transaction_date`),
  KEY `transactions_user_id_type_transaction_date_index` (`user_id`,`type`,`transaction_date`),
  KEY `transactions_transaction_date_index` (`transaction_date`),
  CONSTRAINT `transactions_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_recurring_transaction_id_foreign` FOREIGN KEY (`recurring_transaction_id`) REFERENCES `recurring_transactions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (1,1,1,NULL,'income',49940.00,'Salário - October 2025','2025-10-05 07:00:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(2,1,1,NULL,'income',45733.00,'Salário - November 2025','2025-11-16 16:00:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(3,1,2,NULL,'income',14061.00,'Projeto freelance - Design de website','2025-11-16 16:00:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(4,1,1,NULL,'income',50798.00,'Salário - December 2025','2025-12-05 07:00:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(5,1,1,NULL,'income',54565.00,'Salário - January 2026','2026-01-17 13:00:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(6,1,2,NULL,'income',21205.00,'Projeto freelance - Design de website','2026-01-17 13:00:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(7,1,6,NULL,'expense',427.00,'Combustível','2025-10-28 14:22:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(8,1,15,NULL,'expense',1960.00,'Conta de telefone','2025-10-28 14:22:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(9,1,13,NULL,'expense',1764.00,'Conta de luz','2025-10-29 14:03:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(10,1,14,NULL,'expense',1020.00,'Conta de água','2025-10-30 16:12:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(11,1,5,NULL,'expense',2633.00,'Compras no supermercado','2025-10-31 14:48:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(12,1,11,NULL,'expense',2468.00,'Cinema','2025-11-01 17:15:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(13,1,15,NULL,'expense',1507.00,'Conta de telefone','2025-11-01 17:15:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(14,1,15,NULL,'expense',2016.00,'Conta de telefone','2025-11-01 17:15:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(15,1,11,NULL,'expense',1151.00,'Jogos','2025-11-01 17:15:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(16,1,6,NULL,'expense',1276.00,'Táxi/Uber','2025-11-02 11:49:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(17,1,5,NULL,'expense',784.00,'Delivery - Pizza','2025-11-03 20:01:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(18,1,10,NULL,'expense',4001.00,'Compras online','2025-11-04 14:56:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(19,1,14,NULL,'expense',1067.00,'Conta de água','2025-11-04 14:56:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(20,1,11,NULL,'expense',1885.00,'Streaming - Netflix','2025-11-05 11:17:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(21,1,11,NULL,'expense',1380.00,'Show/Concerto','2025-11-05 11:17:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(22,1,11,NULL,'expense',837.00,'Bar com amigos','2025-11-08 18:39:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(23,1,6,NULL,'expense',1575.00,'Estacionamento','2025-11-09 12:29:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(24,1,15,NULL,'expense',1969.00,'Conta de internet','2025-11-09 12:29:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(25,1,11,NULL,'expense',2429.00,'Cinema','2025-11-11 16:58:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(26,1,13,NULL,'expense',1342.00,'Conta de luz','2025-11-12 13:53:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(27,1,5,NULL,'expense',1721.00,'Café da manhã','2025-11-12 13:53:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(28,1,11,NULL,'expense',2198.00,'Cinema','2025-11-13 18:24:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(29,1,15,NULL,'expense',2425.00,'Conta de internet','2025-11-13 18:24:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(30,1,13,NULL,'expense',1740.00,'Conta de luz','2025-11-14 18:55:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(31,1,14,NULL,'expense',1017.00,'Conta de água','2025-11-14 18:55:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(32,1,14,NULL,'expense',1105.00,'Conta de água','2025-11-15 14:19:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(33,1,10,NULL,'expense',4121.00,'Compras online','2025-11-15 14:19:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(34,1,10,NULL,'expense',2834.00,'Compras online','2025-11-15 14:19:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(35,1,13,NULL,'expense',1255.00,'Conta de luz','2025-11-15 14:19:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(36,1,5,NULL,'expense',3416.00,'Café da manhã','2025-11-16 08:51:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(37,1,13,NULL,'expense',1315.00,'Conta de luz','2025-11-16 08:51:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(38,1,13,NULL,'expense',1666.00,'Conta de luz','2025-11-17 09:01:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(39,1,6,NULL,'expense',1245.00,'Combustível','2025-11-17 09:01:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(40,1,15,NULL,'expense',2050.00,'Conta de telefone','2025-11-18 13:25:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(41,1,13,NULL,'expense',1227.00,'Conta de luz','2025-11-19 11:53:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(42,1,14,NULL,'expense',1165.00,'Conta de água','2025-11-20 15:10:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(43,1,14,NULL,'expense',1008.00,'Conta de água','2025-11-21 16:23:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(44,1,6,NULL,'expense',1217.00,'Manutenção do carro','2025-11-21 16:23:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(45,1,10,NULL,'expense',2399.00,'Compras online','2025-11-22 14:53:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(46,1,5,NULL,'expense',1641.00,'Café da manhã','2025-11-22 14:53:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(47,1,11,NULL,'expense',743.00,'Bar com amigos','2025-11-22 14:53:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(48,1,15,NULL,'expense',2374.00,'Conta de telefone','2025-11-22 14:53:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(49,1,10,NULL,'expense',1465.00,'Eletrônicos','2025-11-23 08:37:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(50,1,13,NULL,'expense',1304.00,'Conta de luz','2025-11-23 08:37:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(51,1,13,NULL,'expense',1578.00,'Conta de luz','2025-11-23 08:37:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(52,1,11,NULL,'expense',2255.00,'Jogos','2025-11-24 20:52:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(53,1,10,NULL,'expense',4657.00,'Eletrônicos','2025-11-24 20:52:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(54,1,11,NULL,'expense',2126.00,'Bar com amigos','2025-11-26 18:53:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(55,1,15,NULL,'expense',1870.00,'Conta de internet','2025-11-26 18:53:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(56,1,13,NULL,'expense',1334.00,'Conta de luz','2025-11-27 06:47:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(57,1,5,NULL,'expense',1378.00,'Delivery - Pizza','2025-11-28 13:03:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(58,1,14,NULL,'expense',870.00,'Conta de água','2025-11-29 19:59:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(59,1,10,NULL,'expense',4643.00,'Farmácia','2025-11-29 19:59:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(60,1,6,NULL,'expense',1746.00,'Combustível','2025-11-29 19:59:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(61,1,14,NULL,'expense',1080.00,'Conta de água','2025-11-30 12:57:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(62,1,5,NULL,'expense',1608.00,'Café da manhã','2025-11-30 12:57:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(63,1,5,NULL,'expense',756.00,'Feira da fruta','2025-11-30 12:57:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(64,1,10,NULL,'expense',4481.00,'Compras online','2025-12-01 18:43:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(65,1,14,NULL,'expense',1072.00,'Conta de água','2025-12-03 08:14:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(66,1,11,NULL,'expense',841.00,'Show/Concerto','2025-12-04 18:59:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(67,1,13,NULL,'expense',1683.00,'Conta de luz','2025-12-04 18:59:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(68,1,14,NULL,'expense',990.00,'Conta de água','2025-12-06 12:33:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(69,1,10,NULL,'expense',1544.00,'Compras online','2025-12-06 12:33:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(70,1,11,NULL,'expense',924.00,'Cinema','2025-12-07 09:02:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(71,1,13,NULL,'expense',1364.00,'Conta de luz','2025-12-07 09:02:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(72,1,14,NULL,'expense',812.00,'Conta de água','2025-12-11 10:52:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(73,1,13,NULL,'expense',1293.00,'Conta de luz','2025-12-11 10:52:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(74,1,15,NULL,'expense',2163.00,'Conta de internet','2025-12-12 12:58:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(75,1,5,NULL,'expense',1508.00,'Delivery - Pizza','2025-12-13 18:40:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(76,1,6,NULL,'expense',1629.00,'Combustível','2025-12-13 18:40:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(77,1,6,NULL,'expense',1357.00,'Táxi/Uber','2025-12-13 18:40:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(78,1,14,NULL,'expense',831.00,'Conta de água','2025-12-13 18:40:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(79,1,15,NULL,'expense',2277.00,'Conta de internet','2025-12-14 18:26:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(80,1,5,NULL,'expense',2831.00,'Lanche na padaria','2025-12-14 18:26:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(81,1,14,NULL,'expense',1132.00,'Conta de água','2025-12-14 18:26:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(82,1,6,NULL,'expense',336.00,'Manutenção do carro','2025-12-15 13:34:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(83,1,11,NULL,'expense',1218.00,'Streaming - Netflix','2025-12-16 19:53:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(84,1,6,NULL,'expense',659.00,'Estacionamento','2025-12-16 19:53:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(85,1,10,NULL,'expense',3638.00,'Eletrônicos','2025-12-17 07:47:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(86,1,14,NULL,'expense',916.00,'Conta de água','2025-12-17 07:47:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(87,1,15,NULL,'expense',2000.00,'Conta de telefone','2025-12-19 18:30:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(88,1,10,NULL,'expense',2091.00,'Eletrônicos','2025-12-19 18:30:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(89,1,6,NULL,'expense',1150.00,'Combustível','2025-12-20 12:10:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(90,1,15,NULL,'expense',2218.00,'Conta de telefone','2025-12-20 12:10:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(91,1,14,NULL,'expense',987.00,'Conta de água','2025-12-20 12:10:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(92,1,11,NULL,'expense',1206.00,'Bar com amigos','2025-12-21 13:09:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(93,1,6,NULL,'expense',1163.00,'Combustível','2025-12-21 13:09:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(94,1,11,NULL,'expense',941.00,'Streaming - Netflix','2025-12-21 13:09:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(95,1,6,NULL,'expense',1953.00,'Manutenção do carro','2025-12-22 16:37:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(96,1,10,NULL,'expense',3103.00,'Eletrônicos','2025-12-22 16:37:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(97,1,15,NULL,'expense',2029.00,'Conta de internet','2025-12-24 12:14:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(98,1,6,NULL,'expense',1138.00,'Estacionamento','2025-12-26 08:16:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(99,1,10,NULL,'expense',2173.00,'Eletrônicos','2025-12-26 08:16:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(100,1,15,NULL,'expense',1576.00,'Conta de internet','2025-12-27 18:06:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(101,1,11,NULL,'expense',1683.00,'Streaming - Netflix','2025-12-27 18:06:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(102,1,10,NULL,'expense',2384.00,'Farmácia','2025-12-27 18:06:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(103,1,6,NULL,'expense',1333.00,'Manutenção do carro','2025-12-28 15:11:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(104,1,15,NULL,'expense',2106.00,'Conta de internet','2025-12-29 18:36:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(105,1,10,NULL,'expense',2742.00,'Produtos de limpeza','2025-12-29 18:36:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(106,1,5,NULL,'expense',2948.00,'Café da manhã','2025-12-30 11:36:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(107,1,5,NULL,'expense',1196.00,'Delivery - Pizza','2025-12-30 11:36:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(108,1,11,NULL,'expense',832.00,'Cinema','2025-12-31 15:21:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(109,1,11,NULL,'expense',756.00,'Bar com amigos','2025-12-31 15:21:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(110,1,5,NULL,'expense',2439.00,'Lanche na padaria','2026-01-01 12:41:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(111,1,13,NULL,'expense',1671.00,'Conta de luz','2026-01-02 08:47:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(112,1,13,NULL,'expense',1778.00,'Conta de luz','2026-01-02 08:47:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(113,1,13,NULL,'expense',1502.00,'Conta de luz','2026-01-03 19:17:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(114,1,6,NULL,'expense',1955.00,'Táxi/Uber','2026-01-04 20:07:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(115,1,11,NULL,'expense',1838.00,'Bar com amigos','2026-01-04 20:07:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(116,1,5,NULL,'expense',2485.00,'Café da manhã','2026-01-04 20:07:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(117,1,14,NULL,'expense',955.00,'Conta de água','2026-01-04 20:07:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(118,1,11,NULL,'expense',1685.00,'Bar com amigos','2026-01-05 12:03:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(119,1,14,NULL,'expense',992.00,'Conta de água','2026-01-05 12:03:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(120,1,14,NULL,'expense',1104.00,'Conta de água','2026-01-06 18:06:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(121,1,10,NULL,'expense',2394.00,'Compras online','2026-01-08 06:55:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(122,1,11,NULL,'expense',1126.00,'Streaming - Netflix','2026-01-08 06:55:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(123,1,15,NULL,'expense',1712.00,'Conta de telefone','2026-01-09 14:31:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(124,1,11,NULL,'expense',1358.00,'Show/Concerto','2026-01-10 08:58:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(125,1,10,NULL,'expense',3965.00,'Eletrônicos','2026-01-11 18:51:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(126,1,13,NULL,'expense',1379.00,'Conta de luz','2026-01-12 08:32:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(127,1,13,NULL,'expense',1264.00,'Conta de luz','2026-01-12 08:32:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(128,1,5,NULL,'expense',1920.00,'Jantar no restaurante','2026-01-13 19:49:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(129,1,15,NULL,'expense',2098.00,'Conta de internet','2026-01-13 19:49:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(130,1,6,NULL,'expense',498.00,'Estacionamento','2026-01-14 09:59:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(131,1,11,NULL,'expense',433.00,'Show/Concerto','2026-01-14 09:59:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(132,1,14,NULL,'expense',1136.00,'Conta de água','2026-01-15 09:27:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(133,1,15,NULL,'expense',1663.00,'Conta de internet','2026-01-16 19:58:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(134,1,10,NULL,'expense',3911.00,'Eletrônicos','2026-01-16 19:58:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(135,1,10,NULL,'expense',3486.00,'Eletrônicos','2026-01-17 18:17:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(136,1,10,NULL,'expense',4224.00,'Produtos de limpeza','2026-01-17 18:17:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(137,1,14,NULL,'expense',1100.00,'Conta de água','2026-01-17 18:17:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(138,1,15,NULL,'expense',1920.00,'Conta de internet','2026-01-18 07:22:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(139,1,11,NULL,'expense',1899.00,'Jogos','2026-01-18 07:22:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(140,1,10,NULL,'expense',4132.00,'Compras online','2026-01-18 07:22:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(141,1,13,NULL,'expense',1484.00,'Conta de luz','2026-01-19 09:27:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(142,1,15,NULL,'expense',2141.00,'Conta de internet','2026-01-20 14:11:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(143,1,15,NULL,'expense',2224.00,'Conta de telefone','2026-01-21 10:34:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(144,1,14,NULL,'expense',1119.00,'Conta de água','2026-01-21 10:34:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(145,1,15,NULL,'expense',1506.00,'Conta de internet','2026-01-22 09:41:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(146,1,13,NULL,'expense',1705.00,'Conta de luz','2026-01-24 11:13:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(147,1,10,NULL,'expense',1890.00,'Produtos de limpeza','2026-01-24 11:13:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(148,1,10,NULL,'expense',2139.00,'Produtos de limpeza','2026-01-25 13:35:50',0,0,NULL,NULL,NULL,'2026-01-26 12:32:51','2026-01-26 12:32:51',NULL),(149,1,1,NULL,'income',35000.00,'Pagamento de Salário Janeiro','2026-01-25 22:00:00',0,0,NULL,'',NULL,'2026-01-26 12:36:46','2026-01-26 12:36:46',NULL);
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `settings` json DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Usuário Demo','demo@perfic.com',NULL,'$2y$12$YJT.Frc8exbo8rxlNYv1n.NRcCatB6cXTvkbcfDcXeQLLmhGnSt2G',NULL,NULL,'2026-01-26 12:32:50','2026-01-26 12:32:50');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-27 10:34:56
