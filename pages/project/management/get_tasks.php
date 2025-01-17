<?php
session_start();
include('../../../config/condb.php');

// รับค่า project_id จาก GET request
$project_id = $_GET['project_id'] ?? null;

if (!$project_id) {
    exit('ไม่พบรหัสโครงการ');
}

// ฟังก์ชันสำหรับดึง Tasks แบบ recursive
function getTasksHierarchy($condb, $project_id, $parent_id = null)
{
    $stmt = $condb->prepare("
        SELECT 
            t.*,
            GROUP_CONCAT(DISTINCT CONCAT(u.first_name, ' ', u.last_name) SEPARATOR ', ') as assigned_users,
            COUNT(DISTINCT st.task_id) as subtask_count
        FROM project_tasks t
        LEFT JOIN project_task_assignments ta ON t.task_id = ta.task_id
        LEFT JOIN users u ON ta.user_id = u.user_id
        LEFT JOIN project_tasks st ON t.task_id = st.parent_task_id
        WHERE t.project_id = ?
        AND (t.parent_task_id IS NULL AND ? IS NULL OR t.parent_task_id = ?)
        GROUP BY t.task_id
        ORDER BY t.task_order ASC, t.created_at ASC
    ");

    $stmt->execute([$project_id, $parent_id, $parent_id]);
    $tasks = [];

    while ($task = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $task['sub_tasks'] = getTasksHierarchy($condb, $project_id, $task['task_id']);
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
    $assignedUsers = htmlspecialchars($task['assigned_users'] ?? 'ไม่มีผู้รับผิดชอบ');
    $startDate = $task['start_date'] ? date('d/m/Y', strtotime($task['start_date'])) : '-';
    $endDate = $task['end_date'] ? date('d/m/Y', strtotime($task['end_date'])) : '-';
    $progress = (int)$task['progress'];

    // กำหนด class ตามสถานะ
    $statusClass = match ($task['status']) {
        'Pending' => 'badge-warning',
        'In Progress' => 'badge-info',
        'Completed' => 'badge-success',
        'Cancelled' => 'badge-danger',
        default => 'badge-secondary'
    };

    $html = "
    <tr class='task-row' data-task-id='{$taskId}' data-level='{$level}'>
        <td>
            <i class='fas fa-grip-vertical task-handle mr-2' style='cursor: move;'></i>
            {$indent}";

    // แสดงไอคอน expand/collapse ถ้ามี subtasks
    if (!empty($task['sub_tasks'])) {
        $html .= "<i class='fas fa-caret-right toggle-subtasks mr-1' style='cursor: pointer;'></i>";
    } else {
        $html .= "<i class='fas fa-minus mr-1'></i>";
    }

    $html .= "{$taskName}</td>
        <td><span class='badge {$statusClass}'>{$task['status']}</span></td>
        <td class='text-center'>{$progress}%</td>
        <td>{$startDate}</td>
        <td>{$endDate}</td>
        <td>{$assignedUsers}</td>
        <td>
            <div class='btn-group'>
                <button type='button' class='btn btn-xs btn-info' onclick='editTask(\"{$taskId}\")'>
                    <i class='fas fa-edit'></i>
                </button>
                <button type='button' class='btn btn-xs btn-primary' onclick='showAddTaskModal(\"{$taskId}\")'>
                    <i class='fas fa-plus'></i>
                </button>
                <button type='button' class='btn btn-xs btn-danger' onclick='deleteTask(\"{$taskId}\")'>
                    <i class='fas fa-trash'></i>
                </button>
            </div>
        </td>
    </tr>";

    // แสดง subtasks
    if (!empty($task['sub_tasks'])) {
        foreach ($task['sub_tasks'] as $subtask) {
            $html .= renderTask($subtask, $level + 1);
        }
    }

    return $html;
}

// แสดงผลตาราง
echo "<table class='table table-hover' id='tasks-table'>
    <thead>
        <tr>
            <th>งาน</th>
            <th>สถานะ</th>
            <th class='text-center'>ความคืบหน้า</th>
            <th>วันที่เริ่ม</th>
            <th>วันที่สิ้นสุด</th>
            <th>ผู้รับผิดชอบ</th>
            <th>จัดการ</th>
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

echo "</tbody></table>";
