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
    <style>
        .import-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .template-card {
            border-left: 4px solid #17a2b8;
            transition: transform 0.2s;
        }

        .template-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .upload-card {
            border-left: 4px solid #007bff;
        }

        .instructions-card {
            border-left: 4px solid #28a745;
        }

        .custom-file-label::after {
            content: "Browse";
            background: #007bff;
            color: white;
        }

        .step-number {
            width: 30px;
            height: 30px;
            background: #007bff;
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }

        .instruction-item {
            display: flex;
            align-items: start;
            margin-bottom: 15px;
        }

        .page-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 0.5rem;
            color: white;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="page-header">
                        <h1><i class="fas fa-file-import mr-2"></i>Import Customers</h1>
                        <p class="mb-0">นำเข้าข้อมูลลูกค้าจากไฟล์ Excel หรือ CSV</p>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="import-container">
                        <div class="row">
                            <!-- Template Download Card -->
                            <div class="col-12 mb-4">
                                <div class="card template-card">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="mr-4">
                                            <i class="fas fa-file-excel text-info" style="font-size: 3rem;"></i>
                                        </div>
                                        <div>
                                            <h5 class="card-title">ดาวน์โหลด Template</h5>
                                            <p class="card-text mb-3">ดาวน์โหลดไฟล์ template สำหรับกรอกข้อมูลลูกค้า</p>
                                            <a href="templates/customer_template.xlsx" class="btn btn-info">
                                                <i class="fas fa-download mr-2"></i>Download Template
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Form Card -->
                            <div class="col-12 mb-4">
                                <div class="card upload-card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-upload mr-2"></i>อัพโหลดไฟล์
                                        </h5>
                                        <form action="" method="POST" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <div class="custom-file mb-3">
                                                    <input type="file" class="custom-file-input" id="file" name="file" accept=".xlsx,.csv" required>
                                                    <label class="custom-file-label" for="file">เลือกไฟล์...</label>
                                                </div>
                                                <small class="form-text text-muted">รองรับไฟล์ .xlsx และ .csv เท่านั้น</small>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                                <i class="fas fa-file-import mr-2"></i>นำเข้าข้อมูล
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Instructions Card -->
                            <div class="col-12">
                                <div class="card instructions-card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-info-circle mr-2"></i>คำแนะนำการนำเข้าข้อมูล
                                        </h5><br><br>
                                        <div class="instruction-item">
                                            <span class="step-number">1</span>
                                            <div>ดาวน์โหลดไฟล์ template จากปุ่ม "Download Template" ด้านบน</div>
                                        </div>
                                        <div class="instruction-item">
                                            <span class="step-number">2</span>
                                            <div>กรอกข้อมูลลูกค้าตามลำดับคอลัมน์: Customer Name, Position, Company, Phone, Email, Address, Office Phone, Extension</div>
                                        </div>
                                        <div class="instruction-item">
                                            <span class="step-number">3</span>
                                            <div>บันทึกไฟล์ในรูปแบบ .xlsx หรือ .csv</div>
                                        </div>
                                        <div class="instruction-item">
                                            <span class="step-number">4</span>
                                            <div>อัพโหลดไฟล์และกดปุ่ม "นำเข้าข้อมูล"</div>
                                        </div>

                                        <div class="alert alert-info mt-4">
                                            <i class="fas fa-lightbulb mr-2"></i>
                                            <strong>คำแนะนำ:</strong> ตรวจสอบข้อมูลให้ครบถ้วนก่อนทำการนำเข้า และอย่าลบ header row ในไฟล์ template
                                        </div>
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
        // Show selected filename
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Add loading state to submit button
        $('form').on('submit', function() {
            $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin mr-2"></i>กำลังนำเข้าข้อมูล...').attr('disabled', true);
        });
    </script>
</body>

</html>