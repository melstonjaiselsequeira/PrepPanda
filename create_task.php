<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacher_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $subject = trim($_POST['subject']);
    $due_date = $_POST['due_date'];

    // Validate inputs
    if (empty($title) || empty($description) || empty($subject) || empty($due_date)) {
        header("Location: create_task.php?error=All fields are required.");
        exit();
    }

    // Get teacher's group ID
    $group_query = $conn->prepare("SELECT id FROM `groups` WHERE teacher_id = ?");
    $group_query->bind_param("i", $teacher_id);
    $group_query->execute();
    $group_result = $group_query->get_result();
    
    if ($group_result->num_rows === 0) {
        header("Location: tasks_teacher.php?error=Group not found.");
        exit();
    }

    $group_id = $group_result->fetch_assoc()['id'];

    // Insert task into the database
    $stmt = $conn->prepare("INSERT INTO tasks (title, description, subject, due_date, group_id, teacher_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $title, $description, $subject, $due_date, $group_id, $teacher_id);

    if ($stmt->execute()) {
        header("Location: tasks_teacher.php?success=Task created successfully.");
    } else {
        header("Location: tasks_teacher.php?error=Failed to create task.");
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="dash2">
        <h2>Create New Task</h2>

        <?php if (isset($_GET['error'])): ?>
            <p class="error"><?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>

        <form action="create_task.php" method="POST">
            <label>Task Title:</label>
            <input type="text" name="title" placeholder="Enter task title" required>

            <label>Task Description:</label>
            <textarea name="description" placeholder="Enter task description" required></textarea>

            <label>Subject:</label>
            <input type="text" name="subject" placeholder="Enter subject name" required>

            <label>Due Date:</label>
            <input type="date" name="due_date" required>

            <button type="submit">Create Task</button>
        </form>

        <a href="tasks_teacher.php" class="button">Back to Tasks</a>
    </div>
</body>
</html>
