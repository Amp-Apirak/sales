<?php

// สร้าง CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


// ดึงข้อมูลจากตาราง users สำหรับแสดงในดรอปดาวน์ team_leader
try {
    $sql = "SELECT user_id, first_name, last_name FROM users ORDER BY first_name, last_name";
    $stmt = $condb->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching users: " . $e->getMessage());
    $error_message = "เกิดข้อผิดพลาดในการดึงข้อมูลผู้ใช้";
}

?>

<div class="modal fade" id="editbtn">
    <div class="modal-dialog editbtn">
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

<script>
    $(document).ready(function() {
        // เริ่มต้น Select2
        $('.select2').select2();

        // จัดการการส่งฟอร์ม
        $('#addTeamForm').on('submit', function(e) {
            e.preventDefault();

            // เตรียมข้อมูลที่จะส่ง
            var formData = {
                team_name: $('#team_name').val().trim(),
                team_description: $('#team_description').val().trim(),
                team_leader: $('#team_leader').val(),
                csrf_token: $('input[name="csrf_token"]').val()
            };

            // ตรวจสอบข้อมูลก่อนส่ง
            if (!formData.team_name || !formData.team_leader) {
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อมูลไม่ครบถ้วน',
                    text: 'กรุณากรอกชื่อทีมและเลือกหัวหน้าทีม'
                });
                return;
            }

            // ส่งข้อมูลไปยัง API
            $.ajax({
                url: 'http://localhost/sales/api/setting/team/team_api.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', formData.csrf_token);
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสำเร็จ',
                            text: response.message
                        }).then(() => {
                            window.location.href = 'team.php'; // ไปยังหน้าแสดงรายการทีม
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: response.error || 'ไม่สามารถบันทึกข้อมูลได้'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
                    });
                }
            });
        });
    });
</script>