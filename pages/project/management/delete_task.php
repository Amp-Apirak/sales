<?php
// pages/project/management/delete_task.php

// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// ตรวจสอบ CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid CSRF token'
    ]);
    exit;
}

// เตรียมการตอบกลับ
$response = array();

try {
    // รับค่า task_id
    $task_id = $_POST['task_id'];

    // เริ่ม Transaction
    $condb->beginTransaction();

    // ฟังก์ชันสำหรับลบ task และ sub-tasks ทั้งหมด
    function deleteTaskAndChildren($condb, $task_id)
    {
        // ค้นหา sub-tasks
        $sql = "SELECT task_id FROM project_tasks WHERE parent_task_id = :task_id";
        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();
        $subtasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ลบ sub-tasks (ถ้ามี)
        foreach ($subtasks as $subtask) {
            deleteTaskAndChildren($condb, $subtask['task_id']);
        }

        // ลบการมอบหมายงาน
        $sql = "DELETE FROM project_task_assignments WHERE task_id = :task_id";
        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();

        // ลบงาน
        $sql = "DELETE FROM project_tasks WHERE task_id = :task_id";
        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();
    }

    // เรียกใช้ฟังก์ชันลบงาน
    deleteTaskAndChildren($condb, $task_id);

    // Commit transaction
    $condb->commit();

    $response['success'] = true;
    $response['message'] = 'ลบข้อมูลสำเร็จ';
} catch (Exception $e) {
    // Rollback transaction ในกรณีที่เกิดข้อผิดพลาด
    $condb->rollBack();

    $response['success'] = false;
    $response['message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
}

// ส่งผลลัพธ์กลับ
echo json_encode($response);

// ปิดการเชื่อมต่อ
$condb = null;
