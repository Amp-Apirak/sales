<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
session_start();
include '../../../config/condb.php';

// ตรวจสอบ Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../index.php");
    exit;
}

// ดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;

// รับ task_id จาก URL
$task_id = $_GET['task_id'] ?? null;

if (!$task_id) {
    $_SESSION['error'] = "ไม่พบรหัสงาน";
    header("Location: ../project.php");
    exit;
}

// ดึงข้อมูล Task พร้อมโครงการและผู้รับผิดชอบ
try {
    $sql = "SELECT
                t.*,
                p.project_name,
                p.project_id,
                p.status as project_status,
                CONCAT(creator.first_name, ' ', creator.last_name) as creator_name,
                -- ดึงรายชื่อผู้รับผิดชอบ
                GROUP_CONCAT(
                    DISTINCT CONCAT(assigned_user.first_name, ' ', assigned_user.last_name)
                    SEPARATOR ', '
                ) as assigned_users,
                -- นับจำนวน comments
                (SELECT COUNT(*) FROM task_comments WHERE task_id = t.task_id AND is_deleted = 0) as comment_count,
                -- นับจำนวน attachments
                (SELECT COUNT(*) FROM task_comment_attachments WHERE task_id = t.task_id) as attachment_count
            FROM project_tasks t
            INNER JOIN projects p ON t.project_id = p.project_id
            LEFT JOIN users creator ON t.created_by = creator.user_id
            LEFT JOIN project_task_assignments pta ON t.task_id = pta.task_id
            LEFT JOIN users assigned_user ON pta.user_id = assigned_user.user_id
            WHERE t.task_id = ?
            GROUP BY t.task_id";

    $stmt = $condb->prepare($sql);
    $stmt->execute([$task_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        $_SESSION['error'] = "ไม่พบงานที่ต้องการ";
        header("Location: ../project.php");
        exit;
    }

    $project_id = $task['project_id'];

    // ตรวจสอบสิทธิ์การเข้าถึง (ต้องเป็นสมาชิกในโครงการหรือผู้รับผิดชอบงาน)
    $access_check = $condb->prepare("
        SELECT 1 FROM (
            SELECT user_id FROM project_members WHERE project_id = ? AND user_id = ?
            UNION
            SELECT user_id FROM project_task_assignments WHERE task_id = ? AND user_id = ?
            UNION
            SELECT created_by as user_id FROM project_tasks WHERE task_id = ? AND created_by = ?
            UNION
            SELECT seller as user_id FROM projects WHERE project_id = ? AND seller = ?
        ) as access_list
    ");
    $access_check->execute([$project_id, $user_id, $task_id, $user_id, $task_id, $user_id, $project_id, $user_id]);

    $hasAccess = $access_check->fetch() || ($role === 'Executive') || ($role === 'Account Management');

    if (!$hasAccess) {
        $_SESSION['error'] = "คุณไม่มีสิทธิ์เข้าถึงงานนี้";
        header("Location: ../project.php");
        exit;
    }

    // ตรวจสอบสิทธิ์การแก้ไขผู้รับผิดชอบ
    // สามารถแก้ไขได้เฉพาะ: Executive, ผู้สร้าง, ผู้รับผิดชอบปัจจุบัน, Project Manager
    $canEditAssignee = false;

    // Executive และ Account Management สามารถแก้ไขได้เสมอ
    if ($role === 'Executive' || $role === 'Account Management') {
        $canEditAssignee = true;
    }

    // ผู้สร้างงานสามารถแก้ไขได้
    if ($task['created_by'] === $user_id) {
        $canEditAssignee = true;
    }

    // ตรวจสอบว่าเป็นผู้รับผิดชอบปัจจุบันหรือไม่
    $assignee_check = $condb->prepare("SELECT user_id FROM project_task_assignments WHERE task_id = ? AND user_id = ?");
    $assignee_check->execute([$task_id, $user_id]);
    if ($assignee_check->fetch()) {
        $canEditAssignee = true;
    }

    // ตรวจสอบว่าเป็น Project Manager หรือไม่
    $pm_check = $condb->prepare("
        SELECT 1 FROM project_members pm
        INNER JOIN project_roles pr ON pm.role_id = pr.role_id
        WHERE pm.project_id = ? AND pm.user_id = ? AND pr.role_name = 'Project Manager'
    ");
    $pm_check->execute([$project_id, $user_id]);
    if ($pm_check->fetch()) {
        $canEditAssignee = true;
    }

    // ดึงรายชื่อสมาชิกในโครงการสำหรับ dropdown
    // รวมจากหลายแหล่ง: project_members, task_assignments, created_by, seller
    $members_stmt = $condb->prepare("
        SELECT DISTINCT u.user_id, CONCAT(u.first_name, ' ', u.last_name) as full_name
        FROM users u
        WHERE u.user_id IN (
            -- สมาชิกในโครงการ
            SELECT pm.user_id FROM project_members pm
            WHERE pm.project_id = ? AND pm.is_active = 1
            UNION
            -- ผู้รับผิดชอบงานในโครงการ
            SELECT pta.user_id FROM project_task_assignments pta
            INNER JOIN project_tasks pt ON pta.task_id = pt.task_id
            WHERE pt.project_id = ?
            UNION
            -- ผู้สร้างงานในโครงการ
            SELECT pt.created_by FROM project_tasks pt
            WHERE pt.project_id = ?
            UNION
            -- ผู้ขาย/เจ้าของโครงการ
            SELECT p.seller FROM projects p
            WHERE p.project_id = ?
        )
        ORDER BY u.first_name, u.last_name
    ");
    $members_stmt->execute([$project_id, $project_id, $project_id, $project_id]);
    $project_members = $members_stmt->fetchAll(PDO::FETCH_ASSOC);

    // ดึงผู้รับผิดชอบปัจจุบัน
    $current_assignees_stmt = $condb->prepare("
        SELECT pta.user_id, CONCAT(u.first_name, ' ', u.last_name) as full_name
        FROM project_task_assignments pta
        INNER JOIN users u ON pta.user_id = u.user_id
        WHERE pta.task_id = ?
    ");
    $current_assignees_stmt->execute([$task_id]);
    $current_assignees = $current_assignees_stmt->fetchAll(PDO::FETCH_ASSOC);
    $current_assignee_ids = array_column($current_assignees, 'user_id');

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// ดึงรายชื่อผู้ใช้ทั้งหมดสำหรับ mention
$stmt_users = $condb->prepare("SELECT user_id, first_name, last_name FROM users ORDER BY first_name, last_name");
$stmt_users->execute();
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// สร้างหรือดึง CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "project"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดงาน - <?php echo htmlspecialchars($task['task_name']); ?></title>
    <?php include '../../../include/header.php'; ?>

    <!-- Emoji Picker -->
    <link href="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.css" rel="stylesheet">

    <style>
        /* ==== Modern Clean Design ==== */
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #2563eb;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius: 0.5rem;
            --radius-lg: 0.75rem;
        }

        /* ==== Main Layout ==== */
        .task-detail-container {
            background: var(--gray-50);
            min-height: 100vh;
            padding: 2rem 1rem;
            overflow-x: hidden;
        }

        .task-detail-container .container-fluid {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
            width: 100%;
            max-width: 100%;
        }

        /* ==== Back Button ==== */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-600);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            transition: all 0.2s;
        }

        .back-link:hover {
            color: var(--primary-color);
            gap: 0.75rem;
        }

        /* ==== Task Header Card ==== */
        .task-header-card {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--gray-200);
        }

        .task-title-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .task-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
            line-height: 1.3;
            word-wrap: break-word;
        }

        .task-actions {
            display: flex;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .btn-edit {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: white;
            color: var(--primary-color);
            border: 1.5px solid var(--primary-color);
            border-radius: var(--radius);
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-edit:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* ==== Task Meta Clean Layout ==== */
        .task-meta-section {
            margin-bottom: 2rem;
        }

        .task-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem 2rem;
            padding: 1.5rem 0;
            border-top: 2px solid var(--gray-100);
            border-bottom: 2px solid var(--gray-100);
        }

        .meta-item-clean {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .meta-label-clean {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-500);
            font-size: 0.8125rem;
            font-weight: 500;
        }

        .meta-label-clean i {
            font-size: 0.875rem;
            color: var(--gray-400);
        }

        .meta-value-clean {
            color: var(--gray-900);
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.4;
        }

        .meta-value-clean.text-muted {
            color: var(--gray-500);
            font-weight: 400;
        }

        /* ==== Progress Section ==== */
        .progress-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1.25rem;
            border-radius: var(--radius);
            color: white;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .progress-label {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .progress-percentage {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .progress-bar-wrapper {
            background: rgba(255, 255, 255, 0.2);
            height: 0.5rem;
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: white;
            border-radius: 9999px;
            transition: width 0.3s ease;
        }

        /* ==== Description Section ==== */
        .description-section {
            background: var(--gray-50);
            padding: 1.25rem;
            border-radius: var(--radius);
            border: 1px solid var(--gray-200);
        }

        .description-title {
            color: var(--gray-700);
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .description-text {
            color: var(--gray-600);
            font-size: 0.9375rem;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* ==== Activity Feed ==== */
        .activity-feed-card {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid var(--gray-200);
        }

        .feed-header {
            background: white;
            padding: 1.5rem;
            border-bottom: 2px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .feed-header h3 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .comment-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .feed-body {
            max-height: 600px;
            overflow-y: auto;
            padding: 1.5rem;
            background: #f8fafc;
            background-image:
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 40px,
                    rgba(0, 0, 0, 0.02) 40px,
                    rgba(0, 0, 0, 0.02) 41px
                );
        }

        /* ==== Comment Item (Chat Block Style) ==== */
        .comment-item {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            transition: all 0.2s;
            animation: slideIn 0.3s ease-out;
            border: 1px solid transparent;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .comment-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            border-color: var(--gray-200);
        }

        .comment-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.875rem;
        }

        .comment-user-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);
        }

        .comment-user-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .comment-username {
            font-weight: 600;
            color: var(--gray-900);
            font-size: 0.9375rem;
        }

        .comment-time {
            color: var(--gray-500);
            font-size: 0.8125rem;
        }

        .comment-actions {
            display: flex;
            gap: 0.5rem;
            margin-left: auto;
        }

        .comment-actions .btn {
            padding: 0.375rem 0.625rem;
            font-size: 0.8125rem;
            border-radius: 6px;
        }

        .comment-actions .btn:hover {
            transform: translateY(-1px);
        }

        .comment-content {
            color: var(--gray-700);
            line-height: 1.6;
            font-size: 0.9375rem;
            white-space: pre-wrap;
            word-break: break-word;
            overflow-wrap: break-word;
            padding-left: 0;
            background: var(--gray-50);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 0.5rem;
        }

        /* ==== System Log Style (Activity Log) ==== */
        .comment-item.system-log {
            background: linear-gradient(135deg, #e0f2fe 0%, #bfdbfe 100%);
            border-left: 4px solid #0284c7;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.15);
        }

        .comment-item.system-log .user-avatar {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        }

        .comment-item.system-log .comment-content {
            background: white;
            color: #0c4a6e;
            font-size: 0.875rem;
            font-weight: 500;
            border-left: 3px solid #0ea5e9;
        }

        /* ==== Attachments ==== */
        .comment-attachments {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed var(--gray-300);
            display: flex;
            flex-wrap: wrap;
            gap: 0.625rem;
        }

        .attachment-item {
            background: white;
            border: 1.5px solid var(--gray-300);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
            cursor: pointer;
            max-width: 320px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .attachment-item:hover {
            border-color: var(--primary-color);
            background: #eff6ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.15);
        }

        .attachment-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .attachment-icon.file-image { background: #dbeafe; color: #1e40af; }
        .attachment-icon.file-pdf { background: #fee2e2; color: #991b1b; }
        .attachment-icon.file-word { background: #dbeafe; color: #1e3a8a; }
        .attachment-icon.file-excel { background: #dcfce7; color: #14532d; }
        .attachment-icon.file-zip { background: #fef3c7; color: #78350f; }
        .attachment-icon.file-default { background: var(--gray-100); color: var(--gray-600); }

        .attachment-info {
            flex: 1;
            min-width: 0;
        }

        .attachment-name {
            font-weight: 500;
            color: var(--gray-900);
            font-size: 0.8125rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .attachment-size {
            color: var(--gray-500);
            font-size: 0.6875rem;
        }

        /* ==== Comment Input Area ==== */
        .comment-input-area {
            background: var(--gray-50);
            padding: 1.5rem;
            border-top: 2px solid var(--gray-200);
        }

        .comment-textarea {
            width: 100%;
            border: 1.5px solid var(--gray-300);
            border-radius: var(--radius);
            padding: 0.875rem;
            font-size: 0.9375rem;
            resize: vertical;
            min-height: 100px;
            transition: all 0.2s;
            font-family: inherit;
            background: white;
        }

        .comment-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .comment-form-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .btn-post-comment {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.625rem 1.5rem;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-post-comment:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-post-comment:active {
            transform: translateY(0);
        }

        .btn-attach-file {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            color: var(--gray-700);
            border: 1.5px solid var(--gray-300);
            padding: 0.625rem 1.25rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .btn-attach-file:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
        }

        /* ==== File Preview List ==== */
        .file-preview-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.75rem;
        }

        .file-preview-item {
            background: white;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            padding: 0.5rem 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8125rem;
            color: var(--gray-700);
        }

        .file-preview-item .remove-file {
            cursor: pointer;
            color: var(--danger-color);
            margin-left: 0.25rem;
            transition: transform 0.2s;
        }

        .file-preview-item .remove-file:hover {
            transform: scale(1.2);
        }

        /* ==== Empty State ==== */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--gray-400);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* ==== Responsive ==== */
        @media (max-width: 768px) {
            .task-detail-container {
                padding: 1rem 0;
            }

            .task-detail-container .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .task-header-card {
                padding: 1rem;
            }

            .task-title {
                font-size: 1.25rem;
            }

            .task-meta {
                gap: 0.75rem;
            }

            .feed-body {
                max-height: 400px;
            }

            .comment-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .feed-header {
                padding: 1rem;
            }

            .comment-input-area {
                padding: 1rem;
            }
        }

        @media (max-width: 576px) {
            .task-title {
                font-size: 1.1rem;
            }

            .btn-outline-primary {
                font-size: 0.875rem;
                padding: 0.25rem 0.5rem;
            }
        }

        /* ==== Scrollbar Styling ==== */
        .feed-body::-webkit-scrollbar {
            width: 6px;
        }

        .feed-body::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        .feed-body::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 3px;
        }

        .feed-body::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }

        /* ==== Status & Priority Badges ==== */
        .status-badge, .priority-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.025em;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde047;
        }

        .status-in-progress {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .priority-low {
            background: #e0f2fe;
            color: #075985;
            border: 1px solid #7dd3fc;
        }

        .priority-medium {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde047;
        }

        .priority-high {
            background: #fed7aa;
            color: #9a3412;
            border: 1px solid #fdba74;
        }

        .priority-urgent {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../../include/navbar.php'; ?>

        <div class="content-wrapper task-detail-container">
            <div class="container-fluid" style="max-width: 100%; padding-left: 1.5rem; padding-right: 1.5rem;">
                <!-- Back Link -->
                <a href="../view_project.php?project_id=<?php echo urlencode(encryptUserId($project_id)); ?>&tab=tasks" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    <span>กลับไปยัง: <?php echo htmlspecialchars($task['project_name']); ?></span>
                </a>

                <!-- Task Header Card -->
                <div class="task-header-card">
                    <!-- Title Section -->
                    <div class="task-title-section">
                        <h1 class="task-title">
                            <?php echo htmlspecialchars($task['task_name']); ?>
                        </h1>
                        <div class="task-actions">
                            <button class="btn-edit" onclick="showEditTaskModal()">
                                <i class="fas fa-edit"></i>
                                <span>แก้ไข</span>
                            </button>
                        </div>
                    </div>

                    <!-- Meta Information Grid (Clean Layout) -->
                    <div class="task-meta-section">
                        <div class="task-meta-grid">
                            <div class="meta-item-clean">
                                <div class="meta-label-clean">
                                    <i class="fas fa-flag"></i>
                                    <span>สถานะ</span>
                                </div>
                                <div class="meta-value-clean">
                                    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $task['status'])); ?>">
                                        <?php
                                        $status_th = ['Pending' => 'รอดำเนินการ', 'In Progress' => 'กำลังดำเนินการ', 'Completed' => 'เสร็จสิ้น', 'Cancelled' => 'ยกเลิก'];
                                        echo $status_th[$task['status']] ?? $task['status'];
                                        ?>
                                    </span>
                                </div>
                            </div>

                            <div class="meta-item-clean">
                                <div class="meta-label-clean">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>ความสำคัญ</span>
                                </div>
                                <div class="meta-value-clean">
                                    <span class="priority-badge priority-<?php echo strtolower($task['priority']); ?>">
                                        <?php
                                        $priority_th = ['Low' => 'ต่ำ', 'Medium' => 'ปานกลาง', 'High' => 'สูง', 'Urgent' => 'เร่งด่วน'];
                                        echo $priority_th[$task['priority']] ?? $task['priority'];
                                        ?>
                                    </span>
                                </div>
                            </div>

                            <div class="meta-item-clean">
                                <div class="meta-label-clean">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>วันที่เริ่ม</span>
                                </div>
                                <div class="meta-value-clean">
                                    <?php echo $task['start_date'] ? date('d/m/Y', strtotime($task['start_date'])) : '<span class="text-muted">ไม่ระบุ</span>'; ?>
                                </div>
                            </div>

                            <div class="meta-item-clean">
                                <div class="meta-label-clean">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>วันที่สิ้นสุด</span>
                                </div>
                                <div class="meta-value-clean">
                                    <?php echo $task['end_date'] ? date('d/m/Y', strtotime($task['end_date'])) : '<span class="text-muted">ไม่ระบุ</span>'; ?>
                                </div>
                            </div>

                            <div class="meta-item-clean">
                                <div class="meta-label-clean">
                                    <i class="fas fa-user-circle"></i>
                                    <span>สร้างโดย</span>
                                </div>
                                <div class="meta-value-clean">
                                    <?php echo htmlspecialchars($task['creator_name']); ?>
                                </div>
                            </div>

                            <div class="meta-item-clean">
                                <div class="meta-label-clean">
                                    <i class="fas fa-users"></i>
                                    <span>ผู้รับผิดชอบ</span>
                                </div>
                                <div class="meta-value-clean">
                                    <?php echo htmlspecialchars($task['assigned_users'] ?: 'ยังไม่กำหนด'); ?>
                                </div>
                            </div>

                            <div class="meta-item-clean">
                                <div class="meta-label-clean">
                                    <i class="fas fa-comment-dots"></i>
                                    <span>ความคิดเห็น</span>
                                </div>
                                <div class="meta-value-clean">
                                    <?php echo $task['comment_count']; ?> ข้อความ
                                </div>
                            </div>

                            <div class="meta-item-clean">
                                <div class="meta-label-clean">
                                    <i class="fas fa-paperclip"></i>
                                    <span>ไฟล์แนบ</span>
                                </div>
                                <div class="meta-value-clean">
                                    <?php echo $task['attachment_count']; ?> ไฟล์
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Section -->
                    <div class="progress-section">
                        <div class="progress-header">
                            <span class="progress-label">ความคืบหน้า</span>
                            <span class="progress-percentage"><?php echo number_format($task['progress'], 0); ?>%</span>
                        </div>
                        <div class="progress-bar-wrapper">
                            <div class="progress-bar-fill" style="width: <?php echo $task['progress']; ?>%;"></div>
                        </div>
                    </div>

                    <!-- Description Section -->
                    <?php if (!empty($task['description'])): ?>
                    <div class="description-section mt-3">
                        <div class="description-title">
                            <i class="fas fa-align-left"></i>
                            <span>รายละเอียดงาน</span>
                        </div>
                        <div class="description-text">
                            <?php echo htmlspecialchars($task['description']); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Activity Feed (Chat Board) -->
                <div class="activity-feed-card">
                    <div class="feed-header">
                        <h3>
                            <i class="fas fa-comments"></i>
                            <span>Activity Log & Comments</span>
                        </h3>
                        <span class="comment-badge" id="total-comments"><?php echo $task['comment_count']; ?></span>
                    </div>

                    <div class="feed-body" id="comments-container">
                        <!-- Comments will be loaded here via AJAX -->
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        </div>
                    </div>

                    <div class="comment-input-area">
                        <form id="commentForm" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

                            <textarea
                                class="comment-textarea"
                                id="comment_text"
                                name="comment_text"
                                placeholder="เขียนความคิดเห็น, อัปเดตความคืบหน้า หรือ @ mention ผู้ร่วมงาน..."
                                required
                            ></textarea>

                            <div id="file-preview-list" class="file-preview-list"></div>

                            <div class="comment-form-actions">
                                <button type="submit" class="btn-post-comment">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>โพสต์ความคิดเห็น</span>
                                </button>

                                <label for="file-upload" class="btn-attach-file mb-0">
                                    <i class="fas fa-paperclip"></i>
                                    <span>แนบไฟล์</span>
                                    <input type="file" id="file-upload" name="attachments[]" multiple style="display: none;" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.txt">
                                </label>

                                <small class="text-muted" style="flex-basis: 100%;">รองรับไฟล์: รูปภาพ, PDF, Word, Excel, ZIP, TXT (สูงสุด 10 MB ต่อไฟล์)</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php include '../../../include/footer.php'; ?>
    </div>

    <!-- Modal แก้ไข Task -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>แก้ไขข้อมูลงาน
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm">
                        <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>ชื่องาน <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="task_name"
                                           value="<?php echo htmlspecialchars($task['task_name']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>สถานะ <span class="text-danger">*</span></label>
                                    <select class="form-control" name="status" id="edit_status" required>
                                        <option value="Pending" <?php echo $task['status'] == 'Pending' ? 'selected' : ''; ?>>รอดำเนินการ</option>
                                        <option value="In Progress" <?php echo $task['status'] == 'In Progress' ? 'selected' : ''; ?>>กำลังดำเนินการ</option>
                                        <option value="Completed" <?php echo $task['status'] == 'Completed' ? 'selected' : ''; ?>>เสร็จสิ้น</option>
                                        <option value="Cancelled" <?php echo $task['status'] == 'Cancelled' ? 'selected' : ''; ?>>ยกเลิก</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>รายละเอียด</label>
                            <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($task['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>วันที่เริ่ม</label>
                                    <input type="date" class="form-control" name="start_date"
                                           value="<?php echo $task['start_date']; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>วันที่สิ้นสุด</label>
                                    <input type="date" class="form-control" name="end_date"
                                           value="<?php echo $task['end_date']; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>ความคืบหน้า (%)</label>
                                    <input type="number" class="form-control" name="progress" id="edit_progress"
                                           min="0" max="100" value="<?php echo $task['progress']; ?>">
                                    <div class="progress mt-2" style="height: 8px;">
                                        <div class="progress-bar bg-success" id="edit_progress_bar"
                                             style="width: <?php echo $task['progress']; ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>ระดับความสำคัญ</label>
                                    <select class="form-control" name="priority" id="edit_priority">
                                        <option value="Low" <?php echo $task['priority'] == 'Low' ? 'selected' : ''; ?>>ต่ำ</option>
                                        <option value="Medium" <?php echo $task['priority'] == 'Medium' ? 'selected' : ''; ?>>ปานกลาง</option>
                                        <option value="High" <?php echo $task['priority'] == 'High' ? 'selected' : ''; ?>>สูง</option>
                                        <option value="Urgent" <?php echo $task['priority'] == 'Urgent' ? 'selected' : ''; ?>>เร่งด่วน</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>ผู้รับผิดชอบ</label>
                                    <?php if ($canEditAssignee): ?>
                                        <select class="form-control select2" name="assigned_users[]" id="edit_assigned_users" multiple="multiple" style="width: 100%;">
                                            <?php
                                            if (empty($project_members)) {
                                                echo '<option value="" disabled>ไม่พบสมาชิกในโครงการ</option>';
                                            } else {
                                                foreach ($project_members as $member):
                                            ?>
                                                <option value="<?php echo $member['user_id']; ?>"
                                                    <?php echo in_array($member['user_id'], $current_assignee_ids) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($member['full_name']); ?>
                                                </option>
                                            <?php
                                                endforeach;
                                            }
                                            ?>
                                        </select>
                                        <small class="text-muted">
                                            สามารถเลือกได้หลายคน
                                            (มีสมาชิก: <?php echo count($project_members); ?> คน,
                                            ผู้รับผิดชอบปัจจุบัน: <?php echo count($current_assignee_ids); ?> คน)
                                        </small>
                                    <?php else: ?>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($task['assigned_users'] ?: 'ยังไม่กำหนด'); ?>" disabled>
                                        <small class="text-muted">ไม่มีสิทธิ์แก้ไขผู้รับผิดชอบ</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>ยกเลิก
                    </button>
                    <button type="button" class="btn btn-primary" onclick="updateTask()">
                        <i class="fas fa-save mr-1"></i>บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const taskId = '<?php echo $task_id; ?>';
        const projectId = '<?php echo $project_id; ?>';
        const currentUserId = '<?php echo $user_id; ?>';
        const csrfToken = '<?php echo $csrf_token; ?>';

        // Load comments on page load
        $(document).ready(function() {
            loadComments();

            // Auto refresh every 30 seconds
            setInterval(loadComments, 30000);
        });

        // Load comments via AJAX
        function loadComments() {
            $.ajax({
                url: 'get_task_comments.php',
                type: 'GET',
                data: { task_id: taskId },
                success: function(response) {
                    $('#comments-container').html(response);
                    scrollToBottom();
                },
                error: function() {
                    $('#comments-container').html('<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>ไม่สามารถโหลดความคิดเห็นได้</p></div>');
                }
            });
        }

        // Scroll to bottom of feed
        function scrollToBottom() {
            const feedBody = document.querySelector('.feed-body');
            if (feedBody) {
                feedBody.scrollTop = feedBody.scrollHeight;
            }
        }

        // Handle file selection
        $('#file-upload').on('change', function() {
            const files = this.files;
            const previewList = $('#file-preview-list');
            previewList.empty();

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB

                if (file.size > 10 * 1024 * 1024) {
                    alert(`ไฟล์ "${file.name}" มีขนาดใหญ่เกิน 10 MB`);
                    continue;
                }

                const fileItem = $(`
                    <div class="file-preview-item" data-index="${i}">
                        <i class="fas fa-file mr-2"></i>
                        <span>${file.name} (${fileSize} MB)</span>
                        <i class="fas fa-times remove-file" onclick="removeFile(${i})"></i>
                    </div>
                `);
                previewList.append(fileItem);
            }
        });

        // Remove file from preview
        function removeFile(index) {
            const fileInput = document.getElementById('file-upload');
            const dt = new DataTransfer();
            const files = fileInput.files;

            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    dt.items.add(files[i]);
                }
            }

            fileInput.files = dt.files;
            $(fileInput).trigger('change');
        }

        // Submit comment form
        $('#commentForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = $(this).find('button[type="submit"]');

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>กำลังโพสต์...');

            $.ajax({
                url: 'post_comment.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        // ตรวจสอบว่า response เป็น string หรือ object
                        let result;
                        if (typeof response === 'string') {
                            result = JSON.parse(response);
                        } else if (typeof response === 'object') {
                            result = response;
                        } else {
                            throw new Error('Invalid response format');
                        }

                        if (result.status === 'success') {
                            // Reset form
                            $('#commentForm')[0].reset();
                            $('#file-preview-list').empty();

                            // Reload comments
                            loadComments();

                            // Update comment count
                            const newCount = parseInt($('#total-comments').text()) + 1;
                            $('#total-comments').text(newCount);

                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: 'โพสต์ความคิดเห็นแล้ว',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            throw new Error(result.message || 'เกิดข้อผิดพลาด');
                        }
                    } catch (e) {
                        console.error('Response:', response);
                        console.error('Error:', e);
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'โพสต์ความคิดเห็นไม่สำเร็จ กรุณาลองใหม่อีกครั้ง'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถโพสต์ความคิดเห็นได้'
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i>โพสต์ความคิดเห็น');
                }
            });
        });

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // แก้ไข Comment
        function editComment(commentId, currentText) {
            Swal.fire({
                title: 'แก้ไขความคิดเห็น',
                input: 'textarea',
                inputValue: currentText,
                inputAttributes: {
                    rows: 5,
                    style: 'resize: vertical;'
                },
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#667eea',
                preConfirm: (text) => {
                    if (!text || text.trim() === '') {
                        Swal.showValidationMessage('กรุณากรอกความคิดเห็น');
                        return false;
                    }
                    return text;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'edit_comment.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            comment_id: commentId,
                            comment_text: result.value
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'สำเร็จ!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    loadComments();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: response.message
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถแก้ไขความคิดเห็นได้'
                            });
                        }
                    });
                }
            });
        }

        // ลบ Comment
        function deleteComment(commentId) {
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: 'คุณต้องการลบความคิดเห็นนี้ใช่หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'delete_comment.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            comment_id: commentId
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'ลบสำเร็จ!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    loadComments();
                                    // ลดจำนวน comment count
                                    const currentCount = parseInt($('#total-comments').text());
                                    $('#total-comments').text(Math.max(0, currentCount - 1));
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: response.message
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถลบความคิดเห็นได้'
                            });
                        }
                    });
                }
            });
        }

        // แสดง Modal แก้ไข Task
        function showEditTaskModal() {
            $('#editTaskModal').modal('show');
        }

        // Initialize Select2 เมื่อ Modal แสดงเสร็จ
        <?php if ($canEditAssignee): ?>
        $(document).ready(function() {
            // ตรวจสอบว่า Select2 โหลดแล้ว
            if (typeof $.fn.select2 === 'undefined') {
                console.error('Select2 is not loaded!');
                return;
            }
            console.log('Select2 is available');

            $('#editTaskModal').on('shown.bs.modal', function () {
                console.log('Modal shown, initializing Select2...');

                // ทำลาย Select2 เก่าก่อน (ถ้ามี)
                if ($('#edit_assigned_users').hasClass("select2-hidden-accessible")) {
                    console.log('Destroying old Select2...');
                    $('#edit_assigned_users').select2('destroy');
                }

                // ตรวจสอบ element
                console.log('Select element:', $('#edit_assigned_users').length);
                console.log('Options count:', $('#edit_assigned_users option').length);

                // Initialize Select2 ใหม่
                try {
                    $('#edit_assigned_users').select2({
                        placeholder: 'เลือกผู้รับผิดชอบ',
                        allowClear: true,
                        theme: 'bootstrap4',
                        dropdownParent: $('#editTaskModal'),
                        width: '100%'
                    });
                    console.log('Select2 initialized successfully');
                } catch (e) {
                    console.error('Select2 initialization error:', e);
                }
            });
        });
        <?php endif; ?>

        // อัปเดต Progress Bar แบบ Real-time
        $('#edit_progress').on('input', function() {
            const value = $(this).val();
            $('#edit_progress_bar').css('width', value + '%');
        });

        // บันทึกการแก้ไข Task พร้อม Log
        function updateTask() {
            const formData = $('#editTaskForm').serialize();

            $.ajax({
                url: 'update_task.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editTaskModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสำเร็จ!',
                            text: 'ข้อมูลงานถูกอัปเดตแล้ว',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Reload หน้าเพื่อแสดงข้อมูลใหม่
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: response.message || 'ไม่สามารถบันทึกข้อมูลได้'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
                    });
                }
            });
        }
    </script>
</body>
</html>
