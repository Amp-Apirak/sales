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
function clean_input($data)
{
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

</html><?php
        // เริ่ม session และเชื่อมต่อฐานข้อมูล
        include '../../include/session.php';

        // ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงหน้านี้
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'Executive' && $_SESSION['role'] != 'Sale Supervisor' && $_SESSION['role'] != 'Seller')) {
            header("Location: login.php");
            exit();
        }

        // ฟังก์ชันทำความสะอาดข้อมูล input
        function clean_input($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        // ดึงข้อมูลสำหรับ dropdown
        $customers = $condb->query("SELECT customer_id, customer_name FROM customers")->fetchAll(PDO::FETCH_ASSOC);
        $sellers = $condb->query("SELECT user_id, CONCAT(first_name, ' ', last_name) AS full_name FROM users WHERE role IN ('Seller', 'Sale Supervisor')")->fetchAll(PDO::FETCH_ASSOC);

        // ตรวจสอบการ submit form
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // รับและทำความสะอาดข้อมูลจากฟอร์ม
            $project_name = clean_input($_POST['project_name']);
            $sales_price = filter_var($_POST['sales_price'], FILTER_VALIDATE_FLOAT);
            $start_date = clean_input($_POST['start_date']);
            $end_date = clean_input($_POST['end_date']);
            $status = clean_input($_POST['status']);
            $contract_no = clean_input($_POST['contract_no']);
            $product = clean_input($_POST['product']);
            $remark = clean_input($_POST['remark']);
            $sales_date = clean_input($_POST['sales_date']);
            $seller = filter_var($_POST['seller'], FILTER_VALIDATE_INT);
            $sale_no_vat = filter_var($_POST['sale_no_vat'], FILTER_VALIDATE_FLOAT);
            $sale_vat = filter_var($_POST['sale_vat'], FILTER_VALIDATE_FLOAT);
            $cost_no_vat = filter_var($_POST['cost_no_vat'], FILTER_VALIDATE_FLOAT);
            $cost_vat = filter_var($_POST['cost_vat'], FILTER_VALIDATE_FLOAT);
            $customer_id = filter_var($_POST['customer_id'], FILTER_VALIDATE_INT);

            // คำนวณค่าอื่นๆ
            $gross_profit = $sale_no_vat - $cost_no_vat;
            $potential = ($gross_profit / $sale_no_vat) * 100;
            $es_sale_no_vat = $sale_no_vat; // ตั้งค่าเริ่มต้นเท่ากับ sale_no_vat
            $es_cost_no_vat = $cost_no_vat; // ตั้งค่าเริ่มต้นเท่ากับ cost_no_vat
            $es_gp_no_vat = $gross_profit; // ตั้งค่าเริ่มต้นเท่ากับ gross_profit

            // เตรียม SQL query
            $sql = "INSERT INTO projects (project_name, sales_price, start_date, end_date, status, contract_no, product, remark, 
            sales_date, seller, sale_no_vat, sale_vat, cost_no_vat, cost_vat, gross_profit, potential, 
            es_sale_no_vat, es_cost_no_vat, es_gp_no_vat, customer_id, created_by, created_at) 
            VALUES (:project_name, :sales_price, :start_date, :end_date, :status, :contract_no, :product, :remark, 
            :sales_date, :seller, :sale_no_vat, :sale_vat, :cost_no_vat, :cost_vat, :gross_profit, :potential, 
            :es_sale_no_vat, :es_cost_no_vat, :es_gp_no_vat, :customer_id, :created_by, NOW())";

            $stmt = $condb->prepare($sql);
            $stmt->bindParam(':project_name', $project_name);
            $stmt->bindParam(':sales_price', $sales_price);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':contract_no', $contract_no);
            $stmt->bindParam(':product', $product);
            $stmt->bindParam(':remark', $remark);
            $stmt->bindParam(':sales_date', $sales_date);
            $stmt->bindParam(':seller', $seller);
            $stmt->bindParam(':sale_no_vat', $sale_no_vat);
            $stmt->bindParam(':sale_vat', $sale_vat);
            $stmt->bindParam(':cost_no_vat', $cost_no_vat);
            $stmt->bindParam(':cost_vat', $cost_vat);
            $stmt->bindParam(':gross_profit', $gross_profit);
            $stmt->bindParam(':potential', $potential);
            $stmt->bindParam(':es_sale_no_vat', $es_sale_no_vat);
            $stmt->bindParam(':es_cost_no_vat', $es_cost_no_vat);
            $stmt->bindParam(':es_gp_no_vat', $es_gp_no_vat);
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->bindParam(':created_by', $_SESSION['user_id']);

            // Execute the statement
            if ($stmt->execute()) {
                // แสดง SweetAlert2 เมื่อบันทึกสำเร็จ
                echo "<script>
            Swal.fire({
                title: 'บันทึกสำเร็จ!',
                text: 'ข้อมูลโครงการถูกบันทึกเรียบร้อยแล้ว',
                icon: 'success',
                confirmButtonText: 'ตกลง'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'project.php';
                }
            });
        </script>";
            } else {
                // แสดง SweetAlert2 เมื่อเกิดข้อผิดพลาด
                echo "<script>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        </script>";
            }
        }
        ?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "project"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เพิ่มโครงการใหม่ | ระบบจัดการโครงการ</title>
    <?php include '../../include/header.php'; ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>
        <?php include '../../include/sidebar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>เพิ่มโครงการใหม่</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
                                <li class="breadcrumb-item"><a href="project.php">จัดการโครงการ</a></li>
                                <li class="breadcrumb-item active">เพิ่มโครงการใหม่</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
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
                                                <option value="Active">กำลังดำเนินการ</option>
                                                <option value="Inactive">ระงับชั่วคราว</option>
                                                <option value="Completed">เสร็จสิ้น</option>
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
                                            <label for="sales_date">วันที่ขาย</label>
                                            <input type="date" class="form-control" id="sales_date" name="sales_date" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="seller">ผู้ขาย</label>
                                            <select class="form-control select2" id="seller" name="seller" required>
                                                <option value="">เลือกผู้ขาย</option>
                                                <?php foreach ($sellers as $seller) : ?>
                                                    <option value="<?php echo $seller['user_id']; ?>"><?php echo htmlspecialchars($seller['full_name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sales_price">ราคาขาย</label>
                                            <input type="number" step="0.01" class="form-control" id="sales_price" name="sales_price" required>
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
                    </div>
                </div>
        </div>
        </section>
    </div>

    <?php include '../../include/footer.php'; ?>
    </div>

    <?php include '../../include/scripts.php'; ?>

    <script>
        $(function() {
            $('.select2').select2();

            // เพิ่ม event listener สำหรับการคำนวณ VAT และ GP อัตโนมัติ
            $('#sale_no_vat, #cost_no_vat').on('input', function() {
                calculateVatAndGp();
            });

            function calculateVatAndGp() {
                var saleNoVat = parseFloat($('#sale_no_vat').val()) || 0;
                var costNoVat = parseFloat($('#cost_no_vat').val()) || 0;
                var vatRate = 0.07; // 7% VAT

                var saleVat = saleNoVat * (1 + vatRate);
                var costVat = costNoVat * (1 + vatRate);
                var grossProfit = saleNoVat - costNoVat;
                var potential = (grossProfit / saleNoVat) * 100;

                $('#sale_vat').val(saleVat.toFixed(2));
                $('#cost_vat').val(costVat.toFixed(2));
                $('#sales_price').val(saleVat.toFixed(2)); // ตั้งค่า sales_price เท่ากับ sale_vat

                // แสดงผล GP และ Potential (อาจเพิ่มฟิลด์ในฟอร์มเพื่อแสดงค่าเหล่านี้)
                $('#gross_profit').val(grossProfit.toFixed(2));
                $('#potential').val(potential.toFixed(2));

                // ตั้งค่า es_sale_no_vat, es_cost_no_vat, es_gp_no_vat เท่ากับค่าปกติ
                $('#es_sale_no_vat').val(saleNoVat.toFixed(2));
                $('#es_cost_no_vat').val(costNoVat.toFixed(2));
                $('#es_gp_no_vat').val(grossProfit.toFixed(2));
            }

            // เพิ่ม validation สำหรับวันที่
            $('#start_date, #end_date, #sales_date').on('change', function() {
                validateDates();
            });

            function validateDates() {
                var startDate = new Date($('#start_date').val());
                var endDate = new Date($('#end_date').val());
                var salesDate = new Date($('#sales_date').val());

                if (endDate < startDate) {
                    alert('วันที่สิ้นสุดต้องมาหลังวันที่เริ่มต้น');
                    $('#end_date').val('');
                }

                if (salesDate > endDate || salesDate < startDate) {
                    alert('วันที่ขายต้องอยู่ระหว่างวันที่เริ่มต้นและวันที่สิ้นสุด');
                    $('#sales_date').val('');
                }
            }
        });
    </script>

</body>

</html>