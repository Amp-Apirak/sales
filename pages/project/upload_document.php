<?php
include '../../include/Add_session.php';

// ตรวจสอบ CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

// ตรวจสอบว่ามีการส่งไฟล์มาหรือไม่
if (!isset($_FILES['documentFile']) || $_FILES['documentFile']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$project_id = $_POST['project_id'];
$document_name = $_POST['documentName'];
$file = $_FILES['documentFile'];

// สร้าง UUID สำหรับ document_id
$document_id = generateUUID();

// ดึงข้อมูลทีมของผู้ใช้
$user_id = $_SESSION['user_id'];
$sql = "SELECT t.team_name FROM users u JOIN teams t ON u.team_id = t.team_id WHERE u.user_id = :user_id";
$stmt = $condb->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$team = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$team) {
    echo json_encode(['success' => false, 'message' => 'User team not found']);
    exit;
}

$team_name = preg_replace('/[^a-zA-Z0-9_]/', '_', $team['team_name']);

// สร้างโฟลเดอร์สำหรับเก็บไฟล์ (ถ้ายังไม่มี)
$upload_dir = '../../uploads/project_documents/' . $team_name . '/' . $project_id . '/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// สร้างชื่อไฟล์ใหม่เพื่อป้องกันการซ้ำกัน
$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$new_filename = uniqid() . '.' . $file_extension;
$file_path = $upload_dir . $new_filename;

// ย้ายไฟล์ที่อัปโหลดไปยังโฟลเดอร์ที่กำหนด
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    // บันทึกข้อมูลลงในฐานข้อมูล
    try {
        $sql = "INSERT INTO project_documents (document_id, project_id, document_name, document_type, file_path, file_size, uploaded_by) 
                VALUES (:document_id, :project_id, :document_name, :document_type, :file_path, :file_size, :uploaded_by)";
        $stmt = $condb->prepare($sql);
        $stmt->execute([
            ':document_id' => $document_id,
            ':project_id' => $project_id,
            ':document_name' => $document_name,
            ':document_type' => $file_extension,
            ':file_path' => $file_path,
            ':file_size' => $file['size'],
            ':uploaded_by' => $user_id
        ]);

        echo json_encode(['success' => true, 'message' => 'File uploaded successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
}

// ฟังก์ชันสำหรับสร้าง UUID
function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}