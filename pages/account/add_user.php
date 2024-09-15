<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../config/condb.php');

// ตรวจสอบสิทธิ์ผู้ใช้
$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session

// print_r($role);
// print_r($team_id);

// ตรวจสอบว่าผู้ใช้กดปุ่ม "เพิ่มผู้ใช้" หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจากฟอร์ม
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone']; // รับค่าจากฟอร์ม
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // เข้ารหัสรหัสผ่าน
    $position = $_POST['position'];
    $team_id_new = $_POST['team_id'];
    $role_new = $_POST['role'];

    // ตรวจสอบสิทธิ์ Sale Supervisor: สามารถเลือกทีมและบทบาทเฉพาะทีมของตัวเอง และไม่สามารถสร้าง Executive ได้
    if ($role === 'Sale Supervisor') {
        // ตรวจสอบว่าผู้ใช้พยายามกำหนดบทบาทเป็น Executive หรือไม่
        if ($role_new === 'Executive') {
            echo "<script>
                    alert('คุณไม่มีสิทธิ์สร้างผู้ใช้งานที่มีบทบาทเป็น Executive');
                    window.location.href = 'add_user.php';
                  </script>";
            exit;
        }

        // ตรวจสอบว่าผู้ใช้พยายามสร้างผู้ใช้งานในทีมอื่นที่ไม่ใช่ทีมของตัวเองหรือไม่
        if ($team_id_new !== $team_id) {
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
    $stmt->bindParam(':username', $username);
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
        $stmt->bindParam(':email', $email);
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
            $stmt->bindParam(':phone', $phone);
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
                    $sql = "INSERT INTO users (first_name, last_name, username, email, phone, password, position, team_id, role)
                            VALUES (:first_name, :last_name, :username, :email, :phone, :password, :position, :team_id, :role)";
                    $stmt = $condb->prepare($sql);
                    $stmt->bindParam(':first_name', $first_name);
                    $stmt->bindParam(':last_name', $last_name);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':phone', $phone);
                    $stmt->bindParam(':password', $password);
                    $stmt->bindParam(':position', $position);
                    $stmt->bindParam(':team_id', $team_id_new);
                    $stmt->bindParam(':role', $role_new);
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
    // สำหรับ Sale Supervisor แสดงเฉพาะทีมที่ตัวเองอยู่
    $sql_teams = "SELECT team_id, team_name FROM teams WHERE team_id = :team_id";
    $stmt_teams = $condb->prepare($sql_teams);
    $stmt_teams->bindParam(':team_id', $team_id);
    $stmt_teams->execute();
    $query_teams = $stmt_teams->fetchAll();
} else {
    // สำหรับ Executive แสดงทุกทีม
    $sql_teams = "SELECT team_id, team_name FROM teams";
    $query_teams = $condb->query($sql_teams)->fetchAll();
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
                <label for="phone">เบอร์โทรศัทพ์</label>
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