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

try {
    // ลบข้อมูลในตารางที่เกี่ยวข้องก่อน
    $sql_delete_documents = "DELETE FROM project_documents WHERE project_id = :project_id";
    $stmt_documents = $condb->prepare($sql_delete_documents);
    $stmt_documents->bindParam(':project_id', $project_id);
    $stmt_documents->execute();

    // ลบข้อมูลใน projects
    $sql = "DELETE FROM projects WHERE project_id = :project_id AND created_by = :created_by";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':project_id', $project_id);
    $stmt->bindParam(':created_by', $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'ลบโครงการสำเร็จ']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด! ไม่สามารถลบโครงการได้']);
    }
} catch (PDOException $e) {
    // ตรวจสอบ error code ของ PDOException
    if ($e->getCode() == 23000) { // 23000 คือรหัสข้อผิดพลาดเกี่ยวกับ Foreign Key Constraint
        echo json_encode([
            'status' => 'error',
            'message' => 'ไม่สามารถลบโครงการได้ เนื่องจากมีไฟล์แนบ, เอกสาร, รูปภาพ หรือข้อมูลอื่นๆที่เกี่ยวข้องกับโครงการนี้อยู่',
        ]);
    } else {
        // แจ้งข้อผิดพลาดอื่น ๆ ที่ไม่ใช่ Foreign Key Constraint
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
}
