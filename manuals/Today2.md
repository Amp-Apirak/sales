# Employee Document Management System - Design Document
**วันที่สร้าง:** 2025-10-12
**ผู้สร้าง:** Claude Code
**เวอร์ชัน:** 1.0

---

## 📋 สรุปโครงการ

เพิ่มระบบจัดการเอกสารและลิงก์สำหรับพนักงานในหน้า `view_employees.php` โดยประยุกต์ใช้โครงสร้างจากระบบ Project ที่มีอยู่แล้ว

---

## 🎯 วัตถุประสงค์

1. เพิ่มความสามารถในการจัดการเอกสารส่วนตัวของพนักงานแต่ละคน
2. รองรับการอัปโหลดไฟล์หลายรูปแบบ (PDF, Word, Excel, Image, ZIP)
3. รองรับการแนบลิงก์จาก Google Drive, SharePoint, OneDrive
4. กำหนดสิทธิ์การเข้าถึงตาม Role-Based Access Control (RBAC)
5. ใช้โครงสร้างและ Pattern ที่มีอยู่ในระบบเดิมเพื่อความสอดคล้อง

---

## 📊 โครงสร้างฐานข้อมูล

### ตารางที่ 1: `employee_documents`
สำหรับเก็บไฟล์เอกสารที่อัปโหลด

```sql
CREATE TABLE `employee_documents` (
  `document_id` char(36) NOT NULL COMMENT 'รหัสเอกสาร (UUID)',
  `employee_id` char(36) NOT NULL COMMENT 'รหัสพนักงาน (FK: employees.id)',
  `document_name` varchar(255) NOT NULL COMMENT 'ชื่อเอกสาร',
  `document_category` varchar(50) NOT NULL COMMENT 'หมวดหมู่: resume|certificate|id_card|contract|other',
  `document_type` varchar(50) NOT NULL COMMENT 'ประเภทไฟล์: pdf|docx|xlsx|jpg|png|zip',
  `file_path` varchar(500) NOT NULL COMMENT 'path ของไฟล์',
  `file_size` bigint(20) DEFAULT NULL COMMENT 'ขนาดไฟล์ (bytes)',
  `description` text DEFAULT NULL COMMENT 'คำอธิบายเอกสาร',
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่อัปโหลด',
  `uploaded_by` char(36) DEFAULT NULL COMMENT 'ผู้อัปโหลด (FK: users.user_id)',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL COMMENT 'ผู้แก้ไข (FK: users.user_id)',
  PRIMARY KEY (`document_id`),
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_uploaded_by` (`uploaded_by`),
  KEY `idx_category` (`document_category`),
  CONSTRAINT `fk_emp_doc_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_emp_doc_uploader` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='เก็บเอกสารของพนักงาน';
