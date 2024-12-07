<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'] ?? '';
$team_id = $_SESSION['team_id'] ?? 0;
$created_by = $_SESSION['user_id'] ?? 0;
$user_id = $_SESSION['user_id'] ?? 0;

error_log("Session role: " . $_SESSION['role']);
error_log("Session user_id: " . $_SESSION['user_id']);

// ตรวจสอบสิทธิ์การเข้าถึง
if (!in_array($role, ['Executive', 'Sale Supervisor', 'Seller'])) {
    header("Location: unauthorized.php");
    exit();
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

// ฟังก์ชันสำหรับสร้าง UUID แบบปลอดภัย
function generateUUID()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // เวอร์ชัน 4 UUID
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // UUID variant
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

$alert = ''; // ตัวแปรสำหรับแสดงข้อความแจ้งเตือน
$error_messages = [];

// ตรวจสอบว่ามีการส่งข้อมูลแบบ POST หรือไม่และเป็น AJAX หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // ตรวจสอบ CSRF Token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    // สร้าง UUID สำหรับ project_id
    $project_id = generateUUID();

    // ทำความสะอาดข้อมูลที่ได้จากฟอร์ม
    $project_name = clean_input($_POST['project_name']);
    $sales_date = clean_input($_POST['sales_date']);
    $date_start = clean_input($_POST['date_start']);
    $date_end = clean_input($_POST['date_end']);
    $status = clean_input($_POST['status']);
    $contract_no = clean_input($_POST['con_number']);
    $product_id = clean_input($_POST['product_id']);
    $customer_id = clean_input($_POST['customer_id']);
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

    // ถอดรหัส JSON ของ project_customers
    $project_customers = [];
    if (!empty($_POST['project_customers'])) {
        $project_customers = json_decode($_POST['project_customers'], true);
        if (!is_array($project_customers)) {
            $project_customers = [];
        }
    }

    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($project_name)) {
        $error_messages[] = "กรุณากรอกชื่อโครงการ";
    }
    if (empty($status) || $status === "Select") {
        $error_messages[] = "กรุณาเลือกสถานะโครงการ";
    }
    if (empty($product_id)) {
        $error_messages[] = "กรุณาเลือกสินค้าที่ขาย";
    }

    // ถ้าไม่มีข้อผิดพลาด ดำเนินการบันทึกข้อมูล
    if (empty($error_messages)) {
        try {
            // เริ่ม transaction
            $condb->beginTransaction();

            // ตรวจสอบว่ามีโครงการชื่อซ้ำหรือไม่
            $stmt = $condb->prepare("SELECT COUNT(*) FROM projects WHERE project_name = :project_name");
            $stmt->bindParam(':project_name', $project_name, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("มีโครงการชื่อนี้อยู่แล้ว");
            }

            // Insert ลงตาราง projects
            $sql = "INSERT INTO projects (project_id, project_name, start_date, end_date, status, contract_no, product_id, customer_id, 
                    sale_vat, sale_no_vat, cost_vat, cost_no_vat, gross_profit, potential, sales_date,
                    es_sale_no_vat, es_cost_no_vat, es_gp_no_vat, remark, vat, created_by, created_at, seller) 
                    VALUES (:project_id, :project_name, :start_date, :end_date, :status, :contract_no, :product_id, :customer_id, 
                    :sale_vat, :sale_no_vat, :cost_vat, :cost_no_vat, :gross_profit, :potential, :sales_date,
                    :es_sale_no_vat, :es_cost_no_vat, :es_gp_no_vat, :remark, :vat, :created_by, NOW(), :seller)";

            $stmt = $condb->prepare($sql);
            $stmt->execute([
                ':project_id' => $project_id,
                ':project_name' => $project_name,
                ':start_date' => $date_start,
                ':end_date' => $date_end,
                ':status' => $status,
                ':contract_no' => $contract_no,
                ':product_id' => $product_id,
                ':customer_id' => $customer_id ?: null,
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
                ':created_by' => $created_by,
                ':seller' => $created_by
            ]);

            // หากมีข้อมูลลูกค้าเพิ่มเติม ให้ Insert ลงตาราง project_customers
            if (!empty($project_customers)) {
                $sql_cust = "INSERT INTO project_customers (id, project_id, customer_id, is_primary, created_at) 
                             VALUES (:id, :project_id, :customer_id, :is_primary, NOW())";
                $stmt_cust = $condb->prepare($sql_cust);

                foreach ($project_customers as $cust) {
                    $pc_id = generateUUID();
                    $stmt_cust->execute([
                        ':id' => $pc_id,
                        ':project_id' => $project_id,
                        ':customer_id' => $cust['customer_id'],
                        ':is_primary' => $cust['is_primary']
                    ]);
                }
            }

            // Commit transaction
            $condb->commit();
            $alert = "success|บันทึกข้อมูลโครงการเรียบร้อยแล้ว";
        } catch (Exception $e) {
            // Rollback transaction หากเกิดข้อผิดพลาด
            $condb->rollBack();
            $alert = "error|" . $e->getMessage();
        }
    }

    // เตรียมข้อมูลสำหรับส่งกลับ
    $response = [
        'success' => empty($error_messages) && strpos($alert, 'success') !== false,
        'errors' => $error_messages,
        'message' => empty($error_messages) && strpos($alert, 'success') !== false ? 'บันทึกข้อมูลโครงการเรียบร้อยแล้ว' : ''
    ];

    // ส่งการตอบกลับเป็น JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// ดึงข้อมูลสำหรับ dropdowns
