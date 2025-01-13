<?php

// ตรวจสอบว่ามี project_id ถูกส่งมาหรือไม่
if (!isset($_GET['project_id']) || empty($_GET['project_id'])) {
    echo "ไม่พบข้อมูลโครงการ";
    exit;
}

// รับค่า project_id และถอดรหัส
$project_id = decryptUserId($_GET['project_id']);
?>

<!-- ส่วนแสดงผล Tasks -->
<div class="task-management-container">
    <!-- ส่วนหัว -->
    <div class="task-header d-flex justify-content-between align-items-center mb-3">
        <h4>จัดการงานในโครงการ</h4>
        <button type="button" class="btn btn-primary btn-sm" onclick="openAddTaskModal()">
            <i class="fas fa-plus"></i> เพิ่มงานใหม่
        </button>
    </div>

    <!-- ตารางแสดง Tasks -->
    <div class="table-responsive">
        <table id="tasksTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 40%">ชื่องาน</th>
                    <th>วันที่เริ่ม</th>
                    <th>วันที่สิ้นสุด</th>
                    <th>ผู้รับผิดชอบ</th>
                    <th>สถานะ</th>
                    <th>ความคืบหน้า</th>
                    <th>การดำเนินการ</th>
                </tr>
            </thead>
            <tbody id="tasksTableBody">
                <!-- ข้อมูล Tasks จะถูกเพิ่มที่นี่ด้วย JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal สำหรับเพิ่ม/แก้ไข Task -->
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
                    <input type="hidden" id="taskId">
                    <input type="hidden" id="parentTaskId">

                    <div class="form-group">
                        <label for="taskName">ชื่องาน <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="taskName" required>
                    </div>

                    <div class="form-group">
                        <label for="taskDescription">รายละเอียดงาน</label>
                        <textarea class="form-control" id="taskDescription" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="startDate">วันที่เริ่ม</label>
                                <input type="date" class="form-control" id="startDate">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="endDate">วันที่สิ้นสุด</label>
                                <input type="date" class="form-control" id="endDate">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="assignee">ผู้รับผิดชอบ</label>
                        <select class="form-control" id="assignee">
                            <option value="">เลือกผู้รับผิดชอบ</option>
                            <!-- ตัวเลือกจะถูกเพิ่มด้วย JavaScript -->
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">สถานะ</label>
                                <select class="form-control" id="status">
                                    <option value="Pending">รอดำเนินการ</option>
                                    <option value="In Progress">กำลังดำเนินการ</option>
                                    <option value="Completed">เสร็จสิ้น</option>
                                    <option value="Cancelled">ยกเลิก</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="priority">ระดับความสำคัญ</label>
                                <select class="form-control" id="priority">
                                    <option value="Low">ต่ำ</option>
                                    <option value="Medium">ปานกลาง</option>
                                    <option value="High">สูง</option>
                                    <option value="Urgent">เร่งด่วน</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="progress">ความคืบหน้า (%)</label>
                        <input type="number" class="form-control" id="progress" min="0" max="100" value="0">
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

<!-- เพิ่ม CSS เฉพาะสำหรับหน้านี้ -->
<style>
    .task-management-container {
        padding: 20px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .sub-task {
        margin-left: 30px;
    }

    .task-row {
        cursor: pointer;
    }

    .task-row:hover {
        background-color: #f8f9fa;
    }
</style>