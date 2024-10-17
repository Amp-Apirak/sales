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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalePipeline | View Project</title>
    <?php include '../../include/header.php'; ?>
    <!-- PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <!-- PDF -->
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

        /* เพิ่ม CSS เพื่อสร้างเส้นใต้ให้กับ Tab */
        .nav-pills {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
            /* margin-bottom: 20px; */
        }

        .nav-pills .nav-link {
            border-bottom: 3px solid transparent;
        }

        .nav-pills .nav-link.active {
            background-color: transparent;
            border-bottom: 3px solid #007bff;
            color: #007bff;
        }

        /* .tab-content {
            padding-top: 20px;
        } */

        .btn-sm {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }

        /* สไตล์สำหรับการจัดการความกว้างของการ์ดรูปภาพ Tab 3 */
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .image-card {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .image-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .image-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .image-info {
            padding: 10px;
            background: rgba(255, 255, 255, 0.9);
        }

        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 0, 0, 0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 16px;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .image-card:hover .delete-btn {
            opacity: 1;
        }

        .lightbox {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
        }

        .lightbox-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
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
                        <li class="nav-item"><a class="nav-link active" href="#project-info" data-toggle="tab" data-tab="project-info">ข้อมูลโครงการ</a></li>
                        <li class="nav-item"><a class="nav-link" href="#documents" data-toggle="tab" data-tab="documents">เอกสารแนบ</a></li>
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

                                        <!-- ข้อมูลลูกค้า -->
                                        <div class="row equal-height-cards">
                                            <div class="col-md-12">
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
                            <!-- แถบที่ 2 ตารางแสดงไฟล์เอกสาร -->
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
                            <!-- แถบที่ 3 ตารางแสดงภาพ -->
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
    let totalSaleAmount = <?php echo $project['sale_no_vat']; ?>; // ราคาขาย (รวมภาษี)

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
        const percentage = parseFormattedNumber(document.getElementById('paymentPercentage').value);
        const amount = (percentage / 100) * totalSaleAmount;
        document.getElementById('amount').value = formatNumber(amount);
        updateAmountPaid();
    }

    // ฟังก์ชันสำหรับคำนวณเปอร์เซ็นต์จากจำนวนเงิน
    function calculatePercentageFromAmount() {
        const amount = parseFormattedNumber(document.getElementById('amount').value);
        const percentage = (amount / totalSaleAmount) * 100;
        document.getElementById('paymentPercentage').value = formatNumber(percentage);
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
            document.getElementById('paymentPercentage').value = formatNumber((payment.amount / totalSaleAmount) * 100);
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
            payment_percentage: parseFormattedNumber(document.getElementById('paymentPercentage').value),
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
    });
</script>
<!-- Modal สำหรับเพิ่ม/แก้ไขการชำระเงิน -->


<!-- Modal สำหรับอัปโหลดเอกสาร -->
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
<!-- Modal สำหรับอัปโหลดเอกสาร -->

<!-- Active Tab -->
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
<!-- Active Tab -->

<!-- การอัปโหลดและแสดงรูปภาพ -->
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
<!-- การอัปโหลดและแสดงรูปภาพ -->

<!-- function สำหรับการพิมพ์ PDF -->
<!-- เพิ่ม CSS สำหรับ loading indicator -->
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

<!-- เพิ่ม loading indicator -->
<div id="loading">
    <h3>กำลังสร้าง PDF...</h3>
</div>

<!-- ปรับปรุงฟังก์ชัน generatePDF -->
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

<style>
    /* CSS สำหรับ PDF */
    @media print {
        body {
            font-size: 12pt;
        }

        .info-card {
            page-break-inside: avoid;
            margin-bottom: 20px;
            width: 100%;
        }

        .row {
            display: block;
            page-break-inside: avoid;
            margin-bottom: 20px;
        }

        .col-md-12 {
            width: 100%;
            float: none;
        }

        .info-card-body {
            padding: 15px;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
            vertical-align: top;
        }

        .info-value {
            display: inline-block;
            width: calc(100% - 160px);
        }

        .table-responsive {
            overflow-x: visible !important;
        }

        .table {
            width: 100% !important;
            table-layout: fixed;
        }

        .table th,
        .table td {
            word-wrap: break-word;
            max-width: 100%;
            white-space: normal;
        }

        .table-section {
            page-break-inside: avoid;
        }

    }
</style>

<!-- function สำหรับการพิมพ์ PDF -->