<?php

// ตรวจสอบว่าแบบฟอร์มถูกส่งมาและเป็น POST request หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // รับข้อมูลจากฟอร์ม
    $team_id = urldecode($_POST['team_id']);
    $team_name = htmlspecialchars(trim($_POST['team_name']), ENT_QUOTES);
    $team_description = htmlspecialchars(trim($_POST['team_description']), ENT_QUOTES);
    $team_leader = htmlspecialchars(trim($_POST['team_leader']), ENT_QUOTES);

    // เตรียมข้อมูลที่ต้องการส่งไปยัง API
    $data = [
        'team_id' => $team_id,
        'team_name' => $team_name,
        'team_description' => $team_description,
        'team_leader' => $team_leader
    ];

    // URL ของ API สำหรับแก้ไขข้อมูลทีม
    $api_url = 'http://localhost/sales/api/setting/team/team_api.php';

    // ใช้ cURL เพื่อส่ง PUT request ไปยัง API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // ส่งข้อมูลในรูปแบบ JSON
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-CSRF-Token: ' . $_POST['csrf_token'] // ส่ง CSRF token ไปด้วย
    ]);

    // ดำเนินการ cURL และรับผลลัพธ์
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // ตรวจสอบผลลัพธ์จาก API
    if ($http_code === 200) {
        // สำเร็จ
        echo "Team updated successfully.";
    } else {
        // มีปัญหาเกิดขึ้น
        echo "Error: " . $response;
    }
}
?>


<!-- Modal Edit Team -->
<div class="modal fade" id="editbtn<?php echo htmlspecialchars($team['team_id']); ?>">
    <div class="modal-dialog editbtn">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Team</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editTeamForm" method="POST" action="edit_team.php">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="team_id" value="<?php echo urlencode(encryptUserId($team['team_id'])); ?>">
                    <div class="form-group">
                        <label for="team_name">Team Name<span class="text-danger">*</span></label>
                        <input type="text" name="team_name" class="form-control" value="<?php echo $team['team_name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="team_description">Description</label>
                        <textarea class="form-control" name="team_description" rows="4"><?php echo $team['team_description']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="team_leader">Lead Team<span class="text-danger">*</span></label>
                        <select class="form-control select2" id="team_leader" name="team_leader" >
                            <option value="<?php echo $team['team_leader']; ?>"><?php echo $team['team_leader_name']; ?></option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                    <?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Update Team</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>