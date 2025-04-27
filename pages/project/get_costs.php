<?php
include '../../include/Add_session.php';

try {
    $project_id = $_GET['project_id'];

    // ดึงข้อมูลต้นทุน
    $sql = "SELECT * FROM project_costs 
            WHERE project_id = :project_id 
            ORDER BY created_at ASC";
    $stmt = $condb->prepare($sql);
    $stmt->execute(['project_id' => $project_id]);
    $costs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ดึงข้อมูลสรุป
    $sql = "SELECT * FROM project_cost_summary 
            WHERE project_id = :project_id";
    $stmt = $condb->prepare($sql);
    $stmt->execute(['project_id' => $project_id]);
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'costs' => $costs,
        'summary' => $summary
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
