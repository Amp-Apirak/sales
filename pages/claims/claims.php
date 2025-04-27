<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ดึงข้อมูลจาก session ของผู้ใช้ที่เข้าสู่ระบบ
$role = $_SESSION['role'];  // บทบาทของผู้ใช้
$team_id = $_SESSION['team_id'];  // team_id ของผู้ใช้
$user_id = $_SESSION['user_id'];  // user_id ของผู้ใช้

// รับค่าการค้นหาจากฟอร์ม
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$submitter_filter = isset($_GET['submitter']) ? $_GET['submitter'] : '';  // เพิ่มตัวกรองผู้ทำเรื่องเบิก

// Default เป็นปีปัจจุบันถ้าไม่มีการระบุช่วงวันที่
$current_year = date('Y'); // ปีปัจจุบัน (เช่น 2025)
if (empty($date_from)) {
    $date_from = "$current_year-01-01"; // วันที่ 1 มกราคมของปีปัจจุบัน
}
if (empty($date_to)) {
    $date_to = "$current_year-12-31"; // วันที่ 31 ธันวาคมของปีปัจจุบัน
}

// Query สำหรับสรุปตัวเลขตามสถานะ
$summary_sql = "SELECT 
                    e.status,
                    COUNT(*) as total_count,
                    SUM(e.total_amount) as total_amount
                FROM expenses e
                LEFT JOIN users u1 ON e.submitter_id = u1.user_id
                LEFT JOIN projects p ON e.project_id = p.project_id
                WHERE e.expense_date BETWEEN :date_from AND :date_to";

// เพิ่มเงื่อนไขตามบทบาท
if ($role == 'Sale Supervisor') {
    $summary_sql .= " AND (e.submitter_id IN (SELECT user_id FROM users WHERE team_id = :team_id) 
                      OR e.submitter_id = :user_id)";
} elseif ($role == 'Seller' || $role == 'Engineer') {
    $summary_sql .= " AND e.submitter_id = :user_id";
}

if (!empty($submitter_filter)) {
    $summary_sql .= " AND e.submitter_id = :submitter";
}

$summary_sql .= " GROUP BY e.status";

$summary_stmt = $condb->prepare($summary_sql);
$summary_stmt->bindParam(':date_from', $date_from, PDO::PARAM_STR);
$summary_stmt->bindParam(':date_to', $date_to, PDO::PARAM_STR);

if ($role == 'Sale Supervisor') {
    $summary_stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR);
    $summary_stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
} elseif ($role == 'Seller' || $role == 'Engineer') {
    $summary_stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
}

if (!empty($submitter_filter)) {
    $summary_stmt->bindParam(':submitter', $submitter_filter, PDO::PARAM_STR);
}

$summary_stmt->execute();
$summary_data = $summary_stmt->fetchAll(PDO::FETCH_ASSOC);

// เตรียมข้อมูลสรุปสำหรับแต่ละสถานะ
$summary = [
    'Pending' => ['count' => 0, 'amount' => 0],
    'Approved' => ['count' => 0, 'amount' => 0],
    'Rejected' => ['count' => 0, 'amount' => 0],
    'Paid' => ['count' => 0, 'amount' => 0],
];

foreach ($summary_data as $data) {
    $summary[$data['status']]['count'] = $data['total_count'];
    $summary[$data['status']]['amount'] = $data['total_amount'];
}

// Query พื้นฐานในการดึงข้อมูลคำขอเบิกค่าใช้จ่ายทั้งหมด
$sql = "SELECT e.*, 
               u1.first_name as submitter_first_name, 
               u1.last_name as submitter_last_name,
               u2.first_name as approver_first_name, 
               u2.last_name as approver_last_name,
               p.project_name
        FROM expenses e
        LEFT JOIN users u1 ON e.submitter_id = u1.user_id
        LEFT JOIN users u2 ON e.approver_id = u2.user_id
        LEFT JOIN projects p ON e.project_id = p.project_id
        WHERE e.expense_date BETWEEN :date_from AND :date_to";

// เพิ่มเงื่อนไขการค้นหาตามบทบาทของผู้ใช้
if ($role == 'Sale Supervisor') {
    $sql .= " AND (e.submitter_id IN (SELECT user_id FROM users WHERE team_id = :team_id) 
                OR e.submitter_id = :user_id)";
} elseif ($role == 'Seller' || $role == 'Engineer') {
    $sql .= " AND e.submitter_id = :user_id";
}

// เพิ่มเงื่อนไขการค้นหา
if (!empty($search)) {
    $sql .= " AND (e.expense_number LIKE :search 
               OR e.expense_title LIKE :search 
               OR p.project_name LIKE :search
               OR u1.first_name LIKE :search 
               OR u1.last_name LIKE :search)";
}

