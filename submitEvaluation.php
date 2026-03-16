<?php
session_start();

// only allow logged-in users
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: index.php');
    exit;
}

// CSRF token validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    exit('Invalid CSRF token.');
}
unset($_SESSION['csrf_token']); 

$mysql_host = "localhost";
$mysql_database = "lovejoy";
$mysql_user = "root";
$mysql_password = "";

$con = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_database);
if ($con->connect_errno) {
    exit('Failed to connect to MySQL: ' . $con->connect_error);
}

// validate POST data
if (!isset($_POST['details'], $_POST['contact'])) {
    exit('Please fill in all required fields.');
}

$details = trim($_POST['details']);
$contact = $_POST['contact'];

if (empty($details) || empty($contact)) {
    exit('Please fill in all required fields.');
}

if (!in_array($contact, ['phone', 'email'])) {
    exit('Invalid contact method selected.');
}

$uploadDir = 'uploads/';
$uploadedFile = null;

$maxFileSize = 5 * 1024 * 1024;

if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['photo'];

        if ($file['size'] > $maxFileSize) {
        exit('File size exceeds the 5 MB limit.');
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        exit('Error uploading file.');
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        exit('Invalid file type. Only JPG, PNG, and GIF allowed.');
    }

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uploadedFile = $uploadDir . uniqid('eval_', true) . '.' . $ext;

    if (!move_uploaded_file($file['tmp_name'], $uploadedFile)) {
        exit('Failed to move uploaded file.');
    }
}

// insert into database using prepared statement
$stmt = $con->prepare("INSERT INTO evaluations (user_id, details, contact_method, photo) VALUES (?, ?, ?, ?)");
$stmt->bind_param(
    "isss",
    $_SESSION['account_id'],
    $details,
    $contact,
    $uploadedFile
);

if ($stmt->execute()) {
    echo '<p>Evaluation request submitted successfully!</p>';
    echo '<p><a href="home.php" class="btn">Return Home</a></p>';
} else {
    echo 'Error submitting evaluation request: ' . htmlspecialchars($stmt->error);
}

$stmt->close();
$con->close();
?>
