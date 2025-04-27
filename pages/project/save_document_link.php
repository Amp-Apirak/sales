<?php
include '../../include/Add_session.php';

// ตรวจสอบ CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

// ตรวจสอบข้อมูลที่จำเป็น
if (!isset($_POST['project_id']) || !isset($_POST['document_name']) || !isset($_POST['url']) || !isset($_POST['category'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

try {
    $link_id = isset($_POST['link_id']) ? $_POST['link_id'] : null;
    $project_id = $_POST['project_id'];
    $category = $_POST['category'];
    $document_name = $_POST['document_name'];
    $url = $_POST['url'];
    $user_id = $_SESSION['user_id'];

    if ($link_id) {
        // อัปเดตข้อมูลที่มีอยู่
        $sql = "UPDATE document_links SET 
                category = :category,
                document_name = :document_name,
                url = :url,
                updated_at = CURRENT_TIMESTAMP,
                updated_by = :updated_by
                WHERE id = :link_id AND project_id = :project_id";

        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':link_id', $link_id);
    } else {
        // เพิ่มข้อมูลใหม่
        $sql = "INSERT INTO document_links (id, project_id, category, document_name, url, created_by) 
                VALUES (UUID(), :project_id, :category, :document_name, :url, :created_by)";

        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':created_by', $user_id);
    }

    // bind parameters ที่ใช้ร่วมกัน
    $stmt->bindParam(':project_id', $project_id);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':document_name', $document_name);
    $stmt->bindParam(':url', $url);
    if ($link_id) {
        $stmt->bindParam(':updated_by', $user_id);
    }

    $result = $stmt->execute();

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
