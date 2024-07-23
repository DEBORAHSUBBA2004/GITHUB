<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_name'])) {
    $name = $_SESSION['user_name'];
} else {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .navbar {
            background-color: #333;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            flex-shrink: 0;
        }

        .logo {
            font-size: 1.5em;
        }

        .profile {
            display: flex;
            align-items: center;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .profile-name {
            font-size: 1em;
        }

        .container {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .sidebar {
            width: 200px;
            background-color: #f4f4f4;
            padding: 15px;
            height: 100%;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar nav ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar nav ul li {
            margin: 15px 0;
        }

        .sidebar nav ul li a {
            text-decoration: none;
            color: #333;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .sidebar nav ul li a:hover {
            background-color: #ddd;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="logo">Dashboard</div>
        <div class="profile">
            <img src="profile.jpg" alt="Profile Picture" class="profile-img">
            <span class="profile-name"><?php echo htmlspecialchars($name); ?></span>
        </div>
    </header>
    <div class="container">
        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="#">Menu Item 1</a></li>
                    <li><a href="#">Menu Item 2</a></li>
                    <li><a href="#">Menu Item 3</a></li>
                    <li><a href="#">Menu Item 4</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <h1>Welcome to the Dashboard</h1>
            <p>This is the main content area.</p>
        </main>
    </div>
</body>
</html>
