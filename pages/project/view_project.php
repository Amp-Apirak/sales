<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;
$user_team_id = $_SESSION['team_id'] ?? 0;

// ตรวจสอบว่า project_id ถูกส่งมาจาก URL หรือไม่
if (!isset($_GET['project_id']) || empty($_GET['project_id'])) {
    echo "ไม่พบข้อมูลโครงการ";
    exit;
}

// รับ project_id จาก URL และทำการถอดรหัส
$project_id = decryptUserId($_GET['project_id']);

// ดึงข้อมูลโครงการและผู้สร้าง
try {
    $sql = "SELECT p.*, u.team_id as creator_team_id, 
            u.first_name, u.last_name, u.email as seller_email, u.phone as seller_phone,
            pr.product_name, pr.product_description,
            c.customer_name, c.company, c.address, c.phone as customer_phone, c.email as customer_email,
            t.team_name,
            tl.first_name as team_leader_first_name, tl.last_name as team_leader_last_name,
            creator.first_name as creator_first_name, creator.last_name as creator_last_name,
            updater.first_name as updater_first_name, updater.last_name as updater_last_name
            FROM projects p 
            LEFT JOIN users u ON p.seller = u.user_id 
            LEFT JOIN products pr ON p.product_id = pr.product_id 
            LEFT JOIN customers c ON p.customer_id = c.customer_id 
            LEFT JOIN teams t ON u.team_id = t.team_id 
            LEFT JOIN users tl ON t.team_leader = tl.user_id
            LEFT JOIN users creator ON p.created_by = creator.user_id
            LEFT JOIN users updater ON p.updated_by = updater.user_id
            WHERE p.project_id = :project_id";

    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':project_id', $project_id, PDO::PARAM_STR);
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        echo "ไม่พบโครงการที่ต้องการแสดง";
        exit;
    }

    // ตรวจสอบสิทธิ์การเข้าถึง
    $hasAccess = false;

    switch ($role) {
        case 'Executive':
            $hasAccess = true;
            break;
        case 'Sale Supervisor':
            if ($user_team_id == $project['creator_team_id']) {
                $hasAccess = true;
            }
            break;
        case 'Seller':
            if ($user_id == $project['created_by']) {
                $hasAccess = true;
            }
            break;
    }

    if (!$hasAccess) {
        echo "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
        exit;
    }

    // ดึงข้อมูลการชำระเงินของโครงการ
    $sql_payments = "SELECT * FROM project_payments WHERE project_id = :project_id ORDER BY payment_number";
    $stmt_payments = $condb->prepare($sql_payments);
    $stmt_payments->bindParam(':project_id', $project_id, PDO::PARAM_STR);
    $stmt_payments->execute();
    $payments = $stmt_payments->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
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

// ฟังก์ชันสำหรับกำหนดคลาส CSS ตามสถานะการชำระเงิน
function getStatusClass($status)
{
    switch ($status) {
        case 'Paid':
            return 'text-success';
        case 'Pending':
            return 'text-warning';
        case 'Overdue':
            return 'text-danger';
        default:
            return '';
    }
}


// ดึงข้อมูลลูกค้าทั้งหมดในโครงการ
// ดึงข้อมูลลูกค้าจาก projects (ลูกค้าหลัก) และ project_customers (ลูกค้าทั้งหมด)
$sql_customers = "
    SELECT DISTINCT c.customer_name, c.company, c.address, c.phone, c.email, c.position
    FROM (
        SELECT p.customer_id FROM projects p WHERE p.project_id = :project_id
        UNION
        SELECT pc.customer_id FROM project_customers pc WHERE pc.project_id = :project_id
    ) AS customer_ids
    JOIN customers c ON customer_ids.customer_id = c.customer_id";

$stmt_customers = $condb->prepare($sql_customers); // เตรียมคำสั่ง SQL
$stmt_customers->bindParam(':project_id', $project_id, PDO::PARAM_STR); // ผูกค่าพารามิเตอร์
$stmt_customers->execute(); // ดำเนินการคำสั่ง SQL
$project_customers = $stmt_customers->fetchAll(PDO::FETCH_ASSOC); // ดึงผลลัพธ์ทั้งหมด


