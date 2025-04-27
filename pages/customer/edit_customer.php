<?php
// session_start and Config DB
include '../../include/Add_session.php';

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

$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$updated_by = $_SESSION['user_id']; // ดึง user_id ของผู้แก้ไขจาก session

// ตรวจสอบว่ามีการส่ง customer_id มาใน URL หรือไม่
if (isset($_GET['customer_id'])) {
    // ถอดรหัส customer_id ที่ได้รับจาก URL
    $encrypted_customer_id = urldecode($_GET['customer_id']);
    $customer_id = decryptUserId($encrypted_customer_id);

    // ตรวจสอบว่าถอดรหัสสำเร็จหรือไม่
    if ($customer_id !== false) {
        // ดึงข้อมูลลูกค้าจากฐานข้อมูลโดยใช้ customer_id
        $sql = "SELECT * FROM customers WHERE customer_id = :customer_id";
        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->execute();
        $customer = $stmt->fetch();

        // ตรวจสอบว่าพบข้อมูลลูกค้าหรือไม่
        if (!$customer) {
            echo "ไม่พบข้อมูลลูกค้าที่ต้องการแก้ไข";
            exit;
        }
    } else {
        echo "รหัสลูกค้าไม่ถูกต้อง";
        exit;
    }
} else {
    echo "ไม่มีการส่งรหัสลูกค้ามา";
    exit;
}

