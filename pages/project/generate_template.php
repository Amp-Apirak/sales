<?php
// เรียกใช้ไฟล์ที่จำเป็น
require_once '../../include/Add_session.php';
require '../../vendor/autoload.php';

// นำเข้าคลาสที่ต้องใช้งาน
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

try {
    // สร้าง Spreadsheet ใหม่
    $spreadsheet = new Spreadsheet();

    // ตั้งค่า sheet หลัก
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Import Template');

    // กำหนดหัวตารางและคำอธิบาย
    $headers = [
        'Sales Date*' => 'วันที่ขาย',
        'Status*' => 'สถานะ',
        'Project Name*' => 'ชื่อโครงการ',
        'Customer Name*' => 'บริษัทลูกค้า',
        'Product*' => 'สินค้า',
        'Sale Price (Vat)' => 'ราคาขายรวม VAT',
        'Cost Price (Vat)' => 'ต้นทุนรวม VAT',
        'Sale Price' => 'ราคาขาย',
        'Cost Price' => 'ต้นทุน',
        'Gross Profit' => 'กำไรขั้นต้น'
    ];

    // นำหัวตารางลงใน Excel
    $sheet->fromArray(array_keys($headers), NULL, 'A2');
    $lastColumn = chr(64 + count($headers)); // แปลงจำนวนคอลัมน์เป็นตัวอักษร (A, B, C, ...)

    // จัดรูปแบบหัวตาราง
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => '000000']
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'E2EFDA']
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ]
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray($headerStyle);
    $sheet->getRowDimension(2)->setRowHeight(30);

    // เพิ่มข้อมูลตัวอย่าง
    $example = [
        '2024-12-31',                          // วันที่ขาย
        'นำเสนอโครงการ (Presentations)',       // สถานะ
        'โครงการระบบบริหารจัดการการขาย',       // ชื่อโครงการ
        'Point IT Consulting Co.,Ltd.',         // บริษัทลูกค้า
        'Smart Healthcare',                     // สินค้า
        '100000',                              // ราคาขายรวม VAT
        '70000',                               // ต้นทุนรวม VAT
        '93457.94',                            // ราคาขาย
        '65420.56',                            // ต้นทุน
        '28037.38'                             // กำไรขั้นต้น
    ];
    $sheet->fromArray([$example], NULL, 'A3');

    // ตั้งค่า Validation สำหรับวันที่
    $dateValidation = $sheet->getDataValidation('A3:A1000');
    $dateValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_DATE)
        ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
        ->setAllowBlank(false)
        ->setShowInputMessage(true)
        ->setShowErrorMessage(true)
        ->setFormula1('DATE(2020,1,1)')
        ->setFormula2('DATE(2030,12,31)')
        ->setPromptTitle('วันที่')
        ->setPrompt('กรุณากรอกวันที่ในรูปแบบ YYYY-MM-DD')
        ->setErrorTitle('วันที่ไม่ถูกต้อง')
        ->setError('กรุณากรอกวันที่ระหว่างปี 2020-2030');

    // ตั้งค่า Validation สำหรับสถานะ
    $validStatuses = [
        'นำเสนอโครงการ (Presentations)',
        'ใบเสนอราคา (Quotation)',
        'ยื่นประมูล (Bidding)',
        'ชนะ (Win)',
        'แพ้ (Loss)',
        'รอการพิจารณา (On Hold)',
        'ยกเลิก (Cancled)'
    ];

    $statusValidation = $sheet->getDataValidation('B3:B1000');
    $statusValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
        ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
        ->setAllowBlank(false)
        ->setShowInputMessage(true)
        ->setShowErrorMessage(true)
        ->setShowDropDown(true)
        ->setFormula1('"' . implode(',', $validStatuses) . '"')
        ->setPromptTitle('สถานะ')
        ->setPrompt('เลือกสถานะจากรายการ')
        ->setErrorTitle('ข้อผิดพลาด')
        ->setError('กรุณาเลือกสถานะจากรายการที่กำหนด');

    // ตั้งค่า Validation สำหรับตัวเลข (ราคาและต้นทุน)
    foreach (range('F', 'J') as $col) { // เปลี่ยนช่วงคอลัมน์ให้ตรงกับคอลัมน์ตัวเลข
        $numericValidation = $sheet->getDataValidation($col . '3:' . $col . '1000');
        $numericValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_DECIMAL)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
            ->setAllowBlank(true)
            ->setShowInputMessage(true)
            ->setShowErrorMessage(true)
            ->setFormula1('0')
            ->setFormula2('999999999.99')
            ->setPromptTitle('ตัวเลข')
            ->setPrompt('กรุณากรอกตัวเลขที่มากกว่าหรือเท่ากับ 0')
            ->setErrorTitle('ค่าไม่ถูกต้อง')
            ->setError('กรุณากรอกตัวเลขที่มากกว่าหรือเท่ากับ 0');
    }

    // ปรับความกว้างคอลัมน์อัตโนมัติ
    foreach (range('A', $lastColumn) as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // เพิ่มคำอธิบายการใช้งาน
    $sheet->insertNewRowBefore(1);
    $sheet->mergeCells("A1:{$lastColumn}1");
    $instructions = <<<EOT
คำแนะนำ: 
- ช่องที่มีเครื่องหมาย * จำเป็นต้องกรอก
- Sales Date: กรอกในรูปแบบ YYYY-MM-DD
- Status: เลือกจากรายการที่กำหนดในช่อง dropdown
- Project Name: กรอกชื่อโครงการ
- Customer Name: กรอกชื่อบริษัทลูกค้า
- Product: กรอกชื่อสินค้า
- ราคาและต้นทุน: กรอกเป็นตัวเลขเท่านั้น
- Gross Profit: จะคำนวณอัตโนมัติจาก Sale Price - Cost Price
EOT;

    $sheet->setCellValue('A1', $instructions);

    // จัดรูปแบบคำอธิบาย
    $sheet->getStyle('A1')->applyFromArray([
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true
        ],
        'font' => [
            'bold' => true,
            'size' => 11
        ]
    ]);
    $sheet->getRowDimension(1)->setRowHeight(180); // เพิ่มความสูงเพื่อรองรับคำอธิบายที่เพิ่มขึ้น

    // บันทึกไฟล์
    $writer = new Xlsx($spreadsheet);
    $writer->save('templates/project_import_template.xlsx');

    echo "สร้าง Template เรียบร้อยแล้ว";
} catch (Exception $e) {
    echo "เกิดข้อผิดพลาด: " . $e->getMessage();
}
