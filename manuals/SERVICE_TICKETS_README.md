# 📋 Service Tickets Database - คู่มือการใช้งาน

## 📑 สารบัญ
1. [ภาพรวมระบบ](#ภาพรวมระบบ)
2. [โครงสร้างตาราง](#โครงสร้างตาราง)
3. [การติดตั้ง](#การติดตั้ง)
4. [Triggers อัตโนมัติ](#triggers-อัตโนมัติ)
5. [Views](#views)
6. [Stored Procedures](#stored-procedures)
7. [ตัวอย่างการใช้งาน](#ตัวอย่างการใช้งาน)

---

## 🎯 ภาพรวมระบบ

ระบบ Service Tickets ถูกออกแบบมาเพื่อจัดการ Ticket แบบครบวงจร รองรับ:
- ✅ **3 ประเภท Ticket:** Incident, Service Request, Change
- ✅ **SLA Management:** คำนวณและแจ้งเตือนอัตโนมัติ
- ✅ **Timeline Tracking:** บันทึกประวัติทุกการเปลี่ยนแปลง
- ✅ **Onsite Support:** จัดการข้อมูลการทำงาน Onsite
- ✅ **Notifications:** แจ้งเตือนอัตโนมัติเมื่อใกล้ครบ SLA
- ✅ **Role-Based Access:** กรองข้อมูลตามสิทธิ์ผู้ใช้

---

## 🗄️ โครงสร้างตาราง

### 1. `service_tickets` (ตารางหลัก)
เก็บข้อมูล Ticket หลักทั้งหมด

**ฟิลด์สำคัญ:**
- `ticket_id` - UUID (Primary Key)
- `ticket_no` - เลข Ticket อัตโนมัติ (TCK-YYYYMM-XXXX)
- `ticket_type` - Incident/Service/Change
- `status` - สถานะ (Draft, New, On Process, Resolved, Closed, etc.)
- `priority` - Critical/High/Medium/Low
- `sla_target` - เป้าหมาย SLA (ชั่วโมง)
- `sla_deadline` - วันเวลาครบ SLA (คำนวณอัตโนมัติ)
- `sla_status` - Within SLA/Near SLA/Overdue

### 2. `service_ticket_onsite`
เก็บข้อมูล Onsite Details

**ฟิลด์สำคัญ:**
- `start_location` - สถานที่เริ่มต้น
- `end_location` - สถานที่ปลายทาง
- `travel_mode` - วิธีการเดินทาง
- `odometer_start/end` - เลขไมล์
- `distance` - ระยะทาง (คำนวณอัตโนมัติ)

### 3. `service_ticket_attachments`
เก็บไฟล์แนบ (รองรับหลายไฟล์)

### 4. `service_ticket_watchers`
เก็บรายชื่อผู้ติดตาม Ticket

### 5. `service_ticket_timeline`
เก็บ Timeline/ประวัติการดำเนินการ

### 6. `service_ticket_history`
บันทึกการเปลี่ยนแปลงทุกฟิลด์

### 7. `service_ticket_comments`
ความคิดเห็น/หมายเหตุ

### 8. `service_ticket_notifications`
การแจ้งเตือน

---

## 💾 การติดตั้ง

### ขั้นตอนที่ 1: Import Schema
```bash
# วิธีที่ 1: ใช้ Command Line
mysql -u root -p sales_db < config/service_tickets_schema.sql

# วิธีที่ 2: ใช้ phpMyAdmin
# 1. เข้า phpMyAdmin
# 2. เลือกฐานข้อมูล sales_db
# 3. ไปที่แท็บ Import
# 4. เลือกไฟล์ service_tickets_schema.sql
# 5. คลิก Go
```

### ขั้นตอนที่ 2: Enable Event Scheduler (สำหรับ Auto SLA Update)
```sql
-- ตรวจสอบสถานะ Event Scheduler
SHOW VARIABLES LIKE 'event_scheduler';

-- เปิดใช้งาน Event Scheduler
SET GLOBAL event_scheduler = ON;

-- ตั้งค่าถาวรใน my.cnf หรือ my.ini
[mysqld]
event_scheduler = ON
```

### ขั้นตอนที่ 3: ทดสอบการทำงาน
```sql
-- ทดสอบสร้าง Ticket
INSERT INTO service_tickets (
    ticket_id, project_id, ticket_type, subject,
    priority, status, sla_target, created_by
) VALUES (
    UUID(),
    (SELECT project_id FROM projects LIMIT 1),
    'Incident',
    'ทดสอบระบบ Ticket',
    'High',
    'New',
    4,
    '2'
);

-- ตรวจสอบ Ticket ที่สร้าง
SELECT * FROM vw_service_tickets_full ORDER BY created_at DESC LIMIT 1;
```

---

## ⚙️ Triggers อัตโนมัติ

### 1. **Auto-generate UUID และ Ticket Number**
```sql
-- Trigger: before_insert_service_tickets
-- สร้าง UUID และเลข Ticket อัตโนมัติ (TCK-202510-0001)
```

### 2. **Auto-calculate SLA Deadline**
```sql
-- คำนวณ SLA Deadline จาก sla_target อัตโนมัติ
-- ตัวอย่าง: sla_target = 4 ชม. → sla_deadline = created_at + 4 ชม.
```

### 3. **Update SLA Status**
```sql
-- Trigger: before_update_service_tickets
-- อัปเดต sla_status อัตโนมัติ:
-- - Overdue: เลย SLA แล้ว
-- - Near SLA: เหลือเวลาน้อยกว่า 4 ชม.
-- - Within SLA: ยังไม่ใกล้ครบ SLA
```

### 4. **Auto-log History**
```sql
-- Trigger: after_update_service_tickets
-- บันทึกการเปลี่ยนแปลง status, priority, job_owner อัตโนมัติ
```

---

## 👁️ Views

### 1. `vw_service_tickets_full`
แสดงข้อมูล Ticket พร้อมรายละเอียดทั้งหมด (Join ทุกตาราง)

```sql
SELECT * FROM vw_service_tickets_full;
```

### 2. `vw_service_tickets_metrics`
Dashboard Metrics สำหรับแสดงสถิติ

```sql
SELECT * FROM vw_service_tickets_metrics;

-- ผลลัพธ์:
-- total_tickets: 128
-- on_process: 32
-- pending: 18
-- resolved: 64
-- closed: 10
-- sla_overdue: 5
```

### 3. `vw_service_tickets_alert`
Tickets ที่ต้องติดตาม (SLA ใกล้หมด หรือ Priority สูง)

```sql
SELECT * FROM vw_service_tickets_alert;
```

---

## 🔧 Stored Procedures

### 1. `sp_update_all_sla_status()`
อัปเดต SLA Status ของทุก Ticket

```sql
-- เรียกใช้ Manual
CALL sp_update_all_sla_status();

-- หรือรันอัตโนมัติทุก 30 นาทีผ่าน Event Scheduler
```

### 2. `sp_create_sla_notifications()`
สร้าง Notification สำหรับ Tickets ที่ใกล้ครบ SLA

```sql
-- เรียกใช้ Manual
CALL sp_create_sla_notifications();

-- หรือรันอัตโนมัติทุก 1 ชั่วโมงผ่าน Event Scheduler
```

### 3. `sp_get_tickets_by_user(user_id, role)`
ดึงข้อมูล Ticket ตามสิทธิ์ผู้ใช้

```sql
-- Executive: เห็นทั้งหมด
CALL sp_get_tickets_by_user('5', 'Executive');

-- Sale Supervisor: เห็นของทีม
CALL sp_get_tickets_by_user('34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'Sale Supervisor');

-- Seller/Engineer: เห็นของตัวเอง
CALL sp_get_tickets_by_user('3', 'Seller');
```

---

## 📝 ตัวอย่างการใช้งาน

### 1. สร้าง Ticket ใหม่
```sql
INSERT INTO service_tickets (
    ticket_id,
    project_id,
    ticket_type,
    subject,
    description,
    status,
    priority,
    urgency,
    impact,
    service_category,
    category,
    sub_category,
    job_owner,
    reporter,
    source,
    sla_target,
    channel,
    created_by
) VALUES (
    UUID(),
    'PROJECT_UUID',
    'Incident',
    'เครื่อง Server ล่ม',
    'Server Production ไม่สามารถเข้าใช้งานได้',
    'New',
    'Critical',
    'High',
    'Organization',
    'Infrastructure',
    'Server',
    'OS Crash',
    'USER_UUID',
    'REPORTER_UUID',
    'Call Center',
    2,
    'Remote',
    'CREATOR_UUID'
);
```

### 2. เพิ่มข้อมูล Onsite (ถ้า Channel = 'Onsite')
```sql
INSERT INTO service_ticket_onsite (
    onsite_id,
    ticket_id,
    start_location,
    end_location,
    travel_mode,
    odometer_start,
    odometer_end
) VALUES (
    UUID(),
    'TICKET_UUID',
    'สำนักงานใหญ่ บางนา',
    'สาขาขอนแก่น',
    'personal_car',
    10350.5,
    10980.2
);
```

### 3. เพิ่มไฟล์แนบ
```sql
INSERT INTO service_ticket_attachments (
    attachment_id,
    ticket_id,
    file_name,
    file_path,
    file_size,
    file_type,
    uploaded_by
) VALUES (
    UUID(),
    'TICKET_UUID',
    'screenshot_error.png',
    '/uploads/tickets/screenshot_error.png',
    245678,
    'png',
    'USER_UUID'
);
```

### 4. เพิ่ม Watchers (ผู้ติดตาม)
```sql
INSERT INTO service_ticket_watchers (watcher_id, ticket_id, user_id, added_by)
VALUES
    (UUID(), 'TICKET_UUID', 'USER1_UUID', 'ADMIN_UUID'),
    (UUID(), 'TICKET_UUID', 'USER2_UUID', 'ADMIN_UUID');
```

### 5. เพิ่ม Timeline Entry
```sql
INSERT INTO service_ticket_timeline (
    timeline_id,
    ticket_id,
    `order`,
    actor,
    action,
    detail
) VALUES (
    UUID(),
    'TICKET_UUID',
    1,
    'Supaporn N. (Service Desk)',
    'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น',
    'ระบุอาการเบื้องต้นและแนบภาพหน้าจอ'
);
```

### 6. อัปเดตสถานะ Ticket
```sql
UPDATE service_tickets
SET
    status = 'Resolved',
    updated_by = 'USER_UUID',
    updated_at = NOW()
WHERE ticket_id = 'TICKET_UUID';

-- History จะถูกบันทึกอัตโนมัติผ่าน Trigger
```

### 7. ปิด Ticket
```sql
UPDATE service_tickets
SET
    status = 'Closed',
    updated_by = 'USER_UUID'
WHERE ticket_id = 'TICKET_UUID';

-- closed_at จะถูกบันทึกอัตโนมัติ
```

### 8. Soft Delete Ticket
```sql
UPDATE service_tickets
SET
    deleted_at = NOW(),
    updated_by = 'USER_UUID'
WHERE ticket_id = 'TICKET_UUID';
```

### 9. ดึงข้อมูล Dashboard
```sql
-- Metrics Overview
SELECT * FROM vw_service_tickets_metrics;

-- Tickets ที่ต้องติดตาม
SELECT * FROM vw_service_tickets_alert;

-- Tickets ทั้งหมด (Full Info)
SELECT * FROM vw_service_tickets_full
WHERE deleted_at IS NULL
ORDER BY created_at DESC
LIMIT 20;
```

### 10. ค้นหา Ticket
```sql
-- ค้นหาตาม Ticket Number
SELECT * FROM vw_service_tickets_full
WHERE ticket_no = 'TCK-202510-0001';

-- ค้นหาตาม Status และ Priority
SELECT * FROM vw_service_tickets_full
WHERE status IN ('On Process', 'Pending')
  AND priority IN ('Critical', 'High')
  AND deleted_at IS NULL;

-- ค้นหา Tickets ของ Team
SELECT vw.*
FROM vw_service_tickets_full vw
INNER JOIN users u ON vw.job_owner = u.user_id
WHERE u.team_id = 'TEAM_UUID'
  AND vw.deleted_at IS NULL;
```

---

## 🔔 Notifications

### ดึง Notifications ของ User
```sql
SELECT
    n.notification_id,
    n.type,
    n.message,
    n.is_read,
    n.created_at,
    t.ticket_no,
    t.subject
FROM service_ticket_notifications n
INNER JOIN service_tickets t ON n.ticket_id = t.ticket_id
WHERE n.user_id = 'USER_UUID'
  AND n.is_read = 0
ORDER BY n.created_at DESC;
```

### ทำเครื่องหมายว่าอ่านแล้ว
```sql
UPDATE service_ticket_notifications
SET
    is_read = 1,
    read_at = NOW()
WHERE notification_id = 'NOTIFICATION_UUID';
```

---

## 📊 Reports & Analytics

### 1. รายงาน SLA Performance
```sql
SELECT
    DATE(created_at) AS date,
    COUNT(*) AS total_tickets,
    SUM(CASE WHEN sla_status = 'Within SLA' THEN 1 ELSE 0 END) AS within_sla,
    SUM(CASE WHEN sla_status = 'Near SLA' THEN 1 ELSE 0 END) AS near_sla,
    SUM(CASE WHEN sla_status = 'Overdue' THEN 1 ELSE 0 END) AS overdue,
    ROUND(SUM(CASE WHEN sla_status = 'Within SLA' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) AS sla_percentage
FROM service_tickets
WHERE deleted_at IS NULL
GROUP BY DATE(created_at)
ORDER BY date DESC
LIMIT 30;
```

### 2. รายงานประสิทธิภาพทีม
```sql
SELECT
    t.team_name,
    COUNT(st.ticket_id) AS total_tickets,
    AVG(TIMESTAMPDIFF(HOUR, st.created_at, st.resolved_at)) AS avg_resolution_hours,
    SUM(CASE WHEN st.status = 'Closed' THEN 1 ELSE 0 END) AS closed_tickets
FROM service_tickets st
INNER JOIN users u ON st.job_owner = u.user_id
INNER JOIN teams t ON u.team_id = t.team_id
WHERE st.deleted_at IS NULL
GROUP BY t.team_name
ORDER BY total_tickets DESC;
```

### 3. Top Issues
```sql
SELECT
    service_category,
    category,
    COUNT(*) AS ticket_count
FROM service_tickets
WHERE deleted_at IS NULL
  AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY service_category, category
ORDER BY ticket_count DESC
LIMIT 10;
```

---

## 🛠️ Maintenance

### Backup ข้อมูล
```bash
# Backup ตาราง Service Tickets
mysqldump -u root -p sales_db \
    service_tickets \
    service_ticket_onsite \
    service_ticket_attachments \
    service_ticket_watchers \
    service_ticket_timeline \
    service_ticket_history \
    service_ticket_comments \
    service_ticket_notifications \
    > service_tickets_backup_$(date +%Y%m%d).sql
```

### ลบข้อมูลเก่า (Archived)
```sql
-- ลบ Tickets ที่ปิดเกิน 1 ปี (Soft Delete)
UPDATE service_tickets
SET deleted_at = NOW()
WHERE status = 'Closed'
  AND closed_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)
  AND deleted_at IS NULL;

-- ลบ Notifications เก่าเกิน 3 เดือน
DELETE FROM service_ticket_notifications
WHERE created_at < DATE_SUB(NOW(), INTERVAL 3 MONTH);
```

---

## 🆘 Troubleshooting

### ปัญหา: Event Scheduler ไม่ทำงาน
```sql
-- ตรวจสอบสถานะ
SHOW PROCESSLIST;
SHOW EVENTS;

-- เปิดใช้งาน
SET GLOBAL event_scheduler = ON;
```

### ปัญหา: Ticket Number ซ้ำ
```sql
-- ตรวจสอบ Ticket Number ซ้ำ
SELECT ticket_no, COUNT(*)
FROM service_tickets
GROUP BY ticket_no
HAVING COUNT(*) > 1;

-- แก้ไขโดยสร้าง Ticket Number ใหม่
UPDATE service_tickets
SET ticket_no = CONCAT('TCK-', DATE_FORMAT(NOW(), '%Y%m'), '-', LPAD(ticket_id, 4, '0'))
WHERE ticket_no IN (SELECT dup_ticket_no FROM (...));
```

### ปัญหา: SLA Status ไม่อัปเดต
```sql
-- รัน Manual
CALL sp_update_all_sla_status();

-- หรือตรวจสอบ Event
SHOW EVENTS WHERE name = 'evt_update_sla_status';
```

---

## 📞 Support

หากพบปัญหาหรือต้องการความช่วยเหลือ:
1. ตรวจสอบ Error Log ของ MySQL
2. ตรวจสอบ Triggers และ Events
3. ติดต่อทีม Development

---

## 📄 License

Copyright © 2025 Point IT Consulting Co., Ltd.
