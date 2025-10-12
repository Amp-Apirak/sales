# แก้ไขปัญหาสิทธิ์การเข้าถึงสำหรับ Role: Account Management

## ปัญหาที่พบ
User ที่มี Role = "Account Management" ไม่สามารถเพิ่มการชำระเงิน (Payment) ในโครงการได้ 
เนื่องจากระบบตรวจสอบสิทธิ์ไม่รวม Role "Account Management" ไว้

**ข้อความแจ้งเตือน**: "ไม่มีสิทธิ์เข้าถึง"

**User ที่ทดสอบ**:
- Name: Oran.gun Point IT
- Role: Account Management
- Team: All Teams

**หน้าที่เกิดปัญหา**: 
http://localhost/sales/pages/project/view_project.php?project_id=XXX
> Tab "การชำระเงิน" > เพิ่มการชำระเงิน > กรอกข้อมูล > บันทึก

---

## ไฟล์ที่แก้ไข (3 ไฟล์)

### 1. `/pages/project/save_payment.php` ✅
**บรรทัด 38**: เพิ่ม 'Account Management' ในรายการ Role ที่ได้รับอนุญาต

**ก่อนแก้ไข**:
```php
if (!in_array($role, ['Executive', 'Sale Supervisor', 'Seller'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง'], JSON_UNESCAPED_UNICODE);
    exit;
}
```

**หลังแก้ไข**:
```php
if (!in_array($role, ['Executive', 'Account Management', 'Sale Supervisor', 'Seller'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง'], JSON_UNESCAPED_UNICODE);
    exit;
}
```

### 2. `/pages/project/delete_payment.php` ✅
**บรรทัด 12**: เพิ่ม 'Account Management' ในรายการ Role ที่ได้รับอนุญาต

**ก่อนแก้ไข**:
```php
if (!in_array($_SESSION['role'] ?? '', ['Executive', 'Sale Supervisor', 'Seller'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง'], JSON_UNESCAPED_UNICODE);
    exit;
}
```

**หลังแก้ไข**:
```php
if (!in_array($_SESSION['role'] ?? '', ['Executive', 'Account Management', 'Sale Supervisor', 'Seller'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง'], JSON_UNESCAPED_UNICODE);
    exit;
}
```

### 3. `/pages/project/save_customer_ajax.php` ✅
**บรรทัด 13**: เพิ่ม 'Account Management' ในรายการ Role ที่ได้รับอนุญาต

**ก่อนแก้ไข**:
```php
if (!in_array($role, ['Executive', 'Sale Supervisor', 'Seller'])) {
    header("Location: unauthorized.php");
    exit();
}
```

**หลังแก้ไข**:
```php
if (!in_array($role, ['Executive', 'Account Management', 'Sale Supervisor', 'Seller'])) {
    header("Location: unauthorized.php");
    exit();
}
```

---

## ไฟล์ที่มี Account Management อยู่แล้ว (ไม่ต้องแก้ไข)

### ✅ `/pages/project/edit_project.php` - บรรทัด 15
```php
if (!in_array($role, ['Executive', 'Account Management', 'Sale Supervisor', 'Seller'])) {
```

### ✅ `/pages/project/delete_project.php` - บรรทัด 12
```php
if (!in_array($role, ['Executive', 'Account Management', 'Sale Supervisor', 'Seller'])) {
```

### ✅ `/pages/project/add_project.php` - บรรทัด 26
```php
if (!in_array($role, ['Executive', 'Account Management', 'Sale Supervisor', 'Seller'])) {
```

---

## การทดสอบ

### ขั้นตอนการทดสอบ:

1. **Login** ด้วย User ที่มี Role = "Account Management"
   - Username: Oran.gun
   - Team: All Teams

2. **เข้าหน้าโครงการ**
   ```
   http://localhost/sales/pages/project/view_project.php?project_id=XXX
   ```

3. **ทดสอบเพิ่มการชำระเงิน**
   - คลิก Tab "การชำระเงิน"
   - คลิก "เพิ่มการชำระเงิน"
   - กรอกข้อมูล:
     * งวดที่: 1
     * จำนวนเงิน: 10000
     * เปอร์เซ็นต์: 10
     * วันที่ครบกำหนด: 2025-12-31
     * สถานะ: รอชำระ
   - คลิก "บันทึก"
   - **ผลลัพธ์**: ✅ บันทึกสำเร็จ (ไม่แสดง "ไม่มีสิทธิ์เข้าถึง")

4. **ทดสอบลบการชำระเงิน**
   - คลิกปุ่ม "ลบ" ที่งวดการชำระเงิน
   - ยืนยันการลบ
   - **ผลลัพธ์**: ✅ ลบสำเร็จ

5. **ทดสอบเพิ่มลูกค้าใหม่ (AJAX)**
   - เข้าหน้าเพิ่มโครงการ
   - คลิก "เพิ่มลูกค้าใหม่"
   - กรอกข้อมูลลูกค้า
   - **ผลลัพธ์**: ✅ บันทึกสำเร็จ

---

## สรุปสิทธิ์ของ Role: Account Management

หลังจากแก้ไข Role "Account Management" สามารถ:

| ฟีเจอร์ | สิทธิ์ | หมายเหตุ |
|---------|--------|----------|
| เพิ่มโครงการ | ✅ | add_project.php |
| แก้ไขโครงการ | ✅ | edit_project.php |
| ลบโครงการ | ✅ | delete_project.php (ในทีมที่สังกัด) |
| เพิ่มการชำระเงิน | ✅ | save_payment.php |
| ลบการชำระเงิน | ✅ | delete_payment.php |
| เพิ่มลูกค้าใหม่ | ✅ | save_customer_ajax.php |

---

## RBAC - Role Hierarchy (อัพเดท)

### Account Management
- **Access**: Team data (ทีมที่สังกัด)
- **Permissions**: 
  - จัดการโครงการในทีม (CRUD)
  - จัดการการชำระเงิน (CRUD)
  - จัดการลูกค้า (CRUD)
  - ดูข้อมูลการเงิน
- **Team Switcher**: สามารถสลับระหว่างทีมที่สังกัดหรือ ALL

---

## หมายเหตุสำหรับนักพัฒนา

เมื่อต้องการเพิ่มฟีเจอร์ใหม่ที่เกี่ยวข้องกับการจัดการโครงการ กรุณาตรวจสอบว่า Role ที่อนุญาตครบถ้วนหรือไม่:

```php
// Template สำหรับตรวจสอบสิทธิ์
$allowed_roles = ['Executive', 'Account Management', 'Sale Supervisor', 'Seller'];

if (!in_array($role, $allowed_roles)) {
    // ไม่มีสิทธิ์เข้าถึง
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง']);
    exit;
}
```

**Engineer Role**: ไม่มีสิทธิ์จัดการโครงการ (เข้าถึงได้เฉพาะ Tasks, Service Tickets)

---

**แก้ไขโดย**: Claude Code  
**วันที่**: 2025-10-10  
**Version**: 1.0  
**Status**: ✅ Completed
