<?php
// เริ่มต้น session และเชื่อมต่อฐานข้อมูล
session_start();
include('../../../config/condb.php');

// ตรวจสอบว่า User ได้ login แล้วหรือยัง และตรวจสอบ Role
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Executive' && $_SESSION['role'] != 'Sale Supervisor' && $_SESSION['role'] != 'Seller')) {
    // ถ้า Role ไม่ใช่ Executive หรือ Sale Supervisor ให้ redirect ไปยังหน้าอื่น เช่น หน้า Dashboard
    header("Location: " . BASE_URL . "index.php"); // เปลี่ยนเส้นทางไปหน้า Dashboard
    exit(); // หยุดการทำงานของสคริปต์
}

// เปิดการแสดงข้อผิดพลาดทั้งหมด (สำหรับใช้ในช่วงการพัฒนา แต่ควรปิดใน production)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// ตรวจสอบการตั้งค่า Session
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่อนุญาต',
                    text: 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้',
                    confirmButtonText: 'ตกลง'
                }).then(function() {
                    window.location.href = 'login.php'; // กลับไปยังหน้า 
                });
            }, 100);
          </script>";
    exit;
}

// เพิ่มต่อจากฟังก์ชัน clean_input
function generateUUID()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // เวอร์ชัน 4 UUID
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // UUID variant
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// สร้างหรือดึง CSRF Token
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

// ดึงข้อมูลจาก session
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];
$updated_by = $user_id; // ตั้งค่าตัวแปร $updated_by จาก user_id ของผู้ใช้งานปัจจุบัน

