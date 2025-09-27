# เวิร์กโฟลว์ระบบ Service Desk (ฉบับยืนยัน)

## ภาพรวมหน้า Service
- `pages/service/service.php` แสดงแดชบอร์ดสรุปงานบริการ (ยังใช้ Mock data) รวมถึงฟอร์มค้นหาและตาราง Ticket สำหรับอ้างอิงสถานะโดยรวม
- `pages/service/add_account.php` เป็นฟอร์มสร้าง Ticket พร้อมการตั้งค่าดีฟอลต์ตาม session และมี Ticket Preview / Activity Timeline จำลองเพื่อให้เห็นผลก่อนบันทึกจริง

## บทบาทผู้ใช้งานหลัก
- **ผู้ใช้งานทั่วไป (ทุก Role)**: สามารถเปิด Ticket ได้ แต่แก้ไขบางฟิลด์ได้เฉพาะช่วงสถานะที่อนุญาต
- **ผู้ควบคุม/ผู้อนุมัติ (Approver / SLA Controller)**: มีสิทธิ์กำหนด SLA Target, อนุมัติ Ticket, เปลี่ยนสถานะที่เกี่ยวข้องกับการควบคุมคุณภาพ (เช่น Approved / Closed) 
- **ผู้รับผิดชอบหลัก (Job Owner)**: เจ้าหน้าที่ที่รับงานไปดำเนินการ สามารถเปลี่ยนสถานะหลังได้รับอนุมัติ และปรับปรุง Next Action / Timeline ได้
- **Watcher / Requester**: ผู้ที่ต้องรับรู้ความคืบหน้า (ระบุได้หลายคนผ่าน Watcher list)

## Flow ตามสถานะ (State Machine)
1. **เปิดงาน (New)**
   - ผู้ใช้งานทุก Role สามารถสร้าง Ticket ได้ → ระบบตั้งค่าดีฟอลต์และบันทึกสถานะเป็น `New`
   - ห้ามเปลี่ยนสถานะอื่น และห้ามแก้ไขฟิลด์ Priority / Urgency / Impact / SLA Target / Start At / Due At จนกว่าจะผ่านการตรวจสอบ

2. **รออนุมัติเริ่มงาน (Open Approval)**
   - ผู้ควบคุม/ผู้อนุมัติเข้ามาตรวจสอบ → ปรับ Priority, Urgency, Impact ตามความสำคัญของงาน
   - ระบบคำนวณ SLA Target (ชั่วโมง) อัตโนมัติจากค่า Priority + Urgency + Impact โดยอิงค่าที่ตั้งไว้ในหน้า Setting SLA
   - หากอนุมัติ → สถานะเปลี่ยนจาก `New` → `Open Approval` เพื่อยืนยันก่อนเริ่มงานจริง (หากงานถูกยกเลิกในช่วงนี้สามารถเปลี่ยนเป็น `Canceled` ได้)
   - เมื่อยืนยันเริ่มงาน → สถานะเปลี่ยนจาก `Open Approval` → `On Process`. นับจากนี้ Priority/Urgency/Impact/SLA Target/Start At/Due At จะถูกล็อก

1. **โครงสร้าง Service Category / Category / Sub Category**
   - ต้องสร้างข้อมูลลำดับชั้น Service → Category → Sub Category ล่วงหน้าก่อนเปิด Ticket
   - เมื่อผู้ใช้เลือก Service Category ระบบจะกรอง Category และ Sub Category ให้สัมพันธ์กันอัตโนมัติ

2. **เปิด Ticket (New)**
   - ผู้ใช้ทุก Role เข้าฟอร์ม `add_account.php` → ระบบตั้งค่าดีฟอลต์ (Ticket Type = Incident, Status = New, Source = Portal, Preferred Channel = Office, Priority/Urgency/Impact = Low, Owner = ผู้ใช้ปัจจุบัน, Support Team = ทีมปัจจุบัน, SLA Target แสดงค่าตั้งต้นจาก Setting เช่น 24 ชม., Start At = เวลาเปิดฟอร์ม)
   - ผู้ใช้กรอกข้อมูลที่จำเป็น, เพิ่ม Watcher, แนบไฟล์ แล้วกดบันทึก → Ticket ถูกสร้างในสถานะ `New`
   - ในสถานะนี้ห้ามเปลี่ยนสถานะอื่น และห้ามแก้ไข Priority/Urgency/Impact/SLA Target/Start At/Due At

