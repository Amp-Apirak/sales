# การทดสอบระบบกรองสถานะโครงการจาก Dashboard

## สรุปการแก้ไข
ปรับปรุงระบบให้การคลิกที่การ์ดสถานะในหน้า Dashboard นำไปยังหน้า Project พร้อมกรองข้อมูลตามสถานะที่เลือก

## รายการที่แก้ไข

### 1. index.php (Dashboard)
- เพิ่มลิงก์ให้การ์ดสถานะโครงการทั้ง 4 แบบ:
  - โครงการที่ชนะ (WIN): `?status=ชนะ (Win)`
  - โครงการกำลังดำเนินการ: `?status=ongoing`
  - โครงการที่แพ้: `?status=แพ้ (Loss)`
  - โครงการที่ยกเลิก: `?status=ยกเลิก (Cancled)`

### 2. project.php (หน้าโครงการ)
- **บรรทัด 38-55**: เพิ่มการรับค่าจาก URL GET parameters
- **บรรทัด 44-47**: เพิ่มตรรกะพิเศษสำหรับ `status=ongoing` 
  - แปลงเป็น 4 สถานะ: นำเสนอโครงการ, ใบเสนอราคา, ยื่นประมูล, รอการพิจารณา
- **บรรทัด 257-270**: ปรับ WHERE clause รองรับทั้ง single status และ multiple statuses
- **บรรทัด 817-834**: เพิ่มตัวเลือก "โครงการกำลังดำเนินการ (ทั้งหมด)" ใน dropdown

### 3. css_dashboard.php
- เพิ่ม CSS hover effects สำหรับการ์ดที่คลิกได้

## วิธีทดสอบ

### ขั้นตอนที่ 1: ทดสอบการ์ด "โครงการที่ชนะ (WIN)"
1. เข้าหน้า Dashboard: http://localhost/sales/index.php
2. คลิกที่การ์ด "โครงการที่ชนะ (WIN)"
3. ตรวจสอบว่า:
   - URL เปลี่ยนเป็น: `project.php?status=ชนะ (Win)`
   - ตารางแสดงเฉพาะโครงการสถานะ "ชนะ (Win)"
   - Dropdown สถานะแสดง "ชนะ (Win)" เป็น selected

### ขั้นตอนที่ 2: ทดสอบการ์ด "โครงการกำลังดำเนินการ"
1. กลับไปหน้า Dashboard
2. คลิกที่การ์ด "โครงการกำลังดำเนินการ"
3. ตรวจสอบว่า:
   - URL เปลี่ยนเป็น: `project.php?status=ongoing`
   - ตารางแสดงโครงการที่มีสถานะใดสถานะหนึ่งจาก:
     * นำเสนอโครงการ (Presentations)
     * ใบเสนอราคา (Quotation)
     * ยื่นประมูล (Bidding)
     * รอการพิจารณา (On Hold)
   - Dropdown สถานะแสดง "โครงการกำลังดำเนินการ (ทั้งหมด)" เป็น selected

### ขั้นตอนที่ 3: ทดสอบการ์ด "โครงการที่แพ้"
1. กลับไปหน้า Dashboard
2. คลิกที่การ์ด "โครงการที่แพ้"
3. ตรวจสอบว่า:
   - URL เปลี่ยนเป็น: `project.php?status=แพ้ (Loss)`
   - ตารางแสดงเฉพาะโครงการสถานะ "แพ้ (Loss)"
   - Dropdown สถานะแสดง "แพ้ (Loss)" เป็น selected

### ขั้นตอนที่ 4: ทดสอบการ์ด "โครงการที่ยกเลิก"
1. กลับไปหน้า Dashboard
2. คลิกที่การ์ด "โครงการที่ยกเลิก"
3. ตรวจสอบว่า:
   - URL เปลี่ยนเป็น: `project.php?status=ยกเลิก (Cancled)`
   - ตารางแสดงเฉพาะโครงการสถานะ "ยกเลิก (Cancled)"
   - Dropdown สถานะแสดง "ยกเลิก (Cancled)" เป็น selected

