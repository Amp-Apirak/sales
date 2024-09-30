<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// ตรวจสอบการตั้งค่า Session เพื่อป้องกันกรณีที่ไม่ได้ล็อกอิน
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");  // Redirect ไปยังหน้า login.php
    exit; // หยุดการทำงานของสคริปต์ปัจจุบันหลังจาก redirect
}

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

// ดึงข้อมูลจาก session
$user_id = $_SESSION['user_id'];
$updated_by = $user_id; // ตั้งค่าตัวแปร $updated_by จาก user_id ของผู้ใช้งานปัจจุบัน

// ตรวจสอบว่ามีการส่ง product_id มาหรือไม่
if (isset($_GET['product_id'])) {
    $encrypted_product_id = urldecode($_GET['product_id']);
    $product_id = decryptUserId($encrypted_product_id);

    // ตรวจสอบว่าถอดรหัสสำเร็จหรือไม่
    if ($product_id !== false) {
        // ดึงข้อมูลสินค้าจากฐานข้อมูล
        $sql = "SELECT * FROM products WHERE product_id = :product_id";
        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
        $stmt->execute();
        $product = $stmt->fetch();

        // ตรวจสอบว่ามีข้อมูลสินค้าหรือไม่
        if (!$product) {
            echo "ไม่พบข้อมูลสินค้า";
            exit;
        }
    } else {
        echo "รหัสสินค้าไม่ถูกต้อง";
        exit;
    }
} else {
    echo "ไม่มีการส่งรหัสสินค้ามา";
    exit;
}

// ตรวจสอบว่าผู้ใช้กดปุ่ม "แก้ไขข้อมูลสินค้า" หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบ CSRF Token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    // รับข้อมูลจากฟอร์มและล้างข้อมูลด้วย htmlspecialchars เพื่อป้องกัน XSS
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
    } else if (
        $product_name == $product['product_name'] &&
        $product_description == $product['product_description']
    ) {
        // ถ้าไม่มีการเปลี่ยนแปลงข้อมูล แสดง SweetAlert
        echo  '<script>
            setTimeout(function() {
                Swal.fire({
                    title: "Opp..",
                    text: "ไม่มีการแก้ไขข้อมูล!",
                    icon: "error"
                }).then(function() {
                    window.location = "product.php"; //หน้าที่ต้องการให้กระโดดไป
                });
            }, 1000);
            </script>';
    } else {
        try {
            // แก้ไขข้อมูลสินค้า
            $sql = "UPDATE products SET product_name = :product_name, product_description = :product_description, updated_by = :updated_by WHERE product_id = :product_id";
            $stmt = $condb->prepare($sql);
            $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
            $stmt->bindParam(':product_description', $product_description, PDO::PARAM_STR);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
            $stmt->bindParam(':updated_by', $updated_by, PDO::PARAM_INT);
            $stmt->execute();

            // แสดงข้อความเมื่อแก้ไขสำเร็จด้วย SweetAlert
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: 'แก้ไขข้อมูลสินค้าสำเร็จแล้ว!',
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

<!DOCTYPE html>
<html lang="en">
<?php $menu = "product"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Edit Product</title>
    <?php include '../../../include/header.php'; ?>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <?php include '../../../include/navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
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
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- ฟอร์มแก้ไขข้อมูลสินค้า -->
                            <div class="card card-primary h-100" style="min-height: 700px;">
                                <div class="card-header">
                                    <h3 class="card-title">Product Information</h3>
                                </div>
                                <div class="card-body">
                                    <form action="#" method="POST">
                                        <!-- CSRF Token -->
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">

                                        <!-- Product Name -->
                                        <div class="form-group">
                                            <label for="product_name">Product Name<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-box"></i></span>
                                                </div>
                                                <input type="text" name="product_name" class="form-control" id="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        <div class="form-group">
                                            <label for="product_description">Description</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                                </div>
                                                <textarea name="product_description" class="form-control" id="product_description" rows="4" placeholder="Description"><?php echo htmlspecialchars($product['product_description']); ?></textarea>
                                            </div>
                                        </div>


                                        <!-- Submit Button -->
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-sm btn-success w-15" style="width: 120px;">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Footer -->
        <?php include '../../../include/footer.php'; ?>
    </div>
</body>

</html>