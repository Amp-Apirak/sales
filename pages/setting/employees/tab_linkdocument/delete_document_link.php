<?php
session_start();
include('../../../../config/condb.php');
require_once __DIR__ . '/../../../../config/validation.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$link_id = decryptUserId($_POST['link_id']);

try {
    $stmt = $condb->prepare("DELETE FROM employee_document_links WHERE link_id = :link_id");
    $stmt->execute([':link_id' => $link_id]);

    echo json_encode(['success' => true, 'message' => 'ลบลิงก์สำเร็จ']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด']);
}
