<?php
session_start();
include('../../../config/condb.php');

// Get current user info from session
$current_user_id = $_SESSION['user_id'] ?? '';
$current_user_role = $_SESSION['role'] ?? '';

// Basic helpers
function timeAgo($datetime) {
    $ts = strtotime($datetime);
    $diff = time() - $ts;
    if ($diff < 60) return 'เมื่อสักครู่';
    if ($diff < 3600) return floor($diff/60) . ' นาทีที่แล้ว';
    if ($diff < 86400) return floor($diff/3600) . ' ชั่วโมงที่แล้ว';
    if ($diff < 604800) return floor($diff/86400) . ' วันที่แล้ว';
    return date('d/m/Y H:i', $ts);
}
function esc($s){return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');}
function initials($full){
    $parts = explode(' ', trim($full));
    if (count($parts) >= 2) return strtoupper(mb_substr($parts[0],0,1).mb_substr($parts[1],0,1));
    return strtoupper(mb_substr($full,0,2));
}
function fmtSize($bytes){
    if (!$bytes) return '0 Bytes';
    $k=1024; $sizes=['Bytes','KB','MB','GB']; $i=floor(log($bytes,$k));
    return round($bytes/pow($k,$i),2).' '.$sizes[$i];
}
function fileIconClass($ext){
    $ext=strtolower($ext);
    $map=['jpg'=>'fa-file-image','jpeg'=>'fa-file-image','png'=>'fa-file-image','gif'=>'fa-file-image','pdf'=>'fa-file-pdf','doc'=>'fa-file-word','docx'=>'fa-file-word','xls'=>'fa-file-excel','xlsx'=>'fa-file-excel','zip'=>'fa-file-archive','rar'=>'fa-file-archive','txt'=>'fa-file-alt'];
    return $map[$ext] ?? 'fa-file';
}

header('Content-Type: text/html; charset=utf-8');

$ticket_id = $_GET['ticket_id'] ?? '';
if (!$ticket_id) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>ไม่พบ Ticket</p></div>';
    exit;
}

