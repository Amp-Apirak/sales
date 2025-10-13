<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ดึงข้อมูลจาก Session
$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';
$team_id = $_SESSION['team_id'] ?? '';

// รับ Ticket ID
$ticket_id = $_GET['id'] ?? null;

if (!$ticket_id) {
    header('Location: service.php');
    exit;
}

// ดึงข้อมูล Ticket
try {
    $stmt = $condb->prepare("SELECT st.*,
            CONCAT(u.first_name, ' ', u.last_name) as job_owner_name,
            CONCAT(r.first_name, ' ', r.last_name) as reporter_name,
            CONCAT(cu.first_name, ' ', cu.last_name) as created_by_name,
            p.project_name
            FROM service_tickets st
            LEFT JOIN users u ON st.job_owner = u.user_id
            LEFT JOIN users r ON st.reporter = r.user_id
            LEFT JOIN users cu ON st.created_by = cu.user_id
            LEFT JOIN projects p ON st.project_id = p.project_id
            WHERE st.ticket_id = :ticket_id");
    $stmt->execute([':ticket_id' => $ticket_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        header('Location: service.php?error=ticket_not_found');
        exit;
    }

    // ตรวจสอบสิทธิ์การเข้าถึง Ticket/การแก้ไข
    $hasAccess = false;
    $canEdit = false;

    // ตรวจสอบสถานะความเป็น Watcher/Reporter/Job Owner/Created by
    $isWatcher = false;
    $isReporter = ($ticket['reporter'] === $user_id);
    $isJobOwner = ($ticket['job_owner'] === $user_id);
    $isCreatedBy = ($ticket['created_by'] === $user_id); // ⭐ เพิ่มการเช็คผู้สร้าง

    // เช็ค Watcher
    $stmtWatcherCheck = $condb->prepare("SELECT COUNT(*) as count FROM service_ticket_watchers WHERE ticket_id = :ticket_id AND user_id = :user_id");
    $stmtWatcherCheck->execute([':ticket_id' => $ticket_id, ':user_id' => $user_id]);
    $watcherCheck = $stmtWatcherCheck->fetch(PDO::FETCH_ASSOC);
    if (!empty($watcherCheck['count']) && $watcherCheck['count'] > 0) {
        $isWatcher = true;
    }

    // ตรวจสอบว่าอยู่ทีมเดียวกับ Job Owner (สำหรับ Sale Supervisor/Account Management)
    $inSupervisorTeam = false;
    if ($role === 'Sale Supervisor' || $role === 'Account Management') {
        $stmtUserTeams = $condb->prepare("SELECT team_id FROM user_teams WHERE user_id = :user_id");
        $stmtUserTeams->execute([':user_id' => $user_id]);
        $userTeams = $stmtUserTeams->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($userTeams)) {
            $placeholders = implode(',', array_fill(0, count($userTeams), '?'));
            $stmtTeamCheck = $condb->prepare("SELECT COUNT(*) as count FROM user_teams WHERE user_id = ? AND team_id IN ($placeholders)");
            $params = array_merge([$ticket['job_owner']], $userTeams);
            $stmtTeamCheck->execute($params);
            $teamCheck = $stmtTeamCheck->fetch(PDO::FETCH_ASSOC);
            $inSupervisorTeam = ($teamCheck['count'] > 0);
        }
    }

    // กำหนดสิทธิ์เข้าดู (ตาม role.txt: Job Owner, Reporter, Watcher, Created by)
    if ($role === 'Executive') {
        // Executive เห็นทั้งหมด
        $hasAccess = true;
    } elseif ($role === 'Account Management' || $role === 'Sale Supervisor') {
        // Account Management/Sale Supervisor เห็น: Job Owner ในทีม, Reporter, Watcher, Created by
        if ($isJobOwner || $isReporter || $isWatcher || $isCreatedBy || $inSupervisorTeam) {
            $hasAccess = true;
        }
    } else {
        // Seller/Engineer เห็น: Job Owner (ตัวเอง), Reporter, Watcher, Created by
        if ($isJobOwner || $isReporter || $isWatcher || $isCreatedBy) {
            $hasAccess = true;
        }
    }

    // อนุญาตแก้ไขสำหรับ Job Owner, Executive, Account Management, Sale Supervisor
    if ($role === 'Executive' || $role === 'Account Management' || $role === 'Sale Supervisor' || $isJobOwner) {
        $canEdit = true;
    }

    // ถ้าไม่มีสิทธิ์เข้าถึงเลย ให้ redirect กลับ
    if (!$hasAccess) {
        header('Location: service.php?error=access_denied');
        exit;
    }

    // ดึง Timeline
    $stmtTimeline = $condb->prepare("SELECT * FROM service_ticket_timeline WHERE ticket_id = :ticket_id ORDER BY `order` ASC");
    $stmtTimeline->execute([':ticket_id' => $ticket_id]);
    $timeline = $stmtTimeline->fetchAll(PDO::FETCH_ASSOC);

    // ดึง Attachments
    $stmtAttach = $condb->prepare("SELECT * FROM service_ticket_attachments WHERE ticket_id = :ticket_id ORDER BY uploaded_at DESC");
    $stmtAttach->execute([':ticket_id' => $ticket_id]);
    $attachments = $stmtAttach->fetchAll(PDO::FETCH_ASSOC);

    // ดึง Watchers
    $stmtWatch = $condb->prepare("
        SELECT w.*, CONCAT(u.first_name, ' ', u.last_name) AS watcher_name, u.profile_image
        FROM service_ticket_watchers w
        INNER JOIN users u ON w.user_id = u.user_id
        WHERE w.ticket_id = :ticket_id
    ");
    $stmtWatch->execute([':ticket_id' => $ticket_id]);
    $watchers = $stmtWatch->fetchAll(PDO::FETCH_ASSOC);

    // ดึง Onsite Details
    $stmtOnsite = $condb->prepare("SELECT * FROM service_ticket_onsite WHERE ticket_id = :ticket_id");
    $stmtOnsite->execute([':ticket_id' => $ticket_id]);
    $onsite = $stmtOnsite->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

$menu = 'service';

// สร้าง CSRF Token สำหรับความปลอดภัย
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Badge colors
$statusColors = [
    'Draft' => 'secondary',
    'New' => 'primary',
    'On Process' => 'info',
    'Pending' => 'warning',
    'Waiting for Approval' => 'warning',
    'Scheduled' => 'info',
    'Resolved' => 'success',
    'Resolved Pending' => 'success',
    'Containment' => 'warning',
    'Closed' => 'dark',
    'Canceled' => 'danger'
];

$priorityColors = [
    'Critical' => 'danger',
    'High' => 'warning',
    'Medium' => 'info',
    'Low' => 'secondary'
];

$slaColors = [
    'Within SLA' => 'success',
    'Near SLA' => 'warning',
    'Overdue' => 'danger'
];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($ticket['ticket_no']); ?> - Service Ticket</title>
    <?php include '../../include/header.php'; ?>

    <style>
        /* Modern UI Styling */
        :root {
            --primary-color: #667eea;
            --success-color: #48bb78;
            --warning-color: #ed8936;
            --danger-color: #f56565;
            --info-color: #4299e1;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        body {
            background-color: var(--gray-50);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .content-wrapper {
            padding: 1.5rem;
        }

        /* Page Header */
        .page-header {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .ticket-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .ticket-subject {
            font-size: 1.1rem;
            color: var(--gray-700);
            margin-top: 0.5rem;
            font-weight: 500;
        }

        /* Cards */
        .modern-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header-modern {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        .card-header-modern h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-900);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-body-modern {
            padding: 1.5rem;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            gap: 1rem;
        }

        .info-item {
            display: grid;
            grid-template-columns: 180px 1fr;
            align-items: start;
            column-gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--gray-600);
            width: 180px;
            min-width: 180px;
            font-size: 0.875rem;
            white-space: nowrap;
        }

        .info-value {
            flex: 1;
            color: var(--gray-900);
            font-size: 0.875rem;
            text-align: left;
        }

        /* Wrap long text nicely in details, comments, and timeline */
        .info-value,
        .activity-feed .activity-item,
        .activity-feed .activity-content,
        .activity-feed .text-truncate,
        .timeline-detail {
            white-space: pre-wrap;         /* respect line breaks; allow wrapping */
            word-break: break-word;        /* break long unbroken strings */
            overflow-wrap: anywhere;       /* as a fallback for various browsers */
        }

        /* Ensure description/value areas stay inside the card */
        .info-item .info-value {
            line-height: 1.6;
        }


        /* Badges */
        .modern-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .badge-primary { background: #dbeafe; color: #1e40af; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-secondary { background: var(--gray-200); color: var(--gray-700); }
        .badge-dark { background: var(--gray-800); color: white; }

        /* Action Buttons */
        .action-btn {
            width: 100%;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .btn-primary-modern {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary-modern:hover {
            background: #5568d3;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-success-modern {
            background: var(--success-color);
            color: white;
        }

        .btn-success-modern:hover {
            background: #38a169;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(72, 187, 120, 0.4);
        }

        .btn-dark-modern {
            background: var(--gray-800);
            color: white;
        }

        .btn-dark-modern:hover {
            background: var(--gray-700);
            transform: translateY(-1px);
        }

        .btn-secondary-modern {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .btn-secondary-modern:hover {
            background: var(--gray-300);
        }

        /* Watchers */
        .watcher-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--gray-50);
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .watcher-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        /* Attachments */
        .attachment-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            background: var(--gray-50);
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }

        .attachment-item:hover {
            background: var(--gray-100);
        }

        .attachment-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .file-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: var(--info-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Activity Feed */
        .activity-feed {
            max-height: 600px;
            overflow-y: auto;
            padding: 1.5rem;
            background: var(--gray-50);
            border-radius: 8px;
        }

        .activity-item {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .activity-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .activity-item.system-log {
            background: linear-gradient(135deg, #e0f2fe 0%, #bfdbfe 100%);
            border-left: 4px solid var(--info-color);
        }

        .activity-item:last-child {
            margin-bottom: 0;
        }

        .activity-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .user-avatar-small {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
        }

        .activity-meta {
            flex: 1;
        }

        .activity-user {
            font-weight: 600;
            color: var(--gray-900);
            font-size: 0.875rem;
        }

        .activity-time {
            color: var(--gray-500);
            font-size: 0.75rem;
        }

        .activity-content {
            color: var(--gray-700);
            font-size: 0.875rem;
            line-height: 1.5;
        }

        /* Comment Input */
        .comment-input-area {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .comment-textarea {
            width: 100%;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 0.875rem;
            min-height: 100px;
            resize: vertical;
            font-family: inherit;
        }

        .comment-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .comment-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.75rem;
        }

        .btn-attach {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--gray-100);
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-700);
            transition: all 0.2s;
        }

        .btn-attach:hover {
            background: var(--gray-200);
        }

        .btn-post-comment {
            padding: 0.5rem 1.5rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-post-comment:hover {
            background: #5568d3;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 40px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 18px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--gray-200);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .timeline-badge {
            position: absolute;
            left: -40px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            z-index: 2;
            box-shadow: 0 0 0 4px white;
        }

        .timeline-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .timeline-actor {
            font-weight: 600;
            color: var(--gray-900);
            font-size: 0.875rem;
        }

        .timeline-time {
            color: var(--gray-500);
            font-size: 0.75rem;
        }

        .timeline-action {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }

        .timeline-detail {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .timeline-location {
            margin-top: 0.5rem;
            color: var(--info-color);
            font-size: 0.75rem;
        }

        /* Onsite Badge */
        .onsite-badge {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        /* File Preview */
        .file-preview-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 0.75rem 0;
        }

        .file-preview-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: var(--gray-100);
            border-radius: 6px;
            font-size: 0.75rem;
            color: var(--gray-700);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .info-item {
                grid-template-columns: 1fr;
            }

            .info-label {
                min-width: auto;
                margin-bottom: 0.25rem;
            }
        }

        /* Scrollbar */
        .activity-feed::-webkit-scrollbar {
            width: 8px;
        }

        .activity-feed::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 4px;
        }

        .activity-feed::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 4px;
        }

        .activity-feed::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }

        /* Breadcrumb */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: var(--gray-400);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            opacity: 0.5;
        }

        /* SweetAlert Image Modal Styling */
        .swal-image-full {
            max-height: 80vh;
            object-fit: contain;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="ticket-number">
                            <?php echo htmlspecialchars($ticket['ticket_no']); ?>
                            <span class="modern-badge badge-<?php echo $statusColors[$ticket['status']] ?? 'secondary'; ?>">
                                <?php echo htmlspecialchars($ticket['status']); ?>
                            </span>
                        </h1>
                        <div class="ticket-subject">
                            <?php echo htmlspecialchars($ticket['subject']); ?>
                        </div>
                        <nav aria-label="breadcrumb" class="mt-2">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="service.php">Service Tickets</a></li>
                                <li class="breadcrumb-item active"><?php echo htmlspecialchars($ticket['ticket_no']); ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- Ticket Information -->
                        <div class="modern-card">
                            <div class="card-header-modern">
                                <h3><i class="fas fa-info-circle"></i> ข้อมูล Ticket</h3>
                            </div>
                            <div class="card-body-modern">
                                <div class="info-grid">
                                    <!-- Classification -->
                                    <div class="info-item">
                                        <div class="info-label">โครงการ:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($ticket['project_name'] ?? '-'); ?></div>
                                    </div>

                                                                        <!-- Description at the end for clean layout -->
                                    <div class="info-item">
                                        <div class="info-label">รายละเอียด:</div>
                                        <div class="info-value"><?php echo nl2br(htmlspecialchars($ticket['description'] ?? '-')); ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">ประเภท:</div>
                                        <div class="">
                                            <span class="modern-badge badge-info"><?php echo htmlspecialchars($ticket['ticket_type']); ?></span>
                                        </div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">Priority:</div>
                                        <div class="">
                                            <span class="modern-badge badge-<?php echo $priorityColors[$ticket['priority']] ?? 'secondary'; ?>"><?php echo htmlspecialchars($ticket['priority']); ?></span>
                                        </div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">Urgency:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($ticket['urgency'] ?? '-'); ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">Impact:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($ticket['impact'] ?? '-'); ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">แหล่งที่มา:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($ticket['source'] ?? '-'); ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">ช่องทาง:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($ticket['channel'] ?? '-'); ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">หมวดหมู่:</div>
                                        <div class="">
                                            <?php echo htmlspecialchars($ticket['service_category'] ?? '-'); ?> /
                                            <?php echo htmlspecialchars($ticket['category'] ?? '-'); ?> /
                                            <?php echo htmlspecialchars($ticket['sub_category'] ?? '-'); ?>
                                        </div>
                                    </div>

                                    <!-- Ownership -->
                                    <div class="info-item">
                                        <div class="info-label">Job Owner:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($ticket['job_owner_name'] ?? '-'); ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">ผู้แจ้ง:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($ticket['reporter_name'] ?? '-'); ?></div>
                                    </div>

                                    <!-- Schedule & SLA -->
                                    <div class="info-item">
                                        <div class="info-label">เริ่มดำเนินการ:</div>
                                        <div class="info-value"><?php echo !empty($ticket['start_at']) ? date('d/m/Y H:i', strtotime($ticket['start_at'])) : '-'; ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">กำหนดแล้วเสร็จ:</div>
                                        <div class="info-value"><?php echo !empty($ticket['due_at']) ? date('d/m/Y H:i', strtotime($ticket['due_at'])) : '-'; ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">SLA Target:</div>
                                        <div class="">
                                            <?php echo htmlspecialchars($ticket['sla_target'] ?? '-'); ?> ชั่วโมง
                                            <?php if ($ticket['sla_deadline']): ?>
                                                <br><small style="color: var(--gray-500);">Deadline: <?php echo date('d/m/Y H:i', strtotime($ticket['sla_deadline'])); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">SLA Status:</div>
                                        <div class="">
                                            <span class="modern-badge badge-<?php echo $slaColors[$ticket['sla_status']] ?? 'secondary'; ?>"><?php echo htmlspecialchars($ticket['sla_status'] ?? '-'); ?></span>
                                        </div>
                                    </div>

                                    <!-- Timestamps -->
                                    <div class="info-item">
                                        <div class="info-label">สร้างเมื่อ:</div>
                                        <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($ticket['created_at'])); ?> <small style="color: var(--gray-500);">โดย <?php echo htmlspecialchars($ticket['created_by_name'] ?? '-'); ?></small></div>
                                    </div>

                                    <?php if ($ticket['updated_at']): ?>
                                    <div class="info-item">
                                        <div class="info-label">อัปเดตล่าสุด:</div>
                                        <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($ticket['updated_at'])); ?></div>
                                    </div>
                                    <?php endif; ?>


                                </div>
                            </div>
                        </div>

                        <!-- Onsite Details -->
                        <?php if ($onsite): ?>
                        <div class="modern-card">
                            <div class="card-header-modern">
                                <h3><i class="fas fa-map-marker-alt"></i> รายละเอียดการเดินทาง Onsite</h3>
                            </div>
                            <div class="card-body-modern">
                                <div class="onsite-badge">
                                    <i class="fas fa-route"></i>
                                    การให้บริการแบบ Onsite
                                </div>

                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">จุดเริ่มต้น:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($onsite['start_location'] ?? '-'); ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">จุดหมายปลายทาง:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($onsite['end_location'] ?? '-'); ?></div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">ยานพาหนะ:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($onsite['travel_mode'] ?? '-'); ?></div>
                                    </div>

                                    <?php if ($onsite['odometer_start'] || $onsite['odometer_end']): ?>
                                    <div class="info-item">
                                        <div class="info-label">เลขไมล์:</div>
                                        <div class="info-value">
                                            <?php echo number_format($onsite['odometer_start'], 1); ?> → <?php echo number_format($onsite['odometer_end'], 1); ?>
                                            <strong>(ระยะทาง: <?php echo number_format($onsite['distance'], 1); ?> km)</strong>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($onsite['note']): ?>
                                    <div class="info-item">
                                        <div class="info-label">หมายเหตุ:</div>
                                        <div class="info-value"><?php echo nl2br(htmlspecialchars($onsite['note'])); ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <!-- Actions -->
                        <div class="modern-card">
                            <div class="card-header-modern">
                                <h3><i class="fas fa-cogs"></i> Actions</h3>
                            </div>
                            <div class="card-body-modern">
                                <?php if ($canEdit): ?>
                                    <a href="edit_ticket.php?id=<?php echo urlencode($ticket_id); ?>" class="action-btn btn-primary-modern">
                                        <i class="fas fa-edit"></i> แก้ไข Ticket
                                    </a>
                                    <button class="action-btn btn-success-modern" onclick="updateStatus('Resolved')">
                                        <i class="fas fa-check"></i> Resolve Ticket
                                    </button>
                                    <button class="action-btn btn-dark-modern" onclick="updateStatus('Closed')">
                                        <i class="fas fa-lock"></i> Close Ticket
                                    </button>
                                <?php else: ?>
                                    <div class="alert alert-info" style="margin: 0;">
                                        <i class="fas fa-info-circle"></i> คุณเป็น Watcher - สามารถดูและคอมเมนต์ได้อย่างเดียว
                                    </div>
                                <?php endif; ?>
                                <a href="service.php" class="action-btn btn-secondary-modern">
                                    <i class="fas fa-arrow-left"></i> กลับรายการ
                                </a>
                            </div>
                        </div>

                        <!-- Watchers -->
                        <div class="modern-card">
                            <div class="card-header-modern">
                                <h3><i class="fas fa-users"></i> Watchers (<?php echo count($watchers); ?>)</h3>
                            </div>
                            <div class="card-body-modern">
                                <?php if (!empty($watchers)): ?>
                                    <?php foreach ($watchers as $watcher): ?>
                                    <div class="watcher-item">
                                        <div class="watcher-avatar">
                                            <?php echo strtoupper(substr($watcher['watcher_name'], 0, 2)); ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--gray-900); font-size: 0.875rem;">
                                                <?php echo htmlspecialchars($watcher['watcher_name']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <p>ไม่มี Watchers</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="modern-card">
                            <div class="card-header-modern">
                                <h3><i class="fas fa-paperclip"></i> ไฟล์แนบ (<?php echo count($attachments); ?>)</h3>
                            </div>
                            <div class="card-body-modern">
                                <?php if (!empty($attachments)): ?>
                                    <?php foreach ($attachments as $file): ?>
                                    <div class="attachment-item">
                                        <div class="attachment-info">
                                            <div class="file-icon">
                                                <i class="fas fa-file"></i>
                                            </div>
                                            <div style="font-size: 0.875rem; color: var(--gray-700);">
                                                <?php echo htmlspecialchars($file['file_name']); ?>
                                            </div>
                                        </div>
                                        <a href="<?php echo htmlspecialchars($file['file_path']); ?>"
                                           class="btn btn-sm"
                                           style="background: var(--primary-color); color: white; border-radius: 6px; padding: 0.375rem 0.75rem;"
                                           download>
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-paperclip"></i>
                                        <p>ไม่มีไฟล์แนบ</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Log & Comments and Timeline Row -->
                <div class="row">
                    <!-- Left Column: Activity Log & Comments -->
                    <div class="col-lg-8">
                        <div class="modern-card">
                            <div class="card-header-modern">
                                <h3>
                                    <i class="fas fa-comments"></i> Activity Log & Comments
                                    <span class="modern-badge badge-info" id="total-comments" style="margin-left: auto;">-</span>
                                </h3>
                            </div>
                            <div class="card-body-modern">
                                <div class="activity-feed" id="ticket-feed">
                                    <div style="text-align: center; padding: 2rem;">
                                        <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--gray-400);"></i>
                                    </div>
                                </div>

                                <div class="comment-input-area">
                                    <form id="ticketCommentForm" method="post" action="api/post_comment.php" enctype="multipart/form-data">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($ticket_id); ?>">

                                        <textarea class="comment-textarea" id="comment_text" name="comment_text" placeholder="เขียนความคิดเห็นหรืออัปเดตงาน..." required></textarea>

                                        <div id="file-preview-list" class="file-preview-list"></div>

                                        <div class="comment-actions">
                                            <label class="btn-attach mb-0">
                                                <i class="fas fa-paperclip"></i> แนบไฟล์
                                                <input type="file" id="ticket_files" name="attachments[]" multiple style="display:none" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.txt">
                                            </label>
                                            <button type="submit" class="btn-post-comment">
                                                <i class="fas fa-paper-plane"></i>โพสต์ความคิดเห็น
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Timeline -->
                    <div class="col-lg-4">
                        <div class="modern-card">
                            <div class="card-header-modern">
                                <h3><i class="fas fa-history"></i> Timeline</h3>
                            </div>
                            <div class="card-body-modern">
                                <?php if (!empty($timeline)): ?>
                                <div class="timeline">
                                    <?php foreach ($timeline as $item): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-badge"><?php echo $item['order']; ?></div>
                                        <div class="timeline-card">
                                            <div class="timeline-header">
                                                <span class="timeline-actor"><?php echo htmlspecialchars($item['actor']); ?></span>
                                                <span class="timeline-time"><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></span>
                                            </div>
                                            <div class="timeline-action"><?php echo htmlspecialchars($item['action']); ?></div>
                                            <?php if ($item['detail']): ?>
                                            <div class="timeline-detail"><?php echo htmlspecialchars($item['detail']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($item['location']): ?>
                                            <div class="timeline-location">
                                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($item['location']); ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-history"></i>
                                    <p>ยังไม่มี Timeline</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include '../../include/footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function updateStatus(newStatus) {
            Swal.fire({
                title: 'ยืนยันการเปลี่ยนสถานะ?',
                text: 'คุณต้องการเปลี่ยนสถานะเป็น ' + newStatus + ' ใช่หรือไม่?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ใช่, เปลี่ยนเลย',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
                    formData.append('ticket_id', '<?php echo $ticket_id; ?>');
                    formData.append('status', newStatus);

                    fetch('api/update_ticket.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: 'อัพเดตสถานะเรียบร้อยแล้ว',
                                confirmButtonColor: '#48bb78'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: data.message,
                                confirmButtonColor: '#f56565'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: error.message,
                            confirmButtonColor: '#f56565'
                        });
                    });
                }
            });
        }

        const ticketId = '<?php echo $ticket_id; ?>';
        const csrfToken = '<?php echo $_SESSION['csrf_token']; ?>';

        $(document).ready(function(){
            loadTicketFeed();
            setInterval(loadTicketFeed, 30000);
        });

        function loadTicketFeed(){
            $.ajax({
                url: 'api/get_ticket_feed.php',
                type: 'GET',
                data: { ticket_id: ticketId },
                success: function(html){
                    $('#ticket-feed').html(html);
                    const feed = document.getElementById('ticket-feed');
                    if (feed) feed.scrollTop = feed.scrollHeight;
                    const count = $('#ticket-feed .activity-item').not('.system-log').length;
                    $('#total-comments').text(count);
                },
                error: function(){
                    $('#ticket-feed').html('<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>ไม่สามารถโหลดฟีดได้</p></div>');
                }
            });
        }

        $('#ticket_files').on('change', function(){
            const list = $('#file-preview-list');
            list.empty();
            const files = this.files;
            for(let i=0;i<files.length;i++){
                const f = files[i];
                const mb = (f.size/1024/1024).toFixed(2);
                if (f.size > 10*1024*1024){
                    alert(`ไฟล์ "${f.name}" มีขนาดใหญ่เกิน 10 MB`);
                    continue;
                }
                list.append(`<div class="file-preview-item"><i class="fas fa-file"></i><span>${f.name} (${mb} MB)</span></div>`);
            }
        });

        $('#ticketCommentForm').on('submit', function(e){
            e.preventDefault();
            const fd = new FormData(this);
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>กำลังโพสต์...');

            $.ajax({
                url: 'api/post_comment.php',
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res){
                    if (res && res.status === 'success'){
                        $('#ticketCommentForm')[0].reset();
                        $('#file-preview-list').empty();
                        loadTicketFeed();
                        Swal.fire({
                            icon:'success',
                            title:'สำเร็จ!',
                            text:'โพสต์ความคิดเห็นแล้ว',
                            timer:1500,
                            showConfirmButton:false
                        });
                    } else {
                        Swal.fire({
                            icon:'error',
                            title:'เกิดข้อผิดพลาด',
                            text: (res && res.message) || 'ไม่สามารถโพสต์ได้',
                            confirmButtonColor: '#f56565'
                        });
                    }
                },
                error: function(){
                    Swal.fire({
                        icon:'error',
                        title:'เกิดข้อผิดพลาด',
                        text:'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้',
                        confirmButtonColor: '#f56565'
                    });
                },
                complete: function(){
                    btn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i>โพสต์ความคิดเห็น');
                }
            });
        });

        // Image modal for click-to-expand functionality
        function openImageModal(imagePath, imageName) {
            Swal.fire({
                imageUrl: imagePath,
                imageAlt: imageName,
                title: imageName,
                width: '80%',
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    image: 'swal-image-full'
                },
                html: '<a href="' + imagePath + '" download="' + imageName + '" class="btn btn-primary mt-3"><i class="fas fa-download"></i> ดาวน์โหลด</a>'
            });
        }

        // Delete comment functionality
        function deleteComment(commentId) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: 'คุณต้องการลบความคิดเห็นนี้ใช่หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('csrf_token', csrfToken);
                    formData.append('comment_id', commentId);

                    fetch('api/delete_comment.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: 'ลบความคิดเห็นเรียบร้อยแล้ว',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadTicketFeed(); // Reload feed to show updated comments
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: data.message,
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้',
                            confirmButtonColor: '#ef4444'
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>
