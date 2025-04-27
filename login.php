<?php
session_start();
require 'config/condb.php'; // นำเข้าการเชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการส่งข้อมูลผ่าน POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // เตรียมคำสั่ง SQL สำหรับการดึงข้อมูลผู้ใช้
    $stmt = $condb->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    // แก้ไขส่วนการ query และการเก็บค่าใน session
    if ($user && password_verify($password, $user['password'])) {

        // ดึงข้อมูล team_name จากตาราง teams
        try {
            $stmt = $condb->prepare("
            SELECT t.team_name 
            FROM teams t 
            INNER JOIN users u ON u.team_id = t.team_id 
            WHERE u.user_id = :user_id
        ");
            $stmt->bindParam(':user_id', $user['user_id']);
            $stmt->execute();
            $team = $stmt->fetch();

            // เก็บข้อมูลใน session
            $_SESSION['team_name'] = $team['team_name'];
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['team_id'] = $user['team_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['profile_image'] = $user['profile_image'];

            // แสดง SweetAlert เมื่อเข้าสู่ระบบสำเร็จ
            echo "<script>
            setTimeout(function() {
                Swal.fire({
                    title: 'Login success.',
                    text: 'Welcome to login Sale Service.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function() {
                    window.location.href = 'index.php';
                });
            }, 100);
        </script>";
        } catch (PDOException $e) {
            // จัดการ error
            echo "Error: " . $e->getMessage();
        }
    } else {
        // แสดง error เมื่อ login ไม่สำเร็จ
        echo "<script>
        setTimeout(function() {
            Swal.fire({
                title: 'Oop.....!', 
                text: 'Invalid username or password, please try again.',
                icon: 'warning',
            }).then(function() {
                window.location.href = 'login.php';
            });
        }, 100);
    </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Point IT Sales Management - Login</title>
    <!-- นำเข้า Bootstrap 5 CSS สำหรับการจัดรูปแบบหน้าเว็บ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- นำเข้า Font Awesome 6 สำหรับไอคอน -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- นำเข้า Google Fonts (Poppins) สำหรับฟอนต์ที่สวยงาม -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- นำเข้า SweetAlert2 สำหรับการแสดงป๊อปอัพแจ้งเตือนที่สวยงาม -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>assets/img/favicon.ico">
    <style>
        /* กำหนดสไตล์ CSS สำหรับหน้า Login */
        body {
            font-family: 'Poppins', sans-serif;
            /* ใช้ฟอนต์ Poppins */
            background: linear-gradient(120deg, #2980b9, #8e44ad);
            /* พื้นหลังแบบ gradient */
            height: 100vh;
            overflow: hidden;
        }

        .login-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 25px rgba(0, 0, 0, .6);
            overflow: hidden;
            max-width: 850px;
            width: 100%;
        }

        .login-box-info {
            background: #4e73df;
            color: white;
            padding: 40px;
        }

        .login-box-form {
            padding: 40px;
        }

        .form-control {
            border-radius: 25px;
        }

        .btn-theme {
            background: #4e73df;
            color: white;
            border-radius: 25px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-theme:hover {
            background: #2e59d9;
            color: white;
        }

        .login-title {
            font-weight: 600;
            color: #4e73df;
        }

        .input-group-text {
            background: transparent;
            border-right: none;
        }

        .form-control {
            border-left: none;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }

        /* กำหนด animation สำหรับ fade-in effect */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        /* แก้ไข input-group-text ให้เส้นขอบและสีชัดเจน */
        .input-group .input-group-text:last-child {
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
            border-left: 0;
            background-color: #ffffff;
            /* เพิ่มพื้นหลังสีขาว */
            border: 1px solid #ced4da;
            /* เพิ่มสีเส้นขอบให้ชัดเจน */
        }

        /* แก้ไข form-control เพื่อให้เส้นขอบสม่ำเสมอ */
        .input-group .form-control {
            border: 1px solid #ced4da;
            /* กำหนดสีเส้นขอบ */
            border-top-left-radius: 25px;
            border-bottom-left-radius: 25px;
            background-color: #ffffff;
            /* เพิ่มพื้นหลังสีขาว */
        }

        /* ปรับเส้นขอบของฟิลด์ input ทั้งหมดในกรณี focus */
        .input-group .form-con
    </style>
</head>

<body>
    <!-- ส่วนหลักของหน้า Login -->
    <div class="container-fluid login-wrapper">
        <div class="login-box row g-0">
            <!-- ส่วนแสดงรูปภาพและข้อความต้อนรับ (แสดงเฉพาะบนจอขนาดใหญ่) -->
            <div class="col-lg-6 login-box-info d-none d-lg-block">
                <h2 class="text-center mb-4 animate-fade-in">Welcome Back!</h2>
                <img src="assets/img/cp.jpg" class="img-fluid rounded animate-fade-in" alt="Company Logo">
            </div>
            <!-- ส่วนฟอร์ม Login -->
            <div class="col-lg-6 login-box-form">
                <div class="p-4 p-md-5">
                    <img src="assets/img/pit.png" class="img-fluid rounded animate-fade-in" alt="Company Logo">
                    <h5 class="login-title text-center mb-4 animate-fade-in">
                        <i class="fas fa-handshake"></i> Point IT Sales Management
                    </h5>
                    <!-- ฟอร์ม Login -->
                    <form action="login.php" method="post" class="animate-fade-in">
                        <!-- ช่องกรอก Username -->
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" name="username" placeholder="Username" required>
                            </div>
                        </div>
                        <!-- ช่องกรอก Password -->
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                                <span class="input-group-text" id="togglePassword">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </span>
                            </div>
                        </div>

                        <!-- ปุ่ม Login -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-theme btn-block">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- นำเข้า Bootstrap 5 JS Bundle with Popper สำหรับฟังก์ชันการทำงานของ Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ส่วนของ PHP สำหรับจัดการการ Login
        <?php if (isset($loginError)): ?>
            // ใช้ SweetAlert2 เพื่อแสดงข้อความแจ้งเตือนเมื่อ Login ไม่สำเร็จ
            Swal.fire({
                title: 'Oops...',
                text: '<?php echo $loginError; ?>',
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        <?php endif; ?>
    </script>

    <!-- JavaScript สำหรับจัดการการแสดง/ซ่อนรหัสผ่านและจดจำ username -->
    <script>
        // จัดการการแสดง/ซ่อนรหัสผ่าน
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        });



        // จดจำ username ที่กรอก
        document.addEventListener('DOMContentLoaded', function() {
            const savedUsername = localStorage.getItem('rememberedUsername');
            if (savedUsername) {
                document.querySelector('input[name="username"]').value = savedUsername;
            }
        });

        // บันทึก username เมื่อ submit form
        document.querySelector('form').addEventListener('submit', function() {
            const username = document.querySelector('input[name="username"]').value;
            localStorage.setItem('rememberedUsername', username);
        });
    </script>
</body>

</html>