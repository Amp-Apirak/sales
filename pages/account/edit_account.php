<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';
include '../../config/validation.php';

// ตรวจสอบสิทธิ์การเข้าถึง
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$created_by = $_SESSION['user_id'];

// ตรวจสอบสิทธิ์การเข้าถึงหน้านี้
// if (!in_array($role, ['Executive', 'Sale Supervisor'])) {
//     header("Location: unauthorized.php");
//     exit();
// }

// สร้าง CSRF Token
$csrf_token = generateCSRFToken();

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

    // ดึงข้อมูลทีมที่ผู้ใช้สังกัดอยู่
    $stmt_user_teams = $condb->prepare("SELECT team_id FROM user_teams WHERE user_id = :user_id");
    $stmt_user_teams->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $stmt_user_teams->execute();
    $user_teams = $stmt_user_teams->fetchAll(PDO::FETCH_COLUMN, 0);

    if (!$user) {
        die("ไม่พบข้อมูลผู้ใช้");
    }

    // ตรวจสอบสิทธิ์ในการแก้ไขข้อมูล
    if ($role === 'Sale Supervisor') {
        if ($user['user_id'] === $_SESSION['user_id']) {
            // Sale Supervisor สามารถแก้ไขบัญชีของตัวเองได้
        } elseif ($user['role'] === 'Sale Supervisor' || $user['role'] === 'Executive') {
            // Sale Supervisor ไม่สามารถแก้ไขบัญชีของ Sale Supervisor คนอื่นหรือ Executive ได้
            $error_message = "คุณไม่มีสิทธิ์ในการแก้ไขข้อมูลของ " . $user['role'];
        } elseif ($user['role'] === 'Seller' && $user['team_id'] != $team_id) {
            // Sale Supervisor ไม่สามารถแก้ไขบัญชี Seller ที่ไม่ได้อยู่ในทีมของตัวเอง
            $error_message = "คุณไม่มีสิทธิ์ในการแก้ไขข้อมูลของ Seller ที่ไม่ได้อยู่ในทีมของคุณ";
        }
    } elseif ($role === 'Executive') {
        // Executive สามารถแก้ไขได้ทุกบัญชี ไม่ต้องมีการตรวจสอบเพิ่มเติม
    } elseif ($role === 'Seller' || $role === 'Engineer') {
        if ($user['user_id'] !== $_SESSION['user_id']) {
            // Seller และ Engineer สามารถแก้ไขได้เฉพาะบัญชีของตัวเองเท่านั้น
            $error_message = "คุณสามารถแก้ไขได้เฉพาะข้อมูลของตัวเองเท่านั้น";
        }
    } else {
        // บทบาทอื่นๆ ไม่มีสิทธิ์แก้ไขข้อมูล
        $error_message = "คุณไม่มีสิทธิ์ในการแก้ไขข้อมูลนี้";
    }

    // ถ้ามีข้อความแจ้งเตือนข้อผิดพลาด ให้แสดง SweetAlert และเด้งกลับไปหน้า account.php
    if (isset($error_message)) {
?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่มีสิทธิ์เข้าถึง',
                    text: '<?php echo $error_message; ?>',
                    confirmButtonText: 'ตกลง'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'account.php';
                    }
                });
            });
        </script>
<?php
        exit();
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
    // Executive จะเห็นทุกทีม
    $stmt_teams = $condb->prepare("SELECT team_id, team_name FROM teams");
}
$stmt_teams->execute();
$teams = $stmt_teams->fetchAll(PDO::FETCH_ASSOC);

$error_messages = [];