?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "project"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalePipeline | View Project</title>
    <?php include '../../include/header.php'; ?>

    <!-- PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <!-- PDF -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/css/view_project.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>
        <div class="content-wrapper">

            <!-- เพิ่มส่วนนี้หลังจาก project-header -->
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#project-info" data-toggle="tab" data-tab="project-info">ข้อมูลโครงการ</a></li>
                        <li class="nav-item"><a class="nav-link " href="#project-cost" data-toggle="tab" data-tab="project-cost">ต้นทุนโครงการ</a></li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tasks" data-toggle="tab" role="tab">บริหารโครงการ</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#documents" data-toggle="tab" data-tab="documents">เอกสารแนบ</a></li>
                        <li class="nav-item"><a class="nav-link" href="#links" data-toggle="tab" data-tab="links">แนบลิงค์เอกสารโครงการ</a></li>
                        <li class="nav-item"><a class="nav-link" href="#images" data-toggle="tab" data-tab="images">รูปภาพ</a></li>

                    </ul
                        </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- แถบที่ 1 ตารางแสดงข้อมูลรวม -->
                            <div class="active tab-pane" id="project-info">
                                <section class="content">
                                    <div class="container-fluid">
                                        <!-- ส่วนหัวของโปรเจค -->
                                        <div class="project-header">
                                            <div class="project-title"><?php echo htmlspecialchars($project['project_name']); ?></div>
                                            <span class="project-status"><?php echo htmlspecialchars($project['status']); ?></span>
                                            <div class="project-date"><i class="far fa-calendar-alt mr-2"></i><?php echo htmlspecialchars($project['start_date']) . ' - ' . htmlspecialchars($project['end_date']); ?></div>
                                        </div>

                                        <!-- ข้อมูลโครงการ -->
                                        <div class="info-card">
                                            <div class="info-card-header">
                                                <span><i class="fas fa-info-circle mr-2"></i>ข้อมูลโครงการ</span>
                                                <button class="edit-button no-print" onclick="location.href='edit_project.php?project_id=<?php echo urlencode(encryptUserId($project['project_id'])); ?>'">
                                                    <i class="fas fa-edit"></i> แก้ไข
                                                </button>
                                                <button class="edit-button no-print" onclick="generatePDF()">
                                                    <i class="fas fa-file-pdf"></i> Save PDF
                                                </button>
                                            </div>
                                            <div class="info-card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="info-item">
                                                            <span class="info-label">เลขที่สัญญา:</span>
                                                            <span class="info-value"><?php echo htmlspecialchars($project['contract_no']); ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="info-label">สินค้า:</span>
                                                            <span class="info-value"><?php echo htmlspecialchars($project['product_name']); ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="info-label">รายละเอียดสินค้า:</span>
                                                            <span class="info-value"><?php echo htmlspecialchars($project['product_description']); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="info-item">
                                                            <span class="info-label">ผู้สร้าง:</span>
                                                            <span class="info-value"><?php echo htmlspecialchars($project['creator_first_name'] . ' ' . $project['creator_last_name']); ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="info-label">วันที่แก้ไขล่าสุด:</span>
                                                            <span class="info-value"><?php echo htmlspecialchars($project['updated_at']); ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="info-label">ผู้แก้ไขล่าสุด:</span>
                                                            <span class="info-value"><?php echo htmlspecialchars($project['updater_first_name'] . ' ' . $project['updater_last_name']); ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="info-label">วันที่สร้าง:</span>
                                                            <span class="info-value"><?php echo htmlspecialchars($project['created_at']); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <!-- ข้อมูลทางการเงิน -->
                                        <div class="info-card">
                                            <div class="info-card-header">
                                                <i class="fas fa-chart-bar mr-2"></i>ข้อมูลทางการเงิน
                                            </div>
                                            <div class="info-card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="info-item">
                                                            <span class="info-label">ราคาขาย (รวมภาษี):</span>
                                                            <span class="info-value"><?php echo number_format($project['sale_vat'], 2); ?> บาท</span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="info-label">ราคาขาย (ไม่รวมภาษี):</span>
                                                            <span class="info-value"><?php echo number_format($project['sale_no_vat'], 2); ?> บาท</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="info-item">
                                                            <span class="info-label">ต้นทุน (รวมภาษี):</span>
                                                            <span class="info-value"><?php echo number_format($project['cost_vat'], 2); ?> บาท</span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="info-label">ต้นทุน (ไม่รวมภาษี):</span>
                                                            <span class="info-value"><?php echo number_format($project['cost_no_vat'], 2); ?> บาท</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="financial-summary">
                                                    <div class="financial-item">
                                                        <span class="financial-label">กำไรขั้นต้น:</span>
                                                        <span class="financial-value profit-highlight"><?php echo number_format($project['gross_profit'], 2); ?> บาท</span>
                                                    </div>
                                                    <div class="financial-item">
                                                        <span class="financial-label">กำไรขั้นต้น (%):</span>
                                                        <span class="financial-value profit-highlight"><?php echo number_format($project['potential'], 2); ?>%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ข้อมูลการชำระเงิน -->
                                        <div class="info-card">
                                            <div class="info-card-header table-section">
                                                <span><i class="fas fa-info-circle mr-2"></i>ข้อมูลการชำระเงิน</span>
                                                <button class="edit-button btn-sm" onclick="openAddPaymentModal()">
                                                    <i class="fas fa-plus"></i> เพิ่ม
                                                </button>
                                            </div>
                                            <div class="info-card-body table-section ">
                                                <div class="payment-info">
                                                    <div class="table-view d-none d-md-block">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>งวดที่</th>
                                                                    <th>จำนวนเงิน</th>
                                                                    <th>คิดเป็นเปอร์เซนต์</th>
                                                                    <th>วันครบกำหนด</th>
                                                                    <th>สถานะ</th>
                                                                    <th>วันที่ชำระ</th>
                                                                    <th>จำนวนเงินที่ชำระแล้ว</th>
                                                                    <th class="no-print">การดำเนินการ</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($payments as $payment): ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars($payment['payment_number']); ?></td>
                                                                        <td><?php echo number_format($payment['amount'], 2); ?> บาท</td>
                                                                        <td><?php echo htmlspecialchars($payment['payment_percentage']); ?></td>
                                                                        <td><?php echo htmlspecialchars($payment['due_date']); ?></td>
                                                                        <td>
                                                                            <span class="<?php echo getStatusClass($payment['status']); ?>">
                                                                                <?php echo htmlspecialchars($payment['status']); ?>
                                                                            </span>
                                                                        </td>
                                                                        <td><?php echo $payment['payment_date'] ? htmlspecialchars($payment['payment_date']) : '-'; ?></td>
                                                                        <td><?php echo number_format($payment['amount_paid'], 2); ?> บาท</td>
                                                                        <td>
                                                                            <button class="btn btn-sm btn-info mr-1" onclick="editPayment('<?php echo $payment['payment_id']; ?>')">
                                                                                <i class="fas fa-edit"></i>
                                                                            </button>
                                                                            <button class="btn btn-sm btn-danger" onclick="deletePayment('<?php echo $payment['payment_id']; ?>')">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div class="card-view d-md-none">
                                                        <?php foreach ($payments as $payment): ?>
                                                            <div class="payment-card mb-3">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <h5 class="card-title">งวดที่ <?php echo htmlspecialchars($payment['payment_number']); ?></h5>
                                                                        <p class="card-text">
                                                                            <strong>จำนวนเงิน:</strong> <?php echo number_format($payment['amount'], 2); ?> บาท<br>
                                                                            <strong>คิดเป็นเปอร์เซนต์:</strong> <?php echo htmlspecialchars($payment['payment_percentage']); ?><br>
                                                                            <strong>วันครบกำหนด:</strong> <?php echo htmlspecialchars($payment['due_date']); ?><br>
                                                                            <strong>สถานะ:</strong> <span class="<?php echo getStatusClass($payment['status']); ?>"><?php echo htmlspecialchars($payment['status']); ?></span><br>
                                                                            <strong>วันที่ชำระ:</strong> <?php echo $payment['payment_date'] ? htmlspecialchars($payment['payment_date']) : '-'; ?><br>
                                                                            <strong>จำนวนเงินที่ชำระแล้ว:</strong> <?php echo number_format($payment['amount_paid'], 2); ?> บาท
                                                                        </p>
                                                                        <div class="btn-group" role="group">
                                                                            <button class="btn btn-sm btn-info mr-1" onclick="editPayment('<?php echo $payment['payment_id']; ?>')">
                                                                                <i class="fas fa-edit"></i> แก้ไข
                                                                            </button>
                                                                            <button class="btn btn-sm btn-danger" onclick="deletePayment('<?php echo $payment['payment_id']; ?>')">
                                                                                <i class="fas fa-trash"></i> ลบ
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <!-- สรุปข้อมูลการชำระเงิน -->
                                                <div class="mt-3">
                                                    <strong>สรุปการชำระเงิน:</strong>
                                                    <div class="row mt-2">
                                                        <div class="col-md-4">
                                                            <div class="info-item">
                                                                <span class="info-label">ราคาขาย (ไม่รวมภาษี):</span>
                                                                <span class="info-value"><?php echo number_format($project['sale_no_vat'], 2); ?> บาท</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="info-item">
                                                                <span class="info-label">จำนวนเงินรวมงวดชำระ :</span>
                                                                <span class="info-value"><?php
                                                                                            $total_scheduled_payments = array_sum(array_column($payments, 'amount'));
                                                                                            echo number_format($total_scheduled_payments, 2);
                                                                                            ?> บาท</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="info-item">
                                                                <span class="info-label">(%)รวมงวดชำระ :</span>
                                                                <span class="info-value"><?php
                                                                                            $total_percentage_scheduled = array_sum(array_column($payments, 'payment_percentage'));
                                                                                            echo number_format($total_percentage_scheduled, 2);
                                                                                            ?>%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="info-item">
                                                                <span class="info-label">จำนวนภาษีมูลค่าเพิ่ม (VAT 7%):</span>
                                                                <span class="info-value"><?php
                                                                                            $vat_rate = 7; // กำหนดอัตรา VAT เป็น 7%
                                                                                            $vat_amount = ($project['sale_no_vat'] * $vat_rate) / 100;
                                                                                            echo number_format($vat_amount, 2);
                                                                                            ?> บาท</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="info-item">
                                                                <span class="info-label text-success">จำนวนเงินที่ชำระแล้ว:</span>
                                                                <span class="info-value text-success"><?php
                                                                                                        // คำนวณจำนวนงวดที่ชำระแล้วและจำนวนงวดทั้งหมด
                                                                                                        $paidInstallments = 0;
                                                                                                        $totalInstallments = count($payments);
                                                                                                        $total_paid = 0;

                                                                                                        foreach ($payments as $payment) {
                                                                                                            if ($payment['status'] == 'Paid') {
                                                                                                                $paidInstallments++;
                                                                                                                $total_paid += $payment['amount'];
                                                                                                            }
                                                                                                        }

                                                                                                        // แสดงผลจำนวนเงินที่ชำระแล้วและจำนวนงวด
                                                                                                        echo number_format($total_paid, 2);
                                                                                                        ?> บาท (<?php echo $paidInstallments; ?>/<?php echo $totalInstallments; ?> งวด)
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="info-item">
                                                                <span class="info-label text-danger">(%)ที่ยังไม่ได้แบ่งชำระ:</span>
                                                                <span class="info-value text-danger"><?php
                                                                                                        $pending_percentage = 0;

                                                                                                        // หาผลรวมเปอร์เซ็นต์ที่ยังไม่ชำระ (Pending)
                                                                                                        foreach ($payments as $payment) {
                                                                                                            if ($payment['status'] == 'Pending') {
                                                                                                                $pending_percentage += floatval($payment['payment_percentage']);
                                                                                                            }
                                                                                                        }

                                                                                                        echo number_format($pending_percentage, 2);
                                                                                                        ?>%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ข้อมูลลูกค้า -->
                                        <div class="row equal-height-cards">
                                            <div class="col-md-12">
                                                <div class="info-card">
                                                    <div class="info-card-header">
                                                        <span><i class="fas fa-user mr-2"></i>ข้อมูลลูกค้า</span>
                                                    </div>
                                                    <div class="info-card-body">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>ชื่อลูกค้า</th>
                                                                    <th>ตำแหน่ง</th>
                                                                    <th>บริษัท</th>
                                                                    <th>โทรศัพท์</th>
                                                                    <th>อีเมล</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($project_customers as $customer): ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars($customer['customer_name']); ?></td>
                                                                        <td><?php echo htmlspecialchars($customer['position'] ?? 'ไม่ระบุ'); ?></td>
                                                                        <td><?php echo htmlspecialchars($customer['company'] ?? 'ไม่ระบุ'); ?></td>
                                                                        <td><?php echo htmlspecialchars($customer['phone'] ?? 'ไม่ระบุ'); ?></td>
                                                                        <td><?php echo htmlspecialchars($customer['email'] ?? 'ไม่ระบุ'); ?></td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- ข้อมูลผู้ขาย -->
                                        <div class="row equal-height-cards">
                                            <div class="col-md-12">
                                                <div class="info-card">
                                                    <div class="info-card-header">
                                                        <i class="fas fa-user-tie mr-2"></i>ข้อมูลผู้ขาย
                                                    </div>
                                                    <div class="info-card-body">
                                                        <div class="info-item">
                                                            <span class="info-label">ชื่อผู้ขาย:</span>
                                                            <span class="info-value"><?php echo htmlspecialchars($project['first_name'] . ' ' . $project['last_name']); ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="info-label">อีเมล:</span>
                                                            <span class="info-value"><?php echo htmlspecialchars($project['seller_email']); ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="info-label">โทรศัพท์:</span>
                                                            <span class="info-value"><?php echo htmlspecialchars($project['seller_phone']); ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="info-label">ทีม:</span>
                                                            <span class="info-value"><?php echo htmlspecialchars($project['team_name']); ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="info-label">หัวหน้าทีมฝ่ายขาย:</span>
                                                            <span class="info-value">
                                                                <?php
                                                                if (isset($project['team_leader_first_name']) && isset($project['team_leader_last_name'])) {
                                                                    echo htmlspecialchars($project['team_leader_first_name'] . ' ' . $project['team_leader_last_name']);
                                                                } else {
                                                                    echo 'ไม่ระบุ';
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </section>
                            </div>

                            <!-- แถบที่ 2 ต้นทุนโครงการ -->
                            <div class="tab-pane" id="project-cost">
                                <div class="table-responsive">
                                    <table id="costTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>PART No.</th>
                                                <th>Description</th>
                                                <th>QTY.</th>
                                                <th>Unit</th>
                                                <th>Price / Unit</th>
                                                <th>Total Amount</th>
                                                <th>Cost / Unit</th>
                                                <th>Total Cost</th>
                                                <th>Supplier</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="costTableBody">
                                            <!-- ข้อมูลจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                        </tbody>
                                        <!-- แถวสำหรับกรอกข้อมูลใหม่ -->
                                        <tfoot>
                                            <tr>
                                                <td><input type="text" id="typeInput" class="form-control form-control-sm" placeholder="A, B, C"></td>
                                                <td><input type="text" id="partNoInput" class="form-control form-control-sm" placeholder="Service, Hardware, Software"></td>
                                                <td><input type="text" id="descriptionInput" class="form-control form-control-sm" placeholder="ใส่รายละเอียด"></td>
                                                <td><input type="number" id="qtyInput" class="form-control form-control-sm" placeholder="จำนวนตัวเลข"></td>
                                                <td><input type="text" id="unitInput" class="form-control form-control-sm" placeholder="เช่น วัน, คน, ชิ้น"></td>
                                                <td><input type="text" id="priceInput" class="form-control form-control-sm" placeholder="ตั้งราคาขาย"></td>
                                                <td><span id="totalAmountInput">0.00</span></td>
                                                <td><input type="text" id="costInput" class="form-control form-control-sm" placeholder="ตั้งราคาต้นทุน"></td>
                                                <td><span id="totalCostInput">0.00</span></td>
                                                <td><input type="text" id="supplierInput" class="form-control form-control-sm" placeholder=""></td>
                                                <td><button class="btn btn-sm btn-success" onclick="saveCost()">เพิ่ม</button></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <!-- Total Section -->
                                <div class="totals-section">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <p>Total Amount: <span id="totalAmount">0.00</span> บาท</p>
                                                    <p>Vat (7%): <span id="vatAmount">0.00</span> บาท</p>
                                                    <p>Grand Total: <span id="grandTotal">0.00</span> บาท</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p>Total Cost: <span id="totalCost">0.00</span> บาท</p>
                                                    <p>Cost Vat (7%): <span id="costVatAmount">0.00</span> บาท</p>
                                                    <p>Total Cost with Vat: <span id="totalCostWithVat">0.00</span> บาท</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p>Profit: <span id="profitAmount">0.00</span> บาท</p>
                                                    <p>Profit Percentage: <span id="profitPercentage">0.00</span>%</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- แถบที่ 3 ตารางแสดงไฟล์เอกสาร -->
                            <div class="tab-pane" id="documents">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadModal">
                                        <i class="fas fa-upload"></i> อัปโหลดเอกสาร
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ลำดับ</th>
                                                <th>ชื่อเอกสาร</th>
                                                <th>ประเภท</th>
                                                <th>วันที่สร้าง</th>
                                                <th>ผู้สร้าง</th>
                                                <th>การดำเนินการ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="documentTableBody">
                                            <!-- ข้อมูลเอกสารจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- แถบที่ 4 ตารางแสดงภาพ -->
                            <div class="tab-pane" id="images">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="$('#imageUpload').click()">
                                        <i class="fas fa-upload"></i> อัปโหลดรูปภาพ
                                    </button>
                                    <input type="file" id="imageUpload" style="display: none;" multiple accept="image/*">
                                </div>
                                <div id="uploadProgress" class="mb-3" style="display: none;">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                    </div>
                                </div>
                                <div id="imageGallery" class="image-gallery">
                                    <!-- รูปภาพจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                </div>
                                <div id="lightbox" class="lightbox">
                                    <span class="close">&times;</span>
                                    <img class="lightbox-content" id="lightbox-img">
                                </div>
                            </div>

                            <!-- แถบที่ 5 ตาราง links -->
                            <div class="tab-pane" id="links">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#linkModal">
                                        <i class="fas fa-plus"></i> เพิ่มลิงก์เอกสาร
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table id="example2" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ลำดับ</th>
                                                <th>หมวด/หมู่เอกสาร</th>
                                                <th>ชื่อเอกสาร</th>
                                                <th>วันที่สร้าง</th>
                                                <th>ผู้สร้าง</th>
                                                <th>การดำเนินการ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="linkTableBody">
                                            <!-- ข้อมูลจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- แถบที่ 6 บริหารโครงการ -->
                            <div class="tab-pane" id="tasks">
                                <?php include 'management/project_tasks.php'; ?>
                            </div>

                            <!-- เพิ่ม JavaScript ที่จำเป็น -->
                            <script src="management/js/task_management.js"></script>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../../include/footer.php'; ?>
    </div>
</body>

</html>

<!-- 1. Modal สำหรับเพิ่ม/แก้ไขการชำระเงิน -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">เพิ่มการชำระเงิน</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" id="paymentId">
                    <div class="form-group">
                        <label for="paymentNumber">งวดที่</label>
                        <input type="number" class="form-control" id="paymentNumber" required>
                    </div>
                    <div class="form-group">
                        <label for="paymentPercentage">เปอร์เซ็นต์การชำระ (%)</label>
                        <input type="text" class="form-control" id="paymentPercentage" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="amount">จำนวนเงิน (บาท)</label>
                        <input type="text" class="form-control" id="amount">
                    </div>
                    <div class="form-group">
                        <label for="dueDate">วันครบกำหนด</label>
                        <input type="date" class="form-control" id="dueDate">
                    </div>
                    <div class="form-group">
                        <label for="status">สถานะ</label>
                        <select class="form-control" id="status" required>
                            <option value="Pending">รอชำระ</option>
                            <option value="Paid">ชำระแล้ว</option>
                            <option value="Overdue">เกินกำหนด</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="paymentDate">วันที่ชำระ</label>
                        <input type="date" class="form-control" id="paymentDate">
                    </div>
                    <div class="form-group">
                        <label for="amountPaid">จำนวนเงินที่ชำระแล้ว (บาท)</label>
                        <input type="text" class="form-control" id="amountPaid" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="savePayment()">บันทึก</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ตัวแปรสำหรับเก็บข้อมูลการชำระเงินทั้งหมด
    let payments = <?php echo json_encode($payments); ?>;
    let totalSaleAmount = <?php echo $project['sale_no_vat']; ?>; // ราคาขาย (ไม่รวมภาษี)

    // ฟังก์ชันสำหรับฟอร์แมตตัวเลขให้มีคอมม่าและทศนิยม 2 ตำแหน่ง
    function formatNumber(num) {
        return new Intl.NumberFormat('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(num);
    }

    // ฟังก์ชันสำหรับแปลงข้อความที่มีคอมม่าเป็นตัวเลข
    function parseFormattedNumber(str) {
        return parseFloat(str.replace(/,/g, '')) || 0;
    }

    // ฟังก์ชันสำหรับจัดการการป้อนข้อมูลในช่องตัวเลข
    function setupNumberInput(inputId) {
        const input = document.getElementById(inputId);
        let previousValue = '';

        input.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            let value = e.target.value.replace(/[^0-9.]/g, '');

            // จำกัดให้มีจุดทศนิยมได้เพียงจุดเดียว
            let parts = value.split('.');
            if (parts.length > 2) {
                parts = [parts[0], parts.slice(1).join('')];
                value = parts.join('.');
            }

            // จำกัดทศนิยมให้เหลือ 2 ตำแหน่ง
            if (parts.length > 1) {
                parts[1] = parts[1].slice(0, 2);
                value = parts.join('.');
            }

            // แปลงค่าเป็นตัวเลขและฟอร์แมตใหม่
            const formattedValue = value ? formatNumber(parseFloat(value)) : '';

            // คำนวณตำแหน่ง cursor ใหม่
            const addedCommas = (formattedValue.match(/,/g) || []).length - (previousValue.match(/,/g) || []).length;
            const newCursorPosition = cursorPosition + addedCommas;

            // อัปเดตค่าในช่องป้อนข้อมูล
            e.target.value = formattedValue;

            // ตั้งตำแหน่ง cursor ใหม่
            e.target.setSelectionRange(newCursorPosition, newCursorPosition);

            previousValue = formattedValue;

            // ทริกเกอร์การคำนวณที่เกี่ยวข้อง
            if (inputId === 'paymentPercentage') {
                calculateAmountFromPercentage();
            } else if (inputId === 'amount') {
                calculatePercentageFromAmount();
            }
        });
    }

    // ฟังก์ชันสำหรับคำนวณจำนวนเงินจากเปอร์เซ็นต์
    function calculateAmountFromPercentage() {
        const percentage = parseFloat($('#paymentPercentage').val().replace(/,/g, '')) || 0;
        const amount = (percentage / 100) * totalSaleAmount;
        $('#amount').val(formatNumber(amount.toFixed(2)));
    }


    // ฟังก์ชันสำหรับคำนวณเปอร์เซ็นต์จากจำนวนเงิน
    function calculatePercentageFromAmount() {
        const amount = parseFloat($('#amount').val().replace(/,/g, '')) || 0;
        const percentage = (amount / totalSaleAmount) * 100;
        $('#paymentPercentage').val(percentage.toFixed(2));
    }

    // ฟังก์ชันสำหรับอัปเดตจำนวนเงินที่ชำระแล้วตามสถานะการชำระเงิน
    function updateAmountPaid() {
        const status = document.getElementById('status').value;
        const amount = parseFormattedNumber(document.getElementById('amount').value);
        if (status === 'Paid') {
            document.getElementById('amountPaid').value = formatNumber(amount);
        } else {
            document.getElementById('amountPaid').value = formatNumber(0);
        }
    }

    // ฟังก์ชันเปิด Modal สำหรับเพิ่มการชำระเงิน
    function openAddPaymentModal() {
        document.getElementById('paymentModalLabel').textContent = 'เพิ่มการชำระเงิน';
        document.getElementById('paymentForm').reset();
        document.getElementById('paymentId').value = '';
        document.getElementById('paymentNumber').value = payments.length + 1;
        $('#paymentModal').modal('show');
    }

    // ฟังก์ชันเปิด Modal สำหรับแก้ไขการชำระเงิน
    function editPayment(paymentId) {
        const payment = payments.find(p => p.payment_id === paymentId);
        if (payment) {
            document.getElementById('paymentModalLabel').textContent = 'แก้ไขการชำระเงิน';
            document.getElementById('paymentId').value = payment.payment_id;
            document.getElementById('paymentNumber').value = payment.payment_number;
            document.getElementById('paymentPercentage').value = formatNumber(payment.payment_percentage);
            document.getElementById('amount').value = formatNumber(payment.amount);
            document.getElementById('dueDate').value = payment.due_date;
            document.getElementById('status').value = payment.status;
            document.getElementById('paymentDate').value = payment.payment_date || '';
            document.getElementById('amountPaid').value = formatNumber(payment.amount_paid);
            $('#paymentModal').modal('show');
        }
    }

    // ฟังก์ชันสำหรับบันทึกข้อมูลการชำระเงิน (เพิ่มหรือแก้ไข)
    function savePayment() {
        const paymentData = {
            csrf_token: document.querySelector('input[name="csrf_token"]').value,
            payment_id: document.getElementById('paymentId').value,
            project_id: '<?php echo $project_id; ?>',
            payment_number: document.getElementById('paymentNumber').value,
            amount: parseFormattedNumber(document.getElementById('amount').value),
            payment_percentage: parseFormattedNumber(document.getElementById('paymentPercentage').value),
            due_date: document.getElementById('dueDate').value,
            status: document.getElementById('status').value,
            payment_date: document.getElementById('paymentDate').value,
            amount_paid: parseFormattedNumber(document.getElementById('amountPaid').value)
        };

        // คำนวณเปอร์เซ็นต์รวมของการชำระเงินทั้งหมด
        let totalPercentage = payments.reduce((total, payment) => {
            // ถ้ากำลังแก้ไขรายการปัจจุบัน ไม่นับเปอร์เซ็นต์เดิม
            if (payment.payment_id !== paymentData.payment_id) {
                return total + parseFloat(payment.payment_percentage);
            }
            return total;
        }, 0);

        // เพิ่มเปอร์เซ็นต์ของการชำระเงินปัจจุบัน
        totalPercentage += parseFloat(paymentData.payment_percentage);

        if (totalPercentage > 100) {
            Swal.fire({
                icon: 'warning',
                title: 'เกินขีดจำกัด',
                text: 'เปอร์เซ็นต์รวมของการชำระเงินเกิน 100% ของราคาขาย (ไม่รวมภาษี)',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        $.ajax({
            url: 'save_payment.php',
            type: 'POST',
            data: paymentData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'บันทึกข้อมูลสำเร็จ',
                        confirmButtonText: 'ตกลง'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message,
                        confirmButtonText: 'ตกลง'
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + textStatus,
                    confirmButtonText: 'ตกลง'
                });
            }
        });
    }

    // ฟังก์ชันสำหรับลบข้อมูลการชำระเงิน
    function deletePayment(paymentId) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบรายการชำระเงินนี้ใช่หรือไม่?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_payment.php',
                    type: 'POST',
                    data: {
                        csrf_token: document.querySelector('input[name="csrf_token"]').value,
                        payment_id: paymentId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ',
                                text: response.message || 'ลบข้อมูลสำเร็จ',
                                confirmButtonText: 'ตกลง'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: (response && response.message) ? response.message : 'ไม่สามารถลบข้อมูลได้',
                                confirmButtonText: 'ตกลง'
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error:', textStatus, errorThrown);
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + textStatus,
                            confirmButtonText: 'ตกลง'
                        });
                    }
                });
            }
        });
    }

    // เพิ่ม Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        setupNumberInput('paymentPercentage');
        setupNumberInput('amount');
        document.getElementById('status').addEventListener('change', updateAmountPaid);
        // ฟังก์ชันจัดการการป้อนข้อมูลเปอร์เซ็นต์
        document.getElementById('paymentPercentage').addEventListener('input', calculateAmountFromPercentage);
        // ฟังก์ชันจัดการการป้อนจำนวนเงิน  
        document.getElementById('amount').addEventListener('input', calculatePercentageFromAmount);
    });

    // Event Listeners
    $('#paymentPercentage').on('input', function() {
        calculateAmountFromPercentage();
    });

    $('#amount').on('input', function() {
        calculatePercentageFromAmount();
    });
