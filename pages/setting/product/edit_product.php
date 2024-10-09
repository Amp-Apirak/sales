<?php
// เริ่มต้น session และเชื่อมต่อฐานข้อมูล
session_start();
include('../../../config/condb.php');

// เปิดการแสดงข้อผิดพลาดทั้งหมด
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
try {
    $condb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; // ถ้าต้องการตรวจสอบการเชื่อมต่อ ให้เอา comment ออก
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// ตรวจสอบการตั้งค่า Session
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

// สร้างหรือดึง CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ดึงข้อมูลจาก session
$user_id = $_SESSION['user_id'];
$updated_by = $user_id;

// ตรวจสอบว่ามีการส่ง product_id มาหรือไม่
if (isset($_GET['product_id'])) {
    $encrypted_product_id = urldecode($_GET['product_id']);
    $product_id = decryptUserId($encrypted_product_id);

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

// ตรวจสอบว่าผู้ใช้กดปุ่ม "แก้ไขข้อมูลสินค้า" หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // ตรวจสอบ CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    // รับข้อมูลจากฟอร์มและล้างข้อมูล
    $product_name = clean_input($_POST['product_name']);
    $product_description = clean_input($_POST['product_description']);

    // ตรวจสอบว่ามีชื่อสินค้าซ้ำหรือไม่
    $checkproduct_sql = "SELECT * FROM products WHERE product_name = :product_name AND product_id != :product_id";
    $stmt = $condb->prepare($checkproduct_sql);
    $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
    $stmt->execute();
    $existing_product = $stmt->fetch();

    if ($existing_product) {
        $error_message = "ชื่อสินค้านี้ถูกใช้ไปแล้ว!";
    } else if (empty($product_name) || empty($product_description)) {
        $error_message = "กรุณากรอกข้อมูลให้ครบถ้วน!";
    } else {
        try {
            // แก้ไขข้อมูลสินค้า
            $sql = "UPDATE products SET product_name = :product_name, product_description = :product_description, updated_by = :updated_by WHERE product_id = :product_id";
            $stmt = $condb->prepare($sql);
            $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
            $stmt->bindParam(':product_description', $product_description, PDO::PARAM_STR);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
            $stmt->bindParam(':updated_by', $updated_by, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                $rowsAffected = $stmt->rowCount();
                if ($rowsAffected > 0) {
                    $success_message = "แก้ไขข้อมูลสินค้าสำเร็จแล้ว!";
                } else {
                    $info_message = "ไม่มีการเปลี่ยนแปลงข้อมูล";
                }
            } else {
                $error_message = "ไม่สามารถอัพเดทข้อมูลได้!";
            }
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                                    <form action="" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                                        <div class="form-group">
                                            <label for="product_name">Product Name<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-box"></i></span>
                                                </div>
                                                <input type="text" name="product_name" class="form-control" id="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="product_description">Description</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                                </div>
                                                <textarea name="product_description" class="form-control" id="product_description" rows="4" placeholder="Description"><?php echo htmlspecialchars($product['product_description']); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" name="submit" class="btn btn-sm btn-success w-15" style="width: 120px;">Save</button>
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
        <?php
        if (isset($success_message)) {
            echo "Swal.fire({
                title: 'สำเร็จ!',
                text: '$success_message',
                icon: 'success',
                confirmButtonText: 'ตกลง'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'product.php';
                }
            });";
        } elseif (isset($error_message)) {
            echo "Swal.fire({
                title: 'เกิดข้อผิดพลาด',
                text: '$error_message',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });";
        } elseif (isset($info_message)) {
            echo "Swal.fire({
                title: 'แจ้งเตือน',
                text: '$info_message',
                icon: 'info',
                confirmButtonText: 'ตกลง'
            });";
        }
        ?>
    });
    </script>
</body>
</html>