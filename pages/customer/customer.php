<?php
include '../../include/Add_session.php';

// ดึงข้อมูลจาก session ของผู้ใช้ที่เข้าสู่ระบบ
$role = $_SESSION['role'];  // บทบาทของผู้ใช้
$team_id = $_SESSION['team_id'];  // team_id ของผู้ใช้
$user_id = $_SESSION['user_id'];  // user_id ของผู้ใช้

// รับค่าการค้นหาจากฟอร์ม (method="GET")
$search_service = isset($_GET['searchservice']) ? trim($_GET['searchservice']) : '';

// Query พื้นฐานในการดึงข้อมูลลูกค้าทั้งหมด
$sql_customers = "SELECT DISTINCT c.*, u.first_name, u.last_name, t.team_name 
                  FROM customers c
                  LEFT JOIN users u ON c.created_by = u.user_id
                  LEFT JOIN user_teams ut ON u.user_id = ut.user_id AND ut.is_primary = 1
                  LEFT JOIN teams t ON ut.team_id = t.team_id
                  WHERE 1=1";

// เพิ่มเงื่อนไขกรณีผู้ใช้เป็น Sale Supervisor หรือผู้ใช้ทั่วไป
if ($role == 'Sale Supervisor') {
    $team_ids = $_SESSION['team_ids'] ?? [];
    if (!empty($team_ids)) {
        $placeholders = implode(',', array_fill(0, count($team_ids), '?'));
        $sql_customers .= " AND u.user_id IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id IN ($placeholders))";
        $params = $team_ids;
    } else {
        // ถ้าไม่มีทีม ให้แสดงผลว่าง
        $sql_customers .= " AND 1=0";
    }
} elseif ($role == 'Seller') {
    // ผู้ใช้ทั่วไป (Seller) เห็นเฉพาะลูกค้าที่ตัวเองสร้าง
    $sql_customers .= " AND c.created_by = :user_id";
} elseif ($role != 'Executive') {
    // กรณีที่เป็นบทบาทอื่นๆ ที่ไม่ใช่ Executive
    $sql_customers .= " AND c.created_by = :user_id";
}

// เพิ่มเงื่อนไขการค้นหาข้อมูลตามที่ผู้ใช้กรอกมา
if (!empty($search_service)) {
    $sql_customers .= " AND (
        c.customer_name LIKE :search OR 
        c.position LIKE :search OR 
        c.phone LIKE :search OR 
        c.email LIKE :search OR 
        c.company LIKE :search OR 
        c.address LIKE :search OR 
        c.remark LIKE :search OR 
        c.office_phone LIKE :search OR 
        c.extension LIKE :search OR 
        u.first_name LIKE :search OR 
        u.last_name LIKE :search OR 
        CONCAT(u.first_name, ' ', u.last_name) LIKE :search OR
        t.team_name LIKE :search OR
        DATE_FORMAT(c.created_at, '%d/%m/%Y') LIKE :search OR
        DATE_FORMAT(c.created_at, '%Y-%m-%d') LIKE :search OR
        YEAR(c.created_at) LIKE :search OR
        MONTH(c.created_at) LIKE :search OR
        DAY(c.created_at) LIKE :search
    )";
}

$sql_customers .= " ORDER BY c.created_at DESC";

// เตรียม statement และ bind ค่าต่างๆ เพื่อความปลอดภัย
$stmt = $condb->prepare($sql_customers);

// ผูกค่า team_id และ user_id ตามบทบาทของผู้ใช้
if ($role == 'Sale Supervisor') {
    $stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR);
} elseif ($role == 'Seller' || $role != 'Executive') {
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
}

// ผูกค่าการค้นหากับ statement
if (!empty($search_service)) {
    $search_param = '%' . $search_service . '%';
    $stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
}

