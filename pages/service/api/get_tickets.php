<?php
/**
 * API: Get Service Tickets
 * Method: GET
 * Input: Query parameters
 */

// เริ่ม session
session_start();

// เชื่อมต่อฐานข้อมูล (แก้ path ให้ถูกต้อง)
include '../../../config/condb.php';

// ตรวจสอบ session
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized. Please login first.'
    ]);
    exit;
}

// ตั้งค่า header สำหรับ JSON response
header('Content-Type: application/json; charset=utf-8');

// ตรวจสอบ HTTP Method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method Not Allowed. Use GET only.'
    ]);
    exit;
}

try {
    // ดึงข้อมูล User
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    $team_id = $_SESSION['team_id'] ?? null;

    // รับ parameters
    $ticket_id = $_GET['ticket_id'] ?? null;
    $status = $_GET['status'] ?? null;
    $priority = $_GET['priority'] ?? null;
    $search = $_GET['search'] ?? null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

    // ถ้าระบุ ticket_id ให้ดึงข้อมูล Ticket เดียว
    if ($ticket_id) {
        // ใช้ view หรือ table โดยตรง ขึ้นกับว่ามี view หรือไม่
        $sql = "SELECT st.*,
                CONCAT(u.first_name, ' ', u.last_name) as job_owner_name,
                CONCAT(r.first_name, ' ', r.last_name) as reporter_name,
                p.project_name
                FROM service_tickets st
                LEFT JOIN users u ON st.job_owner = u.user_id
                LEFT JOIN users r ON st.reporter = r.user_id
                LEFT JOIN projects p ON st.project_id = p.project_id
                WHERE st.ticket_id = :ticket_id";
        $stmt = $condb->prepare($sql);
        $stmt->execute([':ticket_id' => $ticket_id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            throw new Exception('ไม่พบ Ticket นี้');
        }

        // ดึง Timeline
        $sqlTimeline = "SELECT * FROM service_ticket_timeline WHERE ticket_id = :ticket_id ORDER BY `order` ASC";
        $stmtTimeline = $condb->prepare($sqlTimeline);
        $stmtTimeline->execute([':ticket_id' => $ticket_id]);
        $timeline = $stmtTimeline->fetchAll(PDO::FETCH_ASSOC);

        // ดึง Attachments
        $sqlAttach = "SELECT * FROM service_ticket_attachments WHERE ticket_id = :ticket_id ORDER BY uploaded_at DESC";
        $stmtAttach = $condb->prepare($sqlAttach);
        $stmtAttach->execute([':ticket_id' => $ticket_id]);
        $attachments = $stmtAttach->fetchAll(PDO::FETCH_ASSOC);

        // ดึง Watchers
        $sqlWatch = "SELECT w.*, CONCAT(u.first_name, ' ', u.last_name) AS watcher_name
                     FROM service_ticket_watchers w
                     INNER JOIN users u ON w.user_id = u.user_id
                     WHERE w.ticket_id = :ticket_id";
        $stmtWatch = $condb->prepare($sqlWatch);
        $stmtWatch->execute([':ticket_id' => $ticket_id]);
        $watchers = $stmtWatch->fetchAll(PDO::FETCH_ASSOC);

        // ดึง Comments
        $sqlComments = "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) AS commenter_name, u.profile_image
                        FROM service_ticket_comments c
                        INNER JOIN users u ON c.created_by = u.user_id
                        WHERE c.ticket_id = :ticket_id
                        ORDER BY c.created_at DESC";
        $stmtComments = $condb->prepare($sqlComments);
        $stmtComments->execute([':ticket_id' => $ticket_id]);
        $comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => [
                'ticket' => $ticket,
                'timeline' => $timeline,
                'attachments' => $attachments,
                'watchers' => $watchers,
                'comments' => $comments
            ]
        ]);
        exit;
    }

    // สร้าง SQL สำหรับดึง Tickets ตาม Role
    $sql = "SELECT st.*,
            CONCAT(u.first_name, ' ', u.last_name) as job_owner_name,
            CONCAT(r.first_name, ' ', r.last_name) as reporter_name,
            p.project_name
            FROM service_tickets st
            LEFT JOIN users u ON st.job_owner = u.user_id
            LEFT JOIN users r ON st.reporter = r.user_id
            LEFT JOIN projects p ON st.project_id = p.project_id
            WHERE 1=1";
    $params = [];

    // กรองตาม Role
    if ($role === 'Executive') {
        // Executive เห็นทั้งหมด
    } elseif ($role === 'Sale Supervisor') {
        // Sale Supervisor เห็นของทีม
        if ($team_id) {
            $sql .= " AND job_owner IN (SELECT user_id FROM users WHERE team_id = :team_id)";
            $params[':team_id'] = $team_id;
        }
    } else {
        // Seller, Engineer เห็นของตัวเอง
        $sql .= " AND job_owner = :user_id";
        $params[':user_id'] = $user_id;
    }

    // กรองตาม Status
    if ($status) {
        $sql .= " AND status = :status";
        $params[':status'] = $status;
    }

    // กรองตาม Priority
    if ($priority) {
        $sql .= " AND priority = :priority";
        $params[':priority'] = $priority;
    }

    // ค้นหา
    if ($search) {
        $sql .= " AND (ticket_no LIKE :search OR subject LIKE :search OR description LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    // เรียงลำดับ
    $sql .= " ORDER BY created_at DESC";

    // Pagination
    $sql .= " LIMIT :limit OFFSET :offset";

    $stmt = $condb->prepare($sql);

    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // นับจำนวนทั้งหมด
    $sqlCount = "SELECT COUNT(*) as total FROM service_tickets WHERE 1=1";
    $countParams = [];

    if ($role === 'Sale Supervisor' && $team_id) {
        $sqlCount .= " AND job_owner IN (SELECT user_id FROM users WHERE team_id = :team_id)";
        $countParams[':team_id'] = $team_id;
    } elseif ($role !== 'Executive') {
        $sqlCount .= " AND job_owner = :user_id";
        $countParams[':user_id'] = $user_id;
    }

    if ($status) {
        $sqlCount .= " AND status = :status";
        $countParams[':status'] = $status;
    }

    if ($priority) {
        $sqlCount .= " AND priority = :priority";
        $countParams[':priority'] = $priority;
    }

    if ($search) {
        $sqlCount .= " AND (ticket_no LIKE :search OR subject LIKE :search OR description LIKE :search)";
        $countParams[':search'] = '%' . $search . '%';
    }

    $stmtCount = $condb->prepare($sqlCount);
    $stmtCount->execute($countParams);
    $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

    // ดึง Metrics (คำนวณแบบง่าย)
    $sqlMetrics = "SELECT
        COUNT(*) as total_tickets,
        SUM(CASE WHEN status = 'New' THEN 1 ELSE 0 END) as status_new,
        SUM(CASE WHEN status = 'On Process' THEN 1 ELSE 0 END) as status_on_process,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as status_pending,
        SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as status_resolved,
        SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) as status_closed,
        SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as status_cancelled,
        SUM(CASE WHEN sla_status = 'Overdue' THEN 1 ELSE 0 END) as sla_overdue
        FROM service_tickets";
    $stmtMetrics = $condb->query($sqlMetrics);
    $metrics = $stmtMetrics->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $tickets,
        'metrics' => $metrics,
        'pagination' => [
            'total' => intval($total),
            'limit' => $limit,
            'offset' => $offset,
            'current_page' => floor($offset / $limit) + 1,
            'total_pages' => ceil($total / $limit)
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database Error: ' . $e->getMessage()
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
