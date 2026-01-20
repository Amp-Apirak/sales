<?php
include '../../include/Add_session.php';

// ตรวจสอบ CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

try {
    // เตรียมข้อมูลสำหรับการอัพเดท
    $data = [
        'cost_id' => $_POST['cost_id'],
        'project_id' => $_POST['project_id'],
        'type' => $_POST['type'],
        'part_no' => $_POST['part_no'],
        'description' => $_POST['description'],
        'quantity' => $_POST['quantity'],
        'unit' => $_POST['unit'],
        'price_per_unit' => $_POST['price_per_unit'],
        'cost_per_unit' => $_POST['cost_per_unit'],
        'supplier' => $_POST['supplier'],
        'updated_by' => $_SESSION['user_id']
    ];

    // คำนวณ total_amount และ total_cost
    $data['total_amount'] = $data['quantity'] * $data['price_per_unit'];
    $data['total_cost'] = $data['quantity'] * $data['cost_per_unit'];

    // อัพเดทข้อมูลในตาราง project_costs
    $sql = "UPDATE project_costs 
            SET type = :type,
                part_no = :part_no,
                description = :description,
                quantity = :quantity,
                unit = :unit,
                price_per_unit = :price_per_unit,
                cost_per_unit = :cost_per_unit,
                total_amount = :total_amount,
                total_cost = :total_cost,
                supplier = :supplier,
                updated_by = :updated_by,
                updated_at = CURRENT_TIMESTAMP
            WHERE cost_id = :cost_id
            AND project_id = :project_id";

    $stmt = $condb->prepare($sql);
    if (!$stmt->execute($data)) {
        throw new Exception("Failed to update cost data");
    }

    // อัพเดทข้อมูลสรุป
    if (!updateProjectCostSummary($condb, $data['project_id'])) {
        throw new Exception("Failed to update cost summary");
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log("Error in edit_cost.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// ฟังก์ชันอัพเดทข้อมูลสรุป
function updateProjectCostSummary($condb, $project_id) {
    try {
        // คำนวณยอดรวมจากตาราง project_costs
        $sql = "SELECT 
                SUM(quantity * price_per_unit) as total_amount,
                SUM(quantity * cost_per_unit) as total_cost
                FROM project_costs 
                WHERE project_id = :project_id";
        
        $stmt = $condb->prepare($sql);
        $stmt->execute(['project_id' => $project_id]);
        $totals = $stmt->fetch(PDO::FETCH_ASSOC);

        // คำนวณค่าต่างๆ
        $total_amount = $totals['total_amount'] ?? 0;
        $total_cost = $totals['total_cost'] ?? 0;
        $vat_rate = 0.07;

        $vat_amount = $total_amount * $vat_rate;
        $cost_vat_amount = $total_cost * $vat_rate;
        $grand_total = $total_amount + $vat_amount;
        $total_cost_with_vat = $total_cost + $cost_vat_amount;
        $profit_amount = $grand_total - $total_cost_with_vat;
        $profit_percentage = ($grand_total > 0) ? ($profit_amount / $grand_total * 100) : 0;

        // อัพเดทหรือสร้างข้อมูลใหม่ในตาราง project_cost_summary
        $update_sql = "INSERT INTO project_cost_summary 
                      (project_id, total_amount, vat_amount, grand_total,
                       total_cost, cost_vat_amount, total_cost_with_vat,
                       profit_amount, profit_percentage, updated_at)
                      VALUES 
                      (:project_id, :total_amount, :vat_amount, :grand_total,
                       :total_cost, :cost_vat_amount, :total_cost_with_vat,
                       :profit_amount, :profit_percentage, CURRENT_TIMESTAMP)
                      ON DUPLICATE KEY UPDATE 
                      total_amount = VALUES(total_amount),
                      vat_amount = VALUES(vat_amount),
                      grand_total = VALUES(grand_total),
                      total_cost = VALUES(total_cost),
                      cost_vat_amount = VALUES(cost_vat_amount),
                      total_cost_with_vat = VALUES(total_cost_with_vat),
                      profit_amount = VALUES(profit_amount),
                      profit_percentage = VALUES(profit_percentage),
                      updated_at = CURRENT_TIMESTAMP";

        $stmt = $condb->prepare($update_sql);
        $result = $stmt->execute([
            'project_id' => $project_id,
            'total_amount' => $total_amount,
            'vat_amount' => $vat_amount,
            'grand_total' => $grand_total,
            'total_cost' => $total_cost,
            'cost_vat_amount' => $cost_vat_amount,
            'total_cost_with_vat' => $total_cost_with_vat,
            'profit_amount' => $profit_amount,
            'profit_percentage' => $profit_percentage
        ]);

        if (!$result) {
            throw new Exception("Failed to update cost summary");
        }

        return true;
    } catch (Exception $e) {
        error_log("Error updating cost summary: " . $e->getMessage());
        return false;
    }
}