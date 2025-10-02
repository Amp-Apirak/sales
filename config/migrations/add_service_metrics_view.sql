-- ============================================
-- SQL สำหรับเพิ่ม View vw_service_tickets_metrics
-- วันที่สร้าง: 2025-10-02
-- ============================================

USE sales_db;

-- สร้าง View สำหรับ Metrics Dashboard
CREATE OR REPLACE VIEW vw_service_tickets_metrics AS
SELECT
    COUNT(*) as total_tickets,

    -- นับตาม Status
    SUM(CASE WHEN status = 'Draft' THEN 1 ELSE 0 END) as status_draft,
    SUM(CASE WHEN status = 'New' THEN 1 ELSE 0 END) as status_new,
    SUM(CASE WHEN status = 'On Process' THEN 1 ELSE 0 END) as status_on_process,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as status_pending,
    SUM(CASE WHEN status = 'Waiting for Approval' THEN 1 ELSE 0 END) as status_waiting_approval,
    SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as status_approved,
    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as status_in_progress,
    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as status_resolved,
    SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) as status_closed,
    SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as status_cancelled,

    -- นับตาม Priority
    SUM(CASE WHEN priority = 'Critical' THEN 1 ELSE 0 END) as priority_critical,
    SUM(CASE WHEN priority = 'High' THEN 1 ELSE 0 END) as priority_high,
    SUM(CASE WHEN priority = 'Medium' THEN 1 ELSE 0 END) as priority_medium,
    SUM(CASE WHEN priority = 'Low' THEN 1 ELSE 0 END) as priority_low,

    -- นับตาม Ticket Type
    SUM(CASE WHEN ticket_type = 'Incident' THEN 1 ELSE 0 END) as type_incident,
    SUM(CASE WHEN ticket_type = 'Service' THEN 1 ELSE 0 END) as type_service,
    SUM(CASE WHEN ticket_type = 'Change' THEN 1 ELSE 0 END) as type_change,

    -- นับตาม SLA Status
    SUM(CASE WHEN sla_status = 'Within SLA' THEN 1 ELSE 0 END) as sla_within,
    SUM(CASE WHEN sla_status = 'Near SLA' THEN 1 ELSE 0 END) as sla_near,
    SUM(CASE WHEN sla_status = 'Overdue' THEN 1 ELSE 0 END) as sla_overdue,

    -- นับ Tickets ที่ยังไม่ปิด
    SUM(CASE WHEN status NOT IN ('Closed', 'Cancelled') THEN 1 ELSE 0 END) as active_tickets,

    -- นับ Tickets วันนี้
    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_tickets,

    -- นับ Tickets สัปดาห์นี้
    SUM(CASE WHEN YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) THEN 1 ELSE 0 END) as week_tickets,

    -- นับ Tickets เดือนนี้
    SUM(CASE WHEN YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE()) THEN 1 ELSE 0 END) as month_tickets

FROM service_tickets
WHERE deleted_at IS NULL;

-- แสดงผลลัพธ์
SELECT 'View vw_service_tickets_metrics created successfully!' as Status;
SELECT * FROM vw_service_tickets_metrics;
