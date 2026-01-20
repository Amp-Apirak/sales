<!-- Project Discussion Board -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-comments"></i> กระดานสนทนาโครงการ</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-sm btn-success mr-2" id="export-word">
                <i class="fas fa-file-word"></i> Export
            </button>
            <button type="button" class="btn btn-sm btn-primary" id="refresh-discussions">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
    <div class="card-body" style="max-height: 600px; overflow-y: auto;" id="discussions-container">
        <!-- Discussions will be loaded here -->
        <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">กำลังโหลดข้อความ...</p>
        </div>
    </div>
    <div class="card-footer">
        <form id="discussion-form" enctype="multipart/form-data">
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

            <div class="form-group">
                <label for="message-text">ข้อความ</label>
                <div class="input-group">
                    <textarea class="form-control" name="message_text" id="message-text" rows="3" placeholder="พิมพ์ข้อความ..."></textarea>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="emoji-btn" title="เพิ่มอิโมจิ">
                            <i class="far fa-smile"></i>
                        </button>
                    </div>
                </div>
                <div id="emoji-picker" class="emoji-picker" style="display: none;"></div>
            </div>

            <div class="form-group">
                <label class="mb-2">
                    <i class="fas fa-paperclip"></i> แนบไฟล์ <small class="text-muted">(สูงสุด 10MB/ไฟล์, สูงสุด 5 ไฟล์)</small>
                </label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="attachments[]" id="attachments" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.txt,.csv">
                    <label class="custom-file-label" for="attachments" id="file-label">
                        <i class="fas fa-upload"></i> เลือกไฟล์...
                    </label>
                </div>
                <small class="form-text text-muted mt-1">
                    <i class="fas fa-info-circle"></i> รองรับ: รูปภาพ, PDF, Word, Excel, PowerPoint, ZIP, RAR
                </small>
                <div id="file-preview" class="mt-2"></div>
            </div>

            <div class="form-group mb-0">
                <button type="submit" class="btn btn-primary btn-md">
                    <i class="fas fa-paper-plane"></i> ส่งข้อความ
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Discussion Modal -->
<div class="modal fade" id="editDiscussionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">แก้ไขข้อความ</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-discussion-form">
                    <input type="hidden" name="discussion_id" id="edit-discussion-id">
                    <div class="form-group">
                        <label>ข้อความ</label>
                        <textarea class="form-control" name="message_text" id="edit-message-text" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" id="save-edit-discussion">บันทึก</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Discussion Item Styles */
.discussion-item {
    border-bottom: 1px solid #e9ecef;
    padding: 20px 0;
    transition: background-color 0.2s ease;
}

.discussion-item:hover {
    background-color: #f8f9fa;
    border-radius: 8px;
    margin: 0 -10px;
    padding: 20px 10px;
}

.discussion-item:last-child {
    border-bottom: none;
}

.discussion-header {
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.discussion-header strong {
    color: #2c3e50;
    font-weight: 600;
}

.discussion-message {
    color: #495057;
    line-height: 1.7;
    font-size: 0.95rem;
    padding: 8px 0;
}

.discussion-attachments .attachment-item {
    margin-right: 10px;
    margin-bottom: 10px;
}

/* Action Buttons */
.discussion-actions {
    margin-top: 10px;
}

.discussion-actions .btn {
    font-size: 0.875rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.discussion-actions .btn-outline-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,123,255,0.3);
}

.discussion-actions .btn-outline-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(220,53,69,0.3);
}

#file-preview .badge {
    margin-right: 5px;
    margin-bottom: 5px;
}

/* Custom File Input Styling */
.custom-file-label {
    cursor: pointer;
    transition: all 0.3s ease;
}

.custom-file-label:hover {
    background-color: #f8f9fa;
    border-color: #80bdff;
}

.custom-file-input:focus ~ .custom-file-label {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.custom-file-label::after {
    content: "เลือก";
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.custom-file-label:hover::after {
    background-color: #0056b3;
    border-color: #0056b3;
}

#file-preview .badge {
    font-size: 0.85rem;
    padding: 0.5em 0.75em;
}

/* Emoji Picker Styles */
.emoji-picker {
    position: absolute;
    z-index: 1000;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    max-width: 320px;
    margin-top: 5px;
}

.emoji-picker .emoji-category {
    margin-bottom: 10px;
}

.emoji-picker .emoji-category-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: #666;
    margin-bottom: 5px;
    padding-bottom: 3px;
    border-bottom: 1px solid #eee;
}

