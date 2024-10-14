<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ดึงข้อมูลผู้ใช้จาก session
$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;
$user_team_id = $_SESSION['team_id'] ?? 0;

// ตรวจสอบว่า project_id ถูกส่งมาจาก URL หรือไม่
if (!isset($_GET['project_id']) || empty($_GET['project_id'])) {
    echo "ไม่พบข้อมูลโครงการ";
    exit;
}

// รับ project_id จาก URL และทำการถอดรหัส
$project_id = decryptUserId($_GET['project_id']);

// ดึงข้อมูลโครงการและผู้สร้าง
try {
    $sql = "SELECT p.*, u.team_id as creator_team_id, 
            u.first_name, u.last_name, u.email as seller_email, u.phone as seller_phone,
            pr.product_name, pr.product_description,
            c.customer_name, c.company, c.address, c.phone as customer_phone, c.email as customer_email,
            t.team_name,
            tl.first_name as team_leader_first_name, tl.last_name as team_leader_last_name,
            creator.first_name as creator_first_name, creator.last_name as creator_last_name,
            updater.first_name as updater_first_name, updater.last_name as updater_last_name
            FROM projects p 
            LEFT JOIN users u ON p.seller = u.user_id 
            LEFT JOIN products pr ON p.product_id = pr.product_id 
            LEFT JOIN customers c ON p.customer_id = c.customer_id 
            LEFT JOIN teams t ON u.team_id = t.team_id 
            LEFT JOIN users tl ON t.team_leader = tl.user_id
            LEFT JOIN users creator ON p.created_by = creator.user_id
            LEFT JOIN users updater ON p.updated_by = updater.user_id
            WHERE p.project_id = :project_id";

    $stmt = $condb->prepare($sql);
    $stmt->bindParam(':project_id', $project_id, PDO::PARAM_STR);
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        echo "ไม่พบโครงการที่ต้องการแสดง";
        exit;
    }

    // ตรวจสอบสิทธิ์การเข้าถึง
    $hasAccess = false;

    switch ($role) {
        case 'Executive':
            $hasAccess = true;
            break;
        case 'Sale Supervisor':
            if ($user_team_id == $project['creator_team_id']) {
                $hasAccess = true;
            }
            break;
        case 'Seller':
            if ($user_id == $project['created_by']) {
                $hasAccess = true;
            }
            break;
    }

    if (!$hasAccess) {
        echo "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
        exit;
    }

    // ดึงข้อมูลการชำระเงินของโครงการ
    $sql_payments = "SELECT * FROM project_payments WHERE project_id = :project_id ORDER BY payment_number";
    $stmt_payments = $condb->prepare($sql_payments);
    $stmt_payments->bindParam(':project_id', $project_id, PDO::PARAM_STR);
    $stmt_payments->execute();
    $payments = $stmt_payments->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// สร้างหรือดึง CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ฟังก์ชันสำหรับกำหนดคลาส CSS ตามสถานะการชำระเงิน
