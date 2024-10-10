<?php
// เริ่มการทำงานของเซสชัน
session_start();

// นำเข้าไฟล์ config สำหรับการเชื่อมต่อฐานข้อมูล
require_once 'config/condb.php';

// ส่วนที่ 1: การตรวจสอบสิทธิ์และการกำหนดค่าเริ่มต้น
// -------------------------------------------------

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

// ดึงค่าที่เก็บในเซสชันมาใช้งาน
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];

// กำหนดสิทธิ์เริ่มต้นสำหรับการดูข้อมูล
$can_view_all = false;
$can_view_team = false;
$can_view_own = false;
$can_view_financial = true;

// ตั้งค่าการกำหนดสิทธิ์ตาม role
switch ($role) {
    case 'Executive':
        $can_view_all = true;
        break;
    case 'Sale Supervisor':
        $can_view_team = true;
        $filter_team_id = $team_id;
        break;
    case 'Seller':
        $can_view_own = true;
        $filter_user_id = $user_id;
        break;
    case 'Engineer':
        $can_view_own = true;
        $can_view_financial = false;
        $filter_user_id = $user_id;
        break;
}

// ส่วนที่ 2: การกำหนดช่วงเวลาและการกรองข้อมูล
// ------------------------------------------

// กำหนดช่วงเวลาเริ่มต้นในการกรองข้อมูล
$current_year = date('Y');
$current_date = date('Y-m-d');
$filter_date_range = ["$current_year-01-01", $current_date];
$filter_team_id = $filter_team_id ?? '';
$filter_user_id = $filter_user_id ?? '';

// ตรวจสอบการส่งข้อมูลผ่าน POST สำหรับการกรองข้อมูลเพิ่มเติม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filter_team_id = $_POST['team_id'] ?? $filter_team_id;
    $filter_user_id = $_POST['user_id'] ?? $filter_user_id;
    $filter_date_range_input = $_POST['date_range'] ?? '';

    // แปลงวันที่ที่ได้รับจากการกรอง
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

// ส่วนที่ 3: การดึงข้อมูลทีมและสมาชิกในทีม
// --------------------------------------

$teams = [];
$team_members = [];

