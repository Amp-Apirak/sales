<?php
include '../../include/Add_session.php';
header('Content-Type: application/json');

// ดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'] ?? '';
$team_id = $_SESSION['team_id'] ?? 0;
$created_by = $_SESSION['user_id'] ?? 0; // ดึง user_id จาก session

// ฟังก์ชันสำหรับสร้าง UUID
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // รับและทำความสะอาดข้อมูลที่ส่งมา
        $payment_id = isset($_POST['payment_id']) ? trim($_POST['payment_id']) : null;
        $project_id = trim($_POST['project_id']);
        $payment_number = intval($_POST['payment_number']);
        $amount = floatval($_POST['amount']);
        $payment_percentage = floatval($_POST['payment_percentage']);
        $due_date = trim($_POST['due_date']);
        $status = trim($_POST['status']);
        $payment_date = !empty($_POST['payment_date']) ? trim($_POST['payment_date']) : null;
        $amount_paid = floatval($_POST['amount_paid']);

        // ตรวจสอบข้อมูลที่จำเป็น
        if (empty($project_id) || $payment_number <= 0 || $amount <= 0) {
            throw new Exception("กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง");
        }

        // ดึงข้อมูลโครงการ
        $stmt = $condb->prepare("SELECT sale_vat FROM projects WHERE project_id = :project_id");
        $stmt->execute([':project_id' => $project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            throw new Exception("ไม่พบข้อมูลโครงการ");
        }

        $total_project_amount = $project['sale_vat'];

        // คำนวณค่าอัตโนมัติ
        if ($payment_percentage > 0) {
            $amount = ($payment_percentage / 100) * $total_project_amount;
        } else {
            $payment_percentage = ($amount / $total_project_amount) * 100;
        }

        if ($status === 'Paid') {
            $amount_paid = $amount;
        } elseif ($status !== 'Paid' && $amount_paid > 0) {
            $status = 'Partial';
        }

        $condb->beginTransaction();

        if ($payment_id) {
            // อัปเดตการชำระเงินที่มีอยู่
            $sql = "UPDATE project_payments SET 
                    payment_number = :payment_number,
                    amount = :amount,
                    payment_percentage = :payment_percentage,
                    due_date = :due_date,
                    status = :status,
                    payment_date = :payment_date,
                    amount_paid = :amount_paid,
                    remaining_amount = :remaining_amount,
                    payment_progress = :payment_progress,
                    updated_at = NOW(),
                    updated_by = :updated_by
                    WHERE payment_id = :payment_id AND project_id = :project_id";
            $params = [
                ':payment_id' => $payment_id,
                ':project_id' => $project_id,
                ':payment_number' => $payment_number,
                ':amount' => $amount,
                ':payment_percentage' => $payment_percentage,
                ':due_date' => $due_date,
                ':status' => $status,
                ':payment_date' => $payment_date,
                ':amount_paid' => $amount_paid,
                ':remaining_amount' => $amount - $amount_paid,
                ':payment_progress' => ($amount_paid / $amount) * 100,
                ':updated_by' => $created_by
            ];
        } else {
            // เพิ่มการชำระเงินใหม่
            $payment_id = generateUUID();
            $sql = "INSERT INTO project_payments 
                    (payment_id, project_id, payment_number, amount, payment_percentage, due_date, status, payment_date, amount_paid, remaining_amount, payment_progress, created_by, created_at)
                    VALUES 
                    (:payment_id, :project_id, :payment_number, :amount, :payment_percentage, :due_date, :status, :payment_date, :amount_paid, :remaining_amount, :payment_progress, :created_by, NOW())";
            $params = [
                ':payment_id' => $payment_id,
                ':project_id' => $project_id,
                ':payment_number' => $payment_number,
                ':amount' => $amount,
                ':payment_percentage' => $payment_percentage,
                ':due_date' => $due_date,
                ':status' => $status,
                ':payment_date' => $payment_date,
                ':amount_paid' => $amount_paid,
                ':remaining_amount' => $amount - $amount_paid,
                ':payment_progress' => ($amount_paid / $amount) * 100,
                ':created_by' => $created_by
            ];
        }

        $stmt = $condb->prepare($sql);
        if (!$stmt->execute($params)) {
            throw new Exception("ไม่สามารถบันทึกข้อมูลได้");
        }

        $condb->commit();
        echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลสำเร็จ']);
    } catch (PDOException $e) {
        $condb->rollBack();
        error_log("Database Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในฐานข้อมูล: ' . $e->getMessage()]);
    } catch (Exception $e) {
        $condb->rollBack();
        error_log("General Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
