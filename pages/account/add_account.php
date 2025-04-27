<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ตรวจสอบสิทธิ์การเข้าถึง
$role = $_SESSION['role'];  // บทบาทของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // team_id ของผู้ใช้จาก session
$created_by = $_SESSION['user_id'];  // user_id ของผู้สร้างจาก session

// จำกัดการเข้าถึงเฉพาะผู้ใช้ที่มีสิทธิ์เท่านั้น
if (!in_array($role, ['Executive', 'Sale Supervisor', 'Seller'])) {
    header("Location: unauthorized.php");
    exit();
}

// สร้างหรือดึง CSRF Token เพื่อป้องกันการโจมตีแบบ CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ฟังก์ชันทำความสะอาดข้อมูล input เพื่อป้องกันการโจมตีแบบ XSS

function clean_input($data)
{
    // ทำความสะอาดข้อมูลแต่ยังคงเก็บอักขระพิเศษไว้
    $data = trim($data);
    // ป้องกัน SQL Injection โดยใช้ PDO parameters แทน
    return $data;
}
// ฟังก์ชันสำหรับสร้าง UUID สำหรับ user_id ใหม่
function generateUUID()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// ฟังก์ชันตรวจสอบความซับซ้อนของรหัสผ่าน
function isPasswordValid($password)
{
    // ตรวจสอบความยาวขั้นต่ำ 8 ตัวอักษร
    if (strlen($password) < 8) {
        return false;
    }
    // ตรวจสอบว่ามีตัวอักษรพิมพ์ใหญ่อย่างน้อย 1 ตัว
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    // ตรวจสอบว่ามีตัวอักษรพิมพ์เล็กอย่างน้อย 1 ตัว
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    // ตรวจสอบว่ามีตัวเลขอย่างน้อย 1 ตัว
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    // ตรวจสอบว่ามีอักขระพิเศษอย่างน้อย 1 ตัว
    if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
        return false;
    }
    return true;
}

// ฟังก์ชันตรวจสอบความถูกต้องของเบอร์โทรศัพท์
function isPhoneValid($phone)
{
    // ตรวจสอบว่าเบอร์โทรศัพท์มีเฉพาะตัวเลขและมีความยาว 10 หลัก
    return preg_match('/^[0-9]{10}$/', $phone);
}

// ฟังก์ชันตรวจสอบสิทธิ์การเพิ่มผู้ใช้ตามบทบาท
function canAddUser($currentUserRole, $newUserRole)
{
    $roleHierarchy = [
        'Executive' => ['Executive', 'Sale Supervisor', 'Seller', 'Engineer'],
        'Sale Supervisor' => ['Seller', 'Engineer'],
        'Seller' => [],
        'Engineer' => []
    ];
    return in_array($newUserRole, $roleHierarchy[$currentUserRole] ?? []);
}

// ฟังก์ชันบันทึก log การสร้างบัญชีผู้ใช้
function logUserCreation($creator_id, $new_user_id, $new_user_role)
{
    global $condb;
    $sql = "INSERT INTO user_creation_logs (creator_id, new_user_id, new_user_role, created_at) 
            VALUES (:creator_id, :new_user_id, :new_user_role, NOW())";
    $stmt = $condb->prepare($sql);
    $stmt->execute([
        ':creator_id' => $creator_id,
        ':new_user_id' => $new_user_id,
        ':new_user_role' => $new_user_role
    ]);
}

// ดึงข้อมูลทีมจากฐานข้อมูลตามบทบาทของผู้ใช้
if ($role === 'Sale Supervisor') {
    // Sale Supervisor จะเห็นเฉพาะทีมของตนเอง
    $sql_teams = "SELECT team_id, team_name FROM teams WHERE team_id = :team_id";
    $stmt_teams = $condb->prepare($sql_teams);
    $stmt_teams->bindParam(':team_id', $team_id, PDO::PARAM_STR);
    $stmt_teams->execute();
} else {
    // Executive และผู้ที่มีสิทธิ์สูงกว่า สามารถเห็นทีมทั้งหมด
    $sql_teams = "SELECT team_id, team_name FROM teams";
    $stmt_teams = $condb->prepare($sql_teams);
    $stmt_teams->execute();
}
$query_teams = $stmt_teams->fetchAll();

// ตัวแปรเก็บข้อความแจ้งเตือนสำหรับการตรวจสอบข้อมูล
$error_messages = [];

