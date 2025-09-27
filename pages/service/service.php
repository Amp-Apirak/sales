<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ข้อมูลจำลองสำหรับแสดงผลการ์ดสถานะ (ปรับแก้เมื่อเชื่อมฐานข้อมูลจริง)
$serviceMetrics = [
    [
        'title' => 'All Ticket',
        'description' => 'จำนวนงานทั้งหมด',
        'value' => 128,
        'color' => 'bg-info',
        'icon'  => 'fas fa-ticket-alt'
    ],
    [
        'title' => 'On Process',
        'description' => 'งานที่คงค้าง',
        'value' => 32,
        'color' => 'bg-warning',
        'icon'  => 'fas fa-tasks'
    ],
    [
        'title' => 'Pending',
        'description' => 'งานที่อยู่ระหว่างดำเนินการ',
        'value' => 18,
        'color' => 'bg-primary',
        'icon'  => 'fas fa-hourglass-half'
    ],
    [
        'title' => 'SAL',
        'description' => 'งานที่ตก SAL',
        'value' => 9,
        'color' => 'bg-success',
        'icon'  => 'fas fa-clipboard-check'
    ],
    [
        'title' => 'Cancal',
        'description' => 'งานที่ยกเลิก',
        'value' => 5,
        'color' => 'bg-danger',
        'icon'  => 'fas fa-times-circle'
    ],
    [
        'title' => 'จำนวนงานที่แก้ไขแล้ว',
        'description' => 'งานที่ได้รับการแก้ไขเรียบร้อย',
        'value' => 21,
        'color' => 'bg-secondary',
        'icon'  => 'fas fa-tools'
    ],
    [
        'title' => 'จำนวนที่รอยืนยัน',
        'description' => 'งานที่รอการยืนยันผล',
        'value' => 12,
        'color' => 'bg-indigo',
        'icon'  => 'fas fa-user-check'
    ],
    [
        'title' => 'จำนวนงานที่ปิดสมบูรณ์',
        'description' => 'งานที่ปิดงานแล้วทั้งหมด',
        'value' => 14,
        'color' => 'bg-teal',
        'icon'  => 'fas fa-lock'
    ],
];

