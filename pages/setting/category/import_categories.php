<?php
session_start();
require_once __DIR__ . '/../../../config/condb.php';
require_once __DIR__ . '/../../../config/validation.php';

// 1. Security and Initial Checks
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
    $upload_errors = [
        UPLOAD_ERR_INI_SIZE   => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
        UPLOAD_ERR_FORM_SIZE  => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
        UPLOAD_ERR_PARTIAL    => "The uploaded file was only partially uploaded.",
        UPLOAD_ERR_NO_FILE    => "No file was uploaded.",
        UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
        UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
        UPLOAD_ERR_EXTENSION  => "A PHP extension stopped the file upload.",
    ];
    $error_message = $upload_errors[$_FILES['file']['error']] ?? "Unknown file upload error.";
    header('Location: index.php?message=' . urlencode('error:' . $error_message));
    exit();
}

// 2. MIME Type and File Validation
$file = $_FILES['file'];
$allowed_mime_types = [
    'text/csv',
    'application/vnd.ms-excel',
    'text/plain',
    'application/csv',
    'text/x-csv',
    'application/x-csv',
    'text/comma-separated-values',
    'application/octet-stream' // Generic fallback for different system configurations
];
$file_mime_type = mime_content_type($file['tmp_name']);

if (!in_array($file_mime_type, $allowed_mime_types)) {
    $error_message = 'error:Invalid file type. Detected: ' . htmlspecialchars($file_mime_type) . '. Please upload a valid CSV file.';
    header('Location: index.php?message=' . urlencode($error_message));
    exit();
}

// 3. Process CSV File with UTF-8 support
setlocale(LC_ALL, 'en_US.UTF-8'); // Set locale for proper UTF-8 handling

$handle = fopen($file['tmp_name'], 'r');
if ($handle === FALSE) {
    header('Location: index.php?message=' . urlencode('error:Could not open the uploaded file.'));
    exit();
}

$inserted_count = 0;
$skipped_count = 0;
$error_count = 0;

// Skip header row
fgetcsv($handle, 1000, ",");

$condb->beginTransaction();

try {
    $check_stmt = $condb->prepare("SELECT id FROM category WHERE service_category = ? AND category = ? AND (sub_category = ? OR (sub_category IS NULL AND ? IS NULL))");
    $insert_stmt = $condb->prepare("INSERT INTO category (id, service_category, category, sub_category, created_by) VALUES (UUID(), ?, ?, ?, ?)");

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (count($data) < 2) {
            $error_count++;
            continue;
        }

        // Convert each field to UTF-8
        $service_category = mb_convert_encoding(trim($data[0]), 'UTF-8', 'auto');
        $category = mb_convert_encoding(trim($data[1]), 'UTF-8', 'auto');
        $sub_category = isset($data[2]) && trim($data[2]) !== '' ? mb_convert_encoding(trim($data[2]), 'UTF-8', 'auto') : null;

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

// 4. Redirect with Feedback
header('Location: index.php?message=' . urlencode($message));
exit();
?>