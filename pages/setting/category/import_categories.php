<?php
session_start();
require_once __DIR__ . '/../../../config/condb.php';
require_once __DIR__ . '/../../../config/validation.php';

// 1. Security Checks
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?message=' . urlencode('error:Invalid request method.'));
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Executive') {
    header('Location: index.php?message=' . urlencode('error:Unauthorized access.'));
    exit();
}

if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    header('Location: index.php?message=' . urlencode('error:CSRF token validation failed.'));
    exit();
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    header('Location: index.php?message=' . urlencode('error:File upload error.'));
    exit();
}

$file = $_FILES['file'];
$allowed_mime_types = ['text/csv', 'application/vnd.ms-excel', 'text/plain'];
$file_mime_type = mime_content_type($file['tmp_name']);

if (!in_array($file_mime_type, $allowed_mime_types)) {
    header('Location: index.php?message=' . urlencode('error:Invalid file type. Please upload a CSV file.'));
    exit();
}

// 2. Process CSV File
$handle = fopen($file['tmp_name'], 'r');
if ($handle === FALSE) {
    header('Location: index.php?message=' . urlencode('error:Could not open the file.'));
    exit();
}

$inserted_count = 0;
$skipped_count = 0;
$error_count = 0;
$row_number = 0;

// Skip header row
fgetcsv($handle, 1000, ",");

$condb->beginTransaction();

try {
    $check_stmt = $condb->prepare("SELECT id FROM category WHERE service_category = ? AND category = ? AND (sub_category = ? OR (sub_category IS NULL AND ? IS NULL))");
    $insert_stmt = $condb->prepare("INSERT INTO category (id, service_category, category, sub_category, created_by) VALUES (UUID(), ?, ?, ?, ?)");

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $row_number++;
        if (count($data) < 2) { // Must have at least service_category and category
            $error_count++;
            continue;
        }

        $service_category = trim($data[0]);
        $category = trim($data[1]);
        $sub_category = isset($data[2]) && trim($data[2]) !== '' ? trim($data[2]) : null;

        if (empty($service_category) || empty($category)) {
            $error_count++;
            continue;
        }

        // Check for duplicates
        $check_stmt->execute([$service_category, $category, $sub_category, $sub_category]);
        if ($check_stmt->fetch()) {
            $skipped_count++;
            continue;
        }

        // Insert new record
        $insert_stmt->execute([$service_category, $category, $sub_category, $_SESSION['user_id']]);
        $inserted_count++;
    }

    $condb->commit();
    $message = "success:Import successful! {$inserted_count} records added, {$skipped_count} duplicates skipped, {$error_count} rows with errors.";

} catch (Exception $e) {
    $condb->rollBack();
    $message = "error:Database error during import: " . $e->getMessage();
}

fclose($handle);

// 3. Redirect with Feedback
header('Location: index.php?message=' . urlencode($message));
exit();
?>
