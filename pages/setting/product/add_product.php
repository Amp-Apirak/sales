<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// สร้างหรือดึง CSRF Token สำหรับป้องกันการโจมตี CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data)
{
    // ทำความสะอาดข้อมูลแต่ยังคงเก็บอักขระพิเศษไว้
    $data = trim($data);
    // ป้องกัน SQL Injection โดยใช้ PDO parameters แทน
    return $data;
}

// ฟังก์ชันสำหรับสร้าง UUID แบบปลอดภัย
function generateUUID()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // เวอร์ชัน 4 UUID
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // UUID variant
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


// ดึงข้อมูลจาก session
$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$created_by = $_SESSION['user_id']; // ดึง user_id ของผู้สร้างจาก session

// ตรวจสอบว่า User ได้ login แล้วหรือยัง และตรวจสอบ Role
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Executive' && $_SESSION['role'] != 'Sale Supervisor' && $_SESSION['role'] != 'Seller')) {
    // ถ้า Role ไม่ใช่ Executive หรือ Sale Supervisor หรือ Seller ให้ redirect ไปยังหน้าอื่น เช่น หน้า Dashboard
    header("Location: " . BASE_URL . "index.php"); // เปลี่ยนเส้นทางไปหน้า Dashboard
    exit(); // หยุดการทำงานของสคริปต์
}

// ตัวแปรเก็บข้อความแจ้งเตือนและสถานะการบันทึก
$success_message = "";
$error_message = "";

