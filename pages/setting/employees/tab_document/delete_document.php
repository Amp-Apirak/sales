<?php
/**
 * ลบเอกสารของพนักงาน
 * ตรวจสอบสิทธิ์: Executive และ Sale Supervisor (ทีมตัวเอง)
 */

session_start();
include('../../../../config/condb.php');

// ตรวจสอบ Session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$team_id = $_SESSION['team_id'] ?? '';

// ตรวจสอบ CSRF Token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

// ตรวจสอบ document_id
if (!isset($_POST['document_id']) || empty($_POST['document_id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบเอกสารที่ต้องการลบ']);
    exit;
}

$document_id = decryptUserId($_POST['document_id']);

// ดึงข้อมูลเอกสาร
try {
    $sql = "SELECT
                ed.*,
                e.team_id as employee_team_id
            FROM employee_documents ed
            LEFT JOIN employees e ON ed.employee_id = e.id
            WHERE ed.document_id = :document_id";

    $stmt = $condb->prepare($sql);
    $stmt->execute([':document_id' => $document_id]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$document) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบเอกสาร']);
        exit;
    }

    // ตรวจสอบสิทธิ์
    $hasAccess = false;

    if ($role === 'Executive') {
        $hasAccess = true;
    } elseif ($role === 'Sale Supervisor') {
        $employee_team_id = $document['employee_team_id'];
        $supervisor_team_ids = $_SESSION['team_ids'] ?? [];

        if ($team_id === 'ALL') {
            $hasAccess = in_array($employee_team_id, $supervisor_team_ids);
        } else {
            $hasAccess = ($employee_team_id === $team_id);
        }
    }

    if (!$hasAccess) {
        echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ลบเอกสารนี้']);
        exit;
    }

    // ลบไฟล์จาก Server
    $file_path = __DIR__ . '/../../../../' . $document['file_path'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // ลบข้อมูลจากฐานข้อมูล
    $stmt = $condb->prepare("DELETE FROM employee_documents WHERE document_id = :document_id");
    $stmt->execute([':document_id' => $document_id]);

    echo json_encode([
        'success' => true,
        'message' => 'ลบเอกสารสำเร็จ'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
