<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ตั้งค่า header เป็น JSON
header('Content-Type: application/json');

// ตรวจสอบว่ามีการส่งข้อมูลออกมาก่อนหน้านี้หรือไม่ และทำความสะอาด output buffer
ob_clean();

// จำกัดการเข้าถึงเฉพาะผู้ใช้ที่มีสิทธิ์เท่านั้น
if (!in_array($_SESSION['role'] ?? '', ['Executive', 'Sale Supervisor', 'Seller'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ตรวจสอบ CSRF Token
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'CSRF token ไม่ถูกต้อง'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ตรวจสอบว่าเป็น POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// รับค่า payment_id จาก POST data
$payment_id = $_POST['payment_id'] ?? '';

// ตรวจสอบว่า payment_id ไม่ว่างเปล่า
if (empty($payment_id)) {
    echo json_encode(['success' => false, 'message' => 'Payment ID is required']);
    exit;
}

try {
    // เริ่ม transaction
    $condb->beginTransaction();

    // เตรียม SQL statement สำหรับลบข้อมูล
    $sql = "DELETE FROM project_payments WHERE payment_id = :payment_id";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_STR);

    // ทำการลบข้อมูล
    if ($stmt->execute()) {
        // ตรวจสอบว่ามีการลบข้อมูลจริงหรือไม่
        if ($stmt->rowCount() > 0) {
            // ยืนยัน transaction
            $condb->commit();
            echo json_encode(['success' => true, 'message' => 'Payment deleted successfully']);
        } else {
            // ไม่พบข้อมูลที่จะลบ
            $condb->rollBack();
            echo json_encode(['success' => false, 'message' => 'No payment found with the given ID']);
        }
    } else {
        // เกิดข้อผิดพลาดในการลบข้อมูล
        $condb->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to delete payment']);
    }
} catch (PDOException $e) {
    // เกิดข้อผิดพลาดในการเชื่อมต่อหรือทำงานกับฐานข้อมูล
    $condb->rollBack();
    error_log("Database Error in delete_payment.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    // เกิดข้อผิดพลาดอื่นๆ
    $condb->rollBack();
    error_log("General Error in delete_payment.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$condb = null;

// จบการทำงาน
exit;