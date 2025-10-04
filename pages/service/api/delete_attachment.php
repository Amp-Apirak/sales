<?php
// API: Delete an attachment from a ticket
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
    $attachment_id = $_POST['attachment_id'] ?? '';
    if (!$ticket_id || !$attachment_id) { throw new Exception('missing parameters'); }

    // fetch attachment
    $stmt = $condb->prepare("SELECT file_path FROM service_ticket_attachments WHERE attachment_id = :aid AND ticket_id = :tid LIMIT 1");
    $stmt->execute([':aid' => $attachment_id, ':tid' => $ticket_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) { throw new Exception('attachment not found'); }

    // Try delete file from filesystem (best-effort)
    $basename = basename($row['file_path']);
    $fsPath = realpath(__DIR__ . '/../../../uploads/service_tickets/' . $ticket_id);
    if ($fsPath !== false) {
        $fullPath = $fsPath . DIRECTORY_SEPARATOR . $basename;
        if (is_file($fullPath)) { @unlink($fullPath); }
    }

    // Delete from DB
    $del = $condb->prepare("DELETE FROM service_ticket_attachments WHERE attachment_id = :aid AND ticket_id = :tid");
    $del->execute([':aid' => $attachment_id, ':tid' => $ticket_id]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

