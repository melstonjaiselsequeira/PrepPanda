<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$title = trim($_POST["title"]);
$description = trim($_POST["description"]);

// Validate inputs
if (empty($title) || empty($description)) {
    header("Location: notes_teacher.php?error=Title and description are required.");
    exit();
}

// File upload validation
if (!isset($_FILES["file"]) || $_FILES["file"]["error"] != 0) {
    header("Location: notes_teacher.php?error=File upload failed.");
    exit();
}

$file_name = basename($_FILES["file"]["name"]);
$file_tmp = $_FILES["file"]["tmp_name"];
$file_size = $_FILES["file"]["size"];
$file_type = mime_content_type($file_tmp);
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

$allowed_exts = ["pdf"];
$allowed_types = ["application/pdf"];

// Check file type and size (Max: 2MB)
if (!in_array($file_ext, $allowed_exts) || !in_array($file_type, $allowed_types)) {
    header("Location: notes_teacher.php?error=Only PDF files are allowed.");
    exit();
}

if ($file_size > 2 * 1024 * 1024) { // 2MB
    header("Location: notes_teacher.php?error=File size exceeds 2MB limit.");
    exit();
}

// Generate unique file path
$file_path = "uploads/" . time() . "_" . $file_name;

// Move file to uploads folder
if (!move_uploaded_file($file_tmp, $file_path)) {
    header("Location: notes_teacher.php?error=Failed to save uploaded file.");
    exit();
}

// Get teacher's group
$group_query = $conn->prepare("SELECT id FROM `groups` WHERE teacher_id = ?");
$group_query->bind_param("i", $teacher_id);
$group_query->execute();
$group_result = $group_query->get_result();
$group = $group_result->fetch_assoc();

if (!$group) {
    header("Location: notes_teacher.php?error=Group not found.");
    exit();
}

$group_id = $group["id"];

// Insert into database
$query = $conn->prepare("INSERT INTO notes (teacher_id, group_id, title, description, file_path, uploaded_at) VALUES (?, ?, ?, ?, ?, NOW())");
$query->bind_param("iisss", $teacher_id, $group_id, $title, $description, $file_path);

if ($query->execute()) {
    header("Location: notes_teacher.php?success=Note uploaded successfully.");
} else {
    header("Location: notes_teacher.php?error=Database error. Please try again.");
}
exit();
?>