3. **รออนุมัติเริ่มงาน (Open Approval)**
   - ผู้ควบคุม/ผู้อนุมัติเข้ามาตรวจสอบ → ปรับ Priority, Urgency, Impact เพื่อให้ระบบคำนวณ SLA Target อัตโนมัติจากค่าใน Setting SLA
   - หากอนุมัติ → สถานะเปลี่ยนจาก `New` → `Open Approval`
   - เมื่อยืนยันเริ่มงาน → สถานะเปลี่ยนจาก `Open Approval` → `On Process` และล็อกฟิลด์ Priority/Urgency/Impact/SLA Target/Start At/Due At

4. **ดำเนินงาน (On Process / Pending)**
   - Job Owner บันทึก Next Action ระหว่างดำเนินงาน ระบบบันทึก log ทุกครั้ง
   - หากต้องรอข้อมูลหรือการตอบกลับ → เปลี่ยนเป็น `Pending`
   - เมื่อพร้อมทำงานต่อ → กลับไป `On Process`

5. **Assign งาน (เปลี่ยน Job Owner)**
   - ทำได้เฉพาะช่วง `On Process` หรือ `Pending`
   - Owner เดิมต้องกรอก **สาเหตุ / Case Description** และ **แนวทางแก้ไข / Resolve Action** ก่อนส่งมอบ → ระบบบันทึก log และเปลี่ยน Owner เดิมเป็นสถานะ `Resolved`
   - Owner ใหม่รับงานต่อในสถานะ `On Process`

6. **ปิดงาน (Resolved → Waiting for Approval → Closed)**
   - Owner คนสุดท้ายกรอก Case Description / Resolve Action แล้วตั้งสถานะเป็น `Resolved`
   - ระบบเปลี่ยนสถานะอัตโนมัติจาก `Resolved` → `Waiting for Approval`
   - ผู้อนุมัติประเมิน/ตรวจสอบผล → ถ้าผ่านให้เปลี่ยนเป็น `Closed` (หากไม่ผ่านสามารถแจ้งกลับสู่ `On Process` หรือ `Canceled` ตามนโยบาย)
   - ระบบคำนวณ SLA โดยใช้เวลาที่เปลี่ยนเป็น `Resolved` หักด้วย Start At และแสดงผลที่หน้า `account.php`

6. **สถานะที่ใช้ในระบบ (Status Whitelist)**
   - `New`
   - `Open Approval`
   - `On Process`
   - `Pending`
   - `Waiting for Approval`
   - `Resolved`
   - `Closed`
   - `Canceled`

   > หมายเหตุ: ห้ามใช้สถานะอื่นนอกเหนือจากรายการข้างต้นเพื่อความสอดคล้องของ Flow

- **สถานะ New**: ใครเปิดก็ได้, ห้ามเปลี่ยนสถานะอื่น, ห้ามปรับ SLA Target/เวลา/priority/urgency/impact, รอ Approver ตรวจสอบ
- **สถานะ Open Approval**: ผู้อนุมัติใช้สำหรับตรวจสอบ/คอนฟิก SLA และยืนยันการเริ่มงานจริง หากยกเลิกงานก่อนเริ่มสามารถเปลี่ยนเป็น Canceled
- **สถานะ On Process**: อยู่ระหว่างดำเนินการ (แก้ไข SLA Target/เวลา/priority/urgency/impact ไม่ได้), Job Owner บันทึก Next Action ได้ตามต้องการ
- **สถานะ Pending**: ใช้กรณีรอดำเนินการเพิ่มเติม (เช่น รอลูกค้า/รออะไหล่) ยังอยู่ในความรับผิดชอบของ Owner เดิม
- **การเปลี่ยน Job Owner**: ทำเฉพาะช่วง On Process/Pending โดย Owner เดิมต้องกรอก Case Description + Resolve Action ก่อนส่งต่อ → ระบบบันทึก Owner เดิมเป็น Resolved และตั้ง Owner ใหม่เป็น On Process
- **สถานะ Resolved**: เจ้าของคนสุดท้ายกรอก Case Description + Resolve Action เพื่อสรุปผล แล้วระบบจะผลัก Ticket ไป Waiting for Approval เพื่อขออนุมัติปิดงาน
- **สถานะ Waiting for Approval → Closed**: ผู้อนุมัติยืนยันการปิดงาน หากไม่ผ่านสามารถส่งกลับ On Process หรือ Canceled ตามเหตุผล

