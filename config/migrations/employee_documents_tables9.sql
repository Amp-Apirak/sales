-- ============================================
-- Employee Document Management System
-- Database Tables Creation Script
-- Created: 2025-10-12
-- Version: 1.1 (Fixed Foreign Key Constraints)
-- ============================================

-- ============================================
-- ลบตารางเก่า (ถ้ามี) เพื่อสร้างใหม่
-- ============================================
DROP TABLE IF EXISTS `employee_document_links`;
DROP TABLE IF EXISTS `employee_documents`;

-- ============================================
-- ตารางที่ 1: employee_documents
-- สำหรับเก็บไฟล์เอกสารที่อัปโหลด
-- ============================================

CREATE TABLE `employee_documents` (
  `document_id` CHAR(36) NOT NULL,
  `employee_id` CHAR(36) NOT NULL,
  `document_name` VARCHAR(255) NOT NULL,
  `document_category` VARCHAR(50) NOT NULL,
  `document_type` VARCHAR(50) NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `file_size` BIGINT(20) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `upload_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uploaded_by` CHAR(36) DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` CHAR(36) DEFAULT NULL,
  PRIMARY KEY (`document_id`),
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_uploaded_by` (`uploaded_by`),
  KEY `idx_category` (`document_category`),
  KEY `idx_upload_date` (`upload_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- เพิ่ม Foreign Keys สำหรับ employee_documents
ALTER TABLE `employee_documents`
  ADD CONSTRAINT `fk_employee_documents_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_employee_documents_uploader` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_employee_documents_updater` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;


-- ============================================
-- ตารางที่ 2: employee_document_links
-- สำหรับเก็บลิงก์ไปยัง Cloud Storage
-- ============================================

CREATE TABLE `employee_document_links` (
  `link_id` CHAR(36) NOT NULL,
  `employee_id` CHAR(36) NOT NULL,
  `link_name` VARCHAR(255) NOT NULL,
  `link_category` VARCHAR(50) NOT NULL,
  `url` TEXT NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` CHAR(36) NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` CHAR(36) DEFAULT NULL,
  PRIMARY KEY (`link_id`),
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_category` (`link_category`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- เพิ่ม Foreign Keys สำหรับ employee_document_links
ALTER TABLE `employee_document_links`
  ADD CONSTRAINT `fk_employee_document_links_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_employee_document_links_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_employee_document_links_updater` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;


-- ============================================
-- สร้าง Indexes เพิ่มเติมเพื่อ Performance
-- ============================================

-- Composite Index สำหรับค้นหาเอกสารของพนักงานตามหมวดหมู่
CREATE INDEX idx_emp_doc_category ON employee_documents(employee_id, document_category);

-- Composite Index สำหรับค้นหาลิงก์ของพนักงานตามหมวดหมู่
CREATE INDEX idx_emp_link_category ON employee_document_links(employee_id, link_category);


-- ============================================
-- คำสั่งสำหรับลบตาราง (ใช้เมื่อต้องการ Reset)
-- ============================================
-- DROP TABLE IF EXISTS `employee_document_links`;
-- DROP TABLE IF EXISTS `employee_documents`;


-- ============================================
-- ตัวอย่างข้อมูลทดสอบ (Optional)
-- ============================================
/*
-- สมมติว่ามี employee_id = 'test-employee-uuid'
-- และมี user_id = 'test-user-uuid'

INSERT INTO employee_documents
(document_id, employee_id, document_name, document_category, document_type, file_path, file_size, uploaded_by)
VALUES
(UUID(), 'test-employee-uuid', 'Resume_2024.pdf', 'resume', 'pdf', 'uploads/employee_documents/test-employee-uuid/resume.pdf', 1024000, 'test-user-uuid'),
(UUID(), 'test-employee-uuid', 'Certificate.pdf', 'certificate', 'pdf', 'uploads/employee_documents/test-employee-uuid/cert.pdf', 512000, 'test-user-uuid');

INSERT INTO employee_document_links
(link_id, employee_id, link_name, link_category, url, created_by)
VALUES
(UUID(), 'test-employee-uuid', 'CV in Google Drive', 'drive', 'https://drive.google.com/file/d/xxxxx', 'test-user-uuid'),
(UUID(), 'test-employee-uuid', 'Documents Folder', 'sharepoint', 'https://sharepoint.com/folder/xxxxx', 'test-user-uuid');
*/


-- ============================================
-- คำสั่งตรวจสอบตารางที่สร้าง
-- ============================================
-- SHOW CREATE TABLE employee_documents;
-- SHOW CREATE TABLE employee_document_links;
-- SELECT * FROM employee_documents;
-- SELECT * FROM employee_document_links;


-- ============================================
-- END OF SCRIPT
-- ============================================
