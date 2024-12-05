<?php
include '../../include/Add_session.php';

// ตรวจสอบ CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

if (!isset($_POST['link_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing link ID']);
    exit;
}

try {
    $link_id = $_POST['link_id'];
    
    $sql = "DELETE FROM document_links WHERE id = :link_id";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':link_id', $link_id);
    
    $result = $stmt->execute();
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'ลบข้อมูลสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>