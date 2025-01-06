<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// ตรวจสอบการตั้งค่า Session เพื่อป้องกันกรณีที่ไม่ได้ล็อกอิน
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    // กรณีไม่มีการตั้งค่า Session หรือล็อกอิน
    header("Location: " . BASE_URL . "login.php");  // Redirect ไปยังหน้า login.php
    exit; // หยุดการทำงานของสคริปต์ปัจจุบันหลังจาก redirect
}

// ตรวจสอบว่า User ได้ login แล้วหรือยัง และตรวจสอบ Role
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Executive' && $_SESSION['role'] != 'Sale Supervisor' && $_SESSION['role'] != 'Engineer'  && $_SESSION['role'] != 'Seller')) {
    // ถ้า Role ไม่ใช่ Executive หรือ Sale Supervisor ให้ redirect ไปยังหน้าอื่น เช่น หน้า Dashboard
    header("Location: " . BASE_URL . "index.php"); // เปลี่ยนเส้นทางไปหน้า Dashboard
    exit(); // หยุดการทำงานของสคริปต์
}

// ดึงข้อมูลจาก session
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];


// ดึงข้อมูลจากฟอร์มค้นหา
$search = isset($_POST['searchservice']) ? $_POST['searchservice'] : '';
$search_team = isset($_POST['team']) ? $_POST['team'] : '';
$search_position = isset($_POST['position']) ? $_POST['position'] : '';
$search_supervisor = isset($_POST['supervisor']) ? $_POST['supervisor'] : '';

// การดึงข้อมูลจากฐานข้อมูลแต่ละคอลัมน์เพื่อสร้างตัวเลือก
// สำหรับทีม (Team)
$sql_team = "SELECT DISTINCT team_name FROM teams";
$query_team = $condb->query($sql_team);

// สำหรับตำแหน่ง (Position)
$sql_position = "SELECT DISTINCT position FROM employees";
$query_position = $condb->query($sql_position);

// สำหรับหัวหน้า (Supervisor)
$sql_supervisor = "SELECT id, CONCAT(first_name_en, ' ', last_name_en) AS full_name FROM employees";
$query_supervisor = $condb->query($sql_supervisor);

// สร้าง SQL Query โดยพิจารณาจากการค้นหา
$sql_employees = "SELECT e.id, e.first_name_th, e.last_name_th, e.first_name_en, e.last_name_en, 
                  e.nickname_th, e.nickname_en, e.position, t.team_name, 
                  u.first_name as supervisor_first_name, u.last_name as supervisor_last_name,
                  e.phone, e.personal_email, e.company_email, e.created_at
                  FROM employees e
                  LEFT JOIN teams t ON e.team_id = t.team_id
                  LEFT JOIN users u ON e.supervisor_id = u.user_id
                  WHERE 1=1";

// เพิ่มเงื่อนไขการค้นหาตามฟิลด์ที่ระบุ
if (!empty($search)) {
    $sql_employees .= " AND (e.first_name_th LIKE :search OR e.last_name_th LIKE :search OR e.first_name_en LIKE :search OR e.last_name_en LIKE :search OR e.nickname_th LIKE :search OR e.nickname_en LIKE :search OR e.phone LIKE :search)";
}
if (!empty($search_team)) {
    $sql_employees .= " AND t.team_name = :search_team";
}
if (!empty($search_position)) {
    $sql_employees .= " AND e.position = :search_position";
}
if (!empty($search_supervisor)) {
    $sql_employees .= " AND e.supervisor_id = :search_supervisor";
}

$sql_employees .= " ORDER BY e.created_at DESC";

// เตรียม statement
$stmt = $condb->prepare($sql_employees);

// ทำการ bind ค่าต่างๆ
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param);
}
if (!empty($search_team)) {
    $stmt->bindParam(':search_team', $search_team);
}
if (!empty($search_position)) {
    $stmt->bindParam(':search_position', $search_position);
}
if (!empty($search_supervisor)) {
    $stmt->bindParam(':search_supervisor', $search_supervisor);
}

