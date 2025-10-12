<?php
session_start();
include('../../../../config/condb.php');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

$employee_id = decryptUserId($_GET['employee_id']);

$sql = "SELECT
            edl.*,
            u.first_name,
            u.last_name
        FROM employee_document_links edl
        LEFT JOIN users u ON edl.created_by = u.user_id
        WHERE edl.employee_id = :employee_id
        ORDER BY edl.created_at DESC";

$stmt = $condb->prepare($sql);
$stmt->execute([':employee_id' => $employee_id]);
$links = $stmt->fetchAll(PDO::FETCH_ASSOC);

$category_names = [
    'drive' => 'Google Drive',
    'sharepoint' => 'SharePoint',
    'onedrive' => 'OneDrive',
    'other' => 'อื่นๆ'
];

foreach ($links as &$link) {
    $link['category_name'] = $category_names[$link['link_category']] ?? $link['link_category'];
    $link['created_by_name'] = $link['first_name'] . ' ' . $link['last_name'];
    $link['created_at_formatted'] = date('d/m/Y H:i', strtotime($link['created_at']));
    $link['link_id_encrypted'] = encryptUserId($link['link_id']);
}

echo json_encode(['success' => true, 'links' => $links]);
