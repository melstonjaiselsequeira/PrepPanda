<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
        header("Location: login.php");
        exit();
    }

    $submission_id = $_POST['submission_id']; // Get submission ID from form
    $feedback = trim($_POST['feedback']);

    // Ensure task submission exists before updating
    $check_stmt = $conn->prepare("SELECT id FROM task_submissions WHERE id = ?");
    $check_stmt->bind_param("i", $submission_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Update feedback for the existing submission
        $stmt = $conn->prepare("UPDATE task_submissions SET feedback=? WHERE id=?");
        $stmt->bind_param("si", $feedback, $submission_id);

        if ($stmt->execute()) {
            header("Location: tasks_teacher.php?success=Feedback submitted.");
            exit();
        } else {
            header("Location: tasks_teacher.php?error=Failed to submit feedback.");
            exit();
        }
    } else {
        // If no submission exists, prevent feedback submission
        header("Location: tasks_teacher.php?error=No submission found.");
        exit();
    }

    $check_stmt->close();
    $stmt->close();
    $conn->close();
}
?>
