<?php
session_start();

require_once '../../../config/condb.php';
$projectId = $_GET['project_id'] ?? '';
$format = strtolower($_GET['format'] ?? 'csv');

if (empty($projectId)) {
    http_response_code(400);
    echo 'Missing project_id';
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$userRole = $_SESSION['role'] ?? '';
$currentTeamId = $_SESSION['team_id'] ?? '';
$userTeamIds = $_SESSION['team_ids'] ?? [];

if (!$userId) {
    http_response_code(401);
    echo 'Unauthorized';
    exit;
}

try {
    $stmtProject = $condb->prepare(
        "SELECT 
            p.project_id,
            p.project_name,
            p.created_by,
            p.seller,
            seller_team.team_id AS seller_team_id,
            (
                SELECT pm.is_active
                FROM project_members pm
                WHERE pm.project_id = p.project_id
                AND pm.user_id = :user_id
                LIMIT 1
            ) AS membership_status
        FROM projects p
        LEFT JOIN user_teams seller_ut 
            ON p.seller = seller_ut.user_id AND seller_ut.is_primary = 1
        LEFT JOIN teams seller_team 
            ON seller_ut.team_id = seller_team.team_id
        WHERE p.project_id = :project_id
        LIMIT 1"
    );
    $stmtProject->execute([
        ':user_id' => $userId,
        ':project_id' => $projectId
    ]);

    $project = $stmtProject->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        http_response_code(404);
        echo 'Project not found';
        exit;
    }

    $hasAccess = false;
    $isMember = !is_null($project['membership_status']);

    switch ($userRole) {
        case 'Executive':
            $hasAccess = true;
            break;
        case 'Sale Supervisor':
            if ($project['created_by'] === $userId || $project['seller'] === $userId || $isMember) {
                $hasAccess = true;
                break;
            }

            $sellerTeamId = $project['seller_team_id'] ?? null;
            if ($currentTeamId === 'ALL') {
                if ($sellerTeamId && is_array($userTeamIds) && in_array($sellerTeamId, $userTeamIds, true)) {
                    $hasAccess = true;
                }
            } elseif (!empty($currentTeamId) && $sellerTeamId && $sellerTeamId === $currentTeamId) {
                $hasAccess = true;
            }
            break;
        default:
            if (
                $project['created_by'] === $userId ||
                $project['seller'] === $userId ||
                $isMember
            ) {
                $hasAccess = true;
            }
            break;
    }

    if (!$hasAccess) {
        http_response_code(403);
        echo 'Access denied';
        exit;
    }

    $assignmentStmt = $condb->prepare(
        "SELECT CONCAT(u.first_name, ' ', u.last_name) AS full_name
         FROM project_task_assignments ta
         JOIN users u ON ta.user_id = u.user_id
         WHERE ta.task_id = :task_id
         ORDER BY full_name"
    );

    $tasks = [];

    $rootStmt = $condb->prepare(
        "SELECT t.*, CONCAT(creator.first_name, ' ', creator.last_name) AS creator_name
         FROM project_tasks t
         LEFT JOIN users creator ON t.created_by = creator.user_id
         WHERE t.project_id = :project_id AND t.parent_task_id IS NULL
         ORDER BY t.task_order ASC, t.created_at ASC"
    );
    $rootStmt->execute([':project_id' => $projectId]);

    $childStmt = $condb->prepare(
        "SELECT t.*, CONCAT(creator.first_name, ' ', creator.last_name) AS creator_name
         FROM project_tasks t
         LEFT JOIN users creator ON t.created_by = creator.user_id
         WHERE t.project_id = :project_id AND t.parent_task_id = :parent_id
         ORDER BY t.task_order ASC, t.created_at ASC"
    );

    $fetchTasks = function (
        $stmt,
        $projectId,
        $parentId,
        $level,
        array $path = [],
        string $parentDisplayCode = ''
    ) use (&$fetchTasks, &$tasks, $childStmt, $assignmentStmt) {
        $params = [':project_id' => $projectId];
        if ($parentId === null) {
            $stmt->execute($params);
        } else {
            $params[':parent_id'] = $parentId;
            $stmt->execute($params);
        }

        $index = 0;

        while ($task = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $index++;
            $currentPath = array_merge($path, [$index]);

            $assignmentStmt->execute([':task_id' => $task['task_id']]);
            $assignedUsers = $assignmentStmt->fetchAll(PDO::FETCH_COLUMN);
            $assignmentStmt->closeCursor();

            $task['assigned_users'] = implode(', ', array_filter($assignedUsers));
            $task['level'] = $level;
            $task['hierarchy_path'] = implode('.', $currentPath);

            if ($level === 0) {
                $task['display_code'] = 'T' . str_pad((string)$index, 3, '0', STR_PAD_LEFT);
            } else {
                $prefix = $parentDisplayCode !== '' ? $parentDisplayCode . '.' : '';
                $task['display_code'] = $prefix . $index;
            }

            $tasks[] = $task;

            $fetchTasks(
                $childStmt,
                $projectId,
                $task['task_id'],
                $level + 1,
                $currentPath,
                $task['display_code']
            );
        }

        $stmt->closeCursor();
    };

    $fetchTasks($rootStmt, $projectId, null, 0, []);

    if (empty($tasks)) {
        $tasks = [];
    }

    $columns = [
        'ลำดับงาน',
        'โครงสร้างงาน',
        'ID งาน',
        'ชื่อ Task',
        'รายละเอียด',
        'สถานะ',
        'ความคืบหน้า (%)',
        'ระดับความสำคัญ',
        'วันเริ่ม',
        'วันสิ้นสุด',
        'ผู้รับผิดชอบ',
        'ผู้สร้าง',
        'วันที่สร้าง',
        'วันที่แก้ไขล่าสุด'
    ];

    $format = in_array($format, ['csv', 'excel'], true) ? $format : 'csv';

    $filenameBase = 'project_tasks_' . preg_replace('/[^A-Za-z0-9_-]+/', '_', $project['project_name'] ?? 'export');
    $filenameBase = trim($filenameBase, '_');
    if ($filenameBase === '') {
        $filenameBase = 'project_tasks';
    }
    $timestamp = date('Ymd_His');

    if ($format === 'csv') {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filenameBase . '_' . $timestamp . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, $columns);

        foreach ($tasks as $index => $task) {
            $indent = str_repeat('  ', (int)($task['level'] ?? 0));
            $row = [
                ($index + 1),
                $task['display_code'] ?? '',
                $task['task_id'] ?? '',
                $indent . ($task['task_name'] ?? ''),
                preg_replace('/\s+/', ' ', $task['description'] ?? ''),
                $task['status'] ?? '',
                isset($task['progress']) ? (float)$task['progress'] : '',
                $task['priority'] ?? '',
                formatDate($task['start_date'] ?? null),
                formatDate($task['end_date'] ?? null),
                $task['assigned_users'] ?? '',
                $task['creator_name'] ?? '',
                formatDateTime($task['created_at'] ?? null),
                formatDateTime($task['updated_at'] ?? null)
            ];
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filenameBase . '_' . $timestamp . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo "\xEF\xBB\xBF";
    echo '<html><head><meta charset="UTF-8"></head><body>';
    echo '<table border="1">';
    echo '<thead><tr>';
    foreach ($columns as $column) {
        echo '<th>' . htmlspecialchars($column, ENT_QUOTES, 'UTF-8') . '</th>';
    }
    echo '</tr></thead><tbody>';

    foreach ($tasks as $index => $task) {
        echo '<tr>';
        echo '<td>' . ($index + 1) . '</td>';
        echo '<td>' . htmlspecialchars($task['display_code'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($task['task_id'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', (int)($task['level'] ?? 0));
        echo '<td>' . $indent . htmlspecialchars($task['task_name'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $description = htmlspecialchars($task['description'] ?? '', ENT_QUOTES, 'UTF-8');
        echo '<td>' . nl2br($description) . '</td>';
        echo '<td>' . htmlspecialchars($task['status'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . (isset($task['progress']) ? htmlspecialchars(number_format((float)$task['progress'], 2), ENT_QUOTES, 'UTF-8') : '') . '</td>';
        echo '<td>' . htmlspecialchars($task['priority'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars(formatDate($task['start_date'] ?? null), ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars(formatDate($task['end_date'] ?? null), ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($task['assigned_users'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($task['creator_name'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars(formatDateTime($task['created_at'] ?? null), ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars(formatDateTime($task['updated_at'] ?? null), ENT_QUOTES, 'UTF-8') . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</body></html>';
    exit;
} catch (PDOException $e) {
    error_log('Task export failed: ' . $e->getMessage());
    http_response_code(500);
    echo 'Internal Server Error';
    exit;
}

function formatDate(?string $date): string
{
    if (empty($date) || $date === '0000-00-00' || $date === '1970-01-01') {
        return '';
    }

    try {
        return (new DateTime($date))->format('d/m/Y');
    } catch (Exception $e) {
        return '';
    }
}

function formatDateTime(?string $dateTime): string
{
    if (empty($dateTime) || $dateTime === '0000-00-00 00:00:00') {
        return '';
    }

    try {
        return (new DateTime($dateTime))->format('d/m/Y H:i');
    } catch (Exception $e) {
        return '';
    }
}
