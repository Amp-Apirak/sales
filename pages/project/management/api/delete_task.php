<?php
include('../../../../config/condb.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_POST['task_id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสงาน']);
    exit;
}

try {
    $condb->beginTransaction();

    // ลบการมอบหมายงานก่อน
    $sql = "DELETE FROM project_task_assignments WHERE task_id = :task_id";
    $stmt = $condb->prepare($sql);
    $stmt->execute([':task_id' => $_POST['task_id']]);

    // ลบงานและงานย่อยทั้งหมด
    function deleteTaskAndSubtasks($condb, $taskId)
    {
        // ค้นหางานย่อย
        $sql = "SELECT task_id FROM project_tasks WHERE parent_task_id = :task_id";
        $stmt = $condb->prepare($sql);
        $stmt->execute([':task_id' => $taskId]);
        $subTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ลบงานย่อยแต่ละงาน
        foreach ($subTasks as $subTask) {
            deleteTaskAndSubtasks($condb, $subTask['task_id']);
        }

        // ลบงานปัจจุบัน
        $sql = "DELETE FROM project_tasks WHERE task_id = :task_id";
        $stmt = $condb->prepare($sql);
        $stmt->execute([':task_id' => $taskId]);
    }

    // เริ่มลบงาน
    deleteTaskAndSubtasks($condb, $_POST['task_id']);

    $condb->commit();
    echo json_encode(['success' => true, 'message' => 'ลบงานสำเร็จ']);
} catch (Exception $e) {
    $condb->rollBack();
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
