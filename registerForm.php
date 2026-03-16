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
    <title>Register</title>
</head>
<body>
<div class="login">

    <h1>Register</h1>

    <form action="registerFormCheck.php" method="POST" class="form login-form">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <label class="form-label" for="forename">Forename</label>
        <div class="form-group">
            <input class="form-input" type="text" name="txtForename" id="forename" placeholder="Forename" required>
        </div>

        <label class="form-label" for="surname">Surname</label>
        <div class="form-group">
            <input class="form-input" type="text" name="txtSurname" id="surname" placeholder="Surname" required>
        </div>

        <label class="form-label" for="email1">Email</label>
        <div class="form-group">
            <input class="form-input" type="email" name="txtEmail1" id="email1" placeholder="Email address" required>
        </div>

        <label class="form-label" for="email2">Confirm Email</label>
        <div class="form-group">
            <input class="form-input" type="email" name="txtEmail2" id="email2" placeholder="Confirm Email address" required>
        </div>

        <label class="form-label" for="password1">Password</label>
        <div class="form-group">
            <input class="form-input" type="password" name="txtPassword1" id="password1" placeholder="Password" required>
        </div>

        <p id="message">Password is <span id="strength"></span></p> 
        <p id="req">Password must contain minimum 8 characters, 1 uppercase, 1 lowercase, 1 number, 1 special character</p>

        <label class="form-label" for="password2">Confirm Password</label>
        <div class="form-group">
            <input class="form-input" type="password" name="txtPassword2" id="password2" placeholder="Confirm Password" required>
        </div>

        <label class="form-label" for="number">Telephone Number</label>
        <div class="form-group">
            <input class="form-input" type="text" name="txtNumber" id="number" placeholder="Number" required>
        </div>

        <button class="btn blue" type="submit">Register</button>
        <p class="register-link">Already have an account? <a href="loginForm.php" class="form-link">Login</a></p>
    </form>
</div>

<script>
    var pass = document.getElementById("password1");
    var msg = document.getElementById("message");
    var str = document.getElementById("strength");
    var req = document.getElementById("req");

    pass.addEventListener('input', () => {

        const pwd = pass.value;

        msg.style.display = pwd.length > 0 ? "block" : "none";

        const hasLower = /[a-z]/.test(pwd);
        const hasUpper = /[A-Z]/.test(pwd);
        const hasNumber = /[0-9]/.test(pwd);
        const hasSpecial = /[^A-Za-z0-9]/.test(pwd);
        const minLength = pwd.length >= 8;

        if (hasLower && hasUpper && hasNumber && hasSpecial && minLength) {
            req.style.display = "none";
        } else if (pwd.length > 0) {
            req.style.display = "block";
        } else {
            req.style.display = "none";
        }

        let score = 0;
        if (minLength) score++;
        if (pwd.length >= 10) score++;
        if (hasLower) score++;
        if (hasUpper) score++;
        if (hasNumber) score++;
        if (hasSpecial) score++;

        if (score <= 2) {
            str.innerHTML = "Weak";
            str.style.color = "red";
        }
        else if (score <= 4) {
            str.innerHTML = "Medium";
            str.style.color = "orange";
        }
        else if (score <= 5) {
            str.innerHTML = "Strong";
            str.style.color = "green";
        }
        else {
            str.innerHTML = "Very Strong";
            str.style.color = "#0d7a0d";
        }
    });
</script>

</body>
</html>
