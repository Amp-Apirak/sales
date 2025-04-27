<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ดึงข้อมูลจาก session ของผู้ใช้ที่เข้าสู่ระบบ
$role = $_SESSION['role'];  // บทบาทของผู้ใช้
$team_id = $_SESSION['team_id'];  // team_id ของผู้ใช้
$user_id = $_SESSION['user_id'];  // user_id ของผู้ใช้

// รับค่าการค้นหาจากฟอร์ม (method="GET")
$search_service = isset($_GET['searchservice']) ? trim($_GET['searchservice']) : '';

// Query พื้นฐานในการดึงข้อมูลลูกค้าทั้งหมด
$sql_customers = "SELECT DISTINCT c.*, u.first_name, u.last_name, t.team_name 
                  FROM customers c
                  LEFT JOIN users u ON c.created_by = u.user_id
                  LEFT JOIN teams t ON u.team_id = t.team_id
                  WHERE 1=1";

// เพิ่มเงื่อนไขกรณีผู้ใช้เป็น Sale Supervisor หรือผู้ใช้ทั่วไป
if ($role == 'Sale Supervisor') {
    // ผู้จัดการทีม เห็นลูกค้าของทีมตัวเอง
    $sql_customers .= " AND u.team_id = :team_id";
} elseif ($role == 'Seller') {
    // ผู้ใช้ทั่วไป (Seller) เห็นเฉพาะลูกค้าที่ตัวเองสร้าง
    $sql_customers .= " AND c.created_by = :user_id";
} elseif ($role != 'Executive') {
    // กรณีที่เป็นบทบาทอื่นๆ ที่ไม่ใช่ Executive
    $sql_customers .= " AND c.created_by = :user_id";
}

// เพิ่มเงื่อนไขการค้นหาข้อมูลตามที่ผู้ใช้กรอกมา
if (!empty($search_service)) {
    $sql_customers .= " AND (c.customer_name LIKE :search OR c.company LIKE :search OR c.phone LIKE :search OR c.position LIKE :search OR c.email LIKE :search)";
}


$sql_customers .= " ORDER BY c.created_at DESC";

// เตรียม statement และ bind ค่าต่างๆ เพื่อความปลอดภัย
$stmt = $condb->prepare($sql_customers);

// ผูกค่า team_id และ user_id ตามบทบาทของผู้ใช้
if ($role == 'Sale Supervisor') {
    $stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR); // เปลี่ยนเป็น PDO::PARAM_STR เพราะ team_id เป็น CHAR(36)
} elseif ($role == 'Seller' || $role != 'Executive') {
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR); // เปลี่ยนเป็น PDO::PARAM_STR เพราะ user_id เป็น CHAR(36)
}

// ผูกค่าการค้นหากับ statement
if (!empty($search_service)) {
    $search_param = '%' . $search_service . '%';
    $stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
}

// Execute query เพื่อดึงข้อมูลลูกค้า
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<?php $menu = "customer"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Customer Management</title>
    <?php include '../../include/header.php'; ?>

    <!-- /* ใช้ฟอนต์ Noto Sans Thai กับ label */ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <style>
        /* ใช้ฟอนต์ Noto Sans Thai กับ label */
        th,
        h1 {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 600;
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
        <?php include  '../../include/navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Customer Management</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Customer Management</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
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
                                                                <input type="text" class="form-control" id="searchservice" name="searchservice" value="<?php echo isset($_GET['searchservice']) ? htmlspecialchars($_GET['searchservice']) : ''; ?>" placeholder="ค้นหา...">
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
                                    <a href="add_customer.php" class="btn btn-success btn-sm">เพิ่มข้อมูลลูกค้า</a>
                                    <a href="import_customer.php" class="btn btn-info btn-sm mr-2">
                                        <i class="fas fa-file-import"></i> Import ข้อมูล
                                    </a>

                                </div>
                            </div><br>

                            <!-- Section ตารางแสดงผล -->
                            <div class="card">
                                <div class="card-header">
                                    <div class="container-fluid">
                                        <h3 class="card-title">Customer List</h3>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap text-center">Customer Name</th>
                                                <th class="text-nowrap text-center">Position</th>
                                                <th class="text-nowrap text-center">Phone</th>
                                                <th class="text-nowrap text-center">Email</th>
                                                <th class="text-nowrap text-center">Company</th>
                                                <th class="text-nowrap text-center">Created By</th>
                                                <th class="text-nowrap text-center">Created At</th>
                                                <th class="text-nowrap text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- แสดงข้อมูลลูกค้า -->
                                            <?php foreach ($customers as $customer) { ?>
                                                <tr>
                                                    <td class="text-nowrap"><?php echo !empty($customer['customer_name']) ? htmlspecialchars($customer['customer_name']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo !empty($customer['position']) ? htmlspecialchars($customer['position']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo !empty($customer['phone']) ? htmlspecialchars($customer['phone']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo !empty($customer['email']) ? htmlspecialchars($customer['email']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo !empty($customer['company']) ? htmlspecialchars($customer['company']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap">
                                                        <?php
                                                        $creator_name = trim($customer['first_name'] . ' ' . $customer['last_name']);
                                                        echo !empty($creator_name) ? htmlspecialchars($creator_name) : 'ไม่ระบุข้อมูล';
                                                        ?>
                                                    </td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($customer['created_at']); ?></td>
                                                    <td class="text-nowrap">
                                                        <a href="view_customer.php?id=<?php echo urlencode(encryptUserId($customer['customer_id'])); ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="edit_customer.php?customer_id=<?php echo urlencode(encryptUserId($customer['customer_id'])); ?>" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                                        <a href="delete_customer.php?customer_id=<?php echo $customer['customer_id']; ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Customer Name</th>
                                                <th>Position</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Company</th>
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
        <?php include '../../include/footer.php'; ?>
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