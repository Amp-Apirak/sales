<?php
// เริ่มการทำงานของเซสชัน
session_start();

// นำเข้าไฟล์ config สำหรับการเชื่อมต่อฐานข้อมูล
require_once 'config/condb.php';

// ตรวจสอบสิทธิ์การเข้าถึงโดยตรวจสอบว่ามีการตั้งค่า role, team_id, user_id หรือไม่
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php"); // เปลี่ยนเส้นทางไปที่หน้า login ถ้าไม่มีการกำหนดค่าเหล่านี้
    exit; // หยุดการทำงานของสคริปต์
}

// ดึงค่าที่เก็บในเซสชันมาใช้งาน
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];

// กำหนดสิทธิ์เริ่มต้นสำหรับการดูข้อมูล
$can_view_all = false;
$can_view_team = false;
$can_view_own = false;
$can_view_financial = true; // สามารถดูข้อมูลการเงินได้ทุกตำแหน่ง ยกเว้น Engineer

// ตั้งค่าการกำหนดสิทธิ์ตาม role ที่ได้รับ
switch ($role) {
    case 'Executive':
        $can_view_all = true; // Executive สามารถดูข้อมูลทั้งหมด
        break;
    case 'Sale Supervisor':
        $can_view_team = true; // Sale Supervisor สามารถดูข้อมูลทีม
        $filter_team_id = $team_id;
        break;
    case 'Seller':
        $can_view_own = true; // Seller ดูได้เฉพาะข้อมูลของตัวเอง
        $filter_user_id = $user_id;
        break;
    case 'Engineer':
        $can_view_own = true; // Engineer ดูได้เฉพาะข้อมูลของตัวเอง แต่ไม่สามารถดูข้อมูลการเงิน
        $can_view_financial = false;
        $filter_user_id = $user_id;
        break;
}

// กำหนดช่วงเวลาเริ่มต้นในการกรองข้อมูล
$current_year = date('Y');
$current_date = date('Y-m-d');
$filter_date_range = ["$current_year-01-01", $current_date]; // กรองข้อมูลตั้งแต่ต้นปีจนถึงวันที่ปัจจุบัน
$filter_team_id = $filter_team_id ?? ''; // ถ้าไม่มีการกำหนดค่า $filter_team_id ให้กำหนดเป็นค่าว่าง
$filter_user_id = $filter_user_id ?? ''; // ถ้าไม่มีการกำหนดค่า $filter_user_id ให้กำหนดเป็นค่าว่าง

// ตรวจสอบการส่งข้อมูลผ่าน POST สำหรับการกรองข้อมูลเพิ่มเติม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filter_team_id = $_POST['team_id'] ?? $filter_team_id;
    $filter_user_id = $_POST['user_id'] ?? $filter_user_id;
    $filter_date_range_input = $_POST['date_range'] ?? '';

    // ตรวจสอบและแปลงวันที่ที่ได้รับจากการกรอง
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

// สร้างตัวแปรเก็บข้อมูลทีมและสมาชิกในทีม
$teams = [];
$team_members = [];

