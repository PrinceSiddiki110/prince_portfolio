-- Enhanced Authentication Database Tables
-- Run this SQL to create the necessary tables for the authentication system

-- Table for storing remember me tokens
CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `admin_id` (`admin_id`),
  KEY `expires_at` (`expires_at`),
  CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for storing session data (optional, for database session storage)
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(128) NOT NULL,
  `data` longtext NOT NULL,
  `expires` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `expires` (`expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for login attempts tracking (security feature)
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(64) NOT NULL,
  `attempted_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `success` tinyint(1) DEFAULT 0,
  `user_agent` text,
  PRIMARY KEY (`id`),
  KEY `ip_address` (`ip_address`),
  KEY `username` (`username`),
  KEY `attempted_at` (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for admin activity logs
CREATE TABLE IF NOT EXISTS `admin_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(128) NOT NULL,
  `details` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `admin_activity_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample admin if not exists (change password in production!)
INSERT IGNORE INTO `admins` (`username`, `password`) VALUES ('admin', 'admin123');

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_remember_tokens_admin_expires` ON `remember_tokens` (`admin_id`, `expires_at`);
CREATE INDEX IF NOT EXISTS `idx_login_attempts_ip_time` ON `login_attempts` (`ip_address`, `attempted_at`);
CREATE INDEX IF NOT EXISTS `idx_admin_activity_admin_time` ON `admin_activity_logs` (`admin_id`, `created_at`);
