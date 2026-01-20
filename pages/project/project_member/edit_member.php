<?php
session_start();
include('../../../config/condb.php');

// ตรวจสอบว่าเป็นการส่งข้อมูลแบบ POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// รับค่าจากฟอร์ม
$member_id = $_POST['member_id'] ?? '';
$role_id = $_POST['role_id'] ?? '';
$is_active = $_POST['is_active'] ?? '1';
$updated_by = $_SESSION['user_id'] ?? '';

try {
    // เริ่ม transaction
    $condb->beginTransaction();

    // อัพเดทข้อมูลสมาชิก
    $stmt = $condb->prepare("UPDATE project_members 
                            SET role_id = :role_id,
                                is_active = :is_active,
                                updated_by = :updated_by,
                                updated_at = CURRENT_TIMESTAMP,
                                left_date = CASE 
                                    WHEN :is_active = 0 THEN CURRENT_TIMESTAMP
                                    ELSE NULL
                                END
                            WHERE member_id = :member_id");

    $stmt->execute([
        ':role_id' => $role_id,
        ':is_active' => $is_active,
        ':updated_by' => $updated_by,
        ':member_id' => $member_id
    ]);

    // Commit transaction
    $condb->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'อัพเดทข้อมูลสมาชิกเรียบร้อยแล้ว'
    ]);
} catch (Exception $e) {
    // หากเกิดข้อผิดพลาด ให้ rollback การทำงานทั้งหมด
    $condb->rollBack();

    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
