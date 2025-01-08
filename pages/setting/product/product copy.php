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

// รับค่าการค้นหาจากฟอร์ม (method="GET")
$search_service = isset($_GET['searchservice']) ? trim($_GET['searchservice']) : '';

// Query พื้นฐานในการดึงข้อมูลสินค้า
$sql_products = "SELECT p.*, u.first_name AS creator_first_name, u.last_name AS creator_last_name 
                FROM products p 
                LEFT JOIN users u ON p.created_by = u.user_id 
                WHERE 1=1";

// เพิ่มเงื่อนไขการค้นหาตามที่ผู้ใช้กรอกมา
if (!empty($search_service)) {
    $sql_products .= " AND (p.product_name LIKE :search_service 
                        OR p.product_description LIKE :search_service 
                        OR u.first_name LIKE :search_service 
                        OR u.last_name LIKE :search_service)";
}

$sql_products .= " ORDER BY p.created_at DESC";

// เตรียม statement และ bind ค่า
$stmt = $condb->prepare($sql_products);

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
    <title>SalePipeline | Product Magement</title>
    <?php include '../../../include/header.php' ?>

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

    <!-- เพิ่ม CSS สำหรับ lightbox -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">

    <!-- ปรับแต่ง CSS -->
    <style>
        .product-image {
            width: 100%;
            height: 150px;
            /* กำหนดความสูงคงที่ */
            object-fit: contain;
            /* เปลี่ยนจาก cover เป็น contain */
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #f8f9fa;
            /* เพิ่มสีพื้นหลังอ่อนๆ */
        }

        .product-image:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, .05);
        }

        .table th {
            background-color: #f8f9fa;
        }
    </style>

    <style>
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn-group .btn {
            margin-right: 5px;
        }

        .btn-outline-primary:hover,
        .btn-outline-info:hover,
        .btn-outline-danger:hover {
            color: #fff;
        }

        .table th {
            font-weight: 600;
        }

        .font-weight-bold {
            font-weight: 600 !important;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <!-- Navbar -->
        <!-- Main Sidebar Container -->
        <?php include '../../../include/navbar.php' ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Product Magement</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Product Magement v1</li>
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

                            <!-- ส่วน Search -->
                            <section class="content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-info">
                                            <div class="card-header">
                                                <h3 class="card-title">ค้นหา</h3>
                                            </div>
                                            <div class="card-body">
                                                <form action="#" method="GET">
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" id="searchservice" name="searchservice" value="<?php echo htmlspecialchars($search_service, ENT_QUOTES, 'UTF-8'); ?>" placeholder="ค้นหา...">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="input-group-append">
                                                                <button type="submit" class="btn btn-primary " id="search" name="search">ค้นหา</button>
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
                            <div class="col-md-12 pb-3">
                                <a href="add_product.php" class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#addbtn">เพิ่มข้อมูล<i class=""></i></a>
                                <!-- Add Product -->
                                <?php include 'add_product.php'; ?>
                                <!-- Add Product -->
                            </div><br>
                            <!-- //Section ปุ่มเพิ่มข้อมูล -->

                            <!-- Section ตารางแสดงผล -->
                            <!-- Section ตารางแสดงผล -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Product List</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <!-- ปรับแต่งตาราง -->
                                    <!-- ปรับแต่ง CSS ในส่วน head -->
                                    <style>
                                        /* ปรับแต่งขนาดและสไตล์ของตาราง */
                                        .table {
                                            margin-bottom: 0;
                                        }

                                        /* ปรับขนาดคอลัมน์รูปภาพ */
                                        .product-image-column {
                                            width: 180px !important;
                                            /* ลดขนาดลงจาก 200px */
                                            max-width: 180px !important;
                                        }

                                        /* ปรับแต่งรูปภาพ */
                                        .product-image {
                                            width: 160px;
                                            /* ลดขนาดลง */
                                            height: 120px;
                                            /* ลดความสูง */
                                            object-fit: contain;
                                            border-radius: 6px;
                                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                            background-color: #fff;
                                            padding: 8px;
                                            border: 1px solid #dee2e6;
                                        }

                                        /* เพิ่ม animation เมื่อ hover */
                                        .product-image:hover {
                                            transform: scale(1.03);
                                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
                                        }

                                        /* ปรับแต่งส่วนรายละเอียดสินค้า */
                                        .product-details {
                                            padding: 10px 15px;
                                        }

                                        .product-title {
                                            font-size: 1.1rem;
                                            font-weight: 600;
                                            margin-bottom: 8px;
                                            color: #2c3e50;
                                        }

                                        .product-description {
                                            font-size: 0.9rem;
                                            color: #666;
                                            margin-bottom: 10px;
                                        }

                                        .product-meta {
                                            font-size: 0.85rem;
                                            color: #777;
                                            margin-bottom: 5px;
                                        }

                                        /* ปรับแต่งปุ่มกดต่างๆ */
                                        .btn-action-group {
                                            margin-top: 10px;
                                        }

                                        .btn-action-group .btn {
                                            padding: 4px 12px;
                                            font-size: 0.85rem;
                                            margin-right: 5px;
                                        }
                                    </style>

                                    <!-- แก้ไขโครงสร้าง HTML ของตาราง -->
                                    <table id="example1" class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="bg-primary text-white">
                                                <th class="text-center" style="width: 5%">No.</th>
                                                <th class="text-center product-image-column">Image</th>
                                                <th>Product Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $index => $product) { ?>
                                                <tr>
                                                    <td class="text-center align-middle"><?php echo $index + 1; ?></td>
                                                    <td class="text-center product-image-column">
                                                        <a href="<?php echo !empty($product['main_image']) ? '../../../uploads/product_images/' . $product['main_image'] : '../../../assets/img/pit.png'; ?>"
                                                            data-lightbox="product-image"
                                                            data-title="<?php echo htmlspecialchars($product['product_name']); ?>">
                                                            <img src="<?php echo !empty($product['main_image']) ? '../../../uploads/product_images/' . $product['main_image'] : '../../../assets/img/pit.png'; ?>"
                                                                class="product-image"
                                                                alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                                        </a>
                                                    </td>
                                                    <td class="product-details">
                                                        <div class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></div>
                                                        <div class="product-description"><?php echo htmlspecialchars($product['product_description']); ?></div>
                                                        <div class="product-meta">
                                                            <strong>Created By:</strong> <?php echo htmlspecialchars($product['creator_first_name'] . " " . $product['creator_last_name']); ?>
                                                        </div>
                                                        <div class="product-meta">
                                                            <strong>Created Date:</strong> <?php echo date('F j, Y', strtotime($product['created_at'])); ?>
                                                        </div>
                                                        <div class="btn-action-group">
                                                            <a href="view_product.php?id=<?php echo urlencode(encryptUserId($product['product_id'])); ?>"
                                                                class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i> View
                                                            </a>
                                                            <a href="edit_product.php?product_id=<?php echo urlencode(encryptUserId($product['product_id'])); ?>"
                                                                class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-pencil-alt"></i> Edit
                                                            </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
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

        <!-- // include footer -->
        <?php include('../../../include/footer.php'); ?>
    </div>
    <!-- ./wrapper -->


    <!-- DataTables -->
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "language": {
                    "search": "ค้นหา:",
                    "paginate": {
                        "first": "หน้าแรก",
                        "last": "หน้าสุดท้าย",
                        "next": "ถัดไป",
                        "previous": "ก่อนหน้า"
                    },
                    "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    "infoEmpty": "แสดง 0 ถึง 0 จาก 0 รายการ",
                    "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
                    "zeroRecords": "ไม่พบข้อมูลที่ตรงกัน"
                }
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>

    <!-- เพิ่มส่วนนี้ในส่วน <head> ของไฟล์ product.php -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- เพิ่ม JavaScript สำหรับ lightbox -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true
        });
    </script>


</body>

</html>