.emoji-picker .emoji-list {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.emoji-picker .emoji-item {
    font-size: 1.4rem;
    cursor: pointer;
    padding: 5px;
    border-radius: 4px;
    transition: background-color 0.2s;
    user-select: none;
}

.emoji-picker .emoji-item:hover {
    background-color: #f0f0f0;
}

#emoji-btn:hover {
    color: #ffc107;
}

/* Avatar & User Info Styles */
.discussion-item img.rounded-circle {
    border: 2px solid #e9ecef;
    transition: border-color 0.3s ease;
}

.discussion-item:hover img.rounded-circle {
    border-color: #007bff;
}

/* Card Header Styles */
.card-header .btn {
    transition: all 0.3s ease;
}

.card-header .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Message Container */
#discussions-container {
    background: white;
}

#discussions-container::-webkit-scrollbar {
    width: 8px;
}

#discussions-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#discussions-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

#discussions-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Card Footer (Form) */
.card-footer {
    background: white;
    border-top: 2px solid #e9ecef;
}

/* Text Muted Enhancement */
.text-muted {
    font-size: 0.875rem;
}

/* Badge Styles */
.badge-sm {
    font-size: 0.75rem;
    padding: 0.25em 0.5em;
    font-weight: 500;
}

.discussion-header .badge {
    vertical-align: middle;
}

