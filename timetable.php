<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch timetable entries
$query = "SELECT * FROM timetable WHERE student_id = ? ORDER BY FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Timetable</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="dashboard">
        <h2>My Timetable</h2>

        <!-- Add New Schedule Form -->
        <form action="add_timetable.php" method="POST">
            <input type="hidden" name="student_id" value="<?= $student_id ?>">
            <select name="day" required>
                <option value="">Select Day</option>
                <option>Monday</option>
                <option>Tuesday</option>
                <option>Wednesday</option>
                <option>Thursday</option>
                <option>Friday</option>
                <option>Saturday</option>
                <option>Sunday</option>
            </select>
            <input type="text" name="subject" placeholder="Subject" required>
            <input type="time" name="start_time" required>
            <input type="time" name="end_time" required>
            <button type="submit">Add Schedule</button>
        </form>

        <!-- Display Timetable -->
        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Day</th>
                    <th>Subject</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['day'] ?></td>
                    <td><?= $row['subject'] ?></td>
                    <td><?= $row['start_time'] ?></td>
                    <td><?= $row['end_time'] ?></td>
                    <td>
                        <a href="edit_timetable.php?id=<?= $row['id'] ?>" class="btn">Edit</a>
                        <a href="delete_timetable.php?id=<?= $row['id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No timetable entries yet.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 StudyPlanner. All Rights Reserved.</p>
    </footer>
</body>
</html>
