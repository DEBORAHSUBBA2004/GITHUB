<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // Check if the last activity time is set
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 30)) {
        // If last activity was more than 30 seconds ago, destroy the session and redirect to login page
        session_unset(); 
        session_destroy();
        header("Location: index.html");
        exit();
    }

    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    // Fetch the user's name and email from the users table
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($userName, $userEmail);
    $stmt->fetch();
    $stmt->close();
    
    // Fetch the profile picture path from the profile_pictures table
    $stmt = $conn->prepare("SELECT profile_picture_path FROM profile_pictures WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($profilePicturePath);
    $stmt->fetch();
    $stmt->close();

    // Set a default profile picture if none is uploaded
    if (empty($profilePicturePath) || !file_exists($profilePicturePath)) {
        $profilePicturePath = 'default-profile.png';
    }
} else {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit();
}

// Handle profile picture upload via AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile-picture']) && isset($_POST['action']) && $_POST['action'] == 'upload-profile-picture') {
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["profile-picture"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a valid image
    if (!empty($_FILES["profile-picture"]["tmp_name"]) && getimagesize($_FILES["profile-picture"]["tmp_name"])) {
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["profile-picture"]["tmp_name"], $targetFile)) {
            // Update the profile picture path in the profile_pictures table
            $stmt = $conn->prepare("INSERT INTO profile_pictures (user_id, profile_picture_path) VALUES (?, ?) ON DUPLICATE KEY UPDATE profile_picture_path = ?");
            $stmt->bind_param("iss", $userId, $targetFile, $targetFile);
            $stmt->execute();
            $stmt->close();

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error uploading profile picture.";
        }
    } else {
        echo "File is not a valid image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div class="container">
        <div class="sidebar">
            <div class="profile-picture-upload">
                <img id="profile-picture-display" src="<?php echo htmlspecialchars($profilePicturePath); ?>" alt="Profile Picture">
                <form action="dashboard.php" method="post" enctype="multipart/form-data">
                    <label for="file-upload" class="upload-button">
                        <i class="fas fa-upload"></i> Upload Profile Picture
                    </label>
                    <input type="file" name="profile-picture" id="file-upload" accept="image/jpeg, image/png" style="display: none;">
                    <input type="hidden" name="action" value="upload-profile-picture">
                    <input type="submit" value="Submit" class="submit-button">
                </form>
            </div>
            <div class="user-info">
                <p>User ID: <span id="user-id"><?php echo htmlspecialchars($userId); ?></span></p>
                <p>Username: <span id="username"><?php echo htmlspecialchars($userName); ?></span></p>
                <p>Email: <span id="user-email"><?php echo htmlspecialchars($userEmail); ?></span></p>
            </div>
            <button id="logout-button" onclick="window.location.href='index.html'">Logout</button>
        </div>
        <div class="body">
            <h1>Welcome, <?php echo htmlspecialchars($userName); ?>!</h1>
            <div class="about-me">
                <h2>Here's More About Me</h2>
                <div class="about-me-content">
                    <p id="about-me-description">
                        Hi there! I'm Deborah Subba, currently based in Singtam, Sikkim. I am pursuing a Bachelor of Computer Applications (BCA) and have a strong interest in web development. As a dedicated student, I am constantly learning and honing my skills to become proficient in creating dynamic and user-friendly websites.
                        My journey in web development has been fueled by a passion for technology and innovation. I enjoy exploring new tools and methodologies to enhance my capabilities and deliver exceptional web solutions.
                        When I'm not studying or coding, I love to engage in activities that inspire creativity and continuous learning. I am excited to share my experiences and insights with you.
                        Feel free to reach out if you'd like to connect or collaborate. <a href="index.html">Learn More</a>
                    </p>
                    <img src="de.jpeg" alt="Owner's Image" id="owner-image">
                </div>
            </div>
            <div class="social-links">
                <a href="https://github.com/owner" target="_blank"><i class="fab fa-github"></i></a>
                <a href="https://linkedin.com/in/owner" target="_blank"><i class="fab fa-linkedin"></i></a>
                <a href="https://instagram.com/owner" target="_blank"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
</body>
</html>
