<?php
require_once __DIR__ . '/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;
$app->post('/', function (Request $request, Response $response) {
    $client = new GuzzleHttp\Client();

    $body = json_decode($request->getBody(), true);

    foreach ($body['result'] as $msg) {
        $resContent = $msg['content'];
        $cli = new RakutenRws_Client();
        $cli->setApplicationId('1030243823320196712');
        $cli->setAffiliateId('0ca3304d.a811038d.0ca3304e.80024f1e');
        $res = $cli->execute('IchibaItemSearch', array(
            'keyword' => $msg['content']['text'],
            'hits' => 3,
            'carrier' => 2
        ));
        if (!$res->isOk()) {
            error_log(__FILE__.":".__LINE__.":".$res->getMessage());
            continue;
        }
        $resContent['text'] = "";
        foreach ($res['Items'] as $item) {
            $resContent['text'] = $item['Item']['itemUrl'];
        }

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

        try {
            $client->request('post', 'https://trialbot-api.line.me/v1/events', $requestOptions);
        } catch (Exception $e) {
            error_log(__FILE__.":".__LINE__.":".$e->getMessage());
        }
    }

    return $response;
});
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(getenv('FIXIE_URL')."\n".
        getenv('LINE_CHANNEL_ID')."\n".
        getenv('LINE_CHANNEL_SECRET')."\n".
        getenv('LINE_CHANNEL_MID'));
    return $response;
});
$app->run();

