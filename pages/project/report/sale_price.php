<?php
// ตรวจสอบและถอดรหัส project_id
if (!isset($_GET['project_id'])) {
    echo "ไม่พบรหัสโครงการ";
    exit;
}

$project_id = decryptUserId($_GET['project_id']);

// ดึงข้อมูลโครงการและข้อมูลที่เกี่ยวข้อง
$sql = "SELECT p.*,
       u.first_name, u.last_name, u.email, u.phone,
       c.customer_name, c.company, c.address as customer_address,
       c.phone as customer_phone, c.email as customer_email,
       pr.product_name
       FROM projects p
       LEFT JOIN users u ON p.seller = u.user_id
       LEFT JOIN customers c ON p.customer_id = c.customer_id
       LEFT JOIN products pr ON p.product_id = pr.product_id
       WHERE p.project_id = :project_id";

$stmt = $condb->prepare($sql);
$stmt->bindParam(':project_id', $project_id, PDO::PARAM_STR);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// ดึงข้อมูลต้นทุน
$sql_costs = "SELECT * FROM project_costs WHERE project_id = :project_id ORDER BY type";
$stmt_costs = $condb->prepare($sql_costs);
$stmt_costs->bindParam(':project_id', $project_id, PDO::PARAM_STR);
$stmt_costs->execute();
$costs = $stmt_costs->fetchAll(PDO::FETCH_ASSOC);

// คำนวณยอดรวม
$total = 0;
foreach ($costs as $cost) {
    if (isset($cost['total_amount'])) {
        $total += floatval($cost['total_amount']);
    }
}

