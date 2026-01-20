<?php
include '../../include/Add_session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['image_id'])) {
    $image_id = $_POST['image_id'];

    try {
        // ดึงข้อมูลรูปภาพจากฐานข้อมูล
        $stmt = $condb->prepare("SELECT file_path FROM category_image WHERE id = :image_id");
        $stmt->bindParam(':image_id', $image_id, PDO::PARAM_STR);
        $stmt->execute();
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            // ลบไฟล์จากระบบไฟล์
            if (file_exists($image['file_path']) && unlink($image['file_path'])) {
                // ลบข้อมูลจากฐานข้อมูล
                $stmt = $condb->prepare("DELETE FROM category_image WHERE id = :image_id");
                $stmt->bindParam(':image_id', $image_id, PDO::PARAM_STR);
                $result = $stmt->execute();

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'รูปภาพถูกลบเรียบร้อยแล้ว']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบข้อมูลจากฐานข้อมูลได้']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบไฟล์รูปภาพได้']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลรูปภาพ']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'คำขอไม่ถูกต้อง']);
}
