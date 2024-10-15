<?php
include '../../include/Add_session.php';

// ตรวจสอบ CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$document_id = $_POST['document_id'];

try {
    // ดึงข้อมูลไฟล์และทีม
    $sql = "SELECT pd.file_path, t.team_name 
            FROM project_documents pd
            JOIN projects p ON pd.project_id = p.project_id
            JOIN users u ON p.created_by = u.user_id
            JOIN teams t ON u.team_id = t.team_id
            WHERE pd.document_id = :document_id";
    $stmt = $condb->prepare($sql);
    $stmt->execute([':document_id' => $document_id]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($document) {
        // ลบไฟล์จากระบบไฟล์
        if (file_exists($document['file_path'])) {
            unlink($document['file_path']);
        }

        // ลบข้อมูลจากฐานข้อมูล
        $sql = "DELETE FROM project_documents WHERE document_id = :document_id";
        $stmt = $condb->prepare($sql);
        $stmt->execute([':document_id' => $document_id]);

        echo json_encode(['success' => true, 'message' => 'Document deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Document not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
