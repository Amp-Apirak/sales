<?php
// เชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ตรวจสอบว่า User ได้ login แล้วหรือยัง และตรวจสอบ Role
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Engineer' && $_SESSION['role'] != 'Sale Supervisor' && $_SESSION['role'] != 'Seller' && $_SESSION['role'] != 'Executive')) {
    // ถ้า Role ไม่ใช่ Executive หรือ Sale Supervisor ให้ redirect ไปยังหน้าอื่น เช่น หน้า Dashboard
    header("Location: " . BASE_URL . "index.php"); // เปลี่ยนเส้นทางไปหน้า Dashboard
    exit(); // หยุดการทำงานของสคริปต์
}

$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$user_id = $_SESSION['user_id'];  // ดึง user_id ของผู้ใช้จาก session

// ฟังก์ชันสร้าง UUID ที่มีความปลอดภัยมากขึ้น
function generateUUID()
{
    return bin2hex(random_bytes(16));
}

// สร้าง CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success_message = '';
$error_message = '';

// ตรวจสอบการส่งฟอร์มเพิ่มหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบ CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF validation failed.');
    }

    // กรองข้อมูลจากผู้ใช้เพื่อลดความเสี่ยงของ XSS
    $service_category = htmlspecialchars($_POST['service_category'], ENT_QUOTES, 'UTF-8');
    $category_name = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');
    $sub_category = htmlspecialchars($_POST['sub_category'], ENT_QUOTES, 'UTF-8');
    $problems = htmlspecialchars($_POST['problems'], ENT_QUOTES, 'UTF-8');
    $cases = htmlspecialchars($_POST['cases'], ENT_QUOTES, 'UTF-8');
    $resolve = htmlspecialchars($_POST['resolve'], ENT_QUOTES, 'UTF-8');
    $created_by = $_SESSION['user_id']; // ใช้ user_id จาก session

    // สร้าง UUID
    $category_id = generateUUID();

    try {
        $stmt = $condb->prepare("INSERT INTO Category (id, service_category, category, sub_category, problems, cases, resolve, created_by, created_at) VALUES (:id, :service_category, :category, :sub_category, :problems, :cases, :resolve, :created_by, NOW())");
        $stmt->execute([
            ':id' => $category_id,
            ':service_category' => $service_category,
            ':category' => $category_name,
            ':sub_category' => $sub_category,
            ':problems' => $problems,
            ':cases' => $cases,
            ':resolve' => $resolve,
            ':created_by' => $created_by
        ]);

        $success_message = 'เพิ่มหมวดหมู่สำเร็จ';
    } catch (PDOException $e) {
        $error_message = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "category"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Add Category</title>
    <?php include '../../include/header.php'; ?>
    <!-- เรียกใช้ไฟล์ CSS สำหรับหน้านี้ -->
    <?php include 'style_category.php'; ?>

    <!-- /* ใช้ฟอนต์ Noto Sans Thai กับ label */ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <style>
        /* ใช้ฟอนต์ Noto Sans Thai กับ label */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 700;
            /* ปรับระดับน้ำหนักของฟอนต์ */
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
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>

        <!-- ส่วนเนื้อหาหลัก -->
        <div class="content-wrapper">
            <!-- ส่วนหัวของหน้า -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Add Category</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Add Category</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ส่วนเนื้อหา -->
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">Category Information</h3>
                        </div>
                        <div class="card-body">
                            <form id="addCategoryForm" action="" method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="form-group">
                                    <label for="service_category">Service Category*</label>
                                    <input type="text" class="form-control" id="service_category" name="service_category" required placeholder="Enter Service Category">
                                </div>
                                <div class="form-group">
                                    <label for="category">Category*</label>
                                    <input type="text" class="form-control" id="category" name="category" required placeholder="Enter Category">
                                </div>
                                <div class="form-group">
                                    <label for="sub_category">Sub-category*</label>
                                    <input type="text" class="form-control" id="sub_category" name="sub_category" required placeholder="Enter Sub-category">
                                </div>
                                <div class="form-group">
                                    <label for="problems">Problems</label>
                                    <textarea class="form-control" id="problems" name="problems" rows="3" placeholder="Describe problems"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="cases">Cases</label>
                                    <textarea class="form-control" id="cases" name="cases" rows="3" placeholder="Describe cases"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="resolve">Resolution</label>
                                    <textarea class="form-control" id="resolve" name="resolve" rows="3" placeholder="Describe resolution"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../../include/footer.php'; ?>
    </div>

    <!-- JavaScript สำหรับจัดการฟอร์มและการแจ้งเตือน -->
    <script>
        $(document).ready(function() {
            $('#addCategoryForm').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'ยืนยันการเพิ่มหมวดหมู่',
                    text: "คุณแน่ใจหรือไม่ที่จะเพิ่มหมวดหมู่นี้?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });

            <?php if ($success_message): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: '<?php echo $success_message; ?>',
                    confirmButtonText: 'ตกลง'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'category.php';
                    }
                });
            <?php endif; ?>

            <?php if ($error_message): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: '<?php echo $error_message; ?>',
                    confirmButtonText: 'ตกลง'
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>