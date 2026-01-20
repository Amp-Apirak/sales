<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
include('../../../config/condb.php');

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$comment_id = $_POST['comment_id'] ?? null;
$comment_text = trim($_POST['comment_text'] ?? '');

if (!$comment_id || empty($comment_text)) {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
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

    if ($comment['user_id'] !== $user_id) {
        echo json_encode(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์แก้ไขความคิดเห็นนี้']);
        exit;
    }

    // แก้ไข comment
    $stmt = $condb->prepare("
        UPDATE task_comments
        SET comment_text = ?, updated_at = NOW(), is_edited = 1
        WHERE comment_id = ?
    ");
    $stmt->execute([$comment_text, $comment_id]);

    echo json_encode([
        'status' => 'success',
        'message' => 'แก้ไขความคิดเห็นสำเร็จ'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
    error_log("Error editing comment: " . $e->getMessage());
}
?>
