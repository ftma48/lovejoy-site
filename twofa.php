<?php
session_start();

// check if 2FA is pending
if (!isset($_SESSION['2fa_user_id'])) {
    header('Location: loginForm.php');
    exit;
}

// generate CSRF token 
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Two-Factor Authentication</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="login">
    <h1>Enter 2FA Code</h1>
    <p>A verification code has been sent to your email. Please enter it below:</p>

    <form action="twofaCheck.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <label for="code">2FA Code:</label>
        <div class="form-group">
            <input class="form-input" type="text" name="code" id="code" required>
        </div>
        <button class="btn blue" type="submit">Verify</button>
    </form>
</div>
</body>
</html>