if (!empty($status_filter)) {
    $sql .= " AND e.status = :status";
}

if (!empty($submitter_filter)) {
    $sql .= " AND e.submitter_id = :submitter";
}

// เพิ่มการเรียงลำดับ
$sql .= " ORDER BY e.created_at DESC";

// เตรียม statement และ bind ค่าต่างๆ
$stmt = $condb->prepare($sql);

// ผูกค่า
$stmt->bindParam(':date_from', $date_from, PDO::PARAM_STR);
$stmt->bindParam(':date_to', $date_to, PDO::PARAM_STR);

if ($role == 'Sale Supervisor') {
    $stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
} elseif ($role == 'Seller' || $role == 'Engineer') {
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
}

if (!empty($search)) {
    $search_param = '%' . $search . '%';
    $stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
}

if (!empty($status_filter)) {
    $stmt->bindParam(':status', $status_filter, PDO::PARAM_STR);
}

if (!empty($submitter_filter)) {
    $stmt->bindParam(':submitter', $submitter_filter, PDO::PARAM_STR);
}

// Execute query เพื่อดึงข้อมูล
$stmt->execute();
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลผู้ใช้สำหรับตัวเลือกผู้ทำเรื่องเบิก
$submitter_sql = "SELECT user_id, first_name, last_name FROM users WHERE 1=1";
if ($role == 'Sale Supervisor') {
    $submitter_sql .= " AND (team_id = :team_id OR user_id = :user_id)";
} elseif ($role == 'Seller' || $role == 'Engineer') {
    $submitter_sql .= " AND user_id = :user_id";
}
$submitter_sql .= " ORDER BY first_name ASC";
$submitter_stmt = $condb->prepare($submitter_sql);

if ($role == 'Sale Supervisor') {
    $submitter_stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR);
    $submitter_stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
} elseif ($role == 'Seller' || $role == 'Engineer') {
    $submitter_stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
}

$submitter_stmt->execute();
$submitters = $submitter_stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลโครงการสำหรับตัวเลือกในฟอร์มค้นหา
$project_sql = "SELECT project_id, project_name FROM projects WHERE 1=1";
if ($role == 'Sale Supervisor') {
    $project_sql .= " AND (created_by IN (SELECT user_id FROM users WHERE team_id = :team_id) 
                      OR created_by = :user_id OR seller = :user_id)";
} elseif ($role == 'Seller' || $role == 'Engineer') {
    $project_sql .= " AND (created_by = :user_id OR seller = :user_id)";
}
$project_sql .= " ORDER BY project_name ASC";
$project_stmt = $condb->prepare($project_sql);

if ($role == 'Sale Supervisor') {
    $project_stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR);
    $project_stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
} elseif ($role == 'Seller' || $role == 'Engineer') {
    $project_stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
}