// ตรวจสอบการส่งฟอร์มแบบ AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // ตรวจสอบ CSRF Token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die(json_encode(['success' => false, 'errors' => ['Invalid CSRF token']]));
    }

    // ทำความสะอาดและรับข้อมูลจากฟอร์ม
    $user_id = generateUUID();
    $first_name = clean_input($_POST['first_name']);
    $last_name = clean_input($_POST['last_name']);
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $password = $_POST['password'];
    $position = clean_input($_POST['position']);
    $team_id_new = !empty($_POST['team_id']) ? clean_input($_POST['team_id']) : null;
    $role_new = clean_input($_POST['role']);
    $company = clean_input($_POST['company']);

    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($first_name)) $error_messages[] = "กรุณากรอกชื่อ";
    if (empty($last_name)) $error_messages[] = "กรุณากรอกนามสกุล";
    if (empty($username)) $error_messages[] = "กรุณากรอกชื่อผู้ใช้";
    if (empty($email)) $error_messages[] = "กรุณากรอกอีเมล";
    if (empty($password)) $error_messages[] = "กรุณากรอกรหัสผ่าน";
    if (empty($team_id_new)) $error_messages[] = "กรุณาเลือกทีม";
    if (empty($role_new)) $error_messages[] = "กรุณาเลือกบทบาท";

    // ตรวจสอบความถูกต้องของรูปแบบอีเมล
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_messages[] = "รูปแบบอีเมลไม่ถูกต้อง";
    }

    // ตรวจสอบความซับซ้อนของรหัสผ่าน
    if (!isPasswordValid($password)) {
        $error_messages[] = "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร ประกอบด้วยตัวอักษรพิมพ์ใหญ่ พิมพ์เล็ก ตัวเลข และอักขระพิเศษอย่างน้อย 1 ตัว";
    }

    // ตรวจสอบความถูกต้องของเบอร์โทรศัพท์
    if (!empty($phone) && !isPhoneValid($phone)) {
        $error_messages[] = "เบอร์โทรศัพท์ไม่ถูกต้อง กรุณากรอกเฉพาะตัวเลข 10 หลัก";
    }

    // ตรวจสอบสิทธิ์การเพิ่มผู้ใช้
    if (!canAddUser($role, $role_new)) {
        $error_messages[] = "คุณไม่มีสิทธิ์เพิ่มผู้ใช้ในบทบาทนี้";
    }

    // ถ้าไม่มีข้อผิดพลาด ดำเนินการบันทึกข้อมูลผู้ใช้ใหม่
    if (empty($error_messages)) {
        try {
            // ตรวจสอบชื่อผู้ใช้ซ้ำในฐานข้อมูล
            $stmt = $condb->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("ชื่อผู้ใช้นี้มีอยู่แล้ว");
            }

            // ตรวจสอบอีเมลซ้ำในฐานข้อมูล
            $stmt = $condb->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("อีเมลนี้มีอยู่แล้ว");
            }

            // ตรวจสอบเบอร์โทรศัพท์ซ้ำในฐานข้อมูล
            if (!empty($phone)) {
                $stmt = $condb->prepare("SELECT COUNT(*) FROM users WHERE phone = :phone");
                $stmt->execute([':phone' => $phone]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception("เบอร์โทรศัพท์นี้มีอยู่แล้ว");
                }
            }

            // เข้ารหัสรหัสผ่านของผู้ใช้ใหม่
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // อัปโหลดรูปโปรไฟล์ (ถ้ามี)
            $profile_image = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['profile_image']['name'];
                $filetype = $_FILES['profile_image']['type'];
                $filesize = $_FILES['profile_image']['size'];

                // ตรวจสอบนามสกุลไฟล์
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed)) {
                    throw new Exception("รูปแบบไฟล์ไม่ถูกต้อง กรุณาอัปโหลดไฟล์รูปภาพเท่านั้น");
                }

                // ตรวจสอบขนาดไฟล์ (จำกัดที่ 5MB)
                if ($filesize > 5242880) {
                    throw new Exception("ไฟล์มีขนาดใหญ่เกินไป กรุณาอัปโหลดไฟล์ขนาดไม่เกิน 5MB");
                }

                // สร้างชื่อไฟล์ใหม่
                $new_filename = uniqid() . '.' . $ext;
                $upload_path = '../../uploads/profile_images/' . $new_filename;

                // ย้ายไฟล์ที่อัปโหลด
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                    $profile_image = $new_filename;
                } else {
                    throw new Exception("เกิดข้อผิดพลาดในการอัปโหลดไฟล์");
                }
            }

            // เตรียม SQL สำหรับการเพิ่มข้อมูลผู้ใช้ใหม่
            $sql = "INSERT INTO users (user_id, first_name, last_name, username, email, phone, password, position, team_id, role, company, created_by, profile_image) 
                    VALUES (:user_id, :first_name, :last_name, :username, :email, :phone, :password, :position, :team_id, :role, :company, :created_by, :profile_image)";

            $stmt = $condb->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':username' => $username,
                ':email' => $email,
                ':phone' => $phone,
                ':password' => $hashed_password,
                ':position' => $position,
                ':team_id' => $team_id_new,
                ':role' => $role_new,
                ':company' => $company,
                ':created_by' => $created_by,
                ':profile_image' => $profile_image
            ]);

            // บันทึก log การสร้างบัญชีผู้ใช้
            logUserCreation($created_by, $user_id, $role_new);

            // ล้างค่า CSRF token เมื่อบันทึกข้อมูลสำเร็จ
            unset($_SESSION['csrf_token']);

            echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลผู้ใช้เรียบร้อยแล้ว']);
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

