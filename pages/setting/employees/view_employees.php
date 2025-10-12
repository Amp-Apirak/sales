<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
session_start();
include('../../../config/condb.php');
require_once __DIR__ . '/../../../config/validation.php';

$csrf_token = generateCSRFToken();

// ตรวจสอบว่ามีการส่ง id มาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: employees.php");
    exit();
}

// ถอดรหัส id
$employee_id = decryptUserId($_GET['id']);

// ดึงข้อมูลพนักงานและข้อมูลที่เกี่ยวข้อง
try {
    $stmt = $condb->prepare("
        SELECT e.*, 
               t.team_name,
               c.first_name as supervisor_fname, 
               c.last_name as supervisor_lname,
               u.first_name as creator_fname, 
               u.last_name as creator_lname
        FROM employees e
        LEFT JOIN teams t ON e.team_id = t.team_id
        LEFT JOIN users c ON e.supervisor_id = c.user_id 
        LEFT JOIN users u ON e.created_by = u.user_id
        WHERE e.id = :id
    ");
    $stmt->bindParam(':id', $employee_id, PDO::PARAM_STR);
    $stmt->execute();
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        header("Location: employees.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// กำหนด path ของรูปภาพ
$image_path = '';
if (!empty($employee['profile_image'])) {
    $image_path = BASE_URL . 'uploads/employee_images/' . $employee['profile_image'];
} else {
    $image_path = BASE_URL . 'assets/img/pitt.png';
}

// ตรวจสอบสิทธิ์การเข้าถึงเอกสาร (RBAC)
$canAccessDocuments = false;
$canManageDocuments = false;
$canDelete = false; // สิทธิ์ในการลบ
$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';
$team_id = $_SESSION['team_id'] ?? '';
$employee_team_id = $employee['team_id'];

if ($role === 'Executive') {
    // Executive: เข้าถึงทุกคน, จัดการได้ทั้งหมด (รวมลบ)
    $canAccessDocuments = true;
    $canManageDocuments = true;
    $canDelete = true;

} elseif ($role === 'Account Management') {
    // Account Management: เข้าถึงเฉพาะทีมเดียวกัน, จัดการได้ทั้งหมด (รวมลบ)
    $supervisor_team_ids = $_SESSION['team_ids'] ?? [];

    if ($team_id === 'ALL') {
        $canAccessDocuments = in_array($employee_team_id, $supervisor_team_ids);
    } else {
        $canAccessDocuments = ($employee_team_id === $team_id);
    }
    $canManageDocuments = $canAccessDocuments;
    $canDelete = $canAccessDocuments;

} elseif ($role === 'Sale Supervisor') {
    // Sale Supervisor: เข้าถึงเฉพาะทีมเดียวกัน, จัดการได้ (ยกเว้นลบ)
    $supervisor_team_ids = $_SESSION['team_ids'] ?? [];

    if ($team_id === 'ALL') {
        $canAccessDocuments = in_array($employee_team_id, $supervisor_team_ids);
    } else {
        $canAccessDocuments = ($employee_team_id === $team_id);
    }
    $canManageDocuments = $canAccessDocuments;
    $canDelete = false; // Sale Supervisor ไม่สามารถลบได้

} elseif ($role === 'Seller' || $role === 'Engineer') {
    // Seller/Engineer: เข้าถึงเฉพาะเอกสารของตัวเอง, จัดการได้ทั้งหมด (รวมลบ)
    $isOwnProfile = ($employee_id === $user_id);
    $canAccessDocuments = $isOwnProfile;
    $canManageDocuments = $isOwnProfile;
    $canDelete = $isOwnProfile;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "employees"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | View Employee</title>
    <?php include '../../../include/header.php'; ?>
    <style>
        .employee-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #3c8dbc;
            cursor: pointer;
        }

        .btn-edit {
            transition: all 0.3s;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Image Modal (ใช้ชื่อเฉพาะเพื่อไม่ conflict กับ Bootstrap) */
        .image-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.9);
        }

        .image-modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        .image-modal .close-image {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
        }

        .image-modal .close-image:hover,
        .image-modal .close-image:focus {
            color: #bbb;
            text-decoration: none;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include '../../../include/navbar.php'; ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Employee Details</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="employees.php">Employees</a></li>
                                <li class="breadcrumb-item active">View Employee</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-primary card-outline h-100">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <img class="employee-image" src="<?php echo $image_path; ?>" alt="Employee Image" id="employeeImage">
                                    </div>
                                    <h3 class="profile-username text-center mt-3">
                                        <?php echo htmlspecialchars($employee['first_name_th'] . ' ' . $employee['last_name_th']); ?>
                                    </h3>
                                    <p class="text-muted text-center">
                                        <?php echo htmlspecialchars($employee['position']); ?>
                                    </p>
                                    <p class="text-center">
                                        <?php echo htmlspecialchars($employee['team_name'] ?? 'No Team Assigned'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card card-primary card-outline h-100">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <ul class="nav nav-pills">
                                            <li class="nav-item">
                                                <a class="nav-link active" href="#employee-info" data-toggle="tab">
                                                    <i class="fas fa-info-circle"></i> ข้อมูลทั่วไป
                                                </a>
                                            </li>
                                            <?php if ($canAccessDocuments): ?>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#employee-documents" data-toggle="tab">
                                                    <i class="fas fa-file-alt"></i> เอกสารแนบ
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#employee-links" data-toggle="tab">
                                                    <i class="fas fa-link"></i> ลิงก์เอกสาร
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                        <div>
                                            <a href="edit_employees.php?id=<?php echo urlencode($_GET['id']); ?>" class="btn btn-primary btn-sm btn-edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="tab-content">
                                        <!-- Tab: Employee Information -->
                                        <div class="tab-pane active" id="employee-info">
                                            <ul class="list-group list-group-flush">
                                                <!-- ข้อมูลส่วนตัว -->
                                                <li class="list-group-item">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1"><i class="fas fa-user text-primary mr-2"></i>Personal Information</h6>
                                                    </div>
                                                    <p class="mb-1"><small class="text-muted">ชื่อ-สกุล (TH):</small> <?php echo htmlspecialchars($employee['first_name_th'] . ' ' . $employee['last_name_th']); ?></p>
                                                    <p class="mb-1"><small class="text-muted">Name-Surname (EN):</small> <?php echo htmlspecialchars($employee['first_name_en'] . ' ' . $employee['last_name_en']); ?></p>
                                                    <p class="mb-1"><small class="text-muted">ชื่อเล่น:</small> <?php echo htmlspecialchars($employee['nickname_th']); ?> (<?php echo htmlspecialchars($employee['nickname_en']); ?>)</p>
                                                    <p class="mb-1"><small class="text-muted">Gender:</small> <?php echo htmlspecialchars(ucfirst($employee['gender'])); ?></p>
                                                    <p class="mb-1"><small class="text-muted">Birth Date:</small> <?php echo $employee['birth_date'] ? date('d/m/Y', strtotime($employee['birth_date'])) : '-'; ?></p>
                                                </li>

                                                <!-- ข้อมูลการติดต่อ -->
                                                <li class="list-group-item">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1"><i class="fas fa-address-card text-success mr-2"></i>Contact Information</h6>
                                                    </div>
                                                    <p class="mb-1"><small class="text-muted">Email (Personal):</small> <?php echo htmlspecialchars($employee['personal_email']); ?></p>
                                                    <p class="mb-1"><small class="text-muted">Email (Company):</small> <?php echo htmlspecialchars($employee['company_email']); ?></p>
                                                    <p class="mb-1"><small class="text-muted">Phone:</small> <?php echo htmlspecialchars($employee['phone']); ?></p>
                                                    <p class="mb-1"><small class="text-muted">Address:</small> <?php echo nl2br(htmlspecialchars($employee['address'])); ?></p>
                                                </li>

                                                <!-- ข้อมูลการทำงาน -->
                                                <li class="list-group-item">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1"><i class="fas fa-briefcase text-info mr-2"></i>Work Information</h6>
                                                    </div>
                                                    <p class="mb-1"><small class="text-muted">Department:</small> <?php echo htmlspecialchars($employee['department']); ?></p>
                                                    <p class="mb-1"><small class="text-muted">Position:</small> <?php echo htmlspecialchars($employee['position']); ?></p>
                                                    <p class="mb-1"><small class="text-muted">Team:</small> <?php echo htmlspecialchars($employee['team_name'] ?? 'Not Assigned'); ?></p>
                                                    <p class="mb-1"><small class="text-muted">Supervisor:</small>
                                                        <?php
                                                        echo $employee['supervisor_fname']
                                                            ? htmlspecialchars($employee['supervisor_fname'] . ' ' . $employee['supervisor_lname'])
                                                            : 'Not Assigned';
                                                        ?>
                                                    </p>
                                                    <p class="mb-1"><small class="text-muted">Hire Date:</small>
                                                        <?php echo $employee['hire_date'] ? date('d/m/Y', strtotime($employee['hire_date'])) : '-'; ?>
                                                    </p>
                                                </li>

                                                <!-- ข้อมูลระบบ -->
                                                <li class="list-group-item">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1"><i class="fas fa-info-circle text-warning mr-2"></i>System Information</h6>
                                                    </div>
                                                    <p class="mb-1"><small class="text-muted">Created by:</small>
                                                        <?php echo htmlspecialchars($employee['creator_fname'] . ' ' . $employee['creator_lname']); ?>
                                                    </p>
                                                    <p class="mb-1"><small class="text-muted">Created on:</small>
                                                        <?php echo date('d/m/Y H:i:s', strtotime($employee['created_at'])); ?>
                                                    </p>
                                                </li>
                                            </ul>
                                        </div>

                                        <?php if ($canAccessDocuments): ?>
                                        <!-- Tab: Documents -->
                                        <div class="tab-pane" id="employee-documents">
                                            <div class="p-3">
                                                <div class="mb-3">
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadDocumentModal">
                                                        <i class="fas fa-upload"></i> อัปโหลดเอกสาร
                                                    </button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table id="documentsTable" class="table table-bordered table-striped table-hover">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th width="5%">#</th>
                                                                <th width="15%">หมวดหมู่</th>
                                                                <th width="25%">ชื่อเอกสาร</th>
                                                                <th width="10%">ขนาดไฟล์</th>
                                                                <th width="15%">วันที่อัปโหลด</th>
                                                                <th width="15%">ผู้อัปโหลด</th>
                                                                <th width="15%">จัดการ</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tab: Links -->
                                        <div class="tab-pane" id="employee-links">
                                            <div class="p-3">
                                                <div class="mb-3">
                                                    <button type="button" class="btn btn-primary" onclick="openAddLinkModal()">
                                                        <i class="fas fa-plus"></i> เพิ่มลิงก์เอกสาร
                                                    </button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table id="linksTable" class="table table-bordered table-striped table-hover">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th width="5%">#</th>
                                                                <th width="15%">หมวดหมู่</th>
                                                                <th width="35%">ลิงก์</th>
                                                                <th width="15%">วันที่สร้าง</th>
                                                                <th width="15%">ผู้สร้าง</th>
                                                                <th width="15%">จัดการ</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include '../../../include/footer.php'; ?>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="image-modal">
        <span class="close-image">&times;</span>
        <img class="image-modal-content" id="img01">
    </div>

    <?php if ($canAccessDocuments): ?>
        <!-- Include Document Modals -->
        <?php include 'tab_document/document.php'; ?>
        <?php include 'tab_linkdocument/link_document.php'; ?>
    <?php endif; ?>

    <script>
        // Image modal functionality
        var imageModal = document.getElementById("imageModal");
        var img = document.getElementById("employeeImage");
        var modalImg = document.getElementById("img01");

        img.onclick = function() {
            imageModal.style.display = "block";
            modalImg.src = this.src;
        }

        var closeImageBtn = document.querySelector(".close-image");

        closeImageBtn.onclick = function() {
            imageModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == imageModal) {
                imageModal.style.display = "none";
            }
        }

        <?php if ($canAccessDocuments): ?>
        // ตัวแปร PHP สำหรับ JavaScript
        var canDelete = <?php echo json_encode($canDelete); ?>;
        var csrfToken = <?php echo json_encode($csrf_token); ?>;

        // Initialize DataTables when document is ready
        $(document).ready(function() {
            // Initialize Documents DataTable
            var documentsTable = $('#documentsTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "pageLength": 10,
                "order": [[4, "desc"]], // Sort by upload date descending
                "language": {
                    "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
                    "zeroRecords": "ไม่พบข้อมูล",
                    "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
                    "infoEmpty": "ไม่มีข้อมูล",
                    "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
                    "search": "ค้นหา:",
                    "paginate": {
                        "first": "แรก",
                        "last": "สุดท้าย",
                        "next": "ถัดไป",
                        "previous": "ก่อนหน้า"
                    }
                }
            });

            // Initialize Links DataTable
            var linksTable = $('#linksTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "pageLength": 10,
                "order": [[3, "desc"]], // Sort by created date descending
                "language": {
                    "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
                    "zeroRecords": "ไม่พบข้อมูล",
                    "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
                    "infoEmpty": "ไม่มีข้อมูล",
                    "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
                    "search": "ค้นหา:",
                    "paginate": {
                        "first": "แรก",
                        "last": "สุดท้าย",
                        "next": "ถัดไป",
                        "previous": "ก่อนหน้า"
                    }
                }
            });

            // Load data when switching to document tabs
            $('a[href="#employee-documents"]').on('shown.bs.tab', function (e) {
                loadDocuments();
            });

            $('a[href="#employee-links"]').on('shown.bs.tab', function (e) {
                loadLinks();
            });

            // Load data on initial load if we're on the documents/links tab
            if (window.location.hash === '#employee-documents') {
                loadDocuments();
            } else if (window.location.hash === '#employee-links') {
                loadLinks();
            }
        });
        <?php endif; ?>
    </script>
</body>

</html>
