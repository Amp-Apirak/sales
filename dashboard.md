# คำอธิบายหน้า Dashboard และเงื่อนไขการแสดงผล (ละเอียด)

เอกสารนี้สรุปองค์ประกอบของหน้า Dashboard (`index.php`) และอธิบายเงื่อนไข แหล่งข้อมูล ตาราง และลำดับการกรองที่ใช้จริงในแต่ละส่วน เพื่อช่วยทีมพัฒนา/ซัพพอร์ตตรวจสอบพฤติกรรมได้รวดเร็ว

## 1) ตัวกรอง (Filters) และตัวแปรขอบเขต
- ตัวกรองช่วงวันที่ (`date_range`) เริ่มต้น = ตั้งแต่ 1 ม.ค. ของปีปัจจุบัน ถึงวันปัจจุบัน
- Executive เท่านั้นที่มีตัวกรอง “ทีม” (`team_select`) และ “ผู้ใช้” (`user_select`) เพิ่มเติม
  - เมื่อเลือกทีม ระบบจะกรองเฉพาะข้อมูลของผู้ใช้ที่อยู่ในทีมนั้น
  - รายการผู้ใช้ใน `user_select` ถูกกรองด้วย JS ให้สัมพันธ์กับทีมที่เลือก
- Sale Supervisor ใช้สถานะทีมปัจจุบันในเซสชัน (Switch ที่ Navbar)
  - หากโหมด ‘ALL’ = รวมทุกทีมที่ผู้ใช้สังกัด
  - หากเลือกทีมใดทีมหนึ่ง = จำกัดข้อมูลตามทีมนั้น
- Seller/Engineer เห็นเฉพาะข้อมูลของตนเอง (ไม่มีตัวกรองทีม/ผู้ใช้บนฟอร์ม)

ลำดับความสำคัญของการกรองที่ใช้กับทุกคิวรีหลัก:
1) `filter_user_id` (ถ้ามี) มาก่อนเสมอ
2) Executive + `filter_team_id` (ถ้ามี)
3) Supervisor: ทีมปัจจุบันในเซสชัน (หรือ ALL สำหรับรวมทุกทีมของตน)
4) Seller/Engineer: จำกัดเป็นผู้ใช้ปัจจุบัน

ตัวช่วยที่ใช้ซ้ำ:
- `getTeamFilterCondition($can_view_team, $table_alias, $user_field, &$params)` คืนเงื่อนไข SQL IN/=`team_id` จาก `$_SESSION['team_id']`/`team_ids`

ตารางที่ถูกใช้อ้างอิงบ่อย:
- `projects` (p), `products` (pr/p), `users` (u), `user_teams` (ut), `teams` (t)

หมายเหตุเรื่องฟิลด์ใน `projects`:
- การเงินรวมใช้: `sale_no_vat`, `cost_no_vat`, และคำนวณกำไร `gross_profit` หรือ `sale_no_vat - cost_no_vat`
- กราฟยอดขายรายปี/เดือน ใช้ `sale_vat`

## 2) การ์ดสรุป KPI เริ่มต้น (ทุกบทบาทเห็น)
- จำนวนทีม (`$total_teams`)
  - Executive: `SELECT COUNT(*) FROM teams` (หรือ = 1 เมื่อกรองทีม)
  - อื่น ๆ: `SELECT COUNT(DISTINCT team_id) FROM user_teams WHERE user_id = :user_id`
  - ป้ายชื่อแตกต่างตามบทบาท: Executive=“จำนวนทีมทั้งหมด”, อื่นๆ=“จำนวนทีมที่ฉันอยู่”
- จำนวนสมาชิกทีม (`$total_team_members`)
  - Executive: ผู้ใช้ทั้งหมด หรือจำกัดตามทีมที่เลือก
  - Supervisor: รวมสมาชิกจากทุกทีมที่ตนสังกัด (หรือทีมปัจจุบัน)
  - Seller/Engineer: “จำนวนคนในทีมของฉัน” (จำกัดตามทีมที่ตนอยู่)
- จำนวนโครงการทั้งหมด (`$total_projects`)
  - ช่วงวันที่: `p.sales_date BETWEEN :start_date AND :end_date`
  - ขอบเขต: ตามลำดับการกรอง (user/team/supervisor/own) และใช้ `p.seller` เป็นหลัก
- จำนวนสินค้า (`$total_products`)
  - `SELECT COUNT(*) FROM products` (ไม่มีการจำกัดตามบทบาท)