$stmt = $condb->query("SELECT product_id, product_name FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลลูกค้าให้สอดคล้องกับบทบาทผู้ใช้งาน
$customer_query = "SELECT DISTINCT c.* FROM customers c";
if ($role == 'Executive') {
    $customer_query .= " ORDER BY c.customer_name";
} elseif ($role == 'Sale Supervisor') {
    $customer_query .= " INNER JOIN users u ON c.created_by = u.user_id
                         WHERE u.team_id = :team_id
                         ORDER BY c.customer_name";
} else {
    $customer_query .= " WHERE c.created_by = :user_id
                         ORDER BY c.customer_name";
}

$stmt = $condb->prepare($customer_query);
if ($role == 'Sale Supervisor') {
    $stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
} elseif ($role == 'Seller' || $role == 'Engineer') {
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
}
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- ส่วน HTML และ JavaScript -->
<!DOCTYPE html>
<html lang="en">
<?php $menu = "project"; ?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Add Project</title>
    <?php include  '../../include/header.php'; ?>

    <!-- ใช้ฟอนต์ Noto Sans Thai -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <style>
        label, h1 {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 700;
            font-size: 16px;
            color: #333;
        }
        .custom-label {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 600;
            font-size: 18px;
            color: #FF5733;
        }

        @media (max-width: 768px) {
            .col-sm-3, .col-sm-6, .col-sm-12 {
                width: 100%;
                margin-bottom: 15px;
            }
            .card-body .row { margin: 0; }
            .form-group { margin-bottom: 1rem; }
            .select2-container { width: 100% !important; }
        }

        @media (max-width: 576px) {
            .card-body .row>div {
                padding-left: 5px;
                padding-right: 5px;
            }
            h1 { font-size: 24px; }
            .form-control { font-size: 14px; }
        }
    </style>
</head>
<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

    <?php include  '../../include/navbar.php'; ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1 class="m-0">Add Project</h1></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                            <li class="breadcrumb-item active">Add Project</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- ใส่ SweetAlert CSS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 mb-5">
                        <form id="addProjectForm" action="#" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                            <div class="col col-sm-12">
                                <div class="card card-primary ">
                                    <div class="card-header ">
                                        <h3 class="card-title">Project descriptions</h3>
                                    </div>
                                    <div class="card-body">
                                        <!-- ฟอร์มรายละเอียดโครงการ -->
                                        <div class="row">
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>วันเปิดการขาย</label>
                                                    <input type="date" name="sales_date" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>สถานะโครงการ<span class="text-danger">*</span></label>
                                                    <select class="form-control select2" name="status" id="status" style="width: 100%;">
                                                        <option selected="selected">Select</option>
                                                        <option>นำเสนอโครงการ (Presentations)</option>
                                                        <option>ใบเสนอราคา (Quotation)</option>
                                                        <option>ยื่นประมูล (Bidding)</option>
                                                        <option>ชนะ (Win)</option>
                                                        <option>แพ้ (Loss)</option>
                                                        <option>รอการพิจารณา (On Hold)</option>
                                                        <option>ยกเลิก (Cancled)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>สินค้าที่ขาย<span class="text-danger">*</span></label>
                                                    <select name="product_id" class="form-control select2">
                                                        <option value="">Select Product</option>
                                                        <?php foreach ($products as $product): ?>
                                                            <option value="<?php echo htmlspecialchars($product['product_id']); ?>">
                                                                <?php echo htmlspecialchars($product['product_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>เลขที่สัญญา</label>
                                                    <input type="text" name="con_number" class="form-control" placeholder="เลขที่สัญญา">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>ชื่อโครงการ<span class="text-danger">*</span></label>
                                                    <input type="text" name="project_name" class="form-control" placeholder="ชื่อโครงการ">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>วันเริ่มโครงการ</label>
                                                    <input type="date" name="date_start" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>วันสิ้นสุดโครงการ</label>
                                                    <input type="date" name="date_end" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 col-md-12">
                                                <div class="form-group">
                                                    <label>Remark</label>
                                                    <textarea class="form-control" name="remark" rows="4"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cost And Estimate Potential -->
                                    <div class="card-body">
                                        <h5><b><span class="text-primary">Cost Project</span></b></h5>
                                        <hr>
                                        <div class="row">
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>ตั้งการคำนวณ Vat (%)</label>
                                                    <select class="form-control select2" name="vat" id="vat" style="width: 100%;">
                                                        <option value="7">7%</option>
                                                        <option value="0">0%</option>
                                                        <option value="3">3%</option>
                                                        <option value="5">5%</option>
                                                        <option value="15">15%</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3"></div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>กำไรขั้นต้น/รวมไม่ภาษีมูลค่าเพิ่ม</label>
                                                    <input type="int" name="gross_profit" class="form-control" id="gross_profit" style="background-color:#F8F8FF">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>กำไรขั้นต้น/คิดเป็น %</label>
                                                    <input type="int" name="potential" class="form-control" id="potential" style="background-color:#F8F8FF">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>ราคาขาย/รวมภาษีมูลค่าเพิ่ม</label>
                                                    <input type="int" name="sale_vat" class="form-control" id="sale_vat">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>ราคาขาย/รวมไม่ภาษีมูลค่าเพิ่ม</label>
                                                    <input type="int" name="sale_no_vat" id="sale_no_vat" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>ราคาต้นทุน/รวมภาษีมูลค่าเพิ่ม</label>
                                                    <input type="int" name="cost_vat" id="cost_vat" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>ราคาต้นทุน/รวมไม่ภาษีมูลค่าเพิ่ม</label>
                                                    <input type="int" name="cost_no_vat" id="cost_no_vat" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <h5><b><span class="text-primary">Estimate Potential</span></b></h5>
                                        <hr>
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>ประมาณการยอดขาย (No Vat)</label>
                                                    <input type="text" name="es_sale_no_vat" class="form-control" id="es_sale_no_vat" style="background-color:#F8F8FF">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>ประมาณการต้นทุน (No Vat)</label>
                                                    <input type="text" name="es_cost_no_vat" class="form-control" id="es_cost_no_vat" style="background-color:#F8F8FF">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>กำไรที่คาดการณ์ (No Vat)</label>
                                                    <input type="text" name="es_gp_no_vat" class="form-control" id="es_gp_no_vat" style="background-color:#F8F8FF">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <h5><b><span class="text-primary">Customer Project</span></b></h5>
                                        <hr>
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>ข้อมูลลูกค้า (บทบาทดูแลควบคุมโครงการทุกภาคส่วน)</label>
                                                    <select name="customer_id" class="form-control select2">
                                                        <option value="">เลือกลูกค้า</option>
                                                        <?php foreach ($customers as $customer): ?>
                                                            <option value="<?php echo htmlspecialchars($customer['customer_id']); ?>">
                                                                <?php echo htmlspecialchars($customer['customer_name'] . ' - ' . $customer['company']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <div id="customer-list">
                                            <div class="selected-customers"></div>
                                            <button type="button" class="btn btn-sm btn-primary mt-3" id="add-customer-btn">
                                                <i class="fas fa-plus"></i> เลือกรายชื่อลูกค้า
                                            </button>

                                            <template id="customer-row-template">
                                                <div class="customer-row row mt-3">
                                                    <div class="col-md-6">
                                                        <select class="form-control select2 customer-select" name="project_customers[]">
                                                            <option value="">เลือกลูกค้า</option>
                                                            <?php foreach ($customers as $customer): ?>
                                                                <option value="<?php echo htmlspecialchars($customer['customer_id']); ?>">
                                                                    <?php echo htmlspecialchars($customer['customer_name'] . ' - ' . $customer['company']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input primary-customer" name="is_primary[]">
                                                            <label class="form-check-label">ลูกค้าหลัก</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger remove-customer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="container mx-auto">
                                        <div class="row">
                                            <div class="col col-sm-12">
                                                <div class="form-group text-center">
                                                    <button type="submit" name="submit" value="submit" class="btn btn-success">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
        </section>
    </div>
    <?php include  '../../include/footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function() {
        $('.select2').select2();
    });
</script>

<script>
    $(document).ready(function() {
        // จัดการการเพิ่มลูกค้า
        $('#add-customer-btn').click(function() {
            const mainCustomer = $('select[name="customer_id"]').val();
            if (!mainCustomer) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาเลือกลูกค้าหลักก่อน',
                    text: 'ต้องเลือกลูกค้าในช่อง "ข้อมูลลูกค้า (บทบาทดูแลควบคุมโครงการทุกภาคส่วน)" ก่อน',
                    confirmButtonText: 'ตกลง'
                });
                return;
            }

            const currentCustomers = $('.customer-row').length;
            const maxCustomers = 8;

            if (currentCustomers >= maxCustomers) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไม่สามารถเพิ่มลูกค้าได้',
                    text: 'จำนวนลูกค้าเพิ่มเติมสูงสุดที่สามารถเพิ่มได้คือ ' + maxCustomers + ' ราย',
                    confirmButtonText: 'ตกลง'
                });
                return;
            }

            const template = document.querySelector('#customer-row-template');
            const customerRow = template.content.cloneNode(true);
            $('.selected-customers').append(customerRow);

            const newSelect = $('.customer-select').last();
            newSelect.select2({
                width: '100%',
                dropdownAutoWidth: true,
                placeholder: 'เลือกลูกค้า'
            });

            newSelect.on('select2:select', function(e) {
                const selectedId = e.params.data.id;
                const mainCustomerId = $('select[name="customer_id"]').val();

                if (selectedId === mainCustomerId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ไม่สามารถเลือกลูกค้าซ้ำได้',
                        text: 'ลูกค้ารายนี้ถูกเลือกเป็นลูกค้าหลักแล้ว',
                        confirmButtonText: 'ตกลง'
                    });
                    $(this).val('').trigger('change');
                    return;
                }

                let isDuplicate = false;
                $('.customer-select').not(this).each(function() {
                    if ($(this).val() === selectedId) {
                        isDuplicate = true;
                        return false;
                    }
                });

                if (isDuplicate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ไม่สามารถเลือกลูกค้าซ้ำได้',
                        text: 'กรุณาเลือกลูกค้ารายอื่น',
                        confirmButtonText: 'ตกลง'
                    });
                    $(this).val('').trigger('change');
                }
            });
        });

        $(document).on('click', '.remove-customer', function() {
            const row = $(this).closest('.customer-row');
            Swal.fire({
                icon: 'warning',
                title: 'ยืนยันการลบ',
                text: 'คุณต้องการลบข้อมูลลูกค้ารายนี้ใช่หรือไม่?',
                showCancelButton: true,
                confirmButtonText: 'ใช่, ลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    row.remove();
                }
            });
        });

        $('select[name="customer_id"]').on('change', function() {
            const mainCustomerId = $(this).val();
            $('.customer-select').each(function() {
                if ($(this).val() === mainCustomerId) {
                    const row = $(this).closest('.customer-row');
                    Swal.fire({
                        icon: 'warning',
                        title: 'พบข้อมูลซ้ำ',
                        text: 'ลูกค้ารายนี้ถูกเลือกเป็นลูกค้าเพิ่มเติมไว้แล้ว จะถูกลบออก',
                        confirmButtonText: 'ตกลง'
                    }).then(() => {
                        row.remove();
                    });
                }
            });
        });

        // Submit form ด้วย FormData แทน serialize
        $('#addProjectForm').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'กำลังบันทึกข้อมูล...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                },
            });

            var formData = new FormData(this);

            const customers = [];
            const mainCustomerId = $('select[name="customer_id"]').val();
            if (mainCustomerId) {
                customers.push({
                    customer_id: mainCustomerId,
                    is_primary: 1
                });
            }

            $('.customer-row').each(function() {
                const customerId = $(this).find('.customer-select').val();
                const isPrimary = $(this).find('.primary-customer').is(':checked') ? 1 : 0;
                if (customerId && customerId !== mainCustomerId) {
                    customers.push({
                        customer_id: customerId,
                        is_primary: isPrimary
                    });
                }
            });

            const customersJson = JSON.stringify(customers);
            formData.append('project_customers', customersJson);

            $.ajax({
                url: 'add_project.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสำเร็จ',
                            text: response.message,
                            confirmButtonText: 'ตกลง'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'project.php';
                            }
                        });
                    } else {
                        var errorMessage = response.errors.join('<br>');
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            html: errorMessage,
                            confirmButtonText: 'ตกลง'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ กรุณาลองใหม่อีกครั้ง',
                        confirmButtonText: 'ตกลง'
                    });
                }
            });
        });
    });
