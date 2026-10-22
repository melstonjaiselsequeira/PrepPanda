<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student name
$un = $conn->prepare("SELECT name FROM users WHERE id = ?");
$un->bind_param("i", $user_id);
$un->execute();
$un_result = $un->get_result();
$u = ($un_result->num_rows > 0) ? $un_result->fetch_assoc()['name'] : "Student";

// Get the group ID of the student
$gi = $conn->prepare("SELECT group_id FROM users WHERE id = ?");
$gi->bind_param("i", $user_id);
$gi->execute();
$gi_result = $gi->get_result();
$group_id = ($gi_result->num_rows > 0) ? $gi_result->fetch_assoc()['group_id'] : null;

// If student has a group, fetch details
if ($group_id) {
    $task_query = $conn->prepare("SELECT COUNT(*) AS total_tasks FROM tasks WHERE group_id = ?");
    $task_query->bind_param("i", $group_id);
    $task_query->execute();
    $task_result = $task_query->get_result();
    $total_tasks = ($task_result->num_rows > 0) ? $task_result->fetch_assoc()['total_tasks'] : 0;

    $note_query = $conn->prepare("SELECT COUNT(*) AS total_notes FROM notes WHERE group_id = ?");
    $note_query->bind_param("i", $group_id);
    $note_query->execute();
    $note_result = $note_query->get_result();
    $total_notes = ($note_result->num_rows > 0) ? $note_result->fetch_assoc()['total_notes'] : 0;

    $group_query = $conn->prepare("SELECT group_name FROM `groups` WHERE id = ?");
    $group_query->bind_param("i", $group_id);
    $group_query->execute();
    $group_result = $group_query->get_result();
    $group_name = ($group_result->num_rows > 0) ? $group_result->fetch_assoc()['group_name'] : "Not Joined";
} else {
    $group_name = "Not Joined";
    $total_tasks = 0;
    $total_notes = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="dashboard1">
        <h2>Welcome, <?= htmlspecialchars($u) ?></h2>
        <h3>Joined Group: <?= htmlspecialchars($group_name) ?></h3>



        <a href="tasks_student.php" class="btn">View Tasks</a>
        <a href="notes_student.php" class="btn">View Notes</a>
        <a href="timetable.php" class="btn">View Timetable</a>
    </div>

    <div class="stats-container">
        <div class="stat-box">
            <h3>Total Tasks Assigned</h3>
            <p><?= $total_tasks ?></p>
        </div>
        <div class="stat-box">
            <h3>Total Notes Available</h3>
            <p><?= $total_notes ?></p>
        </div>
    </div>

    <h3>About PrepPanda</h3>
    <p>PrepPanda is an online platform designed to help students and teachers manage academic tasks efficiently.</p>
    <div class="social-leave-row">
        <a href="https://t3.ftcdn.net/jpg/06/77/45/84/360_F_677458404_JJXZRTt9DyzdcpQwNWyQGKQHojBu8U4G.jpg">Follow us on</a>
        <?php if ($group_id): ?>
            <button type="button" class="leave-group-btn" onclick="leaveGroup()">Leave Group</button>
        <?php endif; ?>
    </div>
<?php if (!$group_id): ?>
    <div id="groupModal" class="modal">
        <div class="modal-content">
            <h3>Join a Group</h3>
            <p>Enter Group Join Code:</p>
            <input type="text" id="group_code_input" placeholder="Enter Join Code">
            <button onclick="joinGroup()">Join</button>
        </div>
    </div>
    <?php endif; ?>

    <script>
        $(document).ready(function() {
            <?php if (!$group_id): ?>
                $("#groupModal").fadeIn();
            <?php endif; ?>
        });

        function leaveGroup() {
            if (!confirm("Are you sure you want to leave this group? You may lose access to its group-based tasks and notes.")) return;
            const button = document.querySelector('.leave-group-btn');
            if (button) { button.disabled = true; button.textContent = 'Leaving...'; }
            $.ajax({
                url: "leave_group.php",
                method: "POST",
                dataType: "json",
                cache: false,
                success: function(response) {
                    alert(response.message || 'Request completed.');
                    if (response.status === "success") {
                        window.location.replace("dashboard_student.php");
                    } else if (button) { button.disabled = false; button.textContent = 'Leave Group'; }
                },
                error: function(xhr) {
                    let message = 'Unable to leave the group. Please check your connection and try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) message = xhr.responseJSON.message;
                    alert(message);
                    if (button) { button.disabled = false; button.textContent = 'Leave Group'; }
                    console.error('Leave group request failed:', xhr.status, xhr.responseText);
                }
            });
        }

        function joinGroup() {
    var groupCode = $("#group_code_input").val();
    if (groupCode === "") {
        alert("Please enter a valid Group Join Code.");
        return;
    }

    $.ajax({
        url: "join_group.php",
        type: "POST",
        data: { group_code: groupCode },
        dataType: "json",  // Expect JSON response
        success: function(response) {
            if (response.status === "success") {
                alert(response.message);
                window.location.href = "dashboard_student.php"; // Redirect to dashboard after joining
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert("An error occurred. Please try again.");
        }
    });
}

    </script>

    <footer>
        <p>&copy; 2025 StudyPlanner. All Rights Reserved.</p>
    </footer>
</body>
</html>
