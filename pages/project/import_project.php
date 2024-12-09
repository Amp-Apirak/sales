<?php
include '../../include/Add_session.php'; // เรียก session และเชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['importFile'])) {
    $file = $_FILES['importFile'];

    // ตรวจสอบชนิดไฟล์
    $allowedExtensions = ['csv', 'xlsx'];
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);

    if (!in_array($fileExtension, $allowedExtensions)) {
        echo "ชนิดไฟล์ไม่ถูกต้อง กรุณาใช้ไฟล์ .csv หรือ .xlsx เท่านั้น";
        exit;
    }

    // ตรวจสอบขนาดไฟล์
    if ($file['size'] > 5 * 1024 * 1024) {
        echo "ไฟล์มีขนาดใหญ่เกิน 5MB";
        exit;
    }

    // อ่านไฟล์ (กรณี .csv)
    if ($fileExtension == 'csv') {
        $fileData = fopen($file['tmp_name'], 'r');
        fgetcsv($fileData); // ข้าม Header

        while (($row = fgetcsv($fileData, 1000, ",")) !== FALSE) {
            // ดึงข้อมูลแต่ละคอลัมน์จาก CSV
            $projectName = $row[0];
            $customerId = $row[1];
            $status = $row[2];
            $startDate = $row[3];
            $endDate = $row[4];
            $costVat = $row[5];
            $saleVat = $row[6];

            // เตรียมข้อมูลสำหรับ INSERT
            $stmt = $condb->prepare("
                INSERT INTO projects (project_name, customer_id, status, start_date, end_date, cost_vat, sale_vat, created_by)
                VALUES (:project_name, :customer_id, :status, :start_date, :end_date, :cost_vat, :sale_vat, :created_by)
            ");

            $stmt->execute([
                ':project_name' => $projectName,
                ':customer_id' => $customerId,
                ':status' => $status,
                ':start_date' => $startDate,
                ':end_date' => $endDate,
                ':cost_vat' => $costVat,
                ':sale_vat' => $saleVat,
                ':created_by' => $_SESSION['user_id']
            ]);
        }
        fclose($fileData);
    }

    echo "นำเข้าข้อมูลสำเร็จ!";
    header("Location: project.php");
    exit;
}
