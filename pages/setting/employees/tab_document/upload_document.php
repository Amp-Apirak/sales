<?php
/**
 * อัปโหลดเอกสารของพนักงาน
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

// ตรวจสอบข้อมูลที่จำเป็น
if (!isset($_POST['employee_id']) || !isset($_POST['document_name']) || !isset($_POST['document_category'])) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// ถอดรหัส employee_id
$employee_id = decryptUserId($_POST['employee_id']);
$document_name = trim($_POST['document_name']);
$document_category = trim($_POST['document_category']);
$description = isset($_POST['description']) ? trim($_POST['description']) : null;

// ตรวจสอบสิทธิ์การเข้าถึง
$hasAccess = false;

if ($role === 'Executive') {
    $hasAccess = true;
} elseif ($role === 'Sale Supervisor') {
    // ตรวจสอบว่าพนักงานอยู่ในทีมหรือไม่
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
    echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์อัปโหลดเอกสารพนักงานคนนี้']);
    exit;
}

// ตรวจสอบไฟล์
if (!isset($_FILES['document_file']) || $_FILES['document_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเลือกไฟล์ที่ต้องการอัปโหลด']);
    exit;
}

$file = $_FILES['document_file'];
$file_name = $file['name'];
$file_size = $file['size'];
$file_tmp = $file['tmp_name'];
$file_type = mime_content_type($file_tmp);

// กำหนดประเภทไฟล์ที่อนุญาต
$allowed_types = [
    'application/pdf' => 'pdf',
    'application/msword' => 'doc',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
    'application/vnd.ms-excel' => 'xls',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'application/zip' => 'zip',
    'application/x-zip-compressed' => 'zip'
];

if (!array_key_exists($file_type, $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'ประเภทไฟล์ไม่ได้รับอนุญาต (อนุญาตเฉพาะ PDF, Word, Excel, Image, ZIP)']);
    exit;
}

// ตรวจสอบขนาดไฟล์
$max_size = 20 * 1024 * 1024; // 20MB
if ($file_size > $max_size) {
    echo json_encode(['success' => false, 'message' => 'ขนาดไฟล์เกิน 20MB']);
    exit;
}

// สร้างโฟลเดอร์เก็บไฟล์ (ถ้ายังไม่มี)
$upload_dir = __DIR__ . '/../../../../uploads/employee_documents/' . $employee_id . '/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// สร้างชื่อไฟล์ใหม่ด้วย UUID
try {
    $stmt = $condb->query("SELECT UUID() as uuid");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $uuid = $result['uuid'];
} catch (PDOException $e) {
    $uuid = uniqid('doc_', true);
}

$extension = pathinfo($file_name, PATHINFO_EXTENSION);
$new_file_name = $uuid . '.' . $extension;
$file_path = $upload_dir . $new_file_name;

// อัปโหลดไฟล์
if (!move_uploaded_file($file_tmp, $file_path)) {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปโหลดไฟล์ได้']);
    exit;
}

// บันทึกข้อมูลลงฐานข้อมูล
try {
    // สร้าง document_id
    $stmt = $condb->query("SELECT UUID() as document_id");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $document_id = $result['document_id'];

    $relative_path = 'uploads/employee_documents/' . $employee_id . '/' . $new_file_name;

    $sql = "INSERT INTO employee_documents
            (document_id, employee_id, document_name, document_category, document_type,
             file_path, file_size, description, uploaded_by)
            VALUES
            (:document_id, :employee_id, :document_name, :document_category, :document_type,
             :file_path, :file_size, :description, :uploaded_by)";

    $stmt = $condb->prepare($sql);
    $stmt->execute([
        ':document_id' => $document_id,
        ':employee_id' => $employee_id,
        ':document_name' => $document_name,
        ':document_category' => $document_category,
        ':document_type' => $allowed_types[$file_type],
        ':file_path' => $relative_path,
        ':file_size' => $file_size,
        ':description' => $description,
        ':uploaded_by' => $user_id
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'อัปโหลดเอกสารสำเร็จ',
        'document_id' => $document_id
    ]);

} catch (PDOException $e) {
    // ลบไฟล์ถ้าบันทึกฐานข้อมูลไม่สำเร็จ
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()
    ]);
}
