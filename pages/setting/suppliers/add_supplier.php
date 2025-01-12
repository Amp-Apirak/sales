<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// สร้างหรือดึง CSRF Token สำหรับป้องกันการโจมตี CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data)
{
    // ทำความสะอาดข้อมูลแต่ยังคงเก็บอักขระพิเศษไว้
    $data = trim($data);
    // ป้องกัน SQL Injection โดยใช้ PDO parameters แทน
    return $data;
}

// ฟังก์ชันสำหรับสร้าง UUID แบบปลอดภัย
function generateUUID()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // เวอร์ชัน 4 UUID
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // UUID variant
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// ฟังก์ชันตรวจสอบความถูกต้องของเบอร์โทรศัพท์
function isPhoneValid($phone)
{
    // ตรวจสอบว่าเบอร์โทรศัพท์มีเฉพาะตัวเลขและมีความยาว 10 หลัก
    return preg_match('/^[0-9]{10}$/', $phone);
}

$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$created_by = $_SESSION['user_id']; // ดึง user_id ของผู้สร้างจาก session

// ตัวแปรเก็บข้อความแจ้งเตือนสำหรับการตรวจสอบข้อมูล
$error_messages = [];

// ตรวจสอบว่าผู้ใช้กดปุ่ม "เพิ่ม Supplier" หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // ตรวจสอบ CSRF Token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die(json_encode(['success' => false, 'errors' => ['Invalid CSRF token']]));
    }

    // สร้าง UUID สำหรับ supplier_id
    $supplier_id = generateUUID();

    // รับข้อมูลจากฟอร์มและล้างข้อมูลด้วย htmlspecialchars เพื่อป้องกัน XSS
    $supplier_name = clean_input($_POST['supplier_name']);
    $company = clean_input($_POST['company']);
    $address = clean_input($_POST['address']);
    $phone = clean_input($_POST['phone']);
    $email = clean_input($_POST['email']);
    $remark = clean_input($_POST['remark']);
    $position = clean_input($_POST['position']);

    // รับข้อมูลเพิ่มเติมจากฟอร์ม
    $office_phone = clean_input($_POST['office_phone']);
    $extension = clean_input($_POST['extension']);

    // ตรวจสอบและอัปโหลดรูปภาพ
    $suppliers_image = '';
    if (isset($_FILES['suppliers_image']) && $_FILES['suppliers_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['suppliers_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = '../../../uploads/supplier_images/' . $new_filename;
            if (move_uploaded_file($_FILES['suppliers_image']['tmp_name'], $upload_path)) {
                $suppliers_image = $new_filename;
            } else {
                $error_messages[] = "ไม่สามารถอัปโหลดรูปภาพได้";
            }
        } else {
            $error_messages[] = "ไฟล์รูปภาพไม่ถูกต้อง กรุณาอัปโหลดไฟล์ jpg, jpeg, png หรือ gif";
        }
    }

    // ตรวจสอบความถูกต้องของรูปแบบอีเมล
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_messages[] = "รูปแบบอีเมลไม่ถูกต้อง";
    }

    // ตรวจสอบความถูกต้องของเบอร์โทรศัพท์
    if (!empty($phone) && !isPhoneValid($phone)) {
        $error_messages[] = "เบอร์โทรศัพท์ไม่ถูกต้อง กรุณากรอกเฉพาะตัวเลข 10 หลัก";
    }

    // ถ้าไม่มีข้อผิดพลาด ดำเนินการบันทึกข้อมูล Supplier ใหม่
    if (empty($error_messages)) {
        try {
            // บันทึกข้อมูลลงในฐานข้อมูล
            $sql = "INSERT INTO suppliers (supplier_id, supplier_name, company, position, address, phone, email, remark, created_by, suppliers_image, office_phone, extension)
                    VALUES (:supplier_id, :supplier_name, :company, :position, :address, :phone, :email, :remark, :created_by, :suppliers_image, :office_phone, :extension)";
            $stmt = $condb->prepare($sql);
            $stmt->execute([
                ':supplier_id' => $supplier_id,
                ':supplier_name' => $supplier_name,
                ':company' => $company,
                ':position' => $position,
                ':address' => $address,
                ':phone' => $phone,
                ':email' => $email,
                ':remark' => $remark,
                ':created_by' => $created_by,
                ':suppliers_image' => $suppliers_image,
                ':office_phone' => $office_phone,
                ':extension' => $extension
            ]);

            // ล้างค่า CSRF token เมื่อบันทึกข้อมูลสำเร็จ
            unset($_SESSION['csrf_token']);

            echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูล Supplier เรียบร้อยแล้ว']);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'errors' => [$e->getMessage()]]);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'errors' => $error_messages]);
        exit;
    }
}

// เพิ่ม SQL query เพื่อดึงรายการ Company จากฐานข้อมูล
$sql = "SELECT DISTINCT s.company, s.address, s.office_phone, s.extension 
        FROM suppliers s
        LEFT JOIN users u ON s.created_by = u.user_id 
        WHERE s.company IS NOT NULL";

// เพิ่มเงื่อนไขตาม Role
if ($role == 'Sale Supervisor') {
    // Sale Supervisor เห็นเฉพาะ Supplier ในทีมของตัวเอง
    $sql .= " AND u.team_id = :team_id";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':team_id', $team_id);
} elseif ($role == 'Seller') {
    // Seller เห็นเฉพาะ Supplier ที่ตัวเองสร้าง
    $sql .= " AND s.created_by = :user_id";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
} elseif ($role == 'Executive') {
    // Executive เห็นทั้งหมด
    $stmt = $condb->prepare($sql);
} else {
    // Role อื่นๆ เห็นเฉพาะที่ตัวเองสร้าง
    $sql .= " AND s.created_by = :user_id";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
}