// ข้อมูลตัวอย่างสำหรับตาราง Ticket (Mockup)
$mockTickets = [
    [
        'no' => 'TCK-202501',
        'type' => 'Incident',
        'service_category' => 'Network Service',
        'category' => 'Firewall',
        'sub_category' => 'Policy Update',
        'project' => 'Network Refresh 2025',
        'subject' => 'ปรับ Rule อนุญาตระบบ HR',
        'status' => 'On Process',
        'owner' => 'Supaporn N.',
        'source' => 'Email',
        'priority' => 'High',
        'urgency' => 'High',
        'impact' => 'Site',
        'sla' => '4 ชม.',
        'created_at' => '2025-02-01 09:15'
    ],
    [
        'no' => 'TCK-202502',
        'type' => 'Service',
        'service_category' => 'Application Support',
        'category' => 'CRM',
        'sub_category' => 'User Training',
        'project' => 'CRM Enablement FY25',
        'subject' => 'อบรมการใช้งาน CRM รุ่นใหม่',
        'status' => 'Pending',
        'owner' => 'Jakkrit P.',
        'source' => 'Portal',
        'priority' => 'Medium',
        'urgency' => 'Medium',
        'impact' => 'Department',
        'sla' => '3 วัน',
        'created_at' => '2025-02-01 10:40'
    ],
    [
        'no' => 'TCK-202503',
        'type' => 'Change',
        'service_category' => 'Infrastructure',
        'category' => 'Server',
        'sub_category' => 'OS Patch',
        'project' => 'Data Center Reliability',
        'subject' => 'แพตช์ Windows Server เดือน ก.พ.',
        'status' => 'Waiting for Approval',
        'owner' => 'Waranya S.',
        'source' => 'Planner',
        'priority' => 'Medium',
        'urgency' => 'Low',
        'impact' => 'Multiple Sites',
        'sla' => '7 วัน',
        'created_at' => '2025-02-01 12:00'
    ],
    [
        'no' => 'TCK-202504',
        'type' => 'Incident',
        'service_category' => 'Workspace',
        'category' => 'Printer',
        'sub_category' => 'Driver',
        'project' => 'Office Support Program',
        'subject' => 'ไม่สามารถพิมพ์จากแผนกบัญชี',
        'status' => 'In Progress',
        'owner' => 'Somchai T.',
        'source' => 'Call Center',
        'priority' => 'High',
        'urgency' => 'High',
        'impact' => 'Department',
        'sla' => '8 ชม.',
        'created_at' => '2025-02-02 08:25'
    ],
    [
        'no' => 'TCK-202505',
        'type' => 'Service',
        'service_category' => 'Database',
        'category' => 'Backup',
        'sub_category' => 'Schedule Review',
        'project' => 'Database Modernization',
        'subject' => 'ตรวจสอบตาราง Backup Oracle',
        'status' => 'Resolved',
        'owner' => 'Saranphong M.',
        'source' => 'Email',
        'priority' => 'Medium',
        'urgency' => 'Medium',
        'impact' => 'Application',
        'sla' => '2 วัน',
        'created_at' => '2025-02-02 09:50'
    ],
    [
        'no' => 'TCK-202506',
        'type' => 'Incident',
        'service_category' => 'Security',
        'category' => 'Endpoint',
        'sub_category' => 'Virus Detected',
        'project' => 'Endpoint Security Uplift',
        'subject' => 'พบ Malware บนเครื่องผู้บริหาร',
        'status' => 'On Process',
        'owner' => 'Thanakrit K.',
        'source' => 'Security Alert',
        'priority' => 'Critical',
        'urgency' => 'High',
        'impact' => 'Executive',
        'sla' => '2 ชม.',
        'created_at' => '2025-02-02 10:15'
    ],
    [
        'no' => 'TCK-202507',
        'type' => 'Change',
        'service_category' => 'Network Service',
        'category' => 'VPN',
        'sub_category' => 'Bandwidth Upgrade',
        'project' => 'Sales Connect Expansion',
        'subject' => 'ขยาย VPN สำหรับทีมขาย',
        'status' => 'Approved',
        'owner' => 'Chayathon B.',
        'source' => 'Portal',
        'priority' => 'High',
        'urgency' => 'Medium',
        'impact' => 'Remote Users',
        'sla' => '5 วัน',
        'created_at' => '2025-02-02 13:30'
    ],
    [
        'no' => 'TCK-202508',
        'type' => 'Incident',
        'service_category' => 'Application Support',
        'category' => 'ERP',
        'sub_category' => 'Login Issue',
        'project' => 'ERP Stabilization',
        'subject' => 'เข้าสู่ระบบ ERP ไม่ได้ (รหัสผิด)',
        'status' => 'Resolved Pending',
        'owner' => 'Natthapong C.',
        'source' => 'Self-Service',
        'priority' => 'Low',
        'urgency' => 'Low',
        'impact' => 'Single User',
        'sla' => '1 วัน',
        'created_at' => '2025-02-02 15:55'
    ],
    [
        'no' => 'TCK-202509',
        'type' => 'Service',
        'service_category' => 'Workspace',
        'category' => 'Laptop',
        'sub_category' => 'New Hire Setup',
        'project' => 'Onboarding Excellence',
        'subject' => 'เตรียม Laptop สำหรับพนักงานใหม่',
        'status' => 'Scheduled',
        'owner' => 'Suphakorn J.',
        'source' => 'HR Form',
        'priority' => 'Medium',
        'urgency' => 'Medium',
        'impact' => 'Single User',
        'sla' => '3 วัน',
        'created_at' => '2025-02-03 08:00'
    ],
    [
        'no' => 'TCK-202510',
        'type' => 'Incident',
        'service_category' => 'Network Service',
        'category' => 'Wi-Fi',
        'sub_category' => 'Access Point',
        'project' => 'Campus Wi-Fi Upgrade',
        'subject' => 'ชั้น 3 สัญญาณ Wi-Fi อ่อน',
        'status' => 'In Progress',
        'owner' => 'Kittisak W.',
        'source' => 'Call Center',
        'priority' => 'High',
        'urgency' => 'Medium',
        'impact' => 'Floor',
        'sla' => '6 ชม.',
        'created_at' => '2025-02-03 09:20'
    ],
    [
        'no' => 'TCK-202511',
        'type' => 'Service',
        'service_category' => 'Security',
        'category' => 'Access Control',
        'sub_category' => 'Badge Request',
        'project' => 'Security Access 2.0',
        'subject' => 'ขอสร้าง Badge ชั่วคราวสำหรับ Vendor',
        'status' => 'Pending Approval',
        'owner' => 'Narumon L.',
        'source' => 'Portal',
        'priority' => 'Low',
        'urgency' => 'Medium',
        'impact' => 'External',
        'sla' => '2 วัน',
        'created_at' => '2025-02-03 10:10'
    ],
    [
        'no' => 'TCK-202512',
        'type' => 'Change',
        'service_category' => 'Application Support',
        'category' => 'HR System',
        'sub_category' => 'Feature Enhancement',
        'project' => 'HR Digitalization Wave 3',
        'subject' => 'เพิ่มฟิลด์ OT ในระบบ HR',
        'status' => 'CAB Review',
        'owner' => 'Piyanuch Y.',
        'source' => 'Product Owner',
        'priority' => 'High',
        'urgency' => 'High',
        'impact' => 'Company',
        'sla' => '14 วัน',
        'created_at' => '2025-02-03 11:35'
    ],
    [
        'no' => 'TCK-202513',
        'type' => 'Incident',
        'service_category' => 'Database',
        'category' => 'Performance',
        'sub_category' => 'Slow Query',
        'project' => 'Sales Analytics Accelerator',
        'subject' => 'รายงานขายดึงข้อมูลช้ามาก',
        'status' => 'On Process',
        'owner' => 'Athid T.',
        'source' => 'Monitoring',
        'priority' => 'High',
        'urgency' => 'High',
        'impact' => 'Executive',
        'sla' => '4 ชม.',
        'created_at' => '2025-02-03 13:05'
    ],
    [
        'no' => 'TCK-202514',
        'type' => 'Service',
        'service_category' => 'Workspace',
        'category' => 'Accessories',
        'sub_category' => 'Docking Station',
        'project' => 'Workspace Upgrade 2025',
        'subject' => 'ขอสั่ง Docking Station เพิ่ม',
        'status' => 'Assigned',
        'owner' => 'Montira K.',
        'source' => 'Procurement',
        'priority' => 'Medium',
        'urgency' => 'Low',
        'impact' => 'Single User',
        'sla' => '5 วัน',
        'created_at' => '2025-02-03 14:20'
    ],
    [
        'no' => 'TCK-202515',
        'type' => 'Incident',
        'service_category' => 'Security',
        'category' => 'Email',
        'sub_category' => 'Phishing',
        'project' => 'Email Security Awareness',
        'subject' => 'แจ้งอีเมล Phishing ล็อกอินธนาคารdsfsfdsfdsfsfsfdsfsdfsfsdf',
        'status' => 'Containment',
        'owner' => 'Thanawat R.',
        'source' => 'User Report',
        'priority' => 'Critical',
        'urgency' => 'High',
        'impact' => 'Organization',
        'sla' => '1 ชม.',
        'created_at' => '2025-02-03 15:45'
    ]
];

