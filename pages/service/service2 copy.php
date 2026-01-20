<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ใช้ข้อมูลจริง + รองรับตัวกรองจากแบบฟอร์ม
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

if (!function_exists('buildVisibilityFilter')) {
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
}

// รับค่าตัวกรอง (GET) และกำหนดค่าเริ่มต้น
$searchText       = $_GET['searchservice']   ?? '';
$filterType       = $_GET['categorytype']    ?? '';
$filterJobOwner   = array_key_exists('jobowner', $_GET) ? ($_GET['jobowner'] ?? '') : $user_id;
$filterSla        = $_GET['sla']             ?? '';
$filterPriority   = $_GET['priority']        ?? '';
$filterSource     = $_GET['source']          ?? '';
$filterUrgency    = $_GET['urgency']         ?? '';
$filterImpact     = $_GET['impact']          ?? '';
$filterStatus     = $_GET['status']          ?? '';
$filterServiceCat = $_GET['servicecategory'] ?? '';
$filterCategory   = $_GET['category']        ?? '';
$filterSubCat     = $_GET['subcategory']     ?? '';
$filterChannel    = $_GET['channel']         ?? '';

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

if (!function_exists('distinctOptions')) {
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
}

$categoryTypeOptions = distinctOptions($condb, 'ticket_type', $visibility['clause'], $visibility['params']);
$slaOptions          = distinctOptions($condb, 'sla_target', $visibility['clause'], $visibility['params']);
$priorityOptions     = distinctOptions($condb, 'priority', $visibility['clause'], $visibility['params']);
$sourceOptions       = distinctOptions($condb, 'source', $visibility['clause'], $visibility['params']);
$urgencyOptions      = distinctOptions($condb, 'urgency', $visibility['clause'], $visibility['params']);
$impactOptions       = distinctOptions($condb, 'impact', $visibility['clause'], $visibility['params']);
$statusOptions       = distinctOptions($condb, 'status', $visibility['clause'], $visibility['params']);
$serviceCatOptions   = distinctOptions($condb, 'service_category', $visibility['clause'], $visibility['params']);
$categoryOptions     = distinctOptions($condb, 'category', $visibility['clause'], $visibility['params']);
$subCategoryOptions  = distinctOptions($condb, 'sub_category', $visibility['clause'], $visibility['params']);
$channelOptions      = distinctOptions($condb, 'channel', $visibility['clause'], $visibility['params']);

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
    'channel'          => $filterChannel,
];
foreach ($mapFilters as $col => $val) {
    if ($val !== '' && $val !== null) { $where .= " AND st.$col = :$col"; $params[":$col"] = $val; }
}

// ค้นหาข้อความ
if ($searchText !== '') { $where .= " AND (st.ticket_no LIKE :q OR st.subject LIKE :q OR st.description LIKE :q)"; $params[':q'] = "%$searchText%"; }

