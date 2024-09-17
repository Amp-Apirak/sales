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


// รับค่า user_id ที่ส่งมาจากหน้าอื่น
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
if (empty($user_id)) {
    echo "ไม่พบผู้ใช้งานที่ระบุ";
    exit;
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