-- ============================================================
--  Shreeyam Veda — MySQL Database Setup Script
--  Engine : MySQLi / InnoDB
--  Charset: utf8mb4 (full Unicode + emoji support)
--
--  HOW TO IMPORT
--  -------------
--  Option A — phpMyAdmin:
--    1. Open phpMyAdmin
--    2. Click "Import" tab
--    3. Choose this file → click "Go"
--
--  Option B — MySQL CLI:
--    mysql -u root -p < shreeyam_veda.sql
--
--  After import, update api/config.php with your host/user/pass.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ------------------------------------------------------------
-- 1. Create & select the database
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `shreeyam_veda`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `shreeyam_veda`;

-- ------------------------------------------------------------
-- 2. Table: submissions
--    Stores every lead captured from the website contact forms.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `submissions` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(255)    NOT NULL                 COMMENT 'Full name of the lead',
    `phone`      VARCHAR(20)     NOT NULL                 COMMENT 'Primary contact number',
    `email`      VARCHAR(255)    NOT NULL DEFAULT ''      COMMENT 'Email address (optional)',
    `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    PRIMARY KEY (`id`),
    INDEX `idx_phone`      (`phone`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Lead submissions from Shreeyam Veda website';

-- ------------------------------------------------------------
-- 3. (Optional) Sample test record — DELETE before going live
-- ------------------------------------------------------------
-- INSERT INTO `submissions` (`name`, `phone`, `email`) VALUES
-- ('Test User', '9999999999', 'test@example.com');

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  END OF FILE
--  Once imported, verify with:  SHOW TABLES;  SELECT * FROM submissions;
-- ============================================================
