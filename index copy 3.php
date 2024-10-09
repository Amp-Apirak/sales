<?php
session_start();
require_once 'config/condb.php';

if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];

$can_view_all = false;
$can_view_team = false;
$can_view_own = false;
$can_view_financial = true;

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

$current_year = date('Y');
$current_date = date('Y-m-d');
$filter_date_range = ["$current_year-01-01", $current_date];
$filter_team_id = $filter_team_id ?? '';
$filter_user_id = $filter_user_id ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filter_team_id = $_POST['team_id'] ?? $filter_team_id;
    $filter_user_id = $_POST['user_id'] ?? $filter_user_id;
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

$teams = [];
$team_members = [];

if ($can_view_all) {
    $team_query = "SELECT team_id, team_name FROM teams";
    $stmt = $condb->prepare($team_query);
    $stmt->execute();
    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $user_query = "SELECT user_id, CONCAT(first_name, ' ', last_name) as full_name, team_id 
                   FROM users 
                   WHERE role IN ('Seller', 'Sale Supervisor', 'Executive')";
    $stmt = $condb->prepare($user_query);
    $stmt->execute();
    $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($can_view_team) {
    $user_query = "SELECT user_id, CONCAT(first_name, ' ', last_name) as full_name
                   FROM users 
                   WHERE team_id = :team_id AND role IN ('Seller', 'Sale Supervisor', 'Executive')";
    $stmt = $condb->prepare($user_query);
    $stmt->bindParam(':team_id', $team_id);
    $stmt->execute();
    $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFilteredData($condb, $query, $params)
{
    $stmt = $condb->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

try {
    $product_query = "SELECT COUNT(*) as total_products FROM products";
    $result = getFilteredData($condb, $product_query, []);
    $total_products = $result['total_products'];

    $project_query = "SELECT COUNT(*) as total_projects FROM projects p
                      LEFT JOIN users u ON p.created_by = u.user_id
                      WHERE p.sales_date BETWEEN :start_date AND :end_date";
    $project_params = [
        ':start_date' => $filter_date_range[0],
        ':end_date' => $filter_date_range[1]
    ];

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

    $total_teams = 0;
    $total_team_members = 0;
    $team_query = "SELECT COUNT(*) as total_teams FROM teams";
    $result = getFilteredData($condb, $team_query, []);
    $total_teams = $result['total_teams'];

    $member_query = "SELECT COUNT(*) as total_members FROM users WHERE role IN ('Seller', 'Sale Supervisor', 'Executive')";
    $result = getFilteredData($condb, $member_query, []);
    $total_team_members = $result['total_members'];

    $total_profit = $total_sales - $total_cost;
    $profit_percentage = ($total_sales > 0) ? ($total_profit / $total_sales) * 100 : 0;
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
                    <div class="row px-2">
                        <div class="col-lg-3 col-md-6 mb-2">
                            <div class="small-box bg-info rounded shadow-sm">
                                <div class="inner py-3">
                                    <h4 class="mb-0"><?php echo number_format($total_teams); ?></h4>
                                    <p class="mb-0">จำนวนทีมทั้งหมด</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-2">
                            <div class="small-box bg-success rounded shadow-sm">
                                <div class="inner py-3">
                                    <h4 class="mb-0"><?php echo number_format($total_team_members); ?></h4>
                                    <p class="mb-0">จำนวนคนในทีมทั้งหมด</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-2">
                            <div class="small-box bg-warning rounded shadow-sm">
                                <div class="inner py-3">
                                    <h4 class="mb-0"><?php echo number_format($total_products); ?></h4>
                                    <p class="mb-0">จำนวน Product ทั้งหมด</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-box"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-2">
                            <div class="small-box bg-danger rounded shadow-sm">
                                <div class="inner py-3">
                                    <h4 class="mb-0"><?php echo number_format($total_projects); ?></h4>
                                    <p class="mb-0">จำนวน Project ทั้งหมด</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                            </div>
                        </div>

                        <?php if ($can_view_financial): ?>
                            <div class="col-lg-3 col-md-6 mb-2">
                                <div class="small-box bg-primary rounded shadow-sm">
                                    <div class="inner py-3">
                                        <h4 class="mb-0">฿<?php echo number_format($total_cost, 2); ?></h4>
                                        <p class="mb-0">ต้นทุนรวม Vat ทั้งหมด</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-money-bill"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-2">
                                <div class="small-box bg-secondary rounded shadow-sm">
                                    <div class="inner py-3">
                                        <h4 class="mb-0">฿<?php echo number_format($total_sales, 2); ?></h4>
                                        <p class="mb-0">ยอดขายรวม Vat ทั้งหมด</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-2">
                                <div class="small-box bg-success rounded shadow-sm">
                                    <div class="inner py-3">
                                        <h4 class="mb-0">฿<?php echo number_format($total_profit, 2); ?></h4>
                                        <p class="mb-0">กำไรทั้งสิ้น</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-2">
                                <div class="small-box bg-info rounded shadow-sm">
                                    <div class="inner py-3">
                                        <h4 class="mb-0"><?php echo number_format($profit_percentage, 2); ?>%</h4>
                                        <p class="mb-0">กำไรคิดเป็นเปอร์เซ็นต์</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-percentage"></i>
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