<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// ตรวจสอบการตั้งค่า Session
if (!isset($_SESSION['role'], $_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

// ดึงข้อมูลจาก session
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$user_team_ids = $_SESSION['team_ids'] ?? []; // User's teams

// Function to check if user can manage product
function canManageProduct($role, $user_id, $created_by, $user_teams, $product_creator_teams_str) {
    if ($role === 'Executive' || $user_id === $created_by) {
        return true;
    }
    if ($role === 'Sale Supervisor') {
        $product_creator_teams = explode(',', $product_creator_teams_str ?? '');
        return !empty(array_intersect($user_teams, $product_creator_teams));
    }
    return false;
}

// Function to check if user can view cost price
function canViewCostPrice($role, $user_id, $created_by, $user_teams, $product_creator_teams_str) {
    return canManageProduct($role, $user_id, $created_by, $user_teams, $product_creator_teams_str);
}

// Pagination setup
$items_per_page = 12;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Search term
$search_service = trim($_GET['searchservice'] ?? '');

// --- Build Query ---
$params = [];
$base_sql = "FROM products p 
             LEFT JOIN users u ON p.created_by = u.user_id
             LEFT JOIN projects pr ON p.product_id = pr.product_id
             LEFT JOIN customers c ON pr.customer_id = c.customer_id";

$where_conditions = [];
if (!empty($search_service)) {
    $where_conditions[] = "(p.product_name LIKE :search OR p.product_description LIKE :search OR u.first_name LIKE :search OR u.last_name LIKE :search)";
    $params[':search'] = "%$search_service%";
}

// Filtering for non-executives
if ($role !== 'Executive') {
    if (!empty($user_team_ids)) {
        $team_placeholders = [];
        foreach ($user_team_ids as $key => $id) {
            $p = ':team_id_' . $key;
            $team_placeholders[] = $p;
            $params[$p] = $id;
        }
        $in_clause = implode(',', $team_placeholders);
        $where_conditions[] = "(p.created_by = :user_id OR p.created_by IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id IN ($in_clause)))";
        $params[':user_id'] = $user_id;
    } else {
        $where_conditions[] = "p.created_by = :user_id";
        $params[':user_id'] = $user_id;
    }
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(' AND ', $where_conditions) : "";

// Count total items
$count_sql = "SELECT COUNT(DISTINCT p.product_id) AS total $base_sql $where_clause";
$count_stmt = $condb->prepare($count_sql);
$count_stmt->execute($params);
$total_items = $count_stmt->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);

// Main data query
$sql_products = "SELECT p.*, 
                        u.first_name AS creator_first_name, 
                        u.last_name AS creator_last_name,
                        p.created_by,
                        (SELECT GROUP_CONCAT(t.team_name SEPARATOR ', ') FROM teams t JOIN user_teams ut ON t.team_id = ut.team_id WHERE ut.user_id = p.created_by) as team_name,
                        (SELECT GROUP_CONCAT(ut.team_id) FROM user_teams ut WHERE ut.user_id = p.created_by) as creator_team_ids,
                        COUNT(pr.project_id) as project_count,
                        GROUP_CONCAT(DISTINCT CONCAT(pr.project_id, '|', pr.project_name, '|', COALESCE(c.company, c.customer_name)) SEPARATOR '|||') as project_details
                 $base_sql
                 $where_clause
                 GROUP BY p.product_id
                 ORDER BY p.created_at DESC
                 LIMIT :limit OFFSET :offset";

$stmt = $condb->prepare($sql_products);
$stmt->bindParam(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
foreach ($params as $key => &$val) {
    $stmt->bindParam($key, $val);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "product"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Product Management</title>
    <?php include '../../../include/header.php' ?>

    <!-- เพิ่ม Fonts และ Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- โหลด SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- โหลด lightbox -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">

    <!-- Custom CSS -->
    <style>
        /* ตั้งค่าฟอนต์หลัก */
        body {
            font-family: 'Noto Sans Thai', sans-serif;
        }

        /* การ์ดสินค้า */
        .product-card {
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 25px;
            border-radius: 15px;
            overflow: hidden;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
        }

        /* ส่วนหัวการ์ด */
        .card-img-top-container {
            height: 220px;
            overflow: hidden;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .card-img-top {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            padding: 15px;
            transition: transform 0.3s;
        }

        .product-card:hover .card-img-top {
            transform: scale(1.05);
        }

        /* แบดจ์ */
        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            border-radius: 30px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .badge-new {
            background-color: #28a745;
            color: white;
        }

        .badge-creator {
            background-color: #17a2b8;
            color: white;
        }

        /* เนื้อหาการ์ด */
        .card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            line-height: 1.4;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .product-description {
            color: #666;
            margin-bottom: 15px;
            font-size: 14px;
            flex-grow: 1;
        }

        /* รายละเอียดสินค้า */
        .product-details {
            font-size: 13px;
            margin-bottom: 15px;
        }

        .detail-item {
            display: flex;
            margin-bottom: 6px;
            align-items: baseline;
        }

        .detail-label {
            font-weight: 600;
            color: #555;
            width: 100px;
            flex-shrink: 0;
        }

        .detail-value {
            color: #666;
            flex-grow: 1;
        }

        /* ส่วนท้ายการ์ด */
        .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
            padding: 15px 20px;
        }

        /* กลุ่มปุ่ม */
        .btn-group-product {
            display: flex;
            gap: 8px;
        }

        .btn-custom {
            border-radius: 30px;
            padding: 6px 15px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .btn-view {
            color: #fff;
            background-color: #007bff;
            border: none;
        }

        .btn-edit {
            color: #fff;
            background-color: #17a2b8;
            border: none;
        }

        .btn-view:hover,
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: #fff;
        }

        /* สำหรับกล่องค้นหา */
        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-box .form-control {
            border-radius: 30px;
            padding: 12px 20px;
            padding-right: 50px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border: 1px solid #ddd;
            transition: all 0.3s;
        }

        .search-box .form-control:focus {
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            border-color: #80bdff;
        }

        /* แก้ไขขนาดปุ่มค้นหาให้เล็กลงและพอดีกับช่องค้นหา */
        .search-box .btn-search {
            position: absolute;
            right: 5px;
            top: 5px;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            /* เพิ่มการกำหนดขนาดไอคอน */
        }

        /* ปรับแต่งปุ่มเพิ่มสินค้า */
        .btn-add-product {
            position: fixed;
            bottom: 80px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #28a745;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            z-index: 1050;
            transition: all 0.3s;
        }

        .btn-add-product:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }

        /* เทมเพลตสำหรับ skeleton loading */
        .skeleton {
            animation: skeleton-loading 1s linear infinite alternate;
            border-radius: 5px;
            height: 15px;
            margin-bottom: 8px;
        }

        @keyframes skeleton-loading {
            0% {
                background-color: #eee;
            }

            100% {
                background-color: #ddd;
            }
        }

        /* ปรับแต่งสำหรับอุปกรณ์มือถือ */
        @media (max-width: 768px) {
            .card-img-top-container {
                height: 180px;
            }

            .product-title {
                font-size: 16px;
            }

            .detail-label {
                width: 90px;
            }

            .btn-add-product {
                width: 50px;
                height: 50px;
                font-size: 20px;
                bottom: 20px;
                right: 20px;
            }
        }

        /* สีสำหรับราคา */
        .price-highlight {
            color: #e83e8c;
            font-weight: 600;
        }

        /* สีสำหรับ "ไม่มีข้อมูล" */
        .no-data {
            color: #999;
            font-style: italic;
        }

        /* พื้นหลังแสดงผลสินค้า */
        .products-container {
            background-color: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            box-shadow: inset 0 0 15px rgba(0, 0, 0, 0.03);
        }

        /* จัดการระยะห่างในหัวข้อและไอคอน */
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-header i {
            margin-right: 10px;
            color: #007bff;
        }

        .section-header h4 {
            margin-bottom: 0;
            font-weight: 600;
        }

        /* กลุ่มตัวกรอง */
        .filter-group {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-badge {
            background-color: #f0f0f0;
            border-radius: 30px;
            padding: 8px 15px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
            color: #555;
        }

        .filter-badge:hover,
        .filter-badge.active {
            background-color: #007bff;
            color: white;
        }

        /* สำหรับแสดงเมื่อไม่มีสินค้า */
        .no-products {
            text-align: center;
            padding: 50px 0;
            color: #777;
        }

        .no-products i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 15px;
            display: block;
        }

        .no-products p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        /* สไตล์สำหรับ pagination */
        .pagination {
            margin-top: 30px;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }

        .pagination .page-link {
            color: #007bff;
            border-radius: 5px;
            margin: 0 3px;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background-color: #e9ecef;
            transform: scale(1.05);
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            cursor: default;
        }

        .badge-project-count {
            background-color: #17a2b8;
            color: white;
            font-size: 0.8em;
        }

        .btn-delete {
            color: #fff;
            background-color: #dc3545;
            border: none;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: #fff;
            background-color: #c82333;
        }



        /* CSS สำหรับ Project Details Modal */
        .swal-wide {
            max-width: 90% !important;
        }

        .swal-html-container {
            text-align: left !important;
            padding: 0 !important;
        }

        .project-list {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px;
            background-color: #fff;
        }

        .project-item {
            transition: all 0.2s ease;
        }

        .project-item:hover {
            background-color: #f0f8ff !important;
            border-color: #007bff !important;
            transform: translateY(-1px);
        }

        /* สไตล์สำหรับปุ่มจำนวนโครงการ */
        .project-count-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 15px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
        }

        .project-count-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .project-count-badge:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
        }

        .no-projects-text {
            color: #999;
            font-style: italic;
            font-size: 13px;
        }

        /* Animation สำหรับ loading */
        .loading-projects {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }



        /* สไตล์สำหรับปุ่มโครงการขนาดเล็ก - คลิกได้ */
        .project-count-badge-small {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 4px 8px;
            font-weight: 500;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 123, 255, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .project-count-badge-small:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 123, 255, 0.4);
            color: white;
            background: linear-gradient(135deg, #0056b3 0%, #003d82 100%);
        }

        .project-count-badge-small:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.3);
        }

        .project-count-badge-small i {
            font-size: 10px;
        }

        /* สไตล์สำหรับข้อความโครงการ - ไม่คลิกได้ */
        .project-count-readonly {
            background-color: #e9ecef;
            color: #6c757d;
            border-radius: 12px;
            padding: 4px 8px;
            font-weight: 500;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            border: 1px solid #dee2e6;
        }

        .project-count-readonly i {
            font-size: 10px;
            color: #adb5bd;
        }

        /* สไตล์สำหรับข้อความยังไม่ใช้งาน - ขนาดเล็ก */
        .no-projects-text-small {
            color: #999;
            font-style: italic;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .no-projects-text-small i {
            font-size: 10px;
            color: #ccc;
        }

        /* ปรับ detail-label ให้เล็กลง */
        .detail-item .detail-label {
            font-weight: 600;
            color: #555;
            width: 85px;
            /* เล็กลงจาก 100px */
            flex-shrink: 0;
            font-size: 12px;
            /* เพิ่มการกำหนดขนาดฟอนต์ */
        }

        .detail-item .detail-value {
            color: #666;
            flex-grow: 1;
            font-size: 12px;
            /* เพิ่มการกำหนดขนาดฟอนต์ */
        }

        /* Responsive สำหรับมือถือ */
        @media (max-width: 768px) {
            .detail-item .detail-label {
                width: 75px;
                font-size: 11px;
            }

            .detail-item .detail-value {
                font-size: 11px;
            }

            .project-count-badge-small,
            .project-count-readonly {
                font-size: 10px;
                padding: 3px 6px;
            }

            .no-projects-text-small {
                font-size: 10px;
            }
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include '../../../include/navbar.php' ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">
                                <i class="fas fa-box-open text-primary"></i> Product Management
                            </h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Product Management</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">

                    <!-- แสดงข้อความแจ้งเตือน -->
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo $_SESSION['error_message']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>

                    <!-- Search Section -->
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-search"></i> ค้นหาสินค้า
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 col-md-8">
                                    <form action="product.php" method="GET">
                                        <!-- ส่งค่าหน้าปัจจุบันไปด้วยถ้ามีการค้นหา -->
                                        <input type="hidden" name="page" value="1">
                                        <div class="search-box">
                                            <input type="text" class="form-control" id="searchservice" name="searchservice" value="<?php echo htmlspecialchars($search_service, ENT_QUOTES, 'UTF-8'); ?>" placeholder="ค้นหาตามชื่อสินค้า, รายละเอียด หรือผู้สร้าง...">
                                            <button type="submit" class="btn btn-primary btn-search">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-lg-6 col-md-4 text-right">
                                    <div class="filter-group d-flex justify-content-end">
                                        <!-- ตัวกรองสามารถเพิ่มตามต้องการ -->
                                        <div class="filter-badge">
                                            <i class="fas fa-sort-amount-down"></i> เรียงล่าสุด
                                        </div>
                                        <div class="filter-badge">
                                            <i class="fas fa-filter"></i> ตัวกรอง
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($search_service)): ?>
                                <div class="mt-3">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> ผลการค้นหาสำหรับ: <strong><?php echo htmlspecialchars($search_service); ?></strong>
                                        <a href="product.php" class="float-right">ล้างการค้นหา</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- /.Search Section -->

                    <!-- Products Section -->
                    <div class="products-container">
                        <div class="section-header">
                            <i class="fas fa-box-open fa-lg"></i>
                            <h4>สินค้าทั้งหมด (<?php echo $total_items; ?> รายการ)</h4>
                        </div>

                        <?php if (empty($products)): ?>
                            <div class="no-products">
                                <i class="fas fa-search"></i>
                                <p>ไม่พบสินค้าที่ค้นหา</p>
                                <a href="product.php" class="btn btn-primary">แสดงสินค้าทั้งหมด</a>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($products as $index => $product): ?>
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="product-card">
                                            <!-- ส่วนรูปภาพสินค้า -->
                                            <div class="card-img-top-container">
                                                <a href="<?php echo !empty($product['main_image']) ? '../../../uploads/product_images/' . $product['main_image'] : '../../../assets/img/pit.png'; ?>"
                                                    data-lightbox="product-image-<?php echo $product['product_id']; ?>"
                                                    data-title="<?php echo htmlspecialchars($product['product_name']); ?>">
                                                    <img src="<?php echo !empty($product['main_image']) ? '../../../uploads/product_images/' . $product['main_image'] : '../../../assets/img/pit.png'; ?>"
                                                        class="card-img-top"
                                                        alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                                </a>

                                                <?php if (strtotime($product['created_at']) > strtotime('-7 days')): ?>
                                                    <span class="product-badge badge-new">ใหม่</span>
                                                <?php endif; ?>

                                                <?php if ($user_id === $product['created_by']): ?>
                                                    <span class="product-badge badge-creator">ผู้สร้าง</span>
                                                <?php endif; ?>
                                            </div>

                                            <!-- ส่วนรายละเอียดสินค้า -->
                                            <div class="card-body">
                                                <a href="view_product.php?id=<?php echo urlencode(encryptUserId($product['product_id'])); ?>" class="text-decoration-none">
                                                    <h5 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                                                </a>
                                                <a href="view_product.php?id=<?php echo urlencode(encryptUserId($product['product_id'])); ?>" class="text-decoration-none">
                                                    <p class="product-description">
                                                        <?php
                                                        if (!empty($product['product_description'])) {
                                                            echo htmlspecialchars(
                                                                strlen($product['product_description']) > 100 ?
                                                                    substr($product['product_description'], 0, 97) . '...' :
                                                                    $product['product_description']
                                                            );
                                                        } else {
                                                            echo '<span class="no-data">ไม่มีคำอธิบายสินค้า</span>';
                                                        }
                                                        ?>
                                                    </p>
                                                </a>

                                                <div class="product-details">
                                                    <div class="detail-item">
                                                        <span class="detail-label">หน่วยนับ:</span>
                                                        <span class="detail-value">
                                                            <?php echo !empty($product['unit']) ? htmlspecialchars($product['unit']) : '<span class="no-data">ไม่ระบุ</span>'; ?>
                                                        </span>
                                                    </div>

                                                    <?php if ($role != 'Engineer'): ?>
                                                        <div class="detail-item">
                                                            <span class="detail-label">ราคาขาย:</span>
                                                            <span class="detail-value price-highlight">
                                                                ฿<?php echo number_format($product['selling_price'] ?? 0, 2); ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (canViewCostPrice($role, $user_id, $product['created_by'], $user_team_ids, $product['creator_team_ids'])): ?>
                                                        <div class="detail-item">
                                                            <span class="detail-label">ราคาต้นทุน:</span>
                                                            <span class="detail-value">
                                                                ฿<?php echo number_format($product['cost_price'] ?? 0, 2); ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="detail-item">
                                                        <span class="detail-label">ทีมขาย (เจ้าของ):</span>
                                                        <span class="detail-value">
                                                            <?php
                                                            if (!empty($product['team_name'])) {
                                                                echo htmlspecialchars($product['team_name']);

                                                                if (!empty($product['team_description'])) {
                                                                    echo ' (' . htmlspecialchars($product['team_description']) . ')';
                                                                }
                                                            } else {
                                                                echo '<span class="no-data">ไม่ระบุ</span>';
                                                            }
                                                            ?>
                                                        </span>
                                                    </div>

                                                    <div class="detail-item">
                                                        <span class="detail-label">ผู้สร้าง:</span>
                                                        <span class="detail-value">
                                                            <?php echo htmlspecialchars($product['creator_first_name'] . " " . $product['creator_last_name']); ?>
                                                        </span>
                                                    </div>

                                                    <div class="detail-item">
                                                        <span class="detail-label">วันที่สร้าง:</span>
                                                        <span class="detail-value">
                                                            <?php echo date('d M Y', strtotime($product['created_at'])); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- เพิ่มส่วนนี้ในการ์ด หลังจาก detail-item สุดท้าย -->
                                            <div class="detail-item">
                                                <span class="detail-label ml-4">โครงการที่ใช้:</span>
                                                <span class="detail-value">
                                                    <?php if (isset($product['project_count']) && $product['project_count'] > 0): ?>
                                                        <?php if (canManageProduct($role, $user_id, $product['created_by'], $user_team_ids, $product['creator_team_ids'])): ?>
                                                            <!-- มีสิทธิ์ - แสดงปุ่มคลิกได้ -->
                                                            <button type="button"
                                                                class="project-count-badge-small"
                                                                onclick="showProjectModal('<?php echo htmlspecialchars($product['project_details'] ?? '', ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?>')"
                                                                title="คลิกเพื่อดูรายละเอียดโครงการ">
                                                                <i class="fas fa-project-diagram"></i>
                                                                <?php echo $product['project_count']; ?> โครงการ
                                                            </button>
                                                        <?php else: ?>
                                                            <!-- ไม่มีสิทธิ์ - แสดงแค่ตัวเลข -->
                                                            <span class="project-count-readonly">
                                                                <i class="fas fa-project-diagram"></i>
                                                                <?php echo $product['project_count']; ?> โครงการ
                                                            </span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="no-projects-text-small">
                                                            <i class="fas fa-minus-circle"></i>
                                                            ยังไม่ใช้งาน
                                                        </span>
                                                    <?php endif; ?>
                                                </span>
                                            </div>

                                            <!-- ส่วนปุ่มการทำงาน -->
                                            <div class="card-footer">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="btn-group-product">
                                                        <a href="view_product.php?id=<?php echo urlencode(encryptUserId($product['product_id'])); ?>" class="btn btn-custom btn-view">
                                                            <i class="fas fa-eye"></i> ดูข้อมูล
                                                        </a>

                                                        <?php if (canManageProduct($role, $user_id, $product['created_by'], $user_team_ids, $product['creator_team_ids'])): ?>
                                                            <a href="edit_product.php?product_id=<?php echo urlencode(encryptUserId($product['product_id'])); ?>" class="btn btn-custom btn-edit">
                                                                <i class="fas fa-edit"></i> แก้ไข
                                                            </a>
                                                            <!-- แทนที่ปุ่มลบเดิมด้วยโค้ดนี้ -->
                                                            <button type="button"
                                                                class="btn btn-custom btn-delete delete-product-btn"
                                                                data-product-id="<?php echo urlencode(encryptUserId($product['product_id'])); ?>"
                                                                data-product-name="<?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-project-count="<?php echo isset($product['project_count']) ? $product['project_count'] : 0; ?>"
                                                                data-project-details="<?php echo htmlspecialchars($product['project_details'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                                <i class="fas fa-trash"></i> ลบข้อมูล
                                                            </button>
                                                        <?php else: ?>
                                                            <small class="text-muted">
                                                                <i class="fas fa-lock"></i>
                                                                ไม่มีสิทธิ์จัดการ
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- วันที่อัพเดทล่าสุด (ถ้ามี) -->
                                                    <?php if (isset($product['updated_at']) && $product['updated_at'] !== $product['created_at']): ?>
                                                        <small class="text-muted">อัพเดท: <?php echo date('d/m/y', strtotime($product['updated_at'])); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <div class="d-flex justify-content-center mt-4">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination">
                                            <!-- ปุ่ม Previous -->
                                            <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="<?php echo ($current_page > 1) ? '?page=' . ($current_page - 1) . (!empty($search_service) ? '&searchservice=' . urlencode($search_service) : '') : '#'; ?>">
                                                    <i class="fas fa-chevron-left"></i> Previous
                                                </a>
                                            </li>

                                            <!-- ลิงก์หน้าต่างๆ -->
                                            <?php
                                            // แสดงลิงก์หน้าไม่เกิน 5 หน้า
                                            $start_page = max(1, $current_page - 2);
                                            $end_page = min($total_pages, $current_page + 2);

                                            // ถ้าอยู่หน้าท้ายๆ ให้แสดงหน้าแรกๆ มากขึ้น
                                            if ($current_page > $total_pages - 2) {
                                                $start_page = max(1, $total_pages - 4);
                                            }

                                            // ถ้าอยู่หน้าแรกๆ ให้แสดงหน้าท้ายๆ มากขึ้น
                                            if ($current_page < 3) {
                                                $end_page = min($total_pages, 5);
                                            }

                                            // แสดงหน้าแรก
                                            if ($start_page > 1) {
                                                echo '<li class="page-item"><a class="page-link" href="?page=1' . (!empty($search_service) ? '&searchservice=' . urlencode($search_service) : '') . '">1</a></li>';
                                                if ($start_page > 2) {
                                                    echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                                }
                                            }

                                            // แสดงหน้าปัจจุบันและหน้าใกล้เคียง
                                            for ($i = $start_page; $i <= $end_page; $i++) {
                                                echo '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '">
                                                        <a class="page-link" href="?page=' . $i . (!empty($search_service) ? '&searchservice=' . urlencode($search_service) : '') . '">' . $i . '</a>
                                                      </li>';
                                            }

                                            // แสดงหน้าสุดท้าย
                                            if ($end_page < $total_pages) {
                                                if ($end_page < $total_pages - 1) {
                                                    echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                                }
                                                echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . (!empty($search_service) ? '&searchservice=' . urlencode($search_service) : '') . '">' . $total_pages . '</a></li>';
                                            }
                                            ?>

                                            <!-- ปุ่ม Next -->
                                            <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="<?php echo ($current_page < $total_pages) ? '?page=' . ($current_page + 1) . (!empty($search_service) ? '&searchservice=' . urlencode($search_service) : '') : '#'; ?>">
                                                    Next <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <!-- /.Products Section -->

                    <!-- ปุ่มเพิ่มสินค้า (ลอยอยู่ด้านล่างขวา) -->
                    <a href="add_product.php" class="btn-add-product">
                        <i class="fas fa-plus"></i>
                    </a>

                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Footer -->
        <?php include('../../../include/footer.php'); ?>
    </div>
    <!-- ./wrapper -->

    <!-- JS สำหรับ Lightbox -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        // ตั้งค่า Lightbox
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'showImageNumberLabel': false,
            'disableScrolling': true
        });

        // ตั้งค่า Select2
        $(function() {
            $('.select2').select2();

            // เพิ่มเอฟเฟกต์เมื่อคลิกที่ filter-badge
            $('.filter-badge').click(function() {
                $(this).toggleClass('active');
            });

            // เพิ่ม Animation เมื่อโหลดหน้า
            $('.product-card').each(function(index) {
                $(this).css({
                    'animation': 'fadeInUp 0.5s',
                    'animation-delay': (index * 0.1) + 's',
                    'animation-fill-mode': 'both'
                });
            });

            // กำหนด CSS Animation
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    @keyframes fadeInUp {
                        from {
                            opacity: 0;
                            transform: translate3d(0, 30px, 0);
                        }
                        to {
                            opacity: 1;
                            transform: translate3d(0, 0, 0);
                        }
                    }
                `)
                .appendTo('head');
        });

        // ฟังก์ชันรีเฟรชตาราง (ถ้าต้องการ)
        window.refreshProductTable = function() {
            location.reload();
        };



        // อัปเดต Handle Delete Product ให้แสดงรายละเอียดโครงการ
        $('.delete-product-btn').click(function() {
            const productId = $(this).data('product-id');
            const productName = $(this).data('product-name');
            const projectCount = $(this).data('project-count');
            const projectDetails = $(this).data('project-details');

            // ตรวจสอบว่า Product ถูกใช้งานใน Project หรือไม่
            if (projectCount > 0) {
                // แสดงรายละเอียดโครงการที่ใช้งาน Product
                showProjectDetails(projectDetails, productName);
                return;
            }

            // แสดงกล่องยืนยันการลบ (กรณีไม่มีโครงการใช้งาน)
            Swal.fire({
                title: 'ยืนยันการลบ?',
                html: `คุณต้องการลบ Product<br><strong>"${productName}"</strong><br>ใช่หรือไม่?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '<i class="fas fa-trash"></i> ใช่, ลบ!',
                cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // แสดงข้อความกำลังลบ
                    Swal.fire({
                        title: 'กำลังลบ...',
                        text: 'กรุณารอสักครู่',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // ส่งไปยังหน้าลบ
                    window.location.href = `delete_product.php?product_id=${productId}`;
                }
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);




        //ฟังก์ชัน showProjectDetails 
        function showProjectDetails(projectDetails, productName) {
            if (!projectDetails || projectDetails === '') {
                return;
            }

            // แปลงข้อมูลโครงการ
            const projects = projectDetails.split('|||').map(detail => {
                const parts = detail.split('|');
                return {
                    id: parts[0],
                    name: parts[1] || 'ไม่ระบุ',
                    customer: parts[2] || 'ไม่ระบุ'
                };
            });

            // สร้าง HTML สำหรับแสดงรายการโครงการ
            let projectListHtml = '<div class="project-list" style="max-height: 300px; overflow-y: auto;">';

            projects.forEach((project, index) => {
                // สร้าง URL ที่ถูกต้องโดยใช้ PHP function ผ่าน AJAX
                // หรือใช้ URL ที่มี parameter แบบไม่เข้ารหัสก่อน (ขึ้นอยู่กับระบบ)
                const projectViewUrl = `../../project/view_project.php?project_id=${encodeURIComponent(project.id)}`;

                projectListHtml += `
            <div class="project-item" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 12px; margin-bottom: 10px; background-color: #f9f9f9;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1" style="color: #2c3e50;">
                            <i class="fas fa-project-diagram text-primary"></i>
                            ${project.name}
                        </h6>
                        <small class="text-muted">
                            <i class="fas fa-building"></i>
                            ลูกค้า: ${project.customer}
                        </small>
                        <br>
                    </div>
                    <div>
                        <button type="button" 
                                class="btn btn-sm btn-outline-primary" 
                                onclick="openEncryptedProjectUrl('${project.id}')"
                                title="คลิกเพื่อดูโครงการ">
                            <i class="fas fa-external-link-alt"></i> ดูโครงการ
                        </button>
                    </div>
                </div>
            </div>
        `;
            });

            projectListHtml += '</div>';

            // แสดง SweetAlert พร้อมรายละเอียดโครงการ
            Swal.fire({
                title: `<i class="fas fa-exclamation-triangle text-warning"></i> ไม่สามารถลบได้!`,
                html: `
            <div class="text-left">
                <p class="mb-3">
                    Product <strong>"${productName}"</strong> ถูกใช้งานใน <strong>${projects.length}</strong> โครงการ<br>
                    กรุณาลบ Product ออกจากโครงการเหล่านี้ก่อน
                </p>
                <hr>
                <h6 class="mb-3"><i class="fas fa-list"></i> รายการโครงการที่ใช้งาน:</h6>
                ${projectListHtml}
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        คลิกปุ่ม "ดูโครงการ" เพื่อเปิดหน้าโครงการในแท็บใหม่
                    </small>
                </div>
            </div>
        `,
                icon: 'warning',
                width: '650px',
                confirmButtonText: '<i class="fas fa-check"></i> เข้าใจแล้ว',
                confirmButtonColor: '#3085d6',
                customClass: {
                    popup: 'swal-wide',
                    htmlContainer: 'swal-html-container'
                }
            });
        }

        // ฟังก์ชันตรวจสอบสิทธิ์ก่อนแสดง Modal
        function showProjectModal(projectDetails, productName) {
            // ตรวจสอบว่ามีข้อมูลโครงการหรือไม่
            if (!projectDetails || projectDetails === '') {
                Swal.fire({
                    title: 'ไม่มีข้อมูล',
                    text: 'Product นี้ยังไม่ได้ใช้งานในโครงการใด',
                    icon: 'info',
                    confirmButtonText: 'เข้าใจแล้ว',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // แสดงรายละเอียดโครงการ
            showProjectDetails(projectDetails, productName);
        }

        // ป้องกันการคลิกบน element ที่ไม่มีสิทธิ์
        $(document).ready(function() {
            $('.project-count-readonly').click(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'ไม่มีสิทธิ์',
                    text: 'คุณไม่มีสิทธิ์ดูรายละเอียดโครงการนี้',
                    icon: 'warning',
                    confirmButtonText: 'เข้าใจแล้ว',
                    confirmButtonColor: '#3085d6'
                });
            });
        });



        // ฟังก์ชันสำหรับเข้ารหัส project_id และเปิด URL
        function openEncryptedProjectUrl(projectId) {
            if (!projectId || projectId.trim() === '') {
                console.error('Project ID is empty');
                Swal.fire({
                    title: 'ข้อผิดพลาด',
                    text: 'ไม่พบรหัสโครงการ',
                    icon: 'error',
                    confirmButtonText: 'เข้าใจแล้ว'
                });
                return;
            }

            // แสดง loading
            Swal.fire({
                title: 'กำลังเปิดโครงการ...',
                text: 'กรุณารอสักครู่',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // ส่ง AJAX request เพื่อเข้ารหัส project_id
            $.ajax({
                url: 'encrypt_project_id.php', // สร้างไฟล์นี้
                method: 'POST',
                data: {
                    project_id: projectId.trim()
                },
                dataType: 'json',
                success: function(response) {
                    Swal.close();

                    if (response.success && response.encrypted_id) {
                        // เปิด URL ที่เข้ารหัสแล้ว
                        const encryptedUrl = `../../project/view_project.php?project_id=${encodeURIComponent(response.encrypted_id)}`;
                        window.open(encryptedUrl, '_blank', 'noopener,noreferrer');
                    } else {
                        Swal.fire({
                            title: 'ข้อผิดพลาด',
                            text: response.message || 'ไม่สามารถเข้ารหัสรหัสโครงการได้',
                            icon: 'error',
                            confirmButtonText: 'เข้าใจแล้ว'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    console.error('AJAX Error:', error);

                    // ใช้วิธี fallback - ลองเปิดด้วย ID ตรงๆ
                    const fallbackUrl = `../../project/view_project.php?project_id=${encodeURIComponent(projectId)}`;
                    window.open(fallbackUrl, '_blank', 'noopener,noreferrer');
                }
            });
        }
    </script>
</body>

</html>