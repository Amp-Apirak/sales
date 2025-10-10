<?php
header('Content-Type: text/html; charset=utf-8');
include_once('../../../include/Add_session.php');
include_once('../../../config/condb.php');

// Get session variables
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$team_id = isset($_SESSION['team_id']) ? $_SESSION['team_id'] : '';

// Get project_id from request
$project_id = isset($_GET['project_id']) ? $_GET['project_id'] : '';

if (empty($project_id)) {
    echo '<div class="alert alert-danger">ไม่พบข้อมูลโครงการ</div>';
    exit;
}

// Check user access to project
$access_check = false;
if ($role === 'Executive' || $role === 'Account Management') {
    $access_check = true;
} elseif ($role === 'Sale Supervisor') {
    $stmt = $condb->prepare("
        SELECT p.* FROM projects p
        WHERE p.project_id = :project_id
        AND p.seller IN (SELECT user_id FROM user_teams WHERE team_id = :team_id)
    ");
    $stmt->execute([':project_id' => $project_id, ':team_id' => $team_id]);
    if ($stmt->fetch()) $access_check = true;
} else {
    // Seller/Engineer: Own projects or assigned projects
    $stmt = $condb->prepare("
        SELECT * FROM projects
        WHERE project_id = :project_id
        AND (seller = :user_id OR project_id IN (
            SELECT project_id FROM project_members WHERE user_id = :user_id2
        ))
    ");
    $stmt->execute([':project_id' => $project_id, ':user_id' => $user_id, ':user_id2' => $user_id]);
    if ($stmt->fetch()) $access_check = true;
}

if (!$access_check) {
    echo '<div class="alert alert-danger">คุณไม่มีสิทธิ์เข้าถึงกระดานสนทนานี้</div>';
    exit;
}

// Timezone used when formatting discussion timestamps
$timezone = new DateTimeZone('Asia/Bangkok');

// Helper: format time ago strings with Bangkok timezone and guard against future timestamps
function formatDiscussionTime(string $datetime, DateTimeZone $timezone): string
{
    if (empty($datetime)) {
        return '';
    }

    try {
        $createdAt = new DateTimeImmutable($datetime, $timezone);
    } catch (Exception $e) {
        $timestamp = strtotime($datetime);
        if ($timestamp === false) {
            return '';
        }

        $createdAt = (new DateTimeImmutable('@' . $timestamp))->setTimezone($timezone);
    }

    $now = new DateTimeImmutable('now', $timezone);

    if ($createdAt > $now) {
        return $createdAt->format('d/m/Y H:i');
    }

    $diff = $now->diff($createdAt);

    if ($diff->days >= 7) {
        return $createdAt->format('d/m/Y H:i');
    }

    if ($diff->days >= 1) {
        return $diff->days . ' วันที่แล้ว';
    }

    if ($diff->h >= 1) {
        return $diff->h . ' ชั่วโมงที่แล้ว';
    }

    if ($diff->i >= 1) {
        return $diff->i . ' นาทีที่แล้ว';
    }

    return 'เมื่อสักครู่';
}

// Fetch all discussions with user info
$stmt = $condb->prepare("
    SELECT
        d.*,
        u.first_name,
        u.last_name,
        u.profile_image,
        u.role as user_role
    FROM project_discussions d
    LEFT JOIN users u ON d.user_id = u.user_id
    WHERE d.project_id = :project_id
    AND d.is_deleted = 0
    ORDER BY d.created_at ASC
");
$stmt->execute([':project_id' => $project_id]);
$discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($discussions)) {
    echo '<div class="text-center text-muted py-5">
        <i class="fas fa-comments fa-3x mb-3"></i>
        <p>ยังไม่มีการสนทนา เริ่มต้นการสนทนาได้เลย!</p>
    </div>';
    exit;
}

// Display discussions
foreach ($discussions as $disc) {
    $is_own = ($disc['user_id'] === $user_id);
    $can_edit = ($is_own || $role === 'Executive' || $role === 'Account Management');

    // Get attachments
    $stmt_attach = $condb->prepare("
        SELECT * FROM project_discussion_attachments
        WHERE discussion_id = :discussion_id
        ORDER BY uploaded_at ASC
    ");
    $stmt_attach->execute([':discussion_id' => $disc['discussion_id']]);
    $attachments = $stmt_attach->fetchAll(PDO::FETCH_ASSOC);

    // Profile image
    $profile_img = !empty($disc['profile_image'])
        ? BASE_URL . 'uploads/profile_images/' . $disc['profile_image']
        : BASE_URL . 'assets/img/default-avatar.jpg';

    $time_str = formatDiscussionTime($disc['created_at'], $timezone);
    if ($time_str === '') {
        $time_str = date('d/m/Y H:i', strtotime($disc['created_at']));
    }

    echo '<div class="discussion-item mb-3" data-discussion-id="' . $disc['discussion_id'] . '">';
    echo '<div class="d-flex">';
    echo '<img src="' . $profile_img . '" class="rounded-circle mr-3" width="40" height="40" alt="Avatar">';
    echo '<div class="flex-grow-1">';
    echo '<div class="discussion-header">';
    echo '<strong>' . htmlspecialchars($disc['first_name'] . ' ' . $disc['last_name']) . '</strong>';
    echo ' <span class="badge badge-secondary badge-sm ml-1">' . $disc['user_role'] . '</span>';
    echo ' <small class="text-muted ml-2">• ' . $time_str . '</small>';
    if ($disc['is_edited']) {
        echo ' <span class="badge badge-warning badge-sm ml-1"><i class="fas fa-pen"></i> แก้ไขแล้ว</span>';
    }
    echo '</div>';

    // Message text
    echo '<div class="discussion-message mt-1">';
    echo '<span class="message-text">' . nl2br(htmlspecialchars($disc['message_text'], ENT_QUOTES, 'UTF-8')) . '</span>';
    echo '</div>';

    // Attachments
    if (!empty($attachments)) {
        echo '<div class="discussion-attachments mt-2">';
        foreach ($attachments as $file) {
            $file_url = BASE_URL . 'pages/project/discussion/download_attachment.php?id=' . $file['attachment_id'];
            $file_size = round($file['file_size'] / 1024, 2); // KB

            // Check if image
            $image_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array(strtolower($file['file_extension']), $image_exts)) {
                echo '<div class="attachment-item d-inline-block mr-2 mb-2">';
                echo '<a href="' . $file_url . '" download="' . htmlspecialchars($file['file_name']) . '" target="_blank">';
                echo '<img src="' . BASE_URL . $file['file_path'] . '" class="img-thumbnail" style="max-width: 150px; max-height: 150px; cursor: pointer;" title="คลิกเพื่อดาวน์โหลด">';
                echo '</a>';
                echo '<br><small class="text-muted">' . htmlspecialchars($file['file_name']) . '</small>';
                echo '</div>';
            } else {
                // Other file types
                $icon_class = 'fa-file';
                if (stripos($file['file_type'], 'pdf') !== false) $icon_class = 'fa-file-pdf text-danger';
                elseif (stripos($file['file_type'], 'word') !== false || stripos($file['file_type'], 'document') !== false) $icon_class = 'fa-file-word text-primary';
                elseif (stripos($file['file_type'], 'excel') !== false || stripos($file['file_type'], 'sheet') !== false) $icon_class = 'fa-file-excel text-success';
                elseif (stripos($file['file_type'], 'zip') !== false || stripos($file['file_type'], 'rar') !== false) $icon_class = 'fa-file-archive text-warning';

                echo '<div class="attachment-item d-inline-block mr-2 mb-2">';
                echo '<a href="' . $file_url . '" download="' . htmlspecialchars($file['file_name']) . '" target="_blank" class="btn btn-sm btn-outline-secondary">';
                echo '<i class="fas ' . $icon_class . '"></i> ';
                echo htmlspecialchars($file['file_name']) . ' (' . $file_size . ' KB)';
                echo '</a>';
                echo '</div>';
            }
        }
        echo '</div>';
    }

    // Action buttons
    if ($can_edit) {
        echo '<div class="discussion-actions mt-2 d-flex justify-content-end gap-2">';
        if ($is_own) {
            echo '<button class="btn btn-sm btn-outline-primary edit-discussion" data-id="' . $disc['discussion_id'] . '" title="แก้ไขข้อความ">';
            echo '<i class="fas fa-edit"></i> แก้ไข';
            echo '</button>';
        }
        echo '<button class="btn btn-sm btn-outline-danger delete-discussion ml-2" data-id="' . $disc['discussion_id'] . '" title="ลบข้อความ">';
        echo '<i class="fas fa-trash-alt"></i> ลบ';
        echo '</button>';
        echo '</div>';
    }

    echo '</div>'; // flex-grow-1
    echo '</div>'; // d-flex
    echo '</div>'; // discussion-item
}
?>
