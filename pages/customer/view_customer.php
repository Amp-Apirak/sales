<?php
//session_start and Config DB
include '../../include/Add_session.php';

// ตรวจสอบว่ามีการส่ง id มาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // ถ้าไม่มี id ให้ redirect กลับไปหน้า customer.php
    header("Location: customer.php");
    exit();
}

// ถอดรหัส id
$customer_id = decryptUserId($_GET['id']);

// ตรวจสอบว่า $customer_id เป็นตัวเลขหรือไม่
if (!is_numeric($customer_id)) {
    // ถ้าไม่ใช่ตัวเลข ให้ redirect กลับไปหน้า customer.php
    header("Location: customer.php");
    exit();
}

// ดึงข้อมูลลูกค้าจากฐานข้อมูล
try {
    $stmt = $condb->prepare("SELECT c.*, u.first_name, u.last_name 
                             FROM customers c
                             LEFT JOIN users u ON c.created_by = u.user_id
                             WHERE c.customer_id = :id");
    $stmt->bindParam(':id', $customer_id, PDO::PARAM_INT);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        // ถ้าไม่พบข้อมูลลูกค้า ให้ redirect กลับไปหน้า customer.php
        header("Location: customer.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// HTML และส่วนที่เหลือของหน้า view_customer.php
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "customer"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | View Customer</title>
    <?php include '../../include/header.php'; ?>
    <style>
        .customer-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #3c8dbc;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">View Customer</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="customer.php">Customers</a></li>
                                <li class="breadcrumb-item active">View Customer</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-primary card-outline h-100">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <img class="customer-image" src="<?php echo BASE_URL; ?>assets/img/gallery-img3.jpg" alt="Customer Image">
                                    </div>
                                    <p></p>
                                    <p class="text-muted text-center"><?php echo htmlspecialchars($customer['company']); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card card-primary card-outline h-100">
                                <div class="card-body p-0 box-profile ">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><i class="fas fa-map-marker-alt text-primary mr-2"></i>Address</h6>
                                            </div>
                                            <p class="mb-1 text-muted"><?php echo nl2br(htmlspecialchars($customer['address'])); ?></p>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><i class="fas fa-phone text-success mr-2"></i>Contact</h6>
                                            </div>
                                            <p class="mb-1"><small class="text-muted">Customer Name:</small> <?php echo htmlspecialchars($customer['customer_name']); ?></p>
                                            <p class="mb-1"><small class="text-muted">Phone:</small> <?php echo htmlspecialchars($customer['phone']); ?></p>
                                            <p class="mb-1"><small class="text-muted">Email:</small> <?php echo htmlspecialchars($customer['email']); ?></p>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><i class="fas fa-info-circle text-info mr-2"></i>Additional Info</h6>
                                            </div>
                                            <p class="mb-1"><small class="text-muted">Created by:</small> <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></p>
                                            <p class="mb-1"><small class="text-muted">Created on:</small> <?php echo date('F j, Y', strtotime($customer['created_at'])); ?></p>
                                        </li>
                                        <?php if (!empty($customer['remark'])): ?>
                                            <li class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><i class="fas fa-comment text-warning mr-2"></i>Remark</h6>
                                                </div>
                                                <p class="mb-1 text-muted"><?php echo nl2br(htmlspecialchars($customer['remark'])); ?></p>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include '../../include/footer.php'; ?>
    </div>
</body>

</html>