<?php
session_start();

// if the user is logged in, redirect to the home page
if (isset($_SESSION['account_loggedin'])) {
    header('Location: home.php');
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
    <meta name="viewport" content="width=device-width,minimum-scale=1">
    <link href="style.css" rel="stylesheet" type="text/css">
    <title>Login</title>
</head>
<body>
<div class="login">

    <h1>Member Login</h1>

    <form action="loginCheck.php" method="POST" class="form login-form">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <label class="form-label" for="email">Email</label>
        <div class="form-group">
            <input class="form-input" type="email" name="email" placeholder="Email address" id="email" required>
        </div>

        <label class="form-label" for="password">Password</label>
        <div class="form-group mar-bot-5">
            <input class="form-input" type="password" name="password" placeholder="Password" id="password" required>
        </div>

        <p class="forgot-link">Forgot password? Click <a href="forgotPassword.php" class="form-link">here</a></p>

        <button class="btn blue" type="submit">Login</button>

        <p class="register-link">Don't have an account? <a href="registerForm.php" class="form-link">Register</a></p>
    </form>
</div>
</body>
</html>