</script>
<!-- 1. Modal สำหรับเพิ่ม/แก้ไขการชำระเงิน -->


<!-- 2. Modal สำหรับอัปโหลดเอกสาร -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">อัปโหลดเอกสาร</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div class="form-group">
                        <label for="documentName">ชื่อเอกสาร</label>
                        <input type="text" class="form-control" id="documentName" name="documentName" required>
                    </div>
                    <div class="form-group">
                        <label for="documentFile">เลือกไฟล์</label>
                        <input type="file" class="form-control-file" id="documentFile" name="documentFile" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="uploadDocument()">อัปโหลด</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ฟังก์ชันสำหรับอัปโหลดเอกสาร
    function uploadDocument() {
        var formData = new FormData(document.getElementById('uploadForm'));

        $.ajax({
            url: 'upload_document.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'อัปโหลดสำเร็จ',
                        text: 'เอกสารถูกอัปโหลดเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#uploadModal').modal('hide');
                            loadDocuments(); // รีโหลดข้อมูลเอกสาร
                            resetUploadForm(); // เพิ่มฟังก์ชันนี้เพื่อรีเซ็ตฟอร์ม
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message,
                        confirmButtonText: 'ตกลง'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                    confirmButtonText: 'ตกลง'
                });
            }
        });
    }

    // เพิ่มฟังก์ชันใหม่เพื่อรีเซ็ตฟอร์ม
    function resetUploadForm() {
        $('#uploadForm')[0].reset();
        $('#documentFile').val(''); // รีเซ็ต file input
    }

    // เพิ่ม event listener เมื่อ Modal ถูกซ่อน
    $('#uploadModal').on('hidden.bs.modal', function() {
        resetUploadForm();
    });

    // ฟังก์ชันสำหรับโหลดข้อมูลเอกสาร
    function loadDocuments() {
        $.ajax({
            url: 'get_documents.php',
            type: 'GET',
            data: {
                project_id: '<?php echo $project_id; ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var table = $('#example1').DataTable();
                    table.clear().draw();
                    $.each(response.documents, function(index, doc) {
                        table.row.add([
                            index + 1,
                            doc.document_name,
                            doc.document_type,
                            doc.upload_date,
                            doc.uploaded_by,
                            '<button class="btn btn-sm btn-info mr-1" onclick="viewDocument(\'' + doc.document_id + '\')">ดู</button>' +
                            '<button class="btn btn-sm btn-danger" onclick="deleteDocument(\'' + doc.document_id + '\')">ลบ</button>'
                        ]).draw(false);
                    });
                } else {
                    console.error('Failed to load documents:', response.message);
                }
            },
            error: function() {
                console.error('Error connecting to server');
            }
        });
    }

    // ฟังก์ชันสำหรับดูเอกสาร
    function viewDocument(documentId) {
        window.open('view_document.php?document_id=' + documentId, '_blank');
    }

    // ฟังก์ชันสำหรับลบเอกสาร
    function deleteDocument(documentId) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบเอกสารนี้ใช่หรือไม่?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_document.php',
                    type: 'POST',
                    data: {
                        csrf_token: '<?php echo $csrf_token; ?>',
                        document_id: documentId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'ลบแล้ว!',
                                'เอกสารถูกลบเรียบร้อยแล้ว',
                                'success'
                            ).then(() => {
                                loadDocuments(); // รีโหลดข้อมูลเอกสาร
                            });
                        } else {
                            Swal.fire(
                                'เกิดข้อผิดพลาด!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'เกิดข้อผิดพลาด!',
                            'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                            'error'
                        );
                    }
                });
            }
        });
    }

    // โหลดข้อมูลเอกสารเมื่อเปิดแท็บเอกสาร
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        if (e.target.hash === '#documents') {
            loadDocuments();
        }
    });
