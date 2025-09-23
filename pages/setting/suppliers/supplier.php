<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// ดึงข้อมูลจาก session ของผู้ใช้ที่เข้าสู่ระบบ
$role = $_SESSION['role'];
$team_ids = $_SESSION['team_ids'] ?? [];
$user_id = $_SESSION['user_id'];

// รับค่าการค้นหาจากฟอร์ม
$search_supplier = trim($_GET['searchsupplier'] ?? '');

// --- 1. Build Query and Parameters ---
$params = [];
$sql_suppliers = "SELECT DISTINCT s.*, u.first_name, u.last_name, t.team_name 
                  FROM suppliers s
                  LEFT JOIN users u ON s.created_by = u.user_id
                  LEFT JOIN user_teams ut ON u.user_id = ut.user_id AND ut.is_primary = 1
                  LEFT JOIN teams t ON ut.team_id = t.team_id";

$where_conditions = [];

// Role-based filtering
if ($role == 'Sale Supervisor') {
    if (!empty($team_ids)) {
        $team_placeholders = [];
        foreach ($team_ids as $key => $id) {
            $p = ':team_id_' . $key;
            $team_placeholders[] = $p;
            $params[$p] = $id;
        }
        $in_clause = implode(',', $team_placeholders);
        // Filter suppliers created by users who are in the supervisor's teams
        $where_conditions[] = "s.created_by IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id IN ($in_clause))";
    } else {
        $where_conditions[] = "1=0"; // No teams, show no suppliers
    }
} elseif ($role == 'Seller' || $role != 'Executive') {
    $where_conditions[] = "s.created_by = :user_id";
    $params[':user_id'] = $user_id;
}

// Search filtering
if (!empty($search_supplier)) {
    $where_conditions[] = "(
        s.supplier_name LIKE :search OR 
        s.company LIKE :search OR 
        s.phone LIKE :search OR 
        s.position LIKE :search OR 
        s.email LIKE :search
    )";
    $params[':search'] = "%$search_supplier%";
}

if (!empty($where_conditions)) {
    $sql_suppliers .= " WHERE " . implode(' AND ', $where_conditions);
}

$sql_suppliers .= " ORDER BY s.created_at DESC";

// --- 2. Execute Query ---
$stmt = $condb->prepare($sql_suppliers);
$stmt->execute($params);
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
                                        <!-- filepath: c:\xampp\htdocs\sales\pages\setting\suppliers\supplier.php -->
                                        <tbody>
                                            <!-- แสดงข้อมูล supplier -->
                                            <?php foreach ($suppliers as $supplier) { ?>
                                                <tr style="cursor: pointer;">
                                                    <td class="text-nowrap">
                                                        <?php echo !empty($supplier['supplier_name']) ? htmlspecialchars($supplier['supplier_name']) : 'ไม่ระบุข้อมูล'; ?>
                                                    </td>
                                                    <td class="text-nowrap" onclick="window.location.href='view_supplier.php?id=<?php echo urlencode(encryptUserId($supplier['supplier_id'])); ?>'">
                                                        <?php echo !empty($supplier['position']) ? htmlspecialchars($supplier['position']) : 'ไม่ระบุข้อมูล'; ?>
                                                    </td>
                                                    <td class="text-nowrap" onclick="window.location.href='view_supplier.php?id=<?php echo urlencode(encryptUserId($supplier['supplier_id'])); ?>'">
                                                        <?php echo !empty($supplier['phone']) ? htmlspecialchars($supplier['phone']) : 'ไม่ระบุข้อมูล'; ?>
                                                    </td>
                                                    <td class="text-nowrap" onclick="window.location.href='view_supplier.php?id=<?php echo urlencode(encryptUserId($supplier['supplier_id'])); ?>'">
                                                        <?php echo !empty($supplier['email']) ? htmlspecialchars($supplier['email']) : 'ไม่ระบุข้อมูล'; ?>
                                                    </td>
                                                    <td class="text-nowrap" onclick="window.location.href='view_supplier.php?id=<?php echo urlencode(encryptUserId($supplier['supplier_id'])); ?>'">
                                                        <?php echo !empty($supplier['company']) ? htmlspecialchars($supplier['company']) : 'ไม่ระบุข้อมูล'; ?>
                                                    </td>
                                                    <td class="text-nowrap" onclick="window.location.href='view_supplier.php?id=<?php echo urlencode(encryptUserId($supplier['supplier_id'])); ?>'">
                                                        <?php echo !empty($supplier['office_phone']) ? htmlspecialchars($supplier['office_phone']) : 'ไม่ระบุข้อมูล'; ?>
                                                    </td>
                                                    <td class="text-nowrap" onclick="window.location.href='view_supplier.php?id=<?php echo urlencode(encryptUserId($supplier['supplier_id'])); ?>'">
                                                        <?php echo !empty($supplier['extension']) ? htmlspecialchars($supplier['extension']) : 'ไม่ระบุข้อมูล'; ?>
                                                    </td>
                                                    <td class="text-nowrap" onclick="window.location.href='view_supplier.php?id=<?php echo urlencode(encryptUserId($supplier['supplier_id'])); ?>'">
                                                        <?php echo !empty($supplier['address']) ? htmlspecialchars($supplier['address']) : 'ไม่ระบุข้อมูล'; ?>
                                                    </td>
                                                    <td class="text-nowrap" onclick="window.location.href='view_supplier.php?id=<?php echo urlencode(encryptUserId($supplier['supplier_id'])); ?>'">
                                                        <?php echo !empty($supplier['remark']) ? htmlspecialchars($supplier['remark']) : 'ไม่ระบุข้อมูล'; ?>
                                                    </td>
                                                    <td class="text-nowrap" onclick="window.location.href='view_supplier.php?id=<?php echo urlencode(encryptUserId($supplier['supplier_id'])); ?>'">
                                                        <?php
                                                        $creator_name = trim($supplier['first_name'] . ' ' . $supplier['last_name']);
                                                        echo !empty($creator_name) ? htmlspecialchars($creator_name) : 'ไม่ระบุข้อมูล';
                                                        ?>
                                                    </td>
                                                    <td class="text-nowrap" onclick="window.location.href='view_supplier.php?id=<?php echo urlencode(encryptUserId($supplier['supplier_id'])); ?>'">
                                                        <?php echo htmlspecialchars($supplier['created_at']); ?>
                                                    </td>
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