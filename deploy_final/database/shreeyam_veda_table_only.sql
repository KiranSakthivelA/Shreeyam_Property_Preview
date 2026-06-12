-- ============================================================
--  Shreeyam Veda — Table Setup (Shared Hosting / cPanel)
--
--  ⚠️  DO NOT include CREATE DATABASE here.
--      First create the database from cPanel > MySQL Databases,
--      then select that database in phpMyAdmin, then import this file.
--
--  HOW TO IMPORT (Shared Hosting)
--  --------------------------------
--  1. Login to cPanel → MySQL Databases → create DB + user
--  2. Open phpMyAdmin → click your database name (left sidebar)
--  3. Click "Import" tab → Choose this file → Go
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Table: submissions
-- Stores every lead captured from the website contact forms.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `submissions` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(255) NOT NULL                COMMENT 'Full name of the lead',
    `phone`      VARCHAR(20)  NOT NULL                COMMENT 'Primary contact number',
    `email`      VARCHAR(255) NOT NULL DEFAULT ''     COMMENT 'Email address (optional)',
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    PRIMARY KEY (`id`),
    INDEX `idx_phone`      (`phone`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Lead submissions from Shreeyam Veda website';

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  ✅ Done! Verify with:  SHOW TABLES;  SELECT * FROM submissions;
-- ============================================================