function getStatusClass($status)
{
    switch ($status) {
        case 'Paid':
            return 'text-success';
        case 'Pending':
            return 'text-warning';
        case 'Overdue':
            return 'text-danger';
        default:
            return '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "project"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | View Project</title>
    <?php include '../../include/header.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* การกำหนดรูปแบบการแสดงผลของหน้าเว็บ */
        body {
            font-family: 'Noto Sans Thai', sans-serif;
            background-color: #f8f9fa;
            font-size: 14px;
        }

        .content-wrapper {
            padding: 20px;
        }

        .project-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .project-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .project-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .project-date {
            font-size: 14px;
            margin-top: 10px;
        }

        .info-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .info-card-header {
            background-color: #f8f9fa;
            color: black;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 18px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .info-card-body {
            padding: 20px;
        }

        .info-item {
            display: flex;
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: 600;
            color: #34495e;
            width: 150px;
            flex-shrink: 0;
        }

        .info-value {
            flex-grow: 1;
            color: #2c3e50;
        }

        /* สไตล์สำหรับส่วนสรุปทางการเงิน */
        .financial-summary {
            background-color: #ecf0f1;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .financial-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .financial-label {
            font-weight: 600;
            color: #34495e;
        }

        .financial-value {
            font-weight: 700;
            color: #2c3e50;
        }

        .profit-highlight {
            color: #27ae60;
            font-size: 18px;
        }

        /* สไตล์ปุ่มแก้ไข */
        .edit-button {
            float: right;
            background-color: transparent;
            color: #6a11cb;
            border: none;
            padding: 2px 5px;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            font-size: 14px;
        }

        .edit-button:hover {
            background-color: #6a11cb;
            color: white;
        }

        /* สไตล์สำหรับตาราง */
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* สไตล์สำหรับสถานะการชำระเงิน */
        .text-success {
            color: #28a745 !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        /* สไตล์สำหรับปุ่มขนาดเล็ก */
        .btn-sm {
            padding: .25rem .5rem;
            font-size: .875rem;
            line-height: 1.5;
            border-radius: .2rem;
        }

        .mr-1 {
            margin-right: .25rem !important;
        }

        /* สไตล์สำหรับการแสดงผลบนอุปกรณ์มือถือ */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
        }

        @media (max-width: 767px) {
            .payment-card .card {
                border: 1px solid #ddd;
                border-radius: 8px;
            }

            .payment-card .card-body {
                padding: 15px;
            }

            .payment-card .card-title {
                font-size: 18px;
                margin-bottom: 10px;
            }

            .payment-card .card-text {
                font-size: 14px;
                margin-bottom: 15px;
            }

            .payment-card .btn-group {
                display: flex;
                justify-content: space-between;
            }
        }

        /* สไตล์สำหรับการจัดการความสูงของการ์ด */
        .equal-height-cards {
            display: flex;
            flex-wrap: wrap;
        }

        .equal-height-cards>[class*='col-'] {
            display: flex;
            flex-direction: column;
        }

        .equal-height-cards .info-card {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .equal-height-cards .info-card-body {
            flex: 1;
        }

        .info-card {
            min-height: 300px;
            /* ปรับตามความเหมาะสม */
        }

        /* ตั้งค่าซ่อนปุ่มการพิมพ์ */
        @media print {

            .edit-button,
            .btn-sm,
            .btn-info,
            .btn-danger,
            .btn-group,
            .no-print {
                display: none !important;
            }

            .wrapper {
                min-height: initial !important;
                background-color: white !important;
            }

            .content-wrapper {
                margin-left: 0 !important;
                background-color: white !important;
            }

            .main-sidebar {
                display: none !important;
            }

            .main-header {
                display: none !important;
            }

            .main-footer {
                display: none !important;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .container-fluid {
                width: 100%;
                padding: 0;
                margin: 0;
            }

            .info-card {
                break-inside: avoid;
            }
        }

        /* ตั้งค่าให้การพิมพ์มีความสวยงามมากขึ้น */
        @media print {
            body {
                font-size: 12pt;
            }

            .info-card {
                page-break-inside: avoid;
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                page-break-after: avoid;
            }

            img {
                max-width: 100% !important;
            }

            .table {
                border-collapse: collapse !important;
            }

            .table td,
            .table th {
                background-color: #fff !important;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">





    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>
        <div class="content-wrapper">
            <section class="content">
                <!-- เพิ่มส่วนนี้หลังจาก project-header -->
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#project-info" data-toggle="tab">ข้อมูลโครงการ</a></li>
                            <li class="nav-item"><a class="nav-link" href="#documents" data-toggle="tab">เอกสารแนบ</a></li>
                            <li class="nav-item"><a class="nav-link" href="#images" data-toggle="tab">รูปภาพ</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="project-info">
                                <!-- ใส่เนื้อหาของข้อมูลโครงการทั้งหมดที่มีอยู่เดิมตรงนี้ -->
                                <div class="info-card">
                                    <!-- ข้อมูลโครงการ -->
                                </div>
                                <div class="row equal-height-cards">
                                    <!-- ข้อมูลลูกค้าและข้อมูลผู้ขาย -->
                                </div>
                                <div class="info-card">
                                    <!-- ข้อมูลทางการเงิน -->
                                </div>
                                <div class="info-card">
                                    <!-- ข้อมูลการชำระเงิน -->
                                </div>
                            </div>
                            <div class="tab-pane" id="documents">
                                <!-- ตารางแสดงเอกสารแนบ -->
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ชื่อเอกสาร</th>
                                            <th>ประเภท</th>
                                            <th>วันที่อัปโหลด</th>
                                            <th>การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- ตัวอย่างข้อมูล (คุณต้องแทนที่ด้วยข้อมูลจริงจากฐานข้อมูล) -->
                                        <tr>
                                            <td>สัญญาโครงการ.pdf</td>
                                            <td>PDF</td>
                                            <td>2023-05-15</td>
                                            <td>
                                                <button class="btn btn-sm btn-info">ดู</button>
                                                <button class="btn btn-sm btn-danger">ลบ</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="images">
                                <!-- แสดงรูปภาพ -->
                                <div class="row">
                                    <!-- ตัวอย่างการแสดงรูปภาพ (คุณต้องแทนที่ด้วยข้อมูลจริงจากฐานข้อมูล) -->
                                    <div class="col-md-4">
                                        <img src="path/to/image1.jpg" class="img-fluid mb-3" alt="Project Image">
                                    </div>
                                    <div class="col-md-4">
                                        <img src="path/to/image2.jpg" class="img-fluid mb-3" alt="Project Image">
                                    </div>
                                    <div class="col-md-4">
                                        <img src="path/to/image3.jpg" class="img-fluid mb-3" alt="Project Image">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
        </div>
        <?php include '../../include/footer.php'; ?>
    </div>
</body>

</html>

<script>
    // ตัวแปรสำหรับเก็บข้อมูลการชำระเงินทั้งหมด
    let payments = <?php echo json_encode($payments); ?>;
    let totalSaleAmount = <?php echo $project['sale_vat']; ?>; // ราคาขาย (รวมภาษี)

    // ฟังก์ชันสำหรับฟอร์แมตตัวเลขให้มีคอมม่าและทศนิยม 2 ตำแหน่ง
    function formatNumber(num) {
        return parseInt(num).toLocaleString('th-TH');
    }

    // ฟังก์ชันสำหรับแปลงข้อความที่มีคอมม่าเป็นตัวเลข
    function parseFormattedNumber(str) {
        return parseFloat(str.replace(/,/g, '')) || 0;
    }
    // ฟังก์ชันสำหรับคำนวณจำนวนเงินจากเปอร์เซ็นต์
    function calculateAmountFromPercentage() {
        const percentage = parseFloat(document.getElementById('paymentPercentage').value) || 0;
        const amount = (percentage / 100) * totalSaleAmount;
        document.getElementById('amount').value = formatNumber(amount);
        updateAmountPaid();
    }


    // ฟังก์ชันสำหรับคำนวณเปอร์เซ็นต์จากจำนวนเงิน
    function calculatePercentageFromAmount() {
        const amount = parseFormattedNumber(document.getElementById('amount').value);
        const percentage = (amount / totalSaleAmount) * 100;
        document.getElementById('paymentPercentage').value = percentage.toFixed(2);
        updateAmountPaid();
    }

    // ฟังก์ชันสำหรับอัปเดตจำนวนเงินที่ชำระแล้วตามสถานะการชำระเงิน
    function updateAmountPaid() {
        const status = document.getElementById('status').value;
        const amount = parseFormattedNumber(document.getElementById('amount').value);
        if (status === 'Paid') {
            document.getElementById('amountPaid').value = formatNumber(amount);
        } else {
            document.getElementById('amountPaid').value = formatNumber(0);
        }
    }

    // ฟังก์ชันเปิด Modal สำหรับเพิ่มการชำระเงิน
    function openAddPaymentModal() {
        document.getElementById('paymentModalLabel').textContent = 'เพิ่มการชำระเงิน';
        document.getElementById('paymentForm').reset();
        document.getElementById('paymentId').value = '';
        document.getElementById('paymentNumber').value = payments.length + 1;
        $('#paymentModal').modal('show');
    }

    // ฟังก์ชันเปิด Modal สำหรับแก้ไขการชำระเงิน
    function editPayment(paymentId) {
        const payment = payments.find(p => p.payment_id === paymentId);
        if (payment) {
            document.getElementById('paymentModalLabel').textContent = 'แก้ไขการชำระเงิน';
            document.getElementById('paymentId').value = payment.payment_id;
            document.getElementById('paymentNumber').value = payment.payment_number;
            document.getElementById('paymentPercentage').value = ((payment.amount / totalSaleAmount) * 100).toFixed(2);
            document.getElementById('amount').value = formatNumber(payment.amount);
            document.getElementById('dueDate').value = payment.due_date;
            document.getElementById('status').value = payment.status;
            document.getElementById('paymentDate').value = payment.payment_date || '';
            document.getElementById('amountPaid').value = formatNumber(payment.amount_paid);
            $('#paymentModal').modal('show');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'ไม่พบข้อมูล',
                text: 'ไม่พบข้อมูลการชำระเงินที่ต้องการแก้ไข',
                confirmButtonText: 'ตกลง'
            });
        }
    }

    // ฟังก์ชันสำหรับบันทึกข้อมูลการชำระเงิน (เพิ่มหรือแก้ไข)
    function savePayment() {
        const paymentData = {
            csrf_token: document.querySelector('input[name="csrf_token"]').value,
            payment_id: document.getElementById('paymentId').value,
            project_id: '<?php echo $project_id; ?>',
            payment_number: document.getElementById('paymentNumber').value,
            amount: parseFormattedNumber(document.getElementById('amount').value),
            payment_percentage: parseFloat(document.getElementById('paymentPercentage').value),
            due_date: document.getElementById('dueDate').value,
            status: document.getElementById('status').value,
            payment_date: document.getElementById('paymentDate').value,
            amount_paid: parseFormattedNumber(document.getElementById('amountPaid').value)
        };

        // คำนวณเปอร์เซ็นต์รวมของการชำระเงินทั้งหมด
        let totalPercentage = payments.reduce((total, payment) => {
            return total + parseFloat(payment.payment_percentage);
        }, 0);

        // เพิ่มเปอร์เซ็นต์ของการชำระเงินใหม่
        totalPercentage += parseFloat(paymentData.payment_percentage);

        if (totalPercentage > 100) {
            Swal.fire({
                icon: 'warning',
                title: 'เกินขีดจำกัด',
                text: 'เปอร์เซ็นต์รวมของการชำระเงินเกิน 100% ของราคาขาย',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        $.ajax({
            url: 'save_payment.php',
            type: 'POST',
            data: paymentData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'บันทึกข้อมูลสำเร็จ',
                        confirmButtonText: 'ตกลง'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message,
                        confirmButtonText: 'ตกลง'
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + textStatus,
                    confirmButtonText: 'ตกลง'
                });
            }
        });
    }

    // ฟังก์ชันสำหรับลบข้อมูลการชำระเงิน
    function deletePayment(paymentId) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบรายการชำระเงินนี้ใช่หรือไม่?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_payment.php',
                    type: 'POST',
                    data: {
                        csrf_token: document.querySelector('input[name="csrf_token"]').value,
                        payment_id: paymentId
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Raw response:', response);
                        if (response && response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ',
                                text: response.message || 'ลบข้อมูลสำเร็จ',
                                confirmButtonText: 'ตกลง'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: (response && response.message) ? response.message : 'ไม่สามารถลบข้อมูลได้',
                                confirmButtonText: 'ตกลง'
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error:', textStatus, errorThrown);
                        console.log('Response Text:', jqXHR.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + textStatus,
                            confirmButtonText: 'ตกลง'
                        });
                    }
                });
            }
        });
    }

    // เพิ่ม Event Listeners
    document.getElementById('paymentPercentage').addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9.]/g, '');
        e.target.value = value;
        calculateAmountFromPercentage();
    });

    document.getElementById('amount').addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9.]/g, '');
        e.target.value = formatNumber(value);
        calculatePercentageFromAmount();
    });

    document.getElementById('status').addEventListener('change', updateAmountPaid);
</script>