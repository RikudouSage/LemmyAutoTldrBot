#!/usr/bin/php
<?php

use GuzzleHttp\Client;

require '/opt/vendor/autoload.php';

do {
    $request = getNextRequest();
    $handlerCommand = $_ENV['_HANDLER'];

    $payload = $request['payload'];
    if (!is_string($payload)) {
        throw new LogicException('Invalid input, must be a string');
    }

    $response = shell_exec("/var/task/{$handlerCommand} {$payload}");

    sendResponse($request['invocationId'], $response);
} while (true);

function getNextRequest(): array
{
    $client = new Client();
    $response = $client->get('http://' . $_ENV['AWS_LAMBDA_RUNTIME_API'] . '/2018-06-01/runtime/invocation/next');

    return [
        'invocationId' => $response->getHeader('Lambda-Runtime-Aws-Request-Id')[0],
        'payload' => json_decode((string) $response->getBody(), true)
    ];
}

function sendResponse($invocationId, $response): void
{
    $client = new Client();
    $client->post(
        'http://' . $_ENV['AWS_LAMBDA_RUNTIME_API'] . '/2018-06-01/runtime/invocation/' . $invocationId . '/response',
        ['body' => $response]
    );
}
