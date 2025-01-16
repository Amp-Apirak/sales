<?php
// edit_task.php
session_start();
include('../../../config/condb.php');

// ตรวจสอบว่าเป็น GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

// รับค่า task_id
$taskId = $_GET['task_id'] ?? null;

if (!$taskId) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบรหัสงาน']);
    exit;
}

try {
    // ดึงข้อมูล task และผู้รับผิดชอบ
    $stmt = $condb->prepare("
        SELECT 
            t.*,
            GROUP_CONCAT(ta.user_id) as assigned_users
        FROM project_tasks t
        LEFT JOIN project_task_assignments ta ON t.task_id = ta.task_id
        WHERE t.task_id = ?
        GROUP BY t.task_id
    ");
    $stmt->execute([$taskId]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        throw new Exception('ไม่พบข้อมูลงาน');
    }

    // แปลง assigned_users จาก string เป็น array
    $task['assigned_users'] = $task['assigned_users'] ? explode(',', $task['assigned_users']) : [];

    // ส่ง Response
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'data' => $task
    ]);
} catch (Exception $e) {
    // Log error
    error_log("Error in edit_task.php: " . $e->getMessage());

    // ส่ง Response error
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
