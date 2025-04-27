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
                    <div class="form-group">
                        <label>ผู้รับผิดชอบ</label>
                        <select class="form-control" name="assigned_users[]" multiple>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['user_id']; ?>">
                                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
            url: 'management/get_tasks.php',
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
            url: 'management/save_task.php',
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

                        // แสดงข้อความสำเร็จsave_task.php
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
                    url: 'management/delete_task.php',
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
            url: 'management/edit_task.php',
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

                // ตรวจสอบว่า Task นี้ควรเป็น Sub Task ของ Task ก่อนหน้าหรือไม่
                if (prevRow) {
                    const prevLevel = parseInt(prevRow.dataset.level || 0);

                    // ตรวจสอบระยะห่างจากขอบซ้ายเพื่อกำหนดว่าเป็น Sub Task หรือไม่
                    const currentIndent = evt.item.querySelector('.task-handle').offsetLeft;
                    const prevIndent = prevRow.querySelector('.task-handle').offsetLeft;

                    if (currentIndent > prevIndent) {
                        // ถ้าระยะห่างจากขอบซ้ายมากกว่า แสดงว่าเป็น Sub Task ของ Task ก่อนหน้า
                        newLevel = prevLevel + 1;
                        newParentId = prevRow.dataset.taskId;
                    } else if (currentIndent < prevIndent) {
                        // ถ้าระยะห่างจากขอบซ้ายน้อยกว่า แสดงว่าเป็น Task หลัก
                        newLevel = prevLevel - 1;
                        newParentId = prevRow.dataset.parentTaskId || null;
                    } else {
                        // ถ้าระยะห่างเท่ากัน แสดงว่าเป็น Task ระดับเดียวกัน
                        newLevel = prevLevel;
                        newParentId = prevRow.dataset.parentTaskId || null;
                    }
                }

                // ส่งข้อมูลไปอัพเดทที่ฐานข้อมูล
                $.ajax({
                    url: 'management/update_task_position.php',
                    type: 'POST',
                    data: {
                        task_id: taskId,
                        new_parent_id: newParentId,
                        new_level: newLevel
                    },
                    success: function(response) {
                        try {
                            // ตรวจสอบว่า response เป็น JSON string
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }

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
                                throw new Error(response.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ');
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
            url: 'management/update_task_position.php',
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
            url: 'management/get_tasks.php',
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

    .toggle-subtasks {
        transition: transform 0.2s ease-in-out;
    }

    .fa-caret-right {
        transform: rotate(0deg);
    }

    .fa-caret-down {
        transform: rotate(90deg);
    }

    .task-row {
        transition: all 0.2s ease;
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

    /* เพิ่มพื้นที่สำหรับการลากแบบ indent */
    .task-row td:first-child {
        padding-left: 20px;
        position: relative;
    }

    /* แสดงเส้นแนวตั้งเมื่อลาก */
    .task-row.sortable-ghost td:first-child::before {
        content: '';
        position: absolute;
        left: 40px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #2196f3;
    }

    /* สไตล์สำหรับ tooltip */
    .tooltip-inner {
        max-width: 300px;
        text-align: left;
        padding: 8px;
        background-color: rgba(0, 0, 0, 0.8);
    }

    /* สไตล์สำหรับชื่องานและปุ่มรายละเอียด */
    .task-name {
        vertical-align: middle;
    }

    .btn-link.text-info {
        text-decoration: none;
    }

    .btn-link.text-info:hover {
        color: #0056b3 !important;
    }

    /* สไตล์สำหรับปุ่มในกลุ่ม */
    .btn-group .btn-xs {
        padding: 0.1rem 0.3rem;
        font-size: 0.75rem;
    }
</style>

<script>
    $(document).ready(function() {
        // จัดการการ Toggle subtasks
        $(document).on('click', '.toggle-subtasks', function() {
            const icon = $(this);
            icon.toggleClass('fa-caret-down fa-caret-right'); // สลับไอคอนระหว่าง down กับ right

            const currentRow = icon.closest('tr');
            const currentLevel = parseInt(currentRow.data('level'));
            let nextRow = currentRow.next();

            while (nextRow.length && parseInt(nextRow.data('level')) > currentLevel) {
                nextRow.toggle();
                nextRow = nextRow.next();
            }
        });
    });

    // อัพเดท initSortable function
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
                    url: 'management/update_task_position.php',
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