$typeStyles = [
    'Incident' => ['class' => 'badge badge-pill badge-incident', 'label' => 'Incident'],
    'Service'  => ['class' => 'badge badge-pill badge-service', 'label' => 'Service'],
    'Change'   => ['class' => 'badge badge-pill badge-change', 'label' => 'Change'],
];

$statusStyles = [
    'On Process'            => ['class' => 'badge badge-pill badge-status-process'],
    'Pending'               => ['class' => 'badge badge-pill badge-status-pending'],
    'Waiting for Approval'  => ['class' => 'badge badge-pill badge-status-waiting'],
    'In Progress'           => ['class' => 'badge badge-pill badge-status-progress'],
    'Resolved'              => ['class' => 'badge badge-pill badge-status-resolved'],
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

            .badge-status-process {
                background: rgba(23, 162, 184, 0.18);
                color: #138496;
                border: 1px solid rgba(23, 162, 184, 0.25);
            }

            .badge-status-pending {
                background: rgba(255, 193, 7, 0.18);
                color: #b8860b;
                border: 1px solid rgba(255, 193, 7, 0.25);
            }

            .badge-status-waiting {
                background: rgba(108, 117, 125, 0.18);
                color: #495057;
                border: 1px solid rgba(108, 117, 125, 0.25);
            }

            .badge-status-progress {
                background: rgba(40, 167, 69, 0.18);
                color: #1e7e34;
                border: 1px solid rgba(40, 167, 69, 0.25);
            }

            .badge-status-resolved {
                background: rgba(52, 58, 64, 0.18);
                color: #343a40;
                border: 1px solid rgba(52, 58, 64, 0.25);
            }

            .badge-status-approved {
                background: rgba(0, 123, 255, 0.18);
                color: #0056b3;
                border: 1px solid rgba(0, 123, 255, 0.25);
            }

            .badge-status-resolved-pending {
                background: rgba(111, 66, 193, 0.18);
                color: #6f42c1;
                border: 1px solid rgba(111, 66, 193, 0.25);
            }

            .badge-status-scheduled {
                background: rgba(255, 159, 67, 0.18);
                color: #d35400;
                border: 1px solid rgba(255, 159, 67, 0.25);
            }

            .badge-status-pending-approval {
                background: rgba(232, 62, 140, 0.18);
                color: #b21f66;
                border: 1px solid rgba(232, 62, 140, 0.25);
            }

            .badge-status-cab-review {
                background: rgba(255, 99, 132, 0.18);
                color: #d12a5c;
                border: 1px solid rgba(255, 99, 132, 0.25);
            }

            .badge-status-assigned {
                background: rgba(54, 162, 235, 0.18);
                color: #1d7bc9;
                border: 1px solid rgba(54, 162, 235, 0.25);
            }

            .badge-status-containment {
                background: rgba(255, 87, 34, 0.18);
                color: #c0392b;
                border: 1px solid rgba(255, 87, 34, 0.25);
            }

            .badge-status-default {
                background: rgba(108, 117, 125, 0.12);
                color: #495057;
                border: 1px solid rgba(108, 117, 125, 0.2);
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
                                                Mockup Data
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
                                                <form action="#" method="POST">
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group ">
                                                                <input type="text" class="form-control " id="searchservice" name="searchservice" value="" placeholder="ค้นหา...">
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

                                                                    <option value=""></option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Job Owner</label>
                                                                <select class="custom-select select2" name="jobowner">
                                                                    <option value="">เลือก</option>

                                                                    <option value=""></option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>SLA</label>
                                                                <select class="custom-select select2" name="sla">
                                                                    <option value="">เลือก</option>

                                                                    <option value=""></option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Priority</label>
                                                                <select class="custom-select select2" name="priority">
                                                                    <option value="">เลือก</option>

                                                                    <option value=""></option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Source</label>
                                                                <select class="custom-select select2" name="source">
                                                                    <option value="">เลือก</option>

                                                                    <option value=""></option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Urgency</label>
                                                                <select class="custom-select select2" name="urgency">
                                                                    <option value="">เลือก</option>

                                                                    <option value=""></option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Impact</label>
                                                                <select class="custom-select select2" name="impact">
                                                                    <option value="">เลือก</option>

                                                                    <option value=""></option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                         <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Status</label>
                                                                <select class="custom-select select2" name="status">
                                                                    <option value="">เลือก</option>

                                                                    <option value=""></option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Service Category</label>
                                                                <select class="custom-select select2" name="servicecategory">
                                                                    <option value="">เลือก</option>

                                                                    <option value=""></option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Category</label>
                                                                <select class="custom-select select2" name="category">
                                                                    <option value="">เลือก</option>

                                                                    <option value=""></option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Sub-Category</label>
                                                                <select class="custom-select select2" name="subcategory">
                                                                    <option value="">เลือก</option>

                                                                    <option value=""></option>

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
                                    <div class="container-fluid">
                                        <h3 class="card-title">Service Ticket Overview</h3>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="serviceTickets" class="table table-bordered table-striped table-hover">
                                        <thead class="bg-light">
                                            <tr class="text-center align-middle">
                                                <th class="text-nowrap table-header-tooltip" title="เลข Ticket">No.</th>
                                                <th class="text-nowrap table-header-tooltip" title="ประเภทงาน (Incident / Service / Change)">Type</th>
                                                <th class="text-nowrap table-header-tooltip" title="หมวดหมู่บริการหลัก">Service Category</th>
                                                <th class="text-nowrap table-header-tooltip" title="หมวดหมู่รอง">Category</th>
                                                <th class="text-nowrap table-header-tooltip" title="หมวดหมู่ย่อย">Sub-Category</th>
                                                <th class="text-nowrap table-header-tooltip" title="โครงการที่เกี่ยวข้อง">Project</th>
                                                <th class="text-nowrap table-header-tooltip subject-col" title="หัวข้อของ Ticket">Subject</th>
                                                <th class="text-nowrap table-header-tooltip" title="สถานะปัจจุบันของ Ticket">Status</th>
                                                <th class="text-nowrap table-header-tooltip" title="ผู้รับผิดชอบงาน">Job Owner</th>
                                                <th class="text-nowrap table-header-tooltip" title="ช่องทางที่สร้าง Ticket">Source</th>
                                                <th class="text-nowrap table-header-tooltip" title="ระดับความสำคัญ">Priority</th>
                                                <th class="text-nowrap table-header-tooltip" title="ระดับความเร่งด่วน">Urgency</th>
                                                <th class="text-nowrap table-header-tooltip" title="ผลกระทบของเหตุการณ์">Impact</th>
                                                <th class="text-nowrap table-header-tooltip" title="ระยะเวลาตาม SLA">SLA</th>
                                                <th class="text-nowrap table-header-tooltip" title="วันที่สร้าง Ticket">Create Date</th>
                                                <th class="text-nowrap table-header-tooltip" title="จัดการ Ticket (แก้ไข/ลบ/Assign)">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($mockTickets as $ticket): ?>
                                                <?php
                                                    $typeConfig = $typeStyles[$ticket['type']] ?? ['class' => 'badge badge-pill badge-secondary', 'label' => htmlspecialchars($ticket['type'])];
                                                    $statusConfig = $statusStyles[$ticket['status']] ?? ['class' => 'badge badge-pill badge-status-default'];
                                                    [$subjectDisplay, $subjectFull] = summarizeSubject($ticket['subject']);
                                                ?>
                                                <tr>
                                                    <td class="text-nowrap text-center font-weight-bold"><?php echo htmlspecialchars($ticket['no']); ?></td>
                                                    <td class="text-nowrap text-center align-middle"><span class="badge badge-pill px-3 py-2 <?php echo $typeConfig['class']; ?>"><?php echo htmlspecialchars($typeConfig['label']); ?></span></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($ticket['service_category']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($ticket['category']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($ticket['sub_category']); ?></td>
                                                    <td class="text-nowrap" data-toggle="tooltip" data-placement="top" title="<?php echo htmlspecialchars($ticket['project'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($ticket['project']); ?></td>
                                                    <td class="subject-cell" data-toggle="tooltip" data-placement="top" title="<?php echo $subjectFull; ?>"><?php echo $subjectDisplay; ?></td>
                                                    <td class="text-nowrap text-center align-middle"><span class="badge badge-pill px-3 py-2 <?php echo $statusConfig['class']; ?>"><?php echo htmlspecialchars($ticket['status']); ?></span></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($ticket['owner']); ?></td>
                                                    <td class="text-nowrap text-center"><?php echo htmlspecialchars($ticket['source']); ?></td>
                                                    <td class="text-nowrap text-center"><?php echo htmlspecialchars($ticket['priority']); ?></td>
                                                    <td class="text-nowrap text-center"><?php echo htmlspecialchars($ticket['urgency']); ?></td>
                                                    <td class="text-nowrap text-center"><?php echo htmlspecialchars($ticket['impact']); ?></td>
                                                    <td class="text-nowrap text-center"><?php echo htmlspecialchars($ticket['sla']); ?></td>
                                                    <td class="text-nowrap text-center"><?php echo htmlspecialchars($ticket['created_at']); ?></td>
                                                    <td class="text-nowrap text-center">
                                                        <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                                            <a href="#" class="btn btn-info" title="Edit"><i class="fas fa-edit"></i></a>
                                                            <a href="#" class="btn btn-danger" title="Delete"><i class="fas fa-trash-alt"></i></a>
                                                            <a href="#" class="btn btn-primary" title="Assign To"><i class="fas fa-user-plus"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="bg-light">
                                            <tr class="text-center align-middle">
                                                <th>No.</th>
                                                <th>Type</th>
                                                <th>Service Category</th>
                                                <th>Category</th>
                                                <th>Sub-Category</th>
                                                <th>Project</th>
                                                <th>Subject</th>
                                                <th>Status</th>
                                                <th>Job Owner</th>
                                                <th>Source</th>
                                                <th>Priority</th>
                                                <th>Urgency</th>
                                                <th>Impact</th>
                                                <th>SLA</th>
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
                    { "targets": 6, "width": 520 }
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
                "stateSave": true,
                "stateDuration": 60 * 60 * 24,
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
