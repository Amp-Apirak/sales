-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2025 at 11:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `company`, `position`, `address`, `phone`, `email`, `remark`, `created_by`, `created_at`, `updated_by`, `customers_image`, `office_phone`, `extension`, `updated_at`) VALUES
('02e18007-e4e7-4fb7-a2c2-c924ece0a966', 'คุณทอม', 'บริษัท อัลทิเมทเทคแอนด์อินโนเวชั่น จำกัด (สำนักงานใหญ่)', 'Management Director', 'เลขที่ 51 อาคารเมเจอร์ทาวเวอร์ พระราม 9 - รามคำแหง ห้องเลขที่ 2006.2, 2007 ชั้นที่ 20 \r\nถนนพระราม 9 แขวงหัวหมาก เขตบางกะปิ กรุงเทพมหานคร 10240', '0220277836', '', '', '3', '2025-04-09 06:03:36', '', NULL, '', NULL, '2025-04-09 06:03:36'),
('054160f3-f50e-40a2-a45e-569777875172', 'Somboon  Sangcharoen', 'GFCA  Co., Ltd.', 'Manager - Infrastructure', '1526-1540 Soi Pattanakan 48, Pattanakan Road, Suanluang, Bangkok 10250, Thailand', '0845519109', 'somboon@gfca.com', '', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-13 05:01:13', '', '', '020730977', '202', '2025-01-13 05:01:13'),
('065f2ab4-63ac-4758-8eb1-380df80c8f83', 'คุณก้อง  เทพสิทธิ์', 'สหกรณ์ออมทรัพย์การไฟฟ้าฝ่ายผลิตแห่งประเทศไทย จำกัด', 'หัวหน้าฝ่ายไอที', '', '', '', '', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2024-12-16 02:07:49', '8', '', '', '', '2025-07-14 09:10:42'),
('075ad214-ce0d-495d-b27d-d4a4fdb9e083', 'บริษัท ซินเน็ค (ประเทศไทย) จํากัด (มหาชน)', '', '', '', '', '', '', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-06-12 10:55:48', '', NULL, '', NULL, '2025-06-12 10:55:48'),
('0968cd06-9d79-4933-8de8-399cb9ac5868', 'คุณโอฬาร', 'Zoom Information System Company Limited', 'Manager Director', '223/16 หมู่บ้าน เซนสิริทาวน์ หมู่ที่ 1ซอย พรประภานิมิตร 17 ถนนแยกมิตรกมล ต.หนองปรือ อ.บางละมุง จ.ชลบุรี', '', 'Oran.gun@gmail.com', 'ลูกค้าภายใน', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 08:47:01', '', NULL, '', NULL, '2025-01-16 08:47:01'),
('0a20192c-45f2-4032-aae1-39b4861104fc', 'พี่สรวล', 'Master Maker Co., Ltd.', 'MD', '274/3 ซ.รุ่งเรือง ถ.สุทธิสารวินิจฉัย แขวงสามเสนนอก เขตห้วยขวาง กรุงเทพฯ 10310', '', '', '', '5', '2025-06-16 01:13:12', '', NULL, '02-276-4388', NULL, '2025-06-16 01:13:12'),
('0a462754-178e-4f0c-a510-d9dd40db6490', 'คุณพลอยนภัส', 'Master Maker Co., Ltd.', 'เลขานุการ', '274/3 ซ.รุ่งเรือง ถ.สุทธิสารวินิจฉัย แขวงสามเสนนอก เขตห้วยขวาง กรุงเทพฯ 10310', '', '', '', '3', '2024-12-02 13:55:53', '3', '', '02-276-4388', '', '2025-06-15 05:22:31'),
('0d4e8645-ff06-4531-bc5a-09e6570248d8', 'Mr.Yodwarit Krasaeperm', 'Sekisui S-LEC(Thailand) Co.,Ltd.', '', '', '', 'it@slecth.com', '', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-06-19 02:14:06', '', '', '0818102516', '', '2025-06-19 02:14:06'),
('0d5a69bc-9008-463e-aaf2-5a56f142cca3', 'บริษัท โซแอ็ท โซลูชั่น จำกัด', 'คุณวรลักษณ์', '', '', '', '', '', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-16 05:18:15', '44', '', '', '', '2025-06-16 05:18:41'),
('0ecc689d-9c12-4936-b9df-596884715574', 'Passakorn Lermanon', 'Decor Mart Co. Ltd.', 'IT Manager', '', '0894415550', 'passakorn@dm-home.com', '', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-05-14 04:32:13', '', '', '', '', '2025-05-14 04:32:13'),
('0f80acd4-d034-4175-b501-f879a9e203de', 'ธนาคารไทยพาณิชย์ จำกัด(มหาชน)', 'คุณดิเรก วงศ์งาม', '', '', '', '', '', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-01-30 06:57:48', '44', NULL, '', '', '2025-06-16 03:21:56'),
('11ca34d4-27bb-48ce-915a-81996dc98f9b', 'กระทรวงวัฒนธรรม', '', '', '', '', '', '', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2025-06-12 09:48:03', '', NULL, '', NULL, '2025-06-12 09:48:03'),
('1be1add7-4691-4a2b-a2db-5b849cdc5cfe', 'คุณวันเสาร์', 'บริษัท บีวายจีจีอี โซลูชั่น จำกัด', '', '', '', '', '', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-16 04:21:51', '', '', '0930146810', '', '2025-06-16 04:21:51'),
('1d9884a8-7762-4f28-a3b5-8419f13ffe8b', 'สำนักงานส่งกำลังบำรุง', 'สำนักงานตำรวจแห่งชาติ', '', '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2025-06-10 07:27:32', '', NULL, '', NULL, '2025-06-10 07:27:32'),
('1fb0fb81-4482-438a-ab66-5472c52bf9e4', 'องค์การบริหารส่วนจังหวัดชลบุรี', 'บจ.ซูม อินฟอร์เมชั่น ซิสเต็ม', NULL, '', '', '', '', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:38:21', '', '', '', '', '2024-11-04 03:38:21'),
('213830aa-08d9-4673-9081-3fcba6ce1625', 'คุณชาคริยา  นาคมณี', 'บริษัท เอเชี่ยน เอ็กซ์ฟิดิชั่น จำกัด', 'กรรมการผู้จัดการ', '9/1 อาคารมูลนิธิสนธิอิสลาม ชั้น 4 ห้อง 402 ถนนอรุณอมรินทร์  แขวงอรุณอมรินทร์  เขตบางกอกน้อย กรุงเทพมหานคร', '0903177256', '', '', '3', '2024-12-02 13:33:10', '3', '', '', '', '2025-06-15 05:23:57'),
('2621ade4-bbfa-474f-a74d-fcb04d70f2eb', 'คุณตรีเทศ หะหวัง', 'บริษัท โทรคมนาคมแห่งชาติ จำกัด', 'ส่วนบริการเสริมสื่อสารไร้สาย (สปป.2)', '99 ถนนแจ้งวัฒนะ แขวงทุ่งสองห้อง เขตหลักสี่ กรุงเทพมหานคร 10210', '089-482-2387', 'treeted@nt.ntplc.co.th', '', '3', '2024-10-15 21:52:58', '3', '671244b41c0cb.jpg', '', '', '2025-06-15 05:27:09'),
('290b026b-4379-454b-a8df-5aa410a6bd21', 'บริษัท บิซิเนส โซลูชั่น โพรไวเดอร์ (บีเอสพี) จำกัด', '', '', '', '', '', '', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-06-12 10:45:41', '', NULL, '', NULL, '2025-06-12 10:45:41'),
('2b5c101f-db79-4143-89f9-2b42fbea06bd', 'Danai Sinsakjaroongdech', 'SmartBiz Solutions Co.,Ltd.', 'Sales Manager', '', '0954159936', 'danai@smartbiz.co.th', '', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-28 06:10:06', '', '', '0954159936', '', '2025-01-28 06:10:06'),
('2b82d2d9-0373-45c9-8f7d-19ef35641a13', 'คุณพัชรินทร์', 'บริษัท ดีโบลด์ นิกซ์ดอร์ฟ (ประเทศไทย) จำกัด', '', '', '', '', '', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-16 03:43:12', '', '', '', '', '2025-06-16 03:43:12'),
('2d4610f7-471d-42c1-a193-d79ac4eb24e8', 'ผอ.สุดารัตน์ นามกระจ่าง', 'เทศบาลตำบลทับมา', 'ผอ.กองสาธารณสุข', 'เลขที่ 20/3 หมู่ ที่ 4 อำเภอ เมือง, ตำบลทับมา อำเภอเมืองระยอง ระยอง 21000', '0868299839', '', '', '3', '2024-12-02 14:57:17', '3', '', '', '', '2025-06-16 05:03:11'),
('2d4d1aec-e4e4-4836-9308-4c2c19da05cb', 'เจ้าหน้าที่พัสดุ', 'การไฟฟ้าฝ่ายผลิตแห่งประเทศไทย', '', 'เลขที่ 53 หมู่ 2 ถนนจรัญสนิทวงศ์ ตำบลบางกรวย อำเภอบางกรวย จังหวัดนนทบุรี 11130', '', '', '', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-06-23 10:20:04', '', '', '', '', '2025-06-23 10:20:04'),
('2edcb350-5c44-4803-be82-0ce9b0015ac7', 'เทศบาลนคร แหลมฉบัง', '', '', '99 หมู่10 ต.ทุ่งสุขลา อ.ศรีราชา จ.ชลบุรี 20230', '', '', '', '70dd36b5-f587-4aa9-b544-c69542616d34', '2025-06-11 10:41:00', '', NULL, 'เทศบาลนคร แหลมฉบัง', NULL, '2025-06-11 10:41:00'),
('2f12eaa0-9738-484d-8329-80e964ea5ee6', 'คุณสุกันต์', 'โรงพยาบาลกรุงเทพภูเก็ต', '', '', '', '', '', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-16 04:03:08', '', '', '', '', '2025-06-16 04:03:08'),
('2f1ee3da-fe91-4f06-b0ae-62a206c7cd5d', 'บริษัท สมาร์ทบิซ โซลูชั่น จำกัด', '', '', '', '', '', '', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-06-12 11:00:26', '', NULL, '', NULL, '2025-06-12 11:00:26'),
('31ed3e1f-0435-4ede-a66a-ae985d0a751e', 'คุณเตย', 'บริษัท โซยี (ไทยแลนด์) จำกัด', NULL, 'เลขที่ 222/8 หมู่ 4 ตำบลบางแก้ว อำเภอบางพลี จังหวัดสมุทรปราการ 10540', '0971188010', '', '', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-06 10:07:19', '', '', '021153838', '', '2024-12-06 10:07:19'),
('32104ee7-4b28-400b-bb7b-1ab55e1cf19d', 'นายสิรวิชฐ์ อำไพวงษ์', 'องค์การบริหารส่วนตำบลบ่อวิน', 'นายกฯ', 'องค์การบริหารส่วนตำบลบ่อวิน เลขที่ 1 หมู่ที่ 6 ตำบลบ่อวิน อำเภอศรีราชา จังหวัดชลบุรี 20230 โทรศัพท์ 0-3834-5949 ,0-3834-5918 โทรสาร 0-3834-6116 สายด่วนร้องทุกข์ 24 ชม. 08-1949-7771 นายกเทศบาลตาบลบ่อวิน องค์การบริหารส่วนตำบลบ่อวิน', '038345949', 'admin@bowin.go.th', '', '3', '2024-10-11 23:26:14', '3', NULL, '', '', '2025-06-15 05:15:31'),
('34ea3368-fa1c-445a-aeb8-821c87086d3a', 'นงนุช โกวิทวณิช', 'BUSINESS SOLUTIONS PROVIDER CO.,LTD.', 'กรรมการผู้จัดการ', '7/129 18th Floor., Baromrajchonnee Rd.,Arunammarin, Bangkok-Noi, Bangkok. 10700', '0619522110', 'nongnuch@bspc.co.th', '', '3', '2024-10-17 04:45:13', '3', NULL, '', '', '2025-06-15 05:24:31'),
('350429f1-d84a-4cec-8c28-d1a2ce9c4763', 'Mr. CHAWAPAT PRASERTTONGSUK', 'WT Partnership (Thailand) Limited', 'Senior MEP Quantity Surveyor', 'U1802, L18, S Metro Building, 725 Sukhumvit Rd, Klongton Nua, Wattana, Bangkok 10110', '0855750465', 'chawapatp@wtpthailand.com', '', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-03-17 03:25:34', '', NULL, '', NULL, '2025-03-17 03:25:34'),
('360a7a11-6bcd-4301-8156-b4d11ebd6794', 'ผอ.เค', 'สำนักงานศาลรัฐธรรมนูญ', '', '', '', '', '', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2024-12-16 01:51:59', '', '', '', '', '2024-12-16 01:51:59'),
('3761198e-e426-49b5-9dc5-a5efd3b13a33', 'บริษัท กันกุลเอ็นจิเนียริ่ง จํากัด (มหาชน)', '', '', '', '', '', '', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2025-06-12 09:58:35', '', NULL, '', NULL, '2025-06-12 09:58:35'),
('37d762a4-be8c-4b77-b903-7c7e44679b52', 'คุณบุศรินทร์  แก่นหอม', 'ธนาคารกรุงไทย จำกัด (มหาชน)', '', '', '', '', '', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2025-07-14 09:07:17', '', NULL, '', NULL, '2025-07-14 09:07:17'),
('3b652cc4-3afe-4caa-b092-fa8987489c78', 'คุณป๊อบ', 'บริษัท ธนบุรีพานิช จำกัด', 'IT Management', '', '0982499237', 'Sitthinan.sri@thonburi.com', '', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:34:34', '', NULL, '', NULL, '2025-10-27 06:34:34'),
('3ef73d28-72ff-4c90-b04a-693a33baf895', 'นางสาวปรียาภรณ์ บริสุทธิพันธ์', 'ธนาคารออมสิน', 'รองผู้อำนวยการฝ่ายการพัสดุ ส่วยบริหารพัสดุ', '470 ถนนพหลโยธิน แขวงสามเสนใน เขตพญาไท กรุงเทพฯ 10400', '', '', '', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-01-06 04:45:50', '', '677b5ffeaedd3.png', '022998000', '030127', '2025-01-06 04:45:50'),
('45af1f14-b041-43b2-b4ff-d93692564a61', 'Pilanthana Wisawamitr', 'TOYO TIRE (THAILAND) CO., LTD.', 'OE Sales Division', '2/8 Sukhaphiban 2 Rd. Khwang Prawet, Khet Prawet, Bangkok 10250', '0922495893', 'pilanthana@toyotires.co.th', '', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 04:35:11', '', '', '02-329 2012', '203', '2025-01-06 04:35:11'),
('466cca72-833b-4631-80f5-1cafdf402375', 'บริษัท ดับเบิลยูเอสจี จำกัด', '', '', '', '', '', '', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-06-12 11:13:02', '', NULL, '', NULL, '2025-06-12 11:13:02'),
('48cf0983-375c-46de-ab41-72350901a376', 'คุณอลิสา ธนสารเสถียร', 'บริษัท ไอไอเอส ออโตเมชั่น จำกัด', NULL, '36 ซอยสุขาภิบาล 5 ซอย 5  แยก 13  แขวงท่าแร้ง เขตบางเขน  กรุงเทพฯ 10220', '0905424694', '', '', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-06 10:05:07', '', '', '', '', '2024-12-06 10:05:07'),
('4baf1507-337e-43f7-8d21-fbc184d876ac', 'สหกรณ์ออมทรัพย์การไฟฟ้าฝ่ายผลิตแห่งประเทศไทย จำกัด', 'คุณปวิชชา เกตุคง', '', '', '', '', '', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-16 06:39:40', '', '', '', '', '2025-06-16 06:39:40'),
('4ce6231a-57de-44f4-ab4e-817fec010315', 'คุณสร้างรัฐ หัตถาวงศ์ (พี่ไฟท์)', 'บริษัท อินสไปร์ คอมมูนิเคชั่น จำกัด (สำนักงานใหญ่)', 'กรรมการผู้จัดการ', 'บริษัท อินสไปร์คอมมูนิเคชั่น จำกัด (สำนักงนใหญ่)\r\nเลขที่ 169/85 หมูที่ 7 ถนนพุทธมณฑลสาย 4 ตำบลกระทุ่มล้ม อำเภอสามพราน\r\nจังหวัดนครปฐม 73220\r\nเลขประจำตัวผู้เสียภาษี 0735552003555', '0985969009', 'srangrath@inspirecomm.co.th', 'โทร. 024827436\r\nโทรสาร 024827436\r\nhttps://www.inspirecomm.co.th/', '5', '2025-06-16 00:24:41', '', NULL, '024827436', NULL, '2025-06-16 00:24:41'),
('4f049ce6-9488-4664-865f-5d9729659ee2', 'คุณมิกซ์', 'MPLUS INTERNATIONAL CO.,LTD.', '', '1 Empire Tower, 47th Floor., Unit 4703 (river 25), South Sathorn Road, Yannawa, Sathorn, Bangkok,10120, Thailand', '', '', '', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 09:10:26', '', NULL, '', NULL, '2025-01-16 09:10:26'),
('51304482-8440-4d89-836e-c45c9eda7631', 'คุณจิระพงษ์', 'โรงไฟฟ้าวังน้อย', '', '', '', '', '', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-06-26 07:51:36', '', '', '', '', '2025-06-26 07:51:36'),
('594abb40-0296-4aa0-a1fd-82f479359ed5', 'คุณเก๋', 'บริษัท เฟิรส์วัน ซิสเต็มส์ จำกัด', 'ฝ่ายขาย', 'เลขที่ 719 อาคารเคพีเอ็นทาวเวอร์ ชั้น 11 ถนนพระราม 9 แขวงบางกะปิ เขตห้วยขวาง กรุงเทพมหานคร 10310', '0629169244', 'Sumitta@firstone.co.th', '', '3', '2024-12-02 13:47:25', '3', '', '', '', '2025-06-15 05:22:50'),
('5aa126c7-c78d-4234-b0f3-45153034626e', 'คุณบอล', 'เทศบาลเมืองมาบตาพุด', 'หัวหน้าทีม EIC', 'เลขที่ 9 ถนนเมืองใหม่มาบตาพุดสาย 7 ตำบลห้วยโป่ง อำเภอเมืองระยอง จังหวัดระยอง 21150', '0892790210', '', '', '3', '2024-12-02 13:51:04', '3', '', '', '', '2025-06-15 05:21:31'),
('5aece05b-9c59-4c41-b12f-8ceb8f25fd63', 'คุณวราค์ศิริ พูนสินศิริ', 'ธนาคารอาคารสงเคราะห์', 'ฝ่ายพัสดุ', '63 ถนนพระราม 9 เขตห้วยขวาง กรุงเทพฯ 10310', '', '', '', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2025-06-13 03:35:53', '8', '', '', '', '2025-07-14 09:08:31'),
('5db003f0-4196-451c-afda-e22e4481fefd', 'คุณกิ่งกาญจน ไทยกิ่ง', 'ธนาคารอาคารสงเคราะห์', 'พนักงานวางแผนและบริหารโครงการสารสนเทศ', '', '0615329451', 'kingkarn.t@ghb.co.th', '', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-16 02:07:59', '', NULL, '', NULL, '2025-06-16 02:07:59'),
('5db776f7-0e5f-42f2-a0de-2f76ffadf235', 'เทศบาลบางจะเกร็ง', '', '', 'จ.สมุทรสงคราม', '', '', '', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2025-06-12 09:44:34', '', NULL, '', NULL, '2025-06-12 09:44:34'),
('5e2a838a-110f-48bc-9518-f01a7066955b', 'นายอิทธิกร เล่นวารี  (นายกเทศมนตรีตำบลปากท่อ)', 'สำนักงานเทศบาลตำบลปากท่อ จ. ราชบุรี', NULL, '39 หมู่ที่ 7 ต.ปากท่อ อ.ปากท่อ จ. ราชบุรี 70140 โทรศัพท์ 032-281-266 โทรสาร 032-282-564', '0806508585', 'pakthocity@hotmail.com', 'http://www.pakthomunic.go.th/office.php', '2', '2024-10-12 06:24:53', '3', '6715036cbd552.jpg', '', '', '2024-10-20 13:19:40'),
('5f32551c-7a96-4b5f-b485-2357623e9893', 'ผู้จัดการฝ่ายจัดซื้อ', 'บริษัท โรช ไดแอกโนสติกส์ (ประเทศไทย) จำกัด', '', '', '', '', '', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-06-25 03:15:29', '', '', '', '', '2025-06-25 03:15:29'),
('616ce37f-dcfc-4921-a07c-dc9ce335ce45', 'คุณปภาณ คณาภรณ์ธาดา', 'บริษัท เอ็นเอ็มเอส กรุ๊ป แอนด์ เซอร์วิส จำกัด', '', '29/20 หมู่บ้าน กาญจนลักษณ์ 1 หมู่ที่ 11 ซอยวัดพระเงิน ตำบลบางม่วง อำเภอบางใหญ่ จ.นนทบุรี', '0993245965', 'papan.k@outlook.com', '', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-16 02:13:04', '', '', '', '', '2025-06-16 02:13:04'),
('641761a3-129e-4d38-ba11-2c4c9bb44d3f', 'เทศบาลเมืองหนองปรือ', '', '', '111 หมู่ 7 ต.หนองปรือ อ.บางละมุง จ.ชลบุรี 20150', '', '', '', '70dd36b5-f587-4aa9-b544-c69542616d34', '2025-06-11 10:25:23', '', NULL, 'เทศบาลเมืองหนองปรือ', NULL, '2025-06-11 10:25:23'),
('642afc1e-c8d5-42f3-a685-aa899e78be1e', 'Apinya Luanthaisong', 'Master Maker Co.,Ltd.', 'Operation and Business Control Manager', '274/3 Soi Rungruang, Suthisarnvinichai Road, Samsennok, Huaykwang, Bangkok 10310', '0863232642', 'apinya@mastermakerth.com', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-03-13 09:41:23', '', NULL, '022764388', NULL, '2025-03-13 09:41:23'),
('65b9a9b5-5272-4b9d-a02f-1b4c85460069', 'คุณณาศิส', 'บริษัท มายด์ซอฟท์ คอร์ปอเรชั่น จำกัด', NULL, 'เลขที่ 363/38 ซอยพหลโยธิน 26 ถนนพหลโยธิน แขวงจอมพล เขตจตุจักร กรุงเทพ 10900', '0991095665', '', '', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-03 04:23:35', '', '', '', '', '2024-12-03 04:23:35'),
('677f5f38-3f7f-4ca8-b9d6-e4b60f7f241a', 'นายสรวิชญ์ เพชรนคร', 'เทศบาลนครระยอง', 'รองปลัดเทศบาลนครระยอง', '888 ถ. ตากสินมหาราช ตำบล ท่าประดู่ อำเภอเมืองระยอง ระยอง 21000', '0996535194', '', '', '3', '2025-04-09 07:05:27', '3', NULL, '', '', '2025-04-09 07:11:28'),
('67bda6e1-da1b-41c2-8658-f11662f15f6c', 'บริษัท แซตส์ ฟู้ด โซลูชั่นส์ (ไทยแลนด์) จำกัด', '', '', '', '', '', '', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-06-12 10:29:04', '', NULL, '', NULL, '2025-06-12 10:29:04'),
('690cfd6a-0270-4b22-8d1f-de1f91dda830', 'K.Noppadol', 'AT Technology Anywhere Co., Ltd.', 'Project Management', '216/22, City Link Rama 9-Srinakarin, Kanchanaphisek, Thap Chang, Saphan Sung, Bangkok, Thailand', '0863749945', 'noppadol@atanywhere.co.th', '', '3', '2024-12-02 13:43:19', '3', '', '', '', '2025-06-15 05:23:19'),
('69512d42-1a96-42c4-b9a5-5e188b4ea0c8', 'Onpailin Poomsiriroj', 'Master Maker Co.,Ltd.', NULL, '274/3 Soi Rungruang, Suthisarnvinichai Road, Samsennok, Huaykwang, Bangkok 10310', '0863696540', 'onpailin@mastermakerth.com', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 21:51:20', '', '', '', '', '2024-10-31 21:51:20'),
('6b3ba15b-ee6d-41ab-a543-d345e9f62259', 'Auto X', 'Auto X', NULL, '', '', '', '', '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-11-11 08:45:29', '', '', '', '', '2024-11-11 08:45:29'),
('6d128135-3e95-4226-9956-21bb63f25cc0', 'คุณบอล', 'บริษัท มายด์ โซลูชั่น แอนด์ เซอร์วิส จำกัด', NULL, 'เลขที่ 363/38 ซอยพหลโยธิน 26 ถนนพหลโยธิน แขวงจอมพล เขตจตุจักร กรุงเทพ 10900', '', '', '', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-06 10:08:54', '', '', '025124318', '201', '2024-12-06 10:08:54'),
('6e210c39-30b1-44ba-948d-480b184cfe0d', 'องค์การบริหารส่วนตำบลเขาคันทรง', '', '', '198 ทางหลวงชนบท ชลบุรี 5068 ตำบลเขาคันทรง อำเภอศรีราชา ชลบุรี 20110', '', '', '', '70dd36b5-f587-4aa9-b544-c69542616d34', '2025-06-11 09:47:05', '', NULL, 'องค์การบริหารส่วนตำบ', NULL, '2025-06-11 09:47:05'),
('6e23608d-46bb-4e74-8326-21365397565b', 'คุณต้น', 'MPLUS INTERNATIONAL CO.,LTD.', 'เลขาคุณธง', '1 Empire Tower, 47th Floor., Unit 4703 (river 25), South Sathorn Road, Yannawa, Sathorn, Bangkok,10120', '0875087327', '', '', '3', '2025-04-09 06:38:37', '', NULL, '', NULL, '2025-04-09 06:38:37'),
('726fc634-2c97-418b-a230-45e936cf843b', 'สจส', 'กทม', '', '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2025-06-10 07:33:46', '', NULL, '', NULL, '2025-06-10 07:33:46'),
('74f091d7-a81e-426b-a55e-a50eeb43d8e7', 'คุณ ณรงกรค์ (เอิธ)', 'โรงพยาบาลสัตว์ทองหล่อ', '', '80 ถนนริมคลองแสนแสบ แขวงบางกะปิ เขตห้วงขวาง กรุงเทพฯ 10310', '0945604573', '', '', '1b9c09d2-dc91-4b5e-a62b-8c42a41958ab', '2025-06-23 02:34:47', '', NULL, '020799999', NULL, '2025-06-23 02:34:47'),
('778e27be-efad-41b8-a243-d40cf58bba85', 'คุณแม็ค', 'สำนักจราจรและขนส่ง กรุงเทพมหานคร', NULL, '', '', '', '', 'a5741799-938b-4d0a-a3dc-4ca1aa164708', '2024-12-10 03:47:32', '', '6757b9d40a653.png', '', '', '2024-12-10 03:47:32'),
('7f242c52-9e30-4791-97b2-053fb960423b', 'คุณประกอบ จ้องจรัสแสง', 'คุณประกอบ จ้องจรัสแสง', NULL, '215 ซอยพัฒนาการ 50 แขวงสวนหลวง เขตสวนหลวง กรุงเทพฯ 10250', '0816236990', 'prakorb@pointit.co.th', '', '5', '2024-12-02 07:06:59', '5', '', '', '', '2024-12-06 10:02:14'),
('81b62776-9408-4a36-af8e-45799f86883d', 'โรงเรียนสาธิตรามคำแหง(ฝ่ายมัธยม)', 'โรงเรียนสาธิตรามคำแหง(ฝ่ายมัธยม)', NULL, '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-11-23 13:10:13', '', '', '', '', '2024-11-23 13:10:13'),
('88bc1a3c-f646-4e7a-863d-3424b0fbe1c1', 'Toyo Tires Thailand Co.,Ltd.', NULL, NULL, NULL, NULL, NULL, NULL, '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 06:23:08', '', NULL, NULL, NULL, '2025-01-06 06:23:08'),
('88d465c6-3e16-4c58-a6da-10bce309af89', 'สตช.', 'สตช.', '', '', '', '', '', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-03-11 03:56:20', '', '', '', '', '2025-03-11 03:56:20'),
('895e71fc-991e-4b42-9803-4bcafdb03023', 'Suchart Buddhaunchalee', 'IOTtechgroup', 'Director', '', '0952822656', 'suchartb@iottechgroup.com', '', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-31 06:21:25', '', '', '', '', '2025-01-31 06:21:25'),
('8a441dab-7f49-4ff6-bb6d-327003829c1f', 'บริษัท มาสเตอ เมกเคอ', 'บริษัท มาสเตอ เมกเคอ', '', '', '', '', '', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-06-11 08:51:12', '', NULL, '', NULL, '2025-06-11 08:51:12'),
('8b315bda-7e61-4d0d-a995-3653ddda3140', 'สำนักงานตำรวจ สน.ปทุมวัน', 'สำนักงานตำรวจ สน.ปทุมวัน', NULL, '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-12-03 01:13:36', '', '', '', '', '2024-12-03 01:13:36'),
('8c214401-322b-4e34-94fd-d7f32a605d01', 'คุณตรีวิท', 'ธนาคารออมสิน', '', '', '', '', '', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-16 03:26:07', '', NULL, '', NULL, '2025-06-16 03:26:07'),
('8e364a05-4022-454d-b4fb-515393936175', 'คุณจักรกฤษ', 'สถาบันเทคโนโลยีป้องกันประเทศ', NULL, '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-11-23 13:08:02', '', '', '', '', '2024-11-23 13:08:02'),
('92463365-36a7-4898-a759-c4ef2a90cedd', 'สำนักงานตำรวจแห่งชาติ', 'สำนักงานตำรวจแห่งชาติ', NULL, '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-12-03 01:07:13', '', '', '', '', '2024-12-03 01:07:13'),
('9392ce88-098b-49a8-8df4-c4882971735e', 'คุณสุกัลยา ภิรมย์รัตน์', 'ธนาคารกรุงไทย จำกัด (มหาชน)', 'ผู้อำนวยการฝ่าย ผู้บริหารทีมย่อย ทีม Channel Management', 'เลขที่ 10 อาคารสุขุมวิท ชั้น 20 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110', '0917718387', 'sukanlaya.piromrath@krungthai.com', '', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-01-06 07:18:12', '', '677b83b484539.png', '022088340', '', '2025-01-06 07:18:12'),
('9610604a-2d45-4b3f-9c93-70791dc4f0ad', 'โรงพยาบาลพระนั่งเกล้า', '', '', '', '', '', '', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2025-06-12 09:41:14', '', NULL, '', NULL, '2025-06-12 09:41:14'),
('9a8307fa-375b-47c3-b09d-2f7ca12f0c02', 'คุณจุติฝัน คิดฉลาด', 'บริษัท เอ อาร์ ที ไบโอเทค จำกัด', 'กรรมการผู้จัดการ', '162/7 ซอยประเสริฐมนูกิจ 29 ถนนประเสริฐมนูกิจ แขวงจรเข้บัว เขตลาดพร้าว กรุงเทพมหานคร 10230', '0834965777', '', '', '3', '2024-12-02 14:49:20', '3', '', '', '', '2025-06-15 05:22:04'),
('9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', 'บริษัท นิวบางกอกอีเล็คทริค จำกัด', '', '', '', '', '', '', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-06-12 10:35:19', '', NULL, '', NULL, '2025-06-12 10:35:19'),
('9f005cab-6ce1-4813-bafe-95be81d93b1d', 'Ying Bacom', 'บริษัท เบคอม อินเตอร์เน็ทเวอร์ค จำกัด', 'Project sale', '48/1ซอยพระรามเก้า 57, 3 ซอย วิเศษสุข เขตสวนหลวง กรุงเทพมหานคร 10250', '0994151562', 'Anyapat@bacominternetwork.com', '', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-03-17 03:10:24', '', NULL, '', NULL, '2025-03-17 03:10:24'),
('a1556209-1988-461b-bdb6-dca77b0656d6', 'พี่หน่อย', 'บริษัท ไทยยูนีค จำกัด', '', '', '', '', '', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2025-06-16 02:18:24', '0', '', '', '', '2025-06-16 02:19:27'),
('a485226f-e787-44e7-a140-4bf50433c525', 'พี่จา', 'BUSINESS SOLUTIONS PROVIDER CO.,LTD.', 'กรรมการผู้จัดการ', '7/129 ชั้น 18 อาคาร สำนักงาน TowerA เซ็นทรัล ปิ่น เกล้า แขวงอรุณอมรินทร์ เขตบางกอกน้อย กรุงเทพมหานคร 10700', '0863104221', 'jaruwan@bspc.co.th', '', '3', '2024-12-02 13:29:16', '3', '', '', '', '2025-06-15 05:24:10'),
('a6371051-02a6-44d0-83f7-78a929a2fb30', 'คุณจิตรลดา', 'ธนาคารกรุงไทย จำกัด (มหาชน)', 'ฝ่ายบริหารงานจัดซื้อจัดจ้าง (งานจัดซื้อจัดจ้าง 2)', '', '', '', '', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2025-07-14 09:02:26', '', '', '0-2208-7073', '', '2025-07-14 09:02:26'),
('a686607a-56b8-4d1b-ab90-dc2c99ebd878', 'คุณศุภชัย', 'ธนาคารกรุงไทย จำกัด (มหาชน)', '', '', '', '', '', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2024-12-16 01:54:54', '', '', '', '', '2024-12-16 01:54:54'),
('a7398772-5d5f-4f09-9eb6-6edf32fb9893', 'คุณเจษฎา', 'สำนักรักษาความปลอดภัย สำนักงานเลขาธิการสภาผู้แทนราษฎร', 'หัวหน้าตำรวจรัฐสภา', '๑๑๑๑ ถนนสามเสน แขวงถนนนครไชยศรี เขตดุสิต กรุงเทพมหานคร ๑๐๓๐๐', '', 'jetsada321@gmail.com', '', '3', '2025-04-09 05:25:25', '', NULL, '', NULL, '2025-04-09 05:25:25'),
('abb7ccfe-d759-4007-81d6-1f11bc439a37', 'คุณหมอิ๋ว', 'เทศบาลนครมาบตาพุด', 'ผอ. กองสาธารณสุข', '9 ถนนเมืองใหม่มาบตาพุด สาย 7, ตำบลห้วยโป่ง อำเภอเมืองระยอง จังหวัดระยอง 21150', '0811618602', 'info@maptaphutcity.go.th', '9 ถนนเมืองใหม่มาบตาพุด สาย 7, ตำบลห้วยโป่ง อำเภอเมืองระยอง จังหวัดระยอง 21150\r\nโทร. 0-3868-5562\r\nแฟกซ์. 0-3868-5557\r\nE-mail : info@maptaphutcity.go.th , saraban_04210103@dla.go.th', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 12:42:58', '', NULL, '038 685 562', NULL, '2025-10-27 12:42:58'),
('abcb48a0-71b9-4a4f-ad23-7472470dd6e6', 'พี่ไฟท์', 'add', '', '', '', '', '', '5', '2025-06-16 07:24:11', '', NULL, '', NULL, '2025-06-16 07:24:11'),
('ad0ee715-c3b7-4df0-b097-6d0bf1565fb0', 'เทศบาลเมืองป่าตอง', 'เทศบาลเมืองป่าตอง', NULL, '', '', '', '', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2024-11-27 08:10:30', '', '', '', '', '2024-11-27 08:10:30'),
('ad2601e2-f353-4e1f-8acc-817035281810', 'ผู้จัดการฝ่ายจัดซื้อ', 'บริษัท โรช ไดแอกโนสติกส์ (ประเทศไทย) จำกัด', '', '', '', '', '', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-06-25 07:26:08', '', '', '', '', '2025-06-25 07:26:08'),
('ae83116d-3c1a-41f7-a066-3e99373b2b44', 'คุณอำพล', 'บริษัท เอที เทคโนโลยี เอนนี่แวร์ จำกัด', '', '216/22 หมู่บ้านซิตี้ลิงก์ พระราม 9-ศรีนครินทร์ ถ.กาญจนาภิเษก แขวงทับช้าง เขตสะพานสูง กรุงเทพมหานคร 10250', '0657529666', '', '', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 07:30:50', '', NULL, '', NULL, '2025-01-16 07:30:50'),
('affeb10c-dc64-41d9-952e-d6f01c2d05d1', 'Warayutt Suttivas', 'MIND SOLUTION AND SERVICES CO., LTD.', 'Technical Section Director', '', '0863694306', 'warayutt@mindss.co.th', '', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 09:17:07', '', '', '025124318', '', '2025-01-06 09:17:07'),
('b26996d4-08c7-4365-96fe-ea74a40aced8', 'องค์การบริหารส่วนตำบลพลูตาหลวง', 'บจ.ซูม อินฟอร์เมชั่น ซิสเต็ม', NULL, '', '', '', '', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-06 04:37:27', '', '', '', '', '2024-11-06 04:37:27'),
('b2907f8d-53f0-4f71-bc36-11e24a52c10d', 'ธนาคารกรุงไทย', 'ธนาคารกรุงไทย', NULL, '', '', '', '', '8c1c0a55-2610-4081-8d12-b2a6971ffbe8', '2024-12-09 07:58:18', '', '6756a31a6d8d7.png', '', '', '2024-12-09 07:58:18'),
('bcdad84b-ec95-4a80-8765-7f14d2c0a764', 'บริษัท ดับบลิวทีซี คอมพิวเตอร์ จำกัด', 'คุณปวิชชา เกตุคง', '', '', '', '', '', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-16 03:11:26', '44', NULL, '', '', '2025-06-16 03:20:52'),
('c120a5b5-375a-411b-87d4-5fa61e6453d9', 'คุณต้น', 'บมจ.ซีพีเอฟ (ประเทศไทย)', 'ผู้จัดการภาคโซนอีสาน', '168 ม.15 ต.หนองหว้า อ.เบษจลักษ์ จ.ศรีษะเกษ 33110', '', '', 'บมจ.ซีพีเอฟ (ประเทศไทย)\r\nโรงชําแหละและตัดแต่งสุกร ศรีสะเกษ\r\n168 ม.15 ต.หนองหว้า อ.เบญจลักษ์ จ.ศรีษะเกษ 33110\r\n0-1075-55000-02-3 (สาขา จ.ศรีษะเกษ เลขที 00416)', '5', '2025-06-16 00:52:13', '', NULL, '', NULL, '2025-06-16 00:52:13'),
('c2968a16-8dea-4f07-ab94-c7d2197562fa', 'สำนักงานตำรวจแห่งชาติ', 'กองบังคับการตำรวจสันติบาล', NULL, '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-12-03 01:15:27', '', '', '', '', '2024-12-03 01:15:27'),
('c918919d-7d14-4f42-97a8-3357016c382a', 'Jaruwan Chanawong', 'Business Solutions Provider Co.,Ltd.', 'Account Manager', '', '0891133003', 'jaruwan@bspc.co.th', '', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-15 07:22:43', '', '', '028849185', '101', '2025-01-15 07:22:43'),
('c9286dcf-c779-4fd0-8101-ca004bfc51ad', 'คุณเสกสรร (ไก่)', 'Synergic Technology', 'Account Assistant Manager', '', '0818875936', '', 'Smart Meeting Room & Access Control กบข.', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-02-17 03:21:23', '', NULL, '', NULL, '2025-02-17 03:21:23'),
('cb8e3303-3fd7-438c-9c64-07e6c80e012f', 'ธนาคารออมสิน', 'ธนาคารออมสิน', '', '', '', '', '', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-01-06 06:27:17', '44', NULL, '', '', '2025-06-16 03:20:19'),
('cbf32bae-0896-4e5b-ab8e-f4fdca7916f8', 'โรงพยาบาลกรุงเทพ', 'โรงพยาบาลกรุงเทพ', NULL, '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-11-23 13:08:45', '', '', '', '', '2024-11-23 13:08:45'),
('cc80c251-336b-4039-9850-5a042948e8f3', 'คุณเรวีญา ขจิตเนติธรรม', 'เทศบาลตำบลทับมา', 'ปลัดเทศบาลฯ', 'เลข ที่ 20/3 หมู่ ที่ 4 อำเภอ เมือง, ตำบลทับมา อำเภอเมืองระยอง ระยอง 21000', '0928957111', '', '', '3', '2024-12-02 14:55:51', '3', '', '', '', '2025-06-15 05:16:31'),
('cc91e26c-61c7-494a-9dc9-109298dfa5ac', 'Danai Sinsakjaroongdech', 'SmartBiz Solutions Co.,Ltd.', 'Sales Manager', '', '0954159936', 'danai@smartbiz.co.th', 'Customer Evergreen', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 06:12:26', '', '', '', '', '2025-01-06 06:12:26'),
('ccfcd9ae-df30-40e5-98a9-71bb73d4f491', 'คุณญาณิศา เข็มทอง', 'ZOOM INFORMATION SYSTEM', 'ฝ่ายขาย', '', '0873442944', 'yanisa@pointit.co.th', '', '3', '2025-06-15 06:17:50', '', '', '', '', '2025-06-15 06:17:50'),
('cdd15d78-73d7-41d6-9fad-dfd0da61a1a9', 'คุณธัญลักษณ์', 'Yamaha Motor Parts Manufacturing (Thailand) Co., Ltd.', 'ฝ่ายจัดซื้อ', '700/18 Moo 6, Soi 8 Amata Nakorn, Bangna-Trad Highway Km.57 Tambol Nongmaidang, Amphur Muang, Chonburi 20000 Thailand', '0902491469', 'thanyalak@yamaha-motor-parts.co.th', '', '3', '2024-12-02 13:39:12', '3', '', '', '', '2025-06-15 05:23:42'),
('ce88b691-16a7-477a-abaf-c06fe638ee69', 'จุฬา', 'คณะแพทยศาสตร์ จุฬาลงกรณ์มหาวิทยาลัย', '', '', '', '', '', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2025-06-16 02:20:24', '', '', '', '', '2025-06-16 02:20:24'),
('cea804cd-55ab-4a3f-b9ff-a942547402a7', 'Siripong Siriprasert', 'Supreme Distribution Public Company Limited', NULL, '2/1 Soi Praditmanutham 5, Praditmanutham Road, Tha Raeng, Bang Khen, Bangkok 10230', '0651962456', 'siripong.s@supreme.co.th', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 20:59:42', '', '', '', '', '2024-10-31 20:59:42'),
('cf5b1437-ce07-4f44-a672-ecd9cee08e41', 'คุณอเล็กซ์', 'บริษัท ธนบุรีพานิช จํากัด', 'CRM Management', 'เลขที่ 84/1 อาคารวิริยะพันธุ์ ชั้นที่ 4 ถนนจรัญสนิทวงศ์ แขวงบางพลัด เขตบางพลัด กรุงเทพมหานคร', '0909424154', '', '', '3', '2025-04-09 06:16:03', '', NULL, '', NULL, '2025-04-09 06:16:03'),
('d4efc031-32d4-487f-87ff-69afe9f948e4', 'องค์การบริหารส่วนตำบลบ่อวิน', 'บจ.ซูม อินฟอร์เมชั่น ซิสเต็ม', NULL, '', '', '', '', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-06 04:58:23', '', '', '', '', '2024-11-06 04:58:23'),
('d9e67c01-1640-47d0-aeaf-38b622f996de', 'ธนาคารกรุงไทย จำกัด (มหาชน)', 'คุณปวิชชา เกตุคง', '', '', '', '', '', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-16 03:22:37', '', '', '', '', '2025-06-16 03:22:37'),
('da4d87d6-33f5-4937-85e5-d0be395d6123', 'องค์การบริหารส่วนตำบลมาบยางพร', '', '', '199 ต.มาบยางพร อ.ปลวกแดง จ.ระยอง 21140', '', '', '', '70dd36b5-f587-4aa9-b544-c69542616d34', '2025-06-11 10:32:19', '', NULL, 'องค์การบริหารส่วนตำบ', NULL, '2025-06-11 10:32:19'),
('da8ca97f-0a95-49cd-99a4-a6f698cbe98c', 'ศาลรัฐธรรมนูญ', 'ศาลรัฐธรรมนูญ', NULL, '', '', '', '', '8c1c0a55-2610-4081-8d12-b2a6971ffbe8', '2024-12-09 07:59:46', '', '6756a372b7c5f.png', '', '', '2024-12-09 07:59:46'),
('dd7d359f-6c63-4c11-80c5-d4dfa7407c92', 'Naruemon Rayayoy', 'Master Maker Co.,Ltd.', NULL, '274/3 Soi Rungruang, Suthisarnvinichai Road, Samsennok, Huaykwang, Bangkok 10310', '0629829978', 'naruemon@mastermakerth.com', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 20:55:45', '9223372036854775807', '', '', '', '2024-10-31 21:31:25'),
('df1dbd74-1f88-4f78-80ee-60d99e1e7a15', 'ประพัฒ จันเกื้อ', 'PMOS', 'Manager Director', '', '0805425445', '', '', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:09:17', '0', NULL, '', '', '2025-10-27 06:10:16'),
('df6e7ebd-77f2-49e4-bcdf-04c71608005f', 'นางสาวจันทนา อุดม', 'นางสาวจันทนา อุดม', NULL, '8/31 ม.โชคสำอางค์  ถ.บางแวก แขวงบางไผ่ เขตบางแค กรุงเทพมหานคร 10160', '0969372260', '', '', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-06 10:03:28', '', '', '', '', '2024-12-06 10:03:28'),
('e126cf96-c67d-413b-888c-b81dc86ee9b8', 'ธีระพันธ์ น้อยผา', 'บมจ.ซีพีเอฟ (ประเทศไทย)', 'ผู้จัดการโรงงานสุกร', '168 ม.15 ต.หนองหว้า อ.เบษจลักษ์ จ.ศรีษะเกษ 33110', '0942495163', 'thiraphan.nop@hisoft.co.th', '', '3', '2025-06-15 06:32:47', '', NULL, '', NULL, '2025-06-15 06:32:47'),
('e4ad10b0-1850-4f98-82e9-56f8afe1c0ff', 'โรงพยาบาลพริ้นซ์', 'โรงพยาบาลพริ้นซ์', NULL, '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-11-23 13:09:33', '', '', '', '', '2024-11-23 13:09:33'),
('e85465c3-44e3-4210-a4ce-88f9aa09af26', 'คุณ วรา', 'องค์การบริหารส่วนจังหวัดชลบุรี', '', '', '', '', '', 'ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', '2025-10-27 07:35:55', '', NULL, '', NULL, '2025-10-27 07:35:55'),
('ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 'คุณดลพร ทรงโชติรัตน์', 'บริษัท กรุงไทยพานิชประกันภัย จำกัด (มหาชน)', '', '', '', '', '', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-11 03:16:21', '44', NULL, '', '', '2025-06-16 03:23:14'),
('edb5c314-2962-4d20-95e1-59d58f732a6d', 'บริษัท ออโต้ เอกซ์ จำกัด', 'คุณดิเรก วงศ์งาม', '', '', '', '', '', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-16 03:02:31', '44', '', '', '', '2025-06-16 03:21:13'),
('f004cbe4-f666-4de7-8e85-7f940b6d8393', 'Kanitnicha Charoenpattanaphak', 'Business Solutions Provider Co.,Ltd.', NULL, '', '0957965498', 'Kanitnicha@bspc.co.th', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 22:30:43', '', '', '', '', '2024-10-31 22:30:43'),
('f18472d8-50d9-45ed-b267-00948a15a2e9', 'สหกรณ์ออมทรัพย์ครู นครศรีธรรมราช', 'คุณวรลักษณ์', '', '', '', '', '', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-16 06:21:13', '44', NULL, '', '', '2025-06-16 06:25:26'),
('f313a7ba-64ae-4d61-af99-f493a98039b2', 'Adiphol Sermphol', 'Supreme Distribution Public Company Limited', NULL, '2/1 Soi Praditmanutham 5, Praditmanutham Road, Tha Raeng, Bang Khen, Bangkok 10230', '0814847928', 'adiphol.s@supreme.co.th', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 21:53:06', '', '', '', '', '2024-10-31 21:53:06'),
('f53a9e46-d14b-40d7-8acc-cd7a0a2ced0e', 'คุณศุภญา', 'ธนาคารกรุงไทย จำกัด (มหาชน)', '', 'เลขที่ 10 อาคารสุขุมวิท ชั้น 20 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110', '', '', '', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-16 02:33:04', '', '', '022088340', '', '2025-06-16 02:33:04'),
('f5489b6a-fd5b-4896-b655-761768e44b8f', 'SCB', 'SCB', NULL, '', '', '', '', '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-11-11 08:37:29', '', '', '', '', '2024-11-11 08:37:29'),
('f5c84ebc-38a3-47a5-8b55-aeed3c520473', 'Blue Solutions Co.,Ltd.', '', '', '', '', '', '', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-06-12 10:47:14', '', NULL, '', NULL, '2025-06-12 10:47:14'),
('f8a6cd53-4c8f-490d-83c8-85db6fb422bb', 'คุณเล้ง', 'โรงพยาบาลพระนั่งเกล้า', '', '', '', '', '', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-07-17 01:25:38', '', NULL, '', NULL, '2025-07-17 01:25:38'),
('fb683856-9635-4316-ad3a-2eb57d6eb10f', 'คุณโอฬาร สินธุพันธ์', 'ZOOM INFORMATION SYSTEM', 'Management Director', '223/16 หมู่บ้าน เซนสิริทาวน์ หมู่ที่ 1ซอย พรประภานิมิตร 17 ถนนแยกมิตรกมล ตำบลหนองปรือ อำเภอบางละมุง จ.ชลบุรี 20150', '0851511551', 'Oran.gun@gmail.com', '', '3', '2025-04-09 05:57:26', '', NULL, '', NULL, '2025-04-09 05:57:26'),
('fc372e65-cca3-4c7c-b580-c689ef2d0798', 'เทศบาลตำบลบางจะเกร็ง', 'เทศบาลตำบลบางจะเกร็ง', NULL, '', '', '', '', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2024-12-03 02:41:20', '', '', '', '', '2024-12-03 02:41:20'),
('fda15ece-1a00-4583-b354-cb5f3c01bb23', 'ศาลาว่าการเมืองพัทยา', 'บจ.ซูม อินฟอร์เมชั่น ซิสเต็ม', NULL, '', '', '', '', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 02:48:07', '0', '', '', '', '2024-11-04 03:38:49'),
('ff09ea1e-4e6a-44e0-8637-03ac0670070d', 'เทศบาลนครนครศรีธรรมราช', '', '', '', '', '', '', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2025-06-12 09:53:35', '', NULL, '', NULL, '2025-06-12 09:53:35');

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

--
-- Dumping data for table `document_links`
--

INSERT INTO `document_links` (`id`, `project_id`, `category`, `document_name`, `url`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
('0a0f3a40-ab24-11f0-9a0c-005056b8f6d0', '7c67ce7e-ee05-487f-a763-4627899516bb', 'report', 'เอกสารรายงานระบบเฝ้าระวังอุบัติเหตุและเจ็บป่วยฉุกเฉิน Smart Safety แบบรายปี 2025', 'https://docs.google.com/document/d/1qEDOqUeXuo9fp8avC4kdJFe4bra-SSlX/edit', '2025-10-17 06:39:33', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-17 06:39:33', NULL),
('0e2625cb-4a4e-11f0-bc6e-005056b8f6d0', '66c0508f-b34e-4007-938b-2a1dc2f7e297', 'other', 'SO', 'https://pointitcoth-my.sharepoint.com/:x:/g/personal/panit_pointit_co_th/EXwLbq6byBFGmMc0WUwQg8wBdvga_0CO-bZDoraalOuQEA?CID=d6f5b97d-0692-a8c6-8b1e-9ba7f9c659b4&e=iR4Sd2', '2025-06-16 01:05:56', '5', '2025-06-16 02:07:50', '5'),
('25a90a8c-b2fc-11f0-9a0c-005056b8f6d0', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', 'other', 'คลังเอกสารโครงการทั้งหมดของโครงการ', 'https://drive.google.com/drive/folders/1lYUr79pTmYtD6tveki5gq7IZO0x5W422?usp=sharing', '2025-10-27 06:14:33', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:14:33', NULL),
('298d5aee-b2f1-11f0-9a0c-005056b8f6d0', '26b7618c-cba9-47bd-a7f5-026e193dd543', 'other', 'ลิงค์ Jira Software Management', 'https://pointit-innovation.atlassian.net/jira/software/projects/LSP/list/?ignoreStickyVersion=true&jql=project+%3D+%22LSP%22&atlOrigin=eyJpIjoiMzI4ODVhZjU0MmVlNGRlMWFlZWE4NmQ3ZmNhOGI1YzUiLCJwIjoiaiJ9', '2025-10-27 04:55:55', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 04:55:55', NULL),
('393e288b-4a4f-11f0-bc6e-005056b8f6d0', 'f68d2b0e-6ba9-468a-bd47-870036ce545d', 'proposal', 'เอกสารภายในโครงการทั้งหมด', 'https://pointitcoth.sharepoint.com/:f:/s/PoliceInnopolis/ErSlUtRrltNFo8AThWApV2IBYRdq0B9v3dSN1qMR-gCOfg?e=F6U65N', '2025-06-16 01:14:18', '5', '2025-06-16 01:14:18', NULL),
('50077a3d-4a4a-11f0-bc6e-005056b8f6d0', '51e62f2e-3b91-44e8-9875-55239e0e8acc', 'proposal', 'ข้อมูลทั้งหมดของโครงการ', 'https://pointitcoth-my.sharepoint.com/:f:/g/personal/panit_pointit_co_th/EkZ5MFbHPuNCqj8XfYMz0DsBfR9635TOX1Hlx9uT5DxGjw?e=Pa7A8K', '2025-06-16 00:39:09', '5', '2025-06-16 00:39:09', NULL),
('535f0331-b2f1-11f0-9a0c-005056b8f6d0', '26b7618c-cba9-47bd-a7f5-026e193dd543', 'proposal', 'ลิงค์ รายละเอียดตาม TOR Live Stream (Pattaya)', 'https://docs.google.com/spreadsheets/d/1VdBE0_uEibdXAYuULv0Ao-ZCxjQ_7oxhLsy4Ya8e12M/edit?gid=251135509#gid=251135509', '2025-10-27 04:57:05', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 04:57:05', NULL),
('710d68a3-4a4d-11f0-bc6e-005056b8f6d0', '92a99359-0555-4ff1-9be4-c26808189158', 'other', 'SO', 'https://pointitcoth-my.sharepoint.com/:x:/g/personal/panit_pointit_co_th/EXwLbq6byBFGmMc0WUwQg8wBdvga_0CO-bZDoraalOuQEA?CID=5826239f-cef4-3445-f6cb-81a8900d9bfb&e=iR4Sd2', '2025-06-16 01:01:33', '5', '2025-06-16 01:01:33', NULL),
('9016aef7-b2f1-11f0-9a0c-005056b8f6d0', '98ae844c-7b83-4b19-bd1a-5cc769b4d5a3', 'other', 'คลังเอกสารโครงการทั้งหมดของโครงการ', 'https://drive.google.com/drive/folders/1EMTO3vUpg6U794nzQhSw-PHzfGimEkzQ', '2025-10-27 04:58:47', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 04:58:47', NULL),
('9893072c-b2fa-11f0-9a0c-005056b8f6d0', '82a6796b-dd7f-4cc7-b822-5cbee53bc4e1', 'other', 'คลังเอกสารโครงการทั้งหมดของโครงการ', 'https://drive.google.com/drive/folders/1WbN9f2TzS_Fo7fJvZUWYFY1Xl2o1AvvR?usp=sharing', '2025-10-27 06:03:27', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:03:27', NULL),
('baf65fa2-4d76-11f0-b35d-005056b8f6d0', 'c4be4e29-07c2-49b4-8495-a0b2c1e032e3', 'proposal', 'Folder โครงการ', 'https://pointitcoth-my.sharepoint.com/my?id=%2Fpersonal%2Fpanit%5Fpointit%5Fco%5Fth%2FDocuments%2FAI%20Project%202024%2F23%20%E0%B8%A3%E0%B8%B0%E0%B8%9A%E0%B8%9A%E0%B8%95%E0%B8%A3%E0%B8%A7%E0%B8%88%E0%B8%AA%E0%B8%AD%E0%B8%9A%E0%B8%A1%E0%B8%B2%E0%B8%95%E0%B8%A3%E0%B8%90%E0%B8%B2%E0%B8%99%E0%B8%9F%E0%B8%B2%E0%B8%A3%E0%B9%8C%E0%B8%A1%E0%B8%AA%E0%B8%B8%E0%B8%81%E0%B8%A3%5FCPF', '2025-06-20 01:34:38', '5', '2025-06-20 01:34:38', NULL),
('cbf454d4-b2fa-11f0-9a0c-005056b8f6d0', 'b5aaa158-5fb0-466d-9016-9f36ebf15270', 'other', 'คลังเอกสารโครงการทั้งหมดของโครงการ', 'https://drive.google.com/drive/folders/1lYUr79pTmYtD6tveki5gq7IZO0x5W422?usp=sharing*', '2025-10-27 06:04:53', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:04:53', NULL);

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
('0016f66d-64fd-431e-ad26-45b643f987cc', 'น.ส. ภัทราอร', 'อมรโอภาคุณ', 'Phattraorn', 'Amornophakun', 'female', NULL, 'Phattraorn@gmail.com', 'Phattraorn.a@pointit.co.th', '061-952-2111', 'Sales', 'Sales', '1', '5', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-06 01:45:14', 'ซีน', 'Zeen'),
('001c3d0b-b49d-43e7-9d6e-5060c74f9089', 'นายอนุรักษ์', 'บุตศรี', 'Mr.Anurak', 'Butsri', NULL, NULL, NULL, NULL, '063-7426650', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอล', NULL),
('0110c4fb-fcf0-4636-ac8b-78e0c7c48850', 'น.ส.รสชนก', 'โยธี', 'Miss Rotchanok', 'Yothee', NULL, NULL, NULL, NULL, '085-935-9244', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ตุ๋งติ๋ง', NULL),
('0196c061-a693-4d25-b500-32dadaf52163', 'คุณศุภกร', 'ใจผ่อง', 'supakorn', 'jaipong', 'male', '2002-05-23', 'supakonjaipong367@gmail.com', NULL, '086-993-6207', 'Frontend', 'Innovation', '1', '8c782887-8fd3-4f99-ac27-63054a8a1942', '150 อ.เมืองกำแพงเพชร  จ.กำแพงเพชร  ต.ในเมือง', '2025-08-18', NULL, '2', NULL, '2025-09-23 02:22:10', '2025-09-23 02:22:10', 'ไตเติ้ล', 'title'),
('01f91b84-57a6-476b-ac85-bf31ea59ffe6', 'นายอภิชิต', 'ชารีกัน', 'Mr.Apichit', 'Chareekan', NULL, NULL, NULL, NULL, '084-788-6612', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ต๋อง', NULL),
('020c88b6-bb49-43fd-a55b-b04b8a868169', 'นาย กฤษณะ', 'พรหมไหม', 'Kirtsana', 'Phrommai', NULL, NULL, NULL, NULL, '090-909-3073', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอก', NULL),
('05f8e733-dbd6-4ccf-bd8c-9cb4bf191a33', 'นาย กานต์', 'กาญจนมหกุล', 'Kan', 'Kanjanamahakul', NULL, NULL, NULL, NULL, '086-6335050', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'กาน', NULL),
('060ca930-3e08-42a9-b4ca-cd5e47af0d8c', 'นาง ผาณิต', 'เผ่าพันธ์', 'Panit', 'Paophan', 'female', NULL, 'panit@pointit.co.th', 'panitpaophan@gmail.com', '0814834619', 'Executive Director', 'IT Service', '4', '2f6d353b-53f1-4492-8878-bc93c18c5de9', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-05 03:48:10', 'พี่หญิง', 'Ying'),
('07599d62-aad8-44b4-9482-da597a525008', 'ภูดิส', 'รังษีสุริยะชัย', 'Phudis', 'Rungsissuriyachai', 'male', '1999-09-24', 'Phudis.ucsc@gmail.com', 'Phudis@pointit.asia', '061-896-2669', 'DevOps', 'Innovation', '1', '8c782887-8fd3-4f99-ac27-63054a8a1942', '32/163 Ramintra 65, Bangkok 10230', '2025-04-01', NULL, '2', NULL, '2025-06-06 16:35:25', '2025-06-06 16:35:25', 'ปัน', 'Pun'),
('082f8143-9e56-4b25-94a6-9b6da798daec', 'คุณตุลธร', 'ยงประยูร', 'Tulatorn', 'Yongprayoon', 'male', '2045-10-05', 'tulatorn@gmail.com', 'Tulatorn@pointit.co.th', '096-149-1519', 'Tester', 'Innovation', '1', '8c782887-8fd3-4f99-ac27-63054a8a1942', '62/46 หมู่ 2 ซอย 4 ถนน ทวีวัฒนา-กาญจนาภิเษก 30 เขต ทวีวัฒนา แขวง ทวีวัฒนา กรุงเทพมหานคร 10170', NULL, NULL, '2', NULL, '2025-10-27 07:26:14', '2025-10-27 07:26:14', 'ตุลย์', 'Tul'),
('0850049e-2fbf-4d51-961f-5f7fd5363416', 'น.ส. นภัสดา', 'ที่รักษ์', 'Naputsada', 'Teerak', NULL, NULL, NULL, NULL, '083-016-8818', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เป้', NULL),
('087aa549-1e7d-43d5-9262-b552a25b0bfe', 'นายอาทิตยพล', 'พิมลเอกอักษร', 'Mr.Artidtayaphol', 'Phimonekaksorn', NULL, NULL, NULL, NULL, '087-686-9443', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บิ๊ก', NULL),
('08c33992-6bc7-44c6-b2a8-9de00694705f', 'น.ส.ดวงดาว', 'แก้วเรือง', 'Duangdow', 'Kaewrueng', NULL, NULL, NULL, NULL, '087-687-1184', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ดาว', NULL),
('0965b563-9350-4941-822e-ac152969dc07', 'น.ส.ธนวรรณ', 'เที่ยงสมบุญ', 'Mr.Tanawan', 'Tengsomboon', NULL, NULL, NULL, NULL, '095-405-9339', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โย', NULL),
('0b2ea25c-3171-49d4-88c8-d9bfad9a60f1', 'นายชานนทร์', 'แก้วโชติหิรัญ', 'Mr.Chanon', 'Kaewchothiran', NULL, NULL, NULL, NULL, '087-338-0441', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นนท์', NULL),
('0b51a3a4-a2bd-44db-b9cd-dc6cb2dcf860', 'นาย นนทชัย', 'พลับอินทร์', 'Nontachai', 'Plub-in', NULL, NULL, NULL, NULL, '082-256-6580', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นนท์', NULL),
('0ca2ec28-3a41-4408-8321-751e77dfed4a', 'น.ส.ปุญชรัสมิ์', 'มงคลเตียวสกุล', 'Miss Puncharat', 'Mongkoltewsakul', NULL, NULL, NULL, NULL, '090-927-6646', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แนน', NULL),
('0ca753b6-1750-48fd-b9ee-a2e91efecf20', 'นาย สุรพันธ์', 'พะวันรัมย์', 'Mr.Surapan', 'Phawanram', 'male', NULL, 'surapan@gmail.com', 'surapan@pointit.co.th', '0879927502', 'DevOps Engineer', 'IT Service', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-05 03:51:38', 'ขวัญ', 'Khaw'),
('0e335b9a-ddbf-4586-9ae5-e438e0c43928', 'นายชุมพล', 'คงกระจ่าง', 'Mr.Chumpol', 'Khonkrajung', NULL, NULL, NULL, NULL, '086-336-0392', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จอน', NULL),
('1063724d-5db5-4843-8f0d-5d291dfc1400', 'นาย ปิยวัช', 'แผ้วฉ่ำ', 'Piyawach', 'Paewcham', NULL, NULL, NULL, NULL, '087-617-8926', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แมน', NULL),
('109bc197-84d9-4102-ab5b-c38b18c1c78f', 'นายจิรประภา', 'พรโสม', 'Mr.Jiraprapha', 'Pornsom', NULL, NULL, NULL, NULL, '088-369-8398', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จีจี้', NULL),
('11998e43-25fc-43b2-915f-b7e93be3de60', 'นาย กริชเพชร', 'พุ่มซ้อน', 'Mr.Kritpet', 'Pumsorn', NULL, NULL, NULL, NULL, '094-495-5667', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เด่น', NULL),
('119bf90b-0874-4d23-92f6-db0c5f5ee416', 'นาย สมพร', 'หล่อจีรานนท์', 'Somporn', 'Lorgeranon', NULL, NULL, NULL, NULL, '081-8050815', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พี่เข้', NULL),
('12b19ce0-f08c-456a-ac54-17fb9c260ae6', 'นาย ฐานัสท์', 'มหารัชมงคล', 'Thanat', 'Maharatchamongkhon', NULL, NULL, NULL, NULL, '094-916-9399', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เจียว', NULL),
('13b2c0e8-4f29-45b9-b82a-632cdd490e9d', 'นาย ธนาคม', 'อ่องสถาน', 'Tanacom', 'Ongsathan', NULL, NULL, NULL, NULL, '089-777-1155', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จ๋า', NULL),
('13d98cae-03c1-4485-8cd5-dc39ae5b9563', 'น.ส.ปัฐมา', 'ประการแก้ว', 'Miss Pattama', 'Prakarnkaew', NULL, NULL, NULL, NULL, '091-714-9013', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบียร์', NULL),
('154aed4c-a8b4-4a01-b2c4-53cd25133b13', 'น.ส. ภรภัทร', 'ยานธุกิจ', 'Porapath', 'Yanthukij', NULL, NULL, NULL, NULL, '088-896-2702', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แก็ป', NULL),
('157ca547-5b9d-41a6-94ff-faf29ade5dbb', 'นายกานต์ณรงค์', 'เขียวสลับ', 'Mr.Kannarong', 'Khiawsalab', NULL, NULL, NULL, NULL, '086-360-7117', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอล', NULL),
('1701eb8c-db2b-4dfb-9313-11d339cf6c64', 'นายชลชัย', 'ประดิษฐพงศ์', 'Mr.Cholachai', 'Praditthapong', NULL, NULL, NULL, NULL, '082-429-4240', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บิ๊ก', NULL),
('17e2b41b-3ae1-478a-a2c7-5134129a7aee', 'นายเฉลิมเกียรติ์', 'ศรีจำรัส', 'Mr. Chaloemkiat', 'Srijamrat', NULL, NULL, NULL, NULL, '091-775-2965', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ต้น', NULL),
('1b5da05a-ded6-449a-8886-b12c30a3d90d', 'นายเทพดนัย', 'ธรรมจักร์', 'Mr.Tepdanai', 'Thummajak', NULL, NULL, NULL, NULL, '090-173-8499', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แดน', NULL),
('1bea872c-04b1-4d56-a467-5da10dd29e59', 'น.ส.สุภาวดี', 'วุฒิศุภศิริ', 'Miss Suphawadee', 'Wutthisuphasiri', NULL, NULL, NULL, NULL, '083-267-8462', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จูน', NULL),
('1d06a9f3-2669-4283-b712-aaf8472d89c9', 'นาย สมบัติ', 'โสละมัด', 'Sombat', 'Solamad', NULL, NULL, NULL, NULL, '099-735-1366', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบียร์', NULL),
('1e14553a-4d96-49ce-a3fe-543470e1eb02', 'นายทวิ', 'บัวปลื้ม', 'Mr. Thawi', 'Buaplaum', NULL, NULL, NULL, NULL, '088-678-4959', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ไนท์', NULL),
('1e5a9630-fb72-4a6a-82df-56c061553e07', 'นายปริพัฒน์', 'อัศวศรีกุลธร', 'Mr.Paripat', 'Ausavasrikulton', NULL, NULL, NULL, NULL, '082-016-9194', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอย', NULL),
('20a7307f-310d-4543-bccf-f27faf453c27', 'นายพิภูษณ', 'มุ่งดี', 'Mr.Pipusana', 'Mungdee', NULL, NULL, NULL, NULL, '090-791-3425', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ๊ต', NULL),
('2266d0e8-6c83-4274-babf-58bff5a160b6', 'น.ส. ลิขิต', 'ชาวทองหลาง', 'Likhit', 'Chaothonglang', NULL, NULL, NULL, NULL, '091-545-0987', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อ้อย', NULL),
('230b54e6-169b-4c41-8656-79a535ec32bb', 'นาย วรรธน์พล', 'ศิริบุญลักษณ์กุล', 'Watthaphon', 'Siriboonlukkun', NULL, NULL, NULL, NULL, '085-399-0758', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ', NULL),
('26d339f3-7b5e-4abc-999a-05c51fd19ea7', 'นาย วัฒนา', 'ประภาเลิศ', 'Mr.Watthana', 'Prapalert', NULL, NULL, NULL, NULL, '063-421-1002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อั๋น', NULL),
('28203eed-3b4d-4129-91d6-09d564555937', 'นายสุบิน', 'ด่านกลาง', 'Mr.Subin', 'Danklang', NULL, NULL, NULL, NULL, '099-369-2699', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โต้ง', NULL),
('28f44c7d-d75c-4310-b079-f0e7f4445958', 'นาย สมพงษ์', 'ไตรสุทธา', 'Sompong', 'Trisutta', NULL, NULL, NULL, NULL, '063-181-9959', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โล้น', NULL),
('2e3bde74-654e-4d35-ac3a-5bcf251fd8df', 'น.ส. จารุนันท์', 'ปันใจ', 'Jarunan', 'Panjai', NULL, NULL, NULL, NULL, '086-555-4990', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แอน', NULL),
('2e964ef8-82c7-460f-ba23-56bf59e7828a', 'นายนวพล', 'สุนทรถาวร', 'Mr.Nawapol', 'Soontornthaworn', NULL, NULL, NULL, NULL, '092-2847054', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แบงค์', NULL),
('2f2b5845-c1a1-470e-857e-efd61a9916e5', 'นาย วิศรุต', 'สายโยธา', 'Wisrut', 'Saiyotha', NULL, NULL, NULL, NULL, '094-487-2740', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เติ้ล', NULL),
('33e031af-950c-4d75-8b60-f7eea1eccb0e', 'น.ส.ปรียนุช', 'ทัศบุตร', 'Miss Preeyanut', 'Thusabut', NULL, NULL, NULL, NULL, '089-6151916', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ปุ้ย', NULL),
('34660212-5152-407a-aa40-689d6160a832', 'นาย อนุชิต', 'บัวพันธ์', 'Mr.Anuchit', 'Buapan', NULL, NULL, NULL, NULL, '065-029-1450', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ทัช', NULL),
('34d8bd02-3a9a-41fa-a9c3-0df557bf24ac', 'นายเหมวัฒน์', 'ประกอบสุข', 'Mr.Hemewat', 'Prakobsuk', NULL, NULL, NULL, NULL, '081-193-5190', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โป้ง', NULL),
('355b65e8-f4a4-4c16-859c-00ef3ae79a38', 'น.ส.ญาณิศา', 'เข็มทอง', 'Miss Yanisa', 'Khamtong', NULL, NULL, NULL, NULL, '064-181-4939', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ๋', NULL),
('36b9089b-2ccf-4840-ad12-d43d985a943d', 'นายบรรจง', 'ชาติเป', 'Mr.Banjong', 'Chatpae', NULL, NULL, NULL, NULL, '088-784-5297', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จูน', NULL),
('37c39988-5f2e-4fc2-ba34-59020d3da350', 'นายณัฐวุฒิ', 'ม่วงชา', 'Mr. Nuttawut', 'Muangcha', NULL, NULL, NULL, NULL, '098-882-6239', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ๊ต', NULL),
('3b47be7b-5ef5-490b-8666-98cc73b54b82', 'นาย จิตรติชัย', 'ด้วงนาง', 'Chittichai', 'Duangnang', NULL, NULL, NULL, NULL, '082-509-0245', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จิต', NULL),
('3e92e792-1995-403a-bd34-c2ff9fff266f', 'นายกฤษฎา', 'บุญเฉลียว', 'Mr.Krissada', 'Boonchaleaw', NULL, NULL, NULL, NULL, '096-253-4210', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ปุ๊ก', NULL),
('3f350eff-549d-42ad-9d26-9c6294e9c424', 'นายสิทธิพงษ์', 'โพธิน', 'Mr.Sittiphong', 'Phothin', NULL, NULL, NULL, NULL, '061-576-2601', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แบงค์', NULL),
('402028ba-6732-4405-bedf-a5f86bc22044', 'นายพีรวัส', 'ชัยคำวัง', 'Mr.Peerawats', 'Chaikhamwang', NULL, NULL, NULL, NULL, '063-779-0099', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ไนซ์', NULL),
('40a7dd65-8ab2-422b-ae49-3263345fdf13', 'นาย วิรุตม์', 'จุดทะมาศ', 'Virut', 'Chudtamas', NULL, NULL, NULL, NULL, '086-780-4514', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บิว', NULL),
('41813955-8db9-4e53-a2ec-0cdbe76589a5', 'นายวิษณุ', 'พานา', 'Mr.Visanu', 'Pana', NULL, NULL, NULL, NULL, '080-571-4636', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นายวิษณุ', NULL),
('41993a3f-17c1-41da-ac32-66a1553eeb05', 'นายพงศธร', 'ล้วนดำรงค์กุล', 'Mr.Pongsatorn', 'Luandumrongkul', NULL, NULL, NULL, NULL, '099-286-4114', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เฟรน', NULL),
('41c6c35a-9029-4ce3-b842-d43da1319497', 'นาย ปิติ', 'นิธิธนาพรกุล', 'Piti', 'Nititanapornkul', 'male', NULL, 'Piti@pointit.co.th', 'Piti@pointit.co.th', '089-692-6913', 'Programer', 'Innovation', '1', '5', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-06 01:53:28', 'เบียร์', 'Beer'),
('43b64dc9-6c33-434f-84d4-66e7d816db8d', 'นาย วีรยุทธ', 'เจริญชื่น', 'Weerayut', 'Charoenchuen', NULL, NULL, NULL, NULL, '083-157-1142', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ยุทธ', NULL),
('44f12282-8cbe-4f29-9fd1-14a8cd017d48', 'นาย จิรานุวัฒน์', 'กุลนาฑล', 'Jiranuwut', 'Kunnathon', NULL, NULL, NULL, NULL, '083-572-0882', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อ๊อฟ', NULL),
('486cf6ce-bc81-4ab5-a31e-eccf6a3033f9', 'นายชลพรรณ', 'ภักดี', 'Mr.Choniapun', 'Pukdee', NULL, NULL, NULL, NULL, '095-097-0707', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หมี', NULL),
('48b71d45-5875-435f-9923-b49e95e5b73b', 'นายนพพล', 'หลักแหล่ง', 'Mr.Noppon', 'Laklang', NULL, NULL, NULL, NULL, '083-953-9620', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พีท', NULL),
('4951a17d-21c2-44ac-8ee6-eba10e574119', 'นายดนนท์ภรรค', 'ดาวดวง', 'Dhanonpuk', 'Daodoung', NULL, NULL, NULL, NULL, '081-842-1369', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ตี้', NULL),
('4a172280-f198-4e21-a81d-ad2829a7b093', 'นาย ชวนนท์', 'ตันชัยฤทธิกุล', 'Chawanon', 'Tanchairittikul', NULL, NULL, NULL, NULL, '099-113-2815', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอสส์', NULL),
('4d789493-f4fe-472b-9894-875690c94c74', 'นาย เสถียร', 'การคำ', 'Satean', 'Kankam', NULL, NULL, NULL, NULL, '083-124-3326', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โหน่ง', NULL),
('4d79beb9-4363-430f-b5ff-0851a5c1e143', 'นาย ทวีศักดิ์', 'เขียวสระคู', 'Taweesak', 'Khiaewsraku', NULL, NULL, NULL, NULL, '088-141-1669', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบิร์ด', NULL),
('4df5de09-0a6f-4afb-a0b4-2da33400f987', 'ว่าที่ ร.ต. วรดล', 'ดาวดวง', 'Woradol', 'Daodoung', NULL, NULL, NULL, NULL, '081-612-9616', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โด่ง', NULL),
('4e83dfbb-a125-47ce-a3ab-04bc412ffe81', 'น.ส. วรรณา', 'กิจการ', 'Wanna', 'Kitchakarn', NULL, NULL, NULL, NULL, '080-521-1843', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'คิม', NULL),
('4ecf8118-bc6d-4c0a-ab88-ba95bee8454f', 'นายกันตพัฒน์', 'จงเจริญ', 'Mr.kantaphat', 'Jongjaroen', NULL, NULL, NULL, NULL, '099-328-0644', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จิง', NULL),
('50e27beb-d2b3-45f6-a722-6564f17319d3', 'นายศักรินทร์', 'รัตน์ตัน', 'Mr.Sakkarin', 'Rattan', NULL, NULL, NULL, NULL, '096-954-6526', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ก๊อต', NULL),
('5150c3a9-5e53-4dde-9dad-c0cf64df929d', 'นายธาตรี', 'นกแจ่ม', 'Mr. Thatree', 'Nogjaem', NULL, NULL, NULL, NULL, '092-585-1828', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อูฐ', NULL),
('51a03a5c-2261-4e42-a897-a4fdda982ef1', 'นายคุณากร', 'จรัญจอหอ', 'Mr.Kunakon', 'Jaranjoho', NULL, NULL, NULL, NULL, '090-358-3846', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ใหม่', NULL),
('54f00387-cb21-43bd-8298-3f6bef951adb', 'นาย อดุลย์', 'ยะมะ', 'Adul', 'Ya-ma', NULL, NULL, NULL, NULL, '088-789-4840', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ซาบีน', NULL),
('55104721-250e-435f-9273-01fc7ea92a3c', 'นายเมธวิน', 'ทิพยมงคล', 'Mr.Metawin', 'Tippayamongkol', NULL, NULL, NULL, NULL, '093-580-5880', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'รอม', NULL),
('57398376-5a73-4a63-93ba-04271a2456f0', 'นาย นัฐนันท์', 'ฟักแก้ว', 'Natthanan', 'Fakkaew', NULL, NULL, NULL, NULL, '093-328-8985', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ชานัท', NULL),
('57619331-6351-410f-b1c3-ae67a1b9e022', 'นายพันธรัตน์', 'แก้วเกลี้ยง', 'Mr.Pantarat', 'Kaewkliang', NULL, NULL, NULL, NULL, '086-949-7060', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พัน', NULL),
('5798ec7a-ba3a-4b60-b304-d1de75025364', 'นายชนม์นุชา', 'ภู่สุวรรณ', 'Mr.Chonnucha', 'Poosuwan', NULL, NULL, NULL, NULL, '089-154-2431', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โจ', NULL),
('5a4bc6c3-929c-4d91-a8b1-0549122dcdf0', 'นายณัฐพล', 'สิงห์ทอง', 'Mr.Nutthapol', 'Singthong', NULL, NULL, NULL, NULL, '098-857-4309', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หนุ่ม', NULL),
('5b789738-c72c-4ac4-83b9-50bc1fe69aaa', 'นาย ราเชนทร์', 'มิ่งรักษา', 'Rachen', 'Mingraksa', NULL, NULL, NULL, NULL, '082-790-2062', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เชนทร์', NULL),
('5ca3b81a-f2d1-4d3a-8bd1-578700e11a1f', 'นายจิตรภานุ', 'พรหมภัทร', 'Mr.Jitpanu', 'Promphat', NULL, NULL, NULL, NULL, '080-8934471', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เพชร', NULL),
('5d1a3297-f1eb-42bf-a1f1-be83a517bc0f', 'นาย ศุภกิติ์', 'แก้วกล่ำศรี', 'Suprakit', 'Kaewklamsri', NULL, NULL, NULL, NULL, '094-249-4701', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โจ้', NULL),
('5dba38d1-969e-4d18-86bf-4df26f50c76d', 'น.ส.ธณัฐชนน', 'โภคสมบัติ', 'Miss Thanutchanon', 'Phoksombat', NULL, NULL, NULL, NULL, '062-095-6999', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'มด', NULL),
('5f0325d8-d64a-4c1c-99ea-51c190224d97', 'นายประเสริฐ', 'รัฐวิเศษ', 'Mr.Prasert', 'Rattawisad', NULL, NULL, NULL, NULL, '091-714-2267', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ฆัง', NULL),
('609f6e59-2d10-4f43-9558-c663e24167c2', 'นาย ดิเรก', 'วงศ์งาม', 'Direk', 'Wongngam', NULL, NULL, NULL, NULL, '086-379-8041', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ต้อย', NULL),
('62162065-4d9a-4be3-bf14-0cf485979edf', 'นาย พงศ์สรร', 'เปรมสิริพงษ์กุล', 'Pongsan', 'Premsiriphongkun', NULL, NULL, NULL, NULL, '094-870-9996', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เสก', NULL),
('62246af9-dd9f-48ca-805f-b34457c6c6fd', 'นายจักรพันธ์', 'ลือสัตย์', 'Mr.Jarkapan', 'Lousataya', NULL, NULL, NULL, NULL, '080-078-3396', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอก', NULL),
('62a33901-5aa6-4d6f-9aa3-d54bbe325d42', 'นายณัฏฐกิตติ์', 'ทัศนพันธุ์', 'Mr. Natthakit', 'Thussanaphan', NULL, NULL, NULL, NULL, '089-791-9394', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ณัฐ', NULL),
('638df302-3894-4a58-b91c-fa5032eb2ae1', 'นาย ยุทธนา', 'จตุรจิตราพร', 'Yuthana', 'Jaturajitraporn', NULL, NULL, NULL, NULL, '081-626-6307', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ตำรวจ', NULL),
('6a33d6bb-1192-4677-bff0-41a348043bb0', 'น.ส.สุภาพร', 'เที่ยงสมบุญ', 'Suparporn', 'Thengsomboon', NULL, NULL, NULL, NULL, '099-492-2696', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ยุ้ย', NULL),
('6ab384dd-3a07-436d-8ba1-64522e74117a', 'นายธนวิน', 'คณิตนันทกุล', 'Mr. Tanawin', 'Khanitnanthakul', NULL, NULL, NULL, NULL, '083-652-2659', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เทล', NULL),
('6ae73cf7-8803-4b8c-9276-3636f6fb660c', 'น.ส. แพรวอรุณ', 'นุ่นรักษา', 'Preaw-aroon', 'Noonruksa', NULL, NULL, NULL, NULL, '089-405-2477', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เกด', NULL),
('6c556fc8-87fd-46b0-867d-922b6af12643', 'น.ส.กัลยกร', 'เพ็ชรคง', 'Miss Kalayakorn', 'Phetkong', NULL, NULL, NULL, NULL, '095-075-2396', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จูน', NULL),
('7069f6b9-5619-4ae4-a55e-ff1c86b99099', 'นายจิราวุฒิ', 'ฉิมไทย', 'Mr.Jirawut', 'Chimthai', NULL, NULL, NULL, NULL, '086-449-0642', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'วุฒิ', NULL),
('7075bdb9-80ee-4c7f-8066-266c15969499', 'นายนัฐพล', 'กองพรมลิ', 'Mr.Nattapon', 'Kongpromli', NULL, NULL, NULL, NULL, '064-769-2194', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อาร์ม', NULL),
('710dfd16-365b-4714-a08c-ffe754ee79ac', 'นายอรรถกร', 'ปุญญะฐิติ', 'Mr. Atthakorn', 'Punyathiti', NULL, NULL, NULL, NULL, '085-993-6540', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอ็ม', NULL),
('72191de0-63bb-42fe-a80b-c12c3a5ef291', 'นายพงษ์สรรค์', 'ขาครานนท์', 'Mr.Pongsan', 'Chakranon', 'male', NULL, 'pongsan.chakranon@gmail.com', NULL, '065-578-0475', 'Ai Software Developer', 'Innovation', '1', '5', '', NULL, NULL, '2', NULL, '2025-01-06 03:09:48', '2025-01-06 03:09:48', 'ซีน', 'Zeen'),
('73effd44-931a-4a41-a702-b155bf010c14', 'นาย เอกพจน์', 'จันสาขะ', 'Ekapot', 'Junsaka', NULL, NULL, NULL, NULL, '062-661-5225', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอก', NULL),
('75668d5f-6b72-4736-8b08-2d675ea6f180', 'นาย เอกวิทย์', 'มูซา', 'Aekkawit', 'Musa', NULL, NULL, NULL, NULL, '083-448-6490', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอก', NULL),
('76e2bae3-d06a-45f6-9987-f504202511df', 'น.ส. วรลักษณ์', 'คุณสุวรรณชัย', 'Woraluck', 'Khunsuwanchai', NULL, NULL, NULL, NULL, '081-253-5130', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หญิงเล็ก', NULL),
('7748e29c-07da-49a2-bd19-427ecc7a5851', 'นาย สุรเดช', 'สุนะพรม', 'Mr.Suradate', 'Sunaprom', NULL, NULL, NULL, NULL, '088-003-9550', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบล', NULL),
('7a7225e1-ab97-47ae-a559-c302b810d6d4', 'นายยุทธนา', 'ทำคำมูล', 'Mr.Yutthana', 'Thamkhammun', NULL, NULL, NULL, NULL, '094-251-5567', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อาม', NULL),
('7a770b99-bb51-4695-b25b-a086e8f8ea1f', 'นาย วัชระพงศ์', 'ปรือปรัง', 'Watcharapong', 'Prueprang', NULL, NULL, NULL, NULL, '087-458-2215', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เก่ง', NULL),
('7c481b78-104a-4501-a676-636fc98f4b44', 'นาย สุริยา', 'บุญเอื้อ', 'Suriya', 'Boonnou', NULL, NULL, NULL, NULL, '099-058-3071', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ยา', NULL),
('7dbe221a-126d-46c3-816a-33ea9634d570', 'นายพีระพงษ์', 'บรรยงค์', 'Mr.Peerapong', 'Bunyong', NULL, NULL, NULL, NULL, '094-860-5066', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบียร์', NULL),
('7f125064-ce76-4a3f-bcc4-c0842157c3e0', 'นายอดิเทพ', 'จันทรากานตานันท์', 'Mr.Adithap', 'Chantrakantanun', NULL, NULL, NULL, NULL, '081-491-0873', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หยอง', NULL),
('7f9cd3dc-39ba-4a82-9494-5ee4ced1462d', 'นายอภิรักษ์', 'บางพุก', 'Apirak', 'Bangpuk', 'male', '1992-04-05', 'apirak.ba@gmail.com', 'apirak@pointit.co.th', '083-959-5800', 'IT Service', 'Innovation', '1', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'เลขที่ 111/1 ธนพงษ์แมนชั่น ห้อง. 302 ซ. สันนิบาตเทศบาล แขวง จันทรเกษม\r\nเขตจตุจักร ถนนรัชดาภิเษก จังหวัด กรุงเทพมหานคร 10900', NULL, '68eb5e3a55aec.jpg', '2', NULL, '2025-01-04 12:42:12', '2025-10-12 07:52:26', 'แอมป์', 'Amp'),
('7fb7605a-84bb-4dd4-a2c9-b22d8db5f904', 'น.ส.อนิษา', 'อนันตะการ', 'Miss Anisa', 'Anantakan', NULL, NULL, NULL, NULL, '089-026-8149', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ๋', NULL),
('81e4903d-911b-4a7f-93c9-5660d8caa587', 'นายสิทธิศักดิ์', 'พิบูลย์', 'Mr.Sittisak', 'Phiboon', NULL, NULL, NULL, NULL, '095-429-7104', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอย', NULL),
('8264761d-2d42-4963-bfff-57f68af50506', 'นาย กวี', 'กรุณา', 'Kavee', 'Karuna', NULL, NULL, NULL, NULL, '082-790-2069', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ออด', NULL),
('833e79a9-d5e2-45a0-8580-8c09e80335b5', 'น.ส.ปราณี', 'ศรีวรานนท์', 'Miss Pranee', 'Sriwaranon', NULL, NULL, NULL, NULL, '093-920-9551', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ฝ้าย', NULL),
('83a5fbb4-c4e3-43aa-a926-91d3a70e759a', 'นาย พัชรพงศ์', 'พูลสุข', 'Patcharapong', 'Poolsuk', NULL, NULL, NULL, NULL, '087-436-7617', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'สเน็ก', NULL),
('891069a6-3957-4517-aa41-1c025dc26ab2', 'นาย ภาณุวัฒน์', 'สุขชีพ', 'Panuwat', 'Sukcheep', NULL, NULL, NULL, NULL, '089-116-0969', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แพน', NULL),
('8a18bf02-f612-4977-9fea-01ed55cbbec4', 'นายฟักครูดีน', 'ยะยอ', 'Mr.Fakrudin', 'Yayo', NULL, NULL, NULL, NULL, '087-393-4050', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ดีน', NULL),
('8a3fd425-e40f-46e2-ada9-8f9e129cac2b', 'นาย ประกอบ', 'จ้องจรัสแสง', 'Prakorb', 'Jongjarussang', NULL, NULL, NULL, NULL, '081-623-6990', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พี่กอบ', NULL),
('8aefa618-9799-4d34-b7fd-ae66b05cb8e1', 'นายสิทธิพร', 'จึงศรีพิษณุ', 'Mr. Sittiporn', 'Jungsrepisanu', NULL, NULL, NULL, NULL, '096-849-9989', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อ๊อฟ', NULL),
('8be4f4d5-e1fc-417e-9acf-7872bfedae34', 'นาย กิตติกร', 'ม่วงจีน', 'Kittikorn', 'Muangieen', NULL, NULL, NULL, NULL, '083-230-7797', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'กร', NULL),
('8c8101cd-5919-4223-978f-3192d816fb7a', 'นาย นิมิตร', 'หีบสัมฤทธิ์', 'Nimit', 'Hebsumrit', NULL, NULL, NULL, NULL, '082-790-2054', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'มิตร', NULL),
('8cfd6989-01a5-4102-aaf0-b458aef568cd', 'น.ส. ชรินทร์ทิพย์', 'วรพุทธตนัน', 'Charintip', 'Vorraputtanun', NULL, NULL, NULL, NULL, '089-955-7764', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บี๋', NULL),
('8f12e843-f684-4dc8-ab46-06013ac11e0b', 'นายอนุวัฒน์', 'พรมลี', 'Mr.Anuwat', 'Promlee', NULL, NULL, NULL, NULL, '065-916-4814', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เจมส์', NULL),
('8f1598ff-4211-4894-92a3-f91a1b97d93a', 'นายปัญณทัต', 'ปองดี', 'Mr.Pannathat', 'Pongdee', NULL, NULL, NULL, NULL, '095-667-3169', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอล', NULL),
('8ff7c801-ba11-409a-80ab-82019073e018', 'นาย อิทธิพร', 'สุวรรณสิงห์', 'Itiporn', 'Suwannasing', NULL, NULL, NULL, NULL, '085-829-0902', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อิท', NULL),
('9050d4c4-ba7e-4247-9690-dcea357553b4', 'นายอภิชาติ', 'เชิดเพชรรัตน์', 'Mr.Aphichat', 'Choedpetcharat', NULL, NULL, NULL, NULL, '088-711-0203', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โต้', NULL),
('93ba3215-f270-485a-a509-ea934d4fcade', 'นายปิยพร', 'ดีเลิศ', 'Mr.Piyaporn', 'Dee-lert', NULL, NULL, NULL, NULL, '093-616-5265', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แก๊ส', NULL),
('94e5f4b7-b97f-4f5e-8241-c1ac44caec21', 'นาย โอฬาร', 'สินธุพันธ์', 'Oran', 'Sintuphan', 'male', NULL, 'Oran.gun@gmail.com', NULL, '085-151-1551', 'Management Diractor ', 'Sales', '2', '2f6d353b-53f1-4492-8878-bc93c18c5de9', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-16 08:49:41', 'ปืน', 'Gun'),
('968da662-e2c9-4079-957f-7da6e12c71b1', 'นาย สมพงษ์', 'ปานเผือก', 'sompong', 'Pranpuek', NULL, NULL, NULL, NULL, '082-790-2060', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เหมา', NULL),
('97a47848-0290-48ed-9616-4b81209b406c', 'นาย ภูษิต', 'มูหะหมัด', 'Phusit', 'Muhamad', NULL, NULL, NULL, NULL, '095-583-3294', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ลีด', NULL),
('97ff614d-2913-4317-bd44-a828b033714a', 'นาย ฉัตรชัย', 'บุญรัตนเสถียร', 'Chaichai', 'Bunrattanasatien', NULL, NULL, NULL, NULL, '085-560-9000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ตุ้ม', NULL),
('980a8c86-ecad-4b35-a6a6-dea83e4bc141', 'นาย ทวีวัฒน์', 'สวัสดี', 'Taweewat', '-', NULL, NULL, NULL, NULL, '097-245-2449', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เปี๊ยก', NULL),
('9979da4c-c3e7-4026-9654-0c03a7aa8297', 'คุณจิราธิป', 'วิทยานุศักดิ์', 'Jiratip', 'vittayanusak', 'male', NULL, 'j.vittayanusak@gmail.com', NULL, '090-221-5120', 'Software Tester', 'IT Service', '1', '8c782887-8fd3-4f99-ac27-63054a8a1942', '', NULL, NULL, '2', NULL, '2025-01-06 01:49:08', '2025-01-06 01:49:08', 'เอิร์ธ', 'Earth'),
('9b4e89a6-bf08-442a-81df-92470e43b240', 'นายสุกิจ', 'พรพิทยากุล', 'Mr. Sukit', 'Pornpittayakul', NULL, NULL, NULL, NULL, '097-194-8265', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'กิจ', NULL),
('9b998747-978c-48c9-ac5e-f46b1be3b7ae', 'นาย เอกลักษณ์', 'จันทร์เรือง', 'Aekaluk', 'Junruang', NULL, NULL, NULL, NULL, '063-746-4702', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โม่', NULL),
('9c07a6c4-2328-4902-8acb-0f1126797064', 'นายเอกลักษณ์', 'สงวนสินธิ์', 'Mr.Aekalak', 'Sa-nguansin', NULL, NULL, NULL, NULL, '082-072-2972', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เจมส์', NULL),
('9c6f04da-8096-428d-9045-af39787957c5', 'นาง อรุณณีย์', 'เทียมทวีสิน', 'Arunnee', 'Thiamthawisin', NULL, NULL, NULL, NULL, '081-341-1724', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หน่อย', NULL),
('9cafb053-e8f6-4168-bd2a-5b951bbcf54b', 'นายณัฐวัตร', 'มั่งนิมิตร', 'Mr.Natthawat', 'Mungnimit', NULL, NULL, NULL, NULL, '062-556-0393', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เต้ย', NULL),
('9cc0e5db-dea6-44ef-941b-b2525ae6ba2e', 'นายชาญวิทย์', 'บุญมา', 'Mr. Chanwit', 'Boonma', NULL, NULL, NULL, NULL, '084-633-7959', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ๊ต', NULL),
('9fbc57a2-c79c-43d3-b2f2-f6b8e5931d75', 'น.ส. ณฐพรสรวง', 'ชนะแสน', 'Natapornsuang', 'Chanasan', NULL, NULL, NULL, NULL, '086-789-5323', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แหม่ม', NULL),
('a0d1c9bc-9a40-4b04-8917-3edd6d534297', 'นาง สุภาภรณ์', 'สมสงวน', 'Supaporn', 'Somsanguan', NULL, NULL, NULL, NULL, '082-790-2068', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เพชร', NULL),
('a188554c-23d6-4815-b9ce-7a14fba41788', 'น.ส. ฐิติชญา', 'มาลาล้ำ', 'Thitichaya', 'Malalam', NULL, NULL, NULL, NULL, '091-739-7430', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เพ็ญ', NULL),
('a2447f3e-f44e-4ca5-a359-a4bbcaa2306b', 'นาย รุ่งโรจน์', 'มานพ', 'Rungrote', 'Manob', NULL, NULL, NULL, NULL, '088-874-4141', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โรจน์', NULL),
('a32363c3-470d-4788-8592-e6a1c46b9318', 'น.ส.จิราภรณ์', 'บุตรอุดม', 'Miss Jiraporn', 'Bootudom', NULL, NULL, NULL, NULL, '085-043-8518', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จิ', NULL),
('a4b633e4-b737-45bf-b1bb-d4b9cc649b6e', 'นายณัฐกุล', 'จรรยาเจริญกุล', 'Mr.Nattakun', 'Janyacharoenkun', NULL, NULL, NULL, NULL, '082-601-5502', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เพียว', NULL),
('a5624855-feb0-4cd7-b500-be0753fcf995', 'นายสันทัศ', 'วาทีกานท์', 'Mr.Suntut', 'Wateekarn', NULL, NULL, NULL, NULL, '086-749-8928', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอย', NULL),
('a5ed74c6-64a7-43cb-a67f-b2d3b635c224', 'น.ส. พรรณราย', 'บุญมาก', 'Pannarai', 'Boonmark', NULL, NULL, NULL, NULL, '081-303-0472', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จูน', NULL),
('a8ed33a8-4522-4b3b-badb-d2bddf28e5ae', 'นายวสวัตติ์', 'หาญชิงชัย', 'Mr.Wasawat', 'Hanchingchai', NULL, NULL, NULL, NULL, '081-001-6449', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อาร์ม', NULL),
('a9a5f869-ba2d-4f91-b925-e5c83aaf4589', 'นาย เอกรินทร์', 'หาญประชุม', 'Ekarin', 'Hanprachum', NULL, NULL, NULL, NULL, '084-561-5321', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อ๊อฟ', NULL),
('a9ebfeda-7be3-4dee-b503-f2cb4cd2ddbe', 'นาย สำเนียง', 'จำปาแดง', 'Samniang', 'Champadang', NULL, NULL, NULL, NULL, '086-445-1652', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หลุ่ย', NULL),
('a9f9f40b-78d8-4ec5-9f3d-6ffdf96bdfbe', 'นายพรรณภัทร', 'ภูธนะกูล', 'Punnaphat', 'Phuthanakoon', NULL, NULL, NULL, NULL, '097-946-6599', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'กิ่ง', NULL),
('add349cc-bad2-41e8-a6e4-edf51049ebfe', 'น.ส.ภรรตธีรา', 'ดาวดวง', 'Pateera', 'Daodoung', NULL, NULL, NULL, NULL, '097-195-9756', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จิน', NULL),
('af10727c-1a47-4379-afa9-c14ca65b6578', 'นาย พรศักดิ์', 'อุทยานิก', 'Pornsak', 'Uttaganik', NULL, NULL, NULL, NULL, '083-428-3524', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบิร์ด', NULL),
('af873f82-ce92-402b-a5be-9a6e582b276a', 'น.ส.ทัตพิชา', 'อับดุลสลาม', 'Miss Tachpicha', 'Abdulsalam', NULL, NULL, NULL, NULL, '089-672-0380', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'กีส', NULL),
('b2ecd517-43e1-411e-bff0-6143541d038a', 'ว่าที่ ร.ต. สุวรินทร์', 'เศรษฐธราวงศ์', 'Suwarin', 'Settarawong', NULL, NULL, NULL, NULL, '082-169-4929', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หมี', NULL),
('b4e0645a-df8e-4c64-bd33-18366e10f5c0', 'นาย ธรรมนูญ', 'พงษ์ธนู', 'Thummanoon', 'Phongthanoo', NULL, NULL, NULL, NULL, '090-295-8539', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เลย์', NULL),
('b7e7b967-809a-4f51-be3b-314f5e54f3e6', 'น.ส. สุภาภรณ์', 'งามเปลี่ยม', 'Supaporn', 'Ngampliam', NULL, NULL, NULL, NULL, '088-691-4565', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ฝน', NULL),
('ba4cabdc-17a0-42da-92f6-572effd61dc7', 'ภูมิศักดิ์', 'จันทร์ล้วน', 'Poomsak', 'Janluan', 'male', '2537-06-27', 'poomsak1994@gmail.com', NULL, '086-229-5093', 'Full Stack Developer', 'Innovation', '1', '8c782887-8fd3-4f99-ac27-63054a8a1942', '85 หมู่ 9 ต.นาพู่ อ.เพ็ญ จ.อุดรธานี', '2567-01-04', '677b4d923d6e0.jpg', 'ff2acbbb-4ec0-4214-8a30-eb1fc6e02700', NULL, '2025-01-06 03:27:14', '2025-01-06 03:32:25', 'ภูมิ', 'Poom'),
('bacffdf9-60ea-476d-ba96-71af04f68026', 'นาย สุชานนท์', 'ศรีประพันธ์', 'Suchanon', 'Sriparphan', NULL, NULL, NULL, NULL, '063-7134519', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นัท', NULL),
('bb35fbff-87e4-49b1-877f-931d26eb15b5', 'นาย สราวุธ', 'พูสุวรรณ', 'Sarawut', 'Poosuwan', NULL, NULL, NULL, NULL, '080-658-0866', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ปอนด์', NULL),
('bb63771e-f802-4b3d-b38b-706c0f8fe0eb', 'นาย นนทวัฒน์', 'พัฒนกุล', 'Nontawat', 'Pattanakul', NULL, NULL, NULL, NULL, '061-732-8420', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นน', NULL),
('bbd25605-f34d-4810-a57b-cd0c8ff99e4d', 'นายศุภชัย', 'เหมืองทรายมูล', 'Mr.Supachai', 'Muangsaimoon', NULL, NULL, NULL, NULL, '080-492-9178', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นายศุภชัย', NULL),
('bcd4c579-562a-402c-a4de-3ad307b5fd95', 'นาย ธีรชาติ', 'ติยพงศ์พัฒนา', 'Theerachart', 'Tiyapongpattana', 'male', NULL, 'theerachart17@gmail.com', 'theerachart@pointit.co.th', '0819838998', 'Service Management', 'IT Service', '1', '5', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-06 03:50:21', 'ตั้ม', 'Tum'),
('be822eae-0f92-419b-8653-725f80c8235f', 'นายธวัชชัย', 'โตนวน', 'Mr.Thawatchai', 'Tonuan', NULL, NULL, NULL, NULL, '095-061-1681', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แม็ก', NULL),
('bf678ded-d8ef-45c3-905c-bdafafdfa081', 'นายกาโรล', 'เฟรมัน', 'Mr.Karel', 'Veerman', NULL, NULL, NULL, NULL, '087-271-3776', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จอนท์', NULL),
('c05883b4-12cc-4fd2-b938-c9fe034dc3f1', 'นายณัฐดนัย', 'แจ่มแจ้ง', 'Mr.Natdanai', 'Jamjang', NULL, NULL, NULL, NULL, '081-190-7860', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นัด', NULL),
('c3fa26dd-4c54-4386-933a-f65846e84020', 'น.ส. มาลี', 'แซ่หว้า', 'Marlee', 'Sawar', NULL, NULL, NULL, NULL, '090-996-3690', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'มาลี', NULL),
('c71c1c48-3204-4806-b893-0f42ea45a69d', 'นาย จักรกริช', 'พ้นภัย', 'Jakkrit', 'Pontpai', NULL, NULL, NULL, NULL, '081-484-8835', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เพิร์ล', NULL),
('c7b8143b-a6a6-4ea8-b0b0-3737e48929ea', 'ว่าที่ ร.ต.หญิง ชุติพันธุ์', 'วอนเบ้า', 'Acting Sub Lt. Chutiphan', 'Wonbao', NULL, NULL, NULL, NULL, '098-264-0562', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ชุ', NULL),
('c8434fe7-b4b5-41a8-8166-ca8be6a7b03d', 'นาย บุลากร', 'พัวพันธุ์', 'Bulakorn', 'Puapun', NULL, NULL, NULL, NULL, '081-360-2828', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พีท', NULL),
('c8846a35-9d2d-4a9c-b367-244b333afb1c', 'นาย ลิขิต', 'รุ่งเรือง', 'Likit', 'Rungruang', NULL, NULL, NULL, NULL, '084-250-0422', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ชาง', NULL),
('c8efd0c0-5134-4070-9833-936ed3049557', 'นาย ธรรมรัตน์', 'โพธิวัฒน์', 'Thammarat', 'Phothiwat', NULL, NULL, NULL, NULL, '091-545-0995', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ่ง', NULL),
('c95d9acd-3e69-48e9-afae-27a23f0a9b1e', 'น.ส.ฟาตีมะห์', 'สาเต็ง', 'Miss Fatimah', 'Sateng', NULL, NULL, NULL, NULL, '061-448-5706', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'มะห์', NULL),
('c9b28e79-0165-46a2-b9e0-9c8579529c14', 'นาย นัฐพล', 'ศรีประกอบ', 'Nattaphon', 'Sriprakob', NULL, NULL, NULL, NULL, '091-734-0063', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ก๊อต', NULL),
('cb77ead9-ad40-4657-9610-08aabba8efa9', 'ว่าที่ ร.อ. อิศเรศ', 'เกตุเดชา', 'Isaret', 'Ketdecha', NULL, NULL, NULL, NULL, '064-097-3480', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ดุ๊ก', NULL),
('cdf26871-edae-4849-8c19-e75d3d7b9051', 'นาย อวิรุทธ์', 'สมสงวน', 'Awirut', 'Somsanguan', NULL, NULL, NULL, NULL, '081-834-1331', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แว่น', NULL),
('cf306963-754e-4469-9c91-9d534bb72d69', 'นายศศิกร', 'สัญญาอริยาภรณ์', 'Mr.Sasikorn', 'Sanyaariyaporn', NULL, NULL, NULL, NULL, '083-415-9966', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ทีม', NULL),
('cfa9b8fa-d92b-4e81-8e5f-74edf5acb532', 'นายยุทธนา', 'เส็มไข', 'Mr.Yuttana', 'Semkhai', NULL, NULL, NULL, NULL, '090-704-5641', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ริท', NULL),
('cfc63ce0-713c-4c99-940c-0204dfdec09c', 'นายธนัชชา', 'คงสมใจ', 'Mr.Thanatcha', 'Kongsomjai', NULL, NULL, NULL, NULL, '080-098-7677', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อาร์ม', NULL),
('cfe4264f-24b2-4e6c-8641-b7eec4985dfc', 'ว่าที่ ร.ต. พิสุทธ์', 'วงศ์โสภา', 'Phisut', 'Wongsopha', NULL, NULL, NULL, NULL, '091-545-0988', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เชษฐ์', NULL),
('d21600f0-7c43-4822-9bb1-8bf5300b4eda', 'นาย พิศาล', 'ศิริบัณฑิตย์', 'Pisarn', 'Siribandit', NULL, NULL, NULL, NULL, '081-911-4692', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พิศาล', NULL),
('d223e154-f7de-4c1c-a17c-19a85edf2d2f', 'นายกฤษณะ', 'โตสอาด', 'Mr.Kridsana', 'Tosa-ad', NULL, NULL, NULL, NULL, '091-061-6305', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อาร์ม', NULL),
('d36bc038-b249-468e-a3ed-e01d5a781b9b', 'นายปวรุตม์', 'บุตรจันทร์', 'Mr.Pawarut', 'Bootchan', NULL, NULL, NULL, NULL, '095-846-9398', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เปา', NULL),
('d3cce445-e684-4af4-b9c0-dd386658fccc', 'น.ส.เบญจมาศ', 'มีนาค', 'Miss Benjamas', 'Meenak', NULL, NULL, NULL, NULL, '083-778-7059', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ขวัญ', NULL),
('d45fa00f-eb00-4023-96d2-f4766a95c6da', 'นาย พศิน', 'จันพร้อม', 'Pasin', 'Chanphrom', NULL, NULL, NULL, NULL, '062-348-5353', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ดิษ', NULL),
('d516df1f-e81d-406a-94e0-09ff1774a9ed', 'นายสัมพันธ์', 'ประสิทธิเขตกิจ', 'Mr.Samphan', 'Parsittiketekit', NULL, NULL, NULL, NULL, '091-704-1626', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อัท', NULL),
('d6a77940-c799-4452-adf4-0dded8b7d470', 'นาย ธีรยุทธ์', 'แคว้งใจ', 'Teerayut', 'Kaengjai', NULL, NULL, NULL, NULL, '088-088-4969', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แจ๊ค', NULL),
('d72f9275-43c3-4989-aba2-181b2f75628d', 'น.ส. ปวิชชา', 'เกตุคง', 'Pawitcha', 'Katekong', NULL, NULL, NULL, NULL, '084-464-9246', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นก', NULL),
('d7554419-c280-4f1b-8a65-747013138875', 'นาง พรพรรณ', 'สรรพทรัพย์สิริ', 'Pornpan', 'Suppasubsiri', NULL, NULL, NULL, NULL, '097-331-9824', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ป้าโส่ย', NULL),
('d843c477-0ebf-4fc8-b4a9-6ec76fa6c8d8', 'นายพงศกร', 'หมั่นประกอบ', 'Mr.Pongsakorn', 'Munprakop', NULL, NULL, NULL, NULL, '080-832-0818', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พงศ', NULL),
('da02ba90-a15f-495b-b533-71824126f2e2', 'นายณัชพล', 'ยศราวาส', 'Mr.Nutchapol', 'Yotsarawas', NULL, NULL, NULL, NULL, '082-480-4318', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หนึ่ง', NULL),
('dabf09bf-4374-4a76-be88-fcd76475c378', 'นายจิรศักดิ์', 'อินยฤทธิ์', 'Mr. Jirasak', 'Inyarit', NULL, NULL, NULL, NULL, '087-484-8009', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ', NULL),
('db38edd8-2787-4892-bfda-828b57da36bb', 'นายมูฮัมมัดอามีรูเด็ง', 'นาแซ', 'Mr. Muhammad-armirudeng', '-', NULL, NULL, NULL, NULL, '082-234-8254', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อามี', NULL),
('dc422082-567a-4726-b177-fa76b54e763d', 'นาย ธนวัต', 'สันติอนุรักษ์', 'Thanawat', 'Santianurak', NULL, NULL, NULL, NULL, '095-805-7527', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นิค', NULL),
('dce4c1c7-fcdb-4122-bde4-1ef03406ce67', 'นาง ขนิษฐา', 'อ่องสถาน', 'Kanitta', 'Ongsathan', NULL, NULL, NULL, NULL, '088-022-3292', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เชอรี่', NULL),
('df600b53-afcc-4da2-bba5-38e5229e37ab', 'นาย พิชัย', 'บริบาลบุรีภัณฑ์', 'Phichai', 'Boribanbureephan', NULL, NULL, NULL, NULL, '090-131-8323', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ตู่', NULL),
('e1993400-7c75-45d2-b452-81885882534b', 'นาย อภิศักดิ์', 'จันทร์หนองสรวง', 'Apisak', 'Jannongsrong', NULL, NULL, NULL, NULL, '063-473-0990', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ศักดิ์', NULL),
('e46f0f37-5f31-48c0-ae6e-66eef30af78f', 'นาย ธาวิน', 'พร้อมเจริญ', 'Tawin', 'Promcharoen', NULL, NULL, NULL, NULL, '088-874-4142', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นิว', NULL),
('e71752cf-f292-47c3-839f-b51c311cff0e', 'นาย เอกชัย', 'เขียวสด', 'Akkachai', 'Khiawsod', NULL, NULL, NULL, NULL, '088-022-3282', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อ๊อด', NULL);
INSERT INTO `employees` (`id`, `first_name_th`, `last_name_th`, `first_name_en`, `last_name_en`, `gender`, `birth_date`, `personal_email`, `company_email`, `phone`, `position`, `department`, `team_id`, `supervisor_id`, `address`, `hire_date`, `profile_image`, `created_by`, `updated_by`, `created_at`, `updated_at`, `nickname_th`, `nickname_en`) VALUES
('e746b7c4-cee1-4816-9f4f-f4ee02edd61d', 'นายศุภชัย', 'ซื่อตรง', 'Mr.Supachai', 'Suetong', NULL, NULL, NULL, NULL, '099-185-4333', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เตย', NULL),
('e76d95e1-1a90-4a7c-b65a-5013db101c2b', 'น.ส.อัญชลี', 'โอฬารจารุชิต', 'Anchalee', 'Olancharuchit', NULL, NULL, NULL, NULL, '084-704-8919', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นุ้ย', NULL),
('e802e276-1fb5-4805-9d89-d7b2e393f395', 'น.ส.สุวลี', 'หลักหาญ', 'Miss Suwalee', 'Lakhan', NULL, NULL, NULL, NULL, '083-100-4414', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จี', NULL),
('e8985b74-0c37-412b-8e5e-338e4db9a471', 'น.ส.ณัฐทิญา', 'ดาวประดิษฐ์', 'Miss Nattity', 'Daopradit', NULL, NULL, NULL, NULL, '094-590-2913', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แนน', NULL),
('eb38e9bb-9ab2-4a7a-b886-f569402709d8', 'น.ส. นันทิกา', 'จ้องจรัสแสง', 'Nanthika', 'Chongcharassang', 'female', NULL, 'nanthika@gmail.com', 'nanthika@pointit.com', '063-197-9263', 'Project Manager', 'Enterprise', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-06 01:50:58', 'โม', 'Mo'),
('eba684ff-0ab3-4082-878b-92a2d8773fa5', 'น.ส. ธนวรรณ', 'สีหราช', 'Tanawan', 'Siharaj', NULL, NULL, NULL, NULL, '098-167-9929', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'น้ำหวาน', NULL),
('ec65aef7-9df2-4da2-be8b-a027b445b589', 'น.ส. มัทธนา', 'คล้อยวิถี', 'Matthana', 'Khloiwithi', NULL, NULL, NULL, NULL, '089-8902794', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ป้านิด', NULL),
('ec79f246-4382-4a6b-bd32-3ac55c48bfd6', 'น.ส.ชฎาวรรณ', 'ดำคำ', 'Miss Chadawan', 'Dumkhum', NULL, NULL, NULL, NULL, '083-418-2215', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นิด', NULL),
('ecd30c57-b1f0-453d-bdc9-d426a5e8f0e7', 'น.ส.วินยาดา', 'บึงกระเสริม', 'Miss Winyada', 'Buengkraseam', NULL, NULL, NULL, NULL, '094-616-2944', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'มด', NULL),
('ed1d30d4-c223-4b8d-bdf5-8c0d5212f8e5', 'นายฐสิษฐ์', 'ชัยวังกุลพัฒน์', 'Mr.Thasit', 'Chaiwangkunlaput', NULL, NULL, NULL, NULL, '062-376-5454', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ไมค์', NULL),
('ee14a91d-61ef-4390-93c9-28b65fa6e390', 'นายนิกร', 'อ่อนชื่นจิตร', 'Mr.Nikorn', 'Oncheunjit', NULL, NULL, NULL, NULL, '081-677-0902', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'มัง', NULL),
('ef31401f-208d-4574-83c7-b8000ff3665d', 'นายวัฒนชัย', 'น้อยหล่อง', 'Mr.Wattanachai', 'Noilong', NULL, NULL, NULL, NULL, '064-108-3718', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แชมป์', NULL),
('f049ab3c-88a9-4dc5-813a-7f8f4d451228', 'นายกิตติพงษ์', 'บุญสูง', 'Mr.kittipong', 'Boonsung', NULL, NULL, NULL, NULL, '082-018-6679', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เก่ง', NULL),
('f1aaff60-e88e-4905-a166-00808a181664', 'นาย เดชา', 'สุรัจกุลวัฒนา', 'Mr.Decha', 'Suratkullwattana', 'male', NULL, 'suratkullwattana.decha@gmail.com', 'decha@pointit.co.th', '0931581695', 'นักพัฒนาระบบ (C ,C# , JAVA)', 'IT Service', '1', '5', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-05 03:46:37', 'ไข่', 'Khai'),
('f328af8d-389f-41ec-a5cb-00d74b85f7e3', 'นาย ธนกาลบดี', 'กังสรัตน์วาณิช', 'Thanakalbodee', 'Kungsaratwanich', NULL, NULL, NULL, NULL, '091-545-0983', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอก', NULL),
('f38f805b-a1b3-4965-ae4f-a2374f7e2110', 'น.ส. ปรัชญา', 'ปู่แตงอ่อน', 'Pratchaya', 'Poo-tangon', NULL, NULL, NULL, NULL, '093-107-2152', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ปัท', NULL),
('f4b78c9b-b0aa-42f2-8fcb-53c8e125c67e', 'นายภัฏพล', 'กาญจนสิริวิโรจน์', 'Mr.Pattapol', 'Kanjanasirivirote', NULL, NULL, NULL, NULL, '082-241-4676', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ปาร์ค', NULL),
('f4e0505f-63d5-4491-8182-9bcda079e6fc', 'นายธนกฤต', 'สิงห์ประเสริฐ', 'Mr.Thanagrid', 'Singprasoed', NULL, NULL, NULL, NULL, '084-780-2502', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โจ้', NULL),
('f4f8b336-b65c-4a5a-9ed2-2d15a08dd3f1', 'คุณก้องภพ', 'จ้องจรัสแสง', 'Kongpob', 'Jongjarussang', 'male', '2568-07-01', 'kongpob662@icloud.com', NULL, '096-886-3937', 'DevOps Engineer', '', '1', NULL, '69/8 หมู่5 ซ.เทศบาลบางปู118 ต.บางปูใหม่ ถ.สุขุมวิท อ.เมือง จ.สมุทรปราการ 10280', '2025-07-01', NULL, '2', NULL, '2025-07-03 02:12:54', '2025-07-03 02:12:54', 'ก้อง', 'Kong'),
('f54e9031-a608-4f8c-8a0e-450f98c1ede6', 'นายโศภณ', 'ปุญณกิตติ', 'Mr.Somon', 'Punnakitti', NULL, NULL, NULL, NULL, '094-810-5337', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พล', NULL),
('f604f0ce-34ae-4808-8e5a-699e6e012092', 'นาย บุญเกิด', 'ทีภูเขียว', 'Boongerd', 'Theehukhieo', 'male', NULL, 'boongerd@pointit.co.th', 'boongerd@pointit.co.th', '081-874-1889', 'System Engineer Manager', 'Enterprise', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '19 Soi Suphaphong 1 split 6,\r\nKweng Nongbon', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-06 04:41:08', 'บอส', ''),
('f655d139-ca13-4449-9c03-d7604659ca98', 'นาย มนตรี', 'โพธิ์ศรี', 'Montri', 'Poesri', NULL, NULL, NULL, NULL, '087-602-0009', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ต้อม', NULL),
('f7c4d951-23d7-4e01-a116-3bf4cb61a356', 'นายพลังประชา', 'มีแสง', 'Mr.Phalangpracha', 'Meeseang', NULL, NULL, NULL, NULL, '090-336-9929', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอย', NULL),
('f836c2c9-95dd-4c47-8b3f-b53d8045e69d', 'นาย คฑาวุฒิ', 'วงศ์งาม', 'Khatawut', 'Wongngam', NULL, NULL, NULL, NULL, '098-535-9654', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอส', NULL),
('f84f12af-300a-4bb5-a321-fd622516412c', 'นายชัยวัฒน์', 'บุญเพ็ชร', 'Mr.Chaiwat', 'Bunpech', NULL, NULL, NULL, NULL, '087-781-3822', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'กอล์ฟ', NULL),
('f8befa7c-b5b7-496c-a6f2-70be59ce5e73', 'น.ส.อัจฉรา', 'สมนางรอง', 'Miss Atchara', 'Somnangrong', NULL, NULL, NULL, NULL, '092-473-2951', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อัจฉรา', NULL),
('f91a680f-54df-49d3-8c71-a2330e96db65', 'นาย อิสรา', 'เดชคำภู', 'Isara', 'Detchacumpoo', NULL, NULL, NULL, NULL, '091-545-0992', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ทูล', NULL),
('fa8be4b1-3350-4786-bec0-d02a9879fabe', 'นาง นุจรี', 'ชาวทองหลาง', 'Nutjaree', 'Chaowtonglang', NULL, NULL, NULL, NULL, '088-874-4140', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นก', NULL),
('fce2bf50-704c-48b4-8b04-219a2c247b34', 'น.ส.ดารณี', 'ปุญญะฐิติ', 'Miss Daranee', 'Punyathiti', NULL, NULL, NULL, NULL, '096-843-7008', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอ๋', NULL),
('fdbb0a58-25d3-4413-b986-d12538f963f6', 'นาย กฤษณพงศ์', 'เย็นเยือก', 'Kitsanapong', 'Yenyuak', NULL, NULL, NULL, NULL, '081-653-1517', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบียร์', NULL),
('febfd06b-7d52-45f2-a13f-48d68d2fbe5f', 'นายอนุชา', 'หลุยจันทึก', 'Mr.Anucha', 'Luichanthuek', NULL, NULL, NULL, NULL, '065-881-4493', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบียร์', NULL);

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

--
-- Dumping data for table `employee_documents`
--

INSERT INTO `employee_documents` (`document_id`, `employee_id`, `document_name`, `document_category`, `document_type`, `file_path`, `file_size`, `description`, `upload_date`, `uploaded_by`, `updated_at`, `updated_by`) VALUES
('9ac17573-a740-11f0-aff6-005056b8f6d0', '7f9cd3dc-39ba-4a82-9494-5ee4ced1462d', 'Resume_11-10-2025', 'resume', 'pdf', 'uploads/employee_documents/7f9cd3dc-39ba-4a82-9494-5ee4ced1462d/9ac144e4-a740-11f0-aff6-005056b8f6d0.pdf', 1002793, 'Resume_11-10-2025', '2025-10-12 07:54:47', '2', '2025-10-12 07:54:47', NULL);

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
('075afde8-650f-4d75-b73d-f41242854682', 'Software Devlopment', 'การพัฒนาระบบตามความต้องการของลูกค้า', NULL, NULL, NULL, NULL, NULL, '2', '2024-10-11 23:18:35', '2', '2024-12-04 09:28:29', '075afde8-650f-4d75-b73d-f41242854682.jpeg'),
('0e1e8969-0d80-4d96-9571-c7d650945a77', 'Onsite Service', '', 'ครั้ง', NULL, NULL, NULL, '3', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-06-16 01:56:44', NULL, '2025-06-16 01:56:44', NULL),
('0eb7a552-9888-4541-a43d-a6fa5b143dbc', 'Bio IDM', 'Bio IDM', '1', 0.00, 0.00, NULL, '1', '5', '2024-12-02 07:10:43', '2', '2025-04-22 09:41:19', NULL),
('162fd42b-855e-40ac-8696-0d0535fbe2b1', 'Implementation', '', NULL, NULL, NULL, NULL, NULL, '2', '2024-11-01 00:09:37', '2', '2024-12-04 09:19:59', '162fd42b-855e-40ac-8696-0d0535fbe2b1.jpg'),
('19747bf2-8f2d-47db-a2e8-4fca20843812', 'Toner', 'Toner for Printer Samsung 203E', NULL, NULL, NULL, NULL, NULL, '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-11-11 08:38:16', NULL, '2024-11-11 08:38:16', NULL),
('1d285bc6-cc8c-47f7-900e-bf84c92f12ad', 'ค่าเช่าเครื่อง Printer', 'ค่าเช่าเครื่องเงินไชโย', NULL, NULL, NULL, NULL, NULL, '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-11-11 08:39:17', NULL, '2024-11-11 08:39:17', NULL),
('1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 'Auto Update Passbook Printer', 'Hitachi BH-180AZ', NULL, NULL, NULL, NULL, NULL, '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-01-06 04:17:21', NULL, '2025-01-06 04:17:21', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc.png'),
('2d24b1a5-6944-4536-aeff-71ee4a5a4187', 'Visio LTS Professional 2024_Comercial', '', 'License', 16725.00, 20000.00, NULL, '4', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-06-19 02:11:37', NULL, '2025-06-19 02:11:37', NULL),
('3224e7a4-44ee-40ad-a6ac-22305c2b01eb', 'Smart Healthcare', 'ชุดกระเป๋า (Health Kit Set) สำหรับตรวจสุขคัดกรอกสถานะสุขภาพเคลื่อนที่ เก็บค่าข้อมูลเข้าระบบ โดยการตรวจวัดค่าจากอุปกรณ์เชื่อมต่อเข้ากับระบบ', NULL, NULL, NULL, NULL, NULL, '2', '2024-10-11 22:58:23', '2', '2024-12-04 09:27:28', '3224e7a4-44ee-40ad-a6ac-22305c2b01eb.jpg'),
('3431f4cb-f892-4e08-a9af-240a743ebc25', 'Smart Safety', 'งานเกี่ยวกับกล้องโทรทัศน์วงจรปิด\r\nและงานสายใยแก้วนำแสง\r\nรวมถึงซ่อมแซม CCTV', NULL, NULL, NULL, NULL, NULL, 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:26:48', '2', '2024-12-04 09:23:35', '3431f4cb-f892-4e08-a9af-240a743ebc25.jpg'),
('3bf8bc62-f878-4fd9-9bee-2a6917190458', 'Magnetic Stripe LKE477U-N', 'Magnetic Stripe', NULL, NULL, NULL, NULL, NULL, '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-11-11 08:48:14', NULL, '2024-11-11 08:48:14', NULL),
('45e92af5-138c-44de-9a2f-c6fd2e56427e', 'Software License', 'การขาย Software License แบบซื้อมาขายไป เช่น Microsoft , Anti Virus', '-', NULL, NULL, NULL, '4', '5', '2025-06-24 01:20:24', NULL, '2025-06-24 01:20:24', NULL),
('49ded2cf-f13a-4b63-b5dd-ab07478f1519', 'HP Laser Printer', '', 'Unit', NULL, NULL, NULL, 'db32697a-0f69-41f7-9413-58ffe920ad7d', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-06-10 06:55:51', NULL, '2025-06-10 06:55:51', NULL),
('4c85d842-54f3-4f06-87e6-553f81488234', 'Smart Emergency', 'ระบบเฝ้าระวังเหตุฉุกเฉิน', NULL, NULL, NULL, NULL, NULL, '2', '2024-10-12 06:18:20', '3', '2024-10-20 13:35:30', '4c85d842-54f3-4f06-87e6-553f81488234.png'),
('54b6a0a0-54c2-448c-a340-71d12acdc5f6', 'Kudsonmoo', 'ระบบวิเคราะห์สุกร', 'ชุด', 0.00, 0.00, NULL, '1', '5', '2025-06-13 03:04:17', '5', '2025-06-16 00:48:24', '54b6a0a0-54c2-448c-a340-71d12acdc5f6.png'),
('581f6ca7-8e1e-447a-9dae-680755c4fd29', 'Installation', 'งานจ้างเหมาติดตั้งโครงการฯ', NULL, NULL, NULL, NULL, NULL, 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:15:31', '2', '2024-12-04 09:21:55', '581f6ca7-8e1e-447a-9dae-680755c4fd29.jpg'),
('6e2ba9df-293d-4d88-b85e-4399e237d8c0', 'K-Lynx Platform', 'Smart Management', 'ระบบ', 300000.00, 500000.00, '23722daa-6eec-4a29-aa60-89cdea4dcd8c', NULL, '3', '2025-04-09 05:50:58', NULL, '2025-04-09 05:50:58', NULL),
('7defdc10-75d8-4433-8b4f-0eeba38b674f', 'BioIDM Face Scan', 'ระบบยืนยันตัวตน ผ่านการเปรียบเทียบใบหน้า บัตรประจำตัวประชาชน และอื่นๆ', NULL, NULL, NULL, NULL, NULL, '2', '2024-10-11 23:18:48', NULL, '2024-10-11 23:52:54', ''),
('8f1ce116-f010-4b04-b112-d4de66204eef', 'Smart Card Reader', '', 'Set', NULL, NULL, NULL, 'db32697a-0f69-41f7-9413-58ffe920ad7d', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-06-11 07:59:26', NULL, '2025-06-11 07:59:26', NULL),
('983ec118-a24e-4982-9c9d-80ad223b94c1', 'SIS Price List มิ.ย.2025', 'xxxxxxxxxx', '-', NULL, NULL, NULL, '4', '5', '2025-06-24 01:00:54', NULL, '2025-06-24 01:00:54', NULL),
('a2b0ad6f-aaf7-49ef-a6d3-b3d4f55e02f9', 'iPad Air 6 M3 11\"', '', 'Unit', NULL, NULL, NULL, 'db32697a-0f69-41f7-9413-58ffe920ad7d', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-06-11 08:00:18', NULL, '2025-06-11 08:00:18', NULL),
('a46f567c-b5bf-4c7c-843c-f59975388a59', 'Barcode', '', 'Unit', NULL, NULL, NULL, 'db32697a-0f69-41f7-9413-58ffe920ad7d', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-06-11 08:00:55', NULL, '2025-06-11 08:00:55', NULL),
('a94ff83b-e9ff-4d21-87ed-a7849f8e710b', 'Passbook Printer', 'เครื่องอัพเดทสมุดธนาคาร', 'เครื่อง', NULL, NULL, NULL, '3', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2025-06-13 08:54:51', NULL, '2025-06-13 08:54:51', NULL),
('aa203517-e140-4abc-9fa8-0e9926365967', 'Signotec Gamma', 'Signature Pad', NULL, NULL, NULL, NULL, NULL, '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-11-11 08:40:01', NULL, '2024-11-11 08:40:01', NULL),
('abf31336-8385-4be6-9a6c-587719a5e0df', 'NEW6260', '3in1 Pinpad', NULL, NULL, NULL, NULL, NULL, '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-11-11 08:40:33', NULL, '2024-11-11 08:40:33', NULL),
('ae10bae3-0b1c-419f-8b21-8c57c607d3de', 'MA', '', NULL, NULL, NULL, NULL, NULL, '3', '2024-10-15 21:56:31', NULL, '2024-10-15 21:56:31', ''),
('b9fcda13-e694-4e04-a8df-fdf27ee08979', 'IBOC', 'มหาวิทยาลัยขอนแก่น', NULL, NULL, NULL, NULL, NULL, '2', '2024-10-11 23:19:12', '3', '2024-10-11 23:54:16', ''),
('c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 'Smart Showroom', 'ระบบ AI เพื่อเพิ่มประสิทธิภาพของ Showroom', 'ระบบ', 25000.00, 50000.00, '23722daa-6eec-4a29-aa60-89cdea4dcd8c', NULL, '3', '2025-04-09 06:09:43', NULL, '2025-04-09 06:09:43', NULL),
('de486d4d-c877-40a8-a113-d92b2dfcbda5', 'Hardware', '', NULL, NULL, NULL, NULL, NULL, 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-11-25 01:15:10', '2', '2024-12-04 09:19:33', 'de486d4d-c877-40a8-a113-d92b2dfcbda5.jpg'),
('df374787-e96c-4d3c-8089-3867edd96cf4', 'Project Management', '', NULL, NULL, NULL, NULL, NULL, '3', '2024-10-31 21:59:27', '2', '2024-12-04 09:25:06', 'df374787-e96c-4d3c-8089-3867edd96cf4.jpg'),
('e021eb8c-6bd5-49a7-a652-8f0bdc860a17', 'Microsoft 365', 'Microsoft 365 Business Standard\r\nMicrosoft 365 Business Premium', NULL, NULL, NULL, NULL, NULL, '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 04:39:19', NULL, '2025-01-06 04:39:19', NULL);

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

--
-- Dumping data for table `product_documents`
--

INSERT INTO `product_documents` (`id`, `product_id`, `document_type`, `file_path`, `file_name`, `file_size`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
('d8700981-9fb2-457a-a548-6b41057e3915', '54b6a0a0-54c2-448c-a340-71d12acdc5f6', 'manual', '../../../uploads/product_documents/25b1a14d-1daf-4d47-a1d3-39348435a47a.pdf', 'Kudsonmoo Grading AI.pdf', 4903505, '2025-06-13 03:04:17', '5', '2025-06-13 03:04:17', NULL);

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

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `project_name`, `start_date`, `end_date`, `status`, `contract_no`, `remark`, `sales_date`, `seller`, `team_id`, `sale_no_vat`, `sale_vat`, `cost_no_vat`, `cost_vat`, `gross_profit`, `potential`, `es_sale_no_vat`, `es_cost_no_vat`, `es_gp_no_vat`, `customer_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `product_id`, `vat`) VALUES
('0082723c-a633-407f-bd10-a76d6c64b2cf', 'พัฒนาระบบ QR และติดตั้ง บนเครื่อง AUP ธ.กรุงไทย', '0000-00-00', '0000-00-00', 'ยกเลิก (Cancled)', '', '', '0000-00-00', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 14018691.59, 15000000.00, 9345794.39, 10000000.00, 4672897.20, 33.33, 0.00, 0.00, 0.00, NULL, '2024-11-11 08:38:02', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2025-10-11 08:00:04', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('009b7557-c96b-4f2c-aeba-3649b4278cb2', 'เช่าใช้อุปกรณ์และระบบแพลตฟอร์มเฝ้าระวังเหตุฉุกเฉินการล้มในผู้สูงอายุภายในบ้านและภายนอกบ้าน   Emergency Monitoring', '2024-10-07', '0000-00-00', 'ชนะ (Win)', 'CNTR-00066/68', '', '2024-10-10', '3', '1', 494747.66, 529380.00, 280373.83, 300000.00, 214373.83, 43.33, 494747.66, 280373.83, 214373.83, '32104ee7-4b28-400b-bb7b-1ab55e1cf19d', '2024-11-07 04:39:00', '3', '2025-10-11 08:00:04', NULL, '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('00d0728f-5754-4490-b568-55cb9f79da53', 'โครงการจัดหาระบบนวัตกรรมตำรวจสร้างเมืองปลอดภัย (POLICE INNOPOLIS SYSTEMS) ของตำรวจภูธรภาค 6 (New Watchman)', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', 'โครงการจัดหาระบบนวัตกรรมตำรวจสร้างเมืองปลอดภัย (POLICE INNOPOLIS SYSTEMS) ของตำรวจภูธรภาค 6 (New Watchman) การพัฒนาระบบตามความต้องการของลูกค้า', '2025-10-29', '3', '1', 3000000.00, 3210000.00, 50000.00, 53500.00, 2950000.00, 98.33, 3000000.00, 50000.00, 2950000.00, NULL, '2025-10-16 07:41:00', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-25 15:13:47', '3', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('02d2d4de-f417-40b2-b81a-51fb3da16374', 'ระบบพิสูจน์ตัวตน BIO IDM', '2025-01-01', '2025-12-31', 'ชนะ (Win)', '-', '', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 279000.00, 298530.00, 167400.00, 179118.00, 111600.00, 40.00, 279000.00, 167400.00, 111600.00, '616ce37f-dcfc-4921-a07c-dc9ce335ce45', '2025-06-16 02:25:52', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', NULL, '0eb7a552-9888-4541-a43d-a6fa5b143dbc', 7.00),
('02f2b2b7-c67e-4255-95fe-247196b92206', 'ซื้อโทรศัพท์ผ่านเครือข่าย (IP Phone) ขององค์การบริหารส่วนจังหวัดชลบุรี', '2025-05-21', '2025-08-15', 'ชนะ (Win)', '130/2568', '', '2025-05-21', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 60943.93, 65210.00, 36000.00, 38520.00, 24943.93, 40.93, 60943.93, 36000.00, 24943.93, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2025-10-27 07:51:47', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-27 07:51:47', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('0317f37a-b177-48eb-bad3-d297a74edd1e', 'โครงการพัฒนาศักยภาพด้านความปลอดภัยพื้นที่การจราจรบริเวณเทศบาลเมืองป่าตอง ระยะที่  1', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '0000-00-00', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 5467289.72, 5850000.00, 4000000.00, 4280000.00, 1467289.72, 26.84, 2733644.86, 2000000.00, 733644.86, 'ad0ee715-c3b7-4df0-b097-6d0bf1565fb0', '2025-06-11 09:06:05', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('0369b298-43fe-4664-8eaa-f71e691586fe', 'ระบบการประชุมและถ่ายทอดสด', '0000-00-00', '0000-00-00', 'แพ้ (Loss)', '', '', '2025-06-13', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 308411.21, 330000.00, 229100.00, 245137.00, 79311.21, 25.72, 0.00, 0.00, 0.00, NULL, '2024-11-28 08:45:41', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2025-10-11 08:00:04', '5', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('040612ac-aaca-4901-9a36-a92dba0cca31', 'ระบบบริหารศูนย์ภัยพิบัติ กรมปศุสัตว์', '2025-05-07', '2025-09-30', 'ชนะ (Win)', '', '', '2025-05-07', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 2210000.00, 2364700.00, 1776062.00, 1900386.34, 433938.00, 19.64, 2210000.00, 1776062.00, 433938.00, '642afc1e-c8d5-42f3-a685-aa899e78be1e', '2025-06-10 09:40:26', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('04b15a59-91a2-4bab-9dd1-6366c49a06d2', 'จ้างซ่อมแซมท่อร้อยสายใต้ดินและสายใยแก้สนำแสงของระบบกล้องโทรทัศน์วงจรปิด จำนวน 2 รายการ', '2024-10-01', '2025-01-28', 'ชนะ (Win)', '400/2567', '', '2024-09-30', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 4074766.36, 4360000.00, 1085500.00, 1161485.00, 2989266.36, 73.36, 4074766.36, 1085500.00, 2989266.36, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 03:10:34', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2025-10-27 05:24:40', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('05c427ba-81af-4873-9e10-df57427e8305', 'ระบบฌาปนกิจสงเคราะห์ สตช.', '2024-09-30', '2024-12-25', 'ชนะ (Win)', '', 'Sangfor HCI, Netowrk, Microsft License', '2024-08-19', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 2850000.00, 3049500.00, 1982863.87, 2121664.34, 867136.13, 30.43, 2850000.00, 1982863.87, 867136.13, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:22:34', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('05d29d2b-39ab-4c46-b34b-801ede800172', 'โครงการพัฒนางานระบบจัดซื้อจัดจ้าง  กบข.', '2023-12-25', '2024-12-25', 'ชนะ (Win)', 'PO2024012', '', '2023-10-20', '3', '1', 3200000.00, 3424000.00, 2650000.00, 2835500.00, 550000.00, 17.19, 3200000.00, 2650000.00, 550000.00, '34ea3368-fa1c-445a-aeb8-821c87086d3a', '2024-10-17 04:53:37', '3', '2025-10-11 08:00:04', NULL, '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('0671d500-0edc-4c53-abfa-e9886cbf7c0c', 'งานจ้างซ่อมแซมระบบกล้องโทรทัศน์วงจรปิด จำนวน 4 จุด', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '0000-00-00', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 93336.45, 99870.00, 40000.00, 42800.00, 53336.45, 57.14, 46668.22, 20000.00, 26668.22, NULL, '2025-06-11 12:03:55', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('0781e56d-1e40-4dec-8b65-0bd316277935', 'BSP ISO Document', '2025-05-27', '0000-00-00', 'ชนะ (Win)', '', 'เป็นการทำข้อตกลงรวมกันในการพัฒนาระบบ\r\nPackgate 300,000 -\r\nPIT Initial Cost of Development Software  30% (606,000-)\r\nImplement 45% ( BSP = 75% PIT = 25%)\r\nSource Code 45% ( BSP = 50% PIT = 50%)\r\nCommission 10% Sales Lead', '2025-06-11', '3', '1', 660000.00, 706200.00, 100000.00, 107000.00, 560000.00, 84.85, 660000.00, 100000.00, 560000.00, '34ea3368-fa1c-445a-aeb8-821c87086d3a', '2025-06-11 11:58:24', '3', '2025-10-25 14:59:06', '3', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('078a1fd1-f8bb-4c94-86d5-d35fbf00d1bb', 'Laser Printer สำหรับสาขา 700 Set', '2025-06-10', '2025-08-31', 'ยื่นประมูล (Bidding)', '', '', '2025-06-10', '3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 23000000.00, 24610000.00, 20700000.00, 22149000.00, 2300000.00, 10.00, 23000000.00, 20700000.00, 2300000.00, 'f5489b6a-fd5b-4896-b655-761768e44b8f', '2025-06-10 07:02:17', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-10-11 08:00:04', '5', '49ded2cf-f13a-4b63-b5dd-ab07478f1519', 7.00),
('08122fe3-3a63-47f7-abf3-fbd14d3d947e', 'Signature Pad', '2024-08-31', '2024-10-31', 'ชนะ (Win)', '', '', '2024-08-31', '3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 24672897.20, 26400000.00, 20066121.50, 21470750.00, 4606775.70, 18.67, 24672897.20, 20066121.50, 4606775.70, NULL, '2024-11-11 08:55:52', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-10-11 08:00:04', NULL, 'aa203517-e140-4abc-9fa8-0e9926365967', 7.00),
('0945cd12-6bde-4ce9-9f52-d1e2429f24e8', 'MS SQL 2022 std', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-06-30', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 31000.00, 33170.00, 18000.00, 19260.00, 0.00, 0.00, 3100.00, 1800.00, 1300.00, '0d4e8645-ff06-4531-bc5a-09e6570248d8', '2025-06-30 14:10:04', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-10-11 08:00:04', NULL, '45e92af5-138c-44de-9a2f-c6fd2e56427e', 7.00),
('0acb3ca0-9c79-4953-a686-1f4ab035b35c', 'Health Kit Set', '0000-00-00', '0000-00-00', 'แพ้ (Loss)', '', '', '2024-11-18', '3', '1', 186284.11, 199324.00, 142242.99, 152200.00, 44041.12, 23.64, 0.00, 0.00, 0.00, '594abb40-0296-4aa0-a1fd-82f479359ed5', '2024-12-02 13:49:43', '3', '2025-10-25 15:08:25', '3', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('0b0be872-6e8c-4ad8-a958-a5a583d97ba7', 'อุปกรณ์', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-06-12', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 12571.00, 13450.97, 10620.00, 11363.40, 1951.00, 15.52, 12571.00, 10620.00, 1951.00, '2d4d1aec-e4e4-4836-9308-4c2c19da05cb', '2025-06-23 10:36:02', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-10-11 08:00:04', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('0b23febb-a6b0-4897-99b0-f181f3dfe903', 'MA DLD Server', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 160000.00, 171200.00, 88804.49, 95020.80, 71195.51, 44.50, 160000.00, 88804.49, 71195.51, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:44:06', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('0b8f2e6e-5e6f-42c9-8ab5-aafa5ad065eb', 'ระบบบริหารจัดการส่วนกลาง K-Lynx 3 จุด', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', 'Project-co: พี่ซีน\r\nUX/UI: พี่แอมป์\r\nDev Internal: พี่ขวัญ\r\n\r\n**จุดละ 200,000 บาท', '0000-00-00', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 600000.00, 642000.00, 0.93, 1.00, 599999.07, 100.00, 0.00, 0.00, 0.00, '0968cd06-9d79-4933-8de8-399cb9ac5868', '2025-01-17 11:33:20', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-10-11 08:00:04', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '0eb7a552-9888-4541-a43d-a6fa5b143dbc', 7.00),
('0c12c58d-8300-4010-a010-6e0c6a9141f9', 'โครงการแฟลตตำรวจ สกบ', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', '', '2025-05-01', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '28534929-e527-4755-bd37-0acdd51b7b45', 10000000.00, 10700000.00, 7000000.00, 7490000.00, 0.00, 0.00, 0.00, 0.00, 0.00, '1d9884a8-7762-4f28-a3b5-8419f13ffe8b', '2025-06-10 07:46:17', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2025-10-11 08:00:04', '5', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('0d26c143-d6c0-4956-a8d6-5fed886c0a61', 'MA AUP Hitachi สหกรณ์ออมทรัพย์ครู นครศรีธรรมราช', '2025-01-01', '2025-12-31', 'ชนะ (Win)', '', '', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 7000.00, 7490.00, 3000.00, 3210.00, 4000.00, 57.14, 7000.00, 3000.00, 4000.00, 'f18472d8-50d9-45ed-b267-00948a15a2e9', '2025-06-16 06:21:27', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('0df824e9-139b-4fe0-af22-e2c390df0cc6', 'WA SCB Pinpad 3in1 จำนวน 3,000u (ปีที่ 1/5)', '2025-01-01', '2025-12-31', 'ชนะ (Win)', 'PO.674111068117 Date 28/08/2024', 'เริ่ม 01/12/2024 - 30/11/2029', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 600000.00, 642000.00, 10000.00, 10700.00, 590000.00, 98.33, 600000.00, 10000.00, 590000.00, '0f80acd4-d034-4175-b501-f879a9e203de', '2025-01-30 07:53:14', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', 'abf31336-8385-4be6-9a6c-587719a5e0df', 7.00),
('0e29e992-1d85-4173-93c4-7348863a0362', 'Commissioning  Sats Food เฟส 3', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-07-09', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 120000.00, 128400.00, 70000.00, 74900.00, 50000.00, 41.67, 120000.00, 70000.00, 50000.00, '9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', '2025-07-17 01:14:38', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('0e5e2055-5b01-41f6-9456-e04974667287', 'MA VPN Firewall', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-04-19', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 399000.00, 426930.00, 355640.00, 380534.80, 0.00, 0.00, 39900.00, 35564.00, 4336.00, '2f1ee3da-fe91-4f06-b0ae-62a206c7cd5d', '2025-06-12 11:10:09', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('0fff7c73-0e40-4539-9a16-63fc5e610e04', 'โครงการจัดหาระบบนวัตกรรมตำรวจสร้างเมืองปลอดภัย (POLICE INNOPOLIS SYSTEM)​ ภาค 6', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '1. วางระบบ K8s (P&#039;khwan + Pun = 500,000)\r\n1.1 Migrate ระบบ Watchman ขึ้นบน K8s\r\n1.2 Migrate IBOC ขึ้นบน K8s\r\n\r\n2. พัฒนาโปรแกรม Watchman\r\n2.1 UX/UI (P&#039;Amp)\r\n2.2 Front-end (Poom)\r\n2.3 Back-end (P&#039;Beer)\r\n2.4 Tester (Earth)\r\n\r\n3. Project Management (Following ISO 29110)\r\n3.1 Project-co (P&#039;Zeen)\r\n3.2 Inspector (Mo)', '0000-00-00', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2025-04-28 07:28:13', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-10-11 08:00:04', NULL, '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('104d9772-4091-4b50-bad7-b89e445cdada', 'NID DLD', '2024-08-08', '2024-10-08', 'ชนะ (Win)', '', 'Dell Server, Microsoft License', '2024-08-05', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 541000.00, 578870.00, 357230.65, 382236.80, 183769.35, 33.97, 541000.00, 357230.65, 183769.35, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 22:25:01', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('1103540e-b227-4f59-8729-1b85ecd0d05d', 'MA Faculty of Veterinary Science Chulalongkorn University', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '- Preventive Maintenance (PM: 8x5 NBD) 12 Times/year\r\n- Corrective Maintenance (CM: 8x5 NBD) 4 Times/year\r\n- Remote/Email/Call unlimited\r\n- Include Travel expenses and Accommodation (BKK Area)\r\n- Start date 1-Oct- 2024 to 30-Sep-2025', '2025-01-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 90000.00, 96300.00, 0.93, 1.00, 89999.07, 100.00, 90000.00, 0.93, 89999.07, 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', '2025-01-06 09:48:27', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('1145f48e-cbed-4c90-807b-23181de39e4f', 'เคลมประกัน KTB สาขาโกสุมพิสัย ครั้งที่ 2', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-001997', 'SN 0897124C น้ำเข้าเครื่อง Mainboard ช๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 48000.00, 51360.00, 3500.00, 3745.00, 44500.00, 92.71, 48000.00, 3500.00, 44500.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 03:36:15', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('11bf7c88-a563-4a01-8419-a32500a9194d', 'โครงการจ้างเหมาบริการจัดหาและพัฒนาติดตั้งระบบเตือนภัยและแจ้งเหตุร้ายด้วยระบบข้อความผ่านโครงข่ายโทรศัพท์เคลื่อนที่ (Cell Broadcast) ของศูนย์ปฏิบัติการสํานักงานตํารวจแห่งชาติ (ศปก.ตร.) สํานักงานตํารวจแห่งชาติ (Police Emergency Warning System ; PEWS)', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', 'End user : RTP\r\nProject prime : MM', '0000-00-00', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', 'de3fc0f5-9ebf-4c47-88dd-da5a570653ae', 89376612.82, 95632975.72, 76500140.31, 81855150.13, 12876472.51, 14.41, 0.00, 0.00, 0.00, '88d465c6-3e16-4c58-a6da-10bce309af89', '2025-03-17 03:51:49', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-10-11 08:00:04', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('1338b661-5cb2-438f-ad71-e24316d9b2ae', 'Access Control and Time &amp;amp;amp; Attendance Terminal', '0000-00-00', '0000-00-00', 'แพ้ (Loss)', '', '', '2024-09-07', '3', '1', 200000.00, 214000.00, 121495.33, 130000.00, 78504.67, 39.25, 0.00, 0.00, 0.00, '213830aa-08d9-4673-9081-3fcba6ce1625', '2024-12-02 13:35:41', '3', '2025-10-25 15:09:46', '3', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('15288aa6-9497-471c-812c-3251021c8f72', 'MA Faculty of Dentistry Chulalongkorn University', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '- Preventive Maintenance (PM: 8x5 NBD) 3 Times/year (4 months per time)\r\n- Corrective Maintenance (CM: 8x5 NBD) 8 Times/year\r\n- Remote/Email/Call unlimited\r\n- Exclude Hardware Spare part and Software License\r\n- Include Travel expenses and Accommodation (BKK Area)\r\n- Start date 1-Oct- 2024 to 30-Sep-2025', '2025-01-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 150000.00, 160500.00, 0.93, 1.00, 149999.07, 100.00, 150000.00, 0.93, 149999.07, 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', '2025-01-06 09:51:28', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('161e830e-355e-4364-acce-405857cf30b9', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central ลาดพร้าว (ครั้งที่ 8)', '2025-10-22', '2025-10-28', 'ชนะ (Win)', 'QT000001254', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central ลาดพร้าว (ครั้งที่ 8)', '2025-10-21', '3', '1', 67800.00, 72546.00, 51520.00, 55126.40, 16280.00, 24.01, 67800.00, 51520.00, 16280.00, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-04-09 06:20:27', '3', '2025-10-27 06:30:53', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('1890f745-aaf4-4928-acba-032632272c77', 'LPQMS กรมปศุสัตว์', '2025-04-29', '2025-07-31', 'ชนะ (Win)', '', 'HPE Server, HP PC, SQL Server', '2025-04-29', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 3560800.00, 3810056.00, 2670811.75, 2857768.57, 889988.25, 24.99, 3560800.00, 2670811.75, 889988.25, '642afc1e-c8d5-42f3-a685-aa899e78be1e', '2025-06-10 08:46:35', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('1a7bef7e-b9ee-45ff-998f-aaa4cca9f84a', 'งานปรับปรุงชายหาด จอมเทียน บ.ไดนามิค ค่าของ', '2024-10-16', '2025-01-16', 'ชนะ (Win)', '', '', '2024-10-08', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 590069.16, 631374.00, 472000.00, 505040.00, 118069.16, 20.01, 590069.16, 472000.00, 118069.16, NULL, '2025-06-11 12:20:52', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('1b1ac888-9150-4286-918e-398b93277578', 'ระบบอ่านป้ายทะเบียน  PSV เทศบาลนครนครยะลา', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-06-11', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 1040000.00, 1112800.00, 689600.00, 737872.00, 0.00, 0.00, 104000.00, 68960.00, 35040.00, '075ad214-ce0d-495d-b27d-d4a4fdb9e083', '2025-06-12 10:57:20', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('1c9c103d-de4b-4542-8ba0-c017cd06e23b', 'สถานีตรวจวัดก๊าซแบบออนไลน์ รุ่น MUI-Station 1.3', '0000-00-00', '0000-00-00', 'แพ้ (Loss)', '', '', '2024-11-27', '3', '1', 934000.00, 999380.00, 707289.72, 756800.00, 226710.28, 24.27, 0.00, 0.00, 0.00, '5aa126c7-c78d-4234-b0f3-45153034626e', '2024-12-02 13:52:52', '3', '2025-10-25 15:08:04', '3', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('1cf8da79-24cc-4f87-ad15-bae140ab4e55', 'GFCA Project Wireless Controller and Access Point MA 1Y6M_8x5NBD_พัฒนาการ48', '2025-03-17', '2026-08-31', 'ชนะ (Win)', '', '', '2025-02-28', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 75000.00, 80250.00, 59880.00, 64071.60, 15120.00, 20.16, 75000.00, 59880.00, 15120.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-02-28 08:46:37', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('1e3fdaa1-9f43-40d7-93b5-c3fc4b0a70a9', 'Toner (Project ทันใจ)', '2025-06-10', '2025-12-31', 'ยื่นประมูล (Bidding)', '', '', '2025-06-10', '3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 19000000.00, 20330000.00, 17000000.00, 18190000.00, 2000000.00, 10.00, 19000000.00, 17000000.00, 2000000.00, '6b3ba15b-ee6d-41ab-a543-d345e9f62259', '2025-06-10 07:10:26', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-10-11 08:00:04', '3140fdaf-5103-4423-bf87-11b7c1153416', '19747bf2-8f2d-47db-a2e8-4fca20843812', 7.00),
('20378a02-ba9b-414f-b1c0-0a297005a8b8', 'AI Handheal metal detector (เครื่องตรวจจับโลหะแบบมือถือ)', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', 'AI Handheal metal detector (เครื่องตรวจจับโลหะแบบมือถือ)', '2025-10-24', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '1', 280000.00, 299600.00, 124800.00, 133536.00, 0.00, 0.00, 28000.00, 12480.00, 15520.00, '3b652cc4-3afe-4caa-b092-fa8987489c78', '2025-10-27 06:44:38', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:44:38', NULL, 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('218430e9-c452-4019-9aab-dd4b180270c5', 'ติดตั้ง UPS 20KVA', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-01-20', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 640000.00, 684800.00, 572320.00, 612382.40, 67680.00, 10.57, 640000.00, 572320.00, 67680.00, '290b026b-4379-454b-a8df-5aa410a6bd21', '2025-06-12 10:45:57', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('21efec84-4bf1-47b2-8177-c0c36c973061', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central Westgate (ครั้งที่  10)', '2025-11-05', '2025-11-11', 'ชนะ (Win)', '', '', '2025-11-03', '3', '1', 50300.00, 53821.00, 22469.00, 24041.83, 27831.00, 55.33, 50300.00, 22469.00, 27831.00, NULL, '2025-11-05 05:47:27', '3', '2025-11-05 06:09:04', '3', 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('22b13d95-688f-4c84-8012-f08793f2103d', 'MA DLD NSW5', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 230000.00, 246100.00, 4160.00, 4451.20, 225840.00, 98.19, 230000.00, 4160.00, 225840.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:41:44', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('261d79bd-5ed3-4db6-91f5-a686205cd8d0', 'เคลมประกัน KTB สาขาทุ่งสง', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-014214', 'SN 0910424C มดเข้าเครื่อง Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 5000.00, 5350.00, 2776.00, 2970.32, 2224.00, 44.48, 5000.00, 2776.00, 2224.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:45:33', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('26b7618c-cba9-47bd-a7f5-026e193dd543', 'โครงการเพิ่มประสิทธิภาพระบบให้บริการสัญญาณภาพแบบ OnLine  เมืองพัทยา', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', 'เสนองานในนามคุณโอฬาร อบจ.ชลบุรี', '2025-02-03', '3', '1', 300000.00, 321000.00, 50000.00, 53500.00, 250000.00, 83.33, 300000.00, 50000.00, 250000.00, 'fb683856-9635-4316-ad3a-2eb57d6eb10f', '2025-04-09 05:57:36', '3', '2025-10-25 15:02:42', '3', '6e2ba9df-293d-4d88-b85e-4399e237d8c0', 7.00),
('26ca5b00-8508-413f-bab7-4590a960c6e6', 'โครงการจ้างเหมาติดตั้งระบบป้องกันอัคคีภัย 16 โรงเรียน', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', 'รอประกาศยื่นพร้อมกับ 48 รร.', '2025-05-30', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 1120000.00, 1198400.00, 800000.00, 856000.00, 0.00, 0.00, 112000.00, 80000.00, 32000.00, '466cca72-833b-4631-80f5-1cafdf402375', '2025-06-12 11:15:40', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('26ffb5a6-2d63-464a-a9db-3e976c2d3893', 'GFCA MA Service Onsite Support 2025 5x8NBD_1Y_END 31Dec2025', '2025-01-01', '2025-12-30', 'ชนะ (Win)', '', '', '2025-03-19', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 136500.00, 146055.00, 0.93, 1.00, 136499.07, 100.00, 136500.00, 0.93, 136499.07, '054160f3-f50e-40a2-a45e-569777875172', '2025-03-19 09:37:47', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('271444d8-9d28-4689-b5d9-c32aedac1024', 'โครงการปรับปรุงระบบกล้องโทรทัศน์วงจรปิดภายในเขตเทศบาลเมืองหนองปรือ (ระยะที่ 1 )', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '2024-07-11', '70dd36b5-f587-4aa9-b544-c69542616d34', '2', 6146411.21, 6576660.00, 4130844.50, 4420003.62, 2015566.71, 32.79, 3073205.60, 2065422.25, 1007783.35, '641761a3-129e-4d38-ba11-2c4c9bb44d3f', '2025-06-11 10:21:07', '70dd36b5-f587-4aa9-b544-c69542616d34', '2025-10-11 08:00:04', '70dd36b5-f587-4aa9-b544-c69542616d34', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('2754a8f7-1129-4009-855f-a338b6ab58de', 'BSP Daido_Wireless Access Point setup 20 Units', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', 'Daido Wireless Access Point setup 20 Units', '2025-07-18', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 86000.00, 92020.00, 1.00, 1.07, 85999.00, 100.00, 86000.00, 1.00, 85999.00, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-07-21 08:53:54', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('2820d05c-93f5-441e-9e24-1ec1930d2345', 'BSP Daido Core and Access Switch VLAN Configuration', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-10-07', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 10000.00, 10700.00, 0.00, 0.00, 10000.00, 100.00, 10000.00, 0.00, 10000.00, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-10-07 08:20:59', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('284033fa-5e82-48be-a26e-60f91dd0b65f', 'WA SCB Signature Pad 3,000u (ปีที่ 1/5)', '2025-01-01', '2025-12-31', 'ชนะ (Win)', 'PO.674111068116 Date 28/08/2024', 'เริ่ม 01/12/2024 - 30/11/2029', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 600000.00, 642000.00, 10000.00, 10700.00, 590000.00, 98.33, 600000.00, 10000.00, 590000.00, '0f80acd4-d034-4175-b501-f879a9e203de', '2025-01-30 07:02:30', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', 'aa203517-e140-4abc-9fa8-0e9926365967', 7.00),
('29556301-fab6-4d8c-aa62-1b1fbf984168', 'โครงการพัฒนาศักยภาพด้านความปลอดภัยบริเวณพื้นที่เสี่ยงภัย ในเส้นทางสายเลียบหาดป่าตอง ระยะที่ 3', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '0000-00-00', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 13205607.48, 14130000.00, 10000000.00, 10700000.00, 3205607.48, 24.27, 6602803.74, 5000000.00, 1602803.74, 'ad0ee715-c3b7-4df0-b097-6d0bf1565fb0', '2025-06-11 09:04:36', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('29dbf3e9-e0c5-4f32-aa07-9c9460094322', 'ขาย Dell Micro 7020', '2025-06-23', '2027-06-22', 'ชนะ (Win)', 'PO25-012518', 'ส่งสินค้า วันที่ 23/06/2025', '2025-06-20', '1b9c09d2-dc91-4b5e-a62b-8c42a41958ab', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 79500.00, 85065.00, 67866.00, 72616.62, 11634.00, 14.63, 79500.00, 67866.00, 11634.00, '74f091d7-a81e-426b-a55e-a50eeb43d8e7', '2025-06-23 02:50:18', '1b9c09d2-dc91-4b5e-a62b-8c42a41958ab', '2025-10-11 08:00:04', '1b9c09d2-dc91-4b5e-a62b-8c42a41958ab', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('2a710db7-53b6-4cb3-8688-065da1044185', 'CCTV', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-04-09', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 1440807.48, 1541664.00, 1315648.00, 1407743.36, 125159.48, 8.69, 1440807.48, 1315648.00, 125159.48, '9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', '2025-06-12 10:37:49', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('2adbf9e1-d04a-4c49-aebe-cd5e22439502', 'จ้างพัฒนาระบบสารสนเทศสำหรับบริหารจัดการโรงพยาบาลเมืองพัทยา (HIS)', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '2025-05-13', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 14018691.59, 15000000.00, 11000000.00, 11770000.00, 3018691.59, 21.53, 7009345.79, 5500000.00, 1509345.79, NULL, '2025-06-11 12:26:21', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('2bcbf6c0-4d54-4110-925f-405de802197c', 'เคลมประกัน KTB สาขาสัตหีบ', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-013677', 'SN 0882324C มดเข้าเครื่อง Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 5000.00, 5350.00, 1510.00, 1615.70, 3490.00, 69.80, 5000.00, 1510.00, 3490.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:14:27', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('2c2f0090-5f59-46be-a426-e426fde826df', 'MA DLD Regislive', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 500000.00, 535000.00, 89450.00, 95711.50, 410550.00, 82.11, 500000.00, 89450.00, 410550.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:42:55', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('2cfe725d-8bfa-4d78-a196-a30881a8eb22', 'เช่าใช้ชุดเฝ้าระวังเหตุฉุกเฉินการล้มในผู้ที่มีภาวะพึ่งพิงในบ้านและภายนอกบ้านพร้อมระบบแพลตฟอร์และงานบริการ ระยะเวลา 11 เดือน', '2025-02-03', '2025-12-31', 'ชนะ (Win)', '70/2568', '', '2025-01-01', '3', '1', 266822.43, 285500.00, 201192.50, 215275.98, 65629.93, 24.60, 266822.43, 201192.50, 65629.93, '5aa126c7-c78d-4234-b0f3-45153034626e', '2025-04-09 05:45:25', '3', '2025-10-11 08:00:04', '3', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('2de608f9-70bf-49c3-884a-7b663f145cd1', 'โครงการบูรณาการระบบเฝ้าระวังความปลอดภัยใน ปปส.ด้วยเทคโนโลยี AI', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2025-06-05', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2', 26621000.00, 28484470.00, 17570000.00, 18799900.00, 9051000.00, 34.00, 0.00, 0.00, 0.00, '3761198e-e426-49b5-9dc5-a5efd3b13a33', '2025-06-12 10:00:03', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2025-10-11 08:00:04', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('2f4fe9d4-a106-4354-9cec-8085d4723832', 'เคลมประกัน KTB สาขาเทสโก้โลตัส ระยอง', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-03871', 'SN 0909024C ลูกค้าเอากาแฟ เทใส่ช่องปรับสมุด  Mainboard ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 48000.00, 51360.00, 4446.00, 4757.22, 43554.00, 90.74, 48000.00, 4446.00, 43554.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:31:11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('2f8a36c8-5c3b-4453-96c5-07e1b594b246', 'ค่าบำรุงรักษาเครื่อง AUP EGAT จำนวน 5 ชุด Lot 2 (ปีที่ 4/5)', '2025-01-01', '2025-12-31', 'ชนะ (Win)', '5/2563 ลงวันที่ 25 พ.ย. 2563', 'MA AUP 5U (5Y)(25/01/2022 - 24/01/2026)\r\nIV631217001', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 9000.00, 9630.00, 5000.00, 5350.00, 4000.00, 44.44, 9000.00, 5000.00, 4000.00, '4baf1507-337e-43f7-8d21-fbc184d876ac', '2025-06-16 06:44:26', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('328cb493-4f0b-4b1f-ba09-44a443e8e752', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central Rama II (ครั้งที่ 7)', '2025-10-09', '2025-10-15', 'ชนะ (Win)', '', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central Rama II (ครั้งที่ 7)', '2025-10-08', '3', '1', 55300.00, 59171.00, 21165.00, 22646.55, 34135.00, 61.73, 55300.00, 21165.00, 34135.00, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-10-27 06:29:19', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:29:19', NULL, 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('341cad3e-8df3-48a6-8ffd-4a81b0720737', 'ติดตั้งระบบไม้กั้น', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-02-18', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 121967.29, 130505.00, 109150.00, 116790.50, 12817.29, 10.51, 121967.29, 109150.00, 12817.29, '67bda6e1-da1b-41c2-8658-f11662f15f6c', '2025-06-12 10:33:55', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('344231d5-c5d1-43e2-80ed-9b730f4b2782', 'งานซ่อมบำรุงเครื่องคอมพิวเตอร์ โรงเรียนเมืองพัทยา 4', '0000-00-00', '2025-07-31', 'ชนะ (Win)', '', '', '2025-05-13', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 442000.00, 472940.00, 200000.00, 214000.00, 242000.00, 54.75, 442000.00, 200000.00, 242000.00, NULL, '2025-06-11 10:45:14', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('356dab8b-1ff3-4960-9dca-f5cffe58ccd4', 'เคลมประกัน KTB สาขาปากเกร็ด', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-014226', 'SN 0892124C น้ำเข้าเครื่อง Mainboard และ Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 63000.00, 67410.00, 4330.00, 4633.10, 58670.00, 93.13, 63000.00, 4330.00, 58670.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:46:50', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('36f9bfe8-bf92-42cb-82d0-d3080529cc6a', 'Innovation Service  Management Teams (งาน และการบริหารจัดการระบบงานภายในทีม)', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-01-01', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '1', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2025-10-27 11:31:16', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 11:31:16', NULL, 'df374787-e96c-4d3c-8089-3867edd96cf4', 7.00),
('38c30373-b998-4c91-a0bf-7c3c538b5f07', 'Toyota Buzz Onsite service Troubleshoot stack cisco 9300x Core Switch', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', 'บริษัท ซีนิกซ์เทคโนโลยี (ประเทศไทย) จำกัด\r\nK.Suchart Buddhaunchalee\r\nsuchartb@iottechgroup.com', '2025-06-18', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 12000.00, 12840.00, 0.00, 0.00, 12000.00, 100.00, 12000.00, 0.00, 12000.00, '895e71fc-991e-4b42-9803-4bcafdb03023', '2025-06-18 09:06:55', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('38fae358-df4d-41f2-8970-cb2937222dd5', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central Pack (ครั้งที่ 9)', '2025-10-24', '2025-10-31', 'ชนะ (Win)', '', '', '2025-10-24', '3', '1', 67800.00, 72546.00, 25000.00, 26750.00, 42800.00, 63.13, 67800.00, 25000.00, 42800.00, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-10-27 02:09:39', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:31:38', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('3b2460f5-5fec-4dcd-8f3b-eed28da77728', 'งานติดตั้งระบบ Access Control', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-02-18', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 90625.23, 96969.00, 77390.00, 82807.30, 13235.23, 14.60, 90625.23, 77390.00, 13235.23, '67bda6e1-da1b-41c2-8658-f11662f15f6c', '2025-06-12 10:29:07', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('3b284924-c841-4181-b8db-8827823db7d2', 'เคลมประกัน KTB สาขาศูนย์ราชการจังหวัดชัยภูมิ', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-013670', 'SN 0940724D มดเข้าเครื่อง Mainboard และ Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 63000.00, 67410.00, 5090.00, 5446.30, 57910.00, 91.92, 63000.00, 5090.00, 57910.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:13:10', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('3b418fca-65f3-471b-b61d-ba338a9aa36e', 'ระบบบริหารจัดการอุปกรณ์เทคโนโลยีสารสนเทศ', '0000-00-00', '0000-00-00', 'ยกเลิก (Cancled)', '', '', '2024-09-11', '3', '1', 2609906.54, 2792600.00, 1401869.16, 1500000.00, 1208037.38, 46.29, 0.00, 0.00, 0.00, 'a485226f-e787-44e7-a140-4bf50433c525', '2024-12-02 13:31:24', '3', '2025-10-25 15:10:30', '3', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('3bde2447-a24c-4b11-933c-5a5160e902f3', 'จ้างปรับปรุงและเพิ่มประสิทธิภาพระบบสายนำสัญญาณใยแก้วนำแสงแบบฝั่งใต้ดิน ระยะที่6', '2024-05-31', '2024-11-26', 'ชนะ (Win)', '256/2567', '', '2024-05-30', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 32383177.57, 34650000.00, 16562000.00, 17721340.00, 15821177.57, 48.86, 32383177.57, 16562000.00, 15821177.57, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 03:03:57', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2025-10-27 05:23:24', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('3cef16ff-6bf2-4f07-8d0c-cdc2fb0bb4f9', 'โครงการจ้างเหมาบริการดูแลซอฟต์แวร์ขององค์การบริหารส่วนจังหวัดชลบุรี  ประจำปีงบประมาณ 2568', '2025-04-29', '2025-06-28', 'ชนะ (Win)', '', '', '0000-00-00', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 2471028.04, 2644000.00, 2010000.00, 2150700.00, 461028.04, 18.66, 2471028.04, 2010000.00, 461028.04, '8a441dab-7f49-4ff6-bb6d-327003829c1f', '2025-06-11 08:52:47', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-27 05:40:24', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'e021eb8c-6bd5-49a7-a652-8f0bdc860a17', 7.00),
('3e4448e8-33e2-4f49-873c-773fd4a7aacd', 'เคลมประกัน KTB สาขาคลองใหญ่', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-013681', 'SN 0929124D มดเข้าเครื่อง Mainboard และ Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 63000.00, 67410.00, 7565.00, 8094.55, 55435.00, 87.99, 63000.00, 7565.00, 55435.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:16:52', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('40fc9979-20b4-46c5-9091-19b3db46533c', 'งานติดตั้งกล้องโทรทัศน์วงจรปิดเพื่อรักษาความปลอดภัย บริเวณ หมู่ที่ 5', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '0000-00-00', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 242056.07, 259000.00, 192000.00, 205440.00, 50056.07, 20.68, 121028.04, 96000.00, 25028.04, NULL, '2025-06-11 11:56:31', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('41b24403-2ab4-48f2-b972-356190dcfc16', 'โครงการติดตั้งระบบเสียงตามสายภายในชุมชนพร้อมปรับปรุงอุปกรณ์ป้องกันฟ้าผ่า', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2024-12-03', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2', 6803738.32, 7280000.00, 5674000.00, 6071180.00, 1129738.32, 16.60, 0.00, 0.00, 0.00, '5db776f7-0e5f-42f2-a0de-2f76ffadf235', '2025-06-12 09:42:56', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2025-10-11 08:00:04', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('430859b5-6511-4579-8c1e-b1d4b9179cd1', 'เคลมประกัน KTB สาขารามอินทรา กม.2', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-013740', 'SN 0901524C มดเข้าเครื่อง Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 5000.00, 5350.00, 1510.00, 1615.70, 3490.00, 69.80, 5000.00, 1510.00, 3490.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:18:57', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('43ca8d52-9f8f-4b7f-b676-998928a0145d', 'จ้างติดตั้งระบบกล้องโทรทัศน์วงจรปิด', '2024-10-18', '2024-12-19', 'ชนะ (Win)', '', '', '2024-10-02', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '28534929-e527-4755-bd37-0acdd51b7b45', 4665000.00, 4991550.00, 309810.27, 331496.99, 4355189.73, 93.36, 4665000.00, 309810.27, 4355189.73, '8b315bda-7e61-4d0d-a995-3653ddda3140', '2024-12-03 01:12:40', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2025-10-11 08:00:04', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('44345865-d82c-4627-b337-c2565c5a3bb4', 'e-Tracking กรมปศุสัตว์', '2025-06-05', '2025-12-30', 'ชนะ (Win)', '', 'Lenovo Server, Zyxel Switch, Windows Server 2025', '2025-05-15', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 298000.00, 318860.00, 202430.00, 216600.10, 95570.00, 32.07, 298000.00, 202430.00, 95570.00, '642afc1e-c8d5-42f3-a685-aa899e78be1e', '2025-06-10 09:02:43', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('44ad0f39-8211-487d-a9f4-f8e8b68e1e89', 'โครงการจัดซื้อป้ายประชาสัมพันธ์อิเล็กทรอนิกส์ (LED) สำหรับติดตั้งภายนอกอาคาร (LED Full Display Outdoor P8) จุดที่ 1 (บริเวณสี่แยกสถานีตำรวจแหลมฉบัง) เทศบาลนครแหลมฉบัง', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '2024-06-07', '70dd36b5-f587-4aa9-b544-c69542616d34', '2', 2877600.00, 3079032.00, 2289719.63, 2450000.00, 587880.37, 20.43, 1438800.00, 1144859.81, 293940.19, NULL, '2025-06-11 10:41:13', '70dd36b5-f587-4aa9-b544-c69542616d34', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('456192c1-3257-4f9e-8a5d-d34e621f9182', 'เคลมประกัน KTB สาขาตลาดย่านยาว', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-014498', 'SN 0941224D มดเข้าเครื่อง Mainboard และ Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 63000.00, 67410.00, 5970.00, 6387.90, 57030.00, 90.52, 63000.00, 5970.00, 57030.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:57:59', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('458719dc-d89b-48f2-90e0-5bdbf89e458a', 'Website กรมปศุสัตว์', '2025-06-20', '2025-09-07', 'ชนะ (Win)', '', '', '2025-06-16', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 253719.63, 271480.00, 85636.92, 91631.50, 168082.71, 66.25, 253719.63, 85636.92, 168082.71, NULL, '2025-06-15 17:59:11', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-10-11 08:00:04', NULL, '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('464369d4-5720-4b09-9834-8e46884ab187', 'งานติดตั้งสายสัญญาณคอมพิวเตอร์ และย้ายห้องควบคุมระบบคอมพิวเตอร์ (GEN)', '2024-11-08', '2025-01-22', 'ชนะ (Win)', '18/2568', '', '2024-10-25', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 1860000.00, 1990200.00, 1335834.00, 1429342.38, 524166.00, 28.18, 1860000.00, 1335834.00, 524166.00, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2024-11-04 03:49:25', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2025-10-27 05:32:15', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('4677b262-6fc0-4bc7-8708-a0806b091577', 'จัดซื้อจอภาพและระบบการแสดงผลภาพมัลติมีเดียแบบเชื่อมต่อกัน (Video Wall Display)', '2024-05-09', '2024-10-06', 'ชนะ (Win)', '234/2567', '', '2024-04-09', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 3128037.38, 3347000.00, 2100000.00, 2247000.00, 1028037.38, 32.87, 3128037.38, 2100000.00, 1028037.38, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 03:00:40', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2025-10-27 05:28:52', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('4821cea3-07a4-4495-a139-8e8d74e26254', 'OBEC Firewall Gateway', '2024-12-02', '2025-02-02', 'ชนะ (Win)', '', '', '2024-10-21', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 500000.00, 535000.00, 0.00, 0.00, 500000.00, 100.00, 500000.00, 0.00, 500000.00, 'cea804cd-55ab-4a3f-b9ff-a942547402a7', '2024-10-31 22:26:58', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('49100e5b-82a9-4a11-849b-17e45117adba', 'Microsoft 365 Renewal Yearly', '2025-01-02', '2026-01-01', 'ชนะ (Win)', '', '', '2025-01-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 38700.00, 41409.00, 30130.00, 32239.10, 8570.00, 22.14, 38700.00, 30130.00, 8570.00, '88bc1a3c-f646-4e7a-863d-3424b0fbe1c1', '2025-01-06 06:23:09', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '2', 'e021eb8c-6bd5-49a7-a652-8f0bdc860a17', 7.00),
('49b9dd79-d94d-45c9-8645-cf4caaab398a', 'จ้างเหมาติดตั้งสิทธิ์การใช้งานโปรแกรมป้องกันไวรัสคอมพิวเตอร์', '2024-10-05', '2024-10-19', 'ชนะ (Win)', '34/2568', '', '2024-10-04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 732710.28, 784000.00, 618412.00, 661700.84, 114298.28, 15.60, 732710.28, 618412.00, 114298.28, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 02:47:33', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2025-10-27 05:21:57', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('4a9d25d8-dce9-40ab-b8dd-8898d87a00fb', 'GFCA MA Device 2025 1Y_HQ-Site_END_31-Aug-2026', '2025-08-30', '2026-08-31', 'ชนะ (Win)', '', '', '2025-07-21', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 469200.00, 502044.00, 363640.00, 389094.80, 105560.00, 22.50, 469200.00, 363640.00, 105560.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-08-01 06:06:46', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('4ab1ec63-fe78-4c3f-b039-1870bd5ad987', 'งานติดตั้งสายสัญญาณคอมพิวเตอร์ และย้ายห้องควบคุมระบบคอมพิวเตอร์ (ปรับปรุงห้อง control)', '2025-01-13', '2025-03-14', 'ชนะ (Win)', '85/2568', '', '2024-10-25', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 3568000.00, 3817760.00, 3754861.60, 4017701.91, -186861.60, -5.24, 3568000.00, 3754861.60, -186861.60, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2024-11-06 05:07:24', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2025-10-27 05:32:40', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 'งานจ้างบำรุงรักษาระบบ MOBILE FACE RECOGNITION', '2024-06-01', '2025-08-03', 'ชนะ (Win)', 'A02/3160030757/2567', '', '2024-06-01', '3', '1', 1073708.41, 1148868.00, 791114.96, 846493.01, 282593.45, 26.32, 1073708.41, 791114.96, 282593.45, NULL, '2024-10-15 21:59:34', '3', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('4b2ed514-e023-4113-acb6-9f64c0137727', 'Evergreen Fortitoken License ( SmartBiz)', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', 'Sales Yaniza', '2025-05-20', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 15000.00, 16050.00, 1.00, 1.07, 14999.00, 99.99, 15000.00, 1.00, 14999.00, '2b5c101f-db79-4143-89f9-2b42fbea06bd', '2025-05-20 06:13:35', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('4b7ab0ca-b747-482a-a113-03a5891f9aab', 'BSP Hayashi Telempu Project Replacement Veeam Server', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-03-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 42000.00, 44940.00, 1.00, 1.07, 41999.00, 100.00, 42000.00, 1.00, 41999.00, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-03-06 04:39:00', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('4b86e1b4-e921-4ca4-809b-28fc4ef63b07', 'ค่าชุดอุปกรณ์และระบบแพลตฟอร์มวิเคราะห์ข้อมูลและปัญญาประติษฐ์ในบริการการดูแลการใช้ชีวิตและ ดูแลสุขภาพระยะยาวสำหรับผู้สูงอายุ', '2025-07-01', '2025-09-30', 'ชนะ (Win)', '', '', '2025-01-01', '3', '1', 341330.00, 365223.10, 244294.00, 261394.58, 97036.00, 28.43, 341330.00, 244294.00, 97036.00, 'cc80c251-336b-4039-9850-5a042948e8f3', '2025-04-09 05:32:18', '3', '2025-10-11 08:00:04', '3', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('4c4bc3e0-f462-4c9a-b626-8999a69acb72', 'SC Polymer Solar - PO-2024081900001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024081900001', '', '2024-08-19', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 11040.00, 11812.80, 10080.00, 10785.60, 960.00, 8.70, 11040.00, 10080.00, 960.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 09:44:40', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('4cafdcd9-a163-4b74-847b-9d878ececab9', 'Visitor Management -Interface Access Control (ปปส.)', '2025-06-13', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', '', '2025-06-13', '3', '1', 800000.00, 856000.00, 500000.00, 535000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'ccfcd9ae-df30-40e5-98a9-71bb73d4f491', '2025-06-15 06:19:32', '3', '2025-10-11 08:00:04', '3', '6e2ba9df-293d-4d88-b85e-4399e237d8c0', 7.00),
('4ce93953-2518-44e8-9380-e55008c39155', 'GFCA MA IBM Server and Storage_AAI_Site_2Years_END30Apr2027', '2025-05-01', '2027-04-30', 'ชนะ (Win)', '', '', '2025-03-19', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 628700.00, 672709.00, 330400.00, 353528.00, 298300.00, 47.45, 628700.00, 330400.00, 298300.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-03-19 09:49:26', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00);
INSERT INTO `projects` (`project_id`, `project_name`, `start_date`, `end_date`, `status`, `contract_no`, `remark`, `sales_date`, `seller`, `team_id`, `sale_no_vat`, `sale_vat`, `cost_no_vat`, `cost_vat`, `gross_profit`, `potential`, `es_sale_no_vat`, `es_cost_no_vat`, `es_gp_no_vat`, `customer_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `product_id`, `vat`) VALUES
('4d537c51-ec3d-412b-8c7c-4f12cb07b45c', 'GFCA ASIATIC MA 2025 1Y_AAI-Site_END_31-Aug-2026', '2025-08-30', '2026-08-31', 'ชนะ (Win)', '', '', '2025-07-21', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 132300.00, 141561.00, 86590.00, 92651.30, 45710.00, 34.55, 132300.00, 86590.00, 45710.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-08-01 06:05:16', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('4d5ab32c-8838-4780-bde8-a974adc31874', 'งานกล้องน้ำท่วม เมืองพัทยา', '2025-01-09', '2025-06-08', 'ชนะ (Win)', '120/2568', '', '2024-11-13', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 1813084.11, 1940000.00, 1550000.00, 1658500.00, 263084.11, 14.51, 1813084.11, 1550000.00, 263084.11, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2025-06-11 12:32:03', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('4e831d8a-2f18-4d84-8d02-af10e4ed71ff', 'โครงการพัฒนาระบบ LAOS LIMs (LIS) และเชื่้อมต่อเครื่อง GeneXpert ประเทศลาว (MA 2026)', '2026-01-01', '2026-12-31', 'ชนะ (Win)', '', '', '2025-10-20', '3', '1', 100000.00, 107000.00, 0.00, 0.00, 100000.00, 100.00, 100000.00, 0.00, 100000.00, 'df1dbd74-1f88-4f78-80ee-60d99e1e7a15', '2025-10-20 09:36:05', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:10:20', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('4ebc5193-357d-4e62-a158-9c0d61ac3591', 'WTC MSRW 1116U (ปีที่ 2/5)', '2025-01-01', '2025-12-31', 'ชนะ (Win)', 'PO1067030040', 'MA Magnetic Stripe Reader / Writer LKE LKE4777U-N 1,116U (5Y)(03/04/2024 - 02/04/2029)\r\nService ดูแล แบบ Carry In แต่เครื่อง Spare Sale\r\nIV670405007', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 139240.00, 148986.80, 5000.00, 5350.00, 134240.00, 96.41, 139240.00, 5000.00, 134240.00, 'bcdad84b-ec95-4a80-8765-7f14d2c0a764', '2025-06-16 03:11:52', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('4f82e941-065d-41b9-9c23-b950c6e0f410', 'ค่าบำรุงรักษาเครื่อง AUP EGAT จำนวน 5 ชุด (ปีที่ 4/5)', '2025-01-01', '2025-12-31', 'ชนะ (Win)', '2/2564 ลงวันที่ 19 พ.ค. 2564', 'MA เครื่องปรับสมุดเงินฝากอัตโนมัติพร้อมเครื่องคอมพิวเตอร์ PC+OS (5Y) (01/07/2022 - 30/06/2026)', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 10500.00, 11235.00, 5000.00, 5350.00, 5500.00, 52.38, 10500.00, 5000.00, 5500.00, '4baf1507-337e-43f7-8d21-fbc184d876ac', '2025-06-16 06:47:40', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', NULL, '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('5060fb70-ed94-4b48-a1b0-b5911f0e9bc4', 'Service Domain Evergreen', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-01-07', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 17000.00, 18190.00, 12000.00, 12840.00, 5000.00, 29.41, 17000.00, 12000.00, 5000.00, '2f1ee3da-fe91-4f06-b0ae-62a206c7cd5d', '2025-06-12 11:05:41', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('50d43e8b-50cb-40d2-a3d9-d00573595588', 'งานปรับปรุงชายหาด จอมเทียน บ.ไดนามิค ค่าแรง', '2024-10-16', '2025-01-16', 'ชนะ (Win)', '', '', '2024-10-08', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 620349.53, 663774.00, 470000.00, 502900.00, 150349.53, 24.24, 620349.53, 470000.00, 150349.53, NULL, '2025-06-11 12:19:15', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('50e8b22c-fe04-4594-adff-d5a664f64c4b', 'เคลมประกัน KTB สาขาโคกสำโรง', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-014093', 'SN 0885024C น้ำเข้าเครื่อง Mainboard และ Power Supply ช๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 63000.00, 67410.00, 5235.00, 5601.45, 57765.00, 91.69, 63000.00, 5235.00, 57765.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:44:14', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('50e8b6f8-f35b-473f-ace3-278c2008f224', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central Eastville (ครั้งที่ 2)', '2025-04-28', '2025-05-04', 'ชนะ (Win)', '', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central Eastville (ครั้งที่ 2)', '2025-04-27', '3', '1', 55300.00, 59171.00, 25080.00, 26835.60, 30220.00, 54.65, 55300.00, 25080.00, 30220.00, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-10-27 06:22:06', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:22:06', NULL, 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('51e62f2e-3b91-44e8-9875-55239e0e8acc', 'ระบบวิเคราะห์เกรดสุกร Zoetis-CPF', '2025-07-01', '2025-12-31', 'ชนะ (Win)', 'PO2024100011', 'Zoetis เปิด PO Volume License  โดยใช้งบประมาณส่งเสริมการตลาด\n\n(สร้างโดย : พี่หญิง)', '2025-04-29', '3', '1', 800000.00, 856000.00, 720000.00, 770400.00, 80000.00, 10.00, 800000.00, 720000.00, 80000.00, '4ce6231a-57de-44f4-ab4e-817fec010315', '2025-06-16 00:25:04', '3', '2025-10-11 08:00:04', '5', '54b6a0a0-54c2-448c-a340-71d12acdc5f6', 7.00),
('525b742d-5749-40d2-a148-0290161fd3c3', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Auto Showcase ที่ Central Eastville)', '2025-05-07', '2025-05-13', 'ชนะ (Win)', 'QT0000001297', '', '2025-04-09', '3', '1', 93457.94, 100000.00, 46728.97, 50000.00, 46728.97, 50.00, 93457.94, 46728.97, 46728.97, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-04-09 12:25:18', '3', '2025-10-11 08:00:04', '3', 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('52d95985-84b0-4d61-8748-b1a76856536f', 'BSP Hayashi Telempu MA Service Onsite support 8x5 NBD (1Year) 2025', '2025-02-01', '2026-01-31', 'ชนะ (Win)', '', '', '2025-01-20', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 90000.00, 96300.00, 0.93, 1.00, 89999.07, 100.00, 90000.00, 0.93, 89999.07, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-01-20 04:17:13', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('5486e1ad-8bc0-4884-a100-c626c0a2d731', 'BSP NIDEC Network Device preventive maintenance and Asset Management', '0000-00-00', '0000-00-00', 'ยกเลิก (Cancled)', '', 'ยกเลิกโครงการ เนื่องจากเสนอ แยก Phase', '2025-02-28', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 142000.00, 151940.00, 4800.00, 5136.00, 137200.00, 96.62, 0.00, 0.00, 0.00, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-02-28 08:42:34', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('553290bb-4d0e-4820-9efd-c3eae3cb3d41', 'MA BioTech กรมปศุสัตว์', '2025-02-14', '2025-09-30', 'ชนะ (Win)', '', '', '2025-02-14', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 48700.00, 52109.00, 14100.00, 15087.00, 34600.00, 71.05, 48700.00, 14100.00, 34600.00, '642afc1e-c8d5-42f3-a685-aa899e78be1e', '2025-06-10 09:19:43', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('55f87cf2-2b69-40b3-a5fe-bef370e18e2d', 'IP Speaker', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-04-09', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 729580.37, 780651.00, 634160.00, 678551.20, 95420.37, 13.08, 729580.37, 634160.00, 95420.37, '9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', '2025-06-12 10:39:05', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('5663782b-954c-4f56-b20b-dd10800b4b15', 'ค่า Commissioning', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-04-09', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 200000.00, 214000.00, 165000.00, 176550.00, 35000.00, 17.50, 200000.00, 165000.00, 35000.00, '9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', '2025-06-12 10:42:38', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('57053bbe-382e-47fb-a5bc-2d80b4c67c9c', 'โครงการติดตั้งกล้องวงจรปิด (CCTV) พื้นที่เศรษฐกิจชุมชนเมืองใหม่จุดเชื่อมโยงเส้นทาง ส่วนสำคัญ ระยะที่ 1', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '2025-05-22', '70dd36b5-f587-4aa9-b544-c69542616d34', '2', 1401869.16, 1500000.00, 1186915.89, 1270000.00, 214953.27, 15.33, 700934.58, 593457.94, 107476.64, '2edcb350-5c44-4803-be82-0ce9b0015ac7', '2025-06-11 10:45:44', '70dd36b5-f587-4aa9-b544-c69542616d34', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('5713dfdc-147e-4618-83e4-6c4ed544d80c', 'SC Polymer Solar - PO-2024082000001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024082000001', '', '2024-08-20', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 334475.00, 357888.25, 307529.48, 329056.54, 26945.52, 8.06, 334475.00, 307529.48, 26945.52, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 09:51:01', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('578b6f6c-3165-4533-abe0-18bfc5bf4e91', 'จัดซื้อคอมพิวเตอร์งานเวชกรรมสังคม', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-07-16', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 96200.00, 102934.00, 83971.00, 89848.97, 12229.00, 12.71, 96200.00, 83971.00, 12229.00, 'f8a6cd53-4c8f-490d-83c8-85db6fb422bb', '2025-07-17 01:25:44', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('57cff5f7-e083-40ed-be05-323e55b0f12c', 'MA OBEC SUN', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 2099065.42, 2246000.00, 0.00, 0.00, 2099065.42, 100.00, 2099065.42, 0.00, 2099065.42, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:17:32', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('57efd456-ceaa-4471-aba4-ed62eea12b94', 'TV', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-06-26', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 24000.00, 25680.00, 16800.00, 17976.00, 0.00, 0.00, 2400.00, 1680.00, 720.00, '5f32551c-7a96-4b5f-b485-2357623e9893', '2025-06-27 02:20:17', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('581981be-b9fa-4c15-8091-48e893e2880f', 'เคลมประกัน KTB สาขานครพนม', '2567-07-27', '2572-07-27', 'ชนะ (Win)', '25-5-000-001926', 'SN 0951324E  มดเข้าเครื่อง Mainboard ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 48000.00, 51360.00, 6320.00, 6762.40, 41680.00, 86.83, 48000.00, 6320.00, 41680.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 03:16:33', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('5a85ff90-219a-4cc4-8d6a-30fd3153a864', 'บ้านบางแสน - SO2024-AC-08-08', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'SO2024-AC-08-08', '', '2024-08-07', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 33644.86, 36000.00, 23550.00, 25198.50, 10094.86, 30.00, 33644.86, 23550.00, 10094.86, '7f242c52-9e30-4791-97b2-053fb960423b', '2024-12-06 09:57:57', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('5a9e836c-bc06-4d16-9ba3-509ca6b53423', 'เคลมประกัน KTB สาขาพยุหะคีรี', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-013663', 'SN 0889124C มดเข้าเครื่อง Mainboard และ Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 63000.00, 67410.00, 5250.00, 5617.50, 57750.00, 91.67, 63000.00, 5250.00, 57750.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:11:36', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'พัฒนาระบบการขอใช้ยานพาหนะสำหรับหน่วยงาน สอน.และรพ.สต. (อบจ.ชลบุรี)', '2025-06-13', '0000-00-00', 'ชนะ (Win)', '', 'พัฒนาระบบใหม่แทนระบบเก่าที่ยังไม่สมบูรณ์ ทางลูกค้าจะทำเรื่องค่าใช้จ่ายกลับให้ในรูปแบบค่า MA', '2025-06-13', '3', '1', 420560.75, 450000.00, 50000.00, 53500.00, 370560.75, 88.11, 420560.75, 50000.00, 370560.75, 'fb683856-9635-4316-ad3a-2eb57d6eb10f', '2025-06-15 06:49:15', '3', '2025-10-25 14:57:40', '3', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('5e4b1eb5-1afd-4a26-9875-98fc7bb0805d', 'HP DL380', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-06-30', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 399000.00, 426930.00, 360000.00, 385200.00, 0.00, 0.00, 39900.00, 36000.00, 3900.00, '5f32551c-7a96-4b5f-b485-2357623e9893', '2025-06-30 14:13:48', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('5ee574f8-06dc-4c6f-8d61-7fb7c093d010', 'Service Domain evergreen.co.th and SSL Certificate Configuration for Evergreen.co.th', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', 'Service Domain evergreen.co.th and SSL Certificate Configuration for Evergreen.co.th\r\nSales ขาย Yaniza', '2025-01-28', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 0.00, 0.00, 12000.00, 12840.00, 0.00, 0.00, 0.00, 12000.00, -12000.00, '2b5c101f-db79-4143-89f9-2b42fbea06bd', '2025-01-28 06:12:18', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('5fd58030-0398-4401-a18c-4839e7cc0c2b', 'โครงการติดตั้งกล้องโทรทัศน์วงจรปิด (CCTV) เพื่อดูแลความปลอดภัยแนวชายหาดพัทยาและท่าเรือแหลมบาลีฮาย', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '2025-10-27', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 9345794.39, 10000000.00, 7600000.00, 8132000.00, 1745794.39, 18.68, 4672897.20, 3800000.00, 872897.20, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2025-06-11 11:46:32', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-27 12:35:23', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('61b7c176-6f76-4dc8-b764-384c493dddc7', 'Per-call Onsite GHB PSI สาขาสี่แยกบ้านแขก', '2025-08-05', '2025-08-05', 'ชนะ (Win)', 'QT-000001367', '', '2025-08-05', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 2500.00, 2675.00, 234.00, 250.38, 2266.00, 90.64, 2500.00, 234.00, 2266.00, '5db003f0-4196-451c-afda-e22e4481fefd', '2025-08-05 10:09:42', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', NULL, '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('63d6b111-d394-4ac7-a60c-1041a59872c0', 'SC Polymer Solar - PO-2024080800001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024080800001', '', '2024-08-08', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 211500.00, 226305.00, 193500.00, 207045.00, 18000.00, 8.51, 211500.00, 193500.00, 18000.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 07:38:34', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('64e25a53-27be-4e55-8dc8-8a6cdb3b8115', 'จัดซื้อระบบรักษาความปลอดภัยสำนักรักษาความปลอดภัย สำนักงานเลขาธิการสภาผู้แทนราษฎร ด้วยระบบวิเคราะห์ภาพด้วย AI (ระบบเฝ้าระวังอัจฉริยะด้วยปัญญาประดิษฐ์) จำนวน ๑ ระบบ', '0000-00-00', '0000-00-00', 'ยกเลิก (Cancled)', '', '', '2025-01-01', '3', '1', 33641000.00, 35995870.00, 30265607.50, 32384200.03, 3375392.50, 10.03, 0.00, 0.00, 0.00, 'a7398772-5d5f-4f09-9eb6-6edf32fb9893', '2025-04-09 05:25:35', '3', '2025-10-25 15:01:44', '3', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('650ab423-adbd-42ec-9baf-8ae7dc8e2d81', 'โครงการจ้างเหมาติดตั้งระบบป้องกันอัคคีภัยเพื่อความปลอดภัยของโรงเรียนในสังกัดกรุงเทพมหานคร 48 รร.', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', 'รอประกาศยื่น', '2025-05-29', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 5323579.44, 5696230.00, 4286747.00, 4586819.29, 0.00, 0.00, 532357.94, 428674.70, 103683.24, '466cca72-833b-4631-80f5-1cafdf402375', '2025-06-12 11:13:09', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('65c8ab6b-d4c6-4a32-8029-eb189f32b991', 'จ้างเหมาเช่าอุปกรณ์และระบบแพลตฟอร์มเฝ้าระวังเหตุฉุกเฉินการล้มในผู้สูงอายุภายในบ้านและภายนอกบ้าน (Emergency Monitoring)', '2025-11-17', '2026-09-30', 'ชนะ (Win)', '', 'จ้างเหมาเช่าอุปกรณ์และระบบแพลตฟอร์มเฝ้าระวังเหตุฉุกเฉินการล้มในผู้สูงอายุภายในบ้านและภายนอกบ้าน (Emergency Monitoring)', '2025-10-27', '3', '1', 628500.00, 672495.00, 484929.00, 518874.03, 143571.00, 22.84, 628500.00, 484929.00, 143571.00, '32104ee7-4b28-400b-bb7b-1ab55e1cf19d', '2025-10-27 06:48:01', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:48:01', NULL, 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('66c0508f-b34e-4007-938b-2a1dc2f7e297', 'Carcass Grading AI Automation-ศรีษะเกษ', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', 'AIBGRADE002 - Carcass Grading AI Automation\r\n1 x Industrial Grade Camera 4K\r\n1 x Edge AI GPU, 32GB DDR4,1TB SSD\r\n1x POE Switch 8 Ports\r\n1x LED Light , IOT Control Board\r\n1x Industrial Panel 15&amp;amp;amp;quot; with Housing\r\n1x UPS 1KVA (600W)\r\n1x Food Grade Stainless Cart with Power Plug\r\n1 x Accessories Lan 100 Metres\r\n(ราคารวมอุปกรณ์ และค่าติดตัง พร้อมการรับประกัน 1 ป Remote Service Support 24x7 with\r\nHotline Call)\r\n\r\n(สร้างโดย : พี่หญิง)', '2025-05-23', '3', '1', 424250.00, 453947.50, 300000.00, 321000.00, 124250.00, 29.29, 42425.00, 30000.00, 12425.00, 'c120a5b5-375a-411b-87d4-5fa61e6453d9', '2025-06-16 01:04:18', '3', '2025-10-11 08:00:04', '5', '54b6a0a0-54c2-448c-a340-71d12acdc5f6', 7.00),
('66ff5aa2-e79e-4531-a30d-494079448439', 'โครงการงานก่อสร้างถังเก็บน้ำใส ขนาด 20,000 ลบ.ม.ที่สถานีสูบน้ำสําโรง ค่าของ', '2024-03-23', '2024-11-29', 'ชนะ (Win)', '', '', '2024-02-13', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 1386137.38, 1483167.00, 1020000.00, 1091400.00, 366137.38, 26.41, 1386137.38, 1020000.00, 366137.38, NULL, '2025-06-11 12:11:55', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('69b3d4b2-f00b-4d75-9379-f17aaf4c2e34', 'โครงการสวนสัตว์อัจฉริยะเพื่อเศรษฐกิจ การศึกษา และการอนุรักษ์ ขับเคลื่อนระบบนิเวศสู่ความยั่งยืนด้วยเทคโนโลยีดิจิทัล (Thailand ZooNova : A New Era of Smart Zoo - Economic Education and Conservation and Sustainability Ecosystem', '0000-00-00', '0000-00-00', 'ยกเลิก (Cancled)', '', 'โครงการนี้นำเสนอให้กับ องค์การสวนสัตว์แห่งประเทศไทย ในพระบรมราชูปถัมภ์ 327 ถ. สุโขทัย แขวงดุสิต เขตดุสิต กรุงเทพมหานคร 10300', '2025-01-01', '3', '1', 11214953.27, 12000000.00, 10000000.00, 10700000.00, 1214953.27, 10.83, 0.00, 0.00, 0.00, '6e23608d-46bb-4e74-8326-21365397565b', '2025-04-09 06:39:31', '3', '2025-10-25 14:29:16', '3', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('6a50ca74-9f27-483a-a312-126660ddcfc7', '3in1 Pinpad', '2024-08-01', '2024-10-31', 'ชนะ (Win)', '', '', '2024-08-01', '3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 14579439.25, 15600000.00, 11014813.08, 11785850.00, 3564626.17, 24.45, 14579439.25, 11014813.08, 3564626.17, 'f5489b6a-fd5b-4896-b655-761768e44b8f', '2024-11-11 08:57:47', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-10-11 08:00:04', NULL, 'abf31336-8385-4be6-9a6c-587719a5e0df', 7.00),
('6ad9b333-0acc-410b-b5b0-7c6c9497d9be', 'โครงการจัดทำระบบเครือข่ายเสมือน (Server Consolidation and Virtualization) VPN+IP Phone Ph2', '2025-09-02', '2026-03-01', 'ชนะ (Win)', '151/2568', '', '2025-03-11', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 11585046.73, 12396000.00, 10224600.00, 10940322.00, 1360446.73, 11.74, 11585046.73, 10224600.00, 1360446.73, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2024-11-27 08:16:07', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-11 08:00:04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('6cf12295-6b67-466c-b10c-12c12d0ac031', 'จัดซื้อ Notebook', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-06-09', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 36000.00, 38520.00, 30940.00, 33105.80, 5060.00, 14.06, 36000.00, 30940.00, 5060.00, '9610604a-2d45-4b3f-9c93-70791dc4f0ad', '2025-06-12 10:26:04', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('6d273c81-2bb0-4628-8954-b10bcccbfdd1', 'เคลมประกัน KTB สาขาตลาดวังน้อย', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-014617', 'SN 0883624C น้ำเข้าเครื่อง Mainboard ช๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 48000.00, 51360.00, 3446.00, 3687.22, 44554.00, 92.82, 48000.00, 3446.00, 44554.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:59:46', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('6f498e8e-0bc7-45e0-9f5b-117dbdc84c90', 'เคลมประกัน KTB สาขาวิเศษชัยชาญ', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-014084', 'SN 0956824E มดเข้าเครื่อง Mainboard และ Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 63000.00, 67410.00, 5506.00, 5891.42, 57494.00, 91.26, 63000.00, 5506.00, 57494.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:43:02', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('6f49e7bd-2886-4448-8c2c-a34b78d05b7e', 'โครงการเพิ่มประสิทธิภาพระบบกล้องโทรทัศน์วงจรปิดในจุดเสี่ยงภัยของเมืองพัทยา', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '2025-01-13', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 9626168.22, 10300000.00, 7800000.00, 8346000.00, 1826168.22, 18.97, 4813084.11, 3900000.00, 913084.11, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-27 08:22:07', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-11 08:00:04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('6f882833-ac9e-4367-8468-ebb05dd81a8e', 'Mindss Thai-Otsuka VMware Server Deployment', '0000-00-00', '0000-00-00', 'ยกเลิก (Cancled)', '', 'เปลี่ยนเป็น Promote AD to AWN Cloud แทน', '2025-01-21', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 20000.00, 21400.00, 0.93, 1.00, 19999.07, 100.00, 0.00, 0.00, 0.00, 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', '2025-01-21 09:30:36', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('6fde9224-b19d-43b7-9eb0-ac741b8fe057', 'Switch', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-06-26', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 28540.00, 30537.80, 26420.00, 28269.40, 0.00, 0.00, 2854.00, 2642.00, 212.00, '5f32551c-7a96-4b5f-b485-2357623e9893', '2025-06-27 02:21:01', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('71bd06ff-3e69-4fb3-a42f-d6faee810642', 'โครงการจัดซื้อคอมพิวเตอร์', '2025-05-02', '2026-05-02', 'ชนะ (Win)', '', '', '2025-05-05', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2', 99866.36, 106857.00, 92900.00, 99403.00, 6966.36, 6.98, 99866.36, 92900.00, 6966.36, '11ca34d4-27bb-48ce-915a-81996dc98f9b', '2025-06-12 09:48:22', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2025-10-11 08:00:04', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('7269890f-a7b1-47e0-907b-c0fb5eacc576', 'MA OBEC FM', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 4609000.00, 4931630.00, 0.00, 0.00, 4609000.00, 100.00, 4609000.00, 0.00, 4609000.00, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:10:17', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('72f91cb8-944d-44f5-babc-f4288568c964', 'Web Hosting สตช.', '2024-12-01', '2025-11-30', 'ชนะ (Win)', '', 'Nutanix HCI, Network, Backup, UPS, Firewall', '2024-10-07', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 12131800.00, 12981026.00, 10393849.25, 11121418.70, 1737950.75, 14.33, 12131800.00, 10393849.25, 1737950.75, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:20:48', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('736481b1-4d6a-4fba-ab31-c07181024fc1', 'GFCA AAI Factory MA Wireless Cisco9115AX AP 1Y_2025_8x5NBD_END_31-Aug-2026', '2025-08-31', '2026-08-31', 'ชนะ (Win)', '', 'AAI Factory MA Wireless Cisco9115AX AP 1Y_2025_8x5NBD_END_31-Aug-2026', '2025-06-12', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 76500.00, 81855.00, 52250.00, 55907.50, 24250.00, 31.70, 76500.00, 52250.00, 24250.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-06-24 07:10:01', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('73ea99da-21a7-4c2d-992f-005d981da3bc', 'Foritoken License Evergreen', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '0000-00-00', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 185000.00, 197950.00, 150200.00, 160714.00, 34800.00, 18.81, 185000.00, 150200.00, 34800.00, '2f1ee3da-fe91-4f06-b0ae-62a206c7cd5d', '2025-06-12 11:01:37', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('74152581-8a94-443f-ad39-140e5f9dc509', 'Samsung LaserJet Toner SL-4020ND  (SU894A) - 15 box', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'IV671030001', '', '2024-10-30', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 36448.60, 39000.00, 27670.50, 29607.44, 8778.10, 24.08, 36448.60, 27670.50, 8778.10, 'df6e7ebd-77f2-49e4-bcdf-04c71608005f', '2024-12-09 01:55:48', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', '2', '19747bf2-8f2d-47db-a2e8-4fca20843812', 7.00),
('7458164f-8df5-43d1-8883-bff87bbc9496', 'โครงการจัดซื้อจอป้ายประชาสัมพันธ์ LED พร้อมติดตั้ง ให้กับชุมชนภายในเขตองค์การบริหารส่วนตำบลมาบยางพร', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '2025-04-02', '70dd36b5-f587-4aa9-b544-c69542616d34', '2', 9317757.01, 9970000.00, 7209300.00, 7713951.00, 2108457.01, 22.63, 4658878.50, 3604650.00, 1054228.50, 'da4d87d6-33f5-4937-85e5-d0be395d6123', '2025-06-11 10:32:28', '70dd36b5-f587-4aa9-b544-c69542616d34', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('74c45e80-c867-46cc-919b-c6e4c7d0c076', 'Smart Showroom For THONBURI PHANICH', '2025-05-01', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', '- ดูหน้างานเพื่อประเมินการติดตั้งกล้อง (13 มิถุนายน 2568)\r\n- นำเสนอ Solution (20 มิถุนายน 2568)', '2025-05-01', '3', '1', 1500000.00, 1605000.00, 500000.00, 535000.00, 1000000.00, 66.67, 0.00, 0.00, 0.00, '3b652cc4-3afe-4caa-b092-fa8987489c78', '2025-06-11 12:10:19', '3', '2025-10-27 06:35:02', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('759f33fb-b998-4d5f-bd80-343867ef52a0', 'จ้างเหมาปรับปรุงระบบไฟฟ้าของกล้องโทรทัศน์วงจรปิด', '2024-08-23', '2024-12-20', 'ชนะ (Win)', '336/2567', '', '2024-08-22', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 461682.24, 494000.00, 108062.00, 115626.34, 353620.24, 76.59, 461682.24, 108062.00, 353620.24, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 03:07:51', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2025-10-27 05:24:09', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('7692b403-0447-4011-8154-ddcf896f5dd4', 'Apple Tablet - PO6711-00003', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO6711-00003', '', '2024-11-01', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 58100.00, 62167.00, 55520.00, 59406.40, 2580.00, 4.44, 58100.00, 55520.00, 2580.00, '6d128135-3e95-4226-9956-21bb63f25cc0', '2024-12-06 10:27:15', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('77b01509-7fe9-453c-ad97-73e8adadf445', 'โครงการติดตั้งระบบตรวจจับและวิเคราะห์ป้ายทะเบียนรถอัตโนมัติ บริเวณพื้นที่ หมู่ที่ 2 หมู่ที่ 3, 4, 5, 6, 7, 8', '2024-07-25', '2024-12-22', 'ชนะ (Win)', '24/2567', '', '2024-06-04', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 4578598.13, 4899100.00, 3648450.00, 3903841.50, 930148.13, 20.32, 4578598.13, 3648450.00, 930148.13, NULL, '2025-06-11 11:54:27', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('78f216ec-1aad-43a2-898f-e2301ce04a05', 'โครงการจัดตั้งศูนย์รับแจ้งเหตุฉุกเฉินแห่งชาติ สำนักงานตำรวจแห่งชาติ', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', 'End user : RTP\r\nProject prime : SP', '0000-00-00', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', 'de3fc0f5-9ebf-4c47-88dd-da5a570653ae', 934579.44, 1000000.00, 46728.97, 50000.00, 887850.47, 95.00, 0.00, 0.00, 0.00, '88d465c6-3e16-4c58-a6da-10bce309af89', '2025-03-17 04:04:12', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-10-11 08:00:04', '2', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('7b578d51-8794-46f4-8a3a-f1da2429e855', 'MA กล้องอาคารบ้านเจ้าพระยา', '0000-00-00', '0000-00-00', 'แพ้ (Loss)', '', 'ราคา 1 ปี ไม่รวมอะไหล่', '0000-00-00', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 317757.01, 340000.00, 261682.24, 280000.00, 56074.77, 17.65, 0.00, 0.00, 0.00, '360a7a11-6bcd-4301-8156-b4d11ebd6794', '2024-11-28 08:52:18', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2025-10-11 08:00:04', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('7c338957-c9a9-4134-b79b-3d131b19dec9', 'WA SCB Magnetic Stripe 2,200u(ปีที่ 2/5)', '2025-01-01', '2025-12-31', 'ชนะ (Win)', 'PO. 674111078697 Date 03/10/2024', 'เริ่ม 01/12/2024 - 30/11/2029', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 138600.00, 148302.00, 5000.00, 5350.00, 133600.00, 96.39, 138600.00, 5000.00, 133600.00, '0f80acd4-d034-4175-b501-f879a9e203de', '2025-01-30 07:57:09', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3bf8bc62-f878-4fd9-9bee-2a6917190458', 7.00),
('7c67ce7e-ee05-487f-a763-4627899516bb', 'โครงการ บ่อวิน สมาร์ท ซิตี้ ดูแลสุขภาพแบบอัจฉริยะ (Smart Health Care) สำหรับผู้สูงอายุ ประจำปีงบประมาณ 2567', '2023-09-02', '2024-09-02', 'ชนะ (Win)', '1/2567', '', '2023-09-15', '3', '1', 623831.78, 667500.00, 423848.00, 453517.36, 199983.78, 32.06, 623831.78, 423848.00, 199983.78, '32104ee7-4b28-400b-bb7b-1ab55e1cf19d', '2024-10-11 23:29:28', '3', '2025-10-11 08:00:04', '3', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('7cb191ba-d203-4c00-8841-f4957193c26a', 'SC Polymer Solar - PO-2024082900001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024082900001', '', '2024-08-29', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 108720.00, 116330.40, 100000.00, 107000.00, 8720.00, 8.02, 108720.00, 100000.00, 8720.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 09:54:56', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('7cfc60f4-9d2d-4694-ac36-18d15392b2e4', 'GFCA New Server ThinkSystem SR650 v4 - 3yr Warranty_Site AAI', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-07-21', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 540000.00, 577800.00, 429000.00, 459030.00, 0.00, 0.00, 54000.00, 42900.00, 11100.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-07-21 08:47:14', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('7dbb2b14-756e-45fc-bbb1-a4142d02dad3', 'เคลมประกัน KTB สาขาปากช่อง', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-014253', 'SN 0954824E น้ำเข้าเครื่อง Mainboard และ Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 63000.00, 67410.00, 6675.00, 7142.25, 56325.00, 89.40, 63000.00, 6675.00, 56325.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:53:42', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('7e1acdba-620b-4bf5-87fb-1b7ef59f6263', 'โครงการจัดหาระบบนวัตกรรมตำรวจสร้างเมืองปลอดภัย ตำรวจภูธร ภาค 6', '2025-04-18', '2026-04-17', 'ชนะ (Win)', '3/2568', 'ขายผ่าน บริษัท MM', '2025-04-16', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '28534929-e527-4755-bd37-0acdd51b7b45', 14226168.22, 15222000.00, 9398216.75, 10056091.92, 4827951.47, 33.94, 14226168.22, 9398216.75, 4827951.47, NULL, '2025-06-10 07:15:46', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2025-10-11 08:00:04', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('7e7dfe60-c9c7-4239-b8ee-6d34896d6ee7', 'Stiker', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-06-25', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 12000.00, 12840.00, 8100.00, 8667.00, 3900.00, 32.50, 12000.00, 8100.00, 3900.00, '5f32551c-7a96-4b5f-b485-2357623e9893', '2025-06-25 07:27:27', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-10-11 08:00:04', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('7f69f115-4ea7-4c58-8b48-d16f6fefe0be', 'จัดซื้อ Server เทศบาลนครนครศรีธรรมราช', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2024-10-29', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2', 186915.89, 200000.00, 170000.00, 181900.00, 0.00, 0.00, 18691.59, 17000.00, 1691.59, 'ff09ea1e-4e6a-44e0-8637-03ac0670070d', '2025-06-12 09:53:47', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2025-10-11 08:00:04', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('7fc6a707-7a83-4295-a0fc-f32434aeecb0', 'บริการเช่าใช้ระบบ AI Platform พร้อม Hardware สำหรับงาน Showroom ICONIC YOU ห้าง One Bangkokระยะเวลา 2.5 เดือน', '2025-04-16', '2025-06-30', 'ชนะ (Win)', 'QT000001262', 'เดือนที่ 1 วันที่ 16 Apr. - 15 May 2025\r\nเดือนที่ 2 วันที่ 16 May. - 15 Jun. 2025\r\nเดือนที่ 3 วันที่ 16 Jun. - 15 Jul. 2025', '2025-04-09', '3', '1', 95000.00, 101650.00, 61250.00, 65537.50, 33750.00, 35.53, 95000.00, 61250.00, 33750.00, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-04-09 06:24:22', '3', '2025-10-11 08:00:04', '3', 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('8024bdad-92e7-41ca-8830-0aeba1db4e84', 'ระบบอ่านป้ายทะเบียนเทศบาลด่านสำโรง', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', 'ประกาศร่างประชาพิจารย์แล้ว', '2025-04-18', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 5600000.00, 5992000.00, 3068246.00, 3283023.22, 0.00, 0.00, 560000.00, 306824.60, 253175.40, 'f5c84ebc-38a3-47a5-8b55-aeed3c520473', '2025-06-12 10:49:09', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('8091328f-8720-4838-9682-e08933faa8ac', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central Westgate (ครั้งที่ 5)', '2025-08-20', '2025-08-27', 'ชนะ (Win)', '', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central Westgate (ครั้งที่ 5)', '2025-08-19', '3', '1', 55300.00, 59171.00, 19190.00, 20533.30, 36110.00, 65.30, 55300.00, 19190.00, 36110.00, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-10-27 06:26:23', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:26:23', NULL, 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('80c09146-6e1c-48c2-a442-164844260006', 'GFCA Renew Veeam Data Platform Foundation Universal Subscription License (10 Workloads) 476 Days', '2025-05-13', '2026-08-31', 'ชนะ (Win)', 'SE20250328-001', 'GFCA: AAI Site Renew Veeam Data Platform Foundation Universal Subscription License (10 Workloads) 476 Days', '2025-04-24', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 73800.00, 78966.00, 59750.00, 63932.50, 14050.00, 19.04, 73800.00, 59750.00, 14050.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-04-25 08:51:18', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('819456c1-3df2-41b4-874f-377b4d2ecca4', 'โครงการเพิ่มประสิทธิภาพในการบริหารจัดการด้านความปลอดภัยในพื้นที่อาคารกรีฑาในร่ม ศูนย์กีฬาแห่งชาติภาคตะวันออก', '2025-10-10', '2026-06-07', 'ชนะ (Win)', '40/2569', '', '2025-02-11', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 53551401.87, 57300000.00, 38000000.00, 40660000.00, 15551401.87, 29.04, 53551401.87, 38000000.00, 15551401.87, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-27 08:23:11', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-27 04:32:33', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('823bb70a-bf37-4d94-b6ec-d65db3edff56', 'KTB Per-call AUP Shinko', '2025-01-01', '2025-12-31', 'ชนะ (Win)', '-', '', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 800000.00, 856000.00, 470000.00, 502900.00, 330000.00, 41.25, 800000.00, 470000.00, 330000.00, 'f53a9e46-d14b-40d7-8acc-cd7a0a2ced0e', '2025-06-16 02:38:16', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('82a6796b-dd7f-4cc7-b822-5cbee53bc4e1', 'ค่าชุดอุปกรณ์และระบบแพลตฟอร์มวิเคราะห์ข้อมูลและปัญญาประติษฐ์ในบริการการดูแลการใช้ชีวิตและ ดูแลสุขภาพระยะยาวสำหรับผู้สูงอายุ (2568)', '2025-10-01', '2026-09-30', 'ชนะ (Win)', '11/2569', 'ค่าชุดอุปกรณ์และระบบแพลตฟอร์มวิเคราะห์ข้อมูลและปัญญาประติษฐ์ในบริการการดูแลการใช้ชีวิตและ ดูแลสุขภาพระยะยาวสำหรับผู้สูงอายุ \r\nอุปกรณ์ AITRACKER จำนวน 50 ชุด ระยะเวลาเช่าใช้ 12 เดือน', '2025-10-01', '3', '1', 568800.00, 608616.00, 407226.00, 435731.82, 161574.00, 28.41, 568800.00, 407226.00, 161574.00, 'cc80c251-336b-4039-9850-5a042948e8f3', '2025-10-27 06:02:58', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:02:58', NULL, '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('837e23f3-a09d-4927-bad4-27837014451e', 'ค่าบริการเช่าใช้ชุดเฝ้าระวังเหตุฉุกเฉินการล้มในผู้ที่มีภาวะพึ่งพิงในบ้านและภายนอกบ้านพร้อมระบบแพลตฟอร์มและงานบริการ เทศบาลนครมาบตาพุด', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', 'ค่าบริการเช่าใช้ชุดเฝ้าระวังเหตุฉุกเฉินการล้มในผู้ที่มีภาวะพึ่งพิงในบ้านและภายนอกบ้านพร้อมระบบแพลตฟอร์มและงานบริการ เทศบาลนครมาบตาพุด', '2025-10-28', '3', '1', 412500.00, 441375.00, 300000.00, 321000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'abb7ccfe-d759-4007-81d6-1f11bc439a37', '2025-10-27 12:43:24', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 12:43:24', NULL, '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('83a43bff-44ad-4a1b-a7fc-bb3ae2ca67d5', 'Blaster Camera - IIS2410-0011', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'IIS2410-0011', '', '2024-10-17', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 221320.00, 236812.40, 203600.00, 217852.00, 17720.00, 8.01, 221320.00, 203600.00, 17720.00, '48cf0983-375c-46de-ab41-72350901a376', '2024-12-06 10:17:08', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('858613c6-b0b0-46f1-a307-c1eb89c9e588', 'GFCA MA IBM Storwize 5010E_GFCA_Site_2Years_END30Apr2027', '2025-05-01', '2027-04-30', 'ชนะ (Win)', '', '', '2025-03-19', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 380400.00, 407028.00, 266400.00, 285048.00, 114000.00, 29.97, 380400.00, 266400.00, 114000.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-03-19 09:53:39', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('88577e74-e2b4-479d-9b8c-a47f5332a8c8', 'งานจ้างเหมาซ่อมแซมระบบกล้องโทรทัศน์วงจรปิด บริเวณหมู่ที่ 2,4,6 และหมู่ที่ 8 ตำบลพลูตาหลวง อำเภอสัตหีบ จังหวัดชลบุรี จำนวน 4 รายการ', '2025-05-30', '2025-06-10', 'ชนะ (Win)', '173/2568', '', '2025-04-16', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 46213.08, 49448.00, 17600.00, 18832.00, 28613.08, 61.92, 46213.08, 17600.00, 28613.08, NULL, '2025-06-11 12:01:31', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('8b9ac1ee-fee4-4bb4-be6d-aabe610f27aa', 'MA DLD NSW3', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 500000.00, 535000.00, 204160.00, 218451.20, 295840.00, 59.17, 500000.00, 204160.00, 295840.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:39:52', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'ระบบรักษาความปลอดภัยประตูทางเข้า-ออก (มหาวิทยาลัยวลัยลักษณ์)', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', '', '2025-01-16', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 468470.00, 501262.90, 311000.00, 332770.00, 157470.00, 33.61, 0.00, 0.00, 0.00, NULL, '2025-01-16 11:03:50', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-10-11 08:00:04', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('8c4abdec-c8a4-46c3-a65e-76fa084764ae', 'เคลมประกัน KTB สาขาฉะเชิงเทรา', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-014621', 'SN 0894424C น้ำเข้าเครื่อง Mainboard ช๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 48000.00, 51360.00, 3605.00, 3857.35, 44395.00, 92.49, 48000.00, 3605.00, 44395.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 05:02:05', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('8cc371a1-8b3b-4688-8998-7c796af5e492', 'MS server 2025 std', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-06-30', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 38000.00, 40660.00, 34000.00, 36380.00, 0.00, 0.00, 3800.00, 3400.00, 400.00, '0d4e8645-ff06-4531-bc5a-09e6570248d8', '2025-06-30 14:08:26', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-10-11 08:00:04', NULL, '45e92af5-138c-44de-9a2f-c6fd2e56427e', 7.00),
('8d6a62a2-3ca7-4f4b-930a-fa355d752ee9', 'เคลมประกัน KTB สาขาสุโขทัย', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-013519', 'SN 0917524C มดเข้าเครื่อง Mainboard และ Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 63000.00, 67410.00, 4730.00, 5061.10, 58270.00, 92.49, 63000.00, 4730.00, 58270.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:07:55', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('8f307551-8e39-40f6-a66d-ee1ea2c6d7e1', 'ค่าเช่าเครื่องเงินไชโย (Project ทันใจ) 1721 Set', '2024-01-01', '2024-12-31', 'ชนะ (Win)', '', '', '2024-01-01', '3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 3396964.49, 3634752.00, 2479784.07, 2653368.96, 917180.42, 27.00, 3396964.49, 2479784.07, 917180.42, '6b3ba15b-ee6d-41ab-a543-d345e9f62259', '2024-11-11 08:53:21', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-10-11 08:00:04', NULL, '1d285bc6-cc8c-47f7-900e-bf84c92f12ad', 7.00),
('8f6e2e97-d5af-4515-b4e4-60d60a6939e8', 'ค่าบริการเช่าใช้ระบบดูแลเฝ้าระวังการล้มในผู้สูงอายุ Fall Detection and emergency monitoring สามารถรับส่งเหตุฉุกเฉินได้ระหว่างหน่วยงานหลักและหน่วยงานฉุกเฉินย่อยได้', '0000-00-00', '0000-00-00', 'ยกเลิก (Cancled)', '', '', '2024-11-28', '3', '1', 1239359.81, 1326115.00, 707289.72, 756800.00, 532070.09, 42.93, 0.00, 0.00, 0.00, '9a8307fa-375b-47c3-b09d-2f7ca12f0c02', '2024-12-02 14:51:50', '3', '2025-10-25 15:07:02', '3', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('8fc7c97c-4d99-4201-92e6-0ecbbee5c956', 'MA KTBCS Passbook Printer 563 Units HPR4915', '2025-01-01', '2025-06-30', 'ชนะ (Win)', 'PO.4700673803 Date 2/4/2025', '', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 619992.00, 663391.44, 130000.00, 139100.00, 489992.00, 79.03, 619992.00, 130000.00, 489992.00, NULL, '2025-06-16 03:52:08', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', NULL, '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('9057cfe3-9c6d-424d-9044-ff147aa46aab', 'ค่าบริการเช่าระบบ Kin-yoo-dee Healthcare Platform หรือ แพลตฟอร์มสำหรับดูแลสุขภาพดูแลกลุ่มเสี่ยงกลุ่มป่วยด้วยโรคเบาหวานและความดันโลหิตสูง และผู้สูงอายุทางไกล', '0000-00-00', '0000-00-00', 'ยกเลิก (Cancled)', '', '', '2024-11-28', '3', '1', 545424.30, 583604.00, 381121.50, 407800.00, 164302.80, 30.12, 0.00, 0.00, 0.00, '9a8307fa-375b-47c3-b09d-2f7ca12f0c02', '2024-12-02 14:53:09', '3', '2025-10-25 15:06:34', '3', '3224e7a4-44ee-40ad-a6ac-22305c2b01eb', 7.00),
('90e541d0-fdfc-457b-bc48-8331cd1aad81', 'โครงการติดตั้งกล้องในลิฟท์', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-04-16', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 291399.07, 311797.00, 256960.00, 274947.20, 0.00, 0.00, 29139.91, 25696.00, 3443.91, '11ca34d4-27bb-48ce-915a-81996dc98f9b', '2025-06-12 10:54:00', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, '581f6ca7-8e1e-447a-9dae-680755c4fd29', 7.00),
('913fb068-16a4-4e89-926e-488a27430a6e', 'SC Polymer Solar - PO-2024080100001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024080100001', '', '2024-08-01', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 56650.00, 60615.50, 52034.00, 55676.38, 4616.00, 8.15, 56650.00, 52034.00, 4616.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 07:56:40', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('918903af-e6df-4694-82a3-1cea8dda06e0', 'Server Installation: Faculty of Dentistry Chulalongkorn University', '2025-01-06', '2025-01-07', 'ชนะ (Win)', '', '', '2025-01-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 7000.00, 7490.00, 0.93, 1.00, 6999.07, 99.99, 7000.00, 0.93, 6999.07, 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', '2025-01-06 09:40:09', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('925f7e0d-2d9c-4751-85fc-1222c37ee500', 'เคลมประกัน KTB สาขาขุนยวม', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-013647', 'SN 0899824C มดเข้าเครื่อง Mainboard และ Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 63000.00, 67410.00, 5250.00, 5617.50, 57750.00, 91.67, 63000.00, 5250.00, 57750.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:09:52', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('92a99359-0555-4ff1-9be4-c26808189158', 'Belly Grading AI Automation', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', 'AIBGRADE001 - Belly Grading AI Automation\n1 x Industrial Grade Camera 4K\n1 x Edge AI GPU, 32GB DDR4,1TB SSD\n1x POE Switch 8 Ports\n1x LED Light , IOT Control Board\n1x Industrial Panel 15&quot; with Housing\n1x UPS 1KVA (600W)\n1x Food Grade Stainless Cart with Power Plug\n1 x Accessories Lan 100 Metres\n(ราคารวมอุปกรณ์ และค่าติดตัง พร้อมการรับประกัน 1 ป (Remote Service Support 24x7\nwith Hotline Call)\n\n(สร้างโดย : พี่หญิง)', '2025-07-18', '3', '1', 424250.00, 453947.50, 300000.00, 321000.00, 0.00, 0.00, 42425.00, 30000.00, 12425.00, 'c120a5b5-375a-411b-87d4-5fa61e6453d9', '2025-06-16 00:52:25', '3', '2025-10-11 08:00:04', '5', '54b6a0a0-54c2-448c-a340-71d12acdc5f6', 7.00),
('94cfd496-162c-424f-baba-021d517b99a9', 'เคลมประกัน KTB สาขายะลา', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-013755', 'SN 0949724E น้ำเข้าเครื่อง PC จากเหตุน้ำท่วม', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 27000.00, 28890.00, 21920.00, 23454.40, 5080.00, 18.81, 27000.00, 21920.00, 5080.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:29:26', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('953d11ca-bf90-4f85-a34c-737447dc6343', 'งานซื้อคอม 3 รายการ ฝ่ายหลักประกันสุขภาพ สำนักสาธารณะสุข เมืองพัทยา', '2025-02-24', '2025-04-25', 'ชนะ (Win)', '172/2568', '', '2025-02-04', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 74766.36, 80000.00, 67200.00, 71904.00, 7566.36, 10.12, 74766.36, 67200.00, 7566.36, NULL, '2025-06-11 10:48:56', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', '193f9eed-2938-4305-ab65-828ac5253b30', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('95ac9cb3-e7c1-4016-b7c4-447f6d70e1c8', 'MA ระบบจ้างพัฒนาระบบการจัดซื้อจัดจ้าง ระยะที่ 1', '2026-01-01', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', 'MA ระบบจ้างพัฒนาระบบการจัดซื้อจัดจ้าง ระยะที่ 1', '2025-09-01', '3', '1', 480000.00, 513600.00, 290000.00, 310300.00, 0.00, 0.00, 48000.00, 29000.00, 19000.00, '34ea3368-fa1c-445a-aeb8-821c87086d3a', '2025-10-27 06:56:17', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:56:17', NULL, '075afde8-650f-4d75-b73d-f41242854682', 7.00);
INSERT INTO `projects` (`project_id`, `project_name`, `start_date`, `end_date`, `status`, `contract_no`, `remark`, `sales_date`, `seller`, `team_id`, `sale_no_vat`, `sale_vat`, `cost_no_vat`, `cost_vat`, `gross_profit`, `potential`, `es_sale_no_vat`, `es_cost_no_vat`, `es_gp_no_vat`, `customer_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `product_id`, `vat`) VALUES
('96f5ad4d-3a2d-4f3d-a909-9c74eaf3df55', 'MA K8S Honda Leasing', '2025-01-01', '2025-12-31', 'ชนะ (Win)', '', '', '2024-10-28', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 95000.00, 101650.00, 0.00, 0.00, 95000.00, 100.00, 95000.00, 0.00, 95000.00, 'f004cbe4-f666-4de7-8e85-7f940b6d8393', '2024-10-31 22:32:22', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('97db86a0-878d-4627-b410-316f1dda7152', 'งานเช่า Server อบจ.ชลบุรี ครั้งที่ 1', '2025-07-30', '2025-09-30', 'ชนะ (Win)', '14/2568', '', '2025-07-30', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 74766.36, 80000.00, 0.00, 0.00, 74766.36, 100.00, 74766.36, 0.00, 74766.36, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2025-10-27 07:57:50', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-27 10:41:09', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('98ae844c-7b83-4b19-bd1a-5cc769b4d5a3', 'ระบบยืนยันตัวตนผ่าน LDAP (LDAP Authentication)', '2025-10-24', '2025-11-14', 'ชนะ (Win)', 'QT-000001406', 'ระบบยืนยันตัวตนผ่าน LDAP (LDAP Authentication)\r\n\r\nรายละเอียด Scope งาน ดังนี\r\n1. Single Sign-On (SSO)\r\n2. Custom Login Page\r\n3. Forgot Password / Reset via Email\r\n4. บังคับเปลี4ยนรหัสผ่านทุก 120 วัน\r\n5. Account Management ผ่าน Web อบจ.ชลบุรี\r\nTimeline ในการดําเนินงาน 3 สัปดาห์', '2025-10-24', '3', '1', 150000.00, 160500.00, 0.00, 0.00, 150000.00, 100.00, 150000.00, 0.00, 150000.00, 'fb683856-9635-4316-ad3a-2eb57d6eb10f', '2025-10-27 04:28:12', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 04:28:12', NULL, '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('98c33fac-85b6-42dd-a794-2767f03300eb', 'โครงการดูแลสุขภาพของคนในชุมชน ผ่านระบบดูแลผู้สูงอายุด้วยแพลตฟอร์มดิจิตอลและอุปกรณ์ตรวจจับการล้ม แบบอัตโนมัติ เทศบาลนครระยอง', '2025-05-01', '2026-04-30', 'ชนะ (Win)', '', 'เช่าใช้อุปกรณ์พร้อมระบบ จำนวน 30 เครื่อง ระยะเวลา 12 เดือน', '2025-01-01', '3', '1', 320560.75, 343000.00, 266816.00, 285493.12, 53744.75, 16.77, 320560.75, 266816.00, 53744.75, '677f5f38-3f7f-4ca8-b9d6-e4b60f7f241a', '2025-04-09 07:05:39', '3', '2025-10-11 08:00:04', '3', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('992f5009-7d10-4444-bfc4-bd4e03387917', 'โครงการพัฒนาระบบบริหารจัดการคลังส่งกำลังบำรุง', '2024-11-10', '2026-11-10', 'ชนะ (Win)', 'พธ.28/2567', 'ขายผ่าน บริษัท สุพรีม ดิสทริบิวชั่น จำกัด (มหาชน)', '2025-11-18', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '28534929-e527-4755-bd37-0acdd51b7b45', 24899604.00, 26642576.28, 21722924.68, 23243529.41, 3176679.32, 12.76, 24899604.00, 21722924.68, 3176679.32, '1d9884a8-7762-4f28-a3b5-8419f13ffe8b', '2025-06-10 07:27:38', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2025-10-11 08:00:04', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('996fd65e-4532-4f9a-97e2-0e05403bc275', 'GFCA ASIATIC Network Card 1Gbps for ThinkSystem SR650 Upgrade', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-09-10', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 35000.00, 37450.00, 23400.00, 25038.00, 11600.00, 33.14, 35000.00, 23400.00, 11600.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-09-10 08:55:36', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('9ac3c951-ff30-4432-8ed9-207bc3c4e2bb', 'GFCA Food Ingredient Technology Co., Ltd.  MA 1 Year', '2025-08-31', '2026-08-31', 'ชนะ (Win)', '', '', '2025-07-21', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 22800.00, 24396.00, 15400.00, 16478.00, 7400.00, 32.46, 22800.00, 15400.00, 7400.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-07-29 08:13:26', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('9c636263-c064-4166-a18c-2b7bbf47b92f', 'จัดซื้อผ้าหมึกสำหรับเครื่อง Auto Update Passbook Printer ยี่ห้อ Hitachi สำหรับปี 2568', '2025-08-14', '2026-08-13', 'ใบเสนอราคา (Quotation)', '', 'เป็นผ้าหมึก OEM', '2025-07-14', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 2003551.40, 2143800.00, 1720560.00, 1840999.20, 0.00, 0.00, 200355.14, 172056.00, 28299.14, 'a6371051-02a6-44d0-83f7-78a929a2fb30', '2025-07-14 08:58:34', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2025-10-11 08:00:04', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'a94ff83b-e9ff-4d21-87ed-a7849f8e710b', 7.00),
('9d490452-0821-4b5e-ba06-ce9626bff7cc', 'iPad Air 6 M3 11&quot; 730  Set', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', '', '2025-06-11', '3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 16800000.00, 17976000.00, 15000000.00, 16050000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'f5489b6a-fd5b-4896-b655-761768e44b8f', '2025-06-11 08:04:41', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-10-11 08:00:04', NULL, 'a2b0ad6f-aaf7-49ef-a6d3-b3d4f55e02f9', 7.00),
('9d96ef67-1b7d-4102-b1fc-26fef210c292', 'HP DL320', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-06-30', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 290000.00, 310300.00, 270000.00, 288900.00, 0.00, 0.00, 29000.00, 27000.00, 2000.00, '5f32551c-7a96-4b5f-b485-2357623e9893', '2025-06-30 14:12:58', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('9e2cf0c9-4462-4a69-b6c7-c77785578f5e', 'ติดตั้ง Signature Pad พร้อมค่าพัฒนาระบบที่เชื่อมต่อกับระบบธนาคารได้ พร้อมส่งมอบ Source Code และกรรมสิทธิ์ให้กับธนาคาร', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', 'นำส่งใบสืบราคาเพื่อนำเสนอโปรเจค ติดตั้ง Signature Pad พร้อมค่าพัฒนาระบบที่เชื่อมต่อกับระบบธนาคารได้ พร้อมส่งมอบ Source Code และกรรมสิทธิ์ให้กับธนาคาร', '2025-05-17', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 22535420.56, 24112900.00, 19232028.85, 20578270.87, 0.00, 0.00, 0.00, 0.00, 0.00, 'b2907f8d-53f0-4f71-bc36-11e24a52c10d', '2025-06-13 03:13:09', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2025-10-11 08:00:04', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('9f8238dc-3e88-4d57-93f4-bdae13ec301b', 'โครงการติดตั้งระบบโทรทัศน์วงจรปิด (CCTV) อาคารศูนย์เรียนรู้ใต้ทะเลบางแสน', '2025-04-08', '2025-07-07', 'ชนะ (Win)', '', '', '2025-04-08', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 417757.01, 447000.00, 290000.00, 310300.00, 127757.01, 30.58, 417757.01, 290000.00, 127757.01, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2025-10-27 07:42:25', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-27 07:49:04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('a0c2b272-18f2-44f5-9a0b-33bf70e56be3', 'เคลมประกัน KTB สาขาบ่อวิน (ถนนสาย 331)', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-002006', 'SN 0905924C มดเข้าเครื่อง Mainboard ช๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 48000.00, 51360.00, 3546.00, 3794.22, 44454.00, 92.61, 48000.00, 3546.00, 44454.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:03:57', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('a2476611-d200-4882-93b6-a48caab4900e', 'OBEC Infrastructure 77M', '2024-08-01', '2024-10-01', 'ชนะ (Win)', '', '', '2024-07-17', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 1490000.00, 1594300.00, 0.00, 0.00, 1490000.00, 100.00, 1490000.00, 0.00, 1490000.00, 'cea804cd-55ab-4a3f-b9ff-a942547402a7', '2024-10-31 21:15:52', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'df374787-e96c-4d3c-8089-3867edd96cf4', 7.00),
('a313d528-c1ea-49d7-8bde-f9b1920a4993', 'ค่าบริการบํารุงรักษาชุดอุปกรณ์เครื่องวิเคราะห์-โรงงานชำแหละสุกรยโสธร', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', 'ค่าบริการบํารุงรักษาชุดอุปกรณ์เครื่องวิเคราะห์\r\nบริการซ่อมแซมแก้ไขปัญหา 7 วัน 8 ชัวโมงทําการ กรณีอุปกรณ์ชํารุดเนื่องการการใช้งานตาม\r\nปกติไม่รวมกระแสไฟฟ้ารั่ว หรือลัดวงจร เนื่องจากฟ้าผ่า หรือของตกหล่นจากกรใช้งานผิดปกติ\r\n1) ชุดเครื่องวิเคราะห์และจัดเก็บฐานข้อมูลส่วนกลาง จํานวน 2 เครื่อง\r\n2) Switch 8 ports 1 เครื่อง\r\n3) UPS Plug 1 ตัว\r\n4) Rack 19U\r\n5) ชุดเครื่องวิเคราะห์เกรด BF2 จํานวน 1 ชุด\r\n6) ชุดเครื่องวิเคราะห์เกรด 3 ชัOน จํานวน 1 ชุด (ติดตัังที\Zโรงงนแปรรูป)\r\n7) บริการ Domain Kudsonmoo.co สําหรับจัดเก็บข้อมูลบนคลาวด์1 ปี\r\n\r\n(สร้างโดย : พี่หญิง)', '2025-06-24', '3', '1', 89000.00, 95230.00, 80000.00, 85600.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2025-06-16 00:45:38', '3', '2025-10-11 08:00:04', '3', '54b6a0a0-54c2-448c-a340-71d12acdc5f6', 7.00),
('a34537bb-801b-45f1-aa34-f7b3b1f13064', 'โครงการ Anti Virus เมืองพัทยา  68', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '0000-00-00', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 747663.55, 800000.00, 500000.00, 535000.00, 247663.55, 33.12, 373831.78, 250000.00, 123831.78, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2025-06-11 09:01:04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-11 08:00:04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('a3fa105e-b258-474a-87e5-e39272e3f127', 'โครงการจัดทำพื้นที่สำหรับจัดเก็บเอกสารและอุปกรณ์ของสำนักยุทธศาสตร์และงบประมาณ', '2025-01-08', '2025-06-08', 'ชนะ (Win)', '119/2568', '', '2025-01-08', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 2373831.78, 2540000.00, 1950000.00, 2086500.00, 423831.78, 17.85, 2373831.78, 1950000.00, 423831.78, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-27 08:15:04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-11 08:00:04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('a458ea0c-327b-4f5a-8454-63352cefac85', 'SC Polymer Solar - PO-2024102400001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024102400001', '', '2024-10-24', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 152210.00, 162864.70, 140000.00, 149800.00, 12210.00, 8.02, 152210.00, 140000.00, 12210.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 10:21:05', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('a4a2747c-5e78-4196-85d6-22603ccb03b4', 'Project imedisyncTH', '0000-00-00', '0000-00-00', 'ยกเลิก (Cancled)', '', '', '2024-11-27', '3', '1', 2144300.00, 2294401.00, 1401869.16, 1500000.00, 742430.84, 34.62, 0.00, 0.00, 0.00, '0a462754-178e-4f0c-a510-d9dd40db6490', '2024-12-02 13:57:44', '3', '2025-10-25 15:07:44', '3', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('a5addc9d-b9be-41a0-b9ae-aae652e47826', 'BSP Hayashi Telempu Replacement Switch', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-02-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 25000.00, 26750.00, 0.93, 1.00, 24999.07, 100.00, 25000.00, 0.93, 24999.07, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-02-06 09:30:57', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('a5e4b1fb-d1da-4594-9ffd-d28817f252a2', 'โรงฆ่าสัตว์ กรมปศุสัตว์', '2025-03-04', '2025-07-04', 'ชนะ (Win)', '', 'Nutanix, H3C Switch, Fortigate, SQL Server, i-Net Clear Report Plus, WIndows Server 2025', '2025-01-29', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 3408000.00, 3646560.00, 2553552.68, 2732301.37, 854447.32, 25.07, 3408000.00, 2553552.68, 854447.32, '642afc1e-c8d5-42f3-a685-aa899e78be1e', '2025-03-13 09:38:01', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('a636ae0f-66d7-4bbb-98a1-91749eb59211', 'SC Polymer Solar - PO-2024081900002', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024081900002', '', '2024-08-19', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 254320.00, 272122.40, 233648.80, 250004.22, 20671.20, 8.13, 254320.00, 233648.80, 20671.20, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 09:48:15', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('a8036bf1-d571-4e64-82a4-ba9838de8c7e', 'GFCA New Sectigo PositiveSSL Multi-Domain - webmail.gfca.com Yearly', '2025-07-08', '2026-07-07', 'ชนะ (Win)', '', '', '2025-05-14', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 12100.00, 12947.00, 7550.00, 8078.50, 4550.00, 37.60, 12100.00, 7550.00, 4550.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-05-14 04:03:09', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('a8634a06-fac7-43c5-bae1-f1b24d7509aa', 'e-Movement DLD', '2025-05-30', '2025-12-30', 'ชนะ (Win)', '', 'Nutanix HCI, Network, Microsoft License', '2025-04-21', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 6976600.00, 7464962.00, 5556822.85, 5945800.45, 1419777.15, 20.35, 6976600.00, 5556822.85, 1419777.15, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 22:28:46', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('a8f71376-2433-4415-8538-bfffff67dbba', 'บำรุงรักษาเครื่องปรับสมุดเงินฝากอัตโนมัติ พร้อม Software ยี่ห้อ Hitachi รุ่น BH180 จำนวน 291 เครื่อง และระบบสนับสนุน (Server) จำนวน 2 เครื่อง', '2025-01-01', '2025-12-31', 'ชนะ (Win)', 'พณ.พ.03-240/2567', '', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 3898785.05, 4171700.00, 1635514.02, 1750000.00, 2263271.03, 58.05, 3898785.05, 1635514.02, 2263271.03, '3ef73d28-72ff-4c90-b04a-693a33baf895', '2025-01-06 06:27:17', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('aa8cb602-fdfc-4b1c-abc0-0d03c901fea9', 'ติดตั้งระบบ CCTV  4 ชุด', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-07-02', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 39200.00, 41944.00, 32820.00, 35117.40, 6380.00, 16.28, 39200.00, 32820.00, 6380.00, '67bda6e1-da1b-41c2-8658-f11662f15f6c', '2025-07-17 01:16:50', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('abd5e4f2-21a6-43e8-97f3-323e0f2d4230', 'เคลมประกัน KTB สาขาหางดง', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-002018', 'SN 0928124D มดเข้าเครื่อง Mainboard และ Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 63000.00, 67410.00, 4438.00, 4748.66, 58562.00, 92.96, 63000.00, 4438.00, 58562.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:05:54', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('ac62d191-fbb0-4592-b287-014ed16e422d', 'Toner for ML-3710ND  + Toner for ML-4020+ Ribbon Hitachi', '2024-01-01', '2024-12-31', 'ชนะ (Win)', '', '', '2024-01-01', '3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 18583317.76, 19884150.00, 14123321.50, 15111954.00, 4459996.26, 24.00, 18583317.76, 14123321.50, 4459996.26, 'f5489b6a-fd5b-4896-b655-761768e44b8f', '2024-11-11 08:43:18', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-10-11 08:00:04', NULL, '19747bf2-8f2d-47db-a2e8-4fca20843812', 7.00),
('ac6826b4-33b4-4c47-bf34-890d7b8f0e7e', 'จัดซื้อ All in one 5 เครื่อง', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '0000-00-00', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 131500.00, 140705.00, 114500.00, 122515.00, 0.00, 0.00, 13150.00, 11450.00, 1700.00, 'f8a6cd53-4c8f-490d-83c8-85db6fb422bb', '2025-07-17 01:26:50', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('ad3b9787-d382-4eb0-ac96-d9a3917d5db6', 'งานจ้างเหมาติดตั้งระบบป้องกันอัคคีภัยเพื่อความปลอดภัยของโรงเรียนในสังกัดกรุงเทพมหานคร (58 โรงเรียน)', '2025-04-01', '0000-00-00', 'ชนะ (Win)', '', 'Project-co: พี่ซีน\r\nDev Internal: พี่ขวัญ\r\nUX/UI: พี่แอมป์', '2025-01-16', '3', '1', 1118000.00, 1196260.00, 471575.00, 504585.25, 646425.00, 57.82, 1118000.00, 471575.00, 646425.00, NULL, '2025-01-16 08:47:14', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-10-25 15:04:23', '3', '6e2ba9df-293d-4d88-b85e-4399e237d8c0', 7.00),
('ad862d94-87fb-4be9-b37a-5ac08b2b8b7f', 'โครงการปรับปรุงประสิทธิภาพระบบกล้องโทรทัศน์วงจรปิด บริเวณชายหาดพัทยา', '2025-09-10', '2026-02-07', 'ชนะ (Win)', '392/2568', '', '2025-01-14', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 5794392.52, 6200000.00, 4070000.00, 4354900.00, 1724392.52, 29.76, 5794392.52, 4070000.00, 1724392.52, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-27 08:20:57', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-27 08:54:31', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('adac6142-24d1-4efe-a469-859c1cb11243', 'จ้างเหมาบริการดูแลระบบเครือข่ายหลักและความมั่นคงปลอดภัยทางไซเบอร์ขององค์การบริหารส่วนจังหวัดชลบุรี', '2025-10-17', '2026-09-30', 'ชนะ (Win)', '26/2569', '', '2025-10-17', 'ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 1990654.21, 2130000.00, 0.00, 0.00, 1990654.21, 100.00, 1990654.21, 0.00, 1990654.21, 'e85465c3-44e3-4210-a4ce-88f9aa09af26', '2025-10-27 07:36:10', 'ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', '2025-10-27 07:36:10', NULL, '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('adc5c8b8-68bf-420f-b240-0a4263b2d7b6', 'SC Polymer Solar - PO-2024102800001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024102800001', '', '2024-10-28', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 14980.00, 16028.60, 13780.00, 14744.60, 1200.00, 8.01, 14980.00, 13780.00, 1200.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 10:22:42', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('ae469909-1ee5-4e5b-871c-7f48aedc395a', 'Camera', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-06-26', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 140700.00, 150549.00, 110800.00, 118556.00, 29900.00, 21.25, 140700.00, 110800.00, 29900.00, '51304482-8440-4d89-836e-c45c9eda7631', '2025-06-26 07:52:54', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-10-11 08:00:04', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('ae8cd8e6-3101-4adf-a234-5d0fe550230b', 'โครงการปรับปรุงและเพิ่มประสิทธิภาพระบบกล้องโทรทัศน์วงจรปิดในพื้นที่ชุมชนเมืองพัทยา PH1', '2024-10-28', '2025-04-26', 'ชนะ (Win)', '42/2568', '', '2024-10-01', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 12803738.32, 13700000.00, 10250000.00, 10967500.00, 2553738.32, 19.95, 12803738.32, 10250000.00, 2553738.32, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-27 07:58:00', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-11 08:00:04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('aed4f594-e9f4-4ec3-844e-c0e35af9ec6f', 'BSP NIDEC Shibaura Wireless Access Point Onsite Check Problem', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-03-18', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 34000.00, 36380.00, 6880.00, 7361.60, 27120.00, 79.76, 34000.00, 6880.00, 27120.00, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-03-18 09:39:46', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('aeeb73ff-701c-48b2-b112-27a211b21376', 'งานซ่อมสายชัยพฤกษ์ (ไดนามิก+บุญกิจ)', '2024-09-30', '2025-01-28', 'ชนะ (Win)', '400/2567', '', '2024-09-23', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 4074766.36, 4360000.00, 3340000.00, 3573800.00, 734766.36, 18.03, 4074766.36, 3340000.00, 734766.36, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2025-06-11 12:40:34', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('af266db7-2c1d-4f64-a73b-c43d92137d4c', 'งานจ้างซ่อมแซมระบบกล้องโทรทัศน์วงจรปิด จำนวน 6 จุด ครั้งที่ 1 อบต.พลูตาหลวง', '2025-04-29', '2025-05-22', 'ชนะ (Win)', '44/2568', '', '2025-03-12', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 172897.20, 185000.00, 68000.00, 72760.00, 104897.20, 60.67, 172897.20, 68000.00, 104897.20, NULL, '2025-06-11 11:59:30', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('af77dcc6-7850-4057-ac4b-a47ace47a243', 'ติดตั้งระบบ CCTV', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-02-18', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 209707.48, 224387.00, 185770.00, 198773.90, 23937.48, 11.41, 209707.48, 185770.00, 23937.48, '67bda6e1-da1b-41c2-8658-f11662f15f6c', '2025-06-12 10:30:32', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('b1a3f595-09fa-47b6-b8c1-37338463393f', 'โครงการติดตั้งศูนย์ควบคุมและบริหารจัดการระบบเทคโนดลยีการจราจรและความปลอดภัยของ กทม ระยะที่ 1', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', 'ขายเฉพาะ License IBOC 20 license  ผ่าน บริษัท โปรอินไซด์', '2025-06-10', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '28534929-e527-4755-bd37-0acdd51b7b45', 1042160.00, 1115111.20, 780000.00, 834600.00, 262160.00, 25.16, 104216.00, 78000.00, 26216.00, '726fc634-2c97-418b-a230-45e936cf843b', '2025-06-10 07:32:03', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2025-10-11 08:00:04', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', 'b9fcda13-e694-4e04-a8df-fdf27ee08979', 7.00),
('b2c823a7-91ae-4242-a1e3-4aaf44722ed5', 'เคลมประกัน KTB สาขาแม่กลอง', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-014495', 'SN 0954524E น้ำเข้าเครื่อง Mainboard ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 48000.00, 51360.00, 5628.00, 6021.96, 42372.00, 88.28, 48000.00, 5628.00, 42372.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 04:56:27', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('b2fbde7d-175a-4822-a719-495b57d4b9c0', 'MA DLD e-Movement', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 450000.00, 481500.00, 205760.00, 220163.20, 244240.00, 54.28, 450000.00, 205760.00, 244240.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:37:51', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('b37c5366-3c93-41e1-934e-dd90d33fb1b0', 'งานซ่อมบำรุงเครื่องคอมพิวเตอร์ โรงเรียนเมืองพัทยา 9', '2024-12-25', '2025-02-08', 'ชนะ (Win)', '107/2568', '', '2024-12-16', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 455140.19, 487000.00, 176000.00, 188320.00, 279140.19, 61.33, 455140.19, 176000.00, 279140.19, NULL, '2025-06-11 10:27:38', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('b3e78fb7-7d86-44f0-aaf1-3a099f279bad', '15U Monitor', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-06-26', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 19000.00, 20330.00, 17000.00, 18190.00, 2000.00, 10.53, 19000.00, 17000.00, 2000.00, '5f32551c-7a96-4b5f-b485-2357623e9893', '2025-06-26 07:55:06', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('b403c2e1-8913-4e94-b6c8-b9f7f36a4f31', 'Signature Pad 1200 Set', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', 'Repeat Order', '2025-06-11', '3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 9345794.39, 10000000.00, 5700000.00, 6099000.00, 3645794.39, 39.01, 934579.44, 570000.00, 364579.44, 'f5489b6a-fd5b-4896-b655-761768e44b8f', '2025-06-11 07:52:45', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-10-11 08:00:04', '5', 'aa203517-e140-4abc-9fa8-0e9926365967', 7.00),
('b4d8b6c9-1eb5-46ab-a443-68e24357c990', 'SC Polymer Solar - PO-2024071100001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024071100001', '', '2024-07-11', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 123000.00, 131610.00, 113000.00, 120910.00, 10000.00, 8.13, 123000.00, 113000.00, 10000.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-04 02:02:01', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('b56986ef-95fa-4f26-ba4e-ae0a593003f1', 'งานว่าจ้างบำรุงรักษาเครื่องสแกนเนอร์ (Scanner) 13 เครื่อง', '2025-01-01', '2025-12-31', 'ชนะ (Win)', 'PO.4307096760', '', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 50500.00, 54035.00, 5000.00, 5350.00, 45500.00, 90.10, 50500.00, 5000.00, 45500.00, '2f12eaa0-9738-484d-8329-80e964ea5ee6', '2025-06-16 04:05:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', NULL, '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('b5aaa158-5fb0-466d-9016-9f36ebf15270', 'จ้างพัฒนาระบบการจัดซื้อจัดจ้าง ระยะที่ 2', '2025-06-05', '0000-00-00', 'ชนะ (Win)', '', '', '2025-06-05', '3', '1', 493000.00, 527510.00, 406000.00, 434420.00, 87000.00, 17.65, 493000.00, 406000.00, 87000.00, '34ea3368-fa1c-445a-aeb8-821c87086d3a', '2025-06-11 11:50:40', '3', '2025-10-25 15:01:16', '3', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('b659bc53-f12a-4a5c-9ab9-905939c9fb2e', 'Network สกบ.', '2024-10-07', '2024-12-31', 'ชนะ (Win)', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 740000.00, 791800.00, 0.00, 0.00, 740000.00, 100.00, 740000.00, 0.00, 740000.00, 'cea804cd-55ab-4a3f-b9ff-a942547402a7', '2024-10-31 21:24:52', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('b70608c1-6f57-4abd-bce0-9260962b0bb9', 'โครงการระบบแพลตฟอร์มวิเคราะห์ข้อมูลและปัญญาประดิษฐ์ในการบริการดูแลการใช้ชีวิตและดูแล สุขภาพระยะยาวสำหรับผู้สูงอายุ ในพื้นที่เทศบาลตำบลทับมา', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2024-12-02', '3', '1', 568800.00, 608616.00, 407226.00, 435731.82, 161574.00, 28.41, 568800.00, 407226.00, 161574.00, '2d4610f7-471d-42c1-a193-d79ac4eb24e8', '2024-12-02 14:59:29', '3', '2025-10-25 15:06:02', '3', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('b7657841-213b-4ef8-b20b-03fb0ac688c4', 'จัดซื้อครุภัณฑ์คอมพิวเตอร์หรืออิเล็กทรอนิกส์ จำนวน 7 รายการ ส่วนควบคุมโรค ซ.6 ยศศักดิ์', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '0000-00-00', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 403738.32, 432000.00, 356000.00, 380920.00, 47738.32, 11.82, 201869.16, 178000.00, 23869.16, NULL, '2025-06-11 11:44:00', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('b8278876-66e5-4ce7-a8b2-9242bcc37638', 'เคลมประกัน KTB สาขาน่าน ครั้งที่ 2', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-014620', 'SN 0944124E มดเข้าเครื่อง Mainboard และ Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 63000.00, 67410.00, 7912.00, 8465.84, 55088.00, 87.44, 63000.00, 7912.00, 55088.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 05:00:56', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('b83568c2-a38e-48ba-8804-dc2c155c7098', 'เคลมประกัน KTB สาขาพยุหะคีรี ครั้งที่ 2', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-014648', 'SN 0889124C มดเข้าเครื่อง Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 5000.00, 5350.00, 2200.00, 2354.00, 2800.00, 56.00, 5000.00, 2200.00, 2800.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 05:03:24', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('b88aafb0-9c67-4bd4-9043-e9239b92413b', 'MA Fortinet Project JW Estate Management Co., Ltd. (FGT60FTK21099FX8)', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-05-21', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 45000.00, 48150.00, 33000.00, 35310.00, 12000.00, 26.67, 45000.00, 33000.00, 12000.00, '0ecc689d-9c12-4936-b9df-596884715574', '2025-05-21 03:15:20', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('b922d9a1-08be-4ce3-8402-e1b1113ae430', 'โครงการปรับปรุงระบบความปลอดภัยและลิฟท์โดยสารสำนักงานตำรวจแห่งชาติ (ผู้รับผิดชอบโครงการ ตำรวจสันติบาล)', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-05-17', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '28534929-e527-4755-bd37-0acdd51b7b45', 17500000.00, 18725000.00, 12069710.76, 12914590.51, 5430289.24, 31.03, 1750000.00, 1206971.08, 543028.92, 'c2968a16-8dea-4f07-ab94-c7d2197562fa', '2024-12-03 01:18:37', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2025-10-11 08:00:04', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('b9760c3a-33a2-4684-a03a-e10d88230e58', 'คณะแพทยศาสตร์ จุฬาลงกรณ์มหาวิทยาลัย', '2568-01-01', '2568-01-09', 'ชนะ (Win)', 'อว 64.13/บส.40/2568', '', '2025-06-12', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2', 294392.52, 315000.00, 249450.00, 266911.50, 44942.52, 15.27, 294392.52, 249450.00, 44942.52, NULL, '2025-06-16 02:11:09', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2025-10-11 08:00:04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('ba49000b-3509-4377-8d0f-456286a45e5f', 'โครงการพัฒนาระบบบริหารงานบุคคลดิจิทัล (D-HR)', '2025-03-19', '2026-03-14', 'ชนะ (Win)', '83/2568', '', '2025-01-06', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 8037383.18, 8600000.00, 6900000.00, 7383000.00, 1137383.18, 14.15, 8037383.18, 6900000.00, 1137383.18, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2024-11-27 08:16:52', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-11 08:00:04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('bacb3b44-beee-4cb6-9f37-dbbed4ecf8b0', 'SC Polymer Solar - PO-2024102500001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024102500001', '', '2024-10-25', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 29900.00, 31993.00, 27500.00, 29425.00, 2400.00, 8.03, 29900.00, 27500.00, 2400.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 10:24:47', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('bad1c47d-0180-44ef-89eb-b7e853877c6b', 'e-Payment DLD', '2024-10-01', '2025-01-28', 'ชนะ (Win)', '', 'Nutanix HCI, Network, Microsoft License', '2024-09-02', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 9840000.00, 10528800.00, 8360994.85, 8946264.49, 1479005.15, 15.03, 9840000.00, 8360994.85, 1479005.15, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:46:55', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('bc6373b3-01aa-4e8d-815d-af7d94040cd2', 'Encode', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-04-09', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 50000.00, 53500.00, 40145.00, 42955.15, 9855.00, 19.71, 50000.00, 40145.00, 9855.00, '466cca72-833b-4631-80f5-1cafdf402375', '2025-06-12 11:18:15', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('bc78c5f6-8877-4d6e-8d24-7a0bac4746e0', 'Magnetic Stripe 1000 Unit', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2025-06-11', '3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 3600000.00, 3852000.00, 3000000.00, 3210000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'f5489b6a-fd5b-4896-b655-761768e44b8f', '2025-06-11 07:57:34', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-10-11 08:00:04', NULL, '3bf8bc62-f878-4fd9-9bee-2a6917190458', 7.00),
('bc7d814b-f4e5-4e37-a02a-d33143f66717', 'MS user cal', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-06-19', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 69750.00, 74632.50, 63585.00, 68035.95, 6165.00, 8.84, 69750.00, 63585.00, 6165.00, '0d4e8645-ff06-4531-bc5a-09e6570248d8', '2025-06-25 07:10:34', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-10-11 08:00:04', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '45e92af5-138c-44de-9a2f-c6fd2e56427e', 7.00),
('bc89294a-414c-469f-ba0d-26aaa1ba1ae7', 'Magnetic Stripe', '2024-09-01', '2024-11-19', 'ชนะ (Win)', '', '', '2024-09-01', '3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 7401869.16, 7920000.00, 6282106.54, 6721854.00, 1119762.62, 15.13, 7401869.16, 6282106.54, 1119762.62, 'f5489b6a-fd5b-4896-b655-761768e44b8f', '2024-11-11 08:59:40', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-10-11 08:00:04', NULL, '3bf8bc62-f878-4fd9-9bee-2a6917190458', 7.00),
('bcacb043-c719-47b0-8033-4bd80cabcff6', 'โครงการพัฒนาระบบ Queue Management (One Queue) + ท่อลม', '2025-10-20', '2026-03-20', 'ชนะ (Win)', '', '', '2025-10-20', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '1', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2025-10-20 02:19:15', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:19:15', NULL, '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('bcde4847-acf8-4fb7-8d6b-e9ff9f5e7a62', 'งานเช่า Server อบจ.ชลบุรี ครั้งที่ 2', '2025-10-09', '2026-09-30', 'ชนะ (Win)', '1/2569', '', '2025-10-09', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 448598.13, 480000.00, 393800.00, 421366.00, 54798.13, 12.22, 448598.13, 393800.00, 54798.13, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2025-10-27 08:01:37', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-27 08:02:20', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('bdb816d7-49d1-4fae-881f-f6ac087c1bdc', 'MA e-Library กนอ.', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', 'เช่าใช้ Cloud 1 ปี', '2024-09-02', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 84000.00, 89880.00, 20954.88, 22421.72, 63045.12, 75.05, 84000.00, 20954.88, 63045.12, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:06:00', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('bded5dd9-9eff-4685-89cf-962c1953e0ea', 'Datacenter BK01 -บางพลี-', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', 'End user : Mr. CHAWAPAT PRASERTTONGSUK  chawapatp@wtpthailand.com, www.wtpartnership.com\r\nProject lead : ดิว Axis\r\nDistributor : Ying Bacom', '0000-00-00', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', 'de3fc0f5-9ebf-4c47-88dd-da5a570653ae', 6875335.00, 7356608.45, 5718540.00, 6118837.80, 1156795.00, 16.83, 687533.50, 571854.00, 115679.50, '350429f1-d84a-4cec-8c28-d1a2ce9c4763', '2025-03-17 03:26:27', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-10-11 08:00:04', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('c1eeffcd-ceca-46dc-9b09-dae4d6d00091', 'งานติดตั้งสายสัญญาณคอมพิวเตอร์ และย้ายห้องควบคุมระบบคอมพิวเตอร์', '2024-07-01', '2024-12-28', 'ชนะ (Win)', '105/2567', '', '2024-07-01', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 4635514.02, 4960000.00, 2921450.00, 3125951.50, 1714064.02, 36.98, 4635514.02, 2921450.00, 1714064.02, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2024-11-04 03:42:21', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2025-10-27 05:25:16', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('c25a8b9b-64ad-4b8a-b79a-79e97922eb40', 'เช่าใช้ชุดเฝ้าระวังเหตุฉุกเฉินการล้มในผู้สูงอายุภายในบ้านและภายนอกบ้าน อบจ.ชลบุรี 11 อำเภอ', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2025-02-03', '3', '1', 6016600.00, 6437762.00, 4730728.30, 5061879.28, 1285871.70, 21.37, 0.00, 0.00, 0.00, '02e18007-e4e7-4fb7-a2c2-c924ece0a966', '2025-04-09 06:03:50', '3', '2025-10-27 05:58:15', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('c30ec464-061b-40f2-ae7c-06d6aced3219', 'BSP Hayashi Telempu_Preventive Maintenance Rack Switch Network', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-07-18', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 74000.00, 79180.00, 3200.00, 3424.00, 70800.00, 95.68, 74000.00, 3200.00, 70800.00, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-07-21 08:50:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('c391eba0-07b0-4ce8-9450-31a9c3c0ea4f', 'โครงการงานก่อสร้างถังเก็บน้ำใส ขนาด 20,000 ลบ.ม.ที่สถานีสูบน้ำสําโรง ค่าแรง', '2024-03-23', '2024-11-29', 'ชนะ (Win)', '', '', '2024-02-13', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 197149.53, 210950.00, 122000.00, 130540.00, 75149.53, 38.12, 197149.53, 122000.00, 75149.53, NULL, '2025-06-11 12:13:41', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('c3cda5cd-242f-4a35-8364-1304577a7d28', 'อุปกรณ์ต่อพ่วง', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '0000-00-00', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '28534929-e527-4755-bd37-0acdd51b7b45', 2803738.32, 3000000.00, 2056074.77, 2200000.00, 747663.55, 26.67, 280373.83, 205607.48, 74766.36, 'cbf32bae-0896-4e5b-ab8e-f4fdca7916f8', '2024-11-25 01:19:17', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('c49df822-5bb0-4f5e-84e0-4c3f38d8b6f3', 'Access Control', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-04-09', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 495234.58, 529901.00, 442030.00, 472972.10, 53204.58, 10.74, 495234.58, 442030.00, 53204.58, '9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', '2025-06-12 10:36:15', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('c4be4e29-07c2-49b4-8495-a0b2c1e032e3', 'พัฒนาระบบระบบตรวจสอบมาตรฐานสุกรส่วนกลาง แบบ Cloud Web Application', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', 'นำเสนอโครงการใหม่อีกครั้ง', '2025-06-13', '3', '1', 1831520.00, 1959726.40, 1000000.00, 1070000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'e126cf96-c67d-413b-888c-b81dc86ee9b8', '2025-06-15 06:32:57', '3', '2025-10-11 08:00:04', NULL, '54b6a0a0-54c2-448c-a340-71d12acdc5f6', 7.00),
('c71885a0-6f7d-46c5-8131-21adbd5eca1f', 'GFCA HQ MA Wireless Cisco9115AX AP 1Y1M_2025_8x5NBD_END_31-Aug-2026', '0000-00-00', '2026-08-31', 'ชนะ (Win)', '', '', '2025-06-12', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 43500.00, 46545.00, 31500.00, 33705.00, 12000.00, 27.59, 43500.00, 31500.00, 12000.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-06-24 07:08:02', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('c76bdba6-c78d-4071-8371-bde13f3a3c67', 'SC Polymer Solar - PO-2024082000002', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024082000002', '', '2024-08-20', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 294170.00, 314761.90, 270514.12, 289450.11, 23655.88, 8.04, 294170.00, 270514.12, 23655.88, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 09:53:02', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('c87d5dd6-8a5a-4567-aa86-f0dab93842d6', 'AutoX Tunjai PT 1907U + 39U (ปีที่ 3/5)', '2025-01-01', '2025-12-31', 'ชนะ (Win)', '-', 'MA Laser Multifunction Printer  (5Y) (31/8/2022-18/7/2028)', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 2863962.72, 3064440.11, 80000.00, 85600.00, 2783962.72, 97.21, 2863962.72, 80000.00, 2783962.72, 'edb5c314-2962-4d20-95e1-59d58f732a6d', '2025-06-16 02:58:16', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('c9364f56-fb9e-45c6-ae71-d4b21bb10d0a', 'GHB Per-call Passbook PSI', '2025-01-01', '2025-12-31', 'ชนะ (Win)', '-', '', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 150000.00, 160500.00, 35000.00, 37450.00, 115000.00, 76.67, 150000.00, 35000.00, 115000.00, '5db003f0-4196-451c-afda-e22e4481fefd', '2025-06-16 02:08:08', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('c9607961-6240-4066-965f-5a171dcee526', 'MA BAAC PromptPay ปีที่ 5', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', 'ปีสุดท้าย', '2024-09-10', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 116000.00, 124120.00, 0.00, 0.00, 116000.00, 100.00, 116000.00, 0.00, 116000.00, 'f313a7ba-64ae-4d61-af99-f493a98039b2', '2024-10-31 21:57:08', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('cb09510a-f411-4ec9-9ac5-c8a690b2be98', 'จัดซื้อสาย Lan', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-04-24', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 20100.00, 21507.00, 16095.00, 17221.65, 4005.00, 19.93, 20100.00, 16095.00, 4005.00, '9610604a-2d45-4b3f-9c93-70791dc4f0ad', '2025-06-12 10:24:39', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('cb64445d-023f-4677-a63e-3a94d7a3a0bb', 'เคลมประกัน KTB สาขาบ้านดุง', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '25-5-000-001981', 'SN 09068242C มดเข้าเครื่อง Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 5000.00, 5350.00, 2345.00, 2509.15, 2655.00, 53.10, 5000.00, 2345.00, 2655.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 03:32:44', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('cc40765f-12c3-4ca4-bfa5-7c2ed315345a', 'Ugreen', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-06-27', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 6270.00, 6708.90, 5265.00, 5633.55, 0.00, 0.00, 627.00, 526.50, 100.50, '2d4d1aec-e4e4-4836-9308-4c2c19da05cb', '2025-06-27 07:43:47', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('cce12004-7e3d-4a8e-aa44-6e2a07bb9a57', 'BSP NIDEC Wireless Access Point Configuration 9 Sets', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '3 Sets + 6 Sets', '2025-03-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 39000.00, 41730.00, 1.00, 1.07, 38999.00, 100.00, 39000.00, 1.00, 38999.00, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-03-06 04:42:45', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('cd66bfe3-0067-475b-b745-b35d2da71455', 'Thai-Otsuka DR Server Deployment', '2025-01-01', '2025-01-31', 'ชนะ (Win)', '', 'Recheck DR Server and Replication Policy\r\nHardware Preparation IP, Hostname, DNS, Raid Configuration\r\nDeployment vSphere esxi 6.0/6.5/6.7\r\nDeployment vSphere vCenter VCSA 6.0/6.5/6.7\r\nCreate new Virtual Machine Guest for Veeam Backup and Replication\r\nConfiguration Veeam Replicate for SAP and FS\r\nVerify Veeam Replicate job', '2025-01-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 20000.00, 21400.00, 0.93, 1.00, 19999.07, 100.00, 20000.00, 0.93, 19999.07, 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', '2025-01-06 09:43:11', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('cdac6856-358d-4079-b131-f1ca090cc858', 'ซื้อระบบ CCTV เฟส 3', '2025-07-09', '0000-00-00', 'ชนะ (Win)', '', '', '2025-07-09', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 1012786.00, 1083681.02, 832430.00, 890700.10, 180356.00, 17.81, 1012786.00, 832430.00, 180356.00, '9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', '2025-07-17 01:12:41', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('cf4ae75b-1fe6-4e4f-b96d-d49fefb04ffd', 'โครงการปรับปรุงและเพิ่มประสิทธิภาพระบบเฝ้าระวังภัยด้วยกล้องโทรทัศน์วงจรปิดในพื้นที่ชุมชนเมืองพัทยา PH2', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '2025-01-13', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 12052336.45, 12896000.00, 9300000.00, 9951000.00, 2752336.45, 22.84, 6026168.22, 4650000.00, 1376168.22, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-27 08:18:43', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-11 08:00:04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('cf907b6e-e0d5-4aa6-b2b6-006e2cc90a94', 'BSP NEC MA Service Onsite support 8x5 NBD (1Year) 2025', '2025-02-01', '2026-01-31', 'ชนะ (Win)', 'SE20241203-001', 'PO: PO2025005', '2025-01-15', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 98000.00, 104860.00, 0.93, 1.00, 97999.07, 100.00, 98000.00, 0.93, 97999.07, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-01-15 07:37:10', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('d0bf3137-5d61-4c2c-b04a-77d1ae6c65a0', 'เคลมประกัน KTB สาขาประทาย', '2567-07-27', '2572-07-27', 'ชนะ (Win)', '25-5-000-001938', 'SN 0881424C มดและจิ้งจกเข้าเครื่อง Power Supply ซ๊อต', '2025-06-11', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 5000.00, 5350.00, 3855.00, 4124.85, 1145.00, 22.90, 5000.00, 3855.00, 1145.00, 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', '2025-06-11 03:27:35', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('d0c02f40-fc72-4219-b7dc-b6c77f6a8d5a', 'โครงการปรับปรุงและเพิ่มประสิทธิภาพระบบกล้องโทรทัศน์วงจรปิดภายในอาคารของหน่วยงานสังกัดเมืองพัทยา', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '0000-00-00', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 13974766.36, 14953000.00, 11900000.00, 12733000.00, 2074766.36, 14.85, 6987383.18, 5950000.00, 1037383.18, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-27 08:19:42', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-11 08:00:04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('d0ceb94c-4911-4b9b-bebc-2a492504d616', 'โครงการเพิ่มศักยภาพความปลอดภัยภายในชุมชนตั้งแต่สามแยกนาในตลอดสาย', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '0000-00-00', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 10542056.07, 11280000.00, 6700000.00, 7169000.00, 3842056.07, 36.45, 5271028.04, 3350000.00, 1921028.04, 'ad0ee715-c3b7-4df0-b097-6d0bf1565fb0', '2025-06-11 09:03:06', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('d111b3e8-470a-4ac8-9257-618c22a022b6', 'Commisiioning', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '0000-00-00', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 200000.00, 214000.00, 165000.00, 176550.00, 35000.00, 17.50, 200000.00, 165000.00, 35000.00, '9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', '2025-06-12 10:40:35', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('d122993a-93b9-443d-a6b8-226878d0b5e4', 'Smart Card Reader 2000 Unit', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', '', '2025-06-11', '3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 2000000.00, 2140000.00, 1600000.00, 1712000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'f5489b6a-fd5b-4896-b655-761768e44b8f', '2025-06-11 08:02:49', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-10-11 08:00:04', NULL, '8f1ce116-f010-4b04-b112-d4de66204eef', 7.00),
('d15b7906-d0c9-4f4e-be2d-53a1484ea943', 'โครงการประกวดราคาซื้อชุดเครื่องคอมพิวเตอร์แบบพกพาพร้อมระบบปฏิบัติการและรถเข็น  จำนวน 70 เครื่อง', '2025-06-12', '2027-10-14', 'ชนะ (Win)', '', '', '2025-06-12', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2', 3383177.57, 3620000.00, 2914703.00, 3118732.21, 468474.57, 13.85, 3383177.57, 2914703.00, 468474.57, '9610604a-2d45-4b3f-9c93-70791dc4f0ad', '2025-06-12 09:37:14', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2025-10-11 08:00:04', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('d1765bcd-968f-4203-bc56-a62447106389', 'SC Polymer Solar - PO-2024071700001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024071700001', '', '2024-07-17', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 172335.00, 184398.45, 158290.50, 169370.84, 14044.50, 8.15, 172335.00, 158290.50, 14044.50, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-02 07:05:33', '5', '2025-10-11 08:00:04', '5', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00);
INSERT INTO `projects` (`project_id`, `project_name`, `start_date`, `end_date`, `status`, `contract_no`, `remark`, `sales_date`, `seller`, `team_id`, `sale_no_vat`, `sale_vat`, `cost_no_vat`, `cost_vat`, `gross_profit`, `potential`, `es_sale_no_vat`, `es_cost_no_vat`, `es_gp_no_vat`, `customer_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `product_id`, `vat`) VALUES
('d2a7935d-2f11-40f2-9c1c-89088ee9e180', 'GFCA GeoTrust DV SSL Wildcard *.gfca.com', '2025-01-13', '2026-01-13', 'ชนะ (Win)', 'SE20250113-001', '', '2025-01-13', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 13500.00, 14445.00, 12000.00, 12840.00, 1500.00, 11.11, 13500.00, 12000.00, 1500.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-01-13 05:03:48', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('d351ce78-b7c0-4d0f-8ed3-d47104931534', 'เครื่องลงเวลาด้วยใบหน้าระยะไกล Zkteco MB40 VL', '0000-00-00', '0000-00-00', 'แพ้ (Loss)', '', '', '2024-09-24', '3', '1', 92000.00, 98440.00, 57009.35, 61000.00, 34990.65, 38.03, 0.00, 0.00, 0.00, 'cdd15d78-73d7-41d6-9fad-dfd0da61a1a9', '2024-12-02 13:40:51', '3', '2025-10-25 15:09:18', '3', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('d3ff938a-19c4-45ad-96fb-6ad1ce489c1b', 'Preventive Maintenance (PM) and corrective support for CCTV and Access Control', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '0000-00-00', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 251937.00, 269572.59, 208102.00, 222669.14, 0.00, 0.00, 25193.70, 20810.20, 4383.50, '67bda6e1-da1b-41c2-8658-f11662f15f6c', '2025-07-17 01:18:22', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('d466792e-3411-4a51-83ca-287e100c3108', 'MA DLD LIMs', '2025-03-14', '2025-05-31', 'ชนะ (Win)', '', 'MA อุปกรณ์โครงการ LIMs DLD', '2025-03-03', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 150025.00, 160526.75, 71020.00, 75991.40, 79005.00, 52.66, 150025.00, 71020.00, 79005.00, '642afc1e-c8d5-42f3-a685-aa899e78be1e', '2025-03-13 09:46:30', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('d5da6d89-8b1d-4f26-9304-f826c769e800', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central Rama II (ครั้งที่ 6)', '2025-09-11', '2025-09-17', 'ชนะ (Win)', '', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central Rama II (ครั้งที่ 6)', '2025-09-10', '3', '1', 55300.00, 59171.00, 22220.00, 23775.40, 33080.00, 59.82, 55300.00, 22220.00, 33080.00, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-10-27 06:27:55', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:27:55', NULL, 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('d5e371bb-41e9-4a42-aa6f-9dcd2923ab7b', 'BSP Nidec Techno Motor AP Upgrade firmware to 17.12.5_Existing 25APs', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-06-09', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 85000.00, 90950.00, 0.00, 0.00, 85000.00, 100.00, 85000.00, 0.00, 85000.00, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-06-09 04:17:06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('d908bdde-8ddb-4a37-ad87-63be6b05bf58', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central ลาดพร้าว (ครั้งที่ 1)', '2025-03-13', '2025-03-19', 'ชนะ (Win)', '', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central ลาดพร้าว (ครั้งที่ 1)', '2025-03-13', '3', '1', 49500.00, 52965.00, 20000.00, 21400.00, 29500.00, 59.60, 49500.00, 20000.00, 29500.00, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-10-27 06:20:07', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:20:07', NULL, 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('dab73e5c-8b8f-4e9c-82fc-8976ba6ad34d', 'WTC MSRW 112U (ปีที่ 2/5)', '2025-01-01', '2025-12-31', 'ชนะ (Win)', 'PO1067080181 Date 16/8/2024', 'MA Encode Passbook LKE 4777 USB Interface (สีดำ) 112U Carry in (5Y) (', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 13440.00, 14380.80, 1000.00, 1070.00, 12440.00, 92.56, 13440.00, 1000.00, 12440.00, 'bcdad84b-ec95-4a80-8765-7f14d2c0a764', '2025-06-16 03:18:29', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('dba0885b-4710-488d-910e-e32d747e8cc0', 'MA LIMS DLD 4 Months', '2025-06-01', '2025-09-30', 'ชนะ (Win)', '', 'MA Server', '2025-06-01', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 238000.00, 254660.00, 95150.00, 101810.50, 142850.00, 60.02, 238000.00, 95150.00, 142850.00, '642afc1e-c8d5-42f3-a685-aa899e78be1e', '2025-06-10 09:07:22', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('dbe7c117-97cb-4b00-a592-a9c8909b5b53', 'SC Polymer Solar - PO-2024071200001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024071200001', '', '2024-07-12', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 232727.00, 249017.89, 213979.88, 228958.47, 18747.12, 8.06, 232727.00, 213979.88, 18747.12, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-04 02:35:10', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('dca0d055-fbcd-48d1-9675-dfab5ebe1443', 'เหมาติดตั้งระบบอินเตอร์เน็ตพร้อมอุปกรณ์ภายในสำนักงานใหม่', '2025-04-25', '2025-05-26', 'ชนะ (Win)', '23/2568', '', '2025-04-10', '70dd36b5-f587-4aa9-b544-c69542616d34', '2', 384299.07, 411200.00, 244738.00, 261869.66, 139561.07, 36.32, 384299.07, 244738.00, 139561.07, NULL, '2025-06-11 09:39:52', '70dd36b5-f587-4aa9-b544-c69542616d34', '2025-10-11 08:00:04', '70dd36b5-f587-4aa9-b544-c69542616d34', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('dcbfadc3-88d7-4064-88f0-623819025571', 'BYGGE Per-call บริการ Onsite', '2025-01-01', '2025-05-31', 'ชนะ (Win)', 'QT-000001308', '', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 84400.00, 90308.00, 40000.00, 42800.00, 44400.00, 52.61, 84400.00, 40000.00, 44400.00, '1be1add7-4691-4a2b-a2db-5b849cdc5cfe', '2025-06-16 04:30:26', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', NULL, '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('dcf6ae9b-fbb0-48e1-b625-a8b110dabfc3', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central Park (ครั้งที่ 11)', '2025-11-24', '2025-11-28', 'ชนะ (Win)', '', '', '2025-11-03', '3', '1', 47000.00, 50290.00, 20000.00, 21400.00, 27000.00, 57.45, 47000.00, 20000.00, 27000.00, NULL, '2025-11-05 06:08:38', '3', '2025-11-05 06:08:38', NULL, 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('dcfdaa4d-da79-41b7-98b0-448433406dc3', 'Barcode 1500 Unit', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', '', '2025-06-11', '3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 2200000.00, 2354000.00, 2000000.00, 2140000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'f5489b6a-fd5b-4896-b655-761768e44b8f', '2025-06-11 08:06:15', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-10-11 08:00:04', NULL, 'a46f567c-b5bf-4c7c-843c-f59975388a59', 7.00),
('dde630d3-d7cb-4c2b-a017-04a020f2a422', 'โครงการพัฒนาศักยภาพระบบเครือข่ายคอมพิวเตอร์ อบจ.ชลบุรี', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '0000-00-00', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 4579439.25, 4900000.00, 3700000.00, 3959000.00, 879439.25, 19.20, 2289719.63, 1850000.00, 439719.63, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2025-10-27 08:11:25', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-27 08:11:25', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('de838089-b8cd-4964-a05d-c49c19422cb1', 'งานจ้างเหมาเดินสายระบบไฟฟ้าเข้าเครื่องสำรองไฟ', '2024-08-20', '2024-11-18', 'ชนะ (Win)', '203/2567', '', '2024-08-20', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 38258.36, 40936.44, 26762.00, 28635.34, 11496.36, 30.05, 38258.36, 26762.00, 11496.36, 'd4efc031-32d4-487f-87ff-69afe9f948e4', '2024-11-06 05:00:33', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2025-10-27 05:31:28', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('de8c069d-6370-49de-aafc-20b6cde1025b', 'Samsung LaserJet Toner SL-4020ND  (SU894A) - 16 box', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'IV671106003', '', '2024-11-06', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 38130.84, 40800.00, 29515.20, 31581.26, 8615.64, 22.59, 38130.84, 29515.20, 8615.64, 'df6e7ebd-77f2-49e4-bcdf-04c71608005f', '2024-12-09 01:47:45', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '19747bf2-8f2d-47db-a2e8-4fca20843812', 7.00),
('e0bd9cd0-e670-49cc-828e-193908f5a692', 'MA Auto Update Passbook พร้อมติดตั้ง จำนวน 1 เครื่อง (ปีที่ 1/5)', '2025-03-27', '2025-12-31', 'ชนะ (Win)', 'สอ.กผ.003/2568', 'สัญญาซื้อขายเครื่องปรับสมุดเงินฝากอัตโนมัติพร้อมเครื่องคอมพิวเตอร์ PC+OS (5Y (27/03/2025 - 26/03/2030)', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 95219.77, 101885.15, 70000.00, 74900.00, 25219.77, 26.49, 95219.77, 70000.00, 25219.77, '4baf1507-337e-43f7-8d21-fbc184d876ac', '2025-06-16 06:28:43', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '0e1e8969-0d80-4d96-9571-c7d650945a77', 7.00),
('e261d4de-a628-45bd-b28e-7ae8a18a9b66', 'SC Polymer Solar - PO-2024102400002', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024102400002', '', '2024-10-24', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 66960.00, 71647.20, 61600.00, 65912.00, 5360.00, 8.00, 66960.00, 61600.00, 5360.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 10:19:39', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('e33b70d6-0017-4742-9751-fe355d76e392', 'โครงการเพิ่มประสิทธิภาพระบบให้บริการสัญญาณภาพแบบ OnLine เมืองพัทยา', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-06-10', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 1934579.44, 2070000.00, 1182000.00, 1264740.00, 752579.44, 38.90, 1934579.44, 1182000.00, 752579.44, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2025-06-11 08:57:32', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-27 04:26:57', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('e33fc204-3396-4c06-807d-2512218b8fc2', 'MA e-Courtroom ประจำปี 68', '2024-12-01', '2025-09-30', 'แพ้ (Loss)', '', 'แพ้เนื่องจากเจ้าที่เสนอมี SME', '2024-11-13', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 709345.79, 759000.00, 650000.00, 695500.00, 59345.79, 8.37, 0.00, 0.00, 0.00, NULL, '2024-11-11 08:32:52', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2025-10-11 08:00:04', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('e4b1a5aa-dba3-40dd-8ccb-ddf06547fa9e', '3in1 Pinpad  800 Set', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2025-06-11', '3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 4000000.00, 4280000.00, 3000000.00, 3210000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'f5489b6a-fd5b-4896-b655-761768e44b8f', '2025-06-11 07:55:36', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-10-11 08:00:04', NULL, 'abf31336-8385-4be6-9a6c-587719a5e0df', 7.00),
('e54e5686-6be2-44f0-b7a1-3689614b3244', 'ค่า Toner (Project ทันใจ)', '2024-01-01', '2024-11-11', 'ชนะ (Win)', '', '', '2024-01-01', '3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 17971028.04, 19229000.00, 16028037.38, 17150000.00, 1942990.66, 10.81, 17971028.04, 16028037.38, 1942990.66, '6b3ba15b-ee6d-41ab-a543-d345e9f62259', '2024-11-11 08:51:04', '3140fdaf-5103-4423-bf87-11b7c1153416', '2025-10-11 08:00:04', NULL, '19747bf2-8f2d-47db-a2e8-4fca20843812', 7.00),
('e565cdde-1422-4d12-8127-a067e5b01fe5', 'Universal Rice Network Improvement (URC)', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-01-31', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 293000.00, 313510.00, 0.93, 1.00, 292999.07, 100.00, 29300.00, 0.09, 29299.91, '895e71fc-991e-4b42-9803-4bcafdb03023', '2025-01-31 06:23:03', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('e5ff0b66-f1db-4f84-befe-73d06829d4ec', 'MA OBEC Mail', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 228000.00, 243960.00, 0.00, 0.00, 227999.07, 100.00, 228000.00, 0.00, 228000.00, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:11:16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('e6184225-dba7-4cc7-90d5-af9584109353', 'โครงการติดตั้งกล้องโทรทัศน์วงจรปิด', '0000-00-00', '0000-00-00', 'แพ้ (Loss)', '', '', '0000-00-00', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2', 3247312.75, 3474624.64, 2862525.00, 3062901.75, 384787.75, 11.85, 0.00, 0.00, 0.00, 'fc372e65-cca3-4c7c-b580-c689ef2d0798', '2024-12-03 02:43:51', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2025-10-11 08:00:04', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '581f6ca7-8e1e-447a-9dae-680755c4fd29', 7.00),
('e718b8d8-ea0c-4e36-821c-14e1da8fa258', 'ระบบแลกบัตรเข้าออกอาคาร', '0000-00-00', '0000-00-00', 'ยกเลิก (Cancled)', '', 'รอคุยเรื่องผู้เสนอต้องเป็น SME หรือเปล่า', '0000-00-00', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 461682.24, 494000.00, 383177.57, 410000.00, 78504.67, 17.00, 0.00, 0.00, 0.00, '360a7a11-6bcd-4301-8156-b4d11ebd6794', '2024-11-28 08:50:22', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2025-10-11 08:00:04', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('e75996eb-c5ba-4dfe-b36e-5e58ba334bb6', 'Mindss Thai-Otsuka Onsite Recheck Server VMware pink screen', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-01-31', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 5000.00, 5350.00, 0.93, 1.00, 4999.07, 99.98, 5000.00, 0.93, 4999.07, 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', '2025-01-31 06:17:12', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('e7c861a2-b027-4992-a280-c8dc6a180784', 'MA BAAC ICAS ปีที่ 4', '2024-03-01', '2025-02-28', 'ชนะ (Win)', '', '', '2024-11-01', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 50000.00, 53500.00, 0.00, 0.00, 50000.00, 100.00, 50000.00, 0.00, 50000.00, 'f313a7ba-64ae-4d61-af99-f493a98039b2', '2024-10-31 22:00:44', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('e90e5ea9-c4b9-4657-a0e7-0ce63daf791e', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) Central Westgate (ครั้งที่ 3)', '2025-06-04', '2025-06-11', 'ชนะ (Win)', 'QT0000001321', '', '2025-06-04', '3', '1', 55300.00, 59171.00, 23183.33, 24806.16, 32116.67, 58.08, 55300.00, 23183.33, 32116.67, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-06-11 12:04:27', '3', '2025-10-27 06:22:47', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('e96e8f2a-5a2c-48fc-bae1-d19c30217990', 'BSP NEC vSphere and vCenter upgrade to 8.0 Installation Service (Non-Business Day)', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', 'SE20241203-002', 'NEC vSphere and vCenter upgrade to 8.0 Installation Service (Non-Business Day)', '2025-01-15', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 58000.00, 62060.00, 0.93, 1.00, 57999.07, 100.00, 5800.00, 0.09, 5799.91, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-01-15 07:40:07', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('e99893be-bbf3-43f3-b577-5fd07e381157', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) งานบ้านและสวน ไบเทคบางนา (ครั้ง 4)', '2025-08-01', '2025-08-10', 'ชนะ (Win)', '', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth) งานบ้านและสวน ไบเทคบางนา (ครั้ง 4)', '2025-07-30', '3', '1', 67150.00, 71850.50, 23040.00, 24652.80, 44110.00, 65.69, 67150.00, 23040.00, 44110.00, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-10-27 06:24:36', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 06:24:36', NULL, 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'โครงการจ้างระบบแพลตฟอร์มวิเคราะห์ข้อมูล ปัญญาประดิษฐ์ในการบริการดูแลการใช้ชีวิตและดูแลสุขภาพระยะยาวสำหรับผู้สูงอายุและผู้ที่มีภาวะพึ่งพิง', '2024-09-27', '2025-09-29', 'ชนะ (Win)', '๖/๒๕๖๗', '', '2024-09-06', '3', '1', 266822.43, 285500.00, 198336.00, 212219.52, 68486.43, 25.67, 266822.43, 198336.00, 68486.43, '5e2a838a-110f-48bc-9518-f01a7066955b', '2024-10-15 21:17:03', '3', '2025-10-11 08:00:04', '3', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('eab3d4f8-1ab7-4654-b1c3-d1ddce015b5b', 'MA DLD Datalake', '2024-11-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-10-01', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 816400.00, 873548.00, 278320.00, 297802.40, 538080.00, 65.91, 816400.00, 278320.00, 538080.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 22:04:08', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('ebb8ead8-1089-426c-9525-9a352b756974', 'GFCA Project Wireless Access Point Cisco Catalyst 9115AX MA 1Y5M_8x5NBD_AAI-สมุทรสงคราม', '2025-04-19', '2026-08-31', 'ชนะ (Win)', '', '', '2025-02-28', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 13000.00, 13910.00, 7760.00, 8303.20, 5240.00, 40.31, 13000.00, 7760.00, 5240.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-02-28 08:44:58', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('ec3b8fe6-72ec-44d3-92b3-40f8ee0bee87', 'โครงการเช่าใช้บริการระบบบริการสุขภาพอัจฉริยะ (Smart Healthcare) สำหรับดูแลและส่งเสริมสุขภาพประชาชนในเขตเทศบาลตำบลด่านสำโรง', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', 'เทศบาลตำบลด่านสำโรง\r\n545 ซอยด่านสำโรง 47 ตำบลสำโรงเหนือ อำเภอเมือง จังหวัดสมุทรปราการ 10270 \r\nโทรศัพท์ 0-2759-2770 / โทรสาร 0-2759-2554', '2025-10-16', '3', '1', 14000000.00, 14980000.00, 11000000.00, 11770000.00, 3000000.00, 21.43, 1400000.00, 1100000.00, 300000.00, NULL, '2025-10-17 06:34:06', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-25 14:58:02', '3', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('ec5bd8ff-0c1a-4da8-ae4a-ab26488b2c67', 'งานซ่อมไฟเบอร์ 9 จุด ครั้งที่ 1 สำนักยุทธ์', '2024-12-17', '2025-02-15', 'ชนะ (Win)', '92/2568', '', '2024-12-10', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 409731.78, 438413.00, 337000.00, 360590.00, 72731.78, 17.75, 409731.78, 337000.00, 72731.78, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2025-06-11 12:36:53', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('ecb183b4-4add-4215-8b4b-f7f60e544274', 'บริการเช่าใช้ระบบ AI Platform พร้อม Hardware สำหรับงาน Showroom ICONIC YOU ห้าง One Bangkok', '2025-03-14', '2025-04-15', 'ชนะ (Win)', 'QT000001262', '', '2025-02-11', '3', '1', 63200.00, 67624.00, 47100.00, 50397.00, 16100.00, 25.47, 63200.00, 47100.00, 16100.00, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-04-09 06:16:11', '3', '2025-10-11 08:00:04', '3', 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('ecf41d44-20a8-4185-bc52-deb21201033d', 'จัดซื้อครุภัณฑ์คอมพิวเตอร์ จำนวน 45 เครื่อง', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '0000-00-00', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '28534929-e527-4755-bd37-0acdd51b7b45', 925200.00, 989964.00, 841121.50, 900000.00, 84078.50, 9.09, 92520.00, 84112.15, 8407.85, '81b62776-9408-4a36-af8e-45799f86883d', '2024-11-25 01:16:37', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('ed91e3b0-e620-4d6a-b3be-df0820018bd6', 'โครงการบูรณาการระบบเฝ้าระวังความปลอดภัยในกระทรวงวัฒนธรรมด้วยเทคโนโลยี AI', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2025-04-24', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2', 18018691.59, 19280000.00, 14876000.00, 15917320.00, 0.00, 0.00, 0.00, 0.00, 0.00, '11ca34d4-27bb-48ce-915a-81996dc98f9b', '2025-06-12 09:56:12', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2025-10-11 08:00:04', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('eed702ae-86ea-45b3-8688-33fb3bda90e0', 'Firewall ฌาปนกิจสงเคราะห์ สตช.', '2025-01-17', '2026-01-18', 'ชนะ (Win)', '', 'Fortigate 100F', '2025-01-06', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 180000.00, 192600.00, 120000.00, 128400.00, 60000.00, 33.33, 180000.00, 120000.00, 60000.00, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2025-03-13 09:52:52', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('efe3f73f-3a49-4e85-a0cd-7e94d33d6231', 'โครงการเช่าใช้เครื่องปรับสมุด (PassbookPrinter) PSI PR9 จำนวน 660 เครื่อง จำนวน 5 ปี', '2025-10-02', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', 'หลังจบโครงการ เครื่องเป็นทรัพย์สินของบริษัทฯ ต้องทำการเก็บเครื่องคืน', '2025-04-18', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 34048598.13, 36432000.00, 28214468.80, 30189481.62, 0.00, 0.00, 0.00, 0.00, 0.00, '5aece05b-9c59-4c41-b12f-8ceb8f25fd63', '2025-06-13 03:40:51', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2025-10-11 08:00:04', '5', 'a94ff83b-e9ff-4d21-87ed-a7849f8e710b', 7.00),
('eff46efc-b568-467a-ba38-b259647845d9', 'จัดซื้อวัสดุคอมพิวเตอร์ จำนวน 15 รายการ สำนักยุทธ์', '2025-04-29', '2025-06-28', 'ชนะ (Win)', '334/2568', '', '2025-03-12', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 261682.24, 280000.00, 211000.00, 225770.00, 50682.24, 19.37, 261682.24, 211000.00, 50682.24, NULL, '2025-06-11 11:49:10', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('f04f1583-d0bc-4d89-b868-785f3efd07de', 'โทรศัพท์พื้นฐานผ่านเครือข่าย (IP Phone) สำหรับบุคลากร จำนวน 20 เครื่อง', '2025-04-08', '2025-07-07', 'ชนะ (Win)', '93/2568', '', '2025-04-08', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 152000.00, 162640.00, 125700.00, 134499.00, 26300.00, 17.30, 152000.00, 125700.00, 26300.00, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2025-10-27 07:47:58', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2025-10-27 07:47:58', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('f3f66580-6c68-43db-a9cc-27008752b1a6', 'ค่าติดตั้งระบบ CCTV', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-02-18', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 62900.00, 67303.00, 55000.00, 58850.00, 7900.00, 12.56, 62900.00, 55000.00, 7900.00, '67bda6e1-da1b-41c2-8658-f11662f15f6c', '2025-06-12 10:31:57', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '2025-10-11 08:00:04', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('f40d86bd-b4ed-4c79-a96a-7aafe7283719', 'งานพิพิธภัณฑ์ จ.ระนอง', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', 'End user : งานพิพิธภัณฑ์ จ.ระนอง\r\nเข้างานโดย บริษัท เบคอม อินเตอร์เน็ทเวอร์ค จำกัด, Anyapat  Wannakunlapat  (Ying)', '0000-00-00', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', 'de3fc0f5-9ebf-4c47-88dd-da5a570653ae', 376547.50, 402905.83, 289007.01, 309237.50, 87540.49, 23.25, 37654.75, 28900.70, 8754.05, '9f005cab-6ce1-4813-bafe-95be81d93b1d', '2025-03-17 03:10:54', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-10-11 08:00:04', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('f4fb553a-4ecd-4a70-8459-9d8462ccfddd', 'Printer Brather', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-06-25', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 8700.00, 9309.00, 7478.00, 8001.46, 0.00, 0.00, 870.00, 747.80, 122.20, '5f32551c-7a96-4b5f-b485-2357623e9893', '2025-06-25 03:19:03', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-10-11 08:00:04', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('f50eb76c-0230-4f71-b47f-c2e60d652ce1', 'MA OBEC DataCenter', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 3080000.00, 3295600.00, 0.00, 0.00, 3080000.00, 100.00, 3080000.00, 0.00, 3080000.00, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:09:02', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-10-11 08:00:04', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('f655c0f1-25e0-4c51-bf01-4f99b4121ba7', 'ค่าเช่าชุดเฝ้าระวังเหตุฉุกเฉินการล้มในผู้สูงอายุภายในบ้านและภายนอกบ้าน ระยะเวลา 12 เดือน', '0000-00-00', '0000-00-00', 'ยกเลิก (Cancled)', '', '', '2024-10-31', '3', '1', 106800.00, 114276.00, 77570.09, 83000.00, 29229.91, 27.37, 0.00, 0.00, 0.00, '690cfd6a-0270-4b22-8d1f-de1f91dda830', '2024-12-02 13:46:02', '3', '2025-10-25 15:08:53', '3', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('f68d2b0e-6ba9-468a-bd47-870036ce545d', 'โครงการจ้างเหมาบริการเทคโนโลยีอัจฉริยะยกระดับความปลอดภัยประเทศไทยจากอาชญากรรมแก๊งคอลเซ็นเตอร์ และขบวนการค้ามนุษย์', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', 'หน่วยงานเจ้าของโครงการ : ศูนย์ปราบปรามอาชญากรรมทางเทคโนโลยีสารสนเทศ สำนักงานตำรวจแห่งชาติ (ศปอส.ตร.)\r\nมูลค่าโครงการ VAT.  188,953,430.00  บาท\r\n\r\n(สร้างโดย : พี่หญิง)', '2025-05-31', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', 'de3fc0f5-9ebf-4c47-88dd-da5a570653ae', 95300000.00, 99999999.99, 66500000.00, 71155000.00, 0.00, 0.00, 0.00, 0.00, 0.00, '0a20192c-45f2-4032-aae1-39b4861104fc', '2025-06-16 01:13:29', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-10-11 08:00:04', '5', '6e2ba9df-293d-4d88-b85e-4399e237d8c0', 7.00),
('fa1a06ff-e3b5-4243-8508-9ef70aa510a3', 'MA Auto Update Passbook พร้อมติดตั้ง จำนวน 794 เครื่อง (ปีที่ 1/5)', '2025-01-01', '2025-12-31', 'ชนะ (Win)', 'POIT66-136', 'Auto Update Passbook พร้อมติดตั้ง Hitachi BH-180AZ+PC+Frame จำนวน 794 เครื่อง (5Y) เริ่ม 27/07/2024 - 26/07/2029', '2025-01-01', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 3970000.00, 4247900.00, 2300000.00, 2461000.00, 1670000.00, 42.07, 3970000.00, 2300000.00, 1670000.00, 'd9e67c01-1640-47d0-aeaf-38b622f996de', '2025-01-06 08:38:35', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-10-11 08:00:04', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('faf652c9-8bd0-4b21-a935-30c904d8650a', 'GFCA MA IBM Server and Storage_END30Apr2027_GFCA_Site_BackupServer_2Years', '2025-05-01', '2027-04-30', 'ชนะ (Win)', '', '', '2025-03-19', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 191000.00, 204370.00, 98400.00, 105288.00, 92600.00, 48.48, 191000.00, 98400.00, 92600.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-03-19 09:51:32', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('fd4ae80c-d6fb-4e4d-be1e-300f4ebf6bba', 'Software License', '2025-06-18', '0000-00-00', 'ชนะ (Win)', 'PO25-06-T-0074', '', '2025-06-18', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 20000.00, 21400.00, 16725.00, 17895.75, 3275.00, 16.38, 20000.00, 16725.00, 3275.00, '0d4e8645-ff06-4531-bc5a-09e6570248d8', '2025-06-19 02:16:21', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', '2025-10-11 08:00:04', NULL, '2d24b1a5-6944-4536-aeff-71ee4a5a4187', 7.00),
('fdf6a82e-a32e-4c69-bfa3-a255bd1dd4cc', 'Mindss Thai-Otsuka Promote Additional Domain on Cloud', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-06-18', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 18000.00, 19260.00, 0.00, 0.00, 18000.00, 100.00, 18000.00, 0.00, 18000.00, 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', '2025-06-18 09:03:54', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-10-11 08:00:04', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('fe1985b8-1862-42f1-8ed1-7296cc63d91c', 'งานซ่อมบำรุงเครื่องคอมพิวเตอร์ อาคารประถมศึกษา โรงเรียนเมืองพัทยา 10', '2025-01-30', '2025-03-16', 'ชนะ (Win)', '137/2568', '', '2025-01-21', '193f9eed-2938-4305-ab65-828ac5253b30', '2', 465400.00, 497978.00, 186000.00, 199020.00, 279400.00, 60.03, 465400.00, 186000.00, 279400.00, NULL, '2025-06-11 10:31:38', '193f9eed-2938-4305-ab65-828ac5253b30', '2025-10-11 08:00:04', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00);

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
-- Dumping data for table `project_costs`
--

INSERT INTO `project_costs` (`cost_id`, `project_id`, `type`, `part_no`, `description`, `quantity`, `unit`, `price_per_unit`, `cost_per_unit`, `supplier`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
('03f93217-e6c0-4e96-8fd8-f18f35c40d27', 'b403c2e1-8913-4e94-b6c8-b9f7f36a4f31', 'SERVICE BANK', 'Service Bank', 'Service Bank', 1200, '๊ื๊ืUnit', 0.00, 1000.00, 'Service Bank', '2025-06-16 07:03:47', '5', '2025-06-16 07:03:47', NULL),
('13f9d3e5-aa9c-4fe7-b57f-f7e2eaa3f1d3', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'A', 'Hardware', 'ชุดเฝ้าระวัง', 30, NULL, 2850.00, 1347.00, 'Stock Point ', '2024-11-01 11:14:30', '3', '2024-11-01 11:14:30', NULL),
('1f412777-1029-4ff2-bc88-91e2c8984fe6', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', 'A', 'Hardware', 'อุปกรณ์ไมโครคอนโทรลเลอร์	', 169, 'ชุด', 5000.00, 3000.00, 'ต่างประเทศ', '2025-01-23 12:30:53', '3', '2025-01-23 12:31:53', '3'),
('34b4f516-8845-4dfa-8c44-fc3b5338a346', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', 'B', 'Software', 'License โปรแกรมไมโครคอนโทรลเลอร์	', 169, 'ชุด', 1000.00, 2000.00, 'Dev', '2025-01-23 12:32:38', '3', '2025-01-23 12:32:38', NULL),
('4d850f5e-35ef-442b-be1b-0ebc59c0f8ef', '7c67ce7e-ee05-487f-a763-4627899516bb', 'A', 'Service', 'Care Center 24*7', 1, 'คน', 5999.00, 3000.00, 'Service Teams', '2025-01-08 01:26:16', '5', '2025-01-08 01:27:14', '5'),
('50b23a92-cd1c-4cd4-a203-15426a050019', '51e62f2e-3b91-44e8-9875-55239e0e8acc', 'Software Development', 'KDM-SW-003 ', 'Kudson Moo AI Standslone Volume License. 1) Central Management Software, Support 1 Factory , 1 station, 10 Concurrent users with statistic Dashboard (Day, Month, Year) on Customer Server or Customer Private Cloud 2) AI program per a station between  2.1) Pig Classification AI Software or  2.2) Pork Belly Grading AI Software or  2.3) Pork Carcass Grading AI Software 3) Warranty 1Y- Remote /service 7 Days x 8 Hours with Hot-line Call', 10, 'license', 80000.00, 60000.00, 'Innovation Team', '2025-06-16 00:32:16', '5', '2025-06-16 00:36:59', '5'),
('6b095ff2-8002-4a7b-af58-ba64fb3870e7', 'b403c2e1-8913-4e94-b6c8-b9f7f36a4f31', 'Signature Pad', 'SSP-0001', 'Signotect Gamma', 1200, '๊ื๊ืUnit', 8800.00, 5737.00, 'Germany', '2025-06-16 07:02:08', '5', '2025-06-16 07:02:54', '5'),
('6d35285c-0bd3-4406-9ed2-e5674f8704c0', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Software', '-', 'บริหารจัดการส่วนกลาง', 1, 'License', 100000.00, 0.00, 'Innovation', '2025-01-16 11:16:47', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:16:47', NULL),
('74f0fb16-3d4f-4c40-ba67-2b0608ad70ef', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Software', '-', 'ระบบตรวจจับป้ายทะเบียน LPR', 2, 'License', 35000.00, 30000.00, 'อ.มงคล', '2025-01-16 11:15:04', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:15:04', NULL),
('7ec94428-a477-4a67-901a-099f9a739826', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'A', 'Hardware', 'Sim Internet แบบรายเดือน ระยะเวลา 12 เดือน', 31, NULL, 1366.20, 1188.00, 'AIS', '2024-11-01 11:16:14', '3', '2024-11-01 11:20:20', '3'),
('840e083f-1211-4d8e-9257-8674e29bd538', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Software', '-', 'ระบบตรวจจับใบหน้า IBOC', 2, 'License', 35000.00, 30000.00, 'IBOC', '2025-01-16 11:14:10', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:14:10', NULL),
('84863965-80f0-4ce8-aa08-79b3c876af8a', 'f655c0f1-25e0-4c51-bf01-4f99b4121ba7', 'Service', 'S01', 'Service', 12, NULL, 5000.00, 4000.00, 'Service PIT', '2024-12-03 02:55:33', '5', '2024-12-03 02:55:33', NULL),
('8ea6620f-e60a-4df6-96c1-7a617513dc4d', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Installation', '-', 'ค่าติดตั้งกล้อง', 4, 'Job', 2000.00, 0.00, 'Internal', '2025-01-16 11:18:07', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:18:07', NULL),
('a067ebdf-7454-4e12-92ac-8e6d0cd8e501', '078a1fd1-f8bb-4c94-86d5-d35fbf00d1bb', 'Service Bank', 'Service', 'ตรวจรับ และติดตั้ง', 1000, 'เครื่อง', 500.00, 500.00, 'Service Bank', '2025-06-16 06:55:05', '5', '2025-06-16 06:55:05', NULL),
('a2357f90-57be-4482-a45a-a83c5176aba4', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Hardware', '-', 'อุปกรณ์ป้องกันไม้หล่น', 2, 'ชุด', 6160.00, 5500.00, 'HIP', '2025-01-16 11:10:23', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:10:23', NULL),
('c757c157-937a-423f-86c3-7b7f612e65e2', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Hardware', '-', 'ตู้ควบคุมทางเข้าออก', 2, 'ชุด', 64500.00, 55000.00, 'HIP', '2025-01-16 11:08:52', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:09:10', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a'),
('cd45f131-b8a1-4aa6-81c7-994318efe5e6', 'efe3f73f-3a49-4e85-a0cd-7e94d33d6231', 'PR9', 'PR9', 'PR9', 660, 'Unit', 35000.00, 30000.00, 'PIT', '2025-06-16 07:36:30', '5', '2025-06-16 07:36:30', NULL),
('d4d45c0b-b3d4-444e-b7b9-679779e4405b', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Hardware', '-', 'ควบคุมระบบไม้กั้น', 1, 'ชุด', 11550.00, 10000.00, 'HIP', '2025-01-16 11:11:34', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:11:34', NULL),
('e4aaaf8d-2d64-4f8f-83a3-98e8aa846a9e', '51e62f2e-3b91-44e8-9875-55239e0e8acc', 'Software Service', '7x8 SW-Call & Remote Service 1Y', 'Warranty 1Y- Remote /service 7 Days x 8 Hours with Hot-line Call. ', 10, 'Jobs', 0.00, 12000.00, 'Innovation Service', '2025-06-16 00:35:04', '5', '2025-06-16 00:36:25', '5'),
('e975cd23-9e02-4087-8b67-461314a7fe84', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Hardware', '-', 'กล้อง', 4, 'ตัว', 16900.00, 15000.00, 'Hikvision', '2025-01-16 11:12:43', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:12:43', NULL);

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

--
-- Dumping data for table `project_cost_summary`
--

INSERT INTO `project_cost_summary` (`project_id`, `total_amount`, `vat_amount`, `grand_total`, `total_cost`, `cost_vat_amount`, `total_cost_with_vat`, `profit_amount`, `profit_percentage`, `updated_at`) VALUES
('078a1fd1-f8bb-4c94-86d5-d35fbf00d1bb', 500000.00, 35000.00, 535000.00, 500000.00, 35000.00, 535000.00, 0.00, 0.00, '2025-06-16 06:55:05'),
('51e62f2e-3b91-44e8-9875-55239e0e8acc', 800000.00, 56000.00, 856000.00, 720000.00, 50400.00, 770400.00, 85600.00, 10.00, '2025-06-16 00:36:59'),
('7c67ce7e-ee05-487f-a763-4627899516bb', 5999.00, 419.93, 6418.93, 3000.00, 210.00, 3210.00, 3208.93, 49.99, '2025-01-08 01:27:15'),
('8c11b5f4-daf0-4a3b-829d-f856081e9c97', 468470.00, 32792.90, 501262.90, 311000.00, 21770.00, 332770.00, 168492.90, 33.61, '2025-01-16 11:18:07'),
('ad3b9787-d382-4eb0-ac96-d9a3917d5db6', 1014000.00, 70980.00, 1084980.00, 845000.00, 59150.00, 904150.00, 180830.00, 16.67, '2025-01-23 12:32:38'),
('b403c2e1-8913-4e94-b6c8-b9f7f36a4f31', 10560000.00, 739200.00, 11299200.00, 8084400.00, 565908.00, 8650308.00, 2648892.00, 23.44, '2025-06-16 07:03:47'),
('ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 127852.20, 8949.65, 136801.85, 77238.00, 5406.66, 82644.66, 54157.19, 39.59, '2024-11-01 11:20:20'),
('efe3f73f-3a49-4e85-a0cd-7e94d33d6231', 23100000.00, 1617000.00, 24717000.00, 19800000.00, 1386000.00, 21186000.00, 3531000.00, 14.29, '2025-06-16 07:36:30'),
('f50eb76c-0230-4f71-b47f-c2e60d652ce1', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-12-02 07:19:08'),
('f655c0f1-25e0-4c51-bf01-4f99b4121ba7', 60000.00, 4200.00, 64200.00, 48000.00, 3360.00, 51360.00, 12840.00, 20.00, '2024-12-03 02:55:33');

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

--
-- Dumping data for table `project_customers`
--

INSERT INTO `project_customers` (`id`, `project_id`, `customer_id`, `is_primary`, `created_at`) VALUES
('01a3a58f-590e-4be3-b747-14f7c51ebfc4', 'c25a8b9b-64ad-4b8a-b79a-79e97922eb40', '02e18007-e4e7-4fb7-a2c2-c924ece0a966', 1, '2025-10-27 05:58:15'),
('024857e3-6925-4398-a38a-208d39877bbc', '96f5ad4d-3a2d-4f3d-a909-9c74eaf3df55', 'f004cbe4-f666-4de7-8e85-7f940b6d8393', 1, '2025-06-10 09:27:09'),
('0251e854-93ed-4a7b-ae27-7258195c97dc', '38fae358-df4d-41f2-8970-cb2937222dd5', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-10-27 06:31:38'),
('0275b7c4-8f02-4994-b32c-c350616d4bcf', '6d273c81-2bb0-4628-8954-b10bcccbfdd1', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:02:23'),
('064232f7-4d8f-44b5-a69d-4d7ef4c8f965', 'a5addc9d-b9be-41a0-b9ae-aae652e47826', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-02-06 09:30:57'),
('07442d3e-ca5d-4b00-830b-3400cd158701', '26b7618c-cba9-47bd-a7f5-026e193dd543', 'fb683856-9635-4316-ad3a-2eb57d6eb10f', 1, '2025-10-25 15:02:42'),
('0767e2b1-7426-4c7c-b5a5-87177e41bda8', 'bded5dd9-9eff-4685-89cf-962c1953e0ea', '350429f1-d84a-4cec-8c28-d1a2ce9c4763', 1, '2025-03-17 03:26:27'),
('08ef3999-01c2-45d5-8435-712d0d26c41f', '6f498e8e-0bc7-45e0-9f5b-117dbdc84c90', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:06:01'),
('08f38e47-7ecd-4526-a17c-444bc09dfe56', '3b2460f5-5fec-4dcd-8f3b-eed28da77728', '67bda6e1-da1b-41c2-8658-f11662f15f6c', 1, '2025-06-12 10:40:56'),
('0972ed5e-2a3a-4825-9728-30066b9e36a1', 'fd4ae80c-d6fb-4e4d-be1e-300f4ebf6bba', '0d4e8645-ff06-4531-bc5a-09e6570248d8', 1, '2025-06-19 02:16:21'),
('0a46db9a-c81d-4cbc-ab25-45517814e60f', 'bc6373b3-01aa-4e8d-815d-af7d94040cd2', '466cca72-833b-4631-80f5-1cafdf402375', 1, '2025-06-12 11:18:15'),
('0aa82182-a9b1-4194-90b0-fdc69f3f5713', 'e565cdde-1422-4d12-8127-a067e5b01fe5', '895e71fc-991e-4b42-9803-4bcafdb03023', 1, '2025-01-31 06:23:03'),
('0aa8ac71-a235-4b4f-b9da-2b77e6487638', '49100e5b-82a9-4a11-849b-17e45117adba', '88bc1a3c-f646-4e7a-863d-3424b0fbe1c1', 1, '2025-05-02 13:46:38'),
('0acea8d7-fb74-4231-8954-5fad65bec5a2', '5ee574f8-06dc-4c6f-8d61-7fb7c093d010', '2b5c101f-db79-4143-89f9-2b42fbea06bd', 1, '2025-01-28 06:12:18'),
('0c2f4f9d-919d-4376-82fa-12f3f6317217', 'e5ff0b66-f1db-4f84-befe-73d06829d4ec', '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', 1, '2024-12-16 09:24:50'),
('0fd45ce8-0bff-4127-97af-41b0b89bd3b4', 'ebb8ead8-1089-426c-9525-9a352b756974', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-03-19 09:32:50'),
('10139a10-1418-48fd-bc9b-2eeecafe0558', '0b0be872-6e8c-4ad8-a958-a5a583d97ba7', '2d4d1aec-e4e4-4836-9308-4c2c19da05cb', 1, '2025-06-25 09:38:18'),
('114c3d77-c747-4ae0-a0fd-c662d4c839a6', '161e830e-355e-4364-acce-405857cf30b9', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-10-27 06:30:53'),
('1198dce7-c9ec-44c8-9a39-64b43f7d7b74', '9ac3c951-ff30-4432-8ed9-207bc3c4e2bb', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-07-29 09:13:40'),
('120081c7-5517-4674-aa84-8c1b424a3dcd', '7458164f-8df5-43d1-8883-bff87bbc9496', 'da4d87d6-33f5-4937-85e5-d0be395d6123', 1, '2025-06-11 10:32:28'),
('141809b4-ef49-4d65-b64b-55f5f92511b0', '5486e1ad-8bc0-4884-a100-c626c0a2d731', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-08-01 06:01:03'),
('145316d1-a7d6-4632-b1b8-67000badeec3', 'dba0885b-4710-488d-910e-e32d747e8cc0', '642afc1e-c8d5-42f3-a685-aa899e78be1e', 1, '2025-06-10 09:17:37'),
('14a1e002-f1bf-45db-a4fc-81f1b89cd0db', '9d96ef67-1b7d-4102-b1fc-26fef210c292', '5f32551c-7a96-4b5f-b485-2357623e9893', 1, '2025-06-30 14:12:58'),
('159e8071-976e-4eb3-b3e2-93fd94ef176e', '7cfc60f4-9d2d-4694-ac36-18d15392b2e4', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-07-21 08:47:14'),
('16924e1d-5b68-4a28-9f35-d0a30fbd5c13', 'b83568c2-a38e-48ba-8804-dc2c155c7098', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 03:21:10'),
('1750d8de-0867-4947-ac76-7f08577c0529', '3e4448e8-33e2-4f49-873c-773fd4a7aacd', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:07:41'),
('17dd2a8b-ceb1-44ba-86a0-c6ac778267bb', 'd3ff938a-19c4-45ad-96fb-6ad1ce489c1b', '67bda6e1-da1b-41c2-8658-f11662f15f6c', 1, '2025-07-17 01:18:22'),
('1902127f-c705-493b-a7b9-5a7866db790e', 'ba49000b-3509-4377-8d0f-456286a45e5f', '1fb0fb81-4482-438a-ab66-5472c52bf9e4', 1, '2025-09-26 03:40:59'),
('1acfde83-83ee-42d3-86a7-7d114a28648d', 'ae469909-1ee5-4e5b-871c-7f48aedc395a', '51304482-8440-4d89-836e-c45c9eda7631', 1, '2025-06-26 07:53:59'),
('1be26f7f-b625-454a-afea-7606a682b3e7', '078a1fd1-f8bb-4c94-86d5-d35fbf00d1bb', 'f5489b6a-fd5b-4896-b655-761768e44b8f', 1, '2025-06-24 00:51:46'),
('1c7555b3-7651-4658-b991-a23b82a394cc', 'c9607961-6240-4066-965f-5a171dcee526', 'f313a7ba-64ae-4d61-af99-f493a98039b2', 1, '2025-06-10 09:28:20'),
('1db3b6b8-4573-4ccc-9d0d-79a2d0ac7250', '7dbb2b14-756e-45fc-bbb1-a4142d02dad3', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:03:38'),
('1e0a8bca-ed64-4dac-91cf-348e494e110e', 'f04f1583-d0bc-4d89-b868-785f3efd07de', '1fb0fb81-4482-438a-ab66-5472c52bf9e4', 1, '2025-10-27 07:47:58'),
('1e89674d-d0cc-428f-b288-e8fe56d340d5', '6fde9224-b19d-43b7-9eb0-ac741b8fe057', '5f32551c-7a96-4b5f-b485-2357623e9893', 1, '2025-06-27 02:21:01'),
('1f026fa9-c981-46e0-9da6-aa4b478bab9a', 'b659bc53-f12a-4a5c-9ab9-905939c9fb2e', 'cea804cd-55ab-4a3f-b9ff-a942547402a7', 1, '2025-06-10 09:25:20'),
('20f4138c-912c-4a1a-b3fd-443c8235b371', 'b88aafb0-9c67-4bd4-9043-e9239b92413b', '0ecc689d-9c12-4936-b9df-596884715574', 1, '2025-06-09 04:14:38'),
('22492503-e6d0-4bb4-892c-4bee2803f901', 'dcbfadc3-88d7-4064-88f0-623819025571', '1be1add7-4691-4a2b-a2db-5b849cdc5cfe', 1, '2025-06-16 04:30:26'),
('22ebb625-69a3-4367-9ba7-e690c2b16f14', '49b9dd79-d94d-45c9-8645-cf4caaab398a', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-10-27 05:21:57'),
('235c49dd-62bc-4b5d-9a7f-1911329aa5cc', '0c12c58d-8300-4010-a010-6e0c6a9141f9', '1d9884a8-7762-4f28-a3b5-8419f13ffe8b', 1, '2025-06-24 00:53:06'),
('23bb2742-febe-4585-9c9c-d1cd23c936b9', '1890f745-aaf4-4928-acba-032632272c77', '642afc1e-c8d5-42f3-a685-aa899e78be1e', 1, '2025-06-10 09:03:36'),
('25595dd4-631f-44e8-868c-13ccd71bdbd5', '0e5e2055-5b01-41f6-9456-e04974667287', '2f1ee3da-fe91-4f06-b0ae-62a206c7cd5d', 1, '2025-06-12 11:10:09'),
('25699015-b5d1-447c-86bb-2264e7ae9626', '3cef16ff-6bf2-4f07-8d0c-cdc2fb0bb4f9', '8a441dab-7f49-4ff6-bb6d-327003829c1f', 1, '2025-10-27 05:40:24'),
('25a0625d-874b-43ad-b7f9-43e478e43eac', 'e7c861a2-b027-4992-a280-c8dc6a180784', 'f313a7ba-64ae-4d61-af99-f493a98039b2', 1, '2025-06-10 09:28:47'),
('282aafca-f483-4433-9147-5ecfe35e1f99', '2bcbf6c0-4d54-4110-925f-405de802197c', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:09:14'),
('291c9b80-2342-46c8-bdd9-7eab1c49380d', 'e33b70d6-0017-4742-9751-fe355d76e392', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-10-27 04:26:57'),
('291ff2d3-28f3-422a-950f-49e514f2e833', '4ab1ec63-fe78-4c3f-b039-1870bd5ad987', '1fb0fb81-4482-438a-ab66-5472c52bf9e4', 1, '2025-10-27 05:32:40'),
('298a30ee-e134-41c8-994e-0f261268437d', '8c4abdec-c8a4-46c3-a65e-76fa084764ae', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:01:52'),
('2a4700b2-d177-46e6-8a99-da34d65dc08c', '3b284924-c841-4181-b8db-8827823db7d2', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:09:36'),
('2b8ba649-9780-499a-97ac-bc54e740e59a', '9f8238dc-3e88-4d57-93f4-bdae13ec301b', '1fb0fb81-4482-438a-ab66-5472c52bf9e4', 1, '2025-10-27 07:49:04'),
('2c6e2bc9-bd6e-46da-9adc-a48b71b5d467', '57cff5f7-e083-40ed-be05-323e55b0f12c', '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', 1, '2025-06-10 09:29:43'),
('2d13dfb6-52b4-46a4-b660-e8e2c97f4cca', '4821cea3-07a4-4495-a139-8e8d74e26254', 'cea804cd-55ab-4a3f-b9ff-a942547402a7', 1, '2025-06-10 09:27:42'),
('2d2b0a13-1e09-45bc-9a86-fa2aec7289f3', '92a99359-0555-4ff1-9be4-c26808189158', 'c120a5b5-375a-411b-87d4-5fa61e6453d9', 1, '2025-06-16 00:52:25'),
('2d5e271f-f682-4998-9d6b-14166b2ee127', 'cd66bfe3-0067-475b-b745-b35d2da71455', 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', 1, '2025-01-06 09:43:54'),
('2ff59fad-ccbe-4ed7-bb60-560bf6092280', '0b8f2e6e-5e6f-42c9-8ab5-aafa5ad065eb', '0968cd06-9d79-4933-8de8-399cb9ac5868', 1, '2025-01-17 11:44:14'),
('3077857d-d133-4391-b7a2-cfd64b340b0d', '29dbf3e9-e0c5-4f32-aa07-9c9460094322', '74f091d7-a81e-426b-a55e-a50eeb43d8e7', 1, '2025-06-23 02:52:06'),
('3088acc0-9d36-4b9e-ab8a-53705a76b572', 'd1765bcd-968f-4203-bc56-a62447106389', '65b9a9b5-5272-4b9d-a02f-1b4c85460069', 1, '2025-06-24 00:52:30'),
('30cbb715-93c9-49fd-83ec-19e31f429b1d', 'a3fa105e-b258-474a-87e5-e39272e3f127', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-06-11 08:16:14'),
('32ee0e85-f113-4af0-a30a-80ec7c807764', 'b56986ef-95fa-4f26-ba4e-ae0a593003f1', '2f12eaa0-9738-484d-8329-80e964ea5ee6', 1, '2025-06-16 04:05:04'),
('3302e5e4-e459-48c4-aae8-c685af094bbc', '3bde2447-a24c-4b11-933c-5a5160e902f3', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-10-27 05:23:24'),
('3416d767-67dc-407d-8abb-3e78c7abef9d', '7f69f115-4ea7-4c58-8b48-d16f6fefe0be', 'ff09ea1e-4e6a-44e0-8637-03ac0670070d', 1, '2025-06-12 10:11:40'),
('34c139e9-bb89-4a31-9150-49dd068f2263', 'b70608c1-6f57-4abd-bce0-9260962b0bb9', '2d4610f7-471d-42c1-a193-d79ac4eb24e8', 1, '2025-10-25 15:06:02'),
('34edd256-7e9c-4940-b45a-9469742a19e9', 'f4fb553a-4ecd-4a70-8459-9d8462ccfddd', '5f32551c-7a96-4b5f-b485-2357623e9893', 1, '2025-06-25 07:28:34'),
('35a31c86-ce33-48a5-b3cf-404f7f41f661', 'ae8cd8e6-3101-4adf-a234-5d0fe550230b', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-06-11 08:09:34'),
('36683b93-52dd-468b-b2a8-093b5d21fa10', '3b418fca-65f3-471b-b61d-ba338a9aa36e', 'a485226f-e787-44e7-a140-4bf50433c525', 1, '2025-10-25 15:10:30'),
('37e0c3d3-5ba1-4aff-832f-1bf1938e9be7', '819456c1-3df2-41b4-874f-377b4d2ecca4', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-10-27 04:32:33'),
('381593cd-e3bf-4862-9e8f-c6191c1801b0', '2820d05c-93f5-441e-9e24-1ec1930d2345', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-10-07 08:20:59'),
('3845de74-9e2d-4c87-91c1-3ef106cf0ba6', '992f5009-7d10-4444-bfc4-bd4e03387917', '92463365-36a7-4898-a759-c4ef2a90cedd', 1, '2025-06-10 07:36:20'),
('386a4cc6-e885-49fb-96e6-738154342a64', 'd0c02f40-fc72-4219-b7dc-b6c77f6a8d5a', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-06-11 08:27:37'),
('38d44605-b478-4fbf-b88f-7e1957e0516e', 'adac6142-24d1-4efe-a469-859c1cb11243', 'e85465c3-44e3-4210-a4ce-88f9aa09af26', 1, '2025-10-27 07:36:10'),
('39bb78a3-2d5c-4b8f-b59d-723672d6cdc2', '271444d8-9d28-4689-b5d9-c32aedac1024', '641761a3-129e-4d38-ba11-2c4c9bb44d3f', 1, '2025-06-11 10:25:32'),
('3a6b98ef-457b-4912-99d9-06e27fb23441', '6cf12295-6b67-466c-b10c-12c12d0ac031', '9610604a-2d45-4b3f-9c93-70791dc4f0ad', 1, '2025-06-12 10:26:04'),
('3aba6315-78bd-48de-b723-00f2eaa17084', 'b8278876-66e5-4ce7-a8b2-9242bcc37638', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:02:07'),
('3c5a00fd-055c-4c9d-aaa9-bd4ac707543b', 'bcde4847-acf8-4fb7-8d6b-e9ff9f5e7a62', '1fb0fb81-4482-438a-ab66-5472c52bf9e4', 1, '2025-10-27 08:02:20'),
('3cc12fd1-2d33-4cb3-99db-695df6ad35d6', '52d95985-84b0-4d61-8748-b1a76856536f', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-01-20 04:17:13'),
('3cf236b1-a4de-4140-b371-3811b9766981', '72f91cb8-944d-44f5-babc-f4288568c964', '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', 1, '2024-12-16 06:46:49'),
('3d066a3e-faa8-417c-b2d3-4c581518ac3b', 'a0c2b272-18f2-44f5-9a0b-33bf70e56be3', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:00:56'),
('3d600ffe-72e3-4a7d-8df8-5b215361c31b', 'c49df822-5bb0-4f5e-84e0-4c3f38d8b6f3', '9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', 1, '2025-06-12 10:36:15'),
('3e44c96d-d964-48f7-8682-60042a145e94', 'f655c0f1-25e0-4c51-bf01-4f99b4121ba7', '690cfd6a-0270-4b22-8d1f-de1f91dda830', 1, '2025-10-25 15:08:53'),
('41d82d0b-485c-45cb-85ad-800231f12816', 'e6184225-dba7-4cc7-90d5-af9584109353', 'fc372e65-cca3-4c7c-b580-c689ef2d0798', 1, '2025-06-12 10:15:10'),
('44eeae6b-25d6-4afa-91ca-0e3476ce96ff', '4677b262-6fc0-4bc7-8708-a0806b091577', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-10-27 05:28:52'),
('45c038d0-e635-4b50-b0df-21d0133ee7c6', '525b742d-5749-40d2-a148-0290161fd3c3', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-06-15 05:46:46'),
('46db9b83-6aa3-468b-9795-10cade9588d4', '2754a8f7-1129-4009-855f-a338b6ab58de', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-09-04 06:25:31'),
('47c11a86-7698-4533-8572-4149d5f5ae55', 'faf652c9-8bd0-4b21-a935-30c904d8650a', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-03-19 09:51:32'),
('48790f08-8815-4027-bb57-201863c0d3c2', 'cdac6856-358d-4079-b131-f1ca090cc858', '9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', 1, '2025-07-17 01:12:41'),
('48ede749-8a0c-40f3-9084-b2b6bf71db67', '759f33fb-b998-4d5f-bd80-343867ef52a0', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-10-27 05:24:09'),
('49067b3a-a7c7-429a-af20-1ffdaef66454', '0d26c143-d6c0-4956-a8d6-5fed886c0a61', 'f18472d8-50d9-45ed-b267-00948a15a2e9', 1, '2025-06-16 07:13:02'),
('4b3d29d1-062e-4311-b46d-6145ec440eb9', 'ec5bd8ff-0c1a-4da8-ae4a-ab26488b2c67', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-06-11 12:36:53'),
('4b3eb772-8eb3-40e0-9608-acb60159fbbb', 'cb64445d-023f-4677-a63e-3a94d7a3a0bb', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 03:29:02'),
('4c030816-6322-426e-8975-9ec8e5d6fc48', '2de608f9-70bf-49c3-884a-7b663f145cd1', '3761198e-e426-49b5-9dc5-a5efd3b13a33', 1, '2025-07-17 01:30:26'),
('4d0e3e38-637f-4f53-a6dc-12222c7e1bc0', '4b7ab0ca-b747-482a-a113-03a5891f9aab', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-03-06 04:39:00'),
('50954001-2698-445f-aa4c-d732d7c26779', '49100e5b-82a9-4a11-849b-17e45117adba', '45af1f14-b041-43b2-b4ff-d93692564a61', 1, '2025-05-02 13:46:38'),
('50c5a63a-caa0-4ea7-a01c-9d83114df5f3', 'e99893be-bbf3-43f3-b577-5fd07e381157', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-10-27 06:24:36'),
('518f6938-ce2b-4142-a361-3ad440906418', 'ad862d94-87fb-4be9-b37a-5ac08b2b8b7f', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-10-27 08:54:31'),
('51a5bf57-bb3d-40c6-a346-981577accb4d', '8cc371a1-8b3b-4688-8998-7c796af5e492', '0d4e8645-ff06-4531-bc5a-09e6570248d8', 1, '2025-06-30 14:08:26'),
('5293e381-708d-4769-9011-f4f1d2b9d0af', '1145f48e-cbed-4c90-807b-23181de39e4f', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 03:29:35'),
('53665e74-50ef-4ab8-acf7-527c5fa91436', '0e29e992-1d85-4173-93c4-7348863a0362', '9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', 1, '2025-07-17 01:14:38'),
('53736459-c155-4df5-9cde-ad44a8f53654', 'b1a3f595-09fa-47b6-b8c1-37338463393f', '726fc634-2c97-418b-a230-45e936cf843b', 1, '2025-06-10 07:33:55'),
('550b843d-dfac-49ec-815d-08f7f1b5aab5', '1338b661-5cb2-438f-ad71-e24316d9b2ae', '213830aa-08d9-4673-9081-3fcba6ce1625', 1, '2025-10-25 15:09:46'),
('555f1c81-86e3-428d-986b-c1fec998843a', '65c8ab6b-d4c6-4a32-8029-eb189f32b991', '32104ee7-4b28-400b-bb7b-1ab55e1cf19d', 1, '2025-10-27 06:48:01'),
('581dd7f9-b692-46ec-bca6-24d60e45aab1', 'fa1a06ff-e3b5-4243-8508-9ef70aa510a3', 'd9e67c01-1640-47d0-aeaf-38b622f996de', 1, '2025-06-16 05:13:43'),
('582b24ff-690d-4d84-ab5b-7a8be66d8fea', '9e2cf0c9-4462-4a69-b6c7-c77785578f5e', 'b2907f8d-53f0-4f71-bc36-11e24a52c10d', 1, '2025-06-13 03:16:25'),
('5906854a-0947-4cb6-ac06-e7085e9a78d2', 'a5e4b1fb-d1da-4594-9ffd-d28817f252a2', '642afc1e-c8d5-42f3-a685-aa899e78be1e', 1, '2025-03-13 09:41:43'),
('597779f9-a2e7-4644-9aa6-56c8381ce5b5', 'd5e371bb-41e9-4a42-aa6f-9dcd2923ab7b', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-06-09 04:17:06'),
('59970ca0-73ac-4c7d-a509-421c44d3f5ce', '328cb493-4f0b-4b1f-ba09-44a443e8e752', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-10-27 06:29:19'),
('5a739cad-849b-4586-885f-0aca95048003', '0acb3ca0-9c79-4953-a686-1f4ab035b35c', '594abb40-0296-4aa0-a1fd-82f479359ed5', 1, '2025-10-25 15:08:25'),
('5a81c26d-30cc-4029-9747-985a89713f38', '4d5ab32c-8838-4780-bde8-a974adc31874', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-06-11 12:32:03'),
('5bd4f79b-e5a7-41f8-bd7b-f10592d1a82a', '356dab8b-1ff3-4960-9dca-f5cffe58ccd4', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:03:57'),
('5bee27c3-91db-4979-a45c-41f4a36ad70c', '0317f37a-b177-48eb-bad3-d297a74edd1e', 'ad0ee715-c3b7-4df0-b097-6d0bf1565fb0', 1, '2025-06-11 09:06:05'),
('5c3dd662-096a-493a-8b07-6b2c71a2bd5d', '218430e9-c452-4019-9aab-dd4b180270c5', '290b026b-4379-454b-a8df-5aa410a6bd21', 1, '2025-06-12 10:45:57'),
('5d697e9a-b9f3-49f1-9ada-e553ada29b3a', 'ed91e3b0-e620-4d6a-b3be-df0820018bd6', '11ca34d4-27bb-48ce-915a-81996dc98f9b', 1, '2025-06-12 10:10:37'),
('5db40ec7-dd7c-4f26-bd4a-2cec5ac0b6e2', '38c30373-b998-4c91-a0bf-7c3c538b5f07', '895e71fc-991e-4b42-9803-4bcafdb03023', 1, '2025-06-18 09:06:55'),
('5db76591-6aa3-475b-901e-769ef2d6c356', '74152581-8a94-443f-ad39-140e5f9dc509', 'df6e7ebd-77f2-49e4-bcdf-04c71608005f', 1, '2024-12-11 13:15:13'),
('5fd724d5-71e5-495d-bd04-80def56dae53', 'c87d5dd6-8a5a-4567-aa86-f0dab93842d6', 'edb5c314-2962-4d20-95e1-59d58f732a6d', 1, '2025-06-16 03:37:52'),
('60463d3e-9d7b-4429-a2c1-8e18ac1932dc', 'b3e78fb7-7d86-44f0-aaf1-3a099f279bad', '5f32551c-7a96-4b5f-b485-2357623e9893', 1, '2025-06-26 07:55:06'),
('604eb2cd-eb9f-4186-bef1-e36711b7800d', '837e23f3-a09d-4927-bad4-27837014451e', 'abb7ccfe-d759-4007-81d6-1f11bc439a37', 1, '2025-10-27 12:43:24'),
('605b79ad-c9e0-4d0d-a4c5-f18f273f32ec', '456192c1-3257-4f9e-8a5d-d34e621f9182', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:02:58'),
('607d09bb-fb5a-4ae8-beb0-a676ebd9b111', '1103540e-b227-4f59-8729-1b85ecd0d05d', 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', 1, '2025-01-06 09:48:27'),
('6115f616-ca99-4776-9db3-9604263e6dc5', '6f882833-ac9e-4367-8468-ebb05dd81a8e', 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', 1, '2025-08-07 08:40:33'),
('638645af-c230-44e8-bb7e-2b31b468bf60', '7c338957-c9a9-4134-b79b-3d131b19dec9', '0f80acd4-d034-4175-b501-f879a9e203de', 1, '2025-06-16 03:53:27'),
('63a02795-8dc4-48da-ba48-36ecc084c020', '11bf7c88-a563-4a01-8419-a32500a9194d', '88d465c6-3e16-4c58-a6da-10bce309af89', 1, '2025-03-17 03:51:49'),
('6463819d-b997-4224-81bf-c588862fb228', 'd351ce78-b7c0-4d0f-8ed3-d47104931534', 'cdd15d78-73d7-41d6-9fad-dfd0da61a1a9', 1, '2025-10-25 15:09:18'),
('664a38c2-ddae-4ea1-a7a6-19c571335154', 'c4be4e29-07c2-49b4-8495-a0b2c1e032e3', 'e126cf96-c67d-413b-888c-b81dc86ee9b8', 1, '2025-06-15 06:32:57'),
('66a682e8-6e93-4660-9ca2-f1ed375eacd5', 'a8f71376-2433-4415-8538-bfffff67dbba', '3ef73d28-72ff-4c90-b04a-693a33baf895', 1, '2025-06-16 07:29:29'),
('67e322c7-2671-481d-8450-2f8411e4e64f', '7e7dfe60-c9c7-4239-b8ee-6d34896d6ee7', '5f32551c-7a96-4b5f-b485-2357623e9893', 1, '2025-06-26 12:09:49'),
('69d2ea53-a551-4aea-aa23-bd52989e4d19', '61b7c176-6f76-4dc8-b764-384c493dddc7', '5db003f0-4196-451c-afda-e22e4481fefd', 1, '2025-08-05 10:09:42'),
('6c75f19a-0bd4-4ae2-b543-cb8ca7d2d5f4', '4b86e1b4-e921-4ca4-809b-28fc4ef63b07', 'cc80c251-336b-4039-9850-5a042948e8f3', 1, '2025-06-15 06:14:49'),
('6ca7aab7-7052-4698-870a-cb24bed9f09c', 'a8036bf1-d571-4e64-82a4-ba9838de8c7e', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-05-20 06:12:14'),
('6f0d1420-aedf-43be-a151-3efecbbd45b6', '1cf8da79-24cc-4f87-ad15-bae140ab4e55', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-03-19 09:30:08'),
('6f292c95-5ea1-438f-a136-7cd77fbaebaf', 'eab3d4f8-1ab7-4654-b1c3-d1ddce015b5b', 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', 1, '2024-12-16 06:46:16'),
('6fb3e521-871a-4535-992f-a6e6c1a3eec8', '1e3fdaa1-9f43-40d7-93b5-c3fc4b0a70a9', '6b3ba15b-ee6d-41ab-a543-d345e9f62259', 1, '2025-06-10 07:12:26'),
('7002a1e3-2b6e-45d5-97ca-0001dd982c06', '64e25a53-27be-4e55-8dc8-8a6cdb3b8115', 'a7398772-5d5f-4f09-9eb6-6edf32fb9893', 1, '2025-10-25 15:01:44'),
('7233005e-50bd-4995-97b0-a4c61b059725', 'e90e5ea9-c4b9-4657-a0e7-0ce63daf791e', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-10-27 06:22:47'),
('740a7679-2131-44fe-98db-86459e5d1dc2', '71bd06ff-3e69-4fb3-a42f-d6faee810642', '11ca34d4-27bb-48ce-915a-81996dc98f9b', 1, '2025-06-12 10:14:41'),
('74f266d4-3dfb-47b5-8533-929cb56c2ca1', '95ac9cb3-e7c1-4016-b7c4-447f6d70e1c8', '34ea3368-fa1c-445a-aeb8-821c87086d3a', 1, '2025-10-27 06:56:17'),
('75e9211d-4a7b-400f-a4dd-39f5dfdbca68', '50e8b6f8-f35b-473f-ace3-278c2008f224', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-10-27 06:22:06'),
('7602dc3a-83d5-4ab2-bb47-a54d4a9afe3c', 'd908bdde-8ddb-4a37-ad87-63be6b05bf58', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-10-27 06:20:07'),
('76545e5e-76b3-4bb7-b5cb-3521ed5652d9', '5e4b1eb5-1afd-4a26-9875-98fc7bb0805d', '5f32551c-7a96-4b5f-b485-2357623e9893', 1, '2025-06-30 14:13:48'),
('77347889-af1c-4e14-b0bf-924d782a4b86', 'cce12004-7e3d-4a8e-aa44-6e2a07bb9a57', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-03-06 04:42:45'),
('7757c384-0cae-4336-8ae8-6c3d793b1a5b', '26ffb5a6-2d63-464a-a9db-3e976c2d3893', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-03-19 09:37:47'),
('7765be02-8dee-4831-9876-47f14bbc2836', 'd2a7935d-2f11-40f2-9c1c-89088ee9e180', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-01-13 05:04:38'),
('792c4e4e-4f41-4875-a194-4e5d17ece305', 'd111b3e8-470a-4ac8-9257-618c22a022b6', '9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', 1, '2025-06-12 10:40:35'),
('794ac991-4a63-4fe2-a810-2551ed2b8c12', '44345865-d82c-4627-b337-c2565c5a3bb4', '642afc1e-c8d5-42f3-a685-aa899e78be1e', 1, '2025-06-10 09:12:13'),
('7c24fbb5-2689-4dd0-a77c-28823aae45ca', '2cfe725d-8bfa-4d78-a196-a30881a8eb22', '5aa126c7-c78d-4234-b0f3-45153034626e', 1, '2025-04-22 03:57:33'),
('7c538793-ae4d-4ec4-b572-b88c847f5ff2', 'dcfdaa4d-da79-41b7-98b0-448433406dc3', 'f5489b6a-fd5b-4896-b655-761768e44b8f', 1, '2025-06-11 08:06:15'),
('7e3067e1-52b1-4bd8-86b8-d2397a8d8d04', '74c45e80-c867-46cc-919b-c6e4c7d0c076', '3b652cc4-3afe-4caa-b092-fa8987489c78', 1, '2025-10-27 06:35:02'),
('7e87edd7-b236-4b51-ab06-66298e7d38f9', '4cafdcd9-a163-4b74-847b-9d878ececab9', 'ccfcd9ae-df30-40e5-98a9-71bb73d4f491', 1, '2025-06-15 06:28:10'),
('7fc9147e-a0a2-40be-8b85-a5b0ed91f292', '992f5009-7d10-4444-bfc4-bd4e03387917', '1d9884a8-7762-4f28-a3b5-8419f13ffe8b', 1, '2025-06-10 07:36:20'),
('805279a4-334f-40d9-9ee8-3355d60b76de', '51e62f2e-3b91-44e8-9875-55239e0e8acc', '4ce6231a-57de-44f4-ab4e-817fec010315', 1, '2025-06-16 00:28:58'),
('81f91410-83e0-446d-9dfd-7c83a3765c85', '66c0508f-b34e-4007-938b-2a1dc2f7e297', 'c120a5b5-375a-411b-87d4-5fa61e6453d9', 1, '2025-06-24 00:55:15'),
('8343a00a-13c3-47d2-8b67-8c1ff5acb21b', '55f87cf2-2b69-40b3-a5fe-bef370e18e2d', '9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', 1, '2025-06-12 10:39:05'),
('8354c350-ddd4-445c-95e5-ffbd6f3a7fa5', '581981be-b9fa-4c15-8091-48e893e2880f', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 03:26:44'),
('84382aaa-2e4c-4869-a16c-d062b6493785', '858613c6-b0b0-46f1-a307-c1eb89c9e588', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-03-19 09:53:39'),
('85ae9715-8004-41b1-99bb-a6834f8e4afe', '261d79bd-5ed3-4db6-91f5-a686205cd8d0', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:04:17'),
('85e980a2-1b6c-4ba0-98d7-795bae5c3507', 'e4b1a5aa-dba3-40dd-8ccb-ddf06547fa9e', 'f5489b6a-fd5b-4896-b655-761768e44b8f', 1, '2025-06-11 07:55:36'),
('860d3691-175e-4c42-9346-25af04393bcb', '57053bbe-382e-47fb-a5bc-2d80b4c67c9c', '2edcb350-5c44-4803-be82-0ce9b0015ac7', 1, '2025-06-11 10:45:44'),
('86f1a5cf-e47e-483e-a2e0-2c3b30afccbf', '7269890f-a7b1-47e0-907b-c0fb5eacc576', '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', 1, '2025-06-10 09:30:10'),
('87d62587-3861-4711-a9a7-1e07e68db2c2', '04b15a59-91a2-4bab-9dd1-6366c49a06d2', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-10-27 05:24:40'),
('8a1ab784-64e0-4d89-857f-2f557980d73e', '0945cd12-6bde-4ce9-9f52-d1e2429f24e8', '0d4e8645-ff06-4531-bc5a-09e6570248d8', 1, '2025-06-30 14:10:04'),
('8a9cabd8-b3c6-4d14-a051-b4b91c6c2fb2', '2a710db7-53b6-4cb3-8688-065da1044185', '9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', 1, '2025-06-12 10:37:49'),
('8afc7847-55b2-4e20-92ea-b7988611ab2f', '9057cfe3-9c6d-424d-9044-ff147aa46aab', '9a8307fa-375b-47c3-b09d-2f7ca12f0c02', 1, '2025-10-25 15:06:34'),
('8b30e1b8-5f9b-4100-9d18-7870f5fb8d7b', '69b3d4b2-f00b-4d75-9379-f17aaf4c2e34', '6e23608d-46bb-4e74-8326-21365397565b', 1, '2025-10-25 14:29:17'),
('8b6aa6e6-2996-412e-89bc-f210127688b9', '9d490452-0821-4b5e-ba06-ce9626bff7cc', 'f5489b6a-fd5b-4896-b655-761768e44b8f', 1, '2025-06-11 08:04:41'),
('8d0584cb-357e-42c3-9599-5e2adf5b48a3', '040612ac-aaca-4901-9a36-a92dba0cca31', '642afc1e-c8d5-42f3-a685-aa899e78be1e', 1, '2025-06-10 09:40:26'),
('8ddc1707-181d-49ed-a255-0aa307872c93', '6f49e7bd-2886-4448-8c2c-a34b78d05b7e', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-06-11 08:32:30'),
('8e184148-c2f4-4d4e-8cee-3ccb26004484', '823bb70a-bf37-4d94-b6ec-d65db3edff56', 'f53a9e46-d14b-40d7-8acc-cd7a0a2ced0e', 1, '2025-06-16 02:40:01'),
('8eebadd5-4bdd-4f52-978d-b6f15ff04f98', '578b6f6c-3165-4533-abe0-18bfc5bf4e91', 'f8a6cd53-4c8f-490d-83c8-85db6fb422bb', 1, '2025-07-17 01:25:44'),
('8efa1a3b-b00d-455b-85d0-5330298c4dde', '8f6e2e97-d5af-4515-b4e4-60d60a6939e8', '9a8307fa-375b-47c3-b09d-2f7ca12f0c02', 1, '2025-10-25 15:07:02'),
('92cfa4e1-d52b-4f20-bab8-4a6fba4aa081', '2f4fe9d4-a106-4354-9cec-8085d4723832', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:06:26'),
('935b10ab-cbc2-4736-8f24-a2c838b55c7a', '5fd58030-0398-4401-a18c-4839e7cc0c2b', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-10-27 12:35:23'),
('94c595a2-820c-47c0-81c6-0c542754f46c', 'd5da6d89-8b1d-4f26-9304-f826c769e800', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-10-27 06:27:55'),
('96c67ad2-9903-4f3d-bdbb-e541b7702f82', 'aa8cb602-fdfc-4b1c-abc0-0d03c901fea9', '67bda6e1-da1b-41c2-8658-f11662f15f6c', 1, '2025-07-17 01:16:50'),
('98ec547c-2ffe-43a0-a0a6-1b702a04aa99', 'aed4f594-e9f4-4ec3-844e-c0e35af9ec6f', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-03-18 09:39:46'),
('9abb250b-8f94-43c1-a382-692bb195b4a5', 'ac6826b4-33b4-4c47-bf34-890d7b8f0e7e', 'f8a6cd53-4c8f-490d-83c8-85db6fb422bb', 1, '2025-07-17 01:26:50'),
('9b36c423-e963-44b8-8aaf-5fb5b922cc53', '2f8a36c8-5c3b-4453-96c5-07e1b594b246', '4baf1507-337e-43f7-8d21-fbc184d876ac', 1, '2025-06-16 06:48:26'),
('9b38c31f-aec7-458c-bb85-fb6be7c27883', 'd15b7906-d0c9-4f4e-be2d-53a1484ea943', '9610604a-2d45-4b3f-9c93-70791dc4f0ad', 1, '2025-06-12 10:16:21'),
('9f9e8de5-bbc7-4244-bc1b-1163bff9e5b2', '1c9c103d-de4b-4542-8ba0-c017cd06e23b', '5aa126c7-c78d-4234-b0f3-45153034626e', 1, '2025-10-25 15:08:04'),
('a11fb7c0-27fa-4eef-9294-d3e279e642ff', '90e541d0-fdfc-457b-bc48-8331cd1aad81', '11ca34d4-27bb-48ce-915a-81996dc98f9b', 1, '2025-06-12 10:54:00'),
('a1f6725c-d193-4569-8a23-546a5677c482', '0df824e9-139b-4fe0-af22-e2c390df0cc6', '0f80acd4-d034-4175-b501-f879a9e203de', 1, '2025-06-16 03:36:56'),
('a25c081c-d5e3-4964-a70f-98c844098ce1', '5a9e836c-bc06-4d16-9ba3-509ca6b53423', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:09:53'),
('a27a20dc-6ddf-44ee-85af-64fb5f6dc60f', 'f50eb76c-0230-4f71-b47f-c2e60d652ce1', '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', 1, '2025-06-10 09:30:33'),
('a2941ccb-3c50-46d5-953a-6806d27aad89', '996fd65e-4532-4f9a-97e2-0e05403bc275', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-09-10 08:55:36'),
('a361d8fe-224b-4093-b0ab-3dc66bf7c183', 'eed702ae-86ea-45b3-8688-33fb3bda90e0', '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', 1, '2025-05-15 06:27:52'),
('a4154286-eeba-498b-abd5-78ed3850ec74', '02f2b2b7-c67e-4255-95fe-247196b92206', '1fb0fb81-4482-438a-ab66-5472c52bf9e4', 1, '2025-10-27 07:51:47'),
('a438dfff-4327-43f9-af5d-c38277fe79da', '97db86a0-878d-4627-b410-316f1dda7152', '1fb0fb81-4482-438a-ab66-5472c52bf9e4', 1, '2025-10-27 10:41:09'),
('a78a94cf-631e-476c-8f8b-c6fff415a25a', '7fc6a707-7a83-4295-a0fc-f32434aeecb0', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-06-15 06:12:29'),
('a79d2fd8-d4e9-4da6-8259-02654771031c', '4d537c51-ec3d-412b-8c7c-4f12cb07b45c', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-08-07 08:35:25'),
('a7bbf671-45b5-4da3-acae-ef64e26e3e78', 'e0bd9cd0-e670-49cc-828e-193908f5a692', '4baf1507-337e-43f7-8d21-fbc184d876ac', 1, '2025-06-16 07:12:16'),
('a8318a54-ba49-42d8-be5e-b16c552de6d4', 'b2c823a7-91ae-4242-a1e3-4aaf44722ed5', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:03:14'),
('a84f6f14-2a76-4b82-b5aa-f71d94f61025', '1b1ac888-9150-4286-918e-398b93277578', '075ad214-ce0d-495d-b27d-d4a4fdb9e083', 1, '2025-06-12 10:57:20'),
('a8fd1a80-d4f9-46f6-8b53-593811214be1', '82a6796b-dd7f-4cc7-b822-5cbee53bc4e1', 'cc80c251-336b-4039-9850-5a042948e8f3', 1, '2025-10-27 06:02:58'),
('a963a7f6-2cfc-406e-9f34-0f1a611c1a56', 'd122993a-93b9-443d-a6b8-226878d0b5e4', 'f5489b6a-fd5b-4896-b655-761768e44b8f', 1, '2025-06-11 08:02:49'),
('a9ffd114-9a78-4e2f-88a8-df14cf56ed5a', '8024bdad-92e7-41ca-8830-0aeba1db4e84', 'f5c84ebc-38a3-47a5-8b55-aeed3c520473', 1, '2025-06-12 10:49:58'),
('aeea0da3-105e-4806-8ae7-1aad0b7b85b9', 'aeeb73ff-701c-48b2-b112-27a211b21376', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-06-11 12:40:34'),
('af10468f-152f-42a4-af91-d2ae2ca0e667', '4a9d25d8-dce9-40ab-b8dd-8898d87a00fb', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-08-07 08:38:16'),
('b137b530-8aed-4f18-90a9-5083d0f46ec2', 'd0ceb94c-4911-4b9b-bebc-2a492504d616', 'ad0ee715-c3b7-4df0-b097-6d0bf1565fb0', 1, '2025-06-11 09:03:06'),
('b1669519-bd7b-433e-9f1f-f99cd0309a9c', 'cf907b6e-e0d5-4aa6-b2b6-006e2cc90a94', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-01-15 07:37:51'),
('b1969522-c018-4f7d-87d0-ca6c625711a3', 'd466792e-3411-4a51-83ca-287e100c3108', '642afc1e-c8d5-42f3-a685-aa899e78be1e', 1, '2025-03-13 09:48:16'),
('b23d020f-2f6c-46b0-b6a4-41fabf2e574c', '94cfd496-162c-424f-baba-021d517b99a9', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:06:43'),
('b41e3bd1-d97b-4861-ac71-1c403c1573be', '8d6a62a2-3ca7-4f4b-930a-fa355d752ee9', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:10:39'),
('b4a982e3-1880-4a9f-b31e-17051b0dcf5c', 'e718b8d8-ea0c-4e36-821c-14e1da8fa258', '360a7a11-6bcd-4301-8156-b4d11ebd6794', 1, '2025-06-13 09:00:24'),
('b6a9daea-066f-4912-938d-03e0fed15ebc', '73ea99da-21a7-4c2d-992f-005d981da3bc', '2f1ee3da-fe91-4f06-b0ae-62a206c7cd5d', 1, '2025-06-12 11:01:37'),
('b7148021-168b-4f2d-822b-de91ea1d2ca9', 'c9364f56-fb9e-45c6-ae71-d4b21bb10d0a', '5db003f0-4196-451c-afda-e22e4481fefd', 1, '2025-06-16 02:39:44'),
('b9126e9a-43f8-4c55-a673-18bb055f50c7', '29556301-fab6-4d8c-aa62-1b1fbf984168', 'ad0ee715-c3b7-4df0-b097-6d0bf1565fb0', 1, '2025-06-11 09:04:36'),
('b9783026-96c7-4df9-8460-00e438ce3889', 'bc7d814b-f4e5-4e37-a02a-d33143f66717', '0d4e8645-ff06-4531-bc5a-09e6570248d8', 1, '2025-06-25 09:38:43'),
('bdc1cbd7-4353-4cb3-a344-d895165bc06e', 'ecb183b4-4add-4215-8b4b-f7f60e544274', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-06-15 06:11:58'),
('be4add6c-6b66-4cf4-bc7e-4da3041be546', 'c30ec464-061b-40f2-ae7c-06d6aced3219', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-09-04 06:23:06'),
('be5e5db5-adc5-42cd-8264-fe99d3a3c855', 'e96e8f2a-5a2c-48fc-bae1-d19c30217990', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-01-15 07:40:31'),
('bf865c92-8a62-457b-b878-5b3d4f606d5d', 'bc78c5f6-8877-4d6e-8d24-7a0bac4746e0', 'f5489b6a-fd5b-4896-b655-761768e44b8f', 1, '2025-06-11 07:57:34'),
('c036b373-0a12-4722-8012-591b2bbabb55', '41b24403-2ab4-48f2-b972-356190dcfc16', '5db776f7-0e5f-42f2-a0de-2f76ffadf235', 1, '2025-06-12 10:13:30'),
('c1fd501e-0cac-417b-80ff-c2783394710a', 'b5aaa158-5fb0-466d-9016-9f36ebf15270', '34ea3368-fa1c-445a-aeb8-821c87086d3a', 1, '2025-10-25 15:01:16'),
('c2f77270-e889-457e-8881-0d3e742cdddf', 'dab73e5c-8b8f-4e9c-82fc-8976ba6ad34d', 'bcdad84b-ec95-4a80-8765-7f14d2c0a764', 1, '2025-06-16 03:35:19'),
('c307f6d3-3ed4-4cc8-9f78-403fe6fbb067', '736481b1-4d6a-4fba-ab31-c07181024fc1', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-08-01 04:34:24'),
('c308e059-c6b8-4ebf-bd51-359d9d09cb1e', '4f82e941-065d-41b9-9c23-b950c6e0f410', '4baf1507-337e-43f7-8d21-fbc184d876ac', 1, '2025-06-16 06:47:40'),
('c3b7f2ff-2adb-4588-a814-e20887aeb9e4', 'b403c2e1-8913-4e94-b6c8-b9f7f36a4f31', 'f5489b6a-fd5b-4896-b655-761768e44b8f', 1, '2025-06-24 00:45:36'),
('c3d0d226-dab2-4021-a2df-8122aebbf6bc', '26ca5b00-8508-413f-bab7-4590a960c6e6', '466cca72-833b-4631-80f5-1cafdf402375', 1, '2025-06-12 11:15:40'),
('c60fcd31-5c9c-4aa6-9ac0-d2958fa1ca00', 'f3f66580-6c68-43db-a9cc-27008752b1a6', '67bda6e1-da1b-41c2-8658-f11662f15f6c', 1, '2025-06-12 10:32:32'),
('c68ebd0a-f695-4297-b889-afff82bed914', '15288aa6-9497-471c-812c-3251021c8f72', 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', 1, '2025-01-06 09:51:28'),
('c72444ef-f744-4db9-b889-2de102469c9a', 'e75996eb-c5ba-4dfe-b36e-5e58ba334bb6', 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', 1, '2025-01-31 06:17:12'),
('c75b91f3-2d3c-42bf-8d85-12f4344202df', '4e831d8a-2f18-4d84-8d02-af10e4ed71ff', 'df1dbd74-1f88-4f78-80ee-60d99e1e7a15', 1, '2025-10-27 06:10:20'),
('c803fb71-9ab2-482f-a29c-80d6d66f01e8', '5663782b-954c-4f56-b20b-dd10800b4b15', '9ca0c7c9-fd99-41b7-878c-3c0d07b938fa', 1, '2025-06-12 10:42:38'),
('cb3ad4e0-0769-444a-867f-201a6a1ad0e4', '80c09146-6e1c-48c2-a442-164844260006', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-04-25 08:51:18'),
('cbd98d05-2e72-4180-a95c-3744d0f19683', '5060fb70-ed94-4b48-a1b0-b5911f0e9bc4', '2f1ee3da-fe91-4f06-b0ae-62a206c7cd5d', 1, '2025-06-12 11:05:41'),
('cc0fca94-68fb-4efb-985f-ac55517233b7', '4ebc5193-357d-4e62-a158-9c0d61ac3591', 'bcdad84b-ec95-4a80-8765-7f14d2c0a764', 1, '2025-06-16 03:35:32'),
('ce836aa7-d848-4772-ae6c-ccf172a4ff50', '553290bb-4d0e-4820-9efd-c3eae3cb3d41', '642afc1e-c8d5-42f3-a685-aa899e78be1e', 1, '2025-06-10 09:19:43'),
('d09ee016-20dc-460e-b443-6bd7da302be9', 'd0bf3137-5d61-4c2c-b04a-77d1ae6c65a0', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 03:28:35'),
('d1cb6585-1f3a-4113-83df-9628ae2ef74b', '9c636263-c064-4166-a18c-2b7bbf47b92f', 'a6371051-02a6-44d0-83f7-78a929a2fb30', 1, '2025-07-14 09:05:29'),
('d22244d7-573d-4847-bec2-46dc0ec6b5de', 'cb09510a-f411-4ec9-9ac5-c8a690b2be98', '9610604a-2d45-4b3f-9c93-70791dc4f0ad', 1, '2025-06-12 10:24:39'),
('d28937ae-0089-45e5-8aee-e4dc26666aad', '02d2d4de-f417-40b2-b81a-51fb3da16374', '616ce37f-dcfc-4921-a07c-dc9ce335ce45', 1, '2025-06-16 02:25:52'),
('d2910f98-f1f7-4359-9c39-897f758c093e', 'cc40765f-12c3-4ca4-bfa5-7c2ed315345a', '2d4d1aec-e4e4-4836-9308-4c2c19da05cb', 1, '2025-06-27 07:43:47'),
('d3e43d33-805c-4a18-9cb7-1ae090c082c2', '8091328f-8720-4838-9682-e08933faa8ac', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-10-27 06:26:23'),
('d61f3435-690d-44a7-a669-5dbece066eef', '98c33fac-85b6-42dd-a794-2767f03300eb', '677f5f38-3f7f-4ca8-b9d6-e4b60f7f241a', 1, '2025-06-15 06:43:37'),
('d79cdbf9-6581-46aa-95e3-3b4269cbf115', '57efd456-ceaa-4471-aba4-ed62eea12b94', '5f32551c-7a96-4b5f-b485-2357623e9893', 1, '2025-06-27 02:20:17'),
('d81d1ec7-bb93-49e9-97ad-169257e35513', 'cf4ae75b-1fe6-4e4f-b96d-d49fefb04ffd', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-06-11 08:25:19'),
('d9a7b455-5ae4-45f6-8c4f-78082a2f57ea', 'dde630d3-d7cb-4c2b-a017-04a020f2a422', '1fb0fb81-4482-438a-ab66-5472c52bf9e4', 1, '2025-10-27 08:11:25'),
('da638fa9-5c88-4abf-b7e1-7f90cdc8f3ef', 'a2476611-d200-4882-93b6-a48caab4900e', 'cea804cd-55ab-4a3f-b9ff-a942547402a7', 1, '2025-06-10 09:26:24'),
('dcd8333e-efb0-497b-ad87-6c58446913b5', 'abd5e4f2-21a6-43e8-97f3-323e0f2d4230', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:01:13'),
('dcf61128-2041-4d3b-873c-d34c58b47732', 'fdf6a82e-a32e-4c69-bfa3-a255bd1dd4cc', 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', 1, '2025-06-18 09:03:54'),
('dda6bdf2-803b-407e-b5df-1492fc1604b0', 'af77dcc6-7850-4057-ac4b-a47ace47a243', '67bda6e1-da1b-41c2-8658-f11662f15f6c', 1, '2025-06-12 10:30:32'),
('de46507e-ab9d-4c46-9bd1-0aa08de9778d', 'f68d2b0e-6ba9-468a-bd47-870036ce545d', '0a20192c-45f2-4032-aae1-39b4861104fc', 1, '2025-06-24 00:47:33'),
('de939b66-5b35-4df9-8f47-70eb8bc6cb9e', '918903af-e6df-4694-82a3-1cea8dda06e0', 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', 1, '2025-01-06 09:40:09'),
('deb46077-cc73-41de-95f9-9db08703f6ad', '20378a02-ba9b-414f-b1c0-0a297005a8b8', '3b652cc4-3afe-4caa-b092-fa8987489c78', 1, '2025-10-27 06:44:38'),
('df1c81d7-0642-4530-a88d-f810cb8c6e54', '464369d4-5720-4b09-9834-8e46884ab187', '1fb0fb81-4482-438a-ab66-5472c52bf9e4', 1, '2025-10-27 05:32:15'),
('e37c5a0c-d0c5-41fe-bdbb-c876ab11f798', '341cad3e-8df3-48a6-8ffd-4a81b0720737', '67bda6e1-da1b-41c2-8658-f11662f15f6c', 1, '2025-06-12 10:33:56'),
('e3a82bd4-aca0-4b17-8096-14318dddf35c', '4b2ed514-e023-4113-acb6-9f64c0137727', '2b5c101f-db79-4143-89f9-2b42fbea06bd', 1, '2025-05-20 06:13:35'),
('e3e6a3de-c21a-4bc6-88d5-87620bc9eaea', 'efe3f73f-3a49-4e85-a0cd-7e94d33d6231', '5aece05b-9c59-4c41-b12f-8ceb8f25fd63', 1, '2025-06-24 00:44:22'),
('e64dbf6e-ff92-4e5d-9b88-bf60c45a3a67', 'a34537bb-801b-45f1-aa34-f7b3b1f13064', 'fda15ece-1a00-4583-b354-cb5f3c01bb23', 1, '2025-09-26 03:29:19'),
('e88c5ded-e9be-4b3a-ae9d-68b1b5a16727', '7b578d51-8794-46f4-8a3a-f1da2429e855', '360a7a11-6bcd-4301-8156-b4d11ebd6794', 1, '2025-06-13 08:59:54'),
('e9bcdb6f-8770-4543-b7a8-15edbabaee8d', '0781e56d-1e40-4dec-8b65-0bd316277935', '34ea3368-fa1c-445a-aeb8-821c87086d3a', 1, '2025-10-25 14:59:06'),
('ea53fabb-dec2-4df6-8a3e-6fd39abf2908', '284033fa-5e82-48be-a26e-60f91dd0b65f', '0f80acd4-d034-4175-b501-f879a9e203de', 1, '2025-06-16 03:36:24'),
('ea72b9bb-26e1-4a3e-80ea-fbdb3c15b9f2', '98ae844c-7b83-4b19-bd1a-5cc769b4d5a3', 'fb683856-9635-4316-ad3a-2eb57d6eb10f', 1, '2025-10-27 04:28:12'),
('ecb98b43-08ad-49a2-9468-71a490088e9a', '6ad9b333-0acc-410b-b5b0-7c6c9497d9be', '1fb0fb81-4482-438a-ab66-5472c52bf9e4', 1, '2025-09-26 03:47:10'),
('f02cacec-7b38-475f-bc8f-9a3986762f62', '50e8b22c-fe04-4594-adff-d5a664f64c4b', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:04:51'),
('f35bea0b-8e69-4fa5-aca3-8c5ca4b4bb75', '4ce93953-2518-44e8-9380-e55008c39155', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-03-19 09:49:26'),
('f35c32cf-ad18-41cd-9f63-cecdefda745a', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'fb683856-9635-4316-ad3a-2eb57d6eb10f', 1, '2025-10-25 14:57:40'),
('f381024e-b6d0-4de3-8871-13863eefc2e7', 'c1eeffcd-ceca-46dc-9b09-dae4d6d00091', '1fb0fb81-4482-438a-ab66-5472c52bf9e4', 1, '2025-10-27 05:25:16'),
('f3e181c0-0bfc-466f-9f6e-a4a9b325adf2', 'de838089-b8cd-4964-a05d-c49c19422cb1', 'd4efc031-32d4-487f-87ff-69afe9f948e4', 1, '2025-10-27 05:31:28'),
('f976ab8b-e355-46ca-8f31-477cda0ce692', '430859b5-6511-4579-8c1e-b1d4b9179cd1', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:07:06'),
('fa4c3409-ac1e-4dfa-8517-d587377e7061', 'b922d9a1-08be-4ce3-8402-e1b1113ae430', 'c2968a16-8dea-4f07-ab94-c7d2197562fa', 1, '2025-06-10 07:43:11'),
('fb8c30f8-c85c-4f9e-aff1-ec9fb6e20d40', '78f216ec-1aad-43a2-898f-e2301ce04a05', '88d465c6-3e16-4c58-a6da-10bce309af89', 1, '2025-03-17 11:34:04'),
('fbf31815-232a-4d37-a721-0fb31af43d9e', 'f40d86bd-b4ed-4c79-a96a-7aafe7283719', '9f005cab-6ce1-4813-bafe-95be81d93b1d', 1, '2025-03-17 03:10:54'),
('fc10e3ee-8c8a-45ec-a5e4-b5d0274da190', '925f7e0d-2d9c-4751-85fc-1222c37ee500', 'ed0e2ae8-3fe0-4df6-a04c-f07019cac0ca', 1, '2025-06-13 04:10:22'),
('fc9fc960-8afe-40af-95f9-8e99034ec9e7', '650ab423-adbd-42ec-9baf-8ae7dc8e2d81', '466cca72-833b-4631-80f5-1cafdf402375', 1, '2025-07-03 14:30:46'),
('fcf88e41-a249-4931-b8f5-6ccfe38ccf6a', 'c71885a0-6f7d-46c5-8131-21adbd5eca1f', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-07-21 08:45:12'),
('fd47b840-0c6c-4330-9462-7832b64cad5d', 'a8634a06-fac7-43c5-bae1-f1b24d7509aa', 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', 1, '2025-06-10 09:35:10'),
('fe4e2428-ef21-409e-abbc-15855076d9dd', 'a4a2747c-5e78-4196-85d6-22603ccb03b4', '0a462754-178e-4f0c-a510-d9dd40db6490', 1, '2025-10-25 15:07:44');

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

--
-- Dumping data for table `project_discussions`
--

INSERT INTO `project_discussions` (`discussion_id`, `project_id`, `user_id`, `message_text`, `is_edited`, `is_deleted`, `created_at`, `updated_at`) VALUES
('ea200fd0-85ac-402e-b157-4b42bb5feb3d', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'ข้อมูลการเข้าถึงระบบเก่า \r\nhttps://ps07.zwhhosting.com:2083/\r\nUsername :  zscfopsz\r\nPassword :  ***********', 0, 0, '2025-10-24 07:48:18', '2025-10-24 07:48:18');

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

--
-- Dumping data for table `project_documents`
--

INSERT INTO `project_documents` (`document_id`, `project_id`, `document_name`, `document_type`, `file_path`, `file_size`, `upload_date`, `uploaded_by`) VALUES
('0412c148-9070-4004-bd70-e9930742eaad', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'SO', 'pdf', '../../uploads/project_documents/Innovation/ea072d02-f6d1-42b2-bdf9-9451bb5eff3f/670f3fffd9c53.pdf', 124502, '2024-10-15 21:24:31', '3'),
('08987325-ce6b-45dd-a168-a432909467cd', 'b70608c1-6f57-4abd-bce0-9260962b0bb9', 'ทดสอบ', 'pdf', '../../uploads/project_documents/Innovation_PIT/b70608c1-6f57-4abd-bce0-9260962b0bb9/6785224941598.pdf', 524162, '2025-01-13 14:25:13', '2'),
('160d4f57-a72f-4c81-8047-51d8ec59de10', '92a99359-0555-4ff1-9be4-c26808189158', 'QT-000001069-ศรีษะเกษ', 'pdf', '../../uploads/project_documents/Point_IT/92a99359-0555-4ff1-9be4-c26808189158/684f6b0d33f51.pdf', 72841, '2025-06-16 00:53:33', '5'),
('1ef535cb-52a6-43ef-b49c-f79abc9e253c', 'ec3b8fe6-72ec-44d3-92b3-40f8ee0bee87', 'ใบเสนอราคา B บริษัทโครงการเช่าใช้บริการระ', 'xlsx', '../../uploads/project_documents/Innovation_PIT/ec3b8fe6-72ec-44d3-92b3-40f8ee0bee87/68f1e476bda38.xlsx', 21763, '2025-10-17 06:38:46', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('6f890a3b-c869-4e36-8eee-e07383ddc69d', 'a313d528-c1ea-49d7-8bde-f9b1920a4993', 'ใบเสนอราคา MA - ยโสธร QT-000001055', 'pdf', '../../uploads/project_documents/Point_IT/a313d528-c1ea-49d7-8bde-f9b1920a4993/684f695ac413c.pdf', 68855, '2025-06-16 00:46:18', '5'),
('7425cc94-9577-4b28-8859-c910888f42e0', '92a99359-0555-4ff1-9be4-c26808189158', 'SO  7 สาขา - CPF', 'xlsx', '../../uploads/project_documents/Point_IT/92a99359-0555-4ff1-9be4-c26808189158/684f6cc157cec.xlsx', 911181, '2025-06-16 01:00:49', '5'),
('800f3ddb-5289-417b-9f3c-850f5a5838b4', '51e62f2e-3b91-44e8-9875-55239e0e8acc', 'PO Inspire', 'pdf', '../../uploads/project_documents/Point_IT/51e62f2e-3b91-44e8-9875-55239e0e8acc/684f675c0243e.pdf', 142714, '2025-06-16 00:37:48', '5'),
('9e3c19d7-4bc8-47b7-a770-0e7acbf27baa', '66c0508f-b34e-4007-938b-2a1dc2f7e297', 'ใบเสนอราคา Carcass Grading AI Automation', 'pdf', '../../uploads/project_documents/Point_IT/66c0508f-b34e-4007-938b-2a1dc2f7e297/684f6dc1a81fa.pdf', 72841, '2025-06-16 01:05:05', '5'),
('e9d6154e-1ccf-4554-a53c-f4a772ec1400', 'ad862d94-87fb-4be9-b37a-5ac08b2b8b7f', 'สัญญาโครงการ', 'pdf', '../../uploads/project_documents/Zoom/ad862d94-87fb-4be9-b37a-5ac08b2b8b7f/68ff33a9e6084.pdf', 26674676, '2025-10-27 08:56:09', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f');

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

--
-- Dumping data for table `project_images`
--

INSERT INTO `project_images` (`image_id`, `project_id`, `image_name`, `file_path`, `file_type`, `file_size`, `upload_date`, `uploaded_by`, `description`) VALUES
('16531036-05da-400a-b579-cf93b4e56e31', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'LINE_ALBUM_งานติดตั้งอุปกรณ์ aitracker เทศบาลปากท่อ จ.ร.jpg', '../../uploads/project_images/Innovation/ea072d02-f6d1-42b2-bdf9-9451bb5eff3f/670f40da5ac76.jpg', 'image/jpeg', 291125, '2024-10-15 21:28:10', '3', NULL),
('e803c46a-0d19-44d4-9c9d-a3226daf048b', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'S__6987801.jpg', '../../uploads/project_images/Innovation/ea072d02-f6d1-42b2-bdf9-9451bb5eff3f/670f4189888ef.jpg', 'image/jpeg', 460243, '2024-10-15 21:31:05', '3', NULL);

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
-- Dumping data for table `project_members`
--

INSERT INTO `project_members` (`member_id`, `project_id`, `user_id`, `role_id`, `is_active`, `joined_date`, `left_date`, `created_by`, `created_at`, `updated_by`, `updated_at`, `remark`) VALUES
('4b68fa62-1f2e-11f0-a04a-005056b8f6d0', '26b7618c-cba9-47bd-a7f5-026e193dd543', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '91e08e89-d8a9-11ef-8216-005056b8f6d0', 1, '2025-04-22 04:00:14', NULL, '3', '2025-04-22 04:00:14', NULL, NULL, NULL),
('4b9d807c-4a4f-11f0-bc6e-005056b8f6d0', 'f68d2b0e-6ba9-468a-bd47-870036ce545d', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '91dec191-d8a9-11ef-8216-005056b8f6d0', 0, '2025-06-16 01:14:49', '2025-06-16 01:14:56', '5', '2025-06-16 01:14:49', '5', '2025-06-16 01:14:56', NULL),
('60ec2fd4-4a4f-11f0-bc6e-005056b8f6d0', 'f68d2b0e-6ba9-468a-bd47-870036ce545d', 'a5741799-938b-4d0a-a3dc-4ca1aa164708', '91e086ae-d8a9-11ef-8216-005056b8f6d0', 0, '2025-06-16 01:15:25', '2025-06-16 01:15:33', '5', '2025-06-16 01:15:25', '5', '2025-06-16 01:15:33', NULL),
('648fa312-4a56-11f0-bc6e-005056b8f6d0', '66c0508f-b34e-4007-938b-2a1dc2f7e297', '3', '91e086ae-d8a9-11ef-8216-005056b8f6d0', 1, '2025-06-16 02:05:37', NULL, '5', '2025-06-16 02:05:37', '5', '2025-06-16 02:05:53', NULL),
('74121b6a-4a4f-11f0-bc6e-005056b8f6d0', 'f68d2b0e-6ba9-468a-bd47-870036ce545d', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '91e086ae-d8a9-11ef-8216-005056b8f6d0', 1, '2025-06-16 01:15:57', NULL, '5', '2025-06-16 01:15:57', NULL, NULL, NULL),
('8ea635b7-4a48-11f0-bc6e-005056b8f6d0', '51e62f2e-3b91-44e8-9875-55239e0e8acc', '3', '91e086ae-d8a9-11ef-8216-005056b8f6d0', 0, '2025-06-16 00:26:35', '2025-06-16 00:27:16', '5', '2025-06-16 00:26:35', '5', '2025-06-16 00:27:16', NULL),
('af2694eb-d99b-11ef-8216-005056b8f6d0', '6f882833-ac9e-4367-8468-ebb05dd81a8e', 'f30e8b87-d047-4bca-9b34-d223170df87c', '91e08c3d-d8a9-11ef-8216-005056b8f6d0', 1, '2025-01-23 15:07:00', NULL, '2', '2025-01-23 15:07:00', NULL, NULL, NULL),
('e774b9d8-4bec-11f0-bc6e-005056b8f6d0', '66c0508f-b34e-4007-938b-2a1dc2f7e297', '5b698e22-ba83-43c4-a39e-e6d68f98791f', '91e09076-d8a9-11ef-8216-005056b8f6d0', 2, '2025-06-18 02:35:34', NULL, '2', '2025-06-18 02:35:34', '2', '2025-06-18 09:13:25', NULL),
('fe3f3aed-d984-11ef-8216-005056b8f6d0', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', '3', '91e086ae-d8a9-11ef-8216-005056b8f6d0', 1, '2025-01-23 12:24:34', NULL, '2', '2025-01-23 12:24:34', NULL, NULL, NULL);

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

--
-- Dumping data for table `project_payments`
--

INSERT INTO `project_payments` (`payment_id`, `project_id`, `payment_number`, `amount`, `payment_percentage`, `amount_paid`, `payment_date`, `due_date`, `status`, `payment_method`, `transaction_id`, `remark`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
('089576ed-ce30-4e64-964c-bda4c7820cac', 'adac6142-24d1-4efe-a469-859c1cb11243', 2, 597196.26, 30.00, 0.00, NULL, '2026-05-31', 'Pending', NULL, NULL, NULL, '2025-10-27 07:37:03', '2025-10-27 07:37:03', 'ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', ''),
('09365047-accb-4b5f-96ab-ce681cc2922b', '4677b262-6fc0-4bc7-8708-a0806b091577', 1, 3128037.38, 100.00, 3128037.38, '2024-10-06', '2024-10-06', 'Paid', NULL, NULL, NULL, '2025-10-27 05:21:12', '2025-10-27 05:21:12', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('14b58fd0-940f-4201-bf69-9d73fffedca7', '97db86a0-878d-4627-b410-316f1dda7152', 2, 37383.18, 50.00, 74766.36, '2025-09-30', '2025-09-30', 'Paid', NULL, NULL, NULL, '2025-10-27 07:58:57', '2025-10-27 07:58:57', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('15f67018-2f9f-44c8-89eb-6d84bb0834c1', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 3, 88347.95, 7.69, 88347.95, NULL, '2024-09-01', 'Paid', NULL, NULL, NULL, '2024-10-15 22:05:06', '2024-10-15 22:05:06', '3', ''),
('17777793-1c36-48f7-b9fc-db6ceaefe004', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 9, 88347.95, 7.69, 0.00, NULL, '2025-03-03', 'Pending', NULL, NULL, NULL, '2024-10-15 22:07:13', '2024-10-15 22:07:13', '3', ''),
('1807a92b-30a2-4e30-a077-f92d2d1b32da', '05d29d2b-39ab-4c46-b34b-801ede800172', 4, 960000.00, 30.00, 0.00, NULL, '2024-12-30', 'Pending', NULL, NULL, NULL, '2024-10-17 04:56:25', '2024-10-18 11:38:02', '3', '2'),
('1951f248-505f-4115-993c-3f888d081b66', 'bcde4847-acf8-4fb7-8d6b-e9ff9f5e7a62', 3, 112149.53, 25.00, 0.00, NULL, '2026-06-30', 'Pending', NULL, NULL, NULL, '2025-10-27 08:03:52', '2025-10-27 08:03:52', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('1bcabad2-13a1-494e-bcce-290f413bfa91', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 1, 88347.95, 7.69, 88347.95, NULL, '2024-07-01', 'Paid', NULL, NULL, NULL, '2024-10-15 22:03:09', '2024-10-15 22:03:09', '3', ''),
('27bcb4f4-2891-4789-8a96-c525cf99b9c5', 'ad862d94-87fb-4be9-b37a-5ac08b2b8b7f', 1, 3476635.51, 60.00, 0.00, NULL, '2025-12-09', 'Pending', NULL, NULL, NULL, '2025-09-26 03:37:15', '2025-10-27 04:57:59', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f'),
('3284d9f1-cc63-4238-b749-57e320b8b735', 'c1eeffcd-ceca-46dc-9b09-dae4d6d00091', 1, 4635514.02, 100.00, 4635514.02, NULL, '0000-00-00', 'Paid', NULL, NULL, NULL, '2025-10-27 05:25:32', '2025-10-27 05:25:32', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('407de0fe-bee6-4de7-b57f-d22e5d6d18dd', 'adac6142-24d1-4efe-a469-859c1cb11243', 1, 597196.26, 30.00, 0.00, NULL, '2026-01-31', 'Pending', NULL, NULL, NULL, '2025-10-27 07:36:44', '2025-10-27 07:36:44', 'ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', ''),
('48faba38-dd18-445c-b045-18ac3e9ed1c0', '9f8238dc-3e88-4d57-93f4-bdae13ec301b', 1, 417757.01, 100.00, 417757.01, '2025-07-07', '2025-07-07', 'Paid', NULL, NULL, NULL, '2025-10-27 07:43:13', '2025-10-27 07:43:13', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('496db098-563c-44ab-a206-407cb5c51bee', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 13, 88347.95, 7.69, 0.00, NULL, '2025-07-01', 'Pending', NULL, NULL, NULL, '2024-10-15 22:08:37', '2024-10-15 22:08:37', '3', ''),
('4bed82ad-6a45-4b9d-94ff-045eea98acf4', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 1, 266822.43, 100.00, 0.00, NULL, '2024-10-31', 'Pending', NULL, NULL, NULL, '2024-10-19 02:38:58', '2024-10-19 02:38:58', '2', ''),
('4ce991cb-589b-4475-a7fe-ccbea0a47235', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 6, 88347.95, 7.69, 0.00, NULL, '2024-12-02', 'Pending', NULL, NULL, NULL, '2024-10-15 22:06:29', '2024-10-15 22:06:29', '3', ''),
('51f490d6-9d64-4fe6-ac57-940e30d1fbd2', 'bcde4847-acf8-4fb7-8d6b-e9ff9f5e7a62', 4, 112149.53, 25.00, 0.00, NULL, '2026-09-30', 'Pending', NULL, NULL, NULL, '2025-10-27 08:04:14', '2025-10-27 08:04:31', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f'),
('6061a5c2-5637-46e1-9794-ba9343bdf178', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 12, 88347.95, 7.69, 0.00, NULL, '2025-06-02', 'Pending', NULL, NULL, NULL, '2024-10-15 22:08:04', '2024-10-15 22:08:04', '3', ''),
('628f80c8-ed91-4d65-978e-371ad84a167d', '49b9dd79-d94d-45c9-8645-cf4caaab398a', 1, 732710.28, 100.00, 732710.28, NULL, '0000-00-00', 'Paid', NULL, NULL, NULL, '2025-10-27 05:26:03', '2025-10-27 05:26:03', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('65c1021b-79d0-4998-971e-c1a186dbf4ab', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 8, 88347.95, 7.69, 0.00, NULL, '2025-02-03', 'Pending', NULL, NULL, NULL, '2024-10-15 22:06:58', '2024-10-15 22:06:58', '3', ''),
('67a81765-984e-451c-a42c-13ee3bab750d', '819456c1-3df2-41b4-874f-377b4d2ecca4', 2, 18742990.65, 35.00, 0.00, NULL, '2026-02-17', 'Pending', NULL, NULL, NULL, '2025-10-27 04:34:28', '2025-10-27 04:34:28', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('67fa6196-f481-4a71-ad5d-75b442e0e5e7', 'ad862d94-87fb-4be9-b37a-5ac08b2b8b7f', 2, 2317757.01, 40.00, 0.00, NULL, '2026-02-07', 'Pending', NULL, NULL, NULL, '2025-10-27 04:55:43', '2025-10-27 04:58:13', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f'),
('74ff0504-3f23-4685-a21c-1e236437e269', '819456c1-3df2-41b4-874f-377b4d2ecca4', 4, 5355140.19, 10.00, 0.00, NULL, '2026-05-18', 'Pending', NULL, NULL, NULL, '2025-10-27 04:35:43', '2025-10-27 04:35:43', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('759c671b-b7e6-4272-9063-db937e314ac7', 'ae8cd8e6-3101-4adf-a234-5d0fe550230b', 2, 4481308.41, 35.00, 10242990.65, '2025-03-27', '2025-03-27', 'Paid', NULL, NULL, NULL, '2025-10-27 05:06:43', '2025-10-27 05:06:43', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('75b4e432-a182-4d4f-bac9-913da963ccb8', 'ae8cd8e6-3101-4adf-a234-5d0fe550230b', 3, 2560747.66, 20.00, 12803738.31, '2025-04-26', '2025-04-26', 'Paid', NULL, NULL, NULL, '2025-10-27 05:07:17', '2025-10-27 05:07:17', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('77eb282e-9b2a-4b9a-9d0c-d702b6a3c0c3', '4d5ab32c-8838-4780-bde8-a974adc31874', 3, 362616.82, 20.00, 1813084.11, '2025-07-23', '2025-07-23', 'Paid', NULL, NULL, NULL, '2025-10-27 05:19:03', '2025-10-27 05:19:03', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('7a2f8e4b-f32c-4dde-b247-357d86b9bec8', '97db86a0-878d-4627-b410-316f1dda7152', 1, 37383.18, 50.00, 37383.18, '2025-08-31', '2025-08-31', 'Paid', NULL, NULL, NULL, '2025-10-27 07:58:34', '2025-10-27 07:58:34', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('81c18b44-e9d7-45bf-bd52-0e86a7931d86', '464369d4-5720-4b09-9834-8e46884ab187', 1, 1860000.00, 100.00, 1860000.00, NULL, '0000-00-00', 'Paid', NULL, NULL, NULL, '2025-10-27 05:31:04', '2025-10-27 05:31:04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('82d3348c-f211-4781-827a-0191cd799cbb', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 7, 88347.95, 7.69, 0.00, NULL, '2025-01-01', 'Pending', NULL, NULL, NULL, '2024-10-15 22:06:44', '2024-10-15 22:06:44', '3', ''),
('845218d2-d0dd-48d4-ba93-989d6c4ba946', 'bcde4847-acf8-4fb7-8d6b-e9ff9f5e7a62', 1, 112149.53, 25.00, 0.00, NULL, '2025-12-31', 'Pending', NULL, NULL, NULL, '2025-10-27 08:02:54', '2025-10-27 08:02:54', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('85dd68d7-de46-4e81-99f8-4414590503ac', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 4, 88347.95, 7.69, 88347.95, NULL, '2024-10-01', 'Paid', NULL, NULL, NULL, '2024-10-15 22:05:34', '2024-10-15 22:05:34', '3', ''),
('8937df61-8c93-48c6-8984-28b6493ffdaf', '05d29d2b-39ab-4c46-b34b-801ede800172', 2, 640000.00, 20.00, 0.00, NULL, '2024-08-01', 'Pending', NULL, NULL, NULL, '2024-10-17 04:55:42', '2024-10-18 04:19:17', '3', '3'),
('8c023e2d-ca7f-4d1c-84c6-1bc4cc67ff5b', '819456c1-3df2-41b4-874f-377b4d2ecca4', 5, 8032710.28, 15.00, 0.00, NULL, '2026-06-07', 'Pending', NULL, NULL, NULL, '2025-10-27 04:36:10', '2025-10-27 04:36:10', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('8efee306-0748-44f7-9575-fe2ecb19d709', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 5, 88347.95, 7.69, 0.00, NULL, '2024-11-01', 'Pending', NULL, NULL, NULL, '2024-10-15 22:06:08', '2024-10-15 22:06:08', '3', ''),
('93d69544-83ce-415c-8aaa-eb159d4c969e', 'f04f1583-d0bc-4d89-b868-785f3efd07de', 1, 152000.00, 100.00, 152000.00, '2025-07-07', '2025-07-07', 'Paid', NULL, NULL, NULL, '2025-10-27 07:48:46', '2025-10-27 07:48:46', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('99c8f5d0-4c1e-47d0-b390-04e557dc9277', 'bcde4847-acf8-4fb7-8d6b-e9ff9f5e7a62', 2, 112149.53, 25.00, 0.00, NULL, '2026-03-31', 'Pending', NULL, NULL, NULL, '2025-10-27 08:03:22', '2025-10-27 08:03:22', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('a07d90d3-9546-4c27-ba33-0779bce0496b', 'de838089-b8cd-4964-a05d-c49c19422cb1', 1, 38258.36, 100.00, 38258.36, NULL, '0000-00-00', 'Paid', NULL, NULL, NULL, '2025-10-27 05:31:45', '2025-10-27 05:31:45', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('a229e370-811d-408d-ad8c-bf638ff054e8', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 11, 88347.95, 7.69, 0.00, NULL, '2025-05-01', 'Pending', NULL, NULL, NULL, '2024-10-15 22:07:44', '2024-10-15 22:07:44', '3', ''),
('a4486a8f-8548-4ce3-b091-c3d55c0657f3', '6ad9b333-0acc-410b-b5b0-7c6c9497d9be', 1, 5792523.37, 50.00, 0.00, NULL, '2025-12-01', 'Pending', NULL, NULL, NULL, '2025-09-26 03:48:21', '2025-09-26 03:48:21', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('b3ae6692-1ae7-4c27-bc03-0c5b401f85f5', '05d29d2b-39ab-4c46-b34b-801ede800172', 3, 1120000.00, 35.00, 0.00, NULL, '2024-09-02', 'Pending', NULL, NULL, NULL, '2024-10-17 04:56:02', '2024-10-18 04:19:28', '3', '3'),
('b4f2502e-e9a0-438b-a1d4-1d48099e6ced', '819456c1-3df2-41b4-874f-377b4d2ecca4', 3, 5355140.19, 10.00, 0.00, NULL, '2026-03-19', 'Pending', NULL, NULL, NULL, '2025-10-27 04:35:05', '2025-10-27 04:35:05', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('b76b3d7a-3559-4b8c-93a8-b3dbae92ca48', '05d29d2b-39ab-4c46-b34b-801ede800172', 1, 480000.00, 15.00, 480000.00, NULL, '2024-02-01', 'Paid', NULL, NULL, NULL, '2024-10-18 02:49:48', '2024-10-18 04:18:52', '3', '3'),
('b7b50966-c6d0-46ce-8e43-81052cb8e3ce', '7c67ce7e-ee05-487f-a763-4627899516bb', 1, 71690.00, 10.00, 0.00, NULL, '0000-00-00', 'Pending', NULL, NULL, NULL, '2024-10-14 04:59:13', '2024-10-14 09:54:16', '2', '3'),
('be6a8d19-6410-44cd-962b-a795ef3f29c6', '4d5ab32c-8838-4780-bde8-a974adc31874', 2, 634579.44, 35.00, 1450467.29, '2025-05-09', '2025-05-09', 'Paid', NULL, NULL, NULL, '2025-10-27 05:18:20', '2025-10-27 05:18:20', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('c096b705-b4fe-43c0-9040-a6a2f298af77', '7c67ce7e-ee05-487f-a763-4627899516bb', 2, 55488.06, 7.74, 0.00, NULL, '0000-00-00', 'Pending', NULL, NULL, NULL, '2024-10-14 09:43:05', '2024-10-14 09:55:15', '3', '3'),
('c0b88c30-e2b1-425a-bab3-c41eef23470c', '6ad9b333-0acc-410b-b5b0-7c6c9497d9be', 2, 5792523.37, 50.00, 0.00, NULL, '2026-03-01', 'Pending', NULL, NULL, NULL, '2025-09-26 03:48:21', '2025-09-26 03:50:45', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f'),
('c1a72926-f8af-48b0-a9ed-2431c2a7c9cb', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 10, 88347.95, 7.69, 0.00, NULL, '2025-04-01', 'Pending', NULL, NULL, NULL, '2024-10-15 22:07:27', '2024-10-15 22:07:27', '3', ''),
('c725bf71-e0c8-43f1-9763-1b15ff94166c', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 2, 88347.95, 7.69, 88347.95, NULL, '2024-08-01', 'Paid', NULL, NULL, NULL, '2024-10-15 22:04:10', '2024-10-15 22:04:10', '3', ''),
('cb7badd3-57cb-4a9f-8ef8-4e2aca9e375e', 'ae8cd8e6-3101-4adf-a234-5d0fe550230b', 1, 5761682.24, 45.00, 5761682.24, '2025-10-26', '2025-01-26', 'Paid', NULL, NULL, NULL, '2025-10-27 05:06:03', '2025-10-27 05:06:03', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('cc42caec-9a4f-4bd1-93c8-5991b6f78d41', 'a3fa105e-b258-474a-87e5-e39272e3f127', 1, 2373831.78, 100.00, 2373831.78, '2025-10-08', '2025-06-08', 'Paid', NULL, NULL, NULL, '2025-10-27 05:03:48', '2025-10-27 05:03:48', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('d171aa72-b393-4c98-af98-7785a82b5866', '02f2b2b7-c67e-4255-95fe-247196b92206', 1, 60943.93, 100.00, 60943.93, '2025-08-15', '2025-08-15', 'Paid', NULL, NULL, NULL, '2025-10-27 07:52:17', '2025-10-27 07:52:17', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('d583ab71-8f89-4d7b-9d31-259a3604800a', 'adac6142-24d1-4efe-a469-859c1cb11243', 3, 796261.68, 40.00, 0.00, NULL, '2026-09-30', 'Pending', NULL, NULL, NULL, '2025-10-27 07:37:25', '2025-10-27 07:37:25', 'ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', ''),
('d6888ef9-803a-4443-8afc-23ad3e718b59', '3cef16ff-6bf2-4f07-8d0c-cdc2fb0bb4f9', 1, 2471028.04, 100.00, 2471028.04, NULL, '0000-00-00', 'Paid', NULL, NULL, NULL, '2025-10-27 05:40:11', '2025-10-27 05:40:11', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('d708ddb5-f9dd-43b6-982a-660f3e581a23', '4d5ab32c-8838-4780-bde8-a974adc31874', 1, 815887.85, 45.00, 815887.85, '2025-03-10', '2025-03-10', 'Paid', NULL, NULL, NULL, '2025-10-27 05:17:46', '2025-10-27 05:17:46', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('f23e9a67-8ade-499c-ad51-121ebd3f36ec', '819456c1-3df2-41b4-874f-377b4d2ecca4', 1, 16065420.56, 30.00, 0.00, NULL, '2026-01-18', 'Pending', NULL, NULL, NULL, '2025-10-27 04:33:47', '2025-10-27 04:33:47', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('f5b873d0-5ba5-414e-b27b-61a3c14ad06f', 'ba49000b-3509-4377-8d0f-456286a45e5f', 1, 8037383.18, 100.00, 0.00, NULL, '2026-03-14', 'Pending', NULL, NULL, NULL, '2025-09-26 03:41:53', '2025-09-26 03:41:53', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', ''),
('fccae2cf-ddc3-4298-8347-d41647c4528a', '4ab1ec63-fe78-4c3f-b039-1870bd5ad987', 1, 3568000.00, 100.00, 3568000.00, NULL, '0000-00-00', 'Paid', NULL, NULL, NULL, '2025-10-27 05:32:53', '2025-10-27 05:32:53', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '');

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

--
-- Dumping data for table `project_roles`
--

INSERT INTO `project_roles` (`role_id`, `role_name`, `role_description`, `created_by`, `created_at`, `updated_by`, `updated_at`) VALUES
('91dec191-d8a9-11ef-8216-005056b8f6d0', 'Project Manager', 'ผู้จัดการโครงการ - รับผิดชอบการบริหารจัดการโครงการโดยรวม', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e086ae-d8a9-11ef-8216-005056b8f6d0', 'Project Coordinator', 'ผู้ประสานงานโครงการ - ประสานงานระหว่างทีมและลูกค้า', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e08a79-d8a9-11ef-8216-005056b8f6d0', 'Developer', 'นักพัฒนาระบบ - พัฒนาระบบตามความต้องการของโครงการ', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e08b6b-d8a9-11ef-8216-005056b8f6d0', 'System Analyst', 'นักวิเคราะห์ระบบ - วิเคราะห์และออกแบบระบบ', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e08c3d-d8a9-11ef-8216-005056b8f6d0', 'Tester', 'ผู้ทดสอบระบบ - ทดสอบระบบตามข้อกำหนด', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e08d01-d8a9-11ef-8216-005056b8f6d0', 'System Engineer', 'วิศวกรระบบ - ดูแลและติดตั้งระบบ', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e08dbc-d8a9-11ef-8216-005056b8f6d0', 'Support', 'ผู้ดูแลระบบ - ให้การสนับสนุนและแก้ไขปัญหา', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e08e89-d8a9-11ef-8216-005056b8f6d0', 'Business Analyst', 'นักวิเคราะห์ธุรกิจ - วิเคราะห์ความต้องการทางธุรกิจ', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e08f50-d8a9-11ef-8216-005056b8f6d0', 'QA Engineer', 'วิศวกรประกันคุณภาพ - ควบคุมคุณภาพของระบบ', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e09076-d8a9-11ef-8216-005056b8f6d0', 'Technical Lead', 'ผู้นำด้านเทคนิค - กำหนดแนวทางเทคนิคของโครงการ', '1', '2025-01-22 10:13:51', NULL, NULL),
('be76042b-d8a9-11ef-8216-005056b8f6d0', 'UX/UI Designer', NULL, '2', '2025-01-22 10:15:06', NULL, NULL);

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

--
-- Dumping data for table `project_tasks`
--

INSERT INTO `project_tasks` (`task_id`, `project_id`, `parent_task_id`, `task_name`, `description`, `start_date`, `end_date`, `status`, `progress`, `priority`, `created_by`, `created_at`, `updated_by`, `updated_at`, `task_order`, `task_level`) VALUES
('0db715cb-32d5-402d-bc84-fdea13cef6bf', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', NULL, '1. Microcontroller Design', 'Microcontroler Desing พี่ซีนประสานงานพี่ตุ้ม ดังนี้ \n1. ออกแบบกล่อง\n2. นำเข้าบอร์ด ER32 \n3. Screen Brand ติดกล่อง ', '2025-01-23', '2025-01-27', 'In Progress', 50.00, 'Medium', '3', '2025-01-23 12:29:25', '3', '2025-01-24 09:03:35', 1, 0),
('2318643a-04fd-40de-a557-4563f81152f5', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', '3881ba5c-1db3-4aa4-ac0f-6de1fa1950e8', 'DEV-8 : User Management Module', 'จัดการผู้ใช้งานและ Role Mapping', '2025-07-06', '2025-07-08', 'Pending', 0.00, 'High', '2', '2025-06-18 06:56:30', NULL, NULL, 11, 0),
('249f6394-09d9-4756-a4aa-95549b40c00d', '078a1fd1-f8bb-4c94-86d5-d35fbf00d1bb', NULL, 'รอประกาศผลสิงหาคม. 2568', '', '2025-08-01', '2025-08-05', 'In Progress', 0.00, 'High', '5', '2025-06-16 06:50:12', NULL, NULL, 1, 0),
('2bac2237-e8e7-4f36-92fc-159f784be5c9', '51e62f2e-3b91-44e8-9875-55239e0e8acc', NULL, 'นัดประชุมเรื่องการเปิดบิล', 'รอนัดคุณหมอ บอย และพี่ไฟท์', '2025-06-18', '2025-06-18', 'In Progress', 0.00, 'High', '5', '2025-06-16 00:40:56', NULL, NULL, 1, 0),
('3881ba5c-1db3-4aa4-ac0f-6de1fa1950e8', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', NULL, 'Sub-tasks Development', 'Sub-tasks สำหรับ Development', '2025-06-18', '2025-08-15', 'In Progress', 0.00, 'High', '2', '2025-06-18 05:24:51', '2', '2025-06-18 06:58:28', 8, 1),
('3b1e5127-69e4-466c-ba57-15431a3293f4', '66c0508f-b34e-4007-938b-2a1dc2f7e297', '7911293c-bfa0-40e3-9910-ec1e38a0a96d', 'ค่าบริการงบสำรวจ', '1 วัน 2 คน\nค่าตั๋วเครื่องบินไป-กลับอุบล 2 ที่นั่ง\nค่าเช่ารถยนต์\nค่าเบี้ยเลี้ยง 2 คน', '2025-06-20', '2025-06-20', 'Pending', 0.00, 'Urgent', '5', '2025-06-18 02:36:27', NULL, NULL, 2, 0),
('3f2b8902-0d5b-40a6-87e4-5cffc9f27e44', 'b403c2e1-8913-4e94-b6c8-b9f7f36a4f31', NULL, 'รอ Confirm PO Q3', '', '0000-00-00', '0000-00-00', 'Pending', 0.00, 'High', '5', '2025-06-16 07:05:39', NULL, NULL, 1, 0),
('5612a8da-3d60-47b5-baec-b492f51b0acf', '92a99359-0555-4ff1-9be4-c26808189158', NULL, 'ติดตามงานขาย', '', '0000-00-00', '0000-00-00', 'Pending', 0.00, 'Low', '5', '2025-06-16 01:07:59', NULL, NULL, 1, 0),
('5ac64b8d-5f9f-405b-b191-14fc687c6345', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', '3881ba5c-1db3-4aa4-ac0f-6de1fa1950e8', 'DEV-01 : Setup Development Environment', 'จัดเตรียม Environment', '2025-06-20', '2025-06-22', 'Pending', 0.00, 'High', '2', '2025-06-18 06:43:45', '2', '2025-06-18 06:54:50', 1, 1),
('6e5daf27-4ec3-407d-a67b-19950443e2db', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', '3881ba5c-1db3-4aa4-ac0f-6de1fa1950e8', 'DEV-9 : Role & Permission Management', 'ตั้งค่า Role และ Permission ใน Keycloak', '2025-07-09', '2025-07-10', 'Pending', 0.00, 'High', '2', '2025-06-18 06:58:28', NULL, NULL, 12, 0),
('7911293c-bfa0-40e3-9910-ec1e38a0a96d', '66c0508f-b34e-4007-938b-2a1dc2f7e297', NULL, 'ติดตามงานขาย-CPF ศรีษะเกษ กำลังดำเนอนการออก PO ', 'คุณจุ๊ Zoetis ให้ขึ้นไปสำรวจจุดติดตั้ง\n@JACK_INTERNAL @zeen  \nสรุปการเดินทางไป Survey การติดตั้งชุดวัดเกรดซาก BF2 ที่ศรีษะเกษ  \nทีม​ POINT IT ไป 2 คน  คือพี่แจ๊ค และน้องซีน  นัดพบน้องพลอยที่ โรงงาน  วันศุกร์ที่ 20  เช้านี้พี่จะให้ทีมจัดการจองตั๋วเครื่องบินก่อน ไปอุบลและเช่ารถไปที่ โรงงาน  แจ๊คประสานน้องพลอยได้เลยนะ\n1. สำรวจจุดติดตั้ง BF2   ทั้งเครื่องวิเคราะห์ และจำเป็นต้องมีอุปกรณ์เสริมเพื่อควบคุมซากหรือไม่\n2. สำรวจ Network และ Internet\n3. จัดทำรายงานการสำรวจติดตั้ง เพื่อวางแผนกำหนดการติดตั้งระหว่างที่ทางโรงงานกำลังออก PO\n4.ขอชื่อผู้ติดต่อทางโรงงานที่ดูแลด้านระบบ Netowrk และ IT ในการประสานรายละเอียดต่อไป\nพี่หญิงจะให้ @Neit\'(Boss) เป็น Account REP ของโครงการนี้นะ\n__________________________', '2025-06-20', '2025-06-20', 'In Progress', 0.00, 'Urgent', '5', '2025-06-16 01:08:24', '5', '2025-06-18 02:36:27', 1, 0),
('793845f3-3412-43d7-8e88-81356c0dc647', 'f68d2b0e-6ba9-468a-bd47-870036ce545d', NULL, 'Project Engineer', '', '0000-00-00', '0000-00-00', 'Pending', 0.00, 'Low', '5', '2025-06-16 04:03:40', '5', '2025-06-16 04:04:02', 1, 0),
('7cc60017-fb38-4d5c-bfe6-3b5b9d4c8e18', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', '3881ba5c-1db3-4aa4-ac0f-6de1fa1950e8', 'DEV-05  :OAuth 2.0/OIDC Client Setup', 'ตั้งค่า Client Application ใน Keycloak', '2025-07-01', '2025-07-02', 'Pending', 0.00, 'High', '2', '2025-06-18 06:49:38', '2', '2025-06-18 06:54:50', 8, 0),
('7d261938-0a3e-4bb1-9197-87e090e20a57', '38fae358-df4d-41f2-8970-cb2937222dd5', NULL, '2. ประชุมทีม', '1. จัดเตรียมอุปกรณ์ HW \n2. ปรับแต่งระบบ Software ให้ครอบคลุมจำนวน HW \n3. วางแผนเข้าหน้างานติดตั้ง', '2025-10-24', '2025-10-25', 'Completed', 100.00, 'Medium', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 05:11:29', NULL, NULL, 2, 0),
('82fe0088-3c0f-46fe-a4b4-596bd24a00fa', '328cb493-4f0b-4b1f-ba09-44a443e8e752', NULL, 'กดหกหกดหกดหกด', 'หดหกดหกดกหดหกดกหด', '2025-10-01', '2025-10-10', 'Pending', 0.00, 'Low', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 08:07:27', NULL, NULL, 1, 0),
('85f47a5e-3bbd-47fc-8789-a6e0dd818163', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', '3881ba5c-1db3-4aa4-ac0f-6de1fa1950e8', 'DEV-03 : Keycloak Server Setup', '1. ติดตั้งและตั้งค่า Keycloak Server\n2. สร้างและตั้งค่า Realm สำหรับ CPAO', '2025-06-23', '2025-06-27', 'Pending', 0.00, 'High', '2', '2025-06-18 06:46:34', '2', '2025-06-18 06:54:50', 6, 2),
('954d3afb-e95d-4812-b743-4e1064c2d22a', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', '0db715cb-32d5-402d-bc84-fdea13cef6bf', '1.1. อ้ดเดทความคืบหน้าประสานงาน ดำเนินการนำเข้า ES32 และตัวกล่องบรรจุภัฑณ์', '- ประสานงานพี่ตุ๋ม ช่วยหาของภายในประเทศไทย เนื่องอุปกรณ์รองรับการใส่ Sim, WIFI จะถูกตรวจสอบ หรือใช่เวลาดำเนินการค่อนข้างนานเนื่องจากต้องผ่านการตรวจสอบผ่าน กสทช. \n- ประสานงานพี่ขวัญ หาตัว Board ไม่มี SIM , WIFI เพื่อให้ง่ายต่อการนำเข้า \n\nพี่ขวัญ จัดหาเป็นต้น : https://lilygo.cc/products/t-internet-poe?srsltid=AfmBOoq9ALNRGXzV-pSvz5DmnPz_yg9hCyBnNyIdOKIiOr8f2Gvy1vOX\nพี่ตุ้ม : ให้ทางจัดซื้อ จัดซื้อ และประสานงานผู้ผลิตตัวกล่องในการดำเนินการ และขอราคาแพ็กเก็จทั้งหมด', '2025-01-24', '2025-01-27', 'In Progress', 50.00, 'Medium', '3', '2025-01-24 09:02:43', '3', '2025-01-24 09:03:24', 0, 1),
('99b0ab2e-2c86-4b61-b1f1-964385cc5e00', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', '3881ba5c-1db3-4aa4-ac0f-6de1fa1950e8', 'DEV-02 : Database Design & Setup', 'ออกแบบและจัดตั้งฐานข้อมูล', '2025-06-23', '2025-06-25', 'Pending', 0.00, 'High', '2', '2025-06-18 06:44:58', NULL, '2025-06-18 06:54:50', 5, 2),
('a03550ba-9be0-4afd-b133-011dda745576', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', '3881ba5c-1db3-4aa4-ac0f-6de1fa1950e8', 'DEV-07 : SSO Implementation Frontend', 'Implement SSO ใน Nuxt3', '2025-07-03', '2025-07-05', 'Pending', 0.00, 'High', '2', '2025-06-18 06:52:48', NULL, '2025-06-18 06:54:50', 10, 0),
('a0ee357f-10c4-48c4-ad2c-324cc5441ac3', 'a313d528-c1ea-49d7-8bde-f9b1920a4993', NULL, 'ติดตามงานขาย', '', '0000-00-00', '0000-00-00', 'Pending', 0.00, 'Low', '5', '2025-06-16 01:07:28', NULL, NULL, 1, 0),
('dd02abc1-746d-4ea1-b750-58f7a7ec56a8', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', '3881ba5c-1db3-4aa4-ac0f-6de1fa1950e8', 'DEV-04 : LDAP Integration with Keycloak', 'เชื่อมต่อ Keycloak กับ LDAP Server', '2025-06-28', '2025-06-30', 'Pending', 0.00, 'High', '2', '2025-06-18 06:48:14', '2', '2025-06-18 06:54:50', 7, 0),
('e53302e0-1ae9-414f-858c-65b70ce0cad0', '38fae358-df4d-41f2-8970-cb2937222dd5', NULL, '1. รับ Requirment จากลูกค้า', '1. สถานที่จัดงาน\n2. จำนวนรถที่อยู่ภายในงาน\n3. Plan Booth \n4. Contact Sub', '2025-10-24', '2025-10-24', 'Completed', 100.00, 'Medium', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 05:09:14', NULL, NULL, 1, 0),
('ef547534-053a-47f8-b1fe-7288b9e26d33', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', '3881ba5c-1db3-4aa4-ac0f-6de1fa1950e8', 'DEV-06 : Authentication System Integration', 'เชื่อมต่อระบบกับ Keycloak', '2025-07-03', '2025-07-05', 'Pending', 0.00, 'High', '2', '2025-06-18 06:51:29', NULL, '2025-06-18 06:54:50', 9, 0);

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

--
-- Dumping data for table `project_task_assignments`
--

INSERT INTO `project_task_assignments` (`assignment_id`, `task_id`, `user_id`, `assigned_by`, `assigned_at`) VALUES
('15698cd6-5d86-456a-bcb8-dccef2e2a342', '6e5daf27-4ec3-407d-a67b-19950443e2db', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2', '2025-06-18 06:58:28'),
('1e16486c-334e-43d3-9b29-55514af89c6d', 'e53302e0-1ae9-414f-858c-65b70ce0cad0', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 05:09:14'),
('281fb257-fb98-41c3-bab6-a4365e6f1a07', '3f2b8902-0d5b-40a6-87e4-5cffc9f27e44', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '5', '2025-06-16 07:05:39'),
('3f1d4c5f-c024-4a8a-8e82-2b49c1a27635', 'a0ee357f-10c4-48c4-ad2c-324cc5441ac3', '3', '5', '2025-06-16 01:07:28'),
('471516ed-ef74-4b84-ba7c-e0dda9c50861', '954d3afb-e95d-4812-b743-4e1064c2d22a', '3', '3', '2025-01-24 09:03:24'),
('4f196afb-38b4-41e4-a0c3-3ecea464b8ee', '7d261938-0a3e-4bb1-9197-87e090e20a57', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 05:11:29'),
('52606af6-15e2-432e-bf73-b924a0a4b0ac', '5ac64b8d-5f9f-405b-b191-14fc687c6345', '8c782887-8fd3-4f99-ac27-63054a8a1942', '2', '2025-06-18 06:44:03'),
('66fab44b-fc0e-4cd3-857c-2ce2f338c7b2', '85f47a5e-3bbd-47fc-8789-a6e0dd818163', '8c782887-8fd3-4f99-ac27-63054a8a1942', '2', '2025-06-18 06:47:23'),
('70f09622-174f-4150-aaf0-c8335f69a15e', 'dd02abc1-746d-4ea1-b750-58f7a7ec56a8', '8c782887-8fd3-4f99-ac27-63054a8a1942', '2', '2025-06-18 06:48:37'),
('75b381dd-27da-4e8d-a5f6-34c6b8e8e848', '793845f3-3412-43d7-8e88-81356c0dc647', 'a5741799-938b-4d0a-a3dc-4ca1aa164708', '5', '2025-06-16 04:04:02'),
('799ccbe1-11e1-46cd-9923-871d3203c10b', '3881ba5c-1db3-4aa4-ac0f-6de1fa1950e8', '3', '2', '2025-06-18 05:24:51'),
('7a2e0e7d-0161-4f3a-a4f9-a497a0d47f0f', '7d261938-0a3e-4bb1-9197-87e090e20a57', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 05:11:29'),
('873bd5ba-95c7-4b13-8db1-5c9c7d0ecbfe', '2318643a-04fd-40de-a557-4563f81152f5', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2', '2025-06-18 06:56:30'),
('9d25ab84-3ca8-422a-ad4a-caacb1275de6', '249f6394-09d9-4756-a4aa-95549b40c00d', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', '5', '2025-06-16 06:50:12'),
('aa749e77-2fc2-4e40-8399-6d5957984778', '99b0ab2e-2c86-4b61-b1f1-964385cc5e00', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2', '2025-06-18 06:44:58'),
('ac88fd56-1c5c-4128-a5b3-e5222f0d18d8', '0db715cb-32d5-402d-bc84-fdea13cef6bf', '3', '3', '2025-01-24 09:03:35'),
('ce3b64d4-27e7-4978-8836-d8daa0330bb3', '5612a8da-3d60-47b5-baec-b492f51b0acf', '3', '5', '2025-06-16 01:07:59'),
('ce5e3607-f6e7-4f14-bdd4-bfc3aae9af26', '7d261938-0a3e-4bb1-9197-87e090e20a57', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 05:11:29'),
('d54e84d1-2827-4c85-a381-f007857cb6e6', 'ef547534-053a-47f8-b1fe-7288b9e26d33', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2', '2025-06-18 06:51:30'),
('e07b12c1-5914-479b-9b70-f69d00c410b0', '2bac2237-e8e7-4f36-92fc-159f784be5c9', '5', '5', '2025-06-16 00:40:56'),
('e910e652-e005-41ed-b32f-877ab9bc4a02', '7911293c-bfa0-40e3-9910-ec1e38a0a96d', '5b698e22-ba83-43c4-a39e-e6d68f98791f', '5', '2025-06-18 02:34:31'),
('f12deb64-3957-4a67-859a-2322711c7b3d', 'a03550ba-9be0-4afd-b133-011dda745576', 'ff2acbbb-4ec0-4214-8a30-eb1fc6e02700', '2', '2025-06-18 06:52:48'),
('f375fa49-c01f-4d3c-b951-c699b1ad694d', '3b1e5127-69e4-466c-ba57-15431a3293f4', '3efcb87b-ce45-4a66-9d73-91259caba1d0', '5', '2025-06-18 02:36:27'),
('f78cc41d-da7e-4472-a627-87ed29a8982f', '7cc60017-fb38-4d5c-bfe6-3b5b9d4c8e18', '8c782887-8fd3-4f99-ac27-63054a8a1942', '2', '2025-06-18 06:50:06');

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
-- Dumping data for table `service_tickets`
--

INSERT INTO `service_tickets` (`ticket_id`, `ticket_no`, `project_id`, `ticket_type`, `subject`, `description`, `status`, `priority`, `urgency`, `impact`, `service_category`, `category`, `sub_category`, `job_owner`, `reporter`, `source`, `sla_target`, `sla_deadline`, `sla_status`, `start_at`, `due_at`, `resolved_at`, `closed_at`, `channel`, `deleted_at`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
('0475cf8c871b6d870a03ca8beeba95e4', 'TCK-202510-0023', 'ec3b8fe6-72ec-44d3-92b3-40f8ee0bee87', 'Service', 'Set Up ระบบแพลตฟอร์มกิน-อยู่-ดี และผูกอุปกรณ์เข้ากับ Domain ด่านสำโรง เพื่อใช้งานการ Demo ระบบให้กับทางลูกค้า', 'Set Up ระบบแพลตฟอร์มกิน-อยู่-ดี และผูกอุปกรณ์เข้ากับ Domain ด่านสำโรง เพื่อใช้งานการ Demo ระบบให้กับทางลูกค้า \r\nวันที่ 21/10/2025 เวลา : 10.00 น.\r\nสถานที่ : เทศบาลตำบลด่านสำโรง\r\nผู้เข้าร่วม \r\nคณะเทศบาลตำบลด่านสำโรง \r\nบริษัทบลูโซลูชั่น\r\nพี่ตั้ม, กวาง\r\nพี่ซีน, แอมป์', 'Resolved', 'Low', 'Low', 'Department', 'Installation', 'Demo', 'Setup', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-24 08:14:22', 'Within SLA', '2025-10-21 08:10:00', '2025-10-24 08:10:00', '2025-10-21 15:51:33', NULL, 'Office', NULL, '2025-10-21 01:14:22', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 08:51:33', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('097c3183d66a2f98b7f0927512b0895c', 'TCK-202511-0017', '36f9bfe8-bf92-42cb-82d0-d3080529cc6a', 'Service', 'WFH ขอทำงานที่บ้าน', 'WFH ขอทำงานที่บ้าน \r\nเนื่องจากมีอาการปวดหัว ปวดตัว กินยา ทำให้ร่างกายไม่ค่อยแข็งแรงจึง แจ้งทีมและกลุ่มสำหรับ WFH ครับ', 'Resolved', 'Low', 'Low', 'Application', 'Others', 'Survey', 'CSAT/Feedback', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-17 10:53:57', 'Within SLA', '2025-11-08 08:00:00', '2025-11-15 08:00:00', '2025-11-10 10:54:25', NULL, 'Onsite', NULL, '2025-11-10 03:53:57', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 03:54:25', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('112f5fda773c2b665958457654cba090', 'TCK-202510-0004', 'b70608c1-6f57-4abd-bce0-9260962b0bb9', 'Service', 'ขอบริการแจ้งให้จัดทำทำคู่มือการเฝ้าระวังเหตุการณ์ฉุกเฉิน Emergency ของเทศบาลตำบลทับมา', 'ขอบริการแจ้งให้จัดทำทำคู่มือการเฝ้าระวังเหตุการณ์ฉุกเฉิน Emergency ของเทศบาลตำบลทับมา ประกอบไปด้วย \r\n1. คู่มือการใช้งานแพลตฟอร์ม กิน-อยู่-ดี เจ้าหน้าที่คัดกรองเหตุการณ์หน่วยงานกลาง\r\n2. คู่มือการใช้งานแพลตฟอร์ม กิน-อยู่-ดี เจ้าหน้าที่รับแจ้งเหตุฉุกเฉิน EMS', 'Resolved', 'Low', 'Low', 'Department', 'Support', 'Documentation', 'User manual / SOP', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 72, '2025-10-17 19:46:03', 'Within SLA', '2025-10-14 14:42:00', '2025-10-17 14:42:00', '2025-10-14 19:47:20', NULL, 'Office', NULL, '2025-10-14 12:46:03', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 12:47:20', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('13bfaa55fc6a83ce652f638acc7259b7', 'TCK-202511-0009', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Incident', 'รายการจองยานพาหนะไม่แสดงหน้าปฏิทิน หน่วยงาน โรงพยาบาลส่งเสริมสุขภาพตำบลห้วยใหญ่', 'รายการจองยานพาหนะไม่แสดงหน้าปฏิทิน หน่วยงาน โรงพยาบาลส่งเสริมสุขภาพตำบลห้วยใหญ่\r\nวันที่จอง 31/10/2025 \r\nเวลา : 15:00 - 16:00 น. \r\nผู้จอง : นาย  ชัยธวัช นิลวดี\r\nหน่วยงาน : โรงพยาบาลส่งเสริมสุขภาพตำบลห้วยใหญ่\r\nตามไฟล์แนบ', 'Resolved', 'Low', 'Low', 'Application', 'Database', 'SQL Server', 'Agent job failed', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-11 14:21:11', 'Within SLA', '2025-11-04 14:15:00', '2025-11-11 14:15:00', '2025-11-04 15:12:49', NULL, 'Office', NULL, '2025-11-04 07:21:11', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 08:12:49', 'f384c704-5291-4413-8f52-dc25e10b5d4f'),
('1646d2451f8f11f24cd214759fd6535b', 'TCK-202510-0007', '009b7557-c96b-4f2c-aeba-3649b4278cb2', 'Service', 'ขอบริการเข้าหน้างานเก็บ Requirement ลูกค้าระบบเฝ้าระวังเหตุฉุกเฉินในผู้สูงอายุ เทศบาลตำบลด่านสำโรง', 'ขอบริการเข้าหน้างานเก็บ Requirement ลูกค้าระบบเฝ้าระวังเหตุฉุกเฉินในผู้สูงอายุ เทศบาลตำบลด่านสำโรง\r\nเนื่องด้วยเทศบาลตำบลด่านสำโรง มีการเข้าไปดูงานที่เทศบาลตำบลทับมาเมื่อ สัปดาห์ก่อน เรื่องการนำระบบเฝ้าระวังเหตุฉุกเฉินในผู้สูงอายุ รายการคัดกรองค่าสุขภาพเบื้องต้น\r\n\r\nลูกค้าเทศบาลด่านสำโรงขอเลื่อนการเข้าตอบคำถามเกี่ยวกับระบบ smart living และ อุปกรณ์ เป็นพรุ่งนี้เช้า เวลา 9:00 น. คะ พี่รบกวนแอมป์ไปแทนพี่นะ พี่โอ๋จะเข้าไปด้วยคะ ลูกค้าจะสรุปรายการอุปกรณ์โครงการทั้งหมดในวันพรุ่งนี้คะ \r\nNote: ถ้ามีประเด็นเรื่องราคาให้โทรหาพี่ก่อนนะ\r\nขอบคุณคะ', 'Resolved', 'Low', 'Low', 'Department', 'Others', 'Survey', 'CSAT/Feedback', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 72, '2025-10-18 08:39:41', 'Within SLA', '2025-10-15 03:31:00', '2025-10-18 03:31:00', '2025-10-16 14:50:19', NULL, 'Onsite', NULL, '2025-10-15 01:39:41', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 07:50:19', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('19d9016c34677e6c668edde9932256a1', 'TCK-202510-0038', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Service', 'ดึงข้อมูลรายการจองยานพาหนะจากระบบเก่าเพื่อนำเข้าระบบใหม่', 'ดึงข้อมูลรายการจองยานพาหนะจากระบบเก่าเพื่อนำเข้าระบบใหม่ \r\nเนื่องจากมีการเปลี่ยนระบบจองยานพาหนะใหม่ มีบางรายการที่ยังอยู่ที่ระบบเก่าจึงขอย้ายข้อมูลที่ยังไม่มีในระบบใหม่ \r\nข้อมูลที่หายไปคือวันที่ 14 - 21 ตุลาคม 2568', 'Resolved', 'Low', 'Medium', 'Application', 'Database', 'SQL Server', 'Agent job failed', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'Portal', 72, '2025-10-27 10:39:35', 'Overdue', '2025-10-24 10:21:00', '2025-10-27 10:21:00', '2025-10-28 14:45:16', NULL, 'Office', NULL, '2025-10-24 03:39:35', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-28 07:45:16', 'f384c704-5291-4413-8f52-dc25e10b5d4f'),
('1fc0b09aaf3f079bb9e4ca9bafd97bde', 'TCK-202510-0039', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', 'Incident', 'ประชุมสรุปโครงสร้างราคา + และสาธิตการใช้งานระบบไฟไหม้ (Fire Alarm)', 'ประชุมสรุปโครงสร้างราคา + และสาธิตการใช้งานระบบไฟไหม้ (Fire Alarm)\r\n1. โครงสร้างราคาเมื่อเทียบ IBOC ราคามากกว่า ซึ่ง IBOC พร้อมใช้งานแล้ว และรองรับอุปกรณ์ จำนวน 1000 ตัว \r\n2. ระบบ\r\n     2.1.  ต้องการให้เปลี่ยนหมุดรูปอุปกรณ์ใหม่ เป็นการปักหมุดปกติ สีแดง/สีเทา\r\n     2.2.  หน้าระบบควบคุม >> แผนที่ระบบควบคุม >> เมื่อคลิก Icon ระบบแสดงแจ้งเตือนให้กด ✓ Acknowledge >> เมื่อกดแล้ว คลิกที่ Icon อุปกรณ์ ระบบยังแสดงปุ่มให้กด ✓ Acknowledge เสมอ \r\n     2.3.  เมื่อส่งสัญญาณแจ้งเตือนไฟใหม้ หรือปิดอุปกรณ์  หน้าระบบ Icon ไม่แสดงรูปสัญลักษณ์ไฟไหม้ หรือออฟไลน์ ทันที ต้องกด Refresh Brower ทุกครั้ง รวมถึงต่ออุปกรณ์กลับคืนให้อุปกรณ์เชื่อมต่อปกติ สถานะออนไลน์ไม่แสดงทันที  ควรจะต้องRefresh Brower ทุก 3 - 5 วิ ตลอดเวลา \r\n     2.4. หน้าระบบควบคุม >> แผนที่ระบบควบคุม >> เมื่อมีการแจ้งเตือนไฟไหม้ต้องแสดงเสียงแจ้งเตือน \r\n     2.5. หน้าอุปกรณ์ >> แก้ไขข้อมูลอุปกรณ์ >> กดบันทึก ระบบแจ้งบันทึกสำเร็จ >> เมื่อกดแก้ไข หรือดูข้อมูลระบบไม่ดึงข้อมูลที่เคยกรอกและบันทึกมาแสดง\r\n     2.6. หน้า Map >> แผนที่เป็นคนละตัวกับหน้า หน้าระบบควบคุม >> แผนที่ระบบควบคุม\r\n     2.7. เปลี่ยนทีมโทนสีดำแล้วระบบยังเด้งกลับไปเป็นสีขาว\r\n\r\nตามภาพแนบไฟล์ \r\n\r\nอ้างอิงงานเดิมที่ยังไม่เสร็จ : TCK-202510-0025', 'On Process', 'Low', 'Medium', 'Application', 'Development', 'Frontend', 'UI bug / layout', '3768e84a-18c0-49d9-94dd-09b44c7c9a7d', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'Portal', 72, '2025-10-27 14:00:22', 'Within SLA', '2025-10-24 13:24:00', '2025-10-27 13:24:00', NULL, NULL, 'Office', NULL, '2025-10-24 07:00:22', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 07:03:07', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('21e5cc0d2aae7047c2e2bbe663b811e6', 'TCK-202510-0009', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Incident', 'แจ้งปัญหาระบบขอใช้ยานพาหนะ รพ.สต.เหมือง รายการจองยานพาหนะชื่อผู้จองผิด', 'User รพ.สต.เหมือง แจ้งมาให้เปลี่ยนชื่อผู้จอง จาก นางทิพสุคนธ์ กิ่งมณี เป็น นายไชยรัตน์ กิ่งมณี เพราะมีบางรายการชื่อการจองเป็น ชื่อ นางทิพสุคนธ์ กิ่งมณี ครับ', 'Resolved', 'Low', 'Low', 'Department', 'Database', 'SQL Server', 'Agent job failed', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-18 12:32:19', 'Within SLA', '2025-10-15 07:23:00', '2025-10-18 07:23:00', '2025-10-15 13:53:56', NULL, 'Office', NULL, '2025-10-15 05:32:19', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 06:53:56', 'f384c704-5291-4413-8f52-dc25e10b5d4f'),
('22d7359539ef8e483ba62547e2e5136e', 'TCK-202510-0008', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Change', 'สรุป Change Requirement เพิ่มเติม โครงการขอใช้ยานพาหนะ อบจ.ชลบุรี', 'สรุป Change Requirement เพิ่มเติม โครงการขอใช้ยานพาหนะ อบจ.ชลบุรี\r\nเนื่องจากการประชุมวันที่ 10/10/2025 ที่ผ่านมาลูกค้าขอสรุป Flow  การทำงานของระบบ และการปรับหน้าจอการแสดงผลเพื่อให้ตอบโจทย์การทำงานของเจ้าหน้าที่ และถูกต้องตามความต้องการของหน่วยงาน', 'Resolved', 'Low', 'Low', 'Department', 'Support', 'Documentation', 'User manual / SOP', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-18 12:18:55', 'Within SLA', '2025-10-15 07:11:00', '2025-10-18 07:11:00', '2025-10-15 12:22:11', NULL, 'Office', NULL, '2025-10-15 05:18:55', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 05:22:11', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('2bd0058981314c1250b10a592f9af020', 'TCK-202510-0005', '0781e56d-1e40-4dec-8b65-0bd316277935', 'Incident', 'ขอบริการประสานติดตามการติดตั้ง License O365 โครงการ ISO Document กับทีม Enterprise', 'ขอบริการประสานติดตามการติดตั้ง License O365 โครงการ ISO Document กับทีม Enterprise เนื่องจากทางบัญชีทำเรื่องการสั่งซื้อและลงทะเบียนเข้า Domain Point IT เรียบร้อยแล้ว ผ่านช่องทาง Mail', 'Resolved', 'Low', 'Low', 'Department', 'Installation', 'Software', 'Client application rollout', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 72, '2025-10-17 19:54:13', 'Within SLA', '2025-10-14 14:50:00', '2025-10-17 14:50:00', '2025-10-14 19:57:50', NULL, 'Office', NULL, '2025-10-14 12:54:13', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 12:57:50', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('2d72ce32ffe570003a84eea4bbc5153e', 'TCK-202510-0047', 'b70608c1-6f57-4abd-bce0-9260962b0bb9', 'Service', 'จัดทำรายงานประจำเดือนสิงหาคม 2025 (Monthly Report) เทศบาลตำบลทับมา', 'จัดทำรายงานประจำเดือนสิงหาคม 2025 (Monthly Report)  เทศบาลตำบลทับมา \r\nดึงข้อมูลเดือนสิงหาคม 2025 \r\nตั้งแต่วันที่ 01/10/2025 - 31/10/2025 \r\n\r\n\r\n1. ดึงข้อมูลจากระบบ KYD\r\n2. Convert ข้อมูลให้อยู่ในรูปแบบ Data (ข้อมูลที่สามารถนำไปทำกราฟได้)\r\n3. Copy เอกสารต้นแบบของเดือนเก่ามาดำเนินการต่อ \r\n4.  นำข้อมูลจากข้อ 2 มาดำเนินการทำกราฟรูปแบบต่างๆ ตามหัวข้อเก่า และเปลี่ยนแปลงข้อมูลรายงานให้เป็นข้อมูลปัจจุบันและเดือนปัจจุบัน \r\n\r\n**ดำเนินการเสร็จแนบไฟล์ หรือ Link ออนไลน์ ในระบบนี้ได้เลยครับ พร้อมทั้งไฟล์ที่ดึงมาทุกไฟล์ทุกข้อมูลที่ดำเนินการ**', 'Resolved', 'Low', 'Low', 'Site', 'Project Management (การบริหารโครงการ)', 'Document Management', 'Monthly Report', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-03 18:02:18', 'Within SLA', '2025-10-27 17:53:00', '2025-11-03 17:53:00', '2025-10-31 09:19:49', NULL, 'Office', NULL, '2025-10-27 11:02:18', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-31 02:19:49', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6'),
('2d82b2958ff60d48fee678b5e5cdadbb', 'TCK-202510-0058', 'ec3b8fe6-72ec-44d3-92b3-40f8ee0bee87', 'Incident', 'ออกแบบ Logo ให้กับ Product ที่จะนำไปเสนอขายให้กับลูกค้า AI Smart Watch และทำโบวร์ชั่วร์สำหรับเสนองานขาย', 'ออกแบบ Logo ให้กับ Product ที่จะนำไปเสนอขายให้กับลูกค้า AI Smart Watch และทำโบวร์ชั่วร์สำหรับเสนองานขาย\r\nLink : https://www.canva.com/design/DAG3K23_GyM/tF4JMq-Ho-DThy8hnFRiKg/edit \r\nและโบวชัวร์ตามเอกสารแนบ', 'Resolved', 'Low', 'Low', 'Application', 'Marketing Services (บริการด้านการตลาด)', 'Branding & Identity (การสร้างแบรนด์และอัตลักษณ์)', 'Logo Design (การออกแบบโลโก้)', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 168, '2025-11-05 19:20:56', 'Within SLA', '2025-10-29 19:18:00', '2025-11-05 19:18:00', '2025-10-29 19:21:07', NULL, 'Office', NULL, '2025-10-29 12:20:56', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 12:21:07', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('2f5a45eb2c3897400895d1f8a9f6b5c2', 'TCK-202510-0034', 'bcacb043-c719-47b0-8033-4bd80cabcff6', 'Service', 'สรุปโครงการทั้งหมดของทีม Innovation ที่ On Hand อยู่ ณ ปัจจุบันและสรุปสถานะการดำเนินงานโครงการต่างๆ', 'สรุปโครงการทั้งหมดของทีม Innovation ที่ On Hand อยู่ ณ ปัจจุบันและสรุปสถานะการดำเนินงานโครงการต่างๆ \r\nสรุปขอประชุมภายในวันจันทร์ ที่ 27 ตุลาคม 2025 เวลา 14.00 น. \r\nกับทางพี่พิศาล, ตุ๋น', 'On Process', 'Low', 'Low', 'Application', 'Meeting', 'Internal', 'Sprint planning / retrospective', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-25 22:03:42', 'Within SLA', '2025-10-22 22:00:00', '2025-10-25 22:00:00', NULL, NULL, 'Office', NULL, '2025-10-22 15:03:42', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', NULL, NULL),
('30092d60a07c771aba052925e846639e', 'TCK-202511-0004', '36f9bfe8-bf92-42cb-82d0-d3080529cc6a', 'Incident', 'ประชุมสรุปการดำเนินการของ Front end สำหรับเตรียมตัวประเมินการทำงานในรอบ 3 เดือน', 'ประชุมสรุปการดำเนินการของ Front end สำหรับเตรียมตัวประเมินการทำงานในรอบ 3 เดือน', 'Resolved', 'Low', 'Low', 'Application', 'Meeting', 'Internal', 'Sprint planning / retrospective', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-10 19:08:10', 'Within SLA', '2025-11-03 10:10:00', '2025-11-10 10:10:00', '2025-11-03 19:08:29', NULL, 'Office', NULL, '2025-11-03 12:08:10', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:08:29', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('304700a01748aa660aa5afd0930f1a0e', 'TCK-202510-0044', '38fae358-df4d-41f2-8970-cb2937222dd5', 'Service', 'การตั้งค่า Cloudflare Tunnels สถาปัตยกรรมแบบ Zero Trust สำหรับเชื่อมต่อกล้อง CCTV', 'การตั้งค่า Cloudflare Tunnels สถาปัตยกรรมแบบ Zero Trust หน้าที่หลักของมันคือการเผยแพร่แอปพลิเคชันหรือบริการ (เช่น เว็บเซิร์ฟเวอร์ที่อยู่บน 192.168.1.11) ออกสู่อินเทอร์เน็ตผ่านโดเมน (เช่น thonburipark1.pointit.co.th) โดยที่คุณไม่จำเป็นต้องเปิด Firewall Port ที่เซิร์ฟเวอร์ของคุณเลย นี่เป็นการเชื่อมต่อแบบ outbound-only ที่ปลอดภัยมาก ดังภาพ', 'Resolved', 'Low', 'Low', 'Application', 'Security (ความปลอดภัย)', 'Access Control (การควบคุมการเข้าถึง)', 'Zero Trust Network Access (ZTNA) (การเข้าถึงเครือข่ายแบบ Zero Trust)', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 168, '2025-11-03 09:26:16', 'Within SLA', '2025-10-25 09:19:00', '2025-10-24 17:19:00', '2025-10-27 09:26:42', NULL, 'Office', NULL, '2025-10-27 02:26:16', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:26:42', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('350a46a5acbd6776dc3b040297ef322d', 'TCK-202510-0026', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Incident', 'แจ้งปัญหาระบบขอใช้ยานพาหนะ รพ.สต.บ้านไร่เสธ์  อ.พนัสนิคม รายการจองยานพาหนะชื่อผู้จองผิด', 'รพ.สต.บ้านไร่เสธ์ ชื่อผอ. เปลี่ยนเป็นผอ.ที่อื่นหลายรายการครับ ชื่อที่ถุกต้องต้องเป็น นาง กาญจนา อาจศึก ครับ 1ต.ค.68 - 14ต.ค.68 ครับ', 'Resolved', 'Low', 'Low', 'Application', 'Database', 'SQL Server', 'Agent job failed', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-24 16:46:07', 'Within SLA', '2025-10-21 16:43:00', '2025-10-24 16:43:00', '2025-10-21 16:47:47', NULL, 'Office', NULL, '2025-10-21 09:46:07', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:47:47', 'f384c704-5291-4413-8f52-dc25e10b5d4f'),
('3641ec530675c2226fc1420f5d02d71e', 'TCK-202510-0010', '74c45e80-c867-46cc-919b-c6e4c7d0c076', 'Service', 'บริการตรวจสอบและจัดเรียงข้อมูล (Data Cleansing)', 'บริการตรวจสอบและจัดเรียงข้อมูล (Data Cleansing) เนื่องจากระบบ AI มีการดึงภาพจากกล้อง CCTV จากงานบูธธนบุรีพานิณช เข้าระบบทำการวิเคราะห์ด้วย AI Leaning คัดแยกภาพออกมาตามฟังก์ชันต่างๆของระบบ เพื่อส่งออก Output แสดงออกมาในรูปแบบ Dashbaord ให้เข้าใจง่าย ทั้งกราฟและข้อมูล ซึ่งส่วนนี้ ผมได้ดำเนินการตรวจสอบข้อมูลที่ได้จาก AI เพื่อตรวจสอบและปรับเปลี่ยนข้อมูลที่ได้จาก AI ให้มีความถูกต้องมากที่สุด ในการแสดงผลให้ลูกค้าเป็นต้น\r\nกระบวนการ\r\n1. เข้า Link : https://gui.pointit.co.th/\r\n2. กรอกข้อมูลตามวันที่ : 14/10/2025\r\n3. คัดกรอกตรวจสอบรูปแยกพนักงานกับลูกค้า โดยยึดจากภาพรวม', 'Resolved', 'Low', 'Low', 'Department', 'Data/Analytics', 'BI/Dashboard', 'Report incorrect', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-18 13:26:52', 'Within SLA', '2025-10-15 08:18:00', '2025-10-18 08:18:00', '2025-10-15 13:27:43', NULL, 'Office', NULL, '2025-10-15 06:26:52', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 15:23:55', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('375fbb2a1a2a398c6ee3fcbba58316cb', 'TCK-202510-0002', '74c45e80-c867-46cc-919b-c6e4c7d0c076', 'Service', 'บริการตรวจสอบและจัดเรียงข้อมูล (Data Cleansing)', 'บริการตรวจสอบและจัดเรียงข้อมูล (Data Cleansing) เนื่องจากระบบ AI มีการดึงภาพจากกล้อง CCTV จากงานบูธธนบุรีพานิณช เข้าระบบทำการวิเคราะห์ด้วย AI Leaning คัดแยกภาพออกมาตามฟังก์ชันต่างๆของระบบ เพื่อส่งออก Output แสดงออกมาในรูปแบบ Dashbaord ให้เข้าใจง่าย ทั้งกราฟและข้อมูล ซึ่งส่วนนี้ ผมได้ดำเนินการตรวจสอบข้อมูลที่ได้จาก AI เพื่อตรวจสอบและปรับเปลี่ยนข้อมูลที่ได้จาก AI ให้มีความถูกต้องมากที่สุด ในการแสดงผลให้ลูกค้าเป็นต้น \r\nกระบวนการ\r\n1. เข้า Link : https://gui.pointit.co.th/\r\n2. กรอกข้อมูลตามวันที่ : 12-13/10/2025\r\n3. คัดกรอกตรวจสอบรูปแยกพนักงานกับลูกค้า โดยยึดจากภาพรวม', 'Resolved', 'Low', 'Low', 'Application', 'Data/Analytics', 'BI/Dashboard', 'Report incorrect', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-16 18:27:49', 'Within SLA', '2025-10-13 13:21:00', '2025-10-16 13:21:00', '2025-10-13 18:30:02', NULL, 'Office', NULL, '2025-10-13 11:27:49', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 11:30:02', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('3f4fcda2cc17020648deacae219f4708', 'TCK-202510-0029', '161e830e-355e-4364-acce-405857cf30b9', 'Service', 'ช่วย Map IP Network ภายนอกสำหรัส่งข้อมูลระหว่างเครื่อง EDGE AI ฝั่งลูกค้า ส่ง API ไปยัง Server Point', 'ช่วย Map Private IP ให้ออก Network ภายนอกสำหรับส่งข้อมูลระหว่างเครื่อง EDGE AI ฝั่งลูกค้า ส่ง API ไปยัง Server Point\r\nBasic Information\r\nPublic hostname : thonburiedge.pointit.co.th\r\nPath:*\r\nService: http://172.16.2.23', 'Resolved', 'Low', 'Low', 'Application', 'Network', 'IP Management', 'DHCP/DNS/IPAM', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-25 21:34:37', 'Within SLA', '2025-10-22 08:30:00', '2025-10-25 08:30:00', '2025-10-22 21:34:53', NULL, 'Office', NULL, '2025-10-22 14:34:37', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:34:53', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('417d339d06500f15a6b7531da0c7cd28', 'TCK-202510-0020', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Incident', 'แจ้งปัญหาระบบขอใช้ยานพาหนะ รพ.สต.ท่าข้าม อ.พนัสนิคม รายการจองยานพาหนะชื่อผู้จองผิด', 'รพ.สต.ท่าข้าม อ.พนัสนิคม  เปลี่ยนรายการจองยานพาหนะทั้งหมดที่เป็นชื่อธนู เครือวรรณ เปลี่ยนเป็นเป็น นางปฐมา  รวยสำราญ ครับ', 'Resolved', 'Low', 'Low', 'Application', 'Database', 'SQL Server', 'Agent job failed', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-23 10:09:47', 'Within SLA', '2025-10-20 10:05:00', '2025-10-23 10:05:00', '2025-10-20 13:16:49', NULL, 'Office', NULL, '2025-10-20 03:09:47', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 06:16:49', 'f384c704-5291-4413-8f52-dc25e10b5d4f'),
('4526af8066b18a46eb44341c5b187ad8', 'TCK-202510-0033', '00d0728f-5754-4490-b568-55cb9f79da53', 'Incident', 'ประชุมทีม Innovation แจ้งเพื่อทราบ โอนย้ายพนักงานจากทีม SE มาทำการทดสอบระบบ Tester สำหรับทีม Innovation', 'ประชุมทีม Innovation แจ้งเพื่อทราบ โอนย้ายพนักงานจากทีม SE มาทำการทดสอบระบบ Tester สำหรับทีม Innovation \r\nประชุมทีมโดยพี่พิศาล ประกาศแจ้ง \r\n โอนย้ายพนักงานจากทีม SE มาทำการทดสอบระบบ Tester สำหรับทีม Innovation (ตุ๋น) มีผลให้เริ่มเป็นทางการวันที่ 1 เดือน พฤศจิกายน 2025 นี้ \r\nโดยให้ทางน้องตุ๋น ดำรงตำแหน่ง : Role Tester ดังนี้\r\n1. ทดสอบ Application ของทีมทั้งหมดทุกโครงการ \r\n2. ทดสอบ API และ Test Case , Test Plan, Test Report ของทีมและโครงการทั้งหมด \r\n3. ทดสอบอุปกรณ์ในทีมทุกตัว และทุกโครงการ \r\n4. สามารถนำเสนอ Demo งานโครงการต่างๆ กับ Sale ได้ \r\n\r\nเพิ่มเติม \r\n      *มีพี่ขวัญและโม เป็น Tester สำรองสำหรับโครงการทั้งหมด \r\n      *รายงานพี่พิศาลก็ละนี้ทำไม่ทันเกิดปัญหาต้องการความช่วยเหลือ', 'Resolved', 'Low', 'Low', 'Department', 'Meeting', 'Internal', 'Sprint planning / retrospective', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-25 21:59:31', 'Within SLA', '2025-10-22 14:25:00', '2025-10-25 14:25:00', '2025-10-22 21:59:54', NULL, 'Office', NULL, '2025-10-22 14:59:31', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:59:54', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('47255f8f395045b169b65b16f91ebbe9', 'TCK-202510-0014', '0781e56d-1e40-4dec-8b65-0bd316277935', 'Service', 'ขอบริการสร้าง User จำนวน 20 รายการ ให้ลูกค้าใช้ในการทดสอบระบบโครงการ ISO Document', 'ขอบริการสร้าง User จำนวน 20 รายการ ให้ลูกค้าใช้ในการทำสอบระบบโครงการ ISO Document', 'Resolved', 'Low', 'Medium', 'Application', 'Support', 'User Account', 'Create/Modify/Disable user', 'b27b56e5-6f28-4d30-8add-4bddafa38841', '3', 'Portal', 72, '2025-10-19 11:11:50', 'Overdue', NULL, NULL, '2025-10-21 16:50:55', NULL, 'Office', NULL, '2025-10-16 04:11:50', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:50:55', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('4d0f17400296e43ac2a70f6764cce016', 'TCK-202511-0011', 'ec3b8fe6-72ec-44d3-92b3-40f8ee0bee87', 'Service', 'นำ Iot Sim มาทดสอบการใช้งานกับอุปกรณ์ AI Tracker เพื่อสรุปผลว่า Sim เพิ่มเติม (2)', 'นำ Iot Sim มาทดสอบการใช้งานกับอุปกรณ์ AI Tracker เพื่อสรุปผลว่า Sim เพิ่มเติม (2)\r\nเนื่องจากทดสอบครั้งที่ 1 สรุปได้ว่าไม่สามารถโทรเข้า-และออกได้  จึงมีการประสานงานกับเจ้าหน้าที่ให้บริการ SIM ตรวจสอบซึ่งเจ้าหน้าที่แจ้งว่า ไมไ่ด้เปิด Access ให้โทรออก \r\nซึ่ง ณ ปัจจุบันเจ้าหน้าที่ทำการเปิดสัญญาณให้โทรออกแล้ว จึงดำเนินการทดสอบอีกครั้ง', 'Resolved', 'Low', 'Low', 'Application', 'Hardware', 'IoT Devices / Tracker', 'SIM Testing', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-12 13:49:44', 'Within SLA', '2025-11-05 13:44:00', '2025-11-12 13:44:00', '2025-11-05 14:00:37', NULL, 'Office', NULL, '2025-11-05 06:49:44', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-05 07:00:37', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('4dd8e701d99aa9dd90edfc7e94dae7eb', 'TCK-202510-0057', '36f9bfe8-bf92-42cb-82d0-d3080529cc6a', 'Incident', 'ประชุมสรุปงานบูธ ธนบุรีพานิช สำหรับปรับปรุงประสิทธิ์ภาพการทำงานของระบบ และอุปกรณ์', 'ประชุมสรุปงานบูธ ธนบุรีพานิช สำหรับปรับปรุงประสิทธิ์ภาพการทำงานของระบบ และอุปกรณ์\r\nปัญหา \r\n1. เข้าหน้างานบ่อย \r\n     1.1. เครื่องดับบ่อย \r\n     1.2. Internet ช้าดึงภาพ Remote ไมไ่ด้  Data หาย\r\n2. เปิดกล้องต้องเปิดกล้องทีละตัว โดยกดลิงค์ต้องใส่ Password ทุกครั้ง', 'Resolved', 'Low', 'Low', 'Application', 'Meeting', 'Internal', 'Sprint planning / retrospective', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-05 17:04:04', 'Within SLA', '2025-10-29 16:58:00', '2025-11-05 16:58:00', '2025-10-29 17:13:34', NULL, 'Office', NULL, '2025-10-29 10:04:04', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 10:13:34', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('519fee5632c4c3a2970bde1aeb2a1c75', 'TCK-202510-0054', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Service', 'แจ้งปัญหาทำรายการจองยานพาหนะ กดบันทึกระบบ แจ้ง  API Error', 'แจ้งปัญหาทำรายการจองยานพาหนะ กดบันทึกระบบ แจ้ง  API Error : 500 ดังไฟล์แนบครับ', 'Resolved', 'Medium', 'Medium', 'Application', 'Development', 'Backend', 'API timeout / 5xx', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 24, '2025-10-30 09:10:03', 'Within SLA', '2025-10-29 09:07:00', '2025-10-30 09:07:00', '2025-10-29 09:30:13', NULL, 'Office', NULL, '2025-10-29 02:10:03', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 02:30:13', 'f384c704-5291-4413-8f52-dc25e10b5d4f'),
('5332f17963259b72d24f0321737a5302', 'TCK-202511-0007', 'ec3b8fe6-72ec-44d3-92b3-40f8ee0bee87', 'Service', 'นำ Iot Sim มาทดสอบการใช้งานกับอุปกรณ์ AI Tracker เพื่อสรุปผลว่า Sim ใช้งานกับอุปกรณ์ได้หรือไม่', 'นำ Iot Sim มาทดสอบการใช้งานกับอุปกรณ์ AI Tracker เพื่อสรุปผลว่า Sim ใช้งานกับอุปกรณ์ได้หรือไม่ \r\nเนื่องจากมีการติดต่อทางผู้ให้บริการ Sim ขอ Sim สำหรับอุปกรณ์ Iot ในการส่งสัญญาณไปยังระบบเมื่อการกดขอความช่วยเหลือ หรือกรณีพลัดตกหักล้ม \r\nเพื่อเลือก Packgate ที่ตรงกับการใช้งานอุปกรณ์ ลดต้นทุนโครงการ', 'Resolved', 'Low', 'Medium', 'Department', 'Hardware', 'IoT Devices / Tracker', 'SIM Testing', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-11 09:20:45', 'Within SLA', '2025-11-03 16:19:00', '2025-11-10 16:19:00', '2025-11-04 09:24:58', NULL, 'Office', NULL, '2025-11-04 02:20:45', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 02:24:58', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('5356767837cd88ec0215243511c749ab', 'TCK-202510-0025', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', 'Incident', 'รบกวนแก้ไข UI Frontend ระบบ ระบบป้องกันอัคคีภัยเพื่อความปลอดภัยของโรงเรียนในสังกัดกรุงเทพมหานคร (58 โรงเรียน)', 'รบกวนแก้ไข UI Frontend ระบบ ระบบป้องกันอัคคีภัยเพื่อความปลอดภัยของโรงเรียนในสังกัดกรุงเทพมหานคร (58 โรงเรียน)\r\nดังนี้ \r\n1. พื้นหลังให้ Default : เป็นสีดำเสมอ >> ปัจจุบันแสดงเป็นขาวบ้างดำบาง แก้ไขให้หายขาด\r\n2. ตรวจสอบหน้า Dashboard ดังนี้ \r\n     2.1. ตรวจสอบเงื่อนไขการแสดงสถานะไฟใหม้    การตอบให้ตอบ : อาทิเช่น ได้รับ API : เส้น ...... ส่ง Parameter :  alarm : false จึงดึงสถานะมาแสดง ?  หากคำตอบว่าถูกต้องหรือไม่ ? \r\n     2.2. ตรวจสอบเงื่อนไขการแสดงสถานะออนไลน์/ออฟไลน์  เงื่อนไขการดึงมาแสดงหน้าบ้านอย่างไร ? \r\n     2.3. เมื่อกดเหตุไหม้ แสดงเป็นสัญลักษณ์ ไฟใหม้ กด Icon สัญลักษณ์ แสดงข้อมูลของอุปกรณ์ และปุ่ม ✓ Acknowledge  เมื่อกด ✓ Acknowledge ระบบจะ Call API เส้นไหน และผลลัพธ์เป็นอย่างไร สามารถกดแล้วให้เจ้าหน้าที่เขียนคอมเม้นท์ได้หรือไม่ ?\r\n     2.4. การแสดงอุปกรณ์ที่หน้า Dashboard จะต้องแสดงอุปกรณ์ที่มีการอนุมัติ จากหน้า Device แล้วเท่านั้นหรือไม่ ? \r\n     2.5. รายการอุปกรณ์ มุมด้านขวามือ แสดงรายการอุปกรณ์ทั้งหมด และสัญลักษณ์สีสถานะอุปกรณ์ ประกอบด้วย ออฟไลน์ : เทา /ออนไลน์ : เขียว /ไฟใหม้ : แดง ? \r\n     2.6. เมื่ออุปกรณ์ออฟไลน์ ที่หน้าแผนที่ สัญลักษณ์แสดงเป็นสีเทา เมื่อคลิกที่ ICON แสดงข้อมูล ควรมีปุ่มแสดง ✓ Acknowledge  เมื่อกด ✓ Acknowledge ระบบจะ Call API เส้นไหน และผลลัพธ์เป็นอย่างไร สามารถกดแล้วให้เจ้าหน้าที่เขียนคอมเม้นท์ได้หรือไม่ ?\r\n\r\nหลักการตรวจสอบ \r\n1. API \r\n     1.1. API ใช้ตัวไหน \r\n     1.2. API แต่ละเส้น ความหมายคืออะไร และมีการส่ง Parameter ในการ  POST/PUT/PASH/DEL ผลลัพธ์ที่ได้จะแสดงอะไร สิ่งที่ได้คืออะไร \r\n2. กำหนดเงื่อนไขในการแสดงผลหน้า Frontend อ้างอิงโจทย์', 'On Process', 'Low', 'Low', 'Application', 'Development', 'Frontend', 'UI bug / layout', '3768e84a-18c0-49d9-94dd-09b44c7c9a7d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-24 16:38:26', 'Within SLA', '2025-10-21 15:56:00', '2025-10-24 15:56:00', NULL, NULL, 'Office', NULL, '2025-10-21 09:38:26', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', NULL, NULL),
('54ce0ca8f7f731876d95512f0d28debd', 'TCK-202510-0001', '74c45e80-c867-46cc-919b-c6e4c7d0c076', 'Incident', 'แจ้งปัญหาไม่สามารถดูหน้ารายละเอียด ข้อมูลจาก AI Analysis ได้จาก Web ภายนอก', 'แจ้งปัญหาไม่สามารถดูหน้ารายละเอียด ข้อมูลจาก AI Analysis ได้จาก Web ภายนอก', 'Resolved', 'Medium', 'Low', 'Application', 'Support', 'Remote Support', 'Quick Assist / AnyDesk session', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'Portal', 24, '2025-10-14 13:56:52', 'Within SLA', '2025-10-13 08:52:00', '2025-10-14 22:52:00', '2025-10-13 14:18:03', NULL, 'Office', NULL, '2025-10-13 06:56:52', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 07:18:03', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0'),
('574ec888c60f34c5858cbea206e671b0', 'TCK-202510-0035', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Service', 'สรุปเอกสาร Requirment สำหรับจ้างงาน Outsource ในการปรับปรุง Frontend โครงการจองยานพาหนะ', 'สรุปเอกสาร Requirment สำหรับจ้างงาน Outsource ในการปรับปรุง Frontend โครงการจองยานพาหนะ (Change Requirment)\r\nตามเอกสารแนบ : https://docs.google.com/document/d/1oCK_Adl9kvYfsy-qXnMytvjtBbloLEj8fBTMBYlQidU/edit?tab=t.0', 'Pending', 'Low', 'Low', 'Application', 'Change', 'CAB', 'Change advisory board meeting (ประชุมพิจารณาเปลี่ยนแปลง)', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-25 22:12:35', 'Within SLA', '2025-10-22 15:08:00', '2025-10-25 15:08:00', NULL, NULL, 'Office', NULL, '2025-10-22 15:12:35', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:13:20', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('5eb8ec3a227ec1dab8b5463cb9d26f12', 'TCK-202510-0037', '161e830e-355e-4364-acce-405857cf30b9', 'Incident', 'เข้าตรวจสอบเครื่อง Server EDGE AI ค้างและดับไม่สามารถ Remote เข้าใช้งานเครื่องได้', 'ปัญหา : เข้าตรวจสอบเครื่อง Server EDGE AI ค้างและดับไม่สามารถ Remote เข้าใช้งานเครื่องได้ เนื่องจากเครื่องทำหน้าที่ประมวลผลข้อมูล AI และส่งกลับมายัง Server บริษัทส่งผลให้ข้อมูลไม่ถูกส่งมาและไม่แสดงผลที่หน้า Dashboard', 'Resolved', 'Low', 'Low', 'Application', 'Server', 'Service / API', 'Down', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 72, '2025-10-26 20:03:42', 'Within SLA', '2025-10-23 15:30:00', '2025-10-26 15:30:00', '2025-10-23 20:03:55', NULL, 'Onsite', NULL, '2025-10-23 13:03:42', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-23 13:03:55', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('611bfaa99199810cb73ce1721f6ea0a0', 'TCK-202511-0008', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Incident', 'ขอเปลี่ยนรายการจองรถ จากชื่อนายศูรกาจ ภมรนาค เป็น นางกิมบวย เพ็ชรพันธ์ เบ็งลวง ตามไฟล์แนบครับ', 'ขอเปลี่ยนรายการจองรถ จากชื่อนายศูรกาจ ภมรนาค เป็น นางกิมบวย เพ็ชรพันธ์ เบ็งลวง ตามไฟล์แนบครับ', 'Resolved', 'Low', 'Medium', 'Application', 'Database', 'SQL Server', 'Agent job failed', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-11-07 09:29:27', 'Within SLA', '2025-11-03 16:26:00', '2025-11-06 16:26:00', '2025-11-04 15:13:11', NULL, 'Office', NULL, '2025-11-04 02:29:27', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 08:13:11', 'f384c704-5291-4413-8f52-dc25e10b5d4f'),
('632456371e6b634ceb7b30c85cf89ebf', 'TCK-202510-0045', '38fae358-df4d-41f2-8970-cb2937222dd5', 'Service', 'ออกแบบ Dashboard ด้วย Google Looker Studio (หรือ Google Data Studio) สำหรับแสดงข้อมูลการเยี่ยมชมบูธ', 'ออกแบบ Dashboard ด้วย Google Looker Studio (หรือ Google Data Studio) สำหรับแสดงข้อมูลการเยี่ยมชมบูธ \r\nDashboard LInk : https://lookerstudio.google.com/s/q6vzo8DT5mo\r\nData Link : https://docs.google.com/spreadsheets/d/1rezUJcwsPkhtYWWxAfUiN2NeYAA2xEemqYkxCE2hRvA/edit?gid=0#gid=0', 'Resolved', 'Low', 'Low', 'Application', 'Business Intelligence & Analytics Services (บริการด้านการวิเคราะห์ข้อมูลทางธุรกิจ)', 'Data Visualization (การแสดงผลข้อมูล)', 'Dashboard Development (การพัฒนาแดชบอร์ด)', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 168, '2025-11-03 09:32:27', 'Within SLA', '2025-10-24 09:29:00', '2025-10-24 09:29:00', '2025-10-27 09:32:40', NULL, 'Office', NULL, '2025-10-27 02:32:27', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:32:40', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('637269d63c441e053578d2a383dded3a', 'TCK-202511-0003', '38fae358-df4d-41f2-8970-cb2937222dd5', 'Service', 'ปรับปรุงข้อมูล (Data Dashbaord) โครงการจัดตั้งบูธ ดุสิตเซ็นทรัลพาร์ค (Dusit Central Park)', 'ปรับปรุงข้อมูล (Data Dashbaord) โครงการจัดตั้งบูธ ดุสิตเซ็นทรัลพาร์ค (Dusit Central Park)\r\nเนื่องจากมีการปรับปรุงระบบทำให้ ข้อมูลจากกล้องทั้ง 4 จุดนำมาแสดงผลไม่ได้ในทันที่ ส่งผลกระทบให้กราฟที่แสดงผล แสดงจำนวนข้อมูลในปริมาณที่น้อยมาก\r\nไม่สอดคล้องกับจำนวนกล้องที่ติดตั้ง จึงมีดึงข้อมูลเพื่อปรับจำนวนตัวเลขในการแสดงผลใหม่ให้เหมาะสม', 'Resolved', 'Medium', 'Medium', 'Application', 'Data/Analytics', 'BI/Dashboard', 'Report incorrect', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 24, '2025-11-04 19:00:42', 'Within SLA', '2025-11-03 09:30:00', '2025-11-04 09:30:00', '2025-11-03 19:04:02', NULL, 'Office', NULL, '2025-11-03 12:00:42', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:04:02', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('67eab0c67c179f262013eaa95eee8d9c', 'TCK-202511-0001', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Incident', 'แจ้งปัญหาข้อมูลไม่แสดงบนระบบจองยานพาหนะระบบจองรถ อบจ.ชลบุรี (https://supercar-dev.pointit.co.th) Dev', 'แจ้งปัญหาข้อมูลไม่แสดงบนระบบจองยานพาหนะระบบจองรถ อบจ.ชลบุรี (https://supercar-dev.pointit.co.th) Dev\r\nเนื่องจากมีความต้องการที่จะเข้าไปตรวจสอบงานฝั่ง Frontend สำหรับจ้างพัฒนา UI ระบบจองยานพานหนะ อบจ.ชลบุรี (Change) สามารถเข้า URL >> Login ได้ปกติ \r\nแต่ไม่แสดงข้อมูลเพื่อให้ตรวจสอบ \r\nประสานงาน ปัน (DevOps) , พี่เบียร์ (Backend) ตรวจสอบ API ฝั่ง Backend **', 'Resolved', 'Medium', 'Low', 'Application', 'IT Service', 'Dev Environment', 'API Unavailable', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-11-06 14:52:48', 'Within SLA', '2025-11-03 14:48:00', '2025-11-06 14:48:00', '2025-11-03 14:57:56', NULL, 'Office', NULL, '2025-11-03 07:52:48', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 07:57:56', 'f384c704-5291-4413-8f52-dc25e10b5d4f'),
('682b8b2fc1c5de91674a1773db6f539d', 'TCK-202511-0019', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Change', 'ต้องการแก้ไขรายการจองย้อนหลัง เพื่อแก้ไขสถานะ และวันที่จองโดยคงรายละเอียดการจองเหมือนเดิม', 'ต้องการแก้ไขรายการจองย้อนหลัง เพื่อแก้ไขสถานะ และวันที่จองโดยคงรายละเอียดการจองเหมือนเดิม\r\nจาก 16/11 เป็น 07/11 ครับ', 'Resolved', 'Low', 'Medium', 'Application', 'Database', 'SQL Server', 'Agent job failed', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-11-13 12:07:36', 'Within SLA', '2025-11-10 12:02:00', '2025-11-13 12:02:00', '2025-11-10 17:15:52', NULL, 'Office', NULL, '2025-11-10 05:07:36', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 10:15:52', 'f384c704-5291-4413-8f52-dc25e10b5d4f'),
('6a5fda3fdaa7b4addd1f1c1f68ce7d12', 'TCK-202510-0043', '161e830e-355e-4364-acce-405857cf30b9', 'Incident', 'สร้าง Credentials ติดต่อกับ Google Sheets สำหรับเพิ่ม ลบ แก้ไข ข้อมูลในรูปแบบ JSON', 'สร้าง Credentials ติดต่อกับ Google Sheets สำหรับเพิ่ม ลบ แก้ไข ข้อมูลในรูปแบบ JSON ตามไฟล์แนบ', 'Resolved', 'Low', 'Low', 'Application', 'Identity', 'Privileged Access', 'PAM account rotation', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-01 23:08:08', 'Overdue', '2025-10-25 23:03:00', '2025-11-01 23:03:00', '2025-11-06 13:48:30', NULL, 'Office', NULL, '2025-10-25 16:08:08', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-06 06:48:30', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0'),
('6becc7cc78b8b6b8e8066e923c21d181', 'TCK-202510-0059', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', 'Incident', 'แจ้งปัญหาโครงการ Fire Alarm หน้าแสดงสถานะอุปกรณ์แผนที่และหน้าอื่นๆ แสดงอุปกรณ์สลับออฟไลน์และออนไลน์ ทุกๆนาที', '1. เนื่องจากทีมขายมีการ Complain ระบบ K-Lynx ในส่วนของ ระบบ Fire Alarm หน้าแสดงสถานะอุปกรณ์แผนที่และหน้าอื่นๆ แสดงอุปกรณ์สลับออฟไลน์และออนไลน์ ทุกๆนาที\r\nประกอบด้วย\r\n-  แผนที่\r\n- อุปกรณ์ \r\n- Map \r\n2. หน้ากิจกรรม ทำไมแสดงข้อมูล Log ของ D4:D4:DA:12:50:03 อย่างเดียวในเมื่ออุปกรณ์มีการ ออนไลน์ทั้งหมด 3 ตัว ทำไมไม่แสดงทั้ง 3 ตัว \r\n3. ข้อมูลอุปกรณ์แจ้งหรือแสดงสถานะ 3 ตัว ทั้งที่มีการเชื่อมต่อไว้ 2 ตัว ตัวที่ 3 มาจากไหน ?', 'On Process', 'Low', 'Medium', 'Application', 'Development', 'Frontend', 'UI bug / layout', '3768e84a-18c0-49d9-94dd-09b44c7c9a7d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-11-02 22:00:56', 'Within SLA', '2025-10-30 09:30:00', '2025-11-02 09:30:00', NULL, NULL, 'Office', NULL, '2025-10-30 15:00:56', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', NULL, NULL),
('6c6595bb55eb3374513891cfce0bec7e', 'TCK-202510-0016', '74c45e80-c867-46cc-919b-c6e4c7d0c076', 'Service', 'ประชุมโครงการงานบูธ ธนบุรีพานิช สรุปแผนกการ Set กล้องเพิ่มเพื่อ Support งานบูธที่จะเกิดขี้นใหม่ ในวันที่ 21/10/2025', 'ประชุมโครงการงานบูธ ธนบุรีพานิช สรุปแผนกการ Set กล้องเพิ่มเพื่อ Support งานบูธที่จะเกิดขี้นใหม่ ในวันที่ 21/10/2025  นี้\r\nเนื่องด้วยทางพี่ซีน (Sales) รับ Requirment จากลูกค้ามาใหม่ แจ้งว่าจะมีงานบูธ ในวันที่ 21/10/2025  ที่ Central Park  ซึ่งเป็นงานซ้อน กับงานตั้งบูธ Central ladprao\r\nทำให้กล้องไม่เพียงพอในการนำไปใช้ในการติดตั้ง จึงขอนัดประชุม เพื่อสรุปแนวทาง ประกอบด้วย \r\n1. คุณซีน\r\n2. น้องซีนน้อย \r\n3. พี่แจ็ค\r\nเพื่อหาแนวทางแก้ไขและดำเนินการต่อ', 'Resolved', 'Low', 'Low', 'Department', 'Meeting', 'Customer', 'Requirement / status update', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-19 17:34:27', 'Overdue', '2025-10-16 17:27:00', '2025-10-19 17:27:00', '2025-11-06 13:46:47', NULL, 'Office', NULL, '2025-10-16 10:34:27', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-06 06:46:47', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0'),
('713616155e9f40ce7a0bf5f1bbd0ca5f', 'TCK-202510-0050', '36f9bfe8-bf92-42cb-82d0-d3080529cc6a', 'Service', 'ประชุมโครงการ Innovation สำหรับ Update Task และ Prograss ทีม', 'ประชุมโครงการ Innovation สำหรับ Update Task และ Prograss ทีม\r\nโครงการทั้มที่ถืออยู่ปัจจุบัน จำนวน 6 รายการ \r\nประกอบด้วย \r\n1. Carbooking  (ระยะเวลาดำเนินการ 6 เดือน + ระยะเวลา Change 2 วัน) สิ้นสุด 12/11/2025\r\n    - แจ้งเรื่องการปรับโครงสร้างราคา รวม  450,000 บาท\r\n2. Fire Alarm (ระยะเวลา 200 วัน)  On Process\r\n    - ตอบรับ : ปรับอุปกรณ์ให้รองรับ 1000 รายการ โดยใช้งบประมาณเดิม \r\n    - ตอบรับ : ดำเนินการโครงการต่อเนื่อง \r\n    - นัดประชุม พี่แจ็ค พี่โอ๋ ในเรื่องของการบริหารจัดการโครงการ การทำ Plan ต่างๆ \r\n3. ISO Document (เดือน 6) สิ้นสุด 30/11/2025\r\n    - ปรับ Jira ให้ ขยับวันเวลา Task ที่มีปัญหาเลื่อนไปส่งงานปลายเดือน พร้อมกับ Module หรือรอบส่งงวดงานที่ 4\r\n4. LDAP Auth (ระยะเวลา 3 สัปดาห์) สิ้นสุด 14/11/2025\r\n    -  อัพเดท Task เนื่องจากมี Task ล่วงเลยกำหนดตาม Timeline\r\n5. Live Stream Pattaya (ระยะเวลา 200 วัน)\r\n    - รอประกาศผล\r\n6. New Watchman (ภาค 6)   (ระยะเวลาดำเนินการ 6 เดือน ) สิ้นสุด 15/03/2026\r\n    - ทางพี่เบียร์  Review และอัพเดทวันที่ของ Task ต่างๆ และจัดเรียงลำดับความสำคัญการทำก่อนหลัง\r\n7. อื่นๆ \r\n     7.1. บูธลาดพร้าว\r\n     7.2. บูธเซนทลัน Pack\r\n\r\nเพิ่มเติม \r\n  - ให้ทางทีมเข้ามามีบทบาทในการเพิ่ม ลบ แก้ไข  Task ของตัวเอง เมื่อมีโครงการขึ้นใหม่ให้เข้ามาเพิ่มส่วนที่ตัวเองต้องทำให้ Task และกำหนดระยะเวลา และแนบเอกสาร Link ไฟล์ตต่างๆมา Task ในการปิด Task แต่ละครั้ง \r\n  - ให้ตุล เริ่มทำ Test Case/ Test Plan ทุกโครงการทั้งหมดที่ขึ้นและ Review ระบบใหม่ทั้งหมด พร้อมทำใส่ใน Jira Software ทันที ในส่วนของ Tester', 'Resolved', 'Low', 'Low', 'Department', 'Meeting', 'Internal', 'Sprint planning / retrospective', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'Portal', 168, '2025-11-03 19:06:29', 'Within SLA', '2025-10-27 14:30:00', '2025-11-03 14:30:00', '2025-10-27 19:06:49', NULL, 'Office', NULL, '2025-10-27 12:06:29', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 12:06:49', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('7671fe71ebe2a276cf49f946914b5082', 'TCK-202510-0060', 'ec3b8fe6-72ec-44d3-92b3-40f8ee0bee87', 'Incident', 'ออกนอกพื้นที่ประชุมสรุปราคาและคุณสมบัติของอุปกรณ์ โครงการเช่าใช้บริการระบบบริการสุขภาพอัจริยะ อบต.ด่านสำโรง', 'ออกนอกพื้นที่ประชุมสรุปราคาและคุณสมบัติของอุปกรณ์ โครงการเช่าใช้บริการระบบบริการสุขภาพอัจริยะ อบต.ด่านสำโรง\r\nผู้เข้าร่วม\r\n1. บริษัทบลูโซลูชั่น  คุณตี้ , อาจารย์ ม.เกษตร, คุณเชอรี่\r\n2. พี่ตั๊ม , ไอซ์, กวาง\r\n3. พี่โอ๋ , พี่ซีน , พี่แอมป์\r\n\r\nหัวข้อการประชุม \r\n1. สรุปราคาและจำนวนอุปกรณ์', 'Resolved', 'Low', 'Medium', 'Application', 'Support', 'On-site Support', 'Field visit (ออกปฏิบัติงานนอกพื้นที่)', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-11-02 22:08:34', 'Within SLA', '2025-10-30 11:01:00', '2025-11-02 11:01:00', '2025-10-30 22:12:05', NULL, 'Onsite', NULL, '2025-10-30 15:08:34', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-30 15:12:05', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('7718ca773579e9622a2e80901a7484b1', 'TCK-202511-0014', '74c45e80-c867-46cc-919b-c6e4c7d0c076', 'Incident', 'demo ai test showroom', 'เตรียมหลังบ้าน ในการทำ demo ai test showroom', 'New', 'Low', 'Low', 'Department', 'Development', 'Backend', 'API timeout / 5xx', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'Portal', 168, '2025-11-13 13:53:58', 'Within SLA', '2025-11-06 13:50:00', '2025-11-16 13:50:00', NULL, NULL, 'Office', NULL, '2025-11-06 06:53:58', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', '2025-11-06 06:56:48', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0'),
('7940e651309a1a31097e7734e2b4f960', 'TCK-202510-0040', '74c45e80-c867-46cc-919b-c6e4c7d0c076', 'Service', 'ประชุมสรุปการ Demo โครงงการศูนย์ Benz ธนบุรีพานิช', '**สรุปวางแผนการ Demo ระบบศูนย์ Benz  ธนบุรีพานิช \r\n1. AI ตรวจสอบวัตถุบนเคาน์เตอร์และโต๊ะ   >> ใช้กล้อง 1 ตัว \r\n2. AI ตรวจสอบเวลาที่ลูกค้าอยู่ในพื้นที่  >> ใช้กล้อง 2 ตัว \r\n3. AI นับจำนวนลูกค้าที่เข้าและออกในโชว์รูม  >> ใช้กล้อง 1 ตัว\r\n4. AI แยกลูกค้าและพนักงาน >> ใช้กล้องข้อที่ 2. \r\n5. AI อ่านป้ายทะเบียนรถ >> ใช้กล้อง 1 ตัว\r\n6. AI ตรวจจับยานพาหนะที่เข้ามาและออกไปจากบริเวณศูนย์บริการ >> ใช้กล้องเดียวกับข้อ 5. \r\n7. AI นับจำนวนรถที่อยู่ในพื้นที่ >> ใช้กล้อง 1 ตัว ไม่มีอะไรบดบังพื้นที่  >> ใช้กล้อง 2 ตัว \r\n8. AI ค้นหาดูด้วยภาษาพูด (อาทิ รถสีแดงป้ายทะเบียน 5กก 5921) >> ดึง Ifream จาก PointIT ไปใช้ในการค้นหา \r\n9. ลงทะเบียนตรวจสอบใบหน้าพนักงาน พร้อมแจ้งเตือนไปยังระบบบริหารจัดการส่วนกลาง เมื่อ Scan ใบหน้าออกนอกพื้นที่และมีการกลับเข้ามาอีกรอบ >> ระบบแพลตฟอร์ม \r\n\r\n**เพิ่มเติม \r\nนัด Demo : 23 พฤศจิกายน 2568  (วันอาทิตย์)\r\nใช้ NVR Hikvision AI \r\nพี่ซีน คุยกับทาง Vender ขอคู่มือการดึง API ของกล้อง Hikvision (ซีนดำเนินการ Register ที่ https://tpp.hikvision.com/ เพื่อขอ API Doc รอ Approve 3 day)\r\nแอมป์ ทำ UX/UI สำหรับขึ้นหน้าบ้าน \r\nพี่แจ็ค เตรียม Server AI , จัดเตรียมกล้อง CCTV อ่านป้ายทะเบียน\r\n\r\nMeeting 27-10-2025\r\nTeam Thonburi : P\'Pop / P\'Sit / N\'Tom\r\nTeam Point IT : Zeen / Amp / Zeen น้อย\r\n\r\n1. ขอภาพมุมกล้องจากพี่สิทธิ์ เพื่อระบุจุดที่ต้องการดึงภาพมาทำ AI >> พี่สิทธิ์ส่งมาให้แล้ว\r\n2. ซีนน้อยกำหนดจุดที่จะดึงภาพมาทำ AI\r\n3. ซีนน้อยดึง Link RTSP มาเข้า AI\r\n4 ซีนส่งแผนการดำเนินการ Demo ให้พี่ป๊อป\r\n5. กำหนดวันลงระบบ Demo 23-11-2025', 'Resolved', 'Low', 'Low', 'Application', 'Meeting', 'Internal', 'Sprint planning / retrospective', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 168, '2025-10-31 14:44:16', 'Within SLA', '2025-10-24 14:13:00', '2025-10-31 14:13:00', '2025-10-29 12:37:27', NULL, 'Office', NULL, '2025-10-24 07:44:16', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 05:37:27', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('7e24f638b40854acf4a162a469026e9e', 'TCK-202510-0003', 'e90e5ea9-c4b9-4657-a0e7-0ce63daf791e', 'Incident', 'แจ้งปัญหาหน้า Dashboad กราฟไม่เรียงตามวันที่', 'แจ้งปัญหาหน้า Dashboad กราฟไม่เรียงตามวันที่ เนื่องด้วยลูกค้าเข้ามาดูหน้าแสดงผล Dashbaord กราฟ แสดงจำนวนลูกค้า แยกตามวัน, แสดงจำนวนลูกค้า แยกตามวันและแยกโซนไม่จัดตามวันไล่ตามวันนี้', 'Resolved', 'Low', 'Low', 'Department', 'Data/Analytics', 'BI/Dashboard', 'Report incorrect', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 72, '2025-10-17 15:24:55', 'Within SLA', '2025-10-14 10:22:00', '2025-10-17 10:22:00', '2025-10-14 18:44:00', NULL, 'Office', NULL, '2025-10-14 08:24:55', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 11:44:00', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('7eba9a30705d2c1ddc0a1e2d6cac3d96', 'TCK-202511-0018', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Incident', 'ขอเปลี่ยนรายการจองรถ ที่จองของรถที่อนุมัติไปแล้ว จะขอเปลี่ยนเป็นวันที่5/11/68 - 5/11/68 (9.30-12.00น.)', 'User อยากจะแก้ไขวันที่จองของรถที่อนุมัติไปแล้ว จะขอเปลี่ยนเป็นวันที่5/11/68 - 5/11/68 (9.30-12.00น.)\r\nid : 97604c44-b3f32af6-690ab411-b336f98f\r\nตามไฟล์แนบ', 'Resolved', 'Low', 'Medium', 'Application', 'Database', 'SQL Server', 'Agent job failed', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-11-13 11:03:04', 'Within SLA', '2025-11-10 08:56:00', '2025-11-13 08:56:00', '2025-11-10 11:08:38', NULL, 'Office', NULL, '2025-11-10 04:03:04', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 04:08:38', 'f384c704-5291-4413-8f52-dc25e10b5d4f'),
('818fa45a1b16cd16b831a6f1b6e63d72', 'TCK-202510-0046', '74c45e80-c867-46cc-919b-c6e4c7d0c076', 'Service', 'ประชุมสรุปแผนการดำเนินงานกับทางลูก ในส่วนของการนำเสนอฟังก์ชันและ Module สำหรับการทดสอบใช้งานจริง Dome', 'ประชุมสรุปแผนการดำเนินงานกับทางลูก ในส่วนของการนำเสนอฟังก์ชันและ Module สำหรับการทดสอบใช้งานจริง Dome \r\nโดยรายละเอียดมีดังนี้ \r\nกระบวนการและขั้นตอน \r\n1. ทางทีมพอทย์เลือกจุดที่ต้องการทดสอบบนแผนภาพ ส่งให้กับทางลูกคา้ \r\n2. ลูกค้าขออนุญาติการใช้งานกล้องตามข้อที่ 1 \r\n3. ลูกค้าส่ง Link : Stream RTSP CCTV ให้ทางบริษัทตามข้อ 1\r\n4. ทางทีมนำข้อมูลที่ได้มาทดสอบดึง  Stream RTSP CCTV \r\n5. กรณีจากข้อ 4 หากดึง  Stream RTSP CCTV  แล้วข้อมูลที่ได้ไม่ตรงความต้องการ อาทิเช่น มุมกล้องไม่เห็นใบหน้า หรือตรงกับความต้องการของ Module บริษัทจะขอเข้าปรับกล้องหน้างาน\r\n6. นัดติดตั้งเครื่อ Server AI Box ที่ห้อง Server ของลูกค้าและระบบแพลตฟอร์ม เพื่อ Demo (ทำได้ช่วงเลิกงาน)\r\n\r\nสรุปวันเข้าติตตั้ง  23/11/2025 \r\nสิ่งที่ลูกค้าจะเห็นในการติดตั้ง Memo\r\n1. Module 1-7 \r\n\r\nคำถามเพิ่มเติม : \r\n1. ลูกค้าของดึงข้อมูลเพื่อมาทำ BI เพิ่ม  >> ตอบรับ \r\n2. ลูกค้าสอบถามหากขึ้นระบบแพลตฟอร์มสามารถ เชื่อมต่อกล้อง CCTV งานบูธได้ด้วยหรือไม่ >> ไม่ตอบรับ \r\n3. ทางผู้บริหารสอบถาม \r\n    3.1. หากไม่โอเคสามารถยกเลิกได้ทันทีหรือไม่ >> ตอบรับ (ตามงวดงาน)\r\n    3.2.  Module ต่างๆใช้งานได้จริงหรือไม่ \r\n    3.3. เจ้าหน้าที่พนักงานบริษัทของลูกค้า จะใช้หรือไม่ \r\n\r\nสรุป\r\n     ลูกค้าขอ Action Plan สำหรับแผนการดำเนินงานล่าสุดอีกครั้ง', 'Resolved', 'Low', 'Low', 'Application', 'Meeting', 'Customer', 'Requirement / status update', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 168, '2025-11-03 15:03:25', 'Within SLA', '2025-10-27 09:45:00', '2025-10-27 11:45:00', '2025-10-27 15:03:53', NULL, 'Office', NULL, '2025-10-27 08:03:25', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 08:03:53', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('82396eb89e2502198fa334db5707c050', 'TCK-202510-0018', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Incident', 'แจ้งปัญหา รายงานแบบ 3 ได้ัรับการอนุมัติแล้ว แต่ลายเซ็นต์แสดงไม่ครบ', 'แจ้งปัญหา รายงานแบบ 3 ได้ัรับการอนุมัติแล้ว แต่ลายเซ็นต์แสดงไม่ครบ\r\nรพ.สต. บ้านตาลหมัน  \r\nรายงานในวันที่  16/09/65 และวันที่ 18/09/2025', 'Resolved', 'Low', 'Low', 'Application', 'Development', 'Backend', 'API timeout / 5xx', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-20 15:24:26', 'Within SLA', '2025-10-17 15:17:00', '2025-10-20 15:17:00', '2025-10-17 16:02:20', NULL, 'Office', NULL, '2025-10-17 08:24:26', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-17 09:02:20', 'f384c704-5291-4413-8f52-dc25e10b5d4f'),
('887ac5a1a781314baf1c0823330b88fe', 'TCK-202511-0016', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Incident', 'แจ้งระบบจองยานพาหนะ อบจ.ชลบุรี เข้าใช้งานไม่แสดงข้อมูล', 'แจ้งระบบจองยานพาหนะ อบจ.ชลบุรี เข้าใช้งานไม่แสดงข้อมูล', 'On Process', 'Low', 'Medium', 'Application', 'Server', 'Virtualization', 'VM creation / template', 'e083a0dd-3393-44cd-b376-d876d6728d9a', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-11-10 15:25:19', 'Within SLA', '2025-11-07 15:22:00', '2025-11-10 15:22:00', NULL, NULL, 'Office', NULL, '2025-11-07 08:25:19', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', NULL, NULL),
('8b3f3eb00f76e9a698bcc04e0243a566', 'TCK-202510-0048', '7c67ce7e-ee05-487f-a763-4627899516bb', 'Service', 'จัดทำรายงานประจำเดือนสิงหาคม 2025 (Monthly Report) เทศบาลตำบลบ่อวิน', 'จัดทำรายงานประจำเดือนสิงหาคม 2025 (Monthly Report) เทศบาลตำบลบ่อวิน\r\nดึงข้อมูลเดือนสิงหาคม 2025\r\nตั้งแต่วันที่ 01/10/2025 - 31/10/2025\r\n\r\n\r\n1. ดึงข้อมูลจากระบบ KYD\r\n2. Convert ข้อมูลให้อยู่ในรูปแบบ Data (ข้อมูลที่สามารถนำไปทำกราฟได้)\r\n3. Copy เอกสารต้นแบบของเดือนเก่ามาดำเนินการต่อ\r\n4. นำข้อมูลจากข้อ 2 มาดำเนินการทำกราฟรูปแบบต่างๆ ตามหัวข้อเก่า และเปลี่ยนแปลงข้อมูลรายงานให้เป็นข้อมูลปัจจุบันและเดือนปัจจุบัน\r\n\r\n**ดำเนินการเสร็จแนบไฟล์ หรือ Link ออนไลน์ ในระบบนี้ได้เลยครับ พร้อมทั้งไฟล์ที่ดึงมาทุกไฟล์ทุกข้อมูลที่ดำเนินการ**', 'Resolved', 'Low', 'Low', 'Site', 'Project Management (การบริหารโครงการ)', 'Document Management', 'Monthly Report', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-03 18:04:34', 'Within SLA', '2025-10-27 18:02:00', '2025-11-03 18:02:00', '2025-10-31 17:04:20', NULL, 'Office', NULL, '2025-10-27 11:04:34', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-31 10:04:20', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6'),
('8b5dbcd8e12a7f3ada28f739b9f3c9f0', 'TCK-202510-0042', 'bcacb043-c719-47b0-8033-4bd80cabcff6', 'Service', 'จัดทำเอกสารออกแบบ (UX/UI Design) ระบบคิวสำหรับระบบเจาะเลือด เวอร์ชั่น V.1', 'จัดทำเอกสารออกแบบ (UX/UI Design) ระบบคิวสำหรับระบบเจาะเลือด เวอร์ชั่น V.1', 'Resolved', 'Low', 'Low', 'Application', 'Support', 'Documentation', 'User manual / SOP', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-10-31 18:40:18', 'Within SLA', '2025-10-24 18:25:00', '2025-10-31 18:25:00', '2025-10-24 18:40:47', NULL, 'Office', NULL, '2025-10-24 11:40:18', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 11:40:47', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('8e50c1e61286798d0c25cad94c874699', 'TCK-202510-0036', '0781e56d-1e40-4dec-8b65-0bd316277935', 'Service', 'ประชุมทีมภายใน Update งาน ISO Document งวดที่ 2', 'ประชุมทีมภายใน Update งาน ISO Document งวดที่ 2 \r\nกับทางพี่ไข่ \r\nอัพเดทการส่งมอบงานงวดที่ 2 ขอเลื่อนไปรวมกับรอบที่ 3 ในวันที่ 12 พ.ย. 2025\r\nเนื่องจาก Main Flow ผิดทำให้ต้องแก้ไขใหม่', 'Resolved', 'Low', 'Low', 'Application', 'Meeting', 'Minutes', 'MoM / action tracker / decision log', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-25 22:30:52', 'Within SLA', '2025-10-22 16:10:00', '2025-10-25 16:10:00', '2025-10-22 22:31:56', NULL, 'Office', NULL, '2025-10-22 15:30:52', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:31:56', 'c9747f60-de4e-4de1-9dcc-37d317c2057d');
INSERT INTO `service_tickets` (`ticket_id`, `ticket_no`, `project_id`, `ticket_type`, `subject`, `description`, `status`, `priority`, `urgency`, `impact`, `service_category`, `category`, `sub_category`, `job_owner`, `reporter`, `source`, `sla_target`, `sla_deadline`, `sla_status`, `start_at`, `due_at`, `resolved_at`, `closed_at`, `channel`, `deleted_at`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
('8eafedc8ba6f5fc271822ab67c72b744', 'TCK-202511-0006', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Service', 'ลูกค้าขอ Timeline การ Change ระบบจองยานพาหนะตาม Requirment ล่าสุด', 'ลูกค้าขอ Timeline การ Change ระบบจองยานพาหนะตาม Requirment ล่าสุด  \r\nคุณเบริด์ แจ้งทางคุณบี (Support) อบจ.ชลบุรี  สอบถาม Timeline การ Change ระบบจองยานพาหนะตาม Requirment ล่าสุด กำหนดเสร็จวันที่เท่าไหร่ \r\nเพื่อวางแผนการนัดประชุม และทดสอบระบบตาม Flow ใหม่', 'Resolved', 'Low', 'Medium', 'Application', 'Change', 'Change Request', 'Timeline / Plan Inquiry', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-11-07 09:06:04', 'Within SLA', '2025-11-03 13:30:00', '2025-11-06 13:30:00', '2025-11-04 09:08:50', NULL, 'Office', NULL, '2025-11-04 02:06:04', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 02:08:50', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('9439b4fe59f3705e5dd798f99ea78bf7', 'TCK-202510-0052', '837e23f3-a09d-4927-bad4-27837014451e', 'Service', 'จัดทำรายงานประจำเดือนสิงหาคม 2025 (Monthly Report) เทศบาลตำบลมาบตาพุด', 'จัดทำรายงานประจำเดือนสิงหาคม 2025 (Monthly Report) เทศบาลตำบลมาบตาพุด\r\nดึงข้อมูลเดือนสิงหาคม 2025\r\nตั้งแต่วันที่ 01/10/2025 - 31/10/2025\r\n\r\n\r\n1. ดึงข้อมูลจากระบบ KYD\r\n2. Convert ข้อมูลให้อยู่ในรูปแบบ Data (ข้อมูลที่สามารถนำไปทำกราฟได้)\r\n3. Copy เอกสารต้นแบบของเดือนเก่ามาดำเนินการต่อ\r\n4. นำข้อมูลจากข้อ 2 มาดำเนินการทำกราฟรูปแบบต่างๆ ตามหัวข้อเก่า และเปลี่ยนแปลงข้อมูลรายงานให้เป็นข้อมูลปัจจุบันและเดือนปัจจุบัน\r\n\r\n**ดำเนินการเสร็จแนบไฟล์ หรือ Link ออนไลน์ ในระบบนี้ได้เลยครับ พร้อมทั้งไฟล์ที่ดึงมาทุกไฟล์ทุกข้อมูลที่ดำเนินการ**', 'Resolved', 'Low', 'Low', 'Department', 'Project Management (การบริหารโครงการ)', 'Document Management', 'Monthly Report', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-03 19:45:53', 'Within SLA', '2025-10-27 19:43:00', '2025-11-03 19:43:00', '2025-10-31 17:02:55', NULL, 'Office', NULL, '2025-10-27 12:45:53', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-31 10:02:55', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6'),
('9547da3d6ad0c6013ae7a098a9db6d61', 'TCK-202510-0051', '36f9bfe8-bf92-42cb-82d0-d3080529cc6a', 'Incident', 'ประชุมภายในสำหรับวางแผนการพัฒนาระบบ Smart Showroom', 'อ้างอิง : TCK-202510-0046 , \r\nประชุมภายในสำหรับวางแผนการพัฒนาระบบ Smart Showroom \r\nผู้เข้าร่วม \r\nพี่ขวัญ, พี่ซีน, แอมป์, ซีนน้อย \r\nเรื่องการออกแบบพัฒนาระบบตาม Module ทั้งหมด 9 รายการ \r\n\r\n1. AI ตรวจสอบวัตถุบนเคาน์เตอร์และโต๊ะ >> ใช้กล้อง 1 ตัว\r\n2. AI ตรวจสอบเวลาที่ลูกค้าอยู่ในพื้นที่ >> ใช้กล้อง 2 ตัว\r\n3. AI นับจำนวนลูกค้าที่เข้าและออกในโชว์รูม >> ใช้กล้อง 1 ตัว\r\n4. AI แยกลูกค้าและพนักงาพ >> ใช้กล้องข้อที่ 2.\r\n5. AI อ่านป้ายทะเบียนรถ >> ใช้กล้อง 1 ตัว\r\n6. AI ตรวจจับยานพาหนะที่เข้ามาและออกไปจากบริเวณศูนย์บริการ >> ใช้กล้องเดียวกับข้อ 5.\r\n7. AI นับจำนวนรถที่อยู่ในพื้นที่ >> ใช้กล้อง 1 ตัว ไม่มีอะไรบดบังพื้นที่ >> ใช้กล้อง 2 ตัว\r\n8. AI ค้นหาดูด้วยภาษาพูด (อาทิ รถสีแดงป้ายทะเบียน 5กก 5921) >> ดึง Ifream จาก Point ไปใช้ในการค้นหา\r\n9. ลงทะเบียนตรวจสอบใบหน้าพนักงาน พร้อมแจ้งเตือนไปยังระบบบริหารจัดการกลาง เมื่อ Scan ใบหน้าออกนอกพื้นที่และมีการกลับเข้ามาอีกรอบ >> ระบบแพลตฟอร์ม\r\n\r\nสรุป \r\n    - ทำให้ระบบ K-Lynx โดยพี่ขวัญ โดยพี่ขวัญจะช่วยดำเนินการขึ้นให้ในวันที่  3/11/2025 เป็นต้นไป \r\n    - ส่วนของ Setting \r\n         * 1 กล้อง สามารถใช้งานได้ มากกว่า 1 Module สามาถรเปิดได้มากกว่า 1 (Enable AI จากกล้อง)\r\n         * เรื่องเพิ่มใบหน้า ปรับจากหน้า K-watch งานตำรวจ \r\nSetting : \r\n1. Setting\r\n    - Stream RTSP \r\n    - Set ROI (Multi) /Poligon\r\n    - Set เปอร์เซ็น % ของพื้นที่ \r\n2, 4. Set ROI (Multi) /Poligon\r\n3. Set ROI (Multi) /เส้นตรง 2 เส้น\r\n4, 5. Set Labal/Name กำหนดกล้องขาเข้า/ขาออก\r\n7. Set ROI (Multi) /Poligon  (เมื่อเปิดกล้องนับหรือแคปภาพนับจำนวนรถครั้งแรก เมื่อมีรถออก/เข้าให้นับต่อจากเดิม)\r\n8. AI Docker Pontit\r\n9. K-watch งานตำรวจ', 'Resolved', 'Low', 'Low', 'Department', 'Meeting', 'Internal', 'Sprint planning / retrospective', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-03 19:33:05', 'Within SLA', '2025-10-27 19:07:00', '2025-11-03 19:07:00', '2025-10-29 12:36:52', NULL, 'Office', NULL, '2025-10-27 12:33:05', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 05:36:52', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('a005d2bc5a0c79101ef49cdfd84e88bf', 'TCK-202511-0021', '00d0728f-5754-4490-b568-55cb9f79da53', 'Service', 'ออกแบบ UX/UI Design โครงการพัฒนาระบบ New Watchman (หน้าสืบค้น  (ต่อ)', 'ออกแบบ UX/UI Design โครงการพัฒนาระบบ New Watchman (หน้าสืบค้น)\r\nประกอบด้วย\r\nหน้าสืบค้น >> เมนูหน้าการสืบค้น >> ออกแบบหน้า ดำเนินการหน้าแสดงผล เพิ่ม ลบ แก้ไข และการค้นหา >> ประกอบด้วย\r\n1. แฟ้มระเบียนคำสั่ง \r\n     1.1. กฎหมาย ระเบียบ คำสั่ง และแนวทางปฏิบัติราชการ\r\n2.  แฟ้มยึดรถ\r\n3. แฟ้มต่างด้าว', 'Resolved', 'Low', 'Low', 'Application', 'Development', 'UX/UI Design', 'Create', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-17 17:15:48', 'Within SLA', '2025-11-10 09:10:00', '2025-11-17 17:10:00', '2025-11-10 17:16:04', NULL, 'Office', NULL, '2025-11-10 10:15:48', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 10:16:04', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('a2f4f755e19bf78413b83554349a2bab', 'TCK-202511-0010', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Incident', 'ระบบจองยานพาหนะ อบจ.ชลบุรี แสดงลำดับการอนุมัติไม่ถูกต้อง', 'ระบบจองยานพาหนะ อบจ.ชลบุรี แสดงลำดับการอนุมัติไม่ถูกต้อง \r\nรบกวนตรวจสอบ สถานะการอนุมัติให้ทีครับพี่ พอดีผมเห็นขึ้น รอการอนุมัติจากลำดับที่ 01, รอการอนุมัติจากลำดับที่ 21,รอการอนุมัติจากลำดับที่ 11 แบบนี้ครับ', 'Resolved', 'Low', 'Low', 'Application', 'IT Service', 'Dev Environment', 'API Unavailable', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-11 16:03:57', 'Within SLA', '2025-11-04 16:01:00', '2025-11-11 16:01:00', '2025-11-04 16:08:43', NULL, 'Office', NULL, '2025-11-04 09:03:57', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 09:08:43', 'f384c704-5291-4413-8f52-dc25e10b5d4f'),
('a5eb1b3774bb8de6a806482e70a0c608', 'TCK-202510-0015', '00d0728f-5754-4490-b568-55cb9f79da53', 'Incident', 'แจ้งปัญหาเข้าเพิ่มข้อมูลรายการปกครอง+อัยการระบบเด้งไปที่หน้า Main', 'แจ้งปัญหาเข้าเพิ่มข้อมูลรายการปกครอง+อัยการระบบเด้งไปที่หน้า Main\r\nเข้าหน้า รายการปกครอง+อัยการ >> กด \"เพิ่มข้อมูล\" >> กรอกข้อมูล >> กดบันทึก เด้งไปที่หน้า Main ดังภาพแนบ', 'Resolved', 'Low', 'Low', 'Application', 'Development', 'Frontend', 'UI bug / layout', 'f30e8b87-d047-4bca-9b34-d223170df87c', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-19 15:01:02', 'Within SLA', '2025-10-16 14:53:00', '2025-10-19 14:53:00', '2025-10-16 15:58:34', NULL, 'Office', NULL, '2025-10-16 08:01:02', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 08:58:34', 'f30e8b87-d047-4bca-9b34-d223170df87c'),
('aa95c56d33fca80adb2f4305b9d22e7d', 'TCK-202510-0049', '36f9bfe8-bf92-42cb-82d0-d3080529cc6a', 'Service', 'ช่วยสรุป Project Pipeline สำหร้บประชุนงานภายในทีม เพื่อสรุปโครงการและผลการดำเนินงาน', 'ช่วยสรุป Project Pipeline สำหร้บประชุนงานภายในทีม เพื่อสรุปโครงการและผลการดำเนินงาน\r\nผ่านระบบ : http://iss.pointit.co.th/sales', 'Resolved', 'Low', 'Low', 'Single User', 'Project Management (การบริหารโครงการ)', 'Document Management', 'Project Timeline/Schedule', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-03 18:37:24', 'Within SLA', '2025-10-27 13:30:00', '2025-10-27 14:35:00', '2025-10-27 18:38:20', NULL, 'Office', NULL, '2025-10-27 11:37:24', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 11:38:20', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('b180015710d27a896b70940424c14ee0', 'TCK-202511-0002', '00d0728f-5754-4490-b568-55cb9f79da53', 'Service', 'ออกแบบ UX/UI Design โครงการพัฒนาระบบ New Watchman (หน้าสืบค้น)', 'ออกแบบ UX/UI Design โครงการพัฒนาระบบ New Watchman (หน้าสืบค้น)\r\nประกอบด้วย \r\nหน้าสืบค้น >> เมนูหน้าการสืบค้น >> ออกแบบหน้า ดำเนินการหน้าแสดงผล เพิ่ม ลบ แก้ไข และการค้นหา >> ประกอบด้วย\r\n1. ข้อมูลคู่ครอง  \r\n2. ข้อมูลบุตร\r\n3. ข้อมูลพ่อแม่พี่น้อง\r\n4. ข้อมูลผังครอบครัว\r\n5. ข้อมูลการทำบัตร\r\n6. ข้อมูลการเปลี่ยนชื่อ', 'Resolved', 'Low', 'Low', 'Application', 'Development', 'UX/UI Design', 'Create', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-10 18:53:02', 'Within SLA', '2025-11-03 08:10:00', '2025-11-10 08:10:00', '2025-11-03 18:53:21', NULL, 'Office', NULL, '2025-11-03 11:53:02', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 11:53:21', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('b3353d226e75aeda28572314a7abe62f', 'TCK-202510-0032', 'bcacb043-c719-47b0-8033-4bd80cabcff6', 'Service', 'Review/Edit แก้ไข UI หน้าแสดงผลที่จอ Monitor  ช่องรอการเรียกคิว แสดงคิวและช่องโดยยังไม่มีเจ้าหน้าที่กดเรียก', 'Review/Edit แก้ไข UI หน้าแสดงผลที่จอ Monitor  ช่องรอการเรียกคิว แสดงคิวและช่องโดยยังไม่มีเจ้าหน้าที่กดเรียก', 'Resolved', 'Low', 'Low', 'Application', 'Development', 'UX/UI Design', 'Edit and modify', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-25 21:51:04', 'Within SLA', '2025-10-22 13:10:00', '2025-10-25 13:10:00', '2025-10-22 21:51:33', NULL, 'Office', NULL, '2025-10-22 14:51:04', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:51:33', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('ba35446efb9ef49c29fd4cb93181e225', 'TCK-202510-0021', '161e830e-355e-4364-acce-405857cf30b9', 'Service', 'ซื้อผ้าปูรองกล้องสีดำ 2 พื้น สำหรับใช้ในโครงการ Booth Central ลาดพร้าว', 'ออกข้างนอกหาซื้อผ้าปูรองกล้องสีดำ 2 พื้น สำหรับใช้ในโครงการ Booth Central ลาดพร้าว', 'Resolved', 'Low', 'Low', 'Application', 'Support', 'On-site Support', 'Field visit (ออกปฏิบัติงานนอกพื้นที่)', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 72, '2025-10-23 15:52:52', 'Within SLA', '2025-10-20 15:49:00', '2025-10-23 15:49:00', '2025-10-20 15:53:48', NULL, 'Office', NULL, '2025-10-20 08:52:52', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 08:53:48', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('bac7e3042320f2089eecefceee459b07', 'TCK-202510-0012', '8f6e2e97-d5af-4515-b4e4-60d60a6939e8', 'Service', 'หาข้อมูลเกี่ยวกับผลิตภัณฑ์ Tablat สำหรับจัดซื้อเพื่อเตรียมชุดกระเป๋า Health Ket Set จำนวน 5 ชุด', 'หาข้อมูลเกี่ยวกับผลิตภัณฑ์ Tablat สำหรับจัดซื้อเพื่อเตรียมชุดกระเป๋า Health Ket Set จำนวน 5 ชุด สืบเนื่องจากโครงการที่กำลังจะขึ้นของเทศบาลด่านสำโรง มีการตั้งเรื่องขึ้นโครงการสาธารณสุขเพื่อดูแลประชาชนผู้สูงอายุ จำนวน 11,000 คน\r\nโดยมีการกำหนดให้จัดเตรียมชุดกระเป๋า Health Ket Set จำนวน 5 ชุด  เพื่อติดประจำรถ EMS สำหรับตรวจวัดค่าสุขภาพเคลื่อนที่ได้ เป็นต้น', 'Resolved', 'Low', 'Low', 'Department', 'Project', 'Review', 'Steering committee (ประชุม)', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 72, '2025-10-19 09:28:10', 'Within SLA', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2025-10-16 09:28:45', NULL, 'Office', NULL, '2025-10-16 02:28:10', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 02:28:45', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('bc420db35f715fdc06e983365015152d', 'TCK-202510-0055', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', 'Service', 'ประชุมโครงการ   Fire Alarm สรุปปัญหาและทางเลือกการใช้งานระบบ', 'ประชุมโครงการ   Fire Alarm สรุปปัญหาและทางเลือกการใช้งานระบบ\r\nวันที่ 28-10-2025 11:00 น.\r\nผู้เข้าร่วมประชุม\r\n• พี่ปืน\r\n• พี่พิศาล\r\n• พี่ขวัญ\r\n• พี่แจ็ค\r\n• พี่ซีน\r\n• พี่โอ๋\r\n• คุณอั๋น\r\n• แอมป์\r\n\r\nเรื่อง\r\n1. การเลือกใช้ระบบและแพลตฟอร์ม ของเจ้าไหน\r\n2. ปัญหาแพลตฟอร์ม\r\n3. ปัญหาอุปกรณ์\r\n4. แผนการทำงาน การออกแบบแผนงาน', 'Resolved', 'Low', 'Low', 'Application', 'Meeting', 'Internal', 'Sprint planning / retrospective', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-05 11:58:44', 'Within SLA', '2025-10-29 11:45:00', '2025-11-05 11:45:00', '2025-10-29 12:56:02', NULL, 'Office', NULL, '2025-10-29 04:58:44', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 05:56:02', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('c0a1ccdaa3cc8449b8a8648d9f98ff91', 'TCK-202510-0019', 'bcacb043-c719-47b0-8033-4bd80cabcff6', 'Service', 'ออกแบบ UX/UI Design โครงการพัฒนาระบบ Queue Management (One Queue)', 'ออกแบบ UX/UI Design โครงการพัฒนาระบบ Queue Management (One Queue) \r\nประกอบด้วย \r\n1. หน้าจอลงทะเบียนตู้ Key Kiosk \r\n2. หน้าเรียกคิว สำหรับเจ้าหน้าที่ \r\n3. หน้าแสดงคิว Monitor สำหรับคนไข้', 'Resolved', 'Low', 'Low', 'Application', 'Development', 'UX/UI Design', 'Create', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-23 09:25:59', 'Within SLA', '2025-10-20 09:23:00', '2025-10-23 09:23:00', '2025-10-20 09:28:12', NULL, 'Office', NULL, '2025-10-20 02:25:59', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:28:12', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('c3fae15b8715db998f7865db7e4e89ec', 'TCK-202510-0017', '7c67ce7e-ee05-487f-a763-4627899516bb', 'Service', 'จัดทำเอกสารรายงานระบบเฝ้าระวังอุบัติเหตุและเจ็บป่วยฉุกเฉิน Smart Safety ประจำปี 2025', 'จัดทำเอกสารรายงานระบบเฝ้าระวังอุบัติเหตุและเจ็บป่วยฉุกเฉิน Smart Safety ประจำปี 2025\r\nวันที่ 16 ตุลาคม พ.ศ 2567 ถึง วันที่ 30 กันยายน พ.ศ 2568 \r\nโครงการ \"บ่อวิน สมาร์ท ซิตี้” ดูแลสุขภาพแบบอัจฉริยะ (Smart Health Care) สำหรับผู้สูงอายุ  ประจำปีงบประมาณ พ.ศ 2568', 'Resolved', 'Low', 'Low', 'Department', 'Support', 'Documentation', 'User manual / SOP', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-20 13:37:58', 'Within SLA', '2025-10-17 13:34:00', '2025-10-20 13:34:00', '2025-10-17 13:40:31', NULL, 'Office', NULL, '2025-10-17 06:37:58', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-17 06:40:31', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('c963ae31f67a991ec33b1cd411161af5', 'TCK-202510-0024', 'ec3b8fe6-72ec-44d3-92b3-40f8ee0bee87', 'Service', 'นำเสนองาน และทดสอบ Demo ระบบแพลตฟอร์ม กิน-อยู่-ดี และอุปกรณ์ เทศบาลด่านสำโรง', 'นำเสนองาน และทดสอบ Demo ระบบแพลตฟอร์ม กิน-อยู่-ดี และอุปกรณ์ เทศบาลด่านสำโรง\r\nสถานที่ : เทศบาลตำบลด่านสำโรง\r\nผู้เข้าร่วม \r\nคณะเทศบาลตำบลด่านสำโรง \r\nบริษัทบลูโซลูชั่น\r\nพี่ตั้ม, กวาง\r\nพี่ซีน, แอมป์', 'Resolved', 'Low', 'Low', 'Application', 'Presentation', 'POC', 'Proof of Concept presentation', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-24 08:31:35', 'Within SLA', '2025-10-21 08:27:00', '2025-10-24 08:27:00', '2025-10-21 16:49:58', NULL, 'Onsite', NULL, '2025-10-21 01:31:35', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:49:58', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('c9a4a76087ad93b691fa30f4dda02be0', 'TCK-202511-0020', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Incident', 'รบกวนแก้ไขข้อมูลการจองใช้รถ เดือน เมษายน 2568 ชื่อผู้จอง จาก นางสุภาพ สินเธาว์ เป็นชื่อ นายวรศักดิ์ จิตวงศ์  รพ.สต.เสม็ด อ.เมือง ครับ', 'รบกวนแก้ไขข้อมูลการจองใช้รถ เดือน เมษายน 2568 ชื่อผู้จอง จาก นางสุภาพ สินเธาว์ เป็นชื่อ นายวรศักดิ์ จิตวงศ์  รพ.สต.เสม็ด อ.เมือง ครับ\r\nตั้งแต่วันที่ 01/04/2025 -1/05/2025 ครับ', 'Resolved', 'Low', 'Medium', 'Application', 'Database', 'SQL Server', 'Agent job failed', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-11-13 16:52:10', 'Within SLA', '2025-11-10 16:48:00', '2025-11-13 16:48:00', '2025-11-10 17:15:36', NULL, 'Office', NULL, '2025-11-10 09:52:10', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 10:15:36', 'f384c704-5291-4413-8f52-dc25e10b5d4f'),
('cbf32b8dd3e4ef748855c23d4b162e8d', 'TCK-202510-0030', '161e830e-355e-4364-acce-405857cf30b9', 'Service', 'การตั้งค่า Cloudflare Tunnels สถาปัตยกรรมแบบ Zero Trust สำหรับเชื่อมต่อกล้อง CCTV', 'การตั้งค่า Cloudflare Tunnels สถาปัตยกรรมแบบ Zero Trust สำหรับเชื่อมต่อกล้อง CCTV', 'Resolved', 'Low', 'Low', 'Application', 'Security (ความปลอดภัย)', 'Access Control (การควบคุมการเข้าถึง)', 'Zero Trust Network Access (ZTNA) (การเข้าถึงเครือข่ายแบบ Zero Trust)', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-10-29 21:37:36', 'Within SLA', '2025-10-22 10:35:00', '2025-10-25 10:35:00', '2025-10-27 09:38:35', NULL, 'Office', NULL, '2025-10-22 14:37:36', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:38:35', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('db915fd3678a3c7a9a85d8aa606f02f5', 'TCK-202510-0027', '161e830e-355e-4364-acce-405857cf30b9', 'Service', 'ช่วย Map IP CCTV ให้สามารถออก Network ภายนอกสำหรับลูกค้าดู ผ่าน Cloudflare Tunnels', 'ช่วย Map IP CCTV ให้สามารถออก Network ภายนอกสำหรับลูกค้าดู ผ่าน Cloudflare Tunnels\r\nเนื่องจากโครงการมีการติดตั้งกล้อง CCTV ทั้งสิ้น 4 ตัว แบ่งเป็นสองมุม เสาละ 2 ตัวปรับทิศทางให้ครอบคลุมรถ Benz \r\nต่อเข้ากับ Network จาก Router 4G และเชื่อมต่อผ่าน Network Switch เก็บข้อมูลภาพผ่าน NVR', 'Resolved', 'Low', 'Low', 'Application', 'Network', 'IP Management', 'DHCP/DNS/IPAM', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 72, '2025-10-24 22:26:47', 'Within SLA', '2025-10-21 22:21:00', '2025-10-24 22:21:00', '2025-10-22 15:46:44', NULL, 'Office', NULL, '2025-10-21 15:26:47', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:46:44', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('dbbf9e3d828c7ae3afcc8877b1570cc1', 'TCK-202510-0028', '161e830e-355e-4364-acce-405857cf30b9', 'Incident', 'ติดตั้งกล้อง CCTV พร้อมปรับมุม และ ตั้งค่าระบบ AI Analysis เชื่อมต่อระบบแพลตฟอร์ม Dashboard', 'ติดตั้งกล้อง CCTV พร้อมปรับมุม และ ตั้งค่าระบบ AI Analysis เชื่อมต่อระบบแพลตฟอร์ม Dashboard\r\nณ เซ็นทรัลลาดพร้าว \r\nวันที่ 22/10/2025 เวลา 02.30 น - 06.00 น.', 'Resolved', 'Low', 'Low', 'External', 'Installation', 'CCTV', 'Camera install & alignment', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-24 22:33:24', 'Within SLA', '2025-10-21 22:28:00', '2025-10-24 22:28:00', '2025-10-22 15:38:56', NULL, 'Onsite', NULL, '2025-10-21 15:33:24', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:38:56', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('dc0fbcaa30d9222f9ec95cce4d040b49', 'TCK-202510-0013', '8f6e2e97-d5af-4515-b4e4-60d60a6939e8', 'Service', 'ขอบริการเข้าหน้างานเก็บ requirement ลูกค้าระบบเฝ้าระวังเหตุฉุกเฉินในผู้สูงอายุ เทศบาลตำบลด่านสำโรง (เพิ่มเติม)', 'ขอบริการเข้าหน้างานเก็บ requirement ลูกค้าระบบเฝ้าระวังเหตุฉุกเฉินในผู้สูงอายุ เทศบาลตำบลด่านสำโรง (เพิ่มเติม) \r\nคุณตี้ นัดคุยเครื่องคุณสมบัติเครื่อง และอุปกรณ์มีอะไรบ้าง ราคาแต่ละชิ้นเท่าไหร่ และ Scope และงานบริการของเรามีอะไรบ้าง', 'Resolved', 'Low', 'Low', 'Department', 'Offsite Work', 'Survey', 'Pre-sale/technical site survey', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 72, '2025-10-19 11:06:26', 'Within SLA', '2025-10-12 11:00:00', '2025-10-12 12:00:00', '2025-10-16 14:49:48', NULL, 'Onsite', NULL, '2025-10-16 04:06:26', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 07:49:48', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('e0639361c653a406d17686669cae0a1f', 'TCK-202510-0006', '0781e56d-1e40-4dec-8b65-0bd316277935', 'Service', 'ขอบริการ เข้าหน้างานเก็บ Requirment เพิ่มเติม เพื่อนำมาออกแบบ UXUI พัฒนาระบบ Queue ห้องเจาะเลือด', 'ขอบริการ เข้าหน้างานเก็บ Requirment เพิ่มเติม เพื่อนำมาออกแบบ UXUI พัฒนาระบบ Queue ห้องเจาะเลือด \r\nโดยขอนัดหมายล่วงหน้าตั้งแต่อาทิตย์ที่แล้ว ติดต่อผ่านทางพี่นิพนธ์ \r\nสถานที่ โรงพยาบาลธรรมศาสตร์รังสิต  ติดต่อพี่ วินัย TEL : 091-819-1544\r\nรายชื่อดังนี้\r\n1. คุณยุทธนา จตุรจิตราพร\r\n2. คุณนันทิกา จ้องจรัสแสง\r\n3. คุณอภิรักษ์ บางพุก', 'Resolved', 'Low', 'Low', 'Department', 'Support', 'Handover', 'Project/Task handover', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-17 20:12:54', 'Within SLA', NULL, NULL, '2025-10-16 09:36:58', NULL, 'Onsite', NULL, '2025-10-14 13:12:54', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 02:36:58', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('e3263d7e808e55aedf5b535768fc8e3a', 'TCK-202510-0041', '161e830e-355e-4364-acce-405857cf30b9', 'Service', 'บริการตรวจสอบและจัดเรียงข้อมูล (Data Cleansing)', 'บริการตรวจสอบและจัดเรียงข้อมูล (Data Cleansing) เนื่องจากระบบ AI มีการดึงภาพจากกล้อง CCTV จากงานบูธธนบุรีพานิณช เข้าระบบทำการวิเคราะห์ด้วย AI Leaning คัดแยกภาพออกมาตามฟังก์ชันต่างๆของระบบ เพื่อส่งออก Output แสดงออกมาในรูปแบบ Dashbaord ให้เข้าใจง่าย ทั้งกราฟและข้อมูล ซึ่งส่วนนี้ ผมได้ดำเนินการตรวจสอบข้อมูลที่ได้จาก AI เพื่อตรวจสอบและปรับเปลี่ยนข้อมูลที่ได้จาก AI ให้มีความถูกต้องมากที่สุด ในการแสดงผลให้ลูกค้าเป็นต้น\r\nกระบวนการ\r\n1. เข้า Link : http://192.168.1.98:9988/\r\n2. กรอกข้อมูลตามวันที่ : 22-24/10/2025\r\n3. คัดกรอกตรวจสอบรูปแยกพนักงานกับลูกค้า โดยยึดจากภาพรวม', 'Resolved', 'Low', 'Low', 'Application', 'Data/Analytics', 'BI/Dashboard', 'Report incorrect', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-10-31 16:45:28', 'Within SLA', '2025-10-24 16:41:00', '2025-10-31 16:41:00', '2025-10-24 16:46:40', NULL, 'Office', NULL, '2025-10-24 09:45:28', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 09:46:40', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('e568fe0d3564ce271bbb99a9d0f1f6aa', 'TCK-202510-0011', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Incident', 'แจ้งปัญหาระบบขอใช้ยานพาหนะ รพ.สต.บ้านใหม่เชิงเนิน รายการจองยานพาหนะชื่อผู้จองผิด', 'รพ.สต.บ้านใหม่เชิงเนิน เปลี่ยนรายการจองยานพาหนะทั้งหมดที่เป็นชื่อนางกาญจนา อาจศึก เปลี่ยนเป็นเป็น นางกัญญา วสิกรัตรน์ ครับ', 'Resolved', 'Low', 'Low', 'Department', 'Database', 'SQL Server', 'Agent job failed', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-10-18 13:44:13', 'Within SLA', '2025-10-15 08:39:00', '2025-10-18 08:39:00', '2025-10-15 13:53:03', NULL, 'Office', NULL, '2025-10-15 06:44:13', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 06:53:03', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('e6682bbd1a2807550f0b7b0235abd8c8', 'TCK-202510-0022', '4e831d8a-2f18-4d84-8d02-af10e4ed71ff', 'Incident', 'ประชุมทีมวางแผนการดำเนินโครงการ LAOS LIMs (LIS) ในปี 2026', 'ประชุมทีมวางแผนการดำเนินโครงการ LAOS LIMs (LIS) ในปี 2026 \r\n- Jan - 31 Dec 2026 \r\n- Intergrated Loboratory Service\r\n- PM/PA ทำ EMR + Nation Health ID \r\n- BI (NEW)\r\nProduct \r\n- Cloud Service\r\n- Third Tier Support\r\n- GeneXpert Implamtation', 'Resolved', 'Low', 'Low', 'Application', 'Meeting', 'Customer', 'Requirement / status update', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3', 'Portal', 72, '2025-10-23 16:40:31', 'Within SLA', '2025-10-20 16:36:00', '2025-10-23 16:36:00', '2025-10-20 16:41:56', NULL, 'Office', NULL, '2025-10-20 09:40:31', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 09:41:56', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('ec68368ab063a1cf203f014f7975d6b1', 'TCK-202510-0056', '00d0728f-5754-4490-b568-55cb9f79da53', 'Service', 'ออกแบบ UX/UI Design New Watchman ส่วนของเมนูงานสืบสวน', 'ออกแบบ UX/UI Design New Watchman ส่วนของเมนูงานสืบสวน \r\n     - หน้า Home \r\n     - หน้า แฟ้มหมายจับ', 'On Process', 'Low', 'Low', 'Application', 'Development', 'UX/UI Design', 'Create', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-05 16:58:00', 'Within SLA', '2025-10-29 09:25:00', '2025-11-05 09:25:00', NULL, NULL, 'Office', NULL, '2025-10-29 09:58:00', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', NULL, NULL),
('ec78cca5255db3e17e0371382cb93425', 'TCK-202511-0012', '21efec84-4bf1-47b2-8177-c0c36c973061', 'Service', 'ออกแบบ Dashboard ด้วย Google Looker Studio (หรือ Google Data Studio) สำหรับแสดงข้อมูลการเยี่ยมชมบูธ', 'ออกแบบ Dashboard ด้วย Google Looker Studio (หรือ Google Data Studio) สำหรับแสดงข้อมูลการเยี่ยมชมบูธ\r\nDashboard LInk : https://lookerstudio.google.com/reporting/72e8510a-8387-4d9b-8aaf-76575080fc12/page/7Q6IF\r\nData Link : https://docs.google.com/spreadsheets/d/1rezUJcwsPkhtYWWxAfUiN2NeYAA2xEemqYkxCE2hRvA/edit?gid=0#gid=0', 'Resolved', 'Low', 'Low', 'Department', 'Business Intelligence & Analytics Services (บริการด้านการวิเคราะห์ข้อมูลทางธุรกิจ)', 'Data Visualization (การแสดงผลข้อมูล)', 'Dashboard Development (การพัฒนาแดชบอร์ด)', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-12 14:11:10', 'Within SLA', '2025-11-05 14:06:00', '2025-11-12 14:06:00', '2025-11-05 14:11:28', NULL, 'Office', NULL, '2025-11-05 07:11:10', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-05 07:11:28', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('eefbd78e5f2426f6a79494712287f81b', 'TCK-202510-0053', '36f9bfe8-bf92-42cb-82d0-d3080529cc6a', 'Incident', 'ทดสอบระบบติดตามพัสดุ และระบบผู้มาติดต่อ โครงการสำนักงานส่งกำลังบำรุง กรุงเทพ/หุบสบู่', 'ทดสอบระบบติดตามพัสดุ และระบบผู้มาติดต่อ โครงการสำนักงานส่งกำลังบำรุง กรุงเทพ/หุบสบู่  \r\nวันอังคารที่ 28 ตุลาคม เวลา 8:00 น. ถึง 16.00น.\r\nพี่แว่นขอให้ทีม (แอมป์ , เอิร์ท , ตุลย์ ) เข้าไปปรับแต่งสัญญาณอุปกรณ์ RFID และระบบ Tracking Management ที่อาคารคลังอาวุธชั้น 2 สกบ.และระบบ Visitor Management ให้มีความเสถียร เพื่อรองรับการใช้งานระบบจริงของเจ้าหน้าที่', 'On Process', 'Low', 'Low', 'Department', 'Testing', 'UAT', 'Scenario error (ผู้ใช้ทดสอบ)', '6fbca1c7-761f-4027-ba4c-89e04832b717', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-04 18:31:23', 'Within SLA', '2025-10-28 07:30:00', '2025-10-28 18:22:00', NULL, NULL, 'Onsite', NULL, '2025-10-28 11:31:23', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 07:44:21', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('efda3db7e97b4a86927f55e464fa6562', 'TCK-202510-0031', '161e830e-355e-4364-acce-405857cf30b9', 'Service', 'ออกแบบ Dashboard ด้วย Google Looker Studio (หรือ Google Data Studio) สำหรับแสดงข้อมูลการเยี่ยมชมบูธ', 'ออกแบบ Dashboard ด้วย Google Looker Studio (หรือ Google Data Studio) สำหรับแสดงข้อมูลการเยี่ยมชมบูธ\r\nLink : https://lookerstudio.google.com/u/0/reporting/cb517742-af4f-4a4e-89d2-5a20ead3948c/page/7Q6IF\r\nData : https://docs.google.com/spreadsheets/d/1zlIOSSxSTzgPGmPq4dET6lcLrYHrEnTtmh2cC4Gu_UA/edit?gid=0#gid=0', 'Resolved', 'Low', 'Low', 'Department', 'Business Intelligence & Analytics Services (บริการด้านการวิเคราะห์ข้อมูลทางธุรกิจ)', 'Data Visualization (การแสดงผลข้อมูล)', 'Dashboard Development (การพัฒนาแดชบอร์ด)', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-10-29 21:43:18', 'Within SLA', '2025-10-22 10:45:00', '2025-10-25 10:45:00', '2025-10-27 09:36:30', NULL, 'Office', NULL, '2025-10-22 14:43:18', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:36:30', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('f3a0b4c735c95737d37c23cd33793ae9', 'TCK-202511-0013', '5c65a5f9-7d33-40b3-9f28-42ad0867aeb1', 'Service', 'ตรวจสอบการส่งมอบงาน การว่าจ้างงานการพัฒนาระบบจองยานพาหนะ อบจ.ชลบุรี ตาม Change Requirements', 'ตรวจสอบการส่งมอบงาน การว่าจ้างงานการพัฒนาระบบจองยานพาหนะ อบจ.ชลบุรี ตาม Change Requirements  \r\nเอกสาร : https://docs.google.com/document/d/1oCK_Adl9kvYfsy-qXnMytvjtBbloLEj8fBTMBYlQidU/edit?usp=sharing', 'On Process', 'Low', 'Low', 'Application', 'Testing', 'Unit Test', 'Failed case', '6fbca1c7-761f-4027-ba4c-89e04832b717', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 168, '2025-11-13 10:42:04', 'Within SLA', '2025-11-05 14:19:00', '2025-11-12 14:19:00', NULL, NULL, 'Office', NULL, '2025-11-06 03:42:04', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-06 03:45:15', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('fe2a02ab8e28f3c0f8d2e4f214f8ab23', 'TCK-202511-0005', 'b70608c1-6f57-4abd-bce0-9260962b0bb9', 'Incident', 'User แจ้งปัญหารายงานประจำเดือนสิงหาคม 2025 ไม่ถูกต้อง โครงการเฝ้่าระวังเหตุฉุกเฉินในผู้สูงอายุ', 'User แจ้งปัญหารายงานประจำเดือนสิงหาคม 2025 ไม่ถูกต้อง โครงการเฝ้่าระวังเหตุฉุกเฉินในผู้สูงอายุ \r\nเนื่องจากในรายงานแสดงข้อมูลไม่มีการเกิดเหตุ ซึ่งตามจริงหน้างานแจ้งมีการเกิดเหตุการณ์ 2 เคส ดังนี้ \r\n1. คุณน้ำ สุวรรณรัตน์ \r\n2. ลำดวน เมฆอรุณ', 'Resolved', 'Low', 'Medium', 'Application', 'Project Management (การบริหารโครงการ)', 'Document Management', 'Monthly Report', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Portal', 72, '2025-11-06 19:16:28', 'Within SLA', '2025-11-03 13:08:00', '2025-11-06 13:08:00', '2025-11-03 19:17:32', NULL, 'Office', NULL, '2025-11-03 12:16:28', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:17:32', 'c9747f60-de4e-4de1-9dcc-37d317c2057d'),
('fe96c34788500002362380163a086f15', 'TCK-202511-0015', '74c45e80-c867-46cc-919b-c6e4c7d0c076', 'Incident', 'เตรียมเครื่องใช้สำหรับการ backup data ของงาน ออกบูธ', 'ลงตัว backup data\r\n1. s3\r\n2. mongodb', 'Resolved', 'Low', 'Low', 'Department', 'Development', 'Backend', 'API timeout / 5xx', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'Portal', 168, '2025-11-13 13:56:08', 'Within SLA', '2025-11-06 13:54:00', '2025-11-13 13:54:00', '2025-11-06 13:56:12', NULL, 'Office', NULL, '2025-11-06 06:56:08', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', '2025-11-06 06:56:12', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0');

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
-- Dumping data for table `service_ticket_attachments`
--

INSERT INTO `service_ticket_attachments` (`attachment_id`, `ticket_id`, `file_name`, `file_path`, `file_size`, `file_type`, `mime_type`, `uploaded_by`, `uploaded_at`) VALUES
('028ab5f5-a97d-11f0-aff6-005056b8f6d0', '1646d2451f8f11f24cd214759fd6535b', '875658_0.jpg', '/sales/uploads/service_tickets/1646d2451f8f11f24cd214759fd6535b/CMT-f9e402f5-e6b4-4276-904e-e9f48ffe7e71__2c06ed03-776d-4c1b-8236-83529e2f0a24.jpg', 129486, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 04:12:14'),
('02a44201-af23-11f0-9a0c-005056b8f6d0', 'db915fd3678a3c7a9a85d8aa606f02f5', 'Thonburi Boot Champ (2).png', '/sales/uploads/service_tickets/db915fd3678a3c7a9a85d8aa606f02f5/CMT-1957affe-ec21-4000-bce3-4dcd0c7520a4__7056d92a-8310-47b6-9eaf-c9c9881759fd.png', 1382715, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:42:37'),
('0330af60-be1b-11f0-8604-005056b8f6d0', 'c9a4a76087ad93b691fa30f4dda02be0', '2025-11-10_16-52-22.jpg', '/sales/uploads/service_tickets/c9a4a76087ad93b691fa30f4dda02be0/CMT-99befafe-e438-4e33-be27-395465b8b2bd__e8400ad8-dd90-4c2e-9501-e0884dae5a6a.jpg', 35391, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 09:52:48'),
('0c968ae8-b95d-11f0-9a0c-005056b8f6d0', 'a2f4f755e19bf78413b83554349a2bab', 'messageImage_1762245795129_0.jpg', '/sales/uploads/service_tickets/a2f4f755e19bf78413b83554349a2bab/6909c17e5e25a_1762247038.jpg', 204701, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 09:03:58'),
('0cab78e5-b95d-11f0-9a0c-005056b8f6d0', 'a2f4f755e19bf78413b83554349a2bab', 'messageImage_1762245868510_0.jpg', '/sales/uploads/service_tickets/a2f4f755e19bf78413b83554349a2bab/6909c17e7ffae_1762247038.jpg', 187451, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 09:03:58'),
('0e1abfc1-b0a7-11f0-9a0c-005056b8f6d0', '1fc0b09aaf3f079bb9e4ca9bafd97bde', '2025-10-24_13-33-01.png', '/sales/uploads/service_tickets/1fc0b09aaf3f079bb9e4ca9bafd97bde/68fb240719ea1_1761289223.png', 1097192, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 07:00:23'),
('0e239f06-b0a7-11f0-9a0c-005056b8f6d0', '1fc0b09aaf3f079bb9e4ca9bafd97bde', '2025-10-24_13-33-43.png', '/sales/uploads/service_tickets/1fc0b09aaf3f079bb9e4ca9bafd97bde/68fb24072938b_1761289223.png', 982270, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 07:00:23'),
('0e297ee5-b0a7-11f0-9a0c-005056b8f6d0', '1fc0b09aaf3f079bb9e4ca9bafd97bde', '2025-10-24_13-52-32.png', '/sales/uploads/service_tickets/1fc0b09aaf3f079bb9e4ca9bafd97bde/68fb24072b47d_1761289223.png', 1397477, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 07:00:23'),
('0e2ae3da-b0a7-11f0-9a0c-005056b8f6d0', '1fc0b09aaf3f079bb9e4ca9bafd97bde', '2025-10-24_13-59-39.png', '/sales/uploads/service_tickets/1fc0b09aaf3f079bb9e4ca9bafd97bde/68fb240734bd6_1761289223.png', 1807241, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 07:00:23'),
('1e860d6b-b0be-11f0-9a0c-005056b8f6d0', 'e3263d7e808e55aedf5b535768fc8e3a', '2025-10-24_16-32-02.png', '/sales/uploads/service_tickets/e3263d7e808e55aedf5b535768fc8e3a/68fb4ab914e4e_1761299129.png', 389057, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 09:45:29'),
('1ee9e694-b2dd-11f0-9a0c-005056b8f6d0', '632456371e6b634ceb7b30c85cf89ebf', '2025-10-27_09-32-05.png', '/sales/uploads/service_tickets/632456371e6b634ceb7b30c85cf89ebf/68fed9bbd0e77_1761532347.png', 348102, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:32:27'),
('1eebd82c-b2dd-11f0-9a0c-005056b8f6d0', '632456371e6b634ceb7b30c85cf89ebf', '2025-10-27_09-32-13.png', '/sales/uploads/service_tickets/632456371e6b634ceb7b30c85cf89ebf/68fed9bbd4399_1761532347.png', 255868, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:32:27'),
('217d7a59-ae19-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', '320706.jpg', '/sales/uploads/service_tickets/6c6595bb55eb3374513891cfce0bec7e/CMT-1634b1ef-a025-47c1-a7ef-c747d2e6bfec__3c36a48a-82c3-4a16-8263-36ed3fb2a5c2.jpg', 77037, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 00:59:04'),
('2184857b-ae19-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', '1760497316977.jpg', '/sales/uploads/service_tickets/6c6595bb55eb3374513891cfce0bec7e/CMT-1634b1ef-a025-47c1-a7ef-c747d2e6bfec__651ae816-2b4e-4414-96d2-c434e548f055.jpg', 391719, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 00:59:04'),
('2185196d-ae19-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', '1760935811168_0.jpg', '/sales/uploads/service_tickets/6c6595bb55eb3374513891cfce0bec7e/CMT-1634b1ef-a025-47c1-a7ef-c747d2e6bfec__a1b907aa-8f8e-4c2a-a5e2-a8b4da48d110.jpg', 626353, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 00:59:04'),
('218593b0-ae19-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', '1760935898199_0.jpg', '/sales/uploads/service_tickets/6c6595bb55eb3374513891cfce0bec7e/CMT-1634b1ef-a025-47c1-a7ef-c747d2e6bfec__1e3497bf-0043-41c6-91c3-6dd63cba456b.jpg', 440276, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 00:59:04'),
('21870ffb-ae19-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', '1760935942027_0.jpg', '/sales/uploads/service_tickets/6c6595bb55eb3374513891cfce0bec7e/CMT-1634b1ef-a025-47c1-a7ef-c747d2e6bfec__350604af-12df-4aaf-a692-f93e2ee2caf7.jpg', 548389, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 00:59:04'),
('21b06501-ae19-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', '1760940753576.jpg', '/sales/uploads/service_tickets/6c6595bb55eb3374513891cfce0bec7e/CMT-1634b1ef-a025-47c1-a7ef-c747d2e6bfec__4c783d08-a108-4774-be13-6f6193354b2a.jpg', 573660, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 00:59:04'),
('21b0efe0-ae19-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', 'S__810360834.jpg', '/sales/uploads/service_tickets/6c6595bb55eb3374513891cfce0bec7e/CMT-1634b1ef-a025-47c1-a7ef-c747d2e6bfec__fe5d08ee-cda7-4956-8f80-8c434c27f02f.jpg', 231295, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 00:59:04'),
('23a36fa8-b5a1-11f0-9a0c-005056b8f6d0', '6becc7cc78b8b6b8e8066e923c21d181', '2025-10-30_21-56-36.jpg', '/sales/uploads/service_tickets/6becc7cc78b8b6b8e8066e923c21d181/69037da9be35c_1761836457.jpg', 563435, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-30 15:00:57'),
('23a7e2c4-b5a1-11f0-9a0c-005056b8f6d0', '6becc7cc78b8b6b8e8066e923c21d181', '2025-10-30_21-57-00.jpg', '/sales/uploads/service_tickets/6becc7cc78b8b6b8e8066e923c21d181/69037da9c5e72_1761836457.jpg', 169724, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-30 15:00:57'),
('23af6b29-b5a1-11f0-9a0c-005056b8f6d0', '6becc7cc78b8b6b8e8066e923c21d181', '2025-10-30_21-57-15.jpg', '/sales/uploads/service_tickets/6becc7cc78b8b6b8e8066e923c21d181/69037da9c7112_1761836457.jpg', 158219, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-30 15:00:57'),
('24541250-b5a1-11f0-9a0c-005056b8f6d0', '6becc7cc78b8b6b8e8066e923c21d181', '2025-10-30_21-57-51.jpg', '/sales/uploads/service_tickets/6becc7cc78b8b6b8e8066e923c21d181/69037daae5307_1761836458.jpg', 566929, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-30 15:00:58'),
('280b0a10-bdea-11f0-8604-005056b8f6d0', '7eba9a30705d2c1ddc0a1e2d6cac3d96', '2025-11-10_10-41-19.jpg', '/sales/uploads/service_tickets/7eba9a30705d2c1ddc0a1e2d6cac3d96/691163f91dc31_1762747385.jpg', 449592, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 04:03:05'),
('2950d5c1-a968-11f0-aff6-005056b8f6d0', '1646d2451f8f11f24cd214759fd6535b', 'Screenshot_20251015_084233.jpg', '/sales/uploads/service_tickets/1646d2451f8f11f24cd214759fd6535b/68eefc2425124_1760492580.jpg', 517299, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 01:43:00'),
('2b95d352-bdf3-11f0-8604-005056b8f6d0', '682b8b2fc1c5de91674a1773db6f539d', '32693.jpg', '/sales/uploads/service_tickets/682b8b2fc1c5de91674a1773db6f539d/691173187dcc5_1762751256.jpg', 15042, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 05:07:36'),
('2ba959f2-b0b4-11f0-9a0c-005056b8f6d0', '5356767837cd88ec0215243511c749ab', 'Screenshot 2025-10-24 153204.png', '/sales/uploads/service_tickets/5356767837cd88ec0215243511c749ab/CMT-065d9418-a379-4239-8279-0c2c3cb711a4__2b58b43b-80bc-4064-8802-6d38becabdb1.png', 76530, 'png', 'image/png', '3768e84a-18c0-49d9-94dd-09b44c7c9a7d', '2025-10-24 08:34:16'),
('2f0a5e31-af54-11f0-9a0c-005056b8f6d0', '3f4fcda2cc17020648deacae219f4708', '2025-10-21_22-46-41.jpg', '/sales/uploads/service_tickets/3f4fcda2cc17020648deacae219f4708/68f8eb7daffbd_1761143677.jpg', 258162, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:34:37'),
('321ba66e-a980-11f0-aff6-005056b8f6d0', '1646d2451f8f11f24cd214759fd6535b', '875657_0.jpg', '/sales/uploads/service_tickets/1646d2451f8f11f24cd214759fd6535b/CMT-c12f0a5b-7bc5-4135-baa5-4dd87e220df4__aa4a1396-3806-453a-8912-efbe0689dbdc.jpg', 167209, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 04:35:02'),
('32ac6e1d-a988-11f0-aff6-005056b8f6d0', '21e5cc0d2aae7047c2e2bbe663b811e6', '2025-10-15_12-24-38.jpg', '/sales/uploads/service_tickets/21e5cc0d2aae7047c2e2bbe663b811e6/68ef31e3b5fd1_1760506339.jpg', 96928, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 05:32:19'),
('32b9a5f3-a988-11f0-aff6-005056b8f6d0', '21e5cc0d2aae7047c2e2bbe663b811e6', 'S__53436427.jpg', '/sales/uploads/service_tickets/21e5cc0d2aae7047c2e2bbe663b811e6/68ef31e3cba31_1760506339.jpg', 167008, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 05:32:19'),
('32bfd0cc-a988-11f0-aff6-005056b8f6d0', '21e5cc0d2aae7047c2e2bbe663b811e6', 'S__53436428.jpg', '/sales/uploads/service_tickets/21e5cc0d2aae7047c2e2bbe663b811e6/68ef31e3ccc88_1760506339.jpg', 138105, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 05:32:19'),
('3575f68b-ae19-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', 'Presentation-Benz-THONBURI-Re3.pdf', '/sales/uploads/service_tickets/6c6595bb55eb3374513891cfce0bec7e/CMT-27c8347a-5a9c-4c88-a104-6c880cc927f8__ac7b9b02-6541-4134-bb96-5828e3f4fcd6.pdf', 8941453, 'pdf', 'application/pdf', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 00:59:37'),
('37e18b0e-badd-11f0-9a0c-005056b8f6d0', '7718ca773579e9622a2e80901a7484b1', 'Screenshot from 2025-11-06 13-53-29.png', '/sales/uploads/service_tickets/7718ca773579e9622a2e80901a7484b1/690c46071f6fd_1762412039.png', 179428, 'png', 'image/png', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', '2025-11-06 06:53:59'),
('39a40abc-be1e-11f0-8604-005056b8f6d0', 'a005d2bc5a0c79101ef49cdfd84e88bf', '2025-11-10_17-15-01.jpg', '/sales/uploads/service_tickets/a005d2bc5a0c79101ef49cdfd84e88bf/6911bb54749a2_1762769748.jpg', 52982, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 10:15:48'),
('3b3d3a03-ad62-11f0-9a0c-005056b8f6d0', '417d339d06500f15a6b7531da0c7cd28', 'สกรีนช็อต 2025-10-20 094102_0.jpg', '/sales/uploads/service_tickets/417d339d06500f15a6b7531da0c7cd28/68f5a7fc2452b_1760929788.jpg', 27004, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 03:09:48'),
('3b4a4230-ad62-11f0-9a0c-005056b8f6d0', '417d339d06500f15a6b7531da0c7cd28', 'สกรีนช็อต 2025-10-20 094117_0.jpg', '/sales/uploads/service_tickets/417d339d06500f15a6b7531da0c7cd28/68f5a7fc3930e_1760929788.jpg', 53083, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 03:09:48'),
('3cd6ca3b-a8fd-11f0-aff6-005056b8f6d0', '2bd0058981314c1250b10a592f9af020', '2025-10-14_19-55-51.png', '/sales/uploads/service_tickets/2bd0058981314c1250b10a592f9af020/CMT-ced8b0ae-5e3f-4cad-8227-ccee6ff406a5__82a35ed3-2302-4e27-9732-68f30524f3ce.png', 41425, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 12:57:36'),
('3e8d8f91-a992-11f0-aff6-005056b8f6d0', 'e568fe0d3564ce271bbb99a9d0f1f6aa', '2025-10-15_13-40-40.jpg', '/sales/uploads/service_tickets/e568fe0d3564ce271bbb99a9d0f1f6aa/68ef42be9cc03_1760510654.jpg', 84317, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 06:44:14'),
('3e9558be-a992-11f0-aff6-005056b8f6d0', 'e568fe0d3564ce271bbb99a9d0f1f6aa', 'messageImage_1760510319429.jpg', '/sales/uploads/service_tickets/e568fe0d3564ce271bbb99a9d0f1f6aa/68ef42bea9e99_1760510654.jpg', 144233, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 06:44:14'),
('41dd8ce2-b2dc-11f0-9a0c-005056b8f6d0', '304700a01748aa660aa5afd0930f1a0e', '2025-10-27_09-15-40.png', '/sales/uploads/service_tickets/304700a01748aa660aa5afd0930f1a0e/68fed848f3a36_1761531976.png', 97125, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:26:17'),
('42757f54-aa66-11f0-9a0c-005056b8f6d0', 'a5eb1b3774bb8de6a806482e70a0c608', '8403.jpg', '/sales/uploads/service_tickets/a5eb1b3774bb8de6a806482e70a0c608/68f0a63ea68c5_1760601662.jpg', 101928, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 08:01:02'),
('4298c318-aa66-11f0-9a0c-005056b8f6d0', 'a5eb1b3774bb8de6a806482e70a0c608', '8491.jpg', '/sales/uploads/service_tickets/a5eb1b3774bb8de6a806482e70a0c608/68f0a63edf4bf_1760601662.jpg', 68119, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 08:01:02'),
('42a05cc9-aa66-11f0-9a0c-005056b8f6d0', 'a5eb1b3774bb8de6a806482e70a0c608', '8492.jpg', '/sales/uploads/service_tickets/a5eb1b3774bb8de6a806482e70a0c608/68f0a63ee01d0_1760601662.jpg', 73226, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 08:01:02'),
('42a0e599-aa66-11f0-9a0c-005056b8f6d0', 'a5eb1b3774bb8de6a806482e70a0c608', '8493.jpg', '/sales/uploads/service_tickets/a5eb1b3774bb8de6a806482e70a0c608/68f0a63eec4b0_1760601662.jpg', 72816, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 08:01:02'),
('42a27e91-aa66-11f0-9a0c-005056b8f6d0', 'a5eb1b3774bb8de6a806482e70a0c608', '714600.jpg', '/sales/uploads/service_tickets/a5eb1b3774bb8de6a806482e70a0c608/68f0a63eeef73_1760601662.jpg', 95911, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 08:01:02'),
('4913fdff-b0be-11f0-9a0c-005056b8f6d0', 'e3263d7e808e55aedf5b535768fc8e3a', '2025-10-24_16-46-29.png', '/sales/uploads/service_tickets/e3263d7e808e55aedf5b535768fc8e3a/CMT-329aee80-4640-4c15-841c-c0e03e84f853__29a91a62-a36d-4e0a-8053-5b842e875987.png', 565093, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 09:46:40'),
('4be19271-bbb3-11f0-8604-005056b8f6d0', '887ac5a1a781314baf1c0823330b88fe', '2025-11-07_15-22-53.png', '/sales/uploads/service_tickets/887ac5a1a781314baf1c0823330b88fe/690dacef76ecc_1762503919.png', 158802, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-07 08:25:19'),
('4bec1acc-b852-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', '3Nov_รายงานผลการทดสอบระบบคลังอัจฉริยะv0.3.pdf', '/sales/uploads/service_tickets/eefbd78e5f2426f6a79494712287f81b/CMT-56159c4f-d4e1-4b6f-8b34-d3157e75eb09__dd7f33eb-cd91-40f7-8300-8f83f49dc5dc.pdf', 2036340, 'pdf', 'application/pdf', '6fbca1c7-761f-4027-ba4c-89e04832b717', '2025-11-03 01:14:27'),
('4c168a4d-aa6e-11f0-9a0c-005056b8f6d0', 'a5eb1b3774bb8de6a806482e70a0c608', '2025-10-16_15-52-02.png', '/sales/uploads/service_tickets/a5eb1b3774bb8de6a806482e70a0c608/CMT-7b3859c8-d27c-452d-a6a3-851d61faaeeb__1d0fe626-d360-44cf-9258-8315cf60c6f0.png', 210566, 'png', 'image/png', 'f30e8b87-d047-4bca-9b34-d223170df87c', '2025-10-16 08:58:34'),
('4c172585-aa6e-11f0-9a0c-005056b8f6d0', 'a5eb1b3774bb8de6a806482e70a0c608', '2025-10-16_15-51-20.png', '/sales/uploads/service_tickets/a5eb1b3774bb8de6a806482e70a0c608/CMT-7b3859c8-d27c-452d-a6a3-851d61faaeeb__a56810fa-913d-4642-a5a9-3d2dbe6afe17.png', 179450, 'png', 'image/png', 'f30e8b87-d047-4bca-9b34-d223170df87c', '2025-10-16 08:58:34'),
('4c17bb94-aa6e-11f0-9a0c-005056b8f6d0', 'a5eb1b3774bb8de6a806482e70a0c608', '2025-10-16_15-45-36.png', '/sales/uploads/service_tickets/a5eb1b3774bb8de6a806482e70a0c608/CMT-7b3859c8-d27c-452d-a6a3-851d61faaeeb__973c5c34-e4c9-4719-8346-aeffe781fadd.png', 102634, 'png', 'image/png', 'f30e8b87-d047-4bca-9b34-d223170df87c', '2025-10-16 08:58:34'),
('4e2af3ff-b0af-11f0-9a0c-005056b8f6d0', '5356767837cd88ec0215243511c749ab', '2025-10-24_13-33-01.png', '/sales/uploads/service_tickets/5356767837cd88ec0215243511c749ab/CMT-4c40b499-e210-48f5-b3ea-a6064648d1a9__224460ba-de0e-46b6-95bd-e9ed6348c72c.png', 1097192, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 07:59:26'),
('4ee865e8-a804-11f0-aff6-005056b8f6d0', '54ce0ca8f7f731876d95512f0d28debd', 'Screenshot from 2025-10-13 14-15-22.png', '/sales/uploads/service_tickets/54ce0ca8f7f731876d95512f0d28debd/CMT-a44334e0-837f-4cbd-9378-3eb3f0189878__e7607a36-0325-48d5-907b-163adf5cd49a.png', 420045, 'png', 'image/png', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', '2025-10-13 07:15:41'),
('4f421091-af22-11f0-9a0c-005056b8f6d0', 'dbbf9e3d828c7ae3afcc8877b1570cc1', 'S__25534482_0.jpg', '/sales/uploads/service_tickets/dbbf9e3d828c7ae3afcc8877b1570cc1/CMT-3702ba23-dda6-442d-a4d7-ca7736bc0646__ae1b3512-037f-4ff0-bd47-61744568079e.jpg', 944695, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:37:36'),
('4f4a8f46-af22-11f0-9a0c-005056b8f6d0', 'dbbf9e3d828c7ae3afcc8877b1570cc1', 'S__25534483_0.jpg', '/sales/uploads/service_tickets/dbbf9e3d828c7ae3afcc8877b1570cc1/CMT-3702ba23-dda6-442d-a4d7-ca7736bc0646__97c0d361-5ce7-4ceb-a9f6-e2c20c76b815.jpg', 931843, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:37:36'),
('51d8ce10-b46c-11f0-9a0c-005056b8f6d0', '519fee5632c4c3a2970bde1aeb2a1c75', 'messageImage_1761703501154.jpg', '/sales/uploads/service_tickets/519fee5632c4c3a2970bde1aeb2a1c75/6901777bdc0fb_1761703803.jpg', 133107, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 02:10:03'),
('52192fd1-ae19-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', 'Presentation-Benz-THONBURI-Re3.pdf', '/sales/uploads/service_tickets/6c6595bb55eb3374513891cfce0bec7e/CMT-80dbf98e-c972-4a97-b651-e1235699d433__ca4f567d-583d-4945-8953-9f62bc196b91.pdf', 8941453, 'pdf', 'application/pdf', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 01:00:25'),
('53f65b7b-ad5c-11f0-9a0c-005056b8f6d0', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', '001.jpg', '/sales/uploads/service_tickets/c0a1ccdaa3cc8449b8a8648d9f98ff91/CMT-ae69f256-af76-4e3a-80a6-715dff4fd996__f4c4e530-28f0-479a-a3ef-e4c6fc25f530.jpg', 189934, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:27:32'),
('53fc850b-ad5c-11f0-9a0c-005056b8f6d0', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', '001.png', '/sales/uploads/service_tickets/c0a1ccdaa3cc8449b8a8648d9f98ff91/CMT-ae69f256-af76-4e3a-80a6-715dff4fd996__b9b71e03-c334-4132-9ed9-3f92f641c695.png', 233687, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:27:32'),
('53fd6a8b-ad5c-11f0-9a0c-005056b8f6d0', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', '110.png', '/sales/uploads/service_tickets/c0a1ccdaa3cc8449b8a8648d9f98ff91/CMT-ae69f256-af76-4e3a-80a6-715dff4fd996__ffaa7aeb-4ad5-4062-8ce2-484946bed936.png', 552767, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:27:32'),
('53fe0ab2-ad5c-11f0-9a0c-005056b8f6d0', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', '111.png', '/sales/uploads/service_tickets/c0a1ccdaa3cc8449b8a8648d9f98ff91/CMT-ae69f256-af76-4e3a-80a6-715dff4fd996__99c78f04-260d-4564-bfde-8479d6850e58.png', 97388, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:27:32'),
('53fe95bf-ad5c-11f0-9a0c-005056b8f6d0', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', '112.png', '/sales/uploads/service_tickets/c0a1ccdaa3cc8449b8a8648d9f98ff91/CMT-ae69f256-af76-4e3a-80a6-715dff4fd996__28f75282-41c9-4911-ae0a-5e56fc78de21.png', 62066, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:27:32'),
('53ff2642-ad5c-11f0-9a0c-005056b8f6d0', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', '113.png', '/sales/uploads/service_tickets/c0a1ccdaa3cc8449b8a8648d9f98ff91/CMT-ae69f256-af76-4e3a-80a6-715dff4fd996__e0e183ea-f4c7-4294-af14-8f77de72cf52.png', 127606, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:27:32'),
('54f5c6fe-b55d-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', '28Oct_รายงานผลการทดสอบระบบคลังอัจฉริยะv0.2.pdf', '/sales/uploads/service_tickets/eefbd78e5f2426f6a79494712287f81b/CMT-8adafa94-8b89-48d8-ba62-930ac5e02d4f__e262cda7-8b6f-42f4-a74f-97ca56b852ca.pdf', 1668920, 'pdf', 'application/pdf', '6fbca1c7-761f-4027-ba4c-89e04832b717', '2025-10-30 06:55:34'),
('63eb74eb-ae19-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', 'flow_benz.drawio.png', '/sales/uploads/service_tickets/6c6595bb55eb3374513891cfce0bec7e/CMT-26a70b26-38c0-4c7d-9a24-a1a77bb88203__c08c960f-afea-4c71-b573-b955ea3c65a6.png', 1492760, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 01:00:55'),
('657d9892-af55-11f0-9a0c-005056b8f6d0', 'efda3db7e97b4a86927f55e464fa6562', '2025-10-21_22-54-12.jpg', '/sales/uploads/service_tickets/efda3db7e97b4a86927f55e464fa6562/68f8ed8689959_1761144198.jpg', 207157, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:43:18'),
('750ed984-ba16-11f0-9a0c-005056b8f6d0', 'ec78cca5255db3e17e0371382cb93425', '2025-11-05_14-10-16.png', '/sales/uploads/service_tickets/ec78cca5255db3e17e0371382cb93425/690af88e86da1_1762326670.png', 365939, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-05 07:11:10'),
('79cc371a-b0b2-11f0-9a0c-005056b8f6d0', '19d9016c34677e6c668edde9932256a1', '14-24102568_ระบบจองยานพาหนะ.xlsx', '/sales/uploads/service_tickets/19d9016c34677e6c668edde9932256a1/CMT-083ebaa2-eecf-42dc-83c2-a28a053df1e9__17cfbd9a-dd54-431c-981e-519ada05478c.xlsx', 22308, 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 08:22:08'),
('79f871bb-b0b2-11f0-9a0c-005056b8f6d0', '19d9016c34677e6c668edde9932256a1', '2025-10-24_15-18-36.png', '/sales/uploads/service_tickets/19d9016c34677e6c668edde9932256a1/CMT-083ebaa2-eecf-42dc-83c2-a28a053df1e9__cb6cd913-d2d6-46ce-b344-3602dc54aaca.png', 414129, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 08:22:08'),
('79f8e7e5-b0b2-11f0-9a0c-005056b8f6d0', '19d9016c34677e6c668edde9932256a1', '2025-10-24_15-21-17.png', '/sales/uploads/service_tickets/19d9016c34677e6c668edde9932256a1/CMT-083ebaa2-eecf-42dc-83c2-a28a053df1e9__0c10cec0-b229-49fc-9e21-09ab683de98a.png', 173234, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 08:22:08'),
('7b496ba2-af56-11f0-9a0c-005056b8f6d0', 'b3353d226e75aeda28572314a7abe62f', '2025-10-21_23-02-31.jpg', '/sales/uploads/service_tickets/b3353d226e75aeda28572314a7abe62f/68f8ef58971c6_1761144664.jpg', 189795, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:51:04'),
('7e6b9efa-af22-11f0-9a0c-005056b8f6d0', 'dbbf9e3d828c7ae3afcc8877b1570cc1', '878860_0.jpg', '/sales/uploads/service_tickets/dbbf9e3d828c7ae3afcc8877b1570cc1/CMT-79bf0d5b-4095-4a27-9a5e-5060a04797cd__f05273ab-1ee9-4b72-8982-d6d870004c5c.jpg', 156540, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:38:56'),
('7e6c0d8b-af22-11f0-9a0c-005056b8f6d0', 'dbbf9e3d828c7ae3afcc8877b1570cc1', '878866_0.jpg', '/sales/uploads/service_tickets/dbbf9e3d828c7ae3afcc8877b1570cc1/CMT-79bf0d5b-4095-4a27-9a5e-5060a04797cd__7bbc6c6c-95d8-40f6-bb07-be6a89d3ee2c.jpg', 114912, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:38:56'),
('7e6c9202-af22-11f0-9a0c-005056b8f6d0', 'dbbf9e3d828c7ae3afcc8877b1570cc1', '879316_0.jpg', '/sales/uploads/service_tickets/dbbf9e3d828c7ae3afcc8877b1570cc1/CMT-79bf0d5b-4095-4a27-9a5e-5060a04797cd__f74b8847-21e7-43a3-995f-0cf736e55983.jpg', 246441, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:38:56'),
('7e72f52c-af22-11f0-9a0c-005056b8f6d0', 'dbbf9e3d828c7ae3afcc8877b1570cc1', '879319_0.jpg', '/sales/uploads/service_tickets/dbbf9e3d828c7ae3afcc8877b1570cc1/CMT-79bf0d5b-4095-4a27-9a5e-5060a04797cd__c0e282de-799f-41b9-834b-1134352bd246.jpg', 231473, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:38:56'),
('814eaffe-b8ab-11f0-9a0c-005056b8f6d0', 'b180015710d27a896b70940424c14ee0', '2025-11-03_18-52-44.png', '/sales/uploads/service_tickets/b180015710d27a896b70940424c14ee0/6908979eb0833_1762170782.png', 217456, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 11:53:02'),
('88855f98-a827-11f0-aff6-005056b8f6d0', '375fbb2a1a2a398c6ee3fcbba58316cb', '2025-10-13_13-55-38.jpg', '/sales/uploads/service_tickets/375fbb2a1a2a398c6ee3fcbba58316cb/68ece2362420a_1760354870.jpg', 401374, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 11:27:50'),
('8c7421ad-ae92-11f0-9a0c-005056b8f6d0', 'db915fd3678a3c7a9a85d8aa606f02f5', '2025-10-21_22-27-51.jpg', '/sales/uploads/service_tickets/db915fd3678a3c7a9a85d8aa606f02f5/CMT-eabf6327-bcb7-434e-88ba-7a3f73e44e75__310c804f-89e4-4a6e-9388-6de880649698.jpg', 245919, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 15:28:12'),
('922d1515-b3f1-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', '2025-10-28_18-20-12.png', '/sales/uploads/service_tickets/eefbd78e5f2426f6a79494712287f81b/6900a98bb1bbf_1761651083.png', 22150, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-28 11:31:23'),
('a8bc7212-b4c1-11f0-9a0c-005056b8f6d0', '2d82b2958ff60d48fee678b5e5cdadbb', 'AI Smart Watch.pdf', '/sales/uploads/service_tickets/2d82b2958ff60d48fee678b5e5cdadbb/690206a8d9674_1761740456.pdf', 1569510, 'pdf', 'application/pdf', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 12:20:56'),
('ab96e7c3-b640-11f0-9a0c-005056b8f6d0', '9439b4fe59f3705e5dd798f99ea78bf7', 'มาบตาพุด-ตุลา.xlsx', '/sales/uploads/service_tickets/9439b4fe59f3705e5dd798f99ea78bf7/CMT-db13c638-b492-4514-aaec-7f73bacfaefd__50358ccf-a8b0-4faf-b18a-0e048a88d85d.xlsx', 28508, 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:02:55'),
('aba467e1-b640-11f0-9a0c-005056b8f6d0', '9439b4fe59f3705e5dd798f99ea78bf7', 'มาลีตาพุด1.jpg', '/sales/uploads/service_tickets/9439b4fe59f3705e5dd798f99ea78bf7/CMT-db13c638-b492-4514-aaec-7f73bacfaefd__32a82ad9-2a45-472a-a606-f316a30a4996.jpg', 107712, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:02:55'),
('abe70e59-b640-11f0-9a0c-005056b8f6d0', '9439b4fe59f3705e5dd798f99ea78bf7', 'มาลีตาพุด2.jpg', '/sales/uploads/service_tickets/9439b4fe59f3705e5dd798f99ea78bf7/CMT-db13c638-b492-4514-aaec-7f73bacfaefd__01be03e0-5a3e-425f-8094-8b399c108161.jpg', 110472, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:02:56'),
('abe78683-b640-11f0-9a0c-005056b8f6d0', '9439b4fe59f3705e5dd798f99ea78bf7', 'มาลีตาพุด3.jpg', '/sales/uploads/service_tickets/9439b4fe59f3705e5dd798f99ea78bf7/CMT-db13c638-b492-4514-aaec-7f73bacfaefd__4d29f393-d09b-440e-aedd-36cd5396f21a.jpg', 102843, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:02:56'),
('abe7fb8f-b640-11f0-9a0c-005056b8f6d0', '9439b4fe59f3705e5dd798f99ea78bf7', 'มาลีตาพุด4.jpg', '/sales/uploads/service_tickets/9439b4fe59f3705e5dd798f99ea78bf7/CMT-db13c638-b492-4514-aaec-7f73bacfaefd__89d244fe-da74-47cb-88d5-3f8edde6bb0d.jpg', 95452, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:02:56'),
('abe8610e-b640-11f0-9a0c-005056b8f6d0', '9439b4fe59f3705e5dd798f99ea78bf7', 'มาลีตาพุด5.jpg', '/sales/uploads/service_tickets/9439b4fe59f3705e5dd798f99ea78bf7/CMT-db13c638-b492-4514-aaec-7f73bacfaefd__bf64aa06-cc3a-4f06-85ea-fa1c01a6e3eb.jpg', 108982, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:02:56'),
('abe8d04a-b640-11f0-9a0c-005056b8f6d0', '9439b4fe59f3705e5dd798f99ea78bf7', 'มาลีตาพุด6.jpg', '/sales/uploads/service_tickets/9439b4fe59f3705e5dd798f99ea78bf7/CMT-db13c638-b492-4514-aaec-7f73bacfaefd__1713d993-8c9a-4cc3-a241-39de0455c873.jpg', 92156, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:02:56'),
('abe9c9de-b640-11f0-9a0c-005056b8f6d0', '9439b4fe59f3705e5dd798f99ea78bf7', 'รายงานมาบตาพุด ตุลาคม68.docx', '/sales/uploads/service_tickets/9439b4fe59f3705e5dd798f99ea78bf7/CMT-db13c638-b492-4514-aaec-7f73bacfaefd__c409074b-0d07-4c16-b616-4d89e9ec3358.docx', 7498698, 'docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:02:56'),
('ac5a0a78-b640-11f0-9a0c-005056b8f6d0', '9439b4fe59f3705e5dd798f99ea78bf7', 'รายงานมาบตาพุด ตุลาคม68.pdf', '/sales/uploads/service_tickets/9439b4fe59f3705e5dd798f99ea78bf7/CMT-db13c638-b492-4514-aaec-7f73bacfaefd__b7386a10-c809-4e36-8884-b3fdbce0c755.pdf', 651107, 'pdf', 'application/pdf', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:02:57'),
('aeb63609-a801-11f0-aff6-005056b8f6d0', '54ce0ca8f7f731876d95512f0d28debd', '2025-10-13_13-55-38.jpg', '/sales/uploads/service_tickets/54ce0ca8f7f731876d95512f0d28debd/68eca2b555b67_1760338613.jpg', 401374, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 06:56:53'),
('aec28b71-a801-11f0-aff6-005056b8f6d0', '54ce0ca8f7f731876d95512f0d28debd', '2025-10-13_13-55-52.jpg', '/sales/uploads/service_tickets/54ce0ca8f7f731876d95512f0d28debd/68eca2b569d06_1760338613.jpg', 206507, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 06:56:53'),
('b098091d-ae61-11f0-9a0c-005056b8f6d0', '5356767837cd88ec0215243511c749ab', '2025-10-21_16-37-47.png', '/sales/uploads/service_tickets/5356767837cd88ec0215243511c749ab/68f7549418204_1761039508.png', 558882, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:38:28'),
('b0df4d9f-b94e-11f0-9a0c-005056b8f6d0', '13bfaa55fc6a83ce652f638acc7259b7', 'messageImage_1762161049544.jpg', '/sales/uploads/service_tickets/13bfaa55fc6a83ce652f638acc7259b7/6909a967884ee_1762240871.jpg', 134873, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 07:21:11'),
('b0e2fd4b-b94e-11f0-9a0c-005056b8f6d0', '13bfaa55fc6a83ce652f638acc7259b7', 'messageImage_1762238128583.jpg', '/sales/uploads/service_tickets/13bfaa55fc6a83ce652f638acc7259b7/6909a9678e7c8_1762240871.jpg', 29482, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 07:21:11'),
('b0ec8dfa-b94e-11f0-9a0c-005056b8f6d0', '13bfaa55fc6a83ce652f638acc7259b7', 'messageImage_1762240620584.jpg', '/sales/uploads/service_tickets/13bfaa55fc6a83ce652f638acc7259b7/6909a9678f455_1762240871.jpg', 40639, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 07:21:11'),
('b13bcdd6-b4ad-11f0-9a0c-005056b8f6d0', 'ec68368ab063a1cf203f014f7975d6b1', '2025-10-29_16-57-39.png', '/sales/uploads/service_tickets/ec68368ab063a1cf203f014f7975d6b1/6901e52935b1e_1761731881.png', 379969, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 09:58:01'),
('b166e372-ab32-11f0-9a0c-005056b8f6d0', '82396eb89e2502198fa334db5707c050', 'messageImage_1760673093278.jpg', '/sales/uploads/service_tickets/82396eb89e2502198fa334db5707c050/68f1fd3b31e45_1760689467.jpg', 63518, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-17 08:24:27'),
('b173d5eb-ab32-11f0-9a0c-005056b8f6d0', '82396eb89e2502198fa334db5707c050', 'messageImage_1760673211161.jpg', '/sales/uploads/service_tickets/82396eb89e2502198fa334db5707c050/68f1fd3b46b4e_1760689467.jpg', 66792, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-17 08:24:27'),
('b179fc9d-ab32-11f0-9a0c-005056b8f6d0', '82396eb89e2502198fa334db5707c050', 'messageImage_1760688852197.jpg', '/sales/uploads/service_tickets/82396eb89e2502198fa334db5707c050/68f1fd3b478e1_1760689467.jpg', 380335, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-17 08:24:27'),
('b17a5e53-ab32-11f0-9a0c-005056b8f6d0', '82396eb89e2502198fa334db5707c050', 'messageImage_1760689325978.jpg', '/sales/uploads/service_tickets/82396eb89e2502198fa334db5707c050/68f1fd3b516e1_1760689467.jpg', 76037, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-17 08:24:27'),
('c2789f14-ae62-11f0-9a0c-005056b8f6d0', '350a46a5acbd6776dc3b040297ef322d', '2025-10-21_16-45-57.png', '/sales/uploads/service_tickets/350a46a5acbd6776dc3b040297ef322d/68f7565f905c9_1761039967.png', 84986, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:46:07'),
('c27dc006-ae62-11f0-9a0c-005056b8f6d0', '350a46a5acbd6776dc3b040297ef322d', 'messageImage_1761037329262.jpg', '/sales/uploads/service_tickets/350a46a5acbd6776dc3b040297ef322d/68f7565f949ee_1761039967.jpg', 185709, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:46:07'),
('c3fa8991-a8fc-11f0-aff6-005056b8f6d0', '2bd0058981314c1250b10a592f9af020', 'Screenshot 2025-10-14 at 9.45.52 AM.png', '/sales/uploads/service_tickets/2bd0058981314c1250b10a592f9af020/68ee47f5ed87f_1760446453.png', 531631, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 12:54:13'),
('c948bdf3-a8ff-11f0-aff6-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', '2025-10-14_20-14-47.png', '/sales/uploads/service_tickets/e0639361c653a406d17686669cae0a1f/CMT-83baf920-d4dc-4c28-93ce-7d77afac7817__b943f0e2-a394-4195-bdc3-0fcacd1c792a.png', 39887, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 13:15:51'),
('c951b36e-a8ff-11f0-aff6-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', '2025-10-14_20-14-59.png', '/sales/uploads/service_tickets/e0639361c653a406d17686669cae0a1f/CMT-83baf920-d4dc-4c28-93ce-7d77afac7817__5daab8e1-441a-497f-ab60-dafe71b52992.png', 68799, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 13:15:51'),
('ce5121ba-b94e-11f0-9a0c-005056b8f6d0', '13bfaa55fc6a83ce652f638acc7259b7', '2025-11-04_14-13-23.png', '/sales/uploads/service_tickets/13bfaa55fc6a83ce652f638acc7259b7/CMT-5bb18306-812a-457f-bb0d-6483458f189a__131855cc-77c9-41e6-b5b3-5cae34f7f53a.png', 379673, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 07:22:00'),
('d1534110-b55d-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', '28Oct_รายงานผลการทดสอบระบบคลังอัจฉริยะv0.2.pdf', '/sales/uploads/service_tickets/eefbd78e5f2426f6a79494712287f81b/CMT-40551420-245b-46cb-9901-e1d09505faf6__ea006844-c32c-42ed-91ff-ce9467143f5b.pdf', 1668404, 'pdf', 'application/pdf', '6fbca1c7-761f-4027-ba4c-89e04832b717', '2025-10-30 06:59:03'),
('d182f71b-a827-11f0-aff6-005056b8f6d0', '375fbb2a1a2a398c6ee3fcbba58316cb', '2025-10-13_18-29-34.jpg', '/sales/uploads/service_tickets/375fbb2a1a2a398c6ee3fcbba58316cb/CMT-ab76b048-e93d-40ae-a568-2203b91bcd26__4e23041d-5047-4862-8c34-cb8cb9adf716.jpg', 168592, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 11:29:52'),
('d1892b69-a98f-11f0-aff6-005056b8f6d0', '3641ec530675c2226fc1420f5d02d71e', '2025-10-15_13-25-02.jpg', '/sales/uploads/service_tickets/3641ec530675c2226fc1420f5d02d71e/68ef3eacb1eca_1760509612.jpg', 345817, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 06:26:52'),
('d75c6f35-aa37-11f0-9a0c-005056b8f6d0', 'bac7e3042320f2089eecefceee459b07', 'messageImage_1760581297676.jpg', '/sales/uploads/service_tickets/bac7e3042320f2089eecefceee459b07/CMT-1e986bbd-4da1-447f-9b4d-eabb345bcbec__c7e034e1-c356-4cc2-b167-ccc4677ea239.jpg', 69533, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 02:28:46'),
('de525650-b640-11f0-9a0c-005056b8f6d0', '8b3f3eb00f76e9a698bcc04e0243a566', 'บ่อวิน-ตุลา.xlsx', '/sales/uploads/service_tickets/8b3f3eb00f76e9a698bcc04e0243a566/CMT-c6062a9f-d01a-4c4a-b248-5bb8542615fd__f188798b-ae20-4529-a8bc-b945937d7343.xlsx', 28647, 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:04:20'),
('de62fab7-b640-11f0-9a0c-005056b8f6d0', '8b3f3eb00f76e9a698bcc04e0243a566', 'มาลีบ่อวิน1.jpg', '/sales/uploads/service_tickets/8b3f3eb00f76e9a698bcc04e0243a566/CMT-c6062a9f-d01a-4c4a-b248-5bb8542615fd__fa9c2638-6b41-4d07-9f13-7a4e488c6c8f.jpg', 106861, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:04:21'),
('de6c36ba-b640-11f0-9a0c-005056b8f6d0', '8b3f3eb00f76e9a698bcc04e0243a566', 'มาลีบ่อวิน2.jpg', '/sales/uploads/service_tickets/8b3f3eb00f76e9a698bcc04e0243a566/CMT-c6062a9f-d01a-4c4a-b248-5bb8542615fd__290b250f-c359-41b9-b492-f964e47183ff.jpg', 109867, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:04:21'),
('de7d1e35-b640-11f0-9a0c-005056b8f6d0', '8b3f3eb00f76e9a698bcc04e0243a566', 'มาลีบ่อวิน3.jpg', '/sales/uploads/service_tickets/8b3f3eb00f76e9a698bcc04e0243a566/CMT-c6062a9f-d01a-4c4a-b248-5bb8542615fd__ad2049a9-7e3f-4cd7-8c66-abdb0e949a15.jpg', 103895, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:04:21'),
('de8c32ab-b640-11f0-9a0c-005056b8f6d0', '8b3f3eb00f76e9a698bcc04e0243a566', 'มาลีบ่อวิน4.jpg', '/sales/uploads/service_tickets/8b3f3eb00f76e9a698bcc04e0243a566/CMT-c6062a9f-d01a-4c4a-b248-5bb8542615fd__4083062e-f02f-4122-9256-51a51e5bf6e2.jpg', 99404, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:04:21'),
('de98518d-b640-11f0-9a0c-005056b8f6d0', '8b3f3eb00f76e9a698bcc04e0243a566', 'มาลีบ่อวิน5.jpg', '/sales/uploads/service_tickets/8b3f3eb00f76e9a698bcc04e0243a566/CMT-c6062a9f-d01a-4c4a-b248-5bb8542615fd__29be4233-1a93-4f7e-9012-dfafa2b4b3ff.jpg', 109466, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:04:21'),
('deba9166-b640-11f0-9a0c-005056b8f6d0', '8b3f3eb00f76e9a698bcc04e0243a566', 'มาลีบ่อวิน6.jpg', '/sales/uploads/service_tickets/8b3f3eb00f76e9a698bcc04e0243a566/CMT-c6062a9f-d01a-4c4a-b248-5bb8542615fd__32ff8417-b4a6-4624-8340-d97fc454ecc7.jpg', 93163, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:04:21'),
('dee4a81f-b640-11f0-9a0c-005056b8f6d0', '8b3f3eb00f76e9a698bcc04e0243a566', 'รายงานบ่อวินตุลาคม68.docx', '/sales/uploads/service_tickets/8b3f3eb00f76e9a698bcc04e0243a566/CMT-c6062a9f-d01a-4c4a-b248-5bb8542615fd__2a66b74e-dd96-4f3d-94f4-f845f54ca001.docx', 7471598, 'docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:04:21'),
('dee5a030-b640-11f0-9a0c-005056b8f6d0', '8b3f3eb00f76e9a698bcc04e0243a566', 'รายงานบ่อวินตุลาคม68.pdf', '/sales/uploads/service_tickets/8b3f3eb00f76e9a698bcc04e0243a566/CMT-c6062a9f-d01a-4c4a-b248-5bb8542615fd__217e2e86-2bef-46f9-b4ca-c9f8922f2750.pdf', 773092, 'pdf', 'application/pdf', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:04:21'),
('e2aeae62-b483-11f0-9a0c-005056b8f6d0', 'bc420db35f715fdc06e983365015152d', '1C66DD21-FAF5-4B89-A502-C4B1B92B3E2E.jpeg', '/sales/uploads/service_tickets/bc420db35f715fdc06e983365015152d/69019f0549bc5_1761713925.jpeg', 4096386, 'jpeg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 04:58:45'),
('e49ec7c0-b52c-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', '28Oct_รายงานผลการทดสอบระบบคลังอัจฉริยะ.pdf', '/sales/uploads/service_tickets/eefbd78e5f2426f6a79494712287f81b/CMT-584b3d8d-fbf3-43ab-a242-bdaeb09f0220__ab65e525-1ec8-49f5-a6ab-b7b735c6fec9.pdf', 923438, 'pdf', 'application/pdf', '6fbca1c7-761f-4027-ba4c-89e04832b717', '2025-10-30 01:08:50'),
('eb268b0b-a8f2-11f0-aff6-005056b8f6d0', '7e24f638b40854acf4a162a469026e9e', '001.png', '/sales/uploads/service_tickets/7e24f638b40854acf4a162a469026e9e/CMT-20c35593-e240-4ab4-92d1-18948ffb9f22__1969c859-bed1-418f-aa5d-116335110c0e.png', 150676, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 11:43:44'),
('ed7dd801-b8ae-11f0-9a0c-005056b8f6d0', 'fe2a02ab8e28f3c0f8d2e4f214f8ab23', '2025-11-03_13-23-32.png', '/sales/uploads/service_tickets/fe2a02ab8e28f3c0f8d2e4f214f8ab23/CMT-08ffe410-1719-4271-b26b-0eed312b03f0__91d4524c-3103-430d-bb73-7064884d7523.png', 146374, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:17:32'),
('ed845e9a-b8ae-11f0-9a0c-005056b8f6d0', 'fe2a02ab8e28f3c0f8d2e4f214f8ab23', '2025-11-03_13-24-28.png', '/sales/uploads/service_tickets/fe2a02ab8e28f3c0f8d2e4f214f8ab23/CMT-08ffe410-1719-4271-b26b-0eed312b03f0__a439a03c-6286-4a18-b281-462b3dc3c151.png', 237195, 'png', 'image/png', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:17:32'),
('f0298e90-b925-11f0-9a0c-005056b8f6d0', '611bfaa99199810cb73ce1721f6ea0a0', 'messageImage_1762161049544.jpg', '/sales/uploads/service_tickets/611bfaa99199810cb73ce1721f6ea0a0/6909650857fd7_1762223368.jpg', 51205, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 02:29:28'),
('f9a0b5fb-b5ff-11f0-9a0c-005056b8f6d0', '2d72ce32ffe570003a84eea4bbc5153e', 'ทับมา-ตุลา.xlsx', '/sales/uploads/service_tickets/2d72ce32ffe570003a84eea4bbc5153e/CMT-b40a5a9c-a48a-4792-ba52-c3f8169b2bc6__77014ea4-04e6-4872-9790-6c71db147946.xlsx', 34457, 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 02:19:49'),
('f9b08f23-b5ff-11f0-9a0c-005056b8f6d0', '2d72ce32ffe570003a84eea4bbc5153e', 'มาลีทับมา1.jpg', '/sales/uploads/service_tickets/2d72ce32ffe570003a84eea4bbc5153e/CMT-b40a5a9c-a48a-4792-ba52-c3f8169b2bc6__506005e5-8b53-43b5-a632-38fb4dce87b6.jpg', 109684, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 02:19:49'),
('f9c244e3-b5ff-11f0-9a0c-005056b8f6d0', '2d72ce32ffe570003a84eea4bbc5153e', 'มาลีทับมา2.jpg', '/sales/uploads/service_tickets/2d72ce32ffe570003a84eea4bbc5153e/CMT-b40a5a9c-a48a-4792-ba52-c3f8169b2bc6__80fce5ac-acfd-432d-8eab-beb630eab377.jpg', 110306, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 02:19:49'),
('f9cbb6d3-b5ff-11f0-9a0c-005056b8f6d0', '2d72ce32ffe570003a84eea4bbc5153e', 'มาลีทับมา3.jpg', '/sales/uploads/service_tickets/2d72ce32ffe570003a84eea4bbc5153e/CMT-b40a5a9c-a48a-4792-ba52-c3f8169b2bc6__d90ea69a-025d-43a0-b048-b0fa711703ef.jpg', 109124, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 02:19:49'),
('f9d520c4-b5ff-11f0-9a0c-005056b8f6d0', '2d72ce32ffe570003a84eea4bbc5153e', 'มาลีทับมา4.jpg', '/sales/uploads/service_tickets/2d72ce32ffe570003a84eea4bbc5153e/CMT-b40a5a9c-a48a-4792-ba52-c3f8169b2bc6__cb2aed5f-f0ed-4482-9c9d-74182b1f8e09.jpg', 95215, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 02:19:49'),
('f9dfda9c-b5ff-11f0-9a0c-005056b8f6d0', '2d72ce32ffe570003a84eea4bbc5153e', 'มาลีทับมา5.jpg', '/sales/uploads/service_tickets/2d72ce32ffe570003a84eea4bbc5153e/CMT-b40a5a9c-a48a-4792-ba52-c3f8169b2bc6__f4b6ab56-e5e7-4642-94e7-5b583a2cd4f5.jpg', 118813, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 02:19:49'),
('fa366293-b5ff-11f0-9a0c-005056b8f6d0', '2d72ce32ffe570003a84eea4bbc5153e', 'มาลีทับมา6.jpg', '/sales/uploads/service_tickets/2d72ce32ffe570003a84eea4bbc5153e/CMT-b40a5a9c-a48a-4792-ba52-c3f8169b2bc6__b6aa9c36-4059-4781-a879-20dba9f67afe.jpg', 93833, 'jpg', 'image/jpeg', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 02:19:50'),
('fa72ee45-b5ff-11f0-9a0c-005056b8f6d0', '2d72ce32ffe570003a84eea4bbc5153e', 'รายงานทับมา ตุลา 68.docx', '/sales/uploads/service_tickets/2d72ce32ffe570003a84eea4bbc5153e/CMT-b40a5a9c-a48a-4792-ba52-c3f8169b2bc6__8a8e246e-e7e0-4b35-84d5-0c0ec6cc3ab8.docx', 4273630, 'docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 02:19:50'),
('fab9f5fc-b5ff-11f0-9a0c-005056b8f6d0', '2d72ce32ffe570003a84eea4bbc5153e', 'รายงานทับมา ตุลา 68.pdf', '/sales/uploads/service_tickets/2d72ce32ffe570003a84eea4bbc5153e/CMT-b40a5a9c-a48a-4792-ba52-c3f8169b2bc6__ecd6156e-3760-447f-a39a-cb54d4cb49f8.pdf', 669884, 'pdf', 'application/pdf', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 02:19:51'),
('fbbe0596-a801-11f0-aff6-005056b8f6d0', '54ce0ca8f7f731876d95512f0d28debd', '2025-10-13_13-58-34.jpg', '/sales/uploads/service_tickets/54ce0ca8f7f731876d95512f0d28debd/CMT-473f7819-79d4-4ca1-914f-8294af1e19ba__85b16ed6-d2fa-4db3-ada0-891fe80843a3.jpg', 232285, 'jpg', 'image/jpeg', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 06:59:02');

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
-- Dumping data for table `service_ticket_comments`
--

INSERT INTO `service_ticket_comments` (`comment_id`, `ticket_id`, `comment`, `is_internal`, `created_by`, `created_at`, `updated_by`, `updated_at`, `deleted_at`) VALUES
('000b36b8-4628-4c72-acb6-d6de7acda7f3', '47255f8f395045b169b65b16f91ebbe9', 'ปิดงาน', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:50:55', NULL, NULL, NULL),
('065d9418-a379-4239-8279-0c2c3cb711a4', '5356767837cd88ec0215243511c749ab', '2.1. ตรวจสอบเงื่อนไขการแสดงสถานะไฟใหม้ การตอบให้ตอบ : อาทิเช่น ได้รับ API : เส้น ...... ส่ง Parameter : alarm : false จึงดึงสถานะมาแสดง ? หากคำตอบว่าถูกต้องหรือไม่ ?\r\n- ใช่ครับ api // {{urlapi}}/kcontrol\r\n\r\n2.2. ตรวจสอบเงื่อนไขการแสดงสถานะออนไลน์/ออฟไลน์ เงื่อนไขการดึงมาแสดงหน้าบ้านอย่างไร ?\r\nดึงมาจาก สถานะ เป็น true /false true ให้เป็นไฟ \r\n\r\n2.3. เมื่อกดเหตุไหม้ แสดงเป็นสัญลักษณ์ ไฟใหม้ กด Icon สัญลักษณ์ แสดงข้อมูลของอุปกรณ์ และปุ่ม ✓ Acknowledge เมื่อกด ✓ Acknowledge ระบบจะ Call API เส้นไหน และผลลัพธ์เป็นอย่างไร สามารถกดแล้วให้เจ้าหน้าที่เขียนคอมเม้นท์ได้หรือไม่ ?\r\napi /{{urlapi}}/kcontrol/{{deviceId}}/ack กดแล้วจะเปลี่ยนสถานะ ตามที่ api พี่ขวัญ ตั้งไว้\r\n\r\n2.4. การแสดงอุปกรณ์ที่หน้า Dashboard จะต้องแสดงอุปกรณ์ที่มีการอนุมัติ จากหน้า Device แล้วเท่านั้นหรือไม่ ?\r\nใช่ครับ //แนบรูป 2 รูป\r\n\r\n2.5. รายการอุปกรณ์ มุมด้านขวามือ แสดงรายการอุปกรณ์ทั้งหมด และสัญลักษณ์สีสถานะอุปกรณ์ ประกอบด้วย ออฟไลน์ : เทา /ออนไลน์ : เขียว /ไฟใหม้ : แดง ?\r\n//แสดงทั้งหมดทุกสีแล้วครับ \r\n\r\n2.6. เมื่ออุปกรณ์ออฟไลน์ ที่หน้าแผนที่ สัญลักษณ์แสดงเป็นสีเทา เมื่อคลิกที่ ICON แสดงข้อมูล ควรมีปุ่มแสดง ✓ Acknowledge เมื่อกด ✓ Acknowledge ระบบจะ Call API เส้นไหน และผลลัพธ์เป็นอย่างไร สามารถกดแล้วให้เจ้าหน้าที่เขียนคอมเม้นท์ได้หรือไม่ ?\r\n//รอคุยกับพี่ขวัญว่าจะเป็นแบบไหนครับ', 0, '3768e84a-18c0-49d9-94dd-09b44c7c9a7d', '2025-10-24 08:34:16', NULL, NULL, NULL),
('0749df09-26fa-4c03-ba89-612d4cdee240', '5eb8ec3a227ec1dab8b5463cb9d26f12', 'สาเหตุ : เครื่องใช้งานทรัพยากรปริมาณการส่งข้อมูลจำนวนมาก และต่อเนื่องทำให้เครื่องค้าง และ Responsding ไม่ตอบสนอง\r\nวิธีแก้ไข : เบื้องต้นดำเนินการ Restrat Service และเครื่อง เพื่อให้เครื่อง Refraesh การทำงานใหม่ และเพิ่มความเร็วโดยการเปลี่ยน SIM ที่มีความเร็วสูงขึ้น จากเดิม นำเครื่อง Server สำรองไปติดตั้งหน้างานเพื่อแยกการทำงานระหว่าง AI และการส่งข้อมูลขึ้น Server เพื่อให้การทำงานไม่กระตุ้ก', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-23 13:03:55', NULL, NULL, NULL),
('083ebaa2-eecf-42dc-83c2-a28a053df1e9', '19d9016c34677e6c668edde9932256a1', 'ดำเนินการเรียบร้อยแล้วครับ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 08:22:07', NULL, NULL, NULL),
('08ffe410-1719-4271-b26b-0eed312b03f0', 'fe2a02ab8e28f3c0f8d2e4f214f8ab23', 'ดำเนินการตรวจสอบพบว่า เจ้าหน้าที่หน้างานมีการรับเคส แต่กดไม่เกิดเหตุการ ทั้ง 2 เคสทำให้รายงานไม่แสดงตัวเลขจำนวนเหตุการดังภาพ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:17:32', NULL, NULL, NULL),
('0ad5694d-1888-40c7-a05a-9b469c06f2cd', 'aa95c56d33fca80adb2f4305b9d22e7d', 'ดำเนินการ Update งานทั้งหมด เรียบร้อยแล้ว', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 11:38:20', NULL, NULL, NULL),
('0b16c363-8e8e-4e53-bb65-92dd1238aaa2', 'e568fe0d3564ce271bbb99a9d0f1f6aa', 'ปรับแก้ให้แล้ว', 0, 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-10-15 06:53:03', NULL, NULL, NULL),
('0b58359a-b0fd-473d-9b7b-ab006c4a2ecf', '4526af8066b18a46eb44341c5b187ad8', 'ประชุมเรียบร้อยแล้ว', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:59:54', NULL, NULL, NULL),
('0bf1ac2c-5fd0-41e3-bac0-65fdc4b9a4f7', '4d0f17400296e43ac2a70f6764cce016', 'สรุปผลการทดสอบ \r\nSIM 1 : 0672112548 ใส่อุปกรณ์ AI Tracker \r\n1. กดขอความช่วยเหลือ หรือตรวจจับการล้ม  >> ส่งสัญญาณแจ้งเตือนไปยังอุปกรณ์ และโทรออกไปยังเบอร์ที่ต้องค่าไว้  ✔️\r\n2. สามารถเข้ามาจากมือถือ SIM ต่างเครือข่าย หรือ SIM ที่ให้มา Packgate เดียวกันได้ >>รับสายสนทนาได้ปกติ ✔️\r\n3. ส่ง SMS จาก SIM ที่ให้มา Packgate เดียวกันได้ ✔️\r\n\r\n------------------------------------------------------------------------------------------------------------------------\r\nSIM 1 : 0672105736 ใส่มือถือเคลื่อนที่ \r\n1. สามารถรับสายจากอุปกรณ์ AI Tracker (SIM 1 : 0672112548) ได้ ✔️ \r\n2. สามารถโทรเข้าหาอุปกรณ์ AI Tracker (SIM 1 : 0672112548) ได้ ✔️ \r\n3. สามารถโทรออกไปยังเบอร์ภายในเครือข่ายเดียวกัน และต่างเครือข่ายได้ ✔️ \r\n4. สามารถรับสายหรือให้เครื่องอื่นทั้งภายในเครือข่ายเดียวกัน และต่างเครือข่ายได้ ✔️\r\n\r\n------------------------------------------------------------------------------------------------------------------------\r\nไม่สามารถ ส่ง SMS จาก SIM ต่างเครือข่าย ไปยัง SIM ที่ให้มาได้ ❌', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-05 07:00:38', NULL, NULL, NULL),
('0ccf2cf2-d65a-4411-8428-b4c93a6fa8d7', '0475cf8c871b6d870a03ca8beeba95e4', 'อุปกรณ์ 2 (Change)\r\n- Name : อภิรักษ์ บางพุก (Demo)\r\n- Device Name : ทดสอบด่าสำโรงGPS Tracke-32\r\n- Device : 863922038157161', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 01:51:19', NULL, NULL, NULL),
('10af880c-17a5-4c55-9212-a813cea120a9', '1646d2451f8f11f24cd214759fd6535b', 'ปิคเคส', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 04:35:43', NULL, NULL, NULL),
('13633634-8b64-4269-baaf-567d95ef7c6a', 'a005d2bc5a0c79101ef49cdfd84e88bf', 'ดำเนินการเรียบร้อย', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 10:16:04', NULL, NULL, NULL),
('15a72f45-8a31-42c2-921e-afecacad3b98', 'bc420db35f715fdc06e983365015152d', 'สรุปการประชุม \r\nพี่พิศาล : \r\n1. ทางพี่แจ็ค : ให้ดำเนินการออกแผนการติดตั้ง และบริหารโครงการ Map กับส่วนของ Timeline Plan ต่างๆของลูกค้า และทีมพัฒนาระบบให้สอดรับกัน \r\n2. บริการหลังการติดตั้ง : ให้ทางพี่มารี ดำเนินการ Monitor ระบบ \r\nคุณโอ๋ : \r\n1. มีการเซ็นต์สัญญาแล้วในวันที่ 25/09/2025 \r\n2. เดินสายไปที่ตู้แล้ว 10 ร.ร. (รอทางเราเข้าไปติดอุปกรณ์)\r\n3. สั่ง Server ของลูกค้าได้ของ ประมาณ 45 วัน สั่ง/ติดตั้ง \r\n4. กำหนดส่งมอบงานวันที่ 17/03/2026 และจะต้องทดสอบระบบทั้งหมด \r\n\r\nคุณแจ็ค :\r\n1. แสดงแผนการติดตั้ง : แบ่งออกเป็น 7 งวด (งวดละ 7 ร.ร.) \r\n2. แจ้งปัญหาทีมทำงานล่าช้า \r\n3. ไม่สามารถขอเข้าไปติดตั้งทดสอบได้ เนื่องจากมีการเข้าหน้างานทดสอบนัดลูกค้า จำนวน 4 รอบ ในการ Demo ซึ่งตอนนี้ต้องอยู่ในช่วงออน Prod และยืนยันการตัดสินใจเลือกใช้งานระบบและอุปกรณ์ \r\n4. พี่แจ็คสรุปความต้องการ ใช้งานแพลตฟอร์มระบบ ส่งกลับมายังทีมพัฒนาเพื่อปรับปรุงหรือพัฒนาให้สอดรับความต้องการของลูกค้า \r\n\r\nคุณซีน :\r\n1. นำเข้าอุปกรณ์ 18 + 5 วัน พร้อมจัดส่งมาที่ไทย \r\n2. ดำเนินการส่งให้ Suplyerer ประกอบ Board เชื่อมต่อสาย จำนวน 150 ชิ้น รวมอุปกรณ์สำรอง ระยะเวลาดำเนินการ 45 วัน\r\n3. จำนวนอุปกรณ์สำรอง 30 ชุด\r\nสรุประยะเวลาในการ Build อุปกรณ์ 2 เดือน  \r\n\r\n- ขอให้ทางพี่ขวัญ : ปรับปรุงหน้าบ้าน Frontend ให้สามารถใช้างนได้ เพื่อนำไป Demo ให้ลูกค้าในวันพุร้งนี้  \r\n- Monitor ระหว่างทดสอบ และทดสอบระบบทั้งหมดส่วนของาน Dev', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 05:56:02', NULL, NULL, NULL),
('1634b1ef-a025-47c1-a7ef-c747d2e6bfec', '6c6595bb55eb3374513891cfce0bec7e', 'ภาพงานจัดตั้งบูธ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 00:59:04', NULL, NULL, NULL),
('17ec65af-6e95-4fe9-a2ac-7f35f5717e0e', '6a5fda3fdaa7b4addd1f1c1f68ce7d12', 'ไฟล์แนบ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-25 16:08:51', NULL, '2025-10-25 16:08:58', '2025-10-25 23:08:58'),
('18ab2541-5219-46ee-a683-ec9edd324c0c', 'efda3db7e97b4a86927f55e464fa6562', 'ดำเนินการออกแบบเรียบร้อยแล้วครับ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:45:14', NULL, NULL, NULL),
('1957affe-ec21-4000-bce3-4dcd0c7520a4', 'db915fd3678a3c7a9a85d8aa606f02f5', 'ไม่สามารถเปิดเล่นวีดีโอผ่าน Browser ได้จำเป็นจะต้องติดตั้ง Application Hik-Connect และเพิ่มอุปกรณ์ผ่าน QR Code ที่พี่แจ็คดำเนินการ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:42:37', NULL, NULL, NULL),
('1cfc9a92-9fd4-461d-b27f-d27e1f2254fa', 'e6682bbd1a2807550f0b7b0235abd8c8', 'ดำเนินการประชุมเรียบร้อยแล้ว- Jan - 31 Dec 2026\r\n- Intergrated Loboratory Service\r\n- PM/PA ทำ EMR + Nation Health ID\r\n- BI (NEW)\r\nProduct\r\n- Cloud Service\r\n- Third Tier Support\r\n- GeneXpert Implamtation ครับ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 09:41:56', NULL, NULL, NULL),
('1e986bbd-4da1-447f-9b4d-eabb345bcbec', 'bac7e3042320f2089eecefceee459b07', 'ดำเนินการจัดหาเรียบร้อยแล้วตามไฟล์แนบ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 02:28:45', NULL, NULL, NULL),
('20c35593-e240-4ab4-92d1-18948ffb9f22', '7e24f638b40854acf4a162a469026e9e', 'ภาพประกอบ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 11:43:44', NULL, NULL, NULL),
('21ee55b5-ee11-4abf-8d36-1f9a7e511165', 'db915fd3678a3c7a9a85d8aa606f02f5', 'ประสานงานพี่ขวัญ พี่บอส นาย งานโครงการตั้งบูธของธนบุรี มีการติดตั้งกล้อง CCTV + EDEG AI ตั้งที่หน้างาน ซึ่งฝั่งหน้างานจะมี API จะส่ง Data  จากบูธไปหาเครื่อง Server Point ซึ่งตอนนี้ติดปัญหาในเรื่องของ API จากข้างนอกไม่สามารถส่ง Data เข้ามาที่ API เครื่อง Point ได้ ซึ่งเครื่อง Point ถูก Set วง Network ไว้ 2 ลงคือ 192.168.1.98 , 10.40.10.98 ไม่ทราบว่าต้องตั้งค่า Network อย่างไรครับ จะให้สามารถส่ง Data ได้\r\n\r\nData Port : 8300 ครับ ตัวเครื่อง Server มี Set ไว้ 2 วง  10.40.10.98 และ 192.168.1.98 ครับ\r\n\r\nSaint  >> แจ้ง ซีนน้อยดำเนินการ \r\nลองตั้ง ip เป็นตัว public ดูนะครับ\r\n10.40.10.98(TCP: 8300) > 58.137.58.162 (TCP: 8300)\r\n192.168.1.98(TCP: 8300) > 58.137.58.167 (TCP: 8300)', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:46:45', NULL, NULL, NULL),
('26a70b26-38c0-4c7d-9a24-a1a77bb88203', '6c6595bb55eb3374513891cfce0bec7e', 'System Architecture Design', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 01:00:55', NULL, NULL, NULL),
('27c8347a-5a9c-4c88-a104-6c880cc927f8', '6c6595bb55eb3374513891cfce0bec7e', 'เอกสารบูธและแผนภาพ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 00:59:37', NULL, NULL, NULL),
('27ec05d5-82bf-4280-ab94-4edb9516cbc2', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', 'Github : https://github.com/Amp-Apirak/queue_systems.git', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:28:12', NULL, NULL, NULL),
('2bcef5f1-326f-49ec-ad80-c336d34498fa', '3f4fcda2cc17020648deacae219f4708', 'ดำเนินการเรียบร้อยแล้ว', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:34:53', NULL, NULL, NULL),
('3285552e-fa8c-467a-b7da-1098bede9989', '7e24f638b40854acf4a162a469026e9e', 'ปิดงาน', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 11:44:00', NULL, NULL, NULL),
('329aee80-4640-4c15-841c-c0e03e84f853', 'e3263d7e808e55aedf5b535768fc8e3a', 'ดำเนินการเรียบร้อยแล้ว', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 09:46:40', NULL, NULL, NULL),
('3702ba23-dda6-442d-a4d7-ca7736bc0646', 'dbbf9e3d828c7ae3afcc8877b1570cc1', 'ค่าเดินทาง', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:37:36', NULL, NULL, NULL),
('3c07ae14-cc70-46b3-86c6-3adac671163a', '8eafedc8ba6f5fc271822ab67c72b744', 'ดำเนินการแจ้ง Timeline เรียบร้อย \r\nLink : https://pointit-innovation.atlassian.net/jira/software/projects/CB/boards/100?atlOrigin=eyJpIjoiZThlMTc2ZTJkYmQ2NGU1MGJhNDJhNDhiZTJlZjhmMTciLCJwIjoiaiJ9\r\n\r\nกำหนดเสร็จวันที่ : 14/11/2025', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 02:08:50', NULL, NULL, NULL),
('3d3f62f1-835f-464c-a167-5ae242f8bcdd', 'b3353d226e75aeda28572314a7abe62f', 'ดำเนินการแก้ไขเรียบร้อยแล้ว โมส่งให้ทางพี่นิพนธ์เรียบร้อย', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:51:33', NULL, NULL, NULL),
('3f9d53ce-9924-4e6d-9386-10c219a73dbf', '19d9016c34677e6c668edde9932256a1', 'อยู่ระหว่างการดำเนินการ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 03:40:28', NULL, NULL, NULL),
('40551420-245b-46cb-9901-e1d09505faf6', 'eefbd78e5f2426f6a79494712287f81b', 'ทำการแก้ไขหัวข้อและเพิ่มรายละเอียดในหัวข้อที่  1. 2. 3. 6. ครับ', 0, '6fbca1c7-761f-4027-ba4c-89e04832b717', '2025-10-30 06:59:03', NULL, NULL, NULL),
('413d1702-67b8-4397-93b2-131d3e5a8aa9', '5332f17963259b72d24f0321737a5302', 'สรุป Sim Iot \r\nใส่อุปกรณ์ AI Tracker \r\n1. กดแจ้งเหตุหรือพลัดตกหกล้ม ส่งสัญญาณแจ้งเตือนมายัง Platform\r\n2. ตัวอุปกรณ์ไม่สามารถโทรออกไปยังเบอร์ที่ตั้งค่าปลายทางไว้ได้ \r\n\r\nใส่มือถือ \r\n1. โทรเข้า อุปกรณ์ AI Tracker และ SIM หรือเครื่องมือถืออื่นๆ ไม่ได้ ***\r\n2. ส่งข้อความ อุปกรณ์ AI Tracker ได้', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 02:24:58', NULL, NULL, NULL),
('43a6a626-ca9f-467d-a3a6-e934f5a7d2f7', '30092d60a07c771aba052925e846639e', 'ดำเนินการประชุมเรียบร้อย', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:08:29', NULL, NULL, NULL),
('43c9ae96-1b06-4435-8f0a-4f037ec235cf', '7e24f638b40854acf4a162a469026e9e', 'ดำเนินการเรียบร้อยแล้ว', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 11:43:06', NULL, NULL, NULL),
('473f7819-79d4-4ca1-914f-8294af1e19ba', '54ce0ca8f7f731876d95512f0d28debd', 'ดำเนินการ Map IP : http://192.168.1.91:9000 ให้เรียบร้อยแล้ว สามารถเข้าใช้งานได้ปกติ แต่รูปไม่ขึ้น เลยทดสอบ  Map IP : http://192.168.1.91:9030 S3 เพื่อทดสอบว่าภาพแสดงหน้าระบบได้หรือไม่', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 06:59:02', NULL, NULL, NULL),
('4c40b499-e210-48f5-b3ea-a6064648d1a9', '5356767837cd88ec0215243511c749ab', 'ทดสอบ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 07:59:26', NULL, '2025-10-24 07:59:47', '2025-10-24 14:59:47'),
('524132d2-d142-494a-bcce-dd121e958ca1', '637269d63c441e053578d2a383dded3a', 'ดำเนินการปรับข้อมูลเรียบร้อยแล้ว \r\nLink : https://lookerstudio.google.com/reporting/c7cb7f9c-4014-43bc-8ffd-5443c20fac73/page/7Q6IF\r\nData : https://docs.google.com/spreadsheets/d/1rezUJcwsPkhtYWWxAfUiN2NeYAA2xEemqYkxCE2hRvA/edit?gid=0#gid=0', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:04:02', NULL, NULL, NULL),
('56159c4f-d4e1-4b6f-8b34-d3157e75eb09', 'eefbd78e5f2426f6a79494712287f81b', 'เพิ่มรายละเอียดครบทุกหัวข้อแล้วครับ รบกวนตรวจสอบด้วยครับ', 0, '6fbca1c7-761f-4027-ba4c-89e04832b717', '2025-11-03 01:14:27', NULL, NULL, NULL),
('5666ae1c-7132-4364-b68e-d24287107dd2', '9547da3d6ad0c6013ae7a098a9db6d61', 'สรุป\r\n- ทำให้ระบบ K-Lynx โดยพี่ขวัญ โดยพี่ขวัญจะช่วยดำเนินการขึ้นให้ในวันที่ 3/11/2025 เป็นต้นไป\r\n- ส่วนของ Setting\r\n* 1 กล้อง สามารถใช้งานได้ มากกว่า 1 Module สามาถรเปิดได้มากกว่า 1 (Enable AI จากกล้อง)\r\n* เรื่องเพิ่มใบหน้า ปรับจากหน้า K-watch งานตำรวจ\r\nSetting :\r\n1. Setting\r\n- Stream RTSP\r\n- Set ROI (Multi) /Poligon\r\n- Set เปอร์เซ็น % ของพื้นที่\r\n2, 4. Set ROI (Multi) /Poligon\r\n3. Set ROI (Multi) /เส้นตรง 2 เส้น\r\n4, 5. Set Labal/Name กำหนดกล้องขาเข้า/ขาออก\r\n7. Set ROI (Multi) /Poligon (เมื่อเปิดกล้องนับหรือแคปภาพนับจำนวนรถครั้งแรก เมื่อมีรถออก/เข้าให้นับต่อจากเดิม)\r\n8. AI Docker Pontit\r\n9. K-watch งานตำรวจ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 05:36:52', NULL, NULL, NULL),
('583271a9-7e79-48ad-8cc9-7aa3f7f63a2b', '6c6595bb55eb3374513891cfce0bec7e', 'สรุปแผน\r\nพี่แจ็ค : เตรียมกล้องทั้งหมด 6 ตัว เสา ในการตั้งบูธ และเครื่อง EDGE AI พร้อม OS\r\nซีนน้อย : ทดสอบเตรียม AI และการดึงข้อมูล Steam กล้องจากพี่แจ็ค และทดสอบการดึงข้อมูลรวมถึงทำ Analysis ตามฟังก์ชันเดิมที่เคยทำไว้\r\nรอสรุปผล ซึ่งแนวทางเราจะนำเครื่อง เครื่อง EDGE AI ไปตั้งที่หน้างานพร้อมกล้องเพื่อทำการวิเคราะห์ หรือ Analysis ตามฟังก์ชัน เมื่อได้ก็จะดึงมาทำ Dashbaord เป็นต้น', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 10:39:44', NULL, NULL, NULL),
('5835ea40-8661-474c-ac3b-56974caa81fb', '6c6595bb55eb3374513891cfce0bec7e', 'สรุปแผนก \r\nพี่แจ็ค : เตรียมกล้องทั้งหมด 6 ตัว เสา ในการตั้งบูธ และเครื่อง EDGE AI พร้อม OS\r\nซีนน้อย : ทดสอบเตรียม AI และการดึงข้อมูล Steam กล้องจากพี่แจ็ค และทดสอบการดึงข้อมูลรวมถึงทำ Analysis ตามฟังก์ชันเดิมที่เคยทำไว้ \r\nรอสรุปผล ซึ่งแนวทางเราจะนำเครื่อง เครื่อง EDGE AI ไปตั้งที่หน้างานพร้อมกล้องเพื่อทำการวิเคราะห์ หรือ Analysis ตามฟังก์ชัน เมื่อได้ก็จะดึงมาทำ Dashbaord เป็นต้น', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 10:38:29', NULL, '2025-10-16 10:39:40', '2025-10-16 17:39:40'),
('584b3d8d-fbf3-43ab-a242-bdaeb09f0220', 'eefbd78e5f2426f6a79494712287f81b', 'สรุปรายงานเรียบร้อยครับ รบกวนตรวจสอบด้วยครับ', 0, '6fbca1c7-761f-4027-ba4c-89e04832b717', '2025-10-30 01:08:50', NULL, NULL, NULL),
('5bb18306-812a-457f-bb0d-6483458f189a', '13bfaa55fc6a83ce652f638acc7259b7', 'ตรวจสอบเบื้องต้นตามไฟล์แนบ ไม่มีข้อมูลส่งมากับ API List', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 07:22:00', NULL, NULL, NULL),
('5c0e9527-6d71-4153-8c25-d7d4d9883c2f', 'eefbd78e5f2426f6a79494712287f81b', 'ฝากสรุปรายงาน และไฟล์แนบในระบบได้เลยครับ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-28 11:35:38', NULL, NULL, NULL),
('6583e268-7c87-4679-88a8-58f884e739b2', '7940e651309a1a31097e7734e2b4f960', 'ปิดงาน', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 05:37:27', NULL, NULL, NULL),
('68c3e65e-995c-4d7e-b5e0-c7b26d2aa30d', '574ec888c60f34c5858cbea206e671b0', 'ดำเนินการส่งให้ทางคุณก๊อฟ Outsource พิจารณาเรียบร้อยแล้วครับ รอแนบไฟล์ใบเสนอราคาการ Change', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:13:20', NULL, NULL, NULL),
('6d015795-fe58-4f05-a8d2-62271d6b9e06', '22d7359539ef8e483ba62547e2e5136e', 'ดำเนินการสรุปเรียบร้อยแล้ว \r\nLink : https://docs.google.com/document/d/1whPSPl8JxRuNvAPPHxdLOA2ELLRlYFr62Bn8gepChH4/edit?tab=t.0', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 05:19:50', NULL, NULL, NULL),
('6ea901e4-ade8-45ba-bfed-e7a6b1d25540', 'cbf32b8dd3e4ef748855c23d4b162e8d', 'ประสานทีม Network ดำเนินการเรียบร้อยแล้ว', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:38:03', NULL, NULL, NULL),
('6f70efca-4ade-4b0f-ad8f-bd3a1d5995fd', '3641ec530675c2226fc1420f5d02d71e', 'ดำเนินการเรียบร้อยแล้ว', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 06:27:35', NULL, NULL, NULL),
('7222807d-168f-439c-a0a0-b8cd87b07ef4', '713616155e9f40ce7a0bf5f1bbd0ca5f', 'ดำเนินการประชุมเรียบร้อยแล้ว', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 12:06:49', NULL, NULL, NULL),
('79bf0d5b-4095-4a27-9a5e-5060a04797cd', 'dbbf9e3d828c7ae3afcc8877b1570cc1', 'ดำเนินการเข้างานติดตั้งเรียบร้อยแล้วครับ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:38:56', NULL, NULL, NULL),
('7b3859c8-d27c-452d-a6a3-851d61faaeeb', 'a5eb1b3774bb8de6a806482e70a0c608', 'จากการตรวจสอบ Save_directory.php redirect ไปหน้า /WatchmanData/main.php จาก code header(\"Location: /WatchmanData/main.php\");\r\nexit();\r\nแก้ไขโดย comment ส่วนนี้ไว้\r\n--\r\nจากนั้นเจอ Error เกี่ยวกับวันที่ \r\n\"SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: \'\' for column \'date_tr\' at row 1Error saving data: Incorrect datetime value: \'\' for column \'date_tr\' at row 1\"\r\nซึ่ง column \'date_tr\' ไม่สามารถเป็นค่าว่างได้\r\nแก้ไขโดย ปรับตัวแปรที่รับมาจาก Form ให้เป็น NULL ถ้าว่าง และปรับ DB ให้ Default อนุญาตให้เป็น NULL', 0, 'f30e8b87-d047-4bca-9b34-d223170df87c', '2025-10-16 08:58:34', NULL, NULL, NULL),
('7e0025dd-2821-45b3-8c1f-8d0f021886c3', '887ac5a1a781314baf1c0823330b88fe', 'ปันตรวจสอบ : ไฟดับ ทำให้ VM พัง ใช้งานไมไ่ด้ ส่งผลกระทบให้ Database พังหรือ Config หาย.', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-07 08:26:40', NULL, NULL, NULL),
('80dbf98e-c972-4a97-b651-e1235699d433', '6c6595bb55eb3374513891cfce0bec7e', 'System Architecture Design', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 01:00:25', NULL, '2025-10-21 01:00:39', '2025-10-21 08:00:39'),
('83baf920-d4dc-4c28-93ce-7d77afac7817', 'e0639361c653a406d17686669cae0a1f', 'ดำเนินการเข้าหน้างานเก็บ Requirement เรียบร้อยแล้ว \r\nประสานงานคุณ คิม ธรรมศาสตร์ 0857336990', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 13:15:51', NULL, NULL, NULL),
('845afbab-4afc-4384-9885-695051456433', '19d9016c34677e6c668edde9932256a1', 'ระบบเก่า \r\nhttps://ps07.zwhhosting.com:2083/cpsess0210054732/frontend/jupiter/index.html?=undefined&login=1&post_login=49254017234223\r\nUsername : zscfopsz\r\nPaasowrd : f6mY26yTG;6kR+', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 08:24:29', NULL, NULL, NULL),
('87ae7017-2d93-4a27-a5e2-81ce01ed0510', 'b180015710d27a896b70940424c14ee0', 'ดำเนินการเรียบร้อยแล้ว', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 11:53:21', NULL, NULL, NULL),
('89c8afcd-d52c-4ca4-9e00-c1d418facc70', 'c963ae31f67a991ec33b1cd411161af5', 'ปิดเคส', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:49:58', NULL, NULL, NULL),
('8a4eae56-035f-476e-8554-f32641c6761a', '887ac5a1a781314baf1c0823330b88fe', 'ปันประสานงาน พี่บอส ทำการ Roll Back', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-07 08:27:56', NULL, NULL, NULL),
('8a977da6-f7bd-4113-86de-37418f594ae0', '097c3183d66a2f98b7f0927512b0895c', 'แจ้งทีมเรียบร้อยแล้วในกลุ่มไลน์ Dev', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 03:54:25', NULL, NULL, NULL),
('8adafa94-8b89-48d8-ba62-930ac5e02d4f', 'eefbd78e5f2426f6a79494712287f81b', 'แก้ไขเบื้องต้นและเพิ่มรายละเอียดในหัวข้อที่ 1. 2. 3. 6.แล้วครับ\r\nรายละเอียดในหัวข้ออื่นจะอัพเดตเพิ่มภายหลังนะครับ', 0, '6fbca1c7-761f-4027-ba4c-89e04832b717', '2025-10-30 06:55:34', NULL, '2025-10-30 06:56:26', '2025-10-30 13:56:26'),
('99befafe-e438-4e33-be27-395465b8b2bd', 'c9a4a76087ad93b691fa30f4dda02be0', 'รบกวนดำเนินการด้วยครับ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 09:52:48', NULL, NULL, NULL),
('a44334e0-837f-4cbd-9378-3eb3f0189878', '54ce0ca8f7f731876d95512f0d28debd', 'แก้ไขเรียบร้อย สาเหตุ ตัว code ไป fix ตาม config s3 ซึ่งมัน fix ไปที่ ip ตอนนี้ เปลี่ยนให้มา fix ตามชื่อที่พี่ map ให้ https://gui.pointit.co.th/', 0, 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', '2025-10-13 07:15:41', NULL, NULL, NULL),
('a7d72e0b-555b-479c-a13a-c988f37acb35', 'c963ae31f67a991ec33b1cd411161af5', 'ดำเนินการเรียบร้อยครับ ทดสอบระบบ นำเสนอโครงการและแพลตฟอร์ม\r\nแก้ไขข้อมูลแพลตฟอร์ม\r\n\r\nhttps://app.aidery.io/auth/login\r\nDomaim : เทศบาลตำบลด่าสำโรง\r\n- Username : admin@dansamrong\r\n- Password 12345678\r\nEMS Domin :\r\n- Username : admin@dansamrong\r\n- Password: Admin@2025\r\n\r\nอุปกรณ์ 1\r\n- Name : นางสาวภัทราอร อมรโอภาคุณ (Demo)\r\n- Device Name : GPS_Tracker\r\n- Device : 863922038085503\r\n\r\nอุปกรณ์ 2\r\n- Name : นางสาวภัทราอร อมรโอภาคุณ (Demo)\r\n- Device Name : ทดสอบด่าสำโรงGPS Tracke-32\r\n- Device : 863922038157161', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 08:52:41', NULL, NULL, NULL),
('aa7e23a3-a19f-405d-8c20-99224db3ff46', '0475cf8c871b6d870a03ca8beeba95e4', 'ดำเนินการ Setup Platform เรียบร้อยแล้ว ส่วนของ EMS Platform ให้ทางผู้ให้บริการแพลตฟอร์ม (คุณตั๊ม) ดำเนินการสร้างให้ (อยู่ระหว่างดำเนินการ)', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 01:37:57', NULL, NULL, NULL),
('ab76b048-e93d-40ae-a568-2203b91bcd26', '375fbb2a1a2a398c6ee3fcbba58316cb', 'ดำเนินการเรียบร้อยแล้วครับ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 11:29:52', NULL, NULL, NULL),
('ac220ca0-6790-43d5-b59b-7b052cd9037b', '8e50c1e61286798d0c25cad94c874699', 'ประชุมเรียบร้อย \r\nต้องการผู้ช่วยในการทดสอบใช้งานระบบ ให้ทางพี่ไข่แก้ไขเรียบร้อยทางตุ๋นดำเนินการทดสอบ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:31:56', NULL, NULL, NULL),
('ac7e1365-7d21-4471-b4e4-6c949a7cadb0', '632456371e6b634ceb7b30c85cf89ebf', 'ดำเนินการสร้างเรียบร้อยแล้ว', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:32:41', NULL, NULL, NULL),
('ad833472-3113-4e21-9ad0-b67b0ec6da3e', '6c6595bb55eb3374513891cfce0bec7e', 'P4ssw0rd', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 10:01:47', NULL, NULL, NULL),
('ae431231-76aa-4466-85de-196ba939b2d7', 'eefbd78e5f2426f6a79494712287f81b', 'ตารางการตรวจสอบ \r\n1. การลงทะเบียน Tag  RFID ใหม่\r\n2. ขึ้นทะเบียนคุม\r\n3. ผูกแท็ก RFID เข้ากับ พัสดุ\r\n4. นำพัสดุจัดเก็บเข้าคลัง (จากคลังสำรองฝากยุทธภัณฑ์ชั่วคราว --> เข้าคลังอื่นๆ) \r\n5. การเบิกจ่ายพัสดุ \r\n6. การโอนย้ายพัสดุ\r\n7. การแจ้งเตือนการนำพัสดุเข้าคลังโดยไม่ได้รับอนุญาติ \r\n8. การแจ้งเตือนการนำพัสดุออกจากคลังโดยไม่ได้รับอนุญาติ\r\n9. แจ้งเตือนอุณหภูมิผิดปกติ\r\n\r\n การทดสอบจ่าย Supply ออก (SET MOVEMENT TYPE EXIT)\r\n1. เจ้าหน้าที่ทำเรื่องเบิกจ่ายพัสดุ ระบบบริหารจัดการพัสดุ (Inventory Management) >> สถานะเป็น Requisition\r\n2. เจ้าหน้าที่นำพัสดุที่เบิกจ่าย เดินผ่าน Gateway Gate_Monitor >> สถานะเป็น Disbursed \r\n3. เจ้าหน้าที่นำแท็กออก และส่งมอบให้กับผู้ที่ร้องขอ \r\n4. เจ้าหน้าที่นำแท็กกลับมาใช้งานใหม่ โดย ปรับสถานะแท็กจากระบบ >> ระบบติดตามพัสดุคลังส่งกำลังบำรุง >> เมนู RFID Management >> เลือกแท็ก >> ไปแถบ Action : กด Set to Ready  สถานะจะกลับมาเป็น Ready  และหากไปตรวจสอบที่ระบบ ระบบบริหารจัดการพัสดุ สถานะจะเปลี่ยนจาก ใช้งานแล้ว เป็น ยังไม่ได้ใช้งาน (พร้อมใช้งานใหม่)', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-30 01:57:54', NULL, NULL, NULL),
('ae69f256-af76-4e3a-80a6-715dff4fd996', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', 'แนบไฟล์', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:27:32', NULL, NULL, NULL),
('af233fcc-28a9-4c74-b1d6-419da648a983', 'eefbd78e5f2426f6a79494712287f81b', 'โอเคครับ ปิดงานได้', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 07:44:21', NULL, NULL, NULL),
('afd71f24-dd52-48e2-a534-483ad23c9737', '47255f8f395045b169b65b16f91ebbe9', 'ดำเนินการแจ้ง User ให้สร้าง Username Password เองเรียบร้อยโดยพี่ซีน', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:50:46', NULL, NULL, NULL),
('b40a5a9c-a48a-4792-ba52-c3f8169b2bc6', '2d72ce32ffe570003a84eea4bbc5153e', 'จัดทำรายงานเรียบร้อยค่ะ', 0, 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 02:19:49', NULL, NULL, NULL),
('b57628da-4741-4595-a763-cdd4f38eb477', 'ec78cca5255db3e17e0371382cb93425', 'ดำเนินการสร้างเรียบร้อยแล้ว', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-05 07:11:28', NULL, NULL, NULL),
('b868f198-c68e-417b-aa03-d769c7e074b6', 'ba35446efb9ef49c29fd4cb93181e225', 'ดำเนินการสั่งซื้อเรียบร้อยแล้ว', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 08:53:48', NULL, NULL, NULL),
('ba334e82-9677-4988-9da6-065531c6fd07', 'c3fae15b8715db998f7865db7e4e89ec', 'ดำเนินการเรียบร้อยแล้ว \r\nLink : https://docs.google.com/document/d/1qEDOqUeXuo9fp8avC4kdJFe4bra-SSlX/edit', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-17 06:40:31', NULL, NULL, NULL),
('bc199db6-8875-44c4-adb4-d25cde17741f', '6c6595bb55eb3374513891cfce0bec7e', 'ของ ai benz ในเครื่อง \r\nedge at Boots Benz\r\n1283279958\r\nP@ssw0rd\r\n\r\nผูก flow ai เข้าเรียบร้อยแล้วครับ\r\nและฝั่ง\r\nedge at pointIt\r\n1441778911\r\nKudson@2023\r\nผูก flow ai เข้าเรียบร้อยแล้วเช่นกันครับ\r\n\r\nพอกับ test โดยการให้ พี่ jack เดินไปมา\r\n\r\nรบกวนผูก cloudflared เพื่อให้ระหว่างเครื่องคุยกันได้ครับ\r\n\r\nPC\r\n1283279958\r\nP@ssw0rd', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:51:45', NULL, NULL, NULL),
('bd4f578a-3a93-4c79-a021-d742ca86dcde', '0475cf8c871b6d870a03ca8beeba95e4', 'ดำเนินการเรียบร้อยครับ ทดสอบระบบ นำเสนอโครงการและแพลตฟอร์ม\r\nแก้ไขข้อมูลแพลตฟอร์ม\r\n\r\nhttps://app.aidery.io/auth/login\r\nDomaim : เทศบาลตำบลด่าสำโรง\r\n- Username : admin@dansamrong\r\n- Password 12345678\r\nEMS Domin :\r\n- Username : admin@dansamrong\r\n- Password: Admin@2025\r\n\r\nอุปกรณ์ 1\r\n- Name : นางสาวภัทราอร อมรโอภาคุณ (Demo)\r\n- Device Name : GPS_Tracker\r\n- Device : 863922038085503\r\n\r\nอุปกรณ์ 2\r\n- Name : นางสาวภัทราอร อมรโอภาคุณ (Demo)\r\n- Device Name : ทดสอบด่าสำโรงGPS Tracke-32\r\n- Device : 863922038157161', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 08:51:33', NULL, NULL, NULL),
('be66e481-0216-4605-bf49-cc8b7996b004', '4dd8e701d99aa9dd90edfc7e94dae7eb', 'สรุปแนวทาง \r\nSolution 1 : แก้ไขปัญหา เครื่องดับบ่อย \r\nหาเครื่อง PC ที่สามารถตั้งค่า Bios ให้รองรับ เมื่อมีการจ่ายไฟให้อุปกรณ์ในอุปกรณ์เปิดใช้งานอัติโนมัติ >> พี่แจ็ค (มีอยู่แล้ว)\r\nSolution 2 : แก้ไขปัญหา Internet ช้าดึงภาพ Remote ไมไ่ด้ Data หาย\r\nเปลี่ยน Packgate Sim ให้ Internet มีความเร็วที่รองรับการใช้งานของกล้องและการส่งข้อมูล >> พี่ซีน (แก้ไขแล้ว)\r\nSolution 3 : เปิดกล้องต้องเปิดกล้องทีละตัว โดยกดลิงค์ต้องใส่ Password ทุกครั้ง\r\nเพิ่มกล่อง Gateway Dason สำหรับดึง Stream จากกล้องเข้ามาที่ออฟฟิต และดึงเข้า K-lynx \r\n\r\nเพิ่มเติม \r\n- ทำเสาร์ให้มีความคงทนและได้มาตรฐานกว่าเดิม  >> พี่แจ็ค (รับดำเนินการ)\r\n- งานมีเพิ่ม 2 งาน ประกอบด้วย \r\n     1. วันที่ 05/11/2025  ที่เซ็นทลัดเวตเกย์   ใช่ Solution เดิม แต่เพิ่มการ Backup ข้อมูลเพิ่มโดยตั้งค่า PC ไว้หน้างานเพื่อให้ข้อมูลเข้าที่ฐานข้อมูล PC Backup ไว้หน้างาน ก่อน\r\n    2. วันที่ 28/11/2025 ที่เซ็นทลัด Pack  ใช่ Solution ใหม่ ไปด้วย K-Lynx', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 10:13:34', NULL, NULL, NULL),
('c05dad38-5e58-481f-92e9-1433b8db422a', '8b5dbcd8e12a7f3ada28f739b9f3c9f0', 'ดำเนินการเรียบร้อยแล้ว\r\nLink : https://docs.google.com/document/d/1t6ok1OrOA9rVx1ZfhHld6j1jzhD4TCqDG9sKF8NKpcA/edit?tab=t.0', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 11:40:48', NULL, NULL, NULL),
('c12f0a5b-7bc5-4135-baa5-4dd87e220df4', '1646d2451f8f11f24cd214759fd6535b', 'สรุปจาการประชุมคราวๆ \r\nผู้เข้าร่วมประชุม มีนายกเทศบาลตำบลด่านสำโรง เป็นประธาน พร้อมทั้งรองประธาน ท่านปลัด ผอ.กองสาธารณสุข และคณะ รวมประชุมสรุปโครงการ\r\nการทำเทคโนโลยีอุปกรณ์แจ้งเหตุฉุกเฉิน และคัดกรอกค่าสุขภาพ \r\nโดยมีทีม Support ประกอบด้วย \r\n1. คุณภานุวัฒน์, คุณกว้าง  ดูแลระบบแพลตฟอร์มกิน-อยู่-ดี\r\n2. คุณโอ๋ ฝ่ายขาย บริษัทพอทย์ \r\n3. คุณแอมป์ Support Application Platform และอุปกรณ์ \r\n4. คุณตี้  ฝ่ายขาย บริษัทบลูโซลูชั่น \r\n\r\nสรุปโครงการดังนี้ \r\nจำนวนผู้สูงอายุของเทศบาลตำบลด่านสำโรง ประมาณการ 11,000 คน \r\nแบ่งเกณฑ์คัดกรองการใช้งานอุปกรณ์ โดยคำนึงถึงปัจจัยดังนี้ \r\n1. กลุ่มโรค NCD \r\n2. กลุ่มผู้สูงอายุ 60 ปี ขึ้นไป\r\n3. กลุ่มผู้ป่วยติดเตียง\r\n4. กลุ่มความดันสูง\r\nหมายเหตุ : ไม่ได้มองถึงฐานะยากจนหรือบุคคลรวย \r\n\r\nโครงการแบ่งออกเป็น 3 เฟส \r\nเฟสแรก :  จำนวนอุปกรณ์ 5000 ตัว (AI Tracker) >>> 1 ตัว 6000 บาท , จำนวนอุปกรณ์นาฬิกา 100 ตัว ตัวละ 5000 บาท +อื่นๆ มูลค่าโครงการทั้งสิ้น 76,000,000 บาท\r\nเฟสสอง :  จำนวนอุปกรณ์ 3000 ตัว (AI Tracker) +อื่นๆ มูลค่าโครงการทั้งสิ้น 45,000,000 บาท\r\nเฟสสาม :  จำนวนอุปกรณ์ 3000 ตัว (AI Tracker) +อื่นๆ มูลค่าโครงการทั้งสิ้น 45,000,000 บาท\r\n\r\nเพิ่มเติม \r\nจัดเตรียมชุดกระเป๋า 5 ชุด ให้ทางบริษัทเตรียมเสนอให้ทางพี่ตั๊ม', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 04:35:02', NULL, NULL, NULL),
('c6062a9f-d01a-4c4a-b248-5bb8542615fd', '8b3f3eb00f76e9a698bcc04e0243a566', 'เสร็จเรียบร้อยค่ะ', 0, 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:04:20', NULL, NULL, NULL),
('c74c1279-6a1a-42d2-a334-e3f421b42b27', '1646d2451f8f11f24cd214759fd6535b', 'ปิดงาน', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 07:50:19', NULL, NULL, NULL),
('cb24613c-895c-4555-b58c-1eb60a231759', '7eba9a30705d2c1ddc0a1e2d6cac3d96', 'ประสานงานพี่เบียร์ดำเนินการครับ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 04:04:04', NULL, NULL, NULL),
('ced8b0ae-5e3f-4cad-8227-ccee6ff406a5', '2bd0058981314c1250b10a592f9af020', 'ดำเนินการประสานงานพี่บอส เพื่อขอ Key ได้แล้วดังนี้ Activation keys for ‎Office LTSC Standard 2024‎\r\nKey: RVRVN-2Y2K2-M9KC2-YY86K-7XRPW\r\n\r\nประสานงานกับทางพี่ไข่ Activate เรียบร้อยตามไฟล์แนบ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 12:57:36', NULL, NULL, NULL),
('d029cda2-d7f1-44b0-a794-7118dc352aee', '818fa45a1b16cd16b831a6f1b6e63d72', 'ดำเนินการประชุมเรียบร้อย', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 08:03:53', NULL, NULL, NULL),
('d16e5869-bb81-4e4c-9962-2673bec4d3f0', '112f5fda773c2b665958457654cba090', 'ดำเนินการเรียบร้อยแล้ว \r\nตาม Link แนบ \r\nhttps://docs.google.com/document/d/1gCNtm8eIs9VYqZ4Dl-3FakLAZFTa_M_8PPp95_RIq_k/edit?tab=t.0\r\nhttps://docs.google.com/document/d/1BRmfkRDzpyypgG5C4MCtEXwNAexgP_zjBj0DZuPuGIs/edit?tab=t.0', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 12:47:20', NULL, NULL, NULL),
('d872d5a8-e94b-4af3-8944-7ce9a5f3c12a', '0475cf8c871b6d870a03ca8beeba95e4', 'Domaim : เทศบาลตำบลด่าสำโรง \r\n     - Username : admin@dansomrong  \r\n     - Password 12345678\r\nEMS Domin : \r\n     - Username : admin@dansomrong  \r\n     - Password 12345678\r\n\r\nอุปกรณ์ 1\r\n     - Name : นางสาวภัทราอร อมรโอภาคุณ (Demo)\r\n     - Device Name : GPS_Tracker\r\n     - Device : 863922038085503\r\n\r\nอุปกรณ์ 2\r\n     - Name : นางสาวภัทราอร อมรโอภาคุณ (Demo)\r\n     - Device Name : ทดสอบด่าสำโรงGPS Tracke-32\r\n     - Device : 863922038157161', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 01:26:19', NULL, NULL, NULL),
('db13c638-b492-4514-aaec-7f73bacfaefd', '9439b4fe59f3705e5dd798f99ea78bf7', 'เสร็จเรียบร้อยค่ะ', 0, 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:02:55', NULL, NULL, NULL),
('df47af9c-f3ff-42f7-a187-207cd0974544', '304700a01748aa660aa5afd0930f1a0e', 'ดำเนินการ Setup เรียบร้อยแล้ว ทดสอบสามารถเข้าใช้งานได้ปกติ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:26:42', NULL, NULL, NULL),
('e45a6135-61a6-42fc-8269-abeee812314c', '7e24f638b40854acf4a162a469026e9e', 'เเเเเ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 11:42:35', NULL, '2025-10-14 11:42:38', '2025-10-14 18:42:38'),
('e49bfb24-e198-4418-a8d8-fd0b1d452807', '2d82b2958ff60d48fee678b5e5cdadbb', 'ดำเนินการเรียบร้อย', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 12:21:07', NULL, NULL, NULL),
('e4a4deab-b8a7-45c0-a2d7-470ab0b9f1bd', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', 'ดำเนินการเรียบร้อยแล้ว ตามไฟล์แนบ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:26:37', NULL, NULL, NULL),
('e6986be4-51f5-4e44-9ea0-1e3a55c634b0', '682b8b2fc1c5de91674a1773db6f539d', 'พี่เบียร์ให้ทาง Support หน้างาน ทำเรื่องจองใหม่ไป แล้วเดียวเปลี่ยนวันให้ แล้วไป approve เอง เสร็จแล้ว เอา id มา เดียวเปลี่ยนวันให้', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 05:08:24', NULL, NULL, NULL),
('ea23080d-409f-4e2a-ac07-2eefd283ca69', 'f3a0b4c735c95737d37c23cd33793ae9', 'ดำเนินการตรวจสอบตามเอกสาร : ภาพรวม 90 %  ทุกข้อมีการปรับปรุงพัฒนาแล้ว รอดำเนินการทดสอบ\r\nเอกสาร Check List : https://docs.google.com/document/d/1gjCL0HgcgPSES5WmeaTWm5IeXRBem3GQI1YMDQsUKwI/edit?usp=sharing\r\nเอกสารเพิ่มเติม Test Requirement :  https://www.canva.com/design/DAGwbMtIaOM/_fFoCcJnCurxbQZ5iUOSjQ/edit?utm_content=DAGwbMtIaOM&utm_campaign=designshare&utm_medium=link2&utm_source=sharebutton', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-06 03:45:15', NULL, NULL, NULL),
('eabf6327-bcb7-434e-88ba-7a3f73e44e75', 'db915fd3678a3c7a9a85d8aa606f02f5', 'อยู่ระหว่างการตั้งค่า ติดปัญหาเมื่อตั้งค่า cloudflare เข้าใช้งานได้แต่ไม่สามารถเปิดแสดงภาพวีดีโอได้', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 15:28:12', NULL, NULL, NULL),
('f250312b-4959-4aa1-a4ff-0e81594394e3', '6a5fda3fdaa7b4addd1f1c1f68ce7d12', 'แนบ', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-25 16:10:21', NULL, '2025-10-25 16:10:27', '2025-10-25 23:10:27'),
('f8cb9448-c093-469a-8bba-5f4ab6012225', '7671fe71ebe2a276cf49f946914b5082', 'สรุป\r\n1. ราคาอุปกรณ์ปุ่มกดแจ้งเหตุ ไม่เกิน 1900 บาท/ตัว (ไม่ต้องการคอลลิ่ง) จำนวน 11000\r\n2. ซิมการ์ด >> ทางอาจารย์ Provide ให้ ในราคาไม่เกิน 50 บาท/ Sim\r\n3. ราคานาฬิกา ไม่เกิน 2500 บาท/ตัว จำนวน 10000\r\n4. ลดราคา Platform จาก 130 เหลือ 50/คน', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-30 15:12:06', NULL, NULL, NULL),
('f9e3ec46-ac18-4a59-a61b-4c0bce77a0a8', '6c6595bb55eb3374513891cfce0bec7e', 'ระบบทำการติดตั้งจบงานเรียบร้อย\r\n\r\nโดย flow ปัจุบันของ ระบบของผม (tp-detection)\r\nเครื่อง  edge AI 1 u.\r\nเครื่อง pc 2 u.', 0, 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', '2025-11-06 06:46:38', NULL, NULL, NULL),
('f9e402f5-e6b4-4276-904e-e9f48ffe7e71', '1646d2451f8f11f24cd214759fd6535b', 'แนบรูปภาพสำหรับเลขไมค์ค่าเดินทาง', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 04:12:14', NULL, NULL, NULL),
('ff976d9f-400e-4701-8d63-f22b6f155bbc', 'dc0fbcaa30d9222f9ec95cce4d040b49', 'สรุป\r\nสถานที่ : เทศบาลด่านสำโรง\r\nผู้เข้าร่วมประชุม \r\nคุณตี้ \r\nคุณที่ปรึกษาโครงการ ม.ธรรมศาสตร์\r\nคุณซีน\r\nคุณแอมป์ \r\n\r\nสรุปการทำโครงการราคาอุปกรณ์ และ อื่นๆที่เกี่ยวข้อง แบ่งได้ดังนี้ \r\n1. แพลตฟอร์ม  ---> ดูแลโดยคุณตั๊ม\r\n2. อุปกรณ์ --- > ดูแลโดยคุณซีน \r\n3. บริการอบรบ/ติดตั้ง\r\n     3.1. รับมอบอุปกรณ์ และอบรมการใช้งาน ---> โครงการมีการกันงบประมาณส่วนนี้ไว้แล้ว โดยจะเรียกผู้รับบริการเข้ามาอบรมและรับมอบที่ เทศบาลด่านสำโรง\r\n     3.2. บริการเฝ้าระวัง Tire 2  --- > คุณซีน (ยังไม่ได้สรุป รอทางคุณซีน เสนอเป็น Option เสริม)', 0, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 07:49:48', NULL, NULL, NULL);

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
-- Dumping data for table `service_ticket_history`
--

INSERT INTO `service_ticket_history` (`history_id`, `ticket_id`, `field_name`, `old_value`, `new_value`, `changed_by`, `changed_at`) VALUES
('0337a8bf-ad99-11f0-9a0c-005056b8f6d0', 'e6682bbd1a2807550f0b7b0235abd8c8', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 09:41:56'),
('09bb267e-a802-11f0-aff6-005056b8f6d0', '54ce0ca8f7f731876d95512f0d28debd', 'status', 'New', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 06:59:26'),
('0a8c41fb-b3a0-4441-b049-eca50c4bf5c7', '7e24f638b40854acf4a162a469026e9e', 'comment_deleted', 'e45a6135-61a6-42fc-8269-abeee812314c', 'deleted', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 11:42:38'),
('0a97baaa-b8ad-11f0-9a0c-005056b8f6d0', '637269d63c441e053578d2a383dded3a', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:04:02'),
('0e6936ca-b923-11f0-9a0c-005056b8f6d0', '8eafedc8ba6f5fc271822ab67c72b744', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 02:08:50'),
('11305e3f-0283-4c50-a0b2-f06030409bf5', 'eefbd78e5f2426f6a79494712287f81b', 'comment_deleted', '8adafa94-8b89-48d8-ba62-930ac5e02d4f', 'deleted', '6fbca1c7-761f-4027-ba4c-89e04832b717', '2025-10-30 06:56:26'),
('118e5b62-e20a-4910-b04c-80fa329a6e27', '818fa45a1b16cd16b831a6f1b6e63d72', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 08:03:53'),
('140194f5-ce70-4854-a23d-9bb7a465b321', 'cbf32b8dd3e4ef748855c23d4b162e8d', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:38:03'),
('1561ed98-cf3a-4662-b27a-c77a1ac3afcc', 'c3fae15b8715db998f7865db7e4e89ec', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-17 06:40:31'),
('1676cd2d-fde0-4098-bbb6-485ac786bb4c', 'bc420db35f715fdc06e983365015152d', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 05:56:02'),
('1cb33ce9-69d8-4135-a7aa-bac4dbcf23b5', '6c6595bb55eb3374513891cfce0bec7e', 'comment_deleted', '5835ea40-8661-474c-ac3b-56974caa81fb', 'deleted', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 10:39:40'),
('1eea2b61-c3ce-46d0-966a-3237be514ebc', '0475cf8c871b6d870a03ca8beeba95e4', 'status', 'Pending', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 08:51:33'),
('206ca7e0-b08b-11f0-9a0c-005056b8f6d0', '19d9016c34677e6c668edde9932256a1', 'status', 'New', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 03:40:27'),
('22a2300d-b46f-11f0-9a0c-005056b8f6d0', '519fee5632c4c3a2970bde1aeb2a1c75', 'status', 'On Process', 'Resolved', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-10-29 02:30:13'),
('231ed7df-ae5b-11f0-9a0c-005056b8f6d0', '0475cf8c871b6d870a03ca8beeba95e4', 'status', 'Pending', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 08:51:33'),
('2472476a-9e61-4991-a16d-b464e1e76dd7', 'aa95c56d33fca80adb2f4305b9d22e7d', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 11:38:20'),
('256a046e-5e45-4aca-9018-42dff82e55f2', 'f3a0b4c735c95737d37c23cd33793ae9', 'job_owner', 'Apirak Bangpuk', 'Tulatorn Yongprayoon', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-06 03:45:15'),
('269aca91-b2dd-11f0-9a0c-005056b8f6d0', '632456371e6b634ceb7b30c85cf89ebf', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:32:40'),
('276ae041-826c-4fca-b8ae-9670012fbb48', '9547da3d6ad0c6013ae7a098a9db6d61', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 05:36:52'),
('2a15bd88-b3f2-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', 'job_owner', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '6fbca1c7-761f-4027-ba4c-89e04832b717', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-28 11:35:38'),
('2c5dc609-36c5-460a-a0fa-4f2eee6e002d', '4d0f17400296e43ac2a70f6764cce016', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-05 07:00:37'),
('2c711b36-5be6-4886-8ff4-a5a86a9c866e', '304700a01748aa660aa5afd0930f1a0e', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:26:42'),
('2c8d8398-ab24-11f0-9a0c-005056b8f6d0', 'c3fae15b8715db998f7865db7e4e89ec', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-17 06:40:31'),
('2d35f1ab-2214-4c98-b007-63d5d0595f70', 'e3263d7e808e55aedf5b535768fc8e3a', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 09:46:40'),
('2e91b6bc-a63e-41ad-9296-d152a39dc4a2', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:28:12'),
('2f5b74a2-a900-11f0-aff6-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 13:18:42'),
('30b270c3-af5c-11f0-9a0c-005056b8f6d0', '8e50c1e61286798d0c25cad94c874699', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:31:56'),
('323814e2-a13e-4784-a00c-f762ead5f51b', 'eefbd78e5f2426f6a79494712287f81b', 'status', 'On Process', 'Waiting for Approval', '6fbca1c7-761f-4027-ba4c-89e04832b717', '2025-10-30 01:08:50'),
('3257de5d-be1e-11f0-8604-005056b8f6d0', 'c9a4a76087ad93b691fa30f4dda02be0', 'status', 'On Process', 'Resolved', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-11-10 10:15:36'),
('33038611-ad5c-11f0-9a0c-005056b8f6d0', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:26:37'),
('34818b8f-bb52-4e11-b7e9-5ed0c59d13bb', 'a5eb1b3774bb8de6a806482e70a0c608', 'status', 'On Process', 'Resolved', 'f30e8b87-d047-4bca-9b34-d223170df87c', '2025-10-16 08:58:34'),
('35b2a0ed-b489-11f0-9a0c-005056b8f6d0', '9547da3d6ad0c6013ae7a098a9db6d61', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 05:36:52'),
('36c5fa5e-badc-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', 'status', 'On Process', 'Resolved', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', '2025-11-06 06:46:47'),
('387eb443-642d-4239-812a-e8fab19f6a78', '7e24f638b40854acf4a162a469026e9e', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 11:44:00'),
('389b7abe-09b0-45f8-b00c-ba7b1542fa48', '4526af8066b18a46eb44341c5b187ad8', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:59:54'),
('38a61f53-af54-11f0-9a0c-005056b8f6d0', '3f4fcda2cc17020648deacae219f4708', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:34:53'),
('3a83bc53-b0ce-11f0-9a0c-005056b8f6d0', '8b5dbcd8e12a7f3ada28f739b9f3c9f0', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 11:40:47'),
('3af44293-573d-4a1c-8761-31e16fcdff5f', 'dbbf9e3d828c7ae3afcc8877b1570cc1', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:38:56'),
('3b881bcb-ad5c-11f0-9a0c-005056b8f6d0', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', 'status', 'Resolved', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:26:51'),
('3c28572e-be1e-11f0-8604-005056b8f6d0', '682b8b2fc1c5de91674a1773db6f539d', 'status', 'On Process', 'Resolved', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-11-10 10:15:52'),
('3eb6d634-efdd-434c-ab9c-fd01c21e3b4b', '5eb8ec3a227ec1dab8b5463cb9d26f12', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-23 13:03:55'),
('3edecddd-a807-4ce7-b7eb-3bdbd8098aad', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:26:37'),
('42f63e30-be1e-11f0-8604-005056b8f6d0', 'a005d2bc5a0c79101ef49cdfd84e88bf', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 10:16:04'),
('43519840-8954-4a0b-bef2-cb953d38ecb3', '574ec888c60f34c5858cbea206e671b0', 'status', 'On Process', 'Pending', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:13:20'),
('453f704c-a8fd-11f0-aff6-005056b8f6d0', '2bd0058981314c1250b10a592f9af020', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 12:57:50'),
('46cc6ce9-97fd-480d-8ebd-11156df54719', '6c6595bb55eb3374513891cfce0bec7e', 'comment_deleted', '80dbf98e-c972-4a97-b651-e1235699d433', 'deleted', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 01:00:39'),
('47edd9eb-bdf3-11f0-8604-005056b8f6d0', '682b8b2fc1c5de91674a1773db6f539d', 'status', 'New', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 05:08:24'),
('4906de0e-b0be-11f0-9a0c-005056b8f6d0', 'e3263d7e808e55aedf5b535768fc8e3a', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 09:46:40'),
('49c99979-ad92-11f0-9a0c-005056b8f6d0', 'ba35446efb9ef49c29fd4cb93181e225', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 08:53:48'),
('49e6a634-a980-11f0-aff6-005056b8f6d0', '1646d2451f8f11f24cd214759fd6535b', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 04:35:42'),
('4ad32a1d-b489-11f0-9a0c-005056b8f6d0', '7940e651309a1a31097e7734e2b4f960', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 05:37:27'),
('4be65e90-ae63-11f0-9a0c-005056b8f6d0', 'c963ae31f67a991ec33b1cd411161af5', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:49:58'),
('4bf20684-aa6e-11f0-9a0c-005056b8f6d0', 'a5eb1b3774bb8de6a806482e70a0c608', 'status', 'On Process', 'Resolved', 'f30e8b87-d047-4bca-9b34-d223170df87c', '2025-10-16 08:58:34'),
('4f4e96ad-b925-11f0-9a0c-005056b8f6d0', '5332f17963259b72d24f0321737a5302', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 02:24:58'),
('4f635e58-e736-4a7a-aac5-0b544c7a940f', '30092d60a07c771aba052925e846639e', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:08:29'),
('508d0eab-79db-4bfa-aff7-f7e7609127f1', 'a005d2bc5a0c79101ef49cdfd84e88bf', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 10:16:04'),
('511e3518-b2dc-11f0-9a0c-005056b8f6d0', '304700a01748aa660aa5afd0930f1a0e', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:26:42'),
('53a8440e-cfba-45b6-b07b-a9090493ffd4', 'fe2a02ab8e28f3c0f8d2e4f214f8ab23', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:17:32'),
('57cc8486-7621-4b71-9e4a-be89c79f758b', '6a5fda3fdaa7b4addd1f1c1f68ce7d12', 'comment_deleted', 'f250312b-4959-4aa1-a4ff-0e81594394e3', 'deleted', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-25 16:10:27'),
('57ec99e8-0b03-4bc8-aa42-01490cf2f8d9', 'eefbd78e5f2426f6a79494712287f81b', 'status', 'Waiting for Approval', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 07:44:21'),
('5aad5f9b-9199-4787-8557-ff77194d6135', '7671fe71ebe2a276cf49f946914b5082', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-30 15:12:06'),
('5ba93460-b32d-11f0-9a0c-005056b8f6d0', '713616155e9f40ce7a0bf5f1bbd0ca5f', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 12:06:49'),
('5baf1e04-ad7c-11f0-9a0c-005056b8f6d0', '417d339d06500f15a6b7531da0c7cd28', 'status', 'On Process', 'Resolved', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-10-20 06:16:49'),
('5e855629-cead-41d1-b46f-c826bae4e401', '8b5dbcd8e12a7f3ada28f739b9f3c9f0', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 11:40:48'),
('5fd30427-a980-11f0-aff6-005056b8f6d0', '1646d2451f8f11f24cd214759fd6535b', 'status', 'Resolved', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 04:36:19'),
('60c0c739-f643-419f-9623-aefffc34d792', 'dc0fbcaa30d9222f9ec95cce4d040b49', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 07:49:48'),
('614a93d5-b329-11f0-9a0c-005056b8f6d0', 'aa95c56d33fca80adb2f4305b9d22e7d', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 11:38:20'),
('62f029a6-d00e-4e7e-9952-2e949a925bb0', '5356767837cd88ec0215243511c749ab', 'comment_deleted', '4c40b499-e210-48f5-b3ea-a6064648d1a9', 'deleted', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 07:59:47'),
('63c05274-7ddb-402f-8da8-0bc3b54a05dc', '6a5fda3fdaa7b4addd1f1c1f68ce7d12', 'comment_deleted', '17ec65af-6e95-4fe9-a2ac-7f35f5717e0e', 'deleted', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-25 16:08:58'),
('6b7f922d-b30b-11f0-9a0c-005056b8f6d0', '818fa45a1b16cd16b831a6f1b6e63d72', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 08:03:53'),
('6beabaca-ad5c-11f0-9a0c-005056b8f6d0', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:28:12'),
('6dff2a7f-ae63-11f0-9a0c-005056b8f6d0', '47255f8f395045b169b65b16f91ebbe9', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:50:55'),
('73f7cce4-badc-11f0-9a0c-005056b8f6d0', '6a5fda3fdaa7b4addd1f1c1f68ce7d12', 'status', 'New', 'Resolved', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', '2025-11-06 06:48:30'),
('74f438cb-9de0-498d-90ab-b60410580761', '682b8b2fc1c5de91674a1773db6f539d', 'status', 'New', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 05:08:24'),
('7882072b-252a-4e98-b169-68631f4a1478', 'f3a0b4c735c95737d37c23cd33793ae9', 'status', 'New', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-06 03:45:15'),
('794fd297-b0b2-11f0-9a0c-005056b8f6d0', '19d9016c34677e6c668edde9932256a1', 'job_owner', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 08:22:07'),
('79bdda5b-a993-11f0-aff6-005056b8f6d0', 'e568fe0d3564ce271bbb99a9d0f1f6aa', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 06:53:03'),
('7b6a5206-05ca-4fe1-9ee0-b4b3ffeaeead', '8e50c1e61286798d0c25cad94c874699', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:31:56'),
('7e033527-b2dd-11f0-9a0c-005056b8f6d0', 'efda3db7e97b4a86927f55e464fa6562', 'status', 'Resolved', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:35:07'),
('7e6a83d5-af22-11f0-9a0c-005056b8f6d0', 'dbbf9e3d828c7ae3afcc8877b1570cc1', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:38:56'),
('7e9f91e7-1240-4f82-b7b6-b777600422d1', '1646d2451f8f11f24cd214759fd6535b', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 04:35:43'),
('7fe23691-ba16-11f0-9a0c-005056b8f6d0', 'ec78cca5255db3e17e0371382cb93425', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-05 07:11:28'),
('8255458f-28b0-4193-9614-b6053be4e1fc', '632456371e6b634ceb7b30c85cf89ebf', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:32:40'),
('85056394-c3db-4702-b734-2bea9d749691', '19d9016c34677e6c668edde9932256a1', 'status', 'New', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 03:40:28'),
('859e4787-a986-11f0-aff6-005056b8f6d0', '22d7359539ef8e483ba62547e2e5136e', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 05:20:19'),
('864caa0f-ed2d-46fd-a8cf-ea4821efe658', 'b180015710d27a896b70940424c14ee0', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 11:53:21'),
('8763b021-badd-11f0-9a0c-005056b8f6d0', 'fe96c34788500002362380163a086f15', 'status', 'New', 'Resolved', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', '2025-11-06 06:56:12'),
('895855f1-8ecf-4db6-9c2d-57dc2abf5389', '4dd8e701d99aa9dd90edfc7e94dae7eb', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 10:13:34'),
('8a3652fa-d8d8-43f2-bf43-7f75a73cf32d', 'db915fd3678a3c7a9a85d8aa606f02f5', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:46:44'),
('8be4e227-a814-4188-87de-c99736c4d460', '8eafedc8ba6f5fc271822ab67c72b744', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 02:08:50'),
('8c431ab7-b8ab-11f0-9a0c-005056b8f6d0', 'b180015710d27a896b70940424c14ee0', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 11:53:21'),
('8c8d1724-af56-11f0-9a0c-005056b8f6d0', 'b3353d226e75aeda28572314a7abe62f', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:51:33'),
('8db03a70-43f3-4347-badf-e9cea00b13b7', '19d9016c34677e6c668edde9932256a1', 'job_owner', 'Apirak Bangpuk', 'Piti Nithitanabhornkul', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 08:22:07'),
('8e1f9e8e-b4ae-11f0-9a0c-005056b8f6d0', '4dd8e701d99aa9dd90edfc7e94dae7eb', 'status', 'Resolved', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 10:04:11'),
('9077bcfb-ae1e-11f0-9a0c-005056b8f6d0', '0475cf8c871b6d870a03ca8beeba95e4', 'status', 'New', 'Pending', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 01:37:57'),
('937aeb9e-2936-482e-93f5-c600aad4e101', 'efda3db7e97b4a86927f55e464fa6562', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:45:14'),
('942ca470-a804-11f0-aff6-005056b8f6d0', '54ce0ca8f7f731876d95512f0d28debd', 'job_owner', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 07:17:37'),
('945ec504-a909-11f0-aff6-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', 'status', 'Resolved', 'On Process', '2', '2025-10-14 14:25:57'),
('95ea9cd2-af23-11f0-9a0c-005056b8f6d0', 'db915fd3678a3c7a9a85d8aa606f02f5', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 08:46:44'),
('96586a9a-7f7e-4e36-9e40-822b7ddadd29', '713616155e9f40ce7a0bf5f1bbd0ca5f', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 12:06:49'),
('96bff7d2-62d2-47ec-a3fb-f77f6b4219a3', '9439b4fe59f3705e5dd798f99ea78bf7', 'status', 'On Process', 'Resolved', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:02:55'),
('976c1016-af59-11f0-9a0c-005056b8f6d0', '574ec888c60f34c5858cbea206e671b0', 'status', 'On Process', 'Pending', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:13:20'),
('989b85f9-b613-4a4d-9d4a-ebdf8749930c', '2d72ce32ffe570003a84eea4bbc5153e', 'status', 'On Process', 'Resolved', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 02:19:49'),
('996a656d-a993-11f0-aff6-005056b8f6d0', '21e5cc0d2aae7047c2e2bbe663b811e6', 'status', 'On Process', 'Resolved', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-10-15 06:53:56'),
('9bd71dc6-0e03-4885-9cb8-a15812fd9015', '47255f8f395045b169b65b16f91ebbe9', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:50:55'),
('a30f8254-db92-4dbb-9727-248227438553', '5332f17963259b72d24f0321737a5302', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 02:24:58'),
('a37e3bfa-a804-11f0-aff6-005056b8f6d0', '54ce0ca8f7f731876d95512f0d28debd', 'status', 'On Process', 'Resolved', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', '2025-10-13 07:18:03'),
('a9398696-b88a-11f0-9a0c-005056b8f6d0', '67eab0c67c179f262013eaa95eee8d9c', 'status', 'On Process', 'Resolved', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-11-03 07:57:56'),
('a96e5f4a-b8ad-11f0-9a0c-005056b8f6d0', '30092d60a07c771aba052925e846639e', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:08:29'),
('a9f4eb24-af54-11f0-9a0c-005056b8f6d0', 'cbf32b8dd3e4ef748855c23d4b162e8d', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:38:03'),
('aa063ed3-b2db-11f0-9a0c-005056b8f6d0', 'cbf32b8dd3e4ef748855c23d4b162e8d', 'status', 'Resolved', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:22:02'),
('aa8d9446-af55-11f0-9a0c-005056b8f6d0', 'efda3db7e97b4a86927f55e464fa6562', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:45:14'),
('ab867ff9-b640-11f0-9a0c-005056b8f6d0', '9439b4fe59f3705e5dd798f99ea78bf7', 'status', 'On Process', 'Resolved', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:02:55'),
('abf5e456-068c-4b42-9545-52e5fb12fc44', 'ba35446efb9ef49c29fd4cb93181e225', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 08:53:48'),
('ad0097d5-b010-11f0-9a0c-005056b8f6d0', '5eb8ec3a227ec1dab8b5463cb9d26f12', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-23 13:03:55'),
('af07d1bd-b4c1-11f0-9a0c-005056b8f6d0', '2d82b2958ff60d48fee678b5e5cdadbb', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 12:21:07'),
('afbd1da0-b2dd-11f0-9a0c-005056b8f6d0', 'efda3db7e97b4a86927f55e464fa6562', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:36:30'),
('b0ab79ee-aa64-11f0-9a0c-005056b8f6d0', 'dc0fbcaa30d9222f9ec95cce4d040b49', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 07:49:48'),
('b1af1be7-0c8d-4509-84eb-3c819f680028', 'eefbd78e5f2426f6a79494712287f81b', 'job_owner', 'Apirak Bangpuk', 'Tulatorn Yongprayoon', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-28 11:35:38'),
('b1edff68-b5a2-11f0-9a0c-005056b8f6d0', '7671fe71ebe2a276cf49f946914b5082', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-30 15:12:05'),
('b60f56ab-a986-11f0-aff6-005056b8f6d0', '22d7359539ef8e483ba62547e2e5136e', 'status', 'Resolved', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 05:21:41'),
('b681a2e8-b95d-11f0-9a0c-005056b8f6d0', 'a2f4f755e19bf78413b83554349a2bab', 'status', 'On Process', 'Resolved', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-11-04 09:08:43'),
('b7265654-af57-11f0-9a0c-005056b8f6d0', '4526af8066b18a46eb44341c5b187ad8', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:59:54'),
('babd58b2-4ebe-4fb9-b456-e95ecc81ae56', '3f4fcda2cc17020648deacae219f4708', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:34:53'),
('bbb640d1-2375-49d0-aca4-3402c01774ed', 'b3353d226e75aeda28572314a7abe62f', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:51:33'),
('c2e963f0-aa64-11f0-9a0c-005056b8f6d0', '1646d2451f8f11f24cd214759fd6535b', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 07:50:19'),
('c3b66039-b888-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', 'status', 'Waiting for Approval', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 07:44:21'),
('c53126ba-b2dd-11f0-9a0c-005056b8f6d0', 'cbf32b8dd3e4ef748855c23d4b162e8d', 'status', 'Resolved', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:37:06'),
('c53c2341-84b2-40b0-b88f-2977d373d16f', '2d82b2958ff60d48fee678b5e5cdadbb', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 12:21:07'),
('c7f33bb5-a986-11f0-aff6-005056b8f6d0', '22d7359539ef8e483ba62547e2e5136e', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 05:22:11'),
('c89e6ee4-6f7d-4079-89e3-51b91dfab30c', '7940e651309a1a31097e7734e2b4f960', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 05:37:27'),
('c9caeb2a-b2db-11f0-9a0c-005056b8f6d0', 'cbf32b8dd3e4ef748855c23d4b162e8d', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:22:55'),
('ca2fb8cd-ca64-41fa-8005-908f77c62081', 'e6682bbd1a2807550f0b7b0235abd8c8', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 09:41:56'),
('cd5cf924-a8fb-11f0-aff6-005056b8f6d0', '112f5fda773c2b665958457654cba090', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 12:47:20'),
('cf06b6c5-048f-4a4f-9153-84580283e57d', '112f5fda773c2b665958457654cba090', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 12:47:20'),
('d6c24be1-aa37-11f0-9a0c-005056b8f6d0', 'bac7e3042320f2089eecefceee459b07', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 02:28:45'),
('d7239e1c-a827-11f0-aff6-005056b8f6d0', '375fbb2a1a2a398c6ee3fcbba58316cb', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 11:30:02'),
('d7ef8ed5-a8f2-11f0-aff6-005056b8f6d0', '7e24f638b40854acf4a162a469026e9e', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 11:43:12'),
('daa2db7d-bac2-11f0-9a0c-005056b8f6d0', 'f3a0b4c735c95737d37c23cd33793ae9', 'status', 'New', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-06 03:45:15'),
('daa36302-bac2-11f0-9a0c-005056b8f6d0', 'f3a0b4c735c95737d37c23cd33793ae9', 'job_owner', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '6fbca1c7-761f-4027-ba4c-89e04832b717', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-06 03:45:15'),
('dbdd014d-a8ff-11f0-aff6-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 13:16:22'),
('dd56d769-a8f2-11f0-aff6-005056b8f6d0', '7e24f638b40854acf4a162a469026e9e', 'status', 'Resolved', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 11:43:21'),
('ddaa5e9e-b4af-11f0-9a0c-005056b8f6d0', '4dd8e701d99aa9dd90edfc7e94dae7eb', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 10:13:34'),
('de02c55d-b640-11f0-9a0c-005056b8f6d0', '8b3f3eb00f76e9a698bcc04e0243a566', 'status', 'On Process', 'Resolved', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:04:20'),
('de83af81-92ff-4fb3-aa6f-9f8710b894ac', 'ec78cca5255db3e17e0371382cb93425', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-05 07:11:28'),
('df0f1096-d782-4809-841e-c73419ea5506', '097c3183d66a2f98b7f0927512b0895c', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 03:54:25'),
('e33d9baa-b48b-11f0-9a0c-005056b8f6d0', 'bc420db35f715fdc06e983365015152d', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 05:56:02'),
('e494f2b7-b52c-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', 'status', 'On Process', 'Waiting for Approval', '6fbca1c7-761f-4027-ba4c-89e04832b717', '2025-10-30 01:08:50'),
('e548224d-14e1-45d0-bbd2-645e26d1f240', '1646d2451f8f11f24cd214759fd6535b', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 07:50:19'),
('e737d951-b955-11f0-9a0c-005056b8f6d0', '13bfaa55fc6a83ce652f638acc7259b7', 'status', 'On Process', 'Resolved', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-11-04 08:12:49'),
('e87bff12-4f8f-45b9-963a-7308b7f32f55', '637269d63c441e053578d2a383dded3a', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:04:02'),
('e8a9b0a3-b3f1-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', 'status', 'New', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-28 11:33:48'),
('ed7a991b-b8ae-11f0-9a0c-005056b8f6d0', 'fe2a02ab8e28f3c0f8d2e4f214f8ab23', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:17:32'),
('ee8f97be-bdea-11f0-8604-005056b8f6d0', '7eba9a30705d2c1ddc0a1e2d6cac3d96', 'status', 'On Process', 'Resolved', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-11-10 04:08:38'),
('ef43feac-3d8f-4963-bd62-a2727f4488cb', 'e568fe0d3564ce271bbb99a9d0f1f6aa', 'status', 'On Process', 'Resolved', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-10-15 06:53:03'),
('efbfde62-a98f-11f0-aff6-005056b8f6d0', '3641ec530675c2226fc1420f5d02d71e', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 06:27:43'),
('f289614a-bde8-11f0-8604-005056b8f6d0', '097c3183d66a2f98b7f0927512b0895c', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 03:54:25'),
('f3891425-7b89-41a1-9f84-48353acefeba', 'bac7e3042320f2089eecefceee459b07', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 02:28:45'),
('f467c279-a8f2-11f0-aff6-005056b8f6d0', '7e24f638b40854acf4a162a469026e9e', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 11:44:00'),
('f48565de-b955-11f0-9a0c-005056b8f6d0', '611bfaa99199810cb73ce1721f6ea0a0', 'status', 'On Process', 'Resolved', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-11-04 08:13:11'),
('f6334175-d5d9-4305-b9e0-3d3de23099f8', 'c963ae31f67a991ec33b1cd411161af5', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:49:58'),
('f6521a7a-fd6a-485a-b2c7-3078ed9dfa09', '8b3f3eb00f76e9a698bcc04e0243a566', 'status', 'On Process', 'Resolved', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 10:04:20'),
('f7abc7df-a8ff-11f0-aff6-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', 'status', 'Resolved', 'On Process', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 13:17:09'),
('f7d06153-7def-47da-9c82-5eae0c781f21', '0475cf8c871b6d870a03ca8beeba95e4', 'status', 'New', 'Pending', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 01:37:57'),
('f97fdca6-b5ff-11f0-9a0c-005056b8f6d0', '2d72ce32ffe570003a84eea4bbc5153e', 'status', 'On Process', 'Resolved', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', '2025-10-31 02:19:49'),
('fa014928-b2dd-11f0-9a0c-005056b8f6d0', 'cbf32b8dd3e4ef748855c23d4b162e8d', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:38:35'),
('fb7f1a87-b3d1-11f0-9a0c-005056b8f6d0', '19d9016c34677e6c668edde9932256a1', 'status', 'On Process', 'Resolved', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-10-28 07:45:16'),
('fbbc72d2-ba14-11f0-9a0c-005056b8f6d0', '4d0f17400296e43ac2a70f6764cce016', 'status', 'New', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-05 07:00:37'),
('fc69337c-ab37-11f0-9a0c-005056b8f6d0', '82396eb89e2502198fa334db5707c050', 'status', 'On Process', 'Resolved', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-10-17 09:02:20'),
('fc9b51ad-aa38-11f0-9a0c-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', 'status', 'On Process', 'Resolved', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 02:36:58'),
('fe352567-ae62-11f0-9a0c-005056b8f6d0', '350a46a5acbd6776dc3b040297ef322d', 'status', 'On Process', 'Resolved', 'f384c704-5291-4413-8f52-dc25e10b5d4f', '2025-10-21 09:47:47'),
('fe52ceca-b324-11f0-9a0c-005056b8f6d0', '2d72ce32ffe570003a84eea4bbc5153e', 'priority', 'Medium', 'Low', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 11:06:56');

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
-- Dumping data for table `service_ticket_onsite`
--

INSERT INTO `service_ticket_onsite` (`onsite_id`, `ticket_id`, `start_location`, `end_location`, `travel_mode`, `travel_note`, `odometer_start`, `odometer_end`, `note`, `created_at`, `updated_at`) VALUES
('26701835-a900-11f0-aff6-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', 'บริษัท พอยท์ ไอที คอนซัลทิ่ง จํากัด', 'โรงพยาบาลธรรมศาสตร์รังสิต', 'personal_car', '', NULL, NULL, '', '2025-10-14 13:18:27', '2025-10-16 02:36:58'),
('33a427e1-b5a2-11f0-9a0c-005056b8f6d0', '7671fe71ebe2a276cf49f946914b5082', 'บริษัท พอยท์ ไอที คอนซัลทิ่ง จํากัด', 'อารีย์การ์เด้น', 'personal_car', '', NULL, NULL, '', '2025-10-30 15:08:34', NULL),
('464c19e8-ae93-11f0-9a0c-005056b8f6d0', 'dbbf9e3d828c7ae3afcc8877b1570cc1', 'บริษัท พอยท์ ไอที คอนซัลทิ่ง จํากัด', 'เซ็นทรัลลาดพร้าว ', 'personal_car', '', NULL, NULL, '', '2025-10-21 15:33:24', NULL),
('7c4acb78-aa45-11f0-9a0c-005056b8f6d0', 'dc0fbcaa30d9222f9ec95cce4d040b49', 'บริษัท พอยท์ ไอที คอนซัลทิ่ง จํากัด', 'เทศบาลตำบลด่านสำโรง', 'personal_car', '', NULL, NULL, '', '2025-10-16 04:06:26', '2025-10-16 07:44:28'),
('91fd221d-b3f1-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', 'บริษัท พอยท์ ไอที คอนซัลทิ่ง จํากัด', 'สำนักงานส่งกำลังบำรุง (กรุงเทพ)', 'personal_car', '', NULL, NULL, '', '2025-10-28 11:31:23', '2025-10-28 11:33:48'),
('a4e72ac0-b010-11f0-9a0c-005056b8f6d0', '5eb8ec3a227ec1dab8b5463cb9d26f12', 'บริษัท พอยท์ ไอที คอนซัลทิ่ง จํากัด', 'เซ็นทรัลลาดพร้าว ', 'personal_car', '', NULL, NULL, '', '2025-10-23 13:03:42', NULL),
('ac4d03ae-ae1d-11f0-9a0c-005056b8f6d0', 'c963ae31f67a991ec33b1cd411161af5', 'บริษัท พอยท์ ไอที คอนซัลทิ่ง จํากัด', 'เทศบาลตำบลด่านสำโรง', 'personal_car', '', NULL, NULL, '', '2025-10-21 01:31:35', NULL),
('b3727f9b-a967-11f0-aff6-005056b8f6d0', '1646d2451f8f11f24cd214759fd6535b', 'บริษัท พอยท์ ไอที คอนซัลทิ่ง จํากัด', 'เทศบาลตำบลด่านสำโรง', 'personal_car', '', 191164.00, NULL, '', '2025-10-15 01:39:42', '2025-10-15 04:37:40'),
('e1ea14b1-bde8-11f0-8604-005056b8f6d0', '097c3183d66a2f98b7f0927512b0895c', '', '', '', '', NULL, NULL, '', '2025-11-10 03:53:57', NULL);

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
-- Dumping data for table `service_ticket_timeline`
--

INSERT INTO `service_ticket_timeline` (`timeline_id`, `ticket_id`, `order`, `actor`, `action`, `detail`, `attachment`, `location`, `created_at`) VALUES
('01367d99-b08b-11f0-9a0c-005056b8f6d0', '19d9016c34677e6c668edde9932256a1', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-24 03:39:35'),
('0781fbbf-b331-11f0-9a0c-005056b8f6d0', '9547da3d6ad0c6013ae7a098a9db6d61', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-27 12:33:06'),
('07dffdcb-3c4f-4fab-b500-7d9d6c819d27', 'f3a0b4c735c95737d37c23cd33793ae9', 3, 'Apirak Bangpuk', 'เปลี่ยน Job Owner', 'จาก \"Apirak Bangpuk\" เป็น \"Tulatorn Yongprayoon\"', NULL, NULL, '2025-11-06 03:45:15'),
('088123f6-ff4b-404e-8d21-a0eda56cd909', 'efda3db7e97b4a86927f55e464fa6562', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"New\" เป็น \"Resolved\"', NULL, NULL, '2025-10-22 14:45:14'),
('0a2dfc56-a802-11f0-aff6-005056b8f6d0', '54ce0ca8f7f731876d95512f0d28debd', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: New → On Process', NULL, NULL, '2025-10-13 06:59:26'),
('0ad058b9-af5c-11f0-9a0c-005056b8f6d0', '8e50c1e61286798d0c25cad94c874699', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-22 15:30:52'),
('0c3b6536-b95d-11f0-9a0c-005056b8f6d0', 'a2f4f755e19bf78413b83554349a2bab', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-04 09:03:57'),
('0dbbaff6-b0a7-11f0-9a0c-005056b8f6d0', '1fc0b09aaf3f079bb9e4ca9bafd97bde', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-24 07:00:22'),
('0fa940f9-76cc-47da-965b-70a6b569f435', 'a5eb1b3774bb8de6a806482e70a0c608', 2, 'Jiratip vittayanusak', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-16 08:58:34'),
('171fbd72-775f-44e9-b0af-d098ecb71877', '2d82b2958ff60d48fee678b5e5cdadbb', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-29 12:21:07'),
('1d0af279-ad5c-11f0-9a0c-005056b8f6d0', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-20 02:26:00'),
('1e24a9c4-ae8f-4ede-8124-7b523086756b', '682b8b2fc1c5de91674a1773db6f539d', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"New\" เป็น \"On Process\"', NULL, NULL, '2025-11-10 05:08:24'),
('1e5aeb4e-792d-4993-a624-74bdb8e2ba6f', '1646d2451f8f11f24cd214759fd6535b', 6, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-16 07:50:19'),
('1e613710-b0be-11f0-9a0c-005056b8f6d0', 'e3263d7e808e55aedf5b535768fc8e3a', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-24 09:45:28'),
('1eacaec6-b2dd-11f0-9a0c-005056b8f6d0', '632456371e6b634ceb7b30c85cf89ebf', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-27 02:32:27'),
('22a28488-b46f-11f0-9a0c-005056b8f6d0', '519fee5632c4c3a2970bde1aeb2a1c75', 2, 'Piti Nithitanabhornkul', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-10-29 02:30:13'),
('232e5f24-b5a1-11f0-9a0c-005056b8f6d0', '6becc7cc78b8b6b8e8066e923c21d181', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-30 15:00:57'),
('257ccd95-a8d7-11f0-aff6-005056b8f6d0', '7e24f638b40854acf4a162a469026e9e', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-14 08:24:56'),
('2589e803-3c93-4750-86dd-ae3ec91360df', '7671fe71ebe2a276cf49f946914b5082', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-30 15:12:06'),
('2683d6d1-a900-11f0-aff6-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', 4, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: Channel: Office → Onsite', NULL, NULL, '2025-10-14 13:18:27'),
('27ce2c29-bdea-11f0-8604-005056b8f6d0', '7eba9a30705d2c1ddc0a1e2d6cac3d96', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-10 04:03:04'),
('287614e9-ea19-4bd6-86f9-066f57c76a4a', 'cbf32b8dd3e4ef748855c23d4b162e8d', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-22 14:38:03'),
('28faee6b-ad92-11f0-9a0c-005056b8f6d0', 'ba35446efb9ef49c29fd4cb93181e225', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-20 08:52:53'),
('2947638b-b0ce-11f0-9a0c-005056b8f6d0', '8b5dbcd8e12a7f3ada28f739b9f3c9f0', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-24 11:40:19'),
('2b765148-bdf3-11f0-8604-005056b8f6d0', '682b8b2fc1c5de91674a1773db6f539d', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-10 05:07:36'),
('2c260a18-fe92-4c2e-a011-cb1512797b01', '8b3f3eb00f76e9a698bcc04e0243a566', 2, 'Marlee Sawar', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-31 10:04:20'),
('2e2f3363-1e1c-4e37-b864-e5bd89ec5f4c', '4dd8e701d99aa9dd90edfc7e94dae7eb', 3, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-29 10:13:34'),
('2eb7a48b-af54-11f0-9a0c-005056b8f6d0', '3f4fcda2cc17020648deacae219f4708', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-22 14:34:37'),
('2f795d69-a900-11f0-aff6-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', 5, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-10-14 13:18:42'),
('2fc06378-b0ad-11f0-9a0c-005056b8f6d0', '7940e651309a1a31097e7734e2b4f960', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-24 07:44:16'),
('31b42930-1483-48ff-b35e-1ef2c90ee6d5', 'e3263d7e808e55aedf5b535768fc8e3a', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-24 09:46:40'),
('324bca92-a988-11f0-aff6-005056b8f6d0', '21e5cc0d2aae7047c2e2bbe663b811e6', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-15 05:32:19'),
('3251a01e-4ab6-481b-83bb-613c51f5c3c8', '2d72ce32ffe570003a84eea4bbc5153e', 3, 'Marlee Sawar', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-31 02:19:49'),
('325844d1-be1e-11f0-8604-005056b8f6d0', 'c9a4a76087ad93b691fa30f4dda02be0', 2, 'Piti Nithitanabhornkul', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-11-10 10:15:36'),
('33a587cb-b5a2-11f0-9a0c-005056b8f6d0', '7671fe71ebe2a276cf49f946914b5082', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-30 15:08:34'),
('36ccd378-badc-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', 2, 'Pongsan chakranon', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-11-06 06:46:47'),
('37b3b30c-badd-11f0-9a0c-005056b8f6d0', '7718ca773579e9622a2e80901a7484b1', 1, 'Pongsan chakranon (Engineer)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-06 06:53:58'),
('39840664-be1e-11f0-8604-005056b8f6d0', 'a005d2bc5a0c79101ef49cdfd84e88bf', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-10 10:15:48'),
('3afe1776-ad62-11f0-9a0c-005056b8f6d0', '417d339d06500f15a6b7531da0c7cd28', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-20 03:09:47'),
('3b8865c5-ad5c-11f0-9a0c-005056b8f6d0', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', 3, 'Apirak Bangpuk', 'Re-Open Ticket', 'Ticket ถูกเปิดใหม่จากสถานะ \"Resolved\" เป็น \"On Process\"', NULL, NULL, '2025-10-20 02:26:51'),
('3c28a7f0-be1e-11f0-8604-005056b8f6d0', '682b8b2fc1c5de91674a1773db6f539d', 3, 'Piti Nithitanabhornkul', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-11-10 10:15:52'),
('3de8547e-aa46-11f0-9a0c-005056b8f6d0', '47255f8f395045b169b65b16f91ebbe9', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-16 04:11:51'),
('3e5722de-a992-11f0-aff6-005056b8f6d0', 'e568fe0d3564ce271bbb99a9d0f1f6aa', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-15 06:44:14'),
('3f21e1e7-af58-11f0-9a0c-005056b8f6d0', '2f5a45eb2c3897400895d1f8a9f6b5c2', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-22 15:03:42'),
('3f94410e-b329-11f0-9a0c-005056b8f6d0', 'aa95c56d33fca80adb2f4305b9d22e7d', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-27 11:37:24'),
('419e35bd-b2dc-11f0-9a0c-005056b8f6d0', '304700a01748aa660aa5afd0930f1a0e', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-27 02:26:16'),
('424655c0-aa66-11f0-9a0c-005056b8f6d0', 'a5eb1b3774bb8de6a806482e70a0c608', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-16 08:01:02'),
('44cae5c9-ae1b-11f0-9a0c-005056b8f6d0', '0475cf8c871b6d870a03ca8beeba95e4', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-21 01:14:22'),
('45424fcd-a8fd-11f0-aff6-005056b8f6d0', '2bd0058981314c1250b10a592f9af020', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: New → Resolved', NULL, NULL, '2025-10-14 12:57:50'),
('45e9d04f-ad62-11f0-9a0c-005056b8f6d0', '417d339d06500f15a6b7531da0c7cd28', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: วันเริ่มดำเนินการ: 20/10/2025 10:05 → -; วันครบกำหนด: 23/10/2025 10:05 → -', NULL, NULL, '2025-10-20 03:10:06'),
('464c698e-ae93-11f0-9a0c-005056b8f6d0', 'dbbf9e3d828c7ae3afcc8877b1570cc1', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-21 15:33:24'),
('46fab13c-393a-4608-86c3-1a5c8acad0e2', 'e568fe0d3564ce271bbb99a9d0f1f6aa', 3, 'Piti Nithitanabhornkul', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-15 06:53:03'),
('4b13394a-27b2-4d09-949e-7054080d7116', '713616155e9f40ce7a0bf5f1bbd0ca5f', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-27 12:06:49'),
('4b877304-0751-4ac4-9c8c-9a60438de9e4', 'b3353d226e75aeda28572314a7abe62f', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-22 14:51:33'),
('4baa5579-bbb3-11f0-8604-005056b8f6d0', '887ac5a1a781314baf1c0823330b88fe', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-07 08:25:19'),
('4da15625-caec-429f-9ccd-ae490327abc4', '5332f17963259b72d24f0321737a5302', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-11-04 02:24:58'),
('4f7e8ddb-b32d-11f0-9a0c-005056b8f6d0', '713616155e9f40ce7a0bf5f1bbd0ca5f', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-27 12:06:29'),
('4fc423b4-cb02-48b8-89b1-2b828ee57079', '632456371e6b634ceb7b30c85cf89ebf', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-27 02:32:41'),
('506b94bd-d348-4055-8bb1-6949c9fc0951', '112f5fda773c2b665958457654cba090', 3, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-14 12:47:20'),
('51ad6f67-b46c-11f0-9a0c-005056b8f6d0', '519fee5632c4c3a2970bde1aeb2a1c75', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-29 02:10:03'),
('53680c18-a986-11f0-aff6-005056b8f6d0', '22d7359539ef8e483ba62547e2e5136e', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-15 05:18:55'),
('5740f792-d15b-4fad-b7a5-bc7346ee01b4', '3f4fcda2cc17020648deacae219f4708', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"New\" เป็น \"Resolved\"', NULL, NULL, '2025-10-22 14:34:53'),
('588f37b0-b324-11f0-9a0c-005056b8f6d0', '2d72ce32ffe570003a84eea4bbc5153e', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-27 11:02:18'),
('59888fd0-a992-11f0-aff6-005056b8f6d0', 'e568fe0d3564ce271bbb99a9d0f1f6aa', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: รายละเอียด: รพ.สต.บ้านใหม่เชิงเนิน เปลี่ยนรายการจองยานพาหนะทั้งหมดที่เป็นชื่อนางกาญจนา เปลี่ยนเป็นเป็น นางกัญญา วสิกรัตรน์ ครับ → รพ.สต.บ้านใหม่เชิงเนิน เปลี่ยนรายการจองยานพาหนะทั้งหมดที่เป็นชื่อนางกาญจนา อาจศึก เปลี่ยนเป็นเป็น นางกัญญา วสิกรัตรน์ ครับ', NULL, NULL, '2025-10-15 06:44:59'),
('59e53321-ae92-11f0-9a0c-005056b8f6d0', 'db915fd3678a3c7a9a85d8aa606f02f5', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-21 15:26:47'),
('5a6b984d-fcbb-47fe-a462-562fa5b5f56a', '47255f8f395045b169b65b16f91ebbe9', 3, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-21 09:50:55'),
('5b73e3b5-b30b-11f0-9a0c-005056b8f6d0', '818fa45a1b16cd16b831a6f1b6e63d72', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-27 08:03:26'),
('5baf758b-ad7c-11f0-9a0c-005056b8f6d0', '417d339d06500f15a6b7531da0c7cd28', 3, 'Piti Nithitanabhornkul', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-10-20 06:16:49'),
('5ddd42a1-1f37-4d51-a2ad-4279dd7c6b03', '818fa45a1b16cd16b831a6f1b6e63d72', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-27 08:03:53'),
('5fd34603-a980-11f0-aff6-005056b8f6d0', '1646d2451f8f11f24cd214759fd6535b', 4, 'Apirak Bangpuk', 'Re-Open Ticket', 'Ticket ถูกเปิดใหม่จากสถานะ \"Resolved\" เป็น \"On Process\"', NULL, NULL, '2025-10-15 04:36:19'),
('601ee11d-a8ff-11f0-aff6-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-14 13:12:54'),
('65547fbf-af55-11f0-9a0c-005056b8f6d0', 'efda3db7e97b4a86927f55e464fa6562', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-22 14:43:18'),
('688ebc86-bac2-11f0-9a0c-005056b8f6d0', 'f3a0b4c735c95737d37c23cd33793ae9', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-06 03:42:04'),
('68b87181-0dc0-43ba-a279-855e0845cd3b', 'dc0fbcaa30d9222f9ec95cce4d040b49', 3, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-16 07:49:48'),
('6fb67262-4360-447d-8264-1d2442738e58', '304700a01748aa660aa5afd0930f1a0e', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-27 02:26:42'),
('6fe9791a-b0a7-11f0-9a0c-005056b8f6d0', '1fc0b09aaf3f079bb9e4ca9bafd97bde', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: รายละเอียด: ประชุมสรุปโครงสร้างราคา + และสาธิตการใช้งานระบบไฟไหม้ (Fire Alarm)\r\n1. โครงสร้างราคาเมื่อเทียบ IBOC ราคามากกว่า ซึ่ง IBOC พร้อมใช้งานแล้ว และรองรับอุปกรณ์ จำนวน 1000 ตัว \r\n2. ระบบ\r\n     2.1.  ต้องการให้เปลี่ยนหมุดรูปอุปกรณ์ใหม่ เป็นการปักหมุดปกติ สีแดง/สีเทา\r\n     2.2.  หน้าระบบควบคุม >> แผนที่ระบบควบคุม >> เมื่อคลิก Icon ระบบแสดงแจ้งเตือนให้กด ✓ Acknowledge >> เมื่อกดแล้ว คลิกที่ Icon อุปกรณ์ ระบบยังแสดงปุ่มให้กด ✓ Acknowledge เสมอ \r\n     2.3.  เมื่อส่งสัญญาณแจ้งเตือนไฟใหม้ หรือปิดอุปกรณ์  หน้าระบบ Icon ไม่แสดงรูปสัญลักษณ์ไฟไหม้ หรือออฟไลน์ ทันที ต้องกด Refresh Brower ทุกครั้ง รวมถึงต่ออุปกรณ์กลับคืนให้อุปกรณ์เชื่อมต่อปกติ สถานะออนไลน์ไม่แสดงทันที  ควรจะต้องRefresh Brower ทุก 3 - 5 วิ ตลอดเวลา \r\n     2.4. หน้าระบบควบคุม >> แผนที่ระบบควบคุม >> เมื่อมีการแจ้งเตือนไฟไหม้ต้องแสดงเสียงแจ้งเตือน \r\n     2.5. หน้าอุปกรณ์ >> แก้ไขข้อมูลอุปกรณ์ >> กดบันทึก ระบบแจ้งบันทึกสำเร็จ >> เมื่อกดแก้ไข หรือดูข้อมูลระบบไม่ดึงข้อมูลที่เคยกรอกและบันทึกมาแสดง\r\n     2.6. หน้า Map >> แผนที่เป็นคนละตัวกับหน้า หน้าระบบควบคุม >> แผนที่ระบบควบคุม\r\n     2.7. เปลี่ยนทีมโทนสีดำแล้วระบบยังเด้งกลับไปเป็นสีขาว → ประชุมสรุปโครงสร้างราคา + และสาธิตการใช้งานระบบไฟไหม้ (Fire Alarm)\r\n1. โครงสร้างราคาเมื่อเทียบ IBOC ราคามากกว่า ซึ่ง IBOC พร้อมใช้งานแล้ว และรองรับอุปกรณ์ จำนวน 1000 ตัว \r\n2. ระบบ\r\n     2.1.  ต้องการให้เปลี่ยนหมุดรูปอุปกรณ์ใหม่ เป็นการปักหมุดปกติ สีแดง/สีเทา\r\n     2.2.  หน้าระบบควบคุม >> แผนที่ระบบควบคุม >> เมื่อคลิก Icon ระบบแสดงแจ้งเตือนให้กด ✓ Acknowledge >> เมื่อกดแล้ว คลิกที่ Icon อุปกรณ์ ระบบยังแสดงปุ่มให้กด ✓ Acknowledge เสมอ \r\n     2.3.  เมื่อส่งสัญญาณแจ้งเตือนไฟใหม้ หรือปิดอุปกรณ์  หน้าระบบ Icon ไม่แสดงรูปสัญลักษณ์ไฟไหม้ หรือออฟไลน์ ทันที ต้องกด Refresh Brower ทุกครั้ง รวมถึงต่ออุปกรณ์กลับคืนให้อุปกรณ์เชื่อมต่อปกติ สถานะออนไลน์ไม่แสดงทันที  ควรจะต้องRefresh Brower ทุก 3 - 5 วิ ตลอดเวลา \r\n     2.4. หน้าระบบควบคุม >> แผนที่ระบบควบคุม >> เมื่อมีการแจ้งเตือนไฟไหม้ต้องแสดงเสียงแจ้งเตือน \r\n     2.5. หน้าอุปกรณ์ >> แก้ไขข้อมูลอุปกรณ์ >> กดบันทึก ระบบแจ้งบันทึกสำเร็จ >> เมื่อกดแก้ไข หรือดูข้อมูลระบบไม่ดึงข้อมูลที่เคยกรอกและบันทึกมาแสดง\r\n     2.6. หน้า Map >> แผนที่เป็นคนละตัวกับหน้า หน้าระบบควบคุม >> แผนที่ระบบควบคุม\r\n     2.7. เปลี่ยนทีมโทนสีดำแล้วระบบยังเด้งกลับไปเป็นสีขาว\r\n\r\nตามภาพแนบไฟล์ \r\n\r\nอ้างอิงงานเดิมที่ยังไม่เสร็จ : TCK-202510-0025; วันเริ่มดำเนินการ: 24/10/2025 13:24 → -; วันครบกำหนด: 27/10/2025 13:24 → -', NULL, NULL, '2025-10-24 07:03:07'),
('70e9a38a-bc06-41aa-9dc2-44b10e98ce45', 'ba35446efb9ef49c29fd4cb93181e225', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-20 08:53:48'),
('714bd6d2-b517-4d01-9db2-6f431be350d5', '0475cf8c871b6d870a03ca8beeba95e4', 3, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"Pending\" เป็น \"Resolved\"', NULL, NULL, '2025-10-21 08:51:33'),
('72dddb32-12cf-4a25-aadb-359eae8f1af5', 'eefbd78e5f2426f6a79494712287f81b', 5, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"Waiting for Approval\" เป็น \"On Process\"', NULL, NULL, '2025-11-03 07:44:21'),
('73f82577-badc-11f0-9a0c-005056b8f6d0', '6a5fda3fdaa7b4addd1f1c1f68ce7d12', 2, 'Pongsan chakranon', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: New → Resolved', NULL, NULL, '2025-11-06 06:48:30'),
('74d16ed7-ba16-11f0-9a0c-005056b8f6d0', 'ec78cca5255db3e17e0371382cb93425', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-05 07:11:10'),
('76cb77b0-ba13-11f0-9a0c-005056b8f6d0', '4d0f17400296e43ac2a70f6764cce016', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-05 06:49:44'),
('7b24d85d-af56-11f0-9a0c-005056b8f6d0', 'b3353d226e75aeda28572314a7abe62f', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-22 14:51:04'),
('7c4aed8d-aa45-11f0-9a0c-005056b8f6d0', 'dc0fbcaa30d9222f9ec95cce4d040b49', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-16 04:06:26'),
('7c8d7f05-af59-11f0-9a0c-005056b8f6d0', '574ec888c60f34c5858cbea206e671b0', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-22 15:12:35'),
('7e038f9b-b2dd-11f0-9a0c-005056b8f6d0', 'efda3db7e97b4a86927f55e464fa6562', 3, 'Apirak Bangpuk', 'Re-Open Ticket', 'Ticket ถูกเปิดใหม่จากสถานะ \"Resolved\" เป็น \"On Process\"', NULL, NULL, '2025-10-27 02:35:07'),
('811a5679-b8ab-11f0-9a0c-005056b8f6d0', 'b180015710d27a896b70940424c14ee0', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-03 11:53:02'),
('8522185d-badd-11f0-9a0c-005056b8f6d0', 'fe96c34788500002362380163a086f15', 1, 'Pongsan chakranon (Engineer)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-06 06:56:08'),
('859eb266-a986-11f0-aff6-005056b8f6d0', '22d7359539ef8e483ba62547e2e5136e', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: New → Resolved', NULL, NULL, '2025-10-15 05:20:19'),
('8675d699-5cc8-4435-ade5-59e2326c4783', 'db915fd3678a3c7a9a85d8aa606f02f5', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-22 08:46:45'),
('869ec8bc-3747-4cd4-a475-bb6e44996264', '1646d2451f8f11f24cd214759fd6535b', 3, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-15 04:35:43'),
('8765e076-badd-11f0-9a0c-005056b8f6d0', 'fe96c34788500002362380163a086f15', 2, 'Pongsan chakranon', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: New → Resolved', NULL, NULL, '2025-11-06 06:56:12'),
('881d91fb-cfc3-43df-a7a5-ba5166e81b6c', 'eefbd78e5f2426f6a79494712287f81b', 3, 'Apirak Bangpuk', 'เปลี่ยน Job Owner', 'จาก \"Apirak Bangpuk\" เป็น \"Tulatorn Yongprayoon\"', NULL, NULL, '2025-10-28 11:35:38'),
('8845365e-a827-11f0-aff6-005056b8f6d0', '375fbb2a1a2a398c6ee3fcbba58316cb', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-13 11:27:49'),
('89cf0585-b4ae-11f0-9a0c-005056b8f6d0', '4dd8e701d99aa9dd90edfc7e94dae7eb', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-29 10:04:04'),
('8e2c2774-b4ae-11f0-9a0c-005056b8f6d0', '4dd8e701d99aa9dd90edfc7e94dae7eb', 2, 'Apirak Bangpuk', 'Re-Open Ticket', 'Ticket ถูกเปิดใหม่จากสถานะ \"Resolved\" เป็น \"On Process\"', NULL, NULL, '2025-10-29 10:04:11'),
('900f5f19-a980-11f0-aff6-005056b8f6d0', '1646d2451f8f11f24cd214759fd6535b', 5, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: หัวข้อ / Subject: ขอบริการเข้าหน้างานเก็บ requirement ลูกค้าระบบเฝ้าระวังเหตุฉุกเฉินในผู้สูงอายุ เทศบาลตำบลด่านสำโรง → ขอบริการเข้าหน้างานเก็บ Requirement ลูกค้าระบบเฝ้าระวังเหตุฉุกเฉินในผู้สูงอายุ เทศบาลตำบลด่านสำโรง', NULL, NULL, '2025-10-15 04:37:40'),
('91fd8510-b3f1-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-28 11:31:23'),
('93338f21-b8ac-11f0-9a0c-005056b8f6d0', '637269d63c441e053578d2a383dded3a', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-03 12:00:42'),
('944c228e-a804-11f0-aff6-005056b8f6d0', '54ce0ca8f7f731876d95512f0d28debd', 3, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: Job Owner: Apirak Bangpuk → Pongsan chakranon', NULL, NULL, '2025-10-13 07:17:37'),
('945f0a0f-a909-11f0-aff6-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', 6, 'Systems Admin', 'Re-Open Ticket', 'Ticket ถูกเปิดใหม่จากสถานะ \"Resolved\" เป็น \"On Process\"', NULL, NULL, '2025-10-14 14:25:57'),
('996a9b2b-a993-11f0-aff6-005056b8f6d0', '21e5cc0d2aae7047c2e2bbe663b811e6', 2, 'Piti Nithitanabhornkul', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-10-15 06:53:56'),
('99dfaedf-af54-11f0-9a0c-005056b8f6d0', 'cbf32b8dd3e4ef748855c23d4b162e8d', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-22 14:37:36'),
('9bdfe281-e1bf-4f8e-ab64-13b3f9abed03', 'eefbd78e5f2426f6a79494712287f81b', 4, 'Tulatorn Yongprayoon', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Waiting for Approval\"', NULL, NULL, '2025-10-30 01:08:50'),
('9c9f7eb3-badd-11f0-9a0c-005056b8f6d0', '7718ca773579e9622a2e80901a7484b1', 2, 'Pongsan chakranon', 'แก้ไข Ticket', 'เปลี่ยนแปลง: วันเริ่มดำเนินการ: 06/11/2025 13:50 → 11/06/2025 13:50; วันครบกำหนด: 16/11/2025 13:50 → -', NULL, NULL, '2025-11-06 06:56:48'),
('9d4fea66-a97c-11f0-aff6-005056b8f6d0', '1646d2451f8f11f24cd214759fd6535b', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: รายละเอียด: ขอบริการเข้าหน้างานเก็บ requirement ลูกค้าระบบเฝ้าระวังเหตุฉุกเฉินในผู้สูงอายุ เทศบาลตำบลด่านสำโรง\r\nเนื่องด้วยเทศบาลตำบลด่านสำโรง มีการเข้าไปดูงานที่เทศบาลตำบลทับมาเมื่อ สัปดาห์ก่อน เรื่องการนำระบบเฝ้าระวังเหตุฉุกเฉินในผู้สูงอายุ รายการคัดกรองค่าสุขภาพเบื้องต้น → ขอบริการเข้าหน้างานเก็บ Requirement ลูกค้าระบบเฝ้าระวังเหตุฉุกเฉินในผู้สูงอายุ เทศบาลตำบลด่านสำโรง\r\nเนื่องด้วยเทศบาลตำบลด่านสำโรง มีการเข้าไปดูงานที่เทศบาลตำบลทับมาเมื่อ สัปดาห์ก่อน เรื่องการนำระบบเฝ้าระวังเหตุฉุกเฉินในผู้สูงอายุ รายการคัดกรองค่าสุขภาพเบื้องต้น\r\n\r\nลูกค้าเทศบาลด่านสำโรงขอเลื่อนการเข้าตอบคำถามเกี่ยวกับระบบ smart living และ อุปกรณ์ เป็นพรุ่งนี้เช้า เวลา 9:00 น. คะ พี่รบกวนแอมป์ไปแทนพี่นะ พี่โอ๋จะเข้าไปด้วยคะ ลูกค้าจะสรุปรายการอุปกรณ์โครงการทั้งหมดในวันพรุ่งนี้คะ \r\nNote: ถ้ามีประเด็นเรื่องราคาให้โทรหาพี่ก่อนนะ\r\nขอบคุณคะ', NULL, NULL, '2025-10-15 04:09:24'),
('9e532444-b8ad-11f0-9a0c-005056b8f6d0', '30092d60a07c771aba052925e846639e', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-03 12:08:10'),
('9fc69599-a8fb-11f0-aff6-005056b8f6d0', '112f5fda773c2b665958457654cba090', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-14 12:46:03'),
('a25e971a-88eb-41b3-bfc3-3e9abcecda19', '4526af8066b18a46eb44341c5b187ad8', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-22 14:59:54'),
('a37e6885-a804-11f0-aff6-005056b8f6d0', '54ce0ca8f7f731876d95512f0d28debd', 4, 'Pongsan chakranon', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-10-13 07:18:03'),
('a4ecb33f-b010-11f0-9a0c-005056b8f6d0', '5eb8ec3a227ec1dab8b5463cb9d26f12', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-23 13:03:42'),
('a7901527-c6f1-4f23-9afb-7b20b67624cc', '7940e651309a1a31097e7734e2b4f960', 4, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-29 05:37:27'),
('a86ced28-1bce-42b7-b42c-43a16a2dd239', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"New\" เป็น \"Resolved\"', NULL, NULL, '2025-10-20 02:26:37'),
('a8a5caab-b4c1-11f0-9a0c-005056b8f6d0', '2d82b2958ff60d48fee678b5e5cdadbb', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-29 12:20:56'),
('a93899cc-b337-11f0-9a0c-005056b8f6d0', '7940e651309a1a31097e7734e2b4f960', 3, 'Miss Phattraorn Amornophakun', 'แก้ไข Ticket', 'เปลี่ยนแปลง: รายละเอียด: **สรุปวางแผนการ Demo ระบบศูนย์ Benz  ธนบุรีพานิช \r\n1. AI ตรวจสอบวัตถุบนเคาน์เตอร์และโต๊ะ   >> ใช้กล้อง 1 ตัว \r\n2. AI ตรวจสอบเวลาที่ลูกค้าอยู่ในพื้นที่  >> ใช้กล้อง 2 ตัว \r\n3. AI นับจำนวนลูกค้าที่เข้าและออกในโชว์รูม  >> ใช้กล้อง 1 ตัว\r\n4. AI แยกลูกค้าและพนักงาน >> ใช้กล้องข้อที่ 2. \r\n5. AI อ่านป้ายทะเบียนรถ >> ใช้กล้อง 1 ตัว\r\n6. AI ตรวจจับยานพาหนะที่เข้ามาและออกไปจากบริเวณศูนย์บริการ >> ใช้กล้องเดียวกับข้อ 5. \r\n7. AI นับจำนวนรถที่อยู่ในพื้นที่ >> ใช้กล้อง 1 ตัว ไม่มีอะไรบดบังพื้นที่  >> ใช้กล้อง 2 ตัว \r\n8. AI ค้นหาดูด้วยภาษาพูด (อาทิ รถสีแดงป้ายทะเบียน 5กก 5921) >> ดึง Ifream จาก PointIT ไปใช้ในการค้นหา \r\n9. ลงทะเบียนตรวจสอบใบหน้าพนักงาน พร้อมแจ้งเตือนไปยังระบบบริหารจัดการส่วนกลาง เมื่อ Scan ใบหน้าออกนอกพื้นที่และมีการกลับเข้ามาอีกรอบ >> ระบบแพลตฟอร์ม \r\n\r\n**เพิ่มเติม \r\nนัด Demo : 23 พฤศจิกายน 2568  (วันอาทิตย์)\r\nใช้ NVR Hikvision AI \r\nพี่ซีน คุยกับทาง Vender ขอคู่มือการดึง API ของกล้อง Hikvision (ซีนดำเนินการ Register ที่ https://tpp.hikvision.com/ เพื่อขอ API Doc รอ Approve 3 day)\r\nแอมป์ ทำ UX/UI สำหรับขึ้นหน้าบ้าน \r\nพี่แจ็ค เตรียม Server AI , จัดเตรียมกล้อง CCTV อ่านป้ายทะเบียน → **สรุปวางแผนการ Demo ระบบศูนย์ Benz  ธนบุรีพานิช \r\n1. AI ตรวจสอบวัตถุบนเคาน์เตอร์และโต๊ะ   >> ใช้กล้อง 1 ตัว \r\n2. AI ตรวจสอบเวลาที่ลูกค้าอยู่ในพื้นที่  >> ใช้กล้อง 2 ตัว \r\n3. AI นับจำนวนลูกค้าที่เข้าและออกในโชว์รูม  >> ใช้กล้อง 1 ตัว\r\n4. AI แยกลูกค้าและพนักงาน >> ใช้กล้องข้อที่ 2. \r\n5. AI อ่านป้ายทะเบียนรถ >> ใช้กล้อง 1 ตัว\r\n6. AI ตรวจจับยานพาหนะที่เข้ามาและออกไปจากบริเวณศูนย์บริการ >> ใช้กล้องเดียวกับข้อ 5. \r\n7. AI นับจำนวนรถที่อยู่ในพื้นที่ >> ใช้กล้อง 1 ตัว ไม่มีอะไรบดบังพื้นที่  >> ใช้กล้อง 2 ตัว \r\n8. AI ค้นหาดูด้วยภาษาพูด (อาทิ รถสีแดงป้ายทะเบียน 5กก 5921) >> ดึง Ifream จาก PointIT ไปใช้ในการค้นหา \r\n9. ลงทะเบียนตรวจสอบใบหน้าพนักงาน พร้อมแจ้งเตือนไปยังระบบบริหารจัดการส่วนกลาง เมื่อ Scan ใบหน้าออกนอกพื้นที่และมีการกลับเข้ามาอีกรอบ >> ระบบแพลตฟอร์ม \r\n\r\n**เพิ่มเติม \r\nนัด Demo : 23 พฤศจิกายน 2568  (วันอาทิตย์)\r\nใช้ NVR Hikvision AI \r\nพี่ซีน คุยกับทาง Vender ขอคู่มือการดึง API ของกล้อง Hikvision (ซีนดำเนินการ Register ที่ https://tpp.hikvision.com/ เพื่อขอ API Doc รอ Approve 3 day)\r\nแอมป์ ทำ UX/UI สำหรับขึ้นหน้าบ้าน \r\nพี่แจ็ค เตรียม Server AI , จัดเตรียมกล้อง CCTV อ่านป้ายทะเบียน\r\n\r\nMeeting 27-10-2025\r\nTeam Thonburi : P\'Pop / P\'Sit / N\'Tom\r\nTeam Point IT : Zeen / Amp / Zeen น้อย\r\n\r\n1. ขอภาพมุมกล้องจากพี่สิทธิ์ เพื่อระบุจุดที่ต้องการดึงภาพมาทำ AI >> พี่สิทธิ์ส่งมาให้แล้ว\r\n2. ซีนน้อยกำหนดจุดที่จะดึงภาพมาทำ AI\r\n3. ซีนน้อยดึง Link RTSP มาเข้า AI\r\n4 ซีนส่งแผนการดำเนินการ Demo ให้พี่ป๊อป\r\n5. กำหนดวันลงระบบ Demo 23-11-2025; วันเริ่มดำเนินการ: 24/10/2025 14:13 → -; วันครบกำหนด: 31/10/2025 14:13 → -', NULL, NULL, '2025-10-27 13:20:36'),
('a93cff1a-b88a-11f0-9a0c-005056b8f6d0', '67eab0c67c179f262013eaa95eee8d9c', 2, 'Piti Nithitanabhornkul', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-11-03 07:57:56'),
('a963434f-b324-11f0-9a0c-005056b8f6d0', '8b3f3eb00f76e9a698bcc04e0243a566', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-27 11:04:34'),
('a98ec6c7-af57-11f0-9a0c-005056b8f6d0', '4526af8066b18a46eb44341c5b187ad8', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-22 14:59:31'),
('aa297598-b2db-11f0-9a0c-005056b8f6d0', 'cbf32b8dd3e4ef748855c23d4b162e8d', 3, 'Apirak Bangpuk', 'Re-Open Ticket', 'Ticket ถูกเปิดใหม่จากสถานะ \"Resolved\" เป็น \"On Process\"', NULL, NULL, '2025-10-27 02:22:02'),
('ab5ca121-b922-11f0-9a0c-005056b8f6d0', '8eafedc8ba6f5fc271822ab67c72b744', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-04 02:06:04'),
('abb4a128-4c24-458b-81cd-7e4a18092323', 'c963ae31f67a991ec33b1cd411161af5', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-21 09:49:58'),
('ac4fa4b3-ae1d-11f0-9a0c-005056b8f6d0', 'c963ae31f67a991ec33b1cd411161af5', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-21 01:31:35'),
('ad64f807-d66e-4223-b051-1a35da854bfa', 'e6682bbd1a2807550f0b7b0235abd8c8', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-20 09:41:56'),
('ae541cc5-a801-11f0-aff6-005056b8f6d0', '54ce0ca8f7f731876d95512f0d28debd', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-13 06:56:52'),
('afbddd56-b2dd-11f0-9a0c-005056b8f6d0', 'efda3db7e97b4a86927f55e464fa6562', 4, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: หัวข้อ / Subject: ทำหน้ารายการ Dashboard Google Studio สำหรับสรุปจำนวนลูกค้าเยี่ยมชมบูธ → ออกแบบ Dashboard ด้วย Google Looker Studio (หรือ Google Data Studio) สำหรับแสดงข้อมูลการเยี่ยมชมบูธ; รายละเอียด: ทำหน้ารายการ Dashboard Google Studio สำหรับสรุปจำนวนลูกค้าเยี่ยมชมบูธ \r\nLink : https://lookerstudio.google.com/u/0/reporting/cb517742-af4f-4a4e-89d2-5a20ead3948c/page/7Q6IF\r\nData : https://docs.google.com/spreadsheets/d/1zlIOSSxSTzgPGmPq4dET6lcLrYHrEnTtmh2cC4Gu_UA/edit?gid=0#gid=0 → ออกแบบ Dashboard ด้วย Google Looker Studio (หรือ Google Data Studio) สำหรับแสดงข้อมูลการเยี่ยมชมบูธ\r\nLink : https://lookerstudio.google.com/u/0/reporting/cb517742-af4f-4a4e-89d2-5a20ead3948c/page/7Q6IF\r\nData : https://docs.google.com/spreadsheets/d/1zlIOSSxSTzgPGmPq4dET6lcLrYHrEnTtmh2cC4Gu_UA/edit?gid=0#gid=0; สถานะ: On Process → Resolved; Service Category: Data/Analytics → Business Intelligence & Analytics Services (บริการด้านการวิเคราะห์ข้อมูลทางธุรกิจ); Category: BI/Dashboard → Data Visualization (การแสดงผลข้อมูล); Sub Category: Report incorrect → Dashboard Development (การพัฒนาแดชบอร์ด); วันเริ่มดำเนินการ: 22/10/2025 10:45 → -; วันครบกำหนด: 25/10/2025 10:45 → -', NULL, NULL, '2025-10-27 02:36:30'),
('b025bc77-ae61-11f0-9a0c-005056b8f6d0', '5356767837cd88ec0215243511c749ab', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-21 09:38:27'),
('b0965079-b94e-11f0-9a0c-005056b8f6d0', '13bfaa55fc6a83ce652f638acc7259b7', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-04 07:21:11'),
('b109769a-b4ad-11f0-9a0c-005056b8f6d0', 'ec68368ab063a1cf203f014f7975d6b1', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-29 09:58:00'),
('b1305c0d-ab32-11f0-9a0c-005056b8f6d0', '82396eb89e2502198fa334db5707c050', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-17 08:24:26'),
('b1396a7c-aa7b-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-16 10:34:27'),
('b2ec2414-0a1f-46d8-bd3c-c029c3db450e', '574ec888c60f34c5858cbea206e671b0', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Pending\"', NULL, NULL, '2025-10-22 15:13:20'),
('b372970b-a967-11f0-aff6-005056b8f6d0', '1646d2451f8f11f24cd214759fd6535b', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-15 01:39:42'),
('b39a63d6-a8fb-11f0-aff6-005056b8f6d0', '112f5fda773c2b665958457654cba090', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: ผู้แจ้ง: Apirak Bangpuk → Miss Phattraorn Amornophakun', NULL, NULL, '2025-10-14 12:46:37'),
('b39db83e-2e12-4209-b3a8-02ef6ced93d4', '9547da3d6ad0c6013ae7a098a9db6d61', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-29 05:36:52'),
('b62bc6c9-a986-11f0-aff6-005056b8f6d0', '22d7359539ef8e483ba62547e2e5136e', 3, 'Apirak Bangpuk', 'Re-Open Ticket', 'Ticket ถูกเปิดใหม่จากสถานะ \"Resolved\" เป็น \"On Process\"', NULL, NULL, '2025-10-15 05:21:41'),
('b68218c3-b95d-11f0-9a0c-005056b8f6d0', 'a2f4f755e19bf78413b83554349a2bab', 2, 'Piti Nithitanabhornkul', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-11-04 09:08:43'),
('b83c70f4-b924-11f0-9a0c-005056b8f6d0', '5332f17963259b72d24f0321737a5302', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-04 02:20:45'),
('bcb92b45-a9c8-4387-aec0-e6008e784de6', 'aa95c56d33fca80adb2f4305b9d22e7d', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-27 11:38:20'),
('bd054345-b1bc-11f0-9a0c-005056b8f6d0', '6a5fda3fdaa7b4addd1f1c1f68ce7d12', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-25 16:08:08'),
('c158b154-a986-11f0-aff6-005056b8f6d0', '22d7359539ef8e483ba62547e2e5136e', 4, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: Ticket Type: Service → Change', NULL, NULL, '2025-10-15 05:22:00'),
('c1f5abd9-aa37-11f0-9a0c-005056b8f6d0', 'bac7e3042320f2089eecefceee459b07', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-16 02:28:10'),
('c22c8130-d42c-4611-818e-b729e80ad824', '8b5dbcd8e12a7f3ada28f739b9f3c9f0', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-24 11:40:48'),
('c2500cec-ae62-11f0-9a0c-005056b8f6d0', '350a46a5acbd6776dc3b040297ef322d', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-21 09:46:07'),
('c391bac2-4991-4b50-8e61-c27070cc95e0', '8eafedc8ba6f5fc271822ab67c72b744', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-11-04 02:08:50'),
('c3c2e70b-a8fc-11f0-aff6-005056b8f6d0', '2bd0058981314c1250b10a592f9af020', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-14 12:54:13'),
('c53175bd-b2dd-11f0-9a0c-005056b8f6d0', 'cbf32b8dd3e4ef748855c23d4b162e8d', 5, 'Apirak Bangpuk', 'Re-Open Ticket', 'Ticket ถูกเปิดใหม่จากสถานะ \"Resolved\" เป็น \"On Process\"', NULL, NULL, '2025-10-27 02:37:06'),
('c773fba1-b8ae-11f0-9a0c-005056b8f6d0', 'fe2a02ab8e28f3c0f8d2e4f214f8ab23', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-03 12:16:28'),
('c811ebb0-a986-11f0-aff6-005056b8f6d0', '22d7359539ef8e483ba62547e2e5136e', 5, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-10-15 05:22:11'),
('c9738f04-c400-4012-83b1-460a3a511775', 'bac7e3042320f2089eecefceee459b07', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"New\" เป็น \"Resolved\"', NULL, NULL, '2025-10-16 02:28:45'),
('ca0e2266-b2db-11f0-9a0c-005056b8f6d0', 'cbf32b8dd3e4ef748855c23d4b162e8d', 4, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved; Service Category: Network → service_category; Category: IP Management → Access Control (การควบคุมการเข้าถึง); Sub Category: DHCP/DNS/IPAM → Zero Trust Network Access (ZTNA) (การเข้าถึงเครือข่ายแบบ Zero Trust); วันเริ่มดำเนินการ: 22/10/2025 10:35 → -; วันครบกำหนด: 25/10/2025 10:35 → -', NULL, NULL, '2025-10-27 02:22:55'),
('cc275764-9b29-4698-a5e7-dc78a67bf64b', '8e50c1e61286798d0c25cad94c874699', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-22 15:31:56'),
('cd19e5c6-aa46-11f0-9a0c-005056b8f6d0', '47255f8f395045b169b65b16f91ebbe9', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: วันเริ่มดำเนินการ: 30/11/-0001 00:00 → -; วันครบกำหนด: 30/11/-0001 00:00 → -', NULL, NULL, '2025-10-16 04:15:51'),
('cd4caf40-24f2-466c-bc32-a6ad1b7689b0', 'bc420db35f715fdc06e983365015152d', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-29 05:56:02'),
('d089410b-ad98-11f0-9a0c-005056b8f6d0', 'e6682bbd1a2807550f0b7b0235abd8c8', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-20 09:40:31'),
('d0b871e9-b332-11f0-9a0c-005056b8f6d0', '9439b4fe59f3705e5dd798f99ea78bf7', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-27 12:45:53'),
('d14a9442-a98f-11f0-aff6-005056b8f6d0', '3641ec530675c2226fc1420f5d02d71e', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-15 06:26:52'),
('d1c058c0-ab23-11f0-9a0c-005056b8f6d0', 'c3fae15b8715db998f7865db7e4e89ec', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-17 06:37:59'),
('d213c56e-b336-11f0-9a0c-005056b8f6d0', '7940e651309a1a31097e7734e2b4f960', 2, 'Miss Phattraorn Amornophakun', 'แก้ไข Ticket', 'เปลี่ยนแปลง: รายละเอียด: **สรุปวางแผนการ Demo ระบบศูนย์ Benz  ธนบุรีพานิช \r\n1. AI ตรวจสอบวัตถุบนเคาน์เตอร์และโต๊ะ   >> ใช้กล้อง 1 ตัว \r\n2. AI ตรวจสอบเวลาที่ลูกค้าอยู่ในพื้นที่  >> ใช้กล้อง 2 ตัว \r\n3. AI นับจำนวนลูกค้าที่เข้าและออกในโชว์รูม  >> ใช้กล้อง 1 ตัว\r\n4. AI แยกลูกค้าและพนักงาพ >> ใช้กล้องข้อที่ 2. \r\n5. AI อ่านป้ายทะเบียนรถ >> ใช้กล้อง 1 ตัว\r\n6. AI ตรวจจับยานพาหนะที่เข้ามาและออกไปจากบริเวณศูนย์บริการ >> ใช้กล้องเดียวกับข้อ 5. \r\n7. AI นับจำนวนรถที่อยู่ในพื้นที่ >> ใช้กล้อง 1 ตัว ไม่มีอะไรบดบังพื้นที่  >> ใช้กล้อง 2 ตัว \r\n8. AI ค้นหาดูด้วยภาษาพูด (อาทิ รถสีแดงป้ายทะเบียน 5กก 5921) >> ดึง Ifream จาก Point ไปใช้ในการค้นหา \r\n9. ลงทะเบียนตรวจสอบใบหน้าพนักงาน พร้อมแจ้งเตือนไปยังระบบบริหารจัดการกลาง เมื่อ Scan ใบหน้าออกนอกพื้นที่และมีการกลับเข้ามาอีกรอบ >> ระบบแพลตฟอร์ม \r\n\r\n**เพิ่มเติม \r\nนัด Demo : 23 พฤษจิกายน 2568  (วันอาทิตย์)\r\nใช้ NVR Hikvision AI \r\nพี่ซีน คุยกับทาง Vender ขอคู่มือการดึง API ของกล้อง Hikvision\r\nแอมป์ ทำ UX/UI สำหรับขึ้นหน้าบ้าน \r\nพี่แจ็ค เตรียม Server AI , จัดเตรียมกล้อง CCTV → **สรุปวางแผนการ Demo ระบบศูนย์ Benz  ธนบุรีพานิช \r\n1. AI ตรวจสอบวัตถุบนเคาน์เตอร์และโต๊ะ   >> ใช้กล้อง 1 ตัว \r\n2. AI ตรวจสอบเวลาที่ลูกค้าอยู่ในพื้นที่  >> ใช้กล้อง 2 ตัว \r\n3. AI นับจำนวนลูกค้าที่เข้าและออกในโชว์รูม  >> ใช้กล้อง 1 ตัว\r\n4. AI แยกลูกค้าและพนักงาน >> ใช้กล้องข้อที่ 2. \r\n5. AI อ่านป้ายทะเบียนรถ >> ใช้กล้อง 1 ตัว\r\n6. AI ตรวจจับยานพาหนะที่เข้ามาและออกไปจากบริเวณศูนย์บริการ >> ใช้กล้องเดียวกับข้อ 5. \r\n7. AI นับจำนวนรถที่อยู่ในพื้นที่ >> ใช้กล้อง 1 ตัว ไม่มีอะไรบดบังพื้นที่  >> ใช้กล้อง 2 ตัว \r\n8. AI ค้นหาดูด้วยภาษาพูด (อาทิ รถสีแดงป้ายทะเบียน 5กก 5921) >> ดึง Ifream จาก PointIT ไปใช้ในการค้นหา \r\n9. ลงทะเบียนตรวจสอบใบหน้าพนักงาน พร้อมแจ้งเตือนไปยังระบบบริหารจัดการส่วนกลาง เมื่อ Scan ใบหน้าออกนอกพื้นที่และมีการกลับเข้ามาอีกรอบ >> ระบบแพลตฟอร์ม \r\n\r\n**เพิ่มเติม \r\nนัด Demo : 23 พฤศจิกายน 2568  (วันอาทิตย์)\r\nใช้ NVR Hikvision AI \r\nพี่ซีน คุยกับทาง Vender ขอคู่มือการดึง API ของกล้อง Hikvision (ซีนดำเนินการ Register ที่ https://tpp.hikvision.com/ เพื่อขอ API Doc รอ Approve 3 day)\r\nแอมป์ ทำ UX/UI สำหรับขึ้นหน้าบ้าน \r\nพี่แจ็ค เตรียม Server AI , จัดเตรียมกล้อง CCTV อ่านป้ายทะเบียน; วันเริ่มดำเนินการ: 24/10/2025 14:13 → -; วันครบกำหนด: 31/10/2025 14:13 → -', NULL, NULL, '2025-10-27 13:14:35'),
('d56371e2-7549-460b-be34-8e3f602e8793', 'fe2a02ab8e28f3c0f8d2e4f214f8ab23', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"New\" เป็น \"Resolved\"', NULL, NULL, '2025-11-03 12:17:32'),
('d74acd08-a827-11f0-aff6-005056b8f6d0', '375fbb2a1a2a398c6ee3fcbba58316cb', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-10-13 11:30:02'),
('d7bae86f-ea78-4f78-937e-2356736f5a22', 'b180015710d27a896b70940424c14ee0', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-11-03 11:53:21'),
('d7efe46c-a8f2-11f0-aff6-005056b8f6d0', '7e24f638b40854acf4a162a469026e9e', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: New → Resolved', NULL, NULL, '2025-10-14 11:43:12'),
('db3a2ab4-05e4-4d24-a640-8d0429472d0f', '19d9016c34677e6c668edde9932256a1', 3, 'Apirak Bangpuk', 'เปลี่ยน Job Owner', 'จาก \"Apirak Bangpuk\" เป็น \"Piti Nithitanabhornkul\"', NULL, NULL, '2025-10-24 08:22:07'),
('db60216b-4e88-4689-9d71-68b9c3bee49c', '9439b4fe59f3705e5dd798f99ea78bf7', 2, 'Marlee Sawar', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-31 10:02:55'),
('dbcbdfe7-d24b-444d-9b6d-8d1d47118e2b', '7e24f638b40854acf4a162a469026e9e', 4, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-14 11:44:00'),
('dbdd3b09-a8ff-11f0-aff6-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-10-14 13:16:22'),
('dc539a26-59bd-4e4b-99f3-d224b7334867', '0475cf8c871b6d870a03ca8beeba95e4', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"New\" เป็น \"Pending\"', NULL, NULL, '2025-10-21 01:37:57'),
('dd572cb0-a8f2-11f0-aff6-005056b8f6d0', '7e24f638b40854acf4a162a469026e9e', 3, 'Apirak Bangpuk', 'Re-Open Ticket', 'Ticket ถูกเปิดใหม่จากสถานะ \"Resolved\" เป็น \"On Process\"', NULL, NULL, '2025-10-14 11:43:21'),
('dd98b29a-a962-4a47-896c-e8a21ba4d9ce', '19d9016c34677e6c668edde9932256a1', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"New\" เป็น \"On Process\"', NULL, NULL, '2025-10-24 03:40:28'),
('dd9e6f2b-ae62-4786-bb7d-7d982adba677', '097c3183d66a2f98b7f0927512b0895c', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-11-10 03:54:25'),
('e0a8cf30-7630-4d75-8554-74880875ec6f', '4d0f17400296e43ac2a70f6764cce016', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"New\" เป็น \"Resolved\"', NULL, NULL, '2025-11-05 07:00:37'),
('e0f06589-8b75-4355-b943-82934468a68c', '637269d63c441e053578d2a383dded3a', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-11-03 12:04:02'),
('e1770aca-d4e4-427f-9036-a67891f6be7a', 'ec78cca5255db3e17e0371382cb93425', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"New\" เป็น \"Resolved\"', NULL, NULL, '2025-11-05 07:11:28'),
('e1ea3256-bde8-11f0-8604-005056b8f6d0', '097c3183d66a2f98b7f0927512b0895c', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-10 03:53:57'),
('e2410b4e-b483-11f0-9a0c-005056b8f6d0', 'bc420db35f715fdc06e983365015152d', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-10-29 04:58:44'),
('e4e6484b-acf9-4097-ab50-49a295685dd7', 'f3a0b4c735c95737d37c23cd33793ae9', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"New\" เป็น \"On Process\"', NULL, NULL, '2025-11-06 03:45:15'),
('e73be043-b955-11f0-9a0c-005056b8f6d0', '13bfaa55fc6a83ce652f638acc7259b7', 2, 'Piti Nithitanabhornkul', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-11-04 08:12:49'),
('e7fd333a-df81-42aa-9bd3-1eb51e1acf70', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', 4, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-20 02:28:12'),
('e85bd653-7a25-4f0c-9f2b-923cfb2bd7aa', 'dbbf9e3d828c7ae3afcc8877b1570cc1', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-22 08:38:56'),
('e8b0584b-b3f1-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: New → On Process; วันเริ่มดำเนินการ: 28/10/2025 07:30 → -; วันครบกำหนด: 28/10/2025 18:22 → -', NULL, NULL, '2025-10-28 11:33:48'),
('ebaaccc9-3cf5-492b-bc97-d8d999f59217', 'a005d2bc5a0c79101ef49cdfd84e88bf', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-11-10 10:16:04'),
('ec6da189-be1a-11f0-8604-005056b8f6d0', 'c9a4a76087ad93b691fa30f4dda02be0', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-10 09:52:10'),
('ee8ffa37-bdea-11f0-8604-005056b8f6d0', '7eba9a30705d2c1ddc0a1e2d6cac3d96', 2, 'Piti Nithitanabhornkul', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-11-10 04:08:38'),
('efc201d3-ea72-40ca-9175-5a34bd9ba6a7', 'c3fae15b8715db998f7865db7e4e89ec', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-10-17 06:40:31'),
('efc89054-a98f-11f0-aff6-005056b8f6d0', '3641ec530675c2226fc1420f5d02d71e', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-10-15 06:27:43'),
('f015d507-b925-11f0-9a0c-005056b8f6d0', '611bfaa99199810cb73ce1721f6ea0a0', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-04 02:29:28'),
('f1c17139-b889-11f0-9a0c-005056b8f6d0', '67eab0c67c179f262013eaa95eee8d9c', 1, 'Apirak Bangpuk (Account Management)', 'สร้าง Ticket', 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น', NULL, 'Portal', '2025-11-03 07:52:48'),
('f2096729-aa63-11f0-9a0c-005056b8f6d0', 'dc0fbcaa30d9222f9ec95cce4d040b49', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: วันเริ่มดำเนินการ: 30/11/-0001 00:00 → 10/12/2025 11:00; วันครบกำหนด: 30/11/-0001 00:00 → 10/12/2025 12:00', NULL, NULL, '2025-10-16 07:44:28'),
('f485df82-b955-11f0-9a0c-005056b8f6d0', '611bfaa99199810cb73ce1721f6ea0a0', 2, 'Piti Nithitanabhornkul', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-11-04 08:13:11'),
('f7cc3a04-a8ff-11f0-aff6-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', 3, 'Apirak Bangpuk', 'Re-Open Ticket', 'Ticket ถูกเปิดใหม่จากสถานะ \"Resolved\" เป็น \"On Process\"', NULL, NULL, '2025-10-14 13:17:09'),
('f929dc68-b375-43c4-8a4d-e384b6e7f3ce', '30092d60a07c771aba052925e846639e', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"On Process\" เป็น \"Resolved\"', NULL, NULL, '2025-11-03 12:08:29'),
('f9345182-db72-4c32-a78b-2b3e895ad6fe', '5eb8ec3a227ec1dab8b5463cb9d26f12', 2, 'Apirak Bangpuk', 'เปลี่ยนสถานะ Ticket', 'จาก \"New\" เป็น \"Resolved\"', NULL, NULL, '2025-10-23 13:03:55');
INSERT INTO `service_ticket_timeline` (`timeline_id`, `ticket_id`, `order`, `actor`, `action`, `detail`, `attachment`, `location`, `created_at`) VALUES
('fa21453b-b2dd-11f0-9a0c-005056b8f6d0', 'cbf32b8dd3e4ef748855c23d4b162e8d', 6, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: หัวข้อ / Subject: Setup Pubild IP สำหรับรับ-ส่ง API ข้อมูลจากกล้อง CCTV เครื่อง Server (VM) ใหม่ → การตั้งค่า Cloudflare Tunnels สถาปัตยกรรมแบบ Zero Trust สำหรับเชื่อมต่อกล้อง CCTV; รายละเอียด: Setup Pubild IP สำหรับรับ-ส่ง API ข้อมูลจากกล้อง CCTV เครื่อง Server (VM) ใหม่ → การตั้งค่า Cloudflare Tunnels สถาปัตยกรรมแบบ Zero Trust สำหรับเชื่อมต่อกล้อง CCTV; สถานะ: On Process → Resolved; Impact: Department → Application; Service Category: service_category → Security (ความปลอดภัย); วันเริ่มดำเนินการ: 22/10/2025 10:35 → -; วันครบกำหนด: 25/10/2025 10:35 → -', NULL, NULL, '2025-10-27 02:38:35'),
('fb84e7dd-b3d1-11f0-9a0c-005056b8f6d0', '19d9016c34677e6c668edde9932256a1', 4, 'Piti Nithitanabhornkul', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-10-28 07:45:16'),
('fc698368-ab37-11f0-9a0c-005056b8f6d0', '82396eb89e2502198fa334db5707c050', 2, 'Piti Nithitanabhornkul', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-10-17 09:02:20'),
('fcc77619-aa38-11f0-9a0c-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', 7, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved; วันเริ่มดำเนินการ: 14/10/2025 15:03 → -; วันครบกำหนด: 17/10/2025 15:03 → -', NULL, NULL, '2025-10-16 02:36:58'),
('fe4221ab-ae62-11f0-9a0c-005056b8f6d0', '350a46a5acbd6776dc3b040297ef322d', 2, 'Piti Nithitanabhornkul', 'แก้ไข Ticket', 'เปลี่ยนแปลง: สถานะ: On Process → Resolved', NULL, NULL, '2025-10-21 09:47:47'),
('fe61a94f-b324-11f0-9a0c-005056b8f6d0', '2d72ce32ffe570003a84eea4bbc5153e', 2, 'Apirak Bangpuk', 'แก้ไข Ticket', 'เปลี่ยนแปลง: Priority: Medium → Low; วันเริ่มดำเนินการ: 27/10/2025 17:53 → -; วันครบกำหนด: 01/11/2025 17:53 → 11/03/2025 17:53', NULL, NULL, '2025-10-27 11:06:57');

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
-- Dumping data for table `service_ticket_watchers`
--

INSERT INTO `service_ticket_watchers` (`watcher_id`, `ticket_id`, `user_id`, `added_by`, `added_at`) VALUES
('0132884c-b08b-11f0-9a0c-005056b8f6d0', '19d9016c34677e6c668edde9932256a1', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 03:39:35'),
('07552f05-b331-11f0-9a0c-005056b8f6d0', '9547da3d6ad0c6013ae7a098a9db6d61', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 12:33:06'),
('07554405-b331-11f0-9a0c-005056b8f6d0', '9547da3d6ad0c6013ae7a098a9db6d61', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 12:33:06'),
('07556188-b331-11f0-9a0c-005056b8f6d0', '9547da3d6ad0c6013ae7a098a9db6d61', '8c782887-8fd3-4f99-ac27-63054a8a1942', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 12:33:06'),
('0aa6d609-af5c-11f0-9a0c-005056b8f6d0', '8e50c1e61286798d0c25cad94c874699', 'b27b56e5-6f28-4d30-8add-4bddafa38841', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:30:52'),
('0aa6f86f-af5c-11f0-9a0c-005056b8f6d0', '8e50c1e61286798d0c25cad94c874699', 'f30e8b87-d047-4bca-9b34-d223170df87c', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:30:52'),
('0aa70f76-af5c-11f0-9a0c-005056b8f6d0', '8e50c1e61286798d0c25cad94c874699', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:30:52'),
('0c3b4c1c-b95d-11f0-9a0c-005056b8f6d0', 'a2f4f755e19bf78413b83554349a2bab', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 09:03:57'),
('1d08463c-ad5c-11f0-9a0c-005056b8f6d0', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:26:00'),
('1d0ad13a-ad5c-11f0-9a0c-005056b8f6d0', 'c0a1ccdaa3cc8449b8a8648d9f98ff91', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 02:26:00'),
('1e5ff9ac-b0be-11f0-9a0c-005056b8f6d0', 'e3263d7e808e55aedf5b535768fc8e3a', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 09:45:28'),
('1eac9251-b2dd-11f0-9a0c-005056b8f6d0', '632456371e6b634ceb7b30c85cf89ebf', '3768e84a-18c0-49d9-94dd-09b44c7c9a7d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:32:27'),
('232e021b-b5a1-11f0-9a0c-005056b8f6d0', '6becc7cc78b8b6b8e8066e923c21d181', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-30 15:00:57'),
('232e3c0a-b5a1-11f0-9a0c-005056b8f6d0', '6becc7cc78b8b6b8e8066e923c21d181', '8c782887-8fd3-4f99-ac27-63054a8a1942', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-30 15:00:57'),
('25240eeb-a8d7-11f0-aff6-005056b8f6d0', '7e24f638b40854acf4a162a469026e9e', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 08:24:56'),
('27c5e528-bdea-11f0-8604-005056b8f6d0', '7eba9a30705d2c1ddc0a1e2d6cac3d96', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 04:03:04'),
('29012f24-b0ce-11f0-9a0c-005056b8f6d0', '8b5dbcd8e12a7f3ada28f739b9f3c9f0', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 11:40:18'),
('2b762ec0-bdf3-11f0-8604-005056b8f6d0', '682b8b2fc1c5de91674a1773db6f539d', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 05:07:36'),
('2eb5af3a-af54-11f0-9a0c-005056b8f6d0', '3f4fcda2cc17020648deacae219f4708', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:34:37'),
('2eb5c8cb-af54-11f0-9a0c-005056b8f6d0', '3f4fcda2cc17020648deacae219f4708', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:34:37'),
('324b9bf2-a988-11f0-aff6-005056b8f6d0', '21e5cc0d2aae7047c2e2bbe663b811e6', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 05:32:19'),
('324bb249-a988-11f0-aff6-005056b8f6d0', '21e5cc0d2aae7047c2e2bbe663b811e6', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 05:32:19'),
('33a439b7-b5a2-11f0-9a0c-005056b8f6d0', '7671fe71ebe2a276cf49f946914b5082', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-30 15:08:34'),
('33a44a8f-b5a2-11f0-9a0c-005056b8f6d0', '7671fe71ebe2a276cf49f946914b5082', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-30 15:08:34'),
('3983e1e4-be1e-11f0-8604-005056b8f6d0', 'a005d2bc5a0c79101ef49cdfd84e88bf', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 10:15:48'),
('3f21abbe-af58-11f0-9a0c-005056b8f6d0', '2f5a45eb2c3897400895d1f8a9f6b5c2', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:03:42'),
('3f21c76b-af58-11f0-9a0c-005056b8f6d0', '2f5a45eb2c3897400895d1f8a9f6b5c2', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:03:42'),
('3f712cf0-b329-11f0-9a0c-005056b8f6d0', 'aa95c56d33fca80adb2f4305b9d22e7d', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 11:37:24'),
('419e1b89-b2dc-11f0-9a0c-005056b8f6d0', '304700a01748aa660aa5afd0930f1a0e', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:26:16'),
('42406996-aa66-11f0-9a0c-005056b8f6d0', 'a5eb1b3774bb8de6a806482e70a0c608', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 08:01:02'),
('44a5fa08-ae1b-11f0-9a0c-005056b8f6d0', '0475cf8c871b6d870a03ca8beeba95e4', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 01:14:22'),
('45e6bfb9-ad62-11f0-9a0c-005056b8f6d0', '417d339d06500f15a6b7531da0c7cd28', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-20 03:10:06'),
('464c3044-ae93-11f0-9a0c-005056b8f6d0', 'dbbf9e3d828c7ae3afcc8877b1570cc1', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 15:33:24'),
('464c4fc8-ae93-11f0-9a0c-005056b8f6d0', 'dbbf9e3d828c7ae3afcc8877b1570cc1', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 15:33:24'),
('4b9fecec-bbb3-11f0-8604-005056b8f6d0', '887ac5a1a781314baf1c0823330b88fe', '9ae78e96-2b61-4d2c-8058-aa4e7050221b', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-07 08:25:19'),
('4ba210e6-bbb3-11f0-8604-005056b8f6d0', '887ac5a1a781314baf1c0823330b88fe', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-07 08:25:19'),
('4ba22ac7-bbb3-11f0-8604-005056b8f6d0', '887ac5a1a781314baf1c0823330b88fe', '6fbca1c7-761f-4027-ba4c-89e04832b717', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-07 08:25:19'),
('4f7e6fcf-b32d-11f0-9a0c-005056b8f6d0', '713616155e9f40ce7a0bf5f1bbd0ca5f', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 12:06:29'),
('4f7e7e9c-b32d-11f0-9a0c-005056b8f6d0', '713616155e9f40ce7a0bf5f1bbd0ca5f', '8c782887-8fd3-4f99-ac27-63054a8a1942', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 12:06:29'),
('518aa287-b46c-11f0-9a0c-005056b8f6d0', '519fee5632c4c3a2970bde1aeb2a1c75', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 02:10:03'),
('59593af6-a992-11f0-aff6-005056b8f6d0', 'e568fe0d3564ce271bbb99a9d0f1f6aa', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 06:44:59'),
('59883e45-a992-11f0-aff6-005056b8f6d0', 'e568fe0d3564ce271bbb99a9d0f1f6aa', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 06:44:59'),
('59e517f8-ae92-11f0-9a0c-005056b8f6d0', 'db915fd3678a3c7a9a85d8aa606f02f5', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 15:26:47'),
('5b4b7d69-b30b-11f0-9a0c-005056b8f6d0', '818fa45a1b16cd16b831a6f1b6e63d72', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 08:03:26'),
('688ea495-bac2-11f0-9a0c-005056b8f6d0', 'f3a0b4c735c95737d37c23cd33793ae9', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-06 03:42:04'),
('6fe8e8c5-b0a7-11f0-9a0c-005056b8f6d0', '1fc0b09aaf3f079bb9e4ca9bafd97bde', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 07:03:07'),
('6fe906ec-b0a7-11f0-9a0c-005056b8f6d0', '1fc0b09aaf3f079bb9e4ca9bafd97bde', '8c782887-8fd3-4f99-ac27-63054a8a1942', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-24 07:03:07'),
('74d13542-ba16-11f0-9a0c-005056b8f6d0', 'ec78cca5255db3e17e0371382cb93425', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-05 07:11:10'),
('74d1561e-ba16-11f0-9a0c-005056b8f6d0', 'ec78cca5255db3e17e0371382cb93425', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-05 07:11:10'),
('76c2f463-ba13-11f0-9a0c-005056b8f6d0', '4d0f17400296e43ac2a70f6764cce016', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-05 06:49:44'),
('7b24a5f9-af56-11f0-9a0c-005056b8f6d0', 'b3353d226e75aeda28572314a7abe62f', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:51:04'),
('7b24bea3-af56-11f0-9a0c-005056b8f6d0', 'b3353d226e75aeda28572314a7abe62f', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:51:04'),
('7c8d5bca-af59-11f0-9a0c-005056b8f6d0', '574ec888c60f34c5858cbea206e671b0', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:12:35'),
('7c8d6d9c-af59-11f0-9a0c-005056b8f6d0', '574ec888c60f34c5858cbea206e671b0', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 15:12:35'),
('8845083b-a827-11f0-aff6-005056b8f6d0', '375fbb2a1a2a398c6ee3fcbba58316cb', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 11:27:49'),
('884522b8-a827-11f0-aff6-005056b8f6d0', '375fbb2a1a2a398c6ee3fcbba58316cb', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 11:27:49'),
('89ce80f0-b4ae-11f0-9a0c-005056b8f6d0', '4dd8e701d99aa9dd90edfc7e94dae7eb', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 10:04:04'),
('89cec294-b4ae-11f0-9a0c-005056b8f6d0', '4dd8e701d99aa9dd90edfc7e94dae7eb', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 10:04:04'),
('89cee4b7-b4ae-11f0-9a0c-005056b8f6d0', '4dd8e701d99aa9dd90edfc7e94dae7eb', '8c782887-8fd3-4f99-ac27-63054a8a1942', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 10:04:04'),
('93336f56-b8ac-11f0-9a0c-005056b8f6d0', '637269d63c441e053578d2a383dded3a', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:00:42'),
('944b9c74-a804-11f0-aff6-005056b8f6d0', '54ce0ca8f7f731876d95512f0d28debd', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 07:17:37'),
('944bb0fe-a804-11f0-aff6-005056b8f6d0', '54ce0ca8f7f731876d95512f0d28debd', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-13 07:17:37'),
('9c9f2dec-badd-11f0-9a0c-005056b8f6d0', '7718ca773579e9622a2e80901a7484b1', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', '2025-11-06 06:56:48'),
('9c9f422e-badd-11f0-9a0c-005056b8f6d0', '7718ca773579e9622a2e80901a7484b1', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', '2025-11-06 06:56:48'),
('9e293e9f-b8ad-11f0-9a0c-005056b8f6d0', '30092d60a07c771aba052925e846639e', '3768e84a-18c0-49d9-94dd-09b44c7c9a7d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:08:10'),
('9e295a17-b8ad-11f0-9a0c-005056b8f6d0', '30092d60a07c771aba052925e846639e', '8c782887-8fd3-4f99-ac27-63054a8a1942', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:08:10'),
('a4ec8b9a-b010-11f0-9a0c-005056b8f6d0', '5eb8ec3a227ec1dab8b5463cb9d26f12', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-23 13:03:42'),
('a86ba089-b4c1-11f0-9a0c-005056b8f6d0', '2d82b2958ff60d48fee678b5e5cdadbb', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 12:20:56'),
('a9381fe8-b337-11f0-9a0c-005056b8f6d0', '7940e651309a1a31097e7734e2b4f960', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', '3', '2025-10-27 13:20:36'),
('a9632921-b324-11f0-9a0c-005056b8f6d0', '8b3f3eb00f76e9a698bcc04e0243a566', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 11:04:34'),
('a98eaaf4-af57-11f0-9a0c-005056b8f6d0', '4526af8066b18a46eb44341c5b187ad8', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-22 14:59:31'),
('ac4f7197-ae1d-11f0-9a0c-005056b8f6d0', 'c963ae31f67a991ec33b1cd411161af5', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 01:31:35'),
('ac4f8e9d-ae1d-11f0-9a0c-005056b8f6d0', 'c963ae31f67a991ec33b1cd411161af5', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 01:31:35'),
('afbd5f50-b2dd-11f0-9a0c-005056b8f6d0', 'efda3db7e97b4a86927f55e464fa6562', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:36:30'),
('afbd7cba-b2dd-11f0-9a0c-005056b8f6d0', 'efda3db7e97b4a86927f55e464fa6562', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:36:30'),
('b0118a7f-ae61-11f0-9a0c-005056b8f6d0', '5356767837cd88ec0215243511c749ab', 'f30e8b87-d047-4bca-9b34-d223170df87c', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:38:27'),
('b0259bb9-ae61-11f0-9a0c-005056b8f6d0', '5356767837cd88ec0215243511c749ab', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:38:27'),
('b025abc0-ae61-11f0-9a0c-005056b8f6d0', '5356767837cd88ec0215243511c749ab', '8c782887-8fd3-4f99-ac27-63054a8a1942', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:38:27'),
('b0962f1c-b94e-11f0-9a0c-005056b8f6d0', '13bfaa55fc6a83ce652f638acc7259b7', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 07:21:11'),
('b1094813-b4ad-11f0-9a0c-005056b8f6d0', 'ec68368ab063a1cf203f014f7975d6b1', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 09:58:00'),
('b109589c-b4ad-11f0-9a0c-005056b8f6d0', 'ec68368ab063a1cf203f014f7975d6b1', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 09:58:00'),
('b1096754-b4ad-11f0-9a0c-005056b8f6d0', 'ec68368ab063a1cf203f014f7975d6b1', '8c782887-8fd3-4f99-ac27-63054a8a1942', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 09:58:00'),
('b13048b2-ab32-11f0-9a0c-005056b8f6d0', '82396eb89e2502198fa334db5707c050', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-17 08:24:26'),
('b1394525-aa7b-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 10:34:27'),
('b1395748-aa7b-11f0-9a0c-005056b8f6d0', '6c6595bb55eb3374513891cfce0bec7e', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 10:34:27'),
('b3759186-a8fb-11f0-aff6-005056b8f6d0', '112f5fda773c2b665958457654cba090', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-14 12:46:36'),
('b83c5796-b924-11f0-9a0c-005056b8f6d0', '5332f17963259b72d24f0321737a5302', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 02:20:45'),
('c13de570-a986-11f0-aff6-005056b8f6d0', '22d7359539ef8e483ba62547e2e5136e', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 05:21:59'),
('c24ff73f-ae62-11f0-9a0c-005056b8f6d0', '350a46a5acbd6776dc3b040297ef322d', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-21 09:46:07'),
('c773bb99-b8ae-11f0-9a0c-005056b8f6d0', 'fe2a02ab8e28f3c0f8d2e4f214f8ab23', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 12:16:28'),
('d0b7674b-b332-11f0-9a0c-005056b8f6d0', '9439b4fe59f3705e5dd798f99ea78bf7', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 12:45:53'),
('d1c02f7d-ab23-11f0-9a0c-005056b8f6d0', 'c3fae15b8715db998f7865db7e4e89ec', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-17 06:37:59'),
('e240b5e5-b483-11f0-9a0c-005056b8f6d0', 'bc420db35f715fdc06e983365015152d', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 04:58:44'),
('e240cf6f-b483-11f0-9a0c-005056b8f6d0', 'bc420db35f715fdc06e983365015152d', '8c782887-8fd3-4f99-ac27-63054a8a1942', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 04:58:44'),
('e240e76b-b483-11f0-9a0c-005056b8f6d0', 'bc420db35f715fdc06e983365015152d', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-29 04:58:44'),
('e8afac92-b3f1-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', 'f30e8b87-d047-4bca-9b34-d223170df87c', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-28 11:33:48'),
('e8afc799-b3f1-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-28 11:33:48'),
('e8afdf64-b3f1-11f0-9a0c-005056b8f6d0', 'eefbd78e5f2426f6a79494712287f81b', '6fbca1c7-761f-4027-ba4c-89e04832b717', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-28 11:33:48'),
('ec6d84bb-be1a-11f0-8604-005056b8f6d0', 'c9a4a76087ad93b691fa30f4dda02be0', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-10 09:52:10'),
('efdfe83d-b925-11f0-9a0c-005056b8f6d0', '611bfaa99199810cb73ce1721f6ea0a0', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-04 02:29:27'),
('f1b65889-b889-11f0-9a0c-005056b8f6d0', '67eab0c67c179f262013eaa95eee8d9c', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 07:52:48'),
('f1b9fdcb-b889-11f0-9a0c-005056b8f6d0', '67eab0c67c179f262013eaa95eee8d9c', 'e083a0dd-3393-44cd-b376-d876d6728d9a', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-11-03 07:52:48'),
('f3a6177c-aa46-11f0-9a0c-005056b8f6d0', '47255f8f395045b169b65b16f91ebbe9', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 04:16:56'),
('f3d05d99-aa46-11f0-9a0c-005056b8f6d0', '47255f8f395045b169b65b16f91ebbe9', 'e083a0dd-3393-44cd-b376-d876d6728d9a', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 04:16:56'),
('f6d609f7-a9da-11f0-9a0c-005056b8f6d0', '3641ec530675c2226fc1420f5d02d71e', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 15:23:55'),
('f6d61fe4-a9da-11f0-9a0c-005056b8f6d0', '3641ec530675c2226fc1420f5d02d71e', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-15 15:23:55'),
('fa20eb03-b2dd-11f0-9a0c-005056b8f6d0', 'cbf32b8dd3e4ef748855c23d4b162e8d', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 02:38:35'),
('fc9ba097-aa38-11f0-9a0c-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 02:36:58'),
('fcc6a62a-aa38-11f0-9a0c-005056b8f6d0', 'e0639361c653a406d17686669cae0a1f', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-16 02:36:58'),
('fe617630-b324-11f0-9a0c-005056b8f6d0', '2d72ce32ffe570003a84eea4bbc5153e', '3', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '2025-10-27 11:06:57');

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
('1', 'Innovation_PIT', 'Product  Solution Teams', '2', '2024-09-26 03:35:50', '5', '2024-11-04 02:27:46', '5'),
('2', 'Zoom', 'Zoom Project', '2', '2024-09-26 03:35:50', '5', '2024-11-04 01:36:06', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1'),
('28534929-e527-4755-bd37-0acdd51b7b45', 'Sale Corporate _PIT', 'Sale Corporate _PIT', '5', '2024-11-06 01:46:07', '5', '2024-12-03 04:26:43', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6'),
('3', 'Service_PIT', 'Service Bank & Corporate_Chittichai', '2', '2024-09-26 03:35:50', '5', '2024-12-03 04:27:06', '97c68703-a8b7-4ceb-9344-65fe4404c4ab'),
('37547921-5387-4be1-bde0-e9ba5c4e0fdf', 'Enterprise_PIT', 'Presales Service & Enterprise Solution', '5', '2024-11-01 01:05:11', '5', '2024-12-03 04:27:43', '34e67e45-92f6-4e20-a78b-a4ffe97b3775'),
('4', 'Point IT', 'Point IT Consulting Co. Ltd.', '2', '2024-09-26 03:35:50', '2', '2024-10-20 12:39:29', '5'),
('715e81f0-4985-4981-982c-45cafb9748dc', 'Versual Teams (MAZK)', 'ใช้เวลาว่าให้เกิดประโยชน์', '2', '2025-01-07 10:47:16', '2', '2025-01-07 10:48:27', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a'),
('b9db21db-cfd7-4887-9ca7-5088a12f1bda', 'Sales Solution_PIT', 'Sales Solution', '5', '2024-11-04 02:58:23', '5', '2024-12-03 04:28:12', '6614b721-a8b4-46d2-9c80-0caab04772dc'),
('c8fcdec8-4a28-4b6b-be8b-8bb0579d74bc', 'Outsourcing Service_PIT', 'Outsourcing Team Pattaya', '5', '2024-11-25 01:37:30', '5', '2024-12-03 04:28:28', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f'),
('db32697a-0f69-41f7-9413-58ffe920ad7d', 'Sales Bank Corporate_PIT', 'Bank Corporate Sales', '5', '2024-11-04 02:29:50', '5', '2024-11-06 01:21:56', '6614b721-a8b4-46d2-9c80-0caab04772dc'),
('de3fc0f5-9ebf-4c47-88dd-da5a570653ae', 'Smart City Solutions', '', '5', '2024-12-09 03:17:23', NULL, NULL, '5'),
('f4b11a86-0fca-45e5-8511-6a946c7f21d4', 'Sales Smart City_PIT', 'Oran Team', '5', '2024-11-04 01:39:32', '5', '2024-11-06 01:23:26', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f');

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
('1', 'Sale', 'Test Platform', 'Sale', 'Saletest@gmail.com', 'Seller', 'Sale Test Platform', '0839595800', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'Point IT Consulting Co.,Ltd.', '2024-09-15 09:43:58', '2', ''),
('14d9e34c-b691-4ce8-a5ef-929ace71248a', 'Boongred', 'Theephukhieo', 'boongerd', 'boongerd@pointit.co.th', 'Sale Supervisor', 'System Engineer Manager', '0818741889', '$2y$10$nOlaLUtPDsBhJxyi37sYZukj7i8dJJ811mbTxeC749VKxZZuYO1vW', 'Point IT Consulting Co.,Ltd.', '2024-10-31 23:55:23', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', NULL),
('193f9eed-2938-4305-ab65-828ac5253b30', 'อรรถกร', 'ปุญญะฐิติ', 'Atthakorn', 'atthakorn.pm@gmail.com', 'Seller', '', '0859936540', '$2y$10$Gi1tK2Xsl0dZ28YasSajjeusUsK2.1LcP0GYzZGgYhqB6NLNIf5Qm', 'บริษัท ซูม อินฟอร์เมชั่น ซิสเต็ม จํากัด', '2025-06-11 07:47:19', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', NULL),
('1b9c09d2-dc91-4b5e-a62b-8c42a41958ab', 'Arunnee', 'Thiamthawisin', 'Arunnee', 'arunnee@pointit.co.th', 'Seller', 'Account Executive Manager', '', '$2y$10$gcxTM193rEDPM.Ynw.GfEed.RYEBQl7640PXfEzH7Qj05kXleZ5QK', 'Point IT Consulting Co.,Ltd.', '2024-11-04 03:05:45', '5', NULL),
('1f540668-fa06-45ec-8881-b50c378cf648', 'Podchanan', 'Setthanan', 'Podchanan', 'Podchanan@pointit.co.th', 'Seller', 'Account Executive', '', '$2y$10$4Wtf3LOLe3wXebNw4co/e.58NEKkRyjxqUE7vceMnEFSLqA.D7eym', 'Point IT Consulting Co.,Ltd.', '2024-12-03 04:23:35', '5', NULL),
('2', 'Systems', 'Admin', 'Admin', 'Systems_admin@gmail.com', 'Executive', 'Systems Admin', '0839595800', '$2y$10$lMfm90VV7oVMLHypibv3Xuc1enYtrj4hkiHyFxQM3FXPC7n8vALRy', 'Point IT Consulting Co.,Ltd.', '2024-09-15 09:43:58', '2', ''),
('270c74ec-9124-4eb5-9469-0253ba8530af', 'Awirut', 'Somsanguan', 'Awirut', 'Awirut@pointit.co.th', 'Sale Supervisor', 'Smart Innovation Technology Consulting Manager', '', '$2y$10$zbqZ8JHuuGejCPqkozcYb.wzIfiTgY.peFop7RJInr9HIUPjzZFra', 'Point IT Consulting Co.,LTD.', '2024-11-06 02:20:29', '5', NULL),
('2f6d353b-53f1-4492-8878-bc93c18c5de9', 'Prakorb', 'Jongjarussang', 'Prakorb', 'prakorb@pointit.co.th', 'Executive', 'MD', '', '$2y$10$kZrq7.zXl241JjNXWWd0oOGi/f20GFYfBH0veRAL4sMCr20reES3C', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:31:48', '5', NULL),
('3', 'Miss Phattraorn', 'Amornophakun', 'Phattraorn', 'phattraorn.a@pointit.co.th', 'Sale Supervisor', 'Sales', '0619522111', '$2y$10$Jy5nQN0AarVENf8lP49N4O6NoA00uiSdQI.3FQJMDWobak4qTrOeG', 'Point IT Consulting Co.,Ltd.', '2024-09-15 09:43:58', '2', '670e42ef5b4a3.jpg'),
('30750fba-88ab-44ce-baf2-d0894357c67c', 'Bulakorn', 'Puapun', 'Bulakorn', 'bulakorn@gmail.com', 'Sale Supervisor', 'AI Business Consulting Director', '', '$2y$10$FJt7z443LOlzw14xC3ShOO..8su3Jt1dSXR/JEwdZUA44qSAoKmzu', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:14:26', '5', NULL),
('3140fdaf-5103-4423-bf87-11b7c1153416', 'Direk', 'Wongsngam', 'Direk', 'Direk@pointit.co.th', 'Seller', 'Bank & Corporate SalesDirector', '', '$2y$10$M/bAx1lFykgf1LklAvbQKONKI4OQfpu7NofVfwA.r1GDy9xx94uGO', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:39:01', '5', NULL),
('34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'Pisarn', 'Siribandit', 'Pisarn', 'pisarn@pointit.co.th', 'Sale Supervisor', 'Digital Transformation Consulting Director', '', '$2y$10$aEOtRUxIfKi52ib5Jj.Vpue/FP7eIWKeNRdM68DEr1GCH5OUa1uOy', 'Point IT Consulting Co.,Ltd.', '2024-10-31 18:08:53', '5', '67242a25ce524.png'),
('3768e84a-18c0-49d9-94dd-09b44c7c9a7d', 'Supakorn', 'jaipong', 'Supakorn.j', 'supakonjaipong367@gmail.com', 'Engineer', 'Frontend', '0869936207', '$2y$10$KKTAle5Dm2vMP5WI6I1zkOoszGv5HFU0JiA3dH4lQzIPho41wlKX.', 'Point IT Consulting Co.,Ltd.', '2025-10-21 08:55:57', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', NULL),
('3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'Natapornsuang', 'Chanasan', 'Natapornsuang', 'natapornsuang@pointit.co.th', 'Seller', 'Senior Account Executive', '', '$2y$10$b5wxlKujVAxTagneuJOtqOJ4xBWQNkZ8lqJHmjs4CVFb4GIi0NrOi', 'Point IT Consulting Co.,Ltd.', '2024-11-04 03:00:40', '5', NULL),
('3efcb87b-ce45-4a66-9d73-91259caba1d0', 'Teerayut', 'Kaengjai', 'Teerayut', 'Teerayut@pointit.co.th', 'Engineer', 'Head of Enterprise Engineer Service', '', '$2y$10$u5SlcRNFVTOxQ1aFabruaeLG49neZPwAQEWo6ToVm8ZwwZul8lqVS', 'Point IT Consulting Co.,Ltd.', '2024-11-06 02:29:31', '5', NULL),
('4', 'Support', 'Platform', 'Support', 'Support@gmail.com', 'Executive', 'Application Support', '0839595811', '$2y$10$RAWOJU03Vy72u4zMVF/M/O9Af1HSbGOHAjlDKZHgrzbSZodZUcuky', 'Point IT Consulting Co.,Ltd.', '2024-09-15 09:55:43', '2', '6724613260590.png'),
('44ab4e8b-e3e6-431d-ad49-40d4601779b4', 'Nutjaree', 'Chaothonglang', 'Nutjaree', 'nutjaree@pointit.co.th', 'Sale Supervisor', 'Assistant Service Manager', '', '$2y$10$OeTqb/woFTv/pt7uaBRx4ujA7jJYTuyGzSmx2y4jtijxn9oJcRuky', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:04:37', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', NULL),
('5', 'Panit', 'Paophan', 'Panit', 'panit@poinitit.co.th', 'Executive', 'Executive Director', '0814834619', '$2y$10$Td6gIdc/jANDPx3gJJEbGOPMY1Y7MMigduUt6tJ9DeB3KRGykTmg2', 'Point IT Consulting Co.,Ltd.', '2024-09-17 08:15:37', '2', NULL),
('5b698e22-ba83-43c4-a39e-e6d68f98791f', 'Chawanon', 'Tanchairittikul', 'Chawanon', 'Chawanon@pointit.co.th', 'Engineer', 'Project Management', '', '$2y$10$kRhzV6oQJ79bn/tZuuhMD.WBTbSR.NkPniYOL2si5WfsJyzjgtaau', 'Point IT Consulting Co.,Ltd.', '2025-06-18 02:00:45', '2', NULL),
('5eef69ba-15ee-4414-a2e4-be4f68b8839e', 'Kanitta', 'Ongsathan', 'Kanitta', 'kanitta@pointit.co.th', 'Sale Supervisor', 'Senior Procurement', '0880223292', '$2y$10$6BcDhIY.7m7X2s7D6iAXkOTuden3sQucRuN.8mcV4WF44RMmFHHui', 'Point IT Consulting Co.,Ltd.', '2024-12-03 04:01:23', '5', NULL),
('6614b721-a8b4-46d2-9c80-0caab04772dc', 'Woradol', 'Daoduang', 'Woradol', 'Woradol@pointit.co.th', 'Executive', 'Executive Director', '', '$2y$10$l454f/PTDFOabJbIz0BAkedEGdUGc000TRpac7ffYJrRzlIIwcUc2', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:34:51', '5', NULL),
('6fbca1c7-761f-4027-ba4c-89e04832b717', 'Tulatorn', 'Yongprayoon', 'Tulatorn', 'Tulatorn@pointit.co.th', 'Engineer', 'TesterTester', '0961491519', '$2y$10$1nFq.HFtM2BKAWAIiTZTBOW.3m9Ttbw1UpuxhQF9OGyM2hnVXTWTG', 'Point IT Consulting Co.,Ltd.', '2025-10-27 07:27:56', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', NULL),
('70dd36b5-f587-4aa9-b544-c69542616d34', 'Narumon', 'Wongkrua', 'Narumon', 'wongkrua.na@gmail.com', 'Seller', '', '0642614635', '$2y$10$D4Ab6d51oqyzTj4IOTHGsuv5oubG0u37VfDRVoT/xvpJVPWpk8LG.', 'บริษัท ซูม อินฟอร์เมชั่น ซิสเต็ม จํากัด', '2025-06-11 09:15:44', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', NULL),
('85c114ec-a416-41c0-9859-12b90dc5b488', 'Porapath', 'Yanthukij', 'Porapath', 'porapath@pointit.co.th', 'Seller', 'Procurement', '0956422238', '$2y$10$U1hx.FejkNpt5/ltAvw.b.gxPyzq3fS5WpqMh4H.10negrF/7qVk6', 'Point IT Consulting Co.,Ltd.', '2024-12-03 04:02:44', '5', NULL),
('86054531-f751-48c7-b257-222c9ccbd946', 'สารภี', 'ทองแก้ว', 'Kai', 'Acct@pointit.co.th', 'Executive', 'สมุห์บัญชี', '', '$2y$10$flZPg1wESq1UvwWrsS1wDuu2v1dFccAL70ZmJgZtHtlLGCEr54q5y', '', '2025-06-10 05:18:57', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', NULL),
('8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'Pawitcha', 'Katekhong', 'Pawitcha', 'Pawitcha@pointit.co.th', 'Seller', 'Bank &amp; Corporate Account Executive', '', '$2y$10$k1TyBLrPo0z7gk/wWE0N8u08grMt8IoLb0sbcYu82YAUFLkhNC9.6', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:43:57', '5', NULL),
('8c1c0a55-2610-4081-8d12-b2a6971ffbe8', 'Yuthana', 'Jaturajitraporn', 'Yuthana', 'yuthana@pointit.co.th', 'Seller', 'Senior Sales Backend Developer', '', '$2y$10$.ZJ0wDC827yYB5BqJmbrD.sbXB8sk1m4QPbEXHeVsXCrKofMhC0km', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:07:14', '5', NULL),
('8c782887-8fd3-4f99-ac27-63054a8a1942', 'Surapan', 'Pawanrum', 'Surapan', 'Surapan@pointit.co.th', 'Sale Supervisor', 'Platform Development Manager', '', '$2y$10$wf6P22p7BIpJ2bIdRuyyyur2jxxyliqEi4T084m6Slq.4FZsQxCOa', 'Point IT Consulting Co.,Ltd.', '2024-12-02 06:48:59', '5', NULL),
('97c68703-a8b7-4ceb-9344-65fe4404c4ab', 'Chittichai', 'Duangnang', 'Chittichai', 'chittichai@pointit.co.th', 'Sale Supervisor', 'Service Manager', '', '$2y$10$va/6nCSzdBqd/kCyMgYN7.gtksHhW2t14s3Qr1EClGsr10cSFJyza', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:00:10', '5', NULL),
('9ae78e96-2b61-4d2c-8058-aa4e7050221b', 'Kongpob', 'Jongjarussang', 'Kongpob', 'kongpob662@icloud.com', 'Engineer', 'DevOps Engineer', '0968863937', '$2y$10$KpkfiH.E1XWs9NBrf8wc.Oy3o.9loLNsJWKrGOY0jhz7xk/3dLvx.', 'Point IT Consulting Co.,Ltd.', '2025-10-24 09:36:52', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', NULL),
('a5741799-938b-4d0a-a3dc-4ca1aa164708', 'Theerachart', 'Tiyapongpattana', 'Theerachart', 'theerachart@pointit.co.th', 'Engineer', 'Innovation Business Consulting Manager', '', '$2y$10$FcspHzhkNMDUaSMshYrZdOGC/8OHya2fH8nwgcppvoFI0HT9w8W7O', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:28:08', '5', NULL),
('b27b56e5-6f28-4d30-8add-4bddafa38841', 'Decha', 'Suratkullwattana', 'Decha', 'khadectemp@outlook.com', 'Engineer', 'Software Business Consultant', '', '$2y$10$YUnc5HvQZ1UQFx64cdsP2.0S3y38hdWzvqDur3v2Plj8gnE8w3iXa', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:11:18', '5', NULL),
('b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 'Nanthika', 'Chongcharassang', 'nanthika', 'nanthika@pointit.co.th', 'Sale Supervisor', 'Project Manager', '0631979263', '$2y$10$n5MxPcCUuAbzhssVwMIZ7.LYmuR6qzjlzdnDDyv.hjBuoPHXZ4S1e', 'Point IT Consulting Co.,Ltd.', '2024-10-31 23:57:35', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', NULL),
('ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'Gun', 'Oran', 'Oran.gun', 'oran.gun@gmail.com', 'Account Management', 'MD', '0851511551', '$2y$10$uXZ59F.TyI624FgfXbeXdO7KPTHbsz//KLltxxz6PMbukwGEa526K', 'บริษัท ซูม อินฟอร์เมชั่น ซิสเต็ม จํากัด', '2024-11-04 01:34:43', '5', '672824b3cb14d.png'),
('bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'Yanisa', 'Khemthong', 'Yanisa_Pit', 'Yanisa@pointit.co.th', 'Seller', 'Senior Account Executive, Smart City Solution', '', '$2y$10$haXSQgdafMSbDh2Idbq4EuqglczuOEcc63XtUSjg3QOB0PO8ygtFa', 'Point IT Consulting Co.,Ltd.', '2024-11-04 01:49:55', 'ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', NULL),
('c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', 'Yanisa', 'Zoom', 'Yanisa_Zoom', 'yanisa8742@gmail.com', 'Seller', 'Senior Account Executive, Smart City Solution', '', '$2y$10$wH8YfNGAu/AN//ljfPduDOhzhADKMi8RBdz6aUXm3g/VwKYQ0TT9m', 'บริษัท ซูม อินฟอร์เมชั่น ซิสเต็ม จํากัด', '2024-11-04 01:54:45', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', NULL),
('c89b96f1-f916-448d-9725-2e0957cdba49', 'Versual Teams', '(Mazk)', 'mazk', 'innovation@pointit.co.th', 'Sale Supervisor', 'Project Management', '0619512111', '$2y$10$KLN.d4rgbQqAiGH8s2LYYeD.4XgakfQLDzGESSV/HUuLcz0oCaBVG', 'Point IT Consulting Co.,Ltd.', '2025-01-16 06:51:41', '2', '6788ac7dea3bd.jpg'),
('c9245a19-52fa-4b02-a98c-b962f2f51b3f', 'Jakkrit', 'Pontpai', 'Jakkrit', 'jakkrit@pointit.co.th', 'Sale Supervisor', 'Smart City Business Consulting Manager', '', '$2y$10$Vd8C2.69FvbUIvmAejUz4eZddOs.rEUiemJ.e94.7B15R2O0CQJ7S', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:18:56', '5', NULL),
('c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Apirak', 'Bangpuk', 'Apirak.ba', 'apirak.ba@gmail.com', 'Account Management', 'IT Service', '0839595888', '$2y$10$Tma0kaOTwXsxCKbxIxljKekXuxgV/K8rJQGEPPWF682xv54E9tTL.', 'Point IT Consulting Co.,Ltd.', '2025-10-13 06:51:38', '2', '68eca17a53cd2.jpg'),
('e083a0dd-3393-44cd-b376-d876d6728d9a', 'Phudis', 'Rungsissuriyachai', 'Phudis', 'Phudis.ucsc@gmail.com', 'Engineer', 'DevOps Engineering', '0618962669', '$2y$10$fQB3ut/oITnmPYBjbYtHdun4hMuxTEBVe6C7gEM.QFFdZprnJNFBO', 'Point IT Consulting Co.,Ltd.', '2025-10-16 04:15:13', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', NULL),
('e23160ec-23a4-4724-9690-adb205162afb', 'Wilaiwan', 'Vutipram', 'Wilaiwan', 'wilaiwan@pointit.co.th', 'Seller', 'Project Management , Smart city solutions', '', '$2y$10$GdkL6jMtVHIyWuKlOv7KUO2aXwQTB1cC4v2E7GItr3oFjesZRjE36', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:21:15', '5', NULL),
('e40dedaf-3e9b-4694-8ee9-c173d5c44db6', 'Woraluck', 'Khunsuwanchai', 'Woraluck', 'Woraluck@pointit.co.th', 'Sale Supervisor', 'Account Executive Manager', '', '$2y$10$kCmDHGdpiHxlkPPj6wNL2OLy4OzaSo8EshQaBC1cMtW6Eeq6rdhQC', 'Point IT Consulting Co.,Ltd.', '2024-11-06 01:17:45', '5', NULL),
('e79e9929-6132-41ae-ab06-65b29fe70f6c', 'Panuwat', 'Sukcheep', 'Panuwat.S', 'panuwat@pointit.co.th', 'Engineer', 'IT Outsourcing Service Manager', '', '$2y$10$Y.4Xgneo59aAcdNUcz9Zx.Aa71Fj2bLqIZJhVYt95a19ztua6wSxC', 'Point IT Consulting Co.,LTD.', '2024-11-25 01:33:50', '5', NULL),
('e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 'Daranee', 'Punyathiti', 'Daranee', 'daranee@pointit.co.th', 'Executive', 'MD', '', '$2y$10$TUQFW2R8NYBX4TJs6UNX0eur7UHXFj7VAptbi7/UD//DA7iRVpyIO', 'บริษัท ซูม อินฟอร์เมชั่น ซิสเต็ม จํากัด', '2024-11-04 01:30:21', '5', '672823ad217eb.png'),
('ec093af4-810f-4add-9b23-1d9caaa8cfa6', 'Marlee', 'Sawar', 'Marlee', 'Marlee@pointit.co.th', 'Engineer', 'Helpdesk & Support Team', '0909963690', '$2y$10$MOlkw.EDn0ZhDStgFSK.3ekLE2X31DguRJ2h01NEgkbSe1DD8aqW.', 'Point IT Consulting Co.,Ltd.', '2025-10-27 07:32:08', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', NULL),
('ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', 'Oran.gun', 'Point IT', 'Oran.pit', 'Oran@pointit.co.th', 'Account Management', 'Smart City Consulting Director', '0851511551', '$2y$10$H6/.6cFDBCBXHaqn/HS6Nu7C2AT4P9yIlMgr/DLnSm7TbJchvMuWC', 'Point IT Consulting Co.,Ltd.', '2024-11-04 01:45:31', '5', NULL),
('f30e8b87-d047-4bca-9b34-d223170df87c', 'Jiratip', 'vittayanusak', 'Jiratip', 'j.vittayanusak@gmail.com', 'Engineer', 'Software Tester', '0902215120', '$2y$10$5wGa0vBFnkQabLXxjBL5gO9tEhSMgcWIk1g9OnVvSyyCL3BEOILpi', 'Point IT Consulting Co.,Ltd.', '2024-12-09 10:08:45', '2', NULL),
('f384c704-5291-4413-8f52-dc25e10b5d4f', 'Piti', 'Nithitanabhornkul', 'Piti', 'piti@pointit.co.th', 'Engineer', 'Senior Backend Software Develper', '0896926913', '$2y$10$MzOJ8Q9Kc0DtnOPh61sUs.7EU6GjnK6uHVAOqzVcMVpVTpfCekjYS', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:02:34', '5', NULL),
('f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'Pongsan', 'chakranon', 'Pongsan', 'pongsan.chakranon@gmail.com', 'Engineer', 'Ai Software Developer', '0948709996', '$2y$10$bIK/..bQZcN/GbLezh/XuuX5rdsfTxymOeVdHuUuXUbufmK6Mhg.6', 'Point IT Consulting Co.,Ltd.', '2024-12-09 02:57:19', '5', NULL),
('ff2acbbb-4ec0-4214-8a30-eb1fc6e02700', 'Poomsak', 'Janluan', 'Poomsak', 'poomsak1994@gmail.com', 'Engineer', 'Software Development', '0862295093', '$2y$10$WgIEqPWOqk041rbRZ3j6heeA77ShbGw.iKLn7V20R2X4ZmdFKyFS6', 'Point IT Consulting Co.,Ltd.', '2024-12-09 09:27:12', '2', NULL);

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

--
-- Dumping data for table `user_creation_logs`
--

INSERT INTO `user_creation_logs` (`id`, `creator_id`, `new_user_id`, `new_user_role`, `created_at`) VALUES
(1, '3', '1537eabd-0014-4f80-9116-9800cc8df26f', 'Seller', '2024-10-10 16:15:03'),
(2, '2', 'a0c895d7-3730-4b11-827d-8a7423d81761', 'Seller', '2024-10-11 14:34:01'),
(3, '3', '0a9fec94-57d3-4f65-aeaa-052cba2c3b62', 'Seller', '2024-10-11 16:18:12'),
(4, '2', 'bb027ef6-7b53-4775-92d6-8faa0e1f7abc', 'Seller', '2024-10-20 11:55:08'),
(5, '5', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 'Sale Supervisor', '2024-11-04 01:30:21'),
(6, '5', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'Sale Supervisor', '2024-11-04 01:34:43'),
(7, '5', 'ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', 'Sale Supervisor', '2024-11-04 01:45:31'),
(8, 'ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'Seller', '2024-11-04 01:49:55'),
(9, 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', 'Seller', '2024-11-04 01:54:46'),
(10, '5', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', 'Sale Supervisor', '2024-11-04 02:00:11'),
(11, '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', 'Seller', '2024-11-04 02:04:37'),
(12, '5', '2f6d353b-53f1-4492-8878-bc93c18c5de9', 'Executive', '2024-11-04 02:31:48'),
(13, '5', '6614b721-a8b4-46d2-9c80-0caab04772dc', 'Executive', '2024-11-04 02:34:51'),
(14, '5', '3140fdaf-5103-4423-bf87-11b7c1153416', 'Seller', '2024-11-04 02:39:01'),
(15, '5', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'Seller', '2024-11-04 02:43:57'),
(16, '5', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'Seller', '2024-11-04 03:00:40'),
(17, '5', '1b9c09d2-dc91-4b5e-a62b-8c42a41958ab', 'Seller', '2024-11-04 03:05:46'),
(18, '5', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', 'Seller', '2024-11-06 01:17:45'),
(19, '5', '270c74ec-9124-4eb5-9469-0253ba8530af', 'Sale Supervisor', '2024-11-06 02:20:29'),
(20, '5', '3efcb87b-ce45-4a66-9d73-91259caba1d0', 'Engineer', '2024-11-06 02:29:31'),
(21, '5', 'e79e9929-6132-41ae-ab06-65b29fe70f6c', 'Engineer', '2024-11-25 01:33:50'),
(22, '5', '8c782887-8fd3-4f99-ac27-63054a8a1942', 'Sale Supervisor', '2024-12-02 06:48:59'),
(23, '5', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 'Sale Supervisor', '2024-12-03 04:01:23'),
(24, '5', '85c114ec-a416-41c0-9859-12b90dc5b488', 'Seller', '2024-12-03 04:02:44'),
(25, '5', '1f540668-fa06-45ec-8881-b50c378cf648', 'Seller', '2024-12-03 04:23:35'),
(26, '5', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'Engineer', '2024-12-09 02:57:20'),
(27, '5', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'Engineer', '2024-12-09 03:02:34'),
(28, '5', '8c1c0a55-2610-4081-8d12-b2a6971ffbe8', 'Engineer', '2024-12-09 03:07:14'),
(29, '5', 'b27b56e5-6f28-4d30-8add-4bddafa38841', 'Engineer', '2024-12-09 03:11:18'),
(30, '5', '30750fba-88ab-44ce-baf2-d0894357c67c', 'Sale Supervisor', '2024-12-09 03:14:26'),
(31, '5', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', 'Sale Supervisor', '2024-12-09 03:18:56'),
(32, '5', 'e23160ec-23a4-4724-9690-adb205162afb', 'Seller', '2024-12-09 03:21:15'),
(33, '5', 'a5741799-938b-4d0a-a3dc-4ca1aa164708', 'Engineer', '2024-12-09 03:28:08'),
(34, '2', 'ff2acbbb-4ec0-4214-8a30-eb1fc6e02700', 'Engineer', '2024-12-09 09:27:13'),
(35, '2', 'f30e8b87-d047-4bca-9b34-d223170df87c', 'Engineer', '2024-12-09 10:08:46'),
(36, '2', 'c89b96f1-f916-448d-9725-2e0957cdba49', 'Sale Supervisor', '2025-01-16 06:51:42'),
(37, 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '86054531-f751-48c7-b257-222c9ccbd946', 'Executive', '2025-06-10 05:18:57'),
(38, 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '193f9eed-2938-4305-ab65-828ac5253b30', 'Seller', '2025-06-11 07:47:19'),
(39, 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '70dd36b5-f587-4aa9-b544-c69542616d34', 'Seller', '2025-06-11 09:15:44'),
(40, '2', '5b698e22-ba83-43c4-a39e-e6d68f98791f', 'Engineer', '2025-06-18 02:00:45'),
(41, '2', 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'Account Management', '2025-10-13 06:51:38'),
(42, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'e083a0dd-3393-44cd-b376-d876d6728d9a', 'Engineer', '2025-10-16 04:15:14'),
(43, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '3768e84a-18c0-49d9-94dd-09b44c7c9a7d', 'Engineer', '2025-10-21 08:55:57'),
(44, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '9ae78e96-2b61-4d2c-8058-aa4e7050221b', 'Engineer', '2025-10-24 09:36:52'),
(45, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', '6fbca1c7-761f-4027-ba4c-89e04832b717', 'Engineer', '2025-10-27 07:27:56'),
(46, 'c9747f60-de4e-4de1-9dcc-37d317c2057d', 'ec093af4-810f-4add-9b23-1d9caaa8cfa6', 'Engineer', '2025-10-27 07:32:09');

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
('1', '4', 1),
('14d9e34c-b691-4ce8-a5ef-929ace71248a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 1),
('193f9eed-2938-4305-ab65-828ac5253b30', '2', 1),
('1b9c09d2-dc91-4b5e-a62b-8c42a41958ab', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 1),
('1f540668-fa06-45ec-8881-b50c378cf648', '28534929-e527-4755-bd37-0acdd51b7b45', 1),
('2', '1', 1),
('270c74ec-9124-4eb5-9469-0253ba8530af', '28534929-e527-4755-bd37-0acdd51b7b45', 1),
('2f6d353b-53f1-4492-8878-bc93c18c5de9', '4', 1),
('3', '1', 1),
('30750fba-88ab-44ce-baf2-d0894357c67c', '1', 1),
('3140fdaf-5103-4423-bf87-11b7c1153416', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 1),
('34e67e45-92f6-4e20-a78b-a4ffe97b3775', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 1),
('3768e84a-18c0-49d9-94dd-09b44c7c9a7d', '1', 1),
('3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 1),
('3efcb87b-ce45-4a66-9d73-91259caba1d0', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 1),
('4', '4', 1),
('44ab4e8b-e3e6-431d-ad49-40d4601779b4', '3', 1),
('5', '4', 1),
('5b698e22-ba83-43c4-a39e-e6d68f98791f', '1', 1),
('5eef69ba-15ee-4414-a2e4-be4f68b8839e', '4', 1),
('6614b721-a8b4-46d2-9c80-0caab04772dc', '4', 1),
('6fbca1c7-761f-4027-ba4c-89e04832b717', '1', 1),
('70dd36b5-f587-4aa9-b544-c69542616d34', '2', 1),
('85c114ec-a416-41c0-9859-12b90dc5b488', '4', 1),
('86054531-f751-48c7-b257-222c9ccbd946', '4', 1),
('8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 1),
('8c1c0a55-2610-4081-8d12-b2a6971ffbe8', '1', 1),
('8c782887-8fd3-4f99-ac27-63054a8a1942', '1', 1),
('97c68703-a8b7-4ceb-9344-65fe4404c4ab', '3', 1),
('9ae78e96-2b61-4d2c-8058-aa4e7050221b', '1', 1),
('a5741799-938b-4d0a-a3dc-4ca1aa164708', '1', 1),
('b27b56e5-6f28-4d30-8add-4bddafa38841', '1', 1),
('b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 1),
('ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2', 1),
('bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 1),
('c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2', 1),
('c89b96f1-f916-448d-9725-2e0957cdba49', '715e81f0-4985-4981-982c-45cafb9748dc', 1),
('c9245a19-52fa-4b02-a98c-b962f2f51b3f', 'de3fc0f5-9ebf-4c47-88dd-da5a570653ae', 1),
('c9747f60-de4e-4de1-9dcc-37d317c2057d', '1', 1),
('e083a0dd-3393-44cd-b376-d876d6728d9a', '1', 1),
('e23160ec-23a4-4724-9690-adb205162afb', 'de3fc0f5-9ebf-4c47-88dd-da5a570653ae', 1),
('e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '28534929-e527-4755-bd37-0acdd51b7b45', 1),
('e79e9929-6132-41ae-ab06-65b29fe70f6c', 'c8fcdec8-4a28-4b6b-be8b-8bb0579d74bc', 1),
('e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2', 1),
('ec093af4-810f-4add-9b23-1d9caaa8cfa6', '1', 1),
('ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 1),
('f30e8b87-d047-4bca-9b34-d223170df87c', '1', 1),
('f384c704-5291-4413-8f52-dc25e10b5d4f', '1', 1),
('f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', '1', 1),
('ff2acbbb-4ec0-4214-8a30-eb1fc6e02700', '1', 1);

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
