<?php
require_once '../../include/Add_session.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// ดึงข้อมูลสถานะโครงการ (Status) จากฐานข้อมูล
$status_stmt = $condb->prepare("SELECT DISTINCT status FROM projects");
$status_stmt->execute();
$statuses = $status_stmt->fetchAll(PDO::FETCH_COLUMN);
$status_list = implode(",", $statuses);

// ดึงข้อมูลสินค้า/บริการ (Product) จากฐานข้อมูล
$product_stmt = $condb->prepare("SELECT product_name FROM products ORDER BY product_name");
$product_stmt->execute();
$products = $product_stmt->fetchAll(PDO::FETCH_COLUMN);
$product_list = implode(",", $products);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// กำหนดหัวตาราง
$headers = [
    'Project Name*',
    'Start Date (YYYY-MM-DD)*',
    'End Date (YYYY-MM-DD)',
    'Status*',
    'Customer ID*',
    'Contract No.',
    'Product Name*',
    'Sale (No VAT)*',
    'Cost (No VAT)*',
    'VAT (%)',
    'Created By*',
    'Seller ID*',
    'Remark'
];

// จัดรูปแบบหัวตาราง
$sheet->fromArray($headers, NULL, 'A1');
$sheet->getStyle('A1:M1')->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'E2EFDA']
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN
        ]
    ]
]);

// เพิ่มตัวอย่างข้อมูล
$example = [
    'โครงการพัฒนาระบบจัดการข้อมูล',
    '2024-01-15',
    '2024-12-31',
    'Quotation', // ตัวอย่าง Status
    'CUS001',
    'CT2024-001',
    'Software Development', // ตัวอย่าง Product
    '500000',
    '300000',
    '7',
    'USER001',
    'SELL001',
    'หมายเหตุเพิ่มเติม'
];
$sheet->fromArray([$example], NULL, 'A2');

// เพิ่มการตรวจสอบข้อมูล (Data Validation)
// สำหรับ Status
$statusValidation = $sheet->getDataValidation('D2:D1000');
$statusValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
    ->setAllowBlank(false)
    ->setShowInputMessage(true)
    ->setShowErrorMessage(true)
    ->setShowDropDown(true)
    ->setFormula1('"' . $status_list . '"');

// สำหรับ Product Name
$productValidation = $sheet->getDataValidation('G2:G1000');
$productValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
    ->setAllowBlank(false)
    ->setShowInputMessage(true)
    ->setShowErrorMessage(true)
    ->setShowDropDown(true)
    ->setFormula1('"' . $product_list . '"');

// ปรับความกว้างคอลัมน์อัตโนมัติ
foreach (range('A', 'M') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// เพิ่มคำอธิบายการใช้งาน
$sheet->insertNewRowBefore(1);
$sheet->mergeCells('A1:M1');
$sheet->setCellValue('A1', 'คำแนะนำ: ช่องที่มีเครื่องหมาย * จำเป็นต้องกรอก, Status และ Product Name ต้องตรงกับที่มีในระบบ');
$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
$sheet->getRowDimension(1)->setRowHeight(40);

// บันทึกไฟล์
$template_path = 'templates/project_import_template.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($template_path);

echo "สร้าง Template สำเร็จ: <a href='$template_path'>ดาวน์โหลดที่นี่</a>";
