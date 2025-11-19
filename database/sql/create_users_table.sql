-- Users table (for registrations)
-- MySQL syntax; adapt types if you use SQLite (see notes below)

USE `amadtech_ai`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `email_verified_at` DATETIME NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes can be added for lookups
CREATE INDEX IF NOT EXISTS `users_email_index` ON `users` (`email`);

-- Notes for SQLite: replace AUTO_INCREMENT with INTEGER PRIMARY KEY AUTOINCREMENT
-- and use TEXT for strings, DATETIME can be stored as TEXT.
