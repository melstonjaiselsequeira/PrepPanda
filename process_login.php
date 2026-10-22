<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == "student") {
                header("Location: dashboard_student.php");
            } else {
                header("Location: dashboard_teacher.php");
            }
            exit();
        }
    }
    echo"<script>
    alert('Invalid login!');
    window.location.href = 'login.php';
</script>";
}
?>
