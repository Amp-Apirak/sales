<?php
/**
 * API Endpoint: Get Sellers by Team
 * Purpose: Fetch sellers for a specific team (used in add_project.php)
 * Method: GET
 * Parameters: team_id
 */

include '../../include/Add_session.php';

// Must be after Add_session.php to access session variables
$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';

header('Content-Type: application/json; charset=utf-8');

// Check if user is authorized
if (!in_array($role, ['Executive', 'Account Management', 'Sale Supervisor', 'Seller'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized', 'role' => $role]);
    exit();
}

$team_id = $_GET['team_id'] ?? '';

if (empty($team_id)) {
    echo json_encode(['success' => false, 'message' => 'Team ID is required']);
    exit();
}

try {
    $sellers = [];

    // Role-based logic for fetching sellers
    if ($role === 'Executive') {
        // Executive can see all sellers in the selected team
        $sql = "SELECT u.user_id, u.first_name, u.last_name, u.role
                FROM users u
                INNER JOIN user_teams ut ON u.user_id = ut.user_id
                WHERE ut.team_id = :team_id
                  AND u.role IN ('Executive', 'Account Management', 'Sale Supervisor', 'Seller')
                GROUP BY u.user_id
                ORDER BY u.first_name, u.last_name";

        $stmt = $condb->prepare($sql);
        $stmt->execute([':team_id' => $team_id]);
        $sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif ($role === 'Account Management') {
        // Account Management can see sellers in their teams only
        $user_teams = $_SESSION['user_teams'] ?? [];
        $team_ids = array_column($user_teams, 'team_id');

        if (!in_array($team_id, $team_ids)) {
            echo json_encode(['success' => false, 'message' => 'Access denied to this team']);
            exit();
        }

        $sql = "SELECT u.user_id, u.first_name, u.last_name, u.role
                FROM users u
                INNER JOIN user_teams ut ON u.user_id = ut.user_id
                WHERE ut.team_id = :team_id
                  AND u.role IN ('Account Management', 'Sale Supervisor', 'Seller')
                GROUP BY u.user_id
                ORDER BY u.first_name, u.last_name";

        $stmt = $condb->prepare($sql);
        $stmt->execute([':team_id' => $team_id]);
        $sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif ($role === 'Sale Supervisor') {
        // Sale Supervisor can see sellers in their teams only
        $user_teams = $_SESSION['user_teams'] ?? [];
        $team_ids = array_column($user_teams, 'team_id');

        if (!in_array($team_id, $team_ids)) {
            echo json_encode(['success' => false, 'message' => 'Access denied to this team']);
            exit();
        }

        $sql = "SELECT u.user_id, u.first_name, u.last_name, u.role
                FROM users u
                INNER JOIN user_teams ut ON u.user_id = ut.user_id
                WHERE ut.team_id = :team_id
                  AND u.role IN ('Sale Supervisor', 'Seller')
                GROUP BY u.user_id
                ORDER BY u.first_name, u.last_name";

        $stmt = $condb->prepare($sql);
        $stmt->execute([':team_id' => $team_id]);
        $sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif ($role === 'Seller') {
        // Seller can only select themselves
        $sql = "SELECT user_id, first_name, last_name, role
                FROM users
                WHERE user_id = :user_id";

        $stmt = $condb->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        $sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'success' => true,
        'sellers' => $sellers,
        'count' => count($sellers)
    ]);

} catch (PDOException $e) {
    // Log error for debugging
    error_log("get_sellers_by_team.php Error: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error_detail' => $e->getMessage(),
        'debug' => [
            'team_id' => $team_id ?? 'not set',
            'role' => $role ?? 'not set',
            'user_id' => $user_id ?? 'not set'
        ]
    ]);
} catch (Exception $e) {
    // Catch any other errors
    error_log("get_sellers_by_team.php General Error: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'error_detail' => $e->getMessage()
    ]);
}
