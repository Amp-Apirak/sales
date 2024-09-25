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
            background-color: #ffffff;
        }

        .project-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .project-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .project-details {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .project-detail-item {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
        }

        .section-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .section-header {
            background-color: #f8f9fa;
            color: #333;
            padding: 12px 15px;
            font-weight: 600;
            font-size: 16px;
            border-bottom: 1px solid #dee2e6;
        }

        .section-body {
            padding: 15px;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            min-width: 150px;
            display: inline-block;
        }

        .financial-summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            color: #333;
            margin-top: 15px;
            border: 1px solid #dee2e6;
        }

        .financial-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .icon-circle {
            width: 30px;
            height: 30px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
        }

        .icon-circle i {
            font-size: 12px;
            color: #4e73df;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar and Sidebar include -->
        <?php include '../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="project-header">
                        <div class="project-title"><?php echo htmlspecialchars($project['project_name']); ?></div>
                        <div class="project-details">
                            <span class="project-detail-item"><i class="fas fa-chart-line mr-1"></i><?php echo htmlspecialchars($project['status']); ?></span>
                            <span class="project-detail-item"><i class="far fa-calendar-alt mr-1"></i><?php echo htmlspecialchars($project['start_date']); ?> - <?php echo htmlspecialchars($project['end_date']); ?></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="section-card">
                                <div class="section-header">
                                    <i class="fas fa-info-circle mr-2"></i>ข้อมูลโครงการ
                                </div>
                                <div class="section-body">
                                    <div class="info-item"><span class="info-label">เลขที่สัญญา:</span> <?php echo htmlspecialchars($project['contract_no']); ?></div>
                                    <div class="info-item"><span class="info-label">สินค้า:</span> <?php echo htmlspecialchars($project['product_name']); ?></div>
                                    <div class="info-item"><span class="info-label">รายละเอียดสินค้า:</span> <?php echo htmlspecialchars($project['product_description']); ?></div>
                                    <div class="info-item"><span class="info-label">วันที่สร้าง:</span> <?php echo htmlspecialchars($project['created_at']); ?></div>
                                    <div class="info-item"><span class="info-label">ผู้สร้าง:</span> <?php echo htmlspecialchars($project['creator_first_name'] . ' ' . $project['creator_last_name']); ?></div>
                                    <div class="info-item"><span class="info-label">วันที่แก้ไขล่าสุด:</span> <?php echo htmlspecialchars($project['updated_at']); ?></div>
                                    <div class="info-item"><span class="info-label">ผู้แก้ไขล่าสุด:</span> <?php echo htmlspecialchars($project['updater_first_name'] . ' ' . $project['updater_last_name']); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="section-card">
                                <div class="section-header">
                                    <i class="fas fa-user mr-2"></i>ข้อมูลลูกค้า
                                </div>
                                <div class="section-body">
                                    <div class="info-item"><span class="info-label">ชื่อลูกค้า:</span> <?php echo htmlspecialchars($project['customer_name']); ?></div>
                                    <div class="info-item"><span class="info-label">บริษัท:</span> <?php echo htmlspecialchars($project['company']); ?></div>
                                    <div class="info-item"><span class="info-label">ที่อยู่:</span> <?php echo htmlspecialchars($project['address']); ?></div>
                                    <div class="info-item"><span class="info-label">โทรศัพท์:</span> <?php echo htmlspecialchars($project['customer_phone']); ?></div>
                                    <div class="info-item"><span class="info-label">อีเมล:</span> <?php echo htmlspecialchars($project['customer_email']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="section-card">
                                <div class="section-header">
                                    <i class="fas fa-user-tie mr-2"></i>ข้อมูลผู้ขาย
                                </div>
                                <div class="section-body">
                                    <div class="info-item"><span class="info-label">ชื่อผู้ขาย:</span> <?php echo htmlspecialchars($project['first_name'] . ' ' . $project['last_name']); ?></div>
                                    <div class="info-item"><span class="info-label">อีเมล:</span> <?php echo htmlspecialchars($project['seller_email']); ?></div>
                                    <div class="info-item"><span class="info-label">โทรศัพท์:</span> <?php echo htmlspecialchars($project['seller_phone']); ?></div>
                                    <div class="info-item"><span class="info-label">ทีม:</span> <?php echo htmlspecialchars($project['team_name']); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="section-card">
                                <div class="section-header">
                                    <i class="fas fa-chart-bar mr-2"></i>ข้อมูลทางการเงิน
                                </div>
                                <div class="section-body">
                                    <div class="info-item"><span class="info-label">ราคาขาย (รวมภาษี):</span> <?php echo number_format($project['sale_vat'], 2); ?> บาท</div>
                                    <div class="info-item"><span class="info-label">ราคาขาย (ไม่รวมภาษี):</span> <?php echo number_format($project['sale_no_vat'], 2); ?> บาท</div>
                                    <div class="info-item"><span class="info-label">ต้นทุน (รวมภาษี):</span> <?php echo number_format($project['cost_vat'], 2); ?> บาท</div>
                                    <div class="info-item"><span class="info-label">ต้นทุน (ไม่รวมภาษี):</span> <?php echo number_format($project['cost_no_vat'], 2); ?> บาท</div>
                                    <div class="financial-summary">
                                        <div class="financial-item">
                                            <span>
                                                <div class="icon-circle"><i class="fas fa-chart-line"></i></div> กำไรขั้นต้น:
                                            </span>
                                            <strong><?php echo number_format($project['gross_profit'], 2); ?> บาท</strong>
                                        </div>
                                        <div class="financial-item">
                                            <span>
                                                <div class="icon-circle"><i class="fas fa-percentage"></i></div> กำไรขั้นต้น (%):
                                            </span>
                                            <strong><?php echo number_format($project['potential'], 2); ?>%</strong>
                                        </div>
                                    </div>
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