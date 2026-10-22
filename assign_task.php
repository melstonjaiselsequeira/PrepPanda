<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Fetch teacher's group ID
$group_query = $conn->prepare("SELECT id FROM groups WHERE teacher_id = ?");
$group_query->bind_param("i", $teacher_id);
$group_query->execute();
$group_result = $group_query->get_result();
$group = ($group_result->num_rows > 0) ? $group_result->fetch_assoc()['id'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];

    // Ensure the student is in the teacher's group
    $student_check = $conn->prepare("SELECT id FROM users WHERE id = ? AND group_id = ? AND role = 'student'");
    $student_check->bind_param("ii", $student_id, $group);
    $student_check->execute();
    $student_result = $student_check->get_result();

    if ($student_result->num_rows > 0) {
        $query = "INSERT INTO tasks (teacher_id, student_id, title, description, due_date, group_id) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iisssi", $teacher_id, $student_id, $title, $description, $due_date, $group);

        if ($stmt->execute()) {
            header("Location: tasks_teacher.php?success=Task assigned successfully.");
            exit();
        } else {
            $error_message = "Failed to assign task.";
        }
    } else {
        $error_message = "Invalid student selection.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Assign Task</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="dashboard">
        <h2>Assign Task</h2>

        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?= htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form action="assign_task.php" method="POST">
            <label>Student:</label>
            <select name="student_id" required>
                <?php
                // Fetch students only from the teacher's group
                $student_query = $conn->prepare("SELECT id, name FROM users WHERE group_id = ? AND role = 'student'");
                $student_query->bind_param("i", $group);
                $student_query->execute();
                $result = $student_query->get_result();

                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>

            <label>Title:</label>
            <input type="text" name="title" required>

            <label>Description:</label>
            <textarea name="description" required></textarea>

            <label>Due Date:</label>
            <input type="date" name="due_date" id="due_date" required>
                
            <button type="submit">Assign Task</button>
        </form>
    </div>
</body>
</html>
