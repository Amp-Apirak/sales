<?php
// เริ่มต้น session และเชื่อมต่อฐานข้อมูล
// ส่วนนี้เป็นการนำเข้าไฟล์ 'Add_session.php' เพื่อเริ่มต้น session และทำการเชื่อมต่อกับฐานข้อมูล
include '../../include/Add_session.php';

// ตรวจสอบว่ามีการส่ง category_id มาหรือไม่
// เช็คว่าค่าของ category_id ถูกส่งมาผ่าน URL หรือไม่ ถ้าไม่มีการส่งค่าจะถูกส่งไปยังหน้า 'category.php' และจบการทำงานของสคริปต์
if (!isset($_GET['category_id'])) {
    header("Location: category.php"); // ถ้าไม่มีการส่ง category_id มา ให้เปลี่ยนหน้าไปที่ category.php
    exit(); // จบการทำงาน
}

// ถ้ามีการส่ง category_id มา จะถูกถอดรหัสด้วยฟังก์ชัน decryptUserId
$category_id = decryptUserId($_GET['category_id']);

// ดึงข้อมูล Category จากฐานข้อมูล
// เตรียมคำสั่ง SQL เพื่อดึงข้อมูล Category พร้อมกับข้อมูลของผู้ที่สร้าง (created_by) โดยใช้การ JOIN กับตาราง users
$stmt = $condb->prepare("SELECT c.*, u.first_name, u.last_name 
                         FROM Category c 
                         LEFT JOIN users u ON c.created_by = u.user_id 
                         WHERE c.id = :category_id");

// ทำการผูกค่าพารามิเตอร์ category_id กับตัวแปร $category_id
$stmt->bindParam(':category_id', $category_id, PDO::PARAM_STR);
// ประมวลผลคำสั่ง SQL
$stmt->execute();
// ดึงผลลัพธ์ออกมาเป็น array แบบ associative
$category = $stmt->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบว่า Category มีข้อมูลหรือไม่
// ถ้าไม่มีข้อมูลที่ตรงกับ category_id ให้เปลี่ยนหน้าไปที่ 'category.php'
if (!$category) {
    header("Location: category.php");
    exit();
}

//ดึงข้อมูลภาพ ------------------------------------------------------------------------------------
// ดึงข้อมูลรูปภาพที่เกี่ยวข้องกับ Category นี้
// เตรียมคำสั่ง SQL เพื่อดึงข้อมูลรูปภาพที่สัมพันธ์กับ category_id จากตาราง category_image
$stmt = $condb->prepare("SELECT * FROM category_image WHERE category_id = :category_id");
// ผูกค่าพารามิเตอร์ category_id กับตัวแปร $category_id
$stmt->bindParam(':category_id', $category_id, PDO::PARAM_STR);
// ประมวลผลคำสั่ง SQL
$stmt->execute();
// ดึงผลลัพธ์ทั้งหมดออกมาเป็น array แบบ associative
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "category"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | View Category</title>
    <?php include '../../include/header.php'; ?>

    <!-- เชื่อมต่อกับ Google Fonts สำหรับ Noto Sans Thai -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">

    <!-- สไตล์ CSS สำหรับหน้าเว็บ -->
    <?php include 'style_category.php'; ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <!-- ส่วนหัวของเนื้อหา -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>View Category</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="category.php">Categories</a></li>
                                <li class="breadcrumb-item active">View Category</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ส่วนเนื้อหาหลัก -->
            <section class="content">
                <div class="container-fluid">
                    <!-- ส่วนหัวของ Category -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="category-header">
                                <h2><?php echo htmlspecialchars($category['service_category']); ?></h2>
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($category['category']); ?> | <strong>Sub-category:</strong> <?php echo htmlspecialchars($category['sub_category']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- ส่วนข้อมูลเพิ่มเติม -->
                    <div class="info-cards">
                        <div class="info-card">
                            <div class="info-card-icon" style="background-color: #17a2b8;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="info-card-content">
                                <div class="info-card-label">ผู้สร้าง</div>
                                <div class="info-card-value"><?php echo htmlspecialchars($category['first_name'] . ' ' . $category['last_name']) ?: 'N/A'; ?></div>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-card-icon" style="background-color: #28a745;">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="info-card-content">
                                <div class="info-card-label">วันที่สร้าง</div>
                                <div class="info-card-value"><?php echo htmlspecialchars($category['created_at']) ?: 'N/A'; ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- ส่วนรายละเอียด Category -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Category Details</h3>
                                </div>
                                <div class="card-body">
                                    <div class="category-details">
                                        <div class="detail-card">
                                            <div class="detail-card-header">Problems</div>
                                            <div class="detail-card-body">
                                                <div class="detail-card-content">
                                                    <?php echo nl2br(htmlspecialchars($category['problems'] ?? 'No problems specified')); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="detail-card">
                                            <div class="detail-card-header">Cases</div>
                                            <div class="detail-card-body">
                                                <div class="detail-card-content">
                                                    <?php echo nl2br(htmlspecialchars($category['cases'] ?? 'No cases specified')); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="detail-card">
                                            <div class="detail-card-header">Resolution</div>
                                            <div class="detail-card-body">
                                                <div class="detail-card-content">
                                                    <?php echo nl2br(htmlspecialchars($category['resolve'] ?? 'No resolution specified')); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ส่วนรูปภาพ Category -->
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="card category-images">
                                <div class="card-header">
                                    <h3 class="card-title">รูปภาพประกอบ</h3>
                                    <button class="btn btn-primary btn-sm btn-add-image" data-toggle="modal" data-target="#addImageModal">Add Image</button>
                                </div>
                                <div class="card-body">
                                    <div class="image-gallery">
                                        <?php if (empty($images)): ?>
                                            <p>No images available for this category.</p>
                                        <?php else: ?>
                                            <?php foreach ($images as $image): ?>
                                                <div class="image-item">
                                                    <img src="<?php echo htmlspecialchars($image['file_path']); ?>" alt="Category Image">
                                                    <div class="image-actions">
                                                        <button class="btn btn-sm btn-danger" onclick="deleteImage('<?php echo $image['id']; ?>')"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ส่วนปุ่มดำเนินการ -->
                    <div class="row mt-4 mb-5">
                        <div class="col-md-12">
                            <a href="edit_category.php?category_id=<?php echo urlencode($_GET['category_id']); ?>" class="btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i> Edit Category
                            </a>
                            <a href="category.php" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../../include/footer.php'; ?>
    </div>

    <!-- Modal สำหรับเพิ่มรูปภาพ -->
    <div class="modal fade" id="addImageModal" tabindex="-1" role="dialog" aria-labelledby="addImageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title btn-sm" id="addImageModalLabel">Add New Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addImageForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="imageFile">Select Image</label>
                            <input type="file" class="form-control-file" id="imageFile" name="imageFile" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Upload Image</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript สำหรับจัดการการอัปโหลดรูปภาพและลบรูปภาพ -->
    <script>
        $(function() {
            // รีเซ็ตฟอร์มเมื่อเปิด Modal
            $('#addImageModal').on('show.bs.modal', function() {
                $(this).find('form')[0].reset();
            });

            // จัดการการส่งฟอร์มอัปโหลดรูปภาพ
            $('#addImageForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var category_id = '<?php echo $category_id; ?>';
                formData.append('category_id', category_id);

                $.ajax({
                    url: 'upload_image.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json', // เพิ่มบรรทัดนี้
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: response.message
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
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
                        });
                    }
                });
            });
        });

        // ฟังก์ชันสำหรับลบรูปภาพ
        function deleteImage(imageId) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณจะไม่สามารถกู้คืนรูปภาพนี้ได้!",
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
                            image_id: imageId
                        },
                        success: function(response) {
                            try {
                                var result = JSON.parse(response);
                                if (result.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'ลบแล้ว!',
                                        text: 'รูปภาพของคุณถูกลบแล้ว',
                                        confirmButtonText: 'ตกลง'
                                    }).then((result) => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'เกิดข้อผิดพลาด!',
                                        text: 'เกิดข้อผิดพลาดในการลบรูปภาพ: ' + result.message,
                                        confirmButtonText: 'ตกลง'
                                    });
                                }
                            } catch (e) {
                                console.error('Error parsing JSON:', response);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด!',
                                    text: 'ไม่สามารถประมวลผลการตอบกลับจากเซิร์ฟเวอร์ได้',
                                    confirmButtonText: 'ตกลง'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                                confirmButtonText: 'ตกลง'
                            });
                        }
                    });
                }
            });
        }
    </script>
</body>

</html>