// ตรวจสอบว่าผู้ใช้กดปุ่ม "เพิ่มสินค้า" หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบ CSRF Token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Invalid CSRF token";
    } else {
        // สร้าง UUID สำหรับ product_id
        $product_id = generateUUID();

        // รับข้อมูลจากฟอร์มและล้างข้อมูลด้วย htmlspecialchars เพื่อป้องกัน XSS
        $product_name = clean_input($_POST['product_name']);
        $product_description = clean_input($_POST['product_description']);
        $unit = clean_input($_POST['unit']);
        $cost_price = !empty($_POST['cost_price']) ? floatval(str_replace(',', '', $_POST['cost_price'])) : NULL;
        $selling_price = !empty($_POST['selling_price']) ? floatval(str_replace(',', '', $_POST['selling_price'])) : NULL;
        $supplier_id = !empty($_POST['supplier_id']) ? $_POST['supplier_id'] : NULL;

        // ตรวจสอบว่ามีชื่อสินค้าที่ซ้ำหรือไม่
        $checkproduct_sql = "SELECT * FROM products WHERE product_name = :product_name ";
        $stmt = $condb->prepare($checkproduct_sql);
        $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
        $stmt->execute();
        $existing_product = $stmt->fetch();

        if ($existing_product) {
            // ถ้าพบชื่อสินค้าซ้ำ
            $error_message = "ชื่อสินค้านี้ถูกใช้ไปแล้ว!";
        } else {
            // ตัวแปรสำหรับเก็บชื่อไฟล์รูปภาพ
            $main_image = null;

            // ตรวจสอบและอัพโหลดภาพสินค้า
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $file_type = $_FILES['main_image']['type'];
                $max_file_size = 5 * 1024 * 1024; // 5MB

                if (in_array($file_type, $allowed_types) && $_FILES['main_image']['size'] <= $max_file_size) {
                    $file_name = basename($_FILES['main_image']['name']);
                    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                    $new_file_name = $product_id . '.' . $file_ext;
                    $upload_dir = '../../../uploads/product_images/';

                    // สร้างโฟลเดอร์ถ้ายังไม่มี
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $upload_path = $upload_dir . $new_file_name;

                    if (move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_path)) {
                        $main_image = $new_file_name;
                    } else {
                        $error_message = "ไม่สามารถอัพโหลดภาพสินค้าได้!";
                    }
                } else {
                    $error_message = "ประเภทไฟล์ภาพไม่ถูกต้องหรือขนาดเกิน 5MB!";
                }
            }

            // ถ้าไม่มีข้อผิดพลาดในการอัพโหลดรูปภาพ ดำเนินการบันทึกข้อมูล
            if (empty($error_message)) {
                try {
                    // เริ่ม transaction
                    $condb->beginTransaction();

                    // บันทึกข้อมูลสินค้า
                    $sql = "INSERT INTO products (
                        product_id, 
                        product_name, 
                        product_description, 
                        unit,
                        cost_price,
                        selling_price,
                        supplier_id,
                        main_image, 
                        created_by,
                        created_at
                    ) VALUES (
                        :product_id, 
                        :product_name, 
                        :product_description,
                        :unit,
                        :cost_price,
                        :selling_price,
                        :supplier_id,
                        :main_image, 
                        :created_by,
                        NOW()
                    )";

                    $stmt = $condb->prepare($sql);
                    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
                    $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
                    $stmt->bindParam(':product_description', $product_description, PDO::PARAM_STR);
                    $stmt->bindParam(':unit', $unit, PDO::PARAM_STR);
                    $stmt->bindParam(':cost_price', $cost_price, PDO::PARAM_STR);
                    $stmt->bindParam(':selling_price', $selling_price, PDO::PARAM_STR);
                    $stmt->bindParam(':supplier_id', $supplier_id, PDO::PARAM_STR);
                    $stmt->bindParam(':main_image', $main_image, PDO::PARAM_STR);
                    $stmt->bindParam(':created_by', $created_by, PDO::PARAM_STR);
                    $stmt->execute();

                    // อัพโหลดเอกสารประกอบ (ถ้ามี)
                    if (!empty($_FILES['documents']['name'][0])) {
                        $allowed_doc_types = [
                            'pdf' => 'application/pdf',
                            'doc' => 'application/msword',
                            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'xls' => 'application/vnd.ms-excel',
                            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ];
                        $max_file_size = 10 * 1024 * 1024; // 10MB
                        $upload_dir = '../../../uploads/product_documents/';

                        // สร้างโฟลเดอร์ถ้ายังไม่มี
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }

                        $document_files = $_FILES['documents'];
                        $file_count = count($document_files['name']);

                        for ($i = 0; $i < $file_count; $i++) {
                            if ($document_files['error'][$i] === UPLOAD_ERR_OK) {
                                $file_tmp = $document_files['tmp_name'][$i];
                                $file_name = basename($document_files['name'][$i]);
                                $file_size = $document_files['size'][$i];
                                $file_type = $document_files['type'][$i];
                                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                                // ตรวจสอบประเภทไฟล์
                                if (array_key_exists($file_ext, $allowed_doc_types)) {
                                    // ตรวจสอบขนาดไฟล์
                                    if ($file_size <= $max_file_size) {
                                        // สร้างชื่อไฟล์ใหม่
                                        $new_file_name = generateUUID() . '.' . $file_ext;
                                        $upload_path = $upload_dir . $new_file_name;

                                        // กำหนดประเภทเอกสารตามนามสกุลไฟล์
                                        $document_type = '';
                                        switch ($file_ext) {
                                            case 'pdf':
                                                $document_type = 'manual';
                                                break;
                                            case 'doc':
                                            case 'docx':
                                                $document_type = 'specification';
                                                break;
                                            case 'xls':
                                            case 'xlsx':
                                                $document_type = 'datasheet';
                                                break;
                                            default:
                                                $document_type = 'other';
                                        }

                                        if (move_uploaded_file($file_tmp, $upload_path)) {
                                            // บันทึกข้อมูลเอกสาร
                                            $doc_sql = "INSERT INTO product_documents (
                                                id,
                                                product_id, 
                                                document_type, 
                                                file_path,
                                                file_name, 
                                                file_size,
                                                created_by,
                                                created_at
                                            ) VALUES (
                                                :id,
                                                :product_id, 
                                                :document_type,
                                                :file_path,
                                                :file_name,
                                                :file_size,
                                                :created_by,
                                                NOW()
                                            )";

                                            $doc_id = generateUUID();
                                            $doc_stmt = $condb->prepare($doc_sql);
                                            $doc_stmt->execute([
                                                ':id' => $doc_id,
                                                ':product_id' => $product_id,
                                                ':document_type' => $document_type,
                                                ':file_path' => $upload_path,
                                                ':file_name' => $file_name,
                                                ':file_size' => $file_size,
                                                ':created_by' => $created_by
                                            ]);

                                        }
                                    } else {
                                        throw new Exception("ไฟล์ {$file_name} มีขนาดเกิน 10MB");
                                    }
                                } else {
                                    throw new Exception("ประเภทไฟล์ {$file_name} ไม่ได้รับอนุญาต");
                                }
                            }
                        }
                    }

                    // Commit transaction
                    $condb->commit();


                    // ข้อความแจ้งเตือนสำเร็จ
                    $success_message = "เพิ่มสินค้าสำเร็จแล้ว";

                    // สร้าง CSRF Token ใหม่
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    $csrf_token = $_SESSION['csrf_token'];
                } catch (Exception $e) {
                    // Rollback transaction ในกรณีที่เกิดข้อผิดพลาด
                    if ($condb->inTransaction()) {
                        $condb->rollBack();
                    }
                    $error_message = "เกิดข้อผิดพลาด: " . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "product"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | เพิ่มสินค้า</title>
    <?php include '../../../include/header.php' ?>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .preview-image {
            max-width: 300px;
            max-height: 300px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .selected-file {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            padding: 5px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .selected-file button {
            margin-left: auto;
        }

        .profit-display {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #28a745;
        }

        /* เพิ่มสไตล์สำหรับแจ้งเตือน */
        .alert-note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
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
                            <h1 class="m-0">เพิ่มสินค้าใหม่</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">หน้าหลัก</a></li>
                                <li class="breadcrumb-item"><a href="product.php">สินค้า</a></li>
                                <li class="breadcrumb-item active">เพิ่มสินค้า</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">ข้อมูลสินค้า</h3>
                                </div>
                                <!-- /.card-header -->

                                <!-- แสดงข้อความแจ้งเตือนหากมีการบันทึกสำเร็จหรือมีข้อผิดพลาด -->
                                <?php if (!empty($success_message)): ?>
                                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                        <strong><i class="fas fa-check-circle"></i> สำเร็จ!</strong> <?php echo $success_message; ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($error_message)): ?>
                                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                        <strong><i class="fas fa-exclamation-triangle"></i> ข้อผิดพลาด!</strong> <?php echo $error_message; ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <div class="card-body">
                                    <!-- ฟอร์มเพิ่มข้อมูลสินค้า -->
                                    <form id="addProductForm" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                        <!-- <div class="alert alert-note">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            ต้องการสร้าง Product จำเป็นจะต้องสร้าง Supplier ก่อน กรณียังไม่มีข้อมูล Supplier <a href="<?php echo BASE_URL; ?>pages/setting/suppliers/add_supplier.php">Add Supplier</a>
                                        </div> -->

                                        <div class="row">
                                            <div class="col-md-4">
                                                <!-- รูปภาพสินค้า -->
                                                <div class="form-group">
                                                    <label for="main_image">รูปภาพสินค้า</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="main_image" name="main_image" accept="image/*" onchange="previewImage(this);">
                                                        <label class="custom-file-label" for="main_image">เลือกไฟล์</label>
                                                    </div>
                                                    <img id="preview" src="#" alt="ตัวอย่างรูปภาพ" class="preview-image">
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <!-- ชื่อสินค้า -->
                                                <div class="form-group">
                                                    <label for="product_name">ชื่อสินค้า<span class="text-danger">*</span></label>
                                                    <input type="text" name="product_name" class="form-control" id="product_name" placeholder="ชื่อสินค้า" required>
                                                </div>

                                                <!-- รายละเอียดสินค้า -->
                                                <div class="form-group">
                                                    <label for="product_description">รายละเอียด</label>
                                                    <textarea class="form-control" name="product_description" id="product_description" rows="3" placeholder="รายละเอียดสินค้า"></textarea>
                                                </div>

                                                <!-- หน่วยนับ -->
                                                <div class="form-group">
                                                    <label for="unit">หน่วยนับ<span class="text-danger">*</span></label>
                                                    <input type="text" name="unit" class="form-control" id="unit" placeholder="เช่น ชิ้น, อัน, ชุด" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <!-- ราคาต้นทุน -->
                                                <div class="form-group">
                                                    <label for="cost_price">ราคาต้นทุน</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">฿</span>
                                                        </div>
                                                        <input type="text" name="cost_price" class="form-control" id="cost_price" placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <!-- ราคาขาย -->
                                                <div class="form-group">
                                                    <label for="selling_price">ราคาขาย</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">฿</span>
                                                        </div>
                                                        <input type="text" name="selling_price" class="form-control" id="selling_price" placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- แสดงกำไรขั้นต้น -->
                                        <div id="profit_display" class="profit-display" style="display: none;">
                                            กำไรขั้นต้น: 0.00 บาท (0.00%)
                                        </div>

                                        <!-- ผู้จำหน่าย (Supplier) -->
                                        <div class="form-group mt-3">
                                            <label for="supplier_id">ผู้จำหน่าย (Supplier)<span class="text-danger">*</span></label>
                                            <select name="supplier_id" class="form-control select2" id="supplier_id" required>
                                                <option value="">เลือกผู้จำหน่าย</option>
                                                <?php
                                                // ดึงข้อมูลผู้จำหน่าย
                                                $supplier_sql = "SELECT supplier_id, supplier_name, company FROM suppliers ORDER BY supplier_name";
                                                $supplier_stmt = $condb->prepare($supplier_sql);
                                                $supplier_stmt->execute();
                                                $suppliers = $supplier_stmt->fetchAll();
                                                foreach ($suppliers as $supplier) {
                                                    echo '<option value="' . $supplier['supplier_id'] . '">' .
                                                        htmlspecialchars($supplier['supplier_name']) .
                                                        ' (' . htmlspecialchars($supplier['company']) . ')</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- เอกสารประกอบ -->
                                        <div class="form-group">
                                            <label for="documents">เอกสารประกอบ (Data Sheet, Specification, etc.)</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="documents" name="documents[]" multiple
                                                    accept=".pdf,.doc,.docx,.xls,.xlsx">
                                                <label class="custom-file-label" for="documents">เลือกไฟล์เอกสาร</label>
                                            </div>
                                            <small class="form-text text-muted">สามารถเลือกได้หลายไฟล์ (PDF, Word, Excel) ขนาดไม่เกิน 10MB ต่อไฟล์</small>
                                            <div id="selected-files" class="mt-2"></div>
                                        </div>

                                        <!-- ปุ่มดำเนินการ -->
                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="product.php" class="btn btn-default">
                                                <i class="fas fa-arrow-left mr-2"></i>กลับ
                                            </a>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save mr-2"></i>บันทึก
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col-12 -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Footer -->
        <?php include '../../../include/footer.php'; ?>
    </div>
    <!-- ./wrapper -->

    <!-- JavaScript -->
    <script>
        $(function() {
            // เริ่มต้น Select2
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            // เริ่มต้น Custom File Input
            bsCustomFileInput.init();

            // แสดงข้อความสำเร็จหรือล้มเหลวด้วย SweetAlert2
            <?php if (!empty($success_message)): ?>
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: '<?php echo $success_message; ?>',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'product.php';
                    }
                });
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: '<?php echo $error_message; ?>',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            <?php endif; ?>
        });

        // ฟังก์ชันแสดงตัวอย่างรูปภาพ
        function previewImage(input) {
            var preview = document.getElementById('preview');
            var file = input.files[0];
            var reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.style.display = 'block';
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }

        // ฟังก์ชันสำหรับจัดรูปแบบตัวเลขให้มีคอมม่า
        function formatNumber(number) {
            // แยกส่วนทศนิยม
            let parts = number.toString().split('.');
            // ใส่คอมม่าทุก 3 หลักในส่วนจำนวนเต็ม
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            // รวมส่วนจำนวนเต็มและทศนิยมกลับเข้าด้วยกัน
            return parts.join('.');
        }

        // ฟังก์ชันสำหรับลบคอมม่าออกจากตัวเลข
        function unformatNumber(number) {
            return number.toString().replace(/,/g, '');
        }

        // ฟังก์ชันสำหรับจัดการการแสดงผลตัวเลขในช่องกรอก
        function handleNumberInput(element) {
            let value = unformatNumber(element.value);
            // ตรวจสอบว่าเป็นตัวเลขเท่านั้น
            if (!/^\d*\.?\d*$/.test(value)) {
                value = value.replace(/[^\d.]/g, '');
            }
            if (value !== '') {
                const numberValue = parseFloat(value);
                if (!isNaN(numberValue)) {
                    element.value = formatNumber(value);
                }
            } else {
                element.value = '';
            }
        }

        // เพิ่มฟังก์ชันคำนวณกำไรขั้นต้น
        function calculateProfit() {
            var costPrice = parseFloat(unformatNumber($('#cost_price').val())) || 0;
            var sellingPrice = parseFloat(unformatNumber($('#selling_price').val())) || 0;
            var profit = sellingPrice - costPrice;
            var profitPercentage = costPrice > 0 ? (profit / costPrice * 100) : 0;

            $('#profit_display').html(
                'กำไรขั้นต้น: ' + formatNumber(profit.toFixed(2)) + ' บาท (' +
                profitPercentage.toFixed(2) + '%)'
            );

            // แสดงกล่องข้อมูลกำไรเมื่อมีการกรอกราคา
            if (costPrice > 0 || sellingPrice > 0) {
                $('#profit_display').show();
            } else {
                $('#profit_display').hide();
            }
        }

        // เพิ่ม Event Listener สำหรับช่องราคา
        $('#cost_price, #selling_price').on('input', function() {
            handleNumberInput(this);
            calculateProfit();
        });

        // สำหรับอัพโหลดไฟล์
        document.getElementById('documents').addEventListener('change', function(e) {
            const fileList = Array.from(this.files);
            const fileContainer = document.getElementById('selected-files');
            fileContainer.innerHTML = '';

            fileList.forEach((file, index) => {
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // แปลงเป็น MB
                const fileDiv = document.createElement('div');
                fileDiv.className = 'selected-file';
                fileDiv.innerHTML = `
                    <i class="fas fa-file mr-2"></i>
                    ${file.name} (${fileSize} MB)
                    <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeFile(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                fileContainer.appendChild(fileDiv);
            });
        });

        function removeFile(index) {
            const input = document.getElementById('documents');
            const dt = new DataTransfer();
            const {
                files
            } = input;

            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    dt.items.add(files[i]);
                }
            }

            input.files = dt.files;
            // ทริกเกอร์อีเวนต์ change เพื่ออัพเดทรายการไฟล์ที่แสดง
            input.dispatchEvent(new Event('change'));
        }

        // เพิ่มการจัดการก่อนส่งฟอร์ม
        $('#addProductForm').on('submit', function(e) {
            // เพิ่มปุ่มให้แสดงสถานะรอดำเนินการ
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>กำลังบันทึก...').attr('disabled', true);

            // แปลงค่าราคาที่มีคอมม่าให้เป็นตัวเลขปกติก่อนส่งฟอร์ม
            const costPrice = unformatNumber($('#cost_price').val());
            const sellingPrice = unformatNumber($('#selling_price').val());

            $('#cost_price').val(costPrice);
            $('#selling_price').val(sellingPrice);
        });
    </script>
</body>

</html>