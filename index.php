<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
require_once 'config/condb.php';

// ตรวจสอบการตั้งค่า Session เพื่อป้องกันกรณีที่ไม่ได้ล็อกอิน
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];

// กำหนดตัวแปรสำหรับเก็บสิทธิ์การเข้าถึง
$can_view_all = false;
$can_view_team = false;
$can_view_own = false;
$can_view_financial = true;

// ตรวจสอบ Role และกำหนดสิทธิ์การเข้าถึง
switch ($role) {
    case 'Executive':
        $can_view_all = true;
        break;
    case 'Sale Supervisor':
        $can_view_team = true;
        break;
    case 'Seller':
        $can_view_own = true;
        break;
    case 'Engineer':
        $can_view_own = true;
        $can_view_financial = false;
        break;
}

// ดึงข้อมูลสำหรับแสดงใน Card
$total_products = 0;
$total_projects = 0;
$total_cost = 0;
$total_sales = 0;

// กำหนดค่าตัวกรองเริ่มต้น
$current_year = date('Y');
$current_date = date('Y-m-d');
$filter_date_range = ["$current_year-01-01", $current_date]; // กำหนดช่วงเวลาเริ่มต้นเป็นปีปัจจุบันถึงวันที่ปัจจุบัน
$filter_team_id = '';
$filter_user_id = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filter_team_id = $_POST['team_id'] ?? '';
    $filter_user_id = $_POST['user_id'] ?? '';
    $filter_date_range_input = $_POST['date_range'] ?? '';

    if (!empty($filter_date_range_input)) {
        $date_parts = explode(' - ', $filter_date_range_input);
        if (count($date_parts) == 2) {
            $filter_date_range = [
                DateTime::createFromFormat('d/m/Y', trim($date_parts[0]))->format('Y-m-d'),
                DateTime::createFromFormat('d/m/Y', trim($date_parts[1]))->format('Y-m-d')
            ];
        }
    }
}

