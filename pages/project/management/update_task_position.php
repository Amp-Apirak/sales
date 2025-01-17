<?php
session_start();
include('../../../config/condb.php');

// ตรวจสอบว่าเป็น POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

// รับค่าจาก POST
$taskId = $_POST['task_id'] ?? null;
$newParentId = $_POST['new_parent_id'] ?? null;
$newIndex = $_POST['new_index'] ?? 0;

if (!$taskId) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบรหัสงาน']);
    exit;
}

try {
    // เริ่ม Transaction
    $condb->beginTransaction();

    // อัพเดทลำดับงานทั้งหมดในระดับเดียวกัน
    if ($newParentId) {
        // กรณีย้ายไปเป็น sub-task
        $stmt = $condb->prepare("
            UPDATE project_tasks 
            SET parent_task_id = ?, 
                task_order = ?,
                updated_at = CURRENT_TIMESTAMP,
                updated_by = ?
            WHERE task_id = ?
        ");
        $stmt->execute([$newParentId, $newIndex, $_SESSION['user_id'], $taskId]);
    } else {
        // กรณีเป็นงานระดับบนสุด
        $stmt = $condb->prepare("
            UPDATE project_tasks 
            SET parent_task_id = NULL,
                task_order = ?,
                updated_at = CURRENT_TIMESTAMP,
                updated_by = ?
            WHERE task_id = ?
        ");
        $stmt->execute([$newIndex, $_SESSION['user_id'], $taskId]);
    }

    // จัดลำดับงานอื่นๆ ใหม่
    $stmt = $condb->prepare("
        SELECT task_id 
        FROM project_tasks 
        WHERE parent_task_id IS NULL 
        ORDER BY task_order ASC
    ");
    $stmt->execute();
    $tasks = $stmt->fetchAll();

    // อัพเดทลำดับทั้งหมด
    $order = 0;
    foreach ($tasks as $task) {
        if ($task['task_id'] !== $taskId) {
            $stmt = $condb->prepare("
                UPDATE project_tasks 
                SET task_order = ? 
                WHERE task_id = ?
            ");
            $stmt->execute([$order++, $task['task_id']]);
        }
    }

    // Commit transaction
    $condb->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'อัพเดทตำแหน่งสำเร็จ'
    ]);
} catch (Exception $e) {
    // Rollback กรณีเกิดข้อผิดพลาด
    $condb->rollBack();

    error_log("Error in update_task_position.php: " . $e->getMessage());

    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
