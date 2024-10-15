<?php
include '../../include/Add_session.php';

// ตรวจสอบว่ามีการส่ง document_id มาหรือไม่
if (!isset($_GET['document_id'])) {
    die('ไม่พบ ID เอกสาร');
}

$document_id = $_GET['document_id'];

try {
    // ดึงข้อมูลเอกสารจากฐานข้อมูล
    $sql = "SELECT * FROM project_documents WHERE document_id = :document_id";
    $stmt = $condb->prepare($sql);
    $stmt->execute([':document_id' => $document_id]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$document) {
        die('ไม่พบเอกสาร');
    }

    $file_path = $document['file_path'];

    // ตรวจสอบว่าไฟล์มีอยู่จริง
    if (!file_exists($file_path)) {
        die('ไม่พบไฟล์เอกสาร');
    }

    // กำหนด MIME type ตามประเภทของไฟล์
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $file_path);
    finfo_close($file_info);

    // ส่ง header สำหรับการแสดงไฟล์
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
    header('Content-Length: ' . filesize($file_path));

    // อ่านและส่งเนื้อหาของไฟล์
    readfile($file_path);
} catch (PDOException $e) {
    die('เกิดข้อผิดพลาด: ' . $e->getMessage());
}
