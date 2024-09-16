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

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- เพิ่มข้อมูล -->
                            <div class="row">
                                <!-- /.col (left) -->
                                <div class="col-md-12">
                                    <div class="card card-primary">
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
                                                        <input type="text" name="first_name" class="form-control" id="first_name" placeholder="" value="" required>
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
                                                        <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="" required>
                                                    </div>
                                                </div>
                                                <!-- /.email -->

                                                <!-- position -->
                                                <div class="form-group">
                                                    <label for="position">Position<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-star"></i></span>
                                                        </div>
                                                        <input type="text" name="position" class="form-control" id="position" placeholder="" value="" required>
                                                    </div>
                                                </div>
                                                <!-- /.position -->


                                                <!-- team -->
                                                <div class="form-group">
                                                    <label>Team<span class="text-danger">*</span></label>
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                                                        <select class="form-control select2" name="team" required style="width: 100%;">
                                                            <option value=""></option>
                                                            <option>Innovation</option>
                                                            <option>Service Solution</option>
                                                            <option>Service bank</option>
                                                            <option>Infrastructure</option>
                                                            <option>Sale</option>
                                                            <option>Accounting</option>
                                                            <option>Stock</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- /.team -->

                                                <!-- Company -->
                                                <div class="form-group">
                                                    <label for="first_name">Company<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                        </div>
                                                        <input type="text" name="first_name" class="form-control" id="first_name" placeholder="" value="" required>
                                                    </div>
                                                </div>
                                                <!-- /.Company -->


                                                <div class="form-group">
                                                    <label for="username">User Account<span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                        </div>
                                                        <input type="text" name="username" class="form-control" id="username" placeholder="" value="" required>
                                                    </div>
                                                </div>
                                                <!-- /.form-group -->

                                                <!-- Date range -->
                                                <div class="form-group mt-5">
                                                    <button type="submit" name="submit" value="submit" class="btn btn-success">Save</button>
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
</body>

</html>