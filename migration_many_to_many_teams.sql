-- SQL Migration Script for Many-to-Many User-Team Relationship
-- Version 1.0

-- Step 1: Create the new user_teams table to store the many-to-many relationship.
CREATE TABLE `user_teams` (
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `team_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`, `team_id`),
  KEY `user_teams_user_id_foreign` (`user_id`),
  KEY `user_teams_team_id_foreign` (`team_id`),
  CONSTRAINT `user_teams_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`) ON DELETE CASCADE,
  CONSTRAINT `user_teams_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: Populate the new user_teams table with existing data from the users table.
-- This script assumes the user's current team is their primary team.
INSERT INTO `user_teams` (user_id, team_id, is_primary)
SELECT user_id, team_id, 1
FROM `users`
WHERE team_id IS NOT NULL;

-- Step 3: Remove the old team_id column from the users table.
-- IMPORTANT: Before running this, you might need to manually drop the foreign key constraint.
-- You can find the constraint name using a command like: SHOW CREATE TABLE users;
-- Then run: ALTER TABLE `users` DROP FOREIGN KEY `your_foreign_key_name`;
ALTER TABLE `users` DROP COLUMN `team_id`;

