<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php"); // Redirect ไปหน้า Login หากไม่ได้เข้าสู่ระบบ
    exit();
}

// รับค่า project_id จาก URL
if (isset($_GET['project_id'])) {
    $encrypted_project_id = urldecode($_GET['project_id']); // ถอดรหัส URL encoding
    $project_id = decryptUserId($encrypted_project_id); // ถอดรหัส project_id
} else {
    // หากไม่มี project_id ใน URL ให้ redirect กลับหรือแสดงข้อความผิดพลาด
    header("Location: /error.php");
    exit();
}

// ตรวจสอบว่ามีการส่งฟอร์มเพิ่ม Task หรือไม่
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
    $created_by = $_SESSION['user_id']; // ใช้ user_id จาก session

    // สร้าง task_id ด้วย UUID
    $task_id = generateUUID();

    // เพิ่ม Task ใหม่ลงในฐานข้อมูล
    $sql = "INSERT INTO project_tasks (task_id, project_id, task_name, description, start_date, end_date, status, progress, priority, created_by) 
            VALUES (:task_id, :project_id, :task_name, :description, :start_date, :end_date, :status, :progress, :priority, :created_by)";

    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':task_id', $task_id, PDO::PARAM_STR);
    $stmt->bindParam(':project_id', $project_id, PDO::PARAM_STR);
    $stmt->bindParam(':task_name', $task_name, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':progress', $progress, PDO::PARAM_STR);
    $stmt->bindParam(':priority', $priority, PDO::PARAM_STR);
    $stmt->bindParam(':created_by', $created_by, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // หากเพิ่ม Task สำเร็จ ให้เพิ่มผู้รับผิดชอบลงในตาราง project_task_assignments
        foreach ($assignees as $user_id) {
            $assignment_id = generateUUID();
            $sql_assign = "INSERT INTO project_task_assignments (assignment_id, task_id, user_id, assigned_by, assigned_at) 
                           VALUES (:assignment_id, :task_id, :user_id, :assigned_by, NOW())";
            $stmt_assign = $condb->prepare($sql_assign);
            $stmt_assign->bindParam(':assignment_id', $assignment_id, PDO::PARAM_STR);
            $stmt_assign->bindParam(':task_id', $task_id, PDO::PARAM_STR);
            $stmt_assign->bindParam(':user_id', $user_id, PDO::PARAM_STR);
            $stmt_assign->bindParam(':assigned_by', $created_by, PDO::PARAM_STR);
            $stmt_assign->execute();
        }

        // Redirect กลับไปหน้า Project Management
        header("Location: Project_management.php?project_id=" . urlencode(encryptUserId($project_id)));
        exit();
    } else {
        // หากเกิดข้อผิดพลาด
        $error_message = "เกิดข้อผิดพลาดในการเพิ่ม Task";
    }
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

// ดึงข้อมูลผู้ใช้ทั้งหมดเพื่อแสดงใน dropdown สำหรับเลือกผู้รับผิดชอบ
$sql_users = "SELECT user_id, first_name, last_name FROM users";
$stmt_users = $condb->query($sql_users);
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
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
                            <h1 class="m-0">Add Task</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Add Task</li>
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

                            <!-- ฟอร์มเพิ่ม Task -->
                            <form action="add_task.php?project_id=<?php echo urlencode(encryptUserId($project_id)); ?>" method="POST">
                                <div class="form-group">
                                    <label for="task_name">ชื่องาน</label>
                                    <input type="text" id="task_name" name="task_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">คำอธิบาย</label>
                                    <textarea id="description" name="description" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="start_date">วันที่เริ่ม</label>
                                    <input type="date" id="start_date" name="start_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="end_date">วันที่สิ้นสุด</label>
                                    <input type="date" id="end_date" name="end_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="status">สถานะ</label>
                                    <select id="status" name="status" required>
                                        <option value="Pending">รอดำเนินการ</option>
                                        <option value="In Progress">กำลังดำเนินการ</option>
                                        <option value="Completed">เสร็จสิ้น</option>
                                        <option value="Cancelled">ยกเลิก</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="progress">ความคืบหน้า (%)</label>
                                    <input type="number" id="progress" name="progress" min="0" max="100" value="0" required>
                                </div>
                                <div class="form-group">
                                    <label for="priority">ระดับความสำคัญ</label>
                                    <select id="priority" name="priority" required>
                                        <option value="Low">ต่ำ</option>
                                        <option value="Medium">กลาง</option>
                                        <option value="High">สูง</option>
                                        <option value="Urgent">ด่วน</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="assignees">ผู้รับผิดชอบ</label>
                                    <select id="assignees" name="assignees[]" multiple required>
                                        <?php foreach ($users as $user) { ?>
                                            <option value="<?php echo $user['user_id']; ?>">
                                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">เพิ่ม Task</button>
                                    <a href="Project_management.php?project_id=<?php echo urlencode(encryptUserId($project_id)); ?>" class="btn btn-secondary">ยกเลิก</a>
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