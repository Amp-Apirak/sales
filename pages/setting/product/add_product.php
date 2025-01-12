<?php
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

// ฟังก์ชันสำหรับบันทึกล็อกกิจกรรม
function logActivity($user_id, $action, $details)
{
    global $condb;
    $sql = "INSERT INTO activity_log (user_id, action, details) VALUES (:user_id, :action, :details)";
    $stmt = $condb->prepare($sql);
    $stmt->execute([':user_id' => $user_id, ':action' => $action, ':details' => $details]);
}


$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$created_by = $_SESSION['user_id']; // ดึง user_id ของผู้สร้างจาก session

// ตรวจสอบว่าผู้ใช้กดปุ่ม "เพิ่มสินค้า" หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบ CSRF Token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }
    // สร้าง UUID สำหรับ product_id
    $product_id = generateUUID();

    // รับข้อมูลจากฟอร์มและล้างข้อมูลด้วย htmlspecialchars เพื่อป้องกัน XSS
    $product_name = clean_input($_POST['product_name']);
    $product_description = clean_input($_POST['product_description']);
    $unit = clean_input($_POST['unit']);
    $cost_price = !empty($_POST['cost_price']) ? floatval($_POST['cost_price']) : NULL;
    $selling_price = !empty($_POST['selling_price']) ? floatval($_POST['selling_price']) : NULL;
    $supplier_id = !empty($_POST['supplier_id']) ? $_POST['supplier_id'] : NULL;

    // ตรวจสอบว่ามีชื่อสินค้าที่ซ้ำหรือไม่
    $checkproduct_sql = "SELECT * FROM products WHERE product_name = :product_name ";
    $stmt = $condb->prepare($checkproduct_sql);
    $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
    $stmt->execute();
    $existing_product = $stmt->fetch();

    if ($existing_product) {
        // ถ้าพบชื่อสินค้าซ้ำ
        echo "<script>
        setTimeout(function() {
            Swal.fire({
                title: 'เกิดข้อผิดพลาด',
                text: 'ชื่อสินค้านี้ถูกใช้ไปแล้ว!',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        }, 100);
        </script>";
    } else {

        // ตรวจสอบและอัพโหลดภาพสินค้า
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['main_image']['type'];
            $max_file_size = 5 * 1024 * 1024; // 5MB

            if (in_array($file_type, $allowed_types)) {
                $file_name = basename($_FILES['main_image']['name']);
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_file_name = $product_id . '.' . $file_ext;
                $upload_dir = '../../../uploads/product_images/';
                $upload_path = $upload_dir . $new_file_name;

                if (move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_path)) {
                    $main_image = $new_file_name;
                } else {
                    echo "<script>
                    setTimeout(function() {
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถอัพโหลดภาพสินค้าได้!',
                            icon: 'error',
                            confirmButtonText: 'ตกลง'
                        });
                    }, 100);
                    </script>";
                    $main_image = null;
                }
            } else {
                echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ประเภทไฟล์ภาพไม่ถูกต้องหรือขนาดเกิน 5MB!',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                }, 100);
                </script>";
                $main_image = null;
            }
        } else {
            $main_image = null;
        }

        try {
            $sql = "INSERT INTO products (
                product_id, 
                product_name, 
                product_description, 
                unit,
                cost_price,
                selling_price,
                supplier_id,
                main_image, 
                created_by
            ) VALUES (
                :product_id, 
                :product_name, 
                :product_description,
                :unit,
                :cost_price,
                :selling_price,
                :supplier_id,
                :main_image, 
                :created_by
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

            // แสดงข้อความเมื่อเพิ่มสินค้าสำเร็จด้วย SweetAlert
            echo "<script>
            setTimeout(function() {
                Swal.fire({
                    title: 'เพิ่มสินค้าสำเร็จ',
                    text: 'เพิ่มสินค้าสำเร็จแล้ว',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then(function() {
                    window.location.href = 'product.php';
                });
            }, 100);
            </script>";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

// เพิ่มการอัพโหลดเอกสาร
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
            if (!array_key_exists($file_ext, $allowed_doc_types) || !in_array($file_type, $allowed_doc_types)) {
                echo "<script>
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ประเภทไฟล์ {$file_name} ไม่ได้รับอนุญาต',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
                </script>";
                continue;
            }

            // ตรวจสอบขนาดไฟล์
            if ($file_size > $max_file_size) {
                echo "<script>
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไฟล์ {$file_name} มีขนาดเกิน 10MB',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
                </script>";
                continue;
            }

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

            try {
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // บันทึกข้อมูลลงฐานข้อมูล
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
                        ':created_by' => $created_by
                    ]);

                    // บันทึกล็อกกิจกรรม
                    logActivity(
                        $created_by,
                        'upload_document',
                        "อัพโหลดเอกสาร {$file_name} สำหรับสินค้า {$product_name}"
                    );
                } else {
                    throw new Exception("ไม่สามารถอัพโหลดไฟล์ {$file_name} ได้");
                }
            } catch (Exception $e) {
                echo "<script>
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด',
                    text: '{$e->getMessage()}',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
                </script>";
            }
        }
    }
}

?>


