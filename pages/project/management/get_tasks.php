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
            ) as avg_subtask_progress,

            -- สร้าง Task Display ID (T + ลำดับ)
            CONCAT('T', LPAD(ROW_NUMBER() OVER (ORDER BY t.task_order ASC, t.created_at ASC), 3, '0')) as task_display_id

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
function renderTask($task, $level = 0, $taskNumber = '')
{
    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
    $taskId = htmlspecialchars($task['task_id']);
    $taskName = htmlspecialchars($task['task_name']);
    $description = htmlspecialchars($task['description'] ?: 'ไม่มีรายละเอียด');
    $progress = (int)$task['progress'];

    // สร้าง Task Display ID สำหรับแสดงผล
    $taskDisplayId = !empty($taskNumber) ? $taskNumber : 'T' . str_pad($level + 1, 3, '0', STR_PAD_LEFT);

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

    // สร้าง CSS class สำหรับระดับของ Task
    $levelClass = match($level) {
        0 => 'task-id-main',
        1 => 'task-id-subtask task-id-level-1',
        2 => 'task-id-subtask task-id-level-2',
        default => 'task-id-subtask task-id-level-3'
    };

    // สร้าง visual hierarchy indicator
    $hierarchyIndicator = '';
    if ($level > 0) {
        $hierarchyIndicator = '<span class="task-hierarchy-line" style="margin-left: ' . (($level - 1) * 20) . 'px;"></span>';
    }

    // กำหนดการซ่อนแสดงตาม level (subtasks จะถูกซ่อนไว้ตั้งแต่เริ่มต้น)
    $hiddenStyle = $level > 0 ? 'style="display: none;"' : '';

    // HTML สำหรับแต่ละแถว
    $html = "
    <tr class='task-row task-level-{$level}' data-task-id='{$taskId}' data-level='{$level}' {$hiddenStyle}>
        <td class='text-center text-nowrap task-id-cell'>
            {$hierarchyIndicator}
            <span class='badge task-id-badge {$levelClass}'>{$taskDisplayId}</span>
        </td>
        <td>
            <i class='fas fa-grip-vertical task-handle mr-2' style='cursor: move;'></i>
            {$indent}";

    if (!empty($task['sub_tasks'])) {
        $html .= "<i class='fas fa-caret-right toggle-subtasks mr-1' style='cursor: pointer;'></i>";
    } else {
        $html .= "<i class='fas fa-circle mr-1' style='font-size: 0.5em; vertical-align: middle;'></i>";
    }

    $html .= "<a href='management/task_detail.php?task_id={$taskId}' class='task-name-link' style='color: #2563eb; text-decoration: none; font-weight: 500;'>{$taskName}</a>";

    $html .= "</td>
        <td>
            <div class='task-description' style='cursor: pointer;' onclick='window.location.href=\"management/task_detail.php?task_id={$taskId}\"'>
                <div class='text-truncate' style='max-width: 300px;' title='คลิกเพื่อดูรายละเอียดและแสดงความคิดเห็น'>
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
        $subTaskCounter = 1;
        foreach ($task['sub_tasks'] as $subtask) {
            $subTaskId = $taskDisplayId . '.' . $subTaskCounter;
            $html .= renderTask($subtask, $level + 1, $subTaskId);
            $subTaskCounter++;
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
        <thead class='thead-light'>
            <tr>
                <th style='min-width: 80px;' class='text-center text-nowrap'>
                    <i class='fas fa-hashtag mr-1'></i>ID
                </th>
                <th style='min-width: 280px;'>
                    <i class='fas fa-tasks mr-1'></i>งาน
                </th>
                <th style='min-width: 280px;'>
                    <i class='fas fa-align-left mr-1'></i>รายละเอียด
                </th>
                <th style='min-width: 100px;' class='text-center'>
                    <i class='fas fa-flag mr-1'></i>สถานะ
                </th>
                <th style='min-width: 150px;' class='text-center'>
                    <i class='fas fa-chart-line mr-1'></i>ความคืบหน้า
                </th>
                <th style='min-width: 100px;' class='text-center text-nowrap'>
                    <i class='fas fa-exclamation-circle mr-1'></i>ความสำคัญ
                </th>
                <th style='min-width: 100px;' class='text-center'>
                    <i class='fas fa-calendar-check mr-1'></i>วันที่เริ่ม
                </th>
                <th style='min-width: 100px;' class='text-center'>
                    <i class='fas fa-calendar-times mr-1'></i>วันที่สิ้นสุด
                </th>
                <th style='min-width: 100px;' class='text-center text-nowrap'>
                    <i class='fas fa-clock mr-1'></i>ระยะเวลา
                </th>
                <th style='min-width: 150px;' class='text-center'>
                    <i class='fas fa-users mr-1'></i>ผู้รับผิดชอบ
                </th>
                <th style='min-width: 120px;' class='text-center'>
                    <i class='fas fa-user-plus mr-1'></i>ผู้สร้าง
                </th>
                <th style='min-width: 120px;' class='text-center'>
                    <i class='fas fa-cogs mr-1'></i>จัดการ
                </th>
            </tr>
        </thead>
        <tbody>";

// ดึงและแสดงข้อมูล tasks
$tasks = getTasksHierarchy($condb, $project_id);
if (empty($tasks)) {
    echo "<tr><td colspan='12' class='text-center py-4'>
            <div class='text-muted'>
                <i class='fas fa-tasks fa-2x mb-2'></i><br>
                ยังไม่มีรายการงาน<br>
                <small>คลิกปุ่ม 'เพิ่มงานใหม่' เพื่อเริ่มต้น</small>
            </div>
          </td></tr>";
} else {
    $taskCounter = 1;
    foreach ($tasks as $task) {
        echo renderTask($task, 0, 'T' . str_pad($taskCounter, 3, '0', STR_PAD_LEFT));
        $taskCounter++;
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

    /* สไตล์สำหรับ Task ID Badge */
    .task-id-badge {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        font-size: 0.85rem;
        border: 1px solid #ced4da;
        padding: 0.3rem 0.6rem;
        border-radius: 0.375rem;
        letter-spacing: 0.5px;
        display: inline-block;
        transition: all 0.2s ease;
    }

    /* Main Task ID - สีเข้มและโดดเด่น */
    .task-id-main {
        background-color: #007bff !important;
        color: #fff !important;
        border-color: #0056b3;
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
        font-size: 0.9rem;
        font-weight: 700;
    }

    /* Sub Task ID - สีอ่อนและเล็กกว่า */
    .task-id-subtask {
        border-color: #dee2e6;
        font-size: 0.75rem;
        font-weight: 600;
        position: relative;
    }

    /* Sub Task Level 1 - สีเขียว */
    .task-id-level-1 {
        background-color: #d4edda !important;
        color: #155724 !important;
        border-color: #c3e6cb;
    }

    /* Sub Task Level 2 - สีเหลือง */
    .task-id-level-2 {
        background-color: #fff3cd !important;
        color: #856404 !important;
        border-color: #ffeaa7;
    }

    /* Sub Task Level 3+ - สีแดง */
    .task-id-level-3 {
        background-color: #f8d7da !important;
        color: #721c24 !important;
        border-color: #f5c6cb;
    }

    /* เพิ่มเส้นเชื่อมสำหรับ Sub Task */
    .task-id-subtask::before {
        content: '';
        position: absolute;
        left: -10px;
        top: 50%;
        width: 8px;
        height: 1px;
        background-color: #dee2e6;
        transform: translateY(-50%);
    }

    /* Cell สำหรับ Task ID */
    .task-id-cell {
        vertical-align: middle;
        position: relative;
        padding: 0.75rem 0.5rem;
    }

    /* Hierarchy Line สำหรับแสดง level ของ Task */
    .task-hierarchy-line {
        display: inline-block;
        width: 15px;
        height: 1px;
        background-color: #6c757d;
        position: relative;
        margin-right: 8px;
        vertical-align: middle;
    }

    .task-hierarchy-line::before {
        content: '└';
        position: absolute;
        left: -5px;
        top: -8px;
        color: #6c757d;
        font-size: 14px;
    }

    /* แยกสีตาม level ของ Task */
    .task-level-0 {
        background-color: rgba(0, 123, 255, 0.05);
    }

    .task-level-1 {
        background-color: rgba(40, 167, 69, 0.05);
    }

    .task-level-2 {
        background-color: rgba(255, 193, 7, 0.05);
    }

    .task-level-3 {
        background-color: rgba(220, 53, 69, 0.05);
    }

    /* สไตล์สำหรับ thead - โทนสีเรียบง่าย */
    .thead-light th {
        background: linear-gradient(180deg, #ffffff 0%, #f5f7fa 100%) !important;
        border-bottom: 1px solid #e1e8ed;
        border-top: 1px solid #e1e8ed;
        font-weight: 500;
        color: #5a6c7d;
        font-size: 0.875rem;
        white-space: nowrap;
        padding: 1rem 0.75rem;
        letter-spacing: 0.3px;
    }

    .thead-light th i {
        color: #8899a6;
        font-size: 0.8rem;
        margin-right: 0.25rem;
    }

    /* Task Name Link Style */
    .task-name-link {
        transition: all 0.2s ease;
    }

    .task-name-link:hover {
        text-decoration: underline !important;
        color: #1e40af !important;
    }

    .task-description {
        transition: background-color 0.2s ease;
        padding: 0.25rem;
        border-radius: 4px;
    }

    .task-description:hover {
        background-color: rgba(37, 99, 235, 0.05);
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