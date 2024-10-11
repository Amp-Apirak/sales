<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ฟังก์ชันตรวจสอบความซับซ้อนของรหัสผ่าน
function isPasswordValid($password)
{
    return strlen($password) >= 8 &&
        preg_match('/[A-Z]/', $password) &&
        preg_match('/[a-z]/', $password) &&
        preg_match('/[0-9]/', $password) &&
        preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password);
}

// ฟังก์ชันตรวจสอบความถูกต้องของเบอร์โทรศัพท์
function isPhoneValid($phone)
{
    return preg_match('/^[0-9]{10}$/', $phone);
}

// เพิ่มฟังก์ชันตรวจสอบรูปแบบอีเมล
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// ตรวจสอบสิทธิ์การเข้าถึง
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$created_by = $_SESSION['user_id'];

if (!in_array($role, ['Executive', 'Sale Supervisor'])) {
    header("Location: unauthorized.php");
    exit();
}

// สร้างหรือดึง CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ตรวจสอบว่ามีการส่ง user_id มาหรือไม่
if (isset($_GET['user_id'])) {
    $encrypted_user_id = urldecode($_GET['user_id']);
    $user_id = decryptUserId($encrypted_user_id);

    if ($user_id === false) {
        die("รหัสผู้ใช้ไม่ถูกต้อง");
    }

    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $stmt = $condb->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("ไม่พบข้อมูลผู้ใช้");
    }

    // ตรวจสอบสิทธิ์ในการแก้ไขข้อมูล
    if ($role === 'Sale Supervisor' && ($user['team_id'] != $team_id || $user['role'] === 'Sale Supervisor')) {
        die("คุณไม่มีสิทธิ์ในการแก้ไขข้อมูลนี้");
    }
} else {
    die("ไม่มีรหัสผู้ใช้ที่ต้องการแก้ไข");
}

// ดึงข้อมูลทีมจากฐานข้อมูล
if ($role === 'Sale Supervisor') {
    // Sale Supervisor จะเห็นเฉพาะทีมของตนเอง
    $stmt_teams = $condb->prepare("SELECT team_id, team_name FROM teams WHERE team_id = :team_id");
    $stmt_teams->bindParam(':team_id', $team_id, PDO::PARAM_INT);
} else {
    // Executive และ Seller จะเห็นทุกทีม
    $stmt_teams = $condb->prepare("SELECT team_id, team_name FROM teams");
}
$stmt_teams->execute();
$teams = $stmt_teams->fetchAll(PDO::FETCH_ASSOC);

$error_messages = [];

