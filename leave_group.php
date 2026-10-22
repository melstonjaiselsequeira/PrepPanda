<?php
// Handles a student's request to leave their currently assigned group.
header('Content-Type: application/json; charset=utf-8');
session_start();

function respond($status, $message, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    respond('error', 'Your session has expired. Please log in again.', 401);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond('error', 'Invalid request. Please try again.', 405);
}

try {
    require_once __DIR__ . '/includes/db_connect.php';
    $student_id = (int) $_SESSION['user_id'];

    // First check the current membership, then clear it for this student only.
    $check = $conn->prepare("SELECT group_id FROM users WHERE id = ? AND role = 'student' LIMIT 1");
    $check->bind_param('i', $student_id);
    $check->execute();
    $row = $check->get_result()->fetch_assoc();
    $check->close();

    if (!$row || $row['group_id'] === null) {
        respond('error', 'You are not currently in a group.');
    }

    $stmt = $conn->prepare("UPDATE users SET group_id = NULL WHERE id = ? AND role = 'student'");
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $stmt->close();
    unset($_SESSION['group_id']);
    respond('success', 'You have left the group successfully.');
} catch (Throwable $e) {
    error_log('PrepPanda leave_group.php error: ' . $e->getMessage());
    respond('error', 'Could not leave the group because of a server/database error. Check the PHP error log and database connection.', 500);
}
?>
