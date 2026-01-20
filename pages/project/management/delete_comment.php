<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
include('../../../config/condb.php');

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$comment_id = $_POST['comment_id'] ?? null;

if (!$comment_id) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบรหัสความคิดเห็น']);
    exit;
}

try {
    // ตรวจสอบว่าเป็นเจ้าของ comment หรือไม่
    $stmt = $condb->prepare("SELECT user_id FROM task_comments WHERE comment_id = ?");
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$comment) {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบความคิดเห็นนี้']);
        exit;
    }

    // เฉพาะเจ้าของหรือ Executive และ Account Management เท่านั้นที่ลบได้
    if ($comment['user_id'] !== $user_id && $role !== 'Executive' && $role !== 'Account Management') {
        echo json_encode(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ลบความคิดเห็นนี้']);
        exit;
    }

    // ลบแบบ Soft Delete
    $stmt = $condb->prepare("
        UPDATE task_comments
        SET is_deleted = 1, updated_at = NOW()
        WHERE comment_id = ?
    ");
    $stmt->execute([$comment_id]);

    echo json_encode([
        'status' => 'success',
        'message' => 'ลบความคิดเห็นสำเร็จ'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
    error_log("Error deleting comment: " . $e->getMessage());
}
?>
