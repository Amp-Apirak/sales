<?php
session_start();
include('../../../config/condb.php');

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
        LEFT JOIN users u ON p.created_by = u.user_id
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cost Report | Sale Pipeline</title>
    <?php include '../../../include/header.php'; ?>

    <!-- /* ใช้ฟอนต์ Noto Sans Thai กับ label */ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <style>
        /* ใช้ฟอนต์ Noto Sans Thai กับ label */
        th,
        h1 {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 700;
            /* ปรับระดับน้ำหนักของฟอนต์ */
            font-size: 16px;
            color: #333;
        }

        .custom-th,
        .lead {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 600;
            font-size: 18px;
            color: #FF5733;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <!-- หัวข้อและปุ่มพิมพ์ -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <!-- ส่วนหัวรายงาน -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="float-right">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <img src="../../../assets/img/pit.png" width="160" height="75">
                                                </div>
                                                <div class="text-right">
                                                    <small>Point IT Consulting Co., Ltd : 19 ซอยสุภาพงษ์1 แยก 6 แขวงหนองบอน เขตประเวศ กรุงเทพฯ 10250</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- เส้นแบ่งจางๆ ชิดข้อความ -->
                                    <div class="row">
                                        <div class="col-12 ">
                                            <hr style="border-top: 1px solid #dddddd; margin-top: 5px; margin-bottom: 20px;">
                                        </div>
                                    </div>

                                    <br>

                                    <!-- ข้อมูลโครงการ -->
                                    <div class="row invoice-info mb-6">
                                        <div class="col-sm-3">
                                            <h5>ข้อมูลผู้ขาย</h5>
                                            <address>
                                                <strong><?php echo $project['first_name'] . ' ' . $project['last_name']; ?></strong><br>
                                                Phone: <?php echo $project['phone']; ?><br>
                                                Email: <?php echo $project['email']; ?>
                                            </address>
                                        </div>
                                        <div class="col-sm-4">
                                            <h5>ข้อมูลลูกค้า</h5>
                                            <address>
                                                <strong><?php echo $project['company']; ?></strong><br>
                                                <?php echo $project['customer_name']; ?><br>
                                                <?php echo $project['customer_address']; ?><br>
                                                Phone: <?php echo $project['customer_phone']; ?><br>
                                                Email: <?php echo $project['customer_email']; ?>
                                            </address>
                                        </div>
                                        <div class="col-sm-4">
                                            <h5>ข้อมูลโครงการ</h5>
                                            <b>เลขที่สัญญา:</b> <?php echo $project['contract_no']; ?><br>
                                            <b>ชื่อโครงการ:</b> <?php echo $project['project_name']; ?><br>
                                            <b>ผลิตภัณฑ์:</b> <?php echo $project['product_name']; ?><br>
                                        </div>
                                    </div>

                                    <!-- ตารางรายการต้นทุน -->
                                    <div class="row mt-4">
                                        <div class="col-12 table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th class="text-nowrap">Part No.</th>
                                                        <th>Description</th>
                                                        <th>QTY</th>
                                                        <th>Unit</th>
                                                        <th class="text-right">Price/Unit</th>
                                                        <th class="text-right">Total Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($costs as $cost): ?>
                                                        <tr>
                                                            <td><?php echo $cost['type']; ?></td>
                                                            <td><?php echo $cost['part_no']; ?></td>
                                                            <td><?php echo $cost['description']; ?></td>
                                                            <td><?php echo $cost['quantity']; ?></td>
                                                            <td><?php echo $cost['unit']; ?></td>
                                                            <td class="text-right"><?php echo number_format($cost['price_per_unit'], 2); ?></td>
                                                            <td class="text-right"><?php echo number_format($cost['total_amount'], 2); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- สรุปยอดเงิน -->
                                    <div class="row">
                                        <!-- ส่วนแสดงหมายเหตุ -->
                                        <div class="col-6">
                                            <p class="lead mb-2">หมายเหตุ:</p>
                                            <div class="text-muted" style="line-height: 1.3;">
                                                <?php
                                                $remark_lines = explode("\n", $project['remark']);
                                                foreach ($remark_lines as $line) {
                                                    echo htmlspecialchars(trim($line)) . "<br>";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <?php
                                            // คำนวณยอดรวม
                                            $total = 0;
                                            foreach ($costs as $cost) {
                                                $total += $cost['total_amount'];
                                            }

                                            // คำนวณ VAT และยอดรวมทั้งสิ้น
                                            $vat = $total * 0.07;
                                            $grand_total = $total + $vat;
                                            ?>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th style="width:50%">ยอดรวม:</th>
                                                        <td class="text-right"><?php echo number_format($total, 2); ?> บาท</td>
                                                    </tr>
                                                    <tr>
                                                        <th>ภาษีมูลค่าเพิ่ม (7%):</th>
                                                        <td class="text-right"><?php echo number_format($vat, 2); ?> บาท</td>
                                                    </tr>
                                                    <tr>
                                                        <th>ยอดรวมทั้งสิ้น:</th>
                                                        <td class="text-right"><?php echo number_format($grand_total, 2); ?> บาท</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../../../include/footer.php'; ?>
    </div>

    <!-- เพิ่ม CSS สำหรับการพิมพ์ -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .content-wrapper {
                margin-left: 0 !important;
            }
        }
    </style>
</body>

</html>