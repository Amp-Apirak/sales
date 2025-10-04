<?php
/**
 * API: Upload Attachment for Service Ticket
 * Method: POST
 * Input: FormData with files
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
    $ticket_id = $_POST['ticket_id'] ?? null;

    if (empty($ticket_id)) {
        throw new Exception('ไม่พบ Ticket ID');
    }

    // ตรวจสอบว่ามีไฟล์ที่อัปโหลด
    if (empty($_FILES['attachments'])) {
        throw new Exception('กรุณาเลือกไฟล์ที่ต้องการอัปโหลด');
    }

    // ตั้งค่า Upload Directory
    $uploadDir = '../../../uploads/service_tickets/' . $ticket_id . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // ไฟล์ที่อนุญาต
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'docx', 'xlsx', 'doc', 'xls', 'txt'];
    $maxFileSize = 50 * 1024 * 1024; // 50 MB

    $uploadedFiles = [];
    $errors = [];

    // เริ่ม Transaction
    $condb->beginTransaction();

    // จัดการกับ multiple files
    $files = $_FILES['attachments'];
    $fileCount = is_array($files['name']) ? count($files['name']) : 1;

    for ($i = 0; $i < $fileCount; $i++) {
        // ดึงข้อมูลไฟล์
        $fileName = is_array($files['name']) ? $files['name'][$i] : $files['name'];
        $fileTmpName = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
        $fileSize = is_array($files['size']) ? $files['size'][$i] : $files['size'];
        $fileError = is_array($files['error']) ? $files['error'][$i] : $files['error'];

        // ตรวจสอบ Error
        if ($fileError !== UPLOAD_ERR_OK) {
            $errors[] = "ไฟล์ $fileName: เกิดข้อผิดพลาดในการอัปโหลด (Error code: $fileError)";
            continue;
        }

        // ตรวจสอบขนาดไฟล์
        if ($fileSize > $maxFileSize) {
            $errors[] = "ไฟล์ $fileName: ขนาดไฟล์เกิน 50 MB";
            continue;
        }

        // ตรวจสอบ Extension
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = "ไฟล์ $fileName: ประเภทไฟล์ไม่ได้รับอนุญาต";
            continue;
        }

        // สร้างชื่อไฟล์ใหม่ (unique)
        $newFileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $newFileName;

        // อัปโหลดไฟล์
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            // บันทึกลงฐานข้อมูล
            $mimeType = mime_content_type($uploadPath);

            $sql = "INSERT INTO service_ticket_attachments (
                attachment_id, ticket_id, file_name, file_path, file_size, file_type, mime_type, uploaded_by
            ) VALUES (
                UUID(), :ticket_id, :file_name, :file_path, :file_size, :file_type, :mime_type, :uploaded_by
            )";

            $stmt = $condb->prepare($sql);
            // Public URL path for download
            $publicPath = BASE_URL . 'uploads/service_tickets/' . $ticket_id . '/' . $newFileName;

            $stmt->execute([
                ':ticket_id' => $ticket_id,
                ':file_name' => $fileName,
                ':file_path' => $publicPath,
                ':file_size' => $fileSize,
                ':file_type' => $fileExtension,
                ':mime_type' => $mimeType,
                ':uploaded_by' => $_SESSION['user_id']
            ]);

            $uploadedFiles[] = [
                'original_name' => $fileName,
                'saved_name' => $newFileName,
                'size' => $fileSize,
                'path' => $publicPath
            ];
        } else {
            $errors[] = "ไฟล์ $fileName: ไม่สามารถย้ายไฟล์ไปยังโฟลเดอร์ปลายทางได้";
        }
    }

    // Commit Transaction
    $condb->commit();

    // Response
    if (!empty($uploadedFiles)) {
        echo json_encode([
            'success' => true,
            'message' => 'อัปโหลดไฟล์สำเร็จ ' . count($uploadedFiles) . ' ไฟล์',
            'data' => $uploadedFiles,
            'errors' => $errors
        ]);
    } else {
        throw new Exception('ไม่สามารถอัปโหลดไฟล์ได้: ' . implode(', ', $errors));
    }

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
