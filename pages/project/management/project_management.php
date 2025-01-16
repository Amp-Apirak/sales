<?php
session_start();
include('../../../config/condb.php');

$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];

if (isset($_GET['project_id'])) {
    $encrypted_project_id = urldecode($_GET['project_id']);
    $project_id = decryptUserId($encrypted_project_id);
} else {
    header("Location: /error.php");
    exit();
}

$sql_tasks = "SELECT t.*, u.first_name, u.last_name, 
                     GROUP_CONCAT(CONCAT(u2.first_name, ' ', u2.last_name) SEPARATOR ', ') AS assignees
              FROM project_tasks t
              LEFT JOIN users u ON t.created_by = u.user_id
              LEFT JOIN project_task_assignments a ON t.task_id = a.task_id
              LEFT JOIN users u2 ON a.user_id = u2.user_id
              WHERE t.project_id = :project_id
              GROUP BY t.task_id";

$stmt = $condb->prepare($sql_tasks);
$stmt->bindParam(':project_id', $project_id, PDO::PARAM_STR);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "project"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Project Management</title>
    <?php include '../../../include/header.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <style>
        th,
        h1 {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }

        .custom-th {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 600;
            font-size: 18px;
            color: #FF5733;
        }

        .task-row {
            cursor: pointer;
        }

        .sub-task {
            padding-left: 30px;
            background-color: #f9f9f9;
        }

        .avatar {
            display: inline-block;
            margin-right: 5px;
            cursor: pointer;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #007bff;
            color: white;
            text-align: center;
            line-height: 30px;
        }

        .avatar:hover {
            background-color: #0056b3;
        }

        .task-tree {
            width: 100%;
            border-spacing: 0px 10px;
            border-collapse: separate;
        }

        .task-tree th,
        .task-tree td {
            padding: 12px;
            /* เพิ่ม padding */
            border: 1px solid #ddd;
            /* เส้นขอบ */
            background-color: #ffffff;
            /* สีพื้นหลัง */
        }

        .task-tree .task-name {
            font-weight: bold;
            padding-left: 15px;
        }

        .task-name {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .toggle-icon {
            flex-shrink: 0;
            width: 20px;
            text-align: center;
        }

        .toggle-task {
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }


        .task-tree .sub-task .task-name {
            padding-left: 0;
        }

        .task-tree .task-actions {
            white-space: nowrap;
        }

        .toggle-icon {
            cursor: pointer;
            margin-right: 10px;
            font-size: 18px;
            display: inline-block;
        }

        .toggle-icon::before {
            content: '+';
        }

        .toggle-icon.collapsed::before {
            content: '-';
        }

        .sub-task {
            display: none;
        }

        .sub-task.visible {
            display: table-row;
        }

        .actions {
            display: flex;
            gap: 5px;
        }

        .actions button {
            background: none;
            border: none;
            cursor: pointer;
        }

        .text-nowrap {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
            /* กำหนดความกว้างสูงสุด */
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include  '../../../include/navbar.php'; ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Project Management</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Project Management</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="col-md-12 pb-3">
                                <div class="btn-group float-right">
                                    <a href="add_task.php?project_id=<?php echo urlencode(encryptUserId($project_id)); ?>" class="btn btn-success btn-sm">เพิ่ม Task</a>
                                </div>
                            </div><br>
                            <div class="card">
                                <div class="card-header">
                                    <div class="container-fluid">
                                        <h3 class="card-title">Task List</h3>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="taskTable" class="task-tree">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap text-center">ชื่องาน</th>
                                                <th class="text-nowrap text-center">คำอธิบาย</th>
                                                <th class="text-nowrap text-center">วันที่เริ่ม</th>
                                                <th class="text-nowrap text-center">วันที่สิ้นสุด</th>
                                                <th class="text-nowrap text-center">ผู้รับผิดชอบ</th>
                                                <th class="text-nowrap text-center">สถานะ</th>
                                                <th class="text-nowrap text-center">ความคืบหน้า</th>
                                                <th class="text-nowrap text-center">ระดับความสำคัญ</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            function displayTasks($tasks, $parent_task_id = null, $level = 0)
                                            {
                                                foreach ($tasks as $task) {
                                                    if ($task['parent_task_id'] == $parent_task_id) {
                                                        echo '<tr class="task-row" data-task-id="' . $task['task_id'] . '" data-parent-task-id="' . $task['parent_task_id'] . '">';
                                                        echo '<td class="task-name text-nowrap" style="padding-left: ' . ($level * 30) . 'px;">';
                                                        if (count(array_filter($tasks, fn($t) => $t['parent_task_id'] == $task['task_id'])) > 0 || $level === 0) {
                                                            echo '<span class="toggle-icon"></span>';
                                                        }
                                                        echo htmlspecialchars($task['task_name']);
                                                        echo '</td>';
                                                        echo '<td class="description-cell text-nowrap" data-description="' . htmlspecialchars($task['description']) . '">';
                                                        echo mb_strlen($task['description']) > 100
                                                            ? htmlspecialchars(mb_substr($task['description'], 0, 100)) . '...'
                                                            : htmlspecialchars($task['description']);
                                                        echo '</td>';
                                                        echo '<td class="text-nowrap">' . htmlspecialchars($task['start_date']) . '</td>';
                                                        echo '<td class="text-nowrap">' . htmlspecialchars($task['end_date']) . '</td>';
                                                        echo '<td class="text-nowrap">';
                                                        $assignees = explode(', ', $task['assignees']);
                                                        foreach ($assignees as $assignee) {
                                                            echo '<div class="avatar " title="' . htmlspecialchars($assignee) . '">' . substr($assignee, 0, 1) . '</div>';
                                                        }
                                                        echo '</td>';
                                                        echo '<td class="text-nowrap">' . htmlspecialchars($task['status']) . '</td>';
                                                        echo '<td class="text-nowrap text-center">' . htmlspecialchars($task['progress']) . '%</td>';
                                                        echo '<td class="text-nowrap text-center">' . htmlspecialchars($task['priority']) . '</td>';
                                                        echo '<td class="task-actions actions">';
                                                        echo '<a href="edit_task.php?task_id=' . urlencode(encryptUserId($task['task_id'])) . '" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>';
                                                        echo '<a href="delete_task.php?task_id=' . urlencode(encryptUserId($task['task_id'])) . '" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>';
                                                        echo '<a href="add_subtask.php?parent_task_id=' . urlencode(encryptUserId($task['task_id'])) . '" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i></a>';
                                                        echo '</td>';
                                                        echo '</tr>';
                                                        displayTasks($tasks, $task['task_id'], $level + 1);
                                                    }
                                                }
                                            }
                                            displayTasks($tasks);
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include '../../../include/footer.php'; ?>
    </div>
    <script>
        $(function() {
            // เปิดใช้งาน DataTables
            $('#taskTable').DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true
            });

            // Event สำหรับเปิด Modal เมื่อคลิกที่คำอธิบาย
            $(document).on('click', '.description-cell', function() {
                const description = $(this).data('description'); // ดึงข้อมูลคำอธิบายจาก data attribute
                $('#descriptionContent').text(description); // แสดงคำอธิบายใน Modal
                $('#descriptionModal').modal('show'); // เปิด Modal
            });

            // ซ่อนเฉพาะ Sub Task (แถวที่มี data-parent-task-id)
            // $('tr[data-parent-task-id]').hide();

            // ฟังก์ชันสำหรับการย่อ/ขยาย Task
            $(".toggle-icon, .toggle-task").click(function() {
                const taskRow = $(this).closest('tr');
                const taskId = taskRow.data('task-id');
                const toggleIcon = taskRow.find('.toggle-icon');
                const isExpanded = toggleIcon.hasClass('collapsed');

                // สลับสถานะไอคอน
                toggleIcon.toggleClass('collapsed');

                // ฟังก์ชันสำหรับแสดง/ซ่อน Sub Task
                const toggleSubTasks = (taskId, show) => {
                    $('tr[data-parent-task-id="' + taskId + '"]').each(function() {
                        $(this).toggle(show); // แสดง/ซ่อนแถว
                        const subTaskId = $(this).data('task-id');
                        toggleSubTasks(subTaskId, show && $(this).find('.toggle-icon').hasClass('collapsed'));
                    });
                };

                toggleSubTasks(taskId, !isExpanded); // แสดงหรือซ่อน Sub Task
            });
        });
    </script>
</body>

</html>



<!-- Modal Template -->
<div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="descriptionModalLabel">รายละเอียดคำอธิบาย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="descriptionContent"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>