<?php
// profile.php
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2b5876, #4e4376);
            color: #f0f0f0;
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
        }
        .navbar {
            background: #1a3c5e;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        .navbar-brand, .nav-link, .navbar-text {
            color: #f0f0f0 !important;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .nav-link:hover, .navbar-text:hover {
            color: #66d9ef !important;
            transform: translateY(-2px);
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-in;
        }
        .btn {
            transition: transform 0.3s ease, background 0.3s ease;
        }
        .btn:hover {
            transform: scale(1.05);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">Forum</a>
            <div class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a class="nav-link" href="create_post.php">New Post</a>
                    <a class="nav-link" href="profile.php">Profile</a>
                    <?php if ($_SESSION['is_admin']): ?>
                        <a class="nav-link" href="admin.php">Admin Panel</a>
                    <?php endif; ?>
                    <a class="nav-link" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Login</a>
                    <a class="nav-link" href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Profile</h2>
        <p><strong>UID:</strong> <?php echo htmlspecialchars($user['uid']); ?></p>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Created At:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
        <a href="index.php" class="btn btn-primary">Back to Forum</a>
    </div>
</body>
</html>