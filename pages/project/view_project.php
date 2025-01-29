<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;
$user_team_id = $_SESSION['team_id'] ?? 0;

// ตรวจสอบว่า project_id ถูกส่งมาจาก URL หรือไม่
if (!isset($_GET['project_id']) || empty($_GET['project_id'])) {
    $_SESSION['error'] = "ไม่พบข้อมูลโครงการ";
    header("Location: project.php");
    exit;
}

// ดึงข้อมูลโครงการและผู้สร้าง
try {
    // รับ project_id จาก URL และทำการถอดรหัส
    $project_id = decryptUserId($_GET['project_id']);

    try {
        $sql = "SELECT p.*, 
        u.team_id as creator_team_id, 
        u.first_name, u.last_name, u.email as seller_email, u.phone as seller_phone,
        pr.product_name, pr.product_description,
        c.customer_name, c.company, c.address, c.phone as customer_phone, c.email as customer_email,
        t.team_name,
        tl.first_name as team_leader_first_name, tl.last_name as team_leader_last_name,
        creator.first_name as creator_first_name, creator.last_name as creator_last_name,
        updater.first_name as updater_first_name, updater.last_name as updater_last_name,
        pm.is_active, -- เพิ่มการดึงค่า is_active จากตาราง project_members
        CASE 
            WHEN p.created_by = :user_id THEN true
            WHEN EXISTS (
                SELECT 1 FROM project_members pm 
                WHERE pm.project_id = p.project_id 
                AND pm.user_id = :user_id
            ) THEN true
            ELSE false
        END as has_access
        FROM projects p 
        LEFT JOIN users u ON p.seller = u.user_id 
        LEFT JOIN products pr ON p.product_id = pr.product_id 
        LEFT JOIN customers c ON p.customer_id = c.customer_id 
        LEFT JOIN teams t ON u.team_id = t.team_id 
        LEFT JOIN users tl ON t.team_leader = tl.user_id
        LEFT JOIN users creator ON p.created_by = creator.user_id
        LEFT JOIN users updater ON p.updated_by = updater.user_id
        LEFT JOIN project_members pm ON p.project_id = pm.project_id AND pm.user_id = :user_id -- เช็ค is_active ของสมาชิก
        WHERE p.project_id = :project_id";

        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':project_id', $project_id, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();

        // เพิ่มบรรทัดนี้เพื่อดึงข้อมูลโครงการ
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            $_SESSION['error'] = "ไม่พบโครงการที่ต้องการแสดง";
            header("Location: project.php");
            exit;
        }

        // ตรวจสอบสิทธิ์การเข้าถึง
        $hasAccess = false;
        switch ($role) {
            case 'Executive':
                $hasAccess = true;
                break;
            case 'Sale Supervisor':
                // เข้าถึงได้ถ้าเป็นโครงการในทีมหรือเป็นสมาชิก
                $hasAccess = ($user_team_id == $project['creator_team_id'] || $project['has_access']);
                break;
            case 'Seller':
            case 'Engineer':
                // เข้าถึงได้ถ้าเป็นผู้สร้างหรือเป็นสมาชิก
                $hasAccess = $project['has_access'];
                break;
        }

        if (!$hasAccess) {
            $_SESSION['error'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
            header("Location: project.php");
            exit;
        }
    } catch (PDOException $e) {
        echo "เกิดข้อผิดพลาด: " . $e->getMessage();
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

// ตรวจสอบสิทธิ์การเข้าถึงข้อมูลทางการเงิน
$hasAccessToFinancialInfo = false; // ตั้งค่าเริ่มต้นเป็น false
$hasFullAccess = false; // สำหรับสิทธิ์เต็ม
$hasHalfAccess = false; // สำหรับสิทธิ์ครึ่งเดียว

// เงื่อนไข 1: Executive มีสิทธิ์เต็ม
if ($role === 'Executive') {
    $hasAccessToFinancialInfo = true;
    $hasFullAccess = true;
}
// เงื่อนไข 2: Sale Supervisor และอยู่ในทีมเดียวกับผู้สร้าง
elseif ($role === 'Sale Supervisor' && $user_team_id == $project['creator_team_id']) {
    $hasAccessToFinancialInfo = true;
    $hasFullAccess = true;
}
// เงื่อนไข 3: ผู้สร้างโครงการ
elseif ($project['created_by'] == $user_id) {
    $hasAccessToFinancialInfo = true;
    $hasFullAccess = true;
}
// เงื่อนไข 4: สมาชิกที่ถูกเชิญ
elseif (isset($project['is_active'])) {
    switch ($project['is_active']) {
        case 0: // Full Access
            $hasAccessToFinancialInfo = true;
            $hasFullAccess = true;
            break;
        case 1: // View Only
            $hasAccessToFinancialInfo = false;
            break;
        case 2: // Half Access
            $hasHalfAccess = true;
            break;
    }
}



// ดึงข้อมูลสมาชิกในโครงการ
$stmt = $condb->prepare("SELECT pm.*, u.first_name, u.last_name, pr.role_name
                        FROM project_members pm
                        JOIN users u ON pm.user_id = u.user_id
                        JOIN project_roles pr ON pm.role_id = pr.role_id
                        WHERE pm.project_id = ?
                        ORDER BY pr.role_name, u.first_name");
$stmt->execute([$project_id]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลบทบาททั้งหมด
$stmt = $condb->prepare("SELECT * FROM project_roles ORDER BY role_name");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงรายชื่อผู้ใช้ที่ยังไม่ได้เป็นสมาชิกในโครงการ
$stmt = $condb->prepare("SELECT u.* 
                        FROM users u 
                        WHERE u.user_id NOT IN (
                            SELECT pm.user_id 
                            FROM project_members pm 
                            WHERE pm.project_id = ?
                        )
                        ORDER BY u.first_name");
$stmt->execute([$project_id]);
$available_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

// ดึงข้อมูลผู้ใช้ทั้งหมดจากฐานข้อมูล
$stmt_users = $condb->prepare("SELECT user_id, first_name, last_name FROM users ORDER BY first_name, last_name");
$stmt_users->execute();
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "project"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalePipeline | View Project</title>
    <?php include '../../include/header.php'; ?>

    <!-- เพิ่ม SortableJS library -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <!-- PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <!-- PDF -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/css/view_project.css">
    <link rel="stylesheet" href="../../assets/css/project/cost_tab/cost_tab.css">
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
                        <li class="nav-item"><a class="nav-link " href="#members" data-toggle="tab" data-tab="project-member">แชร์โครงการ</a></li>
                        <li class="nav-item"><a class="nav-link " href="#project-cost" data-toggle="tab" data-tab="project-cost">ต้นทุนโครงการ</a></li>


                        <?php if ($hasFullAccess || $hasHalfAccess): ?>
                            <li class="nav-item"><a class="nav-link" href="#tasks" data-toggle="tab" role="tab">บริหารโครงการ</a></li>
                        <?php endif; ?>

                        <?php if ($hasFullAccess || $hasHalfAccess): ?>
                            <li class="nav-item"><a class="nav-link" href="#documents" data-toggle="tab" data-tab="documents">เอกสารแนบ</a></li>
                        <?php endif; ?>

                        <?php if ($hasFullAccess || $hasHalfAccess): ?>
                            <li class="nav-item"><a class="nav-link" href="#links" data-toggle="tab" data-tab="links">แนบลิงค์เอกสารโครงการ</a></li>
                        <?php endif; ?>

                        <?php if ($hasFullAccess || $hasHalfAccess): ?>
                            <li class="nav-item"><a class="nav-link" href="#images" data-toggle="tab" data-tab="images">รูปภาพ</a></li>
                        <?php endif; ?>

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
                                                <?php if ($hasFullAccess): ?>
                                                    <button class="edit-button no-print" onclick="location.href='edit_project.php?project_id=<?php echo urlencode(encryptUserId($project['project_id'])); ?>'">
                                                        <i class="fas fa-edit"></i> แก้ไข
                                                    </button>
                                                    <button class="edit-button no-print" onclick="generatePDF()">
                                                        <i class="fas fa-file-pdf"></i> Save PDF
                                                    </button>
                                                <?php endif; ?>
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

                                        <?php if ($hasAccessToFinancialInfo): ?>
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
                                        <?php endif; ?>

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
                                <?php
                                // เช็คเงื่อนไข is_active
                                if (isset($project['is_active']) && $project['is_active'] == 1) {
                                    // ถ้าเป็น View Only ให้ include หน้า sale_price.php
                                    include 'report/sale_price.php';
                                } else {
                                    // ถ้าไม่ใช่ View Only ให้แสดงเนื้อหาปกติ
                                ?>
                                    <div class="table-responsive">
                                        <table id="costTable" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <!-- คอลัมน์พื้นฐานที่ทุกคนเห็น -->
                                                    <th class="text-nowrap text-center" style="width: 10%">Type</th>
                                                    <th class="text-nowrap" style="width: 10%">PART No.</th>
                                                    <th class="text-nowrap" style="width: 50%">Description</th>
                                                    <th class="text-nowrap" style="width: 5%">QTY.</th>
                                                    <th class="text-nowrap" style="width: 5%">Unit</th>
                                                    <th class="text-nowrap" style="width: 10%">Price / Unit</th>
                                                    <th class="text-nowrap" style="width: 10%">Total Amount</th>
                                                    <!-- คอลัมน์ที่เฉพาะผู้มีสิทธิ์เท่านั้นที่จะเห็น -->
                                                    <?php if ($hasAccessToFinancialInfo): ?>
                                                        <th class="text-nowrap" style="width: 10%">Cost / Unit</th>
                                                        <th class="text-nowrap">Total Cost</th>
                                                        <th class="text-nowrap">Supplier</th>
                                                        <th class="text-nowrap">Actions</th>
                                                    <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tbody id="costTableBody">
                                                <!-- ข้อมูลจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                            </tbody>
                                            <!-- แถวสำหรับกรอกข้อมูลใหม่ -->
                                            <tfoot>
                                                <tr>
                                                    <!-- ฟิลด์พื้นฐานที่ทุกคนเห็น -->
                                                    <td class="text-nowrap"><input type="text" id="typeInput" class="form-control form-control-sm" placeholder="A, B, C"></td>
                                                    <td class="text-nowrap"><input type="text" id="partNoInput" class="form-control form-control-sm" placeholder="Service, Hardware, Software"></td>
                                                    <td class="text-nowrap"><input type="text" id="descriptionInput" class="form-control form-control-sm" placeholder="ใส่รายละเอียด"></td>
                                                    <td class="text-nowrap text-center"><input type="number" id="qtyInput" class="form-control form-control-sm" placeholder="จำนวนตัวเลข"></td>
                                                    <td class="text-nowrap"><input type="text" id="unitInput" class="form-control form-control-sm" placeholder="เช่น วัน, คน, ชิ้น"></td>
                                                    <td class="text-nowrap"><input type="text" id="priceInput" class="form-control form-control-sm" placeholder="ตั้งราคาขาย"></td>
                                                    <td class="text-nowrap"><span id="totalAmountInput">0.00</span></td>
                                                    <!-- ฟิลด์ที่เฉพาะผู้มีสิทธิ์เท่านั้นที่จะเห็น -->
                                                    <?php if ($hasAccessToFinancialInfo): ?>
                                                        <td class="text-nowrap"><input type="text" id="costInput" class="form-control form-control-sm" placeholder="ตั้งราคาต้นทุน"></td>
                                                        <td class="text-nowrap"><span id="totalCostInput">0.00</span></td>
                                                        <td class="text-nowrap"><input type="text" id="supplierInput" class="form-control form-control-sm" placeholder=""></td>
                                                        <td class="text-nowrap"><button class="btn btn-sm btn-success" onclick="saveCost()">เพิ่ม</button></td>
                                                    <?php endif; ?>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <!-- Total Section -->
                                    <div class="totals-section">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <!-- ส่วนที่ทุกคนเห็น -->
                                                    <div class="col-md-4">
                                                        <p>Total Amount: <span id="totalAmount">0.00</span> บาท</p>
                                                        <p>Vat (7%): <span id="vatAmount">0.00</span> บาท</p>
                                                        <p>Grand Total: <span id="grandTotal">0.00</span> บาท</p>
                                                    </div>

                                                    <!-- ส่วนที่เฉพาะผู้มีสิทธิ์เท่านั้นที่จะเห็น -->
                                                    <?php if ($hasAccessToFinancialInfo): ?>
                                                        <div class="col-md-4">
                                                            <p>Total Cost: <span id="totalCost">0.00</span> บาท</p>
                                                            <p>Cost Vat (7%): <span id="costVatAmount">0.00</span> บาท</p>
                                                            <p>Total Cost with Vat: <span id="totalCostWithVat">0.00</span> บาท</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p>Profit: <span id="profitAmount">0.00</span> บาท</p>
                                                            <p>Profit Percentage: <span id="profitPercentage">0.00</span>%</p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php
                                }
                                ?>
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
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-project-diagram mr-1"></i>
                                            ข้อมูลโครงการ
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>ชื่อโครงการ:</strong> <?php echo htmlspecialchars($project['project_name']); ?></p>
                                                <p><strong>ผลิตภัณฑ์:</strong> <?php echo htmlspecialchars($project['product_name']); ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>วันที่เริ่ม:</strong> <?php echo $project['start_date']; ?></p>
                                                <p><strong>วันที่สิ้นสุด:</strong> <?php echo $project['end_date']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-tasks mr-1"></i>
                                            รายการงาน
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="showAddTaskModal()">
                                                <i class="fas fa-plus"></i> เพิ่มงานใหม่
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="task-container">
                                            <!-- Task tree will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- แถบที่ 7 จัดการสมาชิก -->
                            <div class="tab-pane" id="members">
                                <!-- ตารางแสดงสมาชิกในโครงการ -->
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">สมาชิกในโครงการ</h3>
                                        <?php if ($hasAccessToFinancialInfo): ?>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addMemberModal">
                                                    <i class="fas fa-user-plus"></i> เพิ่มสมาชิก
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <table id="membersTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ลำดับ</th>
                                                    <th>ชื่อ-นามสกุล</th>
                                                    <th>บทบาท</th>
                                                    <th>วันที่เข้าร่วม</th>
                                                    <th>สถานะ</th>
                                                    <?php if ($hasAccessToFinancialInfo): ?>
                                                        <th>จัดการ</th>
                                                    <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($members as $index => $member): ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($member['role_name']); ?></td>
                                                        <td><?php echo date('d/m/Y', strtotime($member['joined_date'])); ?></td>
                                                        <td>
                                                            <?php if ($member['is_active'] == 1): ?>
                                                                <span class="badge badge-success">View</span>
                                                            <?php elseif ($member['is_active'] == 2): ?>
                                                                <span class="badge badge-primary">Half Acesss</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-danger">Full Access</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <?php if ($hasAccessToFinancialInfo): ?>
                                                            <td>
                                                                <button type="button" class="btn btn-info btn-sm"
                                                                    onclick="editMember('<?php echo $member['member_id']; ?>', 
                                                                           '<?php echo $member['role_id']; ?>', 
                                                                           <?php echo $member['is_active']; ?>)">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    onclick="confirmDelete('<?php echo $member['member_id']; ?>', 
                                                                             '<?php echo $member['first_name'] . ' ' . $member['last_name']; ?>')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        <?php endif; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
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

<!-- 1. Modal สำหรับเพิ่ม/แก้ไขการชำระเงิน -->
<?php include 'tab_payment/payment.php'; ?>
<!-- 1. Modal สำหรับเพิ่ม/แก้ไขการชำระเงิน -->


<!-- 2. Modal สำหรับอัปโหลดเอกสาร -->
<?php include 'tab_document/document.php'; ?>
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
<?php include 'tab_image/image.php'; ?>
<!-- 4. การอัปโหลดและแสดงรูปภาพ -->

<!-- 5. function สำหรับการพิมพ์ PDF -->
<?php include 'tab_payment/export_pdf.php'; ?>
<!-- 5. function สำหรับการพิมพ์ PDF -->

<!-- 6.  // ฟังก์ชันเพิ่มแถวใหม่ในตารางต้นทุน -->
<?php include 'tab_cost/cost.php'; ?>
<!-- 6.  // ฟังก์ชันเพิ่มแถวใหม่ในตารางต้นทุน -->

<!-- 7 Modal สำหรับเพิ่ม/แก้ไขลิงก์เอกสาร -->
<?php include 'tab_linkdocument/link_document.php'; ?>
<!-- 7 Modal สำหรับเพิ่ม/แก้ไขลิงก์เอกสาร -->

<!-- 8. การจัดการโครงการ -->
<?php include 'management/tab_management.php'; ?>
<!-- 8. การจัดการโครงการ -->

<!-- 9. การจัดการสมาชิก -->
<?php include 'project_member/tab_member.php'; ?>
<!-- 9. การจัดการสมาชิก -->