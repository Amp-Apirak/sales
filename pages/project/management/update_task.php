<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
include('../../../config/condb.php');

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

// ฟังก์ชันสร้าง UUID
function generateUUID() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$task_id = $_POST['task_id'] ?? null;
$project_id = $_POST['project_id'] ?? null;

if (!$task_id || !$project_id) {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

try {
    // ดึงข้อมูลเก่าก่อนอัปเดต
    $stmt = $condb->prepare("SELECT * FROM project_tasks WHERE task_id = ?");
    $stmt->execute([$task_id]);
    $old_task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$old_task) {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบงานนี้']);
        exit;
    }

    // ข้อมูลใหม่
    $task_name = trim($_POST['task_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? '';
    $priority = $_POST['priority'] ?? '';
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $progress = floatval($_POST['progress'] ?? 0);

    if (empty($task_name)) {
        echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกชื่องาน']);
        exit;
    }

    $condb->beginTransaction();

    // อัปเดต Task
    $stmt = $condb->prepare("
        UPDATE project_tasks
        SET task_name = ?,
            description = ?,
            status = ?,
            priority = ?,
            start_date = ?,
            end_date = ?,
            progress = ?,
            updated_by = ?,
            updated_at = NOW()
        WHERE task_id = ?
    ");

    $stmt->execute([
        $task_name,
        $description,
        $status,
        $priority,
        $start_date ?: null,
        $end_date ?: null,
        $progress,
        $user_id,
        $task_id
    ]);

    // สร้าง System Logs สำหรับการเปลี่ยนแปลงแต่ละฟิลด์
    $changes = [];

    // เปรียบเทียบและสร้าง Log
    if ($old_task['status'] !== $status) {
        $log_id = generateUUID();
        $stmt_log = $condb->prepare("
            INSERT INTO task_comments (comment_id, task_id, project_id, user_id, comment_text, comment_type, old_value, new_value, created_at)
            VALUES (?, ?, ?, ?, ?, 'status_change', ?, ?, NOW())
        ");
        $stmt_log->execute([
            $log_id,
            $task_id,
            $project_id,
            $user_id,
            "เปลี่ยนสถานะจาก {$old_task['status']} เป็น {$status}",
            $old_task['status'],
            $status
        ]);
        $changes[] = 'สถานะ';
    }

    if ($old_task['priority'] !== $priority) {
        $log_id = generateUUID();
        $priority_th = [
            'Low' => 'ต่ำ',
            'Medium' => 'ปานกลาง',
            'High' => 'สูง',
            'Urgent' => 'เร่งด่วน'
        ];
        $old_priority_th = $priority_th[$old_task['priority']] ?? $old_task['priority'];
        $new_priority_th = $priority_th[$priority] ?? $priority;

        $stmt_log = $condb->prepare("
            INSERT INTO task_comments (comment_id, task_id, project_id, user_id, comment_text, comment_type, old_value, new_value, created_at)
            VALUES (?, ?, ?, ?, ?, 'system_log', ?, ?, NOW())
        ");
        $stmt_log->execute([
            $log_id,
            $task_id,
            $project_id,
            $user_id,
            "เปลี่ยนความสำคัญจาก {$old_priority_th} เป็น {$new_priority_th}",
            $old_task['priority'],
            $priority
        ]);
        $changes[] = 'ความสำคัญ';
    }

    if ($old_task['progress'] != $progress) {
        $log_id = generateUUID();
        $stmt_log = $condb->prepare("
            INSERT INTO task_comments (comment_id, task_id, project_id, user_id, comment_text, comment_type, old_value, new_value, created_at)
            VALUES (?, ?, ?, ?, ?, 'progress_update', ?, ?, NOW())
        ");
        $stmt_log->execute([
            $log_id,
            $task_id,
            $project_id,
            $user_id,
            "อัปเดตความคืบหน้าจาก {$old_task['progress']}% เป็น {$progress}%",
            $old_task['progress'],
            $progress
        ]);
        $changes[] = 'ความคืบหน้า';
    }

    if ($old_task['task_name'] !== $task_name) {
        $log_id = generateUUID();
        $stmt_log = $condb->prepare("
            INSERT INTO task_comments (comment_id, task_id, project_id, user_id, comment_text, comment_type, old_value, new_value, created_at)
            VALUES (?, ?, ?, ?, ?, 'system_log', ?, ?, NOW())
        ");
        $stmt_log->execute([
            $log_id,
            $task_id,
            $project_id,
            $user_id,
            "เปลี่ยนชื่องานจาก \"{$old_task['task_name']}\" เป็น \"{$task_name}\"",
            $old_task['task_name'],
            $task_name
        ]);
        $changes[] = 'ชื่องาน';
    }

    if ($old_task['start_date'] !== $start_date || $old_task['end_date'] !== $end_date) {
        $log_id = generateUUID();
        $old_dates = "เริ่ม: " . ($old_task['start_date'] ? date('d/m/Y', strtotime($old_task['start_date'])) : '-') .
                      " สิ้นสุด: " . ($old_task['end_date'] ? date('d/m/Y', strtotime($old_task['end_date'])) : '-');
        $new_dates = "เริ่ม: " . ($start_date ? date('d/m/Y', strtotime($start_date)) : '-') .
                      " สิ้นสุด: " . ($end_date ? date('d/m/Y', strtotime($end_date)) : '-');

        $stmt_log = $condb->prepare("
            INSERT INTO task_comments (comment_id, task_id, project_id, user_id, comment_text, comment_type, old_value, new_value, created_at)
            VALUES (?, ?, ?, ?, ?, 'system_log', ?, ?, NOW())
        ");
        $stmt_log->execute([
            $log_id,
            $task_id,
            $project_id,
            $user_id,
            "เปลี่ยนช่วงเวลา: {$new_dates}",
            $old_dates,
            $new_dates
        ]);
        $changes[] = 'ช่วงเวลา';
    }

    if ($old_task['description'] !== $description) {
        $changes[] = 'รายละเอียด';
    }

    $condb->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'อัปเดตงานสำเร็จ',
        'changes' => $changes
    ]);

} catch (PDOException $e) {
    $condb->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
    error_log("Error updating task: " . $e->getMessage());
}
?>
