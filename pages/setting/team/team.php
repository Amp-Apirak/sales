<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// ตรวจสอบการตั้งค่า Session เพื่อป้องกันกรณีที่ไม่ได้ล็อกอิน
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    // กรณีไม่มีการตั้งค่า Session หรือล็อกอิน
    header("Location: " . BASE_URL . "login.php");  // Redirect ไปยังหน้า login.php
    exit; // หยุดการทำงานของสคริปต์ปัจจุบันหลังจาก redirect
}

// ดึงข้อมูลจาก session
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];


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
                    window.location.href = '../../../index.php'; 
                });
            }, 100);
          </script>";
    exit();
}


// รับค่าการค้นหาจากฟอร์ม (method="GET")
$search_service = isset($_GET['searchservice']) ? trim($_GET['searchservice']) : '';

// Query พื้นฐานในการดึงข้อมูลทีมทั้งหมด
$sql_teams = "SELECT t.*, u.first_name AS leader_first_name, u.last_name AS leader_last_name, 
              c.first_name AS creator_first_name, c.last_name AS creator_last_name
              FROM teams t
              LEFT JOIN users u ON t.team_leader = u.user_id
              LEFT JOIN users c ON t.created_by = c.user_id
              WHERE 1=1";

// เพิ่มเงื่อนไขการค้นหาตามที่ผู้ใช้กรอกมา
if (!empty($search_service)) {
    $sql_teams .= " AND (t.team_name LIKE :search OR u.first_name LIKE :search OR u.last_name LIKE :search)";
}

// เตรียม statement และ bind ค่า
$stmt = $condb->prepare($sql_teams);

// ผูกค่าการค้นหากับ statement
if (!empty($search_service)) {
    $search_param = '%' . $search_service . '%';
    $stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
}

// Execute query เพื่อดึงข้อมูลทีม
$stmt->execute();
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "team"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Team Management</title>
    <?php include '../../../include/header.php'; ?>

    <!-- ใช้ฟอนต์ Noto Sans Thai กับ label -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <style>
        /* ใช้ฟอนต์ Noto Sans Thai กับ label */
        th,
        h1 {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 700;
            font-size: 16px;
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
        <?php include '../../../include/navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Team Management</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Team Management</li>
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

                            <!-- ส่วน Search -->
                            <section class="content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-info">
                                            <div class="card-header">
                                                <h3 class="card-title">ค้นหา</h3>
                                            </div>
                                            <div class="card-body">
                                                <form action="team.php" method="GET">
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" id="searchservice" name="searchservice" value="<?php echo htmlspecialchars($search_service, ENT_QUOTES, 'UTF-8'); ?>" placeholder="ค้นหา...">

                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <button type="submit" class="btn btn-primary" id="search" name="search">ค้นหา</button>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-5">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="card-footer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- //Section Search -->

                            <!-- Section ปุ่มเพิ่มข้อมูล -->
                            <?php if ($role == 'Executive') : ?>
                                <div class="col-md-12 pb-3">
                                    <a href="add_team.php" class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#addbtn">เพิ่มข้อมูล<i class=""></i></a>
                                    <!-- Add Team -->
                                    <?php include 'add_team.php'; ?>
                                    <!-- Add Team -->
                                </div><br>
                            <?php endif; ?>
                            <!-- //Section ปุ่มเพิ่มข้อมูล -->

                            <!-- Section ตารางแสดงผล -->
                            <!-- Section ตารางแสดงผล -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Team List</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Team Name</th>
                                                <th>Description</th>
                                                <th>Owner team</th>
                                                <th>Created By</th>
                                                <th>Created At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($teams) > 0): ?>
                                                <?php foreach ($teams as $index => $team) { ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td><?php echo htmlspecialchars($team['team_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($team['team_description'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($team['leader_first_name'] . " " . $team['leader_last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($team['creator_first_name'] . " " . $team['creator_last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($team['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php if ($role == 'Executive' || $role == 'Sale Supervisor') : ?>
                                                                <a href="edit_team.php?team_id=<?php echo urlencode(encryptUserId($team['team_id'])); ?>" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                                                <a href="delete_team.php?team_id=<?php echo urlencode(encryptUserId($team['team_id'])); ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">ไม่มีข้อมูลทีม</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Footer -->
        <?php include '../../../include/footer.php'; ?>
    </div>
    <!-- ./wrapper -->
     
    <!-- JS for Dropdown Select2 -->
    <script>
        $(function() {
            $('.select2').select2();
        });
    </script>


    <!-- DataTables -->
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