// ตรวจสอบการส่งฟอร์มแบบ POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบ CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    // รับและทำความสะอาดข้อมูลจากฟอร์ม
    $first_name = clean_input($_POST['first_name']);
    $last_name = clean_input($_POST['last_name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $position = clean_input($_POST['position']);
    $team_id_new = !empty($_POST['team_id']) ? clean_input($_POST['team_id']) : null;
    $role_new = clean_input($_POST['role']);
    $company = clean_input($_POST['company']);
    $password = $_POST['password']; // ไม่ต้องทำความสะอาดรหัสผ่าน เพราะจะถูกแฮชอยู่แล้ว

    // เพิ่มการตรวจสอบสิทธิ์ก่อนการอัปเดตข้อมูล
    if ($role === 'Sale Supervisor') {
        // Sale Supervisor สามารถแก้ไขได้เฉพาะ Seller ในทีมของตนเอง
        if ($_POST['team_id'] != $team_id || $_POST['role'] !== 'Seller') {
            $error_messages[] = "คุณไม่มีสิทธิ์ในการแก้ไขข้อมูลนี้";
        }
    }

    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($first_name)) $error_messages[] = "กรุณากรอกชื่อ";
    if (empty($last_name)) $error_messages[] = "กรุณากรอกนามสกุล";
    if (empty($email)) $error_messages[] = "กรุณากรอกอีเมล";
    if (empty($team_id_new)) $error_messages[] = "กรุณาเลือกทีม";
    if (empty($role_new)) $error_messages[] = "กรุณาเลือกบทบาท";

    // ตรวจสอบความถูกต้องของรูปแบบอีเมล
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_messages[] = "รูปแบบอีเมลไม่ถูกต้อง";
    }

    // ตรวจสอบความซับซ้อนของรหัสผ่าน (ถ้ามีการเปลี่ยนแปลง)
    if (!empty($password) && !isPasswordValid($password)) {
        $error_messages[] = "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร ประกอบด้วยตัวอักษรพิมพ์ใหญ่ พิมพ์เล็ก ตัวเลข และอักขระพิเศษอย่างน้อย 1 ตัว";
    }

    // ตรวจสอบความถูกต้องของเบอร์โทรศัพท์
    if (!empty($phone) && !isPhoneValid($phone)) {
        $error_messages[] = "เบอร์โทรศัพท์ไม่ถูกต้อง กรุณากรอกเฉพาะตัวเลข 10 หลัก";
    }

    // ถ้าไม่มีข้อผิดพลาด ดำเนินการอัปเดตข้อมูลผู้ใช้
    if (empty($error_messages)) {
        try {
            $sql = "UPDATE users SET 
                    first_name = :first_name, 
                    last_name = :last_name, 
                    email = :email, 
                    phone = :phone, 
                    position = :position, 
                    team_id = :team_id, 
                    role = :role, 
                    company = :company";

            $params = [
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':email' => $email,
                ':phone' => $phone,
                ':position' => $position,
                ':team_id' => $team_id_new,
                ':role' => $role_new,
                ':company' => $company,
                ':user_id' => $user_id
            ];

            // ถ้ามีการเปลี่ยนรหัสผ่าน
            if (!empty($password)) {
                $sql .= ", password = :password";
                $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $sql .= " WHERE user_id = :user_id";

            $stmt = $condb->prepare($sql);
            $stmt->execute($params);

            // อัปเดตรูปโปรไฟล์ (ถ้ามี)
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['profile_image']['name'];
                $filetype = $_FILES['profile_image']['type'];
                $filesize = $_FILES['profile_image']['size'];

                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed)) {
                    throw new Exception("รูปแบบไฟล์ไม่ถูกต้อง กรุณาอัปโหลดไฟล์รูปภาพเท่านั้น");
                }

                if ($filesize > 5242880) {
                    throw new Exception("ไฟล์มีขนาดใหญ่เกินไป กรุณาอัปโหลดไฟล์ขนาดไม่เกิน 5MB");
                }

                $new_filename = uniqid() . '.' . $ext;
                $upload_path = '../../uploads/profile_images/' . $new_filename;

                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                    // อัปเดตชื่อไฟล์รูปโปรไฟล์ในฐานข้อมูล
                    $stmt = $condb->prepare("UPDATE users SET profile_image = :profile_image WHERE user_id = :user_id");
                    $stmt->execute([':profile_image' => $new_filename, ':user_id' => $user_id]);

                    // ลบรูปเก่า (ถ้ามี)
                    if (!empty($user['profile_image'])) {
                        $old_image_path = '../../uploads/profile_images/' . $user['profile_image'];
                        if (file_exists($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                }
            }

            // ล้าง CSRF token หลังจากอัปเดตสำเร็จ
            unset($_SESSION['csrf_token']);

            // แสดงข้อความสำเร็จ
            $success_message = "อัปเดตข้อมูลผู้ใช้เรียบร้อยแล้ว";
        } catch (Exception $e) {
            $error_messages[] = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $e->getMessage();
        }
    }
}

// ดึงข้อมูลทีมจากฐานข้อมูล
$stmt_teams = $condb->prepare("SELECT team_id, team_name FROM teams");
$stmt_teams->execute();
$teams = $stmt_teams->fetchAll(PDO::FETCH_ASSOC);