// ตรวจสอบว่าผู้ใช้กดปุ่ม "แก้ไขข้อมูลลูกค้า" หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบ CSRF Token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    // รับข้อมูลจากฟอร์มและล้างข้อมูลด้วย htmlspecialchars เพื่อป้องกัน XSS
    $customer_name = clean_input($_POST['customer_name']);
    $company = clean_input($_POST['company']);
    $address = clean_input($_POST['address']);
    $phone = clean_input($_POST['phone']);
    $email = clean_input($_POST['email']);
    $remark = clean_input($_POST['remark']);
    $office_phone = clean_input($_POST['office_phone']);
    $extension = clean_input($_POST['extension']);
    $position = clean_input($_POST['position']);

    // เพิ่มตัวแปรเพื่อตรวจสอบการเปลี่ยนแปลงรูปภาพ
    $image_changed = false;

    // ตรวจสอบว่ามีการอัปโหลดรูปภาพใหม่หรือไม่
    if (isset($_FILES['customers_image']) && $_FILES['customers_image']['error'] == 0) {
        // ตรวจสอบขนาดไฟล์ (จำกัดที่ 5MB)
        if ($_FILES['customers_image']['size'] > 5000000) {
            echo "ไฟล์มีขนาดใหญ่เกินไป กรุณาอัปโหลดไฟล์ขนาดไม่เกิน 5MB";
            exit;
        }

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['customers_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = '../../uploads/customer_images/' . $new_filename;
            if (!move_uploaded_file($_FILES['customers_image']['tmp_name'], $upload_path)) {
                // แสดงข้อผิดพลาดที่ชัดเจนมากขึ้น
                echo "ไม่สามารถอัปโหลดรูปภาพได้: " . error_get_last()['message'];
                exit;
            }
            $customers_image = $new_filename;
            $image_changed = true;

            // ลบรูปภาพเก่า (ถ้ามี)
            if (!empty($customer['customers_image']) && file_exists('../../uploads/customer_images/' . $customer['customers_image'])) {
                unlink('../../uploads/customer_images/' . $customer['customers_image']);
            }
        } else {
            echo "ไฟล์รูปภาพไม่ถูกต้อง กรุณาอัปโหลดไฟล์ jpg, jpeg, png หรือ gif";
            exit;
        }
    } else {
        $customers_image = $customer['customers_image'];
    }

    // ตรวจสอบว่ามีการเปลี่ยนแปลงข้อมูลหรือไม่
    $data_changed =
        $customer_name != $customer['customer_name'] ||
        $position != $customer['position'] ||
        $company != $customer['company'] ||
        $email != $customer['email'] ||
        $phone != $customer['phone'] ||
        $address != $customer['address'] ||
        $remark != $customer['remark'] ||
        $office_phone != $customer['office_phone'] ||
        $extension != $customer['extension'] ||
        $image_changed;

    if (!$data_changed) {
        // ถ้าไม่มีการเปลี่ยนแปลงข้อมูล แสดง SweetAlert
        echo '<script>
        setTimeout(function() {
            Swal.fire({
                title: "Oops..",
                text: "No data corrections found.",
                icon: "error"
            }).then(function() {
                window.location = "customer.php"; //หน้าที่ต้องการให้กระโดดไป
            });
        }, 1000);
        </script>';
    } else {
        // แก้ไขข้อมูลลูกค้าในฐานข้อมูล
        try {
            $sql = "UPDATE customers SET customer_name = :customer_name, position = :position, company = :company, address = :address, phone = :phone, email = :email, remark = :remark, updated_by = :updated_by, office_phone = :office_phone, extension = :extension, customers_image = :customers_image WHERE customer_id = :customer_id";
            $stmt = $condb->prepare($sql);
            $stmt->bindParam(':customer_name', $customer_name, PDO::PARAM_STR);
            $stmt->bindParam(':position', $position, PDO::PARAM_STR);
            $stmt->bindParam(':company', $company, PDO::PARAM_STR);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':remark', $remark, PDO::PARAM_STR);
            $stmt->bindParam(':updated_by', $updated_by, PDO::PARAM_INT);
            $stmt->bindParam(':office_phone', $office_phone, PDO::PARAM_STR);
            $stmt->bindParam(':extension', $extension, PDO::PARAM_STR);
            $stmt->bindParam(':customers_image', $customers_image, PDO::PARAM_STR);
            $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_STR);
            $stmt->execute();

            // แสดงข้อความเมื่อแก้ไขสำเร็จด้วย SweetAlert
            echo "<script>
                    setTimeout(function() {
                        Swal.fire({
                            title: 'สำเร็จ!',
                            text: 'แก้ไขข้อมูลลูกค้าเรียบร้อยแล้ว!',
                            icon: 'success',
                            confirmButtonText: 'ตกลง'
                        }).then(function() {
                            window.location.href = 'customer.php';
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
<?php $menu = "customer"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Edit Customer</title>
    <?php include '../../include/header.php'; ?>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <?php include  '../../include/navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Edit Customer</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Edit Customer</li>
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
                            <!-- ฟอร์มแก้ไขข้อมูลลูกค้า -->
                            <div class="card card-primary h-100">
                                <div class="card-header">
                                    <h3 class="card-title">Customer Information</h3>
                                </div>
                                <div class="card-body">
                                    <form action="#" method="POST" enctype="multipart/form-data">
                                        <!-- CSRF Token -->
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                        <!-- Customer Image -->
                                        <div class="form-group">
                                            <label for="customers_image">Customer Logo</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="customers_image" name="customers_image">
                                                    <label class="custom-file-label" for="customers_image">Choose file</label>
                                                </div>
                                            </div>
                                            <?php if (!empty($customer['customers_image'])): ?>
                                                <img src="../../uploads/customer_images/<?php echo htmlspecialchars($customer['customers_image']); ?>" alt="Current Customer Logo" class="mt-2" style="max-width: 200px;">
                                            <?php endif; ?>
                                        </div>

                                        <!-- Company -->
                                        <div class="form-group">
                                            <label for="company">Company<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                </div>
                                                <input type="text" name="company" class="form-control" id="company" value="<?php echo htmlspecialchars($customer['company']); ?>" required>
                                            </div>
                                        </div>

                                        <!-- Address -->
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                </div>
                                                <input type="text" name="address" class="form-control" id="address" value="<?php echo htmlspecialchars($customer['address']); ?>">
                                            </div>
                                        </div>


                                        <!-- Phone -->
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="office_phone">Office Phone</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa fa-phone"></i></span>
                                                    </div>
                                                    <input type="text" name="office_phone" class="form-control" id="office_phone" value="<?php echo htmlspecialchars($customer['office_phone']); ?>">
                                                </div>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="office_phone">Extension</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-phone-square"></i></span>
                                                    </div>
                                                    <input type="text" name="extension" class="form-control" id="extension" value="<?php echo htmlspecialchars($customer['extension']); ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Customer Name -->
                                        <div class="form-group">
                                            <label for="customer_name">Customer Name<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-address-book"></i></span>
                                                </div>
                                                <input type="text" name="customer_name" class="form-control" id="customer_name" value="<?php echo htmlspecialchars($customer['customer_name']); ?>" required>
                                            </div>
                                        </div>

                                        <!-- Position -->
                                        <div class="form-group">
                                            <label for="position">Position</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                </div>
                                                <input type="text" name="position" class="form-control" id="position" value="<?php echo htmlspecialchars($customer['position']); ?>" placeholder="Position">
                                            </div>
                                        </div>

                                        <!-- Phone -->
                                        <div class="form-group">
                                            <label for="phone">Phone</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                </div>
                                                <input type="text" name="phone" class="form-control" id="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>">
                                            </div>
                                        </div>


                                        <!-- Email -->
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                </div>
                                                <input type="email" name="email" class="form-control" id="email" value="<?php echo htmlspecialchars($customer['email']); ?>">
                                            </div>
                                        </div>

                                        <!-- Remark -->
                                        <div class="form-group">
                                            <label for="remark">Remark</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                                </div>
                                                <textarea name="remark" class="form-control" id="remark"><?php echo htmlspecialchars($customer['remark']); ?></textarea>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-sm btn-success w-25">Save</button>
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
        <?php include '../../include/footer.php'; ?>
    </div>
    <script>
        $(function() {
            // แสดงชื่อไฟล์ที่เลือกในช่อง input file
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
</body>

</html>