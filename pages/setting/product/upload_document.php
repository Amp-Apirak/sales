<?php
session_start();
include('../../../config/condb.php');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

try {
    // ตรวจสอบไฟล์ที่อัปโหลด
    if (!isset($_FILES['document_file'])) {
        throw new Exception('ไม่พบไฟล์ที่อัปโหลด');
    }

    $file = $_FILES['document_file'];
    $product_id = $_POST['product_id'];
    $document_type = $_POST['document_type'];
    
    // สร้างโฟลเดอร์สำหรับเก็บไฟล์ถ้ายังไม่มี
    $upload_dir = "../../../uploads/product_documents/$product_id/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // สร้างชื่อไฟล์ใหม่เพื่อป้องกันการซ้ำ
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $extension;
    $file_path = $upload_dir . $new_filename;

    // ย้ายไฟล์ไปยังโฟลเดอร์
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception('ไม่สามารถอัปโหลดไฟล์ได้');
    }

    // บันทึกข้อมูลลงฐานข้อมูล
    $sql = "INSERT INTO product_documents (product_id, document_type, file_name, file_path, file_size, created_by) 
            VALUES (:product_id, :document_type, :file_name, :file_path, :file_size, :created_by)";
    
    $stmt = $condb->prepare($sql);
    $stmt->execute([
        ':product_id' => $product_id,
        ':document_type' => $document_type,
        ':file_name' => $file['name'],
        ':file_path' => $file_path,
        ':file_size' => $file['size'],
        ':created_by' => $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}