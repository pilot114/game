<?php

include __DIR__ . '/../vendor/autoload.php';

$yourApiKey = getenv('YOUR_API_KEY');
$client = OpenAI::client($yourApiKey);

$result = $client->chat()->create([
    'model' => 'gpt-4',
    'messages' => [
        ['role' => 'user', 'content' => 'Hello!'],
    ],
]);

dump($result->choices[0]->message);

$response = $client->models()->list();
foreach ($response->data as $result) {
    dump($result);
}