</script>
<!-- 2. Modal สำหรับอัปโหลดเอกสาร -->

<!-- 3. Active Tab -->
<script>
    // Function to get URL parameter
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Function to set active tab based on URL parameter
    function setActiveTab() {
        var activeTab = getUrlParameter('tab');
        if (activeTab) {
            $('.nav-pills a[data-tab="' + activeTab + '"]').tab('show');
        }
    }

    // Update URL when tab is changed
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var tabName = $(e.target).data('tab');
        var url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);
    });

    // Call setActiveTab on page load
    $(document).ready(function() {
        setActiveTab();
    });
</script>
<!-- 3. Active Tab -->

<!-- 4. การอัปโหลดและแสดงรูปภาพ -->
<script>
    $(document).ready(function() {
        loadImages();

        $('#imageUpload').on('change', function(e) {
            var files = e.target.files;
            uploadImages(files);
        });
    });

    function loadImages() {
        $.ajax({
            url: 'get_images.php',
            type: 'GET',
            data: {
                project_id: '<?php echo $project_id; ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#imageGallery').empty();
                    response.images.forEach(function(image) {
                        addImageToGallery(image);
                    });
                } else {
                    console.error('Failed to load images:', response.message);
                }
            },
            error: function() {
                console.error('Error connecting to server');
            }
        });
    }

    function uploadImages(files) {
        var formData = new FormData();
        formData.append('project_id', '<?php echo $project_id; ?>');
        formData.append('csrf_token', '<?php echo $csrf_token; ?>');

        for (var i = 0; i < files.length; i++) {
            formData.append('images[]', files[i]);
        }

        $.ajax({
            url: 'upload_images.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total * 100;
                        $('.progress-bar').width(percentComplete + '%').attr('aria-valuenow', percentComplete).text(percentComplete.toFixed(2) + '%');
                    }
                }, false);
                return xhr;
            },
            beforeSend: function() {
                $('#uploadProgress').show();
            },
            success: function(response) {
                console.log('Server response:', response);
                if (response.success) {
                    response.images.forEach(function(image) {
                        addImageToGallery(image);
                    });
                    Swal.fire({
                        icon: 'success',
                        title: 'อัปโหลดสำเร็จ',
                        text: 'อัปโหลดรูปภาพเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง'
                    }).then(() => {
                        loadImages(); // เพิ่มการเรียกฟังก์ชัน loadImages() หลังการแจ้งเตือนสำเร็จ
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message || 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ',
                        confirmButtonText: 'ตกลง'
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                    confirmButtonText: 'ตกลง'
                });
            },
            complete: function() {
                $('#uploadProgress').hide();
                $('.progress-bar').width('0%').attr('aria-valuenow', 0).text('0%');
            }
        });
    }

    function addImageToGallery(image) {
        var imageHtml = `
    <div class="image-card" data-id="${image.id}">
      <img src="${image.url}" alt="${image.name}" onclick="openLightbox(this.src)">
      <button class="delete-btn" onclick="deleteImage('${image.id}')">×</button>
      <div class="image-info">
        <h5>${image.name}</h5>
        <p>Size: ${formatFileSize(image.size)}<br>Type: ${image.type}</p>
      </div>
    </div>
  `;
        $('#imageGallery').append(imageHtml);
    }

    function openLightbox(imgSrc) {
        $('#lightbox-img').attr('src', imgSrc);
        $('#lightbox').css('display', 'flex');
    }

    // เพิ่มการจัดการคลิกที่ปุ่มปิดและพื้นหลัง
    $('#lightbox .close, #lightbox').click(function() {
        $('#lightbox').hide();
    });

    // ป้องกันการปิด lightbox เมื่อคลิกที่รูปภาพ
    $('#lightbox-img').click(function(e) {
        e.stopPropagation();
    });


    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function deleteImage(imageId) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบรูปภาพนี้ใช่หรือไม่?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_image.php',
                    type: 'POST',
                    data: {
                        csrf_token: '<?php echo $csrf_token; ?>',
                        image_id: imageId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'ลบแล้ว!',
                                'รูปภาพถูกลบเรียบร้อยแล้ว',
                                'success'
                            ).then(() => {
                                loadImages(); // รีโหลดรูปภาพ
                            });
                        } else {
                            Swal.fire(
                                'เกิดข้อผิดพลาด!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'เกิดข้อผิดพลาด!',
                            'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                            'error'
                        );
                    }
                });
            }
        });
    }
