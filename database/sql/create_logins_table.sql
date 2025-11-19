-- Logins table (track login attempts)
-- Records both successful and failed login attempts; user_id nullable for guest or failed attempts

USE `amadtech_ai`;

CREATE TABLE IF NOT EXISTS `logins` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `email` VARCHAR(255) NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `success` TINYINT(1) NOT NULL DEFAULT 0,
  `notes` TEXT NULL,
  `attempted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `logins_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX IF NOT EXISTS `logins_user_id_index` ON `logins` (`user_id`);
CREATE INDEX IF NOT EXISTS `logins_email_index` ON `logins` (`email`);

-- Notes for SQLite: use INTEGER PRIMARY KEY AUTOINCREMENT, and foreign keys require PRAGMA foreign_keys = ON
