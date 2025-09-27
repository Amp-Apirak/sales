<?php
// เริ่มการทำงานของเซสชัน
session_start();

// นำเข้าไฟล์ config สำหรับการเชื่อมต่อฐานข้อมูล
require_once 'config/condb.php';
require_once 'config/validation.php';

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

// สำหรับ dropdown กรองข้อมูล
if ($can_view_all) {
    // Executive ดึงทีมทั้งหมด
    $team_query = "SELECT team_id, team_name FROM teams ORDER BY team_name ASC";
    $stmt = $condb->prepare($team_query);
    $stmt->execute();
    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ดึงผู้ใช้ทั้งหมดที่สามารถเป็นเจ้าของโครงการได้
    $user_query = "SELECT u.user_id, CONCAT(u.first_name, ' ', u.last_name) as full_name, t.team_name 
                   FROM users u 
                   LEFT JOIN teams t ON (SELECT team_id FROM user_teams ut WHERE ut.user_id = u.user_id AND ut.is_primary = 1 LIMIT 1) = t.team_id
                   WHERE u.role IN ('Seller', 'Sale Supervisor', 'Executive')
                   ORDER BY t.team_name, u.first_name";
    $stmt = $condb->prepare($user_query);
    $stmt->execute();
    $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

} elseif ($can_view_team) {
    // Sale Supervisor ดึงเฉพาะทีมที่ตัวเองสังกัด
    $team_ids = $_SESSION['team_ids'] ?? [];
    if (!empty($team_ids)) {
        $placeholders = implode(',', array_fill(0, count($team_ids), '?'));
        $team_query = "SELECT team_id, team_name FROM teams WHERE team_id IN ($placeholders) ORDER BY team_name ASC";
        $stmt = $condb->prepare($team_query);
        $stmt->execute($team_ids);
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ดึงสมาชิกในทีมทั้งหมดที่ตัวเองสังกัด
        $user_query = "SELECT u.user_id, CONCAT(u.first_name, ' ', u.last_name) as full_name 
                       FROM users u 
                       JOIN user_teams ut ON u.user_id = ut.user_id
                       WHERE ut.team_id IN ($placeholders) AND u.role IN ('Seller', 'Sale Supervisor', 'Executive')
                       GROUP BY u.user_id
                       ORDER BY u.first_name";
        $stmt = $condb->prepare($user_query);
        $stmt->execute($team_ids);
        $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// ส่วนที่ 4: ฟังก์ชันสำหรับการดึงและประมวลผลข้อมูล
// ----------------------------------------------

// ฟังก์ชันช่วยสำหรับสร้าง team filtering condition
function getTeamFilterCondition($can_view_team, $table_alias = 'p', $user_field = 'seller', &$params = []) {
    if (!$can_view_team) {
        return '';
    }

    $current_team_id = $_SESSION['team_id'] ?? 'ALL';
    if ($current_team_id === 'ALL') {
        // Show all teams user belongs to
        $team_ids = $_SESSION['team_ids'] ?? [];
        if (!empty($team_ids)) {
            $team_placeholders = [];
            foreach ($team_ids as $key => $id) {
                $placeholder = ':team_all_' . $key . '_' . rand();
                $team_placeholders[] = $placeholder;
                $params[$placeholder] = $id;
            }
            $in_clause = implode(',', $team_placeholders);
            return " AND {$table_alias}.{$user_field} IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id IN ($in_clause))";
        }
    } else {
        // Show specific team only
        $placeholder = ':current_team_' . rand();
        $params[$placeholder] = $current_team_id;
        return " AND {$table_alias}.{$user_field} IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id = $placeholder)";
    }

    return '';
}

// ฟังก์ชันสำหรับดึงข้อมูลที่ผ่านการกรองจากฐานข้อมูล
function getFilteredData($condb, $query, $params)
{
    $stmt = $condb->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ฟังก์ชันสำหรับนับจำนวนทีม
function getTeamCount($condb, $role, $user_id, $filter_team_id = null)
{
    if ($role === 'Executive') {
        if ($filter_team_id) {
            return 1; // ถ้ากรองมาแล้ว ก็มีแค่ 1 ทีม
        }
        $query = "SELECT COUNT(*) as total FROM teams";
        $stmt = $condb->prepare($query);
    } else {
        // นับจำนวนทีมที่ user คนปัจจุบันสังกัดอยู่
        $query = "SELECT COUNT(DISTINCT team_id) as total FROM user_teams WHERE user_id = :user_id";
        $stmt = $condb->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    }
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// ฟังก์ชันสำหรับนับจำนวนสมาชิกในทีม
function getTeamMemberCount($condb, $role, $user_id, $team_ids, $filter_team_id = null, $filter_user_id = null)
{
    if ($role === 'Executive') {
        if ($filter_user_id) {
            return 1; // กรองผู้ใช้มาแล้ว
        }
        if ($filter_team_id) {
            $query = "SELECT COUNT(DISTINCT user_id) as total FROM user_teams WHERE team_id = :team_id";
            $stmt = $condb->prepare($query);
            $stmt->bindParam(':team_id', $filter_team_id, PDO::PARAM_STR);
        } else {
            $query = "SELECT COUNT(*) as total FROM users";
            $stmt = $condb->prepare($query);
        }
    } else { // Sale Supervisor, Seller, Engineer
        if ($filter_user_id) {
            return 1;
        }
        $placeholders = implode(',', array_fill(0, count($team_ids), '?'));
        $query = "SELECT COUNT(DISTINCT user_id) as total FROM user_teams WHERE team_id IN ($placeholders)";
        $stmt = $condb->prepare($query);
        $stmt->execute($team_ids);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
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

    // --- Refactored Project Count Query ---
    $project_query = "SELECT COUNT(*) as total_projects FROM projects p WHERE p.sales_date BETWEEN :start_date AND :end_date";
    $project_params = [
        ':start_date' => $filter_date_range[0],
        ':end_date' => $filter_date_range[1]
    ];

    if ($filter_user_id) {
        $project_query .= " AND p.seller = :user_id";
        $project_params[':user_id'] = $filter_user_id;
    } elseif ($can_view_all && $filter_team_id) {
        $project_query .= " AND p.seller IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id = :team_id)";
        $project_params[':team_id'] = $filter_team_id;
    } elseif ($can_view_team) {
        $project_query .= getTeamFilterCondition($can_view_team, 'p', 'seller', $project_params);
    } elseif ($can_view_own) {
        $project_query .= " AND p.seller = :user_id";
        $project_params[':user_id'] = $user_id;
    }

    $result = getFilteredData($condb, $project_query, $project_params);
    $total_projects = $result['total_projects'] ?? 0;

    // --- Refactored Financial Query ---
    $total_cost = 0;
    $total_sales = 0;
    if ($can_view_financial) {
        $query = "SELECT SUM(p.cost_no_vat) as total_cost, SUM(p.sale_no_vat) as total_sales FROM projects p WHERE p.sales_date BETWEEN :start_date AND :end_date";
        $params = [
            ':start_date' => $filter_date_range[0],
            ':end_date' => $filter_date_range[1]
        ];

        if ($filter_user_id) {
            $query .= " AND p.seller = :user_id";
            $params[':user_id'] = $filter_user_id;
        } elseif ($can_view_all && $filter_team_id) {
            $query .= " AND p.seller IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id = :team_id)";
            $params[':team_id'] = $filter_team_id;
        } elseif ($can_view_team) {
            $query .= getTeamFilterCondition($can_view_team, 'p', 'seller', $params);
        } elseif ($can_view_own) {
            $query .= " AND p.seller = :user_id";
            $params[':user_id'] = $user_id;
        }

        $result = getFilteredData($condb, $query, $params);
        $total_cost = $result['total_cost'] ?? 0;
        $total_sales = $result['total_sales'] ?? 0;
    }

    // --- Other calculations (remain the same) ---
    $total_teams = getTeamCount($condb, $role, $user_id, $filter_team_id);
    $total_team_members = getTeamMemberCount($condb, $role, $user_id, $_SESSION['team_ids'] ?? [], $filter_team_id, $filter_user_id);
    $total_profit = $total_sales - $total_cost;
    $profit_percentage = ($total_sales > 0) ? ($total_profit / $total_sales) * 100 : 0;
    $team_label = ($role === 'Executive') ? "จำนวนทีมทั้งหมด" : "จำนวนทีมที่ฉันอยู่";
    $member_label = ($role === 'Executive' || $role === 'Sale Supervisor') ? "จำนวนคนทั้งหมด" : "จำนวนคนในทีมของฉัน";

    // จัดเตรียมข้อความ tooltip อธิบายเงื่อนไขการคำนวณ
    $formatted_start = date('d/m/Y', strtotime($filter_date_range[0] ?? $current_year . '-01-01'));
    $formatted_end = date('d/m/Y', strtotime($filter_date_range[1] ?? $current_date));
    $date_range_text = "ช่วงวันที่ {$formatted_start} ถึง {$formatted_end}";

    $scope_text = 'ทุกโครงการในระบบ';
    if (!empty($filter_user_id)) {
        $scope_text = 'เฉพาะโครงการของพนักงานที่เลือกในตัวกรอง';
    } elseif ($can_view_all && !empty($filter_team_id)) {
        $scope_text = 'เฉพาะโครงการของทีมที่เลือกในตัวกรอง';
    } elseif ($can_view_team) {
        $current_team_switch = $_SESSION['team_id'] ?? 'ALL';
        $current_team_name = $_SESSION['team_name'] ?? '';
        if ($current_team_switch === 'ALL') {
            $scope_text = 'ทุกทีมที่คุณสังกัด (โหมด All Teams)';
        } else {
            $scope_text = 'เฉพาะโครงการของทีม ' . ($current_team_name !== '' ? $current_team_name : 'ที่เลือกใน Team Switcher');
        }
    } elseif ($can_view_own) {
        $scope_text = 'เฉพาะโครงการที่คุณรับผิดชอบ';
    }

    $filter_note = (!empty($filter_user_id) || (!empty($filter_team_id) && $can_view_all))
        ? ' (อิงตามตัวกรองที่เลือก)'
        : '';

    if ($role === 'Executive') {
        $tooltip_team = 'นับจำนวนทีมทั้งหมดในระบบจากตารางทีม' . $filter_note . '';
        $tooltip_members = 'นับจำนวนผู้ใช้งานทั้งหมดที่อยู่ในระบบหรือในทีมที่คุณเลือกจากตัวกรอง';
    } elseif ($role === 'Sale Supervisor') {
        $tooltip_team = 'นับจำนวนทีมที่คุณสังกัดตาม Team Switcher หรือตัวกรองในปัจจุบัน';
        $tooltip_members = 'นับจำนวนสมาชิกในทุกทีมที่คุณดูอยู่ขณะนี้ (ไม่รวมทีมที่ไม่ได้รับสิทธิ์)';
    } else {
        $tooltip_team = 'นับจำนวนทีมที่คุณสังกัดอยู่ (จากข้อมูล user_teams)';
        $tooltip_members = 'นับจำนวนสมาชิกภายในทีมที่คุณสังกัดอยู่เท่านั้น';
    }

    if (!empty($filter_user_id) || (!empty($filter_team_id) && $can_view_all)) {
        $tooltip_members .= ' (จำกัดตามตัวกรองที่เลือก)';
    }

    $tooltip_total_projects = 'นับจำนวนโครงการทั้งหมดใน ' . $scope_text . ' ' . $date_range_text;
    $tooltip_total_products = 'นับจำนวนสินค้า/บริการทั้งหมดที่บันทึกไว้ในระบบ (ตาราง products)';

    $ongoing_statuses = ['นำเสนอโครงการ (Presentations)', 'ใบเสนอราคา (Quotation)', 'ยื่นประมูล (Bidding)', 'รอการพิจารณา (On Hold)'];
    $tooltip_win_projects = 'นับจำนวนโครงการสถานะ "ชนะ (Win)" ใน ' . $scope_text . ' ' . $date_range_text;
    $tooltip_ongoing_projects = 'นับจำนวนโครงการสถานะ ' . implode(', ', $ongoing_statuses) . ' ใน ' . $scope_text . ' ' . $date_range_text;
    $tooltip_loss_projects = 'นับจำนวนโครงการสถานะ "แพ้ (Loss)" ใน ' . $scope_text . ' ' . $date_range_text;
    $tooltip_canceled_projects = 'นับจำนวนโครงการสถานะ "ยกเลิก (Cancled)" ใน ' . $scope_text . ' ' . $date_range_text;

    $tooltip_total_sales = 'ยอดขายรวมไม่รวมภาษีของ ' . $scope_text . ' ' . $date_range_text;
    $tooltip_total_cost = 'ต้นทุนรวมไม่รวมภาษีของ ' . $scope_text . ' ' . $date_range_text;
    $tooltip_total_profit = 'กำไร = ยอดขายรวม - ต้นทุนรวม (ไม่รวม VAT) ใน ' . $scope_text . ' ' . $date_range_text;
    $tooltip_profit_percentage = 'คำนวณจาก (กำไรรวม ÷ ยอดขายรวม) × 100 หากยอดขายเป็น 0 จะแสดง 0%';

    $tooltip_win_sales = 'ยอดขายไม่รวม VAT ของโครงการที่สถานะ "ชนะ (Win)" ใน ' . $scope_text . ' ' . $date_range_text;
    $tooltip_win_cost = 'ต้นทุนไม่รวม VAT ของโครงการที่สถานะ "ชนะ (Win)" ใน ' . $scope_text . ' ' . $date_range_text;
    $tooltip_win_profit = 'กำไรของโครงการที่สถานะ "ชนะ (Win)" (ยอดขาย - ต้นทุน)';
    $tooltip_win_profit_percentage = 'เปอร์เซ็นต์กำไรของโครงการที่สถานะ "ชนะ (Win)" คำนวณจาก (กำไร ÷ ยอดขาย Win) × 100';

} catch (PDOException $e) {
    error_log("Database query error in Section 5: " . $e->getMessage());
    // --- Add default values to prevent warnings ---
    $total_products = 0;
    $total_projects = 0;
    $total_cost = 0;
    $total_sales = 0;
    $total_teams = 0;
    $total_team_members = 0;
    $total_profit = 0;
    $profit_percentage = 0;
    $team_label = 'จำนวนทีม';
    $member_label = 'จำนวนสมาชิก';
}

// ส่วนที่ 6: การดึงข้อมูลเพิ่มเติมสำหรับกราฟและการวิเคราะห์
// --------------------------------------------------------

// --- 1. Project Status Query ---
$project_status_query = "SELECT p.status, COUNT(*) as count FROM projects p WHERE p.sales_date BETWEEN :start_date AND :end_date ";
$project_status_params = [
    ':start_date' => $filter_date_range[0],
    ':end_date' => $filter_date_range[1]
];
if ($filter_user_id) {
    $project_status_query .= "AND p.created_by = :user_id ";
    $project_status_params[':user_id'] = $filter_user_id;
} elseif ($filter_team_id && $can_view_all) {
    $project_status_query .= "AND p.created_by IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id = :team_id) ";
    $project_status_params[':team_id'] = $filter_team_id;
} elseif ($can_view_team) {
    $project_status_query .= getTeamFilterCondition($can_view_team, 'p', 'created_by', $project_status_params);
} elseif ($can_view_own) {
    $project_status_query .= "AND p.created_by = :user_id ";
    $project_status_params[':user_id'] = $user_id;
}
$project_status_query .= "GROUP BY status";
$stmt = $condb->prepare($project_status_query);
$stmt->execute($project_status_params);
$project_status_data = $stmt->fetchAll(PDO::FETCH_ASSOC);


// --- 2. Top Products Query ---
$top_products_query = "SELECT p.product_name, COUNT(*) as count FROM projects pr JOIN products p ON pr.product_id = p.product_id WHERE pr.sales_date BETWEEN :start_date AND :end_date ";
$top_products_params = [
    ':start_date' => $filter_date_range[0],
    ':end_date' => $filter_date_range[1]
];
if ($filter_user_id) {
    $top_products_query .= "AND pr.created_by = :user_id ";
    $top_products_params[':user_id'] = $filter_user_id;
} elseif ($filter_team_id && $can_view_all) {
    $top_products_query .= "AND pr.created_by IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id = :team_id) ";
    $top_products_params[':team_id'] = $filter_team_id;
} elseif ($can_view_team) {
    $top_products_query .= getTeamFilterCondition($can_view_team, 'pr', 'created_by', $top_products_params);
} elseif ($can_view_own) {
    $top_products_query .= "AND pr.created_by = :user_id ";
    $top_products_params[':user_id'] = $user_id;
}
$top_products_query .= "GROUP BY p.product_id ORDER BY count DESC LIMIT 10";
$stmt = $condb->prepare($top_products_query);
$stmt->execute($top_products_params);
$top_products_data = $stmt->fetchAll(PDO::FETCH_ASSOC);


// --- 3. Yearly Sales Query ---
$yearly_sales_query = "SELECT YEAR(sales_date) as year, SUM(sale_vat) as total_sales FROM projects p WHERE sales_date BETWEEN :start_date AND :end_date ";
$yearly_sales_params = [
    ':start_date' => $filter_date_range[0],
    ':end_date' => $filter_date_range[1]
];
if ($filter_user_id) {
    $yearly_sales_query .= "AND p.created_by = :user_id ";
    $yearly_sales_params[':user_id'] = $filter_user_id;
} elseif ($filter_team_id && $can_view_all) {
    $yearly_sales_query .= "AND p.created_by IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id = :team_id) ";
    $yearly_sales_params[':team_id'] = $filter_team_id;
} elseif ($can_view_team) {
    $yearly_sales_query .= getTeamFilterCondition($can_view_team, 'p', 'created_by', $yearly_sales_params);
} elseif ($can_view_own) {
    $yearly_sales_query .= "AND p.created_by = :user_id ";
    $yearly_sales_params[':user_id'] = $user_id;
}
$yearly_sales_query .= "GROUP BY YEAR(sales_date) ORDER BY year";
$stmt = $condb->prepare($yearly_sales_query);
$stmt->execute($yearly_sales_params);
$yearly_sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);


// --- 4. Employee Sales Query ---
$employee_sales_query = "SELECT u.first_name, u.last_name, SUM(p.sale_vat) as total_sales FROM projects p JOIN users u ON p.seller = u.user_id WHERE p.sales_date BETWEEN :start_date AND :end_date ";
$employee_sales_params = [
    ':start_date' => $filter_date_range[0],
    ':end_date' => $filter_date_range[1]
];
if ($filter_user_id) {
    $employee_sales_query .= "AND p.seller = :user_id ";
    $employee_sales_params[':user_id'] = $filter_user_id;
} elseif ($filter_team_id && $can_view_all) {
    $employee_sales_query .= "AND p.seller IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id = :team_id) ";
    $employee_sales_params[':team_id'] = $filter_team_id;
} elseif ($can_view_team) {
    $employee_sales_query .= getTeamFilterCondition($can_view_team, 'p', 'seller', $employee_sales_params);
} elseif ($can_view_own) {
    $employee_sales_query .= "AND p.seller = :user_id ";
    $employee_sales_params[':user_id'] = $user_id;
}
$employee_sales_query .= "GROUP BY p.seller ORDER BY total_sales DESC LIMIT 10";
$stmt = $condb->prepare($employee_sales_query);
$stmt->execute($employee_sales_params);
$employee_sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);


// --- 5. Monthly Sales Query ---
$monthly_sales_query = "SELECT DATE_FORMAT(sales_date, '%Y-%m') as month, SUM(sale_vat) as total_sales FROM projects p WHERE sales_date BETWEEN :start_date AND :end_date ";
$monthly_sales_params = [
    ':start_date' => $filter_date_range[0],
    ':end_date' => $filter_date_range[1]
];
if ($filter_user_id) {
    $monthly_sales_query .= "AND p.created_by = :user_id ";
    $monthly_sales_params[':user_id'] = $filter_user_id;
} elseif ($filter_team_id && $can_view_all) {
    $monthly_sales_query .= "AND p.created_by IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id = :team_id) ";
    $monthly_sales_params[':team_id'] = $filter_team_id;
} elseif ($can_view_team) {
    $monthly_sales_query .= getTeamFilterCondition($can_view_team, 'p', 'created_by', $monthly_sales_params);
} elseif ($can_view_own) {
    $monthly_sales_query .= "AND p.created_by = :user_id ";
    $monthly_sales_params[':user_id'] = $user_id;
}
$monthly_sales_query .= "GROUP BY DATE_FORMAT(sales_date, '%Y-%m') ORDER BY month";
$stmt = $condb->prepare($monthly_sales_query);
$stmt->execute($monthly_sales_params);
$monthly_sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);


// --- 6. Team Sales Query (Already fixed, but included for completeness) ---
$team_sales_query = "SELECT t.team_name, SUM(p.sale_vat) as total_sales 
                     FROM projects p
                     JOIN users u ON p.seller = u.user_id
                     JOIN user_teams ut ON u.user_id = ut.user_id
                     JOIN teams t ON ut.team_id = t.team_id
                     WHERE p.sales_date BETWEEN :start_date AND :end_date ";
$team_sales_params = [
    ':start_date' => $filter_date_range[0],
    ':end_date' => $filter_date_range[1]
];
if ($filter_team_id && $can_view_all) {
    $team_sales_query .= "AND t.team_id = :team_id ";
    $team_sales_params[':team_id'] = $filter_team_id;
} elseif ($can_view_team) {
    // Team sales query needs special handling for team filtering
    $current_team_id = $_SESSION['team_id'] ?? 'ALL';
    if ($current_team_id === 'ALL') {
        // Show all teams user belongs to
        $team_ids = $_SESSION['team_ids'] ?? [];
        if (!empty($team_ids)) {
            $team_placeholders = [];
            foreach ($team_ids as $key => $id) {
                $placeholder = ':ts_team_' . $key;
                $team_placeholders[] = $placeholder;
                $team_sales_params[$placeholder] = $id;
            }
            $in_clause = implode(',', $team_placeholders);
            $team_sales_query .= "AND t.team_id IN ($in_clause) ";
        }
    } else {
        // Show specific team only
        $team_sales_query .= "AND t.team_id = :current_team_ts ";
        $team_sales_params[':current_team_ts'] = $current_team_id;
    }
}
$team_sales_query .= "GROUP BY t.team_id ORDER BY total_sales DESC";
$stmt = $condb->prepare($team_sales_query);
$stmt->execute($team_sales_params);
$team_sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);



// ส่วนที่ 7: การดึงข้อมูลนับจำนวนโครงการตามสถานะ
// --------------------------------------------------------

// ฟังก์ชันสำหรับนับจำนวนโครงการตามสถานะ
function countProjectsByStatus($condb, $status_list, $role, $team_id, $user_id, $filter_team_id = null, $filter_user_id = null, $filter_date_range = null)
{
    // ใช้ quote เพื่อป้องกัน SQL Injection ในสถานะ
    $status_in_clause = implode(',', array_map(fn($s) => $condb->quote($s), $status_list));
    $query = "SELECT COUNT(*) as count FROM projects p WHERE p.status IN ($status_in_clause)";
    
    $params = [];

    // เพิ่มเงื่อนไขการกรองตามช่วงวันที่
    if ($filter_date_range) {
        $query .= " AND p.sales_date BETWEEN :start_date AND :end_date";
        $params[':start_date'] = $filter_date_range[0];
        $params[':end_date'] = $filter_date_range[1];
    }

    // จัดลำดับการกรองใหม่: user, team, role
    if ($filter_user_id) {
        // ถ้ามีการกรอง user มา ให้ใช้ user นั้นเสมอ
        $query .= " AND p.seller = :filter_user_id";
        $params[':filter_user_id'] = $filter_user_id;
    } elseif ($filter_team_id && $role === 'Executive') {
        // Executive กรองตามทีม
        $query .= " AND p.seller IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id = :team_id)";
        $params[':team_id'] = $filter_team_id;
    } elseif ($role === 'Sale Supervisor') {
        // Supervisor ดูได้ทุกทีมที่ตัวเองสังกัด หรือเฉพาะทีมที่เลือก
        $current_team_id = $_SESSION['team_id'] ?? 'ALL';
        if ($current_team_id === 'ALL') {
            // Show all teams user belongs to
            $team_ids = $_SESSION['team_ids'] ?? [];
            if (!empty($team_ids)) {
                $team_placeholders = [];
                foreach ($team_ids as $key => $id) {
                    $placeholder = ':cps_team_' . $key;
                    $team_placeholders[] = $placeholder;
                    $params[$placeholder] = $id;
                }
                $in_clause = implode(',', $team_placeholders);
                $query .= " AND p.seller IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id IN ($in_clause))";
            } else {
                // ถ้า Supervisor ไม่มีทีม ให้เห็นแค่ของตัวเอง
                $query .= " AND p.seller = :user_id";
                $params[':user_id'] = $user_id;
            }
        } else {
            // Show specific team only
            $query .= " AND p.seller IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id = :current_team_cps)";
            $params[':current_team_cps'] = $current_team_id;
        }
    } elseif ($role === 'Seller' || $role === 'Engineer') {
        // Seller/Engineer ดูได้แค่ของตัวเอง
        $query .= " AND p.seller = :user_id";
        $params[':user_id'] = $user_id;
    }

    $stmt = $condb->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] ?? 0;
}

