<?php
include_once('../../../include/Add_session.php');
include_once('../../../config/condb.php');

// Get session variables
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$team_id = isset($_SESSION['team_id']) ? $_SESSION['team_id'] : '';

$project_id = isset($_GET['project_id']) ? $_GET['project_id'] : '';

if (empty($project_id)) {
    die('ไม่พบข้อมูลโครงการ');
}

// Check access (same as get_discussions.php)
$access_check = false;
if ($role === 'Executive') {
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
    die('คุณไม่มีสิทธิ์เข้าถึงโครงการนี้');
}

// Get project info
$stmt = $condb->prepare("SELECT * FROM projects WHERE project_id = :project_id");
$stmt->execute([':project_id' => $project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// Get discussions
$stmt = $condb->prepare("
    SELECT d.*, u.first_name, u.last_name
    FROM project_discussions d
    LEFT JOIN users u ON d.user_id = u.user_id
    WHERE d.project_id = :project_id
    AND d.is_deleted = 0
    ORDER BY d.created_at ASC
");
$stmt->execute([':project_id' => $project_id]);
$discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create Word document using PHPWord (if installed via Composer)
// If PHPWord is not available, use simple HTML to DOCX conversion

// Check if PHPWord exists
if (file_exists('../../../vendor/autoload.php')) {
    require_once '../../../vendor/autoload.php';

    $phpWord = new \PhpOffice\PhpWord\PhpWord();

    // Set Thai language
    $phpWord->getSettings()->setThemeFontLang(new \PhpOffice\PhpWord\Style\Language(\PhpOffice\PhpWord\Style\Language::TH_TH));

    // Create section
    $section = $phpWord->addSection([
        'marginLeft' => 1134,
        'marginRight' => 1134,
        'marginTop' => 1134,
        'marginBottom' => 1134,
    ]);

    // Title
    $section->addText(
        'กระดานสนทนาโครงการ',
        ['name' => 'Sarabun', 'size' => 18, 'bold' => true],
        ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 200]
    );

    $section->addText(
        'โครงการ: ' . $project['project_name'],
        ['name' => 'Sarabun', 'size' => 14, 'bold' => true],
        ['spaceAfter' => 100]
    );

    $section->addText(
        'วันที่ Export: ' . date('d/m/Y H:i:s'),
        ['name' => 'Sarabun', 'size' => 11, 'color' => '666666'],
        ['spaceAfter' => 300]
    );

    $section->addText(
        '─────────────────────────────────────────────────',
        ['name' => 'Sarabun', 'size' => 11],
        ['spaceAfter' => 200]
    );

    // Add discussions
    foreach ($discussions as $disc) {
        $userName = $disc['first_name'] . ' ' . $disc['last_name'];
        $dateTime = date('d/m/Y H:i', strtotime($disc['created_at']));

        // User name and time
        $section->addText(
            $userName . ' • ' . $dateTime,
            ['name' => 'Sarabun', 'size' => 11, 'bold' => true, 'color' => '0066cc'],
            ['spaceAfter' => 100]
        );

        // Message
        if (!empty($disc['message_text'])) {
            $section->addText(
                $disc['message_text'],
                ['name' => 'Sarabun', 'size' => 11],
                ['spaceAfter' => 100]
            );
        }

        // Get attachments
        $stmt_attach = $condb->prepare("
            SELECT * FROM project_discussion_attachments
            WHERE discussion_id = :discussion_id
            ORDER BY uploaded_at ASC
        ");
        $stmt_attach->execute([':discussion_id' => $disc['discussion_id']]);
        $attachments = $stmt_attach->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($attachments)) {
            $section->addText(
                'ไฟล์แนบ:',
                ['name' => 'Sarabun', 'size' => 10, 'italic' => true, 'color' => '666666'],
                ['spaceAfter' => 50]
            );

            foreach ($attachments as $att) {
                $section->addText(
                    '  📎 ' . $att['file_name'],
                    ['name' => 'Sarabun', 'size' => 10, 'color' => '666666'],
                    ['spaceAfter' => 50]
                );
            }
        }

        if ($disc['is_edited']) {
            $section->addText(
                '(แก้ไขแล้ว)',
                ['name' => 'Sarabun', 'size' => 9, 'italic' => true, 'color' => '999999'],
                ['spaceAfter' => 200]
            );
        } else {
            $section->addText('', [], ['spaceAfter' => 200]);
        }

        // Separator
        $section->addText(
            '─────────────────────────────────────────────────',
            ['name' => 'Sarabun', 'size' => 11, 'color' => 'eeeeee'],
            ['spaceAfter' => 200]
        );
    }

    // Save file
    $filename = 'การสนทนา_' . date('YmdHis') . '.docx';
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');

    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save('php://output');
    exit;

} else {
    // Fallback: Simple HTML to download
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap");

        body {
            font-family: "Sarabun", sans-serif;
            line-height: 1.6;
            margin: 40px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #0066cc;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 10px;
        }

        .project-info {
            background: #f5f5f5;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #0066cc;
        }

        .discussion {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
        }

        .discussion-header {
            font-weight: 600;
            color: #0066cc;
            margin-bottom: 10px;
        }

        .discussion-message {
            margin: 10px 0;
        }

        .attachments {
            margin-top: 10px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 3px;
        }

        .attachment {
            color: #666;
            font-size: 0.9em;
        }

        .edited-badge {
            font-size: 0.8em;
            color: #999;
            font-style: italic;
        }

        .separator {
            border-top: 1px solid #e0e0e0;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>กระดานสนทนาโครงการ</h1>

    <div class="project-info">
        <strong>โครงการ:</strong> ' . htmlspecialchars($project['project_name']) . '<br>
        <strong>วันที่ Export:</strong> ' . date('d/m/Y H:i:s') . '
    </div>

    <div class="separator"></div>';

    foreach ($discussions as $disc) {
        $userName = htmlspecialchars($disc['first_name'] . ' ' . $disc['last_name']);
        $dateTime = date('d/m/Y H:i', strtotime($disc['created_at']));

        $html .= '<div class="discussion">';
        $html .= '<div class="discussion-header">' . $userName . ' • ' . $dateTime . '</div>';

        if (!empty($disc['message_text'])) {
            $html .= '<div class="discussion-message">' . nl2br(htmlspecialchars($disc['message_text'])) . '</div>';
        }

        // Get attachments
        $stmt_attach = $condb->prepare("
            SELECT * FROM project_discussion_attachments
            WHERE discussion_id = :discussion_id
            ORDER BY uploaded_at ASC
        ");
        $stmt_attach->execute([':discussion_id' => $disc['discussion_id']]);
        $attachments = $stmt_attach->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($attachments)) {
            $html .= '<div class="attachments"><strong>ไฟล์แนบ:</strong><br>';
            foreach ($attachments as $att) {
                $html .= '<div class="attachment">📎 ' . htmlspecialchars($att['file_name']) . '</div>';
            }
            $html .= '</div>';
        }

        if ($disc['is_edited']) {
            $html .= '<div class="edited-badge">(แก้ไขแล้ว)</div>';
        }

        $html .= '</div>';
    }

    $html .= '</body></html>';

    // Set headers for Word download
    $filename = 'การสนทนา_' . date('YmdHis') . '.doc';
    header('Content-Type: application/msword; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');

    echo $html;
    exit;
}
?>
