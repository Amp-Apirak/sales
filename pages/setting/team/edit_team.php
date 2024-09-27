<div class="modal fade" id="editbtn">
    <div class="modal-dialog editbtn">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Team</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editTeamForm" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="team_id" value="">
                    <div class="form-group">
                        <label for="team_name">Team Name<span class="text-danger">*</span></label>
                        <input type="text" name="team_name" class="form-control" id="team_name" required>
                    </div>
                    <div class="form-group">
                        <label for="team_description">Description</label>
                        <textarea class="form-control" name="team_description" id="team_description" rows="4"></textarea>
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
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Update Team</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Team -->
<script>
    $(document).ready(function() {
        // เมื่อหน้าเพจโหลดเสร็จสมบูรณ์แล้ว
        $('.edit-team').on('click', function(e) {
            e.preventDefault(); // ป้องกันไม่ให้ลิงก์ถูกเปิดตามปกติเมื่อคลิก

            var teamId = $(this).data('id'); // ดึงค่า team_id จาก data attribute ของปุ่มที่กด

            // เริ่มต้นทำการ AJAX call เพื่อดึงข้อมูลทีมตาม team_id
            $.ajax({
                url: 'http://localhost/sales/api/setting/team/team_api.php?team_id=' + teamId, // สร้าง URL สำหรับส่งค่า team_id ไปที่ API
                type: 'GET', // ใช้ GET method เพื่อขอดึงข้อมูล
                success: function(response) { // เมื่อดึงข้อมูลสำเร็จ
                    if (response.team) { // ตรวจสอบว่ามีข้อมูลทีมที่ถูกดึงมาหรือไม่
                        // กรอกข้อมูลที่ได้จาก API ลงในฟอร์มที่อยู่ใน Modal
                        $('#editbtn input[name="team_id"]').val(response.team.team_id); // กำหนดค่า team_id
                        $('#editbtn #team_name').val(response.team.team_name); // กำหนดค่า team_name
                        $('#editbtn #team_description').val(response.team.team_description); // กำหนดค่า team_description
                        $('#editbtn #team_leader').val(response.team.team_leader).trigger('change'); // กำหนดค่า team_leader และใช้ trigger('change') เพื่อให้ Select2 หรือ Dropdown อัพเดต

                        // เปิด Modal สำหรับการแก้ไขข้อมูลทีม
                        $('#editbtn').modal('show');
                    } else {
                        // หากไม่พบข้อมูลทีม แสดงข้อความแจ้งเตือน
                        Swal.fire('Error', 'ไม่พบข้อมูลทีม', 'error');
                    }
                },
                error: function() {
                    // กรณีเกิดข้อผิดพลาดในการดึงข้อมูลจาก API
                    Swal.fire('Error', 'ไม่สามารถโหลดข้อมูลได้', 'error');
                }
            });
        });

        // ฟังก์ชันเข้ารหัส Base64
        function encodeId(id) {
            return btoa(id); // Base64 encoding
        }

        // เมื่อผู้ใช้ทำการส่งฟอร์มแก้ไขข้อมูลทีม
        $('#editTeamForm').on('submit', function(e) {
            e.preventDefault(); // ป้องกันการ reload หน้า
            var teamId = $('#editbtn input[name="team_id"]').val(); // ดึง team_id จาก hidden input
            var encryptedUserId = encodeId(teamId); // เข้ารหัส team_id ก่อนส่ง

            // สร้าง data object ที่จะส่งไปยัง API
            var formData = {
                team_name: $('#team_name').val(),
                team_description: $('#team_description').val(),
                team_leader: $('#team_leader').val(),
                csrf_token: $('input[name="csrf_token"]').val() // ดึงค่า CSRF token เพื่อป้องกัน CSRF
            };

            // ส่ง PUT request ไปที่ API เพื่อแก้ไขข้อมูล
            $.ajax({
                url: 'http://localhost/sales/api/setting/team/team_api.php?team_id=' + encryptedUserId, // ส่งไปที่ API พร้อม team_id
                type: 'PUT', // ใช้ PUT method เพื่ออัปเดตข้อมูล
                contentType: 'application/json', // กำหนดว่าเราส่งข้อมูลเป็น JSON
                data: JSON.stringify(formData), // แปลงข้อมูลในรูปแบบ JSON เพื่อส่งไปยัง API
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success', 'ข้อมูลทีมถูกแก้ไขเรียบร้อยแล้ว', 'success').then(function() {
                            location.reload(); // หลังจากอัปเดตสำเร็จ รีเฟรชหน้าเพื่อแสดงข้อมูลใหม่
                        });
                    } else {
                        Swal.fire('Error', response.error || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                }
            });
        });
    });
</script>