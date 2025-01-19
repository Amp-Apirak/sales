<?php
session_start();
include('../../../config/condb.php');

// ฟังก์ชันสร้าง UUID
function generateUUID()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// ตรวจสอบว่าเป็น POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

try {
    // เริ่ม Transaction
    $condb->beginTransaction();

    // รับค่าจากฟอร์ม
    $input = $_POST;

    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($input['project_id']) || empty($input['task_name'])) {
        throw new Exception('กรุณากรอกข้อมูลที่จำเป็น');
    }

    // สร้าง task_id ใหม่ถ้าเป็นการเพิ่มใหม่
    $isNewTask = empty($input['task_id']);
    $taskId = $isNewTask ? generateUUID() : $input['task_id'];

    if ($isNewTask) {
        // SQL สำหรับเพิ่มข้อมูลใหม่
        $sql = "INSERT INTO project_tasks (
                    task_id,
                    project_id,
                    parent_task_id,
                    task_name,
                    description,
                    start_date,
                    end_date,
                    status,
                    progress,
                    priority,
                    created_by,
                    created_at
                ) VALUES (
                    :task_id,
                    :project_id,
                    :parent_task_id,
                    :task_name,
                    :description,
                    :start_date,
                    :end_date,
                    :status,
                    :progress,
                    :priority,
                    :created_by,
                    CURRENT_TIMESTAMP
                )";

        $stmt = $condb->prepare($sql);
        $stmt->execute([
            ':task_id' => $taskId,
            ':project_id' => $input['project_id'],
            ':parent_task_id' => !empty($input['parent_task_id']) ? $input['parent_task_id'] : null,
            ':task_name' => $input['task_name'],
            ':description' => $input['description'] ?? null,
            ':start_date' => $input['start_date'] ?? null,
            ':end_date' => $input['end_date'] ?? null,
            ':status' => $input['status'] ?? 'Pending',
            ':progress' => $input['progress'] ?? 0,
            ':priority' => $input['priority'] ?? 'Medium',
            ':created_by' => $_SESSION['user_id']
        ]);
    } else {
        // SQL สำหรับอัพเดทข้อมูล
        $sql = "UPDATE project_tasks SET 
                    task_name = :task_name,
                    description = :description,
                    start_date = :start_date,
                    end_date = :end_date,
                    status = :status,
                    progress = :progress,
                    priority = :priority,
                    updated_by = :updated_by,
                    updated_at = CURRENT_TIMESTAMP
                WHERE task_id = :task_id";

        $stmt = $condb->prepare($sql);
        $stmt->execute([
            ':task_id' => $taskId,
            ':task_name' => $input['task_name'],
            ':description' => $input['description'] ?? null,
            ':start_date' => $input['start_date'] ?? null,
            ':end_date' => $input['end_date'] ?? null,
            ':status' => $input['status'] ?? 'Pending',
            ':progress' => $input['progress'] ?? 0,
            ':priority' => $input['priority'] ?? 'Medium',
            ':updated_by' => $_SESSION['user_id']
        ]);
    }

    // จัดการผู้รับผิดชอบงาน
    if (isset($input['assigned_users'])) {
        // ลบผู้รับผิดชอบเดิม
        $stmt = $condb->prepare("DELETE FROM project_task_assignments WHERE task_id = ?");
        $stmt->execute([$taskId]);

        // เพิ่มผู้รับผิดชอบใหม่
        if (!empty($input['assigned_users'])) {
            $stmt = $condb->prepare("
                INSERT INTO project_task_assignments (
                    assignment_id,
                    task_id,
                    user_id,
                    assigned_by,
                    assigned_at
                ) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
            ");

            foreach ($input['assigned_users'] as $userId) {
                $stmt->execute([
                    generateUUID(),
                    $taskId,
                    $userId,
                    $_SESSION['user_id']
                ]);
            }
        }
    }

    // Commit transaction
    $condb->commit();

    // ส่ง Response กลับ
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => $isNewTask ? 'เพิ่มข้อมูลสำเร็จ' : 'อัปเดตข้อมูลสำเร็จ',
        'task_id' => $taskId
    ]);
} catch (Exception $e) {
    // Rollback หากเกิดข้อผิดพลาด
    $condb->rollBack();

    // Log error
    error_log("Error in save_task.php: " . $e->getMessage());

    // ส่ง Response error กลับ
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
