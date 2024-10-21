<?php

include __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/helpers.php';

#[Tool('calculation with 2 numbers')]
function calc(
    #[Tool('first argument of calculation')]
    float $a,
    #[Tool('second argument of calculation')]
    float $b,
    #[Tool('operation of calculation', ['+', '-', '*', '/'])]
    string $operation
): float
{
    return match ($operation) {
        '+' => $a + $b,
        '-' => $a - $b,
        '*' => $a * $b,
        '/' => $a / $b,
    };
}

#[Tool('get info about system')]
function getSystemInfo(): string
{
    $commands = [
        'System' => 'uname -a',
        'Memory' => 'free -h',
        'Disks' => 'df -h',
        'Processes' => 'ps aux',
        'Blocks' => 'lsblk',
    ];
    $lines = [];
    foreach ($commands as $name => $command) {
        $lines[] = $name . ":\n" . cmd($command);
    }
    return implode("\n", $lines);
}

#[Tool('search package')]
function searchPackage(#[Tool('name of package')] string $package): string
{
    return cmd("apt search $package");
}

#[Tool('install package')]
function installPackage(#[Tool('name of package')] string $package): string
{
    return cmd("apt install -y $package");
}

#[Tool('recursive search text in all files of directory')]
function searchTextInDir(
    #[Tool('searched text')] string $text,
    #[Tool('name of directory')] string $dir,
): string
{
    return cmd("grep -r '$text' $dir");
}

#[Tool('run any command')]
function command(
    #[Tool('command')] string $command,
): string
{
    return cmd($command);
}

#[Tool('checking that the key is correct')]
function checkKey(#[Tool('the key we want to check')] string $key): string
{
    $keys = [
        'key_53645347',
        'key_34647279',
        'key_74690463',
        'key_29637856',
    ];
    return in_array($key, $keys) ? 'yes' : 'no';
}

$tools = [
    getSystemInfo(...),
    searchPackage(...),
    installPackage(...),
    searchTextInDir(...),
    command(...),
    checkKey(...),
];
$tools = array_map(static fn(callable $x): array => tool($x), $tools);
// TOOLS



//$prompt = <<<PROMPT
//Ты умный калькулятор. Тебе пишут математические выражения, ты их решаешь.
//Учти, что твои сообщения выводятся в консоли, поэтому не используй HTML и LaTeX.
//PROMPT;

// TODO: add instructions for tools usage
$prompt = <<<PROMPT
Your task is to find all the keys in the project code. The project is located in the /app directory.
You can execute any commands in the terminal, as well as install any necessary software.
All keys have the format: key_{8 digits}.

Do not use commands like 'cd' or others that do not produce output.
At the beginning, check the list of files and folders and take it into account, so as not to make useless commands in the future.
When you find a key, write: found key key_12345678 (this is an example).
Remember that keys can be well hidden. Try to find them as quickly and efficiently as possible.
Remember that keys may be in source code or encrypted, so sometimes you will need to execute or modify this code.
Don't go through a bunch of command arguments, it's better to immediately form a command that will give you the most information.
If one approach doesn't work, move on to the next one, be creative.
Don't try out options at random, always make the most of the information you already have.
If you need to decrypt something, use the appropriate utilities.
If you get an error "PHP Fatal error" or "PHP Parse error", it means you have written invalid php code, check it.
PROMPT;

//$command = <<<DOC
//command(
//    ...json_decode(
//        base64_decode('eyJjb21tYW5kIjoicGhwIC1yIFwiZWNobyBiYXNlNjRfZGVjb2RlKCdhMlY1WHpjME5qa3dORFl6Jyk7XCIifQ=='),
//        true
//    )
//)
//DOC;
//dump((string) eval("return $command;"));
//die();

$yourApiKey = getenv('OPENAI_KEY');
$client = OpenAI::client($yourApiKey);

// TODO: create MessageCollection for control adding and logging messages
$messages = [];
$messages[] = new RequestMessage(role: 'system', content: $prompt);
$messages[] = new RequestMessage(role: 'user', content: 'Find 4 keys. Each founded key check with using function "checkKey"');
prettyPrintConversation($messages);

while(true) {
    $last = ask($client, $messages, $tools);
    prettyPrintConversation([$last]);

    $nextMessages = [];
    if ($last->toolCalls) {
        $results = [];
        foreach ($last->toolCalls as $toolCall) {
            $callString = getCallEvaluate($toolCall->function);
            $nextMessages[] = new RequestMessage(
                role: 'tool',
                content: (string) eval("return $callString;"),
                toolCallId: $toolCall->id,
            );
        }
    } else {
        $nextMessages[] = new RequestMessage(role: 'user', content: input());
    }
    prettyPrintConversation($nextMessages);
    $messages = [...$messages, ...$nextMessages];
}
