<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM timetable WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $entry = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $day = $_POST['day'];
    $subject = $_POST['subject'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $query = "UPDATE timetable SET day=?, subject=?, start_time=?, end_time=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $day, $subject, $start_time, $end_time, $id);

    if ($stmt->execute()) {
        header("Location: timetable.php");
    } else {
        echo "Error updating timetable.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Timetable</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="dashboard">
        <h2>Edit Timetable</h2>
        <form action="edit_timetable.php" method="POST">
            <input type="hidden" name="id" value="<?= $entry['id'] ?>">
            <select name="day" required>
                <option value="<?= $entry['day'] ?>" selected><?= $entry['day'] ?></option>
                <option>Monday</option>
                <option>Tuesday</option>
                <option>Wednesday</option>
                <option>Thursday</option>
                <option>Friday</option>
                <option>Saturday</option>
                <option>Sunday</option>
            </select>
            <input type="text" name="subject" value="<?= $entry['subject'] ?>" required>
            <input type="time" name="start_time" value="<?= $entry['start_time'] ?>" required>
            <input type="time" name="end_time" value="<?= $entry['end_time'] ?>" required>
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
