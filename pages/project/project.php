<?php
// session_start and Config DB
include '../../include/Add_session.php';

// ตรวจสอบการ login และดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$created_by = $_SESSION['user_id'];


// รับค่าจากฟอร์มการค้นหา
$search_service = isset($_POST['searchservice']) ? trim($_POST['searchservice']) : '';
$search_product = isset($_POST['product']) ? $_POST['product'] : '';
$search_status = isset($_POST['status']) ? $_POST['status'] : '';
$search_creator = isset($_POST['creator']) ? $_POST['creator'] : '';
$search_customer = isset($_POST['customer']) ? $_POST['customer'] : '';
$search_year = isset($_POST['year']) ? $_POST['year'] : '';

// ดึงข้อมูลจากฐานข้อมูลสำหรับ Dropdown list
$products = $condb->query("SELECT DISTINCT product FROM projects")->fetchAll(PDO::FETCH_ASSOC);
$statuses = $condb->query("SELECT DISTINCT status FROM projects")->fetchAll(PDO::FETCH_ASSOC);
$creators = $condb->query("SELECT DISTINCT created_by, first_name, last_name FROM users")->fetchAll(PDO::FETCH_ASSOC);
$customers = $condb->query("SELECT DISTINCT customer_id, customer_name FROM customers")->fetchAll(PDO::FETCH_ASSOC);
$years = $condb->query("SELECT DISTINCT YEAR(created_at) AS year FROM projects")->fetchAll(PDO::FETCH_ASSOC);


// กำหนด where clause ตามบทบาทผู้ใช้
$where_clause = "WHERE 1=1";
if ($role == 'Sale Supervisor') {
    // หากเป็น Sale Supervisor ให้แสดงเฉพาะข้อมูลทีมตัวเอง
    $where_clause .= " AND p.team_id = :team_id";
} elseif ($role != 'Executive') {
    // หากเป็น Seller หรือบทบาทอื่น ๆ ให้แสดงเฉพาะข้อมูลที่ตัวเองสร้าง
    $where_clause .= " AND p.created_by = :created_by";
}

// เพิ่มเงื่อนไขการค้นหา
if (!empty($search_service)) {
    $where_clause .= " AND (p.project_name LIKE :search_service OR c.customer_name LIKE :search_service)";
}
if (!empty($search_product)) {
    $where_clause .= " AND p.product = :search_product";
}
if (!empty($search_status)) {
    $where_clause .= " AND p.status = :search_status";
}
if (!empty($search_creator)) {
    $where_clause .= " AND p.created_by = :search_creator";
}
if (!empty($search_customer)) {
    $where_clause .= " AND p.customer_id = :search_customer";
}
if (!empty($search_year)) {
    $where_clause .= " AND YEAR(p.created_at) = :search_year";
}

// 1. ดึงจำนวนโปรเจ็กต์ทั้งหมด
$sql_total_projects = "
    SELECT COUNT(p.project_id) as total_projects
    FROM projects p
    LEFT JOIN customers c ON p.customer_id = c.customer_id
    $where_clause
";
$stmt_total_projects = $condb->prepare($sql_total_projects);

// ผูกค่ากับ statement
if ($role == 'Sale Supervisor') {
    $stmt_total_projects->bindParam(':team_id', $team_id, PDO::PARAM_INT);
} elseif ($role != 'Executive') {
    $stmt_total_projects->bindParam(':created_by', $created_by, PDO::PARAM_INT);
}
if (!empty($search_service)) {
    $search_param = '%' . $search_service . '%';
    $stmt_total_projects->bindParam(':search', $search_param, PDO::PARAM_STR);
}

// Execute query และดึงผลลัพธ์
$stmt_total_projects->execute();
$total_projects = $stmt_total_projects->fetchColumn();

// 2. ดึงจำนวนผลิตภัณฑ์ทั้งหมด (Product)
$sql_total_products = "
    SELECT COUNT(DISTINCT p.product) as total_products
    FROM projects p
    $where_clause
";
$stmt_total_products = $condb->prepare($sql_total_products);

// ผูกค่ากับ statement
if ($role == 'Sale Supervisor') {
    $stmt_total_products->bindParam(':team_id', $team_id, PDO::PARAM_INT);
} elseif ($role != 'Executive') {
    $stmt_total_products->bindParam(':created_by', $created_by, PDO::PARAM_INT);
}
if (!empty($search_service)) {
    $stmt_total_products->bindParam(':search', $search_param, PDO::PARAM_STR);
}

