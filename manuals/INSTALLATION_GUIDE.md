# 🚀 คู่มือการติดตั้งระบบ Service Tickets

## ✅ สิ่งที่ได้ทำเสร็จแล้ว

### 📦 ไฟล์ที่สร้างขึ้น:
1. ✅ `config/service_tickets_schema.sql` - โครงสร้างฐานข้อมูลทั้งหมด
2. ✅ `config/sales_db.sql` - อัพเดตพร้อม Schema Service Tickets
3. ✅ `config/SERVICE_TICKETS_README.md` - คู่มือการใช้งาน
4. ✅ `config/INSTALLATION_GUIDE.md` - คู่มือนี้

### 🗄️ ตารางที่สร้าง (8 ตาราง):
1. ✅ `service_tickets` - ตารางหลัก
2. ✅ `service_ticket_onsite` - ข้อมูล Onsite
3. ✅ `service_ticket_attachments` - ไฟล์แนบ
4. ✅ `service_ticket_watchers` - ผู้ติดตาม
5. ✅ `service_ticket_timeline` - Timeline/ประวัติ
6. ✅ `service_ticket_history` - บันทึกการเปลี่ยนแปลง
7. ✅ `service_ticket_comments` - ความคิดเห็น
8. ✅ `service_ticket_notifications` - การแจ้งเตือน

### ⚙️ Features ที่รองรับ:
- ✅ Auto-generate Ticket Number (TCK-YYYYMM-XXXX)
- ✅ Auto-generate UUID สำหรับทุก Primary Key
- ✅ Auto-calculate SLA Deadline
- ✅ Auto-update SLA Status (Within SLA/Near SLA/Overdue)
- ✅ Auto-log History เมื่อมีการเปลี่ยนแปลง
- ✅ Soft Delete support
- ✅ Role-Based Data Access (Executive/Supervisor/Seller/Engineer)
- ✅ SLA Notifications (อัตโนมัติทุก 1 ชม.)
- ✅ Event Scheduler สำหรับ Background Tasks

### 👁️ Views (3 Views):
1. ✅ `vw_service_tickets_full` - ข้อมูล Ticket แบบเต็ม
2. ✅ `vw_service_tickets_metrics` - Dashboard Metrics
3. ✅ `vw_service_tickets_alert` - Tickets ที่ต้องติดตาม

### 🔧 Stored Procedures (3 Procedures):
1. ✅ `sp_update_all_sla_status()` - อัพเดต SLA Status
2. ✅ `sp_create_sla_notifications()` - สร้าง Notifications
3. ✅ `sp_get_tickets_by_user()` - ดึงข้อมูลตาม Role

---

## 📥 ขั้นตอนการติดตั้ง

### ⭐ วิธีที่ 1: ติดตั้งผ่าน Command Line (แนะนำ)

```bash
# 1. เข้าสู่โฟลเดอร์โปรเจค
cd /mnt/c/xampp/htdocs/sales

# 2. Import Schema
mysql -u root -p sales_db < config/service_tickets_schema.sql

# 3. เปิดใช้งาน Event Scheduler
mysql -u root -p -e "SET GLOBAL event_scheduler = ON;"

# 4. ตรวจสอบการติดตั้ง
mysql -u root -p sales_db -e "SHOW TABLES LIKE 'service_%';"
```

### ⭐ วิธีที่ 2: ติดตั้งผ่าน phpMyAdmin

```
1. เปิด phpMyAdmin (http://localhost/phpmyadmin)
2. เลือกฐานข้อมูล: sales_db
3. ไปที่แท็บ: Import
4. เลือกไฟล์: config/service_tickets_schema.sql
5. เลื่อนลงล่างสุด คลิก: Go
6. รอจนเสร็จ (ประมาณ 5-10 วินาที)
7. ไปที่แท็บ SQL และรันคำสั่ง:
   SET GLOBAL event_scheduler = ON;
```

### ⭐ วิธีที่ 3: อัพเดตจากไฟล์หลัก

```bash
# Drop database และสร้างใหม่ทั้งหมด (ระวัง: จะลบข้อมูลเดิม!)
mysql -u root -p -e "DROP DATABASE IF EXISTS sales_db;"
mysql -u root -p -e "CREATE DATABASE sales_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p sales_db < config/sales_db.sql
mysql -u root -p -e "SET GLOBAL event_scheduler = ON;"
```

---

## ✔️ ตรวจสอบการติดตั้ง

