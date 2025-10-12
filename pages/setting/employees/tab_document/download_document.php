<?php
/**
 * ดาวน์โหลดเอกสารของพนักงาน
 * ตรวจสอบสิทธิ์: Executive และ Sale Supervisor (ทีมตัวเอง)
 */

session_start();
include('../../../../config/condb.php');

// ตรวจสอบ Session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die('กรุณาเข้าสู่ระบบ');
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$team_id = $_SESSION['team_id'] ?? '';

// ตรวจสอบ document_id
if (!isset($_GET['document_id']) || empty($_GET['document_id'])) {
    die('ไม่พบเอกสารที่ต้องการ');
}

$document_id = decryptUserId($_GET['document_id']);

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
        die('ไม่พบเอกสาร');
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
        die('คุณไม่มีสิทธิ์ดาวน์โหลดเอกสารนี้');
    }

    // ตรวจสอบไฟล์
    $file_path = __DIR__ . '/../../../../' . $document['file_path'];

    if (!file_exists($file_path)) {
        die('ไม่พบไฟล์');
    }

    // กำหนด MIME Type
    $mime_types = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'zip' => 'application/zip'
    ];

    $extension = $document['document_type'];
    $content_type = $mime_types[$extension] ?? 'application/octet-stream';

    // ส่งไฟล์
    header('Content-Type: ' . $content_type);
    header('Content-Disposition: attachment; filename="' . $document['document_name'] . '.' . $extension . '"');
    header('Content-Length: ' . filesize($file_path));
    header('Cache-Control: private');
    header('Pragma: private');

    readfile($file_path);
    exit;

} catch (PDOException $e) {
    die('เกิดข้อผิดพลาด: ' . $e->getMessage());
}
