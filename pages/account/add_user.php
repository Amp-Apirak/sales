<?php
// เริ่มต้น session
session_start();

// ตรวจสอบการตั้งค่า Session เพื่อป้องกันกรณีที่ไม่ได้ล็อกอิน
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    // กรณีไม่มีการตั้งค่า Session หรือล็อกอิน
    header("Location: login.php"); // Redirect ไปยังหน้า login.php
    exit; // หยุดการทำงานของสคริปต์ปัจจุบันหลังจาก redirect
}

// เชื่อมต่อฐานข้อมูล
include('../../config/condb.php');

// ตรวจสอบการตั้งค่า Session เพื่อป้องกันกรณีที่ไม่ได้ล็อกอิน
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    // กรณีไม่มีการตั้งค่า Session หรือล็อกอิน
    header("Location: login.php"); // Redirect ไปยังหน้า login.php
    exit; // หยุดการทำงานของสคริปต์ปัจจุบันหลังจาก redirect
}


$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$created_by = $_SESSION['user_id']; // ดึง user_id ของผู้สร้างจาก session

// ตรวจสอบว่าผู้ใช้กดปุ่ม "เพิ่มผู้ใช้" หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจากฟอร์มและล้างข้อมูลด้วย htmlspecialchars เพื่อป้องกัน XSS
    $first_name = htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8');
    $last_name = htmlspecialchars($_POST['last_name'], ENT_QUOTES, 'UTF-8');
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // เข้ารหัสรหัสผ่าน
    $position = htmlspecialchars($_POST['position'], ENT_QUOTES, 'UTF-8');
    $team_id_new = $_POST['team_id'];
    $role_new = $_POST['role'];
    $company = htmlspecialchars($_POST['company'], ENT_QUOTES, 'UTF-8');  // รับข้อมูลบริษัทจากฟอร์ม

    // ตรวจสอบสิทธิ์ Sale Supervisor: สามารถเลือกทีมและบทบาทเฉพาะทีมของตัวเอง และไม่สามารถสร้าง Executive ได้
    if ($role === 'Sale Supervisor') {
        if ($role_new === 'Executive') {
            echo "<script>
                    alert('คุณไม่มีสิทธิ์สร้างผู้ใช้งานที่มีบทบาทเป็น Executive');
                    window.location.href = 'add_user.php';
                  </script>";
            exit;
        }

        if ($team_id_new != $team_id) {
            echo "<script>
                    alert('คุณสามารถสร้างผู้ใช้งานได้เฉพาะทีมของคุณเท่านั้น');
                    window.location.href = 'add_user.php';
                  </script>";
            exit;
        }
    }

    // ตรวจสอบว่ามีชื่อผู้ใช้งานระบบที่ซ้ำหรือไม่
    $checkusername_sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $condb->prepare($checkusername_sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $existing_user = $stmt->fetch();

    if ($existing_user) {
        // ถ้าพบชื่อผู้ใช้งานซ้ำ
        echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ชื่อผู้ใช้งานนี้ถูกใช้ไปแล้ว!',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                }, 100);
              </script>";
    } else {
        // ตรวจสอบว่ามีอีเมลที่ซ้ำหรือไม่
        $checkemail_sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $condb->prepare($checkemail_sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $existing_user = $stmt->fetch();

        if ($existing_user) {
            // ถ้าอีเมลซ้ำ
            echo "<script>
                    setTimeout(function() {
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด',
                            text: 'อีเมลนี้ถูกใช้ไปแล้ว!',
                            icon: 'error',
                            confirmButtonText: 'ตกลง'
                        });
                    }, 100);
                  </script>";
        } else {
            // ตรวจสอบว่ามีเบอร์โทรศัพท์ซ้ำหรือไม่
            $checkphone_sql = "SELECT * FROM users WHERE phone = :phone";
            $stmt = $condb->prepare($checkphone_sql);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->execute();
            $existing_user = $stmt->fetch();

            if ($existing_user) {
                // ถ้าเบอร์โทรศัพท์ซ้ำ
                echo "<script>
                        setTimeout(function() {
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด',
                                text: 'เบอร์โทรศัพท์นี้ถูกใช้ไปแล้ว!',
                                icon: 'error',
                                confirmButtonText: 'ตกลง'
                            });
                        }, 100);
                      </script>";
            } else {
                // เพิ่มข้อมูลผู้ใช้ลงฐานข้อมูล
                try {
                    $sql = "INSERT INTO users (first_name, last_name, username, email, phone, password, position, team_id, role, company, created_by)
                            VALUES (:first_name, :last_name, :username, :email, :phone, :password, :position, :team_id, :role, :company, :created_by)";
                    $stmt = $condb->prepare($sql);
                    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
                    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
                    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                    $stmt->bindParam(':position', $position, PDO::PARAM_STR);
                    $stmt->bindParam(':team_id', $team_id_new, PDO::PARAM_INT);
                    $stmt->bindParam(':role', $role_new, PDO::PARAM_STR);
                    $stmt->bindParam(':company', $company, PDO::PARAM_STR);
                    $stmt->bindParam(':created_by', $created_by, PDO::PARAM_INT);
                    $stmt->execute();

                    // แสดงข้อความเมื่อเพิ่มผู้ใช้สำเร็จด้วย SweetAlert
                    echo "<script>
                            setTimeout(function() {
                                Swal.fire({
                                    title: 'สำเร็จ!',
                                    text: 'เพิ่มผู้ใช้งานเรียบร้อยแล้ว!',
                                    icon: 'success',
                                    confirmButtonText: 'ตกลง'
                                }).then(function() {
                                    window.location.href = 'user_list.php';
                                });
                            }, 100);
                          </script>";
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
        }
    }
}

