<?php
session_start();
include('../../../config/condb.php');

// ตรวจสอบว่ามีการส่ง project_id มาหรือไม่
if (!isset($_GET['project_id'])) {
    // ถ้าไม่มี project_id ให้ redirect กลับไปหน้า project.php
    header("Location: ../project.php");
    exit;
}

// รับค่า project_id และถอดรหัส
$encrypted_id = $_GET['project_id'];
$project_id = decryptUserId($encrypted_id);

// ดึงข้อมูลโครงการ
$stmt = $condb->prepare("SELECT p.*, pr.product_name, c.customer_name, c.company 
                        FROM projects p 
                        LEFT JOIN products pr ON p.product_id = pr.product_id
                        LEFT JOIN customers c ON p.customer_id = c.customer_id
                        WHERE p.project_id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// ถ้าไม่พบข้อมูลโครงการ ให้ redirect กลับ
if (!$project) {
    header("Location: ../project.php");
    exit;
}

// ดึงข้อมูลสมาชิกในโครงการ
$stmt = $condb->prepare("SELECT pm.*, u.first_name, u.last_name, pr.role_name
                        FROM project_members pm
                        JOIN users u ON pm.user_id = u.user_id
                        JOIN project_roles pr ON pm.role_id = pr.role_id
                        WHERE pm.project_id = ?
                        ORDER BY pr.role_name, u.first_name");
$stmt->execute([$project_id]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลบทบาททั้งหมด
$stmt = $condb->prepare("SELECT * FROM project_roles ORDER BY role_name");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงรายชื่อผู้ใช้ที่ยังไม่ได้เป็นสมาชิกในโครงการ
$stmt = $condb->prepare("SELECT u.* 
                        FROM users u 
                        WHERE u.user_id NOT IN (
                            SELECT pm.user_id 
                            FROM project_members pm 
                            WHERE pm.project_id = ?
                        )
                        ORDER BY u.first_name");
$stmt->execute([$project_id]);
$available_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "project"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Project Management</title>
    <?php include  '../../../include/header.php'; ?>

    <!-- /* ใช้ฟอนต์ Noto Sans Thai กับ label */ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <style>
        /* ใช้ฟอนต์ Noto Sans Thai กับ label */
        th,
        h1 {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 700;
            /* ปรับระดับน้ำหนักของฟอนต์ */
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

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../../include/navbar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">จัดการสมาชิกโครงการ</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/">หน้าหลัก</a></li>
                                <li class="breadcrumb-item"><a href="../project.php">โครงการ</a></li>
                                <li class="breadcrumb-item active">จัดการสมาชิก</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- ข้อมูลโครงการ -->
                    <!-- <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">ข้อมูลโครงการ</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">ชื่อโครงการ:</dt>
                                        <dd class="col-sm-8"><?php echo htmlspecialchars($project['project_name']); ?></dd>

                                        <dt class="col-sm-4">ลูกค้า:</dt>
                                        <dd class="col-sm-8">
                                            <?php echo htmlspecialchars($project['customer_name'] ?? 'ไม่ระบุ'); ?>
                                            <?php if (!empty($project['company'])): ?>
                                                (<?php echo htmlspecialchars($project['company']); ?>)
                                            <?php endif; ?>
                                        </dd>

                                        <dt class="col-sm-4">ผลิตภัณฑ์:</dt>
                                        <dd class="col-sm-8"><?php echo htmlspecialchars($project['product_name'] ?? 'ไม่ระบุ'); ?></dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">สถานะ:</dt>
                                        <dd class="col-sm-8">
                                            <?php
                                            $statusClass = '';
                                            switch ($project['status']) {
                                                case 'ชนะ (Win)':
                                                    $statusClass = 'badge badge-success';
                                                    break;
                                                case 'แพ้ (Loss)':
                                                    $statusClass = 'badge badge-danger';
                                                    break;
                                                default:
                                                    $statusClass = 'badge badge-info';
                                            }
                                            ?>
                                            <span class="<?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($project['status']); ?>
                                            </span>
                                        </dd>

                                        <dt class="col-sm-4">วันที่เริ่ม:</dt>
                                        <dd class="col-sm-8"><?php echo $project['start_date'] ? date('d/m/Y', strtotime($project['start_date'])) : 'ไม่ระบุ'; ?></dd>

                                        <dt class="col-sm-4">วันที่สิ้นสุด:</dt>
                                        <dd class="col-sm-8"><?php echo $project['end_date'] ? date('d/m/Y', strtotime($project['end_date'])) : 'ไม่ระบุ'; ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <!-- ตารางแสดงสมาชิกในโครงการ -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">สมาชิกในโครงการ</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addMemberModal">
                                    <i class="fas fa-user-plus"></i> เพิ่มสมาชิก
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="membersTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>ชื่อ-นามสกุล</th>
                                        <th>บทบาท</th>
                                        <th>วันที่เข้าร่วม</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($members as $index => $member): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($member['role_name']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($member['joined_date'])); ?></td>
                                            <td>
                                                <?php if ($member['is_active']): ?>
                                                    <span class="badge badge-success">ยังเป็นสมาชิก</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">พ้นสภาพ</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm"
                                                    onclick="editMember('<?php echo $member['member_id']; ?>', 
                                                                           '<?php echo $member['role_id']; ?>', 
                                                                           <?php echo $member['is_active']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="confirmDelete('<?php echo $member['member_id']; ?>', 
                                                                             '<?php echo $member['first_name'] . ' ' . $member['last_name']; ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Modal เพิ่มสมาชิก -->
        <div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-labelledby="addMemberModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMemberModalLabel">เพิ่มสมาชิกใหม่</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="addMemberForm">
                        <div class="modal-body">
                            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                            <div class="form-group">
                                <label for="user_id">เลือกผู้ใช้</label>
                                <select class="form-control select2" name="user_id" required>
                                    <option value="">เลือกผู้ใช้</option>
                                    <?php foreach ($available_users as $user): ?>
                                        <option value="<?php echo $user['user_id']; ?>">
                                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="role_id">บทบาท</label>
                                <select class="form-control  select2" name="role_id" required>
                                    <option value="">เลือกบทบาท</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?php echo $role['role_id']; ?>">
                                            <?php echo htmlspecialchars($role['role_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal แก้ไขสมาชิก -->
        <div class="modal fade" id="editMemberModal" tabindex="-1" role="dialog" aria-labelledby="editMemberModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMemberModalLabel">แก้ไขข้อมูลสมาชิก</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editMemberForm">
                        <div class="modal-body">
                            <input type="hidden" name="member_id" id="edit_member_id">
                            <div class="form-group">
                                <label for="edit_role_id">บทบาท</label>
                                <select class="form-control" name="role_id" id="edit_role_id" required>
                                    <option value="">เลือกบทบาท</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?php echo $role['role_id']; ?>">
                                            <?php echo htmlspecialchars($role['role_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_is_active">สถานะ</label>
                                <select class="form-control" name="is_active" id="edit_is_active" required>
                                    <option value="1">ยังเป็นสมาชิก</option>
                                    <option value="0">พ้นสภาพ</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php include '../../../include/footer.php'; ?>
    </div>

    <!-- JS for Dropdown Select2 -->
    <script>
        $(function() {
            // กำหนดค่า Select2 อย่างง่าย
            $('.select2').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#addMemberModal'), // กำหนด parent เป็น modal
                allowClear: true,
                placeholder: 'เลือกผู้ใช้',
                language: {
                    noResults: function() {
                        return "ไม่พบข้อมูล";
                    },
                    searching: function() {
                        return "กำลังค้นหา...";
                    }
                }
            });

            // รีเซ็ต select2 เมื่อปิด modal
            $('#addMemberModal').on('hidden.bs.modal', function() {
                $('.select2').val(null).trigger('change');
            });
        });
    </script>

    <script>
        $(function() {
            // กำหนดค่าเริ่มต้นสำหรับ DataTable
            $("#membersTable").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#membersTable_wrapper .col-md-6:eq(0)');


            // จัดการการส่งฟอร์มเพิ่มสมาชิก
            $('#addMemberForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'add_member.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: 'เพิ่มสมาชิกเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: response.message
                            });
                        }
                    }
                });
            });

            // จัดการการส่งฟอร์มแก้ไขสมาชิก
            $('#editMemberForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'edit_member.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: 'แก้ไขข้อมูลสมาชิกเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: response.message
                            });
                        }
                    }
                });
            });
        });

        // ฟังก์ชันสำหรับเปิด modal แก้ไขสมาชิก
        function editMember(memberId, roleId, isActive) {
            $('#edit_member_id').val(memberId);
            $('#edit_role_id').val(roleId);
            $('#edit_is_active').val(isActive ? '1' : '0');
            $('#editMemberModal').modal('show');
        }

        // ฟังก์ชันยืนยันการลบสมาชิก
        function confirmDelete(memberId, memberName) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: `คุณต้องการลบ ${memberName} ออกจากโครงการใช่หรือไม่?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'delete_member.php',
                        type: 'POST',
                        data: {
                            member_id: memberId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'สำเร็จ',
                                    text: 'ลบสมาชิกเรียบร้อยแล้ว',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: response.message
                                });
                            }
                        }
                    });
                }
            });
        }
    </script>
</body>

</html>