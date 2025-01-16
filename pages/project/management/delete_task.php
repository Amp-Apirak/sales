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

// ดึงข้อมูล Task จากฐานข้อมูล
$sql_task = "SELECT * FROM project_tasks WHERE task_id = :task_id";
$stmt_task = $condb->prepare($sql_task);
$stmt_task->bindParam(':task_id', $task_id, PDO::PARAM_STR);
$stmt_task->execute();
$task = $stmt_task->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบว่ามีการส่งฟอร์มลบ Task หรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ลบ Task จากฐานข้อมูล
    $sql_delete = "DELETE FROM project_tasks WHERE task_id = :task_id";
    $stmt_delete = $condb->prepare($sql_delete);
    $stmt_delete->bindParam(':task_id', $task_id, PDO::PARAM_STR);

    if ($stmt_delete->execute()) {
        // หากลบ Task สำเร็จ ให้ redirect กลับไปหน้า Project Management
        header("Location: Project_management.php?project_id=" . urlencode(encryptUserId($task['project_id'])));
        exit();
    } else {
        // หากเกิดข้อผิดพลาด
        $error_message = "เกิดข้อผิดพลาดในการลบ Task";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Task</title>
    <?php include '../../../include/header.php'; ?>
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
                            <h1 class="m-0">Delete Task</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Delete Task</li>
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

                            <!-- ฟอร์มลบ Task -->
                            <form action="delete_task.php?task_id=<?php echo urlencode(encryptUserId($task_id)); ?>" method="POST">
                                <div class="alert alert-warning">
                                    คุณแน่ใจหรือไม่ว่าต้องการลบ Task: <strong><?php echo htmlspecialchars($task['task_name']); ?></strong>?
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-danger">ลบ Task</button>
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