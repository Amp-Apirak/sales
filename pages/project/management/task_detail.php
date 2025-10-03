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

    $hasAccess = $access_check->fetch() || ($role === 'Executive');

    if (!$hasAccess) {
        $_SESSION['error'] = "คุณไม่มีสิทธิ์เข้าถึงงานนี้";
        header("Location: ../project.php");
        exit;
    }

    // ตรวจสอบสิทธิ์การแก้ไขผู้รับผิดชอบ
    // สามารถแก้ไขได้เฉพาะ: Executive, ผู้สร้าง, ผู้รับผิดชอบปัจจุบัน, Project Manager
    $canEditAssignee = false;

    // Executive สามารถแก้ไขได้เสมอ
    if ($role === 'Executive') {
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
        /* ==== Main Layout ==== */
        .task-detail-container {
            background: #f5f7fa;
            min-height: 100vh;
            padding: 1.5rem 0;
        }

        /* ==== Task Header Card ==== */
        .task-header-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin-bottom: 1.5rem;
        }

        .task-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .task-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.875rem;
        }

        .meta-item i {
            color: #94a3b8;
        }

        /* ==== Activity Feed (Chat Board) ==== */
        .activity-feed-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .feed-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .feed-header h3 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .feed-body {
            max-height: 600px;
            overflow-y: auto;
            padding: 1.5rem;
            background: #fafbfc;
        }

        /* ==== Comment Item ==== */
        .comment-item {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            transition: all 0.2s;
            animation: slideIn 0.3s ease-out;
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
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
            margin-right: 0.75rem;
        }

        .comment-user-info {
            flex: 1;
        }

        .comment-username {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.9375rem;
        }

        .comment-time {
            color: #94a3b8;
            font-size: 0.8125rem;
        }

        .comment-content {
            color: #475569;
            line-height: 1.6;
            margin-top: 0.5rem;
            white-space: pre-wrap;
            word-break: break-word;
        }

        /* ==== System Log Style ==== */
        .comment-item.system-log {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-left: 4px solid #0ea5e9;
        }

        .comment-item.system-log .comment-content {
            color: #0c4a6e;
            font-size: 0.875rem;
        }

        /* ==== Attachments ==== */
        .comment-attachments {
            margin-top: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .attachment-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
            cursor: pointer;
            max-width: 300px;
        }

        .attachment-item:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }

        .attachment-icon {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
        }

        .attachment-icon.file-image { background: #dbeafe; color: #1e40af; }
        .attachment-icon.file-pdf { background: #fee2e2; color: #991b1b; }
        .attachment-icon.file-word { background: #dbeafe; color: #1e3a8a; }
        .attachment-icon.file-excel { background: #dcfce7; color: #14532d; }
        .attachment-icon.file-zip { background: #fef3c7; color: #78350f; }
        .attachment-icon.file-default { background: #f3f4f6; color: #374151; }

        .attachment-info {
            flex: 1;
            min-width: 0;
        }

        .attachment-name {
            font-weight: 500;
            color: #1e293b;
            font-size: 0.875rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .attachment-size {
            color: #94a3b8;
            font-size: 0.75rem;
        }

        /* ==== Comment Input Area ==== */
        .comment-input-area {
            background: white;
            padding: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .comment-textarea {
            width: 100%;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            font-size: 0.9375rem;
            resize: vertical;
            min-height: 100px;
            transition: all 0.2s;
            font-family: inherit;
        }

        .comment-textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .comment-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .btn-post-comment {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-post-comment:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-attach-file {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-attach-file:hover {
            background: #e2e8f0;
            border-color: #cbd5e1;
        }

        /* ==== File Preview List ==== */
        .file-preview-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .file-preview-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .file-preview-item .remove-file {
            cursor: pointer;
            color: #ef4444;
            margin-left: 0.5rem;
        }

        /* ==== Empty State ==== */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* ==== Progress Bar ==== */
        .task-progress-bar {
            background: #e2e8f0;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .task-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
            transition: width 0.3s;
        }

        /* ==== Responsive ==== */
        @media (max-width: 768px) {
            .task-meta {
                gap: 1rem;
            }

            .feed-body {
                max-height: 400px;
            }

            .comment-actions {
                flex-direction: column;
                align-items: stretch;
            }
        }

        /* ==== Scrollbar Styling ==== */
        .feed-body::-webkit-scrollbar {
            width: 8px;
        }

        .feed-body::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .feed-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .feed-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Badge สถานะ */
        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8125rem;
            font-weight: 500;
        }

        .status-pending { background: #fef3c7; color: #78350f; }
        .status-in-progress { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        /* Priority Badge */
        .priority-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8125rem;
            font-weight: 500;
        }

        .priority-low { background: #e0f2fe; color: #075985; }
        .priority-medium { background: #fef3c7; color: #78350f; }
        .priority-high { background: #fed7aa; color: #9a3412; }
        .priority-urgent { background: #fee2e2; color: #991b1b; }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../../include/navbar.php'; ?>

        <div class="content-wrapper task-detail-container">
            <div class="container-fluid" style="max-width: 100%; padding: 0 2rem;">
                <!-- Task Header -->
                <div class="task-header-card">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div style="flex: 1; min-width: 300px;">
                            <a href="../view_project.php?project_id=<?php echo urlencode(encryptUserId($project_id)); ?>&tab=tasks" class="text-muted mb-2 d-inline-block">
                                <i class="fas fa-arrow-left mr-1"></i> กลับไปยังโครงการ: <?php echo htmlspecialchars($task['project_name']); ?>
                            </a>
                            <div class="d-flex align-items-center gap-3">
                                <h1 class="task-title mb-0">
                                    <?php echo htmlspecialchars($task['task_name']); ?>
                                </h1>
                                <button class="btn btn-sm btn-outline-primary" onclick="showEditTaskModal()" title="แก้ไขข้อมูลงาน">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </button>
                            </div>
                        </div>

                            <div class="task-meta">
                                <div class="meta-item">
                                    <i class="fas fa-flag"></i>
                                    <span>สถานะ:</span>
                                    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $task['status'])); ?>">
                                        <?php echo htmlspecialchars($task['status']); ?>
                                    </span>
                                </div>

                                <div class="meta-item">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>ความสำคัญ:</span>
                                    <span class="priority-badge priority-<?php echo strtolower($task['priority']); ?>">
                                        <?php
                                        $priority_th = ['Low' => 'ต่ำ', 'Medium' => 'ปานกลาง', 'High' => 'สูง', 'Urgent' => 'เร่งด่วน'];
                                        echo $priority_th[$task['priority']] ?? $task['priority'];
                                        ?>
                                    </span>
                                </div>

                                <div class="meta-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>เริ่ม:</span>
                                    <strong><?php echo $task['start_date'] ? date('d/m/Y', strtotime($task['start_date'])) : '-'; ?></strong>
                                </div>

                                <div class="meta-item">
                                    <i class="fas fa-calendar-times"></i>
                                    <span>สิ้นสุด:</span>
                                    <strong><?php echo $task['end_date'] ? date('d/m/Y', strtotime($task['end_date'])) : '-'; ?></strong>
                                </div>

                                <div class="meta-item">
                                    <i class="fas fa-user-plus"></i>
                                    <span>สร้างโดย:</span>
                                    <strong><?php echo htmlspecialchars($task['creator_name']); ?></strong>
                                </div>

                                <div class="meta-item">
                                    <i class="fas fa-users"></i>
                                    <span>ผู้รับผิดชอบ:</span>
                                    <strong><?php echo htmlspecialchars($task['assigned_users'] ?: 'ยังไม่กำหนด'); ?></strong>
                                </div>

                                <div class="meta-item">
                                    <i class="fas fa-comment"></i>
                                    <span><?php echo $task['comment_count']; ?> ความคิดเห็น</span>
                                </div>

                                <div class="meta-item">
                                    <i class="fas fa-paperclip"></i>
                                    <span><?php echo $task['attachment_count']; ?> ไฟล์แนบ</span>
                                </div>
                            </div>

                            <?php if (!empty($task['description'])): ?>
                            <div class="mt-3">
                                <h6 class="text-muted mb-2">รายละเอียดงาน:</h6>
                                <p class="text-secondary" style="white-space: pre-wrap;"><?php echo htmlspecialchars($task['description']); ?></p>
                            </div>
                            <?php endif; ?>

                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">ความคืบหน้า:</span>
                                    <strong><?php echo number_format($task['progress'], 0); ?>%</strong>
                                </div>
                                <div class="task-progress-bar">
                                    <div class="task-progress-fill" style="width: <?php echo $task['progress']; ?>%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Feed (Chat Board) -->
                <div class="activity-feed-card">
                    <div class="feed-header">
                        <h3><i class="fas fa-comments mr-2"></i>Activity Log & Comments</h3>
                        <span class="badge badge-light" id="total-comments"><?php echo $task['comment_count']; ?></span>
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

                            <div class="comment-actions">
                                <button type="submit" class="btn-post-comment">
                                    <i class="fas fa-paper-plane mr-2"></i>โพสต์ความคิดเห็น
                                </button>

                                <label for="file-upload" class="btn-attach-file mb-0">
                                    <i class="fas fa-paperclip"></i>
                                    <span>แนบไฟล์</span>
                                    <input type="file" id="file-upload" name="attachments[]" multiple style="display: none;" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.txt">
                                </label>

                                <small class="text-muted">รองรับไฟล์: รูปภาพ, PDF, Word, Excel, ZIP, TXT (สูงสุด 10 MB ต่อไฟล์)</small>
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
