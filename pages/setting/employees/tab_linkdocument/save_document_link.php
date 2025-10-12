<?php
/**
 * บันทึกลิงก์เอกสารของพนักงาน
 */

session_start();
include('../../../../config/condb.php');
require_once __DIR__ . '/../../../../config/validation.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$team_id = $_SESSION['team_id'] ?? '';

if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$employee_id = decryptUserId($_POST['employee_id']);
$link_name = trim($_POST['link_name']);
$link_category = trim($_POST['link_category']);
$url = trim($_POST['url']);
$description = isset($_POST['description']) ? trim($_POST['description']) : null;
$link_id = isset($_POST['link_id']) ? decryptUserId($_POST['link_id']) : null;

// ตรวจสอบสิทธิ์
$hasAccess = false;
if ($role === 'Executive') {
    $hasAccess = true;
} elseif ($role === 'Sale Supervisor') {
    $stmt = $condb->prepare("SELECT team_id FROM employees WHERE id = :employee_id");
    $stmt->execute([':employee_id' => $employee_id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee) {
        $employee_team_id = $employee['team_id'];
        $supervisor_team_ids = $_SESSION['team_ids'] ?? [];
        if ($team_id === 'ALL') {
            $hasAccess = in_array($employee_team_id, $supervisor_team_ids);
        } else {
            $hasAccess = ($employee_team_id === $team_id);
        }
    }
}

if (!$hasAccess) {
    echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์']);
    exit;
}

// Validate URL
if (!filter_var($url, FILTER_VALIDATE_URL) || strpos($url, 'https://') !== 0) {
    echo json_encode(['success' => false, 'message' => 'URL ไม่ถูกต้อง (ต้องเริ่มด้วย https://)']);
    exit;
}

try {
    if ($link_id) {
        // แก้ไข
        $sql = "UPDATE employee_document_links SET
                link_name = :link_name,
                link_category = :link_category,
                url = :url,
                description = :description,
                updated_by = :updated_by
                WHERE link_id = :link_id";

        $stmt = $condb->prepare($sql);
        $stmt->execute([
            ':link_name' => $link_name,
            ':link_category' => $link_category,
            ':url' => $url,
            ':description' => $description,
            ':updated_by' => $user_id,
            ':link_id' => $link_id
        ]);

        echo json_encode(['success' => true, 'message' => 'แก้ไขลิงก์สำเร็จ']);
    } else {
        // เพิ่มใหม่
        $stmt = $condb->query("SELECT UUID() as link_id");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $new_link_id = $result['link_id'];

        $sql = "INSERT INTO employee_document_links
                (link_id, employee_id, link_name, link_category, url, description, created_by)
                VALUES
                (:link_id, :employee_id, :link_name, :link_category, :url, :description, :created_by)";

        $stmt = $condb->prepare($sql);
        $stmt->execute([
            ':link_id' => $new_link_id,
            ':employee_id' => $employee_id,
            ':link_name' => $link_name,
            ':link_category' => $link_category,
            ':url' => $url,
            ':description' => $description,
            ':created_by' => $user_id
        ]);

        echo json_encode(['success' => true, 'message' => 'เพิ่มลิงก์สำเร็จ', 'link_id' => $new_link_id]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
