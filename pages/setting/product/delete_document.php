<?php
// เริ่มต้น session และเชื่อมต่อฐานข้อมูล
session_start();
include('../../../config/condb.php');

// เพิ่มต่อจากฟังก์ชัน clean_input
function generateUUID()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // เวอร์ชัน 4 UUID
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // UUID variant
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Executive' && $_SESSION['role'] != 'Sale Supervisor' && $_SESSION['role'] != 'Seller')) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

// ตรวจสอบว่ามี ID ถูกส่งมาหรือไม่
if (isset($_GET['id'])) {
    $document_id = $_GET['id'];

    try {
        // เริ่ม transaction
        $condb->beginTransaction();

        // ดึงข้อมูลเอกสารก่อนลบ
        $select_sql = "SELECT file_path, file_name, product_id FROM product_documents WHERE id = :id";
        $select_stmt = $condb->prepare($select_sql);
        $select_stmt->bindParam(':id', $document_id);
        $select_stmt->execute();
        $document = $select_stmt->fetch();

        if ($document) {
            // ลบไฟล์จากระบบไฟล์
            if (file_exists($document['file_path'])) {
                unlink($document['file_path']);
            }

            // ลบข้อมูลจากฐานข้อมูล
            $delete_sql = "DELETE FROM product_documents WHERE id = :id";
            $delete_stmt = $condb->prepare($delete_sql);
            $delete_stmt->bindParam(':id', $document_id);

            if ($delete_stmt->execute()) {

                // Commit transaction
                $condb->commit();

                // สร้าง Session สำหรับแสดงข้อความสำเร็จ
                $_SESSION['success_message'] = "ลบเอกสารสำเร็จ";
            } else {
                throw new Exception("ไม่สามารถลบข้อมูลจากฐานข้อมูลได้");
            }
        } else {
            $_SESSION['error_message'] = "ไม่พบเอกสารที่ต้องการลบ";
        }
    } catch (Exception $e) {
        // Rollback transaction ในกรณีที่เกิดข้อผิดพลาด
        $condb->rollBack();
        $_SESSION['error_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }

    // ดึง URL ของหน้าก่อนหน้า
    $previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'product.php';

    // Redirect กลับไปยังหน้าก่อนหน้า
    header("Location: " . $previous_page);
    exit();
} else {
    $_SESSION['error_message'] = "ไม่พบ ID ของเอกสาร";
    header("Location: product.php");
    exit();
}