### ขั้นตอนที่ 5: ทดสอบการทำงานร่วมกับ Team Switcher
1. เลือกทีมใดทีมหนึ่งจาก Team Switcher
2. คลิกที่การ์ดสถานะใดๆ
3. ตรวจสอบว่า:
   - ตารางแสดงเฉพาะโครงการของทีมที่เลือก + สถานะที่คลิก
   - สามารถเปลี่ยนทีมและกรองข้อมูลใหม่ได้

### ขั้นตอนที่ 6: ทดสอบการใช้ฟอร์มค้นหา
1. จากหน้าที่กรองสถานะแล้ว
2. ลองค้นหาเพิ่มด้วยฟิลด์อื่นๆ (ชื่อโครงการ, สินค้า, ลูกค้า)
3. ตรวจสอบว่าระบบยังคงรักษาค่าสถานะที่เลือกไว้

## การตรวจสอบเพิ่มเติม

### ตรวจสอบ Network/Console
1. เปิด Developer Tools (F12)
2. ดู Console ตรวจสอบว่าไม่มี JavaScript error
3. ดู Network tab ตรวจสอบ SQL query ที่ส่งไป

### ตรวจสอบข้อมูลในตาราง
1. นับจำนวนแถวในตาราง
2. เปรียบเทียบกับตัวเลขในการ์ดบน Dashboard
3. ตรวจสอบว่าทุกแถวมีสถานะที่ถูกต้อง

## ปัญหาที่อาจพบและวิธีแก้

### ปัญหา: URL แสดงผลแปลกๆ (มีเครื่องหมาย %)
**สาเหตุ**: URL encoding สำหรับอักขระพิเศษ (เช่น วงเล็บ, ช่องว่าง)
**แก้ไข**: ไม่ต้องกังวล - นี่เป็นการทำงานปกติ PHP จะ decode อัตโนมัติ

### ปัญหา: Dropdown ไม่แสดงค่า selected
**ตรวจสอบ**: 
- ค่า status ใน URL ตรงกับค่าในฐานข้อมูลหรือไม่
- กรณี "ongoing" จะแสดงตัวเลือกพิเศษแทน

### ปัญหา: แสดงโครงการทั้งหมด ไม่กรอง
**ตรวจสอบ**:
- ดูค่า `$_GET['status']` ว่ามีค่าหรือไม่
- ตรวจสอบ SQL query ว่ามี WHERE status = ... หรือไม่
- ดู error log ที่ `/mnt/c/xampp/htdocs/sales/logs/`

## URL สำหรับทดสอบด้วยตนเอง

```
# ทดสอบโดยตรงจาก URL bar
http://localhost/sales/pages/project/project.php?status=ชนะ (Win)
http://localhost/sales/pages/project/project.php?status=ongoing
http://localhost/sales/pages/project/project.php?status=แพ้ (Loss)
http://localhost/sales/pages/project/project.php?status=ยกเลิก (Cancled)

# URL encoded (ใช้ได้เหมือนกัน)
http://localhost/sales/pages/project/project.php?status=%E0%B8%8A%E0%B8%99%E0%B8%B0%20(Win)
http://localhost/sales/pages/project/project.php?status=%E0%B9%81%E0%B8%9E%E0%B9%89%20(Loss)
http://localhost/sales/pages/project/project.php?status=%E0%B8%A2%E0%B8%81%E0%B9%80%E0%B8%A5%E0%B8%B4%E0%B8%81%20(Cancled)
```

## ไฟล์ที่เกี่ยวข้อง

```
/mnt/c/xampp/htdocs/sales/
├── index.php (บรรทัด 873-1006)
├── pages/project/project.php (บรรทัด 38-55, 257-270, 817-834)
└── css_dashboard.php (บรรทัด 94-107)
```

---
**สร้างเมื่อ**: 2025-10-10
**Version**: 1.0