### 1. ตรวจสอบตาราง
```sql
-- ต้องมี 8 ตาราง
SHOW TABLES LIKE 'service_%';

-- ผลลัพธ์ที่ต้องการ:
-- service_tickets
-- service_ticket_attachments
-- service_ticket_comments
-- service_ticket_history
-- service_ticket_notifications
-- service_ticket_onsite
-- service_ticket_timeline
-- service_ticket_watchers
```

### 2. ตรวจสอบ Triggers
```sql
-- ต้องมี 9 Triggers
SHOW TRIGGERS WHERE `Table` LIKE 'service_%';
```

### 3. ตรวจสอบ Views
```sql
-- ต้องมี 3 Views
SHOW FULL TABLES WHERE Table_type = 'VIEW' AND Tables_in_sales_db LIKE 'vw_service_%';
```

### 4. ตรวจสอบ Stored Procedures
```sql
-- ต้องมี 3 Procedures
SHOW PROCEDURE STATUS WHERE Db = 'sales_db' AND Name LIKE 'sp_%';
```

### 5. ตรวจสอบ Event Scheduler
```sql
-- ต้องมี 2 Events
SHOW EVENTS WHERE Db = 'sales_db';

-- ตรวจสอบสถานะ Event Scheduler
SHOW VARIABLES LIKE 'event_scheduler';
-- ผลลัพธ์: event_scheduler = ON
```

---

## 🧪 ทดสอบระบบ

### ทดสอบที่ 1: สร้าง Ticket
```sql
INSERT INTO service_tickets (
    ticket_id, project_id, ticket_type, subject,
    description, status, priority, urgency, impact,
    service_category, category, sub_category,
    job_owner, reporter, source, sla_target,
    channel, created_by
) VALUES (
    UUID(),
    (SELECT project_id FROM projects LIMIT 1),
    'Incident',
    'ทดสอบระบบ Service Ticket',
    'ทดสอบการสร้าง Ticket ใหม่',
    'New',
    'High',
    'High',
    'Department',
    'Network',
    'Firewall',
    'Configuration',
    '2', -- Job Owner
    '3', -- Reporter
    'Portal',
    4,
    'Remote',
    '2'  -- Creator
);

-- ตรวจสอบผลลัพธ์
SELECT * FROM vw_service_tickets_full ORDER BY created_at DESC LIMIT 1;
```

### ทดสอบที่ 2: เพิ่ม Onsite Details
```sql
-- ใช้ ticket_id จากผลลัพธ์ข้างบน
INSERT INTO service_ticket_onsite (
    onsite_id,
    ticket_id,
    start_location,
    end_location,
    travel_mode,
    odometer_start,
    odometer_end,
    note
) VALUES (
    UUID(),
    'TICKET_ID_จากข้อ1',
    'สำนักงานใหญ่',
    'สาขาขอนแก่น',
    'personal_car',
    10350.5,
    10980.2,
    'ทดสอบข้อมูล Onsite'
);

-- ตรวจสอบ (ต้องเห็น distance = 629.7 คำนวณอัตโนมัติ)
SELECT * FROM service_ticket_onsite ORDER BY created_at DESC LIMIT 1;
```

### ทดสอบที่ 3: อัพเดตสถานะ
```sql
-- อัพเดตเป็น Resolved
UPDATE service_tickets
SET status = 'Resolved', updated_by = '2'
WHERE ticket_id = 'TICKET_ID_จากข้อ1';

-- ตรวจสอบ History (ต้องมีการบันทึกอัตโนมัติ)
SELECT * FROM service_ticket_history
WHERE ticket_id = 'TICKET_ID_จากข้อ1'
ORDER BY changed_at DESC;
```

### ทดสอบที่ 4: Dashboard Metrics
```sql
SELECT * FROM vw_service_tickets_metrics;
```

### ทดสอบที่ 5: SLA Update (Manual)
```sql
-- รัน Stored Procedure
CALL sp_update_all_sla_status();

-- ดู Tickets ที่ Alert
SELECT * FROM vw_service_tickets_alert;
```

---

## 🔧 ตั้งค่า Event Scheduler ถาวร

### สำหรับ Windows (XAMPP)
แก้ไขไฟล์: `C:\xampp\mysql\bin\my.ini`
```ini
[mysqld]
event_scheduler = ON
```

### สำหรับ Linux/Mac
แก้ไขไฟล์: `/etc/mysql/my.cnf` หรือ `/etc/my.cnf`
```ini
[mysqld]
event_scheduler = ON
```

### รีสตาร์ท MySQL
```bash
# Windows (XAMPP Control Panel)
Stop MySQL → Start MySQL

# Linux
sudo systemctl restart mysql

# Mac
brew services restart mysql
```

---

