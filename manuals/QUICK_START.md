# 🚀 Service Tickets - Quick Start Guide

## ⚡ เริ่มใช้งานใน 5 นาที!

### ขั้นตอนที่ 1: Import Database (2 นาที)

#### **Windows (XAMPP)**
```bash
# เปิด Command Prompt
cd C:\xampp\htdocs\sales

# Import Schema
C:\xampp\mysql\bin\mysql.exe -u root -p1234 sales_db < config\service_tickets_schema.sql
```

#### **Linux/Mac**
```bash
cd /path/to/sales
mysql -u root -p sales_db < config/service_tickets_schema.sql
```

---

### ขั้นตอนที่ 2: Enable Event Scheduler (30 วินาที)

```bash
# Windows
C:\xampp\mysql\bin\mysql.exe -u root -p1234 -e "SET GLOBAL event_scheduler = ON;"

# Linux/Mac
mysql -u root -p -e "SET GLOBAL event_scheduler = ON;"
```

**หรือผ่าน phpMyAdmin:**
```sql
-- ไปที่ phpMyAdmin > SQL
SET GLOBAL event_scheduler = ON;
```

---

### ขั้นตอนที่ 3: สร้างโฟลเดอร์ Upload (30 วินาที)

#### **Windows**
```bash
cd C:\xampp\htdocs\sales
mkdir uploads\service_tickets
```

#### **Linux/Mac**
```bash
cd /path/to/sales
mkdir -p uploads/service_tickets
chmod 755 uploads/service_tickets
```

---

### ขั้นตอนที่ 4: ทดสอบระบบ (2 นาที)

#### **4.1 ตรวจสอบ Database**
```sql
-- ตรวจสอบตาราง (ต้องมี 8 ตาราง)
SHOW TABLES LIKE 'service_%';

-- ตรวจสอบ Event Scheduler
SHOW VARIABLES LIKE 'event_scheduler';
-- ผลลัพธ์: event_scheduler = ON
```

#### **4.2 สร้าง Ticket ทดสอบ**
1. เปิดเบราว์เซอร์: `http://localhost/sales/pages/service/add_account.php`
2. Login เข้าระบบ
3. กรอกข้อมูล Ticket:
   - Project: เลือกโครงการที่มี
   - Ticket Type: Incident
   - Subject: ทดสอบระบบ Service Ticket
   - Priority: High
   - Job Owner: เลือกคนรับผิดชอบ
4. คลิก **"สร้าง Ticket"**
5. ถ้าสำเร็จจะเห็น:
   ```
   ✅ สร้าง Ticket สำเร็จ
   Ticket No: TCK-202510-0001
   ```

#### **4.3 ตรวจสอบข้อมูล**
```sql
-- ดู Ticket ที่สร้าง
SELECT * FROM vw_service_tickets_full ORDER BY created_at DESC LIMIT 1;

-- ดู Dashboard Metrics
SELECT * FROM vw_service_tickets_metrics;
```

---

## ✅ เสร็จแล้ว! ระบบพร้อมใช้งาน

### 🎯 **สิ่งที่คุณทำได้ตอนนี้:**

1. **สร้าง Ticket ใหม่**
   - URL: `http://localhost/sales/pages/service/add_account.php`

2. **ดูรายการ Ticket**
   - URL: `http://localhost/sales/pages/service/index.php`

3. **เรียก API โดยตรง**
   - GET Tickets: `http://localhost/sales/pages/service/api/get_tickets.php`
   - ผลลัพธ์: JSON

---

## 📱 **API Endpoints**

### 1. **GET Tickets**
```
GET /sales/pages/service/api/get_tickets.php

Parameters:
- ticket_id (optional)
- status (optional)
- priority (optional)
- search (optional)
- limit (optional, default: 20)
- offset (optional, default: 0)

Response:
{
  "success": true,
  "data": [...],
  "metrics": {...},
  "pagination": {...}
}
```

### 2. **Create Ticket**
```
POST /sales/pages/service/api/create_ticket.php

Body (FormData):
- csrf_token (required)
- project_id (required)
- subject (required)
- ticket_type
- priority
- status
- ... (ดูใน add_account.php)

Response:
{
  "success": true,
  "message": "สร้าง Ticket สำเร็จ",
  "data": {
    "ticket_id": "...",
    "ticket_no": "TCK-202510-0001",
    "redirect": "..."
  }
}
```

### 3. **Update Ticket**
```
POST /sales/pages/service/api/update_ticket.php

Body (FormData):
- csrf_token (required)
- ticket_id (required)
- ... (ฟิลด์ที่ต้องการอัพเดต)

Response:
{
  "success": true,
  "message": "อัพเดต Ticket สำเร็จ"
}
```

### 4. **Upload Files**
```
POST /sales/pages/service/api/upload_attachment.php

Body (FormData):
- csrf_token (required)
- ticket_id (required)
- attachments[] (files, multiple)

Response:
{
  "success": true,
  "message": "อัปโหลดไฟล์สำเร็จ 2 ไฟล์",
  "data": [...]
}
```

---