?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Edit Account</title>
    <?php include '../../include/header.php'; ?>
    <!-- เพิ่ม CSS สำหรับ Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    <!-- เพิ่ม SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <!-- เพิ่มลิงก์ฟอนต์ Noto Sans Thai -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <style>
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

        .card {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .form-control,
        .input-group-text {
            border-radius: 5px;
        }

        .btn-sm {
            border-radius: 5px;
        }

        .select2-container .select2-selection--single {
            height: calc(2.25rem + 2px);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: calc(2.25rem + 2px);
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(2.25rem + 2px);
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <?php include '../../include/navbar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Edit Account</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="account.php">Account</a></li>
                                <li class="breadcrumb-item active">Edit Account</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Edit Account Information</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($error_messages)): ?>
                                        <div class="alert alert-danger">
                                            <ul>
                                                <?php foreach ($error_messages as $error): ?>
                                                    <li><?php echo $error; ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($success_message)): ?>
                                        <div class="alert alert-success">
                                            <?php echo $success_message; ?>
                                        </div>
                                    <?php endif; ?>

                                    <form id="editUserForm" action="<?php echo $_SERVER['PHP_SELF'] . '?user_id=' . urlencode($encrypted_user_id); ?>" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="profile_image">Profile Image</label>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" id="profile_image" name="profile_image">
                                                            <label class="custom-file-label" for="profile_image">Choose file</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if (!empty($user['profile_image'])): ?>
                                                    <img src="../../uploads/profile_images/<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Current Profile Image" class="img-thumbnail mb-3" style="max-width: 200px;">
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="first_name">First Name<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="last_name">Last Name<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email<span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">Phone Number</label>
                                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="position">Position</label>
                                                    <input type="text" class="form-control" id="position" name="position" value="<?php echo htmlspecialchars($user['position']); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="team_id">Team<span class="text-danger">*</span></label>
                                                    <select class="form-control select2" id="team_id" name="team_id" required <?php echo ($role === 'Sale Supervisor') ? 'disabled' : ''; ?>>
                                                        <?php foreach ($teams as $team): ?>
                                                            <option value="<?php echo $team['team_id']; ?>" <?php echo ($team['team_id'] == $user['team_id']) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($team['team_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php if ($role === 'Sale Supervisor'): ?>
                                                        <input type="hidden" name="team_id" value="<?php echo $team_id; ?>">
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="role">Role<span class="text-danger">*</span></label>
                                                    <select class="form-control select2" id="role" name="role">
                                                        <option value="">Select Role</option>
                                                        <?php if ($role === 'Executive'): ?>
                                                            <option value="Executive" <?php echo htmlspecialchars($user['role'] == 'Executive') ? 'selected' : ''; ?>>Executive</option>
                                                            <option value="Sale Supervisor" <?php echo htmlspecialchars($user['role'] == 'Sale Supervisor') ? 'selected' : ''; ?>>Sale Supervisor</option>
                                                            <option value="Seller" <?php echo htmlspecialchars($user['role'] == 'Seller') ? 'selected' : ''; ?>>Seller</option>
                                                            <option value="Engineer" <?php echo htmlspecialchars($user['role'] == 'Engineer') ? 'selected' : ''; ?>>Engineer</option>
                                                        <?php elseif ($role === 'Sale Supervisor'): ?>
                                                            <option value="Seller" <?php echo htmlspecialchars($user['role'] == 'Seller') ? 'selected' : ''; ?>>Seller</option>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="company">Company</label>
                                                    <input type="text" class="form-control" id="company" name="company" value="<?php echo htmlspecialchars($user['company']); ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password">New Password (leave blank to keep current password)</label>
                                                    <input type="password" class="form-control" id="password" name="password">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">Update Account</button>
                                            </div>
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
        <?php include '../../include/footer.php'; ?>
    </div>

    <!-- Scripts -->

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script>
        $(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
            });

            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            <?php if (isset($success_message)): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '<?php echo $success_message; ?>',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'account.php';
                    }
                });
            <?php endif; ?>

            <?php if (!empty($error_messages)): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: '<?php echo implode("<br>", $error_messages); ?>',
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>