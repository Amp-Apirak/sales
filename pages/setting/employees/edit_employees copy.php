<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
session_start();
include('../../../config/condb.php');

// ตรวจสอบการเข้าสู่ระบบและสิทธิ์การเข้าถึง
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Executive', 'Sale Supervisor', 'Engineer', 'Seller'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

// ดึงข้อมูลจาก session
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];

// สร้าง CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data) {
    return trim($data);
}

// ฟังก์ชันจัดการการอัปโหลดรูปภาพ
function handleImageUpload($image) {
    $target_dir = "../../../uploads/employee_images/";
    
    // สร้างโฟลเดอร์ถ้ายังไม่มี
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // สร้างชื่อไฟล์ใหม่
    $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $extension;
    $target_file = $target_dir . $new_filename;

    // ตรวจสอบประเภทไฟล์
    $allowed_types = ['jpg', 'jpeg', 'png'];
    $file_extension = strtolower($extension);
    if (!in_array($file_extension, $allowed_types)) {
        throw new Exception("อนุญาตเฉพาะไฟล์ JPG, JPEG และ PNG เท่านั้น");
    }

    // ตรวจสอบขนาดไฟล์
    if ($image['size'] > 2 * 1024 * 1024) {
        throw new Exception("ขนาดไฟล์ต้องไม่เกิน 2MB");
    }

    // อัปโหลดไฟล์
    if (!move_uploaded_file($image['tmp_name'], $target_file)) {
        throw new Exception("เกิดข้อผิดพลาดในการอัปโหลดไฟล์");
    }

    return $new_filename;
}

// ตรวจสอบว่ามีการส่ง ID มาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: employees.php");
    exit();
}

// ถอดรหัส ID
$employee_id = decryptUserId($_GET['id']);

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

