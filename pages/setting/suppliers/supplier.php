<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// ดึงข้อมูลจาก session ของผู้ใช้ที่เข้าสู่ระบบ
$role = $_SESSION['role'];  // บทบาทของผู้ใช้
$team_id = $_SESSION['team_id'];  // team_id ของผู้ใช้
$user_id = $_SESSION['user_id'];  // user_id ของผู้ใช้

// รับค่าการค้นหาจากฟอร์ม (method="GET")
$search_supplier = isset($_GET['searchsupplier']) ? trim($_GET['searchsupplier']) : '';

// Query พื้นฐานในการดึงข้อมูล supplier ทั้งหมด
$sql_suppliers = "SELECT DISTINCT s.*, u.first_name, u.last_name, t.team_name 
                  FROM suppliers s
                  LEFT JOIN users u ON s.created_by = u.user_id
                  LEFT JOIN teams t ON u.team_id = t.team_id
                  WHERE 1=1";

// เพิ่มเงื่อนไขกรณีผู้ใช้เป็น Sale Supervisor หรือผู้ใช้ทั่วไป
if ($role == 'Sale Supervisor') {
    // ผู้จัดการทีม เห็น supplier ของทีมตัวเอง
    $sql_suppliers .= " AND u.team_id = :team_id";
} elseif ($role == 'Seller') {
    // ผู้ใช้ทั่วไป (Seller) เห็นเฉพาะ supplier ที่ตัวเองสร้าง
    $sql_suppliers .= " AND s.created_by = :user_id";
} elseif ($role != 'Executive') {
    // กรณีที่เป็นบทบาทอื่นๆ ที่ไม่ใช่ Executive
    $sql_suppliers .= " AND s.created_by = :user_id";
}

// เพิ่มเงื่อนไขการค้นหาข้อมูลตามที่ผู้ใช้กรอกมา
if (!empty($search_supplier)) {
    $sql_suppliers .= " AND (s.supplier_name LIKE :search OR s.company LIKE :search OR s.phone LIKE :search OR s.position LIKE :search OR s.email LIKE :search)";
}

$sql_suppliers .= " ORDER BY s.created_at DESC";

// เตรียม statement และ bind ค่าต่างๆ เพื่อความปลอดภัย
$stmt = $condb->prepare($sql_suppliers);

// ผูกค่า team_id และ user_id ตามบทบาทของผู้ใช้
if ($role == 'Sale Supervisor') {
    $stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR); // เปลี่ยนเป็น PDO::PARAM_STR เพราะ team_id เป็น CHAR(36)
} elseif ($role == 'Seller' || $role != 'Executive') {
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR); // เปลี่ยนเป็น PDO::PARAM_STR เพราะ user_id เป็น CHAR(36)
}

// ผูกค่าการค้นหากับ statement
if (!empty($search_supplier)) {
    $search_param = '%' . $search_supplier . '%';
    $stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
}

// Execute query เพื่อดึงข้อมูล supplier
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "supplier"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Supplier Management</title>
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
            font-weight: 600;
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
        <?php include  '../../../include/navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Supplier Management</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Supplier Management</li>
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
                                                <form action="#" method="GET"> <!-- เปลี่ยนเป็น GET -->
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group ">
                                                                <input type="text" class="form-control" id="searchsupplier" name="searchsupplier" value="<?php echo isset($_GET['searchsupplier']) ? htmlspecialchars($_GET['searchsupplier']) : ''; ?>" placeholder="ค้นหา...">
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
                                <div class="btn-group float-right">
                                    <a href="add_supplier.php" class="btn btn-success btn-sm">เพิ่มข้อมูล Supplier</a>
                                    <a href="import_supplier.php" class="btn btn-info btn-sm mr-2">
                                        <i class="fas fa-file-import"></i> Import ข้อมูล
                                    </a>
                                </div>
                            </div><br>

                            <!-- Section ตารางแสดงผล -->
                            <div class="card">
                                <div class="card-header">
                                    <div class="container-fluid">
                                        <h3 class="card-title">Supplier List</h3>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap text-center">Supplier Name</th>
                                                <th class="text-nowrap text-center">Position</th>
                                                <th class="text-nowrap text-center">Phone</th>
                                                <th class="text-nowrap text-center">Email</th>
                                                <th class="text-nowrap text-center">Company</th>
                                                <th class="text-nowrap text-center">Office Phone</th>
                                                <th class="text-nowrap text-center">Extension</th>
                                                <th class="text-nowrap text-center">Address</th>
                                                <th class="text-nowrap text-center">Remark</th>
                                                <th class="text-nowrap text-center">Created By</th>
                                                <th class="text-nowrap text-center">Created At</th>
                                                <th class="text-nowrap text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- แสดงข้อมูล supplier -->
                                            <?php foreach ($suppliers as $supplier) { ?>
                                                <tr>
                                                    <td class="text-nowrap"><?php echo !empty($supplier['supplier_name']) ? htmlspecialchars($supplier['supplier_name']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo !empty($supplier['position']) ? htmlspecialchars($supplier['position']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo !empty($supplier['phone']) ? htmlspecialchars($supplier['phone']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo !empty($supplier['email']) ? htmlspecialchars($supplier['email']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo !empty($supplier['company']) ? htmlspecialchars($supplier['company']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo !empty($supplier['office_phone']) ? htmlspecialchars($supplier['office_phone']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo !empty($supplier['extension']) ? htmlspecialchars($supplier['extension']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo !empty($supplier['address']) ? htmlspecialchars($supplier['address']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo !empty($supplier['remark']) ? htmlspecialchars($supplier['remark']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap">
                                                        <?php
                                                        $creator_name = trim($supplier['first_name'] . ' ' . $supplier['last_name']);
                                                        echo !empty($creator_name) ? htmlspecialchars($creator_name) : 'ไม่ระบุข้อมูล';
                                                        ?>
                                                    </td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($supplier['created_at']); ?></td>
                                                    <td class="text-nowrap">
                                                        <a href="view_supplier.php?id=<?php echo urlencode(encryptUserId($supplier['supplier_id'])); ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="edit_supplier.php?supplier_id=<?php echo urlencode(encryptUserId($supplier['supplier_id'])); ?>" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                                        <a href="delete_supplier.php?supplier_id=<?php echo $supplier['supplier_id']; ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Supplier Name</th>
                                                <th>Position</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Company</th>
                                                <th>Office Phone</th>
                                                <th>Extension</th>
                                                <th>Address</th>
                                                <th>Remark</th>
                                                <th>Created By</th>
                                                <th>Created At</th>
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
        </div><!-- /.container-fluid -->

        <!-- /.content-wrapper -->
        <!-- Footer -->
        <?php include '../../../include/footer.php'; ?>
    </div>
    <!-- ./wrapper -->

    <!-- DataTables -->
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "order": [
                    [5, "desc"]
                ], // เรียงตามคอลัมน์ที่ 5 (created_at) จากมากไปน้อย
                "pageLength": 10 // แสดง 10 รายการต่อหน้า
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>
</body>

</html>