</script>
<!-- 4. การอัปโหลดและแสดงรูปภาพ -->

<!-- 5. function สำหรับการพิมพ์ PDF -->
<!-- 5.1  เพิ่ม CSS สำหรับ loading indicator -->
<style>
    #loading {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        text-align: center;
        color: white;
        padding-top: 200px;
    }
</style>
<!-- 5.1  เพิ่ม CSS สำหรับ loading indicator -->

<!-- 5.2 เพิ่ม loading indicator -->
<div id="loading">
    <h3>กำลังสร้าง PDF...</h3>
</div>
<!-- 5.2 เพิ่ม loading indicator -->

<!-- 5.3 ปรับปรุงฟังก์ชัน generatePDF -->
<script>
    function generatePDF() {

        // ซ่อนปุ่มก่อนสร้าง PDF
        const buttons = document.querySelectorAll('.edit-button, .btn-sm');
        buttons.forEach(button => button.style.display = 'none');

        // แสดง loading indicator
        document.getElementById('loading').style.display = 'block';

        // เลือกเฉพาะส่วนที่ต้องการพิมพ์
        const element = document.getElementById('project-info');

        // คลี่ตารางที่ซ่อนอยู่ใน responsive container
        const responsiveTables = element.querySelectorAll('.table-responsive');
        responsiveTables.forEach(container => {
            container.style.overflow = 'visible';
            container.style.maxWidth = 'none';
        });

        // กำหนดตัวเลือกสำหรับ html2pdf
        const opt = {
            margin: [20, 10, 10, 10],
            filename: 'project-details.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2,
                windowWidth: 1200 // กำหนดความกว้างของ viewport
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            },
            pagebreak: {
                mode: ['avoid-all', 'css', 'legacy']
            }
        };

        // สร้าง PDF
        html2pdf().from(element).set(opt).save().then(() => {
            // คืนค่าสไตล์เดิมให้กับตาราง
            responsiveTables.forEach(container => {
                container.style.overflow = '';
                container.style.maxWidth = '';
            });
            // แสดงปุ่มกลับมาหลังสร้าง PDF เสร็จ
            buttons.forEach(button => button.style.display = '');
            document.getElementById('loading').style.display = 'none';
        });
    }
</script>

<!-- 5.3 ปรับปรุงฟังก์ชัน generatePDF -->
<style>
    /* CSS สำหรับการพิมพ์ PDF */
    @media print {

        /* การตั้งค่าพื้นฐานสำหรับ body และ container */
        body {
            font-size: 12pt;
            /* กำหนดขนาดฟอนต์ให้เหมาะสม */
            padding: 0;
            margin: 0;
        }

        .container-fluid {
            width: 100%;
            /* กำหนดให้ container กินพื้นที่เต็มหน้า */
            padding: 0;
            margin: 0;
        }

        .wrapper,
        .content-wrapper {
            background-color: white !important;
            /* กำหนดสีพื้นหลังเป็นสีขาว */
        }

        /* ซ่อนองค์ประกอบที่ไม่ต้องการให้พิมพ์ */
        .nav-pills,
        .card-header p-2,
        .nav.nav-pills,
        .tab-content>.tab-pane:not(.active),
        .nav-item,
        .edit-button,
        .btn-sm,
        .btn-info,
        .btn-danger,
        .btn-group,
        .no-print,
        .main-sidebar,
        .main-header,
        .main-footer {
            display: none !important;
            /* ไม่แสดงปุ่มหรือส่วนที่ไม่จำเป็น */
        }


        /* การจัดรูปแบบของ info-card และ row */
        .info-card {
            page-break-inside: avoid;
            /* ป้องกันไม่ให้ตัดหน้าในขณะพิมพ์ */
            margin-bottom: 20px;
            width: 100%;
            break-inside: avoid;
            /* ป้องกันการตัดหน้า */
        }

        .row {
            display: block;
            page-break-inside: avoid;
            /* ป้องกันการตัดหน้าในขณะพิมพ์ */
            margin-bottom: 20px;
        }

        /* การจัดการโครงสร้างของคอลัมน์และเนื้อหา info-card */
        .col-md-12 {
            width: 100%;
            /* ปรับให้คอลัมน์มีขนาดเต็ม */
            float: none;
        }

        .info-card-body {
            padding: 15px;
            /* กำหนดระยะห่างภายในของการ์ด */
        }

        .info-item {
            margin-bottom: 10px;
            /* กำหนดระยะห่างระหว่างแต่ละรายการ */
        }

        .info-label {
            font-weight: bold;
            /* กำหนดตัวหนาให้กับ label */
            display: inline-block;
            width: 150px;
            /* กำหนดความกว้างของ label */
            vertical-align: top;
        }

        .info-value {
            display: inline-block;
            width: calc(100% - 160px);
            /* กำหนดให้แสดงผลเต็มพื้นที่ */
        }

        /* การจัดการตาราง */
        .table-responsive {
            overflow-x: visible !important;
            /* แก้ไขปัญหาการแสดงผลของตารางใน container */
        }

        .table {
            width: 100% !important;
            /* กำหนดให้ตารางเต็มหน้ากระดาษ */
            table-layout: fixed;
            /* ใช้การจัดรูปแบบตารางให้เท่ากันทุกคอลัมน์ */
            border-collapse: collapse !important;
            /* รวมเส้นขอบของตาราง */
        }

        .table-section {
            page-break-inside: avoid;
            /* ป้องกันไม่ให้ตัดหน้าในขณะพิมพ์ */
        }

        .table th,
        .table td {
            word-wrap: break-word;
            /* แก้ไขปัญหาคำใน cell ยาวเกิน */
            max-width: 100%;
            white-space: normal;
            background-color: #fff !important;
            /* กำหนดสีพื้นหลังของ cell ให้เป็นสีขาว */
        }

        /* การจัดการหัวข้อและรูปภาพ */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            page-break-after: avoid;
            /* ป้องกันการตัดหน้าในหัวข้อ */
        }

        img {
            max-width: 100% !important;
            /* ป้องกันไม่ให้ภาพขยายเกินขอบ */
        }
    }

    /* การปรับแต่งตารางต้นทุนโครงการ (Datatables) */
    .table-responsive {
        margin: 15px 0;
        /* กำหนดระยะห่างรอบตาราง */
    }

    #costTable {
        width: 100% !important;
        /* กำหนดความกว้างของตารางให้เต็ม */
        margin-bottom: 1rem;
    }

    #costTable th,
    #costTable td {
        padding: 8px;
        /* กำหนดระยะห่างภายใน cell */
        vertical-align: middle;
        /* จัดแนวข้อมูลให้อยู่กลาง */
    }

    .dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
        padding: 0 15px;
        /* จัดระยะห่างภายใน wrapper */
    }

    /* สไตล์ปุ่ม DataTables */
    .dt-buttons {
        margin-bottom: 15px;
        float: left;
        /* จัดตำแหน่งปุ่มไปทางซ้าย */
    }

    .dt-button {
        margin-right: 5px !important;
        /* ระยะห่างระหว่างปุ่ม */
    }

    /* การปรับปรุง responsive */
    @media screen and (max-width: 767px) {
        .table-responsive {
            border: none;
            /* ซ่อนเส้นขอบเมื่อลดขนาดจอ */
        }

        .dataTables_wrapper {
            padding: 0;
            /* ลบระยะห่างภายในเมื่อจอเล็ก */
        }
    }

    /* ปรับแต่งปุ่ม Export */
    .buttons-excel {
        color: #fff !important;
        background-color: #28a745 !important;
        /* กำหนดสีพื้นหลังเป็นสีเขียว */
        border-color: #28a745 !important;
        padding: .25rem .5rem !important;
        /* กำหนดระยะห่างภายในปุ่ม */
        font-size: .875rem !important;
        line-height: 1.5 !important;
        border-radius: .2rem !important;
        /* กำหนดขอบโค้งของปุ่ม */
    }
