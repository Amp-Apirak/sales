<?php
// SLA helper functions: compute SLA hours from Priority, Urgency, Impact
// Usage: require_once __DIR__ . '/sla_helpers.php';

if (!function_exists('computeSlaTarget')) {
    /**
     * Compute SLA target hours from given Priority, Urgency, Impact using config tables.
     * Falls back to sensible defaults if configuration not found.
     *
     * @param PDO         $condb
     * @param string|null $priority  One of: Critical|High|Medium|Low (optional)
     * @param string|null $urgency   One of: High|Medium|Low (optional)
     * @param string|null $impact    Impact name (e.g., Organization, Site, Single User)
     * @return int|null  SLA hours or null if cannot be determined
     */
    function computeSlaTarget(PDO $condb, ?string $priority, ?string $urgency, ?string $impact): ?int
    {
        $priority = normalizePUIP($priority);
        $urgency  = normalizePUIP($urgency);
        $impact   = $impact !== null ? trim($impact) : null;

        // 1) If priority is given, prefer per-impact overrides if available
        if (!empty($priority)) {
            // Look up impact_id if impact provided
            if (!empty($impact) && !empty($urgency)) {
                $iid = null;
                $stmtI = $condb->prepare("SELECT impact_id FROM service_sla_impacts WHERE impact_name = :name AND active = 1 LIMIT 1");
                $stmtI->execute([':name' => $impact]);
                $imp = $stmtI->fetch(PDO::FETCH_ASSOC);
                if ($imp && !empty($imp['impact_id'])) { $iid = $imp['impact_id']; }

                if ($iid) {
                    $stmt = $condb->prepare("SELECT sla_hours FROM service_sla_time_matrix WHERE impact_id = :iid AND priority = :priority AND urgency = :urgency LIMIT 1");
                    $stmt->execute([':iid' => $iid, ':priority' => $priority, ':urgency' => $urgency]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row && isset($row['sla_hours'])) {
                        return (int)$row['sla_hours'];
                    }
                }
            }
            // Fallback to global target by priority
            $stmt = $condb->prepare("SELECT sla_hours FROM service_sla_targets WHERE priority = :priority LIMIT 1");
            $stmt->execute([':priority' => $priority]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && isset($row['sla_hours'])) {
                return (int)$row['sla_hours'];
            }
            // fallback by hardcoded defaults
            $fallback = defaultPriorityToHours();
            return $fallback[$priority] ?? null;
        }

        // 2) Derive priority from (impact, urgency) via priority matrix, then prefer per-impact time overrides
        $derivedPriority = mapImpactUrgencyToPriority($condb, $impact, $urgency);
        if (!empty($derivedPriority)) {
            // Try per-impact override first (requires impact + urgency)
            if (!empty($impact) && !empty($urgency)) {
                $iid = null;
                $stmtI = $condb->prepare("SELECT impact_id FROM service_sla_impacts WHERE impact_name = :name AND active = 1 LIMIT 1");
                $stmtI->execute([':name' => $impact]);
                $imp = $stmtI->fetch(PDO::FETCH_ASSOC);
                if ($imp && !empty($imp['impact_id'])) { $iid = $imp['impact_id']; }
                if ($iid) {
                    $stmt = $condb->prepare("SELECT sla_hours FROM service_sla_time_matrix WHERE impact_id = :iid AND priority = :priority AND urgency = :urgency LIMIT 1");
                    $stmt->execute([':iid' => $iid, ':priority' => $derivedPriority, ':urgency' => $urgency]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row && isset($row['sla_hours'])) {
                        return (int)$row['sla_hours'];
                    }
                }
            }
            // Fallback to global by priority
            $stmt = $condb->prepare("SELECT sla_hours FROM service_sla_targets WHERE priority = :priority LIMIT 1");
            $stmt->execute([':priority' => $derivedPriority]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && isset($row['sla_hours'])) {
                return (int)$row['sla_hours'];
            }
            $fallback = defaultPriorityToHours();
            return $fallback[$derivedPriority] ?? null;
        }

        // 3) Last resort fallback (Medium = 24h)
        $fallback = defaultPriorityToHours();
        return $fallback['Medium'];
    }
}

if (!function_exists('mapImpactUrgencyToPriority')) {
    /**
     * Map impact name + urgency to a configured priority via matrix.
     */
    function mapImpactUrgencyToPriority(PDO $condb, ?string $impactName, ?string $urgency): ?string
    {
        $urgency = normalizePUIP($urgency);
        if (empty($impactName) || empty($urgency)) {
            return null;
        }

        // Find impact_id by name
        $stmt = $condb->prepare("SELECT impact_id FROM service_sla_impacts WHERE impact_name = :name AND active = 1 LIMIT 1");
        $stmt->execute([':name' => $impactName]);
        $impact = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$impact || empty($impact['impact_id'])) {
            return null;
        }

        // Map to priority by urgency
        $stmt2 = $condb->prepare("SELECT priority FROM service_sla_priority_matrix WHERE impact_id = :impact_id AND urgency = :urgency LIMIT 1");
        $stmt2->execute([':impact_id' => $impact['impact_id'], ':urgency' => $urgency]);
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        return $row2['priority'] ?? null;
    }
}

if (!function_exists('normalizePUIP')) {
    /** Normalize Priority/Urgency values to canonical capitalization */
    function normalizePUIP(?string $value): ?string
    {
        if ($value === null) return null;
        $v = trim($value);
        // canonical set
        $map = [
            'critical' => 'Critical',
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
        ];
        $key = strtolower($v);
        return $map[$key] ?? $v; // return as-is if unknown; DB may still accept if configured
    }
}

if (!function_exists('defaultPriorityToHours')) {
    /** Default mapping when DB config not found */
    function defaultPriorityToHours(): array
    {
        return [
            'Critical' => 4,
            'High' => 8,
            'Medium' => 24,
            'Low' => 72,
        ];
    }
}

