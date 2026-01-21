<?php
// เริ่ม session ก่อน output ใดๆ
session_start();

// ลบตัวแปร session ทั้งหมด
$_SESSION = array();

// ลบ session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ทำลาย session
session_destroy();

// Regenerate session ID เพื่อป้องกัน session fixation
session_start();
session_regenerate_id(true);
session_destroy();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Point IT Sales</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(120deg, #2980b9, #8e44ad);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
    </style>
</head>
<body>
    <script>
        // ล้าง localStorage ที่เกี่ยวข้อง (ถ้ามี sensitive data)
        // localStorage.removeItem('rememberedUsername'); // เก็บไว้เพื่อความสะดวก

        // ป้องกันการกลับไปหน้าก่อนหน้า
        if (window.history && window.history.pushState) {
            window.history.pushState(null, null, window.location.href);
            window.onpopstate = function() {
                window.history.pushState(null, null, window.location.href);
            };
        }

        Swal.fire({
            title: 'ออกจากระบบสำเร็จ!',
            text: 'ขอบคุณที่ใช้บริการ',
            icon: 'success',
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#4e73df',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(function() {
            window.location.replace('login.php');
        });
    </script>
</body>
</html>
