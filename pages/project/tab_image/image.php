<!-- 4. การอัปโหลดและแสดงรูปภาพ -->
<script>
    $(document).ready(function() {
        loadImages();

        $('#imageUpload').on('change', function(e) {
            var files = e.target.files;
            uploadImages(files);
        });
    });

    function loadImages() {
        $.ajax({
            url: 'get_images.php',
            type: 'GET',
            data: {
                project_id: '<?php echo $project_id; ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#imageGallery').empty();
                    response.images.forEach(function(image) {
                        addImageToGallery(image);
                    });
                } else {
                    console.error('Failed to load images:', response.message);
                }
            },
            error: function() {
                console.error('Error connecting to server');
            }
        });
    }

    function uploadImages(files) {
        var formData = new FormData();
        formData.append('project_id', '<?php echo $project_id; ?>');
        formData.append('csrf_token', '<?php echo $csrf_token; ?>');

        for (var i = 0; i < files.length; i++) {
            formData.append('images[]', files[i]);
        }

        $.ajax({
            url: 'upload_images.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total * 100;
                        $('.progress-bar').width(percentComplete + '%').attr('aria-valuenow', percentComplete).text(percentComplete.toFixed(2) + '%');
                    }
                }, false);
                return xhr;
            },
            beforeSend: function() {
                $('#uploadProgress').show();
            },
            success: function(response) {
                console.log('Server response:', response);
                if (response.success) {
                    response.images.forEach(function(image) {
                        addImageToGallery(image);
                    });
                    Swal.fire({
                        icon: 'success',
                        title: 'อัปโหลดสำเร็จ',
                        text: 'อัปโหลดรูปภาพเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง'
                    }).then(() => {
                        loadImages(); // เพิ่มการเรียกฟังก์ชัน loadImages() หลังการแจ้งเตือนสำเร็จ
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message || 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ',
                        confirmButtonText: 'ตกลง'
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                    confirmButtonText: 'ตกลง'
                });
            },
            complete: function() {
                $('#uploadProgress').hide();
                $('.progress-bar').width('0%').attr('aria-valuenow', 0).text('0%');
            }
        });
    }

    function addImageToGallery(image) {
        var imageHtml = `
    <div class="image-card" data-id="${image.id}">
      <img src="${image.url}" alt="${image.name}" onclick="openLightbox(this.src)">
      <button class="delete-btn" onclick="deleteImage('${image.id}')">×</button>
      <div class="image-info">
        <h5>${image.name}</h5>
        <p>Size: ${formatFileSize(image.size)}<br>Type: ${image.type}</p>
      </div>
    </div>
  `;
        $('#imageGallery').append(imageHtml);
    }

    function openLightbox(imgSrc) {
        $('#lightbox-img').attr('src', imgSrc);
        $('#lightbox').css('display', 'flex');
    }

    // เพิ่มการจัดการคลิกที่ปุ่มปิดและพื้นหลัง
    $('#lightbox .close, #lightbox').click(function() {
        $('#lightbox').hide();
    });

    // ป้องกันการปิด lightbox เมื่อคลิกที่รูปภาพ
    $('#lightbox-img').click(function(e) {
        e.stopPropagation();
    });


    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function deleteImage(imageId) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบรูปภาพนี้ใช่หรือไม่?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_image.php',
                    type: 'POST',
                    data: {
                        csrf_token: '<?php echo $csrf_token; ?>',
                        image_id: imageId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'ลบแล้ว!',
                                'รูปภาพถูกลบเรียบร้อยแล้ว',
                                'success'
                            ).then(() => {
                                loadImages(); // รีโหลดรูปภาพ
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
</script>
<!-- 4. การอัปโหลดและแสดงรูปภาพ -->