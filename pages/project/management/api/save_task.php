<?php
session_start();

include('../../../../config/condb.php');

// ฟังก์ชันสร้าง UUID
function generate_uuid() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// ตรวจสอบว่าเป็น POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// รับข้อมูลจาก POST
$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบข้อมูลพื้นฐาน
if (empty($data['project_id']) || empty($data['task_name'])) {
    echo json_encode([
        'success' => false,
        'message' => 'กรุณากรอกข้อมูลที่จำเป็น'
    ]);
    exit;
}

try {
    $condb->beginTransaction();

    // ตรวจสอบว่าเป็นการเพิ่มใหม่หรือแก้ไข
    $isNewTask = empty($data['task_id']);
    $taskId = $isNewTask ? generate_uuid() : $data['task_id'];

    if ($isNewTask) {
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
            created_by
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
            :created_by
        )";
    } else {
        $sql = "UPDATE project_tasks SET
            task_name = :task_name,
            description = :description,
            parent_task_id = :parent_task_id,
            start_date = :start_date,
            end_date = :end_date,
            status = :status,
            progress = :progress,
            priority = :priority,
            updated_by = :created_by,
            updated_at = CURRENT_TIMESTAMP
            WHERE task_id = :task_id AND project_id = :project_id";
    }

    $stmt = $condb->prepare($sql);

    // กำหนดค่าพารามิเตอร์พื้นฐาน
    $params = [
        ':task_id' => $taskId,
        ':project_id' => $data['project_id'],
        ':parent_task_id' => !empty($data['parent_task_id']) ? $data['parent_task_id'] : null,
        ':task_name' => $data['task_name'],
        ':description' => $data['description'] ?? '',
        ':start_date' => $data['start_date'] ?? date('Y-m-d'),
        ':end_date' => $data['end_date'] ?? date('Y-m-d'),
        ':status' => $data['status'] ?? 'Pending',
        ':progress' => $data['progress'] ?? 0,
        ':priority' => $data['priority'] ?? 'Medium',
        ':created_by' => $_SESSION['user_id']
    ];

    $stmt->execute($params);

    // จัดการผู้รับผิดชอบ ถ้ามี
    if (!empty($data['assignee'])) {
        // ลบของเดิม
        $stmt = $condb->prepare("DELETE FROM project_task_assignments WHERE task_id = ?");
        $stmt->execute([$taskId]);

        // เพิ่มใหม่
        $stmt = $condb->prepare("INSERT INTO project_task_assignments (
            assignment_id, task_id, user_id, assigned_by
        ) VALUES (?, ?, ?, ?)");
        
        $stmt->execute([
            generate_uuid(),
            $taskId,
            $data['assignee'],
            $_SESSION['user_id']
        ]);
    }

    $condb->commit();

    // ดึงข้อมูลงานที่เพิ่ม/แก้ไข เพื่อส่งกลับ
    $stmt = $condb->prepare("
        SELECT t.*, 
               u.first_name as assigned_to_name,
               c.first_name as created_by_name
        FROM project_tasks t
        LEFT JOIN project_task_assignments ta ON t.task_id = ta.task_id
        LEFT JOIN users u ON ta.user_id = u.user_id
        LEFT JOIN users c ON t.created_by = c.user_id
        WHERE t.task_id = ?
    ");
    $stmt->execute([$taskId]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => $isNewTask ? 'บันทึกข้อมูลสำเร็จ' : 'อัปเดตข้อมูลสำเร็จ',
        'task' => $task
    ]);

} catch (Exception $e) {
    $condb->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
        'error' => $e->getTrace()
    ]);
}
?>