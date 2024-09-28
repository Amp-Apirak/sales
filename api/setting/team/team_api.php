<?php
// เริ่มต้น session และเชื่อมต่อฐานข้อมูล
session_start();
require_once '../../../config/condb.php';

// ตั้งค่า Header เพื่อบอกว่าข้อมูลที่ส่งเป็น JSON
header('Content-Type: application/json; charset=utf-8');

// ฟังก์ชันสร้าง UUID สำหรับใช้เป็นรหัสเฉพาะ
function generateUUID()
{
    return sprintf(
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
}

// ฟังก์ชันทำความสะอาดข้อมูล input เพื่อป้องกันการโจมตี Cross-Site Scripting (XSS)
function clean_input($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ป้องกันการโจมตี CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid CSRF token.']);
        exit();
    }
}

// ตรวจสอบสิทธิ์ผู้ใช้ (ถ้าต้องการ)
// if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'Executive', 'Sale Supervisor'])) {
//     http_response_code(403);
//     echo json_encode(['error' => 'Access denied.']);
//     exit();
// }

try {
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            handleGetRequest($condb);
            break;
        case 'POST':
            handlePostRequest($condb);
            break;
        case 'PUT':
            handlePutRequest($condb);
            break;
        case 'DELETE':
            handleDeleteRequest($condb);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'An unexpected error occurred.']);
    error_log("Database error: " . $e->getMessage());
}

// ฟังก์ชันจัดการ GET Request
function handleGetRequest($condb)
{
    // ตรวจสอบว่ามีการส่ง team_id มาหรือไม่
    if (isset($_GET['team_id'])) {
        $encoded_team_id = $_GET['team_id'];

        // ถอดรหัส team_id
        $team_id = decryptUserId($encoded_team_id);

        // SQL สำหรับดึงข้อมูลทีมโดยใช้ team_id
        $sql = "SELECT teams.*, CONCAT(users.first_name, ' ', users.last_name) AS team_leader_name
                FROM teams
                LEFT JOIN users ON teams.team_leader = users.user_id
                WHERE teams.team_id = :team_id";
        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR);
        $stmt->execute();

        $team = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($team) {
            echo json_encode(['success' => true, 'team' => $team]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Team not found']);
        }
    } else {
        // กรณีไม่ได้ส่ง team_id ให้ดึงข้อมูลทุกทีม
        $search = isset($_GET['search']) ? '%' . clean_input($_GET['search']) . '%' : '%%';
        $limit = isset($_GET['limit']) ? max(1, min((int)$_GET['limit'], 100)) : 10;
        $page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
        $offset = ($page - 1) * $limit;

        $sql = "SELECT teams.*, CONCAT(users.first_name, ' ', users.last_name) AS team_leader_name
                FROM teams
                LEFT JOIN users ON teams.team_leader = users.user_id
                WHERE teams.team_name LIKE :search OR teams.team_description LIKE :search
                LIMIT :limit OFFSET :offset";
        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $count_sql = "SELECT COUNT(*) as total FROM teams WHERE team_name LIKE :search OR team_description LIKE :search";
        $count_stmt = $condb->prepare($count_sql);
        $count_stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $count_stmt->execute();
        $total_rows = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

        echo json_encode([
            'teams' => $teams,
            'total_pages' => ceil($total_rows / $limit),
            'current_page' => $page,
            'total_records' => $total_rows
        ]);
    }
}

// ฟังก์ชันจัดการ POST Request
function handlePostRequest($condb)
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['team_name']) || !isset($data['team_leader'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }

    $team_id = generateUUID();
    $team_name = clean_input($data['team_name']);
    $team_description = isset($data['team_description']) ? clean_input($data['team_description']) : null;
    $team_leader = clean_input($data['team_leader']);

    if (empty($team_name) || empty($team_leader)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data provided']);
        exit();
    }

    $sql = "INSERT INTO teams (team_id, team_name, team_description, team_leader) VALUES (:team_id, :team_name, :team_description, :team_leader)";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':team_id', $team_id);
    $stmt->bindParam(':team_name', $team_name);
    $stmt->bindParam(':team_description', $team_description);
    $stmt->bindParam(':team_leader', $team_leader);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Team added successfully!', 'team_id' => $team_id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add team']);
    }
}

// ฟังก์ชันจัดการ PUT Request
function handlePutRequest($condb)
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['team_id']) || !isset($data['team_name']) || !isset($data['team_leader'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }

    $team_id = decryptUserId(clean_input($data['team_id']));
    $team_name = clean_input($data['team_name']);
    $team_description = isset($data['team_description']) ? clean_input($data['team_description']) : null;
    $team_leader = clean_input($data['team_leader']);

    if (empty($team_id) || empty($team_name) || empty($team_leader)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data provided']);
        exit();
    }

    $sql = "UPDATE teams SET team_name = :team_name, team_description = :team_description, team_leader = :team_leader WHERE team_id = :team_id";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':team_id', $team_id);
    $stmt->bindParam(':team_name', $team_name);
    $stmt->bindParam(':team_description', $team_description);
    $stmt->bindParam(':team_leader', $team_leader);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Team updated successfully!']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update team']);
    }
}

// ฟังก์ชันจัดการ DELETE Request
function handleDeleteRequest($condb)
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['team_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required team_id']);
        exit();
    }

    $team_id = clean_input($data['team_id']);

    if (empty($team_id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid team_id provided']);
        exit();
    }

    $sql = "DELETE FROM teams WHERE team_id = :team_id";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':team_id', $team_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Team deleted successfully!']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete team']);
    }
}
