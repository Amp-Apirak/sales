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

// รับ project_id จาก URL และทำการถอดรหัส
$project_id = decryptUserId($_GET['project_id']);

// ดึงข้อมูลโครงการที่ต้องการแสดงจากฐานข้อมูล
try {
    $sql = "SELECT p.*, 
    u.first_name, u.last_name, u.email as seller_email, u.phone as seller_phone,
    pr.product_name, pr.product_description,
    c.customer_name, c.company, c.address, c.phone as customer_phone, c.email as customer_email,
    t.team_name,
    creator.first_name as creator_first_name, creator.last_name as creator_last_name,
    updater.first_name as updater_first_name, updater.last_name as updater_last_name
    FROM projects p 
    LEFT JOIN users u ON p.seller = u.user_id 
    LEFT JOIN products pr ON p.product_id = pr.product_id 
    LEFT JOIN customers c ON p.customer_id = c.customer_id 
    LEFT JOIN teams t ON u.team_id = t.team_id 
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
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
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
        body {
            font-family: 'Noto Sans Thai', sans-serif;
            background-color: #f4f6f9;
            font-size: 14px;
        }

        .content-wrapper {
            padding: 20px;
        }

        .project-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .project-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .project-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .project-date {
            font-size: 12px;
            margin-top: 5px;
        }

        .info-card {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .info-card-header {
            background-color: #f1f3f5;
            color: #333;
            padding: 10px 15px;
            font-weight: 600;
            font-size: 16px;
            border-bottom: 1px solid #dee2e6;
            border-radius: 5px 5px 0 0;
        }

        .info-card-body {
            padding: 15px;
        }

        .info-item {
            display: flex;
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            width: 140px;
            flex-shrink: 0;
        }

        .info-value {
            flex-grow: 1;
        }

        .financial-summary {
            background-color: #e9ecef;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
        }

        .financial-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .financial-label {
            font-weight: 600;
        }

        .financial-value {
            font-weight: 700;
        }

        .profit-highlight {
            color: #28a745;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="project-header">
                        <div class="project-title">Project Gamma</div>
                        <span class="project-status">On Hold</span>
                        <div class="project-date"><i class="far fa-calendar-alt mr-2"></i>2023-03-05 - 2023-04-10</div>
                    </div>

                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="fas fa-info-circle mr-2"></i>ข้อมูลโครงการ
                        </div>
                        <div class="info-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <span class="info-label">เลขที่สัญญา:</span>
                                        <span class="info-value">CN003</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">สินค้า:</span>
                                        <span class="info-value">Product C</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">รายละเอียดสินค้า:</span>
                                        <span class="info-value">This is a description for Product C.</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">วันที่สร้าง:</span>
                                        <span class="info-value">2024-09-22 12:54:27</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <span class="info-label">ผู้สร้าง:</span>
                                        <span class="info-value">Apirak Bangpuk</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">วันที่แก้ไขล่าสุด:</span>
                                        <span class="info-value">2024-09-25 11:26:36</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">ผู้แก้ไขล่าสุด:</span>
                                        <span class="info-value">Apirak Bangpuk</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-card-header">
                                    <i class="fas fa-user mr-2"></i>ข้อมูลลูกค้า
                                </div>
                                <div class="info-card-body">
                                    <div class="info-item">
                                        <span class="info-label">ชื่อลูกค้า:</span>
                                        <span class="info-value">Michael Brown</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">บริษัท:</span>
                                        <span class="info-value">Design Solutions</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">ที่อยู่:</span>
                                        <span class="info-value">789 Oak St, City C</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">โทรศัพท์:</span>
                                        <span class="info-value">555-7890</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">อีเมล:</span>
                                        <span class="info-value">michael.brown@design.com</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-card-header">
                                    <i class="fas fa-user-tie mr-2"></i>ข้อมูลผู้ขาย
                                </div>
                                <div class="info-card-body">
                                    <div class="info-item">
                                        <span class="info-label">ชื่อผู้ขาย:</span>
                                        <span class="info-value">Apirak Bangpuk</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">อีเมล:</span>
                                        <span class="info-value">apirak.ba@gmail.com</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">โทรศัพท์:</span>
                                        <span class="info-value">-</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">ทีม:</span>
                                        <span class="info-value">Innovation</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">หัวหน้าทีมฝ่ายขาย:</span>
                                        <span class="info-value">Apirak Bangpuk</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="fas fa-chart-bar mr-2"></i>ข้อมูลทางการเงิน
                        </div>
                        <div class="info-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <span class="info-label">ราคาขาย (รวมภาษี):</span>
                                        <span class="info-value">190,000.00 บาท</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">ราคาขาย (ไม่รวมภาษี):</span>
                                        <span class="info-value">10,000.00 บาท</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <span class="info-label">ต้นทุน (รวมภาษี):</span>
                                        <span class="info-value">160,000.00 บาท</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">ต้นทุน (ไม่รวมภาษี):</span>
                                        <span class="info-value">30,000.00 บาท</span>
                                    </div>
                                </div>
                            </div>
                            <div class="financial-summary">
                                <div class="financial-item">
                                    <span class="financial-label">กำไรขั้นต้น:</span>
                                    <span class="financial-value profit-highlight">30,000.00 บาท</span>
                                </div>
                                <div class="financial-item">
                                    <span class="financial-label">กำไรขั้นต้น (%):</span>
                                    <span class="financial-value profit-highlight">80.00%</span>
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