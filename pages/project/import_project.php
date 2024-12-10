<?php
require_once '../../include/Add_session.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// ฟังก์ชันสร้าง UUID
function generateUUID()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

// ฟังก์ชันสำหรับค้นหาหรือสร้าง Product ใหม่
function getOrCreateProduct($condb, $product_name)
{
    // ค้นหาสินค้าจากชื่อ
    $stmt = $condb->prepare("SELECT product_id FROM products WHERE product_name = :product_name");
    $stmt->execute([':product_name' => $product_name]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result['product_id'];
    }

    // ถ้าไม่พบสินค้า สร้างข้อมูลใหม่
    $product_id = generateUUID();
    $stmt = $condb->prepare("
        INSERT INTO products (
            product_id,
            product_name,
            created_by,
            created_at
        ) VALUES (
            :product_id,
            :product_name,
            :created_by,
            NOW()
        )
    ");

    $stmt->execute([
        ':product_id' => $product_id,
        ':product_name' => $product_name,
        ':created_by' => $_SESSION['user_id']
    ]);

    return $product_id;
}

// ฟังก์ชันสำหรับตรวจสอบหรือสร้างข้อมูลลูกค้า
function getOrCreateCustomer($condb, $customer_name)
{
    $stmt = $condb->prepare("SELECT customer_id FROM customers WHERE customer_name = :customer_name");
    $stmt->execute([':customer_name' => $customer_name]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result['customer_id'];
    }

    $customer_id = generateUUID();
    $stmt = $condb->prepare("
        INSERT INTO customers (
            customer_id,
            customer_name,
            created_by,
            created_at
        ) VALUES (
            :customer_id,
            :customer_name,
            :created_by,
            NOW()
        )
    ");

    $stmt->execute([
        ':customer_id' => $customer_id,
        ':customer_name' => $customer_name,
        ':created_by' => $_SESSION['user_id']
    ]);

    return $customer_id;
}

// ตรวจสอบการอัปโหลดไฟล์
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        'status' => 'error',
        'message' => 'กรุณาเลือกไฟล์',
        'errors' => []
    ]);
    exit();
}

// ตรวจสอบนามสกุลและขนาดไฟล์
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
    $spreadsheet = IOFactory::load($file['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    // ข้าม 2 แถวแรก (คำอธิบายและหัวตาราง)
    array_shift($rows);
    array_shift($rows);

    $success = 0;
    $detailed_errors = [];
    $valid_statuses = [
        'นำเสนอโครงการ (Presentations)',
        'ใบเสนอราคา (Quotation)',
        'ยื่นประมูล (Bidding)',
        'ชนะ (Win)',
        'แพ้ (Loss)',
        'รอการพิจารณา (On Hold)',
        'ยกเลิก (Cancled)'
    ];

    foreach ($rows as $index => $row) {
        if (empty(array_filter($row))) {
            continue;
        }

        $row_num = $index + 3;
        $row_errors = [];

        try {
            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty(trim($row[0]))) {
                $row_errors[] = "วันที่ขายห้ามเป็นค่าว่าง";
            } elseif (!\DateTime::createFromFormat('Y-m-d', trim($row[0]))) {
                $row_errors[] = "รูปแบบวันที่ไม่ถูกต้อง (ต้องเป็น YYYY-MM-DD)";
            }

            if (empty(trim($row[1]))) {
                $row_errors[] = "สถานะห้ามเป็นค่าว่าง";
            } elseif (!in_array(trim($row[1]), $valid_statuses)) {
                $row_errors[] = "สถานะไม่ถูกต้อง";
            }

            if (empty(trim($row[2]))) {
                $row_errors[] = "ชื่อโครงการห้ามเป็นค่าว่าง";
            }

            if (empty(trim($row[3]))) {
                $row_errors[] = "ชื่อลูกค้าห้ามเป็นค่าว่าง";
            }

            if (empty(trim($row[4]))) {
                $row_errors[] = "สินค้าห้ามเป็นค่าว่าง";
            }

            // ถ้าไม่มีข้อผิดพลาด ดำเนินการบันทึกข้อมูล
            if (empty($row_errors)) {
                $condb->beginTransaction();

                // ตรวจสอบหรือสร้าง Product ใหม่
                try {
                    $product_id = getOrCreateProduct($condb, trim($row[4]));
                } catch (Exception $e) {
                    throw new Exception("ไม่สามารถสร้างสินค้าใหม่ได้: " . $e->getMessage());
                }

                // ตรวจสอบหรือสร้างข้อมูลลูกค้า
                $customer_id = getOrCreateCustomer($condb, trim($row[3]));

                // เตรียมบันทึกข้อมูลโครงการ
                $stmt = $condb->prepare("
                    INSERT INTO projects (
                        project_id, sales_date, status, project_name,
                        customer_id, product_id, sale_vat, cost_vat,
                        sale_no_vat, cost_no_vat, gross_profit,
                        created_by, created_at, vat
                    ) VALUES (
                        :project_id, :sales_date, :status, :project_name,
                        :customer_id, :product_id, :sale_vat, :cost_vat,
                        :sale_no_vat, :cost_no_vat, :gross_profit,
                        :created_by, NOW(), 7.00
                    )
                ");

                $result = $stmt->execute([
                    ':project_id' => generateUUID(),
                    ':sales_date' => trim($row[0]),
                    ':status' => trim($row[1]),
                    ':project_name' => trim($row[2]),
                    ':customer_id' => $customer_id,
                    ':product_id' => $product_id,
                    ':sale_vat' => !empty($row[5]) ? $row[5] : null,
                    ':cost_vat' => !empty($row[6]) ? $row[6] : null,
                    ':sale_no_vat' => !empty($row[7]) ? $row[7] : null,
                    ':cost_no_vat' => !empty($row[8]) ? $row[8] : null,
                    ':gross_profit' => !empty($row[9]) ? $row[9] : null,
                    ':created_by' => $_SESSION['user_id']
                ]);

                if ($result) {
                    $condb->commit();
                    $success++;
                }
            }
        } catch (Exception $e) {
            if (isset($condb) && $condb->inTransaction()) {
                $condb->rollBack();
            }
            $row_errors[] = $e->getMessage();
        }

        // บันทึกข้อผิดพลาด (ถ้ามี)
        if (!empty($row_errors)) {
            $detailed_errors[] = [
                'row' => $row_num,
                'errors' => $row_errors,
                'data' => [
                    'sales_date' => $row[0] ?? '',
                    'status' => $row[1] ?? '',
                    'project_name' => $row[2] ?? '',
                    'customer_name' => $row[3] ?? '',
                    'product' => $row[4] ?? '',
                    'sale_vat' => $row[5] ?? '',
                    'cost_vat' => $row[6] ?? '',
                    'sale_no_vat' => $row[7] ?? '',
                    'cost_no_vat' => $row[8] ?? '',
                    'gross_profit' => $row[9] ?? ''
                ]
            ];
        }
    }

    // ส่งผลลัพธ์กลับ
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
