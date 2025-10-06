-- ============================================
-- Project Discussion System
-- Migration: Add project discussion tables
-- Created: 2025-10-06
-- ============================================

-- Table 1: project_discussions (ข้อความสนทนา)
CREATE TABLE IF NOT EXISTS `project_discussions` (
    `discussion_id` CHAR(36) PRIMARY KEY,
    `project_id` CHAR(36) NOT NULL,
    `user_id` CHAR(36) NOT NULL,
    `message_text` TEXT,
    `is_edited` TINYINT(1) DEFAULT 0,
    `is_deleted` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (`project_id`) REFERENCES `projects`(`project_id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
    INDEX `idx_project_time` (`project_id`, `created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 2: project_discussion_attachments (ไฟล์แนบ)
CREATE TABLE IF NOT EXISTS `project_discussion_attachments` (
    `attachment_id` CHAR(36) PRIMARY KEY,
    `discussion_id` CHAR(36) NOT NULL,
    `project_id` CHAR(36) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_size` BIGINT,
    `file_type` VARCHAR(100),
    `file_extension` VARCHAR(10),
    `uploaded_by` CHAR(36) NOT NULL,
    `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (`discussion_id`) REFERENCES `project_discussions`(`discussion_id`) ON DELETE CASCADE,
    FOREIGN KEY (`project_id`) REFERENCES `projects`(`project_id`) ON DELETE CASCADE,
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`user_id`),
    INDEX `idx_discussion` (`discussion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 3: project_discussion_mentions (การแท็กผู้ใช้)
CREATE TABLE IF NOT EXISTS `project_discussion_mentions` (
    `mention_id` CHAR(36) PRIMARY KEY,
    `discussion_id` CHAR(36) NOT NULL,
    `mentioned_user_id` CHAR(36) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (`discussion_id`) REFERENCES `project_discussions`(`discussion_id`) ON DELETE CASCADE,
    FOREIGN KEY (`mentioned_user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
    INDEX `idx_user` (`mentioned_user_id`),
    INDEX `idx_discussion_user` (`discussion_id`, `mentioned_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- เสร็จสิ้น Migration
-- ============================================
