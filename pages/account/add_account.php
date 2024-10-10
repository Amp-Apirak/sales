<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ตรวจสอบสิทธิ์การเข้าถึง
$role = $_SESSION['role'];  // บทบาทของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // team_id ของผู้ใช้จาก session
$created_by = $_SESSION['user_id'];  // user_id ของผู้สร้างจาก session

// จำกัดการเข้าถึงเฉพาะผู้ใช้ที่มีสิทธิ์เท่านั้น
if (!in_array($role, ['Executive', 'Sale Supervisor', 'Seller'])) {
    header("Location: unauthorized.php");
    exit();
}

// สร้างหรือดึง CSRF Token เพื่อป้องกันการโจมตีแบบ CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ฟังก์ชันทำความสะอาดข้อมูล input เพื่อป้องกันการโจมตีแบบ XSS
function clean_input($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ฟังก์ชันสำหรับสร้าง UUID สำหรับ user_id ใหม่
function generateUUID()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // กำหนดเวอร์ชัน 4 ของ UUID
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // กำหนด UUID variant
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// ดึงข้อมูลทีมจากฐานข้อมูลตามบทบาทของผู้ใช้
if ($role === 'Sale Supervisor') {
    // Sale Supervisor จะเห็นเฉพาะทีมของตนเอง
    $sql_teams = "SELECT team_id, team_name FROM teams WHERE team_id = :team_id";
    $stmt_teams = $condb->prepare($sql_teams);
    $stmt_teams->bindParam(':team_id', $team_id, PDO::PARAM_STR);
    $stmt_teams->execute();
} else {
    // Executive และผู้ที่มีสิทธิ์สูงกว่า สามารถเห็นทีมทั้งหมด
    $sql_teams = "SELECT team_id, team_name FROM teams";
    $stmt_teams = $condb->prepare($sql_teams);
    $stmt_teams->execute();
}
$query_teams = $stmt_teams->fetchAll();

// ตัวแปรเก็บข้อความแจ้งเตือนสำหรับการตรวจสอบข้อมูล
$error_messages = [];

// ตรวจสอบการส่งฟอร์มแบบ AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // ตรวจสอบ CSRF Token ว่าถูกต้องหรือไม่
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die(json_encode(['success' => false, 'errors' => ['Invalid CSRF token']]));
    }

    // ทำความสะอาดและรับข้อมูลจากฟอร์ม
    $user_id = generateUUID();  // สร้าง user_id ใหม่แบบ UUID
    $first_name = clean_input($_POST['first_name']);
    $last_name = clean_input($_POST['last_name']);
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $password = $_POST['password'];
    $position = clean_input($_POST['position']);
    $team_id_new = !empty($_POST['team_id']) ? clean_input($_POST['team_id']) : null;
    $role_new = clean_input($_POST['role']);
    $company = clean_input($_POST['company']);

    // ตรวจสอบข้อมูลที่จำเป็นว่าถูกกรอกครบถ้วนหรือไม่
    if (empty($first_name)) $error_messages[] = "กรุณากรอกชื่อ";
    if (empty($last_name)) $error_messages[] = "กรุณากรอกนามสกุล";
    if (empty($username)) $error_messages[] = "กรุณากรอกชื่อผู้ใช้";
    if (empty($email)) $error_messages[] = "กรุณากรอกอีเมล";
    if (empty($password)) $error_messages[] = "กรุณากรอกรหัสผ่าน";
    if (empty($team_id_new)) $error_messages[] = "กรุณาเลือกทีม";
    if (empty($role_new)) $error_messages[] = "กรุณาเลือกบทบาท";

    // ตรวจสอบความถูกต้องของรูปแบบอีเมล
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_messages[] = "รูปแบบอีเมลไม่ถูกต้อง";
    }

    // ถ้าไม่มีข้อผิดพลาด ดำเนินการบันทึกข้อมูลผู้ใช้ใหม่
    if (empty($error_messages)) {
        try {
            // ตรวจสอบชื่อผู้ใช้ซ้ำในฐานข้อมูล
            $stmt = $condb->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("ชื่อผู้ใช้นี้มีอยู่แล้ว");
            }

            // ตรวจสอบอีเมลซ้ำในฐานข้อมูล
            $stmt = $condb->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("อีเมลนี้มีอยู่แล้ว");
            }

            // ตรวจสอบเบอร์โทรศัพท์ซ้ำในฐานข้อมูล
            if (!empty($phone)) {
                $stmt = $condb->prepare("SELECT COUNT(*) FROM users WHERE phone = :phone");
                $stmt->execute([':phone' => $phone]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception("เบอร์โทรศัพท์นี้มีอยู่แล้ว");
                }
            }

            // เข้ารหัสรหัสผ่านของผู้ใช้ใหม่
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // เตรียม SQL สำหรับการเพิ่มข้อมูลผู้ใช้ใหม่
            $sql = "INSERT INTO users (user_id, first_name, last_name, username, email, phone, password, position, team_id, role, company, created_by) 
                    VALUES (:user_id, :first_name, :last_name, :username, :email, :phone, :password, :position, :team_id, :role, :company, :created_by)";

            $stmt = $condb->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':username' => $username,
                ':email' => $email,
                ':phone' => $phone,
                ':password' => $hashed_password,
                ':position' => $position,
                ':team_id' => $team_id_new,
                ':role' => $role_new,
                ':company' => $company,
                ':created_by' => $created_by
            ]);

            // ส่งข้อมูลกลับเป็น JSON เมื่อบันทึกข้อมูลสำเร็จ
            echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลผู้ใช้เรียบร้อยแล้ว']);
            exit;
        } catch (Exception $e) {
            // ส่งข้อความข้อผิดพลาดกลับเป็น JSON
            echo json_encode(['success' => false, 'errors' => [$e->getMessage()]]);
            exit;
        }
    } else {
        // ส่งข้อความข้อผิดพลาดกลับเป็น JSON
        echo json_encode(['success' => false, 'errors' => $error_messages]);
        exit;
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
                                            <form id="addUserForm" action="#" method="POST" enctype="multipart/form-data">
                                                <!-- CSRF Token -->
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                                <!-- First Name -->
                                                <div class="form-group">
                                                    <label for="first_name">First Name<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-address-book"></i></span>
                                                        </div>
                                                        <input type="text" name="first_name" class="form-control" id="first_name" placeholder="">
                                                    </div>
                                                </div>

                                                <!-- Last Name -->
                                                <div class="form-group">
                                                    <label for="last_name">Last Name<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-address-book"></i></span>
                                                        </div>
                                                        <input type="text" name="last_name" class="form-control" id="last_name" placeholder="">
                                                    </div>
                                                </div>

                                                <!-- Phone -->
                                                <div class="form-group">
                                                    <label for="phone">Phone Number</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="phone" id="phone">
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
                                                            <option value="">เลือกทีม</option>
                                                            <?php foreach ($query_teams as $team): ?>
                                                                <option value="<?php echo $team['team_id']; ?>" <?php echo ($team['team_id'] == $team_id) ? 'selected' : ''; ?>>
                                                                    <?php echo $team['team_name']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
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
                                                        <input type="text" name="company" class="form-control" id="company" placeholder="">
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
                                                    <select class="form-control select2" id="role" name="role">
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
                                                    <input type="text" name="username" class="form-control" id="username" placeholder="Username">
                                                </div>
                                            </div>

                                            <!-- Password -->
                                            <div class="form-group">
                                                <label for="password">Password<span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-unlock"></i></span>
                                                    </div>
                                                    <input type="password" name="password" class="form-control" id="password" placeholder="Password">
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

    <script>
        $(function() {
            $('.select2').select2();
        });

        $('#addUserForm').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'กำลังบันทึกข้อมูล...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                },
            });

            $.ajax({
                type: 'POST',
                url: 'add_account.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสำเร็จ',
                            text: response.message,
                            confirmButtonText: 'ตกลง'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'account.php';
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            html: response.errors.join('<br>'),
                            confirmButtonText: 'ตกลง'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ กรุณาลองใหม่อีกครั้ง',
                        confirmButtonText: 'ตกลง'
                    });
                }
            });
        });
    </script>
</body>

</html>