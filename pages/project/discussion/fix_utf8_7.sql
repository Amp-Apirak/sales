-- Fix UTF-8 encoding for discussion tables
-- Run this SQL to ensure emoji support

-- Fix project_discussions table
ALTER TABLE project_discussions
  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE project_discussions
  MODIFY message_text TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Fix project_discussion_attachments table
ALTER TABLE project_discussion_attachments
  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE project_discussion_attachments
  MODIFY file_name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Verify changes
SELECT
  TABLE_NAME,
  COLUMN_NAME,
  CHARACTER_SET_NAME,
  COLLATION_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'sales_db'
  AND TABLE_NAME IN ('project_discussions', 'project_discussion_attachments')
  AND CHARACTER_SET_NAME IS NOT NULL;