// ตรวจสอบการส่งฟอร์มแบบ POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบ CSRF Token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        echo "<script>
            setTimeout(function() {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'Security token invalid. Please try again.',
                    icon: 'error',
                }).then(function() {
                    window.location.href = 'account.php';
                });
            }, 100);
          </script>";
        exit;
    }

    // ตรวจสอบ Rate Limiting สำหรับการแก้ไขข้อมูล
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $rateCheck = checkRateLimit('edit_account_' . $clientIP, 5, 600); // 5 ครั้งใน 10 นาที

    if (!$rateCheck['allowed']) {
        echo "<script>
            setTimeout(function() {
                Swal.fire({
                    title: 'ถูกบล็อก!',
                    text: '" . $rateCheck['message'] . "',
                    icon: 'warning',
                });
            }, 100);
          </script>";
        exit;
    }

    // รับและตรวจสอบข้อมูลจากฟอร์มด้วย validation functions
    $validationErrors = [];

    // ตรวจสอบ first_name
    $firstNameValidation = validateText($_POST['first_name'] ?? '', 2, 50, 'ชื่อ');
    if (!$firstNameValidation['valid']) {
        $validationErrors[] = $firstNameValidation['message'];
    } else {
        $first_name = $firstNameValidation['value'];
    }

    // ตรวจสอบ last_name
    $lastNameValidation = validateText($_POST['last_name'] ?? '', 2, 50, 'นามสกุล');
    if (!$lastNameValidation['valid']) {
        $validationErrors[] = $lastNameValidation['message'];
    } else {
        $last_name = $lastNameValidation['value'];
    }

    // ตรวจสอบ email
    $emailValidation = validateEmail($_POST['email'] ?? '');
    if (!$emailValidation['valid']) {
        $validationErrors[] = $emailValidation['message'];
    } else {
        $email = $emailValidation['value'];
    }

    // ตรวจสอบ phone
    $phoneValidation = validatePhone($_POST['phone'] ?? '');
    if (!$phoneValidation['valid']) {
        $validationErrors[] = $phoneValidation['message'];
    } else {
        $phone = $phoneValidation['value'];
    }

    // ตรวจสอบ position
    $positionValidation = validateText($_POST['position'] ?? '', 2, 100, 'ตำแหน่ง');
    if (!$positionValidation['valid']) {
        $validationErrors[] = $positionValidation['message'];
    } else {
        $position = $positionValidation['value'];
    }

    // ตรวจสอบ company
    $companyValidation = validateText($_POST['company'] ?? '', 1, 100, 'บริษัท');
    if (!$companyValidation['valid']) {
        $validationErrors[] = $companyValidation['message'];
    } else {
        $company = $companyValidation['value'];
    }

    $team_ids = $_POST['team_ids'] ?? [];
    $role_new = sanitizeInput($_POST['role'] ?? '');

    if (empty($team_ids)) {
        $validationErrors[] = 'กรุณาเลือกอย่างน้อยหนึ่งทีม';
    }
    if (empty($role_new)) {
        $validationErrors[] = 'กรุณาเลือกบทบาท';
    }

    // ตรวจสอบรหัสผ่าน (ถ้ามีการเปลี่ยนแปลง)
    $password = '';
    if (!empty($_POST['password'])) {
        $passwordValidation = validatePassword($_POST['password']);
        if (!$passwordValidation['valid']) {
            $validationErrors[] = $passwordValidation['message'];
        } else {
            $password = $passwordValidation['value'];
        }
    }

    // รวม validation errors เข้ากับ error_messages
    $error_messages = array_merge($error_messages, $validationErrors);

    // ถ้าไม่มีข้อผิดพลาด ดำเนินการอัปเดตข้อมูลผู้ใช้
    if (empty($error_messages)) {
        try {
            $sql = "UPDATE users SET 
                    first_name = :first_name, 
                    last_name = :last_name, 
                    email = :email, 
                    phone = :phone, 
                    position = :position, 
                    role = :role, 
                    company = :company";

            $params = [
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':email' => $email,
                ':phone' => $phone,
                ':position' => $position,
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

            // --- จัดการข้อมูลใน user_teams ---
            // 1. ลบข้อมูลทีมเดิมทั้งหมดของ user นี้
            $stmt_delete_teams = $condb->prepare("DELETE FROM user_teams WHERE user_id = :user_id");
            $stmt_delete_teams->execute([':user_id' => $user_id]);

            // 2. เพิ่มข้อมูลทีมใหม่ที่เลือก
            $sql_user_teams = "INSERT INTO user_teams (user_id, team_id, is_primary) VALUES (:user_id, :team_id, :is_primary)";
            $stmt_user_teams = $condb->prepare($sql_user_teams);

            foreach ($team_ids as $index => $team_id) {
                $is_primary = ($index === 0) ? 1 : 0; // กำหนดให้ทีมแรกที่เลือกเป็นทีมหลัก
                $stmt_user_teams->execute([
                    ':user_id' => $user_id,
                    ':team_id' => $team_id,
                    ':is_primary' => $is_primary
                ]);
            }

            // อัปเดตรูปโปรไฟล์ (ถ้ามี) ด้วย validateUploadedFile
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $fileValidation = validateUploadedFile($_FILES['profile_image'], ['jpg', 'jpeg', 'png', 'gif'], 5242880); // 5MB

                if (!$fileValidation['valid']) {
                    throw new Exception($fileValidation['message']);
                } else {
                    $new_filename = sanitizeFilename($fileValidation['safe_name']);
                    $upload_path = '../../uploads/profile_images/' . $new_filename;

                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                        $stmt = $condb->prepare("UPDATE users SET profile_image = :profile_image WHERE user_id = :user_id");
                        $stmt->execute([':profile_image' => $new_filename, ':user_id' => $user_id]);

                        if (!empty($user['profile_image'])) {
                            $old_image_path = '../../uploads/profile_images/' . $user['profile_image'];
                            if (file_exists($old_image_path)) {
                                unlink($old_image_path);
                            }
                        }
                    }
                }
            }

            // CSRF token จะถูกจัดการโดย generateCSRFToken() อัตโนมัติ

            $success_message = "อัปเดตข้อมูลผู้ใช้เรียบร้อยแล้ว";
        } catch (Exception $e) {
            $error_messages[] = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | แก้ไขบัญชีผู้ใช้</title>
    <?php include '../../include/header.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
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
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>แก้ไขบัญชีผู้ใช้</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">แก้ไขบัญชีผู้ใช้</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">แก้ไขข้อมูลบัญชีผู้ใช้</h3>
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
                                        <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrf_token); ?>">

                                        <div class="form-group">
                                            <label for="profile_image">รูปโปรไฟล์</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="profile_image" name="profile_image">
                                                <label class="custom-file-label" for="profile_image">เลือกไฟล์</label>
                                            </div>
                                            <?php if (!empty($user['profile_image'])): ?>
                                                <img src="../../uploads/profile_images/<?php echo escapeOutput($user['profile_image']); ?>" alt="รูปโปรไฟล์ปัจจุบัน" class="img-thumbnail mt-2" style="max-width: 200px;">
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group">
                                            <label for="first_name">ชื่อ<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo escapeOutput($user['first_name']); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="last_name">นามสกุล<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo escapeOutput($user['last_name']); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="email">อีเมล<span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo escapeOutput($user['email']); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="phone">เบอร์โทรศัพท์</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo escapeOutput($user['phone']); ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="position">ตำแหน่ง</label>
                                            <input type="text" class="form-control" id="position" name="position" value="<?php echo escapeOutput($user['position']); ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="team_ids">ทีม<span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="team_ids" name="team_ids[]" multiple="multiple" data-placeholder="เลือกทีม" style="width: 100%;" required <?php echo ($role === 'Sale Supervisor') ? 'disabled' : ''; ?>>
                                                <?php foreach ($teams as $team): ?>
                                                    <option value="<?php echo $team['team_id']; ?>" <?php echo in_array($team['team_id'], $user_teams) ? 'selected' : ''; ?>>
                                                        <?php echo escapeOutput($team['team_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if ($role === 'Sale Supervisor'): ?>
                                                <!-- For disabled multi-select, send the values as hidden inputs -->
                                                <?php foreach ($user_teams as $user_team_id): ?>
                                                    <input type="hidden" name="team_ids[]" value="<?php echo $user_team_id; ?>">
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group">
                                            <label for="role">บทบาท<span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="role" name="role" required <?php echo ($role === 'Sale Supervisor') ? 'disabled' : ''; ?>>
                                                <?php if ($role === 'Executive'): ?>
                                                    <option value="Executive" <?php echo ($user['role'] == 'Executive') ? 'selected' : ''; ?>>Executive</option>
                                                    <option value="Sale Supervisor" <?php echo ($user['role'] == 'Sale Supervisor') ? 'selected' : ''; ?>>Sale Supervisor</option>
                                                    <option value="Seller" <?php echo ($user['role'] == 'Seller') ? 'selected' : ''; ?>>Seller</option>
                                                    <option value="Engineer" <?php echo ($user['role'] == 'Engineer') ? 'selected' : ''; ?>>Engineer</option>
                                                <?php elseif ($role === 'Sale Supervisor'): ?>
                                                    <option value="Seller" selected>Seller</option>
                                                <?php elseif ($role === 'Seller'): ?>
                                                    <option value="Seller" selected>Seller</option>
                                                <?php elseif ($role === 'Engineer'): ?>
                                                    <option value="Engineer" selected>Engineer</option>

                                                <?php endif; ?>
                                            </select>
                                            <?php if ($role === 'Sale Supervisor'): ?>
                                                <input type="hidden" name="role" value="Seller">
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group">
                                            <label for="company">บริษัท</label>
                                            <input type="text" class="form-control" id="company" name="company" value="<?php echo escapeOutput($user['company']); ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="password">รหัสผ่านใหม่ (เว้นว่างไว้หากไม่ต้องการเปลี่ยน)</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="password" name="password" placeholder="กรอกรหัสผ่านใหม่ (อย่างน้อย 8 ตัวอักษร)">
                                                <div class="input-group-append">
                                                    <div class="input-group-text password-toggle" onclick="togglePassword('password')">
                                                        <span id="password_icon" class="fas fa-eye-slash"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">
                                                รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร ประกอบด้วยตัวอักษรพิมพ์ใหญ่ พิมพ์เล็ก ตัวเลข และอักขระพิเศษอย่างน้อย 1 ตัว
                                            </small>
                                        </div>

                                        <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include '../../include/footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script>
        // ฟังก์ชันสำหรับแสดง/ซ่อนรหัสผ่าน
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const passwordIcon = document.getElementById(fieldId + '_icon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordIcon.className = 'fas fa-eye';
            } else {
                passwordField.type = 'password';
                passwordIcon.className = 'fas fa-eye-slash';
            }
        }

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
                    title: 'สำเร็จ',
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
                    title: 'เกิดข้อผิดพลาด',
                    html: '<?php echo implode("<br>", $error_messages); ?>',
                });
            <?php endif; ?>

            $('#editUserForm').on('submit', function(e) {
                var isValid = true;
                var errorMessage = '';

                if ($('#first_name').val().trim() === '') {
                    isValid = false;
                    errorMessage += 'กรุณากรอกชื่อ<br>';
                }
                if ($('#last_name').val().trim() === '') {
                    isValid = false;
                    errorMessage += 'กรุณากรอกนามสกุล<br>';
                }
                if ($('#email').val().trim() === '') {
                    isValid = false;
                    errorMessage += 'กรุณากรอกอีเมล<br>';
                }
                if ($('#team_id').val() === '') {
                    isValid = false;
                    errorMessage += 'กรุณาเลือกทีม<br>';
                }
                if ($('#role').val() === '') {
                    isValid = false;
                    errorMessage += 'กรุณาเลือกบทบาท<br>';
                }

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                        html: errorMessage,
                    });
                }
            });
        });
    </script>
</body>

</html>