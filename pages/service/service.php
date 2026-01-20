<?php
// สลับไปมุมมอง classic (ตารางเดิม) หากมี query view=classic
if (isset($_GET['view']) && $_GET['view'] === 'classic') {
    include __DIR__ . '/service2.php';
    return;
}
?>
<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ใช้ข้อมูลจริง + รองรับตัวกรองจากแบบฟอร์ม
$viewMode = $_GET['view'] ?? 'modern';
if ($viewMode === 'classic') {
    include __DIR__ . '/service2.php';
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'] ?? null;
$sessionTeamIds = $_SESSION['team_ids'] ?? [];
$accessibleTeamIds = [];
if (is_array($sessionTeamIds)) {
    foreach ($sessionTeamIds as $tid) {
        if (!empty($tid) && $tid !== 'ALL') {
            $accessibleTeamIds[] = $tid;
        }
    }
}
if (!empty($team_id) && $team_id !== 'ALL') {
    $accessibleTeamIds[] = $team_id;
}
$accessibleTeamIds = array_values(array_unique($accessibleTeamIds));

function buildVisibilityFilter(string $role, array $accessibleTeamIds, string $userId): array
{
    if ($role === 'Executive') {
        // Executive เห็นทั้งหมด
        return ['clause' => '1=1', 'params' => []];
    }

    if ($role === 'Account Management' || $role === 'Sale Supervisor') {
        // Account Management และ Sale Supervisor เห็นตามทีม + ของตัวเอง
        $clauses = [];
        $params = [];

        // เห็น Ticket ที่ Job owner อยู่ในทีม
        if (!empty($accessibleTeamIds)) {
            $teamPlaceholders = [];
            foreach ($accessibleTeamIds as $idx => $tid) {
                $placeholder = ':vis_team_sup_' . $idx;
                $teamPlaceholders[] = $placeholder;
                $params[$placeholder] = $tid;
            }
            $clauses[] = 'st.job_owner IN (SELECT ut_scope.user_id FROM user_teams ut_scope WHERE ut_scope.team_id IN (' . implode(',', $teamPlaceholders) . '))';
        }

        // เห็น Ticket ที่ตนเองเป็น Reporter
        $clauses[] = 'st.reporter = :vis_sup_reporter';
        $params[':vis_sup_reporter'] = $userId;

        // เห็น Ticket ที่ตนเองเป็น Watcher
        $clauses[] = 'st.ticket_id IN (SELECT watcher.ticket_id FROM service_ticket_watchers watcher WHERE watcher.user_id = :vis_sup_watcher)';
        $params[':vis_sup_watcher'] = $userId;

        // เห็น Ticket ที่ตนเองเป็นผู้สร้าง (Created by)
        $clauses[] = 'st.created_by = :vis_sup_created';
        $params[':vis_sup_created'] = $userId;

        if (empty($clauses)) {
            return ['clause' => '1=1', 'params' => []];
        }

        return [
            'clause' => '(' . implode(' OR ', $clauses) . ')',
            'params' => $params
        ];
    }

    // สำหรับ Seller และ Engineer - เห็นเฉพาะของตนเอง
    $clauses = [];
    $params = [];

    // เพิ่มเงื่อนไขสำหรับทีม (ถ้ามี) - ใช้สำหรับกรณีที่เป็นสมาชิกทีม
    if (!empty($accessibleTeamIds)) {
        $teamPlaceholders = [];
        foreach ($accessibleTeamIds as $idx => $tid) {
            $placeholder = ':vis_team_' . $idx;
            $teamPlaceholders[] = $placeholder;
            $params[$placeholder] = $tid;
        }
        $clauses[] = 'st.job_owner IN (SELECT ut_scope.user_id FROM user_teams ut_scope WHERE ut_scope.team_id IN (' . implode(',', $teamPlaceholders) . '))';
    }

    // เงื่อนไขที่ 1: ตนเองเป็น Job owner (ผู้รับผิดชอบ)
    $clauses[] = 'st.job_owner = :vis_self';
    $params[':vis_self'] = $userId;

    // เงื่อนไขที่ 2: ตนเองเป็น Watcher (ผู้ติดตาม)
    $clauses[] = 'st.ticket_id IN (SELECT watcher.ticket_id FROM service_ticket_watchers watcher WHERE watcher.user_id = :vis_watcher)';
    $params[':vis_watcher'] = $userId;

    // เงื่อนไขที่ 3: ตนเองเป็น Reporter (ผู้รายงาน)
    $clauses[] = 'st.reporter = :vis_reporter';
    $params[':vis_reporter'] = $userId;

    // เงื่อนไขที่ 4: ตนเองเป็นผู้สร้าง (Created by) - ไม่ว่าจะมอบหมายให้ใครเป็น Job owner
    $clauses[] = 'st.created_by = :vis_created_by';
    $params[':vis_created_by'] = $userId;

    return [
        'clause' => '(' . implode(' OR ', array_unique($clauses)) . ')',
        'params' => $params,
    ];
}

// รับค่าตัวกรอง (GET) และกำหนดค่าเริ่มต้น
$searchText       = $_GET['searchservice']   ?? '';
$filterType       = $_GET['categorytype']    ?? '';
$filterJobOwner   = array_key_exists('jobowner', $_GET) ? ($_GET['jobowner'] ?? '') : $user_id;
$filterSla        = $_GET['sla']             ?? '';
$filterSlaStatus  = $_GET['sla_status']      ?? '';
$filterPriority   = $_GET['priority']        ?? '';
$filterSource     = $_GET['source']          ?? '';
$filterUrgency    = $_GET['urgency']         ?? '';
$filterImpact     = $_GET['impact']          ?? '';
$filterStatus     = $_GET['status']          ?? '';
$filterServiceCat = $_GET['servicecategory'] ?? '';
$filterCategory   = $_GET['category']        ?? '';
$filterSubCat     = $_GET['subcategory']     ?? '';
$filterChannel    = $_GET['channel']         ?? '';
$filterProject    = $_GET['project_id']      ?? ''; // เพิ่มตัวกรองโปรเจกต์
$filterStartDate  = $_GET['start_date']      ?? ''; // เพิ่มตัวกรองวันที่เริ่ม
$filterEndDate    = $_GET['end_date']        ?? ''; // เพิ่มตัวกรองวันที่สิ้นสุด

// โหลดตัวเลือกสำหรับ dropdown
// Job Owner: Executive เห็นทั้งหมด, Supervisor เห็นเฉพาะทีม, อื่นๆ เห็นเฉพาะตนเอง
$visibility = buildVisibilityFilter($role, $accessibleTeamIds, $user_id);

$jobOwnerSql = "SELECT DISTINCT st.job_owner AS user_id, CONCAT(u.first_name,' ',u.last_name) AS full_name
                FROM service_tickets st
                LEFT JOIN users u ON st.job_owner = u.user_id
                WHERE " . $visibility['clause'] . "
                AND st.job_owner IS NOT NULL
                ORDER BY full_name";
$stmtJO = $condb->prepare($jobOwnerSql);
foreach ($visibility['params'] as $key => $value) {
    $stmtJO->bindValue($key, $value);
}
$stmtJO->execute();
$jobOwnerOptions = $stmtJO->fetchAll(PDO::FETCH_ASSOC);

if (!empty($filterJobOwner) && !in_array($filterJobOwner, array_column($jobOwnerOptions, 'user_id'), true)) {
    $stmtUser = $condb->prepare("SELECT user_id, CONCAT(first_name,' ',last_name) AS full_name FROM users WHERE user_id = :uid");
    $stmtUser->execute([':uid' => $filterJobOwner]);
    if ($extraUser = $stmtUser->fetch(PDO::FETCH_ASSOC)) {
        $jobOwnerOptions[] = $extraUser;
    }
}
usort($jobOwnerOptions, static function ($a, $b) {
    return strcmp($a['full_name'] ?? '', $b['full_name'] ?? '');
});

function distinctOptions(PDO $db, string $col, string $visibilityClause, array $visibilityParams)
{
    $sql = "SELECT DISTINCT $col AS v FROM service_tickets st WHERE " . $visibilityClause . " AND $col IS NOT NULL AND $col <> '' ORDER BY v";
    $stmt = $db->prepare($sql);
    foreach ($visibilityParams as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

$categoryTypeOptions = distinctOptions($condb, 'ticket_type', $visibility['clause'], $visibility['params']);
$slaOptions          = distinctOptions($condb, 'sla_target', $visibility['clause'], $visibility['params']);
$slaStatusOptions    = distinctOptions($condb, 'sla_status', $visibility['clause'], $visibility['params']);
$priorityOptions     = distinctOptions($condb, 'priority', $visibility['clause'], $visibility['params']);
$sourceOptions       = distinctOptions($condb, 'source', $visibility['clause'], $visibility['params']);
$urgencyOptions      = distinctOptions($condb, 'urgency', $visibility['clause'], $visibility['params']);
$impactOptions       = distinctOptions($condb, 'impact', $visibility['clause'], $visibility['params']);
$statusOptions       = distinctOptions($condb, 'status', $visibility['clause'], $visibility['params']);
$serviceCatOptions   = distinctOptions($condb, 'service_category', $visibility['clause'], $visibility['params']);
$categoryOptions     = distinctOptions($condb, 'category', $visibility['clause'], $visibility['params']);
$subCategoryOptions  = distinctOptions($condb, 'sub_category', $visibility['clause'], $visibility['params']);
$channelOptions      = distinctOptions($condb, 'channel', $visibility['clause'], $visibility['params']);

// ดึงรายการโปรเจกต์ทั้งหมดที่ผู้ใช้มีสิทธิ์เห็น
$projectSql = "SELECT DISTINCT p.project_id, p.project_name 
               FROM projects p
               INNER JOIN service_tickets st ON st.project_id = p.project_id
               WHERE " . $visibility['clause'] . " 
               AND p.project_id IS NOT NULL
               ORDER BY p.project_name";
$stmtProj = $condb->prepare($projectSql);
foreach ($visibility['params'] as $key => $value) {
    $stmtProj->bindValue($key, $value);
}
$stmtProj->execute();
$projectOptions = $stmtProj->fetchAll(PDO::FETCH_ASSOC);

// สร้าง WHERE/Params ใช้ซ้ำได้ ทั้งรายการและ Metrics
$where = ' WHERE ' . $visibility['clause'];
$params = $visibility['params'];

// กรองตาม Job Owner จากตัวเลือก (หากมี)
if (!empty($filterJobOwner)) { $where .= " AND st.job_owner = :job_owner"; $params[':job_owner'] = $filterJobOwner; }

// กรองฟิลด์อื่นๆ
$mapFilters = [
    'ticket_type'      => $filterType,
    'priority'         => $filterPriority,
    'source'           => $filterSource,
    'urgency'          => $filterUrgency,
    'impact'           => $filterImpact,
    'status'           => $filterStatus,
    'service_category' => $filterServiceCat,
    'category'         => $filterCategory,
    'sub_category'     => $filterSubCat,
    'sla_target'       => $filterSla,
    'sla_status'       => $filterSlaStatus,
    'channel'          => $filterChannel,
];
foreach ($mapFilters as $col => $val) {
    if ($val !== '' && $val !== null) { $where .= " AND st.$col = :$col"; $params[":$col"] = $val; }
}

// กรองตามโปรเจกต์
if (!empty($filterProject)) {
    $where .= " AND st.project_id = :project_id";
    $params[':project_id'] = $filterProject;
}

// กรองตามช่วงเวลา start_at และ due_at
if (!empty($filterStartDate)) {
    $where .= " AND st.start_at >= :start_date";
    $params[':start_date'] = $filterStartDate . ' 00:00:00';
}
if (!empty($filterEndDate)) {
    $where .= " AND st.due_at <= :end_date";
    $params[':end_date'] = $filterEndDate . ' 23:59:59';
}

$defaultFilterValues = [
    'categorytype'    => '',
    'jobowner'        => $user_id,
    'sla'             => '',
    'priority'        => '',
    'source'          => '',
    'urgency'         => '',
    'impact'          => '',
    'status'          => '',
    'servicecategory' => '',
    'category'        => '',
    'subcategory'     => '',
    'channel'         => '',
    'sla_status'      => '',
    'project_id'      => '',
    'start_date'      => '',
    'end_date'        => ''
];

// ค้นหาข้อความ
if ($searchText !== '') { $where .= " AND (st.ticket_no LIKE :q OR st.subject LIKE :q OR st.description LIKE :q)"; $params[':q'] = "%$searchText%"; }

// ดึง Tickets
$sqlTickets = "SELECT st.*,
        CONCAT(u.first_name, ' ', u.last_name) as job_owner_name,
        CONCAT(r.first_name, ' ', r.last_name) as reporter_name,
        CONCAT(c.first_name, ' ', c.last_name) as creator_name,
        p.project_name,
        (SELECT COUNT(*) FROM service_ticket_attachments att WHERE att.ticket_id = st.ticket_id) AS attachment_count,
        (SELECT GROUP_CONCAT(CONCAT(att.attachment_id, '::', att.file_name, '::', att.file_path, '::', att.mime_type)
                ORDER BY att.uploaded_at DESC SEPARATOR '||')
         FROM service_ticket_attachments att WHERE att.ticket_id = st.ticket_id) AS attachment_list,
        (SELECT GROUP_CONCAT(CONCAT(wu.first_name, ' ', wu.last_name) ORDER BY wu.first_name SEPARATOR ', ')
            FROM service_ticket_watchers w
            JOIN users wu ON w.user_id = wu.user_id
            WHERE w.ticket_id = st.ticket_id) AS watcher_names
        FROM service_tickets st
        LEFT JOIN users u ON st.job_owner = u.user_id
        LEFT JOIN users r ON st.reporter = r.user_id
        LEFT JOIN users c ON st.created_by = c.user_id
        LEFT JOIN projects p ON st.project_id = p.project_id" . $where . " ORDER BY st.created_at DESC LIMIT 100";

$stmtTickets = $condb->prepare($sqlTickets);
foreach ($params as $k=>$v) { $stmtTickets->bindValue($k, $v); }
$stmtTickets->execute();
$tickets = $stmtTickets->fetchAll(PDO::FETCH_ASSOC);

// ดึง Metrics ตามตัวกรองเดียวกัน
$sqlMetrics = "SELECT
    COUNT(*) as total_tickets,
    SUM(CASE WHEN status = 'New' THEN 1 ELSE 0 END) as status_new,
    SUM(CASE WHEN status = 'On Process' THEN 1 ELSE 0 END) as status_on_process,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as status_pending,
    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as status_resolved,
    SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) as status_closed,
    SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as status_cancelled,
    SUM(CASE WHEN sla_status = 'Overdue' THEN 1 ELSE 0 END) as sla_overdue
    FROM service_tickets st" . $where;
$stmtMetrics = $condb->prepare($sqlMetrics);
foreach ($params as $k=>$v) { $stmtMetrics->bindValue($k, $v); }
$stmtMetrics->execute();
$metricsData = $stmtMetrics->fetch(PDO::FETCH_ASSOC);

// แปลง Metrics จาก API เป็นรูปแบบที่ใช้แสดงผล
$serviceMetrics = [
    [
        'title' => 'All Ticket',
        'description' => 'จำนวนงานทั้งหมด',
        'value' => $metricsData['total_tickets'] ?? 0,
        'color' => 'bg-info',
        'icon'  => 'fas fa-ticket-alt',
        'filter_key' => 'all',
        'filter_value' => ''
    ],
    [
        'title' => 'New',
        'description' => 'งานใหม่',
        'value' => $metricsData['status_new'] ?? 0,
        'color' => 'bg-primary',
        'icon'  => 'fas fa-plus-circle',
        'filter_key' => 'status',
        'filter_value' => 'New'
    ],
    [
        'title' => 'On Process',
        'description' => 'งานที่กำลังดำเนินการ',
        'value' => $metricsData['status_on_process'] ?? 0,
        'color' => 'bg-warning',
        'icon'  => 'fas fa-tasks',
        'filter_key' => 'status',
        'filter_value' => 'On Process'
    ],
    [
        'title' => 'Pending',
        'description' => 'งานที่รอดำเนินการ',
        'value' => $metricsData['status_pending'] ?? 0,
        'color' => 'bg-secondary',
        'icon'  => 'fas fa-hourglass-half',
        'filter_key' => 'status',
        'filter_value' => 'Pending'
    ],
    [
        'title' => 'Resolved',
        'description' => 'งานที่แก้ไขแล้ว',
        'value' => $metricsData['status_resolved'] ?? 0,
        'color' => 'bg-success',
        'icon'  => 'fas fa-check-circle',
        'filter_key' => 'status',
        'filter_value' => 'Resolved'
    ],
    [
        'title' => 'Closed',
        'description' => 'งานที่ปิดแล้ว',
        'value' => $metricsData['status_closed'] ?? 0,
        'color' => 'bg-teal',
        'icon'  => 'fas fa-lock',
        'filter_key' => 'status',
        'filter_value' => 'Closed'
    ],
    [
        'title' => 'Cancelled',
        'description' => 'งานที่ยกเลิก',
        'value' => $metricsData['status_cancelled'] ?? 0,
        'color' => 'bg-danger',
        'icon'  => 'fas fa-times-circle',
        'filter_key' => 'status',
        'filter_value' => 'Cancelled'
    ],
    [
        'title' => 'Overdue SLA',
        'description' => 'งานที่เกิน SLA',
        'value' => $metricsData['sla_overdue'] ?? 0,
        'color' => 'bg-maroon',
        'icon'  => 'fas fa-exclamation-triangle',
        'filter_key' => 'sla_status',
        'filter_value' => 'Overdue'
    ],
];

// แปลงรูปแบบข้อมูล Tickets จาก API
// Note: ไม่ต้องใช้ Mock Data แล้ว ใช้ข้อมูลจริงจาก $tickets

$typeStyles = [
    'Incident' => ['class' => 'badge badge-pill badge-incident', 'label' => 'Incident'],
    'Service'  => ['class' => 'badge badge-pill badge-service', 'label' => 'Service'],
    'Change'   => ['class' => 'badge badge-pill badge-change', 'label' => 'Change'],
];

$slaStatusStyles = [
    'Overdue'      => ['class' => 'badge badge-pill badge-sla-overdue', 'label' => 'Overdue'],
    'At Risk'      => ['class' => 'badge badge-pill badge-sla-warning', 'label' => 'At Risk'],
    'Warning'      => ['class' => 'badge badge-pill badge-sla-warning', 'label' => 'Warning'],
    'Approaching'  => ['class' => 'badge badge-pill badge-sla-warning', 'label' => 'Approaching'],
    'Within SLA'   => ['class' => 'badge badge-pill badge-sla-ontrack', 'label' => 'Within SLA'],
    'On Track'     => ['class' => 'badge badge-pill badge-sla-ontrack', 'label' => 'On Track'],
    'Met'          => ['class' => 'badge badge-pill badge-sla-ontrack', 'label' => 'Met'],
    'Completed'    => ['class' => 'badge badge-pill badge-sla-ontrack', 'label' => 'Completed'],
    'None'         => ['class' => 'badge badge-pill badge-sla-default', 'label' => 'N/A'],
    ''             => ['class' => 'badge badge-pill badge-sla-default', 'label' => 'N/A'],
];

$statusStyles = [
    'Draft'                 => ['class' => 'badge badge-pill badge-status-default', 'icon' => 'fas fa-file-alt'],
    'New'                   => ['class' => 'badge badge-pill badge-status-assigned', 'icon' => 'fas fa-bell'],
    'On Process'            => ['class' => 'badge badge-pill badge-status-process', 'icon' => 'fas fa-hourglass-half'],
    'Pending'               => ['class' => 'badge badge-pill badge-status-pending', 'icon' => 'fas fa-clock'],
    'Waiting for Approval'  => ['class' => 'badge badge-pill badge-status-waiting', 'icon' => 'fas fa-user-clock'],
    'In Progress'           => ['class' => 'badge badge-pill badge-status-progress', 'icon' => 'fas fa-spinner'],
    'Resolved'              => ['class' => 'badge badge-pill badge-status-resolved', 'icon' => 'fas fa-check-circle'],
    'Closed'                => ['class' => 'badge badge-pill badge-status-containment', 'icon' => 'fas fa-check-double'],
    'Cancelled'             => ['class' => 'badge badge-pill badge-status-cab-review', 'icon' => 'fas fa-times-circle'],
    'Approved'              => ['class' => 'badge badge-pill badge-status-approved', 'icon' => 'fas fa-thumbs-up'],
    'Resolved Pending'      => ['class' => 'badge badge-pill badge-status-resolved-pending', 'icon' => 'fas fa-pause-circle'],
    'Scheduled'             => ['class' => 'badge badge-pill badge-status-scheduled', 'icon' => 'fas fa-calendar-check'],
    'Pending Approval'      => ['class' => 'badge badge-pill badge-status-pending-approval', 'icon' => 'fas fa-clipboard-check'],
    'CAB Review'            => ['class' => 'badge badge-pill badge-status-cab-review', 'icon' => 'fas fa-users'],
    'Assigned'              => ['class' => 'badge badge-pill badge-status-assigned', 'icon' => 'fas fa-user-tag'],
    'Containment'           => ['class' => 'badge badge-pill badge-status-containment', 'icon' => 'fas fa-lock'],
];

if (!function_exists('summarizeSubject')) {
    function summarizeSubject($subject, $limit = 150)
    {
        $clean = trim($subject);
        if (mb_strlen($clean, 'UTF-8') <= $limit) {
            $safe = htmlspecialchars($clean, ENT_QUOTES, 'UTF-8');
            return [$safe, $safe];
        }

        $display = mb_substr($clean, 0, $limit, 'UTF-8') . '....';
        return [htmlspecialchars($display, ENT_QUOTES, 'UTF-8'), htmlspecialchars($clean, ENT_QUOTES, 'UTF-8')];
    }
}

$baseQueryParams = $_GET;
$modernViewQuery = http_build_query($baseQueryParams);
$modernViewUrl = $_SERVER['PHP_SELF'] . ($modernViewQuery ? '?' . $modernViewQuery : '');
$classicViewUrl = 'service2.php' . ($modernViewQuery ? '?' . $modernViewQuery : '');
?>


<!DOCTYPE html>
<html lang="en">
<?php $menu = "service"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Service Management</title>
    <?php include  '../../include/header.php'; ?>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <style>
            .service-metrics-row {
                display: flex;
                flex-wrap: nowrap;
                gap: 0.5rem;
                overflow-x: auto;
                padding: 0.5rem 0;
                scrollbar-width: thin;
            }

            .service-metrics-row::-webkit-scrollbar {
                height: 6px;
            }

            .service-metrics-row::-webkit-scrollbar-thumb {
                background: rgba(0, 0, 0, 0.2);
                border-radius: 999px;
            }

            .service-metrics-row::-webkit-scrollbar-track {
                background: transparent;
            }

            .service-metrics-row .metric-card-col {
                flex: 0 0 240px;
                max-width: 240px;
            }

            .service-metrics-row .metric-card-col .small-box {
                cursor: pointer;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .service-metrics-row .metric-card-col .small-box:hover,
            .service-metrics-row .metric-card-col .small-box:focus {
                transform: translateY(-2px);
                box-shadow: 0 12px 20px rgba(0, 0, 0, 0.18);
                outline: none;
            }

            .service-metrics-row .metric-card-col .small-box.metric-active {
                box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.35), 0 12px 20px rgba(0, 0, 0, 0.2);
            }

            .service-table-wrapper {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 0.5rem;
            }

            .service-table-wrapper table {
                min-width: 720px;
            }

            #serviceTickets {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                table-layout: auto;
            }

            #serviceTickets thead th {
                background-color: #f8f9fa;
                border-bottom: 1px solid #dee2e6;
                border-top: 1px solid #dee2e6;
                vertical-align: middle;
                text-align: center;
            }

            #serviceTickets thead th,
            #serviceTickets tbody td {
                border-right: 1px solid #dee2e6;
            }

            #serviceTickets thead th:first-child,
            #serviceTickets tbody td:first-child {
                border-left: 1px solid #dee2e6;
            }

            #serviceTickets tbody tr:last-child td {
                border-bottom: 1px solid #dee2e6;
            }

            #serviceTickets th.ticket-summary-col,
            #serviceTickets td.ticket-summary {
                width: 45%;
            }

            #serviceTickets th.ticket-details-col,
            #serviceTickets td.ticket-details {
                width: 35%;
            }

            #serviceTickets th.ticket-actions-col,
            #serviceTickets td.ticket-actions {
                width: 20%;
            }

            #serviceTickets tbody td {
                vertical-align: top;
                padding: 1rem;
                background-color: #ffffff;
            }

            #serviceTickets tbody tr:nth-child(odd) td {
                background-color: #fdfdfd;
            }

            #serviceTickets tbody tr:nth-child(even) td {
                background-color: #f8fafc;
            }

            #serviceTickets tbody tr:hover td {
                background-color: #eef2f7;
            }

            #serviceTickets td.ticket-actions {
                padding: 0;
            }

            .badge-incident {
                background: rgba(220, 53, 69, 0.15);
                color: #dc3545;
                border: 1px solid rgba(220, 53, 69, 0.25);
            }

            .badge-service {
                background: rgba(0, 123, 255, 0.15);
                color: #007bff;
                border: 1px solid rgba(0, 123, 255, 0.25);
            }

            .badge-change {
                background: rgba(40, 167, 69, 0.15);
                color: #28a745;
                border: 1px solid rgba(40, 167, 69, 0.25);
            }

            /* SLA Status Badges */
            .badge-sla-ontrack {
                background: rgba(40, 167, 69, 0.15);
                color: #1e7e34;
                border: 1px solid rgba(40, 167, 69, 0.3);
            }

            .badge-sla-warning {
                background: rgba(255, 193, 7, 0.15);
                color: #d39e00;
                border: 1px solid rgba(255, 193, 7, 0.3);
            }

            .badge-sla-overdue {
                background: rgba(220, 53, 69, 0.15);
                color: #bd2130;
                border: 1px solid rgba(220, 53, 69, 0.3);
            }

            .badge-sla-default {
                background: rgba(108, 117, 125, 0.15);
                color: #495057;
                border: 1px solid rgba(108, 117, 125, 0.25);
            }

            /* Status Badge Colors - ITIL/ITSM Standard (Enhanced) */

            /* Draft - Gray (ฉบับร่าง) */
            .badge-status-default {
                background: linear-gradient(135deg, #6c757d 0%, #868e96 100%);
                color: #ffffff;
                border: none;
                font-weight: 600;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            /* New/Assigned - Orange Red (งานใหม่/มอบหมายแล้ว) - สีร้อน */
            .badge-status-assigned {
                background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
                color: #ffffff;
                border: none;
                font-weight: 700;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
                box-shadow: 0 3px 6px rgba(255, 107, 53, 0.3);
                animation: pulse-orange 2s ease-in-out infinite;
            }

            @keyframes pulse-orange {
                0%, 100% { box-shadow: 0 3px 6px rgba(255, 107, 53, 0.3); }
                50% { box-shadow: 0 3px 10px rgba(255, 107, 53, 0.5); }
            }

            /* On Process/In Progress - Amber (กำลังดำเนินการ) - สีร้อน */
            .badge-status-process {
                background: linear-gradient(135deg, #f39c12 0%, #f5ab35 100%);
                color: #ffffff;
                border: none;
                font-weight: 700;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
                box-shadow: 0 3px 6px rgba(243, 156, 18, 0.3);
            }

            .badge-status-progress {
                background: linear-gradient(135deg, #f39c12 0%, #f5ab35 100%);
                color: #ffffff;
                border: none;
                font-weight: 700;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
                box-shadow: 0 3px 6px rgba(243, 156, 18, 0.3);
            }

            /* Pending/Waiting - Yellow Orange (รอดำเนินการ) - สีร้อน */
            .badge-status-pending {
                background: linear-gradient(135deg, #ffc107 0%, #ffca28 100%);
                color: #000000;
                border: none;
                font-weight: 700;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
                box-shadow: 0 3px 6px rgba(255, 193, 7, 0.3);
            }

            .badge-status-waiting {
                background: linear-gradient(135deg, #ffc107 0%, #ffca28 100%);
                color: #000000;
                border: none;
                font-weight: 700;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
                box-shadow: 0 3px 6px rgba(255, 193, 7, 0.3);
            }

            /* Resolved - Green (แก้ไขเรียบร้อย) - สีเย็น */
            .badge-status-resolved {
                background: linear-gradient(135deg, #28a745 0%, #34ce57 100%);
                color: #ffffff;
                border: none;
                font-weight: 700;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
                box-shadow: 0 3px 6px rgba(40, 167, 69, 0.3);
            }

            /* Closed - Dark Teal (ปิดงานแล้ว) - สีเย็นเข้ม */
            .badge-status-containment {
                background: linear-gradient(135deg, #17a2b8 0%, #20c9e0 100%);
                color: #ffffff;
                border: none;
                font-weight: 700;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
                box-shadow: 0 3px 6px rgba(23, 162, 184, 0.3);
            }

            /* Cancelled/CAB Review - Red (ยกเลิก) - สีร้อนเข้ม */
            .badge-status-cab-review {
                background: linear-gradient(135deg, #dc3545 0%, #e4606d 100%);
                color: #ffffff;
                border: none;
                font-weight: 700;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
                box-shadow: 0 3px 6px rgba(220, 53, 69, 0.3);
            }

            /* Approved - Blue (อนุมัติแล้ว) */
            .badge-status-approved {
                background: linear-gradient(135deg, #007bff 0%, #0d6efd 100%);
                color: #ffffff;
                border: none;
                font-weight: 700;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
                box-shadow: 0 3px 6px rgba(0, 123, 255, 0.3);
            }

            /* Resolved Pending - Purple (รอยืนยันการแก้ไข) */
            .badge-status-resolved-pending {
                background: linear-gradient(135deg, #6f42c1 0%, #8357d5 100%);
                color: #ffffff;
                border: none;
                font-weight: 700;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
                box-shadow: 0 3px 6px rgba(111, 66, 193, 0.3);
            }

            /* Scheduled - Orange (กำหนดการแล้ว) - สีร้อน */
            .badge-status-scheduled {
                background: linear-gradient(135deg, #fd7e14 0%, #ff922b 100%);
                color: #ffffff;
                border: none;
                font-weight: 700;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
                box-shadow: 0 3px 6px rgba(253, 126, 20, 0.3);
            }

            /* Pending Approval - Pink (รออนุมัติ) - สีร้อน */
            .badge-status-pending-approval {
                background: linear-gradient(135deg, #e83e8c 0%, #ec5fa0 100%);
                color: #ffffff;
                border: none;
                font-weight: 700;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
                box-shadow: 0 3px 6px rgba(232, 62, 140, 0.3);
            }

            .ticket-summary {
                min-width: 240px;
            }

            .ticket-header-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.5rem;
                gap: 1rem;
            }

            .ticket-summary .ticket-number {
                flex: 0 0 auto;
            }

            .ticket-summary .ticket-number a {
                font-weight: 600;
                font-size: 1.05rem;
                color: #007bff;
                text-decoration: none;
            }

            .status-badge-container {
                flex: 0 0 auto;
                margin-left: auto;
            }

            .status-badge-container .badge {
                white-space: nowrap;
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
            }

            .status-badge-container .badge i {
                font-size: 0.85rem;
            }

            .ticket-summary .ticket-number a:hover {
                text-decoration: underline;
            }

            .ticket-summary .subject-line {
                margin-top: 0.4rem;
            }

            .ticket-summary .subject-link {
                display: block;
                font-weight: 600;
                color: #212529;
                text-decoration: none;
                line-height: 1.35;
                white-space: normal;
            }

            .ticket-summary .subject-link:hover {
                text-decoration: underline;
            }

            .ticket-summary .badge-group {
                display: flex;
                flex-wrap: wrap;
                gap: 0.35rem;
                margin-top: 0.6rem;
            }

            .ticket-summary .badge-group .badge {
                font-size: 0.75rem;
                padding: 0.35rem 0.6rem;
            }

            .ticket-summary .meta-row {
                margin-top: 0.55rem;
                display: flex;
                flex-wrap: wrap;
                gap: 0.6rem;
                font-size: 0.82rem;
                color: #6c757d;
            }

            .ticket-summary .meta-row span {
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
            }

            .ticket-summary .meta-row i {
                color: #adb5bd;
                font-size: 0.85rem;
            }

            .ticket-summary .attachment-strip {
                margin-top: 0.55rem;
                display: flex;
                flex-wrap: wrap;
                gap: 0.45rem;
            }

            .ticket-summary .attachment-item {
                position: relative;
                width: 48px;
                height: 48px;
                border-radius: 12px;
                background: rgba(0, 0, 0, 0.04);
                overflow: hidden;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }

            .ticket-summary .attachment-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .ticket-summary .attachment-item.file-icon {
                font-size: 0.75rem;
                font-weight: 600;
                color: #495057;
                background: rgba(13, 110, 253, 0.08);
                border: 1px solid rgba(13, 110, 253, 0.12);
            }

            .ticket-summary .attachment-item .ext {
                display: inline-block;
                padding: 0.2rem 0.35rem;
                border-radius: 6px;
                background: rgba(13, 110, 253, 0.18);
                color: #0d6efd;
                font-weight: 700;
                text-transform: uppercase;
            }

            .ticket-summary .attachment-item:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
            }

            .ticket-summary .attachment-count {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 48px;
                height: 48px;
                border-radius: 12px;
                background: rgba(0, 0, 0, 0.07);
                color: #495057;
                font-weight: 600;
                font-size: 0.85rem;
            }

            .ticket-details {
                min-width: 420px;
                width: 100%;
            }

            .ticket-details .details-grid {
                display: grid;
                gap: 0.75rem;
                grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            }

            .detail-item {
                display: flex;
                flex-direction: column;
                gap: 0.2rem;
            }

            .detail-item.full-width {
                grid-column: 1 / -1;
            }

            .detail-label {
                font-size: 0.72rem;
                font-weight: 600;
                color: #6c757d;
                text-transform: uppercase;
                letter-spacing: 0.04em;
            }

            .detail-value {
                font-size: 0.94rem;
                color: #212529;
                white-space: normal;
                word-break: break-word;
            }

            .detail-value .chip {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                font-size: 0.82rem;
                font-weight: 600;
                padding: 0.35rem 0.6rem;
                border-radius: 999px;
                background: rgba(0, 123, 255, 0.12);
                color: #0d6efd;
                margin-right: 0.35rem;
            }

            .detail-value .chip .avatar {
                width: 26px;
                height: 26px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 0.7rem;
                font-weight: 700;
                background: rgba(13, 110, 253, 0.18);
                color: #0d6efd;
            }

            .detail-value .chip.reporter-chip {
                background: rgba(52, 58, 64, 0.12);
                color: #343a40;
            }

            .detail-value .chip.reporter-chip .avatar {
                background: rgba(52, 58, 64, 0.18);
                color: #343a40;
            }

            .detail-value .chip.creator-chip {
                background: rgba(111, 66, 193, 0.12);
                color: #6f42c1;
            }

            .detail-value .chip.creator-chip .avatar {
                background: rgba(111, 66, 193, 0.18);
                color: #6f42c1;
            }

            .detail-value.watcher-value {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                max-height: 3em;
            }

            .ticket-actions {
                min-width: 260px;
                width: 260px;
                padding: 0;
            }

            .ticket-actions-inner {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                height: 100%;
                padding: 1rem;
                border-left: 1px solid #edf2f7;
            }

            .ticket-actions-inner .detail-item {
                gap: 0.3rem;
            }

            .ticket-actions-inner .sla-badge .badge {
                font-size: 0.75rem;
                padding: 0.35rem 0.6rem;
            }

            @media (max-width: 1399.98px) {
                .service-table-wrapper table {
                    min-width: 0;
                }

                #serviceTickets colgroup col,
                #serviceTickets th.ticket-summary-col,
                #serviceTickets td.ticket-summary,
                #serviceTickets th.ticket-details-col,
                #serviceTickets td.ticket-details,
                #serviceTickets th.ticket-actions-col,
                #serviceTickets td.ticket-actions {
                    width: auto;
                }
            }

            @media (max-width: 991.98px) {
                .service-table-wrapper {
                    overflow: visible;
                    padding-bottom: 0;
                }

                #serviceTickets {
                    display: block;
                    width: 100%;
                    border-spacing: 0;
                }

                #serviceTickets colgroup {
                    display: none;
                }

                #serviceTickets thead {
                    border: 0;
                    clip: rect(0 0 0 0);
                    height: 1px;
                    margin: -1px;
                    overflow: hidden;
                    padding: 0;
                    position: absolute;
                    width: 1px;
                }

                #serviceTickets tbody {
                    display: block;
                }

                #serviceTickets tbody tr {
                    display: block;
                    border: 1px solid #dee2e6;
                    border-radius: 16px;
                    margin-bottom: 1.25rem;
                    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
                    background-color: #ffffff;
                }

                #serviceTickets tbody tr:last-child {
                    margin-bottom: 0;
                }

                #serviceTickets tbody td {
                    display: block;
                    width: 100% !important;
                    padding: 1rem 1.25rem;
                    border: 0;
                    background-color: transparent;
                }

                #serviceTickets tbody td + td {
                    border-top: 1px solid #e9ecef;
                }

                #serviceTickets tbody td::before {
                    content: attr(data-title);
                    display: block;
                    font-size: 0.75rem;
                    font-weight: 600;
                    letter-spacing: 0.05em;
                    text-transform: uppercase;
                    color: #6c757d;
                    margin-bottom: 0.35rem;
                }

                #serviceTickets tbody tr:nth-child(odd) td,
                #serviceTickets tbody tr:nth-child(even) td,
                #serviceTickets tbody tr:hover td {
                    background-color: transparent;
                }

                .ticket-summary,
                .ticket-details,
                .ticket-actions {
                    min-width: 0;
                    width: 100%;
                }

                .ticket-summary {
                    padding-bottom: 0.25rem;
                }

                .ticket-header-row {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 0.6rem;
                }

                .status-badge-container {
                    width: 100%;
                    margin-left: 0;
                }

                .status-badge-container .badge {
                    white-space: normal;
                    line-height: 1.35;
                    padding: 0.45rem 0.75rem;
                }

                #serviceTickets tbody td::before {
                    margin-bottom: 0.5rem;
                }

                .ticket-details .details-grid {
                    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                }

                .ticket-actions {
                    padding: 0;
                    margin: 0.75rem 0.9rem 0.95rem;
                    background-color: #eff4fb;
                    border-radius: 1rem;
                    box-shadow: inset 0 1px 0 rgba(148, 163, 184, 0.18);
                }

                .ticket-actions-inner {
                    border-left: 0;
                    padding: 1.35rem 1.4rem 1.45rem;
                    gap: 1rem;
                    background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(241, 245, 249, 1) 100%);
                    border-radius: 1rem;
                }
            }

            .metric-chips {
                display: flex;
                flex-wrap: wrap;
                gap: 0.4rem;
            }

            .metric-chip {
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
                font-size: 0.78rem;
                padding: 0.3rem 0.55rem;
                border-radius: 999px;
                background: rgba(108, 117, 125, 0.12);
                color: #495057;
            }

            .metric-chip i {
                font-size: 0.85rem;
            }

            .metric-chip.impact {
                background: rgba(40, 167, 69, 0.12);
                color: #1e7e34;
            }

            .metric-chip.priority {
                background: rgba(255, 193, 7, 0.15);
                color: #d39e00;
            }

            .ticket-actions .action-buttons .btn-group {
                width: 100%;
            }

            @media (max-width: 1200px) {
                #serviceTickets {
                    table-layout: auto;
                }

                #serviceTickets th.ticket-summary-col,
                #serviceTickets td.ticket-summary,
                #serviceTickets th.ticket-details-col,
                #serviceTickets td.ticket-details,
                #serviceTickets th.ticket-actions-col,
                #serviceTickets td.ticket-actions {
                    width: auto;
                }

                .ticket-actions {
                    min-width: 220px;
                    width: 220px;
                }
            }

            @media (max-width: 992px) {
                #serviceTickets,
                #serviceTickets thead,
                #serviceTickets tbody,
                #serviceTickets tr,
                #serviceTickets th,
                #serviceTickets td,
                #serviceTickets tfoot {
                    display: block;
                    width: 100%;
                }

                #serviceTickets thead,
                #serviceTickets tfoot {
                    display: none;
                }

                #serviceTickets tbody tr {
                    margin-bottom: 1.5rem;
                    border: 1px solid #e2e8f0;
                    border-radius: 0.85rem;
                    overflow: hidden;
                    background-color: #ffffff;
                    box-shadow: 0 10px 25px rgba(148, 163, 184, 0.2);
                }

                #serviceTickets td {
                    border: none;
                    border-top: 1px solid #eef2f7;
                    padding: 0.9rem 1.1rem;
                }

                #serviceTickets td.ticket-summary {
                    border-top: none;
                    padding-bottom: 1.1rem;
                }

                .ticket-summary,
                .ticket-details,
                .ticket-actions {
                    width: 100% !important;
                    min-width: 100% !important;
                }

                .ticket-actions-inner {
                    border-left: none;
                    border-top: 1px solid #e2e8f0;
                    border-radius: 0 0 0.85rem 0.85rem;
                }
            }
        </style>

        <!-- Navbar -->
        <!-- Main Sidebar Container -->
        <!-- Preloader -->
        <?php include  '../../include/navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Service Management</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Service Management v1</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">

                            <!-- Service Status Cards (Mockup) -->
                            <div class="row mb-3 service-metrics-row">
                                <?php foreach ($serviceMetrics as $metric): ?>
                                    <?php
                                        $metricKey = $metric['filter_key'] ?? '';
                                        $metricValue = $metric['filter_value'] ?? '';
                                        $isActive = false;
                                        if ($metricKey === 'status' && $filterStatus !== '' && $filterStatus === $metricValue) {
                                            $isActive = true;
                                        } elseif ($metricKey === 'sla_status' && $filterSlaStatus !== '' && $filterSlaStatus === $metricValue) {
                                            $isActive = true;
                                        } elseif ($metricKey === 'all' && $filterStatus === '' && $filterSlaStatus === '') {
                                            $isActive = true;
                                        }
                                    ?>
                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 d-flex metric-card-col">
                                        <div class="small-box <?php echo htmlspecialchars($metric['color']); ?> flex-fill shadow-sm metric-card-trigger<?php echo $isActive ? ' metric-active' : ''; ?>"
                                             role="button" tabindex="0"
                                             data-filter-key="<?php echo htmlspecialchars($metricKey); ?>"
                                             data-filter-value="<?php echo htmlspecialchars($metricValue); ?>">
                                            <div class="inner">
                                                <h3 class="mb-1" style="font-size: 2rem; line-height: 1.2;">
                                                    <?php echo number_format($metric['value']); ?>
                                                </h3>
                                                <p class="mb-1" style="font-size: 0.95rem;">
                                                    <?php echo htmlspecialchars($metric['title']); ?>
                                                </p>
                                                <span class="d-block text-white-50" style="font-size: 0.75rem;">
                                                    <?php echo htmlspecialchars($metric['description']); ?>
                                                </span>
                                            </div>
                                            <div class="icon">
                                                <i class="<?php echo htmlspecialchars($metric['icon']); ?>"></i>
                                            </div>
                                            <div class="small-box-footer text-xs text-white-50 text-center">
                                                Real-time Data
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- //Service Status Cards (Mockup) -->



                            <!-- Section Search -->
                            <section class="content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-info">
                                            <div class="card-header">
                                                <h3 class="card-title font1 mb-0">
                                                    ค้นหา
                                                </h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <form action="" method="GET" id="serviceFilterForm">
                                                    <input type="hidden" name="sla_status" id="slaStatusInput" value="<?php echo htmlspecialchars($filterSlaStatus); ?>">
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group ">
                                                                <input type="text" class="form-control " id="searchservice" name="searchservice" value="<?php echo htmlspecialchars($searchText); ?>" placeholder="ค้นหา...">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group d-flex align-items-end">
                                                                <button type="submit" class="btn btn-primary mr-2" id="search" name="search">ค้นหา</button>
                                                                <button type="button" class="btn btn-outline-secondary" id="resetFilters">รีเซ็ต</button>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-5">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Category Type</label>
                                                                <select class="custom-select select2" name="categorytype">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($categoryTypeOptions as $v): ?>
                                                                        <option value="<?php echo htmlspecialchars($v); ?>" <?php echo ($filterType === $v ? 'selected' : ''); ?>><?php echo htmlspecialchars($v); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Job Owner</label>
                                                                <select class="custom-select select2" name="jobowner">
                                                                    <option value="">ทั้งหมด</option>
                                                                    <?php foreach ($jobOwnerOptions as $u): ?>
                                                                        <option value="<?php echo htmlspecialchars($u['user_id']); ?>" <?php echo (!empty($filterJobOwner) && $filterJobOwner == $u['user_id'] ? 'selected' : ''); ?>>
                                                                            <?php echo htmlspecialchars($u['full_name']); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>SLA</label>
                                                                <select class="custom-select select2" name="sla">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($slaOptions as $v): ?>
                                                                        <option value="<?php echo htmlspecialchars($v); ?>" <?php echo ($filterSla !== '' && $filterSla == $v ? 'selected' : ''); ?>><?php echo htmlspecialchars($v); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>SLA Status</label>
                                                                <select class="custom-select select2" name="sla_status">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($slaStatusOptions as $v): ?>
                                                                        <option value="<?php echo htmlspecialchars($v); ?>" <?php echo ($filterSlaStatus === $v ? 'selected' : ''); ?>><?php echo htmlspecialchars($v); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Priority</label>
                                                                <select class="custom-select select2" name="priority">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($priorityOptions as $v): ?>
                                                                        <option value="<?php echo htmlspecialchars($v); ?>" <?php echo ($filterPriority === $v ? 'selected' : ''); ?>><?php echo htmlspecialchars($v); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Source</label>
                                                                <select class="custom-select select2" name="source">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($sourceOptions as $v): ?>
                                                                        <option value="<?php echo htmlspecialchars($v); ?>" <?php echo ($filterSource === $v ? 'selected' : ''); ?>><?php echo htmlspecialchars($v); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Urgency</label>
                                                                <select class="custom-select select2" name="urgency">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($urgencyOptions as $v): ?>
                                                                        <option value="<?php echo htmlspecialchars($v); ?>" <?php echo ($filterUrgency === $v ? 'selected' : ''); ?>><?php echo htmlspecialchars($v); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Impact</label>
                                                                <select class="custom-select select2" name="impact">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($impactOptions as $v): ?>
                                                                        <option value="<?php echo htmlspecialchars($v); ?>" <?php echo ($filterImpact === $v ? 'selected' : ''); ?>><?php echo htmlspecialchars($v); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                         <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Status</label>
                                                                <select class="custom-select select2" name="status">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($statusOptions as $v): ?>
                                                                        <option value="<?php echo htmlspecialchars($v); ?>" <?php echo ($filterStatus === $v ? 'selected' : ''); ?>><?php echo htmlspecialchars($v); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Service Category</label>
                                                                <select class="custom-select select2" name="servicecategory">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($serviceCatOptions as $v): ?>
                                                                        <option value="<?php echo htmlspecialchars($v); ?>" <?php echo ($filterServiceCat === $v ? 'selected' : ''); ?>><?php echo htmlspecialchars($v); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Category</label>
                                                                <select class="custom-select select2" name="category">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($categoryOptions as $v): ?>
                                                                        <option value="<?php echo htmlspecialchars($v); ?>" <?php echo ($filterCategory === $v ? 'selected' : ''); ?>><?php echo htmlspecialchars($v); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Sub-Category</label>
                                                                <select class="custom-select select2" name="subcategory">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($subCategoryOptions as $v): ?>
                                                                        <option value="<?php echo htmlspecialchars($v); ?>" <?php echo ($filterSubCat === $v ? 'selected' : ''); ?>><?php echo htmlspecialchars($v); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Channel</label>
                                                                <select class="custom-select select2" name="channel">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($channelOptions as $v): ?>
                                                                        <option value="<?php echo htmlspecialchars($v); ?>" <?php echo ($filterChannel === $v ? 'selected' : ''); ?>><?php echo htmlspecialchars($v); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label>Project</label>
                                                                <select class="custom-select select2" name="project_id">
                                                                    <option value="">ทั้งหมด</option>
                                                                    <?php foreach ($projectOptions as $proj): ?>
                                                                        <option value="<?php echo htmlspecialchars($proj['project_id']); ?>" <?php echo ($filterProject === $proj['project_id'] ? 'selected' : ''); ?>>
                                                                            <?php echo htmlspecialchars($proj['project_name']); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Start Date</label>
                                                                <input type="date" class="form-control" name="start_date" value="<?php echo htmlspecialchars($filterStartDate); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>End Date</label>
                                                                <input type="date" class="form-control" name="end_date" value="<?php echo htmlspecialchars($filterEndDate); ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="card-footer">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                    </div>
                            </section>
                            <!-- //Section Search -->

                            <!-- Section ปุ่มเพิ่มข้อมูล -->
                            <div class="col-md-12 pb-3">
                                <a href="add_account.php" class="btn btn-success btn-sm float-right">เพิ่มข้อมูล<i class=""></i></a>
                            </div><br>
                            <!-- //Section ปุ่มเพิ่มข้อมูล -->

                            <!-- Section ตารางแสดงผล -->
                            <div class="card">
                                <div class="card-header">
                                    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap" style="gap:0.5rem;">
                                        <h3 class="card-title mb-0">Service Ticket Overview</h3>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo htmlspecialchars($modernViewUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                                                <i class="fas fa-th-large mr-1"></i> มุมมองใหม่
                                            </a>
                                            <a href="<?php echo htmlspecialchars($classicViewUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-primary">
                                                <i class="fas fa-table mr-1"></i> มุมมองตาราง
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="mb-3 d-flex flex-wrap align-items-center" style="gap:0.5rem;">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnExportCopy">Copy</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnExportCSV">CSV</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnExportExcel">Excel</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnExportPDF">PDF</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnExportPrint">Print</button>
                                    </div>
                                    <div class="service-table-wrapper table-responsive">
                                    <table id="serviceTickets" class="table table-bordered table-striped table-hover">
                                        <colgroup>
                                            <col style="width:45%">
                                            <col style="width:35%">
                                            <col style="width:20%">
                                        </colgroup>
<thead class="bg-light">
                                            <tr class="text-center align-middle">
                                                <th class="text-nowrap table-header-tooltip ticket-summary-col" title="รายละเอียด Ticket โดยรวม">Ticket</th>
                                                <th class="text-nowrap table-header-tooltip ticket-details-col" title="ข้อมูลประกอบเพิ่มเติมของ Ticket">รายละเอียด</th>
                                                <th class="text-nowrap table-header-tooltip" title="วันที่เริ่มดำเนินการ">วันเริ่ม</th>
                                                <th class="text-nowrap table-header-tooltip" title="วันที่กำหนดแล้วเสร็จ">วันแล้วเสร็จ</th>
                                                <th class="text-nowrap table-header-tooltip ticket-actions-col text-center" title="สถานะ SLA และการดำเนินการ">SLA &amp; Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $exportRows = []; ?>
                                            <?php if (empty($tickets)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">ไม่พบข้อมูล Ticket</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($tickets as $ticket): ?>
                                                    <?php
                                                        $typeConfig = $typeStyles[$ticket['ticket_type']] ?? ['class' => 'badge badge-pill badge-secondary', 'label' => htmlspecialchars($ticket['ticket_type'])];
                                                        $slaStatusRaw = $ticket['sla_status'] ?? '';
                                                        if (is_string($slaStatusRaw)) {
                                                            $slaStatusRaw = trim($slaStatusRaw);
                                                        }
                                                        $slaStatusKey = $slaStatusRaw === null ? '' : $slaStatusRaw;
                                                        $slaStatusConfig = $slaStatusStyles[$slaStatusKey] ?? null;
                                                        $slaBadgeClass = $slaStatusConfig['class'] ?? 'badge badge-pill badge-sla-default';
                                                        $slaBadgeLabel = $slaStatusConfig['label'] ?? ($slaStatusKey === '' ? 'N/A' : $slaStatusKey);
                                                        $statusConfig = $statusStyles[$ticket['status']] ?? ['class' => 'badge badge-pill badge-status-default'];
                                                        [$subjectDisplay, $subjectFull] = summarizeSubject($ticket['subject']);

                                                        $jobOwnerName = $ticket['job_owner_name'] ?? '-';
                                                        $reporterName = $ticket['reporter_name'] ?? '-';
                                                        $creatorName = $ticket['creator_name'] ?? '-';

                                                        // คำนวณ SLA
                                                        $slaDisplay = '-';
                                                        if ($ticket['sla_target']) {
                                                            if ($ticket['sla_target'] < 24) {
                                                                $slaDisplay = $ticket['sla_target'] . ' ชม.';
                                                            } else {
                                                                $slaDisplay = round($ticket['sla_target'] / 24, 1) . ' วัน';
                                                            }
                                                        }

                                                        // จัดการชื่อ Project ให้แสดงแค่ 150 ตัวอักษร
                                                        $projectName = $ticket['project_name'] ?? '-';
                                                        $projectDisplay = $projectName;
                                                        $projectFull = $projectName;
                                                        if (mb_strlen($projectName, 'UTF-8') > 150) {
                                                            $projectDisplay = mb_substr($projectName, 0, 150, 'UTF-8') . '...';
                                                            $projectFull = $projectName;



                                                        }

                                                        $projectSummaryText = $projectName;
                                                        if (mb_strlen($projectSummaryText, 'UTF-8') > 40) {
                                                            $projectSummaryText = mb_substr($projectSummaryText, 0, 40, 'UTF-8') . '...';
                                                        }
                                                        $projectSummary = htmlspecialchars($projectSummaryText, ENT_QUOTES, 'UTF-8');

                                                        // ตัดข้อความ Watchers ที่ยาวเกิน 150 ตัวอักษรให้แสดง ... และเก็บฉบับเต็มไว้เป็น tooltip
                                                        $watcherNames = $ticket['watcher_names'] ?? '-';
                                                        $watchersFull = $watcherNames;
                                                        $watchersDisplay = $watcherNames;
                                                        if ($watchersDisplay !== '-' && mb_strlen($watchersDisplay, 'UTF-8') > 150) {
                                                            $watchersDisplay = mb_substr($watchersDisplay, 0, 150, 'UTF-8') . '...';
                                                        }

                                                        // ตัดข้อความ Category ที่ยาวเกิน 30 ตัวอักษร
                                                        $serviceCategory = $ticket['service_category'] ?? '-';
                                                        $serviceCategoryDisplay = mb_strlen($serviceCategory, 'UTF-8') > 30 
                                                            ? mb_substr($serviceCategory, 0, 30, 'UTF-8') . '...' 
                                                            : $serviceCategory;
                                                        
                                                        $category = $ticket['category'] ?? '-';
                                                        $categoryDisplay = mb_strlen($category, 'UTF-8') > 30 
                                                            ? mb_substr($category, 0, 30, 'UTF-8') . '...' 
                                                            : $category;

                                                        $createdDisplay = date('Y-m-d H:i', strtotime($ticket['created_at']));
                                                        $createdShort = date('d M Y', strtotime($ticket['created_at']));
                                                        
                                                        // จัดการแสดงวันที่เริ่มและสิ้นสุด
                                                        $startAtDisplay = '-';
                                                        if (!empty($ticket['start_at']) && $ticket['start_at'] !== '0000-00-00 00:00:00') {
                                                            $startAtDisplay = date('d/m/Y H:i', strtotime($ticket['start_at']));
                                                        }
                                                        $dueAtDisplay = '-';
                                                        if (!empty($ticket['due_at']) && $ticket['due_at'] !== '0000-00-00 00:00:00') {
                                                            $dueAtDisplay = date('d/m/Y H:i', strtotime($ticket['due_at']));
                                                        }
                                                        
                                                        $resolvedDisplay = '-';
                                                        if (!empty($ticket['resolved_at']) && $ticket['resolved_at'] !== '0000-00-00 00:00:00') {
                                                            $resolvedDisplay = date('Y-m-d H:i', strtotime($ticket['resolved_at']));
                                                        }
                                                        $closedDisplay = '-';
                                                        if (!empty($ticket['closed_at']) && $ticket['closed_at'] !== '0000-00-00 00:00:00') {
                                                            $closedDisplay = date('Y-m-d H:i', strtotime($ticket['closed_at']));
                                                        }
                                                        $statusRaw = is_string($ticket['status']) ? trim($ticket['status']) : '';
                                                        $statusLower = mb_strtolower($statusRaw, 'UTF-8');
                                                        $shouldShowResolved = (
                                                            $statusLower === 'resolved' ||
                                                            $statusLower === 'closed' ||
                                                            mb_stripos($statusRaw, 'resolved', 0, 'UTF-8') !== false
                                                        );
                                                        $shouldShowClosed = ($statusLower === 'closed');

                                                        $attachmentCount = (int)($ticket['attachment_count'] ?? 0);
                                                        $attachmentRecords = [];
                                                        if (!empty($ticket['attachment_list'])) {
                                                            $rawAttachments = explode('||', $ticket['attachment_list']);
                                                            foreach ($rawAttachments as $rawAttachment) {
                                                                if ($rawAttachment === '') {
                                                                    continue;
                                                                }
                                                                $pieces = explode('::', $rawAttachment);
                                                                $fileName = $pieces[1] ?? '';
                                                                $filePath = $pieces[2] ?? '';
                                                                $mimeType = $pieces[3] ?? '';
                                                                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                                $isImage = false;
                                                                if ($mimeType !== '') {
                                                                    $isImage = (strpos($mimeType, 'image/') === 0);
                                                                }
                                                                if (!$isImage && $ext !== '') {
                                                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'], true);
                                                                }
                                                                if ($filePath !== '') {
                                                                    $attachmentRecords[] = [
                                                                        'name' => $fileName,
                                                                        'path' => $filePath,
                                                                        'ext'  => $ext,
                                                                        'is_image' => $isImage,
                                                                    ];
                                                                }
                                                            }
                                                        }
                                                        $attachmentPreviews = array_slice($attachmentRecords, 0, 3);
                                                        $attachmentNames = [];
                                                        foreach ($attachmentRecords as $record) {
                                                            if (!empty($record['name'])) {
                                                                $attachmentNames[] = $record['name'];
                                                            }
                                                        }
                                                        $attachmentsExportValue = implode(', ', $attachmentNames);
                                                        $exportRows[] = [
                                                            'ticket_no' => $ticket['ticket_no'] ?? '',
                                                            'type' => $ticket['ticket_type'] ?? '',
                                                            'status' => $ticket['status'] ?? '',
                                                            'sla_status' => $slaStatusKey === '' ? '' : $slaStatusKey,
                                                            'project' => ($projectName === '-' ? '' : $projectName),
                                                            'subject' => $ticket['subject'] ?? '',
                                                            'job_owner' => ($jobOwnerName === '-' ? '' : $jobOwnerName),
                                                            'reporter' => ($reporterName === '-' ? '' : $reporterName),
                                                            'created_by' => ($creatorName === '-' ? '' : $creatorName),
                                                            'watchers' => ($watchersFull === '-' ? '' : $watchersFull),
                                                            'sla_target' => $ticket['sla_target'] !== null ? (string)$ticket['sla_target'] : '',
                                                            'service_category' => $ticket['service_category'] ?? '',
                                                            'category' => $ticket['category'] ?? '',
                                                            'sub_category' => $ticket['sub_category'] ?? '',
                                                            'impact' => $ticket['impact'] ?? '',
                                                            'priority' => $ticket['priority'] ?? '',
                                                            'source' => $ticket['source'] ?? '',
                                                            'created_at' => $createdDisplay,
                                                            'start_at' => $startAtDisplay,
                                                            'due_at' => $dueAtDisplay,
                                                            'attachments' => $attachmentsExportValue,
                                                        ];
                                                ?>
                                                    <tr>
                                                        <td class="ticket-summary" data-title="Ticket">
                                                            <div class="ticket-header-row">
                                                                <div class="ticket-number">
                                                                    <a href="view_ticket.php?id=<?php echo urlencode($ticket['ticket_id']); ?>">
                                                                        <?php echo htmlspecialchars($ticket['ticket_no']); ?>
                                                                    </a>
                                                                </div>
                                                                <div class="status-badge-container">
                                                                    <span class="<?php echo htmlspecialchars($statusConfig['class'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                        <i class="<?php echo htmlspecialchars($statusConfig['icon'] ?? 'fas fa-circle', ENT_QUOTES, 'UTF-8'); ?>"></i>
                                                                        <?php echo htmlspecialchars($ticket['status']); ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="subject-line" data-toggle="tooltip" data-placement="top" title="<?php echo $subjectFull; ?>">
                                                                <a href="view_ticket.php?id=<?php echo urlencode($ticket['ticket_id']); ?>" class="subject-link">
                                                                    <?php echo $subjectDisplay; ?>
                                                                </a>
                                                            </div>
                                                            <div class="badge-group">
                                                                <span class="<?php echo htmlspecialchars($typeConfig['class'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($typeConfig['label']); ?></span>
                                                            </div>
                                                            <div class="meta-row">
                                                                <span><i class="fas fa-layer-group"></i><?php echo $projectSummary; ?></span>
                                                                <span><i class="far fa-clock"></i><?php echo htmlspecialchars($createdShort, ENT_QUOTES, 'UTF-8'); ?></span>
                                                            </div>
                                                            <?php if ($attachmentCount > 0): ?>
                                                            <div class="attachment-strip">
                                                                <?php foreach ($attachmentPreviews as $preview): ?>
                                                                    <?php
                                                                        $fileTitle = $preview['name'] !== '' ? $preview['name'] : 'ดาวน์โหลดไฟล์';
                                                                        $filePathSafe = htmlspecialchars($preview['path'], ENT_QUOTES, 'UTF-8');
                                                                        $fileTitleSafe = htmlspecialchars($fileTitle, ENT_QUOTES, 'UTF-8');
                                                                    ?>
                                                                    <a href="<?php echo $filePathSafe; ?>" class="attachment-item<?php echo $preview['is_image'] ? '' : ' file-icon'; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $fileTitleSafe; ?>" target="_blank" rel="noopener">
                                                                        <?php if ($preview['is_image']): ?>
                                                                            <img src="<?php echo $filePathSafe; ?>" alt="<?php echo $fileTitleSafe; ?>">
                                                                        <?php else: ?>
                                                                            <span class="ext"><?php echo htmlspecialchars($preview['ext'] ?: 'file', ENT_QUOTES, 'UTF-8'); ?></span>
                                                                        <?php endif; ?>
                                                                    </a>
                                                                <?php endforeach; ?>
                                                                <?php if ($attachmentCount > count($attachmentPreviews)): ?>
                                                                    <span class="attachment-count">+<?php echo $attachmentCount - count($attachmentPreviews); ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="ticket-details" data-title="รายละเอียด">
                                                            <div class="details-grid">
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Project</span>
                                                                    <span class="detail-value" data-toggle="tooltip" data-placement="top" data-html="true" title="<?php echo htmlspecialchars($projectFull, ENT_QUOTES, 'UTF-8'); ?>">
                                                                        <?php echo htmlspecialchars($projectDisplay); ?>
                                                                    </span>
                                                                </div>
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Job Owner</span>
                                                                    <?php
                                                                        $jobOwnerInitials = '-';
                                                                        if ($jobOwnerName !== '-' && $jobOwnerName !== '') {
                                                                            $parts = preg_split('/\s+/', trim($jobOwnerName));
                                                                            $initials = '';
                                                                            foreach ($parts as $part) {
                                                                                if ($part !== '') {
                                                                                    $initials .= mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8');
                                                                                }
                                                                                if (mb_strlen($initials, 'UTF-8') >= 2) {
                                                                                    break;
                                                                                }
                                                                            }
                                                                            $jobOwnerInitials = $initials ?: mb_strtoupper(mb_substr($jobOwnerName, 0, 2, 'UTF-8'), 'UTF-8');
                                                                        }
                                                                    ?>
                                                                    <span class="detail-value">
                                                                        <span class="chip">
                                                                            <span class="avatar"><?php echo htmlspecialchars($jobOwnerInitials, ENT_QUOTES, 'UTF-8'); ?></span>
                                                                            <?php echo htmlspecialchars($jobOwnerName, ENT_QUOTES, 'UTF-8'); ?>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Reporter</span>
                                                                    <?php
                                                                        $reporterInitials = '-';
                                                                        if ($reporterName !== '-' && $reporterName !== '') {
                                                                            $partsReporter = preg_split('/\s+/', trim($reporterName));
                                                                            $initialsReporter = '';
                                                                            foreach ($partsReporter as $part) {
                                                                                if ($part !== '') {
                                                                                    $initialsReporter .= mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8');
                                                                                }
                                                                                if (mb_strlen($initialsReporter, 'UTF-8') >= 2) {
                                                                                    break;
                                                                                }
                                                                            }
                                                                            $reporterInitials = $initialsReporter ?: mb_strtoupper(mb_substr($reporterName, 0, 2, 'UTF-8'), 'UTF-8');
                                                                        }
                                                                    ?>
                                                                    <span class="detail-value">
                                                                        <span class="chip reporter-chip">
                                                                            <span class="avatar"><?php echo htmlspecialchars($reporterInitials, ENT_QUOTES, 'UTF-8'); ?></span>
                                                                            <?php echo htmlspecialchars($reporterName, ENT_QUOTES, 'UTF-8'); ?>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Created By</span>
                                                                    <?php
                                                                        $creatorInitials = '-';
                                                                        if ($creatorName !== '-' && $creatorName !== '') {
                                                                            $partsCreator = preg_split('/\s+/', trim($creatorName));
                                                                            $initialsCreator = '';
                                                                            foreach ($partsCreator as $part) {
                                                                                if ($part !== '') {
                                                                                    $initialsCreator .= mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8');
                                                                                }
                                                                                if (mb_strlen($initialsCreator, 'UTF-8') >= 2) {
                                                                                    break;
                                                                                }
                                                                            }
                                                                            $creatorInitials = $initialsCreator ?: mb_strtoupper(mb_substr($creatorName, 0, 2, 'UTF-8'), 'UTF-8');
                                                                        }
                                                                    ?>
                                                                    <span class="detail-value">
                                                                        <span class="chip creator-chip">
                                                                            <span class="avatar"><?php echo htmlspecialchars($creatorInitials, ENT_QUOTES, 'UTF-8'); ?></span>
                                                                            <?php echo htmlspecialchars($creatorName, ENT_QUOTES, 'UTF-8'); ?>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                                <div class="detail-item full-width">
                                                                    <span class="detail-label">Watchers</span>
                                                                    <span class="detail-value watcher-value" data-toggle="tooltip" data-placement="top" title="<?php echo htmlspecialchars($watchersFull, ENT_QUOTES, 'UTF-8'); ?>">
                                                                        <?php echo htmlspecialchars($watchersDisplay, ENT_QUOTES, 'UTF-8'); ?>
                                                                    </span>
                                                                </div>
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Service Category</span>
                                                                    <span class="detail-value" data-toggle="tooltip" data-placement="top" title="<?php echo htmlspecialchars($serviceCategory, ENT_QUOTES, 'UTF-8'); ?>">
                                                                        <?php echo htmlspecialchars($serviceCategoryDisplay); ?>
                                                                    </span>
                                                                </div>
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Category</span>
                                                                    <span class="detail-value" data-toggle="tooltip" data-placement="top" title="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
                                                                        <?php echo htmlspecialchars($categoryDisplay); ?>
                                                                    </span>
                                                                </div>
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Sub-Category</span>
                                                                    <span class="detail-value"><?php echo htmlspecialchars($ticket['sub_category'] ?? '-'); ?></span>
                                                                </div>
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Source</span>
                                                                    <span class="detail-value"><?php echo htmlspecialchars($ticket['source'] ?? '-'); ?></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center text-nowrap" data-title="วันเริ่ม">
                                                            <?php echo htmlspecialchars($startAtDisplay); ?>
                                                        </td>
                                                        <td class="text-center text-nowrap" data-title="วันแล้วเสร็จ">
                                                            <?php echo htmlspecialchars($dueAtDisplay); ?>
                                                        </td>
                                                        <td class="ticket-actions" data-title="SLA &amp; Action">
                                                            <div class="ticket-actions-inner">
                                                                <div class="detail-item">
                                                                    <span class="detail-label">SLA Target</span>
                                                                    <span class="detail-value"><?php echo $slaDisplay; ?></span>
                                                                </div>
                                                                <div class="detail-item sla-badge">
                                                                    <span class="detail-label">SLA Status</span>
                                                                    <span class="detail-value">
                                                                        <span class="badge badge-pill <?php echo htmlspecialchars($slaBadgeClass, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($slaBadgeLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                                                                    </span>
                                                                </div>
                                                                <div class="metric-chips">
                                                                    <span class="metric-chip impact"><i class="fas fa-bullseye"></i><?php echo htmlspecialchars($ticket['impact'] ?? '-'); ?></span>
                                                                    <span class="metric-chip priority"><i class="fas fa-exclamation-circle"></i><?php echo htmlspecialchars($ticket['priority'] ?? '-'); ?></span>
                                                                </div>
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Created</span>
                                                                    <span class="detail-value"><?php echo htmlspecialchars($createdDisplay, ENT_QUOTES, 'UTF-8'); ?></span>
                                                                </div>
                                                                <?php if ($shouldShowResolved): ?>
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Resolved</span>
                                                                    <span class="detail-value"><?php echo htmlspecialchars($resolvedDisplay, ENT_QUOTES, 'UTF-8'); ?></span>
                                                                </div>
                                                                <?php endif; ?>
                                                                <?php if ($shouldShowClosed): ?>
                                                                <div class="detail-item">
                                                                    <span class="detail-label">Closed</span>
                                                                    <span class="detail-value"><?php echo htmlspecialchars($closedDisplay, ENT_QUOTES, 'UTF-8'); ?></span>
                                                                </div>
                                                                <?php endif; ?>
                                                                <div class="action-buttons">
                                                                    <?php
                                                                    // สิทธิ์แก้ไข: Executive, Account Management, Sale Supervisor หรือ Job Owner
                                                                    $canEdit = ($role === 'Executive' ||
                                                                               $role === 'Account Management' ||
                                                                               $role === 'Sale Supervisor' ||
                                                                               $ticket['job_owner'] === $user_id);

                                                                    // ตรวจสอบสถานะ - ซ่อนปุ่ม Edit เมื่อสถานะเป็น Resolved หรือ Closed
                                                                    $ticketStatus = trim($ticket['status'] ?? '');
                                                                    $isResolved = (strcasecmp($ticketStatus, 'Resolved') === 0);
                                                                    $isClosed = (strcasecmp($ticketStatus, 'Closed') === 0);
                                                                    $canShowEdit = $canEdit && !$isResolved && !$isClosed;
                                                                    ?>
                                                                    <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                                                        <a href="view_ticket.php?id=<?php echo urlencode($ticket['ticket_id']); ?>" class="btn btn-info" title="View"><i class="fas fa-eye"></i></a>
                                                                        <?php if ($canShowEdit): ?>
                                                                        <a href="edit_ticket.php?id=<?php echo urlencode($ticket['ticket_id']); ?>" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot class="bg-light">
                                            <tr class="text-center align-middle">
                                                <th class="ticket-summary-col">Ticket</th>
                                                <th class="ticket-details-col">รายละเอียด</th>
                                                <th class="ticket-actions-col">SLA &amp; Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    </div>
                                    <script>
                                        window.serviceTicketExportHeaders = <?php echo json_encode([
                                            'Ticket No',
                                            'Type',
                                            'Status',
                                            'SLA Status',
                                            'Project',
                                            'Subject',
                                            'Job Owner',
                                            'Reporter',
                                            'Created By',
                                            'Watchers',
                                            'SLA Target',
                                            'Service Category',
                                            'Category',
                                            'Sub Category',
                                            'Impact',
                                            'Priority',
                                            'Source',
                                            'Created At',
                                            'Attachments'
                                        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
                                        window.serviceTicketExportRows = <?php echo json_encode($exportRows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
                                    </script>

                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- //Section ตารางแสดงผล -->

                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- // include footer -->
        <?php include  '../../include/footer.php'; ?>
    </div>
    <!-- ./wrapper -->
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();

            // Initialize Select2 for all select dropdowns in filter form
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'เลือก...',
                allowClear: true,
                width: '100%'
            });

            var $filterForm = $('#serviceFilterForm');
            var defaultFilters = <?php echo json_encode($defaultFilterValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
            var $statusSelect = $filterForm.find('select[name="status"]');
            var $slaStatusInput = $('#slaStatusInput');

            function submitWithFilter(key, value) {
                if (!$filterForm.length) {
                    return;
                }

                if (key === 'status') {
                    $slaStatusInput.val('');
                    $statusSelect.val(value);
                } else if (key === 'sla_status') {
                    $slaStatusInput.val(value);
                    $statusSelect.val('');
                } else {
                    $slaStatusInput.val('');
                    $statusSelect.val('');
                }

                if ($statusSelect.length) {
                    $statusSelect.trigger('change');
                    if ($statusSelect.hasClass('select2')) {
                        $statusSelect.trigger('change.select2');
                    }
                }

                $filterForm.trigger('submit');
            }

            $('.metric-card-trigger').on('click', function () {
                var key = $(this).data('filterKey') || 'all';
                var value = $(this).data('filterValue') || '';
                submitWithFilter(key, value);
            }).on('keydown', function (evt) {
                if (evt.key === 'Enter' || evt.key === ' ') {
                    evt.preventDefault();
                    $(this).trigger('click');
                }
            });

            $('#resetFilters').on('click', function () {
                if (!$filterForm.length) {
                    return;
                }

                $('#searchservice').val('');

                Object.keys(defaultFilters).forEach(function (name) {
                    var value = defaultFilters[name];
                    var $field = $filterForm.find('[name="' + name + '"]');
                    if (!$field.length) {
                        return;
                    }

                    if ($field.is('select')) {
                        $field.val(value);
                        $field.trigger('change');
                        if ($field.hasClass('select2')) {
                            $field.trigger('change.select2');
                        }
                    } else {
                        $field.val(value);
                    }
                });

                submitWithFilter('all', '');
            });

            var stateKey = 'DataTables_serviceTickets';
            var headers = window.serviceTicketExportHeaders || [];
            var rowsData = window.serviceTicketExportRows || [];
            var fieldOrder = ['ticket_no','type','status','sla_status','project','subject','job_owner','reporter','created_by','watchers','sla_target','service_category','category','sub_category','impact','priority','source','created_at','attachments'];

            var table = $('#serviceTickets').DataTable({
                "responsive": false,
                "lengthChange": true,
                "autoWidth": false,
                "scrollX": false,
                "scrollCollapse": false,
                "paging": true,
                "pageLength": 20,
                "lengthMenu": [
                    [10, 20, 30, 50, 100, -1],
                    [10, 20, 30, 50, 100, "ทั้งหมด"]
                ],
                "order": [[0, "desc"]],
                "columnDefs": [
                    { "targets": [1, 2], "orderable": false }
                ],
                "language": {
                    "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
                    "zeroRecords": "ไม่พบข้อมูลที่ต้องการ",
                    "info": "แสดงรายการที่ _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    "infoEmpty": "ไม่มีข้อมูลที่จะแสดง",
                    "infoFiltered": "(กรองจากข้อมูลทั้งหมด _MAX_ รายการ)",
                    "search": "ค้นหา:",
                    "paginate": {
                        "first": "หน้าแรก",
                        "last": "หน้าสุดท้าย",
                        "next": "ถัดไป",
                        "previous": "ก่อนหน้า"
                    },
                    "processing": "กำลังประมวลผล...",
                    "loadingRecords": "กำลังโหลดข้อมูล...",
                    "emptyTable": "ไม่มีข้อมูลในตาราง"
                },
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "stateSave": false,
                "stateDuration": 0,
                "stateSaveCallback": function (settings, data) {
                    localStorage.setItem(stateKey, JSON.stringify(data));
                },
                "stateLoadCallback": function (settings) {
                    var stored = localStorage.getItem(stateKey);
                    if (!stored) {
                        return null;
                    }
                    try {
                        return JSON.parse(stored);
                    } catch (err) {
                        localStorage.removeItem(stateKey);
                        return null;
                    }
                }
            });

            function getVisibleRows() {
                if (!rowsData.length) {
                    return [];
                }
                var indexes = table.rows({ search: 'applied', order: 'applied' }).indexes().toArray();
                if (!indexes.length) {
                    return rowsData;
                }
                return indexes.map(function (idx) {
                    return rowsData[idx] || null;
                }).filter(Boolean);
            }

            function buildMatrix() {
                var rows = getVisibleRows();
                return rows.map(function (row) {
                    return fieldOrder.map(function (field) {
                        var value = row[field];
                        return value === null || value === undefined ? '' : String(value);
                    });
                });
            }

            function showInfo(message) {
                var text = message || 'ไม่มีข้อมูลให้ส่งออก';
                if (typeof Swal !== 'undefined' && Swal.fire) {
                    Swal.fire({ icon: 'info', title: 'แจ้งเตือน', text: text, confirmButtonText: 'ตกลง' });
                } else {
                    alert(text);
                }
            }

            function ensureData(message) {
                if (!headers.length || !rowsData.length) {
                    showInfo(message);
                    return false;
                }
                return true;
            }

            function buildHtmlTable(matrix) {
                var html = '<table border="1" cellspacing="0" cellpadding="4"><thead><tr>' + headers.map(function (h) { return '<th>' + h + '</th>'; }).join('') + '</tr></thead><tbody>';
                matrix.forEach(function (row) {
                    html += '<tr>' + row.map(function (cell) { return '<td>' + cell + '</td>'; }).join('') + '</tr>';
                });
                html += '</tbody></table>';
                return html;
            }

            function downloadBlob(filename, blob) {
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                setTimeout(function () {
                    URL.revokeObjectURL(link.href);
                    document.body.removeChild(link);
                }, 0);
            }

            $('#btnExportCopy').on('click', function () {
                if (!ensureData('ไม่มีข้อมูลให้คัดลอก')) { return; }
                var matrix = buildMatrix();
                var lines = [headers.join('\\t')];
                matrix.forEach(function (row) { lines.push(row.join('\\t')); });
                var textToCopy = lines.join('\\n');
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(textToCopy).then(function () {
                        showInfo('คัดลอกข้อมูลแล้ว');
                    });
                } else {
                    var textarea = document.createElement('textarea');
                    textarea.value = textToCopy;
                    document.body.appendChild(textarea);
                    textarea.select();
                    try {
                        document.execCommand('copy');
                        showInfo('คัดลอกข้อมูลแล้ว');
                    } catch (err) {
                        showInfo('ไม่สามารถคัดลอกข้อมูลได้');
                    }
                    document.body.removeChild(textarea);
                }
            });

            $('#btnExportCSV').on('click', function () {
                if (!ensureData('ไม่มีข้อมูลสำหรับ CSV')) { return; }
                var matrix = buildMatrix();
                var csvLines = [headers.join(',')];
                matrix.forEach(function (row) {
                    csvLines.push(row.map(function (value) {
                        var needsQuotes = /[\",\\n]/.test(value);
                        var escaped = value.replace(/\"/g, '\"\"');
                        return needsQuotes ? '\"' + escaped + '\"' : escaped;
                    }).join(','));
                });
                downloadBlob('Service_Tickets.csv', new Blob(['\\ufeff' + csvLines.join('\\n')], { type: 'text/csv;charset=utf-8;' }));
            });

            $('#btnExportExcel').on('click', function () {
                if (!ensureData('ไม่มีข้อมูลสำหรับ Excel')) { return; }
                var matrix = buildMatrix();
                var html = buildHtmlTable(matrix);
                downloadBlob('Service_Tickets.xls', new Blob(['\\ufeff' + html], { type: 'application/vnd.ms-excel' }));
            });

            $('#btnExportPDF').on('click', function () {
                if (!ensureData('ไม่มีข้อมูลสำหรับ PDF')) { return; }
                var matrix = buildMatrix();
                if (window.pdfMake && window.pdfMake.createPdf) {
                    var body = [headers].concat(matrix);
                    var docDefinition = {
                        pageOrientation: 'landscape',
                        content: [{ table: { headerRows: 1, widths: headers.map(function () { return '*'; }), body: body } }],
                        defaultStyle: { fontSize: 8 }
                    };
                    window.pdfMake.createPdf(docDefinition).download('Service_Tickets.pdf');
                } else {
                    $('#btnExportPrint').trigger('click');
                }
            });

            $('#btnExportPrint').on('click', function () {
                if (!ensureData('ไม่มีข้อมูลสำหรับการพิมพ์')) { return; }
                var matrix = buildMatrix();
                var html = '<html><head><title>Service Tickets</title><meta charset=\"UTF-8\"><style>body{font-family:Arial,sans-serif;font-size:12px;color:#333;margin:20px;}table{border-collapse:collapse;width:100%;}th,td{border:1px solid #999;padding:6px;text-align:left;vertical-align:top;}th{background:#f0f0f0;}</style></head><body>' + buildHtmlTable(matrix) + '</body></html>';
                var w = window.open('', '');
                w.document.write(html);
                w.document.close();
                w.focus();
                setTimeout(function () { w.print(); }, 400);
            });

            $(window).on('resize', function () {
                table.columns.adjust();
            });
        });
    </script>
</body>

</html>
