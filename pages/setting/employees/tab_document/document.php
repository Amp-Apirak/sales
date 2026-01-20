<!-- Modal สำหรับอัปโหลดเอกสาร -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" role="dialog" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="uploadDocumentModalLabel">
                    <i class="fas fa-upload"></i> อัปโหลดเอกสาร
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadDocumentForm" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <input type="hidden" name="employee_id" id="employee_id_for_doc" value="<?php echo urlencode($_GET['id'] ?? ''); ?>">

                    <div class="form-group">
                        <label for="document_category">หมวดหมู่เอกสาร <span class="text-danger">*</span></label>
                        <select class="form-control" id="document_category" name="document_category" required>
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            <option value="resume">📄 เรซูเม่</option>
                            <option value="certificate">🎓 ใบประกาศนียบัตร</option>
                            <option value="id_card">🪪 บัตรประชาชน/Passport</option>
                            <option value="contract">📝 สัญญาจ้าง</option>
                            <option value="other">📋 เอกสารอื่นๆ</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="document_name">ชื่อเอกสาร <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="document_name" name="document_name" required placeholder="เช่น Resume 2024, ใบปริญญาบัตร">
                    </div>

                    <div class="form-group">
                        <label for="document_file">ไฟล์เอกสาร <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="document_file" name="document_file" required
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
                            <label class="custom-file-label" for="document_file">เลือกไฟล์...</label>
                        </div>
                        <small class="form-text text-muted">
                            รองรับไฟล์: PDF, Word, Excel, รูปภาพ (JPG, PNG), ZIP | ขนาดสูงสุด 20MB
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="document_description">คำอธิบาย (ถ้ามี)</label>
                        <textarea class="form-control" id="document_description" name="description" rows="3" placeholder="รายละเอียดเพิ่มเติม..."></textarea>
                    </div>

                    <!-- Progress Bar -->
                    <div class="progress d-none" id="uploadProgress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> ยกเลิก
                </button>
                <button type="button" class="btn btn-primary" onclick="uploadDocument()">
                    <i class="fas fa-upload"></i> อัปโหลด
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// แสดงชื่อไฟล์ที่เลือก
$('#document_file').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
});

// ฟังก์ชันอัปโหลดเอกสาร
function uploadDocument() {
    // Validate form
    if (!$('#uploadDocumentForm')[0].checkValidity()) {
        $('#uploadDocumentForm')[0].reportValidity();
        return;
    }

    var formData = new FormData($('#uploadDocumentForm')[0]);
    var fileInput = document.getElementById('document_file');
    var file = fileInput.files[0];

    // ตรวจสอบขนาดไฟล์
    if (file && file.size > 20 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'ไฟล์ใหญ่เกินไป',
            text: 'ขนาดไฟล์ต้องไม่เกิน 20MB',
            confirmButtonText: 'ตกลง'
        });
        return;
    }

    // แสดง Progress Bar
    $('#uploadProgress').removeClass('d-none');
    $('.progress-bar').css('width', '0%');

    $.ajax({
        url: 'tab_document/upload_document.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    var percentComplete = (e.loaded / e.total) * 100;
                    $('.progress-bar').css('width', percentComplete + '%');
                }
            }, false);
            return xhr;
        },
        success: function(response) {
            $('#uploadProgress').addClass('d-none');

            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: response.message,
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    $('#uploadDocumentModal').modal('hide');
                    loadDocuments(); // Reload table
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
        error: function(xhr, status, error) {
            $('#uploadProgress').addClass('d-none');
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                confirmButtonText: 'ตกลง'
            });
        }
    });
}

// ฟังก์ชันโหลดรายการเอกสาร
function loadDocuments() {
    var employeeId = $('#employee_id_for_doc').val();

    $.ajax({
        url: 'tab_document/get_documents.php',
        type: 'GET',
        data: { employee_id: employeeId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var table = $('#documentsTable').DataTable();
                table.clear();

                $.each(response.documents, function(index, doc) {
                    // สร้างปุ่มจัดการ
                    var actionButtons = '<button class="btn btn-sm btn-info mr-1" onclick="downloadDocument(\'' + doc.document_id_encrypted + '\', \'' + doc.document_name + '\')" title="ดาวน์โหลด">' +
                        '<i class="fas fa-download"></i></button>';

                    // แสดงปุ่มลบเฉพาะผู้ที่มีสิทธิ์
                    if (typeof canDelete !== 'undefined' && canDelete) {
                        actionButtons += '<button class="btn btn-sm btn-danger" onclick="deleteDocument(this, \'' + doc.document_id_encrypted + '\')" title="ลบ">' +
                            '<i class="fas fa-trash"></i></button>';
                    }

                    table.row.add([
                        index + 1,
                        '<i class="fas fa-file-' + getFileIcon(doc.document_type) + '"></i> ' + doc.category_name,
                        doc.document_name,
                        doc.file_size_formatted,
                        doc.upload_date_formatted,
                        doc.uploaded_by_name,
                        actionButtons
                    ]).draw(false);
                });
            }
        },
        error: function() {
            console.error('ไม่สามารถโหลดรายการเอกสารได้');
        }
    });
}

// ฟังก์ชันดาวน์โหลดเอกสาร
function downloadDocument(documentId, documentName) {
    window.location.href = 'tab_document/download_document.php?document_id=' + documentId;
}

// ฟังก์ชันลบเอกสาร
function deleteDocument(button, documentId) {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: 'ต้องการลบเอกสารนี้ใช่หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'tab_document/delete_document.php',
                type: 'POST',
                data: {
                    csrf_token: csrfToken,
                    document_id: documentId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'ลบสำเร็จ!',
                            text: response.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        $('#documentsTable').DataTable().row($(button).parents('tr')).remove().draw();
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
                        title: 'การเชื่อมต่อล้มเหลว',
                        text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้',
                        confirmButtonText: 'ตกลง'
                    });
                }
            });
        }
    });
}

// ฟังก์ชันกำหนด Icon ตามประเภทไฟล์
function getFileIcon(type) {
    var icons = {
        'pdf': 'pdf',
        'doc': 'word',
        'docx': 'word',
        'xls': 'excel',
        'xlsx': 'excel',
        'jpg': 'image',
        'jpeg': 'image',
        'png': 'image',
        'zip': 'archive'
    };
    return icons[type] || 'file';
}

// รีเซ็ตฟอร์มเมื่อปิด Modal
$('#uploadDocumentModal').on('hidden.bs.modal', function() {
    $('#uploadDocumentForm')[0].reset();
    $('.custom-file-label').html('เลือกไฟล์...');
    $('#uploadProgress').addClass('d-none');
    $('.progress-bar').css('width', '0%');
});
</script>
