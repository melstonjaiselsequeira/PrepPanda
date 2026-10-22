<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $task_id = $_POST["task_id"];
    $student_id = $_SESSION["user_id"];

    $target_dir = "uploads/";
    $file_name = time() . "_" . basename($_FILES["file"]["name"]);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if ($file_type == "pdf") {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $query = "UPDATE tasks SET completed_file = ?, submitted_at = NOW(), status = 'completed' WHERE id = ? AND student_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sii", $target_file, $task_id, $student_id);
            if ($stmt->execute()) {
                header("Location: tasks_student.php?success=Task uploaded successfully.");
            } else {
                echo "Database update failed.";
            }
        } else {
            echo "File upload failed.";
        }
    } else {
        echo "Only PDF files are allowed.";
    }
}
?>
