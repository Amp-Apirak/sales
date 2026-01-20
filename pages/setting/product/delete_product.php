<?php
// เริ่มต้น session และเชื่อมต่อฐานข้อมูล
session_start();
include('../../../config/condb.php');

// ตรวจสอบการตั้งค่า Session เพื่อป้องกันกรณีที่ไม่ได้ล็อกอิน
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

// ดึงข้อมูลจาก session
$role = $_SESSION['role'];
$team_id = $_SESSION['team_id'];
$user_id = $_SESSION['user_id'];

// ตรวจสอบว่ามี product_id ถูกส่งมาหรือไม่
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    $_SESSION['error_message'] = "ไม่พบ ID ของ Product ที่ต้องการลบ";
    header("Location: product.php");
    exit();
}

// รับ product_id จาก URL และทำการถอดรหัส
$product_id = decryptUserId($_GET['product_id']);

try {
    // เริ่ม transaction
    $condb->beginTransaction();

    // ดึงข้อมูล Product ก่อนลบ เพื่อตรวจสอบสิทธิ์และไฟล์ที่เกี่ยวข้อง
    $select_sql = "SELECT p.*, u.team_id, u.first_name, u.last_name
                   FROM products p
                   INNER JOIN users u ON p.created_by = u.user_id
                   WHERE p.product_id = :product_id";
    $select_stmt = $condb->prepare($select_sql);
    $select_stmt->bindParam(':product_id', $product_id);
    $select_stmt->execute();
    $product = $select_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        throw new Exception("ไม่พบ Product ที่ต้องการลบ");
    }

    // ตรวจสอบสิทธิ์การลบ
    $is_creator = ($product['created_by'] == $user_id);
    $is_executive = ($role == 'Executive');
    $is_sale_supervisor_or_seller = ($role == 'Sale Supervisor' || $role == 'Seller');
    $is_same_team = ($product['team_id'] == $team_id);

    $can_delete = ($is_creator || $is_executive || ($is_sale_supervisor_or_seller && $is_same_team));

    if (!$can_delete) {
        throw new Exception("คุณไม่มีสิทธิ์ในการลบ Product นี้");
    }

    // ตรวจสอบว่า Product นี้ถูกใช้งานใน Project หรือไม่
    $check_usage_sql = "SELECT COUNT(*) as project_count FROM projects WHERE product_id = :product_id";
    $check_stmt = $condb->prepare($check_usage_sql);
    $check_stmt->bindParam(':product_id', $product_id);
    $check_stmt->execute();
    $usage_result = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if ($usage_result['project_count'] > 0) {
        throw new Exception("ไม่สามารถลบ Product นี้ได้ เนื่องจากมีการใช้งานใน " . $usage_result['project_count'] . " โครงการ");
    }

    // ลบไฟล์ภาพหลัก (main_image)
    if (!empty($product['main_image'])) {
        $main_image_path = '../../../uploads/product_images/' . $product['main_image'];
        if (file_exists($main_image_path)) {
            unlink($main_image_path);
        }
    }

    // ลบไฟล์ภาพเพิ่มเติม (additional_images) ถ้ามี
    if (!empty($product['additional_images'])) {
        $additional_images = json_decode($product['additional_images'], true);
        if (is_array($additional_images)) {
            foreach ($additional_images as $image) {
                $image_path = '../../../uploads/product_images/' . $image;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }
    }

    // ลบเอกสารที่เกี่ยวข้องกับ Product
    $documents_sql = "SELECT file_path FROM product_documents WHERE product_id = :product_id";
    $documents_stmt = $condb->prepare($documents_sql);
    $documents_stmt->bindParam(':product_id', $product_id);
    $documents_stmt->execute();
    $documents = $documents_stmt->fetchAll(PDO::FETCH_ASSOC);

    // ลบไฟล์เอกสารจากระบบไฟล์
    foreach ($documents as $doc) {
        if (file_exists($doc['file_path'])) {
            unlink($doc['file_path']);
        }
    }

    // ลบข้อมูลเอกสารจากฐานข้อมูล
    $delete_documents_sql = "DELETE FROM product_documents WHERE product_id = :product_id";
    $delete_documents_stmt = $condb->prepare($delete_documents_sql);
    $delete_documents_stmt->bindParam(':product_id', $product_id);
    $delete_documents_stmt->execute();

    // ลบ Product จากฐานข้อมูล
    $delete_product_sql = "DELETE FROM products WHERE product_id = :product_id";
    $delete_product_stmt = $condb->prepare($delete_product_sql);
    $delete_product_stmt->bindParam(':product_id', $product_id);

    if ($delete_product_stmt->execute()) {
        // Commit transaction
        $condb->commit();

        // บันทึก Log กิจกรรม (ถ้าต้องการ)
        // logActivity($user_id, 'delete_product', "Deleted product: " . $product['product_name'] . " (ID: $product_id)");

        // สร้าง Session สำหรับแสดงข้อความสำเร็จ
        $_SESSION['success_message'] = "ลบ Product '" . $product['product_name'] . "' สำเร็จ";
    } else {
        throw new Exception("ไม่สามารถลบ Product จากฐานข้อมูลได้");
    }
} catch (Exception $e) {
    // Rollback transaction ในกรณีที่เกิดข้อผิดพลาด
    $condb->rollBack();
    $_SESSION['error_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
}

// Redirect กลับไปยังหน้า product.php
header("Location: product.php");
exit();
