<?php
// delete_task.php
session_start();
include('../../../config/condb.php');

// ตรวจสอบว่าเป็น POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

// ตรวจสอบ task_id
$taskId = isset($_POST['task_id']) ? $_POST['task_id'] : null;

if (!$taskId) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบรหัสงาน']);
    exit;
}

try {
    // เริ่ม Transaction
    $condb->beginTransaction();

    // ฟังก์ชันสำหรับลบ task และ sub-tasks แบบ recursive
    function deleteTaskAndChildren($condb, $taskId)
    {
        // ดึง sub-tasks ทั้งหมด
        $stmt = $condb->prepare("SELECT task_id FROM project_tasks WHERE parent_task_id = ?");
        $stmt->execute([$taskId]);

        // ลบ sub-tasks ก่อน (recursive)
        while ($subTask = $stmt->fetch()) {
            deleteTaskAndChildren($condb, $subTask['task_id']);
        }

        // ลบการมอบหมายงานที่เกี่ยวข้อง
        $stmt = $condb->prepare("DELETE FROM project_task_assignments WHERE task_id = ?");
        $stmt->execute([$taskId]);

        // ลบ task
        $stmt = $condb->prepare("DELETE FROM project_tasks WHERE task_id = ?");
        $stmt->execute([$taskId]);
    }

    // ตรวจสอบว่า task มีอยู่จริง
    $stmt = $condb->prepare("
        SELECT task_id, task_name 
        FROM project_tasks 
        WHERE task_id = ?
    ");
    $stmt->execute([$taskId]);
    $task = $stmt->fetch();

    if (!$task) {
        throw new Exception('ไม่พบข้อมูลงานที่ต้องการลบ');
    }

    // เริ่มการลบ task และ sub-tasks
    deleteTaskAndChildren($condb, $taskId);

    // Commit transaction
    $condb->commit();

    // ส่ง Response กลับ
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'ลบข้อมูลสำเร็จ',
        'task_id' => $taskId
    ]);
} catch (Exception $e) {
    // Rollback หากเกิดข้อผิดพลาด
    $condb->rollBack();

    // Log error
    error_log("Error in delete_task.php: " . $e->getMessage());

    // ส่ง Response error กลับ
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
