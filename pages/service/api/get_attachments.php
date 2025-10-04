<?php
// API: Get attachments list for a ticket
session_start();
include '../../../config/condb.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

try {
    $ticket_id = $_POST['ticket_id'] ?? '';
    if (!$ticket_id) { throw new Exception('missing ticket_id'); }

    $stmt = $condb->prepare("SELECT attachment_id, file_name, file_path, file_size, uploaded_at
                             FROM service_ticket_attachments
                             WHERE ticket_id = :tid
                             ORDER BY uploaded_at DESC");
    $stmt->execute([':tid' => $ticket_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows, 'count' => count($rows)]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

