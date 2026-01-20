<?php
// Clear any previous output
ob_start();

include_once('../../../include/Add_session.php');
include_once('../../../config/condb.php');
include_once('../../../config/validation.php');

// Clear buffer and set JSON header
ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

// Get session variables
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$team_id = isset($_SESSION['team_id']) ? $_SESSION['team_id'] : '';

try {
    // Get POST data
    $project_id = isset($_POST['project_id']) ? trim($_POST['project_id']) : '';
    $message_text = isset($_POST['message_text']) ? trim($_POST['message_text']) : '';

    // Validation
    if (empty($project_id)) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลโครงการ']);
        exit;
    }

    if (empty($message_text) && empty($_FILES['attachments']['name'][0])) {
        echo json_encode(['success' => false, 'message' => 'กรุณาพิมพ์ข้อความหรือแนบไฟล์']);
        exit;
    }

    // Check user access to project
    $access_check = false;
    if ($role === 'Executive' || $role === 'Account Management') {
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
        echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์เข้าถึงโครงการนี้']);
        exit;
    }

    // Generate UUID for discussion
    function generateUUID() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    $discussion_id = generateUUID();

    // Insert discussion
    $stmt = $condb->prepare("
        INSERT INTO project_discussions
        (discussion_id, project_id, user_id, message_text, created_at)
        VALUES (:discussion_id, :project_id, :user_id, :message_text, NOW())
    ");
    $stmt->execute([
        ':discussion_id' => $discussion_id,
        ':project_id' => $project_id,
        ':user_id' => $user_id,
        ':message_text' => $message_text
    ]);

    // Handle file uploads
    $uploaded_files = [];
    if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
        $upload_dir = __DIR__ . '/../../../uploads/discussion_attachments/';

        // Allowed file types (ปรับตามต้องการ)
        $allowed_types = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip', 'application/x-rar-compressed',
            'text/plain', 'text/csv'
        ];

        $max_size = 10485760; // 10MB per file

        for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
            if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['attachments']['tmp_name'][$i];
                $file_name = $_FILES['attachments']['name'][$i];
                $file_size = $_FILES['attachments']['size'][$i];
                $file_type = $_FILES['attachments']['type'][$i];

                // Validate file size
                if ($file_size > $max_size) {
                    continue; // Skip this file
                }

                // Validate file type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $file_tmp);
                finfo_close($finfo);

                if (!in_array($mime_type, $allowed_types)) {
                    continue; // Skip this file
                }

                // Sanitize filename
                $file_name = preg_replace("/[^a-zA-Z0-9._-]/", "_", $file_name);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                // Generate unique filename
                $unique_filename = generateUUID() . '.' . $file_ext;
                $upload_path = $upload_dir . $unique_filename;

                // Move file
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $attachment_id = generateUUID();

                    // Insert attachment record
                    $stmt_attach = $condb->prepare("
                        INSERT INTO project_discussion_attachments
                        (attachment_id, discussion_id, project_id, file_name, file_path, file_size, file_type, file_extension, uploaded_by, uploaded_at)
                        VALUES (:attachment_id, :discussion_id, :project_id, :file_name, :file_path, :file_size, :file_type, :file_extension, :uploaded_by, NOW())
                    ");
                    $stmt_attach->execute([
                        ':attachment_id' => $attachment_id,
                        ':discussion_id' => $discussion_id,
                        ':project_id' => $project_id,
                        ':file_name' => $file_name,
                        ':file_path' => 'uploads/discussion_attachments/' . $unique_filename,
                        ':file_size' => $file_size,
                        ':file_type' => $mime_type,
                        ':file_extension' => $file_ext,
                        ':uploaded_by' => $user_id
                    ]);

                    $uploaded_files[] = $file_name;
                }
            }
        }
    }

    $message = 'ส่งข้อความสำเร็จ';
    if (!empty($uploaded_files)) {
        $message .= ' (แนบไฟล์ ' . count($uploaded_files) . ' ไฟล์)';
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'discussion_id' => $discussion_id
    ]);

} catch (PDOException $e) {
    error_log("Discussion post error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>
