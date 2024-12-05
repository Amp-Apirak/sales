<?php
include '../../include/Add_session.php';

if (!isset($_GET['project_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing project ID']);
    exit;
}

try {
    $project_id = $_GET['project_id'];
    
    $sql = "SELECT dl.*, CONCAT(u.first_name, ' ', u.last_name) as created_by_name 
            FROM document_links dl 
            LEFT JOIN users u ON dl.created_by = u.user_id 
            WHERE dl.project_id = :project_id 
            ORDER BY dl.created_at DESC";
            
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':project_id', $project_id);
    $stmt->execute();
    
    $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'links' => $links]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>