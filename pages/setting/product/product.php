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

// ตรวจสอบว่า User ได้ login แล้วหรือยัง และตรวจสอบ Role
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Executive' && $_SESSION['role'] != 'Sale Supervisor' && $_SESSION['role'] != 'Seller')) {
    // ถ้า Role ไม่ใช่ Executive หรือ Sale Supervisor ให้ redirect ไปยังหน้าอื่น เช่น หน้า Dashboard
    header("Location: " . BASE_URL . "index.php"); // เปลี่ยนเส้นทางไปหน้า Dashboard
    exit(); // หยุดการทำงานของสคริปต์
}

// ดึงข้อมูลจาก session
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];

/**
 * ฟังก์ชันตรวจสอบสิทธิ์การแก้ไขข้อมูลสินค้า
 * @param string $role บทบาทของผู้ใช้
 * @param string $user_id รหัสผู้ใช้ปัจจุบัน
 * @param string $creator_id รหัสผู้สร้างสินค้า
 * @param string $team_id ทีมของผู้ใช้ปัจจุบัน
 * @param string $product_team_id ทีมของผู้สร้างสินค้า
 * @return boolean
 */
function canEditProduct($role, $user_id, $creator_id, $team_id, $product_team_id)
{
    // Executive สามารถแก้ไขได้ทั้งหมด
    if ($role === 'Executive') {
        return true;
    }

    // ผู้สร้างสามารถแก้ไขสินค้าของตัวเองได้
    if ($user_id === $creator_id) {
        return true;
    }

    // Sale Supervisor สามารถแก้ไขได้เฉพาะทีมตัวเอง
    if ($role === 'Sale Supervisor' && $team_id === $product_team_id) {
        return true;
    }

    // กรณีอื่นๆ ไม่สามารถแก้ไขได้
    return false;
}

// รับค่าการค้นหาจากฟอร์ม (method="GET")
$search_service = isset($_GET['searchservice']) ? trim($_GET['searchservice']) : '';

// เพิ่มฟังก์ชันสำหรับตรวจสอบสิทธิ์การเข้าถึงข้อมูลต้นทุน
function canViewCostPrice($role, $team_id, $product_team_id)
{
    if ($role === 'Executive') {
        // Executive สามารถดูข้อมูลได้ทั้งหมด
        return true;
    } elseif (($role === 'Sale Supervisor' || $role === 'Seller') && $team_id === $product_team_id) {
        // Sale Supervisor และ Seller ดูได้เฉพาะทีมตัวเอง
        return true;
    }
    // Role อื่นๆ ไม่สามารถดูได้
    return false;
}

// กำหนดจำนวนรายการต่อหน้า
$items_per_page = 12;

// ดึงค่าหน้าปัจจุบันจาก URL, ถ้าไม่มีจะเป็น 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// คำนวณ offset สำหรับ LIMIT ใน SQL
$offset = ($current_page - 1) * $items_per_page;

// Query พื้นฐานในการดึงข้อมูลสินค้า
$sql_products = "SELECT p.*, 
                        u.first_name AS creator_first_name, 
                        u.last_name AS creator_last_name,
                        u.team_id AS creator_team_id,
                        p.created_by, -- เพิ่มการดึง created_by
                        s.supplier_name,
                        s.company AS supplier_company
                 FROM products p 
                 LEFT JOIN users u ON p.created_by = u.user_id 
                 LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
                 WHERE 1=1";

// สร้าง SQL สำหรับนับจำนวนรายการทั้งหมดเพื่อใช้ใน pagination
$count_sql = "SELECT COUNT(*) AS total FROM products p 
              LEFT JOIN users u ON p.created_by = u.user_id 
              WHERE 1=1";

// เพิ่มเงื่อนไขการค้นหาตามที่ผู้ใช้กรอกมา
if (!empty($search_service)) {
    $search_condition = " AND (p.product_name LIKE :search_service 
                        OR p.product_description LIKE :search_service 
                        OR u.first_name LIKE :search_service 
                        OR u.last_name LIKE :search_service)";
    $sql_products .= $search_condition;
    $count_sql .= $search_condition;
}

// การเรียงลำดับ
$sql_products .= " ORDER BY p.created_at DESC";

// เพิ่ม LIMIT และ OFFSET เพื่อการแบ่งหน้า
$sql_products .= " LIMIT :limit OFFSET :offset";

// เตรียม statement สำหรับนับจำนวนรายการทั้งหมด
$count_stmt = $condb->prepare($count_sql);

// ผูกค่าการค้นหากับ statement สำหรับการนับ
if (!empty($search_service)) {
    $search_param = '%' . $search_service . '%';
    $count_stmt->bindParam(':search_service', $search_param, PDO::PARAM_STR);
}

// Execute query เพื่อนับจำนวนรายการทั้งหมด
$count_stmt->execute();
$row = $count_stmt->fetch(PDO::FETCH_ASSOC);
$total_items = $row['total'];

// คำนวณจำนวนหน้าทั้งหมด
$total_pages = ceil($total_items / $items_per_page);

// เตรียม statement สำหรับดึงข้อมูลสินค้า
$stmt = $condb->prepare($sql_products);

// ผูกค่า LIMIT และ OFFSET
$stmt->bindParam(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

// ผูกค่าการค้นหากับ statement
if (!empty($search_service)) {
    $search_param = '%' . $search_service . '%';
    $stmt->bindParam(':search_service', $search_param, PDO::PARAM_STR);
}

// Execute query เพื่อดึงข้อมูลสินค้า
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
                                                <h5 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
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
                                                                ฿<?php echo number_format($product['selling_price'], 2); ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (canViewCostPrice($role, $team_id, $product['creator_team_id'])): ?>
                                                        <div class="detail-item">
                                                            <span class="detail-label">ราคาต้นทุน:</span>
                                                            <span class="detail-value">
                                                                ฿<?php echo number_format($product['cost_price'], 2); ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="detail-item">
                                                        <span class="detail-label">ผู้จำหน่าย:</span>
                                                        <span class="detail-value">
                                                            <?php
                                                            if (!empty($product['supplier_name'])) {
                                                                echo htmlspecialchars($product['supplier_name']);

                                                                if (!empty($product['supplier_company'])) {
                                                                    echo ' (' . htmlspecialchars($product['supplier_company']) . ')';
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

                                            <!-- ส่วนปุ่มการทำงาน -->
                                            <div class="card-footer">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="btn-group-product">
                                                        <a href="view_product.php?id=<?php echo urlencode(encryptUserId($product['product_id'])); ?>" class="btn btn-custom btn-view">
                                                            <i class="fas fa-eye"></i> ดูรายละเอียด
                                                        </a>

                                                        <?php if (canEditProduct($role, $user_id, $product['created_by'], $team_id, $product['creator_team_id'])): ?>
                                                            <a href="edit_product.php?product_id=<?php echo urlencode(encryptUserId($product['product_id'])); ?>" class="btn btn-custom btn-edit">
                                                                <i class="fas fa-edit"></i> แก้ไข
                                                            </a>
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
                    <a href="#" class="btn-add-product" data-toggle="modal" data-target="#addbtn">
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

    <!-- Add Product Modal -->
    <?php include 'add_product.php'; ?>

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
    </script>
</body>

</html>