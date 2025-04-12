<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// ตรวจสอบว่ามีการส่ง id มาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // ถ้าไม่มี id ให้ redirect กลับไปหน้า supplier.php
    header("Location: supplier.php");
    exit();
}

// ถอดรหัส id
$supplier_id = decryptUserId($_GET['id']);

// ดึงข้อมูลซัพพลายเออร์จากฐานข้อมูล
try {
    $stmt = $condb->prepare("SELECT s.*, u.first_name, u.last_name 
                             FROM suppliers s
                             LEFT JOIN users u ON s.created_by = u.user_id
                             WHERE s.supplier_id = :id");
    $stmt->bindParam(':id', $supplier_id, PDO::PARAM_INT);
    $stmt->execute();
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$supplier) {
        // ถ้าไม่พบข้อมูลซัพพลายเออร์ ให้ redirect กลับไปหน้า supplier.php
        header("Location: supplier.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// กำหนด path ของรูปภาพ
$image_path = !empty($supplier['suppliers_image'])
    ? BASE_URL . 'uploads/supplier_images/' . $supplier['suppliers_image']
    : BASE_URL . 'assets/img/gallery-img3.jpg';

?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "supplier"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | View Supplier</title>
    <?php include '../../../include/header.php'; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <style>
        .supplier-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #3c8dbc;
            cursor: pointer;
        }

        /* ปุ่มแก้ไข */
        .btn-edit {
            transition: all 0.3s;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.9);
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <!-- ส่วน CSS สำหรับตาราง -->
    <style>
        th {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 700;
            font-size: 14px;
            color: #333;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include '../../../include/navbar.php'; ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">View Supplier</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="supplier.php">Suppliers</a></li>
                                <li class="breadcrumb-item active">View Supplier</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-primary card-outline h-100">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <img class="supplier-image" src="<?php echo $image_path; ?>" alt="Supplier Image" id="supplierImage">
                                    </div>
                                    <p></p>
                                    <p class="text-muted text-center"><?php echo htmlspecialchars($supplier['company']); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card card-primary card-outline h-100">
                                <div class="card-body p-0 box-profile">
                                    <div class="text-right p-3">
                                        <a href="edit_supplier.php?supplier_id=<?php echo urlencode($_GET['id']); ?>" class="btn btn-primary btn-sm btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><i class="fas fa-map-marker-alt text-primary mr-2"></i>Address</h6>
                                            </div>
                                            <p class="mb-1 text-muted"><?php echo nl2br(htmlspecialchars($supplier['address'])); ?></p>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><i class="fas fa-phone text-success mr-2"></i>Contact</h6>
                                            </div>
                                            <p class="mb-1"><small class="text-muted">Supplier Name:</small> <?php echo htmlspecialchars($supplier['supplier_name']); ?></p>
                                            <p class="mb-1"><small class="text-muted">Phone:</small> <?php echo htmlspecialchars($supplier['phone']); ?></p>
                                            <p class="mb-1"><small class="text-muted">Office Phone:</small> <?php echo htmlspecialchars($supplier['office_phone']); ?></p>
                                            <p class="mb-1"><small class="text-muted">Extension:</small> <?php echo htmlspecialchars($supplier['extension']); ?></p>
                                            <p class="mb-1"><small class="text-muted">Email:</small> <?php echo htmlspecialchars($supplier['email']); ?></p>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><i class="fas fa-info-circle text-info mr-2"></i>Additional Info</h6>
                                            </div>
                                            <p class="mb-1"><small class="text-muted">Created by:</small> <?php echo htmlspecialchars($supplier['first_name'] . ' ' . $supplier['last_name']); ?></p>
                                            <p class="mb-1"><small class="text-muted">Created on:</small> <?php echo date('F j, Y', strtotime($supplier['created_at'])); ?></p>
                                        </li>
                                        <?php if (!empty($supplier['remark'])): ?>
                                            <li class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><i class="fas fa-comment text-warning mr-2"></i>Remark</h6>
                                                </div>
                                                <p class="mb-1 text-muted"><?php echo nl2br(htmlspecialchars($supplier['remark'])); ?></p>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">รายการสินค้าของซัพพลายเออร์</h3>
                                    <div class="card-tools">
                                        <a href="add_product.php?supplier_id=<?php echo urlencode($_GET['id']); ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-plus"></i> เพิ่มสินค้า
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap text-center">ลำดับ</th>
                                                <th class="text-nowrap text-center">ชื่อสินค้า</th>
                                                <th class="text-nowrap text-center">หมวดหมู่</th>
                                                <th class="text-nowrap text-center">ราคา</th>
                                                <th class="text-nowrap text-center">จำนวน</th>
                                                <th class="text-nowrap text-center">การกระทำ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // ดึงรายการสินค้าของซัพพลายเออร์จากฐานข้อมูล
                                            $stmt = $condb->prepare("SELECT * FROM products WHERE supplier_id = :supplier_id");
                                            $stmt->bindParam(':supplier_id', $supplier_id, PDO::PARAM_INT);
                                            $stmt->execute();
                                            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            if (!empty($products)) {
                                                foreach ($products as $index => $product) {
                                            ?>
                                                    <tr>
                                                        <td class="text-nowrap text-center"><?php echo $index + 1; ?></td>
                                                        <td class="text-nowrap"><?php echo !empty($product['product_name']) ? htmlspecialchars($product['product_name']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                        <td class="text-nowrap text-center"><?php echo !empty($product['category']) ? htmlspecialchars($product['category']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                        <td class="text-nowrap text-center"><?php echo !empty($product['price']) ? htmlspecialchars($product['price']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                        <td class="text-nowrap text-center"><?php echo !empty($product['quantity']) ? htmlspecialchars($product['quantity']) : 'ไม่ระบุข้อมูล'; ?></td>
                                                        <td class="text-nowrap text-center">
                                                            <a href="view_product.php?product_id=<?php echo urlencode($product['product_id']); ?>" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="edit_product.php?product_id=<?php echo urlencode($product['product_id']); ?>" class="btn btn-info btn-sm">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">ไม่มีสินค้าที่เกี่ยวข้องกับซัพพลายเออร์นี้</td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>
        <?php include '../../../include/footer.php'; ?>
    </div>

    <!-- เพิ่ม JavaScript สำหรับ DataTables -->
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "order": [], // ปิดการเรียงลำดับอัตโนมัติ
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>

    <!-- The Modal -->
    <div id="imageModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="img01">
    </div>

    <script>
        // Get the modal
        var modal = document.getElementById("imageModal");

        // Get the image and insert it inside the modal
        var img = document.getElementById("supplierImage");
        var modalImg = document.getElementById("img01");
        img.onclick = function() {
            modal.style.display = "block";
            modalImg.src = this.src;
        }

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>