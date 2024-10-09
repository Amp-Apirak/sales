<?php
// สร้างหรือดึง CSRF Token สำหรับป้องกันการโจมตี CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ฟังก์ชันสำหรับสร้าง UUID แบบปลอดภัย
function generateUUID()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // เวอร์ชัน 4 UUID
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // UUID variant
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
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
        // ถ้าไม่พบชื่อสินค้าซ้ำ
        try {
            $sql = "INSERT INTO products (product_id, product_name, product_description, created_by) 
                    VALUES (:product_id, :product_name, :product_description, :created_by)";
            $stmt = $condb->prepare($sql);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
            $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
            $stmt->bindParam(':product_description', $product_description, PDO::PARAM_STR);
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
                        <div class="form-group">
                            <label for="product_name">Product Name<span class="text-danger">*</span></label>
                            <input type="text" name="product_name" class="form-control" id="product_name" placeholder="Product Name" required>
                        </div>

                        <div class="form-group">
                            <label for="product_description">Description</label>
                            <textarea class="form-control" name="product_description" id="product_description" rows="4" placeholder="Description"></textarea>
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