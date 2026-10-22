<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch student's group ID
$group_query = $conn->prepare("SELECT group_id FROM users WHERE id = ?");
$group_query->bind_param("i", $student_id);
$group_query->execute();
$group_result = $group_query->get_result();
$group_id = ($group_result->num_rows > 0) ? $group_result->fetch_assoc()['group_id'] : null;

// Fetch assigned tasks along with feedback
$tasks = [];
if ($group_id) {
    $task_query = $conn->prepare("
        SELECT t.id, t.title, t.subject, t.description, t.due_date, 
               ts.file_path AS submitted_file, ts.feedback
        FROM tasks t
        LEFT JOIN task_submissions ts ON t.id = ts.task_id AND ts.student_id = ?
        WHERE t.group_id = ?");
    $task_query->bind_param("ii", $student_id, $group_id);
    $task_query->execute();
    $task_result = $task_query->get_result();

    while ($task = $task_result->fetch_assoc()) {
        $tasks[] = $task;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="dash2">
        <h2>My Assigned Tasks</h2>

        <?php if (!empty($tasks)): ?>
        <table>
            <tr>
                <th>Title</th>
                <th>Subject</th>
                <th>Due Date</th>
                <th>Submission</th>
                <th>Feedback</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= htmlspecialchars($task['title']) ?></td>
                <td><?= htmlspecialchars($task['subject']) ?></td>
                <td><?= htmlspecialchars($task['due_date']) ?></td>
                <td>
                    <?php if ($task['submitted_file']): ?>
                        <a href="<?= htmlspecialchars($task['submitted_file']) ?>" target="_blank">View Submission</a>
                    <?php else: ?>
                        <span class="pending">Not Submitted</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($task['feedback'])): ?>
                        <span class="feedback"><?= htmlspecialchars($task['feedback']) ?></span>
                    <?php else: ?>
                        <span class="pending">No feedback yet</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!$task['submitted_file']): ?>
                        <form action="upload_task.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                            <input type="file" name="task_file" id="task_file" accept="application/pdf" required>
                            <button type="submit">Upload</button>
                        </form>
                    <?php else: ?>
                        <span class="completed">Submitted</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>No tasks assigned.</p>
        <?php endif; ?>
    </div>
    
    <footer>
        <p>&copy; 2025 StudyPlanner. All Rights Reserved.</p>
    </footer>
    <script>
        function validateForm() {
            var fileInput = document.getElementById("task_file");
            var filePath = fileInput.value;
            var allowedExtensions = /(\.pdf)$/i;

            if (fileInput.files.length === 0) {
                alert("Please upload a PDF file before submitting.");
                return false; // Prevent form submission
            }

            if (!allowedExtensions.test(filePath)) {
                alert("Invalid file type! Please upload a PDF file.");
                fileInput.value = ""; // Clear the invalid file
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }
    </script>
</body>
</html>
