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

// สร้างหรือดึง CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
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
        } else if (empty($product_name)) {
            $error_message = "กรุณากรอกชื่อสินค้า!";
        } else {
            try {
                // แก้ไขข้อมูลสินค้า
                $sql = "UPDATE products SET product_name = :product_name, product_description = :product_description, updated_by = :updated_by WHERE product_id = :product_id";
                $stmt = $condb->prepare($sql);
                $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
                $stmt->bindParam(':product_description', $product_description, PDO::PARAM_STR);
                $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
                $stmt->bindParam(':updated_by', $updated_by, PDO::PARAM_STR);
                $result = $stmt->execute();

                if ($result) {
                    $success_message = "แก้ไขข้อมูลสินค้าสำเร็จแล้ว!";
                } else {
                    $error_message = "ไม่สามารถอัพเดทข้อมูลได้!";
                }
            } catch (PDOException $e) {
                $error_message = "Error: " . $e->getMessage();
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
                                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($success_message)): ?>
                                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                                    <?php endif; ?>
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
</body>

</html>