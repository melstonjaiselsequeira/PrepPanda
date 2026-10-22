<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Study Planner</title>
    <style>
        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Arial", sans-serif;
        }

        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            text-align: center;
            padding: 50px 20px;
            overflow-x: hidden;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-in-out;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            animation: slideDown 1s ease-in-out;
        }

        p {
            font-size: 1.2em;
            line-height: 1.5;
            margin: 10px 0;
            animation: fadeIn 1.5s ease-in-out;
        }

        .team {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .member {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 10px;
            width: 200px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
            cursor: pointer;
        }

        .member:hover {
            transform: scale(1.1);
        }

        button {
            margin-top: 20px;
            padding: 12px 20px;
            background: white;
            color: #6a11cb;
            font-size: 1.1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            background: #ddd;
            transform: scale(1.05);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Prep Panda</h1>
        <p>PrepPanda is a powerful tool designed to help students and teachers manage academic tasks effectively.</p>
        <p>With task tracking, notes sharing, and a structured timetable, it simplifies learning and enhances collaboration.</p>
        
        <h2>Our Team 👨‍💻👩‍💻</h2>
        <div class="team">
            <div class="member">🚀 Anvith</div>
            <div class="member">🚀 Dishmith</div>
            <div class="member">🚀 Melston</div>
        </div>

        <button onclick="goBack()">⬅ Back to Home</button>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>

</body>
</html>
