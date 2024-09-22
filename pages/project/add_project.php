<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงหน้านี้
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'Executive' && $_SESSION['role'] != 'Sale Supervisor' && $_SESSION['role'] != 'Seller')) {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลสำหรับ dropdown
$stmt = $condb->prepare("SELECT customer_id, customer_name FROM customers");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $condb->prepare("SELECT team_id, team_name FROM teams");
$stmt->execute();
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// ตรวจสอบการ submit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับและทำความสะอาดข้อมูลจากฟอร์ม
    $project_name = clean_input($_POST['project_name']);
    $customer_id = filter_var($_POST['customer_id'], FILTER_VALIDATE_INT);
    $contract_no = clean_input($_POST['contract_no']);
    $product = clean_input($_POST['product']);
    $status = clean_input($_POST['status']);
    $start_date = clean_input($_POST['start_date']);
    $end_date = clean_input($_POST['end_date']);
    $sale_no_vat = filter_var($_POST['sale_no_vat'], FILTER_VALIDATE_FLOAT);
    $sale_vat = filter_var($_POST['sale_vat'], FILTER_VALIDATE_FLOAT);
    $cost_no_vat = filter_var($_POST['cost_no_vat'], FILTER_VALIDATE_FLOAT);
    $cost_vat = filter_var($_POST['cost_vat'], FILTER_VALIDATE_FLOAT);
    $team_id = filter_var($_POST['team_id'], FILTER_VALIDATE_INT);
    $remark = clean_input($_POST['remark']);

    // คำนวณค่าอื่นๆ
    $gross_profit = $sale_no_vat - $cost_no_vat;
    $potential = ($gross_profit / $sale_no_vat) * 100;

    // เตรียม SQL query
    $sql = "INSERT INTO projects (project_name, customer_id, contract_no, product, status, start_date, end_date, 
            sale_no_vat, sale_vat, cost_no_vat, cost_vat, gross_profit, potential, team_id, remark, created_by, created_at) 
            VALUES (:project_name, :customer_id, :contract_no, :product, :status, :start_date, :end_date, 
            :sale_no_vat, :sale_vat, :cost_no_vat, :cost_vat, :gross_profit, :potential, :team_id, :remark, :created_by, NOW())";

    // เตรียม statement
    $stmt = $condb->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':project_name', $project_name);
    $stmt->bindParam(':customer_id', $customer_id);
    $stmt->bindParam(':contract_no', $contract_no);
    $stmt->bindParam(':product', $product);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->bindParam(':sale_no_vat', $sale_no_vat);
    $stmt->bindParam(':sale_vat', $sale_vat);
    $stmt->bindParam(':cost_no_vat', $cost_no_vat);
    $stmt->bindParam(':cost_vat', $cost_vat);
    $stmt->bindParam(':gross_profit', $gross_profit);
    $stmt->bindParam(':potential', $potential);
    $stmt->bindParam(':team_id', $team_id);
    $stmt->bindParam(':remark', $remark);
    $stmt->bindParam(':created_by', $_SESSION['user_id']);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to project list page with success message
        header("Location: project.php?success=1");
        exit();
    } else {
        $error_message = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "project"; ?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | เพิ่มโครงการใหม่</title>
    <?php include '../../include/header.php'; ?>
</head>
<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">เพิ่มโครงการใหม่</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">หน้าหลัก</a></li>
                                <li class="breadcrumb-item"><a href="project.php">จัดการโครงการ</a></li>
                                <li class="breadcrumb-item active">เพิ่มโครงการใหม่</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <?php if (isset($error_message)) : ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">กรอกข้อมูลโครงการ</h3>
                        </div>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="project_name">ชื่อโครงการ</label>
                                    <input type="text" class="form-control" id="project_name" name="project_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="customer_id">ลูกค้า</label>
                                    <select class="form-control select2" id="customer_id" name="customer_id" required>
                                        <option value="">เลือกลูกค้า</option>
                                        <?php foreach ($customers as $customer) : ?>
                                            <option value="<?php echo $customer['customer_id']; ?>"><?php echo htmlspecialchars($customer['customer_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="contract_no">เลขที่สัญญา</label>
                                    <input type="text" class="form-control" id="contract_no" name="contract_no">
                                </div>
                                <div class="form-group">
                                    <label for="product">ผลิตภัณฑ์</label>
                                    <input type="text" class="form-control" id="product" name="product" required>
                                </div>
                                <div class="form-group">
                                    <label for="status">สถานะ</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="">เลือกสถานะ</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="start_date">วันที่เริ่มต้น</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="end_date">วันที่สิ้นสุด</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="sale_no_vat">ราคาขาย (ไม่รวม VAT)</label>
                                    <input type="number" step="0.01" class="form-control" id="sale_no_vat" name="sale_no_vat" required>
                                </div>
                                <div class="form-group">
                                    <label for="sale_vat">ราคาขาย (รวม VAT)</label>
                                    <input type="number" step="0.01" class="form-control" id="sale_vat" name="sale_vat" required>
                                </div>
                                <div class="form-group">
                                    <label for="cost_no_vat">ต้นทุน (ไม่รวม VAT)</label>
                                    <input type="number" step="0.01" class="form-control" id="cost_no_vat" name="cost_no_vat" required>
                                </div>
                                <div class="form-group">
                                    <label for="cost_vat">ต้นทุน (รวม VAT)</label>
                                    <input type="number" step="0.01" class="form-control" id="cost_vat" name="cost_vat" required>
                                </div>
                                <div class="form-group">
                                    <label for="team_id">ทีม</label>
                                    <select class="form-control select2" id="team_id" name="team_id" required>
                                        <option value="">เลือกทีม</option>
                                        <?php foreach ($teams as $team) : ?>
                                            <option value="<?php echo $team['team_id']; ?>"><?php echo htmlspecialchars($team['team_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="remark">หมายเหตุ</label>
                                    <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">บันทึก</button>
                                <a href="project.php" class="btn btn-default float-right">ยกเลิก</a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php include '../../include/footer.php'; ?>
    </div>
    <script>
    $(function() {
        $('.select2').select2();
    });
    </script>
</body>
</html>