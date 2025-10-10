<?php
include '../../include/Add_session.php';

header('Content-Type: application/json');

// รับค่า customer_id จาก GET
$customer_id = isset($_GET['customer_id']) ? htmlspecialchars($_GET['customer_id']) : '';
$customer_id = decryptUserId($customer_id);

if (!$customer_id) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบลูกค้าที่ต้องการลบ']);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'] ?? null;

try {
    // ตรวจสอบสิทธิ์การลบข้อมูลตาม Role
    $sql_check = "";
    if ($role == 'Executive') {
        // Executive สามารถลบลูกค้าได้ทั้งหมด
        $sql_check = "SELECT customer_id FROM customers WHERE customer_id = :customer_id";
        $stmt_check = $condb->prepare($sql_check);
        $stmt_check->bindParam(':customer_id', $customer_id);
    } elseif ($role == 'Account Management' || $role == 'Sale Supervisor') {
        // Account Management และ Sale Supervisor ลบได้เฉพาะลูกค้าในทีมเดียวกัน
        $team_ids = $_SESSION['team_ids'] ?? [];
        if ($team_id === 'ALL' && !empty($team_ids)) {
            $placeholders = implode(',', array_fill(0, count($team_ids), '?'));
            $sql_check = "SELECT c.customer_id FROM customers c
                          INNER JOIN user_teams ut ON c.created_by = ut.user_id
                          WHERE c.customer_id = ? AND ut.team_id IN ($placeholders)";
            $stmt_check = $condb->prepare($sql_check);
            $params = array_merge([$customer_id], $team_ids);
            $stmt_check->execute($params);
            $customer_exists = $stmt_check->fetch(PDO::FETCH_ASSOC);
            if (!$customer_exists) {
                echo json_encode(['status' => 'error', 'message' => 'ไม่พบลูกค้าที่ต้องการลบ หรือคุณไม่มีสิทธิ์ลบลูกค้านี้']);
                exit;
            }
            goto skip_execute;
        } else {
            $sql_check = "SELECT c.customer_id FROM customers c
                          INNER JOIN user_teams ut ON c.created_by = ut.user_id
                          WHERE c.customer_id = :customer_id AND ut.team_id = :team_id";
            $stmt_check = $condb->prepare($sql_check);
            $stmt_check->bindParam(':customer_id', $customer_id);
            $stmt_check->bindParam(':team_id', $team_id);
        }
    } else {
        // Seller ลบได้เฉพาะลูกค้าที่ตนเองสร้าง
        $sql_check = "SELECT customer_id FROM customers WHERE customer_id = :customer_id AND created_by = :created_by";
        $stmt_check = $condb->prepare($sql_check);
        $stmt_check->bindParam(':customer_id', $customer_id);
        $stmt_check->bindParam(':created_by', $user_id);
    }

    $stmt_check->execute();
    $customer_exists = $stmt_check->fetch(PDO::FETCH_ASSOC);

    skip_execute:
    if (!$customer_exists) {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบลูกค้าที่ต้องการลบ หรือคุณไม่มีสิทธิ์ลบลูกค้านี้']);
        exit;
    }

    // เริ่ม transaction
    $condb->beginTransaction();

    // ลบข้อมูลในตารางที่เกี่ยวข้องก่อน (project_customers)
    $sql_delete_project_customers = "DELETE FROM project_customers WHERE customer_id = :customer_id";
    $stmt_project_customers = $condb->prepare($sql_delete_project_customers);
    $stmt_project_customers->bindParam(':customer_id', $customer_id);
    $stmt_project_customers->execute();

    // ตรวจสอบว่ามีโครงการที่ใช้ลูกค้านี้เป็นลูกค้าหลักใน projects table หรือไม่
    $sql_check_main_customer = "SELECT p.project_id, p.project_name, p.contract_no, p.status, p.created_at 
                               FROM projects p 
                               WHERE p.customer_id = :customer_id 
                               ORDER BY p.created_at DESC";
    $stmt_check_main = $condb->prepare($sql_check_main_customer);
    $stmt_check_main->bindParam(':customer_id', $customer_id);
    $stmt_check_main->execute();
    $related_projects = $stmt_check_main->fetchAll(PDO::FETCH_ASSOC);

    if (count($related_projects) > 0) {
        $condb->rollBack();
        
        // สร้างรายการโครงการที่เกี่ยวข้อง
        $project_list = [];
        foreach ($related_projects as $project) {
            $project_list[] = [
                'project_id' => encryptUserId($project['project_id']), // เพิ่ม encrypted project_id
                'project_name' => $project['project_name'],
                'contract_no' => $project['contract_no'],
                'status' => $project['status'],
                'created_date' => date('d/m/Y', strtotime($project['created_at']))
            ];
        }
        
        echo json_encode([
            'status' => 'error',
            'message' => 'ไม่สามารถลบลูกค้าได้ เนื่องจากมีโครงการที่ใช้ลูกค้านี้เป็นลูกค้าหลัก',
            'details' => 'กรุณาเปลี่ยนลูกค้าหลักในโครงการเหล่านี้ก่อน:',
            'related_projects' => $project_list,
            'project_count' => count($related_projects)
        ]);
        exit;
    }

    // ลบข้อมูลใน customers table
    $sql_delete = "DELETE FROM customers WHERE customer_id = :customer_id";
    $stmt_delete = $condb->prepare($sql_delete);
    $stmt_delete->bindParam(':customer_id', $customer_id);

    if ($stmt_delete->execute()) {
        $condb->commit();
        echo json_encode(['status' => 'success', 'message' => 'ลบข้อมูลลูกค้าสำเร็จ']);
    } else {
        $condb->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด! ไม่สามารถลบข้อมูลลูกค้าได้']);
    }

} catch (PDOException $e) {
    $condb->rollBack();
    
    // ตรวจสอบ error code ของ PDOException
    if ($e->getCode() == 23000) { 
        // 23000 คือรหัสข้อผิดพลาดเกี่ยวกับ Foreign Key Constraint
        echo json_encode([
            'status' => 'error',
            'message' => 'ไม่สามารถลบลูกค้าได้ เนื่องจากมีข้อมูลอื่นๆที่เกี่ยวข้องกับลูกค้านี้อยู่',
        ]);
    } else {
        // แจ้งข้อผิดพลาดอื่น ๆ ที่ไม่ใช่ Foreign Key Constraint
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
} catch (Exception $e) {
    $condb->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>