<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
include('../../../config/condb.php');
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// ดึงข้อมูล Supplier จากฐานข้อมูล
$supplier_stmt = $condb->prepare("SELECT supplier_name FROM suppliers ORDER BY supplier_name");
$supplier_stmt->execute();
$suppliers = $supplier_stmt->fetchAll(PDO::FETCH_COLUMN);
$suppliers_list = implode(",", $suppliers);

// ดึงข้อมูล Company จากฐานข้อมูล
$company_stmt = $condb->prepare("SELECT DISTINCT company FROM suppliers ORDER BY company");
$company_stmt->execute();
$companies = $company_stmt->fetchAll(PDO::FETCH_COLUMN);
$companies_list = implode(",", $companies);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// กำหนดหัวตาราง
$headers = [
    'Supplier Name*',
    'Company*',
    'Position',
    'Phone',
    'Email',
    'Address',
    'Office Phone',
    'Extension',
    'Remark'  // เก็บ Remark ไว้
];

// จัดรูปแบบหัวตาราง
$sheet->fromArray($headers, NULL, 'A1');
$sheet->getStyle('A1:I1')->applyFromArray([
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

// ตัวอย่างข้อมูล
$example = [
    'Supplier A',             // Supplier Name*
    'Company A',              // Company*
    'Manager',                // Position
    '0812345678',             // Phone
    'supplierA@email.com',    // Email
    '123 ถนนสุขุมวิท กรุงเทพ 10110', // Address
    '022345678',              // Office Phone
    '123',                    // Extension
    'Supplier ที่มีประวัติการทำงานที่ดี' // Remark
];
$sheet->fromArray([$example], NULL, 'A2');

// Data Validation
// Company
$companyValidation = $sheet->getDataValidation('B2:B1000');
$companyValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
    ->setAllowBlank(false)
    ->setShowInputMessage(true)
    ->setShowErrorMessage(true)
    ->setShowDropDown(true)
    ->setFormula1('"' . $companies_list . '"');

// ปรับความกว้างคอลัมน์
foreach (range('A', 'I') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// คำอธิบาย
$sheet->insertNewRowBefore(1);
$sheet->mergeCells('A1:I1');
$sheet->setCellValue('A1', 'คำแนะนำ: ช่องที่มีเครื่องหมาย * จำเป็นต้องกรอก (Supplier Name และ Company), Company ต้องตรงกับที่มีในระบบ');
$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
$sheet->getRowDimension(1)->setRowHeight(40);

// บันทึกไฟล์
$writer = new Xlsx($spreadsheet);
$writer->save('templates/supplier_template.xlsx');

echo "สร้าง Template เรียบร้อยแล้ว";
