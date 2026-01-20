<?php
session_start();
include('../../../config/condb.php');

$task_id = $_GET['task_id'] ?? null;

if (!$task_id) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>ไม่พบรหัสงาน</p></div>';
    exit;
}

// ฟังก์ชันสร้าง UUID
function generateUUID() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// ฟังก์ชันคำนวณเวลาที่ผ่านมา
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'เมื่อสักครู่';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' นาทีที่แล้ว';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' ชั่วโมงที่แล้ว';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' วันที่แล้ว';
    } else {
        return date('d/m/Y H:i', $timestamp);
    }
}

// ฟังก์ชันสร้างตัวย่อชื่อ
function getInitials($name) {
    $words = explode(' ', $name);
    if (count($words) >= 2) {
        return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    }
    return strtoupper(substr($name, 0, 2));
}

// ฟังก์ชันกำหนดสีตามชื่อ
function getAvatarColor($name) {
    $colors = [
        '#667eea', '#764ba2', '#f093fb', '#4facfe',
        '#43e97b', '#fa709a', '#fee140', '#30cfd0',
        '#a8edea', '#fed6e3', '#c471f5', '#fa8bff'
    ];
    $index = ord(strtoupper($name[0])) % count($colors);
    return $colors[$index];
}

// ฟังก์ชันกำหนดไอคอนตามนามสกุลไฟล์
function getFileIcon($extension) {
    $extension = strtolower($extension);
    $icons = [
        'jpg' => ['icon' => 'fa-file-image', 'class' => 'file-image'],
        'jpeg' => ['icon' => 'fa-file-image', 'class' => 'file-image'],
        'png' => ['icon' => 'fa-file-image', 'class' => 'file-image'],
        'gif' => ['icon' => 'fa-file-image', 'class' => 'file-image'],
        'pdf' => ['icon' => 'fa-file-pdf', 'class' => 'file-pdf'],
        'doc' => ['icon' => 'fa-file-word', 'class' => 'file-word'],
        'docx' => ['icon' => 'fa-file-word', 'class' => 'file-word'],
        'xls' => ['icon' => 'fa-file-excel', 'class' => 'file-excel'],
        'xlsx' => ['icon' => 'fa-file-excel', 'class' => 'file-excel'],
        'zip' => ['icon' => 'fa-file-archive', 'class' => 'file-zip'],
        'rar' => ['icon' => 'fa-file-archive', 'class' => 'file-zip'],
        'txt' => ['icon' => 'fa-file-alt', 'class' => 'file-default'],
    ];
    return $icons[$extension] ?? ['icon' => 'fa-file', 'class' => 'file-default'];
}

