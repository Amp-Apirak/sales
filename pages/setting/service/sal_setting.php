<?php
require_once __DIR__ . '/../../../include/Add_session.php';
require_once __DIR__ . '/../../../config/condb.php';
require_once __DIR__ . '/../../../config/validation.php';

$menu = 'sla_setting';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Executive') {
    header('HTTP/1.1 403 Forbidden');
    echo 'Forbidden';
    exit;
}

$errors = [];
$messages = [];

// Ensure per-impact time matrix table exists (idempotent)
try {
    $condb->exec("CREATE TABLE IF NOT EXISTS `service_sla_time_matrix` (
      `id` char(36) NOT NULL,
      `impact_id` char(36) NOT NULL,
      `urgency` enum('High','Medium','Low') NOT NULL,
      `priority` enum('Critical','High','Medium','Low') NOT NULL,
      `sla_hours` int(11) NOT NULL,
      `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uniq_impact_priority_urgency` (`impact_id`,`priority`,`urgency`),
      CONSTRAINT `fk_time_matrix_impact` FOREIGN KEY (`impact_id`) REFERENCES `service_sla_impacts` (`impact_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (Exception $e) {
    // ignore if lack privilege, page can still function without creating
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    } else {
        $action = $_POST['action'] ?? '';
        try {
            if ($action === 'update_targets') {
                $targets = $_POST['targets'] ?? [];
                foreach (['Critical','High','Medium','Low'] as $p) {
                    if (isset($targets[$p])) {
                        $hours = max(1, intval($targets[$p]));
                        $stmt = $condb->prepare("INSERT INTO service_sla_targets (id, priority, sla_hours)
                                                 VALUES (UUID(), :p, :h)
                                                 ON DUPLICATE KEY UPDATE sla_hours = VALUES(sla_hours)");
                        $stmt->execute([':p' => $p, ':h' => $hours]);
                    }
                }
                $messages[] = 'Updated SLA targets.';
            } elseif ($action === 'update_matrix') {
                $matrix = $_POST['matrix'] ?? [];
                foreach ($matrix as $impact_id => $cols) {
                    foreach (['High','Medium','Low'] as $urg) {
                        if (!empty($cols[$urg])) {
                            $priority = $cols[$urg];
                            $stmt = $condb->prepare("INSERT INTO service_sla_priority_matrix (id, impact_id, urgency, priority)
                                                     VALUES (UUID(), :iid, :u, :p)
                                                     ON DUPLICATE KEY UPDATE priority = VALUES(priority)");
                            $stmt->execute([':iid' => $impact_id, ':u' => $urg, ':p' => $priority]);
                        }
                    }
                }
                $messages[] = 'Updated priority matrix.';
            } elseif ($action === 'update_time_matrix') {
                $tm = $_POST['time_matrix'] ?? [];
                foreach ($tm as $impact_id => $byUrg) {
                    foreach (['High','Medium','Low'] as $u) {
                        if (!isset($byUrg[$u]) || !is_array($byUrg[$u])) continue;
                        foreach (['Critical','High','Medium','Low'] as $p) {
                            if ($byUrg[$u][$p] === '' || !isset($byUrg[$u][$p])) continue;
                            $hours = max(1, intval($byUrg[$u][$p]));
                            $stmt = $condb->prepare("INSERT INTO service_sla_time_matrix (id, impact_id, urgency, priority, sla_hours)
                                                     VALUES (UUID(), :iid, :u, :p, :h)
                                                     ON DUPLICATE KEY UPDATE sla_hours = VALUES(sla_hours)");
                            $stmt->execute([':iid'=>$impact_id, ':u'=>$u, ':p'=>$p, ':h'=>$hours]);
                        }
                    }
                }
                $messages[] = 'Updated per-impact SLA time matrix.';
            } elseif ($action === 'add_impact') {
                $name  = trim($_POST['impact_name'] ?? '');
                $level = $_POST['impact_level'] ?? 'Medium';
                if ($name === '') { throw new Exception('Impact name is required'); }
                $stmt = $condb->prepare("INSERT INTO service_sla_impacts (impact_id, impact_name, impact_level, active)
                                         VALUES (UUID(), :n, :l, 1)");
                $stmt->execute([':n' => $name, ':l' => $level]);
                $messages[] = 'Added impact.';
            } elseif ($action === 'toggle_impact') {
                $iid = $_POST['impact_id'] ?? '';
                $active = intval($_POST['active'] ?? 1) ? 1 : 0;
                $stmt = $condb->prepare("UPDATE service_sla_impacts SET active = :a WHERE impact_id = :iid");
                $stmt->execute([':a' => $active, ':iid' => $iid]);
                $messages[] = 'Impact status updated.';
                } elseif ($action === 'delete_impact') {
                    $iid = $_POST['impact_id'] ?? '';
                    if (!empty($iid)) {
                        $stmt = $condb->prepare("DELETE FROM service_sla_impacts WHERE impact_id = :iid");
                        $stmt->execute([':iid' => $iid]);
                        $messages[] = 'Impact deleted.';
                    }

            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

// Fetch current data
$targets = [];
$stmt = $condb->query("SELECT priority, sla_hours FROM service_sla_targets");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $targets[$row['priority']] = (int)$row['sla_hours'];
}
$priorities = ['Critical','High','Medium','Low'];
foreach ($priorities as $p) {
    if (!isset($targets[$p])) { $targets[$p] = ($p==='Critical'?4:($p==='High'?8:($p==='Medium'?24:72))); }
}

$impacts = [];
$stmt = $condb->query("SELECT impact_id, impact_name, impact_level, active FROM service_sla_impacts ORDER BY impact_name");
$impacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$matrix = [];
$stmt = $condb->query("SELECT impact_id, urgency, priority FROM service_sla_priority_matrix");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $matrix[$row['impact_id']][$row['urgency']] = $row['priority'];
}
$timeMatrix = [];
$stmt = $condb->query("SELECT impact_id, urgency, priority, sla_hours FROM service_sla_time_matrix");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $timeMatrix[$row['impact_id']][$row['urgency']][$row['priority']] = (int)$row['sla_hours'];
}


$csrf = generateCSRFToken();

?>
<!DOCTYPE html>
<html lang="en">
<?php $menu = 'sla_setting'; ?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalePipeline | SLA Settings</title>
    <?php include __DIR__ . '/../../../include/Header.php'; ?>
</head>
<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include __DIR__ . '/../../../include/Navbar.php'; ?>

        <div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-clock"></i> SLA Settings</h1>
                </div>
            </div>
            <?php if ($errors): ?>
                <div class="alert alert-danger"><?php echo escapeOutput(implode(' | ', $errors)); ?></div>
            <?php endif; ?>
            <?php if ($messages): ?>
                <div class="alert alert-success"><?php echo escapeOutput(implode(' | ', $messages)); ?></div>
            <?php endif; ?>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header"><h3 class="card-title">SLA Targets by Priority (hours)</h3></div>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrf); ?>">
                            <input type="hidden" name="action" value="update_targets">
                            <div class="card-body">
                                <?php foreach ($priorities as $p): ?>
                                    <div class="form-group">
                                        <label><?php echo escapeOutput($p); ?></label>
                                        <input type="number" min="1" step="1" class="form-control" name="targets[<?php echo escapeOutput($p); ?>]" value="<?php echo (int)$targets[$p]; ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Targets</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-secondary">
                        <div class="card-header"><h3 class="card-title">Manage Impacts</h3></div>
                        <div class="card-body">
                            <form method="post" class="form-inline mb-3">
                                <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrf); ?>">
                                <input type="hidden" name="action" value="add_impact">
                                <div class="form-group mr-2">
                                    <input type="text" name="impact_name" class="form-control" placeholder="New impact name" required>
                                </div>
                                <div class="form-group mr-2">
                                    <select name="impact_level" class="form-control">
                                        <option>High</option>
                                        <option selected>Medium</option>
                                        <option>Low</option>
                                    </select>
                                </div>
                                <button class="btn btn-success" type="submit"><i class="fas fa-plus"></i> Add</button>
                            </form>

                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Level</th>
                                        <th>Active</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($impacts as $imp): ?>
                                    <tr>
                                        <td><?php echo escapeOutput($imp['impact_name']); ?></td>
                                        <td><?php echo escapeOutput($imp['impact_level']); ?></td>
                                        <td><?php echo $imp['active'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>'; ?></td>
                                        <td>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrf); ?>">
                                                <input type="hidden" name="action" value="toggle_impact">
                                                <input type="hidden" name="impact_id" value="<?php echo escapeOutput($imp['impact_id']); ?>">
                                                <input type="hidden" name="active" value="<?php echo $imp['active'] ? '0' : '1'; ?>">
                                                <button class="btn btn-sm <?php echo $imp['active'] ? 'btn-warning' : 'btn-success'; ?>" type="submit">
                                                    <?php echo $imp['active'] ? 'Disable' : 'Enable'; ?>
                                                </button>
                                            </form>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Delete this impact? This will remove its matrix rules as well.');">
                                                <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrf); ?>">
                                                <input type="hidden" name="action" value="delete_impact">
                                                <input type="hidden" name="impact_id" value="<?php echo escapeOutput($imp['impact_id']); ?>">
                                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header"><h3 class="card-title">Priority Matrix (Impact × Urgency → Priority)</h3></div>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrf); ?>">
                            <input type="hidden" name="action" value="update_matrix">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Impact</th>
                                            <th>Urgency: High</th>
                                            <th>Urgency: Medium</th>
                                            <th>Urgency: Low</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($impacts as $imp): ?>
                                        <tr>
                                            <td><?php echo escapeOutput($imp['impact_name']); ?></td>
                                            <?php foreach (['High','Medium','Low'] as $u): ?>
                                                <td>
                                                    <select class="form-control form-control-sm" name="matrix[<?php echo escapeOutput($imp['impact_id']); ?>][<?php echo $u; ?>]">
                                                        <?php foreach ($priorities as $p): ?>
                                                            <option value="<?php echo $p; ?>" <?php echo (isset($matrix[$imp['impact_id']][$u]) && $matrix[$imp['impact_id']][$u] === $p) ? 'selected' : ''; ?>><?php echo $p; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Matrix</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-warning">
                        <div class="card-header"><h3 class="card-title">SLA Duration by Impact (Priority × Urgency) — hours</h3></div>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrf); ?>">
                            <input type="hidden" name="action" value="update_time_matrix">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="vertical-align: middle;">Impact</th>
                                            <th colspan="4" class="text-center">Urgency: High</th>
                                            <th colspan="4" class="text-center">Urgency: Medium</th>
                                            <th colspan="4" class="text-center">Urgency: Low</th>
                                        </tr>
                                        <tr>
                                            <?php foreach (['High','Medium','Low'] as $u): ?>
                                                <?php foreach ($priorities as $p): ?>
                                                    <th class="text-center"><?php echo $p; ?></th>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($impacts as $imp): ?>
                                            <tr>
                                                <td><?php echo escapeOutput($imp['impact_name']); ?></td>
                                                <?php foreach (['High','Medium','Low'] as $u): ?>
                                                    <?php foreach ($priorities as $p): ?>
                                                        <td style="width:80px;">
                                                            <input type="number" min="1" step="1" class="form-control form-control-sm" name="time_matrix[<?php echo escapeOutput($imp['impact_id']); ?>][<?php echo $u; ?>][<?php echo $p; ?>]" value="<?php echo isset($timeMatrix[$imp['impact_id']][$u][$p]) ? (int)$timeMatrix[$imp['impact_id']][$u][$p] : ''; ?>" placeholder="-">
                                                        </td>
                                                    <?php endforeach; ?>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save SLA Times</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </section>
</div>

<?php include __DIR__ . '/../../../include/Footer.php'; ?>



    </div>
</body>
</html>
