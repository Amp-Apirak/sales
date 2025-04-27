<?php
//session_start and Config DB
include  '../../include/Add_session.php';

$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$user_id = $_SESSION['user_id'];  // ดึง user_id ของผู้ใช้จาก session

// ดึงข้อมูลจากฟอร์มค้นหา
$search = isset($_POST['searchservice']) ? $_POST['searchservice'] : '';
$search_service_category = isset($_POST['service_category']) ? $_POST['service_category'] : '';
$search_category = isset($_POST['category']) ? $_POST['category'] : '';
$search_sub_category = isset($_POST['sub_category']) ? $_POST['sub_category'] : '';

// การดึงข้อมูลจากฐานข้อมูลแต่ละคอลัมน์เพื่อสร้างตัวเลือก
$sql_service_category = "SELECT DISTINCT service_category FROM Category";
$query_service_category = $condb->query($sql_service_category);

$sql_category = "SELECT DISTINCT category FROM Category";
$query_category = $condb->query($sql_category);

$sql_sub_category = "SELECT DISTINCT sub_category FROM Category";
$query_sub_category = $condb->query($sql_sub_category);

// สร้าง SQL Query โดยพิจารณาจากการค้นหา
$sql_categories = "SELECT c.*, u.first_name, u.last_name
                   FROM Category c
                   LEFT JOIN users u ON c.created_by = u.user_id
                   WHERE 1=1";


// เพิ่มเงื่อนไขการค้นหาตามฟิลด์ที่ระบุ
if (!empty($search)) {
    $sql_categories .= " AND (c.service_category LIKE :search OR c.category LIKE :search OR c.sub_category LIKE :search OR c.problems LIKE :search OR c.cases LIKE :search OR c.resolve LIKE :search)";
}
if (!empty($search_service_category)) {
    $sql_categories .= " AND c.service_category = :search_service_category";
}
if (!empty($search_category)) {
    $sql_categories .= " AND c.category = :search_category";
}
if (!empty($search_sub_category)) {
    $sql_categories .= " AND c.sub_category = :search_sub_category";
}

// เตรียม statement
$stmt = $condb->prepare($sql_categories);

// ทำการ bind ค่าต่างๆ
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param);
}
if (!empty($search_service_category)) {
    $stmt->bindParam(':search_service_category', $search_service_category);
}
if (!empty($search_category)) {
    $stmt->bindParam(':search_category', $search_category);
}
if (!empty($search_sub_category)) {
    $stmt->bindParam(':search_sub_category', $search_sub_category);
}

// Execute query
$stmt->execute();
$query_categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<?php $menu = "category"; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | Category Management</title>
    <?php include  '../../include/header.php'; ?>

    <!-- /* ใช้ฟอนต์ Noto Sans Thai กับ label */ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <style>
        /* ใช้ฟอนต์ Noto Sans Thai กับ label */
        th,h1 {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 700;
            /* ปรับระดับน้ำหนักของฟอนต์ */
            font-size: 14px;
            color: #333;
        }

        /* tr {
            font-family: 'Noto Sans Thai', sans-serif;
            font-weight: 400;
            font-size: 14px;
            color: #333;
        } */
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include  '../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Category Management</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item active">Category Management v1</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <section class="content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-info">
                                            <div class="card-header">
                                                <h3 class="card-title font1">ค้นหา</h3>
                                            </div>
                                            <div class="card-body">
                                                <form action="#" method="POST">
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" id="searchservice" name="searchservice" value="<?php echo htmlspecialchars($search); ?>" placeholder="ค้นหา...">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <button type="submit" class="btn btn-primary" id="search" name="search">ค้นหา</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Service Category</label>
                                                                <select class="custom-select select2" name="service_category">
                                                                    <option value="">เลือก</option>
                                                                    <?php while ($service_category = $query_service_category->fetch()) { ?>
                                                                        <option value="<?php echo htmlspecialchars($service_category['service_category']); ?>"><?php echo htmlspecialchars($service_category['service_category']); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Category</label>
                                                                <select class="custom-select select2" name="category">
                                                                    <option value="">เลือก</option>
                                                                    <?php while ($category = $query_category->fetch()) { ?>
                                                                        <option value="<?php echo htmlspecialchars($category['category']); ?>"><?php echo htmlspecialchars($category['category']); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>Sub-Category</label>
                                                                <select class="custom-select select2" name="sub_category">
                                                                    <option value="">เลือก</option>
                                                                    <?php while ($sub_category = $query_sub_category->fetch()) { ?>
                                                                        <option value="<?php echo htmlspecialchars($sub_category['sub_category']); ?>"><?php echo htmlspecialchars($sub_category['sub_category']); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <div class="col-md-12 pb-3">
                                <a href="add_category.php" class="btn btn-success btn-sm float-right">เพิ่มข้อมูล<i class=""></i></a>
                            </div><br>

                            <div class="card">
                                <div class="card-header">
                                    <div class="container-fluid">
                                        <h3 class="card-title">Category Management</h3>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap text-center">ลำดับ</th>
                                                <th class="text-nowrap text-center">หมวดหมู่บริการ</th>
                                                <th class="text-nowrap text-center">หมวดหมู่</th>
                                                <th class="text-nowrap text-center">หมวดหมู่ย่อย</th>
                                                <th class="text-nowrap text-center">ปัญหา</th>
                                                <th class="text-nowrap text-center">สาเหตุ</th>
                                                <th class="text-nowrap text-center">วิธีแก้ไข</th>
                                                <th class="text-nowrap text-center">ผู้สร้าง</th>
                                                <th class="text-nowrap text-center">วันที่สร้าง</th>
                                                <th class="text-nowrap text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($query_categories as $index => $category) { ?>
                                                <tr>
                                                    <td class="text-nowrap text-center"><?php echo $index + 1; ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($category['service_category']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($category['category']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($category['sub_category']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($category['problems']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($category['cases']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($category['resolve']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($category['first_name'] . ' ' . $category['last_name']); ?></td>
                                                    <td class="text-nowrap"><?php echo htmlspecialchars($category['created_at']); ?></td>
                                                    <td class="text-nowrap">
                                                        <a href="view_category.php?category_id=<?php echo urlencode(encryptUserId($category['id'])); ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="edit_category.php?category_id=<?php echo urlencode(encryptUserId($category['id'])); ?>" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                                        <a href="javascript:void(0);" onclick="confirmDelete('<?php echo urlencode(encryptUserId($category['id'])); ?>')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include  '../../include/footer.php'; ?>
    </div>

    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": false,
                "lengthChange": false,
                "autoWidth": false,
                "scrollX": true,
                "scrollCollapse": true,
                "paging": true,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });

        $(function() {
            $('.select2').select2()
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
        });

        function confirmDelete(categoryId) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณจะไม่สามารถเรียกคืนข้อมูลนี้ได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_category.php?category_id=' + categoryId;
                }
            })
        }
    </script>
</body>

</html>