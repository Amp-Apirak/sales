<?php
// เรียกใช้ session และการตั้งค่าพื้นฐาน
include '../../include/Add_session.php';

// โหลด library สำหรับจัดการ Excel/CSV ด้วย PhpSpreadsheet
require '../../vendor/autoload.php';

// เปิดการแสดงข้อผิดพลาดสำหรับ Debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ตรวจสอบสิทธิ์ผู้ใช้งาน (ต้องเป็น Executive หรือ Sale Supervisor เท่านั้น)
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Executive' && $_SESSION['role'] != 'Sale Supervisor')) {
    header("Location: " . BASE_URL . "index.php");
    exit(); // หยุดการทำงานหากไม่มีสิทธิ์
}

// กำหนดตัวแปรพื้นฐาน
$role = $_SESSION['role'];
$created_by = $_SESSION['user_id'];

// ฟังก์ชันสำหรับสร้าง UUID (ใช้ในฐานข้อมูล)
function generateUUID()
{
    if (function_exists('random_bytes')) {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // ตั้งค่า version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // ตั้งค่า variant
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    // สำรองสำหรับ PHP เวอร์ชันเก่า
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

// ตรวจสอบว่ามีการส่งไฟล์มาหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $file = $_FILES["file"]["tmp_name"];
    $file_type = $_FILES["file"]["type"];

    try {
        // ตรวจสอบประเภทไฟล์และสร้าง Reader
        if ($file_type == "text/csv") {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            $reader->setInputEncoding('UTF-8'); // ตั้งค่าการอ่านไฟล์ CSV
        } elseif ($file_type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        } else {
            throw new Exception('รองรับเฉพาะไฟล์ .xlsx และ .csv เท่านั้น');
        }

        // โหลดข้อมูลจากไฟล์และแปลงเป็นอาร์เรย์
        $spreadsheet = $reader->load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        // ลบ Header row (แถวแรก)
        array_shift($data);

        // ตรวจสอบว่ามีข้อมูลในไฟล์หรือไม่
        if (empty($data)) {
            throw new Exception('ไฟล์ที่อัปโหลดไม่มีข้อมูลที่สามารถนำเข้าได้');
        }

        // ตรวจสอบการเชื่อมต่อฐานข้อมูล
        if (!$condb) {
            throw new Exception('ไม่สามารถเชื่อมต่อฐานข้อมูลได้');
        }

        // เริ่มต้น Transaction
        $condb->beginTransaction();

        // ดึงข้อมูลทีมจากฐานข้อมูล
        $teamStmt = $condb->query("SELECT team_id, team_name FROM teams");
        $teams = [];
        while ($row = $teamStmt->fetch()) {
            $teams[$row['team_name']] = $row['team_id'];
        }

        // ดึงข้อมูล Username และ Email ที่มีอยู่ในระบบ
        $existingStmt = $condb->query("SELECT username, email FROM users");
        $existing = $existingStmt->fetchAll(PDO::FETCH_ASSOC);
        $existingUsernames = array_column($existing, 'username');
        $existingEmails = array_column($existing, 'email');

        // ตัวนับจำนวนที่นำเข้าสำเร็จ
        $importCount = 0;
        $errorRows = []; // เก็บข้อผิดพลาด

        // เริ่มนำเข้าข้อมูล
        foreach ($data as $index => $row) {
            $rowNumber = $index + 2; // เลขแถวในไฟล์ (เริ่มที่แถว 2)

            // ตรวจสอบความถูกต้องของข้อมูล
            if (empty($row[0]) || in_array(trim($row[0]), $existingUsernames)) {
                $errorRows[] = "แถว {$rowNumber}: Username '{$row[0]}' ซ้ำในระบบ";
                continue;
            }
            if (empty($row[3]) || in_array(trim($row[3]), $existingEmails)) {
                $errorRows[] = "แถว {$rowNumber}: Email '{$row[3]}' ซ้ำในระบบ";
                continue;
            }
            if (!isset($teams[trim($row[5])])) {
                $errorRows[] = "แถว {$rowNumber}: Team '{$row[5]}' ไม่พบในระบบ";
                continue;
            }

            // เตรียมข้อมูลสำหรับ Insert
            $stmt = $condb->prepare("INSERT INTO users (
                user_id, username, first_name, last_name,
                email, role, team_id, position, phone,
                password, company, created_by, created_at
            ) VALUES (
                :user_id, :username, :first_name, :last_name,
                :email, :role, :team_id, :position, :phone,
                :password, :company, :created_by, NOW()
            )");

            // กำหนดค่าพารามิเตอร์
            $user_id = generateUUID();
            $stmt->execute([
                ':user_id' => $user_id,
                ':username' => trim($row[0]),
                ':first_name' => trim($row[1]),
                ':last_name' => trim($row[2]),
                ':email' => trim($row[3]),
                ':role' => trim($row[4]),
                ':team_id' => $teams[trim($row[5])],
                ':position' => trim($row[6]),
                ':phone' => trim($row[7]),
                ':password' => password_hash('123456', PASSWORD_DEFAULT),
                ':company' => trim($row[8]),
                ':created_by' => $created_by
            ]);
            $importCount++;
        }

        // ตรวจสอบข้อผิดพลาด
        if (!empty($errorRows)) {
            throw new Exception("<ul><li>" . implode("</li><li>", $errorRows) . "</li></ul>");
        }

        // Commit เมื่อสำเร็จ
        $condb->commit();

        // แสดงแจ้งเตือนสำเร็จ
        echo "<script>
            Swal.fire({
                title: 'สำเร็จ!',
                text: 'นำเข้าข้อมูลสำเร็จ {$importCount} รายการ',
                icon: 'success',
                timer: 2000
            }).then(() => window.location.href = 'account.php');
        </script>";
    } catch (Exception $e) {
        // Rollback หากเกิดข้อผิดพลาด
        $condb->rollBack();
        error_log("Error: " . $e->getMessage());
        echo "<script>
            Swal.fire({
                title: 'ข้อผิดพลาด!',
                html: '" . $e->getMessage() . "',
                icon: 'error'
            });
        </script>";
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<?php $menu = "account"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Import Users</title>
    <?php include '../../include/header.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Import Users</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Import User Data</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Template Download -->
                                    <div class="mb-4">
                                        <h5>Template Download</h5>
                                        <p>ดาวน์โหลดไฟล์ template สำหรับกรอกข้อมูล:</p>
                                        <a href="templates/user_template.xlsx" class="btn btn-info">
                                            <i class="fas fa-download"></i> Download Template
                                        </a>
                                    </div>

                                    <!-- Import Form -->
                                    <form action="" method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label>เลือกไฟล์ Excel/CSV</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="file" name="file" accept=".xlsx,.csv" required>
                                                <label class="custom-file-label" for="file">Choose file</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-upload"></i> Import
                                        </button>
                                    </form>

                                    <!-- Instructions -->
                                    <div class="mt-4">
                                        <h5>คำแนะนำการนำเข้าข้อมูล</h5>
                                        <ul>
                                            <li>รองรับไฟล์นามสกุล .xlsx และ .csv</li>
                                            <li>ข้อมูลต้องอยู่ในรูปแบบตาม template ที่กำหนด</li>
                                            <li>ลำดับคอลัมน์: Username, First Name, Last Name, Email, Role, Team Name, Position, Phone, Company</li>
                                            <li>Role ต้องเป็น: Executive, Sale Supervisor, Seller หรือ Engineer</li>
                                            <li>Team Name ต้องตรงกับชื่อทีมในระบบ เช่น Innovation_PIT, Enterprise_PIT</li>
                                            <li>รหัสผ่านเริ่มต้นจะถูกตั้งเป็น: 123456</li>
                                            <li>Row แรกเป็น header จะถูกข้ามไป</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include '../../include/footer.php'; ?>
    </div>

    <script>
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    </script>
</body>

</html>


<!DOCTYPE html>
<html lang="en">
<?php $menu = "account"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Import Users</title>
    <?php include '../../include/header.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Import Users</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Import User Data</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Template Download -->
                                    <div class="mb-4">
                                        <h5>Template Download</h5>
                                        <p>ดาวน์โหลดไฟล์ template สำหรับกรอกข้อมูล:</p>
                                        <a href="templates/user_template.xlsx" class="btn btn-info">
                                            <i class="fas fa-download"></i> Download Template
                                        </a>
                                    </div>

                                    <!-- Import Form -->
                                    <form action="" method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label>เลือกไฟล์ Excel/CSV</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="file" name="file" accept=".xlsx,.csv" required>
                                                <label class="custom-file-label" for="file">Choose file</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-upload"></i> Import
                                        </button>
                                    </form>

                                    <!-- Instructions -->
                                    <div class="mt-4">
                                        <h5>คำแนะนำการนำเข้าข้อมูล</h5>
                                        <ul>
                                            <li>รองรับไฟล์นามสกุล .xlsx และ .csv</li>
                                            <li>ข้อมูลต้องอยู่ในรูปแบบตาม template ที่กำหนด</li>
                                            <li>ลำดับคอลัมน์: Username, First Name, Last Name, Email, Role, Team Name, Position, Phone, Company</li>
                                            <li>Role ต้องเป็น: Executive, Sale Supervisor, Seller หรือ Engineer</li>
                                            <li>Team Name ต้องตรงกับชื่อทีมในระบบ เช่น Innovation_PIT, Enterprise_PIT</li>
                                            <li>รหัสผ่านเริ่มต้นจะถูกตั้งเป็น: 123456</li>
                                            <li>Row แรกเป็น header จะถูกข้ามไป</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include '../../include/footer.php'; ?>
    </div>

    <script>
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    </script>
</body>

</html>