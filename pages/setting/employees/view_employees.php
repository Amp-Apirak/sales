<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
session_start();
include('../../../config/condb.php');

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

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.9);
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
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
                                <div class="card-body p-0 box-profile">
                                    <div class="text-right p-3">
                                        <a href="edit_employees.php?id=<?php echo urlencode($_GET['id']); ?>" class="btn btn-primary btn-sm btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </div>
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
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include '../../../include/footer.php'; ?>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="img01">
    </div>

    <script>
        // Image modal functionality
        var modal = document.getElementById("imageModal");
        var img = document.getElementById("employeeImage");
        var modalImg = document.getElementById("img01");

        img.onclick = function() {
            modal.style.display = "block";
            modalImg.src = this.src;
        }

        var span = document.getElementsByClassName("close")[0];

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>