- **Master Service Category Tree**: ตาราง `service_categories`, `categories`, `sub_categories` (หรือใช้ตารางเดียวที่มี parent_id) เพื่อให้สร้างลำดับชั้น Service → Category → Sub Category
- **Ticket Master**: ticket_id, ticket_code, subject, description, ticket_type, status, channel, priority, urgency, impact, sla_target_hours (คำนวณอัตโนมัติ), start_at, due_at, resolved_at, service_category_id, category_id, sub_category_id, project_id, owner_id, support_team_id, approver_id, created_by, created_at, updated_at, closed_at ฯลฯ
- **Ticket Assignment History**: ประวัติการเปลี่ยน Job Owner (เก็บ owner_from, owner_to, timestamp, note_case, note_resolve) เพื่อใช้ reconstruct log/flow “Resolved by previous owner”
- **Ticket Timeline**: เก็บทุกการอัปเดต (status เปลี่ยน, Next Action, Case Description/Resolve Action, การเปลี่ยน Owner, การอนุมัติ) เพื่อให้ Activity Timeline สร้างข้อมูลจาก DB ได้
- **Ticket Watchers**: user_id ที่ต้องเตือนความคืบหน้า
- **Ticket Attachments**: ข้อมูลไฟล์ที่แนบใน Ticket
- **Ticket Onsite Details**: รายละเอียดการเดินทาง (กรณี channel = Onsite)
- **SLA Matrix / Setting**: ตารางกำหนดค่าผสม Priority + Urgency + Impact → SLA Target (ชั่วโมง) พร้อมหน้าจอ Setting SLA สำหรับปรับแต่งโดยผู้อนุมัติ
- **Ticket SLA Tracking**: timestamp สำหรับคำนวณ SLA (start_at, resolved_at, closed_at) และ flag ว่า SLA Met หรือ Failed (อาจสร้าง field ใน ticket master)

## ประเด็นยืนยันเพิ่มเติมก่อนจัดทำ SQL (ตอบแล้ว)
1. **รูปแบบรหัส Ticket**: ต้องการ pattern เฉพาะ (จะออกแบบให้รองรับการ generate ตามรูปแบบ เช่น `TCK-YYYYMM-####`)
2. **Requester แยกจาก Owner**: ต้องการเก็บข้อมูลผู้แจ้ง (Requester) แยกจากผู้รับผิดชอบ (Owner)
3. **การอนุมัติหลายระดับ**: ต้องรองรับ workflow อนุมัติหลายชั้น (multi-level approval)
4. **สถานะเมื่อ Reject**: หากผู้ควบคุม/ผู้อนุมัติ Reject ให้ย้อนสถานะกลับไปที่ `On Process`
5. **การเปลี่ยน Job Owner**: ใช้ flow ตามที่สรุป (Owner เดิมเป็น Resolved → Owner ใหม่เป็น On Process); หากต้องการ edge case เพิ่มเติมสามารถพิจารณาหลังจากใช้งานจริง
6. **ข้อมูล Onsite / ไฟล์แนบ**: ต้องเก็บได้หลายชุดต่อ Ticket

ด้วยข้อมูลข้างต้น ผมจะดำเนินการออกแบบ SQL DDL (พร้อมคอมเมนต์ภาษาไทย) ให้รองรับ flow นี้ครบถ้วน
