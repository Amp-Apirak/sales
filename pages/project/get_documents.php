<?php
include '../../include/Add_session.php';

$project_id = $_GET['project_id'];

try {
    $sql = "SELECT pd.*, u.first_name, u.last_name 
            FROM project_documents pd
            LEFT JOIN users u ON pd.uploaded_by = u.user_id
            WHERE pd.project_id = :project_id
            ORDER BY pd.upload_date DESC";
    $stmt = $condb->prepare($sql);
    $stmt->execute([':project_id' => $project_id]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formatted_documents = array_map(function($doc) {
        return [
            'document_id' => $doc['document_id'],
            'document_name' => $doc['document_name'],
            'document_type' => $doc['document_type'],
            'upload_date' => date('Y-m-d H:i:s', strtotime($doc['upload_date'])),
            'uploaded_by' => $doc['first_name'] . ' ' . $doc['last_name']
        ];
    }, $documents);

    echo json_encode(['success' => true, 'documents' => $formatted_documents]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}