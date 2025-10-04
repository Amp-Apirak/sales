<!-- Load Custom CSS -->
<link rel="stylesheet" href="../../assets/css/project_member.css">

<!-- Modal เพิ่มสมาชิก -->
<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus"></i>
                    เพิ่มสมาชิกใหม่
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addMemberForm">
                <div class="modal-body">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

                    <div class="form-group">
                        <label for="user_id">
                            <i class="fas fa-user"></i>
                            เลือกผู้ใช้
                        </label>
                        <select class="form-control select2" name="user_id" required>
                            <option value="">-- กรุณาเลือกผู้ใช้ --</option>
                            <?php foreach ($available_users as $user): ?>
                                <option value="<?php echo $user['user_id']; ?>">
                                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> เลือกผู้ใช้ที่ต้องการเพิ่มเข้าโครงการ
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="role_id">
                            <i class="fas fa-user-tag"></i>
                            บทบาทในโครงการ
                        </label>
                        <select class="form-control select2-role" name="role_id" required>
                            <option value="">-- กรุณาเลือกบทบาท --</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['role_id']; ?>">
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> กำหนดบทบาทของสมาชิกในโครงการ
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="is_active">
                            <i class="fas fa-shield-alt"></i>
                            ระดับสิทธิ์การเข้าถึง
                        </label>
                        <select class="form-control" name="is_active" id="is_active" required>
                            <option value="0">Full Access - เข้าถึงและจัดการได้ทั้งหมด</option>
                            <option value="2">Half Access - เข้าถึงได้บางส่วน</option>
                            <option value="1">View Only - ดูข้อมูลอย่างเดียว</option>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> กำหนดระดับสิทธิ์ในการเข้าถึงข้อมูลโครงการ
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal แก้ไขสมาชิก -->
<div class="modal fade" id="editMemberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit"></i>
                    แก้ไขข้อมูลสมาชิก
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editMemberForm">
                <div class="modal-body">
                    <input type="hidden" name="member_id" id="edit_member_id">

                    <div class="form-group">
                        <label for="edit_role_id">
                            <i class="fas fa-user-tag"></i>
                            บทบาทในโครงการ
                        </label>
                        <select class="form-control" name="role_id" id="edit_role_id" required>
                            <option value="">-- กรุณาเลือกบทบาท --</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['role_id']; ?>">
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> กำหนดบทบาทของสมาชิกในโครงการ
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="edit_is_active">
                            <i class="fas fa-shield-alt"></i>
                            ระดับสิทธิ์การเข้าถึง
                        </label>
                        <select class="form-control" name="is_active" id="edit_is_active" required>
                            <option value="0">Full Access - เข้าถึงและจัดการได้ทั้งหมด</option>
                            <option value="2">Half Access - เข้าถึงได้บางส่วน</option>
                            <option value="1">View Only - ดูข้อมูลอย่างเดียว</option>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> กำหนดระดับสิทธิ์ในการเข้าถึงข้อมูลโครงการ
                        </small>
                    </div>

                    <div class="alert alert-info" style="border-radius: 8px; border-left: 4px solid #667eea;">
                        <i class="fas fa-info-circle"></i>
                        <strong>คำอธิบายระดับสิทธิ์:</strong>
                        <ul class="mb-0 mt-2" style="padding-left: 1.5rem;">
                            <li><strong>Full Access:</strong> สามารถดู แก้ไข และจัดการข้อมูลได้ทั้งหมด</li>
                            <li><strong>Half Access:</strong> สามารถดูข้อมูลและแก้ไขบางส่วนได้</li>
                            <li><strong>View Only:</strong> สามารถดูข้อมูลพื้นฐานเท่านั้น ไม่สามารถแก้ไขได้</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS for Dropdown Select2 -->
<script>
    $(function() {
        // Initialize Select2 for user selection
        $('.select2').select2({
            width: '100%',
            theme: 'bootstrap4',
            dropdownParent: $('#addMemberModal'),
            allowClear: true,
            placeholder: '-- กรุณาเลือกผู้ใช้ --',
            language: {
                noResults: function() {
                    return "ไม่พบข้อมูล";
                },
                searching: function() {
                    return "กำลังค้นหา...";
                }
            }
        });

        // Initialize Select2 for role selection
        $('.select2-role').select2({
            width: '100%',
            theme: 'bootstrap4',
            dropdownParent: $('#addMemberModal'),
            allowClear: true,
            placeholder: '-- กรุณาเลือกบทบาท --',
            language: {
                noResults: function() {
                    return "ไม่พบข้อมูล";
                }
            }
        });

        // Reset select2 when modal closes
        $('#addMemberModal').on('hidden.bs.modal', function() {
            $('.select2').val(null).trigger('change');
            $('.select2-role').val(null).trigger('change');
            $('#is_active').val('0'); // Reset to Full Access
            $('#addMemberForm')[0].reset();
        });

        // Reset edit modal when closed
        $('#editMemberModal').on('hidden.bs.modal', function() {
            $('#editMemberForm')[0].reset();
        });
    });
</script>

<script>
    $(function() {
        // กำหนดค่าเริ่มต้นสำหรับ DataTable
        $("#membersTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#membersTable_wrapper .col-md-6:eq(0)');


        // จัดการการส่งฟอร์มเพิ่มสมาชิก
        $('#addMemberForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'project_member/add_member.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ',
                            text: 'เพิ่มสมาชิกเรียบร้อยแล้ว',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: response.message
                        });
                    }
                }
            });
        });

        // จัดการการส่งฟอร์มแก้ไขสมาชิก
        $('#editMemberForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'project_member/edit_member.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ',
                            text: 'แก้ไขข้อมูลสมาชิกเรียบร้อยแล้ว',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: response.message
                        });
                    }
                }
            });
        });
    });

    // ฟังก์ชันสำหรับเปิด modal แก้ไขสมาชิก
    function editMember(memberId, roleId, isActive) {
        $('#edit_member_id').val(memberId);
        $('#edit_role_id').val(roleId);
        $('#edit_is_active').val(isActive ? '1' : '0');
        $('#editMemberModal').modal('show');
    }

    // ฟังก์ชันยืนยันการลบสมาชิก
    function confirmDelete(memberId, memberName) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: `คุณต้องการลบ ${memberName} ออกจากโครงการใช่หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'project_member/delete_member.php',
                    type: 'POST',
                    data: {
                        member_id: memberId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: 'ลบสมาชิกเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: response.message
                            });
                        }
                    }
                });
            }
        });
    }
    
</script>