## 3) การ์ดสรุปสถานะโครงการแบบนับจำนวน (เฉพาะผู้ที่เห็นการเงิน)
ใช้ฟังก์ชัน `countProjectsByStatus(condb, status_list, role, team_id, user_id, filter_team_id, filter_user_id, date_range)` โดยจำกัดช่วงวันที่และขอบเขตเหมือนข้อ 2
- Win Projects: สถานะ `['ชนะ (Win)']`
- Ongoing Projects: `['นำเสนอโครงการ (Presentations)', 'ใบเสนอราคา (Quotation)', 'ยื่นประมูล (Bidding)', 'รอการพิจารณา (On Hold)']`
- Loss Projects: `['แพ้ (Loss)']`
- Canceled Projects: `['ยกเลิก (Cancled)']`

Engineer ไม่เห็นการ์ดกลุ่มนี้

## 4) การ์ดสรุปการเงินรวม (เฉพาะผู้ที่เห็นการเงิน)
ช่วงวันที่: `p.sales_date BETWEEN :start_date AND :end_date` และจำกัดตามขอบเขตเหมือนข้อ 2 โดยใช้ `p.seller`
- ยอดขายรวม (No VAT): `SUM(p.sale_no_vat)`
- ต้นทุนรวม (No VAT): `SUM(p.cost_no_vat)`
- กำไรรวม (No VAT): `ยอดขายรวม - ต้นทุนรวม`
- กำไร (No VAT %) : `(กำไร/ยอดขายรวม) * 100` (ถ้ายอดขาย > 0)

Engineer ไม่เห็นการ์ดกลุ่มนี้

## 5) การ์ดสรุปการเงินเฉพาะสถานะ “ชนะ (Win)” (เฉพาะผู้ที่เห็นการเงิน)
ดึงจาก `getWinProjectSummary()` พร้อมช่วงวันที่และขอบเขต (ใช้ `p.seller`):
- Win ยอดขายรวม (No VAT): `SUM(p.sale_no_vat)` เมื่อ `p.status='ชนะ (Win)'`
- Win ต้นทุนรวม (No VAT): `SUM(p.cost_no_vat)`
- Win กำไรรวม (No VAT): `SUM(p.gross_profit)`
- Win กำไร (No VAT %): `(Win กำไร / Win ยอดขาย) * 100` (ถ้า Win ยอดขาย > 0)

Engineer ไม่เห็นการ์ดกลุ่มนี้

## 6) กราฟและข้อมูลที่ใช้
หมายเหตุ: กราฟที่เป็นการเงินจะแสดงเฉพาะเมื่อผู้ใช้บทบาทนั้นมี `can_view_financial = true`

- สถานะโครงการ (pie/doughnut) — ทุกบทบาทเห็น
  - คิวรี: `SELECT p.status, COUNT(*) FROM projects p WHERE p.sales_date BETWEEN ... [ขอบเขตโดยใช้ p.created_by] GROUP BY status`
  - ใช้ `p.created_by` ในการจำกัดขอบเขต (ต่างจากการ์ดนับโครงการที่ใช้ `p.seller`)
- Product ที่ขายดีที่สุด (bar แนวนอน) — ทุกบทบาทเห็น
  - คิวรี: `SELECT p.product_name, COUNT(*) FROM projects pr JOIN products p ON pr.product_id=p.product_id WHERE pr.sales_date BETWEEN ... [ขอบเขตโดยใช้ pr.created_by] GROUP BY p.product_id ORDER BY count DESC LIMIT 10`

- ยอดขายรายปี (bar) — เฉพาะผู้เห็นการเงิน
  - คิวรี: `SELECT YEAR(sales_date) as year, SUM(sale_vat) FROM projects p WHERE sales_date BETWEEN ... [ขอบเขตโดยใช้ p.created_by] GROUP BY YEAR(sales_date)`
- ยอดขายรายเดือน (line) — เฉพาะผู้เห็นการเงิน
  - คิวรี: `SELECT DATE_FORMAT(sales_date, '%Y-%m') as month, SUM(sale_vat) FROM projects p WHERE sales_date BETWEEN ... [ขอบเขตโดยใช้ p.created_by] GROUP BY DATE_FORMAT(sales_date, '%Y-%m')`
- ยอดขายรายทีม (bar) — เฉพาะผู้เห็นการเงิน
  - คิวรี: Join `projects p -> users u (p.seller=u.user_id) -> user_teams ut -> teams t` และ `SUM(p.sale_vat)`, จำกัดช่วงวันที่ และกรองทีมจาก `t.team_id`
