<?php
require_once __DIR__ . '/../../config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        .navbar {
            background: #4CAF50;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            font-size: 24px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .navbar a:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .welcome {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
        }

        .content {
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <h1>My App</h1>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($username); ?>!</span>
            <a href="../auth/logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome">Welcome to Your Dashboard!</div>
        <div class="content">
            <p>You are successfully logged in. This is your home page.</p>
            <p>You can now add more features and functionality to your application.</p>
        </div>
    </div>
</body>

</html>