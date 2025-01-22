<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'] ?? '';
$team_id = $_SESSION['team_id'] ?? 0;
$created_by = $_SESSION['user_id'] ?? 0;

// ฟังก์ชันสำหรับจัดการกับปี
function getProjectYear($salesDate, $createdAt)
{
    if (!empty($salesDate)) {
        return date('Y', strtotime($salesDate));
    }
    if (!empty($createdAt)) {
        return date('Y', strtotime($createdAt));
    }
    return date('Y');
}

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data)
{
    if ($data === null) return '';
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// รับค่าจากฟอร์มการค้นหาและทำความสะอาด
$search_service = clean_input($_POST['searchservice'] ?? '');
$search_product = clean_input($_POST['product'] ?? '');
$search_status = clean_input($_POST['status'] ?? '');
//$search_status = clean_input($_POST['status'] ?? 'ชนะ (Win)');
$search_creator = clean_input($_POST['creator'] ?? '');
$search_customer = clean_input($_POST['customer'] ?? '');
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
$creators = getDropdownData($condb, "SELECT DISTINCT u.user_id as created_by, u.first_name, u.last_name FROM users u INNER JOIN projects p ON u.user_id = p.created_by $where_clause_dropdown ORDER BY u.first_name, u.last_name", $params_dropdown);
$customers = getDropdownData($condb, "SELECT DISTINCT c.customer_id, c.customer_name FROM customers c INNER JOIN projects p ON c.customer_id = p.customer_id $where_clause_dropdown", $params_dropdown);

// ปรับปรุงการดึงข้อมูลปีจาก sales_date
$years_sql = "
    SELECT DISTINCT 
        CASE 
            WHEN p.sales_date IS NOT NULL AND YEAR(p.sales_date) != 0 THEN YEAR(p.sales_date)
            WHEN p.created_at IS NOT NULL THEN YEAR(p.created_at)
            ELSE YEAR(CURRENT_DATE)
        END AS year 
    FROM projects p 
    $where_clause_dropdown 
    HAVING year IS NOT NULL AND year != 0 
    ORDER BY year DESC
";
$years = getDropdownData($condb, $years_sql, $params_dropdown);

// เพิ่มปีปัจจุบันถ้าไม่มีในรายการ
$current_year = date('Y');
$has_current_year = false;
foreach ($years as $year) {
    if ($year['year'] == $current_year) {
        $has_current_year = true;
        break;
    }
}
if (!$has_current_year) {
    array_unshift($years, ['year' => $current_year]);
}

// Team Dropdown (เฉพาะ Executive หรือ Sale Supervisor)
if ($role == 'Executive' || $role == 'Sale Supervisor') {
    $team_query = ($role == 'Sale Supervisor') ? "WHERE team_id = :team_id" : "";
    $teams = getDropdownData($condb, "SELECT DISTINCT team_id, team_name FROM teams $team_query", $role == 'Sale Supervisor' ? [':team_id' => $team_id] : []);
}

// กำหนด where clause ตามบทบาทผู้ใช้และเงื่อนไขการค้นหา
$where_clause = "WHERE 1=1";
$params = array();

if ($role == 'Sale Supervisor') {
    $where_clause .= " AND (
        u.team_id = :team_id 
        OR EXISTS (
            SELECT 1 
            FROM project_members pm2 
            WHERE pm2.project_id = p.project_id 
            AND pm2.user_id = :user_id
        )
    )";
    $params[':team_id'] = $team_id;
    $params[':user_id'] = $created_by;
} elseif ($role != 'Executive') {
    $where_clause .= " AND (
        p.created_by = :created_by 
        OR EXISTS (
            SELECT 1 
            FROM project_members pm2 
            WHERE pm2.project_id = p.project_id 
            AND pm2.user_id = :user_id
        )
    )";
    $params[':created_by'] = $created_by;
    $params[':user_id'] = $created_by;
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
    $where_clause .= " AND (
        (YEAR(p.sales_date) = :search_year AND p.sales_date IS NOT NULL) OR 
        (YEAR(p.created_at) = :search_year AND p.sales_date IS NULL)
    )";
    $params[':search_year'] = $search_year;
}