// ดึงข้อมูลพนักงานที่ต้องการแก้ไข
try {
    $stmt = $condb->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([$employee_id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        $_SESSION['error'] = "ไม่พบข้อมูลพนักงาน";
        header("Location: employees.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    header("Location: employees.php");
    exit();
}

// ดึงข้อมูลแผนก (ถ้ามี)
try {
    $sql_departments = "SELECT DISTINCT department FROM employees WHERE department IS NOT NULL";
    $stmt_departments = $condb->prepare($sql_departments);
    $stmt_departments->execute();
    $departments = $stmt_departments->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching departments: " . $e->getMessage();
}

// จัดการการอัปเดตข้อมูล
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $condb->beginTransaction();

        // จัดการอัปโหลดรูปภาพ
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            try {
                $profile_image = handleImageUpload($_FILES['profile_image']);
                
                // ลบรูปภาพเก่า
                if (!empty($employee['profile_image'])) {
                    $old_image_path = "../../../uploads/employee_images/" . $employee['profile_image'];
                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }
            } catch (Exception $e) {
                throw new Exception("Image upload error: " . $e->getMessage());
            }
        }

        // เตรียมข้อมูลสำหรับอัปเดต
        $data = [
            'first_name_th' => $_POST['first_name_th'],
            'last_name_th' => $_POST['last_name_th'],
            'first_name_en' => $_POST['first_name_en'],
            'last_name_en' => $_POST['last_name_en'],
            'nickname_th' => $_POST['nickname_th'],
            'nickname_en' => $_POST['nickname_en'],
            'gender' => $_POST['gender'],
            'birth_date' => !empty($_POST['birth_date']) ? $_POST['birth_date'] : null,
            'personal_email' => $_POST['personal_email'],
            'company_email' => $_POST['company_email'],
            'phone' => $_POST['phone'],
            'position' => $_POST['position'],
            'department' => $_POST['department'],
            'team_id' => !empty($_POST['team_id']) ? $_POST['team_id'] : null,
            'supervisor_id' => !empty($_POST['supervisor_id']) ? $_POST['supervisor_id'] : null,
            'address' => $_POST['address'],
            'hire_date' => !empty($_POST['hire_date']) ? $_POST['hire_date'] : null,
            'id' => $employee_id
        ];

        // สร้าง SQL query
        $sql = "UPDATE employees SET 
                first_name_th = :first_name_th,
                last_name_th = :last_name_th,
                first_name_en = :first_name_en,
                last_name_en = :last_name_en,
                nickname_th = :nickname_th,
                nickname_en = :nickname_en,
                gender = :gender,
                birth_date = :birth_date,
                personal_email = :personal_email,
                company_email = :company_email,
                phone = :phone,
                position = :position,
                department = :department,
                team_id = :team_id,
                supervisor_id = :supervisor_id,
                address = :address,
                hire_date = :hire_date";

        // เพิ่มการอัปเดตรูปภาพ
        if (isset($profile_image)) {
            $sql .= ", profile_image = :profile_image";
            $data['profile_image'] = $profile_image;
        }

        $sql .= " WHERE id = :id";

        // ประมวลผล query
        $stmt = $condb->prepare($sql);
        $stmt->execute($data);

        $condb->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว'
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        $condb->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ดึงข้อมูลเพิ่มเติมที่จำเป็น (ถ้ามี)
try {
    // ดึงข้อมูลทีมปัจจุบันของพนักงาน
    if (!empty($employee['team_id'])) {
        $stmt = $condb->prepare("SELECT team_name FROM teams WHERE team_id = ?");
        $stmt->execute([$employee['team_id']]);
        $current_team = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ดึงข้อมูลหัวหน้างานปัจจุบันของพนักงาน
    if (!empty($employee['supervisor_id'])) {
        $stmt = $condb->prepare("SELECT first_name, last_name FROM users WHERE user_id = ?");
        $stmt->execute([$employee['supervisor_id']]);
        $current_supervisor = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching additional data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Create Account</title>
    <?php include '../../../include/header.php'; ?>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                            <h1>Edit Employee</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Edit Employee</li>
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
                            <form id="editEmployeeForm" action="#" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <div class="row">
                                    <!-- ข้อมูลส่วนตัว -->
                                    <div class="col-md-6">
                                        <h4>ข้อมูลส่วนตัว</h4>
                                        <div class="form-group">
                                            <label>ชื่อ (ภาษาไทย)<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="first_name_th" value="<?php echo htmlspecialchars($employee['first_name_th']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>นามสกุล (ภาษาไทย)<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="last_name_th" value="<?php echo htmlspecialchars($employee['last_name_th']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>First Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="first_name_en" value="<?php echo htmlspecialchars($employee['first_name_en']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Last Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="last_name_en" value="<?php echo htmlspecialchars($employee['last_name_en']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>ชื่อเล่น (ภาษาไทย)</label>
                                            <input type="text" class="form-control" name="nickname_th" value="<?php echo htmlspecialchars($employee['nickname_th']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>ชื่อเล่น (ภาษาอังกฤษ)</label>
                                            <input type="text" class="form-control" name="nickname_en" value="<?php echo htmlspecialchars($employee['nickname_en']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>เพศ<span class="text-danger">*</span><?php echo htmlspecialchars($employee['gender']); ?></label>
                                            <select class="form-control" name="gender">
                                                <option value="male">ชาย</option>
                                                <option value="female">หญิง</option>
                                                <option value="other">อื่นๆ</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>วันเกิด</label>
                                            <input type="date" class="form-control" name="birth_date" value="<?php echo htmlspecialchars($employee['birth_date']); ?>">
                                        </div>
                                    </div>

                                    <!-- ข้อมูลการติดต่อ -->
                                    <div class="col-md-6">
                                        <h4>ข้อมูลการติดต่อ</h4>
                                        <div class="form-group">
                                            <label>อีเมลส่วนตัว<span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="personal_email" value="<?php echo htmlspecialchars($employee['personal_email']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>อีเมลบริษัท<span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="company_email" value="<?php echo htmlspecialchars($employee['company_email']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>เบอร์โทรศัพท์</label>
                                            <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($employee['phone']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>ที่อยู่</label>
                                            <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($employee['address']); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- ข้อมูลการทำงาน -->
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h4>ข้อมูลการทำงาน</h4>
                                        <div class="form-group">
                                            <label>ตำแหน่ง</label>
                                            <input type="text" class="form-control" name="position" value="<?php echo htmlspecialchars($employee['position']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>แผนก</label>
                                            <input type="text" class="form-control" name="department" list="departmentList" value="<?php echo htmlspecialchars($employee['department']); ?>">
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
                                                    <option value="<?php echo htmlspecialchars($team['team_id']); ?>"
                                                        <?php echo ($employee['team_id'] == $team['team_id']) ? 'selected' : ''; ?>>
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
                                                    <option value="<?php echo htmlspecialchars($supervisor['user_id']); ?>"
                                                        <?php echo ($employee['supervisor_id'] == $supervisor['user_id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($supervisor['first_name'] . ' ' . $supervisor['last_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>วันที่เริ่มงาน</label>
                                            <input type="date" class="form-control" name="hire_date" value="<?php echo htmlspecialchars($employee['hire_date']); ?>">
                                        </div>
                                    </div>



                                    <!-- รูปโปรไฟล์ -->
                                    <div class="col-md-6">
                                        <h4>รูปโปรไฟล์</h4>
                                        <!-- ส่วนแสดงรูปภาพปัจจุบัน -->
                                        <?php if (!empty($employee['profile_image'])): ?>
                                            <div class="current-image">
                                                <img src="../../../uploads/employee_images/<?php echo $employee['profile_image']; ?>"
                                                    alt="Current profile image" class="img-thumbnail mt-2" style="max-width: 200px;">
                                            </div>
                                        <?php endif; ?>
                                        <div class="form-group mt-2">
                                            <label>อัพโหลดรูปโปรไฟล์</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="profile_image" id="profile_image" accept="image/*">
                                                <label class="custom-file-label">เลือกไฟล์</label>
                                            </div>
                                            <!-- <img id="imgPreview" src="#" alt="Preview" style="max-width: 200px; display: none; margin-top: 10px;"> -->
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
        $(document).ready(function() {
            // กำหนดค่า Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // ตั้งค่าค่าเริ่มต้นสำหรับ select fields
            setInitialValues();

            // จัดการการแสดงตัวอย่างรูปภาพ
            handleImagePreview();

            // ตรวจสอบการเปลี่ยนแปลงฟอร์ม
            let formChanged = false;
            $('#editEmployeeForm :input').on('change input', function() {
                formChanged = true;
            });

            // แจ้งเตือนเมื่อออกจากหน้าที่มีการเปลี่ยนแปลง
            window.onbeforeunload = function() {
                if (formChanged) {
                    return "คุณมีข้อมูลที่ยังไม่ได้บันทึก ต้องการออกจากหน้านี้หรือไม่?";
                }
            };

            // จัดการการส่งฟอร์ม
            $('#editEmployeeForm').on('submit', function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    return false;
                }

                Swal.fire({
                    title: 'ยืนยันการแก้ไข',
                    text: 'คุณต้องการบันทึกการเปลี่ยนแปลงหรือไม่?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'บันทึก',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitForm();
                    }
                });
            });
        });

        // ฟังก์ชันส่งฟอร์ม
        function submitForm() {
            return new Promise((resolve, reject) => {
                const formData = new FormData($('#editEmployeeForm')[0]);

                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json', // ระบุ dataType เป็น json
                    success: function(response) {
                        try {
                            // ตรวจสอบ response
                            console.log('Response:', response); // เพิ่ม debug log

                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'สำเร็จ',
                                    text: response.message,
                                    confirmButtonText: 'ตกลง'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = 'employees.php';
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: response.message,
                                    confirmButtonText: 'ตกลง'
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถประมวลผลข้อมูลได้',
                                confirmButtonText: 'ตกลง'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์',
                            confirmButtonText: 'ตกลง'
                        });
                    }
                });
            });
        }

        // ฟังก์ชันตั้งค่าเริ่มต้น
        function setInitialValues() {
            const employee = <?php echo json_encode($employee); ?>;

            if (employee) {
                $('select[name="gender"]').val(employee.gender);
                $('select[name="team_id"]').val(employee.team_id).trigger('change');
                $('select[name="supervisor_id"]').val(employee.supervisor_id).trigger('change');

                if (employee.profile_image) {
                    $('#imgPreview')
                        .attr('src', '../../../uploads/employee_images/' + employee.profile_image)
                        .show();
                }
            }
        }

        // ฟังก์ชันจัดการรูปภาพ
        function handleImagePreview() {
            $('#profile_image').change(function() {
                const file = this.files[0];
                if (file) {
                    if (validateImageFile(file)) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#imgPreview')
                                .attr('src', e.target.result)
                                .show();
                        };
                        reader.readAsDataURL(file);
                        $('.custom-file-label').text(file.name);
                    } else {
                        resetFileInput();
                        Swal.fire({
                            icon: 'error',
                            title: 'ไฟล์ไม่ถูกต้อง',
                            text: 'กรุณาเลือกไฟล์รูปภาพ (jpg, jpeg, png) ขนาดไม่เกิน 2MB',
                            confirmButtonText: 'ตกลง'
                        });
                    }
                }
            });
        }

        // ฟังก์ชันตรวจสอบความถูกต้อง
        function validateForm() {
            const requiredFields = {
                'first_name_th': 'ชื่อ (ภาษาไทย)',
                'last_name_th': 'นามสกุล (ภาษาไทย)',
                'first_name_en': 'First Name',
                'last_name_en': 'Last Name',
                'personal_email': 'อีเมลส่วนตัว',
                'company_email': 'อีเมลบริษัท'
            };

            const errors = [];

            // ตรวจสอบฟิลด์ที่จำเป็น
            Object.entries(requiredFields).forEach(([field, label]) => {
                const value = $(`[name="${field}"]`).val()?.trim();
                if (!value) {
                    errors.push(`กรุณากรอก${label}`);
                }
            });

            // ตรวจสอบรูปแบบอีเมล
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            ['personal_email', 'company_email'].forEach(field => {
                const email = $(`[name="${field}"]`).val()?.trim();
                if (email && !emailRegex.test(email)) {
                    errors.push(`กรุณากรอก${requiredFields[field]}ให้ถูกต้อง`);
                }
            });

            // ตรวจสอบเบอร์โทรศัพท์
            const phone = $('[name="phone"]').val()?.trim();
            if (phone) {
                const phoneRegex = /^[0-9]{10}$/;
                if (!phoneRegex.test(phone)) {
                    errors.push('กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง (10 หลัก)');
                }
            }

            if (errors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาตรวจสอบข้อมูล',
                    html: errors.join('<br>'),
                    confirmButtonText: 'ตกลง'
                });
                return false;
            }

            return true;
        }

        // ฟังก์ชันตรวจสอบไฟล์รูปภาพ
        function validateImageFile(file) {
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (!validTypes.includes(file.type)) {
                return false;
            }

            if (file.size > maxSize) {
                return false;
            }

            return true;
        }

        // ฟังก์ชันรีเซ็ตอัพโหลดไฟล์
        function resetFileInput() {
            $('#profile_image').val('');
            $('.custom-file-label').text('เลือกไฟล์');
            $('#imgPreview').attr('src', '').hide();
        }
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