<?php
// pages/project/management/get_task_details.php

// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

try {
    // รับค่า task_id จาก GET request
    $task_id = $_GET['task_id'];

    // สร้างคำสั่ง SQL เพื่อดึงข้อมูลงานและผู้รับผิดชอบ
    $sql = "SELECT t.*, 
            GROUP_CONCAT(ta.user_id) as assigned_users
            FROM project_tasks t
            LEFT JOIN project_task_assignments ta ON t.task_id = ta.task_id
            WHERE t.task_id = :task_id
            GROUP BY t.task_id";

    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':task_id', $task_id);
    $stmt->execute();

    // ดึงข้อมูล
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($task) {
        // แปลง string ของ assigned_users เป็น array (ถ้ามีค่า)
        if ($task['assigned_users']) {
            $task['assigned_users'] = explode(',', $task['assigned_users']);
        } else {
            $task['assigned_users'] = [];
        }

        // จัดการวันที่ให้อยู่ในรูปแบบที่ถูกต้อง
        if ($task['start_date']) {
            $task['start_date'] = date('Y-m-d', strtotime($task['start_date']));
        }
        if ($task['end_date']) {
            $task['end_date'] = date('Y-m-d', strtotime($task['end_date']));
        }

        // ส่งข้อมูลกลับในรูปแบบ JSON
        echo json_encode([
            'success' => true,
            'data' => $task
        ]);
    } else {
        // กรณีไม่พบข้อมูล
        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบข้อมูลงาน'
        ]);
    }
} catch (Exception $e) {
    // กรณีเกิดข้อผิดพลาด
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}

// ปิดการเชื่อมต่อ
$condb = null;
