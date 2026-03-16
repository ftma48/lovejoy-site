<?php
session_start();

// check if 2FA session exists
if (!isset($_SESSION['2fa_user_id'])) {
    header('Location: loginForm.php');
    exit;
}

// CSRF token validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    exit('Invalid CSRF token.');
}
unset($_SESSION['csrf_token']); 

$submitted = $_POST['code'] ?? '';
$correct = $_SESSION['2fa_code'] ?? '';
$expire = $_SESSION['2fa_expire'] ?? 0;

// expiration check
if (time() > $expire) {
    session_unset();
    exit('2FA code expired. Please log in again.');
}

if (hash_equals((string)$correct, (string)$submitted)) { 
    // fully log in user
    session_regenerate_id();
    $_SESSION['account_loggedin'] = TRUE;
    $_SESSION['account_id'] = $_SESSION['2fa_user_id'];
    $_SESSION['account_name'] = $_SESSION['2fa_username'];

    $mysqli = new mysqli("localhost", "root", "", "lovejoy");
    if ($stmt = $mysqli->prepare("SELECT role FROM users WHERE id = ?")) {
        $stmt->bind_param("i", $_SESSION['account_id']);
        $stmt->execute();
        $stmt->bind_result($role);
        $stmt->fetch();
        $_SESSION['account_role'] = $role; // <-- store role
        $stmt->close();
    }
    $mysqli->close();
    
    // clear 2FA session variables
    unset($_SESSION['2fa_user_id'], $_SESSION['2fa_username'], $_SESSION['2fa_code'], $_SESSION['2fa_expire']);

    header('Location: home.php');
    exit;
} else {
    echo 'Invalid 2FA code. Please try again.';
}
?>