.badge-secondary {
    background-color: #6c757d;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

/* Attachment Buttons */
.attachment-item .btn {
    transition: all 0.3s ease;
}

.attachment-item .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Form Controls */
#message-text {
    border-radius: 8px;
    border: 1.5px solid #ced4da;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

#message-text:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Submit Button */
.card-footer .btn-primary {
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.card-footer .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}
</style>

<script>
// Wait for jQuery to be loaded
(function checkjQuery() {
    if (typeof jQuery !== 'undefined') {
        initDiscussionBoard();
    } else {
        setTimeout(checkjQuery, 50);
    }
})();

function initDiscussionBoard() {
    jQuery(document).ready(function($) {
        const projectId = '<?php echo $project_id; ?>';
    let autoRefreshInterval;

    // Load discussions
    function loadDiscussions() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>pages/project/discussion/get_discussions.php',
            type: 'GET',
            data: { project_id: projectId },
            success: function(html) {
                $('#discussions-container').html(html);

                // Scroll to bottom
                const container = document.getElementById('discussions-container');
                container.scrollTop = container.scrollHeight;

                // Initialize lightbox for images
                $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                    event.preventDefault();
                    $(this).ekkoLightbox();
                });
            },
            error: function() {
                $('#discussions-container').html('<div class="alert alert-danger">เกิดข้อผิดพลาดในการโหลดข้อความ</div>');
            }
        });
    }

    // Initial load
    loadDiscussions();

    // Auto-refresh every 15 seconds
    autoRefreshInterval = setInterval(loadDiscussions, 15000);

    // Manual refresh
    $('#refresh-discussions').click(function() {
        $(this).find('i').addClass('fa-spin');
        loadDiscussions();
        setTimeout(() => {
            $(this).find('i').removeClass('fa-spin');
        }, 1000);
    });

    // File preview
    $('#attachments').change(function() {
        const files = this.files;
        let preview = '';
        let fileNames = [];

        if (files.length > 5) {
            Swal.fire('แจ้งเตือน', 'สามารถแนบไฟล์ได้สูงสุด 5 ไฟล์', 'warning');
            this.value = '';
            $('#file-label').html('<i class="fas fa-upload"></i> เลือกไฟล์...');
            return;
        }

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB

            if (fileSize > 10) {
                Swal.fire('แจ้งเตือน', `ไฟล์ ${file.name} มีขนาดเกิน 10MB`, 'warning');
                this.value = '';
                $('#file-preview').html('');
                $('#file-label').html('<i class="fas fa-upload"></i> เลือกไฟล์...');
                return;
            }

            fileNames.push(file.name);
            preview += `<span class="badge badge-info mr-1 mb-1"><i class="fas fa-file"></i> ${file.name} <small>(${fileSize} MB)</small></span>`;
        }

        // Update label
        if (files.length > 0) {
            if (files.length === 1) {
                $('#file-label').html(`<i class="fas fa-file-alt"></i> ${fileNames[0]}`);
            } else {
                $('#file-label').html(`<i class="fas fa-files-o"></i> เลือกแล้ว ${files.length} ไฟล์`);
            }
        } else {
            $('#file-label').html('<i class="fas fa-upload"></i> เลือกไฟล์...');
        }

        $('#file-preview').html(preview);
    });

    // Submit discussion
    $('#discussion-form').submit(function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const messageText = $('#message-text').val().trim();
        const filesCount = $('#attachments')[0].files.length;

        if (!messageText && filesCount === 0) {
            Swal.fire('แจ้งเตือน', 'กรุณาพิมพ์ข้อความหรือแนบไฟล์', 'warning');
            return;
        }

        $.ajax({
            url: '<?php echo BASE_URL; ?>pages/project/discussion/post_discussion.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#message-text').val('');
                    $('#attachments').val('');
                    $('#file-preview').html('');
                    $('#file-label').html('<i class="fas fa-upload"></i> เลือกไฟล์...');
                    loadDiscussions();

                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('ข้อผิดพลาด', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการส่งข้อความ: ' + error, 'error');
            }
        });
    });

    // Edit discussion
    $(document).on('click', '.edit-discussion', function() {
        const discussionId = $(this).data('id');
        const messageText = $(this).closest('.discussion-item').find('.message-text').text();

        $('#edit-discussion-id').val(discussionId);
        $('#edit-message-text').val(messageText);
        $('#editDiscussionModal').modal('show');
    });

    // Save edit
    $('#save-edit-discussion').click(function() {
        const formData = $('#edit-discussion-form').serialize();

        $.ajax({
            url: '<?php echo BASE_URL; ?>pages/project/discussion/edit_discussion.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editDiscussionModal').modal('hide');
                    loadDiscussions();
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('ข้อผิดพลาด', response.message, 'error');
                }
            }
        });
    });

    // Delete discussion
    $(document).on('click', '.delete-discussion', function() {
        const discussionId = $(this).data('id');

        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: 'คุณต้องการลบข้อความนี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo BASE_URL; ?>pages/project/discussion/delete_discussion.php',
                    type: 'POST',
                    data: { discussion_id: discussionId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            loadDiscussions();
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('ข้อผิดพลาด', response.message, 'error');
                        }
                    }
                });
            }
        });
    });

    // Emoji Picker
    const emojis = {
        'ยิ้ม': ['😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '🙃', '😉', '😊', '😇'],
        'หัวใจ': ['❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💖', '💗'],
        'มือ': ['👍', '👎', '👌', '✌️', '🤞', '🤝', '👏', '🙌', '🙏', '💪', '✊', '👊'],
        'สัญลักษณ์': ['✅', '❌', '⭐', '🔥', '💯', '✨', '🎉', '🎊', '💡', '📌', '📍', '🔔', '⚡']
    };

    let emojiPickerHTML = '';
    for (const [category, emojiList] of Object.entries(emojis)) {
        emojiPickerHTML += `
            <div class="emoji-category">
                <div class="emoji-category-title">${category}</div>
                <div class="emoji-list">
                    ${emojiList.map(emoji => `<span class="emoji-item" data-emoji="${emoji}">${emoji}</span>`).join('')}
                </div>
            </div>
        `;
    }
    $('#emoji-picker').html(emojiPickerHTML);

    // Toggle emoji picker
    $('#emoji-btn').click(function(e) {
        e.stopPropagation();
        $('#emoji-picker').toggle();
    });

    // Insert emoji
    $(document).on('click', '.emoji-item', function() {
        const emoji = $(this).data('emoji');
        const textarea = $('#message-text');
        const cursorPos = textarea[0].selectionStart;
        const textBefore = textarea.val().substring(0, cursorPos);
        const textAfter = textarea.val().substring(cursorPos);

        textarea.val(textBefore + emoji + textAfter);
        textarea.focus();

        // Set cursor position after emoji
        const newPos = cursorPos + emoji.length;
        textarea[0].setSelectionRange(newPos, newPos);

        $('#emoji-picker').hide();
    });

    // Close emoji picker when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('#emoji-picker, #emoji-btn').length) {
            $('#emoji-picker').hide();
        }
    });

    // Export to Word
    $('#export-word').click(function() {
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> กำลัง Export...');

        window.location.href = '<?php echo BASE_URL; ?>pages/project/discussion/export_word.php?project_id=' + projectId;

        setTimeout(() => {
            $(this).prop('disabled', false).html('<i class="fas fa-file-word"></i> Export');
        }, 2000);
    });

    // Clear interval on page unload
    $(window).on('beforeunload', function() {
        clearInterval(autoRefreshInterval);
    });
    });
}
</script>
