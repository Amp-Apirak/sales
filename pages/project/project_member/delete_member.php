<?php
session_start();
include('../../../config/condb.php');

// ตรวจสอบว่าเป็นการส่งข้อมูลแบบ POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// รับค่า member_id
$member_id = $_POST['member_id'] ?? '';

try {
    // เริ่ม transaction
    $condb->beginTransaction();

    // ลบข้อมูลสมาชิก
    $stmt = $condb->prepare("DELETE FROM project_members WHERE member_id = ?");
    $stmt->execute([$member_id]);

    // ตรวจสอบว่ามีการลบข้อมูลจริงหรือไม่
    if ($stmt->rowCount() === 0) {
        throw new Exception("ไม่พบข้อมูลสมาชิกที่ต้องการลบ");
    }

    // Commit transaction
    $condb->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'ลบข้อมูลสมาชิกเรียบร้อยแล้ว'
    ]);
} catch (Exception $e) {
    // หากเกิดข้อผิดพลาด ให้ rollback การทำงานทั้งหมด
    $condb->rollBack();

    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
