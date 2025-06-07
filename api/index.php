<?php
// index.php
session_start();
require_once 'config.php';

// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$logged_in = isset($_SESSION['user_id']);
$user_id = $logged_in ? $_SESSION['user_id'] : null;
$username = $logged_in ? $_SESSION['username'] : '';

$posts = file_exists($posts_file) ? json_decode(file_get_contents($posts_file), true) : [];
$users = file_exists($users_file) ? json_decode(file_get_contents($users_file), true) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite-Only Forum</title>
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
        .card {
            background: #ffffff;
            color: #333;
            border: none;
            border-radius: 10px;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .alert {
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            border-radius: 10px;
            animation: slideIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">Forum</a>
            <div class="navbar-nav ms-auto">
                <?php if ($logged_in): ?>
                    <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($username); ?></span>
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

    <div class="container mt-4">
        <h1>Forum Posts</h1>
        <!-- Debug Session State -->
        <div class="alert alert-info" role="alert">
            <strong>Debug:</strong> Logged in: <?php echo $logged_in ? 'Yes' : 'No'; ?><br>
            <strong>User ID:</strong> <?php echo $user_id ?? 'Not set'; ?><br>
            <strong>Username:</strong> <?php echo htmlspecialchars($username) ?: 'Not set'; ?><br>
            <strong>Session Data:</strong> <?php echo print_r($_SESSION, true); ?>
        </div>
        <?php if (!$logged_in): ?>
            <div class="alert alert-info" role="alert">
                Please <a href="login.php" class="alert-link">login</a> or <a href="register.php" class="alert-link">register</a> to view forum posts!
            </div>
        <?php else: ?>
            <?php if (empty($posts)): ?>
                <div class="alert alert-info" role="alert">
                    No posts yet! Be the first to create one.
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($post['content']); ?></p>
                            <p class="card-text">
                                <small class="text-muted">
                                    Posted by <?php echo htmlspecialchars($users[array_search($post['user_id'], array_column($users, 'id'))]['username']); ?> 
                                    on <?php echo $post['created_at']; ?>
                                </small>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>