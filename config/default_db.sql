-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 22, 2026 at 03:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sales_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` char(36) NOT NULL COMMENT 'รหัสหมวดหมู่ (UUID)',
  `service_category` varchar(255) NOT NULL COMMENT 'หมวดหมู่บริการ',
  `category` varchar(255) NOT NULL COMMENT 'หมวดหมู่',
  `sub_category` varchar(255) DEFAULT NULL COMMENT 'หมวดหมู่ย่อย',
  `problems` text DEFAULT NULL COMMENT 'ปัญหา',
  `cases` text DEFAULT NULL COMMENT 'กรณีศึกษา',
  `resolve` text DEFAULT NULL COMMENT 'การแก้ไข',
  `image_id` char(36) DEFAULT NULL COMMENT 'รหัสรูปภาพ (เชื่อมโยงกับตาราง Category_image)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตล่าสุด',
  `created_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `service_category`, `category`, `sub_category`, `problems`, `cases`, `resolve`, `image_id`, `created_at`, `updated_at`, `created_by`) VALUES
('27a7e7b7-b323-11f0-9a0c-005056b8f6d0', 'Project Management (การบริหารโครงการ)', 'Document Management', 'Monthly Report', NULL, NULL, NULL, NULL, '2025-10-27 10:53:47', '2025-10-27 10:53:47', '2'),
('4140dc24-a75c-11f0-aff6-005056b8f6d0', 'service_category', 'category', 'sub_category', NULL, NULL, NULL, NULL, '2025-10-12 11:12:42', '2025-10-12 11:12:42', '2'),
('416537d2-a75c-11f0-aff6-005056b8f6d0', 'Offsite Work', 'Installation/Deployment', 'Go-live support (on-site)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:42', '2025-10-12 11:12:42', '2'),
('41656302-a75c-11f0-aff6-005056b8f6d0', 'Offsite Work', 'Maintenance', 'Preventive maintenance (scheduled)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:42', '2025-10-12 11:12:42', '2'),
('416583c5-a75c-11f0-aff6-005056b8f6d0', 'Offsite Work', 'Maintenance', 'Corrective maintenance (break-fix)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:42', '2025-10-12 11:12:42', '2'),
('4165a5f2-a75c-11f0-aff6-005056b8f6d0', 'Offsite Work', 'Survey', 'Pre-sale/technical site survey', NULL, NULL, NULL, NULL, '2025-10-12 11:12:42', '2025-10-12 11:12:42', '2'),
('4165c62b-a75c-11f0-aff6-005056b8f6d0', 'Offsite Work', 'Emergency', 'Urgent incident on-site (P1)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:42', '2025-10-12 11:12:42', '2'),
('4165ecf7-a75c-11f0-aff6-005056b8f6d0', 'Offsite Work', 'VIP Support', 'Executive/VIP on-site assistance', NULL, NULL, NULL, NULL, '2025-10-12 11:12:42', '2025-10-12 11:12:42', '2'),
('4166120e-a75c-11f0-aff6-005056b8f6d0', 'Offsite Work', 'Standby', 'On-call standby on customer site', NULL, NULL, NULL, NULL, '2025-10-12 11:12:42', '2025-10-12 11:12:42', '2'),
('4166314f-a75c-11f0-aff6-005056b8f6d0', 'Offsite Work', 'Handover', 'On-site handover / acceptance (SAT)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:42', '2025-10-12 11:12:42', '2'),
('416650c1-a75c-11f0-aff6-005056b8f6d0', 'Offsite Work', 'Logistics', 'Travel arrangement / hotel / allowance', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41666f6d-a75c-11f0-aff6-005056b8f6d0', 'Offsite Work', 'Safety', 'Toolbox meeting / Work permit / PPE check', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41668d97-a75c-11f0-aff6-005056b8f6d0', 'Offsite Work', 'Documentation', 'Field service report / sign-off', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4166af6c-a75c-11f0-aff6-005056b8f6d0', 'Offsite Work', 'Expense', 'Per diem / mileage claim', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4166cf01-a75c-11f0-aff6-005056b8f6d0', 'Training', 'End-User Training', 'Application usage (basic/advance)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4166f4ac-a75c-11f0-aff6-005056b8f6d0', 'Training', 'Administrator Training', 'System admin / operations', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41671534-a75c-11f0-aff6-005056b8f6d0', 'Training', 'DevOps Training', 'CI/CD & Git workflow', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41673bf2-a75c-11f0-aff6-005056b8f6d0', 'Training', 'Security Awareness', 'Phishing drill / policy', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41675c25-a75c-11f0-aff6-005056b8f6d0', 'Training', 'Onboarding', 'New hire IT orientation', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41677c86-a75c-11f0-aff6-005056b8f6d0', 'Training', 'Train-the-Trainer', 'Enable internal champions', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41679bf0-a75c-11f0-aff6-005056b8f6d0', 'Training', 'Curriculum', 'Course design / outline / schedule', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4167ba6b-a75c-11f0-aff6-005056b8f6d0', 'Training', 'Lab', 'Hands-on lab / environment setup', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4167e115-a75c-11f0-aff6-005056b8f6d0', 'Training', 'Assessment', 'Pre-test / post-test / quiz', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416802c3-a75c-11f0-aff6-005056b8f6d0', 'Training', 'Certificate', 'Certificate issuance / record', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4168242a-a75c-11f0-aff6-005056b8f6d0', 'Training', 'Feedback', 'Course evaluation / improvement', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41684e5c-a75c-11f0-aff6-005056b8f6d0', 'Study Visit', 'Customer Site', 'Best practice observation', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416877ee-a75c-11f0-aff6-005056b8f6d0', 'Study Visit', 'Vendor/Partner', 'Solution roadmap / reference site', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416899e8-a75c-11f0-aff6-005056b8f6d0', 'Study Visit', 'Benchmarking', 'Process & KPI comparison', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4168b996-a75c-11f0-aff6-005056b8f6d0', 'Study Visit', 'Expo/Conference', 'Tech fair / summit attendance', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4168e924-a75c-11f0-aff6-005056b8f6d0', 'Study Visit', 'Report', 'Findings & recommendation write-up', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41691975-a75c-11f0-aff6-005056b8f6d0', 'Presentation', 'Sales Pitch', 'Corporate deck / capabilities', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416937be-a75c-11f0-aff6-005056b8f6d0', 'Presentation', 'Solution Demo', 'Live/recorded demo', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4169559f-a75c-11f0-aff6-005056b8f6d0', 'Presentation', 'POC', 'Proof of Concept presentation', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41697fdb-a75c-11f0-aff6-005056b8f6d0', 'Presentation', 'Executive Briefing', 'C-level/SteerCo briefing', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4169a0bf-a75c-11f0-aff6-005056b8f6d0', 'Presentation', 'QBR/MBR', 'Quarterly/Monthly business review', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4169c3ed-a75c-11f0-aff6-005056b8f6d0', 'Presentation', 'RFP', 'Proposal/Scope walkthrough', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4169e3d8-a75c-11f0-aff6-005056b8f6d0', 'Presentation', 'Internal', 'Design review / peer review', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416a05ad-a75c-11f0-aff6-005056b8f6d0', 'Presentation', 'Public', 'Webinar / community talk', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416a264f-a75c-11f0-aff6-005056b8f6d0', 'Presentation', 'Follow-up', 'Action items & minutes (MoM)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416a44ed-a75c-11f0-aff6-005056b8f6d0', 'Company Activity', 'Town Hall / All-Hands', 'Company update session', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416a6312-a75c-11f0-aff6-005056b8f6d0', 'Company Activity', 'Team Building', 'Outdoor/indoor activities', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416a8198-a75c-11f0-aff6-005056b8f6d0', 'Company Activity', 'CSR', 'Volunteer / community service', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416aa062-a75c-11f0-aff6-005056b8f6d0', 'Company Activity', 'Company Trip', 'Annual outing / retreat', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416ac0b4-a75c-11f0-aff6-005056b8f6d0', 'Company Activity', 'Hackathon/Innovation Day', 'Internal hackfest / demo day', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416adf99-a75c-11f0-aff6-005056b8f6d0', 'Company Activity', 'Kaizen/Improvement Day', 'Process improvement workshop', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416afdaa-a75c-11f0-aff6-005056b8f6d0', 'Company Activity', 'Knowledge Sharing', 'Brown-bag / tech talk', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416b1f65-a75c-11f0-aff6-005056b8f6d0', 'Company Activity', 'ISO/5S', 'Internal audit / 5S inspection', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416b406d-a75c-11f0-aff6-005056b8f6d0', 'Company Activity', 'Safety', 'Fire drill / first aid training', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416b60c3-a75c-11f0-aff6-005056b8f6d0', 'Company Activity', 'Celebration', 'Milestone / award event', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416b80cf-a75c-11f0-aff6-005056b8f6d0', 'Company Activity', 'HR Event', 'Onboarding day / birthday / farewell', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416ba501-a75c-11f0-aff6-005056b8f6d0', 'Meeting', 'Internal', 'Sprint planning / retrospective', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416bce28-a75c-11f0-aff6-005056b8f6d0', 'Meeting', 'Customer', 'Requirement / status update', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416beff7-a75c-11f0-aff6-005056b8f6d0', 'Meeting', 'CAB/Change', 'Change advisory board', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416c125a-a75c-11f0-aff6-005056b8f6d0', 'Meeting', 'Steering Committee', 'Project governance', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416c3270-a75c-11f0-aff6-005056b8f6d0', 'Meeting', 'Vendor', 'Quarterly vendor review', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416c54e0-a75c-11f0-aff6-005056b8f6d0', 'Meeting', 'Minutes', 'MoM / action tracker / decision log', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416c749b-a75c-11f0-aff6-005056b8f6d0', 'Support', 'Remote Support', 'Quick Assist / AnyDesk session', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416c9452-a75c-11f0-aff6-005056b8f6d0', 'Support', 'Remote Support', 'Phone support', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416cb279-a75c-11f0-aff6-005056b8f6d0', 'Support', 'Remote Support', 'Chat/Email support', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416cd2df-a75c-11f0-aff6-005056b8f6d0', 'Support', 'On-site Support', 'Field visit (ออกปฏิบัติงานนอกพื้นที่)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416cf366-a75c-11f0-aff6-005056b8f6d0', 'Support', 'On-site Support', 'Branch visit / Upcountry', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416d1607-a75c-11f0-aff6-005056b8f6d0', 'Support', 'On-site Support', 'Emergency on-site (นอกเวลาราชการ)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416d385d-a75c-11f0-aff6-005056b8f6d0', 'Support', 'User Account', 'Password reset', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416d5a07-a75c-11f0-aff6-005056b8f6d0', 'Support', 'User Account', 'Unlock account', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416d7fa6-a75c-11f0-aff6-005056b8f6d0', 'Support', 'User Account', 'Create/Modify/Disable user', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416da027-a75c-11f0-aff6-005056b8f6d0', 'Support', 'Service Request', 'New equipment request', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416dbf67-a75c-11f0-aff6-005056b8f6d0', 'Support', 'Service Request', 'Software request', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416de08f-a75c-11f0-aff6-005056b8f6d0', 'Support', 'Service Request', 'Access request (ระบบ/ไฟล์/โฟลเดอร์)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416e0222-a75c-11f0-aff6-005056b8f6d0', 'Support', 'How-to', 'User guidance / คู่มือการใช้งาน', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416e26c9-a75c-11f0-aff6-005056b8f6d0', 'Support', 'Meeting', 'User meeting / requirement gathering (ประชุมงาน)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416e4f23-a75c-11f0-aff6-005056b8f6d0', 'Support', 'Training', 'User training / อบรมผู้ใช้', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416e6f1a-a75c-11f0-aff6-005056b8f6d0', 'Support', 'Documentation', 'User manual / SOP', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416e93c8-a75c-11f0-aff6-005056b8f6d0', 'Support', 'Handover', 'Project/Task handover', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416ec29d-a75c-11f0-aff6-005056b8f6d0', 'Support', 'Schedule', 'Site booking / นัดหมายงานนอกพื้นที่', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416eea90-a75c-11f0-aff6-005056b8f6d0', 'Incident', 'Service Outage', 'Major incident (P1)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416f1164-a75c-11f0-aff6-005056b8f6d0', 'Incident', 'Service Degradation', 'Slow performance', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416f453e-a75c-11f0-aff6-005056b8f6d0', 'Incident', 'Hardware Fault', 'PC/Notebook not power on', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416f7541-a75c-11f0-aff6-005056b8f6d0', 'Incident', 'Hardware Fault', 'Monitor display issue', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416f9c92-a75c-11f0-aff6-005056b8f6d0', 'Incident', 'Hardware Fault', 'Printer jam / cannot print', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416fc26b-a75c-11f0-aff6-005056b8f6d0', 'Incident', 'Peripheral', 'Mouse/Keyboard malfunction', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('416ff173-a75c-11f0-aff6-005056b8f6d0', 'Incident', 'Network', 'No network / Link down', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417028f9-a75c-11f0-aff6-005056b8f6d0', 'Incident', 'Network', 'WiFi cannot connect', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417059ce-a75c-11f0-aff6-005056b8f6d0', 'Incident', 'Network', 'Internet down / ISP issue', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41708bb1-a75c-11f0-aff6-005056b8f6d0', 'Incident', 'Security', 'Phishing / malware', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4170ba8e-a75c-11f0-aff6-005056b8f6d0', 'Incident', 'Security', 'Suspicious login / account compromise', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4170e039-a75c-11f0-aff6-005056b8f6d0', 'Problem', 'Root Cause Analysis', 'Recurring network outages', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41710b99-a75c-11f0-aff6-005056b8f6d0', 'Problem', 'Root Cause Analysis', 'Application memory leak', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41713d4e-a75c-11f0-aff6-005056b8f6d0', 'Change', 'Standard Change', 'User permission update', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41715f2d-a75c-11f0-aff6-005056b8f6d0', 'Change', 'Normal Change', 'Patch OS / security update', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41718192-a75c-11f0-aff6-005056b8f6d0', 'Change', 'Emergency Change', 'Firewall rule urgent fix', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4171a50b-a75c-11f0-aff6-005056b8f6d0', 'Change', 'CAB', 'Change advisory board meeting (ประชุมพิจารณาเปลี่ยนแปลง)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4171c93d-a75c-11f0-aff6-005056b8f6d0', 'Maintenance', 'Preventive', 'Endpoint health check (รายเดือน)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4171eb6e-a75c-11f0-aff6-005056b8f6d0', 'Maintenance', 'Preventive', 'Server patching (รายเดือน)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417213f1-a75c-11f0-aff6-005056b8f6d0', 'Maintenance', 'Preventive', 'Network device inspection', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41723817-a75c-11f0-aff6-005056b8f6d0', 'Maintenance', 'Preventive', 'CCTV cleaning & focus adjust', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417bea2b-a75c-11f0-aff6-005056b8f6d0', 'Maintenance', 'Corrective', 'Replace HDD / SSD', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417c170e-a75c-11f0-aff6-005056b8f6d0', 'Maintenance', 'Corrective', 'UPS battery replacement', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417c3973-a75c-11f0-aff6-005056b8f6d0', 'Maintenance', 'Corrective', 'AP/fan replacement', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417c5c41-a75c-11f0-aff6-005056b8f6d0', 'Maintenance', 'Vendor Coordination', 'RMA / Warranty claim', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417c8455-a75c-11f0-aff6-005056b8f6d0', 'Maintenance', 'After Hours', 'Night window / maintenance window', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417ca52d-a75c-11f0-aff6-005056b8f6d0', 'Installation', 'Endpoint', 'New PC/Notebook setup (SOE)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417cc900-a75c-11f0-aff6-005056b8f6d0', 'Installation', 'Endpoint', 'Printer install & test', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417cea91-a75c-11f0-aff6-005056b8f6d0', 'Installation', 'Network', 'Switch install & labeling', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417d0cab-a75c-11f0-aff6-005056b8f6d0', 'Installation', 'Network', 'Access Point mounting & survey', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417d2cbe-a75c-11f0-aff6-005056b8f6d0', 'Installation', 'Server', 'Rack & stack / cabling', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417d4d2a-a75c-11f0-aff6-005056b8f6d0', 'Installation', 'Server', 'Virtualization host install', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417d710c-a75c-11f0-aff6-005056b8f6d0', 'Installation', 'Storage', 'NAS/SAN install', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417d9131-a75c-11f0-aff6-005056b8f6d0', 'Installation', 'CCTV', 'Camera install & alignment', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417db41f-a75c-11f0-aff6-005056b8f6d0', 'Installation', 'Access Control', 'Controller & Reader install', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417dd6ee-a75c-11f0-aff6-005056b8f6d0', 'Installation', 'A/V', 'Meeting room VC setup', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417e00ba-a75c-11f0-aff6-005056b8f6d0', 'Installation', 'IoT', 'Sensor deploy & calibration', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417e24ca-a75c-11f0-aff6-005056b8f6d0', 'Installation', 'Software', 'Client application rollout', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417e462b-a75c-11f0-aff6-005056b8f6d0', 'Deployment', 'Application', 'Release to UAT', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417e675d-a75c-11f0-aff6-005056b8f6d0', 'Deployment', 'Application', 'Release to Production', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417e885d-a75c-11f0-aff6-005056b8f6d0', 'Deployment', 'CI/CD', 'Pipeline configuration', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417ea9d7-a75c-11f0-aff6-005056b8f6d0', 'Field Work', 'Survey', 'Site survey (สำรวจพื้นที่)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417ecaa3-a75c-11f0-aff6-005056b8f6d0', 'Field Work', 'Cabling', 'UTP/Fiber laying & testing', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417eee64-a75c-11f0-aff6-005056b8f6d0', 'Field Work', 'Acceptance', 'SAT/UAT on site (ทดสอบรับมอบ)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417f12f5-a75c-11f0-aff6-005056b8f6d0', 'Field Work', 'HSE', 'Safety briefing / Work permit', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417f33d1-a75c-11f0-aff6-005056b8f6d0', 'Network', 'LAN', 'Port provisioning / VLAN', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417f5420-a75c-11f0-aff6-005056b8f6d0', 'Network', 'LAN', 'Looback/ACL update', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417f7499-a75c-11f0-aff6-005056b8f6d0', 'Network', 'WAN', 'MPLS/SD-WAN cutover', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417f9834-a75c-11f0-aff6-005056b8f6d0', 'Network', 'WiFi', 'SSID config / captive portal', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417fb809-a75c-11f0-aff6-005056b8f6d0', 'Network', 'Firewall', 'Policy change / NAT', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417fd8cb-a75c-11f0-aff6-005056b8f6d0', 'Network', 'Firewall', 'Threat prevention / IPS', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('417ffa1b-a75c-11f0-aff6-005056b8f6d0', 'Network', 'VPN', 'Remote user VPN issue', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41801ade-a75c-11f0-aff6-005056b8f6d0', 'Network', 'Monitoring', 'Netflow/Telemetry alert review', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41803d27-a75c-11f0-aff6-005056b8f6d0', 'Network', 'Capacity', 'Link utilization analysis', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41805f41-a75c-11f0-aff6-005056b8f6d0', 'Network', 'IP Management', 'DHCP/DNS/IPAM', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4180862e-a75c-11f0-aff6-005056b8f6d0', 'Server', 'Windows Server', 'AD/DC health / GPO', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4180a76d-a75c-11f0-aff6-005056b8f6d0', 'Server', 'Linux', 'Service daemon failed', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4180ca70-a75c-11f0-aff6-005056b8f6d0', 'Server', 'Virtualization', 'VM creation / template', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4180ee75-a75c-11f0-aff6-005056b8f6d0', 'Server', 'Virtualization', 'vMotion / live migration', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41811288-a75c-11f0-aff6-005056b8f6d0', 'Server', 'Backup', 'Job failed / retry', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('418134a5-a75c-11f0-aff6-005056b8f6d0', 'Server', 'Backup', 'Restore test / DR drill', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4181578b-a75c-11f0-aff6-005056b8f6d0', 'Storage', 'SAN/NAS', 'LUN provisioning', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4181803b-a75c-11f0-aff6-005056b8f6d0', 'Storage', 'SAN/NAS', 'Disk failure / rebuild', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('4181a707-a75c-11f0-aff6-005056b8f6d0', 'Storage', 'File Services', 'Quota / permission structure', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41a956d9-a75c-11f0-aff6-005056b8f6d0', 'Endpoint', 'Windows', 'BSOD / driver issue', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41a98f52-a75c-11f0-aff6-005056b8f6d0', 'Endpoint', 'macOS', 'Profile deployment', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41a9c12d-a75c-11f0-aff6-005056b8f6d0', 'Endpoint', 'Mobile', 'MDM enroll / policy', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41a9f6b6-a75c-11f0-aff6-005056b8f6d0', 'Endpoint', 'VDI', 'Profile/FSLogix issue', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41aa38ae-a75c-11f0-aff6-005056b8f6d0', 'Endpoint', 'Security', 'BitLocker/Encryption', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41aa8629-a75c-11f0-aff6-005056b8f6d0', 'Endpoint', 'Patch', 'WSUS/Intune compliance', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41aaba3c-a75c-11f0-aff6-005056b8f6d0', 'Collaboration', 'Email', 'SMTP/IMAP/POP issue', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41aae40c-a75c-11f0-aff6-005056b8f6d0', 'Collaboration', 'Email', 'Mailbox quota / archive', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41ab2cb7-a75c-11f0-aff6-005056b8f6d0', 'Collaboration', 'Email Security', 'SPF/DKIM/DMARC', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41ab6c90-a75c-11f0-aff6-005056b8f6d0', 'Collaboration', 'O365/Google Workspace', 'License assign', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41c9c6bc-a75c-11f0-aff6-005056b8f6d0', 'Collaboration', 'Teams/Meet/Zoom', 'Meeting room device', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41ca04f0-a75c-11f0-aff6-005056b8f6d0', 'Collaboration', 'Share/Drive', 'Permission & sharing', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41ca640f-a75c-11f0-aff6-005056b8f6d0', 'Identity', 'Keycloak/SSO', 'LDAP sync / attribute mapping', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d104ef-a75c-11f0-aff6-005056b8f6d0', 'Identity', 'Keycloak/SSO', 'Token/Session issue', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d13a1f-a75c-11f0-aff6-005056b8f6d0', 'Identity', 'AD/LDAP', 'Group policy / OU structure', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d18b8c-a75c-11f0-aff6-005056b8f6d0', 'Identity', 'Privileged Access', 'PAM account rotation', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d1ccb2-a75c-11f0-aff6-005056b8f6d0', 'Database', 'MySQL/MariaDB', 'Slow query / index', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d2204b-a75c-11f0-aff6-005056b8f6d0', 'Database', 'PostgreSQL', 'Connection pool / timeout', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d25e79-a75c-11f0-aff6-005056b8f6d0', 'Database', 'SQL Server', 'Agent job failed', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d292fa-a75c-11f0-aff6-005056b8f6d0', 'Database', 'Backup', 'Point-in-time recovery', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d2cdbd-a75c-11f0-aff6-005056b8f6d0', 'Database', 'Migration', 'Schema change / versioning', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d37021-a75c-11f0-aff6-005056b8f6d0', 'Development', 'Frontend', 'UI bug / layout', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d3a084-a75c-11f0-aff6-005056b8f6d0', 'Development', 'Backend', 'API timeout / 5xx', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d3cf08-a75c-11f0-aff6-005056b8f6d0', 'Development', 'Integration', 'Webhook / API mapping', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d40849-a75c-11f0-aff6-005056b8f6d0', 'Development', 'Security', 'JWT/Token expiry', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d43878-a75c-11f0-aff6-005056b8f6d0', 'DevOps', 'Git/Version Control', 'Merge conflict / branching', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d4689a-a75c-11f0-aff6-005056b8f6d0', 'DevOps', 'CI/CD', 'Runner offline / pipeline failed', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d493c3-a75c-11f0-aff6-005056b8f6d0', 'DevOps', 'Container', 'Docker build / image vuln', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d4c73f-a75c-11f0-aff6-005056b8f6d0', 'DevOps', 'Kubernetes', 'CrashLoopBackOff / HPA', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d4ef69-a75c-11f0-aff6-005056b8f6d0', 'DevOps', 'Observability', 'Logging/Tracing dashboard', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d528ed-a75c-11f0-aff6-005056b8f6d0', 'DevOps', 'IaC', 'Terraform plan/apply', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d550ab-a75c-11f0-aff6-005056b8f6d0', 'DevOps', 'Release', 'Rollback / hotfix', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d57a36-a75c-11f0-aff6-005056b8f6d0', 'Testing', 'Unit Test', 'Failed case', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d5a66f-a75c-11f0-aff6-005056b8f6d0', 'Testing', 'Integration Test', 'Service contract failed', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d5ca70-a75c-11f0-aff6-005056b8f6d0', 'Testing', 'E2E / UI Test', 'Playwright/Cypress error', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d5f128-a75c-11f0-aff6-005056b8f6d0', 'Testing', 'Performance', 'Load/Stress test', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d6202c-a75c-11f0-aff6-005056b8f6d0', 'Testing', 'UAT', 'Scenario error (ผู้ใช้ทดสอบ)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d6454a-a75c-11f0-aff6-005056b8f6d0', 'Testing', 'Regression', 'Old bug reappear', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d6741c-a75c-11f0-aff6-005056b8f6d0', 'Testing', 'Automation', 'Pipeline test flaky', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d6ad65-a75c-11f0-aff6-005056b8f6d0', 'Cloud', 'AWS', 'EC2/EKS issue', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d6e6ba-a75c-11f0-aff6-005056b8f6d0', 'Cloud', 'AWS', 'S3/CloudFront/Route53', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d70bcf-a75c-11f0-aff6-005056b8f6d0', 'Cloud', 'Azure', 'VM/AKS/Storage', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d73039-a75c-11f0-aff6-005056b8f6d0', 'Cloud', 'GCP', 'GKE/Compute/CloudSQL', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d7561e-a75c-11f0-aff6-005056b8f6d0', 'Cloud', 'Cost', 'Budget alert / optimization', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d7798c-a75c-11f0-aff6-005056b8f6d0', 'Cloud', 'Identity', 'Entra/AWS IAM/Google IAM', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d7a57c-a75c-11f0-aff6-005056b8f6d0', 'Security', 'Endpoint Security', 'EDR alert triage', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d7ca7a-a75c-11f0-aff6-005056b8f6d0', 'Security', 'Network Security', 'IDS/IPS alert', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d7f4d3-a75c-11f0-aff6-005056b8f6d0', 'Security', 'Vulnerability', 'Scan & remediation', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d81b33-a75c-11f0-aff6-005056b8f6d0', 'Security', 'Compliance', 'PDPA/GDPR control', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d83e47-a75c-11f0-aff6-005056b8f6d0', 'Security', 'Audit', 'Log review / evidence', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d862a8-a75c-11f0-aff6-005056b8f6d0', 'Security', 'Awareness', 'Phishing drill training', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d890d7-a75c-11f0-aff6-005056b8f6d0', 'Monitoring', 'Availability', 'Uptime alert', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d8b420-a75c-11f0-aff6-005056b8f6d0', 'Monitoring', 'Performance', 'APM slow transaction', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d8d6c1-a75c-11f0-aff6-005056b8f6d0', 'Monitoring', 'Capacity', 'Disk/CPU threshold', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d8fd95-a75c-11f0-aff6-005056b8f6d0', 'Monitoring', 'Logging', 'Parsing / retention policy', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d92276-a75c-11f0-aff6-005056b8f6d0', 'Monitoring', 'Alerting', 'On-call escalation', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d9492c-a75c-11f0-aff6-005056b8f6d0', 'Project', 'Initiation', 'Project kickoff meeting', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d96e6a-a75c-11f0-aff6-005056b8f6d0', 'Project', 'Planning', 'WBS / timeline / resource plan', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d99457-a75c-11f0-aff6-005056b8f6d0', 'Project', 'Execution', 'Sprint / tasks tracking', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d9bfdf-a75c-11f0-aff6-005056b8f6d0', 'Project', 'Review', 'Steering committee (ประชุม)', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41d9f1d5-a75c-11f0-aff6-005056b8f6d0', 'Project', 'Closure', 'Handover & lesson learned', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41da19fa-a75c-11f0-aff6-005056b8f6d0', 'Asset Management', 'Inventory', 'Stock in/out / tagging', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41da3f35-a75c-11f0-aff6-005056b8f6d0', 'Asset Management', 'CMDB', 'Update CI relationship', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41da64ac-a75c-11f0-aff6-005056b8f6d0', 'Asset Management', 'Lifecycle', 'Assign/Return/Dispose', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41da8946-a75c-11f0-aff6-005056b8f6d0', 'Procurement', 'Quotation', 'Vendor sourcing', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41daad51-a75c-11f0-aff6-005056b8f6d0', 'Procurement', 'Purchase', 'PO/GR/Invoice', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dad1c7-a75c-11f0-aff6-005056b8f6d0', 'License', 'Compliance', 'True-up / renewal', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41db01f8-a75c-11f0-aff6-005056b8f6d0', 'License', 'Key Management', 'Activation / KMS', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41db2e78-a75c-11f0-aff6-005056b8f6d0', 'Telephony/UC', 'PBX/VoIP', 'Extension setup', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41db5418-a75c-11f0-aff6-005056b8f6d0', 'Telephony/UC', 'SIP Trunk', 'Call quality / jitter', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41db7ae0-a75c-11f0-aff6-005056b8f6d0', 'Telephony/UC', 'Contact Center', 'ACD/IVR config', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dba15f-a75c-11f0-aff6-005056b8f6d0', 'AV/Meeting Room', 'Display/Projector', 'Not display / lamp', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dbc97c-a75c-11f0-aff6-005056b8f6d0', 'AV/Meeting Room', 'VC Codec', 'Teams/Zoom Room issue', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dbf1b6-a75c-11f0-aff6-005056b8f6d0', 'AV/Meeting Room', 'Scheduling', 'Room calendar / panel', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dc1642-a75c-11f0-aff6-005056b8f6d0', 'CCTV', 'NVR/Recorder', 'Storage full / HDD fault', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dc3a98-a75c-11f0-aff6-005056b8f6d0', 'CCTV', 'Camera', 'No video / IR failure', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dc5f55-a75c-11f0-aff6-005056b8f6d0', 'CCTV', 'Analytics', 'LPR/Face/Speed detection', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dc85c9-a75c-11f0-aff6-005056b8f6d0', 'Access Control', 'Controller', 'Door not open / relay', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dcadf6-a75c-11f0-aff6-005056b8f6d0', 'Access Control', 'Reader', 'Card add/remove', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dcd61a-a75c-11f0-aff6-005056b8f6d0', 'Access Control', 'Badge', 'Lost card / reissue', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dd025d-a75c-11f0-aff6-005056b8f6d0', 'Physical Security', 'Alarm', 'Fire/Water/Smoke sensor', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dd2fd2-a75c-11f0-aff6-005056b8f6d0', 'Physical Security', 'Barrier/Gate', 'Motor failure', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dd53eb-a75c-11f0-aff6-005056b8f6d0', 'Data/Analytics', 'ETL', 'Pipeline failure', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dd78ad-a75c-11f0-aff6-005056b8f6d0', 'Data/Analytics', 'Warehouse', 'Partition/Cluster', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dd9e61-a75c-11f0-aff6-005056b8f6d0', 'Data/Analytics', 'BI/Dashboard', 'Report incorrect', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41ddc6b4-a75c-11f0-aff6-005056b8f6d0', 'Integration', 'REST/GraphQL', 'Mapping / schema', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41ddedef-a75c-11f0-aff6-005056b8f6d0', 'Integration', 'Webhook', 'Retry / dead-letter', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41de135d-a75c-11f0-aff6-005056b8f6d0', 'Integration', 'Auth', 'OAuth2 / OpenID flow', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41de398c-a75c-11f0-aff6-005056b8f6d0', 'DR/BCP', 'Plan', 'Runbook update', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41de5eb3-a75c-11f0-aff6-005056b8f6d0', 'DR/BCP', 'Drill', 'Failover test', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41de832c-a75c-11f0-aff6-005056b8f6d0', 'DR/BCP', 'Event', 'Site failover / incident', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dea87e-a75c-11f0-aff6-005056b8f6d0', 'Facilities IT', 'Power', 'UPS / power outage', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41ded08b-a75c-11f0-aff6-005056b8f6d0', 'Facilities IT', 'Cooling', 'Server room AC', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41def481-a75c-11f0-aff6-005056b8f6d0', 'Facilities IT', 'Rack/Cable', 'Label / tidy up', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41df1a93-a75c-11f0-aff6-005056b8f6d0', 'Finance/Billing', 'Chargeback', 'Showback / cost allocation', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41df3ff1-a75c-11f0-aff6-005056b8f6d0', 'Finance/Billing', 'Vendor Billing', 'Invoice dispute', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41df6556-a75c-11f0-aff6-005056b8f6d0', 'Compliance', 'Policy', 'IT policy update / approve', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41df8acf-a75c-11f0-aff6-005056b8f6d0', 'Compliance', 'Regulatory', 'e-Tax/PDPA evidence', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dfb147-a75c-11f0-aff6-005056b8f6d0', 'Others', 'Suggestion', 'Service improvement idea', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('41dfd634-a75c-11f0-aff6-005056b8f6d0', 'Others', 'Survey', 'CSAT/Feedback', NULL, NULL, NULL, NULL, '2025-10-12 11:12:43', '2025-10-12 11:12:43', '2'),
('424a20a7-b4c1-11f0-9a0c-005056b8f6d0', 'Marketing Services (บริการด้านการตลาด)', 'Branding & Identity (การสร้างแบรนด์และอัตลักษณ์)', 'Logo Design (การออกแบบโลโก้)', NULL, NULL, NULL, NULL, '2025-10-29 12:18:05', '2025-10-29 12:18:05', '2'),
('4838be34-a75b-11f0-aff6-005056b8f6d0', 'Notebook', 'Ram', 'Loss', NULL, NULL, NULL, NULL, '2025-10-12 11:05:44', '2025-10-12 11:05:44', '2'),
('483a47e3-a75b-11f0-aff6-005056b8f6d0', 'Notebook', 'Ram', 'Insert', NULL, NULL, NULL, NULL, '2025-10-12 11:05:44', '2025-10-12 11:05:44', '2'),
('483a6e4d-a75b-11f0-aff6-005056b8f6d0', 'Notebook', 'Ram', 'Change', NULL, NULL, NULL, NULL, '2025-10-12 11:05:44', '2025-10-12 11:05:44', '2'),
('483a957d-a75b-11f0-aff6-005056b8f6d0', 'Notebook', 'Ram', 'Down', NULL, NULL, NULL, NULL, '2025-10-12 11:05:44', '2025-10-12 11:05:44', '2'),
('4a181df4-b889-11f0-9a0c-005056b8f6d0', 'IT Service', 'Dev Environment', 'API Unavailable', NULL, NULL, NULL, NULL, '2025-11-03 07:48:07', '2025-11-03 07:48:07', '2'),
('56276730-b2db-11f0-9a0c-005056b8f6d0', 'Security (ความปลอดภัย)', 'Access Control (การควบคุมการเข้าถึง)', 'Zero Trust Network Access (ZTNA) (การเข้าถึงเครือข่ายแบบ Zero Trust)', NULL, NULL, NULL, NULL, '2025-10-27 02:19:41', '2025-10-27 02:19:41', '2'),
('8335d116-b922-11f0-9a0c-005056b8f6d0', 'Change', 'Change Request', 'Timeline / Plan Inquiry', NULL, NULL, NULL, NULL, '2025-11-04 02:04:57', '2025-11-04 02:04:57', '2'),
('86c853e966365809ea11581594569399', 'Notebook', 'Monitor', 'Edit', 'จอแตก', 'ตกแตก', 'เครม', NULL, '2024-10-06 17:01:49', '2024-10-06 17:07:06', '2'),
('976dd1c9-b924-11f0-9a0c-005056b8f6d0', 'Hardware', 'IoT Devices / Tracker', 'SIM Testing', NULL, NULL, NULL, NULL, '2025-11-04 02:19:50', '2025-11-04 02:19:50', '2'),
('9aa9e442-ad5b-11f0-9a0c-005056b8f6d0', 'Development', 'UX/UI Design', 'Create', NULL, NULL, NULL, NULL, '2025-10-20 02:22:21', '2025-10-20 02:22:21', '2'),
('a3aa5d61-b2dc-11f0-9a0c-005056b8f6d0', 'Business Intelligence & Analytics Services (บริการด้านการวิเคราะห์ข้อมูลทางธุรกิจ)', 'Data Visualization (การแสดงผลข้อมูล)', 'Dashboard Development (การพัฒนาแดชบอร์ด)', NULL, NULL, NULL, NULL, '2025-10-27 02:29:01', '2025-10-27 02:29:01', '2'),
('a98b48ee-ad5b-11f0-9a0c-005056b8f6d0', 'Development', 'UX/UI Design', 'Edit and modify', NULL, NULL, NULL, NULL, '2025-10-20 02:22:46', '2025-10-20 02:22:46', '2'),
('b1792d02-b00f-11f0-9a0c-005056b8f6d0', 'Server', 'Service / API', 'Down', NULL, NULL, NULL, NULL, '2025-10-23 12:56:53', '2025-10-23 12:56:53', '2'),
('bb85b568-ae1a-11f0-9a0c-005056b8f6d0', 'Installation', 'Demo', 'Setup', NULL, NULL, NULL, NULL, '2025-10-21 01:10:32', '2025-10-21 01:10:32', '2'),
('bd1bc951-ad5b-11f0-9a0c-005056b8f6d0', 'Development', 'UX/UI Design', 'Delect', NULL, NULL, NULL, NULL, '2025-10-20 02:23:19', '2025-10-20 02:23:19', '2'),
('d52176e2-b328-11f0-9a0c-005056b8f6d0', 'Project Management (การบริหารโครงการ)', 'Document Management', 'Project Timeline/Schedule', NULL, NULL, NULL, NULL, '2025-10-27 11:34:25', '2025-10-27 11:34:25', '2');

--
-- Triggers `category`
--
DELIMITER $$
CREATE TRIGGER `before_insert_category` BEFORE INSERT ON `category` FOR EACH ROW BEGIN
    -- ถ้าไม่ได้กำหนดค่า id ให้สร้าง UUID ใหม่
    IF NEW.id IS NULL THEN
        SET NEW.id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `category_image`
--

CREATE TABLE `category_image` (
  `id` char(36) NOT NULL COMMENT 'รหัสรูปภาพ (UUID)',
  `file_name` varchar(255) NOT NULL COMMENT 'ชื่อไฟล์',
  `file_path` varchar(255) NOT NULL COMMENT 'ที่อยู่ไฟล์',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'ประเภทไฟล์',
  `file_size` int(11) DEFAULT NULL COMMENT 'ขนาดไฟล์',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่อัปโหลด',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตล่าสุด',
  `category_id` char(36) DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `upload_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `category_image`
--
DELIMITER $$
CREATE TRIGGER `before_insert_category_image` BEFORE INSERT ON `category_image` FOR EACH ROW BEGIN
    -- ถ้าไม่ได้กำหนดค่า id ให้สร้าง UUID ใหม่
    IF NEW.id IS NULL THEN
        SET NEW.id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` char(36) NOT NULL COMMENT '\r\nรหัสลูกค้า (UUID',
  `customer_name` varchar(255) NOT NULL COMMENT 'ชื่อลูกค้า',
  `company` varchar(255) DEFAULT NULL COMMENT 'ชื่อบริษัท',
  `position` varchar(255) DEFAULT NULL COMMENT 'ตำแหน่ง',
  `address` mediumtext DEFAULT NULL COMMENT 'ที่อยู่',
  `phone` varchar(20) DEFAULT NULL COMMENT 'เบอร์โทรศัพท์',
  `email` varchar(255) DEFAULT NULL COMMENT 'อีเมล',
  `remark` mediumtext DEFAULT NULL COMMENT 'หมายเหตุ',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_by` char(36) NOT NULL,
  `customers_image` varchar(255) DEFAULT NULL COMMENT 'รูปบริษัทลูกค้า Logo',
  `office_phone` varchar(20) DEFAULT NULL COMMENT 'เบอร์หน่วยงาน',
  `extension` varchar(10) DEFAULT NULL COMMENT 'เบอร์ต่อ',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัพเดทล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_links`
--

CREATE TABLE `document_links` (
  `id` char(36) NOT NULL,
  `project_id` char(36) NOT NULL,
  `category` varchar(50) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` char(36) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` char(36) NOT NULL COMMENT 'รหัสพนักงาน (UUID)',
  `first_name_th` varchar(255) NOT NULL COMMENT 'ชื่อพนักงาน (ภาษาไทย)',
  `last_name_th` varchar(255) NOT NULL COMMENT 'นามสกุลพนักงาน (ภาษาไทย)',
  `first_name_en` varchar(255) NOT NULL COMMENT 'ชื่อพนักงาน (ภาษาอังกฤษ)',
  `last_name_en` varchar(255) NOT NULL COMMENT 'นามสกุลพนักงาน (ภาษาอังกฤษ)',
  `gender` varchar(10) DEFAULT NULL,
  `birth_date` date DEFAULT NULL COMMENT 'วันเกิดของพนักงาน',
  `personal_email` varchar(255) DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL COMMENT 'แผนกที่สังกัด',
  `team_id` char(36) DEFAULT NULL COMMENT 'รหัสทีม',
  `supervisor_id` char(36) DEFAULT NULL COMMENT 'รหัสหัวหน้า',
  `address` text DEFAULT NULL COMMENT 'ที่อยู่',
  `hire_date` date DEFAULT NULL COMMENT 'วันที่เริ่มงาน',
  `profile_image` varchar(255) DEFAULT NULL COMMENT 'URL รูปโปรไฟล์',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้อัปเดตข้อมูล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตข้อมูลล่าสุด',
  `nickname_th` varchar(50) DEFAULT NULL COMMENT 'ชื่อเล่นภาษาไทย',
  `nickname_en` varchar(50) DEFAULT NULL COMMENT 'ชื่อเล่นภาษาอังกฤษ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `first_name_th`, `last_name_th`, `first_name_en`, `last_name_en`, `gender`, `birth_date`, `personal_email`, `company_email`, `phone`, `position`, `department`, `team_id`, `supervisor_id`, `address`, `hire_date`, `profile_image`, `created_by`, `updated_by`, `created_at`, `updated_at`, `nickname_th`, `nickname_en`) VALUES
('060ca930-3e08-42a9-b4ca-cd5e47af0d8c', 'นาง ผาณิต', 'เผ่าพันธ์', 'Panit', 'Paophan', 'female', NULL, 'panit@pointit.co.th', 'panitpaophan@gmail.com', '0814834619', 'Executive Director', 'IT Service', '4', '2f6d353b-53f1-4492-8878-bc93c18c5de9', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-05 03:48:10', 'พี่หญิง', 'Ying'),
('8a3fd425-e40f-46e2-ada9-8f9e129cac2b', 'นาย ประกอบ', 'จ้องจรัสแสง', 'Prakorb', 'Jongjarussang', NULL, NULL, NULL, NULL, '081-623-6990', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พี่กอบ', NULL),
('c8434fe7-b4b5-41a8-8166-ca8be6a7b03d', 'นาย บุลากร', 'พัวพันธุ์', 'Bulakorn', 'Puapun', NULL, NULL, NULL, NULL, '081-360-2828', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พีท', NULL),
('fce2bf50-704c-48b4-8b04-219a2c247b34', 'น.ส.ดารณี', 'ปุญญะฐิติ', 'Miss Daranee', 'Punyathiti', NULL, NULL, NULL, NULL, '096-843-7008', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอ๋', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `document_id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_category` varchar(50) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `uploaded_by` char(36) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_document_links`
--

CREATE TABLE `employee_document_links` (
  `link_id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `link_name` varchar(255) NOT NULL,
  `link_category` varchar(50) NOT NULL,
  `url` text NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` char(36) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` char(36) NOT NULL,
  `product_name` varchar(255) NOT NULL COMMENT 'ชื่อสินค้า',
  `product_description` mediumtext DEFAULT NULL COMMENT 'รายละเอียดสินค้า',
  `unit` varchar(50) DEFAULT NULL COMMENT 'หน่วยนับ',
  `cost_price` decimal(15,2) DEFAULT NULL COMMENT 'ราคาต้นทุน',
  `selling_price` decimal(15,2) DEFAULT NULL COMMENT 'ราคาขาย',
  `supplier_id` char(36) DEFAULT NULL COMMENT 'รหัสผู้จำหน่าย',
  `team_id` char(36) DEFAULT NULL COMMENT 'รหัสทีม (เชื่อมโยงกับตาราง teams)',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_by` char(36) DEFAULT NULL COMMENT 'ผู้อัพเดทข้อมูลล่าสุด',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันอัพเดทข้อมูลล่าสุด',
  `main_image` varchar(255) DEFAULT NULL COMMENT 'รูปหลักของสินค้า'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_description`, `unit`, `cost_price`, `selling_price`, `supplier_id`, `team_id`, `created_by`, `created_at`, `updated_by`, `updated_at`, `main_image`) VALUES
('3224e7a4-44ee-40ad-a6ac-22305c2b01eb', 'Smart Healthcare', 'ชุดกระเป๋า (Health Kit Set) สำหรับตรวจสุขคัดกรอกสถานะสุขภาพเคลื่อนที่ เก็บค่าข้อมูลเข้าระบบ โดยการตรวจวัดค่าจากอุปกรณ์เชื่อมต่อเข้ากับระบบ', NULL, NULL, NULL, NULL, NULL, '2', '2024-10-11 22:58:23', '2', '2024-12-04 09:27:28', '3224e7a4-44ee-40ad-a6ac-22305c2b01eb.jpg'),
('3431f4cb-f892-4e08-a9af-240a743ebc25', 'Smart Safety', 'งานเกี่ยวกับกล้องโทรทัศน์วงจรปิด\r\nและงานสายใยแก้วนำแสง\r\nรวมถึงซ่อมแซม CCTV', NULL, NULL, NULL, NULL, NULL, 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:26:48', '2', '2024-12-04 09:23:35', '3431f4cb-f892-4e08-a9af-240a743ebc25.jpg'),
('4c85d842-54f3-4f06-87e6-553f81488234', 'Smart Emergency', 'ระบบเฝ้าระวังเหตุฉุกเฉิน', NULL, NULL, NULL, NULL, NULL, '2', '2024-10-12 06:18:20', '3', '2024-10-20 13:35:30', '4c85d842-54f3-4f06-87e6-553f81488234.png'),
('54b6a0a0-54c2-448c-a340-71d12acdc5f6', 'Kudsonmoo', 'ระบบวิเคราะห์สุกร', 'ชุด', 0.00, 0.00, NULL, '1', '5', '2025-06-13 03:04:17', '5', '2025-06-16 00:48:24', '54b6a0a0-54c2-448c-a340-71d12acdc5f6.png'),
('6e2ba9df-293d-4d88-b85e-4399e237d8c0', 'K-Lynx Platform', 'Smart Management', 'ระบบ', 300000.00, 500000.00, '23722daa-6eec-4a29-aa60-89cdea4dcd8c', NULL, '3', '2025-04-09 05:50:58', NULL, '2025-04-09 05:50:58', NULL),
('7defdc10-75d8-4433-8b4f-0eeba38b674f', 'BioIDM Face Scan', 'ระบบยืนยันตัวตน ผ่านการเปรียบเทียบใบหน้า บัตรประจำตัวประชาชน และอื่นๆ', NULL, NULL, NULL, NULL, NULL, '2', '2024-10-11 23:18:48', NULL, '2024-10-11 23:52:54', ''),
('b9fcda13-e694-4e04-a8df-fdf27ee08979', 'IBOC', 'มหาวิทยาลัยขอนแก่น', NULL, NULL, NULL, NULL, NULL, '2', '2024-10-11 23:19:12', '3', '2024-10-11 23:54:16', ''),
('c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 'Smart Showroom', 'ระบบ AI เพื่อเพิ่มประสิทธิภาพของ Showroom', 'ระบบ', 25000.00, 50000.00, '23722daa-6eec-4a29-aa60-89cdea4dcd8c', NULL, '3', '2025-04-09 06:09:43', NULL, '2025-04-09 06:09:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_documents`
--

CREATE TABLE `product_documents` (
  `id` char(36) NOT NULL COMMENT 'รหัสเอกสาร (UUID)',
  `product_id` char(36) DEFAULT NULL COMMENT 'รหัสสินค้า (เชื่อมโยงกับตาราง products)',
  `document_type` enum('presentation','specification','manual','other') DEFAULT NULL COMMENT 'ประเภทเอกสาร',
  `file_path` varchar(255) DEFAULT NULL COMMENT 'ที่อยู่ของไฟล์',
  `file_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อไฟล์',
  `file_size` int(11) DEFAULT NULL COMMENT 'ขนาดไฟล์ (bytes)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้าง',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัพเดทล่าสุด',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้ที่อัพเดทล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บเอกสารและไฟล์ที่เกี่ยวข้องกับสินค้า';

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` char(36) NOT NULL COMMENT 'รหัสรูปภาพ (UUID)',
  `product_id` char(36) NOT NULL COMMENT 'รหัสสินค้า (เชื่อมโยงกับตาราง products)',
  `image_path` varchar(255) DEFAULT NULL COMMENT 'ที่อยู่ของรูปภาพ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้าง',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัพเดทล่าสุด',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้ที่อัพเดทล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บรูปภาพเพิ่มเติมของสินค้า';

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` char(36) NOT NULL COMMENT 'รหัสโปรเจคต์ (UUID)',
  `project_name` varchar(255) NOT NULL COMMENT 'ชื่อโปรเจคต์',
  `start_date` date DEFAULT NULL COMMENT 'วันที่เริ่มโปรเจคต์',
  `end_date` date DEFAULT NULL COMMENT 'วันสิ้นสุดโปรเจคต์',
  `status` varchar(50) DEFAULT NULL COMMENT 'สถานะของโปรเจคต์',
  `contract_no` varchar(50) DEFAULT NULL COMMENT 'หมายเลขสัญญา',
  `remark` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `sales_date` date DEFAULT NULL COMMENT 'วันที่สิ้นสุดขาย',
  `seller` char(36) DEFAULT NULL COMMENT 'รหัสผู้ขาย',
  `team_id` char(36) DEFAULT NULL COMMENT 'ทีมที่รับผิดชอบโครงการ',
  `sale_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'ยอดขายไม่รวมภาษี',
  `sale_vat` decimal(10,2) DEFAULT NULL COMMENT 'ยอดขายรวมภาษี',
  `cost_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'ต้นทุนไม่รวมภาษี',
  `cost_vat` decimal(10,2) DEFAULT NULL COMMENT 'ต้นทุนรวมภาษี',
  `gross_profit` decimal(10,2) DEFAULT NULL COMMENT 'กำไรขั้นต้น',
  `potential` decimal(5,2) DEFAULT NULL COMMENT 'กำไรขั้นต้นแบบเปอร์เซ็นต์',
  `es_sale_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'ยอดขายที่คาดการณ์ (ไม่รวมภาษี)',
  `es_cost_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'ต้นทุนที่คาดการณ์ (ไม่รวมภาษี)',
  `es_gp_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'กำไรที่คาดการณ์ (ไม่รวมภาษี)',
  `customer_id` char(36) DEFAULT NULL COMMENT 'รหัสลูกค้า (เชื่อมโยงกับตาราง customers)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขข้อมูลล่าสุด',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้ที่แก้ไขข้อมูล',
  `product_id` char(36) DEFAULT NULL COMMENT 'รหัสสินค้า (เชื่อมโยงกับตาราง products)',
  `vat` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'ภาษีมูลค่าเพิ่ม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางโปรเจคต์';

-- --------------------------------------------------------

--
-- Table structure for table `project_costs`
--

CREATE TABLE `project_costs` (
  `cost_id` char(36) NOT NULL COMMENT 'รหัสต้นทุน (UUID)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (เชื่อมโยงกับตาราง projects)',
  `type` varchar(100) NOT NULL COMMENT 'ประเภท (Hardware, Software, etc.)',
  `part_no` varchar(100) NOT NULL COMMENT 'รหัสสินค้า/บริการ',
  `description` text NOT NULL COMMENT 'รายละเอียด',
  `quantity` int(11) NOT NULL COMMENT 'จำนวน',
  `unit` varchar(50) DEFAULT NULL,
  `price_per_unit` decimal(15,2) NOT NULL COMMENT 'ราคาต่อหน่วย',
  `total_amount` decimal(15,2) GENERATED ALWAYS AS (`quantity` * `price_per_unit`) STORED COMMENT 'ยอดรวม (จำนวน × ราคาต่อหน่วย)',
  `cost_per_unit` decimal(15,2) NOT NULL COMMENT 'ต้นทุนต่อหน่วย',
  `total_cost` decimal(15,2) GENERATED ALWAYS AS (`quantity` * `cost_per_unit`) STORED COMMENT 'ต้นทุนรวม (จำนวน × ต้นทุนต่อหน่วย)',
  `supplier` varchar(255) NOT NULL COMMENT 'ชื่อซัพพลายเออร์',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างรายการ',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างรายการ',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตล่าสุด',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้ที่อัปเดตล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บรายการต้นทุนโครงการ';

--
-- Triggers `project_costs`
--
DELIMITER $$
CREATE TRIGGER `before_insert_project_costs` BEFORE INSERT ON `project_costs` FOR EACH ROW BEGIN
    IF NEW.cost_id IS NULL THEN
        SET NEW.cost_id = UUID();
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_update_project_costs` BEFORE UPDATE ON `project_costs` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `project_cost_summary`
--

CREATE TABLE `project_cost_summary` (
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ',
  `total_amount` decimal(15,2) DEFAULT 0.00 COMMENT 'ยอดรวมทั้งหมด (ไม่รวมภาษี)',
  `vat_amount` decimal(15,2) DEFAULT 0.00 COMMENT 'ภาษีมูลค่าเพิ่ม (VAT)',
  `grand_total` decimal(15,2) DEFAULT 0.00 COMMENT 'ยอดรวมสุทธิ (รวมภาษี)',
  `total_cost` decimal(15,2) DEFAULT 0.00 COMMENT 'ต้นทุนรวมทั้งหมด (ไม่รวมภาษี)',
  `cost_vat_amount` decimal(15,2) DEFAULT 0.00 COMMENT 'ภาษีมูลค่าเพิ่มของต้นทุน',
  `total_cost_with_vat` decimal(15,2) DEFAULT 0.00 COMMENT 'ต้นทุนรวมทั้งหมด (รวมภาษี)',
  `profit_amount` decimal(15,2) DEFAULT 0.00 COMMENT 'กำไรขั้นต้น',
  `profit_percentage` decimal(5,2) DEFAULT 0.00 COMMENT 'เปอร์เซ็นต์กำไร',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางสรุปต้นทุนโครงการ';

-- --------------------------------------------------------

--
-- Table structure for table `project_customers`
--

CREATE TABLE `project_customers` (
  `id` char(36) NOT NULL COMMENT 'รหัสความสัมพันธ์ (UUID)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (เชื่อมโยงกับตาราง projects)',
  `customer_id` char(36) NOT NULL COMMENT 'รหัสลูกค้า (เชื่อมโยงกับตาราง customers)',
  `is_primary` tinyint(1) DEFAULT 0 COMMENT 'สถานะลูกค้าหลักของโครงการ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างความสัมพันธ์'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บความสัมพันธ์ระหว่างโครงการกับลูกค้า';

-- --------------------------------------------------------

--
-- Table structure for table `project_discussions`
--

CREATE TABLE `project_discussions` (
  `discussion_id` char(36) NOT NULL,
  `project_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `message_text` text DEFAULT NULL,
  `is_edited` tinyint(1) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_discussion_attachments`
--

CREATE TABLE `project_discussion_attachments` (
  `attachment_id` char(36) NOT NULL,
  `discussion_id` char(36) NOT NULL,
  `project_id` char(36) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_extension` varchar(10) DEFAULT NULL,
  `uploaded_by` char(36) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_discussion_mentions`
--

CREATE TABLE `project_discussion_mentions` (
  `mention_id` char(36) NOT NULL,
  `discussion_id` char(36) NOT NULL,
  `mentioned_user_id` char(36) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_documents`
--

CREATE TABLE `project_documents` (
  `document_id` char(36) NOT NULL COMMENT 'รหัสเอกสาร (UUID)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (เชื่อมโยงกับตาราง projects)',
  `document_name` varchar(255) NOT NULL COMMENT 'ชื่อเอกสาร',
  `document_type` varchar(50) NOT NULL COMMENT 'ประเภทเอกสาร',
  `file_path` varchar(255) NOT NULL COMMENT 'ที่อยู่ไฟล์',
  `file_size` int(11) DEFAULT NULL COMMENT 'ขนาดไฟล์ (หน่วยเป็นไบต์)',
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่อัปโหลด',
  `uploaded_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้อัปโหลด (เชื่อมโยงกับตาราง users)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_images`
--

CREATE TABLE `project_images` (
  `image_id` char(36) NOT NULL COMMENT 'รหัสรูปภาพ (UUID)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (เชื่อมโยงกับตาราง projects)',
  `image_name` varchar(255) NOT NULL COMMENT 'ชื่อรูปภาพ',
  `file_path` varchar(255) NOT NULL COMMENT 'ที่อยู่ไฟล์',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'ประเภทไฟล์',
  `file_size` int(11) DEFAULT NULL COMMENT 'ขนาดไฟล์ (หน่วยเป็นไบต์)',
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่อัปโหลด',
  `uploaded_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้อัปโหลด (เชื่อมโยงกับตาราง users)',
  `description` text DEFAULT NULL COMMENT 'คำอธิบายรูปภาพ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_members`
--

CREATE TABLE `project_members` (
  `member_id` char(36) NOT NULL COMMENT 'รหัสสมาชิกในโครงการ (UUID)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (เชื่อมโยงกับตาราง projects)',
  `user_id` char(36) NOT NULL COMMENT 'รหัสพนักงาน (เชื่อมโยงกับตาราง users)',
  `role_id` char(36) NOT NULL COMMENT 'รหัสบทบาทในโครงการ (เชื่อมโยงกับตาราง project_roles)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'สถานะการเป็นสมาชิก (1=ยังเป็นสมาชิก, 0=พ้นสภาพ)',
  `joined_date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่เข้าร่วมโครงการ',
  `left_date` timestamp NULL DEFAULT NULL COMMENT 'วันที่ออกจากโครงการ',
  `created_by` char(36) NOT NULL COMMENT 'ผู้เพิ่มสมาชิก',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่เพิ่มข้อมูล',
  `updated_by` char(36) DEFAULT NULL COMMENT 'ผู้แก้ไขล่าสุด',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด',
  `remark` text DEFAULT NULL COMMENT 'หมายเหตุเพิ่มเติม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บข้อมูลสมาชิกในโครงการ';

--
-- Triggers `project_members`
--
DELIMITER $$
CREATE TRIGGER `before_insert_project_members` BEFORE INSERT ON `project_members` FOR EACH ROW BEGIN
    IF NEW.member_id IS NULL THEN
        SET NEW.member_id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `project_payments`
--

CREATE TABLE `project_payments` (
  `payment_id` char(36) NOT NULL COMMENT 'รหัสการชำระเงิน (UUID)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (เชื่อมโยงกับตาราง projects)',
  `payment_number` int(11) NOT NULL COMMENT 'งวดที่',
  `amount` decimal(10,2) NOT NULL COMMENT 'จำนวนเงินที่ต้องชำระ',
  `payment_percentage` decimal(5,2) NOT NULL COMMENT 'เปอร์เซ็นต์การชำระต่องวด',
  `amount_paid` decimal(10,2) DEFAULT 0.00 COMMENT 'จำนวนเงินที่ชำระแล้ว',
  `remaining_amount` decimal(10,2) GENERATED ALWAYS AS (`amount` - `amount_paid`) STORED COMMENT 'จำนวนเงินคงเหลือ',
  `payment_progress` decimal(5,2) GENERATED ALWAYS AS (`amount_paid` / `amount` * 100) STORED COMMENT 'ความคืบหน้าการชำระ (%)',
  `payment_date` date DEFAULT NULL COMMENT 'วันที่ชำระ',
  `due_date` date DEFAULT NULL COMMENT 'วันครบกำหนดชำระ',
  `status` enum('Pending','Partial','Paid','Overdue') DEFAULT 'Pending' COMMENT 'สถานะการชำระ',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'วิธีการชำระเงิน',
  `transaction_id` varchar(100) DEFAULT NULL COMMENT 'รหัสอ้างอิงการทำธุรกรรม',
  `remark` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างรายการ',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตล่าสุด',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างรายการ (เชื่อมโยงกับตาราง users)',
  `updated_by` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_roles`
--

CREATE TABLE `project_roles` (
  `role_id` char(36) NOT NULL COMMENT 'รหัสบทบาท (UUID)',
  `role_name` varchar(100) NOT NULL COMMENT 'ชื่อบทบาท',
  `role_description` text DEFAULT NULL COMMENT 'คำอธิบายบทบาท',
  `created_by` char(36) NOT NULL COMMENT 'ผู้สร้างบทบาท',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง',
  `updated_by` char(36) DEFAULT NULL COMMENT 'ผู้แก้ไขล่าสุด',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บข้อมูลบทบาทในโครงการ';

-- --------------------------------------------------------

--
-- Table structure for table `project_tasks`
--

CREATE TABLE `project_tasks` (
  `task_id` char(36) NOT NULL COMMENT 'รหัสงาน (UUID)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (เชื่อมโยงกับตาราง projects)',
  `parent_task_id` char(36) DEFAULT NULL COMMENT 'รหัสงานแม่ (ถ้าเป็น Sub Task)',
  `task_name` varchar(255) NOT NULL COMMENT 'ชื่องาน',
  `description` text DEFAULT NULL COMMENT 'รายละเอียดงาน',
  `start_date` date DEFAULT NULL COMMENT 'วันที่เริ่มงาน',
  `end_date` date DEFAULT NULL COMMENT 'วันที่สิ้นสุดงาน',
  `status` enum('Pending','In Progress','Completed','Cancelled') DEFAULT 'Pending' COMMENT 'สถานะงาน',
  `progress` decimal(5,2) DEFAULT 0.00 COMMENT 'ความคืบหน้า (%)',
  `priority` enum('Low','Medium','High','Urgent') DEFAULT 'Medium' COMMENT 'ระดับความสำคัญ',
  `created_by` char(36) NOT NULL COMMENT 'รหัสผู้สร้างงาน',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้แก้ไขล่าสุด',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด',
  `task_order` int(11) DEFAULT 0,
  `task_level` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บข้อมูลงานในโครงการ';

-- --------------------------------------------------------

--
-- Table structure for table `project_task_assignments`
--

CREATE TABLE `project_task_assignments` (
  `assignment_id` char(36) NOT NULL COMMENT 'รหัสการมอบหมายงาน (UUID)',
  `task_id` char(36) NOT NULL COMMENT 'รหัสงาน',
  `user_id` char(36) NOT NULL COMMENT 'รหัสผู้รับผิดชอบ',
  `assigned_by` char(36) NOT NULL COMMENT 'รหัสผู้มอบหมายงาน',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่มอบหมายงาน'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บการมอบหมายงาน';

-- --------------------------------------------------------

--
-- Table structure for table `service_sla_impacts`
--

CREATE TABLE `service_sla_impacts` (
  `impact_id` char(36) NOT NULL,
  `impact_name` varchar(100) NOT NULL,
  `impact_level` enum('High','Medium','Low') NOT NULL DEFAULT 'Medium',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_sla_impacts`
--

INSERT INTO `service_sla_impacts` (`impact_id`, `impact_name`, `impact_level`, `active`, `created_at`, `updated_at`) VALUES
('cb479fb7-a5ca-11f0-aff6-005056b8f6d0', 'Organization', 'High', 1, '2025-10-10 11:18:53', NULL),
('cb4922cd-a5ca-11f0-aff6-005056b8f6d0', 'Multiple Sites', 'High', 1, '2025-10-10 11:18:53', NULL),
('cb49276a-a5ca-11f0-aff6-005056b8f6d0', 'Executive', 'High', 1, '2025-10-10 11:18:53', NULL),
('cb492843-a5ca-11f0-aff6-005056b8f6d0', 'Site', 'Medium', 1, '2025-10-10 11:18:53', NULL),
('cb492901-a5ca-11f0-aff6-005056b8f6d0', 'Department', 'Medium', 1, '2025-10-10 11:18:53', NULL),
('cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'Application', 'Medium', 1, '2025-10-10 11:18:53', NULL),
('cb492a43-a5ca-11f0-aff6-005056b8f6d0', 'Multiple Users', 'Medium', 1, '2025-10-10 11:18:53', NULL),
('cb492ae8-a5ca-11f0-aff6-005056b8f6d0', 'Remote Users', 'Low', 1, '2025-10-10 11:18:53', NULL),
('cb492b96-a5ca-11f0-aff6-005056b8f6d0', 'Single User', 'Low', 1, '2025-10-10 11:18:53', NULL),
('cb492c37-a5ca-11f0-aff6-005056b8f6d0', 'External', 'Low', 1, '2025-10-10 11:18:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_sla_priority_matrix`
--

CREATE TABLE `service_sla_priority_matrix` (
  `id` char(36) NOT NULL,
  `impact_id` char(36) NOT NULL,
  `urgency` enum('High','Medium','Low') NOT NULL,
  `priority` enum('Critical','High','Medium','Low') NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_sla_priority_matrix`
--

INSERT INTO `service_sla_priority_matrix` (`id`, `impact_id`, `urgency`, `priority`, `updated_at`) VALUES
('cb5e15de-a5ca-11f0-aff6-005056b8f6d0', 'cb49276a-a5ca-11f0-aff6-005056b8f6d0', 'High', 'Critical', '2025-10-10 11:18:53'),
('cb5fdef5-a5ca-11f0-aff6-005056b8f6d0', 'cb4922cd-a5ca-11f0-aff6-005056b8f6d0', 'High', 'Critical', '2025-10-10 11:18:53'),
('cb5fe62a-a5ca-11f0-aff6-005056b8f6d0', 'cb479fb7-a5ca-11f0-aff6-005056b8f6d0', 'High', 'Critical', '2025-10-10 11:18:53'),
('cb8beeec-a5ca-11f0-aff6-005056b8f6d0', 'cb49276a-a5ca-11f0-aff6-005056b8f6d0', 'Medium', 'High', '2025-10-10 11:18:53'),
('cb8e5b1b-a5ca-11f0-aff6-005056b8f6d0', 'cb4922cd-a5ca-11f0-aff6-005056b8f6d0', 'Medium', 'High', '2025-10-10 11:18:53'),
('cb8e5e1c-a5ca-11f0-aff6-005056b8f6d0', 'cb479fb7-a5ca-11f0-aff6-005056b8f6d0', 'Medium', 'High', '2025-10-10 11:18:53'),
('cba61523-a5ca-11f0-aff6-005056b8f6d0', 'cb49276a-a5ca-11f0-aff6-005056b8f6d0', 'Low', 'Medium', '2025-10-10 11:18:54'),
('cba7fdbb-a5ca-11f0-aff6-005056b8f6d0', 'cb4922cd-a5ca-11f0-aff6-005056b8f6d0', 'Low', 'Medium', '2025-10-10 11:18:54'),
('cba800d3-a5ca-11f0-aff6-005056b8f6d0', 'cb479fb7-a5ca-11f0-aff6-005056b8f6d0', 'Low', 'Medium', '2025-10-10 11:18:54'),
('cbbf1db0-a5ca-11f0-aff6-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'High', 'High', '2025-10-10 11:18:54'),
('cbc11c8c-a5ca-11f0-aff6-005056b8f6d0', 'cb492901-a5ca-11f0-aff6-005056b8f6d0', 'High', 'High', '2025-10-10 11:18:54'),
('cbc11f95-a5ca-11f0-aff6-005056b8f6d0', 'cb492a43-a5ca-11f0-aff6-005056b8f6d0', 'High', 'High', '2025-10-10 11:18:54'),
('cbc121cf-a5ca-11f0-aff6-005056b8f6d0', 'cb492843-a5ca-11f0-aff6-005056b8f6d0', 'High', 'High', '2025-10-10 11:18:54'),
('cbd3712b-a5ca-11f0-aff6-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'Medium', 'Medium', '2025-10-10 11:18:54'),
('cbd3a1ad-a5ca-11f0-aff6-005056b8f6d0', 'cb492901-a5ca-11f0-aff6-005056b8f6d0', 'Medium', 'Medium', '2025-10-10 11:18:54'),
('cbd3a477-a5ca-11f0-aff6-005056b8f6d0', 'cb492a43-a5ca-11f0-aff6-005056b8f6d0', 'Medium', 'Medium', '2025-10-10 11:18:54'),
('cbd3a6b3-a5ca-11f0-aff6-005056b8f6d0', 'cb492843-a5ca-11f0-aff6-005056b8f6d0', 'Medium', 'Medium', '2025-10-10 11:18:54'),
('cbe3d886-a5ca-11f0-aff6-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'Low', 'Low', '2025-10-10 11:18:54'),
('cbe4074d-a5ca-11f0-aff6-005056b8f6d0', 'cb492901-a5ca-11f0-aff6-005056b8f6d0', 'Low', 'Low', '2025-10-10 11:18:54'),
('cbe40bd2-a5ca-11f0-aff6-005056b8f6d0', 'cb492a43-a5ca-11f0-aff6-005056b8f6d0', 'Low', 'Low', '2025-10-10 11:18:54'),
('cbe40fe3-a5ca-11f0-aff6-005056b8f6d0', 'cb492843-a5ca-11f0-aff6-005056b8f6d0', 'Low', 'Low', '2025-10-10 11:18:54');

-- --------------------------------------------------------

--
-- Table structure for table `service_sla_targets`
--

CREATE TABLE `service_sla_targets` (
  `id` char(36) NOT NULL,
  `priority` enum('Critical','High','Medium','Low') NOT NULL,
  `sla_hours` int(11) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_sla_targets`
--

INSERT INTO `service_sla_targets` (`id`, `priority`, `sla_hours`, `updated_at`) VALUES
('cb1cea48-a5ca-11f0-aff6-005056b8f6d0', 'Critical', 4, '2025-10-10 11:18:53'),
('cb1d0ffa-a5ca-11f0-aff6-005056b8f6d0', 'High', 24, '2025-10-23 13:11:39'),
('cb1d1190-a5ca-11f0-aff6-005056b8f6d0', 'Medium', 72, '2025-10-23 13:11:39'),
('cb1d125b-a5ca-11f0-aff6-005056b8f6d0', 'Low', 168, '2025-10-23 13:11:40');

-- --------------------------------------------------------

--
-- Table structure for table `service_sla_time_matrix`
--

CREATE TABLE `service_sla_time_matrix` (
  `id` char(36) NOT NULL,
  `impact_id` char(36) NOT NULL,
  `urgency` enum('High','Medium','Low') NOT NULL,
  `priority` enum('Critical','High','Medium','Low') NOT NULL,
  `sla_hours` int(11) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_sla_time_matrix`
--

INSERT INTO `service_sla_time_matrix` (`id`, `impact_id`, `urgency`, `priority`, `sla_hours`, `updated_at`) VALUES
('1658a22f-b011-11f0-9a0c-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'High', 'Critical', 1, '2025-10-23 13:06:52'),
('16765331-b011-11f0-9a0c-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'High', 'High', 4, '2025-10-23 13:15:19'),
('16836a66-b011-11f0-9a0c-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'High', 'Medium', 8, '2025-10-23 13:06:52'),
('16a97682-b011-11f0-9a0c-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'High', 'Low', 24, '2025-10-23 13:15:20'),
('16c22a43-b011-11f0-9a0c-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'Medium', 'Critical', 2, '2025-10-23 13:06:53'),
('16cd5e1f-b011-11f0-9a0c-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'Medium', 'High', 6, '2025-10-23 13:06:53'),
('16dae2db-b011-11f0-9a0c-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'Medium', 'Medium', 24, '2025-10-23 13:15:20'),
('16e96825-b011-11f0-9a0c-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'Medium', 'Low', 72, '2025-10-23 13:15:20'),
('16f504db-b011-11f0-9a0c-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'Low', 'Critical', 4, '2025-10-23 13:15:20'),
('16feac0a-b011-11f0-9a0c-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'Low', 'High', 8, '2025-10-23 13:15:20'),
('170a4be8-b011-11f0-9a0c-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'Low', 'Medium', 72, '2025-10-23 13:15:21'),
('17145020-b011-11f0-9a0c-005056b8f6d0', 'cb4929a6-a5ca-11f0-aff6-005056b8f6d0', 'Low', 'Low', 168, '2025-10-23 13:10:08');

-- --------------------------------------------------------

--
-- Table structure for table `service_tickets`
--

CREATE TABLE `service_tickets` (
  `ticket_id` char(36) NOT NULL COMMENT 'รหัส Ticket (UUID)',
  `ticket_no` varchar(50) NOT NULL COMMENT 'เลข Ticket (เช่น TCK-202510-0001)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ',
  `ticket_type` enum('Incident','Service','Change') NOT NULL DEFAULT 'Incident' COMMENT 'ประเภท Ticket',
  `subject` varchar(150) NOT NULL COMMENT 'หัวข้อ',
  `description` text DEFAULT NULL COMMENT 'รายละเอียด/อาการ',
  `status` enum('Draft','New','On Process','Pending','Waiting for Approval','Scheduled','Resolved','Resolved Pending','Containment','Closed','Canceled') NOT NULL DEFAULT 'New' COMMENT 'สถานะ',
  `priority` enum('Critical','High','Medium','Low') NOT NULL DEFAULT 'Low' COMMENT 'ความสำคัญ',
  `urgency` enum('High','Medium','Low') NOT NULL DEFAULT 'Low' COMMENT 'ความเร่งด่วน',
  `impact` varchar(100) DEFAULT NULL COMMENT 'ผลกระทบ',
  `service_category` varchar(255) DEFAULT NULL COMMENT 'หมวดหมู่บริการ',
  `category` varchar(255) DEFAULT NULL COMMENT 'หมวดหมู่',
  `sub_category` varchar(255) DEFAULT NULL COMMENT 'หมวดหมู่ย่อย',
  `job_owner` char(36) DEFAULT NULL COMMENT 'รหัสผู้รับผิดชอบ',
  `reporter` char(36) DEFAULT NULL COMMENT 'รหัสผู้แจ้ง',
  `source` varchar(100) DEFAULT NULL COMMENT 'ช่องทางแจ้ง (Email, Call Center, Portal, etc.)',
  `sla_target` int(11) DEFAULT NULL COMMENT 'SLA เป้าหมาย (ชั่วโมง)',
  `sla_deadline` datetime DEFAULT NULL COMMENT 'วันเวลาครบ SLA (คำนวณอัตโนมัติ)',
  `sla_status` enum('Within SLA','Near SLA','Overdue') DEFAULT 'Within SLA' COMMENT 'สถานะ SLA',
  `start_at` datetime DEFAULT NULL COMMENT 'วันเวลาเริ่มดำเนินการ',
  `due_at` datetime DEFAULT NULL COMMENT 'วันเวลากำหนดเสร็จ',
  `resolved_at` datetime DEFAULT NULL COMMENT 'วันเวลาแก้ไขเสร็จ',
  `closed_at` datetime DEFAULT NULL COMMENT 'วันเวลาปิด Ticket',
  `channel` enum('Onsite','Remote','Office') DEFAULT NULL COMMENT 'ช่องทางการทำงาน',
  `deleted_at` datetime DEFAULT NULL COMMENT 'วันเวลาลบ (Soft Delete)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันเวลาสร้าง',
  `created_by` char(36) NOT NULL COMMENT 'ผู้สร้าง',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันเวลาอัปเดตล่าสุด',
  `updated_by` char(36) DEFAULT NULL COMMENT 'ผู้อัปเดตล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บข้อมูล Service Ticket หลัก';

--
-- Triggers `service_tickets`
--
DELIMITER $$
CREATE TRIGGER `after_update_service_tickets` AFTER UPDATE ON `service_tickets` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_service_tickets` BEFORE INSERT ON `service_tickets` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_update_service_tickets` BEFORE UPDATE ON `service_tickets` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `service_ticket_attachments`
--

CREATE TABLE `service_ticket_attachments` (
  `attachment_id` char(36) NOT NULL COMMENT 'รหัสไฟล์แนบ (UUID)',
  `ticket_id` char(36) NOT NULL COMMENT 'รหัส Ticket',
  `file_name` varchar(255) NOT NULL COMMENT 'ชื่อไฟล์',
  `file_path` varchar(500) NOT NULL COMMENT 'ที่อยู่ไฟล์',
  `file_size` bigint(20) DEFAULT NULL COMMENT 'ขนาดไฟล์ (bytes)',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'ประเภทไฟล์ (jpg, pdf, docx, etc.)',
  `mime_type` varchar(100) DEFAULT NULL COMMENT 'MIME Type',
  `uploaded_by` char(36) NOT NULL COMMENT 'ผู้อัปโหลด',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันเวลาอัปโหลด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บไฟล์แนบของ Ticket';

--
-- Triggers `service_ticket_attachments`
--
DELIMITER $$
CREATE TRIGGER `before_insert_service_ticket_attachments` BEFORE INSERT ON `service_ticket_attachments` FOR EACH ROW BEGIN
    IF NEW.attachment_id IS NULL OR NEW.attachment_id = '' THEN
        SET NEW.attachment_id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `service_ticket_comments`
--

CREATE TABLE `service_ticket_comments` (
  `comment_id` char(36) NOT NULL COMMENT 'รหัสความคิดเห็น (UUID)',
  `ticket_id` char(36) NOT NULL COMMENT 'รหัส Ticket',
  `comment` text NOT NULL COMMENT 'ความคิดเห็น',
  `is_internal` tinyint(1) DEFAULT 0 COMMENT 'ความคิดเห็นภายใน (1=ใช่, 0=ไม่ใช่)',
  `created_by` char(36) NOT NULL COMMENT 'ผู้สร้าง',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันเวลาสร้าง',
  `updated_by` char(36) DEFAULT NULL COMMENT 'ผู้อัปเดต',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันเวลาอัปเดต',
  `deleted_at` datetime DEFAULT NULL COMMENT 'วันเวลาลบ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บความคิดเห็น/หมายเหตุ';

--
-- Triggers `service_ticket_comments`
--
DELIMITER $$
CREATE TRIGGER `before_insert_service_ticket_comments` BEFORE INSERT ON `service_ticket_comments` FOR EACH ROW BEGIN
    IF NEW.comment_id IS NULL OR NEW.comment_id = '' THEN
        SET NEW.comment_id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `service_ticket_history`
--

CREATE TABLE `service_ticket_history` (
  `history_id` char(36) NOT NULL COMMENT 'รหัสประวัติ (UUID)',
  `ticket_id` char(36) NOT NULL COMMENT 'รหัส Ticket',
  `field_name` varchar(100) NOT NULL COMMENT 'ชื่อฟิลด์ที่เปลี่ยน',
  `old_value` text DEFAULT NULL COMMENT 'ค่าเดิม',
  `new_value` text DEFAULT NULL COMMENT 'ค่าใหม่',
  `changed_by` char(36) NOT NULL COMMENT 'ผู้เปลี่ยนแปลง',
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันเวลาเปลี่ยนแปลง'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางบันทึกประวัติการเปลี่ยนแปลงข้อมูล';

--
-- Triggers `service_ticket_history`
--
DELIMITER $$
CREATE TRIGGER `before_insert_service_ticket_history` BEFORE INSERT ON `service_ticket_history` FOR EACH ROW BEGIN
    IF NEW.history_id IS NULL OR NEW.history_id = '' THEN
        SET NEW.history_id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `service_ticket_notifications`
--

CREATE TABLE `service_ticket_notifications` (
  `notification_id` char(36) NOT NULL COMMENT 'รหัสการแจ้งเตือน (UUID)',
  `ticket_id` char(36) NOT NULL COMMENT 'รหัส Ticket',
  `user_id` char(36) NOT NULL COMMENT 'รหัสผู้รับแจ้งเตือน',
  `type` enum('SLA_NEAR','SLA_OVERDUE','STATUS_CHANGE','NEW_COMMENT','ASSIGNED','MENTIONED') NOT NULL COMMENT 'ประเภทการแจ้งเตือน',
  `message` text NOT NULL COMMENT 'ข้อความแจ้งเตือน',
  `is_read` tinyint(1) DEFAULT 0 COMMENT 'อ่านแล้ว (1=ใช่, 0=ยังไม่อ่าน)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันเวลาสร้าง',
  `read_at` datetime DEFAULT NULL COMMENT 'วันเวลาที่อ่าน'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บการแจ้งเตือน';

--
-- Triggers `service_ticket_notifications`
--
DELIMITER $$
CREATE TRIGGER `before_insert_service_ticket_notifications` BEFORE INSERT ON `service_ticket_notifications` FOR EACH ROW BEGIN
    IF NEW.notification_id IS NULL OR NEW.notification_id = '' THEN
        SET NEW.notification_id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `service_ticket_onsite`
--

CREATE TABLE `service_ticket_onsite` (
  `onsite_id` char(36) NOT NULL COMMENT 'รหัส Onsite (UUID)',
  `ticket_id` char(36) NOT NULL COMMENT 'รหัส Ticket',
  `start_location` varchar(255) DEFAULT NULL COMMENT 'สถานที่เริ่มต้น',
  `end_location` varchar(255) DEFAULT NULL COMMENT 'สถานที่ปลายทาง',
  `travel_mode` varchar(100) DEFAULT NULL COMMENT 'วิธีการเดินทาง (รถส่วนตัว, รถบริษัท, etc.)',
  `travel_note` varchar(255) DEFAULT NULL COMMENT 'หมายเหตุพาหนะ',
  `odometer_start` decimal(10,2) DEFAULT NULL COMMENT 'เลขไมล์เริ่มต้น',
  `odometer_end` decimal(10,2) DEFAULT NULL COMMENT 'เลขไมล์สิ้นสุด',
  `distance` decimal(10,2) GENERATED ALWAYS AS (`odometer_end` - `odometer_start`) STORED COMMENT 'ระยะทาง (คำนวณอัตโนมัติ)',
  `note` text DEFAULT NULL COMMENT 'หมายเหตุเพิ่มเติม',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันเวลาสร้าง',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันเวลาอัปเดต'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บข้อมูล Onsite Details';

--
-- Triggers `service_ticket_onsite`
--
DELIMITER $$
CREATE TRIGGER `before_insert_service_ticket_onsite` BEFORE INSERT ON `service_ticket_onsite` FOR EACH ROW BEGIN
    IF NEW.onsite_id IS NULL OR NEW.onsite_id = '' THEN
        SET NEW.onsite_id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `service_ticket_timeline`
--

CREATE TABLE `service_ticket_timeline` (
  `timeline_id` char(36) NOT NULL COMMENT 'รหัส Timeline (UUID)',
  `ticket_id` char(36) NOT NULL COMMENT 'รหัส Ticket',
  `order` int(11) NOT NULL COMMENT 'ลำดับ',
  `actor` varchar(255) NOT NULL COMMENT 'ผู้ดำเนินการ',
  `action` varchar(500) NOT NULL COMMENT 'การกระทำ',
  `detail` text DEFAULT NULL COMMENT 'รายละเอียด',
  `attachment` varchar(255) DEFAULT NULL COMMENT 'ไฟล์แนบ',
  `location` varchar(255) DEFAULT NULL COMMENT 'สถานที่/ช่องทาง',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันเวลาสร้าง'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บ Timeline/ประวัติการดำเนินการ';

--
-- Triggers `service_ticket_timeline`
--
DELIMITER $$
CREATE TRIGGER `before_insert_service_ticket_timeline` BEFORE INSERT ON `service_ticket_timeline` FOR EACH ROW BEGIN
    IF NEW.timeline_id IS NULL OR NEW.timeline_id = '' THEN
        SET NEW.timeline_id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `service_ticket_watchers`
--

CREATE TABLE `service_ticket_watchers` (
  `watcher_id` char(36) NOT NULL COMMENT 'รหัส Watcher (UUID)',
  `ticket_id` char(36) NOT NULL COMMENT 'รหัส Ticket',
  `user_id` char(36) NOT NULL COMMENT 'รหัสผู้ติดตาม',
  `added_by` char(36) DEFAULT NULL COMMENT 'ผู้เพิ่ม',
  `added_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันเวลาเพิ่ม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บผู้ติดตาม Ticket';

--
-- Triggers `service_ticket_watchers`
--
DELIMITER $$
CREATE TRIGGER `before_insert_service_ticket_watchers` BEFORE INSERT ON `service_ticket_watchers` FOR EACH ROW BEGIN
    IF NEW.watcher_id IS NULL OR NEW.watcher_id = '' THEN
        SET NEW.watcher_id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` char(36) NOT NULL,
  `supplier_name` varchar(255) NOT NULL COMMENT 'ชื่อผู้จำหน่าย/ซัพพลายเออร์',
  `company` varchar(255) DEFAULT NULL COMMENT 'ชื่อบริษัท/องค์กร',
  `contact_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อผู้ติดต่อ',
  `position` varchar(255) DEFAULT NULL COMMENT 'ตำแหน่ง',
  `address` text DEFAULT NULL COMMENT 'ที่อยู่',
  `phone` varchar(20) DEFAULT NULL COMMENT 'เบอร์โทรศัพท์',
  `office_phone` varchar(20) DEFAULT NULL COMMENT 'เบอร์สำนักงาน',
  `extension` varchar(10) DEFAULT NULL COMMENT 'เบอร์ต่อ',
  `email` varchar(255) DEFAULT NULL COMMENT 'อีเมล',
  `suppliers_image` varchar(255) DEFAULT NULL COMMENT 'รูปโลโก้บริษัท/องค์กร',
  `remark` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัพเดทล่าสุด',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้แก้ไขข้อมูลล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `company`, `contact_name`, `position`, `address`, `phone`, `office_phone`, `extension`, `email`, `suppliers_image`, `remark`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
('23722daa-6eec-4a29-aa60-89cdea4dcd8c', 'Point IT', 'Point IT Consulting Co.,Ltd.', NULL, 'Service', '19 ซอยสุภาพงษ์ 1 แยก 6 แขวงหนองบอน เขตประเวศ กรุงเทพมหานคร 10250', '087-687-1184', '02-348-4790', '1041', 'info@pointit.co.th', '', 'บริการงานไอทีครบวงจร', '2025-01-12 05:23:59', '2', '2025-01-12 05:25:33', '2');

-- --------------------------------------------------------

--
-- Table structure for table `task_comments`
--

CREATE TABLE `task_comments` (
  `comment_id` char(36) NOT NULL COMMENT 'รหัสความคิดเห็น (UUID)',
  `task_id` char(36) NOT NULL COMMENT 'รหัสงาน (FK -> project_tasks)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (FK -> projects)',
  `user_id` char(36) NOT NULL COMMENT 'รหัสผู้แสดงความคิดเห็น (FK -> users)',
  `comment_text` text NOT NULL COMMENT 'ข้อความความคิดเห็น',
  `comment_type` enum('comment','status_change','file_upload','progress_update','system_log') DEFAULT 'comment' COMMENT 'ประเภทของ Log',
  `old_value` varchar(255) DEFAULT NULL COMMENT 'ค่าเดิม (สำหรับ Log การเปลี่ยนแปลง)',
  `new_value` varchar(255) DEFAULT NULL COMMENT 'ค่าใหม่ (สำหรับ Log การเปลี่ยนแปลง)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่โพสต์',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไข',
  `is_edited` tinyint(1) DEFAULT 0 COMMENT 'มีการแก้ไขหรือไม่',
  `is_deleted` tinyint(1) DEFAULT 0 COMMENT 'ถูกลบหรือไม่'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตาราง Activity Log/Comments ของ Task';

-- --------------------------------------------------------

--
-- Table structure for table `task_comment_attachments`
--

CREATE TABLE `task_comment_attachments` (
  `attachment_id` char(36) NOT NULL COMMENT 'รหัสไฟล์แนบ (UUID)',
  `comment_id` char(36) NOT NULL COMMENT 'รหัสความคิดเห็น (FK -> task_comments)',
  `task_id` char(36) NOT NULL COMMENT 'รหัสงาน (FK -> project_tasks)',
  `file_name` varchar(255) NOT NULL COMMENT 'ชื่อไฟล์ต้นฉบับ',
  `file_path` varchar(500) NOT NULL COMMENT 'path ของไฟล์ในระบบ',
  `file_size` bigint(20) DEFAULT NULL COMMENT 'ขนาดไฟล์ (bytes)',
  `file_type` varchar(100) DEFAULT NULL COMMENT 'ประเภทไฟล์ (MIME type)',
  `file_extension` varchar(10) DEFAULT NULL COMMENT 'นามสกุลไฟล์',
  `uploaded_by` char(36) NOT NULL COMMENT 'ผู้อัปโหลด (FK -> users)',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่อัปโหลด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางไฟล์แนบใน Task Comments';

-- --------------------------------------------------------

--
-- Table structure for table `task_mentions`
--

CREATE TABLE `task_mentions` (
  `mention_id` char(36) NOT NULL COMMENT 'รหัส mention (UUID)',
  `comment_id` char(36) NOT NULL COMMENT 'รหัสความคิดเห็น (FK -> task_comments)',
  `task_id` char(36) NOT NULL COMMENT 'รหัสงาน (FK -> project_tasks)',
  `mentioned_user_id` char(36) NOT NULL COMMENT 'ผู้ถูก mention (FK -> users)',
  `mentioned_by` char(36) NOT NULL COMMENT 'ผู้ mention (FK -> users)',
  `is_read` tinyint(1) DEFAULT 0 COMMENT 'อ่านแล้วหรือยัง',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่ mention'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บการ @ mention ใน Task Comments';

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `team_id` char(36) NOT NULL COMMENT 'รหัสทีม (UUID)',
  `team_name` varchar(255) NOT NULL COMMENT 'ชื่อทีม',
  `team_description` mediumtext DEFAULT NULL COMMENT 'รายละเอียดของทีม',
  `created_by` char(36) NOT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้ที่อัปเดตข้อมูล',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตข้อมูลล่าสุด',
  `team_leader` char(36) DEFAULT NULL COMMENT 'รหัสหัวหน้าทีม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`team_id`, `team_name`, `team_description`, `created_by`, `created_at`, `updated_by`, `updated_at`, `team_leader`) VALUES
('1', 'Innovation_PIT', 'Product  Solution Teams', '2', '2024-09-26 03:35:50', '5', '2024-11-04 02:27:46', '5');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` char(36) NOT NULL COMMENT 'รหัสผู้ใช้ (UUID)',
  `first_name` varchar(255) NOT NULL COMMENT 'ชื่อผู้ใช้',
  `last_name` varchar(255) NOT NULL COMMENT 'นามสกุลผู้ใช้',
  `username` varchar(255) NOT NULL COMMENT 'ชื่อผู้ใช้สำหรับล็อกอิน',
  `email` varchar(255) NOT NULL COMMENT 'อีเมล',
  `role` enum('Executive','Account Management','Sale Supervisor','Seller','Engineer') NOT NULL,
  `position` varchar(255) NOT NULL COMMENT 'ตำแหน่งงาน',
  `phone` varchar(20) DEFAULT NULL COMMENT 'เบอร์โทรศัพท์',
  `password` varchar(255) NOT NULL COMMENT 'รหัสผ่าน',
  `company` varchar(255) DEFAULT NULL COMMENT 'ชื่อบริษัท',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `profile_image` varchar(255) DEFAULT NULL COMMENT 'รูปภาพ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `username`, `email`, `role`, `position`, `phone`, `password`, `company`, `created_at`, `created_by`, `profile_image`) VALUES
('2', 'Systems', 'Admin', 'Admin', 'Systems_admin@gmail.com', 'Executive', 'Systems Admin', '0839595800', '$2y$10$lMfm90VV7oVMLHypibv3Xuc1enYtrj4hkiHyFxQM3FXPC7n8vALRy', 'Point IT Consulting Co.,Ltd.', '2024-09-15 09:43:58', '2', ''),
('30750fba-88ab-44ce-baf2-d0894357c67c', 'Bulakorn', 'Puapun', 'Bulakorn', 'bulakorn@gmail.com', 'Sale Supervisor', 'AI Business Consulting Director', '', '$2y$10$FJt7z443LOlzw14xC3ShOO..8su3Jt1dSXR/JEwdZUA44qSAoKmzu', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:14:26', '5', NULL),
('5', 'Panit', 'Paophan', 'Panit', 'panit@poinitit.co.th', 'Executive', 'Executive Director', '0814834619', '$2y$10$WU6mk3NEdi0HHWU/oUvTleWTCJsIUpD94vdo5uh.lZhnQ8Hwvr7Pm', 'Point IT Consulting Co.,Ltd.', '2024-09-17 08:15:37', '2', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_creation_logs`
--

CREATE TABLE `user_creation_logs` (
  `id` int(11) NOT NULL,
  `creator_id` varchar(255) NOT NULL,
  `new_user_id` varchar(255) NOT NULL,
  `new_user_role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_teams`
--

CREATE TABLE `user_teams` (
  `user_id` char(36) NOT NULL,
  `team_id` char(36) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_teams`
--

INSERT INTO `user_teams` (`user_id`, `team_id`, `is_primary`) VALUES
('2', '1', 1),
('30750fba-88ab-44ce-baf2-d0894357c67c', '1', 1),
('5', '1', 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_service_tickets_full`
-- (See below for the actual view)
--
CREATE TABLE `vw_service_tickets_full` (
`ticket_id` char(36)
,`ticket_no` varchar(50)
,`ticket_type` enum('Incident','Service','Change')
,`subject` varchar(150)
,`description` text
,`status` enum('Draft','New','On Process','Pending','Waiting for Approval','Scheduled','Resolved','Resolved Pending','Containment','Closed','Canceled')
,`priority` enum('Critical','High','Medium','Low')
,`urgency` enum('High','Medium','Low')
,`impact` varchar(100)
,`service_category` varchar(255)
,`category` varchar(255)
,`sub_category` varchar(255)
,`source` varchar(100)
,`sla_target` int(11)
,`sla_deadline` datetime
,`sla_status` enum('Within SLA','Near SLA','Overdue')
,`channel` enum('Onsite','Remote','Office')
,`start_at` datetime
,`due_at` datetime
,`resolved_at` datetime
,`closed_at` datetime
,`project_name` varchar(255)
,`job_owner_name` varchar(511)
,`job_owner_role` enum('Executive','Account Management','Sale Supervisor','Seller','Engineer')
,`reporter_name` varchar(511)
,`created_by_name` varchar(511)
,`start_location` varchar(255)
,`end_location` varchar(255)
,`travel_mode` varchar(100)
,`distance` decimal(10,2)
,`attachment_count` bigint(21)
,`watcher_count` bigint(21)
,`comment_count` bigint(21)
,`created_at` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_service_tickets_metrics`
-- (See below for the actual view)
--
CREATE TABLE `vw_service_tickets_metrics` (
`total_tickets` bigint(21)
,`status_draft` decimal(22,0)
,`status_new` decimal(22,0)
,`status_on_process` decimal(22,0)
,`status_pending` decimal(22,0)
,`status_waiting_approval` decimal(22,0)
,`status_approved` decimal(22,0)
,`status_in_progress` decimal(22,0)
,`status_resolved` decimal(22,0)
,`status_closed` decimal(22,0)
,`status_cancelled` decimal(22,0)
,`priority_critical` decimal(22,0)
,`priority_high` decimal(22,0)
,`priority_medium` decimal(22,0)
,`priority_low` decimal(22,0)
,`type_incident` decimal(22,0)
,`type_service` decimal(22,0)
,`type_change` decimal(22,0)
,`sla_within` decimal(22,0)
,`sla_near` decimal(22,0)
,`sla_overdue` decimal(22,0)
,`active_tickets` decimal(22,0)
,`today_tickets` decimal(22,0)
,`week_tickets` decimal(22,0)
,`month_tickets` decimal(22,0)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_task_comments`
-- (See below for the actual view)
--
CREATE TABLE `vw_task_comments` (
`comment_id` char(36)
,`task_id` char(36)
,`project_id` char(36)
,`user_id` char(36)
,`comment_text` text
,`comment_type` enum('comment','status_change','file_upload','progress_update','system_log')
,`old_value` varchar(255)
,`new_value` varchar(255)
,`created_at` timestamp
,`updated_at` timestamp
,`is_edited` tinyint(1)
,`is_deleted` tinyint(1)
,`first_name` varchar(255)
,`last_name` varchar(255)
,`user_full_name` varchar(511)
,`user_email` varchar(255)
,`attachment_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_service_tickets_full`
--
DROP TABLE IF EXISTS `vw_service_tickets_full`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_service_tickets_full`  AS SELECT `t`.`ticket_id` AS `ticket_id`, `t`.`ticket_no` AS `ticket_no`, `t`.`ticket_type` AS `ticket_type`, `t`.`subject` AS `subject`, `t`.`description` AS `description`, `t`.`status` AS `status`, `t`.`priority` AS `priority`, `t`.`urgency` AS `urgency`, `t`.`impact` AS `impact`, `t`.`service_category` AS `service_category`, `t`.`category` AS `category`, `t`.`sub_category` AS `sub_category`, `t`.`source` AS `source`, `t`.`sla_target` AS `sla_target`, `t`.`sla_deadline` AS `sla_deadline`, `t`.`sla_status` AS `sla_status`, `t`.`channel` AS `channel`, `t`.`start_at` AS `start_at`, `t`.`due_at` AS `due_at`, `t`.`resolved_at` AS `resolved_at`, `t`.`closed_at` AS `closed_at`, `p`.`project_name` AS `project_name`, concat(`owner`.`first_name`,' ',`owner`.`last_name`) AS `job_owner_name`, `owner`.`role` AS `job_owner_role`, concat(`reporter`.`first_name`,' ',`reporter`.`last_name`) AS `reporter_name`, concat(`creator`.`first_name`,' ',`creator`.`last_name`) AS `created_by_name`, `onsite`.`start_location` AS `start_location`, `onsite`.`end_location` AS `end_location`, `onsite`.`travel_mode` AS `travel_mode`, `onsite`.`distance` AS `distance`, (select count(0) from `service_ticket_attachments` where `service_ticket_attachments`.`ticket_id` = `t`.`ticket_id`) AS `attachment_count`, (select count(0) from `service_ticket_watchers` where `service_ticket_watchers`.`ticket_id` = `t`.`ticket_id`) AS `watcher_count`, (select count(0) from `service_ticket_comments` where `service_ticket_comments`.`ticket_id` = `t`.`ticket_id` and `service_ticket_comments`.`deleted_at` is null) AS `comment_count`, `t`.`created_at` AS `created_at`, `t`.`updated_at` AS `updated_at` FROM (((((`service_tickets` `t` left join `projects` `p` on(`t`.`project_id` = `p`.`project_id`)) left join `users` `owner` on(`t`.`job_owner` = `owner`.`user_id`)) left join `users` `reporter` on(`t`.`reporter` = `reporter`.`user_id`)) left join `users` `creator` on(`t`.`created_by` = `creator`.`user_id`)) left join `service_ticket_onsite` `onsite` on(`t`.`ticket_id` = `onsite`.`ticket_id`)) WHERE `t`.`deleted_at` is null ;

-- --------------------------------------------------------

--
-- Structure for view `vw_service_tickets_metrics`
--
DROP TABLE IF EXISTS `vw_service_tickets_metrics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_service_tickets_metrics`  AS SELECT count(0) AS `total_tickets`, sum(case when `service_tickets`.`status` = 'Draft' then 1 else 0 end) AS `status_draft`, sum(case when `service_tickets`.`status` = 'New' then 1 else 0 end) AS `status_new`, sum(case when `service_tickets`.`status` = 'On Process' then 1 else 0 end) AS `status_on_process`, sum(case when `service_tickets`.`status` = 'Pending' then 1 else 0 end) AS `status_pending`, sum(case when `service_tickets`.`status` = 'Waiting for Approval' then 1 else 0 end) AS `status_waiting_approval`, sum(case when `service_tickets`.`status` = 'Approved' then 1 else 0 end) AS `status_approved`, sum(case when `service_tickets`.`status` = 'In Progress' then 1 else 0 end) AS `status_in_progress`, sum(case when `service_tickets`.`status` = 'Resolved' then 1 else 0 end) AS `status_resolved`, sum(case when `service_tickets`.`status` = 'Closed' then 1 else 0 end) AS `status_closed`, sum(case when `service_tickets`.`status` = 'Cancelled' then 1 else 0 end) AS `status_cancelled`, sum(case when `service_tickets`.`priority` = 'Critical' then 1 else 0 end) AS `priority_critical`, sum(case when `service_tickets`.`priority` = 'High' then 1 else 0 end) AS `priority_high`, sum(case when `service_tickets`.`priority` = 'Medium' then 1 else 0 end) AS `priority_medium`, sum(case when `service_tickets`.`priority` = 'Low' then 1 else 0 end) AS `priority_low`, sum(case when `service_tickets`.`ticket_type` = 'Incident' then 1 else 0 end) AS `type_incident`, sum(case when `service_tickets`.`ticket_type` = 'Service' then 1 else 0 end) AS `type_service`, sum(case when `service_tickets`.`ticket_type` = 'Change' then 1 else 0 end) AS `type_change`, sum(case when `service_tickets`.`sla_status` = 'Within SLA' then 1 else 0 end) AS `sla_within`, sum(case when `service_tickets`.`sla_status` = 'Near SLA' then 1 else 0 end) AS `sla_near`, sum(case when `service_tickets`.`sla_status` = 'Overdue' then 1 else 0 end) AS `sla_overdue`, sum(case when `service_tickets`.`status` not in ('Closed','Cancelled') then 1 else 0 end) AS `active_tickets`, sum(case when cast(`service_tickets`.`created_at` as date) = curdate() then 1 else 0 end) AS `today_tickets`, sum(case when yearweek(`service_tickets`.`created_at`,1) = yearweek(curdate(),1) then 1 else 0 end) AS `week_tickets`, sum(case when year(`service_tickets`.`created_at`) = year(curdate()) and month(`service_tickets`.`created_at`) = month(curdate()) then 1 else 0 end) AS `month_tickets` FROM `service_tickets` WHERE `service_tickets`.`deleted_at` is null ;

-- --------------------------------------------------------

--
-- Structure for view `vw_task_comments`
--
DROP TABLE IF EXISTS `vw_task_comments`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_task_comments`  AS SELECT `tc`.`comment_id` AS `comment_id`, `tc`.`task_id` AS `task_id`, `tc`.`project_id` AS `project_id`, `tc`.`user_id` AS `user_id`, `tc`.`comment_text` AS `comment_text`, `tc`.`comment_type` AS `comment_type`, `tc`.`old_value` AS `old_value`, `tc`.`new_value` AS `new_value`, `tc`.`created_at` AS `created_at`, `tc`.`updated_at` AS `updated_at`, `tc`.`is_edited` AS `is_edited`, `tc`.`is_deleted` AS `is_deleted`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `user_full_name`, `u`.`email` AS `user_email`, (select count(0) from `task_comment_attachments` where `task_comment_attachments`.`comment_id` = `tc`.`comment_id`) AS `attachment_count` FROM (`task_comments` `tc` left join `users` `u` on(`tc`.`user_id` = `u`.`user_id`)) WHERE `tc`.`is_deleted` = 0 ORDER BY `tc`.`created_at` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_image` (`image_id`);

--
-- Indexes for table `category_image`
--
ALTER TABLE `category_image`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `document_links`
--
ALTER TABLE `document_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_document_links_project` (`project_id`),
  ADD KEY `fk_document_links_created_by` (`created_by`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_email` (`personal_email`),
  ADD UNIQUE KEY `company_email` (`company_email`),
  ADD UNIQUE KEY `company_email_2` (`company_email`);

--
-- Indexes for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_uploaded_by` (`uploaded_by`),
  ADD KEY `idx_category` (`document_category`),
  ADD KEY `idx_upload_date` (`upload_date`),
  ADD KEY `fk_employee_documents_updater` (`updated_by`);

--
-- Indexes for table `employee_document_links`
--
ALTER TABLE `employee_document_links`
  ADD PRIMARY KEY (`link_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_category` (`link_category`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `products_ibfk_1` (`created_by`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `products_team_fk` (`team_id`);

--
-- Indexes for table `product_documents`
--
ALTER TABLE `product_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `projects_ibfk_2` (`created_by`),
  ADD KEY `projects_ibfk_3` (`seller`),
  ADD KEY `projects_customer_fk` (`customer_id`),
  ADD KEY `projects_product_fk` (`product_id`),
  ADD KEY `idx_team_id` (`team_id`);

--
-- Indexes for table `project_costs`
--
ALTER TABLE `project_costs`
  ADD PRIMARY KEY (`cost_id`),
  ADD KEY `idx_project_costs_project` (`project_id`) COMMENT 'ดัชนีสำหรับการค้นหาตามรหัสโครงการ',
  ADD KEY `idx_costs_created_at` (`created_at`) COMMENT 'ดัชนีสำหรับการค้นหาตามวันที่สร้าง',
  ADD KEY `idx_costs_supplier` (`supplier`) COMMENT 'ดัชนีสำหรับการค้นหาตามซัพพลายเออร์';

--
-- Indexes for table `project_cost_summary`
--
ALTER TABLE `project_cost_summary`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `project_customers`
--
ALTER TABLE `project_customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `project_discussions`
--
ALTER TABLE `project_discussions`
  ADD PRIMARY KEY (`discussion_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_project_time` (`project_id`,`created_at`);

--
-- Indexes for table `project_discussion_attachments`
--
ALTER TABLE `project_discussion_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_discussion` (`discussion_id`);

--
-- Indexes for table `project_discussion_mentions`
--
ALTER TABLE `project_discussion_mentions`
  ADD PRIMARY KEY (`mention_id`),
  ADD KEY `idx_user` (`mentioned_user_id`),
  ADD KEY `idx_discussion_user` (`discussion_id`,`mentioned_user_id`);

--
-- Indexes for table `project_documents`
--
ALTER TABLE `project_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `fk_project_documents_project` (`project_id`),
  ADD KEY `fk_project_documents_user` (`uploaded_by`);

--
-- Indexes for table `project_images`
--
ALTER TABLE `project_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `fk_project_images_project` (`project_id`),
  ADD KEY `fk_project_images_user` (`uploaded_by`);

--
-- Indexes for table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `unique_project_user` (`project_id`,`user_id`) COMMENT 'ป้องกันการเพิ่มซ้ำ',
  ADD KEY `fk_project_members_user` (`user_id`),
  ADD KEY `fk_project_members_role` (`role_id`);

--
-- Indexes for table `project_payments`
--
ALTER TABLE `project_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_project_payments_project` (`project_id`),
  ADD KEY `fk_project_payments_user` (`created_by`);

--
-- Indexes for table `project_roles`
--
ALTER TABLE `project_roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `unique_role_name` (`role_name`) COMMENT 'ป้องกันชื่อบทบาทซ้ำ';

--
-- Indexes for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `parent_task_id` (`parent_task_id`);

--
-- Indexes for table `project_task_assignments`
--
ALTER TABLE `project_task_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `service_sla_impacts`
--
ALTER TABLE `service_sla_impacts`
  ADD PRIMARY KEY (`impact_id`),
  ADD UNIQUE KEY `uniq_impact_name` (`impact_name`);

--
-- Indexes for table `service_sla_priority_matrix`
--
ALTER TABLE `service_sla_priority_matrix`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_impact_urgency` (`impact_id`,`urgency`);

--
-- Indexes for table `service_sla_targets`
--
ALTER TABLE `service_sla_targets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_priority` (`priority`);

--
-- Indexes for table `service_sla_time_matrix`
--
ALTER TABLE `service_sla_time_matrix`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_impact_priority_urgency` (`impact_id`,`priority`,`urgency`);

--
-- Indexes for table `service_tickets`
--
ALTER TABLE `service_tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD UNIQUE KEY `ticket_no` (`ticket_no`),
  ADD KEY `idx_ticket_no` (`ticket_no`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `idx_job_owner` (`job_owner`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_sla_status` (`sla_status`),
  ADD KEY `idx_deleted_at` (`deleted_at`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `reporter` (`reporter`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `service_ticket_attachments`
--
ALTER TABLE `service_ticket_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `idx_ticket_id` (`ticket_id`),
  ADD KEY `idx_uploaded_by` (`uploaded_by`);

--
-- Indexes for table `service_ticket_comments`
--
ALTER TABLE `service_ticket_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `idx_ticket_id` (`ticket_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `service_ticket_history`
--
ALTER TABLE `service_ticket_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_ticket_id` (`ticket_id`),
  ADD KEY `idx_field_name` (`field_name`),
  ADD KEY `idx_changed_at` (`changed_at`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `service_ticket_notifications`
--
ALTER TABLE `service_ticket_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_ticket_id` (`ticket_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `service_ticket_onsite`
--
ALTER TABLE `service_ticket_onsite`
  ADD PRIMARY KEY (`onsite_id`),
  ADD KEY `idx_ticket_id` (`ticket_id`);

--
-- Indexes for table `service_ticket_timeline`
--
ALTER TABLE `service_ticket_timeline`
  ADD PRIMARY KEY (`timeline_id`),
  ADD KEY `idx_ticket_order` (`ticket_id`,`order`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `service_ticket_watchers`
--
ALTER TABLE `service_ticket_watchers`
  ADD PRIMARY KEY (`watcher_id`),
  ADD UNIQUE KEY `unique_watcher` (`ticket_id`,`user_id`),
  ADD KEY `idx_ticket_id` (`ticket_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `task_comments`
--
ALTER TABLE `task_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `idx_task_id` (`task_id`),
  ADD KEY `idx_project_id` (`project_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_comment_type` (`comment_type`),
  ADD KEY `idx_is_deleted` (`is_deleted`);

--
-- Indexes for table `task_comment_attachments`
--
ALTER TABLE `task_comment_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `idx_comment_id` (`comment_id`),
  ADD KEY `idx_task_id` (`task_id`),
  ADD KEY `idx_uploaded_by` (`uploaded_by`),
  ADD KEY `idx_file_type` (`file_type`);

--
-- Indexes for table `task_mentions`
--
ALTER TABLE `task_mentions`
  ADD PRIMARY KEY (`mention_id`),
  ADD KEY `idx_comment_id` (`comment_id`),
  ADD KEY `idx_task_id` (`task_id`),
  ADD KEY `idx_mentioned_user` (`mentioned_user_id`),
  ADD KEY `idx_is_read` (`is_read`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`team_id`),
  ADD KEY `teams_ibfk_1` (`team_leader`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_creation_logs`
--
ALTER TABLE `user_creation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_creator` (`creator_id`),
  ADD KEY `fk_new_user` (`new_user_id`);

--
-- Indexes for table `user_teams`
--
ALTER TABLE `user_teams`
  ADD PRIMARY KEY (`user_id`,`team_id`),
  ADD KEY `user_teams_user_id_foreign` (`user_id`),
  ADD KEY `user_teams_team_id_foreign` (`team_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_creation_logs`
--
ALTER TABLE `user_creation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `fk_category_image` FOREIGN KEY (`image_id`) REFERENCES `category_image` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `document_links`
--
ALTER TABLE `document_links`
  ADD CONSTRAINT `fk_document_links_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_document_links_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD CONSTRAINT `fk_employee_documents_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_employee_documents_updater` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_employee_documents_uploader` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`),
  ADD CONSTRAINT `products_team_fk` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`);

--
-- Constraints for table `product_documents`
--
ALTER TABLE `product_documents`
  ADD CONSTRAINT `product_documents_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`seller`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `projects_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `project_costs`
--
ALTER TABLE `project_costs`
  ADD CONSTRAINT `project_costs_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `project_cost_summary`
--
ALTER TABLE `project_cost_summary`
  ADD CONSTRAINT `project_cost_summary_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `project_customers`
--
ALTER TABLE `project_customers`
  ADD CONSTRAINT `project_customers_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_customers_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `project_discussions`
--
ALTER TABLE `project_discussions`
  ADD CONSTRAINT `project_discussions_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_discussions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `project_discussion_attachments`
--
ALTER TABLE `project_discussion_attachments`
  ADD CONSTRAINT `project_discussion_attachments_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `project_discussions` (`discussion_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_discussion_attachments_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_discussion_attachments_ibfk_3` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `project_discussion_mentions`
--
ALTER TABLE `project_discussion_mentions`
  ADD CONSTRAINT `project_discussion_mentions_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `project_discussions` (`discussion_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_discussion_mentions_ibfk_2` FOREIGN KEY (`mentioned_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `project_documents`
--
ALTER TABLE `project_documents`
  ADD CONSTRAINT `fk_project_documents_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`),
  ADD CONSTRAINT `fk_project_documents_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `project_images`
--
ALTER TABLE `project_images`
  ADD CONSTRAINT `fk_project_images_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`),
  ADD CONSTRAINT `fk_project_images_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `project_members`
--
ALTER TABLE `project_members`
  ADD CONSTRAINT `fk_project_members_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_project_members_role` FOREIGN KEY (`role_id`) REFERENCES `project_roles` (`role_id`),
  ADD CONSTRAINT `fk_project_members_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD CONSTRAINT `project_tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`),
  ADD CONSTRAINT `project_tasks_ibfk_2` FOREIGN KEY (`parent_task_id`) REFERENCES `project_tasks` (`task_id`);

--
-- Constraints for table `project_task_assignments`
--
ALTER TABLE `project_task_assignments`
  ADD CONSTRAINT `project_task_assignments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `project_tasks` (`task_id`),
  ADD CONSTRAINT `project_task_assignments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `service_sla_priority_matrix`
--
ALTER TABLE `service_sla_priority_matrix`
  ADD CONSTRAINT `fk_matrix_impact` FOREIGN KEY (`impact_id`) REFERENCES `service_sla_impacts` (`impact_id`) ON DELETE CASCADE;

--
-- Constraints for table `service_sla_time_matrix`
--
ALTER TABLE `service_sla_time_matrix`
  ADD CONSTRAINT `fk_time_matrix_impact` FOREIGN KEY (`impact_id`) REFERENCES `service_sla_impacts` (`impact_id`) ON DELETE CASCADE;

--
-- Constraints for table `service_tickets`
--
ALTER TABLE `service_tickets`
  ADD CONSTRAINT `service_tickets_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`),
  ADD CONSTRAINT `service_tickets_ibfk_2` FOREIGN KEY (`job_owner`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `service_tickets_ibfk_3` FOREIGN KEY (`reporter`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `service_tickets_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `service_ticket_attachments`
--
ALTER TABLE `service_ticket_attachments`
  ADD CONSTRAINT `service_ticket_attachments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `service_tickets` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_ticket_attachments_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `service_ticket_comments`
--
ALTER TABLE `service_ticket_comments`
  ADD CONSTRAINT `service_ticket_comments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `service_tickets` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_ticket_comments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `service_ticket_history`
--
ALTER TABLE `service_ticket_history`
  ADD CONSTRAINT `service_ticket_history_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `service_tickets` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_ticket_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `service_ticket_notifications`
--
ALTER TABLE `service_ticket_notifications`
  ADD CONSTRAINT `service_ticket_notifications_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `service_tickets` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_ticket_notifications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `service_ticket_onsite`
--
ALTER TABLE `service_ticket_onsite`
  ADD CONSTRAINT `service_ticket_onsite_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `service_tickets` (`ticket_id`) ON DELETE CASCADE;

--
-- Constraints for table `service_ticket_timeline`
--
ALTER TABLE `service_ticket_timeline`
  ADD CONSTRAINT `service_ticket_timeline_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `service_tickets` (`ticket_id`) ON DELETE CASCADE;

--
-- Constraints for table `service_ticket_watchers`
--
ALTER TABLE `service_ticket_watchers`
  ADD CONSTRAINT `service_ticket_watchers_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `service_tickets` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_ticket_watchers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_ticket_watchers_ibfk_3` FOREIGN KEY (`added_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `task_comments`
--
ALTER TABLE `task_comments`
  ADD CONSTRAINT `fk_task_comments_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_comments_task` FOREIGN KEY (`task_id`) REFERENCES `project_tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `task_comment_attachments`
--
ALTER TABLE `task_comment_attachments`
  ADD CONSTRAINT `fk_task_attachments_comment` FOREIGN KEY (`comment_id`) REFERENCES `task_comments` (`comment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_attachments_task` FOREIGN KEY (`task_id`) REFERENCES `project_tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_attachments_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `task_mentions`
--
ALTER TABLE `task_mentions`
  ADD CONSTRAINT `fk_task_mentions_comment` FOREIGN KEY (`comment_id`) REFERENCES `task_comments` (`comment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_mentions_task` FOREIGN KEY (`task_id`) REFERENCES `project_tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_mentions_user` FOREIGN KEY (`mentioned_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `fk_team_leader` FOREIGN KEY (`team_leader`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_teams`
--
ALTER TABLE `user_teams`
  ADD CONSTRAINT `user_teams_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_teams_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
