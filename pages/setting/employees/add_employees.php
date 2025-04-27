<?php
// เริ่มต้น session

use PhpOffice\PhpSpreadsheet\Style\Supervisor;

session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// ตรวจสอบสิทธิ์การเข้าถึง
$role = $_SESSION['role'];  // บทบาทของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // team_id ของผู้ใช้จาก session
$created_by = $_SESSION['user_id'];  // user_id ของผู้สร้างจาก session

// จำกัดการเข้าถึงเฉพาะผู้ใช้ที่มีสิทธิ์เท่านั้น
if (!in_array($role, ['Executive', 'Sale Supervisor', 'Seller', 'Engineer'])) {
    header("Location: index.php");
    exit();
}

// สร้างหรือดึง CSRF Token เพื่อป้องกันการโจมตีแบบ CSRF
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

// ดึงข้อมูลทีมทั้งหมด
try {
    $sql_teams = "SELECT team_id, team_name FROM teams ORDER BY team_name";
    $stmt_teams = $condb->prepare($sql_teams);
    $stmt_teams->execute();
    $query_teams = $stmt_teams->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching teams: " . $e->getMessage();
}

// ดึงข้อมูลหัวหน้างาน
try {
    $sql_supervisors = "SELECT user_id, first_name, last_name 
                       FROM users 
                       WHERE role IN ('Executive', 'Sale Supervisor') 
                       ORDER BY first_name, last_name";
    $stmt_supervisors = $condb->prepare($sql_supervisors);
    $stmt_supervisors->execute();
    $supervisors = $stmt_supervisors->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching supervisors: " . $e->getMessage();
}

// ฟังก์ชันตรวจสอบความถูกต้องของเบอร์โทรศัพท์
function isPhoneValid($phone)
{
    // ตรวจสอบว่าเบอร์โทรศัพท์มีเฉพาะตัวเลขและมีความยาว 10 หลัก
    return preg_match('/^[0-9]{10}$/', $phone);
}

// ฟังก์ชันตรวจสอบข้อมูลซ้ำ
function checkDuplicateData($condb, $field, $value, $table = 'employees')
{
    $sql = "SELECT COUNT(*) as count FROM $table WHERE $field = :value";
    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':value', $value, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}


// ตัวแปรเก็บข้อความแจ้งเตือนสำหรับการตรวจสอบข้อมูล
$error_messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    try {
        // สร้าง UUID สำหรับพนักงานใหม่
        $id = generateUUID();

        // ตรวจสอบและทำความสะอาดข้อมูลที่รับมา
        $employee_data = [
            ':id' => $id,
            ':first_name_th' => clean_input($_POST['first_name_th']),
            ':last_name_th' => clean_input($_POST['last_name_th']),
            ':first_name_en' => clean_input($_POST['first_name_en']),
            ':last_name_en' => clean_input($_POST['last_name_en']),
            ':nickname_th' => clean_input($_POST['nickname_th']),
            ':nickname_en' => clean_input($_POST['nickname_en']),
            ':gender' => clean_input($_POST['gender']),
            ':birth_date' => !empty($_POST['birth_date']) ? $_POST['birth_date'] : null,
            ':personal_email' => clean_input($_POST['personal_email']),
            ':company_email' => !empty($_POST['company_email']) ? clean_input($_POST['company_email']) : null,
            ':phone' => clean_input($_POST['phone']),
            ':position' => clean_input($_POST['position']),
            ':department' => clean_input($_POST['department']),
            ':team_id' => !empty($_POST['team_id']) ? $_POST['team_id'] : null,
            ':supervisor_id' => !empty($_POST['supervisor_id']) ? $_POST['supervisor_id'] : null,
            ':address' => clean_input($_POST['address']),
            ':hire_date' => !empty($_POST['hire_date']) ? $_POST['hire_date'] : null,
            ':created_by' => $_SESSION['user_id']
        ];

        // จัดการอัพโหลดรูปภาพ
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $profile_image = handleImageUpload($_FILES['profile_image']);
            $employee_data[':profile_image'] = $profile_image;
        } else {
            $employee_data[':profile_image'] = null;
        }

        // เตรียม SQL query สำหรับการเพิ่มข้อมูล
        $sql = "INSERT INTO employees (
                    id, first_name_th, last_name_th, first_name_en, last_name_en,
                    nickname_th, nickname_en, gender, birth_date, personal_email,
                    company_email, phone, position, department, team_id,
                    supervisor_id, address, hire_date, profile_image, created_by
                ) VALUES (
                    :id, :first_name_th, :last_name_th, :first_name_en, :last_name_en,
                    :nickname_th, :nickname_en, :gender, :birth_date, :personal_email,
                    :company_email, :phone, :position, :department, :team_id,
                    :supervisor_id, :address, :hire_date, :profile_image, :created_by
                )";

        $stmt = $condb->prepare($sql);
        $stmt->execute($employee_data);

        echo json_encode([
            'status' => 'success',
            'message' => 'เพิ่มข้อมูลพนักงานเรียบร้อยแล้ว'
        ]);
        exit();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ]);
        exit();
    }
}

