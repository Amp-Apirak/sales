<?php
session_start();
include('../../../config/condb.php');

// รับค่า project_id จาก GET request
$project_id = $_GET['project_id'] ?? null;

if (!$project_id) {
    exit('ไม่พบรหัสโครงการ');
}


/**
 * คำนวณจำนวนวันดำเนินการ
 */
function calculateDuration($startDate, $endDate)
{
    if (
        empty($startDate) || empty($endDate) ||
        $startDate == '0000-00-00' || $endDate == '0000-00-00'
    ) {
        return '<span class="text-muted">-</span>';
    }

    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $interval = $start->diff($end);

    // เพิ่ม 1 วันเพราะนับวันเริ่มต้นด้วย
    $days = $interval->days + 1;

    // ถ้าวันที่สิ้นสุดน้อยกว่าวันที่เริ่ม
    if ($end < $start) {
        return '<span class="text-danger" data-toggle="tooltip" title="วันที่ไม่ถูกต้อง">!</span>';
    }

    return '<span data-toggle="tooltip" title="' . $days . ' วัน">' .
        number_format($days) . ' วัน</span>';
}

/**
 * คำนวณความคืบหน้าเฉลี่ยของ tasks และ subtasks แบบ recursive
 */
function calculateTaskProgress($tasks)
{
    if (empty($tasks)) {
        return 0;
    }

    $totalProgress = 0;
    $count = 0;

    foreach ($tasks as $task) {
        if (!empty($task['sub_tasks'])) {
            // คำนวณความคืบหน้าจาก subtasks
            $subTaskProgress = calculateTaskProgress($task['sub_tasks']);
            $totalProgress += $subTaskProgress;
        } else {
            // ใช้ค่าความคืบหน้าของตัวเอง
            $totalProgress += floatval($task['progress']);
        }
        $count++;
    }

    return $count > 0 ? round($totalProgress / $count, 2) : 0;
}


