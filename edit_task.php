<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["task_id"])) {
    header("Location: tasks_teacher.php");
    exit();
}

$task_id = $_GET["task_id"];
$query = "SELECT * FROM tasks WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $due_date = $_POST["due_date"];

    $updateQuery = "UPDATE tasks SET title = ?, description = ?, due_date = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssi", $title, $description, $due_date, $task_id);

    if ($stmt->execute()) {
        header("Location: tasks_teacher.php?success=Task updated.");
    } else {
        echo "Error updating task.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Task</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="dashboard">
        <h2>Edit Task</h2>
        <form action="edit_task.php?task_id=<?= $task_id; ?>" method="POST">
            <label for="title">Title:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($task['title']); ?>" required>

            <label for="description">Description:</label>
            <textarea name="description" required><?= htmlspecialchars($task['description']); ?></textarea>

            <label for="due_date">Due Date:</label>
            <input type="date" name="due_date" value="<?= htmlspecialchars($task['due_date']); ?>" required>

            <button type="submit">Update Task</button>
        </form>
    </div>
</body>
</html>