if ($can_view_all) {
    // ดึงข้อมูลทีมทั้งหมด
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
} elseif ($can_view_team) {
    // ดึงข้อมูลสมาชิกในทีมตาม team_id ที่เลือก
    $user_query = "SELECT user_id, CONCAT(first_name, ' ', last_name) as full_name
                   FROM users 
                   WHERE team_id = :team_id AND role IN ('Seller', 'Sale Supervisor', 'Executive')";
    $stmt = $condb->prepare($user_query);
    $stmt->bindParam(':team_id', $team_id);
    $stmt->execute();
    $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ส่วนที่ 4: ฟังก์ชันสำหรับการดึงและประมวลผลข้อมูล
// ----------------------------------------------

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

// ส่วนที่ 5: การดึงและประมวลผลข้อมูลหลัก
// -------------------------------------

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

    // กรองข้อมูลตาม team_id และ user_id
    if ($filter_team_id && $can_view_all) {
        $project_query .= " AND u.team_id = :team_id";
        $project_params[':team_id'] = $filter_team_id;
    } elseif ($can_view_team) {
        $project_query .= " AND u.team_id = :team_id";
        $project_params[':team_id'] = $team_id;
    }

    if ($filter_user_id) {
        $project_query .= " AND p.created_by = :user_id";
        $project_params[':user_id'] = $filter_user_id;
    } elseif ($can_view_own) {
        $project_query .= " AND p.created_by = :user_id";
        $project_params[':user_id'] = $user_id;
    }

    $result = getFilteredData($condb, $project_query, $project_params);
    $total_projects = $result['total_projects'];

    // ดึงข้อมูลการเงิน (ถ้ามีสิทธิ์)
    $total_cost = 0;
    $total_sales = 0;
    if ($can_view_financial) {
        $query = "SELECT SUM(p.cost_vat) as total_cost, SUM(p.sale_vat) as total_sales 
                  FROM projects p
                  LEFT JOIN users u ON p.created_by = u.user_id
                  WHERE p.sales_date BETWEEN :start_date AND :end_date";
        $params = [
            ':start_date' => $filter_date_range[0],
            ':end_date' => $filter_date_range[1]
        ];

        // เพิ่มเงื่อนไขการกรองข้อมูลตาม team_id และ user_id
        if ($filter_team_id && $can_view_all) {
            $query .= " AND u.team_id = :team_id";
            $params[':team_id'] = $filter_team_id;
        } elseif ($can_view_team) {
            $query .= " AND u.team_id = :team_id";
            $params[':team_id'] = $team_id;
        }

        if ($filter_user_id) {
            $query .= " AND p.created_by = :user_id";
            $params[':user_id'] = $filter_user_id;
        } elseif ($can_view_own) {
            $query .= " AND p.created_by = :user_id";
            $params[':user_id'] = $user_id;
        }

        $result = getFilteredData($condb, $query, $params);
        $total_cost = $result['total_cost'] ?? 0;
        $total_sales = $result['total_sales'] ?? 0;
    }

    // นับจำนวนทีมและสมาชิกทีม
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

// ส่วนที่ 6: การดึงข้อมูลเพิ่มเติมสำหรับกราฟและการวิเคราะห์
// --------------------------------------------------------

// ดึงข้อมูลสถานะโครงการ
$project_status_query = "SELECT status, COUNT(*) as count FROM projects 
                         WHERE sales_date BETWEEN :start_date AND :end_date ";
if ($filter_team_id && $can_view_all) {
    $project_status_query .= "AND created_by IN (SELECT user_id FROM users WHERE team_id = :team_id) ";
} elseif ($can_view_team) {
    $project_status_query .= "AND created_by IN (SELECT user_id FROM users WHERE team_id = :team_id) ";
} elseif ($can_view_own) {
    $project_status_query .= "AND created_by = :user_id ";
}
$project_status_query .= "GROUP BY status";

$stmt = $condb->prepare($project_status_query);
$stmt->bindParam(':start_date', $filter_date_range[0]);
$stmt->bindParam(':end_date', $filter_date_range[1]);
if ($filter_team_id && $can_view_all) {
    $stmt->bindParam(':team_id', $filter_team_id);
} elseif ($can_view_team) {
    $stmt->bindParam(':team_id', $team_id);
} elseif ($can_view_own) {
    $stmt->bindParam(':user_id', $user_id);
}
$stmt->execute();
$project_status_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูล Product ที่ขายดีที่สุด
$top_products_query = "SELECT p.product_name, COUNT(*) as count FROM projects pr 
                       JOIN products p ON pr.product_id = p.product_id 
                       WHERE pr.sales_date BETWEEN :start_date AND :end_date ";
if ($filter_team_id && $can_view_all) {
    $top_products_query .= "AND pr.created_by IN (SELECT user_id FROM users WHERE team_id = :team_id) ";
} elseif ($can_view_team) {
    $top_products_query .= "AND pr.created_by IN (SELECT user_id FROM users WHERE team_id = :team_id) ";
} elseif ($can_view_own) {
    $top_products_query .= "AND pr.created_by = :user_id ";
}
$top_products_query .= "GROUP BY p.product_id ORDER BY count DESC LIMIT 10";

$stmt = $condb->prepare($top_products_query);
$stmt->bindParam(':start_date', $filter_date_range[0]);
$stmt->bindParam(':end_date', $filter_date_range[1]);
if ($filter_team_id && $can_view_all) {
    $stmt->bindParam(':team_id', $filter_team_id);
} elseif ($can_view_team) {
    $stmt->bindParam(':team_id', $team_id);
} elseif ($can_view_own) {
    $stmt->bindParam(':user_id', $user_id);
}
$stmt->execute();
$top_products_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลยอดขายแต่ละปี
$yearly_sales_query = "SELECT YEAR(sales_date) as year, SUM(sale_vat) as total_sales 
                       FROM projects 
                       WHERE sales_date BETWEEN :start_date AND :end_date ";
if ($filter_team_id && $can_view_all) {
    $yearly_sales_query .= "AND created_by IN (SELECT user_id FROM users WHERE team_id = :team_id) ";
} elseif ($can_view_team) {
    $yearly_sales_query .= "AND created_by IN (SELECT user_id FROM users WHERE team_id = :team_id) ";
} elseif ($can_view_own) {
    $yearly_sales_query .= "AND created_by = :user_id ";
}
$yearly_sales_query .= "GROUP BY YEAR(sales_date) ORDER BY year";

$stmt = $condb->prepare($yearly_sales_query);
$stmt->bindParam(':start_date', $filter_date_range[0]);
$stmt->bindParam(':end_date', $filter_date_range[1]);
if ($filter_team_id && $can_view_all) {
    $stmt->bindParam(':team_id', $filter_team_id);
} elseif ($can_view_team) {
    $stmt->bindParam(':team_id', $team_id);
} elseif ($can_view_own) {
    $stmt->bindParam(':user_id', $user_id);
}
$stmt->execute();
$yearly_sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลยอดขายของพนักงานแต่ละคน
$employee_sales_query = "SELECT u.first_name, u.last_name, SUM(p.sale_vat) as total_sales 
                         FROM projects p
                         JOIN users u ON p.created_by = u.user_id
                         WHERE p.sales_date BETWEEN :start_date AND :end_date ";
if ($filter_team_id && $can_view_all) {
    $employee_sales_query .= "AND u.team_id = :team_id ";
} elseif ($can_view_team) {
    $employee_sales_query .= "AND u.team_id = :team_id ";
} elseif ($can_view_own) {
    $employee_sales_query .= "AND p.created_by = :user_id ";
}
$employee_sales_query .= "GROUP BY p.created_by ORDER BY total_sales DESC LIMIT 10";

$stmt = $condb->prepare($employee_sales_query);
$stmt->bindParam(':start_date', $filter_date_range[0]);
$stmt->bindParam(':end_date', $filter_date_range[1]);
if ($filter_team_id && $can_view_all) {
    $stmt->bindParam(':team_id', $filter_team_id);
} elseif ($can_view_team) {
    $stmt->bindParam(':team_id', $team_id);
} elseif ($can_view_own) {
    $stmt->bindParam(':user_id', $user_id);
}
$stmt->execute();
$employee_sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลยอดขายรายเดือน
$monthly_sales_query = "SELECT DATE_FORMAT(sales_date, '%Y-%m') as month, SUM(sale_vat) as total_sales 
                        FROM projects 
                        WHERE sales_date BETWEEN :start_date AND :end_date ";
if ($filter_team_id && $can_view_all) {
    $monthly_sales_query .= "AND created_by IN (SELECT user_id FROM users WHERE team_id = :team_id) ";
} elseif ($can_view_team) {
    $monthly_sales_query .= "AND created_by IN (SELECT user_id FROM users WHERE team_id = :team_id) ";
} elseif ($can_view_own) {
    $monthly_sales_query .= "AND created_by = :user_id ";
}
$monthly_sales_query .= "GROUP BY DATE_FORMAT(sales_date, '%Y-%m') ORDER BY month";

$stmt = $condb->prepare($monthly_sales_query);
$stmt->bindParam(':start_date', $filter_date_range[0]);
$stmt->bindParam(':end_date', $filter_date_range[1]);
if ($filter_team_id && $can_view_all) {
    $stmt->bindParam(':team_id', $filter_team_id);
} elseif ($can_view_team) {
    $stmt->bindParam(':team_id', $team_id);
} elseif ($can_view_own) {
    $stmt->bindParam(':user_id', $user_id);
}
$stmt->execute();
$monthly_sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลยอดขายรายทีม
$team_sales_query = "SELECT t.team_name, SUM(p.sale_vat) as total_sales 
                     FROM projects p
                     JOIN users u ON p.created_by = u.user_id
                     JOIN teams t ON u.team_id = t.team_id
                     WHERE p.sales_date BETWEEN :start_date AND :end_date ";
if ($filter_team_id && $can_view_all) {
    $team_sales_query .= "AND u.team_id = :team_id ";
} elseif ($can_view_team) {
    $team_sales_query .= "AND u.team_id = :team_id ";
}
$team_sales_query .= "GROUP BY t.team_id ORDER BY total_sales DESC";

$stmt = $condb->prepare($team_sales_query);
$stmt->bindParam(':start_date', $filter_date_range[0]);
$stmt->bindParam(':end_date', $filter_date_range[1]);
if ($filter_team_id && $can_view_all) {
    $stmt->bindParam(':team_id', $filter_team_id);
} elseif ($can_view_team) {
    $stmt->bindParam(':team_id', $team_id);
}
$stmt->execute();
$team_sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Dashboard</title>
    <?php include 'include/header.php' ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <?php include 'css_dashboard.php' ?>

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
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="card card-statistic">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <h6 class="card-title text-muted mb-0 ml-3"><?php echo $team_label; ?></h6>
                                    </div>
                                    <h2 class="font-weight-bold mb-1"><?php echo number_format($total_teams); ?></h2>
                                    <p class="mb-0 text-muted"><span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span> จากเดือนที่แล้ว</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="card card-statistic">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-user-friends"></i>
                                        </div>
                                        <h6 class="card-title text-muted mb-0 ml-3"><?php echo $member_label; ?></h6>
                                    </div>
                                    <h2 class="font-weight-bold mb-1"><?php echo number_format($total_team_members); ?></h2>
                                    <p class="mb-0 text-muted"><span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 5.27%</span> จากเดือนที่แล้ว</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="card card-statistic">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-danger">
                                            <i class="fas fa-project-diagram"></i>
                                        </div>
                                        <h6 class="card-title text-muted mb-0 ml-3">จำนวน Project ของทีม</h6>
                                    </div>
                                    <h2 class="font-weight-bold mb-1"><?php echo number_format($total_projects); ?></h2>
                                    <p class="mb-0 text-muted"><span class="text-danger mr-2"><i class="fa fa-arrow-down"></i> 1.08%</span> จากเดือนที่แล้ว</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="card card-statistic">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <h6 class="card-title text-muted mb-0 ml-3">จำนวน Product ทั้งหมด</h6>
                                    </div>
                                    <h2 class="font-weight-bold mb-1"><?php echo number_format($total_products); ?></h2>
                                    <p class="mb-0 text-muted"><span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 2.37%</span> จากเดือนที่แล้ว</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($can_view_financial): ?>
                        <!-- ส่วนแสดงข้อมูลทางการเงิน -->
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="card bg-info">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-money-bill mr-1"></i>
                                            ต้นทุนรวม Vat ทั้งหมด
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h3>฿<?php echo number_format($total_cost, 2); ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="card bg-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-chart-line mr-1"></i>
                                            ยอดขายรวม Vat ทั้งหมด
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h3>฿<?php echo number_format($total_sales, 2); ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="card bg-success">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-coins mr-1"></i>
                                            กำไรทั้งสิ้น
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h3>฿<?php echo number_format($total_profit, 2); ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-percentage mr-1"></i>
                                            กำไรคิดเป็นเปอร์เซ็นต์
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h3><?php echo number_format($profit_percentage, 2); ?>%</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>


                <!-- หลังจากส่วนแสดงข้อมูลยอดขายราบปี และรายบุคคล  -->
                <?php if ($can_view_financial): ?>
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-line mr-1"></i>
                                        ยอดขายรายปี
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="yearlySalesChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-line mr-1"></i>
                                        ยอดขายรายเดือน
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlySalesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- แก้ไขส่วนของกราฟยอดขายรายทีมและรายพนักงาน -->
                <?php if ($can_view_financial): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-bar mr-1"></i>
                                        ยอดขายรายทีม
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="teamSalesChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-user-chart mr-1"></i>
                                        ยอดขายของพนักงาน (Top 10)
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="employeeSalesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- หลังจากส่วนแสดงข้อมูลทางการเงิน -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie mr-1"></i>
                                    สถานะโครงการ
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="projectStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-bar mr-1"></i>
                                    Product ที่ขายดีที่สุด
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="topProductsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>


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


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // สร้าง Pie chart สำหรับสถานะโครงการ
        var ctxStatus = document.getElementById('projectStatusChart').getContext('2d');
        var statusData = <?php echo json_encode($project_status_data); ?>;
        var labels = statusData.map(item => item.status);
        var data = statusData.map(item => item.count);
        var backgroundColors = [
            'rgba(255, 99, 132, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
        ];

        new Chart(ctxStatus, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true
                    }
                }
            }
        });

        // สร้างกราฟแนวนอนสำหรับ Product ที่ขายดีที่สุด
        var ctxProducts = document.getElementById('topProductsChart').getContext('2d');
        var productsData = <?php echo json_encode($top_products_data); ?>;
        var productLabels = productsData.map(item => item.product_name);
        var productData = productsData.map(item => item.count);

        new Chart(ctxProducts, {
            type: 'bar',
            data: {
                labels: productLabels,
                datasets: [{
                    label: 'จำนวนการขาย',
                    data: productData,
                    backgroundColor: 'rgba(75, 192, 192, 0.8)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                    },
                    title: {
                        display: true
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

<!-- แสดงข้อมูลยอดขายรายปี และแต่ละบุคคล -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... (โค้ดเดิมสำหรับกราฟอื่นๆ) ...

        // กราฟแท่งแสดงยอดขายแต่ละปี
        var ctxYearlySales = document.getElementById('yearlySalesChart').getContext('2d');
        var yearlySalesData = <?php echo json_encode($yearly_sales_data); ?>;
        var years = yearlySalesData.map(item => item.year);
        var salesData = yearlySalesData.map(item => item.total_sales);

        // สร้างชุดสีสำหรับแต่ละแท่ง
        var colors = [
            'rgba(255, 99, 132, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)',
            'rgba(199, 199, 199, 0.8)',
            'rgba(83, 102, 255, 0.8)',
            'rgba(40, 159, 64, 0.8)',
            'rgba(210, 105, 30, 0.8)'
        ];

        new Chart(ctxYearlySales, {
            type: 'bar',
            data: {
                labels: years,
                datasets: [{
                    label: 'ยอดขายรวม',
                    data: salesData,
                    backgroundColor: colors,
                    borderColor: colors.map(color => color.replace('0.8', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return '฿' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: 'ยอดขายรายปี'
                    }
                }
            }
        });

        // กราฟแสดงยอดขายของพนักงานแต่ละคน
        var ctxEmployeeSales = document.getElementById('employeeSalesChart').getContext('2d');
        var employeeSalesData = <?php echo json_encode($employee_sales_data); ?>;
        var employees = employeeSalesData.map(item => item.first_name + ' ' + item.last_name);
        var employeeSales = employeeSalesData.map(item => item.total_sales);

        new Chart(ctxEmployeeSales, {
            type: 'horizontalBar',
            data: {
                labels: employees,
                datasets: [{
                    label: 'ยอดขาย',
                    data: employeeSales,
                    backgroundColor: colors,
                    borderColor: colors.map(color => color.replace('0.8', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return '฿' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: 'ยอดขายของพนักงาน (Top 10)'
                    }
                }
            }
        });
    });
</script>

<!-- แสดงข้อมูลยอดขายรายเดือน และแต่ละทีม -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... (โค้ดเดิมสำหรับกราฟอื่นๆ) ...

        // กราฟเส้นแสดงยอดขายรายเดือน
        var ctxMonthlySales = document.getElementById('monthlySalesChart').getContext('2d');
        var monthlySalesData = <?php echo json_encode($monthly_sales_data); ?>;
        var months = monthlySalesData.map(item => {
            var date = new Date(item.month);
            return date.toLocaleString('th-TH', {
                month: 'short',
                year: 'numeric'
            });
        });
        var monthlySales = monthlySalesData.map(item => item.total_sales);

        new Chart(ctxMonthlySales, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'ยอดขายรายเดือน',
                    data: monthlySales,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return '฿' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: 'ยอดขายรายเดือน'
                    }
                }
            }
        });

        // กราฟแท่งเทียนแสดงยอดขายรายทีม
        var ctxTeamSales = document.getElementById('teamSalesChart').getContext('2d');
        var teamSalesData = <?php echo json_encode($team_sales_data); ?>;
        var teamNames = teamSalesData.map(item => item.team_name);
        var teamSales = teamSalesData.map(item => item.total_sales);

        new Chart(ctxTeamSales, {
            type: 'bar',
            data: {
                labels: teamNames,
                datasets: [{
                    label: 'ยอดขายรายทีม',
                    data: teamSales,
                    backgroundColor: 'rgba(255, 159, 64, 0.8)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return '฿' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: 'ยอดขายรายทีม'
                    }
                }
            }
        });
    });
</script>