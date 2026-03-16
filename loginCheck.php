<?php
session_start();
require 'C:/Users/falar/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// database connection
$servername = "localhost";
$rootUser = "root";
$db = "lovejoy";
$rootPassword = "";

$con = new mysqli($servername, $rootUser, $rootPassword, $db);
if ($con->connect_errno) exit('Failed to connect to MySQL!');

// CSRF token validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    exit('Invalid CSRF token.');
}
unset($_SESSION['csrf_token']); 

// check required fields
if (!isset($_POST['email'], $_POST['password'])) {
    exit('Please fill both the email and password fields!');
}

// sanitize input
$emailInput = htmlspecialchars($_POST['email']);
$passwordInput = $_POST['password'];

// fetch user by email
$stmt = $con->prepare('SELECT id, forename, password, verified, email, failed_attempts, lockout_time 
                       FROM users WHERE email = ?');
$stmt->bind_param('s', $emailInput);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    exit('Incorrect email and/or password!');
}

$stmt->bind_result($id, $forename, $hashedPassword, $emailVerified, $email, $failedAttempts, $lockoutTime);
$stmt->fetch();

// check lockout
if ($lockoutTime && strtotime($lockoutTime) > time()) {
    $remaining = ceil((strtotime($lockoutTime) - time()) / 60);
    exit("Account is temporarily locked. Try again in $remaining minute(s).");
}

// check email verification
if (!$emailVerified) {
    exit('Your email is not verified. Please check your inbox.');
}

// verify password
if (password_verify($passwordInput, $hashedPassword)) {
    // reset failed attempts and lockout
    $stmtReset = $con->prepare("UPDATE users SET failed_attempts = 0, lockout_time = NULL WHERE id = ?");
    $stmtReset->bind_param("i", $id);
    $stmtReset->execute();
    $stmtReset->close();

    // generate 2FA code
    $code = rand(100000, 999999);
    $_SESSION['2fa_user_id'] = $id;
    $_SESSION['2fa_email'] = $email;
    $_SESSION['2fa_username'] = $forename;
    $_SESSION['2fa_code'] = $code;
    $_SESSION['2fa_expire'] = time() + 300; // valid for 5 minutes

    // send 2FA email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'e0e287d3eb8689';
        $mail->Password = '4aa88043356716';

        $mail->setFrom('noreply@lovejoy.com', 'Lovejoy');
        $mail->addAddress($email);
        $mail->isHTML(false);
        $mail->Subject = 'Your 2FA Verification Code';
        $mail->Body = "Your 2FA code is: $code. It is valid for 5 minutes.";
        $mail->send();
    } catch (Exception $e) {
        exit("Failed to send 2FA code. Try again later.");
    }

    header('Location: twofa.php');
    exit;

} else {
    // incorrect password
    $failedAttempts++;
    $lockout = NULL;
    if ($failedAttempts > 2) {
        $lockout = date("Y-m-d H:i:s", strtotime('+5 minutes'));
        $failedAttempts = 0;
    }

    $stmtUpdate = $con->prepare("UPDATE users SET failed_attempts = ?, lockout_time = ? WHERE id = ?");
    $stmtUpdate->bind_param("isi", $failedAttempts, $lockout, $id);
    $stmtUpdate->execute();
    $stmtUpdate->close();

    if ($lockout) {
        exit("Too many failed attempts. Account locked for 5 minutes.");
    } else {
        exit("Incorrect email and/or password!");
    }
}

$stmt->close();
$con->close();
?>
