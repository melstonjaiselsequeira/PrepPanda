<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
        header("Location: login.php");
        exit();
    }

    $teacher_id = $_SESSION['user_id'];
    $task_id = $_POST['task_id'];

    // Verify that the task belongs to the teacher
    $check_task = $conn->prepare("SELECT group_id FROM tasks WHERE id = ? AND teacher_id = ?");
    $check_task->bind_param("ii", $task_id, $teacher_id);
    $check_task->execute();
    $task_result = $check_task->get_result();

    if ($task_result->num_rows === 0) {
        header("Location: tasks_teacher.php?error=Task not found or unauthorized action.");
        exit();
    }

    // Delete associated submissions first to maintain referential integrity
    $delete_submissions = $conn->prepare("DELETE FROM task_submissions WHERE task_id = ?");
    $delete_submissions->bind_param("i", $task_id);
    $delete_submissions->execute();

    // Delete the task
    $delete_task = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $delete_task->bind_param("i", $task_id);

    if ($delete_task->execute()) {
        header("Location: tasks_teacher.php?success=Task deleted successfully.");
    } else {
        header("Location: tasks_teacher.php?error=Failed to delete task.");
    }

    $delete_submissions->close();
    $delete_task->close();
}
?>
