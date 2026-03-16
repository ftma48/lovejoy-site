<?php
session_start();
// if the user is logged in, redirect to the home page
if (isset($_SESSION['account_loggedin'])) {
    header('Location: home.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1">
        <title>Lovejoy</title>
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div class="login">
            <h1>Welcome to Lovejoy</h1> <br>
            <a href="registerForm.php" class="btn blue">Register</a><br>
            <a href="loginForm.php" class="btn blue">Login</a>
        </div>  
    </body>
</html>