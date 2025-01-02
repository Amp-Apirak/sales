<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ตรวจสอบการ Login
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // ถ้าไม่ได้ login ให้ redirect ไปหน้า login
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

// ตรวจสอบว่ามีการส่ง project_id มาหรือไม่
if (!isset($_GET['project_id']) || empty($_GET['project_id'])) {
    echo "ไม่พบข้อมูลโครงการ";
    exit;
}

// รับค่าและถอดรหัส project_id 
$project_id = decryptUserId($_GET['project_id']);

try {
    // SQL query ดึงข้อมูลโครงการ
    $sql = "SELECT p.*, 
            u.first_name, u.last_name, u.email as seller_email, u.phone as seller_phone,
            pr.product_name, pr.product_description,
            c.customer_name, c.company as customer_company, c.address as customer_address, 
            c.phone as customer_phone, c.email as customer_email,
            t.team_name
            FROM projects p
            LEFT JOIN users u ON p.seller = u.user_id
            LEFT JOIN products pr ON p.product_id = pr.product_id
            LEFT JOIN customers c ON p.customer_id = c.customer_id
            LEFT JOIN teams t ON u.team_id = t.team_id
            WHERE p.project_id = :project_id";

    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':project_id', $project_id, PDO::PARAM_STR);
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        echo "ไม่พบข้อมูลโครงการ";
        exit;
    }

    // SQL query ดึงข้อมูลต้นทุน
    $sql_costs = "SELECT * FROM project_costs 
                  WHERE project_id = :project_id 
                  ORDER BY created_at ASC";

    $stmt_costs = $condb->prepare($sql_costs);
    $stmt_costs->bindParam(':project_id', $project_id, PDO::PARAM_STR);
    $stmt_costs->execute();
    $costs = $stmt_costs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Project Details</title>

    <!-- Google Font & CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/adminlte.min.css">

    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>assets/img/favicon.ico">

    <style>
        @media print {
            @page {
                size: A4;
                margin: 15mm;
            }

            body {
                font-size: 12pt;
                line-height: 1.5;
            }

            .no-print {
                display: none !important;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }

        body {
            font-family: 'Source Sans Pro', sans-serif;
            line-height: 1.6;
        }

        .project-header {
            border-bottom: 2px solid #000;
            margin-bottom: 30px;
            padding-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            clear: both;
        }

        .project-header img {
            height: 70px;
            margin-right: 20px;
        }

        .project-header .header-info {
            text-align: right;
            line-height: 1.4;
        }

        .company-info {
            margin-bottom: 40px;
        }

        .section-title {
            background-color: #f8f9fa;
            padding: 8px 15px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
            font-weight: bold;
            color: #2c3e50;
        }

        .invoice-info {
            margin-bottom: 30px;
        }

        .invoice-col {
            padding: 15px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .table {
            margin-bottom: 30px;
        }

        .table th {
            background-color: #f8f9fa;
            border-top: 2px solid #dee2e6;
            font-weight: 600;
        }

        .table td,
        .table th {
            padding: 12px;
            vertical-align: middle;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .text-muted {
            color: #6c757d;
            padding: 15px;
            border-radius: 5px;
            background-color: #f8f9fa;
        }

        address {
            line-height: 1.6;
            margin-bottom: 0;
        }

        .btn {
            padding: 8px 20px;
            margin: 5px;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .row {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Main content -->
        <section class="invoice p-3">
            <!-- แถวส่วนหัวโครงการ -->
            <div class="row">
                <div class="col-12">
                    <div class="project-header">
                        <img src="../../assets/img/pit.png" alt="Company Logo" style="height: 60px;" class="float-left mr-2">
                        <div class="float-right">
                            <small>วันที่พิมพ์: <?php echo date('d/m/Y'); ?></small><br>
                            <small>เลขที่: <?php echo htmlspecialchars($project['contract_no']); ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- แถวข้อมูลโครงการ -->
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    <div class="section-title">ข้อมูลผู้ขาย</div>
                    <address>
                        <strong><?php echo htmlspecialchars($project['first_name'] . ' ' . $project['last_name']); ?></strong><br>
                        <?php echo htmlspecialchars($project['team_name']); ?><br>
                        โทร: <?php echo htmlspecialchars($project['seller_phone']); ?><br>
                        อีเมล: <?php echo htmlspecialchars($project['seller_email']); ?>
                    </address>
                </div>

                <div class="col-sm-4 invoice-col">
                    <div class="section-title">ข้อมูลลูกค้า</div>
                    <address>
                        <strong><?php echo htmlspecialchars($project['customer_name']); ?></strong><br>
                        <?php echo htmlspecialchars($project['customer_company']); ?><br>
                        <?php echo htmlspecialchars($project['customer_address']); ?><br>
                        โทร: <?php echo htmlspecialchars($project['customer_phone']); ?><br>
                        อีเมล: <?php echo htmlspecialchars($project['customer_email']); ?>
                    </address>
                </div>

                <div class="col-sm-4 invoice-col">
                    <div class="section-title">ข้อมูลโครงการ</div>
                    <b>สถานะ:</b> <?php echo htmlspecialchars($project['status']); ?><br>
                    <b>วันที่เริ่ม:</b> <?php echo htmlspecialchars($project['start_date']); ?><br>
                    <b>วันที่สิ้นสุด:</b> <?php echo htmlspecialchars($project['end_date']); ?><br>
                    <b>สินค้า:</b> <?php echo htmlspecialchars($project['product_name']); ?>
                </div>
            </div>

            <!-- ตารางแสดงรายการต้นทุน -->
            <div class="row mt-4">
                <div class="col-12 table-responsive">
                    <div class="section-title">รายการต้นทุน</div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ลำดับ</th>
                                <th>ประเภท</th>
                                <th>รหัสสินค้า</th>
                                <th>รายละเอียด</th>
                                <th>จำนวน</th>
                                <th>ราคา/หน่วย</th>
                                <th>ราคารวม</th>
                                <th>ต้นทุน/หน่วย</th>
                                <th>ต้นทุนรวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_amount = 0;
                            $total_cost = 0;
                            foreach ($costs as $index => $cost):
                                $total_amount += $cost['total_amount'];
                                $total_cost += $cost['total_cost'];
                            ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($cost['type']); ?></td>
                                    <td><?php echo htmlspecialchars($cost['part_no']); ?></td>
                                    <td><?php echo htmlspecialchars($cost['description']); ?></td>
                                    <td><?php echo number_format($cost['quantity']); ?></td>
                                    <td><?php echo number_format($cost['price_per_unit'], 2); ?></td>
                                    <td><?php echo number_format($cost['total_amount'], 2); ?></td>
                                    <td><?php echo number_format($cost['cost_per_unit'], 2); ?></td>
                                    <td><?php echo number_format($cost['total_cost'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- สรุปยอดเงิน -->
            <div class="row">
                <div class="col-6">
                    <div class="section-title">หมายเหตุ</div>
                    <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                        <?php echo htmlspecialchars($project['remark'] ?? 'ไม่มีหมายเหตุ'); ?>
                    </p>
                </div>

                <div class="col-6">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th style="width:50%">ยอดรวมก่อนภาษี:</th>
                                <td><?php echo number_format($total_amount, 2); ?></td>
                            </tr>
                            <tr>
                                <th>ภาษีมูลค่าเพิ่ม (<?php echo $project['vat']; ?>%):</th>
                                <td><?php echo number_format($total_amount * ($project['vat'] / 100), 2); ?></td>
                            </tr>
                            <tr>
                                <th>ยอดรวมสุทธิ:</th>
                                <td><?php echo number_format($total_amount * (1 + $project['vat'] / 100), 2); ?></td>
                            </tr>
                            <tr>
                                <th>กำไรขั้นต้น:</th>
                                <td><?php echo number_format($total_amount - $total_cost, 2); ?></td>
                            </tr>
                            <tr>
                                <th>กำไรขั้นต้น (%):</th>
                                <td>
                                    <?php
                                    if ($total_amount > 0) {
                                        echo number_format(($total_amount - $total_cost) / $total_amount * 100, 2);
                                    } else {
                                        echo "0.00";
                                    }
                                    ?>%
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- ปุ่มพิมพ์ -->
    <div class="row no-print">
        <div class="col-12">
            <button class="btn btn-default" onclick="window.print();">
                <i class="fas fa-print"></i> พิมพ์
            </button>
            <button class="btn btn-primary float-right" onclick="window.close();">
                <i class="fas fa-times"></i> ปิด
            </button>
        </div>
    </div>

    <script>
        // เมื่อโหลดหน้าเสร็จ ให้เปิดหน้าต่างพิมพ์อัตโนมัติ
        window.addEventListener("load", function() {
            window.print();
        });
    </script>
</body>

</html>