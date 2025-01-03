<?php
session_start();
include('../../../config/condb.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field = $_POST['field'];
    $value = $_POST['value'];

    // ตรวจสอบว่าฟิลด์ที่ส่งมาถูกต้อง
    $allowedFields = ['personal_email', 'company_email', 'phone'];
    if (!in_array($field, $allowedFields)) {
        echo json_encode(['error' => 'Invalid field']);
        exit;
    }

    try {
        $sql = "SELECT COUNT(*) as count FROM employees WHERE $field = :value";
        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':value', $value, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            'isDuplicate' => $result['count'] > 0
        ]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
