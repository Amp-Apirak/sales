<?php
// เริ่มต้น session
session_start();

require_once '../../../../config/condb.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // ตรวจสอบว่ามี project_id ถูกส่งมาหรือไม่
    if (!isset($_GET['project_id'])) {
        throw new Exception('ไม่พบรหัสโครงการ');
    }

    $project_id = decryptUserId($_GET['project_id']);

    // Log the received project_id
    error_log("Received project_id: " . $project_id);

    // เตรียมคำสั่ง SQL
    $sql = "SELECT t.*, 
            GROUP_CONCAT(DISTINCT CONCAT(u.first_name, ' ', u.last_name)) as assignee_names
            FROM project_tasks t
            LEFT JOIN project_task_assignments ta ON t.task_id = ta.task_id
            LEFT JOIN users u ON ta.user_id = u.user_id
            WHERE t.project_id = :project_id
            GROUP BY t.task_id";

    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':project_id', $project_id, PDO::PARAM_STR);
    $stmt->execute();

    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'tasks' => $tasks,
        'debug' => [
            'project_id' => $project_id,
            'sql' => $sql,
            'row_count' => count($tasks)
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}