try {
    // ดึงจำนวน Product ทั้งหมด
    $query = "SELECT COUNT(*) as total_products FROM products";
    $stmt = $condb->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch();
    $total_products = $result['total_products'];

    // ดึงจำนวน Project ทั้งหมด
    $query = "SELECT COUNT(*) as total_projects FROM projects";
    $conditions = [];
    if ($filter_team_id) {
        $conditions[] = "team_id = :team_id";
    } elseif ($filter_user_id) {
        $conditions[] = "created_by = :user_id";
    }
    $conditions[] = "created_at BETWEEN :start_date AND :end_date";

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    $stmt = $condb->prepare($query);
    if ($filter_team_id) {
        $stmt->bindParam(':team_id', $filter_team_id);
    } elseif ($filter_user_id) {
        $stmt->bindParam(':user_id', $filter_user_id);
    }
    $stmt->bindParam(':start_date', $filter_date_range[0]);
    $stmt->bindParam(':end_date', $filter_date_range[1]);
    $stmt->execute();
    $result = $stmt->fetch();
    $total_projects = $result['total_projects'];

    // ดึงต้นทุนรวม Vat ทั้งหมด (สำหรับผู้ที่มีสิทธิ์เห็นข้อมูลการเงิน)
    if ($can_view_financial) {
        $query = "SELECT SUM(cost_vat) as total_cost FROM projects";
        $conditions = [];
        if ($filter_team_id) {
            $conditions[] = "team_id = :team_id";
        } elseif ($filter_user_id) {
            $conditions[] = "created_by = :user_id";
        }
        $conditions[] = "created_at BETWEEN :start_date AND :end_date";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        $stmt = $condb->prepare($query);
        if ($filter_team_id) {
            $stmt->bindParam(':team_id', $filter_team_id);
        } elseif ($filter_user_id) {
            $stmt->bindParam(':user_id', $filter_user_id);
        }
        $stmt->bindParam(':start_date', $filter_date_range[0]);
        $stmt->bindParam(':end_date', $filter_date_range[1]);
        $stmt->execute();
        $result = $stmt->fetch();
        $total_cost = $result['total_cost'] ?? 0;
    }

    // ดึงยอดขายรวม Vat ทั้งหมด (สำหรับผู้ที่มีสิทธิ์เห็นข้อมูลการเงิน)
    if ($can_view_financial) {
        $query = "SELECT SUM(sale_vat) as total_sales FROM projects";
        $conditions = [];
        if ($filter_team_id) {
            $conditions[] = "team_id = :team_id";
        } elseif ($filter_user_id) {
            $conditions[] = "created_by = :user_id";
        }
        $conditions[] = "created_at BETWEEN :start_date AND :end_date";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        $stmt = $condb->prepare($query);
        if ($filter_team_id) {
            $stmt->bindParam(':team_id', $filter_team_id);
        } elseif ($filter_user_id) {
            $stmt->bindParam(':user_id', $filter_user_id);
        }
        $stmt->bindParam(':start_date', $filter_date_range[0]);
        $stmt->bindParam(':end_date', $filter_date_range[1]);
        $stmt->execute();
        $result = $stmt->fetch();
        $total_sales = $result['total_sales'] ?? 0;
    }
} catch (PDOException $e) {
    error_log("Database query error: " . $e->getMessage());
}
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Dashboard</title>
    <?php include 'include/header.php' ?>

    <style>
        .small-box {
            border-radius: 0.25rem;
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
            display: block;
            margin-bottom: 10px;
            /* ลดจาก 20px เป็น 10px */
            position: relative;
        }

        .small-box>.inner {
            padding: 10px;
        }

        .small-box>.small-box-footer {
            background-color: rgba(0, 0, 0, .1);
            color: rgba(255, 255, 255, .8);
            display: block;
            padding: 3px 0;
            position: relative;
            text-align: center;
            text-decoration: none;
            z-index: 10;
        }

        .small-box>.small-box-footer:hover {
            background-color: rgba(0, 0, 0, .15);
            color: #fff;
        }

        .small-box h3 {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0 0 10px;
            padding: 0;
            white-space: nowrap;
        }

        .small-box p {
            font-size: 1rem;
        }

        .small-box .icon {
            color: rgba(0, 0, 0, .15);
            z-index: 0;
        }

        .small-box .icon>i {
            font-size: 90px;
            position: absolute;
            right: 15px;
            top: 15px;
            transition: transform .3s linear;
        }

        .small-box:hover .icon>i {
            transform: scale(1.1);
        }

        @media (max-width: 767.98px) {
            .small-box {
                text-align: center;
            }

            .small-box .icon {
                display: none;
            }

            .small-box p {
                font-size: 12px;
            }
        }

        .bg-info {
            background-color: #17a2b8 !important;
        }

        .bg-success {
            background-color: #28a745 !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
            color: #1f2d3d !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        .form-label {
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }

        .card-body {
            padding: 1rem;
        }

        @media (max-width: 767.98px) {
            .card-body {
                padding: 0.75rem;
            }
        }

        .card-body {
            padding: 0.5rem;
        }

        .form-label {
            margin-bottom: 0;
            font-size: 0.875rem;
        }

        .form-select-sm,
        .form-control-sm {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        @media (max-width: 767.98px) {
            .row>div {
                margin-bottom: 0.5rem;
            }
        }
    </style>

    <!-- เพิ่ม CSS สำหรับ Date Range Picker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/daterangepicker/3.1.0/daterangepicker.min.css" />
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <!-- Navbar -->
        <!-- Main Sidebar Container -->
        <?php include 'include/navbar.php' ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content การค้นหา -->
            <section class="content">
                <div class="container-fluid">
                    <!-- การ์ดสำหรับตัวกรอง -->
                    <div class="card mb-3">
                        <div class="card-body p-2">
                            <!-- HTML ส่วนแสดงข้อมูล Card และ Form สำหรับการกรองข้อมูล -->
                            <form method="POST" action="">
                                <div class="row align-items-center">
                                    <?php if ($can_view_all || $can_view_team): ?>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">ทีม:</span>
                                                </div>
                                                <select class="form-control form-control-sm" id="team_select" name="team_id">
                                                    <option value="">ทั้งหมด</option>
                                                    <?php
                                                    try {
                                                        $team_query = "SELECT team_id, team_name FROM teams";
                                                        $stmt = $condb->prepare($team_query);
                                                        $stmt->execute();
                                                        while ($row = $stmt->fetch()) {
                                                            $selected = ($row['team_id'] == $filter_team_id) ? 'selected' : '';
                                                            echo "<option value='" . htmlspecialchars($row['team_id']) . "' $selected>" . htmlspecialchars($row['team_name']) . "</option>";
                                                        }
                                                    } catch (PDOException $e) {
                                                        echo "<option value=''>เกิดข้อผิดพลาดในการดึงข้อมูลทีม</option>";
                                                        error_log("Team query error: " . $e->getMessage());
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">ช่วงเวลา:</span>
                                            </div>
                                            <input type="text" class="form-control form-control-sm" id="date_range" name="date_range" value="<?php echo htmlspecialchars(implode(' - ', array_map(function ($date) {
                                                                                                                                                    return date('d/m/Y', strtotime($date));
                                                                                                                                                }, $filter_date_range))); ?>">
                                        </div>
                                    </div>

                                    <?php if ($can_view_all || $can_view_team): ?>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">พนักงานขาย:</span>
                                                </div>
                                                <select class="form-control form-control-sm" id="user_select" name="user_id">
                                                    <option value="">ทั้งหมด</option>
                                                    <?php
                                                    try {
                                                        $user_query = "SELECT user_id, CONCAT(first_name, ' ', last_name) as full_name FROM users WHERE role IN ('Seller', 'Sale Supervisor')";
                                                        if ($can_view_team && !$can_view_all) {
                                                            $user_query .= " AND team_id = :team_id";
                                                        }
                                                        $stmt = $condb->prepare($user_query);
                                                        if ($can_view_team && !$can_view_all) {
                                                            $stmt->bindParam(':team_id', $team_id);
                                                        }
                                                        $stmt->execute();
                                                        while ($row = $stmt->fetch()) {
                                                            $selected = ($row['user_id'] == $filter_user_id) ? 'selected' : '';
                                                            echo "<option value='" . htmlspecialchars($row['user_id']) . "' $selected>" . htmlspecialchars($row['full_name']) . "</option>";
                                                        }
                                                    } catch (PDOException $e) {
                                                        echo "<option value=''>เกิดข้อผิดพลาดในการดึงข้อมูลพนักงานขาย</option>";
                                                        error_log("User query error: " . $e->getMessage());
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary btn-sm btn-block">ค้นหา</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.Main content การค้นหา -->

            <!-- ส่วนแสดงผล KPIs -->
            <section class="content">
                <div class="container-fluid">
                    <!-- HTML ส่วนแสดงข้อมูล Card -->
                    <div class="row px-2">
                        <div class="col-lg-3 col-md-6 mb-2">
                            <div class="small-box bg-info rounded shadow-sm">
                                <div class="inner py-3">
                                    <h3 id="total-products" class="mb-0"><?php echo $total_products; ?></h3>
                                    <p class="mb-0">จำนวน Product ทั้งหมด</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-box"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-2">
                            <div class="small-box bg-success rounded shadow-sm">
                                <div class="inner py-3">
                                    <h3 id="total-projects" class="mb-0"><?php echo $total_projects; ?></h3>
                                    <p class="mb-0">จำนวน Project ทั้งหมด</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                            </div>
                        </div>
                        <?php if ($can_view_financial): ?>
                            <div class="col-lg-3 col-md-6 mb-2">
                                <div class="small-box bg-warning rounded shadow-sm">
                                    <div class="inner py-3">
                                        <h3 id="total-cost" class="mb-0">฿<?php echo number_format($total_cost, 2); ?></h3>
                                        <p class="mb-0">ต้นทุนรวม Vat ทั้งหมด</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-money-bill"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-2">
                                <div class="small-box bg-danger rounded shadow-sm">
                                    <div class="inner py-3">
                                        <h3 id="total-sales" class="mb-0">฿<?php echo number_format($total_sales, 2); ?></h3>
                                        <p class="mb-0">ยอดขายรวม Vat ทั้งหมด</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>


        </div>
        <!-- /.content-wrapper -->

        <!-- // include footer -->
        <?php include('include/footer.php'); ?>
    </div>
    <!-- ./wrapper -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/th.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/daterangepicker/3.1.0/daterangepicker.min.js"></script>
    <script>
        $(function() {
            moment.locale('th');

            $('#date_range').daterangepicker({
                opens: 'right',
                drops: 'down',
                autoApply: true,
                locale: {
                    format: 'DD/MM/YYYY',
                    applyLabel: 'ตกลง',
                    cancelLabel: 'ยกเลิก',
                    fromLabel: 'จาก',
                    toLabel: 'ถึง',
                    customRangeLabel: 'กำหนดเอง',
                    daysOfWeek: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
                    monthNames: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
                    firstDay: 1
                },
                startDate: moment("<?php echo $filter_date_range[0]; ?>"),
                endDate: moment("<?php echo $filter_date_range[1]; ?>")
            });

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                // โค้ด AJAX สำหรับการโหลดข้อมูลและอัปเดต Dashboard
            });

        });
    </script>
</body>

</html>