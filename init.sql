-- Portfolio Database Initialization
-- Adjust credentials for production use
DROP DATABASE IF EXISTS `portfolio`;
CREATE DATABASE IF NOT EXISTS `portfolio` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `portfolio`;

-- Admins table
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(64) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Replace with secure hash in production
INSERT INTO `admins` (`username`, `password`) VALUES
('admin', 'admin');

-- Projects table
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(191) NOT NULL,
  `slug` VARCHAR(191) NOT NULL UNIQUE,
  `description` TEXT,
  `image` VARCHAR(255),
  `type` VARCHAR(64),
  `tags` VARCHAR(255),
  `github_url` VARCHAR(255),
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Project data from the portfolio
INSERT INTO `projects` (`title`, `slug`, `description`, `image`, `type`, `tags`, `github_url`) VALUES
('Bank Management System', 'bank-management-system', 'OOP in C++: simulated banking operations — accounts, transactions, reporting, and file persistence.', 'bank.svg', 'oop', 'C++,OOP', 'https://github.com/your-username/bank-management'),
('Digital Logic Clock', 'digital-logic-clock', 'DLD: A time clock with counter logic that intentionally skips 50 during minutes/seconds — implemented with combinational logic diagrams and simulation.', 'clock.svg', 'dld', 'DLD,Simulation', 'https://github.com/your-username/dld-clock'),
('CPU Design', 'cpu-design', 'Computer Architecture: datapath & control unit design — simulated instruction flow and pipeline basics.', 'cpu.svg', 'arch', 'Architecture,Verilog/Model', 'https://github.com/your-username/cpu-design'),
('Hotel Management System', 'hotel-management-system', 'Desktop app: reservation, billing, and inventory with GUI and data persistence for small hotels.', 'hotel.svg', 'desktop', 'Desktop,C++/GUI', 'https://github.com/your-username/hotel-management');

-- Skills table
CREATE TABLE IF NOT EXISTS `skills` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(128) NOT NULL,
  `category` VARCHAR(64) NOT NULL DEFAULT 'Other',
  `level` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Programming Languages
INSERT INTO `skills` (`name`, `category`, `level`, `sort_order`) VALUES
('C', 'Programming', 85, 1),
('C++', 'Programming', 78, 2),
('Python', 'Programming', 82, 3),
('Assembly', 'Programming', 56, 4),
('Java', 'Programming', 60, 5),
('HTML/CSS', 'Web Development', 88, 6),
('JavaScript', 'Web Development', 75, 7),
('SQL', 'Database', 64, 8),
('Linux', 'Tools', 70, 9),
('Git', 'Tools', 72, 10),
('PyTorch', 'Machine Learning', 50, 11),
('Node.js', 'Web Development', 50, 12),
('React', 'Web Development', 50, 13),
('REST APIs', 'Web Development', 50, 14);

-- Education table
CREATE TABLE IF NOT EXISTS `education` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `institution` VARCHAR(255) NOT NULL,
  `degree` VARCHAR(255),
  `start_year` YEAR,
  `end_year` YEAR,
  `description` TEXT,
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Education data froma portfolio
INSERT INTO `education` (`institution`, `degree`, `start_year`, `end_year`, `description`, `sort_order`) VALUES
('Khulna University of Engineering & Technology (KUET)', 'B.Sc. in Computer Science & Engineering', 2021, 2025, 'Currently 3rd Year, 1st Semester', 1),
('Chuadanga Government College', 'HSC (Science)', 2019, 2021, NULL, 2),
('Victoria Jubilee Government High School', 'SSC (Science)', 2011, 2019, NULL, 3);

-- Contact messages table
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `subject` VARCHAR(255),
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` BOOLEAN NOT NULL DEFAULT FALSE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Helpful notes:
-- 1) Generate admin password hash with PHP: php -r "echo password('YourPassHere', PASSWORD_DEFAULT);"
-- 2) Replace 'REPLACE_WITH_HASH' above with that hash before importing.
-- 3) After import, verify tables in phpMyAdmin and update `db.php` credentials as needed.

-- Optional: create a local MySQL user matching index.php credentials (local dev only)
-- Add more sample projects
INSERT INTO `projects` (`title`, `slug`, `description`, `image`, `type`, `tags`, `github_url`) VALUES
('Portfolio Website', 'portfolio-website', 'My personal portfolio website built with PHP.', 'portfolio.png', 'web', 'php,mysql,html,css', 'https://github.com/PrinceSiddiki/portfolio'),
('Mobile App', 'mobile-app', 'A cross-platform mobile application.', 'mobile-app.png', 'mobile', 'react-native,javascript', 'https://github.com/PrinceSiddiki/mobile-app'),
('E-commerce Site', 'ecommerce-site', 'Full-featured online store.', 'ecommerce.png', 'web', 'php,javascript,mysql', 'https://github.com/PrinceSiddiki/ecommerce');

-- Add more skills
INSERT INTO `skills` (`name`, `category`, `level`, `sort_order`) VALUES
('MySQL', 'Database', 75, 15),
('React', 'Web Development', 65, 16),
('Node.js', 'Web Development', 60, 17),
('Python', 'Programming', 82, 18);

-- Add more education entries
INSERT INTO `education` (`institution`,`degree`,`start_year`,`end_year`,`description`,`sort_order`) VALUES
('Online Academy','Web Development Certificate',2019,2020,'Completed full-stack web development course.',2),
('Tech Institute','Digital Marketing Diploma',2018,2019,'Studied digital marketing fundamentals.',3);

-- Add sample contact messages
INSERT INTO `contact_messages` (`name`,`email`,`subject`,`message`) VALUES
('John Doe','john@example.com','Project Inquiry','I would like to discuss a potential project.'),
('Jane Smith','jane@example.com','Job Opportunity','We have an opening that matches your skills.');

-- WARNING: using a weak password like '1234' is insecure. Use only for local development.
-- Run as root in MySQL shell or phpMyAdmin SQL tab (adjust as needed):
-- CREATE USER 'Prince'@'localhost' IDENTIFIED BY '1234';
-- GRANT ALL PRIVILEGES ON `portfolio`.* TO 'Prince'@'localhost';
-- FLUSH PRIVILEGES;
