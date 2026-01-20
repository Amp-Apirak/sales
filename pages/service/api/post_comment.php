<?php
// Post a new comment with optional attachments for Service Ticket
error_reporting(0);
ini_set('display_errors', 0);

session_start();
include('../../../config/condb.php');

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

function uuidv4() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method Not Allowed');
    }
    if (empty($_SESSION['user_id'])) {
        throw new Exception('กรุณาเข้าสู่ระบบ');
    }
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        throw new Exception('Invalid CSRF token');
    }
    $ticket_id = $_POST['ticket_id'] ?? '';
    $comment_text = trim($_POST['comment_text'] ?? '');
    if (!$ticket_id) throw new Exception('ไม่พบ Ticket ID');
    if ($comment_text === '') throw new Exception('กรุณากรอกความคิดเห็น');

    $user_id_raw = $_SESSION['user_id'] ?? null;
    $user_id = trim((string)$user_id_raw);
    if ($user_id === '') {
        throw new Exception('ไม่พบข้อมูลผู้ใช้งานสำหรับบันทึกประวัติ');
    }
    $role = $_SESSION['role'] ?? '';

/*
    //















    //










    //





    //


    //


    //


    //


    //

    //

    //


    //





    //




    //


    //



 "
"

 Sale Supervisor
 "	"


    //

    // 							 job_owner, reporter


    $stInfo = $condb->prepare("SELECT job_owner, reporter FROM service_tickets WHERE ticket_id = ?");
    $stInfo->execute([$ticket_id]);
    $tinfo = $stInfo->fetch(PDO::FETCH_ASSOC);
    if (!$tinfo) {
        throw new Exception(' Ticket 	');
    }

    $isJobOwner = ($tinfo['job_owner'] === $user_id);
    $isReporter = ($tinfo['reporter'] === $user_id);

    $isWatcher = false;
    $wc = $condb->prepare("SELECT COUNT(*) AS c FROM service_ticket_watchers WHERE ticket_id = ? AND user_id = ?");
    $wc->execute([$ticket_id, $user_id]);
    $rowW = $wc->fetch(PDO::FETCH_ASSOC);
    if (!empty($rowW['c']) && (int)$rowW['c'] > 0) { $isWatcher = true; }

    $inSupervisorTeam = false;
    if ($role === 'Sale Supervisor' || $role === 'Account Management') {
        $ut = $condb->prepare("SELECT team_id FROM user_teams WHERE user_id = ?");
        $ut->execute([$user_id]);
        $teams = $ut->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($teams)) {
            $placeholders = implode(',', array_fill(0, count($teams), '?'));
            $sql = "SELECT COUNT(*) AS c FROM user_teams WHERE user_id = ? AND team_id IN ($placeholders)";
            $chk = $condb->prepare($sql);
            $params = array_merge([$tinfo['job_owner']], $teams);
            $chk->execute($params);
            $r = $chk->fetch(PDO::FETCH_ASSOC);
            $inSupervisorTeam = (!empty($r['c']) && (int)$r['c'] > 0);
        }
    }

    $canComment = ($isJobOwner || $isReporter || $isWatcher || $role === 'Executive' || $role === 'Account Management' || (($role === 'Sale Supervisor' || $role === 'Account Management') && $inSupervisorTeam));
    if (!$canComment) {
        throw new Exception('	

 Ticket 
');
    }

    $condb->beginTransaction();
*/

    // ตรวจสอบสิทธิ์การคอมเมนต์: อนุญาตเฉพาะ Job Owner, Reporter, Watcher, Executive หรือ Sale Supervisor ที่อยู่ทีมเดียวกับ Job Owner
    $stInfo = $condb->prepare("SELECT job_owner, reporter FROM service_tickets WHERE ticket_id = ?");
    $stInfo->execute([$ticket_id]);
    $tinfo = $stInfo->fetch(PDO::FETCH_ASSOC);
    if (!$tinfo) {
        throw new Exception('ไม่พบ Ticket ที่ต้องการ');
    }

    $isJobOwner = ($tinfo['job_owner'] === $user_id);
    $isReporter = ($tinfo['reporter'] === $user_id);

    $isWatcher = false;
    $wc = $condb->prepare("SELECT COUNT(*) AS c FROM service_ticket_watchers WHERE ticket_id = ? AND user_id = ?");
    $wc->execute([$ticket_id, $user_id]);
    $rowW = $wc->fetch(PDO::FETCH_ASSOC);
    if (!empty($rowW['c']) && (int)$rowW['c'] > 0) { $isWatcher = true; }

    $inSupervisorTeam = false;
    if ($role === 'Sale Supervisor' || $role === 'Account Management') {
        $ut = $condb->prepare("SELECT team_id FROM user_teams WHERE user_id = ?");
        $ut->execute([$user_id]);
        $teams = $ut->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($teams)) {
            $placeholders = implode(',', array_fill(0, count($teams), '?'));
            $sql = "SELECT COUNT(*) AS c FROM user_teams WHERE user_id = ? AND team_id IN ($placeholders)";
            $chk = $condb->prepare($sql);
            $params = array_merge([$tinfo['job_owner']], $teams);
            $chk->execute($params);
            $r = $chk->fetch(PDO::FETCH_ASSOC);
            $inSupervisorTeam = (!empty($r['c']) && (int)$r['c'] > 0);
        }
    }

    $canComment = ($isJobOwner || $isReporter || $isWatcher || $role === 'Executive' || $role === 'Account Management' || (($role === 'Sale Supervisor' || $role === 'Account Management') && $inSupervisorTeam));
    if (!$canComment) {
        throw new Exception('คุณไม่มีสิทธิ์แสดงความคิดเห็นใน Ticket นี้');
    }

    $condb->beginTransaction();

    // Get current ticket info
    $ticketQuery = $condb->prepare("SELECT status, job_owner FROM service_tickets WHERE ticket_id = ?");
    $ticketQuery->execute([$ticket_id]);
    $ticketInfo = $ticketQuery->fetch(PDO::FETCH_ASSOC);

    if (!$ticketInfo) {
        throw new Exception('ไม่พบข้อมูล Ticket');
    }

    // Check if status change is requested
    $new_status = trim($_POST['new_status'] ?? '');
    $statusChanged = false;
    $old_status = $ticketInfo['status'];

    // Only process if new_status is not empty AND different from current status
    if (!empty($new_status) && $new_status !== $old_status) {
        // Check if user has permission to change status
        $canEdit = ($role === 'Executive' || $role === 'Account Management' || $role === 'Sale Supervisor' || $isJobOwner);

        if ($canEdit) {
            // Update ticket status
            $updateStatus = $condb->prepare("UPDATE service_tickets SET status = ?, updated_at = NOW(), updated_by = ? WHERE ticket_id = ?");
            $updateStatus->execute([$new_status, $user_id, $ticket_id]);

            // Log status change in history
            $history_id = uuidv4();
            $logStatus = $condb->prepare("INSERT INTO service_ticket_history (history_id, ticket_id, field_name, old_value, new_value, changed_by, changed_at)
                                          VALUES (?, ?, 'status', ?, ?, ?, NOW())");
            $logStatus->execute([$history_id, $ticket_id, $old_status, $new_status, $user_id]);

            // Get user name for timeline
            $userNameQuery = $condb->prepare("SELECT CONCAT(first_name, ' ', last_name) as full_name FROM users WHERE user_id = ?");
            $userNameQuery->execute([$user_id]);
            $userName = $userNameQuery->fetchColumn() ?: 'System';

            // Get next order for timeline
            $orderQuery = $condb->prepare("SELECT COALESCE(MAX(`order`), 0) + 1 as next_order FROM service_ticket_timeline WHERE ticket_id = ?");
            $orderQuery->execute([$ticket_id]);
            $nextOrder = $orderQuery->fetchColumn();

            // Add to timeline
            $timeline_id = uuidv4();
            $timelineInsert = $condb->prepare("INSERT INTO service_ticket_timeline (timeline_id, ticket_id, `order`, actor, action, detail, created_at)
                                               VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $timelineInsert->execute([
                $timeline_id,
                $ticket_id,
                $nextOrder,
                $userName,
                'เปลี่ยนสถานะ Ticket',
                "จาก \"{$old_status}\" เป็น \"{$new_status}\""
            ]);

            $statusChanged = true;
        }
    }

    // Check if Job Owner change is requested
    $new_job_owner = trim($_POST['new_job_owner'] ?? '');
    $jobOwnerChanged = false;
    $old_job_owner = $ticketInfo['job_owner'];
    $old_job_owner_name = '';
    $new_job_owner_name = '';

    // Only process if new_job_owner is not empty AND different from current job owner
    if (!empty($new_job_owner) && $new_job_owner !== $old_job_owner) {
        // Check if user has permission to change job owner
        $canEdit = ($role === 'Executive' || $role === 'Account Management' || $role === 'Sale Supervisor' || $isJobOwner);

        if ($canEdit) {
            // Get old and new owner names
            $ownerQuery = $condb->prepare("SELECT user_id, CONCAT(first_name, ' ', last_name) as full_name FROM users WHERE user_id IN (?, ?)");
            $ownerQuery->execute([$old_job_owner, $new_job_owner]);
            $owners = $ownerQuery->fetchAll(PDO::FETCH_ASSOC);

            foreach ($owners as $owner) {
                if ($owner['user_id'] === $old_job_owner) {
                    $old_job_owner_name = $owner['full_name'];
                } elseif ($owner['user_id'] === $new_job_owner) {
                    $new_job_owner_name = $owner['full_name'];
                }
            }

            // Update ticket job owner
            $updateOwner = $condb->prepare("UPDATE service_tickets SET job_owner = ?, updated_at = NOW(), updated_by = ? WHERE ticket_id = ?");
            $updateOwner->execute([$new_job_owner, $user_id, $ticket_id]);

            // Log job owner change in history
            $history_id = uuidv4();
            $logOwner = $condb->prepare("INSERT INTO service_ticket_history (history_id, ticket_id, field_name, old_value, new_value, changed_by, changed_at)
                                         VALUES (?, ?, 'job_owner', ?, ?, ?, NOW())");
            $logOwner->execute([$history_id, $ticket_id, $old_job_owner_name, $new_job_owner_name, $user_id]);

            // Get user name for timeline (person who made the change)
            $userNameQuery = $condb->prepare("SELECT CONCAT(first_name, ' ', last_name) as full_name FROM users WHERE user_id = ?");
            $userNameQuery->execute([$user_id]);
            $userName = $userNameQuery->fetchColumn() ?: 'System';

            // Get next order for timeline
            $orderQuery = $condb->prepare("SELECT COALESCE(MAX(`order`), 0) + 1 as next_order FROM service_ticket_timeline WHERE ticket_id = ?");
            $orderQuery->execute([$ticket_id]);
            $nextOrder = $orderQuery->fetchColumn();

            // Add to timeline
            $timeline_id = uuidv4();
            $timelineInsert = $condb->prepare("INSERT INTO service_ticket_timeline (timeline_id, ticket_id, `order`, actor, action, detail, created_at)
                                               VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $timelineInsert->execute([
                $timeline_id,
                $ticket_id,
                $nextOrder,
                $userName,
                'เปลี่ยน Job Owner',
                "จาก \"{$old_job_owner_name}\" เป็น \"{$new_job_owner_name}\""
            ]);

            $jobOwnerChanged = true;
        }
    }

    // Insert comment
    $comment_id = uuidv4();
    $sql = "INSERT INTO service_ticket_comments (comment_id, ticket_id, comment, created_by, created_at)
            VALUES (?, ?, ?, ?, NOW())";
    $st = $condb->prepare($sql);
    $st->execute([$comment_id, $ticket_id, $comment_text, $user_id]);

    // Handle attachments (optional)
    $uploaded = [];
    if (!empty($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
        $uploadDir = '../../../uploads/service_tickets/' . $ticket_id . '/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $allowed = [
            'image/jpeg','image/png','image/gif',
            'application/pdf',
            'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip','application/x-zip-compressed',
            'text/plain','text/html','application/xhtml+xml',
            'video/mp4','video/quicktime','video/webm','video/x-m4v'
        ];
        $maxSize = 10 * 1024 * 1024; // 10 MB

        foreach ($_FILES['attachments']['name'] as $i => $origName) {
            if ($_FILES['attachments']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $tmp = $_FILES['attachments']['tmp_name'][$i];
            $size = $_FILES['attachments']['size'][$i];
            $type = $_FILES['attachments']['type'][$i];
            if ($size > $maxSize) continue;
            if (!in_array($type, $allowed)) continue;

            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $storedName = 'CMT-' . $comment_id . '__' . uuidv4() . '.' . $ext; // include comment id prefix
            $dest = $uploadDir . $storedName;

            if (move_uploaded_file($tmp, $dest)) {
                $publicPath = BASE_URL . 'uploads/service_tickets/' . $ticket_id . '/' . $storedName;
                $mimeType = @mime_content_type($dest) ?: $type;
                $ins = $condb->prepare("INSERT INTO service_ticket_attachments (
                    attachment_id, ticket_id, file_name, file_path, file_size, file_type, mime_type, uploaded_by
                ) VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?)");
                $ins->execute([$ticket_id, $origName, $publicPath, $size, $ext, $mimeType, $user_id]);
                $uploaded[] = $origName;
            }
        }
    }

    $condb->commit();

    // Build success message
    $message = 'โพสต์ความคิดเห็นเรียบร้อยแล้ว';
    if ($statusChanged) {
        $message .= ' และเปลี่ยนสถานะจาก "' . $old_status . '" เป็น "' . $new_status . '"';
    }
    if ($jobOwnerChanged) {
        $message .= ' และเปลี่ยน Job Owner จาก "' . $old_job_owner_name . '" เป็น "' . $new_job_owner_name . '"';
    }

    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'comment_id' => $comment_id,
        'uploaded_files' => $uploaded,
        'status_changed' => $statusChanged,
        'old_status' => $old_status,
        'new_status' => $new_status,
        'job_owner_changed' => $jobOwnerChanged,
        'old_job_owner' => $old_job_owner_name,
        'new_job_owner' => $new_job_owner_name
    ]);
} catch (Exception $e) {
    if ($condb && $condb->inTransaction()) $condb->rollBack();
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
?>
