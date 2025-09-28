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
$reporters = []; // ผู้แจ้ง
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
$currentUserName = trim(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')) ?: 'ผู้ใช้ระบบ';
$currentDateTime = date('Y-m-d\TH:i');
$defaultTicketType = 'Incident';
$defaultStatus = 'New';
$defaultSource = 'Portal';
$defaultPriority = 'Low';
$defaultUrgency = 'Low';
$defaultImpact = 'Department';
$defaultSlaTarget = 24;
$defaultStartAt = $currentDateTime;

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
                            <form id="createTicketForm" method="POST" action="#" novalidate>
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                <!-- Header Card -->
                                <div class="card card-primary mb-4">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-plus-circle mr-2"></i>สร้าง Service Ticket</h3>
                                    </div>
                                    <div class="card-body">
                                        <!-- Auto-filled Info Fields -->
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>วันเวลาที่สร้าง</label>
                                                    <input type="datetime-local" name="created_at" class="form-control" value="<?php echo $currentDateTime; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>ผู้สร้าง</label>
                                                    <input type="text" class="form-control" value="<?php echo escapeOutput($currentUserName); ?>" readonly>
                                                    <input type="hidden" name="created_by" value="<?php echo escapeOutput($currentUserId); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Project<span class="text-danger">*</span></label>
                                                    <select name="project_id" id="project_id" class="form-control select2" required>
                                                        <option value="" selected>เลือก</option>
                                                        <?php foreach ($projects as $project): ?>
                                                            <option value="<?php echo escapeOutput($project['project_id']); ?>"><?php echo escapeOutput($project['project_name']); ?></option>
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
                                                            <option value="<?php echo escapeOutput($type['id']); ?>" <?php echo $type['id'] === $defaultTicketType ? 'selected' : ''; ?>><?php echo escapeOutput($type['label']); ?></option>
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
                                                            <option value="<?php echo escapeOutput($owner['user_id']); ?>" <?php echo (string) $owner['user_id'] === (string) $currentUserId ? 'selected' : ''; ?>>
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
                                                        <option value="1">1 ชั่วโมง</option>
                                                        <option value="2">2 ชั่วโมง</option>
                                                        <option value="4" selected>4 ชั่วโมง</option>
                                                        <option value="8">8 ชั่วโมง</option>
                                                        <option value="24">24 ชั่วโมง</option>
                                                        <option value="48">48 ชั่วโมง</option>
                                                        <option value="72">72 ชั่วโมง</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                                <div class="form-group">
                                                    <label>Priority<span class="text-danger">*</span></label>
                                                    <select name="priority" id="priority" class="form-control select2" required>
                                                        <option value="">เลือก</option>
                                                        <?php foreach ($priorityOptions as $priority): ?>
                                                            <option value="<?php echo escapeOutput($priority); ?>" <?php echo $priority === $defaultPriority ? 'selected' : ''; ?>><?php echo escapeOutput($priority); ?></option>
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
                                                            <option value="<?php echo escapeOutput($channel); ?>" <?php echo $channel === 'Office' ? 'selected' : ''; ?>><?php echo escapeOutput($channel); ?></option>
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
                                                            <option value="<?php echo escapeOutput($urgency); ?>" <?php echo $urgency === $defaultUrgency ? 'selected' : ''; ?>><?php echo escapeOutput($urgency); ?></option>
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
                                                            <option value="<?php echo escapeOutput($impact); ?>" <?php echo $impact === $defaultImpact ? 'selected' : ''; ?>><?php echo escapeOutput($impact); ?></option>
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
                                                            <option value="<?php echo escapeOutput($status); ?>" <?php echo $status === $defaultStatus ? 'selected' : ''; ?>><?php echo escapeOutput($status); ?></option>
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
                                                            <option value="<?php echo escapeOutput($category['service_category']); ?>"><?php echo escapeOutput($category['service_category']); ?></option>
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
                                                            <option value="<?php echo escapeOutput($cat['category']); ?>"><?php echo escapeOutput($cat['category']); ?></option>
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
                                                            <option value="<?php echo escapeOutput($subCat['sub_category']); ?>"><?php echo escapeOutput($subCat['sub_category']); ?></option>
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
                                                            <option value="<?php echo escapeOutput($source); ?>" <?php echo $source === $defaultSource ? 'selected' : ''; ?>><?php echo escapeOutput($source); ?></option>
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
                                                            <option value="<?php echo escapeOutput($reporter['user_id']); ?>"><?php echo escapeOutput($reporter['full_name']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>ผู้เกี่ยวข้องเพิ่มเติม (Watcher)</label>
                                                    <select name="watchers[]" class="form-control select2" multiple data-placeholder="เลือกผู้เกี่ยวข้อง">
                                                        <?php foreach ($owners as $owner): ?>
                                                            <option value="<?php echo escapeOutput($owner['user_id']); ?>"><?php echo escapeOutput($owner['full_name']); ?></option>
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
                                                    <input type="datetime-local" name="start_at" id="start_at" class="form-control" value="<?php echo escapeOutput($defaultStartAt); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>กำหนดแล้วเสร็จ (วันเวลา)</label>
                                                    <input type="datetime-local" name="due_at" id="due_at" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Subject and Description -->
                                        <div class="form-group">
                                            <label>หัวข้อ / Subject<span class="text-danger">*</span></label>
                                            <input type="text" name="subject" id="subject" class="form-control" placeholder="สรุปเหตุการณ์หรือคำขอ" maxlength="150" required>
                                            <small class="text-muted">จำกัดไม่เกิน 150 ตัวอักษร</small>
                                        </div>

                                        <div class="form-group">
                                            <label>รายละเอียดงาน / Symptom</label>
                                            <textarea name="description" id="description" rows="4" class="form-control" placeholder="ระบุรายละเอียด ปัญหา หรือความต้องการของผู้ใช้งาน"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Attachments Card -->
                                <div class="card card-outline card-info mb-4">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-paperclip mr-2"></i>Attachments (Job Details) - ได้มากกว่า 1 ไฟล์</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="dropzone-area border-dashed border-2 border-primary p-4 text-center rounded">
                                            <div class="mb-3">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                                            </div>
                                            <p class="mb-2">ลาก & วางไฟล์ที่เกี่ยวข้อง หรือคลิกเพื่อเลือกไฟล์</p>
                                            <small class="text-muted">รองรับไฟล์ JPG, PNG, PDF, DOCX, XLSX ขนาดรวมไม่เกิน 50 MB</small>
                                            <div class="mt-3">
                                                <input type="file" name="attachments[]" multiple class="form-control-file d-none" id="attachmentFiles" accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx">
                                                <label for="attachmentFiles" class="btn btn-outline-primary">
                                                    <i class="fas fa-file-upload mr-2"></i>เลือกไฟล์
                                                </label>
                                            </div>
                                            <div id="selectedFiles" class="mt-3"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Onsite Details Card -->
                                <div class="card card-outline card-warning mb-4 d-none" id="onsiteDetailsCard">
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
                                                    <input type="text" name="onsite_start_location" id="onsite_start_location" class="form-control" placeholder="เช่น สำนักงานใหญ่ บางนา">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-stop-circle text-danger mr-1"></i>สถานที่ปลายทาง / Site End</label>
                                                    <input type="text" name="onsite_end_location" id="onsite_end_location" class="form-control" placeholder="เช่น ศูนย์บริการ ขอนแก่น">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label><i class="fas fa-car text-primary mr-1"></i>เดินทางโดย<span class="text-danger">*</span></label>
                                                    <select name="onsite_travel_mode" id="onsite_travel_mode" class="form-control select2" required>
                                                        <option value="">เลือกวิธีการเดินทาง</option>
                                                        <optgroup label="รถยนต์">
                                                            <option value="personal_car" data-needs-mileage="1">รถส่วนตัว</option>
                                                            <option value="company_car" data-needs-mileage="1">รถบริษัท</option>
                                                            <option value="taxi">แท็กซี่ / รถรับจ้าง</option>
                                                        </optgroup>
                                                        <optgroup label="ขนส่งสาธารณะ">
                                                            <option value="electric_train">รถไฟฟ้า (BTS/MRT)</option>
                                                            <option value="bus">รถโดยสารประจำทาง</option>
                                                            <option value="van">รถตู้โดยสาร</option>
                                                            <option value="train">รถไฟ</option>
                                                            <option value="boat">เรือโดยสาร</option>
                                                        </optgroup>
                                                        <optgroup label="เครื่องบิน">
                                                            <option value="plane">เครื่องบิน</option>
                                                        </optgroup>
                                                        <optgroup label="อื่นๆ">
                                                            <option value="others_mileage" data-needs-mileage="1">อื่นๆ (ต้องบันทึกเลขไมล์)</option>
                                                            <option value="others">อื่นๆ (ไม่ต้องบันทึกเลขไมล์)</option>
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
                                                    <input type="number" step="0.1" min="0" name="onsite_odometer_start" id="onsite_odometer_start" class="form-control" placeholder="เช่น 10351.5">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><i class="fas fa-tachometer-alt text-warning mr-1"></i>เลขไมล์จุดสิ้นสุด</label>
                                                    <input type="number" step="0.1" min="0" name="onsite_odometer_end" id="onsite_odometer_end" class="form-control" placeholder="เช่น 10980.2">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ฟิลด์รายละเอียดเพิ่มเติมสำหรับพาหนะอื่นๆ -->
                                        <div class="row d-none" id="onsiteOtherRow">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label><i class="fas fa-info-circle text-info mr-1"></i>รายละเอียดพาหนะเพิ่มเติม</label>
                                                    <input type="text" name="onsite_travel_note" id="onsite_travel_note" class="form-control" placeholder="ระบุพาหนะ เช่น รถเช่า จังหวัดเชียงใหม่">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label><i class="fas fa-sticky-note text-secondary mr-1"></i>หมายเหตุเพิ่มเติม</label>
                                            <textarea name="onsite_note" id="onsite_note" rows="3" class="form-control" placeholder="รายละเอียดเพิ่มเติมที่ทีมควรรับทราบ เช่น นัดหมายติดต่อผู้ประสานงาน หรือข้อมูลเบิกจ่าย"></textarea>
                                        </div>
                                    </div>
                                </div>


                                <!-- Action Buttons -->
                                <div class="card card-outline card-success mb-4">
                                    <div class="card-body text-center py-3">
                                        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-2">
                                            <button type="reset" class="btn btn-outline-secondary btn-sm px-3">
                                                <i class="fas fa-undo mr-1"></i>ล้างฟอร์ม
                                            </button>
                                            <button type="button" class="btn btn-warning btn-sm px-3" id="btnDraft">
                                                <i class="fas fa-save mr-1"></i>บันทึก Draft
                                            </button>
                                            <button type="submit" class="btn btn-success btn-sm px-3">
                                                <i class="fas fa-paper-plane mr-1"></i>สร้าง Ticket
                                            </button>
                                        </div>
                                        <small class="text-muted mt-2 d-block">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            กรุณาตรวจสอบข้อมูลก่อนกดสร้าง Ticket
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

            const $channelSelect = $('#channel');
            const $onsiteCard = $('#onsiteDetailsCard');
            const $onsiteTravelMode = $('#onsite_travel_mode');
            const $onsiteMileageRow = $('#onsiteMileageRow');
            const $onsiteOtherRow = $('#onsiteOtherRow');

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
                const selectedChannel = $channelSelect.val();
                console.log('Selected channel:', selectedChannel); // Debug log

                if (selectedChannel === 'Onsite') {
                    $onsiteCard.removeClass('d-none');
                    // Make travel mode required when onsite is selected
                    $onsiteTravelMode.prop('required', true);
                } else {
                    $onsiteCard.addClass('d-none');
                    $onsiteMileageRow.addClass('d-none');
                    $onsiteOtherRow.addClass('d-none');
                    // Remove required attribute when not onsite
                    $onsiteTravelMode.prop('required', false);
                    // Clear values when hiding
                    $onsiteTravelMode.val('').trigger('change');
                    $('#onsite_start_location, #onsite_end_location, #onsite_travel_note, #onsite_odometer_start, #onsite_odometer_end, #onsite_note').val('');
                }
            }


            $channelSelect.on('change', function() {
                console.log('Channel changed to:', $(this).val());
                updateOnsiteCardVisibility();
            });

            $onsiteTravelMode.on('change', function() {
                updateOnsiteTravelMode();
            });

            $statusSelect.on('change', function() {
                updateStatusDependentFields();
            });

            // Initialize visibility states
            updateOnsiteCardVisibility();
            updateOnsiteTravelMode();

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

            // Real-time field validation
            $('input[required], select[required], textarea[required]').on('blur change', function() {
                const $field = $(this);
                const value = $field.val();

                if (!value || (Array.isArray(value) && value.length === 0)) {
                    $field.addClass('is-invalid');
                } else {
                    $field.removeClass('is-invalid');
                }
            });
        });
    </script>
</body>

</html>
