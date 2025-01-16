<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}

// รับค่า task_id จาก URL และถอดรหัส
if (isset($_GET['task_id'])) {
    $encrypted_task_id = urldecode($_GET['task_id']); // ถอดรหัส URL encoding
    $task_id = decryptUserId($encrypted_task_id); // ถอดรหัส task_id
} else {
    // หากไม่มี task_id ใน URL ให้ redirect กลับหรือแสดงข้อความผิดพลาด
    header("Location: /error.php");
    exit();
}

// ฟังก์ชันสำหรับสร้าง UUID
function generateUUID()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

// ดึงข้อมูล Task จากฐานข้อมูล
$sql_task = "SELECT * FROM project_tasks WHERE task_id = :task_id";
$stmt_task = $condb->prepare($sql_task);
$stmt_task->bindParam(':task_id', $task_id, PDO::PARAM_STR);
$stmt_task->execute();
$task = $stmt_task->fetch(PDO::FETCH_ASSOC);

// ดึงข้อมูลผู้รับผิดชอบจากตาราง project_task_assignments
$sql_assignees = "SELECT u.user_id, u.first_name, u.last_name 
                  FROM project_task_assignments a
                  JOIN users u ON a.user_id = u.user_id
                  WHERE a.task_id = :task_id";
$stmt_assignees = $condb->prepare($sql_assignees);
$stmt_assignees->bindParam(':task_id', $task_id, PDO::PARAM_STR);
$stmt_assignees->execute();
$assignees = $stmt_assignees->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลผู้ใช้ทั้งหมดเพื่อแสดงใน Dropdown
$sql_users = "SELECT user_id, first_name, last_name FROM users";
$stmt_users = $condb->query($sql_users);
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// ตรวจสอบว่ามีการส่งฟอร์มแก้ไข Task หรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $task_name = $_POST['task_name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $progress = $_POST['progress'];
    $priority = $_POST['priority'];
    $assignees = $_POST['assignees']; // ผู้รับผิดชอบ (Array ของ user_id)
    $updated_by = $_SESSION['user_id']; // ใช้ user_id จาก session

    // อัปเดต Task ในฐานข้อมูล
    $sql_update = "UPDATE project_tasks 
                   SET task_name = :task_name, 
                       description = :description, 
                       start_date = :start_date, 
                       end_date = :end_date, 
                       status = :status, 
                       progress = :progress, 
                       priority = :priority, 
                       updated_by = :updated_by, 
                       updated_at = NOW()
                   WHERE task_id = :task_id";

    $stmt_update = $condb->prepare($sql_update);
    $stmt_update->bindParam(':task_name', $task_name, PDO::PARAM_STR);
    $stmt_update->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt_update->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt_update->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    $stmt_update->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt_update->bindParam(':progress', $progress, PDO::PARAM_STR);
    $stmt_update->bindParam(':priority', $priority, PDO::PARAM_STR);
    $stmt_update->bindParam(':updated_by', $updated_by, PDO::PARAM_STR);
    $stmt_update->bindParam(':task_id', $task_id, PDO::PARAM_STR);

    if ($stmt_update->execute()) {
        // ลบข้อมูลผู้รับผิดชอบเดิม
        $sql_delete_assignees = "DELETE FROM project_task_assignments WHERE task_id = :task_id";
        $stmt_delete_assignees = $condb->prepare($sql_delete_assignees);
        $stmt_delete_assignees->bindParam(':task_id', $task_id, PDO::PARAM_STR);
        $stmt_delete_assignees->execute();

        // เพิ่มข้อมูลผู้รับผิดชอบใหม่
        foreach ($assignees as $user_id) {
            $assignment_id = generateUUID();
            $sql_insert_assignee = "INSERT INTO project_task_assignments (assignment_id, task_id, user_id, assigned_by, assigned_at) 
                                   VALUES (:assignment_id, :task_id, :user_id, :assigned_by, NOW())";
            $stmt_insert_assignee = $condb->prepare($sql_insert_assignee);
            $stmt_insert_assignee->bindParam(':assignment_id', $assignment_id, PDO::PARAM_STR);
            $stmt_insert_assignee->bindParam(':task_id', $task_id, PDO::PARAM_STR);
            $stmt_insert_assignee->bindParam(':user_id', $user_id, PDO::PARAM_STR);
            $stmt_insert_assignee->bindParam(':assigned_by', $updated_by, PDO::PARAM_STR);
            $stmt_insert_assignee->execute();
        }

        // Redirect กลับไปหน้า Project Management
        header("Location: Project_management.php?project_id=" . urlencode(encryptUserId($task['project_id'])));
        exit();
    } else {
        // หากเกิดข้อผิดพลาด
        $error_message = "เกิดข้อผิดพลาดในการอัปเดต Task";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <?php include '../../../include/header.php'; ?>
    <style>
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Navbar -->
        <?php include '../../../include/navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Edit Task</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Edit Task</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- แสดงข้อความผิดพลาด (ถ้ามี) -->
                            <?php if (isset($error_message)) { ?>
                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                            <?php } ?>

                            <!-- ฟอร์มแก้ไข Task -->
                            <form action="edit_task.php?task_id=<?php echo urlencode(encryptUserId($task_id)); ?>" method="POST">
                                <div class="form-group">
                                    <label for="task_name">ชื่องาน</label>
                                    <input type="text" id="task_name" name="task_name" value="<?php echo htmlspecialchars($task['task_name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">คำอธิบาย</label>
                                    <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($task['description']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="start_date">วันที่เริ่ม</label>
                                    <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($task['start_date']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="end_date">วันที่สิ้นสุด</label>
                                    <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($task['end_date']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="status">สถานะ</label>
                                    <select id="status" name="status" required>
                                        <option value="Pending" <?php echo $task['status'] == 'Pending' ? 'selected' : ''; ?>>รอดำเนินการ</option>
                                        <option value="In Progress" <?php echo $task['status'] == 'In Progress' ? 'selected' : ''; ?>>กำลังดำเนินการ</option>
                                        <option value="Completed" <?php echo $task['status'] == 'Completed' ? 'selected' : ''; ?>>เสร็จสิ้น</option>
                                        <option value="Cancelled" <?php echo $task['status'] == 'Cancelled' ? 'selected' : ''; ?>>ยกเลิก</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="progress">ความคืบหน้า (%)</label>
                                    <input type="number" id="progress" name="progress" min="0" max="100" value="<?php echo htmlspecialchars($task['progress']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="priority">ระดับความสำคัญ</label>
                                    <select id="priority" name="priority" required>
                                        <option value="Low" <?php echo $task['priority'] == 'Low' ? 'selected' : ''; ?>>ต่ำ</option>
                                        <option value="Medium" <?php echo $task['priority'] == 'Medium' ? 'selected' : ''; ?>>กลาง</option>
                                        <option value="High" <?php echo $task['priority'] == 'High' ? 'selected' : ''; ?>>สูง</option>
                                        <option value="Urgent" <?php echo $task['priority'] == 'Urgent' ? 'selected' : ''; ?>>ด่วน</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="assignees">ผู้รับผิดชอบ</label>
                                    <select id="assignees" name="assignees[]" multiple required>
                                        <?php foreach ($users as $user) { ?>
                                            <option value="<?php echo $user['user_id']; ?>"
                                                <?php
                                                // ตรวจสอบว่าผู้ใช้นี้เป็นผู้รับผิดชอบหรือไม่
                                                foreach ($assignees as $assignee) {
                                                    if ($assignee['user_id'] == $user['user_id']) {
                                                        echo 'selected';
                                                        break;
                                                    }
                                                }
                                                ?>>
                                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                                    <a href="Project_management.php?project_id=<?php echo urlencode(encryptUserId($task['project_id'])); ?>" class="btn btn-secondary">ยกเลิก</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Footer -->
        <?php include '../../../include/footer.php'; ?>
    </div>
    <!-- ./wrapper -->
</body>

</html>