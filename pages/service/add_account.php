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

// ดึงข้อมูลที่จำเป็นจากฐานข้อมูลเพื่อใช้ใน dropdown
$owners = [];
$supportTeams = [];
$serviceCategories = [];
$categoriesList = [];
$subCategoriesList = [];
$projects = [];
$timelineLogs = [
    [
        'order' => 1,
        'datetime' => '2025-02-03 09:05',
        'actor' => 'Supaporn N. (Service Desk)',
        'action' => 'สร้าง Ticket พร้อมรายละเอียดเบื้องต้น',
        'detail' => 'ระบุอาการเบื้องต้นและแนบภาพหน้าจอ 2 ไฟล์',
        'attachment' => 'incident_screenshot.pdf',
        'location' => 'ช่องทาง Email'
    ],
    [
        'order' => 2,
        'datetime' => '2025-02-03 09:20',
        'actor' => 'Thanakrit K. (Security Team)',
        'action' => 'รับงาน / เปลี่ยนสถานะเป็น On Process',
        'detail' => 'ติดต่อผู้ใช้งานและเริ่มสแกนเครื่องปลายทาง',
        'attachment' => '',
        'location' => 'Security Command Center'
    ],
    [
        'order' => 3,
        'datetime' => '2025-02-03 10:05',
        'actor' => 'Thanakrit K. (Security Team)',
        'action' => 'อัปโหลดรายงานการสแกน',
        'detail' => 'พบมัลแวร์ Trojan.Generic ลบสำเร็จ รีบูตเครื่องแล้ว',
        'attachment' => 'scan_report_0302.docx',
        'location' => 'ระบบ SOC Report Portal'
    ],
    [
        'order' => 4,
        'datetime' => '2025-02-03 10:30',
        'actor' => 'Jirawat P. (Requester)',
        'action' => 'ยืนยันผลการแก้ไข',
        'detail' => 'เครื่องกลับมาใช้งานได้ตามปกติ ขอให้ติดตามอีก 24 ชม.',
        'attachment' => '',
        'location' => 'สำนักงานใหญ่ ชั้น 12'
    ],
];

$latestTimelineIndex = 0;
if (!empty($timelineLogs)) {
    $orders = array_column($timelineLogs, 'order');
    $latestTimelineIndex = $orders ? max($orders) : 0;
}

