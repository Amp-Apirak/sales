<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'] ?? '';
$team_id = $_SESSION['team_id'] ?? 0;
$created_by = $_SESSION['user_id'] ?? 0;

// ตรวจสอบสิทธิ์การเข้าถึง
if (!in_array($role, ['Executive', 'Sale Supervisor', 'Seller'])) {
    header("Location: unauthorized.php");
    exit();
}

// ตรวจสอบว่า project_id ถูกส่งมาจาก URL หรือไม่
if (!isset($_GET['project_id']) || empty($_GET['project_id'])) {
    echo "ไม่พบข้อมูลโครงการ";
    exit;
}

// ตรวจสอบว่ามีการส่ง project_id มาหรือไม่
if (isset($_GET['project_id'])) {
    // ถอดรหัส project_id ที่ได้รับจาก URL
    $encrypted_project_id = urldecode($_GET['project_id']);
    $project_id = decryptUserId($encrypted_project_id);

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

    // ดึงข้อมูลโครงการที่ต้องการแก้ไขจากฐานข้อมูล
    try {
        $sql = "SELECT * FROM projects WHERE project_id = :project_id";
        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':project_id', $project_id, PDO::PARAM_STR);
        $stmt->execute();
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        // ตรวจสอบว่าโครงการมีอยู่จริงหรือไม่
        if (!$project) {
            echo "ไม่พบโครงการที่ต้องการแก้ไข";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }

    // ตรวจสอบว่าผู้ใช้กดปุ่ม "บันทึกการแก้ไข" หรือไม่
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ตรวจสอบ CSRF Token
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Invalid CSRF token");
        }

        // รับข้อมูลจากฟอร์มและทำความสะอาดข้อมูล input
        $project_name = clean_input($_POST['project_name']);
        $sales_date = clean_input($_POST['sales_date']);
        $date_start = clean_input($_POST['date_start']);
        $date_end = clean_input($_POST['date_end']);
        $status = clean_input($_POST['status']);
        $contract_no = clean_input($_POST['con_number']);
        $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
        $customer_id = filter_var($_POST['customer_id'], FILTER_VALIDATE_INT);
        $sale_vat = filter_var(str_replace(',', '', $_POST['sale_vat']), FILTER_VALIDATE_FLOAT);
        $sale_no_vat = filter_var(str_replace(',', '', $_POST['sale_no_vat']), FILTER_VALIDATE_FLOAT);
        $cost_vat = filter_var(str_replace(',', '', $_POST['cost_vat']), FILTER_VALIDATE_FLOAT);
        $cost_no_vat = filter_var(str_replace(',', '', $_POST['cost_no_vat']), FILTER_VALIDATE_FLOAT);
        $gross_profit = filter_var(str_replace(',', '', $_POST['gross_profit']), FILTER_VALIDATE_FLOAT);
        $potential = filter_var(str_replace('%', '', $_POST['potential']), FILTER_VALIDATE_FLOAT);
        $es_sale_no_vat = filter_var(str_replace(',', '', $_POST['es_sale_no_vat']), FILTER_VALIDATE_FLOAT);
        $es_cost_no_vat = filter_var(str_replace(',', '', $_POST['es_cost_no_vat']), FILTER_VALIDATE_FLOAT);
        $es_gp_no_vat = filter_var(str_replace(',', '', $_POST['es_gp_no_vat']), FILTER_VALIDATE_FLOAT);
        $remark = clean_input($_POST['remark']);
        $vat = filter_var($_POST['vat'], FILTER_VALIDATE_FLOAT);

        // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //     echo "<pre>";
        //     print_r($_POST);
        //     echo "</pre>";
        //     exit();
        // }


        // ตรวจสอบข้อมูลที่จำเป็น
        if (empty($project_name) || empty($status) || !$product_id || !is_numeric($product_id)) {
            $alert = "error|กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน ประกอบด้วย ชื่อโครงการ, สถานะ, ชื่อสินค้าที่ขาย";
        } else {
            try {
                // เริ่ม transaction
                $condb->beginTransaction();

                // เตรียม SQL สำหรับการอัปเดตข้อมูล
                $sql = "UPDATE projects SET 
                        project_name = :project_name, 
                        start_date = :start_date, 
                        end_date = :end_date, 
                        status = :status, 
                        contract_no = :contract_no, 
                        product_id = :product_id, 
                        customer_id = :customer_id, 
                        sale_vat = :sale_vat, 
                        sale_no_vat = :sale_no_vat, 
                        cost_vat = :cost_vat, 
                        cost_no_vat = :cost_no_vat, 
                        gross_profit = :gross_profit, 
                        potential = :potential, 
                        sales_date = :sales_date, 
                        es_sale_no_vat = :es_sale_no_vat, 
                        es_cost_no_vat = :es_cost_no_vat, 
                        es_gp_no_vat = :es_gp_no_vat, 
                        remark = :remark, 
                        vat = :vat, 
                        updated_by = :updated_by, 
                        updated_at = NOW(), 
                        seller = :seller 
                    WHERE project_id = :project_id";

                $stmt = $condb->prepare($sql);
                $stmt->execute([
                    ':project_id' => $project_id,
                    ':project_name' => $project_name,
                    ':start_date' => $date_start,
                    ':end_date' => $date_end,
                    ':status' => $status,
                    ':contract_no' => $contract_no,
                    ':product_id' => $product_id,
                    ':customer_id' => $customer_id ?: null, // ใส่ null หากไม่มี customer_id
                    ':sale_vat' => $sale_vat,
                    ':sale_no_vat' => $sale_no_vat,
                    ':cost_vat' => $cost_vat,
                    ':cost_no_vat' => $cost_no_vat,
                    ':gross_profit' => $gross_profit,
                    ':potential' => $potential,
                    ':sales_date' => $sales_date,
                    ':es_sale_no_vat' => $es_sale_no_vat,
                    ':es_cost_no_vat' => $es_cost_no_vat,
                    ':es_gp_no_vat' => $es_gp_no_vat,
                    ':remark' => $remark,
                    ':vat' => $vat,
                    ':updated_by' => $created_by,
                    ':seller' => $created_by
                ]);

                // Commit transaction
                $condb->commit();
                $alert = "success|บันทึกข้อมูลโครงการเรียบร้อยแล้ว";
            } catch (Exception $e) {
                // Rollback transaction ในกรณีที่เกิดข้อผิดพลาด
                $condb->rollBack();
                $alert = "error|" . $e->getMessage();
            }
        }
    }
}