// คำนวณ VAT และยอดรวมทั้งสิ้น
$vat = $total * 0.07;
$grand_total = $total + $vat;
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานราคาขาย</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }

        .container {
            width: 100%;
            padding: 20px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 30px;
            padding: 20px;
            background: #fff;
        }

        .company-details h1 {
            font-size: 1.5rem;
            margin-bottom: 5px;
            color: #333;
        }

        .company-details p {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
        }

        .logo-container img {
            width: 160px;
            height: auto;
        }

        /* Info Cards */
        .info-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
            padding: 0 20px;
        }

        .info-card {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .info-card-header {
            background: #f8f9fa;
            padding: 15px;
            font-weight: 600;
            color: #333;
            border-bottom: 1px solid #e0e0e0;
            border-radius: 8px 8px 0 0;
        }

        .info-card-body {
            padding: 15px;
        }

        .info-item {
            margin-bottom: 12px;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-label {
            display: block;
            font-weight: 500;
            color: #555;
            margin-bottom: 3px;
        }

        .info-value {
            display: block;
            color: #333;
            line-height: 1.4;
            word-break: break-word;
        }

        /* Table */
        .table-container {
            margin: 0 20px 30px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #e0e0e0;
            text-align: left;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        td {
            color: #555;
        }

        .text-right {
            text-align: right;
        }

        /* Summary Section */
        .summary-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 0 20px;
        }

        .remarks-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .remarks-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .price-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .price-summary table {
            width: 100%;
            margin: 0;
            border: none;
        }

        .price-summary td {
            border: none;
            padding: 8px 0;
        }

        .total-row td {
            font-weight: 600;
            color: #333;
            border-top: 2px solid #e0e0e0;
            padding-top: 15px;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .container {
                padding: 0;
            }

            .info-card {
                break-inside: avoid;
            }

            .table-container {
                break-inside: auto;
            }

            tr {
                break-inside: avoid;
            }

            .summary-section {
                break-inside: avoid;
            }
        }

        @media (max-width: 768px) {
            .info-cards {
                grid-template-columns: 1fr;
            }

            .summary-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-details">
                <h1>Point IT Consulting Co., Ltd</h1>
                <p>19 ซอยสุภาพงษ์1 แยก 6 แขวงหนองบอน</p>
                <p>เขตประเวศ กรุงเทพฯ 10250</p>
            </div>
            <div class="logo-container">
                <img src="../../assets/img/pit.png" alt="Company Logo">
            </div>
        </div>

        <!-- Info Cards -->
        <div class="info-cards">
            <!-- ข้อมูลผู้ขาย -->
            <div class="info-card">
                <div class="info-card-header">ข้อมูลผู้ขาย</div>
                <div class="info-card-body">
                    <div class="info-item">
                        <span class="info-label">ชื่อ-นามสกุล:</span>
                        <span class="info-value"><?php echo htmlspecialchars($project['first_name'] . ' ' . $project['last_name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">โทรศัพท์:</span>
                        <span class="info-value"><?php echo htmlspecialchars($project['phone']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">อีเมล:</span>
                        <span class="info-value"><?php echo htmlspecialchars($project['email']); ?></span>
                    </div>
                </div>
            </div>

            <!-- ข้อมูลลูกค้า -->
            <div class="info-card">
                <div class="info-card-header">ข้อมูลลูกค้า</div>
                <div class="info-card-body">
                    <div class="info-item">
                        <span class="info-label">บริษัท:</span>
                        <span class="info-value"><?php echo htmlspecialchars($project['company']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">ที่อยู่:</span>
                        <span class="info-value"><?php echo htmlspecialchars($project['customer_address']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">โทรศัพท์:</span>
                        <span class="info-value"><?php echo htmlspecialchars($project['customer_phone']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">อีเมล:</span>
                        <span class="info-value"><?php echo htmlspecialchars($project['customer_email']); ?></span>
                    </div>
                </div>
            </div>

            <!-- ข้อมูลโครงการ -->
            <div class="info-card">
                <div class="info-card-header">ข้อมูลโครงการ</div>
                <div class="info-card-body">
                    <div class="info-item">
                        <span class="info-label">เลขที่สัญญา:</span>
                        <span class="info-value"><?php echo htmlspecialchars($project['contract_no']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">ชื่อโครงการ:</span>
                        <span class="info-value"><?php echo htmlspecialchars($project['project_name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">ผลิตภัณฑ์:</span>
                        <span class="info-value"><?php echo htmlspecialchars($project['product_name']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th class="text-center text-nowrap">Part No.</th>
                        <th>Description</th>
                        <th style="text-align: center;">QTY</th>
                        <th>Unit</th>
                        <th style="text-align: right;">Price/Unit</th>
                        <th style="text-align: right;">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($costs as $cost): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cost['type']); ?></td>
                            <td><?php echo htmlspecialchars($cost['part_no']); ?></td>
                            <td><?php echo htmlspecialchars($cost['description']); ?></td>
                            <td style="text-align: center;"><?php echo htmlspecialchars($cost['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($cost['unit']); ?></td>
                            <td class="text-right"><?php echo number_format($cost['price_per_unit'], 2); ?></td>
                            <td class="text-right"><?php echo number_format($cost['total_amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <!-- Remarks -->
            <div class="remarks-box">
                <div class="remarks-title">หมายเหตุ:</div>
                <div class="remarks-content">
                    <?php
                    if (isset($project['remark']) && !empty($project['remark'])) {
                        $remark_lines = explode("\n", $project['remark']);
                        foreach ($remark_lines as $line) {
                            echo "<p>" . htmlspecialchars(trim($line)) . "</p>";
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- Price Summary -->
            <div class="price-summary">
                <table>
                    <tr>
                        <td>ยอดรวม:</td>
                        <td class="text-right"><?php echo number_format($total, 2); ?> บาท</td>
                    </tr>
                    <tr>
                        <td>ภาษีมูลค่าเพิ่ม (7%):</td>
                        <td class="text-right"><?php echo number_format($vat, 2); ?> บาท</td>
                    </tr>
                    <tr class="total-row">
                        <td>ยอดรวมทั้งสิ้น:</td>
                        <td class="text-right"><?php echo number_format($grand_total, 2); ?> บาท</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>