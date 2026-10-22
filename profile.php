<?php
session_start();
include 'includes/db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first!'); window.location='login.php';</script>";
    exit;
}

$student_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #667eea, #764ba2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: white;
        }
        .profile-container {
            max-width: 500px;
            background: rgba(255, 255, 255, 0.2);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .profile-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        h2 {
            color: #fff;
            margin-bottom: 15px;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        label {
            font-size: 16px;
            color: #fff;
            display: block;
            text-align: left;
            margin: 10px 0 5px;
        }
        input {
            width: 90%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }
        input:focus {
            border-color: #007bff;
            background: rgba(255, 255, 255, 0.5);
            outline: none;
        }
        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            transition: background 0.3s ease, transform 0.2s ease;
            color: white;
        }
        .save-btn {
            background:rgb(22, 18, 255);
        }
        .logout-btn {
            background: #dc3545;
        }
        .close-btn {
            background-color: rgb(255,255,255,0.1);
  border: none;
  color: white;
  padding: 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  border-radius: 50%;
  width: 60px;
        }
        button:hover {
            transform: scale(1.05);
        }
        .close-btn:hover{
            background-color: rgb(255,255,255,0.5);
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h2>Profile</h2>
    <form action="update_profile.php" method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <button type="submit" class="save-btn">Save</button>
    </form>
    <button onclick="window.location.href='logout.php'" class="logout-btn">Logout</button>
    <button onclick="window.history.back()" class="close-btn">x</button>
    
</div>

</body>
</html>
