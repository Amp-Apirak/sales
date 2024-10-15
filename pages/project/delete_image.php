<?php
include '../../include/Add_session.php';

// Check CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$image_id = $_POST['image_id'];

try {
    // First, get the file path
    $sql = "SELECT file_path FROM project_images WHERE image_id = :image_id";
    $stmt = $condb->prepare($sql);
    $stmt->execute([':image_id' => $image_id]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$image) {
        echo json_encode(['success' => false, 'message' => 'Image not found']);
        exit;
    }

    // Delete the file from the server
    if (file_exists($image['file_path'])) {
        unlink($image['file_path']);
    }

    // Delete the database record
    $sql = "DELETE FROM project_images WHERE image_id = :image_id";
    $stmt = $condb->prepare($sql);
    $stmt->execute([':image_id' => $image_id]);

    echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
