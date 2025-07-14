--  A-pannel Installer Schema
-- Generated on 2025-07-13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table structure for table `share_stats`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `share_stats` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `shid` VARCHAR(255) NOT NULL,
  `path` VARCHAR(255) DEFAULT NULL,
  `oc` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `college_email` VARCHAR(225) NOT NULL,
  `college_email_status` TINYINT(1) NOT NULL,
  `email_status` TINYINT(1) DEFAULT 0,
  `plan` ENUM('basic','student','pro','ultra') NOT NULL DEFAULT 'basic',
  `password` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Optionally insert admin user (default login)
-- --------------------------------------------------------

-- INSERT INTO `users` (`email`, `username`, `college_email`, `college_email_status`, `plan`, `password`)
-- VALUES ('admin@example.com', 'admin', 'admin@college.edu', 1, 'ultra', '$2y$10$examplehash...');

COMMIT;
