<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';
header('Content-Type: application/json');

// ตั้งค่าการรายงานข้อผิดพลาด
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตรวจสอบการตั้งค่า Session เพื่อป้องกันกรณีที่ไม่ได้ล็อกอิน
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่ได้รับอนุญาตให้เข้าถึง'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'] ?? '';
$team_id = $_SESSION['team_id'] ?? 0;
$created_by = $_SESSION['user_id'] ?? 0;

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

// จำกัดการเข้าถึงเฉพาะผู้ใช้ที่มีสิทธิ์เท่านั้น
if (!in_array($role, ['Executive', 'Sale Supervisor', 'Seller'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ตรวจสอบ CSRF Token
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'CSRF token ไม่ถูกต้อง'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data)
{
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // รับและทำความสะอาดข้อมูลที่ส่งมา
        $payment_id = isset($_POST['payment_id']) ? clean_input($_POST['payment_id']) : null;
        $project_id = clean_input($_POST['project_id']);
        $payment_number = intval($_POST['payment_number']);
        $amount = floatval($_POST['amount']);
        $payment_percentage = floatval($_POST['payment_percentage']);
        $due_date = clean_input($_POST['due_date']);
        $status = clean_input($_POST['status']);
        $payment_date = !empty($_POST['payment_date']) ? clean_input($_POST['payment_date']) : null;
        $amount_paid = floatval($_POST['amount_paid']);

        // ตรวจสอบข้อมูลที่จำเป็น
        if (empty($project_id) || $payment_number <= 0 || $amount <= 0) {
            throw new Exception("กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง");
        }

        // ตั้งค่า PDO เพื่อป้องกัน SQL Injection
        $condb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $condb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        // ดึงข้อมูลโครงการ
        $stmt = $condb->prepare("SELECT sale_no_vat FROM projects WHERE project_id = :project_id");
        $stmt->bindParam(':project_id', $project_id, PDO::PARAM_STR);
        $stmt->execute();
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            throw new Exception("ไม่พบข้อมูลโครงการ");
        }

        $total_project_amount = $project['sale_no_vat'];

        // ตรวจสอบเปอร์เซ็นต์รวม
        $stmt = $condb->prepare("SELECT SUM(payment_percentage) as total_percentage FROM project_payments WHERE project_id = :project_id AND payment_id != :payment_id");
        $stmt->bindParam(':project_id', $project_id, PDO::PARAM_STR);
        $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_percentage = ($result['total_percentage'] ?? 0) + $payment_percentage;

        if ($total_percentage > 100) {
            throw new Exception("เปอร์เซ็นต์รวมของการชำระเงินเกิน 100% ของราคาขาย");
        }

        $condb->beginTransaction();

        // คำนวณค่าต่างๆ ก่อน
        $remaining_amount = $amount - $amount_paid;
        $payment_progress = ($amount_paid / $amount) * 100;

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
        echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลสำเร็จ'], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        $condb->rollBack();
        error_log("Database Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในฐานข้อมูล: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $condb->rollBack();
        error_log("General Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'วิธีการร้องขอไม่ถูกต้อง'], JSON_UNESCAPED_UNICODE);
}
