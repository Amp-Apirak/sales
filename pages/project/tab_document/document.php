<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">อัปโหลดเอกสาร</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div class="form-group">
                        <label for="documentName">ชื่อเอกสาร</label>
                        <input type="text" class="form-control" id="documentName" name="documentName" required>
                    </div>
                    <div class="form-group">
                        <label for="documentFile">เลือกไฟล์</label>
                        <input type="file" class="form-control-file" id="documentFile" name="documentFile" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="uploadDocument()">อัปโหลด</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ฟังก์ชันสำหรับอัปโหลดเอกสาร
    function uploadDocument() {
        var formData = new FormData(document.getElementById('uploadForm'));

        $.ajax({
            url: 'upload_document.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'อัปโหลดสำเร็จ',
                        text: 'เอกสารถูกอัปโหลดเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#uploadModal').modal('hide');
                            loadDocuments(); // รีโหลดข้อมูลเอกสาร
                            resetUploadForm(); // เพิ่มฟังก์ชันนี้เพื่อรีเซ็ตฟอร์ม
                        }
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

    // เพิ่มฟังก์ชันใหม่เพื่อรีเซ็ตฟอร์ม
    function resetUploadForm() {
        $('#uploadForm')[0].reset();
        $('#documentFile').val(''); // รีเซ็ต file input
    }

    // เพิ่ม event listener เมื่อ Modal ถูกซ่อน
    $('#uploadModal').on('hidden.bs.modal', function() {
        resetUploadForm();
    });

    // ฟังก์ชันสำหรับโหลดข้อมูลเอกสาร
    function loadDocuments() {
        $.ajax({
            url: 'get_documents.php',
            type: 'GET',
            data: {
                project_id: '<?php echo $project_id; ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var table = $('#example1').DataTable();
                    table.clear().draw();
                    $.each(response.documents, function(index, doc) {
                        table.row.add([
                            index + 1,
                            doc.document_name,
                            doc.document_type,
                            doc.upload_date,
                            doc.uploaded_by,
                            '<button class="btn btn-sm btn-info mr-1" onclick="viewDocument(\'' + doc.document_id + '\')">ดู</button>' +
                            '<button class="btn btn-sm btn-danger" onclick="deleteDocument(\'' + doc.document_id + '\')">ลบ</button>'
                        ]).draw(false);
                    });
                } else {
                    console.error('Failed to load documents:', response.message);
                }
            },
            error: function() {
                console.error('Error connecting to server');
            }
        });
    }

    // ฟังก์ชันสำหรับดูเอกสาร
    function viewDocument(documentId) {
        window.open('view_document.php?document_id=' + documentId, '_blank');
    }

    // ฟังก์ชันสำหรับลบเอกสาร
    function deleteDocument(documentId) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบเอกสารนี้ใช่หรือไม่?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_document.php',
                    type: 'POST',
                    data: {
                        csrf_token: '<?php echo $csrf_token; ?>',
                        document_id: documentId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'ลบแล้ว!',
                                'เอกสารถูกลบเรียบร้อยแล้ว',
                                'success'
                            ).then(() => {
                                loadDocuments(); // รีโหลดข้อมูลเอกสาร
                            });
                        } else {
                            Swal.fire(
                                'เกิดข้อผิดพลาด!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'เกิดข้อผิดพลาด!',
                            'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                            'error'
                        );
                    }
                });
            }
        });
    }

    // โหลดข้อมูลเอกสารเมื่อเปิดแท็บเอกสาร
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        if (e.target.hash === '#documents') {
            loadDocuments();
        }
    });
</script>