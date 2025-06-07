<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit;
}

// Generate invite code
if (isset($_POST['generate_invite'])) {
    $code = bin2hex(random_bytes(8));
    $invites = json_decode(file_get_contents($invites_file), true);
    $invites[] = [
        'id' => count($invites) + 1,
        'code' => password_hash($code, PASSWORD_DEFAULT),
        'used' => false,
        'used_by' => null,
        'created_at' => date('Y-m-d H:i:s'),
        'plain_code' => $code // Store plain code for display only in admin panel
    ];
    file_put_contents($invites_file, json_encode($invites, JSON_PRETTY_PRINT));
}

// Delete user
 if (isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['user_id'];
    $users = json_decode(file_get_contents($users_file), true);
    $users = array_filter($users, fn($u) => $u['id'] !== $user_id);
    $users = array_values($users); // Reindex array
    file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));
}

// Delete post
if (isset($_POST['delete_post'])) {
    $post_id = (int)$_POST['post_id'];
    $posts = json_decode(file_get_contents($posts_file), true);
    $posts = array_filter($posts, fn($p) => $p['id'] !== $post_id);
    $posts = array_values($posts); // Reindex array
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
</head>
<body>
    <div class="container mt-5">
        <h2>Admin Panel</h2>
        
        <h3>Generate Invite Code</h3>
        <form method="POST">
            <button type="submit" name="generate_invite" class="btn btn-primary">Generate</button>
        </form>
        <h4>Active Invite Codes</h4>
        <ul>
            <?php foreach ($invites as $invite): ?>
                <?php if (!$invite['used']): ?>
                    <li><?php echo htmlspecialchars($invite['plain_code']); ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>

        <h3>Users</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>UID</th>
                    <th>Username</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['uid']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
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
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td><?php echo htmlspecialchars($users[array_search($post['user_id'], array_column($users, 'id'))]['username']); ?></td>
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
    </div>
</body>
</html>