<?php
include_once('../../../include/Add_session.php');
include_once('../../../config/condb.php');

// Get session variables
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$team_id = isset($_SESSION['team_id']) ? $_SESSION['team_id'] : '';

$attachment_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (empty($attachment_id)) {
    die('ไม่พบข้อมูลไฟล์');
}

try {
    // Get attachment info
    $stmt = $condb->prepare("
        SELECT a.*, d.project_id
        FROM project_discussion_attachments a
        JOIN project_discussions d ON a.discussion_id = d.discussion_id
        WHERE a.attachment_id = :attachment_id
        AND d.is_deleted = 0
    ");
    $stmt->execute([':attachment_id' => $attachment_id]);
    $attachment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$attachment) {
        die('ไม่พบไฟล์นี้');
    }

    // Check user access to project
    $project_id = $attachment['project_id'];
    $access_check = false;

    if ($role === 'Executive') {
        $access_check = true;
    } elseif ($role === 'Sale Supervisor') {
        $stmt = $condb->prepare("
            SELECT p.* FROM projects p
            WHERE p.project_id = :project_id
            AND p.seller IN (SELECT user_id FROM user_teams WHERE team_id = :team_id)
        ");
        $stmt->execute([':project_id' => $project_id, ':team_id' => $team_id]);
        if ($stmt->fetch()) $access_check = true;
    } else {
        $stmt = $condb->prepare("
            SELECT * FROM projects
            WHERE project_id = :project_id
            AND (seller = :user_id OR project_id IN (
                SELECT project_id FROM project_members WHERE user_id = :user_id2
            ))
        ");
        $stmt->execute([':project_id' => $project_id, ':user_id' => $user_id, ':user_id2' => $user_id]);
        if ($stmt->fetch()) $access_check = true;
    }

    if (!$access_check) {
        die('คุณไม่มีสิทธิ์ดาวน์โหลดไฟล์นี้');
    }

    // File path
    $file_path = __DIR__ . '/../../../' . $attachment['file_path'];

    if (!file_exists($file_path)) {
        die('ไม่พบไฟล์ในระบบ');
    }

    // Send file to browser
    header('Content-Type: ' . $attachment['file_type']);
    header('Content-Disposition: attachment; filename="' . $attachment['file_name'] . '"');
    header('Content-Length: ' . $attachment['file_size']);
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    readfile($file_path);
    exit;

} catch (PDOException $e) {
    error_log("Download attachment error: " . $e->getMessage());
    die('เกิดข้อผิดพลาด');
}
?>
