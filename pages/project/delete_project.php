<?php
include '../../include/Add_session.php';

header('Content-Type: application/json');

// ตรวจสอบสิทธิ์การเข้าถึง
$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';
$team_ids = $_SESSION['team_ids'] ?? [];

// ตรวจสอบว่าผู้ใช้มีสิทธิ์ลบโครงการหรือไม่
if (!in_array($role, ['Executive', 'Account Management', 'Sale Supervisor', 'Seller'])) {
    echo json_encode(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ในการลบโครงการ']);
    exit;
}

$project_id = isset($_GET['project_id']) ? htmlspecialchars($_GET['project_id']) : '';
$project_id = decryptUserId($project_id);

if (!$project_id) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบโครงการที่ต้องการลบ']);
    exit;
}

try {
    // สร้างเงื่อนไขการลบตาม role
    $delete_conditions = [];
    $params = [':project_id' => $project_id];

    switch ($role) {
        case 'Executive':
            // Executive ลบได้ทุกโครงการ
            $delete_conditions[] = "1=1";
            break;

        case 'Account Management':
            // Account Management ลบได้เฉพาะโครงการในทีมที่สังกัด
            if (!empty($team_ids)) {
                $team_placeholders = [];
                foreach ($team_ids as $key => $team_id) {
                    $placeholder = ":team_id_$key";
                    $team_placeholders[] = $placeholder;
                    $params[$placeholder] = $team_id;
                }
                $team_condition = implode(',', $team_placeholders);
                $delete_conditions[] = "seller IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id IN ($team_condition))";
            } else {
                // หากไม่มีทีม ให้ลบได้เฉพาะที่ตัวเองเป็นเจ้าของ
                $delete_conditions[] = "(created_by = :user_id OR seller = :user_id)";
                $params[':user_id'] = $user_id;
            }
            break;

        case 'Sale Supervisor':
            // Sale Supervisor ลบได้เฉพาะโครงการที่ตัวเองเป็นเจ้าของ หรือโครงการในทีมที่ดูแล
            if (!empty($team_ids)) {
                $team_placeholders = [];
                foreach ($team_ids as $key => $team_id) {
                    $placeholder = ":team_id_$key";
                    $team_placeholders[] = $placeholder;
                    $params[$placeholder] = $team_id;
                }
                $team_condition = implode(',', $team_placeholders);
                $delete_conditions[] = "(created_by = :user_id OR (seller IN (SELECT ut.user_id FROM user_teams ut WHERE ut.team_id IN ($team_condition)) AND created_by = :user_id))";
                $params[':user_id'] = $user_id;
            } else {
                $delete_conditions[] = "(created_by = :user_id OR seller = :user_id)";
                $params[':user_id'] = $user_id;
            }
            break;

        case 'Seller':
        default:
            // Seller ลบได้เฉพาะโครงการที่ตัวเองเป็นเจ้าของ
            $delete_conditions[] = "(created_by = :user_id OR seller = :user_id)";
            $params[':user_id'] = $user_id;
            break;
    }

    // ตรวจสอบว่ามีสิทธิ์ลบโครงการนี้หรือไม่ก่อน
    $check_sql = "SELECT COUNT(*) FROM projects WHERE project_id = :project_id AND (" . implode(' OR ', $delete_conditions) . ")";
    $check_stmt = $condb->prepare($check_sql);
    $check_stmt->execute($params);
    $can_delete = $check_stmt->fetchColumn() > 0;

    if (!$can_delete) {
        echo json_encode(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ลบโครงการนี้']);
        exit;
    }

    // ลบข้อมูลในตารางที่เกี่ยวข้องก่อน
    $sql_delete_documents = "DELETE FROM project_documents WHERE project_id = :project_id";
    $stmt_documents = $condb->prepare($sql_delete_documents);
    $stmt_documents->bindParam(':project_id', $project_id);
    $stmt_documents->execute();

    // ลบข้อมูลใน projects
    $sql = "DELETE FROM projects WHERE project_id = :project_id AND (" . implode(' OR ', $delete_conditions) . ")";
    $stmt = $condb->prepare($sql);

    if ($stmt->execute($params)) {
        echo json_encode(['status' => 'success', 'message' => 'ลบโครงการสำเร็จ']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด! ไม่สามารถลบโครงการได้']);
    }
} catch (PDOException $e) {
    // ตรวจสอบ error code ของ PDOException
    if ($e->getCode() == 23000) { // 23000 คือรหัสข้อผิดพลาดเกี่ยวกับ Foreign Key Constraint
        echo json_encode([
            'status' => 'error',
            'message' => 'ไม่สามารถลบโครงการได้ เนื่องจากมีไฟล์แนบ, เอกสาร, รูปภาพ หรือข้อมูลอื่นๆที่เกี่ยวข้องกับโครงการนี้อยู่',
        ]);
    } else {
        // แจ้งข้อผิดพลาดอื่น ๆ ที่ไม่ใช่ Foreign Key Constraint
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
}
