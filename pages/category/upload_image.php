<?php
include '../../include/Add_session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['imageFile']) && isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
    $file = $_FILES['imageFile'];

    // ตรวจสอบว่า category_id ไม่เป็นค่าว่าง
    if (empty($category_id)) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบ category_id']);
        exit;
    }

    // ตรวจสอบประเภทไฟล์
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'ประเภทไฟล์ไม่ถูกต้อง']);
        exit;
    }

    // สร้างชื่อไฟล์ใหม่
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $extension;

    // กำหนดตำแหน่งที่จะบันทึกไฟล์
    $upload_dir = '../../uploads/category_images/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $upload_path = $upload_dir . $new_filename;

    // อัปโหลดไฟล์
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // สร้าง UUID สำหรับ ID
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));

        // บันทึกข้อมูลลงฐานข้อมูล
        try {
            $stmt = $condb->prepare("INSERT INTO category_image (id, category_id, file_name, file_path, file_type, file_size) VALUES (:id, :category_id, :file_name, :file_path, :file_type, :file_size)");
            $stmt->execute([
                ':id' => $uuid,
                ':category_id' => $category_id,
                ':file_name' => $new_filename,
                ':file_path' => $upload_path,
                ':file_type' => $file['type'],
                ':file_size' => $file['size']
            ]);
            echo json_encode(['success' => true, 'message' => 'อัปโหลดรูปภาพสำเร็จ']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปโหลดไฟล์ได้']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
}
