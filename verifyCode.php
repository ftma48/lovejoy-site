<?php
session_start();

// CSRF token validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    exit('Invalid CSRF token.');
}

unset($_SESSION['csrf_token']);

// check code was submitted
if (!isset($_POST['code'])) {
    exit('Please enter the verification code.');
}

$submittedCode = $_POST['code'];
$correctCode = $_SESSION['verification_code'] ?? '';

if ($submittedCode === $correctCode) {
    $email = $_SESSION['pending_email'] ?? '';

    if ($email) {
        $mysql_host = "localhost";
        $mysql_database = "lovejoy";
        $mysql_user = "root";
        $mysql_password = "";

        $con = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_database);
        if (!$con) exit('Database connection failed.');

        $stmt = $con->prepare("UPDATE users SET verified = 1 WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();
        $con->close();

        unset($_SESSION['verification_code']);
        unset($_SESSION['pending_email']);

        echo "Email verified successfully! You can now <a href='loginForm.php'>log in</a>.";
    } else {
        exit('No pending email found in session.');
    }
} else {
    echo "Invalid verification code. Please try again.";
}
?>
