<?php
session_start();
include 'includes/db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM timetable WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: timetable.php");
    } else {
        echo "Error deleting entry.";
    }
}
?>