// SQL query สำหรับดึงข้อมูลโปรเจกต์
$sql_projects = "
    SELECT DISTINCT 
        p.*, 
        u.first_name, 
        u.last_name, 
        c.customer_name, 
        c.company, 
        c.address, 
        c.phone, 
        c.email, 
        t.team_name, 
        pr.product_name,
        seller.first_name AS seller_first_name, 
        seller.last_name AS seller_last_name,
        CASE 
            WHEN p.sales_date IS NOT NULL THEN YEAR(p.sales_date)
            WHEN p.created_at IS NOT NULL THEN YEAR(p.created_at)
            ELSE YEAR(CURRENT_DATE)
        END AS calculated_year
    FROM projects p
    LEFT JOIN customers c ON p.customer_id = c.customer_id
    LEFT JOIN users u ON p.created_by = u.user_id
    LEFT JOIN teams t ON u.team_id = t.team_id
    LEFT JOIN products pr ON p.product_id = pr.product_id
    LEFT JOIN users seller ON p.seller = seller.user_id
    LEFT JOIN project_members pm ON p.project_id = pm.project_id
    $where_clause
    ORDER BY p.created_at DESC, p.project_id DESC;
";


$stmt_projects = $condb->prepare($sql_projects);
$stmt_projects->execute($params);
$projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);

// // Debug: ตรวจสอบคำสั่ง SQL และพารามิเตอร์
// echo "<pre>";
// print_r($stmt_projects->queryString);
// print_r($params);
// echo "</pre>";

// ฟังก์ชันสำหรับตัดข้อความให้สั้นลงและเพิ่ม ...
function truncateText($text, $length = 100)
{
    // ถ้าข้อความเป็น null หรือว่างเปล่า
    if (empty($text)) {
        return 'ไม่ระบุข้อมูล';
    }

    // แปลงเป็น string เพื่อป้องกัน error
    $text = (string)$text;

    // วัดความยาวข้อความ UTF-8
    if (mb_strlen($text, 'UTF-8') > $length) {
        // ตัดข้อความและเพิ่ม ...
        return mb_substr($text, 0, $length, 'UTF-8') . '...';
    }
    return $text;
}

// ฟังก์ชันสำหรับแสดงข้อมูลหรือข้อความ "ไม่ระบุข้อมูล"
function displayData($data, $format = null)
{
    if (isset($data) && $data !== '') {
        if ($format === 'number') {
            return number_format((float)$data, 2);
        } elseif ($format === 'percentage') {
            return $data . '%';
        } else {
            return htmlspecialchars($data);
        }
    }
    return 'ไม่ระบุข้อมูล';
}


