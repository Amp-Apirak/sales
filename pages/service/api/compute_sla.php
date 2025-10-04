<?php
/**
 * API: Compute SLA hours from Priority/Urgency/Impact
 * Method: POST
 * Input: csrf_token, priority, urgency, impact
 * Output: { success: bool, sla_hours?: int, message?: string }
 */

session_start();

header('Content-Type: application/json; charset=utf-8');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// CSRF check
if (empty($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

try {
    require_once __DIR__ . '/../../../config/condb.php';
    require_once __DIR__ . '/../sla_helpers.php';

    $priority = isset($_POST['priority']) ? trim((string)$_POST['priority']) : null;
    $urgency  = isset($_POST['urgency']) ? trim((string)$_POST['urgency']) : null;
    $impact   = isset($_POST['impact']) ? trim((string)$_POST['impact']) : null;

    $sla = computeSlaTarget($condb, $priority, $urgency, $impact);

    if ($sla === null) {
        echo json_encode(['success' => false, 'message' => 'Unable to compute SLA']);
    } else {
        echo json_encode(['success' => true, 'sla_hours' => (int)$sla]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