// ดึงข้อมูลทีมจากฐานข้อมูลตามบทบาทของผู้ใช้
if ($role === 'Sale Supervisor') {
    // Sale Supervisor จะเห็นเฉพาะทีมของตนเอง
    $sql_teams = "SELECT team_id, team_name FROM teams WHERE team_id = :team_id";
    $stmt_teams = $condb->prepare($sql_teams);
    $stmt_teams->bindParam(':team_id', $team_id, PDO::PARAM_STR);
    $stmt_teams->execute();
} else {
    // Executive และผู้ที่มีสิทธิ์สูงกว่า สามารถเห็นทีมทั้งหมด
    $sql_teams = "SELECT team_id, team_name FROM teams";
    $stmt_teams = $condb->prepare($sql_teams);
    $stmt_teams->execute();
}
$query_teams = $stmt_teams->fetchAll();

// เก็บ team_id ของผู้ใช้ปัจจุบันเพื่อใช้เป็นค่า Default
$current_user_team_id = $_SESSION['team_id'];


// เพิ่ม SQL query เพื่อดึงรายการบริษัทที่มีอยู่:
$stmt = $condb->prepare("SELECT DISTINCT company FROM users WHERE company IS NOT NULL AND company != ''");
$stmt->execute();
$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Create Account</title>
    <?php include '../../include/header.php'; ?>
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
        <!-- Navbar and Sidebar -->
        <?php include '../../include/navbar.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Create Account</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Create Account</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Account Information</h3>
                        </div>
                        <div class="card-body">
                            <form id="addUserForm" action="#" method="POST" enctype="multipart/form-data">
                                <!-- CSRF Token -->
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="profile_image">รูปโปรไฟล์</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="profile_image" name="profile_image">
                                                <label class="custom-file-label" for="profile_image">เลือกไฟล์</label>
                                            </div>
                                        </div>

                                        <div class="form-group text-center">
                                            <img id="imgPreview" src="#" alt="รูปโปรไฟล์" class="mt-3" style="max-width: 200px; display: none;" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">ชื่อ<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control" id="first_name" name="first_name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">นามสกุล<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control" id="last_name" name="last_name">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">อีเมล<span class="text-danger">*</span></label>
                                            <div class="input-group input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                </div>
                                                <input type="email" class="form-control form-control" id="email" name="email">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">เบอร์โทรศัพท์</label>
                                            <div class="input-group input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                </div>
                                                <input type="tel" class="form-control form-control" id="phone" name="phone">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">ชื่อผู้ใช้<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control" id="username" name="username">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">รหัสผ่าน<span class="text-danger">*</span></label>
                                            <input type="password" class="form-control form-control" id="password" name="password">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="team_id">ทีม<span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="team_id" name="team_id">
                                                <option value="">เลือกทีม</option>
                                                <?php foreach ($query_teams as $team): ?>
                                                    <option value="<?php echo $team['team_id']; ?>" <?php echo ($team['team_id'] == $current_user_team_id) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($team['team_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="role">บทบาท<span class="text-danger">*</span></label>
                                            <select class="form-control select2 " id="role" name="role">
                                                <option value="">เลือกบทบาท</option>
                                                <?php if ($role === 'Executive'): ?>
                                                    <option value="Executive">Executive</option>
                                                    <option value="Sale Supervisor">Sale Supervisor</option>
                                                    <option value="Seller">Seller</option>
                                                    <option value="Engineer">Engineer</option>
                                                <?php elseif ($role === 'Sale Supervisor'): ?>
                                                    <option value="Seller">Seller</option>
                                                    <option value="Engineer">Engineer</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="position">ตำแหน่ง</label>
                                            <input type="text" class="form-control " id="position" name="position">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company">บริษัท</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                </div>
                                                <input type="text"
                                                    class="form-control"
                                                    id="company"
                                                    name="company"
                                                    list="companyList"
                                                    placeholder="เลือกหรือพิมพ์ชื่อบริษัท">
                                                <datalist id="companyList">
                                                    <?php foreach ($companies as $company): ?>
                                                        <option value="<?php echo htmlspecialchars($company['company']); ?>">
                                                        <?php endforeach; ?>
                                                </datalist>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </form>
                        </div>
                        <div class="card-footer">
                            <button type="submit" form="addUserForm" class="btn btn-primary btn-sm float-right">
                                <i class="fas fa-user-plus mr-2"></i>สร้างบัญชี
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <?php include '../../include/footer.php'; ?>
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

            // จัดการการส่งฟอร์ม
            $('#addUserForm').on('submit', function(e) {
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
                    url: 'add_account.php',
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
                                    window.location.href = 'account.php';
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
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ กรุณาลองใหม่อีกครั้ง',
                            confirmButtonText: 'ตกลง'
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