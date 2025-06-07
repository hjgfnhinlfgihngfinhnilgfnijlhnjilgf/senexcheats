<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$users = json_decode(file_get_contents($users_file), true);
$user = null;
foreach ($users as $u) {
    if ($u['id'] == $user_id) {
        $user = $u;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Profile</h2>
        <p><strong>UID:</strong> <?php echo htmlspecialchars($user['uid']); ?></p>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <a href="index.php" class="btn btn-primary">Back to Forum</a>
    </div>
</body>
</html>