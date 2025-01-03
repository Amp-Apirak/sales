<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// ดึงข้อมูลจาก session
$role = $_SESSION['role'];
$created_by = $_SESSION['user_id'];

// ฟังก์ชัน generateUUID 
function generateUUID()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// ตรวจสอบการอัปโหลดไฟล์
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = "กรุณาเลือกไฟล์";
    header('Location: employees.php');
    exit();
}

$file = $_FILES['file'];
$allowedTypes = ['xlsx', 'xls', 'csv'];
$fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($fileType, $allowedTypes)) {
    $_SESSION['error'] = "รองรับเฉพาะไฟล์ Excel และ CSV เท่านั้น";
    header('Location: employees.php');
    exit();
}

if ($file['size'] > 5 * 1024 * 1024) {
    $_SESSION['error'] = "ขนาดไฟล์ต้องไม่เกิน 5MB";
    header('Location: employees.php');
    exit();
}

try {
    // ดึงข้อมูลทีม
    $team_stmt = $condb->prepare("SELECT team_id, team_name FROM teams");
    $team_stmt->execute();
    $teams = [];
    while ($row = $team_stmt->fetch(PDO::FETCH_ASSOC)) {
        $teams[strtolower($row['team_name'])] = $row['team_id'];
    }

    // ดึงข้อมูลหัวหน้างาน
    $supervisor_stmt = $condb->prepare("SELECT user_id, CONCAT(first_name, ' ', last_name) as full_name FROM users WHERE role IN ('Executive', 'Sale Supervisor')");
    $supervisor_stmt->execute();
    $supervisors = [];
    while ($row = $supervisor_stmt->fetch(PDO::FETCH_ASSOC)) {
        $supervisors[strtolower($row['full_name'])] = $row['user_id'];
    }

    $spreadsheet = IOFactory::load($file['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    // ข้าม 2 แถวแรก
    array_shift($rows);
    array_shift($rows);

    $success = 0;
    $errors = [];
    $valid_genders = ['ชาย', 'หญิง', 'อื่นๆ'];

    function checkDuplicateValue($condb, $field, $value, $table = 'employees')
    {
        $stmt = $condb->prepare("SELECT first_name_th, last_name_th FROM $table WHERE $field = ?");
        $stmt->execute([$value]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    foreach ($rows as $index => $row) {
        if (empty(array_filter($row))) continue;

        $row_num = $index + 3;

        // ตรวจสอบข้อมูลที่จำเป็น
        if (
            empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3]) ||
            empty($row[6]) || empty($row[8]) || empty($row[9]) || empty($row[10]) || empty($row[11])
        ) {
            $errors[] = "แถวที่ {$row_num}: กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน";
            continue;
        }

        // ตรวจสอบเพศ
        if (!in_array($row[6], $valid_genders)) {
            $errors[] = "แถวที่ {$row_num}: เพศไม่ถูกต้อง (ต้องเป็น ชาย, หญิง หรือ อื่นๆ)";
            continue;
        }

        // ตรวจสอบอีเมล
        if (!filter_var($row[8], FILTER_VALIDATE_EMAIL) || !filter_var($row[9], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "แถวที่ {$row_num}: รูปแบบอีเมลไม่ถูกต้อง";
            continue;
        }

        // ตรวจสอบ Personal Email ซ้ำ
        if ($duplicate = checkDuplicateValue($condb, 'personal_email', trim($row[8]))) {
            $errors[] = "แถวที่ {$row_num}: Personal Email '{$row[8]}' ซ้ำกับพนักงาน {$duplicate['first_name_th']} {$duplicate['last_name_th']}";
            continue;
        }

        // ตรวจสอบ Company Email ซ้ำ  
        if ($duplicate = checkDuplicateValue($condb, 'company_email', trim($row[9]))) {
            $errors[] = "แถวที่ {$row_num}: Company Email '{$row[9]}' ซ้ำกับพนักงาน {$duplicate['first_name_th']} {$duplicate['last_name_th']}";
            continue;
        }

        // ตรวจสอบเบอร์โทรซ้ำ
        if ($duplicate = checkDuplicateValue($condb, 'phone', trim($row[10]))) {
            $errors[] = "แถวที่ {$row_num}: เบอร์โทรศัพท์ '{$row[10]}' ซ้ำกับพนักงาน {$duplicate['first_name_th']} {$duplicate['last_name_th']}";
            continue;
        }

        // หา team_id
        $team_id = null;
        if (!empty($row[13])) {
            $team_name_lower = strtolower(trim($row[13]));
            if (isset($teams[$team_name_lower])) {
                $team_id = $teams[$team_name_lower];
            } else {
                $errors[] = "แถวที่ {$row_num}: ไม่พบทีม '{$row[13]}'";
                continue;
            }
        }

        // หา supervisor_id
        $supervisor_id = null;
        if (!empty($row[14])) {
            $supervisor_name_lower = strtolower(trim($row[14]));
            if (isset($supervisors[$supervisor_name_lower])) {
                $supervisor_id = $supervisors[$supervisor_name_lower];
            } else {
                $errors[] = "แถวที่ {$row_num}: ไม่พบหัวหน้า '{$row[14]}'";
                continue;
            }
        }

        try {
            $condb->beginTransaction();

            $id = generateUUID();

            $stmt = $condb->prepare("
               INSERT INTO employees (
                   id, first_name_th, last_name_th, first_name_en, last_name_en,
                   nickname_th, nickname_en, gender, birth_date, personal_email,
                   company_email, phone, position, department, team_id,
                   supervisor_id, address, hire_date, created_by, created_at
               ) VALUES (
                   :id, :first_name_th, :last_name_th, :first_name_en, :last_name_en,
                   :nickname_th, :nickname_en, :gender, :birth_date, :personal_email,
                   :company_email, :phone, :position, :department, :team_id,
                   :supervisor_id, :address, :hire_date, :created_by, NOW()
               )
           ");

            $result = $stmt->execute([
                ':id' => $id,
                ':first_name_th' => trim($row[0]),
                ':last_name_th' => trim($row[1]),
                ':first_name_en' => trim($row[2]),
                ':last_name_en' => trim($row[3]),
                ':nickname_th' => trim($row[4]),
                ':nickname_en' => trim($row[5]),
                ':gender' => trim($row[6]),
                ':birth_date' => !empty($row[7]) ? trim($row[7]) : null,
                ':personal_email' => trim($row[8]),
                ':company_email' => trim($row[9]),
                ':phone' => trim($row[10]),
                ':position' => trim($row[11]),
                ':department' => trim($row[12]),
                ':team_id' => $team_id,
                ':supervisor_id' => $supervisor_id,
                ':address' => trim($row[15]),
                ':hire_date' => !empty($row[16]) ? trim($row[16]) : null,
                ':created_by' => $created_by
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

header('Location: employees.php');
exit();