// เรียกใช้ฟังก์ชันเพื่อนับจำนวนโครงการแต่ละสถานะ
try {
    // นับจำนวนโครงการสถานะชนะ (Win)
    $win_projects = countProjectsByStatus(
        $condb,
        ['ชนะ (Win)'],
        $role,
        $team_id,
        $user_id,
        $filter_team_id,
        $filter_user_id,
        $filter_date_range
    );

    // นับจำนวนโครงการที่กำลังดำเนินการ
    $ongoing_projects = countProjectsByStatus(
        $condb,
        ['นำเสนอโครงการ (Presentations)', 'ใบเสนอราคา (Quotation)', 'ยื่นประมูล (Bidding)', 'รอการพิจารณา (On Hold)'],
        $role,
        $team_id,
        $user_id,
        $filter_team_id,
        $filter_user_id,
        $filter_date_range
    );

    // นับจำนวนโครงการสถานะแพ้ (Loss)
    $loss_projects = countProjectsByStatus(
        $condb,
        ['แพ้ (Loss)'],
        $role,
        $team_id,
        $user_id,
        $filter_team_id,
        $filter_user_id,
        $filter_date_range
    );

    // นับจำนวนโครงการสถานะยกเลิก (Cancled)
    $canceled_projects = countProjectsByStatus(
        $condb,
        ['ยกเลิก (Cancled)'], // เฉพาะสถานะยกเลิกเท่านั้น
        $role,
        $team_id,
        $user_id,
        $filter_team_id,
        $filter_user_id,
        $filter_date_range,
    );
} catch (PDOException $e) {
    // จัดการข้อผิดพลาด
    error_log("Error counting projects by status: " . $e->getMessage());
    $win_projects = $ongoing_projects = $loss_projects = $total_all_projects = 0;
}



