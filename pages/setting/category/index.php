<?php
session_start();
require_once __DIR__ . '/../../../config/condb.php';
require_once __DIR__ . '/../../../config/validation.php';

// Role check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Executive') {
    header('Location: /sales/index.php');
    exit();
}

$csrf_token = generateCSRFToken();
$message = '';

// Handle POST requests for Add, Edit, Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add') {
            $stmt = $condb->prepare("INSERT INTO category (id, service_category, category, sub_category, created_by) VALUES (UUID(), ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['service_category'],
                $_POST['category'],
                $_POST['sub_category'],
                $_SESSION['user_id']
            ]);
            $message = 'success:Category added successfully!';
        } elseif ($action === 'edit') {
            $stmt = $condb->prepare("UPDATE category SET service_category = ?, category = ?, sub_category = ? WHERE id = ?");
            $stmt->execute([
                $_POST['service_category'],
                $_POST['category'],
                $_POST['sub_category'],
                $_POST['edit_id']
            ]);
            $message = 'success:Category updated successfully!';
        } elseif ($action === 'delete') {
            $stmt = $condb->prepare("DELETE FROM category WHERE id = ?");
            $stmt->execute([$_POST['delete_id']]);
            $message = 'success:Category deleted successfully!';
        }
    } catch (PDOException $e) {
        $message = 'error:An error occurred: ' . $e->getMessage();
    }
    
    // Redirect to avoid form resubmission
    header('Location: ' . $_SERVER['PHP_SELF'] . '?message=' . urlencode($message));
    exit();
}

// Fetch all categories for display
$categories = $condb->query("
    SELECT c.*, u.first_name, u.last_name 
    FROM category c 
    LEFT JOIN users u ON c.created_by = u.user_id 
    ORDER BY c.service_category, c.category, c.sub_category
")->fetchAll(PDO::FETCH_ASSOC);

// Display feedback message
if (isset($_GET['message'])) {
    list($type, $text) = explode(':', $_GET['message'], 2);
    $alert_class = $type === 'success' ? 'alert-success' : 'alert-danger';
    $alert_icon = $type === 'success' ? 'fa-check' : 'fa-ban';
    $alert_title = $type === 'success' ? 'Success!' : 'Error!';
}

?>
<!DOCTYPE html>
<html lang="en">
<?php $menu = "service_category"; ?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Service Category Management</title>
    <?php include '../../../include/header.php'; ?>
</head>
<body class="sidebar-mini layout-fixed">
<div class="wrapper">
    <?php include '../../../include/navbar.php'; ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Service Category Management</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/sales/index.php">Home</a></li>
                            <li class="breadcrumb-item active">Service Categories</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <?php if (isset($alert_class)): ?>
                <div class="alert <?= $alert_class ?> alert-dismissible fade show" role="alert">
                    <strong><?= $alert_title ?></strong> <?= htmlspecialchars(urldecode($text)) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Category List</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addCategoryModal">
                                <i class="fas fa-plus"></i> Add New Category
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="categoryTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Service Category</th>
                                    <th>Category</th>
                                    <th>Sub Category</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $index => $cat): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($cat['service_category']) ?></td>
                                    <td><?= htmlspecialchars($cat['category']) ?></td>
                                    <td><?= htmlspecialchars($cat['sub_category'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($cat['first_name'] . ' ' . $cat['last_name']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($cat['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm edit-btn" 
                                                data-id="<?= htmlspecialchars($cat['id']) ?>" 
                                                data-service-category="<?= htmlspecialchars($cat['service_category']) ?>" 
                                                data-category="<?= htmlspecialchars($cat['category']) ?>" 
                                                data-sub-category="<?= htmlspecialchars($cat['sub_category']) ?>" 
                                                data-toggle="modal" data-target="#editCategoryModal">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?= htmlspecialchars($cat['id']) ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include '../../../include/footer.php'; ?>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="index.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Service Category</label>
                        <input type="text" name="service_category" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Sub Category</label>
                        <input type="text" name="sub_category" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="index.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="form-group">
                        <label>Service Category</label>
                        <input type="text" name="service_category" id="edit_service_category" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" id="edit_category" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Sub Category</label>
                        <input type="text" name="sub_category" id="edit_sub_category" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="delete-form" action="index.php" method="POST" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="delete_id" id="delete_id">
</form>

<script>
$(function () {
    $("#categoryTable").DataTable({
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#categoryTable_wrapper .col-md-6:eq(0)');

    // Edit button handler
    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');
        var serviceCategory = $(this).data('service-category');
        var category = $(this).data('category');
        var subCategory = $(this).data('sub-category');

        $('#edit_id').val(id);
        $('#edit_service_category').val(serviceCategory);
        $('#edit_category').val(category);
        $('#edit_sub_category').val(subCategory);
    });

    // Delete button handler
    $('.delete-btn').on('click', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete_id').val(id);
                $('#delete-form').submit();
            }
        })
    });
});
</script>

</body>
</html>
