<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student's group ID
$group_query = $conn->prepare("SELECT group_id FROM users WHERE id = ?");
$group_query->bind_param("i", $user_id);
$group_query->execute();
$group_result = $group_query->get_result();
$group = ($group_result->num_rows > 0) ? $group_result->fetch_assoc()['group_id'] : null;

// Fetch notes uploaded by teachers in the same group
if ($group) {
    $query = $conn->prepare("SELECT title, file_path, uploaded_at FROM notes WHERE group_id = ? ORDER BY uploaded_at DESC");
    $query->bind_param("i", $group);
    $query->execute();
    $result = $query->get_result();
} else {
    $result = false; // No group assigned, so no notes
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Notes</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard">
        <h2>Available Notes</h2>

        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Uploaded At</th>
                    <th>Download</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= date("d M Y, H:i A", strtotime($row['uploaded_at'])); ?></td>
                        <td><a href="<?= htmlspecialchars($row['file_path']); ?>" target="_blank" download>Download</a></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No notes are available for your group yet.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 StudyPlanner. All Rights Reserved.</p>
    </footer>
</body>
</html>
