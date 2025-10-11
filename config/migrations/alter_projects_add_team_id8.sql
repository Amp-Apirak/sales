-- ==============================================
-- Migration Script: Add team_id to projects table
-- Created: 2025-10-11
-- Purpose: Support explicit team ownership for projects
-- ==============================================

-- 1. Add team_id column to projects table
ALTER TABLE `projects`
ADD COLUMN `team_id` CHAR(36) NULL COMMENT 'ทีมที่รับผิดชอบโครงการ' AFTER `seller`;

-- 2. Add index for better query performance
ALTER TABLE `projects`
ADD INDEX `idx_team_id` (`team_id`);

-- 3. Add foreign key constraint (optional - uncomment if you want strict referential integrity)
-- ALTER TABLE `projects`
-- ADD CONSTRAINT `fk_projects_team_id`
-- FOREIGN KEY (`team_id`) REFERENCES `teams`(`team_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 4. Update existing projects to set team_id based on seller's primary team
UPDATE `projects` p
INNER JOIN `user_teams` ut ON p.seller = ut.user_id AND ut.is_primary = 1
SET p.team_id = ut.team_id
WHERE p.team_id IS NULL;

-- 5. For projects where seller has no primary team, use their first team
UPDATE `projects` p
INNER JOIN (
    SELECT user_id, MIN(team_id) as team_id
    FROM user_teams
    GROUP BY user_id
) ut ON p.seller = ut.user_id
SET p.team_id = ut.team_id
WHERE p.team_id IS NULL;

-- 6. Verify the migration
SELECT
    COUNT(*) as total_projects,
    COUNT(team_id) as projects_with_team,
    COUNT(*) - COUNT(team_id) as projects_without_team
FROM projects;

-- Expected result: projects_without_team should be 0 or very few
