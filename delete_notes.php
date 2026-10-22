<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $note_id = $_POST["note_id"];
    $teacher_id = $_SESSION["user_id"];

    // Get file path
    $query = "SELECT file_path FROM notes WHERE id = ? AND teacher_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $note_id, $teacher_id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $stmt->fetch();
    $stmt->close();

    if ($file_path && unlink($file_path)) {
        // Delete from database
        $query = "DELETE FROM notes WHERE id = ? AND teacher_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $note_id, $teacher_id);
        if ($stmt->execute()) {
            header("Location: notes_teacher.php?success=Note deleted successfully.");
        } else {
            echo "Database deletion failed.";
        }
    } else {
        echo "Error deleting file.";
    }
}
?>