// ดึงข้อมูลทีมจากฐานข้อมูลเพื่อนำมาแสดงใน dropdown
if ($role === 'Sale Supervisor') {
    $sql_teams = "SELECT team_id, team_name FROM teams WHERE team_id = :team_id";
    $stmt_teams = $condb->prepare($sql_teams);
    $stmt_teams->bindParam(':team_id', $team_id, PDO::PARAM_INT); // ระบุว่าเป็น integer
    $stmt_teams->execute();
    $query_teams = $stmt_teams->fetchAll();
} else {
    $sql_teams = "SELECT team_id, team_name FROM teams";
    $stmt_teams = $condb->prepare($sql_teams);
    $stmt_teams->execute();
    $query_teams = $stmt_teams->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มผู้ใช้งาน</title>
    <!-- ใส่ CSS ที่จำเป็น -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <!-- ใส่ SweetAlert CSS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <h2 class="mt-5">เพิ่มผู้ใช้งาน</h2>

        <!-- ฟอร์มเพิ่มผู้ใช้งาน -->
        <form method="POST" action="add_user.php">
            <div class="form-group">
                <label for="first_name">ชื่อจริง</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>

            <div class="form-group">
                <label for="last_name">นามสกุล</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>

            <div class="form-group">
                <label for="username">ชื่อผู้ใช้</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="phone">เบอร์โทรศัพท์</label>
                <input type="phone" class="form-control" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="email">อีเมล</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="company">บริษัท</label>
                <input type="text" class="form-control" id="company" name="company" required>
            </div>

            <div class="form-group">
                <label for="position">ตำแหน่ง</label>
                <input type="text" class="form-control" id="position" name="position" required>
            </div>

            <div class="form-group">
                <label for="team_id">ทีม</label>
                <select class="form-control" id="team_id" name="team_id" required>
                    <option value="">เลือกทีม</option>
                    <?php foreach ($query_teams as $team) { ?>
                        <option value="<?php echo $team['team_id']; ?>"><?php echo $team['team_name']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="role">บทบาท</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">เลือกบทบาท</option>
                    <?php if ($role === 'Executive') { ?>
                        <option value="Executive">Executive</option>
                        <option value="Sale Supervisor">Sale Supervisor</option>
                        <option value="Seller">Seller</option>
                        <option value="Engineer">Engineer</option>
                    <?php } else { ?>
                        <option value="Sale Supervisor">Sale Supervisor</option>
                        <option value="Seller">Seller</option>
                        <option value="Engineer">Engineer</option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">เพิ่มผู้ใช้งาน</button>
        </form>
    </div>

    <!-- ใส่ไฟล์ JS ที่จำเป็น -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>