</script>

<!-- ฟังก์ชันการจัดการคอมม่าตัวเลข -->
<script>
    function addCommas(nStr) {
        nStr += '';
        var x = nStr.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    function removeCommas(nStr) {
        return nStr.replace(/,/g, '');
    }

    document.addEventListener('DOMContentLoaded', function() {
        var priceInputs = document.querySelectorAll('input[type="int"]');
        priceInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                var cleanValue = removeCommas(this.value);
                if (!isNaN(cleanValue) && cleanValue.length > 0) {
                    this.value = addCommas(cleanValue);
                }
            });
        });

        document.querySelector('form').addEventListener('submit', function(event) {
            priceInputs.forEach(function(input) {
                input.value = removeCommas(input.value);
            });
        });
    });
</script>

<!-- คำนวณ Cost Project -->
<script>
    $(document).ready(function() {
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function calculateNoVatPrice(priceWithVat, vat) {
            return priceWithVat / (1 + (vat / 100));
        }

        function calculateWithVatPrice(priceNoVat, vat) {
            return priceNoVat * (1 + (vat / 100));
        }

        function calculateGrossProfit() {
            var saleNoVat = parseFloat($("#sale_no_vat").val().replace(/,/g, "")) || 0;
            var costNoVat = parseFloat($("#cost_no_vat").val().replace(/,/g, "")) || 0;

            if (saleNoVat && costNoVat) {
                var grossProfit = saleNoVat - costNoVat;
                $("#gross_profit").val(formatNumber(grossProfit.toFixed(2)));

                var grossProfitPercentage = (grossProfit / saleNoVat) * 100;
                $("#potential").val(grossProfitPercentage.toFixed(2) + "%");
            }
        }

        function recalculateEstimate() {
            var saleNoVat = parseFloat($("#sale_no_vat").val().replace(/,/g, "")) || 0;
            var costNoVat = parseFloat($("#cost_no_vat").val().replace(/,/g, "")) || 0;
            var status = $("#status").val();
            var estimateSaleNoVat = 0;
            var estimateCostNoVat = 0;

            var percentage = 0;
            switch (status) {
                case 'นำเสนอโครงการ (Presentations)':
                    percentage = 0;
                    break;
                case 'ใบเสนอราคา (Quotation)':
                    percentage = 10;
                    break;
                case 'ยื่นประมูล (Bidding)':
                    percentage = 10;
                    break;
                case 'ชนะ (Win)':
                    percentage = 100;
                    break;
                case 'แพ้ (Loss)':
                    percentage = 0;
                    break;
                case 'รอการพิจารณา (On Hold)':
                    percentage = 0;
                    break;
                case 'ยกเลิก (Cancled)':
                    percentage = 0;
                    break;
            }

            estimateSaleNoVat = (saleNoVat * percentage) / 100;
            estimateCostNoVat = (costNoVat * percentage) / 100;

            $("#es_sale_no_vat").val(formatNumber(estimateSaleNoVat.toFixed(2)));
            $("#es_cost_no_vat").val(formatNumber(estimateCostNoVat.toFixed(2)));
            $("#es_gp_no_vat").val(formatNumber((estimateSaleNoVat - estimateCostNoVat).toFixed(2)));
        }

        $("#sale_vat").on("input", function() {
            var saleVat = parseFloat($(this).val().replace(/,/g, "")) || 0;
            var vat = parseFloat($("#vat").val()) || 0;
            var saleNoVat = calculateNoVatPrice(saleVat, vat);
            $("#sale_no_vat").val(formatNumber(saleNoVat.toFixed(2)));
            calculateGrossProfit();
            recalculateEstimate();
        });

        $("#sale_no_vat").on("input", function() {
            var saleNoVat = parseFloat($(this).val().replace(/,/g, "")) || 0;
            var vat = parseFloat($("#vat").val()) || 0;
            if (saleNoVat && vat) {
                var saleVat = calculateWithVatPrice(saleNoVat, vat);
                $("#sale_vat").val(formatNumber(saleVat.toFixed(2)));
            }
            calculateGrossProfit();
            recalculateEstimate();
        });

        $("#cost_no_vat").on("input", function() {
            var costNoVat = parseFloat($(this).val().replace(/,/g, "")) || 0;
            var vat = parseFloat($("#vat").val()) || 0;
            if (costNoVat && vat) {
                var costVat = calculateWithVatPrice(costNoVat, vat);
                $("#cost_vat").val(formatNumber(costVat.toFixed(2)));
            }
            calculateGrossProfit();
            recalculateEstimate();
        });

        $("#cost_vat").on("input", function() {
            var costVat = parseFloat($(this).val().replace(/,/g, "")) || 0;
            var vat = parseFloat($("#vat").val()) || 0;
            var costNoVat = calculateNoVatPrice(costVat, vat);
            $("#cost_no_vat").val(formatNumber(costNoVat.toFixed(2)));
            calculateGrossProfit();
            recalculateEstimate();
        });

        $("#vat").on("change", function() {
            $("#sale_vat").trigger("input");
            $("#sale_no_vat").trigger("input");
            $("#cost_vat").trigger("input");
            $("#cost_no_vat").trigger("input");
        });

        $("#status").on("change", function() {
            recalculateEstimate();
        });
    });
</script>
</body>
</html>
