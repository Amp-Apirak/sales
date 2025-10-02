-- ============================================================================
-- Service Tickets Database Schema
-- Created: 2025-10-02
-- Description: โครงสร้างฐานข้อมูลสำหรับระบบ Service Ticket Management
-- ============================================================================

-- ============================================================================
-- ตาราง 1: service_tickets (ตารางหลัก)
-- ============================================================================
CREATE TABLE `service_tickets` (
  `ticket_id` CHAR(36) NOT NULL COMMENT 'รหัส Ticket (UUID)',
  `ticket_no` VARCHAR(50) UNIQUE NOT NULL COMMENT 'เลข Ticket (เช่น TCK-202510-0001)',

  -- ข้อมูลพื้นฐาน
  `project_id` CHAR(36) NOT NULL COMMENT 'รหัสโครงการ',
  `ticket_type` ENUM('Incident', 'Service', 'Change') NOT NULL DEFAULT 'Incident' COMMENT 'ประเภท Ticket',
  `subject` VARCHAR(150) NOT NULL COMMENT 'หัวข้อ',
  `description` TEXT COMMENT 'รายละเอียด/อาการ',

  -- สถานะและความสำคัญ
  `status` ENUM('Draft','New','On Process','Pending','Waiting for Approval','Scheduled','Resolved','Resolved Pending','Containment','Closed','Canceled') NOT NULL DEFAULT 'New' COMMENT 'สถานะ',
  `priority` ENUM('Critical','High','Medium','Low') NOT NULL DEFAULT 'Low' COMMENT 'ความสำคัญ',
  `urgency` ENUM('High','Medium','Low') NOT NULL DEFAULT 'Low' COMMENT 'ความเร่งด่วน',
  `impact` VARCHAR(100) COMMENT 'ผลกระทบ',

  -- หมวดหมู่
  `service_category` VARCHAR(255) COMMENT 'หมวดหมู่บริการ',
  `category` VARCHAR(255) COMMENT 'หมวดหมู่',
  `sub_category` VARCHAR(255) COMMENT 'หมวดหมู่ย่อย',

  -- ผู้เกี่ยวข้อง
  `job_owner` CHAR(36) COMMENT 'รหัสผู้รับผิดชอบ',
  `reporter` CHAR(36) COMMENT 'รหัสผู้แจ้ง',
  `source` VARCHAR(100) COMMENT 'ช่องทางแจ้ง (Email, Call Center, Portal, etc.)',

  -- เวลาและ SLA
  `sla_target` INT COMMENT 'SLA เป้าหมาย (ชั่วโมง)',
  `sla_deadline` DATETIME COMMENT 'วันเวลาครบ SLA (คำนวณอัตโนมัติ)',
  `sla_status` ENUM('Within SLA','Near SLA','Overdue') DEFAULT 'Within SLA' COMMENT 'สถานะ SLA',
  `start_at` DATETIME COMMENT 'วันเวลาเริ่มดำเนินการ',
  `due_at` DATETIME COMMENT 'วันเวลากำหนดเสร็จ',
  `resolved_at` DATETIME COMMENT 'วันเวลาแก้ไขเสร็จ',
  `closed_at` DATETIME COMMENT 'วันเวลาปิด Ticket',

  -- ช่องทางการทำงาน
  `channel` ENUM('Onsite','Remote','Office') COMMENT 'ช่องทางการทำงาน',

  -- Soft Delete
  `deleted_at` DATETIME COMMENT 'วันเวลาลบ (Soft Delete)',

  -- Audit
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'วันเวลาสร้าง',
  `created_by` CHAR(36) NOT NULL COMMENT 'ผู้สร้าง',
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันเวลาอัปเดตล่าสุด',
  `updated_by` CHAR(36) COMMENT 'ผู้อัปเดตล่าสุด',

  -- Primary Key
  PRIMARY KEY (`ticket_id`),

  -- Indexes
  INDEX idx_ticket_no (`ticket_no`),
  INDEX idx_status (`status`),
  INDEX idx_priority (`priority`),
  INDEX idx_job_owner (`job_owner`),
  INDEX idx_created_at (`created_at`),
  INDEX idx_sla_status (`sla_status`),
  INDEX idx_deleted_at (`deleted_at`),

  -- Foreign Keys
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`project_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`job_owner`) REFERENCES `users`(`user_id`) ON DELETE SET NULL,
  FOREIGN KEY (`reporter`) REFERENCES `users`(`user_id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บข้อมูล Service Ticket หลัก';

-- ============================================================================
-- ตาราง 2: service_ticket_onsite (ข้อมูล Onsite)
-- ============================================================================
CREATE TABLE `service_ticket_onsite` (
  `onsite_id` CHAR(36) NOT NULL COMMENT 'รหัส Onsite (UUID)',
  `ticket_id` CHAR(36) NOT NULL COMMENT 'รหัส Ticket',

  -- ข้อมูลสถานที่
  `start_location` VARCHAR(255) COMMENT 'สถานที่เริ่มต้น',
  `end_location` VARCHAR(255) COMMENT 'สถานที่ปลายทาง',

  -- ข้อมูลการเดินทาง
  `travel_mode` VARCHAR(100) COMMENT 'วิธีการเดินทาง (รถส่วนตัว, รถบริษัท, etc.)',
  `travel_note` VARCHAR(255) COMMENT 'หมายเหตุพาหนะ',

  -- ข้อมูลเลขไมล์
  `odometer_start` DECIMAL(10,2) COMMENT 'เลขไมล์เริ่มต้น',
  `odometer_end` DECIMAL(10,2) COMMENT 'เลขไมล์สิ้นสุด',
  `distance` DECIMAL(10,2) GENERATED ALWAYS AS (odometer_end - odometer_start) STORED COMMENT 'ระยะทาง (คำนวณอัตโนมัติ)',

  -- หมายเหตุ
  `note` TEXT COMMENT 'หมายเหตุเพิ่มเติม',

  -- Audit
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'วันเวลาสร้าง',
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันเวลาอัปเดต',

  -- Primary Key
  PRIMARY KEY (`onsite_id`),

  -- Indexes
  INDEX idx_ticket_id (`ticket_id`),

  -- Foreign Keys
  FOREIGN KEY (`ticket_id`) REFERENCES `service_tickets`(`ticket_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บข้อมูล Onsite Details';

-- ============================================================================
-- ตาราง 3: service_ticket_attachments (ไฟล์แนบ)
-- ============================================================================
CREATE TABLE `service_ticket_attachments` (
  `attachment_id` CHAR(36) NOT NULL COMMENT 'รหัสไฟล์แนบ (UUID)',
  `ticket_id` CHAR(36) NOT NULL COMMENT 'รหัส Ticket',

  -- ข้อมูลไฟล์
  `file_name` VARCHAR(255) NOT NULL COMMENT 'ชื่อไฟล์',
  `file_path` VARCHAR(500) NOT NULL COMMENT 'ที่อยู่ไฟล์',
  `file_size` BIGINT COMMENT 'ขนาดไฟล์ (bytes)',
  `file_type` VARCHAR(50) COMMENT 'ประเภทไฟล์ (jpg, pdf, docx, etc.)',
  `mime_type` VARCHAR(100) COMMENT 'MIME Type',

  -- Audit
  `uploaded_by` CHAR(36) NOT NULL COMMENT 'ผู้อัปโหลด',
  `uploaded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'วันเวลาอัปโหลด',

  -- Primary Key
  PRIMARY KEY (`attachment_id`),

  -- Indexes
  INDEX idx_ticket_id (`ticket_id`),
  INDEX idx_uploaded_by (`uploaded_by`),

  -- Foreign Keys
  FOREIGN KEY (`ticket_id`) REFERENCES `service_tickets`(`ticket_id`) ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บไฟล์แนบของ Ticket';

-- ============================================================================
-- ตาราง 4: service_ticket_watchers (ผู้ติดตาม/Watcher)
-- ============================================================================
CREATE TABLE `service_ticket_watchers` (
  `watcher_id` CHAR(36) NOT NULL COMMENT 'รหัส Watcher (UUID)',
  `ticket_id` CHAR(36) NOT NULL COMMENT 'รหัส Ticket',
  `user_id` CHAR(36) NOT NULL COMMENT 'รหัสผู้ติดตาม',

  -- Audit
  `added_by` CHAR(36) COMMENT 'ผู้เพิ่ม',
  `added_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'วันเวลาเพิ่ม',

  -- Primary Key
  PRIMARY KEY (`watcher_id`),

  -- Indexes
  INDEX idx_ticket_id (`ticket_id`),
  INDEX idx_user_id (`user_id`),

  -- Unique Constraint
  UNIQUE KEY `unique_watcher` (`ticket_id`, `user_id`),

  -- Foreign Keys
  FOREIGN KEY (`ticket_id`) REFERENCES `service_tickets`(`ticket_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`added_by`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บผู้ติดตาม Ticket';

-- ============================================================================
-- ตาราง 5: service_ticket_timeline (Timeline/ประวัติ)
-- ============================================================================
CREATE TABLE `service_ticket_timeline` (
  `timeline_id` CHAR(36) NOT NULL COMMENT 'รหัส Timeline (UUID)',
  `ticket_id` CHAR(36) NOT NULL COMMENT 'รหัส Ticket',

  -- ข้อมูล Timeline
  `order` INT NOT NULL COMMENT 'ลำดับ',
  `actor` VARCHAR(255) NOT NULL COMMENT 'ผู้ดำเนินการ',
  `action` VARCHAR(500) NOT NULL COMMENT 'การกระทำ',
  `detail` TEXT COMMENT 'รายละเอียด',
  `attachment` VARCHAR(255) COMMENT 'ไฟล์แนบ',
  `location` VARCHAR(255) COMMENT 'สถานที่/ช่องทาง',

  -- Audit
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'วันเวลาสร้าง',

  -- Primary Key
  PRIMARY KEY (`timeline_id`),

  -- Indexes
  INDEX idx_ticket_order (`ticket_id`, `order`),
  INDEX idx_created_at (`created_at`),

  -- Foreign Keys
  FOREIGN KEY (`ticket_id`) REFERENCES `service_tickets`(`ticket_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บ Timeline/ประวัติการดำเนินการ';

-- ============================================================================
-- ตาราง 6: service_ticket_history (บันทึกการเปลี่ยนแปลง)
-- ============================================================================
CREATE TABLE `service_ticket_history` (
  `history_id` CHAR(36) NOT NULL COMMENT 'รหัสประวัติ (UUID)',
  `ticket_id` CHAR(36) NOT NULL COMMENT 'รหัส Ticket',

  -- ข้อมูลการเปลี่ยนแปลง
  `field_name` VARCHAR(100) NOT NULL COMMENT 'ชื่อฟิลด์ที่เปลี่ยน',
  `old_value` TEXT COMMENT 'ค่าเดิม',
  `new_value` TEXT COMMENT 'ค่าใหม่',

  -- Audit
  `changed_by` CHAR(36) NOT NULL COMMENT 'ผู้เปลี่ยนแปลง',
  `changed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'วันเวลาเปลี่ยนแปลง',

  -- Primary Key
  PRIMARY KEY (`history_id`),

  -- Indexes
  INDEX idx_ticket_id (`ticket_id`),
  INDEX idx_field_name (`field_name`),
  INDEX idx_changed_at (`changed_at`),

  -- Foreign Keys
  FOREIGN KEY (`ticket_id`) REFERENCES `service_tickets`(`ticket_id`) ON DELETE CASCADE,
  FOREIGN KEY (`changed_by`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางบันทึกประวัติการเปลี่ยนแปลงข้อมูล';

-- ============================================================================
-- ตาราง 7: service_ticket_comments (ความคิดเห็น/หมายเหตุ)
-- ============================================================================
CREATE TABLE `service_ticket_comments` (
  `comment_id` CHAR(36) NOT NULL COMMENT 'รหัสความคิดเห็น (UUID)',
  `ticket_id` CHAR(36) NOT NULL COMMENT 'รหัส Ticket',

  -- ข้อมูลความคิดเห็น
  `comment` TEXT NOT NULL COMMENT 'ความคิดเห็น',
  `is_internal` TINYINT(1) DEFAULT 0 COMMENT 'ความคิดเห็นภายใน (1=ใช่, 0=ไม่ใช่)',

  -- Audit
  `created_by` CHAR(36) NOT NULL COMMENT 'ผู้สร้าง',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'วันเวลาสร้าง',
  `updated_by` CHAR(36) COMMENT 'ผู้อัปเดต',
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันเวลาอัปเดต',

  -- Soft Delete
  `deleted_at` DATETIME COMMENT 'วันเวลาลบ',

  -- Primary Key
  PRIMARY KEY (`comment_id`),

  -- Indexes
  INDEX idx_ticket_id (`ticket_id`),
  INDEX idx_created_at (`created_at`),

  -- Foreign Keys
  FOREIGN KEY (`ticket_id`) REFERENCES `service_tickets`(`ticket_id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บความคิดเห็น/หมายเหตุ';

-- ============================================================================
-- ตาราง 8: service_ticket_notifications (การแจ้งเตือน)
-- ============================================================================
CREATE TABLE `service_ticket_notifications` (
  `notification_id` CHAR(36) NOT NULL COMMENT 'รหัสการแจ้งเตือน (UUID)',
  `ticket_id` CHAR(36) NOT NULL COMMENT 'รหัส Ticket',
  `user_id` CHAR(36) NOT NULL COMMENT 'รหัสผู้รับแจ้งเตือน',

  -- ข้อมูลการแจ้งเตือน
  `type` ENUM('SLA_NEAR','SLA_OVERDUE','STATUS_CHANGE','NEW_COMMENT','ASSIGNED','MENTIONED') NOT NULL COMMENT 'ประเภทการแจ้งเตือน',
  `message` TEXT NOT NULL COMMENT 'ข้อความแจ้งเตือน',
  `is_read` TINYINT(1) DEFAULT 0 COMMENT 'อ่านแล้ว (1=ใช่, 0=ยังไม่อ่าน)',

  -- Audit
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'วันเวลาสร้าง',
  `read_at` DATETIME COMMENT 'วันเวลาที่อ่าน',

  -- Primary Key
  PRIMARY KEY (`notification_id`),

  -- Indexes
  INDEX idx_user_id (`user_id`),
  INDEX idx_ticket_id (`ticket_id`),
  INDEX idx_is_read (`is_read`),
  INDEX idx_created_at (`created_at`),

  -- Foreign Keys
  FOREIGN KEY (`ticket_id`) REFERENCES `service_tickets`(`ticket_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บการแจ้งเตือน';

-- ============================================================================
-- TRIGGERS
-- ============================================================================

-- Trigger 1: Auto-generate UUID for service_tickets
DELIMITER $$
CREATE TRIGGER `before_insert_service_tickets` BEFORE INSERT ON `service_tickets` FOR EACH ROW
BEGIN
    -- สร้าง UUID ถ้ายังไม่ได้กำหนด
    IF NEW.ticket_id IS NULL OR NEW.ticket_id = '' THEN
        SET NEW.ticket_id = UUID();
    END IF;

    -- สร้างเลข Ticket Number อัตโนมัติ (TCK-YYYYMM-XXXX)
    IF NEW.ticket_no IS NULL OR NEW.ticket_no = '' THEN
        SET @ticket_count = (
            SELECT COUNT(*) + 1
            FROM service_tickets
            WHERE DATE_FORMAT(created_at, '%Y%m') = DATE_FORMAT(NOW(), '%Y%m')
        );
        SET NEW.ticket_no = CONCAT('TCK-', DATE_FORMAT(NOW(), '%Y%m'), '-', LPAD(@ticket_count, 4, '0'));
    END IF;

    -- คำนวณ SLA Deadline
    IF NEW.sla_target IS NOT NULL AND NEW.sla_target > 0 THEN
        SET NEW.sla_deadline = DATE_ADD(NEW.created_at, INTERVAL NEW.sla_target HOUR);
    END IF;
END$$
DELIMITER ;

-- Trigger 2: Auto-generate UUID for other tables
DELIMITER $$
CREATE TRIGGER `before_insert_service_ticket_onsite` BEFORE INSERT ON `service_ticket_onsite` FOR EACH ROW
BEGIN
    IF NEW.onsite_id IS NULL OR NEW.onsite_id = '' THEN
        SET NEW.onsite_id = UUID();
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `before_insert_service_ticket_attachments` BEFORE INSERT ON `service_ticket_attachments` FOR EACH ROW
BEGIN
    IF NEW.attachment_id IS NULL OR NEW.attachment_id = '' THEN
        SET NEW.attachment_id = UUID();
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `before_insert_service_ticket_watchers` BEFORE INSERT ON `service_ticket_watchers` FOR EACH ROW
BEGIN
    IF NEW.watcher_id IS NULL OR NEW.watcher_id = '' THEN
        SET NEW.watcher_id = UUID();
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `before_insert_service_ticket_timeline` BEFORE INSERT ON `service_ticket_timeline` FOR EACH ROW
BEGIN
    IF NEW.timeline_id IS NULL OR NEW.timeline_id = '' THEN
        SET NEW.timeline_id = UUID();
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `before_insert_service_ticket_history` BEFORE INSERT ON `service_ticket_history` FOR EACH ROW
BEGIN
    IF NEW.history_id IS NULL OR NEW.history_id = '' THEN
        SET NEW.history_id = UUID();
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `before_insert_service_ticket_comments` BEFORE INSERT ON `service_ticket_comments` FOR EACH ROW
BEGIN
    IF NEW.comment_id IS NULL OR NEW.comment_id = '' THEN
        SET NEW.comment_id = UUID();
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `before_insert_service_ticket_notifications` BEFORE INSERT ON `service_ticket_notifications` FOR EACH ROW
BEGIN
    IF NEW.notification_id IS NULL OR NEW.notification_id = '' THEN
        SET NEW.notification_id = UUID();
    END IF;
END$$
DELIMITER ;

-- Trigger 3: Update SLA Status
DELIMITER $$
CREATE TRIGGER `before_update_service_tickets` BEFORE UPDATE ON `service_tickets` FOR EACH ROW
BEGIN
    -- อัปเดต SLA Status
    IF NEW.sla_deadline IS NOT NULL THEN
        IF NOW() > NEW.sla_deadline THEN
            SET NEW.sla_status = 'Overdue';
        ELSEIF TIMESTAMPDIFF(HOUR, NOW(), NEW.sla_deadline) <= 4 THEN
            SET NEW.sla_status = 'Near SLA';
        ELSE
            SET NEW.sla_status = 'Within SLA';
        END IF;
    END IF;

    -- บันทึกเวลาแก้ไขเสร็จ
    IF NEW.status = 'Resolved' AND OLD.status != 'Resolved' THEN
        SET NEW.resolved_at = NOW();
    END IF;

    -- บันทึกเวลาปิด Ticket
    IF NEW.status = 'Closed' AND OLD.status != 'Closed' THEN
        SET NEW.closed_at = NOW();
    END IF;
END$$
DELIMITER ;

-- Trigger 4: Auto-log History when ticket updated
DELIMITER $$
CREATE TRIGGER `after_update_service_tickets` AFTER UPDATE ON `service_tickets` FOR EACH ROW
BEGIN
    -- บันทึก Status change
    IF OLD.status != NEW.status THEN
        INSERT INTO service_ticket_history (history_id, ticket_id, field_name, old_value, new_value, changed_by, changed_at)
        VALUES (UUID(), NEW.ticket_id, 'status', OLD.status, NEW.status, NEW.updated_by, NOW());
    END IF;

    -- บันทึก Priority change
    IF OLD.priority != NEW.priority THEN
        INSERT INTO service_ticket_history (history_id, ticket_id, field_name, old_value, new_value, changed_by, changed_at)
        VALUES (UUID(), NEW.ticket_id, 'priority', OLD.priority, NEW.priority, NEW.updated_by, NOW());
    END IF;

    -- บันทึก Job Owner change
    IF OLD.job_owner != NEW.job_owner OR (OLD.job_owner IS NULL AND NEW.job_owner IS NOT NULL) THEN
        INSERT INTO service_ticket_history (history_id, ticket_id, field_name, old_value, new_value, changed_by, changed_at)
        VALUES (UUID(), NEW.ticket_id, 'job_owner', OLD.job_owner, NEW.job_owner, NEW.updated_by, NOW());
    END IF;
END$$
DELIMITER ;

-- ============================================================================
-- VIEWS
-- ============================================================================

-- View 1: ดูข้อมูล Ticket พร้อมรายละเอียดทั้งหมด
CREATE OR REPLACE VIEW `vw_service_tickets_full` AS
SELECT
    t.ticket_id,
    t.ticket_no,
    t.ticket_type,
    t.subject,
    t.description,
    t.status,
    t.priority,
    t.urgency,
    t.impact,
    t.service_category,
    t.category,
    t.sub_category,
    t.source,
    t.sla_target,
    t.sla_deadline,
    t.sla_status,
    t.channel,
    t.start_at,
    t.due_at,
    t.resolved_at,
    t.closed_at,

    -- Project Info
    p.project_name,

    -- Job Owner Info
    CONCAT(owner.first_name, ' ', owner.last_name) AS job_owner_name,
    owner.role AS job_owner_role,

    -- Reporter Info
    CONCAT(reporter.first_name, ' ', reporter.last_name) AS reporter_name,

    -- Creator Info
    CONCAT(creator.first_name, ' ', creator.last_name) AS created_by_name,

    -- Onsite Info
    onsite.start_location,
    onsite.end_location,
    onsite.travel_mode,
    onsite.distance,

    -- Counts
    (SELECT COUNT(*) FROM service_ticket_attachments WHERE ticket_id = t.ticket_id) AS attachment_count,
    (SELECT COUNT(*) FROM service_ticket_watchers WHERE ticket_id = t.ticket_id) AS watcher_count,
    (SELECT COUNT(*) FROM service_ticket_comments WHERE ticket_id = t.ticket_id AND deleted_at IS NULL) AS comment_count,

    -- Timestamps
    t.created_at,
    t.updated_at
FROM service_tickets t
LEFT JOIN projects p ON t.project_id = p.project_id
LEFT JOIN users owner ON t.job_owner = owner.user_id
LEFT JOIN users reporter ON t.reporter = reporter.user_id
LEFT JOIN users creator ON t.created_by = creator.user_id
LEFT JOIN service_ticket_onsite onsite ON t.ticket_id = onsite.ticket_id
WHERE t.deleted_at IS NULL;

-- View 2: Dashboard สำหรับแสดง Metrics
CREATE OR REPLACE VIEW `vw_service_tickets_metrics` AS
SELECT
    COUNT(*) AS total_tickets,
    SUM(CASE WHEN status = 'On Process' THEN 1 ELSE 0 END) AS on_process,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending,
    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) AS resolved,
    SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) AS closed,
    SUM(CASE WHEN status = 'Canceled' THEN 1 ELSE 0 END) AS canceled,
    SUM(CASE WHEN sla_status = 'Overdue' THEN 1 ELSE 0 END) AS sla_overdue,
    SUM(CASE WHEN sla_status = 'Near SLA' THEN 1 ELSE 0 END) AS sla_near,
    SUM(CASE WHEN priority = 'Critical' THEN 1 ELSE 0 END) AS critical_priority,
    SUM(CASE WHEN priority = 'High' THEN 1 ELSE 0 END) AS high_priority
FROM service_tickets
WHERE deleted_at IS NULL;

-- View 3: Tickets ที่ต้องติดตาม (SLA ใกล้หมด หรือ Overdue)
CREATE OR REPLACE VIEW `vw_service_tickets_alert` AS
SELECT
    t.ticket_id,
    t.ticket_no,
    t.subject,
    t.status,
    t.priority,
    t.sla_deadline,
    t.sla_status,
    CONCAT(owner.first_name, ' ', owner.last_name) AS job_owner_name,
    TIMESTAMPDIFF(HOUR, NOW(), t.sla_deadline) AS hours_remaining
FROM service_tickets t
LEFT JOIN users owner ON t.job_owner = owner.user_id
WHERE t.deleted_at IS NULL
  AND t.status NOT IN ('Closed', 'Canceled')
  AND (t.sla_status IN ('Near SLA', 'Overdue') OR t.priority = 'Critical')
ORDER BY
    CASE t.sla_status
        WHEN 'Overdue' THEN 1
        WHEN 'Near SLA' THEN 2
        ELSE 3
    END,
    t.sla_deadline ASC;

-- ============================================================================
-- STORED PROCEDURES
-- ============================================================================

-- Procedure 1: คำนวณ SLA ทั้งหมด
DELIMITER $$
CREATE PROCEDURE `sp_update_all_sla_status`()
BEGIN
    UPDATE service_tickets
    SET
        sla_status = CASE
            WHEN sla_deadline IS NULL THEN sla_status
            WHEN NOW() > sla_deadline THEN 'Overdue'
            WHEN TIMESTAMPDIFF(HOUR, NOW(), sla_deadline) <= 4 THEN 'Near SLA'
            ELSE 'Within SLA'
        END
    WHERE deleted_at IS NULL
      AND status NOT IN ('Closed', 'Canceled');
END$$
DELIMITER ;

-- Procedure 2: สร้าง Notification สำหรับ SLA
DELIMITER $$
CREATE PROCEDURE `sp_create_sla_notifications`()
BEGIN
    -- สร้าง Notification สำหรับ Tickets ที่ใกล้ SLA
    INSERT INTO service_ticket_notifications (notification_id, ticket_id, user_id, type, message)
    SELECT
        UUID(),
        t.ticket_id,
        t.job_owner,
        'SLA_NEAR',
        CONCAT('Ticket ', t.ticket_no, ' ใกล้ครบ SLA (เหลือ ', TIMESTAMPDIFF(HOUR, NOW(), t.sla_deadline), ' ชั่วโมง)')
    FROM service_tickets t
    WHERE t.sla_status = 'Near SLA'
      AND t.deleted_at IS NULL
      AND t.status NOT IN ('Closed', 'Canceled')
      AND NOT EXISTS (
          SELECT 1 FROM service_ticket_notifications n
          WHERE n.ticket_id = t.ticket_id
            AND n.type = 'SLA_NEAR'
            AND DATE(n.created_at) = CURDATE()
      );

    -- สร้าง Notification สำหรับ Tickets ที่เลย SLA
    INSERT INTO service_ticket_notifications (notification_id, ticket_id, user_id, type, message)
    SELECT
        UUID(),
        t.ticket_id,
        t.job_owner,
        'SLA_OVERDUE',
        CONCAT('Ticket ', t.ticket_no, ' เลย SLA แล้ว!')
    FROM service_tickets t
    WHERE t.sla_status = 'Overdue'
      AND t.deleted_at IS NULL
      AND t.status NOT IN ('Closed', 'Canceled')
      AND NOT EXISTS (
          SELECT 1 FROM service_ticket_notifications n
          WHERE n.ticket_id = t.ticket_id
            AND n.type = 'SLA_OVERDUE'
            AND DATE(n.created_at) = CURDATE()
      );
END$$
DELIMITER ;

-- Procedure 3: ดึงข้อมูล Ticket ตาม User Role
DELIMITER $$
CREATE PROCEDURE `sp_get_tickets_by_user`(
    IN p_user_id CHAR(36),
    IN p_role VARCHAR(50)
)
BEGIN
    IF p_role = 'Executive' THEN
        -- Executive เห็นทั้งหมด
        SELECT * FROM vw_service_tickets_full WHERE deleted_at IS NULL;
    ELSEIF p_role = 'Sale Supervisor' THEN
        -- Sale Supervisor เห็นของทีม
        SELECT vw.* FROM vw_service_tickets_full vw
        INNER JOIN service_tickets t ON vw.ticket_id = t.ticket_id
        INNER JOIN users u ON t.job_owner = u.user_id
        WHERE (SELECT team_id FROM users WHERE user_id = p_user_id) = u.team_id
          AND vw.deleted_at IS NULL;
    ELSE
        -- Seller, Engineer เห็นของตัวเอง
        SELECT * FROM vw_service_tickets_full
        WHERE job_owner = p_user_id AND deleted_at IS NULL;
    END IF;
END$$
DELIMITER ;

-- ============================================================================
-- EVENT SCHEDULER (ต้อง Enable Event Scheduler ก่อน: SET GLOBAL event_scheduler = ON;)
-- ============================================================================

-- Event 1: อัปเดต SLA Status ทุก 30 นาที
CREATE EVENT IF NOT EXISTS `evt_update_sla_status`
ON SCHEDULE EVERY 30 MINUTE
DO
    CALL sp_update_all_sla_status();

-- Event 2: สร้าง Notification SLA ทุก 1 ชั่วโมง
CREATE EVENT IF NOT EXISTS `evt_create_sla_notifications`
ON SCHEDULE EVERY 1 HOUR
DO
    CALL sp_create_sla_notifications();

-- ============================================================================
-- สิ้นสุดไฟล์ Schema
-- ============================================================================