$stmt->execute();
$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "supplier"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Add Supplier</title>
    <?php include '../../../include/header.php'; ?>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <?php include  '../../../include/navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Add Supplier</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Add Supplier</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- ฟอร์มเพิ่มข้อมูล Supplier -->
                            <div class="card card-primary h-100">
                                <div class="card-header">
                                    <h3 class="card-title">Supplier Information</h3>
                                </div>
                                <div class="card-body">
                                    <form id="addSupplierForm" method="POST" enctype="multipart/form-data">
                                        <!-- CSRF Token -->
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                        <!-- เพิ่มฟิลด์ใหม่: Supplier Image -->
                                        <div class="form-group">
                                            <label for="suppliers_image">Supplier Logo</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="suppliers_image" name="suppliers_image">
                                                    <label class="custom-file-label" for="suppliers_image">Choose file</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Company -->
                                        <div class="form-group">
                                            <label for="company">Company<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                </div>
                                                <input type="text"
                                                    name="company"
                                                    class="form-control"
                                                    id="company"
                                                    placeholder="Company"
                                                    list="companyList"
                                                    required>
                                                <datalist id="companyList">
                                                    <?php foreach ($companies as $company): ?>
                                                        <option value="<?php echo htmlspecialchars($company['company']); ?>"
                                                            data-address="<?php echo htmlspecialchars($company['address']); ?>">
                                                        <?php endforeach; ?>
                                                </datalist>
                                            </div>
                                        </div>

                                        <!-- Address -->
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                </div>
                                                <input type="text" name="address" class="form-control" id="address" placeholder="Address">
                                            </div>
                                        </div>

                                        <!-- Phone -->
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="office_phone">Office Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa fa-phone"></i></span>
                                                    </div>
                                                    <input type="text" name="office_phone" class="form-control" id="office_phone" placeholder="Office Phone">
                                                </div>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="office_phone">Extension</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-phone-square"></i></span>
                                                    </div>
                                                    <input type="text" name="extension" class="form-control" id="extension" placeholder="Extension">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Supplier Name -->
                                        <div class="form-group">
                                            <label for="supplier_name">Supplier Name<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-address-book"></i></span>
                                                </div>
                                                <input type="text" name="supplier_name" class="form-control" id="supplier_name" placeholder="Supplier Name" required>
                                            </div>
                                        </div>

                                        <!-- Position -->
                                        <div class="form-group">
                                            <label for="position">Position</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                </div>
                                                <input type="text" name="position" class="form-control" id="position" placeholder="Position">
                                            </div>
                                        </div>

                                        <!-- Phone -->
                                        <div class="form-group">
                                            <label for="phone">Phone</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                </div>
                                                <input type="text" name="phone" class="form-control" id="phone" placeholder="Phone">
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                </div>
                                                <input type="email" name="email" class="form-control" id="email" placeholder="Email">
                                            </div>
                                        </div>

                                        <!-- Remark -->
                                        <div class="form-group">
                                            <label for="remark">Remark</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                                </div>
                                                <textarea name="remark" class="form-control" id="remark" style="height: 150px;" placeholder="ระบุรายละเอียดสินค้า และบริการที่เกี่ยวข้องกับผู้ขาย Supplier"></textarea>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-sm btn-success w-25">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Footer -->
        <?php include '../../../include/footer.php'; ?>
    </div>

    <script>
        $(function() {
            // จัดการการส่งฟอร์ม
            $('#addSupplierForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                Swal.fire({
                    title: 'กำลังบันทึกข้อมูล...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    },
                });

                $.ajax({
                    type: 'POST',
                    url: 'add_supplier.php',
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'บันทึกสำเร็จ',
                                text: response.message,
                                confirmButtonText: 'ตกลง'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'supplier.php';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                html: response.errors.join('<br>'),
                                confirmButtonText: 'ตกลง'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ กรุณาลองใหม่อีกครั้ง',
                            confirmButtonText: 'ตกลง'
                        });
                    }
                });
            });

            // แสดงชื่อไฟล์ที่เลือกในช่อง input file
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
</body>

</html>

<!-- เพิ่ม JavaScript เพื่อจัดการการเลือก Company และดึงที่อยู่ -->
<script>
    $(function() {
        // สร้าง object เก็บความสัมพันธ์ระหว่าง company และข้อมูลอื่นๆ
        const companyData = {};
        <?php foreach ($companies as $company): ?>
            companyData['<?php echo addslashes($company['company']); ?>'] = {
                address: '<?php echo addslashes($company['address']); ?>',
                office_phone: '<?php echo addslashes($company['office_phone'] ?? ''); ?>',
                extension: '<?php echo addslashes($company['extension'] ?? ''); ?>'
            };
        <?php endforeach; ?>

        // เมื่อเลือกหรือกรอก company
        $('#company').on('input', function() {
            const selectedCompany = $(this).val();
            const data = companyData[selectedCompany];

            if (data) {
                // กำหนดค่าให้กับฟิลด์ต่างๆ
                $('#address').val(data.address);
                $('#office_phone').val(data.office_phone);
                $('#extension').val(data.extension);
            } else {
                // กรณีไม่พบข้อมูล ให้เคลียร์ค่าในฟิลด์
                $('#address').val('');
                $('#office_phone').val('');
                $('#extension').val('');
            }
        });

        // ทำให้สามารถเลือกจาก datalist ได้ใน mobile
        $('#company').on('change', function() {
            const selectedCompany = $(this).val();
            const data = companyData[selectedCompany];
            if (data) {
                $('#address').val(data.address);
                $('#office_phone').val(data.office_phone);
                $('#extension').val(data.extension);
            }
        });
    });
</script>