// ฟังก์ชันแปลงขนาดไฟล์
function formatBytes($bytes) {
    if ($bytes == 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

try {
    // ดึง comments พร้อม user info และ attachments
    $sql = "SELECT
                tc.comment_id,
                tc.task_id,
                tc.user_id,
                tc.comment_text,
                tc.comment_type,
                tc.old_value,
                tc.new_value,
                tc.created_at,
                tc.updated_at,
                tc.is_edited,
                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                u.email as user_email
            FROM task_comments tc
            LEFT JOIN users u ON tc.user_id = u.user_id
            WHERE tc.task_id = ? AND tc.is_deleted = 0
            ORDER BY tc.created_at ASC";

    $stmt = $condb->prepare($sql);
    $stmt->execute([$task_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($comments)) {
        echo '<div class="empty-state">
                <i class="fas fa-comments"></i>
                <p>ยังไม่มีความคิดเห็น</p>
                <small class="text-muted">เป็นคนแรกที่แสดงความคิดเห็นในงานนี้</small>
              </div>';
        exit;
    }

    // แสดง comments
    foreach ($comments as $comment) {
        $initials = getInitials($comment['user_name']);
        $avatarColor = getAvatarColor($comment['user_name']);
        $timeAgo = timeAgo($comment['created_at']);
        $isSystemLog = ($comment['comment_type'] !== 'comment');

        // ดึงไฟล์แนบของ comment นี้
        $stmt_files = $condb->prepare("
            SELECT * FROM task_comment_attachments
            WHERE comment_id = ?
            ORDER BY uploaded_at ASC
        ");
        $stmt_files->execute([$comment['comment_id']]);
        $attachments = $stmt_files->fetchAll(PDO::FETCH_ASSOC);

        // เริ่มต้น HTML
        $commentClass = $isSystemLog ? 'comment-item system-log' : 'comment-item';
        echo "<div class='{$commentClass}'>";

        // Header
        echo "<div class='comment-header'>";
        echo "<div class='comment-user-section'>";

        // Avatar
        if (!$isSystemLog) {
            echo "<div class='user-avatar' style='background: {$avatarColor};'>{$initials}</div>";
        } else {
            echo "<div class='user-avatar'><i class='fas fa-robot'></i></div>";
        }

        echo "<div class='comment-user-info'>";
        echo "<div class='comment-username'>" . htmlspecialchars($comment['user_name']) . "</div>";
        echo "<div class='comment-time'>";
        echo "<i class='far fa-clock' style='margin-right: 0.25rem;'></i>{$timeAgo}";
        if ($comment['is_edited']) {
            echo " <span style='color: #9ca3af; font-size: 0.75rem;'>(แก้ไขแล้ว)</span>";
        }
        echo "</div>";
        echo "</div>";
        echo "</div>"; // close comment-user-section

        // ปุ่มแก้ไข/ลบ (เฉพาะเจ้าของ Comment)
        $current_user_id = $_SESSION['user_id'] ?? null;
        if ($current_user_id && $comment['user_id'] == $current_user_id && !$isSystemLog) {
            echo "<div class='comment-actions'>";
            echo "<button class='btn btn-sm btn-light' onclick='editComment(\"{$comment['comment_id']}\", \"" . htmlspecialchars(addslashes($comment['comment_text'])) . "\")' title='แก้ไข' style='border: 1px solid #e5e7eb;'>";
            echo "<i class='fas fa-edit'></i>";
            echo "</button>";
            echo "<button class='btn btn-sm btn-light text-danger' onclick='deleteComment(\"{$comment['comment_id']}\")' title='ลบ' style='border: 1px solid #e5e7eb;'>";
            echo "<i class='fas fa-trash-alt'></i>";
            echo "</button>";
            echo "</div>";
        }

        echo "</div>"; // close comment-header

        // Content
        if ($isSystemLog) {
            // System log format
            $logText = '';
            switch ($comment['comment_type']) {
                case 'status_change':
                    $logText = "<i class='fas fa-exchange-alt mr-2'></i>เปลี่ยนสถานะจาก <strong>" . htmlspecialchars($comment['old_value']) . "</strong> เป็น <strong>" . htmlspecialchars($comment['new_value']) . "</strong>";
                    break;
                case 'progress_update':
                    $logText = "<i class='fas fa-chart-line mr-2'></i>อัปเดตความคืบหน้าจาก <strong>" . htmlspecialchars($comment['old_value']) . "%</strong> เป็น <strong>" . htmlspecialchars($comment['new_value']) . "%</strong>";
                    break;
                case 'file_upload':
                    $logText = "<i class='fas fa-paperclip mr-2'></i>" . htmlspecialchars($comment['comment_text']);
                    break;
                default:
                    $logText = "<i class='fas fa-info-circle mr-2'></i>" . htmlspecialchars($comment['comment_text']);
            }
            echo "<div class='comment-content'>{$logText}</div>";
        } else {
            // Regular comment
            echo "<div class='comment-content'>" . nl2br(htmlspecialchars($comment['comment_text'])) . "</div>";
        }

        // Attachments
        if (!empty($attachments)) {
            echo "<div class='comment-attachments'>";
            foreach ($attachments as $file) {
                $fileIcon = getFileIcon($file['file_extension']);
                $fileSize = formatBytes($file['file_size']);
                $downloadUrl = "download_attachment.php?id=" . urlencode($file['attachment_id']);

                echo "<a href='{$downloadUrl}' class='attachment-item' target='_blank'>";
                echo "<div class='attachment-icon {$fileIcon['class']}'>";
                echo "<i class='fas {$fileIcon['icon']}'></i>";
                echo "</div>";
                echo "<div class='attachment-info'>";
                echo "<div class='attachment-name'>" . htmlspecialchars($file['file_name']) . "</div>";
                echo "<div class='attachment-size'>{$fileSize}</div>";
                echo "</div>";
                echo "</a>";
            }
            echo "</div>";
        }

        echo "</div>"; // End comment-item
    }

} catch (PDOException $e) {
    echo '<div class="empty-state">
            <i class="fas fa-exclamation-triangle"></i>
            <p>เกิดข้อผิดพลาดในการโหลดความคิดเห็น</p>
          </div>';
    error_log("Error loading comments: " . $e->getMessage());
}
?>