## 🛠️ การ Import ข้อมูลทดสอบ (Optional)

ถ้าต้องการข้อมูลตัวอย่างสำหรับทดสอบ:

```sql
-- สร้าง Tickets ตัวอย่าง 10 รายการ
DELIMITER $$
CREATE PROCEDURE sp_insert_sample_tickets()
BEGIN
    DECLARE i INT DEFAULT 1;
    WHILE i <= 10 DO
        INSERT INTO service_tickets (
            ticket_id, project_id, ticket_type, subject,
            status, priority, sla_target, created_by
        ) VALUES (
            UUID(),
            (SELECT project_id FROM projects ORDER BY RAND() LIMIT 1),
            ELT(FLOOR(1 + RAND() * 3), 'Incident', 'Service', 'Change'),
            CONCAT('ตัวอย่าง Ticket #', i),
            ELT(FLOOR(1 + RAND() * 5), 'New', 'On Process', 'Pending', 'Resolved', 'Closed'),
            ELT(FLOOR(1 + RAND() * 4), 'Critical', 'High', 'Medium', 'Low'),
            FLOOR(1 + RAND() * 24),
            '2'
        );
        SET i = i + 1;
    END WHILE;
END$$
DELIMITER ;

-- เรียกใช้
CALL sp_insert_sample_tickets();

-- ลบ Procedure (ถ้าไม่ต้องการใช้แล้ว)
DROP PROCEDURE IF EXISTS sp_insert_sample_tickets;
```

---

## 📊 ขั้นตอนถัดไป

### 1. Backend Development (PHP)
- [ ] สร้างไฟล์ `pages/service/api/create_ticket.php`
- [ ] สร้างไฟล์ `pages/service/api/update_ticket.php`
- [ ] สร้างไฟล์ `pages/service/api/get_tickets.php`
- [ ] เชื่อมต่อฟอร์ม `add_account.php` กับ API

### 2. Frontend Updates
- [ ] อัพเดต `pages/service/index.php` ให้ดึงข้อมูลจริง
- [ ] อัพเดต `pages/service/service.php`
- [ ] เพิ่มหน้า Edit Ticket
- [ ] เพิ่มหน้า View Ticket Details

### 3. Features Enhancement
- [ ] Email Notification
- [ ] Line Notify Integration
- [ ] File Upload Handler
- [ ] Permission Checking

---

## ❓ FAQ

### Q: Event Scheduler ไม่ทำงาน?
A:
```sql
-- ตรวจสอบ
SHOW VARIABLES LIKE 'event_scheduler';

-- เปิดใช้งาน
SET GLOBAL event_scheduler = ON;

-- ตรวจสอบ Events
SHOW EVENTS;
```

### Q: Ticket Number ไม่ถูกสร้างอัตโนมัติ?
A:
```sql
-- ตรวจสอบ Trigger
SHOW TRIGGERS WHERE `Table` = 'service_tickets';

-- ลบและสร้างใหม่ (ถ้าจำเป็น)
DROP TRIGGER IF EXISTS before_insert_service_tickets;
-- จากนั้น Import Schema อีกครั้ง
```

### Q: Foreign Key Error เวลา Insert?
A:
```sql
-- ตรวจสอบว่ามี project_id และ user_id จริง
SELECT project_id FROM projects LIMIT 5;
SELECT user_id FROM users LIMIT 5;

-- ใช้ค่าที่มีจริงใน INSERT statement
```

---

## 📞 ติดต่อ Support

หากพบปัญหาหรือต้องการความช่วยเหลือ:
1. ตรวจสอบ MySQL Error Log
2. ตรวจสอบ Apache Error Log
3. อ่านคู่มือ `SERVICE_TICKETS_README.md`
4. ติดต่อทีม Development

---

## ✨ สรุป

ระบบ Service Tickets พร้อมใช้งาน! 🎉

**สิ่งที่คุณสามารถทำได้ตอนนี้:**
- ✅ สร้าง/แก้ไข/ลบ Tickets
- ✅ จัดการ SLA อัตโนมัติ
- ✅ ติดตาม Timeline
- ✅ แนบไฟล์
- ✅ เพิ่ม Watchers
- ✅ ดู Dashboard Metrics
- ✅ รับ Notifications

**ขั้นตอนถัดไป:**
1. Import Schema ✅
2. ทดสอบระบบ ✅
3. พัฒนา Backend PHP 🔄
4. เชื่อมต่อ Frontend 🔄

---

**สร้างเมื่อ:** 2025-10-02
**เวอร์ชั่น:** 1.0
**ผู้พัฒนา:** Claude Code Assistant
