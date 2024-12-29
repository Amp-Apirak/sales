<?php
include '../../include/Add_session.php';

// ตรวจสอบ CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

try {
    // สร้าง UUID สำหรับ cost_id
    $cost_id = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );

    $data = [
        'cost_id' => $cost_id,
        'project_id' => $_POST['project_id'],
        'type' => $_POST['type'],
        'part_no' => $_POST['part_no'],
        'description' => $_POST['description'],
        'quantity' => $_POST['quantity'],
        'price_per_unit' => $_POST['price_per_unit'],
        'cost_per_unit' => $_POST['cost_per_unit'],
        'supplier' => $_POST['supplier'],
        'created_by' => $_SESSION['user_id']
    ];

    $sql = "INSERT INTO project_costs 
            (cost_id, project_id, type, part_no, description, quantity,
             price_per_unit, cost_per_unit, supplier, created_by)
            VALUES 
            (:cost_id, :project_id, :type, :part_no, :description, :quantity,
             :price_per_unit, :cost_per_unit, :supplier, :created_by)";

    $stmt = $condb->prepare($sql);
    $stmt->execute($data);

    // อัพเดทข้อมูลสรุป
    updateProjectCostSummary($condb, $_POST['project_id']);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function updateProjectCostSummary($condb, $project_id)
{
    // คำนวณยอดรวม
    $sql = "SELECT 
            SUM(total_amount) as total_amount,
            SUM(total_cost) as total_cost
            FROM project_costs 
            WHERE project_id = :project_id";

    $stmt = $condb->prepare($sql);
    $stmt->execute(['project_id' => $project_id]);
    $totals = $stmt->fetch(PDO::FETCH_ASSOC);

    // คำนวณค่าต่างๆ
    $vat_rate = 0.07;
    $total_amount = $totals['total_amount'] ?? 0;
    $total_cost = $totals['total_cost'] ?? 0;

    $vat_amount = $total_amount * $vat_rate;
    $cost_vat_amount = $total_cost * $vat_rate;

    $grand_total = $total_amount + $vat_amount;
    $total_cost_with_vat = $total_cost + $cost_vat_amount;

    $profit = $grand_total - $total_cost_with_vat;
    $profit_percentage = $grand_total > 0 ? ($profit / $grand_total * 100) : 0;

    // บันทึกข้อมูลสรุป
    $sql = "INSERT INTO project_cost_summary 
            (project_id, total_amount, vat_amount, grand_total,
             total_cost, cost_vat_amount, total_cost_with_vat,
             profit_amount, profit_percentage)
            VALUES 
            (:project_id, :total_amount, :vat_amount, :grand_total,
             :total_cost, :cost_vat_amount, :total_cost_with_vat,
             :profit_amount, :profit_percentage)
            ON DUPLICATE KEY UPDATE
            total_amount = VALUES(total_amount),
            vat_amount = VALUES(vat_amount),
            grand_total = VALUES(grand_total),
            total_cost = VALUES(total_cost),
            cost_vat_amount = VALUES(cost_vat_amount),
            total_cost_with_vat = VALUES(total_cost_with_vat),
            profit_amount = VALUES(profit_amount),
            profit_percentage = VALUES(profit_percentage)";

    $stmt = $condb->prepare($sql);
    $stmt->execute([
        'project_id' => $project_id,
        'total_amount' => $total_amount,
        'vat_amount' => $vat_amount,
        'grand_total' => $grand_total,
        'total_cost' => $total_cost,
        'cost_vat_amount' => $cost_vat_amount,
        'total_cost_with_vat' => $total_cost_with_vat,
        'profit_amount' => $profit,
        'profit_percentage' => $profit_percentage
    ]);
}
