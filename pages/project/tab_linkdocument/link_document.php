
<div class="modal fade" id="linkModal" tabindex="-1" role="dialog" aria-labelledby="linkModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="linkModalLabel">เพิ่มลิงก์เอกสาร</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="linkForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" id="linkId">
                    <div class="form-group">
                        <label for="documentCategory">หมวด/หมู่เอกสาร<span class="text-danger">*</span></label>
                        <select class="form-control" id="documentCategory" required>
                            <option value="">เลือกหมวดหมู่</option>
                            <option value="contract">สัญญา</option>
                            <option value="proposal">หนังสือค่ำประกันสัญญา</option>
                            <option value="proposal">ข้อเสนอโครงการ</option>
                            <option value="report">รายงาน</option>
                            <option value="specification">ข้อกำหนด</option>
                            <option value="other">อื่นๆ</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="documentNames">ชื่อเอกสาร<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="documentNames" required>
                    </div>
                    <div class="form-group">
                        <label for="documentLink">ลิงก์เอกสาร<span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="documentLink" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="saveDocumentLink()">บันทึก</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ฟังก์ชันสำหรับโหลดข้อมูลลิงก์เอกสาร
    function loadDocumentLinks() {
        if ($.fn.DataTable.isDataTable('#example2')) {
            $('#example2').DataTable().destroy();
        }

        // โหลดข้อมูลลิงก์เอกสารที่ต้องการใส่ในตาราง
        $.ajax({
            url: 'get_document_links.php',
            type: 'GET',
            data: {
                project_id: projectId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const tbody = $('#linkTableBody');
                    tbody.empty();

                    response.links.forEach((link, index) => {
                        const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${getCategoryName(link.category)}</td>
                        <td><a href="${link.url}" target="_blank">${link.document_name}</a></td>
                        <td>${formatDate(link.created_at)}</td>
                        <td>${link.created_by_name}</td>
                        <td>
                            <button class="btn btn-sm btn-info mr-1" onclick="editDocumentLink('${link.id}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteDocumentLink('${link.id}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    `;
                        tbody.append(row);
                    });

                    // สร้าง DataTable ใหม่
                    $("#example2").DataTable({
                        "dom": 'Bfrtip',
                        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                        "responsive": true,
                        "lengthChange": true,
                        "autoWidth": false,
                        "order": [],
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json"
                        }
                    }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
                }
            }
        });
    }


    // ฟังก์ชันแปลงหมวดหมู่เป็นภาษาไทย
    function getCategoryName(category) {
        const categories = {
            'contract': 'สัญญา',
            'proposal': 'ข้อเสนอโครงการ',
            'report': 'รายงาน',
            'specification': 'ข้อกำหนด',
            'other': 'อื่นๆ'
        };
        return categories[category] || category;
    }

    // ฟังก์ชันบันทึกลิงก์เอกสาร
    function saveDocumentLink() {
        const linkData = {
            csrf_token: $('input[name="csrf_token"]').val(),
            project_id: projectId,
            link_id: $('#linkId').val(),
            category: $('#documentCategory').val(),
            document_name: $('#documentNames').val(),
            url: $('#documentLink').val()
        };

        $.ajax({
            url: 'save_document_link.php',
            type: 'POST',
            data: linkData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'บันทึกลิงก์เอกสารเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง'
                    }).then(() => {
                        $('#linkModal').modal('hide');
                        loadDocumentLinks();
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

    // ฟังก์ชันแก้ไขลิงก์เอกสาร
    function editDocumentLink(linkId) {
        $.ajax({
            url: 'get_document_link_details.php',
            type: 'GET',
            data: {
                link_id: linkId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#linkId').val(linkId);
                    $('#documentCategory').val(response.link.category);
                    $('#documentNames').val(response.link.document_name);
                    $('#documentLink').val(response.link.url);
                    $('#linkModalLabel').text('แก้ไขลิงก์เอกสาร');
                    $('#linkModal').modal('show');
                }
            }
        });
    }

    // ฟังก์ชันลบลิงก์เอกสาร
    function deleteDocumentLink(linkId) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบลิงก์เอกสารนี้ใช่หรือไม่?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_document_link.php',
                    type: 'POST',
                    data: {
                        csrf_token: $('input[name="csrf_token"]').val(),
                        link_id: linkId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ',
                                text: 'ลบลิงก์เอกสารเรียบร้อยแล้ว',
                                confirmButtonText: 'ตกลง'
                            }).then(() => {
                                loadDocumentLinks();
                            });
                        }
                    }
                });
            }
        });
    }

    // เพิ่ม event listeners
    $(document).ready(function() {
        // โหลดข้อมูลลิงก์เมื่อเปิดแท็บ links
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            if (e.target.hash === '#links') {
                loadDocumentLinks();
            }
        });

        // เพิ่มการเรียกใช้ฟังก์ชันนี้เมื่อโหลดหน้าเว็บเพื่อให้ข้อมูลไม่หายหลังจากรีเฟรช
        loadDocumentLinks();

        // รีเซ็ตฟอร์มเมื่อปิด Modal
        $('#linkModal').on('hidden.bs.modal', function() {
            $('#linkForm').trigger('reset');
            $('#linkId').val('');
            $('#linkModalLabel').text('เพิ่มลิงก์เอกสาร');
        });
    });

    // เพิ่มฟังก์ชันนี้ในส่วน JavaScript
    function formatDate(dateString) {
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('th-TH', options);
    }
</script>

<style>
    /* ปรับสีปุ่ม Excel ให้อยู่ในโทนเดียวกับปุ่มอื่น ๆ */
    .buttons-excel {
        background-color: #007bff !important;
        /* เปลี่ยนสีปุ่มเป็นสีน้ำเงิน */
        border-color: #007bff !important;
        /* เปลี่ยนสีขอบปุ่มให้เข้ากัน */
        color: #ffffff !important;
        /* เปลี่ยนสีตัวอักษรเป็นสีขาว */
    }
</style>