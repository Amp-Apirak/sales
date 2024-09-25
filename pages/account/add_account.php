<?php
//session_start and Config DB
include  '../../include/Add_session.php';

// ตรวจสอบสิทธิ์การเข้าถึง
$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$created_by = $_SESSION['user_id']; // ดึง user_id ของผู้สร้างจาก session

// จำกัดการเข้าถึงเฉพาะผู้ใช้ที่มีสิทธิ์
if (!in_array($role, ['Executive', 'Sale Supervisor', 'Seller'])) {
    header("Location: unauthorized.php");
    exit();
}

// ดึงข้อมูลทีมจากฐานข้อมูล
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

// สร้างหรือดึง CSRF Token สำหรับป้องกันการโจมตี CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ฟังก์ชันสำหรับสร้าง UUID
function generateUUID()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // เวอร์ชัน 4 UUID
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // UUID variant
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// ตรวจสอบการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบ CSRF Token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    // สร้าง UUID สำหรับ user_id
    $user_id = generateUUID();

    // รับข้อมูลจากฟอร์มและทำความสะอาดข้อมูล
    $first_name = clean_input($_POST['first_name']);
    $last_name = clean_input($_POST['last_name']);
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // เข้ารหัสรหัสผ่าน
    $position = clean_input($_POST['position']);
    $team_id_new = $_POST['team_id'];
    $role_new = $_POST['role'];
    $company = clean_input($_POST['company']);  // รับข้อมูลบริษัทจากฟอร์ม

    // ตรวจสอบสิทธิ์ Sale Supervisor
    if ($role === 'Sale Supervisor') {
        if ($role_new === 'Executive') {
            echo "<script>alert('คุณไม่มีสิทธิ์สร้างผู้ใช้งานที่มีบทบาทเป็น Executive'); window.location.href = 'add_user.php';</script>";
            exit;
        }

        if ($team_id_new != $team_id) {
            echo "<script>alert('คุณสามารถสร้างผู้ใช้งานได้เฉพาะทีมของคุณเท่านั้น'); window.location.href = 'add_user.php';</script>";
            exit;
        }
    }

    // ตรวจสอบว่ามีชื่อผู้ใช้งานซ้ำหรือไม่
    $checkusername_sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $condb->prepare($checkusername_sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $existing_user = $stmt->fetch();

    if ($existing_user) {
        echo "<script>setTimeout(function() {
                    Swal.fire({ title: 'เกิดข้อผิดพลาด', text: 'ชื่อผู้ใช้งานนี้ถูกใช้ไปแล้ว!', icon: 'error', confirmButtonText: 'ตกลง' });
                }, 100);</script>";
    } else {
        // ตรวจสอบอีเมลที่ซ้ำ
        $checkemail_sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $condb->prepare($checkemail_sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $existing_user = $stmt->fetch();

        if ($existing_user) {
            echo "<script>setTimeout(function() {
                        Swal.fire({ title: 'เกิดข้อผิดพลาด', text: 'อีเมลนี้ถูกใช้ไปแล้ว!', icon: 'error', confirmButtonText: 'ตกลง' });
                    }, 100);</script>";
        } else {
            // ตรวจสอบเบอร์โทรที่ซ้ำ
            $checkphone_sql = "SELECT * FROM users WHERE phone = :phone";
            $stmt = $condb->prepare($checkphone_sql);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->execute();
            $existing_user = $stmt->fetch();

            if ($existing_user) {
                echo "<script>setTimeout(function() {
                            Swal.fire({ title: 'เกิดข้อผิดพลาด', text: 'เบอร์โทรนี้ถูกใช้ไปแล้ว!', icon: 'error', confirmButtonText: 'ตกลง' });
                        }, 100);</script>";
            } else {
                // เพิ่มข้อมูลผู้ใช้ลงฐานข้อมูล
                try {
                    $sql = "INSERT INTO users (user_id,first_name, last_name, username, email, phone, password, position, team_id, role, company, created_by)
                    VALUES (:user_id, :first_name, :last_name, :username, :email, :phone, :password, :position, :team_id, :role, :company, :created_by)";
                    $stmt = $condb->prepare($sql);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
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

                    // แสดงข้อความสำเร็จ
                    echo "<script>
                        setTimeout(function() {
                            Swal.fire({
                                title: 'สำเร็จ!',
                                text: 'เพิ่มผู้ใช้งานเรียบร้อยแล้ว!',
                                icon: 'success',
                                confirmButtonText: 'ตกลง'
                            }).then(function() {
                                window.location.href = 'account.php'; // เปลี่ยนเส้นทางไปยังหน้า account
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
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "account"; ?>
<!-- ใส่ SweetAlert2 CSS และ JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Create Account</title>
    <?php include '../../include/header.php'; ?>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Navbar and Sidebar -->
        <?php include '../../include/navbar.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Create Account</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Create Account v1</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Account Descriptions</h3>
                                        </div>
                                        <div class="card-body">
                                            <form action="#" method="POST" enctype="multipart/form-data">
                                                <!-- CSRF Token -->
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                                <!-- First Name -->
                                                <div class="form-group">
                                                    <label for="first_name">First Name<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-address-book"></i></span>
                                                        </div>
                                                        <input type="text" name="first_name" class="form-control" id="first_name" placeholder="" required>
                                                    </div>
                                                </div>

                                                <!-- Last Name -->
                                                <div class="form-group">
                                                    <label for="last_name">Last Name<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-address-book"></i></span>
                                                        </div>
                                                        <input type="text" name="last_name" class="form-control" id="last_name" placeholder="" required>
                                                    </div>
                                                </div>

                                                <!-- Phone -->
                                                <div class="form-group">
                                                    <label for="phone">Phone Number</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="phone" id="phone" required>
                                                    </div>
                                                </div>

                                                <!-- Email -->
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                        </div>
                                                        <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                                                    </div>
                                                </div>

                                                <!-- Position -->
                                                <div class="form-group">
                                                    <label for="position">Position</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-star"></i></span>
                                                        </div>
                                                        <input type="text" name="position" class="form-control" id="position" placeholder="">
                                                    </div>
                                                </div>

                                                <!-- Team -->
                                                <div class="form-group">
                                                    <label for="team_id">Team<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-users"></i></span>
                                                        </div>
                                                        <select class="form-control select2" id="team_id" name="team_id" required>
                                                            <option value="">Select Team</option>
                                                            <?php foreach ($query_teams as $team) { ?>
                                                                <option value="<?php echo $team['team_id']; ?>"><?php echo $team['team_name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Company -->
                                                <div class="form-group">
                                                    <label for="company">Company<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                        </div>
                                                        <input type="text" name="company" class="form-control" id="company" placeholder="" required>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Setting Account</h3>
                                        </div>
                                        <div class="card-body">

                                            <!-- Role -->
                                            <div class="form-group">
                                                <label>Role<span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                    </div>
                                                    <select class="form-control select2" id="role" name="role" required>
                                                        <option value="">Select Role</option>
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
                                            </div>

                                            <!-- Username -->
                                            <div class="form-group">
                                                <label for="username">User Account<span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    </div>
                                                    <input type="text" name="username" class="form-control" id="username" placeholder="Username" required>
                                                </div>
                                            </div>

                                            <!-- Password -->
                                            <div class="form-group">
                                                <label for="password">Password<span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-unlock"></i></span>
                                                    </div>
                                                    <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                                                </div>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="form-group mt-5">
                                                <button type="submit" name="submit" value="submit" class="btn btn-sm btn-success w-25">Save</button>
                                            </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <?php include '../../include/footer.php'; ?>
    </div>

    <!-- JS for Dropdown Select2 -->
    <script>
        $(function() {
            $('.select2').select2();
        });
    </script>
    <script>
        // ป้องกันการรีเฟรชหน้าเมื่อกด submit
        document.getElementById("myForm").addEventListener("submit", function(event) {
            event.preventDefault();
            // ตรวจสอบและแสดง SweetAlert
        });
    </script>
</body>

</html>