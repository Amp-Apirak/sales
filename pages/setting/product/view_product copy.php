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



// ดึงข้อมูลจาก session
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];

// ตรวจสอบว่า product_id ถูกส่งมาจาก URL หรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ไม่พบข้อมูลโครงการ";
    exit;
}

// รับ product_id จาก URL และทำการถอดรหัส
$product_id = decryptUserId($_GET['id']);


// ดึงข้อมูลสินค้าจากฐานข้อมูล
$sql = "SELECT * FROM products WHERE product_id = :product_id";
$stmt = $condb->prepare($sql);
$stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "ไม่พบสินค้าที่ต้องการแสดง";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "product"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Product Magement</title>
    <?php include '../../../include/header.php' ?>
    <!-- เพิ่ม CSS ของ DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
    <!-- เพิ่ม JavaScript ของ DataTables -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

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

    <!-- เพิ่ม CSS สำหรับ ตาราง Datasheet -->
    <style>
        .table-responsive {
            overflow-x: auto;
            min-height: 300px;
            width: 100%;
        }

        .table {
            margin-bottom: 0;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .table-responsive {
                min-height: auto;
            }
        }
    </style>

    <!--ปรับ CSS สำหรับตารางให้ขยายเต็มจอ  -->
    <style>
        /* ปรับตารางให้ขยายเต็มจอ */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            margin-bottom: 0;
        }

        /* ปรับความกว้างของคอลัมน์ให้เหมาะสม */
        .table th,
        .table td {
            white-space: nowrap;
        }

        /* ปรับแต่งแท็บให้แสดงผลเต็มจอ */
        .tab-content {
            width: 100%;
        }

        /* ปรับแต่งแท็บ "Document & Data Sheet" ให้ขยายเต็มจอ */
        #product-comments {
            width: 100%;
        }
    </style>

    <!-- ปรับฟอนต์และการแสดงผลส่วนราคาสินค้าและข้อมูลผู้จำหน่าย -->
    <style>
        /* ฟอนต์สำหรับหัวข้อ */
        .content-header h1,
        .card-body h3,
        .card-body h4 {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 600;
            color: #333;
        }

        /* ฟอนต์สำหรับเนื้อหา */
        .price-info,
        .supplier-info,
        .table {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 400;
        }

        /* ปรับแต่งส่วนแสดงราคา */
        .price-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .price-info .text-muted {
            font-size: 14px;
            color: #6c757d;
        }

        .price-info h5 {
            font-size: 16px;
            color: #000;
            margin: 0;
            display: inline-block;
        }

        /* ปรับแต่งส่วนข้อมูลผู้จำหน่าย */
        .supplier-info {
            line-height: 1.8;
        }

        .supplier-info .d-flex {
            margin-bottom: 8px;
        }

        .supplier-info i {
            width: 20px;
            color: #007bff;
        }

        .supplier-info .text-muted {
            font-size: 14px;
            width: 100px;
        }

        .supplier-info span:not(.text-muted) {
            font-size: 14px;
            color: #000;
        }

        /* ปรับแต่งการ์ด */
        .bg-light {
            background-color: #f8f9fa !important;
            border: 1px solid #dee2e6;
        }
    </style>


    <style>
        /* กำหนดฟอนต์ให้ทั้งหน้า */
        body {
            font-family: 'Noto Sans Thai', sans-serif;
        }

        /* กำหนดฟอนต์ให้ตาราง */
        .table {
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 14px;
            /* ปรับขนาดฟอนต์ให้เล็กลง */
        }

        /* ปรับขนาดฟอนต์ของหัวข้อตาราง */
        .table th {
            font-size: 14px;
            /* ปรับขนาดฟอนต์ให้เล็กลง */
            font-weight: 600;
            /* ตัวหนา */
        }

        /* ปรับขนาดฟอนต์ของข้อมูลในตาราง */
        .table td {
            font-size: 14px;
            /* ปรับขนาดฟอนต์ให้เล็กลง */
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
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </div>


            <!-- Main content -->
            <section class="content">

                <!-- Default box -->
                <div class="card card-solid">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <h3 class="d-inline-block d-sm-none">LOWA Men’s Renegade GTX Mid Hiking Boots Review</h3>
                                <div class="col-12">
                                    <img src="<?php echo !empty($product['main_image']) ? '../../../uploads/product_images/' . $product['main_image'] : '../../../assets/img/pit.png'; ?>" class="product-image" alt="Product Image">
                                </div>
                                <div class="col-12 product-image-thumbs">
                                    <div class="product-image-thumb active"><img src="../../../assets/img/pit.png" alt="Product Image"></div>
                                    <div class="product-image-thumb"><img src="../../../assets/img/pit.png" alt="Product Image"></div>
                                    <div class="product-image-thumb"><img src="../../../assets/img/pit.png" alt="Product Image"></div>
                                    <div class="product-image-thumb"><img src="../../../assets/img/pit.png" alt="Product Image"></div>
                                    <div class="product-image-thumb"><img src="../../../assets/img/pit.png" alt="Product Image"></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <h3 class="my-3"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                <p><?php echo htmlspecialchars($product['product_description']); ?></p>

                                <hr>

                                <!-- แสดงข้อมูลราคา -->
                                <div class="price-info-container mb-4">
                                    <h4 class="font-weight-bold mb-3">ราคาสินค้า</h4>
                                    <div class="bg-light p-4 rounded">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="price-info">
                                                    <span class="text-muted">ราคาต้นทุน</span>
                                                    <h5 class="ml-2">฿<?php echo number_format($product['cost_price'], 2); ?></h5>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="price-info">
                                                    <span class="text-muted">ราคาขาย</span>
                                                    <h5 class="ml-2">฿<?php echo number_format($product['selling_price'], 2); ?></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- แสดงข้อมูลผู้จำหน่าย -->
                                <div class="supplier-info-container mb-4">
                                    <h4 class="font-weight-bold mb-3">ข้อมูลผู้จำหน่าย</h4>
                                    <?php
                                    // ดึงข้อมูลผู้จำหน่าย
                                    $supplier_sql = "SELECT s.* FROM suppliers s 
                                INNER JOIN products p ON s.supplier_id = p.supplier_id 
                                WHERE p.product_id = :product_id";
                                    $supplier_stmt = $condb->prepare($supplier_sql);
                                    $supplier_stmt->bindParam(':product_id', $product_id);
                                    $supplier_stmt->execute();
                                    $supplier = $supplier_stmt->fetch();
                                    ?>
                                    <div class="bg-light p-4 rounded">
                                        <?php if ($supplier): ?>
                                            <div class="supplier-info">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-building"></i>
                                                    <span class="text-muted">บริษัท:</span>
                                                    <span class="flex-grow-1"><?php echo htmlspecialchars($supplier['company']); ?></span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user"></i>
                                                    <span class="text-muted">ชื่อผู้ติดต่อ:</span>
                                                    <span class="flex-grow-1"><?php echo htmlspecialchars($supplier['supplier_name']); ?></span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-phone"></i>
                                                    <span class="text-muted">เบอร์โทร:</span>
                                                    <span class="flex-grow-1"><?php echo htmlspecialchars($supplier['phone']); ?></span>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted">ไม่พบข้อมูลผู้จำหน่าย</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row mt-4">
                            <nav class="w-100">
                                <div class="nav nav-tabs" id="product-tab" role="tablist">
                                    <a class="nav-item nav-link active" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab" aria-controls="product-desc" aria-selected="true">Link | Presentation</a>
                                    <a class="nav-item nav-link" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab" aria-controls="product-comments" aria-selected="false">Document & Data Sheet</a>
                                </div>
                            </nav>
                            <div class="tab-content p-3" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab">
                                    (อยู่ระหว่างการพํฒนา !!!!!!) แสดงตารางแสดงข้อมูลลิงค์ ประกอบด้วย ลำดับ,ชื่อ Link ที่ทำการตั้ง (เมื่อกดให้ลิงค์ไปที่หน้าของ Link), วันที่สร้าง, ผู้สร้าง, ปุ่มลบ (อยู่ระหว่างการพํฒนา !!!!!!)
                                </div>
                                <div class="tab-pane fade" id="product-comments" role="tabpanel" aria-labelledby="product-comments-tab">
                                    <!-- ส่วนควบคุมการเพิ่มเอกสาร -->
                                    <a href="edit_product.php?product_id=<?php echo urlencode(encryptUserId($product['product_id'])); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-pencil-alt"></i> Edit
                                    </a>

                                    <!-- ตารางแสดงข้อมูลเอกสาร -->
                                    <div class="table-responsive mt-3">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ลำดับ</th>
                                                    <th>ประเภทเอกสาร</th>
                                                    <th>ชื่อไฟล์</th>
                                                    <th>ขนาดไฟล์</th>
                                                    <th>วันที่สร้าง</th>
                                                    <th>ผู้สร้าง</th>
                                                    <th>จัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // ดึงข้อมูลเอกสารที่เกี่ยวข้องกับสินค้า
                                                $doc_sql = "SELECT pd.*, u.first_name, u.last_name 
                                                FROM product_documents pd
                                                LEFT JOIN users u ON pd.created_by = u.user_id
                                                WHERE pd.product_id = :product_id
                                                ORDER BY pd.created_at DESC";
                                                $doc_stmt = $condb->prepare($doc_sql);
                                                $doc_stmt->bindParam(':product_id', $product_id);
                                                $doc_stmt->execute();
                                                $documents = $doc_stmt->fetchAll();

                                                $i = 1;
                                                foreach ($documents as $doc) :
                                                    // คำนวณขนาดไฟล์ให้อ่านง่าย
                                                    $size = $doc['file_size'];
                                                    $formatted_size = $size > 1048576 ?
                                                        round($size / 1048576, 2) . ' MB' : ($size > 1024 ? round($size / 1024, 2) . ' KB' : $size . ' bytes');
                                                ?>
                                                    <tr>
                                                        <td><?php echo $i++; ?></td>
                                                        <td><?php echo htmlspecialchars($doc['document_type']); ?></td>
                                                        <td>
                                                            <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank">
                                                                <?php echo htmlspecialchars($doc['file_name']); ?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo $formatted_size; ?></td>
                                                        <td><?php echo date('d/m/Y H:i', strtotime($doc['created_at'])); ?></td>
                                                        <td><?php echo htmlspecialchars($doc['first_name'] . ' ' . $doc['last_name']); ?></td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteDocument('<?php echo $doc['id']; ?>')">
                                                                    <i class="fas fa-trash"></i> ลบ
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

            </section>
            <!-- /.content -->
        </div>


        <!-- // include footer -->
        <?php include('../../../include/footer.php'); ?>
    </div>
    <!-- ./wrapper -->

    <script>
        // ฟังก์ชันสำหรับลบเอกสาร
        function deleteDocument(docId) {
            if (confirm('คุณต้องการลบเอกสารนี้ใช่หรือไม่?')) {
                $.ajax({
                    url: 'delete_document.php',
                    type: 'POST',
                    data: {
                        id: docId
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('เกิดข้อผิดพลาด: ' + response.message);
                        }
                    }
                });
            }
        }

        // จัดการการส่งฟอร์มเพิ่มเอกสาร
        $('#documentForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            formData.append('product_id', '<?php echo $product_id; ?>');

            $.ajax({
                url: 'upload_document.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + response.message);
                    }
                }
            });
        });
    </script>


    <!-- deleteDocument: -->
    <script>
        // ฟังก์ชันลบเอกสาร
        function deleteDocument(docId) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "คุณต้องการลบเอกสารนี้ใช่หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่ง request ไปลบเอกสาร
                    window.location.href = `delete_document.php?id=${docId}`;
                }
            });
        }
    </script>
</body>

</html>

<script>
    $(document).ready(function() {
        $('.product-image-thumb').on('click', function() {
            var $image_element = $(this).find('img')
            $('.product-image').prop('src', $image_element.attr('src'))
            $('.product-image-thumb.active').removeClass('active')
            $(this).addClass('active')
        })
    })
</script>

<!-- DataTable -->
<script>
    $(document).ready(function() {
        // เรียกใช้ DataTable บนตารางที่มี id หรือ class ที่กำหนด
        $('.table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Thai.json" // ตั้งค่าภาษาเป็นไทย
            },
            "paging": true, // เปิดใช้งานการแบ่งหน้า
            "lengthChange": true, // เปิดใช้งานการเปลี่ยนจำนวนแถวที่แสดง
            "searching": true, // เปิดใช้งานการค้นหา
            "ordering": true, // เปิดใช้งานการเรียงลำดับ
            "info": true, // เปิดใช้งานข้อมูลสรุป
            "autoWidth": false, // ปิดการปรับความกว้างอัตโนมัติ
            "responsive": true // เปิดใช้งาน responsive
        });
    });
</script>