// Execute query และดึงผลลัพธ์
$stmt_total_products->execute();
$total_products = $stmt_total_products->fetchColumn();

// 3. ดึงยอดขายรวมทั้งหมด (Sale Price with VAT)
$sql_total_sale = "
    SELECT SUM(p.sale_vat) as total_sale
    FROM projects p
    $where_clause
";
$stmt_total_sale = $condb->prepare($sql_total_sale);

// ผูกค่ากับ statement
if ($role == 'Sale Supervisor') {
    $stmt_total_sale->bindParam(':team_id', $team_id, PDO::PARAM_INT);
} elseif ($role != 'Executive') {
    $stmt_total_sale->bindParam(':created_by', $created_by, PDO::PARAM_INT);
}
if (!empty($search_service)) {
    $stmt_total_sale->bindParam(':search', $search_param, PDO::PARAM_STR);
}

// Execute query และดึงผลลัพธ์
$stmt_total_sale->execute();
$total_sale = $stmt_total_sale->fetchColumn();

// 4. ดึงต้นทุนรวมทั้งหมด (Cost Price with VAT)
$sql_total_cost = "
    SELECT SUM(p.cost_vat) as total_cost
    FROM projects p
    $where_clause
";
$stmt_total_cost = $condb->prepare($sql_total_cost);

// ผูกค่ากับ statement
if ($role == 'Sale Supervisor') {
    $stmt_total_cost->bindParam(':team_id', $team_id, PDO::PARAM_INT);
} elseif ($role != 'Executive') {
    $stmt_total_cost->bindParam(':created_by', $created_by, PDO::PARAM_INT);
}
if (!empty($search_service)) {
    $stmt_total_cost->bindParam(':search', $search_param, PDO::PARAM_STR);
}

// Execute query และดึงผลลัพธ์
$stmt_total_cost->execute();
$total_cost = $stmt_total_cost->fetchColumn();

// 5. ดึงจำนวนผู้สร้าง (Create By) ที่แตกต่างกัน
$sql_total_creators = "
    SELECT COUNT(DISTINCT p.created_by) as total_creators
    FROM projects p
    $where_clause
";
$stmt_total_creators = $condb->prepare($sql_total_creators);

// ผูกค่ากับ statement
if ($role == 'Sale Supervisor') {
    $stmt_total_creators->bindParam(':team_id', $team_id, PDO::PARAM_INT);
} elseif ($role != 'Executive') {
    $stmt_total_creators->bindParam(':created_by', $created_by, PDO::PARAM_INT);
}
if (!empty($search_service)) {
    $stmt_total_creators->bindParam(':search', $search_param, PDO::PARAM_STR);
}

// Execute query และดึงผลลัพธ์
$stmt_total_creators->execute();
$total_creators = $stmt_total_creators->fetchColumn();

// 6. ดึงข้อมูลที่จะแสดงในตารางโปรเจ็กต์
// เตรียม query สำหรับดึงข้อมูลโปรเจกต์
$sql_projects = "
    SELECT p.*, u.first_name, u.last_name, c.customer_name
    FROM projects p
    LEFT JOIN customers c ON p.customer_id = c.customer_id
    LEFT JOIN users u ON p.created_by = u.user_id
    $where_clause
    ORDER BY p.project_id DESC
";

$stmt_projects = $condb->prepare($sql_projects);

// ผูกค่ากับ statement
if ($role == 'Sale Supervisor') {
    $stmt_projects->bindParam(':team_id', $team_id, PDO::PARAM_INT);
} elseif ($role != 'Executive') {
    $stmt_projects->bindParam(':created_by', $created_by, PDO::PARAM_INT);
}
if (!empty($search_service)) {
    $search_param = '%' . $search_service . '%';
    $stmt_projects->bindParam(':search_service', $search_param, PDO::PARAM_STR);
}
if (!empty($search_product)) {
    $stmt_projects->bindParam(':search_product', $search_product, PDO::PARAM_STR);
}
if (!empty($search_status)) {
    $stmt_projects->bindParam(':search_status', $search_status, PDO::PARAM_STR);
}
if (!empty($search_creator)) {
    $stmt_projects->bindParam(':search_creator', $search_creator, PDO::PARAM_INT);
}
if (!empty($search_customer)) {
    $stmt_projects->bindParam(':search_customer', $search_customer, PDO::PARAM_INT);
}
if (!empty($search_year)) {
    $stmt_projects->bindParam(':search_year', $search_year, PDO::PARAM_INT);
}

