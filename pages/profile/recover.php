<?php
//session_start and Config DB
include  '../../include/Add_session.php';

// ตรวจสอบว่ามี user_id ใน session หรือไม่
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $stmt = $condb->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "ไม่พบข้อมูลผู้ใช้";
        exit();
    }

    // ตรวจสอบว่าผู้ใช้กดปุ่ม "Change Password" หรือไม่
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // ตรวจสอบว่ารหัสผ่านปัจจุบันถูกต้องหรือไม่
        if (password_verify($current_password, $user['password'])) {
            // ตรวจสอบว่ารหัสผ่านใหม่และการยืนยันรหัสผ่านตรงกันหรือไม่
            if ($new_password === $confirm_password) {
                // อัปเดตรหัสผ่านใหม่ลงในฐานข้อมูล
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $condb->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
                $update_stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $update_stmt->execute();

                session_destroy();
                echo "<script>
                    setTimeout(function() {
                        Swal.fire({
                            title: 'สำเร็จ!',
                            text: 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว!',
                            icon: 'success',
                            confirmButtonText: 'ตกลง'
                        }).then(function() {
                            window.location.href = '../../login.php';
                        });
                    }, 100);
                  </script>";
            } else {
                echo "<script>
                    setTimeout(function() {
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด!',
                            text: 'รหัสผ่านใหม่ไม่ตรงกัน!',
                            icon: 'warning',
                        }).then(function() {
                            window.location.href = 'recover.php';
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
                }).then(function() {
                    window.location.href = 'recover.php';
                });
            }, 100);
          </script>";
        }
    }
} else {
    echo "ผู้ใช้ไม่ได้ล็อกอิน";
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
        }

        .btn-password {
            background-color: #e74c3c;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
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
                                <li class="breadcrumb-item active">Change Password</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="profile-card">
                                <div class="profile-header">
                                    <img src="../../assets/img/add.jpg" alt="Profile Picture" class="profile-img">
                                    <h2 class="profile-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                                    <p class="profile-role"><?php echo htmlspecialchars($user['role']); ?></p>
                                </div>
                                <div class="profile-info">
                                    <form method="POST" action="#">
                                        <div class="form-group">
                                            <label>Current Password</label>
                                            <div class="input-group mb-3">
                                                <input type="password" class="form-control" name="current_password" placeholder="Current Password" required>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-lock"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>New Password</label>
                                            <div class="input-group mb-3">
                                                <input type="password" class="form-control" name="new_password" placeholder="New Password" required>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-lock"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Confirm New Password</label>
                                            <div class="input-group mb-3">
                                                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-lock"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="profile-actions">
                                            <button type="submit" class="btn btn-password">Change Password</button>
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
</body>

</html>
