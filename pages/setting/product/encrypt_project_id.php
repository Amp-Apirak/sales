<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// ตั้งค่า header สำหรับ JSON response
header('Content-Type: application/json');

// ตรวจสอบการตั้งค่า Session เพื่อป้องกันกรณีที่ไม่ได้ล็อกอิน
if (!isset($_SESSION['role']) || !isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่ได้รับอนุญาตให้เข้าถึง'
    ]);
    exit;
}

// ตรวจสอบ method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Method ไม่ถูกต้อง'
    ]);
    exit;
}

// ตรวจสอบข้อมูลที่ส่งมา
if (!isset($_POST['project_id']) || empty(trim($_POST['project_id']))) {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่พบรหัสโครงการ'
    ]);
    exit;
}

$project_id = trim($_POST['project_id']);

try {
    // ตรวจสอบว่าโครงการมีอยู่จริงในฐานข้อมูล
    $check_sql = "SELECT project_id FROM projects WHERE project_id = :project_id LIMIT 1";
    $check_stmt = $condb->prepare($check_sql);
    $check_stmt->bindParam(':project_id', $project_id, PDO::PARAM_STR);
    $check_stmt->execute();

    if ($check_stmt->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบโครงการที่ระบุ'
        ]);
        exit;
    }

    // เข้ารหัส project_id โดยใช้ฟังก์ชัน encryptUserId
    // (สมมติว่ามีฟังก์ชันนี้ใน config หรือ include อื่น)
    if (function_exists('encryptUserId')) {
        $encrypted_id = encryptUserId($project_id);

        echo json_encode([
            'success' => true,
            'encrypted_id' => $encrypted_id,
            'message' => 'เข้ารหัสสำเร็จ'
        ]);
    } else {
        // หากไม่มีฟังก์ชัน encryptUserId ให้ใช้ base64 encode แทน
        $encrypted_id = base64_encode($project_id);

        echo json_encode([
            'success' => true,
            'encrypted_id' => $encrypted_id,
            'message' => 'เข้ารหัสสำเร็จ (fallback)'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