// ดึงข้อมูลสำหรับ dropdowns
$stmt = $condb->query("SELECT product_id, product_name FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $condb->query("SELECT * FROM customers");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- ส่วนของ HTML form ที่มีอยู่แล้ว -->

<!-- เพิ่ม SweetAlert library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "project"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Update Project</title>
    <?php include  '../../include/header.php'; ?>

    <!-- /* ใช้ฟอนต์ Noto Sans Thai กับ label */ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <style>
        /* ใช้ฟอนต์ Noto Sans Thai กับ label */
        label {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 200;
            /* ปรับระดับน้ำหนักของฟอนต์ */
            font-size: 16px;
            color: #333;
        }

        .custom-label {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 600;
            font-size: 18px;
            color: #FF5733;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <!-- Main Sidebar Container -->
        <!-- Preloader -->
        <?php include  '../../include/navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Update Project</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Update Project v1</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- ใส่ SweetAlert CSS -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- เพิ่มข้อมูล -->
                            <div class="row">
                                <!-- /.col (left) -->
                                <div class="col-md-6">
                                    <!-- /.Pipeline descriptions ----------------------------------------------------------------------->
                                    <form action="#" method="POST" enctype="multipart/form-data">
                                        <!-- Include the CSRF token as a hidden input -->
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <!-- /.card -->
                                        <div class="card card-primary h-80 w-100">
                                            <div class="card-header ">
                                                <h3 class="card-title">Pipeline descriptions</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col col-6">
                                                        <div class="form-group">
                                                            <label>วันเปิดการขาย</label>
                                                            <input type="date" name="sales_date" value="<?php echo htmlspecialchars($project['sales_date']); ?>" class="form-control" id="exampleInputEmail1" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col col-6">
                                                        <div class="form-group">
                                                            <label>สถานะโครงการ<span class="text-danger">*</span></label>
                                                            <select class="form-control select2" name="status">
                                                                <option value="Waiting for approve" <?php echo ($project['status'] == 'Waiting for approve') ? 'selected' : ''; ?>>Waiting for approve</option>
                                                                <option value="On-Hold" <?php echo ($project['status'] == 'On-Hold') ? 'selected' : ''; ?>>On-Hold</option>
                                                                <option value="Quotation" <?php echo ($project['status'] == 'Quotation') ? 'selected' : ''; ?>>Quotation</option>
                                                                <option value="Negotiation" <?php echo ($project['status'] == 'Negotiation') ? 'selected' : ''; ?>>Negotiation</option>
                                                                <option value="Bidding" <?php echo ($project['status'] == 'Bidding') ? 'selected' : ''; ?>>Bidding</option>
                                                                <option value="Win" <?php echo ($project['status'] == 'Win') ? 'selected' : ''; ?>>Win</option>
                                                                <option value="Lost" <?php echo ($project['status'] == 'Lost') ? 'selected' : ''; ?>>Lost</option>
                                                                <option value="Cancelled" <?php echo ($project['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                                            </select>
                                                        </div>
                                                        <!-- /.form-group -->
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col col-6">
                                                        <div class="form-group">
                                                            <label>วันเริ่มโครงการ</label>
                                                            <input type="date" name="date_start" value="<?php echo htmlspecialchars($project['start_date']); ?>" class="form-control" id="exampleInputEmail1" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col col-6">
                                                        <div class="form-group">
                                                            <label>วันสิ้นสุดโครงการ</label>
                                                            <input type="date" name="date_end" value="<?php echo htmlspecialchars($project['end_date']); ?>" class="form-control" id="exampleInputEmail1" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col col-6">
                                                        <div class="form-group">
                                                            <label>เลขที่สัญญา</label>
                                                            <input type="text" name="con_number" value="<?php echo htmlspecialchars($project['contract_no']); ?>" class="form-control" id="exampleInputEmail1" placeholder="เลขที่สัญญา">
                                                        </div>
                                                    </div>
                                                    <div class="col col-6">
                                                        <?php
                                                        // ดึงข้อมูลจากตาราง Products โดยใช้ prepared statement
                                                        $query = "SELECT * FROM products";
                                                        $stmt = $condb->prepare($query);
                                                        $stmt->execute();
                                                        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                        ?>

                                                        <div class="form-group">
                                                            <label>สินค้าที่ขาย<span class="text-danger">*</span></label>
                                                            <select name="product_id" class="form-control select2">
                                                                <?php foreach ($products as $product) : ?>
                                                                    <option value="<?php echo htmlspecialchars($product['product_id']); ?>" <?php echo ($project['product_id'] == $product['product_id']) ? 'selected' : ''; ?>>
                                                                        <?php echo htmlspecialchars($product['product_name']); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <!-- /.form-group -->
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>ชื่อโครงการ<span class="text-danger">*</span></label>
                                                    <input type="text" name="project_name" value="<?php echo htmlspecialchars($project['project_name']); ?>" class="form-control" id="exampleInputEmail1" placeholder="ชื่อโครงการ">
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                            </div>
                                            <!-- /.card-body -->
                                        </div>

                                        <!-- /.Customer descriptions ----------------------------------------------------------------------->
                                        <!-- /.card -->
                                        <div class="card card-success h-45 w-100">
                                            <div class="card-header">
                                                <h3 class="card-title">Customer descriptions</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col col-12">
                                                        <?php
                                                        // ดึงข้อมูลลูกค้าทั้งหมดจากตาราง customers โดยใช้ prepared statement
                                                        $query = "SELECT * FROM customers";
                                                        $stmt = $condb->prepare($query);
                                                        $stmt->execute();
                                                        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC); // ดึงข้อมูลทั้งหมดมาเก็บในตัวแปร $customers
                                                        ?>

                                                        <div class="form-group">
                                                            <label>ข้อมูลลูกค้า</label>
                                                            <select name="customer_id" class="form-control select2">
                                                                <option value="">Select Customer</option>
                                                                <?php foreach ($customers as $customer): ?>
                                                                    <option value="<?php echo htmlspecialchars($customer['customer_id']); ?>"
                                                                        <?php echo ($customer['customer_id'] == $project['customer_id']) ? 'selected' : ''; ?>>
                                                                        <?php echo htmlspecialchars($customer['customer_name']); ?> , <?php echo htmlspecialchars($customer['company']); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <!-- /.form-group -->
                                                    </div>
                                                    <div class="col col-4">

                                                    </div>
                                                </div>


                                            </div>
                                            <div class="card-footer">
                                                <label class="custom-label"><small>***ไม่พบข้อมูลลูกค้าสามารถเพิ่มได้ที่ เมนู "Customer"*** </small></label>
                                            </div>
                                            <!-- /.card-body -->
                                        </div>
                                </div>
                                <!-- /.col (right) -->


                                <!-- /.Cost Project ----------------------------------------------------------------------->
                                <!-- /.col (left) -->
                                <div class="col-md-3">
                                    <!-- /.col (left) -->
                                    <div class="card card-warning h-100">
                                        <div class="card-header">
                                            <h3 class="card-title">Cost Project</h3>
                                        </div>
                                        <div class="card-body ">
                                            <div class="row">
                                                <div class="col col">
                                                    <div class="form-group">
                                                        <label>ตั้งการคำนวณ <span class="text-primary">Vat (%)</span></label>
                                                        <select class="form-control select2" name="vat" id="vat" style="width: 100%;">
                                                            <option value="7" <?php echo ($project['vat'] == 7) ? 'selected' : ''; ?>>7%</option>
                                                            <option value="0" <?php echo ($project['vat'] == 0) ? 'selected' : ''; ?>>0%</option>
                                                            <option value="3" <?php echo ($project['vat'] == 3) ? 'selected' : ''; ?>>3%</option>
                                                            <option value="5" <?php echo ($project['vat'] == 5) ? 'selected' : ''; ?>>5%</option>
                                                        </select>
                                                    </div>
                                                    <!-- /.form-group -->

                                                    <div class="form-group">
                                                        <label><span class="text-primary">ราคาขาย</span>/รวมภาษีมูลค่าเพิ่ม</label>
                                                        <input type="int" name="sale_vat" value="<?php echo number_format($project['sale_vat'], 2); ?>" class="form-control" value="" id="sale_vat" placeholder="">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>ราคาขาย/รวมไม่ภาษีมูลค่าเพิ่ม</label>
                                                        <input type="int" name="sale_no_vat" value="<?php echo number_format($project['sale_no_vat'], 2); ?>" id="sale_no_vat" class="form-control" placeholder="">
                                                    </div>

                                                    <div class="form-group">
                                                        <label><span class="text-primary">ราคาต้นทุน</span>/รวมภาษีมูลค่าเพิ่ม</label>
                                                        <input type="int" name="cost_vat" value="<?php echo number_format($project['cost_vat'], 2); ?>" id="cost_vat" class="form-control" placeholder="">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>ราคาต้นทุน/รวมไม่ภาษีมูลค่าเพิ่ม</label>
                                                        <input type="int" name="cost_no_vat" value="<?php echo number_format($project['cost_no_vat'], 2); ?>" class="form-control" value="" id="cost_no_vat" style="background-color:#F8F8FF" placeholder="">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>กำไรขั้นต้น/รวมไม่ภาษีมูลค่าเพิ่ม</label>
                                                        <input type="int" name="gross_profit" value="<?php echo number_format($project['gross_profit'], 2); ?>" class="form-control" value="" id="gross_profit" style="background-color:#F8F8FF" placeholder="">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>กำไรขั้นต้น/คิดเป็น %</label>
                                                        <input type="int" name="potential" value="<?php echo number_format($project['potential'], 2); ?>" class="form-control" value="" id="potential" style="background-color:#F8F8FF" placeholder="">
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">

                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.card -->

                                <!-- /.Cost Project ----------------------------------------------------------------------->

                                <div class="col-md-3">
                                    <!-- /.col (left) -->
                                    <div class="card card-warning h-100">
                                        <div class="card-header">
                                            <h3 class="card-title">Estimate Potential</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col col">
                                                    <!-- /.form-group -->
                                                    <div class="form-group">
                                                        <label><span class="text-primary">ยอดขาย</span>/ที่คาดการณ์ไม่รวมภาษีมูลค่าเพิ่ม</label>
                                                        <input type="text" name="es_sale_no_vat" value="<?php echo number_format($project['es_sale_no_vat'], 2); ?>" class="form-control" value="" id="es_sale_no_vat" style="background-color:#F8F8FF" placeholder="">
                                                    </div>

                                                    <div class="form-group">
                                                        <label><span class="text-primary">ต้นทุน</span>/ที่คาดการณ์ไม่รวมภาษีมูลค่าเพิ่ม</label>
                                                        <input type="text" name="es_cost_no_vat" value="<?php echo number_format($project['es_cost_no_vat'], 2); ?>" class="form-control" value="" id="es_cost_no_vat" style="background-color:#F8F8FF" placeholder="">
                                                    </div>

                                                    <div class="form-group">
                                                        <label><span class="text-primary">กำไรที่คาดการณ์</span>ไม่รวมภาษีมูลค่าเพิ่ม</label>
                                                        <input type="text" name="es_gp_no_vat" value="<?php echo number_format($project['es_gp_no_vat'], 2); ?>" class="form-control" value="" id="es_gp_no_vat" style="background-color:#F8F8FF" placeholder="">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- textarea -->
                                            <div class="form-group">
                                                <label>Remark</label>
                                                <textarea class="form-control" name="remark" id="remark" rows="4" placeholder=""><?php echo htmlspecialchars($project['remark']); ?></textarea>
                                            </div>



                                            <!-- Date range -->
                                            <div class="form-group ">
                                                <button type="submit" name="submit" value="submit" class="btn btn-success">Save</button>
                                            </div>
                                            <!-- /.form group -->
                                        </div>

                                        </form>
                                        <div class="card-footer">
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- // include footer -->
        <?php include  '../../include/footer.php'; ?>
    </div>
    <!-- ./wrapper -->
    <script>
        $(function() {
            $('.select2').select2();
        });

        <?php
        if (!empty($alert)) {
            list($status, $msg) = explode('|', $alert);
            echo "
            Swal.fire({
                icon: '$status',
                title: '" . ($status == 'success' ? 'สำเร็จ!' : 'เกิดข้อผิดพลาด!') . "',
                text: '$msg',
                confirmButtonText: 'ตกลง'
            }).then((result) => {
                if (result.isConfirmed && '$status' === 'success') {
                    window.location.href = 'project.php';
                }
            });
            ";
        }
        ?>
    </script>
</body>

</html>

<!-- // ฟังก์ชันในการเพิ่มคอมม่าในตัวเลข -->
<script>
    // ฟังก์ชันสำหรับการเพิ่มคอมม่าให้ตัวเลขเพื่อแสดงผลให้อ่านง่ายขึ้น
    function addCommas(nStr) {
        nStr += ''; // แปลงค่าตัวเลขเป็นสตริง
        var x = nStr.split('.'); // แยกส่วนจำนวนเต็มและทศนิยม
        var x1 = x[0]; // จำนวนเต็ม
        var x2 = x.length > 1 ? '.' + x[1] : ''; // ทศนิยม
        var rgx = /(\d+)(\d{3})/; // รูปแบบการตรวจจับตัวเลขที่ต้องเพิ่มคอมม่า
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2'); // เพิ่มคอมม่าทุก 3 หลัก
        }
        return x1 + x2; // ส่งกลับค่าพร้อมคอมม่า
    }

    // ฟังก์ชันสำหรับลบคอมม่าออกจากตัวเลขก่อนนำไปคำนวณ
    function removeCommas(nStr) {
        return nStr.replace(/,/g, ''); // ลบคอมม่าทั้งหมดออกจากตัวเลข
    }

    // เมื่อ DOM ถูกโหลดเสร็จสมบูรณ์
    document.addEventListener('DOMContentLoaded', function() {
        var priceInputs = document.querySelectorAll('input[type="int"]'); // เลือกอินพุตที่เป็นประเภทตัวเลข

        // เพิ่ม Event Listener ให้กับอินพุตที่เป็นประเภทตัวเลข
        priceInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                var cleanValue = removeCommas(this.value); // ลบคอมม่าก่อนคำนวณ
                if (!isNaN(cleanValue) && cleanValue.length > 0) {
                    this.value = addCommas(cleanValue); // เพิ่มคอมม่ากลับในค่าใหม่
                }
            });
        });

        // ลบคอมม่าก่อนส่งข้อมูลฟอร์ม
        document.querySelector('form').addEventListener('submit', function(event) {
            priceInputs.forEach(function(input) {
                input.value = removeCommas(input.value); // ลบคอมม่าก่อนส่งค่าไปเซิร์ฟเวอร์
            });
        });
    });
