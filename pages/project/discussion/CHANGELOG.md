# Project Discussion Board - Changelog

## Version 1.1 (2025-10-06)

### 🔧 Bug Fixes
1. **แก้ไขปัญหาโมจิคอนแสดง ????**
   - เปลี่ยน database charset เป็น `utf8mb4_unicode_ci`
   - เพิ่ม `SET NAMES utf8mb4` ในทุก PHP file
   - เพิ่ม `Content-Type: text/html; charset=utf-8` header
   - แก้ไข `htmlspecialchars()` ให้ระบุ UTF-8 encoding

2. **แก้ไขการดาวน์โหลดไฟล์**
   - เพิ่ม `download` attribute ในลิงก์ไฟล์แนบ
   - เพิ่ม `target="_blank"` เพื่อเปิดในแท็บใหม่
   - รูปภาพคลิกแล้วดาวน์โหลดได้ทันที
   - ไฟล์ทั่วไปคลิกแล้วดาวน์โหลดได้ทันที

### ✨ Features Added
- รองรับ Emoji ทุกชนิด: 😀 👍 ❤️ 🎉 ✅ 🔥 และอื่นๆ
- Emoji Picker พร้อมหมวดหมู่
- คลิกดาวน์โหลดไฟล์แนบได้ทันที

### 📝 Files Modified

#### Database Changes
- `project_discussions.message_text` → utf8mb4_unicode_ci
- `project_discussion_attachments.file_name` → utf8mb4_unicode_ci

#### PHP Files Updated
1. **get_discussions.php**
   - Added UTF-8 headers
   - Added `SET NAMES utf8mb4`
   - Fixed `htmlspecialchars` encoding
   - Added download links for attachments

2. **post_discussion.php**
   - Added `SET NAMES utf8mb4`

3. **edit_discussion.php**
   - Added `SET NAMES utf8mb4`

4. **export_word.php**
   - Added UTF-8 headers
   - Added `SET NAMES utf8mb4`

5. **index.php**
   - Added UTF-8 header
   - Fixed Export button text

#### UI Changes
- ปรับปุ่ม Export ให้เล็กลง (btn-sm)
- เอาสีพื้นหลัง gradient ออก
- ปรับ container ให้เป็นพื้นสีขาว
- ปุ่ม Refresh แสดงเฉพาะไอคอน

### 🔍 How to Verify

1. **ทดสอบโมจิคอน:**
   ```
   พิมพ์ข้อความ: สวัสดีครับ 😀 👍 ❤️
   กดส่ง
   ตรวจสอบ: ต้องแสดงโมจิคอนถูกต้อง ไม่ใช่ ????
   ```

2. **ทดสอบดาวน์โหลดไฟล์:**
   ```
   แนบไฟล์ (รูปภาพหรือ PDF)
   ส่งข้อความ
   คลิกที่รูปภาพหรือชื่อไฟล์
   ตรวจสอบ: ไฟล์ดาวน์โหลดทันที
   ```

### 📚 Technical Details

**Character Set:**
- Old: `utf8` (supports 3-byte characters)
- New: `utf8mb4` (supports 4-byte characters including emoji)

**Headers Added:**
```php
header('Content-Type: text/html; charset=utf-8');
$condb->exec("SET NAMES utf8mb4");
```

**Download Links:**
```html
<!-- Images -->
<a href="download_attachment.php?id=xxx" download="filename.jpg" target="_blank">

<!-- Files -->
<a href="download_attachment.php?id=xxx" download="filename.pdf" target="_blank">
```

---

## Version 1.0 (2025-10-05)

### Initial Release
- กระดานสนทนาโครงการ (Project Discussion Board)
- ส่งข้อความและแนบไฟล์
- แก้ไขและลบข้อความ
- Emoji Picker
- Export เป็น Word
- Auto-refresh ทุก 15 วินาที
- Role-based access control

---

**Last Updated:** 2025-10-06
**Current Version:** 1.1
