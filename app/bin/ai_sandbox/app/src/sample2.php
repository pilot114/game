<?php
declare(strict_types=1);

readonly class Client
{
    public function __construct(
        private string $raw
    ) {
    }

    function request(): void
    {
        echo base64_decode($this->raw);
    }
}

$raw = 'a2V5Xzc0NjkwNDYz';
$client = new Client($raw);
$client->request();