</style>
<!-- 5. function สำหรับการพิมพ์ PDF -->

<!-- 6.  // ฟังก์ชันเพิ่มแถวใหม่ในตารางต้นทุน -->
<script>
    // ฟังก์ชันคำนวณยอดรวม
    document.getElementById('qtyInput').addEventListener('input', calculateTotals);
    document.getElementById('priceInput').addEventListener('input', handleInputWithCommas);
    document.getElementById('costInput').addEventListener('input', handleInputWithCommas);

    // ฟังก์ชันฟอร์แมตตัวเลขพร้อมรักษาตำแหน่ง Cursor
    function handleInputWithCommas(event) {
        const input = event.target;
        let value = input.value;

        // เก็บตำแหน่งของ Cursor ก่อนฟอร์แมต
        const cursorPosition = input.selectionStart;

        // ลบคอมม่าออกจากค่าที่มีอยู่
        value = value.replace(/,/g, '');

        // ตรวจสอบและฟอร์แมตตัวเลขใหม่
        if (!isNaN(value) && value !== '') {
            input.value = formatNumber(value);
        } else {
            input.value = '';
        }

        // คืนตำแหน่ง Cursor กลับ
        input.setSelectionRange(cursorPosition, cursorPosition);
        calculateTotals(); // เรียกฟังก์ชันคำนวณยอดรวมใหม่
    }

    // ฟังก์ชันฟอร์แมตตัวเลขให้มีคอมม่า
    function formatNumber(value) {
        const parts = value.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return parts.join('.');
    }

    // ฟังก์ชันคำนวณยอดรวมในแถวใหม่ที่กรอกข้อมูล
    function calculateTotals() {
        const qty = parseFloat(document.getElementById('qtyInput').value.replace(/,/g, '')) || 0;
        const price = parseFloat(document.getElementById('priceInput').value.replace(/,/g, '')) || 0;
        const cost = parseFloat(document.getElementById('costInput').value.replace(/,/g, '')) || 0;

        const totalAmount = qty * price;
        const totalCost = qty * cost;

        document.getElementById('totalAmountInput').textContent = totalAmount.toLocaleString('th-TH', {
            minimumFractionDigits: 2
        });
        document.getElementById('totalCostInput').textContent = totalCost.toLocaleString('th-TH', {
            minimumFractionDigits: 2
        });
    }

    // ฟังก์ชันสำหรับเพิ่มแถวใหม่ในตาราง
    function addRow() {
        const type = document.getElementById('typeInput').value;
        const partNo = document.getElementById('partNoInput').value;
        const description = document.getElementById('descriptionInput').value;
        const qty = parseFloat(document.getElementById('qtyInput').value.replace(/,/g, '')) || 0;
        const price = parseFloat(document.getElementById('priceInput').value.replace(/,/g, '')) || 0;
        const totalAmount = qty * price;
        const cost = parseFloat(document.getElementById('costInput').value.replace(/,/g, '')) || 0;
        const totalCost = qty * cost;
        const supplier = document.getElementById('supplierInput').value;

        if (type && partNo && description && qty && price && cost && supplier) {
            const table = document.getElementById('costTable').getElementsByTagName('tbody')[0];
            const newRow = table.insertRow(-1); // แทรกก่อนแถวฟอร์มเพิ่ม

            newRow.innerHTML = `
            <tr>
                <td>${type}</td>
                <td>${partNo}</td>
                <td>${description}</td>
                <td>${qty}</td>
                <td>${price.toLocaleString('th-TH', { minimumFractionDigits: 2 })}</td>
                <td>${totalAmount.toLocaleString('th-TH', { minimumFractionDigits: 2 })}</td>
                <td>${cost.toLocaleString('th-TH', { minimumFractionDigits: 2 })}</td>
                <td>${totalCost.toLocaleString('th-TH', { minimumFractionDigits: 2 })}</td>
                <td>${supplier}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="editRow(this)">แก้ไข</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteRow(this)">ลบ</button>
                </td>
            </tr>
        `;

            updateTotals();
            clearFormFields();
        } else {
            alert("กรุณากรอกข้อมูลให้ครบถ้วน");
        }
    }

    // ฟังก์ชันสำหรับแก้ไขแถว
    function editRow(button) {
        const row = button.closest('tr');
        const cells = row.getElementsByTagName('td');

        // นำข้อมูลจากแถวที่เลือกไปยังฟิลด์ฟอร์มเพื่อแก้ไข
        document.getElementById('typeInput').value = cells[0].textContent;
        document.getElementById('partNoInput').value = cells[1].textContent;
        document.getElementById('descriptionInput').value = cells[2].textContent;
        document.getElementById('qtyInput').value = cells[3].textContent;
        document.getElementById('priceInput').value = cells[4].textContent.replace(/,/g, '');
        document.getElementById('costInput').value = cells[6].textContent.replace(/,/g, '');
        document.getElementById('supplierInput').value = cells[8].textContent;

        // ลบแถวปัจจุบันหลังจากแก้ไขเสร็จ
        deleteRow(button);
    }

    // ฟังก์ชันสำหรับลบแถว
    function deleteRow(button) {
        const row = button.closest('tr');
        row.parentNode.removeChild(row);
        updateTotals();
    }

    // ฟังก์ชันสำหรับคำนวณยอดรวม
    function updateTotals() {
        const rows = document.querySelectorAll('#costTable tbody tr:not(:last-child)'); // เว้นแถวสุดท้าย
        let totalAmount = 0;
        let totalCost = 0;

        rows.forEach(row => {
            const amountCell = row.cells[5];
            const costCell = row.cells[7];

            totalAmount += parseFloat(amountCell.textContent.replace(/,/g, '')) || 0;
            totalCost += parseFloat(costCell.textContent.replace(/,/g, '')) || 0;
        });

        document.getElementById('totalAmount').textContent = totalAmount.toLocaleString('th-TH', {
            minimumFractionDigits: 2
        });
        document.getElementById('vatAmount').textContent = (totalAmount * 0.07).toLocaleString('th-TH', {
            minimumFractionDigits: 2
        }); // คำนวณ VAT 7%
        document.getElementById('grandTotal').textContent = (totalAmount + (totalAmount * 0.07)).toLocaleString('th-TH', {
            minimumFractionDigits: 2
        });
    }

    // ฟังก์ชันสำหรับล้างข้อมูลในฟอร์ม
    function clearFormFields() {
        document.getElementById('typeInput').value = '';
        document.getElementById('partNoInput').value = '';
        document.getElementById('descriptionInput').value = '';
        document.getElementById('qtyInput').value = '';
        document.getElementById('priceInput').value = '';
        document.getElementById('costInput').value = '';
        document.getElementById('supplierInput').value = '';
    }


    // เพิ่ม global variables
    let projectId = '<?php echo $project_id; ?>'; // รับค่า project_id จาก PHP

    // เมื่อโหลดหน้าเว็บ
    $(document).ready(function() {
        // โหลดข้อมูลเริ่มต้น
        loadCosts();

        // เพิ่ม event listeners
        $('#qtyInput').on('input', calculateTotals);
        $('#priceInput').on('input', handleInputWithCommas);
        $('#costInput').on('input', handleInputWithCommas);
    });

    // แทนที่ฟังก์ชัน addRow เดิมด้วย saveCost
    function saveCost() {
        if (!validateInputs()) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                text: 'โปรดกรอกข้อมูลที่จำเป็นทุกช่อง',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        const costData = {
            csrf_token: $('input[name="csrf_token"]').val(),
            project_id: projectId,
            type: $('#typeInput').val(),
            part_no: $('#partNoInput').val(),
            description: $('#descriptionInput').val(),
            quantity: parseFloat($('#qtyInput').val()),
            unit: $('#unitInput').val(),
            price_per_unit: parseFormattedNumber($('#priceInput').val()),
            cost_per_unit: parseFormattedNumber($('#costInput').val()),
            supplier: $('#supplierInput').val()
        };

        $.ajax({
            url: 'save_cost.php',
            type: 'POST',
            data: costData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกสำเร็จ',
                        text: 'เพิ่มข้อมูลต้นทุนเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง'
                    }).then(() => {
                        clearFormFields(); // ล้างฟอร์ม
                        loadCosts(); // โหลดข้อมูลใหม่
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message,
                        confirmButtonText: 'ตกลง'
                    });
                }
            }
        });
    }

    // ฟังก์ชันป้องกัน XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // เรียกโหลดข้อมูลเมื่อโหลดหน้า
    $(document).ready(function() {
        loadCosts();
    });

    // เพิ่มฟังก์ชันตรวจสอบการกรอกข้อมูล
    function validateInputs() {
        const required = ['typeInput', 'partNoInput', 'descriptionInput', 'qtyInput', 'unitInput', 'priceInput', 'costInput', 'supplierInput'];
        return required.every(id => $('#' + id).val().trim() !== '');
    }

    // ฟังก์ชันโหลดข้อมูลต้นทุน
    // แทนที่ฟังก์ชัน loadCosts เดิมด้วยโค้ดนี้
    function loadCosts() {
        $.ajax({
            url: 'get_costs.php',
            type: 'GET',
            data: {
                project_id: projectId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // ถ้ามีตาราง DataTable อยู่แล้ว ให้ทำลายก่อน
                    if ($.fn.DataTable.isDataTable('#costTable')) {
                        $('#costTable').DataTable().destroy();
                    }

                    const tbody = $('#costTableBody');
                    tbody.empty(); // ล้างข้อมูลเก่า

                    // เพิ่มข้อมูลใหม่
                    response.costs.forEach(function(cost) {
                        const row = $('<tr>');
                        row.html(`
                        <td>${escapeHtml(cost.type)}</td>
                        <td>${escapeHtml(cost.part_no)}</td>
                        <td>${escapeHtml(cost.description)}</td>
                        <td>${cost.quantity}</td>
                        <td>${escapeHtml(cost.unit)}</td>
                        <td>${formatNumber(cost.price_per_unit)}</td>
                        <td>${formatNumber(cost.total_amount)}</td>
                        <td>${formatNumber(cost.cost_per_unit)}</td>
                        <td>${formatNumber(cost.total_cost)}</td>
                        <td>${escapeHtml(cost.supplier)}</td>
                        <td>
                            <button class="btn btn-sm btn-info mr-1" onclick="editCost('${cost.cost_id}')">แก้ไข</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteCost('${cost.cost_id}')">ลบ</button>
                        </td>
                    `);
                        tbody.append(row);
                    });

                    // สร้าง DataTable พร้อมปุ่ม Export
                    $('#costTable').DataTable({
                        dom: 'Bfrtip',
                        buttons: [{
                                extend: 'excel',
                                text: '<i class="fas fa-file-excel"></i> Export Excel',
                                className: 'btn btn-success btn-sm',
                                title: 'Project Cost Report',
                                filename: 'Project_Costs_' + new Date().toISOString().slice(0, 10),
                                customize: function(xlsx) {
                                    var sheet = xlsx.xl.worksheets['sheet1.xml'];

                                    // เพิ่มข้อมูลสรุป
                                    var summaryData = [
                                        ['Summary'],
                                        ['Total Amount:', $('#totalAmount').text()],
                                        ['VAT Amount:', $('#vatAmount').text()],
                                        ['Grand Total:', $('#grandTotal').text()],
                                        ['Total Cost:', $('#totalCost').text()],
                                        ['Cost VAT Amount:', $('#costVatAmount').text()],
                                        ['Total Cost with VAT:', $('#totalCostWithVat').text()],
                                        ['Profit Amount:', $('#profitAmount').text()],
                                        ['Profit Percentage:', $('#profitPercentage').text()]
                                    ];

                                    // คำนวณตำแหน่งแถวสุดท้าย
                                    var lastRow = $('row', sheet).length;

                                    // เพิ่มข้อมูลสรุป
                                    summaryData.forEach(function(data) {
                                        lastRow++;
                                        var row = sheet.createElement('row');

                                        data.forEach(function(text, index) {
                                            var cell = sheet.createElement('c');
                                            var t = sheet.createElement('t');
                                            t.textContent = text;
                                            cell.appendChild(t);
                                            if (index === 0) {
                                                cell.setAttribute('s', '2'); // style สำหรับหัวข้อ
                                            }
                                            row.appendChild(cell);
                                        });

                                        sheet.getElementsByTagName('sheetData')[0].appendChild(row);
                                    });
                                },
                                exportOptions: {
                                    columns: ':not(:last-child)' // ไม่รวมคอลัมน์ Actions
                                }
                            },
                            // *** ปุ่ม Print เพิ่มเข้ามาใหม่ ***
                            {
                                text: '<i class="fas fa-print"></i> Print',
                                className: 'btn btn-primary btn-sm',
                                action: function(e, dt, node, config) {
                                    // เปิดหน้าต่างใหม่ cost_viewprint.php
                                    // พร้อมส่ง project_id ที่เข้ารหัสอย่างปลอดภัย
                                    window.open(
                                        'cost_viewprint.php?project_id=<?php echo urlencode(encryptUserId($project_id)); ?>',
                                        '_blank'
                                    );
                                }
                            }
                        ],
                        pageLength: 10,
                        responsive: true,
                        ordering: true,
                        searching: true,
                        columnDefs: [{
                            targets: -1, // คอลัมน์สุดท้าย (Actions)
                            orderable: false
                        }],
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json'
                        }
                    });

                    // อัพเดทข้อมูลสรุป
                    if (response.summary) {
                        updateSummaryDisplay(response.summary);
                    }
                }
            }
        });
    }


    $(document).ready(function() {
        loadCosts(); // โหลดข้อมูลและสร้าง DataTable เมื่อหน้าเว็บโหลดเสร็จ

        // เพิ่ม event listeners
        $('#qtyInput').on('input', calculateTotals);
        $('#priceInput').on('input', handleInputWithCommas);
        $('#costInput').on('input', handleInputWithCommas);
    });

    // ฟังก์ชันอัพเดทยอดรวม
    // function updateSummary(summary) {
    //     $('#totalAmount').text(formatNumber(summary.total_amount) + ' บาท');
    //     $('#vatAmount').text(formatNumber(summary.vat_amount) + ' บาท');
    //     $('#grandTotal').text(formatNumber(summary.grand_total) + ' บาท');
    //     $('#totalCost').text(formatNumber(summary.total_cost) + ' บาท');
    //     $('#costVatAmount').text(formatNumber(summary.cost_vat_amount) + ' บาท');
    //     $('#totalCostWithVat').text(formatNumber(summary.total_cost_with_vat) + ' บาท');
    //     $('#profitAmount').text(formatNumber(summary.profit_amount) + ' บาท');
    //     $('#profitPercentage').text(formatNumber(summary.profit_percentage) + '%');
    // }

    // ฟังก์ชันอัพเดทการแสดงผลสรุป
    function updateSummaryDisplay(summary) {
        $('#totalAmount').text(formatNumber(summary.total_amount));
        $('#vatAmount').text(formatNumber(summary.vat_amount));
        $('#grandTotal').text(formatNumber(summary.grand_total));
        $('#totalCost').text(formatNumber(summary.total_cost));
        $('#costVatAmount').text(formatNumber(summary.cost_vat_amount));
        $('#totalCostWithVat').text(formatNumber(summary.total_cost_with_vat));
        $('#profitAmount').text(formatNumber(summary.profit_amount));
        $('#profitPercentage').text(formatNumber(summary.profit_percentage));
    }

    // ฟังก์ชันแก้ไขข้อมูล
    function editCost(costId) {
        $.ajax({
            url: 'get_cost_details.php',
            type: 'GET',
            data: {
                cost_id: costId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const cost = response.cost;
                    // นำข้อมูลไปแสดงในฟอร์ม
                    $('#typeInput').val(cost.type);
                    $('#partNoInput').val(cost.part_no);
                    $('#descriptionInput').val(cost.description);
                    $('#qtyInput').val(cost.quantity);
                    $('#unitInput').val(cost.unit);
                    $('#priceInput').val(formatNumber(cost.price_per_unit));
                    $('#costInput').val(formatNumber(cost.cost_per_unit));
                    $('#supplierInput').val(cost.supplier);

                    // เปลี่ยนปุ่มบันทึกเป็นปุ่มอัพเดท
                    const saveButton = $('button[onclick="saveCost()"]');
                    saveButton.text('อัพเดท');
                    saveButton.attr('onclick', `updateCost('${costId}')`);

                    // เลื่อนไปที่ฟอร์ม
                    $('html, body').animate({
                        scrollTop: $('#costForm').offset().top
                    }, 500);
                }
            }
        });
    }

    // ฟังก์ชันอัพเดทข้อมูล
    function updateCost(costId) {
        if (!validateInputs()) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                text: 'โปรดกรอกข้อมูลที่จำเป็นทุกช่อง',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        const costData = {
            csrf_token: $('input[name="csrf_token"]').val(),
            cost_id: costId,
            project_id: projectId,
            type: $('#typeInput').val(),
            part_no: $('#partNoInput').val(),
            description: $('#descriptionInput').val(),
            quantity: parseFloat($('#qtyInput').val()),
            unit: $('#unitInput').val(),
            price_per_unit: parseFormattedNumber($('#priceInput').val()),
            cost_per_unit: parseFormattedNumber($('#costInput').val()),
            supplier: $('#supplierInput').val()
        };

        $.ajax({
            url: 'edit_cost.php',
            type: 'POST',
            data: costData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'อัพเดทสำเร็จ',
                        text: 'แก้ไขข้อมูลต้นทุนเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง'
                    }).then(() => {
                        // รีเซ็ตฟอร์มและปุ่มบันทึก
                        clearFormFields();
                        loadCosts();
                        const saveButton = $('button[onclick*="updateCost"]');
                        saveButton.text('เพิ่ม');
                        saveButton.attr('onclick', 'saveCost()');
                        loadCosts();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message,
                        confirmButtonText: 'ตกลง'
                    });
                }
            }
        });
    }

    // ฟังก์ชันลบข้อมูล
    function deleteCost(costId) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบข้อมูลต้นทุนนี้ใช่หรือไม่?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_cost.php',
                    type: 'POST',
                    data: {
                        csrf_token: $('input[name="csrf_token"]').val(),
                        cost_id: costId,
                        project_id: projectId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ',
                                text: 'ลบข้อมูลต้นทุนเรียบร้อยแล้ว',
                                confirmButtonText: 'ตกลง'
                            }).then(() => {
                                loadCosts(); // โหลดข้อมูลและอัพเดทสรุปใหม่
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: response.message,
                                confirmButtonText: 'ตกลง'
                            });
                        }
                    }
                });
            }
        });
    }
