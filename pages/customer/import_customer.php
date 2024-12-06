<?php
include '../../include/Add_session.php';
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


// ตรวจสอบการ POST และการอัปโหลดไฟล์
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $file = $_FILES["file"]["tmp_name"];
    $file_type = $_FILES["file"]["type"];

    try {
        // สร้าง reader ตามประเภทไฟล์
        if ($file_type == "text/csv") {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            $reader->setInputEncoding('UTF-8');
        } elseif ($file_type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        } else {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'ไฟล์ไม่ถูกต้อง!',
                        text: 'รองรับเฉพาะไฟล์ .xlsx และ .csv เท่านั้น',
                        icon: 'warning',
                        confirmButtonText: 'ตกลง'
                    });
                });
            </script>";
            exit;
        }

        // โหลดข้อมูลจากไฟล์
        $spreadsheet = $reader->load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        // ข้าม header row
        array_shift($data);

        // เริ่ม transaction
        $condb->beginTransaction();

        $importCount = 0; // นับจำนวนรายการที่นำเข้า

        // วนลูปเพื่อนำเข้าข้อมูล
        foreach ($data as $row) {
            if (!empty($row[0])) { // ตรวจสอบว่ามีข้อมูลในแถว
                // สร้าง unique ID
                $customer_id = generateUUID();

                // เตรียม SQL statement
                $stmt = $condb->prepare("INSERT INTO customers (
                    customer_id, customer_name, position, company, 
                    phone, email, address, office_phone, extension,
                    created_by, created_at
                ) VALUES (
                    :customer_id, :customer_name, :position, :company,
                    :phone, :email, :address, :office_phone, :extension,
                    :created_by, NOW()
                )");

                // Execute statement พร้อมข้อมูล
                $stmt->execute([
                    ':customer_id' => $customer_id,
                    ':customer_name' => trim($row[0]),
                    ':position' => trim($row[1]),
                    ':company' => trim($row[2]),
                    ':phone' => trim($row[3]),
                    ':email' => trim($row[4]),
                    ':address' => trim($row[5]),
                    ':office_phone' => trim($row[6]),
                    ':extension' => trim($row[7]),
                    ':created_by' => $created_by
                ]);

                $importCount++;
            }
        }

        // ยืนยัน transaction
        $condb->commit();

        // แสดงผลสำเร็จและจำนวนรายการที่นำเข้า
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: 'นำเข้าข้อมูลสำเร็จ " . $importCount . " รายการ',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    window.location.href = 'customer.php';
                });
            });
        </script>";
    } catch (Exception $e) {
        // ถ้าเกิดข้อผิดพลาด ให้ rollback transaction
        $condb->rollBack();

        // แสดงข้อผิดพลาด
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: '" . $e->getMessage() . "',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            });
        </script>";
    }
}

// ตรวจสอบสิทธิ์การเข้าถึง
if ($role !== 'Executive' && $role !== 'Sale Supervisor' && $role !== 'Seller') {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'ข้อผิดพลาด!',
                text: 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            }).then(function() {
                window.location.href = '../index.php';
            });
        });
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "customer"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Import Customers</title>
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
                            <h1 class="m-0">Import Customers</h1>
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
                                    <h3 class="card-title">Import Customer Data</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Template Download -->
                                    <div class="mb-4">
                                        <h5>Template Download</h5>
                                        <p>ดาวน์โหลดไฟล์ template สำหรับกรอกข้อมูล:</p>
                                        <a href="templates/customer_template.xlsx" class="btn btn-info">
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
                                            <li>ลำดับคอลัมน์: Customer Name, Position, Company, Phone, Email, Address, Office Phone, Extension</li>
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
        // แสดงชื่อไฟล์ที่เลือก
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    </script>
</body>

</html>