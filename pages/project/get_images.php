<?php
include '../../include/Add_session.php';

$project_id = $_GET['project_id'];

try {
    $sql = "SELECT pi.*, u.first_name, u.last_name 
            FROM project_images pi
            LEFT JOIN users u ON pi.uploaded_by = u.user_id
            WHERE pi.project_id = :project_id
            ORDER BY pi.upload_date DESC";
    $stmt = $condb->prepare($sql);
    $stmt->execute([':project_id' => $project_id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formatted_images = array_map(function ($image) {
        return [
            'id' => $image['image_id'],
            'name' => $image['image_name'],
            'url' => $image['file_path'],
            'size' => $image['file_size'],
            'type' => $image['file_type'],
            'upload_date' => $image['upload_date'],
            'uploaded_by' => $image['first_name'] . ' ' . $image['last_name']
        ];
    }, $images);

    echo json_encode(['success' => true, 'images' => $formatted_images]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