</script>
<!-- 6.  // ฟังก์ชันเพิ่มแถวใหม่ในตารางต้นทุน -->

<!-- 7 Modal สำหรับเพิ่ม/แก้ไขลิงก์เอกสาร -->
<div class="modal fade" id="linkModal" tabindex="-1" role="dialog" aria-labelledby="linkModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="linkModalLabel">เพิ่มลิงก์เอกสาร</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="linkForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" id="linkId">
                    <div class="form-group">
                        <label for="documentCategory">หมวด/หมู่เอกสาร<span class="text-danger">*</span></label>
                        <select class="form-control" id="documentCategory" required>
                            <option value="">เลือกหมวดหมู่</option>
                            <option value="contract">สัญญา</option>
                            <option value="proposal">หนังสือค่ำประกันสัญญา</option>
                            <option value="proposal">ข้อเสนอโครงการ</option>
                            <option value="report">รายงาน</option>
                            <option value="specification">ข้อกำหนด</option>
                            <option value="other">อื่นๆ</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="documentNames">ชื่อเอกสาร<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="documentNames" required>
                    </div>
                    <div class="form-group">
                        <label for="documentLink">ลิงก์เอกสาร<span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="documentLink" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="saveDocumentLink()">บันทึก</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ฟังก์ชันสำหรับโหลดข้อมูลลิงก์เอกสาร
    function loadDocumentLinks() {
        if ($.fn.DataTable.isDataTable('#example2')) {
            $('#example2').DataTable().destroy();
        }

        // โหลดข้อมูลลิงก์เอกสารที่ต้องการใส่ในตาราง
        $.ajax({
            url: 'get_document_links.php',
            type: 'GET',
            data: {
                project_id: projectId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const tbody = $('#linkTableBody');
                    tbody.empty();

                    response.links.forEach((link, index) => {
                        const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${getCategoryName(link.category)}</td>
                        <td><a href="${link.url}" target="_blank">${link.document_name}</a></td>
                        <td>${formatDate(link.created_at)}</td>
                        <td>${link.created_by_name}</td>
                        <td>
                            <button class="btn btn-sm btn-info mr-1" onclick="editDocumentLink('${link.id}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteDocumentLink('${link.id}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    `;
                        tbody.append(row);
                    });

                    // สร้าง DataTable ใหม่
                    $("#example2").DataTable({
                        "dom": 'Bfrtip',
                        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                        "responsive": true,
                        "lengthChange": true,
                        "autoWidth": false,
                        "order": [],
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json"
                        }
                    }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
                }
            }
        });
    }


    // ฟังก์ชันแปลงหมวดหมู่เป็นภาษาไทย
    function getCategoryName(category) {
        const categories = {
            'contract': 'สัญญา',
            'proposal': 'ข้อเสนอโครงการ',
            'report': 'รายงาน',
            'specification': 'ข้อกำหนด',
            'other': 'อื่นๆ'
        };
        return categories[category] || category;
    }

    // ฟังก์ชันบันทึกลิงก์เอกสาร
    function saveDocumentLink() {
        const linkData = {
            csrf_token: $('input[name="csrf_token"]').val(),
            project_id: projectId,
            link_id: $('#linkId').val(),
            category: $('#documentCategory').val(),
            document_name: $('#documentNames').val(),
            url: $('#documentLink').val()
        };

        $.ajax({
            url: 'save_document_link.php',
            type: 'POST',
            data: linkData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'บันทึกลิงก์เอกสารเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง'
                    }).then(() => {
                        $('#linkModal').modal('hide');
                        loadDocumentLinks();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message,
                        confirmButtonText: 'ตกลง'
                    });
                }
            }
        });
    }

    // ฟังก์ชันแก้ไขลิงก์เอกสาร
    function editDocumentLink(linkId) {
        $.ajax({
            url: 'get_document_link_details.php',
            type: 'GET',
            data: {
                link_id: linkId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#linkId').val(linkId);
                    $('#documentCategory').val(response.link.category);
                    $('#documentNames').val(response.link.document_name);
                    $('#documentLink').val(response.link.url);
                    $('#linkModalLabel').text('แก้ไขลิงก์เอกสาร');
                    $('#linkModal').modal('show');
                }
            }
        });
    }

    // ฟังก์ชันลบลิงก์เอกสาร
    function deleteDocumentLink(linkId) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบลิงก์เอกสารนี้ใช่หรือไม่?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_document_link.php',
                    type: 'POST',
                    data: {
                        csrf_token: $('input[name="csrf_token"]').val(),
                        link_id: linkId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ',
                                text: 'ลบลิงก์เอกสารเรียบร้อยแล้ว',
                                confirmButtonText: 'ตกลง'
                            }).then(() => {
                                loadDocumentLinks();
                            });
                        }
                    }
                });
            }
        });
    }

    // เพิ่ม event listeners
    $(document).ready(function() {
        // โหลดข้อมูลลิงก์เมื่อเปิดแท็บ links
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            if (e.target.hash === '#links') {
                loadDocumentLinks();
            }
        });

        // เพิ่มการเรียกใช้ฟังก์ชันนี้เมื่อโหลดหน้าเว็บเพื่อให้ข้อมูลไม่หายหลังจากรีเฟรช
        loadDocumentLinks();

        // รีเซ็ตฟอร์มเมื่อปิด Modal
        $('#linkModal').on('hidden.bs.modal', function() {
            $('#linkForm').trigger('reset');
            $('#linkId').val('');
            $('#linkModalLabel').text('เพิ่มลิงก์เอกสาร');
        });
    });

    // เพิ่มฟังก์ชันนี้ในส่วน JavaScript
    function formatDate(dateString) {
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('th-TH', options);
    }
</script>

<style>
    /* ปรับสีปุ่ม Excel ให้อยู่ในโทนเดียวกับปุ่มอื่น ๆ */
    .buttons-excel {
        background-color: #007bff !important;
        /* เปลี่ยนสีปุ่มเป็นสีน้ำเงิน */
        border-color: #007bff !important;
        /* เปลี่ยนสีขอบปุ่มให้เข้ากัน */
        color: #ffffff !important;
        /* เปลี่ยนสีตัวอักษรเป็นสีขาว */
    }
</style>
<!-- 7 Modal สำหรับเพิ่ม/แก้ไขลิงก์เอกสาร -->