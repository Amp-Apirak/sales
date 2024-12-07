<?php
require_once '../../include/Add_session.php';
require '../../vendor/autoload.php';


// ดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'];
$created_by = $_SESSION['user_id'];

// เพิ่มฟังก์ชัน generateUUID
function generateUUID()
{
    if (function_exists('random_bytes')) {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // Set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // Set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // Fallback for older PHP versions
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

use PhpOffice\PhpSpreadsheet\IOFactory;

// เพิ่มฟังก์ชันสำหรับตรวจสอบอีเมล
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// ตรวจสอบการอัปโหลดไฟล์
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = "กรุณาเลือกไฟล์";
    header('Location: account.php');
    exit();
}

$file = $_FILES['file'];
$fileName = $file['name'];
$fileSize = $file['size'];
$fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// ตรวจสอบนามสกุลไฟล์
$allowedTypes = ['xlsx', 'xls', 'csv'];
if (!in_array($fileType, $allowedTypes)) {
    $_SESSION['error'] = "รองรับเฉพาะไฟล์ Excel และ CSV เท่านั้น";
    header('Location: account.php');
    exit();
}

// ตรวจสอบขนาดไฟล์
if ($fileSize > 5 * 1024 * 1024) {
    $_SESSION['error'] = "ขนาดไฟล์ต้องไม่เกิน 5MB";
    header('Location: account.php');
    exit();
}

try {
    // ดึงข้อมูลทีมทั้งหมด
    $team_stmt = $condb->prepare("SELECT team_id, team_name FROM teams");
    $team_stmt->execute();
    $teams = [];
    while ($row = $team_stmt->fetch(PDO::FETCH_ASSOC)) {
        $teams[strtolower($row['team_name'])] = $row['team_id'];
    }

    $spreadsheet = IOFactory::load($file['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    // ข้าม 2 แถวแรก (คำอธิบายและหัวตาราง)
    array_shift($rows);
    array_shift($rows);

    $success = 0;
    $errors = [];
    $valid_roles = ['Executive', 'Sale Supervisor', 'Seller', 'Engineer'];

    foreach ($rows as $index => $row) {
        // ข้ามแถวว่าง
        if (empty(array_filter($row))) {
            continue;
        }

        $row_num = $index + 3; // +3 เพราะข้ามไป 2 แถว และเริ่มนับที่ 1

        // ตรวจสอบข้อมูลที่จำเป็น
        if (empty($row[0]) || empty($row[1]) || empty($row[3]) || empty($row[4]) || empty($row[9])) {
            $errors[] = "แถวที่ {$row_num}: กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน (Username, First Name, Email, Role, Password)";
            continue;
        }

        // ตรวจสอบความถูกต้องของอีเมล
        if (!isValidEmail($row[3])) {
            $errors[] = "แถวที่ {$row_num}: รูปแบบอีเมลไม่ถูกต้อง";
            continue;
        }

        // ตรวจสอบ Role
        if (!in_array($row[4], $valid_roles)) {
            $errors[] = "แถวที่ {$row_num}: Role ไม่ถูกต้อง (ต้องเป็น Executive, Sale Supervisor, Seller หรือ Engineer)";
            continue;
        }

        // ค้นหา team_id จาก team_name
        $team_id = null;
        if (!empty($row[5])) {
            $team_name_lower = strtolower(trim($row[5]));
            if (isset($teams[$team_name_lower])) {
                $team_id = $teams[$team_name_lower];
            } else {
                $errors[] = "แถวที่ {$row_num}: ไม่พบทีม '{$row[5]}' ในระบบ";
                continue;
            }
        }

        // ตรวจสอบ username ซ้ำ
        $check_stmt = $condb->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $check_stmt->execute([$row[0]]);
        if ($check_stmt->fetchColumn() > 0) {
            $errors[] = "แถวที่ {$row_num}: Username '{$row[0]}' มีอยู่ในระบบแล้ว";
            continue;
        }

        // เตรียมข้อมูลสำหรับบันทึก
        try {
            $condb->beginTransaction();

            // สร้าง unique ID
            $user_id = generateUUID();

            $stmt = $condb->prepare("
                INSERT INTO users (
                    user_id, username, first_name, last_name, email, role, team_id, 
                    position, phone, company, password, created_by, created_at
                ) VALUES (
                    :user_id, :username, :first_name, :last_name, :email, :role, :team_id,
                    :position, :phone, :company, :password, :created_by, NOW()
                )
            ");

            $result = $stmt->execute([
                ':user_id' => $user_id,
                'username' => trim($row[0]),
                'first_name' => trim($row[1]),
                'last_name' => trim($row[2]),
                'email' => trim($row[3]),
                'role' => trim($row[4]),
                'team_id' => $team_id,
                'position' => trim($row[6]),
                'phone' => trim($row[7]),
                'company' => trim($row[8]),
                'password' => password_hash(trim($row[9]), PASSWORD_DEFAULT),
                'created_by' => $created_by
            ]);

            if ($result) {
                $condb->commit();
                $success++;
            } else {
                throw new Exception("ไม่สามารถบันทึกข้อมูลได้");
            }
        } catch (Exception $e) {
            $condb->rollBack();
            $errors[] = "แถวที่ {$row_num}: {$e->getMessage()}";
        }
    }

    // สรุปผลการนำเข้าข้อมูล
    if ($success > 0) {
        $_SESSION['success'] = "นำเข้าข้อมูลสำเร็จ {$success} รายการ";
    }
    if (!empty($errors)) {
        $_SESSION['error'] = "พบข้อผิดพลาด:<br>" . implode("<br>", $errors);
    }
    if ($success == 0 && empty($errors)) {
        $_SESSION['error'] = "ไม่พบข้อมูลที่จะนำเข้า หรือข้อมูลไม่ถูกต้อง";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
}

header('Location: account.php');
exit();
