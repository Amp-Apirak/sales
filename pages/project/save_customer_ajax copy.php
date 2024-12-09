<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'] ?? '';
$team_id = $_SESSION['team_id'] ?? 0;
$created_by = $_SESSION['user_id'] ?? 0;
$user_id = $_SESSION['user_id'] ?? 0;


// ตรวจสอบสิทธิ์การเข้าถึง
if (!in_array($role, ['Executive', 'Sale Supervisor', 'Seller'])) {
    header("Location: unauthorized.php");
    exit();
}

header('Content-Type: application/json');

// ตรวจสอบว่าเป็นการเรียกผ่าน AJAX
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    try {
        // ตรวจสอบและทำความสะอาดข้อมูล
        $customer_name = trim($_POST['customer_name'] ?? '');
        if (empty($customer_name)) {
            throw new Exception("กรุณากรอกชื่อลูกค้า");
        }

        // สร้าง UUID
        function generateUUID()
        {
            return sprintf(
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
        }

        $customer_id = generateUUID();
        $company = trim($_POST['company'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $office_phone = trim($_POST['office_phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $remark = trim($_POST['remark'] ?? '');
        $created_by = $_SESSION['user_id'] ?? null;

        // เริ่ม Transaction
        $condb->beginTransaction();

        // เตรียม SQL query
        $sql = "INSERT INTO customers (
            customer_id, customer_name, company, position, phone, 
            email, office_phone, address, remark, created_by, created_at
        ) VALUES (
            :customer_id, :customer_name, :company, :position, :phone,
            :email, :office_phone, :address, :remark, :created_by, NOW()
        )";

        $stmt = $condb->prepare($sql);
        $stmt->execute([
            ':customer_id' => $customer_id,
            ':customer_name' => $customer_name,
            ':company' => $company,
            ':position' => $position,
            ':phone' => $phone,
            ':email' => $email,
            ':office_phone' => $office_phone,
            ':address' => $address,
            ':remark' => $remark,
            ':created_by' => $created_by
        ]);

        // Commit transaction
        $condb->commit();

        // ส่งข้อมูลกลับ
        echo json_encode([
            'success' => true,
            'message' => 'บันทึกข้อมูลสำเร็จ',
            'customer' => [
                'customer_id' => $customer_id,
                'customer_name' => $customer_name,
                'company' => $company
            ]
        ]);
    } catch (Exception $e) {
        // Rollback หากเกิดข้อผิดพลาด
        if ($condb->inTransaction()) {
            $condb->rollBack();
        }
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
