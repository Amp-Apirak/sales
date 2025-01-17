<?php
session_start();
include('../../../config/condb.php');

// ตรวจสอบ HTTP Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

try {
    // เริ่ม Transaction
    $condb->beginTransaction();

    // รับและทำความสะอาดข้อมูล
    $taskId = filter_var($_POST['task_id'] ?? null, FILTER_SANITIZE_STRING);
    $newParentId = !empty($_POST['new_parent_id']) ? filter_var($_POST['new_parent_id'], FILTER_SANITIZE_STRING) : null;
    $newOrder = filter_var($_POST['new_index'] ?? 0, FILTER_VALIDATE_INT);
    $newLevel = filter_var($_POST['new_level'] ?? 0, FILTER_VALIDATE_INT);

    // ตรวจสอบความถูกต้องของข้อมูล
    if (!$taskId) {
        throw new Exception('ไม่พบรหัสงาน');
    }

    // ตรวจสอบว่า task มีอยู่จริง
    $stmt = $condb->prepare("SELECT project_id FROM project_tasks WHERE task_id = ?");
    $stmt->execute([$taskId]);
    $projectId = $stmt->fetchColumn();

    if (!$projectId) {
        throw new Exception('ไม่พบงานที่ระบุ');
    }

    // ตรวจสอบ Circular Reference
    if ($newParentId) {
        $checkStmt = $condb->prepare("SELECT parent_task_id FROM project_tasks WHERE task_id = ?");
        $currentId = $newParentId;

        while ($currentId) {
            $checkStmt->execute([$currentId]);
            $parentId = $checkStmt->fetchColumn();

            if ($parentId === $taskId) {
                throw new Exception('ไม่สามารถย้ายงานไปเป็นงานย่อยของตัวเองได้');
            }
            $currentId = $parentId;
        }
    }

    // อัพเดตข้อมูลงานหลัก
    $updateStmt = $condb->prepare("
        UPDATE project_tasks 
        SET 
            parent_task_id = ?,
            task_order = ?,
            task_level = ?,
            updated_at = CURRENT_TIMESTAMP,
            updated_by = ?
        WHERE task_id = ?
    ");
    $updateStmt->execute([
        $newParentId,
        $newOrder,
        $newLevel,
        $_SESSION['user_id'],
        $taskId
    ]);

    // อัพเดตลำดับของงานย่อยแบบ recursive
    function updateSubtaskLevels($condb, $taskId, $parentLevel)
    {
        $level = $parentLevel + 1;

        $stmt = $condb->prepare("
            UPDATE project_tasks 
            SET task_level = ? 
            WHERE parent_task_id = ?
        ");
        $stmt->execute([$level, $taskId]);

        // ดึงงานย่อยเพื่ออัพเดตต่อ
        $subTasksStmt = $condb->prepare("
            SELECT task_id 
            FROM project_tasks 
            WHERE parent_task_id = ?
        ");
        $subTasksStmt->execute([$taskId]);

        while ($subtask = $subTasksStmt->fetch(PDO::FETCH_ASSOC)) {
            updateSubtaskLevels($condb, $subtask['task_id'], $level);
        }
    }

    // อัพเดตระดับของงานย่อยทั้งหมด
    if ($newLevel !== false) {
        updateSubtaskLevels($condb, $taskId, $newLevel);
    }

    // ปรับลำดับของงานอื่นในระดับเดียวกัน
    $reorderStmt = $condb->prepare("
        UPDATE project_tasks 
        SET 
            task_order = task_order + 1,
            updated_at = CURRENT_TIMESTAMP
        WHERE 
            project_id = ? AND
            (parent_task_id <=> ? OR (parent_task_id IS NULL AND ? IS NULL)) AND
            task_order >= ? AND
            task_id != ?
    ");
    $reorderStmt->execute([
        $projectId,
        $newParentId,
        $newParentId,
        $newOrder,
        $taskId
    ]);

    // Commit transaction
    $condb->commit();

    // ส่งผลลัพธ์กลับ
    echo json_encode([
        'status' => 'success',
        'message' => 'อัพเดตตำแหน่งงานเรียบร้อยแล้ว'
    ]);
} catch (Exception $e) {
    // Rollback transaction
    if ($condb->inTransaction()) {
        $condb->rollBack();
    }

    // Log error
    error_log("Error in update_task_position.php: " . $e->getMessage());

    // ส่งข้อความแสดงข้อผิดพลาด
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
