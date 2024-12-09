<?php
require_once '../../include/Add_session.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Get products for dropdown
$product_stmt = $condb->prepare("SELECT product_name FROM products ORDER BY product_name");
$product_stmt->execute();
$products = $product_stmt->fetchAll(PDO::FETCH_COLUMN);
$products_list = implode(",", $products);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Define headers
$headers = [
    'Status*',
    'Product*',
    'Project Name*'
];

// Style headers
$sheet->fromArray($headers, NULL, 'A1');
$sheet->getStyle('A1:C1')->applyFromArray([
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

// Add example data
$example = [
    'Win',
    'Product A',
    'Example Project Name'
];
$sheet->fromArray([$example], NULL, 'A2');

// Add data validation
// For Status
$statusValidation = $sheet->getDataValidation('A2:A1000');
$statusValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
    ->setAllowBlank(false)
    ->setShowInputMessage(true)
    ->setShowErrorMessage(true)
    ->setShowDropDown(true)
    ->setFormula1('"Win,Loss,On Hold,Quotation,Negotiation,Bidding,Cancelled"');

// For Products
$productValidation = $sheet->getDataValidation('B2:B1000');
$productValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
    ->setAllowBlank(false)
    ->setShowInputMessage(true)
    ->setShowErrorMessage(true)
    ->setShowDropDown(true)
    ->setFormula1('"' . $products_list . '"');

// Auto-size columns
foreach (range('A', 'C') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Add instructions
$sheet->insertNewRowBefore(1);
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A1', 'คำแนะนำ: ช่องที่มีเครื่องหมาย * จำเป็นต้องกรอก, Status ต้องเป็นหนึ่งใน (Win, Loss, On Hold, Quotation, Negotiation, Bidding, Cancelled), Product ต้องตรงกับที่มีในระบบ');
$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
$sheet->getRowDimension(1)->setRowHeight(40);

// Save file
$writer = new Xlsx($spreadsheet);
$writer->save('templates/project_import_template.xlsx');

echo "สร้าง Template เรียบร้อยแล้ว";