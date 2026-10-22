<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_id = $_POST["task_id"];
    $feedback = $_POST["feedback"];

    $query = "UPDATE tasks SET feedback = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $feedback, $task_id);

    if ($stmt->execute()) {
        header("Location: tasks_teacher.php?success=Feedback submitted.");
    } else {
        echo "Failed to submit feedback.";
    }
}
?>
