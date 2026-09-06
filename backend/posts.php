<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {


$sql = '
SELECT
    u.username,
    p.id,
    p.body,
    p.created_at
FROM posts AS p
INNER JOIN users AS u
    ON p.user_id = u.id
ORDER BY p.created_at DESC
';

$stmt = $pdo->query($sql);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($posts, JSON_UNESCAPED_UNICODE);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $body = $data['body'] ?? '';
    $userId = 1; // 今は仮。ログイン実装後に変更

    // 空投稿チェック
    if (trim($body) === '') {
        http_response_code(400);

        echo json_encode([
            'error' => '投稿内容を入力してください'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // 280文字チェック
    if (mb_strlen($body, 'UTF-8') > 280) {
        http_response_code(400);

        echo json_encode([
            'error' => '投稿は280文字以内で入力してください'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO posts (user_id, body)
         VALUES (?, ?)'
    );

    $stmt->execute([
        $userId,
        $body
    ]);

    http_response_code(201);

    echo json_encode([
        'message' => '投稿しました'
    ], JSON_UNESCAPED_UNICODE);
}

if ($method === 'DELETE') {

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    $postId = $data['id'] ?? null;
    $userId = 1;

    if ($postId === null || !is_numeric($postId)) {
        http_response_code(400);

        echo json_encode([
            'error' => '投稿IDが正しくありません'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $stmt = $pdo->prepare(
        'DELETE FROM posts
         WHERE id = ?
         AND user_id = ?'
    );

    $stmt->execute([
        $postId,
        $userId
    ]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);

        echo json_encode([
            'error' => '削除できる投稿が見つかりません'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    echo json_encode([
        'message' => '投稿を削除しました'
    ], JSON_UNESCAPED_UNICODE);
}

if ($method === 'PATCH') {
     $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    $postId = $data['id'] ?? null;
    $body = $data['body'] ?? '';
    $userId = 1;

  
    if ($postId === null || !is_numeric($postId)) {
        http_response_code(400);

        echo json_encode([
            'error' => '投稿IDが正しくありません'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    if (trim($body) === '') {
        http_response_code(400);

        echo json_encode([
            'error' => '投稿内容を入力してください'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    if (mb_strlen($body, 'UTF-8') > 280) {
        http_response_code(400);

        echo json_encode([
            'error' => '投稿は280文字以内で入力してください'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    $stmt = $pdo->prepare(
        'UPDATE posts
        SET body = ?
        WHERE id = ?
        AND user_id = ?'
    );

    $stmt->execute([
        $body,
        $postId,
        $userId
    ]);

    http_response_code(200);

    echo json_encode([

        'message' => '投稿を編集しました'
    ], JSON_UNESCAPED_UNICODE);


}