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

// รับค่า parent_task_id จาก URL และถอดรหัส
if (isset($_GET['parent_task_id'])) {
    $encrypted_parent_task_id = urldecode($_GET['parent_task_id']); // ถอดรหัส URL encoding
    $parent_task_id = decryptUserId($encrypted_parent_task_id); // ถอดรหัส parent_task_id
} else {
    // หากไม่มี parent_task_id ใน URL ให้ redirect กลับหรือแสดงข้อความผิดพลาด
    header("Location: /error.php");
    exit();
}

// ดึงข้อมูล Task หลัก
$sql_parent_task = "SELECT * FROM project_tasks WHERE task_id = :parent_task_id";
$stmt_parent_task = $condb->prepare($sql_parent_task);
$stmt_parent_task->bindParam(':parent_task_id', $parent_task_id, PDO::PARAM_STR);
$stmt_parent_task->execute();
$parent_task = $stmt_parent_task->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบว่ามีการส่งฟอร์มเพิ่ม Sub Task หรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $task_name = $_POST['task_name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $progress = $_POST['progress'];
    $priority = $_POST['priority'];
    $created_by = $_SESSION['user_id']; // ใช้ user_id จาก session

    // สร้าง task_id ด้วย UUID
    $task_id = generateUUID();

    // เพิ่ม Sub Task ลงในฐานข้อมูล
    $sql_insert = "INSERT INTO project_tasks (task_id, project_id, parent_task_id, task_name, description, start_date, end_date, status, progress, priority, created_by) 
                   VALUES (:task_id, :project_id, :parent_task_id, :task_name, :description, :start_date, :end_date, :status, :progress, :priority, :created_by)";

    $stmt_insert = $condb->prepare($sql_insert);
    $stmt_insert->bindParam(':task_id', $task_id, PDO::PARAM_STR);
    $stmt_insert->bindParam(':project_id', $parent_task['project_id'], PDO::PARAM_STR);
    $stmt_insert->bindParam(':parent_task_id', $parent_task_id, PDO::PARAM_STR);
    $stmt_insert->bindParam(':task_name', $task_name, PDO::PARAM_STR);
    $stmt_insert->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt_insert->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt_insert->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    $stmt_insert->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt_insert->bindParam(':progress', $progress, PDO::PARAM_STR);
    $stmt_insert->bindParam(':priority', $priority, PDO::PARAM_STR);
    $stmt_insert->bindParam(':created_by', $created_by, PDO::PARAM_STR);

    if ($stmt_insert->execute()) {
        // หากเพิ่ม Sub Task สำเร็จ ให้ redirect กลับไปหน้า Project Management
        header("Location: Project_management.php?project_id=" . urlencode(encryptUserId($parent_task['project_id'])));
        exit();
    } else {
        // หากเกิดข้อผิดพลาด
        $error_message = "เกิดข้อผิดพลาดในการเพิ่ม Sub Task";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sub Task</title>
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
                            <h1 class="m-0">Add Sub Task</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Add Sub Task</li>
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

                            <!-- ฟอร์มเพิ่ม Sub Task -->
                            <form action="add_subtask.php?parent_task_id=<?php echo urlencode(encryptUserId($parent_task_id)); ?>" method="POST">
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
                                    <button type="submit" class="btn btn-primary">เพิ่ม Sub Task</button>
                                    <a href="Project_management.php?project_id=<?php echo urlencode(encryptUserId($parent_task['project_id'])); ?>" class="btn btn-secondary">ยกเลิก</a>
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