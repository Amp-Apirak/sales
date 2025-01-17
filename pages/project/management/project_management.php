<?php
session_start();
include('../../../config/condb.php');

// ตรวจสอบ project_id ที่ส่งมา
if (!isset($_GET['project_id'])) {
    echo "ไม่พบรหัสโครงการ";
    exit;
}

// ถอดรหัส project_id
$project_id = decryptUserId($_GET['project_id']);

// ดึงข้อมูลโครงการ
$stmt = $condb->prepare("SELECT p.*, pr.product_name 
                        FROM projects p 
                        LEFT JOIN products pr ON p.product_id = pr.product_id 
                        WHERE p.project_id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    echo "ไม่พบข้อมูลโครงการ";
    exit;
}

// ดึงข้อมูลผู้ใช้ทั้งหมดสำหรับ dropdown มอบหมายงาน
$stmt = $condb->prepare("SELECT user_id, first_name, last_name FROM users ORDER BY first_name");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "project"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Project Task Management</title>
    <?php include '../../../include/header.php'; ?>

    <!-- เพิ่ม SortableJS library -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <!-- เพิ่ม CSS สำหรับ Drag & Drop -->
    <style>
        .task-item.sortable-ghost {
            opacity: 0.5;
            background: #c8ebfb;
        }

        .task-item.sortable-drag {
            cursor: move;
        }

        .task-handle {
            cursor: move;
            padding: 5px;
            margin-right: 10px;
            color: #6c757d;
        }

        .task-handle:hover {
            color: #007bff;
        }
    </style>

    <!-- เพิ่ม CSS สำหรับ Tree View -->
    <style>
        .task-tree {
            list-style-type: none;
            padding-left: 20px;
        }

        .task-item {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .task-name {
            font-weight: bold;
        }

        .task-actions {
            display: flex;
            gap: 5px;
        }

        .progress-bar {
            height: 5px;
            background-color: #e9ecef;
            border-radius: 3px;
            margin-top: 5px;
        }

        .progress-value {
            height: 100%;
            background-color: #28a745;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
        }

        .status-Pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-InProgress {
            background-color: #17a2b8;
            color: #fff;
        }

        .status-Completed {
            background-color: #28a745;
            color: #fff;
        }

        .status-Cancelled {
            background-color: #dc3545;
            color: #fff;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../../include/navbar.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">จัดการงานโครงการ</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
                                <li class="breadcrumb-item"><a href="../project.php">โครงการ</a></li>
                                <li class="breadcrumb-item active">จัดการงาน</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Project Info Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-project-diagram mr-1"></i>
                                ข้อมูลโครงการ
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>ชื่อโครงการ:</strong> <?php echo htmlspecialchars($project['project_name']); ?></p>
                                    <p><strong>ผลิตภัณฑ์:</strong> <?php echo htmlspecialchars($project['product_name']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>วันที่เริ่ม:</strong> <?php echo $project['start_date']; ?></p>
                                    <p><strong>วันที่สิ้นสุด:</strong> <?php echo $project['end_date']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks Management Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tasks mr-1"></i>
                                รายการงาน
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" onclick="showAddTaskModal()">
                                    <i class="fas fa-plus"></i> เพิ่มงานใหม่
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="task-container">
                                <!-- Task tree will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../../../include/footer.php'; ?>
    </div>

    <!-- Modal สำหรับเพิ่ม/แก้ไขงาน -->
    <div class="modal fade" id="taskModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalTitle">เพิ่มงานใหม่</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="taskForm">
                        <input type="hidden" id="task_id" name="task_id">
                        <input type="hidden" id="parent_task_id" name="parent_task_id">
                        <div class="form-group">
                            <label>ชื่องาน</label>
                            <input type="text" class="form-control" id="task_name" name="task_name" required>
                        </div>
                        <div class="form-group">
                            <label>รายละเอียด</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>วันที่เริ่ม</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>วันที่สิ้นสุด</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>สถานะ</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="Pending">รอดำเนินการ</option>
                                        <option value="In Progress">กำลังดำเนินการ</option>
                                        <option value="Completed">เสร็จสิ้น</option>
                                        <option value="Cancelled">ยกเลิก</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>ความคืบหน้า (%)</label>
                                    <input type="number" class="form-control" id="progress" name="progress" min="0" max="100" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>ระดับความสำคัญ</label>
                            <select class="form-control" id="priority" name="priority">
                                <option value="Low">ต่ำ</option>
                                <option value="Medium">ปานกลาง</option>
                                <option value="High">สูง</option>
                                <option value="Urgent">เร่งด่วน</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>ผู้รับผิดชอบ</label>
                            <select class="form-control select2" name="assigned_users[]" multiple>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['user_id']; ?>">
                                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="saveTask()">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <!-- เพิ่ม JavaScript -->
    <script>
        $(document).ready(function() {
            loadTasks();
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        });

        function loadTasks() {
            $.ajax({
                url: 'get_tasks.php',
                type: 'GET',
                data: {
                    project_id: '<?php echo $project_id; ?>'
                },
                success: function(response) {
                    $('#task-container').html(response);
                }
            });
        }

        function showAddTaskModal(parentTaskId = null) {
            $('#taskForm')[0].reset();
            $('#task_id').val('');
            $('#parent_task_id').val(parentTaskId);
            $('#taskModalTitle').text(parentTaskId ? 'เพิ่ม Sub Task' : 'เพิ่มงานใหม่');
            $('#taskModal').modal('show');
        }

        // ฟังก์ชันอื่นๆ จะเพิ่มในขั้นตอนถัดไป

        function saveTask() {
            // แปลงข้อมูลจาก form เป็น object
            let formData = new FormData($('#taskForm')[0]);

            // แปลงข้อมูลเป็น plain object
            let formDataObj = {};
            formData.forEach((value, key) => {
                // กรณีที่เป็น assigned_users[] (multiple select)
                if (key === 'assigned_users[]') {
                    if (!formDataObj['assigned_users']) {
                        formDataObj['assigned_users'] = [];
                    }
                    formDataObj['assigned_users'].push(value);
                } else {
                    formDataObj[key] = value;
                }
            });

            // เพิ่ม project_id
            formDataObj.project_id = '<?php echo $project_id; ?>';

            // ส่งข้อมูลไปบันทึก
            $.ajax({
                url: 'save_task.php',
                type: 'POST',
                data: formDataObj,
                success: function(response) {
                    try {
                        // ตรวจสอบว่า response เป็น JSON string
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }

                        if (response.status === 'success') {
                            // ปิด modal
                            $('#taskModal').modal('hide');

                            // แสดงข้อความสำเร็จ
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // โหลดข้อมูล tasks ใหม่
                                loadTasks();
                            });
                        } else {
                            throw new Error(response.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ');
                        }
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: e.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้: ' + error
                    });
                }
            });
        }

        function deleteTask(taskId) {
            // แสดง confirmation dialog
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: 'คุณต้องการลบงานนี้และงานย่อยทั้งหมดใช่หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'delete_task.php',
                        type: 'POST',
                        data: {
                            task_id: taskId
                        },
                        success: function(response) {
                            try {
                                // ตรวจสอบว่า response เป็น JSON string
                                if (typeof response === 'string') {
                                    response = JSON.parse(response);
                                }

                                if (response.status === 'success') {
                                    // แสดงข้อความสำเร็จ
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'ลบสำเร็จ',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // โหลดข้อมูล tasks ใหม่
                                        loadTasks();
                                    });
                                } else {
                                    throw new Error(response.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ');
                                }
                            } catch (e) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: e.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้: ' + error
                            });
                        }
                    });
                }
            });
        }

        function editTask(taskId) {
            // เรียกข้อมูล task ที่ต้องการแก้ไข
            $.ajax({
                url: 'edit_task.php',
                type: 'GET',
                data: {
                    task_id: taskId
                },
                success: function(response) {
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }

                        if (response.status === 'success') {
                            const task = response.data;

                            // กำหนดค่าให้กับฟอร์ม
                            $('#task_id').val(task.task_id);
                            $('#parent_task_id').val(task.parent_task_id);
                            $('#task_name').val(task.task_name);
                            $('#description').val(task.description);
                            $('#start_date').val(task.start_date);
                            $('#end_date').val(task.end_date);
                            $('#status').val(task.status);
                            $('#progress').val(task.progress);
                            $('#priority').val(task.priority);

                            // กำหนดค่าให้ select2 multiple
                            $('#assigned_users').val(task.assigned_users).trigger('change');

                            // เปลี่ยนชื่อปุ่มและหัวข้อ Modal
                            $('#taskModalTitle').text('แก้ไขงาน');

                            // แสดง Modal
                            $('#taskModal').modal('show');
                        } else {
                            throw new Error(response.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ');
                        }
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: e.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้: ' + error
                    });
                }
            });
        }

        // แก้ไขฟังก์ชัน showAddTaskModal ให้รองรับการแก้ไข
        function showAddTaskModal(parentTaskId = null) {
            // รีเซ็ตฟอร์ม
            $('#taskForm')[0].reset();

            // รีเซ็ต select2
            $('#assigned_users').val(null).trigger('change');

            // กำหนดค่าเริ่มต้น
            $('#task_id').val(''); // เคลียร์ task_id เพื่อระบุว่าเป็นการเพิ่มใหม่
            $('#parent_task_id').val(parentTaskId);

            // กำหนดชื่อปุ่มและหัวข้อ Modal
            $('#taskModalTitle').text(parentTaskId ? 'เพิ่ม Sub Task' : 'เพิ่มงานใหม่');

            // แสดง Modal
            $('#taskModal').modal('show');
        }


        // เพิ่มฟังก์ชันสำหรับ initialize Sortable
        // อัพเดท initSortable function
        function initSortable() {
            const tbody = document.querySelector('#tasks-table tbody');
            new Sortable(tbody, {
                handle: '.task-handle',
                animation: 150,
                onEnd: function(evt) {
                    const taskId = evt.item.dataset.taskId;
                    const prevRow = evt.item.previousElementSibling;
                    const nextRow = evt.item.nextElementSibling;

                    let newLevel = 0;
                    let newParentId = null;
                    let newIndex = evt.newIndex;

                    if (prevRow) {
                        const prevLevel = parseInt(prevRow.dataset.level || 0);
                        newLevel = prevLevel;

                        // ตรวจสอบระยะห่างจากขอบซ้ายเพื่อกำหนดว่าเป็น subtask หรือไม่
                        const currentIndent = evt.item.querySelector('.task-handle').offsetLeft;
                        const prevIndent = prevRow.querySelector('.task-handle').offsetLeft;

                        if (currentIndent > prevIndent) {
                            newLevel = prevLevel + 1;
                            newParentId = prevRow.dataset.taskId;
                        }
                    }

                    // ส่งข้อมูลไปอัพเดทที่ฐานข้อมูล
                    $.ajax({
                        url: 'update_task_position.php',
                        type: 'POST',
                        data: {
                            task_id: taskId,
                            new_parent_id: newParentId,
                            new_index: newIndex,
                            new_level: newLevel
                        },
                        success: function(response) {
                            try {
                                response = typeof response === 'string' ? JSON.parse(response) : response;
                                if (response.status === 'success') {
                                    // แสดงข้อความสำเร็จ
                                    const Toast = Swal.mixin({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 1500,
                                        timerProgressBar: true
                                    });
                                    Toast.fire({
                                        icon: 'success',
                                        title: 'บันทึกตำแหน่งสำเร็จ'
                                    });
                                    // โหลดข้อมูลใหม่
                                    loadTasks();
                                } else {
                                    throw new Error(response.message);
                                }
                            } catch (e) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: e.message
                                });
                                loadTasks();
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถบันทึกตำแหน่งได้'
                            });
                            loadTasks();
                        }
                    });
                }
            });
        }

        // เพิ่มฟังก์ชันสำหรับอัพเดทตำแหน่งงาน
        function updateTaskPosition(taskId, newParentId, newIndex) {
            $.ajax({
                url: 'update_task_position.php',
                type: 'POST',
                data: {
                    task_id: taskId,
                    new_parent_id: newParentId,
                    new_index: newIndex
                },
                success: function(response) {
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }

                        if (response.status === 'success') {
                            // แสดงข้อความสำเร็จแบบเบาๆ
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true
                            });

                            Toast.fire({
                                icon: 'success',
                                title: 'อัพเดทตำแหน่งสำเร็จ'
                            });
                        } else {
                            throw new Error(response.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ');
                        }
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: e.message
                        });

                        // โหลดข้อมูลใหม่เมื่อเกิดข้อผิดพลาด
                        loadTasks();
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
                    });
                    loadTasks();
                }
            });
        }

        // เพิ่มการเรียกใช้ initSortable หลังจากโหลด tasks
        function loadTasks() {
            $.ajax({
                url: 'get_tasks.php',
                type: 'GET',
                data: {
                    project_id: '<?php echo $project_id; ?>'
                },
                success: function(response) {
                    $('#task-container').html(response);
                    // เรียกใช้ initSortable หลังจากโหลด tasks
                    initSortable();
                }
            });
        }
    </script>

    <style>
        .task-row[data-level] {
            transition: background-color 0.3s;
        }

        .task-row:hover {
            background-color: #f8f9fa;
        }

        .task-handle {
            color: #ccc;
        }

        .task-handle:hover {
            color: #666;
        }

        .toggle-subtasks {
            transition: transform 0.2s;
        }

        .toggle-subtasks.expanded {
            transform: rotate(90deg);
        }

        .btn-xs {
            padding: 0.1rem 0.3rem;
            font-size: 0.75rem;
        }
    </style>

    <script>
        $(document).ready(function() {
            // จัดการการ Toggle subtasks
            $(document).on('click', '.toggle-subtasks', function() {
                $(this).toggleClass('expanded');
                const currentRow = $(this).closest('tr');
                const currentLevel = parseInt(currentRow.data('level'));
                let nextRow = currentRow.next();

                while (nextRow.length && parseInt(nextRow.data('level')) > currentLevel) {
                    nextRow.toggle();
                    nextRow = nextRow.next();
                }
            });

            // ซ่อน subtasks เมื่อโหลดหน้าเว็บ
            $('.task-row').each(function() {
                const level = parseInt($(this).data('level'));
                if (level > 0) {
                    $(this).hide();
                }
            });
        });

        // อัพเดท initSortable function
        function initSortable() {
            const tbody = document.querySelector('#tasks-table tbody');
            new Sortable(tbody, {
                handle: '.task-handle',
                animation: 150,
                onEnd: function(evt) {
                    // อัพเดทลำดับและ parent
                    const taskId = evt.item.dataset.taskId;
                    const prevRow = evt.item.previousElementSibling;
                    const nextRow = evt.item.nextElementSibling;

                    let newLevel = 0;
                    let newParentId = null;

                    if (prevRow) {
                        const prevLevel = parseInt(prevRow.dataset.level);
                        newLevel = prevLevel;
                        // ตรวจสอบว่าควรเป็น subtask หรือไม่
                        if (evt.item.querySelector('.task-handle').offsetLeft > prevRow.querySelector('.task-handle').offsetLeft) {
                            newLevel = prevLevel + 1;
                            newParentId = prevRow.dataset.taskId;
                        }
                    }

                    updateTaskPosition(taskId, newParentId, evt.newIndex, newLevel);
                }
            });
        }

        // แสดงชื่อผู้รับผิดชอบในรูปแบบ Avatar ในตาราง
        $(document).ready(function() {
            // เปิดใช้งาน tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // รีเฟรช tooltips หลังจากโหลดข้อมูลใหม่
            $(document).ajaxComplete(function() {
                $('[data-toggle="tooltip"]').tooltip();
            });
        });
    </script>

    <!-- Avatar CSS -->
    <style>
        .avatar-group {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #4a90e2;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .avatar:hover {
            transform: scale(1.1);
        }

        .avatar-text {
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        /* สีพื้นหลังที่แตกต่างกันสำหรับแต่ละ avatar */
        .avatar:nth-child(5n+1) {
            background-color: #4a90e2;
        }

        .avatar:nth-child(5n+2) {
            background-color: #50b794;
        }

        .avatar:nth-child(5n+3) {
            background-color: #f39c12;
        }

        .avatar:nth-child(5n+4) {
            background-color: #e74c3c;
        }

        .avatar:nth-child(5n+5) {
            background-color: #8e44ad;
        }
    </style>


</body>

</html>