// Execute query
$stmt->execute();
$query_employees = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "employees"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Employees Management</title>
    <?php include  '../../../include/header.php'; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <style>
        th,
        h1 {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 700;
            font-size: 14px;
            color: #333;
        }

        .custom-th {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 600;
            font-size: 18px;
            color: #FF5733;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include  '../../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Employees Management</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Employees Management</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">

                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-check"></i> สำเร็จ!</h5>
                                    <?php
                                    echo $_SESSION['success'];
                                    unset($_SESSION['success']);
                                    ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-ban"></i> ผิดพลาด!</h5>
                                    <?php
                                    echo $_SESSION['error'];
                                    unset($_SESSION['error']);
                                    ?>
                                </div>
                            <?php endif; ?>

                            <section class="content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-info">
                                            <div class="card-header ">
                                                <h3 class="card-title">ค้นหา</h3>
                                            </div>
                                            <div class="card-body">
                                                <form action="#" method="POST">
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="searchservice" value="<?php echo htmlspecialchars($search); ?>" placeholder="ค้นหา...">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <button type="submit" class="btn btn-primary">ค้นหา</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label>ทีม</label>
                                                                <select class="custom-select select2" name="team">
                                                                    <option value="">เลือก</option>
                                                                    <?php while ($team = $query_team->fetch()) { ?>
                                                                        <option value="<?php echo htmlspecialchars($team['team_name']); ?>"><?php echo htmlspecialchars($team['team_name']); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label>ตำแหน่ง</label>
                                                                <select class="custom-select select2" name="position">
                                                                    <option value="">เลือก</option>
                                                                    <?php while ($position = $query_position->fetch()) { ?>
                                                                        <option value="<?php echo htmlspecialchars($position['position']); ?>"><?php echo htmlspecialchars($position['position']); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label>หัวหน้า</label>
                                                                <select class="custom-select select2" name="supervisor">
                                                                    <option value="">เลือก</option>
                                                                    <?php while ($supervisor = $query_supervisor->fetch()) { ?>
                                                                        <option value="<?php echo htmlspecialchars($supervisor['id']); ?>"><?php echo htmlspecialchars($supervisor['full_name']); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                            </section>

                            <div class="col-md-12 pb-3">
                                <div class="btn-group float-right">
                                    <a href="add_employees.php" class="btn btn-success btn-sm">เพิ่มข้อมูลพนักงาน</a>
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#importModal">
                                        นำเข้าข้อมูล Excel/CSV
                                    </button>
                                </div>
                            </div><br>

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Employees Management</h3>
                                </div>
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap text-center">ชื่อ-นามสกุล (TH)</th>
                                                <th class="text-nowrap text-center">ชื่อ-นามสกุล (EN)</th>
                                                <th class="text-nowrap text-center">ชื่อเล่น (TH)</th>
                                                <th class="text-nowrap text-center">ชื่อเล่น (EN)</th>
                                                <th class="text-nowrap text-center">ตำแหน่ง</th>
                                                <th class="text-nowrap text-center">ทีม</th>
                                                <th class="text-nowrap text-center">หัวหน้า</th>
                                                <th class="text-nowrap text-center">เบอร์โทรศัพท์</th>
                                                <th class="text-nowrap text-center">Email ส่วนตัว</th>
                                                <th class="text-nowrap text-center">Email บริษัท</th>
                                                <th class="text-nowrap text-center">วันที่สร้าง</th>
                                                <th class="text-nowrap text-center">การกระทำ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($query_employees as $employee) { ?>
                                                <tr>
                                                    <td class="text-nowrap "><?php echo htmlspecialchars($employee['first_name_th'] . ' ' . $employee['last_name_th']); ?></td>
                                                    <td class="text-nowrap "><?php echo htmlspecialchars($employee['first_name_en'] . ' ' . $employee['last_name_en']); ?></td>
                                                    <td class="text-nowrap text-center"><?php echo htmlspecialchars($employee['nickname_th']); ?></td>
                                                    <td class="text-nowrap text-center"><?php echo !empty($employee['nickname_en']) ? htmlspecialchars($employee['nickname_en']): 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap "><?php echo !empty($employee['position']) ? htmlspecialchars($employee['position']): 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap "><?php echo !empty($employee['team_name']) ? htmlspecialchars($employee['team_name']): 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap "><?php echo !empty($employee['supervisor_first_name']) ? htmlspecialchars($employee['supervisor_first_name'] . ' ' . $employee['supervisor_last_name']): 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap "><?php echo !empty($employee['phone']) ? htmlspecialchars($employee['phone']): 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap "><?php echo !empty($employee['personal_email']) ? htmlspecialchars($employee['personal_email']): 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap "><?php echo !empty($employee['company_email']) ? htmlspecialchars($employee['company_email']): 'ไม่ระบุข้อมูล'; ?></td>
                                                    <td class="text-nowrap text-center"><?php echo htmlspecialchars($employee['created_at']); ?></td>
                                                    <td class="text-nowrap text-center">
                                                        <a href="view_employees.php?id=<?php echo urlencode(encryptUserId($employee['id'])); ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="edit_employees.php?id=<?php echo urlencode(encryptUserId($employee['id'])); ?>" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include  '../../../include/footer.php'; ?>

        <script>
            $(function() {
                $("#example1").DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "order": [], // ปิดการเรียงลำดับอัตโนมัติ
                    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            });
        </script>

        <script>
            // Dropdown Select2
            $(function() {
                // Initialize Select2 Elements
                $('.select2').select2()

                // Initialize Select2 Elements with Bootstrap4 theme
                $('.select2bs4').select2({
                    theme: 'bootstrap4'
                })
            });
        </script>

        <!-- Modal สำหรับนำเข้าไฟล์:  -->
        <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="importModalLabel">
                            <i class="fas fa-file-import mr-2"></i>นำเข้าข้อมูล Employees
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="import_employees.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <!-- ส่วนแสดงขั้นตอนการนำเข้า -->
                            <div class="import-steps mb-4">
                                <div class="d-flex justify-content-between">
                                    <div class="step text-center">
                                        <a href="templates/employees_import_template.xlsx" class="text-decoration-none step-link">
                                            <div class="step-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">
                                                <i class="fas fa-download"></i>
                                            </div>
                                            <div class="step-text">
                                                <small>ขั้นตอนที่ 1</small><br>
                                                ดาวน์โหลด Template
                                            </div>
                                        </a>
                                    </div>
                                    <div class="step-line flex-grow-1 bg-light my-auto mx-2" style="height: 2px;"></div>
                                    <div class="step text-center">
                                        <div class="step-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">
                                            <i class="fas fa-file-excel"></i>
                                        </div>
                                        <div class="step-text">
                                            <small>ขั้นตอนที่ 2</small><br>
                                            กรอกข้อมูล
                                        </div>
                                    </div>
                                    <div class="step-line flex-grow-1 bg-light my-auto mx-2" style="height: 2px;"></div>
                                    <div class="step text-center">
                                        <div class="step-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">
                                            <i class="fas fa-upload"></i>
                                        </div>
                                        <div class="step-text">
                                            <small>ขั้นตอนที่ 3</small><br>
                                            อัพโหลดไฟล์
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ส่วนอัพโหลดไฟล์ -->
                            <div class="upload-section p-4 bg-light rounded">
                                <div class="text-center mb-4">
                                    <div class="upload-icon mb-3">
                                        <i class="fas fa-cloud-upload-alt text-primary" style="font-size: 48px;"></i>
                                    </div>
                                    <h6 class="font-weight-bold">อัพโหลดไฟล์ Excel/CSV</h6>
                                    <p class="text-muted small">ลากไฟล์มาวางที่นี่ หรือคลิกเพื่อเลือกไฟล์</p>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                                    <label class="custom-file-label" for="importFile">เลือกไฟล์...</label>
                                </div>
                            </div>

                            <!-- ส่วนแสดงข้อมูลและคำแนะนำ -->
                            <div class="info-section mt-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-info h-100">
                                            <div class="card-header bg-info text-white">
                                                <i class="fas fa-info-circle mr-2"></i>ข้อกำหนดไฟล์
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled mb-0">
                                                    <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>รองรับไฟล์ .xlsx, .xls และ .csv</li>
                                                    <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>ขนาดไฟล์ไม่เกิน 5MB</li>
                                                    <li><i class="fas fa-check-circle text-success mr-2"></i>ใช้ Template ที่กำหนดเท่านั้น</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-warning h-100">
                                            <div class="card-header bg-warning text-dark">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>คำแนะนำ
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled mb-0">
                                                    <li class="mb-2"><i class="fas fa-angle-right mr-2"></i>ตรวจสอบข้อมูลให้ครบถ้วน</li>
                                                    <li class="mb-2"><i class="fas fa-angle-right mr-2"></i>ห้ามแก้ไขหัวคอลัมน์ใน Template</li>
                                                    <li><i class="fas fa-angle-right mr-2"></i>บันทึกไฟล์ก่อนอัพโหลด</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times mr-2"></i>ยกเลิก
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-import mr-2"></i>นำเข้าข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <style>
            .modal-lg {
                max-width: 800px;
            }

            .upload-section {
                border: 2px dashed #dee2e6;
                transition: all 0.3s ease;
            }

            .upload-section:hover {
                border-color: #007bff;
                background-color: #f8f9fa;
            }

            .custom-file-label {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .step-text {
                font-size: 0.9rem;
                color: #6c757d;
            }

            .info-section .card {
                transition: all 0.3s ease;
            }

            .info-section .card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
        </style>

        <script>
            // แสดงชื่อไฟล์ที่เลือก
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);
            });

            // เพิ่ม drag and drop functionality
            const uploadSection = document.querySelector('.upload-section');
            const fileInput = document.querySelector('#importFile');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadSection.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadSection.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadSection.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                uploadSection.style.backgroundColor = '#e9ecef';
                uploadSection.style.borderColor = '#007bff';
            }

            function unhighlight(e) {
                uploadSection.style.backgroundColor = '#f8f9fa';
                uploadSection.style.borderColor = '#dee2e6';
            }

            uploadSection.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInput.files = files;
                let fileName = files[0].name;
                $('.custom-file-label').html(fileName);
            }
        </script>
        <!-- ./Modal สำหรับนำเข้าไฟล์:  -->
    </div>
</body>

</html>