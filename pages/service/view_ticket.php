<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
include '../../include/Add_session.php';

// รับ Ticket ID
$ticket_id = $_GET['id'] ?? null;

if (!$ticket_id) {
    header('Location: index.php');
    exit;
}

// ดึงข้อมูล Ticket
try {
    $stmt = $condb->prepare("SELECT st.*,
            CONCAT(u.first_name, ' ', u.last_name) as job_owner_name,
            CONCAT(r.first_name, ' ', r.last_name) as reporter_name,
            CONCAT(cu.first_name, ' ', cu.last_name) as created_by_name,
            p.project_name
            FROM service_tickets st
            LEFT JOIN users u ON st.job_owner = u.user_id
            LEFT JOIN users r ON st.reporter = r.user_id
            LEFT JOIN users cu ON st.created_by = cu.user_id
            LEFT JOIN projects p ON st.project_id = p.project_id
            WHERE st.ticket_id = :ticket_id");
    $stmt->execute([':ticket_id' => $ticket_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        header('Location: index.php?error=ticket_not_found');
        exit;
    }

    // ดึง Timeline
    $stmtTimeline = $condb->prepare("SELECT * FROM service_ticket_timeline WHERE ticket_id = :ticket_id ORDER BY `order` ASC");
    $stmtTimeline->execute([':ticket_id' => $ticket_id]);
    $timeline = $stmtTimeline->fetchAll(PDO::FETCH_ASSOC);

    // ดึง Attachments
    $stmtAttach = $condb->prepare("SELECT * FROM service_ticket_attachments WHERE ticket_id = :ticket_id ORDER BY uploaded_at DESC");
    $stmtAttach->execute([':ticket_id' => $ticket_id]);
    $attachments = $stmtAttach->fetchAll(PDO::FETCH_ASSOC);

    // ดึง Watchers
    $stmtWatch = $condb->prepare("
        SELECT w.*, CONCAT(u.first_name, ' ', u.last_name) AS watcher_name, u.profile_image
        FROM service_ticket_watchers w
        INNER JOIN users u ON w.user_id = u.user_id
        WHERE w.ticket_id = :ticket_id
    ");
    $stmtWatch->execute([':ticket_id' => $ticket_id]);
    $watchers = $stmtWatch->fetchAll(PDO::FETCH_ASSOC);

    // ดึง Onsite Details
    $stmtOnsite = $condb->prepare("SELECT * FROM service_ticket_onsite WHERE ticket_id = :ticket_id");
    $stmtOnsite->execute([':ticket_id' => $ticket_id]);
    $onsite = $stmtOnsite->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

$menu = 'service';

// Badge colors
$statusColors = [
    'Draft' => 'secondary',
    'New' => 'primary',
    'On Process' => 'info',
    'Pending' => 'warning',
    'Resolved' => 'success',
    'Closed' => 'dark',
    'Canceled' => 'danger'
];

$priorityColors = [
    'Critical' => 'danger',
    'High' => 'warning',
    'Medium' => 'info',
    'Low' => 'secondary'
];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($ticket['ticket_no']); ?> - Service Ticket</title>
    <?php include '../../include/header.php'; ?>
    <style>
        .timeline {
            position: relative;
            padding-left: 50px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }
        .timeline-badge {
            position: absolute;
            left: -30px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            z-index: 3; /* show on top of cards */
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            pointer-events: none;
        }
        .info-row {
            display: flex;
            margin-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 0.5rem;
        }
        .info-label {
            font-weight: 600;
            width: 200px;
            color: #6c757d;
        }
        .info-value {
            flex: 1;
        }
        .timeline-item .card {
            overflow: visible;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include '../../include/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">
                                <?php echo htmlspecialchars($ticket['ticket_no']); ?>
                                <span class="badge badge-<?php echo $statusColors[$ticket['status']] ?? 'secondary'; ?> ml-2">
                                    <?php echo htmlspecialchars($ticket['status']); ?>
                                </span>
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="index.php">Service Tickets</a></li>
                                <li class="breadcrumb-item active"><?php echo htmlspecialchars($ticket['ticket_no']); ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-8">
                            <!-- Ticket Details -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>รายละเอียด Ticket</h3>
                                    <div class="card-tools">
                                        <a href="edit_ticket.php?id=<?php echo urlencode($ticket_id); ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="info-row">
                                        <div class="info-label">หัวข้อ:</div>
                                        <div class="info-value"><strong><?php echo htmlspecialchars($ticket['subject']); ?></strong></div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">รายละเอียด:</div>
                                        <div class="info-value"><?php echo nl2br(htmlspecialchars($ticket['description'] ?? '-')); ?></div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">Project:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($ticket['project_name'] ?? '-'); ?></div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">Type:</div>
                                        <div class="info-value">
                                            <span class="badge badge-info"><?php echo htmlspecialchars($ticket['ticket_type']); ?></span>
                                        </div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">Priority:</div>
                                        <div class="info-value">
                                            <span class="badge badge-<?php echo $priorityColors[$ticket['priority']] ?? 'secondary'; ?>">
                                                <?php echo htmlspecialchars($ticket['priority']); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">Urgency:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($ticket['urgency']); ?></div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">Impact:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($ticket['impact'] ?? '-'); ?></div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">Job Owner:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($ticket['job_owner_name'] ?? '-'); ?></div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">Reporter:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($ticket['reporter_name'] ?? '-'); ?></div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">Source:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($ticket['source'] ?? '-'); ?></div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">Channel:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($ticket['channel'] ?? '-'); ?></div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">Category:</div>
                                        <div class="info-value">
                                            <?php echo htmlspecialchars($ticket['service_category'] ?? '-'); ?> /
                                            <?php echo htmlspecialchars($ticket['category'] ?? '-'); ?> /
                                            <?php echo htmlspecialchars($ticket['sub_category'] ?? '-'); ?>
                                        </div>
                                    </div>

	                                    <div class="info-row">
	                                        <div class="info-label">กำหนดเริ่มดำเนินการ (วันเวลา):</div>
	                                        <div class="info-value">
	                                            <?php echo !empty($ticket['start_at']) ? date('d/m/Y H:i', strtotime($ticket['start_at'])) : '-'; ?>
	                                        </div>
	                                    </div>

	                                    <div class="info-row">
	                                        <div class="info-label">กำหนดแล้วเสร็จ (วันเวลา):</div>
	                                        <div class="info-value">
	                                            <?php echo !empty($ticket['due_at']) ? date('d/m/Y H:i', strtotime($ticket['due_at'])) : '-'; ?>
	                                        </div>
	                                    </div>


                                    <div class="info-row">
                                        <div class="info-label">SLA Target:</div>
                                        <div class="info-value">
                                            <?php echo htmlspecialchars($ticket['sla_target'] ?? '-'); ?> ชั่วโมง
                                            <?php if ($ticket['sla_deadline']): ?>
                                                <br><small class="text-muted">Deadline: <?php echo date('d/m/Y H:i', strtotime($ticket['sla_deadline'])); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">SLA Status:</div>
                                        <div class="info-value">
                                            <?php
                                            $slaColor = [
                                                'Within SLA' => 'success',
                                                'Near SLA' => 'warning',
                                                'Overdue' => 'danger'
                                            ];
                                            ?>
                                            <span class="badge badge-<?php echo $slaColor[$ticket['sla_status']] ?? 'secondary'; ?>">
                                                <?php echo htmlspecialchars($ticket['sla_status'] ?? '-'); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label">Created:</div>
                                        <div class="info-value">
                                            <?php echo date('d/m/Y H:i', strtotime($ticket['created_at'])); ?>
                                            <small class="text-muted">โดย <?php echo htmlspecialchars($ticket['created_by_name'] ?? '-'); ?></small>
                                        </div>
                                    </div>

                                    <?php if ($ticket['updated_at']): ?>
                                    <div class="info-row">
                                        <div class="info-label">Updated:</div>
                                        <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($ticket['updated_at'])); ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Onsite Details -->
                            <?php if ($onsite): ?>
                            <div class="card">
                                <div class="card-header bg-warning">
                                    <h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i>Onsite Details</h3>
                                </div>
                                <div class="card-body">
                                    <div class="info-row">
                                        <div class="info-label">สถานที่เริ่มต้น:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($onsite['start_location'] ?? '-'); ?></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">สถานที่ปลายทาง:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($onsite['end_location'] ?? '-'); ?></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">เดินทางโดย:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($onsite['travel_mode'] ?? '-'); ?></div>
                                    </div>
                                    <?php if ($onsite['odometer_start'] || $onsite['odometer_end']): ?>
                                    <div class="info-row">
                                        <div class="info-label">เลขไมล์:</div>
                                        <div class="info-value">
                                            <?php echo number_format($onsite['odometer_start'], 1); ?> → <?php echo number_format($onsite['odometer_end'], 1); ?>
                                            <strong>(ระยะทาง: <?php echo number_format($onsite['distance'], 1); ?> km)</strong>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($onsite['note']): ?>
                                    <div class="info-row">
                                        <div class="info-label">หมายเหตุ:</div>
                                        <div class="info-value"><?php echo nl2br(htmlspecialchars($onsite['note'])); ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Timeline -->
                            <div class="card">
                                <div class="card-header bg-info">
                                    <h3 class="card-title"><i class="fas fa-history mr-2"></i>Timeline</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($timeline)): ?>
                                    <div class="timeline">
                                        <?php foreach ($timeline as $item): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-badge"><?php echo $item['order']; ?></div>
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <strong><?php echo htmlspecialchars($item['actor']); ?></strong>
                                                        <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></small>
                                                    </div>
                                                    <div class="mt-2">
                                                        <p class="mb-1"><strong><?php echo htmlspecialchars($item['action']); ?></strong></p>
                                                        <?php if ($item['detail']): ?>
                                                        <p class="text-muted mb-1"><?php echo htmlspecialchars($item['detail']); ?></p>
                                                        <?php endif; ?>
                                                        <?php if ($item['location']): ?>
                                                        <small class="text-info"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($item['location']); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php else: ?>
                                    <p class="text-muted">ยังไม่มี Timeline</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-4">
                            <!-- Quick Actions -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-cogs mr-2"></i>Actions</h3>
                                </div>
                                <div class="card-body">
                                    <a href="edit_ticket.php?id=<?php echo urlencode($ticket_id); ?>" class="btn btn-primary btn-block">
                                        <i class="fas fa-edit"></i> แก้ไข Ticket
                                    </a>
                                    <button class="btn btn-success btn-block" onclick="updateStatus('Resolved')">
                                        <i class="fas fa-check"></i> Resolve Ticket
                                    </button>
                                    <button class="btn btn-dark btn-block" onclick="updateStatus('Closed')">
                                        <i class="fas fa-lock"></i> Close Ticket
                                    </button>
                                    <a href="index.php" class="btn btn-secondary btn-block">
                                        <i class="fas fa-arrow-left"></i> กลับรายการ
                                    </a>
                                </div>
                            </div>

                            <!-- Watchers -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-users mr-2"></i>Watchers (<?php echo count($watchers); ?>)</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($watchers)): ?>
                                        <?php foreach ($watchers as $watcher): ?>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-user-circle fa-2x text-primary mr-2"></i>
                                            <div>
                                                <div><?php echo htmlspecialchars($watcher['watcher_name']); ?></div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted">ไม่มี Watchers</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Attachments -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-paperclip mr-2"></i>Attachments (<?php echo count($attachments); ?>)</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($attachments)): ?>
                                        <?php foreach ($attachments as $file): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                            <div>
                                                <i class="fas fa-file text-info mr-2"></i>
                                                <small><?php echo htmlspecialchars($file['file_name']); ?></small>
                                            </div>
                                            <a href="<?php echo htmlspecialchars($file['file_path']); ?>" class="btn btn-sm btn-primary" download>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted">ไม่มีไฟล์แนบ</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../../include/footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function updateStatus(newStatus) {
            Swal.fire({
                title: 'ยืนยันการเปลี่ยนสถานะ?',
                text: 'คุณต้องการเปลี่ยนสถานะเป็น ' + newStatus + ' ใช่หรือไม่?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ใช่, เปลี่ยนเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
                    formData.append('ticket_id', '<?php echo $ticket_id; ?>');
                    formData.append('status', newStatus);

                    fetch('api/update_ticket.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: 'อัพเดตสถานะเรียบร้อยแล้ว'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: error.message
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>
