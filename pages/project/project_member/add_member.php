<?php
session_start();
include('../../../config/condb.php');

// ตรวจสอบว่าเป็นการส่งข้อมูลแบบ POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// รับค่าจากฟอร์ม
$project_id = $_POST['project_id'] ?? '';
$user_id = $_POST['user_id'] ?? '';
$role_id = $_POST['role_id'] ?? '';
$created_by = $_SESSION['user_id'] ?? '';

try {
    // เริ่ม transaction
    $condb->beginTransaction();

    // ตรวจสอบว่าผู้ใช้นี้เป็นสมาชิกในโครงการอยู่แล้วหรือไม่
    $stmt = $condb->prepare("SELECT COUNT(*) FROM project_members WHERE project_id = ? AND user_id = ?");
    $stmt->execute([$project_id, $user_id]);
    $exists = $stmt->fetchColumn();

    if ($exists > 0) {
        throw new Exception("ผู้ใช้นี้เป็นสมาชิกในโครงการอยู่แล้ว");
    }

    // เพิ่มสมาชิกใหม่
    $stmt = $condb->prepare("INSERT INTO project_members (
                                member_id,
                                project_id, 
                                user_id, 
                                role_id, 
                                is_active,
                                joined_date,
                                created_by,
                                created_at
                            ) VALUES (
                                UUID(),
                                :project_id,
                                :user_id,
                                :role_id,
                                1,
                                CURRENT_TIMESTAMP,
                                :created_by,
                                CURRENT_TIMESTAMP
                            )");

    $stmt->execute([
        ':project_id' => $project_id,
        ':user_id' => $user_id,
        ':role_id' => $role_id,
        ':created_by' => $created_by
    ]);

    // Commit transaction
    $condb->commit();

    // ส่งการตอบกลับ
    echo json_encode([
        'status' => 'success',
        'message' => 'เพิ่มสมาชิกเรียบร้อยแล้ว'
    ]);
} catch (Exception $e) {
    // หากเกิดข้อผิดพลาด ให้ rollback การทำงานทั้งหมด
    $condb->rollBack();

    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