// ส่วนที่ 8: การดึงข้อมูลนับจำนวนโครงการตามสถานะ Win (ชนะ) เพื่อ Sum ตัวเลข
// --------------------------------------------------------

// ฟังก์ชันดึงข้อมูลสรุปโครงการที่มีสถานะชนะ (Win)
function getWinProjectSummary($condb, $role, $team_id, $user_id, $filter_team_id = null, $filter_user_id = null, $filter_date_range = null)
{
    // สร้างคำสั่ง SQL พื้นฐานเพื่อดึงข้อมูลเฉพาะโครงการที่มีสถานะชนะ (Win)
    $query = "SELECT 
                SUM(sale_no_vat) as total_win_sales, 
                SUM(cost_no_vat) as total_win_cost, 
                SUM(gross_profit) as total_win_profit
             FROM projects p
             WHERE p.status = 'ชนะ (Win)'";

    $params = [];

    // เพิ่มเงื่อนไขกรองตามช่วงวันที่
    if ($filter_date_range) {
        $query .= " AND p.sales_date BETWEEN :start_date AND :end_date";
        $params[':start_date'] = $filter_date_range[0];
        $params[':end_date'] = $filter_date_range[1];
    }

    // เพิ่มเงื่อนไขการกรองตามสิทธิ์การเข้าถึง
    if ($filter_team_id && $role === 'Executive') {
        $query .= " AND p.seller IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id = :team_id)";
        $params[':team_id'] = $filter_team_id;
    } elseif ($role === 'Sale Supervisor') {
        // Supervisor ดูได้ทุกทีมที่ตัวเองสังกัด หรือเฉพาะทีมที่เลือก
        $current_team_id = $_SESSION['team_id'] ?? 'ALL';
        if ($current_team_id === 'ALL') {
            // Show all teams user belongs to
            $team_ids = $_SESSION['team_ids'] ?? [];
            if (!empty($team_ids)) {
                $team_placeholders = [];
                foreach ($team_ids as $key => $id) {
                    $placeholder = ':team_id_' . $key;
                    $team_placeholders[] = $placeholder;
                    $params[$placeholder] = $id;
                }
                $in_clause = implode(',', $team_placeholders);
                $query .= " AND p.seller IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id IN ($in_clause))";
            }
        } else {
            // Show specific team only
            $query .= " AND p.seller IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id = :current_team_ws)";
            $params[':current_team_ws'] = $current_team_id;
        }
    } elseif ($role === 'Seller') {
        $query .= " AND p.seller = :user_id";
        $params[':user_id'] = $user_id;
    }

    // เพิ่มเงื่อนไขกรองตาม user_id ที่เลือก
    if ($filter_user_id) {
        $query .= " AND p.seller = :filter_user_id";
        $params[':filter_user_id'] = $filter_user_id;
    }

    $stmt = $condb->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// เรียกใช้ฟังก์ชันเพื่อดึงข้อมูล
try {
    $win_summary = getWinProjectSummary(
        $condb,
        $role,
        $team_id,
        $user_id,
        $filter_team_id,
        $filter_user_id,
        $filter_date_range
    );

    // กำหนดค่าให้กับตัวแปร หรือให้เป็น 0 ถ้าไม่มีข้อมูล
    $win_sales = $win_summary['total_win_sales'] ?? 0;
    $win_cost = $win_summary['total_win_cost'] ?? 0;
    $win_profit = $win_summary['total_win_profit'] ?? 0;

    // คำนวณเปอร์เซ็นต์กำไร
    $win_profit_percentage = ($win_sales > 0) ? ($win_profit / $win_sales) * 100 : 0;
} catch (PDOException $e) {
    // จัดการข้อผิดพลาด
    error_log("Error getting win project summary: " . $e->getMessage());
    $win_sales = $win_cost = $win_profit = $win_profit_percentage = 0;
}

?>



<!DOCTYPE html>
<html lang="en">
<?php $menu = "index"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Dashboard</title>
    <?php include 'include/header.php' ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <?php include 'css_dashboard.php' ?>

    <!-- เพิ่ม CSS สำหรับ Date Range Picker -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/daterangepicker/3.1.0/daterangepicker.min.css" />
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
                                                <select class="form-control form-control-sm" id="team_select"
                                                    name="team_id">
                                                    <option value="">ทั้งหมด</option>
                                                    <?php foreach ($teams as $team): ?>
                                                        <option value="<?php echo escapeOutput($team['team_id']); ?>"
                                                            <?php echo ($team['team_id'] == $filter_team_id) ? 'selected' : ''; ?>>
                                                            <?php echo escapeOutput($team['team_name']); ?>
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
                                            <input type="text" class="form-control form-control-sm" id="date_range"
                                                name="date_range" value="<?php echo escapeOutput(implode(' - ', array_map(function ($date) {
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
                                                <select class="form-control form-control-sm" id="user_select"
                                                    name="user_id">
                                                    <option value="">ทั้งหมด</option>
                                                    <?php foreach ($team_members as $member): ?>
                                                        <option value="<?php echo escapeOutput($member['user_id']); ?>"
                                                            <?php echo ($member['user_id'] == $filter_user_id) ? 'selected' : ''; ?>>
                                                            <?php echo escapeOutput($member['full_name']); ?>
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
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                            <div class="card card-statistic" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_team); ?>">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <h6 class="card-title text-muted mb-0 ml-3"><?php echo $team_label; ?></h6>
                                    </div>
                                    <h2 class="font-weight-bold mb-1"><?php echo number_format($total_teams); ?></h2>
                                    <p class="mb-0 text-muted"><span class="text-success mr-2"><i
                                                class="fa fa-arrow-up"></i> 3.48%</span> จากเดือนที่แล้ว</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                            <div class="card card-statistic" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_members); ?>">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-user-friends"></i>
                                        </div>
                                        <h6 class="card-title text-muted mb-0 ml-3"><?php echo $member_label; ?></h6>
                                    </div>
                                    <h2 class="font-weight-bold mb-1"><?php echo number_format($total_team_members); ?>
                                    </h2>
                                    <p class="mb-0 text-muted"><span class="text-success mr-2"><i
                                                class="fa fa-arrow-up"></i> 5.27%</span> จากเดือนที่แล้ว</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                            <div class="card card-statistic" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_total_projects); ?>">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-danger">
                                            <i class="fas fa-project-diagram"></i>
                                        </div>
                                        <h6 class="card-title text-muted mb-0 ml-3">จำนวนโครงการทั้งหมด</h6>
                                    </div>
                                    <h2 class="font-weight-bold mb-1"><?php echo number_format($total_projects); ?></h2>
                                    <p class="mb-0 text-muted"><span class="text-danger mr-2"><i
                                                class="fa fa-arrow-down"></i> 1.08%</span> จากเดือนที่แล้ว</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                            <div class="card card-statistic" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_total_products); ?>">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <h6 class="card-title text-muted mb-0 ml-3">จำนวนสินค้าที่ขายทั้งหมด</h6>
                                    </div>
                                    <h2 class="font-weight-bold mb-1"><?php echo number_format($total_products); ?></h2>
                                    <p class="mb-0 text-muted"><span class="text-success mr-2"><i
                                                class="fa fa-arrow-up"></i> 2.37%</span> จากเดือนที่แล้ว</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- เพิ่มส่วนแสดงผลการ์ดสรุปสถานะโครงการ -->
                    <?php if ($can_view_financial): ?>
                        <div class="row">
                            <!-- การ์ดแสดงจำนวนโครงการสถานะชนะ (Win) -->
                            <div class="col-lg-3 col-6">
                                <div class="card card-statistic" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_win_projects); ?>">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="icon-circle bg-success">
                                                <i class="fas fa-trophy"></i>
                                            </div>
                                            <h6 class="card-title text-muted mb-0 ml-3">โครงการที่ชนะ (WIN)</h6>
                                        </div>
                                        <h2 class="font-weight-bold mb-1"><?php echo number_format($win_projects); ?></h2>
                                        <p class="mb-0 text-muted"><span class="text-success mr-2"><i
                                                    class="fa fa-arrow-up"></i> 2.5%</span> จากเดือนที่แล้ว</p>
                                    </div>
                                </div>
                            </div>

                            <!-- การ์ดแสดงจำนวนโครงการที่กำลังดำเนินการ -->
                            <div class="col-lg-3 col-6">
                                <div class="card card-statistic" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_ongoing_projects); ?>">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="icon-circle bg-warning">
                                                <i class="fas fa-hourglass-half"></i>
                                            </div>
                                            <h6 class="card-title text-muted mb-0 ml-3">โครงการกำลังดำเนินการ</h6>
                                        </div>
                                        <h2 class="font-weight-bold mb-1"><?php echo number_format($ongoing_projects); ?>
                                        </h2>
                                        <p class="mb-0 text-muted"><span class="text-warning mr-2"><i
                                                    class="fa fa-arrow-right"></i> 1.2%</span> จากเดือนที่แล้ว</p>
                                    </div>
                                </div>
                            </div>

                            <!-- การ์ดแสดงจำนวนโครงการสถานะแพ้ (Loss) -->
                            <div class="col-lg-3 col-6">
                                <div class="card card-statistic" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_loss_projects); ?>">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="icon-circle bg-danger">
                                                <i class="fas fa-times"></i>
                                            </div>
                                            <h6 class="card-title text-muted mb-0 ml-3">โครงการที่แพ้</h6>
                                        </div>
                                        <h2 class="font-weight-bold mb-1"><?php echo number_format($loss_projects); ?></h2>
                                        <p class="mb-0 text-muted"><span class="text-danger mr-2"><i
                                                    class="fa fa-arrow-down"></i> 0.8%</span> จากเดือนที่แล้ว</p>
                                    </div>
                                </div>
                            </div>

                            <!-- การ์ดแสดงจำนวนโครงการทั้งหมด -->
                            <div class="col-lg-3 col-6">
                                <div class="card card-statistic" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_canceled_projects); ?>">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="icon-circle bg-info">
                                                <i class="fas fa-project-diagram"></i>
                                            </div>
                                            <h6 class="card-title text-muted mb-0 ml-3">โครงการที่ยกเลิก</h6>
                                        </div>
                                        <h2 class="font-weight-bold mb-1"><?php echo number_format($canceled_projects); ?>
                                        </h2>
                                        <p class="mb-0 text-muted"><span class="text-info mr-2"><i
                                                    class="fa fa-arrow-up"></i> 3.0%</span> จากเดือนที่แล้ว</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>



                    <!-- ส่วนแสดงผล KPIs มีอยู่แล้ว -->
                    <!-- ส่วนแสดงผลการ์ดสรุปสถานะโครงการมีอยู่แล้ว -->

                    <!-- เพิ่มส่วนนี้ก่อนถึงส่วนของ "ส่วนแสดงข้อมูลสรุปโครงการที่มีสถานะชนะ (Win)" -->
                    <?php if ($can_view_financial): ?>
                        <!-- แถวแสดงข้อมูลภาพรวมทั้งหมด -->
                        <div class="row">
                            <!-- Card 1: ยอดขายรวมทั้งหมด (No vat) -->
                            <div class="col-lg-3 col-6">
                                <div class="card bg-info" style="border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_total_sales); ?>">
                                    <div class="card-header" style="background: linear-gradient(to right, #17a2b8, #3498db); border-radius: 15px 15px 0 0; border: none;">
                                        <h3 class="card-title" style="font-weight: 600; color: white;">
                                            <i class="fas fa-chart-line mr-2"></i>
                                            ยอดขายรวมทั้งหมด (No vat)
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus" style="color: white;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body text-center">
                                        <h3 style="font-size: 2rem; font-weight: 700; color: white;">
                                            ฿<?php echo number_format($total_sales, 2); ?>
                                        </h3>
                                        <p class="mb-0" style="color: rgba(255,255,255,0.8);">
                                            <i class="fas fa-signal mr-1"></i> ยอดขายจากทุกโครงการ
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: ต้นทุนรวมทั้งหมด (No vat) -->
                            <div class="col-lg-3 col-6">
                                <div class="card bg-secondary" style="border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_total_cost); ?>">
                                    <div class="card-header" style="background: linear-gradient(to right, #6c757d, #495057); border-radius: 15px 15px 0 0; border: none;">
                                        <h3 class="card-title" style="font-weight: 600; color: white;">
                                            <i class="fas fa-money-bill mr-2"></i>
                                            ต้นทุนรวมทั้งหมด (No vat)
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus" style="color: white;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body text-center">
                                        <h3 style="font-size: 2rem; font-weight: 700; color: white;">
                                            ฿<?php echo number_format($total_cost, 2); ?>
                                        </h3>
                                        <p class="mb-0" style="color: rgba(255,255,255,0.8);">
                                            <i class="fas fa-tags mr-1"></i> ต้นทุนจากทุกโครงการ
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3: กำไรรวมทั้งหมด (No Vat) -->
                            <div class="col-lg-3 col-6">
                                <div class="card bg-primary" style="border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_total_profit); ?>">
                                    <div class="card-header" style="background: linear-gradient(to right, #007bff, #0056b3); border-radius: 15px 15px 0 0; border: none;">
                                        <h3 class="card-title" style="font-weight: 600; color: white;">
                                            <i class="fas fa-hand-holding-usd mr-2"></i>
                                            กำไรรวมทั้งหมด (No Vat)
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus" style="color: white;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body text-center">
                                        <h3 style="font-size: 2rem; font-weight: 700; color: white;">
                                            ฿<?php echo number_format($total_profit, 2); ?>
                                        </h3>
                                        <p class="mb-0" style="color: rgba(255,255,255,0.8);">
                                            <i class="fas fa-calculator mr-1"></i> กำไรรวมจากทุกโครงการ
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 4: กำไรรวมทั้งหมด (No Vat %) -->
                            <div class="col-lg-3 col-6">
                                <div class="card bg-purple" style="border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_profit_percentage); ?>">
                                    <div class="card-header" style="background: linear-gradient(to right, #6f42c1, #563d7c); border-radius: 15px 15px 0 0; border: none;">
                                        <h3 class="card-title" style="font-weight: 600; color: white;">
                                            <i class="fas fa-percentage mr-2"></i>
                                            กำไรรวมทั้งหมด (No Vat %)
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus" style="color: white;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body text-center">
                                        <h3 style="font-size: 2rem; font-weight: 700; color: white;">
                                            <?php echo number_format($profit_percentage, 2); ?>%
                                        </h3>
                                        <p class="mb-0" style="color: rgba(255,255,255,0.8);">
                                            <i class="fas fa-chart-pie mr-1"></i> เปอร์เซ็นต์กำไรจากทุกโครงการ
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- ส่วนแสดงข้อมูลสรุปโครงการที่มีสถานะชนะ (Win) ที่มีอยู่แล้ว -->

                    <!-- ส่วนการแสดงผล Card ใหม่ -->
                    <?php if ($can_view_financial): ?>
                        <!-- แถวแสดงข้อมูลสรุปโครงการที่มีสถานะชนะ (Win) -->
                        <div class="row">
                            <!-- Card 1: Win ยอดขายรวม (No vat) -->
                            <div class="col-lg-3 col-6">
                                <div class="card bg-success" style="border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_win_sales); ?>">
                                    <div class="card-header" style="background: linear-gradient(to right, #28a745, #20c997); border-radius: 15px 15px 0 0; border: none;">
                                        <h3 class="card-title" style="font-weight: 600; color: white;">
                                            <i class="fas fa-chart-line mr-2"></i>
                                            Win ยอดขายรวม (No vat)
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus" style="color: white;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body text-center">
                                        <h3 style="font-size: 2rem; font-weight: 700; color: white;">
                                            ฿<?php echo number_format($win_sales, 2); ?>
                                        </h3>
                                        <p class="mb-0" style="color: rgba(255,255,255,0.8);">
                                            <i class="fas fa-trophy mr-1"></i> เฉพาะโครงการสถานะชนะ
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: Win ต้นทุนรวม (No Vat) -->
                            <div class="col-lg-3 col-6">
                                <div class="card bg-primary" style="border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_win_cost); ?>">
                                    <div class="card-header" style="background: linear-gradient(to right, #007bff, #17a2b8); border-radius: 15px 15px 0 0; border: none;">
                                        <h3 class="card-title" style="font-weight: 600; color: white;">
                                            <i class="fas fa-shopping-cart mr-2"></i>
                                            Win ต้นทุนรวม (No Vat)
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus" style="color: white;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body text-center">
                                        <h3 style="font-size: 2rem; font-weight: 700; color: white;">
                                            ฿<?php echo number_format($win_cost, 2); ?>
                                        </h3>
                                        <p class="mb-0" style="color: rgba(255,255,255,0.8);">
                                            <i class="fas fa-tags mr-1"></i> ต้นทุนโครงการสถานะชนะ
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3: Win กำไรรวม (No Vat) -->
                            <div class="col-lg-3 col-6">
                                <div class="card bg-warning" style="border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_win_profit); ?>">
                                    <div class="card-header" style="background: linear-gradient(to right, #ffc107, #fd7e14); border-radius: 15px 15px 0 0; border: none;">
                                        <h3 class="card-title" style="font-weight: 600; color: white;">
                                            <i class="fas fa-coins mr-2"></i>
                                            Win กำไรรวม (No Vat)
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus" style="color: white;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body text-center">
                                        <h3 style="font-size: 2rem; font-weight: 700; color: white;">
                                            ฿<?php echo number_format($win_profit, 2); ?>
                                        </h3>
                                        <p class="mb-0" style="color: rgba(255,255,255,0.8);">
                                            <i class="fas fa-piggy-bank mr-1"></i> กำไรโครงการสถานะชนะ
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 4: Win กำไร (No Vat %) -->
                            <div class="col-lg-3 col-6">
                                <div class="card bg-danger" style="border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" data-toggle="tooltip" data-placement="top" title="<?php echo escapeOutput($tooltip_win_profit_percentage); ?>">
                                    <div class="card-header" style="background: linear-gradient(to right, #dc3545, #e83e8c); border-radius: 15px 15px 0 0; border: none;">
                                        <h3 class="card-title" style="font-weight: 600; color: white;">
                                            <i class="fas fa-percentage mr-2"></i>
                                            Win กำไร (No Vat %)
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus" style="color: white;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body text-center">
                                        <h3 style="font-size: 2rem; font-weight: 700; color: white;">
                                            <?php echo number_format($win_profit_percentage, 2); ?>%
                                        </h3>
                                        <p class="mb-0" style="color: rgba(255,255,255,0.8);">
                                            <i class="fas fa-chart-pie mr-1"></i> เปอร์เซ็นต์กำไรจากโครงการชนะ
                                        </p>
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
                                <div class="card-body" style="height: 360px; min-height: 360px;">
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
                                <div class="card-body" style="height: 360px; min-height: 360px;">
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
                                <div class="card-body" style="height: 360px; min-height: 360px;">
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
                                <div class="card-body" style="height: 360px; min-height: 360px;">
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
                            <div class="card-body" style="height: 360px; min-height: 360px;">
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
                            <div class="card-body" style="height: 360px; min-height: 360px;">
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
            $('[data-toggle="tooltip"]').tooltip();

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
                    monthNames: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.',
                        'ต.ค.', 'พ.ย.', 'ธ.ค.'
                    ],
                    firstDay: 1
                },
                startDate: moment("<?php echo $filter_date_range[0]; ?>"),
                endDate: moment("<?php echo $filter_date_range[1]; ?>")
            });

            // ฟังก์ชันสำหรับกรองพนักงานขายตามทีมที่เลือก
            // แก้ไขฟังก์ชัน change ของ team_select
            $('#team_select').change(function() {
                var selectedTeam = $(this).val();
                var userSelect = $('#user_select');
                var currentUserId = '<?php echo $filter_user_id; ?>'; // เก็บค่า user_id ที่เลือกไว้

                userSelect.empty();
                userSelect.append('<option value="">ทั้งหมด</option>');

                <?php if ($can_view_all): ?>
                    <?php foreach ($team_members as $member): ?>
                        if (selectedTeam == '' || selectedTeam == '<?php echo $member['team_id']; ?>') {
                            var option = $('<option></option>')
                                .val('<?php echo $member['user_id']; ?>')
                                .text('<?php echo $member['full_name']; ?>');

                            // เช็คว่าเป็น user ที่เลือกไว้หรือไม่
                            if ('<?php echo $member['user_id']; ?>' == currentUserId) {
                                option.prop('selected', true);
                            }

                            userSelect.append(option);
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

<!-- เพิ่ม Chart.js ล่าสุด -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>
    // =====================================================
    // ส่วนที่ 1: การเตรียมพร้อมและฟังก์ชันช่วยเหลือ
    // =====================================================
    document.addEventListener('DOMContentLoaded', function() {

        let charts = {}; // เก็บ reference ของกราฟทั้งหมด

        // ฟังก์ชันสำหรับจัดรูปแบบตัวเลขทั่วไป เช่น 1,234
        function formatNumber(number) {
            return new Intl.NumberFormat('th-TH', {
                style: 'decimal',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(number || 0);
        }

        // ฟังก์ชันสำหรับจัดรูปแบบเงิน เช่น ฿1,234
        function formatCurrency(number) {
            return '฿' + formatNumber(number);
        }

        // ฟังก์ชันสำหรับจัดรูปแบบเปอร์เซ็นต์ เช่น 12.34%
        function formatPercent(number) {
            return number.toFixed(2) + '%';
        }

        // ฟังก์ชันทำลายกราฟเดิม
        function destroyCharts() {
            Object.values(charts).forEach(chart => {
                if (chart instanceof Chart) {
                    chart.destroy();
                }
            });
            charts = {};
        }

        // เพิ่มชุดสีสำหรับกราฟ
        const chartColors = {
            bar: [
                'rgba(255, 99, 132, 0.8)', // สีชมพู
                'rgba(54, 162, 235, 0.8)', // สีฟ้า
                'rgba(255, 206, 86, 0.8)', // สีเหลือง
                'rgba(75, 192, 192, 0.8)', // สีเขียวมิ้นต์
                'rgba(153, 102, 255, 0.8)', // สีม่วง
                'rgba(255, 159, 64, 0.8)', // สีส้ม
                'rgba(76, 175, 80, 0.8)', // สีเขียว
                'rgba(244, 67, 54, 0.8)', // สีแดง
                'rgba(156, 39, 176, 0.8)', // สีม่วงเข้ม
                'rgba(63, 81, 181, 0.8)' // สีน้ำเงินเข้ม
            ],
            borderColors: function() {
                return this.bar.map(color => color.replace('0.8', '1'));
            },
            // สร้างสีตามจำนวนข้อมูล
            generateColors: function(count) {
                let colors = [];
                for (let i = 0; i < count; i++) {
                    colors.push(this.bar[i % this.bar.length]);
                }
                return colors;
            }
        };



        // =====================================================
        // ส่วนที่ 2: การตั้งค่าพื้นฐานของ Chart.js
        // =====================================================

        // กำหนดค่าเริ่มต้นสำหรับ Chart.js ทั้งหมด
        Chart.defaults.font.family = 'Kanit, sans-serif';
        Chart.defaults.font.size = 13;
        Chart.defaults.plugins.tooltip.padding = 10;
        Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.8)';

        // กำหนดตัวเลือกพื้นฐานที่ใช้ร่วมกันในทุกกราฟ
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.raw;
                            return label + ': ' + formatCurrency(value);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => formatCurrency(value)
                    }
                }
            },
            animation: {
                duration: 500
            }
        };

        // =====================================================
        // ส่วนที่ 3: การสร้างกราฟแต่ละประเภท
        // =====================================================

        // 1. กราฟวงกลมแสดงสถานะโครงการ
        // -----------------------------------------------------
        new Chart(document.getElementById('projectStatusChart'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_map(function ($item) {
                            return $item['status'];
                        }, $project_status_data)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_map(function ($item) {
                                return intval($item['count']);
                            }, $project_status_data)); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)', // สีแดง
                        'rgba(54, 162, 235, 0.8)', // สีฟ้า
                        'rgba(255, 206, 86, 0.8)', // สีเหลือง
                        'rgba(75, 192, 192, 0.8)', // สีเขียว
                        'rgba(153, 102, 255, 0.8)' // สีม่วง
                    ]
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + formatNumber(context.raw) + ' โครงการ';
                            }
                        }
                    }
                }
            }
        });

        // 2. กราฟยอดขายรายปี
        // -----------------------------------------------------
        new Chart(document.getElementById('yearlySalesChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_map(function ($item) {
                            return $item['year'];
                        }, $yearly_sales_data)); ?>,
                datasets: [{
                    label: 'ยอดขายรวม',
                    data: <?php echo json_encode(array_map(function ($item) {
                                return floatval($item['total_sales']);
                            }, $yearly_sales_data)); ?>,
                    backgroundColor: chartColors.generateColors(
                        <?php echo count($yearly_sales_data); ?>),
                    borderColor: chartColors.borderColors(),
                    borderWidth: 1
                }]
            },
            options: commonOptions
        });

        // 3. กราฟยอดขายรายเดือน
        // -----------------------------------------------------
        new Chart(document.getElementById('monthlySalesChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_map(function ($item) {
                            return date('M Y', strtotime($item['month'] . '-01'));
                        }, $monthly_sales_data)); ?>,
                datasets: [{
                    label: 'ยอดขายรายเดือน',
                    data: <?php echo json_encode(array_map(function ($item) {
                                return floatval($item['total_sales']);
                            }, $monthly_sales_data)); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });

        // 4. กราฟยอดขายของพนักงาน (แนวนอน)
        // -----------------------------------------------------
        new Chart(document.getElementById('employeeSalesChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_map(function ($item) {
                            return $item['first_name'] . ' ' . $item['last_name'];
                        }, $employee_sales_data)); ?>,
                datasets: [{
                    label: 'ยอดขาย',
                    data: <?php echo json_encode(array_map(function ($item) {
                                return floatval($item['total_sales']);
                            }, $employee_sales_data)); ?>,
                    backgroundColor: chartColors.generateColors(
                        <?php echo count($employee_sales_data); ?>),
                    borderColor: chartColors.borderColors(),
                    borderWidth: 1
                }]
            },
            options: {
                ...commonOptions,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => formatCurrency(value)
                        }
                    }
                }
            }
        });

        // 5. กราฟยอดขายรายทีม
        // -----------------------------------------------------
        new Chart(document.getElementById('teamSalesChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_map(function ($item) {
                            return $item['team_name'];
                        }, $team_sales_data)); ?>,
                datasets: [{
                    label: 'ยอดขายรายทีม',
                    data: <?php echo json_encode(array_map(function ($item) {
                                return floatval($item['total_sales']);
                            }, $team_sales_data)); ?>,
                    backgroundColor: chartColors.generateColors(
                        <?php echo count($team_sales_data); ?>),
                    borderColor: chartColors.borderColors(),
                    borderWidth: 1
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'ยอดขาย: ' + formatCurrency(context.raw);
                            }
                        }
                    }
                }
            }
        });
        // 6. กราฟแท่งแสดงจำนวนการขายสินค้า
        // -----------------------------------------------------
        // 6. กราฟแท่งแสดงจำนวนการขายสินค้า
        new Chart(document.getElementById('topProductsChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_map(function ($item) {
                            return $item['product_name'];
                        }, $top_products_data)); ?>,
                datasets: [{
                    label: 'จำนวนการขาย',
                    data: <?php echo json_encode(array_map(function ($item) {
                                return intval($item['count']);
                            }, $top_products_data)); ?>,
                    backgroundColor: chartColors.generateColors(
                        <?php echo count($top_products_data); ?>),
                    borderColor: chartColors.borderColors(),
                    borderWidth: 1
                }]
            },
            options: {
                ...commonOptions,
                indexAxis: 'y',
                layout: {
                    padding: {
                        left: 5,
                        right: 25
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        },
                        ticks: {
                            callback: value => formatNumber(value)
                        }
                    },
                    y: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxRotation: 0,
                            minRotation: 0,
                            mirror: false,
                            padding: 5,
                            // เพิ่มการจัดการความยาวของ label
                            callback: function(value, index) {
                                let label = this.getLabelForValue(value);
                                if (!label) return '';

                                // จำกัดความยาวของ label และเพิ่ม ... ถ้ายาวเกินไป
                                if (label.length > 25) {
                                    return label.substr(0, 25) + '...';
                                }
                                return label;
                            },
                            font: {
                                size: 12 // ปรับขนาดตัวอักษร
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // ซ่อน legend เพราะมี label เดียว
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                // แสดงชื่อเต็มใน tooltip
                                return context[0].label;
                            },
                            label: function(context) {
                                return 'จำนวน: ' + formatNumber(context.raw) + ' ครั้ง';
                            }
                        }
                    }
                },
                maintainAspectRatio: false,
                responsive: true,
                height: 400 // กำหนดความสูงของกราฟ
            }
        });
    });
</script>
