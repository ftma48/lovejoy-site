<?php
session_start();

// restrict access to logged-in users
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: index.php');
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
    <title>Request Evaluation</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="login">
    <h1>Request Evaluation</h1>
    <form action="submitEvaluation.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <label class="form-label" for="details">Details of Object / Request:</label>
        <div class="form-group">
            <textarea class="form-input" name="details" id="details" rows="6" placeholder="Describe your object and request..." required></textarea>
        </div>

        <label class="form-label" for="contact">Preferred Contact Method:</label>
        <div class="form-group">
            <select class="form-input" name="contact" id="contact" required>
                <option value="">-- Select --</option>
                <option value="phone">Phone</option>
                <option value="email">Email</option>
            </select>
        </div>

        <label class="form-label" for="photo">Upload Photo of Object:</label>
        <div class="form-group">
            <input class="form-input" type="file" name="photo" id="photo" accept="image/*">
        </div>

        <button type="submit" class="btn btn-full">Submit Evaluation Request</button>
    </form>
</div>
</body>
</html>