- ยอดขายของพนักงาน Top 10 (bar แนวนอน) — เฉพาะผู้เห็นการเงิน
  - คิวรี: `SELECT u.first_name, u.last_name, SUM(p.sale_vat) FROM projects p JOIN users u ON p.seller=u.user_id WHERE p.sales_date BETWEEN ... [ขอบเขตโดยใช้ p.seller] GROUP BY p.seller ORDER BY total_sales DESC LIMIT 10`

สรุปความแตกต่างของฟิลด์กำหนดขอบเขตในกราฟ:
- ใช้ `p.created_by`: สถานะโครงการ, Product ที่ขายดีที่สุด, ยอดขายรายปี/เดือน
- ใช้ `p.seller`: ยอดขายรายทีม, ยอดขายพนักงาน, การเงินรวม/การ์ดสรุป (ส่วนใหญ่)

## 7) การแสดงผล/เงื่อนไขฝั่ง UI
- ส่วนการเงินทั้งหมด (การ์ดการเงิน, การ์ด Win, กราฟรายปี/เดือน/ทีม/พนักงาน) ถูกห่อด้วย `if ($can_view_financial)`
- การ์ดนับสถานะโครงการ (Win/Ongoing/Loss/Canceled) อยู่ภายใน `if ($can_view_financial)` เช่นกัน (Engineer ไม่เห็น)
- กราฟ “สถานะโครงการ” และ “Product ที่ขายดีที่สุด” อยู่ “นอก” เงื่อนไขดังกล่าว จึงแสดงให้ทุกบทบาทเห็น

## 8) Team Switcher (Navbar) และผลกระทบ
- ปรากฏเมื่อผู้ใช้สังกัดมากกว่า 1 ทีม (`$_SESSION['user_teams']`)
- ค่า `$_SESSION['team_id']` จะเป็น `'ALL'` หากมีหลายทีม (รวมทุกทีมของผู้ใช้)
- เมื่อสลับทีมผ่าน `switch_team.php` ข้อมูลทั้งหมดที่ขึ้นกับทีม (โดยเฉพาะ Supervisor) จะสะท้อนทีมที่เลือกทันที

## 9) ข้อควรทราบสำหรับการซัพพอร์ต/ทดสอบ
- เมื่ออธิบายความต่างของข้อมูลต่อผู้ใช้ ให้เช็ค 3 จุด: บทบาท, ทีมปัจจุบัน (หรือ ALL), ตัวกรองผู้ใช้/ทีมในฟอร์ม (สำหรับ Executive)
- หากตัวเลข “จำนวนทีม/สมาชิกทีม” ไม่ตรง ให้ตรวจการเป็นสมาชิกทีมใน `user_teams`
- หากตัวเลขการเงินไม่ขึ้นสำหรับ Engineer ถือว่า “ปกติ” เพราะถูกปิดตามสิทธิ์
- บางกราฟอ้าง `created_by` ขณะที่การ์ดนับโครงการ/การเงินใช้ `seller` — ความต่างนี้ตั้งใจออกแบบเพื่อสะท้อนมุมที่ต่างกันของข้อมูล (ผู้สร้าง vs. ผู้ขาย)

## 10) สรุปผลของตัวกรองและข้อยกเว้น (สำหรับ Dashboard)
- ลำดับความสำคัญของตัวกรอง: `user` > `team` > (Supervisor team session/ALL) > (own for Seller/Engineer)
- ตัวกรองช่วงวันที่ (`sales_date BETWEEN ...`) มีผลต่อ: จำนวนโครงการ, การ์ดการเงินรวม, การ์ด Win, กราฟสถานะโครงการ, Top Products, ยอดขายรายปี/เดือน, ยอดขายรายทีม/รายพนักงาน
- ไม่ได้รับผลจากช่วงวันที่: จำนวนสินค้า (`COUNT(*) FROM products`), จำนวนทีม/จำนวนสมาชิกทีม (อิงบทบาท/ทีม ไม่อิงช่วงวัน)
- Executive: ฟอร์มสามารถเลือกทีม/ผู้ใช้ → ข้อมูลทุกส่วนที่รองรับจะอัปเดตตามการเลือก
- Sale Supervisor: อัปเดตตาม Team Switcher (ALL = รวมทุกทีมของตน หรือทีมที่กำลังเลือก)
- Seller/Engineer: จำกัดตาม `user_id` ของตนเองเสมอ ไม่มีฟิลเตอร์ทีม/ผู้ใช้บนฟอร์ม
  - หากสังกัดหลายทีม ระบบจะรวมข้อมูลของผู้ใช้นั้นทุกทีม (Team Switcher ไม่มีผล)
