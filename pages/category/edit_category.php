<?php
include '../../include/Add_session.php';

// ตรวจสอบว่าได้ส่ง category_id มาใน URL หรือไม่
if (!isset($_GET['category_id'])) {
    header("Location: category.php");
    exit();
}

$category_id = decryptUserId($_GET['category_id']);

// ดึงข้อมูล Category จากฐานข้อมูล
$stmt = $condb->prepare("SELECT * FROM Category WHERE id = :category_id");
$stmt->bindParam(':category_id', $category_id, PDO::PARAM_STR);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header("Location: category.php");
    exit();
}

// สร้าง CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success_message = '';
$error_message = '';

// ตรวจสอบการส่งฟอร์มแก้ไข
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบ CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF validation failed.');
    }

    // กรองข้อมูลจากผู้ใช้เพื่อลดความเสี่ยงจาก XSS
    $service_category = htmlspecialchars($_POST['service_category'], ENT_QUOTES, 'UTF-8');
    $category_name = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');
    $sub_category = htmlspecialchars($_POST['sub_category'], ENT_QUOTES, 'UTF-8');
    $problems = htmlspecialchars($_POST['problems'], ENT_QUOTES, 'UTF-8');
    $cases = htmlspecialchars($_POST['cases'], ENT_QUOTES, 'UTF-8');
    $resolve = htmlspecialchars($_POST['resolve'], ENT_QUOTES, 'UTF-8');

    try {
        $stmt = $condb->prepare("UPDATE Category SET service_category = :service_category, category = :category, sub_category = :sub_category, problems = :problems, cases = :cases, resolve = :resolve WHERE id = :category_id");
        $stmt->execute([
            ':service_category' => $service_category,
            ':category' => $category_name,
            ':sub_category' => $sub_category,
            ':problems' => $problems,
            ':cases' => $cases,
            ':resolve' => $resolve,
            ':category_id' => $category_id
        ]);

        // เก็บข้อความสำเร็จ
        $success_message = 'แก้ไขข้อมูลสำเร็จ';
    } catch (PDOException $e) {
        $error_message = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "category"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Edit Category</title>
    <?php include '../../include/header.php'; ?>
    <?php include 'style_category.php'; ?>
    <!-- รวม SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Edit Category</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="category.php">Categories</a></li>
                                <li class="breadcrumb-item active">Edit Category</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Edit Category Details</h3>
                                </div>
                                <div class="card-body">
                                    <?php if ($error_message): ?>
                                        <script>
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'เกิดข้อผิดพลาด!',
                                                text: '<?php echo $error_message; ?>',
                                                confirmButtonText: 'ตกลง'
                                            });
                                        </script>
                                    <?php endif; ?>

                                    <?php if ($success_message): ?>
                                        <script>
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'สำเร็จ!',
                                                text: '<?php echo $success_message; ?>',
                                                confirmButtonText: 'ตกลง'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = 'view_category.php?category_id=<?php echo urlencode($_GET['category_id']); ?>';
                                                }
                                            });
                                        </script>
                                    <?php endif; ?>

                                    <form action="" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <div class="form-group">
                                            <label for="service_category">Service Category</label>
                                            <input type="text" class="form-control" id="service_category" name="service_category" value="<?php echo htmlspecialchars($category['service_category']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="category">Category</label>
                                            <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($category['category']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="sub_category">Sub-category</label>
                                            <input type="text" class="form-control" id="sub_category" name="sub_category" value="<?php echo htmlspecialchars($category['sub_category']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="problems">Problems</label>
                                            <textarea class="form-control" id="problems" name="problems" rows="3"><?php echo htmlspecialchars($category['problems']); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="cases">Cases</label>
                                            <textarea class="form-control" id="cases" name="cases" rows="3"><?php echo htmlspecialchars($category['cases']); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="resolve">Resolution</label>
                                            <textarea class="form-control" id="resolve" name="resolve" rows="3"><?php echo htmlspecialchars($category['resolve']); ?></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update Category</button>
                                        <a href="view_category.php?category_id=<?php echo urlencode($_GET['category_id']); ?>" class="btn btn-secondary">Cancel</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../../include/footer.php'; ?>
    </div>

    <!-- SweetAlert การแจ้งเตือน -->
    <script>
        <?php if ($success_message): ?>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '<?php echo $success_message; ?>',
                confirmButtonText: 'ตกลง'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'view_category.php?category_id=<?php echo urlencode($_GET['category_id']); ?>';
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>