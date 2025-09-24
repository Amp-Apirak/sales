<?php
//session_start and Config DB
include  '../../include/Add_session.php';


$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$user_id = $_SESSION['user_id'];  // ดึง user_id ของผู้ใช้จาก session
$first_name = $_SESSION['first_name']; // ดึง first_name ของผู้ใช้จาก session
$lastname = $_SESSION['last_name']; // ดึง last_name ของผู้ใช้จาก session
$profile_image = $_SESSION['profile_image']; // ดึง profile_image ของผู้ใช้จาก session


// ตรวจสอบว่ามี user_id ใน session หรือไม่
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $stmt = $condb->prepare("
        SELECT u.*
        FROM users u
        WHERE u.user_id = :user_id
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ดึงข้อมูลทีมทั้งหมดที่ผู้ใช้สังกัด
    $teams_stmt = $condb->prepare("
        SELECT t.team_name
        FROM teams t
        INNER JOIN user_teams ut ON t.team_id = ut.team_id
        WHERE ut.user_id = :user_id
        ORDER BY t.team_name
    ");
    $teams_stmt->bindParam(':user_id', $user_id);
    $teams_stmt->execute();
    $team_names = $teams_stmt->fetchAll(PDO::FETCH_COLUMN, 0);





    if ($user) {
        // ตั้งค่าตัวแปรสำหรับใช้ใน HTML (กำหนดค่าเริ่มต้นเพื่อป้องกัน error)
        $fullName = ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');
        $email = $user['email'] ?? 'N/A';
        $position = $user['position'] ?? 'N/A';
        $phone = $user['phone'] ?? 'N/A';
        // ใช้ implode เพื่อแสดงทุกทีม หรือ 'N/A' หากไม่ได้สังกัดทีมใด
        // แก้ไข: บังคับสร้าง team_display ใหม่ให้แน่ใจ
        if (!empty($team_names) && is_array($team_names)) {
            $team_display = implode(', ', $team_names);
        } else {
            $team_display = 'N/A';
        }


        $company = $user['company'] ?? 'N/A';
        $user_role = $user['role'] ?? 'N/A'; // บทบาทของผู้ใช้ (เปลี่ยนชื่อตัวแปรเพื่อไม่ให้ซ้ำกับ $role จาก session)
    } else {
        // กำหนดค่าเริ่มต้นเมื่อไม่พบผู้ใช้
        $fullName = 'N/A';
        $email = 'N/A';
        $position = 'N/A';
        $phone = 'N/A';
        $team_display = 'N/A';
        $company = 'N/A';
        $user_role = 'N/A';
        echo "ไม่พบข้อมูลผู้ใช้";
    }
} else {
    // กำหนดค่าเริ่มต้นเมื่อผู้ใช้ไม่ได้ล็อกอิน
    $fullName = 'N/A';
    $email = 'N/A';
    $position = 'N/A';
    $phone = 'N/A';
    $team_display = 'N/A';
    $company = 'N/A';
    $user_role = 'N/A';
    $user = null;
    echo "ผู้ใช้ไม่ได้ล็อกอิน";
}

?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "profile"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Profile Setting</title>
    <?php include  '../../include/header.php'; ?>
    <style>
        .profile-card {
            max-width: 400px;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-header {
            background: #3498db;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid white;
            margin: 0 auto 10px;
            display: block;
            object-fit: cover;
        }

        .profile-name {
            font-size: 1.5em;
            margin: 0;
        }

        .profile-role {
            font-size: 1em;
            opacity: 0.8;
            margin: 5px 0 0;
        }

        .profile-info {
            padding: 20px;
        }

        .info-item {
            margin-bottom: 10px;
            font-size: 0.9em;
        }

        .info-label {
            font-weight: bold;
            color: #555;
            width: 80px;
            display: inline-block;
        }

        .profile-actions {
            padding: 0 20px 20px;
            text-align: center;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s;
        }

        .btn-edit {
            background-color: #2ecc71;
            color: white;
            margin-right: 10px;
        }

        .btn-password {
            background-color: #e74c3c;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include  '../../include/navbar.php'; ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Profile Setting</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Profile Setting</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="profile-card">
                                <div class="profile-header">
                                    <?php
                                    // ตรวจสอบว่ามี profile image หรือไม่ หากไม่มีให้ใช้ภาพเริ่มต้น
                                    $profile_image = !empty($_SESSION['profile_image']) ? BASE_URL . 'uploads/profile_images/' . htmlspecialchars($_SESSION['profile_image']) : BASE_URL . 'assets/img/add.jpg';
                                    ?>
                                    <img src="<?php echo $profile_image; ?>" alt="User Image" class="profile-img">
                                    <h2 class="profile-name"><?php echo htmlspecialchars($fullName); ?></h2>
                                    <p class="profile-role"><?php echo htmlspecialchars($user_role); ?></p>
                                </div>
                                <div class="profile-info">
                                    <div class="info-item">
                                        <span class="info-label">Email:</span>
                                        <span><?php echo htmlspecialchars($email); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Position:</span>
                                        <span><?php echo htmlspecialchars($position); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Phone:</span>
                                        <span><?php echo htmlspecialchars($phone); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Team:</span>
                                        <span><?php echo htmlspecialchars(implode(', ', $team_names)); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Company:</span>
                                        <span><?php echo htmlspecialchars(is_array($company) ? implode(', ', $company) : $company); ?></span>
                                    </div>
                                </div>
                                <div class="profile-actions">
                                    <?php if ($user): ?>
                                    <a href="<?php echo BASE_URL; ?>/pages/account/edit_account.php?user_id=<?php echo urlencode(encryptUserId($user['user_id'])); ?>" class="btn btn-edit">Edit Information</a>
                                    <a href="recover.php?id=<?php echo urlencode(encryptUserId($user['user_id'])); ?>" class="btn btn-password">Change Password</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include  '../../include/footer.php'; ?>
    </div>
</body>

</html>