// Execute query และดึงข้อมูลโปรเจ็กต์ทั้งหมด
$stmt_projects->execute();
$projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);


// นับจำนวนโครงการทั้งหมดและข้อมูลอื่นๆ สำหรับแสดงผลในกราฟ
$total_projects = count($projects);
$total_cost = 0;
$total_sale = 0;
foreach ($projects as $project) {
    $total_cost += $project['cost_no_vat'];
    $total_sale += $project['sale_no_vat'];
}

?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "project"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Project Management</title>
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
                            <h1 class="m-0">Project Management</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Project Management v1</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes (Stat box) -->
                    <div class="row">

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo number_format($total_projects); ?></h3>
                                    <p>Project All</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-folder"></i>
                                </div>
                            </div>
                        </div>
                        <!-- ./col -->

                        <!-- ------------------------------------------------------------------------------------------------------------------ -->


                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?php echo number_format($total_creators); ?></h3>
                                    <p>Seller</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>

                        <!-- ------------------------------------------------------------------------------------------------------------------ -->

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3><?php echo number_format($total_cost, 2); ?></h3>
                                    <p>Cost Price (Vat)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                        <!-- ./col -->

                        <!-- ------------------------------------------------------------------------------------------------------------------ -->

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?php echo number_format($total_sale, 2); ?></h3>
                                    <p>Sale Price (Vat)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ------------------------------------------------------------------------------------------------------------------ -->
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->

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
                                                                <input type="text" class="form-control" id="searchservice" name="searchservice" placeholder="ค้นหา...">
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
                                                                <label>Product</label>
                                                                <select class="custom-select select2" name="product">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($products as $product) { ?>
                                                                        <option value="<?php echo $product['product']; ?>">
                                                                            <?php echo $product['product']; ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Status</label>
                                                                <select class="custom-select select2" name="status">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($statuses as $status) { ?>
                                                                        <option value="<?php echo $status['status']; ?>">
                                                                            <?php echo $status['status']; ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Create By</label>
                                                                <select class="custom-select select2" name="creator">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($creators as $creator) { ?>
                                                                        <option value="<?php echo $creator['created_by']; ?>">
                                                                            <?php echo $creator['first_name'] . ' ' . $creator['last_name']; ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Customer</label>
                                                                <select class="custom-select select2" name="customer">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($customers as $customer) { ?>
                                                                        <option value="<?php echo $customer['customer_id']; ?>">
                                                                            <?php echo $customer['customer_name']; ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Year</label>
                                                                <select class="custom-select select2" name="year">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($years as $year) { ?>
                                                                        <option value="<?php echo $year['year']; ?>">
                                                                            <?php echo $year['year']; ?>
                                                                        </option>
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
                                <a href="add_project.php" class="btn btn-success btn-sm float-right">เพิ่มข้อมูล<i class=""></i></a>
                            </div><br>
                            <!-- //Section ปุ่มเพิ่มข้อมูล -->

                            <!-- Section ตารางแสดงผล -->
                            <div class="card">
                                <div class="card-header">
                                    <div class="container-fluid">
                                        <h3 class="card-title">Project Management</h3>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Contact No.</th>
                                                <th>Product</th>
                                                <th>Project Name</th>
                                                <th>Status</th>
                                                <th>Sale Price (Vat)</th>
                                                <th>Cost Price (Vat)</th>
                                                <th>(% GP)</th>
                                                <th>Create By</th>
                                                <th>Create Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($projects as $project) { ?>
                                                <tr id="myTable">
                                                    <td><?php echo htmlspecialchars($project['project_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($project['customer_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($project['status']); ?></td>
                                                    <td><?php echo number_format($project['sale_no_vat'], 2); ?></td>
                                                    <td><?php echo number_format($project['cost_no_vat'], 2); ?></td>
                                                    <td><?php echo htmlspecialchars($project['first_name'] . ' ' . $project['last_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($project['created_at']); ?></td>
                                                    <td>
                                                        <a href="view_project.php?id=<?php echo urlencode(encryptUserId($customer['project_id'])); ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="edit_project.php?user_id" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                                        <a href="" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Contact No.</th>
                                                <th>Product</th>
                                                <th>Project Name</th>
                                                <th>Status</th>
                                                <th>Sale Price (Vat)</th>
                                                <th>Cost Price (Vat)</th>
                                                <th>(% GP)</th>
                                                <th>Create By</th>
                                                <th>Create Date</th>
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