$project_stmt->execute();
$projects = $project_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<?php $menu = "claims"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | การเบิกค่าใช้จ่าย</title>
    <?php include '../../include/header.php'; ?>
    <style>
        .status-badge {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 500;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }

        .status-Pending {
            background-color: #f0f0f0;
            color: #666;
        }

        .status-Approved {
            background-color: #d1fae5;
            color: #047857;
        }

        .status-Rejected {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .status-Paid {
            background-color: #dbeafe;
            color: #1e40af;
        }

        /* ช่วงวันที่ picker */
        .date-range-container {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        /* ปรับขนาดปุ่ม */
        .btn-sm-custom {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        /* สไตล์สำหรับ Card สรุป */
        .summary-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .summary-card:hover {
            transform: translateY(-5px);
        }

        .summary-card .card-body {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .summary-card .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .summary-card .card-text {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .summary-card .card-subtext {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .summary-card .icon {
            font-size: 2.5rem;
            opacity: 0.2;
        }

        /* สีของ Card ตามสถานะ */
        .summary-card.pending {
            background-color: #f0f0f0;
            color: #666;
        }

        .summary-card.approved {
            background-color: #d1fae5;
            color: #047857;
        }

        .summary-card.rejected {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .summary-card.paid {
            background-color: #dbeafe;
            color: #1e40af;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include '../../include/navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">การเบิกค่าใช้จ่าย</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">หน้าแรก</a></li>
                                <li class="breadcrumb-item active">การเบิกค่าใช้จ่าย</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- Card สรุปตัวเลข -->
                            <div class="row mb-4">
                                <!-- Card: รอพิจารณา -->
                                <div class="col-md-3">
                                    <div class="card summary-card pending">
                                        <div class="card-body">
                                            <div>
                                                <h5 class="card-title">รอพิจารณา</h5>
                                                <p class="card-text"><?php echo number_format($summary['Pending']['count']); ?> รายการ</p>
                                                <p class="card-subtext"><?php echo number_format($summary['Pending']['amount'], 2); ?> บาท</p>
                                            </div>
                                            <i class="fas fa-hourglass-half icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card: อนุมัติแล้ว -->
                                <div class="col-md-3">
                                    <div class="card summary-card approved">
                                        <div class="card-body">
                                            <div>
                                                <h5 class="card-title">อนุมัติแล้ว</h5>
                                                <p class="card-text"><?php echo number_format($summary['Approved']['count']); ?> รายการ</p>
                                                <p class="card-subtext"><?php echo number_format($summary['Approved']['amount'], 2); ?> บาท</p>
                                            </div>
                                            <i class="fas fa-check-circle icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card: ปฏิเสธ -->
                                <div class="col-md-3">
                                    <div class="card summary-card rejected">
                                        <div class="card-body">
                                            <div>
                                                <h5 class="card-title">ปฏิเสธ</h5>
                                                <p class="card-text"><?php echo number_format($summary['Rejected']['count']); ?> รายการ</p>
                                                <p class="card-subtext"><?php echo number_format($summary['Rejected']['amount'], 2); ?> บาท</p>
                                            </div>
                                            <i class="fas fa-times-circle icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card: ชำระแล้ว -->
                                <div class="col-md-3">
                                    <div class="card summary-card paid">
                                        <div class="card-body">
                                            <div>
                                                <h5 class="card-title">ชำระแล้ว</h5>
                                                <p class="card-text"><?php echo number_format($summary['Paid']['count']); ?> รายการ</p>
                                                <p class="card-subtext"><?php echo number_format($summary['Paid']['amount'], 2); ?> บาท</p>
                                            </div>
                                            <i class="fas fa-money-check-alt icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.Card สรุปตัวเลข -->

                            <!-- ส่วนค้นหา -->
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">ค้นหา</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form action="" method="GET">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="search">ค้นหา</label>
                                                    <input type="text" class="form-control" id="search" name="search"
                                                        placeholder="เลขที่การเบิก, หัวข้อ, โครงการ, ชื่อ"
                                                        value="<?php echo htmlspecialchars($search); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="status">สถานะ</label>
                                                    <select class="form-control" id="status" name="status">
                                                        <option value="" <?php echo $status_filter === '' ? 'selected' : ''; ?>>ทุกสถานะ</option>
                                                        <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>รอพิจารณา</option>
                                                        <option value="Approved" <?php echo $status_filter === 'Approved' ? 'selected' : ''; ?>>อนุมัติแล้ว</option>
                                                        <option value="Rejected" <?php echo $status_filter === 'Rejected' ? 'selected' : ''; ?>>ปฏิเสธ</option>
                                                        <option value="Paid" <?php echo $status_filter === 'Paid' ? 'selected' : ''; ?>>ชำระแล้ว</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="submitter">ผู้ทำเรื่องเบิก</label>
                                                    <select class="form-control" id="submitter" name="submitter">
                                                        <option value="" <?php echo $submitter_filter === '' ? 'selected' : ''; ?>>ทุกคน</option>
                                                        <?php foreach ($submitters as $submitter) { ?>
                                                            <option value="<?php echo $submitter['user_id']; ?>"
                                                                <?php echo $submitter_filter === $submitter['user_id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($submitter['first_name'] . ' ' . $submitter['last_name']); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>ช่วงวันที่</label>
                                                    <div class="date-range-container">
                                                        <input type="date" class="form-control" id="date_from" name="date_from"
                                                            value="<?php echo htmlspecialchars($date_from); ?>">
                                                        <span>ถึง</span>
                                                        <input type="date" class="form-control" id="date_to" name="date_to"
                                                            value="<?php echo htmlspecialchars($date_to); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex justify-content-end align-items-end">
                                                <div class="form-group mb-0">
                                                    <button type="submit" class="btn btn-primary mr-2">ค้นหา</button>
                                                    <a href="claims.php" class="btn btn-default">รีเซ็ต</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- /.card -->

                            <!-- ปุ่มเพิ่มคำขอเบิกใหม่ -->
                            <div class="d-flex justify-content-end mb-3">
                                <a href="add_claim.php" class="btn btn-success">
                                    <i class="fas fa-plus"></i> เพิ่ม
                                </a>
                            </div>

                            <!-- ตารางแสดงข้อมูล -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">รายการการเบิกค่าใช้จ่าย</h3>
                                </div>
                                <div class="card-body">
                                    <table id="expensesTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>เลขที่การเบิก</th>
                                                <th>หัวข้อ</th>
                                                <th>โครงการ</th>
                                                <th>วันที่</th>
                                                <th>จำนวนเงิน</th>
                                                <th>สถานะ</th>
                                                <th>ผู้ทำเรื่องเบิก</th>
                                                <th>ผู้อนุมัติ</th>
                                                <th>การดำเนินการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (count($expenses) > 0) {
                                                foreach ($expenses as $expense) {
                                                    // กำหนด class สำหรับแสดงสถานะด้วยสี
                                                    $status_class = 'status-' . $expense['status'];
                                                    $status_text = [
                                                        'Pending' => 'รอพิจารณา',
                                                        'Approved' => 'อนุมัติแล้ว',
                                                        'Rejected' => 'ปฏิเสธ',
                                                        'Paid' => 'ชำระแล้ว'
                                                    ];
                                            ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($expense['expense_number']); ?></td>
                                                        <td><?php echo htmlspecialchars($expense['expense_title']); ?></td>
                                                        <td><?php echo htmlspecialchars($expense['project_name'] ?? 'ไม่มีข้อมูล'); ?></td>
                                                        <td><?php echo date('d/m/Y', strtotime($expense['expense_date'])); ?></td>
                                                        <td class="text-right"><?php echo number_format($expense['total_amount'], 2); ?></td>
                                                        <td>
                                                            <span class="status-badge <?php echo $status_class; ?>">
                                                                <?php echo $status_text[$expense['status']]; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            echo htmlspecialchars($expense['submitter_first_name'] . ' ' . $expense['submitter_last_name']);
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            echo $expense['approver_id']
                                                                ? htmlspecialchars($expense['approver_first_name'] . ' ' . $expense['approver_last_name'])
                                                                : 'ไม่มีข้อมูล';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <a href="view_claim.php?id=<?php echo $expense['expense_id']; ?>"
                                                                    class="btn btn-info btn-sm btn-sm-custom" title="ดูรายละเอียด">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>

                                                                <?php if (
                                                                    $expense['status'] == 'Pending' &&
                                                                    ($expense['submitter_id'] == $user_id || $role == 'Executive')
                                                                ) { ?>
                                                                    <a href="edit_claim.php?id=<?php echo $expense['expense_id']; ?>"
                                                                        class="btn btn-warning btn-sm btn-sm-custom" title="แก้ไข">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                <?php } ?>

                                                                <?php if (($role == 'Executive' || $role == 'Sale Supervisor') &&
                                                                    $expense['status'] == 'Pending'
                                                                ) { ?>
                                                                    <a href="approve_claim.php?id=<?php echo $expense['expense_id']; ?>"
                                                                        class="btn btn-success btn-sm btn-sm-custom" title="อนุมัติ">
                                                                        <i class="fas fa-check"></i>
                                                                    </a>
                                                                <?php } ?>

                                                                <?php if (($expense['submitter_id'] == $user_id || $role == 'Executive') &&
                                                                    $expense['status'] == 'Pending'
                                                                ) { ?>
                                                                    <button type="button" class="btn btn-danger btn-sm btn-sm-custom delete-expense"
                                                                        data-id="<?php echo $expense['expense_id']; ?>"
                                                                        data-number="<?php echo $expense['expense_number']; ?>"
                                                                        title="ลบ">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                <?php } ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="9" class="text-center">ไม่พบรายการการเบิกค่าใช้จ่าย</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Footer -->
        <?php include '../../include/footer.php'; ?>
    </div>
    <!-- ./wrapper -->

    <!-- JavaScript สำหรับตาราง DataTable และการลบข้อมูล -->
    <script>
        $(function() {
            // ตั้งค่า DataTable
            $("#expensesTable").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 10,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#expensesTable_wrapper .col-md-6:eq(0)');

            // จัดการการลบข้อมูล
            $('.delete-expense').click(function() {
                const expenseId = $(this).data('id');
                const expenseNumber = $(this).data('number');

                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: `คุณต้องการลบรายการเบิก ${expenseNumber} หรือไม่?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ลบรายการ!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ทำการลบข้อมูลโดยส่ง request ไปที่ API
                        $.ajax({
                            url: 'delete_claim.php',
                            type: 'POST',
                            data: {
                                expense_id: expenseId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'ลบสำเร็จ!',
                                        'รายการเบิกถูกลบเรียบร้อยแล้ว',
                                        'success'
                                    ).then(() => {
                                        // รีโหลดหน้าเพื่อแสดงข้อมูลล่าสุด
                                        location.reload();
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
                                    'เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>