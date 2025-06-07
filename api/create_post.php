<?php
// create_post.php
session_start();
require_once 'config.php';

$logged_in = isset($_SESSION['user_id']);
$user_id = $logged_in ? $_SESSION['user_id'] : null;

if (!$logged_in) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    
    if (!empty($title) && !empty($content)) {
        $posts = json_decode(file_get_contents($posts_file), true);
        $new_post = [
            'id' => count($posts) + 1,
            'title' => $title,
            'content' => $content,
            'user_id' => $user_id,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $posts[] = $new_post;
        file_put_contents($posts_file, json_encode($posts, JSON_PRETTY_PRINT));
        header("Location: index.php");
        exit;
    } else {
        $error = "Title and content are required!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
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
        .form-control {
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background: #ffffff;
            box-shadow: 0 0 10px rgba(102, 217, 239, 0.5);
        }
        .btn {
            transition: transform 0.3s ease, background 0.3s ease;
        }
        .btn:hover {
            transform: scale(1.05);
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

    <div class="container mt-4">
        <h1>Create New Post</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Content</label>
                <textarea name="content" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Post</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>