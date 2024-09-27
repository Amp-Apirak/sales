<?php
// เริ่มต้น session และเชื่อมต่อฐานข้อมูล
session_start();
require_once '../../../config/condb.php';

// ตั้งค่า Header เพื่อบอกว่าข้อมูลที่ส่งเป็น JSON
header('Content-Type: application/json; charset=utf-8');

// ฟังก์ชันสร้าง UUID สำหรับใช้เป็นรหัสเฉพาะ
function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// ฟังก์ชันทำความสะอาดข้อมูล input เพื่อป้องกันการโจมตี Cross-Site Scripting (XSS)
function clean_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ป้องกันการโจมตี CSRF
// ตรวจสอบว่า token ที่ส่งมากับ HTTP Headers ตรงกับ token ที่บันทึกใน session หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
        http_response_code(403); // Forbidden
        echo json_encode(['error' => 'Invalid CSRF token.']);
        exit();
    }
}

// ตรวจสอบสิทธิ์ผู้ใช้ (ในกรณีที่ต้องการให้เฉพาะบาง role เท่านั้นที่เข้าถึงได้)
// if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'Executive', 'Sale Supervisor'])) {
//     http_response_code(403); // Forbidden
//     echo json_encode(['error' => 'Access denied.']);
//     exit();
// }

try {
    $method = $_SERVER['REQUEST_METHOD']; // ตรวจสอบ HTTP Method ที่ส่งเข้ามา

    // เลือกการทำงานตาม HTTP Method
    switch ($method) {
        case 'GET':
            handleGetRequest($condb); // ดึงข้อมูลทีม
            break;
        case 'POST':
            handlePostRequest($condb); // เพิ่มข้อมูลทีมใหม่
            break;
        case 'PUT':
            handlePutRequest($condb); // อัปเดตข้อมูลทีม
            break;
        case 'DELETE':
            handleDeleteRequest($condb); // ลบข้อมูลทีม
            break;
        default:
            http_response_code(405); // Method Not Allowed (ไม่รองรับ Method อื่นๆ)
            echo json_encode(['error' => 'Method Not Allowed']);
    }
} catch (PDOException $e) {
    // จัดการกรณีเกิดข้อผิดพลาดที่ไม่คาดคิด เช่น ปัญหาฐานข้อมูล
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'An unexpected error occurred. Please try again later.']);
    error_log("Database error: " . $e->getMessage()); // บันทึกข้อผิดพลาดในไฟล์ log
}

// ฟังก์ชันจัดการ GET Request (ใช้ดึงข้อมูลทีมจากฐานข้อมูล)
function handleGetRequest($condb) {
    $search = isset($_GET['search']) ? '%' . clean_input($_GET['search']) . '%' : '%%'; // ตรวจสอบเงื่อนไขการค้นหา
    $limit = isset($_GET['limit']) ? max(1, min((int)$_GET['limit'], 100)) : 10; // กำหนด limit ของจำนวนข้อมูลต่อหน้า
    $page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1); // กำหนด page
    $offset = ($page - 1) * $limit; // คำนวณ OFFSET สำหรับแบ่งหน้า

    // คำสั่ง SQL สำหรับดึงข้อมูลทีม พร้อมชื่อผู้เป็นหัวหน้าทีม (JOIN กับตาราง users)
    $sql = "
        SELECT teams.*, CONCAT(users.first_name, ' ', users.last_name) AS team_leader_name
        FROM teams
        LEFT JOIN users ON teams.team_leader = users.user_id
        WHERE teams.team_name LIKE :search OR teams.team_description LIKE :search
        LIMIT :limit OFFSET :offset";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC); // ดึงข้อมูลในรูปแบบ associative array

    // นับจำนวนรายการทั้งหมดที่ตรงกับเงื่อนไขการค้นหา
    $count_sql = "SELECT COUNT(*) as total FROM teams WHERE team_name LIKE :search OR team_description LIKE :search";
    $count_stmt = $condb->prepare($count_sql);
    $count_stmt->bindParam(':search', $search, PDO::PARAM_STR);
    $count_stmt->execute();
    $total_rows = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // ส่งข้อมูลในรูปแบบ JSON พร้อมกับข้อมูลการแบ่งหน้า
    echo json_encode([
        'teams' => $teams,
        'total_pages' => ceil($total_rows / $limit),
        'current_page' => $page,
        'total_records' => $total_rows
    ]);
}