// ตรวจสอบว่ามีการส่ง product_id มาหรือไม่
if (isset($_GET['product_id'])) {
    $encrypted_product_id = urldecode($_GET['product_id']);
    $product_id = decryptUserId($encrypted_product_id); // ตรวจสอบการถอดรหัสให้ถูกต้อง

    if ($product_id !== false) {
        // ดึงข้อมูลสินค้าจากฐานข้อมูล
        $sql = "SELECT * FROM products WHERE product_id = :product_id";
        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            die("ไม่พบข้อมูลสินค้า");
        }
    } else {
        die("รหัสสินค้าไม่ถูกต้อง");
    }
} else {
    die("ไม่มีการส่งรหัสสินค้ามา");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // ตรวจสอบ CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Invalid CSRF token";
    } else {
        // รับข้อมูลจากฟอร์มและทำความสะอาด
        $product_name = clean_input($_POST['product_name']);
        $product_description = clean_input($_POST['product_description']);
        $unit = clean_input($_POST['unit']);
        $cost_price = !empty($_POST['cost_price']) ? floatval(str_replace(',', '', $_POST['cost_price'])) : NULL;
        $selling_price = !empty($_POST['selling_price']) ? floatval(str_replace(',', '', $_POST['selling_price'])) : NULL;
        $supplier_id = !empty($_POST['supplier_id']) ? $_POST['supplier_id'] : NULL;

        // ตรวจสอบว่ามีชื่อสินค้าซ้ำหรือไม่
        $checkproduct_sql = "SELECT * FROM products WHERE product_name = :product_name AND product_id != :product_id";
        $stmt = $condb->prepare($checkproduct_sql);
        $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
        $stmt->execute();
        $existing_product = $stmt->fetch();


        if ($existing_product) {
            $error_message = "ชื่อสินค้านี้ถูกใช้ไปแล้ว!";
        } else if (empty($product_name)) {
            $error_message = "กรุณากรอกชื่อสินค้า!";
        } else {
            try {
                // เริ่ม transaction
                $condb->beginTransaction();

                // อัพเดทข้อมูลสินค้า
                $update_sql = "UPDATE products SET 
                    product_name = :product_name, 
                    product_description = :product_description,
                    unit = :unit,
                    cost_price = :cost_price,
                    selling_price = :selling_price,
                    supplier_id = :supplier_id,
                    updated_by = :updated_by,
                    updated_at = CURRENT_TIMESTAMP 
                    WHERE product_id = :product_id";

                $stmt = $condb->prepare($update_sql);
                $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
                $stmt->bindParam(':product_description', $product_description, PDO::PARAM_STR);
                $stmt->bindParam(':unit', $unit, PDO::PARAM_STR);
                $stmt->bindParam(':cost_price', $cost_price, PDO::PARAM_STR);
                $stmt->bindParam(':selling_price', $selling_price, PDO::PARAM_STR);
                $stmt->bindParam(':supplier_id', $supplier_id, PDO::PARAM_STR);
                $stmt->bindParam(':updated_by', $updated_by, PDO::PARAM_STR);
                $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
                $stmt->execute();

                // จัดการอัพโหลดรูปภาพ
                if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                    $file_type = $_FILES['product_image']['type'];
                    $max_file_size = 5 * 1024 * 1024; // 5MB

                    if (in_array($file_type, $allowed_types) && $_FILES['product_image']['size'] <= $max_file_size) {
                        $file_name = basename($_FILES['product_image']['name']);
                        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                        $new_file_name = $product_id . '.' . $file_ext;
                        $upload_dir = '../../../uploads/product_images/';
                        $upload_path = $upload_dir . $new_file_name;

                        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                            $update_image_sql = "UPDATE products SET main_image = :main_image WHERE product_id = :product_id";
                            $stmt = $condb->prepare($update_image_sql);
                            $stmt->bindParam(':main_image', $new_file_name, PDO::PARAM_STR);
                            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
                            $stmt->execute();
                        }
                    }
                }

                // จัดการอัพโหลดเอกสาร
                if (!empty($_FILES['documents'])) {
                    $allowed_doc_types = [
                        'pdf' => 'application/pdf',
                        'doc' => 'application/msword',
                        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'xls' => 'application/vnd.ms-excel',
                        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ];
                    $max_file_size = 10 * 1024 * 1024; // 10MB
                    $upload_dir = '../../../uploads/product_documents/';

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
                                if ($file_size <= $max_file_size) {
                                    $new_file_name = generateUUID() . '.' . $file_ext;
                                    $upload_path = $upload_dir . $new_file_name;

                                    if (move_uploaded_file($file_tmp, $upload_path)) {
                                        // กำหนดประเภทเอกสารตามนามสกุลไฟล์
                                        $document_type = 'specification'; // หรือกำหนดตามเงื่อนไขที่ต้องการ

                                        $doc_sql = "INSERT INTO product_documents (
                                            id,
                                            product_id,
                                            document_type,
                                            file_path,
                                            file_name,
                                            file_size,
                                            created_by
                                        ) VALUES (
                                            :id,
                                            :product_id,
                                            :document_type,
                                            :file_path,
                                            :file_name,
                                            :file_size,
                                            :created_by
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
                                            ':created_by' => $updated_by
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                // Commit transaction
                $condb->commit();

                $success_message = "แก้ไขข้อมูลสินค้าสำเร็จแล้ว!";

                // บันทึกล็อกกิจกรรม
                //logActivity($user_id, 'edit_product', "Edited product: $product_name (ID: $product_id)");
            } catch (Exception $e) {
                // Rollback transaction ในกรณีที่เกิดข้อผิดพลาด
                $condb->rollBack();
                $error_message = "เกิดข้อผิดพลาด: " . $e->getMessage();
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
    <title>SalePipeline | Edit Product</title>
    <?php include '../../../include/header.php'; ?>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include '../../../include/navbar.php'; ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Edit Product</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Edit Product</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-primary h-100" style="min-height: 700px;">
                                <div class="card-header">
                                    <h3 class="card-title">Product Information</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($error_message)): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?php echo $error_message; ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($success_message)): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <?php echo $success_message; ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                    <form action="" method="POST" enctype="multipart/form-data">
                                        <!-- ฟิลด์เดิม -->
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">

                                        <!-- รูปภาพหลัก -->
                                        <div class="form-group">
                                            <label for="product_image">ภาพสินค้าหลัก</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="product_image" name="product_image" accept="image/*" onchange="previewImage(this);">
                                                <label class="custom-file-label" for="product_image">Choose file</label>
                                            </div>
                                            <img id="preview" src="<?php echo !empty($product['main_image']) ? '../../../uploads/product_images/' . $product['main_image'] : '#'; ?>"
                                                alt="ตัวอย่างรูปภาพ" style="max-width: 200px; margin-top: 10px; <?php echo empty($product['main_image']) ? 'display:none;' : ''; ?>">
                                        </div>

                                        <!-- ชื่อสินค้า -->
                                        <div class="form-group">
                                            <label for="product_name">Product Name<span class="text-danger">*</span></label>
                                            <input type="text" name="product_name" class="form-control" id="product_name"
                                                value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                        </div>

                                        <!-- คำอธิบาย -->
                                        <div class="form-group">
                                            <label for="product_description">Description</label>
                                            <textarea class="form-control" name="product_description" id="product_description" rows="4"><?php echo htmlspecialchars($product['product_description']); ?></textarea>
                                        </div>

                                        <!-- หน่วยนับ -->
                                        <div class="form-group">
                                            <label for="unit">หน่วยนับ<span class="text-danger">*</span></label>
                                            <input type="text" name="unit" class="form-control" id="unit"
                                                value="<?php echo htmlspecialchars($product['unit']); ?>" required>
                                        </div>

                                        <!-- ราคาต้นทุน -->
                                        <div class="form-group">
                                            <label for="cost_price">ราคาต้นทุน</label>
                                            <input type="text" name="cost_price" class="form-control" id="cost_price"
                                                value="<?php echo number_format($product['cost_price'], 2); ?>">
                                        </div>

                                        <!-- ราคาขาย -->
                                        <div class="form-group">
                                            <label for="selling_price">ราคาขาย</label>
                                            <input type="text" name="selling_price" class="form-control" id="selling_price"
                                                value="<?php echo number_format($product['selling_price'], 2); ?>">
                                        </div>

                                        <!-- ผู้จำหน่าย -->
                                        <div class="form-group">
                                            <label for="supplier_id">ผู้จำหน่าย<span class="text-danger">*</span></label>
                                            <select name="supplier_id" class="form-control select2" id="supplier_id" required>
                                                <option value="">เลือกผู้จำหน่าย</option>
                                                <?php
                                                $supplier_sql = "SELECT supplier_id, supplier_name, company FROM suppliers ORDER BY supplier_name";
                                                $supplier_stmt = $condb->prepare($supplier_sql);
                                                $supplier_stmt->execute();
                                                $suppliers = $supplier_stmt->fetchAll();
                                                foreach ($suppliers as $supplier) {
                                                    $selected = ($supplier['supplier_id'] == $product['supplier_id']) ? 'selected' : '';
                                                    echo '<option value="' . $supplier['supplier_id'] . '" ' . $selected . '>' .
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
                                            <small class="form-text text-muted">สามารถเลือกได้หลายไฟล์ (PDF, Word, Excel)</small>
                                            <div id="selected-files" class="mt-2"></div>

                                            <!-- แสดงรายการเอกสารที่มีอยู่ -->
                                            <?php
                                            $doc_sql = "SELECT * FROM product_documents WHERE product_id = :product_id";
                                            $doc_stmt = $condb->prepare($doc_sql);
                                            $doc_stmt->bindParam(':product_id', $product_id);
                                            $doc_stmt->execute();
                                            $documents = $doc_stmt->fetchAll();

                                            if (!empty($documents)): ?>
                                                <div class="mt-3">
                                                    <h5>เอกสารที่มีอยู่:</h5>
                                                    <ul class="list-group">
                                                        <?php foreach ($documents as $doc): ?>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <?php echo htmlspecialchars($doc['file_name']); ?>
                                                                <div>
                                                                    <a href="<?php echo $doc['file_path']; ?>" class="btn btn-sm btn-info" target="_blank">
                                                                        <i class="fas fa-eye"></i> ดู
                                                                    </a>
                                                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteDocument('<?php echo $doc['id']; ?>')">
                                                                        <i class="fas fa-trash"></i> ลบ
                                                                    </button>
                                                                </div>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- ปุ่มบันทึก -->
                                        <div class="form-group">
                                            <button type="submit" name="submit" class="btn btn-success">บันทึก</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include '../../../include/footer.php'; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($success_message)): ?>
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
            <?php elseif (isset($error_message)): ?>
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: '<?php echo $error_message; ?>',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            <?php endif; ?>
        });
    </script>

    <!-- สำหรับแสดงตัวอย่างรูปภาพ -->
    <script>
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
    </script>

    <!-- File Input Select -->
    <script>
        $(function() {
            bsCustomFileInput.init();
        });
    </script>

    <!-- เพิ่ม Script สำหรับจัดการคอมม่าในราคา -->
    <script>
        // ฟังก์ชันสำหรับจัดรูปแบบตัวเลขให้มีคอมม่า
        function formatNumber(number) {
            let parts = number.toString().split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            return parts.join('.');
        }

        function unformatNumber(number) {
            return number.toString().replace(/,/g, '');
        }

        function handleNumberInput(element) {
            let value = unformatNumber(element.value);
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

        // เพิ่ม Event Listener สำหรับช่องราคา
        $('#cost_price, #selling_price').on('input', function() {
            handleNumberInput(this);
        });

        // จัดการเอกสาร
        document.getElementById('documents').addEventListener('change', function(e) {
            const fileList = Array.from(this.files);
            const fileContainer = document.getElementById('selected-files');
            fileContainer.innerHTML = '';

            fileList.forEach((file, index) => {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
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
            input.dispatchEvent(new Event('change'));
        }

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