// ฟังก์ชันสำหรับดึง Tasks แบบ recursive
function getTasksHierarchy($condb, $project_id, $parent_id = null)
{
    $stmt = $condb->prepare("
        SELECT 
            t.*,
            -- ผู้รับผิดชอบงาน
            GROUP_CONCAT(
                DISTINCT CONCAT(u.first_name, ' ', u.last_name) 
                SEPARATOR ', '
            ) as assigned_users,
            
            -- จำนวน subtasks
            (
                SELECT COUNT(*) 
                FROM project_tasks st 
                WHERE st.parent_task_id = t.task_id
            ) as subtask_count,
            
            -- ผู้สร้างงาน
            CONCAT(creator.first_name, ' ', creator.last_name) as creator_name,
            
            -- คำนวณความคืบหน้าเฉลี่ยจาก subtasks
            COALESCE(
                (
                    SELECT AVG(st.progress)
                    FROM project_tasks st
                    WHERE st.parent_task_id = t.task_id
                ),
                t.progress
            ) as avg_subtask_progress
            
        FROM project_tasks t
        LEFT JOIN project_task_assignments ta ON t.task_id = ta.task_id
        LEFT JOIN users u ON ta.user_id = u.user_id
        LEFT JOIN users creator ON t.created_by = creator.user_id
        
        WHERE t.project_id = ?
        AND (t.parent_task_id IS NULL AND ? IS NULL OR t.parent_task_id = ?)
        
        GROUP BY t.task_id
        ORDER BY t.task_order ASC, t.created_at ASC
    ");

    $stmt->execute([$project_id, $parent_id, $parent_id]);
    $tasks = [];

    while ($task = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // ดึง subtasks แบบ recursive
        $task['sub_tasks'] = getTasksHierarchy($condb, $project_id, $task['task_id']);

        // คำนวณความคืบหน้าที่แท้จริง
        if (!empty($task['sub_tasks'])) {
            // ถ้ามี subtasks ใช้ค่าเฉลี่ยจาก subtasks
            $task['real_progress'] = calculateTaskProgress($task['sub_tasks']);
            $task['has_subtasks'] = true;
        } else {
            // ถ้าไม่มี subtasks ใช้ค่าความคืบหน้าของตัวเอง
            $task['real_progress'] = floatval($task['progress']);
            $task['has_subtasks'] = false;
        }

        // จัดการวันที่
        $task['start_date'] = !empty($task['start_date']) ? date('Y-m-d', strtotime($task['start_date'])) : null;
        $task['end_date'] = !empty($task['end_date']) ? date('Y-m-d', strtotime($task['end_date'])) : null;

        // ตรวจสอบความล่าช้า
        if ($task['end_date'] && strtotime($task['end_date']) < time()) {
            $task['is_overdue'] = true;
            $task['days_overdue'] = floor((time() - strtotime($task['end_date'])) / (60 * 60 * 24));
        }

        $tasks[] = $task;
    }

    return $tasks;
}

// ฟังก์ชันแสดงผล Task แต่ละรายการ
function renderTask($task, $level = 0)
{
    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
    $taskId = htmlspecialchars($task['task_id']);
    $taskName = htmlspecialchars($task['task_name']);
    $description = htmlspecialchars($task['description'] ?: 'ไม่มีรายละเอียด');
    $progress = (int)$task['progress'];

    // ส่วนแสดงวันที่เริ่ม
    if (empty($task['start_date']) || $task['start_date'] == '0000-00-00') {
        $startDateDisplay = '<span class="text-danger" data-toggle="tooltip" title="กรุณาระบุวันที่เริ่มต้น">ไม่ระบุวันที่</span>';
    } else {
        $startDateDisplay = date('d/m/Y', strtotime($task['start_date']));
    }

    // ส่วนแสดงวันที่สิ้นสุด
    if (empty($task['end_date']) || $task['end_date'] == '0000-00-00') {
        $endDateDisplay = '<span class="text-danger" data-toggle="tooltip" title="กรุณาระบุวันที่สิ้นสุด">ไม่ระบุวันที่</span>';
    } else {
        $endDate = strtotime($task['end_date']);
        $today = strtotime('today');
        if ($endDate < $today) {
            $endDateDisplay = '<span class="text-danger" data-toggle="tooltip" title="เลยกำหนดเวลา ' . floor(($today - $endDate) / (60 * 60 * 24)) . ' วัน">' .
                date('d/m/Y', $endDate) .
                ' <i class="fas fa-exclamation-circle"></i></span>';
        } else {
            $endDateDisplay = date('d/m/Y', $endDate);
        }
    }

    // กำหนดสีและ class สำหรับระดับความสำคัญ
    $priorityClass = match ($task['priority']) {
        'High' => 'badge-danger',
        'Medium' => 'badge-warning',
        'Low' => 'badge-info',
        'Urgent' => 'badge-dark',
        default => 'badge-secondary'
    };

    // แปลความหมายระดับความสำคัญเป็นภาษาไทย
    $priorityText = match ($task['priority']) {
        'High' => 'สูง',
        'Medium' => 'ปานกลาง',
        'Low' => 'ต่ำ',
        'Urgent' => 'เร่งด่วน',
        default => 'ไม่ระบุ'
    };

    // คำนวณจำนวนวันดำเนินการ
    $duration = calculateDuration($task['start_date'], $task['end_date']);


    // กำหนด class ตามสถานะ
    $statusClass = match ($task['status']) {
        'Pending' => 'badge-warning',
        'In Progress' => 'badge-info',
        'Completed' => 'badge-success',
        'Cancelled' => 'badge-danger',
        default => 'badge-secondary'
    };

    // กำหนดสีของ progress bar
    $progressBarColor = match (true) {
        $progress >= 100 => 'bg-success',
        $progress >= 70 => 'bg-info',
        $progress >= 30 => 'bg-warning',
        default => 'bg-danger'
    };

    // สร้าง avatars HTML สำหรับผู้รับผิดชอบ
    $assignedUsersArray = $task['assigned_users'] ? explode(', ', $task['assigned_users']) : [];
    $avatarsHtml = '<div class="avatar-group">';
    foreach ($assignedUsersArray as $user) {
        if (!empty($user)) {
            $initials = implode('', array_map(function ($name) {
                return strtoupper(substr($name, 0, 1));
            }, explode(' ', $user)));

            $avatarsHtml .= sprintf(
                '<div class="avatar" data-toggle="tooltip" title="%s">
                    <span class="avatar-text">%s</span>
                </div>',
                htmlspecialchars($user),
                htmlspecialchars($initials)
            );
        }
    }
    if (empty($assignedUsersArray)) {
        $avatarsHtml .= '<div class="avatar bg-secondary">
            <span class="avatar-text">-</span>
        </div>';
    }
    $avatarsHtml .= '</div>';

    // ใช้ค่าความคืบหน้าที่คำนวณได้จาก subtasks
    $progress = $task['has_subtasks'] ? $task['real_progress'] : (int)$task['progress'];

    // HTML สำหรับแต่ละแถว
    $html = "
    <tr class='task-row' data-task-id='{$taskId}' data-level='{$level}'>
        <td>
            <i class='fas fa-grip-vertical task-handle mr-2' style='cursor: move;'></i>
            {$indent}";

    if (!empty($task['sub_tasks'])) {
        $html .= "<i class='fas fa-caret-down toggle-subtasks mr-1' style='cursor: pointer;'></i>";
    } else {
        $html .= "<i class='fas fa-circle mr-1' style='font-size: 0.5em; vertical-align: middle;'></i>";
    }

    $html .= "<span class='task-name'>{$taskName}</span>";

    $html .= "</td>
        <td>
            <div class='task-description' style='cursor: pointer;' onclick='showTaskDetails(`{$taskName}`, `{$description}`)'>
                <div class='text-truncate' style='max-width: 300px;' title='คลิกเพื่อดูรายละเอียด'>
                    {$description}
                </div>
            </div>
        </td>
        <td><span class='badge {$statusClass}'>{$task['status']}</span></td>
        <td class='text-center'>
            <div class='progress' style='height: 24px; background-color: #f8f9fa; border-radius: 12px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);'>
                <div class='progress-bar {$progressBarColor}' 
                     role='progressbar' 
                     style='width: {$progress}%; font-weight: 600; font-size: 0.85rem;' 
                     aria-valuenow='{$progress}' 
                     aria-valuemin='0' 
                     aria-valuemax='100'>
                    {$progress}%
                </div>
            </div>
        </td>
        <td><span class='badge text-center {$priorityClass}'>{$priorityText}</span></td>
        <td class='text-nowrap'>{$startDateDisplay}</td>
        <td class='text-nowrap'>{$endDateDisplay}</td>
        <td class='text-center text-nowrap'>{$duration}</td>
        <td>{$avatarsHtml}</td>
        <td>" . htmlspecialchars($task['creator_name'] ?? 'Systems Admin') . "</td>
        <td>
            <div class='btn-group'>
                <button type='button' class='btn btn-sm btn-info' onclick='editTask(\"{$taskId}\")' title='แก้ไข'>
                    <i class='fas fa-edit'></i>
                </button>
                <button type='button' class='btn btn-sm btn-primary' onclick='showAddTaskModal(\"{$taskId}\")' title='เพิ่ม Sub Task'>
                    <i class='fas fa-plus'></i>
                </button>
                <button type='button' class='btn btn-sm btn-danger' onclick='deleteTask(\"{$taskId}\")' title='ลบ'>
                    <i class='fas fa-trash'></i>
                </button>
            </div>
        </td>
    </tr>";


    // แสดง subtasks ถ้ามี
    if (!empty($task['sub_tasks'])) {
        foreach ($task['sub_tasks'] as $subtask) {
            $html .= renderTask($subtask, $level + 1);
        }
    }

    return $html;
}


// เพิ่ม Modal สำหรับแสดงรายละเอียด
echo '<div class="modal fade" id="taskDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskDetailsTitle"></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="taskDetailsContent" class="p-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>';

// แสดงผลตาราง
echo "<div class='table-responsive'>
    <table class='table table-hover' id='tasks-table'>
        <thead>
            <tr>
    <th style='min-width: 300px;'>งาน</th>
    <th style='min-width: 300px;'>รายละเอียด</th>
    <th style='min-width: 100px;'>สถานะ</th>
    <th style='min-width: 150px;' class='text-center'>ความคืบหน้า</th>
    <th style='min-width: 100px;' class='text-center text-nowrap'>ความสำคัญ</th>
    <th style='min-width: 100px;'>วันที่เริ่ม</th>
    <th style='min-width: 100px;'>วันที่สิ้นสุด</th>
    <th style='min-width: 100px;' class='text-center text-nowrap'>ระยะเวลา</th>
    <th style='min-width: 150px;'>ผู้รับผิดชอบ</th>
    <th style='min-width: 150px;'>ผู้สร้าง</th>
    <th style='min-width: 100px;'>จัดการ</th>
</tr>
        </thead>
        <tbody>";

// ดึงและแสดงข้อมูล tasks
$tasks = getTasksHierarchy($condb, $project_id);
if (empty($tasks)) {
    echo "<tr><td colspan='7' class='text-center'>ยังไม่มีรายการงาน</td></tr>";
} else {
    foreach ($tasks as $task) {
        echo renderTask($task);
    }
}

echo "</tbody></table></div>";
?>

<script>
    // เพิ่ม JavaScript function สำหรับแสดงรายละเอียด
    function showTaskDetails(taskName, description) {
        $('#taskDetailsTitle').text(taskName);
        $('#taskDetailsContent').html(description.replace(/\n/g, '<br>'));
        $('#taskDetailsModal').modal('show');
    }
</script>

<style>
    /* สไตล์สำหรับ tooltip */
    .tooltip-inner {
        max-width: 300px;
        text-align: left;
        padding: 8px;
        background-color: rgba(0, 0, 0, 0.8);
    }

    /* สไตล์สำหรับ task row */
    .task-row {
        transition: background-color 0.2s;
    }

    .task-row:hover {
        background-color: #f8f9fa;
    }

    /* สไตล์สำหรับ avatars */
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

    /* แก้ไขสไตล์ของ progress bar */
    .progress {
        position: relative;
        margin: 0;
        min-width: 150px;
    }

    .progress-bar {
        transition: width .6s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.2);
    }

    /* สีพื้นหลังและความโค้งมนของ progress bar */
    .bg-success {
        background: linear-gradient(45deg, #28a745, #34ce57);
    }

    .bg-info {
        background: linear-gradient(45deg, #17a2b8, #1fc8e3);
    }

    .bg-warning {
        background: linear-gradient(45deg, #ffc107, #ffce3a);
    }

    .bg-danger {
        background: linear-gradient(45deg, #dc3545, #f55c6c);
    }

    /* สไตล์สำหรับปุ่มกลุ่ม */
    .btn-group .btn-xs {
        padding: 0.1rem 0.3rem;
        font-size: 0.75rem;
    }

    /* สไตล์สำหรับ caret icon */
    .toggle-subtasks {
        transition: transform 0.2s;
    }

    .toggle-subtasks.expanded {
        transform: rotate(90deg);
    }

    /* สไตล์สำหรับ task handle */
    .task-handle {
        color: #ccc;
        transition: color 0.2s;
    }

    .task-handle:hover {
        color: #666;
    }
</style>

<style>
    .text-danger {
        animation: warning-pulse 2s infinite;
    }

    @keyframes warning-pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }

        100% {
            opacity: 1;
        }
    }

    .task-description {
        cursor: pointer;
        transition: all 0.2s;
    }

    .task-description:hover {
        color: #007bff;
        background-color: rgba(0, 123, 255, 0.1);
        border-radius: 4px;
    }

    #taskDetailsContent {
        white-space: pre-wrap;
        font-size: 0.9rem;
        line-height: 1.5;
        color: #333;
    }
</style>