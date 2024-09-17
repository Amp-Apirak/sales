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

$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$created_by = $_SESSION['user_id']; // ดึง user_id ของผู้สร้างจาก session

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
                                    window.location.href = 'account.php';
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

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Update Profiles</title>
    <?php include  '../../include/header.php'; ?>


</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <!-- Main Sidebar Container -->
        <!-- Preloader -->
        <?php include  '../../include/navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Update Profiles</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Update Profiles v1</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- ใส่ SweetAlert CSS -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- เพิ่มข้อมูล -->
                            <div class="row">
                                <!-- /.col (left) -->
                                <div class="col-md-6">
                                    <div class="card card-primary h-100">
                                        <div class="card-header">
                                            <h3 class="card-title">Account Descriptions</h3>
                                        </div>
                                        <div class="card-body">

                                            <form action="#" method="POST"  enctype="multipart/form-data">
                                                <!-- First name -->
                                                <div class="form-group">
                                                    <label for="first_name">First Name<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-address-book"></i></span>
                                                        </div>
                                                        <input type="text" name="first_name" class="form-control" id="first_name" placeholder="" value="" required >
                                                    </div>
                                                </div>
                                                <!-- /.First name -->

                                                <!-- Last name -->
                                                <div class="form-group">
                                                    <label for="last_name">Last Name<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-address-book"></i></span>
                                                        </div>
                                                        <input type="text" name="last_name" class="form-control" id="last_name" placeholder="" value="" required>
                                                    </div>
                                                </div>
                                                <!-- /.Last name -->

                                                <!-- phone -->
                                                <div class="form-group">
                                                    <label for="phone">Phone Number</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" name="phone" id="phone" data-inputmask='"mask": "(999) 999-9999"' data-mask value="" required>
                                                    </div>
                                                </div>
                                                <!-- /.phone -->

                                                <p>
                                                    <!-- email -->
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Email</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                        </div>
                                                        <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="" >
                                                    </div>
                                                </div>
                                                <!-- /.email -->

                                                <!-- position -->
                                                <div class="form-group">
                                                    <label for="position">Position</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-star"></i></span>
                                                        </div>
                                                        <input type="text" name="position" class="form-control" id="position" placeholder="" value="" >
                                                    </div>
                                                </div>
                                                <!-- /.position -->

                                                <!-- team -->
                                                <div class="form-group">
                                                    <label for="team_id">Team<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-users"></i></span>
                                                        </div>
                                                        <select class="form-control select2" id="team_id" name="team_id" required tyle="width: 100%;">
                                                            <option value="">Select Team</option>
                                                            <?php foreach ($query_teams as $team) { ?>
                                                                <option value="<?php echo $team['team_id']; ?>"><?php echo $team['team_name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- /.team -->

                                                <!-- Company -->
                                                <div class="form-group">
                                                    <label for="company">Company<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                        </div>
                                                        <input type="text" name="company" class="form-control" id="company" placeholder="" value="" required>
                                                    </div>
                                                </div>
                                                <!-- /.Company -->

                                        </div>
                                        <div class="card-footer">
                                            Visit <a href="https://getdatepicker.com/5-4/">tempusdominus </a> for more
                                            examples and information about
                                            the plugin.
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.card -->
                                <!-- /.col (right) -->
                                <div class="col-md-6">
                                    <div class="card card-warning h-100">
                                        <div class="card-header">
                                            <h3 class="card-title">Setting Account</h3>
                                        </div>

                                        <div class="card-body">

                                            <!-- /.form-group -->
                                            <div class="form-group">
                                                <label>Role<span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-key"></i></span> <!-- เพิ่มไอคอน -->
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

                                            <div class="form-group">
                                                <label for="username">User Account<span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    </div>
                                                    <input type="text" name="username" class="form-control" id="username" placeholder="Username" value="" required>
                                                </div>
                                            </div>
                                            <!-- /.form-group -->

                                            <div class="form-group">
                                                <label for="password">Password<span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-unlock"></i></span>
                                                    </div>
                                                    <input type="text" name="password" class="form-control" id="password" placeholder="Password" value="" required>
                                                </div>
                                            </div>
                                            <!-- /.form-group -->

                                            <!-- Date range -->
                                            <div class="form-group mt-5">
                                                <button type="submit" name="submit" value="submit" class="btn btn-sm btn-success w-25">Save</button>
                                            </div>
                                            <!-- /.form group -->

                                            </form>

                                        </div>
                                        <div class="card-footer">
                                            Visit <a href="https://getdatepicker.com/5-4/">tempusdominus </a> for more
                                            examples and information about
                                            the plugin.
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col (right) -->
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- // include footer -->
        <?php include  '../../include/footer.php'; ?>
    </div>
    <!-- ./wrapper -->
    <script>
        // Dropdown Select2
        $(function() {
            // Initialize Select2 Elements
            $('.select2').select2()

            // Initialize Select2 Elements with Bootstrap4 theme
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
        });

    </script>
</body>

</html>