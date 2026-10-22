<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
        header("Location: login.php");
        exit();
    }

    $task_id = $_POST['task_id'];
    $student_id = $_SESSION['user_id'];

    // Step 1: Find the student's group
    $group_query = $conn->prepare("SELECT group_id FROM users WHERE id = ?");
    $group_query->bind_param("i", $student_id);
    $group_query->execute();
    $group_result = $group_query->get_result();
    
    if ($group_result->num_rows === 0) {
        header("Location: tasks_student.php?error=Student group not found.");
        exit();
    }
    
    $group_id = $group_result->fetch_assoc()['group_id'];

    // Step 2: Verify that the task belongs to the student's group
    $task_query = $conn->prepare("SELECT id FROM tasks WHERE id = ? AND group_id = ?");
    $task_query->bind_param("ii", $task_id, $group_id);
    $task_query->execute();
    $task_result = $task_query->get_result();

    if ($task_result->num_rows === 0) {
        header("Location: tasks_student.php?error=Unauthorized task upload.");
        exit();
    }

    // Step 3: Handle File Upload
    $target_dir = "uploads/";
    $file_name = basename($_FILES["task_file"]["name"]);
    $target_file = $target_dir . time() . "_" . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Allow only PDF uploads
    if ($file_type !== "pdf") {
        header("Location: tasks_student.php?error=Only PDF files are allowed.");
        exit();
    }

    if (move_uploaded_file($_FILES["task_file"]["tmp_name"], $target_file)) {
        // Step 4: Insert student's submission into `task_submissions` table
        $stmt = $conn->prepare("INSERT INTO task_submissions (task_id, student_id, file_path, status) VALUES (?, ?, ?, 'completed')");
        $stmt->bind_param("iis", $task_id, $student_id, $target_file);

        if ($stmt->execute()) {
            header("Location: tasks_student.php?success=Task uploaded successfully.");
        } else {
            header("Location: tasks_student.php?error=Failed to save task submission.");
        }
        $stmt->close();
    } else {
        header("Location: tasks_student.php?error=Failed to upload file.");
    }
}
?>
