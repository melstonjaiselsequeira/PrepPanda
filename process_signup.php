<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"];

    // Check if email already exists
    $check_query = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email already in use!'); window.location.href='signup.php';</script>";
        //header("Location: signup.php?error=Email already in use.");
        exit();
    }

    // Insert new user
    $insert_query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        $_SESSION["user_id"] = $stmt->insert_id;
        $_SESSION["role"] = $role;
        $_SESSION["name"] = $name;
        $user_id=$_SESSION["user_id"];
        if ($role == "teacher") {
            
            $group_code = substr(time(), -6); // Takes the last 6 digits of the current timestamp

                $group_name = $name . "'s Group"; // Default group name
                
                $stmt = $conn->prepare("INSERT INTO `groups` (teacher_id, group_code, group_name) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $user_id, $group_code, $group_name);
                $stmt->execute();
            
            header("Location: dashboard_teacher.php");
        } else {
            header("Location: dashboard_student.php");
        }
    } else {
        echo "Signup failed. Please try again.";
    }
   
    
}
?>
