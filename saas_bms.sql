-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 06, 2026 at 12:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12
--
-- IDEMPOTENT VERSION - Safe to run multiple times

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `adalgorythm_bms`
--
-- Note: Database should already exist in production
-- If importing to a different database name, update this USE statement
USE `adalgorythm_bms`;

-- --------------------------------------------------------
-- Drop existing procedures before recreating
-- --------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_generate_renewal_notifications`;
DROP PROCEDURE IF EXISTS `sp_update_expired_services`;

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=CURRENT_USER PROCEDURE `sp_generate_renewal_notifications` ()
BEGIN
    -- Generate notifications for domains expiring in 30 days
    INSERT INTO notifications (user_id, type, title, message, related_type, related_id, priority)
    SELECT 
        1, -- Admin user
        'domain_expiry',
        CONCAT('Domain Expiring: ', d.domain_name),
        CONCAT('Domain ', d.domain_name, ' for client ', c.name, ' expires on ', DATE_FORMAT(d.expiry_date, '%Y-%m-%d'), ' (', DATEDIFF(d.expiry_date, CURDATE()), ' days left)'),
        'domain',
        d.id,
        CASE 
            WHEN DATEDIFF(d.expiry_date, CURDATE()) <= 7 THEN 'urgent'
            WHEN DATEDIFF(d.expiry_date, CURDATE()) <= 15 THEN 'high'
            ELSE 'medium'
        END
    FROM domains d
    INNER JOIN clients c ON d.client_id = c.id
    WHERE d.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        AND d.status = 'active'
        AND NOT EXISTS (
            SELECT 1 FROM notifications n 
            WHERE n.related_type = 'domain' 
                AND n.related_id = d.id 
                AND n.created_at >= CURDATE()
        );
    
    -- Generate notifications for hosting expiring in 30 days
    INSERT INTO notifications (user_id, type, title, message, related_type, related_id, priority)
    SELECT 
        1, -- Admin user
        'hosting_expiry',
        CONCAT('Hosting Expiring: ', h.server_name),
        CONCAT('Hosting ', h.plan_name, ' on ', h.server_name, ' for client ', c.name, ' expires on ', DATE_FORMAT(h.expiry_date, '%Y-%m-%d'), ' (', DATEDIFF(h.expiry_date, CURDATE()), ' days left)'),
        'hosting',
        h.id,
        CASE 
            WHEN DATEDIFF(h.expiry_date, CURDATE()) <= 7 THEN 'urgent'
            WHEN DATEDIFF(h.expiry_date, CURDATE()) <= 15 THEN 'high'
            ELSE 'medium'
        END
    FROM hosting h
    INNER JOIN clients c ON h.client_id = c.id
    WHERE h.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        AND h.status = 'active'
        AND NOT EXISTS (
            SELECT 1 FROM notifications n 
            WHERE n.related_type = 'hosting' 
                AND n.related_id = h.id 
                AND n.created_at >= CURDATE()
        );
END$$

CREATE DEFINER=CURRENT_USER PROCEDURE `sp_update_expired_services` ()
BEGIN
    -- Update expired domains
    UPDATE domains 
    SET status = 'expired' 
    WHERE expiry_date < CURDATE() AND status = 'active';
    
    -- Update expired hosting
    UPDATE hosting 
    SET status = 'expired' 
    WHERE expiry_date < CURDATE() AND status = 'active';
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL COMMENT 'create, update, delete, login, etc.',
  `module` varchar(50) NOT NULL COMMENT 'clients, domains, hosting, etc.',
  `record_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_module` (`module`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', 'auth', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:01:41'),
(2, 1, 'delete', 'clients', 3, 'Deleted client: Bob Johnson', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:05:05'),
(3, 1, 'delete', 'clients', 2, 'Deleted client: Jane Smith', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:05:09'),
(4, 1, 'delete', 'clients', 1, 'Deleted client: John Doe', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:05:11'),
(5, 1, 'create', 'clients', 4, 'Created client: Apple Garments', NULL, NULL, '2026-05-18 18:07:57'),
(6, 1, 'create', 'clients', 4, 'Created client: Apple Garments', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:07:57'),
(7, 1, 'update', 'clients', 4, 'Updated client: Fitha-Ul-Huq', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:12:34'),
(8, 1, 'create', 'clients', 5, 'Created client: ', NULL, NULL, '2026-05-18 18:14:28'),
(9, 1, 'create', 'clients', 5, 'Created client: ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:14:28'),
(10, 1, 'create', 'clients', 6, 'Created client: ', NULL, NULL, '2026-05-18 18:17:11'),
(11, 1, 'create', 'clients', 6, 'Created client: ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:17:11'),
(12, 1, 'create', 'clients', 7, 'Created client: ', NULL, NULL, '2026-05-18 18:17:41'),
(13, 1, 'create', 'clients', 7, 'Created client: ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:17:41'),
(14, 1, 'create', 'clients', 8, 'Created client: ', NULL, NULL, '2026-05-18 18:17:54'),
(15, 1, 'create', 'clients', 8, 'Created client: ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:17:54'),
(16, 1, 'create', 'clients', 9, 'Created client: Dharma', NULL, NULL, '2026-05-18 18:20:02'),
(17, 1, 'create', 'clients', 9, 'Created client: Dharma', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:20:02'),
(18, 1, 'update', 'clients', 5, 'Updated client: Abdul Kadhar', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:20:44'),
(19, 1, 'update', 'clients', 8, 'Updated client: Riyaz', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:21:44'),
(20, 1, 'update', 'clients', 7, 'Updated client: Fayaz', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:22:07'),
(21, 1, 'create', 'domains', 5, 'Created domain: mkprojects.algorythms.in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:32:02'),
(22, 1, 'login', 'auth', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 13:56:21'),
(23, 1, 'login', 'auth', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 06:19:39'),
(24, 1, 'login', 'auth', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-06 10:13:29'),
(25, 1, 'update', 'domains', 5, 'Updated domain: mkprojects.algorythms.in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-06 10:23:35'),
(26, 1, 'update', 'domains', 5, 'Updated domain: mkprojects.algorythms.in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-06 10:24:29'),
(27, 1, 'update', 'domains', 5, 'Updated domain: mkprojects.algorythms.in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-06 10:35:14'),
(28, 1, 'update', 'domains', 5, 'Updated domain: mkprojects.algorythms.in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-06 10:36:41')
ON DUPLICATE KEY UPDATE 
  `user_id` = VALUES(`user_id`),
  `action` = VALUES(`action`),
  `module` = VALUES(`module`),
  `record_id` = VALUES(`record_id`),
  `description` = VALUES(`description`),
  `ip_address` = VALUES(`ip_address`),
  `user_agent` = VALUES(`user_agent`),
  `created_at` = VALUES(`created_at`);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `phone`, `company_name`, `address`, `notes`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(4, 'Fitha-Ul-Huq', '', '9488567522', 'Apple Garments', 'Cumbum', '', 'active', 1, '2026-05-18 18:07:57', '2026-05-18 18:12:34'),
(5, 'Abdul Kadhar', '', '9500616646', 'Mk Projects', 'Chennai', '', 'active', 1, '2026-05-18 18:14:28', '2026-05-18 18:20:44'),
(6, '', '', '7373067976', 'JJ Powersolutions', 'Dindigul', '', 'active', 1, '2026-05-18 18:17:11', '2026-05-18 18:17:11'),
(7, 'Fayaz', '', '8778856675', 'Periyar Tex', 'Cumbum', '', 'active', 1, '2026-05-18 18:17:41', '2026-05-18 18:22:07'),
(8, 'Riyaz', '', '9443674461', 'Fashion Touch', 'Cumbum', '', 'active', 1, '2026-05-18 18:17:54', '2026-05-18 18:21:44'),
(9, 'Dharma', '', '9652076706', 'Swachatha Foods', 'Hyderabad', '', 'active', 1, '2026-05-18 18:20:02', '2026-05-18 18:20:02')
ON DUPLICATE KEY UPDATE 
  `name` = VALUES(`name`),
  `email` = VALUES(`email`),
  `phone` = VALUES(`phone`),
  `company_name` = VALUES(`company_name`),
  `address` = VALUES(`address`),
  `notes` = VALUES(`notes`),
  `status` = VALUES(`status`),
  `created_by` = VALUES(`created_by`),
  `updated_at` = VALUES(`updated_at`);

--
-- Triggers `clients`
--
DROP TRIGGER IF EXISTS `tr_client_after_insert`;
DELIMITER $$
CREATE TRIGGER `tr_client_after_insert` AFTER INSERT ON `clients` FOR EACH ROW BEGIN
    INSERT INTO activity_logs (user_id, action, module, record_id, description)
    VALUES (NEW.created_by, 'create', 'clients', NEW.id, CONCAT('Created client: ', NEW.name));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `domains`
--

CREATE TABLE IF NOT EXISTS `domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `domain_name` varchar(255) NOT NULL,
  `provider` varchar(100) NOT NULL COMMENT 'GoDaddy, Namecheap, etc.',
  `purchase_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `auto_renew` tinyint(1) DEFAULT 0,
  `status` enum('active','expired','pending') DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_domain_name` (`domain_name`),
  KEY `idx_expiry_date` (`expiry_date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `domains`
--

INSERT INTO `domains` (`id`, `client_id`, `domain_name`, `provider`, `purchase_date`, `expiry_date`, `cost`, `auto_renew`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(5, 5, 'mkprojects.algorythms.in', 'Algorythms Subdomain', '2024-10-01', '2026-06-10', 20000.00, 0, 'active', 'Annual Subscription', '2026-05-18 18:32:02', '2026-06-06 10:36:41')
ON DUPLICATE KEY UPDATE 
  `client_id` = VALUES(`client_id`),
  `domain_name` = VALUES(`domain_name`),
  `provider` = VALUES(`provider`),
  `purchase_date` = VALUES(`purchase_date`),
  `expiry_date` = VALUES(`expiry_date`),
  `cost` = VALUES(`cost`),
  `auto_renew` = VALUES(`auto_renew`),
  `status` = VALUES(`status`),
  `notes` = VALUES(`notes`),
  `updated_at` = VALUES(`updated_at`);

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE IF NOT EXISTS `email_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipient` varchar(255) NOT NULL,
  `subject` varchar(500) NOT NULL,
  `status` enum('sent','failed') NOT NULL DEFAULT 'sent',
  `sent_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_recipient` (`recipient`),
  KEY `idx_status` (`status`),
  KEY `idx_sent_at` (`sent_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_logs`
--

INSERT INTO `email_logs` (`id`, `recipient`, `subject`, `status`, `sent_at`) VALUES
(4, 'algorythms3141@gmail.com', 'Domain Expiry Alert: mkprojects.algorythms.in - 4 days left', 'sent', '2026-06-06 16:17:47'),
(5, 'joshuajeberson@gmail.com', 'Domain Expiry Alert: mkprojects.algorythms.in - 4 days left', 'sent', '2026-06-06 16:17:47'),
(6, 'sadiqsariq55@gmail.com', 'Domain Expiry Alert: mkprojects.algorythms.in - 4 days left', 'sent', '2026-06-06 16:17:47')
ON DUPLICATE KEY UPDATE 
  `recipient` = VALUES(`recipient`),
  `subject` = VALUES(`subject`),
  `status` = VALUES(`status`),
  `sent_at` = VALUES(`sent_at`);

-- --------------------------------------------------------

--
-- Table structure for table `hosting`
--

CREATE TABLE IF NOT EXISTS `hosting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `domain_id` int(11) DEFAULT NULL,
  `server_name` varchar(100) NOT NULL,
  `plan_name` varchar(100) NOT NULL COMMENT 'Basic, Premium, Enterprise, etc.',
  `start_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `disk_space` varchar(50) DEFAULT NULL COMMENT 'e.g., 10GB, Unlimited',
  `bandwidth` varchar(50) DEFAULT NULL COMMENT 'e.g., 100GB, Unlimited',
  `auto_renew` tinyint(1) DEFAULT 0,
  `status` enum('active','expired','suspended') DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_domain_id` (`domain_id`),
  KEY `idx_expiry_date` (`expiry_date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `type` enum('domain_expiry','hosting_expiry','payment_due','system') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `related_type` enum('client','domain','hosting','payment') DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_type` (`type`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `related_type`, `related_id`, `is_read`, `priority`, `created_at`) VALUES
(1, NULL, 'domain_expiry', 'Domain Expiring: smithsolutions.com', 'Domain smithsolutions.com for client Jane Smith expires on 10 Jun 2026 (23 days left)', 'domain', 3, 0, 'medium', '2026-05-18 18:03:14'),
(2, NULL, 'hosting_expiry', 'Hosting Expiring: Server-EU-02', 'Hosting Business Plan on Server-EU-02 for client Jane Smith expires on 10 Jun 2026 (23 days left)', 'hosting', 2, 0, 'medium', '2026-05-18 18:03:14'),
(3, NULL, 'domain_expiry', 'Domain Expiring: mkprojects.algorythms.in', 'Domain mkprojects.algorythms.in for client Abdul Kadhar expires on 10 Jun 2026 (4 days left)', 'domain', 5, 0, 'urgent', '2026-06-06 10:36:42')
ON DUPLICATE KEY UPDATE 
  `user_id` = VALUES(`user_id`),
  `type` = VALUES(`type`),
  `title` = VALUES(`title`),
  `message` = VALUES(`message`),
  `related_type` = VALUES(`related_type`),
  `related_id` = VALUES(`related_id`),
  `is_read` = VALUES(`is_read`),
  `priority` = VALUES(`priority`);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `service_type` enum('domain','hosting','other') NOT NULL,
  `service_id` int(11) DEFAULT NULL COMMENT 'Reference to domain_id or hosting_id',
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'Bank Transfer, PayPal, Credit Card, etc.',
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('paid','pending','failed','refunded') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_service_type` (`service_type`),
  KEY `idx_payment_date` (`payment_date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@saas-bms.local', '$2y$10$ee9iyfC5TrRaxCirKfKtm.915Gimpb1kYrvNV/xwCPP7vKHl.raLa', 'System Administrator', 'admin', 'active', '2026-05-18 17:42:52', '2026-06-06 10:13:29')
ON DUPLICATE KEY UPDATE 
  `username` = VALUES(`username`),
  `email` = VALUES(`email`),
  `password` = VALUES(`password`),
  `full_name` = VALUES(`full_name`),
  `role` = VALUES(`role`),
  `status` = VALUES(`status`),
  `updated_at` = VALUES(`updated_at`);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_client_summary`
-- (See below for the actual view)
--
CREATE TABLE IF NOT EXISTS `v_client_summary` (
`id` int(11)
,`name` varchar(100)
,`email` varchar(100)
,`company_name` varchar(150)
,`total_domains` bigint(21)
,`total_hosting` bigint(21)
,`total_paid` decimal(32,2)
,`status` enum('active','inactive')
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_expiring_domains`
-- (See below for the actual view)
--
CREATE TABLE IF NOT EXISTS `v_expiring_domains` (
`id` int(11)
,`domain_name` varchar(255)
,`client_name` varchar(100)
,`client_email` varchar(100)
,`expiry_date` date
,`days_left` int(7)
,`cost` decimal(10,2)
,`provider` varchar(100)
,`status` enum('active','expired','pending')
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_expiring_hosting`
-- (See below for the actual view)
--
CREATE TABLE IF NOT EXISTS `v_expiring_hosting` (
`id` int(11)
,`server_name` varchar(100)
,`plan_name` varchar(100)
,`client_name` varchar(100)
,`client_email` varchar(100)
,`domain_name` varchar(255)
,`expiry_date` date
,`days_left` int(7)
,`cost` decimal(10,2)
,`status` enum('active','expired','suspended')
);

-- --------------------------------------------------------

--
-- Structure for view `v_client_summary`
--
DROP TABLE IF EXISTS `v_client_summary`;
DROP VIEW IF EXISTS `v_client_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `v_client_summary`  AS SELECT `c`.`id` AS `id`, `c`.`name` AS `name`, `c`.`email` AS `email`, `c`.`company_name` AS `company_name`, count(distinct `d`.`id`) AS `total_domains`, count(distinct `h`.`id`) AS `total_hosting`, coalesce(sum(`p`.`amount`),0) AS `total_paid`, `c`.`status` AS `status` FROM (((`clients` `c` left join `domains` `d` on(`c`.`id` = `d`.`client_id`)) left join `hosting` `h` on(`c`.`id` = `h`.`client_id`)) left join `payments` `p` on(`c`.`id` = `p`.`client_id` and `p`.`status` = 'paid')) GROUP BY `c`.`id`, `c`.`name`, `c`.`email`, `c`.`company_name`, `c`.`status` ;

-- --------------------------------------------------------

--
-- Structure for view `v_expiring_domains`
--
DROP TABLE IF EXISTS `v_expiring_domains`;
DROP VIEW IF EXISTS `v_expiring_domains`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `v_expiring_domains`  AS SELECT `d`.`id` AS `id`, `d`.`domain_name` AS `domain_name`, `c`.`name` AS `client_name`, `c`.`email` AS `client_email`, `d`.`expiry_date` AS `expiry_date`, to_days(`d`.`expiry_date`) - to_days(curdate()) AS `days_left`, `d`.`cost` AS `cost`, `d`.`provider` AS `provider`, `d`.`status` AS `status` FROM (`domains` `d` join `clients` `c` on(`d`.`client_id` = `c`.`id`)) WHERE `d`.`expiry_date` <= curdate() + interval 30 day AND `d`.`status` = 'active' ORDER BY `d`.`expiry_date` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_expiring_hosting`
--
DROP TABLE IF EXISTS `v_expiring_hosting`;
DROP VIEW IF EXISTS `v_expiring_hosting`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY INVOKER VIEW `v_expiring_hosting`  AS SELECT `h`.`id` AS `id`, `h`.`server_name` AS `server_name`, `h`.`plan_name` AS `plan_name`, `c`.`name` AS `client_name`, `c`.`email` AS `client_email`, `d`.`domain_name` AS `domain_name`, `h`.`expiry_date` AS `expiry_date`, to_days(`h`.`expiry_date`) - to_days(curdate()) AS `days_left`, `h`.`cost` AS `cost`, `h`.`status` AS `status` FROM ((`hosting` `h` join `clients` `c` on(`h`.`client_id` = `c`.`id`)) left join `domains` `d` on(`h`.`domain_id` = `d`.`id`)) WHERE `h`.`expiry_date` <= curdate() + interval 30 day AND `h`.`status` = 'active' ORDER BY `h`.`expiry_date` ASC ;

-- --------------------------------------------------------

--
-- Add Foreign Key Constraints (only if they don't exist)
--

-- Check and add constraint for activity_logs
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'activity_logs' 
    AND CONSTRAINT_NAME = 'activity_logs_ibfk_1');

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE `activity_logs` ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL',
    'SELECT "Constraint activity_logs_ibfk_1 already exists"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add constraint for clients
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'clients' 
    AND CONSTRAINT_NAME = 'clients_ibfk_1');

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE `clients` ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL',
    'SELECT "Constraint clients_ibfk_1 already exists"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add constraint for domains
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'domains' 
    AND CONSTRAINT_NAME = 'domains_ibfk_1');

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE `domains` ADD CONSTRAINT `domains_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE',
    'SELECT "Constraint domains_ibfk_1 already exists"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add constraints for hosting
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'hosting' 
    AND CONSTRAINT_NAME = 'hosting_ibfk_1');

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE `hosting` ADD CONSTRAINT `hosting_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE',
    'SELECT "Constraint hosting_ibfk_1 already exists"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'hosting' 
    AND CONSTRAINT_NAME = 'hosting_ibfk_2');

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE `hosting` ADD CONSTRAINT `hosting_ibfk_2` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE SET NULL',
    'SELECT "Constraint hosting_ibfk_2 already exists"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add constraint for notifications
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'notifications' 
    AND CONSTRAINT_NAME = 'notifications_ibfk_1');

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE `notifications` ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
    'SELECT "Constraint notifications_ibfk_1 already exists"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add constraint for payments
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'payments' 
    AND CONSTRAINT_NAME = 'payments_ibfk_1');

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE `payments` ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE',
    'SELECT "Constraint payments_ibfk_1 already exists"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Made with Bob