// ฟังก์ชันจัดการ POST Request (ใช้เพิ่มข้อมูลทีมใหม่)
function handlePostRequest($condb) {
    $data = json_decode(file_get_contents('php://input'), true); // รับข้อมูล JSON ที่ส่งมา

    // ตรวจสอบว่ามีการส่งข้อมูลที่จำเป็นหรือไม่
    if (!isset($data['team_name']) || !isset($data['team_leader'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }

    $team_id = generateUUID(); // สร้าง UUID สำหรับ team_id
    $team_name = clean_input($data['team_name']); // ทำความสะอาดข้อมูล team_name
    $team_description = isset($data['team_description']) ? clean_input($data['team_description']) : null;
    $team_leader = clean_input($data['team_leader']); // ทำความสะอาดข้อมูล team_leader

    // ตรวจสอบว่าข้อมูลที่จำเป็นไม่ว่างเปล่า
    if (empty($team_name) || empty($team_leader)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid data provided']);
        exit();
    }

    // SQL สำหรับเพิ่มข้อมูลทีมใหม่
    $sql = "INSERT INTO teams (team_id, team_name, team_description, team_leader) VALUES (:team_id, :team_name, :team_description, :team_leader)";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':team_id', $team_id);
    $stmt->bindParam(':team_name', $team_name);
    $stmt->bindParam(':team_description', $team_description);
    $stmt->bindParam(':team_leader', $team_leader);

    // ตรวจสอบว่าบันทึกข้อมูลสำเร็จหรือไม่
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Team added successfully!', 'team_id' => $team_id]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to add team']);
    }
}

// ฟังก์ชันจัดการ PUT Request (ใช้แก้ไขข้อมูลทีมที่มีอยู่แล้ว)
function handlePutRequest($condb) {
    $data = json_decode(file_get_contents('php://input'), true); // รับข้อมูล JSON ที่ส่งมา

    // ตรวจสอบว่ามีการส่งข้อมูลที่จำเป็นหรือไม่
    if (!isset($data['team_id']) || !isset($data['team_name']) || !isset($data['team_leader'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }

    $team_id = clean_input($data['team_id']);
    $team_name = clean_input($data['team_name']);
    $team_description = isset($data['team_description']) ? clean_input($data['team_description']) : null;
    $team_leader = clean_input($data['team_leader']);

    // ตรวจสอบว่าข้อมูลที่จำเป็นไม่ว่างเปล่า
    if (empty($team_id) || empty($team_name) || empty($team_leader)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid data provided']);
        exit();
    }

    // SQL สำหรับแก้ไขข้อมูลทีม
    $sql = "UPDATE teams SET team_name = :team_name, team_description = :team_description, team_leader = :team_leader WHERE team_id = :team_id";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':team_id', $team_id);
    $stmt->bindParam(':team_name', $team_name);
    $stmt->bindParam(':team_description', $team_description);
    $stmt->bindParam(':team_leader', $team_leader);

    // ตรวจสอบว่าการอัปเดตสำเร็จหรือไม่
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Team updated successfully!']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to update team']);
    }
}

// ฟังก์ชันจัดการ DELETE Request (ใช้ลบข้อมูลทีม)
function handleDeleteRequest($condb) {
    $data = json_decode(file_get_contents('php://input'), true); // รับข้อมูล JSON ที่ส่งมา

    // ตรวจสอบว่ามีการส่ง team_id มาหรือไม่
    if (!isset($data['team_id'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Missing required team_id']);
        exit();
    }

    $team_id = clean_input($data['team_id']); // ทำความสะอาดข้อมูล team_id

    // ตรวจสอบว่าข้อมูลที่จำเป็นไม่ว่างเปล่า
    if (empty($team_id)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid team_id provided']);
        exit();
    }

    // SQL สำหรับลบข้อมูลทีม
    $sql = "DELETE FROM teams WHERE team_id = :team_id";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':team_id', $team_id);

    // ตรวจสอบว่าการลบสำเร็จหรือไม่
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Team deleted successfully!']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to delete team']);
    }
}
