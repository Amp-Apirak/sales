<?php
//session_start and Config DB
include  '../../include/Add_session.php';


// ตรวจสอบว่ามีการส่ง user_id มาหรือไม่
if (isset($_GET['user_id'])) {
    // ถอดรหัส user_id ที่ได้รับจาก URL
    $encrypted_user_id = urldecode($_GET['user_id']);
    $user_id = decryptUserId($encrypted_user_id);

    // ตรวจสอบว่าถอดรหัสสำเร็จหรือไม่
    if ($user_id !== false) {
        // ดึงข้อมูลผู้ใช้จากฐานข้อมูลโดยใช้ user_id
        $sql = "SELECT * FROM users WHERE user_id = :user_id";
        $stmt = $condb->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();

        // ตรวจสอบว่าพบข้อมูลผู้ใช้หรือไม่
        if (!$user) {
            echo "ไม่พบผู้ใช้ที่ต้องการแก้ไข";
            exit;
        }
    } else {
        echo "รหัสผู้ใช้ไม่ถูกต้อง";
        exit;
    }
} else {
    echo "ไม่มีการส่งรหัสผู้ใช้มา";
    exit;
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$sql = "SELECT * FROM users WHERE user_id = :user_id";
$stmt = $condb->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    echo "ไม่พบผู้ใช้งานที่ระบุ";
    exit;
}

// ตรวจสอบว่าผู้ใช้กดปุ่ม "อัปเดตข้อมูล" หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8');
    $last_name = htmlspecialchars($_POST['last_name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $position = htmlspecialchars($_POST['position'], ENT_QUOTES, 'UTF-8');
    $role = htmlspecialchars($_POST['role'], ENT_QUOTES, 'UTF-8');
    $company = htmlspecialchars($_POST['company'], ENT_QUOTES, 'UTF-8');
    $team_id = $_POST['team_id'];

    // อัปเดตข้อมูลผู้ใช้ในฐานข้อมูล
    $sql_update = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, 
                   position = :position, role = :role, company = :company, team_id = :team_id WHERE user_id = :user_id";
    $stmt_update = $condb->prepare($sql_update);
    $stmt_update->bindParam(':first_name', $first_name);
    $stmt_update->bindParam(':last_name', $last_name);
    $stmt_update->bindParam(':email', $email);
    $stmt_update->bindParam(':phone', $phone);
    $stmt_update->bindParam(':position', $position);
    $stmt_update->bindParam(':role', $role);
    $stmt_update->bindParam(':company', $company);
    $stmt_update->bindParam(':team_id', $team_id, PDO::PARAM_INT);
    $stmt_update->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt_update->execute()) {
        echo "<script>alert('อัปเดตข้อมูลสำเร็จ!'); window.location.href = 'account.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account</title>
    <?php include '../../include/header.php'; ?>
</head>

<body>
    <?php include '../../include/navbar.php'; ?>
    <div class="container">
        <h2>Edit Account</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username (ไม่สามารถแก้ไขได้)</label>
                <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            <div class="form-group">
                <label for="position">Position</label>
                <input type="text" class="form-control" id="position" name="position" value="<?php echo htmlspecialchars($user['position']); ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="Executive" <?php if ($user['role'] == 'Executive') echo 'selected'; ?>>Executive</option>
                    <option value="Sale Supervisor" <?php if ($user['role'] == 'Sale Supervisor') echo 'selected'; ?>>Sale Supervisor</option>
                    <option value="Seller" <?php if ($user['role'] == 'Seller') echo 'selected'; ?>>Seller</option>
                    <option value="Engineer" <?php if ($user['role'] == 'Engineer') echo 'selected'; ?>>Engineer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="company">Company</label>
                <input type="text" class="form-control" id="company" name="company" value="<?php echo htmlspecialchars($user['company']); ?>" required>
            </div>
            <div class="form-group">
                <label for="team_id">Team</label>
                <select class="form-control" id="team_id" name="team_id" required>
                    <?php
                    $sql_teams = "SELECT * FROM teams";
                    $query_teams = $condb->query($sql_teams);
                    while ($team = $query_teams->fetch()) {
                        $selected = $user['team_id'] == $team['team_id'] ? 'selected' : '';
                        echo "<option value='" . $team['team_id'] . "' $selected>" . $team['team_name'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
    <?php include '../../include/footer.php'; ?>
</body>

</html>