</script>

<!-- คำนวณ Cost Project -->
<script>
    $(document).ready(function() {
        // ฟังก์ชันจัดรูปแบบตัวเลขให้มีคอมม่า
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // ฟังก์ชันคำนวณราคาที่ไม่รวม VAT จากราคาที่รวม VAT
        function calculateNoVatPrice(priceWithVat, vat) {
            return priceWithVat / (1 + (vat / 100)); // คำนวณราคาที่ไม่รวม VAT
        }

        // ฟังก์ชันคำนวณราคาที่รวม VAT จากราคาที่ไม่รวม VAT
        function calculateWithVatPrice(priceNoVat, vat) {
            return priceNoVat * (1 + (vat / 100)); // คำนวณราคาที่รวม VAT
        }

        // ฟังก์ชันคำนวณกำไรขั้นต้นและเปอร์เซ็นต์กำไรขั้นต้น
        function calculateGrossProfit() {
            var saleNoVat = parseFloat($("#sale_no_vat").val().replace(/,/g, "")) || 0; // อ่านค่าราคาขาย/รวมไม่ภาษีมูลค่าเพิ่ม
            var costNoVat = parseFloat($("#cost_no_vat").val().replace(/,/g, "")) || 0; // อ่านค่าราคาต้นทุน/รวมไม่ภาษีมูลค่าเพิ่ม

            if (saleNoVat && costNoVat) {
                var grossProfit = saleNoVat - costNoVat; // คำนวณกำไรขั้นต้น
                $("#gross_profit").val(formatNumber(grossProfit.toFixed(2))); // แสดงผลกำไรขั้นต้น

                var grossProfitPercentage = (grossProfit / saleNoVat) * 100; // คำนวณเปอร์เซ็นต์กำไรขั้นต้น
                $("#potential").val(grossProfitPercentage.toFixed(2) + "%"); // แสดงผลเปอร์เซ็นต์กำไรขั้นต้น
            }
        }

        // ฟังก์ชันคำนวณ Estimate Potential (การประมาณการยอดขาย ต้นทุน และกำไร)
        // ปรับปรุงฟังก์ชัน recalculateEstimate
        function recalculateEstimate() {
            var saleNoVat = parseFloat($("#sale_no_vat").val().replace(/,/g, "")) || 0;
            var costNoVat = parseFloat($("#cost_no_vat").val().replace(/,/g, "")) || 0;
            var status = $("select[name='status']").val(); // แก้ไขการอ้างอิง status
            var estimateSaleNoVat = 0;
            var estimateCostNoVat = 0;

            // กำหนดเปอร์เซ็นต์ตามสถานะโครงการ
            var percentage = 0;
            switch (status) {
                case 'Lost':
                case 'Cancelled':
                    percentage = 0;
                    break;
                case 'Quotation':
                    percentage = 10;
                    break;
                case 'Negotiation':
                    percentage = 30;
                    break;
                case 'Bidding':
                    percentage = 50;
                    break;
                case 'Win':
                case 'Waiting for approve':
                case 'On-Hold':
                    percentage = 100;
                    break;
            }

            // คำนวณยอดขายและต้นทุนที่คาดการณ์ตามสถานะ
            estimateSaleNoVat = (saleNoVat * percentage) / 100;
            estimateCostNoVat = (costNoVat * percentage) / 100;

            // แสดงผลยอดขาย ต้นทุน และกำไรที่คาดการณ์
            $("#es_sale_no_vat").val(formatNumber(estimateSaleNoVat.toFixed(2)));
            $("#es_cost_no_vat").val(formatNumber(estimateCostNoVat.toFixed(2)));
            $("#es_gp_no_vat").val(formatNumber((estimateSaleNoVat - estimateCostNoVat).toFixed(2)));
        }

        // เพิ่ม event listeners และเรียกใช้ recalculateEstimate
        $("#sale_vat, #sale_no_vat, #cost_vat, #cost_no_vat, #vat").on("input change", function() {
            calculateGrossProfit();
            recalculateEstimate();
        });

        $("select[name='status']").on("change", function() {
            recalculateEstimate();
        });

        // เมื่อกรอกข้อมูลในช่อง ราคาขาย/รวมภาษีมูลค่าเพิ่ม
        $("#sale_vat").on("input", function() {
            var saleVat = parseFloat($(this).val().replace(/,/g, "")) || 0;
            var vat = parseFloat($("#vat").val()) || 0;

            var saleNoVat = calculateNoVatPrice(saleVat, vat); // คำนวณราคาที่ไม่รวมภาษีมูลค่าเพิ่ม
            $("#sale_no_vat").val(formatNumber(saleNoVat.toFixed(2))); // แสดงราคาขาย/รวมไม่ภาษีมูลค่าเพิ่ม

            calculateGrossProfit(); // คำนวณกำไรขั้นต้น
            recalculateEstimate(); // คำนวณ Estimate Potential
        });

        // เมื่อกรอกข้อมูลในช่อง ราคาขาย/รวมไม่ภาษีมูลค่าเพิ่ม
        $("#sale_no_vat").on("input", function() {
            var saleNoVat = parseFloat($(this).val().replace(/,/g, "")) || 0;
            var vat = parseFloat($("#vat").val()) || 0;

            if (saleNoVat && vat) {
                var saleVat = calculateWithVatPrice(saleNoVat, vat); // คำนวณราคาขาย/รวมภาษีมูลค่าเพิ่ม
                $("#sale_vat").val(formatNumber(saleVat.toFixed(2))); // แสดงราคาขาย/รวมภาษีมูลค่าเพิ่ม
            }

            calculateGrossProfit(); // คำนวณกำไรขั้นต้น
            recalculateEstimate(); // คำนวณ Estimate Potential
        });

        // แก้ไขฟังก์ชันสำหรับช่อง ราคาต้นทุน/รวมไม่ภาษีมูลค่าเพิ่ม
        $("#cost_no_vat").on("input", function() {
            var costNoVat = parseFloat($(this).val().replace(/,/g, "")) || 0;
            var vat = parseFloat($("#vat").val()) || 0;

            if (costNoVat && vat) {
                var costVat = calculateWithVatPrice(costNoVat, vat); // คำนวณราคาต้นทุน/รวมภาษีมูลค่าเพิ่ม
                $("#cost_vat").val(formatNumber(costVat.toFixed(2))); // แสดงราคาต้นทุน/รวมภาษีมูลค่าเพิ่ม
            }

            calculateGrossProfit(); // คำนวณกำไรขั้นต้น
            recalculateEstimate(); // คำนวณ Estimate Potential
        });

        // แก้ไขฟังก์ชันสำหรับช่อง ราคาต้นทุน/รวมภาษีมูลค่าเพิ่ม
        $("#cost_vat").on("input", function() {
            var costVat = parseFloat($(this).val().replace(/,/g, "")) || 0;
            var vat = parseFloat($("#vat").val()) || 0;

            var costNoVat = calculateNoVatPrice(costVat, vat); // คำนวณราคาต้นทุน/รวมไม่ภาษีมูลค่าเพิ่ม
            $("#cost_no_vat").val(formatNumber(costNoVat.toFixed(2))); // แสดงราคาต้นทุน/รวมไม่ภาษีมูลค่าเพิ่ม

            calculateGrossProfit(); // คำนวณกำไรขั้นต้น
            recalculateEstimate(); // คำนวณ Estimate Potential
        });

        // เพิ่มการทริกเกอร์การคำนวณเมื่อมีการเปลี่ยนค่า VAT
        $("#vat").on("change", function() {
            $("#sale_vat").trigger("input");
            $("#sale_no_vat").trigger("input");
            $("#cost_vat").trigger("input");
            $("#cost_no_vat").trigger("input");
        });

        // เมื่อมีการเปลี่ยนสถานะโครงการ
        $("#status").on("change", function() {
            recalculateEstimate(); // คำนวณ Estimate Potential ใหม่
        });

        // เรียกใช้ฟังก์ชันเมื่อโหลดหน้าเสร็จ
        calculateGrossProfit();
        recalculateEstimate();
    });
</script>