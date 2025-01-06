<?php
include '../../include/Add_session.php';

header('Content-Type: application/json');

$project_id = isset($_GET['project_id']) ? htmlspecialchars($_GET['project_id']) : '';
$project_id = decryptUserId($project_id);

if (!$project_id) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบโครงการที่ต้องการลบ']);
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "DELETE FROM projects WHERE project_id = :project_id AND created_by = :created_by";
$stmt = $condb->prepare($sql);
$stmt->bindParam(':project_id', $project_id);
$stmt->bindParam(':created_by', $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'ลบโครงการสำเร็จ']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด! ไม่สามารถลบโครงการได้']);
}
