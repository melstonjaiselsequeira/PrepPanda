<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Get teacher's group ID
$query = "SELECT id FROM `groups` WHERE teacher_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$group = $result->fetch_assoc();
$group_id = $group['id'] ?? null;

if (!$group_id) {
    die("No group found for this teacher.");
}

// Fetch only unique tasks assigned by the teacher
$tasks_stmt = $conn->prepare("
    SELECT id, title, subject, due_date, description
    FROM tasks
    WHERE group_id = ? AND teacher_id = ?
    ORDER BY due_date DESC
");
$tasks_stmt->bind_param("ii", $group_id, $teacher_id);
$tasks_stmt->execute();
$tasks_result = $tasks_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Tasks</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="dash2">
        <h2>Assign New Task</h2>
        <form action="process_task.php" method="POST">
            <input type="hidden" name="group_id" value="<?= $group_id ?>">

            <label>Title:</label>
            <input type="text" name="title" required>

            <label>Description:</label>
            <textarea name="description" required></textarea>

            <label>Subject:</label>
            <input type="text" name="subject" required>

            <label>Due Date:</label>
            <input type="date" name="due_date" id="due_date" min="" required onfocus="this.min=new Date().toISOString().split('T')[0]">


            <button type="submit">Assign Task</button>
            <br>
        </form>

        <h2>Assigned Tasks</h2>
        <?php if ($tasks_result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Title</th>
                <th>Subject</th>
                <th>Due Date</th>
                <th>Submissions</th>
                <th>Actions</th>
            </tr>
            <?php while ($task = $tasks_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($task['title']) ?></td>
                <td><?= htmlspecialchars($task['subject']) ?></td>
                <td><?= htmlspecialchars($task['due_date']) ?></td>
                <td>
                    <?php
                    // Fetch submissions for this task, including student_id
                    $submission_stmt = $conn->prepare("
                        SELECT ts.id AS submission_id, ts.file_path, ts.status, ts.feedback, 
                               u.name AS student_name, ts.student_id
                        FROM task_submissions ts
                        JOIN users u ON ts.student_id = u.id
                        WHERE ts.task_id = ?
                    ");
                    $submission_stmt->bind_param("i", $task['id']);
                    $submission_stmt->execute();
                    $submissions_result = $submission_stmt->get_result();

                    if ($submissions_result->num_rows > 0):
                        while ($submission = $submissions_result->fetch_assoc()):
                    ?>
                        <p>
                            <strong><?= htmlspecialchars($submission['student_name']) ?>:</strong>
                            <?php if (!empty($submission['file_path'])): ?>
                                <a href="<?= htmlspecialchars($submission['file_path']) ?>" target="_blank">View File</a>
                            <?php else: ?>
                                <span class="pending">Not Uploaded</span>
                            <?php endif; ?>
                            - <?= ($submission['status'] === 'completed') ? '<span class="completed">Completed</span>' : '<span class="pending">Pending</span>' ?>
                        </p>

                        <!-- Feedback Form with Student ID -->
                        <form action="give_feedback.php" method="POST">
                            <input type="hidden" name="submission_id" value="<?= $submission['submission_id'] ?>">
                            <input type="hidden" name="student_id" value="<?= $submission['student_id'] ?>"> <!-- Pass Student ID -->
                            <textarea name="feedback" placeholder="Enter feedback here"><?= htmlspecialchars($submission['feedback'] ?? '') ?></textarea>
                            <button type="submit">Submit Feedback</button>
                        </form>

                    <?php endwhile; else: ?>
                        <span class="pending">No Submissions Yet</span>
                    <?php endif; ?>
                </td>
                <td>
                    <form action="delete_task.php" method="POST">
                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this task?');">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <p>No tasks have been assigned yet.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 StudyPlanner. All Rights Reserved.</p>
    </footer>
</body>
</html>
