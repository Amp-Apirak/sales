# แก้ไขปัญหาโมจิคอนแสดง ???? (UTF-8 Emoji Support)

## ปัญหา
เมื่อพิมพ์โมจิคอนในกระดานสนทนา ระบบแสดงผลเป็น `????` แทนที่จะเป็นอิโมจิ

## สาเหตุ
- Database table ไม่รองรับ `utf8mb4` character set
- PHP ไม่ได้ set charset เป็น `utf8mb4` ก่อน query

## วิธีแก้ไข

### 1. แก้ไข Header Error
หาก Warning: Cannot modify header information - headers already sent

**สาเหตุ:** มีการส่ง output ก่อน header()

**การแก้ไข:**
- ✅ เพิ่ม `header('Content-Type: text/html; charset=utf-8');` ใน `Add_session.php`
- ✅ เปลี่ยน charset เป็น `utf8mb4` ใน `condb.php`
- ✅ ลบ header ที่ซ้ำซ้อนออกจากไฟล์อื่น

### 2. แก้ไข Database (รันคำสั่ง SQL)
เปิดไฟล์ `fix_utf8.sql` แล้วรันใน phpMyAdmin หรือ MySQL client:

```bash
# วิธี 1: ใน phpMyAdmin
# - เปิด Database sales_db
# - ไปที่แท็บ SQL
# - Copy โค้ดจากไฟล์ fix_utf8.sql มาวาง
# - กด Go

# วิธี 2: Command Line
mysql -u root -p sales_db < fix_utf8.sql
```

### 3. ไฟล์ที่แก้ไขแล้ว

✅ **include/Add_session.php**
- เพิ่ม `header('Content-Type: text/html; charset=utf-8');` หลัง `session_start()`

✅ **config/condb.php**
- เปลี่ยน charset จาก `utf8` เป็น `utf8mb4`
- เพิ่ม `$condb->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");`

✅ **get_discussions.php**
- เปลี่ยน `htmlspecialchars()` เป็น `htmlspecialchars($text, ENT_QUOTES, 'UTF-8')`
- เพิ่ม download links สำหรับไฟล์แนบ

✅ **post_discussion.php**
- รองรับ UTF-8 (ผ่าน condb.php)

✅ **edit_discussion.php**
- รองรับ UTF-8 (ผ่าน condb.php)

✅ **export_word.php**
- รองรับ UTF-8 (ผ่าน condb.php)

### 4. ทดสอบ

1. **รีเฟรชหน้า** กระดานสนทนา (`Ctrl + Shift + R`)
2. **ตรวจสอบ Warning:** ไม่ควรมี "Cannot modify header information"
3. **ทดสอบโมจิคอน:**
   - พิมพ์: `สวัสดีครับ 😀 👍 ❤️ 🎉`
   - กดส่งข้อความ
   - ตรวจสอบ: แสดงโมจิถูกต้อง (ไม่ใช่ ????)
4. **ทดสอบดาวน์โหลด:**
   - แนบไฟล์ (รูปหรือเอกสาร)
   - คลิกไฟล์แนบ
   - ตรวจสอบ: ดาวน์โหลดได้ทันที

### 5. ตรวจสอบการตั้งค่า

**ไฟล์ `config/condb.php` ต้องมี:**
```php
$condb = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
$condb->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
```

**ไฟล์ `include/Add_session.php` ต้องมี:**
```php
session_start();
header('Content-Type: text/html; charset=utf-8');
```

## ฟีเจอร์ที่เพิ่ม

### 1. ดาวน์โหลดไฟล์แนบ
- คลิกที่รูปภาพหรือไฟล์เพื่อดาวน์โหลดได้ทันที
- เพิ่ม `download` attribute ในลิงก์
- เปิดในแท็บใหม่ด้วย `target="_blank"`

**รูปภาพ:**
```html
<a href="download_attachment.php?id=xxx" download="filename.jpg" target="_blank">
    <img src="..." class="img-thumbnail" title="คลิกเพื่อดาวน์โหลด">
</a>
```

**ไฟล์ทั่วไป:**
```html
<a href="download_attachment.php?id=xxx" download="filename.pdf" target="_blank" class="btn btn-sm btn-outline-secondary">
    <i class="fas fa-file-pdf"></i> filename.pdf (250 KB)
</a>
```

### 2. รองรับ Emoji ทุกชนิด
- 😀 😃 😄 😁 😆 😅 🤣 😂 (ยิ้ม)
- ❤️ 🧡 💛 💚 💙 💜 🖤 🤍 (หัวใจ)
- 👍 👎 👌 ✌️ 🤞 🤝 👏 🙌 (มือ)
- ✅ ❌ ⭐ 🔥 💯 ✨ 🎉 🎊 (สัญลักษณ์)

## สรุป

การแก้ไขครั้งนี้:
1. ✅ แก้ไขปัญหาโมจิคอนแสดง ????
2. ✅ เพิ่มฟีเจอร์ดาวน์โหลดไฟล์คลิกเดียว
3. ✅ รองรับ UTF-8 ทุกไฟล์
4. ✅ Export Word รองรับภาษาไทยและโมจิ

---

**อัพเดทล่าสุด:** 2025-10-06
**เวอร์ชัน:** 1.1
