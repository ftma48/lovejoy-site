<?php
session_start();

// generate CSRF token for the form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Forgot Password</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="login">
    <h1>Forgot Password</h1>
    <form action="forgotPasswordCheck.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <label class="form-label" for="email">Enter your email:</label>
        <div class="form-group">
            <input class="form-input" type="email" name="email" id="email" required>
            <button class="btn" type="submit">Send Reset Link</button>
        </div>
    </form>
</div>
</body>
</html>
