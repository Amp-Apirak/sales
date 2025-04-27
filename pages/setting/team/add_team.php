<?php


// จำกัดการเข้าถึงเฉพาะผู้ใช้ที่มีสิทธิ์เท่านั้น
if (!in_array($role, ['Executive'])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่อนุญาต',
                    text: 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้',
                    confirmButtonText: 'ตกลง'
                }).then(function() {
                    window.location.href = 'team.php'; // กลับไปยังหน้า team.php
                });
            }, 100);
          </script>";
    exit();
}

// ตรวจสอบการตั้งค่า Session เพื่อป้องกันกรณีที่ไม่ได้ล็อกอิน
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    // กรณีไม่มีการตั้งค่า Session หรือล็อกอิน
    header("Location: " . BASE_URL . "login.php");  // Redirect ไปยังหน้า login.php
    exit; // หยุดการทำงานของสคริปต์ปัจจุบันหลังจาก redirect
}


// สร้างหรือดึง CSRF Token สำหรับป้องกันการโจมตี CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ฟังก์ชันทำความสะอาดข้อมูล input
function clean_input($data)
{
    // ทำความสะอาดข้อมูลแต่ยังคงเก็บอักขระพิเศษไว้
    $data = trim($data);
    // ป้องกัน SQL Injection โดยใช้ PDO parameters แทน
    return $data;
}

// ฟังก์ชันสำหรับสร้าง UUID แบบปลอดภัย
function generateUUID()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // เวอร์ชัน 4 UUID
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // UUID variant
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$created_by = $_SESSION['user_id']; // ดึง user_id ของผู้สร้างจาก session


// ดึงข้อมูล users สำหรับเลือกหัวหน้าทีม
$sql_users = "SELECT user_id, first_name, last_name FROM users";
$stmt_users = $condb->prepare($sql_users);
$stmt_users->execute();
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// ตรวจสอบว่าผู้ใช้กดปุ่ม "เพิ่มลูกค้า" หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบ CSRF Token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }
    // สร้าง UUID สำหรับ project_id
    $team_id = generateUUID();

    // รับข้อมูลจากฟอร์มและล้างข้อมูลด้วย htmlspecialchars เพื่อป้องกัน XSS
    $team_name = clean_input($_POST['team_name']);
    $team_description = clean_input($_POST['team_description']);
    $team_leader = clean_input($_POST['team_leader']);

    // ตรวจสอบว่ามีชื่อบริษัทหรืออีเมลที่ซ้ำหรือไม่
    $checkteam_sql = "SELECT * FROM teams WHERE team_name = :team_name ";
    $stmt = $condb->prepare($checkteam_sql);
    $stmt->bindParam(':team_name', $team_name, PDO::PARAM_STR);
    $stmt->execute();
    $existing_team = $stmt->fetch();

    if ($existing_team) {
        // ถ้าพบชื่อบริษัทหรืออีเมลซ้ำ
        echo "<script>
        setTimeout(function() {
            Swal.fire({
                title: 'เกิดข้อผิดพลาด',
                text: 'ชื่อทีมนี้ถูกใช้ไปแล้ว!',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        }, 100);
        </script>";
    } else {
        // ถ้าไม่พบชื่อบริษัทหรืออีเมลซ้ำ
        try {
            $sql = "INSERT INTO teams (team_id, team_name, team_description, team_leader, created_by) VALUES (:team_id, :team_name, :team_description, :team_leader, :created_by)";
            $stmt = $condb->prepare($sql);
            $stmt->bindParam(':team_id', $team_id, PDO::PARAM_STR);
            $stmt->bindParam(':team_name', $team_name, PDO::PARAM_STR);
            $stmt->bindParam(':team_description', $team_description, PDO::PARAM_STR);
            $stmt->bindParam(':team_leader', $team_leader, PDO::PARAM_INT);
            $stmt->bindParam(':created_by', $created_by, PDO::PARAM_INT);
            $stmt->execute();

            // แสดงข้อความเมื่อเพิ่มทีมสำเร็จด้วย SweetAlert
            echo "<script>
        setTimeout(function() {
            Swal.fire({
                title: 'เพิ่มทีมสำเร็จ',
                text: 'เพิ่มทีมสำเร็จแล้ว',
                icon: 'success',
                confirmButtonText: 'ตกลง'
            }).then(function() {
                window.location.href = 'team.php';
            });
        }, 100);
        </script>";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

?>

<div class="modal fade" id="addbtn">
    <div class="modal-dialog addbtn">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Team</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- ฟอร์มเพิ่มข้อมูลทีม -->
                <form id="addTeamForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="team_name">Team Name<span class="text-danger">*</span></label>
                            <input type="text" name="team_name" class="form-control" id="team_name" placeholder="Team Name" required>
                        </div>

                        <div class="form-group">
                            <label for="team_description">Description</label>
                            <textarea class="form-control" name="team_description" id="team_description" rows="4" placeholder="Description"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="team_leader">Lead Team<span class="text-danger">*</span></label>
                            <select class="form-control select2" id="team_leader" name="team_leader" required>
                                <option value="">เลือกหัวหน้าทีม</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                        <?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>