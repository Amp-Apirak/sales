<?php
// Clear any previous output
ob_start();

include_once('../../../include/Add_session.php');
include_once('../../../config/condb.php');

// Clear buffer and set JSON header
ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

// Get session variables
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

try {
    $discussion_id = isset($_POST['discussion_id']) ? trim($_POST['discussion_id']) : '';
    $message_text = isset($_POST['message_text']) ? trim($_POST['message_text']) : '';

    if (empty($discussion_id) || empty($message_text)) {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
        exit;
    }

    // Check ownership or Executive role
    $stmt = $condb->prepare("SELECT * FROM project_discussions WHERE discussion_id = :id");
    $stmt->execute([':id' => $discussion_id]);
    $discussion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$discussion) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อความนี้']);
        exit;
    }

    // Check permission
    $can_edit = ($discussion['user_id'] === $user_id || $role === 'Executive' || $role === 'Account Management');

    if (!$can_edit) {
        echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์แก้ไขข้อความนี้']);
        exit;
    }

    // Update message
    $stmt = $condb->prepare("
        UPDATE project_discussions
        SET message_text = :message_text,
            is_edited = 1,
            updated_at = NOW()
        WHERE discussion_id = :discussion_id
    ");
    $stmt->execute([
        ':message_text' => $message_text,
        ':discussion_id' => $discussion_id
    ]);

    echo json_encode(['success' => true, 'message' => 'แก้ไขข้อความสำเร็จ']);

} catch (PDOException $e) {
    error_log("Edit discussion error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด']);
}
?>
