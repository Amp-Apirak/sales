<?php
session_start();
include('../../../config/condb.php');
include('../../../config/validation.php');

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'กรุณาเข้าสู่ระบบก่อน'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode([
        'success' => false,
        'message' => 'CSRF token ไม่ถูกต้อง'
    ]);
    exit;
}

// Get comment_id from POST
$comment_id = sanitizeInput($_POST['comment_id'] ?? '');

if (empty($comment_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่พบ comment_id'
    ]);
    exit;
}

try {
    // Get comment details to check ownership
    $stmt = $condb->prepare("SELECT comment_id, created_by, ticket_id FROM service_ticket_comments WHERE comment_id = :comment_id AND deleted_at IS NULL");
    $stmt->execute([':comment_id' => $comment_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$comment) {
        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบความคิดเห็นนี้'
        ]);
        exit;
    }

    // Check permission: Executive can delete all, or must be the comment owner
    $canDelete = false;
    if ($role === 'Executive') {
        $canDelete = true;
    } elseif ($comment['created_by'] === $user_id) {
        $canDelete = true;
    }

    if (!$canDelete) {
        echo json_encode([
            'success' => false,
            'message' => 'คุณไม่มีสิทธิ์ลบความคิดเห็นนี้'
        ]);
        exit;
    }

    // Soft delete: Update deleted_at timestamp
    $stmtDelete = $condb->prepare("UPDATE service_ticket_comments SET deleted_at = NOW() WHERE comment_id = :comment_id");
    $stmtDelete->execute([':comment_id' => $comment_id]);

    // Log the deletion in service_ticket_history
    // Generate UUID for history record
    function generateUUID() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    $history_id = generateUUID();
    $logStmt = $condb->prepare("INSERT INTO service_ticket_history (history_id, ticket_id, field_name, old_value, new_value, changed_by, changed_at)
                                 VALUES (:history_id, :ticket_id, :field_name, :old_value, :new_value, :changed_by, NOW())");
    $logStmt->execute([
        ':history_id' => $history_id,
        ':ticket_id' => $comment['ticket_id'],
        ':field_name' => 'comment_deleted',
        ':old_value' => $comment_id,
        ':new_value' => 'deleted',
        ':changed_by' => $user_id
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'ลบความคิดเห็นเรียบร้อยแล้ว'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
?>