<div class="modal fade" id="addbtn">
    <div class="modal-dialog addbtn">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Product</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- ฟอร์มเพิ่มข้อมูลสินค้า -->
                <form id="addProductForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="card-body">
                        <!-- ต้องการใส่ข้อความ เป็นสีแดง หมายเหตุ -->
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                                ต้องการสร้าง Product จำเป็นจะต้องสร้าง Supplier ก่อน กรณียังไม่มีข้อมูล Supplier <a href="<?php echo BASE_URL; ?>pages/setting/suppliers/add_supplier.php">Add Supplier</a>
                        </div>
                        <div class="form-group">
                            <label for="main_image">เลือกภาพสินค้าหลัก</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="main_image" name="main_image" accept="image/*" onchange="previewImage(this);">
                                <label class="custom-file-label" for="main_image">Choose file</label>
                            </div>
                            <img id="preview" src="#" alt="ตัวอย่างรูปภาพ" style="display:none;">
                        </div>

                        <div class="form-group">
                            <label for="product_name">Product Name<span class="text-danger">*</span></label>
                            <input type="text" name="product_name" class="form-control" id="product_name" placeholder="Product Name" required>
                        </div>

                        <div class="form-group">
                            <label for="product_description">Description</label>
                            <textarea class="form-control" name="product_description" id="product_description" rows="4" placeholder="Description"></textarea>
                        </div>

                        <!-- เพิ่มฟิลด์ใหม่ในฟอร์ม หลังจาก product_description -->
                        <div class="form-group">
                            <label for="unit">หน่วยนับ<span class="text-danger">*</span></label>
                            <input type="text" name="unit" class="form-control" id="unit" placeholder="เช่น ชิ้น, อัน, ชุด" required>
                        </div>

                        <!-- เปลี่ยนจาก type="number" เป็น type="text" -->
                        <div class="form-group">
                            <label for="cost_price">ราคาต้นทุน</label>
                            <input type="text" name="cost_price" class="form-control" id="cost_price">
                        </div>

                        <div class="form-group">
                            <label for="selling_price">ราคาขาย</label>
                            <input type="text" name="selling_price" class="form-control" id="selling_price">
                        </div>

                        <div class="form-group ">
                            <label for="supplier_id">ผู้จำหน่าย(Supplier)<span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-control select2" id="supplier_id" required>
                                <option value="">เลือกผู้จำหน่าย</option>
                                <?php
                                // เพิ่มโค้ดดึงข้อมูลผู้จำหน่าย
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
                        <!-- เพิ่มหลังจากส่วน main_image -->
                        <div class="form-group">
                            <label for="documents">เอกสารประกอบ (Data Sheet, Specification, etc.)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="documents" name="documents[]" multiple
                                    accept=".pdf,.doc,.docx,.xls,.xlsx">
                                <label class="custom-file-label" for="documents">เลือกไฟล์เอกสาร</label>
                            </div>
                            <small class="form-text text-muted">สามารถเลือกได้หลายไฟล์ (PDF, Word, Excel)</small>
                            <div id="selected-files" class="mt-2"></div>
                        </div>
                    </div>



                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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

    // เริ่มต้น Custom File Input
    $(function() {
        bsCustomFileInput.init();
    });
</script>

<style>
    #preview {
        max-width: 100%;
        max-height: 300px;
        /* หรือขนาดที่คุณต้องการ */
        object-fit: contain;
        margin-top: 10px;
    }
</style>

<script>
    // เพิ่มฟังก์ชันคำนวณกำไรขั้นต้น
    function calculateProfit() {
        var costPrice = parseFloat($('#cost_price').val()) || 0;
        var sellingPrice = parseFloat($('#selling_price').val()) || 0;
        var profit = sellingPrice - costPrice;
        var profitPercentage = costPrice > 0 ? (profit / costPrice * 100) : 0;

        $('#profit_display').html(
            'กำไรขั้นต้น: ' + profit.toFixed(2) + ' บาท (' +
            profitPercentage.toFixed(2) + '%)'
        );
    }

    // เรียกใช้ฟังก์ชันเมื่อมีการเปลี่ยนแปลงราคา
    $('#cost_price, #selling_price').on('input', calculateProfit);
</script>

<!-- สำหรับอัพโหลดไฟล์ -->
<script>
    // เพิ่มในส่วน script
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
</script>

<!-- คอมม่า -->
<script>
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
    }

    // เพิ่ม Event Listener สำหรับช่องราคา
    $('#cost_price, #selling_price').on('input', function() {
        handleNumberInput(this);
        calculateProfit();
    });

    // เพิ่มการจัดการก่อนส่งฟอร์ม
    $('#addProductForm').on('submit', function(e) {
        const costPrice = unformatNumber($('#cost_price').val());
        const sellingPrice = unformatNumber($('#selling_price').val());

        // ตรวจสอบว่าเป็นตัวเลขที่ถูกต้อง
        if (isNaN(parseFloat(costPrice)) || isNaN(parseFloat(sellingPrice))) {
            e.preventDefault();
            Swal.fire({
                title: 'เกิดข้อผิดพลาด',
                text: 'กรุณากรอกราคาให้ถูกต้อง',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
            return false;
        }

        $('#cost_price').val(costPrice);
        $('#selling_price').val(sellingPrice);
    });
</script>