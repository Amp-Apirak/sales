<?php
require_once '../../include/Add_session.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// ดึงรายชื่อทีมจากฐานข้อมูล
$team_stmt = $condb->prepare("SELECT team_name FROM teams ORDER BY team_name");
$team_stmt->execute();
$teams = $team_stmt->fetchAll(PDO::FETCH_COLUMN);
$teams_list = implode(",", $teams);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// กำหนดหัวตาราง
$headers = [
    'Username*',
    'First Name*',
    'Last Name',
    'Email*',
    'Role*',
    'Team Name',
    'Position',
    'Phone',
    'Company',
    'Password*'
];

// จัดรูปแบบหัวตาราง
$sheet->fromArray($headers, NULL, 'A1');
$sheet->getStyle('A1:J1')->applyFromArray([
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
    'john.doe',
    'John',
    'Doe',
    'john@example.com',
    'Seller',
    'Innovation_PIT', // ตัวอย่างชื่อทีม
    'Sales Executive',
    '0812345678',
    'Point IT Consulting Co.,Ltd.',
    '123456'
];
$sheet->fromArray([$example], NULL, 'A2');

// เพิ่มการตรวจสอบข้อมูล (Data Validation)
// สำหรับ Role
$roleValidation = $sheet->getDataValidation('E2:E1000');
$roleValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
    ->setAllowBlank(false)
    ->setShowInputMessage(true)
    ->setShowErrorMessage(true)
    ->setShowDropDown(true)
    ->setFormula1('"Executive,Sale Supervisor,Seller,Engineer"');

// สำหรับ Team Name
$teamValidation = $sheet->getDataValidation('F2:F1000');
$teamValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
    ->setAllowBlank(true)
    ->setShowInputMessage(true)
    ->setShowErrorMessage(true)
    ->setShowDropDown(true)
    ->setFormula1('"' . $teams_list . '"');

// ปรับความกว้างคอลัมน์อัตโนมัติ
foreach (range('A', 'J') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// เพิ่มคำอธิบายการใช้งาน
$sheet->insertNewRowBefore(1);
$sheet->mergeCells('A1:J1');
$sheet->setCellValue('A1', 'คำแนะนำ: ช่องที่มีเครื่องหมาย * จำเป็นต้องกรอก, Role ต้องเป็นหนึ่งใน (Executive, Sale Supervisor, Seller, Engineer), Team Name ต้องตรงกับที่มีในระบบ');
$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
$sheet->getRowDimension(1)->setRowHeight(40);

// บันทึกไฟล์
$writer = new Xlsx($spreadsheet);
$writer->save('templates/account_import_template.xlsx');

echo "สร้าง Template เรียบร้อยแล้ว";
