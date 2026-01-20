<?php
//session_start and Config DB
include  '../../include/Add_session.php';
include  '../../config/validation.php';

// Clear cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// ตรวจสอบว่ามีการส่ง id มาหรือไม่
if (isset($_GET['id'])) {
    $encrypted_user_id = urldecode($_GET['id']);
    $user_id = decryptUserId($encrypted_user_id);

    if ($user_id === false) {
        echo "<script>
            setTimeout(function() {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'รหัสผู้ใช้ไม่ถูกต้อง!',
                    icon: 'error',
                }).then(function() {
                    window.location.href = 'profile.php';
                });
            }, 100);
          </script>";
        exit();
    }

    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $stmt = $condb->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<script>
            setTimeout(function() {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'ไม่พบข้อมูลผู้ใช้!',
                    icon: 'error',
                }).then(function() {
                    window.location.href = 'profile.php';
                });
            }, 100);
          </script>";
        exit();
    }

    // ตรวจสอบสิทธิ์การเข้าถึง (ผู้ใช้สามารถเปลี่ยนรหัสผ่านตัวเองได้เท่านั้น หรือ Executive สามารถเปลี่ยนของคนอื่นได้)
    if ($_SESSION['user_id'] != $user_id && $_SESSION['role'] != 'Executive') {
        echo "<script>
            setTimeout(function() {
                Swal.fire({
                    title: 'ไม่มีสิทธิ์!',
                    text: 'คุณไม่มีสิทธิ์เปลี่ยนรหัสผ่านของผู้ใช้คนนี้!',
                    icon: 'warning',
                }).then(function() {
                    window.location.href = 'profile.php';
                });
            }, 100);
          </script>";
        exit();
    }

    // ตรวจสอบและทำความสะอาดข้อมูลสำหรับการแสดงผล
    $display_name = trim($user['first_name'] . ' ' . $user['last_name']);
    $display_role = trim($user['role']);

    // ตรวจสอบว่าชื่อไม่ว่างเปล่า
    if (empty($display_name) || $display_name == ' ') {
        $display_name = $user['username']; // ใช้ username แทนถ้าไม่มีชื่อ
    }

    // สร้าง CSRF Token
    $csrf_token = generateCSRFToken();

    // ตรวจสอบว่าผู้ใช้กดปุ่ม "Change Password" หรือไม่
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ตรวจสอบ CSRF Token
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'Security token invalid. Please try again.',
                        icon: 'error',
                    });
                }, 100);
              </script>";
            exit;
        }

        // ตรวจสอบ Rate Limiting สำหรับการเปลี่ยนรหัสผ่าน
        $clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $rateCheck = checkRateLimit('password_change_' . $clientIP, 3, 900); // 3 ครั้งใน 15 นาที

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

        // รับและตรวจสอบข้อมูลจากฟอร์ม
        $validationErrors = [];

        $current_password = sanitizeInput($_POST['current_password'] ?? '');
        if (empty($current_password)) {
            $validationErrors[] = 'กรุณาป้อนรหัสผ่านปัจจุบัน';
        }

        // ตรวจสอบรหัสผ่านใหม่
        $passwordValidation = validatePassword($_POST['new_password'] ?? '');
        if (!$passwordValidation['valid']) {
            $validationErrors[] = $passwordValidation['message'];
        } else {
            $new_password = $passwordValidation['value'];
        }

        $confirm_password = sanitizeInput($_POST['confirm_password'] ?? '');
        if (empty($confirm_password)) {
            $validationErrors[] = 'กรุณายืนยันรหัสผ่านใหม่';
        }

        // ถ้ามี validation errors แสดงข้อความแจ้งเตือน
        if (!empty($validationErrors)) {
            $errorMessage = implode('\\n', $validationErrors);
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'ข้อมูลไม่ถูกต้อง!',
                        text: '" . $errorMessage . "',
                        icon: 'warning',
                    });
                }, 100);
              </script>";
            exit;
        }

        // ตรวจสอบว่ารหัสผ่านปัจจุบันถูกต้องหรือไม่
        if (password_verify($current_password, $user['password'])) {
            // ตรวจสอบว่ารหัสผ่านใหม่และการยืนยันรหัสผ่านตรงกันหรือไม่
            if ($new_password === $confirm_password) {
                // รหัสผ่านถูกตรวจสอบความแข็งแกร่งแล้วใน validatePassword function
                {
                    // อัปเดตรหัสผ่านใหม่ลงในฐานข้อมูล
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_stmt = $condb->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
                    $update_stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                    $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    
                    if ($update_stmt->execute()) {
                        // ถ้าเป็นการเปลี่ยนรหัสผ่านตัวเอง ให้ logout
                        if ($_SESSION['user_id'] == $user_id) {
                            session_destroy();
                            echo "<script>
                                setTimeout(function() {
                                    Swal.fire({
                                        title: 'สำเร็จ!',
                                        text: 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว! กรุณาเข้าสู่ระบบใหม่',
                                        icon: 'success',
                                        confirmButtonText: 'ตกลง'
                                    }).then(function() {
                                        window.location.href = '../../login.php';
                                    });
                                }, 100);
                              </script>";
                        } else {
                            // ถ้าเป็น Executive เปลี่ยนให้คนอื่น
                            echo "<script>
                                setTimeout(function() {
                                    Swal.fire({
                                        title: 'สำเร็จ!',
                                        text: 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว!',
                                        icon: 'success',
                                        confirmButtonText: 'ตกลง'
                                    }).then(function() {
                                        window.location.href = 'profile.php';
                                    });
                                }, 100);
                              </script>";
                        }
                    } else {
                        echo "<script>
                            setTimeout(function() {
                                Swal.fire({
                                    title: 'เกิดข้อผิดพลาด!',
                                    text: 'ไม่สามารถอัปเดตรหัสผ่านได้!',
                                    icon: 'error',
                                });
                            }, 100);
                          </script>";
                    }
                }
            } else {
                echo "<script>
                    setTimeout(function() {
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด!',
                            text: 'รหัสผ่านใหม่ไม่ตรงกัน!',
                            icon: 'warning',
                        });
                    }, 100);
                  </script>";
            }
        } else {
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'รหัสผ่านปัจจุบันไม่ถูกต้อง!',
                        icon: 'error',
                    });
                }, 100);
              </script>";
        }
    }
} else {
    echo "<script>
        setTimeout(function() {
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่ได้ระบุผู้ใช้ที่ต้องการเปลี่ยนรหัสผ่าน!',
                icon: 'warning',
            }).then(function() {
                window.location.href = 'profile.php';
            });
        }, 100);
      </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "profile"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Change Password</title>
    <?php include  '../../include/header.php'; ?>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .profile-card {
            max-width: 400px;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-header {
            background: #3498db;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid white;
            margin-bottom: 10px;
        }

        .profile-name {
            font-size: 1.5em;
            margin: 0;
        }

        .profile-role {
            font-size: 1em;
            opacity: 0.8;
            margin: 5px 0 0;
        }

        .profile-info {
            padding: 20px;
        }

        .info-item {
            margin-bottom: 10px;
            font-size: 0.9em;
        }

        .info-label {
            font-weight: bold;
            color: #555;
            width: 80px;
            display: inline-block;
        }

        .profile-actions {
            padding: 0 20px 20px;
            text-align: center;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-password {
            background-color: #e74c3c;
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            margin-left: 10px;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* Password Toggle Styles */
        .password-toggle {
            cursor: pointer;
            user-select: none;
        }
        
        .password-toggle:hover {
            color: #007bff;
        }

        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
            font-family: monospace;
            font-size: 12px;
        }

        .permission-info {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
            font-size: 14px;
            color: #155724;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include  '../../include/navbar.php'; ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Change Password</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="profile.php">Profile</a></li>
                                <li class="breadcrumb-item active">Change Password</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Debug Information (เปิดใช้เมื่อต้องการ debug) -->
                    <?php if (false): // เปลี่ยนเป็น true เมื่อต้องการ debug ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="debug-info">
                                <strong>Debug Information:</strong><br>
                                URL ID: <?php echo isset($_GET['id']) ? $_GET['id'] : 'Not set'; ?><br>
                                Decrypted User ID: <?php echo $user_id; ?><br>
                                Session User ID: <?php echo $_SESSION['user_id']; ?><br>
                                Session Role: <?php echo $_SESSION['role']; ?><br>
                                Target User: <?php echo htmlspecialchars($user['username']); ?><br>
                                Target First Name: <?php echo htmlspecialchars($user['first_name']); ?><br>
                                Target Last Name: <?php echo htmlspecialchars($user['last_name']); ?><br>
                                Display Name: <?php echo htmlspecialchars($display_name); ?><br>
                                Target Role: <?php echo htmlspecialchars($display_role); ?><br>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Permission Information -->
                    <?php if ($_SESSION['user_id'] != $user_id): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="permission-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>หมายเหตุ:</strong> คุณกำลังเปลี่ยนรหัสผ่านของผู้ใช้: <strong><?php echo escapeOutput($display_name); ?></strong>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="profile-card">
                                <div class="profile-header">
                                    <?php 
                                    // ตรวจสอบรูปโปรไฟล์
                                    $profile_image_path = !empty($user['profile_image']) ?
                                        BASE_URL . 'uploads/profile_images/' . escapeOutput($user['profile_image']) :
                                        '../../assets/img/add.jpg';
                                    ?>
                                    <img src="<?php echo $profile_image_path; ?>" alt="Profile Picture" class="profile-img">
                                    <h2 class="profile-name"><?php echo escapeOutput($display_name); ?></h2>
                                    <p class="profile-role"><?php echo escapeOutput($display_role); ?></p>
                                </div>
                                <div class="profile-info">
                                    <form method="POST" action="" onsubmit="return validatePassword()">
                                        <!-- CSRF Token -->
                                        <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrf_token); ?>">

                                        <div class="form-group">
                                            <label>Current Password</label>
                                            <div class="input-group mb-3">
                                                <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Current Password" required>
                                                <div class="input-group-append">
                                                    <div class="input-group-text password-toggle" onclick="togglePassword('current_password')">
                                                        <span id="current_password_icon" class="fas fa-eye-slash"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>New Password</label>
                                            <div class="input-group mb-3">
                                                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password (อย่างน้อย 6 ตัวอักษร)" required minlength="6">
                                                <div class="input-group-append">
                                                    <div class="input-group-text password-toggle" onclick="togglePassword('new_password')">
                                                        <span id="new_password_icon" class="fas fa-eye-slash"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Confirm New Password</label>
                                            <div class="input-group mb-3">
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required minlength="6">
                                                <div class="input-group-append">
                                                    <div class="input-group-text password-toggle" onclick="togglePassword('confirm_password')">
                                                        <span id="confirm_password_icon" class="fas fa-eye-slash"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="profile-actions">
                                            <button type="submit" class="btn btn-password">Change Password</button>
                                            <a href="profile.php" class="btn btn-secondary">Back to Profile</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include  '../../include/footer.php'; ?>
    </div>

    <!-- JavaScript สำหรับการตรวจสอบรหัสผ่าน -->
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

        // ฟังก์ชันตรวจสอบรหัสผ่าน
        function validatePassword() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword.length < 6) {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร!',
                    icon: 'warning',
                });
                return false;
            }
            
            if (newPassword !== confirmPassword) {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'รหัสผ่านใหม่ไม่ตรงกัน!',
                    icon: 'warning',
                });
                return false;
            }
            
            return true;
        }
    </script>
</body>

</html>