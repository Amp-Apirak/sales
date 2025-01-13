<?php
include('../../../../config/condb.php');

try {
    $stmt = $condb->prepare("
        SELECT user_id, first_name, last_name, position, role
        FROM users 
        WHERE role IN ('Seller', 'Engineer')
        ORDER BY first_name ASC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
