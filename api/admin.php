<?php
// admin.php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['generate_invite'])) {
    $code = bin2hex(random_bytes(8));
    $invites = json_decode(file_get_contents($invites_file), true);
    $invites[] = [
        'id' => count($invites) + 1,
        'code' => password_hash($code, PASSWORD_DEFAULT),
        'used' => false,
        'used_by' => null,
        'created_at' => date('Y-m-d H:i:s'),
        'plain_code' => $code
    ];
    file_put_contents($invites_file, json_encode($invites, JSON_PRETTY_PRINT));
}

if (isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['user_id'];
    $users = json_decode(file_get_contents($users_file), true);
    $users = array_filter($users, fn($u) => $u['id'] !== $user_id);
    $users = array_values($users);
    file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));
}

if (isset($_POST['delete_post'])) {
    $post_id = (int)$_POST['post_id'];
    $posts = json_decode(file_get_contents($posts_file), true);
    $posts = array_filter($posts, fn($p) => $p['id'] !== $post_id);
    $posts = array_values($posts);
    file_put_contents($posts_file, json_encode($posts, JSON_PRETTY_PRINT));
}

$users = json_decode(file_get_contents($users_file), true);
$posts = json_decode(file_get_contents($posts_file), true);
$invites = json_decode(file_get_contents($invites_file), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
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
        .table {
            background: #ffffff;
            color: #333;
            border-radius: 10px;
            overflow: hidden;
        }
        .table th {
            background: #1a3c5e;
            color: #f0f0f0;
        }
        .btn {
            transition: transform 0.3s ease, background 0.3s ease;
        }
        .btn:hover {
            transform: scale(1.05);
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
        h3 {
            color: #66d9ef;
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
    <div class="container mt-5">
        <h2>Admin Panel</h2>
        
        <h3>Generate Invite Code</h3>
        <form method="POST">
            <button type="submit" name="generate_invite" class="btn btn-primary">Generate New Code</button>
        </form>
        <div class="card mt-3">
            <div class="card-body">
                <h4>Active Invite Codes</h4>
                <?php if (empty($invites)): ?>
                    <p>No active invite codes.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($invites as $invite): ?>
                            <?php if (!$invite['used']): ?>
                                <li class="list-group-item">
                                    <?php echo htmlspecialchars($invite['plain_code']); ?>
                                    <small class="text-muted"> (Created: <?php echo $invite['created_at']; ?>)</small>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <h3>Users</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>UID</th>
                    <th>Username</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['uid']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Posts</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td><?php echo htmlspecialchars($users[array_search($post['user_id'], array_column($users, 'id'))]['username']); ?></td>
                        <td><?php echo htmlspecialchars($post['created_at']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" name="delete_post" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-secondary">Back to Forum</a>
    </div>
</body>
</html>