<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .password-container {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }
        .password-container input {
            width: 100%;
            padding-right: 40px; /* Space for the eye icon */
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            cursor: pointer;
            font-size: 18px;
            color: #666;
            background: transparent;
            border: none;
            outline: none;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>Login</h2>
        <form action="process_login.php" method="POST">
            <input type="email" name="email" value="" placeholder="Email" required>
            <div class="password-container">
                <input type="password" name="password" id="password" placeholder="Password" required >
                <span class="toggle-password" onclick="togglePassword()">👁</span>
            </div>
            <button type="submit">Login</button><br>
            <a href="signup.php">Dont't have a account</a>
        </form>
    </div>
    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
