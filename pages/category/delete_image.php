<?php
include '../../include/Add_session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['image_id'])) {
    $image_id = $_POST['image_id'];

    // ลบไฟล์จากระบบไฟล์และลบข้อมูลจากฐานข้อมูล
    $stmt = $condb->prepare("DELETE FROM category_image WHERE id = :image_id");
    $stmt->bindParam(':image_id', $image_id, PDO::PARAM_STR);
    $result = $stmt->execute();

    echo json_encode(['success' => $result]);
} else {
    echo json_encode(['success' => false]);
}
