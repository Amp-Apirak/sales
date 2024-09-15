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

    // ตรวจสอบว่ามีผู้ใช้นี้หรือไม่ และตรวจสอบรหัสผ่าน
    if ($user && password_verify($password, $user['password'])) {
        // เก็บข้อมูลผู้ใช้ใน session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['team_id'] = $user['team_id'];

        // แสดง SweetAlert เมื่อเข้าสู่ระบบสำเร็จ
        echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: 'เข้าสู่ระบบเรียบร้อยแล้ว!',
                        icon: 'success',
                        confirmButtonText: 'ตกลง'
                    }).then(function() {
                        window.location.href = 'pages/account/account.php'; // นำไปยังหน้าถัดไปหลังจาก SweetAlert
                    });
                }, 100);
              </script>";
    } else {
        // กรณีไม่พบผู้ใช้หรือรหัสผ่านไม่ถูกต้อง
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keywords" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Meta Responsive tag -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Custom style.css -->
    <link rel="stylesheet" href="assets/css/quicksand.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/fontawesome.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Login</title>
</head>

<body class="login-body">

    <!-- Login Wrapper -->
    <div class="container-fluid login-wrapper">
        <div class="login-box">
            <h1 class="text-center mb-5"><i class="fas fa-handshake text-primary"></i> Point IT Sales Management</h1>
            <div class="row">
                <!-- Login Box Info -->
                <div class="col-md-6 col-sm-6 col-12 login-box-info">
                    <img src="assets/img/cp.jpg" width="100%" height="100%" alt="Company Logo">
                </div>
                <!-- Login Box Form -->
                <div class="col-md-6 col-sm-6 col-12 login-box-form p-4">
                    <h3 class="mb-2">Login</h3>
                    <small class="text-muted bc-description"></small>

                    <?php if (!empty($error)) : ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="login.php" method="post" class="mt-2">
                        <!-- Username Input -->
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                            </div>
                            <input type="text" class="form-control mt-0" name="username" placeholder="username"
                                aria-label="username" required aria-describedby="basic-addon1">
                        </div>
                        <!-- Password Input -->
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-lock"></i></span>
                            </div>
                            <input type="password" class="form-control mt-0" name="password" placeholder="password"
                                aria-label="password" required aria-describedby="basic-addon1">
                        </div>
                        <!-- Login Button -->
                        <div class="form-group">
                            <button class="btn btn-theme btn-block p-2 mb-1">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Login Wrapper -->

    <!-- Page JavaScript Files -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery-1.12.4.min.js"></script>
    <!-- Popper JS -->
    <script src="assets/js/popper.min.js"></script>
    <!-- Bootstrap -->
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- Custom Js Script -->
    <script src="assets/js/custom.js"></script>
</body>

</html>