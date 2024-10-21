<?php

use OpenAI\Client;
use OpenAI\Responses\Chat\CreateResponseMessage;
use OpenAI\Responses\Chat\CreateResponseToolCallFunction;

include __DIR__ . '/../../vendor/autoload.php';

function getModels(Client $client): void
{
    $response = $client->models()->list();
    foreach ($response->data as $result) {
        if (str_starts_with($result->id, 'gpt-')) {
            dump($result->id);
//            dump($result->created);
        }
    }
}

/** @param array<CreateResponseMessage | RequestMessage> $messages */
function ask(Client $client, array &$messages, array $tools = []): CreateResponseMessage
{
    $parameters = [
//        'model' => 'gpt-4o-2024-08-06',
        'model' => 'gpt-4o-mini',
        'messages' => array_map(static fn($x): array => $x->toArray(), $messages),
    ];
    if ($tools !== []) {
        $parameters['tools'] = $tools;
    }
    $result = $client->chat()->create($parameters);
    $message = $result->choices[0]->message;

    askLog(json_encode($message->toArray()));

    // $result->choices[0]->finishReason (tool_calls | stop)
//    $result->usage
//    $result->meta()->requestLimit
//    $result->meta()->tokenLimit

    $messages[] = $message;
    return $message;
}

function askLog(string $data): void
{
    $dt = new DateTime();
    $date = $dt->format('d-m-Y');
    $time = $dt->format('i:s') . ':' . substr($dt->format('u'), 0, 3);;

    file_put_contents(__DIR__ . "/logs/$date.log", "$time $data\n", FILE_APPEND | LOCK_EX);
}

function cmd(string $command): string
{
    $command = base64_encode($command);
    return trim(`make cmd CMD="echo '$command' | base64 --decode | bash"`);
}

function colored(string $text, string $color): string {
    $colors = [
        "red" => "\033[31m",
        "green" => "\033[32m",
        "blue" => "\033[34m",
        "magenta" => "\033[35m",
        "reset" => "\033[0m"
    ];

    return $colors[$color] . $text . $colors["reset"];
}

/** @param array<CreateResponseMessage | RequestMessage> $messages */
function prettyPrintConversation(array $messages): void {
    $roleToColor = [
        "system" => "red",
        "user" => "green",
        "assistant" => "blue",
    ];

    foreach ($messages as $message) {
        $toolCallsContent = [];
        if (isset($message->toolCalls)) {
            foreach ($message->toolCalls as $toolCall) {
                $toolCallsContent[] = getCallString($toolCall->function);
            }
        }
        $role = $message->role;
        $content = match ($role) {
            'system', 'user', 'tool' => $message->content,
            'assistant' => $message->content ?? implode(', ', $toolCallsContent),
            default => ''
        };
        echo colored("{$role}: {$content}\n", $roleToColor[$role] ?? 'magenta');
    }
}

function getCallString(CreateResponseToolCallFunction $function): string
{
    $args = [];
    foreach (json_decode($function->arguments) as $name => $value) {
        $args[] = "$name: " . (is_numeric($value) ? $value : "'$value'");
    }
    return sprintf(
        '%s(%s)',
        $function->name,
        implode(', ', $args),
    );
}

function getCallEvaluate(CreateResponseToolCallFunction $function): string
{
    $args = base64_encode($function->arguments);
    return "$function->name(...json_decode(base64_decode('$args'), true))";
}

function loader(int $durationInSeconds): void {
    $spinner = ['|', '/', '-', '\\'];

    $startTime = time();
    $i = 0;

    while ((time() - $startTime) < $durationInSeconds) {
        echo $spinner[$i % count($spinner)] . " Loading...\r";
        fflush(STDOUT);
        usleep(100000); // 100ms
        $i++;
    }
}

function input(string $prompt = ''): string {
    if ($prompt !== '') {
        echo colored("$prompt", 'green');
    }
    $input = fgets(STDIN);
    return trim($input);
}

readonly class RequestMessage
{
    public function __construct(
        public string $role,
        public string $content,
        public ?string $toolCallId = null,
    ) {
    }

    public function toArray(): array
    {
        $result = [
            'role' => $this->role,
            'content' => $this->content,
        ];
        if ($this->toolCallId !== null) {
            $result['tool_call_id'] =  $this->toolCallId;
        }
        return $result;
    }
}

#[Attribute] class Tool
{
    public function __construct(
        public string $description,
        public array $enum = [],
    ){
    }
}

function convertType(string $type): string
{
    return match ($type) {
        'int' => 'integer',
        'float', 'double' => 'number',
        'bool' => 'boolean',
        'mixed' => 'anyOf',
        default => $type,
    };
}

function tool(callable $fn): array
{
    $reflection = new ReflectionFunction($fn);

    $required = [];
    $properties = [];
    foreach ($reflection->getParameters() as $parameter) {
        if (!$parameter->isOptional() && !$parameter->isDefaultValueAvailable()) {
            $required[] = $parameter->getName();
        }
        /** @var Tool $toolProperty */
        $toolProperty = $parameter->getAttributes(Tool::class)[0]->newInstance();
        $property = [
            'type' => convertType($parameter->getType()->getName()),
            'description' => $toolProperty->description,
        ];
        if ($toolProperty->enum !== []) {
            $property['enum'] = $toolProperty->enum;
        }
        $properties[$parameter->getName()] = $property;
    }

    /** @var Tool $toolPropertyOfCallable */
    $toolPropertyOfCallable = $reflection->getAttributes(Tool::class)[0]->newInstance();

    $base = [
        'type' => 'function',
        'function' => [
            'name' => $reflection->getName(),
            'description' => $toolPropertyOfCallable->description,
        ],
        'strict' => true,
    ];
    if (count($properties) > 0) {
        $base['function']['parameters'] = [
            'type' => 'object',
            'properties' => $properties,
            'required' => $required
        ];
    }
    return $base;
}