## 🧪 **ทดสอบด้วย Postman/Insomnia**

### ตัวอย่าง: Create Ticket

#### **Request:**
```
POST http://localhost/sales/pages/service/api/create_ticket.php
Content-Type: multipart/form-data

Body:
{
  "csrf_token": "YOUR_SESSION_TOKEN",
  "project_id": "PROJECT_UUID",
  "ticket_type": "Incident",
  "subject": "ทดสอบจาก Postman",
  "description": "ทดสอบการสร้าง Ticket ผ่าน API",
  "status": "New",
  "priority": "High",
  "urgency": "High",
  "impact": "Department",
  "job_owner": "USER_UUID",
  "source": "Portal",
  "sla_target": "4",
  "created_by": "USER_UUID"
}
```

#### **Response:**
```json
{
  "success": true,
  "message": "สร้าง Ticket สำเร็จ",
  "data": {
    "ticket_id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
    "ticket_no": "TCK-202510-0002",
    "redirect": "http://localhost/sales/pages/service/view_ticket.php?id=..."
  }
}
```

---

## 🔍 **Troubleshooting**

### ❌ ปัญหา: "Database Error: Table doesn't exist"
**แก้ไข:**
```bash
# Import Schema อีกครั้ง
mysql -u root -p1234 sales_db < config\service_tickets_schema.sql
```

### ❌ ปัญหา: "Invalid CSRF token"
**แก้ไข:**
```php
// ตรวจสอบว่ามี session_start() ใน Add_session.php
// และตรวจสอบว่ามี csrf_token ใน form
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
```

### ❌ ปัญหา: "Cannot upload file"
**แก้ไข:**
```bash
# ตรวจสอบสิทธิ์โฟลเดอร์
chmod 755 uploads/service_tickets

# หรือใน Windows
# Right click > Properties > Security > Edit > ให้สิทธิ์ Write
```

### ❌ ปัญหา: "Event Scheduler not running"
**แก้ไข:**
```sql
-- ตรวจสอบ
SHOW VARIABLES LIKE 'event_scheduler';

-- เปิดใช้งาน
SET GLOBAL event_scheduler = ON;

-- ตรวจสอบ Events
SHOW EVENTS;
```

### ❌ ปัญหา: "Ticket Number ไม่ถูกสร้าง"
**แก้ไข:**
```sql
-- ตรวจสอบ Trigger
SHOW TRIGGERS WHERE `Table` = 'service_tickets';

-- ถ้าไม่มี ให้ Import Schema ใหม่
```

---

## 📊 **ตรวจสอบสถานะระบบ**

### **1. ตรวจสอบตาราง**
```sql
SHOW TABLES LIKE 'service_%';
-- ผลลัพธ์: ต้องมี 8 ตาราง
```

### **2. ตรวจสอบ Triggers**
```sql
SHOW TRIGGERS WHERE `Table` LIKE 'service_%';
-- ผลลัพธ์: ต้องมี 9 triggers
```

### **3. ตรวจสอบ Views**
```sql
SHOW FULL TABLES WHERE Table_type = 'VIEW';
-- ผลลัพธ์: ต้องมี vw_service_tickets_full, vw_service_tickets_metrics, vw_service_tickets_alert
```

### **4. ตรวจสอบ Stored Procedures**
```sql
SHOW PROCEDURE STATUS WHERE Db = 'sales_db';
-- ผลลัพธ์: ต้องมี sp_update_all_sla_status, sp_create_sla_notifications, sp_get_tickets_by_user
```

### **5. ตรวจสอบ Events**
```sql
SHOW EVENTS;
-- ผลลัพธ์: ต้องมี evt_update_sla_status, evt_create_sla_notifications
```

---

## 🎯 **Next Steps**

### **ขั้นตอนถัดไป (ทำเอง):**

1. **อัพเดต index.php**
   - เปลี่ยนจาก Mock Data เป็น Real Data
   - เรียก API `get_tickets.php`

2. **สร้าง view_ticket.php**
   - แสดงรายละเอียด Ticket
   - แสดง Timeline, Attachments, Comments

3. **สร้าง edit_ticket.php**
   - แก้ไข Ticket
   - อัพเดตผ่าน API

---

## 📚 **เอกสารเพิ่มเติม**

- **คู่มือการใช้งาน:** `config/SERVICE_TICKETS_README.md`
- **คู่มือติดตั้ง:** `config/INSTALLATION_GUIDE.md`
- **สถานะการพัฒนา:** `pages/service/IMPLEMENTATION_STATUS.md`
- **Quick Start:** `pages/service/QUICK_START.md` (ไฟล์นี้)

---

## 🆘 **ต้องการความช่วยเหลือ?**

1. อ่าน Documentation
2. ตรวจสอบ Error Log (`/xampp/apache/logs/error.log`)
3. ตรวจสอบ MySQL Error Log
4. ดูตัวอย่างโค้ดใน `add_account.php`

---

**สร้างเมื่อ:** 2025-10-02
**เวอร์ชั่น:** 1.0
**ระยะเวลาติดตั้ง:** ~5 นาที
