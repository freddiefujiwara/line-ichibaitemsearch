<?php
require_once __DIR__ . '/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;
$app->post('/', function (Request $request, Response $response) {
    error_log(__FILE__.":".__LINE__);
    $client = new GuzzleHttp\Client();
    error_log(__FILE__.":".__LINE__);

    $body = json_decode($request->getBody(), true);
    error_log(__FILE__.":".__LINE__);

    foreach ($body['result'] as $msg) {
    error_log(__FILE__.":".__LINE__);
        $resContent = $msg['content'];
        $resContent['text'] = 'hello';

    error_log(__FILE__.":".__LINE__);
        $requestOptions = [
            'body' => json_encode([
                'to' => [$msg['content']['from']],
                'toChannel' => 1383378250, # Fixed value
                'eventType' => '138311608800106203', # Fixed value
                'content' => $resContent,
            ]),
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
                'X-Line-ChannelID' => getenv('LINE_CHANNEL_ID'),
                'X-Line-ChannelSecret' => getenv('LINE_CHANNEL_SECRET'),
                'X-Line-Trusted-User-With-ACL' => getenv('LINE_CHANNEL_MID'),
            ],
            'proxy' => [
                'https' => getenv('FIXIE_URL'),
            ]
        ];
    error_log(__FILE__.":".__LINE__);

        try {
            $client->request('post', 'https://trialbot-api.line.me/v1/events', $requestOptions);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    return $response;
});
$app->get('/', function (Request $request, Response $response) {
    error_log(__FILE__.":".__LINE__);
    $response->getBody()->write(getenv('FIXIE_URL')."\n".
                getenv('LINE_CHANNEL_ID')."\n".
                getenv('LINE_CHANNEL_SECRET')."\n".
                getenv('LINE_CHANNEL_MID'));
    error_log(__FILE__.":".__LINE__);
    return $response;
});
$app->run();
