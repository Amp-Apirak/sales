<?php
// pages\project\management\get_users.php

// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

try {
    // สร้างคำสั่ง SQL สำหรับดึงข้อมูลผู้ใช้
    $sql = "SELECT user_id, first_name, last_name, role, team_id FROM users WHERE 1 ORDER BY first_name";
    $stmt = $condb->prepare($sql);
    $stmt->execute();

    // ดึงข้อมูลทั้งหมด
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ส่งข้อมูลกลับในรูปแบบ JSON
    echo json_encode($users);
} catch (PDOException $e) {
    // ถ้ามีข้อผิดพลาดให้ส่ง error กลับไป
    $response = array(
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    );
    echo json_encode($response);
}

// ปิดการเชื่อมต่อ
$condb = null;
