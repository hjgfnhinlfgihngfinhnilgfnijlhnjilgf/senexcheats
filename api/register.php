<?php
// register.php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $invite_code = $_POST['invite_code'];

    $invites = json_decode(file_get_contents($invites_file), true);

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
        $users = json_decode(file_get_contents($users_file), true);
        
        $uid = 'UID' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        
        $new_user = [
            'id' => count($users) + 1,
            'uid' => $uid,
            'username' => $username,
            'password' => $password,
            'is_admin' => false,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $users[] = $new_user;
        file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));

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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2b5876, #4e4376);
            color: #f0f0f0;
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
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
    <div class="container mt-5">
        <h2>Register</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
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
            <a href="login.php" class="btn btn-secondary">Back to Login</a>
        </form>
    </div>
</body>
</html>