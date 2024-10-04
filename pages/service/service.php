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
<?php $menu = "service"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Service Management</title>
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
                            <h1 class="m-0">Service Management</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Service Management v1</li>
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

                            <!-- Section Search -->
                            <section class="content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-info">
                                            <div class="card-header ">
                                                <h3 class="card-title font1">
                                                    ค้นหา
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <form action="#" method="POST">
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group ">
                                                                <input type="text" class="form-control " id="searchservice" name="searchservice" value="<?php echo htmlspecialchars($search); ?>" placeholder="ค้นหา...">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group ">
                                                                <button type="submit" class="btn btn-primary" id="search" name="search">ค้นหา</button>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-5">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Service Category</label>
                                                                <select class="custom-select select2" name="company">
                                                                    <option value="">เลือก</option>
                                                                    <?php while ($company = $query_company->fetch()) { ?>
                                                                        <option value="<?php echo htmlspecialchars($company['company']); ?>"><?php echo htmlspecialchars($company['company']); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Category</label>
                                                                <select class="custom-select select2" name="team">
                                                                    <option value="">เลือก</option>
                                                                    <?php while ($team = $query_team->fetch()) { ?>
                                                                        <option value="<?php echo htmlspecialchars($team['team_name']); ?>"><?php echo htmlspecialchars($team['team_name']); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Sub-Category</label>
                                                                <select class="custom-select select2" name="role">
                                                                    <option value="">เลือก</option>
                                                                    <?php while ($role = $query_role->fetch()) { ?>
                                                                        <option value="<?php echo htmlspecialchars($role['role']); ?>"><?php echo htmlspecialchars($role['role']); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="card-footer">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                    </div>
                            </section>
                            <!-- //Section Search -->

                            <!-- Section ปุ่มเพิ่มข้อมูล -->
                            <div class="col-md-12 pb-3">
                                <a href="add_account.php" class="btn btn-success btn-sm float-right">เพิ่มข้อมูล<i class=""></i></a>
                            </div><br>
                            <!-- //Section ปุ่มเพิ่มข้อมูล -->

                            <!-- Section ตารางแสดงผล -->
                            <div class="card">
                                <div class="card-header">
                                    <div class="container-fluid">
                                        <h3 class="card-title">Account Management</h3>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap text-center">ชื่อผู้ใช้งาน</th>
                                                <th class="text-nowrap text-center">ชื่อ-สกุล</th>
                                                <th class="text-nowrap text-center">บริษัท</th>
                                                <th class="text-nowrap text-center">ทีม</th>
                                                <th class="text-nowrap text-center">บทบาท</th>
                                                <th class="text-nowrap text-center">ตำแหน่ง</th>
                                                <th class="text-nowrap text-center">เบอร์โทรศัทพ์</th>
                                                <th class="text-nowrap text-center">Email</th>
                                                <th class="text-nowrap text-center">วันที่สร้าง</th>
                                                <th class="text-nowrap text-center">Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($query_users as $user) { ?>
                                                <tr id="myTable">
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($user['username']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($user['company']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($user['team_name']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($user['role']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($user['position']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($user['phone']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($user['email']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($user['created_at']); ?></td>
                                                    <td>
                                                        <a href="edit_account.php?user_id=<?php echo urlencode(encryptUserId($user['user_id'])); ?>" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                                        <a href="" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>ชื่อผู้ใช้งาน</th>
                                                <th>ชื่อ-สกุล</th>
                                                <th>บริษัท</th>
                                                <th>ทีม</th>
                                                <th>บทบาท</th>
                                                <th>ตำแหน่ง</th>
                                                <th>เบอร์โทรศัทพ์</th>
                                                <th>Email</th>
                                                <th>วันที่สร้าง</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- //Section ตารางแสดงผล -->

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