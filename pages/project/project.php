<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'] ?? '';
$team_id = $_SESSION['team_id'] ?? 0;
$created_by = $_SESSION['user_id'] ?? 0;

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// รับค่าจากฟอร์มการค้นหาและทำความสะอาด
$search_service = clean_input($_POST['searchservice'] ?? '');
$search_product = filter_var($_POST['product'] ?? 0, FILTER_VALIDATE_INT);
$search_status = clean_input($_POST['status'] ?? '');
$search_creator = filter_var($_POST['creator'] ?? 0, FILTER_VALIDATE_INT);
$search_customer = filter_var($_POST['customer'] ?? 0, FILTER_VALIDATE_INT);
$search_year = filter_var($_POST['year'] ?? 0, FILTER_VALIDATE_INT);
$search_team = clean_input($_POST['team'] ?? '');

// กำหนด where clause สำหรับ dropdown ตามบทบาทผู้ใช้
$where_clause_dropdown = "";
$params_dropdown = array();

if ($role == 'Sale Supervisor') {
    $where_clause_dropdown = " WHERE p.created_by IN (SELECT user_id FROM users WHERE team_id = :team_id)";
    $params_dropdown[':team_id'] = $team_id;
} elseif ($role != 'Executive') {
    $where_clause_dropdown = " WHERE p.created_by = :created_by";
    $params_dropdown[':created_by'] = $created_by;
}

// ฟังก์ชันสำหรับดึงข้อมูล dropdown
function getDropdownData($condb, $sql, $params)
{
    $stmt = $condb->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ดึงข้อมูลสำหรับ dropdowns
$products = getDropdownData($condb, "SELECT DISTINCT p.product_id, pr.product_name FROM projects p JOIN products pr ON p.product_id = pr.product_id $where_clause_dropdown", $params_dropdown);
$statuses = getDropdownData($condb, "SELECT DISTINCT status FROM projects p $where_clause_dropdown", $params_dropdown);
$creators = getDropdownData($condb, "SELECT DISTINCT u.user_id as created_by, u.first_name, u.last_name FROM users u INNER JOIN projects p ON u.user_id = p.created_by $where_clause_dropdown", $params_dropdown);
$customers = getDropdownData($condb, "SELECT DISTINCT c.customer_id, c.customer_name FROM customers c INNER JOIN projects p ON c.customer_id = p.customer_id $where_clause_dropdown", $params_dropdown);
// ปรับปรุงการดึงข้อมูลปีจาก sales_date
$years = getDropdownData($condb, "SELECT DISTINCT YEAR(sales_date) AS year FROM projects p $where_clause_dropdown ORDER BY year DESC", $params_dropdown);

// Team Dropdown (เฉพาะ Executive หรือ Sale Supervisor)
if ($role == 'Executive' || $role == 'Sale Supervisor') {
    $team_query = ($role == 'Sale Supervisor') ? "WHERE team_id = :team_id" : "";
    $teams = getDropdownData($condb, "SELECT DISTINCT team_id, team_name FROM teams $team_query", $role == 'Sale Supervisor' ? [':team_id' => $team_id] : []);
}

// กำหนด where clause ตามบทบาทผู้ใช้และเงื่อนไขการค้นหา
$where_clause = "WHERE 1=1";
$params = array();

if ($role == 'Sale Supervisor') {
    $where_clause .= " AND u.team_id = :team_id";
    $params[':team_id'] = $team_id;
} elseif ($role != 'Executive') {
    $where_clause .= " AND p.created_by = :created_by";
    $params[':created_by'] = $created_by;
}

// เพิ่มเงื่อนไขการค้นหา
if (!empty($search_service)) {
    $where_clause .= " AND (p.project_name LIKE :search_service OR c.customer_name LIKE :search_service)";
    $params[':search_service'] = "%$search_service%";
}
if (!empty($search_team)) {
    $where_clause .= " AND t.team_name = :search_team";
    $params[':search_team'] = $search_team;
}
if (!empty($search_product)) {
    $where_clause .= " AND p.product_id = :search_product";
    $params[':search_product'] = $search_product;
}
if (!empty($search_status)) {
    $where_clause .= " AND p.status = :search_status";
    $params[':search_status'] = $search_status;
}
if (!empty($search_creator)) {
    $where_clause .= " AND p.created_by = :search_creator";
    $params[':search_creator'] = $search_creator;
}
if (!empty($search_customer)) {
    $where_clause .= " AND p.customer_id = :search_customer";
    $params[':search_customer'] = $search_customer;
}
if (!empty($search_year)) {
    $where_clause .= " AND YEAR(p.sales_date) = :search_year";
    $params[':search_year'] = $search_year;
}

// SQL query สำหรับดึงข้อมูลโปรเจกต์
$sql_projects = "
    SELECT p.*, u.first_name, u.last_name, c.customer_name, c.company, c.address, c.phone, c.email, t.team_name, pr.product_name,
           seller.first_name AS seller_first_name, seller.last_name AS seller_last_name,
           YEAR(p.sales_date) AS sales_year
    FROM projects p
    LEFT JOIN customers c ON p.customer_id = c.customer_id
    LEFT JOIN users u ON p.created_by = u.user_id
    LEFT JOIN teams t ON u.team_id = t.team_id
    LEFT JOIN products pr ON p.product_id = pr.product_id
    LEFT JOIN users seller ON p.seller = seller.user_id
    $where_clause
    ORDER BY p.project_id DESC 
";

$stmt_projects = $condb->prepare($sql_projects);
$stmt_projects->execute($params);
$projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);

