<?php
/**
 * ดึงรายการเอกสารของพนักงาน
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

// ตรวจสอบ employee_id
if (!isset($_GET['employee_id']) || empty($_GET['employee_id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลพนักงาน']);
    exit;
}

$employee_id = decryptUserId($_GET['employee_id']);

// ตรวจสอบสิทธิ์การเข้าถึง
$hasAccess = false;

if ($role === 'Executive') {
    $hasAccess = true;
} elseif ($role === 'Sale Supervisor') {
    $stmt = $condb->prepare("SELECT team_id FROM employees WHERE id = :employee_id");
    $stmt->execute([':employee_id' => $employee_id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee) {
        $employee_team_id = $employee['team_id'];
        $supervisor_team_ids = $_SESSION['team_ids'] ?? [];

        if ($team_id === 'ALL') {
            $hasAccess = in_array($employee_team_id, $supervisor_team_ids);
        } else {
            $hasAccess = ($employee_team_id === $team_id);
        }
    }
}

if (!$hasAccess) {
    echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ดูเอกสารพนักงานคนนี้']);
    exit;
}

// ดึงข้อมูลเอกสาร
try {
    $sql = "SELECT
                ed.document_id,
                ed.document_name,
                ed.document_category,
                ed.document_type,
                ed.file_size,
                ed.description,
                ed.upload_date,
                u.first_name,
                u.last_name
            FROM employee_documents ed
            LEFT JOIN users u ON ed.uploaded_by = u.user_id
            WHERE ed.employee_id = :employee_id
            ORDER BY ed.upload_date DESC";

    $stmt = $condb->prepare($sql);
    $stmt->execute([':employee_id' => $employee_id]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // แปลงหมวดหมู่เป็นภาษาไทย
    $category_names = [
        'resume' => 'เรซูเม่',
        'certificate' => 'ใบประกาศนียบัตร',
        'id_card' => 'บัตรประชาชน',
        'contract' => 'สัญญาจ้าง',
        'other' => 'อื่นๆ'
    ];

    // Format ข้อมูล
    foreach ($documents as &$doc) {
        $doc['category_name'] = $category_names[$doc['document_category']] ?? $doc['document_category'];
        $doc['file_size_formatted'] = formatFileSize($doc['file_size']);
        $doc['uploaded_by_name'] = $doc['first_name'] . ' ' . $doc['last_name'];
        $doc['upload_date_formatted'] = date('d/m/Y H:i', strtotime($doc['upload_date']));

        // เข้ารหัส document_id สำหรับ URL
        $doc['document_id_encrypted'] = encryptUserId($doc['document_id']);
    }

    echo json_encode([
        'success' => true,
        'documents' => $documents
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}

/**
 * แปลงขนาดไฟล์เป็น Human Readable
 */
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