// ดึงรายชื่อหัวหน้างานที่มีสิทธิ์เป็น supervisor
$sql_supervisors = "SELECT user_id, first_name, last_name FROM users ";
$stmt_supervisors = $condb->prepare($sql_supervisors);
$stmt_supervisors->execute();
$supervisors = $stmt_supervisors->fetchAll();

function handleImageUpload($file)
{
    $target_dir = "../../../uploads/employee_images/";
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    // ตรวจสอบประเภทไฟล์
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_types)) {
        throw new Exception("อนุญาตเฉพาะไฟล์รูปภาพนามสกุล .jpg, .jpeg, .png, .gif เท่านั้น");
    }

    // ตรวจสอบขนาดไฟล์ (5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("ขนาดไฟล์ต้องไม่เกิน 5MB");
    }

    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $new_filename;
    } else {
        throw new Exception("เกิดข้อผิดพลาดในการอัพโหลดไฟล์");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Create Account</title>
    <?php include '../../../include/header.php'; ?>
    <!-- เพิ่ม CSS สำหรับ Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    <!-- เพิ่ม SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <!-- เพิ่มลิงก์ฟอนต์ Noto Sans Thai ในส่วน <head> ของเอกสาร HTML -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">

    <style>
        .card {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .form-control,
        .input-group>.form-control {
            border-radius: 5px;
        }

        .btn-sm {
            border-radius: 5px;
        }

        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(1.8125rem + 2px) !important;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
            line-height: calc(1.8125rem + 2px) !important;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            top: 50% !important;
            transform: translateY(-50%) !important;
        }

        .select2-container .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: calc(2.25rem + 2px) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(2.25rem + 2px) !important;
        }
    </style>
    <!-- เพิ่ม CSS ต่อไปนี้ในแท็ก <style> หรือไฟล์ CSS ของคุณ -->
    <style>
        /* ใช้ฟอนต์ Noto Sans Thai กับทั้งหน้าเว็บ */
        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        label,
        input,
        select,
        textarea,
        button {
            font-family: 'Noto Sans Thai', sans-serif;
        }

        /* ปรับแต่งสไตล์เฉพาะสำหรับหัวข้อและฟิลด์ข้อมูล */
        h1,
        h2,
        h3,
        .card-title {
            font-weight: 700;
            color: #333;
        }

        label {
            font-weight: 500;
            color: #555;
        }

        .form-control {
            font-weight: 400;
        }

        /* สไตล์สำหรับ custom-th ที่คุณกำหนดไว้ */
        .custom-th {
            font-weight: 600;
            font-size: 18px;
            color: #FF5733;
        }

        /* ปรับขนาดฟอนต์ตามความเหมาะสม */
        @media (max-width: 768px) {
            body {
                font-size: 14px;
            }

            h1 {
                font-size: 24px;
            }

            .card-title {
                font-size: 18px;
            }
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include '../../../include/Navbar.php'; ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Add Employee</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Add Employee</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Employee Information</h3>
                        </div>
                        <div class="card-body">
                            <form id="addEmployeeForm" action="#" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <div class="row">
                                    <!-- ข้อมูลส่วนตัว -->
                                    <div class="col-md-6">
                                        <h4>ข้อมูลส่วนตัว</h4>
                                        <div class="form-group">
                                            <label>ชื่อ (ภาษาไทย)<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="first_name_th" required>
                                        </div>
                                        <div class="form-group">
                                            <label>นามสกุล (ภาษาไทย)<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="last_name_th" required>
                                        </div>
                                        <div class="form-group">
                                            <label>First Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="first_name_en" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Last Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="last_name_en" required>
                                        </div>
                                        <div class="form-group">
                                            <label>ชื่อเล่น (ภาษาไทย)</label>
                                            <input type="text" class="form-control" name="nickname_th">
                                        </div>
                                        <div class="form-group">
                                            <label>ชื่อเล่น (ภาษาอังกฤษ)</label>
                                            <input type="text" class="form-control" name="nickname_en">
                                        </div>
                                        <div class="form-group">
                                            <label>เพศ<span class="text-danger">*</span></label>
                                            <select class="form-control" name="gender" required>
                                                <option value="male">ชาย</option>
                                                <option value="female">หญิง</option>
                                                <option value="other">อื่นๆ</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>วันเกิด</label>
                                            <input type="date" class="form-control" name="birth_date">
                                        </div>
                                    </div>

                                    <!-- ข้อมูลการติดต่อ -->
                                    <div class="col-md-6">
                                        <h4>ข้อมูลการติดต่อ</h4>
                                        <div class="form-group">
                                            <label>อีเมลส่วนตัว<span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="personal_email" required>
                                        </div>
                                        <div class="form-group">
                                            <label>อีเมลบริษัท</label>
                                            <input type="email" class="form-control" name="company_email">
                                        </div>
                                        <div class="form-group">
                                            <label>เบอร์โทรศัพท์<span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" name="phone" required>
                                        </div>
                                        <div class="form-group">
                                            <label>ที่อยู่</label>
                                            <textarea class="form-control" name="address" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- ข้อมูลการทำงาน -->
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h4>ข้อมูลการทำงาน</h4>
                                        <div class="form-group">
                                            <label>ตำแหน่ง</label>
                                            <input type="text" class="form-control" name="position">
                                        </div>
                                        <div class="form-group">
                                            <label>แผนก</label>
                                            <input type="text" class="form-control" name="department" list="departmentList">
                                            <datalist id="departmentList">
                                                <?php foreach ($departments as $dept): ?>
                                                    <option value="<?php echo htmlspecialchars($dept['department']); ?>">
                                                    <?php endforeach; ?>
                                            </datalist>
                                        </div>
                                        <div class="form-group">
                                            <label>ทีม</label>
                                            <select class="form-control select2" name="team_id">
                                                <option value="">เลือกทีม</option>
                                                <?php foreach ($query_teams as $team): ?>
                                                    <option value="<?php echo htmlspecialchars($team['team_id']); ?>">
                                                        <?php echo htmlspecialchars($team['team_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>หัวหน้างาน</label>
                                            <select class="form-control select2" name="supervisor_id">
                                                <option value="">เลือกหัวหน้างาน</option>
                                                <?php foreach ($supervisors as $supervisor): ?>
                                                    <option value="<?php echo htmlspecialchars($supervisor['user_id']); ?>">
                                                        <?php echo htmlspecialchars($supervisor['first_name'] . ' ' . $supervisor['last_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>วันที่เริ่มงาน</label>
                                            <input type="date" class="form-control" name="hire_date">
                                        </div>
                                    </div>

                                    <!-- รูปโปรไฟล์ -->
                                    <div class="col-md-6">
                                        <h4>รูปโปรไฟล์</h4>
                                        <div class="form-group">
                                            <label>อัพโหลดรูปโปรไฟล์</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="profile_image" id="profile_image" accept="image/*">
                                                <label class="custom-file-label">เลือกไฟล์</label>
                                            </div>
                                            <img id="imgPreview" src="#" alt="Preview" style="max-width: 200px; display: none; margin-top: 10px;">
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-sm float-right">
                                <i class="fas fa-user-plus mr-2"></i>บันทึกข้อมูล
                            </button>
                        </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../../../include/Footer.php'; ?>
    </div>
    <!-- เพิ่ม JavaScript ที่จำเป็น -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script>
        $(function() {
            // ทำให้ Select2 ทำงานกับ dropdowns
            $('.select2').select2({
                theme: 'bootstrap4',
                selectionCssClass: 'form-control'
            });

            // เพิ่มโค้ดนี้เพื่อให้ Select2 แสดงค่า Default ที่ถูกต้อง
            $('#team_id').trigger('change');

            // แสดงชื่อไฟล์ที่เลือกใน custom file input
            $(".custom-file-input").on("change", function() {
                var fileName = $(this).val().split("\\").pop();
                $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
            });

            // แสดงตัวอย่างรูปโปรไฟล์ที่อัพโหลด
            $('#profile_image').on('change', function() {
                var file = this.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(event) {
                        $('#imgPreview').attr('src', event.target.result).show();
                    }
                    reader.readAsDataURL(file);
                }
            });

            // ฟังก์ชันตรวจสอบข้อมูลซ้ำ
            async function checkDuplicate(field, value) {
                try {
                    const response = await $.ajax({
                        type: 'POST',
                        url: 'check_duplicate.php', // สร้างไฟล์นี้ใหม่
                        data: {
                            field: field,
                            value: value
                        }
                    });
                    return JSON.parse(response);
                } catch (error) {
                    console.error('Error checking duplicate:', error);
                    return {
                        isDuplicate: false
                    };
                }
            }

            // เพิ่มฟังก์ชันตรวจสอบเบอร์โทร
            function validatePhone(phone) {
                // ลบเครื่องหมาย - ออกก่อนตรวจสอบความยาว
                const cleanPhone = phone.replace(/-/g, '');

                // ตรวจสอบว่าเป็นตัวเลขเท่านั้นและมีความยาว 10 หลัก
                const phoneRegex = /^[0-9]{10}$/;

                // ตรวจสอบรูปแบบเบอร์มือถือไทย (เริ่มต้นด้วย 06-09)
                const thaiMobileRegex = /^0[6-9][0-9]{8}$/;

                if (!phoneRegex.test(cleanPhone)) {
                    return 'กรุณากรอกเบอร์โทรเป็นตัวเลข 10 หลัก';
                }
                if (!thaiMobileRegex.test(cleanPhone)) {
                    return 'กรุณากรอกเบอร์โทรให้ถูกต้องตามรูปแบบเบอร์มือถือไทย';
                }
                return null;
            }

            // เพิ่ม event listener สำหรับการตรวจสอบขณะกรอกข้อมูล
            $('input[name="phone"]').on('input', function(e) {
                let input = e.target.value;

                // อนุญาตเฉพาะตัวเลขและเครื่องหมาย -
                let cleaned = input.replace(/[^0-9-]/g, '');

                // จำกัดความยาวรวมไม่เกิน 12 ตัว (10 ตัวเลข + 2 เครื่องหมาย -)
                if (cleaned.length > 12) {
                    cleaned = cleaned.substring(0, 12);
                }

                // จัดรูปแบบอัตโนมัติ xxx-xxx-xxxx
                if (cleaned.length >= 3 && cleaned.length <= 12) {
                    let parts = [];
                    let cleanNumber = cleaned.replace(/-/g, '');

                    if (cleanNumber.length >= 3) {
                        parts.push(cleanNumber.substring(0, 3));
                    }
                    if (cleanNumber.length >= 6) {
                        parts.push(cleanNumber.substring(3, 6));
                    }
                    if (cleanNumber.length > 6) {
                        parts.push(cleanNumber.substring(6));
                    }

                    cleaned = parts.join('-');
                }

                // อัพเดทค่าในช่องกรอก
                $(this).val(cleaned);
            });


            // จัดการการส่งฟอร์ม
            $('#addEmployeeForm').on('submit', async function(e) {
                e.preventDefault();


                // ตรวจสอบข้อมูลซ้ำ
                const personalEmail = $('input[name="personal_email"]').val();
                const companyEmail = $('input[name="company_email"]').val();
                const phone = $('input[name="phone"]').val();

                // ตรวจสอบเบอร์โทร
                if (phone) {
                    const phoneError = validatePhone(phone);
                    if (phoneError) {
                        Swal.fire({
                            icon: 'error',
                            title: 'รูปแบบเบอร์โทรไม่ถูกต้อง',
                            text: phoneError,
                            confirmButtonText: 'ตกลง'
                        });
                        return;
                    }
                }

                let duplicateErrors = [];

                // ตรวจสอบอีเมลส่วนตัวซ้ำ
                const personalEmailCheck = await checkDuplicate('personal_email', personalEmail);
                if (personalEmailCheck.isDuplicate) {
                    duplicateErrors.push('อีเมลส่วนตัวนี้มีในระบบแล้ว');
                }

                // ตรวจสอบอีเมลบริษัทซ้ำ
                // ตรวจสอบอีเมลบริษัทซ้ำเฉพาะเมื่อมีการกรอกข้อมูล
                if (companyEmail && companyEmail.length > 0) { // เพิ่มเงื่อนไขตรวจสอบ
                    const companyEmailCheck = await checkDuplicate('company_email', companyEmail);
                    if (companyEmailCheck.isDuplicate) {
                        duplicateErrors.push('อีเมลบริษัทนี้มีในระบบแล้ว');
                    }
                }

                // ตรวจสอบเบอร์โทรศัพท์ซ้ำ
                if (phone) {
                    const phoneCheck = await checkDuplicate('phone', phone);
                    if (phoneCheck.isDuplicate) {
                        duplicateErrors.push('เบอร์โทรศัพท์นี้มีในระบบแล้ว');
                    }
                }

                // ถ้ามีข้อมูลซ้ำ
                if (duplicateErrors.length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'พบข้อมูลซ้ำ',
                        html: duplicateErrors.join('<br>'),
                        confirmButtonText: 'ตกลง'
                    });
                    return;
                }

                // ถ้าไม่มีข้อมูลซ้ำ ดำเนินการส่งฟอร์ม
                Swal.fire({
                    title: 'ยืนยันการบันทึกข้อมูล',
                    text: 'คุณต้องการบันทึกข้อมูลใช่หรือไม่?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData(this);

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
                            url: 'add_employees.php',
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            success: function(response) {
                                Swal.fire({
                                    icon: response.status,
                                    title: response.status === 'success' ? 'สำเร็จ' : 'ข้อผิดพลาด',
                                    text: response.message,
                                    confirmButtonText: 'ตกลง'
                                }).then((result) => {
                                    if (result.isConfirmed && response.status === 'success') {
                                        window.location.href = 'employees.php';
                                    }
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: xhr.responseJSON?.message || 'ไม่สามารถบันทึกข้อมูลได้',
                                    confirmButtonText: 'ตกลง'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>

    <!-- เพิ่ม JavaScript เพื่อจัดการการแสดงผล:บริษัท -->
    <script>
        $(function() {
            // จัดการการแสดงผลของ datalist
            $('#company').on('input', function() {
                var val = $(this).val();
                var list = $('#companyList');

                // ถ้าผู้ใช้พิมพ์ค่าใหม่ที่ไม่มีในรายการ ก็จะใช้ค่านั้นได้
                if (val && !list.find('option').filter(function() {
                        return $(this).val() === val;
                    }).length) {
                    // ค่าใหม่ที่ผู้ใช้กรอก
                }
            });
        });
    </script>
</body>

</html>