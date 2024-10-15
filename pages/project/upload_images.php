<?php
include '../../include/Add_session.php';
header('Content-Type: application/json');

// ตรวจสอบ CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$project_id = $_POST['project_id'];
$user_id = $_SESSION['user_id'];

// ดึงข้อมูล team_id ของผู้ใช้
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
$upload_dir = "../../uploads/project_images/{$team_name}/{$project_id}/";
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true) && !is_dir($upload_dir)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
        exit;
    }
}

$uploaded_images = [];

// ตรวจสอบว่ามีไฟล์ถูกอัปโหลดหรือไม่
if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
    echo json_encode(['success' => false, 'message' => 'No files were uploaded']);
    exit;
}

foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
    // ตรวจสอบว่าไฟล์มีข้อผิดพลาดหรือไม่
    if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Error uploading file: ' . $_FILES['images']['name'][$key]]);
        exit;
    }

    $file_name = $_FILES['images']['name'][$key];
    $file_size = $_FILES['images']['size'][$key];
    $file_tmp = $_FILES['images']['tmp_name'][$key];
    $file_type = $_FILES['images']['type'][$key];

    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $extensions = array("jpeg", "jpg", "png", "gif");

    // ตรวจสอบนามสกุลของไฟล์
    if (in_array($file_ext, $extensions)) {
        $unique_name = uniqid() . '.' . $file_ext;
        $file_path = $upload_dir . $unique_name;

        // ย้ายไฟล์ที่อัปโหลดไปยังโฟลเดอร์ที่กำหนด
        if (move_uploaded_file($file_tmp, $file_path)) {
            // ตรวจสอบ file_type อีกครั้งหลังจากอัปโหลด
            $file_type = mime_content_type($file_path);

            // บันทึกข้อมูลลงในฐานข้อมูล
            try {
                $sql = "INSERT INTO project_images (image_id, project_id, image_name, file_path, file_size, file_type, uploaded_by) 
                        VALUES (:image_id, :project_id, :image_name, :file_path, :file_size, :file_type, :uploaded_by)";
                $stmt = $condb->prepare($sql);
                $stmt->execute([
                    ':image_id' => generateUUID(),
                    ':project_id' => $project_id,
                    ':image_name' => $file_name,
                    ':file_path' => $file_path,
                    ':file_size' => $file_size,
                    ':file_type' => $file_type,
                    ':uploaded_by' => $user_id
                ]);

                $uploaded_images[] = [
                    'id' => $condb->lastInsertId(),
                    'name' => $file_name,
                    'url' => 'uploads/project_images/' . $team_name . '/' . $project_id . '/' . $unique_name,
                    'size' => $file_size,
                    'type' => $file_type
                ];
            } catch (PDOException $e) {
                error_log('Database error: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Database error occurred']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file: ' . $file_name]);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid file extension for file: ' . $file_name]);
        exit;
    }
}

// ตรวจสอบว่ามีรูปภาพที่อัปโหลดสำเร็จหรือไม่
if (empty($uploaded_images)) {
    echo json_encode(['success' => false, 'message' => 'No images were uploaded successfully']);
    exit;
}

// ส่งผลลัพธ์กลับไป
echo json_encode(['success' => true, 'images' => $uploaded_images]);

// ฟังก์ชันสำหรับสร้าง UUID
function generateUUID()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}
