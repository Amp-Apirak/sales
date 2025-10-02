<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูลให้เหมือนหน้าอื่น ๆ
include '../../include/Add_session.php';

// โหลด helper ด้านความปลอดภัยหากยังไม่ได้โหลด
if (!function_exists('escapeOutput')) {
    require_once __DIR__ . '/../../config/validation.php';
}

// ตรวจสอบ/สร้าง CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// รับ Ticket ID จาก URL
$ticket_id = $_GET['id'] ?? null;

if (!$ticket_id) {
    header('Location: service.php?error=missing_ticket_id');
    exit;
}

// ดึงข้อมูล Ticket ที่ต้องการแก้ไข
$ticket = null;
$onsiteData = null;
$selectedWatchers = [];

try {
    $stmt = $condb->prepare("SELECT st.*
            FROM service_tickets st
            WHERE st.ticket_id = :ticket_id");
    $stmt->execute([':ticket_id' => $ticket_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        header('Location: service.php?error=ticket_not_found');
        exit;
    }

    // ดึง Onsite Details (ถ้ามี)
    $stmtOnsite = $condb->prepare("SELECT * FROM service_ticket_onsite WHERE ticket_id = :ticket_id");
    $stmtOnsite->execute([':ticket_id' => $ticket_id]);
    $onsiteData = $stmtOnsite->fetch(PDO::FETCH_ASSOC);

    // ดึง Watchers
    $stmtWatchers = $condb->prepare("SELECT user_id FROM service_ticket_watchers WHERE ticket_id = :ticket_id");
    $stmtWatchers->execute([':ticket_id' => $ticket_id]);
    $selectedWatchers = $stmtWatchers->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    die('Database Error: ' . $e->getMessage());
}

// ดึงข้อมูลที่จำเป็นจากฐานข้อมูลเพื่อใช้ใน dropdown
$owners = [];
$supportTeams = [];
$serviceCategories = [];
$categoriesList = [];
$subCategoriesList = [];
$projects = [];
$reporters = [];

$currentUserId = $_SESSION['user_id'] ?? null;
$currentTeamId = $_SESSION['team_id'] ?? null;
$currentUserName = trim(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')) ?: 'ผู้ใช้ระบบ';
$currentDateTime = date('Y-m-d\TH:i');

try {
    // รายชื่อเจ้าของงาน (ผู้ใช้งานระบบทั้งหมด)
    $stmt = $condb->query("SELECT user_id, CONCAT(first_name, ' ', last_name) AS full_name, role FROM users ORDER BY first_name ASC");
    $owners = $stmt->fetchAll();

    // หมวดหมู่บริการ (service_category)
    $stmtCategory = $condb->query("SELECT DISTINCT service_category FROM category ORDER BY service_category");
    $serviceCategories = $stmtCategory->fetchAll();

    // ทีมที่ให้บริการ
    $stmtTeam = $condb->query("SELECT team_id, team_name FROM teams ORDER BY team_name ASC");
    $supportTeams = $stmtTeam->fetchAll();

    // หมวดหมู่หลัก / ย่อย
    $stmtCatList = $condb->query("SELECT DISTINCT category FROM category WHERE category IS NOT NULL AND category <> '' ORDER BY category");
    $categoriesList = $stmtCatList->fetchAll();

    $stmtSubCatList = $condb->query("SELECT DISTINCT sub_category FROM category WHERE sub_category IS NOT NULL AND sub_category <> '' ORDER BY sub_category");
    $subCategoriesList = $stmtSubCatList->fetchAll();

    // รายชื่อโครงการที่เปิดอยู่
    $stmtProjects = $condb->query("SELECT project_id, project_name FROM projects ORDER BY project_name ASC");
    $projects = $stmtProjects->fetchAll();

    // ผู้แจ้ง (ใช้รายชื่อผู้ใช้งานเดียวกันกับ Job Owner)
    $reporters = $owners;
} catch (PDOException $e) {
    // หากดึงข้อมูลไม่ได้ให้ยังคงแสดงหน้าโดยใช้ array ว่าง ๆ
}

// กำหนดค่าคงที่สำหรับ dropdown
$ticketTypes = [
    ['id' => 'Incident', 'label' => 'Incident'],
    ['id' => 'Service', 'label' => 'Service Request'],
    ['id' => 'Change', 'label' => 'Change'],
];

$statusOptions = [
    'Draft',
    'New',
    'On Process',
    'Pending',
    'Waiting for Approval',
    'Scheduled',
    'Resolved',
    'Resolved Pending',
    'Containment',
    'Closed',
    'Cancelled'
];

$sourceOptions = ['Email', 'Call Center', 'Portal', 'Self-Service', 'Monitoring', 'Planner', 'Security Alert', 'Product Owner'];
$priorityOptions = ['Critical', 'High', 'Medium', 'Low'];
$urgencyOptions = ['High', 'Medium', 'Low'];
$impactOptions = ['Organization', 'Multiple Sites', 'Site', 'Department', 'Application', 'Executive', 'Remote Users', 'Single User', 'External'];
$channelOptions = ['Onsite', 'Remote', 'Office'];

$menu = 'service';
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Desk | Edit Ticket</title>
    <?php include '../../include/header.php'; ?>

    <style>
        /* Modern CSS for Service Ticket Form */
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .content-wrapper {
            background: transparent;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #007bff, #6610f2);
            border: none;
            border-radius: 1rem 1rem 0 0 !important;
            color: white;
            padding: 1.25rem 1.5rem;
        }

        .card-title {
            font-weight: 600;
            margin: 0;
            font-size: 1.1rem;
        }

        .card-body {
            padding: 2rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
            background: white;
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .select2-container--default .select2-selection--single {
            height: calc(2.25rem + 4px);
            border: 2px solid #e9ecef;
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.8);
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
        }

        .btn {
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #6610f2);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
            color: #212529;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4);
            color: #212529;
        }

        .btn-outline-secondary {
            border: 2px solid #6c757d;
            color: #6c757d;
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            transform: translateY(-2px);
        }

        .dropzone-area {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.05), rgba(102, 16, 242, 0.05));
            border: 2px dashed #007bff !important;
            border-radius: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .dropzone-area:hover {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(102, 16, 242, 0.1));
            border-color: #6610f2 !important;
            transform: translateY(-2px);
        }

        .text-danger {
            color: #dc3545 !important;
            font-weight: 600;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .gap-3 {
            gap: 1rem;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }

            .btn-sm {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
        }

        /* Custom animation for form */
        .card {
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Select2 enhancements */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 0.75rem;
            padding-top: 0.5rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(2.25rem + 4px);
            right: 0.75rem;
        }

        /* Form layout improvements */
        .form-group {
            margin-bottom: 1.5rem;
        }

        /* Card outline variations */
        .card-outline-info .card-header {
            background: linear-gradient(135deg, #17a2b8, #6f42c1);
        }

        .card-outline-warning .card-header {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: #212529;
        }

        .card-outline-success .card-header {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        /* Additional spacing and layout adjustments */
        .container-fluid {
            padding: 2rem;
        }

        @media (max-width: 991.98px) {
            .container-fluid {
                padding: 1rem;
            }
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <form id="editTicketForm" method="POST" action="#" novalidate>
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="ticket_id" value="<?php echo escapeOutput($ticket_id); ?>">

                                <!-- Header Card -->
                                <div class="card card-primary mb-4">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-edit mr-2"></i>แก้ไข Service Ticket: <?php echo escapeOutput($ticket['ticket_no']); ?></h3>
                                    </div>
                                    <div class="card-body">
                                        <!-- Auto-filled Info Fields -->
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>วันเวลาที่สร้าง</label>
                                                    <input type="datetime-local" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($ticket['created_at'])); ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Ticket No.</label>
                                                    <input type="text" class="form-control" value="<?php echo escapeOutput($ticket['ticket_no']); ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Project<span class="text-danger">*</span></label>
                                                    <select name="project_id" id="project_id" class="form-control select2" required>
                                                        <option value="">เลือก</option>
                                                        <?php foreach ($projects as $project): ?>
                                                            <option value="<?php echo escapeOutput($project['project_id']); ?>" <?php echo $ticket['project_id'] === $project['project_id'] ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($project['project_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- First Row of Main Fields -->
                                        <div class="row">
                                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Ticket Type<span class="text-danger">*</span></label>
                                                    <select name="ticket_type" id="ticket_type" class="form-control select2" required>
                                                        <option value="">เลือก</option>
                                                        <?php foreach ($ticketTypes as $type): ?>
                                                            <option value="<?php echo escapeOutput($type['id']); ?>" <?php echo $ticket['ticket_type'] === $type['id'] ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($type['label']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Job Owner<span class="text-danger">*</span></label>
                                                    <select name="job_owner" id="job_owner" class="form-control select2" required>
                                                        <option value="">เลือก</option>
                                                        <?php foreach ($owners as $owner): ?>
                                                            <option value="<?php echo escapeOutput($owner['user_id']); ?>" <?php echo $ticket['job_owner'] == $owner['user_id'] ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($owner['full_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>SLA Target (ชั่วโมง)</label>
                                                    <select name="sla_target" id="sla_target" class="form-control select2">
                                                        <option value="">เลือก</option>
                                                        <option value="1" <?php echo $ticket['sla_target'] == 1 ? 'selected' : ''; ?>>1 ชั่วโมง</option>
                                                        <option value="2" <?php echo $ticket['sla_target'] == 2 ? 'selected' : ''; ?>>2 ชั่วโมง</option>
                                                        <option value="4" <?php echo $ticket['sla_target'] == 4 ? 'selected' : ''; ?>>4 ชั่วโมง</option>
                                                        <option value="8" <?php echo $ticket['sla_target'] == 8 ? 'selected' : ''; ?>>8 ชั่วโมง</option>
                                                        <option value="24" <?php echo $ticket['sla_target'] == 24 ? 'selected' : ''; ?>>24 ชั่วโมง</option>
                                                        <option value="48" <?php echo $ticket['sla_target'] == 48 ? 'selected' : ''; ?>>48 ชั่วโมง</option>
                                                        <option value="72" <?php echo $ticket['sla_target'] == 72 ? 'selected' : ''; ?>>72 ชั่วโมง</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Priority<span class="text-danger">*</span></label>
                                                    <select name="priority" id="priority" class="form-control select2" required>
                                                        <option value="">เลือก</option>
                                                        <?php foreach ($priorityOptions as $priority): ?>
                                                            <option value="<?php echo escapeOutput($priority); ?>" <?php echo $ticket['priority'] === $priority ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($priority); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Channel</label>
                                                    <select name="channel" id="channel" class="form-control select2">
                                                        <option value="">เลือก</option>
                                                        <?php foreach ($channelOptions as $channel): ?>
                                                            <option value="<?php echo escapeOutput($channel); ?>" <?php echo $ticket['channel'] === $channel ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($channel); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Urgency<span class="text-danger">*</span></label>
                                                    <select name="urgency" id="urgency" class="form-control select2" required>
                                                        <option value="">เลือก</option>
                                                        <?php foreach ($urgencyOptions as $urgency): ?>
                                                            <option value="<?php echo escapeOutput($urgency); ?>" <?php echo $ticket['urgency'] === $urgency ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($urgency); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Second Row of Main Fields -->
                                        <div class="row">
                                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Impact<span class="text-danger">*</span></label>
                                                    <select name="impact" id="impact" class="form-control select2" required>
                                                        <option value="">เลือก</option>
                                                        <?php foreach ($impactOptions as $impact): ?>
                                                            <option value="<?php echo escapeOutput($impact); ?>" <?php echo $ticket['impact'] === $impact ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($impact); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Status<span class="text-danger">*</span></label>
                                                    <select name="status" id="status" class="form-control select2" required>
                                                        <option value="">เลือก</option>
                                                        <?php foreach ($statusOptions as $status): ?>
                                                            <option value="<?php echo escapeOutput($status); ?>" <?php echo $ticket['status'] === $status ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($status); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Service Category<span class="text-danger">*</span></label>
                                                    <select name="service_category" id="service_category" class="form-control select2" required>
                                                        <option value="">เลือก</option>
                                                        <?php foreach ($serviceCategories as $category): ?>
                                                            <option value="<?php echo escapeOutput($category['service_category']); ?>" <?php echo $ticket['service_category'] === $category['service_category'] ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($category['service_category']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Category<span class="text-danger">*</span></label>
                                                    <select name="category" id="category" class="form-control select2" required>
                                                        <option value="">เลือก</option>
                                                        <?php foreach ($categoriesList as $cat): ?>
                                                            <option value="<?php echo escapeOutput($cat['category']); ?>" <?php echo $ticket['category'] === $cat['category'] ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($cat['category']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Sub Category<span class="text-danger">*</span></label>
                                                    <select name="sub_category" id="sub_category" class="form-control select2" required>
                                                        <option value="">เลือก</option>
                                                        <?php foreach ($subCategoriesList as $subCat): ?>
                                                            <option value="<?php echo escapeOutput($subCat['sub_category']); ?>" <?php echo $ticket['sub_category'] === $subCat['sub_category'] ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($subCat['sub_category']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Ticket Source<span class="text-danger">*</span></label>
                                                    <select name="source" id="source" class="form-control select2" required>
                                                        <option value="">เลือก</option>
                                                        <?php foreach ($sourceOptions as $source): ?>
                                                            <option value="<?php echo escapeOutput($source); ?>" <?php echo $ticket['source'] === $source ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($source); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Additional Information Fields -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>ผู้แจ้ง</label>
                                                    <select name="reporter" id="reporter" class="form-control select2">
                                                        <option value="">เลือกผู้แจ้ง</option>
                                                        <?php foreach ($reporters as $reporter): ?>
                                                            <option value="<?php echo escapeOutput($reporter['user_id']); ?>" <?php echo $ticket['reporter'] == $reporter['user_id'] ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($reporter['full_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>ผู้เกี่ยวข้องเพิ่มเติม (Watcher)</label>
                                                    <select name="watchers[]" class="form-control select2" multiple data-placeholder="เลือกผู้เกี่ยวข้อง">
                                                        <?php foreach ($owners as $owner): ?>
                                                            <option value="<?php echo escapeOutput($owner['user_id']); ?>" <?php echo in_array($owner['user_id'], $selectedWatchers) ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($owner['full_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Date/Time Fields -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>กำหนดเริ่มดำเนินการ (วันเวลา)</label>
                                                    <input type="datetime-local" name="start_at" id="start_at" class="form-control"
                                                        value="<?php echo $ticket['start_at'] ? date('Y-m-d\TH:i', strtotime($ticket['start_at'])) : ''; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>กำหนดแล้วเสร็จ (วันเวลา)</label>
                                                    <input type="datetime-local" name="due_at" id="due_at" class="form-control"
                                                        value="<?php echo $ticket['due_at'] ? date('Y-m-d\TH:i', strtotime($ticket['due_at'])) : ''; ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Subject and Description -->
                                        <div class="form-group">
                                            <label>หัวข้อ / Subject<span class="text-danger">*</span></label>
                                            <input type="text" name="subject" id="subject" class="form-control"
                                                value="<?php echo escapeOutput($ticket['subject']); ?>"
                                                placeholder="สรุปเหตุการณ์หรือคำขอ" maxlength="150" required>
                                            <small class="text-muted">จำกัดไม่เกิน 150 ตัวอักษร</small>
                                        </div>

                                        <div class="form-group">
                                            <label>รายละเอียดงาน / Symptom</label>
                                            <textarea name="description" id="description" rows="4" class="form-control"
                                                placeholder="ระบุรายละเอียด ปัญหา หรือความต้องการของผู้ใช้งาน"><?php echo escapeOutput($ticket['description'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Onsite Details Card -->
                                <div class="card card-outline card-warning mb-4 <?php echo $ticket['channel'] === 'Onsite' ? '' : 'd-none'; ?>" id="onsiteDetailsCard">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i>Onsite Details</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted small mb-4">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            ข้อมูลสำหรับการทำงานหน้างาน ใช้ในการประสานงานเดินทางและการเบิกค่าใช้จ่าย
                                        </p>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-play-circle text-success mr-1"></i>สถานที่เริ่มต้น / Site Start</label>
                                                    <input type="text" name="onsite_start_location" id="onsite_start_location" class="form-control"
                                                        value="<?php echo escapeOutput($onsiteData['start_location'] ?? ''); ?>"
                                                        placeholder="เช่น สำนักงานใหญ่ บางนา">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-stop-circle text-danger mr-1"></i>สถานที่ปลายทาง / Site End</label>
                                                    <input type="text" name="onsite_end_location" id="onsite_end_location" class="form-control"
                                                        value="<?php echo escapeOutput($onsiteData['end_location'] ?? ''); ?>"
                                                        placeholder="เช่น ศูนย์บริการ ขอนแก่น">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label><i class="fas fa-car text-primary mr-1"></i>เดินทางโดย</label>
                                                    <select name="onsite_travel_mode" id="onsite_travel_mode" class="form-control select2">
                                                        <option value="">เลือกวิธีการเดินทาง</option>
                                                        <optgroup label="รถยนต์">
                                                            <option value="personal_car" data-needs-mileage="1" <?php echo ($onsiteData['travel_mode'] ?? '') === 'personal_car' ? 'selected' : ''; ?>>รถส่วนตัว</option>
                                                            <option value="company_car" data-needs-mileage="1" <?php echo ($onsiteData['travel_mode'] ?? '') === 'company_car' ? 'selected' : ''; ?>>รถบริษัท</option>
                                                            <option value="taxi" <?php echo ($onsiteData['travel_mode'] ?? '') === 'taxi' ? 'selected' : ''; ?>>แท็กซี่ / รถรับจ้าง</option>
                                                        </optgroup>
                                                        <optgroup label="ขนส่งสาธารณะ">
                                                            <option value="electric_train" <?php echo ($onsiteData['travel_mode'] ?? '') === 'electric_train' ? 'selected' : ''; ?>>รถไฟฟ้า (BTS/MRT)</option>
                                                            <option value="bus" <?php echo ($onsiteData['travel_mode'] ?? '') === 'bus' ? 'selected' : ''; ?>>รถโดยสารประจำทาง</option>
                                                            <option value="van" <?php echo ($onsiteData['travel_mode'] ?? '') === 'van' ? 'selected' : ''; ?>>รถตู้โดยสาร</option>
                                                            <option value="train" <?php echo ($onsiteData['travel_mode'] ?? '') === 'train' ? 'selected' : ''; ?>>รถไฟ</option>
                                                            <option value="boat" <?php echo ($onsiteData['travel_mode'] ?? '') === 'boat' ? 'selected' : ''; ?>>เรือโดยสาร</option>
                                                        </optgroup>
                                                        <optgroup label="เครื่องบิน">
                                                            <option value="plane" <?php echo ($onsiteData['travel_mode'] ?? '') === 'plane' ? 'selected' : ''; ?>>เครื่องบิน</option>
                                                        </optgroup>
                                                        <optgroup label="อื่นๆ">
                                                            <option value="others_mileage" data-needs-mileage="1" <?php echo ($onsiteData['travel_mode'] ?? '') === 'others_mileage' ? 'selected' : ''; ?>>อื่นๆ (ต้องบันทึกเลขไมล์)</option>
                                                            <option value="others" <?php echo ($onsiteData['travel_mode'] ?? '') === 'others' ? 'selected' : ''; ?>>อื่นๆ (ไม่ต้องบันทึกเลขไมล์)</option>
                                                        </optgroup>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ฟิลด์เลขไมล์จะแสดงเมื่อเลือกพาหนะที่ต้องบันทึกไมล์ -->
                                        <div class="row d-none" id="onsiteMileageRow">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-tachometer-alt text-info mr-1"></i>เลขไมล์จุดเริ่มต้น</label>
                                                    <input type="number" step="0.1" min="0" name="onsite_odometer_start" id="onsite_odometer_start" class="form-control"
                                                        value="<?php echo escapeOutput($onsiteData['odometer_start'] ?? ''); ?>"
                                                        placeholder="เช่น 10351.5">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-tachometer-alt text-warning mr-1"></i>เลขไมล์จุดสิ้นสุด</label>
                                                    <input type="number" step="0.1" min="0" name="onsite_odometer_end" id="onsite_odometer_end" class="form-control"
                                                        value="<?php echo escapeOutput($onsiteData['odometer_end'] ?? ''); ?>"
                                                        placeholder="เช่น 10980.2">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ฟิลด์รายละเอียดเพิ่มเติมสำหรับพาหนะอื่นๆ -->
                                        <div class="row d-none" id="onsiteOtherRow">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label><i class="fas fa-info-circle text-info mr-1"></i>รายละเอียดพาหนะเพิ่มเติม</label>
                                                    <input type="text" name="onsite_travel_note" id="onsite_travel_note" class="form-control"
                                                        value="<?php echo escapeOutput($onsiteData['travel_note'] ?? ''); ?>"
                                                        placeholder="ระบุพาหนะ เช่น รถเช่า จังหวัดเชียงใหม่">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label><i class="fas fa-sticky-note text-secondary mr-1"></i>หมายเหตุเพิ่มเติม</label>
                                            <textarea name="onsite_note" id="onsite_note" rows="3" class="form-control"
                                                placeholder="รายละเอียดเพิ่มเติมที่ทีมควรรับทราบ เช่น นัดหมายติดต่อผู้ประสานงาน หรือข้อมูลเบิกจ่าย"><?php echo escapeOutput($onsiteData['note'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="card card-outline card-success mb-4">
                                    <div class="card-body text-center py-3">
                                        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-2">
                                            <a href="view_ticket.php?id=<?php echo urlencode($ticket_id); ?>" class="btn btn-outline-secondary btn-sm px-3">
                                                <i class="fas fa-times mr-1"></i>ยกเลิก
                                            </a>
                                            <button type="submit" class="btn btn-success btn-sm px-3">
                                                <i class="fas fa-save mr-1"></i>บันทึกการแก้ไข
                                            </button>
                                        </div>
                                        <small class="text-muted mt-2 d-block">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            กรุณาตรวจสอบข้อมูลก่อนกดบันทึก
                                        </small>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../../include/footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            const $channelSelect = $('#channel');
            const $onsiteCard = $('#onsiteDetailsCard');
            const $onsiteTravelMode = $('#onsite_travel_mode');
            const $onsiteMileageRow = $('#onsiteMileageRow');
            const $onsiteOtherRow = $('#onsiteOtherRow');

            function updateOnsiteTravelMode() {
                const value = $onsiteTravelMode.val();
                const selectedOption = value ? $onsiteTravelMode.find(`option[value="${value}"]`) : null;
                const needsMileage = selectedOption ? Boolean(selectedOption.data('needs-mileage')) : false;
                const needsAdditionalDetail = value === 'others' || value === 'others_mileage';

                if (needsMileage) {
                    $onsiteMileageRow.removeClass('d-none');
                } else {
                    $onsiteMileageRow.addClass('d-none');
                }

                if (needsAdditionalDetail) {
                    $onsiteOtherRow.removeClass('d-none');
                } else {
                    $onsiteOtherRow.addClass('d-none');
                }
            }

            function updateOnsiteCardVisibility() {
                const selectedChannel = $channelSelect.val();

                if (selectedChannel === 'Onsite') {
                    $onsiteCard.removeClass('d-none');
                } else {
                    $onsiteCard.addClass('d-none');
                    $onsiteMileageRow.addClass('d-none');
                    $onsiteOtherRow.addClass('d-none');
                }
            }

            $channelSelect.on('change', function() {
                updateOnsiteCardVisibility();
            });

            $onsiteTravelMode.on('change', function() {
                updateOnsiteTravelMode();
            });

            // Initialize visibility states
            updateOnsiteCardVisibility();
            updateOnsiteTravelMode();

            $('#editTicketForm').on('submit', function(e) {
                e.preventDefault();

                // แสดง Loading
                Swal.fire({
                    title: 'กำลังบันทึก...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // ส่งข้อมูลไปยัง API
                const formData = new FormData(this);

                $.ajax({
                    url: 'api/update_ticket.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: response.message,
                                confirmButtonText: 'ดู Ticket',
                                showCancelButton: true,
                                cancelButtonText: 'กลับหน้ารายการ',
                                customClass: {
                                    confirmButton: 'btn btn-success',
                                    cancelButton: 'btn btn-secondary'
                                },
                                buttonsStyling: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'view_ticket.php?id=<?php echo urlencode($ticket_id); ?>';
                                } else {
                                    window.location.href = 'service.php';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'เกิดข้อผิดพลาดในการบันทึก';

                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch(e) {
                            errorMessage = xhr.responseText || errorMessage;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: errorMessage,
                            footer: 'HTTP Status: ' + xhr.status
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>