// คำนวณสถิติต่างๆ
$total_projects = count($projects);
$total_cost = 0;
$total_sale = 0;
$unique_creators = array();
foreach ($projects as $project) {
    $total_cost += $project['cost_vat'];
    $total_sale += $project['sale_vat'];
    $unique_creators[$project['created_by']] = true;
}
$total_creators = count($unique_creators);

// ฟังก์ชันสำหรับแสดงข้อมูลหรือ "ไม่ระบุข้อมูล" ถ้าไม่มีข้อมูล
function displayData($data, $format = null)
{
    if (isset($data) && $data !== '') {
        if ($format === 'number') {
            return number_format($data, 2);
        } elseif ($format === 'percentage') {
            return $data . '%';
        } else {
            return htmlspecialchars($data);
        }
    } else {
        return 'ไม่ระบุข้อมูล';
    }
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
                                                        <?php if ($role == 'Executive') : ?>
                                                            <div class="col-sm-2">
                                                                <div class="form-group">
                                                                    <label>Team</label>
                                                                    <select class="custom-select select2" name="team">
                                                                        <option value="">เลือก</option>
                                                                        <?php foreach ($teams as $team) : ?>
                                                                            <option value="<?php echo htmlspecialchars($team['team_name']); ?>" <?php echo ($search_team == $team['team_name']) ? 'selected' : ''; ?>>
                                                                                <?php echo htmlspecialchars($team['team_name']); ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Product</label>
                                                                <select class="custom-select select2" name="product">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($products as $product) { ?>
                                                                        <option value="<?php echo htmlspecialchars($product['product_id']); ?>" <?php echo ($search_product == $product['product_id']) ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($product['product_name']); ?>
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
                                                                        <option value="<?php echo htmlspecialchars($status['status']); ?>" <?php echo ($search_status == $status['status']) ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($status['status']); ?>
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
                                                                    <?php foreach ($creators as $creator) : ?>
                                                                        <option value="<?php echo htmlspecialchars($creator['created_by']); ?>" <?php echo ($search_creator == $creator['created_by']) ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($creator['first_name'] . ' ' . $creator['last_name']); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Customer</label>
                                                                <select class="custom-select select2" name="customer">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($customers as $customer) : ?>
                                                                        <option value="<?php echo htmlspecialchars($customer['customer_id']); ?>" <?php echo ($search_customer == $customer['customer_id']) ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($customer['customer_name']); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Year</label>
                                                                <select class="custom-select select2" name="year">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($years as $year) : ?>
                                                                        <option value="<?php echo htmlspecialchars($year['year']); ?>" <?php echo ($search_year == $year['year']) ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($year['year']); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
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
                            <?php if ($role != 'Engineer') : ?>
                                <div class="col-md-12 pb-3">
                                    <a href="add_project.php" class="btn btn-success btn-sm float-right">เพิ่มข้อมูล<i class=""></i></a>
                                </div><br>
                            <?php endif; ?>
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
                                                <th class="text-nowrap text-center">Action</th>
                                                <th class="text-nowrap text-center">Contact No.</th>
                                                <th class="text-nowrap text-center">Sales Date</th>
                                                <th class="text-nowrap text-center">Start Date</th>
                                                <th class="text-nowrap text-center">End Date</th>
                                                <th class="text-nowrap text-center">Status</th>
                                                <th class="text-nowrap text-center">Product</th>
                                                <th class="text-nowrap text-center">Project Name</th>
                                                <th class="text-nowrap text-center">Cost Price</th>
                                                <th class="text-nowrap text-center">Cost Price (Vat)</th>
                                                <th class="text-nowrap text-center">Sale Price</th>
                                                <th class="text-nowrap text-center">Sale Price (Vat)</th>
                                                <th class="text-nowrap text-center">Gross Profit</th>
                                                <th class="text-nowrap text-center">(% GP)</th>
                                                <th class="text-nowrap text-center">Vat (%)</th>
                                                <th class="text-nowrap text-center">Estimate Cost</th>
                                                <th class="text-nowrap text-center">Estimate Sale</th>
                                                <th class="text-nowrap text-center">Estimate GP</th>
                                                <th class="text-nowrap text-center">Seller</th>
                                                <th class="text-nowrap text-center">Team</th>
                                                <th class="text-nowrap text-center">Customer Name</th>
                                                <th class="text-nowrap text-center">Customer Company</th>
                                                <th class="text-nowrap text-center">Customer Address</th>
                                                <th class="text-nowrap text-center">Customer Phone</th>
                                                <th class="text-nowrap text-center">Customer Email</th>
                                                <th class="text-nowrap text-center">Remark</th>
                                                <th class="text-nowrap text-center">Create By</th>
                                                <th class="text-nowrap text-center">Create Date</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($projects as $project) : ?>
                                                <tr>
                                                    <td class="text-nowrap">
                                                        <a href="view_project.php?project_id=<?php echo urlencode(encryptUserId($project['project_id'])); ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="edit_project.php?project_id=<?php echo urlencode(encryptUserId($project['project_id'])); ?>" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                                        <a href="" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                                    </td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['contract_no']) ? htmlspecialchars($project['contract_no']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['sales_date']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['start_date']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['end_date']); ?></td>
                                                    <td class="text-nowrap text-center">
                                                        <?php
                                                        if (strcasecmp($project["status"], 'Waiting for approve') == 0) {
                                                            echo "<span class='badge badge-primary'>{$project['status']}</span>";
                                                        } elseif (strcasecmp($project["status"], 'On Hold') == 0) {
                                                            echo "<span class='badge badge-warning'>{$project['status']}</span>";
                                                        } elseif (strcasecmp($project["status"], 'Quotation') == 0) {
                                                            echo "<span class='badge badge-info'>{$project['status']}</span>";
                                                        } elseif (strcasecmp($project["status"], 'Negotiation') == 0) {
                                                            echo "<span class='badge badge-primary'>{$project['status']}</span>";
                                                        } elseif (strcasecmp($project["status"], 'Bidding') == 0) {
                                                            echo "<span class='badge badge-warning'>{$project['status']}</span>";
                                                        } elseif (strcasecmp($project["status"], 'Win') == 0) {
                                                            echo "<span class='badge badge-success'>{$project['status']}</span>";
                                                        } elseif (strcasecmp($project["status"], 'Lost') == 0) {
                                                            echo "<span class='badge badge-danger'>{$project['status']}</span>";
                                                        } elseif (strcasecmp($project["status"], 'Cancelled') == 0) {
                                                            echo "<span class='badge badge-secondary'>{$project['status']}</span>";
                                                        } else {
                                                            echo "<span class='badge badge-secondary'>{$project['status']}</span>";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['product_name']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['project_name']); ?></td>
                                                    <td class="text-nowrap "><?php echo number_format($project['cost_no_vat'], 2); ?></td>
                                                    <td class="text-nowrap "><?php echo number_format($project['cost_vat'], 2); ?></td>
                                                    <td class="text-nowrap "><?php echo number_format($project['sale_no_vat'], 2); ?></td>
                                                    <td class="text-nowrap" ><?php echo number_format($project['sale_vat'], 2); ?></td>
                                                    <td class="text-nowrap" style="color: Green; font-weight: bold;" ><?php echo number_format($project['gross_profit'], 2); ?></td>
                                                    <td class="text-nowrap" style="color: Green; font-weight: bold;" ><?php echo !empty($project['potential']) ? htmlspecialchars($project['potential']) . '%' : ''; ?></td>
                                                    <td class="text-nowrap"><?php echo number_format($project['vat'], 2); ?>%</td>
                                                    <td class="text-nowrap"><?php echo number_format($project['es_cost_no_vat'], 2); ?></td>
                                                    <td class="text-nowrap"><?php echo number_format($project['es_sale_no_vat'], 2); ?></td>
                                                    <td class="text-nowrap"><?php echo number_format($project['es_gp_no_vat'], 2); ?></td>
                                                    <td class="text-nowrap"><?php echo displayData($project['seller_first_name'] . ' ' . $project['seller_last_name']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['team_name']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['customer_name']) ? htmlspecialchars($project['customer_name']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo isset($project['company']) ? htmlspecialchars($project['company']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo isset($project['address']) ? htmlspecialchars($project['address']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo isset($project['phone']) ? htmlspecialchars($project['phone']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo isset($project['email']) ? htmlspecialchars($project['email']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['remark']) ? htmlspecialchars($project['remark']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['first_name'] . ' ' . $project['last_name']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['created_at']); ?></td>

                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
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
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": false,
                "lengthChange": false,
                "autoWidth": false,
                "scrollX": true,
                "scrollCollapse": true,
                "paging": true,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "columnDefs": [{
                        "width": "80px",
                        "targets": [0, 1, 2, 3]
                    }, // Contact No., Dates
                    {
                        "width": "100px",
                        "targets": [4, 5]
                    }, // Status, Product
                    {
                        "width": "700px",
                        "targets": 6
                    }, // Project Name
                    {
                        "width": "100px",
                        "targets": [7, 8, 9, 10, 11]
                    }, // Prices
                    {
                        "width": "60px",
                        "targets": [12, 13]
                    }, // GP%, VAT%
                    {
                        "width": "100px",
                        "targets": [14, 15, 16]
                    }, // Estimates
                    {
                        "width": "100px",
                        "targets": [17, 18]
                    }, // Seller, Team
                    {
                        "width": "150px",
                        "targets": [19, 20, 21, 22, 23]
                    }, // Customer info
                    {
                        "width": "500px",
                        "targets": 24
                    }, // Remark
                    {
                        "width": "120px",
                        "targets": [25, 26]
                    }, // Create By, Create Date
                    {
                        "width": "100px",
                        "targets": 27
                    } // Action
                ],
                "fixedColumns": {
                    leftColumns: 2 // ตรึง 2 คอลัมน์ซ้าย
                }
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