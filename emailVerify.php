<?php
require 'C:/Users/falar/vendor/autoload.php';
$mysql_host="localhost";
$mysql_database="lovejoy";
$mysql_user="root";
$mysql_password="";

$con = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_database) or die("Could not connect to the server");
if (mysqli_connect_errno()) exit('Failed to connect to MySQL: ' . mysqli_connect_error());

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendVerificationEmail($email, $verificationCode) {
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
        $mail->Subject = 'Email Verification';
        $mail->Body    = "Welcome to Lovejoy! Your verification code is: $verificationCode";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function generateVerificationCode($length = 6) {
    $characters = '0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

session_start();

// generate a fresh CSRF token for this form
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$email = $_SESSION['pending_email'];
$verificationCode = generateVerificationCode();
$_SESSION['verification_code'] = $verificationCode;

if (!sendVerificationEmail($email, $verificationCode)) {
    echo "Failed to send the verification email. Please try again later.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Email</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="login">
    <h1>Email Verification</h1>
    <p>A verification code has been sent to <?php echo htmlspecialchars($_SESSION['pending_email']); ?>. Please enter it below:</p>

    <form action="verifyCode.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <label class="form-label" for="code">Verification Code:</label>
        <div class="form-group">
            <input class="form-input" type="text" name="code" id="code" required>
        </div>
        <button class="btn blue" type="submit">Verify</button>
    </form>
</div>
</body>
</html>
