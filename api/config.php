<?php
// config.php
$data_dir = '/tmp/data';

if (!file_exists($data_dir)) {
    mkdir($data_dir, 0755, true);
}

$users_file = $data_dir . '/users.json';
$posts_file = $data_dir . '/posts.json';
$invites_file = $data_dir . '/invite_codes.json';

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
    $invite_codes = [
        [
            'id' => 1,
            'code' => password_hash('9F3A6C7D8B1E2G0H4J5K6L7M8N9P0QR', PASSWORD_DEFAULT),
            'used' => false,
            'used_by' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'plain_code' => '9F3A6C7D8B1E2G0H4J5K6L7M8N9P0QR'
        ],
        [
            'id' => 2,
            'code' => password_hash('ABCD1234EFGH5678IJKL9012MNOP3456', PASSWORD_DEFAULT),
            'used' => false,
            'used_by' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'plain_code' => 'ABCD1234EFGH5678IJKL9012MNOP3456'
        ]
    ];
    file_put_contents($invites_file, json_encode($invite_codes, JSON_PRETTY_PRINT));
}
?>