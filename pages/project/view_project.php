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
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="project-header">
                        <div class="project-title"><?php echo htmlspecialchars($project['project_name']); ?></div>
                        <span class="project-status"><?php echo htmlspecialchars($project['status']); ?></span>
                        <div class="project-date"><i class="far fa-calendar-alt mr-2"></i><?php echo htmlspecialchars($project['start_date']) . ' - ' . htmlspecialchars($project['end_date']); ?></div>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-card-header">
                                    <i class="fas fa-user mr-2"></i>ข้อมูลลูกค้า
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
                </div>
            </section>
        </div>
        <?php include '../../include/footer.php'; ?>
    </div>
</body>

</html>