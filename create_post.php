<?php
session_start();

// Load posts
$posts_file = 'data/posts.json';
$posts = file_exists($posts_file) ? json_decode(file_get_contents($posts_file), true) : [];

// Load users for username lookup
$users_file = 'data/users.json';
$users = file_exists($users_file) ? json_decode(file_get_contents($users_file), true) : [];

// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);
$user_id = $logged_in ? $_SESSION['user_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite-Only Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Forum</a>
            <div class="navbar-nav">
                <?php if ($logged_in): ?>
                    <a class="nav-link" href="create_post.php">New Post</a>
                    <a class="nav-link" href="profile.php">Profile</a>
                    <?php if ($_SESSION['is_admin']): ?>
                        <a class="nav-link" href="admin.php">Admin讨admin.php">Admin Panel</a>
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
        <?php foreach ($posts as $post): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($post['content']); ?></p>
                    <p class="card-text"><small class="text-muted">Posted by <?php echo htmlspecialchars($users[array_search($post['user_id'], array_column($users, 'id'))]['username']); ?> on <?php echo $post['created_at']; ?></small></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>