// ดำเนินการ query และดึงข้อมูล
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "customer"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Customer Management</title>
    <?php include '../../include/header.php'; ?>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    /* แก้ไข CSS ในส่วน <style>
        ของไฟล์ customer.php */ <style>.project-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .project-item {
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .project-item h6 {
            color: #007bff;
            margin-bottom: 5px;
        }

        .project-details {
            font-size: 0.9em;
            color: #666;
        }

        /* แก้ไขสีลิงก์เฉพาะ Column แรก (Customer Name) ให้เป็นสีดำ */
        #example1 tbody tr td:first-child a {
            color: #333 !important;
            text-decoration: none;
        }

        #example1 tbody tr td:first-child a:hover {
            color: #007bff !important;
            text-decoration: underline;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Customer Management</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../..">Home</a></li>
                                <li class="breadcrumb-item active">Customer</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Section Search -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title">ค้นหา</h3>
                                </div>
                                <div class="card-body">
                                    <form action="#" method="GET">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="searchservice" name="searchservice"
                                                        value="<?php echo isset($_GET['searchservice']) ? htmlspecialchars($_GET['searchservice']) : ''; ?>"
                                                        placeholder="ค้นหา...">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary" id="search" name="search">ค้นหา</button>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- //Section Search -->

                    <!-- Section ปุ่มเพิ่มข้อมูล -->
                    <div class="col-md-12 pb-3">
                        <div class="btn-group float-right">
                            <a href="add_customer.php" class="btn btn-success btn-sm">เพิ่มข้อมูลลูกค้า</a>
                            <a href="import_customer.php" class="btn btn-info btn-sm mr-2">
                                <i class="fas fa-file-import"></i> Import ข้อมูล
                            </a>
                        </div>
                    </div><br>

                    <!-- Section ตารางแสดงผล -->
                    <div class="card">
                        <div class="card-header">
                            <div class="container-fluid">
                                <h3 class="card-title">Customer List</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Position</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Company</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customers as $customer) { ?>
                                        <tr>
                                            <!-- Link ไปยังหน้า รายละเอียด -->
                                            <td class="text-nowrap" onclick="window.location.href='view_customer.php?id=<?php echo urlencode(encryptUserId($customer['customer_id'])); ?>'">
                                                <a href="view_customer.php?id=<?php echo urlencode(encryptUserId($customer['customer_id'])); ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($customer['customer_name']); ?>
                                                </a>
                                            </td>

                                            <td class="text-nowrap"><?php echo !empty($customer['position']) ? htmlspecialchars($customer['position']) : 'ไม่ระบุข้อมูล'; ?></td>
                                            <td class="text-nowrap"><?php echo htmlspecialchars($customer['phone']); ?></td>
                                            <td class="text-nowrap"><?php echo htmlspecialchars($customer['email']); ?></td>
                                            <td class="text-nowrap"><?php echo !empty($customer['company']) ? htmlspecialchars($customer['company']) : 'ไม่ระบุข้อมูล'; ?></td>
                                            <td class="text-nowrap">
                                                <?php
                                                $creator_name = trim($customer['first_name'] . ' ' . $customer['last_name']);
                                                echo !empty($creator_name) ? htmlspecialchars($creator_name) : 'ไม่ระบุข้อมูล';
                                                ?>
                                            </td>
                                            <td class="text-nowrap"><?php echo htmlspecialchars($customer['created_at']); ?></td>
                                            <td class="text-nowrap">
                                                <a href="view_customer.php?id=<?php echo urlencode(encryptUserId($customer['customer_id'])); ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit_customer.php?customer_id=<?php echo urlencode(encryptUserId($customer['customer_id'])); ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <button class="btn btn-danger btn-sm" onclick="confirmDeleteCustomer('<?php echo urlencode(encryptUserId($customer['customer_id'])); ?>', '<?php echo htmlspecialchars($customer['customer_name']); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Position</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Company</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../../include/footer.php'; ?>
    </div>

    <!-- DataTables & Plugins -->
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": false,
                "lengthChange": true,
                "autoWidth": false,
                "scrollX": true,
                "scrollCollapse": true,
                "paging": true,
                "pageLength": 20,
                "lengthMenu": [
                    [10, 20, 30, 50, 100, 200, -1],
                    [10, 20, 30, 50, 100, 200, "ทั้งหมด"]
                ],
                "order": [
                    [6, "desc"]
                ],
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
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
                    "loadingRecords": "กำลังโหลดข้อมูล..."
                }
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });

        // ฟังก์ชันยืนยันการลบลูกค้า
        function confirmDeleteCustomer(customerId, customerName) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: `คุณต้องการลบข้อมูลลูกค้า "${customerName}" ใช่หรือไม่?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบ!',
                cancelButtonText: 'ยกเลิก',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`delete_customer.php?customer_id=${customerId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    const response = result.value;

                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'สำเร็จ!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'ตกลง'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        // ตรวจสอบว่ามีโครงการที่เกี่ยวข้องหรือไม่
                        if (response.related_projects && response.related_projects.length > 0) {
                            showProjectListModal(response);
                        } else {
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'ตกลง'
                            });
                        }
                    }
                }
            });
        }

        // ฟังก์ชันแสดง Modal รายการโครงการที่เกี่ยวข้อง
        function showProjectListModal(response) {
            let projectListHtml = '<div class="project-list">';

            response.related_projects.forEach((project, index) => {
                // สร้าง encrypted project_id สำหรับ URL - แก้ไข path ให้ถูกต้อง
                const projectUrl = `../project/view_project.php?project_id=${encodeURIComponent(project.project_id)}`;

                projectListHtml += `
                    <div class="project-item">
                        <h6>
                            <i class="fas fa-project-diagram"></i> 
                            <span class="project-link" data-url="${projectUrl}" title="คลิกเพื่อดูรายละเอียดโครงการ">
                                ${project.project_name}
                                <i class="fas fa-external-link-alt ml-1" style="font-size: 0.8em;"></i>
                            </span>
                        </h6>
                        <div class="project-details">
                            <div><strong>เลขที่สัญญา:</strong> ${project.contract_no || 'ไม่ระบุ'}</div>
                            <div><strong>สถานะ:</strong> <span class="badge badge-info">${project.status}</span></div>
                            <div><strong>วันที่สร้าง:</strong> ${project.created_date}</div>
                        </div>
                    </div>
                `;
            });

            projectListHtml += '</div>';

            Swal.fire({
                title: '<i class="fas fa-exclamation-triangle text-warning"></i> ไม่สามารถลบลูกค้าได้',
                html: `
                    <div class="text-left">
                        <p class="mb-3">${response.message}</p>
                        <p class="mb-3"><strong>${response.details}</strong></p>
                        <p class="mb-2">พบ <strong>${response.project_count}</strong> โครงการที่เกี่ยวข้อง:</p>
                        ${projectListHtml}
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> 
                            <strong>คลิกที่ชื่อโครงการ</strong> เพื่อดูรายละเอียดและแก้ไขข้อมูลโครงการ<br>
                            หรือติดต่อผู้ดูแลระบบเพื่อความช่วยเหลือ
                        </div>
                    </div>
                `,
                icon: 'warning',
                width: '700px',
                confirmButtonText: 'เข้าใจแล้ว',
                confirmButtonColor: '#3085d6',
                customClass: {
                    popup: 'swal-wide'
                },
                didOpen: () => {
                    // เพิ่ม event listener สำหรับลิงก์โครงการหลังจาก modal เปิดแล้ว
                    document.querySelectorAll('.project-link').forEach(link => {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            const url = this.getAttribute('data-url');
                            // เปิด tab ใหม่
                            window.open(url, '_blank');
                        });
                    });
                }
            });
        }
    </script>

    <style>
        .swal-wide {
            max-width: 90% !important;
        }

        .swal2-html-container {
            max-height: 400px;
            overflow-y: auto;
        }

        .project-list {
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            background-color: #f8f9fa;
        }

        .project-item {
            padding: 10px;
            border: 1px solid #e9ecef;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .project-item:last-child {
            margin-bottom: 0;
        }

        .project-item h6 {
            color: #007bff;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .project-link {
            color: #007bff !important;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-block;
        }

        .project-link:hover {
            color: #0056b3 !important;
            text-decoration: underline;
            cursor: pointer;
        }

        .project-link:hover .fa-external-link-alt {
            transform: translateX(2px);
        }

        .project-details {
            font-size: 0.9em;
            color: #666;
        }

        .project-details div {
            margin-bottom: 3px;
        }

        .badge {
            font-size: 0.8em;
        }
    </style>
</body>

</html>