<?php
session_start();
include('../../../config/condb.php');

// รับค่า project_id จาก GET request
$project_id = $_GET['project_id'] ?? null;

if (!$project_id) {
    exit('ไม่พบรหัสโครงการ');
}

// ฟังก์ชันสำหรับดึง Tasks แบบ recursive (ดึงทั้ง task หลักและ sub-tasks)
function getTasksHierarchy($condb, $project_id, $parent_id = null) {
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
        ORDER BY t.created_at ASC
    ");
    
    $stmt->execute([$project_id, $parent_id, $parent_id]);
    $tasks = [];
    
    while ($task = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // ดึง sub-tasks recursively
        $task['sub_tasks'] = getTasksHierarchy($condb, $project_id, $task['task_id']);
        $tasks[] = $task;
    }
    
    return $tasks;
}

// ฟังก์ชันแสดงผล Task แต่ละรายการ
function renderTask($task, $level = 0) {
    // ทำความสะอาดข้อมูลก่อนแสดงผล
    $taskId = htmlspecialchars($task['task_id']);
    $taskName = htmlspecialchars($task['task_name']);
    $description = htmlspecialchars($task['description'] ?? '');
    $assignedUsers = htmlspecialchars($task['assigned_users'] ?? 'ไม่มีผู้รับผิดชอบ');
    $startDate = $task['start_date'] ? date('d/m/Y', strtotime($task['start_date'])) : '-';
    $endDate = $task['end_date'] ? date('d/m/Y', strtotime($task['end_date'])) : '-';
    $progress = (int)$task['progress'];
    
    // กำหนด class ตามสถานะ
    $statusClass = match($task['status']) {
        'Pending' => 'badge-warning',
        'In Progress' => 'badge-info',
        'Completed' => 'badge-success',
        'Cancelled' => 'badge-danger',
        default => 'badge-secondary'
    };
    
    // กำหนด class ตามความสำคัญ
    $priorityClass = match($task['priority']) {
        'Low' => 'badge-success',
        'Medium' => 'badge-info',
        'High' => 'badge-warning',
        'Urgent' => 'badge-danger',
        default => 'badge-secondary'
    };

    // สร้าง HTML สำหรับ Task
    $html = "
    <div class='task-item mb-3' id='task-{$taskId}' data-task-id='{$taskId}'>
        <div class='card'>
            <div class='card-header'>
                <div class='d-flex justify-content-between align-items-center'>
                    <div>
                        <h5 class='mb-0 d-flex align-items-center'>
                            <span class='task-name'>{$taskName}</span>
                            <span class='badge {$statusClass} ml-2'>" . htmlspecialchars($task['status']) . "</span>
                            <span class='badge {$priorityClass} ml-2'>" . htmlspecialchars($task['priority']) . "</span>
                        </h5>
                    </div>
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
                </div>
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-md-6'>
                        <p class='mb-1'><strong>รายละเอียด:</strong> {$description}</p>
                        <p class='mb-1'><strong>ผู้รับผิดชอบ:</strong> {$assignedUsers}</p>
                    </div>
                    <div class='col-md-6'>
                        <p class='mb-1'><strong>วันที่เริ่ม:</strong> {$startDate}</p>
                        <p class='mb-1'><strong>วันที่สิ้นสุด:</strong> {$endDate}</p>
                    </div>
                </div>
                <div class='progress mt-2' style='height: 5px;'>
                    <div class='progress-bar' role='progressbar' 
                         style='width: {$progress}%; background-color: " . ($progress == 100 ? '#28a745' : '#17a2b8') . "' 
                         aria-valuenow='{$progress}' aria-valuemin='0' aria-valuemax='100'>
                    </div>
                </div>
                <small class='text-muted'>ความคืบหน้า: {$progress}%</small>
            </div>
        </div>";

    // ถ้ามี sub-tasks ให้แสดงด้วย
    if (!empty($task['sub_tasks'])) {
        $html .= "<div class='sub-tasks pl-4 mt-2'>";
        foreach ($task['sub_tasks'] as $subTask) {
            $html .= renderTask($subTask, $level + 1);
        }
        $html .= "</div>";
    }

    $html .= "</div>";
    return $html;
}

try {
    // ดึงข้อมูล tasks ทั้งหมด
    $tasks = getTasksHierarchy($condb, $project_id);
    
    // แสดงผล
    if (empty($tasks)) {
        echo "<div class='alert alert-info'>ยังไม่มีรายการงาน คลิกปุ่ม \"เพิ่มงานใหม่\" เพื่อเริ่มต้น</div>";
    } else {
        foreach ($tasks as $task) {
            echo renderTask($task);
        }
    }
} catch (Exception $e) {
    // บันทึก error log
    error_log("Error in get_tasks.php: " . $e->getMessage());
    // แสดงข้อความ error แบบ user-friendly
    echo "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการดึงข้อมูล กรุณาลองใหม่อีกครั้ง</div>";
}
?>