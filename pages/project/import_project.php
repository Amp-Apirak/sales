<?php
require_once '../../include/Add_session.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        'status' => 'error',
        'message' => 'กรุณาเลือกไฟล์',
        'errors' => []
    ]);
    exit();
}

$file = $_FILES['file'];
$allowedTypes = ['xlsx', 'xls', 'csv'];
$fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($fileType, $allowedTypes)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'รองรับเฉพาะไฟล์ Excel และ CSV เท่านั้น',
        'errors' => []
    ]);
    exit();
}

if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ขนาดไฟล์ต้องไม่เกิน 5MB',
        'errors' => []
    ]);
    exit();
}

try {
    // Fetch valid products
    $product_stmt = $condb->prepare("SELECT product_id, product_name FROM products");
    $product_stmt->execute();
    $products = [];
    while ($row = $product_stmt->fetch(PDO::FETCH_ASSOC)) {
        $products[$row['product_name']] = $row['product_id'];
    }

    $spreadsheet = IOFactory::load($file['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    // Skip header rows
    array_shift($rows);
    array_shift($rows);

    $success = 0;
    $errors = [];
    $valid_statuses = ['Win', 'Loss', 'On Hold', 'Quotation', 'Negotiation', 'Bidding', 'Cancelled'];
    $detailed_errors = [];

    foreach ($rows as $index => $row) {
        if (empty(array_filter($row))) {
            continue;
        }

        $row_num = $index + 3;
        $row_errors = [];

        // Validate required fields
        if (empty($row[0])) {
            $row_errors[] = "สถานะห้ามเป็นค่าว่าง";
        }
        if (empty($row[1])) {
            $row_errors[] = "สินค้าห้ามเป็นค่าว่าง";
        }
        if (empty($row[2])) {
            $row_errors[] = "ชื่อโครงการห้ามเป็นค่าว่าง";
        }

        // Validate status
        if (!empty($row[0]) && !in_array($row[0], $valid_statuses)) {
            $row_errors[] = "สถานะไม่ถูกต้อง (ต้องเป็น: " . implode(", ", $valid_statuses) . ")";
        }

        // Validate product
        if (!empty($row[1])) {
            $product_name = trim($row[1]);
            if (!isset($products[$product_name])) {
                $row_errors[] = "ไม่พบสินค้า '{$product_name}' ในระบบ";
            }
        }

        if (!empty($row_errors)) {
            $detailed_errors[] = [
                'row' => $row_num,
                'errors' => $row_errors,
                'data' => [
                    'status' => $row[0],
                    'product' => $row[1],
                    'project_name' => $row[2]
                ]
            ];
            continue;
        }

        try {
            $condb->beginTransaction();

            $stmt = $condb->prepare("
                INSERT INTO projects (
                    project_id, status, product_id, project_name,
                    created_by, created_at, vat
                ) VALUES (
                    UUID(), :status, :product_id, :project_name,
                    :created_by, NOW(), 7.00
                )
            ");

            $result = $stmt->execute([
                ':status' => trim($row[0]),
                ':product_id' => $products[$product_name],
                ':project_name' => trim($row[2]),
                ':created_by' => $_SESSION['user_id']
            ]);

            if ($result) {
                $condb->commit();
                $success++;
            }
        } catch (Exception $e) {
            $condb->rollBack();
            $detailed_errors[] = [
                'row' => $row_num,
                'errors' => [$e->getMessage()],
                'data' => [
                    'status' => $row[0],
                    'product' => $row[1],
                    'project_name' => $row[2]
                ]
            ];
        }
    }

    // Prepare response
    if ($success > 0 && empty($detailed_errors)) {
        echo json_encode([
            'status' => 'success',
            'message' => "นำเข้าข้อมูลสำเร็จ {$success} รายการ",
            'success_count' => $success
        ]);
    } elseif (!empty($detailed_errors)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'พบข้อผิดพลาดในการนำเข้าข้อมูล',
            'errors' => $detailed_errors,
            'success_count' => $success
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'ไม่พบข้อมูลที่จะนำเข้า หรือข้อมูลไม่ถูกต้อง',
            'errors' => []
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
        'errors' => []
    ]);
}
