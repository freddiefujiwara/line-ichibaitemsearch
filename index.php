<?php
require_once __DIR__ . '/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("Hello, world");

    return $response;
});
$app->run();
