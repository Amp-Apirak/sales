<?php
/**
 * API: Create Service Ticket
 * Method: POST
 * Input: JSON/FormData
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
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method Not Allowed. Use POST only.'
    ]);
    exit;
}

// ตรวจสอบ CSRF Token
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid CSRF token'
    ]);
    exit;
}

try {
    // รับข้อมูลจาก POST
    $project_id = $_POST['project_id'] ?? null;
    $ticket_type = $_POST['ticket_type'] ?? 'Incident';
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $status = $_POST['status'] ?? 'New';
    $priority = $_POST['priority'] ?? 'Low';
    $urgency = $_POST['urgency'] ?? 'Low';
    $impact = $_POST['impact'] ?? '';

    $service_category = $_POST['service_category'] ?? null;
    $category = $_POST['category'] ?? null;
    $sub_category = $_POST['sub_category'] ?? null;

    $job_owner = $_POST['job_owner'] ?? null;
    $reporter = $_POST['reporter'] ?? null;
    $source = $_POST['source'] ?? 'Portal';

    require_once __DIR__ . '/../sla_helpers.php';
    $sla_target = computeSlaTarget($condb, $priority, $urgency, $impact);
    $channel = $_POST['channel'] ?? null;

    $start_at = !empty($_POST['start_at']) ? $_POST['start_at'] : null;
    $due_at = !empty($_POST['due_at']) ? $_POST['due_at'] : null;

    $created_by = $_SESSION['user_id'];

    // Watchers (array)
    $watchers = isset($_POST['watchers']) ? $_POST['watchers'] : [];

    // Validation (required fields)
    if (empty($project_id)) { throw new Exception('กรุณาเลือกโครงการ'); }
    if (empty($ticket_type)) { throw new Exception('กรุณาเลือก Ticket Type'); }
    if (empty($job_owner)) { throw new Exception('กรุณาเลือก Job Owner'); }
    if (empty($priority)) { throw new Exception('กรุณาเลือก Priority'); }
    if (empty($channel)) { throw new Exception('กรุณาเลือก Channel'); }
    if (empty($urgency)) { throw new Exception('กรุณาเลือก Urgency'); }
    if (empty($impact)) { throw new Exception('กรุณาเลือก Impact'); }
    if (empty($status)) { throw new Exception('กรุณาเลือก Status'); }
    if (empty($service_category)) { throw new Exception('กรุณาเลือก Service Category'); }
    if (empty($category)) { throw new Exception('กรุณาเลือก Category'); }
    if (empty($sub_category)) { throw new Exception('กรุณาเลือก Sub Category'); }
    if (empty($source)) { throw new Exception('กรุณาเลือก Ticket Source'); }
    if (empty($reporter)) { throw new Exception('กรุณาเลือกผู้แจ้ง'); }
    if (empty($start_at)) { throw new Exception('กรุณาเลือกกำหนดเริ่มดำเนินการ'); }
    if (empty($due_at)) { throw new Exception('กรุณาเลือกกำหนดแล้วเสร็จ'); }
    if (empty($subject)) { throw new Exception('กรุณากระบุหัวข้อ Ticket'); }
    if (empty($description)) { throw new Exception('กรุณากรอกรายละเอียดงาน'); }

    // เริ่ม Transaction
    $condb->beginTransaction();

    // สร้าง Ticket ID
    $ticket_id = bin2hex(random_bytes(16));

    // INSERT Ticket
    $sql = "INSERT INTO service_tickets (
        ticket_id, project_id, ticket_type, subject, description,
        status, priority, urgency, impact,
        service_category, category, sub_category,
        job_owner, reporter, source,
        sla_target, channel, start_at, due_at,
        created_by
    ) VALUES (
        :ticket_id, :project_id, :ticket_type, :subject, :description,
        :status, :priority, :urgency, :impact,
        :service_category, :category, :sub_category,
        :job_owner, :reporter, :source,
        :sla_target, :channel, :start_at, :due_at,
        :created_by
    )";

    $stmt = $condb->prepare($sql);
    $stmt->execute([
        ':ticket_id' => $ticket_id,
        ':project_id' => $project_id,
        ':ticket_type' => $ticket_type,
        ':subject' => $subject,
        ':description' => $description,
        ':status' => $status,
        ':priority' => $priority,
        ':urgency' => $urgency,
        ':impact' => $impact,
        ':service_category' => $service_category,
        ':category' => $category,
        ':sub_category' => $sub_category,
        ':job_owner' => $job_owner,
        ':reporter' => $reporter,
        ':source' => $source,
        ':sla_target' => $sla_target,
        ':channel' => $channel,
        ':start_at' => $start_at,
        ':due_at' => $due_at,
        ':created_by' => $created_by
    ]);

    // ดึง Ticket Number ที่ถูกสร้าง
    $ticketData = $condb->prepare("SELECT ticket_no FROM service_tickets WHERE ticket_id = :ticket_id");
    $ticketData->execute([':ticket_id' => $ticket_id]);
    $ticket = $ticketData->fetch();
    $ticket_no = $ticket['ticket_no'];

    // INSERT Onsite Details (ถ้า channel = Onsite)
    if ($channel === 'Onsite') {
        $onsite_start_location = $_POST['onsite_start_location'] ?? null;
        $onsite_end_location = $_POST['onsite_end_location'] ?? null;
        $onsite_travel_mode = $_POST['onsite_travel_mode'] ?? null;
        $onsite_travel_note = $_POST['onsite_travel_note'] ?? null;
        $onsite_odometer_start = !empty($_POST['onsite_odometer_start']) ? floatval($_POST['onsite_odometer_start']) : null;
        $onsite_odometer_end = !empty($_POST['onsite_odometer_end']) ? floatval($_POST['onsite_odometer_end']) : null;
        $onsite_note = $_POST['onsite_note'] ?? null;

        $sqlOnsite = "INSERT INTO service_ticket_onsite (
            onsite_id, ticket_id, start_location, end_location,
            travel_mode, travel_note, odometer_start, odometer_end, note
        ) VALUES (
            UUID(), :ticket_id, :start_location, :end_location,
            :travel_mode, :travel_note, :odometer_start, :odometer_end, :note
        )";

        $stmtOnsite = $condb->prepare($sqlOnsite);
        $stmtOnsite->execute([
            ':ticket_id' => $ticket_id,
            ':start_location' => $onsite_start_location,
            ':end_location' => $onsite_end_location,
            ':travel_mode' => $onsite_travel_mode,
            ':travel_note' => $onsite_travel_note,
            ':odometer_start' => $onsite_odometer_start,
            ':odometer_end' => $onsite_odometer_end,
            ':note' => $onsite_note
        ]);
    }

    // INSERT Watchers
    if (!empty($watchers) && is_array($watchers)) {
        $sqlWatcher = "INSERT INTO service_ticket_watchers (watcher_id, ticket_id, user_id, added_by)
                       VALUES (UUID(), :ticket_id, :user_id, :added_by)";
        $stmtWatcher = $condb->prepare($sqlWatcher);

        foreach ($watchers as $watcher_id) {
            if (!empty($watcher_id)) {
                $stmtWatcher->execute([
                    ':ticket_id' => $ticket_id,
                    ':user_id' => $watcher_id,
                    ':added_by' => $created_by
                ]);
            }
        }
    }

    // INSERT Timeline Entry (สร้าง Ticket)
    $actor = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
    $role = $_SESSION['role'] ?? 'User';

    $sqlTimeline = "INSERT INTO service_ticket_timeline (
        timeline_id, ticket_id, `order`, actor, action, detail, location
    ) VALUES (
        UUID(), :ticket_id, 1, :actor, :action, :detail, :location
    )";

    $stmtTimeline = $condb->prepare($sqlTimeline);
    $stmtTimeline->execute([
        ':ticket_id' => $ticket_id,
        ':actor' => $actor . ' (' . $role . ')',
        ':action' => 'สร้าง Ticket',
        ':detail' => 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น',
        ':location' => $source
    ]);

    // Commit Transaction
    $condb->commit();

    // Success Response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'สร้าง Ticket สำเร็จ',
        'data' => [
            'ticket_id' => $ticket_id,
            'ticket_no' => $ticket_no,
            'redirect' => BASE_URL . 'pages/service/view_ticket.php?id=' . urlencode($ticket_id)
        ]
    ]);

} catch (PDOException $e) {
    // Rollback on error
    if ($condb->inTransaction()) {
        $condb->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database Error: ' . $e->getMessage()
    ]);

} catch (Exception $e) {
    // Rollback on error
    if ($condb->inTransaction()) {
        $condb->rollBack();
    }

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
