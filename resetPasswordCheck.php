<?php
session_start();

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

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($password !== $confirm) exit("Passwords do not match.");

$stmt = $con->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) exit("Invalid or expired token.");

// update password
$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = $con->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
$stmt->bind_param("si", $hashed, $user['id']);
$stmt->execute();
$stmt->close();
$con->close();

echo "Your password has been reset successfully! You can now <a href='loginForm.php'>log in</a>.";
?>
