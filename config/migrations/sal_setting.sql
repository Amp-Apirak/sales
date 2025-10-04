-- --------------------------------------------------------
-- SLA configuration tables and seed data
-- --------------------------------------------------------

-- Impacts master
CREATE TABLE IF NOT EXISTS `service_sla_impacts` (
  `impact_id` char(36) NOT NULL,
  `impact_name` varchar(100) NOT NULL,
  `impact_level` enum('High','Medium','Low') NOT NULL DEFAULT 'Medium',
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`impact_id`),
  UNIQUE KEY `uniq_impact_name` (`impact_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SLA hours by Priority
CREATE TABLE IF NOT EXISTS `service_sla_targets` (
  `id` char(36) NOT NULL,
  `priority` enum('Critical','High','Medium','Low') NOT NULL,
  `sla_hours` int(11) NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Impact × Urgency → Priority mapping
CREATE TABLE IF NOT EXISTS `service_sla_priority_matrix` (
  `id` char(36) NOT NULL,
  `impact_id` char(36) NOT NULL,
  `urgency` enum('High','Medium','Low') NOT NULL,
  `priority` enum('Critical','High','Medium','Low') NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_impact_urgency` (`impact_id`,`urgency`),
  CONSTRAINT `fk_matrix_impact` FOREIGN KEY (`impact_id`) REFERENCES `service_sla_impacts` (`impact_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Per-impact SLA time matrix (Priority × Urgency → SLA hours)
CREATE TABLE IF NOT EXISTS `service_sla_time_matrix` (
  `id` char(36) NOT NULL,
  `impact_id` char(36) NOT NULL,
  `urgency` enum('High','Medium','Low') NOT NULL,
  `priority` enum('Critical','High','Medium','Low') NOT NULL,
  `sla_hours` int(11) NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_impact_priority_urgency` (`impact_id`,`priority`,`urgency`),
  CONSTRAINT `fk_time_matrix_impact` FOREIGN KEY (`impact_id`) REFERENCES `service_sla_impacts` (`impact_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Seed default SLA targets (idempotent)
INSERT INTO `service_sla_targets` (`id`, `priority`, `sla_hours`)
VALUES
  (UUID(), 'Critical', 4),
  (UUID(), 'High', 8),
  (UUID(), 'Medium', 24),
  (UUID(), 'Low', 72)
ON DUPLICATE KEY UPDATE `sla_hours`=VALUES(`sla_hours`);

-- Seed impacts (idempotent by name)
INSERT INTO `service_sla_impacts` (`impact_id`, `impact_name`, `impact_level`, `active`)
VALUES
  (UUID(), 'Organization', 'High', 1),
  (UUID(), 'Multiple Sites', 'High', 1),
  (UUID(), 'Executive', 'High', 1),
  (UUID(), 'Site', 'Medium', 1),
  (UUID(), 'Department', 'Medium', 1),
  (UUID(), 'Application', 'Medium', 1),
  (UUID(), 'Multiple Users', 'Medium', 1),
  (UUID(), 'Remote Users', 'Low', 1),
  (UUID(), 'Single User', 'Low', 1),
  (UUID(), 'External', 'Low', 1)
ON DUPLICATE KEY UPDATE `impact_level`=VALUES(`impact_level`), `active`=VALUES(`active`);

-- Seed matrix rules (idempotent via unique key)
-- High impact group → Critical/High/Medium by urgency High/Medium/Low
INSERT INTO `service_sla_priority_matrix` (`id`, `impact_id`, `urgency`, `priority`)
SELECT UUID(), `impact_id`, 'High', 'Critical' FROM `service_sla_impacts` WHERE `impact_name` IN ('Organization','Multiple Sites','Executive')
ON DUPLICATE KEY UPDATE `priority`='Critical';
INSERT INTO `service_sla_priority_matrix` (`id`, `impact_id`, `urgency`, `priority`)
SELECT UUID(), `impact_id`, 'Medium', 'High' FROM `service_sla_impacts` WHERE `impact_name` IN ('Organization','Multiple Sites','Executive')
ON DUPLICATE KEY UPDATE `priority`='High';
INSERT INTO `service_sla_priority_matrix` (`id`, `impact_id`, `urgency`, `priority`)
SELECT UUID(), `impact_id`, 'Low', 'Medium' FROM `service_sla_impacts` WHERE `impact_name` IN ('Organization','Multiple Sites','Executive')
ON DUPLICATE KEY UPDATE `priority`='Medium';

-- Medium impact group → High/Medium/Low
INSERT INTO `service_sla_priority_matrix` (`id`, `impact_id`, `urgency`, `priority`)
SELECT UUID(), `impact_id`, 'High', 'High' FROM `service_sla_impacts` WHERE `impact_name` IN ('Site','Department','Application','Multiple Users')
ON DUPLICATE KEY UPDATE `priority`='High';
INSERT INTO `service_sla_priority_matrix` (`id`, `impact_id`, `urgency`, `priority`)
SELECT UUID(), `impact_id`, 'Medium', 'Medium' FROM `service_sla_impacts` WHERE `impact_name` IN ('Site','Department','Application','Multiple Users')
ON DUPLICATE KEY UPDATE `priority`='Medium';
INSERT INTO `service_sla_priority_matrix` (`id`, `impact_id`, `urgency`, `priority`)
SELECT UUID(), `impact_id`, 'Low', 'Low' FROM `service_sla_impacts` WHERE `impact_name` IN ('Site','Department','Application','Multiple Users')
ON DUPLICATE KEY UPDATE `priority`='Low';

-- Seed per-impact SLA time matrix with current global targets as baseline (idempotent)
-- This fills every Impact x (High/Medium/Low) Urgency x (Critical/High/Medium/Low) Priority
-- with the SLA hours from service_sla_targets so behavior remains unchanged until you customize.
INSERT INTO `service_sla_time_matrix` (`id`, `impact_id`, `urgency`, `priority`, `sla_hours`)
SELECT UUID(), i.`impact_id`, u.`urgency`, t.`priority`, t.`sla_hours`
FROM `service_sla_impacts` i
CROSS JOIN (
  SELECT 'High' AS `urgency`
  UNION ALL SELECT 'Medium'
  UNION ALL SELECT 'Low'
) u
JOIN `service_sla_targets` t ON 1=1
ON DUPLICATE KEY UPDATE `sla_hours`=VALUES(`sla_hours`);

-- Low impact group → Medium/Low/Low
INSERT INTO `service_sla_priority_matrix` (`id`, `impact_id`, `urgency`, `priority`)
SELECT UUID(), `impact_id`, 'High', 'Medium' FROM `service_sla_impacts` WHERE `impact_name` IN ('Remote Users','Single User','External')
ON DUPLICATE KEY UPDATE `priority`='Medium';
INSERT INTO `service_sla_priority_matrix` (`id`, `impact_id`, `urgency`, `priority`)
SELECT UUID(), `impact_id`, 'Medium', 'Low' FROM `service_sla_impacts` WHERE `impact_name` IN ('Remote Users','Single User','External')
ON DUPLICATE KEY UPDATE `priority`='Low';
INSERT INTO `service_sla_priority_matrix` (`id`, `impact_id`, `urgency`, `priority`)
SELECT UUID(), `impact_id`, 'Low', 'Low' FROM `service_sla_impacts` WHERE `impact_name` IN ('Remote Users','Single User','External')
ON DUPLICATE KEY UPDATE `priority`='Low';
