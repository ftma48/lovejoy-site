<?php
session_start();
require 'C:/Users/falar/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mysql_host = "localhost";
$mysql_database = "lovejoy";
$mysql_user = "root";
$mysql_password = "";

$con = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_database);
if (!$con) exit('Database connection failed.');

// CSRF token validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    exit('Invalid CSRF token.');
}
unset($_SESSION['csrf_token']);

$email = $_POST['email'] ?? '';
$emailSanitized = htmlspecialchars($email);

// check if email exists using prepared statements
$stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user) {
    $token = bin2hex(random_bytes(50));
    $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

    $stmt = $con->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
    $stmt->bind_param("ssi", $token, $expires, $user['id']);
    $stmt->execute();
    $stmt->close();

    $_SESSION['pending_email'] = $emailSanitized;

    // send reset email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'e0e287d3eb8689';
        $mail->Password = '4aa88043356716';

        $mail->setFrom('noreply@lovejoy.com', 'Lovejoy');
        $mail->addAddress($emailSanitized);
        $mail->isHTML(false);
        $mail->Subject = 'Password Reset Request';
        $resetLink = "http://localhost/coursework/resetPassword.php?token=$token";
        $mail->Body = "You requested a password reset. Click the link to reset your password: $resetLink";
        $mail->send();
    } catch (Exception $e) {
        exit("Failed to send reset email. Please try again later.");
    }
}

echo "If your email exists in our system, a password reset link has been sent.";
$con->close();
?>
