<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/db.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware(); // Bắt buộc để nhận JSON
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
});

// Xử lý preflight
// $app->options('/{routes:.+}', function (Request $request, Response $response) {
//     return $response;
// });

$app->add(function ($request, $handler) {
    error_log('Path: ' . $request->getUri()->getPath());
    return $handler->handle($request);
});

$app->get('/', function (Request $request, Response $response) use ($pdo) {
    $stmt = $pdo->query("SELECT * FROM posts");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($posts));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/create', function (Request $request, Response $response) use ($pdo) {
    $data = $request->getParsedBody();
    $stmt = $pdo->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
    $stmt->execute([$data['title'], $data['content']]);
    $response->getBody()->write(json_encode(["msg" => "Post created"]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
