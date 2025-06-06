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
$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response;
});

$app->add(function ($request, $handler) {
    error_log('Path: ' . $request->getUri()->getPath());
    return $handler->handle($request);
});

$app->post('/trigger-build', function (Request $request, Response $response) {
    $username = "admin";
    $api_token = "11d20e0a3bbde6f09c86cc6fbf67fea942";

    // Lấy crumb
    $crumbUrl = "http://localhost:8080/crumbIssuer/api/json";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $crumbUrl);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$api_token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $crumbResponse = curl_exec($ch);
    $httpCodeCrumb = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCodeCrumb !== 200) {
        $response->getBody()->write(json_encode([
            'error' => 'Failed to get Jenkins crumb',
            'jenkins_response' => $crumbResponse,
            'http_code' => $httpCodeCrumb
        ]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    $crumbData = json_decode($crumbResponse, true);
    $crumbField = $crumbData['crumbRequestField'] ?? null;
    $crumb = $crumbData['crumb'] ?? null;

    if (!$crumb || !$crumbField) {
        $response->getBody()->write(json_encode([
            'error' => 'Invalid crumb response'
        ]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    // Gửi lệnh build
    $data = $request->getParsedBody();
    $jobName = $data['job'] ?? 'your-default-job';
    $buildUrl = "http://localhost:8080/job/{$jobName}/build";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $buildUrl);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$api_token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "$crumbField: $crumb"
    ]);
    $responseBody = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $response->getBody()->write(json_encode([
        'jenkins_http_code' => $httpCode,
        'jenkins_response' => $responseBody
    ]));

    return $response->withHeader('Content-Type', 'application/json');
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
