<?php

//session_start and Config DB
include  '../../include/Add_session.php';

// ตรวจสอบว่ามีการส่ง user_id มาหรือไม่
if (isset($_GET['user_id'])) {
    // ถอดรหัส user_id ที่ได้รับจาก URL
    $encrypted_user_id = urldecode($_GET['user_id']);
    $user_id = decryptUserId($encrypted_user_id);

    // ตรวจสอบว่าถอดรหัสสำเร็จหรือไม่
    if ($user_id !== false) {
        // ดึงข้อมูลผู้ใช้จากฐานข้อมูลโดยใช้ user_id
        $sql = "SELECT * FROM users WHERE user_id = :user_id";
        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();

        // ตรวจสอบว่าพบข้อมูลผู้ใช้หรือไม่
        if (!$user) {
            echo "ไม่พบผู้ใช้ที่ต้องการแก้ไข";
            exit;
        }
    } else {
        echo "รหัสผู้ใช้ไม่ถูกต้อง";
        exit;
    }
} else {
    echo "ไม่มีการส่งรหัสผู้ใช้มา";
    exit;
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$sql = "SELECT * FROM users WHERE user_id = :user_id";
$stmt = $condb->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    echo "ไม่พบผู้ใช้งานที่ระบุ";
    exit;
}

// ตรวจสอบว่าผู้ใช้กดปุ่ม "อัปเดตข้อมูล" หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8');
    $last_name = htmlspecialchars($_POST['last_name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $position = htmlspecialchars($_POST['position'], ENT_QUOTES, 'UTF-8');
    $role = htmlspecialchars($_POST['role'], ENT_QUOTES, 'UTF-8');
    $company = htmlspecialchars($_POST['company'], ENT_QUOTES, 'UTF-8');
    $team_id = $_POST['team_id'];
    $password = $_POST['password'];

    // ตรวจสอบว่ามีการเปลี่ยนแปลงข้อมูลหรือไม่
    if (
        $first_name == $user['first_name'] &&
        $last_name == $user['last_name'] &&
        $email == $user['email'] &&
        $phone == $user['phone'] &&
        $position == $user['position'] &&
        $role == $user['role'] &&
        $company == $user['company'] &&
        $team_id == $user['team_id'] &&
        empty($password)
    ) {
        // ถ้าไม่มีการเปลี่ยนแปลงข้อมูล แสดง SweetAlert
        echo  '<script>
            setTimeout(function() {
                Swal.fire({
                    title: "Opp..",
                    text: "No data corrections found.",
                    icon: "error"
                }).then(function() {
                    window.location = "account.php"; //หน้าที่ต้องการให้กระโดดไป
                });
            }, 1000);
            </script>';
    } else {
        // ถ้ามีการเปลี่ยนแปลงข้อมูล อัปเดตข้อมูลผู้ใช้ในฐานข้อมูล
        try {
            if (!empty($password)) {
                // เข้ารหัสรหัสผ่านใหม่
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // อัปเดตรหัสผ่านและข้อมูลอื่นๆ
                $sql_update = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, 
                  position = :position, role = :role, company = :company, team_id = :team_id, password = :password WHERE user_id = :user_id";
                $stmt_update = $condb->prepare($sql_update);
                $stmt_update->bindParam(':password', $hashed_password);
            } else {
                // อัปเดตเฉพาะข้อมูลอื่นๆ โดยไม่เปลี่ยนแปลงรหัสผ่าน
                $sql_update = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, 
                  position = :position, role = :role, company = :company, team_id = :team_id WHERE user_id = :user_id";
                $stmt_update = $condb->prepare($sql_update);
            }

            $stmt_update->bindParam(':first_name', $first_name);
            $stmt_update->bindParam(':last_name', $last_name);
            $stmt_update->bindParam(':email', $email);
            $stmt_update->bindParam(':phone', $phone);
            $stmt_update->bindParam(':position', $position);
            $stmt_update->bindParam(':role', $role);
            $stmt_update->bindParam(':company', $company);
            $stmt_update->bindParam(':team_id', $team_id, PDO::PARAM_INT);
            $stmt_update->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            $stmt_update->execute();

            echo '<script>
                    Swal.fire({
                        title: "Success",
                        text: "ข้อมูลได้รับการอัปเดตเรียบร้อยแล้ว",
                        icon: "success"
                    }).then(function() {
                        window.location = "account.php"; 
                    });
                </script>';
        } catch (Exception $e) {
            echo '<script>
                    Swal.fire({
                        title: "Error",
                        text: "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage() . '",
                        icon: "error"
                    }).then(function() {
                        window.location = "account.php"; 
                    });
                </script>';
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
    <title>SalePipeline | Create Account</title>
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
                            <h1 class="m-0">Create Account</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Create Account v1</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

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

                                            <form action="#" method="POST" enctype="multipart/form-data">
                                                <!-- First name -->
                                                <div class="form-group">
                                                    <label for="first_name">First Name<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-address-book"></i></span>
                                                        </div>
                                                        <input type="text" name="first_name" class="form-control" id="first_name" placeholder="" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
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
                                                        <input type="text" name="last_name" class="form-control" id="last_name" placeholder="" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
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
                                                        <input type="text" class="form-control" name="phone" id="phone" data-inputmask='"mask": "(999) 999-9999"' data-mask value="<?php echo htmlspecialchars($user['phone']); ?>" required>
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
                                                        <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>">
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
                                                        <input type="text" name="position" class="form-control" id="position" placeholder="" value="<?php echo htmlspecialchars($user['position']); ?>">
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
                                                        <select class="form-control" id="team_id" name="team_id" required>
                                                            <?php
                                                            $sql_teams = "SELECT * FROM teams";
                                                            $query_teams = $condb->query($sql_teams);
                                                            while ($team = $query_teams->fetch()) {
                                                                $selected = $user['team_id'] == $team['team_id'] ? 'selected' : '';
                                                                echo "<option value='" . $team['team_id'] . "' $selected>" . $team['team_name'] . "</option>";
                                                            }
                                                            ?>
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
                                                        <input type="text" name="company" class="form-control" id="company" placeholder="" value="<?php echo htmlspecialchars($user['company']); ?>" required>
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
                                                    <select class="form-control" id="role" name="role" required>
                                                        <option value="Executive" <?php if ($user['role'] == 'Executive') echo 'selected'; ?>>Executive</option>
                                                        <option value="Sale Supervisor" <?php if ($user['role'] == 'Sale Supervisor') echo 'selected'; ?>>Sale Supervisor</option>
                                                        <option value="Seller" <?php if ($user['role'] == 'Seller') echo 'selected'; ?>>Seller</option>
                                                        <option value="Engineer" <?php if ($user['role'] == 'Engineer') echo 'selected'; ?>>Engineer</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="username">User Account</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    </div>
                                                    <input type="text" name="username" class="form-control" id="username" placeholder="Username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                                </div>
                                            </div>
                                            <!-- /.form-group -->

                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-unlock"></i></span>
                                                    </div>
                                                    <input type="text" name="password" class="form-control" id="password" placeholder="Password" value="">
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