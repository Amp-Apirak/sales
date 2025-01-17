<?php
require_once '../../config/condb.php';

// ตั้งค่า Header เพื่อบอกว่าข้อมูลที่ส่งเป็น JSON
header('Content-Type: application/json; charset=utf-8');

// ฟังก์ชันทำความสะอาดข้อมูล input เพื่อป้องกันการโจมตี Cross-Site Scripting (XSS)
function clean_input($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ฟังก์ชันสำหรับการสร้าง UUID
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

// ตรวจสอบ HTTP Method
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

// ----------------------------------------------------
// ฟังก์ชันการทำงานของ API
// ----------------------------------------------------

// ฟังก์ชันสำหรับดึงข้อมูลผู้ใช้ทั้งหมด พร้อมการค้นหาและแบ่งหน้า
function handleGetRequest($condb)
{
    $search = isset($_GET['search']) ? '%' . clean_input($_GET['search']) . '%' : '%%';
    $company = isset($_GET['company']) ? clean_input($_GET['company']) : '';
    $team = isset($_GET['team']) ? clean_input($_GET['team']) : '';
    $role = isset($_GET['role']) ? clean_input($_GET['role']) : '';
    $position = isset($_GET['position']) ? clean_input($_GET['position']) : '';
    $limit = isset($_GET['limit']) ? max(1, min((int)$_GET['limit'], 100)) : 10;
    $page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
    $offset = ($page - 1) * $limit;

    // SQL สำหรับค้นหาตามเงื่อนไขต่าง ๆ และการแบ่งหน้า
    $sql = "SELECT * FROM users WHERE (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)
            AND (company = :company OR :company = '')
            AND (team_id = :team OR :team = '')
            AND (role = :role OR :role = '')
            AND (position = :position OR :position = '')
            LIMIT :limit OFFSET :offset";

    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    $stmt->bindParam(':company', $company, PDO::PARAM_STR);
    $stmt->bindParam(':team', $team, PDO::PARAM_STR);
    $stmt->bindParam(':role', $role, PDO::PARAM_STR);
    $stmt->bindParam(':position', $position, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // นับจำนวนข้อมูลทั้งหมด
    $countSql = "SELECT COUNT(*) as total FROM users WHERE (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)
                 AND (company = :company OR :company = '')
                 AND (team_id = :team OR :team = '')
                 AND (role = :role OR :role = '')
                 AND (position = :position OR :position = '')";
    $countStmt = $condb->prepare($countSql);
    $countStmt->bindParam(':search', $search, PDO::PARAM_STR);
    $countStmt->bindParam(':company', $company, PDO::PARAM_STR);
    $countStmt->bindParam(':team', $team, PDO::PARAM_STR);
    $countStmt->bindParam(':role', $role, PDO::PARAM_STR);
    $countStmt->bindParam(':position', $position, PDO::PARAM_STR);
    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    echo json_encode([
        'users' => $users,
        'total_pages' => ceil($total / $limit),
        'current_page' => $page,
        'total_records' => $total
    ]);
}

// ฟังก์ชันสำหรับเพิ่มข้อมูลผู้ใช้ใหม่
function handlePostRequest($condb)
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['first_name']) || !isset($data['last_name']) || !isset($data['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }

    $user_id = generateUUID();
    $first_name = clean_input($data['first_name']);
    $last_name = clean_input($data['last_name']);
    $email = clean_input($data['email']);
    $role = isset($data['role']) ? clean_input($data['role']) : '';
    $company = isset($data['company']) ? clean_input($data['company']) : '';
    $team_id = isset($data['team_id']) ? clean_input($data['team_id']) : '';
    $position = isset($data['position']) ? clean_input($data['position']) : '';
    $password = password_hash($data['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (user_id, first_name, last_name, email, role, company, team_id, position, password)
            VALUES (:user_id, :first_name, :last_name, :email, :role, :company, :team_id, :position, :password)";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':company', $company);
    $stmt->bindParam(':team_id', $team_id);
    $stmt->bindParam(':position', $position);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User added successfully!']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add user']);
    }
}

// ฟังก์ชันสำหรับแก้ไขข้อมูลผู้ใช้
function handlePutRequest($condb)
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['user_id']) || !isset($data['first_name']) || !isset($data['last_name']) || !isset($data['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }

    $user_id = clean_input($data['user_id']);
    $first_name = clean_input($data['first_name']);
    $last_name = clean_input($data['last_name']);
    $email = clean_input($data['email']);
    $role = isset($data['role']) ? clean_input($data['role']) : '';
    $company = isset($data['company']) ? clean_input($data['company']) : '';
    $team_id = isset($data['team_id']) ? clean_input($data['team_id']) : '';
    $position = isset($data['position']) ? clean_input($data['position']) : '';

    $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, role = :role, company = :company, team_id = :team_id, position = :position WHERE user_id = :user_id";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':company', $company);
    $stmt->bindParam(':team_id', $team_id);
    $stmt->bindParam(':position', $position);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully!']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update user']);
    }
}

// ฟังก์ชันสำหรับลบข้อมูลผู้ใช้
function handleDeleteRequest($condb)
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['user_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing user_id']);
        exit();
    }

    $user_id = clean_input($data['user_id']);

    $sql = "DELETE FROM users WHERE user_id = :user_id";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully!']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete user']);
    }
}
