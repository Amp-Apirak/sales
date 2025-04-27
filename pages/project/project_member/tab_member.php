
<!-- Modal เพิ่มสมาชิก -->
<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มสมาชิกใหม่</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addMemberForm">
                <div class="modal-body">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                    <div class="form-group">
                        <label for="user_id">เลือกผู้ใช้</label>
                        <select class="form-control select2" name="user_id" required>
                            <option value="">เลือกผู้ใช้</option>
                            <?php foreach ($available_users as $user): ?>
                                <option value="<?php echo $user['user_id']; ?>">
                                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="role_id">บทบาท</label>
                        <select class="form-control select2" name="role_id" required>
                            <option value="">เลือกบทบาท</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['role_id']; ?>">
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal แก้ไขสมาชิก -->
<div class="modal fade" id="editMemberModal" tabindex="-1" role="dialog" aria-labelledby="editMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMemberModalLabel">แก้ไขข้อมูลสมาชิก</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editMemberForm">
                <div class="modal-body">
                    <input type="hidden" name="member_id" id="edit_member_id">
                    <div class="form-group">
                        <label for="edit_role_id">บทบาท</label>
                        <select class="form-control" name="role_id" id="edit_role_id" required>
                            <option value="">เลือกบทบาท</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['role_id']; ?>">
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_is_active">สถานะ</label>
                        <select class="form-control" name="is_active" id="edit_is_active" required>
                            <option value="1">View</option>
                            <option value="2">Half Acesss</option>
                            <option value="0">Full Access</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS for Dropdown Select2 -->
<script>
    $(function() {
        // กำหนดค่า Select2 อย่างง่าย
        $('.select2').select2({
            width: '100%',
            theme: 'bootstrap4',
            dropdownParent: $('#addMemberModal'), // กำหนด parent เป็น modal
            allowClear: true,
            placeholder: 'เลือกผู้ใช้',
            language: {
                noResults: function() {
                    return "ไม่พบข้อมูล";
                },
                searching: function() {
                    return "กำลังค้นหา...";
                }
            }
        });

        // รีเซ็ต select2 เมื่อปิด modal
        $('#addMemberModal').on('hidden.bs.modal', function() {
            $('.select2').val(null).trigger('change');
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