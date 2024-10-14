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
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "project"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | View Project</title>
    <?php include '../../include/header.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* การกำหนดรูปแบบการแสดงผลของหน้าเว็บ */
        body {
            font-family: 'Noto Sans Thai', sans-serif;
            background-color: #f8f9fa;
            font-size: 14px;
        }

        .content-wrapper {
            padding: 20px;
        }

        .project-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .project-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .project-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .project-date {
            font-size: 14px;
            margin-top: 10px;
        }

        .info-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .info-card-header {
            background-color: #f8f9fa;
            color: black;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 18px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .info-card-body {
            padding: 20px;
        }

        .info-item {
            display: flex;
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: 600;
            color: #34495e;
            width: 150px;
            flex-shrink: 0;
        }

        .info-value {
            flex-grow: 1;
            color: #2c3e50;
        }

        /* สไตล์สำหรับส่วนสรุปทางการเงิน */
        .financial-summary {
            background-color: #ecf0f1;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .financial-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .financial-label {
            font-weight: 600;
            color: #34495e;
        }

        .financial-value {
            font-weight: 700;
            color: #2c3e50;
        }

        .profit-highlight {
            color: #27ae60;
            font-size: 18px;
        }

        /* สไตล์ปุ่มแก้ไข */
        .edit-button {
            float: right;
            background-color: transparent;
            color: #6a11cb;
            border: none;
            padding: 2px 5px;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            font-size: 14px;
        }

        .edit-button:hover {
            background-color: #6a11cb;
            color: white;
        }

        /* สไตล์สำหรับตาราง */
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* สไตล์สำหรับสถานะการชำระเงิน */
        .text-success {
            color: #28a745 !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        /* สไตล์สำหรับปุ่มขนาดเล็ก */
        .btn-sm {
            padding: .25rem .5rem;
            font-size: .875rem;
            line-height: 1.5;
            border-radius: .2rem;
        }

        .mr-1 {
            margin-right: .25rem !important;
        }

        /* สไตล์สำหรับการแสดงผลบนอุปกรณ์มือถือ */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
        }

        @media (max-width: 767px) {
            .payment-card .card {
                border: 1px solid #ddd;
                border-radius: 8px;
            }

            .payment-card .card-body {
                padding: 15px;
            }

            .payment-card .card-title {
                font-size: 18px;
                margin-bottom: 10px;
            }

            .payment-card .card-text {
                font-size: 14px;
                margin-bottom: 15px;
            }

            .payment-card .btn-group {
                display: flex;
                justify-content: space-between;
            }
        }

        /* สไตล์สำหรับการจัดการความสูงของการ์ด */
        .equal-height-cards {
            display: flex;
            flex-wrap: wrap;
        }

        .equal-height-cards>[class*='col-'] {
            display: flex;
            flex-direction: column;
        }

        .equal-height-cards .info-card {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .equal-height-cards .info-card-body {
            flex: 1;
        }

        .info-card {
            min-height: 300px;
            /* ปรับตามความเหมาะสม */
        }

        /* ตั้งค่าซ่อนปุ่มการพิมพ์ */
        @media print {

            .edit-button,
            .btn-sm,
            .btn-info,
            .btn-danger,
            .btn-group,
            .no-print {
                display: none !important;
            }

            .wrapper {
                min-height: initial !important;
                background-color: white !important;
            }

            .content-wrapper {
                margin-left: 0 !important;
                background-color: white !important;
            }

            .main-sidebar {
                display: none !important;
            }

            .main-header {
                display: none !important;
            }

            .main-footer {
                display: none !important;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .container-fluid {
                width: 100%;
                padding: 0;
                margin: 0;
            }

            .info-card {
                break-inside: avoid;
            }
        }

        /* ตั้งค่าให้การพิมพ์มีความสวยงามมากขึ้น */
        @media print {
            body {
                font-size: 12pt;
            }

            .info-card {
                page-break-inside: avoid;
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                page-break-after: avoid;
            }

            img {
                max-width: 100% !important;
            }

            .table {
                border-collapse: collapse !important;
            }

            .table td,
            .table th {
                background-color: #fff !important;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>
        <div class="content-wrapper">

            <!-- เพิ่มส่วนนี้หลังจาก project-header -->
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#project-info" data-toggle="tab">ข้อมูลโครงการ</a></li>
                        <li class="nav-item"><a class="nav-link" href="#documents" data-toggle="tab">เอกสารแนบ</a></li>
                        <li class="nav-item"><a class="nav-link" href="#images" data-toggle="tab">รูปภาพ</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
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

                                    <!-- ข้อมูลลูกค้า -->
                                    <div class="row equal-height-cards">
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-card-header">
                                                    <span><i class="fas fa-user mr-2"></i>ข้อมูลลูกค้า</span>
                                                </div>
                                                <div class="info-card-body">
                                                    <div class="info-item">
                                                        <span class="info-label">ชื่อลูกค้า:</span>
                                                        <span class="info-value"><?php echo htmlspecialchars($project['customer_name']); ?></span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">บริษัท:</span>
                                                        <span class="info-value"><?php echo htmlspecialchars($project['company']); ?></span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">ที่อยู่:</span>
                                                        <span class="info-value"><?php echo htmlspecialchars($project['address']); ?></span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">โทรศัพท์:</span>
                                                        <span class="info-value"><?php echo htmlspecialchars($project['customer_phone']); ?></span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">อีเมล:</span>
                                                        <span class="info-value"><?php echo htmlspecialchars($project['customer_email']); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- ข้อมูลผู้ขาย -->
                                        <div class="col-md-6">
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
                                        <div class="info-card-header">
                                            <span><i class="fas fa-info-circle mr-2"></i>ข้อมูลการชำระเงิน</span>
                                            <button class="edit-button btn-sm" onclick="openAddPaymentModal()">
                                                <i class="fas fa-plus"></i> เพิ่ม
                                            </button>
                                        </div>
                                        <div class="info-card-body">
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
                                                <?php
                                                $total_amount = array_sum(array_column($payments, 'amount'));
                                                $total_paid = array_sum(array_column($payments, 'amount_paid'));
                                                $remaining = $total_amount - $total_paid;
                                                ?>
                                                <p>จำนวนเงินทั้งหมด: <?php echo number_format($total_amount, 2); ?> บาท</p>
                                                <p>จำนวนเงินที่ชำระแล้ว: <?php echo number_format($total_paid, 2); ?> บาท</p>
                                                <p>จำนวนเงินคงเหลือ: <?php echo number_format($remaining, 2); ?> บาท</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="tab-pane" id="documents">
                            <!-- ตารางแสดงเอกสารแนบ -->
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ชื่อเอกสาร</th>
                                        <th>ประเภท</th>
                                        <th>วันที่อัปโหลด</th>
                                        <th>การดำเนินการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- ตัวอย่างข้อมูล (คุณต้องแทนที่ด้วยข้อมูลจริงจากฐานข้อมูล) -->
                                    <tr>
                                        <td>สัญญาโครงการ.pdf</td>
                                        <td>PDF</td>
                                        <td>2023-05-15</td>
                                        <td>
                                            <button class="btn btn-sm btn-info">ดู</button>
                                            <button class="btn btn-sm btn-danger">ลบ</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="images">
                            <!-- แสดงรูปภาพ -->
                            <div class="row">
                                <!-- ตัวอย่างการแสดงรูปภาพ (คุณต้องแทนที่ด้วยข้อมูลจริงจากฐานข้อมูล) -->
                                <div class="col-md-4">
                                    <img src="path/to/image1.jpg" class="img-fluid mb-3" alt="Project Image">
                                </div>
                                <div class="col-md-4">
                                    <img src="path/to/image2.jpg" class="img-fluid mb-3" alt="Project Image">
                                </div>
                                <div class="col-md-4">
                                    <img src="path/to/image3.jpg" class="img-fluid mb-3" alt="Project Image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <?php include '../../include/footer.php'; ?>
    </div>
</body>

</html>

<!-- Modal สำหรับเพิ่ม/แก้ไขการชำระเงิน -->
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
    let totalSaleAmount = <?php echo $project['sale_vat']; ?>; // ราคาขาย (รวมภาษี)

    // ฟังก์ชันสำหรับฟอร์แมตตัวเลขให้มีคอมม่าและทศนิยม 2 ตำแหน่ง
    function formatNumber(num) {
        return parseInt(num).toLocaleString('th-TH');
    }

    // ฟังก์ชันสำหรับแปลงข้อความที่มีคอมม่าเป็นตัวเลข
    function parseFormattedNumber(str) {
        return parseFloat(str.replace(/,/g, '')) || 0;
    }
    // ฟังก์ชันสำหรับคำนวณจำนวนเงินจากเปอร์เซ็นต์
    function calculateAmountFromPercentage() {
        const percentage = parseFloat(document.getElementById('paymentPercentage').value) || 0;
        const amount = (percentage / 100) * totalSaleAmount;
        document.getElementById('amount').value = formatNumber(amount);
        updateAmountPaid();
    }


    // ฟังก์ชันสำหรับคำนวณเปอร์เซ็นต์จากจำนวนเงิน
    function calculatePercentageFromAmount() {
        const amount = parseFormattedNumber(document.getElementById('amount').value);
        const percentage = (amount / totalSaleAmount) * 100;
        document.getElementById('paymentPercentage').value = percentage.toFixed(2);
        updateAmountPaid();
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
            document.getElementById('paymentPercentage').value = ((payment.amount / totalSaleAmount) * 100).toFixed(2);
            document.getElementById('amount').value = formatNumber(payment.amount);
            document.getElementById('dueDate').value = payment.due_date;
            document.getElementById('status').value = payment.status;
            document.getElementById('paymentDate').value = payment.payment_date || '';
            document.getElementById('amountPaid').value = formatNumber(payment.amount_paid);
            $('#paymentModal').modal('show');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'ไม่พบข้อมูล',
                text: 'ไม่พบข้อมูลการชำระเงินที่ต้องการแก้ไข',
                confirmButtonText: 'ตกลง'
            });
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
            payment_percentage: parseFloat(document.getElementById('paymentPercentage').value),
            due_date: document.getElementById('dueDate').value,
            status: document.getElementById('status').value,
            payment_date: document.getElementById('paymentDate').value,
            amount_paid: parseFormattedNumber(document.getElementById('amountPaid').value)
        };

        // คำนวณเปอร์เซ็นต์รวมของการชำระเงินทั้งหมด
        let totalPercentage = payments.reduce((total, payment) => {
            return total + parseFloat(payment.payment_percentage);
        }, 0);

        // เพิ่มเปอร์เซ็นต์ของการชำระเงินใหม่
        totalPercentage += parseFloat(paymentData.payment_percentage);

        if (totalPercentage > 100) {
            Swal.fire({
                icon: 'warning',
                title: 'เกินขีดจำกัด',
                text: 'เปอร์เซ็นต์รวมของการชำระเงินเกิน 100% ของราคาขาย',
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
                        console.log('Raw response:', response);
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
                        console.log('Response Text:', jqXHR.responseText);
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
    document.getElementById('paymentPercentage').addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9.]/g, '');
        e.target.value = value;
        calculateAmountFromPercentage();
    });

    document.getElementById('amount').addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9.]/g, '');
        e.target.value = formatNumber(value);
        calculatePercentageFromAmount();
    });

    document.getElementById('status').addEventListener('change', updateAmountPaid);
</script>