// ดึง Tickets
$sqlTickets = "SELECT st.*,
        CONCAT(u.first_name, ' ', u.last_name) as job_owner_name,
        CONCAT(r.first_name, ' ', r.last_name) as reporter_name,
        p.project_name,
        (SELECT GROUP_CONCAT(CONCAT(wu.first_name, ' ', wu.last_name) ORDER BY wu.first_name SEPARATOR ', ')
            FROM service_ticket_watchers w
            JOIN users wu ON w.user_id = wu.user_id
            WHERE w.ticket_id = st.ticket_id) AS watcher_names
        FROM service_tickets st
        LEFT JOIN users u ON st.job_owner = u.user_id
        LEFT JOIN users r ON st.reporter = r.user_id
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
        'icon'  => 'fas fa-ticket-alt'
    ],
    [
        'title' => 'New',
        'description' => 'งานใหม่',
        'value' => $metricsData['status_new'] ?? 0,
        'color' => 'bg-primary',
        'icon'  => 'fas fa-plus-circle'
    ],
    [
        'title' => 'On Process',
        'description' => 'งานที่กำลังดำเนินการ',
        'value' => $metricsData['status_on_process'] ?? 0,
        'color' => 'bg-warning',
        'icon'  => 'fas fa-tasks'
    ],
    [
        'title' => 'Pending',
        'description' => 'งานที่รอดำเนินการ',
        'value' => $metricsData['status_pending'] ?? 0,
        'color' => 'bg-secondary',
        'icon'  => 'fas fa-hourglass-half'
    ],
    [
        'title' => 'Resolved',
        'description' => 'งานที่แก้ไขแล้ว',
        'value' => $metricsData['status_resolved'] ?? 0,
        'color' => 'bg-success',
        'icon'  => 'fas fa-check-circle'
    ],
    [
        'title' => 'Closed',
        'description' => 'งานที่ปิดแล้ว',
        'value' => $metricsData['status_closed'] ?? 0,
        'color' => 'bg-teal',
        'icon'  => 'fas fa-lock'
    ],
    [
        'title' => 'Cancelled',
        'description' => 'งานที่ยกเลิก',
        'value' => $metricsData['status_cancelled'] ?? 0,
        'color' => 'bg-danger',
        'icon'  => 'fas fa-times-circle'
    ],
    [
        'title' => 'Overdue SLA',
        'description' => 'งานที่เกิน SLA',
        'value' => $metricsData['sla_overdue'] ?? 0,
        'color' => 'bg-maroon',
        'icon'  => 'fas fa-exclamation-triangle'
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
    'Draft'                 => ['class' => 'badge badge-pill badge-status-default'],
    'New'                   => ['class' => 'badge badge-pill badge-status-assigned'],
    'On Process'            => ['class' => 'badge badge-pill badge-status-process'],
    'Pending'               => ['class' => 'badge badge-pill badge-status-pending'],
    'Waiting for Approval'  => ['class' => 'badge badge-pill badge-status-waiting'],
    'In Progress'           => ['class' => 'badge badge-pill badge-status-progress'],
    'Resolved'              => ['class' => 'badge badge-pill badge-status-resolved'],
    'Closed'                => ['class' => 'badge badge-pill badge-status-containment'],
    'Cancelled'             => ['class' => 'badge badge-pill badge-status-cab-review'],
    'Approved'              => ['class' => 'badge badge-pill badge-status-approved'],
    'Resolved Pending'      => ['class' => 'badge badge-pill badge-status-resolved-pending'],
    'Scheduled'             => ['class' => 'badge badge-pill badge-status-scheduled'],
    'Pending Approval'      => ['class' => 'badge badge-pill badge-status-pending-approval'],
    'CAB Review'            => ['class' => 'badge badge-pill badge-status-cab-review'],
    'Assigned'              => ['class' => 'badge badge-pill badge-status-assigned'],
    'Containment'           => ['class' => 'badge badge-pill badge-status-containment'],
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
$modernViewUrl = 'service.php' . ($modernViewQuery ? '?' . $modernViewQuery : '');
$classicViewUrl = $_SERVER['PHP_SELF'] . ($modernViewQuery ? '?' . $modernViewQuery : '');
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

            #serviceTickets {
                table-layout: auto;
                width: 100%;
            }

            #serviceTickets th,
            #serviceTickets td {
                vertical-align: middle;
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

            /* Status Badge Colors - ITIL/ITSM Standard */

            /* Draft - Gray (ฉบับร่าง) */
            .badge-status-default {
                background: rgba(108, 117, 125, 0.15);
                color: #495057;
                border: 1px solid rgba(108, 117, 125, 0.3);
            }

            /* New/Assigned - Blue (งานใหม่/มอบหมายแล้ว) */
            .badge-status-assigned {
                background: rgba(0, 123, 255, 0.15);
                color: #0056b3;
                border: 1px solid rgba(0, 123, 255, 0.3);
            }

            /* On Process/In Progress - Cyan (กำลังดำเนินการ) */
            .badge-status-process {
                background: rgba(23, 162, 184, 0.15);
                color: #117a8b;
                border: 1px solid rgba(23, 162, 184, 0.3);
            }

            .badge-status-progress {
                background: rgba(23, 162, 184, 0.15);
                color: #117a8b;
                border: 1px solid rgba(23, 162, 184, 0.3);
            }

            /* Pending/Waiting - Yellow (รอดำเนินการ) */
            .badge-status-pending {
                background: rgba(255, 193, 7, 0.15);
                color: #d39e00;
                border: 1px solid rgba(255, 193, 7, 0.3);
            }

            .badge-status-waiting {
                background: rgba(255, 193, 7, 0.15);
                color: #d39e00;
                border: 1px solid rgba(255, 193, 7, 0.3);
            }

            /* Resolved - Green (แก้ไขเรียบร้อย) ⭐ เปลี่ยนใหม่ */
            .badge-status-resolved {
                background: rgba(40, 167, 69, 0.15);
                color: #28a745;
                border: 1px solid rgba(40, 167, 69, 0.3);
                font-weight: 600;
            }

            /* Closed - Dark Green (ปิดงานแล้ว) */
            .badge-status-containment {
                background: rgba(32, 134, 68, 0.15);
                color: #1e7e34;
                border: 1px solid rgba(32, 134, 68, 0.3);
            }

            /* Cancelled/CAB Review - Red (ยกเลิก) */
            .badge-status-cab-review {
                background: rgba(220, 53, 69, 0.15);
                color: #bd2130;
                border: 1px solid rgba(220, 53, 69, 0.3);
            }

            /* Approved - Blue (อนุมัติแล้ว) */
            .badge-status-approved {
                background: rgba(0, 123, 255, 0.15);
                color: #0056b3;
                border: 1px solid rgba(0, 123, 255, 0.3);
            }

            /* Resolved Pending - Purple (รอยืนยันการแก้ไข) */
            .badge-status-resolved-pending {
                background: rgba(111, 66, 193, 0.15);
                color: #6f42c1;
                border: 1px solid rgba(111, 66, 193, 0.3);
            }

            /* Scheduled - Orange (กำหนดการแล้ว) */
            .badge-status-scheduled {
                background: rgba(255, 159, 67, 0.15);
                color: #e67e22;
                border: 1px solid rgba(255, 159, 67, 0.3);
            }

            /* Pending Approval - Pink (รออนุมัติ) */
            .badge-status-pending-approval {
                background: rgba(232, 62, 140, 0.15);
                color: #d63384;
                border: 1px solid rgba(232, 62, 140, 0.3);
            }

            .subject-cell {
                width: 520px;
                max-width: 520px;
                display: inline-block;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                line-height: 1.4;
            }

            #serviceTickets th.subject-col {
                width: 520px !important;
                max-width: 520px;
            }

            /* Project Column */
            .project-cell {
                max-width: 300px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .project-cell a,
            .subject-cell a {
                display: block;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                color: inherit;
                text-decoration: none;
            }

            .project-cell a:hover,
            .subject-cell a:hover {
                text-decoration: underline;
            }

            .watcher-cell {
                width: 260px;
                max-width: 260px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .watcher-cell .watcher-text {
                display: block;
                width: 100%;
                max-width: 100%;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
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
                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 d-flex metric-card-col">
                                        <div class="small-box <?php echo htmlspecialchars($metric['color']); ?> flex-fill shadow-sm">
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
                                                <form action="" method="GET">
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group ">
                                                                <input type="text" class="form-control " id="searchservice" name="searchservice" value="<?php echo htmlspecialchars($searchText); ?>" placeholder="ค้นหา...">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group ">
                                                                <button type="submit" class="btn btn-primary" id="search" name="search">ค้นหา</button>
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
                                                                <label>Channel</label>
                                                                <select class="custom-select select2" name="channel">
                                                                    <option value="">เลือก</option>
                                                                    <?php foreach ($channelOptions as $v): ?>
                                                                        <option value="<?php echo htmlspecialchars($v); ?>" <?php echo ($filterChannel === $v ? 'selected' : ''); ?>><?php echo htmlspecialchars($v); ?></option>
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
                                            <a href="<?php echo htmlspecialchars($modernViewUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-primary">
                                                <i class="fas fa-th-large mr-1"></i> มุมมองใหม่
                                            </a>
                                            <a href="<?php echo htmlspecialchars($classicViewUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                                                <i class="fas fa-table mr-1"></i> มุมมองตาราง
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="serviceTickets" class="table table-bordered table-striped table-hover">
                                        <thead class="bg-light">
                                            <tr class="text-center align-middle">
                                                <th class="text-nowrap table-header-tooltip" title="เลข Ticket">No.</th>
                                                <th class="text-nowrap table-header-tooltip" title="ประเภทงาน (Incident / Service / Change)">Type</th>
                                                <th class="text-nowrap table-header-tooltip" title="สถานะ SLA">SLA Status</th>
                                                <th class="text-nowrap table-header-tooltip" title="สถานะปัจจุบันของ Ticket">Status</th>
                                                <th class="text-nowrap table-header-tooltip" title="โครงการที่เกี่ยวข้อง">Project</th>
                                                <th class="text-nowrap table-header-tooltip subject-col" title="หัวข้อของ Ticket">Subject</th>
                                                <th class="text-nowrap table-header-tooltip" title="ผู้รับผิดชอบงาน">Job Owner</th>
                                                <th class="text-nowrap table-header-tooltip" title="ผู้แจ้งงาน">Reporter</th>
                                                <th class="text-nowrap table-header-tooltip" title="รายชื่อผู้ติดตาม Ticket">Watchers</th>
                                                <th class="text-nowrap table-header-tooltip" title="ระยะเวลาตาม SLA">SAL</th>
                                                <th class="text-nowrap table-header-tooltip" title="หมวดหมู่บริการหลัก">Service Category</th>
                                                <th class="text-nowrap table-header-tooltip" title="หมวดหมู่รอง">Category</th>
                                                <th class="text-nowrap table-header-tooltip" title="หมวดหมู่ย่อย">Sub-Category</th>
                                                <th class="text-nowrap table-header-tooltip" title="ผลกระทบของเหตุการณ์">Impact</th>
                                                <th class="text-nowrap table-header-tooltip" title="ระดับความสำคัญ">Priority</th>
                                                <th class="text-nowrap table-header-tooltip" title="ช่องทางที่สร้าง Ticket">Source</th>
                                                <th class="text-nowrap table-header-tooltip" title="วันที่สร้าง Ticket">Create Date</th>
                                                <th class="text-nowrap table-header-tooltip" title="จัดการ Ticket (แก้ไข/ลบ/Assign)">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($tickets)): ?>
                                                <tr>
                                                    <td colspan="18" class="text-center">ไม่พบข้อมูล Ticket</td>
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

                                                        // ตัดข้อความ Watchers ที่ยาวเกิน 150 ตัวอักษรให้แสดง ... และเก็บฉบับเต็มไว้เป็น tooltip
                                                        $watcherNames = $ticket['watcher_names'] ?? '-';
                                                        $watchersDisplay = $watcherNames;
                                                        $watchersFull = $watcherNames;
                                                        if (mb_strlen($watcherNames, 'UTF-8') > 150) {
                                                            $watchersDisplay = mb_substr($watcherNames, 0, 150, 'UTF-8') . '...';
                                                            $watchersFull = $watcherNames;
                                                        }

                                                    ?>
                                                    <tr>
                                                        <td class="text-nowrap text-center">
                                                            <a href="view_ticket.php?id=<?php echo urlencode($ticket['ticket_id']); ?>" class="text-primary">
                                                                <?php echo htmlspecialchars($ticket['ticket_no']); ?>
                                                            </a>
                                                        </td>



                                                        <td class="text-nowrap text-center align-middle"><span class="badge badge-pill px-3 py-2 <?php echo $typeConfig['class']; ?>"><?php echo htmlspecialchars($typeConfig['label']); ?></span></td>
                                                        <td class="text-nowrap text-center align-middle">
                                                            <span class="badge badge-pill px-3 py-2 <?php echo htmlspecialchars($slaBadgeClass, ENT_QUOTES, 'UTF-8'); ?>">
                                                                <?php echo htmlspecialchars($slaBadgeLabel, ENT_QUOTES, 'UTF-8'); ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-nowrap text-center align-middle"><span class="badge badge-pill px-3 py-2 <?php echo $statusConfig['class']; ?>"><?php echo htmlspecialchars($ticket['status']); ?></span></td>
                                                        <td class="project-cell" data-toggle="tooltip" data-placement="top" data-html="true" title="<?php echo htmlspecialchars($projectFull, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <a href="view_ticket.php?id=<?php echo urlencode($ticket['ticket_id']); ?>" class="text-reset">
                                                                <?php echo htmlspecialchars($projectDisplay); ?>
                                                            </a>
                                                        </td>
                                                        <td class="subject-cell" data-toggle="tooltip" data-placement="top" title="<?php echo $subjectFull; ?>">
                                                            <a href="view_ticket.php?id=<?php echo urlencode($ticket['ticket_id']); ?>" class="text-reset">
                                                                <?php echo $subjectDisplay; ?>
                                                            </a>
                                                        </td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($ticket['job_owner_name'] ?? '-'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($ticket['reporter_name'] ?? '-'); ?></td>
                                                        <td class="text-nowrap watcher-cell" data-toggle="tooltip" data-placement="top" title="<?php echo htmlspecialchars($watchersFull, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <span class="watcher-text"><?php echo htmlspecialchars($watchersDisplay, ENT_QUOTES, 'UTF-8'); ?></span>
                                                        </td>
                                                        <td class="text-nowrap text-center"><?php echo $slaDisplay; ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($ticket['service_category'] ?? '-'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($ticket['category'] ?? '-'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($ticket['sub_category'] ?? '-'); ?></td>
                                                        <td class="text-nowrap text-center"><?php echo htmlspecialchars($ticket['impact'] ?? '-'); ?></td>
                                                        <td class="text-nowrap text-center"><?php echo htmlspecialchars($ticket['priority'] ?? '-'); ?></td>
                                                        <td class="text-nowrap text-center"><?php echo htmlspecialchars($ticket['source'] ?? '-'); ?></td>
                                                        <td class="text-nowrap text-center"><?php echo date('Y-m-d H:i', strtotime($ticket['created_at'])); ?></td>
                                                        <td class="text-nowrap text-center">
                                                            <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                                                <a href="view_ticket.php?id=<?php echo urlencode($ticket['ticket_id']); ?>" class="btn btn-info" title="View"><i class="fas fa-eye"></i></a>
                                                                <?php
                                                                // สิทธิ์แก้ไข: Executive, Account Management, Sale Supervisor หรือ Job Owner
                                                                $canEdit = ($role === 'Executive' ||
                                                                           $role === 'Account Management' ||
                                                                           $role === 'Sale Supervisor' ||
                                                                           $ticket['job_owner'] === $user_id);
                                                                if ($canEdit):
                                                                ?>
                                                                <a href="edit_ticket.php?id=<?php echo urlencode($ticket['ticket_id']); ?>" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot class="bg-light">
                                            <tr class="text-center align-middle">
                                                <th>No.</th>
                                                <th>Type</th>
                                                <th>SLA Status</th>
                                                <th>Status</th>
                                                <th>Project</th>
                                                <th>Subject</th>
                                                <th>Job Owner</th>
                                                <th>Reporter</th>
                                                <th>Watchers</th>
                                                <th>SAL</th>
                                                <th>Service Category</th>
                                                <th>Category</th>
                                                <th>Sub-Category</th>
                                                <th>Impact</th>
                                                <th>Priority</th>
                                                <th>Source</th>
                                                <th>Create Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
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

            var stateKey = 'DataTables_serviceTickets';

            var table = $('#serviceTickets').DataTable({
                "responsive": false,
                "lengthChange": true,
                "autoWidth": false,
                "scrollX": true,
                "scrollCollapse": true,
                "paging": true,
                "pageLength": 20,
                "lengthMenu": [
                    [10, 20, 30, 50, 100, -1],
                    [10, 20, 30, 50, 100, "ทั้งหมด"]
                ],
                "order": [[0, "desc"]],
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "columnDefs": [
                    { "targets": 5, "width": 520 }
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
                "stateSave": false,  // ปิด state save เพื่อให้ reload ข้อมูลทุกครั้ง
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

            table.buttons().container().appendTo('#serviceTickets_wrapper .col-md-6:eq(0)');
            table.columns.adjust();
            $(window).on('resize', function () {
                table.columns.adjust();
            });
        });
    </script>
</body>

</html>
