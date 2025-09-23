<?php
session_start();

// Check if user is logged in and the request is valid
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['team_id'])) {
    http_response_code(400);
    echo "Invalid Request";
    exit;
}

$selected_team_id = $_POST['team_id'];
$user_teams = $_SESSION['user_teams'] ?? [];

// Verify that the user is actually a member of the selected team
$is_member = false;
$selected_team_name = '';
foreach ($user_teams as $team) {
    if ($team['team_id'] === $selected_team_id) {
        $is_member = true;
        $selected_team_name = $team['team_name'];
        break;
    }
}

if ($is_member) {
    // Update the active team in the session
    $_SESSION['team_id'] = $selected_team_id;
    $_SESSION['team_name'] = $selected_team_name;
    echo "Team switched successfully to " . htmlspecialchars($selected_team_name);
} else {
    http_response_code(403);
    echo "Forbidden: You are not a member of the selected team.";
}
