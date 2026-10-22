<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Planner</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script defer src="assets/js/script.js"></script>
    <style>
        @keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #667eea, #764ba2);
            animation: gradientBG 10s ease infinite;
            background-size: 400% 400%;
            color: white;
            text-align: center;
            overflow-x: hidden;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            backdrop-filter: blur(5px);
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            transition: 0.3s;
        }
        nav a:hover {
            color: yellow;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            background: yellow;
            color: black;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn.signup {
            background: orange;
        }
        .btn:hover {
            background: white;
            color: black;
        }
        .hero {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: url('assets/images/hero-bg.jpg') no-repeat center center/cover;
            animation: fadeIn 2s ease-in;
        }
        .hero-content {
            max-width: 700px;
            height:150px;
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
            animation: slideUp 1.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .features {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 50px 20px;
            flex-wrap: wrap;
        }
        .feature {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 10px;
            width: 250px;
            transition: transform 0.3s;
        }
        .feature:hover {
            transform: scale(1.1);
        }
        footer {
            padding: 20px;
            background: rgba(0, 0, 0, 0.3);
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo"><img src="assets/images/logo.png" alt="Study Planner Logo">
        <h1>Prep Panda</h1></div>
        <nav>
            <a href="#features">Features</a>
            <a href="about.php">About</a>
            <a href="login.php" class="btn">Login</a>
            <a href="signup.php" class="btn signup">Sign Up</a>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Organize Your Studies Effortlessly</h1>
            <p>Manage tasks, track progress, and collaborate with teachers efficiently.</p><br>
            <a href="signup.php" class="btn big">Get Started</a>
        </div>
    </section>

    <section id="features" class="features">
        <div class="feature">
            <h2>Task Management</h2>
            <p>Assign and track tasks efficiently.</p>
        </div>
        <div class="feature">
            <h2>Notes Sharing</h2>
            <p>Access shared study materials anytime.</p>
        </div>
        <div class="feature">
            <h2>Timetable</h2>
            <p>To schedule your activities.</p>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 StudyPlanner. All Rights Reserved.</p>
    </footer>
</body>
</html>
