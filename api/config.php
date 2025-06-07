<?php
// Ensure data directory exists
if (!file_exists('data')) {
    mkdir('data', 0755, true);
}

// Initialize JSON files if they don't exist
$users_file = 'data/users.json';
$posts_file = 'data/posts.json';
$invites_file = 'data/invite_codes.json';

if (!file_exists($users_file)) {
    file_put_contents($users_file, json_encode([
        [
            'id' => 1,
            'uid' => 'UID000001',
            'username' => 'admin',
	    'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'is_admin' => true,
            'created_at' => date('Y-m-d H:i:s')
        ]
    ], JSON_PRETTY_PRINT));
}

if (!file_exists($posts_file)) {
    file_put_contents($posts_file, json_encode([], JSON_PRETTY_PRINT));
}

if (!file_exists($invites_file)) {
    file_put_contents($invites_file, json_encode([], JSON_PRETTY_PRINT));
}
?>