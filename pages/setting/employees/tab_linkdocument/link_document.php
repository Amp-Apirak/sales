<!-- Modal สำหรับเพิ่ม/แก้ไขลิงก์เอกสาร -->
<div class="modal fade" id="linkDocumentModal" tabindex="-1" role="dialog" aria-labelledby="linkDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="linkDocumentModalLabel">
                    <i class="fas fa-link"></i> <span id="linkModalTitle">เพิ่มลิงก์เอกสาร</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="linkDocumentForm">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="employee_id" id="employee_id_for_link" value="<?php echo urlencode($_GET['id'] ?? ''); ?>">
                    <input type="hidden" name="link_id" id="link_id">

                    <div class="form-group">
                        <label for="link_category">หมวดหมู่ลิงก์ <span class="text-danger">*</span></label>
                        <select class="form-control" id="link_category" name="link_category" required>
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            <option value="drive">📁 Google Drive</option>
                            <option value="sharepoint">📁 SharePoint</option>
                            <option value="onedrive">📁 OneDrive</option>
                            <option value="other">🔗 ลิงก์อื่นๆ</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="link_name">ชื่อลิงก์ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="link_name" name="link_name" required placeholder="เช่น CV Folder, เอกสารส่วนตัว">
                    </div>

                    <div class="form-group">
                        <label for="link_url">URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="link_url" name="url" required placeholder="https://...">
                        <small class="form-text text-muted">
                            URL ต้องเริ่มด้วย https:// เท่านั้น
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="link_description">คำอธิบาย (ถ้ามี)</label>
                        <textarea class="form-control" id="link_description" name="description" rows="3" placeholder="รายละเอียดเพิ่มเติม..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> ยกเลิก
                </button>
                <button type="button" class="btn btn-primary" onclick="saveLink()">
                    <i class="fas fa-save"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ฟังก์ชันบันทึกลิงก์
function saveLink() {
    // Validate form
    if (!$('#linkDocumentForm')[0].checkValidity()) {
        $('#linkDocumentForm')[0].reportValidity();
        return;
    }

    // Validate URL format
    var url = $('#link_url').val();
    if (!url.startsWith('https://')) {
        Swal.fire({
            icon: 'error',
            title: 'URL ไม่ถูกต้อง',
            text: 'URL ต้องเริ่มด้วย https:// เท่านั้น',
            confirmButtonText: 'ตกลง'
        });
        return;
    }

    $.ajax({
        url: 'tab_linkdocument/save_document_link.php',
        type: 'POST',
        data: $('#linkDocumentForm').serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                setTimeout(loadLinks, 200);
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: response.message,
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    $('#linkDocumentModal').modal('hide');
                    $('#linkDocumentForm')[0].reset();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message,
                    confirmButtonText: 'ตกลง'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                confirmButtonText: 'ตกลง'
            });
        }
    });
}

// ฟังก์ชันโหลดรายการลิงก์
function loadLinks() {
    var employeeId = $('#employee_id_for_link').val();

    $.ajax({
        url: 'tab_linkdocument/get_document_links.php',
        type: 'GET',
        data: {
            employee_id: employeeId,
            _: Date.now() // ป้องกันการ cache ผลลัพธ์เก่า
        },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var table = $('#linksTable').DataTable();
                var rows = [];

                $.each(response.links, function(index, link) {
                    var actionButtons = '<button class="btn btn-sm btn-warning mr-1" onclick="editLink(\'' + link.link_id_encrypted + '\')" title="แก้ไข">' +
                        '<i class="fas fa-edit"></i></button>';

                    if (typeof canDelete !== 'undefined' && canDelete) {
                        actionButtons += '<button class="btn btn-sm btn-danger" onclick="deleteLink(\'' + link.link_id_encrypted + '\')" title="ลบ">' +
                            '<i class="fas fa-trash"></i></button>';
                    }

                    rows.push([
                        index + 1,
                        '<i class="fas fa-folder"></i> ' + link.category_name,
                        '<a href="' + link.url + '" target="_blank">' + link.link_name + ' <i class="fas fa-external-link-alt"></i></a>',
                        link.created_at_formatted,
                        link.created_by_name,
                        actionButtons
                    ]);
                });

                table.clear().rows.add(rows).draw(false);
                table.columns.adjust().draw(false);
                if (table.responsive && table.responsive.recalc) {
                    table.responsive.recalc();
                }
            }
        },
        error: function() {
            console.error('ไม่สามารถโหลดรายการลิงก์ได้');
        }
    });
}

// ฟังก์ชันแก้ไขลิงก์
function editLink(linkId) {
    $.ajax({
        url: 'tab_linkdocument/get_document_links.php',
        type: 'GET',
        data: { employee_id: $('#employee_id_for_link').val() },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var link = response.links.find(l => l.link_id_encrypted === linkId);
                if (link) {
                    $('#link_id').val(linkId);
                    $('#link_category').val(link.link_category);
                    $('#link_name').val(link.link_name);
                    $('#link_url').val(link.url);
                    $('#link_description').val(link.description);
                    $('#linkModalTitle').text('แก้ไขลิงก์เอกสาร');
                    $('#linkDocumentModal').modal('show');
                }
            }
        }
    });
}

// ฟังก์ชันลบลิงก์
function deleteLink(linkId) {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: 'ต้องการลบลิงก์นี้ใช่หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'tab_linkdocument/delete_document_link.php',
                type: 'POST',
                data: {
                    csrf_token: csrfToken,
                    link_id: linkId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        setTimeout(loadLinks, 200);
                        Swal.fire({
                            icon: 'success',
                            title: 'ลบสำเร็จ',
                            text: response.message,
                            confirmButtonText: 'ตกลง'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: response.message,
                            confirmButtonText: 'ตกลง'
                        });
                    }
                }
            });
        }
    });
}

// รีเซ็ตฟอร์มเมื่อปิด Modal
$('#linkDocumentModal').on('hidden.bs.modal', function() {
    $('#linkDocumentForm')[0].reset();
    $('#link_id').val('');
    $('#linkModalTitle').text('เพิ่มลิงก์เอกสาร');
});

// เปิด Modal เพื่อเพิ่มลิงก์ใหม่
function openAddLinkModal() {
    $('#linkDocumentForm')[0].reset();
    $('#link_id').val('');
    $('#linkModalTitle').text('เพิ่มลิงก์เอกสาร');
    $('#linkDocumentModal').modal('show');
}
</script>
