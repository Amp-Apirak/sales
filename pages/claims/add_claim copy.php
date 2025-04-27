<?php
// รวมไฟล์ตั้งค่า session และการเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// ตรวจสอบสิทธิ์ผู้ใช้และดึงข้อมูลจาก session
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];

// สร้าง CSRF token เพื่อป้องกันการโจมตี
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ดึงข้อมูลโครงการที่ผู้ใช้เกี่ยวข้อง
$project_sql = "SELECT project_id, project_name FROM projects WHERE 1=1";
$params = [];
if ($role == 'Sale Supervisor') {
    $project_sql .= " AND (created_by IN (SELECT user_id FROM users WHERE team_id = :team_id) OR created_by = :user_id OR seller = :user_id)";
    $params[':team_id'] = $team_id;
    $params[':user_id'] = $user_id;
} elseif ($role == 'Seller' || $role == 'Engineer') {
    $project_sql .= " AND (created_by = :user_id OR seller = :user_id)";
    $params[':user_id'] = $user_id;
}
$project_sql .= " ORDER BY project_name ASC";
$project_stmt = $condb->prepare($project_sql);
$project_stmt->execute($params);
$projects = $project_stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลประเภทค่าใช้จ่าย
$expense_type_sql = "SELECT type_id, type_name FROM expense_types WHERE is_active = 1 ORDER BY type_name ASC";
$expense_type_stmt = $condb->prepare($expense_type_sql);
$expense_type_stmt->execute();
$expense_types = $expense_type_stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงวงเงินอนุมัติตามบทบาทผู้ใช้
$limit_sql = "SELECT max_amount FROM expense_approval_limits WHERE role = :role";
$limit_stmt = $condb->prepare($limit_sql);
$limit_stmt->bindParam(':role', $role, PDO::PARAM_STR);
$limit_stmt->execute();
$approval_limit = $limit_stmt->fetch(PDO::FETCH_ASSOC)['max_amount'] ?? 0;

// กำหนดวันที่ปัจจุบันในรูปแบบ dd/mm/yyyy
$today = date('d/m/Y');

// ตัวแปรสำหรับเก็บข้อความแจ้งเตือน
$success_message = $error_message = '';
$item_count = isset($_SESSION['expense_form_data']['item_count']) ? (int)$_SESSION['expense_form_data']['item_count'] : 1;

// ฟังก์ชันตรวจสอบ CSRF token
function verifyCsrfToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ฟังก์ชันคำนวณยอดรวม
function calculateTotalAmount($form_data)
{
    $total = 0;
    if (isset($form_data['amount']) && is_array($form_data['amount'])) {
        foreach ($form_data['amount'] as $amount) {
            $total += (float)$amount;
        }
    }
    return $total;
}

// ประมวลผลการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'])) {
    $form_data = $_POST;

    if (isset($_POST['add_item'])) {
        // เพิ่มรายการใหม่
        $item_count++;
        $form_data['item_count'] = $item_count;
        $_SESSION['expense_form_data'] = $form_data;
    } elseif (isset($_POST['remove_item']) && isset($_POST['remove_item_id'])) {
        // ลบรายการที่ระบุ
        $remove_id = (int)$_POST['remove_item_id'];
        if ($item_count > 1) {
            $item_count--;
            $new_items = [];
            $index = 1;

            foreach ($form_data['expense_type'] as $i => $type) {
                if ($i != $remove_id) {
                    $new_items[$index] = [
                        'expense_type' => $type,
                        'amount' => $form_data['amount'][$i] ?? '',
                        'description' => $form_data['description'][$i] ?? '',
                        'expense_date_start' => $form_data['expense_date_start'][$i] ?? '',
                        'expense_date_end' => $form_data['expense_date_end'][$i] ?? '',
                        'origin' => $form_data['origin'][$i] ?? '',
                        'destination' => $form_data['destination'][$i] ?? '',
                        'distance' => $form_data['distance'][$i] ?? '',
                        'rate' => $form_data['rate'][$i] ?? ''
                    ];
                    $index++;
                }
            }

            // อัปเดตข้อมูลฟอร์ม
            $form_data = array_merge($form_data, [
                'expense_type' => array_column($new_items, 'expense_type'),
                'amount' => array_column($new_items, 'amount'),
                'description' => array_column($new_items, 'description'),
                'expense_date_start' => array_column($new_items, 'expense_date_start'),
                'expense_date_end' => array_column($new_items, 'expense_date_end'),
                'origin' => array_column($new_items, 'origin'),
                'destination' => array_column($new_items, 'destination'),
                'distance' => array_column($new_items, 'distance'),
                'rate' => array_column($new_items, 'rate'),
                'item_count' => $item_count
            ]);

            $_SESSION['expense_form_data'] = $form_data;
        } else {
            $error_message = 'ต้องมีอย่างน้อย 1 รายการค่าใช้จ่าย';
        }
    } elseif (isset($_POST['calculate']) && isset($_POST['calculate_item_id'])) {
        // คำนวณค่าเดินทาง
        $calc_id = (int)$_POST['calculate_item_id'];
        $distance = (float)($form_data['distance'][$calc_id] ?? 0);
        $rate = (float)($form_data['rate'][$calc_id] ?? 0);

        if ($distance > 0 && $rate > 0) {
            $form_data['amount'][$calc_id] = $distance * $rate;
            $success_message = "คำนวณค่าเดินทางเรียบร้อย: {$distance} กม. × {$rate} บาท = " . number_format($form_data['amount'][$calc_id], 2) . " บาท";
        } else {
            $error_message = 'กรุณากรอกระยะทางและอัตราค่าเดินทางให้ถูกต้อง';
        }

        $_SESSION['expense_form_data'] = $form_data;
    } elseif (isset($_POST['submit'])) {
        // บันทึกข้อมูลการเบิก (ในตัวอย่างนี้เป็นการจำลอง)
        $total_amount = calculateTotalAmount($form_data);
        if ($total_amount > $approval_limit) {
            $error_message = "ยอดรวมเกินวงเงินอนุมัติ ({$approval_limit} บาท)";
        } else {
            $success_message = 'บันทึกข้อมูลการเบิกเรียบร้อยแล้ว';
            unset($_SESSION['expense_form_data']); // ล้างข้อมูลฟอร์ม
            $item_count = 1; // รีเซ็ตจำนวนรายการ
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error_message = 'CSRF token ไม่ถูกต้อง';
}

// ดึงข้อมูลฟอร์มจาก session
$form_data = $_SESSION['expense_form_data'] ?? [];
$total_amount = calculateTotalAmount($form_data);
?>

<!DOCTYPE html>
<html lang="th">
<?php $menu = "claims"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | เพิ่มรายการเบิกค่าใช้จ่าย</title>
    <?php include '../../include/header.php'; ?>
    <!-- CSS สำหรับ DatePicker และ Select2 -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <style>
        /* สไตล์สำหรับการ์ดรายการค่าใช้จ่าย */
        .expense-item-card {
            border-left: 4px solid #3f51b5;
            margin-bottom: 20px;
        }

        /* สไตล์สำหรับส่วนค่าเดินทาง */
        .travel-expense-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
            border-left: 4px solid #4caf50;
        }

        /* ฟิลด์ที่ต้องกรอก */
        .required-field::after {
            content: " *";
            color: #f44336;
        }

        /* สรุปยอดรวม */
        .total-summary {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }

        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #004d40;
        }

        /* ซ่อนปุ่มในโหมดพิมพ์ */
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include '../../include/navbar.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">เพิ่มรายการเบิกค่าใช้จ่าย</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">หน้าแรก</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>pages/claims/claims.php">การเบิกค่าใช้จ่าย</a></li>
                                <li class="breadcrumb-item active">เพิ่มรายการเบิก</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- แสดงข้อความแจ้งเตือน -->
                    <?php if ($success_message): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <strong><i class="fas fa-check-circle mr-2"></i>สำเร็จ!</strong> <?php echo htmlspecialchars($success_message); ?>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    <?php endif; ?>
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong><i class="fas fa-exclamation-circle mr-2"></i>ผิดพลาด!</strong> <?php echo htmlspecialchars($error_message); ?>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    <?php endif; ?>

                    <!-- แสดงวงเงินอนุมัติ -->
                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <h5><i class="icon fas fa-info"></i> ข้อมูลสำคัญ!</h5>
                        วงเงินสูงสุดที่คุณสามารถเบิกได้คือ <strong><?php echo number_format($approval_limit, 2); ?> บาท</strong> ตามบทบาท <?php echo htmlspecialchars($role); ?>
                    </div>

                    <!-- ฟอร์มการเบิกค่าใช้จ่าย -->
                    <form id="expenseForm" method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="item_count" value="<?php echo $item_count; ?>">

                        <!-- ข้อมูลหลักของการเบิก -->
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-file-invoice-dollar mr-2"></i>ข้อมูลหลักของการเบิก</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="expense_title" class="required-field">หัวข้อการเบิก</label>
                                            <input type="text" class="form-control" id="expense_title" name="expense_title"
                                                value="<?php echo htmlspecialchars($form_data['expense_title'] ?? ''); ?>"
                                                placeholder="ระบุหัวข้อการเบิก" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="project_id">โครงการที่เกี่ยวข้อง</label>
                                            <select class="form-control select2" id="project_id" name="project_id">
                                                <option value="">-- ไม่เกี่ยวข้องกับโครงการ --</option>
                                                <?php foreach ($projects as $project): ?>
                                                    <option value="<?php echo $project['project_id']; ?>"
                                                        <?php echo ($form_data['project_id'] ?? '') == $project['project_id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($project['project_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="expense_date" class="required-field">วันที่เบิก</label>
                                            <div class="input-group date" id="expense_date_picker" data-target-input="nearest">
                                                <input type="text" class="form-control datetimepicker-input" id="expense_date" name="expense_date"
                                                    value="<?php echo htmlspecialchars($form_data['expense_date'] ?? $today); ?>"
                                                    placeholder="วัน/เดือน/ปี" data-target="#expense_date_picker" required>
                                                <div class="input-group-append" data-target="#expense_date_picker" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="far fa-calendar-alt"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="submitter">ผู้ทำรายการเบิก</label>
                                            <input type="text" class="form-control" id="submitter" value="<?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>" readonly>
                                            <input type="hidden" name="submitter_id" value="<?php echo $user_id; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="expense_number">เลขที่การเบิก</label>
                                            <input type="text" class="form-control" id="expense_number" name="expense_number"
                                                value="<?php echo htmlspecialchars($form_data['expense_number'] ?? '(ระบบจะสร้างให้อัตโนมัติ)'); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="remark">หมายเหตุ</label>
                                            <textarea class="form-control" id="remark" name="remark" rows="3"
                                                placeholder="ระบุหมายเหตุเพิ่มเติม (ถ้ามี)"><?php echo htmlspecialchars($form_data['remark'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- รายการค่าใช้จ่าย -->
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-list-ul mr-2"></i>รายการค่าใช้จ่าย</h3>
                            </div>
                            <div class="card-body">
                                <?php for ($i = 1; $i <= $item_count; $i++): ?>
                                    <div class="expense-item-card card" id="expense-item-<?php echo $i; ?>">
                                        <div class="card-header bg-light">
                                            <h3 class="card-title">รายการที่ <?php echo $i; ?></h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="expense_type_<?php echo $i; ?>" class="required-field">ประเภทค่าใช้จ่าย</label>
                                                        <select class="form-control select2 expense-type" id="expense_type_<?php echo $i; ?>" name="expense_type[<?php echo $i; ?>]"
                                                            onchange="showTravelFields(<?php echo $i; ?>, this.value)" required>
                                                            <option value="">-- เลือกประเภทค่าใช้จ่าย --</option>
                                                            <?php foreach ($expense_types as $type): ?>
                                                                <option value="<?php echo $type['type_name']; ?>"
                                                                    <?php echo ($form_data['expense_type'][$i] ?? '') == $type['type_name'] ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars($type['type_name']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="required-field" for="amount_<?php echo $i; ?>">จำนวนเงิน (บาท)</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span></div>
                                                            <input
                                                                type="text"
                                                                class="form-control amount-input"
                                                                id="amount_<?php echo $i; ?>"
                                                                name="amount[<?php echo $i; ?>]"
                                                                value="<?php echo isset($form_data['amount'][$i]) ? number_format((float)$form_data['amount'][$i], 2) : ''; ?>"
                                                                placeholder="0.00" autocomplete="off" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- ส่วนค่าเดินทาง -->
                                            <?php $show_travel = ($form_data['expense_type'][$i] ?? '') == 'ค่าเดินทาง' || ($form_data['expense_type'][$i] ?? '') == 'ค่าน้ำมัน'; ?>
                                            <div id="travel-expense-<?php echo $i; ?>" class="travel-expense-section" style="<?php echo $show_travel ? 'display: block;' : 'display: none;'; ?>">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="origin_<?php echo $i; ?>" class="required-field">ต้นทาง</label>
                                                            <input type="text" class="form-control" id="origin_<?php echo $i; ?>" name="origin[<?php echo $i; ?>]"
                                                                value="<?php echo htmlspecialchars($form_data['origin'][$i] ?? ''); ?>" placeholder="ระบุสถานที่ต้นทาง">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="destination_<?php echo $i; ?>" class="required-field">ปลายทาง</label>
                                                            <input type="text" class="form-control" id="destination_<?php echo $i; ?>" name="destination[<?php echo $i; ?>]"
                                                                value="<?php echo htmlspecialchars($form_data['destination'][$i] ?? ''); ?>" placeholder="ระบุสถานที่ปลายทาง">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="distance_<?php echo $i; ?>" class="required-field">ระยะทาง (กิโลเมตร)</label>
                                                            <input type="number" step="0.1" min="0" class="form-control" id="distance_<?php echo $i; ?>" name="distance[<?php echo $i; ?>]"
                                                                value="<?php echo htmlspecialchars($form_data['distance'][$i] ?? ''); ?>" placeholder="0.0">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="rate_<?php echo $i; ?>" class="required-field">อัตรา (บาท/กม.)</label>
                                                            <input type="number" step="0.01" min="0" class="form-control" id="rate_<?php echo $i; ?>" name="rate[<?php echo $i; ?>]"
                                                                value="<?php echo htmlspecialchars($form_data['rate'][$i] ?? '4.00'); ?>" placeholder="0.00">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>คำนวณค่าเดินทาง</label>
                                                            <button type="submit" name="calculate" class="btn btn-warning btn-block">
                                                                <i class="fas fa-calculator mr-2"></i>คำนวณ
                                                            </button>
                                                            <input type="hidden" name="calculate_item_id" value="<?php echo $i; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="description_<?php echo $i; ?>">รายละเอียด</label>
                                                        <textarea class="form-control" id="description_<?php echo $i; ?>" name="description[<?php echo $i; ?>]" rows="2"
                                                            placeholder="ระบุรายละเอียดค่าใช้จ่าย"><?php echo htmlspecialchars($form_data['description'][$i] ?? ''); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="expense_date_start_<?php echo $i; ?>" class="required-field">วันที่เกิดค่าใช้จ่าย</label>
                                                        <div class="input-group date expense-date-start-picker" data-target-input="nearest">
                                                            <input type="text" class="form-control datetimepicker-input expense-date-start"
                                                                id="expense_date_start_<?php echo $i; ?>" name="expense_date_start[<?php echo $i; ?>]"
                                                                value="<?php echo htmlspecialchars($form_data['expense_date_start'][$i] ?? $today); ?>"
                                                                placeholder="วัน/เดือน/ปี" required>
                                                            <div class="input-group-append" data-toggle="datetimepicker">
                                                                <div class="input-group-text"><i class="far fa-calendar-alt"></i></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="expense_date_end_<?php echo $i; ?>">วันที่สิ้นสุด (ถ้ามี)</label>
                                                        <div class="input-group date expense-date-end-picker" data-target-input="nearest">
                                                            <input type="text" class="form-control datetimepicker-input expense-date-end"
                                                                id="expense_date_end_<?php echo $i; ?>" name="expense_date_end[<?php echo $i; ?>]"
                                                                value="<?php echo htmlspecialchars($form_data['expense_date_end'][$i] ?? ''); ?>"
                                                                placeholder="วัน/เดือน/ปี">
                                                            <div class="input-group-append" data-toggle="datetimepicker">
                                                                <div class="input-group-text"><i class="far fa-calendar-alt"></i></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- อัพโหลดเอกสารแนบ -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="document_<?php echo $i; ?>" class="required-field">เอกสารแนบ</label>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" id="document_<?php echo $i; ?>" name="document[<?php echo $i; ?>][]" multiple>
                                                            <label class="custom-file-label" for="document_<?php echo $i; ?>">เลือกไฟล์...</label>
                                                        </div>
                                                        <small class="form-text text-muted">รองรับไฟล์ .jpg, .png, .pdf ขนาดไม่เกิน 5MB ต่อไฟล์</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($item_count > 1): ?>
                                            <div class="card-footer bg-light">
                                                <div class="text-right">
                                                    <button type="submit" name="remove_item" class="btn btn-danger btn-sm no-print">
                                                        <i class="fas fa-trash mr-2"></i>ลบรายการนี้
                                                        <input type="hidden" name="remove_item_id" value="<?php echo $i; ?>">
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endfor; ?>

                                <div class="text-center mt-3">
                                    <button type="submit" name="add_item" class="btn btn-info no-print">
                                        <i class="fas fa-plus mr-2"></i>เพิ่มรายการค่าใช้จ่าย
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- สรุปยอดรวม -->
                        <div class="total-summary">
                            <div class="row">
                                <div class="col-md-8">
                                    <p class="mb-0">จำนวนรายการทั้งหมด: <span id="total-item-count"><?php echo $item_count; ?></span> รายการ</p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <p class="mb-0">ยอดรวมทั้งสิ้น: <span id="total-amount" class="total-amount"><?php echo number_format($total_amount, 2); ?></span> บาท</p>
                                </div>
                            </div>
                        </div>

                        <!-- ปุ่มบันทึกและยกเลิก -->
                        <div class="text-center mb-4">
                            <button type="submit" name="submit" class="btn btn-primary btn-lg px-5 no-print">
                                <i class="fas fa-save mr-2"></i>บันทึกการเบิก
                            </button>
                            <a href="<?php echo BASE_URL; ?>pages/claims/claims.php" class="btn btn-default btn-lg px-5 ml-2 no-print">
                                <i class="fas fa-times mr-2"></i>ยกเลิก
                            </a>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <?php include '../../include/footer.php'; ?>
    </div>

    <!-- JavaScript Libraries -->
    <script src="<?php echo BASE_URL; ?>assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/plugins/moment/moment-with-locales.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/plugins/select2/js/select2.full.min.js"></script>

    <script>
        $(function() {
            // ตั้งค่า moment.js ภาษาไทย
            moment.locale('th');
            moment.updateLocale('th', {
                months: 'มกราคม_กุมภาพันธ์_มีนาคม_เมษายน_พฤษภาคม_มิถุนายน_กรกฎาคม_สิงหาคม_กันยายน_ตุลาคม_พฤศจิกายน_ธันวาคม'.split('_'),
                monthsShort: 'ม.ค._ก.พ._มี.ค._เม.ย._พ.ค._มิ.ย._ก.ค._ส.ค._ก.ย._ต.ค._พ.ย._ธ.ค.'.split('_'),
                weekdays: 'อาทิตย์_จันทร์_อังคาร_พุธ_พฤหัสบดี_ศุกร์_เสาร์'.split('_'),
                weekdaysShort: 'อา._จ._อ._พ._พฤ._ศ._ส.'.split('_'),
                weekdaysMin: 'อา._จ._อ._พ._พฤ._ศ._ส.'.split('_')
            });

            // ตั้งค่า Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // ตั้งค่า DatePicker
            const datePickerConfig = {
                format: 'DD/MM/YYYY',
                locale: 'th',
                useCurrent: true,
                icons: {
                    time: 'far fa-clock',
                    date: 'far fa-calendar-alt',
                    up: 'fas fa-arrow-up',
                    down: 'fas fa-arrow-down',
                    previous: 'fas fa-chevron-left',
                    next: 'fas fa-chevron-right',
                    today: 'far fa-calendar-check',
                    clear: 'far fa-trash-alt',
                    close: 'fas fa-times'
                },
                buttons: {
                    showToday: true,
                    showClear: true,
                    showClose: true
                }
            };

            $('#expense_date_picker').datetimepicker(datePickerConfig);

            // ตั้งค่า DatePicker สำหรับรายการค่าใช้จ่าย
            $('.expense-date-start-picker').datetimepicker(datePickerConfig);
            $('.expense-date-end-picker').datetimepicker(Object.assign({}, datePickerConfig, {
                useCurrent: false
            }));

            // ตรวจสอบวันที่เริ่มต้นและสิ้นสุด
            $('.expense-date-start-picker').each(function() {
                const $startPicker = $(this);
                const $endPicker = $startPicker.closest('.row').find('.expense-date-end-picker');
                $startPicker.on('change.datetimepicker', function(e) {
                    $endPicker.datetimepicker('minDate', e.date);
                });
                $endPicker.on('change.datetimepicker', function(e) {
                    $startPicker.datetimepicker('maxDate', e.date);
                });
            });

            // แสดงชื่อไฟล์เมื่อเลือก
            $('.custom-file-input').on('change', function() {
                const fileName = Array.from(this.files).map(file => file.name).join(', ');
                $(this).next('.custom-file-label').text(fileName || 'เลือกไฟล์...');
            });
        });

        // ฟังก์ชันแสดง/ซ่อนฟิลด์ค่าเดินทาง
        function showTravelFields(itemId, selectedType) {
            const travelSection = document.getElementById(`travel-expense-${itemId}`);
            travelSection.style.display = (selectedType === 'ค่าเดินทาง' || selectedType === 'ค่าน้ำมัน') ? 'block' : 'none';
        }



        /**
         * ใส่ comma คั่นหลักพัน (รองรับทศนิยม 2 ตำแหน่ง)
         */
        function addCommas(num) {
            if (num === '' || num === null) return '';
            let parts = num.toString().replace(/,/g, '').split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            return parts.length > 1 ? parts[0] + '.' + parts[1].substring(0, 2) : parts[0];
        }

        /**
         * คำนวณยอดรวมแล้วอัปเดตที่ <span id="total-amount">
         */
        function updateTotal() {
            let sum = 0;
            $('.amount-input').each(function() {
                let val = $(this).val().replace(/,/g, '');
                if (val !== '') sum += parseFloat(val);
            });
            $('#total-amount').text(addCommas(sum.toFixed(2)));
        }

        /* -- ฟอร์แมตทันทีที่พิมพ์ -- */
        $(document).on('input', '.amount-input', function() {
            let caretPos = this.selectionStart; // จดจำตำแหน่งเคอร์เซอร์
            let beforeLen = $(this).val().length;

            $(this).val(addCommas($(this).val()));

            /* ปรับตำแหน่งเคอร์เซอร์ไม่ให้กระโดด */
            let afterLen = $(this).val().length;
            this.setSelectionRange(caretPos + (afterLen - beforeLen), caretPos + (afterLen - beforeLen));

            updateTotal();
        });

        /* -- เมื่อเพิ่ม/ลบรายการ ให้คำนวณใหม่ -- */
        updateTotal();

        /* -- ก่อน submit : เอา comma ออกทุกช่อง -- */
        $('#expenseForm').on('submit', function() {
            $('.amount-input').each(function() {
                this.value = this.value.replace(/,/g, '');
            });
        });
    </script>


</body>

</html>