<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';
include '../../config/validation.php';

// ดึงข้อมูลผู้ใช้จาก session
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// สร้าง CSRF Token
$csrf_token = generateCSRFToken();

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$stmt = $condb->prepare("SELECT * FROM users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("ไม่พบข้อมูลผู้ใช้");
}

// ดึงข้อมูลทีมที่ผู้ใช้สังกัดอยู่
$stmt_user_teams = $condb->prepare("
    SELECT t.team_id, t.team_name, ut.is_primary
    FROM user_teams ut
    JOIN teams t ON ut.team_id = t.team_id
    WHERE ut.user_id = :user_id
    ORDER BY ut.is_primary DESC, t.team_name ASC
");
$stmt_user_teams->bindParam(':user_id', $user_id, PDO::PARAM_STR);
$stmt_user_teams->execute();
$user_teams = $stmt_user_teams->fetchAll(PDO::FETCH_ASSOC);

$error_messages = [];
$rateLimitError = null;

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
                    window.location.href = 'edit_profile.php';
                });
            }, 100);
          </script>";
        exit;
    }

    // ตรวจสอบ Rate Limiting สำหรับการแก้ไขข้อมูล
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $rateCheck = checkRateLimit('edit_profile_' . $clientIP, 5, 600); // 5 ครั้งใน 10 นาที

    $rateLimitError = null;
    if (!$rateCheck['allowed']) {
        $rateLimitError = $rateCheck;
    } else {
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
        $positionInput = $_POST['position'] ?? '';
        if (trim($positionInput) !== '') {
            $positionValidation = validateText($positionInput, 1, 100, 'ตำแหน่ง');
            if (!$positionValidation['valid']) {
                $validationErrors[] = $positionValidation['message'];
            } else {
                $position = $positionValidation['value'];
            }
        } else {
            $position = '';
        }

        // ตรวจสอบ company (1-500 ตัวอักษร)
        $companyValidation = validateText($_POST['company'] ?? '', 1, 500, 'บริษัท');
        if (!$companyValidation['valid']) {
            $validationErrors[] = $companyValidation['message'];
        } else {
            $company = $companyValidation['value'];
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
                        company = :company";

                $params = [
                    ':first_name' => $first_name,
                    ':last_name' => $last_name,
                    ':email' => $email,
                    ':phone' => $phone,
                    ':position' => $position,
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

                // อัปเดตรูปโปรไฟล์ (ถ้ามี) ด้วย validateUploadedFile
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                    $fileValidation = validateUploadedFile($_FILES['profile_image'], ['jpg', 'jpeg', 'png', 'gif'], 5242880); // 5MB

                    if (!$fileValidation['valid']) {
                        throw new Exception($fileValidation['message']);
                    }

                    $uploadedFile = $fileValidation['file'];

                    $uploadDir = __DIR__ . '/../../uploads/profile_images';
                    if (!is_dir($uploadDir)) {
                        if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
                            throw new Exception('ไม่สามารถสร้างโฟลเดอร์อัปโหลดได้');
                        }
                    }

                    $originalName = $uploadedFile['name'] ?? '';
                    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
                    $sanitizedBase = sanitizeFilename($baseName ?: 'profile_image');
                    if ($sanitizedBase === '') {
                        $sanitizedBase = 'profile_image';
                    }

                    $new_filename = $sanitizedBase . '_' . $user_id . '_' . time();
                    if (!empty($extension)) {
                        $new_filename .= '.' . $extension;
                    }

                    $upload_path = $uploadDir . '/' . $new_filename;

                    if (!move_uploaded_file($uploadedFile['tmp_name'], $upload_path)) {
                        throw new Exception('ไม่สามารถอัปโหลดไฟล์ได้');
                    }

                    $stmt = $condb->prepare("UPDATE users SET profile_image = :profile_image WHERE user_id = :user_id");
                    $stmt->execute([':profile_image' => $new_filename, ':user_id' => $user_id]);

                    // อัปเดต session
                    $_SESSION['profile_image'] = $new_filename;

                    if (!empty($user['profile_image'])) {
                        $old_image_path = $uploadDir . '/' . $user['profile_image'];
                        if (is_file($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                }

                // อัปเดต session ด้วยข้อมูลใหม่
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;

                echo "<script>
                    setTimeout(function() {
                        Swal.fire({
                            title: 'สำเร็จ!',
                            text: 'อัปเดตข้อมูลโปรไฟล์เรียบร้อยแล้ว',
                            icon: 'success',
                        }).then(function() {
                            window.location.href = 'edit_profile.php';
                        });
                    }, 100);
                  </script>";
                exit;
            } catch (Exception $e) {
                $error_messages[] = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | แก้ไขโปรไฟล์</title>
    <?php include '../../include/header.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            font-family: 'Sarabun', sans-serif;
        }

        .profile-image-container {
            position: relative;
            display: inline-block;
        }

        .profile-image-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
        }

        .info-badge {
            display: inline-block;
            padding: 5px 12px;
            margin: 2px;
            border-radius: 15px;
            font-size: 0.9em;
            font-weight: 500;
        }

        .info-badge.primary {
            background-color: #007bff;
            color: white;
        }

        .info-badge.secondary {
            background-color: #6c757d;
            color: white;
        }

        .role-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
        }

        .role-executive {
            background-color: #dc3545;
            color: white;
        }

        .role-account {
            background-color: #28a745;
            color: white;
        }

        .role-supervisor {
            background-color: #17a2b8;
            color: white;
        }

        .role-seller {
            background-color: #ffc107;
            color: #212529;
        }

        .role-engineer {
            background-color: #6c757d;
            color: white;
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
                            <h1>แก้ไขโปรไฟล์</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../../index.php">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">แก้ไขโปรไฟล์</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3">
                            <!-- Profile Image Card -->
                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <?php if (!empty($user['profile_image'])): ?>
                                            <img class="profile-image-preview" src="../../uploads/profile_images/<?php echo escapeOutput($user['profile_image']); ?>" alt="รูปโปรไฟล์">
                                        <?php else: ?>
                                            <img class="profile-image-preview" src="../../AdminLTE/dist/img/avatar5.png" alt="Default Avatar">
                                        <?php endif; ?>
                                    </div>

                                    <h3 class="profile-username text-center mt-3">
                                        <?php echo escapeOutput($user['first_name'] . ' ' . $user['last_name']); ?>
                                    </h3>

                                    <p class="text-muted text-center">
                                        <span class="role-badge role-<?php echo strtolower(str_replace(' ', '', $user['role'])); ?>">
                                            <?php echo escapeOutput($user['role']); ?>
                                        </span>
                                    </p>

                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>ทีม</b>
                                            <div class="mt-2">
                                                <?php foreach ($user_teams as $team): ?>
                                                    <span class="info-badge <?php echo ($team['is_primary'] == 1) ? 'primary' : 'secondary'; ?>">
                                                        <?php echo escapeOutput($team['team_name']); ?>
                                                        <?php echo ($team['is_primary'] == 1) ? ' ⭐' : ''; ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <b>อีเมล</b>
                                            <a class="float-right"><?php echo escapeOutput($user['email']); ?></a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>เบอร์โทร</b>
                                            <a class="float-right"><?php echo escapeOutput($user['phone']); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header p-2">
                                    <h3 class="card-title">แก้ไขข้อมูลส่วนตัว</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($error_messages)): ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php foreach ($error_messages as $error): ?>
                                                    <li><?php echo $error; ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <form id="editProfileForm" action="edit_profile.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrf_token); ?>">

                                        <div class="form-group">
                                            <label for="profile_image">รูปโปรไฟล์</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="profile_image" name="profile_image" accept="image/*">
                                                <label class="custom-file-label" for="profile_image">เลือกรูปภาพ</label>
                                            </div>
                                            <small class="form-text text-muted">รองรับไฟล์: JPG, JPEG, PNG, GIF (สูงสุด 5MB)</small>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="first_name">ชื่อ<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo escapeOutput($user['first_name']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="last_name">นามสกุล<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo escapeOutput($user['last_name']); ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="email">อีเมล<span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo escapeOutput($user['email']); ?>" required>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">เบอร์โทรศัพท์</label>
                                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo escapeOutput($user['phone']); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="position">ตำแหน่ง</label>
                                                    <input type="text" class="form-control" id="position" name="position" value="<?php echo escapeOutput($user['position']); ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="company">บริษัท</label>
                                            <input type="text" class="form-control" id="company" name="company" value="<?php echo escapeOutput($user['company']); ?>">
                                        </div>

                                        <hr>

                                        <h5 class="mb-3">เปลี่ยนรหัสผ่าน</h5>

                                        <div class="form-group">
                                            <label for="password">รหัสผ่านใหม่ (เว้นว่างไว้หากไม่ต้องการเปลี่ยน)</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="password" name="password" placeholder="กรอกรหัสผ่านใหม่ (อย่างน้อย 8 ตัวอักษร)">
                                                <div class="input-group-append">
                                                    <div class="input-group-text password-toggle" onclick="togglePassword('password')" style="cursor: pointer;">
                                                        <span id="password_icon" class="fas fa-eye-slash"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">
                                                รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร ประกอบด้วยตัวอักษรพิมพ์ใหญ่ พิมพ์เล็ก ตัวเลข และอักขระพิเศษอย่างน้อย 1 ตัว
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                                            </button>
                                            <a href="../../index.php" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> ยกเลิก
                                            </a>
                                        </div>
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
            // Custom file input label
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            <?php if (!empty($rateLimitError)): ?>
            (function() {
                var remaining = <?php echo max(1, (int)ceil($rateLimitError['retry_after'] ?? 0)); ?>;
                var message = '<?php echo addslashes($rateLimitError['message']); ?>';
                var detail = '<?php echo addslashes($rateLimitError['details'] ?? ''); ?>';
                var countdownInterval = null;

                function formatCountdown(seconds) {
                    var mins = Math.floor(seconds / 60);
                    var secs = seconds % 60;
                    var parts = [];
                    if (mins > 0) {
                        parts.push(mins + ' นาที');
                    }
                    parts.push(secs + ' วินาที');
                    return parts.join(' ');
                }

                var htmlText = message + '<br>กรุณารอ <span id="rate-limit-countdown"></span> ก่อนลองใหม่';
                if (detail) {
                    htmlText += '<br><small class="text-muted">เหตุผล: ' + detail + '</small>';
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'ถูกบล็อก!',
                    html: htmlText,
                    allowOutsideClick: true,
                    allowEscapeKey: true,
                    showConfirmButton: true,
                    confirmButtonText: 'ปิด',
                    didOpen: function() {
                        var countdownEl = Swal.getHtmlContainer().querySelector('#rate-limit-countdown');
                        countdownEl.textContent = formatCountdown(remaining);
                        countdownInterval = setInterval(function() {
                            remaining--;
                            if (remaining <= 0) {
                                clearInterval(countdownInterval);
                                countdownEl.textContent = '0 วินาที';
                                Swal.update({
                                    confirmButtonText: 'ลองใหม่',
                                });
                            } else {
                                countdownEl.textContent = formatCountdown(remaining);
                            }
                        }, 1000);
                    },
                    willClose: function() {
                        if (countdownInterval) {
                            clearInterval(countdownInterval);
                        }
                    }
                }).then(function(result) {
                    if (remaining <= 0 && result.isConfirmed) {
                        window.location.reload();
                    }
                });
            })();
            <?php endif; ?>

            // Form validation
            $('#editProfileForm').on('submit', function(e) {
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
                var emailVal = $('#email').val().trim();
                if (emailVal === '') {
                    isValid = false;
                    errorMessage += 'กรุณากรอกอีเมล<br>';
                } else {
                    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(emailVal)) {
                        isValid = false;
                        errorMessage += 'รูปแบบอีเมลไม่ถูกต้อง<br>';
                    }
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
