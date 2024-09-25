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
    $sql = "SELECT p.*, u.first_name, u.last_name, pr.product_name, c.customer_name, t.team_name 
            FROM projects p 
            LEFT JOIN users u ON p.seller = u.user_id 
            LEFT JOIN products pr ON p.product_id = pr.product_id 
            LEFT JOIN customers c ON p.customer_id = c.customer_id 
            LEFT JOIN teams t ON u.team_id = t.team_id 
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
        }

        .content-wrapper {
            padding: 30px;
            background-color: #ffffff;
        }

        .project-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .project-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .project-details {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .project-detail-item {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 10px 15px;
            border-radius: 20px;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .section-card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .section-header {
            background-color: #f8f9fa;
            color: #333;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 18px;
            border-bottom: 1px solid #dee2e6;
        }

        .section-body {
            padding: 20px;
        }

        .table-custom {
            width: 100%;
        }

        .table-custom th {
            background-color: #f8f9fa;
            font-weight: 600;
            padding: 12px;
        }

        .table-custom td {
            padding: 12px;
            vertical-align: middle;
        }

        .badge-custom {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 14px;
        }

        .financial-summary {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            padding: 20px;
            border-radius: 10px;
            color: #ffffff;
            margin-top: 20px;
        }

        .financial-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .icon-circle {
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar and Sidebar include -->
        <?php include '../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">รายละเอียดโครงการ</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">รายละเอียดโครงการ</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="project-header">
                        <div class="project-title"><?php echo htmlspecialchars($project['project_name']); ?></div>
                        <div class="project-details">
                            <span class="project-detail-item"><i class="fas fa-chart-line mr-2"></i>สถานะ: <?php echo htmlspecialchars($project['status']); ?></span>
                            <span class="project-detail-item"><i class="far fa-calendar-alt mr-2"></i>เริ่ม: <?php echo htmlspecialchars($project['start_date']); ?></span>
                            <span class="project-detail-item"><i class="far fa-calendar-check mr-2"></i>สิ้นสุด: <?php echo htmlspecialchars($project['end_date']); ?></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="section-card">
                                <div class="section-header">
                                    <i class="fas fa-info-circle mr-2"></i>ข้อมูลโครงการ
                                </div>
                                <div class="section-body">
                                    <table class="table table-custom">
                                        <tr>
                                            <th><i class="fas fa-file-contract mr-2"></i>เลขที่สัญญา</th>
                                            <td><?php echo htmlspecialchars($project['contract_no']); ?></td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-user mr-2"></i>ชื่อลูกค้า</th>
                                            <td><?php echo htmlspecialchars($project['customer_name']); ?></td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-box mr-2"></i>ชื่อสินค้า</th>
                                            <td><?php echo htmlspecialchars($project['product_name']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="section-card">
                                <div class="section-header">
                                    <i class="fas fa-user-tie mr-2"></i>ข้อมูลผู้ขาย
                                </div>
                                <div class="section-body">
                                    <table class="table table-custom">
                                        <tr>
                                            <th><i class="fas fa-id-badge mr-2"></i>ชื่อผู้ขาย</th>
                                            <td><?php echo htmlspecialchars($project['first_name'] . ' ' . $project['last_name']); ?></td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-users mr-2"></i>ทีม</th>
                                            <td><?php echo htmlspecialchars($project['team_name']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="section-header">
                            <i class="fas fa-chart-bar mr-2"></i>ข้อมูลทางการเงิน
                        </div>
                        <div class="section-body">
                            <table class="table table-custom">
                                <thead>
                                    <tr>
                                        <th>รายการ</th>
                                        <th>ราคา (ไม่รวมภาษี)</th>
                                        <th>ภาษี</th>
                                        <th>ราคา (รวมภาษี)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><i class="fas fa-tag mr-2"></i>ราคาขาย</td>
                                        <td><?php echo number_format($project['sale_no_vat'], 2); ?></td>
                                        <td><?php echo number_format($project['sale_vat'] - $project['sale_no_vat'], 2); ?></td>
                                        <td><?php echo number_format($project['sale_vat'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td><i class="fas fa-coins mr-2"></i>ต้นทุน</td>
                                        <td><?php echo number_format($project['cost_no_vat'], 2); ?></td>
                                        <td><?php echo number_format($project['cost_vat'] - $project['cost_no_vat'], 2); ?></td>
                                        <td><?php echo number_format($project['cost_vat'], 2); ?></td>
                                    </tr>
                                </tbody>
                            </table>

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
            </section>
        </div>

        <?php include '../../include/footer.php'; ?>
    </div>
</body>

</html>