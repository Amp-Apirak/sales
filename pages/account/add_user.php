<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../config/condb.php');
include('../../config/validation.php'); // นำเข้าฟังก์ชัน validation

// ตรวจสอบสิทธิ์การเข้าถึงหน้า
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Executive', 'Account Management', 'Sale Supervisor'])) {
    header("Location: ../../index.php");
    exit();
}

// ตรวจสอบสิทธิ์ผู้ใช้
$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$team_ids = $_SESSION['team_ids'] ?? [];  // ดึง team_ids ของผู้ใช้จาก session (สำหรับ Account Management)
$created_by = $_SESSION['user_id']; // ดึง user_id ของผู้สร้างจาก session

// สร้าง CSRF Token
$csrf_token = generateCSRFToken();

// ตรวจสอบว่าผู้ใช้กดปุ่ม "เพิ่มผู้ใช้" หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบ CSRF Token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        echo "<script>
                alert('Security token invalid. Please try again.');
                window.location.href = 'add_user.php';
              </script>";
        exit;
    }

    // รับข้อมูลจากฟอร์มและตรวจสอบความถูกต้อง
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

    // ตรวจสอบ username
    $usernameValidation = validateUsername($_POST['username'] ?? '');
    if (!$usernameValidation['valid']) {
        $validationErrors[] = $usernameValidation['message'];
    } else {
        $username = $usernameValidation['value'];
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

    // ตรวจสอบ password
    $passwordValidation = validatePassword($_POST['password'] ?? '');
    if (!$passwordValidation['valid']) {
        $validationErrors[] = $passwordValidation['message'];
    } else {
        $password = password_hash($passwordValidation['value'], PASSWORD_DEFAULT);
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

    // ตรวจสอบ team_id และ role
    $team_id_new = filter_var($_POST['team_id'] ?? 0, FILTER_VALIDATE_INT);
    $role_new = sanitizeInput($_POST['role'] ?? '');

    // ถ้ามี validation errors แสดงข้อความแจ้งเตือน
    if (!empty($validationErrors)) {
        $errorMessage = implode('\\n', $validationErrors);
        echo "<script>
                alert('ข้อมูลไม่ถูกต้อง:\\n" . $errorMessage . "');
                window.location.href = 'add_user.php';
              </script>";
        exit;
    }

    // ตรวจสอบสิทธิ์ตาม role
    if ($role === 'Account Management') {
        // Account Management ไม่สามารถสร้าง Executive ได้
        if ($role_new === 'Executive') {
            echo "<script>
                    alert('คุณไม่มีสิทธิ์สร้างผู้ใช้งานที่มีบทบาทเป็น Executive');
                    window.location.href = 'add_user.php';
                  </script>";
            exit;
        }

        // Account Management สามารถสร้างผู้ใช้ได้เฉพาะในทีมที่ตัวเองสังกัด
        if (!in_array($team_id_new, $team_ids)) {
            echo "<script>
                    alert('คุณสามารถสร้างผู้ใช้งานได้เฉพาะทีมที่คุณสังกัดเท่านั้น');
                    window.location.href = 'add_user.php';
                  </script>";
            exit;
        }
    } elseif ($role === 'Sale Supervisor') {
        // Sale Supervisor ไม่สามารถสร้าง Executive ได้
        if ($role_new === 'Executive') {
            echo "<script>
                    alert('คุณไม่มีสิทธิ์สร้างผู้ใช้งานที่มีบทบาทเป็น Executive');
                    window.location.href = 'add_user.php';
                  </script>";
            exit;
        }

        // Sale Supervisor สามารถสร้างผู้ใช้ได้เฉพาะทีมของตัวเอง
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
                    $sql = "INSERT INTO users (first_name, last_name, username, email, phone, password, position, team_id, role, company, created_by)
                            VALUES (:first_name, :last_name, :username, :email, :phone, :password, :position, :team_id, :role, :company, :created_by)";
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
                    $stmt->bindParam(':company', $company); // เพิ่มการเก็บ company
                    $stmt->bindParam(':created_by', $created_by); // เก็บ user ที่สร้างผู้ใช้งาน
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
if ($role === 'Executive') {
    // Executive เห็นทีมทั้งหมด
    $sql_teams = "SELECT team_id, team_name FROM teams";
    $query_teams = $condb->query($sql_teams)->fetchAll();
} elseif ($role === 'Account Management') {
    // Account Management เห็นเฉพาะทีมที่ตัวเองสังกัด
    if (!empty($team_ids)) {
        $placeholders = implode(',', array_fill(0, count($team_ids), '?'));
        $sql_teams = "SELECT team_id, team_name FROM teams WHERE team_id IN ($placeholders)";
        $stmt_teams = $condb->prepare($sql_teams);
        $stmt_teams->execute($team_ids);
        $query_teams = $stmt_teams->fetchAll();
    } else {
        $query_teams = [];
    }
} else { // Sale Supervisor
    // Sale Supervisor เห็นเฉพาะทีมของตัวเอง
    $sql_teams = "SELECT team_id, team_name FROM teams WHERE team_id = :team_id";
    $stmt_teams = $condb->prepare($sql_teams);
    $stmt_teams->bindParam(':team_id', $team_id);
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
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrf_token); ?>">

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
                        <option value="Account Management">Account Management</option>
                        <option value="Sale Supervisor">Sale Supervisor</option>
                        <option value="Seller">Seller</option>
                        <option value="Engineer">Engineer</option>
                    <?php } elseif ($role === 'Account Management') { ?>
                        <option value="Account Management">Account Management</option>
                        <option value="Sale Supervisor">Sale Supervisor</option>
                        <option value="Seller">Seller</option>
                        <option value="Engineer">Engineer</option>
                    <?php } else { // Sale Supervisor ?>
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