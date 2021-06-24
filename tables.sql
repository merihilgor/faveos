-- MySQL dump 10.19  Distrib 10.3.29-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: faveo
-- ------------------------------------------------------
-- Server version	10.3.29-MariaDB-0ubuntu0.20.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `api_settings`
--

DROP TABLE IF EXISTS `api_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_settings`
--

LOCK TABLES `api_settings` WRITE;
/*!40000 ALTER TABLE `api_settings` DISABLE KEYS */;
INSERT INTO `api_settings` VALUES (1,'api_enable','1','2021-02-17 12:39:54','2021-02-17 12:39:54'),(2,'api_key_mandatory','0','2021-02-17 12:39:54','2021-02-17 12:39:54'),(3,'api_key','','2021-02-17 12:39:54','2021-02-17 12:39:54'),(4,'web_hook','','2021-03-22 12:41:16','2021-03-22 13:23:17');
/*!40000 ALTER TABLE `api_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approval_levels`
--

DROP TABLE IF EXISTS `approval_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approval_levels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `approval_workflow_id` int(10) unsigned NOT NULL,
  `match` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `order` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_levels_approval_workflow_id_foreign` (`approval_workflow_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_levels`
--

LOCK TABLES `approval_levels` WRITE;
/*!40000 ALTER TABLE `approval_levels` DISABLE KEYS */;
INSERT INTO `approval_levels` VALUES (1,'Approval From Team1',1,'all',1),(2,'Manager',2,'any',1),(3,'Level 1 ',3,'all',1),(4,'Level 1 ',4,'any',1);
/*!40000 ALTER TABLE `approval_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approval_level_approvers`
--

DROP TABLE IF EXISTS `approval_level_approvers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approval_level_approvers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `approval_level_id` int(10) unsigned NOT NULL,
  `approval_level_approver_id` int(10) unsigned NOT NULL,
  `approval_level_approver_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_level_approvers_approval_level_id_foreign` (`approval_level_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_level_approvers`
--

LOCK TABLES `approval_level_approvers` WRITE;
/*!40000 ALTER TABLE `approval_level_approvers` DISABLE KEYS */;
INSERT INTO `approval_level_approvers` VALUES (3,1,5,'App\\Model\\helpdesk\\Manage\\UserType'),(4,2,10,'App\\User'),(8,2,4,'App\\Model\\helpdesk\\Manage\\UserType'),(10,1,6,'App\\User'),(9,2,7,'App\\User'),(11,3,12,'App\\User'),(12,3,4,'App\\Model\\helpdesk\\Manage\\UserType'),(13,4,12,'App\\User'),(14,4,5,'App\\Model\\helpdesk\\Manage\\UserType'),(16,4,1,'App\\User');
/*!40000 ALTER TABLE `approval_level_approvers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approval_level_statuses`
--

DROP TABLE IF EXISTS `approval_level_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approval_level_statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `approval_level_id` int(10) unsigned NOT NULL,
  `approval_workflow_ticket_id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `match` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `order` tinyint(3) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_level_statuses_approval_level_id_foreign` (`approval_level_id`),
  KEY `approval_level_statuses_approval_workflow_ticket_id_foreign` (`approval_workflow_ticket_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_level_statuses`
--

LOCK TABLES `approval_level_statuses` WRITE;
/*!40000 ALTER TABLE `approval_level_statuses` DISABLE KEYS */;
INSERT INTO `approval_level_statuses` VALUES (2,1,2,'Approval From Team1','any',1,1,'PENDING','2021-02-20 12:38:40','2021-02-20 12:38:40'),(4,1,4,'Approval From Team1','any',1,0,'APPROVED','2021-02-23 18:39:19','2021-02-23 18:42:41'),(5,2,5,'Manager','all',1,0,'APPROVED','2021-02-24 08:07:11','2021-02-24 08:09:15'),(6,2,6,'Manager','all',1,0,'APPROVED','2021-02-24 09:40:18','2021-02-24 09:45:09'),(7,2,7,'Manager','all',1,0,'APPROVED','2021-02-24 13:20:46','2021-02-24 13:29:20'),(8,2,8,'Manager','any',1,0,'APPROVED','2021-02-24 13:33:43','2021-02-24 13:35:52'),(9,4,9,'Level 1 ','any',1,0,'APPROVED','2021-02-24 13:39:25','2021-02-24 13:41:36'),(11,3,11,'Level 1 ','all',1,0,'APPROVED','2021-02-25 12:20:22','2021-02-25 12:20:51'),(12,4,12,'Level 1 ','any',1,0,'APPROVED','2021-02-25 12:22:24','2021-02-25 12:23:05'),(13,4,13,'Level 1 ','any',1,0,'APPROVED','2021-02-25 12:27:20','2021-02-25 12:27:43'),(14,3,14,'Level 1 ','all',1,0,'APPROVED','2021-02-25 12:29:40','2021-02-25 12:30:14'),(16,4,16,'Level 1 ','any',1,0,'APPROVED','2021-02-25 13:53:06','2021-03-03 09:05:43'),(17,3,17,'Level 1 ','all',1,1,'PENDING','2021-03-03 08:24:48','2021-03-03 08:24:48'),(18,2,18,'Manager','any',1,0,'APPROVED','2021-03-04 12:30:02','2021-03-04 12:31:43'),(19,2,19,'Manager','any',1,0,'APPROVED','2021-03-11 09:07:28','2021-03-11 09:08:38');
/*!40000 ALTER TABLE `approval_level_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approval_workflows`
--

DROP TABLE IF EXISTS `approval_workflows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approval_workflows` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `action_on_approve` int(11) NOT NULL,
  `action_on_deny` int(11) NOT NULL,
  `type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_workflows_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_workflows`
--

LOCK TABLES `approval_workflows` WRITE;
/*!40000 ALTER TABLE `approval_workflows` DISABLE KEYS */;
INSERT INTO `approval_workflows` VALUES (1,'Approval Flow',1,'2021-02-20 12:22:50','2021-02-20 12:22:50',3,1,'approval_workflow'),(2,'Tiger Approval Flow',1,'2021-02-23 15:25:39','2021-02-23 15:25:39',3,1,'approval_workflow'),(3,'Test Approval Flow All',1,'2021-02-24 13:25:34','2021-02-25 13:43:33',3,10,'approval_workflow'),(4,'Test Approval Flow Any',1,'2021-02-24 13:37:20','2021-02-25 13:46:43',3,10,'approval_workflow');
/*!40000 ALTER TABLE `approval_workflows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backup_paths`
--

DROP TABLE IF EXISTS `backup_paths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backup_paths` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `backup_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup_paths`
--

LOCK TABLES `backup_paths` WRITE;
/*!40000 ALTER TABLE `backup_paths` DISABLE KEYS */;
INSERT INTO `backup_paths` VALUES (1,'/var/www/storage','2021-02-17 05:35:49','2021-02-17 05:35:49');
/*!40000 ALTER TABLE `backup_paths` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bar_notifications`
--

DROP TABLE IF EXISTS `bar_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bar_notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bar_notifications`
--

LOCK TABLES `bar_notifications` WRITE;
/*!40000 ALTER TABLE `bar_notifications` DISABLE KEYS */;
INSERT INTO `bar_notifications` VALUES (1,'new-version','A new update is available. Please <a href=\'http://dcase-fav-dev.dnext-vfal.com/check-updates\'> click here </a> to update your system to v4.4.5','2021-02-26 00:00:04','2021-03-27 00:00:03');
/*!40000 ALTER TABLE `bar_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_payment_gateways`
--

DROP TABLE IF EXISTS `billing_payment_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_payment_gateways` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `gateway_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_payment_gateways`
--

LOCK TABLES `billing_payment_gateways` WRITE;
/*!40000 ALTER TABLE `billing_payment_gateways` DISABLE KEYS */;
INSERT INTO `billing_payment_gateways` VALUES (1,'PayPal','PayPal_Rest','clientID',NULL,0,0,'2021-02-17 05:39:09','2021-02-17 05:39:09'),(2,'PayPal','PayPal_Rest','secret',NULL,0,0,'2021-02-17 05:39:09','2021-02-17 05:39:09'),(3,'PayPal','PayPal_Rest','testMode','0',0,0,'2021-02-17 05:39:09','2021-02-17 05:39:09'),(4,'Bank Transfer',NULL,NULL,NULL,1,1,'2021-02-17 05:39:09','2021-02-17 05:39:09'),(5,'Cash',NULL,NULL,NULL,1,0,'2021-02-17 05:39:09','2021-02-17 05:39:09');
/*!40000 ALTER TABLE `billing_payment_gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_hours`
--

DROP TABLE IF EXISTS `business_hours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business_hours` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_hours`
--

LOCK TABLES `business_hours` WRITE;
/*!40000 ALTER TABLE `business_hours` DISABLE KEYS */;
INSERT INTO `business_hours` VALUES (1,'Default Business-Hours','default',1,'Europe/Istanbul',1,'2021-02-17 05:35:47','2021-02-17 05:37:34');
/*!40000 ALTER TABLE `business_hours` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_schedule`
--

DROP TABLE IF EXISTS `business_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business_schedule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `business_hours_id` int(10) unsigned NOT NULL,
  `days` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `business_schedule_business_hours_id_foreign` (`business_hours_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_schedule`
--

LOCK TABLES `business_schedule` WRITE;
/*!40000 ALTER TABLE `business_schedule` DISABLE KEYS */;
INSERT INTO `business_schedule` VALUES (1,1,'Sunday','Closed','2021-02-17 05:35:47','2021-02-17 05:35:47'),(2,1,'Monday','Open_fixed','2021-02-17 05:35:47','2021-02-17 05:35:47'),(3,1,'Tuesday','Open_fixed','2021-02-17 05:35:47','2021-02-17 05:35:47'),(4,1,'Wednesday','Open_fixed','2021-02-17 05:35:47','2021-02-17 05:35:49'),(5,1,'Thursday','Open_fixed','2021-02-17 05:35:47','2021-02-17 05:35:47'),(6,1,'Friday','Open_fixed','2021-02-17 05:35:47','2021-02-17 05:35:47'),(7,1,'Saturday','Closed','2021-02-17 05:35:47','2021-02-17 05:35:47');
/*!40000 ALTER TABLE `business_schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_settings`
--

DROP TABLE IF EXISTS `common_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `option_value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `optional_field` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_settings`
--

LOCK TABLES `common_settings` WRITE;
/*!40000 ALTER TABLE `common_settings` DISABLE KEYS */;
INSERT INTO `common_settings` VALUES (1,'itil','',0,'',NULL,NULL),(2,'ticket_token_time_duration','1',NULL,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(3,'enable_rtl','0',NULL,'','2021-02-17 05:35:47','2021-03-11 06:57:29'),(4,'user_set_ticket_status','',1,'','2021-02-17 05:35:47','2021-02-23 18:49:23'),(5,'send_otp','',0,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(6,'email_mandatory','',1,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(7,'user_priority','',0,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(8,'dummy_data_installation','',0,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(9,'user_registration','',1,'','2021-02-17 05:35:47','2021-02-23 18:49:23'),(10,'user_show_org_ticket','',1,'','2021-02-17 05:35:47','2021-02-23 18:49:23'),(11,'user_reply_org_ticket','',1,'','2021-02-17 05:35:47','2021-02-23 18:49:23'),(12,'allow_users_to_create_ticket','',1,'','2021-02-17 05:35:47','2021-02-23 18:49:23'),(13,'login_restrictions','email',NULL,'','2021-02-17 05:35:47','2021-02-23 18:49:23'),(14,'micro_organization_status','',0,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(15,'dashboard-statistics','departments,agents,teams',NULL,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(16,'helptopic_link_with_type','',0,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(17,'batch_tickets','',0,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(18,'redirect_to_timeline','',0,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(19,'allow_external_login','',0,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(20,'allow_users_to_access_system_url','',0,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(21,'redirect_unauthenticated_users_to','',NULL,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(22,'validate_token_api','',NULL,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(23,'validate_api_parameter','token',NULL,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(24,'allow_organization_mngr_approve_tickets','',0,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(25,'allow_organization_dept_mngr_approve_tickets','',0,'','2021-02-17 05:35:47','2021-02-23 18:49:23'),(26,'show_gravatar_image','',1,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(27,'time_track','',0,'','2021-02-17 05:35:48','2021-02-17 05:35:48'),(28,'registration_mail_templates','multiple',NULL,'','2021-02-17 05:35:48','2021-02-17 05:35:48'),(29,'reports_max_date_range','2',NULL,'','2021-02-17 05:35:48','2021-02-17 05:35:48'),(30,'reports_records_per_file','1000',NULL,'','2021-02-17 05:35:48','2021-02-17 05:35:50'),(31,'cdn_settings','0',NULL,'','2021-02-17 05:35:49','2021-02-17 05:35:49'),(32,'user_show_cc_ticket','',1,'','2021-02-17 05:35:49','2021-02-23 18:49:23'),(33,'storage','local',NULL,'default','2021-02-17 05:39:08','2021-02-17 05:39:08'),(34,'storage','/var/www/faveo/storage/app/private',NULL,'private-root','2021-02-17 05:39:08','2021-02-17 05:39:08'),(35,'storage','/var/www/faveo/public',NULL,'public-root','2021-02-17 05:39:08','2021-02-17 05:39:08'),(36,'bill','v2.0.0',NULL,'version','2021-02-17 05:39:09','2021-02-17 05:39:09');
/*!40000 ALTER TABLE `common_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conditions`
--

DROP TABLE IF EXISTS `conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conditions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `icon` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `command` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `job_info` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `plugin_job` tinyint(1) NOT NULL DEFAULT 0,
  `plugin_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conditions`
--

LOCK TABLES `conditions` WRITE;
/*!40000 ALTER TABLE `conditions` DISABLE KEYS */;
INSERT INTO `conditions` VALUES (1,'fetching','everyMinute','2021-02-17 05:35:47','2021-02-25 13:23:38','fa fa-arrow-circle-o-down','ticket:fetch','fetching-info',1,0,NULL),(2,'notification','everyMinute','2021-02-17 05:35:47','2021-02-25 13:23:38','fa fa-line-chart','report:send','notification-info',1,0,NULL),(3,'work','yearly','2021-02-17 05:35:47','2021-02-25 13:23:38','fa fa-archive','ticket:close','work-info',1,0,NULL),(4,'escalation','everyMinute','2021-02-17 05:35:47','2021-02-25 13:23:38','fa fa-hourglass-half','send:escalation','escalation-info',1,0,NULL),(5,'recur','daily','2021-02-17 05:35:47','2021-02-25 13:23:38','fa  fa-repeat','ticket:recur','recur-info',1,0,NULL),(6,'check-updates','daily','2021-02-17 05:35:47','2021-02-25 13:23:38','fa fa-refresh','faveo:checkupdate','check-updates-info',1,0,NULL),(7,'logs','daily','2021-02-17 05:35:48','2021-02-25 13:23:38','glyphicon glyphicon-trash','logs:delete','logs-delete-info',1,0,NULL),(8,'contract-expiry-notify','daily','2021-02-17 05:39:08','2021-02-25 13:23:38','fa fa-cloud-download','contract:notification-expiry','contract-expiry-notify-info',1,1,'ServiceDesk');
/*!40000 ALTER TABLE `conditions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country_code`
--

DROP TABLE IF EXISTS `country_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `iso` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `nicename` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `iso3` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `numcode` smallint(6) NOT NULL,
  `phonecode` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `example` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=240 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country_code`
--

LOCK TABLES `country_code` WRITE;
/*!40000 ALTER TABLE `country_code` DISABLE KEYS */;
INSERT INTO `country_code` VALUES (1,'AF','AFGHANISTAN','Afghanistan','AFG',4,93,'2021-02-17 05:35:47','2021-02-17 05:35:48','70 123 4567'),(2,'AL','ALBANIA','Albania','ALB',8,355,'2021-02-17 05:35:47','2021-02-17 05:35:48','66 123 4567'),(3,'DZ','ALGERIA','Algeria','DZA',12,213,'2021-02-17 05:35:47','2021-02-17 05:35:48','551 23 45 67'),(4,'AS','AMERICAN SAMOA','American Samoa','ASM',16,1684,'2021-02-17 05:35:47','2021-02-17 05:35:48','684 733-1234'),(5,'AD','ANDORRA','Andorra','AND',20,376,'2021-02-17 05:35:47','2021-02-17 05:35:48','312 345'),(6,'AO','ANGOLA','Angola','AGO',24,244,'2021-02-17 05:35:47','2021-02-17 05:35:48','923 123 456'),(7,'AI','ANGUILLA','Anguilla','AIA',660,1264,'2021-02-17 05:35:47','2021-02-17 05:35:48','264 235-1234'),(8,'AQ','ANTARCTICA','Antarctica','NUL',0,0,'2021-02-17 05:35:47','2021-02-17 05:35:48','0'),(9,'AG','ANTIGUA AND BARBUDA','Antigua and Barbuda','ATG',28,1268,'2021-02-17 05:35:47','2021-02-17 05:35:48','268 464-1234'),(10,'AR','ARGENTINA','Argentina','ARG',32,54,'2021-02-17 05:35:47','2021-02-17 05:35:48','11 15-2345-6789'),(11,'AM','ARMENIA','Armenia','ARM',51,374,'2021-02-17 05:35:47','2021-02-17 05:35:48','77 123456'),(12,'AW','ARUBA','Aruba','ABW',533,297,'2021-02-17 05:35:47','2021-02-17 05:35:48','560 1234'),(13,'AU','AUSTRALIA','Australia','AUS',36,61,'2021-02-17 05:35:47','2021-02-17 05:35:48','412 345 678'),(14,'AT','AUSTRIA','Austria','AUT',40,43,'2021-02-17 05:35:47','2021-02-17 05:35:48','664 123456'),(15,'AZ','AZERBAIJAN','Azerbaijan','AZE',31,994,'2021-02-17 05:35:47','2021-02-17 05:35:48','40 123 45 67'),(16,'BS','BAHAMAS','Bahamas','BHS',44,1242,'2021-02-17 05:35:47','2021-02-17 05:35:48','242 359-1234'),(17,'BH','BAHRAIN','Bahrain','BHR',48,973,'2021-02-17 05:35:47','2021-02-17 05:35:48','3600 1234'),(18,'BD','BANGLADESH','Bangladesh','BGD',50,880,'2021-02-17 05:35:47','2021-02-17 05:35:48','1812-345678'),(19,'BB','BARBADOS','Barbados','BRB',52,1246,'2021-02-17 05:35:47','2021-02-17 05:35:48','246 250-1234'),(20,'BY','BELARUS','Belarus','BLR',112,375,'2021-02-17 05:35:47','2021-02-17 05:35:48','8 029 491-19-11'),(21,'BE','BELGIUM','Belgium','BEL',56,32,'2021-02-17 05:35:47','2021-02-17 05:35:48','470 12 34 56'),(22,'BZ','BELIZE','Belize','BLZ',84,501,'2021-02-17 05:35:47','2021-02-17 05:35:48','622-1234'),(23,'BJ','BENIN','Benin','BEN',204,229,'2021-02-17 05:35:47','2021-02-17 05:35:48','90 01 12 34'),(24,'BM','BERMUDA','Bermuda','BMU',60,1441,'2021-02-17 05:35:47','2021-02-17 05:35:48','441 370-1234'),(25,'BT','BHUTAN','Bhutan','BTN',64,975,'2021-02-17 05:35:47','2021-02-17 05:35:48','17 12 34 56'),(26,'BO','BOLIVIA','Bolivia','BOL',68,591,'2021-02-17 05:35:47','2021-02-17 05:35:48','71234567'),(27,'BA','BOSNIA AND HERZEGOVINA','Bosnia and Herzegovina','BIH',70,387,'2021-02-17 05:35:47','2021-02-17 05:35:48','61 123 456'),(28,'BW','BOTSWANA','Botswana','BWA',72,267,'2021-02-17 05:35:47','2021-02-17 05:35:48','71 123 456'),(29,'BV','BOUVET ISLAND','Bouvet Island','NUL',0,0,'2021-02-17 05:35:47','2021-02-17 05:35:48','0'),(30,'BR','BRAZIL','Brazil','BRA',76,55,'2021-02-17 05:35:47','2021-02-17 05:35:48','11 96123-4567'),(31,'IO','BRITISH INDIAN OCEAN TERRITORY','British Indian Ocean Territory','NUL',0,246,'2021-02-17 05:35:47','2021-02-17 05:35:48','380 1234'),(32,'BN','BRUNEI DARUSSALAM','Brunei Darussalam','BRN',96,673,'2021-02-17 05:35:47','2021-02-17 05:35:48','712 3456'),(33,'BG','BULGARIA','Bulgaria','BGR',100,359,'2021-02-17 05:35:47','2021-02-17 05:35:48','48 123 456'),(34,'BF','BURKINA FASO','Burkina Faso','BFA',854,226,'2021-02-17 05:35:47','2021-02-17 05:35:48','70 12 34 56'),(35,'BI','BURUNDI','Burundi','BDI',108,257,'2021-02-17 05:35:47','2021-02-17 05:35:48','79 56 12 34'),(36,'KH','CAMBODIA','Cambodia','KHM',116,855,'2021-02-17 05:35:47','2021-02-17 05:35:48','91 234 567'),(37,'CM','CAMEROON','Cameroon','CMR',120,237,'2021-02-17 05:35:47','2021-02-17 05:35:48','6 71 23 45 67'),(38,'CA','CANADA','Canada','CAN',124,1,'2021-02-17 05:35:47','2021-02-17 05:35:48','204 234-5678'),(39,'CV','CAPE VERDE','Cape Verde','CPV',132,238,'2021-02-17 05:35:47','2021-02-17 05:35:48','991 12 34'),(40,'KY','CAYMAN ISLANDS','Cayman Islands','CYM',136,1345,'2021-02-17 05:35:47','2021-02-17 05:35:48','345 323-1234'),(41,'CF','CENTRAL AFRICAN REPUBLIC','Central African Republic','CAF',140,236,'2021-02-17 05:35:47','2021-02-17 05:35:48','70 01 23 45'),(42,'TD','CHAD','Chad','TCD',148,235,'2021-02-17 05:35:47','2021-02-17 05:35:48','63 01 23 45'),(43,'CL','CHILE','Chile','CHL',152,56,'2021-02-17 05:35:47','2021-02-17 05:35:48','9 6123 4567'),(44,'CN','CHINA','China','CHN',156,86,'2021-02-17 05:35:47','2021-02-17 05:35:48','131 2345 6789'),(45,'CX','CHRISTMAS ISLAND','Christmas Island','NUL',0,61,'2021-02-17 05:35:47','2021-02-17 05:35:48','412 345 678'),(46,'CC','COCOS (KEELING) ISLANDS','Cocos (Keeling) Islands','NUL',0,672,'2021-02-17 05:35:47','2021-02-17 05:35:48','412 345 678'),(47,'CO','COLOMBIA','Colombia','COL',170,57,'2021-02-17 05:35:47','2021-02-17 05:35:48','321 1234567'),(48,'KM','COMOROS','Comoros','COM',174,269,'2021-02-17 05:35:47','2021-02-17 05:35:48','321 23 45'),(49,'CG','CONGO','Congo','COG',178,242,'2021-02-17 05:35:47','2021-02-17 05:35:48','991 234 567'),(50,'CD','CONGO, THE DEMOCRATIC REPUBLIC OF THE','Congo, the Democratic Republic of the','COD',180,242,'2021-02-17 05:35:47','2021-02-17 05:35:48','6 123 4567'),(51,'CK','COOK ISLANDS','Cook Islands','COK',184,682,'2021-02-17 05:35:47','2021-02-17 05:35:48','71 234'),(52,'CR','COSTA RICA','Costa Rica','CRI',188,506,'2021-02-17 05:35:47','2021-02-17 05:35:48','8312 3456'),(53,'CI','COTE DIVOIRE','Cote DIvoire','CIV',384,225,'2021-02-17 05:35:47','2021-02-17 05:35:48','1 23 45 67'),(54,'HR','CROATIA','Croatia','HRV',191,385,'2021-02-17 05:35:47','2021-02-17 05:35:48','91 234 5678'),(55,'CU','CUBA','Cuba','CUB',192,53,'2021-02-17 05:35:47','2021-02-17 05:35:48','5 1234567'),(56,'CY','CYPRUS','Cyprus','CYP',196,357,'2021-02-17 05:35:47','2021-02-17 05:35:48','96 123456'),(57,'CZ','CZECH REPUBLIC','Czech Republic','CZE',203,420,'2021-02-17 05:35:47','2021-02-17 05:35:48','601 123 456'),(58,'DK','DENMARK','Denmark','DNK',208,45,'2021-02-17 05:35:47','2021-02-17 05:35:48','20 12 34 56'),(59,'DJ','DJIBOUTI','Djibouti','DJI',262,253,'2021-02-17 05:35:47','2021-02-17 05:35:48','77 83 10 01'),(60,'DM','DOMINICA','Dominica','DMA',212,1767,'2021-02-17 05:35:47','2021-02-17 05:35:48','767 225-1234'),(61,'DO','DOMINICAN REPUBLIC','Dominican Republic','DOM',214,1809,'2021-02-17 05:35:47','2021-02-17 05:35:48','809 234-5678'),(62,'EC','ECUADOR','Ecuador','ECU',218,593,'2021-02-17 05:35:47','2021-02-17 05:35:48','99 123 4567'),(63,'EG','EGYPT','Egypt','EGY',818,20,'2021-02-17 05:35:47','2021-02-17 05:35:48','100 123 4567'),(64,'SV','EL SALVADOR','El Salvador','SLV',222,503,'2021-02-17 05:35:47','2021-02-17 05:35:48','7012 3456'),(65,'GQ','EQUATORIAL GUINEA','Equatorial Guinea','GNQ',226,240,'2021-02-17 05:35:47','2021-02-17 05:35:48','222 123 456'),(66,'ER','ERITREA','Eritrea','ERI',232,291,'2021-02-17 05:35:47','2021-02-17 05:35:48','7 123 456'),(67,'EE','ESTONIA','Estonia','EST',233,372,'2021-02-17 05:35:47','2021-02-17 05:35:48','5123 4567'),(68,'ET','ETHIOPIA','Ethiopia','ETH',231,251,'2021-02-17 05:35:47','2021-02-17 05:35:48','91 123 4567'),(69,'FK','FALKLAND ISLANDS (MALVINAS)','Falkland Islands (Malvinas)','FLK',238,500,'2021-02-17 05:35:47','2021-02-17 05:35:48','51234'),(70,'FO','FAROE ISLANDS','Faroe Islands','FRO',234,298,'2021-02-17 05:35:47','2021-02-17 05:35:48','211234'),(71,'FJ','FIJI','Fiji','FJI',242,679,'2021-02-17 05:35:47','2021-02-17 05:35:48','701 2345'),(72,'FI','FINLAND','Finland','FIN',246,358,'2021-02-17 05:35:47','2021-02-17 05:35:48','41 2345678'),(73,'FR','FRANCE','France','FRA',250,33,'2021-02-17 05:35:47','2021-02-17 05:35:48','6 12 34 56 78'),(74,'GF','FRENCH GUIANA','French Guiana','GUF',254,594,'2021-02-17 05:35:47','2021-02-17 05:35:48','694 20 12 34'),(75,'PF','FRENCH POLYNESIA','French Polynesia','PYF',258,689,'2021-02-17 05:35:47','2021-02-17 05:35:48','87 12 34 56'),(76,'TF','FRENCH SOUTHERN TERRITORIES','French Southern Territories','NUL',0,0,'2021-02-17 05:35:47','2021-02-17 05:35:48','0'),(77,'GA','GABON','Gabon','GAB',266,241,'2021-02-17 05:35:47','2021-02-17 05:35:48','6 03 12 34'),(78,'GM','GAMBIA','Gambia','GMB',270,220,'2021-02-17 05:35:47','2021-02-17 05:35:48','301 2345'),(79,'GE','GEORGIA','Georgia','GEO',268,995,'2021-02-17 05:35:47','2021-02-17 05:35:48','555 12 34 56'),(80,'DE','GERMANY','Germany','DEU',276,49,'2021-02-17 05:35:47','2021-02-17 05:35:48','1512 3456789'),(81,'GH','GHANA','Ghana','GHA',288,233,'2021-02-17 05:35:47','2021-02-17 05:35:48','23 123 4567'),(82,'GI','GIBRALTAR','Gibraltar','GIB',292,350,'2021-02-17 05:35:47','2021-02-17 05:35:48','57123456'),(83,'GR','GREECE','Greece','GRC',300,30,'2021-02-17 05:35:47','2021-02-17 05:35:48','691 234 5678'),(84,'GL','GREENLAND','Greenland','GRL',304,299,'2021-02-17 05:35:47','2021-02-17 05:35:48','22 12 34'),(85,'GD','GRENADA','Grenada','GRD',308,1473,'2021-02-17 05:35:47','2021-02-17 05:35:48','473 403-1234'),(86,'GP','GUADELOUPE','Guadeloupe','GLP',312,590,'2021-02-17 05:35:47','2021-02-17 05:35:48','690 30-1234'),(87,'GU','GUAM','Guam','GUM',316,1671,'2021-02-17 05:35:47','2021-02-17 05:35:48','671 300-1234'),(88,'GT','GUATEMALA','Guatemala','GTM',320,502,'2021-02-17 05:35:47','2021-02-17 05:35:48','5123 4567'),(89,'GN','GUINEA','Guinea','GIN',324,224,'2021-02-17 05:35:47','2021-02-17 05:35:48','601 12 34 56'),(90,'GW','GUINEA-BISSAU','Guinea-Bissau','GNB',624,245,'2021-02-17 05:35:47','2021-02-17 05:35:48','955 012 345'),(91,'GY','GUYANA','Guyana','GUY',328,592,'2021-02-17 05:35:47','2021-02-17 05:35:48','609 1234'),(92,'HT','HAITI','Haiti','HTI',332,509,'2021-02-17 05:35:47','2021-02-17 05:35:48','34 10 1234'),(93,'HM','HEARD ISLAND AND MCDONALD ISLANDS','Heard Island and Mcdonald Islands','NUL',0,0,'2021-02-17 05:35:47','2021-02-17 05:35:48','0'),(94,'VA','HOLY SEE (VATICAN CITY STATE)','Holy See (Vatican City State)','VAT',336,39,'2021-02-17 05:35:47','2021-02-17 05:35:48','0'),(95,'HN','HONDURAS','Honduras','HND',340,504,'2021-02-17 05:35:47','2021-02-17 05:35:48','9123-4567'),(96,'HK','HONG KONG','Hong Kong','HKG',344,852,'2021-02-17 05:35:47','2021-02-17 05:35:48','5123 4567'),(97,'HU','HUNGARY','Hungary','HUN',348,36,'2021-02-17 05:35:47','2021-02-17 05:35:48','20 123 4567'),(98,'IS','ICELAND','Iceland','ISL',352,354,'2021-02-17 05:35:47','2021-02-17 05:35:48','611 1234'),(99,'IN','INDIA','India','IND',356,91,'2021-02-17 05:35:47','2021-02-17 05:35:48','99876 54321'),(100,'ID','INDONESIA','Indonesia','IDN',360,62,'2021-02-17 05:35:47','2021-02-17 05:35:48','812-345-678'),(101,'IR','IRAN, ISLAMIC REPUBLIC OF','Iran, Islamic Republic of','IRN',364,98,'2021-02-17 05:35:47','2021-02-17 05:35:48','912 345 6789'),(102,'IQ','IRAQ','Iraq','IRQ',368,964,'2021-02-17 05:35:47','2021-02-17 05:35:48','791 234 5678'),(103,'IE','IRELAND','Ireland','IRL',372,353,'2021-02-17 05:35:47','2021-02-17 05:35:48','85 012 3456'),(104,'IL','ISRAEL','Israel','ISR',376,972,'2021-02-17 05:35:47','2021-02-17 05:35:48','50-123-4567'),(105,'IT','ITALY','Italy','ITA',380,39,'2021-02-17 05:35:47','2021-02-17 05:35:48','312 345 6789'),(106,'JM','JAMAICA','Jamaica','JAM',388,1876,'2021-02-17 05:35:47','2021-02-17 05:35:48','876 210-1234'),(107,'JP','JAPAN','Japan','JPN',392,81,'2021-02-17 05:35:47','2021-02-17 05:35:48','90-1234-5678'),(108,'JO','JORDAN','Jordan','JOR',400,962,'2021-02-17 05:35:47','2021-02-17 05:35:48','7 9012 3456'),(109,'KZ','KAZAKHSTAN','Kazakhstan','KAZ',398,7,'2021-02-17 05:35:47','2021-02-17 05:35:48','8 771 000 9998'),(110,'KE','KENYA','Kenya','KEN',404,254,'2021-02-17 05:35:47','2021-02-17 05:35:48','712 123456'),(111,'KI','KIRIBATI','Kiribati','KIR',296,686,'2021-02-17 05:35:47','2021-02-17 05:35:48','72012345'),(112,'KP','KOREA, DEMOCRATIC PEOPLES REPUBLIC OF','Korea, Democratic Peoples Republic of','PRK',408,850,'2021-02-17 05:35:47','2021-02-17 05:35:48','2-312-3456'),(113,'KR','KOREA, REPUBLIC OF','Korea, Republic of','KOR',410,82,'2021-02-17 05:35:47','2021-02-17 05:35:48','2-312-3456'),(114,'KW','KUWAIT','Kuwait','KWT',414,965,'2021-02-17 05:35:47','2021-02-17 05:35:48','500 12345'),(115,'KG','KYRGYZSTAN','Kyrgyzstan','KGZ',417,996,'2021-02-17 05:35:47','2021-02-17 05:35:48','700 123 456'),(116,'LA','LAO PEOPLES DEMOCRATIC REPUBLIC','Lao Peoples Democratic Republic','LAO',418,856,'2021-02-17 05:35:47','2021-02-17 05:35:48','0'),(117,'LV','LATVIA','Latvia','LVA',428,371,'2021-02-17 05:35:47','2021-02-17 05:35:48','21 234 567'),(118,'LB','LEBANON','Lebanon','LBN',422,961,'2021-02-17 05:35:47','2021-02-17 05:35:48','71 123 456'),(119,'LS','LESOTHO','Lesotho','LSO',426,266,'2021-02-17 05:35:47','2021-02-17 05:35:48','5012 3456'),(120,'LR','LIBERIA','Liberia','LBR',430,231,'2021-02-17 05:35:47','2021-02-17 05:35:48','77 012 3456'),(121,'LY','LIBYAN ARAB JAMAHIRIYA','Libyan Arab Jamahiriya','LBY',434,218,'2021-02-17 05:35:47','2021-02-17 05:35:48','91-2345678'),(122,'LI','LIECHTENSTEIN','Liechtenstein','LIE',438,423,'2021-02-17 05:35:47','2021-02-17 05:35:48','660 234 567'),(123,'LT','LITHUANIA','Lithuania','LTU',440,370,'2021-02-17 05:35:47','2021-02-17 05:35:48','8-612 34567'),(124,'LU','LUXEMBOURG','Luxembourg','LUX',442,352,'2021-02-17 05:35:47','2021-02-17 05:35:48','628 123 456'),(125,'MO','MACAO','Macao','MAC',446,853,'2021-02-17 05:35:47','2021-02-17 05:35:48','6612 3456'),(126,'MK','MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','Macedonia, the Former Yugoslav Republic of','MKD',807,389,'2021-02-17 05:35:47','2021-02-17 05:35:48','72 345 678'),(127,'MG','MADAGASCAR','Madagascar','MDG',450,261,'2021-02-17 05:35:47','2021-02-17 05:35:48','32 12 345 67'),(128,'MW','MALAWI','Malawi','MWI',454,265,'2021-02-17 05:35:47','2021-02-17 05:35:48','991 23 45 67'),(129,'MY','MALAYSIA','Malaysia','MYS',458,60,'2021-02-17 05:35:47','2021-02-17 05:35:48','12-345 6789'),(130,'MV','MALDIVES','Maldives','MDV',462,960,'2021-02-17 05:35:47','2021-02-17 05:35:48','771-2345'),(131,'ML','MALI','Mali','MLI',466,223,'2021-02-17 05:35:47','2021-02-17 05:35:48','65 01 23 45'),(132,'MT','MALTA','Malta','MLT',470,356,'2021-02-17 05:35:47','2021-02-17 05:35:48','9696 1234'),(133,'MH','MARSHALL ISLANDS','Marshall Islands','MHL',584,692,'2021-02-17 05:35:47','2021-02-17 05:35:48','235-1234'),(134,'MQ','MARTINIQUE','Martinique','MTQ',474,596,'2021-02-17 05:35:47','2021-02-17 05:35:48','696 20 12 34'),(135,'MR','MAURITANIA','Mauritania','MRT',478,222,'2021-02-17 05:35:47','2021-02-17 05:35:48','22 12 34 56'),(136,'MU','MAURITIUS','Mauritius','MUS',480,230,'2021-02-17 05:35:47','2021-02-17 05:35:48','5251 2345'),(137,'YT','MAYOTTE','Mayotte','NUL',0,269,'2021-02-17 05:35:47','2021-02-17 05:35:48','639 12 34 56'),(138,'MX','MEXICO','Mexico','MEX',484,52,'2021-02-17 05:35:47','2021-02-17 05:35:48','44 222 123 4567'),(139,'FM','MICRONESIA, FEDERATED STATES OF','Micronesia, Federated States of','FSM',583,691,'2021-02-17 05:35:47','2021-02-17 05:35:48','350 1234'),(140,'MD','MOLDOVA, REPUBLIC OF','Moldova, Republic of','MDA',498,373,'2021-02-17 05:35:47','2021-02-17 05:35:48','621 12 345'),(141,'MC','MONACO','Monaco','MCO',492,377,'2021-02-17 05:35:47','2021-02-17 05:35:48','6 12 34 56 78'),(142,'MN','MONGOLIA','Mongolia','MNG',496,976,'2021-02-17 05:35:47','2021-02-17 05:35:48','8812 3456'),(143,'MS','MONTSERRAT','Montserrat','MSR',500,1664,'2021-02-17 05:35:47','2021-02-17 05:35:48','664 492-3456'),(144,'MA','MOROCCO','Morocco','MAR',504,212,'2021-02-17 05:35:47','2021-02-17 05:35:48','650-123456'),(145,'MZ','MOZAMBIQUE','Mozambique','MOZ',508,258,'2021-02-17 05:35:47','2021-02-17 05:35:48','82 123 4567'),(146,'MM','MYANMAR','Myanmar','MMR',104,95,'2021-02-17 05:35:47','2021-02-17 05:35:48','9 212 3456'),(147,'NA','NAMIBIA','Namibia','NAM',516,264,'2021-02-17 05:35:47','2021-02-17 05:35:48','81 123 4567'),(148,'NR','NAURU','Nauru','NRU',520,674,'2021-02-17 05:35:47','2021-02-17 05:35:48','555 1234'),(149,'NP','NEPAL','Nepal','NPL',524,977,'2021-02-17 05:35:47','2021-02-17 05:35:48','984-1234567'),(150,'NL','NETHERLANDS','Netherlands','NLD',528,31,'2021-02-17 05:35:47','2021-02-17 05:35:48','6 12345678'),(151,'AN','NETHERLANDS ANTILLES','Netherlands Antilles','ANT',530,599,'2021-02-17 05:35:47','2021-02-17 05:35:48','6 12345678'),(152,'NC','NEW CALEDONIA','New Caledonia','NCL',540,687,'2021-02-17 05:35:47','2021-02-17 05:35:48','75.12.34'),(153,'NZ','NEW ZEALAND','New Zealand','NZL',554,64,'2021-02-17 05:35:47','2021-02-17 05:35:48','21 123 4567'),(154,'NI','NICARAGUA','Nicaragua','NIC',558,505,'2021-02-17 05:35:47','2021-02-17 05:35:48','8123 4567'),(155,'NE','NIGER','Niger','NER',562,227,'2021-02-17 05:35:47','2021-02-17 05:35:48','93 12 34 56'),(156,'NG','NIGERIA','Nigeria','NGA',566,234,'2021-02-17 05:35:47','2021-02-17 05:35:48','802 123 4567'),(157,'NU','NIUE','Niue','NIU',570,683,'2021-02-17 05:35:47','2021-02-17 05:35:48','1234'),(158,'NF','NORFOLK ISLAND','Norfolk Island','NFK',574,672,'2021-02-17 05:35:47','2021-02-17 05:35:48','3 81234'),(159,'MP','NORTHERN MARIANA ISLANDS','Northern Mariana Islands','MNP',580,1670,'2021-02-17 05:35:47','2021-02-17 05:35:48','670 234-5678'),(160,'NO','NORWAY','Norway','NOR',578,47,'2021-02-17 05:35:47','2021-02-17 05:35:48','406 12 345'),(161,'OM','OMAN','Oman','OMN',512,968,'2021-02-17 05:35:47','2021-02-17 05:35:48','9212 3456'),(162,'PK','PAKISTAN','Pakistan','PAK',586,92,'2021-02-17 05:35:47','2021-02-17 05:35:48','301 2345678'),(163,'PW','PALAU','Palau','PLW',585,680,'2021-02-17 05:35:47','2021-02-17 05:35:48','620 1234'),(164,'PS','PALESTINIAN TERRITORY, OCCUPIED','Palestinian Territory, Occupied','NUL',0,970,'2021-02-17 05:35:47','2021-02-17 05:35:48','599 123 456'),(165,'PA','PANAMA','Panama','PAN',591,507,'2021-02-17 05:35:47','2021-02-17 05:35:48','6001-2345'),(166,'PG','PAPUA NEW GUINEA','Papua New Guinea','PNG',598,675,'2021-02-17 05:35:47','2021-02-17 05:35:48','681 2345'),(167,'PY','PARAGUAY','Paraguay','PRY',600,595,'2021-02-17 05:35:47','2021-02-17 05:35:48','961 456789'),(168,'PE','PERU','Peru','PER',604,51,'2021-02-17 05:35:47','2021-02-17 05:35:48','912 345 678'),(169,'PH','PHILIPPINES','Philippines','PHL',608,63,'2021-02-17 05:35:47','2021-02-17 05:35:48','905 123 4567'),(170,'PN','PITCAIRN','Pitcairn','PCN',612,0,'2021-02-17 05:35:47','2021-02-17 05:35:48','0'),(171,'PL','POLAND','Poland','POL',616,48,'2021-02-17 05:35:47','2021-02-17 05:35:48','512 345 678'),(172,'PT','PORTUGAL','Portugal','PRT',620,351,'2021-02-17 05:35:47','2021-02-17 05:35:48','912 345 678'),(173,'PR','PUERTO RICO','Puerto Rico','PRI',630,1787,'2021-02-17 05:35:47','2021-02-17 05:35:48','787 234-5678'),(174,'QA','QATAR','Qatar','QAT',634,974,'2021-02-17 05:35:47','2021-02-17 05:35:48','3312 3456'),(175,'RE','REUNION','Reunion','REU',638,262,'2021-02-17 05:35:47','2021-02-17 05:35:48','692 12 34 56'),(176,'RO','ROMANIA','Romania','ROM',642,40,'2021-02-17 05:35:47','2021-02-17 05:35:48','712 345 678'),(177,'RU','RUSSIAN FEDERATION','Russian Federation','RUS',643,7,'2021-02-17 05:35:47','2021-02-17 05:35:48','8 912 345-67-89'),(178,'RW','RWANDA','Rwanda','RWA',646,250,'2021-02-17 05:35:47','2021-02-17 05:35:48','720 123 456'),(179,'SH','SAINT HELENA','Saint Helena','SHN',654,290,'2021-02-17 05:35:47','2021-02-17 05:35:48','51234'),(180,'KN','SAINT KITTS AND NEVIS','Saint Kitts and Nevis','KNA',659,1869,'2021-02-17 05:35:47','2021-02-17 05:35:48','869 765-2917'),(181,'LC','SAINT LUCIA','Saint Lucia','LCA',662,1758,'2021-02-17 05:35:47','2021-02-17 05:35:48','758 284-5678'),(182,'PM','SAINT PIERRE AND MIQUELON','Saint Pierre and Miquelon','SPM',666,508,'2021-02-17 05:35:47','2021-02-17 05:35:48','55 12 34'),(183,'VC','SAINT VINCENT AND THE GRENADINES','Saint Vincent and the Grenadines','VCT',670,1784,'2021-02-17 05:35:47','2021-02-17 05:35:48','784 430-1234'),(184,'WS','SAMOA','Samoa','WSM',882,684,'2021-02-17 05:35:47','2021-02-17 05:35:48','601234'),(185,'SM','SAN MARINO','San Marino','SMR',674,378,'2021-02-17 05:35:47','2021-02-17 05:35:48','66 66 12 12'),(186,'ST','SAO TOME AND PRINCIPE','Sao Tome and Principe','STP',678,239,'2021-02-17 05:35:47','2021-02-17 05:35:48','981 2345'),(187,'SA','SAUDI ARABIA','Saudi Arabia','SAU',682,966,'2021-02-17 05:35:47','2021-02-17 05:35:48','51 234 5678'),(188,'SN','SENEGAL','Senegal','SEN',686,221,'2021-02-17 05:35:47','2021-02-17 05:35:48','70 123 45 67'),(189,'CS','SERBIA AND MONTENEGRO','Serbia and Montenegro','NUL',0,381,'2021-02-17 05:35:47','2021-02-17 05:35:48','60 1234567'),(190,'SC','SEYCHELLES','Seychelles','SYC',690,248,'2021-02-17 05:35:47','2021-02-17 05:35:48','2 510 123'),(191,'SL','SIERRA LEONE','Sierra Leone','SLE',694,232,'2021-02-17 05:35:47','2021-02-17 05:35:48','25 123456'),(192,'SG','SINGAPORE','Singapore','SGP',702,65,'2021-02-17 05:35:47','2021-02-17 05:35:48','8123 4567'),(193,'SK','SLOVAKIA','Slovakia','SVK',703,421,'2021-02-17 05:35:47','2021-02-17 05:35:48','912 123 456'),(194,'SI','SLOVENIA','Slovenia','SVN',705,386,'2021-02-17 05:35:47','2021-02-17 05:35:48','31 234 567'),(195,'SB','SOLOMON ISLANDS','Solomon Islands','SLB',90,677,'2021-02-17 05:35:47','2021-02-17 05:35:48','74 21234'),(196,'SO','SOMALIA','Somalia','SOM',706,252,'2021-02-17 05:35:47','2021-02-17 05:35:48','7 1123456'),(197,'ZA','SOUTH AFRICA','South Africa','ZAF',710,27,'2021-02-17 05:35:47','2021-02-17 05:35:48','71 123 4567'),(198,'GS','SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS','South Georgia and the South Sandwich Islands','NUL',0,0,'2021-02-17 05:35:47','2021-02-17 05:35:48','0'),(199,'ES','SPAIN','Spain','ESP',724,34,'2021-02-17 05:35:47','2021-02-17 05:35:48','612 34 56 78'),(200,'LK','SRI LANKA','Sri Lanka','LKA',144,94,'2021-02-17 05:35:47','2021-02-17 05:35:48','71 234 5678'),(201,'SD','SUDAN','Sudan','SDN',736,249,'2021-02-17 05:35:47','2021-02-17 05:35:48','91 123 1234'),(202,'SR','SURINAME','Suriname','SUR',740,597,'2021-02-17 05:35:47','2021-02-17 05:35:48','741-2345'),(203,'SJ','SVALBARD AND JAN MAYEN','Svalbard and Jan Mayen','SJM',744,47,'2021-02-17 05:35:47','2021-02-17 05:35:48','412 34 567'),(204,'SZ','SWAZILAND','Swaziland','SWZ',748,268,'2021-02-17 05:35:47','2021-02-17 05:35:48','7612 3456'),(205,'SE','SWEDEN','Sweden','SWE',752,46,'2021-02-17 05:35:47','2021-02-17 05:35:48','70-123 45 67'),(206,'CH','SWITZERLAND','Switzerland','CHE',756,41,'2021-02-17 05:35:47','2021-02-17 05:35:48','78 123 45 67'),(207,'SY','SYRIAN ARAB REPUBLIC','Syrian Arab Republic','SYR',760,963,'2021-02-17 05:35:47','2021-02-17 05:35:48','944 567 890'),(208,'TW','TAIWAN, PROVINCE OF CHINA','Taiwan, Province of China','TWN',158,886,'2021-02-17 05:35:47','2021-02-17 05:35:48','912 345 678'),(209,'TJ','TAJIKISTAN','Tajikistan','TJK',762,992,'2021-02-17 05:35:47','2021-02-17 05:35:48','8 917 12 3456'),(210,'TZ','TANZANIA, UNITED REPUBLIC OF','Tanzania, United Republic of','TZA',834,255,'2021-02-17 05:35:47','2021-02-17 05:35:48','621 234 567'),(211,'TH','THAILAND','Thailand','THA',764,66,'2021-02-17 05:35:47','2021-02-17 05:35:48','81 234 5678'),(212,'TL','TIMOR-LESTE','Timor-Leste','NUL',0,670,'2021-02-17 05:35:47','2021-02-17 05:35:48','7721 2345'),(213,'TG','TOGO','Togo','TGO',768,228,'2021-02-17 05:35:47','2021-02-17 05:35:48','90 11 23 45'),(214,'TK','TOKELAU','Tokelau','TKL',772,690,'2021-02-17 05:35:47','2021-02-17 05:35:48','7290'),(215,'TO','TONGA','Tonga','TON',776,676,'2021-02-17 05:35:47','2021-02-17 05:35:48','771 5123'),(216,'TT','TRINIDAD AND TOBAGO','Trinidad and Tobago','TTO',780,1868,'2021-02-17 05:35:47','2021-02-17 05:35:48','868 291-1234'),(217,'TN','TUNISIA','Tunisia','TUN',788,216,'2021-02-17 05:35:47','2021-02-17 05:35:48','20 123 456'),(218,'TR','TURKEY','Turkey','TUR',792,90,'2021-02-17 05:35:47','2021-02-17 05:35:48','501 234 56 78'),(219,'TM','TURKMENISTAN','Turkmenistan','TKM',795,7370,'2021-02-17 05:35:47','2021-02-17 05:35:48','8 66 123456'),(220,'TC','TURKS AND CAICOS ISLANDS','Turks and Caicos Islands','TCA',796,1649,'2021-02-17 05:35:47','2021-02-17 05:35:48','649 231-1234'),(221,'TV','TUVALU','Tuvalu','TUV',798,688,'2021-02-17 05:35:47','2021-02-17 05:35:48','901234'),(222,'UG','UGANDA','Uganda','UGA',800,256,'2021-02-17 05:35:47','2021-02-17 05:35:48','712 345678'),(223,'UA','UKRAINE','Ukraine','UKR',804,380,'2021-02-17 05:35:47','2021-02-17 05:35:48','39 123 4567'),(224,'AE','UNITED ARAB EMIRATES','United Arab Emirates','ARE',784,971,'2021-02-17 05:35:47','2021-02-17 05:35:48','50 123 4567'),(225,'GB','UNITED KINGDOM','United Kingdom','GBR',826,44,'2021-02-17 05:35:47','2021-02-17 05:35:48','7400 123456'),(226,'US','UNITED STATES','United States','USA',840,1,'2021-02-17 05:35:47','2021-02-17 05:35:48','201 555-0123'),(227,'UM','UNITED STATES MINOR OUTLYING ISLANDS','United States Minor Outlying Islands','NUL',0,1,'2021-02-17 05:35:47','2021-02-17 05:35:48','201 555-0123'),(228,'UY','URUGUAY','Uruguay','URY',858,598,'2021-02-17 05:35:47','2021-02-17 05:35:48','94 231 234'),(229,'UZ','UZBEKISTAN','Uzbekistan','UZB',860,998,'2021-02-17 05:35:47','2021-02-17 05:35:48','8 91 234 56 78'),(230,'VU','VANUATU','Vanuatu','VUT',548,678,'2021-02-17 05:35:47','2021-02-17 05:35:48','591 2345'),(231,'VE','VENEZUELA','Venezuela','VEN',862,58,'2021-02-17 05:35:47','2021-02-17 05:35:48','412-1234567'),(232,'VN','VIET NAM','Viet Nam','VNM',704,84,'2021-02-17 05:35:47','2021-02-17 05:35:48','91 234 56 78'),(233,'VG','VIRGIN ISLANDS, BRITISH','Virgin Islands, British','VGB',92,1284,'2021-02-17 05:35:47','2021-02-17 05:35:48','340 642-1234'),(234,'VI','VIRGIN ISLANDS, U.S.','Virgin Islands, U.s.','VIR',850,1340,'2021-02-17 05:35:47','2021-02-17 05:35:48','340 642-1234'),(235,'WF','WALLIS AND FUTUNA','Wallis and Futuna','WLF',876,681,'2021-02-17 05:35:47','2021-02-17 05:35:48','50 12 34'),(236,'EH','WESTERN SAHARA','Western Sahara','ESH',732,212,'2021-02-17 05:35:47','2021-02-17 05:35:48','650-123456'),(237,'YE','YEMEN','Yemen','YEM',887,967,'2021-02-17 05:35:47','2021-02-17 05:35:48','712 345 678'),(238,'ZM','ZAMBIA','Zambia','ZMB',894,260,'2021-02-17 05:35:47','2021-02-17 05:35:48','95 5123456'),(239,'ZW','ZIMBABWE','Zimbabwe','ZWE',716,263,'2021-02-17 05:35:47','2021-02-17 05:35:48','0');
/*!40000 ALTER TABLE `country_code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `custom_form_value`
--

DROP TABLE IF EXISTS `custom_form_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_form_value` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_field_id` int(10) unsigned NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `custom_id` int(11) NOT NULL,
  `custom_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(33) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_form_value_custom_id_index` (`custom_id`),
  KEY `custom_form_value_custom_type_index` (`custom_type`),
  KEY `custom_form_value_form_field_id_foreign` (`form_field_id`)
) ENGINE=MyISAM AUTO_INCREMENT=26454 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `custom_form_value`
--

LOCK TABLES `custom_form_value` WRITE;
/*!40000 ALTER TABLE `custom_form_value` DISABLE KEYS */;
INSERT INTO `custom_form_value` VALUES (25088,60,'\"RelatedParty\"','2021-06-22 13:16:23','2021-06-22 13:16:23',1009,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25144,47,'\"2\"','2021-06-22 13:17:21','2021-06-22 13:17:21',1010,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25145,48,'\"sdsd\"','2021-06-22 13:17:21','2021-06-22 13:17:21',1010,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25146,64,'\"adas\"','2021-06-22 13:17:21','2021-06-22 13:17:21',1010,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25141,54,'\"9176\"','2021-06-22 13:17:21','2021-06-22 13:17:21',1010,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25143,53,'\"https:\\/\\/host:port\\/partyManagement\\/v1\\/customer\\/9176\"','2021-06-22 13:17:21','2021-06-22 13:17:21',1010,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25142,55,'\"Jack Smith\"','2021-06-22 13:17:21','2021-06-22 13:17:21',1010,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25139,56,'\"customer\"','2021-06-22 13:17:21','2021-06-22 13:17:21',1010,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25140,57,'\"RelatedParty\"','2021-06-22 13:17:21','2021-06-22 13:17:21',1010,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25138,58,'\"Customer\"','2021-06-22 13:17:21','2021-06-22 13:17:21',1010,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25137,61,'\"https:\\/\\/dnext-dev.kubernetes.pia-team.com\\/ui\\/caseManagement\\/ticket\\/1010\"','2021-06-22 13:17:21','2021-06-22 13:17:21',1010,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25136,60,'\"RelatedParty\"','2021-06-22 13:17:21','2021-06-22 13:17:21',1010,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25135,62,'\"Others\"','2021-06-22 13:17:21','2021-06-22 13:17:21',1010,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25148,47,'\"2\"','2021-06-22 13:33:19','2021-06-22 13:33:19',1011,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25149,54,'\"f4bcbaca-d6b4-4fda-94e3-79f9b387ad81\"','2021-06-22 13:33:19','2021-06-22 13:33:19',1011,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25150,55,'\"IZMIR DENMARK UNIVERSITY\"','2021-06-22 13:33:19','2021-06-22 13:33:19',1011,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25151,56,'\"customer\"','2021-06-22 13:33:19','2021-06-22 13:33:19',1011,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25152,58,'\"Customer\"','2021-06-22 13:33:19','2021-06-22 13:33:19',1011,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25153,117,'\"CAS TV\"','2021-06-22 13:33:19','2021-06-22 13:33:19',1011,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25154,125,'\"Error Ticket\"','2021-06-22 13:33:19','2021-06-22 13:33:19',1011,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26202,54,'\"9176\"','2021-06-23 12:48:05','2021-06-23 12:48:05',1033,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26150,54,'\"9176\"','2021-06-23 12:36:57','2021-06-23 12:36:57',1032,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25286,48,'\"kjhkj\"','2021-06-22 22:04:49','2021-06-22 22:04:49',1012,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25287,48,'\"not come true 3\"','2021-06-23 06:32:49','2021-06-23 06:32:49',1013,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25285,47,'\"2\"','2021-06-22 22:04:49','2021-06-22 22:04:49',1012,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25283,55,'\"KENDAL UGURLU\"','2021-06-22 22:04:49','2021-06-22 22:04:49',1012,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25284,54,'\"f0565812-1857-4767-b9cd-33b02a9c4193\"','2021-06-22 22:04:49','2021-06-22 22:04:49',1012,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25282,58,'\"Customer\"','2021-06-22 22:04:49','2021-06-22 22:04:49',1012,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25281,56,'\"customer\"','2021-06-22 22:04:49','2021-06-22 22:04:49',1012,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25280,99,'\"New Installation\"','2021-06-22 22:04:49','2021-06-22 22:04:49',1012,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25278,104,'\"kjhjkhkjh\"','2021-06-22 22:04:49','2021-06-22 22:04:49',1012,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25279,100,'\"khjhjk\"','2021-06-22 22:04:49','2021-06-22 22:04:49',1012,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25276,106,'\"kjhkjhk\"','2021-06-22 22:04:49','2021-06-22 22:04:49',1012,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25277,101,'\"hkjhk\"','2021-06-22 22:04:49','2021-06-22 22:04:49',1012,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25274,134,'\"2021-06-02 07:40:44\"','2021-06-22 22:04:49','2021-06-22 22:04:49',1012,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25288,47,'\"2\"','2021-06-23 06:32:49','2021-06-23 06:32:49',1013,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25289,54,'\"aa0e46ad-3ae3-4c34-b2fd-05108ae8c390\"','2021-06-23 06:32:49','2021-06-23 06:32:49',1013,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25290,55,'\"IZMIR GERMAN UNIVERSITY\"','2021-06-23 06:32:49','2021-06-23 06:32:49',1013,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25291,56,'\"customer\"','2021-06-23 06:32:49','2021-06-23 06:32:49',1013,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25292,58,'\"Customer\"','2021-06-23 06:32:49','2021-06-23 06:32:49',1013,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25293,117,'\"CAS TV\"','2021-06-23 06:32:49','2021-06-23 06:32:49',1013,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25294,125,'\"Error Ticket\"','2021-06-23 06:32:49','2021-06-23 06:32:49',1013,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26148,60,'\"RelatedParty\"','2021-06-23 12:36:57','2021-06-23 12:36:57',1032,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25356,58,'\"Customer\"','2021-06-23 06:41:13','2021-06-23 06:41:13',1014,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25354,55,'\"IZMIR GERMAN UNIVERSITY\"','2021-06-23 06:41:13','2021-06-23 06:41:13',1014,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25355,56,'\"customer\"','2021-06-23 06:41:13','2021-06-23 06:41:13',1014,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25353,48,'\"not come true\"','2021-06-23 06:41:12','2021-06-23 06:41:12',1014,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26144,47,'\"2\"','2021-06-23 12:36:57','2021-06-23 12:36:57',1032,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25352,54,'\"aa0e46ad-3ae3-4c34-b2fd-05108ae8c390\"','2021-06-23 06:41:12','2021-06-23 06:41:12',1014,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25349,47,'\"2\"','2021-06-23 06:41:12','2021-06-23 06:41:12',1014,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25348,117,'\"Vodafone TV\"','2021-06-23 06:41:12','2021-06-23 06:41:12',1014,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25347,131,'\"Error Ticket\"','2021-06-23 06:41:12','2021-06-23 06:41:12',1014,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25389,68,'\"Hardware Issues\"','2021-06-23 06:43:08','2021-06-23 06:43:08',1015,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25386,55,'\"IZMIR GERMAN UNIVERSITY\"','2021-06-23 06:43:08','2021-06-23 06:43:08',1015,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25388,56,'\"customer\"','2021-06-23 06:43:08','2021-06-23 06:43:08',1015,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25387,58,'\"Customer\"','2021-06-23 06:43:08','2021-06-23 06:43:08',1015,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25385,47,'\"2\"','2021-06-23 06:43:08','2021-06-23 06:43:08',1015,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25382,71,'\"383838\"','2021-06-23 06:43:08','2021-06-23 06:43:08',1015,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25383,48,'\"come true5\"','2021-06-23 06:43:08','2021-06-23 06:43:08',1015,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25384,54,'\"aa0e46ad-3ae3-4c34-b2fd-05108ae8c390\"','2021-06-23 06:43:08','2021-06-23 06:43:08',1015,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25390,48,'\"test\"','2021-06-23 06:52:18','2021-06-23 06:52:18',1016,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25391,47,'\"2\"','2021-06-23 06:52:18','2021-06-23 06:52:18',1016,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25392,54,'\"78c6b2df-5c74-4dd6-8846-3ea6962c5a77\"','2021-06-23 06:52:18','2021-06-23 06:52:18',1016,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25393,55,'\"BORAN KUZUM\"','2021-06-23 06:52:18','2021-06-23 06:52:18',1016,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25394,56,'\"customer\"','2021-06-23 06:52:18','2021-06-23 06:52:18',1016,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25395,58,'\"Customer\"','2021-06-23 06:52:18','2021-06-23 06:52:18',1016,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25396,99,'\"New Installation\"','2021-06-23 06:52:18','2021-06-23 06:52:18',1016,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25397,100,'\"a\"','2021-06-23 06:52:18','2021-06-23 06:52:18',1016,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25398,101,'\"a\"','2021-06-23 06:52:18','2021-06-23 06:52:18',1016,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25399,104,'\"343\"','2021-06-23 06:52:18','2021-06-23 06:52:18',1016,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25400,106,'\"432423\"','2021-06-23 06:52:18','2021-06-23 06:52:18',1016,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25402,134,'\"2021-07-02 23:55:05\"','2021-06-23 06:52:18','2021-06-23 06:52:18',1016,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25629,54,'\"78c6b2df-5c74-4dd6-8846-3ea6962c5a77\"','2021-06-23 07:19:32','2021-06-23 07:19:32',1020,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25480,134,'\"1989-05-03 13:10:23\"','2021-06-23 07:07:18','2021-06-23 07:07:18',1017,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25478,47,'\"2\"','2021-06-23 07:07:18','2021-06-23 07:07:18',1017,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25479,48,'\"deniz\"','2021-06-23 07:07:18','2021-06-23 07:07:18',1017,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25476,58,'\"Customer\"','2021-06-23 07:07:18','2021-06-23 07:07:18',1017,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25477,54,'\"78c6b2df-5c74-4dd6-8846-3ea6962c5a77\"','2021-06-23 07:07:18','2021-06-23 07:07:18',1017,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25475,99,'\"New Installation\"','2021-06-23 07:07:18','2021-06-23 07:07:18',1017,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25474,100,'\"1\"','2021-06-23 07:07:18','2021-06-23 07:07:18',1017,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25473,55,'\"BORAN KUZUM\"','2021-06-23 07:07:18','2021-06-23 07:07:18',1017,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25471,104,'\"45435\"','2021-06-23 07:07:18','2021-06-23 07:07:18',1017,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25472,56,'\"customer\"','2021-06-23 07:07:18','2021-06-23 07:07:18',1017,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25470,106,'\"5453\"','2021-06-23 07:07:18','2021-06-23 07:07:18',1017,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25469,101,'\"d\"','2021-06-23 07:07:18','2021-06-23 07:07:18',1017,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26275,47,'\"2\"','2021-06-23 12:51:13','2021-06-23 12:51:13',1030,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25619,134,'\"1212-12-12 23:11:11\"','2021-06-23 07:19:32','2021-06-23 07:19:32',1020,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25545,117,'\"VOIP\"','2021-06-23 07:12:42','2021-06-23 07:12:42',1018,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25543,56,'\"customer\"','2021-06-23 07:12:42','2021-06-23 07:12:42',1018,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25544,58,'\"Customer\"','2021-06-23 07:12:42','2021-06-23 07:12:42',1018,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25541,54,'\"aa0e46ad-3ae3-4c34-b2fd-05108ae8c390\"','2021-06-23 07:12:42','2021-06-23 07:12:42',1018,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25542,55,'\"IZMIR GERMAN UNIVERSITY\"','2021-06-23 07:12:42','2021-06-23 07:12:42',1018,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25540,47,'\"2\"','2021-06-23 07:12:42','2021-06-23 07:12:42',1018,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25539,128,'\"Error Ticket\"','2021-06-23 07:12:42','2021-06-23 07:12:42',1018,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25491,48,'\"yalin test\"','2021-06-23 07:08:29','2021-06-23 07:08:29',1019,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25492,47,'\"2\"','2021-06-23 07:08:29','2021-06-23 07:08:29',1019,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25493,54,'\"78c6b2df-5c74-4dd6-8846-3ea6962c5a77\"','2021-06-23 07:08:29','2021-06-23 07:08:29',1019,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25494,55,'\"BORAN KUZUM\"','2021-06-23 07:08:29','2021-06-23 07:08:29',1019,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25495,56,'\"customer\"','2021-06-23 07:08:29','2021-06-23 07:08:29',1019,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25496,58,'\"Customer\"','2021-06-23 07:08:29','2021-06-23 07:08:29',1019,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25497,99,'\"New Installation\"','2021-06-23 07:08:29','2021-06-23 07:08:29',1019,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25498,100,'\"fdg\"','2021-06-23 07:08:29','2021-06-23 07:08:29',1019,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25499,101,'\"gdfgd\"','2021-06-23 07:08:29','2021-06-23 07:08:29',1019,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25500,104,'\"234\"','2021-06-23 07:08:29','2021-06-23 07:08:29',1019,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25501,106,'\"4543\"','2021-06-23 07:08:29','2021-06-23 07:08:29',1019,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26274,48,'\"gonna come\"','2021-06-23 12:51:13','2021-06-23 12:51:13',1030,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25503,134,'\"2020-09-09 11:21:07\"','2021-06-23 07:08:29','2021-06-23 07:08:29',1019,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25628,47,'\"2\"','2021-06-23 07:19:32','2021-06-23 07:19:32',1020,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25627,48,'\"deneme\"','2021-06-23 07:19:32','2021-06-23 07:19:32',1020,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25626,56,'\"customer\"','2021-06-23 07:19:32','2021-06-23 07:19:32',1020,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25625,55,'\"BORAN KUZUM\"','2021-06-23 07:19:32','2021-06-23 07:19:32',1020,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25624,58,'\"Customer\"','2021-06-23 07:19:32','2021-06-23 07:19:32',1020,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25621,106,'\"345\"','2021-06-23 07:19:32','2021-06-23 07:19:32',1020,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25622,101,'\"hamburg\"','2021-06-23 07:19:32','2021-06-23 07:19:32',1020,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25623,99,'\"New Installation\"','2021-06-23 07:19:32','2021-06-23 07:19:32',1020,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25618,100,'\"fdg\"','2021-06-23 07:19:32','2021-06-23 07:19:32',1020,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25538,48,'\"not found\"','2021-06-23 07:12:42','2021-06-23 07:12:42',1018,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26145,48,'\"sdf\"','2021-06-23 12:36:57','2021-06-23 12:36:57',1032,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26273,55,'\"PWC FINLANDDD\"','2021-06-23 12:51:13','2021-06-23 12:51:13',1030,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25664,62,'\"Others\"','2021-06-23 07:21:38','2021-06-23 07:21:38',1021,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25662,47,'\"4\"','2021-06-23 07:21:38','2021-06-23 07:21:38',1021,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25663,48,'\"asdsdfsdfsdfsdf\"','2021-06-23 07:21:38','2021-06-23 07:21:38',1021,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25661,53,'\"https:\\/\\/host:port\\/partyManagement\\/v1\\/customer\\/9176\"','2021-06-23 07:21:38','2021-06-23 07:21:38',1021,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25660,54,'\"9176\"','2021-06-23 07:21:38','2021-06-23 07:21:38',1021,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25659,55,'\"Jack Smith\"','2021-06-23 07:21:38','2021-06-23 07:21:38',1021,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25658,56,'\"customer\"','2021-06-23 07:21:38','2021-06-23 07:21:38',1021,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25656,58,'\"Customer\"','2021-06-23 07:21:38','2021-06-23 07:21:38',1021,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25657,57,'\"RelatedParty\"','2021-06-23 07:21:38','2021-06-23 07:21:38',1021,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25655,60,'\"RelatedParty\"','2021-06-23 07:21:38','2021-06-23 07:21:38',1021,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25654,61,'\"https:\\/\\/dnext-dev.kubernetes.pia-team.com\\/ui\\/caseManagement\\/ticket\\/1021\"','2021-06-23 07:21:38','2021-06-23 07:21:38',1021,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25665,64,'\"asd\"','2021-06-23 07:21:38','2021-06-23 07:21:38',1021,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25697,48,'\"sa\"','2021-06-23 07:23:17','2021-06-23 07:23:17',1022,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25695,47,'\"1\"','2021-06-23 07:23:17','2021-06-23 07:23:17',1022,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25696,53,'\"https:\\/\\/host:port\\/partyManagement\\/v1\\/customer\\/9176\"','2021-06-23 07:23:17','2021-06-23 07:23:17',1022,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25694,54,'\"9176\"','2021-06-23 07:23:17','2021-06-23 07:23:17',1022,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25693,55,'\"Jack Smith\"','2021-06-23 07:23:17','2021-06-23 07:23:17',1022,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25692,56,'\"customer\"','2021-06-23 07:23:17','2021-06-23 07:23:17',1022,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25689,60,'\"RelatedParty\"','2021-06-23 07:23:17','2021-06-23 07:23:17',1022,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25690,58,'\"Customer\"','2021-06-23 07:23:17','2021-06-23 07:23:17',1022,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25691,57,'\"RelatedParty\"','2021-06-23 07:23:17','2021-06-23 07:23:17',1022,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25688,61,'\"https:\\/\\/dnext-dev.kubernetes.pia-team.com\\/ui\\/caseManagement\\/ticket\\/1022\"','2021-06-23 07:23:17','2021-06-23 07:23:17',1022,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25698,66,'\"Converged Offers\"','2021-06-23 07:23:17','2021-06-23 07:23:17',1022,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25741,100,'\"fsdfsd\"','2021-06-23 07:41:44','2021-06-23 07:41:44',1023,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25742,101,'\"sdfs\"','2021-06-23 07:41:44','2021-06-23 07:41:44',1023,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25740,99,'\"New Installation\"','2021-06-23 07:41:44','2021-06-23 07:41:44',1023,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25738,55,'\"BORAN KUZUM\"','2021-06-23 07:41:44','2021-06-23 07:41:44',1023,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25739,58,'\"Customer\"','2021-06-23 07:41:44','2021-06-23 07:41:44',1023,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25736,54,'\"78c6b2df-5c74-4dd6-8846-3ea6962c5a77\"','2021-06-23 07:41:44','2021-06-23 07:41:44',1023,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25737,56,'\"customer\"','2021-06-23 07:41:44','2021-06-23 07:41:44',1023,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25735,48,'\"de\"','2021-06-23 07:41:44','2021-06-23 07:41:44',1023,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25734,47,'\"2\"','2021-06-23 07:41:44','2021-06-23 07:41:44',1023,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25732,134,'\"2078-08-12 13:16:17\"','2021-06-23 07:41:44','2021-06-23 07:41:44',1023,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25733,106,'\"dfsdfsfsd\"','2021-06-23 07:41:44','2021-06-23 07:41:44',1023,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25743,48,'\"1223\"','2021-06-23 08:04:58','2021-06-23 08:04:58',1024,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25744,47,'\"2\"','2021-06-23 08:04:58','2021-06-23 08:04:58',1024,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25745,54,'\"78c6b2df-5c74-4dd6-8846-3ea6962c5a77\"','2021-06-23 08:04:58','2021-06-23 08:04:58',1024,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25746,55,'\"BORAN KUZUM\"','2021-06-23 08:04:58','2021-06-23 08:04:58',1024,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25747,56,'\"customer\"','2021-06-23 08:04:58','2021-06-23 08:04:58',1024,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25748,58,'\"Customer\"','2021-06-23 08:04:58','2021-06-23 08:04:58',1024,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25749,99,'\"New Installation\"','2021-06-23 08:04:58','2021-06-23 08:04:58',1024,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25750,100,'\"gfdg\"','2021-06-23 08:04:58','2021-06-23 08:04:58',1024,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25751,101,'\"dsfdsfds\"','2021-06-23 08:04:58','2021-06-23 08:04:58',1024,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25752,106,'\"32432432\"','2021-06-23 08:04:58','2021-06-23 08:04:58',1024,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25753,134,'\"2021-09-17 11:07:42\"','2021-06-23 08:04:58','2021-06-23 08:04:58',1024,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26271,58,'\"Customer\"','2021-06-23 12:51:13','2021-06-23 12:51:13',1030,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25794,72,'\"Network Issues\"','2021-06-23 08:16:40','2021-06-23 08:16:40',1025,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25795,73,'\"Service loss\"','2021-06-23 08:16:40','2021-06-23 08:16:40',1025,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25796,79,'\"sd\"','2021-06-23 08:16:40','2021-06-23 08:16:40',1025,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25791,55,'\"BORAN KUZUM\"','2021-06-23 08:16:40','2021-06-23 08:16:40',1025,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25793,56,'\"customer\"','2021-06-23 08:16:40','2021-06-23 08:16:40',1025,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25792,58,'\"Customer\"','2021-06-23 08:16:40','2021-06-23 08:16:40',1025,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25789,48,'\"date\"','2021-06-23 08:16:40','2021-06-23 08:16:40',1025,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25790,54,'\"78c6b2df-5c74-4dd6-8846-3ea6962c5a77\"','2021-06-23 08:16:40','2021-06-23 08:16:40',1025,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25788,47,'\"2\"','2021-06-23 08:16:40','2021-06-23 08:16:40',1025,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25787,80,'\"324\"','2021-06-23 08:16:40','2021-06-23 08:16:40',1025,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25829,72,'\"TV Related Issues\"','2021-06-23 08:21:46','2021-06-23 08:21:46',1026,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25828,56,'\"customer\"','2021-06-23 08:21:46','2021-06-23 08:21:46',1026,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25827,58,'\"Customer\"','2021-06-23 08:21:46','2021-06-23 08:21:46',1026,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25826,55,'\"WATER AS\"','2021-06-23 08:21:46','2021-06-23 08:21:46',1026,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25825,54,'\"5866529d-0163-4db7-8525-fcf620e0d643\"','2021-06-23 08:21:46','2021-06-23 08:21:46',1026,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25824,47,'\"2\"','2021-06-23 08:21:46','2021-06-23 08:21:46',1026,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25822,48,'\"a\"','2021-06-23 08:21:46','2021-06-23 08:21:46',1026,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26272,140,'\"a3030303033\"','2021-06-23 12:51:13','2021-06-23 12:51:13',1030,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25873,101,'\"sdfs\"','2021-06-23 08:24:38','2021-06-23 08:24:38',1027,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25872,100,'\"dasd\"','2021-06-23 08:24:38','2021-06-23 08:24:38',1027,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25871,99,'\"New Installation\"','2021-06-23 08:24:38','2021-06-23 08:24:38',1027,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25869,56,'\"customer\"','2021-06-23 08:24:38','2021-06-23 08:24:38',1027,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25870,58,'\"Customer\"','2021-06-23 08:24:38','2021-06-23 08:24:38',1027,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25868,55,'\"WATER AS\"','2021-06-23 08:24:38','2021-06-23 08:24:38',1027,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25866,48,'\"rfe\"','2021-06-23 08:24:38','2021-06-23 08:24:38',1027,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25867,54,'\"5866529d-0163-4db7-8525-fcf620e0d643\"','2021-06-23 08:24:38','2021-06-23 08:24:38',1027,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25865,47,'\"2\"','2021-06-23 08:24:38','2021-06-23 08:24:38',1027,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25864,106,'\"2312\"','2021-06-23 08:24:38','2021-06-23 08:24:38',1027,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25863,134,'\"1291-03-24 17:39:56\"','2021-06-23 08:24:38','2021-06-23 08:24:38',1027,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25909,72,'\"Lack of Access\"','2021-06-23 08:27:56','2021-06-23 08:27:56',1028,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25908,58,'\"Customer\"','2021-06-23 08:27:56','2021-06-23 08:27:56',1028,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25905,47,'\"2\"','2021-06-23 08:27:56','2021-06-23 08:27:56',1028,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25906,55,'\"HACETTEPE\"','2021-06-23 08:27:56','2021-06-23 08:27:56',1028,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25907,56,'\"customer\"','2021-06-23 08:27:56','2021-06-23 08:27:56',1028,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25904,54,'\"f24d1cf9-ef09-4bc3-a640-5770f56a6798\"','2021-06-23 08:27:56','2021-06-23 08:27:56',1028,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25903,48,'\"a\"','2021-06-23 08:27:56','2021-06-23 08:27:56',1028,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25902,76,'\"No Access\"','2021-06-23 08:27:56','2021-06-23 08:27:56',1028,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26277,117,'\"VOIP\"','2021-06-23 12:51:13','2021-06-23 12:51:13',1030,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26270,54,'\"36e2236f-2423-42d5-ac30-50388e025485\"','2021-06-23 12:51:13','2021-06-23 12:51:13',1030,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25953,134,'\"2312-12-21 02:33:33\"','2021-06-23 08:31:38','2021-06-23 08:31:38',1029,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25950,101,'\"3\"','2021-06-23 08:31:38','2021-06-23 08:31:38',1029,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25951,100,'\"23424\"','2021-06-23 08:31:38','2021-06-23 08:31:38',1029,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25952,106,'\"weqe\"','2021-06-23 08:31:38','2021-06-23 08:31:38',1029,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25949,58,'\"Customer\"','2021-06-23 08:31:38','2021-06-23 08:31:38',1029,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25947,56,'\"customer\"','2021-06-23 08:31:38','2021-06-23 08:31:38',1029,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25948,99,'\"New Installation\"','2021-06-23 08:31:38','2021-06-23 08:31:38',1029,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25945,47,'\"2\"','2021-06-23 08:31:38','2021-06-23 08:31:38',1029,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25946,55,'\"HACETTEPE\"','2021-06-23 08:31:38','2021-06-23 08:31:38',1029,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25944,54,'\"f24d1cf9-ef09-4bc3-a640-5770f56a6798\"','2021-06-23 08:31:38','2021-06-23 08:31:38',1029,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(25943,48,'\"dfdsf\"','2021-06-23 08:31:38','2021-06-23 08:31:38',1029,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26200,56,'\"customer\"','2021-06-23 12:48:05','2021-06-23 12:48:05',1033,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26201,55,'\"Jack Smith\"','2021-06-23 12:48:05','2021-06-23 12:48:05',1033,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26198,58,'\"Customer\"','2021-06-23 12:48:05','2021-06-23 12:48:05',1033,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26199,57,'\"RelatedParty\"','2021-06-23 12:48:05','2021-06-23 12:48:05',1033,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26197,60,'\"RelatedParty\"','2021-06-23 12:48:05','2021-06-23 12:48:05',1033,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26237,91,'\"2\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26235,48,'\"aeness1\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26236,90,'\"1\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26234,47,'\"1\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26232,54,'\"9176\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26233,53,'\"https:\\/\\/host:port\\/partyManagement\\/v1\\/customer\\/9176\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26231,55,'\"Jack Smith\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26230,56,'\"customer\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26228,58,'\"Customer\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26229,57,'\"RelatedParty\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26227,60,'\"RelatedParty\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26226,61,'\"http:\\/\\/localhost:4300\\/ticket\\/1034\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26196,61,'\"https:\\/\\/dnext-dev.kubernetes.pia-team.com\\/ui\\/caseManagement\\/ticket\\/1033\"','2021-06-23 12:48:05','2021-06-23 12:48:05',1033,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26207,76,'\"OSS Code\"','2021-06-23 12:48:05','2021-06-23 12:48:05',1033,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26238,92,'\"3\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26225,86,'\"Refunds & Reimbursement\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26224,88,'\"I Would Like to Return My Order\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26239,93,'\"444\"','2021-06-23 12:48:06','2021-06-23 12:48:06',1034,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26344,60,'\"RelatedParty\"','2021-06-23 13:04:28','2021-06-23 13:04:28',1035,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26345,58,'\"Customer\"','2021-06-23 13:04:28','2021-06-23 13:04:28',1035,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26346,64,'\"1\"','2021-06-23 13:04:28','2021-06-23 13:04:28',1035,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26347,61,'\"https:\\/\\/dnext-dev.kubernetes.pia-team.com\\/ui\\/caseManagement\\/ticket\\/1035\"','2021-06-23 13:04:28','2021-06-23 13:04:28',1035,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26348,62,'\"Others\"','2021-06-23 13:04:28','2021-06-23 13:04:28',1035,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26343,57,'\"RelatedParty\"','2021-06-23 13:04:28','2021-06-23 13:04:28',1035,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26342,54,'\"9176\"','2021-06-23 13:04:28','2021-06-23 13:04:28',1035,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26339,56,'\"customer\"','2021-06-23 13:04:28','2021-06-23 13:04:28',1035,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26340,47,'\"4\"','2021-06-23 13:04:28','2021-06-23 13:04:28',1035,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26341,53,'\"https:\\/\\/host:port\\/partyManagement\\/v1\\/customer\\/9176\"','2021-06-23 13:04:28','2021-06-23 13:04:28',1035,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26337,48,'\"sdfs\"','2021-06-23 13:04:28','2021-06-23 13:04:28',1035,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26338,55,'\"Jack Smith\"','2021-06-23 13:04:28','2021-06-23 13:04:28',1035,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26389,79,'\"2\"','2021-06-23 13:30:30','2021-06-23 13:30:30',1036,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26387,47,'\"2\"','2021-06-23 13:30:30','2021-06-23 13:30:30',1036,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26388,48,'\"eee\"','2021-06-23 13:30:30','2021-06-23 13:30:30',1036,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26386,53,'\"https:\\/\\/host:port\\/partyManagement\\/v1\\/customer\\/9176\"','2021-06-23 13:30:30','2021-06-23 13:30:30',1036,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26385,54,'\"9176\"','2021-06-23 13:30:30','2021-06-23 13:30:30',1036,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26384,55,'\"Jack Smith\"','2021-06-23 13:30:30','2021-06-23 13:30:30',1036,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26383,56,'\"customer\"','2021-06-23 13:30:30','2021-06-23 13:30:30',1036,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26382,57,'\"RelatedParty\"','2021-06-23 13:30:30','2021-06-23 13:30:30',1036,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26381,58,'\"Customer\"','2021-06-23 13:30:30','2021-06-23 13:30:30',1036,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26380,60,'\"RelatedParty\"','2021-06-23 13:30:30','2021-06-23 13:30:30',1036,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26379,61,'\"http:\\/\\/localhost:4300\\/ticket\\/1036\"','2021-06-23 13:30:30','2021-06-23 13:30:30',1036,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26378,72,'\"Connectivity\"','2021-06-23 13:30:30','2021-06-23 13:30:30',1036,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26377,74,'\"Fiber Cut\"','2021-06-23 13:30:30','2021-06-23 13:30:30',1036,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26390,80,'\"1\"','2021-06-23 13:30:30','2021-06-23 13:30:30',1036,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26391,48,'\"zsaddas\"','2021-06-23 16:57:33','2021-06-23 16:57:33',1037,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26392,47,'\"2\"','2021-06-23 16:57:33','2021-06-23 16:57:33',1037,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26393,54,'\"153fd413-b48b-43a3-a610-96b1924c1415\"','2021-06-23 16:57:33','2021-06-23 16:57:33',1037,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26394,55,'\"TEST\"','2021-06-23 16:57:33','2021-06-23 16:57:33',1037,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26395,56,'\"customer\"','2021-06-23 16:57:33','2021-06-23 16:57:33',1037,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26396,58,'\"Customer\"','2021-06-23 16:57:33','2021-06-23 16:57:33',1037,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26397,99,'\"New Installation\"','2021-06-23 16:57:33','2021-06-23 16:57:33',1037,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26398,100,'\"testshop\"','2021-06-23 16:57:33','2021-06-23 16:57:33',1037,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26399,101,'\"testcity\"','2021-06-23 16:57:33','2021-06-23 16:57:33',1037,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26400,104,'\"dsadasdas\"','2021-06-23 16:57:33','2021-06-23 16:57:33',1037,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26401,106,'\"12\"','2021-06-23 16:57:33','2021-06-23 16:57:33',1037,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26402,134,'\"2021-06-18 19:45:52\"','2021-06-23 16:57:33','2021-06-23 16:57:33',1037,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26452,92,'\"4\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26451,91,'\"3\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26449,48,'\"test daily desc\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26450,90,'\"2\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26448,47,'\"1\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26447,53,'\"https:\\/\\/host:port\\/partyManagement\\/v1\\/customer\\/9176\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26446,54,'\"9176\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26445,55,'\"Jack Smith\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26444,56,'\"customer\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26443,57,'\"RelatedParty\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26442,58,'\"Customer\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26441,60,'\"RelatedParty\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26440,61,'\"http:\\/\\/localhost:4300\\/ticket\\/1038\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26439,86,'\"Invoice Issues\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26438,87,'\"How Do I View My Bill Online?\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26437,89,'\"1\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets',''),(26453,93,'\"55\"','2021-06-24 06:49:14','2021-06-24 06:49:14',1038,'App\\Model\\helpdesk\\Ticket\\Tickets','');
/*!40000 ALTER TABLE `custom_form_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `date_format`
--

DROP TABLE IF EXISTS `date_format`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `date_format` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `format` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `date_format`
--

LOCK TABLES `date_format` WRITE;
/*!40000 ALTER TABLE `date_format` DISABLE KEYS */;
INSERT INTO `date_format` VALUES (1,'dd/mm/yyyy'),(2,'dd-mm-yyyy'),(3,'dd.mm.yyyy'),(4,'mm/dd/yyyy'),(5,'mm:dd:yyyy'),(6,'mm-dd-yyyy'),(7,'yyyy/mm/dd'),(8,'yyyy.mm.dd'),(9,'yyyy-mm-dd');
/*!40000 ALTER TABLE `date_format` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `date_time_format`
--

DROP TABLE IF EXISTS `date_time_format`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `date_time_format` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `format` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `date_time_format`
--

LOCK TABLES `date_time_format` WRITE;
/*!40000 ALTER TABLE `date_time_format` DISABLE KEYS */;
INSERT INTO `date_time_format` VALUES (1,'F j, Y, g:i a'),(2,'jS F Y'),(3,'H:i'),(4,'d/m/Y H:i'),(5,'d.m.Y H:i'),(6,'d-m-Y H:i'),(7,'m/d/Y H:i'),(8,'m.d.Y H:i'),(9,'m-d-Y H:i'),(10,'Y/m/d H:i'),(11,'Y.m.d H:i'),(12,'Y-m-d H:i');
/*!40000 ALTER TABLE `date_time_format` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `manager` int(10) unsigned DEFAULT NULL,
  `ticket_assignment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `outgoing_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `template_set` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_ticket_response` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_message_response` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_response_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `recipient` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `group_access` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `department_sign` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `business_hour` int(11) NOT NULL,
  `en_auto_assign` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `manager_2` (`manager`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department`
--

LOCK TABLES `department` WRITE;
/*!40000 ALTER TABLE `department` DISABLE KEYS */;
INSERT INTO `department` VALUES (1,'Support','1',NULL,'','','','','','','','','','2021-02-17 05:35:47','2021-02-17 05:35:50',0,0),(2,'Pre-Sales Department','1',NULL,'','','','','','','','','','2021-02-17 05:35:47','2021-02-17 14:39:20',0,0),(3,'Field Operations','1',NULL,'','','','','','','','','','2021-02-17 05:35:47','2021-02-24 13:21:45',0,0),(4,'First Line Support','1',NULL,'','','','','','','','','','2021-02-17 14:39:48','2021-02-17 14:39:48',0,0),(5,'Back Office Support','1',NULL,'','','','','','','','','','2021-02-17 14:40:21','2021-02-17 14:40:21',0,0),(6,'Second Line Billing','1',NULL,'','','','','','','','','','2021-02-17 14:40:42','2021-02-23 18:02:12',0,0);
/*!40000 ALTER TABLE `department` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department_assign_agents`
--

DROP TABLE IF EXISTS `department_assign_agents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department_assign_agents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_assign_agents`
--

LOCK TABLES `department_assign_agents` WRITE;
/*!40000 ALTER TABLE `department_assign_agents` DISABLE KEYS */;
INSERT INTO `department_assign_agents` VALUES (1,1,1,'2021-02-17 05:35:47','2021-02-17 05:35:47'),(2,4,2,NULL,NULL),(3,4,3,NULL,NULL),(4,2,5,NULL,NULL),(5,1,7,NULL,NULL),(8,3,8,NULL,NULL),(7,4,10,NULL,NULL),(9,6,1,'2021-02-23 18:02:12','2021-02-23 18:02:12'),(10,5,6,NULL,NULL),(11,3,11,NULL,NULL),(12,5,13,NULL,NULL),(13,1,14,NULL,NULL),(16,3,12,NULL,NULL),(17,6,16,NULL,NULL),(18,1,16,NULL,NULL),(19,1,17,NULL,NULL),(20,5,17,NULL,NULL),(21,6,17,NULL,NULL),(24,3,19,NULL,NULL),(23,3,18,NULL,NULL),(25,6,22,NULL,NULL);
/*!40000 ALTER TABLE `department_assign_agents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department_assign_manager`
--

DROP TABLE IF EXISTS `department_assign_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department_assign_manager` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_assign_manager`
--

LOCK TABLES `department_assign_manager` WRITE;
/*!40000 ALTER TABLE `department_assign_manager` DISABLE KEYS */;
INSERT INTO `department_assign_manager` VALUES (1,6,1,'2021-02-23 18:02:12','2021-02-23 18:02:12');
/*!40000 ALTER TABLE `department_assign_manager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department_form_group`
--

DROP TABLE IF EXISTS `department_form_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department_form_group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_group_id` int(10) unsigned NOT NULL,
  `department_id` int(10) unsigned NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `department_form_group_form_group_id_foreign` (`form_group_id`),
  KEY `department_form_group_department_id_foreign` (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_form_group`
--

LOCK TABLES `department_form_group` WRITE;
/*!40000 ALTER TABLE `department_form_group` DISABLE KEYS */;
INSERT INTO `department_form_group` VALUES (1,1,2,0),(2,2,4,0),(3,3,3,0),(4,4,3,2),(5,5,4,1),(6,6,6,0),(7,7,3,3),(8,8,3,1),(9,9,1,0),(10,10,1,0);
/*!40000 ALTER TABLE `department_form_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emails`
--

DROP TABLE IF EXISTS `emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `department` int(10) unsigned DEFAULT NULL,
  `priority` int(10) unsigned DEFAULT NULL,
  `help_topic` int(10) unsigned DEFAULT NULL,
  `user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` text COLLATE utf8_unicode_ci NOT NULL,
  `fetching_host` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fetching_port` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fetching_protocol` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fetching_encryption` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mailbox_protocol` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imap_config` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `folder` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sending_host` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sending_port` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sending_protocol` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sending_encryption` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `smtp_validate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `smtp_authentication` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `internal_notes` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_response` tinyint(1) NOT NULL,
  `fetching_status` tinyint(1) NOT NULL,
  `move_to_folder` tinyint(1) NOT NULL,
  `delete_email` tinyint(1) NOT NULL,
  `do_nothing` tinyint(1) NOT NULL,
  `sending_status` tinyint(1) NOT NULL,
  `authentication` tinyint(1) NOT NULL,
  `header_spoofing` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `block_auto_generated` tinyint(1) NOT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `region` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `version` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `available_for_fetch` tinyint(1) DEFAULT 1,
  `last_fetched_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `department` (`department`,`priority`,`help_topic`),
  KEY `department_2` (`department`,`priority`,`help_topic`),
  KEY `priority` (`priority`),
  KEY `help_topic` (`help_topic`),
  KEY `emails_last_fetched_at_index` (`last_fetched_at`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emails`
--

LOCK TABLES `emails` WRITE;
/*!40000 ALTER TABLE `emails` DISABLE KEYS */;
INSERT INTO `emails` VALUES (1,'service@pia-team.com','Service',NULL,NULL,NULL,'service@pia-team.com','eyJpdiI6IlpmZVB2aTZWbUdiTVpieHB6YmFzOGc9PSIsInZhbHVlIjoiaEZ0bk9JSEtzY3lUV0NRV1lFTW8xcnlKYlV1d2dtWk5tVmJVTVI4M0xJcz0iLCJtYWMiOiI1NzBkZTk3Y2RlYjFhMTA0ODJjZTdmMDZjNGZiYTdjZGI2MTlhMTVmNmM5MGQyN2Q4NzEwMjRhOTJhNTIxZWFiIn0=','pop.gmail.com','995','pop3','ssl','','','','smtp.gmail.com','587','smtp','tls','','','',0,1,0,0,0,1,0,0,'2021-02-17 05:52:43','2021-06-24 11:25:01',0,'','','','','',1,'2021-06-24 11:25:01');
/*!40000 ALTER TABLE `emails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faveo_license`
--

DROP TABLE IF EXISTS `faveo_license`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faveo_license` (
  `SETTING_ID` tinyint(1) NOT NULL AUTO_INCREMENT,
  `ROOT_URL` varchar(250) NOT NULL,
  `CLIENT_EMAIL` varchar(250) NOT NULL,
  `LICENSE_CODE` varchar(250) NOT NULL,
  `LCD` varchar(250) NOT NULL,
  `LRD` varchar(250) NOT NULL,
  `INSTALLATION_KEY` varchar(250) NOT NULL,
  `INSTALLATION_HASH` varchar(250) NOT NULL,
  PRIMARY KEY (`SETTING_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faveo_license`
--

LOCK TABLES `faveo_license` WRITE;
/*!40000 ALTER TABLE `faveo_license` DISABLE KEYS */;
INSERT INTO `faveo_license` VALUES (1,'http://dcase-fav-dev.dnext-vfal.com','','ZNY4HVXDT1WT0000','aCtiamh6UmwvQlVRbGttUDNGUGdoZz09Ojp00wzu0fEMtdmUkJUJbr+p','VzVRS1hrbWxyWTFEQXBaaXljVGEvdz09OjpKBj7eXplCMPqDWVkL5b50','MllueFFiUXhKTlFWUmI4ZEhyNEE2N1lha1lrUVUwaVBVTTlLckVpOGF0Q0VEZG5sZnZuWTBUWkJhTnRFTG5pMXAxREo4NUFOQzh5WE1FVjUyTHZHL0E9PTo6xIw+rOTrkIGid+TMn5PdMQ==','e84c9cacbea639e8265068fe808124a67799696b844c8b461ca1083729d3440e');
/*!40000 ALTER TABLE `faveo_license` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forms`
--

DROP TABLE IF EXISTS `forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `json` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forms`
--

LOCK TABLES `forms` WRITE;
/*!40000 ALTER TABLE `forms` DISABLE KEYS */;
INSERT INTO `forms` VALUES (1,'ticket','[{ \'title\': \'Requester\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Requester\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Requester\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'email\', \'agentCCfield\':true, \'customerCCfield\':false, \'customerDisplay\':true, \'agentDisplay\':true, \'agentRequiredFormSubmit\':true, \'customerRequiredFormSubmit\':true, \'default\':\'yes\', \'value\':\'\', \'unique\':\'requester\' },{ \'title\': \'Subject\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Subject\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Subject\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'text\', \'agentRequiredFormSubmit\':true, \'customerDisplay\':true, \'agentDisplay\':true, \'customerRequiredFormSubmit\':true, \'default\':\'yes\', \'value\':\'\', \'unique\':\'subject\' },{ \'title\': \'Status\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Status\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Status\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'select\', \'agentRequiredFormSubmit\':true, \'customerDisplay\':false, \'agentDisplay\':true, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'api\':\'status\', \'options\':[ ], \'default\':\'yes\', \'unique\':\'status\' },{ \'title\': \'Priority\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Priority\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Priority\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'select\', \'agentRequiredFormSubmit\':true, \'customerDisplay\':true, \'agentDisplay\':true, \'customerRequiredFormSubmit\':true, \'value\':\'\', \'api\':\'priority\', \'options\':[ ], \'default\':\'yes\', \'unique\':\'priority\' }, { \'title\': \'Location\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Location\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Location\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'select\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'agentDisplay\':true, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'api\':\'location\', \'options\':[ ], \'default\':\'yes\', \'unique\':\'location\' }, { \'title\': \'Help Topic\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Help Topic\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Help Topic\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'multiselect\', \'agentRequiredFormSubmit\':true, \'customerDisplay\':true, \'agentDisplay\':true, \'customerRequiredFormSubmit\':true, \'value\':\'\', \'api\':\'helptopic\', \'options\':[ ], \'default\':\'yes\', \'unique\':\'help_topic\', \'link\':false },{ \'title\': \'Department\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Department\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Department\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'multiselect\', \'agentRequiredFormSubmit\':true, \'agentDisplay\':true, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'api\':\'department\', \'options\':[ ], \'default\':\'yes\', \'unique\':\'department\', \'link\':false },{ \'title\': \'Type\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Type\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Type\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'select\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':true, \'agentDisplay\':true, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'api\':\'type\', \'options\':[ ], \'default\':\'yes\', \'unique\':\'type\' },{ \'title\': \'Assigned\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Assigned\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Assigned\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'select\', \'agentRequiredFormSubmit\':false, \'agentDisplay\':false, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'api\':\'assigned_to\', \'options\':[ ], \'default\':\'yes\', \'unique\':\'assigned\' },{ \'title\': \'Description\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Description\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Description\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'textarea\', \'agentRequiredFormSubmit\':true, \'agentDisplay\':true, \'customerDisplay\':true, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'description\', \'media_option\':true },{ \'title\': \'Captcha\', \'agentDisplay\':true, \'customerDisplay\':true, \'default\':\'yes\', \'value\':\'\' },{ \'title\': \'Organization\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Organization\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Organization\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'select\', \'agentRequiredFormSubmit\':false, \'agentDisplay\':true, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'api\':\'company\', \'options\':[ ], \'default\':\'yes\', \'unique\':\'company\' },{ \'title\': \'Organisation Department\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Organisation Department\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Organisation Department\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'select\', \'agentRequiredFormSubmit\':false, \'agentDisplay\':true, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'api\':\'org_dept\', \'options\':[ ], \'default\':\'yes\', \'unique\':\'org_dept\' } ]',NULL,NULL),(2,'user','[{ \'title\': \'First Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'First Name\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'First Name\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'text\', \'customerDisplay\':true, \'agentDisplay\':true, \'agentRequiredFormSubmit\':true, \'customerRequiredFormSubmit\':true, \'default\':\'yes\', \'value\':\'\', \'unique\':\'first_name\' },{ \'title\': \'Last Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Last Name\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Last Name\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'text\', \'customerDisplay\':true, \'agentDisplay\':true, \'agentRequiredFormSubmit\':true, \'customerRequiredFormSubmit\':true, \'default\':\'yes\', \'value\':\'\', \'unique\':\'last_name\' }, { \'title\': \'User Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'User Name\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'User Name\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'text\', \'customerDisplay\':false, \'agentDisplay\':true, \'agentRequiredFormSubmit\':false, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'user_name\' },{ \'title\': \'Work Phone\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Work Phone\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Work Phone\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'number\', \'agentRequiredFormSubmit\':false, \'agentDisplay\':true, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'phone_number\' },{ \'title\': \'Email\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Email\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Email\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'email\', \'agentRequiredFormSubmit\':true, \'agentDisplay\':true, \'customerDisplay\':true, \'customerRequiredFormSubmit\':true, \'value\':\'\', \'default\':\'yes\', \'unique\':\'email\' },{ \'title\': \'Mobile Phone\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Mobile Phone\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Mobile Phone\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'number\', \'agentRequiredFormSubmit\':false, \'agentDisplay\':true, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'default\':\'yes\', \'unique\':\'mobile\' },{ \'title\': \'Address\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Address\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Address\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'textarea\', \'agentRequiredFormSubmit\':false, \'agentDisplay\':true, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'default\':\'no\', \'unique\':\'address\' },{ \'title\': \'Organisation\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Organisation\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Organisation\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'select2\', \'agentRequiredFormSubmit\':false, \'agentDisplay\':true, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'organisation\' },{ \'title\': \'Department Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Department Name\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Department Name\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'select\', \'agentRequiredFormSubmit\':false, \'agentDisplay\':true, \'customerDisplay\':false, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'department\', \'options\':[ ], \'api\':\'organisationdept\' }, { \'title\': \'Captcha\', \'agentDisplay\':true, \'customerDisplay\':true, \'default\':\'yes\', \'value\':\'\' }]',NULL,NULL),(3,'organisation','[{ \'title\': \'Organization Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Organization Name\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Organization Name\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'text\', \'customerDisplay\':true, \'agentDisplay\':true, \'agentRequiredFormSubmit\':true, \'customerRequiredFormSubmit\':true, \'default\':\'yes\', \'value\':\'\', \'unique\':\'name\' },{ \'title\': \'Phone\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Phone\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Phone\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'number\', \'customerDisplay\':true, \'agentDisplay\':true, \'agentRequiredFormSubmit\':false, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'phone\' },{ \'title\': \'Organization Domain Name\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Organization Domain Name\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Organization Domain Name\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'select2\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':true, \'agentDisplay\':true, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'domain\' },{ \'title\': \'Description\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Description\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Description\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'textarea\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':true, \'agentDisplay\':true, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'default\':\'yes\', \'unique\':\'internal_notes\' },{ \'title\': \'Address\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Address\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Address\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'textarea\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':false, \'agentDisplay\':true, \'customerRequiredFormSubmit\':false, \'value\':\'\', \'default\':\'yes\', \'unique\':\'address\' }, { \'title\': \'Department\', \'agentlabel\':[ {\'language\':\'en\',\'label\':\'Department\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'clientlabel\':[ {\'language\':\'en\',\'label\':\'Department\',\'flag\':\'http://34.254.189.147/lb-faveo/flags/en.png\'} ], \'type\':\'select2\', \'agentRequiredFormSubmit\':false, \'customerDisplay\':true, \'agentDisplay\':true, \'customerRequiredFormSubmit\':false, \'default\':\'yes\', \'value\':\'\', \'unique\':\'department\' }]',NULL,NULL);
/*!40000 ALTER TABLE `forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_categories`
--

DROP TABLE IF EXISTS `form_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'helpdesk',
  `name` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_categories`
--

LOCK TABLES `form_categories` WRITE;
/*!40000 ALTER TABLE `form_categories` DISABLE KEYS */;
INSERT INTO `form_categories` VALUES (1,'ticket','2021-02-17 05:35:48','2021-02-17 05:35:50','helpdesk','Ticket'),(2,'organisation','2021-02-17 05:35:48','2021-02-17 05:35:50','helpdesk','Organization'),(3,'user','2021-02-17 05:35:48','2021-02-17 05:35:50','helpdesk','Requester'),(4,'asset','2021-02-17 05:39:08','2021-02-17 05:39:08','servicedesk','Asset');
/*!40000 ALTER TABLE `form_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_fields`
--

DROP TABLE IF EXISTS `form_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned DEFAULT NULL,
  `category_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `required_for_agent` tinyint(1) NOT NULL,
  `required_for_user` tinyint(1) NOT NULL,
  `display_for_agent` tinyint(1) NOT NULL,
  `display_for_user` tinyint(1) NOT NULL,
  `default` tinyint(1) NOT NULL,
  `is_linked` tinyint(1) NOT NULL,
  `media_option` tinyint(1) NOT NULL,
  `api_info` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pattern` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `option_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_edit_visible` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `unique` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `form_group_id` int(10) unsigned DEFAULT NULL,
  `is_deletable` tinyint(1) NOT NULL DEFAULT 1,
  `is_customizable` tinyint(1) NOT NULL DEFAULT 1,
  `is_observable` tinyint(1) NOT NULL DEFAULT 1,
  `is_filterable` tinyint(1) NOT NULL DEFAULT 1,
  `is_agent_config` tinyint(1) NOT NULL DEFAULT 1,
  `is_user_config` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `form_fields_form_group_id_foreign` (`form_group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=142 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_fields`
--

LOCK TABLES `form_fields` WRITE;
/*!40000 ALTER TABLE `form_fields` DISABLE KEYS */;
INSERT INTO `form_fields` VALUES (1,1,'App\\Model\\helpdesk\\Form\\FormCategory',0,'Requester','text',1,1,1,1,1,0,0,'url:=/api/dependency/users?meta=true;;',NULL,NULL,'2021-02-17 05:35:48','2021-06-02 12:44:26',1,1,0,'requester',NULL,0,1,1,1,1,1),(2,1,'App\\Model\\helpdesk\\Form\\FormCategory',1,'CC','api',0,0,0,0,1,0,0,'url:=/api/dependency/users?meta=true;;',NULL,NULL,'2021-02-17 05:35:48','2021-06-02 12:44:26',0,1,0,'cc',NULL,0,1,1,1,1,1),(3,1,'App\\Model\\helpdesk\\Form\\FormCategory',2,'Subject','text',1,1,1,1,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-06-02 12:44:26',1,1,0,'subject',NULL,0,1,1,1,1,1),(4,1,'App\\Model\\helpdesk\\Form\\FormCategory',6,'Status','api',0,0,0,0,1,0,0,'url:=/api/dependency/statuses;;',NULL,NULL,'2021-02-17 05:35:48','2021-06-02 12:44:26',1,1,0,'status_id',NULL,0,1,1,1,1,1),(5,1,'App\\Model\\helpdesk\\Form\\FormCategory',4,'Priority','api',1,1,1,1,1,0,0,'url:=/api/dependency/priorities;;',NULL,NULL,'2021-02-17 05:35:48','2021-06-02 12:44:26',1,1,0,'priority_id',NULL,0,1,1,1,1,1),(6,1,'App\\Model\\helpdesk\\Form\\FormCategory',7,'Location','api',0,0,0,0,1,0,0,'url:=/api/dependency/locations;;',NULL,NULL,'2021-02-17 05:35:48','2021-06-02 12:44:26',1,1,0,'location_id',NULL,0,1,1,1,1,1),(7,1,'App\\Model\\helpdesk\\Form\\FormCategory',8,'Source','api',0,0,0,0,1,0,0,'url:=/api/dependency/sources;;',NULL,NULL,'2021-02-17 05:35:48','2021-06-02 12:44:26',1,1,0,'source_id',NULL,0,1,1,1,1,1),(8,1,'App\\Model\\helpdesk\\Form\\FormCategory',5,'Help Topic','api',1,1,1,1,1,0,0,'url:=/api/dependency/help-topics?meta=true;;',NULL,NULL,'2021-02-17 05:35:48','2021-06-02 12:44:26',1,1,0,'help_topic_id',NULL,0,1,1,1,1,1),(9,1,'App\\Model\\helpdesk\\Form\\FormCategory',9,'Department','api',0,0,0,0,1,0,0,'url:=/api/dependency/departments?meta=true;;',NULL,NULL,'2021-02-17 05:35:48','2021-06-02 12:44:26',1,1,0,'department_id',NULL,0,1,1,1,1,1),(10,1,'App\\Model\\helpdesk\\Form\\FormCategory',10,'Type','api',0,0,0,0,1,0,0,'url:=/api/dependency/types;;',NULL,NULL,'2021-02-17 05:35:48','2021-06-02 12:44:26',1,1,0,'type_id',NULL,0,1,1,1,1,1),(11,1,'App\\Model\\helpdesk\\Form\\FormCategory',11,'Assigned','api',0,0,0,0,1,0,0,'url:=/api/dependency/agents?meta=true;;',NULL,NULL,'2021-02-17 05:35:48','2021-06-02 12:44:26',1,1,0,'assigned_id',NULL,0,1,1,1,1,1),(12,1,'App\\Model\\helpdesk\\Form\\FormCategory',3,'Description','textarea',1,1,1,1,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-06-02 12:44:26',0,1,0,'description',NULL,0,1,1,1,1,1),(13,1,'App\\Model\\helpdesk\\Form\\FormCategory',12,'Captcha','select',0,0,0,0,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-06-02 12:44:26',1,1,0,'captcha',NULL,0,1,1,1,1,1),(14,2,'App\\Model\\helpdesk\\Form\\FormCategory',1,'Organisation Name','text',1,1,1,1,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'organisation_name',NULL,0,1,1,1,1,1),(15,2,'App\\Model\\helpdesk\\Form\\FormCategory',2,'Phone','number',1,0,1,1,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'phone',NULL,0,1,1,1,1,1),(16,2,'App\\Model\\helpdesk\\Form\\FormCategory',3,'Organisation Domain Name','select2',0,0,1,1,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'organisation_domain_name',NULL,0,1,1,1,1,1),(17,2,'App\\Model\\helpdesk\\Form\\FormCategory',4,'Description','textarea',0,0,1,1,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',0,1,0,'description',NULL,0,1,1,1,1,1),(18,2,'App\\Model\\helpdesk\\Form\\FormCategory',5,'Address','textarea',0,0,1,0,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'address',NULL,0,1,1,1,1,1),(19,2,'App\\Model\\helpdesk\\Form\\FormCategory',6,'Organisation Logo','file',0,0,1,1,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'organisation_logo',NULL,0,1,1,1,1,1),(20,2,'App\\Model\\helpdesk\\Form\\FormCategory',7,'Organisation Department','select2',0,0,1,0,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,0,0,'organisation_department',NULL,0,1,1,1,1,1),(21,2,'App\\Model\\helpdesk\\Form\\FormCategory',8,'Captcha','select',0,0,0,0,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'captcha',NULL,0,1,1,1,1,1),(22,3,'App\\Model\\helpdesk\\Form\\FormCategory',1,'First Name','text',1,1,1,1,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'first_name',NULL,0,1,1,1,1,1),(23,3,'App\\Model\\helpdesk\\Form\\FormCategory',2,'Last Name','text',1,1,1,1,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'last_name',NULL,0,1,1,1,1,1),(24,3,'App\\Model\\helpdesk\\Form\\FormCategory',3,'User Name','text',0,0,1,0,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'user_name',NULL,0,1,1,1,1,1),(25,3,'App\\Model\\helpdesk\\Form\\FormCategory',4,'Work Phone','number',0,0,1,0,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'work_phone',NULL,0,1,1,1,1,1),(26,3,'App\\Model\\helpdesk\\Form\\FormCategory',5,'Email','email',1,1,1,1,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'email',NULL,0,1,1,1,1,1),(27,3,'App\\Model\\helpdesk\\Form\\FormCategory',6,'Mobile Phone','number',0,0,1,0,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'mobile_phone',NULL,0,1,1,1,1,1),(28,3,'App\\Model\\helpdesk\\Form\\FormCategory',7,'Address','textarea',0,0,1,0,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'address',NULL,0,1,1,1,1,1),(29,3,'App\\Model\\helpdesk\\Form\\FormCategory',8,'Organisation','select2',0,0,1,0,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'organisation',NULL,0,1,1,1,1,1),(30,3,'App\\Model\\helpdesk\\Form\\FormCategory',9,'Organisation Department','select2',0,0,1,0,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,0,0,'organisation_department',NULL,0,1,1,1,1,1),(31,3,'App\\Model\\helpdesk\\Form\\FormCategory',10,'Captcha','select',0,0,0,0,1,0,0,NULL,NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',1,1,0,'captcha',NULL,0,1,1,1,1,1),(32,1,'App\\Model\\helpdesk\\Form\\FormCategory',14,'Billing','select',0,1,1,1,0,0,0,'url:=/ticket/form/dependancy?dependency=user-packages;;',NULL,NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48',0,0,0,'package_order',NULL,0,0,0,0,1,1),(33,4,'App\\Model\\helpdesk\\Form\\FormCategory',1,'Name','text',1,0,1,0,1,0,0,'url:=;;','',NULL,'2021-02-17 05:39:08','2021-02-17 05:39:08',1,1,0,'name',NULL,0,1,1,1,0,0),(34,4,'App\\Model\\helpdesk\\Form\\FormCategory',2,'Identifier','text',0,0,1,0,1,0,0,'url:=;;','',NULL,'2021-02-17 05:39:08','2021-02-17 05:39:08',1,1,0,'identifier',NULL,0,1,1,1,1,0),(35,4,'App\\Model\\helpdesk\\Form\\FormCategory',3,'Department','api',1,0,1,0,1,0,0,'url:=/api/dependency/departments;;','',NULL,'2021-02-17 05:39:08','2021-02-17 05:39:08',1,1,0,'department_id',NULL,0,1,1,1,0,0),(36,4,'App\\Model\\helpdesk\\Form\\FormCategory',4,'Impact Type','api',0,0,1,0,1,0,0,'url:=/service-desk/api/dependency/impacts;;','',NULL,'2021-02-17 05:39:08','2021-02-17 05:39:08',1,1,0,'impact_type_id',NULL,0,1,1,1,1,0),(37,4,'App\\Model\\helpdesk\\Form\\FormCategory',5,'Organization','api',0,0,1,0,1,0,0,'url:=/api/dependency/organizations;;','',NULL,'2021-02-17 05:39:08','2021-02-17 05:39:08',1,1,0,'organization_id',NULL,0,1,1,1,1,0),(38,4,'App\\Model\\helpdesk\\Form\\FormCategory',6,'Location','api',0,0,1,0,1,0,0,'url:=/api/dependency/locations;;','',NULL,'2021-02-17 05:39:08','2021-02-17 05:39:08',1,1,0,'location_id',NULL,0,1,1,1,1,0),(39,4,'App\\Model\\helpdesk\\Form\\FormCategory',7,'Managed By','api',0,0,1,0,1,0,0,'url:=/api/dependency/agents?meta=true;;','',NULL,'2021-02-17 05:39:08','2021-02-17 05:39:08',1,1,0,'managed_by_id',NULL,0,1,1,1,1,0),(40,4,'App\\Model\\helpdesk\\Form\\FormCategory',8,'Used By','api',0,0,1,0,1,0,0,'url:=/api/dependency/users?meta=true;;','',NULL,'2021-02-17 05:39:08','2021-02-17 05:39:08',1,1,0,'used_by_id',NULL,0,1,1,1,1,0),(41,4,'App\\Model\\helpdesk\\Form\\FormCategory',9,'Product','api',0,0,1,0,1,0,0,'url:=/service-desk/api/dependency/products;;','',NULL,'2021-02-17 05:39:08','2021-02-17 05:39:08',1,1,0,'product_id',NULL,0,1,1,1,1,0),(42,4,'App\\Model\\helpdesk\\Form\\FormCategory',10,'Asset Type','api',1,0,1,0,1,0,0,'url:=/service-desk/api/dependency/asset_types?meta=true;;','',NULL,'2021-02-17 05:39:08','2021-02-17 05:39:08',1,1,0,'asset_type_id',NULL,0,1,1,1,0,0),(43,4,'App\\Model\\helpdesk\\Form\\FormCategory',11,'Assigned On','date',0,0,1,0,1,0,0,'url:=;;','',NULL,'2021-02-17 05:39:08','2021-02-17 05:39:08',1,1,0,'assigned_on',NULL,0,1,1,1,1,0),(44,4,'App\\Model\\helpdesk\\Form\\FormCategory',12,'Description','textarea',1,0,1,0,1,0,0,'url:=;;','',NULL,'2021-02-17 05:39:08','2021-02-17 05:39:08',1,1,0,'description',NULL,0,1,1,1,0,0),(45,4,'App\\Model\\helpdesk\\Form\\FormCategory',13,'Attachments','file',0,0,1,0,1,0,0,'url:=;;','',NULL,'2021-02-17 05:39:08','2021-02-17 05:39:08',1,1,0,'attachments',NULL,0,1,1,1,1,0),(46,4,'App\\Model\\helpdesk\\Form\\FormCategory',10,'Asset Status','api',0,0,1,0,1,0,0,'url:=/service-desk/api/dependency/asset_statuses;;','',NULL,'2021-02-17 05:39:08','2021-02-17 05:39:08',1,1,0,'status_id',NULL,0,1,1,1,1,0),(47,1,'App\\Model\\helpdesk\\Form\\FormCategory',13,'Select','select',1,1,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:41:21','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(48,1,'App\\Model\\helpdesk\\Form\\FormCategory',14,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:49:31','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(49,1,'App\\Model\\helpdesk\\Form\\FormCategory',15,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:49:31','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(50,1,'App\\Model\\helpdesk\\Form\\FormCategory',16,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:49:31','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(51,1,'App\\Model\\helpdesk\\Form\\FormCategory',17,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:49:31','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(52,1,'App\\Model\\helpdesk\\Form\\FormCategory',18,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:49:31','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(53,1,'App\\Model\\helpdesk\\Form\\FormCategory',19,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:49:31','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(54,1,'App\\Model\\helpdesk\\Form\\FormCategory',20,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:49:31','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(55,1,'App\\Model\\helpdesk\\Form\\FormCategory',21,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:49:31','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(56,1,'App\\Model\\helpdesk\\Form\\FormCategory',23,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:49:31','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(57,1,'App\\Model\\helpdesk\\Form\\FormCategory',24,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:49:31','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(58,1,'App\\Model\\helpdesk\\Form\\FormCategory',22,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:49:31','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(59,1,'App\\Model\\helpdesk\\Form\\FormCategory',25,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:49:31','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(60,1,'App\\Model\\helpdesk\\Form\\FormCategory',26,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:49:31','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(61,1,'App\\Model\\helpdesk\\Form\\FormCategory',27,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 12:49:31','2021-06-02 12:44:26',1,1,0,'',NULL,1,1,1,1,1,1),(62,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',NULL,'2021-02-17 17:15:17','2021-06-02 10:43:08',1,1,0,'',1,1,1,1,1,1,1),(63,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',4,'2021-02-17 17:15:17','2021-06-02 10:43:08',1,1,0,'',NULL,1,1,1,1,1,1),(64,NULL,'',1,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 17:15:17','2021-06-02 10:43:08',1,1,0,'',1,1,1,1,1,1,1),(66,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',NULL,'2021-02-17 17:20:07','2021-06-11 07:09:59',1,1,0,'',2,1,1,1,1,1,1),(67,NULL,'',1,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 17:20:07','2021-06-11 07:09:59',1,1,0,'',2,1,1,1,1,1,1),(68,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',NULL,'2021-02-17 17:25:11','2021-03-08 11:57:37',1,1,0,'',3,1,1,1,1,1,1),(69,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',16,'2021-02-17 17:25:11','2021-03-08 11:57:37',1,1,0,'',NULL,1,1,1,1,1,1),(70,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',19,'2021-02-17 17:25:11','2021-03-08 11:57:37',1,1,0,'',NULL,1,1,1,1,1,1),(71,NULL,'',1,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 17:25:11','2021-03-08 11:57:37',1,1,0,'',3,1,1,1,1,1,1),(72,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',NULL,'2021-02-17 17:51:02','2021-06-23 10:34:39',1,1,0,'',4,1,1,1,1,1,1),(73,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',24,'2021-02-17 17:51:02','2021-06-23 10:34:39',1,1,0,'',NULL,1,1,1,1,1,1),(74,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',29,'2021-02-17 17:51:02','2021-06-23 10:34:39',1,1,0,'',NULL,1,1,1,1,1,1),(75,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',38,'2021-02-17 17:51:02','2021-06-23 10:34:39',1,1,0,'',NULL,1,1,1,1,1,1),(76,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',45,'2021-02-17 17:51:02','2021-06-23 10:34:39',1,1,0,'',NULL,1,1,1,1,1,1),(77,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',49,'2021-02-17 17:51:02','2021-06-23 10:34:39',1,1,0,'',NULL,1,1,1,1,1,1),(78,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',52,'2021-02-17 17:51:02','2021-06-23 10:34:39',1,1,0,'',NULL,1,1,1,1,1,1),(79,NULL,'',1,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 17:51:02','2021-06-23 10:34:39',1,1,0,'',4,1,1,1,1,1,1),(80,NULL,'',2,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 17:51:03','2021-06-23 10:34:39',1,1,0,'',4,1,1,1,1,1,1),(81,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',64,'2021-02-17 17:59:43','2021-06-23 10:34:39',1,1,0,'',NULL,1,1,1,1,1,1),(82,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',NULL,'2021-02-17 18:10:39','2021-03-08 11:59:18',1,1,0,'',5,1,1,1,1,1,1),(83,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',81,'2021-02-17 18:10:39','2021-03-08 11:59:18',1,1,0,'',NULL,1,1,1,1,1,1),(84,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',86,'2021-02-17 18:10:39','2021-03-08 11:59:18',1,1,0,'',NULL,1,1,1,1,1,1),(85,NULL,'',1,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 18:10:39','2021-03-08 11:59:18',1,1,0,'',5,1,1,1,1,1,1),(86,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',NULL,'2021-02-17 18:18:27','2021-05-25 10:39:45',1,1,0,'',6,1,1,1,1,1,1),(87,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',88,'2021-02-17 18:18:27','2021-05-25 10:39:45',1,1,0,'',NULL,1,1,1,1,1,1),(88,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',90,'2021-02-17 18:18:27','2021-05-25 10:39:45',1,1,0,'',NULL,1,1,1,1,1,1),(89,NULL,'',1,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 18:18:27','2021-05-25 10:39:45',1,1,0,'',6,1,1,1,1,1,1),(90,NULL,'',2,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 18:18:27','2021-05-25 10:39:45',1,1,0,'',6,1,1,1,1,1,1),(91,NULL,'',3,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 18:18:27','2021-05-25 10:39:45',1,1,0,'',6,1,1,1,1,1,1),(92,NULL,'',4,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 18:18:27','2021-05-25 10:39:45',1,1,0,'',6,1,1,1,1,1,1),(93,NULL,'',5,'Text Field','text',1,1,1,1,0,0,0,NULL,'',NULL,'2021-02-17 18:18:27','2021-05-25 10:39:45',1,1,0,'',6,1,1,1,1,1,1),(94,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',NULL,'2021-02-17 18:23:11','2021-03-08 12:01:34',1,1,0,'',7,1,1,1,1,1,1),(95,NULL,'',1,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 18:23:11','2021-03-08 12:01:34',1,1,0,'',7,1,1,1,1,1,1),(96,NULL,'',2,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 18:23:11','2021-03-08 12:01:34',1,1,0,'',7,1,1,1,1,1,1),(97,NULL,'',3,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 18:23:11','2021-03-08 12:01:34',1,1,0,'',7,1,1,1,1,1,1),(98,NULL,'',4,'Text Field','text',0,0,1,1,0,0,0,NULL,'',NULL,'2021-02-17 18:23:11','2021-03-08 12:01:34',1,1,0,'',7,1,1,1,1,1,1),(99,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',NULL,'2021-05-24 10:01:07','2021-06-23 10:33:21',1,1,0,'',8,1,1,1,1,1,1),(100,NULL,'',0,'Text Field','text',1,1,1,1,0,0,0,NULL,'',107,'2021-05-24 10:14:58','2021-06-23 10:33:21',1,1,0,'',NULL,1,1,1,1,1,1),(101,NULL,'',1,'Text Field','text',1,1,1,1,0,0,0,NULL,'',107,'2021-05-24 10:14:58','2021-06-23 10:33:21',1,1,0,'',NULL,1,1,1,1,1,1),(141,NULL,'',2,'Text Field','text',1,1,1,1,0,0,0,NULL,'',NULL,'2021-06-23 10:25:25','2021-06-23 10:28:36',1,1,0,'',10,1,1,1,1,1,1),(104,NULL,'',3,'Text Field','text',0,0,1,1,0,0,0,NULL,'',107,'2021-05-24 10:14:58','2021-06-23 10:33:21',1,1,0,'',NULL,1,1,1,1,1,1),(134,NULL,'',2,'Date','date',1,1,1,1,0,0,0,NULL,'',107,'2021-06-22 08:33:49','2021-06-23 10:33:21',1,1,0,'',NULL,1,1,1,1,1,1),(106,NULL,'',4,'Text Field','text',1,1,1,1,0,0,0,NULL,'',107,'2021-05-24 10:14:58','2021-06-23 10:33:21',1,1,0,'',NULL,1,1,1,1,1,1),(116,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',NULL,'2021-06-14 11:22:11','2021-06-14 11:22:11',1,1,0,'',9,1,1,1,1,1,1),(117,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',NULL,'2021-06-16 09:58:07','2021-06-23 10:28:36',1,1,0,'',10,1,1,1,1,1,1),(125,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',113,'2021-06-21 10:12:31','2021-06-23 10:28:36',1,1,0,'',NULL,1,1,1,1,1,1),(122,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',112,'2021-06-21 10:12:31','2021-06-23 10:28:36',1,1,0,'',NULL,1,1,1,1,1,1),(140,NULL,'',1,'Text Field','text',1,1,1,1,0,0,0,NULL,'',NULL,'2021-06-23 10:25:25','2021-06-23 10:28:36',1,1,0,'',10,1,1,1,1,1,1),(128,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',114,'2021-06-21 10:12:31','2021-06-23 10:28:36',1,1,0,'',NULL,1,1,1,1,1,1),(131,NULL,'',0,'Select','select',1,1,1,1,0,0,0,NULL,'',115,'2021-06-21 10:12:31','2021-06-23 10:28:36',1,1,0,'',NULL,1,1,1,1,1,1);
/*!40000 ALTER TABLE `form_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_field_labels`
--

DROP TABLE IF EXISTS `form_field_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_field_labels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meant_for` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `labelable_id` int(10) unsigned DEFAULT NULL,
  `labelable_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=388 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_field_labels`
--

LOCK TABLES `form_field_labels` WRITE;
/*!40000 ALTER TABLE `form_field_labels` DISABLE KEYS */;
INSERT INTO `form_field_labels` VALUES (1,'en','Requester','form_field',1,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(3,'en','CC','form_field',2,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(5,'en','Subject','form_field',3,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(76,'en','Description','form_field',44,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:39:08','2021-02-17 05:39:08',''),(7,'en','Status','form_field',4,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(9,'en','Priority','form_field',5,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(11,'en','Location','form_field',6,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(75,'en','Assigned On','form_field',43,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:39:08','2021-02-17 05:39:08',''),(13,'en','Source','form_field',7,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(15,'en','Case Type','form_field',8,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-03-08 11:47:50',''),(17,'en','Department','form_field',9,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(19,'en','Type','form_field',10,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(74,'en','Asset Type','form_field',42,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:39:08','2021-02-17 05:39:08',''),(21,'en','Assigned','form_field',11,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(23,'en','Description','form_field',12,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(73,'en','Product','form_field',41,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:39:08','2021-02-17 05:39:08',''),(25,'en','Captcha','form_field',13,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(72,'en','Used By','form_field',40,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:39:08','2021-02-17 05:39:08',''),(27,'en','Organisation Name','form_field',14,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(29,'en','Phone','form_field',15,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(31,'en','Organisation Domain Name','form_field',16,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(33,'en','Description','form_field',17,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(71,'en','Managed By','form_field',39,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:39:08','2021-02-17 05:39:08',''),(35,'en','Address','form_field',18,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(70,'en','Location','form_field',38,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:39:08','2021-02-17 05:39:08',''),(37,'en','Organisation Logo','form_field',19,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(39,'en','Organisation Department','form_field',20,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(69,'en','Organization','form_field',37,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:39:08','2021-02-17 05:39:08',''),(41,'en','Captcha','form_field',21,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(43,'en','First Name','form_field',22,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(45,'en','Last Name','form_field',23,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(47,'en','User Name','form_field',24,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(49,'en','Work Phone','form_field',25,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(51,'en','Email','form_field',26,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(53,'en','Mobile Phone','form_field',27,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(68,'en','Impact Type','form_field',36,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:39:08','2021-02-17 05:39:08',''),(55,'en','Address','form_field',28,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(67,'en','Department','form_field',35,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:39:08','2021-02-17 05:39:08',''),(57,'en','Organisation','form_field',29,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(59,'en','Organisation Department','form_field',30,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(66,'en','Identifier','form_field',34,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:39:08','2021-02-17 05:39:08',''),(61,'en','Captcha','form_field',31,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(65,'en','Name','form_field',33,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:39:08','2021-02-17 05:39:08',''),(63,'en','Select Package','form_field',32,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:35:48','2021-02-17 05:35:50',''),(77,'en','Attachments','form_field',45,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:39:08','2021-02-17 05:39:08',''),(78,'en','Asset Status','form_field',46,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 05:39:08','2021-02-17 05:39:08',''),(79,'en','DNextSeverity','form_field',47,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:41:21','2021-02-17 12:41:21',''),(80,'en','','validation',47,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:41:21','2021-02-17 12:41:21',''),(285,'en','3','option',103,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-03-09 12:18:05','2021-03-09 12:18:05',''),(283,'en','1','option',101,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-03-09 12:18:05','2021-03-09 12:18:05',''),(84,'en','DNextDescription','form_field',48,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(85,'en','','validation',48,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(86,'en','DNextRequestedResolutionDate','form_field',49,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(87,'en','','validation',49,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(88,'en','DNextBaseType','form_field',50,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(89,'en','','validation',50,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(90,'en','DNextSchemaLocation','form_field',51,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(91,'en','','validation',51,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(92,'en','DNextType','form_field',52,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(93,'en','','validation',52,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(94,'en','DNextRelatedPartyHref','form_field',53,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-03-23 09:48:55',''),(95,'en','','validation',53,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(96,'en','DNextRelatedPartyId','form_field',54,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-03-23 09:48:55',''),(97,'en','','validation',54,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(98,'en','DNextRelatedPartyName','form_field',55,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-03-23 09:48:55',''),(99,'en','','validation',55,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(100,'en','DNextRelatedPartyRole','form_field',56,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-03-23 09:48:55',''),(101,'en','','validation',56,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(102,'en','DNextRelatedPartyBaseType','form_field',57,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-03-23 09:48:55',''),(103,'en','','validation',57,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(104,'en','DNextRelatedPartyReferredType','form_field',58,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-03-23 09:48:55',''),(105,'en','','validation',58,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(106,'en','DNextRelatedPartySchemaLocation','form_field',59,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-03-23 09:48:55',''),(107,'en','','validation',59,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(108,'en','DNextRelatedPartyType','form_field',60,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-03-23 09:48:55',''),(109,'en','','validation',60,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(110,'en','DNextContextUrl','form_field',61,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:31','2021-02-17 12:49:31',''),(111,'en','','validation',61,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 12:49:32','2021-02-17 12:49:32',''),(112,'en','Sub Type','form_field',62,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:15:17','2021-02-17 17:15:17',''),(113,'en','','validation',62,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:15:17','2021-02-17 17:15:17',''),(114,'en','Sales Related','option',4,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:15:17','2021-02-17 17:15:17',''),(115,'en','Issue Category','form_field',63,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:15:17','2021-02-17 17:15:17',''),(116,'en','','validation',63,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:15:17','2021-02-17 17:15:17',''),(117,'en','I want to upgrade or change my plan (or contract)','option',5,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:15:17','2021-02-17 17:15:17',''),(118,'en','I am moving/moved; what do I need to do?','option',6,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:15:17','2021-02-17 17:15:17',''),(119,'en','Where and how can I make a full payment?','option',7,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:15:17','2021-02-17 17:15:17',''),(120,'en','Support Question','option',8,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:15:17','2021-02-17 17:15:17',''),(121,'en','Dealership Question','option',9,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:15:17','2021-02-17 17:15:17',''),(122,'en','Others','option',10,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:15:17','2021-02-17 17:15:17',''),(123,'en','Product Information','form_field',64,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:15:17','2021-02-17 17:15:17',''),(124,'en','','validation',64,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:15:17','2021-02-17 17:15:17',''),(128,'en','','validation',66,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:20:07','2021-02-17 17:20:07',''),(127,'en','Sub Types','form_field',66,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:20:07','2021-02-17 17:20:07',''),(129,'en','Coaxial Network','option',11,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:20:07','2021-02-17 17:20:07',''),(130,'en','Fiber Optics','option',12,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:20:07','2021-02-17 17:20:07',''),(131,'en','Internet Broadband','option',13,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:20:07','2021-02-17 17:20:07',''),(132,'en','TV Products','option',14,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:20:07','2021-02-17 17:20:07',''),(133,'en','Converged Offers','option',15,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:20:07','2021-02-17 17:20:07',''),(134,'en','Product Information','form_field',67,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:20:07','2021-02-17 17:20:07',''),(135,'en','','validation',67,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:20:07','2021-02-17 17:20:07',''),(136,'en','Sub Types','form_field',68,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(137,'en','','validation',68,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(138,'en','Installation Related Issues','option',16,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(139,'en','Issue Category','form_field',69,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(140,'en','','validation',69,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(141,'en','Complaint about the installation','option',17,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(142,'en','When is my engineer coming or they missed an appointment','option',18,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(143,'en','Repair Issues','option',19,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(144,'en','Issue Category','form_field',70,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(145,'en','','validation',70,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(146,'en','Repair ETR','option',20,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(147,'en','Hardware Issues','option',21,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(148,'en','Software Issues','option',22,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(149,'en','Reporting Issues','option',23,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(150,'en','Product Information','form_field',71,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(151,'en','','validation',71,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:25:11','2021-02-17 17:25:11',''),(152,'en','Sub Types','form_field',72,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(153,'en','','validation',72,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(154,'en','Network Issues','option',24,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(155,'en','Issue Category','form_field',73,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(156,'en','','validation',73,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(157,'en','Service loss','option',25,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(158,'en','Service interruption','option',26,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(159,'en','Slow internet','option',27,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(160,'en','IP Issue','option',28,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(161,'en','Connectivity','option',29,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(162,'en','Issue Category','form_field',74,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(163,'en','','validation',74,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(164,'en','Node Issue','option',30,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(165,'en','Fiber Cut','option',31,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(166,'en','Coax Cable Cut','option',32,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(167,'en','Authentication Issue','option',33,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(168,'en','No-Internet Configuration File','option',34,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(169,'en','GIS Fault','option',35,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(170,'en','Connector Fault','option',36,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(171,'en','Tap Signal Issue','option',37,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(172,'en','WiFi','option',38,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(173,'en','Issue Category','form_field',75,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(174,'en','','validation',75,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(175,'en','WiFi Authentication Failure','option',39,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(176,'en','WiFi Configuration Issues','option',40,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(177,'en','Coverage Issues','option',41,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(178,'en','Interference Issue','option',42,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(179,'en','Booster Problem','option',43,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(180,'en','TV Related Issues','option',44,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(181,'en','Lack of Access','option',45,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(182,'en','Issue Category','form_field',76,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(183,'en','','validation',76,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(184,'en','No Access','option',46,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(185,'en','OSS Code','option',47,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(186,'en','Scrambled Channel','option',48,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(187,'en','Lack of Signal','option',49,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(188,'en','Issue Category','form_field',77,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(189,'en','','validation',77,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(190,'en','No Signal','option',50,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(191,'en','Some Channels Are Missing, Signal Frequency Issue','option',51,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(192,'en','Telephony Issues','option',52,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(193,'en','Issue Category','form_field',78,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(194,'en','','validation',78,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(195,'en','Number Is Not Configured','option',53,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(196,'en','Number Not Ported In','option',54,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(197,'en','CPE Does Not Support Telephony Service','option',55,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(198,'en','Issue With Customer Device','option',56,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(199,'en','Issue With Internet','option',57,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(200,'en','Inbound Calls Issue','option',58,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(201,'en','Outbound Calls Issue','option',59,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(202,'en','Dropped Calls','option',60,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(203,'en','Audio Issues/Echo','option',61,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(204,'en','Order Related Issues','option',62,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(205,'en','Mobile Specific Issues','option',63,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(206,'en','Device Issues','option',64,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:51:02','2021-02-17 17:51:02',''),(207,'en','Product Information','form_field',79,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:03','2021-02-17 17:51:03',''),(208,'en','','validation',79,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:03','2021-02-17 17:51:03',''),(209,'en','Installation Ticket Number','form_field',80,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:03','2021-02-17 17:51:03',''),(210,'en','','validation',80,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:51:03','2021-02-17 17:51:03',''),(211,'en','Issue Category','form_field',81,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(212,'en','','validation',81,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(213,'en','UI Not Accessible','option',65,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(214,'en','No Traffic','option',66,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(215,'en','Restart','option',67,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(216,'en','Reset','option',68,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(217,'en','WiFi Card Issue','option',69,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(218,'en','LAN Ports Issue','option',70,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(219,'en','Power Adapter Failure','option',71,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(220,'en','Power Unit Failure','option',72,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(221,'en','LAN Issues','option',73,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(222,'en','Issue with Customer Personal CPE','option',74,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(223,'en','Not processing US/DS Signal','option',75,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(224,'en','HDMI Cable Issue','option',76,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(225,'en','STB HDMI Port Failure','option',77,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(226,'en','TV HDMI Port Failure','option',78,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(227,'en','Smart Card Issue','option',79,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(228,'en','RCU Issue','option',80,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 17:59:43','2021-02-17 17:59:43',''),(229,'en','Sub Types','form_field',82,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(230,'en','','validation',82,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(231,'en','Registration & Account','option',81,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(232,'en','Issue Category','form_field',83,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(233,'en','','validation',83,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(234,'en','I Need Help Using My Vodafone, Online Account','option',82,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(235,'en','E-Care Issues','option',83,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(236,'en','Bonus Complaints','option',84,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(237,'en','Individual Offers','option',85,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(238,'en','Online Payments','option',86,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(239,'en','Issue Category','form_field',84,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(240,'en','','validation',84,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(241,'en','How Do I Access The Online Payment Screen?','option',87,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(242,'en','Product Information','form_field',85,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(243,'en','','validation',85,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:10:39','2021-02-17 18:10:39',''),(244,'en','Sub Types','form_field',86,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(245,'en','','validation',86,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(246,'en','Invoice Issues','option',88,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(247,'en','Issue Category','form_field',87,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(248,'en','','validation',87,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(249,'en','How Do I View My Bill Online?','option',89,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(250,'en','Refunds & Reimbursement','option',90,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(251,'en','Issue Category','form_field',88,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(252,'en','','validation',88,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(253,'en','I Would Like to Return My Order','option',91,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(254,'en','Deposits','option',92,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(255,'en','Debt & Unbilled Usage','option',93,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(256,'en','Product Information','form_field',89,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(257,'en','','validation',89,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(258,'en','Invoice Number','form_field',90,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(259,'en','','validation',90,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(260,'en','Billing Account','form_field',91,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(261,'en','','validation',91,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(262,'en','Invoice Amount','form_field',92,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(263,'en','','validation',92,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(264,'en','Refund Amount','form_field',93,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(265,'en','','validation',93,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:18:27','2021-02-17 18:18:27',''),(266,'en','Sub Type','form_field',94,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:23:11','2021-02-17 18:23:11',''),(267,'en','','validation',94,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:23:11','2021-02-17 18:23:11',''),(268,'en','Partner Account Issues','option',94,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:23:11','2021-02-17 18:23:11',''),(269,'en','Shop Related Issues','option',95,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:23:11','2021-02-17 18:23:11',''),(270,'en','Dealer Related Issues','option',96,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-02-17 18:23:11','2021-02-17 18:23:11',''),(271,'en','Product Information','form_field',95,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:23:11','2021-02-17 18:23:11',''),(272,'en','','validation',95,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:23:11','2021-02-17 18:23:11',''),(273,'en','Partner ID','form_field',96,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:23:11','2021-02-17 18:23:11',''),(274,'en','','validation',96,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:23:11','2021-02-17 18:23:11',''),(275,'en','Partner Contact','form_field',97,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:23:11','2021-02-17 18:23:11',''),(276,'en','','validation',97,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:23:11','2021-02-17 18:23:11',''),(277,'en','POS Number','form_field',98,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:23:11','2021-02-17 18:23:11',''),(278,'en','','validation',98,'App\\Model\\helpdesk\\Form\\FormField','2021-02-17 18:23:11','2021-02-17 18:23:11',''),(289,'en','Sub Types','form_field',99,'App\\Model\\helpdesk\\Form\\FormField','2021-05-24 10:01:07','2021-05-24 10:14:58',''),(284,'en','2','option',102,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-03-09 12:18:05','2021-03-09 12:18:05',''),(290,'en','','validation',99,'App\\Model\\helpdesk\\Form\\FormField','2021-05-24 10:01:07','2021-05-24 10:01:07',''),(286,'en','4','option',104,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-03-09 12:18:05','2021-03-09 12:18:05',''),(291,'en','New Installation','option',107,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-05-24 10:01:07','2021-05-24 10:01:07',''),(292,'en','Optimization','option',108,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-05-24 10:01:07','2021-05-24 10:01:07',''),(293,'en','Support','option',109,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-05-24 10:01:07','2021-05-24 10:01:07',''),(294,'en','Shop','form_field',100,'App\\Model\\helpdesk\\Form\\FormField','2021-05-24 10:14:58','2021-05-24 10:14:58',''),(295,'en','','validation',100,'App\\Model\\helpdesk\\Form\\FormField','2021-05-24 10:14:58','2021-05-24 10:14:58',''),(296,'en','Service Address City','form_field',101,'App\\Model\\helpdesk\\Form\\FormField','2021-05-24 10:14:58','2021-05-24 10:14:58',''),(297,'en','','validation',101,'App\\Model\\helpdesk\\Form\\FormField','2021-05-24 10:14:58','2021-05-24 10:14:58',''),(386,'en','Error on System','form_field',141,'App\\Model\\helpdesk\\Form\\FormField','2021-06-23 10:25:25','2021-06-23 10:25:25',''),(385,'en','','validation',140,'App\\Model\\helpdesk\\Form\\FormField','2021-06-23 10:25:25','2021-06-23 10:25:25',''),(302,'en','MAC Address','form_field',104,'App\\Model\\helpdesk\\Form\\FormField','2021-05-24 10:14:58','2021-05-24 10:14:58',''),(303,'en','','validation',104,'App\\Model\\helpdesk\\Form\\FormField','2021-05-24 10:14:58','2021-05-24 10:14:58',''),(373,'en','','validation',134,'App\\Model\\helpdesk\\Form\\FormField','2021-06-22 08:33:49','2021-06-22 08:33:49',''),(372,'en','Service Activation Date','form_field',134,'App\\Model\\helpdesk\\Form\\FormField','2021-06-22 08:33:49','2021-06-23 10:33:21',''),(306,'en','Order Item Number','form_field',106,'App\\Model\\helpdesk\\Form\\FormField','2021-05-24 10:14:58','2021-05-24 10:14:58',''),(307,'en','','validation',106,'App\\Model\\helpdesk\\Form\\FormField','2021-05-24 10:14:58','2021-05-24 10:14:58',''),(326,'en','Select','form_field',116,'App\\Model\\helpdesk\\Form\\FormField','2021-06-14 11:22:11','2021-06-14 11:22:11',''),(327,'en','','validation',116,'App\\Model\\helpdesk\\Form\\FormField','2021-06-14 11:22:11','2021-06-14 11:22:11',''),(328,'en','Support','option',110,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-06-14 11:22:11','2021-06-14 11:22:11',''),(329,'en','Sub Type','form_field',117,'App\\Model\\helpdesk\\Form\\FormField','2021-06-16 09:58:07','2021-06-16 09:58:07',''),(330,'en','','validation',117,'App\\Model\\helpdesk\\Form\\FormField','2021-06-16 09:58:07','2021-06-16 09:58:07',''),(344,'en','Issue Category','form_field',122,'App\\Model\\helpdesk\\Form\\FormField','2021-06-21 10:12:31','2021-06-21 10:12:31',''),(345,'en','','validation',122,'App\\Model\\helpdesk\\Form\\FormField','2021-06-21 10:12:31','2021-06-21 10:12:31',''),(343,'en','Vodafone TV','option',115,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-06-21 07:40:58','2021-06-21 07:40:58',''),(346,'en','Error Ticket','option',116,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-06-21 10:12:31','2021-06-21 10:12:31',''),(342,'en','VOIP','option',114,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-06-21 07:40:58','2021-06-21 07:40:58',''),(341,'en','CAS TV','option',113,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-06-21 07:40:58','2021-06-21 07:40:58',''),(340,'en','Internet Product','option',112,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-06-21 07:40:58','2021-06-21 07:40:58',''),(384,'en','Order Item ID','form_field',140,'App\\Model\\helpdesk\\Form\\FormField','2021-06-23 10:25:25','2021-06-23 10:25:25',''),(351,'en','Issue Category','form_field',125,'App\\Model\\helpdesk\\Form\\FormField','2021-06-21 10:12:31','2021-06-21 10:12:31',''),(352,'en','','validation',125,'App\\Model\\helpdesk\\Form\\FormField','2021-06-21 10:12:31','2021-06-21 10:12:31',''),(353,'en','Error Ticket','option',117,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-06-21 10:12:31','2021-06-21 10:12:31',''),(358,'en','Issue Category','form_field',128,'App\\Model\\helpdesk\\Form\\FormField','2021-06-21 10:12:31','2021-06-21 10:12:31',''),(359,'en','','validation',128,'App\\Model\\helpdesk\\Form\\FormField','2021-06-21 10:12:31','2021-06-21 10:12:31',''),(360,'en','Error Ticket','option',118,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-06-21 10:12:31','2021-06-21 10:12:31',''),(365,'en','Issue Category','form_field',131,'App\\Model\\helpdesk\\Form\\FormField','2021-06-21 10:12:31','2021-06-21 10:12:31',''),(366,'en','','validation',131,'App\\Model\\helpdesk\\Form\\FormField','2021-06-21 10:12:31','2021-06-21 10:12:31',''),(367,'en','Error Ticket','option',119,'App\\Model\\helpdesk\\Form\\FormFieldOption','2021-06-21 10:12:31','2021-06-21 10:12:31',''),(387,'en','','validation',141,'App\\Model\\helpdesk\\Form\\FormField','2021-06-23 10:25:25','2021-06-23 10:25:25','');
/*!40000 ALTER TABLE `form_field_labels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_field_options`
--

DROP TABLE IF EXISTS `form_field_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_field_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_field_id` int(10) unsigned NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_field_options`
--

LOCK TABLES `form_field_options` WRITE;
/*!40000 ALTER TABLE `form_field_options` DISABLE KEYS */;
INSERT INTO `form_field_options` VALUES (103,47,'','2021-03-09 12:18:05','2021-05-21 15:01:53',2),(101,47,'','2021-03-09 12:18:05','2021-05-21 15:01:53',0),(4,62,'','2021-02-17 17:15:17','2021-02-17 17:15:17',0),(5,63,'','2021-02-17 17:15:17','2021-02-17 17:15:17',0),(6,63,'','2021-02-17 17:15:17','2021-02-17 17:15:17',1),(7,63,'','2021-02-17 17:15:17','2021-02-17 17:15:17',2),(8,62,'','2021-02-17 17:15:17','2021-02-17 17:15:17',1),(9,62,'','2021-02-17 17:15:17','2021-02-17 17:15:17',2),(10,62,'','2021-02-17 17:15:17','2021-02-17 17:15:17',3),(11,66,'','2021-02-17 17:20:07','2021-02-17 17:20:07',0),(12,66,'','2021-02-17 17:20:07','2021-02-17 17:20:07',1),(13,66,'','2021-02-17 17:20:07','2021-02-17 17:20:07',2),(14,66,'','2021-02-17 17:20:07','2021-02-17 17:20:07',3),(15,66,'','2021-02-17 17:20:07','2021-02-17 17:20:07',4),(16,68,'','2021-02-17 17:25:11','2021-02-17 17:25:11',0),(17,69,'','2021-02-17 17:25:11','2021-02-17 17:25:11',0),(18,69,'','2021-02-17 17:25:11','2021-02-17 17:25:11',1),(19,68,'','2021-02-17 17:25:11','2021-02-17 17:25:11',1),(20,70,'','2021-02-17 17:25:11','2021-02-17 17:25:11',0),(21,68,'','2021-02-17 17:25:11','2021-02-17 17:25:11',2),(22,68,'','2021-02-17 17:25:11','2021-02-17 17:25:11',3),(23,68,'','2021-02-17 17:25:11','2021-02-17 17:25:11',4),(24,72,'','2021-02-17 17:51:02','2021-02-17 17:51:02',0),(25,73,'','2021-02-17 17:51:02','2021-02-17 17:51:02',0),(26,73,'','2021-02-17 17:51:02','2021-02-17 17:51:02',1),(27,73,'','2021-02-17 17:51:02','2021-02-17 17:51:02',2),(28,73,'','2021-02-17 17:51:02','2021-02-17 17:51:02',3),(29,72,'','2021-02-17 17:51:02','2021-02-17 17:51:02',1),(30,74,'','2021-02-17 17:51:02','2021-02-17 17:51:02',0),(31,74,'','2021-02-17 17:51:02','2021-02-17 17:51:02',1),(32,74,'','2021-02-17 17:51:02','2021-02-17 17:51:02',2),(33,74,'','2021-02-17 17:51:02','2021-02-17 17:51:02',3),(34,74,'','2021-02-17 17:51:02','2021-02-17 17:51:02',4),(35,74,'','2021-02-17 17:51:02','2021-02-17 17:51:02',5),(36,74,'','2021-02-17 17:51:02','2021-02-17 17:51:02',6),(37,74,'','2021-02-17 17:51:02','2021-02-17 17:51:02',7),(38,72,'','2021-02-17 17:51:02','2021-02-17 17:51:02',2),(39,75,'','2021-02-17 17:51:02','2021-02-17 17:51:02',0),(40,75,'','2021-02-17 17:51:02','2021-02-17 17:51:02',1),(41,75,'','2021-02-17 17:51:02','2021-02-17 17:51:02',2),(42,75,'','2021-02-17 17:51:02','2021-02-17 17:51:02',3),(43,75,'','2021-02-17 17:51:02','2021-02-17 17:51:02',4),(44,72,'','2021-02-17 17:51:02','2021-02-17 17:51:02',3),(45,72,'','2021-02-17 17:51:02','2021-02-17 17:51:02',4),(46,76,'','2021-02-17 17:51:02','2021-02-17 17:51:02',0),(47,76,'','2021-02-17 17:51:02','2021-02-17 17:51:02',1),(48,76,'','2021-02-17 17:51:02','2021-02-17 17:51:02',2),(49,72,'','2021-02-17 17:51:02','2021-02-17 17:51:02',5),(50,77,'','2021-02-17 17:51:02','2021-02-17 17:51:02',0),(51,77,'','2021-02-17 17:51:02','2021-02-17 17:51:02',1),(52,72,'','2021-02-17 17:51:02','2021-02-17 17:51:02',6),(53,78,'','2021-02-17 17:51:02','2021-02-17 17:51:02',0),(54,78,'','2021-02-17 17:51:02','2021-02-17 17:51:02',1),(55,78,'','2021-02-17 17:51:02','2021-02-17 17:51:02',2),(56,78,'','2021-02-17 17:51:02','2021-02-17 17:51:02',3),(57,78,'','2021-02-17 17:51:02','2021-02-17 17:51:02',4),(58,78,'','2021-02-17 17:51:02','2021-02-17 17:51:02',5),(59,78,'','2021-02-17 17:51:02','2021-02-17 17:51:02',6),(60,78,'','2021-02-17 17:51:02','2021-02-17 17:51:02',7),(61,78,'','2021-02-17 17:51:02','2021-02-17 17:51:02',8),(62,72,'','2021-02-17 17:51:02','2021-02-17 17:51:02',7),(63,72,'','2021-02-17 17:51:02','2021-02-17 17:51:02',8),(64,72,'','2021-02-17 17:51:02','2021-02-17 17:51:02',9),(65,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',0),(66,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',1),(67,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',2),(68,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',3),(69,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',4),(70,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',5),(71,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',6),(72,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',7),(73,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',8),(74,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',9),(75,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',10),(76,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',11),(77,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',12),(78,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',13),(79,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',14),(80,81,'','2021-02-17 17:59:43','2021-02-17 17:59:43',15),(81,82,'','2021-02-17 18:10:39','2021-02-17 18:10:39',0),(82,83,'','2021-02-17 18:10:39','2021-02-17 18:10:39',0),(83,82,'','2021-02-17 18:10:39','2021-02-17 18:10:39',1),(84,82,'','2021-02-17 18:10:39','2021-02-17 18:10:39',2),(85,82,'','2021-02-17 18:10:39','2021-02-17 18:10:39',3),(86,82,'','2021-02-17 18:10:39','2021-02-17 18:10:39',4),(87,84,'','2021-02-17 18:10:39','2021-02-17 18:10:39',0),(88,86,'','2021-02-17 18:18:27','2021-02-17 18:18:27',0),(89,87,'','2021-02-17 18:18:27','2021-02-17 18:18:27',0),(90,86,'','2021-02-17 18:18:27','2021-02-17 18:18:27',1),(91,88,'','2021-02-17 18:18:27','2021-02-17 18:18:27',0),(92,86,'','2021-02-17 18:18:27','2021-02-17 18:18:27',2),(93,86,'','2021-02-17 18:18:27','2021-02-17 18:18:27',3),(94,94,'','2021-02-17 18:23:11','2021-02-17 18:23:11',0),(95,94,'','2021-02-17 18:23:11','2021-02-17 18:23:11',1),(96,94,'','2021-02-17 18:23:11','2021-02-17 18:23:11',2),(107,99,'','2021-05-24 10:01:07','2021-05-24 10:01:07',0),(102,47,'','2021-03-09 12:18:05','2021-05-21 15:01:53',1),(104,47,'','2021-03-09 12:18:05','2021-05-21 15:01:53',3),(108,99,'','2021-05-24 10:01:07','2021-05-24 10:01:07',1),(109,99,'','2021-05-24 10:01:07','2021-05-24 10:01:07',2),(110,116,'','2021-06-14 11:22:11','2021-06-14 11:22:11',0),(116,122,'','2021-06-21 10:12:31','2021-06-21 10:12:31',0),(112,117,'','2021-06-21 07:40:58','2021-06-21 10:12:31',0),(113,117,'','2021-06-21 07:40:58','2021-06-21 10:12:31',1),(114,117,'','2021-06-21 07:40:58','2021-06-21 10:12:31',2),(115,117,'','2021-06-21 07:40:58','2021-06-21 10:12:31',3),(117,125,'','2021-06-21 10:12:31','2021-06-21 10:12:31',0),(118,128,'','2021-06-21 10:12:31','2021-06-21 10:12:31',0),(119,131,'','2021-06-21 10:12:31','2021-06-21 10:12:31',0);
/*!40000 ALTER TABLE `form_field_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_groups`
--

DROP TABLE IF EXISTS `form_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `group_type` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ticket',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_groups`
--

LOCK TABLES `form_groups` WRITE;
/*!40000 ALTER TABLE `form_groups` DISABLE KEYS */;
INSERT INTO `form_groups` VALUES (1,'General Information',1,'2021-02-17 17:15:17','2021-02-17 17:15:17','ticket'),(2,'Fix Line Sales',1,'2021-02-17 17:17:12','2021-02-17 17:17:12','ticket'),(3,'Installation Issues',1,'2021-02-17 17:25:11','2021-02-17 17:25:11','ticket'),(4,'After-Sales Incident',1,'2021-02-17 17:51:02','2021-02-17 17:51:02','ticket'),(5,'Customer Self Service',1,'2021-02-17 18:10:39','2021-02-17 18:10:39','ticket'),(6,'Billing Issues',1,'2021-02-17 18:18:27','2021-02-17 18:18:27','ticket'),(7,'Partner Services',1,'2021-02-17 18:23:11','2021-02-17 18:23:11','ticket'),(8,'New Installations',1,'2021-05-24 10:01:07','2021-05-24 10:01:07','ticket'),(9,'Support query case',1,'2021-06-14 11:22:11','2021-06-14 11:22:11','ticket'),(10,'Manual Inspection Service',1,'2021-06-16 09:58:07','2021-06-16 09:58:07','ticket');
/*!40000 ALTER TABLE `form_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `help_topic`
--

DROP TABLE IF EXISTS `help_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `help_topic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent_topic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `custom_form` int(10) unsigned DEFAULT NULL,
  `department` int(10) unsigned DEFAULT NULL,
  `ticket_status` int(10) unsigned DEFAULT NULL,
  `thank_page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ticket_num_format` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `internal_notes` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `auto_response` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `linked_departments` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `help_topic_ibfk_2` (`department`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `help_topic`
--

LOCK TABLES `help_topic` WRITE;
/*!40000 ALTER TABLE `help_topic` DISABLE KEYS */;
INSERT INTO `help_topic` VALUES (1,'Support query','',NULL,1,1,'','1','',0,1,0,'2021-02-17 05:35:47','2021-06-14 11:32:45','1'),(2,'Sales query','',NULL,2,1,'','1','',0,1,0,'2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(3,'Operational query','',NULL,3,1,'','1','',0,1,0,'2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(4,'General Info Case Type','',NULL,1,NULL,'','','',1,1,0,'2021-02-17 13:11:51','2021-06-14 11:32:21','2'),(5,'Fix Line Sales Case Type','',NULL,4,NULL,'','','',1,1,0,'2021-02-17 13:16:48','2021-02-17 18:32:53','4'),(6,'Installation Issues Case Type','',NULL,3,NULL,'','','',1,1,0,'2021-02-17 13:17:37','2021-02-17 18:34:53','3'),(7,'After Sales Incident Case Type','',NULL,3,NULL,'','','',1,1,0,'2021-02-17 13:18:35','2021-02-17 18:30:07','3'),(8,'Customer Self-Service Case Type','',NULL,4,NULL,'','','',1,1,0,'2021-02-17 13:19:17','2021-02-17 18:32:00','4'),(9,'Billing Issues Case Type','',NULL,6,NULL,'','','',1,1,0,'2021-02-17 13:20:04','2021-02-17 18:31:21','6'),(10,'Partner Services Case Type','',NULL,3,NULL,'','','',1,1,0,'2021-02-17 13:20:41','2021-02-17 18:36:38','3'),(12,'Installation Management','',NULL,3,NULL,'','','',1,1,0,'2021-05-24 09:57:24','2021-05-25 12:34:33','3'),(13,'Manual Inspection Service','',NULL,3,NULL,'','','',1,1,0,'2021-06-16 09:20:44','2021-06-16 09:20:44','3');
/*!40000 ALTER TABLE `help_topic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `help_topic_form_group`
--

DROP TABLE IF EXISTS `help_topic_form_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `help_topic_form_group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_group_id` int(10) unsigned NOT NULL,
  `help_topic_id` int(10) unsigned NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `help_topic_form_group_form_group_id_foreign` (`form_group_id`),
  KEY `help_topic_form_group_help_topic_id_foreign` (`help_topic_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `help_topic_form_group`
--

LOCK TABLES `help_topic_form_group` WRITE;
/*!40000 ALTER TABLE `help_topic_form_group` DISABLE KEYS */;
INSERT INTO `help_topic_form_group` VALUES (1,1,4,0),(2,2,5,0),(3,3,6,0),(4,4,7,0),(5,5,8,0),(6,6,9,0),(7,7,10,0),(8,8,12,0),(9,9,1,0),(10,10,13,0);
/*!40000 ALTER TABLE `help_topic_form_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kb_settings`
--

DROP TABLE IF EXISTS `kb_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kb_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pagination` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL,
  `date_format` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'MMMM Do YYYY, h:mm:ss a',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kb_settings`
--

LOCK TABLES `kb_settings` WRITE;
/*!40000 ALTER TABLE `kb_settings` DISABLE KEYS */;
INSERT INTO `kb_settings` VALUES (1,10,'2021-02-17 05:35:47','2021-02-17 05:35:47',1,'MMMM Do YYYY, h:mm:ss a');
/*!40000 ALTER TABLE `kb_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'English','en'),(2,'Italian','it'),(3,'German','de'),(4,'French','fr'),(5,'Brazilian Portuguese','pt_BR'),(6,'Dutch','nl'),(7,'Spanish','es'),(8,'Norwegian','nb_NO'),(9,'Danish','da');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_default` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` VALUES (4,'Tirana','naweed.ahmed@pia-team.com','','Voda Fone Head Office \r\nTirana Albania',0,'2021-02-17 19:15:24','2021-02-17 19:15:24',0),(5,'Durres','naweed.ahmed@pia-team.com','','Voda Fone Site\r\nDurres Albania',0,'2021-02-17 19:16:05','2021-02-17 19:16:05',0),(6,'Vlora','naweed.ahmed@pia-team.com','','Voda Fone Site\r\nVlora Albania',0,'2021-02-17 19:16:42','2021-02-17 19:16:42',0);
/*!40000 ALTER TABLE `location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_categories`
--

DROP TABLE IF EXISTS `log_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_categories`
--

LOCK TABLES `log_categories` WRITE;
/*!40000 ALTER TABLE `log_categories` DISABLE KEYS */;
INSERT INTO `log_categories` VALUES (1,'ticket-create'),(2,'ticket-reply'),(3,'ticket-update'),(4,'ticket-escalate'),(5,'user-create'),(6,'user-update'),(7,'mail-fetch'),(8,'report'),(9,'rating'),(10,'default'),(11,'mail-send'),(12,'cron');
/*!40000 ALTER TABLE `log_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mailbox_protocol`
--

DROP TABLE IF EXISTS `mailbox_protocol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mailbox_protocol` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mailbox_protocol`
--

LOCK TABLES `mailbox_protocol` WRITE;
/*!40000 ALTER TABLE `mailbox_protocol` DISABLE KEYS */;
INSERT INTO `mailbox_protocol` VALUES (1,'IMAP','/imap'),(2,'IMAP+SSL','/imap/ssl'),(3,'IMAP+TLS','/imap/tls'),(4,'IMAP+SSL/No-validate','/imap/ssl/novalidate-cert');
/*!40000 ALTER TABLE `mailbox_protocol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_services`
--

DROP TABLE IF EXISTS `mail_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_services`
--

LOCK TABLES `mail_services` WRITE;
/*!40000 ALTER TABLE `mail_services` DISABLE KEYS */;
INSERT INTO `mail_services` VALUES (1,'SMTP','smtp','2021-02-17 05:35:47','2021-02-17 05:35:47'),(2,'Php Mail','mail','2021-02-17 05:35:47','2021-02-17 05:35:47'),(3,'Send Mail','sendmail','2021-02-17 05:35:47','2021-02-17 05:35:47'),(4,'Mailgun','mailgun','2021-02-17 05:35:47','2021-02-17 05:35:47'),(5,'Mandrill','mandrill','2021-02-17 05:35:47','2021-02-17 05:35:47'),(6,'Log file','log','2021-02-17 05:35:47','2021-02-17 05:35:47'),(7,'Mailrelay','mailrelay','2021-02-17 05:35:47','2021-02-17 05:35:47');
/*!40000 ALTER TABLE `mail_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugins`
--

DROP TABLE IF EXISTS `plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `version` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0.0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugins`
--

LOCK TABLES `plugins` WRITE;
/*!40000 ALTER TABLE `plugins` DISABLE KEYS */;
INSERT INTO `plugins` VALUES (1,'ServiceDesk','ServiceDesk',1,'2021-02-17 05:35:50','2021-02-17 05:39:08','4.0.1');
/*!40000 ALTER TABLE `plugins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `queue_services`
--

DROP TABLE IF EXISTS `queue_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `queue_services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queue_services`
--

LOCK TABLES `queue_services` WRITE;
/*!40000 ALTER TABLE `queue_services` DISABLE KEYS */;
INSERT INTO `queue_services` VALUES (1,'Sync','sync',1,'2021-02-17 05:35:47','2021-02-17 05:35:47'),(2,'Database','database',0,'2021-02-17 05:35:47','2021-02-17 05:35:47'),(3,'Beanstalkd','beanstalkd',0,'2021-02-17 05:35:47','2021-02-17 05:35:47'),(4,'SQS','sqs',0,'2021-02-17 05:35:47','2021-02-17 05:35:47'),(5,'Iron','iron',0,'2021-02-17 05:35:47','2021-02-17 05:35:47'),(6,'Redis','redis',0,'2021-02-17 05:35:47','2021-02-17 05:35:47');
/*!40000 ALTER TABLE `queue_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ratings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_order` int(11) NOT NULL,
  `allow_modification` int(11) NOT NULL,
  `rating_scale` int(11) NOT NULL,
  `rating_area` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `restrict` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `rating_icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ratings`
--

LOCK TABLES `ratings` WRITE;
/*!40000 ALTER TABLE `ratings` DISABLE KEYS */;
INSERT INTO `ratings` VALUES (1,'OverAll Satisfaction',1,1,5,'Helpdesk Area','','2021-02-17 05:35:47','2021-02-17 05:35:47','star'),(2,'Reply Rating',2,1,5,'Comment Area','','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL);
/*!40000 ALTER TABLE `ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon_class` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `category` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `view_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `export_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (1,'helpdesk-in-depth','helpdesk-in-depth-description',NULL,0,'helpdesk-in-depth','fa fa-support fa-stack-1x','helpdesk-analysis','reports/helpdesk-in-depth/1','api/agent/report-export/1',1,'2021-02-17 05:35:49','2021-02-17 05:35:49'),(2,'ticket-volume-trends','ticket-volume-trends-description',NULL,0,'ticket-volume-trends','fa fa-calendar fa-stack-1x','helpdesk-analysis','reports/ticket-volume-trends/2','api/agent/report-export/2',1,'2021-02-17 05:35:49','2021-02-17 05:35:49'),(3,'management-report','management-report-description',NULL,0,'management-report','fa fa-user-secret fa-stack-1x','helpdesk-analysis','reports/management-report/3','api/agent/report-export/3',1,'2021-02-17 05:35:49','2021-02-26 09:18:09'),(4,'agent-performance','agent-performance-description',NULL,0,'agent-performance','fa fa-user fa-stack-1x','productivity','reports/agent-performance/4','api/agent/report-export/4',1,'2021-02-17 05:35:49','2021-02-17 05:35:50'),(5,'department-performance','department-performance-description',NULL,0,'department-performance','fa fa-building fa-stack-1x','productivity','reports/department-performance/5','api/agent/report-export/5',1,'2021-02-17 05:35:49','2021-02-17 05:35:50'),(6,'team-performance','team-performance-description',NULL,0,'team-performance','fa fa-users fa-stack-1x','productivity','reports/team-performance/6','api/agent/report-export/6',1,'2021-02-17 05:35:49','2021-02-17 05:35:50'),(7,'performance-distribution','performance-distribution-description',NULL,0,'performance-distribution','fa fa-cubes fa-stack-1x','productivity','reports/performance-distribution/7','api/agent/report-export/7',1,'2021-02-17 05:35:49','2021-02-17 05:35:49'),(8,'top-customer-analysis','top-customer-analysis-description',NULL,0,'top-customer-analysis','fa fa-bank fa-stack-1x','customer-happiness','reports/top-customer-analysis/8','api/agent/report-export/8',1,'2021-02-17 05:35:49','2021-02-17 05:35:49');
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_columns`
--

DROP TABLE IF EXISTS `report_columns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_columns` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 0,
  `is_sortable` tinyint(1) NOT NULL DEFAULT 0,
  `is_timestamp` tinyint(1) NOT NULL DEFAULT 0,
  `timestamp_format` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_html` tinyint(1) NOT NULL DEFAULT 0,
  `is_custom` tinyint(1) NOT NULL DEFAULT 0,
  `equation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `type` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sub_report_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=105 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_columns`
--

LOCK TABLES `report_columns` WRITE;
/*!40000 ALTER TABLE `report_columns` DISABLE KEYS */;
INSERT INTO `report_columns` VALUES (1,'ticket_number','ticket_number',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',1,'management_report',4),(2,'subject','subject',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',2,'management_report',4),(3,'statuses.name','status',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',3,'management_report',4),(4,'department.name','department',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',4,'management_report',4),(5,'helptopic.name','helptopic',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',5,'management_report',4),(6,'types.name','type',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',6,'management_report',4),(7,'priority.name','priority',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',7,'management_report',4),(8,'user.name','owner',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',8,'management_report',4),(9,'organizations','organizations',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',9,'management_report',4),(10,'assigned.name','assigned_agent',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',10,'management_report',4),(11,'sources.name','source',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',11,'management_report',4),(12,'assigned_team.name','assigned_team',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',12,'management_report',4),(13,'creator.name','creator',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',13,'management_report',4),(14,'location.name','location',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',14,'management_report',4),(15,'time_tracked','time_tracked',1,0,0,NULL,0,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',15,'management_report',4),(16,'overdue','is_overdue',1,0,0,NULL,0,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',16,'management_report',4),(17,'is_response_sla','has_response_sla_met',1,1,0,NULL,0,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',17,'management_report',4),(18,'is_resolution_sla','has_resolution_sla_met',1,1,0,NULL,0,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',18,'management_report',4),(19,'labels','labels',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',19,'management_report',4),(20,'tags','tags',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',20,'management_report',4),(21,'resolution_time','resolved_in',1,1,0,NULL,0,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',21,'management_report',4),(22,'first_response_time','first_replied_at',1,1,1,NULL,0,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',22,'management_report',4),(23,'created_at','created_at',1,1,1,NULL,0,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',23,'management_report',4),(24,'updated_at','updated_at',1,1,1,NULL,0,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',24,'management_report',4),(25,'closed_at','closed_at',1,1,1,NULL,0,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',25,'management_report',4),(26,'description','description',0,0,0,NULL,0,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',26,'management_report',4),(27,'name','name',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',1,'agent_performance_report',5),(28,'assigned_tickets','assigned_tickets',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',2,'agent_performance_report',5),(29,'reopened_tickets','reopened_tickets',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',3,'agent_performance_report',5),(30,'resolved_tickets','resolved_tickets',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',4,'agent_performance_report',5),(31,'tickets_with_response_sla_met','tickets_with_response_sla_met',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',5,'agent_performance_report',5),(32,'tickets_with_resolution_sla_met','tickets_with_resolution_sla_met',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',6,'agent_performance_report',5),(33,'avg_resolution_time','avg_resolution_time',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:49',7,'agent_performance_report',5),(34,'avg_response_time','avg_response_time',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',8,'agent_performance_report',5),(35,'responses','responses',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',9,'agent_performance_report',5),(36,'name','name',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',1,'department_performance_report',6),(37,'assigned_tickets','assigned_tickets',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',2,'department_performance_report',6),(38,'reopened_tickets','reopened_tickets',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',3,'department_performance_report',6),(39,'resolved_tickets','resolved_tickets',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',4,'department_performance_report',6),(40,'tickets_with_response_sla_met','tickets_with_response_sla_met',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',5,'department_performance_report',6),(41,'tickets_with_resolution_sla_met','tickets_with_resolution_sla_met',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',6,'department_performance_report',6),(42,'avg_resolution_time','avg_resolution_time',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',7,'department_performance_report',6),(43,'avg_response_time','avg_response_time',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',8,'department_performance_report',6),(44,'responses','responses',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',9,'department_performance_report',6),(45,'name','name',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',1,'team_performance_report',7),(46,'assigned_tickets','assigned_tickets',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',2,'team_performance_report',7),(47,'reopened_tickets','reopened_tickets',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',3,'team_performance_report',7),(48,'resolved_tickets','resolved_tickets',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',4,'team_performance_report',7),(49,'tickets_with_response_sla_met','tickets_with_response_sla_met',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',5,'team_performance_report',7),(50,'tickets_with_resolution_sla_met','tickets_with_resolution_sla_met',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',6,'team_performance_report',7),(51,'avg_resolution_time','avg_resolution_time',1,1,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',7,'team_performance_report',7),(52,'avg_response_time','avg_response_time',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',8,'team_performance_report',7),(53,'responses','responses',1,0,0,NULL,1,0,'','2021-02-17 05:35:49','2021-02-17 05:35:50',9,'team_performance_report',7),(54,'custom_47','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',54,'',4),(55,'custom_48','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',55,'',4),(56,'custom_49','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',56,'',4),(57,'custom_50','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',57,'',4),(58,'custom_51','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',58,'',4),(59,'custom_52','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',59,'',4),(60,'custom_53','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',60,'',4),(61,'custom_54','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',61,'',4),(62,'custom_55','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',62,'',4),(63,'custom_56','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',63,'',4),(64,'custom_57','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',64,'',4),(65,'custom_58','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',65,'',4),(66,'custom_59','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',66,'',4),(67,'custom_60','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',67,'',4),(68,'custom_61','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',68,'',4),(69,'custom_62','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',69,'',4),(70,'custom_64','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',70,'',4),(71,'custom_66','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',71,'',4),(72,'custom_67','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',72,'',4),(73,'custom_68','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',73,'',4),(74,'custom_71','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',74,'',4),(75,'custom_72','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',75,'',4),(76,'custom_79','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',76,'',4),(77,'custom_80','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',77,'',4),(78,'custom_82','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',78,'',4),(79,'custom_85','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',79,'',4),(80,'custom_86','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',80,'',4),(81,'custom_89','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',81,'',4),(82,'custom_90','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',82,'',4),(83,'custom_91','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',83,'',4),(84,'custom_92','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',84,'',4),(85,'custom_93','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',85,'',4),(86,'custom_94','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',86,'',4),(87,'custom_95','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',87,'',4),(88,'custom_96','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',88,'',4),(89,'custom_97','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',89,'',4),(90,'custom_98','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',90,'',4),(91,'custom_63','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',91,'',4),(92,'custom_69','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',92,'',4),(93,'custom_70','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',93,'',4),(94,'custom_73','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',94,'',4),(95,'custom_74','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',95,'',4),(96,'custom_75','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',96,'',4),(97,'custom_76','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',97,'',4),(98,'custom_77','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',98,'',4),(99,'custom_78','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',99,'',4),(100,'custom_81','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',100,'',4),(101,'custom_83','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',101,'',4),(102,'custom_84','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',102,'',4),(103,'custom_87','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',103,'',4),(104,'custom_88','',0,0,0,NULL,1,0,'','2021-02-26 09:18:09','2021-02-26 09:18:09',104,'',4);
/*!40000 ALTER TABLE `report_columns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requireds`
--

DROP TABLE IF EXISTS `requireds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requireds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `option` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requireds`
--

LOCK TABLES `requireds` WRITE;
/*!40000 ALTER TABLE `requireds` DISABLE KEYS */;
INSERT INTO `requireds` VALUES (1,'ticket','requester','required','required',NULL,'','Requester','2021-02-17 05:35:48','2021-02-17 05:35:48'),(2,'ticket','subject','required','required',NULL,'','Subject','2021-02-17 05:35:48','2021-02-17 05:35:48'),(3,'ticket','status','required',NULL,NULL,'','Status','2021-02-17 05:35:48','2021-02-17 05:35:48'),(4,'ticket','priority','required','required',NULL,'','Priority','2021-02-17 05:35:48','2021-02-17 05:35:48'),(5,'ticket','location',NULL,NULL,NULL,'','Location','2021-02-17 05:35:48','2021-02-17 05:35:48'),(6,'ticket','help_topic','required','required',NULL,'','Help Topic','2021-02-17 05:35:48','2021-02-17 05:35:48'),(7,'ticket','department','required',NULL,NULL,'','Department','2021-02-17 05:35:48','2021-02-17 05:35:48'),(8,'ticket','type',NULL,NULL,NULL,'','Type','2021-02-17 05:35:48','2021-02-17 05:35:48'),(9,'ticket','assigned',NULL,NULL,NULL,'','Assigned','2021-02-17 05:35:48','2021-02-17 05:35:48'),(10,'ticket','description','required',NULL,NULL,'','Description','2021-02-17 05:35:48','2021-02-17 05:35:48'),(11,'ticket','',NULL,NULL,NULL,'','','2021-02-17 05:35:48','2021-02-17 05:35:48'),(12,'ticket','company',NULL,NULL,NULL,'','Organization','2021-02-17 05:35:48','2021-02-17 05:35:48'),(13,'ticket','org_dept',NULL,NULL,NULL,'','Organisation Department','2021-02-17 05:35:48','2021-02-17 05:35:48'),(14,'user','first_name','required','required',NULL,'','First Name','2021-02-17 05:35:48','2021-02-17 05:35:48'),(15,'user','last_name','required','required',NULL,'','Last Name','2021-02-17 05:35:48','2021-02-17 05:35:48'),(16,'user','user_name',NULL,NULL,NULL,'','User Name','2021-02-17 05:35:48','2021-02-17 05:35:48'),(17,'user','phone_number',NULL,NULL,NULL,'','Work Phone','2021-02-17 05:35:48','2021-02-17 05:35:48'),(18,'user','email','required','required',NULL,'','Email','2021-02-17 05:35:48','2021-02-17 05:35:48'),(19,'user','mobile',NULL,NULL,NULL,'','Mobile Phone','2021-02-17 05:35:48','2021-02-17 05:35:48'),(20,'user','address',NULL,NULL,NULL,'','Address','2021-02-17 05:35:48','2021-02-17 05:35:48'),(21,'user','organisation',NULL,NULL,NULL,'','Organisation','2021-02-17 05:35:48','2021-02-17 05:35:48'),(22,'user','department',NULL,NULL,NULL,'','Department Name','2021-02-17 05:35:48','2021-02-17 05:35:48'),(23,'user','',NULL,NULL,NULL,'','','2021-02-17 05:35:48','2021-02-17 05:35:48'),(24,'organisation','name','required','required',NULL,'','Organization Name','2021-02-17 05:35:48','2021-02-17 05:35:48'),(25,'organisation','phone',NULL,NULL,NULL,'','Phone','2021-02-17 05:35:48','2021-02-17 05:35:48'),(26,'organisation','domain',NULL,NULL,NULL,'','Organization Domain Name','2021-02-17 05:35:48','2021-02-17 05:35:48'),(27,'organisation','internal_notes',NULL,NULL,NULL,'','Description','2021-02-17 05:35:48','2021-02-17 05:35:48'),(28,'organisation','address',NULL,NULL,NULL,'','Address','2021-02-17 05:35:48','2021-02-17 05:35:48'),(29,'organisation','department',NULL,NULL,NULL,'','Department','2021-02-17 05:35:48','2021-02-17 05:35:48');
/*!40000 ALTER TABLE `requireds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_agent_permission`
--

DROP TABLE IF EXISTS `sd_agent_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_agent_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `permission` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_agent_permission`
--

LOCK TABLES `sd_agent_permission` WRITE;
/*!40000 ALTER TABLE `sd_agent_permission` DISABLE KEYS */;
INSERT INTO `sd_agent_permission` VALUES (10,19,'{\"create_problem\":\"1\",\"edit_problem\":\"1\",\"view_problems\":\"1\",\"delete_problem\":\"1\",\"attach_problem\":\"1\",\"detach_problem\":\"1\",\"create_change\":\"1\",\"edit_change\":\"1\",\"view_changes\":\"1\",\"delete_change\":\"1\",\"attach_change\":\"1\",\"detach_change\":\"1\",\"create_release\":\"1\",\"edit_release\":\"1\",\"view_releases\":\"1\",\"delete_release\":\"1\",\"attach_release\":\"1\",\"detach_release\":\"1\",\"create_asset\":\"1\",\"edit_asset\":\"1\",\"view_assets\":\"1\",\"delete_asset\":\"1\",\"attach_asset\":\"1\",\"detach_asset\":\"1\",\"create_contract\":\"1\",\"edit_contract\":\"1\",\"view_contracts\":\"1\",\"delete_contract\":\"1\"}','2021-03-10 06:42:43','2021-03-10 06:42:43'),(5,8,'{\"create_problem\":\"1\",\"edit_problem\":\"1\",\"view_problems\":\"1\",\"delete_problem\":\"1\",\"attach_problem\":\"1\",\"detach_problem\":\"1\",\"create_change\":\"1\",\"edit_change\":\"1\",\"view_changes\":\"1\",\"delete_change\":\"1\",\"attach_change\":\"1\",\"detach_change\":\"1\",\"create_release\":\"1\",\"edit_release\":\"1\",\"view_releases\":\"1\",\"delete_release\":\"1\",\"attach_release\":\"1\",\"detach_release\":\"1\",\"create_asset\":\"1\",\"edit_asset\":\"1\",\"view_assets\":\"1\",\"delete_asset\":\"1\",\"attach_asset\":\"1\",\"detach_asset\":\"1\",\"create_contract\":\"1\",\"edit_contract\":\"1\",\"view_contracts\":\"1\",\"delete_contract\":\"1\"}','2021-02-24 08:12:22','2021-02-24 08:12:22'),(11,10,'{\"create_problem\":\"1\",\"edit_problem\":\"1\",\"view_problems\":\"1\",\"delete_problem\":\"1\",\"attach_problem\":\"1\",\"detach_problem\":\"1\",\"create_change\":\"1\",\"edit_change\":\"1\",\"view_changes\":\"1\",\"delete_change\":\"1\",\"attach_change\":\"1\",\"detach_change\":\"1\",\"create_release\":\"1\",\"edit_release\":\"1\",\"view_releases\":\"1\",\"delete_release\":\"1\",\"attach_release\":\"1\",\"detach_release\":\"1\",\"create_asset\":\"1\",\"edit_asset\":\"1\",\"view_assets\":\"1\",\"delete_asset\":\"1\",\"attach_asset\":\"1\",\"detach_asset\":\"1\",\"create_contract\":\"1\",\"edit_contract\":\"1\",\"view_contracts\":\"1\",\"delete_contract\":\"1\"}','2021-03-10 06:47:34','2021-03-10 06:47:34'),(8,16,'{\"create_problem\":\"1\",\"edit_problem\":\"1\",\"view_problems\":\"1\",\"delete_problem\":\"1\",\"attach_problem\":\"1\",\"detach_problem\":\"1\",\"create_change\":\"1\",\"edit_change\":\"1\",\"view_changes\":\"1\",\"delete_change\":\"1\",\"attach_change\":\"1\",\"detach_change\":\"1\",\"create_release\":\"1\",\"edit_release\":\"1\",\"view_releases\":\"1\",\"delete_release\":\"1\",\"attach_release\":\"1\",\"detach_release\":\"1\",\"create_asset\":\"1\",\"edit_asset\":\"1\",\"view_assets\":\"1\",\"delete_asset\":\"1\",\"attach_asset\":\"1\",\"detach_asset\":\"1\",\"create_contract\":\"1\",\"edit_contract\":\"1\",\"view_contracts\":\"1\",\"delete_contract\":\"1\"}','2021-03-04 12:14:55','2021-03-04 12:14:55'),(9,17,'{\"create_problem\":\"1\",\"edit_problem\":\"1\",\"view_problems\":\"1\",\"delete_problem\":\"1\",\"attach_problem\":\"1\",\"detach_problem\":\"1\",\"create_change\":\"1\",\"edit_change\":\"1\",\"view_changes\":\"1\",\"delete_change\":\"1\",\"attach_change\":\"1\",\"detach_change\":\"1\",\"create_release\":\"1\",\"edit_release\":\"1\",\"view_releases\":\"1\",\"delete_release\":\"1\",\"attach_release\":\"1\",\"detach_release\":\"1\",\"create_asset\":\"1\",\"edit_asset\":\"1\",\"view_assets\":\"1\",\"delete_asset\":\"1\",\"attach_asset\":\"1\",\"detach_asset\":\"1\",\"create_contract\":\"1\",\"edit_contract\":\"1\",\"view_contracts\":\"1\",\"delete_contract\":\"1\"}','2021-03-04 12:19:30','2021-03-04 12:19:30');
/*!40000 ALTER TABLE `sd_agent_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_asset_statuses`
--

DROP TABLE IF EXISTS `sd_asset_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_asset_statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_asset_statuses`
--

LOCK TABLES `sd_asset_statuses` WRITE;
/*!40000 ALTER TABLE `sd_asset_statuses` DISABLE KEYS */;
INSERT INTO `sd_asset_statuses` VALUES (1,'In Use','Assets in use','2021-02-17 05:39:08','2021-02-17 05:39:08'),(2,'Missing','Assets missing','2021-02-17 05:39:08','2021-02-17 05:39:08'),(3,'In Stock','Assets in stock','2021-02-17 05:39:08','2021-02-17 05:39:08'),(4,'Dispose','Assets which could be disposed','2021-02-17 05:39:08','2021-02-17 05:39:08'),(5,'Maintenance','Assets under maintenance','2021-02-17 05:39:08','2021-02-17 05:39:08'),(6,'Move','Assets which need to moved from one location/organization/department to another location/organization/department','2021-02-17 05:39:08','2021-02-17 05:39:08');
/*!40000 ALTER TABLE `sd_asset_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_asset_types`
--

DROP TABLE IF EXISTS `sd_asset_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_asset_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_asset_types`
--

LOCK TABLES `sd_asset_types` WRITE;
/*!40000 ALTER TABLE `sd_asset_types` DISABLE KEYS */;
INSERT INTO `sd_asset_types` VALUES (1,'Services',0,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(2,'Cloud',0,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(3,'Hardware',0,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(4,'Software',0,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(5,'Consumable',0,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(6,'Network',0,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(7,'Document',0,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(8,'Others',0,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(9,'Business Service',1,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(10,'IT Service',1,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(11,'Sales Service',9,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(12,'Support Service',9,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(13,'Email service',10,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(14,'Backup service',10,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(15,'Hosting service',10,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(16,'AWS',2,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(17,'EC2',16,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(18,'RDS',16,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(19,'EBS',16,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(20,'Computer',3,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(21,'Storage',3,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(22,'Data Center',3,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(23,'Mobile Devices',3,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(24,'Monitor',3,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(25,'Printer',3,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(26,'Projector',3,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(27,'Scanner',3,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(28,'Router',3,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(29,'Switch',3,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(30,'Access Point',3,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(31,'Firewall',3,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(32,'Other Devices',3,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(33,'Desktop',20,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(34,'Laptop',20,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(35,'Server',20,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(36,'Unix Server',35,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(37,'Solaris Server',35,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(38,'Aix Server',35,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(39,'VMwareServer',35,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(40,'Windows Server',35,'2021-02-17 05:02:39','2021-02-17 05:02:39'),(41,'Disk',21,'2021-02-17 05:02:39','2021-02-17 05:02:39');
/*!40000 ALTER TABLE `sd_asset_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_attachment_types`
--

DROP TABLE IF EXISTS `sd_attachment_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_attachment_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_attachment_types`
--

LOCK TABLES `sd_attachment_types` WRITE;
/*!40000 ALTER TABLE `sd_attachment_types` DISABLE KEYS */;
INSERT INTO `sd_attachment_types` VALUES (1,'Database','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'File','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `sd_attachment_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_barcode_templates`
--

DROP TABLE IF EXISTS `sd_barcode_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_barcode_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `width` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `height` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `labels_per_row` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `space_between_labels` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logo_image` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `display_logo_confirmed` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_barcode_templates`
--

LOCK TABLES `sd_barcode_templates` WRITE;
/*!40000 ALTER TABLE `sd_barcode_templates` DISABLE KEYS */;
INSERT INTO `sd_barcode_templates` VALUES (1,'2','1','4','4',NULL,0,'2021-02-17 05:39:08','2021-02-17 05:39:08');
/*!40000 ALTER TABLE `sd_barcode_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_change_priorities`
--

DROP TABLE IF EXISTS `sd_change_priorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_change_priorities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_change_priorities`
--

LOCK TABLES `sd_change_priorities` WRITE;
/*!40000 ALTER TABLE `sd_change_priorities` DISABLE KEYS */;
INSERT INTO `sd_change_priorities` VALUES (1,'Low','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'Medium','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'High','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'Urgent','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `sd_change_priorities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_change_status`
--

DROP TABLE IF EXISTS `sd_change_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_change_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_change_status`
--

LOCK TABLES `sd_change_status` WRITE;
/*!40000 ALTER TABLE `sd_change_status` DISABLE KEYS */;
INSERT INTO `sd_change_status` VALUES (1,'Open','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'Planning','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'Awaiting Approval','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'Pending Release','0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,'Pending Review','0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,'Closed','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `sd_change_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_change_types`
--

DROP TABLE IF EXISTS `sd_change_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_change_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_change_types`
--

LOCK TABLES `sd_change_types` WRITE;
/*!40000 ALTER TABLE `sd_change_types` DISABLE KEYS */;
INSERT INTO `sd_change_types` VALUES (1,'Minor','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'Standard','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'Major','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'Emergency','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `sd_change_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_contract_statuses`
--

DROP TABLE IF EXISTS `sd_contract_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_contract_statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_contract_statuses`
--

LOCK TABLES `sd_contract_statuses` WRITE;
/*!40000 ALTER TABLE `sd_contract_statuses` DISABLE KEYS */;
INSERT INTO `sd_contract_statuses` VALUES (1,'Draft','2021-02-17 05:39:08','2021-02-17 05:39:08','status'),(2,'Approved','2021-02-17 05:39:08','2021-02-17 05:39:08','status'),(3,'Active','2021-02-17 05:39:08','2021-02-17 05:39:08','status'),(4,'Terminated','2021-02-17 05:39:08','2021-02-17 05:39:08','status'),(5,'Expired','2021-02-17 05:39:08','2021-02-17 05:39:08','status'),(6,'Rejected','2021-02-17 05:39:08','2021-02-17 05:39:08','status'),(7,'Renewal Approval Request','2021-02-17 05:39:08','2021-02-17 05:39:08','renewal_status'),(8,'Renewed','2021-02-17 05:39:08','2021-02-17 05:39:08','renewal_status'),(9,'Renewal Rejected','2021-02-17 05:39:08','2021-02-17 05:39:08','renewal_status'),(10,'Extension Approval Request','2021-02-17 05:39:08','2021-02-17 05:39:08','renewal_status'),(11,'Extension Rejected','2021-02-17 05:39:08','2021-02-17 05:39:08','renewal_status'),(12,'Extended','2021-02-17 05:39:08','2021-02-17 05:39:08','renewal_status');
/*!40000 ALTER TABLE `sd_contract_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_contract_types`
--

DROP TABLE IF EXISTS `sd_contract_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_contract_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_contract_types`
--

LOCK TABLES `sd_contract_types` WRITE;
/*!40000 ALTER TABLE `sd_contract_types` DISABLE KEYS */;
INSERT INTO `sd_contract_types` VALUES (1,'Lease','2021-02-17 05:02:39','2021-02-17 05:02:39'),(2,'Software License','2021-02-17 05:02:39','2021-02-17 05:02:39'),(3,'Maintenance','2021-02-17 05:02:39','2021-02-17 05:02:39');
/*!40000 ALTER TABLE `sd_contract_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_default`
--

DROP TABLE IF EXISTS `sd_default`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_default` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_type_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sd_default_asset_type_id_foreign` (`asset_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_default`
--

LOCK TABLES `sd_default` WRITE;
/*!40000 ALTER TABLE `sd_default` DISABLE KEYS */;
INSERT INTO `sd_default` VALUES (1,1,'2021-02-17 05:39:08','2021-02-17 05:39:08');
/*!40000 ALTER TABLE `sd_default` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_impact_types`
--

DROP TABLE IF EXISTS `sd_impact_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_impact_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_impact_types`
--

LOCK TABLES `sd_impact_types` WRITE;
/*!40000 ALTER TABLE `sd_impact_types` DISABLE KEYS */;
INSERT INTO `sd_impact_types` VALUES (1,'Low','2021-02-17 05:39:08','2021-02-17 05:39:08'),(2,'Medium','2021-02-17 05:39:08','2021-02-17 05:39:08'),(3,'High','2021-02-17 05:39:08','2021-02-17 05:39:08');
/*!40000 ALTER TABLE `sd_impact_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_license_types`
--

DROP TABLE IF EXISTS `sd_license_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_license_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_license_types`
--

LOCK TABLES `sd_license_types` WRITE;
/*!40000 ALTER TABLE `sd_license_types` DISABLE KEYS */;
INSERT INTO `sd_license_types` VALUES (1,'Open source','2021-02-17 05:02:39','2021-02-17 05:02:39'),(2,'Commercial','2021-02-17 05:02:39','2021-02-17 05:02:39');
/*!40000 ALTER TABLE `sd_license_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_product_proc_mode`
--

DROP TABLE IF EXISTS `sd_product_proc_mode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_product_proc_mode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_product_proc_mode`
--

LOCK TABLES `sd_product_proc_mode` WRITE;
/*!40000 ALTER TABLE `sd_product_proc_mode` DISABLE KEYS */;
INSERT INTO `sd_product_proc_mode` VALUES (1,'Buy','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'Lease','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `sd_product_proc_mode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_product_status`
--

DROP TABLE IF EXISTS `sd_product_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_product_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_product_status`
--

LOCK TABLES `sd_product_status` WRITE;
/*!40000 ALTER TABLE `sd_product_status` DISABLE KEYS */;
INSERT INTO `sd_product_status` VALUES (1,'In Pipeline','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'In Production','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'Retired','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `sd_product_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_release_priorities`
--

DROP TABLE IF EXISTS `sd_release_priorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_release_priorities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_release_priorities`
--

LOCK TABLES `sd_release_priorities` WRITE;
/*!40000 ALTER TABLE `sd_release_priorities` DISABLE KEYS */;
INSERT INTO `sd_release_priorities` VALUES (1,'Low','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'Medium','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'High','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'Urgent','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `sd_release_priorities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_release_status`
--

DROP TABLE IF EXISTS `sd_release_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_release_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_release_status`
--

LOCK TABLES `sd_release_status` WRITE;
/*!40000 ALTER TABLE `sd_release_status` DISABLE KEYS */;
INSERT INTO `sd_release_status` VALUES (1,'Open','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'On Hold','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'In Progress','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'Incomplete','0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,'Completed','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `sd_release_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sd_release_types`
--

DROP TABLE IF EXISTS `sd_release_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sd_release_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sd_release_types`
--

LOCK TABLES `sd_release_types` WRITE;
/*!40000 ALTER TABLE `sd_release_types` DISABLE KEYS */;
INSERT INTO `sd_release_types` VALUES (1,'Minor','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'Standard','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'Major','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'Emergency','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `sd_release_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_alert_notice`
--

DROP TABLE IF EXISTS `settings_alert_notice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings_alert_notice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_alert_notice`
--

LOCK TABLES `settings_alert_notice` WRITE;
/*!40000 ALTER TABLE `settings_alert_notice` DISABLE KEYS */;
INSERT INTO `settings_alert_notice` VALUES (1,'new_ticket_alert','1','2021-02-25 13:29:04','2021-02-25 13:29:04'),(2,'new_ticket_alert_mode','email,system','2021-02-25 13:29:04','2021-02-25 13:29:04'),(3,'new_ticket_alert_persons','admin,department_members,department_manager','2021-02-25 13:29:04','2021-02-25 13:29:04'),(4,'ticket_assign_alert','1','2021-02-25 13:29:04','2021-02-25 13:29:04'),(5,'ticket_assign_alert_mode','email,system','2021-02-25 13:29:04','2021-02-25 13:29:04'),(6,'ticket_assign_alert_persons','assigned_agent_team','2021-02-25 13:29:04','2021-02-25 13:29:04'),(7,'notification_alert','0','2021-02-25 13:29:04','2021-02-25 13:29:04'),(8,'notification_alert_mode','email,system','2021-02-25 13:29:04','2021-02-25 13:29:04'),(9,'notification_alert_persons','admin,agent,department_manager,team_lead','2021-02-25 13:29:04','2021-02-25 13:29:04'),(10,'new_ticket_confirmation_alert','1','2021-02-25 13:29:04','2021-02-25 13:29:04'),(11,'new_ticket_confirmation_alert_mode','email','2021-02-25 13:29:04','2021-02-25 13:29:04'),(12,'new_ticket_confirmation_alert_persons','client','2021-02-25 13:29:04','2021-02-25 13:29:04'),(13,'internal_activity_alert','1','2021-02-25 13:29:04','2021-02-25 13:29:04'),(14,'internal_activity_alert_mode','email,system','2021-02-25 13:29:04','2021-02-25 13:29:04'),(15,'internal_activity_alert_persons','assigned_agent_team','2021-02-25 13:29:04','2021-02-25 13:29:04'),(16,'rating_feedback_alert_mode','email','2021-02-25 13:29:04','2021-02-25 13:29:04'),(17,'rating_feedback_alert_persons','client','2021-02-25 13:29:04','2021-02-25 13:29:04'),(18,'ticket_transfer_alert','1','2021-02-25 13:29:04','2021-02-25 13:29:04'),(19,'ticket_transfer_alert_mode','email,system','2021-02-25 13:29:04','2021-02-25 13:29:04'),(20,'ticket_transfer_alert_persons','assigned_agent_team,department_members','2021-02-25 13:29:04','2021-02-25 13:29:04'),(21,'registration_alert','1','2021-02-25 13:29:04','2021-02-25 13:29:04'),(22,'registration_alert_mode','email,system','2021-02-25 13:29:04','2021-02-25 13:29:04'),(23,'registration_alert_persons','new_user','2021-02-25 13:29:04','2021-02-25 13:29:04'),(24,'new_user_alert','1','2021-02-25 13:29:04','2021-02-25 13:29:04'),(25,'new_user_alert_mode','system','2021-02-25 13:29:04','2021-02-25 13:29:04'),(26,'new_user_alert_persons','admin','2021-02-25 13:29:04','2021-02-25 13:29:04'),(27,'email_verify_alert','1','2021-02-25 13:29:04','2021-02-25 13:29:04'),(28,'email_verify_alert_mode','email','2021-02-25 13:29:04','2021-02-25 13:29:04'),(29,'email_verify_alert_persons','new_user','2021-02-25 13:29:04','2021-02-25 13:29:04'),(30,'reply_alert','1','2021-02-25 13:29:04','2021-02-25 13:29:04'),(31,'reply_alert_mode','email,system','2021-02-25 13:29:04','2021-02-25 13:29:04'),(32,'reply_alert_persons','client','2021-02-25 13:29:04','2021-02-25 13:29:04'),(33,'reply_notification_alert','1','2021-02-25 13:29:04','2021-02-25 13:29:04'),(34,'reply_notification_alert_mode','email,system','2021-02-25 13:29:04','2021-02-25 13:29:04'),(35,'reply_notification_alert_persons','assigned_agent_team','2021-02-25 13:29:04','2021-02-25 13:29:04'),(36,'browser_notification_status','0','2021-02-25 13:29:04','2021-02-25 13:29:04'),(37,'api_id','','2021-02-25 13:29:04','2021-02-25 13:29:04'),(38,'rest_api_key','','2021-02-25 13:29:04','2021-02-25 13:29:04');
/*!40000 ALTER TABLE `settings_alert_notice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_company`
--

DROP TABLE IF EXISTS `settings_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings_company` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `landing_page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `offline_page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `thank_page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `use_logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_company`
--

LOCK TABLES `settings_company` WRITE;
/*!40000 ALTER TABLE `settings_company` DISABLE KEYS */;
INSERT INTO `settings_company` VALUES (1,'PIA','','','','','','','6912.dCase2.PNG','0','2021-02-17 05:35:47','2021-02-17 11:48:15');
/*!40000 ALTER TABLE `settings_company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_email`
--

DROP TABLE IF EXISTS `settings_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings_email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sys_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alert_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `admin_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mta` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_fetching` tinyint(1) NOT NULL,
  `notification_cron` tinyint(1) NOT NULL,
  `strip` tinyint(1) NOT NULL,
  `separator` tinyint(1) NOT NULL,
  `all_emails` tinyint(1) NOT NULL,
  `email_collaborator` tinyint(1) NOT NULL,
  `attachment` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_email`
--

LOCK TABLES `settings_email` WRITE;
/*!40000 ALTER TABLE `settings_email` DISABLE KEYS */;
INSERT INTO `settings_email` VALUES (1,'default','1','','','',1,0,0,0,1,1,1,'2021-02-17 05:35:47','2021-02-17 05:52:43');
/*!40000 ALTER TABLE `settings_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_security`
--

DROP TABLE IF EXISTS `settings_security`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings_security` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lockout_message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `backlist_offender` int(11) NOT NULL,
  `backlist_threshold` int(11) NOT NULL,
  `lockout_period` int(11) NOT NULL,
  `days_to_keep_logs` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_security`
--

LOCK TABLES `settings_security` WRITE;
/*!40000 ALTER TABLE `settings_security` DISABLE KEYS */;
INSERT INTO `settings_security` VALUES (1,'You have been locked out of application due to too many failed login attempts.',0,5,15,0,'2021-02-17 05:35:47','2021-02-17 05:35:49');
/*!40000 ALTER TABLE `settings_security` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_system`
--

DROP TABLE IF EXISTS `settings_system`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings_system` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `page_size` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `log_level` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `purge_log` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `api_enable` int(11) NOT NULL,
  `api_key_mandatory` int(11) NOT NULL,
  `api_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_format` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `time_farmat` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'g:i a',
  `date_format` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'F j, Y',
  `time_zone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_time_format` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `day_date_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `serial_key` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_number` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_via_ip` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `time_farmat` (`time_farmat`),
  KEY `date_format` (`date_format`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_system`
--

LOCK TABLES `settings_system` WRITE;
/*!40000 ALTER TABLE `settings_system` DISABLE KEYS */;
INSERT INTO `settings_system` VALUES (1,1,'http://dcase-fav-dev.dnext-vfal.com','Faveo','1','','','',1,0,'','','H:i','d-m-Y','Europe/Istanbul','F j, Y, g:i a','','en-gb','v4.0.1','2021-02-17 05:35:48','2021-03-11 06:58:10',NULL,NULL,0);
/*!40000 ALTER TABLE `settings_system` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_ticket`
--

DROP TABLE IF EXISTS `settings_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings_ticket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `num_format` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `num_sequence` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `help_topic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `max_open_ticket` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `collision_avoid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lock_ticket_frequency` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `captcha` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `claim_response` tinyint(1) NOT NULL,
  `assigned_ticket` tinyint(1) NOT NULL,
  `answered_ticket` tinyint(1) NOT NULL,
  `agent_mask` tinyint(1) NOT NULL,
  `html` tinyint(1) NOT NULL,
  `client_update` tinyint(1) NOT NULL,
  `max_file_size` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `count_internal` tinyint(1) NOT NULL DEFAULT 0,
  `show_status_date` tinyint(1) NOT NULL DEFAULT 0,
  `show_org_details` tinyint(1) NOT NULL DEFAULT 0,
  `custom_field_name` text COLLATE utf8_unicode_ci NOT NULL,
  `waiting_time` int(11) NOT NULL,
  `ticket_number_prefix` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_ticket`
--

LOCK TABLES `settings_ticket` WRITE;
/*!40000 ALTER TABLE `settings_ticket` DISABLE KEYS */;
INSERT INTO `settings_ticket` VALUES (1,'$$$$-####-####','sequence','4','','2','0','',1,0,0,0,0,0,0,0,'2021-02-17 05:35:47','2021-06-23 10:26:02',0,0,0,'AAAA,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,64,66,67,68,71,72,79,80,138,82,85,86,89,90,91,92,93,94,95,96,97,98,99,116,141,117,140,63,69,70,73,74,75,76,77,78,81,83,84,87,88,100,101,104,134,106,109,125,122,128,131,139,126,127,129,130,132,133',17520,'AAAA');
/*!40000 ALTER TABLE `settings_ticket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sla_approach_escalate`
--

DROP TABLE IF EXISTS `sla_approach_escalate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sla_approach_escalate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sla_plan` int(11) NOT NULL,
  `escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `escalate_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sla_approach_escalate`
--

LOCK TABLES `sla_approach_escalate` WRITE;
/*!40000 ALTER TABLE `sla_approach_escalate` DISABLE KEYS */;
INSERT INTO `sla_approach_escalate` VALUES (1,1,'diff::5~minute','response','assignee','2021-02-18 14:03:40','2021-02-18 14:03:40'),(2,1,'diff::1~hour','resolution','team_lead','2021-02-18 14:03:40','2021-02-18 14:03:40'),(3,2,'diff::5~minute','response','assignee','2021-02-21 14:49:21','2021-02-21 14:49:21'),(4,2,'diff::10~minute','resolution','team_lead,assignee','2021-02-21 14:49:21','2021-02-21 14:49:21'),(5,3,'diff::5~minute','response','assignee','2021-02-21 15:27:18','2021-02-21 15:27:18'),(6,3,'diff::5~minute','resolution','team_lead,assignee','2021-02-21 15:27:18','2021-02-21 15:27:18'),(7,4,'diff::5~minute','response','assignee','2021-02-21 15:50:32','2021-02-21 15:50:32'),(8,4,'diff::5~minute','resolution','assignee','2021-02-21 15:50:32','2021-02-21 15:50:32'),(9,5,'diff::5~minute','response','assignee','2021-02-21 15:54:35','2021-02-21 15:54:35'),(10,5,'diff::5~minute','resolution','team_lead,assignee','2021-02-21 15:54:35','2021-02-21 15:54:35');
/*!40000 ALTER TABLE `sla_approach_escalate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sla_plan`
--

DROP TABLE IF EXISTS `sla_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sla_plan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `grace_period` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `admin_note` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `sla_target` int(11) NOT NULL,
  `apply_sla_depertment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `apply_sla_company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `apply_sla_tickettype` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `apply_sla_ticketsource` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `transient` tinyint(1) NOT NULL,
  `ticket_overdue` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `apply_sla_helptopic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `apply_sla_orgdepts` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `apply_sla_labels` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `apply_sla_tags` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sla_plan`
--

LOCK TABLES `sla_plan` WRITE;
/*!40000 ALTER TABLE `sla_plan` DISABLE KEYS */;
INSERT INTO `sla_plan` VALUES (1,'Low','','',1,1,'','','','',0,0,'2021-02-17 05:35:47','2021-02-17 05:35:47','','','','',0,1),(2,'Normal','','',1,2,'','','','',0,0,'2021-02-17 05:35:47','2021-02-17 05:35:47','','','','',0,0),(3,'High','','',1,3,'','','','',0,0,'2021-02-17 05:35:47','2021-02-17 05:35:47','','','','',0,0),(4,'Emergency','','',1,4,'','','','',0,0,'2021-02-17 05:35:47','2021-02-17 05:35:47','','','','',0,0);
/*!40000 ALTER TABLE `sla_plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sla_targets`
--

DROP TABLE IF EXISTS `sla_targets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sla_targets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sla_id` int(11) NOT NULL,
  `priority_id` int(11) NOT NULL,
  `respond_within` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resolve_within` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `business_hour_id` int(11) NOT NULL,
  `send_email` int(11) NOT NULL,
  `send_sms` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `in_app` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sla_targets`
--

LOCK TABLES `sla_targets` WRITE;
/*!40000 ALTER TABLE `sla_targets` DISABLE KEYS */;
INSERT INTO `sla_targets` VALUES (1,'Low',1,1,'5-hrs','10-hrs',1,1,0,'2021-02-17 05:35:47','2021-02-17 05:35:47',0),(2,'Normal',2,2,'4-hrs','9-hrs',1,1,0,'2021-02-17 05:35:47','2021-02-17 05:35:47',0),(3,'High',3,3,'2-hrs','4-hrs',1,1,0,'2021-02-17 05:35:47','2021-02-17 05:35:47',0),(4,'Emergency',4,4,'1-hrs','2-hrs',1,1,0,'2021-02-17 05:35:47','2021-02-17 05:35:47',0);
/*!40000 ALTER TABLE `sla_targets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sla_violated_escalate`
--

DROP TABLE IF EXISTS `sla_violated_escalate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sla_violated_escalate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sla_plan` int(11) NOT NULL,
  `escalate_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `escalate_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `escalate_person` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sla_violated_escalate`
--

LOCK TABLES `sla_violated_escalate` WRITE;
/*!40000 ALTER TABLE `sla_violated_escalate` DISABLE KEYS */;
INSERT INTO `sla_violated_escalate` VALUES (1,1,'diff::5~minute','response','team_lead','2021-02-18 14:03:40','2021-02-18 14:03:40'),(2,1,'diff::5~minute','resolution','department_manager,team_lead','2021-02-18 14:03:40','2021-02-18 14:03:40'),(3,2,'diff::5~minute','response','department_manager','2021-02-21 14:49:21','2021-02-21 14:49:21'),(4,2,'diff::5~minute','resolution','department_manager,team_lead','2021-02-21 14:49:21','2021-02-22 11:06:31'),(5,3,'diff::5~minute','response','department_manager,team_lead','2021-02-21 15:27:18','2021-02-21 15:27:18'),(6,3,'diff::5~minute','resolution','department_manager,team_lead','2021-02-21 15:27:18','2021-02-21 15:27:18'),(7,4,'diff::5~minute','response','department_manager,team_lead','2021-02-21 15:50:32','2021-02-21 15:50:32'),(8,4,'diff::5~minute','resolution','department_manager,team_lead','2021-02-21 15:50:32','2021-02-21 15:50:32'),(9,5,'diff::5~minute','response','department_manager,team_lead','2021-02-21 15:54:35','2021-02-21 15:54:35'),(10,5,'diff::5~minute','resolution','department_manager,team_lead','2021-02-21 15:54:35','2021-02-21 15:54:35');
/*!40000 ALTER TABLE `sla_violated_escalate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_reports`
--

DROP TABLE IF EXISTS `sub_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sub_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `report_id` bigint(20) NOT NULL,
  `identifier` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `data_type` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_widget_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `selected_chart_type` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `list_view_by` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `selected_view_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `add_custom_column_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `layout` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n*1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_reports`
--

LOCK TABLES `sub_reports` WRITE;
/*!40000 ALTER TABLE `sub_reports` DISABLE KEYS */;
INSERT INTO `sub_reports` VALUES (1,1,'helpdesk-in-depth-graph','category-chart','api/agent/helpdesk-in-depth-widget/1','api/agent/helpdesk-in-depth/1','bar','[\"priority\",\"source\",\"type\",\"status\"]','priority',NULL,'n*2','2021-02-17 05:35:49','2021-02-17 05:35:49'),(2,2,'overall-ticket-trend-graph','time-series-chart','api/agent/ticket-volume-trend/overall-ticket-trend-widget/2','api/agent/ticket-volume-trend/overall-ticket-trend/2',NULL,'[\"day\",\"week\",\"month\",\"year\"]','day',NULL,'n*1','2021-02-17 05:35:49','2021-02-17 05:35:49'),(3,2,'weekday-hour-trend-graph','time-series-chart','api/agent/ticket-volume-trend/day-ticket-trend-widget/2','api/agent/ticket-volume-trend/day-ticket-trend/2',NULL,'[\"monday\",\"tuesday\",\"wednesday\",\"thursday\",\"friday\",\"saturday\",\"sunday\"]','monday',NULL,'n*1','2021-02-17 05:35:49','2021-02-17 05:35:49'),(4,3,'management-report-table','datatable',NULL,'api/agent/management-report/3',NULL,NULL,NULL,'api/add-custom-column/4','n*1','2021-02-17 05:35:49','2021-02-26 09:18:09'),(5,4,'agent-performance-table','datatable',NULL,'api/agent/agent-performance-report/4',NULL,NULL,NULL,'api/add-custom-column/5','n*1','2021-02-17 05:35:49','2021-02-17 05:35:50'),(6,5,'department-performance-table','datatable',NULL,'api/agent/department-performance-report/5',NULL,NULL,NULL,'api/add-custom-column/6','n*1','2021-02-17 05:35:49','2021-02-17 05:35:50'),(7,6,'team-performance-table','datatable',NULL,'api/agent/team-performance-report/6',NULL,NULL,NULL,'api/add-custom-column/6','n*1','2021-02-17 05:35:49','2021-02-17 05:35:50'),(8,7,'time-report-chart','category-chart',NULL,'api/agent/performance-distribution/time-report/7','bar',NULL,NULL,NULL,'n*1','2021-02-17 05:35:49','2021-02-17 05:35:49'),(9,7,'trend-report-chart','time-series-chart',NULL,'api/agent/performance-distribution/trend-report/7',NULL,'[\"day\",\"week\",\"month\",\"year\"]','day',NULL,'n*1','2021-02-17 05:35:49','2021-02-17 05:35:49'),(10,8,'top-customer-analysis-chart','category-chart',NULL,'api/agent/top-customer-analysis/8','bar','[\"3\",\"5\",\"10\"]','3',NULL,'n*2','2021-02-17 05:35:49','2021-02-17 05:35:49');
/*!40000 ALTER TABLE `sub_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_portal`
--

DROP TABLE IF EXISTS `system_portal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_portal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_header_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `agent_header_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_header_color` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_button_color` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_button_border_color` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_input_field_color` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_portal`
--

LOCK TABLES `system_portal` WRITE;
/*!40000 ALTER TABLE `system_portal` DISABLE KEYS */;
INSERT INTO `system_portal` VALUES (1,'skin-yellow','skin-blue','#009aba','#009aba','#00c0ef','#d2d6de','4849.dCase2.PNG','6008.dCase2.PNG','2021-02-17 05:35:47','2021-02-17 11:48:15');
/*!40000 ALTER TABLE `system_portal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teams` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `team_lead` int(10) unsigned DEFAULT NULL,
  `assign_alert` tinyint(1) NOT NULL,
  `admin_notes` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `team_lead` (`team_lead`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
INSERT INTO `teams` VALUES (1,'Level 1 Support',1,NULL,0,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(2,'Level 2 Support',0,NULL,0,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(3,'Developer',0,NULL,0,'','2021-02-17 05:35:47','2021-02-17 05:35:47'),(4,'Dummy Team1',1,1,0,'','2021-02-20 12:20:12','2021-02-20 12:20:12'),(5,'VF Tiger Team',1,NULL,0,'<div style=\"display: none;\">&nbsp;</div>\r\n\r\n<p>&nbsp;</p>\r\n','2021-02-23 14:13:25','2021-04-19 13:16:27'),(6,'Billing L2 Team',1,1,0,'','2021-02-23 17:56:07','2021-02-23 17:56:07'),(7,'Testing Team',1,NULL,0,'','2021-02-24 13:22:31','2021-04-15 17:20:38');
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_assign_agent`
--

DROP TABLE IF EXISTS `team_assign_agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_assign_agent` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` int(10) unsigned DEFAULT NULL,
  `agent_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `team_id` (`team_id`),
  KEY `agent_id` (`agent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_assign_agent`
--

LOCK TABLES `team_assign_agent` WRITE;
/*!40000 ALTER TABLE `team_assign_agent` DISABLE KEYS */;
INSERT INTO `team_assign_agent` VALUES (1,0,2,NULL,NULL),(2,0,3,NULL,NULL),(3,0,5,NULL,NULL),(4,4,1,'2021-02-20 12:20:12','2021-02-20 12:20:12'),(14,0,17,NULL,NULL),(8,6,1,'2021-02-23 17:56:07','2021-02-23 17:56:07'),(7,5,8,'2021-02-23 14:13:25','2021-02-23 14:13:25'),(9,5,11,NULL,NULL),(10,5,13,NULL,NULL),(11,5,14,NULL,NULL),(12,7,13,'2021-02-24 13:22:31','2021-02-24 13:22:31'),(13,5,16,NULL,NULL),(16,5,19,NULL,NULL),(17,5,18,NULL,NULL),(18,0,22,NULL,NULL);
/*!40000 ALTER TABLE `team_assign_agent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `variable` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `set_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `template_category` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `templates`
--

LOCK TABLES `templates` WRITE;
/*!40000 ALTER TABLE `templates` DISABLE KEYS */;
INSERT INTO `templates` VALUES (1,'template-register-confirmation-with-account-details','1',7,'Registration Confirmation','Hello {!! $receiver_name !!},<br /><br />This email is confirmation that you are now registered at our helpdesk.<br /><br /><strong>Registered Email:</strong> {!! $new_user_email !!}<br /><strong>Password:</strong> {!! $user_password !!}<br /><br />You can visit the helpdesk to browse articles and contact us at any time: {!! $system_link !!}<br />Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:49','common-templates'),(2,'template-reset-password-link','1',8,'Reset your Password','Hello {!! $receiver_name !!},<br /><br />You asked to reset your password. To do so, please click this link:<br />{!! $password_reset_link !!}<br /><br />This will let you change your password to something new. If you did not ask for this, do not worry, we will keep your password safe.<br />Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:49','common-templates'),(3,'template-new-password','1',15,'Password changed','Hello {!! $receiver_name !!},<br /><br />Your password is successfully changed. Your new password is : {!! $user_password !!}<br /><br />Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:49','common-templates'),(4,'template-register-confirmation-with-activation-link','1',11,'Verify your email address','Hello {!! $receiver_name !!},<br/><br/>This is a verification mail for your email registered with us.<br/><br/><strong>Registered Email:</strong> {!! $new_user_email !!}<br/><br/>Please click on the below link to verify your email address so you can use the system.<br/>{!! $account_activation_link !!}<br/><br/>Thank You.<br/><br/>Kind Regards,<br/>{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:49','common-templates'),(5,'template-ticket-checking-wihtout-login-link','1',2,'Check your Ticket','Hello {!! $receiver_name !!},<br /><br />Click the link below to view your requested ticket<br />{!! $ticket_link !!}<br /><br />Kind Regards,<br />{!! $system_from !!}</p>','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','client-templates'),(6,'template-ticket-creation-acknowledgement-client-by-client','0',4,'','Hello {!! $receiver_name !!}<br /><br />Thank you for contacting us. This is an automated response confirming the receipt of your ticket. Our team will get back to you as soon as possible. When replying, please make sure that the ticket ID is kept in the subject so that we can track your replies.<br /><br /><strong>Ticket ID:</strong> {!! $ticket_number !!} <br /><br />{!! $department_signature !!}<br />You can check the status of or update this ticket online at: {!! $system_link !!}<br /><br />Kind Regards,<br />{!! $system_from !!}</p>','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','client-templates'),(7,'template-ticket-status-update-client','1',21,'','Hello {!! $receiver_name !!},<br /><br />This email is regarding your ticket with ID: {!! $ticket_number !!}.<br />{!! $message_content !!}<br />If you are not satisfied please respond to the ticket here {!! $ticket_link !!}<br /><br />Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','client-templates'),(8,'template-ticket-creation-acknowledgement-client-by-agent','0',6,'','Hello {!! $receiver_name !!}<br /><br />Thank you for contacting us. This is to confirm that our agent has logged your request in our support system. When replying, please make sure that the ticket ID is kept in the subject so that we can track your replies.<br /><br /><strong>Ticket ID:</strong> {!! $ticket_number !!} <br /><br /><strong>Request message:</strong> {!! $message_content !!} <br /><br />{!! $department_signature !!}<br />You can check the status of or update this ticket online at: {!! $system_link !!}<br /><br />Kind Regards,<br />{!! $system_from !!}</p>','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','client-templates'),(9,'template-ticket-assignment-notice-to-client','0',1,'','Hello {!! $receiver_name !!},<br /><br />Your ticket with ID: {!! $ticket_number !!} has been assigned to one of our agents. They will contact you soon.<br /><br /><br />Kind Regards,<br />{!! $system_from !!}</p>','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','client-templates'),(10,'template-ticket-reply-to-client-by-agent','0',9,'','{!! $message_content !!}<br /><br /><strong>Ticket Details</strong><strong>Ticket ID:</strong> {!! $ticket_number !!}<br /><br />{!! $agent_signature !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','client-templates'),(11,'template-ticket-assigment-notice-to-team-client','1',14,'','<p>Hello {!! $receiver_name !!},<br /><br />Your ticket with ID: {!! $ticket_number !!} has been assigned to our {!! $assigned_team_name !!} team. They will contact you soon.<br /><br /><br />Kind Regards,<br />{!! $system_from !!}</p>','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','client-templates'),(12,'template-ticket-assignment-notice-to-assigned-agent','1',1,'{!! $ticket_subject !!}','<p>Hello {!! $receiver_name !!},<br />\r\n<br />\r\n<strong>Ticket No:</strong> {!! $ticket_number !!}<br />\r\nHas been assigned to you by {!! $activity_by !!}<br />\r\n<br />\r\nThank You<br />\r\nKind Regards,<br />\r\n{!! $system_from !!}</p>\r\n','',1,'2021-02-17 05:35:47','2021-05-21 11:22:33','assigend-agent-templates'),(13,'template-ticket-reply-to-assigned-agents-by-client','0',10,'','Hello {!! $receiver_name !!},<br /><br />Client has made a new reply on their ticket which is assigned to you.<br /><br /><strong>Ticket ID:</strong>  {!! $ticket_number !!}<br /><strong>Reply Message</strong><br />{!! $message_content !!}<br /><br />Please follow the link below to check and reply on ticket.<br />{!! $ticket_link !!}<br /><br />Thanks<br />{!! $system_from !!}<br />','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','assigend-agent-templates'),(14,'template-response-voilate-escalation-to-assigned-agent','1',12,'Response Time SLA Violate','Hello {!! $receiver_name !!},<br /><br />There has been no response for a ticket assigned to you. The first response was due by {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','assigend-agent-templates'),(15,'template-resolve-voilate-escalation-to-assigned-agent','1',13,'Resolve Time SLA Violate','Hello {!! $receiver_name !!},<br /><br />Ticket which is assigened to you has not been resolved within the SLA time period. The ticket was due by {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','assigend-agent-templates'),(16,'template-internal-change-to-assigned-agent','1',16,'','Hello {!! $receiver_name !!},<br /><br />This message is regarding a ticket assigned to you with the ticket ID {!! $ticket_number !!}.<br />{!! $message_content !!}.<br />By {!! $activity_by !!}<br /><br />Thank you<br />Kind regards,<br />{!! $system_from !!}<br />','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','assigend-agent-templates'),(17,'template-response-time-approach-to-assigned-agents','1',17,'Response Time SLA Approaching','Hello {!! $receiver_name !!},<br /><br />There has been no response for a ticket assigned to you. The first response should happen on or before {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!} <br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','assigend-agent-templates'),(18,'template-resolve-time-approach-to-assigned-agents','1',18,'Resolution Time SLA Approaching','Hello {!! $receiver_name !!},<br /><br />The ticket assigned to you has not yet resolved. The ticket has to be resolved on or before {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!} <br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','assigend-agent-templates'),(19,'template-ticket-status-update-assign-agent','1',21,'','Hello {!! $receiver_name !!},<br /><br />This email is regarding ticket {!! $ticket_number !!} which is assigned to you.<br />{!! $message_content !!}<br />Thank You.<br/><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','assigend-agent-templates'),(20,'template-ticket-reply-to-assigned-agents-by-agent','0',9,'','Hello {!! $receiver_name !!},<br /><br />A reply has been made to a ticket assigned to you with ID: {!! $ticket_number !!} by {!! $activity_by !!} in our helpdesk system.<br /><strong>Reply content</strong><br />{!! $message_content !!}<br /><strong>Ticket link</strong><br />{!! $ticket_link !!}<br />{!! $agent_signature !!}<br />Thanks<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','assigend-agent-templates'),(21,'template-new-ticket-creation-notice-agents','0',5,'','Hello {!! $receiver_name !!},<br /><br />New ticket with ID: {!! $ticket_number !!} has been created in our helpdesk.<br /><br /><strong>Client Details</strong><br /><strong>Name:</strong> {!! $client_name !!}<br /><strong>E-mail:</strong> {!! $client_email !!}<br /><br /><strong>Message</strong><br />{!! $message_content !!}<br /><br />Kind Regards,<br />{!! $system_from !!}</p>','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(22,'template-ticket-reply-to-agents-by-client','0',10,'','Hello {!! $receiver_name !!},<br /><br />Client has made a new reply on their ticket in our helpdesk system.<br /><br /><strong>Ticket ID:</strong>  {!! $ticket_number !!}<br /><strong>Reply Message</strong><br />{!! $message_content !!}<br /><br />Please follow the link below to check and reply on ticket.<br />{!! $ticket_link !!}<br /><br />Thanks<br />{!! $system_from !!}<br />','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(23,'template-response-voilate-escalation-to-agent','1',12,'Response Time SLA Violate','Hello {!! $receiver_name !!},<br /><br />There has been no response for a ticket. The first response was due by {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(24,'template-resolve-voilate-escalation-to-agent','1',13,'Resolve Time SLA Violate','Hello {!! $receiver_name !!},<br /><br />Ticket has not been resolved within the SLA time period. The ticket was due by {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!}<br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(25,'template-ticket-assigment-notice-to-team','1',14,'','<p>Hello {!! $receiver_name !!},<br /><br /><strong>Ticket No:</strong> {!! $ticket_number !!}<br /><br />Has been assigned to your team <b>{!! $assigned_team_name !!}</b> by {!! $activity_by !!}<br /><br />Follow the link below to check and reply on the ticket.<br />{!! $ticket_link !!}<br /><br />Thank You<br />Kind Regards,<br />{!! $system_from !!}</p>','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(26,'template-internal-change-to-agent','1',16,'','Hello {!! $receiver_name !!},<br /><br />This message is regarding a ticket with the ticket ID {!! $ticket_number !!}.<br />{!! $message_content !!}.<br />By {!! $activity_by !!}<br /><br />Thank you<br />Kind regards,<br />{!! $system_from !!}<br />','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(27,'template-response-time-approach-to-agents','1',17,'Response Time SLA Approaching','Hello {!! $receiver_name !!},<br /><br />There has been no response for a ticket. The first response should happen on or before {!! $ticket_due_date !!}.<br/><br />Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!} <br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(28,'template-resolve-time-approach-to-agents','1',18,'Resolution Time SLA Approaching','Hello {!! $receiver_name !!},<br /><br />Ticket has not been resolved within the SLA time period. The ticket has to be resolved on or before {!! $ticket_due_date !!}.<br /><br />Ticket Details:<br />Subject: {!! $ticket_subject !!}<br />Number: {!! $ticket_number !!}<br />Requester: {!! $client_name !!}<br /><br />This is an automatic escalation email from {!! $system_from !!}<br /><br />Respond to ticket here {!! $ticket_link !!} <br /><br />Thank You.<br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(29,'template-new-user-entry-notice','1',19,'New user has created','Hello {!! $receiver_name !!},<br/><br/>A new user has been registered in our helpdesk system.<br/><br/><strong>User Details</strong><br/><strong>Name: </strong>{!! $new_user_name !!}<br/><strong>Email</strong><strong>:</strong> {!! $new_user_email !!}<br/><br/>You can check or update the user\'s complete profile by clicking the link below<br/>{!! $user_profile_link !!}<br/><br/>Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(30,'template-non-assign-escalation-notice','1',20,'Non Assign Ticket','','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(31,'template-ticket-status-update-agent','1',21,'','Hello {!! $receiver_name !!},<br /><br />This email is regarding ticket {!! $ticket_number !!}.<br />{!! $message_content !!}<br />Thank You.<br/><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(32,'template-ticket-assignment-notice-to-agent','0',1,'','Hello {!! $receiver_name !!},<br /><br /><strong>Ticket No:</strong> {!! $ticket_number !!}<br />Has been assigned to {!! $agent_name !!} by {!! $activity_by !!}<br /><br /><br />Thank You<br /><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(33,'template-ticket-reply-to-agents-by-agent','0',9,'','Hello {!! $receiver_name !!},<br /><br />An agent has replied to a ticket with ID: {!! $ticket_number !!} in our helpdesk system.<br /><br /><strong>Reply content</strong><br />{!! $message_content !!}<br /><strong>Ticket link</strong><br />{!! $ticket_link !!}<br />{!! $agent_signature !!}<br />Thanks.<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(34,'template-ticket-approve','0',22,'','Hello {!! $agent_name !!},<br /><br />An agent has applied for approval to a ticket with ID: {!! $ticket_number !!} in our helpdesk system.<br /><br /><strong>Ticket link</strong><br />{!! $ticket_link !!}<br /><br /><strong>Approve link</strong><br />{!! $approve_url !!}<br /><strong>Deny link</strong><br />{!! $deny_url !!}<br /><br />Thanks.<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(35,'template-report-export','1',33,'Helpdesk report available for download','<p>Hello {!! $receiver_name !!},<br /><br />{!! $report_type !!} is successfully generated and ready for download.<br /><br /><strong>Download link</strong><br />{!! $report_link !!}<br /><br />***Please note this link will be expired in {!! $report_expiry !!}.<br /><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(36,'template-invoice-to-clients','0',23,'','Hello {!! $client_name !!},<br /><br />An invoice has generated for a ticket with ID: {!! $ticket_number !!}.<br /><br /><strong>Ticket link</strong><br />{!! $ticket_link !!}<br /><br /><strong>Total time spend</strong><br />{!! $total_time !!} Hours<br /><br /><strong>Cost</strong><br />{!! $currency !!}{!! $cost !!}<br /><br /><strong>Billing Date</strong><br />{!! $bill_date !!}<br /><br />Thanks.<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','client-templates'),(37,'task-reminder','1',24,'Task Alert','<p>Hello {!! $receiver_name !!},<br /><br />Your task {!! $task_name !!} is due on {!! $task_end_date !!}. </p>Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:49','common-templates'),(38,'task-created','1',25,'Task created','<p>Hello {!! $receiver_name !!},<br /><br />Your task {!! $task_name !!} has been created,  is due on {!! $task_end_date !!}. </p>Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:49','common-templates'),(39,'task-update','1',26,'Task Update','<p>Hello {!! $receiver_name !!},<br /><br />Your task {!! $task_name !!} has been updated by {!! $updated_by !!}.</p>Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:49','common-templates'),(40,'task-status','1',27,'Task status update','<p>Hello {!! $receiver_name !!},<br /><br />Your task {!! $task_name !!} status has been {!! $status !!}.</p>Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:49','common-templates'),(41,'task-assigned','1',28,'Task Assigned','<p>Hello {!! $receiver_name !!},<br /><br />You have been assigned to task {!! $task_name !!} by {!! $created_by !!}  is due on {!! $task_end_date !!}.</p>Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:49','common-templates'),(42,'task-deleted','1',29,'Task Deleted','<p>Hello {!! $receiver_name !!},<br /><br />Your task {!! $task_name !!} was deleted or removed by {!! $updated_by !!}.</p>Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:49','common-templates'),(43,'task-assigned-owner','1',30,'Task Assigned','<p>Hello {!! $receiver_name !!},<br /><br />Your task {!! $task_name !!} has been assigned to {!! $agent_name !!} and  is due on {!! $task_end_date !!}.</p><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:49','common-templates'),(44,'template-rating-feedback','1',31,'Rating','<p>Hello {!! $receiver_name !!},</p><p>We hope you are happy with our support for the <strong>Ticket ID:</strong>&nbsp;{!! $ticket_number !!}</p><p>Please provide us with your valuable feedback for our service by clicking on a star of your choice.</p><p>{!! $ratings_icon_with_link !!}</p><br/>Thank You.<br/>Kind Regards,<br/>{!! $system_from !!}</p>','',1,'2021-02-17 05:35:47','2021-02-17 05:35:49','common-templates'),(45,'template-rating-confirmation','1',32,'Rating Submitted','<p>Hello {!! $receiver_name !!},<br /><br />Overall rating has been submitted for a ticket {!! $ticket_link !!}<br /><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','agent-templates'),(46,'template-ticket-creation-acknowledgement-org-mngr-by-client','0',4,'','<p>Hello {!! $receiver_name !!},<br/><br/>A member of your organization has created a new ticket in our helpdesk system.</p><p><strong>Ticket ID:</strong>&nbsp; {!! $ticket_number !!}<br/><br/><strong>Member&#39;s Details</strong><br/><strong>Name:</strong>&nbsp; {!! $client_name !!}<br/><strong>E-mail:</strong> {!! $client_email !!}<br/><br/><strong>Message</strong><br/>{!! $message_content !!}<br/><br/>Kind Regards,<br />{!! $system_from !!}</p>','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','org-mngr-templates'),(47,'template-ticket-creation-acknowledgement-org-mngr-by-agent','0',6,'','<p>Hello {!! $receiver_name !!},<br/><br/>Our agent has created a new ticket for a request of your organization&#39;s member.</p><p><strong>Ticket ID:</strong>&nbsp; {!! $ticket_number !!}<br/><br/><strong>Member&#39;s Details</strong><br/><strong>Name:</strong>&nbsp; {!! $client_name !!}<br/><strong>E-mail:</strong> {!! $client_email !!}<br/><br/><strong>Message</strong><br/>{!! $message_content !!}<br/><br/>Kind Regards,<br />{!! $system_from !!}</p>','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','org-mngr-templates'),(48,'template-ticket-reply-to-agents-by-client-to-org-mngr','0',9,'','{!! $message_content !!}<br /><br /><strong>Ticket Details</strong><strong>Ticket ID:</strong> {!! $ticket_number !!}<br /><br />{!! $agent_signature !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','org-mngr-templates'),(49,'template-ticket-reply-to-client-by-agent-to-org-mngr','0',10,'','Hello {!! $receiver_name !!},<br /><br />Your organization member has made a new reply on their ticket in our helpdesk system.<br /><br /><strong>Ticket ID:</strong>  {!! $ticket_number !!}<br /><strong>Reply Message</strong><br />{!! $message_content !!}<br /><br />Please follow the link below to check and reply on ticket.<br />{!! $ticket_link !!}<br /><br />Thanks<br />{!! $system_from !!}<br />','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','org-mngr-templates'),(50,'template-ticket-assignment-notice-to-org-mngr','0',1,'','Hello {!! $receiver_name !!},<br /><br />Your organization member\'s ticket with ID: {!! $ticket_number !!} has been assigned to one of our agents. They will contact them soon.<br /><br /><br />Kind Regards,<br />{!! $system_from !!}</p>','',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','org-mngr-templates'),(51,'template-ticket-approval','1',34,'Ticket Approval Link','<p>Hello {!! $receiver_name !!},<br /><br />Ticket <a href=\"{!! $ticket_link !!}\">{!! $ticket_number !!}</a> has been waiting for your approval.<br /><br />Please click <a href={!! $approval_link !!}>here</a> to review the ticket.<br /><br />Have a nice day.<br /><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:49','common-templates'),(52,'template-register-confirmation-with-account-details-and-activation','1',35,'Registration confirmed, account verifcation required','Hello {!! $receiver_name !!},<br /><br />This email is confirmation that you are now registered at our helpdesk.<br /><br /><strong>Registered Email:</strong> {!! $new_user_email !!}<br /><strong>Password:</strong> {!! $user_password !!}<br /><br />Please click on the below link to activate your account and Login to the system<br/>{!! $account_activation_link !!}<br/><br/>You can visit the helpdesk to browse articles and contact us at any time: {!! $system_link !!}<br />Thank You.<br /><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:47','2021-02-17 05:35:49','common-templates'),(53,'template-error-notification','1',36,'Helpdesk error notification','<p>Hello {!! $receiver_name !!},<br /><br />{!! $error_message !!}.<br /><br /><strong>Details</strong><br />Message: {!! $ex_message !!}<br />File: {!! $ex_file !!}<br />Line: {!! $ex_line !!}<br />Trace: <blockquote>{!! $ex_trace !!}</blockquote><br /><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:48','2021-02-17 05:35:48','agent-templates'),(54,'ticket-forwarding','1',37,'','Hello,<br /><br />Following ticket has been forwarded to you by {!! $user_profile_link !!}<br /><br />{!! $ticket_conversation !!}Thank You.<br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:35:48','2021-02-17 05:35:49','common-templates'),(55,'system-backup-completed','1',38,'Your system backup has been completed successfully','Hello {!! $receiver_name !!} <br/> <br/>Your system backup for {!! $version !!} has been completed successfully <br/> <br/> Kind Regards, <br>{!! $company_name !!}','',1,'2021-02-17 05:35:49','2021-02-17 05:35:49','agent-templates'),(56,'template-ticket-reply-to-client-by-client','0',10,'','Hello {!! $receiver_name !!},<br /><br />{!! $activity_by !!} has made a new reply on the ticket with Id <strong>{!! $ticket_number !!}</strong></.<br /><br />Reply Message</strong><br />{!! $message_content !!}<br /><br />Please follow the link below to check and reply on ticket.<br />{!! $ticket_link !!}<br /><br />Thanks<br />{!! $system_from !!}<br />','',1,'2021-02-17 05:35:49','2021-02-17 05:35:49','client-templates'),(57,'template-contract-approval-by-agent-or-admin','1',39,'Request for contract approval','Hello {!! $receiver_name !!} <br/> <br/>A new Contract {!! $contract_name !!} (#CNTR-{!! $contract_id !!}) has been submitted for your approval. Please verify the contract and confirm your approval. <br/> <br/>Contract : {!! $contract_link !!} <br/> <br/>Please click <a href={!! $contract_approval_link !!}>here</a> to review the contract.<br /><br />Kind Regards, <br>{!! $company_name !!}','',1,'2021-02-17 05:39:08','2021-02-17 05:39:08','agent-templates'),(58,'template-contract-approved-by-agent-or-admin','1',40,'Your contract is approved','Hello {!! $receiver_name !!} <br/> <br/>Contract {!! $contract_name !!} (#CNTR-{!! $contract_id !!}) has been approved by {!! $approver_name !!}.You can go through the contract. <br/> <br/>Contract : {!! $contract_link !!} <br/> <br/>Kind Regards, <br>{!! $company_name !!}','',1,'2021-02-17 05:39:08','2021-02-17 05:39:08','agent-templates'),(59,'template-contract-rejected-by-agent-or-admin','1',41,'Your Contract is rejected','Hello {!! $receiver_name !!} <br/> <br/>Contract {!! $contract_name !!} (#CNTR-{!! $contract_id !!}) has been rejected by {!! $approver_name !!}. <br/>Purpose of Rejection : {!! $contract_reason_rejection !!} <br/>You can go through the contract and take necessary actions. <br/> <br/>Contract : {!! $contract_link !!} <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> Kind Regards, <br>{!! $company_name !!}','',1,'2021-02-17 05:39:08','2021-02-17 05:39:08','agent-templates'),(60,'template-contract-notify-agent-and-admin','1',42,'Your contract will expire on {!! $contract_expiry !!}','Hello {!! $receiver_name !!} <br/> <br/>Contract {!! $contract_name !!} (#CNTR-{!! $contract_id !!}) will expire on {!! $contract_expiry !!}.You can go through the contract and take necessary actions. <br/> <br/>Contract : {!! $contract_link !!} <br/> <br/>Kind Regards, <br>{!! $company_name !!}','',1,'2021-02-17 05:39:08','2021-02-17 05:39:08','agent-templates'),(61,'template-change-approval','1',43,'Change Approval Link','<p>Hello {!! $receiver_name !!},<br /><br />Change <a href=\"{!! $change_link !!}\">{!! $change_number !!}</a> has been waiting for your approval.<br /><br />Please click <a href={!! $change_approval_link !!}>here</a> to review the change.<br /><br />Have a nice day.<br /><br />Kind Regards,<br />{!! $system_from !!}','',1,'2021-02-17 05:39:08','2021-02-17 05:39:08','agent-templates'),(62,'template-send-invoice','0',44,'','<p>Hello {!! $receiver_name !!}<br /><br />We found that you were interested in purchasing one of our support packages. We have raised and attached an invoice for the same with this email.</p><p>Please continue to checkout process so we can confirm your order.</p>.<p><br />Kind Regards,<br />{!! $system_from !!}</p><p>&nbsp;</p>','',1,'2021-02-17 05:39:09','2021-02-17 05:39:09','common-templates'),(63,'template-purchase-notification','0',45,'','<p>Hello {!! $receiver_name !!}</p><br /><p>An order of a user has been confirmed for a support package in the store. Below are the details of the order.</p><p>Order Details:</p><p><strong>Invoice No</strong>:&nbsp;{!! $invoice_id !!}</p><p><strong>Client Name</strong>:&nbsp;{!! $invoice_user_name !!}</p><p><strong>Amount Paid</strong>:&nbsp;{!! $invioce_amount_paid !!}</p><p><strong>Paid date:</strong> {!! $invoice_paid_date !!}</p><p><strong>Payment mode</strong>:&nbsp;{!! $invoice_payment_mode !!}</p><p><strong>Package Name</strong>:&nbsp;{!! $package_name !!}</p><p><strong>Package Validity</strong>:&nbsp;{!! $package_validity !!}</p><p><strong>Package expriry date</strong>: {!! $order_expriy_date !!}</p><p><strong>Package Credit Type</strong>: {!! $package_credit_type !!}</p><p><strong>Last Transaction amount</strong>: {!! $last_transaction_amount !!}</p><p><strong>Last Transaction processed by</strong>:&nbsp;{!! $last_transaction_by_name !!}</p><p>Please find the attached invoice for more details about the order.</p><p><br />Kind Regards,<br />{!! $system_from !!}</p>','',1,'2021-02-17 05:39:09','2021-02-17 05:39:09','agent-templates'),(64,'template-purchase-confirmation','0',45,'','<p>Hello {!! $receiver_name !!}<br />Your recent purchase has been confirmed. You can use the purchased package for raising support tickets.</p><p>Order Details:</p><p><strong>Invoice No</strong>:&nbsp;{!! $invoice_id !!}</p><p><strong>Amount Paid</strong>:&nbsp;{!! $invioce_amount_paid !!}</p><p><strong>Paid date:</strong> {!! $invoice_paid_date !!}</p><p><strong>Package Name</strong>:&nbsp;{!! $package_name !!}</p><p><strong>Package Validity</strong>:&nbsp;{!! $package_validity !!}</p><p><strong>Package expriry date</strong>: {!! $order_expriy_date !!}</p><p><strong>Package Credit Type</strong>: {!! $package_credit_type !!}</p><p>Please find the attached invoice for your order.<br />Kind Regards,<br />{!! $system_from !!}</p><p>&nbsp;</p>','',1,'2021-02-17 05:39:09','2021-02-17 05:39:09','client-templates'),(65,'template-payment-approval','0',46,'','<p>Hello {!! $receiver_name !!}</p><br /><p>A user has purchased a package and selected on an offline payment&nbsp;method.</p><br /><p>Order Details:</p><p><strong>Invoice No</strong>:&nbsp;{!! $invoice_id !!}</p><p><strong>Invoice Link</strong>:&nbsp;{!! $invoice_link_for_agent !!}</p><p><strong>Client Name</strong>:&nbsp;{!! $invoice_user_name !!}</p><p><strong>Amount Paid</strong>:&nbsp;{!! $invioce_amount_paid !!}</p><p><strong>Paid date:</strong> {!! $invoice_paid_date !!}</p><p><strong>Payment mode</strong>:&nbsp;{!! $invoice_payment_mode !!}</p><p><strong>Package Name</strong>:&nbsp;{!! $package_name !!}</p><p><strong>Package Validity</strong>:&nbsp;{!! $package_validity !!}</p><p><strong>Package expriry date</strong>: {!! $order_expriy_date !!}</p><p><strong>Package Credit Type</strong>: {!! $package_credit_type !!}</p><br /><p>Please contact the user to&nbsp;verify payment details and confirm the order</p><p><br />Kind Regards,<br />{!! $system_from !!}</p>','',1,'2021-02-17 05:39:09','2021-02-17 05:39:09','agent-templates');
/*!40000 ALTER TABLE `templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_sets`
--

DROP TABLE IF EXISTS `template_sets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_sets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `template_language` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'en',
  `department_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `template_sets_department_id_foreign` (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_sets`
--

LOCK TABLES `template_sets` WRITE;
/*!40000 ALTER TABLE `template_sets` DISABLE KEYS */;
INSERT INTO `template_sets` VALUES (1,'default',1,'2021-02-17 05:35:47','2021-02-17 05:35:47','en',NULL);
/*!40000 ALTER TABLE `template_sets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_shortcodes`
--

DROP TABLE IF EXISTS `template_shortcodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_shortcodes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `shortcode` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description_lang_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `plugin_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_shortcodes`
--

LOCK TABLES `template_shortcodes` WRITE;
/*!40000 ALTER TABLE `template_shortcodes` DISABLE KEYS */;
INSERT INTO `template_shortcodes` VALUES (1,'receiver_name','{!! $receiver_name !!}','lang.shortcode_receiver_name_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(2,'system_link','{!! $system_link !!}','lang.shortcode_system_link_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(3,'system_name','{!! $system_name !!}','lang.shortcode_system_from_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(4,'company_name','{!! $company_name !!}','lang.shortcode_company_name_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(5,'company_link','{!! $company_link !!}','lang.shortcode_company_link_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(6,'new_user_name','{!! $new_user_name !!}','lang.shortcode_new_user_name_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(7,'new_user_email','{!! $new_user_email !!}','lang.shortcode_new_user_email_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(8,'user_password','{!! $user_password !!}','lang.shortcode_user_password_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(9,'password_reset_link','{!! $password_reset_link !!}','lang.shortcode_password_reset_link_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(10,'account_activation_link','{!! $account_activation_link !!}','lang.shortcode_account_activation_link_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(11,'ticket_link','{!! $ticket_link !!}','lang.shortcode_ticket_link_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(12,'ticket_number','{!! $ticket_number !!}','lang.shortcode_ticket_number_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(13,'ticket_subject','{!! $ticket_subject !!}','lang.shortcode_ticket_subject_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(14,'ticket_due_date','{!! $ticket_due_date !!}','lang.shortcode_ticket_due_date_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(15,'ticket_created_at','{!! $ticket_created_at !!}','lang.shortcode_ticket_created_at_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(16,'department_signature','{!! $department_signature !!}','lang.shortcode_department_signature_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(17,'agent_name','{!! $agent_name !!}','lang.shortcode_agent_name_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(18,'agent_email','{!! $agent_email !!}','lang.shortcode_agent_email_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(19,'agent_contact','{!! $agent_contact !!}','lang.shortcode_agent_contact_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(20,'agent_sign','{!! $agent_signature !!}','lang.shortcode_agent_signature_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(21,'client_name','{!! $client_name !!}','lang.shortcode_client_name_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(22,'client_email','{!! $client_email !!}','lang.shortcode_client_email_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(23,'client_contact','{!! $client_contact !!}','lang.shortcode_client_contact_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(24,'user_profile_link','{!! $user_profile_link !!}','lang.shortcode_user_profile_link_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(25,'message_content','{!! $message_content !!}','lang.shortcode_message_content_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(26,'activity_by','{!! $activity_by !!}','lang.shortcode_activity_by_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(27,'assigned_team_name','{!! $assigned_team_name !!}','lang.shortcode_assigned_team_name_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(28,'otp_code','{!! $otp_code !!}','lang.shortcode_otp_code_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(29,'ticket_client_edit_link','{!! $ticket_edit_link !!}','lang.shortcode_to_send_ticket_edit_link',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(30,'approve_url','{!! $approve_url !!}','lang.shortcode_approve_url_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(31,'deny_url','{!! $deny_url !!}','lang.shortcode_deny_url_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(32,'total_time','{!! $total_time !!}','lang.shortcode_total_time_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(33,'cost','{!! $cost !!}','lang.shortcode_cost_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(34,'bill_date','{!! $bill_date !!}','lang.shortcode_bill_date_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(35,'currency','{!! $currency !!}','lang.shortcode_currency_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(36,'task_name','{!! $task_name !!}','lang.shortcode_task_name_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(37,'task_end_date','{!! $task_end_date !!}','lang.shortcode_task_end_date_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(38,'status','{!! $status !!}','lang.shortcode_status_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(39,'updated_by','{!! $updated_by !!}','lang.shortcode_updated_by_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(40,'created_by','{!! $created_by !!}','lang.shortcode_created_by_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(41,'report_type','{!! $report_type !!}','lang.shortcode_report_type_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(42,'report_link','{!! $report_link !!}','lang.shortcode_report_link_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(43,'report_expiry','{!! $report_expiry !!}','lang.shortcode_report_expiry_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(44,'approval_link','{!! $approval_link !!}','lang.shortcode_approval_link_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(45,'ratings_icon_with_link','{!! $ratings_icon_with_link !!}','lang.shortcode_ratings_icon_with_link_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(46,'error_message','{!! $error_message !!}','lang.shortcode_error_message_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(47,'ex_message','{!! $ex_message !!}','lang.shortcode_ex_message_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(48,'ex_file','{!! $ex_file !!}','lang.shortcode_ex_file_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(49,'ex_line','{!! $ex_line !!}','lang.shortcode_ex_line_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(50,'ex_trace','{!! $ex_trace !!}','lang.shortcode_ex_trace_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(51,'rating_value','{!! $rating_value !!}','lang.shortcode_rating_value_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(52,'session_id','{!! $session_id !!}','lang.shortcode_session_id_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(53,'survey_link','{!! $survey_link !!}','lang.shortcode_survey_link_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(54,'reference_link','{!! $reference_link !!}','lang.shortcode_reference_link_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(55,'ticket_conversation','{!! $ticket_conversation !!}','lang.shortcode_ticket_conversation_description',NULL,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(56,'version','{!! $version !!}','lang.shortcode_version_description',NULL,'2021-02-17 05:35:49','2021-02-17 05:35:49'),(57,'contract_id','{!! $contract_id !!}','ServiceDesk::lang.shortcode_contract_id_description','ServiceDesk','2021-02-17 05:39:08','2021-02-17 05:39:08'),(58,'contract_name','{!! $contract_name !!}','ServiceDesk::lang.shortcode_contract_name_description','ServiceDesk','2021-02-17 05:39:08','2021-02-17 05:39:08'),(59,'contract_link','{!! $contract_link !!}','ServiceDesk::lang.shortcode_contract_link_description','ServiceDesk','2021-02-17 05:39:08','2021-02-17 05:39:08'),(60,'approver_name','{!! $approver_name !!}','ServiceDesk::lang.shortcode_approver_name_description','ServiceDesk','2021-02-17 05:39:08','2021-02-17 05:39:08'),(61,'contract_expiry','{!! $contract_expiry !!}','ServiceDesk::lang.shortcode_contract_expiry_description','ServiceDesk','2021-02-17 05:39:08','2021-02-17 05:39:08'),(62,'contract_reason_rejection','{!! $contract_reason_rejection !!}','ServiceDesk::lang.shortcode_contract_reason_rejection_description','ServiceDesk','2021-02-17 05:39:08','2021-02-17 05:39:08'),(63,'change_number','{!! $change_number !!}','ServiceDesk::lang.shortcode_change_number_description','ServiceDesk','2021-02-17 05:39:08','2021-02-17 05:39:08'),(64,'change_link','{!! $change_link !!}','ServiceDesk::lang.shortcode_change_link_description','ServiceDesk','2021-02-17 05:39:08','2021-02-17 05:39:08'),(65,'change_approval_link','{!! $change_approval_link !!}','ServiceDesk::lang.shortcode_change_approval_link_description','ServiceDesk','2021-02-17 05:39:08','2021-02-17 05:39:08'),(66,'contract_approval_link','{!! $contract_approval_link !!}','ServiceDesk::lang.shortcode_contract_approval_link_description','ServiceDesk','2021-02-17 05:39:08','2021-02-17 05:39:08'),(67,'invoice_id','{!! $invoice_id !!}','Bill::lang.shortcode_invoice_id_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(68,'invoice_created_at','{!! $invoice_created_at !!}','Bill::lang.shortcode_invoice_created_at_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(69,'invoice_total_amount','{!! $invoice_total_amount !!}','Bill::lang.shortcode_invoice_total_amount_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(70,'invioce_payable_amount','{!! $invioce_payable_amount !!}','Bill::lang.shortcode_invioce_payable_amount_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(71,'invoice_payment_mode','{!! $invoice_payment_mode !!}','Bill::lang.shortcode_invoice_payment_mode_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(72,'invoice_due_by','{!! $invoice_due_by !!}','Bill::lang.shortcode_invoice_due_by_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(73,'invioce_amount_paid','{!! $invioce_amount_paid !!}','Bill::lang.shortcode_invioce_amount_paid_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(74,'invoice_paid_date','{!! $invoice_paid_date !!}','Bill::lang.shortcode_invoice_paid_date_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(75,'invoice_user_name','{!! $invoice_user_name !!}','Bill::lang.shortcode_invoice_user_name_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(76,'invoice_user_email','{!! $invoice_user_email !!}','Bill::lang.shortcode_invoice_user_email_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(77,'invoice_link_for_client','{!! $invoice_link_for_client !!}','Bill::lang.shortcode_invoice_link_for_client_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(78,'invoice_link_for_agent','{!! $invoice_link_for_agent !!}','Bill::lang.shortcode_invoice_link_for_agent_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(79,'invoice_discount','{!! $invoice_discount !!}','Bill::lang.shortcode_invoice_discount_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(80,'invoice_tax','{!! $invoice_tax !!}','Bill::lang.shortcode_invoice_tax_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(81,'package_credit_type','{!! $package_credit_type !!}','Bill::lang.shortcode_package_credit_type_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(82,'order_id','{!! $order_expriy_date !!}','Bill::lang.shortcode_order_id_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(83,'order_expriy_date','{!! $order_expriy_date !!}','Bill::lang.shortcode_order_expriy_date_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(84,'order_link_for_client','{!! $order_link_for_client !!}','Bill::lang.shortcode_order_link_for_client_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(85,'package_name','{!! $package_name !!}','Bill::lang.shortcode_package_name_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(86,'package_description','{!! $package_description !!}','Bill::lang.shortcode_package_description_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(87,'package_price','{!! $package_price !!}','Bill::lang.shortcode_package_price_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(88,'package_validity','{!! $package_validity !!}','Bill::lang.shortcode_package_validity_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(89,'last_transaction_id','{!! $last_transaction_id !!}','Bill::lang.shortcode_last_transaction_id_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(90,'last_transaction_amount','{!! $last_transaction_amount !!}','Bill::lang.shortcode_last_transaction_amount_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(91,'last_transaction_status','{!! $last_transaction_status !!}','Bill::lang.shortcode_last_transaction_status_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(92,'last_transaction_method','{!! $last_transaction_method !!}','Bill::lang.shortcode_last_transaction_method_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(93,'last_transaction_by_name','{!! $last_transaction_by_name !!}','Bill::lang.shortcode_last_transaction_by_name_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09'),(94,'last_transaction_by_email','{!! $last_transaction_by_email !!}','Bill::lang.shortcode_last_transaction_by_email_description','Bill','2021-02-17 05:39:09','2021-02-17 05:39:09');
/*!40000 ALTER TABLE `template_shortcodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_types`
--

DROP TABLE IF EXISTS `template_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `plugin_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_types`
--

LOCK TABLES `template_types` WRITE;
/*!40000 ALTER TABLE `template_types` DISABLE KEYS */;
INSERT INTO `template_types` VALUES (1,'assign-ticket','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(2,'check-ticket','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(3,'close-ticket','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(4,'create-ticket','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(5,'create-ticket-agent','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(6,'create-ticket-by-agent','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(7,'registration-notification','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(8,'reset-password','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(9,'ticket-reply','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(10,'ticket-reply-agent','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(11,'email_verify','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(12,'response_due_violate','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(13,'resolve_due_violate','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(14,'team_assign_ticket','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(15,'reset_new_password','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(16,'internal_change','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(17,'response_due_approach','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(18,'resolve_due_approach','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(19,'new-user','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(20,'no_assign_message','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(21,'status-update','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(22,'approve-ticket','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(23,'invoice','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(24,'task-reminder','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(25,'task-created','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(26,'task-update','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(27,'task-status','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(28,'task-assigned','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(29,'task-deleted','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(30,'task-assigned-owner','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(31,'rating','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(32,'rating-confirmation','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(33,'report-export','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(34,'ticket-approval','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(35,'registration-and-verify','2021-02-17 05:35:47','2021-02-17 05:35:47',NULL),(36,'error-notification','2021-02-17 05:35:48','2021-02-17 05:35:48',NULL),(37,'ticket-forwarding','2021-02-17 05:35:48','2021-02-17 05:35:48',NULL),(38,'backup-completed','2021-02-17 05:35:49','2021-02-17 05:35:49',NULL),(39,'approve-contract','2021-02-17 05:39:08','2021-02-17 05:39:08','ServiceDesk'),(40,'approved-contract','2021-02-17 05:39:08','2021-02-17 05:39:08','ServiceDesk'),(41,'rejected-contract','2021-02-17 05:39:08','2021-02-17 05:39:08','ServiceDesk'),(42,'notify-expiry-contract','2021-02-17 05:39:08','2021-02-17 05:39:08','ServiceDesk'),(43,'approve-change','2021-02-17 05:39:08','2021-02-17 05:39:08','ServiceDesk'),(44,'send-invoice','2021-02-17 05:39:09','2021-02-17 05:39:09','Bill'),(45,'purchase-confirmation','2021-02-17 05:39:09','2021-02-17 05:39:09','Bill'),(46,'payment-approval','2021-02-17 05:39:09','2021-02-17 05:39:09','Bill');
/*!40000 ALTER TABLE `template_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_actions`
--

DROP TABLE IF EXISTS `ticket_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_actions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference_id` int(10) unsigned DEFAULT NULL,
  `reference_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_actions`
--

LOCK TABLES `ticket_actions` WRITE;
/*!40000 ALTER TABLE `ticket_actions` DISABLE KEYS */;
INSERT INTO `ticket_actions` VALUES (58,1,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','department_id','6','2021-04-19 09:06:50','2021-04-19 09:06:50'),(2,2,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','priority_id','4','2021-02-22 10:31:23','2021-02-22 10:31:23'),(3,2,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','department_id','5','2021-02-22 10:31:23','2021-02-22 10:31:23'),(4,2,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','mail_agent','\"\"','2021-02-22 10:31:23','2021-02-22 10:31:23'),(59,58,'App\\Model\\helpdesk\\Ticket\\TicketAction','custom_86','\"\"','2021-04-19 09:06:50','2021-04-19 09:06:50'),(60,59,'App\\Model\\helpdesk\\Ticket\\TicketAction','custom_87','\"\"','2021-04-19 09:06:50','2021-04-19 09:06:50'),(27,4,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','team_id','5','2021-02-23 14:35:41','2021-02-24 07:52:49'),(61,59,'App\\Model\\helpdesk\\Ticket\\TicketAction','custom_88','\"\"','2021-04-19 09:06:50','2021-04-19 09:06:50'),(62,58,'App\\Model\\helpdesk\\Ticket\\TicketAction','custom_89','\"\"','2021-04-19 09:06:50','2021-04-19 09:06:50'),(63,58,'App\\Model\\helpdesk\\Ticket\\TicketAction','custom_90','\"\"','2021-04-19 09:06:50','2021-04-19 09:06:50'),(64,58,'App\\Model\\helpdesk\\Ticket\\TicketAction','custom_91','\"\"','2021-04-19 09:06:50','2021-04-19 09:06:50'),(65,58,'App\\Model\\helpdesk\\Ticket\\TicketAction','custom_92','\"\"','2021-04-19 09:06:50','2021-04-19 09:06:50'),(66,58,'App\\Model\\helpdesk\\Ticket\\TicketAction','custom_93','\"\"','2021-04-19 09:06:50','2021-04-19 09:06:50'),(48,5,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','team_id','6','2021-02-23 17:56:34','2021-02-23 17:57:15'),(67,6,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','team_id','1','2021-05-21 10:25:55','2021-05-21 10:25:55'),(68,7,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','team_id','4','2021-05-21 10:27:54','2021-05-21 10:27:54'),(69,7,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','mail_requester','\"\"','2021-05-21 10:27:54','2021-05-21 10:27:54');
/*!40000 ALTER TABLE `ticket_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_filters`
--

DROP TABLE IF EXISTS `ticket_filters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_filters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `parent_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `display_on_dashboard` tinyint(1) DEFAULT NULL,
  `icon_class` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `icon_color` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_filters_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_filters`
--

LOCK TABLES `ticket_filters` WRITE;
/*!40000 ALTER TABLE `ticket_filters` DISABLE KEYS */;
INSERT INTO `ticket_filters` VALUES (1,'',1,NULL,'2021-02-17 05:35:49','2021-02-17 05:35:49',1,'App\\FaveoReport\\Models\\Report',NULL,NULL,NULL),(2,'',1,NULL,'2021-02-17 05:35:49','2021-02-17 05:35:49',2,'App\\FaveoReport\\Models\\Report',NULL,NULL,NULL),(3,'',1,NULL,'2021-02-17 05:35:49','2021-02-17 05:35:49',3,'App\\FaveoReport\\Models\\Report',NULL,NULL,NULL),(4,'',1,NULL,'2021-02-17 05:35:49','2021-02-17 05:35:49',4,'App\\FaveoReport\\Models\\Report',NULL,NULL,NULL),(5,'',1,NULL,'2021-02-17 05:35:49','2021-02-17 05:35:49',5,'App\\FaveoReport\\Models\\Report',NULL,NULL,NULL),(6,'',1,NULL,'2021-02-17 05:35:49','2021-02-17 05:35:49',6,'App\\FaveoReport\\Models\\Report',NULL,NULL,NULL),(7,'',1,NULL,'2021-02-17 05:35:49','2021-02-17 05:35:49',7,'App\\FaveoReport\\Models\\Report',NULL,NULL,NULL),(8,'',1,NULL,'2021-02-17 05:35:49','2021-02-17 05:35:49',8,'App\\FaveoReport\\Models\\Report',NULL,NULL,NULL);
/*!40000 ALTER TABLE `ticket_filters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_filter_meta`
--

DROP TABLE IF EXISTS `ticket_filter_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_filter_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_filter_id` int(10) unsigned NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value_meta` longtext COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_filter_meta_ticket_filter_id_foreign` (`ticket_filter_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_filter_meta`
--

LOCK TABLES `ticket_filter_meta` WRITE;
/*!40000 ALTER TABLE `ticket_filter_meta` DISABLE KEYS */;
INSERT INTO `ticket_filter_meta` VALUES (1,1,'created-at','','s:12:\"last::60~day\";'),(2,2,'created-at','','s:12:\"last::60~day\";'),(3,3,'created-at','','s:12:\"last::60~day\";'),(4,4,'created-at','','s:12:\"last::60~day\";'),(5,5,'created-at','','s:12:\"last::60~day\";'),(6,6,'created-at','','s:12:\"last::60~day\";'),(7,7,'created-at','','s:12:\"last::60~day\";'),(8,8,'created-at','','s:12:\"last::60~day\";');
/*!40000 ALTER TABLE `ticket_filter_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_priority`
--

DROP TABLE IF EXISTS `ticket_priority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_priority` (
  `priority_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `priority` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `priority_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `priority_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `priority_urgency` tinyint(1) NOT NULL,
  `ispublic` tinyint(1) NOT NULL,
  `is_default` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`priority_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_priority`
--

LOCK TABLES `ticket_priority` WRITE;
/*!40000 ALTER TABLE `ticket_priority` DISABLE KEYS */;
INSERT INTO `ticket_priority` VALUES (1,'Low','1','Low','#00a65a',4,1,'',NULL,NULL),(2,'Medium','1','Medium','#00bfef',3,1,'1',NULL,NULL),(3,'High','1','High','#f39c11',2,1,'',NULL,NULL),(4,'Urgent','1','Urgent','#dd4b38',1,1,'',NULL,NULL);
/*!40000 ALTER TABLE `ticket_priority` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_rules`
--

DROP TABLE IF EXISTS `ticket_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference_id` int(10) unsigned DEFAULT NULL,
  `reference_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `relation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `base_rule` tinyint(1) NOT NULL DEFAULT 0,
  `category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_rules`
--

LOCK TABLES `ticket_rules` WRITE;
/*!40000 ALTER TABLE `ticket_rules` DISABLE KEYS */;
INSERT INTO `ticket_rules` VALUES (1,1,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','help_topic_id','equal','9','2021-02-17 19:46:13','2021-02-17 19:46:13',0,'ticket'),(5,5,'App\\Model\\helpdesk\\Ticket\\TicketSla','custom_47','equal','\"4\"','2021-02-20 12:05:29','2021-05-28 09:38:11',1,'ticket'),(17,4,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','help_topic_id','equal','7','2021-02-23 14:32:48','2021-02-24 07:52:49',1,'ticket'),(10,2,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','help_topic_id','equal','6','2021-02-22 10:31:23','2021-02-22 10:31:23',0,'ticket'),(11,10,'App\\Model\\helpdesk\\Ticket\\TicketRule','custom_68','equal','\"Installation Related Issues\"','2021-02-22 10:31:23','2021-02-22 10:31:23',0,''),(12,11,'App\\Model\\helpdesk\\Ticket\\TicketRule','custom_69','equal','\"When is my engineer coming or they missed an appointment\"','2021-02-22 10:31:23','2021-04-19 09:07:17',1,''),(13,2,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','source_id','equal','6','2021-02-22 10:31:23','2021-04-19 09:07:17',1,'ticket'),(19,5,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','help_topic_id','equal','9','2021-02-23 17:56:34','2021-02-23 17:56:34',0,'ticket'),(30,7,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','location_id','equal','4','2021-05-21 10:27:54','2021-05-21 10:27:54',1,'ticket'),(28,6,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','location_id','equal','4','2021-05-21 10:25:55','2021-05-21 10:25:55',1,'ticket'),(29,7,'App\\Model\\helpdesk\\Ticket\\TicketWorkflow','status_id','equal','11','2021-05-21 10:27:54','2021-05-21 10:27:54',1,'ticket'),(24,2,'App\\Model\\helpdesk\\Ticket\\TicketSla','custom_47','equal','\"1\"','2021-03-02 14:20:00','2021-03-11 15:12:08',1,'ticket'),(25,3,'App\\Model\\helpdesk\\Ticket\\TicketSla','custom_47','equal','\"2\"','2021-03-02 14:22:55','2021-03-10 15:50:37',1,'ticket'),(26,4,'App\\Model\\helpdesk\\Ticket\\TicketSla','custom_47','equal','\"3\"','2021-03-02 14:28:52','2021-05-28 09:37:56',1,'ticket');
/*!40000 ALTER TABLE `ticket_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_slas`
--

DROP TABLE IF EXISTS `ticket_slas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_slas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `order` int(11) NOT NULL,
  `matcher` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL,
  `internal_notes` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_slas`
--

LOCK TABLES `ticket_slas` WRITE;
/*!40000 ALTER TABLE `ticket_slas` DISABLE KEYS */;
INSERT INTO `ticket_slas` VALUES (1,'Due Time Escalation',1,5,'',1,NULL,'2021-02-17 05:35:49','2021-03-02 15:38:26'),(2,'SLA1 Corporate Customers',1,1,'any',0,'SLA for Corporate customers','2021-02-20 11:23:05','2021-03-11 15:12:08'),(3,'SLA2 Business Customer',1,2,'any',0,'','2021-02-20 12:04:08','2021-03-10 15:50:37'),(4,'SLA3 SME Customers',1,3,'any',0,'','2021-02-20 12:04:53','2021-05-28 09:37:56'),(5,'SLA4 Consumer Customers',1,4,'all',0,'','2021-02-20 12:05:29','2021-05-28 09:38:11');
/*!40000 ALTER TABLE `ticket_slas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_sla_metas`
--

DROP TABLE IF EXISTS `ticket_sla_metas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_sla_metas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_sla_id` int(11) NOT NULL,
  `priority_id` int(11) NOT NULL,
  `respond_within` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resolve_within` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `business_hour_id` int(11) NOT NULL,
  `send_email_notification` tinyint(1) NOT NULL DEFAULT 1,
  `send_app_notification` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_sla_metas`
--

LOCK TABLES `ticket_sla_metas` WRITE;
/*!40000 ALTER TABLE `ticket_sla_metas` DISABLE KEYS */;
INSERT INTO `ticket_sla_metas` VALUES (1,1,1,'diff::60~minute','diff::48~hour',1,1,1),(2,1,2,'diff::30~minute','diff::12~hour',1,1,1),(3,1,3,'diff::20~minute','diff::6~hour',1,1,1),(4,1,4,'diff::10~minute','diff::2~hour',1,1,1),(5,2,3,'diff::20~minute','diff::6~hour',1,1,1),(6,2,1,'diff::60~minute','diff::48~hour',1,1,1),(7,2,2,'diff::30~minute','diff::12~hour',1,1,1),(8,2,4,'diff::10~minute','diff::2~hour',1,1,1),(9,3,3,'diff::30~minute','diff::12~hour',1,1,1),(10,3,1,'diff::60~minute','diff::36~hour',1,1,1),(11,3,2,'diff::45~minute','diff::24~hour',1,1,1),(12,3,4,'diff::20~minute','diff::4~hour',1,1,1),(13,4,3,'diff::60~minute','diff::16~hour',1,1,1),(14,4,1,'diff::150~minute','diff::36~hour',1,1,1),(15,4,2,'diff::90~minute','diff::24~hour',1,1,1),(16,4,4,'diff::30~minute','diff::8~hour',1,1,1),(17,5,3,'diff::2~hour','diff::24~hour',1,1,1),(18,5,1,'diff::8~hour','diff::2~day',1,1,1),(19,5,2,'diff::4~hour','diff::36~hour',1,1,1),(20,5,4,'diff::1~hour','diff::12~hour',1,1,1),(21,6,3,'diff::4~minute','diff::10~minute',1,0,0),(22,6,1,'diff::4~minute','diff::10~minute',1,0,0),(23,6,2,'diff::4~minute','diff::10~minute',1,0,0),(24,6,4,'diff::4~minute','diff::10~minute',1,1,1),(25,7,3,'diff::10~minute','diff::10~hour',1,1,1),(26,7,1,'diff::20~minute','diff::10~hour',1,0,0),(27,7,2,'diff::15~minute','diff::10~hour',1,1,1),(28,7,4,'diff::5~minute','diff::10~hour',1,0,0);
/*!40000 ALTER TABLE `ticket_sla_metas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_source`
--

DROP TABLE IF EXISTS `ticket_source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_source` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `css_class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_default` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_source`
--

LOCK TABLES `ticket_source` WRITE;
/*!40000 ALTER TABLE `ticket_source` DISABLE KEYS */;
INSERT INTO `ticket_source` VALUES (1,'Web','Web','fa fa-globe',NULL,'',1),(2,'Email','E-mail','fa fa-envelope',NULL,'',1),(3,'Agent','Agent Panel','fa fa-user',NULL,'',1),(4,'Facebook','Facebook','fa fa-facebook',NULL,'',1),(5,'Twitter','Twitter','fa fa-twitter',NULL,'',1),(6,'Call','Call','fa fa-phone',NULL,'',1),(7,'Chat','Chat','fa fa-comment',NULL,'',1);
/*!40000 ALTER TABLE `ticket_source` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_status`
--

DROP TABLE IF EXISTS `ticket_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `visibility_for_client` tinyint(1) NOT NULL,
  `allow_client` tinyint(1) NOT NULL,
  `visibility_for_agent` tinyint(1) NOT NULL,
  `purpose_of_status` int(11) NOT NULL,
  `secondary_status` int(11) DEFAULT NULL,
  `send_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `halt_sla` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `default` int(11) DEFAULT NULL,
  `send_sms` tinyint(1) NOT NULL DEFAULT 0,
  `auto_close` int(10) unsigned DEFAULT NULL,
  `comment` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ticket_status_auto_close_foreign` (`auto_close`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_status`
--

LOCK TABLES `ticket_status` WRITE;
/*!40000 ALTER TABLE `ticket_status` DISABLE KEYS */;
INSERT INTO `ticket_status` VALUES (1,'Open','Ticket has been Reopened by {!!$user!!}','2021-02-17 05:35:47','2021-02-17 05:35:48',1,1,1,1,NULL,'{\"client\":\"1\",\"admin\":\"0\",\"assigned_agent_team\":\"1\"}',0,1,'fa fa-clock-o','#32c777',1,0,1,0),(10,'Stalled','','2021-02-23 19:41:45','2021-02-23 19:41:45',1,0,0,1,NULL,'{\"client\":\"0\",\"admin\":\"0\",\"assigned_agent_team\":\"0\"}',1,3,'fa fa-edit','#75e66d',NULL,0,NULL,1),(3,'Closed','<p>Ticket has been Closed by {!!$user!!}</p>\r\n','2021-02-17 05:35:47','2021-03-31 08:28:49',1,1,1,2,NULL,'{\"client\":\"1\",\"assigned_agent_team\":\"1\"}',1,5,'fa fa-minus-circle','#5cb85c',1,0,NULL,0),(4,'Deleted','<p>Ticket has been Deleted by {!!$user!!}</p>\r\n','2021-02-17 05:35:47','2021-03-31 08:28:37',1,1,1,4,NULL,'{\"client\":\"0\",\"admin\":\"0\",\"assigned_agent_team\":\"0\"}',1,6,'fa fa-edit','#f20630',1,0,NULL,0),(9,'In Progress','','2021-02-23 19:41:32','2021-06-03 09:36:51',1,0,0,1,NULL,'{\"client\":\"0\",\"admin\":\"0\",\"assigned_agent_team\":\"0\"}',0,2,'fa fa-edit','#75e66d',NULL,0,NULL,1),(6,'Spam','<p>Ticket has been marked as Spam by {!!$user!!}</p>\r\n','2021-02-17 05:35:47','2021-03-31 08:28:26',0,0,1,6,1,'{\"client\":\"0\",\"admin\":\"0\",\"assigned_agent_team\":\"0\"}',1,7,'glyphicon glyphicon-alert','#f0ad4e',1,0,NULL,0),(7,'Unapproved','<p>Ticket has been marked as Unapproved by {!!$user!!}</p>\r\n','2021-02-17 05:35:47','2021-03-31 08:27:45',0,0,1,7,1,'{\"admin\":\"1\"}',1,8,'fa fa-clock-o','#f20630',1,0,NULL,0),(8,'Merged','<p>Ticket have been marked as Merged by {!!$user!!}</p>\r\n','2021-02-17 05:35:48','2021-03-31 08:27:32',0,0,1,8,1,'{\"admin\":\"1\"}',1,9,'fa fa-clock-o','#f20630',1,0,NULL,0),(11,'Resolved','','2021-03-31 08:30:43','2021-03-31 08:30:43',1,0,0,1,NULL,'{\"client\":\"0\",\"admin\":\"0\",\"assigned_agent_team\":\"0\"}',1,4,'fa fa-edit','#75e66d',NULL,0,NULL,1);
/*!40000 ALTER TABLE `ticket_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_status_type`
--

DROP TABLE IF EXISTS `ticket_status_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_status_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_status_type`
--

LOCK TABLES `ticket_status_type` WRITE;
/*!40000 ALTER TABLE `ticket_status_type` DISABLE KEYS */;
INSERT INTO `ticket_status_type` VALUES (1,'open','2021-02-17 05:35:47','2021-02-17 05:35:47'),(2,'closed','2021-02-17 05:35:47','2021-02-17 05:35:47'),(3,'archieved','2021-02-17 05:35:47','2021-02-17 05:35:47'),(4,'deleted','2021-02-17 05:35:47','2021-02-17 05:35:47'),(6,'spam','2021-02-17 05:35:47','2021-02-17 05:35:47'),(7,'unapproved','2021-02-17 05:35:47','2021-02-17 05:35:47'),(8,'merged','2021-02-17 05:35:48','2021-02-17 05:35:48');
/*!40000 ALTER TABLE `ticket_status_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_type`
--

DROP TABLE IF EXISTS `ticket_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `type_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ispublic` int(11) NOT NULL,
  `is_default` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_type`
--

LOCK TABLES `ticket_type` WRITE;
/*!40000 ALTER TABLE `ticket_type` DISABLE KEYS */;
INSERT INTO `ticket_type` VALUES (1,'Question',1,'Question',1,1,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(2,'Incident',0,'Incident',0,0,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(3,'Problem',0,'Problem',0,0,'2021-02-17 05:35:48','2021-02-17 05:35:48'),(5,'Corporate Ticket Type',1,'For Corporate Customers',1,0,'2021-02-22 15:09:34','2021-02-22 15:09:34'),(6,'Business Ticket Type',1,'For Business Customer Type',1,0,'2021-02-22 15:20:04','2021-02-22 15:20:49'),(7,'SME Ticket Type',1,'For SME Customers',1,0,'2021-02-22 20:06:37','2021-02-22 20:06:37'),(8,'Consumer Ticket Type',1,'For Consumer/Residential Customers',1,0,'2021-02-23 01:01:39','2021-02-23 01:02:00');
/*!40000 ALTER TABLE `ticket_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_workflows`
--

DROP TABLE IF EXISTS `ticket_workflows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_workflows` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `order` int(11) NOT NULL,
  `matcher` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `internal_notes` text COLLATE utf8_unicode_ci NOT NULL,
  `target` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_workflows`
--

LOCK TABLES `ticket_workflows` WRITE;
/*!40000 ALTER TABLE `ticket_workflows` DISABLE KEYS */;
INSERT INTO `ticket_workflows` VALUES (1,'Manual Activation',0,1,'any','2021-02-17 19:46:13','2021-04-19 09:06:50','',''),(2,'Field Support Escalation',0,2,'all','2021-02-22 10:31:23','2021-04-19 09:07:17','',''),(6,'Assign to City coordinator',0,5,'any','2021-05-21 10:25:55','2021-05-21 10:25:55','',''),(4,'VF Tiger Workflow',1,4,'any','2021-02-23 14:32:48','2021-02-24 09:29:11','',''),(5,'Assign Billing ticket  to Billing Team',1,5,'any','2021-02-23 17:56:34','2021-04-21 11:46:04','',''),(7,'If resolved assign back to team',0,6,'any','2021-05-21 10:27:54','2021-05-21 10:27:54','','');
/*!40000 ALTER TABLE `ticket_workflows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timezone`
--

DROP TABLE IF EXISTS `timezone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timezone` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=116 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timezone`
--

LOCK TABLES `timezone` WRITE;
/*!40000 ALTER TABLE `timezone` DISABLE KEYS */;
INSERT INTO `timezone` VALUES (1,'Pacific/Midway','(GMT-11:00) Midway Island'),(2,'US/Samoa','(GMT-11:00) Samoa'),(3,'US/Hawaii','(GMT-10:00) Hawaii'),(4,'US/Alaska','(GMT-09:00) Alaska'),(5,'US/Pacific','(GMT-08:00) Pacific Time (US &amp; Canada)'),(6,'America/Tijuana','(GMT-08:00) Tijuana'),(7,'US/Arizona','(GMT-07:00) Arizona'),(8,'US/Mountain','(GMT-07:00) Mountain Time (US &amp; Canada)'),(9,'America/Chihuahua','(GMT-07:00) Chihuahua'),(10,'America/Mazatlan','(GMT-07:00) Mazatlan'),(11,'America/Mexico_City','(GMT-06:00) Mexico City'),(12,'America/Monterrey','(GMT-06:00) Monterrey'),(13,'America/Santo_Domingo','(GMT-04:00) Santo_Domingo'),(14,'Canada/Saskatchewan','(GMT-06:00) Saskatchewan'),(15,'US/Central','(GMT-06:00) Central Time (US &amp; Canada)'),(16,'US/Eastern','(GMT-05:00) Eastern Time (US &amp; Canada)'),(17,'US/East-Indiana','(GMT-05:00) Indiana (East)'),(18,'America/Bogota','(GMT-05:00) Bogota'),(19,'America/Lima','(GMT-05:00) Lima'),(20,'America/Caracas','(GMT-04:30) Caracas'),(21,'Canada/Atlantic','(GMT-04:00) Atlantic Time (Canada)'),(22,'America/La_Paz','(GMT-04:00) La Paz'),(23,'America/Santiago','(GMT-04:00) Santiago'),(24,'Canada/Newfoundland','(GMT-03:30) Newfoundland'),(25,'America/Buenos_Aires','(GMT-03:00) Buenos Aires'),(26,'America/Godthab','(GMT-03:00) Greenland'),(27,'Atlantic/Stanley','(GMT-02:00) Stanley'),(28,'Atlantic/Azores','(GMT-01:00) Azores'),(29,'Atlantic/Cape_Verde','(GMT-01:00) Cape Verde Is.'),(30,'Africa/Casablanca','(GMT) Casablanca'),(31,'Europe/Dublin','(GMT) Dublin'),(32,'Europe/Lisbon','(GMT) Lisbon'),(33,'Europe/London','(GMT) London'),(34,'Africa/Monrovia','(GMT) Monrovia'),(35,'Europe/Amsterdam','(GMT+01:00) Amsterdam'),(36,'Europe/Belgrade','(GMT+01:00) Belgrade'),(37,'Europe/Berlin','(GMT+01:00) Berlin'),(38,'Europe/Bratislava','(GMT+01:00) Bratislava'),(39,'Europe/Brussels','(GMT+01:00) Brussels'),(40,'Europe/Budapest','(GMT+01:00) Budapest'),(41,'Europe/Copenhagen','(GMT+01:00) Copenhagen'),(42,'Europe/Ljubljana','(GMT+01:00) Ljubljana'),(43,'Europe/Madrid','(GMT+01:00) Madrid'),(44,'Europe/Paris','(GMT+01:00) Paris'),(45,'Europe/Prague','(GMT+01:00) Prague'),(46,'Europe/Rome','(GMT+01:00) Rome'),(47,'Europe/Sarajevo','(GMT+01:00) Sarajevo'),(48,'Europe/Skopje','(GMT+01:00) Skopje'),(49,'Europe/Stockholm','(GMT+01:00) Stockholm'),(50,'Europe/Vienna','(GMT+01:00) Vienna'),(51,'Europe/Warsaw','(GMT+01:00) Warsaw'),(52,'Europe/Zagreb','(GMT+01:00) Zagreb'),(53,'Europe/Athens','(GMT+02:00) Athens'),(54,'Europe/Bucharest','(GMT+02:00) Bucharest'),(55,'Africa/Cairo','(GMT+02:00) Cairo'),(56,'Africa/Harare','(GMT+02:00) Harare'),(57,'Europe/Helsinki','(GMT+02:00) Helsinki'),(58,'Europe/Istanbul','(GMT+02:00) Istanbul'),(59,'Asia/Jerusalem','(GMT+02:00) Jerusalem'),(60,'Europe/Kiev','(GMT+02:00) Kyiv'),(61,'Europe/Minsk','(GMT+02:00) Minsk'),(62,'Europe/Riga','(GMT+02:00) Riga'),(63,'Europe/Sofia','(GMT+02:00) Sofia'),(64,'Europe/Tallinn','(GMT+02:00) Tallinn'),(65,'Europe/Vilnius','(GMT+02:00) Vilnius'),(66,'Asia/Baghdad','(GMT+03:00) Baghdad'),(67,'Asia/Kuwait','(GMT+03:00) Kuwait'),(68,'Africa/Nairobi','(GMT+03:00) Nairobi'),(69,'Asia/Riyadh','(GMT+03:00) Riyadh'),(70,'Asia/Tehran','(GMT+03:30) Tehran'),(71,'Europe/Moscow','(GMT+04:00) Moscow'),(72,'Asia/Baku','(GMT+04:00) Baku'),(73,'Europe/Volgograd','(GMT+04:00) Volgograd'),(74,'Asia/Muscat','(GMT+04:00) Muscat'),(75,'Asia/Dubai','(GMT+04:00) Dubai'),(76,'Asia/Tbilisi','(GMT+04:00) Tbilisi'),(77,'Asia/Yerevan','(GMT+04:00) Yerevan'),(78,'Asia/Kabul','(GMT+04:30) Kabul'),(79,'Asia/Karachi','(GMT+05:00) Karachi'),(80,'Asia/Tashkent','(GMT+05:00) Tashkent'),(81,'Asia/Kolkata','(GMT+05:30) Kolkata'),(82,'Asia/Kathmandu','(GMT+05:45) Kathmandu'),(83,'Asia/Yekaterinburg','(GMT+06:00) Ekaterinburg'),(84,'Asia/Almaty','(GMT+06:00) Almaty'),(85,'Asia/Dhaka','(GMT+06:00) Dhaka'),(86,'Asia/Novosibirsk','(GMT+07:00) Novosibirsk'),(87,'Asia/Bangkok','(GMT+07:00) Bangkok'),(88,'Asia/Ho_Chi_Minh','(GMT+07.00) Ho Chi Minh'),(89,'Asia/Jakarta','(GMT+07:00) Jakarta'),(90,'Asia/Krasnoyarsk','(GMT+08:00) Krasnoyarsk'),(91,'Asia/Chongqing','(GMT+08:00) Chongqing'),(92,'Asia/Hong_Kong','(GMT+08:00) Hong Kong'),(93,'Asia/Kuala_Lumpur','(GMT+08:00) Kuala Lumpur'),(94,'Australia/Perth','(GMT+08:00) Perth'),(95,'Asia/Singapore','(GMT+08:00) Singapore'),(96,'Asia/Taipei','(GMT+08:00) Taipei'),(97,'Asia/Ulaanbaatar','(GMT+08:00) Ulaan Bataar'),(98,'Asia/Urumqi','(GMT+08:00) Urumqi'),(99,'Asia/Irkutsk','(GMT+09:00) Irkutsk'),(100,'Asia/Seoul','(GMT+09:00) Seoul'),(101,'Asia/Tokyo','(GMT+09:00) Tokyo'),(102,'Australia/Adelaide','(GMT+09:30) Adelaide'),(103,'Australia/Darwin','(GMT+09:30) Darwin'),(104,'Asia/Yakutsk','(GMT+10:00) Yakutsk'),(105,'Australia/Brisbane','(GMT+10:00) Brisbane'),(106,'Australia/Canberra','(GMT+10:00) Canberra'),(107,'Pacific/Guam','(GMT+10:00) Guam'),(108,'Australia/Hobart','(GMT+10:00) Hobart'),(109,'Australia/Melbourne','(GMT+10:00) Melbourne'),(110,'Pacific/Port_Moresby','(GMT+10:00) Port Moresby'),(111,'Australia/Sydney','(GMT+10:00) Sydney'),(112,'Asia/Vladivostok','(GMT+11:00) Vladivostok'),(113,'Asia/Magadan','(GMT+12:00) Magadan'),(114,'Pacific/Auckland','(GMT+12:00) Auckland'),(115,'Pacific/Fiji','(GMT+12:00) Fiji');
/*!40000 ALTER TABLE `timezone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_format`
--

DROP TABLE IF EXISTS `time_format`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `time_format` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `format` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_format`
--

LOCK TABLES `time_format` WRITE;
/*!40000 ALTER TABLE `time_format` DISABLE KEYS */;
INSERT INTO `time_format` VALUES (1,'H:i:s'),(2,'H.i.s');
/*!40000 ALTER TABLE `time_format` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_types`
--

DROP TABLE IF EXISTS `user_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_types`
--

LOCK TABLES `user_types` WRITE;
/*!40000 ALTER TABLE `user_types` DISABLE KEYS */;
INSERT INTO `user_types` VALUES (1,'User','user'),(2,'Agent','agent'),(3,'Admin','admin'),(4,'Department Manager','department_manager'),(5,'Team Lead','team_lead'),(6,'Organization Manager','organization_manager'),(7,'Organization Department Manager','organization_department_manager'),(8,'Assignee','assignee');
/*!40000 ALTER TABLE `user_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `version_check`
--

DROP TABLE IF EXISTS `version_check`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `version_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `current_version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `new_version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `version_check`
--

LOCK TABLES `version_check` WRITE;
/*!40000 ALTER TABLE `version_check` DISABLE KEYS */;
INSERT INTO `version_check` VALUES (1,'','','2021-02-17 05:35:47','2021-02-17 05:35:47');
/*!40000 ALTER TABLE `version_check` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `widgets`
--

DROP TABLE IF EXISTS `widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'social-icon',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `widgets`
--

LOCK TABLES `widgets` WRITE;
/*!40000 ALTER TABLE `widgets` DISABLE KEYS */;
INSERT INTO `widgets` VALUES (1,'footer1',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:50','footer'),(2,'footer2',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:50','footer'),(3,'footer3',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:50','footer'),(4,'footer4',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:50','footer'),(5,'linkedin',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:47','social-icon'),(6,'stumble',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:47','social-icon'),(7,'google',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:47','social-icon'),(8,'deviantart',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:47','social-icon'),(9,'flickr',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:47','social-icon'),(10,'skype',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:47','social-icon'),(11,'rss',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:47','social-icon'),(12,'twitter',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:47','social-icon'),(13,'facebook',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:47','social-icon'),(14,'youtube',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:47','social-icon'),(15,'vimeo',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:47','social-icon'),(16,'pinterest',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:47','social-icon'),(17,'dribbble',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:47','social-icon'),(18,'instagram',NULL,NULL,'2021-02-17 05:35:47','2021-02-17 05:35:47','social-icon');
/*!40000 ALTER TABLE `widgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workflow_close`
--

DROP TABLE IF EXISTS `workflow_close`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workflow_close` (
  `id` int(10) unsigned NOT NULL,
  `days` int(11) NOT NULL,
  `condition` int(11) NOT NULL,
  `send_email` int(11) NOT NULL,
  `status` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_close_status_foreign` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workflow_close`
--

LOCK TABLES `workflow_close` WRITE;
/*!40000 ALTER TABLE `workflow_close` DISABLE KEYS */;
INSERT INTO `workflow_close` VALUES (1,2,1,1,3,'2021-02-17 05:35:47','2021-02-17 05:35:47');
/*!40000 ALTER TABLE `workflow_close` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-06-24 11:25:19