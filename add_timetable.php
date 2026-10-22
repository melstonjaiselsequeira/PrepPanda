<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $day = $_POST['day'];
    $subject = $_POST['subject'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $query = "INSERT INTO timetable (student_id, day, subject, start_time, end_time) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $student_id, $day, $subject, $start_time, $end_time);

    if ($stmt->execute()) {
        header("Location: timetable.php");
    } else {
        echo "Error adding timetable.";
    }
}
?>
