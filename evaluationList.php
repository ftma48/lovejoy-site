<?php
session_start();

// only allow admin users
if (!isset($_SESSION['account_loggedin']) || $_SESSION['account_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// database connection
$mysql_host = "localhost";
$mysql_database = "lovejoy";
$mysql_user = "root";
$mysql_password = "";

$con = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_database);
if ($con->connect_errno) {
    exit('Failed to connect to MySQL: ' . $con->connect_error);
}

// fetch evaluation requests
$query = "
    SELECT e.id, u.forename, u.surname, e.details, e.contact_method, e.photo, e.submitted_at
    FROM evaluations e
    JOIN users u ON e.user_id = u.id
    ORDER BY e.submitted_at DESC
";

$result = $con->query($query);
if (!$result) {
    exit('Database query error: ' . $con->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Evaluation Requests</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        img.preview {
            max-width: 100px;
            border: 1px solid #ccc;
            padding: 2px;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>Evaluation Requests</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Details</th>
                    <th>Contact Method</th>
                    <th>Photo</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['forename'] . " " . $row['surname'], ENT_QUOTES) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['details'], ENT_QUOTES)) ?></td>
                        <td><?= htmlspecialchars($row['contact_method'], ENT_QUOTES) ?></td>S
                        <td>
                            <?php if ($row['photo']): ?>
                                <a href="<?= htmlspecialchars($row['photo'], ENT_QUOTES) ?>" target="_blank">
                                    <img src="<?= htmlspecialchars($row['photo'], ENT_QUOTES) ?>" class="preview" alt="Uploaded Image">
                                </a>
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['submitted_at'], ENT_QUOTES) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <p><a href="home.php" class="btn">Return Home</a></p>
    </div>
</body>
</html>

<?php
$result->free();
$con->close();
?>
