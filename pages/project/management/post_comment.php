<?php
// ปิด error display เพื่อไม่ให้รบกวน JSON response
error_reporting(0);
ini_set('display_errors', 0);

session_start();
include('../../../config/condb.php');

// Clear any output buffer
if (ob_get_length()) ob_clean();

header('Content-Type: application/json; charset=utf-8');

// ฟังก์ชันสร้าง UUID
function generateUUID() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// ตรวจสอบ CSRF Token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
    exit;
}

// ตรวจสอบข้อมูลที่จำเป็น
if (!isset($_POST['task_id']) || !isset($_POST['project_id']) || !isset($_POST['comment_text'])) {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$task_id = $_POST['task_id'];
$project_id = $_POST['project_id'];
$comment_text = trim($_POST['comment_text']);
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

if (empty($comment_text)) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกความคิดเห็น']);
    exit;
}

try {
    $condb->beginTransaction();

    // สร้าง comment
    $comment_id = generateUUID();
    $comment_type = 'comment'; // ค่าเริ่มต้นเป็น comment ทั่วไป

    $sql = "INSERT INTO task_comments (
                comment_id, task_id, project_id, user_id, comment_text, comment_type, created_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, NOW()
            )";

    $stmt = $condb->prepare($sql);
    $stmt->execute([
        $comment_id,
        $task_id,
        $project_id,
        $user_id,
        $comment_text,
        $comment_type
    ]);

    // จัดการ File Uploads
    $uploadedFiles = [];
    if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
        $upload_dir = '../../../uploads/task_attachments/';

        // สร้างโฟลเดอร์ถ้ายังไม่มี
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $allowed_types = [
            'image/jpeg', 'image/png', 'image/gif',
            'application/pdf',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip', 'application/x-zip-compressed',
            'text/plain'
        ];

        $max_file_size = 10 * 1024 * 1024; // 10 MB

        foreach ($_FILES['attachments']['name'] as $key => $filename) {
            if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['attachments']['tmp_name'][$key];
                $file_size = $_FILES['attachments']['size'][$key];
                $file_type = $_FILES['attachments']['type'][$key];

                // ตรวจสอบขนาดไฟล์
                if ($file_size > $max_file_size) {
                    continue; // ข้ามไฟล์ที่ใหญ่เกิน
                }

                // ตรวจสอบประเภทไฟล์
                if (!in_array($file_type, $allowed_types)) {
                    continue; // ข้ามไฟล์ที่ไม่อนุญาต
                }

                // สร้างชื่อไฟล์ใหม่
                $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $new_filename = generateUUID() . '.' . $file_extension;
                $file_path = $upload_dir . $new_filename;

                // อัปโหลดไฟล์
                if (move_uploaded_file($file_tmp, $file_path)) {
                    // บันทึกข้อมูลไฟล์ในฐานข้อมูล
                    $attachment_id = generateUUID();

                    $sql_file = "INSERT INTO task_comment_attachments (
                                    attachment_id, comment_id, task_id, file_name, file_path,
                                    file_size, file_type, file_extension, uploaded_by, uploaded_at
                                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

                    $stmt_file = $condb->prepare($sql_file);
                    $stmt_file->execute([
                        $attachment_id,
                        $comment_id,
                        $task_id,
                        $filename,
                        $file_path,
                        $file_size,
                        $file_type,
                        $file_extension,
                        $user_id
                    ]);

                    $uploadedFiles[] = $filename;
                }
            }
        }
    }

    // สร้าง System Log สำหรับการอัปโหลดไฟล์
    if (!empty($uploadedFiles)) {
        $file_log_id = generateUUID();
        $file_count = count($uploadedFiles);
        $log_text = "อัปโหลดไฟล์ {$file_count} ไฟล์";

        $sql_log = "INSERT INTO task_comments (
                        comment_id, task_id, project_id, user_id, comment_text, comment_type, created_at
                    ) VALUES (?, ?, ?, ?, ?, 'file_upload', NOW())";

        $stmt_log = $condb->prepare($sql_log);
        $stmt_log->execute([$file_log_id, $task_id, $project_id, $user_id, $log_text]);
    }

    $condb->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'โพสต์ความคิดเห็นสำเร็จ',
        'comment_id' => $comment_id,
        'uploaded_files' => $uploadedFiles
    ]);

} catch (PDOException $e) {
    $condb->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
    error_log("Error posting comment: " . $e->getMessage());
}
?>
