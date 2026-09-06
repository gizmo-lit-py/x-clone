<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (
        trim($username) === '' ||
        trim($email) === '' ||
        $password === ''
    ) {
        http_response_code(400);

        echo json_encode([
            'error' => 'すべての項目を入力してください'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


}