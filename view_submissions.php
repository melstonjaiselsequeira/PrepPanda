<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$task_id = $_GET['task_id'] ?? 0;

// Get teacher's group ID
$group_query = $conn->prepare("SELECT id FROM `groups` WHERE teacher_id = ?");
$group_query->bind_param("i", $teacher_id);
$group_query->execute();
$group_result = $group_query->get_result();
$group_id = ($group_result->num_rows > 0) ? $group_result->fetch_assoc()['id'] : null;

// Fetch submissions for the given task
if ($group_id) {
    $submissions_query = $conn->prepare("
        SELECT ts.id, ts.file_path, ts.submitted_at, u.name AS student_name, t.title AS task_title, ts.feedback
        FROM task_submissions ts
        JOIN users u ON ts.student_id = u.id
        JOIN tasks t ON ts.task_id = t.id
        WHERE t.id = ? AND t.group_id = ?
        ORDER BY ts.submitted_at DESC");
    $submissions_query->bind_param("ii", $task_id, $group_id);
    $submissions_query->execute();
    $submissions_result = $submissions_query->get_result();
}

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_id'], $_POST['feedback'])) {
    $submission_id = $_POST['submission_id'];
    $feedback = $_POST['feedback'];

    $feedback_query = $conn->prepare("UPDATE task_submissions SET feedback = ? WHERE id = ?");
    $feedback_query->bind_param("si", $feedback, $submission_id);
    
    if ($feedback_query->execute()) {
        header("Location: view_submissions.php?task_id=$task_id&success=Feedback submitted.");
    } else {
        header("Location: view_submissions.php?task_id=$task_id&error=Failed to submit feedback.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Submissions</title>
</head>
<body>
    <h2>Task Submissions</h2>

    <?php if ($group_id && $submissions_result->num_rows > 0): ?>
        <ul>
            <?php while ($submission = $submissions_result->fetch_assoc()): ?>
                <li>
                    <strong><?= htmlspecialchars($submission['student_name']) ?></strong> submitted 
                    <strong><?= htmlspecialchars($submission['task_title']) ?></strong>
                    <br>Submitted at: <?= $submission['submitted_at'] ?>
                    <br><a href="<?= $submission['file_path'] ?>" target="_blank">View File</a>

                    <form action="view_submissions.php?task_id=<?= $task_id ?>" method="POST">
                        <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                        <textarea name="feedback" placeholder="Enter feedback..." required><?= htmlspecialchars($submission['feedback']) ?></textarea>
                        <button type="submit">Submit Feedback</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No submissions yet.</p>
    <?php endif; ?>
</body>
</html>
