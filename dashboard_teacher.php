<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch teacher name securely
$un = $conn->prepare("SELECT name FROM users WHERE id = ?");
$un->bind_param("i", $user_id);
$un->execute();
$un_result = $un->get_result();
$u = ($un_result->num_rows > 0) ? $un_result->fetch_assoc()['name'] : "Teacher";

// Count total tasks assigned by the teacher
$task_query = $conn->prepare("SELECT COUNT(*) AS total_tasks FROM tasks WHERE teacher_id = ?");
$task_query->bind_param("i", $user_id);
$task_query->execute();
$task_result = $task_query->get_result();
$total_tasks = ($task_result->num_rows > 0) ? $task_result->fetch_assoc()['total_tasks'] : 0;

// Count total notes uploaded by the teacher
$note_query = $conn->prepare("SELECT COUNT(*) AS total_notes FROM notes WHERE teacher_id = ?");
$note_query->bind_param("i", $user_id);
$note_query->execute();
$note_result = $note_query->get_result();
$total_notes = ($note_result->num_rows > 0) ? $note_result->fetch_assoc()['total_notes'] : 0;

// Fetch the teacher's group details
$query = $conn->prepare("SELECT id, group_name, group_code FROM `groups` WHERE teacher_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$group = ($result->num_rows > 0) ? $result->fetch_assoc() : null;

// Count students who joined this teacher's group
$total_students = 0;
if ($group) {
    $student_query = $conn->prepare("SELECT COUNT(*) AS total_students FROM users WHERE role = 'student' AND group_id = ?");
    $student_query->bind_param("i", $group['id']);
    $student_query->execute();
    $student_result = $student_query->get_result();
    $total_students = ($student_result->num_rows > 0)
        ? (int) $student_result->fetch_assoc()['total_students']
        : 0;
    $student_query->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        .group-box {
            border: 2px solid #4CAF50;
            padding: 10px;
            width: fit-content;
            margin: 10px 0;
            background-color: #f9f9f9;
            text-align: center;
            border-radius: 8px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .group-box h4 {
            margin: 5px 0;
            color: #333;
        }
        .group-box p {
            font-size: 16px;
            font-weight: bold;
            color: #4CAF50;
        }
        .group-student-count{display:flex;align-items:center;justify-content:space-between;gap:18px;margin-top:18px;padding:16px 20px;border:1px solid #b6c3f5;border-radius:14px;background:linear-gradient(120deg,#b8d9ff,#d1c1ff);}
        .group-student-count-label{font-weight:700;color:#28356f}
        .group-student-count strong{font:800 2rem Manrope,'DM Sans',sans-serif;color:#514fe0}
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="dashboard1">
        <h2>Welcome, <?= htmlspecialchars($u) ?></h2>
        <a href="tasks_teacher.php" class="btn">Manage Tasks</a>
        <a href="notes_teacher.php" class="btn">Manage Notes</a>
    </div>

    <div class="stats-container">
        <div class="stat-box">
            <h3>Total Tasks Assigned</h3>
            <p><?= $total_tasks ?></p>
        </div>
        <div class="stat-box">
            <h3>Total Notes Uploaded</h3>
            <p><?= $total_notes ?></p>
        </div>
    </div>

    <h3>Your Group</h3>
    <?php if ($group): ?>
        <div class="group-details">
            <p><strong>Group Name:</strong> <?= htmlspecialchars($group['group_name']) ?></p>
            <p><strong>Group Code:</strong> <b><?= htmlspecialchars($group['group_code']) ?></b> (Share this with students)</p>
            <div class="group-student-count">
                <span class="group-student-count-label">Total Students Joined</span>
                <strong><?= $total_students ?></strong>
            </div>
        </div>
    <?php else: ?>
        <p>You haven't created a group yet. <a href="create_group.php">Create Now</a></p>
    <?php endif; ?>

    <h3>About PrepPanda</h3>
    <p>PrepPanda is an online platform designed to help students and teachers manage academic tasks efficiently. 
       It provides a structured way to assign, track, and complete tasks, ensuring better study organization.</p>
    
    <a href="https://t3.ftcdn.net/jpg/06/77/45/84/360_F_677458404_JJXZRTt9DyzdcpQwNWyQGKQHojBu8U4G.jpg">Follow us on</a>

    <br>

    <footer>
        <p>&copy; 2025 StudyPlanner. All Rights Reserved.</p>
    </footer>
</body>
</html>