// ฟังก์ชันคำนวณข้อมูลสำหรับการ์ด
function calculateProjectMetrics($projects, $search_params = [])
{
    // กำหนดค่าเริ่มต้นสำหรับการคำนวณ
    $metrics = [
        'total_projects' => 0,      // จำนวนโครงการทั้งหมด
        'total_creators' => 0,      // จำนวนผู้ขาย
        'total_cost' => 0,          // ต้นทุนรวม
        'total_sale' => 0,          // ยอดขายรวม
        'total_gross_profit' => 0,  // กำไรขั้นต้นรวม
        'avg_gp_percentage' => 0    // เปอร์เซ็นต์กำไรเฉลี่ย
    ];

    // สร้างอาเรย์เก็บ creator_id ที่ไม่ซ้ำกัน
    $unique_creators = array();

    // วนลูปผ่านโครงการทั้งหมด
    foreach ($projects as $project) {
        $include_project = true; // ตัวแปรควบคุมการนับโครงการ

        // ตรวจสอบเงื่อนไขการค้นหาต่างๆ
        if (!empty($search_params['status']) && $project['status'] !== $search_params['status']) {
            $include_project = false;
        }
        if (!empty($search_params['product']) && $project['product_id'] !== $search_params['product']) {
            $include_project = false;
        }
        if (!empty($search_params['customer']) && $project['customer_id'] !== $search_params['customer']) {
            $include_project = false;
        }
        if (!empty($search_params['team']) && $project['team_name'] !== $search_params['team']) {
            $include_project = false;
        }
        if (!empty($search_params['year']) && date('Y', strtotime($project['sales_date'])) != $search_params['year']) {
            $include_project = false;
        }
        if (!empty($search_params['creator']) && $project['created_by'] !== $search_params['creator']) {
            $include_project = false;
        }

        // ถ้าผ่านเงื่อนไขการค้นหาทั้งหมด ให้นับและคำนวณ
        if ($include_project) {
            // นับจำนวนโครงการ
            $metrics['total_projects']++;

            // เก็บ creator_id เพื่อนับจำนวน seller ที่ไม่ซ้ำกัน
            if (!empty($project['created_by'])) {
                $unique_creators[$project['created_by']] = true;
            }

            // สะสมค่าทางการเงิน
            $metrics['total_cost'] += floatval($project['cost_no_vat']);
            $metrics['total_sale'] += floatval($project['sale_no_vat']);
            $metrics['total_gross_profit'] += floatval($project['gross_profit']);
        }
    }

    // นับจำนวน seller ที่ไม่ซ้ำกัน
    $metrics['total_creators'] = count($unique_creators);

    // คำนวณ Average GP % เมื่อมียอดขาย
    if ($metrics['total_sale'] > 0) {
        $metrics['avg_gp_percentage'] = ($metrics['total_gross_profit'] / $metrics['total_sale']) * 100;
    }

    return $metrics;
}

// ส่วนการใช้งาน - เก็บพารามิเตอร์การค้นหาทั้งหมด
$search_params = [
    'status' => clean_input($_POST['status'] ?? ''),
    'product' => clean_input($_POST['product'] ?? ''),
    'customer' => clean_input($_POST['customer'] ?? ''),
    'team' => clean_input($_POST['team'] ?? ''),
    'year' => filter_var($_POST['year'] ?? null, FILTER_VALIDATE_INT),
    'creator' => clean_input($_POST['creator'] ?? '')
];

