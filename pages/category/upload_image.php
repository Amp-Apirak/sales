<?php
include '../../include/Add_session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['imageFile']) && isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
    $file = $_FILES['imageFile'];
    $user_id = $_SESSION['user_id'];

    if (empty($category_id)) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบ category_id']);
        exit;
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'ประเภทไฟล์ไม่ถูกต้อง']);
        exit;
    }

    // ดึงข้อมูลทีมและชื่อผู้ใช้
    try {
        $stmt = $condb->prepare("
    SELECT t.team_name, u.first_name 
    FROM users u 
    LEFT JOIN teams t ON u.team_id = t.team_id 
    WHERE u.user_id = :user_id
");
        $stmt->execute([':user_id' => $user_id]);
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user_info) {
            throw new Exception('ไม่พบข้อมูลผู้ใช้หรือทีม');
        }

        $team_folder = $user_info['team_name'] ?? 'NoTeam';
        $user_folder = $user_info['first_name'] ?? 'Unknown';

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $extension;

        $upload_dir = "../../uploads/category_images/{$team_folder}/{$user_folder}/";
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                throw new Exception('ไม่สามารถสร้างโฟลเดอร์สำหรับเก็บไฟล์ได้');
            }
        }
        $upload_path = $upload_dir . $new_filename;

        if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
            throw new Exception('ไม่สามารถอัปโหลดไฟล์ได้');
        }

        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));

        $stmt = $condb->prepare("INSERT INTO category_image (id, category_id, file_name, file_path, file_type, file_size, created_by, upload_path) VALUES (:id, :category_id, :file_name, :file_path, :file_type, :file_size, :created_by, :upload_path)");
        $stmt->execute([
            ':id' => $uuid,
            ':category_id' => $category_id,
            ':file_name' => $new_filename,
            ':file_path' => $upload_path,
            ':file_type' => $file['type'],
            ':file_size' => $file['size'],
            ':created_by' => $user_id,
            ':upload_path' => $upload_path
        ]);

        echo json_encode(['success' => true, 'message' => 'อัปโหลดรูปภาพสำเร็จ']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
}
