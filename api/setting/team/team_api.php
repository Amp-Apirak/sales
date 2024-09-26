<?php
// เริ่มต้น session และเชื่อมต่อฐานข้อมูล
session_start();
include('../../../config/condb.php');

// ตรวจสอบสิทธิ์ผู้ใช้
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { // ตัวอย่าง: ให้เฉพาะ role 'admin' เท่านั้นที่เข้าถึงได้
//     http_response_code(403); // Forbidden
//     echo json_encode(['error' => 'Access denied.']);
//     exit;
// }

// ตั้งค่า Header เพื่อบอกว่าข้อมูลที่ส่งเป็น JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // กรองและตรวจสอบค่าการค้นหา (Search) เพื่อป้องกัน SQL Injection
    $search = isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '';
    $search = '%' . $search . '%';

    // กำหนดจำนวนรายการที่จะแสดงต่อหน้า (ตรวจสอบ limit และ page)
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $limit = $limit > 0 && $limit <= 100 ? $limit : 10; // จำกัดจำนวนรายการต่อหน้าที่ 100

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = $page > 0 ? $page : 1;

    $offset = ($page - 1) * $limit; // คำนวณ OFFSET สำหรับการแบ่งหน้า

    // Query ดึงข้อมูลทีมพร้อมชื่อ-นามสกุลของ team_leader
    $sql = "
        SELECT teams.*, CONCAT(users.first_name, ' ', users.last_name) AS team_leader_name
        FROM teams
        LEFT JOIN users ON teams.team_leader = users.user_id
        WHERE teams.team_name LIKE :search OR teams.team_description LIKE :search
        LIMIT :limit OFFSET :offset";
        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':search', $search);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

    // นำผลลัพธ์ออกมาในรูปแบบ associative array
    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query นับจำนวนรายการทั้งหมดสำหรับการแบ่งหน้า (Count Total Records)
    $count_sql = "SELECT COUNT(*) as total FROM teams WHERE team_name LIKE :search OR team_description LIKE :search";
    $count_stmt = $condb->prepare($count_sql);
    $count_stmt->bindParam(':search', $search);
    $count_stmt->execute();
    $total_rows = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // ส่งข้อมูลในรูปแบบ JSON พร้อมกับข้อมูลการแบ่งหน้าและจำนวนรายการทั้งหมด
    echo json_encode([
        'teams' => $teams,
        'total_pages' => ceil($total_rows / $limit),  // จำนวนหน้าทั้งหมด
        'current_page' => $page,                      // หน้าปัจจุบัน
        'total_records' => $total_rows                // จำนวนรายการทั้งหมด (Total Records)
    ]);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'An unexpected error occurred. Please try again later.']);
}
