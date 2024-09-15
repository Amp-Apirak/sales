<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../config/condb.php');

// ตรวจสอบสิทธิ์ผู้ใช้งาน
if (isset($_SESSION['role']) && isset($_SESSION['team_id'])) {
    $role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
    $team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
} else {
    // กรณีที่ไม่มีการเข้าสู่ระบบหรือตัวแปร $_SESSION ไม่ถูกตั้งค่า
    echo "Error: Session variables not set. Please login again.";
    exit;
}

// ดึงข้อมูลจากฟอร์มค้นหา
$search = isset($_POST['searchservice']) ? $_POST['searchservice'] : '';
$search_company = isset($_POST['company']) ? $_POST['company'] : '';
$search_team = isset($_POST['team']) ? $_POST['team'] : '';
$search_role = isset($_POST['role']) ? $_POST['role'] : '';
$search_position = isset($_POST['position']) ? $_POST['position'] : '';

//การดึงข้อมูลจากฐานข้อมูลแต่ละคอลัมน์เพื่อสร้างตัวเลือก
// สำหรับบริษัท (Company)
$sql_company = "SELECT DISTINCT company FROM users";
$query_company = $condb->query($sql_company);

// สำหรับทีม (Team)
$sql_team = "SELECT DISTINCT team_name FROM teams";
$query_team = $condb->query($sql_team);

// สำหรับบทบาท (Role)
$sql_role = "SELECT DISTINCT role FROM users";
$query_role = $condb->query($sql_role);

// สำหรับตำแหน่ง (Position)
$sql_position = "SELECT DISTINCT position FROM users";
$query_position = $condb->query($sql_position);

// สร้าง SQL Query โดยพิจารณาจากการค้นหา
$sql_users = "SELECT u.username, u.first_name, u.last_name, u.company, u.role, t.team_name, u.position, u.phone, u.email, u.created_at
              FROM users u
              LEFT JOIN teams t ON u.team_id = t.team_id
              WHERE 1=1";

// กรณีที่ role ไม่ใช่ Executive ให้แสดงเฉพาะข้อมูลทีมของผู้ใช้เอง
if ($role !== 'Executive') {
    $sql_users .= " AND u.team_id = :team_id";
}

// เพิ่มเงื่อนไขการค้นหาตามฟิลด์ที่ระบุ
if (!empty($search)) {
    $sql_users .= " AND (u.username LIKE :search OR u.first_name LIKE :search OR u.last_name LIKE :search OR u.phone LIKE :search OR u.email LIKE :search)";
}
if (!empty($search_company)) {
    $sql_users .= " AND u.company = :search_company";
}
if (!empty($search_team)) {
    $sql_users .= " AND t.team_name = :search_team";
}
if (!empty($search_role)) {
    $sql_users .= " AND u.role = :search_role";
}
if (!empty($search_position)) {
    $sql_users .= " AND u.position = :search_position";
}

// เตรียม statement
$stmt = $condb->prepare($sql_users);

// ทำการ bind ค่าต่างๆ
if ($role !== 'Executive') {
    $stmt->bindParam(':team_id', $team_id);
}
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param);
}
if (!empty($search_company)) {
    $stmt->bindParam(':search_company', $search_company);
}
if (!empty($search_team)) {
    $stmt->bindParam(':search_team', $search_team);
}
if (!empty($search_role)) {
    $stmt->bindParam(':search_role', $search_role);
}
if (!empty($search_position)) {
    $stmt->bindParam(':search_position', $search_position);
}

// Execute query
$stmt->execute();
$query_users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "account"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Account Management</title>
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
                            <h1 class="m-0">Account Management</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Account Management v1</li>
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
                                                                <label>บริษัท</label>
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
                                                                <label>ทีม</label>
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
                                                                <label>บทบาท</label>
                                                                <select class="custom-select select2" name="role">
                                                                    <option value="">เลือก</option>
                                                                    <?php while ($role = $query_role->fetch()) { ?>
                                                                        <option value="<?php echo htmlspecialchars($role['role']); ?>"><?php echo htmlspecialchars($role['role']); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>ตำแหน่ง</label>
                                                                <select class="custom-select select2" name="position">
                                                                    <option value="">เลือก</option>
                                                                    <?php while ($position = $query_position->fetch()) { ?>
                                                                        <option value="<?php echo htmlspecialchars($position['position']); ?>"><?php echo htmlspecialchars($position['position']); ?></option>
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
                                <a href="add_user.php" class="btn btn-success btn-sm float-right">เพิ่มข้อมูล<i class=""></i></a>
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
                                        </thead>

                                        <tbody>
                                            <?php foreach ($query_users as $user) { ?>
                                                <tr id="myTable">
                                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['company']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['team_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['position']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                                    <td>
                                                        <a href="" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>
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
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
</body>

</html>
