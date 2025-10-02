<?php
session_start();
include('../../../config/condb.php');

$attachment_id = $_GET['id'] ?? null;

if (!$attachment_id) {
    die('ไม่พบรหัสไฟล์');
}

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if (!$user_id) {
    die('กรุณาเข้าสู่ระบบ');
}

try {
    // ดึงข้อมูลไฟล์
    $sql = "SELECT
                tca.*,
                t.project_id
            FROM task_comment_attachments tca
            INNER JOIN project_tasks t ON tca.task_id = t.task_id
            WHERE tca.attachment_id = ?";

    $stmt = $condb->prepare($sql);
    $stmt->execute([$attachment_id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file) {
        die('ไม่พบไฟล์');
    }

    $project_id = $file['project_id'];

    // ตรวจสอบสิทธิ์การเข้าถึง
    $access_check = $condb->prepare("
        SELECT 1 FROM (
            SELECT user_id FROM project_members WHERE project_id = ? AND user_id = ?
            UNION
            SELECT user_id FROM project_task_assignments WHERE task_id = ? AND user_id = ?
            UNION
            SELECT created_by as user_id FROM project_tasks WHERE task_id = ? AND created_by = ?
        ) as access_list
    ");
    $access_check->execute([$project_id, $user_id, $file['task_id'], $user_id, $file['task_id'], $user_id]);

    $hasAccess = $access_check->fetch() || ($role === 'Executive');

    if (!$hasAccess) {
        die('คุณไม่มีสิทธิ์เข้าถึงไฟล์นี้');
    }

    // ตรวจสอบว่าไฟล์มีอยู่จริง
    if (!file_exists($file['file_path'])) {
        die('ไม่พบไฟล์ในระบบ');
    }

    // ส่งไฟล์ให้ดาวน์โหลด
    header('Content-Type: ' . $file['file_type']);
    header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
    header('Content-Length: ' . $file['file_size']);
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    readfile($file['file_path']);
    exit;

} catch (PDOException $e) {
    die('เกิดข้อผิดพลาด: ' . $e->getMessage());
}
?>
