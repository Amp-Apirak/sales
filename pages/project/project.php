<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'] ?? '';
$team_ids = $_SESSION['team_ids'] ?? []; // Array of team IDs
$current_user_id = $_SESSION['user_id'] ?? null;
if (!$current_user_id) {
    die("Error: User ID not found in session");
}

// ฟังก์ชันสำหรับดึงข้อมูล dropdown
function getDropdownData($condb, $sql, $params = [])
{
    try {
        $stmt = $condb->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Dropdown query failed: " . $e->getMessage());
        return [];
    }
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

// --- 1. รับและทำความสะอาด Input จากฟอร์ม ---
$search_service = trim($_POST['searchservice'] ?? '');
$search_product = trim($_POST['product'] ?? '');
$search_status = trim($_POST['status'] ?? '');
$search_creator = trim($_POST['creator'] ?? '');
$search_customer = trim($_POST['customer'] ?? '');
$search_year = filter_var($_POST['year'] ?? '', FILTER_VALIDATE_INT);
$search_team = trim($_POST['team'] ?? '');


// --- 2. สร้างเงื่อนไขพื้นฐาน (WHERE clause) สำหรับ Dropdowns ---
$dropdown_where = '';
$dropdown_params = [];

if ($role === 'Sale Supervisor') {
    if (!empty($team_ids)) {
        $placeholders = implode(',', array_fill(0, count($team_ids), '?'));
        $dropdown_where = "WHERE p.seller IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id IN ($placeholders))";
        $dropdown_params = $team_ids;
    } else {
        $dropdown_where = "WHERE p.seller = ?";
        $dropdown_params[] = $current_user_id;
    }
} elseif ($role === 'Seller' || $role === 'Engineer') {
    $dropdown_where = "WHERE p.seller = ?";
    $dropdown_params[] = $current_user_id;
}

// --- 3. ดึงข้อมูลสำหรับ Dropdowns ---
$products = getDropdownData($condb, "SELECT DISTINCT p.product_id, pr.product_name FROM projects p JOIN products pr ON p.product_id = pr.product_id $dropdown_where ORDER BY pr.product_name", $dropdown_params);
$statuses = getDropdownData($condb, "SELECT DISTINCT status FROM projects p $dropdown_where ORDER BY status", $dropdown_params);
$customers = getDropdownData($condb, "SELECT DISTINCT c.customer_id, c.customer_name FROM customers c JOIN projects p ON c.customer_id = p.customer_id $dropdown_where ORDER BY c.customer_name", $dropdown_params);

// Dropdown for Creators (Sellers)
$creators_sql = "SELECT DISTINCT u.user_id as seller_id, u.first_name, u.last_name FROM users u ";
$creator_params = [];
if ($role === 'Sale Supervisor') {
    if (!empty($team_ids)) {
        $placeholders = implode(',', array_fill(0, count($team_ids), '?'));
        $creators_sql .= "JOIN user_teams ut ON u.user_id = ut.user_id WHERE ut.team_id IN ($placeholders) AND u.role IN ('Seller', 'Sale Supervisor')";
        $creator_params = $team_ids;
    }
} elseif ($role === 'Executive') {
    $creators_sql .= "WHERE u.role IN ('Seller', 'Sale Supervisor', 'Executive')";
} else { // Seller, Engineer
    $creators_sql .= "WHERE u.user_id = ?";
    $creator_params[] = $current_user_id;
}
$creators = getDropdownData($condb, $creators_sql, $creator_params);

// Dropdown for Years
$years_sql = "SELECT DISTINCT YEAR(p.sales_date) as year FROM projects p $dropdown_where HAVING year IS NOT NULL ORDER BY year DESC";
$years = getDropdownData($condb, $years_sql, $dropdown_params);

// Dropdown for Teams (only for Executive)
$teams = [];
if ($role === 'Executive') {
    $teams = getDropdownData($condb, "SELECT DISTINCT team_id, team_name FROM teams ORDER BY team_name");
}


// --- 4. สร้าง Main Query ---
$main_params = [];
$main_where_conditions = [];

// Role-based filtering
if ($role === 'Sale Supervisor') {
    if (!empty($team_ids)) {
        $team_placeholders = [];
        foreach ($team_ids as $key => $id) {
            $p = ':main_team_' . $key;
            $team_placeholders[] = $p;
            $main_params[$p] = $id;
        }
        $in_clause = implode(',', $team_placeholders);
        $main_where_conditions[] = "p.seller IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id IN ($in_clause))";
    } else {
        $main_where_conditions[] = "p.seller = :current_user_id";
        $main_params[':current_user_id'] = $current_user_id;
    }
} elseif ($role === 'Seller' || $role === 'Engineer') {
    $main_where_conditions[] = "p.seller = :current_user_id";
    $main_params[':current_user_id'] = $current_user_id;
}

// Search-based filtering
if (!empty($search_service)) {
    $main_where_conditions[] = "(p.project_name LIKE :search_service OR c.customer_name LIKE :search_service)";
    $main_params[':search_service'] = "%$search_service%";
}
if (!empty($search_team) && $role === 'Executive') {
    $main_where_conditions[] = "seller_team.team_id = :search_team";
    $main_params[':search_team'] = $search_team;
}
if (!empty($search_product)) {
    $main_where_conditions[] = "p.product_id = :search_product";
    $main_params[':search_product'] = $search_product;
}
if (!empty($search_status)) {
    $main_where_conditions[] = "p.status = :search_status";
    $main_params[':search_status'] = $search_status;
}
if (!empty($search_creator)) {
    $main_where_conditions[] = "p.seller = :search_creator";
    $main_params[':search_creator'] = $search_creator;
}
if (!empty($search_customer)) {
    $main_where_conditions[] = "p.customer_id = :search_customer";
    $main_params[':search_customer'] = $search_customer;
}
if (!empty($search_year)) {
    $main_where_conditions[] = "YEAR(p.sales_date) = :search_year";
    $main_params[':search_year'] = $search_year;
}

$where_clause = "";
if (!empty($main_where_conditions)) {
    $where_clause = "WHERE " . implode(' AND ', $main_where_conditions);
}

// Final SQL
$team_id_list_for_query = !empty($team_ids) ? implode(',', array_map(fn($id) => $condb->quote($id), $team_ids)) : "NULL";

$sql_projects = "
    SELECT DISTINCT 
        p.*,
        creator.first_name as creator_first_name, 
        creator.last_name as creator_last_name,
        c.customer_name, c.company, c.address, c.phone, c.email, 
        seller_team.team_name,
        pr.product_name,
        seller.first_name AS seller_first_name, 
        seller.last_name AS seller_last_name,
        pm_user.is_active as member_access_level,

        -- Re-added columns for permission checking
        CASE 
            WHEN p.created_by = :current_user_id_main OR p.seller = :current_user_id_main THEN 1
            ELSE 0
        END as is_project_owner,

        CASE 
            WHEN seller_team.team_id IN ($team_id_list_for_query) THEN 1
            ELSE 0
        END as is_team_project,

        CASE 
            WHEN p.created_by = :current_user_id_main THEN 'owner'
            WHEN p.seller = :current_user_id_main THEN 'owner'
            WHEN EXISTS (
                SELECT 1 FROM project_members pm 
                WHERE pm.project_id = p.project_id 
                AND pm.user_id = :current_user_id_main
            ) THEN 'shared'
            ELSE 'none'
        END as user_permission

    FROM projects p
    LEFT JOIN customers c ON p.customer_id = c.customer_id
    LEFT JOIN users creator ON p.created_by = creator.user_id
    LEFT JOIN users seller ON p.seller = seller.user_id
    LEFT JOIN user_teams ut ON seller.user_id = ut.user_id AND ut.is_primary = 1
    LEFT JOIN teams seller_team ON ut.team_id = seller_team.team_id
    LEFT JOIN products pr ON p.product_id = pr.product_id
    LEFT JOIN project_members pm_user ON p.project_id = pm_user.project_id AND pm_user.user_id = :current_user_id_main
    $where_clause
    ORDER BY p.created_at DESC, p.project_id DESC
";
$main_params[':current_user_id_main'] = $current_user_id;

$stmt_projects = $condb->prepare($sql_projects);
$stmt_projects->execute($main_params);
$projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);


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
function calculateProjectMetrics($projects, $search_params = [], $user_team_name = '', $user_role = '')
{
    // กำหนดค่าเริ่มต้นสำหรับการคำนวณ
    $metrics = [
        'total_projects' => 0,
        'total_creators' => 0,
        'total_cost' => 0,
        'total_sale' => 0,
        'total_gross_profit' => 0,
        'avg_gp_percentage' => 0
    ];

    $unique_creators = array();

    foreach ($projects as $project) {
        $include_project = true;

        // ตรวจสอบว่าเป็นโครงการที่แชร์มาจากทีมอื่นหรือไม่
        $isSharedFromOtherTeam = false;
        if ($user_role != 'Executive') {
            if (!empty($project['team_name']) && $project['team_name'] != $user_team_name) {
                $isSharedFromOtherTeam = true;
            }
        }

        // ตรวจสอบเงื่อนไขการค้นหาต่างๆ (เหมือนเดิม)
        if (!empty($search_params['status']) && $project['status'] !== $search_params['status']) {
            $include_project = false;
        }
        // ... เงื่อนไขอื่นๆ เหมือนเดิม

        // ถ้าผ่านเงื่อนไขการค้นหาทั้งหมด ให้นับและคำนวณ
        if ($include_project) {
            $metrics['total_projects']++;

            if (!empty($project['seller'])) {
                $unique_creators[$project['seller']] = true;
            }

            // เฉพาะโครงการที่ไม่ใช่แชร์จากทีมอื่น ถึงจะนำมาคำนวณทางการเงิน
            if (!$isSharedFromOtherTeam) {
                $metrics['total_cost'] += floatval($project['cost_no_vat']);
                $metrics['total_sale'] += floatval($project['sale_no_vat']);
                $metrics['total_gross_profit'] += floatval($project['gross_profit']);
            }
        }
    }

    $metrics['total_creators'] = count($unique_creators);

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

$user_team_name = $_SESSION['team_name'] ?? '';
$metrics = calculateProjectMetrics($projects, $search_params, $user_team_name, $role);
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



        /* CSS สำหรับปุ่มที่ไม่มีสิทธิ์ */
        .btn-secondary[disabled] {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary[disabled]:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            transform: none;
        }

        /* สำหรับ badge แสดงสถานะ */
        .badge.ml-1 {
            font-size: 0.7rem;
            vertical-align: middle;
        }

        /* Tooltip สำหรับปุ่มที่ถูก disabled */
        .btn[disabled][title]:hover {
            position: relative;
        }

        .btn[disabled][title]:hover::after {
            content: attr(title);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
            margin-bottom: 5px;
        }

        /* Animation สำหรับปุ่มที่ใช้งานได้ */
        .btn:not([disabled]):hover {
            transform: translateY(-1px);
            transition: all 0.2s;
        }

        /* สีสำหรับ badge ต่างๆ */
        .badge-info {
            background-color: #17a2b8;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-secondary {
            background-color: #6c757d;
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
                                                                <input type="text" class="form-control" id="searchservice" name="searchservice"
                                                                    placeholder="ค้นหา..." value="<?php echo htmlspecialchars($search_service); ?>">
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
                                                                <label>ผู้ขาย</label>
                                                                <select class="custom-select select2" name="creator">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($creators as $creator) : ?>
                                                                        <option value="<?php echo $creator['seller_id']; ?>"
                                                                            <?php echo ($search_creator === $creator['seller_id']) ? 'selected' : ''; ?>>
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
                                                                    <option value="">เลือกทั้งหมด</option>
                                                                    <?php foreach ($years as $year) : ?>
                                                                        <option value="<?php echo htmlspecialchars($year['year']); ?>"
                                                                            <?php echo ($search_year == $year['year']) ? 'selected' : ''; ?>>
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
                                                <tr>
                                                    <?php if ($role != 'Engineer'): ?>
                                                        <td class="text-nowrap">
                                                            <?php
                                                            // ตรวจสอบสิทธิ์สำหรับแต่ละปุ่ม
                                                            $can_view = true; // ทุกคนดูได้
                                                            $can_edit = false;
                                                            $can_delete = false;
                                                            $can_manage = false;

                                                            // ตรวจสอบสิทธิ์จาก project_members
                                                            $memberAccessLevel = $project['member_access_level'] ?? 999;
                                                            $accessLevel = $project['access_level'] ?? 'none';

                                                            // กำหนดสิทธิ์ตาม role และสิทธิ์ใน project_members
                                                            switch ($role) {
                                                                case 'Executive':
                                                                    // Executive มีสิทธิ์ทุกอย่าง (ยกเว้นถ้าถูกกำหนดเป็น View Only)
                                                                    $can_edit = ($memberAccessLevel != 1); // 1 = View Only
                                                                    $can_delete = ($memberAccessLevel != 1);
                                                                    $can_manage = ($memberAccessLevel != 1);
                                                                    break;

                                                                case 'Sale Supervisor':
                                                                    if ($project['is_project_owner']) {
                                                                        // เป็นเจ้าของโครงการ
                                                                        $can_edit = true;
                                                                        $can_delete = true;
                                                                        $can_manage = true;
                                                                    } elseif ($project['is_team_project']) {
                                                                        // โครงการในทีม
                                                                        $can_edit = ($memberAccessLevel != 1); // ไม่ใช่ View Only
                                                                        $can_delete = false; // ไม่สามารถลบโครงการของคนอื่นได้
                                                                        $can_manage = ($memberAccessLevel == 0 || $memberAccessLevel == 2); // Full หรือ Half Access
                                                                    } elseif ($accessLevel == 'view_only') {
                                                                        // ถูกแชร์แบบ View Only
                                                                        $can_edit = false;
                                                                        $can_delete = false;
                                                                        $can_manage = false;
                                                                    } elseif ($accessLevel == 'half_access') {
                                                                        // ถูกแชร์แบบ Half Access
                                                                        $can_edit = false;
                                                                        $can_delete = false;
                                                                        $can_manage = true;
                                                                    } elseif ($accessLevel == 'full_member') {
                                                                        // ถูกแชร์แบบ Full Access
                                                                        $can_edit = true;
                                                                        $can_delete = false;
                                                                        $can_manage = true;
                                                                    }
                                                                    break;

                                                                case 'Seller':
                                                                    if ($project['is_project_owner']) {
                                                                        // เป็นเจ้าของโครงการ
                                                                        $can_edit = true;
                                                                        $can_delete = true;
                                                                        $can_manage = true;
                                                                    } elseif ($accessLevel == 'view_only') {
                                                                        // ถูกแชร์แบบ View Only
                                                                        $can_edit = false;
                                                                        $can_delete = false;
                                                                        $can_manage = false;
                                                                    } elseif ($accessLevel == 'half_access') {
                                                                        // ถูกแชร์แบบ Half Access
                                                                        $can_edit = true;
                                                                        $can_delete = false;
                                                                        $can_manage = false;
                                                                    } elseif ($accessLevel == 'full_member') {
                                                                        // ถูกแชร์แบบ Full Access
                                                                        $can_edit = true;
                                                                        $can_delete = false;
                                                                        $can_manage = true;
                                                                    }
                                                                    break;
                                                            }

                                                            // แสดงข้อมูล Debug (ลบออกเมื่อใช้งานจริง)
                                                            // echo "<!-- Debug: memberAccessLevel = {$memberAccessLevel}, accessLevel = {$accessLevel}, role = {$role} -->";
                                                            ?>

                                                            <!-- ปุ่มดู - แสดงเสมอ -->
                                                            <a href="view_project.php?project_id=<?php echo urlencode(encryptUserId($project['project_id'])); ?>"
                                                                class="btn btn-sm btn-primary" title="ดูรายละเอียด">
                                                                <i class="fas fa-eye"></i>
                                                            </a>

                                                            <!-- ปุ่มแก้ไข - แสดงตามสิทธิ์ -->
                                                            <?php if ($can_edit): ?>
                                                                <a href="edit_project.php?project_id=<?php echo urlencode(encryptUserId($project['project_id'])); ?>"
                                                                    class="btn btn-info btn-sm" title="แก้ไข">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                            <?php else: ?>
                                                                <button class="btn btn-secondary btn-sm" disabled title="<?php
                                                                                                                            if ($memberAccessLevel == 1) echo 'สิทธิ์ View Only';
                                                                                                                            elseif ($accessLevel == 'view_only') echo 'โครงการแชร์แบบ View Only';
                                                                                                                            else echo 'ไม่มีสิทธิ์แก้ไข';
                                                                                                                            ?>">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </button>
                                                            <?php endif; ?>

                                                            <!-- ปุ่มลบ - แสดงตามสิทธิ์ -->
                                                            <?php if ($can_delete): ?>
                                                                <button class="btn btn-danger btn-sm"
                                                                    onclick="confirmDelete('<?php echo urlencode(encryptUserId($project['project_id'])); ?>', '<?php echo htmlspecialchars($project['project_name']); ?>')"
                                                                    title="ลบ">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            <?php else: ?>
                                                                <button class="btn btn-secondary btn-sm" disabled title="<?php
                                                                                                                            if ($memberAccessLevel == 1) echo 'สิทธิ์ View Only';
                                                                                                                            elseif (!$project['is_project_owner']) echo 'ลบได้เฉพาะเจ้าของโครงการ';
                                                                                                                            else echo 'ไม่มีสิทธิ์ลบ';
                                                                                                                            ?>">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            <?php endif; ?>

                                                            <!-- ปุ่มจัดการโครงการ - แสดงตามสิทธิ์ -->
                                                            <?php if ($can_manage): ?>
                                                                <a href="management/project_management.php?project_id=<?php echo urlencode(encryptUserId($project['project_id'])); ?>"
                                                                    class="btn btn-sm btn-warning" title="จัดการสมาชิกโครงการ">
                                                                    <i class="fas fa-project-diagram"></i>
                                                                </a>
                                                            <?php else: ?>
                                                                <button class="btn btn-secondary btn-sm" disabled title="<?php
                                                                                                                            if ($memberAccessLevel == 1) echo 'สิทธิ์ View Only';
                                                                                                                            elseif ($accessLevel == 'half_access') echo 'สิทธิ์ Half Access';
                                                                                                                            else echo 'ไม่มีสิทธิ์จัดการ';
                                                                                                                            ?>">
                                                                    <i class="fas fa-project-diagram"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </td>
                                                    <?php else: ?>
                                                        <!-- สำหรับ Engineer - แสดงเฉพาะปุ่มดู -->
                                                        <td class="text-nowrap">
                                                            <a href="view_project.php?project_id=<?php echo urlencode(encryptUserId($project['project_id'])); ?>"
                                                                class="btn btn-sm btn-primary" title="ดูรายละเอียด">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button class="btn btn-secondary btn-sm" disabled title="Engineer ไม่มีสิทธิ์แก้ไข">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </button>
                                                            <button class="btn btn-secondary btn-sm" disabled title="Engineer ไม่มีสิทธิ์ลบ">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                            <button class="btn btn-secondary btn-sm" disabled title="Engineer ไม่มีสิทธิ์จัดการ">
                                                                <i class="fas fa-project-diagram"></i>
                                                            </button>
                                                            <span class="badge badge-warning ml-1">Engineer View</span>
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
                                                    <td class="text-nowrap" onclick="window.location='view_project.php?project_id=<?php echo urlencode(encryptUserId($project['project_id'])); ?>';" style="cursor: pointer;">
                                                        <div class="truncate-text-project" title="<?php echo htmlspecialchars($project['project_name']); ?>">
                                                            <!-- แสดงสถานะโครงการ -->
                                                            <?php
                                                            if ($memberAccessLevel == 1): ?>
                                                                <span class="badge badge-secondary ml-1">Shared</span>
                                                            <?php elseif ($memberAccessLevel == 2): ?>
                                                                <span class="badge badge-warning ml-1">Shared</span>
                                                            <?php elseif ($project['user_permission'] == 'shared'): ?>
                                                                <span class="badge badge-info ml-1">Shared</span>
                                                            <?php endif; ?>
                                                            &nbsp;&nbsp;
                                                            <?php
                                                            echo truncateText(htmlspecialchars($project['project_name']), 300); // เปลี่ยนค่า length เป็น 300
                                                            ?>
                                                        </div>
                                                    </td>
                                                    <td class="text-nowrap"><?php echo isset($project['company']) ? htmlspecialchars($project['company']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['customer_name'] ?? '') ? htmlspecialchars($project['customer_name'] ?? '') : 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['product_name']); ?> </td>
                                                    <?php
                                                    // ตรวจสอบว่าเป็นโครงการที่แชร์มาจากทีมอื่นหรือไม่
                                                    $isSharedFromOtherTeam = false;

                                                    // สำหรับ Engineer หรือ role อื่นๆ ที่ไม่ใช่ Executive
                                                    if ($role != 'Executive') {
                                                        // ตรวจสอบว่าโครงการมาจากทีมอื่นหรือไม่ (ดูจาก seller's team)
                                                        if (!empty($project['team_name']) && $project['team_name'] != $_SESSION['team_name']) {
                                                            $isSharedFromOtherTeam = true;
                                                        }
                                                    }

                                                    // ตรวจสอบสิทธิ์ในการแสดงข้อมูลการเงิน
                                                    $canViewFinancial = ($role != 'Engineer' && !$isSharedFromOtherTeam);
                                                    ?>

                                                    <?php if ($role != 'Engineer'): ?>
                                                        <?php if ($canViewFinancial): ?>
                                                            <td class="text-nowrap "><?php echo number_format($project['sale_no_vat'], 2); ?></td>
                                                            <td class="text-nowrap "><?php echo number_format($project['cost_no_vat'], 2); ?></td>
                                                            <td class="text-nowrap"><?php echo number_format($project['sale_vat'], 2); ?></td>
                                                            <td class="text-nowrap "><?php echo number_format($project['cost_vat'], 2); ?></td>
                                                            <td class="text-nowrap" style="color: Green; font-weight: bold;"><?php echo number_format($project['gross_profit'], 2); ?></td>
                                                            <td class="text-nowrap" style="color: Green; font-weight: bold;"><?php echo !empty($project['potential']) ? htmlspecialchars($project['potential']) . '%' : ''; ?></td>
                                                            <td class="text-nowrap"><?php echo number_format($project['vat'], 2); ?>%</td>
                                                            <td class="text-nowrap"><?php echo number_format($project['es_cost_no_vat'], 2); ?></td>
                                                            <td class="text-nowrap"><?php echo number_format($project['es_sale_no_vat'], 2); ?></td>
                                                            <td class="text-nowrap"><?php echo number_format($project['es_gp_no_vat'], 2); ?></td>
                                                        <?php else: ?>
                                                            <!-- แสดงเครื่องหมาย - สำหรับโครงการที่แชร์มาจากทีมอื่น -->
                                                            <td class="text-nowrap text-center text-muted">-</td>
                                                            <td class="text-nowrap text-center text-muted">-</td>
                                                            <td class="text-nowrap text-center text-muted">-</td>
                                                            <td class="text-nowrap text-center text-muted">-</td>
                                                            <td class="text-nowrap text-center text-muted">-</td>
                                                            <td class="text-nowrap text-center text-muted">-</td>
                                                            <td class="text-nowrap text-center text-muted">-</td>
                                                            <td class="text-nowrap text-center text-muted">-</td>
                                                            <td class="text-nowrap text-center text-muted">-</td>
                                                            <td class="text-nowrap text-center text-muted">-</td>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    <td class="text-nowrap"><?php echo displayData($project['seller_first_name'] . ' ' . $project['seller_last_name']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['team_name'] ?? 'ไม่ระบุข้อมูล'); ?></td>
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
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($project['creator_first_name'] . ' ' . $project['creator_last_name']); ?></td>
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
                "lengthChange": true,
                "autoWidth": false,
                "scrollX": true,
                "scrollCollapse": true,
                "paging": true,
                "pageLength": 20, // เปลี่ยนค่าเริ่มต้นเป็น 20
                "lengthMenu": [
                    [10, 20, 30, 50, 100, 200, -1],
                    [10, 20, 30, 50, 100, 200, "ทั้งหมด"]
                ], // เพิ่มตัวเลือก "ทั้งหมด"
                "order": [
                    [1, "desc"]
                ],
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "fixedColumns": {
                    leftColumns: 2
                },
                "language": {
                    "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
                    "zeroRecords": "ไม่พบข้อมูลที่ต้องการ",
                    "info": "แสดงรายการที่ _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    "infoEmpty": "ไม่มีข้อมูลที่จะแสดง",
                    "infoFiltered": "(กรองจากข้อมูลทั้งหมด _MAX_ รายการ)",
                    "search": "ค้นหา:",
                    "paginate": {
                        "first": "หน้าแรก",
                        "last": "หน้าสุดท้าย",
                        "next": "ถัดไป",
                        "previous": "ก่อนหน้า"
                    },
                    "processing": "กำลังประมวลผล...",
                    "loadingRecords": "กำลังโหลดข้อมูล...",
                    "emptyTable": "ไม่มีข้อมูลในตาราง"
                },
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>', // ปรับ layout
                "stateSave": true, // จดจำการตั้งค่าของผู้ใช้
                "stateDuration": 60 * 60 * 24 // จดจำเป็นเวลา 24 ชั่วโมง
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