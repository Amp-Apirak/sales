<?php
include '../../include/Add_session.php';

try {
    $sql = "SELECT * FROM project_costs WHERE cost_id = :cost_id";
    $stmt = $condb->prepare($sql);
    $stmt->execute(['cost_id' => $_GET['cost_id']]);
    $cost = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cost) {
        echo json_encode([
            'success' => true,
            'cost' => $cost
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบข้อมูลต้นทุน'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}