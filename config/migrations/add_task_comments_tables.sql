-- ============================================
-- Migration: เพิ่มตาราง Task Comments และ Attachments
-- วันที่: 2025-10-02
-- จุดประสงค์: สร้างระบบ Activity Log แบบกระดานแชทสำหรับ Task
-- ============================================

-- ตาราง task_comments: เก็บความคิดเห็น/Log ของ Task
CREATE TABLE IF NOT EXISTS `task_comments` (
  `comment_id` char(36) NOT NULL COMMENT 'รหัสความคิดเห็น (UUID)',
  `task_id` char(36) NOT NULL COMMENT 'รหัสงาน (FK -> project_tasks)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (FK -> projects)',
  `user_id` char(36) NOT NULL COMMENT 'รหัสผู้แสดงความคิดเห็น (FK -> users)',
  `comment_text` text NOT NULL COMMENT 'ข้อความความคิดเห็น',
  `comment_type` enum('comment', 'status_change', 'file_upload', 'progress_update', 'system_log') DEFAULT 'comment' COMMENT 'ประเภทของ Log',
  `old_value` varchar(255) DEFAULT NULL COMMENT 'ค่าเดิม (สำหรับ Log การเปลี่ยนแปลง)',
  `new_value` varchar(255) DEFAULT NULL COMMENT 'ค่าใหม่ (สำหรับ Log การเปลี่ยนแปลง)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่โพสต์',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไข',
  `is_edited` tinyint(1) DEFAULT 0 COMMENT 'มีการแก้ไขหรือไม่',
  `is_deleted` tinyint(1) DEFAULT 0 COMMENT 'ถูกลบหรือไม่',
  PRIMARY KEY (`comment_id`),
  KEY `idx_task_id` (`task_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_task_comments_task` FOREIGN KEY (`task_id`) REFERENCES `project_tasks` (`task_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_task_comments_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_task_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตาราง Activity Log/Comments ของ Task';

-- ตาราง task_comment_attachments: เก็บไฟล์แนบในความคิดเห็น
CREATE TABLE IF NOT EXISTS `task_comment_attachments` (
  `attachment_id` char(36) NOT NULL COMMENT 'รหัสไฟล์แนบ (UUID)',
  `comment_id` char(36) NOT NULL COMMENT 'รหัสความคิดเห็น (FK -> task_comments)',
  `task_id` char(36) NOT NULL COMMENT 'รหัสงาน (FK -> project_tasks)',
  `file_name` varchar(255) NOT NULL COMMENT 'ชื่อไฟล์ต้นฉบับ',
  `file_path` varchar(500) NOT NULL COMMENT 'path ของไฟล์ในระบบ',
  `file_size` bigint(20) DEFAULT NULL COMMENT 'ขนาดไฟล์ (bytes)',
  `file_type` varchar(100) DEFAULT NULL COMMENT 'ประเภทไฟล์ (MIME type)',
  `file_extension` varchar(10) DEFAULT NULL COMMENT 'นามสกุลไฟล์',
  `uploaded_by` char(36) NOT NULL COMMENT 'ผู้อัปโหลด (FK -> users)',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่อัปโหลด',
  PRIMARY KEY (`attachment_id`),
  KEY `idx_comment_id` (`comment_id`),
  KEY `idx_task_id` (`task_id`),
  KEY `idx_uploaded_by` (`uploaded_by`),
  CONSTRAINT `fk_task_attachments_comment` FOREIGN KEY (`comment_id`) REFERENCES `task_comments` (`comment_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_task_attachments_task` FOREIGN KEY (`task_id`) REFERENCES `project_tasks` (`task_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_task_attachments_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางไฟล์แนบใน Task Comments';

-- ตาราง task_mentions: เก็บการ @ mention ผู้ใช้
CREATE TABLE IF NOT EXISTS `task_mentions` (
  `mention_id` char(36) NOT NULL COMMENT 'รหัส mention (UUID)',
  `comment_id` char(36) NOT NULL COMMENT 'รหัสความคิดเห็น (FK -> task_comments)',
  `task_id` char(36) NOT NULL COMMENT 'รหัสงาน (FK -> project_tasks)',
  `mentioned_user_id` char(36) NOT NULL COMMENT 'ผู้ถูก mention (FK -> users)',
  `mentioned_by` char(36) NOT NULL COMMENT 'ผู้ mention (FK -> users)',
  `is_read` tinyint(1) DEFAULT 0 COMMENT 'อ่านแล้วหรือยัง',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่ mention',
  PRIMARY KEY (`mention_id`),
  KEY `idx_comment_id` (`comment_id`),
  KEY `idx_task_id` (`task_id`),
  KEY `idx_mentioned_user` (`mentioned_user_id`),
  KEY `idx_is_read` (`is_read`),
  CONSTRAINT `fk_task_mentions_comment` FOREIGN KEY (`comment_id`) REFERENCES `task_comments` (`comment_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_task_mentions_task` FOREIGN KEY (`task_id`) REFERENCES `project_tasks` (`task_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_task_mentions_user` FOREIGN KEY (`mentioned_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บการ @ mention ใน Task Comments';

-- Index เพิ่มเติมสำหรับ Performance
CREATE INDEX idx_comment_type ON task_comments(comment_type);
CREATE INDEX idx_is_deleted ON task_comments(is_deleted);
CREATE INDEX idx_file_type ON task_comment_attachments(file_type);

-- View สำหรับดึงข้อมูล Comments พร้อม User และ Attachments
CREATE OR REPLACE VIEW vw_task_comments AS
SELECT
    tc.comment_id,
    tc.task_id,
    tc.project_id,
    tc.user_id,
    tc.comment_text,
    tc.comment_type,
    tc.old_value,
    tc.new_value,
    tc.created_at,
    tc.updated_at,
    tc.is_edited,
    tc.is_deleted,
    -- User info
    u.first_name,
    u.last_name,
    CONCAT(u.first_name, ' ', u.last_name) as user_full_name,
    u.email as user_email,
    -- Attachment count
    (SELECT COUNT(*) FROM task_comment_attachments WHERE comment_id = tc.comment_id) as attachment_count
FROM task_comments tc
LEFT JOIN users u ON tc.user_id = u.user_id
WHERE tc.is_deleted = 0
ORDER BY tc.created_at ASC;
