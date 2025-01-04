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

// ดึงข้อมูลทีมจากฐานข้อมูล
$team_stmt = $condb->prepare("SELECT team_name FROM teams ORDER BY team_name");
$team_stmt->execute();
$teams = $team_stmt->fetchAll(PDO::FETCH_COLUMN);
$teams_list = implode(",", $teams);

// ดึงข้อมูลหัวหน้างาน
// แก้ไข SQL ในการดึงข้อมูลหัวหน้างาน
$supervisor_stmt = $condb->prepare("SELECT CONCAT(first_name, ' ', last_name) as full_name 
                                 FROM users WHERE role IN ('Executive', 'Sale Supervisor')");
$supervisor_stmt->execute();
$supervisors = $supervisor_stmt->fetchAll(PDO::FETCH_COLUMN);
$supervisors_list = implode(",", $supervisors);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// กำหนดหัวตาราง
$headers = [
    'First Name TH*',
    'Last Name TH*',
    'First Name EN',
    'Last Name EN',
    'Nickname TH',
    'Nickname EN',
    'Gender',
    'Birth Date',
    'Personal Email',
    'Company Email',
    'Phone',
    'Position',
    'Department',
    'Team',
    'Supervisor',
    'Address',
    'Hire Date'
];

// จัดรูปแบบหัวตาราง
$sheet->fromArray($headers, NULL, 'A1');
$sheet->getStyle('A1:Q1')->applyFromArray([
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
    'สมชาย',             // First Name TH*
    'ใจดี',              // Last Name TH*
    'Somchai',           // First Name EN
    'Jaidee',           // Last Name EN
    'ชาย',              // Nickname TH
    'Chai',             // Nickname EN
    'ชาย',              // Gender
    '1990-01-01',       // Birth Date
    'somchai@email.com', // Personal Email
    'somchai@company.com', // Company Email
    '0812345678',       // Phone
    'Sale Executive',   // Position
    'Sales',            // Department
    'Innovation_PIT',   // Team
    'John Doe',         // Supervisor
    '123 ถนนสุขุมวิท กรุงเทพ 10110', // Address
    '2024-01-01'        // Hire Date
];
$sheet->fromArray([$example], NULL, 'A2');

// Data Validation
// Gender
$genderValidation = $sheet->getDataValidation('G2:G1000');
$genderValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
    ->setAllowBlank(false)
    ->setShowInputMessage(true)
    ->setShowErrorMessage(true)
    ->setShowDropDown(true)
    ->setFormula1('"ชาย,หญิง,อื่นๆ"');

// Team
$teamValidation = $sheet->getDataValidation('N2:N1000');
$teamValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
    ->setAllowBlank(true)
    ->setShowInputMessage(true)
    ->setShowErrorMessage(true)
    ->setShowDropDown(true)
    ->setFormula1('"' . $teams_list . '"');

// Supervisor
$supervisorValidation = $sheet->getDataValidation('O2:O1000');
$supervisorValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
    ->setAllowBlank(true)
    ->setShowInputMessage(true)
    ->setShowErrorMessage(true)
    ->setShowDropDown(true)
    ->setFormula1('"' . $supervisors_list . '"');

// ปรับความกว้างคอลัมน์
foreach (range('A', 'Q') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// คำอธิบาย
// คำอธิบาย
$sheet->insertNewRowBefore(1);
$sheet->mergeCells('A1:Q1');
$sheet->setCellValue('A1', 'คำแนะนำ: ช่องที่มีเครื่องหมาย * จำเป็นต้องกรอก (First Name TH และ Last Name TH), Gender ต้องเลือกจาก (ชาย,หญิง,อื่นๆ), Team และ Supervisor ต้องตรงกับที่มีในระบบ, วันที่ให้กรอกในรูปแบบ YYYY-MM-DD');
$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
$sheet->getRowDimension(1)->setRowHeight(40);

// บันทึกไฟล์
$writer = new Xlsx($spreadsheet);
$writer->save('templates/employees_import_template.xlsx');

echo "สร้าง Template เรียบร้อยแล้ว";
