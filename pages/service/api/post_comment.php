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

    $user_id = $_SESSION['user_id'];
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
    if ($role === 'Sale Supervisor') {
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

    $canComment = ($isJobOwner || $isReporter || $isWatcher || $role === 'Executive' || ($role === 'Sale Supervisor' && $inSupervisorTeam));
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
    if ($role === 'Sale Supervisor') {
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

    $canComment = ($isJobOwner || $isReporter || $isWatcher || $role === 'Executive' || ($role === 'Sale Supervisor' && $inSupervisorTeam));
    if (!$canComment) {
        throw new Exception('คุณไม่มีสิทธิ์แสดงความคิดเห็นใน Ticket นี้');
    }

    $condb->beginTransaction();


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
            'image/jpeg','image/png','image/gif','application/pdf',
            'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip','application/x-zip-compressed','text/plain'
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

    echo json_encode(['status'=>'success','message'=>'


','comment_id'=>$comment_id,'uploaded_files'=>$uploaded]);
} catch (Exception $e) {
    if ($condb && $condb->inTransaction()) $condb->rollBack();
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
?>

