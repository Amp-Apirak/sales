<?php
include '../../include/Add_session.php';

if (!isset($_GET['link_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing link ID']);
    exit;
}

try {
    $link_id = $_GET['link_id'];
    
    $sql = "SELECT * FROM document_links WHERE id = :link_id";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':link_id', $link_id);
    $stmt->execute();
    
    $link = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($link) {
        echo json_encode(['success' => true, 'link' => $link]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลลิงก์']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>