// ถ้าสามารถดูข้อมูลทั้งหมดได้
if ($can_view_all) {
    // ดึงข้อมูลทีมทั้งหมดจากฐานข้อมูล
    $team_query = "SELECT team_id, team_name FROM teams";
    $stmt = $condb->prepare($team_query);
    $stmt->execute();
    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ดึงข้อมูลผู้ใช้งานทั้งหมดในบทบาทที่เกี่ยวข้อง
    $user_query = "SELECT user_id, CONCAT(first_name, ' ', last_name) as full_name, team_id 
                   FROM users 
                   WHERE role IN ('Seller', 'Sale Supervisor', 'Executive')";
    $stmt = $condb->prepare($user_query);
    $stmt->execute();
    $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// ถ้าสามารถดูข้อมูลเฉพาะทีมได้
elseif ($can_view_team) {
    // ดึงข้อมูลสมาชิกในทีมตาม team_id ที่เลือก
    $user_query = "SELECT user_id, CONCAT(first_name, ' ', last_name) as full_name
                   FROM users 
                   WHERE team_id = :team_id AND role IN ('Seller', 'Sale Supervisor', 'Executive')";
    $stmt = $condb->prepare($user_query);
    $stmt->bindParam(':team_id', $team_id);
    $stmt->execute();
    $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ฟังก์ชันสำหรับดึงข้อมูลที่ผ่านการกรองจากฐานข้อมูล
function getFilteredData($condb, $query, $params)
{
    $stmt = $condb->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ฟังก์ชันสำหรับนับจำนวนทีม
function getTeamCount($condb, $role, $team_id, $filter_team_id = null)
{
    if ($role === 'Executive' && $filter_team_id) {
        $query = "SELECT COUNT(*) as total FROM teams WHERE team_id = :team_id";
        $stmt = $condb->prepare($query);
        $stmt->bindParam(':team_id', $filter_team_id, PDO::PARAM_STR);
    } elseif ($role === 'Executive') {
        $query = "SELECT COUNT(*) as total FROM teams";
        $stmt = $condb->prepare($query);
    } else {
        $query = "SELECT COUNT(*) as total FROM teams WHERE team_id = :team_id";
        $stmt = $condb->prepare($query);
        $stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR);
    }
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// ฟังก์ชันสำหรับนับจำนวนสมาชิกในทีม
function getTeamMemberCount($condb, $role, $team_id, $user_id, $filter_team_id = null, $filter_user_id = null)
{
    if ($role === 'Executive' && $filter_team_id) {
        $query = "SELECT COUNT(*) as total FROM users WHERE team_id = :team_id";
        $stmt = $condb->prepare($query);
        $stmt->bindParam(':team_id', $filter_team_id, PDO::PARAM_STR);
    } elseif ($role === 'Executive' && $filter_user_id) {
        $query = "SELECT COUNT(*) as total FROM users WHERE user_id = :user_id";
        $stmt = $condb->prepare($query);
        $stmt->bindParam(':user_id', $filter_user_id, PDO::PARAM_STR);
    } elseif ($role === 'Executive') {
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $condb->prepare($query);
    } elseif ($role === 'Sale Supervisor' && $filter_user_id) {
        $query = "SELECT COUNT(*) as total FROM users WHERE team_id = :team_id AND user_id = :user_id";
        $stmt = $condb->prepare($query);
        $stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $filter_user_id, PDO::PARAM_STR);
    } elseif ($role === 'Sale Supervisor') {
        $query = "SELECT COUNT(*) as total FROM users WHERE team_id = :team_id";
        $stmt = $condb->prepare($query);
        $stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR);
    } else {
        $query = "SELECT COUNT(*) as total FROM users WHERE user_id = :user_id";
        $stmt = $condb->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    }
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

try {
    // นับจำนวน Product ทั้งหมด
    $product_query = "SELECT COUNT(*) as total_products FROM products";
    $result = getFilteredData($condb, $product_query, []);
    $total_products = $result['total_products'];

    // นับจำนวน Project ตามช่วงเวลาที่กรอง
    $project_query = "SELECT COUNT(*) as total_projects FROM projects p
                      LEFT JOIN users u ON p.created_by = u.user_id
                      WHERE p.sales_date BETWEEN :start_date AND :end_date";
    $project_params = [
        ':start_date' => $filter_date_range[0],
        ':end_date' => $filter_date_range[1]
    ];

    // กรองข้อมูลตาม team_id ถ้าดูข้อมูลทั้งหมดได้
    if ($filter_team_id && $can_view_all) {
        $project_query .= " AND u.team_id = :team_id";
        $project_params[':team_id'] = $filter_team_id;
    }
    // กรองข้อมูลตาม team_id ถ้าดูข้อมูลเฉพาะทีมได้
    elseif ($can_view_team) {
        $project_query .= " AND u.team_id = :team_id";
        $project_params[':team_id'] = $team_id;
    }

    // กรองข้อมูลตาม user_id ถ้าดูข้อมูลเฉพาะผู้ใช้งานเอง
    if ($filter_user_id) {
        $project_query .= " AND p.created_by = :user_id";
        $project_params[':user_id'] = $filter_user_id;
    } elseif ($can_view_own) {
        $project_query .= " AND p.created_by = :user_id";
        $project_params[':user_id'] = $user_id;
    }

    $result = getFilteredData($condb, $project_query, $project_params);
    $total_projects = $result['total_projects'];

    // กำหนดตัวแปรเริ่มต้นสำหรับค่าใช้จ่ายและยอดขาย
    $total_cost = 0;
    $total_sales = 0;

    // ถ้าสามารถดูข้อมูลการเงินได้
    if ($can_view_financial) {
        $query = "SELECT SUM(p.cost_vat) as total_cost, SUM(p.sale_vat) as total_sales 
                  FROM projects p
                  LEFT JOIN users u ON p.created_by = u.user_id
                  WHERE p.sales_date BETWEEN :start_date AND :end_date";
        $params = [
            ':start_date' => $filter_date_range[0],
            ':end_date' => $filter_date_range[1]
        ];

        // กรองข้อมูลตาม team_id ถ้าดูข้อมูลทั้งหมดได้
        if ($filter_team_id && $can_view_all) {
            $query .= " AND u.team_id = :team_id";
            $params[':team_id'] = $filter_team_id;
        }
        // กรองข้อมูลตาม team_id ถ้าดูข้อมูลเฉพาะทีมได้
        elseif ($can_view_team) {
            $query .= " AND u.team_id = :team_id";
            $params[':team_id'] = $team_id;
        }

        // กรองข้อมูลตาม user_id ถ้าดูข้อมูลเฉพาะผู้ใช้งานเอง
        if ($filter_user_id) {
            $query .= " AND p.created_by = :user_id";
            $params[':user_id'] = $filter_user_id;
        } elseif ($can_view_own) {
            $query .= " AND p.created_by = :user_id";
            $params[':user_id'] = $user_id;
        }

        // ดึงข้อมูลยอดขายและค่าใช้จ่าย
        $result = getFilteredData($condb, $query, $params);
        $total_cost = $result['total_cost'] ?? 0;
        $total_sales = $result['total_sales'] ?? 0;
    }

    // นับจำนวนทีมและสมาชิกทีมตามเงื่อนไข
    $total_teams = getTeamCount($condb, $role, $team_id, $filter_team_id);
    $total_team_members = getTeamMemberCount($condb, $role, $team_id, $user_id, $filter_team_id, $filter_user_id);

    // คำนวณกำไรและเปอร์เซ็นต์กำไร
    $total_profit = $total_sales - $total_cost;
    $profit_percentage = ($total_sales > 0) ? ($total_profit / $total_sales) * 100 : 0;

    // ปรับข้อความแสดงผลตาม Role
    $team_label = ($role === 'Executive') ? "จำนวนทีมทั้งหมด" : "จำนวนทีมที่ฉันอยู่";
    $member_label = ($role === 'Executive' || $role === 'Sale Supervisor') ? "จำนวนคนในทีมทั้งหมด" : "จำนวนคนในทีมของฉัน";
} catch (PDOException $e) {
    // บันทึกข้อผิดพลาดถ้ามีปัญหาในการดึงข้อมูล
    error_log("Database query error: " . $e->getMessage());
    // อาจจะต้องจัดการข้อผิดพลาดเพิ่มเติม เช่น แสดงข้อความแจ้งเตือนผู้ใช้
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
            margin-bottom: 20px;
            position: relative;
        }

        .small-box>.inner {
            padding: 10px;
        }

        .small-box h4 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 0 10px 0;
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
            font-size: 70px;
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
        <!-- Navbar และ Sidebar -->
        <?php include 'include/navbar.php' ?>

        <!-- เนื้อหาหลัก -->
        <div class="content-wrapper">
            <!-- ส่วนหัวของหน้า -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- เนื้อหาหลัก -->
            <section class="content">
                <div class="container-fluid">
                    <!-- ส่วนฟอร์มค้นหาและกรองข้อมูล -->
                    <div class="card mb-3">
                        <div class="card-body p-2">
                            <form method="POST" action="">
                                <div class="row align-items-center">
                                    <?php if ($can_view_all): ?>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">ทีม:</span>
                                                </div>
                                                <select class="form-control form-control-sm" id="team_select" name="team_id">
                                                    <option value="">ทั้งหมด</option>
                                                    <?php foreach ($teams as $team): ?>
                                                        <option value="<?php echo htmlspecialchars($team['team_id']); ?>"
                                                            <?php echo ($team['team_id'] == $filter_team_id) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($team['team_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">ช่วงเวลา:</span>
                                            </div>
                                            <input type="text" class="form-control form-control-sm" id="date_range" name="date_range"
                                                value="<?php echo htmlspecialchars(implode(' - ', array_map(function ($date) {
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
                                                    <?php foreach ($team_members as $member): ?>
                                                        <option value="<?php echo htmlspecialchars($member['user_id']); ?>"
                                                            <?php echo ($member['user_id'] == $filter_user_id) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($member['full_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
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

                    <!-- ส่วนแสดงผล KPIs -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo number_format($total_teams); ?></h3>
                                    <p><?php echo $team_label; ?></p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?php echo number_format($total_team_members); ?></h3>
                                    <p><?php echo $member_label; ?></p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3><?php echo number_format($total_projects); ?></h3>
                                    <p>จำนวน Project ของทีม</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?php echo number_format($total_products); ?></h3>
                                    <p>จำนวน Product ทั้งหมดของบริษัท</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-box"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($can_view_financial): ?>
                        <!-- ส่วนแสดงข้อมูลทางการเงิน -->
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3>฿<?php echo number_format($total_cost, 2); ?></h3>
                                        <p>ต้นทุนรวม Vat ทั้งหมด</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-money-bill"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-secondary">
                                    <div class="inner">
                                        <h3>฿<?php echo number_format($total_sales, 2); ?></h3>
                                        <p>ยอดขายรวม Vat ทั้งหมด</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>฿<?php echo number_format($total_profit, 2); ?></h3>
                                        <p>กำไรทั้งสิ้น</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-light">
                                    <div class="inner">
                                        <h3><?php echo number_format($profit_percentage, 2); ?>%</h3>
                                        <p>กำไรคิดเป็นเปอร์เซ็นต์</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ส่วนสำหรับเพิ่มกราฟหรือตารางข้อมูลเพิ่มเติม -->
                <!-- เพิ่มส่วนนี้ตามความต้องการ -->

        </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include('include/footer.php'); ?>
    </div>

    <!-- JavaScript ที่จำเป็น -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/th.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/daterangepicker/3.1.0/daterangepicker.min.js"></script>
    <script>
        $(function() {
            // ตั้งค่าภาษาไทยสำหรับ moment.js
            moment.locale('th');

            // ตั้งค่า DateRangePicker
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

            // ฟังก์ชันสำหรับกรองพนักงานขายตามทีมที่เลือก
            $('#team_select').change(function() {
                var selectedTeam = $(this).val();
                var userSelect = $('#user_select');
                userSelect.empty();
                userSelect.append('<option value="">ทั้งหมด</option>');

                <?php if ($can_view_all): ?>
                    <?php foreach ($team_members as $member): ?>
                        if (selectedTeam == '' || selectedTeam == '<?php echo $member['team_id']; ?>') {
                            userSelect.append('<option value="<?php echo $member['user_id']; ?>"><?php echo $member['full_name']; ?></option>');
                        }
                    <?php endforeach; ?>
                <?php endif; ?>
            });

            // ทริกเกอร์การเปลี่ยนแปลงทีมเมื่อโหลดหน้า
            $('#team_select').trigger('change');
        });
    </script>
</body>

</html>