try {
    // 1) Load comments
    $sqlC = "SELECT c.comment_id, c.ticket_id, c.comment, c.created_by, c.created_at,
                    CONCAT(u.first_name,' ',u.last_name) AS user_name
             FROM service_ticket_comments c
             LEFT JOIN users u ON c.created_by = u.user_id
             WHERE c.ticket_id = ? AND c.deleted_at IS NULL
             ORDER BY c.created_at ASC";
    $stC = $condb->prepare($sqlC);
    $stC->execute([$ticket_id]);
    $comments = $stC->fetchAll(PDO::FETCH_ASSOC);

    // 2) Load timeline
    $sqlT = "SELECT timeline_id, ticket_id, `order`, actor, action, detail, location, created_at
             FROM service_ticket_timeline WHERE ticket_id = ? ORDER BY created_at ASC";
    $stT = $condb->prepare($sqlT);
    $stT->execute([$ticket_id]);
    $timeline = $stT->fetchAll(PDO::FETCH_ASSOC);

    // 3) Merge into single chronological feed
    $feed = [];
    foreach ($comments as $c) {
        $feed[] = ['type'=>'comment','ts'=>$c['created_at'],'data'=>$c];
    }
    foreach ($timeline as $t) {
        $feed[] = ['type'=>'timeline','ts'=>$t['created_at'],'data'=>$t];
    }
    usort($feed, function($a,$b){ return strtotime($a['ts']) <=> strtotime($b['ts']); });

    if (empty($feed)) {
        echo '<div class="empty-state"><i class="fas fa-comments"></i><p>ยังไม่มีข้อมูล</p><small class="text-muted">เริ่มพูดคุยหรืออัปเดตงานได้เลย</small></div>';
        exit;
    }

    // Render feed with improved card-based layout
    foreach ($feed as $item) {
        if ($item['type'] === 'timeline') {
            $t = $item['data'];
            echo '<div class="activity-item system-log">';
            echo '  <div class="activity-header">';
            echo '    <div class="user-avatar-small" style="background: linear-gradient(135deg, #4299e1 0%, #667eea 100%);"><i class="fas fa-robot"></i></div>';
            echo '    <div class="activity-meta">';
            echo '      <div class="activity-user">'.esc($t['actor']).'</div>';
            echo '      <div class="activity-time"><i class="far fa-clock"></i> '.timeAgo($t['created_at']).'</div>';
            echo '    </div>';
            echo '  </div>';
            $detail = '';
            if (!empty($t['action'])) $detail .= '<div style="font-weight: 600; color: #1e40af; margin-bottom: 0.25rem;">'.esc($t['action']).'</div>';
            if (!empty($t['detail'])) $detail .= '<div style="color: #6b7280; font-size: 0.875rem;">'.esc($t['detail']).'</div>';
            if (!empty($t['location'])) $detail .= '<div style="color: #4299e1; font-size: 0.875rem; margin-top: 0.5rem;"><i class="fas fa-map-marker-alt"></i> '.esc($t['location']).'</div>';
            echo '  <div class="activity-content">'.$detail.'</div>';
            echo '</div>';
            continue;
        }
        // comment
        $c = $item['data'];
        $name = $c['user_name'] ?: 'ผู้ใช้งาน';
        $ini = initials($name);

        // Random gradient colors for avatars
        $gradients = [
            'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
            'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
            'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
            'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
            'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
        ];
        $gradientIndex = abs(crc32($c['created_by'] ?? '0')) % count($gradients);
        $gradient = $gradients[$gradientIndex];

        // Check if user can delete this comment
        $canDelete = false;
        if ($current_user_role === 'Executive') {
            $canDelete = true; // Executive can delete all comments
        } elseif ($c['created_by'] === $current_user_id) {
            $canDelete = true; // Owner can delete their own comment
        }

        echo '<div class="activity-item">';
        echo '  <div class="activity-header">';
        echo '    <div class="user-avatar-small" style="background: '.$gradient.';">'.$ini.'</div>';
        echo '    <div class="activity-meta">';
        echo '      <div class="activity-user">'.esc($name).'</div>';
        echo '      <div class="activity-time"><i class="far fa-clock"></i> '.timeAgo($c['created_at']).'</div>';
        echo '    </div>';
        if ($canDelete) {
            echo '    <button class="btn-delete-comment" onclick="deleteComment(\''.esc($c['comment_id']).'\')" title="ลบความคิดเห็น" style="margin-left: auto; background: none; border: none; color: #ef4444; cursor: pointer; padding: 0.5rem; border-radius: 6px; transition: all 0.2s;" onmouseover="this.style.background=\'#fee2e2\';" onmouseout="this.style.background=\'none\';"><i class="fas fa-trash-alt"></i></button>';
        }
        echo '  </div>';
        echo '  <div class="activity-content">'.nl2br(esc($c['comment'])).'</div>';

        // Load attachments linked to this comment by filename prefix in file_path
        $prefix = 'CMT-'.$c['comment_id'].'__';
        $stA = $condb->prepare("SELECT file_name, file_path, file_size FROM service_ticket_attachments WHERE ticket_id = ? AND file_path LIKE ? ORDER BY uploaded_at ASC");
        $stA->execute([$ticket_id, '%/'.$prefix.'%']);
        $files = $stA->fetchAll(PDO::FETCH_ASSOC);
        if ($files) {
            // Separate images and non-image files
            $images = [];
            $otherFiles = [];
            foreach ($files as $f) {
                $ext = strtolower(pathinfo($f['file_name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'])) {
                    $images[] = $f;
                } else {
                    $otherFiles[] = $f;
                }
            }

            // Display images inline
            if ($images) {
                echo '<div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px dashed #e5e7eb; display: flex; flex-wrap: wrap; gap: 0.75rem;">';
                foreach ($images as $img) {
                    echo '<div style="position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s; cursor: pointer;" onmouseover="this.style.transform=\'scale(1.05)\'; this.style.boxShadow=\'0 4px 16px rgba(0,0,0,0.15)\';" onmouseout="this.style.transform=\'scale(1)\'; this.style.boxShadow=\'0 2px 8px rgba(0,0,0,0.1)\';" onclick="openImageModal(\''.esc($img['file_path']).'\', \''.esc($img['file_name']).'\')">';
                    echo '  <img src="'.esc($img['file_path']).'" alt="'.esc($img['file_name']).'" style="max-width: 200px; max-height: 200px; width: auto; height: auto; display: block; object-fit: cover;">';
                    echo '  <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.7), transparent); padding: 0.5rem; color: white;">';
                    echo '    <div style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">'.esc($img['file_name']).'</div>';
                    echo '  </div>';
                    echo '</div>';
                }
                echo '</div>';
            }

            // Display non-image files
            if ($otherFiles) {
                echo '<div style="margin-top: 0.75rem;'.($images ? '' : ' padding-top: 0.75rem; border-top: 1px dashed #e5e7eb;').' display: flex; flex-wrap: wrap; gap: 0.5rem;">';
                foreach ($otherFiles as $f) {
                    $ext = pathinfo($f['file_name'], PATHINFO_EXTENSION);
                    $icon = fileIconClass($ext);
                    $display = $f['file_name'];

                    // Icon color based on file type
                    $iconColors = [
                        'fa-file-image' => '#f59e0b',
                        'fa-file-pdf' => '#ef4444',
                        'fa-file-word' => '#3b82f6',
                        'fa-file-excel' => '#10b981',
                        'fa-file-archive' => '#8b5cf6',
                    ];
                    $iconColor = $iconColors[$icon] ?? '#6b7280';

                    echo '<a href="'.esc($f['file_path']).'" target="_blank" style="text-decoration: none; display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: white; border: 1px solid #e5e7eb; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background=\'#f9fafb\'; this.style.borderColor=\'#d1d5db\';" onmouseout="this.style.background=\'white\'; this.style.borderColor=\'#e5e7eb\';">';
                    echo '  <div style="width: 32px; height: 32px; border-radius: 6px; background: '.$iconColor.'20; color: '.$iconColor.'; display: flex; align-items: center; justify-content: center;"><i class="fas '.$icon.'"></i></div>';
                    echo '  <div style="flex: 1; min-width: 0;">';
                    echo '    <div style="font-weight: 500; font-size: 0.8rem; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">'.esc($display).'</div>';
                    echo '    <div style="font-size: 0.7rem; color: #9ca3af;">'.esc(fmtSize($f['file_size'] ?? 0)).'</div>';
                    echo '  </div>';
                    echo '  <div style="color: '.$iconColor.';"><i class="fas fa-download" style="font-size: 0.875rem;"></i></div>';
                    echo '</a>';
                }
                echo '</div>';
            }
        }
        echo '</div>';
    }
} catch (Exception $e) {
    echo '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>โหลดข้อมูลไม่สำเร็จ</p></div>';
}
?>
