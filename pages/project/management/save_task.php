<?php
// pages\project\management\save_task.php

// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// ตรวจสอบ CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $response = array(
        'success' => false,
        'message' => 'Invalid CSRF token'
    );
    echo json_encode($response);
    exit;
}

// รับค่า JSON จาก POST request
$response = array();

try {
    // รับค่าจากฟอร์ม
    $task_id = !empty($_POST['task_id']) ? $_POST['task_id'] : null;
    $project_id = $_POST['project_id'];
    $parent_task_id = !empty($_POST['parent_task_id']) ? $_POST['parent_task_id'] : null;
    $task_name = $_POST['task_name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $progress = $_POST['progress'];
    $users = isset($_POST['users']) ? $_POST['users'] : array();

    // เริ่ม Transaction
    $condb->beginTransaction();

    if (empty($task_id)) {
        // สร้าง UUID สำหรับ task ใหม่
        $task_id = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );

        // เพิ่มงานใหม่
        $sql = "INSERT INTO project_tasks (task_id, project_id, parent_task_id, task_name, description, 
                start_date, end_date, status, progress, created_by) 
                VALUES (:task_id, :project_id, :parent_task_id, :task_name, :description, 
                :start_date, :end_date, :status, :progress, :created_by)";
    } else {
        // อัพเดทงานที่มีอยู่
        $sql = "UPDATE project_tasks SET 
                project_id = :project_id,
                parent_task_id = :parent_task_id,
                task_name = :task_name,
                description = :description,
                start_date = :start_date,
                end_date = :end_date,
                status = :status,
                progress = :progress,
                updated_by = :created_by,
                updated_at = CURRENT_TIMESTAMP
                WHERE task_id = :task_id";
    }

    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':task_id', $task_id);
    $stmt->bindParam(':project_id', $project_id);
    $stmt->bindParam(':parent_task_id', $parent_task_id);
    $stmt->bindParam(':task_name', $task_name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':progress', $progress);
    $stmt->bindParam(':created_by', $_SESSION['user_id']);
    $stmt->execute();

    // ลบการมอบหมายงานเก่า (ถ้ามี)
    $sql = "DELETE FROM project_task_assignments WHERE task_id = :task_id";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':task_id', $task_id);
    $stmt->execute();

    // เพิ่มการมอบหมายงานใหม่
    if (!empty($users)) {
        foreach ($users as $user_id) {
            $assignment_id = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff)
            );

            $sql = "INSERT INTO project_task_assignments (assignment_id, task_id, user_id, assigned_by) 
                    VALUES (:assignment_id, :task_id, :user_id, :assigned_by)";
            $stmt = $condb->prepare($sql);
            $stmt->bindParam(':assignment_id', $assignment_id);
            $stmt->bindParam(':task_id', $task_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':assigned_by', $_SESSION['user_id']);
            $stmt->execute();
        }
    }

    // Commit transaction
    $condb->commit();

    $response['success'] = true;
    $response['message'] = 'บันทึกข้อมูลสำเร็จ';
    $response['task_id'] = $task_id;
} catch (Exception $e) {
    // Rollback transaction
    $condb->rollBack();

    $response['success'] = false;
    $response['message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
}

// ส่งผลลัพธ์กลับ
echo json_encode($response);
