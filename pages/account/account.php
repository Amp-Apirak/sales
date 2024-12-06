<?php
//session_start and Config DB
include  '../../include/Add_session.php';

// ตรวจสอบว่า User ได้ login แล้วหรือยัง และตรวจสอบ Role
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Executive' && $_SESSION['role'] != 'Sale Supervisor')) {
    // ถ้า Role ไม่ใช่ Executive หรือ Sale Supervisor ให้ redirect ไปยังหน้าอื่น เช่น หน้า Dashboard
    header("Location: " . BASE_URL . "index.php"); // เปลี่ยนเส้นทางไปหน้า Dashboard
    exit(); // หยุดการทำงานของสคริปต์
}

$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$user_id = $_SESSION['user_id'];  // ดึง user_id ของผู้ใช้จาก session

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
$sql_users = "SELECT u.user_id, u.username, u.first_name, u.last_name, u.company, u.role, t.team_name, u.position, u.phone, u.email, u.created_at
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

$sql_users .= " ORDER BY u.created_at DESC";

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

    <!-- /* ใช้ฟอนต์ Noto Sans Thai กับ label */ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <style>
        /* ใช้ฟอนต์ Noto Sans Thai กับ label */
        th,
        h1 {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 700;
            /* ปรับระดับน้ำหนักของฟอนต์ */
            font-size: 14px;
            color: #333;
        }

        .custom-th {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 600;
            font-size: 18px;
            color: #FF5733;
        }
    </style>
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
                            <!-- Section ปุ่มเพิ่มข้อมูล -->
                            <div class="col-md-12 pb-3">
                                <div class="btn-group float-right">
                                    <a href="import_account.php" class="btn btn-info btn-sm mr-2">
                                        <i class="fas fa-file-import"></i> Import Excel/CSV
                                    </a>
                                    <a href="add_account.php" class="btn btn-success btn-sm">เพิ่มข้อมูล</a>
                                </div>
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
                                                        <?php
                                                        // ตรวจสอบเงื่อนไขการแสดงปุ่มแก้ไข
                                                        $showEditButton = true;

                                                        // ตรวจสอบว่า Username ของผู้ใช้ในตารางเป็น Admin หรือไม่
                                                        if ($user['username'] === 'Admin') {
                                                            $showEditButton = false; // ถ้า username เป็น Admin ให้ซ่อนปุ่ม
                                                        }

                                                        // ตรวจสอบว่าเป็น Sale Supervisor หรือไม่
                                                        if ($_SESSION['role'] === 'Sale Supervisor') {
                                                            // ถ้า role ของผู้ใช้ที่ล็อกอินคือ Sale Supervisor และผู้ใช้ที่กำลังแสดงอยู่เป็น Executive หรือเป็น Sale Supervisor (ที่ไม่ใช่ตัวเอง)
                                                            if ($user['role'] === 'Executive' || ($user['role'] === 'Sale Supervisor' && $user['user_id'] !== $_SESSION['user_id'])) {
                                                                $showEditButton = false;
                                                            }
                                                        }

                                                        // ถ้าเงื่อนไขการแสดงปุ่มแก้ไขผ่าน ให้แสดงปุ่มแก้ไข
                                                        if ($showEditButton): ?>
                                                            <a href="edit_account.php?user_id=<?php echo urlencode(encryptUserId($user['user_id'])); ?>" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                                        <?php endif; ?>

                                                        <!-- ปุ่มลบจะถูกซ่อนถ้าเป็น Admin -->
                                                        <?php if ($user['username'] !== 'Admin'): ?>
                                                            <a href="" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                                        <?php endif; ?>
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
                "order": [], // ปิดการเรียงลำดับอัตโนมัติ
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>
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