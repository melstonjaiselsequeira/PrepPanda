<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

include 'includes/db_connect.php';

$teacher_id = $_SESSION['user_id'];
$group_id = $_POST['group_id'];
$title = trim($_POST['title']);
$description = trim($_POST['description']);
$subject = trim($_POST['subject']);
$due_date = $_POST['due_date'];

// Validate group ownership
$checkGroup = $conn->prepare("SELECT id FROM `groups` WHERE id = ? AND teacher_id = ?");
$checkGroup->bind_param("ii", $group_id, $teacher_id);
$checkGroup->execute();
$result = $checkGroup->get_result();

if ($result->num_rows == 0) {
    die("Invalid group or unauthorized action.");
}

// Insert task
$stmt = $conn->prepare("INSERT INTO tasks (teacher_id, group_id, title, description, subject, due_date) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissss", $teacher_id, $group_id, $title, $description, $subject, $due_date);

if ($stmt->execute()) {
    header("Location: tasks_teacher.php?success=Task assigned!");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
