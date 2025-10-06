<?php
/**
 * API: Update Service Ticket
 * Method: POST
 * Input: JSON/FormData
 */

// เริ่ม session
session_start();

// เชื่อมต่อฐานข้อมูล (แก้ path ให้ถูกต้อง)
include '../../../config/condb.php';

// ตรวจสอบ session
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized. Please login first.'
    ]);
    exit;
}

// ตั้งค่า header สำหรับ JSON response
header('Content-Type: application/json; charset=utf-8');

// ตรวจสอบ HTTP Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method Not Allowed. Use POST only.'
    ]);
    exit;
}

// ตรวจสอบ CSRF Token
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid CSRF token'
    ]);
    exit;
}

try {
    // รับข้อมูลจาก POST
    $ticket_id = $_POST['ticket_id'] ?? null;

    if (empty($ticket_id)) {
        throw new Exception('ไม่พบ Ticket ID');
    }

    // ตรวจสอบว่า Ticket มีอยู่จริง
    $checkStmt = $condb->prepare("SELECT * FROM service_tickets WHERE ticket_id = :ticket_id");
    $checkStmt->execute([':ticket_id' => $ticket_id]);
    $existingTicket = $checkStmt->fetch();

    if (!$existingTicket) {
        throw new Exception('ไม่พบ Ticket นี้');
    }

    // เริ่ม Transaction
    $condb->beginTransaction();

    // สร้าง array สำหรับเก็บฟิลด์ที่ต้อง UPDATE
    $updateFields = [];
    $params = [':ticket_id' => $ticket_id];

    // ตรวจสอบและเพิ่มฟิลด์ที่ต้องการอัพเดต
    $allowedFields = [
        'ticket_type',
        'project_id',
        'subject',
        'description',
        'status',
        'priority',
        'urgency',
        'impact',
        'service_category',
        'category',
        'sub_category',
        'job_owner',
        'reporter',
        'source',
        'channel',
        'sla_status',
        'start_at',
        'due_at'
    ];

    $validTicketTypes = ['Incident', 'Service', 'Change'];
    $validSlaStatuses = ['Within SLA', 'Near SLA', 'Overdue'];

    foreach ($allowedFields as $field) {
        if (!array_key_exists($field, $_POST)) {
            continue;
        }

        $value = $_POST[$field];
        if (is_string($value)) {
            $value = trim($value);
        }

        if ($field === 'ticket_type') {
            if ($value === '' || !in_array($value, $validTicketTypes, true)) {
                throw new Exception('Ticket Type ไม่ถูกต้อง');
            }
        }

        if ($field === 'sla_status' && $value !== '' && !in_array($value, $validSlaStatuses, true)) {
            throw new Exception('SLA Status ไม่ถูกต้อง');
        }

        if ($field === 'project_id' && $value === '') {
            $value = null;
        }

        if (in_array($field, ['service_category', 'category', 'sub_category', 'source'], true) && $value === '') {
            $value = null;
        }

        if (in_array($field, ['start_at', 'due_at'], true)) {
            if ($value === '') {
                $value = null;
            } else {
                $timestamp = strtotime($value);
                $value = $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
            }
        }

        $updateFields[] = "$field = :$field";
        $params[":$field"] = $value;
    }


    // Recompute SLA target if priority/urgency/impact changed
    if (isset($_POST['priority']) || isset($_POST['urgency']) || isset($_POST['impact'])) {
        require_once __DIR__ . '/../sla_helpers.php';
        $newPriority = $_POST['priority'] ?? $existingTicket['priority'];
        $newUrgency  = $_POST['urgency']  ?? $existingTicket['urgency'];
        $newImpact   = $_POST['impact']   ?? $existingTicket['impact'];
        $newSlaTarget = computeSlaTarget($condb, $newPriority, $newUrgency, $newImpact);
        $updateFields[] = "sla_target = :sla_target";
        $params[':sla_target'] = $newSlaTarget;

        if (!empty($newSlaTarget) && !empty($existingTicket['created_at'])) {
            $createdAt = new DateTime($existingTicket['created_at']);
            $createdAt->modify('+' . (int)$newSlaTarget . ' hour');
            $updateFields[] = "sla_deadline = :sla_deadline";
            $params[':sla_deadline'] = $createdAt->format('Y-m-d H:i:s');
        }
    }

    // เพิ่ม updated_by และ updated_at
    $updateFields[] = "updated_by = :updated_by";
    $updateFields[] = "updated_at = NOW()";
    $params[':updated_by'] = $_SESSION['user_id'];

    // สร้าง SQL UPDATE
    if (!empty($updateFields)) {
        $sql = "UPDATE service_tickets SET " . implode(', ', $updateFields) . " WHERE ticket_id = :ticket_id";
        $stmt = $condb->prepare($sql);
        $stmt->execute($params);
    }

    // อัพเดต Watchers
    if (array_key_exists('watchers', $_POST)) {
        $watchers = $_POST['watchers'];
        if (!is_array($watchers)) {
            $watchers = ($watchers === '' || $watchers === null) ? [] : [$watchers];
        }

        $sqlDeleteWatchers = "DELETE FROM service_ticket_watchers WHERE ticket_id = :ticket_id";
        $stmtDeleteWatchers = $condb->prepare($sqlDeleteWatchers);
        $stmtDeleteWatchers->execute([':ticket_id' => $ticket_id]);

        if (!empty($watchers)) {
            $sqlWatcher = "INSERT INTO service_ticket_watchers (watcher_id, ticket_id, user_id, added_by)
                           VALUES (UUID(), :ticket_id, :user_id, :added_by)";
            $stmtWatcher = $condb->prepare($sqlWatcher);

            foreach ($watchers as $watcher_id) {
                if (!empty($watcher_id)) {
                    $stmtWatcher->execute([
                        ':ticket_id' => $ticket_id,
                        ':user_id' => $watcher_id,
                        ':added_by' => $_SESSION['user_id']
                    ]);
                }
            }
        }
    }

    // อัพเดต Onsite Details (ถ้ามี)
    if (isset($_POST['channel']) && $_POST['channel'] === 'Onsite') {
        // ตรวจสอบว่ามี record Onsite อยู่แล้วหรือไม่
        $checkOnsite = $condb->prepare("SELECT onsite_id FROM service_ticket_onsite WHERE ticket_id = :ticket_id");
        $checkOnsite->execute([':ticket_id' => $ticket_id]);
        $existingOnsite = $checkOnsite->fetch();

        $onsiteFields = [
            'start_location' => $_POST['onsite_start_location'] ?? null,
            'end_location' => $_POST['onsite_end_location'] ?? null,
            'travel_mode' => $_POST['onsite_travel_mode'] ?? null,
            'travel_note' => $_POST['onsite_travel_note'] ?? null,
            'odometer_start' => !empty($_POST['onsite_odometer_start']) ? floatval($_POST['onsite_odometer_start']) : null,
            'odometer_end' => !empty($_POST['onsite_odometer_end']) ? floatval($_POST['onsite_odometer_end']) : null,
            'note' => $_POST['onsite_note'] ?? null
        ];

        if ($existingOnsite) {
            // UPDATE
            $onsiteUpdate = [];
            $onsiteParams = [':ticket_id' => $ticket_id];

            foreach ($onsiteFields as $key => $value) {
                $onsiteUpdate[] = "$key = :$key";
                $onsiteParams[":$key"] = $value;
            }

            $sqlOnsite = "UPDATE service_ticket_onsite SET " . implode(', ', $onsiteUpdate) . ", updated_at = NOW() WHERE ticket_id = :ticket_id";
            $stmtOnsite = $condb->prepare($sqlOnsite);
            $stmtOnsite->execute($onsiteParams);
        } else {
            // INSERT
            $sqlOnsite = "INSERT INTO service_ticket_onsite (
                onsite_id, ticket_id, start_location, end_location,
                travel_mode, travel_note, odometer_start, odometer_end, note
            ) VALUES (
                UUID(), :ticket_id, :start_location, :end_location,
                :travel_mode, :travel_note, :odometer_start, :odometer_end, :note
            )";

            $stmtOnsite = $condb->prepare($sqlOnsite);
            $stmtOnsite->execute([
                ':ticket_id' => $ticket_id,
                ':start_location' => $onsiteFields['start_location'],
                ':end_location' => $onsiteFields['end_location'],
                ':travel_mode' => $onsiteFields['travel_mode'],
                ':travel_note' => $onsiteFields['travel_note'],
                ':odometer_start' => $onsiteFields['odometer_start'],
                ':odometer_end' => $onsiteFields['odometer_end'],
                ':note' => $onsiteFields['note']
            ]);
        }
    }

    // เพิ่ม Timeline Entry สำหรับการแก้ไข Ticket
    // ดึง order ล่าสุดจาก Timeline
    $sqlMaxOrder = "SELECT COALESCE(MAX(`order`), 0) as max_order FROM service_ticket_timeline WHERE ticket_id = :ticket_id";
    $stmtMaxOrder = $condb->prepare($sqlMaxOrder);
    $stmtMaxOrder->execute([':ticket_id' => $ticket_id]);
    $maxOrderResult = $stmtMaxOrder->fetch(PDO::FETCH_ASSOC);
    $nextOrder = $maxOrderResult['max_order'] + 1;

    // ดึงชื่อ User ที่แก้ไข
    $sqlUser = "SELECT CONCAT(first_name, ' ', last_name) as full_name FROM users WHERE user_id = :user_id";
    $stmtUser = $condb->prepare($sqlUser);
    $stmtUser->execute([':user_id' => $_SESSION['user_id']]);
    $userData = $stmtUser->fetch(PDO::FETCH_ASSOC);
    $actorName = $userData['full_name'] ?? 'Unknown';

    // สร้าง detail สำหรับ Timeline (เฉพาะฟิลด์ที่เปลี่ยนจริง)
        $fieldLabels = [
        'ticket_type' => 'Ticket Type',
        'project_id' => 'Project',
        'subject' => 'หัวข้อ / Subject',
        'description' => 'รายละเอียด',
        'status' => 'สถานะ',
        'priority' => 'Priority',
        'urgency' => 'Urgency',
        'impact' => 'Impact',
        'service_category' => 'Service Category',
        'category' => 'Category',
        'sub_category' => 'Sub Category',
        'job_owner' => 'Job Owner',
        'reporter' => 'ผู้แจ้ง',
        'source' => 'Ticket Source',
        'channel' => 'Channel',
        'sla_status' => 'SLA Status',
        'start_at' => 'วันเริ่มดำเนินการ',
        'due_at' => 'วันครบกำหนด'
    ];

    $normalize = function($v, $field) {
        if ($v === '' || $v === null) return null;
        if (in_array($field, ['start_at','due_at'])) {
            // รับทั้งรูปแบบ Y-m-d H:i และอื่นๆ แล้ว normalize เป็น Y-m-d H:i:s
            $ts = strtotime($v);
            return $ts ? date('Y-m-d H:i:s', $ts) : null;
        }
        return is_string($v) ? trim($v) : $v;
    };

    $userNameById = function($id) use ($condb) {
        if (!$id) return '-';
        $stmt = $condb->prepare("SELECT CONCAT(first_name,' ',last_name) AS n FROM users WHERE user_id = :id");
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r['n'] ?? $id;
    };

    $projectNameById = function($id) use ($condb) {
        if (!$id) {
            return '-';
        }
        static $cache = [];
        if (isset($cache[$id])) {
            return $cache[$id];
        }
        $stmt = $condb->prepare("SELECT project_name FROM projects WHERE project_id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $cache[$id] = $row['project_name'] ?? $id;
        return $cache[$id];
    };

    $pretty = function($v, $field) use ($userNameById, $projectNameById) {
        if ($v === null) return '-';
        if (in_array($field, ['start_at','due_at'], true)) {
            $ts = strtotime($v);
            return $ts ? date('d/m/Y H:i', $ts) : '-';
        }
        if (in_array($field, ['job_owner','reporter'], true)) {
            return $userNameById($v);
        }
        if ($field === 'project_id') {
            return $projectNameById($v);
        }
        return (string)$v;
    };

    $changes = [];
    foreach ($allowedFields as $field) {
        if (!array_key_exists($field, $_POST)) continue; // อัปเดตเฉพาะฟิลด์ที่ส่งมา
        $old = $normalize($existingTicket[$field] ?? null, $field);
        $new = $normalize($_POST[$field], $field);
        if ($old === $new) continue; // ไม่มีการเปลี่ยน
        $label = $fieldLabels[$field] ?? $field;
        $changes[] = $label . ': ' . $pretty($old, $field) . ' → ' . $pretty($new, $field);
    }

    if (!empty($changes)) {
        $detail = 'เปลี่ยนแปลง: ' . implode('; ', $changes);

        // Insert Timeline Entry เฉพาะเมื่อมีการเปลี่ยนแปลงจริง
        $sqlTimeline = "INSERT INTO service_ticket_timeline (
            timeline_id, ticket_id, `order`, actor, action, detail, location, created_at
        ) VALUES (
            UUID(), :ticket_id, :order, :actor, :action, :detail, :location, NOW()
        )";

        $stmtTimeline = $condb->prepare($sqlTimeline);
        $stmtTimeline->execute([
            ':ticket_id' => $ticket_id,
            ':order' => $nextOrder,
            ':actor' => $actorName,
            ':action' => 'แก้ไข Ticket',
            ':detail' => $detail,
            ':location' => null
        ]);
    }

    // Commit Transaction
    $condb->commit();

    // Success Response
    echo json_encode([
        'success' => true,
        'message' => 'อัพเดต Ticket สำเร็จ',
        'data' => [
            'ticket_id' => $ticket_id
        ]
    ]);

} catch (PDOException $e) {
    // Rollback on error
    if ($condb->inTransaction()) {
        $condb->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database Error: ' . $e->getMessage()
    ]);

} catch (Exception $e) {
    // Rollback on error
    if ($condb->inTransaction()) {
        $condb->rollBack();
    }

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