$currentUserId = $_SESSION['user_id'] ?? null;
$currentTeamId = $_SESSION['team_id'] ?? null;
$defaultTicketType = 'Incident';
$defaultStatus = 'New';
$defaultSource = 'Portal';
$defaultPriority = 'Low';
$defaultUrgency = 'Low';
$defaultImpact = 'Department';
$defaultSlaTarget = 24;
$defaultStartAt = date('Y-m-d\TH:i');

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
    'Canceled'
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
    <title>Service Desk | Create Ticket</title>
    <?php include '../../include/header.php'; ?>

    <style>
        .page-intro {
            background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
            color: #fff;
            border-radius: 1rem;
            padding: 1.75rem;
            box-shadow: 0px 20px 40px rgba(0, 0, 0, 0.12);
            position: relative;
            overflow: hidden;
        }

        .page-intro::after {
            content: '';
            position: absolute;
            right: -40px;
            bottom: -40px;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.09);
            border-radius: 50%;
        }

        .page-intro .ticket-badge {
            font-size: 0.85rem;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            background: rgba(0, 0, 0, 0.2);
        }

        .summary-card {
            border-radius: 1rem;
            border: 1px solid rgba(0, 123, 255, 0.12);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        }

        .summary-card .card-header {
            border-radius: 1rem 1rem 0 0;
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.12), rgba(102, 16, 242, 0.2));
        }

        .form-section-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-section-title i {
            color: #007bff;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.07);
        }

        .card-primary:not(.collapsed-card)>.card-header {
            background: linear-gradient(135deg, #0d6efd, #6f42c1);
        }

        .card-primary>.card-header h3 {
            color: #fff;
        }

        .info-helper {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .pill-stat {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.9rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: 0.35rem;
        }

        .pill-stat i {
            margin-right: 0.35rem;
        }

        .pill-stat.type {
            background: rgba(13, 110, 253, 0.12);
            color: #0d6efd;
        }

        .pill-stat.status {
            background: rgba(25, 135, 84, 0.12);
            color: #198754;
        }

        .pill-stat.priority {
            background: rgba(220, 53, 69, 0.12);
            color: #dc3545;
        }

        .ticket-preview textarea {
            background: rgba(248, 249, 250, 0.85);
        }

        .ticket-preview .service-preview-value {
            display: inline-block;
            font-size: 1.05rem;
            font-weight: 600;
            color: #212529;
        }

        .dropzone-mock {
            border: 2px dashed rgba(13, 110, 253, 0.35);
            border-radius: 1rem;
            background: rgba(13, 110, 253, 0.04);
            padding: 1.5rem;
            text-align: center;
            color: #4663ab;
            transition: all 0.2s ease;
        }

        .dropzone-mock:hover {
            background: rgba(13, 110, 253, 0.08);
            border-color: rgba(13, 110, 253, 0.55);
        }

        .timeline-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(13, 110, 253, 0.12);
            color: #0d6efd;
            margin-right: 0.85rem;
        }

        .timeline-item+.timeline-item {
            border-top: 1px dashed rgba(13, 110, 253, 0.2);
            padding-top: 1rem;
        }

        .timeline-item small {
            color: #6c757d;
        }

        .form-control,
        .select2-container--default .select2-selection--single {
            border-radius: 0.75rem;
            border-color: rgba(0, 0, 0, 0.08);
        }

        .select2-container--default .select2-selection--single {
            height: calc(2.5rem + 2px);
            padding: 0.35rem 0.85rem;
        }

        .radio-soft label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 0.8rem;
            border-radius: 0.75rem;
            border: 1px solid rgba(13, 110, 253, 0.15);
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .radio-soft input:checked+label {
            background: rgba(13, 110, 253, 0.08);
            border-color: rgba(13, 110, 253, 0.45);
            color: #0d6efd;
            font-weight: 600;
        }

        .section-divider {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.12rem;
            color: #6c757d;
        }

        .ticket-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.75rem;
        }

        .ticket-meta-grid .meta-card {
            border-radius: 0.75rem;
            padding: 0.65rem 0.75rem;
            background: rgba(248, 249, 250, 0.85);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .meta-card span {
            display: block;
            font-size: 0.75rem;
            color: #6c757d;
        }

        .meta-card strong {
            font-size: 0.95rem;
            color: #212529;
        }

        .activity-timeline-card {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(13, 110, 253, 0.08);
            position: relative;
        }

        .activity-timeline-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.08), rgba(102, 16, 242, 0.04));
            opacity: 0.4;
            pointer-events: none;
        }

        .activity-timeline-card .card-body {
            position: relative;
            z-index: 1;
            padding: 1.5rem 1.75rem;
        }

        .activity-timeline {
            position: relative;
            margin-left: 1.25rem;
        }

        .activity-timeline::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, rgba(13, 110, 253, 0.4), rgba(102, 16, 242, 0.4));
        }

        .activity-item {
            position: relative;
            padding-left: 2.5rem;
            margin-bottom: 1.75rem;
        }

        .activity-marker {
            position: absolute;
            left: -1px;
            top: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .activity-marker .marker-index {
            font-size: 0.75rem;
            background: #0d6efd;
            color: #fff;
            border-radius: 999px;
            padding: 0.25rem 0.6rem;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.35);
            margin-bottom: 0.45rem;
        }

        .activity-marker .marker-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #6f42c1;
            box-shadow: 0 0 0 4px rgba(111, 66, 193, 0.12);
        }

        .activity-content {
            background: rgba(255, 255, 255, 0.88);
            border-radius: 0.85rem;
            border: 1px solid rgba(13, 110, 253, 0.08);
            padding: 1.15rem 1.25rem;
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.08);
        }

        .activity-content h5 {
            font-size: 1rem;
            color: #212529;
        }

        .activity-content .badge-light {
            background: rgba(13, 110, 253, 0.15);
            color: #0d6efd;
        }

        .activity-attachment {
            display: inline-flex;
            align-items: center;
            background: rgba(13, 110, 253, 0.12);
            color: #0d6efd;
            border-radius: 999px;
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .activity-attachment i {
            margin-right: 0.4rem;
        }

        @media (max-width: 991.98px) {
            .summary-card {
                margin-top: 1.5rem;
            }

            .subject-cell {
                width: 100%;
            }

            .activity-timeline::before {
                left: 8px;
            }

            .activity-marker {
                left: -6px;
            }

            .activity-content {
                padding: 1rem 1.05rem;
            }
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="page-intro d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                                <div>
                                    <span class="ticket-badge mb-2 d-inline-flex align-items-center"><i class="fas fa-tools mr-2"></i>Service Desk</span>
                                    <h1 class="mb-1">Create Service Ticket</h1>
                                    <p class="mb-0" style="opacity:0.8;">บันทึกงานบริการ Incident / Request / Change พร้อมรายละเอียดที่ทีมต้องติดตาม</p>
                                </div>
                                <div class="text-md-right mt-3 mt-md-0">
                                    <div class="ticket-meta-grid bg-white p-3 rounded-lg" style="min-width: 580px;">
                                        <div class="meta-card">
                                            <span>Ticket ID</span>
                                            <strong id="preview-ticket-id">AUTO-GENERATED</strong>
                                        </div>
                                        <div class="meta-card">
                                            <span>Created By</span>
                                            <strong><?php echo escapeOutput($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></strong>
                                        </div>
                                        <div class="meta-card">
                                            <span>Team Context</span>
                                            <strong><?php echo escapeOutput($_SESSION['team_name'] ?? 'N/A'); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-8 order-2 order-lg-1">
                            <form id="createTicketForm" method="POST" action="#" novalidate>
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                <div class="card card-primary mb-4">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Ticket Information</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-section-title"><i class="fas fa-ticket-alt"></i> ข้อมูลพื้นฐาน</div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Project</label>
                                                    <select name="project_id" id="project_id" class="form-control select2" data-preview="project">
                                                        <option value="" selected>ไม่ระบุโครงการ</option>
                                                        <?php foreach ($projects as $project): ?>
                                                            <option value="<?php echo escapeOutput($project['project_id']); ?>"><?php echo escapeOutput($project['project_name']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Ticket Type<span class="text-danger">*</span></label>
                                                    <select name="ticket_type" id="ticket_type" class="form-control select2" data-preview="type" required>
                                                        <option value="" disabled>เลือกประเภทงาน</option>
                                                        <?php foreach ($ticketTypes as $type): ?>
                                                            <option value="<?php echo escapeOutput($type['id']); ?>" <?php echo $type['id'] === $defaultTicketType ? 'selected' : ''; ?>><?php echo escapeOutput($type['label']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <!-- <small class="info-helper">เลือก Incident สำหรับเหตุขัดข้อง, Service สำหรับคำขอ, Change สำหรับการเปลี่ยนแปลง</small> -->
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Status<span class="text-danger">*</span></label>
                                                    <select name="status" id="status" class="form-control select2" data-preview="status" required>
                                                        <option value="" disabled>เลือกสถานะ</option>
                                                        <?php foreach ($statusOptions as $status): ?>
                                                            <option value="<?php echo escapeOutput($status); ?>" <?php echo $status === $defaultStatus ? 'selected' : ''; ?>><?php echo escapeOutput($status); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Ticket Source<span class="text-danger">*</span></label>
                                                    <select name="source" id="source" class="form-control select2" required>
                                                        <option value="" disabled>เลือกช่องทาง</option>
                                                        <?php foreach ($sourceOptions as $source): ?>
                                                            <option value="<?php echo escapeOutput($source); ?>" <?php echo $source === $defaultSource ? 'selected' : ''; ?>><?php echo escapeOutput($source); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Service Category<span class="text-danger">*</span></label>
                                                    <select name="service_category" id="service_category" class="form-control select2" data-preview="service_category" required>
                                                        <option value="" disabled selected>เลือกหมวดหมู่บริการ</option>
                                                        <?php foreach ($serviceCategories as $category): ?>
                                                            <option value="<?php echo escapeOutput($category['service_category']); ?>"><?php echo escapeOutput($category['service_category']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Category</label>
                                                    <select name="category" class="form-control select2" data-preview="category">
                                                        <option value="" selected>เลือก Category</option>
                                                        <?php foreach ($categoriesList as $cat): ?>
                                                            <option value="<?php echo escapeOutput($cat['category']); ?>"><?php echo escapeOutput($cat['category']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Sub Category</label>
                                                    <select name="sub_category" class="form-control select2" data-preview="sub_category">
                                                        <option value="" selected>เลือก Sub Category</option>
                                                        <?php foreach ($subCategoriesList as $subCat): ?>
                                                            <option value="<?php echo escapeOutput($subCat['sub_category']); ?>"><?php echo escapeOutput($subCat['sub_category']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Subject<span class="text-danger">*</span></label>
                                            <input type="text" name="subject" id="subject" class="form-control" placeholder="สรุปเหตุการณ์หรือคำขอ" maxlength="150" data-preview="subject" required>
                                            <small class="info-helper">จำกัดไม่เกิน 150 ตัวอักษร</small>
                                        </div>

                                        <div class="form-group mb-0">
                                            <label>รายละเอียดงาน / Symptom</label>
                                            <textarea name="description" id="description" rows="5" class="form-control" placeholder="ระบุรายละเอียด ปัญหา หรือความต้องการของผู้ใช้งาน"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-outline card-info mb-4">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-user-shield mr-2"></i>Assignment & SLA</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Job Owner<span class="text-danger">*</span></label>
                                                    <select name="job_owner" id="job_owner" class="form-control select2" data-preview="owner" required>
                                                        <option value="" disabled>เลือกผู้รับผิดชอบ</option>
                                                        <?php foreach ($owners as $owner): ?>
                                                            <option value="<?php echo escapeOutput($owner['user_id']); ?>" data-role="<?php echo escapeOutput($owner['role']); ?>" <?php echo (string) $owner['user_id'] === (string) $currentUserId ? 'selected' : ''; ?>>
                                                                <?php echo escapeOutput($owner['full_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Support Team</label>
                                                    <select name="support_team" id="support_team" class="form-control select2">
                                                        <option value="" disabled>เลือกทีมที่ดูแล</option>
                                                        <?php foreach ($supportTeams as $team): ?>
                                                            <option value="<?php echo escapeOutput($team['team_id']); ?>" <?php echo (string) $team['team_id'] === (string) $currentTeamId ? 'selected' : ''; ?>><?php echo escapeOutput($team['team_name']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Preferred Channel</label>
                                                <div class="d-flex flex-wrap gap-2 radio-soft">
                                                    <?php foreach ($channelOptions as $index => $channel): ?>
                                                        <div class="mr-2 mb-2">
                                                            <input type="radio" class="d-none" name="channel" id="channel_<?php echo $index; ?>" value="<?php echo escapeOutput($channel); ?>" <?php echo $channel === 'Office' ? 'checked' : ''; ?>>
                                                            <label for="channel_<?php echo $index; ?>"><i class="fas fa-headset"></i> <?php echo escapeOutput($channel); ?></label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>ผู้เกี่ยวข้องเพิ่มเติม (Watcher)</label>
                                            <select name="watchers[]" class="form-control select2" multiple data-placeholder="เลือกผู้เกี่ยวข้อง">
                                                <?php foreach ($owners as $owner): ?>
                                                    <option value="<?php echo escapeOutput($owner['user_id']); ?>"><?php echo escapeOutput($owner['full_name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>


                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Priority<span class="text-danger">*</span></label>
                                                    <select name="priority" id="priority" class="form-control select2" data-preview="priority" required>
                                                        <option value="" disabled>เลือกระดับความสำคัญ</option>
                                                        <?php foreach ($priorityOptions as $priority): ?>
                                                            <option value="<?php echo escapeOutput($priority); ?>" <?php echo $priority === $defaultPriority ? 'selected' : ''; ?>><?php echo escapeOutput($priority); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Urgency<span class="text-danger">*</span></label>
                                                    <select name="urgency" id="urgency" class="form-control select2" required>
                                                        <option value="" disabled>เลือกระดับความเร่งด่วน</option>
                                                        <?php foreach ($urgencyOptions as $urgency): ?>
                                                            <option value="<?php echo escapeOutput($urgency); ?>" <?php echo $urgency === $defaultUrgency ? 'selected' : ''; ?>><?php echo escapeOutput($urgency); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Impact<span class="text-danger">*</span></label>
                                                    <select name="impact" id="impact" class="form-control select2" required>
                                                        <option value="" disabled>เลือกผลกระทบ</option>
                                                        <?php foreach ($impactOptions as $impact): ?>
                                                            <option value="<?php echo escapeOutput($impact); ?>" <?php echo $impact === $defaultImpact ? 'selected' : ''; ?>><?php echo escapeOutput($impact); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>SLA Target (ชั่วโมง)</label>
                                                    <input type="number" step="0.25" min="0" name="sla_target" id="sla_target" class="form-control" placeholder="เช่น 4" data-preview="sla" value="<?php echo escapeOutput($defaultSlaTarget); ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>กำหนดเริ่มดำเนินการ</label>
                                                    <input type="datetime-local" name="start_at" id="start_at" class="form-control" data-preview="start_at" value="<?php echo escapeOutput($defaultStartAt); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>กำหนดแล้วเสร็จ</label>
                                                    <input type="datetime-local" name="due_at" id="due_at" class="form-control" data-preview="due_at">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-0 d-none" id="caseDescriptionGroup">
                                            <label>สาเหตุ / Case Description<span class="text-danger" id="caseDescriptionRequired"></span></label>
                                            <textarea name="case_description" id="case_description" rows="4" class="form-control" placeholder="อธิบายสาเหตุที่พบและผลกระทบที่เกิดขึ้น"></textarea>
                                        </div>

                                        <div class="form-group mb-0 mt-3 d-none" id="resolveActionGroup">
                                            <label>แนวทางแก้ไข / Resolve Action<span class="text-danger" id="resolveActionRequired"></span></label>
                                            <textarea name="resolve_action" id="resolve_action" rows="4" class="form-control" placeholder="สรุปขั้นตอนการแก้ไขและผลลัพธ์ที่เกิดขึ้น"></textarea>
                                        </div>

                                        <div class="form-group mb-0" id="nextActionGroup">
                                            <label>รายละเอียด / Next Action<span class="text-danger" id="nextActionRequired"></span></label>
                                            <textarea name="next_action" id="next_action" rows="4" class="form-control" placeholder="ระบุขั้นตอนถัดไปหรือสิ่งที่ต้องติดตาม"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-outline card-secondary mb-4 d-none" id="onsiteDetailsCard">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-user-friends mr-2"></i>Onsite Details</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted small mb-3">สรุปข้อมูลการปฏิบัติงานหน้างานเพื่อประสานงานเดินทางและการเบิกค่าใช้จ่าย</p>
                                        <div class="mb-3">
                                            <span class="badge badge-light border text-secondary mr-1 mb-1">รถส่วนตัว</span>
                                            <span class="badge badge-light border text-secondary mr-1 mb-1">รถบริษัท</span>
                                            <span class="badge badge-light border text-secondary mr-1 mb-1">รถไฟฟ้า (BTS/MRT)</span>
                                            <span class="badge badge-light border text-secondary mr-1 mb-1">แท็กซี่ / รถรับจ้าง</span>
                                            <span class="badge badge-light border text-secondary mr-1 mb-1">รถโดยสารประจำทาง</span>
                                            <span class="badge badge-light border text-secondary mr-1 mb-1">รถไฟ</span>
                                            <span class="badge badge-light border text-secondary mr-1 mb-1">เรือโดยสาร</span>
                                            <span class="badge badge-light border text-secondary mr-1 mb-1">เครื่องบิน</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>สถานที่เริ่มต้น / Site Start</label>
                                                    <input type="text" name="onsite_start_location" id="onsite_start_location" class="form-control" placeholder="เช่น สำนักงานใหญ่ บางนา">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>สถานที่ปลายทาง / Site End</label>
                                                    <input type="text" name="onsite_end_location" id="onsite_end_location" class="form-control" placeholder="เช่น ศูนย์บริการ ขอนแก่น">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>เดินทางโดย<span class="text-danger">*</span></label>
                                                    <select name="onsite_travel_mode" id="onsite_travel_mode" class="form-control select2" data-placeholder="เลือกวิธีการเดินทาง">
                                                        <option value="" disabled selected>เลือกวิธีการเดินทาง</option>
                                                        <option value="personal_car" data-needs-mileage="1">รถส่วนตัว</option>
                                                        <option value="company_car" data-needs-mileage="1">รถบริษัท</option>
                                                        <option value="electric_train">รถไฟฟ้า (BTS/MRT)</option>
                                                        <option value="taxi">แท็กซี่ / รถรับจ้าง</option>
                                                        <option value="bus">รถโดยสารประจำทาง</option>
                                                        <option value="van">รถตู้โดยสาร</option>
                                                        <option value="train">รถไฟ</option>
                                                        <option value="boat">เรือโดยสาร</option>
                                                        <option value="plane">เครื่องบิน</option>
                                                        <option value="others_mileage" data-needs-mileage="1">อื่นๆ (ระบุและต้องบันทึกเลขไมล์)</option>
                                                        <option value="others">อื่นๆ (ไม่ต้องบันทึกเลขไมล์)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-none" id="onsiteOtherRow">
                                                <div class="form-group">
                                                    <label>รายละเอียดพาหนะเพิ่มเติม</label>
                                                    <input type="text" name="onsite_travel_note" id="onsite_travel_note" class="form-control" placeholder="ระบุพาหนะ เช่น รถเช่า จังหวัดเชียงใหม่">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row d-none" id="onsiteMileageRow">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>เลขไมล์เริ่มต้น</label>
                                                    <input type="number" min="0" name="onsite_odometer_start" id="onsite_odometer_start" class="form-control" placeholder="เช่น 10351">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>เลขไมล์ปลายทาง</label>
                                                    <input type="number" min="0" name="onsite_odometer_end" id="onsite_odometer_end" class="form-control" placeholder="เช่น 10980">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-0">
                                            <label>หมายเหตุเพิ่มเติม</label>
                                            <textarea name="onsite_note" id="onsite_note" rows="3" class="form-control" placeholder="รายละเอียดเพิ่มเติมที่ทีมควรรับทราบ เช่น นัดหมายติดต่อผู้ประสานงาน หรือข้อมูลเบิกจ่าย"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-outline card-primary mb-4">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-paperclip mr-2"></i>Attachments (Job Details)</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="dropzone-mock">
                                            <div class="mb-2"><i class="fas fa-cloud-upload-alt fa-2x"></i></div>
                                            <p class="mb-1">ลาก & วางไฟล์ที่เกี่ยวข้อง หรือเลือกจากเครื่อง</p>
                                            <small class="text-muted">รองรับไฟล์ JPG, PNG, PDF, DOCX ขนาดรวมไม่เกิน 20 MB</small>
                                            <div class="mt-3">
                                                <label class="btn btn-outline-primary btn-sm mb-0">
                                                    <i class="fas fa-file-upload mr-2"></i>เลือกไฟล์
                                                    <input type="file" name="attachments[]" multiple class="d-none">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <div>
                                        <button type="reset" class="btn btn-light border"><i class="fas fa-undo mr-1"></i>Clear Form</button>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-secondary mr-2" id="btnDraft"><i class="fas fa-save mr-1"></i>Save as Draft</button>
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i>Create Ticket</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-lg-4 order-1 order-lg-2">
                            <div class="card summary-card">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h3 class="card-title mb-0"><i class="fas fa-eye mr-2"></i>Ticket Preview</h3>
                                    <span class="badge badge-primary">Real-time</span>
                                </div>
                                <div class="card-body ticket-preview">
                                    <div class="mb-3">
                                        <span class="pill-stat type" id="preview-pill-type"><i class="fas fa-info-circle"></i>Awaiting Type</span>
                                        <span class="pill-stat status" id="preview-pill-status"><i class="fas fa-stream"></i>Awaiting Status</span>
                                        <span class="pill-stat priority" id="preview-pill-priority"><i class="fas fa-bolt"></i>Priority</span>
                                    </div>

                                    <h4 id="preview-subject" class="mb-2">Subject</h4>
                                    <p class="mb-3 text-muted" id="preview-description">รายละเอียดงานจะแสดงที่นี่เมื่อกรอกข้อมูล</p>

                                    <div class="section-divider mb-2">Service Details</div>
                                    <ul class="list-unstyled small text-muted mb-3" style="line-height: 1.6;">
                                        <li><i class="fas fa-layer-group mr-2 text-primary"></i>Service Category: <span class="service-preview-value" id="preview-service-category">-</span></li>
                                        <li><i class="fas fa-project-diagram mr-2 text-primary"></i>Project: <span class="service-preview-value" id="preview-project">-</span></li>
                                        <li><i class="fas fa-tags mr-2 text-primary"></i>Category: <span class="service-preview-value" id="preview-category">-</span> / <span class="service-preview-value" id="preview-sub_category">-</span></li>
                                        <li><i class="fas fa-user-cog mr-2 text-primary"></i>Owner: <span class="service-preview-value" id="preview-owner">-</span></li>
                                    </ul>

                                    <div class="section-divider mb-2">SLA Overview</div>
                                    <div class="timeline-item d-flex">
                                        <span class="timeline-badge"><i class="fas fa-calendar-plus"></i></span>
                                        <div>
                                            <strong>Start At</strong>
                                            <div id="preview-start_at" class="text-muted small">-</div>
                                        </div>
                                    </div>
                                    <div class="timeline-item d-flex mt-3">
                                        <span class="timeline-badge"><i class="fas fa-clock"></i></span>
                                        <div>
                                            <strong>Due At</strong>
                                            <div id="preview-due_at" class="text-muted small">-</div>
                                        </div>
                                    </div>
                                    <div class="timeline-item d-flex mt-3">
                                        <span class="timeline-badge"><i class="fas fa-stopwatch"></i></span>
                                        <div>
                                            <strong>SLA Target</strong>
                                            <div id="preview-sla" class="text-muted small">-</div>
                                        </div>
                                    </div>

                                    <div class="section-divider mb-2 mt-4">Change Log</div>
                                    <textarea class="form-control" rows="3" readonly id="preview-log">• Awaiting ticket information...</textarea>
                                </div>
                            </div>

                            <div class="card activity-timeline-card mt-4">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h3 class="card-title mb-0"><i class="fas fa-stream mr-2"></i>Activity Timeline</h3>
                                    <span class="badge badge-light text-primary border border-primary">Live Preview</span>
                                </div>
                                <div class="card-body">
                                    <div class="activity-timeline" id="activityTimeline">
                                        <?php foreach ($timelineLogs as $log): ?>
                                            <div class="activity-item">
                                                <div class="activity-marker">
                                                    <span class="marker-index">#<?php echo escapeOutput($log['order']); ?></span>
                                                    <span class="marker-dot"></span>
                                                </div>
                                                <div class="activity-content">
                                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                                                        <div class="mb-2 mb-md-0">
                                                            <h5 class="mb-1"><?php echo escapeOutput($log['action']); ?></h5>
                                                            <div class="text-muted small"><i class="fas fa-user-circle mr-2"></i><?php echo escapeOutput($log['actor']); ?></div>
                                                        </div>
                                                        <div class="text-md-right">
                                                            <span class="badge badge-light border"><i class="far fa-clock mr-1"></i><?php echo escapeOutput($log['datetime']); ?></span>
                                                            <div class="small text-muted mt-1"><i class="fas fa-map-marker-alt mr-1 text-danger"></i><?php echo escapeOutput($log['location']); ?></div>
                                                        </div>
                                                    </div>
                                                    <p class="mt-3 mb-2 text-muted">
                                                        <?php echo escapeOutput($log['detail']); ?>
                                                    </p>
                                                    <?php if (!empty($log['attachment'])): ?>
                                                        <span class="activity-attachment"><i class="fas fa-paperclip"></i><?php echo escapeOutput($log['attachment']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-4">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-lightbulb mr-2"></i>Guideline</h3>
                                </div>
                                <div class="card-body">
                                    <ul class="small text-muted mb-0" style="line-height: 1.7;">
                                        <li>ตรวจสอบว่าชนิดของ Ticket ถูกต้องก่อนบันทึก</li>
                                        <li>ระบุ Priority / Impact ให้สอดคล้องกับความเร่งด่วน</li>
                                        <li>กำหนด SLA และผู้รับผิดชอบให้ชัดเจนเพื่อไม่ให้ Ticket ค้าง</li>
                                        <li>หาก Ticket มีความเสี่ยง ให้บันทึกหมายเหตุเพิ่มเติมหรือแนบไฟล์ประกอบ</li>
                                    </ul>
                                </div>
                            </div>
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

            const previewMap = {
                type: '#preview-pill-type',
                status: '#preview-pill-status',
                priority: '#preview-pill-priority',
                subject: '#preview-subject',
                service_category: '#preview-service-category',
                project: '#preview-project',
                category: '#preview-category',
                sub_category: '#preview-sub_category',
                owner: '#preview-owner',
                sla: '#preview-sla',
                start_at: '#preview-start_at',
                due_at: '#preview-due_at'
            };

            const $statusSelect = $('#status');
            const $caseGroup = $('#caseDescriptionGroup');
            const $resolveGroup = $('#resolveActionGroup');
            const $nextGroup = $('#nextActionGroup');
            const $caseField = $('#case_description');
            const $resolveField = $('#resolve_action');
            const $nextField = $('#next_action');
            const $caseRequired = $('#caseDescriptionRequired');
            const $resolveRequired = $('#resolveActionRequired');
            const $nextRequired = $('#nextActionRequired');

            const $channelRadios = $('input[name="channel"]');
            const $onsiteCard = $('#onsiteDetailsCard');
            const $onsiteTravelMode = $('#onsite_travel_mode');
            const $onsiteMileageRow = $('#onsiteMileageRow');
            const $onsiteOtherRow = $('#onsiteOtherRow');
            const $activityTimeline = $('#activityTimeline');
            const $previewLog = $('#preview-log');

            const actorName = <?php echo json_encode(trim(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')) ?: 'Service User'); ?>;
            let timelineCounter = <?php echo (int) $latestTimelineIndex; ?>;
            let previewLogDetails = [];

            function updateStatusDependentFields() {
                const isResolved = $statusSelect.val() === 'Resolved';

                if (isResolved) {
                    $caseGroup.removeClass('d-none');
                    $resolveGroup.removeClass('d-none');
                    $nextGroup.addClass('d-none');
                    $caseField.prop('required', true);
                    $resolveField.prop('required', true);
                    $nextField.prop('required', false);
                    $caseRequired.text('*');
                    $resolveRequired.text('*');
                    $nextRequired.text('');
                } else {
                    $caseGroup.addClass('d-none');
                    $resolveGroup.addClass('d-none');
                    $nextGroup.removeClass('d-none');
                    $caseField.prop('required', false);
                    $resolveField.prop('required', false);
                    $nextField.prop('required', true);
                    $caseRequired.text('');
                    $resolveRequired.text('');
                    $nextRequired.text('*');
                }
            }

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
                const selectedChannel = $channelRadios.filter(':checked').val();
                if (selectedChannel === 'Onsite') {
                    $onsiteCard.removeClass('d-none');
                } else {
                    $onsiteCard.addClass('d-none');
                    $onsiteMileageRow.addClass('d-none');
                    $onsiteOtherRow.addClass('d-none');
                }
            }

            function formatTimestamp(date) {
                try {
                    return date.toLocaleString('th-TH', {
                        dateStyle: 'medium',
                        timeStyle: 'short'
                    });
                } catch (error) {
                    const y = date.getFullYear();
                    const m = String(date.getMonth() + 1).padStart(2, '0');
                    const d = String(date.getDate()).padStart(2, '0');
                    const hh = String(date.getHours()).padStart(2, '0');
                    const mm = String(date.getMinutes()).padStart(2, '0');
                    return `${y}-${m}-${d} ${hh}:${mm}`;
                }
            }

            function updateSubjectLogLine(subjectValue) {
                const subjectLine = subjectValue ? `• ปรับหัวข้อเป็น: ${subjectValue}` : '• Awaiting ticket information...';
                const lines = [subjectLine].concat(previewLogDetails);
                $previewLog.val(lines.join('\n'));
            }

            function appendPreviewLogEntry(lines) {
                previewLogDetails = lines;
                updateSubjectLogLine($('#subject').val());
            }

            function appendTimelineEntry({ statusText, isResolved, caseDescription, resolveAction, nextAction, timestamp }) {
                timelineCounter += 1;
                const displayIndex = `#${String(timelineCounter).padStart(2, '0')}`;
                const displayTimestamp = formatTimestamp(timestamp);

                const $item = $('<div>').addClass('activity-item');
                const $marker = $('<div>').addClass('activity-marker')
                    .append($('<span>').addClass('marker-index').text(displayIndex))
                    .append($('<span>').addClass('marker-dot'));

                const $content = $('<div>').addClass('activity-content');
                const $headerRow = $('<div>').addClass('d-flex flex-column flex-md-row justify-content-between align-items-md-center');

                const $headerLeft = $('<div>').addClass('mb-2 mb-md-0');
                $headerLeft.append($('<h5>').addClass('mb-1').text(`อัปเดตสถานะเป็น ${statusText}`));
                const $actorInfo = $('<div>').addClass('text-muted small');
                $actorInfo.append($('<i>').addClass('fas fa-user-circle mr-2'));
                $actorInfo.append(document.createTextNode(actorName));
                $headerLeft.append($actorInfo);

                const $headerRight = $('<div>').addClass('text-md-right');
                const $clockBadge = $('<span>').addClass('badge badge-light border');
                $clockBadge.append($('<i>').addClass('far fa-clock mr-1'));
                $clockBadge.append(document.createTextNode(displayTimestamp));
                $headerRight.append($clockBadge);
                const $locationInfo = $('<div>').addClass('small text-muted mt-1');
                $locationInfo.append($('<i>').addClass('fas fa-map-marker-alt mr-1 text-danger'));
                $locationInfo.append(document.createTextNode('บันทึกผ่านระบบ Service Desk (Web)'));
                $headerRight.append($locationInfo);

                $headerRow.append($headerLeft).append($headerRight);

                const $detailParagraph = $('<p>').addClass('mt-3 mb-2 text-muted');
                if (isResolved) {
                    $detailParagraph.append($('<strong>').text('สาเหตุ: '));
                    $detailParagraph.append(document.createTextNode(caseDescription || '-'));
                    $detailParagraph.append($('<br>'));
                    $detailParagraph.append($('<strong>').text('แนวทางแก้ไข: '));
                    $detailParagraph.append(document.createTextNode(resolveAction || '-'));
                } else {
                    $detailParagraph.append($('<strong>').text('Next Action: '));
                    $detailParagraph.append(document.createTextNode(nextAction || '-'));
                }

                $content.append($headerRow).append($detailParagraph);

                $item.append($marker).append($content);
                $activityTimeline.prepend($item);

                return displayTimestamp;
            }

            function syncPreviewValue($element) {
                const target = $element.data('preview');
                if (!target || !previewMap[target]) {
                    return;
                }

                let rawValue;
                let displayText;

                if ($element.is('select')) {
                    rawValue = $element.val();
                    displayText = $element.find('option:selected').text();
                } else {
                    rawValue = $element.val();
                    displayText = rawValue;
                }

                if (!rawValue) {
                    displayText = '-';
                }

                if (target === 'subject') {
                    displayText = rawValue ? rawValue : 'Subject';
                    $('#preview-description').text($('#description').val() || 'รายละเอียดงานจะแสดงที่นี่เมื่อกรอกข้อมูล');
                    updateSubjectLogLine(rawValue);
                }

                $(previewMap[target]).text(displayText);
            }

            $channelRadios.on('change', function() {
                updateOnsiteCardVisibility();
            });

            $onsiteTravelMode.on('change', function() {
                updateOnsiteTravelMode();
            });

            $statusSelect.on('change', function() {
                updateStatusDependentFields();
            });

            updateOnsiteCardVisibility();
            updateOnsiteTravelMode();
            updateStatusDependentFields();
            updateSubjectLogLine($('#subject').val());

            $('[data-preview]').on('change keyup', function() {
                syncPreviewValue($(this));
            });

            $('[data-preview]').each(function() {
                syncPreviewValue($(this));
            });

            $('#description').on('keyup', function() {
                $('#preview-description').text($(this).val() || 'รายละเอียดงานจะแสดงที่นี่เมื่อกรอกข้อมูล');
            });

            $('#job_owner').on('change', function() {
                const selected = $(this).find('option:selected');
                const role = selected.data('role') ? ' (' + selected.data('role') + ')' : '';
                const text = selected.text() ? selected.text() + role : '-';
                $('#preview-owner').text(text);
            });

            $('#job_owner').trigger('change');

            $('#createTicketForm').on('submit', function(e) {
                e.preventDefault();
                const statusValue = $statusSelect.val();
                const statusText = $statusSelect.find('option:selected').text() || statusValue || '-';
                const isResolved = statusValue === 'Resolved';
                const caseDescription = $caseField.val().trim();
                const resolveAction = $resolveField.val().trim();
                const nextAction = $nextField.val().trim();
                const timestamp = new Date();

                const displayTimestamp = appendTimelineEntry({
                    statusText,
                    isResolved,
                    caseDescription,
                    resolveAction,
                    nextAction,
                    timestamp
                });

                const logLines = [`• ${displayTimestamp} ${actorName} อัปเดตสถานะเป็น ${statusText}`];
                if (isResolved) {
                    logLines.push(`   - สาเหตุ: ${caseDescription || '-'}`);
                    logLines.push(`   - แนวทางแก้ไข: ${resolveAction || '-'}`);
                } else {
                    logLines.push(`   - Next Action: ${nextAction || '-'}`);
                }
                appendPreviewLogEntry(logLines);

                Swal.fire({
                    icon: 'success',
                    title: 'บันทึก Ticket สำเร็จ',
                    text: 'ข้อมูลจะถูกส่งต่อให้ทีมที่เกี่ยวข้อง',
                    confirmButtonText: 'ตกลง',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(() => {
                    this.reset();
                    $('.select2').each(function() {
                        const $select = $(this);
                        const defaultSelections = $select.find('option[selected]').map(function() {
                            return $(this).val();
                        }).get();

                        if ($select.prop('multiple')) {
                            $select.val(defaultSelections.length ? defaultSelections : null).trigger('change');
                        } else if (defaultSelections.length > 0) {
                            $select.val(defaultSelections[0]).trigger('change');
                        } else {
                            $select.val(null).trigger('change');
                        }
                    });

                    previewLogDetails = [];
                    updateSubjectLogLine('');
                    updateStatusDependentFields();
                    updateOnsiteCardVisibility();
                    updateOnsiteTravelMode();

                    $('[data-preview]').each(function() {
                        syncPreviewValue($(this));
                    });

                    $('#preview-description').text('รายละเอียดงานจะแสดงที่นี่เมื่อกรอกข้อมูล');
                    $('#job_owner').trigger('change');
                });
            });

            $('#btnDraft').on('click', function() {
                Swal.fire({
                    icon: 'info',
                    title: 'บันทึกเป็น Draft แล้ว',
                    text: 'สามารถกลับมาแก้ไขได้ในภายหลัง',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        });
    </script>
</body>

</html>
