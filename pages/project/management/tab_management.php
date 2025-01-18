<?php
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

<!-- เพิ่ม SortableJS library -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<!-- Main content -->
<section class="content">
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
</section>

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

    function saveTask() {
        let formData = new FormData($('#taskForm')[0]);
        let formDataObj = {};
        formData.forEach((value, key) => {
            if (key === 'assigned_users[]') {
                if (!formDataObj['assigned_users']) {
                    formDataObj['assigned_users'] = [];
                }
                formDataObj['assigned_users'].push(value);
            } else {
                formDataObj[key] = value;
            }
        });

        formDataObj.project_id = '<?php echo $project_id; ?>';

        $.ajax({
            url: 'save_task.php',
            type: 'POST',
            data: formDataObj,
            success: function(response) {
                try {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }

                    if (response.status === 'success') {
                        $('#taskModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
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
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }

                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'ลบสำเร็จ',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
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
                            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
                        });
                    }
                });
            }
        });
    }

    function editTask(taskId) {
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
                        $('#task_id').val(task.task_id);
                        $('#parent_task_id').val(task.parent_task_id);
                        $('#task_name').val(task.task_name);
                        $('#description').val(task.description);
                        $('#start_date').val(task.start_date);
                        $('#end_date').val(task.end_date);
                        $('#status').val(task.status);
                        $('#progress').val(task.progress);
                        $('#priority').val(task.priority);
                        $('#assigned_users').val(task.assigned_users).trigger('change');
                        $('#taskModalTitle').text('แก้ไขงาน');
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

    function initSortable() {
        const taskContainer = document.querySelector('#tasks-table tbody');
        new Sortable(taskContainer, {
            handle: '.task-handle',
            animation: 150,
            onEnd: function(evt) {
                const taskId = evt.item.dataset.taskId;
                const targetRow = evt.item;
                const prevRow = evt.item.previousElementSibling;
                const currentIndent = parseInt(evt.item.getAttribute('data-level') || 0);

                let newParentId = null;
                let newLevel = 0;

                if (prevRow) {
                    const prevLevel = parseInt(prevRow.getAttribute('data-level') || 0);
                    const dragOffset = evt.originalEvent.offsetX;

                    // คำนวณระดับใหม่ตามระยะห่างจากขอบซ้าย
                    if (dragOffset > 50) { // ถ้าลากเข้าไปด้านใน > 50px
                        // ทำให้เป็น subtask ของ task ก่อนหน้า
                        newLevel = prevLevel + 1;
                        newParentId = prevRow.getAttribute('data-task-id');
                    } else {
                        // อยู่ระดับเดียวกับ task ก่อนหน้า
                        newLevel = prevLevel;
                        // หา parent ID จาก task ก่อนหน้าที่อยู่ระดับเดียวกัน
                        newParentId = prevRow.getAttribute('data-parent-id');
                    }
                }

                // อัพเดตตำแหน่งและระดับ
                $.ajax({
                    url: 'update_task_position.php',
                    type: 'POST',
                    data: {
                        task_id: taskId,
                        new_parent_id: newParentId,
                        new_level: newLevel,
                        new_position: evt.newIndex
                    },
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.status === 'success') {
                                // รีโหลดรายการ tasks เพื่อแสดงผลที่ถูกต้อง
                                loadTasks();
                            } else {
                                throw new Error(result.message || 'เกิดข้อผิดพลาดในการอัพเดตตำแหน่ง');
                            }
                        } catch (error) {
                            console.error(error);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: error.message
                            });
                            loadTasks(); // รีโหลดกลับสู่สถานะเดิม
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
                        });
                        loadTasks(); // รีโหลดกลับสู่สถานะเดิม
                    }
                });
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

    .task-row.sortable-ghost {
        opacity: 0.5;
        background-color: #e3f2fd !important;
    }

    .task-row.sortable-drag {
        background-color: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .task-handle {
        cursor: move;
        opacity: 0.6;
    }

    .task-handle:hover {
        opacity: 1;
    }

    .task-row td:first-child {
        padding-left: 20px;
        position: relative;
    }

    .task-row.sortable-ghost td:first-child::before {
        content: '';
        position: absolute;
        left: 40px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #2196f3;
    }

    .tooltip-inner {
        max-width: 300px;
        text-align: left;
        padding: 8px;
        background-color: rgba(0, 0, 0, 0.8);
    }

    .task-name {
        vertical-align: middle;
    }

    .btn-link.text-info {
        text-decoration: none;
    }

    .btn-link.text-info:hover {
        color: #0056b3 !important;
    }

    .btn-group .btn-xs {
        padding: 0.1rem 0.3rem;
        font-size: 0.75rem;
    }
</style>