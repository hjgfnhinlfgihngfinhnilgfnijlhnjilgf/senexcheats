<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $invite_code = $_POST['invite_code'];

    // Load invite codes
    $invites = json_decode(file_get_contents($invites_file), true);

    // Verify invite code
    $invite_valid = false;
    $invite_index = null;
    foreach ($invites as $index => $invite) {
        if (password_verify($invite_code, $invite['code']) && !$invite['used']) {
            $invite_valid = true;
            $invite_index = $index;
            break;
        }
    }

    if ($invite_valid) {
        // Load users
        $users = json_decode(file_get_contents($users_file), true);
        
        // Generate unique UID
        $uid = 'UID' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        
        // Create new user
        $new_user = [
            'id' => count($users) + 1,
            'uid' => $uid,
            'username' => $username,
            'password' => $password,
            'is_admin' => false,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Add user to users.json
        $users[] = $new_user;
        file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));

        // Mark invite code as used
        $invites[$invite_index]['used'] = true;
        $invites[$invite_index]['used_by'] = $new_user['id'];
        file_put_contents($invites_file, json_encode($invites, JSON_PRETTY_PRINT));

        header("Location: login.php");
        exit;
    } else {
        $error = "Invalid or used invite code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Register</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Invite Code</label>
                <input type="text" name="invite_code" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</body>
</html>