// คำนวณ metrics ตามพารามิเตอร์การค้นหา
$metrics = calculateProjectMetrics($projects, $search_params);
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
        /* สไตล์สำหรับ tooltip ที่หัวตาราง */
        .table-header-tooltip {
            position: relative;
            cursor: help;
        }

        .table-header-tooltip:hover::after {
            content: attr(title);
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            white-space: normal;
            max-width: 200px;
            z-index: 1000;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            margin-top: 5px;
        }
    </style>

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

    <!-- ฟังก์ชันสำหรับแสดงข้อมูลกำหนดความยาว 100 ตัวอักษร หากมากกว่าให้แสดง ... (Customer Address) -->
    <style>
        .truncate-text {
            max-width: 400px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            cursor: pointer;
            /* เพิ่ม cursor เมื่อ hover */
        }

        /* เพิ่ม tooltip เมื่อ hover */
        .truncate-text:hover::after {
            content: attr(title);
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            white-space: normal;
            max-width: 300px;
            z-index: 1000;
        }
    </style>

    <!-- ฟังก์ชันสำหรับแสดงข้อมูลกำหนดความยาว 100 ตัวอักษร หากมากกว่าให้แสดง ... (Customer Address) -->
    <style>
        /* เพิ่ม class ใหม่สำหรับ Project Name */
        .truncate-text-project {
            max-width: 600px;
            /* ปรับความกว้างให้มากกว่า truncate-text ปกติ */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            cursor: pointer;
        }

        /* ปรับปรุง tooltip สำหรับ Project Name */
        .truncate-text-project:hover::after {
            content: attr(title);
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            white-space: normal;
            max-width: 600px;
            /* เพิ่มความกว้างสูงสุดของ tooltip */
            z-index: 1000;
            margin-top: 20px;
            line-height: 1.4;
            left: 0;
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

            <!-- ส่วนแสดงผลการ์ด -->
            <?php if ($role != 'Engineer'): ?>
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- Project All Card -->
                            <div class="col-lg-2 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3><?php echo number_format($metrics['total_projects']); ?></h3>
                                        <p>Project <?php echo $search_status ? "($search_status)" : "All"; ?></p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-folder"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Seller Card -->
                            <div class="col-lg-2 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3><?php echo number_format($metrics['total_creators']); ?></h3>
                                        <p>Seller</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Cost Price Card -->
                            <div class="col-lg-2 col-6">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3><?php echo number_format($metrics['total_cost'], 2); ?></h3>
                                        <p>Cost Price</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Sale Price Card -->
                            <div class="col-lg-2 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3><?php echo number_format($metrics['total_sale'], 2); ?></h3>
                                        <p>Sale Price</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Gross Profit Card -->
                            <div class="col-lg-2 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3><?php echo number_format($metrics['total_gross_profit'], 2); ?></h3>
                                        <p>Gross Profit</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Average GP % Card -->
                            <div class="col-lg-2 col-6">
                                <div class="small-box bg-pink">
                                    <div class="inner">
                                        <h3><?php echo number_format($metrics['avg_gp_percentage'], 2); ?>%</h3>
                                        <p>Average GP %</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>


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
                                                                    <label>ทีม</label>
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
                                                                <label>ผลิตภัณฑ์</label>
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
                                                                <label>สถานะโครงการ</label>
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
                                                                <label>พนักงาน</label>
                                                                <select class="custom-select select2" name="creator">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($creators as $creator) : ?>
                                                                        <option value="<?php echo $creator['created_by']; ?>"
                                                                            <?php echo ($search_creator === $creator['created_by']) ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($creator['first_name'] . ' ' . $creator['last_name']); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>ลูกค้า</label>
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
                                                                <label>ปีที่เสนอขาย</label>
                                                                <select class="custom-select select2" name="year">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($years as $year) : ?>
                                                                        <?php
                                                                        // ตรวจสอบค่าปีว่าเป็น 0 หรือ null หรือไม่
                                                                        $yearValue = !empty($year['year']) ? $year['year'] : date('Y');
                                                                        ?>
                                                                        <option value="<?php echo htmlspecialchars($yearValue); ?>"
                                                                            <?php echo ($search_year == $yearValue) ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($yearValue); ?>
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
                                    <div class="btn-group float-right">
                                        <a href="add_project.php" class="btn btn-success btn-sm">เพิ่มข้อมูลโครงการ</a>
                                        <button class="btn btn-info btn-sm float-right mr-2" data-toggle="modal" data-target="#importModal"> Import ข้อมูล <i class="fas fa-upload"></i></button>
                                    </div>
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
                                                <?php if ($role != 'Engineer'): ?>
                                                    <th class="text-nowrap text-center table-header-tooltip" title="ดูรายละเอียดและแก้ไขข้อมูลโครงการ">Action</th>
                                                <?php endif; ?>
                                                <th class="text-nowrap text-center table-header-tooltip" title="วันที่/เวลา เพิ่มข้อมูลโครงการเข้าระบบ">Create Date</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="วันที่ขาย">Sales Date</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="สถานะของโครงการ">Status</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="เลขที่สัญญาหรือเลขที่เอกสารอ้างอิง">Contact No.</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="ชื่อโครงการ">Project Name</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="ชื่อบริษัทของลูกค้า">Customer Company</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="ชื่อผู้ติดต่อของลูกค้า">Customer Name</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="ประเภทผลิตภัณฑ์หรือบริการ">Product</th>
                                                <?php if ($role != 'Engineer'): ?>
                                                    <th class="text-nowrap text-center table-header-tooltip" title="ราคาขายไม่รวม VAT">Sale Price</th>
                                                    <th class="text-nowrap text-center table-header-tooltip" title="ราคาต้นทุนไม่รวม VAT">Cost Price</th>
                                                    <th class="text-nowrap text-center table-header-tooltip" title="ราคาขายรวม VAT">Sale Price (Vat)</th>
                                                    <th class="text-nowrap text-center table-header-tooltip" title="ราคาต้นทุนรวม VAT">Cost Price (Vat)</th>
                                                    <th class="text-nowrap text-center table-header-tooltip" title="กำไรขั้นต้น">Gross Profit</th>
                                                    <th class="text-nowrap text-center table-header-tooltip" title="เปอร์เซ็นต์กำไรขั้นต้น">% GP</th>
                                                    <th class="text-nowrap text-center table-header-tooltip" title="อัตราภาษีมูลค่าเพิ่ม">Vat (%)</th>
                                                    <th class="text-nowrap text-center table-header-tooltip" title="ประมาณการต้นทุน">Estimate Cost</th>
                                                    <th class="text-nowrap text-center table-header-tooltip" title="ประมาณการยอดขาย">Estimate Sale</th>
                                                    <th class="text-nowrap text-center table-header-tooltip" title="ประมาณการกำไรขั้นต้น">Estimate GP</th>
                                                <?php endif; ?>
                                                <th class="text-nowrap text-center table-header-tooltip" title="ชื่อพนักงานขาย">Seller</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="ทีมที่รับผิดชอบโครงการ">Team</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="ที่อยู่ของลูกค้าหรือบริษัท">Customer Address</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="หมายเลขโทรศัพท์ติดต่อลูกค้า">Customer Phone</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="อีเมลติดต่อลูกค้า">Customer Email</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="หมายเหตุเพิ่มเติมของโครงการ">Remark</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="วันที่เริ่มโครงการ">Start Date</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="วันที่สิ้นสุดโครงการ">End Date</th>
                                                <th class="text-nowrap text-center table-header-tooltip" title="ผู้สร้างข้อมูลโครงการ">Create By</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($projects as $project): ?>
                                                <tr onclick="window.location='view_project.php?project_id=<?php echo urlencode(encryptUserId($project['project_id'])); ?>';" style="cursor: pointer;">
                                                    <?php if ($role != 'Engineer'): ?>
                                                        <td class="text-nowrap">
                                                            <a href="view_project.php?project_id=<?php echo urlencode(encryptUserId($project['project_id'])); ?>" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="edit_project.php?project_id=<?php echo urlencode(encryptUserId($project['project_id'])); ?>" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                                            <?php if ($project['created_by'] === $created_by): ?>
                                                                <button class="btn btn-danger btn-sm" onclick="confirmDelete('<?php echo urlencode(encryptUserId($project['project_id'])); ?>', '<?php echo htmlspecialchars($project['project_name']); ?>')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                            <a href="management/project_management.php?project_id=<?php echo urlencode(encryptUserId($project['project_id'])); ?>" class="btn btn-sm btn-warning" title="จัดการสมาชิกโครงการ">
                                                                <i class="fas fa-project-diagram"></i>
                                                            </a>
                                                            <!-- เพิ่มปุ่มสำหรับจัดการสมาชิกโครงการตรงนี้ -->
                                                            <!-- <a href="project_member/manage_members.php?project_id=<?php echo urlencode(encryptUserId($project['project_id'])); ?>"
                                                                class="btn btn-secondary btn-sm"
                                                                title="จัดการสมาชิกโครงการ">
                                                                <i class="fas fa-users"></i>
                                                            </a> -->
                                                        </td>
                                                    <?php endif; ?>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['created_at']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['sales_date']); ?></td>
                                                    <td class="text-nowrap text-center">
                                                        <?php
                                                        if (strcasecmp($project["status"], 'นำเสนอโครงการ (Presentations)') == 0) {
                                                            echo "<span class='badge badge-primary'>{$project['status']}</span>";
                                                        } elseif (strcasecmp($project["status"], 'On Hold') == 0) {
                                                            echo "<span class='badge badge-warning'>{$project['status']}</span>";
                                                        } elseif (strcasecmp($project["status"], 'ใบเสนอราคา (Quotation)') == 0) {
                                                            echo "<span class='badge badge-info'>{$project['status']}</span>";
                                                        } elseif (strcasecmp($project["status"], 'Negotiation') == 0) {
                                                            echo "<span class='badge badge-primary'>{$project['status']}</span>";
                                                        } elseif (strcasecmp($project["status"], 'ยื่นประมูล (Bidding)') == 0) {
                                                            echo "<span class='badge badge-warning'>{$project['status']}</span>";
                                                        } elseif (strcasecmp($project["status"], 'ชนะ (Win)') == 0) {
                                                            echo "<span class='badge badge-success'>{$project['status']}</span>";
                                                        } elseif (strcasecmp($project["status"], 'แพ้ (Loss)') == 0) {
                                                            echo "<span class='badge badge-danger'>{$project['status']}</span>";
                                                        } elseif (strcasecmp($project["status"], 'ยกเลิก (Cancled)') == 0) {
                                                            echo "<span class='badge badge-secondary'>{$project['status']}</span>";
                                                        } else {
                                                            echo "<span class='badge badge-secondary'>{$project['status']}</span>";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['contract_no']) ? htmlspecialchars($project['contract_no']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap">
                                                        <div class="truncate-text-project" title="<?php echo htmlspecialchars($project['project_name']); ?>">
                                                            <?php
                                                            echo truncateText(htmlspecialchars($project['project_name']), 300); // เปลี่ยนค่า length เป็น 300
                                                            ?>
                                                        </div>
                                                    </td>
                                                    <td class="text-nowrap"><?php echo isset($project['company']) ? htmlspecialchars($project['company']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['customer_name']) ? htmlspecialchars($project['customer_name']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['product_name']); ?></td>
                                                    <?php if ($role != 'Engineer'): ?>
                                                        <td class="text-nowrap"><?php echo number_format($project['sale_vat'], 2); ?></td>
                                                        <td class="text-nowrap "><?php echo number_format($project['cost_vat'], 2); ?></td>
                                                        <td class="text-nowrap "><?php echo number_format($project['sale_no_vat'], 2); ?></td>
                                                        <td class="text-nowrap "><?php echo number_format($project['cost_no_vat'], 2); ?></td>
                                                        <td class="text-nowrap" style="color: Green; font-weight: bold;"><?php echo number_format($project['gross_profit'], 2); ?></td>
                                                        <td class="text-nowrap" style="color: Green; font-weight: bold;"><?php echo !empty($project['potential']) ? htmlspecialchars($project['potential']) . '%' : ''; ?></td>
                                                        <td class="text-nowrap"><?php echo number_format($project['vat'], 2); ?>%</td>
                                                        <td class="text-nowrap"><?php echo number_format($project['es_cost_no_vat'], 2); ?></td>
                                                        <td class="text-nowrap"><?php echo number_format($project['es_sale_no_vat'], 2); ?></td>
                                                        <td class="text-nowrap"><?php echo number_format($project['es_gp_no_vat'], 2); ?></td>
                                                    <?php endif; ?>
                                                    <td class="text-nowrap"><?php echo displayData($project['seller_first_name'] . ' ' . $project['seller_last_name']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['team_name']); ?></td>
                                                    <td class="text-nowrap">
                                                        <div class="truncate-text" title="<?php echo htmlspecialchars($project['address'] ?? 'ไม่ระบุข้อมูล'); ?>">
                                                            <?php echo isset($project['address']) ? truncateText(htmlspecialchars($project['address'])) : 'ไม่ระบุข้อมูล'; ?>
                                                        </div>
                                                    </td>
                                                    <td class="text-nowrap"><?php echo isset($project['phone']) ? htmlspecialchars($project['phone']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo isset($project['email']) ? htmlspecialchars($project['email']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap">
                                                        <div class="truncate-text" title="<?php echo htmlspecialchars($project['remark'] ?? 'ไม่ระบุข้อมูล'); ?>">
                                                            <?php echo isset($project['remark']) ? truncateText(htmlspecialchars($project['remark'])) : 'ไม่ระบุข้อมูล'; ?>
                                                        </div>
                                                    </td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['start_date']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['end_date']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['first_name'] . ' ' . $project['last_name']); ?></td>
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
                "order": [
                    [1, "desc"]
                ], // ปรับให้เรียงตามคอลัมน์ที่ 2 (Create Date) จากล่าสุดไปเก่าสุด
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "fixedColumns": {
                    leftColumns: 2 // ตรึง 2 คอลัมน์ซ้าย
                }
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>

    <!-- JavaScript สำหรับอัพเดตการแสดงผลแบบ Real-time: การค้นหาสถานะ -->
    <script>
        // เพิ่ม event listener สำหรับการเปลี่ยนแปลงค่าใน dropdown สถานะ
        $(document).ready(function() {
            $('select[name="status"]').on('change', function() {
                // ส่งฟอร์มอัตโนมัติเมื่อมีการเปลี่ยนแปลงค่า
                $(this).closest('form').submit();
            });
            $('select[name="team"]').on('change', function() {
                // ส่งฟอร์มอัตโนมัติเมื่อมีการเปลี่ยนแปลงค่า
                $(this).closest('form').submit();
            });
            $('select[name="product"]').on('change', function() {
                // ส่งฟอร์มอัตโนมัติเมื่อมีการเปลี่ยนแปลงค่า
                $(this).closest('form').submit();
            });
            $('select[name="customer"]').on('change', function() {
                // ส่งฟอร์มอัตโนมัติเมื่อมีการเปลี่ยนแปลงค่า
                $(this).closest('form').submit();
            });
            $('select[name="created_by"]').on('change', function() {
                // ส่งฟอร์มอัตโนมัติเมื่อมีการเปลี่ยนแปลงค่า
                $(this).closest('form').submit();
            });
            $('select[name="year"]').on('change', function() {
                // ส่งฟอร์มอัตโนมัติเมื่อมีการเปลี่ยนแปลงค่า
                $(this).closest('form').submit();
            });
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

    <!-- Modal สำหรับการ Import -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="fas fa-file-import mr-2"></i> นำเข้าข้อมูลโครงการ
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="importForm" action="import_project.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- ขั้นตอนการนำเข้า -->
                        <div class="import-steps mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Step 1 -->
                                <div class="step text-center">
                                    <a href="templates/project_import_template.xlsx" class="text-decoration-none step-link">
                                        <div class="step-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px;">
                                            <i class="fas fa-download"></i>
                                        </div>
                                        <div class="step-text">
                                            <small>ขั้นตอนที่ 1</small><br>ดาวน์โหลด Template
                                        </div>
                                    </a>
                                </div>
                                <div class="step-line flex-grow-1 bg-light mx-2" style="height: 2px;"></div>
                                <!-- Step 2 -->
                                <div class="step text-center">
                                    <div class="step-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px;">
                                        <i class="fas fa-file-excel"></i>
                                    </div>
                                    <div class="step-text">
                                        <small>ขั้นตอนที่ 2</small><br>กรอกข้อมูล
                                    </div>
                                </div>
                                <div class="step-line flex-grow-1 bg-light mx-2" style="height: 2px;"></div>
                                <!-- Step 3 -->
                                <div class="step text-center">
                                    <div class="step-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px;">
                                        <i class="fas fa-upload"></i>
                                    </div>
                                    <div class="step-text">
                                        <small>ขั้นตอนที่ 3</small><br>อัพโหลดไฟล์
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ส่วนอัพโหลดไฟล์ -->
                        <div class="upload-section p-4 bg-light rounded">
                            <div class="text-center mb-4">
                                <i class="fas fa-cloud-upload-alt text-primary mb-3" style="font-size: 48px;"></i>
                                <h6 class="font-weight-bold">อัพโหลดไฟล์ Excel/CSV</h6>
                                <p class="text-muted small">ลากไฟล์มาวางที่นี่ หรือคลิกเพื่อเลือกไฟล์</p>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                                <label class="custom-file-label" for="importFile">เลือกไฟล์...</label>
                            </div>
                        </div>

                        <!-- คำแนะนำและข้อกำหนด -->
                        <div class="info-section mt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-info h-100">
                                        <div class="card-header bg-info text-white">
                                            <i class="fas fa-info-circle mr-2"></i> ข้อกำหนดไฟล์
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li><i class="fas fa-check-circle text-success mr-2"></i> รองรับไฟล์ .xlsx, .xls, และ .csv</li>
                                                <li><i class="fas fa-check-circle text-success mr-2"></i> ขนาดไฟล์ไม่เกิน 5MB</li>
                                                <li><i class="fas fa-check-circle text-success mr-2"></i> ใช้ Template ที่กำหนดเท่านั้น</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-warning h-100">
                                        <div class="card-header bg-warning text-dark">
                                            <i class="fas fa-exclamation-triangle mr-2"></i> คำแนะนำ
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li><i class="fas fa-angle-right text-warning mr-2"></i> ตรวจสอบข้อมูลให้ครบถ้วน</li>
                                                <li><i class="fas fa-angle-right text-warning mr-2"></i> ห้ามแก้ไขหัวคอลัมน์ใน Template</li>
                                                <li><i class="fas fa-angle-right text-warning mr-2"></i> บันทึกไฟล์ก่อนอัพโหลด</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i> ยกเลิก
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-import mr-2"></i> นำเข้าข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CSS -->
    <style>
        .modal-lg {
            max-width: 800px;
        }

        .upload-section {
            border: 2px dashed #dee2e6;
            transition: all 0.3s ease;
        }

        .upload-section:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        .custom-file-label {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .step-text {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .info-section .card {
            transition: all 0.3s ease;
        }

        .info-section .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>

    <!-- JS -->
    <script>
        // แสดงชื่อไฟล์ที่เลือก
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });

        // Drag & Drop
        const uploadSection = document.querySelector('.upload-section');
        const fileInput = document.querySelector('#importFile');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadSection.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadSection.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadSection.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            uploadSection.style.backgroundColor = '#e9ecef';
            uploadSection.style.borderColor = '#007bff';
        }

        function unhighlight(e) {
            uploadSection.style.backgroundColor = '#f8f9fa';
            uploadSection.style.borderColor = '#dee2e6';
        }

        uploadSection.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            let fileName = files[0].name;
            $('.custom-file-label').html(fileName);
        }
    </script>


    <!-- แจ้งเตือน Import Error -->
    <script>
        $(document).ready(function() {
            $('#importForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: 'import_project.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);

                            if (result.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'สำเร็จ',
                                    text: result.message
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.reload();
                                    }
                                });
                            } else {
                                let errorMessage = '<div style="max-height: 400px; overflow-y: auto;">';
                                errorMessage += '<p>' + result.message + '</p>';

                                if (result.errors && result.errors.length > 0) {
                                    errorMessage += '<div class="text-left">';
                                    result.errors.forEach(function(error) {
                                        errorMessage += `<div class="alert alert-danger mb-2">`;
                                        errorMessage += `<strong>แถวที่ ${error.row}:</strong><br>`;
                                        error.errors.forEach(function(err) {
                                            errorMessage += `- ${err}<br>`;
                                        });
                                        errorMessage += `<small>ข้อมูล: สถานะ="${error.data.status}", `;
                                        errorMessage += `สินค้า="${error.data.product}", `;
                                        errorMessage += `ชื่อโครงการ="${error.data.project_name}"</small>`;
                                        errorMessage += `</div>`;
                                    });
                                    errorMessage += '</div>';
                                }
                                errorMessage += '</div>';

                                if (result.success_count > 0) {
                                    errorMessage += `<div class="alert alert-success mt-3">`;
                                    errorMessage += `นำเข้าสำเร็จ ${result.success_count} รายการ`;
                                    errorMessage += `</div>`;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'พบข้อผิดพลาด',
                                    html: errorMessage,
                                    width: '800px'
                                });
                            }
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถประมวลผลการนำเข้าข้อมูลได้'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
                        });
                    }
                });
            });
        });
    </script>

    <!-- ลบโครกงาร -->
    <script>
        function confirmDelete(projectId, projectName) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: `ที่จะลบโครงการ "${projectName}" นี้ คำเตือน: ข้อมูลที่เชื่อมโยงทั้งหมดจะถูกลบด้วย!!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่ง request ไปยัง delete_project.php
                    fetch(`delete_project.php?project_id=${projectId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'สำเร็จ!',
                                    text: `ลบโครงการ "${projectName}" สำเร็จแล้ว`,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Redirect ไปหน้า project.php
                                    window.location.href = 'project.php';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด!',
                                    text: data.message,
                                });
                            }
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                            });
                        });
                }
            });
        }
    </script>


</body>

</html>