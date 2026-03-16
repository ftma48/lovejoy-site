<?php
session_start();

$mysql_host="localhost";
$mysql_database="lovejoy";
$mysql_user="root";
$mysql_password="";

// CSRF token validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    exit('Invalid CSRF token.');
}

unset($_SESSION['csrf_token']);

// connect to MySQL
$con = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_database);
if (!$con) exit('Database connection failed.');

$required_fields = ['txtForename', 'txtSurname', 'txtEmail1', 'txtEmail2', 'txtPassword1', 'txtPassword2', 'txtNumber'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) exit('Please complete the registration form.');
}

// sanitize input
$forename = htmlspecialchars($_POST['txtForename']);
$surname = htmlspecialchars($_POST['txtSurname']);
$email1 = htmlspecialchars($_POST['txtEmail1']);
$email2 = htmlspecialchars($_POST['txtEmail2']);
$number = htmlspecialchars($_POST['txtNumber']);


$password1 = $_POST['txtPassword1'];
$password2 = $_POST['txtPassword2'];

// check matching emails
if ($email1 !== $email2) exit('Email addresses do not match!');

// validate email format
if (!filter_var($email1, FILTER_VALIDATE_EMAIL)) exit('Invalid email address.');

// check matching passwords
if ($password1 !== $password2) exit('Passwords do not match!');

// password strength
$pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
if (!preg_match($pattern, $password1)) {
    exit('Password must be at least 8 characters, include 1 uppercase, 1 lowercase, 1 number, and 1 special character!');
}

// hash password
$hashed_password = password_hash($password1, PASSWORD_DEFAULT);

// insert user
$stmt = $con->prepare('INSERT INTO users (forename, surname, password, email, number) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('sssss', $forename, $surname, $hashed_password, $email1, $number);

if ($stmt->execute()) {
    $_SESSION['pending_email'] = $email1;
    header('Location: emailVerify.php');
    exit;
} else {
    exit('Failed to register user. Please try again.');
}

$stmt->close();
$con->close();
?>
