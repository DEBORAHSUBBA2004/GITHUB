<?php
include 'db.php';
session_start();

const MAX_ATTEMPTS = 3;
const LOCKOUT_TIME = 2 * 60; // 2 minutes in seconds

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    try {
        // Verify reCAPTCHA response
        $recaptchaSecret = '6LdiZPApAAAAAFHeLQjYrUrOo0BTiFII23FaWpw-';
        $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify?' . http_build_query(array(
            'secret' => $recaptchaSecret,
            'response' => $recaptchaResponse
        ));
        $response = file_get_contents($recaptchaUrl);
        $responseKeys = json_decode($response, true);

        if (!$responseKeys["success"]) {
            echo "Please complete the CAPTCHA.";
            exit();
        }
    } catch (Exception $e) {
        echo "Error fetching reCAPTCHA response: " . $e->getMessage();
        exit();
    }

    // Sanitize input to prevent SQL injection
    $email = mysqli_real_escape_string($conn, $email);

    // Check for lockout
    $lockoutExpiry = isset($_SESSION['lockout_expiry'][$email]) ? $_SESSION['lockout_expiry'][$email] : 0;
    if (time() < $lockoutExpiry) {
        echo "Account is locked. Please try again later.";
        exit();
    }

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE LOWER(email) = LOWER(?)");
    if ($stmt === false) {
        echo "Error preparing statement: " . htmlspecialchars($conn->error);
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $stored_password);
        $stmt->fetch();

        if (password_verify($password, $stored_password)) {
            // Reset login attempts and lockout on successful login
            unset($_SESSION['login_attempts'][$email]);
            unset($_SESSION['lockout_expiry'][$email]);

            if (isset($_POST['remember-me']) && $_POST['remember-me'] == 'on') {
                // Set a cookie to remember the user
                setcookie('rememberMe', $email, time() + (60 * 60 * 24 * 30)); // 30 days
            }

            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            header("Location: dashboard.php");
            exit();
        } else {
            // Increment login attempts
            $_SESSION['login_attempts'][$email] = isset($_SESSION['login_attempts'][$email]) ? $_SESSION['login_attempts'][$email] + 1 : 1;

            // Check if maximum attempts have been reached
            if ($_SESSION['login_attempts'][$email] >= MAX_ATTEMPTS) {
                $_SESSION['lockout_expiry'][$email] = time() + LOCKOUT_TIME;
                echo "Too many incorrect attempts. Your account is locked for 2 minutes.";
                exit();
            } else {
                echo "Invalid email or password.";
                exit();
            }
        }
    } else {
        echo "No user found with that email.";
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
