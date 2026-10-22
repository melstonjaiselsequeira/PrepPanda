<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // Replace with `$_SESSION['user_id']`
    $name = $_POST['name'];
    $email = $_POST['email'];

    $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $email, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully'); window.location='profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile'); window.location='profile.php';</script>";
    }
}
?>
