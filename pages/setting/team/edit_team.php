<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');


// ตรวจสอบการตั้งค่า Session เพื่อป้องกันกรณีที่ไม่ได้ล็อกอิน
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");  // Redirect ไปยังหน้า login.php
    exit; // หยุดการทำงานของสคริปต์ปัจจุบันหลังจาก redirect
}

// สร้างหรือดึง CSRF Token สำหรับป้องกันการโจมตี CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data) {
    // ทำความสะอาดข้อมูลแต่ยังคงเก็บอักขระพิเศษไว้
    $data = trim($data);
    // ป้องกัน SQL Injection โดยใช้ PDO parameters แทน
    return $data;
}

// ดึงข้อมูลจาก session
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];
$updated_by = $user_id; // ตั้งค่าตัวแปร $updated_by จาก user_id ของผู้ใช้งานปัจจุบัน


// จำกัดการเข้าถึงเฉพาะผู้ใช้ที่มีสิทธิ์เท่านั้น
// จำกัดการเข้าถึงเฉพาะผู้ใช้ที่มีสิทธิ์เท่านั้น (Executive หรือ Sale Supervisor)
if (!in_array($role, ['Executive'])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่อนุญาต',
                    text: 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้',
                    confirmButtonText: 'ตกลง'
                }).then(function() {
                    window.location.href = 'team.php'; // กลับไปยังหน้า team.php
                });
            }, 100);
          </script>";
    exit();
}


// ตรวจสอบว่ามีการส่ง team_id มาหรือไม่
if (isset($_GET['team_id'])) {
    $encrypted_team_id = urldecode($_GET['team_id']);
    $team_id = decryptUserId($encrypted_team_id);

    // ตรวจสอบว่าถอดรหัสสำเร็จหรือไม่
    if ($team_id !== false) {
        // ดึงข้อมูลทีมจากฐานข้อมูล
        $sql = "SELECT * FROM teams WHERE team_id = :team_id";
        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR);
        $stmt->execute();
        $team = $stmt->fetch();

        // ตรวจสอบว่ามีข้อมูลทีมหรือไม่
        if (!$team) {
            echo "ไม่พบข้อมูลทีม";
            exit;
        }
    } else {
        echo "รหัสทีมไม่ถูกต้อง";
        exit;
    }
} else {
    echo "ไม่มีการส่งรหัสทีมมา";
    exit;
}

// ดึงข้อมูลของผู้ใช้ทั้งหมดสำหรับหัวหน้าทีม
$sql_users = "SELECT user_id, first_name, last_name FROM users";
$stmt_users = $condb->prepare($sql_users);
$stmt_users->execute();
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// ตรวจสอบว่าผู้ใช้กดปุ่ม "แก้ไขข้อมูลทีม" หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบ CSRF Token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    // รับข้อมูลจากฟอร์มและล้างข้อมูลด้วย htmlspecialchars เพื่อป้องกัน XSS
    $team_name = clean_input($_POST['team_name']);
    $team_description = clean_input($_POST['team_description']);
    $team_leader = clean_input($_POST['team_leader']);

    // ตรวจสอบว่ามีชื่อทีมซ้ำหรือไม่
    $checkteam_sql = "SELECT * FROM teams WHERE team_name = :team_name AND team_id != :team_id";
    $stmt = $condb->prepare($checkteam_sql);
    $stmt->bindParam(':team_name', $team_name, PDO::PARAM_STR);
    $stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR);
    $stmt->execute();
    $existing_team = $stmt->fetch();

    if ($existing_team) {
        echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ชื่อทีมถูกใช้ไปแล้ว!',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                }, 100);
              </script>";
    } else if (
        $team_name == $team['team_name'] &&
        $team_description == $team['team_description'] &&
        $team_leader == $team['team_leader']
    ) {
        // ถ้าไม่มีการเปลี่ยนแปลงข้อมูล แสดง SweetAlert
        echo  '<script>
            setTimeout(function() {
                Swal.fire({
                    title: "Opp..",
                    text: "ไม่มีการแก้ไขข้อมูล!",
                    icon: "error"
                }).then(function() {
                    window.location = "team.php"; //หน้าที่ต้องการให้กระโดดไป
                });
            }, 1000);
            </script>';
    } else {

        try {
            // แก้ไขข้อมูลทีม
            $sql = "UPDATE teams SET team_name = :team_name, team_description = :team_description, team_leader = :team_leader, updated_by = :updated_by WHERE team_id = :team_id";
            $stmt = $condb->prepare($sql);
            $stmt->bindParam(':team_name', $team_name, PDO::PARAM_STR);
            $stmt->bindParam(':team_description', $team_description, PDO::PARAM_STR);
            $stmt->bindParam(':team_leader', $team_leader, PDO::PARAM_STR);
            $stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR);
            $stmt->bindParam(':updated_by', $updated_by, PDO::PARAM_INT);
            $stmt->execute();

            // แสดงข้อความเมื่อแก้ไขสำเร็จด้วย SweetAlert
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: 'แก้ไขข้อมูลทีมเรียบร้อยแล้ว!',
                        icon: 'success',
                        confirmButtonText: 'ตกลง'
                    }).then(function() {
                        window.location.href = 'team.php';
                    });
                }, 100);
              </script>";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "team"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Edit Team</title>
    <?php include '../../../include/header.php'; ?>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <?php include '../../../include/navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Edit Team</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Edit Team</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- ฟอร์มแก้ไขข้อมูลทีม -->
                            <div class="card card-primary h-100" style="min-height: 700px;">
                                <div class="card-header">
                                    <h3 class="card-title">Team Information</h3>
                                </div>
                                <div class="card-body">
                                    <form action="#" method="POST">
                                        <!-- CSRF Token -->
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($team['team_id']); ?>">

                                        <!-- Team Name -->
                                        <div class="form-group">
                                            <label for="team_name">Team Name<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-address-book"></i></span>
                                                </div>
                                                <input type="text" name="team_name" class="form-control" id="team_name" value="<?php echo htmlspecialchars($team['team_name']); ?>" required>
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        <div class="form-group">
                                            <label for="team_description">Description<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                </div>
                                                <input type="text" name="team_description" class="form-control" id="team_description" value="<?php echo htmlspecialchars($team['team_description']); ?>" required>
                                            </div>
                                        </div>

                                        <!-- Lead Team -->
                                        <div class="form-group">
                                            <label for="team_leader">Lead Team<span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="team_leader" name="team_leader" required>
                                                <option value="">เลือกหัวหน้าทีม</option>
                                                <?php foreach ($users as $user): ?>
                                                    <option value="<?php echo htmlspecialchars($user['user_id']); ?>" <?php if ($team['team_leader'] == $user['user_id']) echo 'selected'; ?>>
                                                        <?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-sm btn-success w-15" style="width: 120px;">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Footer -->
        <?php include '../../../include/footer.php'; ?>
    </div>
    <!-- JS for Dropdown Select2 -->
    <script>
        $(function() {
            $('.select2').select2();
        });
    </script>
</body>

</html>