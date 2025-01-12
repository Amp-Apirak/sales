<?php
// pages/project/management/get_tasks.php
session_start();
include('../../../config/condb.php');

try {
    $project_id = $_GET['project_id'];

    // Debug
    error_log("Loading tasks for project_id: " . $project_id);

    $sql = "SELECT t.*, 
            GROUP_CONCAT(CONCAT(u.first_name, ' ', u.last_name) SEPARATOR ', ') as assigned_users,
            COUNT(st.task_id) as subtask_count
            FROM project_tasks t
            LEFT JOIN project_task_assignments ta ON t.task_id = ta.task_id
            LEFT JOIN users u ON ta.user_id = u.user_id
            LEFT JOIN project_tasks st ON st.parent_task_id = t.task_id
            WHERE t.project_id = :project_id 
            AND t.parent_task_id IS NULL
            GROUP BY t.task_id
            ORDER BY t.created_at";

    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':project_id', $project_id);
    $stmt->execute();

    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug
    error_log("Found " . count($tasks) . " tasks");

    // Get subtasks for each task
    foreach ($tasks as &$task) {
        $task['subtasks'] = getSubtasks($condb, $task['task_id']);
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $tasks
    ]);
} catch (Exception $e) {
    error_log("Error in get_tasks.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function getSubtasks($condb, $parent_id)
{
    $sql = "SELECT t.*, 
            GROUP_CONCAT(CONCAT(u.first_name, ' ', u.last_name) SEPARATOR ', ') as assigned_users
            FROM project_tasks t
            LEFT JOIN project_task_assignments ta ON t.task_id = ta.task_id
            LEFT JOIN users u ON ta.user_id = u.user_id
            WHERE t.parent_task_id = :parent_id
            GROUP BY t.task_id
            ORDER BY t.created_at";

    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':parent_id', $parent_id);
    $stmt->execute();

    $subtasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($subtasks as &$subtask) {
        $subtask['subtasks'] = getSubtasks($condb, $subtask['task_id']);
    }

    return $subtasks;
}
