<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';
include '../../config/validation.php';

// ตรวจสอบว่ามีการส่ง id หรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: account.php');
    exit();
}

$encryptedId = urldecode($_GET['id']);
$userId = decryptUserId($encryptedId);

if ($userId === false) {
    $_SESSION['error'] = 'รหัสผู้ใช้ไม่ถูกต้อง';
    header('Location: account.php');
    exit();
}

$role = $_SESSION['role'];
$currentUserId = $_SESSION['user_id'];
$currentTeamId = $_SESSION['team_id'] ?? 'ALL';
$currentTeamIds = $_SESSION['team_ids'] ?? [];

try {
    $stmt = $condb->prepare('
        SELECT u.user_id,
               u.username,
               u.first_name,
               u.last_name,
               u.email,
               u.phone,
               u.position,
               u.role,
               u.company,
               u.created_at,
               u.profile_image,
               u.created_by,
               creator.first_name AS creator_first_name,
               creator.last_name AS creator_last_name
        FROM users u
        LEFT JOIN users creator ON u.created_by = creator.user_id
        WHERE u.user_id = :user_id
    ');
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
    $stmt->execute();
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$account) {
        $_SESSION['error'] = 'ไม่พบข้อมูลผู้ใช้';
        header('Location: account.php');
        exit();
    }

    // ตรวจสอบสิทธิ์การเข้าถึง (เหมือนหน้า edit)
    if ($role === 'Sale Supervisor') {
        if ($account['user_id'] === $currentUserId) {
            // สามารถดูของตัวเองได้
        } else {
            if ($account['role'] === 'Executive') {
                $errorMessage = 'คุณไม่มีสิทธิ์ดูข้อมูลของ Executive';
            } elseif ($account['role'] === 'Sale Supervisor' && $account['user_id'] !== $currentUserId) {
                $errorMessage = 'คุณไม่มีสิทธิ์ดูข้อมูลของ Sale Supervisor ท่านอื่น';
            } else {
                // ตรวจสอบทีมว่ามีทีมที่ตรงกันหรือไม่
                $teamStmt = $condb->prepare('
                    SELECT COUNT(*)
                    FROM user_teams ut
                    WHERE ut.user_id = :target_user_id
                      AND ut.team_id IN (
                          SELECT team_id FROM user_teams WHERE user_id = :current_user_id
                      )
                ');
                $teamStmt->execute([
                    ':target_user_id'   => $userId,
                    ':current_user_id' => $currentUserId,
                ]);

                if ($teamStmt->fetchColumn() == 0) {
                    $errorMessage = 'คุณไม่มีสิทธิ์ดูข้อมูลของผู้ใช้นอกทีม';
                }
            }
        }
    } elseif ($role !== 'Executive') {
        $errorMessage = 'คุณไม่มีสิทธิ์ในการดูข้อมูลนี้';
    }

    if (isset($errorMessage)) {
        $_SESSION['error'] = $errorMessage;
        header('Location: account.php');
        exit();
    }

    // ดึงรายชื่อทีมที่ผู้ใช้นี้สังกัด
    $teamStmt = $condb->prepare('
        SELECT t.team_id, t.team_name, ut.is_primary
        FROM user_teams ut
        JOIN teams t ON ut.team_id = t.team_id
        WHERE ut.user_id = :user_id
        ORDER BY ut.is_primary DESC, t.team_name ASC
    ');
    $teamStmt->execute([':user_id' => $userId]);
    $userTeams = $teamStmt->fetchAll(PDO::FETCH_ASSOC);

    // กำหนดสิทธิ์การแก้ไขสำหรับการแสดงปุ่ม
    $canEditAccount = false;
    if ($role === 'Executive') {
        if ($account['username'] !== 'Admin') {
            $canEditAccount = true;
        }
    } elseif ($role === 'Sale Supervisor') {
        if ($account['username'] !== 'Admin'
            && $account['role'] !== 'Executive'
            && !($account['role'] === 'Sale Supervisor' && $account['user_id'] !== $currentUserId)
        ) {
            $canEditAccount = true;
        }
    }
} catch (PDOException $e) {
    error_log('view_account.php error: ' . $e->getMessage());
    $_SESSION['error'] = 'เกิดข้อผิดพลาดในการดึงข้อมูล';
    header('Location: account.php');
    exit();
}

$profileImage = !empty($account['profile_image'])
    ? BASE_URL . 'uploads/profile_images/' . $account['profile_image']
    : BASE_URL . 'assets/img/add.jpg';

?>
<!DOCTYPE html>
<html lang="th">
<?php $menu = "account"; ?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | ดูรายละเอียดผู้ใช้</title>
    <?php include '../../include/header.php'; ?>
    <style>
        .profile-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #3c8dbc;
        }

        .badge-role {
            font-size: 0.85rem;
        }
    </style>
</head>
<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <?php include '../../include/navbar.php'; ?>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">รายละเอียดบัญชีผู้ใช้</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="account.php">Account</a></li>
                            <li class="breadcrumb-item active">View Account</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-primary card-outline h-100">
                            <div class="card-body box-profile text-center">
                                <img class="profile-image" src="<?php echo $profileImage; ?>" alt="Profile Image">
                                <h3 class="profile-username text-center mt-3">
                                    <?php echo escapeOutput($account['first_name'] . ' ' . $account['last_name']); ?>
                                </h3>
                                <p class="text-muted text-center mb-1">
                                    <span class="badge badge-info badge-role">
                                        <?php echo escapeOutput($account['role']); ?>
                                    </span>
                                </p>
                                <?php if (!empty($account['position'])): ?>
                                    <p class="text-muted text-center mb-0"><?php echo escapeOutput($account['position']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($account['company'])): ?>
                                    <small class="text-muted">บริษัท: <?php echo escapeOutput($account['company']); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-users mr-2"></i>ทีมที่สังกัด</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($userTeams)): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($userTeams as $team): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><?php echo escapeOutput($team['team_name']); ?></span>
                                                <?php if ((int)$team['is_primary'] === 1): ?>
                                                    <span class="badge badge-success">ทีมหลัก</span>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted mb-0">- ไม่มีข้อมูลทีม -</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card card-primary card-outline h-100">
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <h6 class="mb-3"><i class="fas fa-id-card text-primary mr-2"></i>ข้อมูลทั่วไป</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">ชื่อผู้ใช้งาน</small>
                                                <span><?php echo escapeOutput($account['username']); ?></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">ชื่อ-สกุล</small>
                                                <span><?php echo escapeOutput($account['first_name'] . ' ' . $account['last_name']); ?></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">อีเมล</small>
                                                <span><?php echo escapeOutput($account['email']); ?></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">เบอร์โทรศัพท์</small>
                                                <span><?php echo escapeOutput($account['phone'] ?: '-'); ?></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">ตำแหน่ง</small>
                                                <span><?php echo escapeOutput($account['position'] ?: '-'); ?></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">บริษัท</small>
                                                <span><?php echo escapeOutput($account['company'] ?: '-'); ?></span>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="list-group-item">
                                        <h6 class="mb-3"><i class="fas fa-clock text-success mr-2"></i>ข้อมูลการสร้าง</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">วันที่สร้าง</small>
                                                <span><?php echo escapeOutput(date('d/m/Y H:i', strtotime($account['created_at']))); ?></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">สร้างโดย</small>
                                                <span>
                                                    <?php
                                                    if (!empty($account['creator_first_name'])) {
                                                        echo escapeOutput($account['creator_first_name'] . ' ' . $account['creator_last_name']);
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-footer text-right">
                                <a href="account.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> กลับ</a>
                                <?php if ($canEditAccount): ?>
                                    <a href="edit_account.php?user_id=<?php echo urlencode($encryptedId); ?>" class="btn btn-primary">
                                        <i class="fas fa-edit mr-1"></i> แก้ไขข้อมูล
                                    </a>
                                <?php endif; ?>
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