```

**คำอธิบายฟิลด์:**
- `document_category`: หมวดหมู่เอกสาร
  - `resume` = เรซูเม่
  - `certificate` = ใบประกาศนียบัตร
  - `id_card` = บัตรประชาชน/Passport
  - `contract` = สัญญาจ้าง
  - `other` = เอกสารอื่นๆ

- `file_path`: รูปแบบ `uploads/employee_documents/{employee_id}/{uuid}.{ext}`

---

### ตารางที่ 2: `employee_document_links`
สำหรับเก็บลิงก์ไปยัง Cloud Storage

```sql
CREATE TABLE `employee_document_links` (
  `link_id` char(36) NOT NULL COMMENT 'รหัสลิงก์ (UUID)',
  `employee_id` char(36) NOT NULL COMMENT 'รหัสพนักงาน (FK: employees.id)',
  `link_name` varchar(255) NOT NULL COMMENT 'ชื่อลิงก์/เอกสาร',
  `link_category` varchar(50) NOT NULL COMMENT 'หมวดหมู่: drive|sharepoint|onedrive|other',
  `url` text NOT NULL COMMENT 'URL ของลิงก์',
  `description` text DEFAULT NULL COMMENT 'คำอธิบาย',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` char(36) NOT NULL COMMENT 'ผู้สร้าง (FK: users.user_id)',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL COMMENT 'ผู้แก้ไข (FK: users.user_id)',
  PRIMARY KEY (`link_id`),
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_category` (`link_category`),
  CONSTRAINT `fk_emp_link_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_emp_link_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='เก็บลิงก์เอกสารของพนักงาน';
```

**คำอธิบายฟิลด์:**
- `link_category`: หมวดหมู่ลิงก์
  - `drive` = Google Drive
  - `sharepoint` = Microsoft SharePoint
  - `onedrive` = Microsoft OneDrive
  - `other` = ลิงก์อื่นๆ

---

## 🎨 การออกแบบ UI/UX

### โครงสร้าง Tabs ใหม่

```
┌─────────────────────────────────────────────────────────────┐
│  Employee Details: [ชื่อ-นามสกุล พนักงาน]                  │
├─────────────────────────────────────────────────────────────┤
│  [ข้อมูลพนักงาน] [เอกสารแนบ] [ลิงก์เอกสาร]                │
├─────────────────────────────────────────────────────────────┤
│                    Tab Content Here                         │
└─────────────────────────────────────────────────────────────┘
```

### แถบที่ 1: ข้อมูลพนักงาน (เดิม)
- ข้อมูลส่วนตัว (Personal Information)
- ข้อมูลการติดต่อ (Contact Information)
- ข้อมูลการทำงาน (Work Information)
- ข้อมูลระบบ (System Information)

### แถบที่ 2: เอกสารแนบ (ใหม่)

**หน้าตาแถบ:**
```
┌─────────────────────────────────────────────────────────────┐
│  📄 เอกสารแนบ                            [🔼 อัปโหลดเอกสาร]  │
├─────────────────────────────────────────────────────────────┤
│  ┌───────────────────────────────────────────────────────┐  │
│  │ # │ ชื่อเอกสาร        │ หมวดหมู่    │ ขนาด │ การจัดการ│  │
│  ├───┼──────────────────┼───────────┼─────┼──────────┤  │
│  │ 1 │ Resume_2024.pdf   │ 📄 Resume  │ 2MB │ [ดู][ลบ]  │  │
│  │ 2 │ Bachelor_Deg.pdf  │ 🎓 Cert    │ 1MB │ [ดู][ลบ]  │  │
│  │ 3 │ ID_Card_Front.jpg │ 🪪 ID Card │ 500KB│[ดู][ลบ]  │  │
│  │ 4 │ Contract_2024.pdf │ 📝 Contract│ 3MB │ [ดู][ลบ]  │  │
│  └───────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

**ฟีเจอร์:**
- ✅ อัปโหลดหลายไฟล์พร้อมกัน
- ✅ Drag & Drop
- ✅ Progress Bar
- ✅ Preview ไฟล์ (PDF, Image)
- ✅ ดาวน์โหลดไฟล์
- ✅ ลบไฟล์พร้อมยืนยัน
- ✅ กรองตามหมวดหมู่
- ✅ ค้นหาชื่อเอกสาร

**หมวดหมู่เอกสาร:**
| Icon | หมวดหมู่ | ชื่อภาษาไทย | คำอธิบาย |
|------|---------|------------|---------|
| 📄 | resume | เรซูเม่ | ประวัติการทำงาน |
| 🎓 | certificate | ใบประกาศนียบัตร | ใบเกรียตินิยม, ใบวุฒิบัตร |
| 🪪 | id_card | บัตรประชาชน | บัตรประชาชน, Passport |
| 📝 | contract | สัญญาจ้าง | สัญญาจ้างงาน, NDA |
| 📋 | other | เอกสารอื่นๆ | เอกสารทั่วไป |

**ไฟล์ที่รองรับ:**
- 📕 PDF (.pdf) - สูงสุด 10MB
- 📘 Word (.doc, .docx) - สูงสุด 10MB
- 📗 Excel (.xls, .xlsx) - สูงสุด 10MB
- 🖼️ Image (.jpg, .jpeg, .png) - สูงสุด 5MB
- 📦 ZIP (.zip) - สูงสุด 20MB

---

### แถบที่ 3: ลิงก์เอกสาร (ใหม่)

**หน้าตาแถบ:**
```
┌─────────────────────────────────────────────────────────────┐
│  🔗 ลิงก์เอกสาร                              [➕ เพิ่มลิงก์]  │
├─────────────────────────────────────────────────────────────┤
│  ┌───────────────────────────────────────────────────────┐  │
│  │ # │ ชื่อลิงก์           │ หมวดหมู่ │ URL  │ การจัดการ │  │
│  ├───┼────────────────────┼─────────┼──────┼──────────┤  │
│  │ 1 │ CV_Folder          │ 📁 Drive │ 🔗  │ [แก้][ลบ] │  │
│  │ 2 │ Certificates_Drive │ 📁 Drive │ 🔗  │ [แก้][ลบ] │  │
│  │ 3 │ Work_Documents     │ 📁 SP    │ 🔗  │ [แก้][ลบ] │  │
│  └───────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

**ฟีเจอร์:**
- ✅ เพิ่มลิงก์ไปยัง Cloud Storage
- ✅ ตั้งชื่อลิงก์ที่จดจำง่าย
- ✅ เปิดลิงก์ในแท็บใหม่
- ✅ แก้ไขลิงก์
- ✅ ลบลิงก์พร้อมยืนยัน
- ✅ กรองตามหมวดหมู่
- ✅ ค้นหาชื่อลิงก์

**หมวดหมู่ลิงก์:**
| Icon | หมวดหมู่ | ชื่อภาษาไทย | Platform |
|------|---------|------------|----------|
| 📁 | drive | Google Drive | drive.google.com |
| 📁 | sharepoint | SharePoint | sharepoint.com |
| 📁 | onedrive | OneDrive | onedrive.live.com |
| 🔗 | other | ลิงก์อื่นๆ | อื่นๆ |

---

## 🔐 สิทธิ์การเข้าถึง (RBAC)

### Permission Matrix

| Role | ดูข้อมูลพนักงาน | ดูเอกสาร | เพิ่มเอกสาร | แก้ไข/ลบเอกสาร | เงื่อนไข |
|------|:-------------:|:-------:|:---------:|:-------------:|---------|
| **Executive** | ✅ ทั้งหมด | ✅ ทั้งหมด | ✅ ทั้งหมด | ✅ ทั้งหมด | สิทธิ์เต็มทุกพนักงาน |
| **Sale Supervisor** | ✅ ทีมตัวเอง | ✅ ทีมตัวเอง | ✅ ทีมตัวเอง | ✅ ทีมตัวเอง | จำกัดเฉพาะพนักงานในทีม |
| **Seller** | ❌ | ❌ | ❌ | ❌ | ไม่มีสิทธิ์เข้าถึง HR Data |
| **Engineer** | ❌ | ❌ | ❌ | ❌ | ไม่มีสิทธิ์เข้าถึง HR Data |

### เหตุผลการกำหนดสิทธิ์

1. **Executive** - เป็นผู้บริหารระดับสูง ต้องการเข้าถึงข้อมูล HR ทั้งหมด
2. **Sale Supervisor** - ต้องดูแลทีม จำเป็นต้องเข้าถึงเอกสารพนักงานในทีม
3. **Seller & Engineer** - เป็นเพียงผู้ปฏิบัติงาน ไม่จำเป็นต้องเข้าถึงข้อมูล HR

### Logic การตรวจสอบสิทธิ์

```php
// ใน view_employees.php
$canAccessDocuments = false;
$canManageDocuments = false;

if ($role === 'Executive') {
    // Executive เข้าถึงได้ทั้งหมด
    $canAccessDocuments = true;
    $canManageDocuments = true;

} elseif ($role === 'Sale Supervisor') {
    // Supervisor เข้าถึงได้เฉพาะพนักงานในทีม
    $employee_team_id = $employee['team_id'];
    $supervisor_team_ids = $_SESSION['team_ids'] ?? [];

    if ($team_id === 'ALL') {
        // ถ้าเลือก ALL Teams ให้เช็คว่าพนักงานอยู่ในทีมใดทีมหนึ่งที่ Supervisor ดูแล
        $canAccessDocuments = in_array($employee_team_id, $supervisor_team_ids);
    } else {
        // ถ้าเลือกทีมเฉพาะ ให้เช็คว่าตรงกับทีมของพนักงานหรือไม่
        $canAccessDocuments = ($employee_team_id === $team_id);
    }

    $canManageDocuments = $canAccessDocuments;

} else {
    // Seller, Engineer ไม่มีสิทธิ์
    $canAccessDocuments = false;
    $canManageDocuments = false;
}
```

### การแสดงผล UI ตามสิทธิ์

```php
<?php if ($canAccessDocuments): ?>
    <!-- แสดงแถบเอกสาร -->
    <li class="nav-item">
        <a class="nav-link" href="#documents" data-toggle="tab">
            <i class="fas fa-file-alt"></i> เอกสารแนบ
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#links" data-toggle="tab">
            <i class="fas fa-link"></i> ลิงก์เอกสาร
        </a>
    </li>
<?php endif; ?>

<?php if ($canManageDocuments): ?>
    <!-- แสดงปุ่มเพิ่ม/แก้ไข/ลบ -->
    <button class="btn btn-primary" onclick="openUploadModal()">
        <i class="fas fa-upload"></i> อัปโหลดเอกสาร
    </button>
<?php endif; ?>
```

---

## 📁 โครงสร้างไฟล์

### ไดเรกทอรี่ที่จะสร้าง

```
pages/setting/employees/
│
├── view_employees.php              (แก้ไข - เพิ่ม tabs และ logic)
│
├── tab_document/                   (โฟลเดอร์ใหม่)
│   ├── document.php                (Modal อัปโหลดเอกสาร)
│   ├── upload_document.php         (Backend: อัปโหลดไฟล์)
│   ├── get_documents.php           (Backend: ดึงรายการเอกสาร)
│   ├── view_document.php           (Backend: แสดงเอกสาร)
│   ├── download_document.php       (Backend: ดาวน์โหลดเอกสาร)
│   └── delete_document.php         (Backend: ลบเอกสาร)
│
└── tab_linkdocument/               (โฟลเดอร์ใหม่)
    ├── link_document.php           (Modal เพิ่ม/แก้ไขลิงก์)
    ├── save_document_link.php      (Backend: บันทึกลิงก์)
    ├── get_document_links.php      (Backend: ดึงรายการลิงก์)
    ├── get_link_details.php        (Backend: ดึงข้อมูลลิงก์เดียว)
    └── delete_document_link.php    (Backend: ลบลิงก์)
```

### โฟลเดอร์เก็บไฟล์อัปโหลด

```
uploads/
└── employee_documents/
    ├── {employee_id_1}/
    │   ├── {uuid_1}.pdf
    │   ├── {uuid_2}.docx
    │   └── {uuid_3}.jpg
    ├── {employee_id_2}/
    │   └── {uuid_4}.pdf
    └── ...
```

**ตัวอย่าง Path:**
```
uploads/employee_documents/3fa85f64-5717-4562-b3fc-2c963f66afa6/671a3b2c-8d4e-4f91-9c5a-1234567890ab.pdf
```

---

## 🔧 การทำงานของระบบ

### 1. การอัปโหลดเอกสาร

**Flow:**
```
User กดปุ่ม "อัปโหลดเอกสาร"
  ↓
แสดง Modal (document.php)
  ↓
User เลือกไฟล์ + กรอกข้อมูล
  - ชื่อเอกสาร
  - หมวดหมู่
  - คำอธิบาย (optional)
  ↓
JavaScript ส่ง FormData ไปยัง upload_document.php
  ↓
Backend ตรวจสอบ:
  - CSRF Token
  - ประเภทไฟล์
  - ขนาดไฟล์
  - สิทธิ์ผู้ใช้
  ↓
สร้างโฟลเดอร์ (ถ้ายังไม่มี)
  ↓
บันทึกไฟล์ด้วย UUID.extension
  ↓
บันทึกข้อมูลลง employee_documents
  ↓
ส่ง JSON Response กลับ
  ↓
แสดง SweetAlert Success
  ↓
Reload ตารางเอกสาร
```

**Validation:**
```php
// upload_document.php
$allowed_types = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'image/jpeg',
    'image/png',
    'application/zip'
];

$max_file_sizes = [
    'image' => 5 * 1024 * 1024,  // 5MB
    'document' => 10 * 1024 * 1024, // 10MB
    'zip' => 20 * 1024 * 1024   // 20MB
];
```

---

### 2. การดูเอกสาร

**Flow:**
```
User กดปุ่ม "ดู"
  ↓
เปิด view_document.php?document_id={id}
  ↓
Backend ตรวจสอบ:
  - Document exists
  - สิทธิ์ผู้ใช้
  ↓
อ่านไฟล์
  ↓
Set Header ตามประเภทไฟล์
  ↓
Output ไฟล์ (inline)
```

**ประเภทการแสดงผล:**
- PDF → แสดงใน Browser
- Image → แสดงใน Browser
- Word/Excel → ดาวน์โหลดโดยอัตโนมัติ

---

### 3. การดาวน์โหลดเอกสาร

**Flow:**
```
User กดปุ่ม "ดาวน์โหลด" หรือ filename
  ↓
เรียก download_document.php?document_id={id}
  ↓
Backend ตรวจสอบสิทธิ์
  ↓
Set Header: Content-Disposition: attachment
  ↓
Output ไฟล์
  ↓
Browser ดาวน์โหลดไฟล์
```

---

### 4. การลบเอกสาร

**Flow:**
```
User กดปุ่ม "ลบ"
  ↓
แสดง SweetAlert ยืนยัน
  ↓
User ยืนยันลบ
  ↓
Ajax POST ไปยัง delete_document.php
  ↓
Backend ตรวจสอบ:
  - CSRF Token
  - สิทธิ์ผู้ใช้
  - Document exists
  ↓
ลบไฟล์จาก Server
  ↓
ลบข้อมูลจาก Database
  ↓
ส่ง JSON Response
  ↓
แสดง SweetAlert Success
  ↓
Reload ตารางเอกสาร
```

---

### 5. การเพิ่มลิงก์

**Flow:**
```
User กดปุ่ม "เพิ่มลิงก์"
  ↓
แสดง Modal (link_document.php)
  ↓
User กรอกข้อมูล:
  - ชื่อลิงก์
  - หมวดหมู่
  - URL
  - คำอธิบาย (optional)
  ↓
JavaScript POST ไปยัง save_document_link.php
  ↓
Backend ตรวจสอบ:
  - CSRF Token
  - URL Format
  - สิทธิ์ผู้ใช้
  ↓
สร้าง UUID
  ↓
บันทึกลง employee_document_links
  ↓
ส่ง JSON Response
  ↓
แสดง SweetAlert Success
  ↓
Reload ตารางลิงก์
```

**Validation URL:**
```php
// save_document_link.php
function validateURL($url) {
    // ต้องเป็น HTTPS
    if (strpos($url, 'https://') !== 0) {
        return false;
    }

    // ใช้ filter_var ตรวจสอบ
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    return true;
}
```

---

## 🎯 Features Checklist

### แถบเอกสารแนบ
- [ ] Modal อัปโหลดเอกสาร
- [ ] Multi-file upload
- [ ] Drag & Drop zone
- [ ] Progress bar
- [ ] File type validation
- [ ] File size validation
- [ ] ตารางแสดงรายการเอกสาร (DataTables)
- [ ] กรองตามหมวดหมู่
- [ ] ค้นหาชื่อเอกสาร
- [ ] ดูเอกสาร (Preview)
- [ ] ดาวน์โหลดเอกสาร
- [ ] ลบเอกสาร (พร้อมยืนยัน)
- [ ] Export ตาราง (Excel, PDF, CSV)

### แถบลิงก์เอกสาร
- [ ] Modal เพิ่ม/แก้ไขลิงก์
- [ ] URL validation
- [ ] ตารางแสดงรายการลิงก์ (DataTables)
- [ ] กรองตามหมวดหมู่
- [ ] ค้นหาชื่อลิงก์
- [ ] เปิดลิงก์ในแท็บใหม่
- [ ] แก้ไขลิงก์
- [ ] ลบลิงก์ (พร้อมยืนยัน)
- [ ] Export ตาราง (Excel, PDF, CSV)

### Security & RBAC
- [ ] CSRF Protection
- [ ] Role-based access control
- [ ] Team-based access control (Supervisor)
- [ ] File upload security
- [ ] SQL Injection prevention (PDO)
- [ ] XSS prevention (htmlspecialchars)
- [ ] Path traversal prevention

### Database
- [ ] สร้างตาราง employee_documents
- [ ] สร้างตาราง employee_document_links
- [ ] สร้าง Foreign Keys
- [ ] สร้าง Indexes
- [ ] เพิ่ม Sample Data (ถ้าต้องการ)

---

## 📝 Code Standards

### PHP Coding Standards
1. ใช้ PDO Prepared Statements เสมอ
2. Escape output ด้วย `htmlspecialchars()` หรือ `escapeOutput()`
3. Validate input ทุกตัว
4. ใช้ UUID จาก `generateUUID()`
5. Encrypt/Decrypt ID ด้วย `encryptUserId()` / `decryptUserId()`
6. ตรวจสอบ Session และ Role ทุกหน้า
7. ใช้ CSRF Token ใน Form
8. Log errors แต่แสดง generic message ให้ User

### JavaScript Coding Standards
1. ใช้ jQuery สำหรับ AJAX
2. ใช้ SweetAlert2 สำหรับ Alert
3. ใช้ DataTables สำหรับตาราง
4. Handle errors ทุก AJAX call
5. แสดง loading indicator ขณะ upload
6. Validate ฝั่ง Client ก่อน submit

### File Naming Convention
- PHP Files: `lowercase_with_underscore.php`
- JavaScript: `camelCase` functions
- CSS Classes: `kebab-case`
- Database Tables: `lowercase_with_underscore`

---

## 🚀 Implementation Plan

### Phase 1: Database Setup
1. สร้างตาราง `employee_documents`
2. สร้างตาราง `employee_document_links`
3. Test Foreign Keys และ Constraints

### Phase 2: Backend - Document Management
1. สร้าง `upload_document.php`
2. สร้าง `get_documents.php`
3. สร้าง `view_document.php`
4. สร้าง `download_document.php`
5. สร้าง `delete_document.php`
6. Test การอัปโหลด/ลบไฟล์

### Phase 3: Backend - Link Management
1. สร้าง `save_document_link.php`
2. สร้าง `get_document_links.php`
3. สร้าง `get_link_details.php`
4. สร้าง `delete_document_link.php`
5. Test CRUD operations

### Phase 4: Frontend - UI Development
1. แก้ไข `view_employees.php` เพิ่ม tabs
2. สร้าง `tab_document/document.php` (Modal)
3. สร้าง `tab_linkdocument/link_document.php` (Modal)
4. เขียน JavaScript สำหรับ AJAX calls
5. Style UI ให้สอดคล้องกับระบบเดิม

### Phase 5: RBAC Implementation
1. เพิ่ม Logic ตรวจสอบสิทธิ์
2. ซ่อน/แสดง UI ตาม Role
3. Test สิทธิ์ทุก Role
4. Test Team-based access (Supervisor)

### Phase 6: Testing & Deployment
1. Test ทุก Feature
2. Test Security (CSRF, SQL Injection, XSS)
3. Test Performance (Large files)
4. Fix bugs
5. Deploy to Production

---

## 🐛 Known Issues & Limitations

### Limitations
1. ขนาดไฟล์สูงสุด 20MB (กำหนดโดย PHP `upload_max_filesize`)
2. ไม่รองรับการแก้ไขไฟล์ที่อัปโหลดแล้ว (ต้องลบและอัปโหลดใหม่)
3. ไม่มี Version Control สำหรับเอกสาร
4. ไม่มีการ Encrypt ไฟล์บน Server

### Future Enhancements
1. เพิ่ม File Versioning
2. เพิ่ม File Encryption
3. เพิ่ม Activity Log (Who uploaded/deleted what)
4. เพิ่ม Notification เมื่อมีการเพิ่ม/ลบเอกสาร
5. เพิ่ม Bulk Upload
6. เพิ่ม Thumbnail สำหรับ Image
7. เพิ่ม OCR สำหรับ PDF
8. เพิ่ม Search ใน PDF content

---

## 📚 References

### เอกสารอ้างอิง
- [CLAUDE.md](CLAUDE.md) - Project Documentation
- [Project Document System](pages/project/tab_document/) - ระบบเอกสารโครงการที่ใช้เป็นต้นแบบ
- [Project Link System](pages/project/tab_linkdocument/) - ระบบลิงก์โครงการที่ใช้เป็นต้นแบบ

### Libraries ที่ใช้
- **Bootstrap 4** - UI Framework
- **AdminLTE 3** - Admin Template
- **jQuery 3.x** - JavaScript Library
- **DataTables** - Table Plugin
- **SweetAlert2** - Alert/Confirm Dialog
- **Select2** - Dropdown Enhancement
- **Font Awesome 5** - Icons

---

## 📞 Support

หากมีปัญหาหรือข้อสงสัย:
1. ตรวจสอบ Console (Browser DevTools)
2. ตรวจสอบ PHP Error Log
3. ตรวจสอบ Network Tab (AJAX requests)
4. อ่าน CLAUDE.md สำหรับ Troubleshooting

---

## 📋 Change Log

### Version 1.0 (2025-10-12)
- ✨ Initial design document
- 📊 Database schema designed
- 🎨 UI/UX mockups created
- 🔐 RBAC permissions defined
- 📁 File structure planned

---

**หมายเหตุ:** เอกสารนี้จะได้รับการอัปเดตทุกครั้งที่มีการเปลี่ยนแปลงหรือเพิ่มฟีเจอร์ใหม่
